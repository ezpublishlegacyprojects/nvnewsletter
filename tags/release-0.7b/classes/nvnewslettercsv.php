<?php
/**
 * File containing the nvNewsletterCSV class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletterCSV handles CSV writing and donwloading.
 */
class nvNewsletterCSV
{
    const EOF = "\r\n";
    
    var $filehash = false;
    var $filename = false;
    var $csvDir   = false;
    
    /**
     * Create complete CSV file
     *
     * @param array $data
     * @param array $label
     * @param string $filehash
     * @param string $filename
     * @param string $dir
     * @return string
     */
    function __construct( $data, $label, $filehash, $filename, $dir='nvnewsletter_csv' ) 
    {
        $csvData        = '"'.implode( '";"', $label ).'"'.self::EOF;
        
        $this->filehash = self::sanitizeFileName( $filehash );
        $this->filename = self::sanitizeFileName( $filename );
        $this->csvDir   = self::getPath( $dir );

        foreach ( $data as $dataIndex => $row ) 
        {
            $cellCount = count( $row );
            $cellRound = 1;
            
            foreach ( $row as $rowIndex => $cell ) 
            {
                $csvData .= '"'.$this->removeBadCharacters( $this->doubleQuote( $cell ) ).'"';
                
                if ( $cellRound !== $cellCount ) 
                {
                    $csvData .= ";";
                }
                
                $cellRound++;
            }
            
            $csvData .= self::EOF;
        }

        eZFile::create( $this->filehash.".csv", $this->csvDir, trim( $csvData ) );
    }
    
    static function getPath( $dir )
    {
        $dirPath = eZSys::varDirectory().eZSys::fileSeparator().$dir;
        
        if ( !eZFileHandler::doExists( $dirPath ) ) 
        {
            eZDir::mkdir( $dirPath, eZDir::directoryPermission(), true ); 
        }
        
        return $dirPath;
    }
    
    function getCSV() 
    {
        $filePath = $this->csvDir.eZSys::fileSeparator().$this->filehash.'.csv';

        if ( eZFileHandler::doExists( $filePath ) ) 
        {
            return $filePath;
        }
        
        return false;
    }
    
    /**
     * Sanitizes filename
     *
     * @param string $hash
     * @return string
     */
    static function sanitizeFileName( $hash ) 
    {
        return preg_replace( '/[^A-Za-z0-9\_\-]+/', '', $hash );
    }
    
    /**
     * Download dialog
     */
    function download( ) 
    {
        if ( $csv = $this->getCSV() ) 
        {
            header('Pragma: public');
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
            header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: pre-check=0, post-check=0, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');
            header('Content-Transfer-Encoding: none');
            header('Content-Type: text/x-csv');
            header('Content-Disposition: attachment; filename="' . $this->filename . '.csv"');
            
            readfile( $csv );
        }
        
        return false;
    }
    
    /**
     * Remove bad characters
     *
     * @param string $str
     * @param array $removeThese
     * @return string
     */
    function removeBadCharacters( $str, $removeThese=array(";", "\r", "\n") ) 
    {
        $str = str_replace( $removeThese, " ", $str );
        $str = trim( $str );
        return $str;
    }

    /**
     * Double quote quotes 
     *
     * @param string $value
     * @return string
     */
    function doubleQuote( $value ) 
    {
        $value = stripslashes( $value );
        $value = str_replace( array( '"' ), array( '""' ), $value);
        return $value;
    }

    /**
     * @deprecated
     */
    function create( $data, $label, $file, $dir='nvnewsletter_csv' ) 
    {
        $csvData = '"'.implode( '";"', $label ).'"'.self::EOF;
        $csvDir  = self::getPath( $dir );
        $file    = $file.'.csv';

        foreach ( $data as $dataIndex => $row ) 
        {
            $cellCount = count( $row );
            $cellRound = 1;
            
            foreach ( $row as $rowIndex => $cell ) 
            {
                $csvData .= '"'.$this->removeBadCharacters( $this->doubleQuote( $cell ) ).'"';
                
                if ( $cellRound !== $cellCount ) 
                {
                    $csvData .= ";";
                }
                
                $cellRound++;
            }
            
            $csvData .= self::EOF;
        }

        eZFile::create( $file, $csvDir, trim( $csvData ) );
    }
}
?>