<?php
/**
 * Test helper: saves one setting through expIniEditor as another user, in a child process.
 * Usage: setpriv --reuid=alpha --regid=<gid> --groups=<list> php expinisaveasuser.php <fixture root> <value>
 * The test starts it that way (PHP has no setgroups); it refuses to run as root. Prints "OK" and the warnings.
 */
list( , $root, $value ) = $_SERVER['argv'] + array_fill( 0, 3, '' );
$kernel = dirname( __DIR__, 6 ) . '/kernel/classes/ini/';
foreach ( array( 'expiniexception', 'expiniscope', 'expiniscopeprovider', 'expinicorescopeprovider',
                 'expiniextensionscopeprovider', 'expinilocator', 'expiniwriteresult', 'expiniwriter', 'expinieditor' ) as $f )
    require_once $kernel . $f . '.php';

if ( posix_geteuid() === 0 )
{
    echo "STILL ROOT\n";
    exit( 2 );
}

expIniEditor::setRoot( $root );
$e = new expIniEditor( expIniEditor::scope( 'global' ), 'site' );
$e->set( 'SiteSettings', 'SiteName', $value );
$r = $e->save();
echo "OK\n", implode( "\n", $r->warnings() ), "\n";
