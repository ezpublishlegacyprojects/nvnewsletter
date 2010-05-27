<?php
/**
 * Module subscribe
 * @package nvNewsletter
 */
$Module = $Params['Module'];

$http = eZHTTPTool::instance();
$ini  = eZINI::instance( 'nvnewsletter.ini' );

$node            = eZContentObjectTreeNode::fetch( $http->postVariable('NodeID') );
$allowedGroupIDs = $ini->variable( 'SubscribeSettings', 'GroupsAllowed' );
$returnParameter = $ini->variable( 'SubscribeSettings', 'StatusViewParameter' );
$email           = $http->postVariable('nvNewsletterEmail');
$sentGroupIDs    = $http->postVariable('nvNewsletterGroupID');
$sentGroupTypes  = $http->postVariable('nvNewsletterGroupType');
$groupIDs        = false;
$groupTypes      = false;

$returnURI = nvNewsletterTools::formatURLPath( $node->urlAlias() );

if ( $allowedGroupIDs )
{
    foreach ( $allowedGroupIDs as $groupID ) 
    {
        if ( is_numeric( $groupID ) && in_array( $groupID, $sentGroupIDs ) )
        {
            $groupIDs[] = $groupID;
            
            if ( array_key_exists( $groupID, $sentGroupTypes ) ) 
            {
                $groupTypes[$groupID] = $sentGroupTypes[$groupID];
            }
        }
    }
}

if ( eZMail::validate( $email ) ) 
{
    if ( is_array( $groupIDs ) )
    {
        $user = nvNewsletterReceiver::subscribe( $email, $groupIDs, $groupTypes, false, nvNewsletterReceiver::STATUS_GROUP_APPROVED );

        if ( $user )
        {
            $returnURI .= "/($returnParameter)/success";
        }
        else
        {
            $returnURI .= "/($returnParameter)/failed";
        }
    }
    else
    {
        $returnURI .= "/($returnParameter)/groupfailed";
    }
} 
else 
{
    $returnURI .= "/($returnParameter)/emailfailed";
}

return $Module->redirectTo( $returnURI );

?>