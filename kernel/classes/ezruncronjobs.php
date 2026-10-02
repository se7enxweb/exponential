<?php
/**
 * File containing the eZRunCronjobs class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
 Class containing helper functions to execute cronjob parts.
*/
class eZRunCronjobs
{
    /*!
     \static
     Function for running a cronjob script.
    */
    static function runScript( $cli, $scriptFile )
    {
        $scriptMutex = new eZMutex( $scriptFile );
        $lockTS = $scriptMutex->lockTS();
        $runScript = false;
        $maxTime = self::maxScriptExecutionTime();
        if ( $lockTS === false )
        {
            if ( $scriptMutex->lock() )
            {
                $scriptMutex->setMeta( 'pid', getmypid() );
                $runScript = true;
            }
            else
            {
                $cli->error( 'Failed to aquire cronjob part lock: ' . $scriptFile );
            }
        }
        // If the cronjob part has been blocked for  2 * self::maxScriptExecutionTime(),
        // force stealing of the cronjob part
        else if ( $lockTS < time() - 2 * $maxTime )
        {
            $cli->output( 'Forcing to steal the mutex lock: ' . $scriptFile );
            $runScript = eZRunCronjobs::stealMutex( $cli, $scriptMutex, true );
        }
        else if ( $lockTS < time() - $maxTime )
        {
            $cli->output( 'Trying to steal the mutex lock: ' . $scriptFile );
            $runScript = eZRunCronjobs::stealMutex( $cli, $scriptMutex );
        }
        else
        {
            $cli->output( 'Cronjob part locked by other process: ' . $scriptMutex->meta( 'pid' ) );
        }
        if ( $runScript )
        {
            global $script;
            global $isQuiet;
            global $cronPart;
            // Audit (doc/bc/6.0/audit.md, system.cronjob.run / system.cronjob.fail): every event of the part
            // carries the run (this invocation's request id)
            $auditStart = microtime( true );
            if ( class_exists( 'expAudit' ) )
                expAudit::setRun( expAudit::requestId() );
            try
            {
                include( $scriptFile );
            }
            catch ( Throwable $e )
            {
                $scriptMutex->unlock();
                self::auditPart( $scriptFile, $cronPart, $auditStart, $e );
                throw $e;
            }
            $scriptMutex->unlock();
            self::auditPart( $scriptFile, $cronPart, $auditStart, null );
        }
    }

    /**
     * Records a cronjob part that ran (system.cronjob.run) or threw (system.cronjob.fail). Never throws.
     *
     * @param string $scriptFile
     * @param string|false $cronPart the [CronjobPart-*] group, false for the default
     * @param float $start
     * @param Throwable|null $error
     */
    protected static function auditPart( $scriptFile, $cronPart, $start, $error )
    {
        if ( !class_exists( 'expAuditHook' ) )
            return;
        $name = $error ? 'system.cronjob.fail' : 'system.cronjob.run';
        expAuditHook::emit( $name, function () use ( $scriptFile, $cronPart, $start, $error ) {
            $data = array( 'object' => array( 'type' => 'cronjob', 'id' => basename( (string)$scriptFile, '.php' ),
                                              'file' => class_exists( 'expAudit' ) ? expAudit::relativePath( $scriptFile ) : basename( $scriptFile ) ),
                           'target' => array( 'type' => 'cronjob_part', 'id' => $cronPart ? (string)$cronPart : 'default' ),
                           'verb' => 'run',
                           'after' => array( 'ms' => (int)round( ( microtime( true ) - $start ) * 1000 ), 'result' => $error ? 'failed' : 'success' ) );
            if ( $error )
            {
                $data['result'] = 'failed';
                $data['reason'] = 'error';
                $data['error'] = array( 'message' => get_class( $error ),
                                        'where' => ( class_exists( 'expAudit' ) ? expAudit::relativePath( $error->getFile() ) : basename( $error->getFile() ) ) . ':' . $error->getLine() );
            }
            return $data;
        } );
    }

    /**
     * \static
     * Returns the maximum permitted execution time for cronjobs.
     * This may be different per cronjob part (see cronjob.ini).
     * @return execution time in seconds
     */
    static function maxScriptExecutionTime()
    {
        global $cronPart;
        $cronjobIni = eZINI::instance( 'cronjob.ini' );

        $scriptGroup = "CronjobPart-$cronPart";
        if ( $cronPart !== false and $cronjobIni->hasVariable( $scriptGroup, 'MaxScriptExecutionTime' ) )
            return $cronjobIni->variable( $scriptGroup, 'MaxScriptExecutionTime' );
        else
            return $cronjobIni->variable( 'CronjobSettings', 'MaxScriptExecutionTime' );
    }

    /*!
     \static
     \private

     Steal a script mutex

     \param cli
     \param script mutex to steal
     \param force stealing of mutex ( optional, false by default )

     \return true if mutex is stole successfully
    */
    static function stealMutex( $cli, $scriptMutex, $force = false )
    {
        $cli->output( 'Stealing mutex. Old process has run too long.' );
        $oldPid = $scriptMutex->meta( 'pid' );
        if ( $force )
        {
            if ( is_numeric( $oldPid ) &&
                 $oldPid != 0 &&
                 function_exists( 'posix_kill' ) )
            {
                $cli->output( 'Killing process: ' . $oldPid );
                posix_kill( $oldPid, 9 );
            }
        }
        if ( $scriptMutex->steal( $force ) )
        {
            $scriptMutex->setMeta( 'pid', getmypid() );
            return true;
        }
        else
        {
            $cli->error( 'Failed to steal cronjob part lock.' );
        }
        return false;
    }
}

?>
