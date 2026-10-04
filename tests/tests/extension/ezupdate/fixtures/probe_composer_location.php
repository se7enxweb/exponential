#!/usr/bin/env php
<?php
/**
 * Prints, as JSON, what eZUpdateManager finds for the [ComposerSettings] given as the first argument (JSON).
 * Run it under open_basedir: php -d open_basedir=<root>:/tmp tests/tests/extension/ezupdate/fixtures/probe_composer_location.php '{"SearchPath":["var/tmp/x/"]}'
 */
$root = dirname( __DIR__, 5 );
chdir( $root );
require_once $root . '/autoload.php';
$script = eZScript::instance( array( 'use-session' => false, 'use-modules' => false, 'use-extensions' => true ) );
$script->startup();
$script->setUseSiteAccess( 'admin' );
$script->initialize();
$ini = eZINI::instance( 'ezupdate.ini' );
foreach ( (array)json_decode( isset( $argv[1] ) ? $argv[1] : '{}', true ) as $name => $value )
{
    $ini->setVariable( 'ComposerSettings', $name, $value );
}
$manager = eZUpdateManager::getInstance();
echo "\n@@", json_encode( array(
    'binary'    => $manager->composerBinary(),
    'command'   => $manager->composerCommand(),
    'trusted'   => $manager->isTrusted(),
    'php'       => $manager->phpBinary(),
    'allowed_usr_local_bin' => $manager->pathAllowed( '/usr/local/bin/composer' ),
    'allowed_root' => $manager->pathAllowed( $root . '/var/ezupdate/composer.phar' ),
    'searched'  => $manager->searchedPlaces(),
    'message'   => $manager->notFoundMessage(),
) );
