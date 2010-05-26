<?php
/**
 * Module view receiver group
 * @package nvNewsletter
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$http = eZHTTPTool::instance();
$receiverGroupID = $Params['ReceiverGroupID'];
$Module = $Params['Module'];

$offset = $Params['Offset'];
if( !is_numeric( $offset ) )
{
    $offset = 0;
}

$offset2 = $Params['Offset2'];
if( !is_numeric( $offset2 ) )
{
    $offset2 = 0;
}

$tpl   = nvNewsletterTemplate::factory();
$limit = nvNewsletterAdmin::getAdminListLimit();
$group = nvNewsletterReceiverGroup::fetch( $receiverGroupID );

if ( !$group ) 
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

if ( $http->hasPostVariable( 'RemoveReceiverButton' ) ) 
{
    $receiverIDArray = $http->postVariable( 'ReceiverIDArray' );
    $http->setSessionVariable( 'ReceiverIDArray', $receiverIDArray );
    $receivers = array();
    
    foreach ( $receiverIDArray as $receiverID ) 
    {
        $receiver = nvNewsletterReceiver::fetch( $receiverID );
        $receivers[] = $receiver;
    }

    $tpl->setVariable( 'delete_result', $receivers );
    $tpl->setVariable( 'receivergroup_id', $receiverGroupID );
    
    $Result = array();
    $Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
    $Result['content'] = $tpl->fetch("design:$extension/confirmremove_group_receiver.tpl");
    $Result['path'] = array(array(
                'url' => false,
                'text' => 'Newsletter receivers'));
    return;
} 
elseif ( $http->hasPostVariable( 'ConfirmRemoveReceiverButton') ) 
{
    $receiverIDArray = $http->sessionVariable('ReceiverIDArray');

    $db = eZDB::instance();
    $db->begin();
    
    foreach ( $receiverIDArray as $receiverID ) 
    {
        nvNewsletterReceiver::removeAll( $receiverID );
    }
    
    $db->commit();
} 
elseif ( $http->hasPostVariable( 'UnsubscribeReceiver' ) ) 
{
    $receiverIDArray = $http->postVariable( 'ReceiverIDArray' );
    $receivers = array();
    
    foreach ( $receiverIDArray as $receiverID ) 
    {
        $receiver = nvNewsletterReceiver::fetch( $receiverID );
        $receiver->unsubscribeGroups( array( $receiverGroupID ), nvNewsletterReceiver::STATUS_GROUP_UNSUBSCRIBED_BY_ADMIN );
    }
} 
elseif ( $http->hasPostVariable( 'SubscribeReceiver' ) ) 
{
    $receiverIDArray = $http->postVariable( 'ReceiverIDArray' );
    $receivers = array();
    
    foreach ( $receiverIDArray as $receiverID ) 
    {
        $receiver = nvNewsletterReceiver::fetch( $receiverID );
        $receiver->updateReceiverGroups( array( $receiverGroupID ) );
    }
}

$tpl->setVariable( 'group', $group );
$tpl->setVariable( 'view_parameters', array( 'offset' => $offset, 'offset2' => $offset2 ) );
$tpl->setVariable( 'limit', $limit );

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:$extension/view_receiver_group.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'design/nvnewsletter', 'View receiver group' ) ) );
?>
