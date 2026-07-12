#!/usr/bin/env php
<?php
/**
 * @description Reset a user password with admin authentication or root bypass.
 *
 * Process:
 * 1. Parse CLI options.
 * 2. If --allow-root-user is used, require the OS root user and skip admin auth.
 * 3. Otherwise authenticate an admin user with -a / -ap and verify the
 *    Administrator role.
 * 4. Resolve the target user (-u, default admin) and the new password (-p),
 *    or generate a random one.
 * 5. Validate and set the new bcrypt password.
 * 6. Persist and report the result.
 */

require_once 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance(
    array(
        'description' => "Reset a user password with admin authentication or root bypass.\n" .
                         "\n" .
                         "Admin auth (required unless bypassed with --allow-root-user):\n" .
                         "  ./bin/php/resetuserpassword.php -a admin -ap 'adminpass' -u admin -p 'newpass'\n" .
                         "\n" .
                         "Root bypass (resets any user without admin auth):\n" .
                         "  ./bin/php/resetuserpassword.php --allow-root-user -u admin -p 'newpass'\n" .
                         "\n" .
                         "Generate a random password:\n" .
                         "  ./bin/php/resetuserpassword.php -a admin -ap 'adminpass' -u admin -g -l 16",
        'use-session' => false,
        'use-modules' => false,
        'use-extensions' => true
    )
);

$script->startup();

// ---------- 1. Parse CLI options ----------
$adminLogin     = null;
$adminPassword  = null;
$targetLogin    = 'admin';
$targetPassword = null;
$generate       = false;
$length         = 16;
$siteaccess     = null;
$allowRoot      = false;
$quiet          = false;
$help           = false;

$argv = $GLOBALS['argv'];
$argc = count( $argv );
for ( $i = 1; $i < $argc; $i++ )
{
    $arg = $argv[$i];

    // Long options with embedded value
    if ( strpos( $arg, '--siteaccess=' ) === 0 )
    {
        $siteaccess = substr( $arg, strlen( '--siteaccess=' ) );
        continue;
    }

    switch ( $arg )
    {
        case '-h':
        case '--help':
            $help = true;
            break;

        case '-q':
        case '--quiet':
            $quiet = true;
            break;

        case '-r':
        case '--allow-root-user':
            $allowRoot = true;
            break;

        case '-g':
        case '--generate':
            $generate = true;
            break;

        case '-a':
            $i++;
            if ( $i >= $argc )
            {
                $cli->error( 'Option -a requires a value (admin login).' );
                $script->shutdown( 1 );
            }
            $adminLogin = $argv[$i];
            break;

        case '-ap':
            $i++;
            if ( $i >= $argc )
            {
                $cli->error( "Option -ap requires a value (admin password)." );
                $script->shutdown( 1 );
            }
            $adminPassword = $argv[$i];
            break;

        case '-u':
            $i++;
            if ( $i >= $argc )
            {
                $cli->error( 'Option -u requires a value (target login).' );
                $script->shutdown( 1 );
            }
            $targetLogin = $argv[$i];
            break;

        case '-p':
            $i++;
            if ( $i >= $argc )
            {
                $cli->error( 'Option -p requires a value (target password).' );
                $script->shutdown( 1 );
            }
            $targetPassword = $argv[$i];
            break;

        case '-l':
            $i++;
            if ( $i >= $argc )
            {
                $cli->error( 'Option -l requires a value (password length).' );
                $script->shutdown( 1 );
            }
            $length = (int) $argv[$i];
            break;

        case '-s':
        case '--siteaccess':
            $i++;
            if ( $i >= $argc )
            {
                $cli->error( 'Option -s / --siteaccess requires a value.' );
                $script->shutdown( 1 );
            }
            $siteaccess = $argv[$i];
            break;

        default:
            $cli->error( "Unknown option: $arg" );
            $script->shutdown( 1 );
    }
}

if ( $help )
{
    $program = basename( $argv[0] );
    $cli->output(
        "Usage: $program [OPTIONS]\n" .
        "\n" .
        "Reset a user password with admin authentication or root bypass.\n" .
        "\n" .
        "Admin authentication (one of these two):\n" .
        "  -a <login>            Admin login used to authorize the reset\n" .
        "  -ap <password>        Password for the admin login\n" .
        "\n" .
        "Root bypass (OS root user only):\n" .
        "  -r, --allow-root-user Skip admin auth and reset any user\n" .
        "\n" .
        "Target user and password:\n" .
        "  -u <login>            Target login to reset (default: admin)\n" .
        "  -p <password>         New password for the target user\n" .
        "  -g, --generate        Generate a random password instead of -p\n" .
        "  -l <length>           Length of generated password (default: 16)\n" .
        "\n" .
        "General options:\n" .
        "  -s, --siteaccess <sa> Selected siteaccess (default from site.ini)\n" .
        "  -q, --quiet           Suppress non-error output\n" .
        "  -h, --help            Show this help and exit"
    );
    $script->shutdown( 0 );
}

if ( $quiet )
    $script->setIsQuiet( true );

if ( $siteaccess )
    $script->setUseSiteAccess( $siteaccess );

$script->initialize();

// ---------- 2. Authorize the operator ----------
if ( $allowRoot )
{
    if ( function_exists( 'posix_getuid' ) && posix_getuid() !== 0 )
    {
        $cli->error( '--allow-root-user / -r requires the OS root user.' );
        $script->shutdown( 1 );
    }
    // Root bypass: no admin auth required.
}
else
{
    if ( $adminLogin === null || $adminLogin === '' || $adminPassword === null || $adminPassword === '' )
    {
        $cli->error( 'Admin authentication required. Use -a <login> -ap <password>, or --allow-root-user as root.' );
        $script->shutdown( 1 );
    }

    // 2a. Authenticate the admin user.
    $adminUser = eZUser::fetchByName( $adminLogin );
    if ( !is_object( $adminUser ) )
    {
        $cli->error( "Admin user not found: $adminLogin" );
        $script->shutdown( 1 );
    }

    $authOk = eZUser::authenticateHash(
        $adminUser->attribute( 'login' ),
        $adminPassword,
        eZUser::site(),
        $adminUser->attribute( 'password_hash_type' ),
        $adminUser->attribute( 'password_hash' )
    );

    if ( !$authOk )
    {
        $cli->error( 'Admin authentication failed.' );
        $script->shutdown( 1 );
    }

    // 2b. Verify the admin user has the Administrator role.
    $adminRole = eZRole::fetchByName( 'Administrator' );
    if ( !is_object( $adminRole ) )
    {
        $cli->error( 'Administrator role not found. Cannot verify admin privileges.' );
        $script->shutdown( 1 );
    }

    $roleIds = $adminUser->roleIDList();
    if ( !is_array( $roleIds ) || !in_array( $adminRole->attribute( 'id' ), $roleIds ) )
    {
        $cli->error( "User '$adminLogin' does not have the Administrator role." );
        $script->shutdown( 1 );
    }
}

// ---------- 3. Resolve target user and new password ----------
if ( $targetLogin === null || $targetLogin === '' )
{
    $cli->error( 'Target user login is required (-u <login>).' );
    $script->shutdown( 1 );
}

if ( $targetPassword === null && !$generate )
{
    $generate = true;
}

if ( $targetPassword === null && $generate )
{
    $targetPassword = eZUser::createPassword( $length );
}

if ( $targetPassword === null || $targetPassword === '' )
{
    $cli->error( 'No target password provided or generated.' );
    $script->shutdown( 1 );
}

if ( !eZUser::validatePassword( $targetPassword ) )
{
    $ini = eZINI::instance();
    $minLength = (int) $ini->variable( 'UserSettings', 'MinPasswordLength' );
    $cli->error( "Target password does not validate. It must be at least $minLength characters long." );
    $script->shutdown( 1 );
}

// ---------- 4. Reset target password ----------
$targetUser = eZUser::fetchByName( $targetLogin );
if ( !is_object( $targetUser ) )
{
    $cli->error( "Target user not found: $targetLogin" );
    $script->shutdown( 1 );
}

$targetUser->setInformation(
    $targetUser->attribute( 'contentobject_id' ),
    $targetUser->attribute( 'login' ),
    $targetUser->attribute( 'email' ),
    $targetPassword,
    $targetPassword
);
$targetUser->store();

// ---------- 5. Report ----------
$cli->output( "Password reset for user: $targetLogin" );
if ( $generate )
{
    $cli->output( "Generated password: $targetPassword" );
}

$script->shutdown( 0 );
