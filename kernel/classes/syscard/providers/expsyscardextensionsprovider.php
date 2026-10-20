<?php
/**
 * File containing the expSysCardExtensionsProvider class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * exp:info: every active extension with its version, from its extension.xml <metadata> or ezinfo.php, then its
 * composer.json (the same sources as the about page). An extension without a version is listed as "no version".
 */
class expSysCardExtensionsProvider extends expSysCardBaseProvider
{
    protected $id = 'extensions';
    protected $section = 'extensions';
    protected $modes = array( expSysCardContext::MODE_INFO );
    protected $priority = 15;
    protected $budget = 2.0;

    public function collect( expSysCardContext $context )
    {
        $list = $context->get( 'extensions.list', function () { return self::installed(); }, array() );
        $facts = array();
        foreach ( is_array( $list ) ? $list : array() as $row )
        {
            if ( !isset( $row['extension'] ) )
                continue;
            $version = isset( $row['version'] ) && $row['version'] !== '' ? (string)$row['version'] : 'no version';
            $facts[] = $this->fact( 'ext:' . $row['extension'], (string)$row['extension'], $version );
        }
        if ( empty( $facts ) )
            $facts[] = $this->fact( 'none', 'Extensions', 'none active' );
        return $facts;
    }

    /**
     * The active extensions and their versions.
     *
     * @return array list of array( 'extension', 'version' )
     */
    public static function installed()
    {
        if ( !class_exists( 'eZExtension' ) )
            return array();
        $list = array();
        foreach ( eZExtension::activeExtensions() as $extension )
        {
            $version = '';
            $info = class_exists( 'ezpExtension' ) ? ezpExtension::getInstance( $extension )->getInfo() : null;
            if ( is_array( $info ) )
                foreach ( $info as $key => $value )
                    if ( strtolower( (string)$key ) === 'version' && is_string( $value ) && trim( $value ) !== '' && strpos( trim( $value ), '//' ) !== 0 )
                    {
                        $version = trim( $value );
                        break;
                    }
            $path = eZExtension::extensionPath( $extension );
            if ( $version === '' && $path && is_readable( $path . '/composer.json' ) )
            {
                $composer = json_decode( (string)file_get_contents( $path . '/composer.json' ), true );
                if ( isset( $composer['version'] ) && is_string( $composer['version'] ) )
                    $version = $composer['version'];
            }
            $list[] = array( 'extension' => $extension, 'version' => $version );
        }
        return $list;
    }
}
