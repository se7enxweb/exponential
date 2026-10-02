<?php
/**
 * File containing the Exponential\Command\Kernel\Ini class, the exp:ini command (bin/php/ini.php).
 *
 * @description Read and change INI settings in every scope: get, set, add, rem, clear, toggle, copy, move, where, list, scopes
 * Guide: doc/bc/6.0/console-exp-ini.md
 *
 * The command starts the script, parses the options and hands the rest to expIniCommand::dispatch(), which
 * finds the action in ini.ini [IniCommandSettings] Actions[] and runs it (kernel/classes/ini/actions/).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel;

class Ini extends \Exponential\Runnable\Command
{
    public function run()
    {
        $argv = array_slice( isset( $_SERVER['argv'] ) ? $_SERVER['argv'] : array(), 1 );
        list( $forOptions, $literal, $wantsHelp ) = \expIniCommand::splitArguments( $argv );

        $cli = $this->cli();
        $this->script( array( 'description' => "Read and change INI settings in every scope (exp:ini --help)",
                              'use-session' => false,
                              'use-modules' => false,
                              'use-extensions' => true ) );
        if ( $wantsHelp )
        {
            // The help is readable without --allow-root-user (as "console help exp:ini" asks for it) and lists
            // the actions extensions register, so the extensions are loaded but the options are not parsed:
            // the words that are not options say which action's help to show.
            // eZScript::getOptions() would refuse root without --allow-root-user (and warns with no standard options)
            $this->script()->startup();
            $this->script()->initialize();
            $words = array_values( array_filter( $forOptions, function ( $a ) { return $a === '' || $a[0] !== '-'; } ) );
            $out = function ( $line ) { fwrite( STDOUT, $line . "\n" ); };
            $this->shutdown( \expIniCommand::dispatch( $words, \expIniCommandContext::defaults(), true, $out, $out ) );
        }

        $options = $this->startup( \expIniCommand::OPTION_CONFIG, '', \expIniCommand::optionHelp(), $forOptions );

        $code = \expIniCommand::dispatch(
            array_merge( $options['arguments'], $literal ),
            \expIniCommand::contextOptions( $options ),
            $wantsHelp,
            function ( $line ) use ( $cli ) { $cli->output( $line ); },
            function ( $line ) use ( $cli ) { $cli->error( $line ); }
        );

        $this->shutdown( $code );
    }
}
