<?php
/**
 * The views of the class module moved from the namespace Exponential\View\Kernel\Class (a reserved word before
 * PHP 8.0) to Exponential\View\Kernel\ContentClass in 6.0.15. The old names keep working.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expRenamedClassViewNamespaceTest extends PHPUnit\Framework\TestCase
{
    public function testTheNewNameIsTheClassOfTheModuleView()
    {
        $this->assertTrue( class_exists( 'Exponential\\View\\Kernel\\ContentClass\\Edit' ) );
        $source = file_get_contents( __DIR__ . '/../../../../kernel/class/edit.php' );
        $this->assertStringContainsString( '\\Exponential\\View\\Kernel\\ContentClass\\Edit::main(', $source );
    }

    public function testTheOldNameLoadsTheSameClass()
    {
        $this->assertTrue( class_exists( 'Exponential\\View\\Kernel\\Class\\Grouplist' ), 'autoloaded by its old name' );
        $old = new ReflectionClass( 'Exponential\\View\\Kernel\\Class\\Grouplist' );
        $this->assertSame( 'Exponential\\View\\Kernel\\ContentClass\\Grouplist', $old->getName() );
    }

    public function testEveryViewKeepsItsOldName()
    {
        foreach ( glob( __DIR__ . '/../../../../kernel/private/classes/views/class/*.php' ) as $file )
        {
            $source = file_get_contents( $file );
            $this->assertMatchesRegularExpression( '/^namespace Exponential\\\\View\\\\Kernel\\\\ContentClass$/m', $source, basename( $file ) );
            $this->assertStringContainsString( "'Exponential\\\\View\\\\Kernel\\\\Class\\\\", $source, basename( $file ) . ' registers the old name' );
        }
    }
}
