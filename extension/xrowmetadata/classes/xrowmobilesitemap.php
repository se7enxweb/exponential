<?php

class xrowMobileSitemap extends xrowSitemapList
{
    const BASENAME = 'urlset';
    const SUFFIX = 'xml';
    const ITEMNAME = 'url';

    function __construct()
    {
        // Create the DOMnode
        $this->dom = new DOMDocument( "1.0", "UTF-8" );
        $this->dom->formatOutput = true;
        // Create DOM-Root (urlset)
        $this->root = $this->dom->createElement( constant( get_class( $this ) . '::BASENAME' ) );
        $this->root->setAttribute( "xmlns", "http://www.sitemaps.org/schemas/sitemap/0.9" );
        $this->root->setAttribute( "xmlns:mobile", "http://www.google.com/schemas/sitemap-mobile/1.0" );
        $this->dom->appendChild( $this->root );
    }

    /**
     * Add a new child to the mobile sitemap
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

        // mobile stuff
        $m = $this->dom->createElement( 'mobile:mobile' );
        $node->appendChild( $m );

        // append to root node
        $this->root->appendChild( $node );
    }
}
