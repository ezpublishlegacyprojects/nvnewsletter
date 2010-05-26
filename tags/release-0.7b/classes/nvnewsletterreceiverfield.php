<?php
/**
 * File containing the nvNewsletterReceiverField class
 *
 * @copyright Copyright (c) 2009 Naviatech Solutions. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License V2
 * @author Naviatech Solutions <http://www.naviatech.fi>
 * @version 0.7b
 * @package nvNewsletter
 */
/**
 * Class nvNewsletterReceiverField handles newsletter receivers additional fields
 */
class nvNewsletterReceiverField extends eZPersistentObject 
{
    const STATUS_DRAFT     = 0;
    const STATUS_PUBLISHED = 1;

    const TYPE_TEXT = 'TEXT';
    const TYPE_INT  = 'INT';

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
                    'field_name' => array(
                        'name' => 'field_name',
                        'datatype' => 'string',
                        'default' => '',
                        'required' => true),
                    'field_type' => array(
                        'name' => 'field_type',
                        'datatype' => 'string',
                        'default' => '',
                        'required' => true),
                    'required' => array(
                        'name' => 'required',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => false),
                    'meta' => array(
                        'name' => 'meta',
                        'datatype' => 'string',
                        'default' => '',
                        'required' => false),
                    'field_order' => array(
                        'name' => 'field_order',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => false),
                    'status' => array(
                        'name' => 'status',
                        'datatype' => 'integer',
                        'default' => 0,
                        'required' => true)),
                'keys'          => array('id', 'status'),
                'increment_key' => 'id',
                'sort'          => array('id' => 'asc'),
                'function_attributes' => array(
                    'groups' => 'groups',
                    'group_ids' => 'groupIDs'),
                'class_name'    => 'nvNewsletterReceiverField',
                'name'          => 'nvnewsletter_receiverfields');
    }

    function attribute( $attr, $noFunction = false ) 
    {
        return eZPersistentObject::attribute($attr);
    }

    static function fetchByOffset( $status=self::STATUS_PUBLISHED, $asObject=true ) 
    {
        return eZPersistentObject::fetchObjectList( self::definition(), 
                                                    null, 
                                                    array('status' => $status),
                                                    array('field_order' => 'ASC'), 
                                                    null, 
                                                    $asObject);
    }

    static function fetchList( $status = self::STATUS_PUBLISHED, $asObject=true ) 
    {
        return eZPersistentObject::fetchObjectList( self::definition(), 
                                                    null, 
                                                    array( 'status' => $status ),
                                                    array( 'field_order' => 'ASC' ), 
                                                    null, 
                                                    $asObject);
    }

    static function fetch( $receiverFieldID, $status=self::STATUS_PUBLISHED, $asObject=true ) 
    {
        return eZPersistentObject::fetchObject( self::definition(), 
                                                null,
                                                array( 'id' => $receiverFieldID, 'status' => $status ), 
                                                $asObject);
    }

    static function fetchDraft( $id, $asObject=true ) 
    {
        $field = nvNewsletterReceiverField::fetch( $id, self::STATUS_DRAFT, $asObject );
        
        if ( !$field ) 
        {
            $field = nvNewsletterReceiverField::fetch( $id, self::STATUS_PUBLISHED, $asObject );
            
            if ( $field ) 
            {
                $field->setAttribute('status', self::STATUS_DRAFT);
                $field->store();
            }
        }

        if ( !$field ) 
        {
            return false;
        }

        return $field;
    }

    function publish() 
    {
        $this->setAttribute( 'status', self::STATUS_PUBLISHED );
        $this->store();
        $this->removeDraft();
    }

    function removeDraft() 
    {
        $receiverDraft = self::fetchDraft( $this->attribute('id') );
        $receiverDraft->remove();
    }

    static function removeAll($id) 
    {
        eZPersistentObject::removeObject( self::definition(),
                                          array( 'id' => $id ) );
    }

    static function create( $fieldName='', $fieldType=self::TYPE_TEXT ) 
    {
        $field = new nvNewsletterReceiverField( array( 'field_name' => $fieldName,
                                                       'field_type' => $fieldType,
                                                       'status' => self::STATUS_DRAFT ) );
        $field->store();

        return $field;
    }
}
