<?php

class xrowSitemapItemModified extends xrowSitemapItem
{

    public $date; // DateTime,  YYYY-MM-DDThh:mm:ssTZD (e.g., 1997-07-16T19:20:30+01:00)


    function __construct( $date )
    {
        if ( $date instanceof DateTime )
        {
            $this->date = $date;
        }
        elseif ( is_numeric( $date ) )
        {
            $this->date = new DateTime( '@' . $date );
        }
    }

    function DOMElement( xrowSitemapList $sitemap )
    {
        return $sitemap->dom->createElement( 'lastmod', $this->date->format( DateTime::W3C ) );
    }

    /**
     * @return xrowSitemapItemModified
     */
    static public function __set_state( array $array )
    {
        return new xrowSitemapItemModified( $array['date'] );
    }
}
