<?php
/**
 * Module search receivers
 * @package nvNewsletter
 */
$extension = 'nvnewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

$searchParams = false;
$searchText = $http->variable('SearchText');
$searchFrom = $http->variable('SearchFrom');
$searchGroup = $http->variable('SearchGroup');

if ( $searchFrom == 'fields' )
{
    $searchParams['searchFrom'] = 'fields';
} 
else 
{
    $searchParams['searchFrom'] = 'email';
}

if ( is_numeric( $searchGroup ) )
{
    $searchParams['searchGroup'] = $searchGroup;
}

$offset = $Params['Offset'];
if( !is_numeric( $offset ) )
{
    $offset = 0;
}

$limit = nvNewsletterAdmin::getAdminListLimit();

$tpl = nvNewsletterTemplate::factory();
$tpl->setVariable( 'module', $Module );
$tpl->setVariable( 'view_parameters', array( 'offset' => $offset, 
                                             'groupID' => $groupID ) );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'receiver_count', 0 );
$tpl->setVariable( 'search_from', $searchParams['searchFrom'] );
$tpl->setVariable( 'search_group', $searchParams['searchGroup'] );

if ( !empty( $searchText ) ) 
{
    $newsletterReceiverArray = nvNewsletterSearch::receiver( $searchText, 
                                                             $searchParams, 
                                                             array( 'offset' => $offset, 
                                                                    'limit' => $limit ), 
                                                             array( 'email_address' => 'asc' ) );
    $tpl->setVariable('receiver_array', $newsletterReceiverArray['SearchResults']);
    $tpl->setVariable('receiver_count', $newsletterReceiverArray['SearchCount']);
}

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/nvnewsletter_menu.tpl';
$Result['content'] = $tpl->fetch("design:$extension/search_receivers.tpl");
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'design/nvnewsletter', 'Receiver search' ) ) );
?>