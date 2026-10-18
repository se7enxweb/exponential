<?php
/**
 * The license of the documentation the help shows (exphelp.ini [License], doc/README-LICENSE.md).
 *
 * The documentation the project holds is free documentation under the GNU Free Documentation License 1.3 or any
 * later version. A few inherited files (OtherLicensePathRules[]) keep the terms they came with; forPage() tells the
 * two apart, so every help page, the print view and the PDF show the notice that is true for that page.
 *
 *   expHelpLicense::forPage( 'help/getting-started.md' );   // array( 'free' => true, 'name' => ..., 'notice' => ... )
 *   expHelpLicense::noticeText( 'help/getting-started.md' ); // plain text for a PDF or print footer
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpLicense
{
    /** SHA-256 of https://www.gnu.org/licenses/fdl-1.3.txt as published by the Free Software Foundation */
    const GFDL_13_SHA256 = '110535522396708cea37c72a802c5e7e81391139f5f7985631c93ef242b206a4';

    /** Defaults when exphelp.ini has no [License] group */
    protected static $defaults = array(
        'Enabled' => 'true',
        'Name' => 'GNU Free Documentation License',
        'ShortName' => 'GFDL',
        'Version' => '1.3',
        'VersionDate' => '3 November 2008',
        'OrLater' => 'true',
        'URL' => 'https://www.gnu.org/licenses/fdl-1.3.html',
        'TextURL' => 'https://www.gnu.org/licenses/fdl-1.3.txt',
        'TextPage' => 'LICENSE-GFDL-1.3.txt',
        'NoticePage' => 'README-LICENSE.md',
        'ExplainerPage' => 'help/free-documentation.md',
        'CopyrightYears' => '1998 - 2026',
        'CopyrightHolder' => '7x & Exponential Foundation and the Exponential contributors',
        // the same list as the shipped exphelp.ini
        'OtherLicensePathRules' => array( 'doc/features/[2-5].*', 'doc/bc/[2-5].*', 'doc/specifications/[2-5].*',
                                          'doc/specifications/trunk/*', 'doc/changelogs/[2-5].*', 'doc/changelogs/README.md',
                                          'doc/shop/*', 'doc/branch-overview.md', 'doc/release-overview', 'doc/standards/header.txt',
                                          'doc/examples/ezpublish.conf', 'doc/examples/htaccess' ),
        'OtherCopyright' => 'Copyright (C) 1999 - 2014 eZ Systems AS and contributors',
        'OtherLicenseName' => 'GNU General Public License, version 2',
        'OtherLicenseURL' => 'https://www.gnu.org/licenses/old-licenses/gpl-2.0.html' );

    /**
     * A [License] setting of exphelp.ini, or its default.
     *
     * @param string $name
     * @return mixed
     */
    public static function setting( $name )
    {
        $default = isset( self::$defaults[$name] ) ? self::$defaults[$name] : null;
        $value = expHelp::setting( 'License', $name, $default );
        if ( is_array( $default ) )
            return is_array( $value ) ? array_values( array_filter( array_map( 'strval', $value ), 'strlen' ) ) : $default;
        return is_scalar( $value ) ? trim( (string)$value ) : $default;
    }

    /**
     * Whether the help shows the license notice at all ([License] Enabled).
     *
     * @return bool
     */
    public static function isEnabled()
    {
        return self::setting( 'Enabled' ) !== 'false';
    }

    /**
     * The license of the documentation: name, version, links, copyright and the standard notice.
     *
     * @return array
     */
    public static function info()
    {
        $name = (string)self::setting( 'Name' );
        $version = (string)self::setting( 'Version' );
        $orLater = self::setting( 'OrLater' ) !== 'false';
        $holder = (string)self::setting( 'CopyrightHolder' );
        $years = (string)self::setting( 'CopyrightYears' );
        $copyright = 'Copyright (C) ' . ( $years !== '' ? $years . ' ' : '' ) . $holder . '.';
        $notice = 'Permission is granted to copy, distribute and/or modify this document under the terms of the '
                . $name . ', Version ' . $version . ( $orLater ? ' or any later version' : '' )
                . ' published by the Free Software Foundation; with no Invariant Sections, no Front-Cover Texts, and no'
                . ' Back-Cover Texts. A copy of the license is included in the section entitled "' . $name . '".';
        return array(
            'enabled' => self::isEnabled(),
            'name' => $name,
            'short_name' => (string)self::setting( 'ShortName' ),
            'version' => $version,
            'version_date' => (string)self::setting( 'VersionDate' ),
            'or_later' => $orLater,
            'label' => $name . ' ' . $version . ( $orLater ? '+' : '' ),
            'url' => self::safeUrl( self::setting( 'URL' ) ),
            'text_url' => self::safeUrl( self::setting( 'TextURL' ) ),
            'text_page' => (string)self::setting( 'TextPage' ),
            'notice_page' => (string)self::setting( 'NoticePage' ),
            'explainer_page' => (string)self::setting( 'ExplainerPage' ),
            'holder' => $holder,
            'years' => $years,
            'copyright' => $copyright,
            'notice' => $notice );
    }

    /**
     * The license that applies to one page (id below doc/, as the viewer uses it): info() plus 'free' (true for a
     * page under the documentation license) and, for an inherited page, its copyright and license.
     *
     * @param string $id page id ('help/getting-started.md'); '' for the help as a whole
     * @return array
     */
    public static function forPage( $id = '' )
    {
        $info = self::info();
        $info['id'] = (string)$id;
        // an extension's own documentation (ext/<name>/...) has the extension's license, not the kernel's
        $info['extension'] = preg_match( '#^ext/([^/]+)/#', (string)$id, $m ) ? $m[1] : '';
        $info['free'] = $info['extension'] === '' && !self::isOtherLicense( (string)$id );
        $info['other_copyright'] = (string)self::setting( 'OtherCopyright' );
        $info['other_name'] = (string)self::setting( 'OtherLicenseName' );
        $info['other_url'] = self::safeUrl( self::setting( 'OtherLicenseURL' ) );
        return $info;
    }

    /**
     * Whether a page keeps another license (OtherLicensePathRules[] match its doc/ path).
     *
     * @param string $id page id below doc/ or a path starting with doc/
     * @return bool
     */
    public static function isOtherLicense( $id )
    {
        $id = ltrim( (string)$id, '/' );
        if ( $id === '' || strpos( $id, 'ext/' ) === 0 ) // extension pages: see forPage()
            return false;
        $path = strpos( $id, 'doc/' ) === 0 ? $id : 'doc/' . $id;
        foreach ( (array)self::setting( 'OtherLicensePathRules' ) as $pattern )
            if ( fnmatch( trim( $pattern ), $path ) )
                return true;
        return false;
    }

    /**
     * The notice of a page as plain text, for a print footer or the last page of a PDF.
     *
     * @param string $id
     * @return string
     */
    public static function noticeText( $id = '' )
    {
        $license = self::forPage( $id );
        if ( !$license['enabled'] )
            return '';
        if ( $license['extension'] !== '' )
            return 'This page is part of the documentation of the extension ' . $license['extension']
                 . ' and is distributed under the license of that extension.';
        if ( !$license['free'] )
            return trim( $license['other_copyright'] . "\n"
                 . 'This page was inherited from the documentation of the original software and is not covered by the '
                 . $license['name'] . '. It is distributed under the ' . $license['other_name']
                 . ( $license['other_url'] !== '' ? ' (' . $license['other_url'] . ')' : '' ) . '.' );
        return $license['copyright'] . "\n" . $license['notice'] . "\n"
             . 'This documentation is free documentation, licensed under the ' . $license['name'] . ' '
             . $license['version'] . ( $license['or_later'] ? ' or later' : '' )
             . ( $license['url'] !== '' ? ': ' . $license['url'] : '' ) . '.';
    }

    /**
     * Whether a file holds the unmodified GFDL 1.3 text (SHA-256 of the official file).
     *
     * @param string $file
     * @return bool
     */
    public static function isOfficialText( $file )
    {
        return is_file( $file ) && hash_file( 'sha256', $file ) === self::GFDL_13_SHA256;
    }

    /**
     * An https URL, or '' (the settings feed href attributes).
     *
     * @param mixed $url
     * @return string
     */
    protected static function safeUrl( $url )
    {
        $url = is_scalar( $url ) ? trim( (string)$url ) : '';
        return preg_match( '#^https://[^\s"<>]+$#', $url ) ? $url : '';
    }
}

?>
