<?php
/**
 * Loads classes out of an engine archive, as an installation with EXP_ENGINE_PHAR does, and prints
 * as JSON what their installation-root helpers answer. EXP_ROOT_DIR is published first, as
 * autoload.php (always on disk) does.
 *
 *   php engine_archive_roots.php <archive.phar> <root>
 */
list( , $archive, $root ) = $argv;
define( 'EXP_ROOT_DIR', $root );
$in = 'phar://' . $archive . '/';
$out = array();

if ( is_file( $in . 'kernel/classes/audit/expauditconfig.php' ) )
{
    require $in . 'kernel/classes/audit/expauditconfig.php';
    $out['audit.root'] = expAuditConfig::root();
    $out['audit.source'] = ( new ReflectionClass( 'expAuditConfig' ) )->getFileName();
}
if ( is_file( $in . 'lib/ezutils/classes/ezprepairqueue.php' ) )
{
    require $in . 'lib/ezutils/classes/ezprepairqueue.php';
    $out['repairqueue.root'] = ezpRepairQueue::root();
}
if ( is_file( $in . 'lib/ezdb/classes/ezdbinterface.php' ) )
{
    require $in . 'lib/ezdb/classes/ezdbinterface.php';
    $m = new ReflectionMethod( 'eZDBInterface', 'sqlProfilePath' );
    $m->setAccessible( true );
    $out['db.sqlprofile'] = $m->invoke( null, 'x.log' );
    if ( is_file( $in . 'lib/ezdb/classes/expmongodb.php' ) )
    {
        require $in . 'lib/ezdb/classes/expmongodb.php';
        $out['mongodb.profile'] = expMongoDB::profilePath( 'x.log' );
    }
}
if ( is_file( $in . 'kernel/classes/contentjob/expcontentjob.php' ) )
{
    require $in . 'kernel/classes/contentjob/expcontentjob.php';
    $out['contentjob.root'] = expContentJob::rootDir();
}
echo json_encode( $out, JSON_UNESCAPED_SLASHES ), "\n";
