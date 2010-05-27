<?php
/**
 * Cronjob file which handles newsletter sending.
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7.1b
 * @package nvNewsletter
 * @todo Support for huge receiver groups. Now might run out of memory.
 * @todo Fix sent count bug if sending is interrupted.
 */

$cli->setUseStyles( true );

$nvnewsletterIni = eZINI::instance( 'nvnewsletter.ini' );
$siteIni         = eZINI::instance( 'site.ini' );
$db              = eZDB::instance();

nvNewsletterAdmin::setLoggedIn();

$errors                         = '';
$mailSendingInProgress          = false;
$interruptedMailSendingFound    = false;
$queuedMailSendingFound         = false;
$newsletterContentObjectID      = false;
$newsletterContentObjectVersion = false;
$locale                         = false;

$receiversBeforeUpdatingNewsletter = $nvnewsletterIni->variable( 'MailSendSettings', 'MailSentBeforeUpdatingNewsletterData' );
$mailResendBufferTime              = $nvnewsletterIni->variable( 'MailSendSettings', 'MailResendBufferTime' );
$sleepTimeAfterUpdating            = $nvnewsletterIni->variable( 'MailSendSettings', 'SleepTimeAfterUpdating' );

// Create filtertime for mail resend
$currentDateTime = nvNewsletterTools::currentDatetime();
$currentDateTimeMinus = date( "Y-m-d H:i:s", mktime( date("H"), ( date("i") - $mailResendBufferTime ), date("s"), date("m"), date("d"), date("Y") ) );

if ( !$isQuiet )
{
    $cli->output( "\nStarting mail sending process\n-----------------------------\n" );
    $cli->output( "Fetching current mail sendings:\n" );
}

// If there are any mail sendings in progress, 
// check if there are any interrupted mail sendings among them
$newsletters = nvNewsletter::fetchByOffset( array( 'status' => nvNewsletter::STATUS_SENDING ), 
                                            false, 
                                            array( 'send_start_time' => 'asc' ) );

foreach ( $newsletters as $newsletter ) 
{
    if( $newsletter->send_last_access_time < $currentDateTimeMinus && !$interruptedMailSendingFound )
    {
        $interruptedMailSendingFound = true;
        $newsletterID = $newsletter->id;
        $newsletterContentObjectID = $newsletter->contentobject_id;
        $newsletterContentObjectVersion = $newsletter->contentobject_version;
        $locale = $newsletter->locale;
        
        if ( !$isQuiet )
            $cli->output( "\t- Interrupted mail sending found for mail ".$newsletterID." (".$newsletterContentObjectID.":v".$newsletterContentObjectVersion.")\n" );
    }
}

if ( !$interruptedMailSendingFound && count( $newsletters ) ) 
{
    if ( !$isQuiet )
        $cli->output( "\t- Mail sending in progress for mail ".$newsletter->id." (".$newsletter->contentobject_id.":v".$newsletter->contentobject_version.")\n" );
    
    $mailSendingInProgress = true;
} 
elseif ( !$interruptedMailSendingFound && !count( $newsletters ) )
{
    if ( !$isQuiet )
        $cli->output( "\t- No mail sendings in progress\n" );
}

// If interrupted mail sending was found or there are no current sendings active, 
// then we can proceed to the actual mail sending routine
if ( !$mailSendingInProgress ) 
{
    // If there was no interrupted sendings, we need to fetch fresh one 
    if( !$interruptedMailSendingFound )
    {
        if ( !$isQuiet )
            $cli->output( "Fetching queued mail sendings:\n" );

        // Check if there are any mail sendings in progress
        $newsletters = nvNewsletter::fetchByOffset( array( 'status' => nvNewsletter::STATUS_IN_PROGRESS,
                                                           'send_time' => array( '<', nvNewsletterTools::currentDatetime() ) ), 
                                                    false, 
                                                    array( 'send_time' => 'desc' ) );

        foreach ( $newsletters as $newsletter ) 
        {
            $queuedMailSendingFound = true;
            $newsletterID = $newsletter->id;
            $newsletterContentObjectID = $newsletter->contentobject_id;
            $newsletterContentObjectVersion = $newsletter->contentobject_version;
            $locale = $newsletter->locale;
            
            if ( !$isQuiet )
                $cli->output( "\t- Queued mail sending found for mail ".$newsletterID." (".$newsletterContentObjectID.":v".$newsletterContentObjectVersion.") (".$newsletter->send_time.")\n" );
        }
        
        if ( !$queuedMailSendingFound ) 
        {
            if ( !$isQuiet )
                $cli->output( "\t- No mail sendings in queue" );
        }
    }
  
    // If there is interrupted or fresh mail, we can continue
    if ( $interruptedMailSendingFound || $queuedMailSendingFound ) 
    {
        if ( !$isQuiet )
            $cli->output( "Setting up newsletter data\n" );

        $newsletterMailer = new nvNewsletterMailer( $newsletterContentObjectID, $newsletterContentObjectVersion, $locale );

        // If object and HTML file exists we can continue
        if( $newsletterMailer->getMailAvailability() && $newsletterMailer->getPregeneratedDataAvailability() ) 
        {
            // Final check if either interrupted or queued sending is found
            if ( $queuedMailSendingFound ) 
            {
                if ( !$isQuiet )
                    $cli->output( "Starting mail sending process for mail ".$newsletterID." (".$newsletterContentObjectID.":v".$newsletterContentObjectVersion.")\n" );

                $newsletterMailer->populateMailReceiverTable();
                $newsletterMailer->startMailSending();
            } 
            elseif ( $interruptedMailSendingFound ) 
            {
                if ( !$isQuiet )
                    $cli->output( "Continuing interrupted mail sending process for mail ".$newsletterID." (".$newsletterContentObjectID.":v".$newsletterContentObjectVersion.")\n" );
                    
                $newsletterMailer->restartMailSending();
            }

            if ( !$isQuiet )
                $cli->output( "\t- Fetching mail receivers for mail sending process" );

            // Fetch all receivers and start mailsending
            $allReceivers = $newsletterMailer->getReceivers();

            // Loop through all receivers and remove them from nvnewsletter_newsletters
            $cli->output( "\nMail sending process started\n" );

            $counter = 0;
            
            if ( $interruptedMailSendingFound ) 
            {
                $counter = $newsletterMailer->getMailSentCount();
            }
            
            $minCounter = $receiversBeforeUpdatingNewsletter;
            $maxCounter = 0;

            foreach ( $allReceivers as $receiver ) 
            {
                $counter++;
                $minCounter--;

                // Actual mail sending
                $newsletterMailer->sendMail( $receiver->receiver_id, 
                                             $receiver->email_address, 
                                             $receiver->mail_type, 
                                             $receiver->receiver()->fields() );

                // Remove email from temp table
                $receiver->remove();
                
                // Update newsletter status if counter == 0
                // This is to prevent update after every mail send
                if ( $minCounter == 0 ) 
                {
                    $minCounter = $receiversBeforeUpdatingNewsletter;
                    $cli->output( "\tmails ".( ( $maxCounter * $minCounter ) + 1 )."-".( ( $maxCounter * $minCounter ) + $receiversBeforeUpdatingNewsletter )." sent" );
                    $newsletterMailer->updateMailLastSendTime( $counter );
                    $maxCounter++;
                    
                    // Sleep a while
                    if ( is_numeric( $sleepTimeAfterUpdating ) )
                    {
                        usleep( $sleepTimeAfterUpdating );
                    }
                }
            }
            
            if ( $minCounter != $receiversBeforeUpdatingNewsletter ) 
            {
                if ( $counter > 1 )
                {
                    if ( !$isQuiet )
                        $cli->output( "\t- mails ".( ( $maxCounter * $minCounter ) + 1 )."-".$counter." sent" );
                } 
                else 
                {
                    if ( !$isQuiet )
                        $cli->output( "\t- mail sent" );
                }
            }

            // Finalizing mail sending
            $newsletterMailer->updateMailSendingEnd( $counter );
            eZLog::write( "nvNewsletter (nvnewslettermailsender.php): $counter mails successfully sent with objectID $newsletterContentObjectID and objectVersion $newsletterContentObjectVersion", "nvnewsletter.log" );

            /*
            $db->query( "UPDATE 
            nvnewsletter_newsletters 
            SET 
            status = '1' 
            WHERE 
            contentobject_id = '".$newsletterContentObjectID."' AND
            contentobject_version = '".$newsletterContentObjectVersion."'
            ;" );
            */
            
            if ( !$isQuiet )
                $cli->output( "\nMail sending succesfully completed!" );
        } 
        else 
        {
            if ( !$isQuiet )
                $cli->output( "\nMail sending procedure interrupted because of errors!" );

            if ( !$newsletterMailer->getMailAvailability() ) 
            {
                if ( $errors )
                { 
                    $errors .= '\n'; 
                } 
                
                $errors .= '- Mail cannot be found';
                
                eZLog::write( "nvNewsletter (nvnewslettermailsender.php): Mail cannot be found with objectID $newsletterContentObjectID and objectVersion $newsletterContentObjectVersion", "nvnewsletter.log" );
            }
            
            if ( !$newsletterMailer->getPregeneratedDataAvailability() ) 
            {
                if ( $errors ) 
                { 
                    $errors .= '\n'; 
                } 
                
                $errors .= '- Cannot find pregenerated HTML/text-file';
                
                eZLog::write( "nvNewsletter (nvnewslettermailsender.php): cannot find pregenerated HTML/text-file with objectID $newsletterContentObjectID and objectVersion $newsletterContentObjectVersion", "nvnewsletter.log" );
            }
        }
    }
}

if ( $errors ) 
    $newsletterMailer->updateErrors( $errors );

if ( !$isQuiet )
    $cli->output( "\n----------------------------------\nEnding newsletter sendmail process" );
?>
