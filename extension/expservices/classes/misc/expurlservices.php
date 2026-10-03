<?php
/**
 * ezjscore/call/expurl::<service> - the external links stored by URL attributes (ezurl): lists, search, validity,
 * the objects that use a link, and a check of a stored link. Policies url/list, url/view and url/edit. The check
 * only contacts http and https addresses that resolve to public hosts.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expUrlServices extends expServiceBase
{
    public static $services = array(
        'list' => array( 'summary' => 'The stored links, newest first', 'access' => array( 'url', 'list' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int', 'validity' => 'string' ), 'returns' => 'paged list of links (validity: all, valid, invalid, unchecked)' ),
        'get' => array( 'summary' => 'One link with its check state', 'access' => array( 'url', 'view' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'link' ),
        'count' => array( 'summary' => 'Number of stored links', 'access' => array( 'url', 'list' ), 'write' => false,
            'args' => array( 'validity' => 'string' ), 'returns' => 'count' ),
        'stats' => array( 'summary' => 'Links by validity: valid, invalid, never checked', 'access' => array( 'url', 'list' ), 'write' => false,
            'args' => array(), 'returns' => 'total, valid, invalid, unchecked' ),
        'invalid' => array( 'summary' => 'The links found invalid', 'access' => array( 'url', 'list' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of links' ),
        'unchecked' => array( 'summary' => 'The links never checked', 'access' => array( 'url', 'list' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of links' ),
        'search' => array( 'summary' => 'Links whose address contains the text', 'access' => array( 'url', 'list' ), 'write' => false,
            'args' => array( 'text' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of links' ),
        'byurl' => array( 'summary' => 'The stored link with exactly this address', 'access' => array( 'url', 'view' ), 'write' => false,
            'args' => array( 'url' => 'string' ), 'returns' => 'link' ),
        'objects' => array( 'summary' => 'The objects (readable by the user) that use a link', 'access' => array( 'url', 'view' ), 'write' => false,
            'args' => array( 'id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of object, node, version' ),
        'bydomain' => array( 'summary' => 'Link counts per host name', 'access' => array( 'url', 'list' ), 'write' => false,
            'args' => array( 'limit' => 'int' ), 'returns' => 'list of host, count' ),
        'check' => array( 'summary' => 'Requests a stored link (HEAD) and records whether it is valid; only public http/https hosts', 'access' => array( 'url', 'edit' ), 'write' => true,
            'args' => array( 'id' => 'int' ), 'returns' => 'the link with status code' ),
        'setvalid' => array( 'summary' => 'Sets the validity of a stored link by hand', 'access' => array( 'url', 'edit' ), 'write' => true,
            'args' => array( 'id' => 'int' ), 'returns' => 'the link; POST valid (1/0)' ),
    );

    protected static function exportUrl( eZURL $u )
    {
        $checked = (int)$u->attribute( 'last_checked' );
        return array( 'id' => (int)$u->attribute( 'id' ), 'url' => $u->attribute( 'url' ), 'is_valid' => (bool)$u->attribute( 'is_valid' ),
                      'last_checked' => $checked ? self::iso( $checked ) : null, 'created' => self::iso( $u->attribute( 'created' ) ),
                      'modified' => self::iso( $u->attribute( 'modified' ) ) );
    }

    protected static function fetchUrl( $id )
    {
        $u = eZURL::fetch( (int)$id );
        if ( !$u instanceof eZURL )
            throw new expServiceException( "Link $id does not exist", 404 );
        return $u;
    }

    protected static function where( $validity, $extra = '' )
    {
        switch ( $validity )
        {
            case 'all': $w = '1=1'; break;
            case 'valid': $w = 'is_valid=1'; break;
            case 'invalid': $w = 'is_valid=0'; break;
            case 'unchecked': $w = 'last_checked=0'; break;
            default: throw new expServiceException( 'validity is all, valid, invalid or unchecked', 400 );
        }
        return $w . $extra;
    }

    protected static function listWhere( $where, $limit, $offset )
    {
        $total = (int)eZDB::instance()->arrayQuery( "SELECT COUNT(*) AS n FROM ezurl WHERE $where" )[0]['n'];
        $rows = eZDB::instance()->arrayQuery( "SELECT id FROM ezurl WHERE $where ORDER BY id DESC", array( 'limit' => $limit, 'offset' => $offset ) );
        $items = array();
        foreach ( is_array( $rows ) ? $rows : array() as $r )
            $items[] = self::exportUrl( eZURL::fetch( (int)$r['id'] ) );
        return self::page( $items, $total, $offset, $limit );
    }

    public static function list( $args )
    {
        self::guard( __FUNCTION__ );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        return self::listWhere( self::where( self::arg( $args, 2, 'string', 'all' ) ), $limit, $offset );
    }

    public static function get( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( self::exportUrl( self::fetchUrl( self::arg( $args, 0, 'int' ) ) ) );
    }

    public static function count( $args )
    {
        self::guard( __FUNCTION__ );
        $w = self::where( self::arg( $args, 0, 'string', 'all' ) );
        return self::ok( array( 'count' => (int)eZDB::instance()->arrayQuery( "SELECT COUNT(*) AS n FROM ezurl WHERE $w" )[0]['n'] ) );
    }

    public static function stats( $args )
    {
        self::guard( __FUNCTION__ );
        $n = function ( $w ) { return (int)eZDB::instance()->arrayQuery( "SELECT COUNT(*) AS n FROM ezurl WHERE $w" )[0]['n']; };
        return self::ok( array( 'total' => $n( '1=1' ), 'valid' => $n( 'is_valid=1' ), 'invalid' => $n( 'is_valid=0' ), 'unchecked' => $n( 'last_checked=0' ) ) );
    }

    public static function invalid( $args )
    {
        self::guard( __FUNCTION__ );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        return self::listWhere( self::where( 'invalid' ), $limit, $offset );
    }

    public static function unchecked( $args )
    {
        self::guard( __FUNCTION__ );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        return self::listWhere( self::where( 'unchecked' ), $limit, $offset );
    }

    public static function search( $args )
    {
        self::guard( __FUNCTION__ );
        $text = trim( self::arg( $args, 0, 'string' ) );
        if ( strlen( $text ) < 2 )
            throw new expServiceException( 'The search text needs at least 2 characters', 422 );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $like = eZDB::instance()->escapeString( str_replace( array( '\\', '%', '_' ), array( '\\\\', '\\%', '\\_' ), $text ) );
        return self::listWhere( "url LIKE '%$like%'", $limit, $offset );
    }

    public static function byurl( $args )
    {
        self::guard( __FUNCTION__ );
        $u = eZURL::fetchByUrl( self::arg( $args, 0, 'string' ) );
        if ( !$u instanceof eZURL )
            throw new expServiceException( 'No stored link with this address', 404 );
        return self::ok( self::exportUrl( $u ) );
    }

    public static function objects( $args )
    {
        self::guard( __FUNCTION__ );
        $u = self::fetchUrl( self::arg( $args, 0, 'int' ) );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $rows = eZDB::instance()->arrayQuery( 'SELECT l.contentobject_attribute_id AS a, l.contentobject_attribute_version AS v FROM ezurl_object_link l WHERE l.url_id=' . (int)$u->attribute( 'id' ) . ' ORDER BY l.contentobject_attribute_id', array( 'limit' => 1000 ) );
        $items = array();
        foreach ( is_array( $rows ) ? $rows : array() as $r )
        {
            $attr = eZContentObjectAttribute::fetch( (int)$r['a'], (int)$r['v'] );
            $object = $attr ? eZContentObject::fetch( (int)$attr->attribute( 'contentobject_id' ) ) : null;
            if ( !$object || !$object->canRead() )
                continue;
            $node = $object->attribute( 'main_node' );
            $items[] = array( 'object_id' => (int)$object->attribute( 'id' ), 'name' => $object->attribute( 'name' ), 'node_id' => $node ? (int)$node->attribute( 'node_id' ) : 0,
                              'attribute_id' => (int)$r['a'], 'version' => (int)$r['v'], 'current' => (int)$r['v'] === (int)$object->attribute( 'current_version' ) );
        }
        return self::pageOf( $items, array( 1 => $limit, 2 => $offset ), 1, 2 );
    }

    public static function bydomain( $args )
    {
        self::guard( __FUNCTION__ );
        list( $limit ) = self::paging( $args, 0, 99 );
        $counts = array();
        foreach ( eZDB::instance()->arrayQuery( 'SELECT url FROM ezurl', array( 'limit' => 50000 ) ) as $r )
        {
            $host = parse_url( $r['url'], PHP_URL_HOST );
            $host = $host ? strtolower( $host ) : '(none)';
            $counts[$host] = ( isset( $counts[$host] ) ? $counts[$host] : 0 ) + 1;
        }
        arsort( $counts );
        $list = array();
        foreach ( array_slice( $counts, 0, $limit, true ) as $h => $n )
            $list[] = array( 'host' => (string)$h, 'count' => $n );
        return self::ok( $list, array( 'limit' => $limit ) );
    }

    /** @return string|null why the address may not be requested, null when it may */
    public static function refusal( $url )
    {
        $parts = parse_url( $url );
        if ( !$parts || empty( $parts['host'] ) || !isset( $parts['scheme'] ) || !in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true ) )
            return 'only http and https addresses are checked';
        if ( isset( $parts['user'] ) || isset( $parts['pass'] ) )
            return 'addresses with credentials are not checked';
        $ips = filter_var( $parts['host'], FILTER_VALIDATE_IP ) ? array( $parts['host'] ) : (array)gethostbynamel( $parts['host'] );
        if ( !$ips )
            return 'the host name does not resolve';
        foreach ( $ips as $ip )
            if ( !filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) )
                return 'the host is not a public address';
        return null;
    }

    public static function check( $args )
    {
        self::guard( __FUNCTION__ );
        $u = self::fetchUrl( self::arg( $args, 0, 'int' ) );
        $why = self::refusal( $u->attribute( 'url' ) );
        if ( $why !== null )
            throw new expServiceException( 'Not checked: ' . $why, 422 );
        $code = 0;
        if ( function_exists( 'curl_init' ) )
        {
            $ch = curl_init( $u->attribute( 'url' ) );
            curl_setopt_array( $ch, array( CURLOPT_NOBODY => true, CURLOPT_FOLLOWLOCATION => false, CURLOPT_TIMEOUT => 8, CURLOPT_CONNECTTIMEOUT => 4,
                                           CURLOPT_RETURNTRANSFER => true, CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS ) );
            curl_exec( $ch );
            $code = (int)curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
            curl_close( $ch );
        }
        $valid = $code >= 200 && $code < 400;
        eZURL::setIsValid( (int)$u->attribute( 'id' ), $valid ? 1 : 0 );
        eZURL::setLastChecked( (int)$u->attribute( 'id' ) );
        $out = self::exportUrl( self::fetchUrl( $u->attribute( 'id' ) ) );
        $out['status_code'] = $code;
        return self::ok( $out );
    }

    public static function setvalid( $args )
    {
        self::guard( __FUNCTION__ );
        $u = self::fetchUrl( self::arg( $args, 0, 'int' ) );
        $valid = self::post( 'valid', 'bool' );
        eZURL::setIsValid( (int)$u->attribute( 'id' ), $valid ? 1 : 0 );
        return self::ok( self::exportUrl( self::fetchUrl( $u->attribute( 'id' ) ) ) );
    }
}
