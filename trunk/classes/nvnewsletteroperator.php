<?php
/**
 * nvNewsletterOperator class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletterOperator contains newsletter template operators
 */
class nvNewsletterOperator
{
    function __construct(){}

    function operatorList()
    {
        return array( 'nvnewslettergetviewlink', 'nvnewslettergetsitelink', 'is_nvnewsletter' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'nvnewslettergetviewlink' => array( 'objectID' => array( 'type' => 'integer',
                                                                               'required' => true,
                                                                               'default' => '' ),
                                                          'objectVersion' => array( 'type' => 'integer',
                                                                               'required' => true,
                                                                               'default' => '' ) ),
                    'nvnewslettergetsitelink' => array( 'objectID' => array( 'type' => 'integer',
                                                                             'required' => true,
                                                                             'default' => '' ),
                                                        'objectVersion' => array( 'type' => 'integer',
                                                                                  'required' => true,
                                                                                  'default' => '' ) ),
                    'is_nvnewsletter' => array() );
    }
    
    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement )
    {
        switch ( $operatorName )
        {
            case 'nvnewslettergetviewlink':
            {
                if ( is_numeric( $namedParameters['objectID'] ) && is_numeric( $namedParameters['objectVersion'] ) ) 
                {
                    $objectID      = $namedParameters['objectID'];
                    $objectVersion = $namedParameters['objectVersion'];
                    $object        = eZContentObject::fetch($objectID);
                    $siteDomain    = nvNewsletterTools::getLink( $objectID, $objectVersion );
                    $hash          = nvNewsletterTools::getFileHash( $objectID, $objectVersion, $object->CurrentLanguage );
                    $newsletterLink = "/nvnewsletter/viewmail/$objectID/$objectVersion/$hash";

                    $operatorValue = $siteDomain[0].$newsletterLink;
                }
            }
            break;
            case 'nvnewslettergetsitelink':
            {
                if ( is_numeric( $namedParameters['objectID'] ) && is_numeric( $namedParameters['objectVersion'] ) ) 
                {
                    $objectID      = $namedParameters['objectID'];
                    $objectVersion = $namedParameters['objectVersion'];
                    $siteDomain    = nvNewsletterTools::getLink( $objectID, $objectVersion );

                    $operatorValue = $siteDomain;
                }
            }
            break;
            case 'is_nvnewsletter':
            {
                $contentObject = $operatorValue;
                
                if ( nvNewsletterTools::isNewsletter( $contentObject ) )
                {
                    $operatorValue = true;
                }
                else
                {
                    $operatorValue = false;
                }
                
                unset( $contentObject );
            }
            break;
        }
    }
}
?>