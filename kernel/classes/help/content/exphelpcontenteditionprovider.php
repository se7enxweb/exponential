<?php
/**
 * What goes into a stored edition of a manual (expHelpContentEbooks): every Released object, whoever builds the book,
 * and for a public manual only what is public. A stored e-book is read by everyone its section and state let read it,
 * so its content must not depend on the rights of the person whose release started the build, and a public book must
 * never carry an internal topic, chapter or figure.
 *
 * Public means: a topic in the section help_public whose audience is public (both, so a section that has not been set
 * from the audience yet keeps a topic out), a chapter or figure in the section help_public.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentEditionProvider extends expHelpContentProvider
{
    /** @var bool the public edition */
    protected $public;

    /**
     * @param expHelpContentRepository $repository
     * @param bool $public the edition of a public manual: public objects only
     */
    public function __construct( expHelpContentRepository $repository, $public )
    {
        parent::__construct( $repository );
        $this->public = (bool)$public;
    }

    /** @return bool */
    public function isPublic()
    {
        return $this->public;
    }

    /**
     * Released (and public in a public edition), else null; never a preview
     *
     * @param array $item
     * @return array|null
     */
    public function visible( array $item )
    {
        if ( !isset( $item['state'] ) || $item['state'] !== expHelpContentSchema::STATE_RELEASED )
            return null;
        if ( $this->public && !self::isPublicItem( $item ) )
            return null;
        $item['preview'] = false;
        return $item;
    }

    /**
     * Whether an object belongs in a public edition
     *
     * @param array $item
     * @return bool
     */
    public static function isPublicItem( array $item )
    {
        if ( !isset( $item['section'] ) || $item['section'] !== expHelpContentSchema::SECTION_PUBLIC )
            return false;
        if ( isset( $item['class'] ) && isset( expHelpContentSchema::TOPIC_TYPES[$item['class']] ) )
            return expHelpContentSchema::isPublicAudience( isset( $item['audience'] ) ? (array)$item['audience'] : array() );
        return true;
    }

    /**
     * The related topics and figures that belong in the edition
     *
     * @param array $topic
     * @return array array( 'related' => list, 'figures' => list )
     */
    public function relations( array $topic )
    {
        $related = array();
        foreach ( $topic['related'] as $objectId )
        {
            $found = $this->repository()->byObjectId( $objectId );
            if ( $found !== null && $found['kind'] === 'topics' && ( $item = $this->visible( $found['item'] ) ) !== null )
                $related[] = $item;
        }
        $figures = array();
        foreach ( $topic['figures'] as $objectId )
        {
            $found = $this->repository()->byObjectId( $objectId );
            if ( $found !== null && $found['kind'] === 'figures' && $found['item']['image_url'] !== '' && $this->visible( $found['item'] ) !== null )
                $figures[] = $found['item'];
        }
        return array( 'related' => $related, 'figures' => $figures );
    }
}

?>
