<?php
/**
 * File containing nvNewsletter open tracker
 *
 * @copyright Copyright (c) 2005-2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7.1b
 * @package nvnewsletter
 */
$emptyImage = 'extension/nvnewsletter/design/standard/images/empty.gif';

header( 'Content-Type: image/gif' );
header( 'Content-Length: ' . ( filesize( $emptyImage ) ) );

require 'autoload.php';

$http = eZHTTPTool::instance();
$code = $http->getVariable( 'code' );

if ( $code != nvNewsletterMailer::TRACKERCODE_REPLACEMENT )
{
    $arr = explode( '_', $code );
    
    if ( is_array( $arr ) && count( $arr ) == 3 )
    {
        $objectID   = $arr[0];
        $receiverID = $arr[1];
        $hash       = $arr[2];
        
        $receiver = nvNewsletterReceiver::fetch( $receiverID );
        $userHash = nvNewsletterTools::getUserHash( $receiver->attribute( 'email_address' ) );
        
        if ( $userHash == $hash && !empty( $hash ) )
        {
            $newsletter = nvNewsletter::fetchByContentObjectID( $objectID );
            
            if ( $newsletter && $newsletter->attribute('status') == nvNewsletter::STATUS_SENT )
            {
                $statistics = nvNewsletterStatistics::fetchByReceiverAction( $newsletter->attribute('id'),
                                                                             $receiverID, 
                                                                             nvNewsletterStatistics::NEWSLETTER_READ );
                if ( !$statistics ) 
                {
                    $statistics = nvNewsletterStatistics::create( $newsletter->attribute('id'),
                                                                  $receiverID, 
                                                                  nvNewsletterStatistics::NEWSLETTER_READ, 
                                                                  $hash );
                } 
                else 
                {
                    $statistics->setAttribute( 'data_int', $statistics->attribute('data_int')+1 );
                    $statistics->store();
                }
            }
        }
    }
}

readfile( $emptyImage );
?>
