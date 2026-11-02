<?php
/**
 * File containing the expMigrationRemoteAdmin class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The remotes and deploys of Setup > Migrations (the views migration/remotes, migration/remote, migration/deploy and
 * migration/deployment): the wizard's settings read from the form and checked, the lines written to
 * settings/override/migration.ini.append.php through expIniEditor (backup, atomic write, audited as
 * system.migration.remote), "Test connection" (the remote's migration status read once, or for FTP one file read back),
 * the deploy form read into the options of a deploy job (expMigrationJob kind deploy), and what the pages show of a
 * deploy. A secret is never written: the credential is a name (sshkey:<secret name>, password:<secret name>, either
 * with env:VARIABLE) or none.
 *
 * Policies: migration/read (see the remotes and the deploys), migration/remotes (add, change and remove remotes, test
 * a connection), migration/deploy (start a deploy, approve the gate of a staged one, roll one back).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationRemoteAdmin
{
    /** The settings of a [Remote_<id>] section the wizard writes, in this order */
    const WIZARD_SETTINGS = array( 'Environment', 'Transport', 'Host', 'Port', 'User', 'Path', 'Php', 'Credential', 'Groups', 'HostKeyFingerprint', 'AllowRootUser' );

    /** The kinds of credential the wizard offers */
    const CREDENTIAL_KINDS = array( 'none', 'sshkey', 'password' );

    /** The environments the wizard offers (others can be typed) */
    const ENVIRONMENTS = array( 'alpha', 'dev', 'test', 'stage', 'beta', 'production', 'post', 'arch' );

    /** The seconds "Test connection" waits */
    const TEST_TIMEOUT = 30;

    /**
     * The settings of a new remote. Pure.
     *
     * @return array
     */
    public static function newSettings()
    {
        return array( 'Environment' => 'production', 'Transport' => 'rsync', 'Host' => '', 'Port' => '', 'User' => '', 'Path' => '', 'Php' => 'php',
                      'Credential' => 'none', 'Groups' => array(), 'HostKeyFingerprint' => '', 'AllowRootUser' => 'disabled' );
    }

    /**
     * The settings of a remote as the wizard shows them. Pure.
     *
     * @param expMigrationRemote $remote
     * @return array
     */
    public static function settingsOf( expMigrationRemote $remote )
    {
        $settings = self::newSettings();
        foreach ( self::WIZARD_SETTINGS as $name )
            $settings[$name] = $name === 'Groups' ? $remote->groups() : (string)$remote->setting( $name, $name === 'AllowRootUser' ? 'disabled' : '' );
        if ( $settings['Transport'] === '' )
            $settings['Transport'] = 'ssh';
        if ( $settings['Credential'] === '' )
            $settings['Credential'] = 'none';
        return $settings;
    }

    /**
     * The wizard's settings from a POST (Remote[<setting>], RemoteCredentialKind, RemoteCredentialName,
     * RemoteCredentialEnv, RemoteGroups as a comma list). Pure.
     *
     * @param array $post
     * @return array the settings of WIZARD_SETTINGS
     */
    public static function settingsFromPost( array $post )
    {
        $in = isset( $post['Remote'] ) && is_array( $post['Remote'] ) ? $post['Remote'] : array();
        $text = function ( $name ) use ( $in ) {
            return isset( $in[$name] ) && is_scalar( $in[$name] ) ? trim( (string)$in[$name] ) : '';
        };
        $settings = self::newSettings();
        foreach ( array( 'Environment', 'Transport', 'Host', 'Port', 'User', 'Path', 'Php', 'HostKeyFingerprint' ) as $name )
            $settings[$name] = $text( $name );
        $settings['Transport'] = strtolower( $settings['Transport'] );
        $settings['AllowRootUser'] = $text( 'AllowRootUser' ) === 'enabled' ? 'enabled' : 'disabled';
        $groups = array();
        foreach ( explode( ',', isset( $post['RemoteGroups'] ) && is_scalar( $post['RemoteGroups'] ) ? (string)$post['RemoteGroups'] : '' ) as $group )
            if ( ( $group = trim( $group ) ) !== '' && !in_array( $group, $groups, true ) )
                $groups[] = $group;
        $settings['Groups'] = $groups;
        $kind = isset( $post['RemoteCredentialKind'] ) && in_array( $post['RemoteCredentialKind'], self::CREDENTIAL_KINDS, true ) ? $post['RemoteCredentialKind'] : 'none';
        $env = isset( $post['RemoteCredentialEnv'] ) && is_scalar( $post['RemoteCredentialEnv'] ) ? trim( (string)$post['RemoteCredentialEnv'] ) : '';
        $name = isset( $post['RemoteCredentialName'] ) && is_scalar( $post['RemoteCredentialName'] ) ? trim( (string)$post['RemoteCredentialName'] ) : '';
        $settings['Credential'] = $kind === 'none' ? 'none' : $kind . ':' . ( $env !== '' ? 'env:' . $env : $name );
        return $settings;
    }

    /**
     * The credential as the wizard's three fields. Pure.
     *
     * @param string $credential
     * @return array kind (none, sshkey, password), name (the secret's), env (the variable's)
     */
    public static function credentialChoice( $credential )
    {
        $parsed = expMigrationRemoteCredential::parse( (string)$credential );
        $kind = in_array( $parsed[0], self::CREDENTIAL_KINDS, true ) ? $parsed[0] : 'none';
        $source = isset( $parsed[1] ) ? (string)$parsed[1] : '';
        $isEnv = isset( $parsed[2] ) ? (bool)$parsed[2] : strpos( $source, 'env:' ) === 0;
        if ( strpos( $source, 'env:' ) === 0 )
            $source = substr( $source, 4 );
        return array( 'kind' => $kind, 'name' => $isEnv ? '' : $source, 'env' => $isEnv ? $source : '' );
    }

    /**
     * Checks the wizard's settings.
     *
     * @param string $id
     * @param array $settings
     * @param string[] $existing the ids of RemoteList[]
     * @param bool $isNew
     * @param callable|null $lookup the credential lookup (tests)
     * @return array errors, warnings (texts)
     */
    public static function check( $id, array $settings, array $existing, $isNew, $lookup = null )
    {
        $errors = array();
        $warnings = array();
        if ( $isNew && in_array( (string)$id, $existing, true ) )
            $errors[] = self::tr( 'A remote with the id %id exists already.', array( '%id' => $id ) );
        if ( $settings['Port'] !== '' && !ctype_digit( (string)$settings['Port'] ) )
            $errors[] = self::tr( 'The port is a number from 1 to 65535.' );
        if ( !preg_match( '/^[A-Za-z0-9][A-Za-z0-9_-]{0,49}$/', (string)$settings['Environment'] ) )
            $errors[] = self::tr( 'The environment is a word of letters, digits, - and _ (alpha, dev, stage, beta, production ...).' );
        foreach ( $settings['Groups'] as $group )
            if ( !preg_match( '/^[A-Za-z0-9][A-Za-z0-9_-]{0,63}$/', $group ) || $group === 'all' )
                $errors[] = self::tr( 'A group is a word of letters, digits, - and _ (not all): %group', array( '%group' => $group ) );
        $remote = new expMigrationRemote( (string)$id, self::iniSettings( $settings ) );
        foreach ( $remote->validate() as $problem )
            $errors[] = $problem;
        if ( !$errors && $settings['Credential'] !== 'none' )
        {
            $credential = new expMigrationRemoteCredential( $settings['Credential'], array( 'work_dir' => expMigrationRemoteDeploy::workDirectory(), 'lookup' => $lookup ) );
            if ( ( $why = $credential->problem() ) !== '' )
                $warnings[] = self::tr( 'The credential cannot be read now: %problem. Save it in Setup > Secrets (or set the variable) before the first deploy.', array( '%problem' => $why ) );
            $credential->cleanup();
        }
        if ( !$errors && in_array( $settings['Transport'], array( 'ftp', 'ftps' ), true ) )
            $warnings[] = self::tr( 'An FTP remote only receives files: a deploy to it is "Transfer only", and its migrations are run there by hand.' );
        return array( 'errors' => array_values( array_unique( $errors ) ), 'warnings' => $warnings );
    }

    /**
     * The settings as the INI section holds them: empty ones left out (their defaults apply). Pure.
     *
     * @param array $settings
     * @return array
     */
    public static function iniSettings( array $settings )
    {
        $ini = array();
        foreach ( self::WIZARD_SETTINGS as $name )
        {
            if ( !array_key_exists( $name, $settings ) )
                continue;
            if ( $name === 'Groups' )
            {
                if ( $settings['Groups'] )
                    $ini['Groups'] = array_values( (array)$settings['Groups'] );
                continue;
            }
            if ( (string)$settings[$name] !== '' && !( $name === 'AllowRootUser' && $settings[$name] === 'disabled' ) )
                $ini[$name] = (string)$settings[$name];
        }
        return $ini;
    }

    /**
     * The editor of settings/override/migration.ini.append.php with a remote written: its [Remote_<id>] section
     * (an empty setting removed) and its id in [RemoteSettings] RemoteList[].
     *
     * @param string $id
     * @param array $settings
     * @param expIniScope|null $scope another scope (tests)
     * @return expIniEditor
     */
    public static function editor( $id, array $settings, $scope = null )
    {
        $editor = new expIniEditor( $scope ?: expIniEditor::scope( 'global' ), 'migration' );
        $block = 'Remote_' . $id;
        $ini = self::iniSettings( $settings );
        foreach ( self::WIZARD_SETTINGS as $name )
        {
            if ( $name === 'Groups' )
            {
                if ( isset( $ini['Groups'] ) || $editor->get( $block, 'Groups' ) !== null )
                {
                    $editor->clearArray( $block, 'Groups' );
                    foreach ( isset( $ini['Groups'] ) ? $ini['Groups'] : array() as $group )
                        $editor->add( $block, 'Groups', $group );
                }
                continue;
            }
            if ( isset( $ini[$name] ) )
                $editor->set( $block, $name, $ini[$name] );
            else if ( $editor->get( $block, $name ) !== null )
                $editor->remove( $block, $name );
        }
        $listed = $editor->get( 'RemoteSettings', 'RemoteList' );
        if ( !in_array( (string)$id, self::listed( $listed ), true ) && !in_array( (string)$id, array_keys( expMigrationRemote::all() ), true ) )
            $editor->add( 'RemoteSettings', 'RemoteList', (string)$id );
        return $editor;
    }

    /** @return string[] the values of an INI list as the editor returns it */
    protected static function listed( $value )
    {
        if ( $value === null )
            return array();
        return array_map( 'strval', is_array( $value ) ? array_values( $value ) : array( $value ) );
    }

    /**
     * Writes a remote, audited as system.migration.remote.
     *
     * @param string $id
     * @param array $settings
     * @param bool $isNew
     * @return string '' when written, else why not
     */
    public static function save( $id, array $settings, $isNew )
    {
        try
        {
            $editor = self::editor( $id, $settings );
            if ( $editor->hasChanges() )
                $editor->save( array( 'create' => true ) );
        }
        catch ( Throwable $e )
        {
            self::audit( $id, $isNew ? 'create' : 'update', 'failure', array( 'reason' => $e->getMessage() ) );
            return $e->getMessage();
        }
        self::settingsChanged();
        self::audit( $id, $isNew ? 'create' : 'update', 'success', array( 'x' => array( 'environment' => $settings['Environment'], 'transport' => $settings['Transport'],
                                                                                       'host' => $settings['Host'], 'credential' => $settings['Credential'],
                                                                                       'groups' => $settings['Groups'] ) ) );
        return '';
    }

    /**
     * Removes a remote: its section and its id in RemoteList[] of the override. A remote set in another settings
     * file stays (the answer says so).
     *
     * @param string $id
     * @param expIniScope|null $scope
     * @return string '' when removed, else why not
     */
    public static function remove( $id, $scope = null )
    {
        try
        {
            $editor = new expIniEditor( $scope ?: expIniEditor::scope( 'global' ), 'migration' );
            if ( in_array( (string)$id, self::listed( $editor->get( 'RemoteSettings', 'RemoteList' ) ), true ) )
                $editor->remove( 'RemoteSettings', 'RemoteList', (string)$id );
            if ( in_array( 'Remote_' . $id, $editor->blocks(), true ) )
                $editor->removeBlock( 'Remote_' . $id );
            if ( !$editor->hasChanges() )
                return self::tr( 'The remote %id is not in settings/override/migration.ini.append.php; remove it where it is set.', array( '%id' => $id ) );
            $editor->save( array( 'create' => false ) );
        }
        catch ( Throwable $e )
        {
            self::audit( $id, 'delete', 'failure', array( 'reason' => $e->getMessage() ) );
            return $e->getMessage();
        }
        self::settingsChanged();
        self::audit( $id, 'delete', 'success' );
        return '';
    }

    /** The INI cache and this request's instances forget migration.ini. */
    protected static function settingsChanged()
    {
        try
        {
            if ( class_exists( 'eZCache' ) )
                eZCache::clearByTag( 'ini' );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( 'INI cache: ' . $e->getMessage(), __METHOD__ );
        }
        eZINI::resetInstance( 'migration.ini' );
    }

    /** The audit event of a remote's settings (never a secret: the credential is a name). */
    protected static function audit( $id, $verb, $result, array $data = array() )
    {
        expUpgrade::audit( 'system.migration.remote', $data + array( 'object' => array( 'type' => 'migration_remote', 'id' => (string)$id ), 'verb' => $verb, 'result' => $result ) );
    }

    /**
     * "Test connection": one remote reached once, nothing changed. A remote that runs commands has its migration
     * status read (the preflight's first step: reachable, version, lock, done, pending, failed); an FTP remote has its
     * index.php read back.
     *
     * @param expMigrationRemote $remote
     * @param array $options timeout, lookup (tests), work_dir, root, version
     * @return array ok (bool), state, version, migrations (done, pending, failed), lock (bool), problems, warnings,
     *               duration_ms
     */
    public static function testConnection( expMigrationRemote $remote, array $options = array() )
    {
        $options += array( 'timeout' => self::TEST_TIMEOUT, 'lookup' => null, 'work_dir' => expMigrationRemoteDeploy::workDirectory(), 'root' => (string)eZSys::rootDir(),
                           'version' => class_exists( 'ExponentialSDK' ) ? (string)ExponentialSDK::version() : '' );
        $started = microtime( true );
        $result = array( 'ok' => false, 'state' => '', 'version' => '', 'migrations' => array( 'done' => 0, 'pending' => 0, 'failed' => 0 ), 'lock' => false,
                         'problems' => array(), 'warnings' => array(), 'duration_ms' => 0 );
        if ( $remote->canRun() )
        {
            $deploy = new expMigrationRemoteDeploy( array( $remote->id() => $remote ), array( 'timeout' => (int)$options['timeout'], 'retries' => 0, 'store' => false,
                                                                                               'lookup' => $options['lookup'], 'work_dir' => $options['work_dir'],
                                                                                               'root' => $options['root'], 'version' => $options['version'],
                                                                                               'audit' => function () {} ) );
            $report = $deploy->status();
            $entry = $report['remotes'][$remote->id()];
            $result['state'] = (string)$entry['state'];
            $result['version'] = (string)$entry['version'];
            $result['migrations'] = $entry['migrations'];
            $result['lock'] = !empty( $entry['lock'] );
            $result['problems'] = array_values( $entry['problems'] );
            $result['warnings'] = array_values( $report['warnings'] );
            $result['ok'] = in_array( $entry['state'], array( 'ready', 'prepared' ), true ) && !$entry['problems'];
        }
        else
        {
            foreach ( $remote->validate() as $problem )
                $result['problems'][] = $problem;
            $credential = new expMigrationRemoteCredential( $remote->credential(), array( 'work_dir' => $options['work_dir'], 'root' => $options['root'], 'lookup' => $options['lookup'] ) );
            if ( !$result['problems'] && ( $why = $credential->problem() ) !== '' )
                $result['problems'][] = $why;
            if ( !$result['problems'] )
            {
                $transport = new expMigrationRemoteTransport( $remote, $credential, rtrim( (string)$options['work_dir'], '/' ) . '/known_hosts/' . $remote->id() );
                $runs = expMigrationRemoteProcess::runAll( array( 'test' => array( $transport->fetch( 'index.php' ) ) ), 1, (int)$options['timeout'] );
                $run = $runs['test'];
                if ( (int)$run['exit'] === 0 && trim( (string)$run['stdout'] ) !== '' )
                {
                    $result['ok'] = true;
                    $result['state'] = 'ready';
                    $result['warnings'][] = self::tr( 'Signed in over FTP and read index.php. The migration status of an FTP remote cannot be read.' );
                }
                else
                {
                    $tail = trim( (string)$run['stderr'] ) !== '' ? (string)$run['stderr'] : (string)$run['stdout'];
                    $result['state'] = 'unreachable';
                    $result['problems'][] = $credential->redact( 'not reachable or index.php not readable (exit ' . (int)$run['exit'] . '): '
                                                                 . implode( ' | ', array_slice( array_filter( array_map( 'trim', preg_split( '/\r?\n/', $tail ) ) ), -3 ) ) );
                }
            }
            $credential->cleanup();
        }
        $result['duration_ms'] = (int)round( ( microtime( true ) - $started ) * 1000 );
        return $result;
    }

    /**
     * The remotes as the list shows them. Pure but for the settings.
     *
     * @param expMigrationRemote[] $remotes
     * @return array[] id, environment, transport, address, groups, credential (kind, name), can_run, problems
     */
    public static function overview( array $remotes )
    {
        $rows = array();
        foreach ( $remotes as $id => $remote )
        {
            $choice = self::credentialChoice( $remote->credential() );
            $rows[] = array( 'id' => (string)$id, 'environment' => $remote->environment(), 'transport' => $remote->transport(), 'address' => $remote->address(),
                             'groups' => $remote->groups(), 'credential' => $choice, 'can_run' => $remote->canRun(), 'problems' => $remote->validate() );
        }
        return $rows;
    }

    /**
     * The groups of the remotes, each with its remotes. Pure.
     *
     * @param expMigrationRemote[] $remotes
     * @return array group => remote ids
     */
    public static function groups( array $remotes )
    {
        $groups = array();
        foreach ( $remotes as $id => $remote )
            foreach ( $remote->groups() as $group )
                $groups[$group][] = (string)$id;
        ksort( $groups );
        return $groups;
    }

    /**
     * The options of a deploy job from the deploy form (DeployRemote[], DeployGroup[], DeployMode, DeployStaged,
     * DeployOnly, ConfirmDeploy). Pure.
     *
     * @param array $post
     * @param expMigrationRemote[] $remotes the remotes of RemoteList[]
     * @param bool $preflight the preflight button (a dry run) rather than the deploy button
     * @param string[] $errors codes: remotes, unknown, confirm, ftp, only
     * @return array remotes (ids), only, apply, transfer-only, staged
     */
    public static function deployOptions( array $post, array $remotes, $preflight, array &$errors = array() )
    {
        $picked = array();
        foreach ( isset( $post['DeployRemote'] ) ? (array)$post['DeployRemote'] : array() as $id )
            $picked[] = (string)$id;
        $groups = self::groups( $remotes );
        foreach ( isset( $post['DeployGroup'] ) ? (array)$post['DeployGroup'] : array() as $group )
            if ( isset( $groups[(string)$group] ) )
                $picked = array_merge( $picked, $groups[(string)$group] );
        $ids = array();
        foreach ( array_unique( $picked ) as $id )
        {
            if ( !isset( $remotes[$id] ) )
            {
                $errors[] = 'unknown';
                continue;
            }
            $ids[] = $id;
        }
        // in the order of RemoteList[]
        $ids = array_values( array_intersect( array_map( 'strval', array_keys( $remotes ) ), $ids ) );
        if ( !$ids )
            $errors[] = 'remotes';
        $mode = isset( $post['DeployMode'] ) && in_array( $post['DeployMode'], array( 'apply', 'transfer-only' ), true ) ? (string)$post['DeployMode'] : 'apply';
        $only = array();
        foreach ( preg_split( '/[\s,]+/', isset( $post['DeployOnly'] ) && is_scalar( $post['DeployOnly'] ) ? (string)$post['DeployOnly'] : '' ) as $id )
            if ( ( $id = trim( $id ) ) !== '' )
            {
                if ( !preg_match( '/^[A-Za-z0-9][A-Za-z0-9_.-]{0,149}$/', $id ) )
                    $errors[] = 'only';
                else
                    $only[] = $id;
            }
        if ( !$preflight && empty( $post['ConfirmDeploy'] ) )
            $errors[] = 'confirm';
        if ( !$preflight && $mode === 'apply' )
            foreach ( $ids as $id )
                if ( !$remotes[$id]->canRun() )
                {
                    $errors[] = 'ftp';
                    break;
                }
        $staged = $mode === 'apply' && !empty( $post['DeployStaged'] );
        return array( 'remotes' => $ids, 'only' => array_values( array_unique( $only ) ), 'apply' => !$preflight && $mode === 'apply',
                      'transfer-only' => !$preflight && $mode === 'transfer-only', 'staged' => !$preflight && $staged,
                      'mode' => $mode, 'staged_choice' => !empty( $post['DeployStaged'] ) );
    }

    /**
     * The kind of badge of a deploy's or a remote's state (ok, warn, bad, info). Pure.
     *
     * @param string $state
     * @return string
     */
    public static function stateKind( $state )
    {
        switch ( (string)$state )
        {
            case 'deployed':
            case 'applied':
            case 'planned':
            case 'transferred':
            case 'ready':
            case 'verified':
            case 'sent':
            case 'rolled-back':
            case 'unchanged':
                return 'ok';
            case 'gated':
            case 'waiting':
            case 'skipped':
            case 'restored':
            case 'status':
                return 'warn';
            case 'failed':
            case 'refused':
            case 'out-of-sync':
            case 'unreachable':
            case 'unknown':
                return 'bad';
        }
        return 'info';
    }

    /**
     * A deploy's report as its page shows it: the remotes with their stage, the phases, what can be done. Pure.
     *
     * @param array $report exp-migration-deploy/1
     * @return array the report with remotes (a list: id, environment, address, transport, stage, state, kind, send,
     *               applied, pending, problems, fixes, version, caption), phases (a list: name, ok, duration_ms),
     *               actions (expMigrationRemoteDeploy::actions()), kind (of the state)
     */
    public static function decorate( array $report )
    {
        $stageOf = array();
        foreach ( isset( $report['stages'] ) ? (array)$report['stages'] : array() as $index => $ids )
            foreach ( (array)$ids as $id )
                $stageOf[(string)$id] = $index + 1;
        $remotes = array();
        foreach ( isset( $report['remotes'] ) ? (array)$report['remotes'] : array() as $id => $remote )
            $remotes[] = array( 'id' => (string)$id, 'environment' => isset( $remote['remote']['environment'] ) ? (string)$remote['remote']['environment'] : '',
                                'address' => isset( $remote['remote']['address'] ) ? (string)$remote['remote']['address'] : '',
                                'transport' => isset( $remote['remote']['transport'] ) ? (string)$remote['remote']['transport'] : '',
                                'stage' => isset( $stageOf[(string)$id] ) ? $stageOf[(string)$id] : 0,
                                'state' => (string)$remote['state'], 'kind' => self::stateKind( $remote['state'] ),
                                'send' => array_values( (array)$remote['send'] ), 'applied' => array_values( (array)$remote['applied'] ),
                                'pending' => array_values( (array)$remote['pending'] ), 'problems' => array_values( (array)$remote['problems'] ),
                                'fixes' => array_values( (array)$remote['fixes'] ), 'version' => (string)$remote['version'],
                                'caption' => (string)$remote['caption'] );
        $phases = array();
        foreach ( isset( $report['phases'] ) ? (array)$report['phases'] : array() as $name => $phase )
            $phases[] = array( 'name' => (string)$name, 'ok' => !empty( $phase['ok'] ), 'duration_ms' => isset( $phase['duration_ms'] ) ? (int)$phase['duration_ms'] : 0 );
        $report['remote_list'] = $remotes;
        $report['phase_list'] = $phases;
        $report['actions'] = expMigrationRemoteDeploy::actions( $report );
        $report['kind'] = self::stateKind( isset( $report['state'] ) ? $report['state'] : '' );
        $report['stage_count'] = isset( $report['stages'] ) ? count( (array)$report['stages'] ) : 0;
        $report['next_stage_number'] = $report['actions']['next_stage'] !== null ? $report['actions']['next_stage'] + 1 : 0;
        return $report;
    }

    /** @return string a text of the context design/admin/migration, translated */
    public static function tr( $source, array $arguments = array() )
    {
        if ( class_exists( 'ezpI18n' ) )
            return (string)ezpI18n::tr( 'design/admin/migration', $source, null, $arguments );
        return strtr( $source, $arguments );
    }

    /**
     * The secret names a credential may name (Setup > Secrets), for the wizard's list.
     *
     * @return string[]
     */
    public static function secretNames()
    {
        try
        {
            return class_exists( 'expSecrets' ) ? array_keys( expSecrets::names() ) : array();
        }
        catch ( Throwable $e )
        {
            return array();
        }
    }
}
