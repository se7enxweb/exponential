<?php
/**
 * File containing the expImagePlaceholder class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The image answered while an image is missing (an alias that is not made yet, a file a staging site has not fetched):
 * an SVG of the exact size in a soft neutral tone, light or dark as the browser prefers, with no text and no logo, so
 * it fits any design and the page keeps its layout. Applies to any missing image, not only aliases.
 *
 * @since 6.0.15
 * @package kernel
 */
class expImagePlaceholder
{
    /** Size used when none is known */
    const DEFAULT_WIDTH = 300;
    const DEFAULT_HEIGHT = 200;

    /** Largest side drawn (a forged token cannot ask for a huge image) */
    const MAX_SIDE = 8000;

    /**
     * The placeholder SVG for an image of $width x $height pixels.
     *
     * @param int|false|null $width
     * @param int|false|null $height
     * @return string
     */
    public static function svg( $width = null, $height = null )
    {
        list( $w, $h ) = self::size( $width, $height );
        // A soft gradient and a faint frame in light mode; the same, darker, in dark mode. Colors only, no text.
        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $w . '" height="' . $h . '" viewBox="0 0 ' . $w . ' ' . $h . '"'
             . ' preserveAspectRatio="none" role="img" aria-hidden="true">'
             . '<style>'
             . '.a{stop-color:#eceef1}.b{stop-color:#e2e5e9}.f{fill:none;stroke:#d5d9de}'
             . '@media (prefers-color-scheme:dark){.a{stop-color:#2a2d31}.b{stop-color:#24272b}.f{stroke:#34383d}}'
             . '</style>'
             . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
             . '<stop offset="0" class="a"/><stop offset="1" class="b"/>'
             . '</linearGradient></defs>'
             . '<rect width="100%" height="100%" fill="url(#g)"/>'
             . '<rect class="f" x="0.5" y="0.5" width="' . max( 0, $w - 1 ) . '" height="' . max( 0, $h - 1 ) . '"/>'
             . '</svg>';
    }

    /**
     * The size drawn: whole positive numbers no larger than MAX_SIDE; a missing side follows the other one's
     * default aspect ratio.
     *
     * @param mixed $width
     * @param mixed $height
     * @return int[] array( width, height )
     */
    public static function size( $width, $height )
    {
        $w = is_numeric( $width ) ? (int)round( (float)$width ) : 0;
        $h = is_numeric( $height ) ? (int)round( (float)$height ) : 0;
        if ( $w <= 0 && $h <= 0 )
            return array( self::DEFAULT_WIDTH, self::DEFAULT_HEIGHT );
        if ( $w <= 0 )
            $w = (int)round( $h * self::DEFAULT_WIDTH / self::DEFAULT_HEIGHT );
        if ( $h <= 0 )
            $h = (int)round( $w * self::DEFAULT_HEIGHT / self::DEFAULT_WIDTH );
        return array( max( 1, min( self::MAX_SIDE, $w ) ), max( 1, min( self::MAX_SIDE, $h ) ) );
    }

    /**
     * The response headers of a placeholder: never cached, marked for the swap script.
     *
     * @param string $body the SVG
     * @param bool $swap mark it for the swap script (Server-Timing)
     * @return array name => value
     */
    public static function headers( $body, $swap = true )
    {
        $headers = array(
            'Content-Type' => 'image/svg+xml; charset=utf-8',
            'Content-Length' => (string)strlen( $body ),
            'Cache-Control' => 'no-store, max-age=0',
            'X-Image-Placeholder' => '1',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline'",
        );
        if ( $swap )
            $headers['Server-Timing'] = 'exp-image-placeholder';
        return $headers;
    }
}
