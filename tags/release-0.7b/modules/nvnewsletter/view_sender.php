<?php
/**
 * Module view sender
 * @package nvNewsletter
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$http = eZHTTPTool::instance();
$senderID = $Params['SenderID'];
$Module = $Params['Module'];

$sender = nvNewsletterSender::fetch($senderID);

if (!$sender) {
    return $Module->handleError(eZError::KERNEL_NOT_AVAILABLE, 'kernel');
}

$tpl = nvNewsletterTemplate::factory();

$tpl->setVariable('sender', $sender);

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:$extension/view_sender.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'design/nvnewsletter', 'View sender' ) ) );
?>
