<?php
/**
 * File containing the expIniCommandContext class.
 *
 * What an exp:ini action is handed: the arguments after the action's name, the options, the output
 * (text, or one JSON object with --json), the settings editor factory and the write step every
 * writing action shares (diff, dry run, backup, save, ini cache clear, hint).
 *
 *   $setting = $c->setting();                       // the next argument(s) as a setting
 *   $scope   = $c->writeScope( $c->shift( 'scope' ) );
 *   $editor  = $c->editor( $scope, $setting['file'] );
 *   $editor->set( $setting['block'], $setting['variable'], $value );
 *   return $c->commit( $editor, $scope, $setting['file'], 'set ' . $c->settingText( $setting ) );
 *
 * Guide: doc/bc/6.0/console-exp-ini.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expIniCommandContext
{
    const EXIT_OK = 0;
    const EXIT_USAGE = 1;
    const EXIT_NOT_FOUND = 2;
    const EXIT_REFUSED = 3;
    const EXIT_WRITE_FAILED = 4;

    /** What a secret value is shown as. */
    const MASK = '********';

    /** @var string the action's name (as typed, aliases resolved) */
    private $action;

    /** @var array the arguments after the action's name */
    private $arguments;

    /** @var int the next argument shift() returns */
    private $cursor = 0;

    /** @var array option => value */
    private $options;

    /** @var expIniActionRegistry */
    private $registry;

    /** @var callable|null function( string $line ): writes a line of text output */
    private $writer;

    /** @var callable|null function( expIniScope $scope, string $file ): expIniEditor */
    private $editorFactory;

    /** @var callable|null function(): array, clears the ini cache; returns array( ok, message ) */
    private $cacheClearer;

    /** @var string the result's message */
    private $message = '';

    /** @var array the result's data (the JSON object's "data") */
    private $data = array();

    /** @var array warnings of the run */
    private $warnings = array();

    /**
     * @param string $action
     * @param array $arguments the arguments after the action's name
     * @param array $options dry-run, json, show-secrets, allow-default, clear-cache, backup, create (bool);
     *                       missing ones take their defaults (see defaults())
     * @param expIniActionRegistry|null $registry
     */
    public function __construct( $action, array $arguments, array $options = array(), $registry = null )
    {
        $this->action = (string)$action;
        $this->arguments = array_values( $arguments );
        $this->options = array_merge( self::defaults(), $options );
        $this->registry = $registry !== null ? $registry : expIniActionRegistry::fromSettings();
    }

    /**
     * The options and their defaults. create is null: on for set and add, never for rem.
     *
     * @return array
     */
    public static function defaults()
    {
        return array( 'dry-run' => false, 'json' => false, 'show-secrets' => false, 'allow-default' => false,
                      'clear-cache' => true, 'backup' => true, 'create' => null, 'root' => null,
                      'keep-target' => false, 'only' => null, 'files' => null, 'force' => false,
                      'create-extension' => false, 'activate' => false );
    }

    // ── Wiring (the command sets these; tests replace them) ─────────────────

    /** @param callable $writer function( string $line ) */
    public function setWriter( $writer )
    {
        $this->writer = $writer;
    }

    /** @param callable $factory function( expIniScope $scope, string $file ): expIniEditor */
    public function setEditorFactory( $factory )
    {
        $this->editorFactory = $factory;
    }

    /** @param callable $clearer function(): array( bool ok, string message ) */
    public function setCacheClearer( $clearer )
    {
        $this->cacheClearer = $clearer;
    }

    // ── Arguments and options ───────────────────────────────────────────────

    /** @return string */
    public function action()
    {
        return $this->action;
    }

    /** @return expIniActionRegistry */
    public function registry()
    {
        return $this->registry;
    }

    /** @return array every argument after the action's name */
    public function arguments()
    {
        return $this->arguments;
    }

    /** @return int how many arguments are left */
    public function remaining()
    {
        return count( $this->arguments ) - $this->cursor;
    }

    /** @return array the arguments shift() has not returned yet */
    public function rest()
    {
        return array_slice( $this->arguments, $this->cursor );
    }

    /**
     * The next argument.
     *
     * @param string|false $what what it is, for the usage error when it is missing; false: optional
     * @return string|null null when there is none and it is optional
     * @throws expIniException (usage) when it is missing and required
     */
    public function shift( $what = false )
    {
        if ( $this->cursor >= count( $this->arguments ) )
        {
            if ( $what === false )
                return null;
            throw expIniException::usage( "missing <$what>" );
        }
        return (string)$this->arguments[$this->cursor++];
    }

    /**
     * Refuses arguments nobody asked for.
     *
     * @throws expIniException (usage)
     */
    public function noMoreArguments()
    {
        if ( $this->remaining() > 0 )
            throw expIniException::usage( 'unexpected argument' . ( $this->remaining() > 1 ? 's' : '' ) . ': '
                                          . implode( ' ', $this->rest() ) );
    }

    /**
     * The next argument(s) as a setting. Takes one argument ("site.ini/SiteSettings/SiteName",
     * "site.ini:SiteSettings.SiteName", "site.ini [SiteSettings] SiteName" quoted) or the three words of
     * "site.ini [SiteSettings] SiteName" unquoted.
     *
     * @return array file, block, variable, kind (plain|array|hash), key, as expIniEditor::parseSetting()
     * @throws expIniException (usage)
     */
    public function setting()
    {
        $text = $this->shift( 'file>/<Block>/<Variable' );
        $next = $this->cursor < count( $this->arguments ) ? (string)$this->arguments[$this->cursor] : '';
        if ( strpos( $text, '/' ) === false && strpos( $text, ':' ) === false && strpos( $text, ' ' ) === false
             && preg_match( '/^\[[^\]]+\]$/', $next ) && $this->remaining() >= 2 )
            $text .= ' ' . $this->shift() . ' ' . $this->shift();
        $setting = expIniEditor::parseSetting( $text );
        if ( $setting['block'] === null || $setting['variable'] === null )
            throw expIniException::usage( "\"$text\" names no variable: give <file>/<Block>/<Variable>" );
        return $setting;
    }

    /**
     * The next argument(s) as a file, optionally with a block: "site.ini", "site", "site.ini/SiteSettings",
     * "site.ini:SiteSettings", "site.ini [SiteSettings]" (one argument or two).
     *
     * @return array file (without .ini), block (or null)
     * @throws expIniException (usage)
     */
    public function fileAndBlock()
    {
        $text = $this->shift( 'file>[/<Block>]' );
        $next = $this->cursor < count( $this->arguments ) ? (string)$this->arguments[$this->cursor] : '';
        if ( strpos( $text, '/' ) === false && strpos( $text, ':' ) === false && preg_match( '/^\[[^\]]+\]$/', $next ) )
            $text .= ' ' . $this->shift();

        $setting = expIniEditor::parseSetting( $text );
        if ( $setting['variable'] !== null )
            throw expIniException::usage( "give a file or a file and a block, not a variable: \"$text\"" );
        return array( 'file' => $setting['file'], 'block' => $setting['block'] );
    }

    /**
     * A setting as one line of text: site.ini/SiteSettings/SiteName, .../Variable[], .../Variable[key].
     *
     * @param array $setting parseSetting()
     * @return string
     */
    public static function settingText( array $setting )
    {
        $text = $setting['file'] . '.ini/' . $setting['block'] . '/' . $setting['variable'];
        if ( $setting['kind'] === 'array' )
            $text .= '[]';
        else if ( $setting['kind'] === 'hash' )
            $text .= '[' . $setting['key'] . ']';
        return $text;
    }

    /** @return mixed an option's value (see defaults()), null when unknown */
    public function option( $name )
    {
        return array_key_exists( $name, $this->options ) ? $this->options[$name] : null;
    }

    /** @return array every option */
    public function options()
    {
        return $this->options;
    }

    /** @return bool --dry-run */
    public function isDryRun()
    {
        return (bool)$this->options['dry-run'];
    }

    /** @return bool --json */
    public function isJson()
    {
        return (bool)$this->options['json'];
    }

    /** @return bool --show-secrets */
    public function showSecrets()
    {
        return (bool)$this->options['show-secrets'];
    }

    /**
     * Whether a missing file or block may be created: --create / --no-create, else the action's default.
     *
     * @param bool $default
     * @return bool
     */
    public function mayCreate( $default )
    {
        return $this->options['create'] === null ? (bool)$default : (bool)$this->options['create'];
    }

    /**
     * Refuses --root for what reads the settings in effect: those are merged by eZINI, which knows only the
     * installation the command runs in.
     *
     * @param string $what e.g. "where"
     * @throws expIniException (usage)
     */
    public function requireOwnInstallation( $what )
    {
        if ( $this->options['root'] !== null && $this->options['root'] !== '' )
            throw expIniException::usage( "$what reads the settings in effect of this installation only: "
                                          . 'with --root give a scope' );
    }

    // ── Scopes and the editor ───────────────────────────────────────────────

    /**
     * A scope by its name: global, siteaccess:<sa>, <sa>, extension:<ext>, extension:<ext>:siteaccess:<sa>,
     * default, or one of a scope provider's.
     *
     * @param string $spec
     * @return expIniScope
     * @throws expIniException
     */
    public function scope( $spec )
    {
        return expIniEditor::scope( $spec );
    }

    /**
     * A scope that is about to be written: refuses the kernel's defaults (settings/<file>.ini) without
     * --allow-default and a scope the editor says may not be written.
     *
     * @param string $spec
     * @return expIniScope
     * @throws expIniException (refused)
     */
    public function writeScope( $spec )
    {
        $scope = $this->scope( $spec );
        // a dry run may show what a change of the defaults would be; writing them needs --allow-default
        if ( $this->isDryRun() )
            return $scope;
        if ( $scope->kind() === expIniScope::KIND_DEFAULT )
        {
            if ( !$this->options['allow-default'] )
                throw expIniException::refused( 'the default scope is the kernel\'s shipped settings/<file>.ini; '
                                                . 'write an override (global) instead, or pass --allow-default' );
        }
        else if ( !$scope->policyWritable() )
            throw expIniException::refused( 'scope ' . $scope->name() . ' may not be written' );
        return $scope;
    }

    /**
     * The editor of one file in one scope.
     *
     * @param expIniScope $scope
     * @param string $file without .ini
     * @return expIniEditor
     */
    public function editor( $scope, $file )
    {
        if ( $this->editorFactory !== null )
            return call_user_func( $this->editorFactory, $scope, $file );
        return new expIniEditor( $scope, $file );
    }

    // ── Secrets ─────────────────────────────────────────────────────────────

    /**
     * Whether a variable holds a secret (Password, Secret, Token, Salt, ApiKey, ... in its name):
     * expIniEditor::isSecret(), the one rule the engine and the command share.
     *
     * @param string $variable
     * @return bool
     */
    public static function isSecret( $variable )
    {
        return (bool)expIniEditor::isSecret( (string)$variable );
    }

    /**
     * A value as it may be shown: masked when the variable is a secret, unless --show-secrets.
     *
     * @param string $variable
     * @param mixed $value
     * @return mixed
     */
    public function display( $variable, $value )
    {
        if ( $this->showSecrets() || !self::isSecret( $variable ) || $value === null )
            return $value;
        return expIniEditor::maskValue( $value );
    }

    /**
     * A diff (or any text of INI lines) with the values of secrets masked, unless --show-secrets.
     *
     * @param string $text
     * @return string
     */
    public function maskText( $text )
    {
        if ( $this->showSecrets() )
            return (string)$text;
        return preg_replace_callback( '/^([-+ ]?\s*)([A-Za-z0-9_.\-]+)(\[[^\]]*\])?=(.*)$/m', function ( $m ) {
            if ( !self::isSecret( $m[2] ) || $m[4] === '' )
                return $m[0];
            return $m[1] . $m[2] . $m[3] . '=' . self::MASK;
        }, (string)$text );
    }

    // ── Output ──────────────────────────────────────────────────────────────

    /**
     * Writes a line of text output (nothing with --json).
     *
     * @param string $line
     */
    public function line( $line = '' )
    {
        if ( $this->isJson() )
            return;
        if ( $this->writer !== null )
            call_user_func( $this->writer, (string)$line );
        else
            fwrite( STDOUT, $line . "\n" );
    }

    /**
     * Adds to the result's data.
     *
     * @param string $key
     * @param mixed $value
     */
    public function data( $key, $value )
    {
        $this->data[$key] = $value;
    }

    /** @param string $text a warning, shown in the text output and listed in the JSON */
    public function warn( $text )
    {
        $this->warnings[] = (string)$text;
        $this->line( 'Warning: ' . $text );
    }

    /**
     * Ends the action: sets the result's message and returns the exit code.
     *
     * @param int $code
     * @param string $message also written as the last line of text output, unless empty
     * @return int $code
     */
    public function finish( $code, $message = '' )
    {
        $this->message = (string)$message;
        if ( $this->message !== '' )
            $this->line( $this->message );
        return (int)$code;
    }

    /**
     * The result as one array: what --json prints.
     *
     * @param int $code
     * @return array ok, code, action, message, warnings, data
     */
    public function result( $code )
    {
        return array( 'ok' => $code === self::EXIT_OK, 'code' => (int)$code, 'action' => $this->action,
                      'message' => $this->message, 'warnings' => $this->warnings, 'data' => $this->data );
    }

    // ── Writing ─────────────────────────────────────────────────────────────

    /**
     * The write step every writing action ends with. Nothing to change: says so (exit 0). --dry-run:
     * prints the diff and writes nothing. Otherwise saves (backup first unless --no-backup), clears the
     * ini cache unless --no-clear-cache and prints where the change reaches the web servers.
     *
     * @param expIniEditor $editor with the change made
     * @param expIniScope $scope
     * @param string $file the file, without .ini
     * @param string $what e.g. "set site.ini/SiteSettings/SiteName", for the messages
     * @param bool $createDefault whether a missing file may be created unless --create/--no-create say otherwise
     * @return int exit code
     */
    public function commit( $editor, $scope, $file, $what, $createDefault = true )
    {
        $diff = (string)$editor->diff();
        $this->data( 'scope', $scope->name() );
        $this->data( 'diff', $this->maskText( $diff ) );

        if ( trim( $diff ) === '' )
        {
            $this->data( 'changed', false );
            return $this->finish( self::EXIT_OK, "Nothing to change: $what in " . $scope->name() . ' is already so' );
        }

        if ( !is_file( $scope->path( $file ) ) && !$this->mayCreate( $createDefault ) )
            return $this->finish( self::EXIT_NOT_FOUND, 'Not found: ' . $scope->relativePath( $file ) . ' does not exist and --no-create was given' );

        if ( $this->isDryRun() )
        {
            $this->data( 'changed', false );
            $this->data( 'dry_run', true );
            foreach ( explode( "\n", rtrim( $this->maskText( $diff ), "\n" ) ) as $l )
                $this->line( $l );
            return $this->finish( self::EXIT_OK, "Dry run: $what in " . $scope->name() . ', nothing written' );
        }

        try
        {
            $result = $editor->save( array( 'backup' => (bool)$this->options['backup'],
                                            'create' => $this->mayCreate( $createDefault ),
                                            'allowDefault' => (bool)$this->options['allow-default'] ) );
        }
        catch ( expIniException $e )
        {
            $code = in_array( $e->getCode(), array( 1, 2, 3, 4 ), true ) ? $e->getCode() : self::EXIT_WRITE_FAILED;
            return $this->finish( $code, 'Not written: ' . $e->getMessage() );
        }
        catch ( Exception $e )
        {
            return $this->finish( self::EXIT_WRITE_FAILED, 'Not written: ' . $e->getMessage() );
        }

        $this->data( 'changed', (bool)$result->changed() );
        $this->data( 'path', $result->relativePath() ?: $result->path() );
        $this->data( 'created', (bool)$result->created() );
        $this->data( 'backup', $result->backup() );
        foreach ( (array)$result->warnings() as $warning )
            $this->warn( $warning );

        $this->line( ucfirst( $what ) . ' in ' . $scope->name() . ': ' . ( $result->relativePath() ?: $result->path() )
                     . ( $result->created() ? ' (created)' : '' ) );
        if ( $result->backup() )
            $this->line( 'Backup: ' . $result->backup() );

        $this->afterWrite();
        return $this->finish( self::EXIT_OK, 'Done' );
    }

    /**
     * What follows every write: the ini cache clear (unless --no-clear-cache, never with --root) and the
     * hint saying when the web servers see the change.
     */
    public function afterWrite()
    {
        $cleared = false;
        if ( $this->options['root'] !== null && $this->options['root'] !== '' )
        {
            // another installation's settings: this installation's ini cache has nothing of it
            $this->line( 'INI cache: not cleared (--root): clear the ini cache of that installation' );
            $this->data( 'cache_cleared', false );
            return;
        }
        if ( $this->options['clear-cache'] )
        {
            list( $ok, $text ) = $this->clearIniCache();
            $cleared = $ok;
            $this->line( $text );
        }
        $this->data( 'cache_cleared', $cleared );
        $hint = self::reloadHint( $cleared );
        $this->data( 'hint', $hint );
        $this->line( $hint );
    }

    /**
     * Clears the ini cache tag (what "ezcache.php --clear-tag=ini" and "exp:cache ini" do): the global INI
     * cache in var/cache/ini, the per-site INI cache and what depends on them.
     *
     * @return array( bool ok, string message )
     */
    public function clearIniCache()
    {
        if ( $this->cacheClearer !== null )
            return call_user_func( $this->cacheClearer );
        try
        {
            if ( class_exists( 'expCacheManager' ) )
            {
                $manager = new expCacheManager();
                $result = $manager->clear( 'tag', array( 'ini' ) );
                return array( (bool)$result['ok'], 'INI cache: ' . $result['message'] );
            }
            eZCache::clearByTag( 'ini' );
            return array( true, 'INI cache: cleared tag ini' );
        }
        catch ( Exception $e )
        {
            return array( false, 'INI cache not cleared: ' . $e->getMessage() . ' (run exp:cache ini)' );
        }
    }

    /**
     * When the web servers see a change, for this installation.
     *
     * config.php can switch eZINI's file mtime checks off (EZP_INI_FILEMTIME_CHECK=false): then a changed
     * file is not seen until the ini cache is cleared. Velocity keeps eZINI from its warm-up in every
     * worker, so it needs a restart either way.
     *
     * @param bool $cleared whether the ini cache was cleared
     * @return string
     */
    public static function reloadHint( $cleared )
    {
        $mtime = !defined( 'EZP_INI_FILEMTIME_CHECK' ) || EZP_INI_FILEMTIME_CHECK;
        if ( $cleared )
            $web = 'PHP-FPM reads the change on its next request (ini cache cleared)';
        else if ( $mtime )
            $web = 'PHP-FPM reads the change on its next request (INI mtime checks are on); the ini cache was not cleared';
        else
            $web = 'PHP-FPM does NOT see the change yet: INI mtime checks are off (config.php EZP_INI_FILEMTIME_CHECK=false), '
                 . 'clear the ini cache with exp:cache ini';
        return 'Hint: ' . $web . '; Velocity workers keep the settings of their warm-up: exp:velocity restart to apply it there.';
    }
}
