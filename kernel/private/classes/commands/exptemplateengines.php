<?php
/**
 * @description List the template engines: library found or not, version, file endings, settings, security policy, compiled-template cache
 * @alias exp:template:engines
 *
 * File containing the exp:template:engines command (bin/php/exptemplateengines.php). The data comes from
 * expTemplateEngineInfo (lib/templatebridge/classes/); guide: doc/eng-us/guides/template-engines.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Exptemplateengines extends \Exponential\Runnable\Command
{
    public function run()
    {
        $this->script( array( 'description' => "List the template engines of the installation\n" .
                                               "For each engine: whether its library is available, its version, the file endings it renders, its\n" .
                                               "settings, whether its security policy is on and where its compiled templates are kept.\n" .
                                               "\n" .
                                               "./console exp:template:engines\n" .
                                               "./console exp:template:engines --format=json\n" .
                                               "\n" .
                                               "Exit code: 0 every engine is available; 3 an engine's library is missing; 2 wrong options.",
                              'use-session' => false,
                              'use-modules' => false,
                              'use-extensions' => true ) );
        $options = $this->startup( '[format:]', '', array(
            'format' => 'text (the default) or json',
        ) );
        $format = isset( $options['format'] ) && $options['format'] ? (string)$options['format'] : 'text';
        if ( $format !== 'text' && $format !== 'json' )
        {
            $this->cli()->error( "FAIL --format must be text or json: $format" );
            $this->shutdown( 2 );
        }
        $engines = \expTemplateEngineInfo::engines();
        $missing = false;
        foreach ( $engines as $engine )
            $missing = $missing || !$engine['available'];
        if ( $format === 'json' )
            $this->cli()->output( json_encode( array( 'schema' => 'exp-template-engines/1', 'engines' => array_values( $engines ) ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
        else
        {
            foreach ( $engines as $engine )
            {
                $this->cli()->output( sprintf( '%-12s %-22s %s', ( $engine['available'] ? 'AVAILABLE' : 'UNAVAILABLE' ), $engine['engine'] . ' (' . $engine['name'] . ')',
                                               $engine['version'] !== null ? $engine['version'] : 'library not found' ) );
                $this->cli()->output( '             endings ' . implode( ' ', $engine['endings'] ) . '; settings ' . $engine['settings'] .
                                      ( $engine['security'] === null ? '' : '; security policy ' . ( $engine['security'] ? 'on' : 'off' ) ) .
                                      '; compiled ' . $engine['compiled'] );
            }
            $this->cli()->output( $missing ? 'FAIL an engine is not available' : 'PASS ' . count( $engines ) . ' engines available' );
        }
        $this->shutdown( $missing ? 3 : 0 );
    }
}

}
