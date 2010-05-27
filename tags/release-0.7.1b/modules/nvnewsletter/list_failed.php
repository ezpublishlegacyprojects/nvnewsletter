<?php
/**
 * Module list failed
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
$tpl->setVariable('module', $Module);
$tpl->setVariable( 'view_parameters', array( 'offset' => $offset ) );
$tpl->setVariable( 'limit', $limit );

if ($http->hasPostVariable('RemoveNewsletterButton')) 
{
    $newsletterIDArray = $http->postVariable('NewsletterIDArray');
    $http->setSessionVariable('NewsletterIDArray', $newsletterIDArray);
    $newsletters = array();
    
    foreach ($newsletterIDArray as $newsletterID) 
    {
        $newsletter = nvNewsletter::fetch($newsletterID);
        $newsletters[] = $newsletter;
    }

    $tpl->setVariable('delete_result', $newsletters);
    $Result = array();
    $Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
    $Result['content'] = $tpl->fetch("design:$extension/confirmremove_newsletter.tpl");
    $Result['path'] = array( array( 'url' => false,
                                    'text' => ezi18n( 'design/nvnewsletter', 'Newsletters' ) ) );
    return;
} 
elseif ($http->hasPostVariable( 'ConfirmRemoveNewsletterButton'))
{
    $newsletterIDArray = $http->sessionVariable('NewsletterIDArray');

    $db = eZDB::instance();
    $db->begin();
    
    foreach ($newsletterIDArray as $newsletterID) 
    {
        nvNewsletter::removeNewsletter($newsletterID);
    }
    
    $db->commit();
} 
elseif ($http->hasPostVariable('MoveToDraftsButton')) 
{
    $newsletterIDArray = $http->postVariable('NewsletterIDArray');
    $http->setSessionVariable('NewsletterIDArray', $newsletterIDArray);
    $newsletters = array();
    
    foreach ( $newsletterIDArray as $newsletterID ) 
    {
        $newsletter = nvNewsletter::fetch($newsletterID);
        $newsletter->setAttribute( 'status', nvNewsletter::STATUS_DRAFT );
        $newsletter->setAttribute( 'info', '' );
        $newsletter->store();
    }
}

$newsletterArray = nvNewsletter::fetchByOffset( array( 'status' => nvNewsletter::STATUS_FAILED ), 
                                                array( 'offset' => $offset, 
                                                       'length' => $limit ) );
                                                
$tpl->setVariable('newsletter_array', $newsletterArray);

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content'] = $tpl->fetch("design:$extension/list_failed.tpl");
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'design/nvnewsletter', 'Failed newsletters' ) ) );
?>
