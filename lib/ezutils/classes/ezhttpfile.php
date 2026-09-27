<?php
/**
 * File containing the eZHTTPFile class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZHTTPFile ezhttpfile.php
  \ingroup eZHTTP
  \brief Provides access to HTTP post files

  This class provides easy access to files posted by clients over HTTP.
  The HTTP file will be present as a temporary file which can be moved
  to store it, if not the file will be removed when the PHP script is done.

*/

class eZHTTPFile
{
    const UPLOADEDFILE_OK = 0;
    const UPLOADEDFILE_DOES_NOT_EXIST = -1;
    const UPLOADEDFILE_EXCEEDS_PHP_LIMIT = -2;
    const UPLOADEDFILE_EXCEEDS_MAX_SIZE = -3;
    const UPLOADEDFILE_MISSING_TMP_DIR = -4;
    const UPLOADEDFILE_CANT_WRITE = -5;
    const UPLOADEDFILE_UNKNOWN_ERROR = -6;

    /*!
     Initializes with a name and http variable.
    */
    public function __construct( /*! Name of the HTTP variable */ $http_name,
                                 /*! The HTTP variable structure */ $variable )
    {
        $this->HTTPName = $http_name;
        $this->OriginalFilename = $variable["name"];
        $this->Type = $variable["type"];
        $mime = explode( "/", $this->Type );
        $this->MimeCategory = $mime[0];
        $this->MimePart = $mime[1];
        $this->Filename = $variable["tmp_name"];
        $this->Size = $variable["size"];
        $this->IsTemporary = true;
    }

    /*!
     \return the directory where the file should be stored.
    */
    function storageDir( $sub_dir = false )
    {
        $sys = eZSys::instance();
        $storage_dir = $sys->storageDirectory();
        if ( $sub_dir !== false )
            $dir = $storage_dir . "/$sub_dir/" . $this->MimeCategory;
        else
            $dir = $storage_dir . "/" . $this->MimeCategory;
        return $dir;
    }

    /*!
     Stores the temporary file to the destination dir $dir.
    */
    function store( $sub_dir = false, $suffix = false, $mimeData = false )
    {
        if ( !$this->IsTemporary )
        {
            eZDebug::writeError( "Cannot store non temporary file: " . $this->Filename,
                                 "eZHTTPFile" );
            return false;
        }
        $this->IsTemporary = false;

        $ini = eZINI::instance();
//         $storage_dir = $ini->variable( "FileSettings", "VarDir" ) . '/' . $ini->variable( "FileSettings", "StorageDir" );
        $storage_dir = eZSys::storageDirectory();
        if ( $sub_dir !== false )
            $dir = $storage_dir . "/$sub_dir/";
        else
            $dir = $storage_dir . "/";
        if ( $mimeData )
            $dir = $mimeData['dirpath'];

        if ( !$mimeData )
        {
            $dir .= $this->MimeCategory;
        }

        if ( !file_exists( $dir ) )
        {
            if ( !eZDir::mkdir( $dir, false, true ) )
            {
                return false;
            }
        }

        $suffixString = false;
        if ( $suffix != false )
            $suffixString = ".$suffix";

        if ( $mimeData )
        {
            $dest_name = $mimeData['url'];
        }
        else
        {
            $dest_name = $dir . "/" . md5( basename( $this->Filename ) . microtime() . mt_rand() ) . $suffixString;
        }

        if ( !move_uploaded_file( $this->Filename, $dest_name ) )
        {
            eZDebug::writeError( "Failed moving uploaded file " . $this->Filename . " to destination $dest_name" );
            unlink( $dest_name );
            $ret = false;
        }
        else
        {
            $ret = true;
            $this->Filename = $dest_name;
            $perm = $ini->variable( "FileSettings", "StorageFilePermissions" );
            $oldumask = umask( 0 );
            chmod( $dest_name, octdec( $perm ) );
            umask( $oldumask );

            // Write log message to storage.log
            $storageDir = $dir . "/";
            eZLog::writeStorageLog( basename( $this->Filename ), $storageDir );
        }
        return $ret;
    }

    /*!
     \return an array with the attributes for this object.
    */
    function attributes()
    {
        return array( "original_filename",
                      "filename",
                      "filesize",
                      "is_temporary",
                      "mime_type",
                      "mime_type_category",
                      "mime_type_part" );
    }

    /*!
     \return true if the attribute $attr exists
    */
    function hasAttribute( $attr )
    {
        return in_array( $attr, $this->attributes() );
    }

    /*!
     \return the value for the attribute $attr or null if the attribute does not exist.
    */
    function attribute( $attr )
    {
        switch ( $attr )
        {
            case "original_filename":
                return $this->OriginalFilename;
            case "mime_type":
                return $this->Type;
            case "mime_type_category":
                return $this->MimeCategory;
            case "mime_type_part":
                return $this->MimePart;
            case "filename":
                return $this->Filename;
            case "filesize":
                return $this->Size;
            case "is_temporary":
                return $this->IsTemporary;
            default:
            {
                eZDebug::writeError( "Attribute '$attr' does not exist", __METHOD__ );
                return null;
            } break;
        }
    }

    /**
     * Returns whether a file can be fetched.
     *
     * @param string $httpName Name of the file.
     * @param bool|int Whether a maximum file size applies or size in bytes of the maximum allowed.
     *
     * @return bool|int true if the HTTP file $httpName can be fetched. If $maxSize is given,
     * the function returns
     *  eZHTTPFile::UPLOADEDFILE_OK if the file can be fetched,
     *  eZHTTPFile::UPLOADEDFILE_DOES_NOT_EXIST if there has been no file uploaded,
     *  eZHTTPFile::UPLOADEDFILE_EXCEEDS_PHP_LIMIT if the file was uploaded but size
     *     exceeds the upload_max_size limit (set in the PHP configuration),
     *  eZHTTPFile::UPLOADEDFILE_EXCEEDS_MAX_SIZE if the file was uploaded but size
     *     exceeds $maxSize or MAX_FILE_SIZE variable in the form.
     *  eZHTTPFile::UPLOADEDFILE_MISSING_TMP_DIR if the temporary directory is missing
     *  eZHTTPFile::UPLOADEDFILE_CANT_WRITE if the file can't be written
     *  eZHTTPFile::UPLOADEDFILE_UNKNOWN_ERROR if an unknown error occured
     */
    static function canFetch( $httpName, $maxSize = false )
    {
        if ( !isset( $GLOBALS["eZHTTPFile-$httpName"] ) ||
             !( $GLOBALS["eZHTTPFile-$httpName"] instanceof eZHTTPFile ) )
        {
            // A field posted as name[] gives arrays for name, error, size...;
            // there is no single file under $httpName then, which is reported
            // as "no file" (it used to fall through to UPLOADEDFILE_UNKNOWN_ERROR
            // here, and fetch() built an eZHTTPFile out of the arrays).
            $upload = self::singleUpload( $httpName );
            if ( $maxSize === false )
            {
                return $upload !== null and $upload['error'] == 0;
            }

            if ( $upload !== null )
            {
                switch ( (int)$upload['error'] )
                {
                    case ( UPLOAD_ERR_NO_FILE ):
                    {
                        return eZHTTPFile::UPLOADEDFILE_DOES_NOT_EXIST;
                    }break;

                    case ( UPLOAD_ERR_INI_SIZE ):
                    {
                        return eZHTTPFile::UPLOADEDFILE_EXCEEDS_PHP_LIMIT;
                    }break;

                    case ( UPLOAD_ERR_FORM_SIZE ):
                    {
                        return eZHTTPFile::UPLOADEDFILE_EXCEEDS_MAX_SIZE;
                    }break;

                    case ( UPLOAD_ERR_NO_TMP_DIR ):
                    {
                        return eZHTTPFile::UPLOADEDFILE_MISSING_TMP_DIR;
                    }break;

                    case ( UPLOAD_ERR_CANT_WRITE ):
                    {
                        return eZHTTPFile::UPLOADEDFILE_CANT_WRITE;
                    }break;

                    case ( 0 ):
                    {
                        return ( $maxSize == 0 || $upload['size'] <= $maxSize )? eZHTTPFile::UPLOADEDFILE_OK:
                                                                                             eZHTTPFile::UPLOADEDFILE_EXCEEDS_MAX_SIZE;
                    }break;

                    default:
                    {
                        return eZHTTPFile::UPLOADEDFILE_UNKNOWN_ERROR;
                    }
                }
            }
            else
            {
                return eZHTTPFile::UPLOADEDFILE_DOES_NOT_EXIST;
            }
        }
        // The file was fetched already. The two results were swapped here:
        // without $maxSize the caller expects a boolean and got
        // UPLOADEDFILE_OK (0, which reads as "cannot fetch"), with $maxSize it
        // expects a status code and got true. Every caller in the kernel either
        // tests the boolean or compares the status with === against the error
        // codes (eZContentUpload also accepts true), so both keep working.
        if ( $maxSize === false )
            return true;
        else
            return eZHTTPFile::UPLOADEDFILE_OK;
    }

    /**
     * Returns the $_FILES entry of $httpName when it describes exactly one
     * uploaded file (string name that is not empty, scalar error and size),
     * or null otherwise.
     *
     * @param string $httpName
     * @return array|null
     */
    protected static function singleUpload( $httpName )
    {
        if ( !isset( $_FILES[$httpName] ) || !is_array( $_FILES[$httpName] ) )
            return null;
        $upload = $_FILES[$httpName];
        if ( !isset( $upload['name'] ) || !is_string( $upload['name'] ) || $upload['name'] === '' )
            return null;
        if ( !isset( $upload['error'] ) || !is_scalar( $upload['error'] ) )
            return null;
        if ( isset( $upload['size'] ) && !is_scalar( $upload['size'] ) )
            return null;
        if ( isset( $upload['tmp_name'] ) && !is_string( $upload['tmp_name'] ) )
            return null;
        if ( !isset( $upload['size'] ) )
            $upload['size'] = 0;
        return $upload;
    }

    /*!
     Fetches the HTTP file named $http_name and returns a eZHTTPFile object,
     or null if the file could not be fetched.
    */
    static function fetch( $http_name )
    {
        if ( !isset( $GLOBALS["eZHTTPFile-$http_name"] ) ||
             !( $GLOBALS["eZHTTPFile-$http_name"] instanceof eZHTTPFile ) )
        {
            $GLOBALS["eZHTTPFile-$http_name"] = null;

            // See canFetch(): only a single-file entry can become an eZHTTPFile.
            if ( self::singleUpload( $http_name ) !== null )
            {
                $mimeType = eZMimeType::findByURL( $_FILES[$http_name]['name'] );
                $_FILES[$http_name]['type'] = $mimeType['name'];
                $GLOBALS["eZHTTPFile-$http_name"] = new eZHTTPFile( $http_name, $_FILES[$http_name] );
            }
            else
            {
                eZDebug::writeError( "Unknown file for post variable: $http_name",
                                     "eZHTTPFile" );
            }
        }
        return $GLOBALS["eZHTTPFile-$http_name"];
    }

    /*!
     Changes the MIME-Type to $mime.
    */
    function setMimeType( $mime )
    {
        $this->Type = $mime;
        list ( $this->MimeCategory, $this->MimePart ) = explode( '/', $mime, 2 );
    }

    /// The name of the HTTP file
    public $HTTPName;
    /// The original name of the file from the client
    public $OriginalFilename;
    /// The mime type of the file
    public $Type;
    /// The mimetype category (first part)
    public $MimeCategory;
    /// The mimetype type (second part)
    public $MimePart;
    /// The local filename
    public $Filename;
    /// The size of the local file
    public $Size;
    /// Whether the file is a temporary file or if it has been moved(stored).
    public $IsTemporary;
}

?>
