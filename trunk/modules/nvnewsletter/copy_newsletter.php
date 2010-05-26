<?php
/**
 * Module copy newsletter
 * @package nvNewsletter
 * @internal copy from kernel/content/copy.php
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

$ini = eZINI::instance( 'nvnewsletter.ini' );

$objectID = $Params['ObjectID'];
$object   = eZContentObject::fetch( $objectID );
$allVersions = false;

if( !is_object( $object ) )
{
   return $Module->handleError(eZError::KERNEL_NOT_AVAILABLE, 'kernel');
}

$newParentNodeID = $ini->variable('ContentClassSettings', 'NewsletterNodeID');
$newParentNode   = eZContentObjectTreeNode::fetch( $newParentNodeID );

if ( !is_object( $newParentNode ) )
{
   return $Module->handleError(eZError::KERNEL_NOT_AVAILABLE, 'kernel');
}

$classID = $object->attribute('contentclass_id');

if ( !$newParentNode->checkAccess( 'create', $classID ) )
{
    eZDebug::writeError( "Cannot copy object $objectID to node $newParentNodeID, " .
                           "the current user does not have create permission for class ID $classID",
                         'content/copy' );
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$db = eZDB::instance();
$db->begin();
$newObject = $object->copy( $allVersions );
// We should reset section that will be updated in updateSectionID().
// If sectionID is 0 then the object has been newly created
$newObject->setAttribute( 'section_id', 0 );
$newObject->store();

$curVersion        = $newObject->attribute( 'current_version' );
$curVersionObject  = $newObject->attribute( 'current' );
$newObjAssignments = $curVersionObject->attribute( 'node_assignments' );
unset( $curVersionObject );

// remove old node assignments
foreach( $newObjAssignments as $assignment )
{
    $assignment->purge();
}

// and create a new one
$nodeAssignment = eZNodeAssignment::create( array(
                                                 'contentobject_id' => $newObject->attribute( 'id' ),
                                                 'contentobject_version' => $curVersion,
                                                 'parent_node' => $newParentNodeID,
                                                 'is_main' => 1
                                                 ) );
$nodeAssignment->store();

// publish the newly created object
eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $newObject->attribute( 'id' ),
                                                          'version'   => $curVersion ) );
// Update "is_invisible" attribute for the newly created node.
$newNode = $newObject->attribute( 'main_node' );
eZContentObjectTreeNode::updateNodeVisibility( $newNode, $newParentNode );

$db->commit();

return $Module->redirectTo( 'nvnewsletter/list_draft' );
?>