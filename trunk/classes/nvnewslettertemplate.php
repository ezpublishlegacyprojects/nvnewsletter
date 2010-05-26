<?php
/**
 * File containing the nvNewsletterTemplate class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletterTemplate provides template specific functions
 */
class nvNewsletterTemplate
{
    /**
     * Instantiate template system. Provides backwards compatibility
     * by checking if factory method exists (implemented in 4.3).
     */
    static function factory()
    {
        if ( method_exists( 'eZTemplate', 'factory' ) )
        {
            $tpl = eZTemplate::factory();
        }
        else
        {
            require_once( 'kernel/common/template.php' );
            $tpl = templateInit();
        }
        
        return $tpl;
    }
}
?>
