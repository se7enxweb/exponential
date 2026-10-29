<?php
/**
 * Twig's Environment for persistent workers (Velocity): Twig names a compiled template's PHP class after the template
 * name and the options only, and a worker that has declared the class keeps it after the file changes. With auto_reload
 * on ([TwigSettings] AutoReload=enabled), the class name also carries the file's modification time, so a changed
 * template is compiled and declared again under a new name (the old class stays in memory until the worker ends).
 * With auto_reload off nothing changes: a deploy restarts the workers. Within one page each template's file is looked at
 * once (pageStart(), called by expTwigRenderer when a page's first .twig starts).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigEnvironment extends \Twig\Environment
{
    /** @var string */
    private $expRoot;

    public function __construct( \Twig\Loader\LoaderInterface $loader, array $options, $root )
    {
        parent::__construct( $loader, $options );
        $this->expRoot = rtrim( $root, '/' );
    }

    /** @var string[] name and index => class name, for the page being rendered (pageStart() forgets them) */
    private $classes = array();

    /**
     * A new page: a template may have changed since the last one; within a page every template is stat()ed once.
     */
    public function pageStart()
    {
        $this->classes = array();
        $this->variant = null;
    }

    /** @var string|null see variant() */
    private $variant = null;

    /**
     * The siteaccess and locale a template is compiled for: translations with constant texts are part of the compiled
     * template (expTwigConstantVisitor), so each siteaccess and language gets compiled templates of its own, as
     * compiled .tpl files do.
     *
     * @return string
     */
    public function variant()
    {
        if ( $this->variant === null )
        {
            $access = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : '';
            $this->variant = substr( md5( $access . "\n" . eZLocale::instance()->localeFullCode() . "\n" .
                                          eZINI::instance()->variable( 'RegionalSettings', 'TextTranslation' ) ), 0, 8 );
        }
        return $this->variant;
    }

    public function getTemplateClass( string $name, ?int $index = null ): string
    {
        $key = $index === null ? $name : $name . "\0" . $index;
        if ( isset( $this->classes[$key] ) )
            return $this->classes[$key];
        return $this->classes[$key] = $this->templateClass( $name, $index );
    }

    private function templateClass( string $name, ?int $index )
    {
        $class = self::withSuffix( parent::getTemplateClass( $name, $index ), '_v' . $this->variant(), $index );
        if ( !$this->isAutoReload() )
            return $class;
        try
        {
            $file = $this->getLoader()->getCacheKey( $name );
        }
        catch ( \Twig\Error\LoaderError $e )
        {
            return $class;
        }
        if ( $file === '' || $file[0] !== '/' )
            $file = $this->expRoot . '/' . $file;
        // another process writes the file: the worker's stat cache must not answer for it
        clearstatcache( true, $file );
        $mtime = @filemtime( $file );
        if ( $mtime === false )
            return $class;
        return self::withSuffix( $class, '_m' . $mtime, $index );
    }

    /**
     * A class name with a suffix before an index (___N), which Twig appends to name a template's embedded classes.
     */
    private static function withSuffix( $class, $suffix, $index )
    {
        $marker = null === $index ? '' : '___' . $index;
        $base = $marker === '' ? $class : substr( $class, 0, -strlen( $marker ) );
        return $base . $suffix . $marker;
    }
}
