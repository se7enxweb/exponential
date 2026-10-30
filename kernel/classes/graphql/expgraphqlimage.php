<?php
/**
 * The value of an image field (the type Image): the image in an alias of image.ini, made by the kernel's image
 * system when it is first asked for, as on the site.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLImage
{
    /** @var eZContentObjectAttribute */
    protected $attribute;

    /** @var expGraphQLContext */
    protected $context;

    /** @var array alias => array|null, the aliases read for this value */
    protected $aliases = array();

    /**
     * @param eZContentObjectAttribute $attribute
     * @param expGraphQLContext $context
     */
    public function __construct( eZContentObjectAttribute $attribute, expGraphQLContext $context )
    {
        $this->attribute = $attribute;
        $this->context = $context;
    }

    /**
     * @param string $alias
     * @return array|null url, width, height, mimeType of the image in the alias; null when it cannot be made
     */
    public function alias( $alias )
    {
        $alias = (string)$alias;
        if ( !array_key_exists( $alias, $this->aliases ) )
        {
            if ( !eZImageManager::factory()->hasAlias( $alias ) )
                throw new expGraphQLError( "There is no image alias $alias (image.ini [AliasSettings] AliasList[]).", expGraphQLError::BAD_USER_INPUT );
            $handler = $this->attribute->content();
            $data = is_object( $handler ) ? $handler->imageAlias( $alias ) : null;
            $this->aliases[$alias] = is_array( $data ) && !empty( $data['url'] )
                ? array( 'alias' => $alias,
                         'url' => $this->context->absoluteUrl( eZSys::wwwDir() . '/' . ltrim( (string)$data['url'], '/' ) ),
                         'width' => isset( $data['width'] ) ? (int)$data['width'] : null,
                         'height' => isset( $data['height'] ) ? (int)$data['height'] : null,
                         'mimeType' => isset( $data['mime_type'] ) ? (string)$data['mime_type'] : null )
                : null;
        }
        return $this->aliases[$alias];
    }

    /**
     * @param string $alias
     * @param string $key url, width, height or mimeType
     * @return mixed
     */
    public function value( $alias, $key )
    {
        $data = $this->alias( $alias );
        return $data === null ? null : $data[$key];
    }

    /** @return string|null */
    public function alternativeText()
    {
        $handler = $this->attribute->content();
        return is_object( $handler ) ? (string)$handler->attribute( 'alternative_text' ) : null;
    }

    /**
     * @param string[] $aliases
     * @return array[] the image in each alias that can be made
     */
    public function variations( array $aliases )
    {
        $variations = array();
        foreach ( array_unique( $aliases ) as $alias )
        {
            $data = $this->alias( $alias );
            if ( $data !== null )
                $variations[] = $data;
        }
        return $variations;
    }
}

?>
