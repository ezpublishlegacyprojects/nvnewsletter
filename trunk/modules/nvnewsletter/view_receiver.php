<?php
/**
 * Module view receiver
 * @package nvNewsletter
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$http = eZHTTPTool::instance();
$receiverID = $Params['ReceiverID'];
$Module = $Params['Module'];

$receiver = nvNewsletterReceiver::fetch( $receiverID );

if ( !$receiver ) 
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

if ( $http->hasPostVariable( 'UnsubscribeReceiver' ) ) 
{
    $receiver->unsubscribe( nvNewsletterReceiver::STATUS_GROUP_UNSUBSCRIBED_BY_ADMIN );
}

$tpl = nvNewsletterTemplate::factory();
$tpl->setVariable( 'receiver', $receiver );

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:$extension/view_receiver.tpl" );
$Result['path'] = array(array(
            'url' => false,
            'text' => ezi18n('nvnewsletter/view_receiver', 'View receiver')));
?>
