<?php
/**
 * File containing the nvNewsletterUserGroupType class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletterUserGroupType
 */
class nvNewsletterUserGroupType extends eZWorkflowEventType 
{
    const WORKFLOW_TYPE_STRING = 'nvnewsletterusergroup';

    public function __construct() 
    {
        $this->ezWorkflowEventType( self::WORKFLOW_TYPE_STRING, 'nvNewsletter user group' );
        $this->setTriggerTypes( array( 'content' => array( 'publish'     => array( 'after' ),
                                                           'addlocation' => array( 'after' ) ) ) );
    }

    public function attributes() 
    {
        return eZWorkflowEventType::attributes();
    }

    public function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        $user    = eZUser::fetch( $parameters['object_id'] );
        $object  = $user->contentObject();
        
        $assignedNodes = $object->assignedNodes();
        
        $ini = eZINI::instance( 'nvnewsletter.ini' );
        $eZUserGroupMapping = $ini->variable( 'SubscribeSettings', 'eZUserGroupMapping' );
        $eZUserGroupFormat  = $ini->variable( 'SubscribeSettings', 'eZUserGroupFormat' );
        $eZUserGroupField   = $ini->variable( 'SubscribeSettings', 'eZUserGroupField' );

        if ( !$eZUserGroupMapping ) 
        {
            return eZWorkflowType::STATUS_ACCEPTED;
        }
        
        $groupIDs = false;
        
        if ( $assignedNodes )
        {
            foreach ( $assignedNodes as $assignedNode )
            {
                $groupNodeID = $assignedNode->fetchParent()->NodeID;
                
                if ( array_key_exists( $groupNodeID, $eZUserGroupMapping ) )
                {
                    if ( nvNewsletterReceiverGroup::fetch( $eZUserGroupMapping[$groupNodeID] ) )
                    {
                        $groupIDs[] = $eZUserGroupMapping[$groupNodeID];
                    }
                }
            }
        }
        
        if ( $groupIDs )
        {
            $groupIDs = array_unique( $groupIDs );
        
            if ( eZMail::validate( $user->Email ) ) 
            {
                $dataMap = $object->DataMap();
            
                // Set formats
                $receiverGroupFormats = array();
                
                foreach ( $groupIDs as $groupID )
                {
                    $format = 1;
                    
                    if ( array_key_exists( $groupID, $eZUserGroupFormat ) )
                    {
                        $format = $eZUserGroupFormat[$groupID];
                    }
                    
                    $receiverGroupFormats[$groupID] = $format;
                }
                
                // Set user data
                if ( $eZUserGroupField ) 
                {
                    $receiverFields = false;
                
                    foreach ( $eZUserGroupField as $key => $field )
                    {
                        if ( $dataMap[$key] )
                        {
                            $receiverFields[$field] = $dataMap[$key]->content();
                        }
                    }
                }
            
                // Prevent double emails
                $existingReceiver = nvNewsletterReceiver::fetchByEmail( $user->Email );
                
                if ( !$existingReceiver ) 
                {
                    $receiver = nvNewsletterReceiver::create( $user->Email );
                    $receiver->publish();
                } 
                else 
                {
                    $receiver = $existingReceiver;
                }
                
                if ( $receiver ) 
                {
                    $receiver->updateReceiverGroups( $groupIDs, $receiverGroupFormats, true );
                    
                    if ( $receiverFields )
                    {
                        $receiver->setReceiverFields( $receiverFields );
                    }
                }
            }
        }
        
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( nvNewsletterUserGroupType::WORKFLOW_TYPE_STRING, "nvNewsletterUserGroupType" );
?>