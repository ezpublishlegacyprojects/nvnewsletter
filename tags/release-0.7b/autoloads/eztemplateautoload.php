<?php
/**
 * Operator autoload
 *
 * @copyright Copyright (c) 2005-2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7b
 * @package nvNewsletter
 */

$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array( 'script' => 'extension/nvnewsletter/classes/nvnewsletteroperator.php',
                                    'class' => 'nvNewsletterOperator',
                                    'operator_names' => array( 'nvnewslettergetviewlink', 'nvnewslettergetsitelink', 'is_nvnewsletter' ) );
?>
