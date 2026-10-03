<?php
/**
 * ezjscore/call/expfeed::<method>: feeds. The RSS exports of the site (list, view, output as the reader gets it,
 * manage), RSS 2.0, Atom 1.0 and JSON Feed 1.1 of any subtree the caller may read, and the RSS imports (list, view,
 * status, check of the source). Output services return the document as text in data.content, with its content
 * type, so a remote app can show or store it unchanged.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expFeedServices extends expServiceBase
{
    public static $services = array(
        'exports' => array( 'summary' => 'The RSS exports with their address, format and number of items', 'access' => 'public', 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int', 'only_active' => 'bool' ), 'returns' => 'paged list of exports' ),
        'export' => array( 'summary' => 'One RSS export with its sources', 'access' => 'public', 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'export with sources' ),
        'exportByUrl' => array( 'summary' => 'One RSS export by its access URL name', 'access' => 'public', 'write' => false, 'args' => array( 'access_url' => 'string' ), 'returns' => 'export with sources' ),
        'output' => array( 'summary' => 'The feed document of an RSS export as the reader gets it (RSS 1.0, 2.0, Atom, OPML, iTunes)', 'access' => 'public', 'write' => false,
            'args' => array( 'access_url' => 'string' ), 'returns' => 'content type and the document text' ),
        'formats' => array( 'summary' => 'The export formats and their names', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'list of formats' ),
        'rss' => array( 'summary' => 'An RSS 2.0 feed of the latest content below a node', 'access' => 'public', 'write' => false,
            'args' => array( 'node_id' => 'int', 'limit' => 'int', 'classes' => 'list' ), 'returns' => 'content type and the document text' ),
        'atom' => array( 'summary' => 'An Atom 1.0 feed of the latest content below a node', 'access' => 'public', 'write' => false,
            'args' => array( 'node_id' => 'int', 'limit' => 'int', 'classes' => 'list' ), 'returns' => 'content type and the document text' ),
        'json' => array( 'summary' => 'A JSON Feed 1.1 of the latest content below a node', 'access' => 'public', 'write' => false,
            'args' => array( 'node_id' => 'int', 'limit' => 'int', 'classes' => 'list' ), 'returns' => 'the feed object' ),
        'items' => array( 'summary' => 'The feed items of a node as plain data (title, url, date, summary)', 'access' => 'public', 'write' => false,
            'args' => array( 'node_id' => 'int', 'limit' => 'int', 'offset' => 'int', 'classes' => 'list' ), 'returns' => 'paged list of items' ),
        'discover' => array( 'summary' => 'The feed addresses a site offers for a node: the generic ones and the exports that cover it', 'access' => 'public', 'write' => false,
            'args' => array( 'node_id' => 'int' ), 'returns' => 'list of feeds with type and URL' ),
        'createExport' => array( 'summary' => 'Creates an active RSS export of a subtree', 'access' => array( 'rss', 'edit' ), 'write' => true,
            'args' => array( 'title' => 'string POST', 'access_url' => 'string POST', 'source_node_id' => 'int POST', 'rss_version' => 'string POST 1.0|2.0|ATOM', 'number_of_objects' => 'int POST', 'description' => 'string POST' ),
            'returns' => 'the export' ),
        'updateExport' => array( 'summary' => 'Changes title, description, size or format of an export', 'access' => array( 'rss', 'edit' ), 'write' => true,
            'args' => array( 'id' => 'int POST', 'title' => 'string POST', 'description' => 'string POST', 'number_of_objects' => 'int POST', 'rss_version' => 'string POST', 'main_node_only' => 'bool POST' ), 'returns' => 'the export' ),
        'setActive' => array( 'summary' => 'Switches an export on or off', 'access' => array( 'rss', 'edit' ), 'write' => true, 'args' => array( 'id' => 'int POST', 'active' => 'bool POST' ), 'returns' => 'the export' ),
        'addSource' => array( 'summary' => 'Adds a source subtree to an export', 'access' => array( 'rss', 'edit' ), 'write' => true,
            'args' => array( 'id' => 'int POST', 'source_node_id' => 'int POST', 'subnodes' => 'bool POST' ), 'returns' => 'the export' ),
        'removeSource' => array( 'summary' => 'Removes a source from an export', 'access' => array( 'rss', 'edit' ), 'write' => true, 'args' => array( 'id' => 'int POST', 'source_id' => 'int POST' ), 'returns' => 'the export' ),
        'removeExport' => array( 'summary' => 'Removes an export with its sources', 'access' => array( 'rss', 'edit' ), 'write' => true, 'args' => array( 'id' => 'int POST' ), 'returns' => 'removed id' ),
        'imports' => array( 'summary' => 'The RSS imports with their source URL, destination and state', 'access' => array( 'rss', 'edit' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of imports' ),
        'import' => array( 'summary' => 'One RSS import', 'access' => array( 'rss', 'edit' ), 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'import' ),
        'importStatus' => array( 'summary' => 'What an import brought in: objects created by it, the newest one and when', 'access' => array( 'rss', 'edit' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'objects, latest, latest_date, active' ),
        'importCheck' => array( 'summary' => 'Whether the source of an import is an address the server may fetch, and optionally its feed version (fetches the source)', 'access' => array( 'rss', 'edit' ), 'write' => false,
            'args' => array( 'id' => 'int', 'fetch' => 'bool' ), 'returns' => 'fetchable, version' ),
        'importSetActive' => array( 'summary' => 'Switches an import on or off (the rssimport cronjob runs the active ones)', 'access' => array( 'rss', 'edit' ), 'write' => true,
            'args' => array( 'id' => 'int POST', 'active' => 'bool POST' ), 'returns' => 'the import' ),
    );

    // ---------------------------------------------------------------- helpers

    protected static function fetchExport( $id )
    {
        $e = eZRSSExport::fetch( (int)$id );
        if ( !$e instanceof eZRSSExport )
            throw new expServiceException( "No RSS export $id", 404 );
        return $e;
    }

    protected static function fetchImport( $id )
    {
        $i = eZRSSImport::fetch( (int)$id );
        if ( !$i instanceof eZRSSImport )
            throw new expServiceException( "No RSS import $id", 404 );
        return $i;
    }

    protected static function limit( array $args, $i, $default = 20 )
    {
        $l = self::arg( $args, $i, 'int', $default );
        if ( $l < 1 )
            throw new expServiceException( 'limit must be 1 or more', 400 );
        return min( $l, 100 );
    }

    /** The first text found among the usual summary attributes, as plain text. */
    protected static function summary( eZContentObjectTreeNode $n )
    {
        $map = $n->attribute( 'data_map' );
        foreach ( array( 'intro', 'short_description', 'description', 'summary', 'abstract', 'message', 'body', 'text' ) as $f )
        {
            if ( !isset( $map[$f] ) )
                continue;
            $a = $map[$f];
            if ( $a->attribute( 'data_type_string' ) === 'ezxmltext' )
            {
                $html = (string)$a->content()->attribute( 'output' )->attribute( 'output_text' );
                $text = trim( preg_replace( '/\s+/', ' ', html_entity_decode( strip_tags( $html ), ENT_QUOTES, 'UTF-8' ) ) );
            }
            else
                $text = trim( preg_replace( '/\s+/', ' ', strip_tags( (string)$a->toString() ) ) );
            if ( $text !== '' )
                return mb_strlen( $text ) > 400 ? mb_substr( $text, 0, 397 ) . '...' : $text;
        }
        return '';
    }

    protected static function site()
    {
        return eZRSSExport::publicSiteURL();
    }

    /** The feed items below a node: newest published first. */
    protected static function feedItems( $nodeId, $limit, $offset, array $classes )
    {
        $root = self::node( $nodeId, 'read' );
        $p = array( 'SortBy' => array( array( 'published', false ), array( 'node_id', false ) ), 'Limit' => $limit, 'Offset' => $offset, 'MainNodeOnly' => true );
        if ( $classes )
        {
            $p['ClassFilterType'] = 'include';
            $p['ClassFilterArray'] = $classes;
        }
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( $p, $root->attribute( 'node_id' ) );
        $base = self::site();
        $items = array();
        foreach ( (array)$nodes as $n )
        {
            $o = $n->attribute( 'object' );
            $owner = $o->attribute( 'owner' );
            $items[] = array( 'id' => $base . '/content/view/full/' . $n->attribute( 'node_id' ), 'node_id' => (int)$n->attribute( 'node_id' ), 'title' => $n->attribute( 'name' ),
                'url' => $base . '/' . ltrim( $n->attribute( 'url_alias' ), '/' ), 'published' => self::iso( $o->attribute( 'published' ) ), 'modified' => self::iso( $o->attribute( 'modified' ) ),
                'summary' => self::summary( $n ), 'author' => $owner ? $owner->attribute( 'name' ) : null, 'class' => $o->attribute( 'class_identifier' ) );
        }
        return array( $root, $items, (int)eZContentObjectTreeNode::subTreeCountByNodeID( $p, $root->attribute( 'node_id' ) ) );
    }

    protected static function classesArg( array $args, $i )
    {
        return self::arg( $args, $i, 'list', array() );
    }

    protected static function doc( $contentType, $content )
    {
        return self::ok( array( 'content_type' => $contentType, 'content' => $content ) );
    }

    protected static function rfc822( $iso )
    {
        return $iso ? gmdate( 'D, d M Y H:i:s', strtotime( $iso ) ) . ' GMT' : null;
    }

    // ---------------------------------------------------------------- exports

    public static function exports( array $args )
    {
        self::guard( 'exports' );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        $only = self::arg( $args, 2, 'bool', false );
        $items = array();
        foreach ( (array)eZRSSExport::fetchList( true ) as $e )
            if ( !$only || $e->attribute( 'active' ) )
                $items[] = expCommerceExport::rssExport( $e );
        return self::page( array_slice( $items, $offset, $limit ), count( $items ), $offset, $limit );
    }

    public static function export( array $args )
    {
        self::guard( 'export' );
        return self::ok( expCommerceExport::rssExport( self::fetchExport( self::arg( $args, 0, 'int' ) ), true ) );
    }

    public static function exportByUrl( array $args )
    {
        self::guard( 'exportByUrl' );
        $e = eZRSSExport::fetchByName( self::arg( $args, 0, 'string' ) );
        if ( !$e instanceof eZRSSExport )
            throw new expServiceException( 'No RSS export with that address', 404 );
        return self::ok( expCommerceExport::rssExport( $e, true ) );
    }

    public static function output( array $args )
    {
        self::guard( 'output' );
        $e = eZRSSExport::fetchByName( self::arg( $args, 0, 'string' ) );
        if ( !$e instanceof eZRSSExport || !$e->attribute( 'active' ) )
            throw new expServiceException( 'No active RSS export with that address', 404 );
        $xml = $e->rssXmlContent();
        if ( !is_string( $xml ) || $xml === '' )
            throw new expServiceException( 'The export produced no document', 500 );
        $type = $e->attribute( 'rss_version' ) === 'ATOM' ? 'application/atom+xml' : ( $e->attribute( 'rss_version' ) === 'OPML' ? 'text/x-opml' : 'application/rss+xml' );
        return self::doc( $type . '; charset=utf-8', $xml );
    }

    public static function formats( array $args )
    {
        self::guard( 'formats' );
        $out = array();
        foreach ( eZRSSExport::formatLabels() as $value => $label )
            $out[] = array( 'value' => $value, 'label' => $label );
        return self::ok( $out );
    }

    // ---------------------------------------------------------------- feeds of a subtree

    public static function items( array $args )
    {
        self::guard( 'items' );
        $limit = self::limit( $args, 1 );
        $offset = self::arg( $args, 2, 'int', 0 );
        list( $root, $items, $total ) = self::feedItems( self::arg( $args, 0, 'int' ), $limit, $offset, self::classesArg( $args, 3 ) );
        return self::page( $items, $total, $offset, $limit );
    }

    public static function json( array $args )
    {
        self::guard( 'json' );
        list( $root, $items ) = self::feedItems( self::arg( $args, 0, 'int' ), self::limit( $args, 1 ), 0, self::classesArg( $args, 2 ) );
        $base = self::site();
        $feed = array( 'version' => 'https://jsonfeed.org/version/1.1', 'title' => $root->attribute( 'name' ),
            'home_page_url' => $base . '/' . ltrim( $root->attribute( 'url_alias' ), '/' ),
            'feed_url' => $base . '/ezjscore/call/expfeed::json::' . (int)$root->attribute( 'node_id' ), 'items' => array() );
        foreach ( $items as $i )
        {
            $item = array( 'id' => (string)$i['node_id'], 'url' => $i['url'], 'title' => $i['title'], 'content_text' => $i['summary'] !== '' ? $i['summary'] : $i['title'] );
            if ( $i['published'] )
                $item['date_published'] = $i['published'];
            if ( $i['modified'] )
                $item['date_modified'] = $i['modified'];
            if ( $i['author'] )
                $item['authors'] = array( array( 'name' => $i['author'] ) );
            $feed['items'][] = $item;
        }
        return self::ok( $feed );
    }

    public static function rss( array $args )
    {
        self::guard( 'rss' );
        list( $root, $items ) = self::feedItems( self::arg( $args, 0, 'int' ), self::limit( $args, 1 ), 0, self::classesArg( $args, 2 ) );
        $base = self::site();
        $d = new DOMDocument( '1.0', 'UTF-8' );
        $d->formatOutput = true;
        $rss = $d->appendChild( $d->createElement( 'rss' ) );
        $rss->setAttribute( 'version', '2.0' );
        $c = $rss->appendChild( $d->createElement( 'channel' ) );
        $c->appendChild( $d->createElement( 'title' ) )->appendChild( $d->createTextNode( (string)$root->attribute( 'name' ) ) );
        $c->appendChild( $d->createElement( 'link' ) )->appendChild( $d->createTextNode( $base . '/' . ltrim( $root->attribute( 'url_alias' ), '/' ) ) );
        $c->appendChild( $d->createElement( 'description' ) )->appendChild( $d->createTextNode( (string)$root->attribute( 'name' ) ) );
        foreach ( $items as $i )
        {
            $it = $c->appendChild( $d->createElement( 'item' ) );
            $it->appendChild( $d->createElement( 'title' ) )->appendChild( $d->createTextNode( (string)$i['title'] ) );
            $it->appendChild( $d->createElement( 'link' ) )->appendChild( $d->createTextNode( $i['url'] ) );
            $g = $it->appendChild( $d->createElement( 'guid' ) );
            $g->setAttribute( 'isPermaLink', 'false' );
            $g->appendChild( $d->createTextNode( $i['id'] ) );
            $it->appendChild( $d->createElement( 'description' ) )->appendChild( $d->createTextNode( $i['summary'] ) );
            if ( $i['published'] )
                $it->appendChild( $d->createElement( 'pubDate' ) )->appendChild( $d->createTextNode( self::rfc822( $i['published'] ) ) );
        }
        return self::doc( 'application/rss+xml; charset=utf-8', $d->saveXML() );
    }

    public static function atom( array $args )
    {
        self::guard( 'atom' );
        list( $root, $items ) = self::feedItems( self::arg( $args, 0, 'int' ), self::limit( $args, 1 ), 0, self::classesArg( $args, 2 ) );
        $base = self::site();
        $d = new DOMDocument( '1.0', 'UTF-8' );
        $d->formatOutput = true;
        $f = $d->appendChild( $d->createElementNS( 'http://www.w3.org/2005/Atom', 'feed' ) );
        $f->appendChild( $d->createElement( 'title' ) )->appendChild( $d->createTextNode( (string)$root->attribute( 'name' ) ) );
        $f->appendChild( $d->createElement( 'id' ) )->appendChild( $d->createTextNode( $base . '/content/view/full/' . (int)$root->attribute( 'node_id' ) ) );
        $l = $f->appendChild( $d->createElement( 'link' ) );
        $l->setAttribute( 'href', $base . '/' . ltrim( $root->attribute( 'url_alias' ), '/' ) );
        $updated = $items ? $items[0]['modified'] ?: $items[0]['published'] : self::iso( time() );
        $f->appendChild( $d->createElement( 'updated' ) )->appendChild( $d->createTextNode( $updated ?: self::iso( time() ) ) );
        foreach ( $items as $i )
        {
            $e = $f->appendChild( $d->createElement( 'entry' ) );
            $e->appendChild( $d->createElement( 'title' ) )->appendChild( $d->createTextNode( (string)$i['title'] ) );
            $e->appendChild( $d->createElement( 'id' ) )->appendChild( $d->createTextNode( $i['id'] ) );
            $el = $e->appendChild( $d->createElement( 'link' ) );
            $el->setAttribute( 'href', $i['url'] );
            $e->appendChild( $d->createElement( 'updated' ) )->appendChild( $d->createTextNode( $i['modified'] ?: $i['published'] ?: self::iso( time() ) ) );
            if ( $i['published'] )
                $e->appendChild( $d->createElement( 'published' ) )->appendChild( $d->createTextNode( $i['published'] ) );
            $e->appendChild( $d->createElement( 'summary' ) )->appendChild( $d->createTextNode( $i['summary'] ) );
            if ( $i['author'] )
                $e->appendChild( $d->createElement( 'author' ) )->appendChild( $d->createElement( 'name' ) )->appendChild( $d->createTextNode( $i['author'] ) );
        }
        return self::doc( 'application/atom+xml; charset=utf-8', $d->saveXML() );
    }

    public static function discover( array $args )
    {
        self::guard( 'discover' );
        $n = self::node( self::arg( $args, 0, 'int' ), 'read' );
        $id = (int)$n->attribute( 'node_id' );
        $pathIds = array_map( 'intval', explode( '/', trim( (string)$n->attribute( 'path_string' ), '/' ) ) );
        $feeds = array(
            array( 'type' => 'rss', 'title' => $n->attribute( 'name' ), 'url' => '/ezjscore/call/expfeed::rss::' . $id ),
            array( 'type' => 'atom', 'title' => $n->attribute( 'name' ), 'url' => '/ezjscore/call/expfeed::atom::' . $id ),
            array( 'type' => 'json', 'title' => $n->attribute( 'name' ), 'url' => '/ezjscore/call/expfeed::json::' . $id ) );
        foreach ( (array)eZRSSExport::fetchList( true ) as $e )
        {
            if ( !$e->attribute( 'active' ) )
                continue;
            foreach ( (array)$e->itemList() as $s )
                if ( (int)$s->attribute( 'source_node_id' ) === $id || ( $s->attribute( 'subnodes' ) && in_array( (int)$s->attribute( 'source_node_id' ), $pathIds, true ) ) )
                {
                    $feeds[] = array( 'type' => 'export', 'title' => $e->attribute( 'title' ), 'url' => '/rss/feed/' . $e->attribute( 'access_url' ), 'format' => $e->attribute( 'rss_version' ) );
                    break;
                }
        }
        return self::ok( $feeds );
    }

    // ---------------------------------------------------------------- export management

    protected static function applyExport( eZRSSExport $e, $create )
    {
        if ( ( $v = self::post( 'title', 'string', null ) ) !== null )
        {
            if ( trim( $v ) === '' || mb_strlen( $v ) > 255 )
                throw new expServiceException( 'The title is required, up to 255 characters', 422 );
            $e->setAttribute( 'title', trim( $v ) );
        }
        if ( ( $v = self::post( 'description', 'string', null ) ) !== null )
            $e->setAttribute( 'description', $v );
        if ( ( $v = self::post( 'number_of_objects', 'int', null ) ) !== null )
        {
            if ( $v < 1 || $v > 500 )
                throw new expServiceException( 'number_of_objects is between 1 and 500', 422 );
            $e->setAttribute( 'number_of_objects', $v );
        }
        if ( ( $v = self::post( 'rss_version', 'string', null ) ) !== null )
        {
            if ( !in_array( $v, array( '1.0', '2.0', 'ATOM' ), true ) )
                throw new expServiceException( 'rss_version is 1.0, 2.0 or ATOM', 422 );
            $e->setAttribute( 'rss_version', $v );
        }
        if ( ( $v = self::post( 'main_node_only', 'bool', null ) ) !== null )
            $e->setAttribute( 'main_node_only', $v ? 1 : 0 );
        if ( $create && ( $v = self::post( 'access_url', 'string', null ) ) !== null )
        {
            if ( !preg_match( '/^[A-Za-z0-9_.-]{1,100}$/', $v ) )
                throw new expServiceException( 'access_url is letters, digits, dot, dash and underscore', 422 );
            if ( eZRSSExport::fetchByName( $v ) )
                throw new expServiceException( 'That access URL is taken', 409 );
            $e->setAttribute( 'access_url', $v );
        }
    }

    public static function createExport( array $args )
    {
        self::guard( 'createExport' );
        $node = self::node( self::post( 'source_node_id', 'int' ), 'read' );
        self::post( 'title', 'string' );
        self::post( 'access_url', 'string' );
        $e = eZRSSExport::create( (int)eZUser::currentUser()->attribute( 'contentobject_id' ) );
        $e->setAttribute( 'status', eZRSSExport::STATUS_VALID );
        $e->setAttribute( 'rss_version', '2.0' );
        $e->setAttribute( 'number_of_objects', 10 );
        $e->setAttribute( 'main_node_only', 1 );
        $e->setAttribute( 'active', 1 );
        self::applyExport( $e, true );
        $e->store();
        $item = eZRSSExportItem::create( $e->attribute( 'id' ) );
        $item->setAttribute( 'status', eZRSSExport::STATUS_VALID );
        $item->setAttribute( 'source_node_id', (int)$node->attribute( 'node_id' ) );
        $item->setAttribute( 'subnodes', 1 );
        $item->setAttribute( 'class_id', (int)$node->attribute( 'object' )->attribute( 'contentclass_id' ) );
        $item->store();
        return self::ok( expCommerceExport::rssExport( eZRSSExport::fetch( $e->attribute( 'id' ) ), true ) );
    }

    public static function updateExport( array $args )
    {
        self::guard( 'updateExport' );
        $e = self::fetchExport( self::post( 'id', 'int' ) );
        self::applyExport( $e, false );
        $e->store();
        return self::ok( expCommerceExport::rssExport( eZRSSExport::fetch( $e->attribute( 'id' ) ), true ) );
    }

    public static function setActive( array $args )
    {
        self::guard( 'setActive' );
        $e = self::fetchExport( self::post( 'id', 'int' ) );
        $e->setAttribute( 'active', self::post( 'active', 'bool' ) ? 1 : 0 );
        $e->store();
        return self::ok( expCommerceExport::rssExport( eZRSSExport::fetch( $e->attribute( 'id' ) ), true ) );
    }

    public static function addSource( array $args )
    {
        self::guard( 'addSource' );
        $e = self::fetchExport( self::post( 'id', 'int' ) );
        $node = self::node( self::post( 'source_node_id', 'int' ), 'read' );
        $item = eZRSSExportItem::create( $e->attribute( 'id' ) );
        $item->setAttribute( 'status', eZRSSExport::STATUS_VALID );
        $item->setAttribute( 'source_node_id', (int)$node->attribute( 'node_id' ) );
        $item->setAttribute( 'subnodes', self::post( 'subnodes', 'bool', true ) ? 1 : 0 );
        $item->setAttribute( 'class_id', (int)$node->attribute( 'object' )->attribute( 'contentclass_id' ) );
        $item->store();
        return self::ok( expCommerceExport::rssExport( eZRSSExport::fetch( $e->attribute( 'id' ) ), true ) );
    }

    public static function removeSource( array $args )
    {
        self::guard( 'removeSource' );
        $e = self::fetchExport( self::post( 'id', 'int' ) );
        $item = eZRSSExportItem::fetch( self::post( 'source_id', 'int' ) );
        if ( !$item instanceof eZRSSExportItem || (int)$item->attribute( 'rssexport_id' ) !== (int)$e->attribute( 'id' ) )
            throw new expServiceException( 'No such source in this export', 404 );
        $item->remove();
        return self::ok( expCommerceExport::rssExport( eZRSSExport::fetch( $e->attribute( 'id' ) ), true ) );
    }

    public static function removeExport( array $args )
    {
        self::guard( 'removeExport' );
        $e = self::fetchExport( self::post( 'id', 'int' ) );
        $id = (int)$e->attribute( 'id' );
        $e->removeThis();
        return self::ok( array( 'removed' => $id ) );
    }

    // ---------------------------------------------------------------- imports

    public static function imports( array $args )
    {
        self::guard( 'imports' );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        $items = array();
        foreach ( (array)eZRSSImport::fetchList( true, eZRSSImport::STATUS_VALID, $offset, $limit ) as $i )
            $items[] = expCommerceExport::rssImport( $i );
        return self::page( $items, (int)eZRSSImport::fetchListCount(), $offset, $limit );
    }

    public static function import( array $args )
    {
        self::guard( 'import' );
        return self::ok( expCommerceExport::rssImport( self::fetchImport( self::arg( $args, 0, 'int' ) ) ) );
    }

    public static function importStatus( array $args )
    {
        self::guard( 'importStatus' );
        $i = self::fetchImport( self::arg( $args, 0, 'int' ) );
        $id = (int)$i->attribute( 'id' );
        $db = eZDB::instance();
        $n = (int)$db->arrayQuery( "SELECT COUNT(*) AS c FROM ezcontentobject WHERE remote_id LIKE 'RSSImport\\_" . $id . "\\_%'" )[0]['c'];
        $last = $db->arrayQuery( "SELECT id, name, published FROM ezcontentobject WHERE remote_id LIKE 'RSSImport\\_" . $id . "\\_%' ORDER BY published DESC", array( 'limit' => 1 ) );
        return self::ok( array( 'import_id' => $id, 'active' => (bool)$i->attribute( 'active' ), 'objects' => $n, 'latest' => $last ? (int)$last[0]['id'] : null,
            'latest_name' => $last ? $last[0]['name'] : null, 'latest_date' => $last ? self::iso( $last[0]['published'] ) : null ) );
    }

    public static function importCheck( array $args )
    {
        self::guard( 'importCheck' );
        $i = self::fetchImport( self::arg( $args, 0, 'int' ) );
        $url = eZRSSImport::fetchableURL( $i->attribute( 'url' ) );
        $out = array( 'import_id' => (int)$i->attribute( 'id' ), 'fetchable' => $url !== false, 'fetched' => false, 'version' => null );
        if ( $url !== false && self::arg( $args, 1, 'bool', false ) )
        {
            $v = eZRSSImport::getRSSVersion( $url );
            $out['fetched'] = true;
            $out['version'] = $v ?: null;
        }
        return self::ok( $out );
    }

    public static function importSetActive( array $args )
    {
        self::guard( 'importSetActive' );
        $i = self::fetchImport( self::post( 'id', 'int' ) );
        $i->setAttribute( 'active', self::post( 'active', 'bool' ) ? 1 : 0 );
        $i->store();
        return self::ok( expCommerceExport::rssImport( eZRSSImport::fetch( $i->attribute( 'id' ) ) ) );
    }
}
