<?php
/**
 * File containing the ezpFormTokenException exception.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * A POST was refused because its form token (CSRF token) was missing or did
 * not belong to the session: the form was loaded in another session, before a
 * login or logout, or is not one of this site's forms at all.
 *
 * Thrown by the request/input check (the ezformtoken extension). The web
 * kernel turns it into an HTTP 403 page through the error module (kernel
 * error eZError::KERNEL_FORM_TOKEN_REFUSED), or a JSON body for XHR and JSON
 * requests; see ezpFormTokenRefusal. It is a refusal, not a fault, so it is
 * logged as one warning line and never as an error with a stack.
 *
 * @package kernel
 */
class ezpFormTokenException extends Exception
{
    /** No token in the posted form nor in an X-CSRF-Token header */
    const MISSING = 'missing';

    /** A token was sent, but it is not this session's */
    const WRONG = 'wrong';

    /** @var string self::MISSING or self::WRONG */
    protected $reason;

    /**
     * @param string $reason self::MISSING or self::WRONG
     * @param string|null $message defaults to the message the check always used
     * @param Throwable|null $previous
     */
    public function __construct( $reason, $message = null, $previous = null )
    {
        $this->reason = $reason === self::WRONG ? self::WRONG : self::MISSING;
        if ( $message === null )
        {
            $message = $this->reason === self::MISSING
                ? 'Missing form token from Request'
                : 'Wrong form token found in Request!';
        }
        parent::__construct( $message, 403, $previous instanceof Throwable ? $previous : null );
    }

    /**
     * Which check failed.
     *
     * @return string self::MISSING or self::WRONG
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * The reason as a machine-readable code, as the JSON body carries it.
     *
     * @return string form_token_missing or form_token_wrong
     */
    public function getReasonCode()
    {
        return 'form_token_' . $this->reason;
    }
}
