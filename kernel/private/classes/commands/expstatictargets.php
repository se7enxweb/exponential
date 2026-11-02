<?php
/**
 * @description List the targets of static publishing (staticcache.ini [Target_<id>]) with their siteaccess, links and folder, and check each one
 * @alias exp:static:targets
 *
 * File containing the exp:static:targets command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expstatictargets extends ExpstaticBase
{
    protected function name()
    {
        return 'exp:static:targets';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "The targets of static publishing\n" .
                             "Every target of staticcache.ini [StaticPublishSettings] TargetList[] with its siteaccess, how its links\n" .
                             "are written, its public address and its folder, and what is wrong with its settings. Changes nothing;\n" .
                             "exit 1 when a target has an error.\n" .
                             "\n" .
                             "./console exp:static:targets\n" .
                             "./console exp:static:targets --format=json\n" .
                             "./console exp:static:targets --export=all > migrations/20261101120000_static_targets.ini\n" .
                             "\n" .
                             "--export prints targets as an INI migration (also for a site package): their [Target_<id>] blocks and\n" .
                             "TargetList[]. Credentials travel as their secret names, never their values.",
        ) + self::SCRIPT );
        return (array)$this->startup( '[format:][export:]', '', array(
            'format' => 'text (default) or json (the machine output exp-static/1)',
            'export' => 'print these targets (ids separated by commas, or all) as an INI migration and change nothing',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        if ( isset( $options['export'] ) && $options['export'] )
        {
            $wanted = array_filter( array_map( 'trim', explode( ',', (string)$options['export'] ) ), 'strlen' );
            $targets = array();
            foreach ( \expStaticTarget::all() as $id => $target )
                if ( in_array( 'all', $wanted, true ) || in_array( (string)$id, $wanted, true ) )
                    $targets[(string)$id] = $target;
            foreach ( $wanted as $id )
                if ( $id !== 'all' && !isset( $targets[$id] ) )
                    $this->usage( "--export: no target $id in staticcache.ini [StaticPublishSettings] TargetList[]" );
            if ( !$targets )
                $this->usage( '--export: there is no target to export' );
            $this->cli()->output( rtrim( \expStaticTarget::migrationIni( array_values( $targets ) ) ) );
            return;
        }
        $format = $this->format( $options );
        $context = \expStaticTarget::siteAccessContext();
        $ini = \eZINI::instance( 'staticcache.ini' );
        $workDir = $ini->hasVariable( 'StaticPublishSettings', 'WorkDir' ) ? (string)$ini->variable( 'StaticPublishSettings', 'WorkDir' ) : 'static-publish';
        $report = array( 'mode' => 'targets', 'enabled' => \expStaticTarget::enabled(), 'targets' => array(), 'errors' => array(), 'warnings' => array() );
        foreach ( \expStaticTarget::all() as $id => $target )
        {
            $check = $target->validate( $context );
            $directory = $target->outputDirectory( \eZSys::varDirectory(), $workDir );
            $manifest = \expStaticManifest::load( $directory );
            $meta = $manifest !== null ? $manifest->meta() : array();
            $report['targets'][] = $target->toArray() + array(
                'siteaccesses' => \expStaticAdmin::memberList( $target, $context ),
                'directory' => $directory,
                'built_at' => isset( $meta['built_at'] ) ? $meta['built_at'] : null,
                'pages' => $manifest !== null ? $manifest->count( 'page' ) : 0,
                'bytes' => $manifest !== null ? $manifest->bytes() : 0,
                'errors' => $check['errors'],
                'warnings' => $check['warnings'],
            );
            foreach ( $check['errors'] as $error )
                $report['errors'][] = "$id: $error";
        }
        if ( !$report['enabled'] )
            $report['warnings'][] = 'Static publishing is switched off (staticcache.ini [StaticPublishSettings] Enabled=disabled): nothing is built.';
        if ( !$report['targets'] )
            $report['warnings'][] = 'No target: staticcache.ini [StaticPublishSettings] TargetList[] is empty.';
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        $lines = array( 'Static publishing: ' . ( $report['enabled'] ? 'switched on' : 'switched off' ) . ', ' . count( $report['targets'] ) . ' target(s)' );
        foreach ( $report['targets'] as $t )
        {
            $lines[] = '';
            $lines[] = sprintf( '  %-20s %s', $t['id'], $t['name'] );
            $siteaccesses = array();
            foreach ( $t['siteaccesses'] as $sa )
                $siteaccesses[] = $sa['name'] . ( count( $t['siteaccesses'] ) > 1 ? ' ' . $sa['folder'] : '' );
            $lines[] = sprintf( '    %s %s, %s, links %s%s', count( $siteaccesses ) > 1 ? 'siteaccesses' : 'siteaccess',
                                $siteaccesses ? implode( ', ', $siteaccesses ) : '(none)', $t['Kind'], $t['Links'],
                                $t['Links'] === 'base-path' ? ' below ' . \expStaticLinkRewriter::normaliseBasePath( $t['BasePath'] ) : '' );
            if ( $t['PublicURL'] !== '' || $t['CustomDomain'] !== '' )
                $lines[] = '    public at ' . ( $t['PublicURL'] !== '' ? $t['PublicURL'] : 'https://' . $t['CustomDomain'] . '/' );
            $lines[] = '    folder ' . $t['directory'] . ( $t['built_at'] ? sprintf( ', built %s, %d pages, %s', $t['built_at'], $t['pages'], \expStaticBuild::bytes( $t['bytes'] ) ) : ', not built yet' );
            foreach ( $t['errors'] as $e )
                $lines[] = '    ERROR ' . $e;
            foreach ( $t['warnings'] as $w )
                $lines[] = '    warn  ' . $w;
        }
        return $lines;
    }
}

}
