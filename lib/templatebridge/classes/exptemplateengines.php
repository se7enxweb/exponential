<?php
/**
 * The template engines the template system hands files to, by file ending: .stpl to Smarty 5 (expSmartyRenderer),
 * .twig and .html.twig to Twig 3 (expTwigRenderer). Every other file (.tpl) is the template system's own.
 *
 * eZTemplateFileResource asks engineFor() for every template file it loads: an engine template is parsed as the
 * stub of its renderer (stubFor(), an operator call that renders the file at run time) and compiled under a key of
 * its own (cacheKey()).
 *
 * The registry is site.ini [TemplateBridgeSettings]:
 *   EngineSuffixList[<ending>]=<engine>    .stpl=stpl, .twig=twig, .html.twig=twig
 *   RendererList[<engine>]=<class>         stpl=expSmartyRenderer, twig=expTwigRenderer
 * The renderer class has the static methods stubFor( $file ) and cacheKey( $keyData, $templatePath ). The longest
 * ending matches first, so a.html.twig is a Twig template named a.html.twig and never a.html + .twig.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTemplateEngines
{
    /** The defaults, used when site.ini has no registry (and merged under it). */
    const DEFAULT_SUFFIXES = array( '.html.twig' => 'twig', '.stpl' => 'stpl', '.twig' => 'twig' );
    const DEFAULT_RENDERERS = array( 'stpl' => 'expSmartyRenderer', 'twig' => 'expTwigRenderer' );

    /** @var array|null ending => engine, longest ending first */
    private static $suffixes = null;

    /** @var array|null engine => renderer class */
    private static $renderers = null;

    /** @var array path => engine or false: the answers of engineFor() for this process */
    private static $answers = array();

    /**
     * @return array ending => engine, the longest ending first
     */
    public static function suffixes()
    {
        if ( self::$suffixes === null )
            self::load();
        return self::$suffixes;
    }

    /**
     * @return array engine => renderer class
     */
    public static function renderers()
    {
        if ( self::$renderers === null )
            self::load();
        return self::$renderers;
    }

    private static function load()
    {
        $suffixes = self::DEFAULT_SUFFIXES;
        $renderers = self::DEFAULT_RENDERERS;
        if ( class_exists( 'eZINI', false ) || class_exists( 'eZINI' ) )
        {
            $ini = eZINI::instance();
            if ( $ini->hasVariable( 'TemplateBridgeSettings', 'EngineSuffixList' ) && is_array( $list = $ini->variable( 'TemplateBridgeSettings', 'EngineSuffixList' ) ) )
            {
                foreach ( $list as $ending => $engine )
                {
                    if ( is_string( $ending ) && $ending !== '' && trim( (string)$engine ) !== '' )
                        $suffixes[$ending] = trim( $engine );
                }
            }
            if ( $ini->hasVariable( 'TemplateBridgeSettings', 'RendererList' ) && is_array( $list = $ini->variable( 'TemplateBridgeSettings', 'RendererList' ) ) )
            {
                foreach ( $list as $engine => $class )
                {
                    if ( is_string( $engine ) && $engine !== '' && trim( (string)$class ) !== '' )
                        $renderers[$engine] = trim( $class );
                }
            }
        }
        uksort( $suffixes, function ( $a, $b ) { return strlen( $b ) - strlen( $a ) ?: strcmp( $a, $b ); } );
        self::$suffixes = $suffixes;
        self::$renderers = $renderers;
    }

    /**
     * @param string $path a template file name
     * @return string|null the engine of an engine template (stpl, twig), null for a template of the template system
     */
    public static function engineFor( $path )
    {
        if ( !is_string( $path ) || $path === '' )
            return null;
        // the common case first: a .tpl never needs the registry
        if ( substr( $path, -4 ) === '.tpl' )
            return null;
        if ( isset( self::$answers[$path] ) )
            return self::$answers[$path] ?: null;
        $engine = false;
        foreach ( self::suffixes() as $ending => $suffixEngine )
        {
            $length = strlen( $ending );
            if ( strlen( $path ) > $length && substr( $path, -$length ) === $ending )
            {
                $engine = $suffixEngine;
                break;
            }
        }
        if ( count( self::$answers ) > 4096 )
            self::$answers = array();
        self::$answers[$path] = $engine;
        return $engine ?: null;
    }

    /**
     * @param string $path
     * @return bool whether the file is rendered by an engine other than the template system
     */
    public static function isEngineTemplate( $path )
    {
        return self::engineFor( $path ) !== null;
    }

    /**
     * @param string $path
     * @return string the ending of an engine template that matched ('.html.twig', '.twig', '.stpl'), '' for none
     */
    public static function suffixOf( $path )
    {
        if ( !is_string( $path ) )
            return '';
        foreach ( self::suffixes() as $ending => $engine )
        {
            $length = strlen( $ending );
            if ( strlen( $path ) > $length && substr( $path, -$length ) === $ending )
                return $ending;
        }
        return '';
    }

    /**
     * @param string $engine
     * @return string|null the renderer class of an engine
     */
    public static function rendererFor( $engine )
    {
        $renderers = self::renderers();
        return isset( $renderers[$engine] ) ? $renderers[$engine] : null;
    }

    /**
     * The template text the template system parses in place of an engine template.
     *
     * @param string $path
     * @return string|null the stub, null for a template of the template system
     */
    public static function stubFor( $path )
    {
        $engine = self::engineFor( $path );
        if ( $engine === null )
            return null;
        $class = self::rendererFor( $engine );
        if ( $class === null || !class_exists( $class ) )
            return '<!-- ' . $engine . ': no renderer for the template engine -->';
        return $class::stubFor( $path );
    }

    /**
     * Compiled-template key of a template file: the renderer's for an engine template, the kernel's md5 otherwise.
     *
     * @param string $keyData
     * @param string $templatePath
     * @return string
     */
    public static function cacheKey( $keyData, $templatePath )
    {
        $engine = self::engineFor( (string)$keyData );
        if ( $engine === null )
            $engine = self::engineFor( (string)$templatePath );
        if ( $engine !== null )
        {
            $class = self::rendererFor( $engine );
            if ( $class !== null && class_exists( $class ) )
                return $class::cacheKey( $keyData, $templatePath );
        }
        return md5( $keyData );
    }

    /**
     * Forgets the registry and the answers (tests; a persistent worker after a settings change).
     */
    public static function reset()
    {
        self::$suffixes = null;
        self::$renderers = null;
        self::$answers = array();
    }
}
