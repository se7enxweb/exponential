<?php
/**
 * File containing the expErrorHeaderList class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 97690e9c93), Copyright (C) Mugo Web, GPL v2
 */

/**
 * The response headers of an error page, from error.ini [HTTPError-<code>]:
 *
 * - the status lines as before: "<protocol> <code> <HTTPName>" and "Status: <code> <HTTPName>";
 * - then every HeaderList[<name>]=<value>, such as HeaderList[Cache-Control]=no-store.
 *
 * HeaderList[Status]=<name> is read as HTTPName when HTTPName is not set (the form LoveStack's error.ini uses).
 * A header name that is not an HTTP token, or a value with a line break or a NUL byte, is left out with a
 * warning, so a setting can never add a header of its own or split the response.
 *
 * The error view sends the lines and stores them in $Result['responseHeaders'], which the content view cache
 * keeps with the error page and sends again when it serves the cached page.
 */
class expErrorHeaderList
{
    /**
     * @param eZINI $errorINI error.ini
     * @param int|string $httpErrorCode the HTTP code of the error, e.g. 404
     * @param string|null $serverProtocol SERVER_PROTOCOL, e.g. HTTP/1.1
     * @return array 'name' => the status text or null, 'status' => the two status lines (empty without a status
     *               text), 'headers' => the HeaderList lines
     */
    public static function build( eZINI $errorINI, $httpErrorCode, $serverProtocol )
    {
        $group = 'HTTPError-' . $httpErrorCode;
        $list = array();
        if ( $errorINI->hasVariable( $group, 'HeaderList' ) )
        {
            $list = $errorINI->variable( $group, 'HeaderList' );
            if ( !is_array( $list ) )
                $list = array();
        }

        $name = null;
        if ( $errorINI->hasVariable( $group, 'HTTPName' ) && trim( (string)$errorINI->variable( $group, 'HTTPName' ) ) !== '' )
            $name = trim( (string)$errorINI->variable( $group, 'HTTPName' ) );

        $headers = array();
        foreach ( $list as $headerName => $value )
        {
            $headerName = trim( (string)$headerName );
            $value = trim( (string)$value );
            if ( strcasecmp( $headerName, 'Status' ) === 0 )
            {
                if ( $name === null && $value !== '' && self::isSafeValue( $value ) )
                    $name = $value;
                continue;
            }
            if ( !preg_match( '/^[A-Za-z0-9!#$%&\'*+.^_`|~-]+$/', $headerName ) || !self::isSafeValue( $value ) )
            {
                eZDebug::writeWarning( "error.ini [$group] HeaderList: the header '" . addcslashes( $headerName, "\0..\37" ) . "' is not a valid header and is not sent", __METHOD__ );
                continue;
            }
            $headers[] = $headerName . ': ' . $value;
        }

        $status = array();
        if ( $name !== null )
        {
            $protocol = trim( (string)$serverProtocol );
            if ( !preg_match( '#^HTTP/\d(\.\d)?$#', $protocol ) )
                $protocol = 'HTTP/1.1';
            $status = array( "$protocol $httpErrorCode $name", "Status: $httpErrorCode $name" );
        }

        return array( 'name' => $name, 'status' => $status, 'headers' => $headers );
    }

    /**
     * @param string $value
     * @return bool true when the value can not end the header line
     */
    protected static function isSafeValue( $value )
    {
        return strpbrk( $value, "\r\n\0" ) === false;
    }
}
