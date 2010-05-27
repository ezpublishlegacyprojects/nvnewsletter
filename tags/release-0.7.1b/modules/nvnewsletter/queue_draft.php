<?php
/**
 * Module queue draft
 * @package nvNewsletter
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

$newsletterID = $Params['NewsletterID'];
$newsletter = nvNewsletter::fetch($newsletterID);

$warning = array();

if ($http->hasPostVariable('QueueButton')) 
{
    $year = null;
    $month = null;
    $day = null;
    $hour = null;
    $minute = null;
    
    if (strlen($http->postVariable('QueueYear')) > 0) 
    {
        $year = $http->postVariable('QueueYear');
    } 
    else 
    {
        $warning[] = ezi18n('design/nvnewsletter', 'Year missing.');
    }

    if (strlen($http->postVariable('QueueMonth')) > 0) 
    {
        $month = $http->postVariable('QueueMonth');
    } 
    else 
    {
        $warning[] = ezi18n('design/nvnewsletter', 'Month missing.');
    }

    if (strlen($http->postVariable('QueueDay')) > 0) 
    {
        $day = $http->postVariable('QueueDay');
    } 
    else 
    {
        $warning[] = ezi18n('design/nvnewsletter', 'Day missing.');
    }

    if (strlen($http->postVariable('QueueHour')) > 0) 
    {
        $hour = $http->postVariable('QueueHour');
    } 
    else 
    {
        $warning[] = ezi18n('design/nvnewsletter', 'Hour missing.');
    }

    if (strlen($http->postVariable('QueueMinute')) > 0) 
    {
        $minute = $http->postVariable('QueueMinute');
    } 
    else 
    {
        $warning[] = ezi18n('design/nvnewsletter', 'Minute missing.');
    }

    if ($year != null && $month != null && $hour != null && $minute != null) 
    {
        $newsletter->setAttribute('send_time', $year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':00');
        $newsletter->setAttribute('status', nvNewsletter::STATUS_IN_PROGRESS);
        $newsletter->store();
    }
}

if (0 === count($warning) && $http->hasPostVariable('QueueButton')) 
{
    return $Module->redirectToView('view_newsletter', array($newsletterID));
}

if ($http->hasPostVariable('CancelButton')) 
{
    return $Module->redirectToView('view_newsletter', array($newsletterID));
}

$tpl = nvNewsletterTemplate::factory();
$tpl->setVariable('newsletter', $newsletter);

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:$extension/queue_draft.tpl" );
$Result['path'] = array( array( 'url' => false, 
                                'text' => ezi18n( 'design/nvnewsletter', 'Queue newsletter' ) ) );
?>
