<?php
/**
 * File containing the nvNewsletterClickTrack class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7b
 * @package nvNewsletter
 */
class nvNewsletterClickTrack extends eZPersistentObject 
{
    function __construct( $row ) 
    {
        parent::__construct( $row );
    }

    static function definition() 
    {
        return array(
                'fields' => array(
                    'newsletter_id' => array(
                        'name' => 'newsletter_id',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => true),
                    'link_id' => array(
                        'name' => 'link_id',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => true),
                    'action_date' => array(
                        'name' => 'action_date',
                        'datatype' => 'string',
                        'default' => '',
                        'required' => true),
                    'data_int' => array(
                        'name' => 'data_int',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => true)),
                'keys'          => array('newsletter_id', 'link_id', 'action_date'),
                'sort'          => array('newsletter_id' => 'asc'),
                'class_name'    => 'nvNewsletterClickTrack',
                'name'          => 'nvnewsletter_clicktrack');
    }

    function attribute( $attr, $noFunction = false ) 
    {
        return eZPersistentObject::attribute( $attr );
    }

    static function fetchByNewsletter( $newsletterID, $asObject=true ) 
    {
        return eZPersistentObject::fetchObject( self::definition(), 
                                                null,
                                                array( 'newsletter_id' => $newsletterID ), 
                                                $asObject);
    }

    static function fetchByDate( $newsletterID, $linkID, $date, $asObject=true ) 
    {
        return eZPersistentObject::fetchObject( self::definition(), 
                                                null,
                                                array( 'newsletter_id' => $newsletterID,
                                                       'link_id' => $linkID,
                                                       'action_date' => $date ), 
                                                $asObject);
    }

    static function create( $newsletterID, $linkID, $data_int=1 ) 
    {
        $click = new nvNewsletterClickTrack( array( 'newsletter_id' => $newsletterID,
                                                    'link_id' => $linkID,
                                                    'action_date' => date('Y-m-d'),
                                                    'data_int' => $data_int ));
        $click->store();

        return $click;
    }
    
    /**
     * Checks if URL and objectID match. Otherwise someone could flood database with URLs
     * that doesn't exists in newsletter.
     *
     * @param string $url
     * @param int $objectID
     * @return boolean
     * @todo Could we use URL tracker outside XML area?
     */
    static function objectAndURLMatch( $url, $objectID )
    {
        $db = eZDB::instance();
        
        $objectID = (int)$objectID;
        
        $checkURLQuery = "
              SELECT 
                ezurl.id  
              FROM 
                ezurl, 
                ezurl_object_link, 
                ezcontentobject, 
                ezcontentobject_attribute 
             WHERE 
                ezurl.url = '" . $db->escapeString( $url ) . "' AND
                ezurl.id = ezurl_object_link.url_id AND
                ezurl_object_link.contentobject_attribute_id = ezcontentobject_attribute.id AND
                ezcontentobject_attribute.contentobject_id = ezcontentobject.id AND
                ezcontentobject.id = $objectID 
             LIMIT 1";
        
        $urlArray = $db->arrayQuery( $checkURLQuery );
        
        if ( count( $urlArray ) == 1 )
        {
            return true;
        }
        
        return false;
    }
}
?>