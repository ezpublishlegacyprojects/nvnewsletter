<?php
/**
 * File containing the nvNewsletterGroupsType class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletterGroupsType is datatype for receiver group selection
 */
class nvNewsletterGroupsType extends eZDataType
{
    const DATA_TYPE_STRING = 'nvnewslettergroups';

    function __construct()
    {
        $this->eZDataType( self::DATA_TYPE_STRING, "nvNewsletter: Group selection" );
    }

    function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $classAttribute->setAttribute( 'data_text1', $http->postVariable( 'ContentClass_nvnewslettergroups_table_'. $classAttribute->attribute( 'id' ) ) );
        $classAttribute->sync();	
        return true;
    }

    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute ) 
    {
        if ( $http->hasPostVariable( 'Attribute_array_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $selectOptions = $http->postVariable( 'Attribute_array_' . $contentObjectAttribute->attribute( 'id' ) );

            if ( !is_array( $selectOptions ) || count( $selectOptions ) == 0 )
            {
                if( $contentObjectAttribute->validateIsRequired() ) 
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes', 'Selection required.' ) );
                    return eZInputValidator::STATE_INVALID;
                } 
                else 
                {
                    return eZInputValidator::STATE_ACCEPTED;
                }
            }
        }
        return  eZInputValidator::STATE_ACCEPTED;
    }

    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute ) 
    {
        if ( $http->hasPostVariable( 'Attribute_array_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $selectOptions = $http->postVariable( 'Attribute_array_' . $contentObjectAttribute->attribute( 'id' ) );
            $idString = ( is_array( $selectOptions ) ? implode( '-', $selectOptions ) : "" );
            $contentObjectAttribute->setAttribute( 'data_text', $idString );
            return true;
        }
        return false;
    }

    function objectAttributeContent( $contentObjectAttribute ) 
    {
        $groups   = nvNewsletterReceiverGroup::fetchList();
        $idString = explode( '-', $contentObjectAttribute->attribute( 'data_text' ) );
        
        foreach ( $groups as $group ) 
        {
            $options[$group->id] = trim($group->group_name);
            
            if( in_array($group->id, $idString ))
            {
                $optionsSelected[$group->id] = 1;
            }
        }

        return array( 'options' => $options, 'selected' => $optionsSelected );
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

eZDataType::register( nvNewsletterGroupsType::DATA_TYPE_STRING, "nvnewslettergroupstype" );
?>