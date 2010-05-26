<?php
/**
 * Module list in progress
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

if ($http->hasPostVariable('MoveToDraftsButton')) 
{
    $newsletterIDArray = $http->postVariable('NewsletterIDArray');
    $http->setSessionVariable('NewsletterIDArray', $newsletterIDArray);
    $newsletters = array();
    
    foreach ( $newsletterIDArray as $newsletterID ) 
    {
        $newsletter = nvNewsletter::fetch($newsletterID);
        $newsletter->setAttribute( 'status', nvNewsletter::STATUS_DRAFT );
        $newsletter->setAttribute( 'total_mail_count', 0 );
        $newsletter->store();
    }
}

$newsletterArray = nvNewsletter::fetchInProgress();
$tpl->setVariable( 'newsletter_array', $newsletterArray );

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content'] = $tpl->fetch("design:$extension/list_in_progress.tpl");
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'design/nvnewsletter', 'In progress newsletters' ) ) );
?>
