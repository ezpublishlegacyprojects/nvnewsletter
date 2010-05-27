<?php
/**
 * File containing the nvNewsletter class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7.1b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletter handles newsletters
 *
 * @todo Proper language support. Now it's recommended to create new template for every language.
 */
class nvNewsletter extends eZPersistentObject 
{
    const STATUS_DRAFT           = 0;
    const STATUS_IN_PROGRESS     = 1;
    const STATUS_SENDING         = 2;
    const STATUS_SENT            = 3;
    const STATUS_FAILED          = 4;
    
    const NEWSLETTER_FORMAT_TEXT = 0;
    const NEWSLETTER_FORMAT_HTML = 1;

    function __construct($row) 
    {
        parent::__construct($row);
    }

    static function definition() 
    {
        return array(
                'fields' => array(
                    'id' => array(
                        'name' => 'id',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => true),
                    'contentobject_id' => array(
                        'name' => 'contentobject_id',
                        'datatype' => 'integer',
                        'required' => true),
                    'contentobject_version' => array(
                        'name' => 'contentobject_version',
                        'datatype' => 'integer',
                        'required' => true),
                    'status' => array(
                        'name' => 'status',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => true),
                    'send_time' => array(
                        'name' => 'send_time',
                        'datatype' => 'string',
                        'default' => '',
                        'required' => false),
                    'send_start_time' => array(
                        'name' => 'send_start_time',
                        'datatype' => 'string',
                        'default' => '',
                        'required' => false),
                    'send_last_access_time' => array(
                        'name' => 'send_last_access_time',
                        'datatype' => 'string',
                        'default' => '',
                        'required' => false),
                    'send_end_time' => array(
                        'name' => 'send_end_time',
                        'datatype' => 'string',
                        'default' => '',
                        'required' => false),
                    'total_mail_count' => array(
                        'name' => 'total_mail_count',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => false),
                    'sent_mail_count' => array(
                        'name' => 'sent_mail_count',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => false),
                    'info' => array(
                        'name' => 'info',
                        'datatype' => 'string',
                        'default' => '',
                        'required' => false),
                    'locale' => array(
                        'name' => 'locale',
                        'datatype' => 'string',
                        'default' => '',
                        'required' => false)),
                'keys'          => array('id'),
                'function_attributes' => array(
                        'contentobject' => 'contentObject',
                        'opened_count' => 'openedCount',
                        'opened_count_total' => 'openedCountTotal',
                        'unsubscribe_count' => 'unsubscribeCount',
                        'link_click_count' => 'linkClickCount',
                        'links_clicked_by_date' => 'linksClickedGroupByDate',
                        'links_clicked' => 'linksClickedGroupByLink'),
                'increment_key' => 'id',
                'sort'          => array('id' => 'asc'),
                'class_name'    => 'nvNewsletter',
                'name'          => 'nvnewsletter_newsletters');
    }

    static function fetchByOffset( $conds=array( 'status'=>nvNewsletter::STATUS_DRAFT ), $limit=null, $sorts=array( 'id' => 'desc' ), $asObject=true ) 
    {
        return eZPersistentObject::fetchObjectList( nvNewsletter::definition(), 
                                                   null, 
                                                   $conds,
                                                   $sorts, 
                                                   $limit, 
                                                   $asObject );
    }

    static function fetchByContentObjectID( $contentObjectID, $asObject=true ) 
    {
        return eZPersistentObject::fetchObject( nvNewsletter::definition(), 
                                                null,
                                                array( 'contentobject_id' => $contentObjectID ), 
                                                $asObject );
    }

    /**
     * @deprecated
     */
    static function fetchInProgress( $asObject=true ) 
    {
        return eZPersistentObject::fetchObjectList( nvNewsletter::definition(), 
                                                   null, 
                                                   null,
                                                   array('send_time' => 'asc'), 
                                                   null, 
                                                   $asObject, 
                                                   false, 
                                                   null, 
                                                   null,
                                                   ' WHERE status = '.nvNewsletter::STATUS_IN_PROGRESS.' OR 
                                                           status = '.nvNewsletter::STATUS_SENDING );
    }

    /**
     * @deprecated
     */
    static function fetchFailed( $asObject=true ) 
    {
        return eZPersistentObject::fetchObjectList( nvNewsletter::definition(), 
                                                   null, 
                                                   null,
                                                   array('send_time' => 'asc'), 
                                                   null, 
                                                   $asObject, 
                                                   false, 
                                                   null, 
                                                   null,
                                                   ' WHERE status = '.nvNewsletter::STATUS_FAILED );
    }

    static function fetchList( $status=nvNewsletter::STATUS_DRAFT, $asObject=true, $conditions=false ) 
    {
        return eZPersistentObject::fetchObjectList( nvNewsletter::definition(), 
                                                    null, 
                                                    array( 'status' => $status ), 
                                                    null, 
                                                    null,
                                                    $asObject );
    }

    static function fetch( $newsletterID, $asObject=true ) 
    {
        return eZPersistentObject::fetchObject( nvNewsletter::definition(), 
                                                null,
                                                array( 'id' => $newsletterID ), 
                                                $asObject );
    }

    static function removeAll( $id ) 
    {
        eZPersistentObject::removeObject( nvNewsletter::definition(),
                                          array('id' => $id ) );
        $db = eZDB::instance();
        $db->query("DELETE FROM nvnewsletter_clicktrack WHERE newsletter_id = $id");
        $db->query("DELETE FROM nvnewsletter_statistics WHERE newsletter_id = $id");
    }
    
    /**
     * Removes HTML-files, newsletter object and node.
     *
     * @param int $id
     * @return boolean
     */
    static function removeNewsletter( $id ) 
    {
        $newsletter = nvNewsletter::fetch( $id );
        
        if ( $newsletter ) 
        {
            // Get object ID
            $objectID = $newsletter->contentobject_id;
            $object = eZContentObject::fetch( $objectID );
            
            // Get node
            $nodeID = $object->attribute("main_node_id");
            $node   = eZContentObjectTreeNode::fetch( $nodeID );
            
            // Remove node if possible
            if ( $node->canRemove() ) 
            {
                eZContentObjectTreeNode::removeSubtrees( array( $nodeID ), false );
                eZLog::write( "removeNewsletter (nvnewsletter.php): newsletter node removed with nodeID $nodeID", "nvnewsletter.log" );
                
                // Remove newsletter
                self::removeFiles( $objectID );
                self::removeAll( $id );
                
                eZLog::write( "removeNewsletter (nvnewsletter.php): newsletter object removed with newsletterID $id", "nvnewsletter.log" );
                
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Remove newsletter files
     *
     * @param int $objectID
     * @return boolean
     */
    static function removeFiles( $objectID ) 
    {
        if ( is_numeric( $objectID ) ) 
        {
            if ( $dir = nvNewsletterTools::getDir( $objectID ) ) 
            {
                if ( eZFileHandler::doExists( $dir ) ) 
                {
                    eZDir::recursiveDelete( $dir );
                    eZLog::write( "removeFiles (nvnewsletter.php): newsletter dir removed with path $dir", "nvnewsletter.log" );
                    return true;
                }
            }
        }

        return false;
    }

    static function create() 
    {
        $newsletter = new nvNewsletter( array( 'status' => nvNewsletter::STATUS_DRAFT ) );
        $newsletter->store();
        return $newsletter;
    }

    function contentObject() 
    {
        return eZContentObject::fetch( $this->attribute( 'contentobject_id' ) );
    }
    
    function openedCount() 
    {
        return eZPersistentObject::count( nvNewsletterStatistics::definition(), 
                                          array( 'newsletter_id' => $this->attribute('id'),
                                                 'action' => nvNewsletterStatistics::NEWSLETTER_READ ), 
                                          null);
    }
    
    function openedCountTotal()
    {
        $stat = eZPersistentObject::fetchObjectList( nvNewsletterStatistics::definition(),
                                                     array(), 
                                                     array( 'newsletter_id' => $this->attribute('id'),
                                                            'action' => nvNewsletterStatistics::NEWSLETTER_READ ), 
                                                     null,
                                                     null,
                                                     false,
                                                     false,
                                                     array( array( 'operation' => 'SUM( data_int )', 
                                                                   'name' => 'count' ) ) );
        return $stat[0]['count'];
    }
    
    function linkClickCount() 
    {
        $stat = nvNewsletterStatistics::fetchByNewsletterAction( $this->attribute('id'), 
                                                                 nvNewsletterStatistics::NEWSLETTER_LINK_CLICK );
        if ( $stat ) 
        {
            return $stat[0]->attribute('data_int');
        }
        
        return 0;
    }
    
    function linksClickedGroupByLink()
    {
        $ret = eZPersistentObject::fetchObjectList( nvNewsletterClickTrack::definition(), 
                                                array(),
                                                array( 'newsletter_id' => $this->attribute('id') ), 
                                                true,
                                                null,
                                                false,
                                                array( 'nvnewsletter_clicktrack.link_id' ),
                                                array( 'link', array( 'operation' => 'SUM( data_int )', 
                                                              'name' => 'count' ) ),
                                                array( 'nvnewsletter_clicktrack_link' ),
                                                ' AND nvnewsletter_clicktrack_link.id = nvnewsletter_clicktrack.link_id');
        return $ret;
    }

    function linksClickedGroupByDate()
    {
        $ret = false;
        $results = eZPersistentObject::fetchObjectList( nvNewsletterClickTrack::definition(), 
                                                        null,
                                                        array( 'newsletter_id' => $this->attribute('id') ), 
                                                        true,
                                                        null,
                                                        false, // couldn't get link field if as object
                                                        false,
                                                        array( 'link' ),
                                                        array( 'nvnewsletter_clicktrack_link' ),
                                                               ' AND nvnewsletter_clicktrack_link.id = nvnewsletter_clicktrack.link_id');
        if ( $results )
        {
            foreach ( $results as $result )
            {
                $ret[$result['action_date']][] = $result;
            }
            
            if ( is_array( $ret ) )
            {
                ksort( $ret );
            }
            
        }
        
        return $ret;
    }

    function unsubscribeCount() 
    {
        $stat = nvNewsletterStatistics::fetchByNewsletterAction( $this->attribute('id'), 
                                                                 nvNewsletterStatistics::NEWSLETTER_UNSUBSCRIBE );
        if ( $stat ) 
        {
            return count( $stat );
        }
        
        return 0;
    }
    
    /**
     * Create newsletter
     *
     * @param int $objectID
     * @param int $objectVersion
     * @param string $language
     * @return boolean
     */
    static function createNewsletter( $objectID, $objectVersion, $language, $createTemp=false ) 
    {
        // Do we need this?
        self::setPrioritizedLanguages( $language );
        
        // Check directory structure
        if ( !self::checkBaseDir( $objectID ) ) 
        {
            eZDebug::writeWarning("nvNewsletter: main dir generation failed");
            return false;
        }
        
        // Get newsletter data
        $data = self::generateNewsletterData( $objectID, $objectVersion, $language, $createTemp );
        
        if ( !$data ) 
        {
            eZLog::write( "createNewsletter (nvnewsletter.php): content generation failed with objectID $objectID and version $objectVersion", "nvnewsletter.log" );
            return false;
        }
    
        // HTML
        $htmlFileName = nvNewsletterTools::getFileName( $objectID, $objectVersion, true, false, 'html', $language );
        $htmlContent  = $data['html'];
        
        // Text
        $textFileName = nvNewsletterTools::getFileName( $objectID, $objectVersion, true, false, 'text', $language );
        $textContent  = nvNewsletterTools::br2nl( $data['text'] );
        
        $oldumask = @umask( 0 );

        $htmlCreated = eZFile::create( $htmlFileName, false, trim( $htmlContent ) );
        $textCreated = eZFile::create( $textFileName, false, trim( $textContent ) );
        
        $ini = eZINI::instance('site.ini');
        
        // Fix file permissions
        $permissions = octdec( $ini->variable( 'FileSettings', 'LogFilePermissions' ) );
        
        @chmod( $htmlFileName, $permissions );
        @chmod( $textFileName, $permissions );
        
        @umask( $oldumask );

        if ( $htmlCreated && $textCreated ) 
        {
            return true;
        }

        eZLog::write( "createNewsletter (nvnewsletter.php): email generation failed with objectID $objectID and version $objectVersion", "nvnewsletter.log" );
        
        return false;
    }
    
    /**
     * Generates newsletter content html and text file
     *
     * @param int $objectID
     * @param int $objectVersion
     * @param string $language
     * @param boolean $createTemp
     * @return array containing html and text data
     */
    static function generateNewsletterData( $objectID, $objectVersion, $language, $createTemp=false ) 
    {
        $versionObject = eZContentObject::fetch( $objectID )->version( $objectVersion );

        if ( !$versionObject )
            return false;
        
        $contentObject = $versionObject->contentObject();
        $nodeID        = $contentObject->attribute('main_node_id');
        
        if ( !$contentObject ) 
            return false;

        if ( $createTemp )
        {
            if ( !$node = self::tempMainNode( $versionObject ) ) 
                return false;
        }
        else
        {
            if ( !$node = $contentObject->mainNode() ) 
                return false;
        }
        
        // Set vars
        $localVars = array( "cacheFileArray", "NodeID",  "Module", "tpl",
                            "LanguageCode",  "ViewMode", "Offset", "ini",
                            "cacheFileArray", "viewParameters", "collectionAttributes",
                            "validation" );

        $tpl            = nvNewsletterTemplate::factory();
        $LanguageCode   = $language;
        $ViewMode       = 'nvnewsletterhtml';
        
        // HTML
        $result = eZNodeviewfunctions::generateNodeViewData( $tpl, $node, $contentObject,
                                                             $LanguageCode, $ViewMode, false, 
                                                             false, false, false);
        $retval = array(
                'content' => $result,
                'scope'   => 'viewcache',
                'store'   => $result['cache_ttl'] != 0);

        $htmlData = $retval['content'];

        // Text
        $ViewMode = 'nvnewslettertext';

        $result = eZNodeviewfunctions::generateNodeViewData( $tpl, $node, $contentObject,
                                                             $LanguageCode, $ViewMode, false, 
                                                             false, false, false);
        $retval = array(
                'content' => $result,
                'scope'   => 'viewcache',
                'store'   => $result['cache_ttl'] != 0);

        $textData = $retval['content'];

        if ( $htmlData['content'] && $textData['content'] ) 
        {
            return array( 'html' => $htmlData['content'],
                          'text' => $textData['content']);
        }
        
        return false;
    }
    
    /**
     * Set newsletter language
     *
     * @param mixed $languages
     * @todo Do we need this?
     */
    static function setPrioritizedLanguages( $languages ) 
    {
        if ( empty( $languages ) ) 
        {
            $ini         = eZINI::instance('site.ini');
            $languages   = $ini->variable('RegionalSettings', 'Locale');
        }

        if ( !is_array( $languages ) ) 
        {
            $languages = array( $languages );
        }
        
        eZContentLanguage::setPrioritizedLanguages( $languages );
    }
    
    /**
     * Checks if newsletter var directory exists
     *
     * @param int $objectID
     * @return boolean
     */
    static function checkBaseDir( $objectID ) 
    {
        $tmpDir = nvNewsletterTools::getDir( $objectID );

        if ( !eZFileHandler::doExists( $tmpDir ) ) 
        {
            if ( !eZDir::mkdir( $tmpDir, eZDir::directoryPermission(), true ) ) 
            {
                eZLog::write( "checkBaseDir (nvnewsletter.php): could not create temporary directory $tmpDir", "nvnewsletter.log" );
                return false;
            }
        }

        if ( !eZFileHandler::doIsWriteable( $tmpDir ) ) 
        {
            eZLog::write( "checkBaseDir (nvnewsletter.php): please make $tmpDir writable", "nvnewsletter.log" );
            return false;
        }
        
        return true;
    }
    
    /**
     * Send preview newsletter
     *
     * @param string $email
     * @param int $format 0=text, 1=html
     * @param int $objectID
     * @param int $objectVersion
     * @param string $languageCode
     * @param boolean $createTemp Tells if we need to create temp node
     */
    static function sendPreview( $email, $format, $objectID, $objectVersion, $languageCode, $createTemp=false ) 
    {
        $receiverID = 0;
        $fields = false;
        
        $receiver = nvNewsletterReceiver::fetchByEmail( $email );
        
        if ( $receiver )
        {
            $receiverID = $receiver->attribute('id');
            $fields     = $receiver->fields();
        }
        
        $newsletterMailer = new nvNewsletterMailer( $objectID, $objectVersion, $languageCode, $createTemp );
        $newsletterMailer->sendMail( $receiverID, $email, $format, $fields );
    }
    
    /**
     * Create temp node from version object and set some missing variables.
     *
     * @param object $versionObject
     * @return object
     */
    static function tempMainNode( $versionObject )
    {
        // Set current version
        $contentObject = $versionObject->contentObject();
        $contentObject->setAttribute( 'current_version', $versionObject->attribute('version') );
        
        $virtualNodeID = $contentObject->attribute( 'main_node_id' );
        $node          = $versionObject->tempMainNode();
        
        // Set name and other missing variables
        $class         = eZContentClass::fetch( $contentObject->attribute( 'contentclass_id' ) );
        $objectName    = $class->contentObjectName( $contentObject );

        $node->setName( $objectName );
        $node->setAttribute( 'node_id', $virtualNodeID );
        $node->setAttribute( 'main_node_id', $virtualNodeID );
        
        return $node;
    }
}
?>
