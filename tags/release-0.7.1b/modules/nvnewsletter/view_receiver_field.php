<?php
/**
 * Module view receiver field
 * @package nvNewsletter
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$http = eZHTTPTool::instance();
$receiverFieldID = $Params['ReceiverFieldID'];
$Module = $Params['Module'];

$field = nvNewsletterReceiverField::fetch($receiverFieldID);

if ( !$field ) 
{
    return $Module->handleError(eZError::KERNEL_NOT_AVAILABLE, 'kernel');
}

$tpl = nvNewsletterTemplate::factory();
$tpl->setVariable('field', $field);

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:$extension/view_receiver_field.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'design/nvnewsletter', 'View receiver field' ) ) );
?>
