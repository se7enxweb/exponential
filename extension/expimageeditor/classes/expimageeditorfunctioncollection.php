<?php
/**
 * File containing the expImageEditorFunctionCollection class
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expimageeditor
 */

/**
 * The fetch functions of the expimageeditor module.
 *
 * @package expimageeditor
 */
class expImageEditorFunctionCollection
{
    /**
     * fetch( 'expimageeditor', 'aspect_ratios' ): the ratios the crop tool offers besides a free selection, as a list of
     * array( 'label' => ..., 'ratio' => width / height ): first those of the image aliases that give an image a fixed
     * shape (image.ini, an alias of AliasList[] with a Filters[] entry geometry/scaleexact=<w>;<h> or
     * geometry/crop=<w>;<h>;...), then expimageeditor.ini [EditorSettings] AspectRatios[] (<w>:<h>). The same ratio
     * appears once, under its first label.
     *
     * @return array( 'result' => array )
     */
    public function fetchAspectRatios()
    {
        return array( 'result' => self::aspectRatios( eZINI::instance( 'image.ini' ), eZINI::instance( 'expimageeditor.ini' ) ) );
    }

    /**
     * @param eZINI $imageIni
     * @param eZINI $editorIni
     * @return array
     */
    public static function aspectRatios( $imageIni, $editorIni )
    {
        $ratios = array();
        $add = function ( $label, $width, $height ) use ( &$ratios ) {
            $width = (int)$width;
            $height = (int)$height;
            if ( $width <= 0 || $height <= 0 )
                return;
            $key = sprintf( '%.4f', $width / $height );
            if ( !isset( $ratios[$key] ) )
                $ratios[$key] = array( 'label' => $label, 'ratio' => round( $width / $height, 6 ) );
        };

        $aliases = $imageIni->hasVariable( 'AliasSettings', 'AliasList' ) ? (array)$imageIni->variable( 'AliasSettings', 'AliasList' ) : array();
        foreach ( $aliases as $alias )
        {
            if ( !$imageIni->hasVariable( $alias, 'Filters' ) )
                continue;
            foreach ( (array)$imageIni->variable( $alias, 'Filters' ) as $filter )
            {
                if ( preg_match( '#^geometry/(?:scaleexact|crop)=(\d+);(\d+)#', trim( (string)$filter ), $m ) )
                {
                    $add( $alias . ' (' . $m[1] . ':' . $m[2] . ')', $m[1], $m[2] );
                    break;
                }
            }
        }

        $own = $editorIni->hasVariable( 'EditorSettings', 'AspectRatios' ) ? (array)$editorIni->variable( 'EditorSettings', 'AspectRatios' ) : array();
        foreach ( $own as $entry )
        {
            if ( preg_match( '/^\s*(\d+)\s*:\s*(\d+)\s*$/', (string)$entry, $m ) )
                $add( $m[1] . ':' . $m[2], $m[1], $m[2] );
        }
        return array_values( $ratios );
    }
}
