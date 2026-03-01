<?php

class xrowSitemapItemNews extends xrowSitemapItem
{
    public $publication = array(); // array
    public $access;
    public $genres = array(); // Array
    public $publication_date; // YYYY-MM-DDThh:mm:ssTZD (e.g., 1997-07-16T19:20:30+01:00)
    public $title; // text
    public $keywords = array(); // array
    public $stock_tickers = array(); // Array


    function __construct()
    {
        if ( ! eZINI::instance( 'xrowsitemap.ini' )->hasVariable( 'NewsSitemapSettings', 'Name' ) )
        {
            throw new Exception( "Publication Name is required for news sitemap" );
        }
        $this->publication = array(
            'name' => eZINI::instance( 'xrowsitemap.ini' )->variable( 'NewsSitemapSettings', 'Name' ) ,
            'language' => xrowSitemapTools::language()
        );
        if ( ! eZINI::instance( 'xrowsitemap.ini' )->hasVariable( 'NewsSitemapSettings', 'UseGenres' ) || ( eZINI::instance( 'xrowsitemap.ini' )->hasVariable( 'NewsSitemapSettings', 'UseGenres' ) && eZINI::instance( 'xrowsitemap.ini' )->variable( 'NewsSitemapSettings', 'UseGenres' ) != 'disable' ) )
        {
            $this->genres = array(
                'PressRelease'
            );
        }
    }

    function DOMElement( xrowSitemapList $sitemap )
    {
        $sitemap->root->setAttribute( "xmlns:news", "http://www.google.com/schemas/sitemap-news/0.9" );
        $news = $sitemap->dom->createElement( 'news:news' );

        $publication = $sitemap->dom->createElement( 'news:publication' );

        $pname = $sitemap->dom->createElement( 'news:name' );
        $cdata_pname = $sitemap->dom->createCDATASection( $this->publication['name'] );
        $pname->appendChild( $cdata_pname );
        $publication->appendChild( $pname );

        $plang = $sitemap->dom->createElement( 'news:language', $this->publication['language'] );
        $publication->appendChild( $plang );

        $news->appendChild( $publication );
        $publication_date = $sitemap->dom->createElement( 'news:publication_date', $this->publication_date->format( DateTime::W3C ) );
        $news->appendChild( $publication_date );

        $title = $sitemap->dom->createElement( 'news:title' );
        $cdata_title = $sitemap->dom->createCDATASection( $this->title );
        $title->appendChild( $cdata_title );

        $news->appendChild( $title );
        if ( $this->access )
        {
            $access = $sitemap->dom->createElement( 'news:access' );
            $access->appendChild( $sitemap->dom->createTextNode( $this->access ) );
            $news->appendChild( $access );
        }
        if ( $this->genres )
        {
            $genres = $sitemap->dom->createElement( 'news:genres' );
            $genres->appendChild( $sitemap->dom->createTextNode( implode( ',', $this->genres ) ) );
            $news->appendChild( $genres );
        }
        if ( count( $this->keywords ) > 0 )
        {
            $keywords = $sitemap->dom->createElement( 'news:keywords' );
            $keywords->appendChild( $sitemap->dom->createTextNode( join( ",", $this->keywords ) ) );
            $news->appendChild( $keywords );
        }
        return $news;
    }

    /**
     * @return xrowMetaData
     */
    static public function __set_state( array $array )
    {
        return new xrowSitemapItemNews( $array['publication'], $array['access'], $array['genres'], $array['publication_date'], $array['title'], $array['stock_tickers'] );
    }
}
