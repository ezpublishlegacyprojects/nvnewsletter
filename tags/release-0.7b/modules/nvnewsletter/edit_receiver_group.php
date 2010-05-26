<?php
/**
 * Module edit receiver group
 * @package nvNewsletter
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

$receiverGroupID = $Params['ReceiverGroupID'];
$receiverGroup = nvNewsletterReceiverGroup::fetchDraft($receiverGroupID);

if (!$receiverGroup) 
{
    $receiverGroup = nvNewsletterReceiverGroup::create();
    $Module->redirectToView('edit_receiver_group', array($receiverGroup->attribute('id')));
}

$warning = array();

if ($http->hasPostVariable('StoreButton')) 
{
    if (strlen($http->postVariable('ReceiverGroupName')) > 0) 
    {
        $receiverGroup->setAttribute('group_name', $http->postVariable('ReceiverGroupName'));
    } 
    else 
    {
        $warning[] = ezi18n('eznewsletter/edit_receiver_group', 'You have not defined a name for this receiver group');
    }

    $receiverGroupDescription = $http->postVariable('ReceiverGroupDescription');
    
    if (strlen($http->postVariable('ReceiverGroupDescription')) > 0) 
    {
        $receiverGroup->setAttribute('group_description', $http->postVariable('ReceiverGroupDescription'));
    } 
    else 
    {
        $receiverGroup->setAttribute('group_description', '');
    }

    $receiverGroup->store();
}

if (0 === count($warning) && $http->hasPostVariable('StoreButton')) 
{
    $receiverGroup->publish();
    return $Module->redirectTo($Module->functionURI('list_receiver_groups'));
}

if ($http->hasPostVariable('CancelButton')) 
{
    $receiverGroup->removeDraft();
    $Module->redirectTo($Module->functionURI('list_receiver_groups'));
}

$tpl = nvNewsletterTemplate::factory();
$tpl->setVariable('warning', $warning);
$tpl->setVariable('group', $receiverGroup);

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:$extension/edit_receiver_group.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'design/nvnewsletter', 'Edit receiver group' ) ) );
?>
