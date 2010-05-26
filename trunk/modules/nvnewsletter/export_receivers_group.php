<?php
/**
 * Module export receivers
 * @package nvNewsletter
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$Module = $Params['Module'];
$http = eZHTTPTool::instance();
$db   = eZDB::instance();

$receiverGroupID     = $Params['ReceiverGroupID'];
$receiverGroupMode   = $Params['ReceiverGroupMode']; // Subscribed or unsubscribed
$receiverGroup       = nvNewsletterReceiverGroup::fetch( $receiverGroupID );
$data                = array();

if ( !$receiverGroup ) 
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

if ( isset( $receiverGroupMode ) && $receiverGroupMode == 0 ) 
{
    $method     = 'membersUnsubscribed';
    $fileappend = 'unsubscribed';
} 
else 
{
    $method     = 'members';
    $fileappend = 'subscribed';
}

$receivers      = call_user_func( 'nvNewsletterReceiverGroup::' . $method, $receiverGroupID, false, false );
$receiverFields = nvNewsletterReceiverField::fetchList( nvNewsletterReceiverField::STATUS_PUBLISHED, false );
$receiversCount = count( $receivers );
$receiverIDs    = false;
$receiversTemp  = false;
$receiverFieldTemp   = false;
$receiverFieldsLimit = 203;

if ( $receiversCount < $receiverFieldsLimit ) 
{
    $receiverFieldsLimit = $receiversCount;
}

$i = 1;
$r = 1;

if ( $receivers ) 
{
    foreach ( $receivers as $receiver ) 
    {
        $receiversTemp[$receiver['id']] = $receiver;
    }
    
    $receivers = $receiversTemp;
    unset( $receiversTemp );

    // Now we need to fetch user fields
    if ( $receiverFields ) 
    {
        foreach ( $receivers as $receiverID => $receiver ) 
        {
            if ( $i <= $receiverFieldsLimit ) 
            {
                $receiverIDs[] = $receiverID;
            }
            
            if ( $i == $receiverFieldsLimit || $r == $receiversCount ) 
            {
                if ( $receiverIDs ) 
                {
                    $receiverFieldValues = $db->arrayQuery("SELECT 
                                                                rhf.receiver_id, 
                                                                rhf.receiverfield_id, 
                                                                rhf.data
                                                            FROM 
                                                                nvnewsletter_receivers_has_fields rhf
                                                             WHERE 
                                                                rhf.receiver_id IN ( ".implode( ', ', $receiverIDs )." )");
                            
                    if ( $receiverFieldValues ) 
                    {
                        foreach ( $receiverFieldValues as $value ) 
                        {
                            $receiverFieldTemp[$value['receiver_id']][$value['receiverfield_id']] = $value['data'];
                        }
                    }
                }
                
                $i = 1;
                ++$r;
                $receiverIDs = array();
                continue;
            }
            
            ++$i;
            ++$r;
        }
    }
    
    // Build labels array
    $csvLabel = array( 'ID', 'Email', 'Format' );
    
    foreach ( $receiverFields as $fieldKey => $field ) 
    {
        $csvLabel[] = $field['field_name'];
    }
    
    // Build actual csv data
    $csvData  = array();
    
    foreach ( $receivers as $receiverID => $receiver ) 
    {
        $csvData[$receiverID][] = $receiver['id'];
        $csvData[$receiverID][] = $receiver['email_address'];
        $csvData[$receiverID][] = $receiver['mail_type'] ? 'html' : 'text';
        
        if ( $receiverFields ) 
        {
            foreach ( $receiverFields as $fieldKey => $field ) 
            {
                $data = "";
                
                if ( $receiverFieldTemp && array_key_exists( $receiverID, $receiverFieldTemp ) ) 
                {
                    if ( array_key_exists( $field['id'], $receiverFieldTemp[$receiverID] ) )
                    {
                        $data = $receiverFieldTemp[$receiverID][$field['id']];
                    }
                }
                
                $csvData[$receiverID][] = $data;
            }
            
            unset( $receiverFieldTemp[$receiverID], $receivers[$receiverID] );
        }
    }
    
    unset( $receiverFieldTemp, $receivers ); 
    
    // Write
    $filehash = $receiverGroupID. '_' . md5( $method . $receiverGroupID ); // Keep same filenames so we don't have to clean up CSVs
    $filename = 'group' . $receiverGroupID . '_' . $fileappend . '_' . date('YmdHis'); // Just for display
    
    $csv = new nvNewsletterCSV( $csvData, $csvLabel, $filehash, $filename );
    $csv->download();
    
    $Result = array();
    $Result['pagelayout'] = false;

    eZExecution::cleanExit();
}

?>