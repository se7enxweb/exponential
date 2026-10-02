<?php
/**
 * File containing the ezpRequestRuleResult class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * What a rule decided. An action returns one; the kernel turns it into the
 * response (ezpRequestRuleKernel::moduleResult()). Every action, built in or
 * from an extension, answers with one of these types:
 *
 *  allow         the request runs as usual and no later rule is asked
 *  notfound      the "not found" page, HTTP 404 (kernel error 3)
 *  forbidden     the "access denied" page, HTTP 403 (kernel error 1)
 *  login         an anonymous visitor goes to user/login and comes back after
 *                signing in; a signed-in one gets "access denied"
 *  redirect      HTTP redirect to $location with $status
 *  rewrite       the kernel runs $location instead, without a redirect
 *  module_result $moduleResult is the page (an action that renders its own)
 */
class ezpRequestRuleResult
{
    const ALLOW = 'allow';
    const NOT_FOUND = 'notfound';
    const FORBIDDEN = 'forbidden';
    const LOGIN = 'login';
    const REDIRECT = 'redirect';
    const REWRITE = 'rewrite';
    const MODULE_RESULT = 'module_result';

    /** @var string one of the constants */
    public $type;
    /** @var string|null redirect or rewrite target, a URI inside the siteaccess ('/Fit-Healthy') or an absolute URL */
    public $location;
    /** @var int HTTP status of a redirect */
    public $status;
    /** @var array|null a module result, for MODULE_RESULT */
    public $moduleResult;
    /** @var string|null name of the rule that decided, set by the engine */
    public $rule;

    protected function __construct( $type, $location = null, $status = 0, $moduleResult = null )
    {
        $this->type = $type;
        $this->location = $location;
        $this->status = (int)$status;
        $this->moduleResult = $moduleResult;
    }

    public static function allow() { return new self( self::ALLOW ); }
    public static function notFound() { return new self( self::NOT_FOUND, null, 404 ); }
    public static function forbidden() { return new self( self::FORBIDDEN, null, 403 ); }
    public static function login() { return new self( self::LOGIN, null, 302 ); }

    /**
     * @param string $location
     * @param int $status 301, 302, 303, 307 or 308; anything else is 302
     * @return ezpRequestRuleResult
     */
    public static function redirect( $location, $status = 302 )
    {
        $status = (int)$status;
        if ( !isset( self::$statusText[$status] ) )
            $status = 302;
        return new self( self::REDIRECT, (string)$location, $status );
    }

    /**
     * @param string $location the URI to run instead, e.g. 'content/view/full/2'
     * @return ezpRequestRuleResult
     */
    public static function rewrite( $location ) { return new self( self::REWRITE, ltrim( (string)$location, '/' ) ); }

    /**
     * @param array $moduleResult array( 'content' => ..., 'path' => ..., ... ) as a module view returns it
     * @return ezpRequestRuleResult
     */
    public static function moduleResult( array $moduleResult ) { return new self( self::MODULE_RESULT, null, 200, $moduleResult ); }

    /** @var array HTTP status => status line text */
    public static $statusText = array(
        301 => '301 Moved Permanently',
        302 => '302 Found',
        303 => '303 See Other',
        307 => '307 Temporary Redirect',
        308 => '308 Permanent Redirect',
    );

    /**
     * @return string e.g. '301 Moved Permanently'
     */
    public function statusLine()
    {
        return isset( self::$statusText[$this->status] ) ? self::$statusText[$this->status] : self::$statusText[302];
    }

    /**
     * @return string one line, for the explain command and the debug output
     */
    public function describe()
    {
        switch ( $this->type )
        {
            case self::REDIRECT: return "redirect {$this->status} to {$this->location}";
            case self::REWRITE: return "rewrite to {$this->location}";
            default: return $this->type;
        }
    }
}

?>
