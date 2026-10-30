<?php
/**
 * File containing the expContentCheckFileFix class.
 *
 * The resolutions of the missing files list (doc/eng-us/features/6.0/content-checks.md, "Resolving"): each for one field
 * version or for many, always planned first (what will happen to each, shown on a confirmation page) and done only
 * with a confirmation token bound to exactly that plan, in batches.
 *
 *   regenerate  make the image's aliases again from its original (missing, stale and never made ones)
 *   restore     copy the file of another version of the same field, which still has it, to the expected path
 *   replace     put an uploaded file at the expected path (same extension; an image must be an image)
 *   remove      remove the dead reference: the field version is empty afterwards (the database only)
 *   review      mark the field version for review (kept in ezsite_data, shown in the list)
 *
 * Every action is planned and done again against the storage at that moment: a file that came back is not
 * overwritten, a field that has no problem any more is left alone. An existing file is never replaced or deleted.
 * Each item needs content/edit on its object (review: content/read), on top of the policy contentcheck/fixfiles
 * the view checks. Each item done or failed is recorded in the audit (content.file.*).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentCheckFileFix
{
    /** The resolutions, in the order they are offered */
    const ACTIONS = array( 'regenerate', 'restore', 'replace', 'remove', 'review' );

    /** The audit event of each resolution */
    const AUDIT_EVENTS = array( 'regenerate' => 'content.file.alias.regenerate',
                                'restore' => 'content.file.restore',
                                'replace' => 'content.file.replace',
                                'remove' => 'content.file.reference.remove',
                                'review' => 'content.file.review.mark' );

    /** Extensions a replacement may never have, whatever the expected path says */
    const REFUSED_EXTENSIONS = array( 'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'pht', 'phps', 'cgi', 'pl',
                                      'py', 'sh', 'asp', 'aspx', 'jsp', 'htaccess', 'html', 'htm', 'shtml', 'xhtml', 'svg', 'svgz', 'js' );

    /**
     * At most this many field versions in one plan.
     *
     * @return int
     */
    public static function maxItems()
    {
        return expContentCheck::intSetting( 'MissingFileSettings', 'MaxFixItems', 200, 1, 1000 );
    }

    /**
     * How many field versions one request works on; the next batch follows with a button (or by itself, see the view).
     *
     * @return int
     */
    public static function batchSize()
    {
        return expContentCheck::intSetting( 'MissingFileSettings', 'FixBatchSize', 10, 1, 200 );
    }

    /**
     * The largest replacement taken, in bytes (0: what PHP takes).
     *
     * @return int
     */
    public static function maxReplaceSize()
    {
        return expContentCheck::intSetting( 'MissingFileSettings', 'MaxReplaceSize', 0, 0, PHP_INT_MAX );
    }

    // ------------------------------------------------------------------ planning

    /**
     * What a resolution would do, field version by field version.
     *
     * @param string $action one of ACTIONS
     * @param array $keys from expContentCheckFiles::keyList()
     * @return array array( 'action', 'items' => list of array( key, object_id, object_name, node_id, field_name,
     *               version, version_status, language, problem, path, aliases, allowed, reason, source_version,
     *               source_path ), 'allowed' => keys, 'too_many' => bool, 'fingerprint' => string )
     *               reason: '' | not_found | not_allowed | fixed | no_original | no_aliases | no_source | not_missing
     */
    public static function plan( $action, array $keys )
    {
        $action = in_array( $action, self::ACTIONS, true ) ? $action : '';
        $max = self::maxItems();
        $tooMany = count( $keys ) > $max;
        $keys = array_slice( $keys, 0, $max, true );
        $found = $action !== '' && $keys ? expContentCheckFiles::inspect( $keys ) : array();
        // an image whose only problem is an alias never made (listed when "Alias never made" is ticked)
        $rest = array_diff_key( $keys, $found );
        if ( $rest && in_array( $action, array( 'regenerate', 'review' ), true ) )
            $found += expContentCheckFiles::inspect( $rest, array( 'alias_absent' ) );
        $items = array();
        $allowed = array();
        foreach ( $keys as $key => $pair )
        {
            $item = self::planItem( $action, $key, $pair, isset( $found[$key] ) ? $found[$key] : array() );
            if ( $item['allowed'] )
                $allowed[] = $key;
            $items[] = $item;
        }
        sort( $allowed );
        return array( 'action' => $action, 'items' => $items, 'allowed' => $allowed, 'too_many' => $tooMany,
                      'fingerprint' => 'files|' . $action . '|' . implode( ',', $allowed ) );
    }

    /**
     * @param string $action
     * @param string $key
     * @param array $pair attribute id, version
     * @param array $rows the field version's problem rows now (inspect())
     * @return array
     */
    protected static function planItem( $action, $key, array $pair, array $rows )
    {
        $item = array( 'key' => $key, 'attribute_id' => (int)$pair[0], 'object_id' => 0, 'object_name' => '', 'node_id' => 0, 'field_name' => '',
                       'version' => (int)$pair[1], 'version_status' => '', 'language' => '', 'datatype' => '', 'problem' => '', 'path' => '',
                       'aliases' => array(), 'modified' => 0, 'file_modified' => 0, 'allowed' => false, 'reason' => '',
                       'source_version' => 0, 'source_path' => '', 'review' => null, 'extension' => '',
                       'upload_field' => 'ReplaceFile_' . str_replace( '-', '_', $key ) );
        if ( $action === '' )
            return self::refused( $item, 'not_found' );
        $info = self::fieldInfo( (int)$pair[0], (int)$pair[1] );
        if ( $info === null )
            return self::refused( $item, 'not_found' );
        $item = array_merge( $item, $info );
        // the missing original first: it is what every other fix needs
        $row = null;
        foreach ( $rows as $candidate )
        {
            if ( $row === null || $candidate['problem'] === 'missing' )
                $row = $candidate;
        }
        if ( $row !== null )
        {
            foreach ( array( 'object_id', 'object_name', 'node_id', 'field_name', 'version_status', 'language', 'datatype', 'problem', 'path',
                             'aliases', 'modified', 'file_modified', 'review' ) as $name )
                $item[$name] = $row[$name];
        }
        $item['extension'] = strtolower( (string)pathinfo( (string)$item['path'], PATHINFO_EXTENSION ) );
        $item['upload_field'] = 'ReplaceFile_' . str_replace( '-', '_', $key );
        if ( $action === 'review' )
        {
            if ( !$info['can_read'] )
                return self::refused( $item, 'not_found' );
            $item['allowed'] = true;
            return $item;
        }
        if ( !$info['can_edit'] )
            return self::refused( $item, 'not_allowed' );
        if ( $row === null )
            return self::refused( $item, 'fixed' );

        switch ( $action )
        {
            case 'regenerate':
                $handler = expContentCheckFiles::handler( $item['datatype'] );
                if ( !$handler || !$handler->hasAliases() )
                    return self::refused( $item, 'no_aliases' );
                if ( $row['problem'] === 'missing' )
                    return self::refused( $item, 'no_original' );
                break;

            case 'restore':
                if ( $row['problem'] !== 'missing' )
                    return self::refused( $item, 'not_missing' );
                $source = self::restoreSource( $item['attribute_id'], $item['version'], $item['datatype'], $item['path'] );
                if ( $source === null )
                    return self::refused( $item, 'no_source' );
                $item['source_version'] = $source['version'];
                $item['source_path'] = $source['path'];
                break;

            case 'replace':
            case 'remove':
                if ( $row['problem'] !== 'missing' )
                    return self::refused( $item, 'not_missing' );
                break;
        }
        $item['allowed'] = true;
        return $item;
    }

    /**
     * The object of a field version and what the current user may do with it.
     *
     * @param int $attributeID
     * @param int $version
     * @return array|null null when there is no such field version or the user may not read its object
     */
    protected static function fieldInfo( $attributeID, $version )
    {
        $attribute = eZContentObjectAttribute::fetch( (int)$attributeID, (int)$version );
        if ( !$attribute instanceof eZContentObjectAttribute )
            return null;
        $object = eZContentObject::fetch( (int)$attribute->attribute( 'contentobject_id' ) );
        if ( !$object instanceof eZContentObject || !$object->canRead() )
            return null;
        return array( 'object_id' => (int)$object->attribute( 'id' ), 'object_name' => (string)$object->attribute( 'name' ),
                      'node_id' => (int)$object->attribute( 'main_node_id' ), 'datatype' => (string)$attribute->attribute( 'data_type_string' ),
                      'language' => (string)$attribute->attribute( 'language_code' ), 'can_read' => true, 'can_edit' => (bool)$object->canEdit() );
    }

    /**
     * Another version of the same field whose file is still there: the published one first, then the newest.
     *
     * @param int $attributeID
     * @param int $version the version that misses its file
     * @param string $datatype
     * @param string $missingPath
     * @return array|null array( 'version' => int, 'path' => string )
     */
    public static function restoreSource( $attributeID, $version, $datatype, $missingPath )
    {
        $handler = expContentCheckFiles::handler( $datatype );
        if ( !$handler )
            return null;
        $rows = eZDB::instance()->arrayQuery( 'SELECT a.id, a.version, a.contentobject_id, a.data_type_string, a.data_text, a.data_int, a.language_code,' .
                                              ' v.status AS version_status FROM ezcontentobject_attribute a, ezcontentobject_version v' .
                                              ' WHERE a.id = ' . (int)$attributeID . ' AND a.version <> ' . (int)$version .
                                              ' AND v.contentobject_id = a.contentobject_id AND v.version = a.version' );
        $rows = is_array( $rows ) ? $rows : array();
        return self::chooseSource( $rows, $handler->files( $rows ), (string)$missingPath, array( 'expContentCheckFiles', 'exists' ) );
    }

    /**
     * Chooses the source of a restore (no database access).
     *
     * @param array $rows the other versions: version, version_status
     * @param array $files key => files of a handler
     * @param string $missingPath
     * @param callable $exists
     * @return array|null array( 'version' => int, 'path' => string )
     */
    public static function chooseSource( array $rows, array $files, $missingPath, $exists )
    {
        usort( $rows, function ( $a, $b ) {
            $aPublished = (int)$a['version_status'] === 1 ? 1 : 0;
            $bPublished = (int)$b['version_status'] === 1 ? 1 : 0;
            return $aPublished !== $bPublished ? $bPublished - $aPublished : (int)$b['version'] - (int)$a['version'];
        } );
        foreach ( $rows as $row )
        {
            $key = expContentCheckFiles::key( $row['id'], $row['version'] );
            if ( empty( $files[$key] ) || $files[$key][0]['role'] !== 'original' )
                continue;
            $path = $files[$key][0]['path'];
            if ( $path !== '' && $path !== $missingPath && $exists( $path ) )
                return array( 'version' => (int)$row['version'], 'path' => $path );
        }
        return null;
    }

    /**
     * @param array $item
     * @param string $reason
     * @return array
     */
    protected static function refused( array $item, $reason )
    {
        $item['allowed'] = false;
        $item['reason'] = (string)$reason;
        return $item;
    }

    // ------------------------------------------------------------------ doing

    /**
     * Plans again and, when the plan is still the one confirmed (the same field versions allowed, for the same user),
     * does the first batch of it.
     *
     * @param string $action
     * @param array $keys from expContentCheckFiles::keyList()
     * @param mixed $confirmToken
     * @param array $uploads key => array( 'tmp' => path, 'name' => original name, 'uploaded' => bool ) for replace
     * @param bool $dryRun plan and check the token, change nothing (the command's --dry-run)
     * @return array array( 'done' => bool, 'reason' => '' | changed | nothing, 'fixed' => keys, 'failed' => key =>
     *               why, 'remaining' => keys still to do, 'plan' => the plan )
     */
    public static function execute( $action, array $keys, $confirmToken, array $uploads = array(), $dryRun = false )
    {
        $plan = self::plan( $action, $keys );
        $outcome = array( 'done' => false, 'reason' => '', 'fixed' => array(), 'failed' => array(), 'remaining' => array(), 'plan' => $plan );
        if ( !$plan['allowed'] )
            return array_merge( $outcome, array( 'reason' => 'nothing' ) );
        if ( !expContentCheck::matches( $confirmToken, expContentCheck::confirmToken( $plan['fingerprint'] ) ) )
            return array_merge( $outcome, array( 'reason' => 'changed' ) );
        $batch = self::batchSize();
        foreach ( $plan['items'] as $item )
        {
            if ( !$item['allowed'] )
                continue;
            if ( $batch <= 0 )
            {
                $outcome['remaining'][] = $item['key'];
                continue;
            }
            $batch--;
            if ( $dryRun )
            {
                $outcome['fixed'][] = $item['key'];
                continue;
            }
            $why = self::run( $action, $item, isset( $uploads[$item['key']] ) ? $uploads[$item['key']] : null );
            if ( $why === '' )
                $outcome['fixed'][] = $item['key'];
            else
                $outcome['failed'][$item['key']] = $why;
            self::audit( $action, $item, $why );
        }
        $outcome['done'] = count( $outcome['fixed'] ) > 0;
        // what is missing changed: the next list walks the objects again
        if ( $outcome['done'] && !$dryRun )
            expContentCheckFiles::clearListCache();
        return $outcome;
    }

    /**
     * Does one resolution.
     *
     * @param string $action
     * @param array $item an allowed item of plan()
     * @param array|null $upload for replace
     * @return string '' when done, else why not: no_file | wrong_type | too_large | exists | write | failed
     */
    public static function run( $action, array $item, $upload = null )
    {
        try
        {
            switch ( $action )
            {
                case 'regenerate':
                    $why = self::regenerate( $item );
                    break;
                case 'restore':
                    $why = self::restore( $item );
                    break;
                case 'replace':
                    $why = self::replace( $item, $upload );
                    break;
                case 'remove':
                    $why = self::removeReference( $item );
                    break;
                case 'review':
                    $why = self::markForReview( $item );
                    break;
                default:
                    $why = 'failed';
            }
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'Content checks: ' . $action . ' of field ' . $item['key'] . ' failed: ' . $e->getMessage(), __METHOD__ );
            $why = 'failed';
        }
        if ( $why === '' && $action !== 'review' )
        {
            eZContentCacheManager::clearContentCacheIfNeeded( (int)$item['object_id'] );
            // a review mark is about a problem; the problem is gone
            self::clearReview( $item['key'] );
        }
        return $why;
    }

    /**
     * Makes the aliases of an image field version again from its original: those the field's XML names and those
     * the list found absent. The alias entries go from the XML, then each alias is made as the image datatype makes
     * it when first shown (eZImageAliasHandler::imageAlias()), which writes the file at its path and stores the XML.
     *
     * @param array $item
     * @return string
     */
    protected static function regenerate( array $item )
    {
        $attribute = eZContentObjectAttribute::fetch( (int)$item['attribute_id'], (int)$item['version'] );
        if ( !$attribute instanceof eZContentObjectAttribute )
            return 'failed';
        $files = expContentCheckFileImage::filesOfXML( (string)$attribute->attribute( 'data_text' ) );
        if ( !$files || !expContentCheckFiles::exists( $files[0]['path'] ) )
            return 'no_file';
        $names = array();
        foreach ( array_slice( $files, 1 ) as $file )
            $names[$file['alias']] = true;
        foreach ( $item['aliases'] as $alias )
            $names[$alias['alias']] = true;
        $defined = expContentCheckFiles::aliasKeys();
        $names = array_values( array_intersect( array_keys( $names ), array_keys( $defined ) ) );
        if ( !$names )
            return '';

        $xml = self::withoutAliases( (string)$attribute->attribute( 'data_text' ), $names );
        if ( $xml === null )
            return 'failed';
        $attribute->setAttribute( 'data_text', $xml );
        $attribute->storeData();

        $fresh = eZContentObjectAttribute::fetch( (int)$item['attribute_id'], (int)$item['version'] );
        $handler = $fresh instanceof eZContentObjectAttribute ? $fresh->content() : null;
        if ( !$handler instanceof eZImageAliasHandler )
            return 'failed';
        $made = 0;
        foreach ( $names as $name )
        {
            $alias = $handler->imageAlias( $name );
            if ( is_array( $alias ) && !empty( $alias['url'] ) )
            {
                expContentCheckFiles::forget( $alias['url'] );
                $made++;
            }
        }
        return $made > 0 ? '' : 'failed';
    }

    /**
     * An image field's XML without the alias entries of some names.
     *
     * @param string $xml
     * @param string[] $names
     * @return string|null null when the XML cannot be read
     */
    public static function withoutAliases( $xml, array $names )
    {
        $doc = new DOMDocument( '1.0', 'utf-8' );
        $useErrors = libxml_use_internal_errors( true );
        $loaded = $doc->loadXML( (string)$xml, LIBXML_NONET );
        libxml_clear_errors();
        libxml_use_internal_errors( $useErrors );
        if ( !$loaded )
            return null;
        foreach ( iterator_to_array( $doc->getElementsByTagName( 'alias' ) ) as $node )
        {
            if ( in_array( (string)$node->getAttribute( 'name' ), $names, true ) )
                $node->parentNode->removeChild( $node );
        }
        return $doc->saveXML();
    }

    /**
     * Copies the file of another version to the expected path; an image then gets its aliases again.
     *
     * @param array $item
     * @return string
     */
    protected static function restore( array $item )
    {
        $target = (string)$item['path'];
        $source = (string)$item['source_path'];
        expContentCheckFiles::forget( $target );
        expContentCheckFiles::forget( $source );
        if ( $target === '' || $source === '' || !expContentCheckFiles::exists( $source ) )
            return 'no_file';
        if ( expContentCheckFiles::exists( $target ) )
            return 'exists';
        if ( !self::storagePath( $target ) )
            return 'write';
        $cluster = eZClusterFileHandler::instance();
        if ( $cluster instanceof eZFSFileHandler && !eZDir::mkdir( dirname( $target ), false, true ) && !is_dir( dirname( $target ) ) )
            return 'write';
        $cluster->fileCopy( $source, $target );
        expContentCheckFiles::forget( $target );
        if ( !expContentCheckFiles::exists( $target ) )
            return 'write';
        self::afterFileWritten( $item );
        return '';
    }

    /**
     * Puts an uploaded file at the expected path.
     *
     * @param array $item
     * @param array|null $upload tmp, name, uploaded
     * @return string
     */
    protected static function replace( array $item, $upload )
    {
        $target = (string)$item['path'];
        if ( !is_array( $upload ) || empty( $upload['tmp'] ) || !is_file( (string)$upload['tmp'] ) )
            return 'no_file';
        $tmp = (string)$upload['tmp'];
        if ( !empty( $upload['uploaded'] ) && !is_uploaded_file( $tmp ) )
            return 'no_file';
        $why = self::checkReplacement( $target, isset( $upload['name'] ) ? (string)$upload['name'] : '', (int)filesize( $tmp ),
                                       $item['datatype'] === 'ezimage' ? self::sniffMime( $tmp ) : '' , $item['datatype'] === 'ezimage' );
        if ( $why !== '' )
            return $why;
        expContentCheckFiles::forget( $target );
        if ( expContentCheckFiles::exists( $target ) )
            return 'exists';
        if ( !self::storagePath( $target ) )
            return 'write';
        if ( !eZDir::mkdir( dirname( $target ), false, true ) && !is_dir( dirname( $target ) ) )
            return 'write';
        if ( !copy( $tmp, $target ) )
            return 'write';
        $perm = eZINI::instance()->variable( 'FileSettings', 'StorageFilePermissions' );
        @chmod( $target, eZFile::fileMode( octdec( $perm ) ) );
        // a cluster takes the local file into its storage; the local file system needs nothing more
        eZClusterFileHandler::instance()->fileStore( $target, $item['datatype'] === 'ezimage' ? 'image' : 'binaryfile', false );
        expContentCheckFiles::forget( $target );
        if ( !expContentCheckFiles::exists( $target ) )
            return 'write';
        self::afterFileWritten( $item );
        return '';
    }

    /**
     * Whether a file may replace a missing one (no file access).
     *
     * @param string $target the expected path
     * @param string $name the name the file was uploaded with
     * @param int $size
     * @param string $mime what the file's contents are (images only; '' otherwise)
     * @param bool $mustBeImage
     * @return string '' | no_file | wrong_type | too_large
     */
    public static function checkReplacement( $target, $name, $size, $mime, $mustBeImage )
    {
        if ( (int)$size <= 0 )
            return 'no_file';
        $max = self::maxReplaceSize();
        if ( $max > 0 && (int)$size > $max )
            return 'too_large';
        $want = strtolower( (string)pathinfo( (string)$target, PATHINFO_EXTENSION ) );
        $have = strtolower( (string)pathinfo( (string)$name, PATHINFO_EXTENSION ) );
        if ( $want === '' || $have !== $want || in_array( $want, self::REFUSED_EXTENSIONS, true ) )
            return 'wrong_type';
        if ( $mustBeImage && ( strpos( (string)$mime, 'image/' ) !== 0 || stripos( (string)$mime, 'svg' ) !== false ) )
            return 'wrong_type';
        return '';
    }

    /**
     * @param string $path
     * @return string the mime type the contents say
     */
    protected static function sniffMime( $path )
    {
        if ( function_exists( 'getimagesize' ) )
        {
            $info = @getimagesize( $path );
            if ( is_array( $info ) && !empty( $info['mime'] ) )
                return (string)$info['mime'];
        }
        return '';
    }

    /**
     * Whether a path is one the storage may be written at: below the storage directory, no "..".
     *
     * @param string $path
     * @param string|null $storageDirectory null: eZSys::storageDirectory()
     * @return bool
     */
    public static function storagePath( $path, $storageDirectory = null )
    {
        $path = (string)$path;
        $storageDirectory = rtrim( $storageDirectory === null ? eZSys::storageDirectory() : (string)$storageDirectory, '/' ) . '/';
        if ( $storageDirectory === '/' || strpos( $path, $storageDirectory ) !== 0 )
            return false;
        return !preg_match( '#(^|/)\.\.?(/|$)#', substr( $path, strlen( $storageDirectory ) ) ) && strpos( $path, "\0" ) === false;
    }

    /**
     * After an original came back: an image gets its aliases again, as they may be older than the file now.
     *
     * @param array $item
     */
    protected static function afterFileWritten( array $item )
    {
        $handler = expContentCheckFiles::handler( $item['datatype'] );
        if ( $handler && $handler->hasAliases() )
            self::regenerate( $item );
    }

    /**
     * Empties the field version (database only).
     *
     * @param array $item
     * @return string
     */
    protected static function removeReference( array $item )
    {
        expContentCheckFiles::forget( $item['path'] );
        if ( expContentCheckFiles::exists( $item['path'] ) )
            return 'exists';
        $handler = expContentCheckFiles::handler( $item['datatype'] );
        $attribute = eZContentObjectAttribute::fetch( (int)$item['attribute_id'], (int)$item['version'] );
        if ( !$handler || !$attribute instanceof eZContentObjectAttribute )
            return 'failed';
        $db = eZDB::instance();
        $db->begin();
        $ok = $handler->removeReference( $attribute );
        $db->commit();
        if ( !$ok )
            return 'failed';
        if ( $item['version_status'] === 'published' )
            eZContentOperationCollection::registerSearchObject( (int)$item['object_id'] );
        return '';
    }

    /**
     * Marks a field version for review.
     *
     * @param array $item
     * @return string
     */
    protected static function markForReview( array $item )
    {
        $user = eZUser::currentUser();
        $value = json_encode( array( 'user_id' => $user instanceof eZUser ? (int)$user->attribute( 'contentobject_id' ) : 0,
                                     'time' => time(), 'problem' => (string)$item['problem'], 'path' => (string)$item['path'] ) );
        eZSiteData::createNew( expContentCheckFiles::reviewName( $item['key'] ), $value );
        return '';
    }

    /**
     * @param string $key
     */
    public static function clearReview( $key )
    {
        if ( preg_match( '/^[0-9]{1,10}-[0-9]{1,6}$/', (string)$key ) )
            eZDB::instance()->query( "DELETE FROM ezsite_data WHERE name = '" . expContentCheckFiles::reviewName( $key ) . "'" );
    }

    /**
     * Records one item in the audit.
     *
     * @param string $action
     * @param array $item
     * @param string $why '' when done
     */
    protected static function audit( $action, array $item, $why )
    {
        if ( !class_exists( 'expAudit' ) || !isset( self::AUDIT_EVENTS[$action] ) )
            return;
        $data = array( 'object' => array( 'type' => 'object', 'id' => (int)$item['object_id'] ),
                       'target' => array( 'type' => 'file', 'id' => (string)$item['path'] ),
                       'after' => array( 'attribute_id' => (int)$item['attribute_id'], 'version' => (int)$item['version'],
                                         'language' => (string)$item['language'], 'problem' => (string)$item['problem'] ),
                       'result' => $why === '' ? 'success' : 'failure' );
        if ( $action === 'restore' )
            $data['before'] = array( 'source_version' => (int)$item['source_version'], 'source_path' => (string)$item['source_path'] );
        if ( $action === 'regenerate' && $item['aliases'] )
            $data['after']['aliases'] = array_column( $item['aliases'], 'alias' );
        if ( $why !== '' )
            $data['reason'] = $why;
        expAudit::event( self::AUDIT_EVENTS[$action], $data );
    }
}

?>
