<?php
/**
 * The resolutions of the missing files that need no database (doc/eng-us/features/6.0/content-checks.md, "Resolving"): the
 * choice of a restore source, what a replacement may be, where files may be written, the POST path of
 * contentcheck/fixfiles, the audit names and the templates.
 *
 *  CX-01  the source of a restore: the published version first, then the newest, never the missing path itself
 *  CX-02  a replacement: the same extension, never a script or SVG, an image must be an image, a size limit
 *  CX-03  writes stay in the storage directory; an image's XML loses only the named aliases
 *  CX-04  contentcheck/fixfiles: another method than POST is refused (405) before anything else, a POST without
 *         the form token is refused (403), a row's button and the bulk form are read, Continue and Cancel; the way
 *         back is the broken relations with the missing files ticked
 *  CX-05  the audit names are in the taxonomy, valid and on, with a verb; the templates exist in both designs
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Contentcheck\Fixfiles;

class expContentCheckFileFixTest extends PHPUnit\Framework\TestCase
{
    const IMAGE_XML = '<?xml version="1.0" encoding="utf-8"?>
<ezimage serial_number="1" is_valid="1" filename="a.jpg" suffix="jpg" basename="a" dirpath="var/site/storage/images/a/12-1-eng-GB"
 url="var/site/storage/images/a/12-1-eng-GB/a.jpg" original_filename="photo.jpg" mime_type="image/jpeg" width="10" height="10"
 alternative_text="" alias_key="1293033771" timestamp="1700000000">
  <original attribute_id="12" attribute_version="1" attribute_language="eng-GB"/>
  <alias name="small" filename="a_small.jpg" suffix="jpg" dirpath="var/site/storage/images/a/12-1-eng-GB"
   url="var/site/storage/images/a/12-1-eng-GB/a_small.jpg" mime_type="image/jpeg" width="5" height="5" alias_key="111" timestamp="1700000100" is_valid="1"/>
  <alias name="large" url="var/site/storage/images/a/12-1-eng-GB/a_large.jpg" alias_key="222" timestamp="1700000100" is_valid="1"/>
  <alias name="small" url="var/site/storage/images/a/12-1-eng-GB/a_small2.jpg" alias_key="111" timestamp="1" is_valid="1"/>
  <alias name="evil" url="../../etc/passwd" alias_key="333" timestamp="1" is_valid="1"/>
</ezimage>';

    /** CX-01 */
    public function testRestoreSource()
    {
        $rows = array( array( 'id' => 12, 'version' => 1, 'version_status' => 3 ),
                       array( 'id' => 12, 'version' => 2, 'version_status' => 1 ),
                       array( 'id' => 12, 'version' => 4, 'version_status' => 0 ),
                       array( 'id' => 12, 'version' => 5, 'version_status' => 0 ) );
        $file = function ( $path ) { return array( array( 'role' => 'original', 'alias' => '', 'path' => $path, 'alias_key' => '', 'timestamp' => 0, 'name' => '' ) ); };
        $files = array( '12-1' => $file( 'v1.jpg' ), '12-2' => $file( 'v2.jpg' ), '12-4' => $file( 'v4.jpg' ), '12-5' => $file( 'missing.jpg' ) );
        $all = function ( $path ) { return $path !== 'gone.jpg'; };
        $this->assertSame( array( 'version' => 2, 'path' => 'v2.jpg' ), expContentCheckFileFix::chooseSource( $rows, $files, 'missing.jpg', $all ) );
        $noPublished = function ( $path ) { return $path !== 'v2.jpg'; };
        $this->assertSame( array( 'version' => 4, 'path' => 'v4.jpg' ), expContentCheckFileFix::chooseSource( $rows, $files, 'missing.jpg', $noPublished ),
                           'then the newest; never the missing path itself' );
        $none = function ( $path ) { return false; };
        $this->assertNull( expContentCheckFileFix::chooseSource( $rows, $files, 'missing.jpg', $none ) );
    }

    /** CX-02 */
    public function testReplacement()
    {
        $t = 'var/site/storage/original/application/report.pdf';
        $this->assertSame( '', expContentCheckFileFix::checkReplacement( $t, 'new.PDF', 10, '', false ) );
        $this->assertSame( 'wrong_type', expContentCheckFileFix::checkReplacement( $t, 'new.doc', 10, '', false ) );
        $this->assertSame( 'wrong_type', expContentCheckFileFix::checkReplacement( $t, 'new', 10, '', false ) );
        $this->assertSame( 'no_file', expContentCheckFileFix::checkReplacement( $t, 'new.pdf', 0, '', false ) );
        foreach ( array( 'php', 'phtml', 'phar', 'html', 'svg', 'js' ) as $ext )
            $this->assertSame( 'wrong_type', expContentCheckFileFix::checkReplacement( 'var/site/storage/x.' . $ext, 'y.' . $ext, 10, '', false ), $ext );
        $i = 'var/site/storage/images/a/12-1-eng-GB/a.jpg';
        $this->assertSame( '', expContentCheckFileFix::checkReplacement( $i, 'b.jpg', 10, 'image/jpeg', true ) );
        $this->assertSame( 'wrong_type', expContentCheckFileFix::checkReplacement( $i, 'b.jpg', 10, '', true ), 'not an image' );
        $this->assertSame( 'wrong_type', expContentCheckFileFix::checkReplacement( $i, 'b.jpg', 10, 'image/svg+xml', true ) );
        $this->assertSame( 'wrong_type', expContentCheckFileFix::checkReplacement( $i, 'b.png', 10, 'image/png', true ) );
    }

    /** CX-03 */
    public function testStoragePathAndAliasRemoval()
    {
        $this->assertTrue( expContentCheckFileFix::storagePath( 'var/site/storage/images/a/b.jpg', 'var/site/storage' ) );
        $this->assertFalse( expContentCheckFileFix::storagePath( 'var/site/storage/../../index.php', 'var/site/storage' ) );
        $this->assertFalse( expContentCheckFileFix::storagePath( 'var/site/storagex/a.jpg', 'var/site/storage' ) );
        $this->assertFalse( expContentCheckFileFix::storagePath( 'settings/site.ini', 'var/site/storage' ) );
        $this->assertFalse( expContentCheckFileFix::storagePath( 'var/site/storage/a.jpg', '' ) );
        $xml = expContentCheckFileFix::withoutAliases( self::IMAGE_XML, array( 'small' ) );
        $files = expContentCheckFileImage::filesOfXML( $xml );
        $this->assertSame( array( '', 'large', 'evil' ), array_column( $files, 'alias' ) );
        $this->assertSame( 'var/site/storage/images/a/12-1-eng-GB/a.jpg', $files[0]['path'], 'the original stays' );
        $this->assertNull( expContentCheckFileFix::withoutAliases( 'not xml', array( 'small' ) ) );
        $this->assertSame( 'var/site/storage/original/application/x.pdf', expContentCheckFileTable::path( 'application/pdf', 'x.pdf', 'var/site/storage' ) );
        $this->assertSame( 'var/site/storage/original/application/x.pdf', expContentCheckFileTable::path( 'application/pdf', '../../x.pdf', 'var/site/storage' ) );
        $this->assertSame( 'var/site/storage/original//x.pdf', expContentCheckFileTable::path( '../pdf', 'x.pdf', 'var/site/storage' ) );
        $this->assertSame( '', expContentCheckFileTable::path( 'application/pdf', '', 'var/site/storage' ) );
    }

    /** CX-04 */
    public function testTheFixViewPostPath()
    {
        foreach ( array( 'GET', 'HEAD', 'PUT', '' ) as $method )
        {
            $d = Fixfiles::decide( $method, array( 'FixAction' => 'remove', 'Keys' => array( '12-1' ), 'ConfirmButton' => 1, 'NodeID' => 58 ), 'tok' );
            $this->assertSame( 'refused', $d['step'], $method );
            $this->assertSame( 'method', $d['reason'], $method );
            $this->assertSame( array(), $d['keys'] );
        }
        $d = Fixfiles::decide( 'POST', array( 'FixAction' => 'remove', 'Keys' => array( '12-1' ), 'ConfirmButton' => 1 ), 'tok' );
        $this->assertSame( array( 'refused', 'token' ), array( $d['step'], $d['reason'] ), 'without the form token' );
        $d = Fixfiles::decide( 'POST', array( 'ContentCheckToken' => 'tox', 'FixAction' => 'remove', 'Keys' => array( '12-1' ), 'ConfirmButton' => 1 ), 'tok' );
        $this->assertSame( 'token', $d['reason'], 'with a wrong form token' );

        $post = array( 'ContentCheckToken' => 'tok', 'NodeID' => '58' );
        $d = Fixfiles::decide( 'POST', $post + array( 'ItemAction' => 'regenerate:12-3', 'FixAction' => 'remove', 'Keys' => array( '1-1' ) ), 'tok' );
        $this->assertSame( array( 'confirm', 'regenerate', array( '12-3' => array( 12, 3 ) ), 58 ),
                           array( $d['step'], $d['action'], $d['keys'], $d['node_id'] ), 'a row button wins over the bulk form' );
        $d = Fixfiles::decide( 'POST', $post + array( 'FixAction' => 'restore', 'Keys' => array( '12-3', '14-1', 'x' ), 'PreviewButton' => 1 ), 'tok' );
        $this->assertSame( array( 'confirm', 'restore', array( '12-3', '14-1' ) ), array( $d['step'], $d['action'], array_keys( $d['keys'] ) ) );
        $d = Fixfiles::decide( 'POST', $post + array( 'FixAction' => 'delete', 'Keys' => array( '12-3' ), 'ConfirmButton' => 1 ), 'tok' );
        $this->assertSame( array( 'confirm', '' ), array( $d['step'], $d['action'] ), 'an unknown action is never executed' );
        $d = Fixfiles::decide( 'POST', $post + array( 'ItemAction' => 'review:12-3; DROP', 'ConfirmButton' => 1 ), 'tok' );
        $this->assertSame( array( '', array() ), array( $d['action'], $d['keys'] ) );
        $d = Fixfiles::decide( 'POST', $post + array( 'FixAction' => 'review', 'Keys' => array( '12-3' ), 'ConfirmButton' => 1 ), 'tok' );
        $this->assertSame( 'execute', $d['step'] );
        $d = Fixfiles::decide( 'POST', $post + array( 'FixAction' => 'review', 'Keys' => array( '12-3' ), 'ContinueButton' => 1, 'Done' => '10',
                                                       'Failed' => '2', 'Total' => '40', 'AutoContinue' => '1' ), 'tok' );
        $this->assertSame( array( 'execute', 10, 2, 40, true ), array( $d['step'], $d['done'], $d['failed'], $d['total'], $d['auto'] ) );
        $d = Fixfiles::decide( 'POST', $post + array( 'FixAction' => 'review', 'Keys' => array( '12-3' ), 'ConfirmButton' => 1, 'Done' => '-1' ), 'tok' );
        $this->assertSame( 0, $d['done'] );
        $d = Fixfiles::decide( 'POST', $post + array( 'CancelButton' => 1, 'ConfirmButton' => 1, 'FixAction' => 'remove', 'Keys' => array( '12-3' ) ), 'tok' );
        $this->assertSame( 'cancel', $d['step'] );
        // "Preview for all results": always the preview first, never executed from the list
        $d = Fixfiles::decide( 'POST', $post + array( 'FixAction' => 'regenerate', 'AllResultsButton' => 1, 'Keys' => array( '12-3' ) ), 'tok' );
        $this->assertSame( array( 'confirm', true ), array( $d['step'], $d['all_results'] ) );
        $d = Fixfiles::decide( 'POST', $post + array( 'FixAction' => 'regenerate', 'AllResultsButton' => 1, 'ConfirmButton' => 1, 'Keys' => array( '12-3' ) ), 'tok' );
        $this->assertSame( array( 'execute', false ), array( $d['step'], $d['all_results'] ), 'the confirmation posts the planned rows' );
        $d = Fixfiles::decide( 'POST', $post + array( 'FixAction' => 'nonsense', 'AllResultsButton' => 1 ), 'tok' );
        $this->assertFalse( $d['all_results'] );
        $back = Fixfiles::backURI( 58 );
        $this->assertStringStartsWith( '/contentcheck/brokenrelations/58?', $back );
        $this->assertStringContainsString( 'Kind%5B%5D=deleted', $back );
        $this->assertStringEndsWith( 'Kind%5B%5D=missing_file', $back );
    }

    /** CX-05 */
    public function testAuditNamesAndTemplates()
    {
        $catalogue = expAuditTaxonomy::catalogue();
        foreach ( expContentCheckFileFix::AUDIT_EVENTS as $action => $name )
        {
            $this->assertContains( $action, expContentCheckFileFix::ACTIONS );
            $this->assertTrue( expAuditTaxonomy::isValidName( $name ), $name );
            $this->assertArrayHasKey( $name, $catalogue, $name );
            $this->assertSame( 'on', $catalogue[$name]['default'], $name );
        }
        $this->assertSame( expContentCheckFileFix::ACTIONS, array_keys( expContentCheckFileFix::AUDIT_EVENTS ) );
        $root = dirname( __DIR__, 4 ) . '/';
        foreach ( array( 'fixconfirm', 'fixprogress', 'fixrefused', 'fixreason', 'missingfiles_filter', 'file_row' ) as $name )
        {
            $admin4 = $root . 'design/admin4/templates/contentcheck/' . $name . '.tpl';
            $this->assertFileExists( $admin4 );
            $this->assertFileEquals( $admin4, $root . 'design/admin/templates/contentcheck/' . $name . '.tpl', $name . ': the same in both designs' );
            $this->assertDoesNotMatchRegularExpression( '/<style|style="/i', (string)file_get_contents( $admin4 ), $name . ': no inline CSS' );
        }
        $this->assertFileExists( $root . 'design/standard/javascript/contentcheck_fixfiles.js' );
    }
}
