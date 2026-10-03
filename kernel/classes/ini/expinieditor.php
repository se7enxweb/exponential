<?php
/**
 * File containing the expIniEditor class: reads and changes one INI file of one scope, line by line, for
 * `console exp:ini` and anything else that edits settings.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The INI editor.
 *
 *   $scope  = expIniEditor::scope( 'siteaccess:admin' );
 *   $editor = new expIniEditor( $scope, 'site' );
 *   $editor->set( 'SiteSettings', 'SiteName', 'Exponential' );
 *   echo $editor->diff();
 *   $result = $editor->save();          // backup, atomic write, owner/group/mode kept
 *
 * Writing is line-preserving (see expIniWriter): only the touched lines change, a new variable goes at the end of
 * its block, a new block at the end of the file (before the PHP wrapper's closing line), a new file starts like the
 * files in settings/override. eZINI::save() is never used: it rewrites the whole file.
 *
 * Scopes come from the built-in providers (expIniCoreScopeProvider, expIniExtensionScopeProvider) and every class
 * named in settings/ini.ini [IniCommandSettings] ScopeProviders[] (expIniScopeProvider).
 */
class expIniEditor
{
    /** @var string The value shown instead of a secret */
    const MASK = '********';

    /** @var string|null Installation root override (tests) */
    protected static $rootOverride = null;
    /** @var array|null Active extensions override (tests) */
    protected static $activeOverride = null;
    /** @var array|null Known siteaccesses override (tests) */
    protected static $siteAccessOverride = null;
    /** @var expIniScope[]|null */
    protected static $scopeCache = null;

    /** @var expIniScope */
    protected $scope;
    /** @var string Base name, e.g. 'site' */
    protected $file;
    /** @var string Absolute path */
    protected $path;
    /** @var bool */
    protected $existed;
    /** @var string Bytes read ('' for a new file) */
    protected $original;
    /** @var expIniWriter */
    protected $writer;

    // ------------------------------------------------------------------ installation context

    /**
     * Work under another installation root (tests). null resets everything to the real installation.
     *
     * @param string|null $root
     * @param string[]|null $activeExtensions Active extensions to report (null: ask eZExtension, or none)
     * @param string[]|null $siteAccesses Siteaccesses known besides the directories (null: AvailableSiteAccessList)
     */
    public static function setRoot( $root = null, $activeExtensions = null, $siteAccesses = null )
    {
        self::$rootOverride = $root === null ? null : rtrim( $root, '/' ) . '/';
        self::$activeOverride = $activeExtensions;
        self::$siteAccessOverride = $siteAccesses;
        self::$scopeCache = null;
    }

    /** Forget the cached scope list. */
    public static function resetScopes()
    {
        self::$scopeCache = null;
    }

    /** @return string The installation root, with a trailing slash */
    public static function root()
    {
        if ( self::$rootOverride !== null )
            return self::$rootOverride;
        return self::realRoot();
    }

    /** @return string The real installation root, with a trailing slash */
    public static function realRoot()
    {
        if ( defined( 'EXP_ROOT_DIR' ) )
            return rtrim( EXP_ROOT_DIR, '/' ) . '/';
        $root = realpath( __DIR__ . '/../../..' );
        return ( $root !== false ? $root : __DIR__ . '/../../..' ) . '/';
    }

    /** @return bool Working on the real installation (not a test root) */
    public static function isRealRoot()
    {
        return self::$rootOverride === null || realpath( self::$rootOverride ) === realpath( self::realRoot() );
    }

    /** @return string[] Active extensions (empty when they cannot be determined) */
    public static function activeExtensions()
    {
        if ( self::$activeOverride !== null )
            return array_values( self::$activeOverride );
        if ( !self::isRealRoot() || !class_exists( 'eZExtension' ) )
            return array();
        try
        {
            return array_values( (array)eZExtension::activeExtensions() );
        }
        catch ( Throwable $e )
        {
            return array();
        }
    }

    /** @return string[] Siteaccesses named in site.ini [SiteAccessSettings] AvailableSiteAccessList[] */
    public static function knownSiteAccesses()
    {
        if ( self::$siteAccessOverride !== null )
            return array_values( self::$siteAccessOverride );
        if ( !self::isRealRoot() || !class_exists( 'eZINI' ) )
            return array();
        try
        {
            $ini = eZINI::instance();
            if ( $ini->hasVariable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) )
                return array_values( (array)$ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) );
        }
        catch ( Throwable $e )
        {
        }
        return array();
    }

    /**
     * A name usable as a siteaccess or extension directory: no path separators, no '..'.
     *
     * @param string $name
     * @return bool
     */
    public static function isValidName( $name )
    {
        return is_string( $name ) && preg_match( '#^[A-Za-z0-9_][A-Za-z0-9_.-]*$#', $name ) && strpos( $name, '..' ) === false;
    }

    // ------------------------------------------------------------------ scopes

    /**
     * The scope provider classes: the built-ins, then settings/ini.ini [IniCommandSettings] ScopeProviders[].
     *
     * @return string[]
     */
    public static function providerClasses()
    {
        $classes = array( 'expIniCoreScopeProvider', 'expIniExtensionScopeProvider' );
        foreach ( self::registeredProviders() as $class )
        {
            if ( !in_array( $class, $classes, true ) )
                $classes[] = $class;
        }
        return $classes;
    }

    /** @return string[] Classes named in ScopeProviders[] */
    protected static function registeredProviders()
    {
        $root = self::root();
        if ( !file_exists( $root . 'settings/ini.ini' ) )
            return array();
        $list = array();
        try
        {
            if ( self::isRealRoot() && class_exists( 'eZINI' ) )
            {
                // through eZINI, so an extension can register a provider by an ini.ini.append.php
                $ini = eZINI::instance( 'ini.ini' );
                if ( $ini->hasVariable( 'IniCommandSettings', 'ScopeProviders' ) )
                    $list = (array)$ini->variable( 'IniCommandSettings', 'ScopeProviders' );
            }
            else
            {
                $values = expIniWriter::fromFile( $root . 'settings/ini.ini' )->values();
                if ( isset( $values['IniCommandSettings']['ScopeProviders'] ) )
                    $list = (array)$values['IniCommandSettings']['ScopeProviders'];
            }
        }
        catch ( Throwable $e )
        {
            return array();
        }
        return array_values( array_filter( array_map( 'trim', $list ), 'strlen' ) );
    }

    /**
     * Every scope of this installation: global, default, siteaccesses, extensions, extension siteaccesses, then the
     * registered providers' scopes. The first scope of a name wins.
     *
     * @return expIniScope[]
     */
    public static function scopes()
    {
        if ( self::$scopeCache !== null )
            return self::$scopeCache;
        $root = self::root();
        $scopes = array();
        foreach ( self::providerClasses() as $class )
        {
            if ( !class_exists( $class ) )
            {
                self::warn( "INI scope provider class '$class' does not exist" );
                continue;
            }
            $provider = new $class();
            if ( !$provider instanceof expIniScopeProvider )
            {
                self::warn( "INI scope provider class '$class' does not implement expIniScopeProvider" );
                continue;
            }
            foreach ( (array)$provider->scopes( $root ) as $scope )
            {
                if ( $scope instanceof expIniScope && !isset( $scopes[$scope->name()] ) )
                    $scopes[$scope->name()] = $scope;
            }
        }
        return self::$scopeCache = array_values( $scopes );
    }

    /**
     * The scope for a spec: global | override | default | siteaccess:<sa> | <sa> | extension:<ext> |
     * extension:<ext>:siteaccess:<sa> | the name of a registered provider's scope.
     *
     * @param string $spec
     * @return expIniScope
     * @throws expIniException USAGE when malformed, REFUSED when the siteaccess or extension is unknown
     */
    public static function scope( $spec )
    {
        $spec = trim( (string)$spec );
        if ( $spec === '' )
            throw expIniException::usage( 'No scope given' );
        $byName = array();
        foreach ( self::scopes() as $s )
            $byName[$s->name()] = $s;
        if ( $spec === 'override' )
            $spec = 'global';
        if ( isset( $byName[$spec] ) )
            return $byName[$spec];

        $parts = explode( ':', $spec );
        if ( $parts[0] === 'siteaccess' && count( $parts ) === 2 )
        {
            if ( !self::isValidName( $parts[1] ) )
                throw expIniException::usage( "Malformed siteaccess name '{$parts[1]}'" );
            throw expIniException::refused( "Unknown siteaccess '{$parts[1]}' (not in settings/siteaccess/ nor in AvailableSiteAccessList)" );
        }
        if ( $parts[0] === 'extension' && count( $parts ) === 2 )
        {
            if ( !self::isValidName( $parts[1] ) )
                throw expIniException::usage( "Malformed extension name '{$parts[1]}'" );
            // an extension that does not exist yet: a scope whose exists() is false; save() refuses it unless
            // createScope is given
            $ext = $parts[1];
            return new expIniScope( "extension:$ext", expIniScope::KIND_EXTENSION, "extension/$ext/settings", self::root(),
                                    "Extension $ext (does not exist yet, extension/$ext/settings)",
                                    array( 'extension' => $ext, 'active' => false ) );
        }
        if ( $parts[0] === 'extension' && count( $parts ) === 4 && $parts[2] === 'siteaccess' )
        {
            list( , $ext, , $sa ) = $parts;
            if ( !self::isValidName( $ext ) || !self::isValidName( $sa ) )
                throw expIniException::usage( "Malformed scope '$spec'" );
            if ( !isset( $byName["siteaccess:$sa"] ) )
                throw expIniException::refused( "Unknown siteaccess '$sa' (not in settings/siteaccess/ nor in AvailableSiteAccessList)" );
            return expIniExtensionScopeProvider::siteAccessScope( self::root(), $ext, $sa,
                                                                  isset( $byName["extension:$ext"] ) ? $byName["extension:$ext"]->isActive() : false );
        }
        if ( count( $parts ) === 1 && self::isValidName( $spec ) )
        {
            if ( isset( $byName["siteaccess:$spec"] ) )
                return $byName["siteaccess:$spec"];
            throw expIniException::refused( "Unknown scope or siteaccess '$spec'" );
        }
        throw expIniException::usage( "Malformed scope '$spec' (global, default, siteaccess:<sa>, <sa>, extension:<ext>, extension:<ext>:siteaccess:<sa>)" );
    }

    // ------------------------------------------------------------------ setting syntax

    /**
     * Parses a setting in any accepted syntax:
     *   site.ini/SiteSettings/SiteName     site.ini [SiteSettings] SiteName     site.ini:SiteSettings.SiteName
     * The '.ini' suffix is optional; 'Var[]' is an array, 'Var[key]' a hash entry. 'site', 'site/Block',
     * 'site [Block]' and 'site:Block' are accepted with no variable (block and variable null).
     *
     * @param string $text
     * @return array file, block, variable, kind (plain|array|hash|null), key
     * @throws expIniException USAGE
     */
    public static function parseSetting( $text )
    {
        $text = trim( (string)$text );
        $file = '[A-Za-z0-9_][A-Za-z0-9_.-]*?';
        $var = '([\w*@-]+)(\[([^\]\r\n]*)\])?';
        $m = null;
        $result = null;
        if ( preg_match( "#^($file)(?:\.ini)?/(.+)/$var$#", $text, $m ) )
            $result = array( $m[1], $m[2], $m[3], isset( $m[4] ) ? $m[4] : '', isset( $m[5] ) ? $m[5] : null );
        else if ( preg_match( "#^($file)(?:\.ini)?\s*\[([^\]\r\n]+)\]\s*$var$#", $text, $m ) )
            $result = array( $m[1], $m[2], $m[3], isset( $m[4] ) ? $m[4] : '', isset( $m[5] ) ? $m[5] : null );
        else if ( preg_match( "#^($file)(?:\.ini)?:(.+)\.$var$#", $text, $m ) )
            $result = array( $m[1], $m[2], $m[3], isset( $m[4] ) ? $m[4] : '', isset( $m[5] ) ? $m[5] : null );
        else if ( preg_match( "#^($file)(?:\.ini)?(?:/([^/\r\n]+)|\s*\[([^\]\r\n]+)\]|:([^\r\n]+))?$#", $text, $m ) )
        {
            $block = null;
            foreach ( array( 2, 3, 4 ) as $i )
            {
                if ( isset( $m[$i] ) && $m[$i] !== '' )
                    $block = trim( $m[$i] );
            }
            return array( 'file' => expIniScope::baseName( $m[1] ), 'block' => $block, 'variable' => null,
                          'kind' => null, 'key' => null );
        }
        if ( $result === null )
            throw expIniException::usage( "Cannot read the setting '$text' (site.ini/Block/Variable, site.ini [Block] Variable or site.ini:Block.Variable)" );

        list( $fileName, $block, $variable, $bracket, $key ) = $result;
        $block = trim( $block );
        self::checkBlock( $block );
        $kind = 'plain';
        if ( $bracket !== '' )
            $kind = ( $key === null || $key === '' ) ? 'array' : 'hash';
        return array( 'file' => expIniScope::baseName( $fileName ), 'block' => $block, 'variable' => $variable,
                      'kind' => $kind, 'key' => $kind === 'hash' ? $key : null );
    }

    // ------------------------------------------------------------------ secrets and toggles

    /**
     * Whether a variable holds a secret whose value is masked unless asked for.
     * Case-insensitive substrings password, passwd, passphrase, secret, token, salt, credential, privatekey,
     * apikey; or a name that is 'key' or ends in 'Key', '_key', '-key' (ApiKey, LicenseKey) -- except a few
     * well-known names that are not secrets (SortKey, CacheKey, PrimaryKey, ...). A name only starting with Key
     * (KeyField, KeywordList) is not a secret.
     *
     * @param string $variable
     * @return bool
     */
    public static function isSecret( $variable )
    {
        $v = (string)$variable;
        $lower = strtolower( $v );
        foreach ( array( 'password', 'passwd', 'passphrase', 'secret', 'token', 'salt', 'credential', 'privatekey', 'apikey' ) as $word )
        {
            if ( strpos( $lower, $word ) !== false )
                return true;
        }
        $notSecret = array( 'sortkey', 'cachekey', 'primarykey', 'foreignkey', 'indexkey', 'shortcutkey', 'hotkey',
                            'groupkey', 'sectionkey', 'languagekey', 'idkey' );
        if ( in_array( $lower, $notSecret, true ) )
            return false;
        if ( $lower === 'key' )
            return true;
        // ApiKey, LicenseKey, license_key, LICENSE-KEY; not KeyField, Keywords, MonkeyList
        return (bool)preg_match( '#[a-z0-9]Key$#', $v ) || (bool)preg_match( '#[_-]key$#i', $v );
    }

    /**
     * The masked form of a value: '********' for a non-empty string, every value masked (keys kept) for an array.
     *
     * @param string|array|null $value
     * @return string|array|null
     */
    public static function maskValue( $value )
    {
        if ( is_array( $value ) )
            return array_map( array( __CLASS__, 'maskValue' ), $value );
        if ( $value === null || $value === '' )
            return $value;
        return self::MASK;
    }

    /** @var array Toggle pairs, lower case */
    protected static $togglePairs = array(
        'enabled' => 'disabled', 'disabled' => 'enabled',
        'true' => 'false', 'false' => 'true',
        'yes' => 'no', 'no' => 'yes',
        'on' => 'off', 'off' => 'on',
        '1' => '0', '0' => '1',
        'enable' => 'disable', 'disable' => 'enable',
    );

    /**
     * The flipped value, in the original's case style (enabled -> disabled, TRUE -> FALSE, Yes -> No, 1 -> 0).
     *
     * @param string $value
     * @return string
     * @throws expIniException REFUSED when the value is not one of the pairs
     */
    public static function toggleValue( $value )
    {
        if ( !is_string( $value ) && !is_int( $value ) )
            throw expIniException::refused( 'Only a plain value can be toggled, this one is an array' );
        $value = (string)$value;
        $trimmed = trim( $value );
        $lower = strtolower( $trimmed );
        if ( !isset( self::$togglePairs[$lower] ) )
            throw expIniException::refused( "Cannot toggle '$value' (enabled/disabled, true/false, yes/no, on/off, 1/0)" );
        $new = self::$togglePairs[$lower];
        if ( $trimmed === strtoupper( $trimmed ) && $trimmed !== $lower )
            $new = strtoupper( $new );
        else if ( $trimmed === ucfirst( $lower ) && $trimmed !== $lower )
            $new = ucfirst( $new );
        return $new;
    }

    // ------------------------------------------------------------------ the file

    /**
     * @param expIniScope $scope
     * @param string $file 'site', 'site.ini'
     * @throws expIniException USAGE for a bad file name, WRITE_FAILED when it exists and cannot be read
     */
    public function __construct( expIniScope $scope, $file )
    {
        $base = expIniScope::baseName( $file );
        if ( strpbrk( (string)$file, "/\\\0" ) !== false || !self::isValidName( $base ) )
            throw expIniException::usage( "Malformed INI file name '$file'" );
        $this->scope = $scope;
        $this->file = $base;
        $this->path = $scope->path( $base );
        $this->existed = is_file( $this->path );
        if ( $this->existed )
        {
            $this->writer = expIniWriter::fromFile( $this->path );
            $this->original = $this->writer->content();
        }
        else
        {
            $this->original = '';
            $this->writer = substr( $this->path, -4 ) === '.php' ? expIniWriter::newPhpWrapped() : new expIniWriter( '' );
        }
    }

    /** @return expIniScope */
    public function getScope() { return $this->scope; }
    /** @return string Base name, e.g. 'site' */
    public function file() { return $this->file; }
    /** @return string Absolute path */
    public function path() { return $this->path; }
    /** @return string Path relative to the installation root */
    public function relativePath() { return $this->scope->relativePath( $this->file ); }
    /** @return bool The file exists on disk */
    public function fileExists() { return $this->existed; }
    /** @return string Bytes as read */
    public function originalContent() { return $this->original; }

    /** @return string Bytes with the pending changes */
    public function content()
    {
        if ( !$this->existed && !$this->writer->blocks() )
            return '';
        return $this->writer->content();
    }

    /** @return bool There are pending changes */
    public function hasChanges()
    {
        return $this->content() !== $this->original;
    }

    /**
     * The value in THIS file, read as eZINI reads this file alone.
     *
     * @param string $block
     * @param string $variable
     * @return string|array|null null when this file does not set it
     */
    public function get( $block, $variable )
    {
        $values = $this->writer->values();
        if ( isset( $values[$block] ) && array_key_exists( $variable, $values[$block] ) )
            return $values[$block][$variable];
        return null;
    }

    /** @return string[] Blocks of this file */
    public function blocks()
    {
        return $this->writer->blocks();
    }

    /**
     * @param string $block
     * @return array variable => value, as set in this file
     */
    public function variables( $block )
    {
        $values = $this->writer->values();
        return isset( $values[$block] ) ? $values[$block] : array();
    }

    /**
     * Sets a plain variable, or one hash entry when $key is given.
     *
     * @param string $block
     * @param string $variable
     * @param string $value
     * @param string|null $key
     * @return bool Changed (false: the file already says so)
     * @throws expIniException USAGE
     */
    public function set( $block, $variable, $value, $key = null )
    {
        $this->check( $block, $variable, $value, $key );
        $w = $this->writer;
        if ( $key !== null && $key !== '' )
        {
            if ( $w->variableLines( $block, $variable, 'plain' ) )
                throw expIniException::usage( "$block/$variable is a plain value in {$this->relativePath()}, not a hash" );
            $lines = array();
            foreach ( $w->variableLines( $block, $variable, 'hash' ) as $i )
            {
                if ( $w->entry( $i )['key'] === (string)$key )
                    $lines[] = $i;
            }
            if ( $lines )
            {
                $last = end( $lines );
                if ( $w->entry( $last )['value'] === (string)$value )
                    return false;
                $w->replaceValue( $last, (string)$value );
                return true;
            }
            $this->insertForVariable( $block, $variable, array( "{$variable}[{$key}]={$value}" ) );
            return true;
        }

        $arrayLines = array_merge( $w->variableLines( $block, $variable, 'reset' ), $w->variableLines( $block, $variable, 'append' ),
                                   $w->variableLines( $block, $variable, 'hash' ) );
        if ( $arrayLines )
            throw expIniException::usage( "$block/$variable is an array in {$this->relativePath()}: use add, clear or rem, or set a hash key" );
        $plain = $w->variableLines( $block, $variable, 'plain' );
        if ( $plain )
        {
            $last = end( $plain );
            if ( $w->entry( $last )['value'] === (string)$value )
                return false;
            $w->replaceValue( $last, (string)$value );
            return true;
        }
        $w->insertInBlock( $block, array( "$variable=$value" ) );
        return true;
    }

    /**
     * Appends a value to an array (Var[]=value).
     *
     * @param string $block
     * @param string $variable
     * @param string $value
     * @return bool Changed (false: the value is already in this file's array, after its last reset)
     * @throws expIniException USAGE
     */
    public function add( $block, $variable, $value )
    {
        $this->check( $block, $variable, $value );
        $w = $this->writer;
        if ( $w->variableLines( $block, $variable, 'plain' ) )
            throw expIniException::usage( "$block/$variable is a plain value in {$this->relativePath()}, not an array" );
        $current = $this->get( $block, $variable );
        if ( is_array( $current ) && in_array( (string)$value, array_map( 'strval', $current ), true ) )
            return false;
        $this->insertForVariable( $block, $variable, array( "{$variable}[]={$value}" ) );
        return true;
    }

    /**
     * Removes the whole variable (every line of it in the block), one array value (every Var[]=value line with that
     * value) or one hash key. The block stays, even when it becomes empty.
     *
     * @param string $block
     * @param string $variable
     * @param string|null $value
     * @param string|null $key
     * @return bool true
     * @throws expIniException NOT_FOUND when nothing matches
     */
    public function remove( $block, $variable, $value = null, $key = null )
    {
        $this->check( $block, $variable );
        $w = $this->writer;
        $where = $this->relativePath();
        if ( !$w->hasBlock( $block ) )
            throw expIniException::notFound( "No block [$block] in $where" );
        $remove = array();
        if ( $key !== null && $key !== '' )
        {
            foreach ( $w->variableLines( $block, $variable, 'hash' ) as $i )
            {
                if ( $w->entry( $i )['key'] === (string)$key )
                    $remove[] = $i;
            }
            if ( !$remove )
                throw expIniException::notFound( "No {$variable}[{$key}] in [$block] of $where" );
        }
        else if ( $value !== null )
        {
            foreach ( $w->variableLines( $block, $variable, 'append' ) as $i )
            {
                if ( $w->entry( $i )['value'] === (string)$value )
                    $remove[] = $i;
            }
            if ( !$remove )
                throw expIniException::notFound( "No {$variable}[]={$value} in [$block] of $where (only values set in this file can be removed; use clear and add to replace an inherited array)" );
        }
        else
        {
            $remove = $w->variableLines( $block, $variable );
            if ( !$remove )
                throw expIniException::notFound( "No $variable in [$block] of $where" );
        }
        $w->removeLines( $remove );
        return true;
    }

    /**
     * Removes a block: every occurrence's header and setting lines (comments before the next block stay with the
     * next block), and the blank line that separated it when two blank lines would otherwise meet. The undo of a
     * set() that created the block, byte for byte.
     *
     * @param string $block
     * @param bool $onlyIfEmpty Refuse (USAGE) when the block still has settings in this file
     * @return bool true
     * @throws expIniException NOT_FOUND when the block is not in the file, USAGE (see $onlyIfEmpty)
     */
    public function removeBlock( $block, $onlyIfEmpty = false )
    {
        self::checkBlock( $block );
        $w = $this->writer;
        if ( !$w->hasBlock( $block ) )
            throw expIniException::notFound( "No block [$block] in {$this->relativePath()}" );
        if ( $onlyIfEmpty && $this->variables( $block ) )
            throw expIniException::usage( "[$block] in {$this->relativePath()} still has settings" );
        while ( $regions = $w->blockRegions( $block ) )
        {
            list( $header, $end ) = $regions[0];
            $lines = $w->displayLines();
            $remove = range( $header, $end );
            $before = $header - 1;
            $after = $end + 1;
            $afterBlank = !isset( $lines[$after] ) || trim( $lines[$after] ) === '' || $after >= $w->endOfSettings();
            if ( $before >= 0 && trim( $lines[$before] ) === '' && $afterBlank )
                array_unshift( $remove, $before );
            $w->removeLines( $remove );
        }
        return true;
    }

    /**
     * The lines of a block exactly as in the file (no line ends): everything after the header -- settings,
     * comments, blank lines -- up to the next block's header (without the comment lines directly above that
     * header, which describe the next block) or to the closing lines of the PHP wrapper. A block that occurs more
     * than once gives the lines of every occurrence, in order.
     *
     * @param string $block
     * @return string[]|null null when the block is not in the file
     */
    public function blockLines( $block )
    {
        $regions = $this->writer->blockRegions( $block );
        if ( !$regions )
            return null;
        $lines = $this->writer->displayLines();
        $out = array();
        foreach ( $regions as $r )
            for ( $i = $r[0] + 1; $i <= $r[1]; ++$i )
                $out[] = $lines[$i];
        return $out;
    }

    /**
     * Appends raw lines (as blockLines() gives them) to a block: after its last setting, or as a new block at the
     * end of the file. Trailing blank lines and PHP wrapper lines ('<?php ...', '*' . '/ ?>', '?>') are dropped (the
     * writer keeps the separators and the wrapper). A line that would
     * start another block, or that contains a line break, NUL or (in a PHP-wrapped file) the end of the PHP
     * comment, is refused.
     *
     * @param string $block
     * @param string[] $lines
     * @return bool Changed
     * @throws expIniException USAGE
     */
    public function insertBlockLines( $block, array $lines )
    {
        self::checkBlock( $block );
        $lines = array_values( array_map( 'strval', $lines ) );
        // a PHP wrapper line inside a block region (a few real files close the comment early and go on) is not
        // part of the block: eZINI ignores it, and it must not be copied into the middle of another file
        $lines = array_values( array_filter( $lines, function ( $l ) {
            return !preg_match( '#^\s*((\*\s*)?\*/\s*(\?>)?|\?>|<\?(php)?.*)\s*$#', $l );
        } ) );
        while ( $lines && trim( end( $lines ) ) === '' )
            array_pop( $lines );
        // only a file PHP may execute (*.php) must not close its comment early
        $php = substr( $this->path, -4 ) === '.php';
        foreach ( $lines as $line )
        {
            if ( strpbrk( $line, "\r\n\0" ) !== false )
                throw expIniException::usage( 'A block line cannot contain a line break or a NUL byte' );
            $core = preg_match( "/^(.+)##.*/", $line, $m ) ? $m[1] : $line;
            if ( $line !== '' && $line[0] !== '#' && preg_match( "#^\[(.+)\]\s*$#", $core ) )
                throw expIniException::usage( "A block line cannot start another block: '$line'" );
            if ( $php && strpos( $line, '*/' ) !== false )
                throw expIniException::usage( "A block line cannot contain '*/' in a PHP-wrapped INI file" );
        }
        if ( !$lines )
        {
            if ( $this->writer->hasBlock( $block ) )
                return false;
            $this->writer->insertInBlock( $block, array() );
            return true;
        }
        $this->writer->insertInBlock( $block, $lines );
        return true;
    }

    /**
     * Nothing but the PHP wrapper, comments and blank lines: no block, no setting.
     *
     * @return bool
     */
    public function isEmpty()
    {
        foreach ( $this->writer->entries() as $e )
        {
            if ( in_array( $e['type'], array( 'block', 'reset', 'plain', 'append', 'hash' ), true ) )
                return false;
        }
        return true;
    }

    /**
     * Makes the variable an empty array in this file: its lines in the block become one 'Var[]' line.
     *
     * @param string $block
     * @param string $variable
     * @return bool Changed
     */
    public function clearArray( $block, $variable )
    {
        $this->check( $block, $variable );
        $w = $this->writer;
        $lines = $w->variableLines( $block, $variable );
        if ( count( $lines ) === 1 && $w->entry( $lines[0] )['type'] === 'reset' )
            return false;
        if ( !$lines )
        {
            $w->insertInBlock( $block, array( "{$variable}[]" ) );
            return true;
        }
        $first = $lines[0];
        $w->removeLines( $lines );
        $w->insertLines( $first, array( "{$variable}[]" ) );
        return true;
    }

    /**
     * Flips the value (enabled/disabled, true/false, yes/no, on/off, 1/0), keeping its case style. The value is
     * this file's, or -- when this file does not set it -- the effective value in this scope's context, and the new
     * value is written to this file.
     *
     * @param string $block
     * @param string $variable
     * @return string The new value
     * @throws expIniException NOT_FOUND when there is no value, REFUSED when it cannot be toggled
     */
    public function toggle( $block, $variable )
    {
        $this->check( $block, $variable );
        $current = $this->get( $block, $variable );
        if ( $current === null )
            $current = self::effectiveValue( $this->file, $block, $variable, $this->scope->siteAccess() );
        if ( $current === null )
            throw expIniException::notFound( "$block/$variable is not set, nothing to toggle" );
        $new = self::toggleValue( $current );
        $this->set( $block, $variable, $new );
        return $new;
    }

    /**
     * The effective value of a variable for a siteaccess (null: the current context).
     *
     * On the real installation this is eZINI's own value (expIniLocator::where()). Under another root (setRoot(),
     * tests, `--root`) eZINI cannot be pointed there, so the files are read in the kernel's order for the scopes
     * that exist there: settings/<file>.ini, the active extensions (the last one in ActiveExtensions first, as
     * eZExtension prepends them), settings/siteaccess/<sa>, the extensions' siteaccess directories, then
     * settings/override.
     *
     * @param string $file
     * @param string $block
     * @param string $variable
     * @param string|null $siteAccess
     * @return string|array|null
     */
    public static function effectiveValue( $file, $block, $variable, $siteAccess = null )
    {
        if ( self::isRealRoot() && class_exists( 'eZINI' ) )
        {
            try
            {
                $where = expIniLocator::where( $file, $block, $variable, $siteAccess );
                return $where['effective'];
            }
            catch ( Throwable $e )
            {
                return null;
            }
        }
        $order = array( 'default' );
        foreach ( array_reverse( self::activeExtensions() ) as $ext )
            $order[] = "extension:$ext";
        if ( $siteAccess !== null )
        {
            $order[] = "siteaccess:$siteAccess";
            foreach ( self::activeExtensions() as $ext )
                $order[] = "extension:$ext:siteaccess:$siteAccess";
        }
        $order[] = 'global';
        $byName = array();
        foreach ( self::scopes() as $s )
            $byName[$s->name()] = $s;
        $values = array();
        foreach ( $order as $name )
        {
            if ( !isset( $byName[$name] ) || !is_file( $byName[$name]->path( $file ) ) )
                continue;
            $fileValues = expIniWriter::fromFile( $byName[$name]->path( $file ) )->values();
            foreach ( $fileValues as $b => $vars )
                foreach ( $vars as $v => $value )
                {
                    if ( is_array( $value ) && isset( $values[$b][$v] ) && is_array( $values[$b][$v] ) && !self::resetsArray( $byName[$name]->path( $file ), $b, $v ) )
                        $values[$b][$v] = array_merge( $values[$b][$v], $value );
                    else
                        $values[$b][$v] = $value;
                }
        }
        return isset( $values[$block] ) && array_key_exists( $variable, $values[$block] ) ? $values[$block][$variable] : null;
    }

    /** Whether a file has a 'Var[]' reset line for the variable. */
    protected static function resetsArray( $path, $block, $variable )
    {
        $w = expIniWriter::fromFile( $path );
        return (bool)$w->variableLines( $block, $variable, 'reset' );
    }

    /**
     * The unified diff of the pending changes, like `diff -u` ('' when nothing changed).
     *
     * @return string
     */
    public function diff()
    {
        $new = $this->content();
        if ( $new === $this->original )
            return '';
        $rel = $this->relativePath();
        return self::unifiedDiff( $this->original, $new, $this->existed ? "a/$rel" : '/dev/null', "b/$rel" );
    }

    /**
     * Writes the pending changes.
     *
     * Options: dryRun (false) -- only report; backup (true) -- copy the old file to
     * <backupRoot>/<Ymd-His>/<relative path> first; backupRoot (<root>var/backup/ini); allowDefault (false) -- may
     * write the default scope (settings/*.ini); create (true) -- may create a missing file and directories.
     *
     * The write is atomic (a temporary file in the same directory, then rename) and keeps the file's owner, group
     * and mode; a new file and new directories get the owner and group of the nearest existing parent directory.
     * Owner and group are only changed when running as root; not being able to keep them is a warning, not an
     * error. When not root and the file belongs to someone else, it is written in place (still locked) so its owner
     * stays.
     *
     * @param array $options
     * @return expIniWriteResult
     * @throws expIniException REFUSED, NOT_FOUND, WRITE_FAILED
     */
    public function save( array $options = array() )
    {
        $options += array( 'dryRun' => false, 'backup' => true, 'backupRoot' => self::root() . 'var/backup/ini',
                           'allowDefault' => false, 'create' => true, 'createScope' => false );
        $data = array( 'path' => $this->path, 'relativePath' => $this->relativePath(), 'created' => !$this->existed,
                       'dryRun' => (bool)$options['dryRun'], 'warnings' => array() );
        if ( !$this->scope->policyWritable() && !$options['allowDefault'] )
            throw expIniException::refused( "The scope '{$this->scope->name()}' ({$this->relativePath()}) holds the shipped defaults and is not written unless allowed (--allow-default)" );
        if ( !$this->hasChanges() )
        {
            $data['created'] = false;
            return new expIniWriteResult( $data );
        }
        if ( !$this->existed && !$options['create'] )
            throw expIniException::notFound( "{$this->relativePath()} does not exist (and creating it was not allowed)" );
        $data['changed'] = true;
        $data['diff'] = $this->diff();
        $ext = $this->scope->extension();
        $missingExtension = $ext !== null && !is_dir( $this->scope->root() . 'extension/' . $ext );
        if ( $options['dryRun'] )
        {
            if ( $missingExtension && !$options['createScope'] )
                $data['warnings'][] = "The extension '$ext' does not exist yet: the write needs createScope";
            return new expIniWriteResult( $data );
        }
        if ( $missingExtension && !$options['createScope'] )
            throw expIniException::refused( "The extension '$ext' does not exist (no directory extension/$ext); creating it was not allowed (createScope)" );

        $new = $this->content();
        $existsNow = is_file( $this->path );
        if ( $existsNow !== $this->existed || ( $existsNow && @file_get_contents( $this->path ) !== $this->original ) )
            throw expIniException::writeFailed( "{$this->relativePath()} changed on disk since it was read; nothing written" );

        // [AuditSettings] OnWriteFailure=refuse: no settings write while the audit cannot record it
        // (system.setting.write; doc/bc/6.0/audit.md, "When the audit cannot write"). Writes in a fixture root
        // are not recorded, so they are not guarded either.
        if ( class_exists( 'expAuditGuard' ) && ( self::isRealRoot() || ( class_exists( 'expAuditConfig' ) && expAuditConfig::isOverridden() ) )
             && !expAuditGuard::allows( 'system.setting.write' ) )
            throw expIniException::refused( expAuditGuard::message() . " Nothing written to {$this->relativePath()}." );

        $dir = dirname( $this->path );
        self::makeDirectory( $dir, $data['warnings'] );

        if ( $this->existed && $options['backup'] )
            $data['backup'] = self::backup( $this->path, $this->relativePath(), $options['backupRoot'], $data['warnings'] );

        self::writeFile( $this->path, $new, $this->existed, $data['warnings'] );

        if ( @file_get_contents( $this->path ) !== $new )
            throw expIniException::writeFailed( "{$this->relativePath()} does not read back as written" );
        $data['written'] = true;
        // system.setting.write per changed variable (exp:ini, the debug bar, everything else writing through this
        // editor); secrets are recorded as [secret]. Never throws.
        if ( class_exists( 'expAudit' ) )
            expAudit::settingWrite( $this->file . '.ini', $this->scope->name(), $this->relativePath(), $this->original, $new, $data['diff'] );
        $this->original = $new;
        $this->existed = true;
        $this->writer = new expIniWriter( $new );
        return new expIniWriteResult( $data );
    }

    // ------------------------------------------------------------------ helpers

    /**
     * Inserts lines after the last line of the variable in the block, or at the end of the block.
     */
    protected function insertForVariable( $block, $variable, array $texts )
    {
        $lines = $this->writer->variableLines( $block, $variable );
        if ( $lines )
            $this->writer->insertLines( end( $lines ) + 1, $texts );
        else
            $this->writer->insertInBlock( $block, $texts );
    }

    /**
     * Validates names and the value.
     *
     * @throws expIniException USAGE
     */
    protected function check( $block, $variable, $value = null, $key = null )
    {
        self::checkBlock( $block );
        if ( !is_string( $variable ) || !preg_match( '#^[\w*@-]+$#', $variable ) )
            throw expIniException::usage( "Malformed variable name '$variable'" );
        if ( $key !== null && $key !== '' )
        {
            if ( !is_scalar( $key ) || strpbrk( (string)$key, "]\r\n\0" ) !== false )
                throw expIniException::usage( "Malformed hash key '$key'" );
            if ( (string)$key === '0' )
                throw expIniException::usage( "The hash key '0' cannot be written: eZINI reads {$variable}[0]= as an array append" );
        }
        if ( $value !== null )
        {
            if ( !is_scalar( $value ) )
                throw expIniException::usage( 'A value must be a string' );
            $value = (string)$value;
            if ( strpbrk( $value, "\r\n\0" ) !== false )
                throw expIniException::usage( 'A value cannot contain a line break or a NUL byte' );
            if ( strpos( $value, '##' ) !== false )
                throw expIniException::usage( "A value cannot contain '##': eZINI reads the rest of the line as a comment" );
            if ( substr( $this->path, -4 ) === '.php' && strpos( $value, '*/' ) !== false )
                throw expIniException::usage( "A value cannot contain '*/' in a PHP-wrapped INI file" );
        }
    }

    /**
     * @param string $block
     * @throws expIniException USAGE
     */
    protected static function checkBlock( $block )
    {
        if ( !is_string( $block ) || trim( $block ) === '' || trim( $block ) !== $block
             || strpbrk( $block, "]\r\n\0" ) !== false || strpos( $block, '##' ) !== false )
            throw expIniException::usage( "Malformed block name '$block'" );
    }

    /** @return bool Running as root */
    public static function isRoot()
    {
        return function_exists( 'posix_geteuid' ) && posix_geteuid() === 0;
    }

    /**
     * Applies an owner and group (only as root; as anyone else only the group, and only if allowed).
     *
     * @return bool Owner and group are as asked
     */
    protected static function applyOwnership( $path, $uid, $gid )
    {
        clearstatcache( true, $path );
        if ( @fileowner( $path ) === $uid && @filegroup( $path ) === $gid )
            return true;
        if ( self::isRoot() )
        {
            $ok = @chown( $path, $uid );
            $ok = @chgrp( $path, $gid ) && $ok;
            return $ok;
        }
        if ( @filegroup( $path ) !== $gid )
            @chgrp( $path, $gid );
        clearstatcache( true, $path );
        return @fileowner( $path ) === $uid && @filegroup( $path ) === $gid;
    }

    /**
     * Creates a directory and its missing parents, each owned like the nearest existing parent, with its mode.
     *
     * @param string $dir
     * @param string[] $warnings
     * @throws expIniException WRITE_FAILED
     */
    protected static function makeDirectory( $dir, array &$warnings )
    {
        if ( is_dir( $dir ) )
            return;
        $missing = array();
        $parent = $dir;
        while ( !is_dir( $parent ) )
        {
            $missing[] = $parent;
            $next = dirname( $parent );
            if ( $next === $parent )
                throw expIniException::writeFailed( "No existing parent directory for $dir" );
            $parent = $next;
        }
        clearstatcache( true, $parent );
        $uid = fileowner( $parent );
        $gid = filegroup( $parent );
        $mode = fileperms( $parent ) & 0777;
        foreach ( array_reverse( $missing ) as $d )
        {
            if ( !@mkdir( $d, $mode ) && !is_dir( $d ) )
                throw expIniException::writeFailed( "Cannot create the directory $d" );
            @chmod( $d, $mode );
            if ( !self::applyOwnership( $d, $uid, $gid ) )
                $warnings[] = "The new directory $d could not be given the owner of " . $parent . ' (not running as root)';
        }
    }

    /**
     * Copies a file to <backupRoot>/<Ymd-His>[-N]/<relative path>, never over an earlier backup.
     *
     * @return string The backup's path
     * @throws expIniException WRITE_FAILED
     */
    protected static function backup( $path, $relativePath, $backupRoot, array &$warnings )
    {
        $backupRoot = rtrim( $backupRoot, '/' );
        $stamp = date( 'Ymd-His' );
        $n = 1;
        do
        {
            $target = $backupRoot . '/' . $stamp . ( $n > 1 ? "-$n" : '' ) . '/' . $relativePath;
            ++$n;
        }
        while ( file_exists( $target ) );
        // owner and group of the directory above the backup root (var/backup), or of the one above that (var):
        // a later run as the site user must still be able to create backups
        $reference = dirname( $backupRoot );
        if ( !is_dir( $reference ) )
            $reference = dirname( $reference );
        clearstatcache();
        $uid = @fileowner( $reference );
        $gid = @filegroup( $reference );
        $dir = dirname( $target );
        $chain = array();
        for ( $d = $dir; strlen( $d ) >= strlen( $backupRoot ); $d = dirname( $d ) )
        {
            $chain[] = $d;
            if ( $d === $backupRoot )
                break;
        }
        $chain = array_reverse( $chain );
        $missing = array();
        for ( $d = dirname( $backupRoot ); !is_dir( $d ); $d = dirname( $d ) )
            $missing[] = $d;
        foreach ( array_merge( array_reverse( $missing ), $chain ) as $d )
        {
            $created = false;
            if ( !is_dir( $d ) )
            {
                if ( !@mkdir( $d, 0700 ) && !is_dir( $d ) )
                    throw expIniException::writeFailed( "Cannot create the backup directory $d; nothing written" );
                @chmod( $d, 0700 );
                $created = true;
            }
            // as root, the backup tree itself is kept in the site's hands (also when an earlier root run made it)
            if ( $uid !== false && ( $created || self::isRoot() ) && !self::applyOwnership( $d, $uid, $gid ) && $created )
                $warnings[] = "The backup directory $d could not be given the owner of $reference (not running as root)";
        }
        if ( !@copy( $path, $target ) )
            throw expIniException::writeFailed( "Cannot back up $path to $target; nothing written" );
        // the backup can hold secrets (settings/override): readable by its owner only
        @chmod( $target, 0600 );
        if ( $uid !== false && self::isRoot() )
            self::applyOwnership( $target, $uid, $gid );
        return $target;
    }

    /**
     * Writes a file atomically, keeping (or, for a new file, giving it) owner, group and mode.
     *
     * @throws expIniException WRITE_FAILED
     */
    protected static function writeFile( $path, $content, $existed, array &$warnings )
    {
        $dir = dirname( $path );
        clearstatcache();
        if ( $existed )
        {
            $uid = fileowner( $path );
            $gid = filegroup( $path );
            $mode = fileperms( $path ) & 07777;
        }
        else
        {
            $uid = fileowner( $dir );
            $gid = filegroup( $dir );
            $mode = 0644;
        }

        $runner = function_exists( 'posix_geteuid' ) ? posix_geteuid() : null;
        if ( $existed && !self::isRoot() && $runner !== null && $uid !== $runner )
        {
            // renaming over it would hand the file to this user: write it in place instead, locked
            self::writeInPlace( $path, $content, $warnings, 'to keep its owner' );
            return;
        }

        // Not root, owner is this user, but the file's group is not one this user belongs to: a chgrp of the
        // temporary file would fail and the rename would hand the file to this user's primary group. Rewrite
        // the original inode instead (not atomic, but owner, group and mode stay as they are).
        if ( $existed && !self::isRoot() && !self::canUseGroup( $gid ) )
        {
            self::writeInPlace( $path, $content, $warnings, "its group $gid cannot be set by this user" );
            return;
        }

        $tmp = $dir . '/.' . basename( $path ) . '.expini-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) ) . '.tmp';
        $h = @fopen( $tmp, 'x' );
        if ( !$h )
            throw expIniException::writeFailed( "Cannot create a temporary file in $dir" );
        $ok = fwrite( $h, $content ) === strlen( $content );
        $ok = fflush( $h ) && $ok;
        if ( function_exists( 'fsync' ) )
            @fsync( $h );
        fclose( $h );
        if ( !$ok )
        {
            self::moveAside( $tmp );
            throw expIniException::writeFailed( "Cannot write the temporary file for $path" );
        }
        @chmod( $tmp, $mode );
        if ( !self::applyOwnership( $tmp, $uid, $gid ) )
        {
            if ( $existed && !self::isRoot() )
            {
                // chgrp was refused after all: keep the original inode, and with it the group
                self::moveAside( $tmp );
                self::writeInPlace( $path, $content, $warnings, "the group $gid could not be set on a new file" );
                return;
            }
            $warnings[] = "$path could not be given owner $uid / group $gid (not running as root)";
        }
        if ( !@rename( $tmp, $path ) )
        {
            self::moveAside( $tmp );
            throw expIniException::writeFailed( "Cannot move the new $path into place" );
        }
    }

    /**
     * @return bool This process may give a file the group (root, its primary group, or a supplementary one)
     */
    protected static function canUseGroup( $gid )
    {
        if ( !function_exists( 'posix_getegid' ) )
            return true;
        if ( $gid === posix_getegid() )
            return true;
        return function_exists( 'posix_getgroups' ) && in_array( $gid, posix_getgroups(), true );
    }

    /**
     * Rewrites a file's own inode, locked: owner, group and mode cannot change. Not atomic.
     *
     * @throws expIniException WRITE_FAILED
     */
    protected static function writeInPlace( $path, $content, array &$warnings, $why )
    {
        if ( !is_writable( $path ) )
            throw expIniException::writeFailed( "$path is not writable by this user" );
        if ( @file_put_contents( $path, $content, LOCK_EX ) !== strlen( $content ) )
            throw expIniException::writeFailed( "Cannot write $path" );
        $warnings[] = "$path written in place (not atomically): $why (not running as root)";
    }

    /**
     * A temporary file that could not be used is moved into var/tmp/ (nothing is deleted).
     */
    protected static function moveAside( $tmp )
    {
        $aside = self::root() . 'var/tmp/ini-failed';
        if ( !is_dir( $aside ) )
            @mkdir( $aside, 0700, true );
        @rename( $tmp, $aside . '/' . basename( $tmp ) );
    }

    /**
     * A unified diff of two texts (3 lines of context), like `diff -u`.
     *
     * @param string $old
     * @param string $new
     * @param string $labelOld
     * @param string $labelNew
     * @return string
     */
    public static function unifiedDiff( $old, $new, $labelOld = 'a', $labelNew = 'b' )
    {
        if ( $old === $new )
            return '';
        $a = $old === '' ? array() : explode( "\n", $old );
        $b = $new === '' ? array() : explode( "\n", $new );
        // a final newline is a terminator, not an empty line
        $aNl = $old !== '' && substr( $old, -1 ) === "\n";
        $bNl = $new !== '' && substr( $new, -1 ) === "\n";
        if ( $aNl )
            array_pop( $a );
        if ( $bNl )
            array_pop( $b );
        $a = array_map( function ( $l ) { return rtrim( $l, "\r" ); }, $a );
        $b = array_map( function ( $l ) { return rtrim( $l, "\r" ); }, $b );
        // a last line without a newline differs from the same line with one, as in `diff -u`
        if ( !$aNl && $a )
            $a[count( $a ) - 1] .= "\0";
        if ( !$bNl && $b )
            $b[count( $b ) - 1] .= "\0";

        $ops = self::diffOps( $a, $b );
        $context = 3;
        $out = "--- $labelOld\n+++ $labelNew\n";
        $n = count( $ops );
        $i = 0;
        while ( $i < $n )
        {
            if ( $ops[$i][0] === '=' )
            {
                ++$i;
                continue;
            }
            // a hunk: from $start to $end (inclusive) around changes closer than 2*context
            $start = max( 0, $i - $context );
            $end = $i;
            $j = $i;
            while ( $j < $n )
            {
                if ( $ops[$j][0] !== '=' )
                {
                    $end = $j;
                    ++$j;
                    continue;
                }
                $k = $j;
                while ( $k < $n && $ops[$k][0] === '=' )
                    ++$k;
                if ( $k < $n && $k - $j <= 2 * $context )
                {
                    $j = $k;
                    continue;
                }
                break;
            }
            $stop = min( $n - 1, $end + $context );
            $oldStart = $ops[$start][1];
            $newStart = $ops[$start][2];
            $oldCount = 0;
            $newCount = 0;
            $body = '';
            for ( $x = $start; $x <= $stop; ++$x )
            {
                list( $op, , , $text ) = $ops[$x];
                $noNewline = substr( $text, -1 ) === "\0";
                if ( $noNewline )
                    $text = substr( $text, 0, -1 );
                if ( $op === '=' ) { ++$oldCount; ++$newCount; $body .= " $text\n"; }
                else if ( $op === '-' ) { ++$oldCount; $body .= "-$text\n"; }
                else { ++$newCount; $body .= "+$text\n"; }
                if ( $noNewline )
                    $body .= "\\ No newline at end of file\n";
            }
            $oldFrom = $oldCount === 0 ? $oldStart : $oldStart + 1;
            $newFrom = $newCount === 0 ? $newStart : $newStart + 1;
            $out .= '@@ -' . $oldFrom . ( $oldCount === 1 ? '' : ",$oldCount" ) . ' +' . $newFrom . ( $newCount === 1 ? '' : ",$newCount" ) . " @@\n" . $body;
            $i = $stop + 1;
        }
        return $out;
    }

    /**
     * Edit script between two line lists: array( op ('=', '-', '+'), index in old, index in new, text ). For '+'
     * the old index is where it would go (the count of old lines before it), for '-' the new index likewise.
     */
    protected static function diffOps( array $a, array $b )
    {
        $na = count( $a );
        $nb = count( $b );
        $pre = 0;
        while ( $pre < $na && $pre < $nb && $a[$pre] === $b[$pre] )
            ++$pre;
        $suf = 0;
        while ( $suf < $na - $pre && $suf < $nb - $pre && $a[$na - 1 - $suf] === $b[$nb - 1 - $suf] )
            ++$suf;
        $ma = array_slice( $a, $pre, $na - $pre - $suf );
        $mb = array_slice( $b, $pre, $nb - $pre - $suf );
        $mid = array();
        $la = count( $ma );
        $lb = count( $mb );
        if ( $la * $lb > 4000000 )
        {
            foreach ( $ma as $x => $t ) $mid[] = array( '-', $x, 0, $t );
            foreach ( $mb as $y => $t ) $mid[] = array( '+', $la, $y, $t );
        }
        else
        {
            // LCS table from the end
            $L = array_fill( 0, $la + 1, array_fill( 0, $lb + 1, 0 ) );
            for ( $x = $la - 1; $x >= 0; --$x )
                for ( $y = $lb - 1; $y >= 0; --$y )
                    $L[$x][$y] = $ma[$x] === $mb[$y] ? $L[$x + 1][$y + 1] + 1 : max( $L[$x + 1][$y], $L[$x][$y + 1] );
            $x = 0;
            $y = 0;
            while ( $x < $la || $y < $lb )
            {
                if ( $x < $la && $y < $lb && $ma[$x] === $mb[$y] )
                {
                    $mid[] = array( '=', $x, $y, $ma[$x] );
                    ++$x; ++$y;
                }
                else if ( $y < $lb && ( $x >= $la || $L[$x][$y + 1] > $L[$x + 1][$y] ) )
                {
                    // deletions before insertions on a tie, as diff -u prints them
                    $mid[] = array( '+', $x, $y, $mb[$y] );
                    ++$y;
                }
                else
                {
                    $mid[] = array( '-', $x, $y, $ma[$x] );
                    ++$x;
                }
            }
        }
        $ops = array();
        for ( $p = 0; $p < $pre; ++$p )
            $ops[] = array( '=', $p, $p, $a[$p] );
        foreach ( $mid as $m )
            $ops[] = array( $m[0], $m[1] + $pre, $m[2] + $pre, $m[3] );
        for ( $s = 0; $s < $suf; ++$s )
        {
            $ia = $na - $suf + $s;
            $ib = $nb - $suf + $s;
            $ops[] = array( '=', $ia, $ib, $a[$ia] );
        }
        return $ops;
    }

    protected static function warn( $message )
    {
        if ( class_exists( 'eZDebug' ) )
            eZDebug::writeWarning( $message, __CLASS__ );
    }
}
