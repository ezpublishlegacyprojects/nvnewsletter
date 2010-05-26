<?php
/**
 * Module create newsletter
 * @package nvNewsletter
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$Module = $Params['Module'];
$http = eZHTTPTool::instance();
$ini = eZINI::instance( 'nvnewsletter.ini' );

if ($http->hasPostVariable("CreateButton")) 
{
    $user = eZUser::currentUser();
    $userID = $user->attribute('contentobject_id');
    
    // Set redirect URIs if present
    if ($http->hasPostVariable('RedirectURIAfterPublish')) 
    {
        $http->setSessionVariable('RedirectURIAfterPublish', $http->postVariable('RedirectURIAfterPublish'));
    }
    
    if ($http->hasPostVariable('RedirectIfDiscarded')) 
    {
        $http->setSessionVariable('RedirectIfDiscarded', $http->postVariable('RedirectIfDiscarded'));
    }

    $class = eZContentClass::fetch($http->postVariable('TemplateID'));
    
    if (!$class) 
    {
        return $Module->handleError(eZError::KERNEL_NOT_AVAILABLE, 'kernel');
    }

    $parentNode = eZContentObjectTreeNode::fetch($ini->variable('ContentClassSettings', 'NewsletterNodeID'));

    $db = eZDB::instance();
    $db->begin();

    $contentObject = $class->instantiate($userID, $ini->variable('ContentClassSettings', 'NewsletterSection'));
    $contentObject->store();

    $nodeAssignment = eZNodeAssignment::create(array(
                'contentobject_id' => $contentObject->attribute('id'),
                'contentobject_version' => $contentObject->attribute('current_version'),
                'parent_node' => $parentNode->attribute('node_id'),
                'is_main' => 1));
    $nodeAssignment->store();

    $db->commit();

    return $Module->redirectTo('content/edit/' . $contentObject->attribute('id').'/'.$contentObject->attribute('current_version'));
}

$classGroupID = $ini->variable('ContentClassSettings', 'NewsletterClassGroup');

$groupInfo = eZContentClassGroup::fetch($classGroupID);

if ( !$groupInfo ) 
{
    return $Module->handleError(eZError::KERNEL_NOT_AVAILABLE, 'kernel');
}

$templates = eZContentClassClassGroup::fetchClassList( 0, $classGroupID, true );

$tpl = nvNewsletterTemplate::factory();
$tpl->setVariable('templates', $templates);

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:$extension/create_newsletter.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'design/nvnewsletter', 'Create newsletter' ) ) );
?>
