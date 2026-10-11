<?php
/**
 * Convenience base of editor engines: reads the engine's own ezoe.ini block [Engine_<identifier>]
 * (ToolbarMap[<button>]=<item>, ExternalPlugins[<name>]=<design path>, Scripts[], Styles[], Version, Description).
 *
 * version() and description() are optional for an engine (expOEEditor::details() asks for them when they are
 * there); the base answers them from the ini block, an engine that knows better overrides them.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package ezoe
 */

abstract class expOEEditorEngineBase implements expOEEditorEngine
{
    /** @var array file => version found in it, read once per process */
    protected static $scriptVersions = array();

    protected function setting( $name, $default = array() )
    {
        $ini = eZINI::instance( 'ezoe.ini' );
        $block = 'Engine_' . $this->identifier();
        return $ini->hasVariable( $block, $name ) ? $ini->variable( $block, $name ) : $default;
    }

    public function isAvailable()
    {
        return true;
    }

    /** @return string the version of the editor, shown on ezoe/engine; [Engine_<id>] Version, '' when unknown */
    public function version()
    {
        $version = $this->setting( 'Version', '' );
        return is_string( $version ) ? $version : '';
    }

    /** @return string an English sentence about the editor, translated by the caller (context design/standard/ezoe) */
    public function description()
    {
        $description = $this->setting( 'Description', '' );
        return is_string( $description ) ? $description : '';
    }

    /**
     * The TinyMCE version a script declares (majorVersion:"8",minorVersion:"9.2" => 8.9.2).
     *
     * @param string $file absolute file name
     * @return string '' when the file or the declaration is missing
     */
    protected static function tinyMCEVersion( $file )
    {
        if ( isset( self::$scriptVersions[$file] ) )
            return self::$scriptVersions[$file];
        $version = '';
        $source = is_file( $file ) ? (string) file_get_contents( $file ) : '';
        if ( preg_match( '/majorVersion\s*:\s*"(\d+)"\s*,\s*minorVersion\s*:\s*"([\d.]+)"/', $source, $m ) )
            $version = $m[1] . '.' . $m[2];
        return self::$scriptVersions[$file] = $version;
    }

    public function template()
    {
        return '';
    }

    public function assets()
    {
        return array( 'scripts' => array_values( array_filter( (array) $this->setting( 'Scripts' ) ) ),
                      'styles'  => array_values( array_filter( (array) $this->setting( 'Styles' ) ) ) );
    }

    public function toolbarMap()
    {
        return array_filter( (array) $this->setting( 'ToolbarMap' ), 'strlen' );
    }

    public function plugins()
    {
        return array_filter( (array) $this->setting( 'ExternalPlugins' ), 'strlen' );
    }

    public function config( array $context = array() )
    {
        return array();
    }
}
