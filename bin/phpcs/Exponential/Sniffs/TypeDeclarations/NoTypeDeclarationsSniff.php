<?php
/**
 * Exponential code is written without the type declarations of PHP 7 and later,
 * as the 6.0.15 code is. The parameter hints PHP 5 already had stay allowed:
 * array, callable, self and class names, also nullable (?array, ?eZINI), as the
 * kernel uses them throughout.
 *
 * Reported, each with its own code so a ruleset can switch one off:
 *
 *   StrictTypes      declare( strict_types=1 )
 *   ParameterType    function foo( int $a ), function foo( string|array $a ): scalar and
 *                    pseudo types (int, string, float, bool, mixed, iterable, object, ...)
 *                    and union or intersection types, also on promoted constructor parameters
 *   ReturnType       function foo(): array
 *   PropertyType     public string $name;
 *   ConstantType     const string NAME = '...';
 *
 * Files under tests/ may declare the return types PHPUnit requires (: void on
 * setUp(), tearDown() and the other template methods), so ReturnType "void" is
 * allowed there.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package bin
 */

namespace Exponential\Sniffs\TypeDeclarations;

use PHP_CodeSniffer\Sniffs\Sniff;

class NoTypeDeclarationsSniff implements Sniff
{
    /**
     * Returns the tokens this sniff looks at
     *
     * @return array
     */
    public function register()
    {
        $tokens = array( T_DECLARE, T_FUNCTION, T_CLOSURE, T_VARIABLE, T_CONST );
        if ( defined( 'T_FN' ) )
        {
            $tokens[] = T_FN;
        }
        return $tokens;
    }

    /**
     * Reports the type declarations at $stackPtr
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process( $phpcsFile, $stackPtr )
    {
        $tokens = $phpcsFile->getTokens();
        $code = $tokens[$stackPtr]['code'];

        if ( $code === T_DECLARE )
        {
            $this->processDeclare( $phpcsFile, $stackPtr );
        }
        else if ( $code === T_VARIABLE )
        {
            $this->processProperty( $phpcsFile, $stackPtr );
        }
        else if ( $code === T_CONST )
        {
            $this->processConstant( $phpcsFile, $stackPtr );
        }
        else
        {
            $this->processFunction( $phpcsFile, $stackPtr );
        }
    }

    /**
     * declare( strict_types=1 )
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile
     * @param int $stackPtr
     */
    protected function processDeclare( $phpcsFile, $stackPtr )
    {
        $tokens = $phpcsFile->getTokens();
        if ( !isset( $tokens[$stackPtr]['parenthesis_opener'], $tokens[$stackPtr]['parenthesis_closer'] ) )
        {
            return;
        }
        $content = $phpcsFile->getTokensAsString( $tokens[$stackPtr]['parenthesis_opener'],
                                                  $tokens[$stackPtr]['parenthesis_closer'] - $tokens[$stackPtr]['parenthesis_opener'] + 1 );
        if ( stripos( $content, 'strict_types' ) !== false )
        {
            $phpcsFile->addError( 'Exponential code does not declare strict_types', $stackPtr, 'StrictTypes' );
        }
    }

    /**
     * Parameter and return types of functions, methods, closures and arrow functions
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile
     * @param int $stackPtr
     */
    protected function processFunction( $phpcsFile, $stackPtr )
    {
        foreach ( $phpcsFile->getMethodParameters( $stackPtr ) as $parameter )
        {
            if ( $this->isModernParameterType( $parameter['type_hint'] ) )
            {
                $phpcsFile->addError( 'Exponential code declares no parameter types; found %s %s',
                                      $parameter['token'], 'ParameterType',
                                      array( $parameter['type_hint'], $parameter['name'] ) );
            }
        }

        $properties = $phpcsFile->getMethodProperties( $stackPtr );
        if ( $properties['return_type'] === '' )
        {
            return;
        }
        if ( $this->isTestFile( $phpcsFile ) && strtolower( ltrim( $properties['return_type'], '?' ) ) === 'void' )
        {
            return;
        }
        $phpcsFile->addError( 'Exponential code declares no return types; found %s',
                              $properties['return_type_token'] !== false ? $properties['return_type_token'] : $stackPtr,
                              'ReturnType', array( $properties['return_type'] ) );
    }

    /**
     * Tells whether a parameter type hint is one PHP 5 did not have: a scalar or
     * pseudo type, or a union or intersection type. array, callable, self and
     * class names, also nullable, are not.
     *
     * @param string $typeHint
     * @return bool
     */
    protected function isModernParameterType( $typeHint )
    {
        $typeHint = ltrim( trim( $typeHint ), '?' );
        if ( $typeHint === '' )
        {
            return false;
        }
        if ( strpbrk( $typeHint, '|&()' ) !== false )
        {
            return true;
        }
        $modern = array( 'int', 'integer', 'string', 'float', 'double', 'bool', 'boolean', 'mixed',
                         'iterable', 'object', 'false', 'true', 'null', 'void', 'never', 'static' );
        return in_array( strtolower( $typeHint ), $modern, true );
    }

    /**
     * Types of class, trait and enum properties
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile
     * @param int $stackPtr
     */
    protected function processProperty( $phpcsFile, $stackPtr )
    {
        if ( !$this->isProperty( $phpcsFile, $stackPtr ) )
        {
            return;
        }
        try
        {
            $properties = $phpcsFile->getMemberProperties( $stackPtr );
        }
        catch ( \Exception $e )
        {
            return;
        }
        if ( $properties['type'] !== '' )
        {
            $phpcsFile->addError( 'Exponential code declares no property types; found %s %s',
                                  $stackPtr, 'PropertyType',
                                  array( $properties['type'], $phpcsFile->getTokens()[$stackPtr]['content'] ) );
        }
    }

    /**
     * Typed class constants: const string NAME = '...'
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile
     * @param int $stackPtr
     */
    protected function processConstant( $phpcsFile, $stackPtr )
    {
        $tokens = $phpcsFile->getTokens();
        $equals = $phpcsFile->findNext( array( T_EQUAL, T_SEMICOLON ), $stackPtr + 1 );
        if ( $equals === false || $tokens[$equals]['code'] !== T_EQUAL )
        {
            return;
        }
        $parts = 0;
        for ( $i = $stackPtr + 1; $i < $equals; $i++ )
        {
            if ( !in_array( $tokens[$i]['code'], array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ), true ) )
            {
                $parts++;
            }
        }
        // "const NAME =" has one part between const and "=", a typed constant more
        if ( $parts > 1 )
        {
            $phpcsFile->addError( 'Exponential code declares no constant types', $stackPtr, 'ConstantType' );
        }
    }

    /**
     * Tells whether the variable at $stackPtr declares a property: directly inside
     * a class, trait or enum body, not inside a method
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile
     * @param int $stackPtr
     * @return bool
     */
    protected function isProperty( $phpcsFile, $stackPtr )
    {
        $tokens = $phpcsFile->getTokens();
        if ( empty( $tokens[$stackPtr]['conditions'] ) )
        {
            return false;
        }
        $conditions = $tokens[$stackPtr]['conditions'];
        $last = end( $conditions );
        $scopes = array( T_CLASS, T_TRAIT, T_ANON_CLASS );
        if ( defined( 'T_ENUM' ) )
        {
            $scopes[] = T_ENUM;
        }
        return in_array( $last, $scopes, true ) && !isset( $tokens[$stackPtr]['nested_parenthesis'] );
    }

    /**
     * Tells whether the file is a test (under tests/)
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile
     * @return bool
     */
    protected function isTestFile( $phpcsFile )
    {
        $path = str_replace( '\\', '/', $phpcsFile->getFilename() );
        return strpos( $path, '/tests/' ) !== false || strpos( $path, 'tests/' ) === 0;
    }
}
