<?php
/**
 * Test helper: the files eZINI itself reads for one INI file of one siteaccess of a fixture installation root,
 * and the values it ends up with, in a child process.
 *
 * Usage: php expiniezinichain.php <fixture root> <siteaccess|-> <file> [<Block/Variable> ...]
 *
 * The process works inside the fixture root (its working directory, EXP_ROOT_DIR) with the kernel classes of this
 * installation, so eZSiteAccess::getIni() runs the kernel's own siteaccess loading -- eZExtension::
 * activateExtensions( 'default' ), eZSiteAccess::change(), activateExtensions( 'access' ) -- on the fixture's
 * settings. Prints JSON: files (eZINI::findInputFiles(), relative to the fixture root, in load order) and values
 * ("Block/Variable" => value, null when unset). The "-" siteaccess is none: only the extensions of
 * ActiveExtensions[] are activated, as before a siteaccess is chosen.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
$argv = $_SERVER['argv'];
if ( count( $argv ) < 4 )
{
    fwrite( STDERR, "Usage: php expiniezinichain.php <fixture root> <siteaccess|-> <file> [<Block/Variable> ...]\n" );
    exit( 1 );
}
$root = rtrim( realpath( $argv[1] ), '/' );
$siteAccess = $argv[2] === '-' ? null : $argv[2];
$file = $argv[3];
$wanted = array_slice( $argv, 4 );

define( 'EXP_ROOT_DIR', $root );
chdir( $root );
require dirname( __DIR__, 6 ) . '/autoload.php';

// no INI cache, and no text codec: the fixture has no i18n.ini to configure one, and its files are UTF-8 already
eZINI::setIsCacheEnabled( false );
eZINI::setIsTextCodecEnabled( false );
if ( $siteAccess === null )
{
    $siteIni = new eZINI( 'site.ini', 'settings', null, false, true );
    eZExtension::activateExtensions( 'default', $siteIni );
}
else
{
    $siteIni = eZSiteAccess::getIni( $siteAccess, 'site.ini' );
}
$ini = new eZINI( $file, 'settings', null, false, true, false, false, false );
$ini->setOverrideDirs( $siteIni->overrideDirs( false ) );
$ini->load();

$inputFiles = array();
$iniFile = null;
$ini->findInputFiles( $inputFiles, $iniFile );
$files = array();
foreach ( $inputFiles as $path )
{
    $real = realpath( $path[0] === '/' ? $path : $root . '/' . $path );
    $path = $real !== false ? $real : $path;
    $files[] = strpos( $path, $root . '/' ) === 0 ? substr( $path, strlen( $root ) + 1 ) : $path;
}
$values = array();
foreach ( $wanted as $setting )
{
    list( $block, $variable ) = explode( '/', $setting, 2 ) + array( '', '' );
    $values[$setting] = $ini->hasVariable( $block, $variable ) ? $ini->variable( $block, $variable ) : null;
}
echo json_encode( array( 'files' => $files, 'values' => $values ) ), "\n";
