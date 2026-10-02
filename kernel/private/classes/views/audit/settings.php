<?php
/**
 * The audit/settings view (doc/bc/6.0/audit.md, "Views", "The settings reference"): the effective audit.ini of this
 * siteaccess, every value with the file it comes from (default, override, siteaccess, extension), secrets masked;
 * the keys as ids and fingerprints only; the sinks and the archive format handlers with what keeps them from working
 * here; and the state of the index (tables, full-text search, rows, lag). Read-only: settings are written with
 * exp:ini (./console exp:ini set audit.ini/<Block>/<Variable> <value> <scope>), which records the write.
 * Policy audit/manage. Recorded as system.audit.read.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Audit
{

class Settings extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !class_exists( 'expAuditConsole' ) || !\expAuditConsole::available() )
            return $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
        $config = \expAuditConfig::get();

        // the effective values with their origin, from a private instance (placements reload the file)
        $ini = new \eZINI( 'audit.ini', 'settings', null, null, true );
        $ini->setOverrideDirs( \eZINI::instance()->overrideDirs( false ) );
        $ini->load();
        $placements = $ini->groupPlacements();
        $blocks = array();
        foreach ( $ini->groups() as $block => $vars )
        {
            $rows = array();
            foreach ( $vars as $var => $value )
            {
                $secret = class_exists( 'expIniEditor' ) && \expIniEditor::isSecret( $var );
                $origin = array();
                $p = isset( $placements[$block][$var] ) ? $placements[$block][$var] : null;
                foreach ( is_array( $p ) ? $p : array( $p ) as $path )
                    if ( is_string( $path ) && $path !== '' )
                        $origin[\expAudit::relativePath( $path )] = $ini->findSettingPlacement( $path );
                if ( is_array( $value ) )
                {
                    $items = array();
                    foreach ( $value as $k => $v )
                        $items[] = ( is_int( $k ) ? '' : $k . ' = ' ) . ( $secret ? '[secret]' : (string)$v );
                    $text = $items ? implode( "\n", $items ) : '(empty list)';
                }
                else
                    $text = $secret ? ( (string)$value === '' ? '' : '[secret]' ) : (string)$value;
                $originText = array();
                foreach ( $origin as $file => $kind )
                    $originText[] = $kind . ': ' . $file;
                $rows[] = array( 'variable' => $var, 'value' => $text, 'is_list' => is_array( $value ), 'secret' => $secret,
                                 'origin' => implode( ', ', array_unique( $originText ) ),
                                 'overridden' => (bool)array_filter( $origin, function ( $k ) { return $k !== 'default'; } ) );
            }
            $blocks[] = array( 'name' => $block, 'rows' => $rows );
        }

        // keys: ids and fingerprints only
        $keys = new \expAuditKeys( $config );
        $keyRows = array();
        try
        {
            $all = $keys->keys( false );
            $active = $keys->activeKeyId();
            foreach ( isset( $all['signing'] ) ? (array)$all['signing'] : array() as $id => $raw )
                $keyRows[] = array( 'kind' => 'signing', 'id' => $id, 'fingerprint' => \expAuditKeys::fingerprint( $raw ), 'active' => $id === $active );
            if ( !empty( $all['pseudonym'] ) )
                $keyRows[] = array( 'kind' => 'pseudonym', 'id' => '', 'fingerprint' => \expAuditKeys::fingerprint( $all['pseudonym'] ), 'active' => true );
            $installation = (string)$keys->installationId();
        }
        catch ( \Throwable $e )
        {
            $installation = '';
        }

        // sinks and format handlers (their classes come with the sinks and archives of the audit)
        $sinks = array();
        if ( class_exists( 'expAuditSinkRegistry' ) )
            foreach ( \expAuditSinkRegistry::classes() as $name => $class )
            {
                $sink = \expAuditSinkRegistry::get( $name );
                $sinks[] = array( 'name' => $name, 'class' => $class,
                                  'problem' => $sink ? (string)$sink->problem() : "the class $class does not exist or does not implement expAuditSink" );
            }
        $formats = array();
        if ( class_exists( 'expAuditFormatRegistry' ) )
            foreach ( \expAuditFormatRegistry::status() as $name => $s )
                $formats[] = array( 'name' => $name, 'class' => $s['class'], 'extension' => $s['extension'], 'problem' => $s['problem'] );

        // the index
        $indexSettings = \expAuditIndexSettings::get();
        $index = array( 'enabled' => $indexSettings['index'], 'installed' => false, 'fulltext' => '', 'rows' => 0, 'lag' => 0,
                        'updated' => '', 'engine' => \expAuditIndexSchema::type() );
        if ( \expAuditIndexSchema::isInstalled() )
        {
            $index['installed'] = true;
            $index['fulltext'] = \expAuditIndexSchema::fullTextKind();
            $q = new \expAuditQuery();
            $index['rows'] = (int)$q->count( array(), null );
            $indexer = new \expAuditIndexer();
            $index['lag'] = $indexer->lag()['bytes'];
            $last = 0;
            foreach ( $indexer->cursors() as $c )
                $last = max( $last, (int)$c['updated_ms'] );
            $index['updated'] = $last ? date( 'Y-m-d H:i:s', (int)( $last / 1000 ) ) : '';
        }

        \expAuditConsole::recordRead( 'audit/settings', array(), count( $blocks ) );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'blocks', $blocks );
        $tpl->setVariable( 'key_rows', $keyRows );
        $tpl->setVariable( 'installation', $installation );
        $tpl->setVariable( 'sinks', $sinks );
        $tpl->setVariable( 'formats', $formats );
        $tpl->setVariable( 'index', $index );
        $tpl->setVariable( 'audit_enabled', $config['enabled'] );
        $tpl->setVariable( 'log_dir', \expAudit::relativePath( $config['logDir'] ) );
        $tpl->setVariable( 'index_settings', $indexSettings );
        return \expAuditConsole::result( $tpl->fetch( 'design:audit/settings.tpl' ), \ezpI18n::tr( 'design/admin/audit', 'Settings' ) );
    }
}

}
