<?php
/**
 * expeditor: the online editor (ezoe) for remote clients: the registered editor engines, the engine of the
 * current user, the file types the editor's upload accepts and the editor's configuration. Everything goes
 * through expOEEditor, the registry the templates and the upload view use too.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expEditorServices extends expServiceBase
{
    public static $services = array(
        'engines' => array( 'summary' => 'The registered editor engines with the one this user gets, and the entries that cannot work',
            'access' => array( 'ezoe', 'editor' ), 'write' => false, 'args' => array(), 'returns' => 'engines (identifier, label, class, current, default), current, default, switch_enabled, problems' ),
        'get' => array( 'summary' => 'The editor engine of the current user: the stored preference, the engine in use, the site default',
            'access' => array( 'ezoe', 'editor' ), 'write' => false, 'args' => array(), 'returns' => 'preference, engine, default, switch_enabled' ),
        'set' => array( 'summary' => 'Chooses the editor engine of the current user (POST engine; empty = the site default)',
            'access' => array( 'ezoe', 'editor' ), 'write' => true, 'args' => array( 'engine' => 'string (POST)' ), 'returns' => 'preference, engine' ),
        'uploadExtensions' => array( 'summary' => 'The file types the upload tab of the editor accepts, and how strictly the server checks them',
            'access' => array( 'ezoe', 'editor' ), 'write' => false, 'args' => array(), 'returns' => 'extensions, check, enforced' ),
        'config' => array( 'summary' => 'The configuration of the editor for the current user: engine, toolbar, plugins, upload types',
            'access' => array( 'ezoe', 'editor' ), 'write' => false, 'args' => array( 'layout' => 'string' ), 'returns' => 'engine, toolbar_map, plugins, config, buttons, upload_extensions, switch_enabled' ),
    );

    public static function engines( array $a = array() )
    {
        self::guard( __FUNCTION__ );
        $current = expOEEditor::resolve();
        $default = expOEEditor::configuredEngine();
        $registered = expOEEditor::registered();
        $list = array();
        foreach ( expOEEditor::labels() as $id => $label )
        {
            $list[] = array( 'identifier' => $id, 'label' => $label, 'class' => $registered[$id],
                             'current' => $id === $current, 'default' => $id === $default );
        }
        return self::ok( array( 'engines' => $list, 'current' => $current, 'default' => $default,
                                'switch_enabled' => expOEEditor::switchEnabled(), 'problems' => expOEEditor::problems() ) );
    }

    public static function get( array $a = array() )
    {
        self::guard( __FUNCTION__ );
        $preference = eZPreferences::value( expOEEditor::PREFERENCE );
        return self::ok( array( 'preference' => is_string( $preference ) ? $preference : '',
                                'engine' => expOEEditor::resolve(),
                                'default' => expOEEditor::configuredEngine(),
                                'switch_enabled' => expOEEditor::switchEnabled() ) );
    }

    public static function set( array $a = array() )
    {
        self::guard( __FUNCTION__ );
        $engine = trim( (string) self::post( 'engine', 'string', '' ) );
        if ( !expOEEditor::setUserEngine( $engine ) )
            throw new expServiceException( 'Unknown editor engine: ' . $engine, 422 );
        return self::ok( array( 'preference' => $engine, 'engine' => expOEEditor::resolve() ) );
    }

    public static function uploadExtensions( array $a = array() )
    {
        self::guard( __FUNCTION__ );
        $ini = eZINI::instance( 'ezoe.ini' );
        $check = $ini->hasVariable( 'EditorSettings', 'UploadExtensionCheck' ) ? $ini->variable( 'EditorSettings', 'UploadExtensionCheck' ) : 'engine';
        return self::ok( array( 'extensions' => expOEEditor::uploadExtensions(), 'check' => $check,
                                'enforced' => $check === 'always' || ( $check !== 'disabled' && expOEEditor::resolve() !== expOEEditor::DEFAULT_ENGINE ) ) );
    }

    public static function config( array $a = array() )
    {
        self::guard( __FUNCTION__ );
        $layout = trim( (string) self::arg( $a, 0, 'string', '' ) );
        $section = $layout === '' ? 'EditorLayout' : 'EditorLayout_' . preg_replace( '/[^A-Za-z0-9_]/', '', $layout );
        $ini = eZINI::instance( 'ezoe.ini' );
        $buttons = $ini->hasVariable( $section, 'Buttons' ) ? array_values( $ini->variable( $section, 'Buttons' ) ) : array();
        $info = expOEEditor::info( expOEEditor::resolve() );
        return self::ok( array( 'engine' => $info['identifier'], 'label' => $info['label'], 'toolbar_map' => $info['toolbar_map'],
                                'plugins' => $info['plugins'], 'config' => $info['config'], 'buttons' => $buttons,
                                'upload_extensions' => expOEEditor::uploadExtensions(),
                                'switch_enabled' => expOEEditor::switchEnabled() ) );
    }
}
