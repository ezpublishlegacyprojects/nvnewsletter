<?php
/**
 * Module list receiver fields
 * @package nvNewsletter
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

$tpl = nvNewsletterTemplate::factory();
$tpl->setVariable('module', $Module);

if ($http->hasPostVariable('CreateReceiverFieldButton')) 
{
    $Module->redirectTo($Module->functionURI('edit_receiver_field'));
} 
elseif ($http->hasPostVariable('RemoveReceiverFieldButton')) 
{
    $receiverFieldIDArray = $http->postVariable('ReceiverFieldIDArray');
    $http->setSessionVariable('ReceiverFieldIDArray', $receiverFieldIDArray);
    $fields = array();
    
    foreach ( $receiverFieldIDArray as $receiverFieldID ) 
    {
        $field = nvNewsletterReceiverField::fetch($receiverFieldID);
        $fields[] = $field;
    }

    $tpl->setVariable('delete_result', $fields);
    $Result = array();
    $Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
    $Result['content'] = $tpl->fetch("design:$extension/confirmremove_receiver_field.tpl");
    $Result['path'] = array( array( 'url' => false,
                                    'text' => ezi18n( 'design/nvnewsletter', 'Newsletter receiver fields' ) ) );
    return;
} 
elseif ($http->hasPostVariable( 'ConfirmRemoveReceiverFieldButton')) 
{
    $receiverFieldIDArray = $http->sessionVariable('ReceiverFieldIDArray');

    $db = eZDB::instance();
    $db->begin();
    foreach ($receiverFieldIDArray as $receiverFieldID) 
    {
        nvNewsletterReceiverField::removeAll($receiverFieldID);
    }
    $db->commit();
}

$receiverFieldArray = nvNewsletterReceiverField::fetchByOffset();
$tpl->setVariable('receiver_field_array', $receiverFieldArray);

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content'] = $tpl->fetch("design:$extension/list_receiver_fields.tpl");
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'design/nvnewsletter', 'Newsletter receiver fields' ) ) );
?>
