<?php
/**
 * File containing the ezpMail class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

/**
 * ezpMail extends ezcMail in order to override default values and limitations.
 */
class ezpMail extends ezcMail
{
    /**
     * The User-Agent eZMail::setUserAgent() gave this mail. ezcMail::generateHeaders() sets its own
     * ("Apache Zeta Components") on every call, which replaced it for mails sent by SMTP; null or '' keeps that
     * default.
     *
     * @var string|null
     */
    public $userAgent = null;

    /**
     * Override of {@link ezcMailPart::setHeader()}: the User-Agent stays the one of {@link $userAgent} when set.
     *
     * @param string $name
     * @param string $value
     * @param string $charset
     */
    public function setHeader( $name, $value, $charset = 'us-ascii' )
    {
        // The property is public: a value set on it directly gets the same line break cleaning as setUserAgent(),
        // since ezcMail writes an us-ascii header as it is and a break would start a header of the caller's choosing
        if ( is_string( $this->userAgent ) && $this->userAgent !== '' && strcasecmp( $name, 'User-Agent' ) === 0 )
            $value = eZMail::cleanHeaderValue( $this->userAgent );
        parent::setHeader( $name, $value, $charset );
    }

    /**
     * Override of original {@link ezcMail::generateHeaders()}.
     * Allows headers customization
     *
     * @return string The mail headers
     */
    public function generateHeaders()
    {
        // Workaround for encoded email addresses.
        // When encoded, email addresses (at least the name param) have more characters
        // By default, line length is set to 76 characters, after what a new line is created with $lineBreak.
        // This operation is done during encoding via iconv (see ezcMailTools::composeEmailAddress()).
        // Problem is that this operation is done a 2nd time in ezcMailPart::generateHeaders().
        // Following code ensures that there is no double $lineBreak introduced
        // by this process because it potentially breaks headers
        $lineBreak = ezcMailTools::lineBreak();
        $headers = str_replace( "$lineBreak$lineBreak", $lineBreak, parent::generateHeaders() );
        return $headers;
    }
}
?>
