<?php
/**
 * Fixtures shared by the package module tests: package definitions, package directories under var/tmp, and
 * gzip compressed tar archives written byte by byte (so an archive can carry what no well-behaved tool writes:
 * "../" paths, absolute paths, links, devices).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZPackageTestFixtures
{
    /**
     * A package.xml.
     *
     * @param array $options name, vendor, type, summary, install_type, requires (names), classes (identifiers),
     *                       objects (item count), extensions (names), settings (file names), maintainer
     * @return string
     */
    static function definition( array $options = array() )
    {
        $o = $options + array( 'name' => 'k1_pkg', 'vendor' => '', 'type' => 'contentclass', 'summary' => 'A test package',
                               'install_type' => 'install', 'requires' => array(), 'classes' => array(), 'objects' => 0,
                               'extensions' => array(), 'settings' => array(), 'maintainer' => 'Test Maintainer', 'version' => '1.0',
                               'release' => '2' );
        $esc = function ( $s ) { return htmlspecialchars( (string)$s, ENT_QUOTES | ENT_XML1, 'UTF-8' ); };
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
             . '<package version="6.0.15-0" development="false" install_type="' . $esc( $o['install_type'] ) . '">'
             . '<name>' . $esc( $o['name'] ) . '</name><summary>' . $esc( $o['summary'] ) . '</summary>'
             . ( $o['vendor'] !== '' ? '<vendor>' . $esc( $o['vendor'] ) . '</vendor>' : '' )
             . '<type value="' . $esc( $o['type'] ) . '" />'
             . '<ezpublish><version>6.0.15</version><named-version>6.0</named-version></ezpublish>'
             . '<maintainers><maintainer><name>' . $esc( $o['maintainer'] ) . '</name><role>lead</role></maintainer></maintainers>'
             . '<packaging><timestamp>1700000000</timestamp><host>test.example</host></packaging>'
             . '<simple-files /><files />'
             . '<version><number>' . $esc( $o['version'] ) . '</number><release>' . $esc( $o['release'] ) . '</release></version>'
             . '<licence>GPL</licence><state>stable</state><dependencies><provides>';
        foreach ( $o['classes'] as $class )
            $xml .= '<provide type="ezcontentclass" name="contentclass" value="' . $esc( $class ) . '"/>';
        $xml .= '</provides><requires>';
        foreach ( $o['requires'] as $require )
            $xml .= '<require type="ezpackage" name="' . $esc( $require ) . '" min-version="1.0"/>';
        $xml .= '</requires><obsoletes /><conflicts /></dependencies>';
        if ( $o['settings'] )
        {
            $xml .= '<settings>';
            foreach ( $o['settings'] as $file )
                $xml .= '<settings-file filename="' . $esc( $file ) . '"/>';
            $xml .= '</settings>';
        }
        $xml .= '<install>';
        foreach ( $o['classes'] as $class )
            $xml .= '<item type="ezcontentclass" filename="class-' . $esc( $class ) . '" sub-directory="ezcontentclass"/>';
        for ( $i = 0; $i < $o['objects']; $i++ )
            $xml .= '<item type="ezcontentobject" filename="contentobjects' . ( $i ?: '' ) . '" sub-directory="ezcontentobject"/>';
        foreach ( $o['extensions'] as $extension )
            $xml .= '<item type="ezextension" filename="extension-' . $esc( $extension ) . '" sub-directory="ezextension"/>';
        $xml .= '</install><uninstall /></package>';
        return $xml;
    }

    /**
     * Writes files under $dir (path => content), making the directories.
     */
    static function writeFiles( $dir, array $files )
    {
        foreach ( $files as $path => $content )
        {
            $full = $dir . '/' . $path;
            if ( !is_dir( dirname( $full ) ) )
                mkdir( dirname( $full ), 0775, true );
            file_put_contents( $full, $content );
        }
    }

    /**
     * One ustar entry: header block and data blocks.
     *
     * @param string $name
     * @param string $content
     * @param string $type '0' file, '5' directory, '2' symlink, '1' hard link, '3' character device, '6' fifo
     * @param string $link
     */
    static function tarEntry( $name, $content = '', $type = '0', $link = '' )
    {
        $size = $type === '0' ? strlen( $content ) : 0;
        $header = str_pad( $name, 100, "\0" )
                . str_pad( $type === '5' ? '0000755' : '0000644', 7, '0', STR_PAD_LEFT ) . "\0"
                . "0000000\0" . "0000000\0"
                . str_pad( decoct( $size ), 11, '0', STR_PAD_LEFT ) . "\0"
                . str_pad( decoct( 1700000000 ), 11, '0', STR_PAD_LEFT ) . "\0"
                . '        '
                . $type
                . str_pad( $link, 100, "\0" )
                . "ustar\0" . '00'
                . str_pad( 'root', 32, "\0" ) . str_pad( 'root', 32, "\0" )
                . "0000000\0" . "0000000\0"
                . str_repeat( "\0", 155 ) . str_repeat( "\0", 12 );
        $sum = 0;
        for ( $i = 0; $i < 512; $i++ )
            $sum += ord( $header[$i] );
        $header = substr( $header, 0, 148 ) . str_pad( decoct( $sum ), 6, '0', STR_PAD_LEFT ) . "\0 " . substr( $header, 156 );
        $data = $size ? $content . str_repeat( "\0", ( 512 - $size % 512 ) % 512 ) : '';
        return $header . $data;
    }

    /**
     * A gzip compressed tar archive at $path of the entries given (each array( name, content, type, link )).
     */
    static function archive( $path, array $entries )
    {
        $tar = '';
        foreach ( $entries as $entry )
            $tar .= self::tarEntry( $entry[0], isset( $entry[1] ) ? $entry[1] : '', isset( $entry[2] ) ? $entry[2] : '0', isset( $entry[3] ) ? $entry[3] : '' );
        $tar .= str_repeat( "\0", 1024 );
        if ( !is_dir( dirname( $path ) ) )
            mkdir( dirname( $path ), 0775, true );
        file_put_contents( $path, gzencode( $tar ) );
        return $path;
    }

    /**
     * A fresh directory under var/tmp.
     */
    static function tempDir( $label )
    {
        $dir = 'var/tmp/phpunit-' . $label . '-' . getmypid() . '-' . mt_rand();
        mkdir( $dir, 0775, true );
        return $dir;
    }
}
