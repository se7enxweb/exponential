<?php
/**
 * File containing the eZBinaryFile class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZMedia ezmedia.php
  \ingroup eZDatatype
  \brief The class eZMedia handles registered media files

*/

class eZMedia extends eZPersistentObject
{
    static function definition()
    {
        static $definition = array( "fields" => array( "contentobject_attribute_id" => array( 'name' => "ContentObjectAttributeID",
                                                                                'datatype' => 'integer',
                                                                                'default' => 0,
                                                                                'required' => true,
                                                                                'foreign_class' => 'eZContentObjectAttribute',
                                                                                'foreign_attribute' => 'id',
                                                                                'multiplicity' => '1..*' ),
                                         "version" => array( 'name' => "Version",
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true ),
                                         "filename" => array( 'name' => "Filename",
                                                              'datatype' => 'string',
                                                              'default' => '',
                                                              'required' => true ),
                                         "original_filename" => array( 'name' => "OriginalFilename",
                                                                       'datatype' => 'string',
                                                                       'default' => '',
                                                                       'required' => true ),
                                         "mime_type" => array( 'name' => "MimeType",
                                                               'datatype' => 'string',
                                                               'default' => '',
                                                               'required' => true ),
                                         "width" => array( 'name' => "Width",
                                                           'datatype' => 'integer',
                                                           'default' => 0,
                                                           'required' => true ),
                                         "height" => array( 'name' => "Height",
                                                            'datatype' => 'integer',
                                                            'default' => 0,
                                                            'required' => true ),
                                         "has_controller" => array( 'name' => "HasController",
                                                                    'datatype' => 'integer',
                                                                    'default' => 0,
                                                                    'required' => true ),
                                         "controls" => array( 'name' => "Controls",
                                                              'datatype' => 'string',
                                                              'default' => '',
                                                              'required' => true ),
                                         "is_autoplay" => array( 'name' => "IsAutoplay",
                                                                 'datatype' => 'integer',
                                                                 'default' => 0,
                                                                 'required' => true ),
                                         "pluginspage" => array( 'name' => "Pluginspage",
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => true ),
                                         "quality" => array( 'name' => 'Quality',
                                                             'datatype' => 'string',
                                                             'default' => '',
                                                             'required' => true ),
                                         "is_loop" => array( 'name' => "IsLoop",
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true ) ),
                      "keys" => array( "contentobject_attribute_id", "version" ),
                      'function_attributes' => array( 'filesize' => 'filesize',
                                                      'filepath' => 'filepath',
                                                      'mime_type_category' => 'mimeTypeCategory',
                                                      'mime_type_part' => 'mimeTypePart' ),
                      "relations" => array( "contentobject_attribute_id" => array( "class" => "ezcontentobjectattribute",
                                                                                   "field" => "id" ),
                                            "version" => array( "class" => "ezcontentobjectattribute",
                                                                "field" => "version" )),
                      "class_name" => "eZMedia",
                      "name" => "ezmedia" );
        return $definition;
    }

    function fileSize()
    {
        $fileInfo = $this->storedFileInfo();

        $file = eZClusterFileHandler::instance( $fileInfo['filepath'] );

        if ( $file->exists() )
        {
            return $file->size();
        }

        return 0;
    }

    function filePath()
    {
        $fileInfo = $this->storedFileInfo();
        return $fileInfo['filepath'];
    }

    function mimeTypeCategory()
    {
        $types = explode( "/", $this->attribute( "mime_type" ) );
        return $types[0];
    }

    function mimeTypePart()
    {
        // A mime type without a slash (empty, broken or imported data) has no part
        $types = explode( "/", (string)$this->attribute( "mime_type" ) );
        return $types[1] ?? '';
    }

    /*!
     \static
     \return the group of \a $mimeType ("video" of "video/mp4") as a directory name
     that stays inside var/storage/original, '' when there is none. The mime type
     comes from the database or a package and is data, not a path: a group of ".."
     or with a backslash or control character would otherwise walk the file path of
     a delete or a download out of the storage directory.
    */
    static function mimeGroup( $mimeType )
    {
        if ( !is_string( $mimeType ) || $mimeType === '' )
            return '';
        $parts = explode( '/', $mimeType, 2 );
        $group = $parts[0];
        if ( $group === '.' || $group === '..' || preg_match( '/[\\\\\x00-\x1f]/', $group ) )
            return '';
        return $group;
    }

    /*!
     \static
     \return true when \a $fileName is a plain file name that can be joined to a
     storage directory: not empty, no directory part, not "." or "..", no NUL.
     Every name this datatype stores is one (md5 + suffix); anything else came from
     a broken row or a crafted package and must not be used to reach a file.
    */
    static function isSafeFileName( $fileName )
    {
        return is_string( $fileName ) && $fileName !== '' && $fileName !== '.' && $fileName !== '..'
            && strpbrk( $fileName, "/\\\0" ) === false;
    }

    /*!
     \static
     \return \a $fileName reduced to its last path part, so that a stored name with
     a directory part cannot point a download or a package export outside the storage
     directory. A safe name (all names this datatype writes) is returned unchanged.
    */
    static function safeFileName( $fileName )
    {
        if ( eZMedia::isSafeFileName( $fileName ) )
            return $fileName;
        $name = basename( str_replace( array( '\\', "\0" ), '/', (string)$fileName ) );
        return ( $name === '.' || $name === '..' ) ? '' : $name;
    }

    static function create( $contentObjectAttributeID, $version )
    {
        $row = array( "contentobject_attribute_id" => $contentObjectAttributeID,
                      "version" => $version,
                      "filename" => "",
                      "original_filename" => "",
                      "mime_type" => "",
                      "width" => "0",
                      "height" => "0",
                      "controller" => true,
                      "autoplay" => true,
                      "pluginspage" => "",
                      "is_loop" => false,
                      "quality" => "",
                      "controls" => ""
                      );
        return new eZMedia( $row );
    }

    static function fetch( $id, $version, $asObject = true )
    {
        if( $version == null )
        {
            return eZPersistentObject::fetchObjectList( eZMedia::definition(),
                                                        null,
                                                        array( "contentobject_attribute_id" => $id ),
                                                        null,
                                                        null,
                                                        $asObject );
        }
        else
        {
            return eZPersistentObject::fetchObject( eZMedia::definition(),
                                                    null,
                                                    array( "contentobject_attribute_id" => $id,
                                                           "version" => $version ),
                                                    $asObject );
        }
    }

    static function fetchByFileName( $filename, $version = null, $asObject = true )
    {
        if ( $version == null )
        {
            return eZPersistentObject::fetchObjectList( eZMedia::definition(),
                                                        null,
                                                        array( 'filename' => $filename ),
                                                        null,
                                                        null,
                                                        $asObject );
        }
        else
        {
            return eZPersistentObject::fetchObject( eZMedia::definition(),
                                                    null,
                                                    array( 'filename' => $filename,
                                                           'version' => $version ),
                                                    $asObject );
        }
    }

    /**
     * Fetch media objects by content object id
     * @param int $contentObjectID contentobject id
     * @param string $languageCode language code
     * @param boolean $asObject if return object
     * @return array
     */
    static function fetchByContentObjectID( $contentObjectID, $languageCode = null, $asObject = true )
    {
        $condition = array();
        $condition['contentobject_id'] = $contentObjectID;
        $condition['data_type_string'] = 'ezmedia';
        if ( $languageCode != null )
        {
            $condition['language_code'] = $languageCode;
        }
        $custom = array( array( 'operation' => 'DISTINCT id',
                             'name' => 'id' ) );
        $ids = eZPersistentObject::fetchObjectList( eZContentObjectAttribute::definition(),
                                             array(),
                                             $condition,
                                             null,
                                             null,
                                             false,
                                             false,
                                             $custom );
        $mediaFiles = array();
        foreach ( $ids as $id )
        {
            $mediaFileObjectAttribute = eZMedia::fetch( $id['id'], null, $asObject );
            // A failed fetch gives null, which array_merge() refuses with a TypeError
            $mediaFiles = array_merge( $mediaFiles, (array)$mediaFileObjectAttribute );
        }
        return $mediaFiles;
    }

    static function removeByID( $id, $version )
    {
        if( $version == null )
        {
            eZPersistentObject::removeObject( eZMedia::definition(),
                                              array( "contentobject_attribute_id" => $id ) );
        }
        else
        {
            eZPersistentObject::removeObject( eZMedia::definition(),
                                              array( "contentobject_attribute_id" => $id,
                                                     "version" => $version ) );
        }
    }

    function storedFileInfo()
    {
        $fileName = $this->attribute( 'filename' );
        $mimeType = $this->attribute( 'mime_type' );
        $originalFileName = $this->attribute( 'original_filename' );

        $storageDir = eZSys::storageDirectory();

        // Neither part of the path is trusted to stay inside the storage directory;
        // a mime type without a slash used to raise an undefined offset warning
        $group = eZMedia::mimeGroup( $mimeType );

        $filePath = $storageDir . '/original/' . $group . '/' . eZMedia::safeFileName( $fileName );

        return array( 'filename' => $fileName,
                      'original_filename' => $originalFileName,
                      'filepath' => $filePath,
                      'mime_type' => $mimeType );
    }

    public $ContentObjectAttributeID;
    public $Filename;
    public $OriginalFilename;
    public $MimeType;
    public $Width;
    public $Height;
    public $HasController;
    public $Controls;
    public $IsLoop;
    public $IsAutoplay;
    public $Pluginspage;
    public $Quality;
}

?>
