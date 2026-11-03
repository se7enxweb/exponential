<?php
/**
 * The permanent contract of the exp:user commands (user management from the console): their names, their options and
 * arguments, the exit codes and the keys of the machine output exp-user/1, and the pure parts of expUserManager (how
 * an account is named, the list filters, the last administrator, the generated password). Options, arguments and keys
 * may be added (extend the snapshot); nothing here may ever be renamed or removed. Needs no installation.
 *
 *  UC-01 every command and its option configuration (snapshot), and the arguments
 *  UC-02 exit codes and the format name; every output names the schema and the command
 *  UC-03 every command has its entry point in bin/php with the @alias the console dispatches, and a description
 *  UC-04 the literal option configuration, arguments and help of each command (what exp:help:examples reads) are the
 *        contract's
 *  UC-05 secrets: no command takes a password on the command line (--password), changing commands are dry runs
 *        unless --apply, and every one of them has --apply, --dry-run and --as
 *  UC-06 refKind(): an id, an e-mail address or a login
 *  UC-07 listFilters(): defaults, every state and sort, wrong values named
 *  UC-08 isLastAdministrator()
 *  UC-09 generatePassword(): long enough, all four kinds of characters, none that can be misread, never twice the same
 *  UC-10 the text output: actionLines() of a dry run and of a change with a generated password, listLines(), showLines()
 *  UC-11 the pure parts of the second commands: the import file read (formula cells, a password column refused), an
 *        export row (never a hash), the --confirm check of bulk, a role target, the audit filters
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expUserCommandContractTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    /** The snapshot. Add, never change or remove. */
    const COMMANDS = array(
        'exp:user:list' => '[state:][group:][search:][days:][sort:][order:][limit:][offset:][format:]',
        'exp:user:show' => '[as:][format:]',
        'exp:user:create' => '[login:][email:][first-name:][last-name:][group:*][class:][password-stdin][generate-password][send-activation][disabled][apply][dry-run][as:][format:]',
        'exp:user:edit' => '[email:][login:][first-name:][last-name:][add-group:*][remove-group:*][main-group:][apply][dry-run][as:][format:]',
        'exp:user:delete' => '[apply][dry-run][as:][format:]',
        'exp:user:disable' => '[apply][dry-run][as:][format:]',
        'exp:user:enable' => '[apply][dry-run][as:][format:]',
        'exp:user:reset-password' => '[send-link][generate][password-stdin][require-change][apply][dry-run][as:][format:]',
        'exp:user:approve' => '[reject][apply][dry-run][as:][format:]',
        'exp:user:pending' => '[search:][limit:][offset:][format:]',
        'exp:user:groups' => '[parent:][name:][to:][apply][dry-run][as:][format:]',
        'exp:user:roles' => '[subtree:][section:][apply][dry-run][as:][format:]',
        'exp:user:sessions' => '[end][session:][apply][dry-run][as:][format:]',
        'exp:user:unlock' => '[apply][dry-run][as:][format:]',
        'exp:user:2fa' => '[reset][remove-passkeys][apply][dry-run][as:][format:]',
        'exp:user:require-password-change' => '[clear][apply][dry-run][as:][format:]',
        'exp:user:resend-activation' => '[apply][dry-run][as:][format:]',
        'exp:user:export' => '[state:][group:][search:][days:][type:][output:][format:]',
        'exp:user:import' => '[type:][group:*][send-activation][disabled][apply][dry-run][as:][format:]',
        'exp:user:bulk' => '[state:][group:][search:][days:][limit:][confirm:][apply][dry-run][as:][format:]',
        'exp:user:audit' => '[event:][result:][from:][to:][limit:][offset:][format:]',
        'exp:user:hygiene' => '[days:][format:]',
        'exp:user:whoami' => '[as:][format:]',
        'exp:user:check-login' => '[password-stdin][format:]',
    );

    /** The arguments. Add, never change or remove. */
    const ARGUMENTS = array(
        'exp:user:show' => '[user]',
        'exp:user:edit' => '[user]',
        'exp:user:delete' => '[user]',
        'exp:user:disable' => '[user]',
        'exp:user:enable' => '[user]',
        'exp:user:reset-password' => '[user]',
        'exp:user:approve' => '[user]',
        'exp:user:groups' => '[action][group]',
        'exp:user:roles' => '[action][role][target]',
        'exp:user:sessions' => '[user]',
        'exp:user:unlock' => '[user]',
        'exp:user:2fa' => '[user]',
        'exp:user:require-password-change' => '[user]',
        'exp:user:resend-activation' => '[user]',
        'exp:user:import' => '[file]',
        'exp:user:bulk' => '[action]',
        'exp:user:audit' => '[user]',
        'exp:user:check-login' => '[user]',
    );

    /** command => entry point in bin/php */
    const ENTRY = array(
        'exp:user:list' => 'expuserlist',
        'exp:user:show' => 'expusershow',
        'exp:user:create' => 'expusercreate',
        'exp:user:edit' => 'expuseredit',
        'exp:user:delete' => 'expuserdelete',
        'exp:user:disable' => 'expuserdisable',
        'exp:user:enable' => 'expuserenable',
        'exp:user:reset-password' => 'expuserresetpassword',
        'exp:user:approve' => 'expuserapprove',
        'exp:user:pending' => 'expuserpending',
        'exp:user:groups' => 'expusergroups',
        'exp:user:roles' => 'expuserroles',
        'exp:user:sessions' => 'expusersessions',
        'exp:user:unlock' => 'expuserunlock',
        'exp:user:2fa' => 'expuser2fa',
        'exp:user:require-password-change' => 'expuserrequirepasswordchange',
        'exp:user:resend-activation' => 'expuserresendactivation',
        'exp:user:export' => 'expuserexport',
        'exp:user:import' => 'expuserimport',
        'exp:user:bulk' => 'expuserbulk',
        'exp:user:audit' => 'expuseraudit',
        'exp:user:hygiene' => 'expuserhygiene',
        'exp:user:whoami' => 'expuserwhoami',
        'exp:user:check-login' => 'expuserchecklogin',
    );

    /** The commands that change something (a dry run unless --apply). Add only. */
    const CHANGING = array( 'exp:user:create', 'exp:user:edit', 'exp:user:delete', 'exp:user:disable', 'exp:user:enable', 'exp:user:reset-password',
                            'exp:user:approve', 'exp:user:groups', 'exp:user:roles', 'exp:user:sessions', 'exp:user:unlock',
                            'exp:user:2fa', 'exp:user:require-password-change', 'exp:user:resend-activation', 'exp:user:import',
                            'exp:user:bulk' );

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
        foreach ( array( 'upgrade/expupgradestep', 'upgrade/expupgraderegistry', 'upgrade/expupgrade' ) as $f )
            require_once self::$root . "/kernel/classes/$f.php";
        require_once self::$root . '/kernel/private/classes/runnable/runnable.php';
        require_once self::$root . '/kernel/private/classes/runnable/command.php';
        require_once self::$root . '/kernel/private/classes/commands/expuserbase.php';
        require_once self::$root . '/kernel/private/classes/commands/expusershow.php';
    }

    /** @return array option => true of a config string */
    private static function options( $config )
    {
        preg_match_all( '/\[([a-z-]+:?\*?)\]/', $config, $m );
        return array_fill_keys( $m[1], true );
    }

    /** UC-01 */
    public function testCommandsAndOptionsOnlyGrow()
    {
        $now = \Exponential\Command\Kernel\ExpuserBase::contract();
        foreach ( self::COMMANDS as $command => $config )
        {
            $this->assertArrayHasKey( $command, $now, "the command $command is permanent" );
            foreach ( array_keys( self::options( $config ) ) as $option )
                $this->assertArrayHasKey( $option, self::options( $now[$command] ), "$command keeps --$option with the same arity" );
        }
        $help = \Exponential\Command\Kernel\ExpuserBase::optionHelp();
        foreach ( $now as $command => $config )
        {
            $this->assertArrayHasKey( $command, self::COMMANDS, "a new command $command: add it to the snapshot" );
            foreach ( array_keys( self::options( $config ) ) as $option )
                $this->assertArrayHasKey( rtrim( $option, ':*' ), $help, "--$option has a help text" );
        }
        $arguments = \Exponential\Command\Kernel\ExpuserBase::arguments();
        foreach ( self::ARGUMENTS as $command => $config )
            $this->assertSame( $config, isset( $arguments[$command] ) ? $arguments[$command] : null, "$command keeps its argument $config" );
        foreach ( array_keys( $arguments ) as $command )
            $this->assertArrayHasKey( $command, self::ARGUMENTS, "a new argument of $command: add it to the snapshot" );
    }

    /** UC-02 */
    public function testCodesAndFormat()
    {
        $this->assertSame( 'exp-user/1', expUserManager::REPORT );
        $this->assertSame( array( 0, 1, 2, 3, 4 ), array( expUpgrade::EXIT_OK, expUpgrade::EXIT_FAILED, expUpgrade::EXIT_USAGE,
                                                           expUpgrade::EXIT_UNREACHABLE, expUpgrade::EXIT_INTERNAL ) );
        $base = file_get_contents( self::$root . '/kernel/private/classes/commands/expuserbase.php' );
        $this->assertStringContainsString( "array( 'schema' => \\expUserManager::REPORT, 'command' => \$this->name() ) + \$report", $base,
                                           'every command output names the schema and the command' );
        $manager = file_get_contents( self::$root . '/kernel/classes/user/expusermanager.php' );
        foreach ( array( 'changes', 'applied', 'errors', 'warnings', 'user_id', 'generated_password', 'sessions_ended', 'state_before', 'state' ) as $key )
            $this->assertStringContainsString( "'$key'", $manager, "result key $key" );
        foreach ( array( 'mode', 'dry_run', 'user' ) as $key )
            $this->assertStringContainsString( "'$key' =>", $base, "report key $key" );
    }

    /** UC-03 */
    public function testEntryPoints()
    {
        foreach ( self::ENTRY as $command => $file )
        {
            $path = self::$root . "/bin/php/$file.php";
            $this->assertFileExists( $path );
            $this->assertTrue( is_executable( $path ), "$file.php is executable" );
            $text = file_get_contents( $path );
            $this->assertStringStartsWith( "#!/usr/bin/env php\n", $text );
            $this->assertStringContainsString( "@alias $command\n", $text );
            $class = self::$root . "/kernel/private/classes/commands/$file.php";
            $this->assertFileExists( $class );
            $code = file_get_contents( $class );
            $this->assertMatchesRegularExpression( '/@description \S/', $code );
            $this->assertStringContainsString( "@alias $command\n", $code );
            $this->assertStringContainsString( "return '$command';", $code, "$file names itself $command" );
        }
        $this->assertSame( array_keys( self::COMMANDS ), array_keys( self::ENTRY ), 'every command has its entry point' );
    }

    /** UC-04 */
    public function testLiteralOptionsMatchTheContract()
    {
        require_once self::$root . '/kernel/classes/help/exphelpexamplesgenerator.php';
        $contract = \Exponential\Command\Kernel\ExpuserBase::contract();
        $arguments = \Exponential\Command\Kernel\ExpuserBase::arguments();
        $help = \Exponential\Command\Kernel\ExpuserBase::optionHelp();
        foreach ( self::ENTRY as $command => $file )
        {
            $code = file_get_contents( self::$root . "/kernel/private/classes/commands/$file.php" );
            $this->assertSame( 1, preg_match( "/->startup\\( '([^']+)', '([^']*)'/", $code, $m ), "$file: startup() with a literal configuration" );
            $this->assertSame( $contract[$command], $m[1], "$command: the literal option configuration is the contract's" );
            $this->assertSame( isset( $arguments[$command] ) ? $arguments[$command] : '', $m[2], "$command: the literal arguments are the contract's" );
            $parsed = expHelpExamplesGenerator::scriptHelp( $code );
            $this->assertNotSame( '', $parsed['description'], "$command: a literal description" );
            foreach ( $parsed['options'] as $name => $option )
                $this->assertSame( $help[$name], $option['help'], "$command --$name: the help text of optionHelp()" );
            $this->assertCount( count( self::options( $contract[$command] ) ), $parsed['options'], "$command: every option has its help" );
            $this->assertStringNotContainsString( 'eZ Publish', $parsed['description'] );
        }
    }

    /** UC-05 */
    public function testSecretsAndDryRuns()
    {
        foreach ( \Exponential\Command\Kernel\ExpuserBase::contract() as $command => $config )
        {
            $options = self::options( $config );
            $this->assertArrayNotHasKey( 'password:', $options, "$command never takes a password on the command line" );
            $this->assertArrayNotHasKey( 'siteaccess:', $options, "$command leaves --siteaccess to the script" );
            if ( in_array( $command, self::CHANGING, true ) )
                foreach ( array( 'apply', 'dry-run', 'as:' ) as $option )
                    $this->assertArrayHasKey( $option, $options, "$command has --$option" );
            else
                $this->assertArrayNotHasKey( 'apply', $options, "$command changes nothing" );
        }
        foreach ( self::CHANGING as $command )
        {
            $code = file_get_contents( self::$root . '/kernel/private/classes/commands/' . self::ENTRY[$command] . '.php' );
            $this->assertStringContainsString( '$apply = $this->apply( $options );', $code, "$command reads --apply" );
        }
    }

    /** UC-06 */
    public function testRefKind()
    {
        $this->assertSame( 'id', expUserManager::refKind( '14' ) );
        $this->assertSame( 'id', expUserManager::refKind( ' 14 ' ) );
        $this->assertSame( 'email', expUserManager::refKind( 'jane@example.com' ) );
        $this->assertSame( 'login', expUserManager::refKind( 'jane' ) );
        $this->assertSame( 'login', expUserManager::refKind( 'jane.doe-2' ) );
        $this->assertSame( '', expUserManager::refKind( '  ' ) );
    }

    /** UC-07 */
    public function testListFilters()
    {
        $checked = expUserManager::listFilters( array() );
        $this->assertSame( array(), $checked['errors'] );
        $f = $checked['filters'];
        $this->assertSame( array( 'all', 'all', 180, '', '', 'name', 'asc', 25, 0 ),
                           array( $f['state'], $f['filter'], $f['days'], $f['group'], $f['q'], $f['sort'], $f['order'], $f['limit'], $f['offset'] ) );
        foreach ( expUserManager::STATES as $state )
            $this->assertSame( array(), expUserManager::listFilters( array( 'state' => $state ) )['errors'], "state $state" );
        foreach ( expUserManager::SORTS as $sort )
            $this->assertSame( array(), expUserManager::listFilters( array( 'sort' => $sort ) )['errors'], "sort $sort" );
        $this->assertSame( array_keys( expUserHygiene::SORTS ), expUserManager::SORTS, 'the sorts are those of the hygiene list' );
        foreach ( expUserHygiene::FILTERS as $filter )
            $this->assertContains( $filter, expUserManager::STATES, "the hygiene filter $filter is a state" );
        $bad = expUserManager::listFilters( array( 'state' => 'gone', 'sort' => 'age', 'order' => 'up', 'limit' => '501', 'offset' => '-1', 'days' => '0' ) );
        $this->assertCount( 6, $bad['errors'] );
        $this->assertSame( 500, expUserManager::listFilters( array( 'limit' => '500' ) )['filters']['limit'] );
        $this->assertSame( 'Jane', expUserManager::listFilters( array( 'search' => ' Jane ' ) )['filters']['q'], 'the search is trimmed (compared in any case)' );
    }

    /** UC-08 */
    public function testLastAdministrator()
    {
        $this->assertTrue( expUserManager::isLastAdministrator( 14, array( 14 ) ) );
        $this->assertFalse( expUserManager::isLastAdministrator( 14, array( 14, 99 ) ) );
        $this->assertFalse( expUserManager::isLastAdministrator( 99, array( 14 ) ), 'not an administrator' );
        $this->assertFalse( expUserManager::isLastAdministrator( 14, array() ) );
        $this->assertNotSame( '', expUserManager::refusalText( 'last_administrator' ) );
        foreach ( array( 'anonymous', 'self', 'creator', 'admin' ) as $reason )
            $this->assertNotSame( $reason, expUserManager::refusalText( $reason ), "a sentence for $reason" );
    }

    /** UC-09 */
    public function testGeneratedPassword()
    {
        $seen = array();
        for ( $i = 0; $i < 20; $i++ )
        {
            $password = expUserManager::generatePassword( 16 );
            $this->assertSame( 16, strlen( $password ) );
            foreach ( array( expUserManager::PASSWORD_LOWER, expUserManager::PASSWORD_UPPER, expUserManager::PASSWORD_DIGITS, expUserManager::PASSWORD_SYMBOLS ) as $set )
                $this->assertNotSame( false, strpbrk( $password, $set ), 'every kind of character' );
            $this->assertFalse( strpbrk( $password, '0O1lI' ), 'no character that can be misread' );
            $seen[$password] = true;
        }
        $this->assertCount( 20, $seen, 'never twice the same' );
        $this->assertGreaterThanOrEqual( 12, strlen( expUserManager::generatePassword( 4 ) ), 'at least 12 characters' );
    }

    /** UC-10 */
    public function testTextOutput()
    {
        $dry = \Exponential\Command\Kernel\ExpuserBase::actionLines( array( 'user' => array( 'id' => 99, 'login' => 'jane', 'enabled' => true ), 'dry_run' => true,
                                                                            'applied' => false, 'changes' => array( 'disable the account jane and end its sessions' ) ) );
        $this->assertSame( array( 'Account jane (id 99, enabled)', 'Would change (dry run, nothing changed; --apply makes the change):',
                                  '  disable the account jane and end its sessions' ), $dry );
        $done = implode( "\n", \Exponential\Command\Kernel\ExpuserBase::actionLines( array( 'user' => array( 'id' => 99, 'login' => 'jane', 'enabled' => true ),
                                                                                          'dry_run' => false, 'applied' => true, 'changes' => array( 'x' ),
                                                                                          'sessions_ended' => 2, 'generated_password' => 'Ab3-xyzXYZ234' ) ) );
        $this->assertStringContainsString( "Changed:\n  x", $done );
        $this->assertStringContainsString( 'Sessions ended: 2', $done );
        $this->assertStringContainsString( "shown this once", $done );
        $this->assertStringContainsString( "\n  Ab3-xyzXYZ234", $done );

        $account = array( 'id' => 99, 'login' => 'jane', 'email' => 'jane@example.com', 'last_visit' => false, 'states' => array( 'never' ),
                          'groups' => array( array( 'node_id' => 12, 'name' => 'Members' ) ) );
        $list = \Exponential\Command\Kernel\ExpuserBase::listLines( array( $account ), 40, 25 );
        $this->assertSame( '40 account(s), 26 to 26 listed', $list[0] );
        $this->assertStringContainsString( 'jane', $list[2] );
        $this->assertStringContainsString( 'never', $list[2] );
        $this->assertStringContainsString( 'Members', $list[2] );
        $this->assertSame( array( '0 account(s)' ), \Exponential\Command\Kernel\ExpuserBase::listLines( array(), 0, 0 ) );

        $show = implode( "\n", \Exponential\Command\Kernel\Expusershow::showLines( array(
            'id' => 99, 'login' => 'jane', 'email' => 'jane@example.com', 'name' => 'Jane Doe', 'class' => 'user', 'node_id' => 100, 'object' => 'published',
            'enabled' => false, 'unactivated' => true, 'locked' => false, 'failed_attempts' => 0, 'login_count' => 0, 'last_visit' => false,
            'password_changed' => false, 'password_change_required' => true, 'registration' => 'email', 'two_factor' => null, 'sessions' => null,
            'protected' => '', 'administrator' => false, 'groups' => array( array( 'node_id' => 12, 'name' => 'Members', 'main' => true ) ),
            'roles' => array( array( 'role' => 'Member', 'role_id' => 5, 'via' => 11, 'via_name' => 'Members', 'limitation' => 'Section:1' ) ) ) ) );
        $this->assertStringContainsString( 'Account jane (id 99)', $show );
        $this->assertStringContainsString( 'State:         unactivated', $show );
        $this->assertStringContainsString( 'Registration:  email', $show );
        $this->assertStringContainsString( 'a new one is required', $show );
        $this->assertStringContainsString( 'Members (node 12), main', $show );
        $this->assertStringContainsString( 'Member (Section:1), via Members (object 11)', $show );
        $this->assertStringNotContainsString( 'hash', strtolower( $show ) );
    }

    /** UC-11 */
    public function testImportExportBulkAndFilters()
    {
        $this->assertSame( 'csv', expUserBulk::typeOf( 'members.CSV' ) );
        $this->assertSame( 'json', expUserBulk::typeOf( 'var/x.json' ) );
        $this->assertSame( '', expUserBulk::typeOf( 'members.xlsx' ) );

        $parsed = expUserBulk::parse( "\xEF\xBB\xBFLogin,Email,first_name\njane,jane@example.com,'=Jane\n\n,bob@example.com,Bob\n", 'csv' );
        $this->assertSame( '', $parsed['error'] );
        $this->assertSame( array( 'line' => 2, 'login' => 'jane', 'email' => 'jane@example.com', 'first_name' => '=Jane', 'last_name' => '', 'groups' => '' ),
                           $parsed['rows'][0], 'a protected formula cell gets its text back; missing columns are empty' );
        $this->assertSame( 4, $parsed['rows'][1]['line'] );
        foreach ( array( "login,email,password\n" => 'password', "login,email,password_hash\n" => 'password', "login,email,hash\n" => 'password',
                         "login,email,phone\n" => 'unknown column', "login\nx\n" => 'login and email', '' => 'empty' ) as $text => $error )
            $this->assertStringContainsString( $error, expUserBulk::parse( $text, 'csv' )['error'], json_encode( $text ) );
        $this->assertStringContainsString( 'array of objects', expUserBulk::parse( '{"login":"x"}', 'json' )['error'] );
        $this->assertSame( 'x', expUserBulk::parse( '[{"login":"x","email":"x@example.com"}]', 'json' )['rows'][0]['login'] );

        $row = expUserBulk::exportRow( array( 'id' => 9, 'login' => 'jane', 'email' => 'jane@example.com', 'name' => 'Jane Doe', 'enabled' => true,
                                              'states' => array( 'never' ), 'groups' => array( array( 'node_id' => 12 ), array( 'node_id' => 13 ) ),
                                              'last_visit' => false, 'login_count' => 0, 'password_hash' => 'secret' ), array( 'first_name' => 'Jane' ) );
        $this->assertSame( expUserBulk::EXPORT_COLUMNS, array_keys( $row ) );
        $this->assertSame( array( '12|13', 12, 'never', '' ), array( $row['groups'], $row['main_group'], $row['states'], $row['last_visit'] ) );
        $this->assertNotContains( 'secret', $row, 'never a hash' );

        $this->assertSame( '', expUserBulk::confirmProblem( 42, '42' ) );
        $this->assertStringContainsString( '--confirm=42', expUserBulk::confirmProblem( 42, null ) );
        $this->assertStringContainsString( 'does not match', expUserBulk::confirmProblem( 42, '41' ) );
        $this->assertStringContainsString( 'does not match', expUserBulk::confirmProblem( 42, 'all' ) );

        $this->assertSame( array( 'user', 'group', 'group', '' ), array( expUserGroupManager::targetKind( 'jane' ), expUserGroupManager::targetKind( 'group:12' ),
                                                                       expUserGroupManager::targetKind( 'GROUP:Editors' ), expUserGroupManager::targetKind( ' ' ) ) );
        $this->assertSame( array( 'list', 'create', 'rename', 'move', 'delete' ), expUserGroupManager::GROUP_ACTIONS );
        $this->assertSame( array( 'list', 'assign', 'unassign' ), expUserGroupManager::ROLE_ACTIONS );
        $this->assertSame( array( 'disable', 'enable', 'delete', 'approve', 'reject', 'unlock' ), expUserBulk::ACTIONS );

        $audit = expUserManager::auditFilters( array( 'event' => 'access.user.password.*', 'result' => 'refused', 'from' => '2026-10-01', 'limit' => '100' ) );
        $this->assertSame( array(), $audit['errors'] );
        $this->assertSame( array( 'action' => 'access.user.password', 'result' => 'refused', 'from' => '2026-10-01', 'to' => '', 'limit' => 100, 'offset' => 0 ),
                           $audit['filters'] );
        $this->assertCount( 5, expUserManager::auditFilters( array( 'event' => 'a b', 'result' => 'ok', 'from' => '2026-02-30', 'to' => 'today', 'limit' => '0' ) )['errors'] );
    }
}
