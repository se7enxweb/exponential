<?php
/**
 * File containing the expContentWriter class
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Creates, updates and removes content from plain values, for callers that are not the content module: the REST
 * content API (ezprestapi), import scripts and integrations. It is what ezprestapi used nxc_powercontent for, now in
 * the kernel, so the REST writes need no extra extension.
 *
 * Values are given by attribute identifier, as text:
 *  - rich text (ezxmltext) as HTML, converted with the Online Editor's input parser when ezoe is active (its links and
 *    embeds become relations), else with the simplified XML input parser;
 *  - files (ezimage, ezbinaryfile, ezmedia) as "<http(s) URL>" or "<http(s) URL>|<alternative text or file name>":
 *    the file is downloaded from a public address and stored. A path on the server or an address that resolves to a
 *    private, loopback or reserved network is refused, so a caller cannot make the server read its own files or
 *    reach internal services;
 *  - every other datatype in its text form (eZContentObjectAttribute::fromString()).
 *
 * Every write publishes through the content/publish operation, so workflows, caches, search and notifications run as
 * for an edit in the administration. The caller checks the rights first (expRestContentPermission for REST).
 *
 * Each method answers an array: status (published, pending when a workflow holds the version, failed, invalid),
 * message, object (eZContentObject or null), version (int).
 */
class expContentWriter
{
    /**
     * The datatypes whose value is a file.
     */
    const FILE_DATATYPES = array( 'ezimage', 'ezbinaryfile', 'ezmedia' );

    /**
     * The largest file accepted from a URL, in bytes.
     */
    const MAX_FILE_BYTES = 52428800;

    /**
     * Creates and publishes an object below a node.
     *
     * @param array $params parentNodeID (int), classIdentifier (string), languageLocale (string), attributes
     *                      (identifier => text); optional ownerID (the current user), sectionID (the parent's), remoteID
     * @return array
     */
    public static function create( array $params )
    {
        $parentNode = isset( $params['parentNodeID'] ) ? eZContentObjectTreeNode::fetch( (int)$params['parentNodeID'] ) : null;
        if ( !$parentNode instanceof eZContentObjectTreeNode )
            return self::answer( 'invalid', 'The parent node ' . ( isset( $params['parentNodeID'] ) ? (int)$params['parentNodeID'] : '' ) . ' does not exist.' );
        $class = isset( $params['classIdentifier'] ) ? eZContentClass::fetchByIdentifier( (string)$params['classIdentifier'] ) : null;
        if ( !$class instanceof eZContentClass )
            return self::answer( 'invalid', 'The class ' . ( isset( $params['classIdentifier'] ) ? (string)$params['classIdentifier'] : '' ) . ' does not exist.' );
        $languageCode = isset( $params['languageLocale'] ) ? (string)$params['languageLocale'] : '';
        if ( !eZContentLanguage::fetchByLocale( $languageCode ) instanceof eZContentLanguage )
            return self::answer( 'invalid', 'The language ' . $languageCode . ' is not a language of this installation.' );
        $values = isset( $params['attributes'] ) && is_array( $params['attributes'] ) ? $params['attributes'] : array();
        $classAttributes = array();
        foreach ( $class->fetchAttributes() as $classAttribute )
            $classAttributes[$classAttribute->attribute( 'identifier' )] = $classAttribute->attribute( 'data_type_string' );
        if ( ( $invalid = self::invalidValues( $classAttributes, $values, $class->attribute( 'identifier' ) ) ) !== null )
            return self::answer( 'invalid', $invalid );
        if ( isset( $params['remoteID'] ) && eZContentObject::fetchByRemoteID( (string)$params['remoteID'] ) instanceof eZContentObject )
            return self::answer( 'invalid', 'An object with the remote ID ' . (string)$params['remoteID'] . ' exists already.' );

        $files = self::downloadFiles( $classAttributes, $values );
        if ( isset( $files['error'] ) )
            return self::answer( 'invalid', $files['error'] );

        $db = eZDB::instance();
        $db->begin();
        try
        {
            $ownerID = isset( $params['ownerID'] ) ? (int)$params['ownerID'] : eZUser::currentUserID();
            $sectionID = isset( $params['sectionID'] ) ? (int)$params['sectionID'] : (int)$parentNode->attribute( 'object' )->attribute( 'section_id' );
            $object = $class->instantiate( $ownerID, $sectionID, false, $languageCode );
            if ( isset( $params['remoteID'] ) )
                $object->setAttribute( 'remote_id', (string)$params['remoteID'] );
            $object->store();
            $nodeAssignment = eZNodeAssignment::create( array(
                'contentobject_id' => $object->attribute( 'id' ),
                'contentobject_version' => $object->attribute( 'current_version' ),
                'parent_node' => $parentNode->attribute( 'node_id' ),
                'is_main' => 1,
                'sort_field' => $class->attribute( 'sort_field' ),
                'sort_order' => $class->attribute( 'sort_order' ) ) );
            $nodeAssignment->store();
            $version = $object->version( $object->attribute( 'current_version' ) );
            // a new object's first version is an internal draft, which the publish operation leaves alone
            $version->setAttribute( 'modified', time() );
            $version->setAttribute( 'status', eZContentObjectVersion::STATUS_DRAFT );
            $version->store();
            $versionNumber = (int)$version->attribute( 'version' );
            self::setValues( $object, $version->contentObjectAttributes( $languageCode ), $values, $files );
            $object->commitInputRelations( $versionNumber );
            $object->resetInputRelationList();
        }
        catch ( Throwable $e )
        {
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            self::removeFiles( $files );
            eZDebug::writeError( 'Create below node ' . (int)$parentNode->attribute( 'node_id' ) . ': ' . $e->getMessage(), __METHOD__ );
            return self::answer( 'failed', 'The content could not be created.' );
        }
        $db->commit();
        self::removeFiles( $files );

        return self::publish( $object, $versionNumber, 'Created' );
    }

    /**
     * Publishes a new version of an object with the given values; attributes left out keep theirs. A language the
     * object does not have yet adds that translation.
     *
     * @param eZContentObject $object
     * @param array $values identifier => text
     * @param string|false $languageCode the object's main language when false
     * @return array
     */
    public static function update( eZContentObject $object, array $values, $languageCode = false )
    {
        if ( !$languageCode )
        {
            $initialLanguage = eZContentLanguage::fetch( $object->attribute( 'initial_language_id' ) );
            $languageCode = $initialLanguage instanceof eZContentLanguage ? $initialLanguage->attribute( 'locale' ) : '';
        }
        if ( !eZContentLanguage::fetchByLocale( (string)$languageCode ) instanceof eZContentLanguage )
            return self::answer( 'invalid', 'The language ' . $languageCode . ' is not a language of this installation.', $object );
        if ( !$values )
            return self::answer( 'invalid', 'No attribute values were given; the content is unchanged.', $object );
        $classAttributes = array();
        foreach ( $object->contentObjectAttributes() as $attribute )
            $classAttributes[$attribute->attribute( 'contentclass_attribute_identifier' )] = $attribute->attribute( 'data_type_string' );
        if ( ( $invalid = self::invalidValues( $classAttributes, $values, $object->attribute( 'class_identifier' ) ) ) !== null )
            return self::answer( 'invalid', $invalid, $object );

        $files = self::downloadFiles( $classAttributes, $values );
        if ( isset( $files['error'] ) )
            return self::answer( 'invalid', $files['error'], $object );

        $db = eZDB::instance();
        $db->begin();
        try
        {
            $version = $object->createNewVersion( false, true, $languageCode );
            if ( !$version instanceof eZContentObjectVersion )
            {
                $db->rollback();
                self::removeFiles( $files );
                return self::answer( 'failed', 'No new version could be made.', $object );
            }
            $version->setAttribute( 'modified', time() );
            $version->store();
            $versionNumber = (int)$version->attribute( 'version' );
            self::setValues( $object, $version->contentObjectAttributes( $languageCode ), $values, $files );
            $object->commitInputRelations( $versionNumber );
            $object->resetInputRelationList();
        }
        catch ( Throwable $e )
        {
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            self::removeFiles( $files );
            eZDebug::writeError( 'Update of object ' . (int)$object->attribute( 'id' ) . ': ' . $e->getMessage(), __METHOD__ );
            return self::answer( 'failed', 'The content could not be updated.', $object );
        }
        $db->commit();
        self::removeFiles( $files );

        return self::publish( $object, $versionNumber, 'Updated', (string)$languageCode );
    }

    /**
     * Removes an object with all its locations and what is below them, as content/removeobject does after its checks.
     *
     * @param eZContentObject $object
     * @param bool $moveToTrash
     * @return array
     */
    public static function remove( eZContentObject $object, $moveToTrash = false )
    {
        $nodeIDs = array();
        foreach ( $object->assignedNodes() as $node )
            $nodeIDs[] = (int)$node->attribute( 'node_id' );
        if ( !$nodeIDs )
        {
            $object->purge();
            return self::answer( 'published', 'Removed', $object );
        }
        eZContentObjectTreeNode::removeSubtrees( $nodeIDs, (bool)$moveToTrash );
        return self::answer( 'published', 'Removed', $object );
    }

    /**
     * Why the values cannot be written to a class, or null when they can: an unknown attribute, or a value that is
     * not text.
     *
     * @param array $classAttributes identifier => datatype
     * @param array $values identifier => value
     * @param string $classIdentifier for the message
     * @return string|null
     */
    public static function invalidValues( array $classAttributes, array $values, $classIdentifier )
    {
        $unknown = $notText = array();
        foreach ( $values as $identifier => $value )
        {
            if ( !isset( $classAttributes[$identifier] ) )
                $unknown[] = $identifier;
            else if ( $value !== null && !is_scalar( $value ) )
                $notText[] = $identifier;
        }
        if ( $unknown )
            return 'The class ' . $classIdentifier . ' has no attribute ' . implode( ', ', $unknown ) . '.';
        if ( $notText )
            return 'The value of ' . implode( ', ', $notText ) . ' must be text.';
        return null;
    }

    /**
     * Whether a URL may be fetched for a file value: http or https, with a host whose every address is public (no
     * private, loopback, link-local or reserved range).
     *
     * @param string $url
     * @param callable|null $resolve host => list of IP addresses (gethostbynamel by default; tests pass their own)
     * @return bool
     */
    public static function isPublicHttpUrl( $url, $resolve = null )
    {
        $parts = parse_url( (string)$url );
        if ( !is_array( $parts ) || !isset( $parts['scheme'], $parts['host'] ) || !in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true ) )
            return false;
        if ( isset( $parts['user'] ) || isset( $parts['pass'] ) )
            return false;
        $host = trim( $parts['host'], '[]' );
        if ( filter_var( $host, FILTER_VALIDATE_IP ) )
            $addresses = array( $host );
        else
            $addresses = $resolve ? call_user_func( $resolve, $host ) : gethostbynamel( $host );
        if ( !is_array( $addresses ) || !$addresses )
            return false;
        foreach ( $addresses as $address )
        {
            if ( !filter_var( $address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) )
                return false;
            // older PHP versions do not count every IPv6 loopback, link-local, unique-local or IPv4-mapped address
            if ( filter_var( $address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) )
            {
                $packed = inet_pton( $address );
                if ( $packed === false || $packed === inet_pton( '::1' ) || $packed === inet_pton( '::' ) )
                    return false;
                $first = ord( $packed[0] );
                if ( ( $first & 0xfe ) === 0xfc || ( $first === 0xfe && ( ord( $packed[1] ) & 0xc0 ) === 0x80 ) )
                    return false;
                if ( substr( $packed, 0, 12 ) === str_repeat( "\0", 10 ) . "\xff\xff" )
                    return self::isPublicHttpUrl( 'http://' . inet_ntop( substr( $packed, 12 ) ) . '/', $resolve );
            }
        }
        return true;
    }

    /**
     * Splits a file value into its URL and the text after the first "|" (alternative text or file name).
     *
     * @param string $value
     * @return array url, text
     */
    public static function splitFileValue( $value )
    {
        $parts = explode( '|', trim( (string)$value ), 2 );
        return array( 'url' => trim( $parts[0] ), 'text' => isset( $parts[1] ) ? trim( $parts[1] ) : '' );
    }

    /**
     * Downloads the file values to temporary files.
     *
     * @param array $classAttributes identifier => datatype
     * @param array $values identifier => value
     * @return array identifier => array( path, text ), or array( 'error' => message )
     */
    protected static function downloadFiles( array $classAttributes, array $values )
    {
        $files = array();
        foreach ( $values as $identifier => $value )
        {
            if ( !in_array( $classAttributes[$identifier], self::FILE_DATATYPES, true ) || trim( (string)$value ) === '' )
                continue;
            $file = self::splitFileValue( $value );
            if ( !self::isPublicHttpUrl( $file['url'] ) )
            {
                self::removeFiles( $files );
                return array( 'error' => 'The value of ' . $identifier . ' must be a public http or https address of the file.' );
            }
            $path = self::download( $file['url'] );
            if ( $path === false )
            {
                self::removeFiles( $files );
                return array( 'error' => 'The file of ' . $identifier . ' could not be downloaded (at most ' . ( self::MAX_FILE_BYTES >> 20 ) . ' MB).' );
            }
            $name = basename( (string)parse_url( $file['url'], PHP_URL_PATH ) );
            $files[$identifier] = array( 'path' => $path, 'text' => $file['text'] !== '' ? $file['text'] : $name );
        }
        return $files;
    }

    /**
     * Downloads one URL to a temporary file, without following redirects (a redirect could lead to an internal
     * address), up to MAX_FILE_BYTES.
     *
     * @param string $url
     * @return string|false the path
     */
    protected static function download( $url )
    {
        if ( !function_exists( 'curl_init' ) )
            return false;
        $dir = eZSys::cacheDirectory() . '/expcontentwriter';
        if ( !is_dir( $dir ) )
            eZDir::mkdir( $dir, false, true );
        $extension = strtolower( (string)pathinfo( (string)parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
        $path = $dir . '/' . md5( $url . microtime() . mt_rand() ) . ( preg_match( '/^[a-z0-9]{1,8}$/', $extension ) ? '.' . $extension : '' );
        $out = fopen( $path, 'wb' );
        if ( !$out )
            return false;
        $bytes = 0;
        $curl = curl_init( $url );
        curl_setopt_array( $curl, array(
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FAILONERROR => true,
            CURLOPT_WRITEFUNCTION => function( $curl, $data ) use ( $out, &$bytes )
            {
                $bytes += strlen( $data );
                if ( $bytes > self::MAX_FILE_BYTES )
                    return 0;
                return fwrite( $out, $data );
            } ) );
        $ok = curl_exec( $curl ) !== false;
        if ( PHP_VERSION_ID < 80000 ) curl_close( $curl ); // no effect since PHP 8.0, deprecated in 8.5
        fclose( $out );
        if ( !$ok || $bytes === 0 )
        {
            @unlink( $path );
            return false;
        }
        return $path;
    }

    /**
     * @param array $files from downloadFiles()
     */
    protected static function removeFiles( array $files )
    {
        foreach ( $files as $file )
        {
            if ( is_array( $file ) && isset( $file['path'] ) && is_file( $file['path'] ) )
                @unlink( $file['path'] );
        }
    }

    /**
     * Sets the values on the attributes of a version.
     *
     * @param eZContentObject $object
     * @param eZContentObjectAttribute[] $attributes
     * @param array $values identifier => text
     * @param array $files from downloadFiles()
     */
    protected static function setValues( eZContentObject $object, array $attributes, array $values, array $files )
    {
        foreach ( $attributes as $attribute )
        {
            $identifier = $attribute->attribute( 'contentclass_attribute_identifier' );
            if ( !array_key_exists( $identifier, $values ) )
                continue;
            $datatype = $attribute->attribute( 'data_type_string' );
            if ( in_array( $datatype, self::FILE_DATATYPES, true ) )
            {
                if ( !isset( $files[$identifier] ) )
                    continue;
                $attribute->fromString( $files[$identifier]['path'] . '|' . $files[$identifier]['text'] );
            }
            else if ( $datatype === 'ezxmltext' )
                $attribute->fromString( self::xmlFromHtml( $object, $attribute, (string)$values[$identifier] ) );
            else
                $attribute->fromString( (string)$values[$identifier] );
            $attribute->store();
        }
    }

    /**
     * The stored XML of rich text given as HTML; its links and embeds are recorded as relations of the object.
     *
     * @param eZContentObject $object
     * @param eZContentObjectAttribute $attribute
     * @param string $html
     * @return string
     */
    public static function xmlFromHtml( eZContentObject $object, eZContentObjectAttribute $attribute, $html )
    {
        if ( class_exists( 'eZOEInputParser' ) )
        {
            $parser = new eZOEInputParser();
            $document = $parser->process( '<div>' . trim( $html ) . '</div>' );
            $urlIDs = $parser->getUrlIDArray();
            if ( $urlIDs && class_exists( 'eZOEXMLInput' ) )
                eZOEXMLInput::updateUrlObjectLinks( $attribute, $urlIDs );
            $object->appendInputRelationList( $parser->getLinkedObjectIDArray(), eZContentObject::RELATION_LINK );
            $object->appendInputRelationList( $parser->getEmbeddedObjectIDArray(), eZContentObject::RELATION_EMBED );
        }
        else
        {
            $parser = new eZSimplifiedXMLInputParser( $object->attribute( 'id' ) );
            $parser->setParseLineBreaks( true );
            $document = $parser->process( $html );
        }
        return $document ? eZXMLTextType::domString( $document ) : '';
    }

    /**
     * Publishes a version through the content/publish operation.
     *
     * @param eZContentObject $object
     * @param int $versionNumber
     * @param string $message for a published version
     * @param string $languageCode
     * @return array
     */
    protected static function publish( eZContentObject $object, $versionNumber, $message, $languageCode = '' )
    {
        // outside the content module (a REST call, a script) the module repositories may not be known yet, and the
        // operation handler then finds no content/publish and does nothing
        if ( !eZModule::globalPathList() )
            eZModule::setGlobalPathList( eZModule::activeModuleRepositories() );
        $operation =eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $object->attribute( 'id' ),
                                                                               'version' => (int)$versionNumber ) );
        $object = eZContentObject::fetch( $object->attribute( 'id' ) );
        $published = isset( $operation['status'] ) && $operation['status'] == eZModuleOperationInfo::STATUS_CONTINUE;
        if ( !$published )
            eZLog::write( 'Object ' . (int)$object->attribute( 'id' ) . ' version ' . (int)$versionNumber . ': the publish operation answered status ' . var_export( isset( $operation['status'] ) ? $operation['status'] : null, true ) . ', not published (a workflow holds it, or a step stopped)', 'contentwriter.log' );
        $answer = self::answer( $published ? 'published' : 'pending', $published ? $message : 'Waiting for a workflow', $object, $versionNumber );
        $answer['languageLocale'] = $languageCode;
        return $answer;
    }

    /**
     * @param string $status
     * @param string $message
     * @param eZContentObject|null $object
     * @param int $version
     * @return array
     */
    protected static function answer( $status, $message, $object = null, $version = 0 )
    {
        return array( 'status' => $status, 'message' => $message, 'object' => $object instanceof eZContentObject ? $object : null, 'version' => (int)$version );
    }
}

?>
