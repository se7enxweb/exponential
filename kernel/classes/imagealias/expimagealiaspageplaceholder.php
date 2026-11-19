<?php
/**
 * File containing the expImageAliasPagePlaceholder class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The placeholders a page links to itself when it shows many images whose aliases are not made yet
 * ([AliasSettings] PagePlaceholders, PagePlaceholderAfter).
 *
 * In on-demand mode every alias not made yet is linked through content/image, which makes it or answers a
 * placeholder: one PHP request per image. On a page of hundreds of new images those requests, not the conversions,
 * decide when the page has loaded (on a busy server 75 to 180 a second through nginx, Apache and PHP-FPM, fewer over
 * one HTTP/2 connection to Velocity). So past PagePlaceholderAfter aliases not made yet in one page, the rest are
 * linked to a static placeholder of their size, which the web server answers without PHP and the browser asks for
 * once per size:
 *
 *     var/<site>/cache/public/imagealias-placeholder/160x106.svg#exp-src=<the content/image URL>&exp-depth=6
 *
 * The fragment is never sent to the server; it names the address of the real image for the swap script
 * (exp_image_swap.js), and exp-depth the number of path segments of the placeholder below the installation's root,
 * so the script resolves a relative address as the template's ezroot did. The aliases go to the backlog of the server
 * while the page is made (expImageAliasBacklog), in page order, and the request that made the page makes them right
 * after its answer, as content/image requests do; the swap script then shows each image once it is made.
 *
 * A page that links such placeholders is not kept in the view cache for longer than PagePlaceholderTime seconds
 * (expCacheUntil::limit()), so the next visitors get the real addresses.
 *
 * Only where the backlog applies (a server that answers first: PHP-FPM, LiteSpeed, FrankenPHP, Velocity) and only
 * for an alias whose URL asks for it to be made: an outdated alias keeps its URL (its old file is shown until then).
 *
 * @since 6.0.15
 * @package kernel
 */
class expImageAliasPagePlaceholder
{
    /** Directory of the placeholder files below <cache dir> (served by the web server as the other public caches) */
    const DIRECTORY = 'public/imagealias-placeholder';

    /** The fragment that names the real image */
    const FRAGMENT = 'exp-src';

    /** @var string the request the counter belongs to (a persistent worker serves many) */
    protected static $request = null;

    /** @var int aliases not made yet linked by this request */
    protected static $count = 0;

    /** @var array the entries linked through content/image so far (entry, attribute, alias name), in page order */
    protected static $first = array();

    /** @var array size => URL of the placeholder file, or false when it cannot be written */
    protected static $files = array();

    /** @var bool|null whether the feature applies to this process, over the settings and the server (tests) */
    protected static $applies = null;

    /** @var string|null the cache directory (tests) */
    protected static $cacheDirectory = null;

    /**
     * Sets whether the feature applies, over the settings and the server (tests); null: as configured.
     *
     * @param bool|null $applies
     */
    public static function setApplies( $applies )
    {
        self::$applies = $applies === null ? null : (bool)$applies;
    }

    /**
     * Sets the cache directory the placeholder files go to (tests); null: eZSys::cacheDirectory().
     *
     * @param string|null $directory
     */
    public static function setCacheDirectory( $directory )
    {
        self::$cacheDirectory = $directory === null ? null : rtrim( (string)$directory, '/' );
        self::$files = array();
    }

    /**
     * Starts the count again (a new request in a persistent worker does this by itself; tests).
     */
    public static function reset()
    {
        self::$request = null;
        self::$count = 0;
        self::$first = array();
        self::$files = array();
    }

    /**
     * @return int aliases not made yet this request has linked so far
     */
    public static function count()
    {
        self::startRequest();
        return self::$count;
    }

    /**
     * Whether pages link placeholders of their own: PagePlaceholders enabled and a server that makes the backlog
     * (expImageAliasBacklog::applies()).
     *
     * @return bool
     */
    public static function applies()
    {
        if ( self::$applies !== null )
            return self::$applies;
        return expImageAliasSettings::pagePlaceholdersEnabled() && expImageAliasBacklog::applies();
    }

    /**
     * The entry a template gets for an alias not made yet (expImageAliasUrl::computedEntry()): as it is for the first
     * PagePlaceholderAfter aliases of this request, then with the placeholder as its URL, the alias in the backlog and
     * the page's cache time limited.
     *
     * @param array $entry the computed entry
     * @param array $attribute id, version, language_code
     * @param string $aliasName
     * @param array $options outdated => true for an alias whose old file is still there
     * @return array the entry; page_placeholder => true when its URL is the placeholder
     */
    public static function apply( array $entry, array $attribute, $aliasName, array $options = array() )
    {
        if ( !empty( $options['outdated'] ) || !self::applies() )
            return $entry;
        self::startRequest();
        self::$count++;
        if ( self::$count <= expImageAliasSettings::pagePlaceholderAfter() )
        {
            // Kept until the page passes the limit: then they go to the backlog first, in page order
            self::$first[] = array( $entry, $attribute, (string)$aliasName );
            return $entry;
        }
        $url = self::url( $entry );
        if ( $url === null )
            return $entry;
        // The aliases linked through content/image before this one are queued first, so the backlog is in page
        // order: the swap script asks in page order and stops at the first image still waiting. A content/image
        // request that makes one of them itself leaves an entry with nothing to do.
        foreach ( self::$first as $before )
            self::queue( $before[0], $before[1], $before[2] );
        self::$first = array();
        self::queue( $entry, $attribute, $aliasName );
        expImageAliasBacklog::scheduleDrain();
        if ( class_exists( 'expCacheUntil' ) )
            expCacheUntil::limit( time() + expImageAliasSettings::pagePlaceholderTime(), 'image aliases not made yet' );
        $entry['url'] = $url;
        $entry['page_placeholder'] = true;
        return $entry;
    }

    /**
     * Adds an alias to the backlog, with the variants a browser may be answered in.
     *
     * @param array $entry
     * @param array $attribute id, version, language_code
     * @param string $aliasName
     */
    protected static function queue( array $entry, array $attribute, $aliasName )
    {
        $formats = array();
        foreach ( expImageAliasSettings::negotiatedFormats( $aliasName ) as $format )
        {
            if ( expImageFormatConverter::variantApplies( (string)( $entry['mime_type'] ?? '' ), $format ) )
                $formats[] = $format;
        }
        // Written after the answer: hundreds of entry files are no reason to keep the page's first byte waiting
        expImageAliasBacklog::addAfterAnswer( (int)$attribute['id'], (int)$attribute['version'], (string)$attribute['language_code'],
                                              (string)$aliasName, $formats, isset( $entry['full_path'] ) ? (string)$entry['full_path'] : null );
    }

    /**
     * The placeholder URL of an entry: the file of its size (written once) and the fragment naming the real image.
     * Null when the file cannot be written.
     *
     * @param array $entry
     * @return string|null
     */
    public static function url( array $entry )
    {
        list( $width, $height ) = expImagePlaceholder::size( $entry['width'] ?? null, $entry['height'] ?? null );
        $file = self::file( $width, $height );
        if ( $file === null )
            return null;
        return $file . '#' . self::FRAGMENT . '=' . rawurlencode( (string)$entry['url'] )
             . '&exp-depth=' . count( explode( '/', $file ) );
    }

    /**
     * The placeholder file of $width x $height below the cache directory, written when it is not there.
     *
     * @param int $width
     * @param int $height
     * @return string|null its path relative to the installation, null when it cannot be written
     */
    public static function file( $width, $height )
    {
        $key = (int)$width . 'x' . (int)$height;
        if ( isset( self::$files[$key] ) )
            return self::$files[$key] === false ? null : self::$files[$key];
        $directory = ( self::$cacheDirectory !== null ? self::$cacheDirectory : eZSys::cacheDirectory() ) . '/' . self::DIRECTORY;
        $path = $directory . '/' . $key . '.svg';
        if ( !is_file( $path ) )
        {
            if ( !is_dir( $directory ) )
                eZDir::mkdir( $directory, false, true );
            $temporary = $path . '.' . getmypid() . '.tmp';
            if ( @file_put_contents( $temporary, expImagePlaceholder::svg( $width, $height ) ) === false )
            {
                self::$files[$key] = false;
                return null;
            }
            // Velocity runs as root, PHP-FPM as the site user: the web server reads it, either may write the next one
            @chmod( $temporary, eZFile::fileMode( 0666 ) );
            if ( !@rename( $temporary, $path ) )
            {
                @unlink( $temporary );
                if ( !is_file( $path ) )
                {
                    self::$files[$key] = false;
                    return null;
                }
            }
        }
        self::$files[$key] = $path;
        return $path;
    }

    /**
     * Starts the count for a new request (a persistent worker keeps statics from one request to the next).
     */
    protected static function startRequest()
    {
        $request = class_exists( 'eZExecution' ) ? eZExecution::requestKey() : '';
        if ( self::$request === $request )
            return;
        self::$request = $request;
        self::$count = 0;
        self::$first = array();
        // A persistent worker serves other siteaccesses (other cache directories) and the files may be cleared meanwhile
        self::$files = array();
    }
}
