<?php
/**
 * Module definition
 * @package nvNewsletter
 */
$Module = array('name' => 'nvNewsletter');

$ViewList = array();

$ViewList['list_senders'] = array(
        'script' => 'list_senders.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('senders'),
        'unordered_params' => array(
            'offset' => 'Offset'),
        'params' => array());

$ViewList['edit_sender'] = array(
        'script' => 'edit_sender.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('senders'),
        'params' => array('SenderID'));

$ViewList['view_sender'] = array(
        'script' => 'view_sender.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('senders'),
        'params' => array('SenderID'));

$ViewList['list_receiver_groups'] = array(
        'script' => 'list_receiver_groups.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('receiver_groups'),
        'unordered_params' => array(
            'offset' => 'Offset'),
        'params' => array());

$ViewList['edit_receiver_group'] = array(
        'script' => 'edit_receiver_group.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('receiver_groups'),
        'params' => array('ReceiverGroupID'));

$ViewList['view_receiver_group'] = array(
        'script' => 'view_receiver_group.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('receiver_groups'),
        'unordered_params' => array(
            'offset' => 'Offset',
            'offset2' => 'Offset2'),
        'params' => array('ReceiverGroupID'));

$ViewList['import_receivers'] = array(
        'script' => 'import_receivers.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('receiver_groups'),
        'params' => array('ReceiverGroupID'));
        
$ViewList['export_receivers_group'] = array(
        'script' => 'export_receivers_group.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('receiver_groups'),
        'params' => array('ReceiverGroupID', 'ReceiverGroupMode'));

$ViewList['list_receivers'] = array(
        'script' => 'list_receivers.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('receiver'),
        'unordered_params' => array(
            'offset' => 'Offset'),
        'params' => array());
        
$ViewList['search_receivers'] = array(
        'script' => 'search_receivers.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('receiver'),
        'unordered_params' => array(
            'offset' => 'Offset',
            'groupID' => 'GroupID' ),
        'params' => array());

$ViewList['list_receiver_fields'] = array(
        'script' => 'list_receiver_fields.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('receiver_field'),
        'unordered_params' => array(
            'offset' => 'Offset'),
        'params' => array( ));

$ViewList['edit_receiver'] = array(
        'script' => 'edit_receiver.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('receiver'),
        'unordered_params' => array(
            'status' => 'status'),
        'params' => array('ReceiverID'));

$ViewList['edit_receiver_field'] = array(
        'script' => 'edit_receiver_field.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('receiver_field'),
        'params' => array('ReceiverFieldID'));

$ViewList['view_receiver'] = array(
        'script' => 'view_receiver.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('receiver'),
        'params' => array('ReceiverID'));

$ViewList['view_receiver_field'] = array(
        'script' => 'view_receiver_field.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('receiver_field'),
        'params' => array('ReceiverFieldID'));

$ViewList['list_sent'] = array(
        'script' => 'list_sent.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('read'),
        'unordered_params' => array(
            'offset' => 'Offset'),
        'params' => array());

$ViewList['list_in_progress'] = array(
        'script' => 'list_in_progress.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('read'),
        'unordered_params' => array(
            'offset' => 'Offset'),
        'params' => array());

$ViewList['list_draft'] = array(
        'script' => 'list_draft.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('read'),
        'unordered_params' => array(
            'offset' => 'Offset'),
        'params' => array());

$ViewList['list_failed'] = array(
        'script' => 'list_failed.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('read'),
        'unordered_params' => array(
            'offset' => 'Offset'),
        'params' => array());

$ViewList['create_newsletter'] = array(
        'script' => 'create_newsletter.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('read'));
        
$ViewList['copy_newsletter'] = array(
        'script' => 'copy_newsletter.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('read'),
        'params' => array('ObjectID'));

$ViewList['edit_newsletter'] = array(
        'script' => 'edit_newsletter.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('read'),
        'params' => array('NewsletterID'));

$ViewList['view_newsletter'] = array(
        'script' => 'view_newsletter.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('read'),
        'params' => array('NewsletterID'));

$ViewList['queue_draft'] = array(
        'script' => 'queue_draft.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('read'),
        'params' => array('NewsletterID'));

$ViewList['viewmail'] = array(
        'functions' => array('read'),
        'script' => 'viewmail.php',
        'params' => array( 'ObjectID', 'ObjectVersion', 'Hash', 'UserID', 'UserHash' ));

$ViewList['preview'] = array(
        'functions' => array('read'),
        'script' => 'preview.php',
        'params' => array( 'ObjectID', 'ObjectVersion', 'Language', 'Format', 'Preview' ));

$ViewList['send_preview'] = array(
        'functions' => array('read'),
        'script' => 'send_preview.php',
        'params' => array( 'ObjectID', 'ObjectVersion', 'Language', 'Preview' ));
        
$ViewList['subscribe'] = array(
        'script' => 'subscribe.php',
        'functions' => array('read'),
        'params' => array());
        
$ViewList['unsubscribe'] = array(
        'script' => 'unsubscribe.php',
        'functions' => array('read'),
        'params' => array( 'ObjectID', 'UserID', 'Hash' ));
        
$ViewList['dashboard'] = array(
        'script' => 'dashboard.php',
        'default_navigation_part' => 'nvnewsletter',
        'functions' => array('read'),
        'params' => array());

$ViewList['viewlink'] = array(
        'script' => 'viewlink.php',
        'functions' => array('read'),
        'params' => array('ObjectID'));

$FunctionList = array();
$FunctionList['read'] = array();
$FunctionList['receiver_field'] = array();
$FunctionList['receiver_groups'] = array();
$FunctionList['receiver'] = array();
$FunctionList['senders'] = array();
?>
