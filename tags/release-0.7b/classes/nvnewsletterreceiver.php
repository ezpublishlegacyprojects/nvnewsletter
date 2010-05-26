<?php
/**
 * File containing the nvNewsletterReceiver class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletterReceiver handles newsletter receivers
 */
class nvNewsletterReceiver extends eZPersistentObject 
{
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;
    
    // Following used by group basis
    const STATUS_GROUP_PENDING   = 1;
    const STATUS_GROUP_CONFIRMED = 2;
    const STATUS_GROUP_APPROVED  = 3;
    const STATUS_GROUP_UNSUBSCRIBED_BY_USER = 11;
    const STATUS_GROUP_UNSUBSCRIBED_BY_ADMIN = 12;

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
                    'email_address' => array(
                        'name' => 'email_address',
                        'datatype' => 'string',
                        'default' => '',
                        'required' => true),
                    'status' => array(
                        'name' => 'status',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => true)),
                'keys'          => array('id', 'status'),
                'increment_key' => 'id',
                'sort'          => array('id' => 'asc'),
                'function_attributes' => array(
                    'groups'       => 'groups',
                    'group_ids'    => 'groupIDs',
                    'fields'       => 'fields',
                    'groups_unsubscribed' => 'groupsUnsubscribed',
                    'ezuser'       => 'eZUserByEmail',
                    'groups_status' => 'groupsStatus', 
                    'groups_unsubscribed_status' => 'groupsUnsubscribedStatus' ),
                'class_name'    => 'nvNewsletterReceiver',
                'name'          => 'nvnewsletter_receivers');
    }

    function attribute($attr, $noFunction = false) 
    {
        $retVal = eZPersistentObject::attribute($attr);
        return $retVal;
    }

    static function fetchByOffset( $status=self::STATUS_PUBLISHED, $limit=null, $sorts=array('id' => 'asc'), $asObject=true ) 
    {
        return eZPersistentObject::fetchObjectList( self::definition(), 
                                                    null, 
                                                    array( 'status' => $status ),
                                                    $sorts, 
                                                    $limit, 
                                                    $asObject);
    }

    static function fetchByEmail( $email, $asObject =true ) 
    {
        return eZPersistentObject::fetchObject( self::definition(), 
                                                null,
                                                array('email_address' => $email), 
                                                $asObject );
    }

    static function fetchList( $status=self::STATUS_PUBLISHED, $asObject=true ) 
    {
        return eZPersistentObject::fetchObjectList( self::definition(), 
                                                    null, 
                                                    array('status' => $status), 
                                                    null, 
                                                    null, 
                                                    $asObject );
    }

    static function fetch( $receiverID, $status=self::STATUS_PUBLISHED, $asObject=true ) 
    {
        return eZPersistentObject::fetchObject( self::definition(), 
                                                null,
                                                array( 'id' => $receiverID, 
                                                       'status' => $status), 
                                                $asObject );
    }

    static function fetchDraft( $id, $asObject=true ) 
    {
        $receiver = self::fetch( $id, self::STATUS_DRAFT, $asObject );
        
        if ( !$receiver ) 
        {
            $receiver = self::fetch( $id, self::STATUS_PUBLISHED, $asObject );
            
            if ( $receiver ) 
            {
                $receiver->setAttribute( 'status', self::STATUS_DRAFT );
                $receiver->store();
            }
        }

        if ( !$receiver ) 
        {
            return false;
        }

        return $receiver;
    }
    
    static function getUnsubscribedCount() 
    {
        $db = eZDB::instance();
        $result = $db->arrayQuery( "SELECT 
                                        COUNT( rhg.receiver_id ) AS count
                                    FROM 
                                        nvnewsletter_receivergroups g, 
                                        nvnewsletter_receivers_has_groups_unsub rhg
                                    WHERE 
                                        g.id = rhg.receivergroup_id AND 
                                        g.status = ".self::STATUS_PUBLISHED );
        return $result[0]['count'];
    }
    
    function eZUserByEmail() 
    {
        return eZUser::fetchByEmail( $this->attribute( 'email_address' ) );
    }
    
    function groupsStatus()
    {
        $db = eZDB::instance();
        $results = $db->arrayQuery(" SELECT 
                                        g.id, 
                                        nrhg.status
                                     FROM 
                                         nvnewsletter_receivergroups g, 
                                         nvnewsletter_receivers_has_groups nrhg
                                     WHERE 
                                         g.id = nrhg.receivergroup_id AND 
                                         g.status = ".nvNewsletterReceiverGroup::STATUS_PUBLISHED." AND
                                         nrhg.receiver_id = " . $this->attribute('id') );
        $ret = array();
        foreach ( $results as $result )
        {
            $ret[$result['id']] = $result['status'];
        }
        return $ret;
    }
    
    function groupsUnsubscribedStatus()
    {
        $db = eZDB::instance();
        $results = $db->arrayQuery(" SELECT 
                                        g.id, 
                                        nrhgu.status
                                     FROM 
                                         nvnewsletter_receivergroups g, 
                                         nvnewsletter_receivers_has_groups_unsub nrhgu
                                     WHERE 
                                         g.id = nrhgu.receivergroup_id AND 
                                         g.status = ".nvNewsletterReceiverGroup::STATUS_PUBLISHED." AND
                                         nrhgu.receiver_id = " . $this->attribute('id') );
        $ret = array();
        foreach ( $results as $result )
        {
            $ret[$result['id']] = $result['status'];
        }
        return $ret;
    }
    
    function groupsUnsubscribed( $asObject=false ) 
    {
        $db = eZDB::instance();
        $ret = $db->arrayQuery("SELECT 
                                    g.*, 
                                    nrhgu.mail_type, 
                                    nrhgu.unsub_date, 
                                    nrhgu.status
                                FROM 
                                    nvnewsletter_receivergroups g, 
                                    nvnewsletter_receivers_has_groups_unsub nrhgu
                                WHERE 
                                    g.id = nrhgu.receivergroup_id AND 
                                    g.status = ".nvNewsletterReceiverGroup::STATUS_PUBLISHED." AND
                                    nrhgu.receiver_id = ".$this->attribute('id') );
        return $ret;
    }

    function groupsUnsubscribedIDs() 
    {
        $db = eZDB::instance();
        $receiverGroupIDs = $db->arrayQuery("SELECT 
                                                 g.id 
                                             FROM 
                                                 nvnewsletter_receivergroups g, 
                                                 nvnewsletter_receivers_has_groups_unsub nrhgu
                                             WHERE 
                                                 g.id = nrhgu.receivergroup_id AND 
                                                 g.status = ".nvNewsletterReceiverGroup::STATUS_PUBLISHED." AND
                                                 nrhgu.receiver_id = ".$this->attribute('id') );
                
        $groupIDs = array();
        
        foreach ( $receiverGroupIDs as $groupID ) 
        {
            $groupIDs[] = $groupID['id'];
        }

        return $groupIDs;
    }
    
    function groups( $groupIDs=false, $asObject=false ) 
    {
        $db = eZDB::instance();
        
        $groupIDSQL = '';
        
        if ( is_array( $groupIDs ) ) 
        {
            $tempIDs = array();
            
            foreach ( $groupIDs as $groupID ) 
            {
                if (is_numeric( $groupID )) 
                {
                    $tempIDs[] = $groupID;
                }
            }
            if ( count( $tempIDs ) > 0  )
            {
                $groupIDSQL = " AND g.id IN (".implode( ', ', $tempIDs ).")";
            }
        }

        $ret = $db->arrayQuery("SELECT 
                                    g.*, 
                                    nrhg.mail_type, 
                                    nrhg.pub_date, 
                                    nrhg.status 
                                FROM 
                                    nvnewsletter_receivergroups g, 
                                    nvnewsletter_receivers_has_groups nrhg
                                WHERE 
                                    g.id = nrhg.receivergroup_id AND 
                                    g.status = ".nvNewsletterReceiverGroup::STATUS_PUBLISHED . " 
                                    $groupIDSQL AND 
                                    nrhg.receiver_id = ".$this->attribute('id') );
        return $ret;
    }

    function groupIDs() 
    {
        $db = eZDB::instance();
        $receiverGroupIDs = $db->arrayQuery("SELECT 
                                                 g.id 
                                             FROM 
                                                 nvnewsletter_receivergroups g, 
                                                 nvnewsletter_receivers_has_groups nrhg
                                             WHERE 
                                                 g.id = nrhg.receivergroup_id AND 
                                                 g.status = ".nvNewsletterReceiverGroup::STATUS_PUBLISHED." AND
                                                 nrhg.receiver_id = ".$this->attribute('id') );
                
        $groupIDs = array();
        
        foreach ( $receiverGroupIDs as $groupID ) 
        {
            $groupIDs[] = $groupID['id'];
        }

        return $groupIDs;
    }

    /**
     * Update receiver groups. In admin interface we want to replace all groups.
     *
     * @param array $receiverGroupIDs
     * @param array $receiverGroupFormats
     * @param array $subscribeStatusArray
     * @param array $unsubscribeStatusArray
     */
    function setReceiverGroups( $receiverGroupIDs, $receiverGroupFormats=array(), $subscribeStatusArray=false, $unsubscribeStatusArray=false ) 
    {
        $db = eZDB::instance();
        $db->begin();
        
        $receiverID = $this->attribute('id');
        
        $groupUnsub = false;
        $groupIDs   = false;
        $groups     = $this->groups();
        
        // Check if there is unsub groups
        if ( $groups ) 
        {
            foreach ( $groups as $group ) 
            {
                if ( !in_array( $group['id'], $receiverGroupIDs ) ) 
                {
                    $groupUnsub[] = $group;
                }
            }
            
            if ( $groupUnsub ) 
            {
                foreach ( $groupUnsub as $group ) 
                {
                    $unsubscribeStatus = self::STATUS_GROUP_UNSUBSCRIBED_BY_ADMIN;
                    
                    if ( $unsubscribeStatusArray )
                    {
                        if ( array_key_exists( $groupID, $subsribeStatusArray ) )
                        {
                            if ( $unsubscribeStatusArray[$groupID] === self::STATUS_GROUP_UNSUBSCRIBED_BY_USER ||
                                 $unsubscribeStatusArray[$groupID] === self::STATUS_GROUP_UNSUBSCRIBED_BY_ADMIN )
                            {
                                $unsubscribeStatus = $unsubscribeStatusArray[$groupID];
                            }
                        }
                    } 
                
                    $db->query("INSERT INTO nvnewsletter_receivers_has_groups_unsub ( receiver_id, receivergroup_id, mail_type, unsub_date, status ) 
                                                                             VALUES ( $receiverID, ".$group["id"].", ".$group["mail_type"].", NOW(), $unsubscribeStatus )");
                }
            }
        }
        
        // Sanitize group IDs
        foreach ( $receiverGroupIDs as $groupID ) 
        {
            if ( is_numeric( $groupID ) ) 
            {
                $groupIDs[] = $groupID;
            }            
        }

        // Delete all group relations
        $db->query("DELETE FROM nvnewsletter_receivers_has_groups WHERE receiver_id = $receiverID");
        
        if ( $groupIDs ) 
        {  
            // Insert selected groups
            foreach ( $groupIDs as $groupID ) 
            {
                $subscribeStatus = self::STATUS_GROUP_APPROVED;
                
                if ( $subscribeStatusArray )
                {
                    if ( array_key_exists( $groupID, $subscribeStatusArray ) )
                    {
                        if ( $subscribeStatusArray[$groupID] === self::STATUS_GROUP_PENDING ||
                             $subscribeStatusArray[$groupID] === self::STATUS_GROUP_APPROVED )
                        {
                            $subscribeStatus = $subscribeStatusArray[$groupID];
                        }
                    }
                } 
                $format = (int)( array_key_exists( $groupID, $receiverGroupFormats ) ) ? $receiverGroupFormats[$groupID] : 1;
                $db->query("INSERT INTO nvnewsletter_receivers_has_groups ( receiver_id, receivergroup_id, mail_type, pub_date, status ) 
                                                                   VALUES ( $receiverID, $groupID, $format, NOW(), $subscribeStatus ) ");
            }
            
            // Delete unsub groups
            if ( $groupIDs ) 
            {
                $db->query("DELETE FROM nvnewsletter_receivers_has_groups_unsub WHERE receiver_id = $receiverID AND receivergroup_id IN ( ".implode(', ', $groupIDs )." )");
            }
        }

        $db->commit();
    }
    
    /**
     * Update receiver groups. Used for subscribe form or new user creation. 
     * We don't want to delete existing group subscriptions.
     *
     * @param array $receiverGroupIDs
     * @param array $receiverGroupFormats
     * @param boolean $keepUnsubscribeStatus
     * @param array $subscribeStatusArray
     */
    function updateReceiverGroups( $receiverGroupIDs, $receiverGroupFormats=array(), $keepUnsubscribeStatus=false, $subscribeStatusArray=false ) 
    {
        $db = eZDB::instance();
        $db->begin();
        
        $receiverID = $this->attribute('id');
        $groupIDs   = false;
        
        $currentGroupsUnsubscribedIDs = array();
        
        if ( $keepUnsubscribeStatus ) 
        {
            $currentGroupsUnsubscribedIDs = $this->groupsUnsubscribedIDs();
        }
        
        foreach ( $receiverGroupIDs as $groupID ) 
        {
            if ( is_numeric( $groupID ) && !in_array( $groupID, $currentGroupsUnsubscribedIDs ) ) 
            {
                $groupIDs[] = $groupID;
            }            
        }
        
        if ( $groupIDs ) 
        {
            foreach ( $groupIDs as $groupID ) 
            {
                $subscribeStatus = self::STATUS_GROUP_APPROVED;
                
                if ( $subscribeStatusArray )
                {
                    if ( array_key_exists( $groupID, $subscribeStatusArray ) )
                    {
                        if ( $subscribeStatusArray[$groupID] === self::STATUS_GROUP_PENDING ||
                             $subscribeStatusArray[$groupID] === self::STATUS_GROUP_APPROVED )
                        {
                            $subscribeStatus = $subscribeStatusArray[$groupID];
                        }
                    }
                } 

                $format = (int)( array_key_exists( $groupID, $receiverGroupFormats ) ) ? $receiverGroupFormats[$groupID] : 1;
                $db->query("REPLACE INTO 
                                nvnewsletter_receivers_has_groups ( receiver_id, receivergroup_id, mail_type, pub_date, status ) 
                            VALUES 
                                ( $receiverID, $groupID, $format, NOW(), $subscribeStatus )");
            }
            
            if ( $groupIDs ) 
            {
                $db->query("DELETE FROM 
                                nvnewsletter_receivers_has_groups_unsub 
                            WHERE 
                                receiver_id = $receiverID AND 
                                receivergroup_id IN ( ".implode(', ', $groupIDs )." )");
            }
        }
        
        $db->commit();
    }

    /**
     * Get fields
     */
    function fields( $asObject=false ) 
    {
        $db = eZDB::instance();

        $receiverID = $this->attribute('id');
        $receiverFields = $db->arrayQuery("SELECT 
                                               f.*
                                           FROM     
                                               nvnewsletter_receiverfields f
                                           WHERE 
                                               f.status = ".nvNewsletterReceiverField::STATUS_PUBLISHED." 
                                           ORDER BY 
                                               f.field_order");

        $receiverFieldValues = $db->arrayQuery("SELECT 
                                                    rhf.receiverfield_id, 
                                                    rhf.data
                                                FROM 
                                                    nvnewsletter_receivers_has_fields rhf
                                                WHERE 
                                                    rhf.receiver_id = $receiverID");

        foreach ( $receiverFields as $idx => $field ) 
        {
            foreach ( $receiverFieldValues as $value ) 
            {
                if ( $field['id'] == $value['receiverfield_id'] ) 
                {
                    $receiverFields[$idx]['value'] = $value['data'];
                }
            }
        }

        return $receiverFields;
    }

    /**
     * Set receiver fields
     *
     * @param array $receiverFields
     */
    function setReceiverFields( $receiverFields ) 
    {
        $db = eZDB::instance();
        $db->begin();
        
        $receiverID = $this->attribute('id');
        $keyArray = false;
        
        foreach ( $receiverFields as $key => $field ) 
        {
            if ( is_numeric( $key ) ) 
            {
                $keyArray[] = $key;
            }
        }
        
        if ( $keyArray )
        {
            $db->query("DELETE FROM nvnewsletter_receivers_has_fields WHERE receiver_id = $receiverID AND receiverfield_id IN ( ".implode( ',', $keyArray )." )");
            
            foreach ( $receiverFields as $key => $field ) 
            {
                if ( is_numeric( $key ) ) 
                {
                    $db->query("INSERT INTO nvnewsletter_receivers_has_fields (receiver_id, receiverfield_id, data) VALUES ($receiverID, $key, '".$db->escapeString( $field )."')");
                }
            }
        }
        
        $db->commit();
    }

    function publish() 
    {
        $this->setAttribute('status', nvNewsletterReceiver::STATUS_PUBLISHED);
        $this->store();
        $this->removeDraft();
    }

    function removeDraft() 
    {
        $receiverDraft = self::fetchDraft($this->attribute('id'));
        $receiverDraft->remove();
    }

    static function removeAll( $id ) 
    {
        eZPersistentObject::removeObject( self::definition(),
                                          array('id' => $id));
        $db = eZDB::instance();
        $db->query("DELETE FROM nvnewsletter_receivers_has_fields WHERE receiver_id = $id");
        $db->query("DELETE FROM nvnewsletter_receivers_has_groups WHERE receiver_id = $id");
        $db->query("DELETE FROM nvnewsletter_receivers_has_groups_unsub WHERE receiver_id = $id");
    }

    static function create( $email ) 
    {
        $receiver = new nvNewsletterReceiver( array( 'email_address' => $email,
                                                     'status'        => self::STATUS_DRAFT ) );
        $receiver->store();

        return $receiver;
    }
    
    /** 
     * Unsubscribe all user groups. Insert receivers to unsubscribe table.
     *
     * @param array $groupIDs
     * @return boolean
     */
    function unsubscribeGroups( $groupIDs=false, $status=false ) 
    {
        if ( !$status )
        {
            $status = self::STATUS_GROUP_UNSUBSCRIBED_BY_USER;
        }
        
        if ( !$groupIDs ) 
        {
            $groups = $this->groups();
        } 
        else 
        {
            $groups = $this->groups( $groupIDs );
        }
        
        if ( is_array( $groups ) && count( $groups ) > 0 ) 
        {
            $db = eZDB::instance();
            $db->begin();
        
            $receiverID = $this->attribute('id');
            
            foreach ( $groups as $group ) 
            {
                $groupIDs[] = $group["id"];
                $db->query("INSERT INTO nvnewsletter_receivers_has_groups_unsub ( receiver_id, receivergroup_id, mail_type, unsub_date, status ) 
                                                                         VALUES ( $receiverID, ".$group["id"].", ".$group["mail_type"].", NOW(), $status )");
            }
            
            $db->query("DELETE FROM nvnewsletter_receivers_has_groups WHERE receiver_id = $receiverID AND receivergroup_id IN ( ".implode(', ', $groupIDs )." )");
            $db->commit();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Unsubscribe
     */
    function unsubscribe( $status=false ) 
    {
        return $this->unsubscribeGroups( false, $status );
    }
    
    /**
     * Subscribe newsletter. Create new or edit existing.
     *
     * @param string $email
     * @param array $receiverGroupIDs
     * @param array $receiverGroupFormats
     * @param array $receiverFields
     * @param array $statusArray
     * @return mixed false or receiver object
     */
    static function subscribe( $email, $receiverGroupIDs, $receiverGroupFormats, $receiverFields=false, $statusArray=false ) 
    {
        if ( !$receiver = self::fetchByEmail( $email ) ) 
        {
            $receiver = self::create( $email );
            $receiver->publish();
        }
        
        if ( $receiver ) 
        {
            $receiver->updateReceiverGroups( $receiverGroupIDs, $receiverGroupFormats, false, $statusArray );
            
            if ( $receiverFields )
            {
                $receiver->setReceiverFields( $receiverFields );
            }
            
            return $receiver;
        }
        
        return false;
    }
}
?>