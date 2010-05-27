<?php
/**
 * File containing the nvNewsletterSenderType class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7.1b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletterSenderType is datatype for sender details handling
 */
class nvNewsletterSenderType extends eZDataType
{
    const DATA_TYPE_STRING = 'nvnewslettersender';

    function __construct()
    {
        $this->eZDataType( self::DATA_TYPE_STRING, "nvNewsletter: Sender information" );
    }

    function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $classAttribute->setAttribute( 'data_text1', $http->postVariable( 'ContentClass_nvnewslettersender_table_'. $classAttribute->attribute( 'id' ) ) );
        $classAttribute->sync();	
        return true;
    }

    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return  eZInputValidator::STATE_ACCEPTED;
    }

    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $contentObjectAttribute->setAttribute( 'data_text', $http->postVariable( 'Attribute_' . $contentObjectAttribute->attribute( 'id' ) ) );
        $contentObjectAttribute->sync();
        return true;
    }

    function objectAttributeContent( $contentObjectAttribute )
    {
		  $senders = nvNewsletterSender::fetchList();
          
        foreach ($senders as $sender)
        {
            $endparam = '-';
            
            if ( !empty( $sender->reply_to ) )
            { 
                $endparam = trim( $sender->reply_to ); 
            }
            
            $options[$sender->id] = trim( $sender->sender_name ).' &lt;'.trim( $sender->sender_email ).'&gt;, Reply-To: '.$endparam;
        }
		  
		  return array( 'options' => $options, 'selected' => $contentObjectAttribute->attribute( 'data_text' ) );
    }

    function metaData( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    function title( $contentObjectAttribute, $name = null )
    {
        return "";
    }

    function isIndexable()
    {
        return false;
    }
    
    public function hasObjectAttributeContent($attribute) 
    {
        return $attribute->attribute( 'data_text' ) != '';
    }
}

eZDataType::register( nvNewsletterSenderType::DATA_TYPE_STRING, "nvnewslettersendertype" );
?>
