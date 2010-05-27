<?php
/**
 * File containing the nvNewsletterStatistics class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7.1b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletterStatistics contains statistics stuff
 */
class nvNewsletterStatistics extends eZPersistentObject 
{
    const NEWSLETTER_READ = 1;
    const NEWSLETTER_UNSUBSCRIBE = 2;
    const NEWSLETTER_LINK_CLICK = 3;
    const NEWSLETTER_BOUNCE_HARD = 4;
    const NEWSLETTER_BOUNCE_SOFT = 5;

    function __construct($row) 
    {
        parent::__construct($row);
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
                    'newsletter_id' => array(
                        'name' => 'newsletter_id',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => true),
                    'receiver_id' => array(
                        'name' => 'receiver_id',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => true),
                    'action' => array(
                        'name' => 'action',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => true),
                    'action_date' => array(
                        'name' => 'action_date',
                        'datatype' => 'string',
                        'default' => '',
                        'required' => true),
                    'data_text' => array(
                        'name' => 'data_text',
                        'datatype' => 'string',
                        'default' => '',
                        'required' => false),
                    'data_int' => array(
                        'name' => 'data_int',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => false)),
                'keys'          => array('id'),
                'increment_key' => 'id',
                'sort'          => array('action_date' => 'asc'),
                'function_attributes' => array(
                    'receiver' => 'receiver',
                    'newsletter' => 'newsletter'),
                'class_name'    => 'nvNewsletterStatistics',
                'name'          => 'nvnewsletter_statistics' );
    }

    function attribute($attr, $noFunction = false) 
    {
        return eZPersistentObject::attribute($attr);
    }

    static function fetchByReceiverAction( $newsletterID, $receiverID, $action, $asObject=true ) 
    {
        return eZPersistentObject::fetchObject( self::definition(), 
                                                null,
                                                array( 'newsletter_id' => $newsletterID,
                                                       'receiver_id' => $receiverID,
                                                       'action' => $action), 
                                                $asObject);
    }

    static function fetchByNewsletterAction( $newsletterID, $action, $asObject=true ) 
    {
        return eZPersistentObject::fetchObjectList( self::definition(), 
                                                    null,
                                                    array( 'newsletter_id' => $newsletterID,
                                                           'action' => $action ), 
                                                    $asObject);
    }

    static function create( $newsletterID=0, $receiverID=0, $action=0, $data_text=null, $data_int=1 ) 
    {
        $statistics = new nvNewsletterStatistics( array( 'newsletter_id' => $newsletterID,
                                                         'receiver_id' => $receiverID,
                                                         'action' => $action,
                                                         'action_date' => date('Y-m-d H:i:00'),
                                                         'data_text' => $data_text,
                                                         'data_int' => $data_int ));
        $statistics->store();

        return $statistics;
    }
}
?>
