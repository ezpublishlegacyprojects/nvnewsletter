<?php
/**
 * Module preview
 * @package nvNewsletter
 */
$Module        = $Params['Module'];
$objectID      = (int)$Params['ObjectID'];
$objectVersion = (int)$Params['ObjectVersion'];
$languageCode  = (string)$Params['Language'];
$titleArray    = array();
$content       = false;
$createTemp    = false;
$format        = 'html';

if ( $Params['Preview'] == 1 )
    $createTemp = true;

if ( $Params['Format'] == 'text' ) 
    $format = 'text';

/**
 * Create HTML and text files
 */
if ( $object = eZContentObject::fetch( $objectID ) ) 
{
    if ( nvNewsletter::createNewsletter( $objectID, $objectVersion, $languageCode, $createTemp ) ) 
    {
        $content = nvNewsletterTools::getContent( $objectID, $objectVersion, $format, false, $languageCode );
        $content = nvNewsletterTools::replaceUserCode( $content );
    }
}

if ( !$content ) 
{
    eZLog::write( "nvNewsletter (preview.php): generated mail not found with objectID $objectID, objectVersion $objectVersion and languageCode $languageCode", "nvnewsletter.log" );
    $titleArray = array( array( 'url' => false, 
                                'text' => ezi18n( 'design/nvnewsletter', 'Newsletter not generated' ) ) );
    $content = ezi18n( 'design/nvnewsletter', 'Newsletter not generated' );
}

$tpl = nvNewsletterTemplate::factory();
$tpl->setVariable( 'format', $format );
$tpl->setVariable( 'result', array( 'content' => $content ) );

$Result['path'] = $titleArray;
$Result['content'] = $tpl->fetch('design:viewmail.tpl');
$Result['pagelayout'] = false;
?>