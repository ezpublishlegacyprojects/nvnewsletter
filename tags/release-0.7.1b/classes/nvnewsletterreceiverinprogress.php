<?php
/**
 * File containing the nvNewsletterReceiverInProgress class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7.1b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletterReceiverInProgress handles newsletter receivers
 */
class nvNewsletterReceiverInProgress extends eZPersistentObject 
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
                    'contentobject_id' => array(
                        'name' => 'contentobject_id',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => true ),
                    'contentobject_version' => array(
                        'name' => 'contentobject_version',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => true ),
                    'receiver_id' => array(
                        'name' => 'receiver_id',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => true ),
                    'email_address' => array(
                        'name' => 'email_address',
                        'datatype' => 'string',
                        'default' => '',
                        'required' => true),
                    'mail_type' => array(
                        'name' => 'mail_type',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => true ) ),
                'keys'          => array('id'),
                'increment_key' => 'id',
                'sort'          => array('id' => 'asc'),
                'function_attributes' => array(
                    'receiver' => 'receiver' ),
                'class_name'    => 'nvNewsletterReceiverInProgress',
                'name'          => 'nvnewsletter_receivers_in_progress' );
    }
    
    static function fetchListByContentObjectID( $contentObjectID, $contentObjectVersion, $asObject=true ) 
    {
        return eZPersistentObject::fetchObjectList( self::definition(), 
                                                    null, 
                                                    array( 'contentobject_id' => $contentObjectID,
                                                           'contentobject_version' => $contentObjectVersion ),
                                                    array( 'email_address' => 'asc' ),
                                                    null,
                                                    $asObject,
                                                    array( 'email_address' ) );
    }
    
    function receiver()
    {
        return nvNewsletterReceiver::fetch( $this->attribute( 'receiver_id' ) );
    }
}
?>
