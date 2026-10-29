<?php
/**
 * File containing the expWebDAVEntryScript class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * The name of the WebDAV entry script, and the notice for requests that still come in through its old name.
 *
 * The WebDAV server is index_webdav.php, named like index_rest.php, index_treemenu.php and index_cluster.php.
 * webdav.php, its name from 3.2 to 6.0.14, is kept as a shim that only includes index_webdav.php, so that
 * virtual hosts and rewrite rules written for the old name keep working until they are changed; it is removed
 * in a later major version. A request that came through the shim writes one warning a day to warning.log,
 * telling the administrator which rules to change.
 */
class expWebDAVEntryScript
{
    /** The entry script */
    const SCRIPT = 'index_webdav.php';

    /** The old name, kept as a forwarding shim */
    const DEPRECATED_SCRIPT = 'webdav.php';

    /** The file in the log directory holding the day the last notice was written */
    const STAMP_FILE = 'webdav-entry-script-deprecated.stamp';

    /**
     * The name of the script this request came in through: webdav.php when the shim defined
     * EXP_WEBDAV_ENTRY_SCRIPT, index_webdav.php otherwise. eZSys::init() needs the name of the file
     * actually run (SCRIPT_FILENAME) to find the installation root and the index directory.
     *
     * @return string
     */
    public static function current()
    {
        if ( defined( 'EXP_WEBDAV_ENTRY_SCRIPT' ) && EXP_WEBDAV_ENTRY_SCRIPT === self::DEPRECATED_SCRIPT )
        {
            return self::DEPRECATED_SCRIPT;
        }
        return self::SCRIPT;
    }

    /**
     * Whether $script (a file name or a path) is the WebDAV entry script under either name.
     *
     * @param string $script
     * @return bool
     */
    public static function isEntryScript( $script )
    {
        $name = basename( (string)$script );
        return $name === self::SCRIPT || $name === self::DEPRECATED_SCRIPT;
    }

    /**
     * Whether $requestUri asks for the root of the WebDAV server: "", "/", or the entry script under
     * either name, with or without its trailing slash.
     *
     * @param string $requestUri
     * @return bool
     */
    public static function isRootRequest( $requestUri )
    {
        $uri = (string)$requestUri;
        if ( $uri === '' || $uri === '/' )
        {
            return true;
        }
        foreach ( array( self::SCRIPT, self::DEPRECATED_SCRIPT ) as $script )
        {
            if ( $uri === '/' . $script || $uri === '/' . $script . '/' )
            {
                return true;
            }
        }
        return false;
    }

    /**
     * The text of the deprecation notice.
     *
     * @return string
     */
    public static function deprecationMessage()
    {
        return 'WebDAV request through webdav.php, the old name of the WebDAV entry script. '
            . 'webdav.php only forwards to index_webdav.php and will be removed in a later major version: '
            . 'change the virtual host and rewrite rules (Apache RewriteRule, nginx location and '
            . 'SCRIPT_FILENAME, IIS rewrite rules) from webdav.php to index_webdav.php. '
            . 'See doc/eng-us/bc/6.0/webdav-entry-script-index-webdav.md.';
    }

    /**
     * Writes the deprecation notice to warning.log once a day: the first request through the shim on a day
     * writes it and the day into the stamp file in $logDir, later requests that day see the day and write
     * nothing.
     *
     * @param string|null $logDir The log directory; null for the one eZDebug writes to
     * @param int|null $now A timestamp; null for now
     * @return bool Whether the notice was written
     */
    public static function noteDeprecatedEntry( $logDir = null, $now = null )
    {
        if ( $logDir === null )
        {
            $logDir = eZDebug::instance()->logDirectory();
        }
        $logDir = rtrim( (string)$logDir, '/' );
        $day = date( 'Y-m-d', $now === null ? time() : (int)$now );
        $stamp = $logDir . '/' . self::STAMP_FILE;

        if ( is_file( $stamp ) && trim( (string)@file_get_contents( $stamp ) ) === $day )
        {
            return false;
        }
        if ( !is_dir( $logDir ) )
        {
            eZDir::mkdir( $logDir, false, true );
        }
        if ( !eZFile::create( self::STAMP_FILE, $logDir, $day ) )
        {
            return false;
        }
        eZLog::write( self::deprecationMessage(), 'warning.log', $logDir );
        return true;
    }
}
