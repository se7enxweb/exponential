<?php
/**
 * File containing the expSecret class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One secret of the kernel's secret store (table exp_secret): its name, the tool it belongs to (scope), a description,
 * and its value encrypted with XChaCha20-Poly1305 (sodium) under the store's key (expSecretKey). The value is never
 * an attribute: only expSecrets decrypts it, for the code that needs it. See expSecrets.
 *
 * @since 6.0.15
 * @package kernel
 */
class expSecret extends eZPersistentObject
{
    const TABLE = 'exp_secret';

    public static function definition()
    {
        static $definition = null;
        if ( $definition === null )
        {
            $definition = array(
                'fields' => array(
                    'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'name' => array( 'name' => 'Name', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'scope' => array( 'name' => 'Scope', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'description' => array( 'name' => 'Description', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'ciphertext' => array( 'name' => 'Ciphertext', 'datatype' => 'text', 'default' => '', 'required' => true ),
                    'key_id' => array( 'name' => 'KeyID', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'created' => array( 'name' => 'Created', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'modified' => array( 'name' => 'Modified', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'creator_id' => array( 'name' => 'CreatorID', 'datatype' => 'integer', 'default' => 0, 'required' => true,
                                           'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id', 'multiplicity' => '1..*' ),
                    'modifier_id' => array( 'name' => 'ModifierID', 'datatype' => 'integer', 'default' => 0, 'required' => true,
                                            'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id', 'multiplicity' => '1..*' ),
                ),
                'keys' => array( 'id' ),
                'increment_key' => 'id',
                'function_attributes' => array( 'readable' => 'isReadable' ),
                'sort' => array( 'name' => 'asc' ),
                'class_name' => 'expSecret',
                'name' => self::TABLE,
            );
        }
        return $definition;
    }

    /**
     * @param string $name
     * @return expSecret|null
     */
    public static function fetchByName( $name )
    {
        return self::fetchObject( self::definition(), null, array( 'name' => (string)$name ) );
    }

    /**
     * @param string|null $scope only this tool's secrets
     * @return expSecret[]
     */
    public static function fetchList( $scope = null )
    {
        $conditions = $scope === null ? null : array( 'scope' => (string)$scope );
        $list = self::fetchObjectList( self::definition(), null, $conditions, array( 'name' => 'asc' ) );
        return is_array( $list ) ? $list : array();
    }

    /** @return bool whether the store can decrypt the value (its key is the current or the previous one) */
    public function isReadable()
    {
        return expSecrets::decryptRow( $this ) !== null;
    }
}
?>
