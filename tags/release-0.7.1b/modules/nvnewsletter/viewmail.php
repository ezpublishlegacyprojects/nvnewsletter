<?php
/**
 * Module view mail
 * @package nvNewsletter
 */
$hash          = (string)$Params['Hash'];
$userHash      = (string)$Params['UserHash'];
$userID        = (int)$Params['UserID'];
$objectID      = (int)$Params['ObjectID'];
$objectVersion = (int)$Params['ObjectVersion'];
$titleArray    = array();
$content       = false;

$object = eZContentObject::fetch( $objectID );

if ( $object ) 
{
    $content = nvNewsletterTools::getContent( $objectID, $objectVersion, 'html', $hash );
    
    if ( is_numeric( $userID ) && $userHash )
    {
        $receiver = nvNewsletterReceiver::fetch( $userID );
        
        if ( $receiver )
        {
            if ( $userHash == nvNewsletterTools::getUserHash( $receiver->email_address ) && !empty( $userHash ) )
            {
                $fields  = nvNewsletterTools::formatReceiverFields( $receiver->fields() );
                $tags    = nvNewsletterTools::personalizedTags( $objectID, $userID, $receiver->email_address, $fields );
                $content = str_replace( $tags['tags'], $tags['values'], $content );
            }
        }
    }
    
    $content = nvNewsletterTools::replaceUserCode( $content );
}

if ( !$content ) 
{
    eZLog::write( "nvNewsletter (viewmail.php): generated mail not found with objectID $objectID and hash $hash", "nvnewsletter.log" );
    $titleArray = array( array( 'url' => false, 
                                'text' => ezi18n( 'design/nvnewsletter', 'Newsletter not found' ) ) );
    $content = ezi18n( 'design/nvnewsletter', 'Newsletter not found' );
}

$tpl = nvNewsletterTemplate::factory();
$tpl->setVariable( 'result', array( 'content' => $content ) );

$Result['path']        = $titleArray;
$Result['content']     = $tpl->fetch( "design:viewmail.tpl" );
$Result['pagelayout']  = false;
?>