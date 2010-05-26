<?php
/**
 * Module edit receiver field
 * @package nvNewsletter
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

$receiverFieldID = $Params['ReceiverFieldID'];
$field = nvNewsletterReceiverField::fetchDraft($receiverFieldID);

if (!$field) {
    $field = nvNewsletterReceiverField::create();
    $Module->redirectToView('edit_receiver_field', array($field->attribute('id')));
}

$warning = array();

if ($http->hasPostVariable('StoreButton')) 
{
    $fieldName = $http->postVariable('ReceiverFieldName');
    
    if (!isset($fieldName) || empty($fieldName)) 
    {
        $warning[] = ezi18n('design/nvnewsletter', 'Field name missing.');
    } 
    else 
    {
        $field->setAttribute('field_name', $fieldName);
    }

    if (strlen($http->postVariable('ReceiverFieldType')) > 0) 
    {
        $field->setAttribute('field_type', $http->postVariable('ReceiverFieldType'));
    } 
    else 
    {
        $field->setAttribute('field_type', '');
    }

    $fieldRequired = $http->postVariable('ReceiverFieldRequired');
    
    if ( isset($fieldName) && $fieldRequired == '1') 
    {
        $field->setAttribute('required', 1);
    } 
    else 
    {
        $field->setAttribute('required', 0);
    }

    $field->store();
}

if ( 0 === count($warning) && $http->hasPostVariable('StoreButton')) 
{
    $field->publish();
    return $Module->redirectTo($Module->functionURI('list_receiver_fields'));
}

if ($http->hasPostVariable('CancelButton')) 
{
    $field->removeDraft();
    $Module->redirectTo($Module->functionURI('list_receiver_fields'));
}

$tpl = nvNewsletterTemplate::factory();
$tpl->setVariable('warning', $warning);
$tpl->setVariable('field', $field);

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:$extension/edit_receiver_field.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'design/nvnewsletter', 'Edit receiver field' ) ) );
?>
