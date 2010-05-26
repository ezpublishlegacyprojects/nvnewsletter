<?php
/**
 * Module list senders
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

if ($http->hasPostVariable('CreateSenderButton')) 
{
    $Module->redirectTo($Module->functionURI('edit_sender'));
} 
elseif ($http->hasPostVariable('RemoveSenderButton')) 
{
    $senderIDArray = $http->postVariable('SenderIDArray');
    $http->setSessionVariable('SenderIDArray', $senderIDArray);
    $senders = array();
    
    foreach ( $senderIDArray as $senderID ) 
    {
        $sender = nvNewsletterSender::fetch( $senderID );
        $senders[] = $sender;
    }

    $tpl->setVariable('delete_result', $senders);
    $Result = array();
    $Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
    $Result['content'] = $tpl->fetch("design:$extension/confirmremove_sender.tpl");
    $Result['path'] = array( array( 'url' => false,
                                    'text' => ezi18n( 'design/nvnewsletter', 'Newsletter senders' ) ) );
    return;
} 
elseif ( $http->hasPostVariable( 'ConfirmRemoveSenderButton' ) ) 
{
    $senderIDArray = $http->sessionVariable('SenderIDArray');

    $db = eZDB::instance();
    $db->begin();
    
    foreach ( $senderIDArray as $senderID ) 
    {
        nvNewsletterSender::removeAll( $senderID );
    }
    
    $db->commit();
}


$newsletterSenderArray = nvNewsletterSender::fetchByOffset( nvNewsletterSender::STATUS_PUBLISHED,
                                                            array( 'offset' => $offset,
                                                                   'length' => $limit ) );
$tpl->setVariable( 'sender_array', $newsletterSenderArray );

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content'] = $tpl->fetch("design:$extension/list_senders.tpl");
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'design/nvnewsletter', 'Newsletter senders' ) ) );
?>
