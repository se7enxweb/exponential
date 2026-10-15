<?php
/**
 * File containing the expSvgSanitizer class
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * Makes an SVG image safe to store and serve from the site's own origin, by allow-list.
 *
 * An SVG is an XML document a browser can run script from when it is opened directly or embedded
 * as a document. The sanitizer parses the markup with DOMDocument (no network, no entity
 * substitution, no DTD loading; a DOCTYPE with an internal subset or entity declarations is
 * refused, which also refuses XXE and "billion laughs" files) and builds a new document from the
 * parts it knows to be safe:
 *
 * - elements: the drawing, structure, gradient, filter and text elements of SVG 1.1 / 2 (the
 *   svg-sanitize and DOMPurify SVG profiles), always in the SVG namespace. Everything else is
 *   left out together with its content: script, foreignObject (and any HTML inside it), iframe,
 *   elements of editor namespaces (Inkscape, Sodipodi, Illustrator), comments, processing
 *   instructions and CDATA wrappers;
 * - attributes: the SVG presentation and geometry attributes, id, class, style, role, aria-*,
 *   xml:space, xml:lang. No on* event attribute, no editor attribute;
 * - references: href and xlink:href only to an internal #fragment, except <a> (http, https and
 *   mailto as well) and <image>, which keeps only an embedded data:image/png, jpeg, gif or webp in
 *   base64 (an <image> with any other source is left out). javascript:, vbscript: and every other
 *   data: URL are removed wherever they appear;
 * - url(...) in style, a <style> element and presentation attributes (fill, mask, filter ...)
 *   only to an internal #fragment; @import, expression(), behavior and -moz-binding are removed,
 *   after CSS escapes are decoded;
 * - <animate>, <set>, <animateTransform>, <animateMotion> may not target href, style or an event
 *   attribute, and their values are checked like any other value.
 *
 * A gzip-compressed SVG (.svgz) is decompressed first; the result is always plain SVG. The input
 * is limited in size (decompressed) and in the number of nodes and depth of the tree.
 *
 * sanitize() returns the safe markup, or false when the input is not well-formed SVG or nothing
 * drawable remains; lastError() says why.
 *
 * Serving: SVG files should go out with the headers of servingHeaders() as a second line of
 * defence (Content-Security-Policy: sandbox, X-Content-Type-Options: nosniff).
 *
 * Stateless apart from the last error message.
 *
 * @package lib
 */
class expSvgSanitizer
{
    const SVG_NS = 'http://www.w3.org/2000/svg';
    const XLINK_NS = 'http://www.w3.org/1999/xlink';
    const XML_NS = 'http://www.w3.org/XML/1998/namespace';

    /** The Content-Security-Policy SVG files are served with */
    const SERVE_CSP = "sandbox; default-src 'none'; style-src 'unsafe-inline'";

    /** Largest SVG accepted, in bytes, after decompression */
    const DEFAULT_MAX_BYTES = 5242880;

    /** Most nodes (elements, attributes and text) the input may have */
    const DEFAULT_MAX_NODES = 50000;

    /** Deepest element nesting accepted */
    const DEFAULT_MAX_DEPTH = 128;

    /** Lower-case name => the name written */
    const ELEMENTS = array(
        'svg' => 'svg', 'g' => 'g', 'defs' => 'defs', 'symbol' => 'symbol', 'use' => 'use', 'switch' => 'switch',
        'title' => 'title', 'desc' => 'desc', 'metadata' => 'metadata', 'view' => 'view', 'a' => 'a',
        'path' => 'path', 'rect' => 'rect', 'circle' => 'circle', 'ellipse' => 'ellipse', 'line' => 'line',
        'polyline' => 'polyline', 'polygon' => 'polygon', 'image' => 'image', 'style' => 'style',
        'text' => 'text', 'tspan' => 'tspan', 'textpath' => 'textPath',
        'lineargradient' => 'linearGradient', 'radialgradient' => 'radialGradient', 'stop' => 'stop',
        'pattern' => 'pattern', 'clippath' => 'clipPath', 'mask' => 'mask', 'marker' => 'marker',
        'filter' => 'filter',
        'feblend' => 'feBlend', 'fecolormatrix' => 'feColorMatrix', 'fecomponenttransfer' => 'feComponentTransfer',
        'fecomposite' => 'feComposite', 'feconvolvematrix' => 'feConvolveMatrix',
        'fediffuselighting' => 'feDiffuseLighting', 'fedisplacementmap' => 'feDisplacementMap',
        'fedistantlight' => 'feDistantLight', 'fedropshadow' => 'feDropShadow', 'feflood' => 'feFlood',
        'fefunca' => 'feFuncA', 'fefuncb' => 'feFuncB', 'fefuncg' => 'feFuncG', 'fefuncr' => 'feFuncR',
        'fegaussianblur' => 'feGaussianBlur', 'feimage' => 'feImage', 'femerge' => 'feMerge',
        'femergenode' => 'feMergeNode', 'femorphology' => 'feMorphology', 'feoffset' => 'feOffset',
        'fepointlight' => 'fePointLight', 'fespecularlighting' => 'feSpecularLighting',
        'fespotlight' => 'feSpotLight', 'fetile' => 'feTile', 'feturbulence' => 'feTurbulence',
        'animate' => 'animate', 'animatetransform' => 'animateTransform', 'animatemotion' => 'animateMotion',
        'mpath' => 'mpath', 'set' => 'set',
    );

    /** Elements whose text is kept */
    const TEXT_ELEMENTS = array( 'title', 'desc', 'text', 'tspan', 'textPath', 'style', 'a' );

    /** Elements that draw nothing on their own, for "nothing safe remains" */
    const NON_DRAWING = array( 'title', 'desc', 'metadata', 'style' );

    /** Animation elements, whose target attribute is checked */
    const ANIMATION = array( 'animate', 'animateTransform', 'animateMotion', 'set' );

    /** Attributes without a namespace: lower-case name => the name written */
    const ATTRIBUTES = array(
        'id' => 'id', 'class' => 'class', 'style' => 'style', 'role' => 'role', 'lang' => 'lang', 'tabindex' => 'tabindex',
        'version' => 'version', 'baseprofile' => 'baseProfile', 'viewbox' => 'viewBox',
        'preserveaspectratio' => 'preserveAspectRatio', 'zoomandpan' => 'zoomAndPan',
        'x' => 'x', 'y' => 'y', 'x1' => 'x1', 'y1' => 'y1', 'x2' => 'x2', 'y2' => 'y2', 'z' => 'z',
        'width' => 'width', 'height' => 'height', 'cx' => 'cx', 'cy' => 'cy', 'r' => 'r', 'rx' => 'rx', 'ry' => 'ry',
        'fx' => 'fx', 'fy' => 'fy', 'fr' => 'fr', 'd' => 'd', 'points' => 'points', 'pathlength' => 'pathLength',
        'dx' => 'dx', 'dy' => 'dy', 'rotate' => 'rotate', 'transform' => 'transform', 'transform-origin' => 'transform-origin',
        'href' => 'href', 'target' => 'target', 'type' => 'type', 'media' => 'media', 'title' => 'title',
        'fill' => 'fill', 'fill-opacity' => 'fill-opacity', 'fill-rule' => 'fill-rule', 'stroke' => 'stroke',
        'stroke-width' => 'stroke-width', 'stroke-opacity' => 'stroke-opacity', 'stroke-linecap' => 'stroke-linecap',
        'stroke-linejoin' => 'stroke-linejoin', 'stroke-miterlimit' => 'stroke-miterlimit',
        'stroke-dasharray' => 'stroke-dasharray', 'stroke-dashoffset' => 'stroke-dashoffset',
        'vector-effect' => 'vector-effect', 'paint-order' => 'paint-order',
        'opacity' => 'opacity', 'color' => 'color', 'display' => 'display', 'visibility' => 'visibility',
        'overflow' => 'overflow', 'clip' => 'clip', 'clip-path' => 'clip-path', 'clip-rule' => 'clip-rule',
        'mask' => 'mask', 'filter' => 'filter', 'marker-start' => 'marker-start', 'marker-mid' => 'marker-mid',
        'marker-end' => 'marker-end', 'mix-blend-mode' => 'mix-blend-mode', 'isolation' => 'isolation',
        'color-interpolation' => 'color-interpolation', 'color-interpolation-filters' => 'color-interpolation-filters',
        'color-rendering' => 'color-rendering', 'shape-rendering' => 'shape-rendering',
        'image-rendering' => 'image-rendering', 'text-rendering' => 'text-rendering',
        'stop-color' => 'stop-color', 'stop-opacity' => 'stop-opacity', 'offset' => 'offset',
        'flood-color' => 'flood-color', 'flood-opacity' => 'flood-opacity', 'lighting-color' => 'lighting-color',
        'font-family' => 'font-family', 'font-size' => 'font-size', 'font-size-adjust' => 'font-size-adjust',
        'font-stretch' => 'font-stretch', 'font-style' => 'font-style', 'font-variant' => 'font-variant',
        'font-weight' => 'font-weight', 'letter-spacing' => 'letter-spacing', 'word-spacing' => 'word-spacing',
        'text-anchor' => 'text-anchor', 'text-decoration' => 'text-decoration', 'dominant-baseline' => 'dominant-baseline',
        'alignment-baseline' => 'alignment-baseline', 'baseline-shift' => 'baseline-shift',
        'writing-mode' => 'writing-mode', 'direction' => 'direction', 'unicode-bidi' => 'unicode-bidi',
        'textlength' => 'textLength', 'lengthadjust' => 'lengthAdjust', 'startoffset' => 'startOffset',
        'method' => 'method', 'spacing' => 'spacing', 'side' => 'side',
        'gradientunits' => 'gradientUnits', 'gradienttransform' => 'gradientTransform', 'spreadmethod' => 'spreadMethod',
        'patternunits' => 'patternUnits', 'patterncontentunits' => 'patternContentUnits',
        'patterntransform' => 'patternTransform', 'clippathunits' => 'clipPathUnits',
        'maskunits' => 'maskUnits', 'maskcontentunits' => 'maskContentUnits',
        'markerunits' => 'markerUnits', 'markerwidth' => 'markerWidth', 'markerheight' => 'markerHeight',
        'refx' => 'refX', 'refy' => 'refY', 'orient' => 'orient',
        'filterunits' => 'filterUnits', 'primitiveunits' => 'primitiveUnits', 'in' => 'in', 'in2' => 'in2',
        'result' => 'result', 'mode' => 'mode', 'values' => 'values', 'operator' => 'operator',
        'k1' => 'k1', 'k2' => 'k2', 'k3' => 'k3', 'k4' => 'k4', 'order' => 'order', 'kernelmatrix' => 'kernelMatrix',
        'divisor' => 'divisor', 'bias' => 'bias', 'targetx' => 'targetX', 'targety' => 'targetY',
        'edgemode' => 'edgeMode', 'kernelunitlength' => 'kernelUnitLength', 'preservealpha' => 'preserveAlpha',
        'surfacescale' => 'surfaceScale', 'diffuseconstant' => 'diffuseConstant',
        'specularconstant' => 'specularConstant', 'specularexponent' => 'specularExponent',
        'scale' => 'scale', 'xchannelselector' => 'xChannelSelector', 'ychannelselector' => 'yChannelSelector',
        'azimuth' => 'azimuth', 'elevation' => 'elevation', 'pointsatx' => 'pointsAtX', 'pointsaty' => 'pointsAtY',
        'pointsatz' => 'pointsAtZ', 'limitingconeangle' => 'limitingConeAngle', 'stddeviation' => 'stdDeviation',
        'radius' => 'radius', 'basefrequency' => 'baseFrequency', 'numoctaves' => 'numOctaves', 'seed' => 'seed',
        'stitchtiles' => 'stitchTiles', 'tablevalues' => 'tableValues', 'slope' => 'slope', 'intercept' => 'intercept',
        'amplitude' => 'amplitude', 'exponent' => 'exponent',
        'attributename' => 'attributeName', 'attributetype' => 'attributeType', 'begin' => 'begin', 'dur' => 'dur',
        'end' => 'end', 'min' => 'min', 'max' => 'max', 'restart' => 'restart', 'repeatcount' => 'repeatCount',
        'repeatdur' => 'repeatDur', 'calcmode' => 'calcMode', 'keytimes' => 'keyTimes', 'keysplines' => 'keySplines',
        'keypoints' => 'keyPoints', 'from' => 'from', 'to' => 'to', 'by' => 'by', 'additive' => 'additive',
        'accumulate' => 'accumulate', 'path' => 'path', 'systemlanguage' => 'systemLanguage',
    );

    /** Attributes whose value is a reference */
    const REFERENCE_ATTRIBUTES = array( 'href' );

    /** Image types an <image> may embed as data: */
    const DATA_IMAGE_TYPES = array( 'image/png', 'image/jpeg', 'image/gif', 'image/webp' );

    /** @var string why the last call returned false ('' after a success) */
    protected static $lastError = '';

    /**
     * $markup as safe SVG, or false.
     *
     * @param string $markup SVG markup, or gzip-compressed SVG
     * @param array $options maxBytes, maxNodes, maxDepth
     * @return string|false
     */
    public static function sanitize( $markup, array $options = array() )
    {
        static::$lastError = '';
        $maxBytes = isset( $options['maxBytes'] ) ? (int)$options['maxBytes'] : self::DEFAULT_MAX_BYTES;
        $maxNodes = isset( $options['maxNodes'] ) ? (int)$options['maxNodes'] : self::DEFAULT_MAX_NODES;
        $maxDepth = isset( $options['maxDepth'] ) ? (int)$options['maxDepth'] : self::DEFAULT_MAX_DEPTH;

        if ( !is_string( $markup ) || $markup === '' )
            return static::fail( 'empty' );
        $markup = static::decompress( $markup, $maxBytes );
        if ( $markup === false )
            return false;
        if ( strlen( $markup ) > $maxBytes )
            return static::fail( 'too large' );
        // UTF-16 and UTF-32 would hide the markup from the checks below: only UTF-8 (or ASCII-based) input
        if ( strpos( $markup, "\0" ) !== false )
            return static::fail( 'not UTF-8' );

        // Entity declarations and internal DTD subsets are refused before parsing
        if ( preg_match( '/<!ENTITY/i', $markup ) || preg_match( '/<!DOCTYPE[^>\[]*\[/i', $markup ) )
            return static::fail( 'DOCTYPE with entities' );

        $doc = static::parse( $markup );
        if ( !$doc )
            return false;
        if ( $doc->doctype && ( $doc->doctype->entities->length > 0 || trim( (string)$doc->doctype->internalSubset ) !== '' ) )
            return static::fail( 'DOCTYPE with entities' );

        $root = $doc->documentElement;
        if ( !$root || strtolower( $root->localName ) !== 'svg' ||
             ( $root->namespaceURI !== null && $root->namespaceURI !== self::SVG_NS ) )
            return static::fail( 'not SVG' );

        $count = 0;
        if ( !static::withinLimits( $root, $maxNodes, $maxDepth, 0, $count ) )
            return false;

        $out = new DOMDocument( '1.0', 'UTF-8' );
        $outRoot = static::copyElement( $root, $out, $root->namespaceURI );
        if ( !$outRoot )
            return static::fail( 'not SVG' );
        $out->appendChild( $outRoot );

        if ( !static::hasDrawing( $outRoot ) )
            return static::fail( 'nothing safe remains' );

        $xml = $out->saveXML( $out->documentElement );
        if ( !is_string( $xml ) || $xml === '' )
            return static::fail( 'not SVG' );
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $xml . "\n";
    }

    /**
     * Sanitizes the file at $path into $targetPath ($path itself when null). The result is always
     * plain (not compressed) SVG.
     *
     * @param string $path
     * @param string|null $targetPath
     * @param array $options see sanitize()
     * @return bool false when the file is refused; the file is then left unchanged
     */
    public static function sanitizeFile( $path, $targetPath = null, array $options = array() )
    {
        $markup = static::readFile( $path, isset( $options['maxBytes'] ) ? (int)$options['maxBytes'] : self::DEFAULT_MAX_BYTES );
        if ( $markup === false )
            return false;
        $clean = static::sanitize( $markup, $options );
        if ( $clean === false )
            return false;
        $targetPath = $targetPath === null ? $path : $targetPath;
        return @file_put_contents( $targetPath, $clean ) === strlen( $clean );
    }

    /**
     * Whether the file at $path is an SVG by content (gzip-compressed or not): its root element,
     * after any XML declaration, comments and DOCTYPE, is <svg>.
     *
     * @param string $path
     * @return bool
     */
    public static function isSVGFile( $path )
    {
        if ( !is_string( $path ) || $path === '' || strpos( $path, "\0" ) !== false || !is_file( $path ) || !is_readable( $path ) )
            return false;
        $head = (string)@file_get_contents( $path, false, null, 0, 65536 );
        if ( strncmp( $head, "\x1f\x8b", 2 ) === 0 )
        {
            $fp = @gzopen( $path, 'rb' );
            if ( !$fp )
                return false;
            $head = (string)@gzread( $fp, 65536 );
            gzclose( $fp );
        }
        return static::isSVGMarkup( $head );
    }

    /**
     * Whether $markup (or its start) has <svg> as its root element.
     *
     * @param string $markup
     * @return bool
     */
    public static function isSVGMarkup( $markup )
    {
        if ( !is_string( $markup ) || $markup === '' )
            return false;
        // a byte order mark, then the prolog: XML declaration, comments, PIs, DOCTYPE, white space
        $markup = preg_replace( '/^\xEF\xBB\xBF/', '', $markup );
        $prolog = '/^\s*(?:<\?xml[^>]*\?>\s*)?(?:(?:<!--.*?-->|<\?[^>]*\?>|<!DOCTYPE[^>\[]*(?:\[.*?\])?\s*>)\s*)*<((?:[A-Za-z_][\w.-]*:)?svg)[\s>\/]/is';
        return preg_match( $prolog, $markup ) === 1;
    }

    /**
     * The content of the file at $path, decompressed when gzip-compressed, or false.
     *
     * @param string $path
     * @param int $maxBytes
     * @return string|false
     */
    public static function readFile( $path, $maxBytes = self::DEFAULT_MAX_BYTES )
    {
        static::$lastError = '';
        if ( !is_string( $path ) || $path === '' || strpos( $path, "\0" ) !== false || !is_file( $path ) || !is_readable( $path ) )
            return static::fail( 'no file' );
        $size = (int)@filesize( $path );
        if ( $size <= 0 )
            return static::fail( 'empty' );
        if ( $size > $maxBytes )
            return static::fail( 'too large' );
        $data = @file_get_contents( $path );
        if ( !is_string( $data ) )
            return static::fail( 'no file' );
        return static::decompress( $data, $maxBytes );
    }

    /**
     * Width and height in CSS pixels, from the width and height attributes, else the viewBox;
     * false when neither gives them.
     *
     * @param string $markupOrPath SVG markup, or the path of an SVG file
     * @return array{0: int, 1: int}|false
     */
    public static function dimensions( $markupOrPath )
    {
        if ( !is_string( $markupOrPath ) || $markupOrPath === '' )
            return false;
        $markup = $markupOrPath;
        if ( strpos( $markupOrPath, '<' ) === false )
        {
            $markup = static::readFile( $markupOrPath );
            if ( $markup === false )
                return false;
        }
        else
        {
            $markup = static::decompress( $markup, self::DEFAULT_MAX_BYTES );
            if ( $markup === false )
                return false;
        }
        if ( preg_match( '/<!ENTITY/i', $markup ) )
            return false;
        $doc = static::parse( $markup );
        if ( !$doc || !$doc->documentElement || strtolower( $doc->documentElement->localName ) !== 'svg' )
            return false;
        $root = $doc->documentElement;

        $viewBox = false;
        if ( $root->hasAttribute( 'viewBox' ) )
        {
            $parts = preg_split( '/[\s,]+/', trim( $root->getAttribute( 'viewBox' ) ) );
            if ( count( $parts ) === 4 && is_numeric( $parts[2] ) && is_numeric( $parts[3] ) && $parts[2] > 0 && $parts[3] > 0 )
                $viewBox = array( (float)$parts[2], (float)$parts[3] );
        }
        $width = static::length( $root->getAttribute( 'width' ) );
        $height = static::length( $root->getAttribute( 'height' ) );
        if ( $width === false && $height === false )
        {
            if ( !$viewBox )
                return false;
            list( $width, $height ) = $viewBox;
        }
        else if ( $width === false || $height === false )
        {
            if ( !$viewBox )
                return false;
            if ( $width === false )
                $width = $height * $viewBox[0] / $viewBox[1];
            else
                $height = $width * $viewBox[1] / $viewBox[0];
        }
        return array( max( 1, (int)round( $width ) ), max( 1, (int)round( $height ) ) );
    }

    /**
     * The headers an SVG file is served with: name => value.
     *
     * @return array<string, string>
     */
    public static function servingHeaders()
    {
        return array( 'Content-Security-Policy' => self::SERVE_CSP,
                      'X-Content-Type-Options' => 'nosniff' );
    }

    /**
     * Why the last call returned false ('' when it did not).
     *
     * @return string
     */
    public static function lastError()
    {
        return static::$lastError;
    }

    /**
     * @param string $reason
     * @return false
     */
    protected static function fail( $reason )
    {
        static::$lastError = $reason;
        return false;
    }

    /**
     * $data decompressed when it is gzip data, else $data; false over $maxBytes or when broken.
     *
     * @param string $data
     * @param int $maxBytes
     * @return string|false
     */
    protected static function decompress( $data, $maxBytes )
    {
        if ( strncmp( $data, "\x1f\x8b", 2 ) !== 0 )
            return $data;
        if ( !function_exists( 'gzdecode' ) )
            return static::fail( 'compressed SVG without zlib' );
        $plain = @gzdecode( $data, $maxBytes + 1 );
        if ( !is_string( $plain ) )
            return static::fail( 'broken or too large compressed SVG' );
        if ( strlen( $plain ) > $maxBytes )
            return static::fail( 'too large' );
        if ( strncmp( $plain, "\x1f\x8b", 2 ) === 0 )
            return static::fail( 'compressed twice' );
        return $plain;
    }

    /**
     * $markup parsed without network access, entity substitution or DTD loading, or false.
     *
     * @param string $markup
     * @return DOMDocument|false
     */
    protected static function parse( $markup )
    {
        $doc = new DOMDocument();
        $useErrors = libxml_use_internal_errors( true );
        $loader = static function () { return null; };
        libxml_set_external_entity_loader( $loader );
        try
        {
            $ok = $doc->loadXML( $markup, LIBXML_NONET | LIBXML_NOCDATA | LIBXML_COMPACT );
        }
        catch ( Throwable $e )
        {
            $ok = false;
        }
        libxml_set_external_entity_loader( null );
        libxml_clear_errors();
        libxml_use_internal_errors( $useErrors );
        if ( !$ok )
            return static::fail( 'not well-formed' );
        return $doc;
    }

    /**
     * Whether the tree below $node stays within the node and depth limits.
     *
     * @param DOMNode $node
     * @param int $maxNodes
     * @param int $maxDepth
     * @param int $depth
     * @param int $count
     * @return bool
     */
    protected static function withinLimits( DOMNode $node, $maxNodes, $maxDepth, $depth, &$count )
    {
        if ( $depth > $maxDepth )
            return static::fail( 'nested too deeply' );
        $count += 1 + ( $node->attributes ? $node->attributes->length : 0 );
        if ( $count > $maxNodes )
            return static::fail( 'too many nodes' );
        foreach ( $node->childNodes as $child )
        {
            if ( $child->nodeType === XML_ELEMENT_NODE )
            {
                if ( !static::withinLimits( $child, $maxNodes, $maxDepth, $depth + 1, $count ) )
                    return false;
            }
            else if ( ++$count > $maxNodes )
            {
                return static::fail( 'too many nodes' );
            }
        }
        return true;
    }

    /**
     * A safe copy of $element in $out, or null when the element is not kept.
     *
     * @param DOMElement $element
     * @param DOMDocument $out
     * @param string|null $rootNamespace the namespace of the input's root (null: none)
     * @return DOMElement|null
     */
    protected static function copyElement( DOMElement $element, DOMDocument $out, $rootNamespace )
    {
        // Only SVG elements: in the SVG namespace, or without one when the root has none
        if ( $element->namespaceURI !== self::SVG_NS && !( $rootNamespace === null && $element->namespaceURI === null ) )
            return null;
        $lower = strtolower( (string)$element->localName );
        if ( !isset( self::ELEMENTS[$lower] ) )
            return null;
        $name = self::ELEMENTS[$lower];

        $attributes = static::safeAttributes( $element, $name );
        if ( $attributes === null )
            return null;

        $copy = $out->createElementNS( self::SVG_NS, $name );
        foreach ( $attributes as $attribute )
        {
            list( $namespace, $qualifiedName, $value ) = $attribute;
            if ( $namespace === null )
                $copy->setAttribute( $qualifiedName, $value );
            else
                $copy->setAttributeNS( $namespace, $qualifiedName, $value );
        }

        if ( $name === 'style' )
        {
            $css = '';
            foreach ( $element->childNodes as $child )
            {
                if ( $child->nodeType === XML_TEXT_NODE || $child->nodeType === XML_CDATA_SECTION_NODE )
                    $css .= $child->nodeValue;
            }
            $css = static::sanitizeCSS( $css, false );
            if ( trim( $css ) !== '' )
                $copy->appendChild( $out->createTextNode( $css ) );
            return $copy;
        }

        $keepText = in_array( $name, self::TEXT_ELEMENTS, true );
        foreach ( $element->childNodes as $child )
        {
            if ( $child->nodeType === XML_ELEMENT_NODE )
            {
                $childCopy = static::copyElement( $child, $out, $rootNamespace );
                if ( $childCopy )
                    $copy->appendChild( $childCopy );
            }
            else if ( $keepText && ( $child->nodeType === XML_TEXT_NODE || $child->nodeType === XML_CDATA_SECTION_NODE ) )
            {
                $copy->appendChild( $out->createTextNode( $child->nodeValue ) );
            }
            else if ( $child->nodeType === XML_TEXT_NODE && trim( $child->nodeValue ) === '' )
            {
                // indentation between elements
                $copy->appendChild( $out->createTextNode( $child->nodeValue ) );
            }
            // comments, processing instructions and other text are left out
        }
        return $copy;
    }

    /**
     * The attributes of $element to keep, as [namespace, qualified name, value] lists, or null when
     * the element itself has to go (an <image> without a safe source, an animation of a forbidden
     * attribute).
     *
     * @param DOMElement $element
     * @param string $name the element's SVG name
     * @return array|null
     */
    protected static function safeAttributes( DOMElement $element, $name )
    {
        $kept = array();
        $hasHref = false;
        foreach ( $element->attributes as $attribute )
        {
            $namespace = $attribute->namespaceURI;
            $local = strtolower( (string)$attribute->localName );
            $value = (string)$attribute->value;

            if ( $namespace === self::XLINK_NS )
            {
                if ( $local === 'href' )
                {
                    $href = static::safeReference( $name, $value );
                    if ( $href === false )
                    {
                        if ( $name === 'image' || $name === 'feImage' || $name === 'use' || $name === 'mpath' )
                            return null;
                        continue;
                    }
                    $kept[] = array( self::XLINK_NS, 'xlink:href', $href );
                    $hasHref = true;
                }
                else if ( $local === 'title' && !static::hasDangerousValue( $value ) )
                {
                    $kept[] = array( self::XLINK_NS, 'xlink:title', $value );
                }
                continue;
            }
            if ( $namespace === self::XML_NS )
            {
                if ( ( $local === 'space' || $local === 'lang' ) && !static::hasDangerousValue( $value ) )
                    $kept[] = array( self::XML_NS, 'xml:' . $local, $value );
                continue;
            }
            if ( $namespace !== null && $namespace !== '' )
                continue;

            if ( strncmp( $local, 'on', 2 ) === 0 )
                continue;
            if ( strncmp( $local, 'aria-', 5 ) === 0 && preg_match( '/^aria-[a-z]+$/', $local ) )
            {
                if ( !static::hasDangerousValue( $value ) )
                    $kept[] = array( null, $local, $value );
                continue;
            }
            if ( !isset( self::ATTRIBUTES[$local] ) )
                continue;
            $attributeName = self::ATTRIBUTES[$local];

            if ( $attributeName === 'href' )
            {
                $href = static::safeReference( $name, $value );
                if ( $href === false )
                {
                    if ( $name === 'image' || $name === 'feImage' || $name === 'use' || $name === 'mpath' )
                        return null;
                    continue;
                }
                $kept[] = array( null, 'href', $href );
                $hasHref = true;
                continue;
            }
            if ( $attributeName === 'style' )
            {
                $css = static::sanitizeCSS( $value, true );
                if ( trim( $css ) !== '' )
                    $kept[] = array( null, 'style', $css );
                continue;
            }
            if ( $attributeName === 'target' && !preg_match( '/^_(?:blank|self|parent|top)$/', $value ) )
                continue;
            if ( $attributeName === 'attributeName' )
            {
                $target = strtolower( trim( preg_replace( '/^[^:]*:/', '', $value ) ) );
                if ( $target === 'href' || $target === 'style' || strncmp( $target, 'on', 2 ) === 0 ||
                     !isset( self::ATTRIBUTES[$target] ) )
                    return null;
            }
            if ( static::hasDangerousValue( $value ) )
            {
                if ( in_array( $name, self::ANIMATION, true ) && in_array( $attributeName, array( 'from', 'to', 'by', 'values' ), true ) )
                    return null;
                continue;
            }
            $kept[] = array( null, $attributeName, $value );
        }

        // an <image>, <feImage> or <mpath> without a source draws nothing; an animation needs a safe target
        if ( !$hasHref && ( $name === 'image' || $name === 'feImage' || $name === 'mpath' ) )
            return null;
        if ( in_array( $name, array( 'animate', 'set' ), true ) && !$element->hasAttribute( 'attributeName' ) )
            return null;
        return $kept;
    }

    /**
     * $value as a reference the element $name may have, or false.
     *
     * @param string $name
     * @param string $value
     * @return string|false
     */
    protected static function safeReference( $name, $value )
    {
        $plain = static::normalizedValue( $value );
        if ( $plain !== '' && $plain[0] === '#' )
            return preg_match( '/^#[A-Za-z_][\w.:-]*$/', trim( $value ) ) ? trim( $value ) : false;

        if ( $name === 'a' )
        {
            if ( preg_match( '#^(?:https?://|mailto:)#', $plain ) && !preg_match( '/[\s"<>]/', trim( $value ) ) )
                return trim( $value );
            return false;
        }
        if ( $name === 'image' && preg_match( '#^data:(image/(?:png|jpeg|gif|webp));base64,([A-Za-z0-9+/=\s]+)$#', trim( $value ), $m ) )
        {
            $data = base64_decode( preg_replace( '/\s+/', '', $m[2] ), true );
            if ( $data === false || $data === '' || !function_exists( 'getimagesizefromstring' ) )
                return false;
            $info = @getimagesizefromstring( $data );
            if ( !is_array( $info ) || !in_array( $info['mime'] ?? '', self::DATA_IMAGE_TYPES, true ) )
                return false;
            return 'data:' . $info['mime'] . ';base64,' . base64_encode( $data );
        }
        return false;
    }

    /**
     * $value in lower case without white space, control characters and CSS escapes, for the checks.
     *
     * @param string $value
     * @return string
     */
    protected static function normalizedValue( $value )
    {
        $value = static::decodeCSSEscapes( (string)$value );
        $value = html_entity_decode( $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        return strtolower( preg_replace( '/[\x00-\x20\x7F]+/', '', $value ) );
    }

    /**
     * Whether $value carries a script URL, a data: URL or a url() that is not an internal fragment.
     *
     * @param string $value
     * @return bool
     */
    protected static function hasDangerousValue( $value )
    {
        $plain = static::normalizedValue( $value );
        if ( preg_match( '/(?:javascript|vbscript|livescript|data):/', $plain ) )
            return true;
        if ( strpos( $plain, 'expression(' ) !== false || strpos( $plain, '@import' ) !== false )
            return true;
        if ( preg_match_all( '/url\(([^)]*)\)?/', $plain, $m ) )
        {
            foreach ( $m[1] as $target )
            {
                $target = trim( $target, '\'"' );
                if ( $target === '' || $target[0] !== '#' )
                    return true;
            }
        }
        return false;
    }

    /**
     * $css with every CSS escape replaced by the character it stands for.
     *
     * @param string $css
     * @return string
     */
    protected static function decodeCSSEscapes( $css )
    {
        if ( strpos( $css, '\\' ) === false )
            return $css;
        $css = preg_replace( '/\\\\\r?\n/', '', $css );
        return preg_replace_callback( '/\\\\(?:([0-9a-fA-F]{1,6})\s?|(.))/su', static function ( $m ) {
            if ( isset( $m[1] ) && $m[1] !== '' )
            {
                $code = hexdec( $m[1] );
                if ( $code === 0 || $code > 0x10FFFF || ( $code >= 0xD800 && $code <= 0xDFFF ) )
                    return "\u{FFFD}";
                return mb_chr( $code, 'UTF-8' );
            }
            return $m[2];
        }, $css );
    }

    /**
     * $css without anything that loads or runs something: @import, expression(), behavior,
     * -moz-binding, script and data: URLs and url() to anything but an internal #fragment.
     *
     * @param string $css a style sheet, or with $declarations the declarations of a style attribute
     * @param bool $declarations
     * @return string
     */
    public static function sanitizeCSS( $css, $declarations )
    {
        $css = (string)$css;
        $css = preg_replace( '/[\x00-\x08\x0B\x0E-\x1F\x7F]/', '', $css );
        $css = static::decodeCSSEscapes( $css );
        $css = preg_replace( '#/\*.*?(?:\*/|$)#s', '', $css );
        // nothing that could close the element or open markup when the text is written out
        $css = str_replace( '<', '', $css );

        if ( !$declarations )
        {
            $css = preg_replace( '/@import[^;{}]*(?:;|$)/i', '', $css );
            $css = preg_replace( '/@(?:namespace|charset)[^;{}]*(?:;|$)/i', '', $css );
            // each rule's declarations are cleaned on their own
            return preg_replace_callback( '/\{([^{}]*)\}/', static function ( $m ) {
                return '{' . expSvgSanitizer::sanitizeCSS( $m[1], true ) . '}';
            }, $css );
        }

        $kept = array();
        foreach ( explode( ';', $css ) as $declaration )
        {
            if ( trim( $declaration ) === '' )
                continue;
            $colon = strpos( $declaration, ':' );
            if ( $colon === false )
                continue;
            $property = strtolower( trim( substr( $declaration, 0, $colon ) ) );
            $value = trim( substr( $declaration, $colon + 1 ) );
            if ( !preg_match( '/^-?[a-z][a-z0-9-]*$/', $property ) )
                continue;
            if ( in_array( $property, array( 'behavior', '-moz-binding', 'binding' ), true ) )
                continue;
            if ( static::hasDangerousValue( $value ) || stripos( $value, '@import' ) !== false )
                continue;
            $kept[] = $property . ':' . $value;
        }
        return implode( ';', $kept );
    }

    /**
     * Whether $element draws something: an element other than title, desc, metadata and style.
     *
     * @param DOMElement $element
     * @return bool
     */
    protected static function hasDrawing( DOMElement $element )
    {
        foreach ( $element->childNodes as $child )
        {
            if ( $child->nodeType === XML_ELEMENT_NODE && !in_array( $child->localName, self::NON_DRAWING, true ) )
                return true;
        }
        return false;
    }

    /**
     * A length attribute in CSS pixels, or false (none, a percentage, or not a length).
     *
     * @param string $value
     * @return float|false
     */
    protected static function length( $value )
    {
        if ( !preg_match( '/^\s*([0-9]*\.?[0-9]+(?:[eE][-+]?[0-9]+)?)\s*(px|pt|pc|mm|cm|in|em|ex)?\s*$/', (string)$value, $m ) )
            return false;
        $factors = array( '' => 1, 'px' => 1, 'pt' => 96 / 72, 'pc' => 16, 'mm' => 96 / 25.4, 'cm' => 96 / 2.54,
                          'in' => 96, 'em' => 16, 'ex' => 8 );
        $number = (float)$m[1] * $factors[isset( $m[2] ) ? strtolower( $m[2] ) : ''];
        return $number > 0 ? $number : false;
    }
}
