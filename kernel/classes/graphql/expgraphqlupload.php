<?php
/**
 * A file sent with a mutation of the GraphQL API by the GraphQL multipart request specification
 * (https://github.com/jaydenseric/graphql-multipart-request-spec): a POST of multipart/form-data with the field
 * operations (the JSON request: query and variables), the field map (which file goes to which variable,
 * {"0": ["variables.file"]}) and the files. readMultipart() puts each file, as an expGraphQLUpload, where map says;
 * the scalar Upload of the schema passes it on to the resolver.
 *
 * Before a resolver stores a file, check() refuses what graphql.ini forbids: a file larger than MaxUploadSize, a
 * file PHP did not receive whole, and what a check of UploadCheckList[] (a virus scanner, a type filter:
 * expGraphQLUploadCheck) refuses. Needs no library and no database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLUpload
{
    /** The largest file accepted when graphql.ini MaxUploadSize does not say: 32 MiB. */
    const MAX_BYTES = 33554432;

    /** @var string the file name the client sent (never used as a path) */
    public $name;

    /** @var string the MIME type the client claimed */
    public $type;

    /** @var string where PHP keeps the file until the request ends (or a file of the installation, uploadFromUrl) */
    public $tmpName;

    /** @var int */
    public $size;

    /** @var int one of PHP's UPLOAD_ERR_* */
    public $error;

    /** @var bool the kernel fetched the file itself (uploadFromUrl): it did not come with the request */
    public $fetched = false;

    /**
     * @param string $name
     * @param string $type
     * @param string $tmpName
     * @param int $size
     * @param int $error
     */
    public function __construct( $name, $type, $tmpName, $size, $error = UPLOAD_ERR_OK )
    {
        $this->name = (string)$name;
        $this->type = (string)$type;
        $this->tmpName = (string)$tmpName;
        $this->size = (int)$size;
        $this->error = (int)$error;
    }

    /**
     * @param array $file an entry of $_FILES (name, type, tmp_name, size, error)
     * @return self|null null when it is not one file
     */
    public static function fromFilesEntry( $file )
    {
        if ( !is_array( $file ) || !isset( $file['tmp_name'] ) || is_array( $file['tmp_name'] ) )
            return null;
        return new self( isset( $file['name'] ) ? $file['name'] : '', isset( $file['type'] ) ? $file['type'] : '',
                         $file['tmp_name'], isset( $file['size'] ) ? $file['size'] : 0, isset( $file['error'] ) ? $file['error'] : UPLOAD_ERR_NO_FILE );
    }

    /**
     * The request of a multipart POST: the JSON of the field operations with every file of map put in its place.
     *
     * @param array $post $_POST (operations, map)
     * @param array $files $_FILES
     * @return array the request (query, variables, operationName, extensions, id), or error (a message)
     */
    public static function readMultipart( array $post, array $files )
    {
        $operations = isset( $post['operations'] ) && is_string( $post['operations'] ) ? json_decode( $post['operations'], true ) : null;
        if ( !is_array( $operations ) || ( $operations && array_keys( $operations ) === range( 0, count( $operations ) - 1 ) ) )
            return array( 'error' => expGraphQLMessage::tr( 'The field operations of a multipart request is not one JSON request.' ) );
        $map = isset( $post['map'] ) && is_string( $post['map'] ) && $post['map'] !== '' ? json_decode( $post['map'], true ) : array();
        if ( !is_array( $map ) )
            return array( 'error' => expGraphQLMessage::tr( 'The field map of a multipart request is not a JSON object.' ) );
        foreach ( $map as $field => $paths )
        {
            $upload = isset( $files[$field] ) ? self::fromFilesEntry( $files[$field] ) : null;
            if ( $upload === null )
                return array( 'error' => expGraphQLMessage::tr( 'The multipart request has no file %field.', array( '%field' => (string)$field ) ) );
            foreach ( (array)$paths as $path )
            {
                $parts = explode( '.', (string)$path );
                if ( count( $parts ) < 2 || $parts[0] !== 'variables' )
                    return array( 'error' => expGraphQLMessage::tr( 'A path of the field map must start with variables.: %path', array( '%path' => (string)$path ) ) );
                array_shift( $parts );
                if ( !isset( $operations['variables'] ) || !is_array( $operations['variables'] ) )
                    $operations['variables'] = array();
                if ( !self::place( $operations['variables'], $parts, $upload ) )
                    return array( 'error' => expGraphQLMessage::tr( 'A path of the field map must start with variables.: %path', array( '%path' => (string)$path ) ) );
            }
        }
        return $operations;
    }

    /**
     * Puts a file at a path of the variables (file, files.0, input.image).
     *
     * @param array $variables
     * @param string[] $parts
     * @param self $upload
     * @return bool
     */
    protected static function place( array &$variables, array $parts, self $upload )
    {
        $here = &$variables;
        $last = array_pop( $parts );
        foreach ( $parts as $part )
        {
            if ( !isset( $here[$part] ) || !is_array( $here[$part] ) )
                $here[$part] = array();
            $here = &$here[$part];
        }
        if ( $last === null || $last === '' )
            return false;
        $here[$last] = $upload;
        return true;
    }

    /**
     * @return int the largest file accepted (graphql.ini MaxUploadSize, in bytes, with k, M or G)
     */
    public static function maxBytes()
    {
        $value = trim( (string)expGraphQL::setting( 'MaxUploadSize', '' ) );
        if ( $value === '' || !preg_match( '/^(\d+)\s*([kmg]?)b?$/i', $value, $m ) )
            return self::MAX_BYTES;
        $bytes = (int)$m[1] * array( '' => 1, 'k' => 1024, 'm' => 1048576, 'g' => 1073741824 )[strtolower( $m[2] )];
        return $bytes > 0 ? $bytes : self::MAX_BYTES;
    }

    /**
     * The file name a stored file gets: the client's name without a directory and with only letters, digits,
     * - _ and . (never empty, never hidden).
     *
     * @return string
     */
    public function safeName()
    {
        $name = basename( str_replace( '\\', '/', $this->name ) );
        $name = preg_replace( '/[^A-Za-z0-9._-]+/', '-', $name );
        $name = ltrim( (string)$name, '.-' );
        return $name !== '' ? substr( $name, -120 ) : 'upload';
    }

    /**
     * Why the file may not be stored, or null: PHP's upload error, MaxUploadSize, and the checks of
     * UploadCheckList[] (expGraphQLUploadCheck), in their order.
     *
     * @param bool $uploaded the file must have come with this request (is_uploaded_file()); false for a file the
     *                       kernel fetched itself (uploadFromUrl) or a test
     * @return string|null
     */
    public function problem( $uploaded = true )
    {
        if ( $this->error !== UPLOAD_ERR_OK )
            return expGraphQLMessage::tr( 'The file %name did not arrive whole (upload error %error).', array( '%name' => $this->safeName(), '%error' => $this->error ) );
        if ( $this->size > self::maxBytes() )
            return expGraphQLMessage::tr( 'The file %name is larger than %max bytes.', array( '%name' => $this->safeName(), '%max' => self::maxBytes() ) );
        if ( !is_file( $this->tmpName ) || ( $uploaded && !is_uploaded_file( $this->tmpName ) ) )
            return expGraphQLMessage::tr( 'The file %name did not arrive whole (upload error %error).', array( '%name' => $this->safeName(), '%error' => UPLOAD_ERR_NO_FILE ) );
        foreach ( expGraphQL::listSetting( 'UploadCheckList' ) as $class )
        {
            if ( !class_exists( $class ) || !is_subclass_of( $class, 'expGraphQLUploadCheck' ) )
            {
                // a check that cannot run refuses: a missing virus scanner must not let every file through
                eZDebug::writeError( "graphql.ini UploadCheckList[]: $class is not a class implementing expGraphQLUploadCheck", __METHOD__ );
                return expGraphQLMessage::tr( 'The file %name could not be checked.', array( '%name' => $this->safeName() ) );
            }
            $check = new $class();
            $reason = $check->check( $this );
            if ( $reason !== null && $reason !== '' )
                return (string)$reason;
        }
        return null;
    }
}

?>
