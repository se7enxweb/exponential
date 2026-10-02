<?php
/**
 * The audit/event view (doc/bc/6.0/audit.md, "Views"): one record in full: when, who, the request, object and
 * target with links into the admin, before and after side by side, the parent, the children, the other events of
 * the same request and job, and the chain position (prev, hash, whether the hash matches the record and the
 * file's verification state). /(format)/json sends the record as it is in the file. Policy audit/read; a record
 * of a channel the Channel limitation leaves out is refused. Recorded as system.audit.read.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Audit
{

class Event extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !class_exists( 'expAuditConsole' ) || !\expAuditConsole::available() )
            return $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
        $id = isset( $Params['EventID'] ) ? (string)$Params['EventID'] : '';
        if ( !preg_match( '/^[0-9A-HJKMNP-TV-Z]{26}$/', $id ) )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );

        $allowed = \expAuditConsole::allowedChannels();
        $config = \expAuditConfig::get();
        \expAuditConsole::refreshIndex();

        // the row of the index, else the record found in the live files
        $row = null;
        $query = \expAuditConsole::indexUsable() ? new \expAuditQuery() : null;
        if ( $query )
            $row = $query->byId( $id );
        $line = $row ? (string)$row['record'] : null;
        $file = $row ? $row['file_name'] : null;
        if ( !$row )
        {
            $reader = new \expAuditReader( $config['logDir'] );
            $found = $reader->find( $id );
            if ( $found )
            {
                $line = $found['raw'];
                $file = $found['file'];
                $rec = json_decode( $line, true );
                $row = is_array( $rec ) ? \expAuditIndexRow::fromRecord( $rec, $line, $file ) : null;
            }
        }
        if ( !$row )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        if ( !\expAuditConsole::channelAllowed( $row['channel'], $allowed ) )
            return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );

        $record = json_decode( $line, true );
        $record = is_array( $record ) ? $record : array();

        if ( isset( $Params['Format'] ) && $Params['Format'] === 'json' )
        {
            \expAuditConsole::recordRead( 'audit/event', array( 'id' => $id ), 1, array( 'format' => 'json' ) );
            \expAudit::flush();
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'Content-Disposition: attachment; filename="audit-' . $id . '.json"' );
            header( 'Cache-Control: private, no-store, max-age=0' );
            header( 'X-Content-Type-Options: nosniff' );
            while ( @ob_end_clean() );
            // no catch( Exception ) around the download and cleanExit(): under Velocity cleanExit() throws
            echo $line, "\n";
            \eZExecution::cleanExit();
            return $this->viewResult( null, null );
        }

        // the chain position: the hash recomputed over the record as written
        $hashOk = null;
        if ( empty( $row['pseudonymised'] ) && empty( $row['imported'] ) && isset( $record['hash'] ) )
        {
            $obj = \expAuditJson::decode( $line );
            $algo = strpos( (string)$record['hash'], ':' ) !== false ? strstr( (string)$record['hash'], ':', true ) : 'sha256';
            $hashOk = $obj !== null && in_array( $algo, hash_algos(), true ) && \expAuditWriter::hashOf( $obj, $algo ) === $record['hash'];
        }
        $fileState = null;
        if ( $query )
        {
            $indexer = new \expAuditIndexer();
            $states = $indexer->fileStates();
            $fileState = isset( $states[$row['channel'] . "\n" . $file] ) ? $states[$row['channel'] . "\n" . $file] : null;
        }

        // related events: parent, children, the same request, the same job
        $related = array( 'parent' => null, 'children' => array(), 'request' => array(), 'job_count' => 0 );
        if ( $query )
        {
            if ( $row['parent_id'] )
            {
                $p = $query->byId( $row['parent_id'] );
                if ( $p && \expAuditConsole::channelAllowed( $p['channel'], $allowed ) )
                    $related['parent'] = \expAuditConsole::view( $p );
            }
            foreach ( (array)$query->fetch( array( 'parent' => $id ), $allowed, 0, 50 ) as $c )
                $related['children'][] = \expAuditConsole::view( $c );
            if ( $row['request_id'] )
                foreach ( (array)$query->fetch( array( 'request' => $row['request_id'] ), $allowed, 0, 50 ) as $c )
                    if ( $c['id'] !== $id )
                        $related['request'][] = \expAuditConsole::view( $c );
            if ( $row['job_id'] )
                $related['job_count'] = (int)$query->count( array( 'job' => $row['job_id'] ), $allowed );
        }
        else
        {
            $reader = new \expAuditReader( $config['logDir'] );
            $rel = $reader->related( $record );
            foreach ( $rel['children'] as $c )
                if ( ( $r = \expAuditIndexRow::fromRecord( $c, json_encode( $c ), '' ) ) && \expAuditConsole::channelAllowed( $r['channel'], $allowed ) )
                    $related['children'][] = \expAuditConsole::view( $r );
            foreach ( $rel['siblings'] as $c )
                if ( ( $r = \expAuditIndexRow::fromRecord( $c, json_encode( $c ), '' ) ) && \expAuditConsole::channelAllowed( $r['channel'], $allowed ) )
                    $related['request'][] = \expAuditConsole::view( $r );
        }

        $before = isset( $record['before'] ) && is_array( $record['before'] ) ? $record['before'] : array();
        $after = isset( $record['after'] ) && is_array( $record['after'] ) ? $record['after'] : array();
        $changes = array();
        foreach ( array_unique( array_merge( array_keys( $before ), array_keys( $after ) ) ) as $k )
        {
            $b = array_key_exists( $k, $before ) ? self::text( $before[$k] ) : null;
            $a = array_key_exists( $k, $after ) ? self::text( $after[$k] ) : null;
            $changes[] = array( 'key' => (string)$k, 'before' => $b, 'after' => $a, 'changed' => $b !== $a );
        }

        $event = \expAuditConsole::view( $row );
        $links = array(
            'object' => \expAuditConsole::linkFor( $row['object_type'], $row['object_id'] ),
            'target' => \expAuditConsole::linkFor( $row['target_type'], $row['target_id'] ),
            'user' => $row['user_id'] ? \expAuditConsole::linkFor( 'user', $row['user_id'] ) : null,
            'job' => $row['job_id'] ? \expAuditConsole::linkFor( 'job', $row['job_id'] ) : null,
        );
        $roles = array();
        if ( isset( $record['actor']['roles'] ) && is_array( $record['actor']['roles'] ) && class_exists( 'eZRole' ) )
            foreach ( $record['actor']['roles'] as $rid )
            {
                $role = ctype_digit( (string)$rid ) ? \eZRole::fetch( (int)$rid ) : null;
                $roles[] = array( 'id' => (int)$rid, 'name' => $role ? $role->attribute( 'name' ) : '#' . $rid );
            }

        \expAuditConsole::recordRead( 'audit/event', array( 'id' => $id ), 1 );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'event', $event );
        $tpl->setVariable( 'record', $record );
        $tpl->setVariable( 'raw', self::pretty( $line ) );
        $tpl->setVariable( 'request', isset( $record['request'] ) && is_array( $record['request'] ) ? array_map( array( __CLASS__, 'text' ), $record['request'] ) : array() );
        $tpl->setVariable( 'actor', isset( $record['actor'] ) && is_array( $record['actor'] ) ? array_map( array( __CLASS__, 'text' ), $record['actor'] ) : array() );
        $tpl->setVariable( 'object_fields', isset( $record['object'] ) && is_array( $record['object'] ) ? array_map( array( __CLASS__, 'text' ), $record['object'] ) : array() );
        $tpl->setVariable( 'target_fields', isset( $record['target'] ) && is_array( $record['target'] ) ? array_map( array( __CLASS__, 'text' ), $record['target'] ) : array() );
        $tpl->setVariable( 'error_fields', isset( $record['error'] ) && is_array( $record['error'] ) ? array_map( array( __CLASS__, 'text' ), $record['error'] ) : array() );
        $tpl->setVariable( 'changes', $changes );
        $tpl->setVariable( 'roles', $roles );
        $tpl->setVariable( 'links', $links );
        $tpl->setVariable( 'related', $related );
        $tpl->setVariable( 'hash_ok', $hashOk === null ? 'n/a' : ( $hashOk ? 'yes' : 'no' ) );
        $tpl->setVariable( 'file_state', $fileState ? $fileState['verified'] : 'unchecked' );
        $tpl->setVariable( 'prev', isset( $record['prev'] ) ? (string)$record['prev'] : '' );
        $tpl->setVariable( 'hash', isset( $record['hash'] ) ? (string)$record['hash'] : '' );
        $tpl->setVariable( 'console_request_url', $row['request_id'] ? \expAuditConsole::url( 'audit/console', array( 'request' => $row['request_id'] ) ) : '' );
        $tpl->setVariable( 'console_job_url', $row['job_id'] ? \expAuditConsole::url( 'audit/console', array( 'job' => $row['job_id'] ) ) : '' );

        return \expAuditConsole::result( $tpl->fetch( 'design:audit/event.tpl' ), \ezpI18n::tr( 'design/admin/audit', 'Event' ),
                                         array( array( 'text' => $id, 'url' => false ) ) );
    }

    /**
     * A value as text: scalars as they are, lists and objects as compact JSON.
     *
     * @param mixed $v
     * @return string
     */
    public static function text( $v )
    {
        if ( $v === null )
            return '';
        if ( is_bool( $v ) )
            return $v ? 'true' : 'false';
        if ( is_array( $v ) )
            return json_encode( $v, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        return (string)$v;
    }

    /** @return string the record indented, for reading */
    protected static function pretty( $line )
    {
        $v = json_decode( (string)$line );
        return $v === null ? (string)$line : json_encode( $v, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    }
}

}
