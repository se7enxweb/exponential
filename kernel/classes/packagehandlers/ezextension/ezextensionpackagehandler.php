<?php
/**
 * File containing the eZExtensionPackageHandler class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZExtensionPackageHandler ezextensionpackagehandler.php
  \brief Handles extenstions in the package system

*/

class eZExtensionPackageHandler extends eZPackageHandler
{
    public $HandlerType;
    const ERROR_EXISTS = 1;

    const ACTION_REPLACE = 1;
    const ACTION_SKIP = 2;

    public function __construct()
    {
        parent::__construct( 'ezextension', array( 'extract-install-content' => true ) );
    }

    /*!
     Returns an explanation for the extension install item.
    */
    function explainInstallItem( $package, $installItem, $requestedInfo = array() )
    {
        if ( $installItem['filename'] )
        {
            $filename = $installItem['filename'];
            $subdirectory = $installItem['sub-directory'];
            if ( $subdirectory )
                $filepath = $subdirectory . '/' . $filename . '.xml';
            else
                $filepath = $filename . '.xml';

            $filepath = $package->path() . '/' . $filepath;

            $dom = $package->fetchDOMFromFile( $filepath );
            if ( $dom )
            {
                $root = $dom->documentElement;
                $extensionName = $root->getAttribute( 'name' );
                return array( 'description' => ezpI18n::tr( 'kernel/package', 'Extension \'%extensionname\'', false,
                                                       array( '%extensionname' => $extensionName ) ) );
            }
        }
    }

    /*!
     Uninstalls extensions.
    */
    function uninstall( $package, $installType, $parameters,
                      $name, $os, $filename, $subdirectory,
                      $content, &$installParameters,
                      &$installData )
    {
        $extensionName = $content->getAttribute( 'name' );

        $extensionDir = eZExtension::extensionPath( $extensionName );

        // TODO: don't delete modified files?

        if ( $extensionDir !== false && file_exists( $extensionDir ) )
            eZDir::recursiveDelete( $extensionDir );

        // Deactivate extension
        $siteINI = eZINI::instance( 'site.ini', 'settings/override', null, null, false, true );
        $selectedExtensions = $siteINI->variable( 'ExtensionSettings', "ActiveExtensions" );

        if ( in_array( $extensionName, $selectedExtensions ) )
        {
            $extensionsFlipped = array_flip( $selectedExtensions );

            $extKey = $extensionsFlipped[$extensionName];
            unset( $selectedExtensions[$extKey] );

            $siteINI->setVariable( "ExtensionSettings", "ActiveExtensions", $selectedExtensions );
            $siteINI->save( 'site.ini.append', '.php', false, false );
        }

        // Regenerate the autoloads to remove of no longer existing classes
        ezpAutoloader::updateExtensionAutoloadArray();

        return true;
    }

    /*!
     Copy extension from the package to extension repository.
    */
    function install( $package, $installType, $parameters,
                      $name, $os, $filename, $subdirectory,
                      $content, &$installParameters,
                      &$installData )
    {
        //$this->Package =& $package;

        $trans = eZCharTransform::instance();
        $name = $content->getAttribute( 'name' );
        $extensionName = $trans->transformByGroup( $name, 'urlalias' );
        if ( strcmp( $name, $extensionName ) !== 0 )
        {
            $description = ezpI18n::tr( 'kernel/package', 'Package contains an invalid extension name: %extensionname', false, array( '%extensionname' => $name ) );
            $installParameters['error'] = array( 'error_code' => false,
                                                 'element_id' => $name,
                                                 'description' => $description );
            return false;
        }

        $extensionRootDir = eZExtension::baseDirectory();
        $extensionDir = $extensionRootDir . '/' . $extensionName;
        $existingExtensionPath = eZExtension::extensionPath( $extensionName );
        $packageExtensionDir = $package->path() . '/' . $parameters['sub-directory'] . '/' . $extensionName;

        // Error: extension already exists.
        if ( $existingExtensionPath !== false && file_exists( $existingExtensionPath ) )
        {
            $description = ezpI18n::tr( 'kernel/package', "Extension '%extensionname' already exists.",
                                   false, array( '%extensionname' => $extensionName ) );
            $choosenAction = $this->errorChoosenAction( self::ERROR_EXISTS,
                                                        $installParameters, $description, $this->HandlerType );
            switch( $choosenAction )
            {
            case self::ACTION_SKIP:
                return true;

            case eZPackage::NON_INTERACTIVE:
            case self::ACTION_REPLACE:
                eZDir::recursiveDelete( $existingExtensionPath );
                if ( $existingExtensionPath !== $extensionDir && file_exists( $extensionDir ) )
                {
                    eZDir::recursiveDelete( $extensionDir );
                }
                break;

            default:
                $installParameters['error'] = array( 'error_code' => self::ERROR_EXISTS,
                                                     'element_id' => $extensionName,
                                                     'description' => $description,
                                                     'actions' => array( self::ACTION_REPLACE => ezpI18n::tr( 'kernel/package', "Replace extension" ),
                                                                         self::ACTION_SKIP => ezpI18n::tr( 'kernel/package', 'Skip' ) ) );
                return false;
            }
        }

        eZDir::mkdir( $extensionDir, false, true );
        eZDir::copy( $packageExtensionDir, $extensionRootDir );

        // Regenerate autoloads for extensions to pick up the newly created extension
        ezpAutoloader::updateExtensionAutoloadArray();

        // Activate extension
        $siteINI = eZINI::instance( 'site.ini', 'settings/override', null, null, false, true );

        if ( $siteINI->hasVariable( 'ExtensionSettings', "ActiveExtensions" ) )
        {
            $selectedExtensions = $siteINI->variable( 'ExtensionSettings', "ActiveExtensions" );
        }
        else
        {
            $selectedExtensions = array();
        }

        if ( !in_array( $extensionName, $selectedExtensions ) )
        {
            $selectedExtensions[] = $extensionName;
            $siteINI->setVariable( "ExtensionSettings", "ActiveExtensions", $selectedExtensions );
            $siteINI->save( 'site.ini.append', '.php', false, false );
        }
        return true;
    }

    function add( $packageType, $package, $cli, $parameters )
    {
        foreach ( $parameters as $extensionName )
        {
            $cli->output( 'adding extension ' . $cli->stylize( 'dir', $extensionName ) );
            $this->addExtension( $package, $extensionName );
        }
    }

    /** Files that hold key material, anywhere in an extension: by name, any case */
    const KEY_FILE_PATTERN = '/(\.(pem|key|p12|pfx|jks|keystore|kdbx|crt|der|p8|ppk|gpg|asc)$|\.(pem|key|p12|pfx)\.|^\.env(\.(?!(example|sample|template)$).*)?$|^id_(rsa|dsa|ecdsa|ed25519)|^\.htpasswd$|^\.netrc$|^\.pgpass$|^credentials|service[-_]?account)/i';

    /** Directories whose files are key material, anywhere in an extension, any case */
    const KEY_DIRECTORY_PATTERN = '/(^(jwt|\.ssh|\.gnupg)$|\.(pem|key)$)/i';

    /** Copies and backups of settings files: never packed, whatever they hold */
    const BACKUP_FILE_PATTERN = '/(\.(bak|orig|dist|old|save|swp|swo|tmp|rej|backup)$|~$)/i';

    /** A file whose text is read for secrets is at most this big; a bigger one is refused, not skipped */
    const MAX_SCAN_BYTES = 4194304;

    /** An extension with more entries than this is refused, not partly checked */
    const MAX_ENTRIES = 200000;

    /**
     * Whether $extensionName names an extension directory that can be packed: a plain name (no path), present below
     * the extension directory.
     *
     * @param string $extensionName
     * @return bool
     */
    static function isPackableName( $extensionName )
    {
        $extensionName = (string)$extensionName;
        if ( !preg_match( '/^[A-Za-z0-9][A-Za-z0-9_.\-]*$/', $extensionName ) )
            return false;
        $dir = eZExtension::baseDirectory() . '/' . $extensionName;
        return is_dir( $dir ) && !is_link( $dir );
    }

    /**
     * Every entry below $dir, breadth first as eZDir::copy() walks, with what the package takes of it: a file or a
     * directory whose name and parents do not start with "." (eZDir::copy() without hidden items) and that is not
     * a symbolic link (eZDir::findSubitems() without "l"); a link is listed, not followed. addExtension() copies
     * exactly the entries marked 'packed', so the check and the package see the same files.
     *
     * @param string $dir
     * @return array relative path => array( 'type' => file|dir|link|other, 'packed' => bool ), parents first
     * @throws RuntimeException a directory that cannot be read, or too many entries
     */
    static function packEntries( $dir )
    {
        $dir = rtrim( (string)$dir, '/' );
        $entries = array();
        $queue = array( array( '', false ) );
        while ( $queue )
        {
            list( $relative, $hidden ) = array_shift( $queue );
            $absolute = $relative === '' ? $dir : $dir . '/' . $relative;
            $handle = @opendir( $absolute );
            if ( $handle === false )
                throw new RuntimeException( "unreadable directory: " . ( $relative === '' ? '.' : $relative ) );
            $names = array();
            while ( ( $name = readdir( $handle ) ) !== false )
            {
                if ( $name !== '.' && $name !== '..' )
                    $names[] = $name;
            }
            closedir( $handle );
            sort( $names, SORT_STRING );
            foreach ( $names as $name )
            {
                $path = $relative === '' ? $name : $relative . '/' . $name;
                $isHidden = $hidden || $name[0] === '.';
                $full = $absolute . '/' . $name;
                if ( is_link( $full ) )
                    $entries[$path] = array( 'type' => 'link', 'packed' => false );
                else if ( is_dir( $full ) )
                {
                    $entries[$path] = array( 'type' => 'dir', 'packed' => !$isHidden );
                    $queue[] = array( $path, $isHidden );
                }
                else if ( is_file( $full ) )
                    $entries[$path] = array( 'type' => 'file', 'packed' => !$isHidden );
                else
                    $entries[$path] = array( 'type' => 'other', 'packed' => false );
                if ( count( $entries ) > self::MAX_ENTRIES )
                    throw new RuntimeException( 'more than ' . self::MAX_ENTRIES . ' entries' );
            }
        }
        return $entries;
    }

    /** Secrets by their shape, in any text: private keys, bearer tokens and the token formats of common services */
    const SECRET_SHAPE_PATTERNS = array(
        'private key' => '/-----BEGIN [A-Z0-9 ]*PRIVATE KEY( BLOCK)?-----/',
        'bearer token' => '/\bAuthorization\s*[:=]\s*["\']?Bearer\s+[A-Za-z0-9\-._~+\/]{8,}/i',
        'API key' => '/\b(sk-(proj-|live-|test-)?[A-Za-z0-9_\-]{20,}|AKIA[0-9A-Z]{16}|ASIA[0-9A-Z]{16}|AIza[0-9A-Za-z_\-]{35}|gh[pousr]_[A-Za-z0-9]{30,}|github_pat_[A-Za-z0-9_]{30,}|xox[abprs]-[A-Za-z0-9\-]{10,}|glpat-[A-Za-z0-9_\-]{20,}|sk_live_[A-Za-z0-9]{20,}|rk_live_[A-Za-z0-9]{20,})/',
        'constant' => '/\bdefine\s*\(\s*["\'][A-Za-z0-9_]*(KEY|SECRET|TOKEN|PASS|PASSWORD|PWD|CREDENTIAL)[A-Za-z0-9_]*["\']\s*,\s*["\'][^"\']+["\']/i',
        'address with a password' => '#[a-z][a-z0-9+.\-]*://[^/\s:@"\']+:[^@\s/"\']+@#i',
    );

    /** Text files whose lines are read as "name = value" or "name: value" pairs, besides settings files */
    const CONFIG_FILE_PATTERN = '/(\.(ya?ml|conf|cfg|cnf|properties|toml|env|neon)$|^\.env)/i';

    /**
     * Whether $value is only a placeholder as a whole: "...", "…", "xxx" (three or more), "***" (three or more),
     * "<...>" or "<…>". Anything else under a secret name is taken for a secret. Pure.
     *
     * @param string $value
     * @return bool
     */
    static function isPlaceholder( $value )
    {
        return preg_match( '/^(\.\.\.|\x{2026}|[xX]{3,}|\*{3,}|<\.\.\.>|<\x{2026}>)$/u', trim( (string)$value ) ) === 1;
    }

    /**
     * Whether a setting whose name only looks like a secret is a measure or a switch: a number (at most nine digits,
     * maybe with decimals, maybe followed by words) under a name that says it measures (MaxTokens, TokenLength,
     * PasswordLifeTime, CharsPerToken ...), or a
     * switch (enabled, disabled, true, false) under a name that says it switches (AllowSessionAuth, RequireToken,
     * IEDisableWithPassword ...). "Password=12345678", "ApiKey=12345" and "Password=true" are none of these. Pure.
     *
     * @param string $name
     * @param string $value
     * @return bool
     */
    static function isMeasureOrSwitch( $name, $value )
    {
        $name = (string)$name;
        $value = trim( (string)$value );
        if ( preg_match( '/^\d{1,9}(\.\d+)?(\s|$)/', $value ) )
            return preg_match( '/(tokens|length|lifetime|budget|count|size|limit|timeout|ttl|^max|^min|^estimate|per_?token)/i', $name ) === 1;
        if ( preg_match( '/^(enabled|disabled|true|false)$/i', $value ) )
            return preg_match( '/(^(allow|use|require|enable|disable|is|has|show|check)|(enabled|required|disable)|with)/i', $name ) === 1
                && preg_match( '/^(allow|use|require|enable|disable|is|has|show|check|ie)/i', $name ) === 1;
        return false;
    }

    /**
     * Whether the pair $name = $value holds a secret: under a secret name (expSystemReportMask::isSecretSettingName())
     * every value but an empty one, a placeholder (isPlaceholder()) or a measure or switch its name announces
     * (isMeasureOrSwitch()); other numbers and switches count as secrets; under another
     * name a value that carries a secret inline (expSecretRule::hasInlineSecret()) or has the shape of one. Pure.
     *
     * @param string $name
     * @param string $value
     * @return bool
     */
    static function isSecretPair( $name, $value )
    {
        $value = trim( preg_replace( '#\s*\*/\s*(\?>)?\s*$#', '', (string)$value ) );
        $value = trim( rtrim( $value, ',;' ) );
        $value = trim( $value, " \t\"'" );
        if ( $value === '' )
            return false;
        if ( expSystemReportMask::isSecretSettingName( $name ) )
            return !self::isPlaceholder( $value ) && !self::isMeasureOrSwitch( $name, $value );
        if ( expSecretRule::hasInlineSecret( $value ) )
            return true;
        foreach ( self::SECRET_SHAPE_PATTERNS as $pattern )
            if ( preg_match( $pattern, $value ) )
                return true;
        return false;
    }

    /**
     * The secrets in the raw text of a file, by the names they stand under or by their kind, without their values.
     *
     * Every text: SECRET_SHAPE_PATTERNS (a private key, "Authorization: Bearer ...", service tokens such as sk-...,
     * AKIA..., ghp_..., xoxb-..., define( '..._KEY', '...' ), an address with a password).
     * $pairs 'ini' (settings files): every "name = value" in every line, read as text, not as eZINI reads it, so
     * nothing eZINI skips or reads differently is missed (commented lines and the PHP comment wrapper, Key[]= and
     * Key[name]=, blanks around "=", quotes, a byte order mark, CRLF or CR, duplicates, several pairs on a line),
     * judged by isSecretPair(), and outside comments a line carrying a secret inline ("password: ...").
     * $pairs 'config' (CONFIG_FILE_PATTERN: YAML, .env, .conf, .properties ...): the same with "name: value" and quoted
     * names as well, and a secret name in YAML whose value stands on the next line.
     * Text in UTF-16 or UTF-32 is not read: it is reported as such. Pure.
     *
     * @param string $text
     * @param string|false $pairs 'ini', 'config' or false
     * @return string[] names, or kinds in parentheses
     */
    static function secretsInText( $text, $pairs = 'ini' )
    {
        $text = (string)$text;
        if ( preg_match( '/^(\xFF\xFE|\xFE\xFF|\x00\x00\xFE\xFF)/', $text ) )
            return array( '(text in UTF-16 or UTF-32)' );
        $text = preg_replace( '/^\xEF\xBB\xBF/', '', $text );
        $found = array();
        foreach ( self::SECRET_SHAPE_PATTERNS as $kind => $pattern )
        {
            if ( preg_match( $pattern, $text ) )
                $found['(' . $kind . ')'] = true;
        }
        if ( $pairs === false )
            return array_keys( $found );

        $pairPattern = $pairs === 'config'
            ? '/["\']?([A-Za-z0-9_.\-]+)["\']?\s*(?:\[[^\]\r\n]*\])?\s*(?:=(?![=>])|:(?![:\/]))([^\r\n]*)/'
            : '/([A-Za-z0-9_.\-]+)\s*(?:\[[^\]\r\n]*\])?\s*=(?![=>])([^\r\n]*)/';
        $lines = preg_split( '/\r\n|\r|\n/', $text );
        foreach ( $lines as $i => $line )
        {
            $isComment = preg_match( '/^\s*(#|;|\/\/|\*)/', $line ) === 1;
            if ( preg_match_all( $pairPattern, $line, $matches, PREG_SET_ORDER ) )
            {
                foreach ( $matches as $m )
                {
                    if ( self::isSecretPair( $m[1], $m[2] ) )
                        $found[$m[1]] = true;
                    // YAML: "password:" with its value on the next, deeper line
                    else if ( $pairs === 'config' && ( trim( $m[2] ) === '' || preg_match( '/^\s*[|>][\-+]?\s*$/', $m[2] ) ) )
                    {
                        if ( expSystemReportMask::isSecretSettingName( $m[1] ) && isset( $lines[$i + 1] )
                             && preg_match( '/^(\s+)(\S.*)$/', $lines[$i + 1], $next ) && !self::isPlaceholder( trim( $next[2], " \t\"'" ) )
                             && !preg_match( '/^\s*[A-Za-z0-9_.\-]+\s*:/', $lines[$i + 1] ) )
                            $found[$m[1]] = true;
                    }
                }
            }
            else if ( !$isComment && expSecretRule::hasInlineSecret( $line ) )
                $found['(inline)'] = true;
        }
        return array_keys( $found );
    }

    /**
     * What in the extension directory $dir must not leave the server in a package, one line each, without a value.
     * Every entry of packEntries() is checked, the packed ones and the others (hidden files, links) alike:
     *  - key material anywhere, by name in any case: KEY_FILE_PATTERN (*.pem, *.key, *.p12, *.pfx, also *.pem.* and
     *    *.key.* such as private.pem.old, credentials*, *service-account*, .env but not .env.example, .sample or
     *    .template, id_rsa, ...) and every file in a KEY_DIRECTORY_PATTERN directory (jwt/, .ssh/, *.pem/, *.key/);
     *  - a symbolic link that leads outside the extension or nowhere, and anything that is neither file, directory
     *    nor link;
     *  - a copy or backup of a settings file (BACKUP_FILE_PATTERN: *.bak, *.orig, *.dist, *~, ...) below settings/
     *    or named *.ini*;
     *  - the text of every file: secretsInText(). Settings files (below settings/, any case, any depth, or named
     *    *.ini*) are read as INI pairs, the files of CONFIG_FILE_PATTERN as pairs of names and values, every other
     *    file (PHP, JSON, XML, text, templates ...) for the shapes of secrets (SECRET_SHAPE_PATTERNS). A file is binary when its first 8 KB hold a NUL byte: a binary file
     *    is not read, but a binary settings file is refused. Text in UTF-16 or UTF-32, text bigger than
     *    MAX_SCAN_BYTES and a file that cannot be read are refused.
     * A directory that cannot be read refuses the whole extension. Reads the files only.
     *
     * @param string $dir
     * @return string[] "<path below $dir>: <setting>" or "<path below $dir>: <reason>", sorted
     */
    static function secretFiles( $dir )
    {
        $dir = rtrim( (string)$dir, '/' );
        if ( $dir === '' || !is_dir( $dir ) )
            return array();
        try
        {
            $entries = self::packEntries( $dir );
        }
        catch ( RuntimeException $e )
        {
            return array( '.: ' . $e->getMessage() );
        }
        $realDir = realpath( $dir );
        $found = array();
        foreach ( $entries as $relative => $entry )
        {
            $full = $dir . '/' . $relative;
            $parts = explode( '/', $relative );
            $name = array_pop( $parts );
            if ( $entry['type'] === 'link' )
            {
                $target = realpath( $full );
                if ( $target === false || $realDir === false || ( $target !== $realDir && strpos( $target, $realDir . '/' ) !== 0 ) )
                    $found[$relative . ': link outside the extension'] = true;
                continue;
            }
            if ( $entry['type'] === 'other' )
            {
                $found[$relative . ': not a regular file'] = true;
                continue;
            }
            if ( $entry['type'] !== 'file' )
                continue;
            if ( preg_match( self::KEY_FILE_PATTERN, $name ) )
            {
                $found[$relative . ': key material'] = true;
                continue;
            }
            foreach ( $parts as $part )
            {
                if ( preg_match( self::KEY_DIRECTORY_PATTERN, $part ) )
                {
                    $found[$relative . ': key material'] = true;
                    continue 2;
                }
            }
            $isSettings = ( isset( $parts[0] ) && strtolower( $parts[0] ) === 'settings' ) || preg_match( '/\.ini(\.|$)/i', $name );
            if ( $isSettings && preg_match( self::BACKUP_FILE_PATTERN, $name ) )
            {
                $found[$relative . ': copy of a settings file'] = true;
                continue;
            }

            $handle = @fopen( $full, 'rb' );
            $text = $handle ? fread( $handle, self::MAX_SCAN_BYTES + 1 ) : false;
            if ( $handle )
                fclose( $handle );
            if ( $text === false )
            {
                $found[$relative . ': unreadable'] = true;
                continue;
            }
            $utf16 = preg_match( '/^(\xFF\xFE|\xFE\xFF|\x00\x00\xFE\xFF)/', $text ) === 1;
            if ( !$utf16 && strpos( substr( $text, 0, 8192 ), "\0" ) !== false )
            {
                if ( $isSettings )
                    $found[$relative . ': binary settings file'] = true;
                continue;
            }
            if ( strlen( $text ) > self::MAX_SCAN_BYTES )
            {
                $found[$relative . ': text too large to check'] = true;
                continue;
            }
            $pairs = $isSettings ? 'ini' : ( preg_match( self::CONFIG_FILE_PATTERN, $name ) ? 'config' : false );
            foreach ( self::secretsInText( $text, $pairs ) as $setting )
                $found[$relative . ': ' . $setting] = true;
        }
        $found = array_keys( $found );
        sort( $found );
        return $found;
    }

    /**
     * Why the extension $extensionName cannot be packed, or an empty string when it can (isPackableName() and no
     * secretFiles()). The one rule of Setup > Extensions, the package wizard, the command line and addExtension().
     *
     * @param string $extensionName
     * @return string
     */
    static function packRefusal( $extensionName )
    {
        if ( !self::isPackableName( $extensionName ) )
            return ezpI18n::tr( 'kernel/package', 'The extension %name is not known.', null, array( '%name' => (string)$extensionName ) );
        $secrets = self::secretFiles( eZExtension::baseDirectory() . '/' . $extensionName );
        if ( !$secrets )
            return '';
        return ezpI18n::tr( 'kernel/package', 'The extension %name was not packed: it holds secret values or key material (%files). Copy it from the server instead.', null,
                            array( '%name' => $extensionName,
                                   '%files' => implode( ', ', array_slice( $secrets, 0, 5 ) ) . ( count( $secrets ) > 5 ? ', ...' : '' ) ) );
    }

    /**
     * Adds the extension $extensionName to $package. The extension is checked before anything is copied, and an
     * extension that packRefusal() names a reason for is refused (RuntimeException, nothing copied): every way to a
     * package (Setup > Extensions, the package wizard, the command line) passes here. Then exactly the entries
     * packEntries() marks as packed are copied, the same ones the check read.
     *
     * @param eZPackage $package
     * @param string $extensionName
     * @throws RuntimeException
     */
    static function addExtension( $package, $extensionName )
    {
        $refusal = self::packRefusal( $extensionName );
        if ( $refusal !== '' )
            throw new RuntimeException( $refusal );

        $fileList = array();
        $sourceDir = eZExtension::baseDirectory() . '/' . $extensionName;
        $entries = self::packEntries( $sourceDir );
        $targetDir = $package->path() . '/ezextension';

        eZDir::mkdir( $targetDir, false, true );
        // as eZDir::copy( $sourceDir, $targetDir ) did: the extension's directory below ezextension/
        $copyDir = $targetDir . '/' . $extensionName;
        eZDir::mkdir( $copyDir, eZDir::directoryPermission(), false );
        foreach ( $entries as $relative => $entry )
        {
            if ( !$entry['packed'] )
                continue;
            $source = $sourceDir . '/' . $relative;
            if ( is_link( $source ) )
                throw new RuntimeException( "The extension $extensionName changed while it was packed: $relative" );
            if ( $entry['type'] === 'dir' )
                eZDir::mkdir( $copyDir . '/' . $relative, eZDir::directoryPermission(), false );
            else if ( !eZFileHandler::copy( $source, $copyDir . '/' . $relative ) )
                throw new RuntimeException( "The file $relative of the extension $extensionName could not be packed." );
        }

        eZDir::recursiveList( $targetDir, '', $fileList );

        $doc = new DOMDocument;

        $packageRoot = $doc->createElement( 'extension' );
        $packageRoot->setAttribute( 'name', $extensionName );

        foreach( $fileList as $file )
        {
            $fileNode = $doc->createElement( 'file' );
            $fileNode->setAttribute( 'name', $file['name'] );

            if ( $file['path'] )
                $fileNode->setAttribute( 'path', $file['path'] );

            $fullPath = $targetDir . $file['path'] . '/' . $file['name'];
            $fileNode->setAttribute( 'md5sum', $package->md5sum( $fullPath ) );

            if ( $file['type'] == 'dir' )
                 $fileNode->setAttribute( 'type', 'dir' );

            $packageRoot->appendChild( $fileNode );
            unset( $fileNode );
        }

        $filename = 'extension-' . $extensionName;

        $package->appendInstall( 'ezextension', false, false, true,
                                 $filename, 'ezextension',
                                 array( 'content' => $packageRoot ) );
        $package->appendInstall( 'ezextension', false, false, false,
                                 $filename, 'ezextension',
                                 array( 'content' => false ) );
    }

    function handleAddParameters( $packageType, $package, $cli, $arguments )
    {
        $arguments = array_unique( $arguments );
        $extensionsToAdd = array();

        $extensionList = array();
        foreach ( eZExtension::extensionRootDirectories() as $extensionRoot )
        {
            if ( is_dir( $extensionRoot ) )
            {
                foreach ( eZDir::findSubItems( $extensionRoot, 'dl' ) as $extensionName )
                {
                    $extensionList[$extensionName] = $extensionName;
                }
            }
        }
        $extensionList = array_values( $extensionList );

        foreach ( $arguments as $argument )
        {
            if ( in_array( $argument, $extensionList ) )
            {
                $refusal = self::packRefusal( $argument );
                if ( $refusal !== '' )
                {
                    $cli->error( $refusal );
                    return false;
                }
                $extensionsToAdd[] = $argument;
            }
            else
            {
                $cli->error( 'Extension ' . $cli->style( 'dir' ) . $argument .  $cli->style( 'dir-end' ) . ' not found.' );
                return false;
            }
        }

        return $extensionsToAdd;
    }

    public $Package = null;
}

?>
