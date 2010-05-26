<?php
/**
 * File containing the nvNewsletterHandler class
 *
 * @copyright Copyright (c) 2009-2010 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletterHandler automatically maintains the nvnewsletter_newsletters 
 * table when new newsletters are created or old ones updated.
 *
 * Each time an object belonging to the newsletter class group is published, the newsletters table
 * is checked for a newsletter with that content object. If one is found, the version is updated,
 * otherwise a new row is inserted.
 */
class nvNewsletterHandler extends eZContentObjectEditHandler 
{
    function fetchInput( $http, &$module, &$class, $object, &$version, $contentObjectAttributes,
                         $editVersion, $editLanguage, $fromLanguage ) 
    {
    }
 
    static function storeActionList() 
    {
        return array();
    }
 
    function publish( $contentObjectID, $contentObjectVersion ) 
    {
        $object = eZContentObject::fetch( $contentObjectID );
        
        if ( $object ) 
        {
            $ini = eZINI::instance('nvnewsletter.ini');
            $newsletterClassGroupID = $ini->variable( 'ContentClassSettings', 'NewsletterClassGroup' );
            $class = eZContentClass::fetch( $object->ClassID );
            $classGroupIDs = $class->fetchGroupIDList();
            
            if ( in_array( $newsletterClassGroupID, $classGroupIDs ) ) 
            {
                // Okay, we are dealing with a newsletter
                $newsletter = nvNewsletter::fetchByContentObjectID( $contentObjectID );
                
                if ( $newsletter ) 
                {
                    // Update the object version for this newsletter
                    $newsletter->setAttribute( 'contentobject_version', $contentObjectVersion );
                    $newsletter->setAttribute( 'locale', $object->CurrentLanguage );
                    $newsletter->store();
                } 
                else 
                {
                    // Create a new newsletter for this content object and version
                    $newsletter = nvNewsletter::create();
                    $newsletter->setAttribute( 'contentobject_id', $contentObjectID );
                    $newsletter->setAttribute( 'contentobject_version', $contentObjectVersion );
                    $newsletter->setAttribute( 'locale', $object->CurrentLanguage );
                    $newsletter->store();
                }
            }
        }

        return true;
    }
}

?>