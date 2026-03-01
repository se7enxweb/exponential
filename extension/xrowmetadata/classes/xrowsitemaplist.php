<?php

/*
 * $sitemap = new xrowSitemapList();
 * $image = new xrowSitemapItemImage();
 * $image->url = 'http://www.example.com/test.jpg';
 * $extensions[] = $image;
 * $sitemap->add( $url, $extensions ) );
 * $sitemap->save( $filename );
 */
class xrowSitemapList
{
    public $dom;
    public $root;

    const BASENAME = 'urlset';
    const SUFFIX = 'xml';
    const ITEMNAME = 'url';

    /**
     *
     */
    function __construct()
    {
        // Create the DOMnode
        $this->dom = new DOMDocument( "1.0", "UTF-8" );
        $this->dom->formatOutput = true;
        // Create DOM-Root (urlset)
        $this->root = $this->dom->createElement( constant( get_class( $this ) . '::BASENAME' ) );
        $this->root->setAttribute( "xmlns", "http://www.sitemaps.org/schemas/sitemap/0.9" );
        $this->root->setAttribute( "xmlns:image", "http://www.google.com/schemas/sitemap-image/1.1" );
        $this->root->setAttribute( "xmlns:video", "http://www.google.com/schemas/sitemap-video/1.1" );

        $this->root->setAttribute( "xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance" );
        $this->root->setAttribute( "xsi:schemaLocation", "http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.google.com/schemas/sitemap-image/1.1 http://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd" );

        $ini = eZINI::instance( 'xrowsitemap.ini' );
        if ( $ini->hasVariable( 'SitemapSettings', 'CreateAlternateLink' ) )
        {
            $this->root->setAttribute( "xmlns:xhtml", "http://www.w3.org/1999/xhtml" );
        }

        $this->dom->appendChild( $this->root );
    }

    /**
     * Add a new child to the sitemap
     *
     * @param string $url
     * @param array $extensions Extensions/Modules to the sitemap standard
     * @param int $modified
     * @param string $frequency
     * @param string $priority
     */
    function add( $url, $extensions = array() )
    {
        if ( trim( $url ) == "" )
        {
            return;
        }

        $node = $this->dom->createElement( constant( get_class( $this ) . '::ITEMNAME' ) );
        $subNode = $this->dom->createElement( 'loc' );
        $subNode->appendChild( $this->dom->createTextNode( $url ) );
        $node->appendChild( $subNode );

        if ( is_array( $extensions ) )
        {
            foreach ( $extensions as $extension )
            {
                if ( $extension instanceof xrowSitemapItem )
                {
                    $node->appendChild( $extension->DOMElement( $this ) );
                }
            }
        }

        // append to root node
        $this->root->appendChild( $node );
    }

    /**
     * Saves the xml content
     *
     * @param $filename Path to file
     */
    function save( $filename = 'sitemap.xml' )
    {
        $file = eZClusterFileHandler::instance( $filename );
        if ( $file->exists() )
        {
            eZDebug::writeDebug( "Time: " . date( 'd.m.Y H:i:s' ) . ". Action: " . $filename . " exists. File will be remove." );
            if ( ! $isQuiet )
            {
                $cli = eZCLI::instance();
                $cli->output( "Time: " . date( 'd.m.Y H:i:s' ) . ". Action: " . $filename . " exists. File will be remove." );
                $cli->output( "\n" );
            }
            $file->delete();
        }
        $xml = $this->dom->saveXML();
        return $file->storeContents( $xml, 'sitemap', 'text/xml' );
    }

    function saveLocal( $filename )
    {
        return file_put_contents( $filename, $this->dom->saveXML() );
    }

    /**
     * Gives the xml content
     *
     * @return string XML
     */
    function saveXML()
    {
        return $this->dom->saveXML();
    }
}
