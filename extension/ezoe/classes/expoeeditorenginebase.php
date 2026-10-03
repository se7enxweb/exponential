<?php
/**
 * Convenience base of editor engines: reads the engine's own ezoe.ini block [Engine_<identifier>]
 * (ToolbarMap[<button>]=<item>, ExternalPlugins[<name>]=<design path>, Scripts[], Styles[]).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package ezoe
 */

abstract class expOEEditorEngineBase implements expOEEditorEngine
{
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
