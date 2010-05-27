<?php
/**
 * File containing the nvNewsletterNotification class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7.1b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletterNotification handles notifications.
 * At the moment only unsubscribe notification implemented.
 */
class nvNewsletterNotification 
{
    function __construct() 
    {
    }
    
    /**
     * Send email notification after unsubscribe.
     * 
     * @param array $params
     */
    static function unsubscription( $params )
    {
        $ini = eZINI::instance( 'nvnewsletter.ini' );
        $emails  = $ini->variable( 'NotificationSettings', 'UnsubscribeEmail' );
        $subject = $ini->variable( 'NotificationSettings', 'UnsubscribeSubject' );
        
        if ( is_array( $emails ) )
        {
            $tpl = nvNewsletterTemplate::factory();
            $tpl->setVariable( 'receiver', $params['receiver'] );
            $content = $tpl->fetch( 'design:nvnewsletter/notification/unsubscribe.tpl' );
            
            self::send( $emails, $subject, $content );
        }
    }
    
    /**
     * Handles actual sending. Common send method for all notifications.
     * 
     * @param mixed $to Email address or array of emails
     * @param string $subject Message subject
     * @param string $content Message content
     * @return boolean
     */
    static function send( $to, $subject, $content )
    {
        $ini    = eZINI::instance( 'site.ini' );
        $sender = $ini->variable( 'MailSettings', 'EmailSender' );
        
        if ( empty( $sender ) )
        {
            $sender = $ini->variable( 'MailSettings', 'AdminEmail' );
        }
        
        $mail = new eZMail();
        $mail->setSender( $sender );
        
        $receiverSet = false;
        
        if ( !is_array( $to ) )
        {
            $to = array( $to );
        }
        
        foreach ( $to as $email )
        {
            if ( !$receiverSet )
            {
                $mail->setReceiver( $email );
                $receiverSet = true;
            }
            else
            {
                $mail->addCc( $email );
            }
        }
        
        $mail->setSubject( $subject );
        $mail->setBody( $content );
        
        $mailResult = eZMailTransport::send( $mail );
        
        return true;
    }
}
?>
