<?php
/**
 * File containing the nvNewsletterClickTrackLink class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7.1b
 * @package nvNewsletter
 */
class nvNewsletterClickTrackLink extends eZPersistentObject 
{
    function __construct( $row ) 
    {
        parent::__construct( $row );
    }

    static function definition() 
    {
        return array(
                'fields' => array(
                    'id' => array(
                        'name' => 'id',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => true),
                    'link' => array(
                        'name' => 'newsletter_id',
                        'datatype' => 'string',
                        'default' => '',
                        'required' => true)),
                'keys'          => array('id'),
                'increment_key' => 'id',
                'sort'          => array('id' => 'asc'),
                'class_name'    => 'nvNewsletterClickTrackLink',
                'name'          => 'nvnewsletter_clicktrack_link' );
    }

    function attribute( $attr, $noFunction=false ) 
    {
        return eZPersistentObject::attribute( $attr );
    }

    static function fetchByLink( $link, $asObject=true ) 
    {
        return eZPersistentObject::fetchObject( self::definition(), 
                                                null,
                                                array( 'link' => $link ) );
    }

    static function create( $link = '' ) 
    {
        $link = new nvNewsletterClickTrackLink( array( 'link' => $link ) );
        $link->store();

        return $link;
    }
}
?>
