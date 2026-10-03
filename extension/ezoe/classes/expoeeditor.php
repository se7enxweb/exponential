<?php
/**
 * The editor engine registry and the helpers every caller shares: the templates, the ezoe module, the
 * ezjscore functions and the expservices class all go through here.
 *
 *  - engines():        the registered engines (ezoe.ini [EditorSettings] Engines[<id>]=<class>), instantiated
 *  - problems():       registered entries that cannot work (class missing, not an engine, not available)
 *  - resolve():        the engine an editor gets: user preference > siteaccess ezoe.ini > global ezoe.ini > tinymce3
 *  - uploadExtensions(), uploadExtensionAllowed(): the file types the embed dialog's upload accepts, enforced
 *                      on the server by modules/ezoe/upload.php
 *  - setUserEngine():  stores the user preference ezoe_engine and writes the audit event
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package ezoe
 */

class expOEEditor
{
    const DEFAULT_ENGINE = 'tinymce3';
    const PREFERENCE = 'ezoe_engine';

    /** @var array extensions that are never accepted, wherever they stand in a file name */
    protected static $executableExtensions = array( 'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'pht', 'phps', 'pl', 'cgi', 'asp', 'aspx', 'jsp', 'sh', 'exe', 'htaccess' );

    /** @var array|null id => expOEEditorEngine */
    protected static $engines = null;
    /** @var array list of array( id, class, why ) */
    protected static $problems = array();

    /** Forgets what was read (tests, or after the ini cache was cleared in a long-running process). */
    public static function reset()
    {
        self::$engines  = null;
        self::$problems = array();
    }

    /** @return array identifier => class name, as registered in ezoe.ini */
    public static function registered()
    {
        $ini = eZINI::instance( 'ezoe.ini' );
        $map = $ini->hasVariable( 'EditorSettings', 'Engines' ) ? $ini->variable( 'EditorSettings', 'Engines' ) : array();
        $map = is_array( $map ) ? array_filter( $map, 'strlen' ) : array();
        // The built-in editor is always there, whatever an override leaves out of the list
        if ( !isset( $map[self::DEFAULT_ENGINE] ) )
            $map[self::DEFAULT_ENGINE] = 'expOETinyMCE3Engine';
        return $map;
    }

    /** @return array identifier => expOEEditorEngine, of the entries that work and are available */
    public static function engines()
    {
        if ( self::$engines !== null )
            return self::$engines;

        self::$engines  = array();
        self::$problems = array();
        foreach ( self::registered() as $id => $class )
        {
            if ( !preg_match( '/^[a-z0-9_]+$/', (string) $id ) )
                $why = 'the identifier may only use a-z, 0-9 and _';
            else if ( !class_exists( $class ) )
                $why = 'the class does not exist';
            else if ( !is_subclass_of( $class, 'expOEEditorEngine' ) )
                $why = 'the class does not implement expOEEditorEngine';
            else
            {
                try
                {
                    $engine = new $class();
                    if ( $engine->identifier() !== (string) $id )
                        $why = 'the class says its identifier is "' . $engine->identifier() . '"';
                    else if ( !$engine->isAvailable() )
                        $why = 'the engine is not available (its files are missing)';
                    else
                    {
                        self::$engines[$id] = $engine;
                        continue;
                    }
                }
                catch ( Exception $e )
                {
                    $why = $e->getMessage();
                }
                catch ( Error $e )
                {
                    $why = $e->getMessage();
                }
            }
            self::$problems[] = array( 'id' => (string) $id, 'class' => (string) $class, 'why' => $why );
        }
        return self::$engines;
    }

    /** @return array list of array( id, class, why ) for every registered entry that cannot be used */
    public static function problems()
    {
        self::engines();
        return self::$problems;
    }

    /** @return bool */
    public static function hasEngine( $id )
    {
        $engines = self::engines();
        return is_string( $id ) && isset( $engines[$id] );
    }

    /** @return string the engine the siteaccess and global settings ask for, tinymce3 when that is not usable */
    public static function configuredEngine()
    {
        $ini = eZINI::instance( 'ezoe.ini' );
        $id = $ini->hasVariable( 'EditorSettings', 'EditorEngine' ) ? $ini->variable( 'EditorSettings', 'EditorEngine' ) : self::DEFAULT_ENGINE;
        return self::hasEngine( $id ) ? $id : self::DEFAULT_ENGINE;
    }

    /**
     * The engine the current user edits with: the user preference ezoe_engine when it names an available
     * engine, else the siteaccess setting (ezoe.ini is read per siteaccess), else the global default.
     *
     * @param string|null $preference the preference value, read for the current user when null
     * @return string an identifier of engines()
     */
    public static function resolve( $preference = null )
    {
        if ( $preference === null )
            $preference = eZPreferences::value( self::PREFERENCE );
        if ( is_string( $preference ) && $preference !== '' && self::hasEngine( $preference ) )
            return $preference;
        return self::configuredEngine();
    }

    /** @return array identifier, label (translated), template, assets, toolbar_map, plugins, config of one engine */
    public static function info( $id, array $context = array() )
    {
        $engines = self::engines();
        if ( !isset( $engines[$id] ) )
            $id = self::DEFAULT_ENGINE;
        $engine = $engines[$id];
        return array( 'identifier'  => $id,
                      'label'       => ezpI18n::tr( 'design/standard/ezoe', $engine->label() ),
                      'template'    => $engine->template(),
                      'assets'      => $engine->assets(),
                      'toolbar_map' => $engine->toolbarMap(),
                      'plugins'     => $engine->plugins(),
                      'config'      => $engine->config( $context ) );
    }

    /** @return array of identifier => translated label, every available engine */
    public static function labels()
    {
        $labels = array();
        foreach ( self::engines() as $id => $engine )
            $labels[$id] = ezpI18n::tr( 'design/standard/ezoe', $engine->label() );
        return $labels;
    }

    /** @return bool whether editors may choose their engine themselves (ezoe.ini [EditorSettings] EngineSwitch) */
    public static function switchEnabled()
    {
        $ini = eZINI::instance( 'ezoe.ini' );
        return $ini->hasVariable( 'EditorSettings', 'EngineSwitch' )
            && $ini->variable( 'EditorSettings', 'EngineSwitch' ) === 'enabled';
    }

    /** @return array list of array( identifier, label ) of the other engines an editor could switch to */
    public static function switchChoices( $current )
    {
        $choices = array();
        foreach ( self::labels() as $id => $label )
        {
            if ( $id !== $current )
                $choices[] = array( 'identifier' => $id, 'label' => $label );
        }
        return $choices;
    }

    /**
     * Stores the engine of the current user (preference ezoe_engine) and records the audit event.
     *
     * @param string $id an identifier of engines(), or '' to go back to the siteaccess default
     * @return bool false when the engine is unknown
     */
    public static function setUserEngine( $id )
    {
        if ( $id !== '' && !self::hasEngine( $id ) )
            return false;
        $before = self::resolve();
        eZPreferences::setValue( self::PREFERENCE, $id );
        if ( class_exists( 'expAudit' ) )
        {
            expAudit::event( 'content.ezoe.engine.change', array(
                'object' => array( 'type' => 'user', 'id' => (int) eZUser::currentUserID() ),
                'before' => array( 'engine' => $before ),
                'after'  => array( 'engine' => $id === '' ? self::configuredEngine() : $id ),
                'result' => 'success' ) );
        }
        return true;
    }

    /** @return array the file extensions (lower case, no dot) the upload tab of the embed dialog accepts */
    public static function uploadExtensions()
    {
        $ini = eZINI::instance( 'ezoe.ini' );
        $list = $ini->hasVariable( 'EditorSettings', 'UploadFileExtensions' ) ? $ini->variable( 'EditorSettings', 'UploadFileExtensions' ) : array();
        $list = array_map( 'strtolower', array_map( 'trim', (array) $list ) );
        return array_values( array_unique( array_filter( array_map( function ( $e ) { return ltrim( $e, '.' ); }, $list ), 'strlen' ) ) );
    }

    /**
     * Whether the server accepts an uploaded file name. ezoe.ini [EditorSettings] UploadExtensionCheck:
     * 'engine' (default) checks when the user's engine is not tinymce3 (the TinyMCE 3 dialog has never
     * limited types), 'always' checks every upload, 'disabled' none. A file with no extension is refused
     * whenever the check is on.
     *
     * @param string $fileName the name the client sent
     * @return bool
     */
    public static function uploadExtensionAllowed( $fileName )
    {
        $ini  = eZINI::instance( 'ezoe.ini' );
        $mode = $ini->hasVariable( 'EditorSettings', 'UploadExtensionCheck' ) ? $ini->variable( 'EditorSettings', 'UploadExtensionCheck' ) : 'engine';
        if ( $mode === 'disabled' || ( $mode !== 'always' && self::resolve() === self::DEFAULT_ENGINE ) )
            return true;
        $parts = explode( '.', basename( str_replace( '\\', '/', (string) $fileName ) ) );
        if ( count( $parts ) < 2 )
            return false;
        array_shift( $parts );
        // "shell.php.jpg" is served as PHP by some server setups: no executable type anywhere in the name
        foreach ( $parts as $part )
        {
            if ( in_array( strtolower( $part ), self::$executableExtensions, true ) )
                return false;
        }
        return in_array( strtolower( end( $parts ) ), self::uploadExtensions(), true );
    }
}
