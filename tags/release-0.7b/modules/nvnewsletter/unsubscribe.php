<?php
/**
 * Module unsubscribe
 * @package nvNewsletter
 */
$objectID    = (int)$Params['ObjectID'];
$userID      = (int)$Params['UserID'];
$hash        = (string)$Params['Hash'];
$titleArray  = array();
$success     = false;
$error       = false;

if ( $receiver = nvNewsletterReceiver::fetch( $userID ) )
{
    $userHash = nvNewsletterTools::getUserHash( $receiver->email_address );
}

if ( nvNewsletterTools::hashMatch( $userHash, $hash ) ) 
{
    if ( $receiver->unsubscribe( nvNewsletterReceiver::STATUS_GROUP_UNSUBSCRIBED_BY_USER ) ) 
    {
        $success    = true;
        $newsletter = nvNewsletter::fetchByContentObjectID($objectID);
        
        if ( $newsletter ) 
        {
            $statistics = nvNewsletterStatistics::create( $newsletter->attribute('id'), 
                                                          $userID, nvNewsletterStatistics::NEWSLETTER_UNSUBSCRIBE, 
                                                          $hash );
        }
        
        nvNewsletterNotification::unsubscription( array( 'receiver' => $receiver ) );
    }
}

if ( empty( $userID ) && empty( $hash ) ) 
{
    $error = 'usercode';
}

$tpl = nvNewsletterTemplate::factory();
$tpl->setVariable( 'result', array( 'success' => $success, 
                                    'error'   => $error ) );

$Result['path']         = array( array( 'url' => false, 
                                        'text' => ezi18n( 'design/nvnewsletter', 'Unsubscribe newsletter' ) ) );
$Result['content']      = $tpl->fetch( 'design:nvnewsletter/unsubscribe.tpl' );
$Result['pagelayout']   = true;
?>