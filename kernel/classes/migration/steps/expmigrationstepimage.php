<?php
/**
 * File containing the expMigrationStepImage class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "image": puts an image of the migrations folder into an image attribute (ezimage) of an object, sets
 * its alternative text, and its focal point and crop rectangle.
 *
 *   - type: image
 *     object: remote:news-welcome       (or node:)
 *     field: image
 *     file: images/welcome.jpg          next to the migration, below its folder (left out: the image stays)
 *     alt: The team at the opening
 *     focal: { x: 0.3, y: 0.4 }         the part that matters, as fractions of the width and height (null removes it)
 *     crop: { x: 0.1, y: 0, width: 0.8, height: 1 }   cut out before every alias (null removes it)
 *
 * The image is stored through the datatype's fromString() (which checks that it is an image); the focal point and crop
 * rectangle through eZImageAliasHandler::setFocalPoint(), as the image editor of the administration sets them
 * (expImageFocalPoint): the aliases are made again from them. Like the content step, the change is a new version
 * published through the publish operation, or "in_place: true".
 *
 * A step of this type does not undo itself (give the migration a "down:").
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepImage extends expMigrationStepFile
{
    /** The datatypes a step of this type writes. */
    const DATATYPES = array( 'ezimage' );

    /** @return string */
    public function name()
    {
        return 'image';
    }

    /** @return array */
    public function schema()
    {
        return array(
            'description' => 'Puts an image of the migrations folder into an image attribute of an object, with its alternative text, focal point and crop rectangle',
            'keys' => $this->targetKeys() + array(
                'file' => array( 'type' => 'string',
                                 'description' => 'the image: a path below the folder of the migration file (left out: the image the attribute has stays)' ),
                'alt' => array( 'type' => 'string', 'description' => 'the alternative text of the image' ),
                'focal' => array( 'type' => 'any', 'description' => 'the focal point: { x, y } as fractions (0 to 1) of the width and height; null removes it' ),
                'crop' => array( 'type' => 'any', 'description' => 'the crop rectangle: { x, y, width, height } as fractions of the original; null removes it' ),
            ) + $this->commonKeys(),
            'values' => $this->valueTexts(),
            'example' => "- type: image\n  object: remote:news-welcome\n  field: image\n  file: images/welcome.jpg\n  alt: The team at the opening\n"
                       . "  focal: { x: 0.3, y: 0.4 }\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $errors = parent::validate( $step );
        if ( !array_intersect( array( 'file', 'alt', 'focal', 'crop' ), array_keys( $step ) ) )
            $errors[] = 'nothing to change: file:, alt:, focal: or crop: is missing';
        if ( isset( $step['focal'] ) && !expMigrationReferences::contains( $step['focal'] ) )
        {
            $focal = $step['focal'];
            if ( !is_array( $focal ) || array_diff( array_keys( $focal ), array( 'x', 'y' ) ) || !isset( $focal['x'], $focal['y'] )
                 || expImageFocalPoint::normalizePoint( $focal['x'], $focal['y'] ) === null )
                $errors[] = 'focal: { x, y }, each a number from 0 to 1 (null removes the focal point)';
        }
        if ( isset( $step['crop'] ) && !expMigrationReferences::contains( $step['crop'] ) )
        {
            $crop = $step['crop'];
            if ( !is_array( $crop ) || array_diff( array_keys( $crop ), array( 'x', 'y', 'width', 'height' ) ) || expImageFocalPoint::normalizeCrop( $crop ) === null )
                $errors[] = 'crop: { x, y, width, height }, fractions of the original inside it, not the whole image (null removes the crop rectangle)';
        }
        return $errors;
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $parts = array();
        if ( isset( $step['file'] ) )
            $parts[] = 'the image ' . $step['file'];
        if ( isset( $step['alt'] ) )
            $parts[] = 'the alternative text';
        if ( array_key_exists( 'focal', $step ) )
            $parts[] = $step['focal'] === null ? 'no focal point' : 'the focal point';
        if ( array_key_exists( 'crop', $step ) )
            $parts[] = $step['crop'] === null ? 'no crop rectangle' : 'the crop rectangle';
        return array( 'set ' . implode( ', ', $parts ) . ' of ' . $step['field'] . ' of ' . $this->target( $step ) . $this->planFlags( $step ) );
    }

    /**
     * @param eZContentObjectAttribute $attribute
     * @param array $step
     * @param string|null $file
     * @return string[]
     */
    protected function write( eZContentObjectAttribute $attribute, array $step, $file )
    {
        $lines = array();
        $alt = isset( $step['alt'] ) ? (string)$step['alt'] : null;
        if ( $file !== null )
        {
            if ( !$attribute->dataType()->fromString( $attribute, $file . ( $alt !== null ? '|' . $alt : '' ) ) )
                throw new RuntimeException( 'the image ' . $step['file'] . ' was not stored in ' . $step['field'] . ' (is it an image?)' );
            $lines[] = 'put the image ' . $step['file'] . ' into ' . $step['field'];
        }
        else if ( $alt !== null )
        {
            $content = $attribute->content();
            $content->setAttribute( 'alternative_text', $alt );
            $content->store( $attribute );
            $lines[] = 'set the alternative text of ' . $step['field'];
        }
        if ( array_key_exists( 'focal', $step ) || array_key_exists( 'crop', $step ) )
        {
            /** @var eZImageAliasHandler $content */
            $content = $attribute->content();
            if ( !$content instanceof eZImageAliasHandler || !$content->supportsFocalPoint() )
                throw new RuntimeException( 'the attribute ' . $step['field'] . ' has no image that takes a focal point (none, or an SVG)' );
            $focal = array_key_exists( 'focal', $step ) ? $step['focal'] : $content->focalPoint();
            $crop = array_key_exists( 'crop', $step ) ? $step['crop'] : $content->cropRectangle();
            if ( !$content->setFocalPoint( $focal, $crop ) )
                throw new RuntimeException( 'the focal point and crop rectangle of ' . $step['field'] . ' were not set' );
            $content->store( $attribute );
            $lines[] = 'set the focal point ' . ( $focal === null ? '(none)' : $focal['x'] . ', ' . $focal['y'] )
                     . ' and the crop rectangle ' . ( $crop === null ? '(none)' : implode( ', ', array_map( 'strval', array( $crop['x'], $crop['y'], $crop['width'], $crop['height'] ) ) ) )
                     . ' of ' . $step['field'];
        }
        $attribute->store();
        return $lines;
    }
}
