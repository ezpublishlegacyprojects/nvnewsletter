<?php
/**
 * Module edit newsletter
 * @package nvNewsletter
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

$newsletterID = $Params['NewsletterID'];
$newsletter = nvNewsletter::fetchDraft($newsletterID);

if ( !$newsletter ) 
{
    $newsletter = nvNewsletter::create();
    $Module->redirectToView('edit_newsletter', array($newsletter->attribute('id')));
}

$warning = array();

if ($http->hasPostVariable('StoreButton')) 
{
    if (strlen($http->postVariable('SenderName')) > 0) 
    {
        $sender->setAttribute('sender_name', $http->postVariable('SenderName'));
    } else {
        $sender->setAttribute('sender_name', '');
    }

    $senderAddress = $http->postVariable('SenderAddress');
    
    if (eZMail::validate($senderAddress)) 
    {
        $sender->setAttribute('sender_email', $senderAddress);
    } else {
        $warning[] = ezi18n('eznewsletter/edit_sender', 'Email address "%address" did not validate.', false, array('%address' => $senderAddress));
    }

    $replyToAddress = $http->postVariable('ReplyToAddress');
    
    if (0 < strlen($replyToAddress)) 
    {
        if (eZMail::validate($replyToAddress)) 
        {
            $sender->setAttribute('reply_to', $replyToAddress);
        } 
        else 
        {
            $warning[] = ezi18n('eznewsletter/edit_sender', 'Email address "%address" did not validate.', false, array('%address' => $replyToAddress));
        }
    }
    else 
    {
        $sender->setAttribute('reply_to', '');
    }

    $sender->store();
}

if (0 === count($warning) && $http->hasPostVariable('StoreButton')) 
{
    $sender->publish();
    return $Module->redirectTo($Module->functionURI('list_senders'));
}

if ($http->hasPostVariable('CancelButton')) 
{
    $newsletter->removeDraft();
    $Module->redirectTo($Module->functionURI('list_senders'));
}

$ini = eZINI::instance( 'nvnewsletter.ini' );
$classGroupID = $ini->variable('ContentClassSettings', 'NewsletterClassGroup');

$groupInfo = eZContentClassGroup::fetch($classGroupID);

if (!$groupInfo) 
{
    return $Module->handleError(eZError::KERNEL_NOT_AVAILABLE, 'kernel');
}

$templates = eZContentClassClassGroup::fetchClassList(0, $classGroupID, $asObject = true);

$tpl = nvNewsletterTemplate::factory();
$tpl->setVariable('warning', $warning);
$tpl->setVariable('newsletter', $newsletter);
$tpl->setVariable('templates', $templates);

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:$extension/edit_newsletter.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'design/nvnewsletter', 'Edit newsletter' ) ) );
?>
