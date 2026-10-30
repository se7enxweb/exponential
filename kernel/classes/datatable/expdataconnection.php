<?php
/**
 * File containing the expDataConnection class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A named database connection made in the administration (Setup > Data tables > Connections): a row of
 * exp_data_connection. The password is the NAME of a secret of the kernel's secret store (expSecrets), never a value.
 * Connections an extension declares in datatable.ini [Connection_<identifier>] are not rows; expDataConnections
 * lists both the same way (doc/eng-us/features/6.0/data-tables.md, "Connections").
 *
 * @since 6.0.15
 * @package kernel
 */
class expDataConnection extends eZPersistentObject
{
    const TABLE = 'exp_data_connection';

    public static function definition()
    {
        static $definition = null;
        if ( $definition === null )
        {
            $int = function ( $name ) { return array( 'name' => $name, 'datatype' => 'integer', 'default' => 0, 'required' => true ); };
            $str = function ( $name ) { return array( 'name' => $name, 'datatype' => 'string', 'default' => '', 'required' => true ); };
            $definition = array(
                'fields' => array(
                    'id' => $int( 'ID' ),
                    'identifier' => $str( 'Identifier' ),
                    'name' => $str( 'Name' ),
                    'engine' => $str( 'Engine' ),
                    'server' => $str( 'Server' ),
                    'port' => $int( 'Port' ),
                    'database_name' => $str( 'DatabaseName' ),
                    'user_name' => $str( 'UserName' ),
                    'secret' => $str( 'Secret' ),
                    'options' => array( 'name' => 'Options', 'datatype' => 'text', 'default' => '', 'required' => false ),
                    'description' => $str( 'Description' ),
                    'read_only' => $int( 'ReadOnly' ),
                    'created' => $int( 'Created' ),
                    'modified' => $int( 'Modified' ),
                    'creator_id' => $int( 'CreatorID' ) + array( 'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id', 'multiplicity' => '1..*' ),
                    'modifier_id' => $int( 'ModifierID' ) + array( 'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id', 'multiplicity' => '1..*' ),
                ),
                'keys' => array( 'id' ),
                'increment_key' => 'id',
                'function_attributes' => array( 'options_array' => 'optionsArray' ),
                'sort' => array( 'identifier' => 'asc' ),
                'class_name' => 'expDataConnection',
                'name' => self::TABLE,
            );
        }
        return $definition;
    }

    /**
     * @param string $identifier
     * @return expDataConnection|null
     */
    public static function fetchByIdentifier( $identifier )
    {
        return self::fetchObject( self::definition(), null, array( 'identifier' => (string)$identifier ) );
    }

    /** @return expDataConnection[] */
    public static function fetchList()
    {
        $list = self::fetchObjectList( self::definition(), null, null, array( 'identifier' => 'asc' ) );
        return is_array( $list ) ? $list : array();
    }

    /** @return array the options (charset, socket, dsn, dialect, timeout ...) */
    public function optionsArray()
    {
        $options = json_decode( (string)$this->attribute( 'options' ), true );
        return is_array( $options ) ? $options : array();
    }

    /**
     * The connection as expDataConnections describes every connection.
     *
     * @return array
     */
    public function toDescriptor()
    {
        return expDataConnections::normalize( array(
            'identifier' => $this->attribute( 'identifier' ),
            'name' => $this->attribute( 'name' ),
            'engine' => $this->attribute( 'engine' ),
            'server' => $this->attribute( 'server' ),
            'port' => $this->attribute( 'port' ),
            'database' => $this->attribute( 'database_name' ),
            'user' => $this->attribute( 'user_name' ),
            'secret' => $this->attribute( 'secret' ),
            'options' => $this->optionsArray(),
            'description' => $this->attribute( 'description' ),
            'read_only' => (bool)$this->attribute( 'read_only' ),
            'origin' => 'admin',
            'id' => (int)$this->attribute( 'id' ),
            'modified' => (int)$this->attribute( 'modified' ),
        ) );
    }
}
?>
