<?php
/**
 * The code of bin/php/publish_content.php, moved into a class (#207 stage 1). The file bin/php/publish_content.php is one call to it.
 * @description Publish one queued object version (OBJECT_ID VERSION_ID); internal, used by the publisher
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/publish_content.php:
 *
 *
 * File containing the publish_content.php bin script
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 * @subpackage content
 *
 */

namespace Exponential\Command\Kernel
{

class PublishContent extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'argumentConfig', 'cli', 'objectId', 'operationResult', 'options', 'optionsConfig', 'pid', 'script', 'version' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $pid = getmypid();

        $cli = $this->cli();
        $script = $this->script( array( 'description' => 'Asynchronous publishing handler, not meant to be used directly',
                                             'use-session' => false,
                                             'use-modules' => true,
                                             'use-extensions' => true ) );
        $script->startup();

        $argumentConfig = '[OBJECT_ID] [VERSION_ID]';
        $optionsConfig = '';
        $options = $this->options( $optionsConfig, $argumentConfig );

        $script->initialize();
        if ( count( $options['arguments'] ) != 2 )
        {
            \eZLog::write( "Wrong arguments count", 'publishqueue.log' );
            $script->shutdown( 1, 'wrong argument count' );
        }

        $objectId = $options['arguments'][0];
        $version = $options['arguments'][1];

        \eZLog::write( "[$pid] Publishing #{$objectId}/{$version}", 'async.log' );
        $operationResult = \eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $objectId, 'version' => $version  ) );

        if ( isset( $operationResult['status'] ) && $operationResult['status'] == \eZModuleOperationInfo::STATUS_CONTINUE )
        {
            \eZLog::write( "[$pid] Published #{$objectId}/{$version}", 'async.log' );
            $script->shutdown( 0 );
        }
        else
        {
            \eZLog::write( "[$pid] Operation result for #{$objectId}/{$version}: " . print_r( $operationResult, true ), 'async.log' );
            $script->shutdown( 2, 'Publishing did not complete' );
        }
    }
}

}
