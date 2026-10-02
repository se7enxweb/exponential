<?php
/**
 * The code of bin/php/ezconvert2isbn13.php, moved into a class (#207 stage 1). The file bin/php/ezconvert2isbn13.php is one call to it.
 * @description Convert ISBN-10 numbers to ISBN-13
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/ezconvert2isbn13.php:
 *
 *
 * File containing the ezconvert2isbn13.php script.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\Command\Kernel
{

class Ezconvert2isbn13 extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'allClasses', 'allClassesStatus', 'attributeID', 'attributeStatus', 'classID', 'classStatus', 'cli', 'converter', 'force', 'found', 'options', 'params', 'script' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = $this->cli();
        $script = $this->script( array( 'description' => ( "Exponential ISBN-10 to ISBN-13 converter\n\n" .
                                                                "Converts an ISBN-10 number to ISBN-13\n" ),
                                             'use-session' => false,
                                             'use-modules' => true,
                                             'use-extensions' => true ) );

        $script->startup();

        $options = $this->options( "[class-id:][attribute-id:][all-classes][f|force]",
                                        "",
                                        array( 'class-id' => 'The class id for the ISBN attribute.',
                                               'attribute-id' => 'The attribute id for the ISBN attribute which should be converted.',
                                               'all-classes' => 'Will convert all ISBN attributes in all content classes.',
                                               'f' => 'Short alias for force.',
                                               'force' => 'Will convert all attributes even if the class is set to ISBN.' ) );
        $script->initialize();

        $classID = $options['class-id'];
        $attributeID = $options['attribute-id'];
        $allClasses = $options['all-classes'];
        $force = $options['force'];

        $params = array( 'force' => $force );
        $converter = new \eZISBN10To13Converter( $script, $cli, $params );

        $found = false;
        if ( $allClasses === true )
        {
            $allClassesStatus = $converter->addAllClasses();
            $found = true;
        }
        else
        {
            if ( is_numeric( $classID ) )
            {
                $classStatus = $converter->addClass( $classID );
                $found = true;
            }

            if ( is_numeric( $attributeID ) )
            {
                $attributeStatus = $converter->addAttribute( $attributeID );
                $found = true;
            }
        }

        if ( $found == true )
        {
            if ( $converter->attributeCount() > 0 )
            {
                $converter->execute();
            }
            else
            {
                $cli->output( 'Did not find any ISBN attributes.' );
            }
        }
        else
        {
            $script->showHelp();
        }

        $script->shutdown();
    }
}

}
