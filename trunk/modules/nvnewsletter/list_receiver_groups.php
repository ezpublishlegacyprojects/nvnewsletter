<?php
/**
 * Module list receiver groups
 * @package nvNewsletter
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

$offset = $Params['Offset'];
if( !is_numeric( $offset ) )
{
    $offset = 0;
}

$limit = nvNewsletterAdmin::getAdminListLimit();

$tpl = nvNewsletterTemplate::factory();
$tpl->setVariable( 'module', $Module );
$tpl->setVariable( 'view_parameters', array( 'offset' => $offset ) );
$tpl->setVariable( 'limit', $limit );

if ($http->hasPostVariable('CreateReceiverGroupButton')) 
{
    $Module->redirectTo($Module->functionURI('edit_receiver_group'));
} 
elseif ($http->hasPostVariable('RemoveReceiverGroupButton')) 
{
    $receiverGroupIDArray = $http->postVariable('ReceiverGroupIDArray');
    $http->setSessionVariable('ReceiverGroupIDArray', $receiverGroupIDArray);
    $receiverGroups = array();
    
    foreach ( $receiverGroupIDArray as $receiverGroupID ) 
    {
        $receiverGroup = nvNewsletterReceiverGroup::fetch( $receiverGroupID );
        $receiverGroups[] = $receiverGroup;
    }

    $tpl->setVariable( 'delete_result', $receiverGroups );
    
    $Result = array();
    $Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
    $Result['content'] = $tpl->fetch("design:$extension/confirmremove_receivergroup.tpl");
    $Result['path'] = array( array( 'url' => false,
                                    'text' => ezi18n( 'design/nvnewsletter', 'Newsletter receiver groups' ) ) );
    return;
} 
elseif ($http->hasPostVariable( 'ConfirmRemoveReceiverGroupButton')) 
{
    $receiverGroupIDArray = $http->sessionVariable('ReceiverGroupIDArray');

    $db = eZDB::instance();
    $db->begin();
    
    foreach ( $receiverGroupIDArray as $receiverGroupID ) 
    {
        nvNewsletterReceiverGroup::removeAll( $receiverGroupID );
    }
    
    $db->commit();
}

$newsletterReceiverGroupArray = nvNewsletterReceiverGroup::fetchByOffset( nvNewsletterReceiverGroup::STATUS_PUBLISHED, 
                                                                          array( 'offset' => $offset, 
                                                                                 'length'  => $limit ), 
                                                                          array( 'group_name' => 'asc' ) );
$tpl->setVariable( 'receiver_group_array', $newsletterReceiverGroupArray );

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content'] = $tpl->fetch("design:$extension/list_receiver_groups.tpl");
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'design/nvnewsletter', 'Newsletter receiver groups' ) ) );
?>
