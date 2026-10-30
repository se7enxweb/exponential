<?php
/**
 * The help classes as a content class package (expHelpContentPackage, CJW #16564), without a database: the
 * package's definition comes from the schema, and a package written in the kernel's format (package.xml and
 * ezcontentclass/class-<identifier>.xml, as eZContentClassPackageHandler writes them) is compared with the schema.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use PHPUnit\Framework\TestCase;

class expHelpContentPackageTest extends TestCase
{
    /** @var string */
    private $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/exphelp-package-' . bin2hex( random_bytes( 6 ) );
        mkdir( $this->dir . '/' . expHelpContentPackage::NAME . '/ezcontentclass', 0700, true );
    }

    protected function tearDown(): void
    {
        $remove = function ( $path ) use ( &$remove ) {
            if ( is_dir( $path ) && !is_link( $path ) )
            {
                foreach ( array_diff( scandir( $path ), array( '.', '..' ) ) as $entry )
                    $remove( $path . '/' . $entry );
                rmdir( $path );
            }
            elseif ( file_exists( $path ) || is_link( $path ) )
                unlink( $path );
        };
        $remove( $this->dir );
    }

    /** A package directory in the kernel's format, from the schema; $change( identifier, DOMDocument ) may alter a class */
    private function writePackage( ?callable $change = null, array $skip = array() )
    {
        $packageDir = $this->dir . '/' . expHelpContentPackage::NAME;
        $provides = '';
        $items = '';
        foreach ( expHelpContentSchema::classes() as $identifier => $definition )
        {
            if ( in_array( $identifier, $skip, true ) )
                continue;
            $provides .= '<provide type="ezcontentclass" name="contentclass" value="' . $identifier . '"/>';
            $items .= '<item type="ezcontentclass" filename="class-' . $identifier . '" sub-directory="ezcontentclass"/>';
            $doc = new DOMDocument( '1.0', 'utf-8' );
            $class = $doc->createElement( 'content-class' );
            $doc->appendChild( $class );
            $class->setAttribute( 'is-container', $definition['container'] ? 'true' : 'false' );
            $class->appendChild( $doc->createElement( 'identifier', $identifier ) );
            $remote = $doc->createElement( 'remote' );
            $groups = $doc->createElement( 'groups' );
            $group = $doc->createElement( 'group' );
            $group->setAttribute( 'id', '7' );
            $group->setAttribute( 'name', expHelpContentSchema::CLASS_GROUP );
            $groups->appendChild( $group );
            $remote->appendChild( $groups );
            $class->appendChild( $remote );
            $attributes = $doc->createElementNS( 'http://ezpublish/contentclassattribute', 'ezcontentclass-attribute:attributes' );
            $class->appendChild( $attributes );
            foreach ( $definition['attributes'] as $name => $attribute )
            {
                $node = $doc->createElement( 'attribute' );
                $node->setAttribute( 'datatype', $attribute['type'] );
                $node->setAttribute( 'required', $attribute['required'] ? 'true' : 'false' );
                $node->appendChild( $doc->createElement( 'identifier', $name ) );
                $attributes->appendChild( $node );
            }
            if ( $change )
                $change( $identifier, $doc );
            file_put_contents( $packageDir . '/ezcontentclass/class-' . $identifier . '.xml', $doc->saveXML() );
        }
        file_put_contents( $packageDir . '/package.xml', '<?xml version="1.0" encoding="utf-8"?><package version="3.5.2">'
            . '<name>' . expHelpContentPackage::NAME . '</name><type value="contentclass"/><dependencies><provides>' . $provides
            . '</provides></dependencies><install>' . $items . '</install></package>' );
        return $packageDir;
    }

    public function testDefinitionFromTheSchema()
    {
        $this->assertSame( array( 'help_manual', 'help_section', 'help_concept', 'help_task', 'help_reference', 'help_troubleshooting',
                                  'help_screenshot', 'help_glossary_entry' ), expHelpContentPackage::provides() );
        $parameters = expHelpContentPackage::parameters( '6.0.15' );
        $this->assertSame( 'contentclass', $parameters['type'] );
        $this->assertSame( '7x', $parameters['vendor'] );
        $this->assertSame( '6.0', $parameters['version-number'] );
        $this->assertSame( '15', $parameters['release-number'] );
        $this->assertSame( 'exp_help_classes-6.0.15.ezpkg', expHelpContentPackage::fileName( '6.0.15' ) );
        $this->assertSame( '6.1.0', expHelpContentPackage::release( '6.1' )['version'] );
        $this->assertMatchesRegularExpression( '/^exp_help_classes-\d+\.\d+\.\d+\.ezpkg$/', expHelpContentPackage::fileName() );
        $this->expectException( InvalidArgumentException::class );
        expHelpContentPackage::release( 'latest' );
    }

    public function testAPackageThatMatchesTheSchemaPasses()
    {
        $this->assertSame( array(), expHelpContentPackage::verifyDirectory( $this->writePackage() ) );
    }

    public function testDifferencesAreReported()
    {
        $dir = $this->writePackage( function ( $identifier, DOMDocument $doc ) {
            if ( $identifier !== 'help_task' )
                return;
            foreach ( iterator_to_array( $doc->getElementsByTagName( 'attribute' ) ) as $attribute )
            {
                $name = $attribute->getElementsByTagName( 'identifier' )->item( 0 )->textContent;
                if ( $name === 'steps' )
                    $attribute->parentNode->removeChild( $attribute );
                if ( $name === 'help_id' )
                    $attribute->setAttribute( 'datatype', 'eztext' );
                if ( $name === 'audience' )
                    $attribute->setAttribute( 'required', 'false' );
            }
            $doc->getElementsByTagName( 'group' )->item( 0 )->setAttribute( 'name', 'Content' );
        }, array( 'help_glossary_entry' ) );
        $problems = implode( "\n", expHelpContentPackage::verifyDirectory( $dir ) );
        $this->assertStringContainsString( 'help_task: no attribute steps', $problems );
        $this->assertStringContainsString( 'help_task/help_id: datatype eztext, the schema has ezstring', $problems );
        $this->assertStringContainsString( 'help_task/audience: required should be true', $problems );
        $this->assertStringContainsString( 'help_task: not in the class group Help', $problems );
        $this->assertStringContainsString( 'does not provide the class help_glossary_entry', $problems );
        $this->assertStringNotContainsString( 'help_concept', $problems );
        $this->assertSame( array( 'No package.xml' ), expHelpContentPackage::verifyDirectory( $this->dir . '/nothing' ) );
    }

    public function testTheArchiveIsReadWithoutUnpacking()
    {
        $dir = $this->writePackage();
        // a ustar archive as an export writes it: the files below a directory named after the package; one name
        // longer than 100 bytes takes the GNU long name entry
        $tar = '';
        $entry = function ( $name, $content, $type = '0' ) {
            $header = str_pad( substr( $name, 0, 100 ), 100, "\0" ) . str_pad( '0000644', 8, "\0" ) . str_pad( '0000000', 8, "\0" )
                    . str_pad( '0000000', 8, "\0" ) . str_pad( sprintf( '%011o', strlen( $content ) ), 12, "\0" )
                    . str_pad( sprintf( '%011o', 0 ), 12, "\0" ) . '        ' . $type . str_repeat( "\0", 100 ) . "ustar\0" . '00';
            $header = str_pad( $header, 512, "\0" );
            $sum = 0;
            for ( $i = 0; $i < 512; $i++ )
                $sum += ord( $header[$i] );
            $header = substr_replace( $header, sprintf( "%06o\0 ", $sum ), 148, 8 );
            return $header . str_pad( $content, (int)ceil( strlen( $content ) / 512 ) * 512, "\0" );
        };
        $tar .= $entry( expHelpContentPackage::NAME . '/package.xml', file_get_contents( $dir . '/package.xml' ) );
        foreach ( glob( $dir . '/ezcontentclass/*.xml' ) as $file )
        {
            $name = expHelpContentPackage::NAME . '/ezcontentclass/' . basename( $file );
            if ( basename( $file ) === 'class-help_glossary_entry.xml' )
            {
                // the name from a GNU long name entry wins over the (here shortened) name of the header
                $tar .= $entry( '././@LongLink', $name . "\0", 'L' );
                $tar .= $entry( 'shortened', file_get_contents( $file ) );
                continue;
            }
            $tar .= $entry( $name, file_get_contents( $file ) );
        }
        $tar .= str_repeat( "\0", 1024 );
        file_put_contents( $this->dir . '/exp_help_classes-6.0.15.ezpkg', gzencode( $tar ) );

        $files = expHelpContentPackage::readArchive( $this->dir . '/exp_help_classes-6.0.15.ezpkg' );
        $this->assertCount( 9, $files );
        $this->assertSame( array(), expHelpContentPackage::verifyArchive( $this->dir . '/exp_help_classes-6.0.15.ezpkg' ) );
        $this->assertSame( array( 'Not a package archive (a gzip compressed tar file)' ), expHelpContentPackage::verifyArchive( $dir . '/package.xml' ) );
        $this->assertSame( array( 'No file ' . $this->dir . '/none.ezpkg' ), expHelpContentPackage::verifyArchive( $this->dir . '/none.ezpkg' ) );
    }
}
