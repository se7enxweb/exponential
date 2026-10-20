<?php
/**
 * The start location and the storage files of the content checks, the parts that need no database
 * (doc/en/us/features/6.0/content-checks.md, "Where a check starts", "Storage files", "Quarantine").
 *
 *  CS-01  the start location: the form's node, else the address's, else the preference, else the default; only whole
 *         numbers; a node given is remembered, the preference is not written again
 *  CS-02  the presets: named places, node ids and URL alias paths; anything else is left out; the views of ViewList
 *  CS-03  the node picker view: GET opens it for a listed view only; its POST takes one node, a cancel goes back, an
 *         unknown view is refused
 *  CS-04  paths below the storage: no "..", no empty or control parts; the ignored prefixes
 *  CS-05  the problems of a used file: another owner, missing mode bits, empty; the expected mode is a minimum
 *  CS-06  image files: the attribute their directory names; the XML names a file by url or dirpath/filename, also
 *         escaped; a url that is not dirpath/filename, a name that differs only in case; a missing file is not a
 *         mismatch (the missing files check reports it)
 *  CS-07  files and media: original/<first part of the MIME type>/<name>; a name with a slash is refused
 *  CS-08  fixing owner and mode: only what differs; the owner only as root; the mode only as root or the owner
 *  CS-09  the action view: POST only, the form token, the policies of each action, a plan before every action, the
 *         delete box, the lists cleaned and bounded
 *  CS-10  restore and delete in the quarantine, in a throwaway directory: the plan changes nothing, a file at the
 *         destination is never overwritten, links and paths outside are refused, a delete needs its own item
 *  CS-11  the storage view: the list, order and kinds read from the request; the pages keep them; the orphans sorted
 *  CS-12  the scan state as the view shows it: the share done; what an action took out of the lists
 *  CS-14  "All N results": the action for the whole list always shows its plan first; the trash of the unreferenced
 *         objects the same
 *  CS-13  one layout for every check: the scope picker first, "Select all" / "Select none" of a filter group as
 *         buttons of the form (no JavaScript needed) after "Show", which Enter in a field must keep pressing, the shared
 *         results header and pager
 *  CS-15  the scope buttons (Whole tree, Websites, Media) and the second row of sites and sections: visible children
 *         only, in order, the first N and "More", the current one and the one the start location lies in; the picker
 *         at another level; the links keep plain filters only; one template in both designs
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Contentcheck\Scope;
use Exponential\View\Kernel\Contentcheck\Storage;
use Exponential\View\Kernel\Contentcheck\Storageaction;

class expContentCheckStorageTest extends PHPUnit\Framework\TestCase
{
    /** @var string|null a throwaway directory of CS-10 */
    protected $dir = null;

    /** @var callable|null the exception handler found (the kernel's code may install its own) */
    protected $handler = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = set_exception_handler( null );
        restore_exception_handler();
        expContentCheck::setTestSecret( str_repeat( 'a', 64 ) );
    }

    protected function tearDown(): void
    {
        expContentCheck::setTestSecret( null );
        expContentCheckScope::setTestPreference( null );
        expContentCheckStorage::setTestStorage( null );
        expContentCheckStorage::setTestState( null );
        expContentCheckQuarantine::setTestUid( null );
        if ( $this->dir !== null && is_dir( $this->dir ) )
            $this->removeTree( $this->dir );
        for ( $i = 0; $i < 5; $i++ )
        {
            $current = set_exception_handler( null );
            restore_exception_handler();
            if ( $current === $this->handler )
                break;
            restore_exception_handler();
        }
        parent::tearDown();
    }

    /**
     * @param string $dir
     */
    protected function removeTree( $dir )
    {
        foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST ) as $item )
        {
            if ( $item->isDir() && !$item->isLink() )
                rmdir( $item->getPathname() );
            else
                unlink( $item->getPathname() );
        }
        rmdir( $dir );
    }

    /** CS-01 */
    public function testTheStartLocationComesFromTheFormTheAddressOrThePreference()
    {
        expContentCheckScope::setTestPreference( false );
        $this->assertSame( array( 'node_id' => 43, 'given' => true ), expContentCheckScope::choose( '2', '43', 2 ) );
        $this->assertSame( array( 'node_id' => 5, 'given' => true ), expContentCheckScope::choose( '5', null, 2 ) );
        $this->assertSame( array( 'node_id' => 1, 'given' => true ), expContentCheckScope::choose( null, '1', 2 ) );
        $this->assertSame( array( 'node_id' => 2, 'given' => false ), expContentCheckScope::choose( null, null, 2 ) );
        foreach ( array( '0', '-3', '1 OR 1=1', 'abc', '', array( 4 ), '99999999999' ) as $bad )
            $this->assertSame( 7, expContentCheckScope::choose( '7', $bad, 2 )['node_id'], var_export( $bad, true ) );

        expContentCheckScope::setTestPreference( 58 );
        $this->assertSame( array( 'node_id' => 58, 'given' => false ), expContentCheckScope::choose( null, null, 2 ) );
        $this->assertSame( 43, expContentCheckScope::choose( null, '43', 2 )['node_id'] );

        // resolve() remembers a node given, and only then
        $this->assertSame( 58, expContentCheckScope::resolve( null, null ) );
        $this->assertSame( 58, expContentCheckScope::preference() );
        $this->assertSame( 43, expContentCheckScope::resolve( '43', null ) );
        $this->assertSame( 43, expContentCheckScope::preference() );
        $this->assertSame( 1, expContentCheckScope::resolve( '43', '1' ) );
        $this->assertSame( 1, expContentCheckScope::preference() );
    }

    /** CS-02 */
    public function testThePresetsAndTheViews()
    {
        $this->assertSame( 'node:1', expContentCheckScope::presetSource( 'tree' ) );
        $this->assertSame( 'ini:RootNode', expContentCheckScope::presetSource( 'content' ) );
        $this->assertSame( 'ini:MediaRootNode', expContentCheckScope::presetSource( 'media' ) );
        $this->assertSame( 'ini:UserRootNode', expContentCheckScope::presetSource( 'users' ) );
        $this->assertSame( 'path:media/images', expContentCheckScope::presetSource( 'Media/Images' ) );
        $this->assertSame( 'node:58', expContentCheckScope::presetSource( '58' ) );
        foreach ( array( '', '0', '../etc', 'a b', "x\ny", 'media//images', '/media', array( 'tree' ), null, str_repeat( 'a', 201 ) ) as $bad )
            $this->assertNull( expContentCheckScope::presetSource( $bad ), var_export( $bad, true ) );
        $this->assertSame( 1, expContentCheckScope::presetNodeID( 'node:1' ) );
        $this->assertSame( 0, expContentCheckScope::presetNodeID( 'other:1' ) );

        $views = expContentCheckScope::views();
        foreach ( array( 'unreferenced', 'brokenrelations', 'storage' ) as $view )
        {
            $this->assertContains( $view, $views );
            $this->assertSame( $view, expContentCheckScope::view( $view ) );
        }
        foreach ( array( 'trash', 'storageaction', '../x', '', null, array( 'storage' ) ) as $bad )
            $this->assertNull( expContentCheckScope::view( $bad ), var_export( $bad, true ) );
    }

    /** CS-03 */
    public function testTheNodePickerView()
    {
        $d = Scope::decide( 'GET', 'storage', '43', array() );
        $this->assertSame( array( 'browse', 'storage', 43 ), array( $d['step'], $d['view'], $d['node_id'] ) );
        $this->assertSame( 'refused', Scope::decide( 'GET', 'trash', '43', array() )['step'] );
        $this->assertSame( 'refused', Scope::decide( 'GET', null, null, array() )['step'] );

        $post = array( 'ContentCheckView' => 'unreferenced', 'ContentCheckNodeID' => '2', 'BrowseActionName' => expContentCheckScope::BROWSE_ACTION,
                       'SelectedNodeIDArray' => array( '43', '58' ) );
        $d = Scope::decide( 'POST', null, null, $post );
        $this->assertSame( array( 'chosen', 'unreferenced', 2, 43 ), array( $d['step'], $d['view'], $d['node_id'], $d['chosen'] ) );
        $this->assertSame( 'cancel', Scope::decide( 'POST', null, null, array_merge( $post, array( 'BrowseCancelButton' => 1 ) ) )['step'] );
        $this->assertSame( 'cancel', Scope::decide( 'POST', null, null, array_merge( $post, array( 'BrowseActionName' => 'MoveNode' ) ) )['step'] );
        $this->assertSame( 'cancel', Scope::decide( 'POST', null, null, array_merge( $post, array( 'SelectedNodeIDArray' => array( 'x' ) ) ) )['step'] );
        $this->assertSame( 'refused', Scope::decide( 'POST', null, null, array_merge( $post, array( 'ContentCheckView' => 'trash' ) ) )['step'] );
    }

    /** CS-04 */
    public function testPathsBelowTheStorage()
    {
        $this->assertSame( 'images/a/b.jpg', expContentCheckStorage::cleanRelative( '/images/a/b.jpg/' ) );
        $this->assertSame( 'original/image/x.png', expContentCheckStorage::cleanRelative( 'original\\image\\x.png' ) );
        foreach ( array( '', '/', '../x', 'images/../../etc/passwd', 'images/./a', 'images//a', "images/a\0b", "a\nb", array( 'x' ), null, 5 ) as $bad )
            $this->assertNull( expContentCheckStorage::cleanRelative( $bad ), var_export( $bad, true ) );

        $ignored = array( 'images/_video-thumbnails' );
        $this->assertTrue( expContentCheckStorage::isIgnored( 'images/_video-thumbnails', $ignored ) );
        $this->assertTrue( expContentCheckStorage::isIgnored( 'images/_video-thumbnails/a.jpg', $ignored ) );
        $this->assertFalse( expContentCheckStorage::isIgnored( 'images/_video-thumbnails-x/a.jpg', $ignored ) );
        $this->assertFalse( expContentCheckStorage::isIgnored( 'images/a.jpg', $ignored ) );
    }

    /** CS-05 */
    public function testTheProblemsOfAUsedFile()
    {
        $this->assertSame( array(), expContentCheckStorage::fileProblems( array( 'uid' => 1000, 'mode' => 0666, 'size' => 10 ), 1000, 0666 ) );
        $this->assertSame( array(), expContentCheckStorage::fileProblems( array( 'uid' => 1000, 'mode' => 0777, 'size' => 10 ), 1000, 0666 ) );
        $this->assertSame( array( 'owner' ), expContentCheckStorage::fileProblems( array( 'uid' => 0, 'mode' => 0666, 'size' => 10 ), 1000, 0666 ) );
        $this->assertSame( array( 'mode' ), expContentCheckStorage::fileProblems( array( 'uid' => 1000, 'mode' => 0644, 'size' => 10 ), 1000, 0666 ) );
        $this->assertSame( array( 'owner', 'mode', 'empty' ), expContentCheckStorage::fileProblems( array( 'uid' => 0, 'mode' => 0600, 'size' => 0 ), 1000, 0644 ) );
        // no expected owner (a cluster, an unknown user): the owner is not judged
        $this->assertSame( array(), expContentCheckStorage::fileProblems( array( 'uid' => 0, 'mode' => 0644, 'size' => 1 ), null, 0644 ) );
    }

    /** CS-06 */
    public function testImageFiles()
    {
        $this->assertSame( array( 1234, 5 ), expContentCheckStorage::imageAttributeOf( 'images/media/images/logo/1234-5-eng-GB/logo.png' ) );
        $this->assertSame( array( 9, 2 ), expContentCheckStorage::imageAttributeOf( 'images/a/9-2/x.jpg' ) );
        $this->assertNull( expContentCheckStorage::imageAttributeOf( 'images/_video-thumbnails/x.jpg' ) );

        $base = 'var/site/storage/images/media/logo/12-3-eng-GB';
        $xml = '<?xml version="1.0"?><ezimage serial_number="1" is_valid="1" filename="logo.png" basename="logo" dirpath="' . $base . '" url="' . $base . '/logo.png">' .
               '<original attribute_id="12" attribute_version="3" attribute_language="eng-GB"/>' .
               '<alias name="small" filename="logo_small.png" suffix="small" dirpath="' . $base . '" url="' . $base . '/logo_small.png"/>' .
               '<alias name="large" filename="logo&amp;large.png" suffix="large" dirpath="' . $base . '" url="' . $base . '/logo&amp;large.png"/></ezimage>';
        $this->assertTrue( expContentCheckStorage::imageXmlNames( $xml, $base . '/logo_small.png' ) );
        $this->assertTrue( expContentCheckStorage::imageXmlNames( $xml, $base . '/logo&large.png' ) );
        $this->assertFalse( expContentCheckStorage::imageXmlNames( $xml, $base . '/logo_medium.png' ) );
        $this->assertFalse( expContentCheckStorage::imageXmlNames( $xml, $base . '/logo' ) );
        $this->assertFalse( expContentCheckStorage::imageXmlNames( '', $base . '/logo.png' ) );

        $files = expContentCheckStorage::imageXmlFiles( $xml );
        $this->assertSame( array( 'small', 'large' ), array( $files[1]['name'], $files[2]['name'] ) );
        $this->assertSame( $base . '/logo&large.png', $files[2]['url'] );

        $on = array( $base . '/logo.png' => true, $base . '/logo_small.png' => true, $base . '/logo&large.png' => true );
        $exists = function ( $path ) use ( &$on ) { return isset( $on[$path] ); };
        $list = function ( $dir ) { return array( '.', '..', 'logo.png', 'LOGO_SMALL.png', 'logo&large.png' ); };
        $this->assertSame( array(), expContentCheckStorage::imageXmlMismatches( $xml, $exists, $list ) );
        unset( $on[$base . '/logo_small.png'] );
        $this->assertSame( array( array( 'kind' => 'name', 'path' => $base . '/logo_small.png', 'detail' => $base . '/LOGO_SMALL.png' ) ),
                           expContentCheckStorage::imageXmlMismatches( $xml, $exists, $list ) );
        unset( $on[$base . '/logo&large.png'] );
        // missing altogether: not a mismatch
        $this->assertCount( 1, expContentCheckStorage::imageXmlMismatches( $xml, $exists, $list ) );

        $odd = '<ezimage><alias name="x" filename="b.png" dirpath="var/site/storage/images/a" url="var/site/storage/images/c/b.png"/></ezimage>';
        $this->assertSame( array( array( 'kind' => 'path', 'path' => 'var/site/storage/images/c/b.png', 'detail' => 'var/site/storage/images/a/b.png' ) ),
                           expContentCheckStorage::imageXmlMismatches( $odd, function () { return true; }, function () { return array(); } ) );
    }

    /** CS-07 */
    public function testFilesAndMediaPaths()
    {
        $this->assertSame( 'var/s/storage/original/application/abc.pdf', expContentCheckStorage::binaryPath( 'abc.pdf', 'application/pdf', 'var/s/storage' ) );
        $this->assertSame( 'var/s/storage/original/video/abc.mp4', expContentCheckStorage::binaryPath( 'abc.mp4', 'video/mp4', 'var/s/storage' ) );
        $this->assertSame( 'var/s/storage/original/application/abc', expContentCheckStorage::binaryPath( 'abc', '../x', 'var/s/storage' ) );
        foreach ( array( '', '.', '..', 'a/b', '../../etc/passwd' ) as $bad )
            $this->assertNull( expContentCheckStorage::binaryPath( $bad, 'image/png', 'var/s/storage' ), $bad );
    }

    /** CS-08 */
    public function testFixingOwnerAndMode()
    {
        $c = expContentCheckQuarantine::permissionChange( array( 'uid' => 1000, 'mode' => 0666 ), 1000, 0666, 1000 );
        $this->assertSame( array( null, null, 'fine' ), array( $c['owner'], $c['mode'], $c['reason'] ) );
        $c = expContentCheckQuarantine::permissionChange( array( 'uid' => 0, 'mode' => 0644 ), 1000, 0666, 0 );
        $this->assertSame( array( 1000, 0666, '' ), array( $c['owner'], $c['mode'], $c['reason'] ) );
        $c = expContentCheckQuarantine::permissionChange( array( 'uid' => 0, 'mode' => 0666 ), 1000, 0666, 1000 );
        $this->assertSame( array( 1000, null, 'needs_root' ), array( $c['owner'], $c['mode'], $c['reason'] ) );
        $c = expContentCheckQuarantine::permissionChange( array( 'uid' => 1000, 'mode' => 0600 ), 1000, 0666, 1000 );
        $this->assertSame( array( null, 0666, '' ), array( $c['owner'], $c['mode'], $c['reason'] ) );
        $c = expContentCheckQuarantine::permissionChange( array( 'uid' => 1000, 'mode' => 0600 ), null, 0666, 1001 );
        $this->assertSame( array( null, 0666, 'not_owner' ), array( $c['owner'], $c['mode'], $c['reason'] ) );
        $this->assertStringContainsString( '0600 -> ', $c['detail'] );
        $this->assertStringEndsWith( ' 0666', $c['detail'] );
    }

    /** CS-09 */
    public function testTheActionViewDecides()
    {
        $all = function () { return true; };
        $token = expContentCheck::formToken();
        $base = array( expContentCheck::TOKEN_FIELD => $token, 'NodeID' => '43', 'Show' => 'orphans' );

        foreach ( array( 'GET', 'HEAD', 'PUT', '' ) as $method )
            $this->assertSame( array( 'refused', 'method' ), array_values( array_intersect_key( Storageaction::decide( $method, $base + array( 'ScanButton' => 1 ), $all ), array( 'step' => 1, 'reason' => 1 ) ) ) );
        $d = Storageaction::decide( 'POST', array( 'ScanButton' => 1 ), $all );
        $this->assertSame( array( 'refused', 'token' ), array( $d['step'], $d['reason'] ) );
        $d = Storageaction::decide( 'POST', array( expContentCheck::TOKEN_FIELD => 'x' . substr( $token, 1 ), 'ScanButton' => 1 ), $all );
        $this->assertSame( 'token', $d['reason'] );

        $d = Storageaction::decide( 'POST', $base + array( 'ScanButton' => 1 ), $all );
        $this->assertSame( array( 'scan', true, 43, 'orphans' ), array( $d['step'], $d['restart'], $d['node_id'], $d['show'] ) );
        $this->assertFalse( Storageaction::decide( 'POST', $base + array( 'ContinueButton' => 1 ), $all )['restart'] );
        $this->assertSame( 'cancel', Storageaction::decide( 'POST', $base + array( 'CancelButton' => 1, 'Action' => 'quarantine' ), $all )['step'] );
        $this->assertSame( 'orphans', Storageaction::decide( 'POST', array_merge( $base, array( 'Show' => '<x>', 'ScanButton' => 1 ) ), $all )['show'] );

        // the policies: storage for everything, quarantine or permissions for the actions
        $only = function ( array $functions ) { return function ( $f ) use ( $functions ) { return in_array( $f, $functions, true ); }; };
        $this->assertSame( 'policy', Storageaction::decide( 'POST', $base + array( 'ScanButton' => 1 ), $only( array() ) )['reason'] );
        $this->assertSame( 'scan', Storageaction::decide( 'POST', $base + array( 'ScanButton' => 1 ), $only( array( 'storage' ) ) )['step'] );
        foreach ( array( 'quarantine' => 'quarantine', 'restore' => 'quarantine', 'delete' => 'quarantine', 'permissions' => 'permissions' ) as $action => $function )
        {
            $post = $base + array( 'Action' => $action, 'Items' => array( 'images/a.jpg', '2026-10-16/images/a.jpg' ) );
            $this->assertSame( 'policy', Storageaction::decide( 'POST', $post, $only( array( 'storage' ) ) )['reason'], $action );
            $d = Storageaction::decide( 'POST', $post, $only( array( 'storage', $function ) ) );
            $this->assertSame( 'confirm', $d['step'], $action );
            // a quarantine item is a date and a path; a path below the storage may look like one, too
            $this->assertCount( in_array( $action, array( 'restore', 'delete' ), true ) ? 1 : 2, $d['items'], $action );
        }

        // a plan first; then the confirmation; a delete needs its box as well
        $post = $base + array( 'Action' => 'quarantine', 'Items' => array( 'images/a.jpg', '../x', 'images/a.jpg' ), 'ConfirmButton' => 1 );
        $d = Storageaction::decide( 'POST', $post, $all );
        $this->assertSame( array( 'execute', array( 'images/a.jpg' ) ), array( $d['step'], $d['items'] ) );
        $post = $base + array( 'Action' => 'delete', 'Items' => array( '2026-10-16/images/a.jpg' ), 'ConfirmButton' => 1 );
        $this->assertSame( 'confirm', Storageaction::decide( 'POST', $post, $all )['step'] );
        $this->assertSame( 'execute', Storageaction::decide( 'POST', $post + array( 'ConfirmDelete' => '1' ), $all )['step'] );
        $this->assertSame( 'cancel', Storageaction::decide( 'POST', $base + array( 'Action' => 'rm -rf' ), $all )['step'] );

        // the lists are bounded: one more than the batch says that too many were ticked
        $many = array();
        for ( $i = 0; $i < expContentCheckQuarantine::batchLimit() + 50; $i++ )
            $many[] = 'images/f' . $i . '.jpg';
        $this->assertCount( expContentCheckQuarantine::batchLimit() + 1, expContentCheckQuarantine::cleanList( 'quarantine', $many ) );
        $this->assertSame( array( '2026-10-16/images/a.jpg' ), expContentCheckQuarantine::cleanList( 'restore', array( '2026-10-16/images/a.jpg', 'images/a.jpg', '2026-1-1/a', '2026-10-16/../a' ) ) );
        $this->assertSame( array( 'date' => '2026-10-16', 'path' => 'images/a.jpg' ), expContentCheckQuarantine::parseItem( '2026-10-16/images/a.jpg' ) );
        $this->assertNull( expContentCheckQuarantine::parseItem( '2026-10-16/../../etc/passwd' ) );
    }

    /** CS-10 */
    public function testRestoreAndDeleteInTheQuarantine()
    {
        $this->dir = sys_get_temp_dir() . '/cs10-' . bin2hex( random_bytes( 6 ) );
        $storage = $this->dir . '/site/storage';
        mkdir( $storage . '/images/a', 0777, true );
        expContentCheckStorage::setTestStorage( $storage );
        expContentCheckStorage::setTestState( false );
        $root = expContentCheckQuarantine::root();
        $this->assertSame( $this->dir . '/site/storage-quarantine', $root );
        mkdir( $root . '/2026-10-16/images/a', 0777, true );
        file_put_contents( $root . '/2026-10-16/images/a/one.jpg', 'one' );
        file_put_contents( $root . '/2026-10-16/images/a/two.jpg', 'two' );
        file_put_contents( $storage . '/images/a/two.jpg', 'already back' );
        file_put_contents( $this->dir . '/outside.txt', 'outside' );
        symlink( $this->dir . '/outside.txt', $root . '/2026-10-16/images/a/link.jpg' );

        $listing = expContentCheckQuarantine::listing();
        $this->assertSame( 2, $listing['count'] );
        $this->assertSame( array( '2026-10-16/images/a/one.jpg', '2026-10-16/images/a/two.jpg' ), array_column( $listing['items'], 'item' ) );

        $items = array( '2026-10-16/images/a/one.jpg', '2026-10-16/images/a/two.jpg', '2026-10-16/images/a/link.jpg', '2026-10-16/images/a/none.jpg' );
        $plan = expContentCheckQuarantine::plan( 'restore', $items );
        $this->assertSame( array( '', 'exists', 'link', 'not_found' ), array_column( $plan['items'], 'reason' ) );
        $this->assertSame( array( '2026-10-16/images/a/one.jpg' ), $plan['allowed'] );
        $this->assertFileExists( $root . '/2026-10-16/images/a/one.jpg', 'the plan changes nothing' );

        $this->assertSame( 'changed', expContentCheckQuarantine::execute( 'restore', $items, 'wrong' )['reason'] );
        $outcome = expContentCheckQuarantine::execute( 'restore', $items, expContentCheck::confirmToken( $plan['fingerprint'] ) );
        $this->assertSame( array( true, 1, 0 ), array( $outcome['done'], $outcome['count'], $outcome['failed'] ) );
        $this->assertSame( 'one', file_get_contents( $storage . '/images/a/one.jpg' ) );
        $this->assertSame( 'already back', file_get_contents( $storage . '/images/a/two.jpg' ), 'never overwritten' );

        $plan = expContentCheckQuarantine::plan( 'delete', array( '2026-10-16/images/a/two.jpg', '2026-10-16/images/a/link.jpg' ) );
        $this->assertSame( array( '2026-10-16/images/a/two.jpg' ), $plan['allowed'] );
        $outcome = expContentCheckQuarantine::execute( 'delete', array( '2026-10-16/images/a/two.jpg', '2026-10-16/images/a/link.jpg' ),
                                                       expContentCheck::confirmToken( $plan['fingerprint'] ) );
        $this->assertSame( 1, $outcome['count'] );
        $this->assertFileDoesNotExist( $root . '/2026-10-16/images/a/two.jpg' );
        $this->assertFileExists( $this->dir . '/outside.txt', 'a link is never followed' );
        $this->assertFileExists( $storage . '/images/a/two.jpg', 'the storage is never touched by a delete' );
    }

    /** CS-11 */
    public function testTheStorageViewParameters()
    {
        $p = Storage::parameters( array() );
        $this->assertSame( array( 'orphans', 'size', 'desc' ), array( $p['show'], $p['sort'], $p['order'] ) );
        $this->assertSame( expContentCheckStorage::PROBLEMS, $p['kinds'] );
        $p = Storage::parameters( array( 'Show' => 'problems', 'Sort' => 'x', 'Order' => 'up', 'Kind' => array( 'owner', 'owner', 'evil', array() ) ) );
        $this->assertSame( array( 'problems', 'size', 'desc', array( 'owner' ) ), array( $p['show'], $p['sort'], $p['order'], $p['kinds'] ) );
        $this->assertSame( '?Show=problems&Kind%5B%5D=owner', Storage::querySuffix( $p ) );
        $p = Storage::parameters( array( 'Show' => 'orphans', 'Sort' => 'path' ) );
        $this->assertSame( '?Show=orphans&Sort=path&Order=asc', Storage::querySuffix( $p ) );

        $state = array( 'orphans' => array( array( 'p' => 'b', 's' => 5, 'm' => 100 ), array( 'p' => 'a', 's' => 9, 'm' => 300 ), array( 'p' => 'c', 's' => 5, 'm' => 200 ) ) );
        $this->assertSame( array( 'a', 'b', 'c' ), array_column( expContentCheckStorage::sortedOrphans( $state, 'path', 'asc' ), 'p' ) );
        $this->assertSame( array( 'a', 'c', 'b' ), array_column( expContentCheckStorage::sortedOrphans( $state, 'size', 'desc' ), 'p' ) );
        // the age descending: the oldest first
        $this->assertSame( array( 'b', 'c', 'a' ), array_column( expContentCheckStorage::sortedOrphans( $state, 'age', 'desc' ), 'p' ) );
    }

    /** CS-12 */
    public function testTheScanStateAsShown()
    {
        $state = array( 'phase' => 'done' );
        $this->assertSame( 100, expContentCheckStorage::percent( $state ) );
        $this->assertSame( 85, expContentCheckStorage::percent( array( 'phase' => 'images' ) ) );

        $scan = array( 'format' => expContentCheckStorage::FORMAT, 'phase' => 'done',
                       'counts' => array( 'files' => 3, 'bytes' => 30, 'orphans' => 2, 'orphan_bytes' => 20, 'problems' => array( 'owner' => 1, 'mode' => 1, 'empty' => 1 ) ),
                       'orphans' => array( array( 'p' => 'images/a.jpg', 's' => 10 ), array( 'p' => 'images/b.jpg', 's' => 10 ) ),
                       'problems' => array( array( 'k' => 'owner', 'p' => 'images/c.jpg' ), array( 'k' => 'mode', 'p' => 'images/c.jpg' ), array( 'k' => 'empty', 'p' => 'images/c.jpg' ) ) );
        expContentCheckStorage::setTestState( $scan );
        expContentCheckStorage::forget( 'quarantine', array( 'images/a.jpg' ) );
        $after = expContentCheckStorage::lastScan();
        $this->assertSame( array( 'images/b.jpg' ), array_column( $after['orphans'], 'p' ) );
        $this->assertSame( array( 1, 10, 2, 20 ), array( $after['counts']['orphans'], $after['counts']['orphan_bytes'], $after['counts']['files'], $after['counts']['bytes'] ) );
        $this->assertArrayHasKey( 'changed', $after );
        expContentCheckStorage::forget( 'permissions', array( 'images/c.jpg' ) );
        $after = expContentCheckStorage::lastScan();
        $this->assertSame( array( 'empty' ), array_column( $after['problems'], 'k' ), 'an empty file is not fixed by owner and mode' );
        $this->assertSame( array( 0, 0, 1 ), array_values( $after['counts']['problems'] ) );
    }

    /** CS-13 */
    public function testTheClassFilterButtons()
    {
        $this->assertSame( array( 5, 12 ), \Exponential\View\Kernel\Contentcheck\Unreferenced::classChoice( array( 'ClassID' => array( '5' ) ), array( '5', 12 ), true, false ) );
        $this->assertSame( array(), \Exponential\View\Kernel\Contentcheck\Unreferenced::classChoice( array( 'ClassID' => array( '5' ) ), array( 5, 12 ), true, true ) );
        $this->assertSame( array( '5' ), \Exponential\View\Kernel\Contentcheck\Unreferenced::classChoice( array( 'ClassID' => array( '5' ) ), array( 5, 12 ), false, false ) );
        $this->assertSame( '5,12', \Exponential\View\Kernel\Contentcheck\Unreferenced::classChoice( array( 'ClassID' => '5,12' ), array( 5 ), false, false ) );
        $this->assertSame( array(), \Exponential\View\Kernel\Contentcheck\Unreferenced::classChoice( array(), array( 5 ), false, false ) );

        $root = dirname( __DIR__, 4 );
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            foreach ( array( 'unreferenced', 'storage', 'brokenrelations' ) as $view )
            {
                $tpl = (string)file_get_contents( $root . '/design/' . $design . '/templates/contentcheck/' . $view . '.tpl' );
                $show = strpos( $tpl, "{'Show'|i18n( 'kernel/contentcheck' )}</button>" );
                $this->assertNotFalse( $show, $design . ' ' . $view );
                $at = strpos( $tpl, "design:contentcheck/filter_choice.tpl" );
                $this->assertNotFalse( $at, $design . ' ' . $view );
                $this->assertGreaterThan( $show, $at, $design . ' ' . $view . ': the choice buttons after Show' );
                // one layout: the scope first, the results header, the pager; no list markup of its own
                $this->assertLessThan( $show, strpos( $tpl, 'design:contentcheck/scope.tpl' ), $design . ' ' . $view );
                $this->assertNotFalse( strpos( $tpl, 'design:contentcheck/results_head.tpl' ), $design . ' ' . $view );
                $this->assertNotFalse( strpos( $tpl, 'design:contentcheck/pager.tpl' ), $design . ' ' . $view );
                $this->assertFalse( strpos( $tpl, 'class="a4-listfoot"' ), $design . ' ' . $view . ': pager.tpl, no copy' );
            }
            $choice = (string)file_get_contents( $root . '/design/' . $design . '/templates/contentcheck/filter_choice.tpl' );
            $this->assertStringContainsString( 'Choice" value="all"', $choice );
        }
    }

    /** CS-14 */
    public function testAllResultsAlwaysPlanFirst()
    {
        $all = function () { return true; };
        $base = array( expContentCheck::TOKEN_FIELD => expContentCheck::formToken(), 'NodeID' => '43', 'Show' => 'orphans' );
        $d = Storageaction::decide( 'POST', $base + array( 'AllResultsAction' => 'quarantine' ), $all );
        $this->assertSame( array( 'confirm', 'quarantine', true ), array( $d['step'], $d['action'], $d['all_results'] ) );
        $d = Storageaction::decide( 'POST', $base + array( 'AllResultsAction' => 'quarantine', 'Action' => 'quarantine', 'Items' => array( 'images/a.jpg' ), 'ConfirmButton' => 1 ), $all );
        $this->assertSame( array( 'execute', false ), array( $d['step'], $d['all_results'] ), 'the confirmation posts the planned files' );
        $this->assertSame( 'cancel', Storageaction::decide( 'POST', $base + array( 'AllResultsAction' => 'chmod' ), $all )['step'] );
        $only = function ( $f ) { return $f === 'storage'; };
        $this->assertSame( 'policy', Storageaction::decide( 'POST', $base + array( 'AllResultsAction' => 'delete' ), $only )['reason'] );

        $post = array( expContentCheck::TOKEN_FIELD => expContentCheck::formToken(), 'NodeID' => '43', 'AllResultsButton' => 1 );
        $d = \Exponential\View\Kernel\Contentcheck\Trash::decide( 'POST', $post );
        $this->assertSame( array( 'confirm', true ), array( $d['step'], $d['all_results'] ) );
        $d = \Exponential\View\Kernel\Contentcheck\Trash::decide( 'POST', $post + array( 'ObjectIDList' => array( '5' ), 'ConfirmButton' => 1 ) );
        $this->assertSame( array( 'execute', false ), array( $d['step'], $d['all_results'] ) );
    }

    /** CS-15 */
    public function testTheScopeButtonsAndTheSitesRow()
    {
        // the quick buttons: Whole tree, Websites, Media by default; a node id or a path keeps the node's name
        $this->assertSame( array( 'tree', 'content', 'media' ), expContentCheckScope::DEFAULT_PRESETS );
        $this->assertSame( 'Websites', expContentCheckScope::presetLabel( 'content', 'Home' ) );
        $this->assertSame( 'Media', expContentCheckScope::presetLabel( 'media', 'Media files' ) );
        $this->assertSame( 'Whole tree', expContentCheckScope::presetLabel( 'tree', '' ) );
        $this->assertSame( 'Images', expContentCheckScope::presetLabel( 'media/images', 'Images' ) );
        $this->assertSame( 'Blog', expContentCheckScope::presetLabel( '58', 'Blog' ) );
        $this->assertSame( '', expContentCheckScope::presetLabel( array( 'content' ), '' ) );

        // the settings: on, under the content root, 12 at most, bounded
        $settings = expContentCheckScope::siteRowSettings();
        $this->assertTrue( $settings['enabled'] );
        $this->assertSame( 'content', $settings['parent'] );
        $this->assertGreaterThanOrEqual( 1, $settings['limit'] );
        $this->assertLessThanOrEqual( 50, $settings['limit'] );

        // the row: visible children only, in the order given (the content root's sort order), the first N, "More"
        $children = array();
        foreach ( range( 60, 75 ) as $id )
            $children[] = array( 'node_id' => $id, 'name' => 'Site ' . $id, 'is_hidden' => 0, 'is_invisible' => 0 );
        $children[1]['is_hidden'] = 1;
        $children[2]['is_invisible'] = 1;
        array_unshift( $children, array( 'node_id' => 0, 'name' => 'none' ), 'junk', array( 'node_id' => 61, 'name' => 'twice' ) );
        $row = expContentCheckScope::siteRow( $children, '/1/2/64/90/', 64, 12 );
        $ids = array_column( $row['items'], 'node_id' );
        $this->assertSame( array( 61, 60, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72 ), $ids );
        $this->assertSame( 'twice', $row['items'][0]['name'], 'a node comes once, the first time' );
        $this->assertTrue( $row['more'] );
        $this->assertTrue( $row['items'][3]['current'] );
        $this->assertFalse( $row['items'][3]['within'] );
        $row = expContentCheckScope::siteRow( $children, '/1/2/64/90/', 90, 50 );
        $this->assertFalse( $row['more'] );
        $this->assertSame( 15, count( $row['items'] ) );
        $within = array_values( array_filter( $row['items'], function ( $item ) { return $item['within']; } ) );
        $this->assertSame( array( 64 ), array_column( $within, 'node_id' ), 'the site the start location lies in' );
        $this->assertSame( array(), array_filter( array_column( $row['items'], 'current' ) ) );
        // /1/2/6/ must not mark 64 nor 60 by prefix
        $row = expContentCheckScope::siteRow( $children, '/1/2/6/', 6, 50 );
        $this->assertSame( array(), array_filter( array_column( $row['items'], 'within' ) ) );
        $this->assertSame( array( 'items' => array(), 'more' => false ), expContentCheckScope::siteRow( array(), '', 2, 0 ) );

        // the node picker: back to the check at the node, "More" opens it at another level
        $this->assertSame( '/contentcheck/scope/storage/64', expContentCheckScope::browseURL( 'storage', 64 ) );
        $this->assertSame( '/contentcheck/scope/storage/64/2', expContentCheckScope::browseURL( 'storage', 64, 2 ) );
        $this->assertSame( '/contentcheck/scope/storage/2', expContentCheckScope::browseURL( 'storage', 2, 2 ) );
        $this->assertSame( '', expContentCheckScope::browseURL( '../x', 2, 2 ) );

        // the links keep plain filters only, never NodeID
        $this->assertSame( '?Show=problems', expContentCheckScope::query( array( 'Show' => 'problems' ) ) );
        $this->assertSame( '', expContentCheckScope::query( array( 'Show' => '"><script>', 'NodeID' => '5', 'a b' => 'x', 'K' => array( 1 ) ) ) );
        $this->assertSame( '', expContentCheckScope::query( array() ) );

        // one shared part in both designs: plain links, the current one marked, no inline CSS, escaped names
        $root = dirname( __DIR__, 4 );
        $admin = (string)file_get_contents( $root . '/design/admin/templates/contentcheck/scope.tpl' );
        $this->assertSame( $admin, (string)file_get_contents( $root . '/design/admin4/templates/contentcheck/scope.tpl' ) );
        foreach ( array( 'aria-current="true"', '$site.name|wash', '$scope.sites.more_url|ezurl', 'a4-toolbar', 'a4-btn' ) as $needle )
            $this->assertStringContainsString( $needle, $admin );
        $this->assertDoesNotMatchRegularExpression( '/<style|style="|<script|onclick=/i', $admin );
    }
}
