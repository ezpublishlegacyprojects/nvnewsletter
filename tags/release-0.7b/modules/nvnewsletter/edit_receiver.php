<?php
/**
 * Module edit receiver
 * @package nvNewsletter
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

$status = '';
if ($Params['status'] == 'new') {
    $status = 'new';
}

$receiverID = $Params['ReceiverID'];
$receiver = nvNewsletterReceiver::fetchDraft($receiverID);

if ( !$receiver ) 
{
    $receiver = nvNewsletterReceiver::create();
    $Module->redirectToView( 'edit_receiver', array( $receiver->attribute('id')), array('status' => 'new'));
}

$warning = array();
$receiverStatus = $http->postVariable('ReceiverStatus');

if ($http->hasPostVariable('StoreButton')) 
{
    $receiverEmail = $http->postVariable('ReceiverEmail');
    $receiverGroupIDs = $http->postVariable('ReceiverGroupIDArray');
    $receiverGroupFormats = $http->postVariable('ReceiverGroupFormatArray');
    
    if (eZMail::validate($receiverEmail)) 
    {
        // Prevent double emails
        $existingReceiver = nvNewsletterReceiver::fetchByEmail( $receiverEmail );
        
        if ( !$existingReceiver ) 
        {
            $receiver->setAttribute('email_address', $receiverEmail);
        } 
        else 
        {
            $receiver = $existingReceiver;
        }
    } 
    else 
    {
        $warning[] = ezi18n('eznewsletter/edit_receiver', 'Email address "%address" did not validate.', false, array('%address' => $receiverEmail));
    }

    // Set groups depending on if we edit old or new receiver
    if ( $receiverStatus == 'new' ) 
    {
        $receiver->updateReceiverGroups( $receiverGroupIDs, $receiverGroupFormats );
    } 
    else 
    {
        $receiver->setReceiverGroups( $receiverGroupIDs, $receiverGroupFormats );
    }

    $receiverFields = $http->postVariable('ReceiverFields');
    $receiver->setReceiverFields($receiverFields);

    $receiver->store();
}

if ( 0 === count($warning) && $http->hasPostVariable('StoreButton') ) 
{
    $receiver->publish();
    return $Module->redirectToView( 'view_receiver', array( $receiver->attribute('id')));
}

if ($http->hasPostVariable('CancelButton')) 
{
    $receiver->removeDraft();
    
    if ( $receiverStatus == 'new' ) 
    {
        return $Module->redirectTo($Module->functionURI('list_receivers'));
    } 
    else 
    {
        return $Module->redirectToView( 'view_receiver', array( $receiver->attribute('id')));
    }
}

$tpl = nvNewsletterTemplate::factory();
$tpl->setVariable('warning', $warning);
$tpl->setVariable('receiver', $receiver);
$tpl->setVariable('status', $status);
$newsletterReceiverGroupArray = nvNewsletterReceiverGroup::fetchByOffset();
$tpl->setVariable('receiver_group_array', $newsletterReceiverGroupArray);

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:$extension/edit_receiver.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'design/nvnewsletter', 'Edit receiver' ) ) );
?>