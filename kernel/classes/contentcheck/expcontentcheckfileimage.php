<?php
/**
 * File containing the expContentCheckFileImage class.
 *
 * The files of an image field (ezimage): the original and the aliases its stored XML names
 * (doc/eng-us/features/6.0/content-checks.md, "Missing files"). Reads the XML only; no image is opened.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentCheckFileImage implements expContentCheckFileHandler
{
    public function files( array $attributes )
    {
        $files = array();
        foreach ( $attributes as $attribute )
        {
            $key = (int)$attribute['id'] . '-' . (int)$attribute['version'];
            $files[$key] = self::filesOfXML( isset( $attribute['data_text'] ) ? (string)$attribute['data_text'] : '' );
        }
        return $files;
    }

    /**
     * The original and the aliases an image field's XML names.
     *
     * @param string $xml
     * @return array list of files (see expContentCheckFileHandler::files()); empty for an empty or broken field
     */
    public static function filesOfXML( $xml )
    {
        $xml = (string)$xml;
        if ( $xml === '' || strpos( $xml, '<ezimage' ) === false )
            return array();
        $doc = new DOMDocument( '1.0', 'utf-8' );
        $useErrors = libxml_use_internal_errors( true );
        // no network, no entities: the text is data
        $loaded = $doc->loadXML( $xml, LIBXML_NONET );
        libxml_clear_errors();
        libxml_use_internal_errors( $useErrors );
        if ( !$loaded )
            return array();
        $image = $doc->getElementsByTagName( 'ezimage' )->item( 0 );
        if ( !$image instanceof DOMElement )
            return array();
        $original = self::cleanPath( $image->getAttribute( 'url' ) );
        if ( $original === '' )
            return array();
        $files = array( array( 'role' => 'original', 'alias' => '', 'path' => $original, 'alias_key' => (string)$image->getAttribute( 'alias_key' ),
                               'timestamp' => (int)$image->getAttribute( 'timestamp' ), 'name' => (string)$image->getAttribute( 'original_filename' ) ) );
        $seen = array( '' => true );
        foreach ( $image->getElementsByTagName( 'alias' ) as $alias )
        {
            $name = (string)$alias->getAttribute( 'name' );
            $path = self::cleanPath( $alias->getAttribute( 'url' ) );
            if ( $name === '' || $name === 'original' || isset( $seen[$name] ) || !preg_match( '/^[A-Za-z0-9_\-]{1,64}$/', $name ) )
                continue;
            $seen[$name] = true;
            $files[] = array( 'role' => 'alias', 'alias' => $name, 'path' => $path, 'alias_key' => (string)$alias->getAttribute( 'alias_key' ),
                              'timestamp' => (int)$alias->getAttribute( 'timestamp' ), 'name' => '' );
        }
        return $files;
    }

    /**
     * A stored path as a path inside the installation: relative, no "..", no NUL; '' for anything else.
     *
     * @param string $path
     * @return string
     */
    public static function cleanPath( $path )
    {
        $path = ltrim( str_replace( '\\', '/', (string)$path ), '/' );
        if ( $path === '' || strpos( $path, "\0" ) !== false || preg_match( '#(^|/)\.\.(/|$)#', $path ) || preg_match( '#^[a-z][a-z0-9+.-]*:#i', $path ) )
            return '';
        return $path;
    }

    public function removeReference( eZContentObjectAttribute $attribute )
    {
        $attributeID = (int)$attribute->attribute( 'id' );
        $version = (int)$attribute->attribute( 'version' );
        $files = self::filesOfXML( (string)$attribute->attribute( 'data_text' ) );
        $attribute->setAttribute( 'data_text', '' );
        $attribute->store();
        // the row of ezimagefile that names the missing original goes when no other version of the field names it;
        // a file is never touched
        if ( $files && class_exists( 'eZImageFile' ) )
        {
            $path = $files[0]['path'];
            $named = false;
            $others = eZDB::instance()->arrayQuery( 'SELECT data_text FROM ezcontentobject_attribute WHERE id = ' . $attributeID . ' AND version <> ' . $version );
            foreach ( is_array( $others ) ? $others : array() as $other )
            {
                foreach ( self::filesOfXML( (string)$other['data_text'] ) as $file )
                    $named = $named || $file['path'] === $path;
            }
            if ( !$named && !eZClusterFileHandler::instance( $path )->exists() )
                eZImageFile::removeFilepath( $attributeID, $path );
        }
        return true;
    }

    public function hasAliases()
    {
        return true;
    }
}

?>
