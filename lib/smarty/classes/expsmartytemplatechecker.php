<?php
/**
 * exp:template:check for Smarty templates (.stpl): compiled in memory with Smarty 5 and the bridge (expSmartyExtension,
 * the exp* tags), under the security policy as the site applies it (smarty.ini Security); at compile level the
 * compiled PHP is linted; at strict level every design: include ({expinclude uri='design:...'}) is resolved.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expSmartyTemplateChecker implements expTemplateEngineChecker
{
    public function available()
    {
        return expSmartyRenderer::loadLibrary();
    }

    public function check( $file, $text, $level, array $context )
    {
        $findings = array();
        $smarty = expSmartyRenderer::smarty();
        if ( !$smarty )
            return array( expTplTemplateChecker::finding( 'ENGINE-UNAVAILABLE', 'ERROR', 0, 0, 'Smarty 5 is not available' ) );
        try
        {
            $template = $smarty->createTemplate( 'eval:' . $text );
            $code = $template->getCompiler()->compileTemplate( $template );
            if ( $level !== 'syntax' && ( $error = expTemplateChecker::lintPhp( $code ) ) !== null )
                $findings[] = expTplTemplateChecker::finding( 'STPL-COMPILE-001', 'ERROR', 0, 0, 'the compiled template is not valid PHP: ' . $error );
        }
        catch ( Throwable $e )
        {
            // the template's name ("eval:<the whole text>") can hold quotes: the head ends where 'on line <n> "' starts
            $message = trim( $e->getMessage() );
            $message = preg_match( '/^Syntax error in template .*?(?=\bon line \d+ ")/s', $message ) ?
                preg_replace( '/^Syntax error in template .*?(?=\bon line \d+ ")/s', '', $message ) :
                preg_replace( '/^Syntax error in template "[^"]*"\s*/', '', $message );
            $line = self::line( $e, $message );
            $findings[] = expTplTemplateChecker::finding( self::isSecurity( $message ) ? 'STPL-SECURITY-001' : 'STPL-SYNTAX-001', 'ERROR', $line, 0,
                                                          self::message( $message, $text, $line ) );
        }
        if ( !empty( $context['includes'] ) && isset( $context['resolve'] ) &&
             preg_match_all( "/\\{expinclude\\s+uri=(['\"])(design:[A-Za-z0-9_.\\/-]+)\\1/", $text, $matches, PREG_OFFSET_CAPTURE ) )
        {
            foreach ( $matches[2] as $match )
            {
                if ( !call_user_func( $context['resolve'], $match[0] ) )
                    $findings[] = expTplTemplateChecker::finding( 'STPL-STRICT-INCLUDE-MISSING', 'ERROR', substr_count( $text, "\n", 0, $match[1] ) + 1, 0,
                                                                  "the included template {$match[0]} is not found in the design chain" );
            }
        }
        return $findings;
    }

    /** Whether a Smarty message is the security policy's ("... not allowed by security setting", "... disabled by security setting"). */
    public static function isSecurity( $message )
    {
        return (bool)preg_match( '/\b(not allowed|disabled) by security\b/i', (string)$message );
    }

    /**
     * The message without Smarty's 'on line <n> "<source line>"' head (the finding shows the line and its source apart),
     * on one line.
     */
    public static function message( $message, $text, $line )
    {
        $message = (string)$message;
        $lines = explode( "\n", (string)$text );
        $source = $line > 0 && isset( $lines[$line - 1] ) ? trim( $lines[$line - 1] ) : null;
        $head = 'on line ' . $line . ' "' . $source . '"';
        if ( $source !== null && strncmp( $message, $head, strlen( $head ) ) === 0 )
            $message = substr( $message, strlen( $head ) );
        else
            $message = preg_replace( '/^on line \d+ ".*?"(?=\s|$)/s', '', $message );
        return trim( preg_replace( '/\s+/', ' ', $message ) );
    }

    private static function line( Throwable $e, $message )
    {
        if ( preg_match( '/line (\d+)/i', (string)$message, $m ) )
            return (int)$m[1];
        return $e instanceof \Smarty\CompilerException ? (int)$e->getLine() : 0;
    }
}
