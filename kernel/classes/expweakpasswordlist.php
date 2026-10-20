<?php
/**
 * File containing the expWeakPasswordList class
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The one list of well-known and common passwords, and the check every place that sets a new password uses: the
 * setup wizard, the kickstarter and exp:install (through eZStepSiteAdmin), user/password, registration and the user
 * edit form (through expPasswordPolicy and the user account datatype), exp:resetuserpassword and the web services.
 *
 * A password is refused when, after these simple variants are taken away, it is one of the words:
 *  - case and surrounding white space ("Admin", " ADMIN ");
 *  - digits and symbols at the end or the start ("admin2026!", "#1admin");
 *  - separators inside (spaces, "-", "_", "."): "change me", "change_me";
 *  - the usual letter swaps (@ and 4 for a, 3 for e, 1 and ! for i, 0 for o, $ and 5 for s, 7 for t): "p@ssw0rd";
 *  - common endings (-s, -es, -ed, -er, -ers, -ing, -ings): a word with one of them counts as the word itself;
 *  - the word typed twice or more ("adminadmin").
 * It is also refused when it is one character repeated, a short pattern repeated ("abcabcabc") or a run of the
 * keyboard, the alphabet or the digits ("qwertyuiop", "abcdefgh", "12345678", "98765432"), also with digits and
 * symbols around it ("qwertyuiop2026!").
 *
 * The list is extensible: site.ini [UserSettings] WeakPasswordList[] adds words, WeakPasswordFile names a file with
 * one word per line (# starts a comment; relative to the installation root). [UserSettings] WeakPasswordCheck
 * (enabled as shipped) switches the check off for user/password, registration, the user edit form, the console and
 * the web services. The installers (setup wizard, kickstarter, exp:install) always check: a new site never starts
 * with a well-known administrator password. Only a new password is checked: signing in with an existing one is
 * never refused. Guide: doc/en/us/bc/6.0/weak-passwords-and-reset-key-lifetime.md.
 *
 * @package kernel
 */
class expWeakPasswordList
{
    /** Endings that make a variant of a word, longest first */
    const SUFFIXES = array( 'ings', 'ing', 'ers', 'er', 'ed', 'es', 's' );

    /** @var array the words of WeakPasswordFile, keyed by "path|mtime|size" */
    protected static $fileCache = array();

    /** @var eZINI|null */
    protected $ini;

    /** @var array|null the words in force, as keys */
    protected $words = null;

    /**
     * @param eZINI|null $ini site.ini (tests pass their own; null reads site.ini when the kernel is loaded)
     */
    public function __construct( $ini = null )
    {
        if ( $ini instanceof eZINI )
            $this->ini = $ini;
        else if ( class_exists( 'eZINI', false ) )
            $this->ini = eZINI::instance();
    }

    /**
     * @return expWeakPasswordList
     */
    public static function instance()
    {
        return new self();
    }

    /**
     * The words every installation refuses, lowercase. The variants listed in the class comment are refused too,
     * so a word appears here only once, in its base form.
     *
     * @return array
     */
    public static function builtInWords()
    {
        return array(
            // the software's own names and historical defaults
            'publish', 'exponential', 'ezpublish', 'ez', 'ezp', 'ezsystems', 'se7enx', 'sevenx', 'netgen', 'demo',
            // accounts and roles
            'admin', 'administrator', 'admins', 'root', 'superuser', 'sysadmin', 'webmaster', 'editor', 'user', 'guest',
            'anonymous', 'test', 'tester', 'testing', 'login', 'logon', 'manager', 'master', 'owner', 'staff', 'support',
            // the classics
            'password', 'passwort', 'passwd', 'pass', 'passw', 'pw', 'pwd', 'motdepasse', 'contrasena', 'wachtwoord',
            'changeme', 'change', 'secret', 'default', 'welcome', 'letmein', 'trustno', 'access', 'opensesame',
            'iloveyou', 'monkey', 'dragon', 'sunshine', 'princess', 'football', 'baseball', 'soccer', 'superman',
            'batman', 'starwars', 'shadow', 'freedom', 'whatever', 'hello', 'hallo', 'computer', 'internet', 'server',
            'website', 'company', 'summer', 'winter', 'spring', 'autumn', 'abc', 'xyz', 'qwerty', 'qwertz', 'azerty',
            'asdf', 'zxcv',
            // digits on their own
            '0', '1', '12', '123', '1234', '12345', '123456', '1234567', '12345678', '123456789', '1234567890',
            '111111', '000000', '654321', '696969', '112233', '121212', '123123', '159753', '147258369', '987654321',
        );
    }

    /**
     * @return bool [UserSettings] WeakPasswordCheck (enabled when it is not set)
     */
    public function enabled()
    {
        if ( !$this->ini || !$this->ini->hasVariable( 'UserSettings', 'WeakPasswordCheck' ) )
            return true;
        return !in_array( strtolower( trim( (string)$this->ini->variable( 'UserSettings', 'WeakPasswordCheck' ) ) ),
                          array( 'disabled', 'false', '0', 'no' ), true );
    }

    /**
     * The words in force: the built-in ones, [UserSettings] WeakPasswordList[] and the file WeakPasswordFile.
     *
     * @return array lowercase words as keys
     */
    public function words()
    {
        if ( $this->words !== null )
            return $this->words;
        $words = array();
        foreach ( self::builtInWords() as $w )
            $words[$w] = true;
        if ( $this->ini && $this->ini->hasVariable( 'UserSettings', 'WeakPasswordList' ) )
        {
            foreach ( (array)$this->ini->variable( 'UserSettings', 'WeakPasswordList' ) as $w )
                self::addWord( $words, $w );
        }
        if ( $this->ini && $this->ini->hasVariable( 'UserSettings', 'WeakPasswordFile' ) )
        {
            $file = trim( (string)$this->ini->variable( 'UserSettings', 'WeakPasswordFile' ) );
            if ( $file !== '' )
                foreach ( self::readFile( $file ) as $w => $unused )
                    $words[$w] = true;
        }
        return $this->words = $words;
    }

    /**
     * Adds a word in its lowercase form; an empty one is left out.
     */
    protected static function addWord( array &$words, $word )
    {
        if ( !is_scalar( $word ) )
            return;
        $word = self::lower( trim( (string)$word ) );
        if ( $word !== '' )
            $words[$word] = true;
    }

    /**
     * The words of a file, one per line (# starts a comment), cached per process while the file is unchanged.
     *
     * @param string $file absolute, or relative to the installation root (the working directory)
     * @return array lowercase words as keys; empty when the file cannot be read
     */
    public static function readFile( $file )
    {
        $path = (string)$file;
        if ( !is_file( $path ) || !is_readable( $path ) )
        {
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeWarning( "WeakPasswordFile cannot be read: $path", __METHOD__ );
            return array();
        }
        $key = $path . '|' . (int)@filemtime( $path ) . '|' . (int)@filesize( $path );
        if ( isset( self::$fileCache[$key] ) )
            return self::$fileCache[$key];
        $words = array();
        $handle = @fopen( $path, 'rb' );
        if ( $handle )
        {
            while ( ( $line = fgets( $handle ) ) !== false )
            {
                $line = trim( $line );
                if ( $line === '' || $line[0] === '#' )
                    continue;
                self::addWord( $words, $line );
            }
            fclose( $handle );
        }
        // one file per process is enough; a changed file replaces the old entry
        self::$fileCache = array( $key => $words );
        return $words;
    }

    /** Lowercase, multi-byte safe when mbstring is there */
    protected static function lower( $s )
    {
        return function_exists( 'mb_strtolower' ) ? mb_strtolower( $s, 'UTF-8' ) : strtolower( $s );
    }

    /**
     * The forms of a password that are compared with the words: lowercase, without separators, without digits and
     * symbols at either end, with the usual letter swaps undone, and each of these without a common ending.
     *
     * @param string $password
     * @return array distinct non-empty forms
     */
    public static function forms( $password )
    {
        $base = self::lower( trim( (string)$password ) );
        if ( $base === '' )
            return array();
        $swaps = array( '@' => 'a', '4' => 'a', '3' => 'e', '1' => 'i', '!' => 'i', '0' => 'o', '$' => 's', '5' => 's', '7' => 't' );
        $forms = array();
        foreach ( array_unique( array( $base, str_replace( array( ' ', '-', '_', '.' ), '', $base ) ) ) as $f )
        {
            // The digits and symbols at the end are either added ("admin2026!") or a swapped last letter ("w3lc0m3"),
            // so every cut of that run is tried (at most 8 characters of it)
            $run = preg_match( '/[^a-z]+$/', $f, $m ) ? min( 8, strlen( $m[0] ) ) : 0;
            for ( $cut = 0; $cut <= $run; $cut++ )
            {
                $g = $cut ? substr( $f, 0, -$cut ) : $f;
                // a cut that leaves no letter is not a word with something added ("193847265" is not "1" + digits)
                if ( $cut && !preg_match( '/[a-z]/', $g ) )
                    break;
                $swapped = strtr( $g, $swaps );
                $forms[] = $g;
                $forms[] = $swapped;
                // and without digits and symbols at either end ("!admin", "2026admin")
                $forms[] = (string)preg_replace( '/^[^\p{L}]+|[^\p{L}]+$/u', '', $g );
                $forms[] = (string)preg_replace( '/^[^a-z]+|[^a-z]+$/', '', $swapped );
            }
        }
        foreach ( $forms as $f )
        {
            foreach ( self::SUFFIXES as $suffix )
            {
                $len = strlen( $suffix );
                if ( strlen( $f ) > $len + 2 && substr( $f, -$len ) === $suffix )
                {
                    $forms[] = substr( $f, 0, -$len );
                    // "changing" -> "chang" -> also "change"
                    $forms[] = substr( $f, 0, -$len ) . 'e';
                }
            }
        }
        $out = array();
        foreach ( $forms as $f )
            if ( is_string( $f ) && $f !== '' )
                $out[$f] = true;
        return array_keys( $out );
    }

    /**
     * @param string $password
     * @return bool the password is well-known or common (see the class comment); an empty password is weak
     */
    public function isWeak( $password )
    {
        if ( !is_scalar( $password ) )
            return true;
        $password = (string)$password;
        if ( trim( $password ) === '' )
            return true;
        $words = $this->words();
        $lower = self::lower( trim( $password ) );
        // the password as typed, digits and all ("123456", "abc123")
        if ( isset( $words[$lower] ) )
            return true;
        foreach ( self::forms( $password ) as $form )
        {
            if ( isset( $words[$form] ) )
                return true;
            if ( self::isRepeatedWord( $form, $words ) )
                return true;
            // "qwerty2026!", "abcdefg1": a run or a pattern with digits and symbols around it
            if ( strlen( $form ) >= 4 && self::isPattern( $form ) )
                return true;
        }
        return self::isPattern( $lower );
    }

    /**
     * @param string $form
     * @param array $words as keys
     * @return bool the form is one of the words typed two or more times ("adminadmin")
     */
    protected static function isRepeatedWord( $form, array $words )
    {
        $len = strlen( $form );
        for ( $unit = 2; $unit <= intdiv( $len, 2 ); $unit++ )
        {
            if ( $len % $unit !== 0 )
                continue;
            $piece = substr( $form, 0, $unit );
            if ( isset( $words[$piece] ) && str_repeat( $piece, intdiv( $len, $unit ) ) === $form )
                return true;
        }
        return false;
    }

    /**
     * One character or a short pattern repeated, or a run of the keyboard, the alphabet or the digits.
     *
     * @param string $lower the password, lowercase
     * @return bool
     */
    public static function isPattern( $lower )
    {
        $lower = (string)$lower;
        $len = strlen( $lower );
        if ( $len === 0 )
            return true;
        // a pattern of up to three characters repeated ("aaaaaaaa", "ababab", "abcabcabc", "!!!!!!")
        for ( $unit = 1; $unit <= 3 && $unit < $len; $unit++ )
            if ( $len % $unit === 0 && str_repeat( substr( $lower, 0, $unit ), intdiv( $len, $unit ) ) === $lower )
                return true;
        if ( $len < 4 )
            return false;
        // runs, forwards and backwards
        $runs = array( 'abcdefghijklmnopqrstuvwxyz', '01234567890', 'qwertyuiop', 'asdfghjkl', 'zxcvbnm', 'qwertzuiop',
                       'yxcvbnm', 'azertyuiop', 'qsdfghjklm', 'wxcvbn', '1qaz2wsx3edc4rfv5tgb', 'qazwsxedcrfvtgb' );
        foreach ( $runs as $run )
        {
            if ( strpos( $run, $lower ) !== false || strpos( strrev( $run ), $lower ) !== false )
                return true;
        }
        return false;
    }

    /**
     * The check as the forms for a new password use it: false when WeakPasswordCheck is off.
     *
     * @param string $password
     * @return bool
     */
    public function refuses( $password )
    {
        return $this->enabled() && $this->isWeak( $password );
    }
}
?>
