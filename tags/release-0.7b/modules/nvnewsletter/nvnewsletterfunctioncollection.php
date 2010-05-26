<?php
/**
 * File containing the nvNewsletterFunctionCollection class
 * @package nvNewsletter
 */
class nvNewsletterFunctionCollection 
{
    function __construct() {}

    function fetchSenderCount() 
    {
        $customOperation = array( array( 'operation' => 'count(*)',
                                         'name' => 'count' ) );
        $rows = eZPersistentObject::fetchObjectList( nvNewsletterSender::definition(),
                                                     array(), 
                                                     array( 'status' => nvNewsletterSender::STATUS_PUBLISHED ), 
                                                     null, 
                                                     null, 
                                                     false, 
                                                     false, 
                                                     $customOperation );

        return array('result' => $rows[0]['count']);
    }

    function fetchReceiverGroupCount() 
    {
        $customOperation = array( array( 'operation' => 'count(*)',
                                         'name' => 'count' ) );
        $rows = eZPersistentObject::fetchObjectList( nvNewsletterReceiverGroup::definition(),
                                                     array(), 
                                                     array( 'status' => nvNewsletterReceiverGroup::STATUS_PUBLISHED ), 
                                                     null, 
                                                     null, 
                                                     false, 
                                                     false, 
                                                     $customOperation );

        return array('result' => $rows[0]['count']);
    }
    
    function fetchUnsubscribedCount() 
    {
        return array( 'result' => nvNewsletterReceiver::getUnsubscribedCount() );
    }

    function fetchReceiverCount() 
    {
        $customOperation = array( array( 'operation' => 'count(*)',
                                         'name' => 'count' ) );
        $rows = eZPersistentObject::fetchObjectList( nvNewsletterReceiver::definition(),
                                                     array(), 
                                                     array( 'status' => nvNewsletterReceiver::STATUS_PUBLISHED ), 
                                                     null, 
                                                     null, 
                                                     false, 
                                                     false, 
                                                     $customOperation );
        return array('result' => $rows[0]['count']);
    }

    function fetchReceiverFieldCount() 
    {
        $customOperation = array( array( 'operation' => 'count(*)',
                                         'name' => 'count'));
        $rows = eZPersistentObject::fetchObjectList( nvNewsletterReceiverField::definition(),
                                                     array(), 
                                                     array( 'status' => nvNewsletterReceiverField::STATUS_PUBLISHED ), 
                                                     null, 
                                                     null, 
                                                     false, 
                                                     false, 
                                                     $customOperation );

        return array('result' => $rows[0]['count']);
    }

    function fetchSentNewsletterCount() 
    {
        $customOperation = array( array( 'operation' => 'count(*)',
                                         'name' => 'count' ) );
        $rows = eZPersistentObject::fetchObjectList( nvNewsletter::definition(),
                                                     array(), 
                                                     array( 'status' => nvNewsletter::STATUS_SENT ), 
                                                     null, 
                                                     null, 
                                                     false, 
                                                     false,
                                                     $customOperation );

        return array('result' => $rows[0]['count']);
    }

    function fetchInProgressNewsletterCount() 
    {
        $customOperation = array( array( 'operation' => 'count(*)',
                                         'name' => 'count'));
        $rows = eZPersistentObject::fetchObjectList( nvNewsletter::definition(), 
                                                     array(), 
                                                     null, 
                                                     null, 
                                                     null, 
                                                     false, 
                                                     false, 
                                                     $customOperation, 
                                                     false, 
                                                     ' WHERE status = '.nvNewsletter::STATUS_IN_PROGRESS.' OR status = '.nvNewsletter::STATUS_SENDING );

        return array('result' => $rows[0]['count']);
    }

    function fetchDraftsNewsletterCount() 
    {
        $customOperation = array( array( 'operation' => 'count(*)',
                                         'name' => 'count' ) );
        $rows = eZPersistentObject::fetchObjectList( nvNewsletter::definition(), 
                                                     array(), 
                                                     array( 'status' => nvNewsletter::STATUS_DRAFT ), 
                                                     null, 
                                                     null, 
                                                     false, 
                                                     false,
                                                     $customOperation );

        return array('result' => $rows[0]['count']);
    }

    function fetchFailedNewsletterCount() 
    {
        $customOperation = array( array( 'operation' => 'count(*)',
                                         'name' => 'count' ) );
        $rows = eZPersistentObject::fetchObjectList( nvNewsletter::definition(),
                                                     array(), 
                                                     array( 'status' => nvNewsletter::STATUS_FAILED ), 
                                                     null, 
                                                     null, 
                                                     false, 
                                                     false,
                                                     $customOperation );

        return array( 'result' => $rows[0]['count'] );
    }
    
    function fetchGroups( $offset, $limit ) 
    {
        $ret = nvNewsletterReceiverGroup::fetchByOffset( nvNewsletterReceiverGroup::STATUS_PUBLISHED, 
                                                          array( 'offset' => $offset, 
                                                                 'length' => $limit ), 
                                                          array( 'group_name' => 'asc' ) );
        return array( 'result' => $ret );
    }
    
    function getGroupMembers( $groupID, $offset, $limit ) 
    {
        $ret = nvNewsletterReceiverGroup::members( $groupID, array( 'offset'=>$offset, 
                                                                    'length'=>$limit ) );
        return array( 'result' => $ret );
    }

    function getGroupMembersUnsubscribed( $groupID, $offset, $limit )
    {
        $ret = nvNewsletterReceiverGroup::membersUnsubscribed( $groupID, array( 'offset'=>$offset, 
                                                                                'length'=>$limit ) );
        return array( 'result' => $ret );
    }
}
?>
