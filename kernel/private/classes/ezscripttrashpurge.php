<?php
/**
 * This class handles purging of trash items. It is used by both the script
 * and cronjob.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */
class eZScriptTrashPurge
{
    /**
     * eZCLI object used along running the operation.
     *
     * @var eZCLI
     */

    protected $cli;

    /**
     * Whether the operation should be quiet.
     *
     * @var bool
     */
    protected $quiet;

    /**
     * Whether memory monitoring is active.
     *
     * @var bool
     */
    protected $memoryMonitoring;

    /**
     * eZScript object used along running the operation.
     *
     * @var eZScript
     */
    protected $script;

    /**
     * Filename to use while logging memory monitoring.
     *
     * @var string
     */
    protected $logFile;

    /**
     * Constructor of eZScriptTrashPurge.
     *
     * @param eZCLI $cli The instance of eZCLI.
     * @param bool $quiet Whether the operation should be quiet or not.
     * @param bool $memoryMonitoring Set to true to turn on memory monitoring.
     * @param eZScript $script Optional eZScript object used while running.
     * @param string $logFile Log file to use for memory monitoring.
     */
    public function __construct( eZCLI $cli, $quiet = true, $memoryMonitoring = false, ?eZScript $script = null, $logFile = "trashpurge.log" )
    {
        $this->cli = $cli;
        $this->quiet = $quiet;
        $this->memoryMonitoring = $memoryMonitoring;
        $this->script = $script;
        $this->logFile = $logFile;
    }

    /**
     * Executes the purge operation
     *
     * @param int|null $iterationLimit Number of trashed objects to treat per iteration, use null to use a default value.
     * @param int|null $sleep Number of seconds to sleep between two iterations, use null to use a default value.
     * @param int|null $trashedDays Number of days an object should had been in trash to be purged, use null to use a default value so any object in the trash will be purged.
     *
     * @return bool True if the operation succeeded.
     */
    public function run( $iterationLimit = 100, $sleep = 1, $trashedDays = null )
    {
        if ( $iterationLimit === null )
        {
            $iterationLimit = 100;
        }

        if ( $sleep === null )
        {
            $sleep = 1;
        }

        $trashed = $trashedDays ? strtotime( "-{$trashedDays} days" ) : null;
        // A huge number of days overflows the date arithmetic into a future time, which would purge everything
        if ( $trashedDays && ( !is_int( $trashed ) || $trashed >= time() ) )
        {
            $this->cli->error( "Cannot purge items trashed at least $trashedDays days ago: the date is out of range." );
            return false;
        }

        if ( $this->memoryMonitoring )
        {
            eZLog::rotateLog( $this->logFile );
            $this->cli->output( "Logging memory usage to {$this->logFile}" );
        }

        $this->cli->output( "Purging trash items:" );

        $this->monitor( "start" );

        // Get user's ID who can remove subtrees. (Admin by default with userID = 14)
        $userCreatorID = eZINI::instance()->variable( "UserSettings", "UserCreatorID" );
        $user = eZUser::fetch( $userCreatorID );
        if ( !$user )
        {
            $this->cli->error( "Cannot get user object with userID = '$userCreatorID'.\n(See site.ini[UserSettings].UserCreatorID)" );
            return false;
        }
        eZUser::setCurrentlyLoggedInUser( $user, $userCreatorID );

        $trashCount = eZContentObjectTrashNode::trashListCount(  array( 'Trashed' => $trashed ) );
        if ( !$this->quiet && !$trashed )
        {
            $this->cli->output( "Found $trashCount object(s) in trash." );
        }
        else if ( !$this->quiet && $trashed )
        {
            $this->cli->output( "Found $trashCount object(s) in trash for at least $trashedDays days." );
        }
        if ( $trashCount == 0 )
        {
            return true;
        }
        if ( $this->script !== null )
        {
            $this->script->resetIteration( $trashCount );
        }

        // The batches are the trash service's, which the trash view's Empty button uses too. Loaded by path:
        // a server whose workers kept an autoload array from before the class existed still finds it.
        require_once __DIR__ . '/services/trash.php';
        $result = \Exponential\Service\Trash::purgeInBatches(
            $iterationLimit, $sleep, $trashed,
            function ( $object ) {
                $this->monitor( "purge" );
                if ( $this->script !== null )
                    $this->script->iterate( $this->cli, true );
            },
            function ( $moment ) {
                $this->monitor( "iteration $moment" );
            } );

        if ( !$result['ok'] )
        {
            $this->cli->output();
            $this->cli->error( 'Trash has not been emptied, impossible to commit the whole transaction' );
            return false;
        }

        if ( !$this->quiet )
        {
            $this->cli->output( 'Trash successfully emptied' );
        }

        $this->monitor( "end" );

        return true;
    }

    /**
     * Log memory usage.
     *
     * @param string $text Text to use while logging.
     */
    private function monitor( $text )
    {
        if ( $this->memoryMonitoring )
        {
            eZLog::write( "mem [$text]: " . memory_get_usage(), $this->logFile );
        }
    }
}

?>
