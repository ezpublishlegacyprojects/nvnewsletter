<?php
/**
 * File containing the nvNewsletterReceiverGroup class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7.1b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletterReceiverGroup handles newsletter receiver groups
 */
class nvNewsletterReceiverGroup extends eZPersistentObject 
{
    const STATUS_DRAFT     = 0;
    const STATUS_PUBLISHED = 1;

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
                    'group_name' => array(
                        'name' => 'group_name',
                        'datatype' => 'string',
                        'default' => '',
                        'required' => true),
                    'group_description' => array(
                        'name' => 'group_description',
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
                'function_attributes' => array(
                    'members_count' => 'membersCount',
                    'members_unsubscribed_count' => 'membersUnsubscribedCount' ),
                'class_name'    => 'nvNewsletterReceiverGroup',
                'name'          => 'nvnewsletter_receivergroups');
    }

    function attribute( $attr, $noFunction=false ) 
    {
        return eZPersistentObject::attribute( $attr );
    }

    static function fetchByOffset( $status=nvNewsletterReceiverGroup::STATUS_PUBLISHED, $limit=null, $sorts=array( 'id' => 'asc') , $asObject=true ) 
    {
        return eZPersistentObject::fetchObjectList( self::definition(), 
                                                    null, 
                                                    array( 'status' => $status ),
                                                    $sorts, 
                                                    $limit, 
                                                    $asObject );
    }

    static function fetchList( $status=nvNewsletterReceiverGroup::STATUS_PUBLISHED, $asObject=true ) 
    {
        return eZPersistentObject::fetchObjectList( self::definition(), 
                                                    null, 
                                                    array('status' => $status), 
                                                    null, 
                                                    null, 
                                                    $asObject );
    }

    static function fetch( $id, $status=nvNewsletterReceiverGroup::STATUS_PUBLISHED, $asObject=true ) 
    {
        return eZPersistentObject::fetchObject( self::definition(), 
                                                null,
                                                array( 'id' => $id, 
                                                       'status' => $status ), 
                                                $asObject);
    }

    static function fetchDraft( $id, $asObject=true ) 
    {
        $group = nvNewsletterReceiverGroup::fetch( $id, nvNewsletterReceiverGroup::STATUS_DRAFT, $asObject );
        
        if ( !$group ) 
        {
            $group = nvNewsletterReceiverGroup::fetch( $id, nvNewsletterReceiverGroup::STATUS_PUBLISHED, $asObject );
            
            if ( $group ) 
            {
                $group->setAttribute( 'status', nvNewsletterReceiverGroup::STATUS_DRAFT );
                $group->store();
            }
        }

        if ( $group ) 
        {
            return $group;
        }

        return false;
    }
    
    /**
     * Fetch group unsubscribed members
     */
    static function membersUnsubscribed( $receiverGroupID, $limit=false, $asObject=true ) 
    {
        return eZPersistentObject::fetchObjectList( nvNewsletterReceiver::definition(), 
                                                    array( 'id', 
                                                           'email_address' ),
                                                    array( 'nvnewsletter_receivers.status' => nvNewsletterReceiver::STATUS_PUBLISHED ), 
                                                    null, 
                                                    $limit, 
                                                    $asObject, 
                                                    null, 
                                                    array( 'mail_type, unsub_date, nrhgu.status AS subscribe_status' ),
                                                    array( 'nvnewsletter_receivers_has_groups_unsub AS nrhgu' ), 
                                                    ' AND nrhgu.receiver_id = nvnewsletter_receivers.id 
                                                      AND nrhgu.receivergroup_id = '.$receiverGroupID );
    }
    
    function membersUnsubscribedCount() 
    {
        $db = eZDB::instance();
        $members = $db->arrayQuery("SELECT COUNT( r.id ) AS count 
                                    FROM 
                                          nvnewsletter_receivers r, 
                                          nvnewsletter_receivers_has_groups_unsub nrhg 
                                    WHERE r.id = nrhg.receiver_id AND 
                                          r.status = ".nvNewsletterReceiver::STATUS_PUBLISHED." AND 
                                          nrhg.receivergroup_id = ".$this->attribute('id') );
        return $members[0]['count'];
    }
    
    /**
     * Fetch group subscribed members
     */
    static function members( $receiverGroupID, $limit=false, $asObject=true ) 
    {
        return eZPersistentObject::fetchObjectList( nvNewsletterReceiver::definition(), 
                                                    array( 'id', 
                                                           'email_address' ),
                                                    array( 'nvnewsletter_receivers.status' => nvNewsletterReceiver::STATUS_PUBLISHED ), 
                                                    null, 
                                                    $limit, 
                                                    $asObject,
                                                    null, 
                                                    array( 'mail_type, pub_date, nrhg.status AS subscribe_status' ),
                                                    array( 'nvnewsletter_receivers_has_groups AS nrhg' ), 
                                                    ' AND nrhg.receiver_id = nvnewsletter_receivers.id 
                                                      AND nrhg.receivergroup_id = '.$receiverGroupID );
    }
    
    function membersCount() 
    {
        $db = eZDB::instance();
        $members = $db->arrayQuery("SELECT COUNT( r.id ) AS count 
                                    FROM 
                                        nvnewsletter_receivers r, 
                                        nvnewsletter_receivers_has_groups nrhg 
                                    WHERE r.id = nrhg.receiver_id AND 
                                          r.status = ".nvNewsletterReceiver::STATUS_PUBLISHED." AND 
                                          nrhg.receivergroup_id = ".$this->attribute('id') );
        return $members[0]['count'];
    }

    function publish() 
    {
        $this->setAttribute( 'status', nvNewsletterReceiverGroup::STATUS_PUBLISHED );
        $this->store();
        $this->removeDraft();
    }

    function removeDraft() 
    {
        $groupDraft = nvNewsletterReceiverGroup::fetchDraft( $this->attribute('id') );
        $groupDraft->remove();
    }

    static function removeAll( $id ) 
    {
        eZPersistentObject::removeObject( nvNewsletterReceiverGroup::definition(),
                                          array('id' => $id ) );
                
        $db = eZDB::instance();
        $db->query("DELETE FROM nvnewsletter_receivers_has_groups WHERE receivergroup_id = $id");
        $db->query("DELETE FROM nvnewsletter_receivers_has_groups_unsub WHERE receivergroup_id = $id");
        $db->query("DELETE FROM 
                        nvnewsletter_receivers 
                    WHERE 
                        nvnewsletter_receivers.id NOT IN ( SELECT receiver_id FROM nvnewsletter_receivers_has_groups ) AND 
                        nvnewsletter_receivers.id NOT IN ( SELECT receiver_id FROM nvnewsletter_receivers_has_groups_unsub )");
        $db->query("DELETE FROM 
                        nvnewsletter_receivers_has_fields 
                    WHERE 
                        nvnewsletter_receivers_has_fields.receiver_id NOT IN ( SELECT receiver_id FROM nvnewsletter_receivers_has_groups ) AND 
                        nvnewsletter_receivers_has_fields.receiver_id NOT IN ( SELECT receiver_id FROM nvnewsletter_receivers_has_groups_unsub )");
    }

    static function create( $name='', $desc='' ) 
    {
        $group = new nvNewsletterReceiverGroup( array( 'group_name' => $name,
                                                       'group_description' => $desc,
                                                       'status' => nvNewsletterReceiverGroup::STATUS_DRAFT ) );
        $group->store();

        return $group;
    }
}
?>
