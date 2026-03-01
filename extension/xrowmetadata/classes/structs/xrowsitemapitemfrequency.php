<?php

class xrowSitemapItemFrequency extends xrowSitemapItem
{

    public $frequency; // text


    function __construct( $frequency = false )
    {
        if ( $frequency )
        {
            $this->frequency = $frequency;
        }

    }

    function DOMElement( xrowSitemapList $sitemap )
    {
        $changefreq = $sitemap->dom->createElement( 'changefreq' );
        $changefreq->appendChild( $sitemap->dom->createTextNode( $this->frequency ) );
        return $changefreq;
    }

    /**
     * @return xrowSitemapItemModified
     */
    static public function __set_state( array $array )
    {
        return new xrowSitemapItemFrequency( $array['frequency'] );
    }
}
