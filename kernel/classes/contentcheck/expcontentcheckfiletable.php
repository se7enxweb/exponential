<?php
/**
 * File containing the expContentCheckFileTable class.
 *
 * The file of a field whose datatype keeps it in a table with the columns of ezbinaryfile: contentobject_attribute_id,
 * version, filename, original_filename, mime_type (doc/eng-us/features/6.0/content-checks.md, "Missing files"). The file is
 * var/<site>/storage/original/<the mime type's group>/<filename>, as eZBinaryFile::storedFileInfo() and
 * eZMedia::storedFileInfo() build it. The table of each datatype is contentcheck.ini [MissingFileSettings]
 * Tables[<datatype>] (ezbinaryfile and ezmedia in the kernel).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentCheckFileTable implements expContentCheckFileHandler
{
    public function files( array $attributes )
    {
        $byTable = array();
        foreach ( $attributes as $attribute )
        {
            $table = self::table( (string)$attribute['data_type_string'] );
            if ( $table !== '' )
                $byTable[$table][(int)$attribute['id']][] = (int)$attribute['version'];
        }
        $files = array();
        $db = eZDB::instance();
        foreach ( $byTable as $table => $versionsByAttribute )
        {
            $rows = $db->arrayQuery( 'SELECT contentobject_attribute_id, version, filename, original_filename, mime_type FROM ' . $table .
                                     ' WHERE contentobject_attribute_id IN ( ' . expContentCheck::inList( array_keys( $versionsByAttribute ) ) . ' )' );
            foreach ( is_array( $rows ) ? $rows : array() as $row )
            {
                $attributeID = (int)$row['contentobject_attribute_id'];
                $version = (int)$row['version'];
                if ( !in_array( $version, $versionsByAttribute[$attributeID], true ) )
                    continue;
                $path = self::path( (string)$row['mime_type'], (string)$row['filename'] );
                if ( $path === '' )
                    continue;
                $files[$attributeID . '-' . $version] = array( array( 'role' => 'original', 'alias' => '', 'path' => $path, 'alias_key' => '',
                                                                       'timestamp' => 0, 'name' => (string)$row['original_filename'] ) );
            }
        }
        return $files;
    }

    /**
     * The path of a stored file, as the datatypes build it.
     *
     * @param string $mimeType
     * @param string $fileName
     * @param string|null $storageDirectory null: eZSys::storageDirectory()
     * @return string '' when the row names no file
     */
    public static function path( $mimeType, $fileName, $storageDirectory = null )
    {
        $name = eZBinaryFile::safeFileName( (string)$fileName );
        if ( $name === '' )
            return '';
        $group = eZBinaryFile::mimeGroup( (string)$mimeType );
        $storageDirectory = $storageDirectory === null ? eZSys::storageDirectory() : (string)$storageDirectory;
        return $storageDirectory . '/original/' . $group . '/' . $name;
    }

    /**
     * The table of a datatype, from the settings; only a plain table name.
     *
     * @param string $datatype
     * @return string '' for a datatype without one
     */
    public static function table( $datatype )
    {
        $tables = expContentCheck::setting( 'MissingFileSettings', 'Tables', array() );
        $table = is_array( $tables ) && isset( $tables[$datatype] ) ? (string)$tables[$datatype] : '';
        return preg_match( '/^[a-z][a-z0-9_]{0,63}$/', $table ) ? $table : '';
    }

    public function removeReference( eZContentObjectAttribute $attribute )
    {
        $table = self::table( (string)$attribute->attribute( 'data_type_string' ) );
        if ( $table === '' )
            return false;
        // the row only: the file is missing already, and another version may name the same file name
        eZDB::instance()->query( 'DELETE FROM ' . $table . ' WHERE contentobject_attribute_id = ' . (int)$attribute->attribute( 'id' ) .
                                 ' AND version = ' . (int)$attribute->attribute( 'version' ) );
        $attribute->setAttribute( 'data_text', '' );
        $attribute->setAttribute( 'sort_key_string', '' );
        $attribute->store();
        return true;
    }

    public function hasAliases()
    {
        return false;
    }
}

?>
