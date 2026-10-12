<?php
/**
 * The registry of the audit console's sources (expAuditSource), from audit.ini [AuditSourceSettings] Sources[]:
 * which are registered, which work here, which the current user may see, and the safe way to ask them. A class
 * that is missing or does not implement the interface, and a source whose problem() is not '', are left out and
 * reported (audit/settings). A source that throws is left out of that call and logged; the view carries on.
 *
 * With no source registered (the shipped settings) every method answers "nothing", so the views look and work as
 * without the registry. Guide: doc/bc/6.0/audit.md ("An extension's source in the console").
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditSourceRegistry
{
    /** @var expAuditSource[]|null identifier => source, for the settings state in $builtFor */
    protected static $sources = null;

    /** @var array identifier => class, label, channel, problem */
    protected static $status = array();

    /** @var string|null */
    protected static $builtFor = null;

    /**
     * Every registered source with its state, also the ones that do not work here.
     *
     * @return array identifier => class, label, channel, problem
     */
    public static function status()
    {
        self::build();
        return self::$status;
    }

    /**
     * The sources that work here.
     *
     * @return expAuditSource[] identifier => source
     */
    public static function sources()
    {
        self::build();
        return self::$sources;
    }

    /**
     * The sources the current user may see: those of a channel the user may read (a source without a channel of
     * its own: any user who may read the audit).
     *
     * @param string[]|null $allowed expAuditConsole::allowedChannels()
     * @return expAuditSource[] identifier => source
     */
    public static function readable( $allowed )
    {
        $out = array();
        if ( is_array( $allowed ) && !$allowed )
            return $out;
        foreach ( self::sources() as $id => $source )
        {
            $channel = self::call( $id, function () use ( $source ) { return (string)$source->channel(); }, '' );
            if ( $channel === '' || expAuditConsole::channelAllowed( $channel, $allowed ) )
                $out[$id] = $source;
        }
        return $out;
    }

    /**
     * The keys of a left menu's links (menu.ini [<section>] Links[<key>]) that lead to a channel the current user
     * may not read under the Channel limitation of audit/read, so the menu leaves them out instead of offering a
     * page the console refuses. A link leads to a channel when its address is an audit view filtered on it
     * (audit/console/(channel)/ai), or when menu.ini names it: AuditChannel_<key>=<channel>. This is how an
     * extension source's entries ("AI events") disappear for a user limited to other channels; it never needs the
     * extension's name. A user who may read every channel (or none: the links' policies hide them) gets none.
     *
     * @param string $section menu.ini block, default Leftmenu_audit
     * @param string[]|null|false $allowed expAuditConsole::allowedChannels(), false: the current user's
     * @return string[] link keys
     */
    public static function hiddenMenuLinks( $section = 'Leftmenu_audit', $allowed = false )
    {
        if ( $allowed === false )
            $allowed = class_exists( 'expAuditConsole' ) ? expAuditConsole::allowedChannels() : null;
        if ( $allowed === null || !class_exists( 'eZINI' ) )
            return array();
        $ini = eZINI::instance( 'menu.ini' );
        if ( !$ini->hasVariable( $section, 'Links' ) )
            return array();
        $hidden = array();
        foreach ( (array)$ini->variable( $section, 'Links' ) as $key => $url )
        {
            $channel = $ini->hasVariable( $section, 'AuditChannel_' . $key ) ? trim( (string)$ini->variable( $section, 'AuditChannel_' . $key ) )
                                                                             : self::menuLinkChannel( (string)$url );
            if ( $channel !== '' && !expAuditConsole::channelAllowed( $channel, $allowed ) )
                $hidden[] = (string)$key;
        }
        return $hidden;
    }

    /**
     * @param string $url a menu link's address
     * @return string the channel an audit view address is filtered on ('' for none)
     */
    public static function menuLinkChannel( $url )
    {
        return preg_match( '#^/?audit/[a-z_]+(?:/.*)?/\(channel\)/([^/]+)#', (string)$url, $m ) ? rawurldecode( $m[1] ) : '';
    }

    /**
     * The source that owns a channel.
     *
     * @param string $channel
     * @return array|null array( identifier, source )
     */
    public static function forChannel( $channel )
    {
        if ( (string)$channel === '' )
            return null;
        foreach ( self::sources() as $id => $source )
            if ( self::call( $id, function () use ( $source ) { return (string)$source->channel(); }, '' ) === (string)$channel )
                return array( $id, $source );
        return null;
    }

    /**
     * The label of each source's channel, for the channel filters ("ai" => "Exponential Intelligence").
     *
     * @return string[] channel => label
     */
    public static function channelLabels()
    {
        $out = array();
        foreach ( self::status() as $s )
            if ( $s['channel'] !== '' && $s['problem'] === '' )
                $out[$s['channel']] = $s['label'];
        return $out;
    }

    /**
     * Links of the readable sources for a view.
     *
     * @param string $view audit/console ...
     * @param array $filters the view's raw filters
     * @param string[]|null $allowed
     * @return array[] identifier, label, channel, links (label, url)
     */
    public static function links( $view, array $filters, $allowed )
    {
        $out = array();
        foreach ( self::readable( $allowed ) as $id => $source )
        {
            $links = self::cleanLinks( self::call( $id, function () use ( $source, $view, $filters ) { return $source->links( $view, $filters ); }, array() ) );
            if ( $links )
                $out[] = array( 'identifier' => $id, 'label' => self::$status[$id]['label'], 'channel' => self::$status[$id]['channel'], 'links' => $links );
        }
        return $out;
    }

    /**
     * The dashboard panels of the readable sources.
     *
     * @param expAuditQuery $q
     * @param string[]|null $allowed
     * @param array $period today_ms, week_ms
     * @return array[] identifier, label, channel, figures, lists, links, note
     */
    public static function panels( expAuditQuery $q, $allowed, array $period )
    {
        $out = array();
        foreach ( self::readable( $allowed ) as $id => $source )
        {
            $panel = self::call( $id, function () use ( $source, $q, $allowed, $period ) { return $source->dashboard( $q, $allowed, $period ); }, null );
            if ( is_array( $panel ) )
                $out[] = self::cleanPanel( $id, $panel );
        }
        return $out;
    }

    /**
     * What the source of a record's channel says about it, for audit/event.
     *
     * @param array $record
     * @return array|null identifier, label, fields (label, value), links (label, url)
     */
    public static function event( array $record )
    {
        $owner = self::forChannel( isset( $record['channel'] ) ? (string)$record['channel'] : '' );
        if ( !$owner )
            return null;
        list( $id, $source ) = $owner;
        $detail = self::call( $id, function () use ( $source, $record ) { return $source->event( $record ); }, null );
        if ( !is_array( $detail ) )
            return null;
        $fields = array();
        foreach ( isset( $detail['fields'] ) ? (array)$detail['fields'] : array() as $f )
            if ( is_array( $f ) && isset( $f['label'], $f['value'] ) && is_scalar( $f['value'] ) && (string)$f['value'] !== '' )
                $fields[] = array( 'label' => (string)$f['label'], 'value' => (string)$f['value'] );
        return array( 'identifier' => $id, 'label' => self::$status[$id]['label'], 'fields' => $fields,
                      'links' => self::cleanLinks( isset( $detail['links'] ) ? $detail['links'] : array() ) );
    }

    /**
     * The address of an object or target type that a source records.
     *
     * @param string $type
     * @param string $id
     * @return string|null
     */
    public static function linkFor( $type, $id )
    {
        foreach ( self::sources() as $sid => $source )
        {
            $link = self::call( $sid, function () use ( $source, $type, $id ) { return $source->linkFor( $type, $id ); }, null );
            if ( is_string( $link ) && self::isPath( $link ) )
                return $link;
        }
        return null;
    }

    /**
     * The charts of the readable sources.
     *
     * @param string[]|null $allowed
     * @return array[] identifier, label, channel, title, series (label, name)
     */
    public static function charts( $allowed )
    {
        $out = array();
        foreach ( self::readable( $allowed ) as $id => $source )
            foreach ( (array)self::call( $id, function () use ( $source ) { return $source->charts(); }, array() ) as $chart )
            {
                if ( !is_array( $chart ) || empty( $chart['series'] ) )
                    continue;
                $series = array();
                foreach ( (array)$chart['series'] as $s )
                    if ( is_array( $s ) && isset( $s['label'], $s['name'] ) && expAuditTaxonomy::patternProblem( (string)$s['name'] ) === null )
                        $series[] = array( 'label' => (string)$s['label'], 'name' => (string)$s['name'] );
                if ( $series )
                    $out[] = array( 'identifier' => $id, 'label' => self::$status[$id]['label'], 'channel' => self::$status[$id]['channel'],
                                    'title' => isset( $chart['title'] ) ? (string)$chart['title'] : self::$status[$id]['label'], 'series' => $series );
            }
        return $out;
    }

    /**
     * The CSV columns of the source that owns a channel (expAuditSource::exportColumns()), cleaned: a name of a-z,
     * digits and _ (at most 48 characters) that is not one of the generic columns, a path of ranks into the record
     * (after.usage.in). Nothing for a channel without a working source, or for no channel.
     *
     * @param string|null $channel the export's channel filter
     * @param string[] $taken the generic columns
     * @return string[] column => path
     */
    public static function exportColumns( $channel, array $taken = array() )
    {
        $owner = self::forChannel( (string)$channel );
        if ( !$owner )
            return array();
        list( $id, $source ) = $owner;
        $out = array();
        foreach ( (array)self::call( $id, function () use ( $source ) { return $source->exportColumns(); }, array() ) as $name => $path )
            if ( is_string( $name ) && preg_match( '/^[a-z][a-z0-9_]{0,47}$/', $name ) && !in_array( $name, $taken, true )
                 && is_string( $path ) && preg_match( '/^[a-z][A-Za-z0-9_]*(\.[A-Za-z0-9_]+){0,5}$/', $path ) )
                $out[$name] = $path;
        return $out;
    }

    /**
     * The values of exportColumns() for one record: '' for a value that is not there, 1/0 for a boolean, JSON for a
     * list or a hash.
     *
     * @param string[] $columns column => path
     * @param array $record the record as written
     * @return string[] column => value
     */
    public static function exportValues( array $columns, array $record )
    {
        $row = array();
        foreach ( $columns as $name => $path )
        {
            $v = $record;
            foreach ( explode( '.', $path ) as $rank )
            {
                if ( !is_array( $v ) || !array_key_exists( $rank, $v ) )
                {
                    $v = null;
                    break;
                }
                $v = $v[$rank];
            }
            $row[$name] = $v === null ? '' : ( is_bool( $v ) ? ( $v ? '1' : '0' ) : ( is_scalar( $v ) ? (string)$v : expAuditJson::encode( $v ) ) );
        }
        return $row;
    }

    /**
     * @param string $path
     * @return bool a module/view path with view parameters: no scheme, no leading slash, no query, no fragment
     */
    public static function isPath( $path )
    {
        return is_string( $path ) && preg_match( '#^[A-Za-z0-9_]+(/[^/?\#\s]+)*/?$#', $path ) === 1 && strpos( $path, '://' ) === false
               && strpos( $path, '..' ) === false;
    }

    /** Forgets the registry (tests, a settings change in the same process) */
    public static function reset()
    {
        self::$sources = null;
        self::$status = array();
        self::$builtFor = null;
    }

    /**
     * Runs a call of a source; a throw is logged and gives $default.
     *
     * @param string $id
     * @param callable $call
     * @param mixed $default
     * @return mixed
     */
    protected static function call( $id, $call, $default )
    {
        try
        {
            return $call();
        }
        catch ( Throwable $e )
        {
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeError( "audit source $id: " . get_class( $e ) . ': ' . $e->getMessage(), __METHOD__ );
            return $default;
        }
    }

    /** @return array[] label, url: the links whose address is a module/view path */
    protected static function cleanLinks( $links )
    {
        $out = array();
        foreach ( (array)$links as $l )
            if ( is_array( $l ) && isset( $l['label'], $l['url'] ) && (string)$l['label'] !== '' && self::isPath( (string)$l['url'] ) )
                $out[] = array( 'label' => (string)$l['label'], 'url' => (string)$l['url'] );
        return $out;
    }

    /** @return array a panel with only the parts and fields the template shows */
    protected static function cleanPanel( $id, array $panel )
    {
        $figures = array();
        foreach ( isset( $panel['figures'] ) ? (array)$panel['figures'] : array() as $f )
            if ( is_array( $f ) && isset( $f['label'], $f['value'] ) && is_scalar( $f['value'] ) )
                $figures[] = array( 'label' => (string)$f['label'], 'value' => (string)$f['value'],
                                    'url' => isset( $f['url'] ) && self::isPath( (string)$f['url'] ) ? (string)$f['url'] : '',
                                    'state' => isset( $f['state'] ) && in_array( $f['state'], array( 'success', 'refused', 'failed' ), true ) ? $f['state'] : '' );
        $lists = array();
        foreach ( isset( $panel['lists'] ) ? (array)$panel['lists'] : array() as $l )
        {
            if ( !is_array( $l ) || !isset( $l['title'] ) )
                continue;
            $items = array();
            foreach ( isset( $l['items'] ) ? (array)$l['items'] : array() as $i )
                if ( is_array( $i ) && isset( $i['label'] ) && is_scalar( $i['label'] ) )
                    $items[] = array( 'label' => (string)$i['label'], 'n' => isset( $i['n'] ) ? (int)$i['n'] : 0,
                                      'url' => isset( $i['url'] ) && self::isPath( (string)$i['url'] ) ? (string)$i['url'] : '' );
            $lists[] = array( 'title' => (string)$l['title'], 'items' => $items );
        }
        return array( 'identifier' => $id, 'label' => self::$status[$id]['label'], 'channel' => self::$status[$id]['channel'],
                      'figures' => $figures, 'lists' => $lists,
                      'links' => self::cleanLinks( isset( $panel['links'] ) ? $panel['links'] : array() ),
                      'note' => isset( $panel['note'] ) && is_scalar( $panel['note'] ) ? (string)$panel['note'] : '' );
    }

    /** Builds the registry once per settings state */
    protected static function build()
    {
        $config = expAuditConfig::get();
        $registered = isset( $config['sources'] ) ? (array)$config['sources'] : array();
        // the labels are translated: one build per locale too (a Velocity worker serves several siteaccesses)
        $locale = class_exists( 'eZINI' ) ? (string)eZINI::instance()->variable( 'RegionalSettings', 'Locale' ) : '';
        $key = md5( serialize( $registered ) . "\n" . implode( ',', $config['channels'] ) . "\n" . $locale );
        if ( self::$sources !== null && self::$builtFor === $key )
            return;
        self::$sources = array();
        self::$status = array();
        self::$builtFor = $key;
        foreach ( $registered as $id => $class )
        {
            $id = (string)$id;
            $status = array( 'class' => (string)$class, 'label' => $id, 'channel' => '', 'problem' => '' );
            if ( !preg_match( '/^[a-z][a-z0-9_]{0,31}$/', $id ) )
                $status['problem'] = 'not a valid identifier';
            elseif ( !is_string( $class ) || $class === '' || !class_exists( $class ) )
                $status['problem'] = 'the class does not exist';
            elseif ( !is_subclass_of( $class, 'expAuditSource' ) )
                $status['problem'] = 'the class does not implement expAuditSource';
            else
            {
                $source = self::call( $id, function () use ( $class ) { return new $class(); }, null );
                if ( !$source )
                    $status['problem'] = 'the class could not be created';
                else
                {
                    $status['label'] = (string)self::call( $id, function () use ( $source ) { return $source->label(); }, $id ) ?: $id;
                    $status['channel'] = (string)self::call( $id, function () use ( $source ) { return $source->channel(); }, '' );
                    $status['problem'] = (string)self::call( $id, function () use ( $source ) { return $source->problem(); }, 'problem() failed' );
                    if ( $status['problem'] === '' && $status['channel'] !== '' && !in_array( $status['channel'], $config['channels'], true ) )
                        $status['problem'] = "the channel '{$status['channel']}' is not in [AuditChannelSettings] Channels[]";
                    if ( $status['problem'] === '' )
                        self::$sources[$id] = $source;
                }
            }
            self::$status[$id] = $status;
        }
    }
}
