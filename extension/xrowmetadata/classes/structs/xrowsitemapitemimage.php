<?php

class xrowSitemapItemImage extends xrowSitemapItem
{
    public $url = array(); // array
    public $caption;
    public $geo_location = array(); // Array
    public $title; // text
    public $license; // text


    function DOMElement( xrowSitemapList $sitemap )
    {
        $image = $sitemap->dom->createElement( 'image:image' );

        $loc = $sitemap->dom->createElement( 'image:loc' );
        $loc->appendChild( $sitemap->dom->createTextNode( $this->url ) );
        $image->appendChild( $loc );

        if ( isset( $this->caption ) )
        {
            $caption = $sitemap->dom->createElement( 'image:caption' );
            $caption->appendChild( $sitemap->dom->createTextNode( htmlspecialchars( $this->caption, ENT_QUOTES, 'UTF-8' ) ) );
            $image->appendChild( $caption );
        }
        if ( isset( $this->title ) )
        {
            $title = $sitemap->dom->createElement( 'image:title' );
            $title->appendChild( $sitemap->dom->createTextNode( htmlspecialchars( $this->title, ENT_QUOTES, 'UTF-8' ) ) );
            $image->appendChild( $title );
        }
        if ( isset( $this->license ) )
        {
            $license = $sitemap->dom->createElement( 'image:license' );
            $license->appendChild( $sitemap->dom->createTextNode( htmlspecialchars( $this->license, ENT_QUOTES, 'UTF-8' ) ) );
            $image->appendChild( $license );
        }
        return $image;
    }

    /**
     * @return xrowMetaData
     */
    static public function __set_state( array $array )
    {
        return new xrowSitemapItemImage( $array['url'], $array['caption'], $array['geo_location'], $array['title'], $array['license'] );
    }
}
