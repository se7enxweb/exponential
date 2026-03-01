<?php

class xrowSitemapItemVideo extends xrowSitemapItem
{
    public $thumbnail_loc;
    public $title;
    public $description;
    public $content_loc;
    public $player_loc;
    public $duration;
    public $expiration_date;
    public $rating;
    public $content_segment_loc;
    public $view_count;
    public $publication_date;
    public $tags = array();
    public $categories = array();
    public $family_friendly;
    public $restriction;
    public $gallery_loc;
    public $price;
    public $requires_subscription;
    public $uploader;
    public $live;

    function __construct()
    {
        $this->live = false;
        $this->requires_subscription = false;
        $this->family_friendly = true;
    }

    function DOMElement( xrowSitemapList $sitemap )
    {
        $video = $sitemap->dom->createElement( 'video:video' );
        if ( $this->thumbnail_loc )
        {
            $thumbnail_locNode = $sitemap->dom->createElement( 'video:thumbnail_loc' );
            $thumbnail_locNode->appendChild( $sitemap->dom->createTextNode( $this->thumbnail_loc ) );
            $video->appendChild( $thumbnail_locNode );
        }
        if ( $this->title )
        {
            $titleNode = $sitemap->dom->createElement( 'video:title' );
            $titleNode->appendChild( $sitemap->dom->createTextNode( htmlspecialchars( $this->title, ENT_QUOTES, 'UTF-8' ) ) );
            $video->appendChild( $titleNode );
        }
        if ( $this->description )
        {
            $descriptionNode = $sitemap->dom->createElement( 'video:description' );
            $descriptionNode->appendChild( $sitemap->dom->createTextNode( htmlspecialchars( $this->description, ENT_QUOTES, 'UTF-8' ) ) );
            $video->appendChild( $descriptionNode );
        }
        if ( $this->content_loc )
        {
            $content_locNode = $sitemap->dom->createElement( 'video:content_loc' );
            $content_locNode->appendChild( $sitemap->dom->createTextNode( $this->content_loc ) );
            $video->appendChild( $content_locNode );
        }
        if ( $this->player_loc )
        {
            $player_locNode = $sitemap->dom->createElement( 'video:player_loc' );
            $player_locNode->appendChild( $sitemap->dom->createTextNode( $this->player_loc ) );
            $video->appendChild( $player_locNode );
        }

        if ( $this->duration )
        {
            $durationNode = $sitemap->dom->createElement( 'video:duration' );
            $durationNode->appendChild( $sitemap->dom->createTextNode( $this->duration ) );
            $video->appendChild( $durationNode );
        }

        if ( $this->tags )
        {
            foreach ( $this->tags as $tag )
            {
                $tagNode = $sitemap->dom->createElement( 'video:tag' );

                $tagNode->appendChild( $sitemap->dom->createTextNode( htmlspecialchars( $tag, ENT_QUOTES, 'UTF-8' ) ) );
                $video->appendChild( $tagNode );
            }
        }
        if ( $this->categories )
        {
            foreach ( $this->categories as $category )
            {
                $categoryNode = $sitemap->dom->createElement( 'video:category' );

                $categoryNode->appendChild( $sitemap->dom->createTextNode( htmlspecialchars( $category, ENT_QUOTES, 'UTF-8' ) ) );
                $video->appendChild( $categoryNode );
            }
        }
        if ( $this->family_friendly === false )
        {
            $family_friendlyNode = $sitemap->dom->createElement( 'video:family_friendly' );
            $family_friendlyNode->appendChild( $sitemap->dom->createTextNode( 'No' ) );
            $video->appendChild( $family_friendlyNode );
        }
        if ( $this->publication_date instanceof DateTime )
        {
            $publication_dateNode = $sitemap->dom->createElement( 'video:publication_date' );
            $publication_dateNode->appendChild( $sitemap->dom->createTextNode( $this->publication_date->format( DateTime::W3C ) ) );
            $video->appendChild( $publication_dateNode );
        }
        if ( $this->view_count )
        {
            $view_countNode = $sitemap->dom->createElement( 'video:view_count' );
            $view_countNode->appendChild( $sitemap->dom->createTextNode( (int) $this->view_count ) );
            $video->appendChild( $view_countNode );
        }
        return $video;
    }

    /**
     * @return xrowMetaData
     */
    static public function __set_state( array $array )
    {
        return new xrowSitemapItemVideo( $array['thumbnail_loc'], $array['title'], $array['description'], $array['content_loc'], $array['duration'], $array['expiration_date'], $array['rating'], $array['content_segment_loc'], $array['view_count'], $array['publication_date'], $array['tags'], $array['categories'], $array['family_friendly'] );
    }
}
