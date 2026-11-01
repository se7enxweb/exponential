<?php
/**
 * File containing the expImageFocalPoint class
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The focal point and the crop rectangle of an image, and what they do to the filters of an image alias.
 *
 * An image attribute version may store a focal point (the part of the image that matters, as fractions
 * of the original's width and height, 0 to 1) and a crop rectangle (also fractions of the original). Both
 * live in the attribute's XML, as two elements of <ezimage>:
 *
 *     <focal x="0.25" y="0.4"/>
 *     <crop x="0.1" y="0" width="0.8" height="1"/>
 *
 * When an alias is made from the original:
 *
 * - the crop rectangle is cut out first, before the alias's own filters;
 * - every geometry/crop filter of the alias is moved so that its window is centered on the focal point,
 *   as far as the image allows (the window never leaves the image). Without a focal point a crop filter
 *   keeps its own offsets (within the cut image when there is a crop rectangle).
 *
 * To know where a crop filter's window lies, the size of the image is followed through the alias's filters
 * (the scale filters of image.ini, crop and border). A filter whose result size is not known (resize, a
 * filter of an extension's handler) stops it: the crop filters after it keep their own offsets.
 *
 * An alias made from another alias (Reference=) gets the crop rectangle through that alias; its focal
 * point is followed through the reference's filters, see eZImageManager::convert().
 *
 * The same calculation is done by the page editors that show live previews; plan() returns the part of
 * the original each alias shows, for them and for tests.
 *
 * Images without a focal point or crop rectangle are converted exactly as before.
 */
class expImageFocalPoint
{
    /** The smallest crop rectangle, as a fraction of the original's width or height */
    const MIN_CROP = 0.01;

    /** Filters that leave the size of the image as it is */
    protected static $sizeKeepingFilters = array(
        'colorspace', 'colorspace/gray', 'colorspace/transparent', 'luminance/gray', 'luminance/sepia',
        'color/monochrome', 'filter/noise', 'filter/swirl', 'border/color', 'border/width', 'flatten',
    );

    /**
     * A focal point, or null for none. Values outside 0 to 1, or that are not numbers, give null.
     *
     * @param mixed $x
     * @param mixed $y
     * @return array|null array( 'x' => float, 'y' => float )
     */
    public static function normalizePoint( $x, $y )
    {
        $x = self::fraction( $x );
        $y = self::fraction( $y );
        if ( $x === null || $y === null )
            return null;
        return array( 'x' => $x, 'y' => $y );
    }

    /**
     * A crop rectangle, or null for none. A rectangle outside the image, smaller than MIN_CROP or that is
     * the whole image gives null.
     *
     * @param mixed $crop array with x, y, width and height (fractions)
     * @return array|null
     */
    public static function normalizeCrop( $crop )
    {
        if ( !is_array( $crop ) )
            return null;
        foreach ( array( 'x', 'y', 'width', 'height' ) as $key )
        {
            if ( !isset( $crop[$key] ) )
                return null;
            $crop[$key] = self::fraction( $crop[$key] );
            if ( $crop[$key] === null )
                return null;
        }
        if ( $crop['width'] < self::MIN_CROP || $crop['height'] < self::MIN_CROP )
            return null;
        if ( $crop['x'] + $crop['width'] > 1.0000005 || $crop['y'] + $crop['height'] > 1.0000005 )
            return null;
        $crop['width'] = min( $crop['width'], 1 - $crop['x'] );
        $crop['height'] = min( $crop['height'], 1 - $crop['y'] );
        if ( $crop['x'] == 0 && $crop['y'] == 0 && $crop['width'] >= 0.9999995 && $crop['height'] >= 0.9999995 )
            return null;
        return array( 'x' => $crop['x'], 'y' => $crop['y'], 'width' => $crop['width'], 'height' => $crop['height'] );
    }

    /**
     * A number from 0 to 1 (six decimals), or null.
     *
     * @param mixed $value
     * @return float|null
     */
    protected static function fraction( $value )
    {
        if ( is_string( $value ) )
            $value = trim( $value );
        if ( !is_int( $value ) && !is_float( $value ) && !( is_string( $value ) && is_numeric( $value ) ) )
            return null;
        $value = (float)$value;
        if ( is_nan( $value ) || $value < 0 || $value > 1 )
            return null;
        return round( $value, 6 );
    }

    /**
     * The focal point and crop rectangle stored in the <ezimage> element $imageNode.
     *
     * @param DOMElement $imageNode
     * @return array array( 'focal' => array|null, 'crop' => array|null )
     */
    public static function readFromXML( $imageNode )
    {
        $data = array( 'focal' => null, 'crop' => null );
        if ( !$imageNode instanceof DOMElement )
            return $data;
        foreach ( $imageNode->childNodes as $child )
        {
            if ( !$child instanceof DOMElement )
                continue;
            if ( $child->tagName === 'focal' && $data['focal'] === null )
                $data['focal'] = self::normalizePoint( $child->getAttribute( 'x' ), $child->getAttribute( 'y' ) );
            else if ( $child->tagName === 'crop' && $data['crop'] === null )
                $data['crop'] = self::normalizeCrop( array( 'x' => $child->getAttribute( 'x' ), 'y' => $child->getAttribute( 'y' ),
                                                            'width' => $child->getAttribute( 'width' ), 'height' => $child->getAttribute( 'height' ) ) );
        }
        return $data;
    }

    /**
     * The focal point and crop rectangle in the image XML $xml (data_text of an image attribute).
     *
     * @param string $xml
     * @return array array( 'focal' => array|null, 'crop' => array|null )
     */
    public static function readFromXMLString( $xml )
    {
        if ( !is_string( $xml ) || strpos( $xml, '<focal' ) === false && strpos( $xml, '<crop' ) === false )
            return array( 'focal' => null, 'crop' => null );
        $doc = new DOMDocument( '1.0', 'utf-8' );
        $useErrors = libxml_use_internal_errors( true );
        $loaded = $doc->loadXML( $xml );
        libxml_clear_errors();
        libxml_use_internal_errors( $useErrors );
        if ( !$loaded || !$doc->documentElement || $doc->documentElement->tagName !== 'ezimage' )
            return array( 'focal' => null, 'crop' => null );
        return self::readFromXML( $doc->documentElement );
    }

    /**
     * Writes $focal and $crop into the <ezimage> element $imageNode, replacing what it held (null removes it).
     *
     * @param DOMElement $imageNode
     * @param array|null $focal
     * @param array|null $crop
     */
    public static function writeToXML( $imageNode, $focal, $crop )
    {
        if ( !$imageNode instanceof DOMElement )
            return;
        foreach ( iterator_to_array( $imageNode->childNodes ) as $child )
        {
            if ( $child instanceof DOMElement && ( $child->tagName === 'focal' || $child->tagName === 'crop' ) )
                $imageNode->removeChild( $child );
        }
        $doc = $imageNode->ownerDocument;
        if ( is_array( $focal ) )
        {
            $node = $doc->createElement( 'focal' );
            $node->setAttribute( 'x', self::format( $focal['x'] ) );
            $node->setAttribute( 'y', self::format( $focal['y'] ) );
            $imageNode->appendChild( $node );
        }
        if ( is_array( $crop ) )
        {
            $node = $doc->createElement( 'crop' );
            foreach ( array( 'x', 'y', 'width', 'height' ) as $key )
                $node->setAttribute( $key, self::format( $crop[$key] ) );
            $imageNode->appendChild( $node );
        }
    }

    /**
     * @param float $value
     * @return string
     */
    protected static function format( $value )
    {
        $text = rtrim( rtrim( sprintf( '%.6F', $value ), '0' ), '.' );
        return $text === '' ? '0' : $text;
    }

    /**
     * The focal point as CSS object-position / background-position ("25% 40%"), "50% 50%" without one.
     *
     * @param array|null $focal
     * @return string
     */
    public static function cssPosition( $focal )
    {
        if ( !is_array( $focal ) )
            return '50% 50%';
        return self::format( round( $focal['x'] * 100, 4 ) ) . '% ' . self::format( round( $focal['y'] * 100, 4 ) ) . '%';
    }

    /**
     * The geometry/crop filter that cuts $crop out of an image of $width x $height pixels.
     *
     * @param array $crop
     * @param int $width
     * @param int $height
     * @return array a filter as eZImageManager holds it
     */
    public static function cropFilter( $crop, $width, $height )
    {
        $x = (int)round( $crop['x'] * $width );
        $y = (int)round( $crop['y'] * $height );
        $w = max( 1, min( (int)round( $crop['width'] * $width ), $width - $x ) );
        $h = max( 1, min( (int)round( $crop['height'] * $height ), $height - $y ) );
        return array( 'name' => 'geometry/crop', 'data' => array( $w, $h, $x, $y ) );
    }

    /**
     * The filters of an alias, with its crop filters centered on the focal point, for a source image of
     * $width x $height pixels.
     *
     * @param array $filters the alias's filters (eZImageManager's structure: name, data)
     * @param int $width size of the source image
     * @param int $height
     * @param array|null $focal focal point in the source image (fractions)
     * @param array|null $crop crop rectangle of the source image (fractions), cut out first; null when the
     *                         source already is cut (an alias made from another alias)
     * @return array filters => the filters to run, width/height => the result size (false when not known),
     *               focal => the focal point in the result (fractions, false when not known),
     *               source => the part of the source the result shows (x, y, width, height in source
     *               pixels, false when not known)
     */
    public static function plan( array $filters, $width, $height, $focal, $crop = null )
    {
        $width = (int)$width;
        $height = (int)$height;
        $result = array( 'filters' => $filters, 'width' => false, 'height' => false, 'focal' => false, 'source' => false );
        if ( $width <= 0 || $height <= 0 )
            return $result;

        // The state: current size, focal point in current pixels, and the source window it shows
        $w = (float)$width;
        $h = (float)$height;
        $sx = 0.0;
        $sy = 0.0;
        $sw = (float)$width;
        $sh = (float)$height;
        $out = array();
        if ( is_array( $crop ) )
        {
            $filter = self::cropFilter( $crop, $width, $height );
            $out[] = $filter;
            list( $cw, $ch, $cx, $cy ) = $filter['data'];
            $sx = $cx;
            $sy = $cy;
            $sw = $cw;
            $sh = $ch;
            $w = $cw;
            $h = $ch;
        }
        // Without a focal point the crop filters keep their own offsets (in the cut image)
        $hasFocal = is_array( $focal );
        if ( $hasFocal )
        {
            $fx = $focal['x'] * $width - $sx;
            $fy = $focal['y'] * $height - $sy;
            $fx = max( 0.0, min( $w, $fx ) );
            $fy = max( 0.0, min( $h, $fy ) );
        }
        else
        {
            $fx = $w / 2;
            $fy = $h / 2;
        }

        $known = true;
        foreach ( $filters as $filter )
        {
            $name = isset( $filter['name'] ) ? $filter['name'] : '';
            $data = isset( $filter['data'] ) && is_array( $filter['data'] ) ? array_values( $filter['data'] ) : array();
            if ( !$known )
            {
                $out[] = $filter;
                continue;
            }
            $scale = self::scaleFactors( $name, $data, $w, $h );
            if ( $scale !== null )
            {
                list( $nw, $nh ) = $scale;
                $fx *= $nw / $w;
                $fy *= $nh / $h;
                $w = $nw;
                $h = $nh;
                $out[] = $filter;
                continue;
            }
            if ( $name === 'geometry/crop' && count( $data ) >= 2 && is_numeric( $data[0] ) && is_numeric( $data[1] ) )
            {
                $cw = (int)$data[0];
                $ch = (int)$data[1];
                $iw = (int)round( $w );
                $ih = (int)round( $h );
                $x = $hasFocal ? (int)round( $fx - $cw / 2 ) : ( isset( $data[2] ) && is_numeric( $data[2] ) ? (int)$data[2] : 0 );
                $y = $hasFocal ? (int)round( $fy - $ch / 2 ) : ( isset( $data[3] ) && is_numeric( $data[3] ) ? (int)$data[3] : 0 );
                if ( $hasFocal )
                {
                    $x = max( 0, min( $x, $iw - $cw ) );
                    $y = max( 0, min( $y, $ih - $ch ) );
                    $data[2] = $x;
                    $data[3] = $y;
                }
                else
                {
                    $x = max( 0, min( $x, $iw ) );
                    $y = max( 0, min( $y, $ih ) );
                }
                $filter['data'] = $data;
                $out[] = $filter;
                $nw = max( 0, min( $cw, $iw - $x ) );
                $nh = max( 0, min( $ch, $ih - $y ) );
                // The window in source pixels
                $sx += $x * $sw / $w;
                $sy += $y * $sh / $h;
                $sw = $nw * $sw / $w;
                $sh = $nh * $sh / $h;
                $fx -= $x;
                $fy -= $y;
                $w = (float)$nw;
                $h = (float)$nh;
                if ( $w <= 0 || $h <= 0 )
                    $known = false;
                continue;
            }
            if ( $name === 'border' && count( $data ) >= 2 && is_numeric( $data[0] ) && is_numeric( $data[1] ) )
            {
                // The border is drawn around the image: the source window is the same, the focal point moves with it
                $fx += (int)$data[0];
                $fy += (int)$data[1];
                $w += 2 * (int)$data[0];
                $h += 2 * (int)$data[1];
                $out[] = $filter;
                continue;
            }
            if ( !in_array( $name, self::$sizeKeepingFilters, true ) )
                $known = false;
            $out[] = $filter;
        }

        $result['filters'] = $out;
        if ( $known )
        {
            $result['width'] = (int)round( $w );
            $result['height'] = (int)round( $h );
            $result['focal'] = array( 'x' => $w > 0 ? round( max( 0.0, min( 1.0, $fx / $w ) ), 6 ) : 0.5,
                                      'y' => $h > 0 ? round( max( 0.0, min( 1.0, $fy / $h ) ), 6 ) : 0.5 );
            $result['source'] = array( 'x' => round( $sx, 2 ), 'y' => round( $sy, 2 ),
                                       'width' => round( $sw, 2 ), 'height' => round( $sh, 2 ) );
        }
        return $result;
    }

    /**
     * The size after the scale filter $name, or null when $name is not a scale filter (or its data is not
     * usable). As eZImageGDHandler and the ImageMagick geometry options compute it.
     *
     * @param string $name
     * @param array $data
     * @param float $w
     * @param float $h
     * @return array|null array( width, height )
     */
    protected static function scaleFactors( $name, array $data, $w, $h )
    {
        $a = isset( $data[0] ) && is_numeric( $data[0] ) ? (float)$data[0] : null;
        $b = isset( $data[1] ) && is_numeric( $data[1] ) ? (float)$data[1] : null;
        switch ( $name )
        {
            case 'geometry/scale':
            case 'geometry/scaledownonly':
                if ( !$a || !$b )
                    return null;
                $ratio = min( $a / $w, $b / $h );
                if ( $name === 'geometry/scaledownonly' )
                    $ratio = min( 1.0, $ratio );
                break;
            case 'geometry/scalewidth':
            case 'geometry/scalewidthdownonly':
                if ( !$a )
                    return null;
                $ratio = $a / $w;
                if ( $name === 'geometry/scalewidthdownonly' )
                    $ratio = min( 1.0, $ratio );
                break;
            case 'geometry/scaleheight':
            case 'geometry/scaleheightdownonly':
                if ( !$a )
                    return null;
                $ratio = $a / $h;
                if ( $name === 'geometry/scaleheightdownonly' )
                    $ratio = min( 1.0, $ratio );
                break;
            case 'geometry/scaleexact':
                if ( !$a || !$b )
                    return null;
                return array( $a, $b );
            case 'geometry/scalepercent':
                if ( !$a )
                    return null;
                return array( max( 1.0, round( $w * $a / 100 ) ), max( 1.0, round( $h * ( $b ?: $a ) / 100 ) ) );
            default:
                return null;
        }
        return array( max( 1.0, round( $w * $ratio ) ), max( 1.0, round( $h * $ratio ) ) );
    }

    /**
     * The aliases an alias is made from, the furthest first, without 'original' ($aliasName itself is not
     * in the list). A missing or circular reference ends the list.
     *
     * @param array $aliasList eZImageManager::aliasList()
     * @param string $aliasName
     * @return string[]
     */
    public static function referenceChain( array $aliasList, $aliasName )
    {
        $chain = array();
        $name = $aliasName;
        while ( isset( $aliasList[$name] ) && !empty( $aliasList[$name]['reference'] ) )
        {
            $name = $aliasList[$name]['reference'];
            if ( $name === 'original' || !isset( $aliasList[$name] ) || $name === $aliasName || in_array( $name, $chain, true ) )
                break;
            $chain[] = $name;
        }
        return array_reverse( $chain );
    }

    /**
     * Every filter applied from the original to the alias $aliasName, through the aliases it is made
     * from, in order.
     *
     * @param array $aliasList eZImageManager::aliasList()
     * @param string $aliasName
     * @return array
     */
    public static function filterChain( array $aliasList, $aliasName )
    {
        if ( !isset( $aliasList[$aliasName] ) )
            return array();
        $filters = array();
        foreach ( self::referenceChain( $aliasList, $aliasName ) as $name )
            $filters = array_merge( $filters, (array)$aliasList[$name]['filters'] );
        return array_merge( $filters, (array)$aliasList[$aliasName]['filters'] );
    }

    /**
     * The focal point (fractions) in the image an alias is made from: in the original for an alias made
     * from the original, otherwise followed through the filters of the aliases it is made from.
     *
     * @param array $aliasList eZImageManager::aliasList()
     * @param string $aliasName
     * @param array|null $focal focal point in the original
     * @param array|null $crop crop rectangle of the original
     * @param int $width size of the original
     * @param int $height
     * @return array|null|false the focal point; null without one; false when it cannot be followed
     */
    public static function focalInSource( array $aliasList, $aliasName, $focal, $crop, $width, $height )
    {
        if ( !is_array( $focal ) )
            return null;
        $chain = self::referenceChain( $aliasList, $aliasName );
        if ( !$chain )
            return $focal;
        $filters = array();
        foreach ( $chain as $name )
            $filters = array_merge( $filters, (array)$aliasList[$name]['filters'] );
        $plan = self::plan( $filters, $width, $height, $focal, $crop );
        return $plan['focal'];
    }
}

?>
