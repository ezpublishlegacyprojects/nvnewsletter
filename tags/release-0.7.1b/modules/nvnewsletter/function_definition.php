<?php
/**
 * Function definition
 * @package nvNewsletter
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/modules/nvnewsletter/";

$FunctionList = array();

$FunctionList['sender_count'] = array(
        'name' => 'sender_count',
        'call_method' => array(
            'include_file' => $baseDir . 'nvnewsletterfunctioncollection.php',
            'class' => 'nvNewsletterFunctionCollection',
            'method' => 'fetchSenderCount'),
        'parameter_type' => 'standard',
        'parameters' => array());

$FunctionList['receiver_group_count'] = array(
        'name' => 'receiver_group_count',
        'call_method' => array(
            'include_file' => $baseDir . 'nvnewsletterfunctioncollection.php',
            'class' => 'nvNewsletterFunctionCollection',
            'method' => 'fetchReceiverGroupCount'),
        'parameter_type' => 'standard',
        'parameters' => array());

$FunctionList['receiver_count'] = array(
        'name' => 'receiver_count',
        'call_method' => array(
            'include_file' => $baseDir . 'nvnewsletterfunctioncollection.php',
            'class' => 'nvNewsletterFunctionCollection',
            'method' => 'fetchReceiverCount'),
        'parameter_type' => 'standard',
        'parameters' => array());

$FunctionList['receiver_field_count'] = array(
        'name' => 'receiver_field_count',
        'call_method' => array(
            'include_file' => $baseDir . 'nvnewsletterfunctioncollection.php',
            'class' => 'nvNewsletterFunctionCollection',
            'method' => 'fetchReceiverFieldCount'),
        'parameter_type' => 'standard',
        'parameters' => array());

$FunctionList['sent_newsletter_count'] = array(
        'name' => 'sent_newsletter_count',
        'call_method' => array(
            'include_file' => $baseDir . 'nvnewsletterfunctioncollection.php',
            'class' => 'nvNewsletterFunctionCollection',
            'method' => 'fetchSentNewsletterCount'),
        'parameter_type' => 'standard',
        'parameters' => array());

$FunctionList['in_progress_newsletter_count'] = array(
        'name' => 'in_progress_newsletter_count',
        'call_method' => array(
            'include_file' => $baseDir . 'nvnewsletterfunctioncollection.php',
            'class' => 'nvNewsletterFunctionCollection',
            'method' => 'fetchInProgressNewsletterCount'),
        'parameter_type' => 'standard',
        'parameters' => array());

$FunctionList['drafts_newsletter_count'] = array(
        'name' => 'drafts_newsletter_count',
        'call_method' => array(
            'include_file' => $baseDir . 'nvnewsletterfunctioncollection.php',
            'class' => 'nvNewsletterFunctionCollection',
            'method' => 'fetchDraftsNewsletterCount'),
        'parameter_type' => 'standard',
        'parameters' => array());

$FunctionList['failed_newsletter_count'] = array(
        'name' => 'failed_newsletter_count',
        'call_method' => array(
            'include_file' => $baseDir . 'nvnewsletterfunctioncollection.php',
            'class' => 'nvNewsletterFunctionCollection',
            'method' => 'fetchFailedNewsletterCount'),
        'parameter_type' => 'standard',
        'parameters' => array());
        
$FunctionList['unsubscribed_count'] = array(
        'name' => 'unsubscribed_count',
        'call_method' => array(
            'include_file' => $baseDir . 'nvnewsletterfunctioncollection.php',
            'class' => 'nvNewsletterFunctionCollection',
            'method' => 'fetchUnsubscribedCount'),
        'parameter_type' => 'standard',
        'parameters' => array());
        
$FunctionList['groups'] = array(
        'name' => 'groups',
        'call_method' => array(
            'include_file' => $baseDir . 'nvnewsletterfunctioncollection.php',
            'class' => 'nvNewsletterFunctionCollection',
            'method' => 'fetchGroups'),
        'parameter_type' => 'standard',
        'parameters' => array(
            array(
                'name'     => 'offset',
                'type'     => 'int',
                'required' => false
                ),
            array(
                'name'     => 'limit',
                'type'     => 'int',
                'required' => false
                )
            )
        );
        
$FunctionList['group_members'] = array( 
        'name' => 'group_members',
        'operation_types' => array('read'), 
        'call_method' => array(
            'include_file' => $baseDir . 'nvnewsletterfunctioncollection.php',
            'class'        => 'nvNewsletterFunctionCollection',
            'method'       => 'getGroupMembers'),
        'parameter_type' => 'standard', 
        'parameters' => array(
            array(
                'name'     => 'group_id',
                'type'     => 'int',
                'required' => true
                ),
            array(
                'name'     => 'offset',
                'type'     => 'int',
                'required' => false
                ),
            array(
                'name'     => 'limit',
                'type'     => 'int',
                'required' => false
                )
            )
        );
        
$FunctionList['group_members_unsubscribed'] = array( 
        'name' => 'group_members',
        'operation_types' => array('read'), 
        'call_method' => array(
            'include_file' => $baseDir . 'nvnewsletterfunctioncollection.php',
            'class'        => 'nvNewsletterFunctionCollection',
            'method'       => 'getGroupMembersUnsubscribed'),
        'parameter_type' => 'standard', 
        'parameters' => array(
            array(
                'name'     => 'group_id',
                'type'     => 'int',
                'required' => true
                ),
            array(
                'name'     => 'offset',
                'type'     => 'int',
                'required' => false
                ),
            array(
                'name'     => 'limit',
                'type'     => 'int',
                'required' => false
                )
            )
        );

?>
