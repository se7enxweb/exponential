<?php
/**
 * File containing the expIniException class: every error of the INI editor (exp:ini), with a code that is the
 * command's exit code.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * An error of the INI editor. getCode() is one of the class constants, which are also the exit codes of
 * `console exp:ini`: 1 usage, 2 not found, 3 refused, 4 write failed.
 */
class expIniException extends Exception
{
    /** Malformed input: a setting, a scope spec, a value that cannot be written, a wrong kind of operation. */
    const USAGE = 1;
    /** The thing to change is not there (rem of a missing variable, array value or hash key). */
    const NOT_FOUND = 2;
    /** Refused by policy: default scope, unknown siteaccess or extension, a value toggle() cannot flip. */
    const REFUSED = 3;
    /** The file could not be written (permissions, disk, changed on disk since it was read). */
    const WRITE_FAILED = 4;

    /**
     * @param string $message
     * @param int $code One of the class constants
     * @param Throwable|null $previous
     */
    public function __construct( $message, $code = self::USAGE, $previous = null )
    {
        parent::__construct( $message, $code, $previous );
    }

    public static function usage( $message )
    {
        return new self( $message, self::USAGE );
    }

    public static function notFound( $message )
    {
        return new self( $message, self::NOT_FOUND );
    }

    public static function refused( $message )
    {
        return new self( $message, self::REFUSED );
    }

    public static function writeFailed( $message )
    {
        return new self( $message, self::WRITE_FAILED );
    }
}
