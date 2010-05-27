<?php
/**
 * Module import receivers
 * @package nvNewsletter
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

$receiverGroupID    = $Params['ReceiverGroupID'];
$receiverGroup      = nvNewsletterReceiverGroup::fetch( $receiverGroupID );
$receiverFieldArray = nvNewsletterReceiverField::fetchByOffset();
$imported           = false;
$data               = array();
$failedArray        = array();

$tpl = nvNewsletterTemplate::factory();

if ( !$receiverGroup ) 
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

$delimiter = $http->hasPostVariable( 'CSVDelimiter' ) ? $http->variable( 'CSVDelimiter' ) : ';';

if ( $http->hasPostVariable( 'CancelButton' ) ) 
{
    if ( $http->hasSessionVariable( 'CSVFilename' ) ) 
    {
        $file = $http->sessionVariable( 'CSVFilename' );
        unlink($file);
        $http->removeSessionVariable( 'CSVFilename' );
    }
    
    return $Module->redirectToView( 'view_receiver_group', array( $receiverGroupID ) );
} 
elseif ( $http->hasPostVariable( 'ImportButton' ) ) 
{
    if ( $http->hasSessionVariable( 'CSVFilename' ) ) 
    {
        $file = $http->sessionVariable( 'CSVFilename' );
        $parser = new eZCSVParser( $file, $http->hasPostVariable( 'FirstRowLabel' ) ? true : false, $delimiter );
        $data = $parser->data();

        // Check if output_format is set
        $outputSet = false;
        
        if ( $http->hasPostVariable( 'OutputFormat' ) ) 
        {
            if ( $http->postVariable( 'OutputFormat' ) == 'html' ) 
            {
                $receiverGroupFormats[$receiverGroupID] = nvNewsletter::NEWSLETTER_FORMAT_HTML;
            } 
            else 
            {
                $receiverGroupFormats[$receiverGroupID] = nvNewsletter::NEWSLETTER_FORMAT_TEXT;
            }
            
            $outputSet = true;
        }

        // Check if email mapping is set
        $emailSet = false;
        
        foreach ( array_keys( $data ) as $label ) 
        {
            $mapName = 'LabelMap_' . $label;
            
            if ( $http->hasPostVariable( $mapName ) ) 
            {
               if ( $http->postVariable( $mapName ) === "email" ) 
               {
                   $emailSet = true;
               }
           }
        }

        // Output error and return
        if ( $emailSet == false ) 
        {
           $warning = ezi18n( 'design/nvnewsletter', 'Please select a field mapping for the email address!' );
        } 
        elseif ( $outputSet == false ) 
        {
            $warning = ezi18n( 'design/nvnewsletter', 'Please select a output format!' );
        } 
        else 
        { 
            $labelMap = array();
            
            foreach ( array_keys($data) as $label ) 
            {
                $mapName = 'LabelMap_' . $label;
                
                if ( $http->hasPostVariable( $mapName ) ) 
                {
                    $labelMap[$http->postVariable( $mapName )] = $label;
                }
            }
            
            $importCount = 0;
            $failedCount = 0;
            $existsCount = 0;
            $totalCount  = count( $data[$labelMap['email']] );
            
            if ( $totalCount > 0 ) 
            {
                $db = eZDB::instance();
                $db->begin();

                for ( $i=0; $i < $totalCount; $i++ ) 
                {
                    $receiverSubscribed = false;
                    $receiverEmail = $data[$labelMap['email']][$i];
                
                    if ( eZMail::validate( $receiverEmail ) ) 
                    {
                        // Prevent double emails
                        $existingReceiver = nvNewsletterReceiver::fetchByEmail( $receiverEmail );
                        
                        if ( !$existingReceiver ) 
                        {
                            $receiver = nvNewsletterReceiver::create( $receiverEmail );
                            $receiver->publish();
                        } 
                        else 
                        {
                            $receiver = $existingReceiver;
                            ++$existsCount;
                        }
                        
                        if ( $receiver ) 
                        {
                            $receiver->updateReceiverGroups( array( $receiverGroupID ), $receiverGroupFormats, true );
                        
                            foreach ( $labelMap as $fieldName => $label ) 
                            {
                                if ( $fieldName != 'email' ) 
                                {
                                    $field      = 'field';
                                    $fieldID    = substr( $fieldName, strlen( $field ) );
                                    $receiverFields[$fieldID] = $data[$label][$i];
                                }
                            }
                            
                            $receiver->setReceiverFields( $receiverFields );
                            $receiverSubscribed = true;
                        }
                    }
                    
                    if ( $receiverSubscribed ) 
                    {
                        ++$importCount;
                    } 
                    else 
                    {
                        ++$failedCount;
                        $failedArray[] = $receiverEmail;
                    }
                }
                
                $db->commit();
            }
            
            $imported = true;
            $http->removeSessionVariable( 'CSVFilename' );
            unlink($file);
        }
    }
}

if ( $warning ) 
{
    $tpl->setVariable('warning', $warning);
    $tpl->setVariable('data', $data);
}

if ( eZHTTPFile::canFetch('UploadCSVFile') ) 
{
    $binaryFile = eZHTTPFile::fetch( 'UploadCSVFile' );
    $binaryFile->store( 'nvnewsletter', 'csv' );
    
    $parser = new eZCSVParser( $binaryFile->attribute('filename'), $http->hasPostVariable('FirstRowLabel') ? true : false, $delimiter, 10 );
    $data = $parser->data();
    
    $http->setSessionVariable( 'CSVFilename', $binaryFile->attribute('filename') );

    $tpl->setVariable( 'data', $data );
    $tpl->setVariable( 'line_count', $parser->lineCount() );
    
    $showCount = $parser->lineCount();
    
    if ( $showCount > 10 ) 
    {
        $showCount = 9;
    }
    else
    {
        $showCount = $showCount-1;
    }
    
    $tpl->setVariable( 'show_count', $showCount );
} 
elseif ( $imported ) 
{
    $tpl->setVariable( 'imported', true );
    $tpl->setVariable( 'import_result', array( 'total'        => $totalCount, 
                                               'failed'       => $failedCount, 
                                               'success'      => $importCount,
                                               'exists'       => $existsCount,
                                               'failed_array' => $failedArray ) );
}

if ( $http->hasPostVariable( 'OutputFormat' ) ) 
{
    $tpl->setVariable( 'output_set', $http->postVariable('OutputFormat') );
}

$tpl->setVariable( 'group', $receiverGroup );
$tpl->setVariable( 'CSVDelimiter', $delimiter );
$tpl->setVariable( 'receiver_field_array', $receiverFieldArray );
$tpl->setVariable( 'first_row_label', $http->postVariable('FirstRowLabel' ) );

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu']       = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content']         = $tpl->fetch( "design:$extension/import_receivers.tpl" );
$Result['path']            = array( array( 'url' => false,
                                           'text' => ezi18n( 'design/nvnewsletter', 'Import receivers' ) ) );
?>
