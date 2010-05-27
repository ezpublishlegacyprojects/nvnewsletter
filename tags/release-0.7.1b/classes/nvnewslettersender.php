<?php
/**
 * File containing the nvNewsletterSender class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7.1b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletterSender handles newsletter sender details
 */
class nvNewsletterSender extends eZPersistentObject 
{
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;

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
                    'sender_name' => array(
                        'name' => 'sender_name',
                        'datatype' => 'string',
                        'default' => '',
                        'required' => false),
                    'sender_email' => array(
                        'name' => 'sender_email',
                        'datatype' => 'string',
                        'default' => '',
                        'required' => true),
                    'reply_to' => array(
                        'name' => 'reply_to',
                        'datatype' => 'string',
                        'default' => '',
                        'required' => false),
                    'status' => array(
                        'name' => 'status',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => true)),
                'keys'          => array('id', 'status'),
                'increment_key' => 'id',
                'sort'          => array('id' => 'asc'),
                'class_name'    => 'nvNewsletterSender',
                'name'          => 'nvnewsletter_senders');
    }

    function attribute( $attr, $noFunction = false ) 
    {
        return eZPersistentObject::attribute($attr);
    }

    static function fetchByOffset( $status=self::STATUS_PUBLISHED, $limit=false, $sorts=array('id' => 'asc'), $asObject=true ) 
    {
        return eZPersistentObject::fetchObjectList( self::definition(), 
                                                    null, 
                                                    array('status' => $status),
                                                    $sorts, 
                                                    $limit, 
                                                    $asObject );
    }

    static function fetchList( $status=self::STATUS_PUBLISHED, $asObject = true) 
    {
        return eZPersistentObject::fetchObjectList( self::definition(), 
                                                    null, 
                                                    array('status' => $status), 
                                                    null, 
                                                    null, 
                                                    $asObject);
    }

    static function fetch($senderID, $status=self::STATUS_PUBLISHED, $asObject=true) 
    {
        return eZPersistentObject::fetchObject( self::definition(), 
                                                null,
                                                array( 'id' => $senderID, 
                                                       'status' => $status ), 
                                                $asObject);
    }

    static function fetchDraft( $id, $asObject=true ) 
    {
        $sender = self::fetch( $id, self::STATUS_DRAFT, $asObject );
        
        if ( !$sender ) 
        {
            $sender = self::fetch( $id, self::STATUS_PUBLISHED, $asObject );
            
            if ( $sender ) 
            {
                $sender->setAttribute('status', self::STATUS_DRAFT);
                $sender->store();
            }
        }

        if ( !$sender ) 
        {
            return false;
        }

        return $sender;
    }

    function publish() 
    {
        $this->setAttribute('status', self::STATUS_PUBLISHED );
        $this->store();
        $this->removeDraft();
    }

    function removeDraft() 
    {
        $senderDraft = self::fetchDraft($this->attribute('id'));
        $senderDraft->remove();
    }

    static function removeAll($id) 
    {
        eZPersistentObject::removeObject( self::definition(),
                                          array('id' => $id ) );
    }

    static function create( $name = '', $email = '', $replyto = '' ) 
    {
        $sender = new nvNewsletterSender(array( 'sender_name' => $name,
                                                'sender_email' => $email,
                                                'reply_to' => $replyto,
                                                'status' => self::STATUS_DRAFT ) );
        $sender->store();

        return $sender;
    }
}
