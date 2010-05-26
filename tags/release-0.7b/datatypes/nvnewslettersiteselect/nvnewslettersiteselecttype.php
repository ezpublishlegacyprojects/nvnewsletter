<?php
/**
 * File containing the nvNewsletterSiteSelectType class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletterSiteSelectType is datatype for site selection
 */
class nvNewsletterSiteSelectType extends eZDataType 
{
    const DATA_TYPE_STRING = 'nvnewslettersiteselect';

    function __construct()
    {
        $this->eZDataType( self::DATA_TYPE_STRING, "nvNewsletter: Site selection" );
    }

    function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $classAttribute->setAttribute( 'data_text1', $http->postVariable( 'ContentClass_nvnewslettersiteselect_table_'. $classAttribute->attribute( 'id' ) ) );
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
        $ini    = eZINI::instance( 'nvnewsletter.ini' );
        $sites  = $ini->variable( 'SiteSettings', 'SiteURL' );
		  
		  foreach ( $sites as $key => $value )
        {
				$options[$value] = $key;
		  }
		  
		  return array( 'options' => $options, 
                      'selected' => $contentObjectAttribute->attribute( 'data_text' ) );
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

eZDataType::register( nvNewsletterSiteSelectType::DATA_TYPE_STRING, "nvnewslettersiteselecttype" );
?>
