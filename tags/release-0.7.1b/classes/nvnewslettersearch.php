<?php
/**
 * File containing the nvNewsletterSearch class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7.1b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletterSearch handles search functions
 */
class nvNewsletterSearch 
{
    /**
     * Receiver search
     *
     * @param string $searchText
     * @param array $searchParams
     * @param array $limit
     * @param array $sorts
     * @return array
     * @todo Implement search with eZ Find if possible.
     */
    static function receiver( $searchText, $searchParams, $limit=array( 'offset' => 0, 'limit' => 10 ), $sorts=array( 'email_address' => 'asc' ) ) 
    {
        $db  = eZDB::instance();
        $ini = eZINI::instance('nvnewsletter.ini');
        
        $wildcard    = $ini->variable('SearchSettings', 'AllowWildcard');
        $wildcardPre = $ini->variable('SearchSettings', 'AllowWildcardPre');
    
        $searchText = trim( ereg_replace('\*\*+', '*', $searchText ) );
        $searchText = trim( ereg_replace(' +', ' ', $searchText ) );
        $searchText = $db->escapeString( $searchText );
        $searchText = str_replace( array('*'), array('%'), $searchText );
        
        $isGroupQuery = false;
        
        if ( $wildcard == 'disabled' ) 
        {
            $searchText = str_replace( array('*', '%'), '', $searchText );
        }

        if ( $wildcardPre == 'disabled' ) 
        {
            $hasWildcardPre = substr( $searchText, 0, 1 );
            if ( $hasWildcardPre == '%' ) 
            {
                $searchText = substr( $searchText, 1 );
            }
        }
    
        if ( strlen( $searchText ) < 2 ) 
        {
            return array( 'SearchResults' => false, 
                          'SearchCount'   => 0 );
        }
        
        if ( $searchText == '%' ) 
        {
            return array( 'SearchResults' => false, 
                          'SearchCount'   => 0 );
        }
        
        if ( is_numeric( $searchParams['searchGroup'] ) ) 
        {
            $isGroupQuery = true;
            $sqlFromGroupSub  = "nvnewsletter_receivers_has_groups nrhg, ";
            $sqlFromGroupUnsub  = "nvnewsletter_receivers_has_groups_unsub nrhgu, ";
            $sqlWhereGroupSub = "nrhg.receiver_id = nr.id AND nrhg.receivergroup_id = ".$searchParams['searchGroup']." AND ";
            $sqlWhereGroupUnsub = "nrhgu.receiver_id = nr.id AND nrhgu.receivergroup_id = ".$searchParams['searchGroup']." AND ";
        }
        
        if ( is_numeric( $limit['offset'] ) && 
             is_numeric( $limit['limit'] ) ) 
        {
            $sqlLimit = " LIMIT ".$limit['offset'].", ".$limit['limit'];
        }
        
        if ( $sorts ) 
        {
            foreach ( $sorts as $key => $value ) 
            {
                if ( $value == 'asc' || $value == 'desc' ) 
                {
                    $value = $value;
                } 
                else 
                {
                    $value = 'asc';
                }
                
                $sqlSort = " ORDER BY ".$db->escapeString( $key )." $value";
                
                break;
            }
        }
        
        // Search from user fields
        if ( $searchParams['searchFrom'] == 'fields' ) 
        {
            $fieldsSearchText = str_replace( array('%'), array(''), $searchText );
            $fieldsData = explode( ' ', $fieldsSearchText );
            $fieldsSQL  = "";
            
            if ( count( $fieldsData ) > 1 ) 
            {
                $fieldsSQL  = "( nrhf.data = '" . implode( "' OR nrhf.data = '", $fieldsData ) . "' ) OR ";
            }
            
            if ( $isGroupQuery ) 
            {
                $sql = "
                    SELECT * FROM 
                    (
                        (
                        SELECT DISTINCT
                            nr.id, nr.email_address 
                        FROM 
                            $sqlFromGroupSub
                            nvnewsletter_receivers nr, 
                            nvnewsletter_receivers_has_fields nrhf
                        WHERE 
                            $sqlWhereGroupSub
                            nr.status = ".nvNewsletterReceiver::STATUS_PUBLISHED." AND 
                            ( $fieldsSQL  
                              nrhf.data LIKE '$searchText' ) AND 
                              nrhf.receiver_id = nr.id
                        )
                    UNION ALL 
                        (
                        SELECT DISTINCT
                            nr.id, nr.email_address 
                        FROM 
                            $sqlFromGroupUnsub
                            nvnewsletter_receivers nr, 
                            nvnewsletter_receivers_has_fields nrhf
                        WHERE 
                            $sqlWhereGroupUnsub
                            nr.status = ".nvNewsletterReceiver::STATUS_PUBLISHED." AND 
                            ( $fieldsSQL  
                              nrhf.data LIKE '$searchText' ) AND 
                              nrhf.receiver_id = nr.id
                        )
                    ) AS combinedGroups
                    $sqlSort
                    $sqlLimit";
                
                $sqlCount = "
                    SELECT COUNT( * ) AS count FROM 
                    (
                        (
                        SELECT DISTINCT
                            nr.id, nr.email_address 
                        FROM 
                            $sqlFromGroupSub
                            nvnewsletter_receivers nr, 
                            nvnewsletter_receivers_has_fields nrhf
                        WHERE 
                            $sqlWhereGroupSub
                            nr.status = ".nvNewsletterReceiver::STATUS_PUBLISHED." AND 
                            ( $fieldsSQL  
                              nrhf.data LIKE '$searchText' ) AND 
                              nrhf.receiver_id = nr.id
                        )
                    UNION DISTINCT  
                        (
                        SELECT DISTINCT
                            nr.id, nr.email_address 
                        FROM 
                            $sqlFromGroupUnsub
                            nvnewsletter_receivers nr, 
                            nvnewsletter_receivers_has_fields nrhf
                        WHERE 
                            $sqlWhereGroupUnsub
                            nr.status = ".nvNewsletterReceiver::STATUS_PUBLISHED." AND 
                            ( $fieldsSQL  
                              nrhf.data LIKE '$searchText' ) AND 
                              nrhf.receiver_id = nr.id
                        )
                    ) AS combinedGroups";
            } 
            else 
            {
                $sql = "SELECT DISTINCT
                            nr.id, nr.email_address 
                        FROM 
                            nvnewsletter_receivers nr, 
                            nvnewsletter_receivers_has_fields nrhf
                        WHERE 
                            nr.status = ".nvNewsletterReceiver::STATUS_PUBLISHED." AND 
                            ( $fieldsSQL  
                              nrhf.data LIKE '$searchText' ) AND 
                              nrhf.receiver_id = nr.id
                        $sqlLimit";

                $sqlCount = "SELECT 
                                COUNT( * ) AS count 
                            FROM 
                                nvnewsletter_receivers nr, 
                                nvnewsletter_receivers_has_fields nrhf 
                            WHERE 
                                nr.status = ".nvNewsletterReceiver::STATUS_PUBLISHED." AND 
                                ( $fieldsSQL  
                                  nrhf.data LIKE '$searchText' ) AND 
                                  nrhf.receiver_id = nr.id";
            }
        // Search from emails
        } 
        else 
        {
            if ( $isGroupQuery ) 
            {
                $sql = "
                    SELECT * FROM 
                    (
                        (
                        SELECT 
                            nr.id, nr.email_address  
                        FROM 
                            $sqlFromGroupSub
                            nvnewsletter_receivers nr
                        WHERE 
                            $sqlWhereGroupSub
                            nr.status = ".nvNewsletterReceiver::STATUS_PUBLISHED." AND 
                            nr.email_address LIKE '$searchText'
                        )
                    UNION ALL 
                        (
                        SELECT 
                            nr.id, nr.email_address  
                        FROM 
                            $sqlFromGroupUnsub
                            nvnewsletter_receivers nr
                        WHERE 
                            $sqlWhereGroupUnsub
                            nr.status = ".nvNewsletterReceiver::STATUS_PUBLISHED." AND 
                            nr.email_address LIKE '$searchText'
                        )
                    ) AS combinedGroups
                    $sqlSort
                    $sqlLimit";
                
                $sqlCount = "
                    SELECT COUNT( * ) AS count FROM 
                    (
                        (
                        SELECT 
                            nr.id, nr.email_address  
                        FROM 
                            $sqlFromGroupSub
                            nvnewsletter_receivers nr
                        WHERE 
                            $sqlWhereGroupSub
                            nr.status = ".nvNewsletterReceiver::STATUS_PUBLISHED." AND 
                            nr.email_address LIKE '$searchText'
                        )
                    UNION ALL 
                        (
                        SELECT 
                            nr.id, nr.email_address  
                        FROM 
                            $sqlFromGroupUnsub
                            nvnewsletter_receivers nr
                        WHERE 
                            $sqlWhereGroupUnsub
                            nr.status = ".nvNewsletterReceiver::STATUS_PUBLISHED." AND 
                            nr.email_address LIKE '$searchText'
                        )
                    ) AS combinedGroups";
            } 
            else 
            {
                $sql = "SELECT 
                            nr.id, nr.email_address  
                        FROM 
                            nvnewsletter_receivers nr
                        WHERE 
                            nr.status = ".nvNewsletterReceiver::STATUS_PUBLISHED." AND 
                            nr.email_address LIKE '$searchText'
                        $sqlSort
                        $sqlLimit";

                $sqlCount = "SELECT 
                                COUNT( * ) AS count 
                            FROM 
                                nvnewsletter_receivers nr
                            WHERE 
                                nr.status = ".nvNewsletterReceiver::STATUS_PUBLISHED." AND 
                                nr.email_address LIKE '$searchText'";
            }
        }
        
        $resultCount = $db->arrayQuery( $sqlCount );
        
        if ( is_numeric( $resultCount[0]['count'] ) ) 
        {
            $resultCount = $resultCount[0]['count'];
        } 
        else 
        {
            $resultCount = 0;
        }
        
        if ( $resultCount > 0 ) 
        {
            $result = $db->arrayQuery( $sql );
        }
        
        if ( !count( $result ) ) 
        {
            $result = false;
        }
        
        return array( 'SearchResults' => $result, 
                      'SearchCount'   => $resultCount );
    }
}
?>
