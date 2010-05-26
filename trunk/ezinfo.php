<?php
/**
 * File containing the nvNewsletterInfo class
 *
 * @copyright Copyright (c) 2005-2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7b
 * @package nvnewsletter
 */

class nvNewsletterInfo
{
    static function info()
    {
        return array( 'Name' => '<a href="http://projects.ez.no/nvnewsletter">nvNewsletter</a> by <a href="http://www.naviatech.fi/">Naviatech Solutions Oy</a>',
                      'Version' => '0.7b',
                      'Copyright' => 'Copyright (C) 2009-' . date('Y') . ' Naviatech Solutions Oy',
                      'Author' => 'Naviatech Solutions Oy',
                      'License' => 'GNU General Public License v2.0'
                     );
    }
}
?>
