<?php

class xrowSitemapItemAlternateLink extends xrowSitemapItem
{
    const DEFAULT_MOBILE_MAX_WIDTH = 480;

    public $href; // text

    function __construct( $href = false )
    {
        if ( $href )
        {
            $this->href = $href;
        }

    }

    function DOMElement( xrowSitemapList $sitemap )
    {
        $alter = $sitemap->dom->createElement( 'xhtml:link' );
        $alter->setAttribute( 'rel', 'alternate' );
        $xrowSitemapIni = eZINI::instance( 'xrowsitemap.ini' );
        $mobileMaxWidth = $xrowSitemapIni->hasVariable( 'SitemapSettings', 'MobileMaxWidth' ) ? $xrowSitemapIni->variable( 'SitemapSettings', 'MobileMaxWidth' ) : self::DEFAULT_MOBILE_MAX_WIDTH;
        $alter->setAttribute( 'media', 'only screen and (max-width: ' . $mobileMaxWidth . 'px)' );
        $alter->setAttribute( 'href', $this->href );
        return $alter;
    }

    /**
     * @return xrowSitemapItemAlternateLink
     */
    static public function __set_state( array $array )
    {
        return new xrowSitemapItemAlternateLink( $array['href'] );
    }
}
