<?php
/**
 * Module view link
 * @package nvNewsletter
 */
$Module = $Params['Module'];
$http = eZHTTPTool::instance();
$lnk = $http->getVariable('lnk');

$objectID = $Params['ObjectID'];
if( !is_numeric( $objectID ) )
{
    $objectID = 0;
}

// Check URL
if ( !nvNewsletterClickTrack::objectAndURLMatch( $lnk, $objectID ) )
{
    eZExecution::cleanExit();
}

if ( $objectID )
{
    $newsletter = nvNewsletter::fetchByContentObjectID( $objectID );
    
    if ( $newsletter && $newsletter->status == nvNewsletter::STATUS_SENT ) 
    {
        $statistics = nvNewsletterStatistics::fetchByNewsletterAction( $newsletter->attribute('id'),
                                                                       nvNewsletterStatistics::NEWSLETTER_LINK_CLICK );
        if ( $statistics ) 
        {
            $statistics[0]->setAttribute('data_int', $statistics[0]->attribute('data_int')+1);
            $statistics[0]->store();
        } else {
            $statistics = nvNewsletterStatistics::create( $newsletter->attribute('id'),
                                                          0, 
                                                          nvNewsletterStatistics::NEWSLETTER_LINK_CLICK);
        }
        
        $link = nvNewsletterClickTrackLink::fetchByLink( $lnk );
        
        if ( !$link )
        {
            $link = nvNewsletterClickTrackLink::create( $lnk );
        }
        
        $click = nvNewsletterClickTrack::fetchByDate( $newsletter->attribute('id'), $link->attribute('id'), date('Y-m-d') );
        
        if ( $click ) 
        {
            $click->setAttribute('data_int', $click->attribute('data_int')+1);
            $click->store();
        } 
        else 
        {
            $click = nvNewsletterClickTrack::create($newsletter->attribute('id'), $link->attribute('id'));
        }
    }
}

eZHTTPTool::redirect( $lnk );
eZExecution::cleanExit();
?>