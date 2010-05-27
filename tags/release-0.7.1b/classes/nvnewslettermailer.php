<?php
/**
 * File containing the nvNewsletterMailer class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7.1b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletterMailer handles mail sending
 */
class nvNewsletterMailer 
{
    const USERCODE_REPLACEMENT              = '__USERCODE__';
    const TRACKERCODE_REPLACEMENT           = '__TRACKERCODE__';
    
    const USERCODE_TAG                      = 'NVN_USER_CODE';
    const TRACKERCODE_TAG                   = 'NVN_TRACKER_CODE';

    private $newsletter                     = false; 
    
    private $nvnewsletterIni                = null;
    private $siteIni                        = null;
    private $db                             = null;

    private $objectID                       = null;
    private $objectVersion                  = null;
    private $locale                         = null;

    private $transport                      = null;

    private $SenderFieldIdentifier          = null;
    private $GroupsFieldIdentifier          = null;
    
    private $receiverFields                 = null;

    private $senderName                     = null;
    private $senderEmail                    = null;
    private $replyToEmail                   = null;
    private $subject                        = null;
    private $charset                        = null;

    private $mail                           = null;
    private $plainTextOnly                  = null;
    private $bothParts                      = null;

    private $mailAvailability               = false;
    private $pregeneratedDataAvailability   = false;

    private $newsletterDataMap              = null;
    private $newsletterGroups               = null;

    private $personalTags                   = null;
    private $personalization                = false;

    private $mailTextContentPersonal        = false;
    private $mailHtmlContentPersonal        = false;

    private $logEveryReceiver               = false;
    
    private $createTemp                     = false;

    /**
     * Constructor
     *
     * @param int $contentObjectID
     * @param int $contentObjectVersion
     * @param string $locale
     * @param boolean $createTemp
     */
    function __construct( $contentObjectID, $contentObjectVersion, $locale, $createTemp=false ) 
    {
        $this->nvnewsletterIni = eZINI::instance( 'nvnewsletter.ini' );
        $this->siteIni         = eZINI::instance( 'site.ini' );
        $this->db              = eZDB::instance();

        $this->objectID        = $contentObjectID;
        $this->objectVersion   = $contentObjectVersion;
        $this->locale          = $locale;
        
        if ( $createTemp )
        {
            $this->createTemp = true;
        }

        // Log
        if ( $this->nvnewsletterIni->variable( 'LogSettings', 'LogEveryReceiver' ) ) 
        {
            $this->logEveryReceiver = true;
        }

        // Fetch replacements and check if we need personalization
        $this->personalTags = nvNewsletterTools::tagsAvailableForPersonalization();

        if ( is_array( $this->personalTags ) && count( $this->personalTags ) > 0 )
        {
            $this->personalization = true;
        }
        
        // Charset
        $this->charset = $this->nvnewsletterIni->variable( 'MailSendSettings', 'Charset' );

        // Fetching defined mail transport method from site.ini
        $transportType = $this->siteIni->variable( 'MailSettings', 'Transport' );

        if ( $transportType == 'sendmail' ) 
        {
            $this->transport = new ezcMailMtaTransport();
        } 
        else 
        {
            $transportServer   = $this->siteIni->variable( 'MailSettings', 'TransportServer' );
            $transportPort     = $this->siteIni->variable( 'MailSettings', 'TransportPort' );
            $transportUser     = $this->siteIni->variable( 'MailSettings', 'TransportUser' );
            $transportPassword = $this->siteIni->variable( 'MailSettings', 'TransportPassword' );

            $options = new ezcMailSmtpTransportOptions();
            
            $connectionType = $this->nvnewsletterIni->variable( 'MailSendSettings', 'ConnectionType' );
            
            if ( $connectionType )
            {
                $options->connectionType = constant( 'ezcMailSmtpTransport::'. $connectionType );
            }

            $this->transport = new ezcMailSmtpTransport( $transportServer, $transportUser, $transportPassword, $transportPort, $options );
        }

        $this->SenderFieldIdentifier = $this->nvnewsletterIni->variable( 'ContentClassSettings', 'SenderFieldIdentifier' );
        $this->GroupsFieldIdentifier = $this->nvnewsletterIni->variable( 'ContentClassSettings', 'GroupsFieldIdentifier' );

        $this->senderName   = $this->nvnewsletterIni->variable( 'MailSendSettings', 'MailDefaultSenderName' );
        $this->senderEmail  = $this->nvnewsletterIni->variable( 'MailSendSettings', 'MailDefaultSenderEmail' );
        $this->replyToEmail = $this->nvnewsletterIni->variable( 'MailSendSettings', 'MailDefaultReplyToEmail' );

        $versionObject = eZContentObject::fetch( $contentObjectID )->version( $contentObjectVersion );

        if ( $versionObject )
            $contentObject = $versionObject->contentObject();
        
        if ( $this->createTemp )
        {
            $node = nvNewsletter::tempMainNode( $versionObject );
        }
        else
        {
            $node = $contentObject->mainNode();
        }

        if ( $node )
        {
            $this->newsletterDataMap = $node->dataMap();
            $this->subject           = $node->attribute( 'name' );
            $this->mailAvailability  = true;

            // Fetching sender id from node
            $senderContent = $this->newsletterDataMap[$this->SenderFieldIdentifier]->content();
            $senderID      = $senderContent['selected'];
            $senderData    = nvNewsletterSender::fetch( $senderID );

            if ( $senderData ) 
            {
                if ( !empty( $senderData->sender_name ) ) 
                {
                    $this->senderName   = $senderData->sender_name;
                }
                
                if ( !empty( $senderData->sender_email ) )
                {
                    $this->senderEmail  = $senderData->sender_email;
                }
                
                if ( !empty( $senderData->reply_to ) )
                {
                    $this->replyToEmail = $senderData->reply_to;
                }
            }

            $this->createNewsletter();

            // Setting up basic maildata
            $this->mail                 = new ezcMail();
            $this->mail->from           = new ezcMailAddress( $this->senderEmail, $this->senderName, $this->charset );
            $this->mail->subject        = $this->subject;
            $this->mail->subjectCharset = $this->charset; 

            // If Reply-To is set
            if( $this->replyToEmail )
            { 
                $this->mail->setHeader( 'Reply-To', $this->replyToEmail ); 
            }

            // Building mail body
            $this->setMailBody();
        }
    }
    
    /**
     * Fetch newsletter object
     *
     * @param boolean $useCache Doesn't fetch newsletter if it's already fetched
     */
    function newsletter( $useCache=true )
    {
        $fetchNewsletter = false;
        
        if ( $useCache === false )
        {
            $fetchNewsletter = true;
        }
        
        if ( !$this->newsletter )
        {
            $fetchNewsletter = true;
        }
        
        if ( !$fetchNewsletter && 
              $this->newsletter->attribute('contentobject_id') !== $this->objectID &&
              $this->newsletter->attribute('contentobject_version') !== $this->objectVersion ) 
        {
            $fetchNewsletter = true;
        }
        
        if ( $fetchNewsletter )
        {
            $this->newsletter = nvNewsletter::fetchByContentObjectID( $this->objectID );
        }
        
        return $this->newsletter;
    }
    
    /**
     * Start mail sending
     */
    function startMailSending()
    {
        $newsletter = $this->newsletter();
        $now = nvNewsletterTools::currentDatetime();
        
        $newsletter->setAttribute( 'status', nvNewsletter::STATUS_SENDING );
        $newsletter->setAttribute( 'send_start_time', $now );
        $newsletter->setAttribute( 'send_last_access_time', $now );
        $newsletter->store();
    }
    
    /**
     * Restart mail sending
     */
    function restartMailSending() 
    {
        $newsletter = $this->newsletter();
        $now = nvNewsletterTools::currentDatetime();
        
        $newsletter->setAttribute( 'send_last_access_time', $now );
        $newsletter->setAttribute( 'info', 'Mail sending interrupted - resending '.$now );
        $newsletter->store();
    }
    
    /**
     * Get sent count
     *
     * @return int
     */
    function getMailSentCount() 
    {
        return $this->newsletter( false )->attribute('sent_mail_count');
    }
    
    /**
     * Update last time when mail is sent
     *
     * @param int $counter
     */
    function updateMailLastSendTime( $counter ) 
    {
        $newsletter = $this->newsletter();
        $now = nvNewsletterTools::currentDatetime();
        
        $newsletter->setAttribute( 'send_last_access_time', $now );
        $newsletter->setAttribute( 'sent_mail_count', $counter );
        $newsletter->store();
    }
    
    /**
     * Update total mail count
     *
     * @param int $counter
     */
    function updateMailReceiverCount( $counter )
    {
        $newsletter = $this->newsletter();
        
        $newsletter->setAttribute( 'total_mail_count', $counter );
        $newsletter->store();
    }
    
    /**
     * Update values when all newsletters are sent
     *
     * @param int $counter
     */
    function updateMailSendingEnd( $counter ) 
    {
        $newsletter = $this->newsletter();
        $now = nvNewsletterTools::currentDatetime();
        
        $newsletter->setAttribute( 'status', nvNewsletter::STATUS_SENT );
        $newsletter->setAttribute( 'send_last_access_time', $now );
        $newsletter->setAttribute( 'send_end_time', $now );
        $newsletter->setAttribute( 'sent_mail_count', $counter );
        
        $newsletter->store();
    }
    
    /**
     * Update errors
     *
     * @param string $errors
     */
    function updateErrors( $errors ) 
    {
        $newsletter = $this->newsletter();
        
        $newsletter->setAttribute( 'status', nvNewsletter::STATUS_FAILED );
        $newsletter->setAttribute( 'info', $errors );
        
        $newsletter->store();
    }
    
    /**
     * Get receivers from temp table for actual sending
     */
    function getReceivers()
    {
        return nvNewsletterReceiverInProgress::fetchListByContentObjectID( $this->objectID, $this->objectVersion );
    }
    
    /**
     * Populate receivers temp table
     *
     * @return boolean
     */
    function populateMailReceiverTable() 
    {
        $groupsArray = false;
        $groupsContent = $this->newsletterDataMap[$this->GroupsFieldIdentifier]->content();

        if ( is_array( $groupsContent['selected'] ) ) 
        {
            foreach ( $groupsContent['selected'] as $key => $value ) 
            {
                if ( is_numeric( $key ) ) 
                {
                    $groupsArray[] = $key;
                }
            }
        }
        
        if ( $groupsArray )
        {
            $receivers = $this->db->arrayQuery("
                SELECT 
                  r.id, r.email_address, g.mail_type 
                FROM 
                  nvnewsletter_receivers_has_groups g, nvnewsletter_receivers r 
                WHERE 
                  g.receivergroup_id IN (".implode( ', ', $groupsArray ).") AND g.receiver_id = r.id
                GROUP BY 
                  r.email_address");
            
            if ( $receivers )
            {
                $this->db->begin();

                foreach ( $receivers as $receiver ) 
                {
                    $receiverInProgress = new nvNewsletterReceiverInProgress( array( 'contentobject_id'      => $this->objectID,
                                                                                     'contentobject_version' => $this->objectVersion,
                                                                                     'receiver_id'           => $receiver['id'], 
                                                                                     'email_address'         => $receiver['email_address'],
                                                                                     'mail_type'             => $receiver['mail_type'] ) );
                    $receiverInProgress->store();
                }
                
                $this->updateMailReceiverCount( count( $receivers ) );
                $this->db->commit();
                
                unset( $receivers );
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * Generate newsletter
     */
    function createNewsletter()
    {
        if( nvNewsletter::createNewsletter( $this->objectID, $this->objectVersion, $this->locale, $this->createTemp ) ) 
        {
            $this->mailHtmlContent = nvNewsletterTools::getContent( $this->objectID, $this->objectVersion, 'html', false, $this->locale );
            $this->mailTextContent = nvNewsletterTools::getContent( $this->objectID, $this->objectVersion, 'text', false, $this->locale );

            $this->pregeneratedDataAvailability = true;
        }
    }

    /**
     * Get receiver email address
     *
     * @return string
     */
    function getReceiverEmail() 
    {
        if ( $this->mail && !empty( $this->mail->to[0]->email ) )
        {
            return $this->mail->to[0]->email;
        }
        
        return false;
    }
    
    /**
     * Get user data for personalization
     *
     * @return string
     */
    function getUserData( $fieldIndex )
    {
        if ( $this->receiverFields )
        {
            if ( isset( $this->receiverFields[$fieldIndex] ) )
            {
                return $this->receiverFields[$fieldIndex];
            }
        }
        
        return false;
    }

    /**
     * Get personalized tag array
     *
     * @param int $objectID 
     * @param int $receiverID
     * @param string $email
     * @param array $personalTags
     */
    static function personalizedTags( $objectID, $receiverID, $email, $personalTags, $fields ) 
    {
        $tags   = array();
        $values = array();

        if ( $personalTags )
        {
            foreach( $personalTags as $value ) 
            {
                switch( $value ) 
                {
                    case self::USERCODE_TAG:
                        $tags[] = "<$value>";
                        $receiverID == 0 ? $values[] = self::USERCODE_REPLACEMENT : $values[] = $receiverID . '/' . nvNewsletterTools::getUserHash( $email );
                        break;
                    case self::TRACKERCODE_TAG:
                        $tags[] = "<$value>";
                        $receiverID == 0 ? $values[] = self::TRACKERCODE_REPLACEMENT : $values[] = $objectID . '_' . $receiverID . '_' . nvNewsletterTools::getUserHash( $email );
                        break;
                    default:
                        $tags[] = "[[$value]]";
                        $receiverID == 0 ? $values[] = '__'.strtoupper( $value ).'__' : $values[] = $fields[$value];
                        break;
                }
            }
        }
        
        return array( 'tags'   => $tags, 
                      'values' => $values );
    }
    
    /**
     * Handle personalization
     */
    function personalize( $receiverID=false ) 
    {
        $tags = self::personalizedTags( $this->objectID, $receiverID, $this->getReceiverEmail(), $this->personalTags, $this->receiverFields );

        $this->mailTextContentPersonal = str_replace( $tags['tags'], $tags['values'], $this->mailTextContent );
        $this->mailHtmlContentPersonal = str_replace( $tags['tags'], $tags['values'], $this->mailHtmlContent );
        $this->mail->subject           = str_replace( $tags['tags'], $tags['values'], $this->subject );

        $this->setMailBody();
    }

    /**
     * Set mail body. Checks if newsletter is personalized
     */
    function setMailBody() 
    {
        // Building html-mail
        if ( $this->mailTextContentPersonal && $this->personalization ) 
        {
            $plainText = new ezcMailText( $this->mailTextContentPersonal, $this->charset );
        } 
        else 
        {
            $plainText = new ezcMailText( $this->mailTextContent, $this->charset );
        }
        
        $plainText->subType = 'plain';

        if ( $this->mailHtmlContentPersonal && $this->personalization ) 
        {
            $htmlText = new ezcMailText( $this->mailHtmlContentPersonal, $this->charset );
        } 
        else 
        {
            $htmlText = new ezcMailText( $this->mailHtmlContent, $this->charset );
        }
        
        $htmlText->subType = 'html';

        $this->bothParts = new ezcMailMultipartAlternative( $plainText, $htmlText );

        // Building text-mail
        if ( $this->mailTextContentPersonal && $this->personalization ) 
        {
            $this->plainTextOnly = new ezcMailText( $this->mailTextContentPersonal, $this->charset );
        } 
        else 
        {
            $this->plainTextOnly = new ezcMailText( $this->mailTextContent, $this->charset );
        }

        $this->plainTextOnly->subType = 'plain';
    }

    /**
     * Send mail
     *
     * @param int $receiverID
     * @param string $emailAddress
     * @param int $emailType
     * @param object $fields
     */
    function sendMail( $receiverID, $emailAddress, $emailType, $fields=false ) 
    {
        $this->mail->to = array( new ezcMailAddress( $emailAddress, '', $this->charset ) );
        $this->receiverFields = nvNewsletterTools::formatReceiverFields( $fields );

        if ( $this->personalization ) 
        {
            $this->personalize( $receiverID );
        }

        if( $emailType == nvNewsletter::NEWSLETTER_FORMAT_TEXT )
        {
            $this->mail->body = $this->plainTextOnly;
        } 
        else
        {
            $this->mail->body = $this->bothParts;
        }

        $this->transport->send( $this->mail );

        if ( $this->logEveryReceiver ) 
        {
            eZLog::write( "nvNewsletterMailer (nvnewslettermailer.php): mail sent to ".$this->getReceiverEmail(), "nvnewsletter_mail.log" );
        }
    }

    /**
     * Get mail availability
     *
     * @return boolean
     */
    function getMailAvailability() 
    {
        return $this->mailAvailability;
    }

    /**
     * Get pregenerated data availability
     *
     * @return boolean
     */
    function getPregeneratedDataAvailability() 
    {
        return $this->pregeneratedDataAvailability;
    }
}
?>
