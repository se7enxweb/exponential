<?php
/**
 * Builds a small engine archive for EngineArchiveInstallationRootTest and expAuditConfigRootTest.
 * Run with phar.readonly=0:  php -d phar.readonly=0 engine_archive_build.php <out.phar> <root> <file>...
 * Each <file> is relative to <root> and is stored under the same path inside the archive.
 */
list( , $out, $root ) = $argv;
$phar = new Phar( $out );
$phar->startBuffering();
foreach ( array_slice( $argv, 3 ) as $file )
    $phar->addFile( rtrim( $root, '/' ) . '/' . $file, $file );
$phar->setStub( '<?php __HALT_COMPILER();' );
$phar->stopBuffering();
echo "built\n";
