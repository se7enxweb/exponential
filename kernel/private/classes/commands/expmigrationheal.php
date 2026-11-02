<?php
/**
 * @description Self-healing before a migration run: a stale run lock, missing migration folders, file modes, caches (what a deploy may fix on a remote)
 * @alias exp:migration:heal
 *
 * File containing the exp:migration:heal command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationheal extends ExpmigrationBase
{
    protected function name()
    {
        return 'exp:migration:heal';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Self-healing before a migration run\n" .
                             "The fixes a deploy may make on a remote before it runs the migrations there ([RemoteSettings] SelfHeal[]\n" .
                             "of the source), each only when named: stale-lock (a run lock whose process is gone and that is older than\n" .
                             "--stale-lock-age, held on this host by this user), paths (the missing migration folders), modes (the\n" .
                             "permissions of [FileSettings] on the migration folders and var/<site>/migration, only added), caches (ini\n" .
                             "and template-override). What cannot be fixed safely is only reported. Without --apply it lists what it\n" .
                             "would do.\n" .
                             "\n" .
                             "./console exp:migration:heal --fix=stale-lock,paths,modes\n" .
                             "./console exp:migration:heal --fix=stale-lock --stale-lock-age=3600 --apply --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[fix:*][stale-lock-age:][apply][dry-run][format:]', '', array(
            'fix' => 'The fixes: stale-lock, paths, modes, caches (a comma list or repeated)',
            'stale-lock-age' => 'Seconds from which a held run lock whose process is gone counts as stale (default 7200)',
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = $this->apply( $options );
        $unknown = array();
        $fixes = \expMigrationHeal::normalize( isset( $options['fix'] ) && $options['fix'] ? (array)$options['fix'] : array(), $unknown );
        if ( $unknown )
            $this->usage( '--fix takes ' . implode( ', ', \expMigrationHeal::FIXES ) . ', not ' . implode( ', ', $unknown ) );
        if ( !$fixes )
            $this->usage( '--fix names no fix: ' . implode( ', ', \expMigrationHeal::FIXES ) );
        $age = \expMigrationHeal::STALE_LOCK_AGE;
        if ( isset( $options['stale-lock-age'] ) && $options['stale-lock-age'] !== false )
        {
            if ( !ctype_digit( (string)$options['stale-lock-age'] ) )
                $this->usage( '--stale-lock-age must be a number of seconds' );
            $age = (int)$options['stale-lock-age'];
        }
        $heal = new \expMigrationHeal( array( 'apply' => $apply, 'stale_lock_age' => $age ) );
        $report = $heal->run( $fixes );
        unset( $report['schema'] );
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        $lines = array( 'Exponential migrations: self-healing' . ( $report['apply'] ? '' : ' (dry run: nothing changed)' ) );
        foreach ( $report['fixes'] as $fix )
            $lines[] = '  fixed   ' . $fix;
        foreach ( $report['problems'] as $problem )
            $lines[] = '  left    ' . $problem;
        if ( !$report['fixes'] && !$report['problems'] )
            $lines[] = '  nothing to fix';
        return $lines;
    }
}

}
