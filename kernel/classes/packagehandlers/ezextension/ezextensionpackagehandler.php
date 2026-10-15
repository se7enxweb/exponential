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

    /**
     * Files that hold key material, anywhere in an extension: by name, any case. Certificates and signatures
     * (*.pem, *.crt, *.der, *.asc, such as a public CA bundle) are not refused by name: their content is read like
     * every file's, and a private key block in them refuses the extension (SECRET_SHAPE_PATTERNS).
     */
    const KEY_FILE_PATTERN = '/(\.(key|p12|pfx|jks|keystore|kdbx|p8|ppk|gpg)$|\.(key|p12|pfx)\.|^\.env(\.(?!(example|sample|template)$).*)?$|^id_(rsa|dsa|ecdsa|ed25519)|^\.htpasswd$|^\.netrc$|^\.pgpass$|^credentials|service[-_]?account)/i';

    /** Directories whose files are key material, anywhere in an extension, any case */
    const KEY_DIRECTORY_PATTERN = '/(^(jwt|\.ssh|\.gnupg)$|\.(pem|key)$)/i';

    /** Copies and backups of settings files: never packed, whatever they hold */
    const BACKUP_FILE_PATTERN = '/(\.(bak|orig|dist|old|save|swp|swo|tmp|rej|backup)$|~$)/i';

    /** A file whose text is read for secrets is at most this big; a bigger one is refused, not skipped */
    const MAX_SCAN_BYTES = 4194304;

    /** An extension with more entries than this is refused, not partly checked */
    const MAX_ENTRIES = 200000;

    /** MAX_ENTRIES, lowered by the tests */
    public static $maxEntries = self::MAX_ENTRIES;

    /** A name longer than this is taken for a secret's name, not read: no pattern runs over it */
    const MAX_NAME_BYTES = 256;

    /** A value longer than this is taken for a secret, not read */
    const MAX_VALUE_BYTES = 65536;

    /**
     * preg_match() that fails closed: any PCRE error (the backtrack or recursion limit, the JIT stack, bad UTF-8
     * under /u) is a RuntimeException, never a "no match" that would read as "no secret".
     *
     * @return bool
     * @throws RuntimeException
     */
    static function pregMatch( $pattern, $subject, &$matches = null )
    {
        $result = preg_match( $pattern, (string)$subject, $matches );
        if ( $result === false || preg_last_error() !== PREG_NO_ERROR )
            throw new RuntimeException( 'pattern check failed: ' . preg_last_error_msg() );
        return $result === 1;
    }

    /**
     * preg_match_all() that fails closed (see pregMatch()).
     *
     * @return int
     * @throws RuntimeException
     */
    static function pregMatchAll( $pattern, $subject, &$matches = null, $flags = PREG_PATTERN_ORDER )
    {
        $result = preg_match_all( $pattern, (string)$subject, $matches, $flags );
        if ( $result === false || preg_last_error() !== PREG_NO_ERROR )
            throw new RuntimeException( 'pattern check failed: ' . preg_last_error_msg() );
        return $result;
    }

    /**
     * preg_replace() that fails closed (see pregMatch()).
     *
     * @return string
     * @throws RuntimeException
     */
    static function pregReplace( $pattern, $replacement, $subject )
    {
        $result = preg_replace( $pattern, $replacement, (string)$subject );
        if ( $result === null || preg_last_error() !== PREG_NO_ERROR )
            throw new RuntimeException( 'pattern check failed: ' . preg_last_error_msg() );
        return $result;
    }

    /**
     * preg_split() that fails closed (see pregMatch()).
     *
     * @return string[]
     * @throws RuntimeException
     */
    static function pregSplit( $pattern, $subject )
    {
        $result = preg_split( $pattern, (string)$subject );
        if ( $result === false || preg_last_error() !== PREG_NO_ERROR )
            throw new RuntimeException( 'pattern check failed: ' . preg_last_error_msg() );
        return $result;
    }

    /**
     * expSystemReportMask::isSecretSettingName(), failing closed: a name over MAX_NAME_BYTES, or a PCRE error in any
     * of the rule's patterns, counts as a secret's name.
     *
     * @param string $name
     * @return bool
     */
    static function isSecretName( $name )
    {
        $name = (string)$name;
        if ( strlen( $name ) > self::MAX_NAME_BYTES )
            return true;
        // the names the check passes here are runs of [A-Za-z0-9_.-] of at most MAX_NAME_BYTES: no pattern of the
        // rule can reach a PCRE limit on them; an error is still read as a secret
        $secret = expSystemReportMask::isSecretSettingName( $name );
        return $secret || preg_last_error() !== PREG_NO_ERROR;
    }

    /**
     * expSecretRule::hasInlineSecret(), failing closed: a value over MAX_VALUE_BYTES, or a PCRE error, counts as a
     * secret.
     *
     * @param string $value
     * @return bool
     */
    static function hasInlineSecret( $value )
    {
        $value = (string)$value;
        if ( strlen( $value ) > self::MAX_VALUE_BYTES )
            return true;
        $secret = expSecretRule::hasInlineSecret( $value );
        return $secret || preg_last_error() !== PREG_NO_ERROR;
    }

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
        if ( !self::pregMatch( '/^[A-Za-z0-9][A-Za-z0-9_.\-]*$/', $extensionName ) )
            return false;
        $dir = eZExtension::baseDirectory() . '/' . $extensionName;
        return is_dir( $dir ) && !is_link( $dir );
    }

    /**
     * Directories that hold the dependencies of a build, not the extension: an extension package never takes them
     * (a checkout of exp_oe_tiptap has node_modules for its editor build; the extension runs from its design files),
     * and the check does not read them.
     */
    const UNPACKED_DIRECTORY_NAMES = array( 'node_modules' );

    /**
     * Every entry below $dir, breadth first as eZDir::copy() walks, with what the package takes of it: a file or a
     * directory whose name and parents do not start with "." (eZDir::copy() without hidden items) and that is not
     * a symbolic link (eZDir::findSubitems() without "l"); a link is listed, not followed; a directory of
     * UNPACKED_DIRECTORY_NAMES is listed as not packed and not walked. addExtension() copies
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
                else if ( is_dir( $full ) && in_array( $name, self::UNPACKED_DIRECTORY_NAMES, true ) )
                    // the dependencies of a build (node_modules): never packed, and not read either
                    $entries[$path] = array( 'type' => 'dir', 'packed' => false );
                else if ( is_dir( $full ) )
                {
                    $entries[$path] = array( 'type' => 'dir', 'packed' => !$isHidden );
                    $queue[] = array( $path, $isHidden );
                }
                else if ( is_file( $full ) )
                    $entries[$path] = array( 'type' => 'file', 'packed' => !$isHidden );
                else
                    $entries[$path] = array( 'type' => 'other', 'packed' => false );
                if ( count( $entries ) > self::$maxEntries )
                    throw new RuntimeException( 'more than ' . self::$maxEntries . ' entries' );
            }
        }
        return $entries;
    }

    /** Secrets by their shape, in any text: private keys, bearer tokens and the token formats of common services */
    const SECRET_SHAPE_PATTERNS = array(
        'private key' => '/-----BEGIN [A-Z0-9 ]*PRIVATE KEY( BLOCK)?-----/',
        'bearer token' => '/\bAuthorization\s*[:=]\s*["\']?Bearer\s+[A-Za-z0-9\-._~+\/]{8,}/i',
        'API key' => '/\b(sk-(proj-|live-|test-)?[A-Za-z0-9_\-]{20,}|AKIA[0-9A-Z]{16}|ASIA[0-9A-Z]{16}|AIza[0-9A-Za-z_\-]{35}|gh[pousr]_[A-Za-z0-9]{30,}|github_pat_[A-Za-z0-9_]{30,}|xox[abprs]-[A-Za-z0-9\-]{10,}|glpat-[A-Za-z0-9_\-]{20,}|sk_live_[A-Za-z0-9]{20,}|rk_live_[A-Za-z0-9]{20,})/',
        'constant' => '/\bdefine\s*\(\s*["\'][A-Za-z0-9_]*(KEY|SECRET|TOKEN|PASS|PASSWORD|PWD|CREDENTIAL)[A-Za-z0-9_]*["\']\s*,\s*["\']([^"\']+)["\']/i',
        'address with a password' => '#[a-z][a-z0-9+.\-]*://([^/\s:@"\']+):([^@\s/"\']+)@#i',
    );

    /**
     * Words a value is made of when it only stands for a secret: documentation and test values such as
     * "password", "dbpassword", "your_secret", "changeme" or the well-known demo account "scott"/"tiger". Matched
     * against the whole value (an optional prefix of these words, then one of them, maybe digits), any case.
     */
    const PLACEHOLDER_WORDS_PATTERN = '/^((my|your|the|some|db|database|example|sample|test|dummy|demo|fake|new|old|default|user|admin|api|client|app)[_\- ]?)*(password|passwd|pass|pwd|pw|secret|key|token|changeme|change_?me|example|sample|test|testing|dummy|fake|placeholder|redacted|foo|bar|baz|xyz|scott|tiger|none|null|empty|value|here)\d*$/i';

    /** The start of a value that only tells the reader what to fill in: "Enter your key here", "Your secret" */
    const FILL_IN_PATTERN = '/^(enter|your|insert|put|replace|set|add|paste|fill)\s+\S/i';

    /** The body of a test or documentation token: test-..., dummy..., SECRET..., example..., xxxx... */
    const FIXTURE_TOKEN_PATTERN = '/^(test|dummy|fake|example|sample|placeholder|secret|redacted|changeme|your|my|x{4,}|\*{3,}|\.\.\.)/i';

    /** User names of the example credentials in addresses (https://user:pass@...) */
    const EXAMPLE_USER_PATTERN = '/^(u|a|x|me|user|abc|username|name|login|admin|test|foo|demo|example|someone|john|jane|alice|bob|%[A-Za-z_]+%|\$\{[^}]*\}|\{[A-Za-z_]+\}|<[^>]*>)$/i';

    /**
     * The extension's own list of findings that are known not to be secrets: a plain file at the root of the
     * extension, read only by the package check. See doc/bc/6.0/extension-package-secret-check.md.
     */
    const ALLOW_FILE = 'package_check.ini';

    /** Findings the allow file can never let through: key material, private keys, copies, links, unreadable files */
    const NEVER_ALLOWED_PATTERN = '/^(key material|\(private key\)|copy of a settings file|link outside the extension|not a regular file|unreadable|could not be checked|text too large to check|binary settings file|binary configuration file|\(text in UTF-16 or UTF-32\))$/';

    /** Text files whose lines are read as "name = value" or "name: value" pairs, besides settings files */
    const CONFIG_FILE_PATTERN = '/(\.(ya?ml|conf|cfg|cnf|properties|toml|env|neon)$|^\.env)/i';

    /**
     * Whether $value is only a placeholder as a whole: "...", "…", "xxx" (three or more), "***" (three or more),
     * "<...>" or "<…>". Anything else under a secret name is taken for a secret. Pure.
     *
     * @param string $value
     * @return bool
     */
    static function isPlaceholder( $value, $words = false )
    {
        $value = trim( (string)$value, " \t\"'" );
        if ( $value === '' )
            return true;
        if ( strlen( $value ) > self::MAX_NAME_BYTES )
            return false;
        // in example text (a comment, a constant or an address in a document or a test) a value made of
        // PLACEHOLDER_WORDS_PATTERN stands for a secret; an active setting "Password=secret" is taken at its word
        if ( $words && self::pregMatch( self::PLACEHOLDER_WORDS_PATTERN, $value ) )
            return true;
        return self::pregMatch( '/^(\.\.\.|\xE2\x80\xA6|[xX]{3,}|\*{3,}|<\.\.\.>|<\xE2\x80\xA6>|<[A-Za-z_][A-Za-z_ \-]*>)$/', $value )
            // a switch or nothing: no password is one of these words, and refusing Password=disabled protects nothing
            || self::pregMatch( '/^(enabled|disabled|true|false|none|null|0)$/i', $value )
            // a template placeholder as a whole: %name%, {name}, ${NAME}, {{ name }}, %env(NAME)%
            || self::pregMatch( '/^(%[A-Za-z_][A-Za-z0-9_.\-]*%|\{[A-Za-z_][A-Za-z0-9_.\-]*\}|\$\{[^{}]+\}|\{\{[^{}]+\}\}|%env\([^()]*\)%)$/', $value )
            // a value put together when it is used: it holds an expression, not a literal secret
            || self::pregMatch( '/(\$\{\{[^{}]+\}\}|\$\{[A-Za-z_][A-Za-z0-9_.:\-]*\}|%env\([^()]*\)%)/', $value )
            || self::pregMatch( self::FILL_IN_PATTERN, $value );
    }

    /**
     * Whether a secret found inside a value (expSecretRule::inlineSecretParts()) or by its shape stands only for
     * one: a placeholder (isPlaceholder()), or the credentials of an example address, such as user:pass@ or a:b@
     * (a user name of EXAMPLE_USER_PATTERN with a placeholder password, or both at most two characters). Pure.
     *
     * @param string $secret
     * @param string|null $user the user name of an address, null elsewhere
     * @return bool
     */
    static function isExampleSecret( $secret, $user = null )
    {
        $secret = trim( (string)$secret, " \t\"'" );
        if ( $user === null )
            return self::isPlaceholder( $secret, true );
        $user = (string)$user;
        if ( strlen( $user ) <= 2 && strlen( $secret ) <= 2 )
            return true;
        return ( $user === '' || self::pregMatch( self::EXAMPLE_USER_PATTERN, $user ) )
            && ( self::isPlaceholder( $secret, true ) || strlen( $secret ) <= 2 );
    }

    /**
     * expSecretRule's inline secrets in $value that are not examples (isExampleSecret()), failing closed: a value
     * over MAX_VALUE_BYTES, or a PCRE error, counts as a secret.
     *
     * @param string $value
     * @return bool
     */
    static function hasRealInlineSecret( $value )
    {
        $value = (string)$value;
        if ( strlen( $value ) > self::MAX_VALUE_BYTES )
            return true;
        if ( !self::hasInlineSecret( $value ) )
            return false;
        $parts = expSecretRule::inlineSecretParts( $value );
        if ( preg_last_error() !== PREG_NO_ERROR || count( $parts ) === 0 )
            return true;
        foreach ( $parts as $part )
        {
            if ( !self::isExampleSecret( $part['secret'], $part['user'] ) )
                return true;
        }
        return false;
    }

    /**
     * Whether $name only looks like a secret's name: an address, a header's name, a type, mode or method
     * (TokenURL, ApiKeyHeader, AuthType), or a public key (PublicKey, public_key: a captcha site key is published
     * in every page). Its value is still read for secrets inside it. Pure.
     *
     * @param string $name
     * @return bool
     */
    static function isNotSecretName( $name )
    {
        return self::pregMatch( '/(url|uri|endpoint|header|type|mode|method)(\[\])?$/i', (string)$name )
            || self::pregMatch( '/public[_\-]?key/i', (string)$name );
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
        if ( self::pregMatch( '/^\d{1,9}(\.\d+)?(\s|$)/', $value ) )
            // a measure, a version (TokenVersion) or a browser version (IEDisableWithPassword[]=11)
            return self::pregMatch( '/^IE[A-Z]/', $name )
                || self::pregMatch( '/(tokens|length|lifetime|budget|count|size|limit|timeout|ttl|version|^max|^min|^estimate|per_?token)/i', $name );
        if ( self::pregMatch( '/^(enabled|disabled|true|false)$/i', $value ) )
            return self::pregMatch( '/(^(allow|use|require|enable|disable|is|has|show|check)|(enabled|required|disable)|with)/i', $name )
                && self::pregMatch( '/^(allow|use|require|enable|disable|is|has|show|check|ie)/i', $name );
        return false;
    }

    /**
     * Whether the pair $name = $value holds a secret: under a secret name (expSystemReportMask::isSecretSettingName(),
     * but not isNotSecretName()) every value but an empty one, a placeholder or a switch (isPlaceholder(); in a
     * comment also PLACEHOLDER_WORDS_PATTERN) or a measure its name announces (isMeasureOrSwitch()); other numbers
     * count as secrets; under another name a value that carries a real secret inline (hasRealInlineSecret()) or has
     * the shape of one (shapesIn()). Pure.
     *
     * @param string $name
     * @param string $value
     * @param bool $inComment the pair stands on a commented line
     * @return bool
     */
    static function isSecretPair( $name, $value, $inComment = false )
    {
        $value = trim( self::pregReplace( '#\s*\*/\s*(\?>)?\s*$#', '', (string)$value ) );
        $value = trim( rtrim( $value, ',;' ) );
        $value = trim( $value, " \t\"'" );
        if ( $value === '' )
            return false;
        if ( self::isSecretName( $name ) && !self::isNotSecretName( $name ) )
            return !self::isPlaceholder( $value, $inComment ) && !self::isMeasureOrSwitch( $name, $value );
        if ( self::hasRealInlineSecret( $value ) )
            return true;
        return count( self::shapesIn( $value ) ) > 0;
    }

    /**
     * The secrets in the raw text of a file, by the names they stand under or by their kind, without their values.
     *
     * Every text: shapesIn() (SECRET_SHAPE_PATTERNS: a private key, "Authorization: Bearer ...", service tokens such
     * as sk-..., AKIA..., ghp_..., xoxb-..., define( '..._KEY', '...' ), an address with a password), and a UTF-16
     * or UTF-32 byte order mark. $pairs 'ini' or 'config': pairSecretsIn() as well. The text is read as it is: no
     * byte is removed, decoded or changed before it is read. Pure.
     *
     * @param string $text
     * @param string|false $pairs 'ini', 'config' or false
     * @return string[] names, or kinds in parentheses
     */
    static function secretsInText( $text, $pairs = 'ini' )
    {
        $text = (string)$text;
        $found = array();
        if ( self::pregMatch( '/^(\xFF\xFE|\xFE\xFF|\x00\x00\xFE\xFF)/', $text ) )
            $found['(text in UTF-16 or UTF-32)'] = true;
        foreach ( self::shapesIn( $text ) as $kind )
            $found[$kind] = true;
        if ( $pairs !== false )
        {
            foreach ( self::pairSecretsIn( $text, $pairs ) as $name )
                $found[$name] = true;
        }
        return array_keys( $found );
    }

    /**
     * The kinds of SECRET_SHAPE_PATTERNS in $bytes, read as they are and once more without NUL bytes (text in
     * UTF-16 or UTF-32 without a byte order mark). Pure.
     *
     * @param string $bytes
     * @return string[] kinds in parentheses
     */
    static function shapesIn( $bytes )
    {
        $found = array();
        $views = array( (string)$bytes );
        if ( strpos( (string)$bytes, "\0" ) !== false )
            $views[] = str_replace( "\0", '', (string)$bytes );
        foreach ( self::SECRET_SHAPE_PATTERNS as $kind => $pattern )
        {
            foreach ( $views as $view )
            {
                if ( !self::pregMatchAll( $pattern, $view, $matches, PREG_SET_ORDER ) )
                    continue;
                foreach ( $matches as $m )
                {
                    if ( !self::isExampleShape( $kind, $m ) )
                    {
                        $found['(' . $kind . ')'] = true;
                        break 2;
                    }
                }
            }
        }
        return array_keys( $found );
    }

    /**
     * Whether a match $m of the SECRET_SHAPE_PATTERNS kind $kind is a test or documentation value: a bearer or
     * service token whose body starts like a fixture (FIXTURE_TOKEN_PATTERN: "Bearer test-...", "sk-test-SECRET"),
     * a constant whose value is a placeholder (define( 'DB_PASS', 'dbpassword' )), an address with example
     * credentials (isExampleSecret()). A private key is never an example. Pure.
     *
     * @param string $kind
     * @param array $m the match, with its groups
     * @return bool
     */
    static function isExampleShape( $kind, array $m )
    {
        switch ( $kind )
        {
            case 'bearer token':
                return self::pregMatch( '/Bearer\s+(.*)$/is', $m[0], $body ) && self::pregMatch( self::FIXTURE_TOKEN_PATTERN, $body[1] );
            case 'API key':
                // the body after the vendor's prefix (sk-, sk-test-, ghp_, xoxb- ...)
                $body = self::pregReplace( '/^(sk-(proj-|live-|test-)?|AKIA|ASIA|AIza|gh[pousr]_|github_pat_|xox[abprs]-|glpat-|sk_live_|rk_live_)/', '', $m[0] );
                return self::pregMatch( self::FIXTURE_TOKEN_PATTERN, $body );
            case 'constant':
                return isset( $m[2] ) && self::isPlaceholder( $m[2], true );
            case 'address with a password':
                // example credentials only (user:pass@, abc:xyz@): a real password pasted into a document is real,
                // whatever host it stands with
                return isset( $m[1], $m[2] ) && self::isExampleSecret( $m[2], $m[1] );
        }
        return false;
    }

    /**
     * The names of the secret pairs in $text, read from the bytes as they are: no byte order mark is removed and no
     * line end changed. 'ini' (settings files): every "name = value", read as text, not as eZINI reads it, so nothing
     * eZINI skips or reads differently is missed (commented lines and the PHP comment wrapper, Key[]= and Key[name]=,
     * blanks around "=", quotes, a byte order mark, CRLF or CR, duplicates), judged by isSecretPair(), and outside
     * comments a line carrying a secret inline ("password: ..."). 'config' (CONFIG_FILE_PATTERN): also
     * "name: value", quoted names, and a secret name in YAML whose value stands on a following, deeper line. A line
     * holds one pair, the one it starts with; the rest of the line is that pair's value, read for secrets inside it
     * (hasRealInlineSecret()): "Title=x Password=y" is refused for the secret in Title's value, and
     * "MySQLDumpCommand=mysqldump --password={password}" passes. A commented pair is read too, where a documentation
     * word stands for the value ("# Password=changeme") and words that explain the setting are no value. Lines end
     * at CR, LF, FF or VT. Pure.
     *
     * @param string $text
     * @param string $pairs 'ini' or 'config'
     * @return string[]
     */
    static function pairSecretsIn( $text, $pairs )
    {
        // the pair a line starts with (after a comment mark, blanks, a YAML list dash or a PHP comment opener); the
        // rest of the line is its value, which is read for secrets inside it (hasRealInlineSecret()), not split into
        // more pairs: "MySQLDumpCommand=mysqldump --user={user} --password={password}" is one setting
        $lead = '^(?:\xEF\xBB\xBF)?[ \t]*(?:<\?php[ \t]*)?(?:\/\*+[ \t]*)?(?:(?:#|;|\/\/|\*)+[ \t]*)?(?:-[ \t]+)?';
        $pairPattern = $pairs === 'config'
            ? '/' . $lead . '["\']?([A-Za-z0-9_.\-]+)["\']?[ \t]*(?:\[[^\]\r\n]*\])?[ \t]*(?:=(?![=>])|:(?![:\/]))([^\r\n\f\v]*)/'
            : '/' . $lead . '([A-Za-z0-9_.\-]+)[ \t]*(?:\[[^\]\r\n]*\])?[ \t]*=(?![=>])([^\r\n\f\v]*)/';
        $found = array();
        $lines = self::pregSplit( '/\r\n|[\r\n\f\v]/', (string)$text );
        foreach ( $lines as $i => $line )
        {
            $isComment = self::pregMatch( '/^\s*(#|;|\/\/|\*)/', $line );
            if ( self::pregMatchAll( $pairPattern, $line, $matches, PREG_SET_ORDER ) )
            {
                foreach ( $matches as $m )
                {
                    // a comment that explains a setting in words ("# ClientSecret = the client's secret key"):
                    // three or more words of letters only, no value
                    if ( $isComment && self::pregMatch( '/^\s*[A-Za-z\']+([ \t]+[A-Za-z\'(),.]+){2,}\s*$/', $m[2] ) )
                        continue;
                    if ( self::isSecretPair( $m[1], $m[2], $isComment ) )
                        $found[$m[1]] = true;
                    // YAML: a secret name whose value stands on the next non-empty, deeper line ("password:",
                    // "password: |"), a nested mapping included
                    else if ( $pairs === 'config' && self::isSecretName( $m[1] )
                              && self::pregMatch( '/^\s*([|>][\-+]?)?\s*(#.*)?$/', $m[2] ) )
                    {
                        self::pregMatch( '/^[ \t]*/', $line, $indent );
                        for ( $n = $i + 1; isset( $lines[$n] ); $n++ )
                        {
                            if ( trim( $lines[$n] ) === '' )
                                continue;
                            if ( self::pregMatch( '/^([ \t]*)(\S.*)$/', $lines[$n], $next ) && strlen( $next[1] ) > strlen( $indent[0] )
                                 && !self::isPlaceholder( trim( self::pregReplace( '/^-\s*/', '', $next[2] ), " \t\"'" ) ) )
                                $found[$m[1]] = true;
                            break;
                        }
                    }
                }
            }
            if ( !$isComment && !self::pregMatchAll( $pairPattern, $line ) && self::hasRealInlineSecret( $line ) )
                $found['(inline)'] = true;
        }
        return array_keys( $found );
    }

    /**
     * The kinds of SECRET_SHAPE_PATTERNS in the whole file $file, read in pieces that overlap, so a file of any
     * size and any content (text or binary) is read to its end. Null when the file cannot be read to its end.
     *
     * @param string $file
     * @return string[]|null
     */
    static function shapesInFile( $file )
    {
        $handle = @fopen( $file, 'rb' );
        if ( $handle === false )
            return null;
        $found = array();
        $tail = '';
        $read = 0;
        while ( !feof( $handle ) )
        {
            $chunk = fread( $handle, 1048576 );
            if ( $chunk === false )
            {
                fclose( $handle );
                return null;
            }
            $read += strlen( $chunk );
            $buffer = $tail . $chunk;
            foreach ( self::shapesIn( $buffer ) as $kind )
                $found[$kind] = true;
            $tail = substr( $buffer, -65536 );
        }
        fclose( $handle );
        clearstatcache( true, $file );
        if ( $read !== @filesize( $file ) )
            return null;
        return array_keys( $found );
    }

    /**
     * What in the extension directory $dir must not leave the server in a package, one line each, without a value.
     * Every entry of packEntries() is checked, the packed ones and the others (hidden files, links) alike:
     *  - key material anywhere, by name in any case: KEY_FILE_PATTERN (*.key, *.p12, *.pfx, *.jks, *.ppk, also *.key.*
     *    such as server.key.old, credentials*, *service-account*, .env but not .env.example, .sample or .template,
     *    id_rsa, ...) and every file in a KEY_DIRECTORY_PATTERN directory (jwt/, .ssh/, *.pem/, *.key/); a
     *    certificate (*.pem, *.crt, *.der, *.asc) is refused only for a private key block in it (shapesInFile());
     *  - a symbolic link that leads outside the extension or nowhere, and anything that is neither file, directory
     *    nor link;
     *  - a copy or backup of a settings file (BACKUP_FILE_PATTERN: *.bak, *.orig, *.dist, *~, ...) below settings/
     *    or named *.ini*;
     *  - every file, text or binary, whole and as it is packed, for the shapes of secrets (shapesInFile(),
     *    SECRET_SHAPE_PATTERNS, also with NUL bytes left out); a file with a UTF-16 or UTF-32 byte order mark;
     *  - settings files (below settings/, any case, any depth, or named *.ini*) as INI pairs and the files of
     *    CONFIG_FILE_PATTERN as pairs of names and values (pairSecretsIn()), from their whole text, unchanged; such a
     *    file with a NUL byte, bigger than MAX_SCAN_BYTES or not readable to its end is refused.
     * A directory that cannot be read refuses the whole extension. Reads the files only. Last, the findings the
     * extension's ALLOW_FILE names are left out (allowedFindings(); never key material, a private key, a copy, a
     * link or a file that could not be read).
     *
     * @param string $dir
     * @return string[] "<path below $dir>: <setting>" or "<path below $dir>: <reason>", sorted
     */
    static function secretFiles( $dir )
    {
        $dir = rtrim( (string)$dir, '/' );
        if ( $dir === '' || !is_dir( $dir ) || is_link( $dir ) )
            return array( '.: not a directory' );
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
            $relative = (string)$relative;
            try
            {
                foreach ( self::entryFindings( $dir, $realDir, $relative, $entry ) as $finding )
                    $found[$relative . ': ' . $finding] = true;
            }
            catch ( RuntimeException $e )
            {
                // a pattern that failed, a file that changed: the entry is not checked, so the extension is refused
                $found[$relative . ': could not be checked'] = true;
            }
        }
        $found = array_keys( $found );
        $allowed = self::allowedFindings( $dir );
        if ( count( $allowed ) > 0 )
            $found = array_values( array_filter( $found, function ( $finding ) use ( $allowed )
            {
                return !self::isAllowedFinding( $finding, $allowed );
            } ) );
        sort( $found, SORT_STRING );
        return $found;
    }

    /**
     * The findings the extension in $dir lists as known not to be secrets, from its ALLOW_FILE:
     * "AllowedFindings[]=<path below the extension>:<setting name or (kind)>", the path a file, or a directory
     * when it ends in "/". A link, an unreadable or a too big file allows nothing.
     *
     * @param string $dir
     * @return array[] array( path, finding )
     */
    static function allowedFindings( $dir )
    {
        $file = rtrim( (string)$dir, '/' ) . '/' . self::ALLOW_FILE;
        if ( is_link( $file ) || !is_file( $file ) )
            return array();
        $size = @filesize( $file );
        if ( $size === false || $size > 65536 )
            return array();
        $text = @file_get_contents( $file );
        if ( !is_string( $text ) )
            return array();
        $allowed = array();
        foreach ( self::pregSplit( '/\r\n|[\r\n]/', $text ) as $line )
        {
            if ( !self::pregMatch( '/^\s*AllowedFindings\[\]\s*=\s*([^:\s][^:]*):\s*(\S.*?)\s*$/', $line, $m ) )
                continue;
            $path = trim( $m[1] );
            // a plain path below the extension: no "..", no leading "/", no wildcard
            if ( $path[0] === '/' || self::pregMatch( '#(^|/)\.\.(/|$)|[*?\[]#', $path ) )
                continue;
            $allowed[] = array( $path, $m[2] );
        }
        return $allowed;
    }

    /**
     * Whether $finding ("<path>: <setting or reason>", from secretFiles()) is one of $allowed (allowedFindings()).
     * Key material, private keys, copies of settings files, links and files that could not be read are never
     * allowed (NEVER_ALLOWED_PATTERN).
     *
     * @param string $finding
     * @param array[] $allowed
     * @return bool
     */
    static function isAllowedFinding( $finding, array $allowed )
    {
        $at = strrpos( (string)$finding, ': ' );
        if ( $at === false )
            return false;
        $path = substr( $finding, 0, $at );
        $what = substr( $finding, $at + 2 );
        if ( self::pregMatch( self::NEVER_ALLOWED_PATTERN, $what ) )
            return false;
        foreach ( $allowed as $entry )
        {
            list( $allowedPath, $allowedWhat ) = $entry;
            $pathMatches = substr( $allowedPath, -1 ) === '/' ? strpos( $path, $allowedPath ) === 0 : $path === $allowedPath;
            if ( $pathMatches && $what === $allowedWhat )
                return true;
        }
        return false;
    }

    /**
     * What is wrong with one entry of packEntries() below $dir (see secretFiles()), without the path.
     *
     * @param string $dir
     * @param string|false $realDir realpath( $dir )
     * @param string $relative
     * @param array $entry
     * @return string[]
     * @throws RuntimeException a pattern that failed
     */
    static function entryFindings( $dir, $realDir, $relative, array $entry )
    {
        $found = array();
        $full = $dir . '/' . $relative;
        $parts = explode( '/', $relative );
        $name = array_pop( $parts );
        if ( $entry['type'] === 'link' )
        {
            $target = realpath( $full );
            if ( $target === false || $realDir === false || ( $target !== $realDir && strpos( $target, $realDir . '/' ) !== 0 ) )
                $found['link outside the extension'] = true;
            return array_keys( $found );
        }
        if ( $entry['type'] === 'other' )
        {
            $found['not a regular file'] = true;
            return array_keys( $found );
        }
        if ( $entry['type'] !== 'file' )
            return array_keys( $found );
        if ( self::pregMatch( self::KEY_FILE_PATTERN, $name ) )
        {
            $found['key material'] = true;
            return array_keys( $found );
        }
        foreach ( $parts as $part )
        {
            if ( self::pregMatch( self::KEY_DIRECTORY_PATTERN, $part ) )
            {
                $found['key material'] = true;
                return array_keys( $found );
            }
        }
        $isSettings = ( isset( $parts[0] ) && strtolower( $parts[0] ) === 'settings' ) || self::pregMatch( '/\.ini(\.|$)/i', $name );
        if ( $isSettings && self::pregMatch( self::BACKUP_FILE_PATTERN, $name ) )
        {
            $found['copy of a settings file'] = true;
            return array_keys( $found );
        }

        // the shapes of secrets: the whole file, text or binary, as it is packed
        $shapes = self::shapesInFile( $full );
        if ( $shapes === null )
        {
            $found['unreadable'] = true;
            return array_keys( $found );
        }
        foreach ( $shapes as $kind )
            $found[$kind] = true;
        $head = (string)@file_get_contents( $full, false, null, 0, 4 );
        if ( self::pregMatch( '/^(\xFF\xFE|\xFE\xFF|\x00\x00\xFE\xFF)/', $head ) )
        {
            $found['(text in UTF-16 or UTF-32)'] = true;
            return array_keys( $found );
        }
        $pairs = $isSettings ? 'ini' : ( self::pregMatch( self::CONFIG_FILE_PATTERN, $name ) ? 'config' : false );
        if ( $pairs === false )
            return array_keys( $found );
        // settings and configuration files: the pairs, from the whole text exactly as it is packed
        $size = @filesize( $full );
        if ( $size === false || $size > self::MAX_SCAN_BYTES )
        {
            $found['text too large to check'] = true;
            return array_keys( $found );
        }
        $text = @file_get_contents( $full );
        if ( $text === false || strlen( $text ) !== $size )
        {
            $found['unreadable'] = true;
            return array_keys( $found );
        }
        if ( strpos( $text, "\0" ) !== false )
        {
            $found[$isSettings ? 'binary settings file' : 'binary configuration file'] = true;
            return array_keys( $found );
        }
        foreach ( self::pairSecretsIn( $text, $pairs ) as $setting )
            $found[$setting] = true;
        return array_keys( $found );
    }

    /**
     * The refusal text for $findings of secretFiles().
     *
     * @param string $extensionName
     * @param string[] $findings
     * @return string
     */
    static function refusalText( $extensionName, array $findings )
    {
        return ezpI18n::tr( 'kernel/package', 'The extension %name was not packed: it holds secret values or key material (%files). Copy it from the server instead.', null,
                            array( '%name' => (string)$extensionName,
                                   '%files' => implode( ', ', array_slice( $findings, 0, 5 ) ) . ( count( $findings ) > 5 ? ', ...' : '' ) ) );
    }

    /**
     * Why the extension $extensionName cannot be packed, or an empty string when it can (isPackableName() and no
     * secretFiles()). The one rule of Setup > Extensions, the package wizard, the command line and addExtension(),
     * which applies it once more to the copy it makes. Any error while checking is a refusal.
     *
     * @param string $extensionName
     * @return string
     */
    static function packRefusal( $extensionName )
    {
        try
        {
            if ( !self::isPackableName( $extensionName ) )
                return ezpI18n::tr( 'kernel/package', 'The extension %name is not known.', null, array( '%name' => (string)$extensionName ) );
            $secrets = self::secretFiles( eZExtension::baseDirectory() . '/' . $extensionName );
        }
        catch ( Exception $e )
        {
            $secrets = array( '.: could not be checked' );
        }
        if ( count( $secrets ) === 0 )
            return '';
        return self::refusalText( $extensionName, $secrets );
    }

    /**
     * Why the extension $extensionName may not be exported here, or an empty string when it may: package.ini
     * [ExtensionExportSettings] AllowedExtensions[] (when it lists any extension, only those), and with $checkPolicy
     * and RequirePolicy=enabled the policy package/export_extension of the current user. The command line asks
     * without $checkPolicy: it runs as no user.
     *
     * @param string $extensionName
     * @param bool $checkPolicy
     * @return string
     */
    static function exportRefusal( $extensionName, $checkPolicy = true )
    {
        $ini = eZINI::instance( 'package.ini' );
        $allowed = array();
        if ( $ini->hasVariable( 'ExtensionExportSettings', 'AllowedExtensions' ) )
        {
            // not array_filter() without a callback: it would drop an extension named "0"
            foreach ( (array)$ini->variable( 'ExtensionExportSettings', 'AllowedExtensions' ) as $item )
            {
                if ( is_string( $item ) && trim( $item ) !== '' )
                    $allowed[] = trim( $item );
            }
        }
        if ( count( $allowed ) > 0 && !in_array( (string)$extensionName, $allowed, true ) )
            return ezpI18n::tr( 'kernel/package', 'The extension %name may not be exported: it is not in package.ini [ExtensionExportSettings] AllowedExtensions[].', null,
                                array( '%name' => (string)$extensionName ) );
        if ( $checkPolicy && $ini->hasVariable( 'ExtensionExportSettings', 'RequirePolicy' )
             && $ini->variable( 'ExtensionExportSettings', 'RequirePolicy' ) === 'enabled' )
        {
            $access = eZUser::currentUser()->hasAccessTo( 'package', 'export_extension' );
            if ( !isset( $access['accessWord'] ) || $access['accessWord'] === 'no' )
                return ezpI18n::tr( 'kernel/package', 'Exporting an extension needs the policy package/export_extension.' );
        }
        return '';
    }

    /**
     * Adds the extension $extensionName to $package, refusing (RuntimeException) what exportRefusal() (without the
     * policy, which the callers in the administration ask) or packRefusal() names a reason for: every way to a
     * package (Setup > Extensions, the package wizard, the command line) passes here. The entries packEntries()
     * marks as packed are copied into the package, and the decision is made on that copy: secretFiles() of the
     * copy, and the copy holds nothing the walk of the source did not mark as packed. What is packed is exactly
     * what was read; on any refusal or error the copy is removed.
     *
     * @param eZPackage $package
     * @param string $extensionName
     * @throws RuntimeException
     */
    static function addExtension( $package, $extensionName )
    {
        $extensionName = (string)$extensionName;
        $refusal = static::exportRefusal( $extensionName, false );
        if ( $refusal === '' )
            $refusal = static::packRefusal( $extensionName );
        if ( $refusal !== '' )
            throw new RuntimeException( $refusal );

        $fileList = array();
        $sourceDir = eZExtension::baseDirectory() . '/' . $extensionName;
        $entries = self::packEntries( $sourceDir );
        $targetDir = $package->path() . '/ezextension';

        eZDir::mkdir( $targetDir, false, true );
        // as eZDir::copy( $sourceDir, $targetDir ) did: the extension's directory below ezextension/
        $copyDir = $targetDir . '/' . $extensionName;
        if ( file_exists( $copyDir ) || is_link( $copyDir ) )
            throw new RuntimeException( "The extension $extensionName is in the package already." );
        if ( !eZDir::mkdir( $copyDir, eZDir::directoryPermission(), false ) || !is_dir( $copyDir ) )
            throw new RuntimeException( "The extension $extensionName could not be packed." );
        try
        {
            foreach ( $entries as $relative => $entry )
            {
                $relative = (string)$relative;
                if ( !$entry['packed'] )
                    continue;
                $source = $sourceDir . '/' . $relative;
                if ( is_link( $source ) )
                    throw new RuntimeException( "The extension $extensionName changed while it was packed: $relative" );
                if ( $entry['type'] === 'dir' )
                {
                    if ( !eZDir::mkdir( $copyDir . '/' . $relative, eZDir::directoryPermission(), false ) && !is_dir( $copyDir . '/' . $relative ) )
                        throw new RuntimeException( "The directory $relative of the extension $extensionName could not be packed." );
                }
                else if ( !eZFileHandler::copy( $source, $copyDir . '/' . $relative ) )
                    throw new RuntimeException( "The file $relative of the extension $extensionName could not be packed." );
            }

            // The decision is made on the copy: the check reads what the package holds, byte for byte.
            $findings = self::secretFiles( $copyDir );
            if ( count( $findings ) > 0 )
                throw new RuntimeException( self::refusalText( $extensionName, $findings ) );
            foreach ( self::packEntries( $copyDir ) as $relative => $copied )
            {
                $relative = (string)$relative;
                if ( !isset( $entries[$relative] ) || !$entries[$relative]['packed'] || $entries[$relative]['type'] !== $copied['type'] )
                    throw new RuntimeException( "The extension $extensionName changed while it was packed: $relative" );
            }
        }
        catch ( Exception $e )
        {
            eZDir::recursiveDelete( $copyDir );
            throw $e instanceof RuntimeException ? $e : new RuntimeException( $e->getMessage(), 0, $e );
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
                $refusal = self::exportRefusal( $argument, false );
                if ( $refusal === '' )
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
