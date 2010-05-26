<?php
/**
 * Module send preview
 * @package nvNewsletter
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$Module = $Params['Module'];

$http   = eZHTTPTool::instance();
$format = $http->postVariable('PreviewFormat');
$email  = $http->postVariable('PreviewEmail');
$redirectURIAfterPreview = $http->postVariable('RedirectURIAfterPreview');

$objectID      = (int)$Params['ObjectID'];
$objectVersion = (int)$Params['ObjectVersion'];
$languageCode  = (string)$Params['Language'];
$preview       = false;

if ( $Params['Preview'] == 1 )
    $preview = true;

$redirectURIAfterPreview = nvNewsletterTools::formatURLPath( $redirectURIAfterPreview );

if ( $object = eZContentObject::fetch( $objectID ) ) 
{
    if ( eZMail::validate( $email ) ) 
    {
        nvNewsletter::sendPreview( $email, $format, $objectID, $objectVersion, $languageCode, $preview );
    }
    
    $Module->redirectTo( $redirectURIAfterPreview );
} 
else 
{
    eZLog::write( "nvNewsletter (send_preview.php): sending preview failed with $objectID, objectVersion $objectVersion and languageCode $languageCode", "nvnewsletter.log" );
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

?>