<?php
/**
 * File containing the nvNewsletterAdmin class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletterAdmin handles miscellaneous tasks
 */
class nvNewsletterAdmin 
{
    function __construct() {}
    
    /**
     * Admin list limit
     */
    static function getAdminListLimit() 
    {
        $limit = eZPreferences::value( 'admin_list_limit' );
        
        switch ( $limit ) {
            case 1:
                $limit = 10;
                break;
            case 2:
                $limit = 25;
                break;
            case 3:
                $limit = 50;
                break;
            default:
                $limit = 25;
                break;
        }
        
        return $limit;
    }
    
    /**
     * Set admin logged
     */
    static function setLoggedIn()
    {
        $ini = eZINI::instance( 'nvnewsletter.ini' );
        
        $userID = $ini->variable( 'UserSettings', 'AdminID' );
        $user   = eZUser::fetch( $userID );

        if ( $user instanceof eZUser ) 
        {
            $user->loginCurrent();
        }
    }
}
?>
