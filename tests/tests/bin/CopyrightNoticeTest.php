<?php
/**
 * One copyright notice everywhere (#284, owner decision 2026-10-29): every line of the repository that names one of
 * the project's own holders (7x, the Exponential Foundation) next to a notice marker uses one of the two approved
 * forms:
 *
 *   files and metadata:  Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 *   shown to users:      Exponential Copyright © 1998-2026 Exponential Foundation and 7x
 *
 * A notice marker is "(C)", the copyright sign (also as &copy;), a year, "@copyright", "<copyright>",
 * "'Copyright' =>" or "Copyright=" / "Copyright:" on a line that has the word copyright. Lines that name only other
 * holders (eZ Systems AS, Mugo Web, Moxiecode, the Free Software Foundation, Unicode ...) are third-party notices:
 * the license requires them to stay and the test leaves them alone. The exceptions that do name 7x or the Foundation
 * (another author's notice, history, the books) are in copyright-notice-allowlist.json, each with its reason.
 *
 * The same rules are documented in doc/eng-us/help/copyright-and-copyleft.md. DB-less: reads the files of the
 * checkout (git ls-files when git is there, else a walk that leaves out vendor/ and var/).
 *
 * Run: php vendor/bin/phpunit tests/tests/bin/CopyrightNoticeTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class CopyrightNoticeTest extends PHPUnit\Framework\TestCase
{
    const HEADER = 'Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.';
    const VISIBLE = 'Exponential Copyright © 1998-2026 Exponential Foundation and 7x';

    const OWN = '/(?<![0-9A-Za-z])7x(?![0-9A-Za-z])|exponential foundation/i';
    const MARKER = '/\(c\)|©|&copy;|&amp;copy;|(?<![0-9])(19|20)[0-9]{2}(?![0-9])|@copyright|<copyright>|[\'"]copyright[\'"]\s*=>|copyright\s*[:=]/i';

    const NOTICE = '/(?:copyright|\(c\)|©|&(?:amp;)?copy;)(?:\s|[:=(),"\'„“]|\(c\)|©|&(?:amp;)?copy;)*(?:(?:19|20)[0-9]{2}\s*-\s*(?:19|20)[0-9]{2},?\s*|<year>\s*)?(?:(?:<[^<>]*>|&lt;.*?&gt;)\s*)*(?:the\s+)?(?:7x(?![0-9A-Za-z])|exponential foundation)/iu';

    private static $allowlist;

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function allowlist()
    {
        if ( self::$allowlist === null )
            self::$allowlist = json_decode( (string)file_get_contents( __DIR__ . '/copyright-notice-allowlist.json' ), true );
        return self::$allowlist;
    }

    /**
     * The line with its tags removed and its entities decoded (three rounds: a .ts catalogue escapes the HTML of a
     * template once more), whitespace collapsed.
     */
    public static function flatten( $text )
    {
        for ( $i = 0; $i < 3; $i++ )
            $text = preg_replace( '/<[^<>]*>/', '', html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
        return preg_replace( '/\s+/u', ' ', $text );
    }

    /**
     * 'header', 'visible', 'own-other', 'third-party', or null for a line that is no notice.
     */
    public static function classify( $line )
    {
        if ( stripos( $line, 'copyright' ) === false || !preg_match( self::MARKER, $line ) )
            return null;
        if ( !preg_match( self::OWN, $line ) )
            return 'third-party';
        $flat = self::flatten( $line );
        // a notice names the holder right after "Copyright" / "(C)" / the sign and the years; prose that only
        // mentions the holders and a year next to the word copyright is no notice
        if ( !preg_match( self::NOTICE, $line ) && !preg_match( self::NOTICE, $flat ) )
            return null;
        if ( strpos( $line, self::HEADER ) !== false || strpos( $flat, self::HEADER ) !== false )
            return 'header';
        if ( strpos( $flat, self::VISIBLE ) !== false )
            return 'visible';
        return 'own-other';
    }

    public static function allowed( $path, $line )
    {
        $allow = self::allowlist();
        foreach ( array_keys( $allow['skip'] ) as $pattern )
        {
            if ( fnmatch( $pattern, $path ) )
                return true;
        }
        foreach ( $allow['lines'] as $row )
        {
            if ( fnmatch( $row['path'], $path ) && strpos( $line, $row['contains'] ) !== false )
                return true;
        }
        return false;
    }

    private static function files()
    {
        $root = self::root();
        $out = array();
        if ( is_dir( $root . '/.git' ) || is_file( $root . '/.git' ) )
        {
            $list = shell_exec( 'git -c safe.directory=' . escapeshellarg( $root ) . ' -C ' . escapeshellarg( $root ) . ' ls-files -z 2>/dev/null' );
            if ( is_string( $list ) && $list !== '' )
                return array_values( array_filter( explode( "\0", $list ), 'strlen' ) );
        }
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ) );
        foreach ( $it as $file )
        {
            $rel = substr( $file->getPathname(), strlen( $root ) + 1 );
            if ( preg_match( '#^(vendor|var|\.git|node_modules)/#', $rel ) )
                continue;
            $out[] = $rel;
        }
        return $out;
    }

    public static function classifyProvider()
    {
        return array(
            'approved header' => array( ' * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.', 'header' ),
            'approved header in COPYRIGHT NOTICE' => array( '// COPYRIGHT NOTICE: Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.', 'header' ),
            'approved header in extension.xml' => array( '<copyright>Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved. Copyright (C) eZ Systems AS. All rights reserved.</copyright>', 'header' ),
            'visible notice in a template' => array( '{\'<a href="%ezpublish_link">Exponential</a> Copyright &copy; 1998-2026 <a href="%foundation_link">Exponential Foundation</a> and <a href="%7x_link">7x</a>\'|i18n( \'design/admin/page_copyright\' )}', 'visible' ),
            'visible notice in a catalogue' => array( '<source>&lt;a href=&quot;%ezpublish_link&quot;&gt;Exponential&lt;/a&gt; Copyright &amp;copy; 1998-2026 &lt;a href=&quot;%foundation_link&quot;&gt;Exponential Foundation&lt;/a&gt; and &lt;a href=&quot;%7x_link&quot;&gt;7x&lt;/a&gt;</source>', 'visible' ),
            'visible notice plain' => array( 'Exponential Copyright © 1998-2026 Exponential Foundation and 7x', 'visible' ),
            'old: 7x & Exponential Foundation' => array( ' * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.', 'own-other' ),
            'old: 7x and the Exponential Foundation' => array( ' * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.', 'own-other' ),
            'old: 7x / Exponential Foundation' => array( 'Copyright (C) 1998 - 2026 7x / Exponential Foundation. All rights reserved.', 'own-other' ),
            'old: 7x alone' => array( ' * @copyright Copyright (C) 7x. All rights reserved.', 'own-other' ),
            'old: 7x and others' => array( 'Exponential Copyright &copy; 1998-2026 <a href="https://se7enx.com">7x</a> and others.', 'own-other' ),
            'old order of the holders' => array( 'Exponential Copyright © 1998-2026 7x and Exponential Foundation', 'own-other' ),
            'third party: eZ Systems' => array( ' * @copyright Copyright (C) eZ Systems AS. All rights reserved.', 'third-party' ),
            'third party: FSF' => array( ' Copyright (C) 1989, 1991 Free Software Foundation, Inc.', 'third-party' ),
            'notice without the approved wording' => array( 'Copyright: 1998 - 2026 Exponential Foundation and 7x; 1999 - 2014 eZ Systems AS', 'own-other' ),
            'prose with years' => array( '| The copyright notice runs from 1998 to 2026 for the Exponential Foundation and 7x | `COPYRIGHT.md` |', null ),
            'another author with the Foundation' => array( ' * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.', null ),
            'prose without a marker' => array( 'The copyright belongs to the Exponential Foundation and 7x together.', null ),
            'no copyright word' => array( 'Exponential Foundation and 7x, 1998 - 2026', null ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('classifyProvider')]
    public function testClassify( $line, $expected )
    {
        $this->assertSame( $expected, self::classify( $line ) );
    }

    public function testAllowlistIsValid()
    {
        $allow = self::allowlist();
        $this->assertIsArray( $allow, 'copyright-notice-allowlist.json is JSON' );
        $this->assertSame( self::HEADER, $allow['approved']['header'] );
        $this->assertSame( self::VISIBLE, $allow['approved']['visible'] );
        foreach ( $allow['skip'] as $pattern => $reason )
            $this->assertNotSame( '', trim( $reason ), "skip $pattern has a reason" );
        foreach ( $allow['lines'] as $row )
        {
            $this->assertNotSame( '', trim( (string)$row['reason'] ), $row['contains'] . ' has a reason' );
            // an allowlist entry may never let one of the project's own old notices through
            foreach ( array( '7x & Exponential Foundation', '7x and the Exponential Foundation', '7x / Exponential Foundation', '7x and others' ) as $old )
                $this->assertStringNotContainsString( $old, $row['contains'], 'the allowlist does not excuse an old own notice' );
        }
    }

    public function testEveryOwnNoticeUsesAnApprovedForm()
    {
        $root = self::root();
        $bad = array();
        $checked = 0;
        foreach ( self::files() as $path )
        {
            $full = $root . '/' . $path;
            if ( is_link( $full ) || !is_file( $full ) || filesize( $full ) > 20000000 )
                continue;
            $data = (string)file_get_contents( $full );
            if ( strpos( substr( $data, 0, 8192 ), "\0" ) !== false )
                continue;
            if ( stripos( $data, 'copyright' ) === false )
                continue;
            foreach ( preg_split( '/\R/', $data ) as $no => $line )
            {
                $kind = self::classify( $line );
                if ( $kind === null )
                    continue;
                $checked++;
                if ( $kind === 'own-other' && !self::allowed( $path, $line ) )
                    $bad[] = $path . ':' . ( $no + 1 ) . ': ' . substr( trim( $line ), 0, 160 );
            }
        }
        $this->assertGreaterThan( 1000, $checked, 'the scan found the notices of the repository' );
        $this->assertSame( array(), array_slice( $bad, 0, 100 ),
            count( $bad ) . ' copyright line(s) name 7x or the Exponential Foundation in a form that is not approved; use "'
            . self::HEADER . '" in files or "' . self::VISIBLE . '" where users see it (doc/eng-us/help/copyright-and-copyleft.md)' );
    }
}
