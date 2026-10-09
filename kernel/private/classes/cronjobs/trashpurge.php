<?php
/**
 * The code of cronjobs/trashpurge.php, moved into a class (#207 stage 1). The file cronjobs/trashpurge.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of cronjobs/trashpurge.php:
 *
 *
 * @description Permanently delete all objects currently held in the trash
 *
 * Trash purge cronjob. With content.ini [TrashSettings] KeepItemsForDays only what has been in the trash that long.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\Cronjob\Kernel
{

class Trashpurge extends \Exponential\Runnable\CronjobPart
{
    /** The longest age KeepItemsForDays accepts, a hundred years; larger values overflow the date arithmetic */
    const MAX_KEEP_DAYS = 36500;

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $keepDays = static::keepDays( $this->contentINI() );
        if ( $keepDays === false )
        {
            // A mistyped age must not empty the whole trash
            $this->reportError( 'content.ini [TrashSettings] KeepItemsForDays is not a whole number of days between 0 and '
                                . self::MAX_KEEP_DAYS . '; the trash is left as it is.' );
            return false;
        }
        return $this->purge( $keepDays );
    }

    /**
     * content.ini as the siteaccess of this run sees it, read from the files rather than the INI cache: a purge
     * cannot be undone, so a KeepItemsForDays just added (in a new override file, or on a server whose config.php
     * turns off the INI modification checks) must count on the very next run instead of the cached "purge
     * everything". One INI read per run.
     *
     * @return \eZINI
     */
    protected function contentINI()
    {
        return new \eZINI( 'content.ini', 'settings', null, false );
    }

    /**
     * Reports an error on the command line and in the error log.
     *
     * @param string $message
     */
    protected function reportError( $message )
    {
        \eZCLI::instance()->error( $message );
        \eZDebug::writeError( $message, __METHOD__ );
    }

    /**
     * Purges what has been in the trash for $keepDays days, everything for null.
     *
     * @param int|null $keepDays
     * @return bool
     */
    protected function purge( $keepDays )
    {
        return \Exponential\Service\Trash::purge( \eZCLI::instance(), true, false, null, null, null, $keepDays );
    }

    /**
     * How many days an item stays in the trash before this cronjob purges it: content.ini [TrashSettings]
     * KeepItemsForDays.
     *
     * @param \eZINI $ini content.ini
     * @return int|null|false the days; null when the setting is missing, empty or 0 (purge everything, as before);
     *                        false when it is not a whole number of days up to MAX_KEEP_DAYS
     */
    public static function keepDays( \eZINI $ini )
    {
        if ( !$ini->hasVariable( 'TrashSettings', 'KeepItemsForDays' ) )
            return null;
        $value = $ini->variable( 'TrashSettings', 'KeepItemsForDays' );
        if ( $value === null )
            return null;
        if ( !is_string( $value ) && !is_int( $value ) )
            return false;
        $value = trim( (string)$value );
        if ( $value === '' )
            return null;
        if ( !ctype_digit( $value ) || strlen( ltrim( $value, '0' ) ) > 6 || (int)$value > self::MAX_KEEP_DAYS )
            return false;
        return (int)$value > 0 ? (int)$value : null;
    }
}

}
