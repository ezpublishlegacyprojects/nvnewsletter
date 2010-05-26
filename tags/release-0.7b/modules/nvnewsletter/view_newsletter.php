<?php
/**
 * Module view newsletter
 * @package nvNewsletter
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$http = eZHTTPTool::instance();
$newsletterID = $Params['NewsletterID'];
$Module = $Params['Module'];

$newsletter    = nvNewsletter::fetch( $newsletterID );
$contentObject = $newsletter->contentObject();

if ( !$newsletter ) 
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

if ( !$contentObject ) 
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

$tpl = nvNewsletterTemplate::factory();
$tpl->setVariable( 'newsletter', $newsletter );
$tpl->setVariable( 'node', $contentObject->mainNode() );

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:$extension/view_newsletter.tpl" );
$Result['path'] = array(array(
            'url' => false,
            'text' => ezi18n('nvnewsletter/view_newsletter', 'View newsletter')));
?>
