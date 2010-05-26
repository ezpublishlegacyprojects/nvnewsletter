<?php
/**
 * Module list receivers
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

if ($http->hasPostVariable('CreateReceiverButton')) 
{
   $Module->redirectTo($Module->functionURI('edit_receiver'));
} 
elseif ($http->hasPostVariable('RemoveReceiverButton')) 
{
    $receiverIDArray = $http->postVariable('ReceiverIDArray');
    $http->setSessionVariable('ReceiverIDArray', $receiverIDArray);
    $receivers = array();
    
    foreach ( $receiverIDArray as $receiverID ) 
    {
        $receiver = nvNewsletterReceiver::fetch($receiverID);
        $receivers[] = $receiver;
    }

    $tpl->setVariable( 'delete_result', $receivers );
    
    $Result = array();
    $Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
    $Result['content'] = $tpl->fetch("design:$extension/confirmremove_receiver.tpl");
    $Result['path'] = array( array( 'url' => false,
                                    'text' => ezi18n( 'design/nvnewsletter', 'Newsletter receivers' ) ) );
    return;
} 
elseif ($http->hasPostVariable( 'ConfirmRemoveReceiverButton')) 
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

$newsletterReceiverArray = nvNewsletterReceiver::fetchByOffset( nvNewsletterReceiver::STATUS_PUBLISHED, 
                                                                array( 'offset' => $offset, 
                                                                       'length' => $limit ), 
                                                                array( 'email_address' => 'asc' ) );
$tpl->setVariable( 'receiver_array', $newsletterReceiverArray );

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content'] = $tpl->fetch("design:$extension/list_receivers.tpl");
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'design/nvnewsletter', 'Newsletter receivers' ) ) );
?>
