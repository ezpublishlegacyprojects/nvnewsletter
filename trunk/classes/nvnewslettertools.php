<?php
/**
 * File containing the nvNewsletterTools class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7.1b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletterTools contains miscellaneous helper methods
 */
class nvNewsletterTools 
{
    function __construct() {}
    
    /**
     * Get hash from settings file
     *
     * @param string $type
     * @return string|array
     */
    static function getHash ( $type='User' ) 
    {
        $ini = eZINI::instance( 'nvnewsletter.ini' );
        return $ini->variable( 'HashSettings', ucfirst($type).'Hash' );
    }
    
    /**
     * Breaks to new lines
     *
     * @param string $string
     * @return string
     */
    static function br2nl ( $string, $charlist = "\t\n\r\0\x0B" ) 
    {
        $string = str_replace( str_split($charlist), "", $string );
        $string = ereg_replace( " +", " ", $string );
        $string = str_replace( "\n", "", $string );
        return preg_replace( '/\<br(\s*)?\/?\>/i', "\n", $string );
    }
    
    /**
     * Get newsletter directory with objectID
     *
     * @param int $objectID
     * @return string
     */
    static function getDir( $objectID=false ) 
    {
        if ( $objectID ) 
        {
            $fileSep      = eZSys::fileSeparator();
            $tmpDir       = eZSys::varDirectory().$fileSep.'nvnewsletter';
            $dirStructure = self::getNewsletterFileDir( $objectID );
            $tmpDir       = $tmpDir.$fileSep.$dirStructure;
            return $tmpDir;
         }
         
         return false;
    }
    
    /**
     * Get directory structure for files
     *
     * @param int $objectID
     * @return string
     */
    static function getNewsletterFileDir( $objectID ) 
    {
        if ( $objectID ) 
        {
            return $objectID;
        }
        
        return '0';
    }
    
    /**
     * Append missing / to URL
     *
     * @param string $url
     * @return string
     */
    static function formatURLPath( $url )
    {
        if ( substr( $url, -1  ) == '/' ) 
        {
             $url = substr( $url, 0, -1  );
        }
        
        return $url;
    }
    
    /**
     * Get newsletter site link
     *
     * @param int $objectID
     * @param int $objectVersion
     * @return string
     */
    static function getLink ( $objectID, $objectVersion=false ) 
    {
        $siteLink = '';
        
        $object  = eZContentObject::fetch( $objectID );
        
        if ( is_numeric( $objectVersion ) ) 
        {
            $object = $object->version( $objectVersion );
        }
        
        $dataMap  = $object->DataMap();
        
        if ( $dataMap['site_selection'] ) 
        {
            $content  = $dataMap['site_selection']->content();
            $siteLink = $content['selected'];
            $siteLink = explode( ';', $siteLink );
            $return = array();
            
            for ( $i=0; $i<=1; $i++ ) 
            {
                if ( !empty( $siteLink[$i] ) ) 
                {
                    $link = self::formatURLPath( $siteLink[$i] );

                    if ( substr( $link, 0, 7  ) != 'http://' && 
                         substr( $link, 0, 8  ) != 'https://' ) 
                    {
                         $link = 'http://'.$link;
                    }
                    
                    $return[] = $link;
                }
            }
        }
        
        if ( empty( $return[1] ) ) 
        {
            $return[1] = $return[0];
        }

        return $return;
    }
    
    /**
     * Build hash
     *
     * @param int $objectID
     * @param int $objectVersion
     * @param string $language
     * @return string
     */
    static function getFileHash ( $objectID, $objectVersion, $language ) 
    {
        return md5( $objectID . $objectVersion . $language . self::getHash('File') );
    }
    
    /**
     * Build user hash
     *
     * @param string $email
     * @return string
     */
    static function getUserHash ( $email ) 
    {
        return md5( $email . self::getHash( 'User' ) );
    }
    
    /**
     * Replace user code with dummy values
     *
     * @param string $content
     * @return string
     */
    static function replaceUserCode ( $content ) 
    {
        return str_replace( array( '<'.nvNewsletterMailer::USERCODE_TAG.'>', '<'.nvNewsletterMailer::TRACKERCODE_TAG.'>' ), 
                            array( nvNewsletterMailer::USERCODE_REPLACEMENT, nvNewsletterMailer::TRACKERCODE_REPLACEMENT ), 
                            $content );
    }
    
    /**
     * Put receiverFields object values to array
     *
     * @return array
     */
    static function formatReceiverFields( $fields )
    {
        $receiverFields = false;
    
        if ( $fields )
        {
            foreach ( $fields as $index => $value )
            {
                $fieldIndex = strtolower( $value['field_name'] );
                $receiverFields[$fieldIndex] = $value['value'];
            }
        }
        
        return $receiverFields;
    }
    
    /**
     * Gets personalized tag array
     *
     * @param int $objectID 
     * @param int $receiverID
     * @param string $email
     * @param array $personalTags
     */
    static function personalizedTags( $objectID, $receiverID, $email, $fields )
    {
        return nvNewsletterMailer::personalizedTags( $objectID, $receiverID, $email, self::tagsAvailableForPersonalization(), $fields );
    }
    
    /**
     * Return personalization tags from nvnewsletter.ini
     */
    static function tagsAvailableForPersonalization()
    {
        $ini = eZINI::instance( 'nvnewsletter.ini' );
        return $ini->variable( 'Personalization', 'ReplaceTag' );
    }  
    
    /**
     * Get filename for the generated newsletter file
     *
     * @param int $objectID
     * @param int $objectVersion
     * @param boolean $withPath
     * @param boolean $hash
     * @param string $mode
     * @param string $language
     * @return string
     */
    static function getFileName ( $objectID, $objectVersion, $withPath=false, $hash=false, $mode='html', $language='eng-GB' ) 
    {
        if ( $mode !== 'html' ) 
        {
            $mode = 'text';
        }
        
        $ini = eZINI::instance( 'nvnewsletter.ini' );
        
        if ( $hash ) 
        {
            $generatedHash = $hash;
        } 
        else 
        {
            $generatedHash = self::getFileHash( $objectID, $objectVersion, $language );
        }
        
        $filename = $ini->variable('FileSettings', 'FilePrefix').$objectVersion.'_'.$mode.'_'.$generatedHash.'.html';
        
        if ( $withPath ) 
        {
            return self::getDir( $objectID ).eZSys::fileSeparator().$filename;
        } 
        else 
        {
            return $filename;
        }
    }
    
    /**
     * Get newsletter content
     *
     * @param int $objectID
     * @param int $objectVersion
     * @param string $mode
     * @param string $hash
     * @param string $language
     * @return mixed
     */
    static function getContent( $objectID, $objectVersion, $mode='html', $hash=false, $language=false ) 
    {
        if ( !is_numeric( $objectID ) || !is_numeric( $objectVersion ) ) 
        {
            return false;
        }
    
        if ( $mode !== 'html' ) 
        {
            $mode = 'text';
        }
        
        if ( $hash ) 
        {
            $hash = self::sanitizeHTMLFileName( $hash );
        }
        
        $filePath = self::getFileName( $objectID, $objectVersion, true, $hash, $mode, $language );

        if ( eZFileHandler::doExists( $filePath ) ) 
        {
            return file_get_contents( $filePath );
        }
        
        return false;
    }
    
    /**
     * Sanitizes hash name
     *
     * @param string $hash
     * @return string
     */
    static function sanitizeHTMLFileName($hash) 
    {
        return preg_replace( '/[^A-Za-z0-9]+/', '', $hash );
    }
    
    /**
     * Get current datetime
     *
     * @return string
     */
    static function currentDatetime()
    {
        return date('Y-m-d H:i:s');
    }
    
    /**
     * Checks if two hashes match
     *
     * @param string $hash1
     * @param string $hash2
     * @return boolean
     */
    static function hashMatch( $hash1, $hash2 )
    {
        if ( $hash1 == $hash2 && !empty( $hash1 ) )
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * Checks if content object is newsletter object.
     * Assumes that every object which has sender data 
     * set is newsletter.
     *
     * @param object $contentObject
     * @return boolean
     */
    static function isNewsletter( $contentObject )
    {
        if ( is_object( $contentObject ) )
        {
            if ( $dataMap = $contentObject->DataMap() )
            {
                $ini = eZINI::instance( 'nvnewsletter.ini' );
                $senderFieldIdentifier = $ini->variable( 'ContentClassSettings', 'SenderFieldIdentifier' );
                
                if ( isset( $dataMap[$senderFieldIdentifier] ) )
                {
                    return true;
                }
            }
        }
        
        return false;
    }
}
?>
