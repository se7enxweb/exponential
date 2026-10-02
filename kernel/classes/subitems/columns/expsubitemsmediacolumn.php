<?php
/**
 * Subitems list columns about the images and files of the content object.
 *
 * Field= picks the column: has_image, image_count, image_dimensions, image_width, image_height,
 * image_size, image_mime, image_alt, file_name, file_size, file_mime, file_downloads.
 * The image fields read the first image attribute (ezimage) with content -- Attributes[] in the
 * column block lists identifiers to try first --, its "original" alias, which is stored in the
 * attribute itself (no alias is generated). The file fields read the first binary file
 * (ezbinaryfile, enhancedezbinaryfile) or media (ezmedia) attribute. Every field reads the data
 * map, which the columns of one row share; image_size and file_size ask the file handler for
 * the stored file's size. Objects whose class has no such attribute give null.
 * Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsMediaColumn extends expSubitemsFieldColumn
{
    const IMAGE_TYPES = array( 'ezimage' );
    const FILE_TYPES = array( 'ezbinaryfile', 'enhancedezbinaryfile', 'ezmedia' );

    /** Whether the object has an image; null when its class has no image attribute at all. */
    protected function fieldHasImage( eZContentObjectTreeNode $node )
    {
        if ( !$this->hasType( $node, self::IMAGE_TYPES ) )
            return null;
        return $this->image( $node ) !== null;
    }

    /** Image attributes with an image. */
    protected function fieldImageCount( eZContentObjectTreeNode $node )
    {
        if ( !$this->hasType( $node, self::IMAGE_TYPES ) )
            return null;
        $n = 0;
        foreach ( self::dataMap( $node ) as $attribute )
        {
            if ( $attribute->attribute( 'data_type_string' ) === 'ezimage' && $attribute->hasContent() )
                $n++;
        }
        return $n;
    }

    /** "1200 x 800" (width x height) of the original image. */
    protected function fieldImageDimensions( eZContentObjectTreeNode $node )
    {
        $original = $this->image( $node );
        if ( !$original || empty( $original['width'] ) || empty( $original['height'] ) )
            return null;
        return (int)$original['width'] . ' x ' . (int)$original['height'];
    }

    protected function fieldImageWidth( eZContentObjectTreeNode $node )
    {
        $original = $this->image( $node );
        return $original && !empty( $original['width'] ) ? (int)$original['width'] : null;
    }

    protected function fieldImageHeight( eZContentObjectTreeNode $node )
    {
        $original = $this->image( $node );
        return $original && !empty( $original['height'] ) ? (int)$original['height'] : null;
    }

    /** The original image's size in bytes (the cell shows it as kB/MB). */
    protected function fieldImageSize( eZContentObjectTreeNode $node )
    {
        $original = $this->image( $node );
        if ( !$original )
            return null;
        if ( !empty( $original['filesize'] ) )
            return (int)$original['filesize'];
        if ( !empty( $original['url'] ) )
        {
            $file = eZClusterFileHandler::instance( $original['url'] );
            $size = $file->exists() ? $file->size() : false;
            return $size !== false && $size !== null ? (int)$size : null;
        }
        return null;
    }

    protected function fieldImageMime( eZContentObjectTreeNode $node )
    {
        $original = $this->image( $node );
        return $original && !empty( $original['mime_type'] ) ? (string)$original['mime_type'] : null;
    }

    /** The image's alternative text; '' (shown empty) when the image has none -- worth fixing. */
    protected function fieldImageAlt( eZContentObjectTreeNode $node )
    {
        $original = $this->image( $node );
        if ( !$original )
            return null;
        return isset( $original['alternative_text'] ) ? (string)$original['alternative_text'] : '';
    }

    /** The name the file was uploaded with. */
    protected function fieldFileName( eZContentObjectTreeNode $node )
    {
        $file = $this->file( $node );
        return $file ? (string)$file->attribute( 'original_filename' ) : null;
    }

    protected function fieldFileSize( eZContentObjectTreeNode $node )
    {
        $file = $this->file( $node );
        if ( !$file )
            return null;
        $size = $file->attribute( 'filesize' );
        return $size === false || $size === null ? null : (int)$size;
    }

    protected function fieldFileMime( eZContentObjectTreeNode $node )
    {
        $file = $this->file( $node );
        return $file ? (string)$file->attribute( 'mime_type' ) : null;
    }

    /** Downloads counted by content/download (binary files only). */
    protected function fieldFileDownloads( eZContentObjectTreeNode $node )
    {
        $file = $this->file( $node );
        if ( !$file || !$file->hasAttribute( 'download_count' ) )
            return null;
        return (int)$file->attribute( 'download_count' );
    }

    /** Sizes as "245 kB", the rest as the base class shows it. */
    public function html( eZContentObjectTreeNode $node, $value )
    {
        if ( is_int( $value ) && in_array( $this->setting( 'Field' ), array( 'image_size', 'file_size' ), true ) )
            return self::escape( self::formatBytes( $value ) );
        return parent::html( $node, $value );
    }

    /** Whether the object's class has an attribute of one of $types (filled or not). */
    protected function hasType( eZContentObjectTreeNode $node, array $types )
    {
        foreach ( self::dataMap( $node ) as $attribute )
        {
            if ( in_array( $attribute->attribute( 'data_type_string' ), $types, true ) )
                return true;
        }
        return false;
    }

    /** The "original" alias array of the first image, or null. */
    protected function image( eZContentObjectTreeNode $node )
    {
        $attribute = self::firstAttribute( $node, self::IMAGE_TYPES, $this->listSetting( 'Attributes' ) );
        if ( !$attribute )
            return null;
        $id = $attribute->attribute( 'id' ) . '/' . $attribute->attribute( 'version' ) . '/' . $attribute->attribute( 'language_code' );
        return self::memo( 'image', $id, function () use ( $attribute )
        {
            $handler = $attribute->content();
            if ( !is_object( $handler ) )
                return null;
            $original = $handler->attribute( 'original' );
            return is_array( $original ) && !empty( $original['is_valid'] ) ? $original : null;
        } );
    }

    /** The content (eZBinaryFile, eZMedia) of the first file attribute, or null. */
    protected function file( eZContentObjectTreeNode $node )
    {
        $attribute = self::firstAttribute( $node, self::FILE_TYPES, $this->listSetting( 'Attributes' ) );
        if ( !$attribute )
            return null;
        $content = $attribute->content();
        return is_object( $content ) && method_exists( $content, 'hasAttribute' ) ? $content : null;
    }
}
