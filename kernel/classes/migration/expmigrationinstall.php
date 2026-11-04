<?php
/**
 * File containing the expMigrationInstall class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Migrations run at installation (migration.ini [InstallSettings]):
 *
 *  - a package that ships a directory migrations/ (next to its package.xml or package.json; an exported package
 *    carries it, eZPackage::exportToArchive() copies every top-level directory) has them run when it is installed,
 *    after its items: eZPackage::install() calls afterPackageInstall(). Only that package's migrations run
 *    (--path=<its migrations directory>); one that requires a migration not yet applied elsewhere stops the run.
 *  - the kickstarter runs every pending migration of the new installation after the sites are created
 *    (afterKickstart()).
 *
 * Both run ./console exp:migration:migrate --apply --format=json in a process of its own (expMigrationProcess, no
 * shell): the run takes the installation lock, its captions and its record as any other, and a web request that
 * installs a package does not mix the run's state (user, transactions, caches) with its own. The options are
 * [InstallSettings] RunOptions[] (by default include-uncommitted, as installing writes settings and design files, and
 * no-push). A package installed from the administration runs its migrations as the signed-in user.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationInstall
{
    /** The command a run at installation starts. */
    const SCRIPT = 'bin/php/expmigrationmigrate.php';

    /** The options of exp:migration:migrate RunOptions[] may name. */
    const RUN_OPTIONS = array( 'no-caption', 'no-restore', 'no-commit', 'no-push', 'no-tag', 'include-uncommitted' );

    /**
     * [InstallSettings] of migration.ini, with the defaults of a missing setting.
     *
     * @param eZINI|null $ini
     * @return array package (bool), kickstart (bool), options (string[] of RUN_OPTIONS), timeout (seconds)
     */
    public static function settings( $ini = null )
    {
        $ini = $ini ?: eZINI::instance( 'migration.ini' );
        $get = function ( $name, $default ) use ( $ini ) {
            return $ini->hasVariable( 'InstallSettings', $name ) ? $ini->variable( 'InstallSettings', $name ) : $default;
        };
        $options = array();
        foreach ( (array)$get( 'RunOptions', array( 'include-uncommitted', 'no-push' ) ) as $option )
        {
            $option = trim( (string)$option );
            if ( in_array( $option, self::RUN_OPTIONS, true ) && !in_array( $option, $options, true ) )
                $options[] = $option;
        }
        $timeout = (int)$get( 'Timeout', 3600 );
        return array( 'package' => $get( 'RunOnPackageInstall', 'enabled' ) !== 'disabled',
                      'kickstart' => $get( 'RunOnKickstart', 'enabled' ) !== 'disabled',
                      'options' => $options,
                      'timeout' => $timeout > 0 ? $timeout : 3600 );
    }

    /**
     * The migrations directory of a package when it holds a migration file (YYYYMMDDHHMMSS_<name>.<type>), else ''.
     *
     * @param string $packagePath the package's directory
     * @param eZINI|null $ini migration.ini ([MigrationSettings] ExtensionDirectory names the directory)
     * @return string
     */
    public static function packageDirectory( $packagePath, $ini = null )
    {
        $ini = $ini ?: eZINI::instance( 'migration.ini' );
        $sub = $ini->hasVariable( 'MigrationSettings', 'ExtensionDirectory' ) ? trim( (string)$ini->variable( 'MigrationSettings', 'ExtensionDirectory' ), '/' ) : 'migrations';
        $directory = rtrim( (string)$packagePath, '/' ) . '/' . ( $sub !== '' ? $sub : 'migrations' );
        if ( !is_dir( $directory ) )
            return '';
        foreach ( (array)scandir( $directory ) as $file )
            if ( is_string( $file ) && preg_match( '/^\d{14}_/', $file ) && is_file( $directory . '/' . $file ) )
                return $directory;
        return '';
    }

    /**
     * The arguments of a run at installation, without the php binary and the script. Pure.
     *
     * @param string[] $paths --path (empty: every pending migration)
     * @param string[] $options RUN_OPTIONS to pass on
     * @param string $login '' for [RunSettings] RunAsUser
     * @param string $siteaccess '' for the default one
     * @param bool $allowRoot whether this process runs as root (the command then needs --allow-root-user)
     * @return string[]
     */
    public static function arguments( array $paths, array $options, $login = '', $siteaccess = '', $allowRoot = false )
    {
        $arguments = array( '--apply', '--format=json' );
        foreach ( $paths as $path )
            $arguments[] = '--path=' . $path;
        foreach ( $options as $option )
            if ( in_array( $option, self::RUN_OPTIONS, true ) )
                $arguments[] = '--' . $option;
        if ( (string)$login !== '' )
            $arguments[] = '--login=' . $login;
        if ( (string)$siteaccess !== '' )
            $arguments[] = '--siteaccess=' . $siteaccess;
        if ( $allowRoot )
            $arguments[] = '--allow-root-user';
        return $arguments;
    }

    /**
     * Runs exp:migration:migrate --apply in a process of its own.
     *
     * @param string[] $paths --path (empty: every pending migration)
     * @param array $settings settings() (default migration.ini's), login, siteaccess, root
     * @return array ok (bool), exit (int), report (the exp-migration/1 report, null when it printed none), message
     *               (one line: what ran, or why it failed), arguments
     */
    public static function run( array $paths, array $settings = array() )
    {
        $settings += self::settings();
        $root = isset( $settings['root'] ) ? (string)$settings['root'] : expMigrationProcess::root();
        $arguments = self::arguments( $paths, $settings['options'], isset( $settings['login'] ) ? (string)$settings['login'] : '',
                                      isset( $settings['siteaccess'] ) ? (string)$settings['siteaccess'] : '',
                                      function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 );
        list( $exit, $output ) = expMigrationProcess::run( self::SCRIPT, $arguments, $root, (int)$settings['timeout'] );
        self::forgetChangedDefinitions();
        $report = expMigrationJob::report( $output );
        if ( is_array( $report ) && isset( $report['exit'] ) )
            $exit = (int)$report['exit'];
        return array( 'ok' => $exit === 0, 'exit' => (int)$exit, 'report' => $report,
                      'message' => self::message( $exit, $report, $output ), 'arguments' => $arguments );
    }

    /**
     * Forgets what this process has kept of the definitions a run in its own process may have changed. The object
     * states a new object gets (eZContentObjectState::defaults()) are kept for the whole process: an installer that
     * created objects before the run (and so read them) gave the objects it created after it the states of before,
     * without the state groups the run added (the review hold of 6.0.15-review-hold, on 56 help manual objects of a
     * fresh installation).
     */
    public static function forgetChangedDefinitions()
    {
        if ( class_exists( 'eZContentObjectState' ) )
            eZContentObjectState::cleanDefaultsCache();
    }

    /**
     * One line about a run: what it applied, or why it failed. Pure.
     *
     * @param int $exit
     * @param array|null $report
     * @param string $output what the command printed (for a run without a report)
     * @return string
     */
    public static function message( $exit, $report, $output = '' )
    {
        if ( !is_array( $report ) )
            return 'the migration run printed no report (exit ' . (int)$exit . '): ' . expMigrationProcess::tail( $output, 3 );
        $summary = isset( $report['summary'] ) && is_array( $report['summary'] ) ? $report['summary'] : array();
        $applied = isset( $summary['applied'] ) ? (int)$summary['applied'] : 0;
        $failed = isset( $summary['failed'] ) ? (int)$summary['failed'] : 0;
        $text = $applied === 1 ? '1 migration applied' : "$applied migrations applied";
        if ( $failed > 0 )
            $text .= ", $failed failed";
        if ( isset( $report['run_id'] ) && $report['run_id'] !== '' && ( $applied > 0 || $failed > 0 ) )
            $text .= ' (run ' . $report['run_id'] . ')';
        if ( (int)$exit !== 0 )
        {
            $errors = isset( $report['errors'] ) && is_array( $report['errors'] ) ? $report['errors'] : array();
            $text .= ': ' . ( $errors ? implode( '; ', array_slice( array_map( 'strval', $errors ), 0, 3 ) ) : 'exit ' . (int)$exit );
        }
        return $text;
    }

    /**
     * After the items of a package are installed (eZPackage::install()): runs the package's migrations.
     *
     * @param string $packagePath the package's directory
     * @param string $packageName
     * @param array $installParameters eZPackage::install()'s; migration-report is set to the report
     * @param array $settings as run() (tests)
     * @return bool false when the run failed (the package then counts as not installed)
     */
    public static function afterPackageInstall( $packagePath, $packageName, array &$installParameters, array $settings = array() )
    {
        $settings += self::settings();
        if ( !$settings['package'] )
            return true;
        $directory = self::packageDirectory( $packagePath );
        if ( $directory === '' )
            return true;
        if ( !isset( $settings['login'] ) && !eZSys::isShellExecution() )
        {
            $user = eZUser::currentUser();
            $settings['login'] = $user && $user->isRegistered() ? (string)$user->attribute( 'login' ) : '';
        }
        if ( !isset( $settings['siteaccess'] ) )
            $settings['siteaccess'] = expMigrationJob::currentSiteaccess();
        $result = self::run( array( $directory ), $settings );
        $installParameters['migration-report'] = $result['report'];
        if ( $result['ok'] )
            eZDebug::writeNotice( "Migrations of the package $packageName: " . $result['message'], __METHOD__ );
        else
            eZDebug::writeError( "Migrations of the package $packageName failed: " . $result['message'], __METHOD__ );
        return $result['ok'];
    }

    /**
     * After the kickstarter created the sites: runs every pending migration.
     *
     * @param array $settings as run() (tests)
     * @return array|null run()'s result; null when [InstallSettings] RunOnKickstart=disabled
     */
    public static function afterKickstart( array $settings = array() )
    {
        $settings += self::settings();
        if ( !$settings['kickstart'] )
            return null;
        return self::run( array(), $settings );
    }
}
