<?php
/**
 * An error a resolver reports to the client: its message is shown, its code goes to extensions.code
 * (doc/eng-us/features/6.0/graphql.md, "Errors"). It needs no library: the codes are used before the library is
 * loaded (a switched-off end point), and expGraphQLServer::formatError() shows its message to the client.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLError extends Exception
{
    /** The codes of extensions.code. A code once released stays (expGraphQLContractTest). */
    const FORBIDDEN = 'FORBIDDEN';
    const NOT_FOUND = 'NOT_FOUND';
    const VALIDATION = 'VALIDATION';
    const PARSE = 'PARSE';
    const BAD_USER_INPUT = 'BAD_USER_INPUT';
    const DEPTH_LIMIT = 'DEPTH_LIMIT';
    const COMPLEXITY_LIMIT = 'COMPLEXITY_LIMIT';
    const INTROSPECTION_DISABLED = 'INTROSPECTION_DISABLED';
    const RATE_LIMITED = 'RATE_LIMITED';
    const DISABLED = 'DISABLED';
    const INTERNAL = 'INTERNAL';
    const PERSISTED_QUERY_NOT_FOUND = 'PERSISTED_QUERY_NOT_FOUND';
    const PERSISTED_QUERY_NOT_SUPPORTED = 'PERSISTED_QUERY_NOT_SUPPORTED';
    const PERSISTED_QUERY_REQUIRED = 'PERSISTED_QUERY_REQUIRED';

    /** @var string */
    protected $errorCode;

    /**
     * @param string $message
     * @param string $code one of the constants
     */
    public function __construct( $message, $code = self::BAD_USER_INPUT )
    {
        parent::__construct( (string)$message );
        $this->errorCode = (string)$code;
    }

    /** @return string */
    public function errorCode()
    {
        return $this->errorCode;
    }

    public function isClientSafe(): bool
    {
        return true;
    }
}

?>
