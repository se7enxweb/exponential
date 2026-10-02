<?php
/**
 * The code of bin/php/ezgeneratetranslationcache.php, moved into a class (#207 stage 1). The file bin/php/ezgeneratetranslationcache.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/ezgeneratetranslationcache.php:
 *
 *
 * File containing the ezgeneratetranslationcache.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 * @description Generate translation cache files for all configured locales
 * @long-description Pre-generates cached translation files for configured locales and siteaccesses to speed up the first page load after a cache clear. Usage: ./bin/php/ezgeneratetranslationcache.php -s <siteaccess>
 *
 */

namespace Exponential\Command\Kernel
{

class Ezgeneratetranslationcache extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'cli', 'ini', 'script', 'scriptOptions', 'translation', 'translations' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = \eZCLI::instance();
        $script = \eZScript::instance( array( 'description' => ( "\n" .
                                                                "This script will generate caches for translations.\n" .
                                                                "Default usage: ./bin/php/ezgeneratetranslationcache -s setup\n" ),
                                             'use-session' => false,
                                             'use-modules' => true,
                                             'use-extensions' => true,
                                             'user' => true ) );
        $script->startup();

        $scriptOptions = $script->getOptions( "[ts-list:]",
                                              "",
                                              array( 'ts-list' => "A list of translations to generate caches for, for example 'rus-RU nor-NO'\n".
                                                                  "By default caches for all translations will be generated" ),
                                              false,
                                              array( 'user' => true )
                                             );
        $script->initialize();

        /**************************************************************
        * process options                                             *
        ***************************************************************/

        //
        // 'ts-list' option
        //
        $translations = isset( $scriptOptions['ts-list'] ) ? explode( ' ', $scriptOptions['ts-list'] ) : array();
        $translations = \eZTSTranslator::fetchList( $translations );


        /**************************************************************
        * do the work
        ***************************************************************/

        $cli->output( $cli->stylize( 'blue', "Processing: " ), false );

        $ini = \eZINI::instance();

        foreach( $translations as $translation )
        {
            $cli->output( "$translation->Locale ", false );

            $ini->setVariable( 'RegionalSettings', 'Locale', $translation->Locale );
            \eZTranslationCache::resetGlobals();

            $translation->load( '' );
        }

        $cli->output( "", true );

        $script->shutdown( 0 );
    }
}

}
