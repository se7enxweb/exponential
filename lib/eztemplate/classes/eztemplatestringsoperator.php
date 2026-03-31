<?php
/**
 * File containing the eZTemplateStringsOperator class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZTemplateStringsOperator eztemplatestringsoperator.php
  \ingroup eZTemplateOperators
  \brief PHP string function operators for eZ Publish / Exponential CMS templates.

  Provides template access to PHP string functions documented at
  https://www.php.net/manual/en/ref.strings.php that are NOT already
  covered by existing Exponential template operators.

  The piped input value maps to the primary string argument of each PHP
  function. Additional template parameters map to the remaining PHP arguments
  in left-to-right positional order.

  The following operators are intentionally NOT implemented in this class
  because they are already available as built-in Exponential template operators:
    chr, ord, trim, nl2br, rot13/str_rot13, crc32, md5, sha1, concat,
    indent, upcase/strtoupper, downcase/strtolower, count_chars (mb_strlen),
    count_words, break (nl2br), wrap/wordwrap, shorten, pad/str_pad,
    upfirst/ucfirst, upword/ucwords, simplify, wash/htmlspecialchars,
    append, prepend, merge, contains, compare, extract, extract_left,
    extract_right, begins_with, ends_with, implode, explode,
    repeat/str_repeat, reverse/strrev, insert, remove, replace, unique,
    array_sum.

  Creative additions:
  - ristring: str_replace(search, replace, subject) — case-sensitive replace
  - rstring:  str_ireplace(search, replace, subject) — case-insensitive replace

\code
// Usage examples:
{$str|addslashes}
{$str|strlen}
{$str|bin2hex}
{$str|hex2bin}
{$str|lcfirst}
{$str|quotemeta}
{$str|soundex}
{$str|strip_tags}
{$str|strip_tags('<p><a>')}
{$str|chunk_split(76, "\r\n")}
{$str|ltrim}
{$str|ltrim(" \t")}
{$str|rtrim}
{$str|strpos("world")}
{$str|strpos("world", 5)}
{$str|substr(0, 5)}
{$str|substr_replace("insert", 3)}
{$str|substr_count("needle")}
{$str|str_split(3)}
{$str|str_word_count}
{$str|str_contains("needle")}
{$str|str_starts_with("prefix")}
{$str|str_ends_with("suffix")}
{$str|strcmp("other")}
{$str|strcasecmp("other")}
{$str|levenshtein("other")}
{$str|similar_text("other")}
{$str|sprintf($arg1, $arg2)}
{$str|number_format(2, ".", ",")}
{$str|htmlentities}
{$str|html_entity_decode}
{$str|htmlspecialchars_decode}
{$str|ristring("search", "replacement")}
{$str|rstring("Search", "replacement")}
\endcode

*/

class eZTemplateStringsOperator
{
    // Operator registry arrays
    public $Operators;
    public $NoParamOperators;
    public $ParamOperators;

    // No-parameter operator name aliases
    public $AddslashesName;
    public $Bin2hexName;
    public $Convert_uudecodeName;
    public $Convert_uuencodeName;
    public $Hex2binName;
    public $LcfirstName;
    public $Quoted_printable_decodeName;
    public $Quoted_printable_encodeName;
    public $QuotemetaName;
    public $SoundexName;
    public $Str_shuffleName;
    public $StripcslashesName;
    public $StrlenName;
    public $StripslashesName;
    // PHP 8.3+ (null until conditionally set)
    public $Str_incrementName = null;
    public $Str_decrementName = null;

    // Parameter operator name aliases
    public $AddcslashesName;
    public $Chunk_splitName;
    public $HebrevName;
    public $Html_entity_decodeName;
    public $HtmlentitiesName;
    public $Htmlspecialchars_decodeName;
    public $LevenshteinName;
    public $LtrimName;
    public $MetaphoneName;
    public $Number_formatName;
    public $RtrimName;
    public $Similar_textName;
    public $SprintfName;
    public $Str_containsName;
    public $Str_ends_withName;
    public $Str_getcsvName;
    public $Str_splitName;
    public $Str_starts_withName;
    public $Str_word_countName;
    public $StrcasecmpName;
    public $StrcmpName;
    public $StrcollName;
    public $StrcspnName;
    public $Strip_tagsName;
    public $StriposName;
    public $StristrName;
    public $StrnatcasecmpName;
    public $StrnatcmpName;
    public $StrncasecmpName;
    public $StrncmpName;
    public $StrpbrkName;
    public $StrposName;
    public $StrrchrName;
    public $StrriposName;
    public $StrrposName;
    public $StrspnName;
    public $StrstrName;
    public $StrtokName;
    public $StrtrName;
    public $SubstrName;
    public $Substr_compareName;
    public $Substr_countName;
    public $Substr_replaceName;
    public $VsprintfName;
    // Creative additions
    public $RistringName;
    public $RstringName;

    public function __construct()
    {
        // Operators whose name matches the PHP function exactly and accept
        // NO additional parameters (the piped input is the only argument).
        $this->NoParamOperators = array(
            'addslashes',
            'bin2hex',
            'convert_uudecode',
            'convert_uuencode',
            'hex2bin',
            'lcfirst',
            'quoted_printable_decode',
            'quoted_printable_encode',
            'quotemeta',
            'soundex',
            'str_shuffle',
            'stripcslashes',
            'strlen',
            'stripslashes',
        );

        // PHP 8.3+ operators — registered only when the functions exist.
        if ( function_exists( 'str_increment' ) )
        {
            $this->NoParamOperators[] = 'str_increment';
        }
        if ( function_exists( 'str_decrement' ) )
        {
            $this->NoParamOperators[] = 'str_decrement';
        }

        // Operators that accept one or more additional template parameters.
        $this->ParamOperators = array(
            'addcslashes',
            'chunk_split',
            'hebrev',
            'html_entity_decode',
            'htmlentities',
            'htmlspecialchars_decode',
            'levenshtein',
            'ltrim',
            'metaphone',
            'number_format',
            'rtrim',
            'similar_text',
            'sprintf',
            'str_contains',
            'str_ends_with',
            'str_getcsv',
            'str_split',
            'str_starts_with',
            'str_word_count',
            'strcasecmp',
            'strcmp',
            'strcoll',
            'strcspn',
            'strip_tags',
            'stripos',
            'stristr',
            'strnatcasecmp',
            'strnatcmp',
            'strncasecmp',
            'strncmp',
            'strpbrk',
            'strpos',
            'strrchr',
            'strripos',
            'strrpos',
            'strspn',
            'strstr',
            'strtok',
            'strtr',
            'substr',
            'substr_compare',
            'substr_count',
            'substr_replace',
            'vsprintf',
            'ristring',
            'rstring',
        );

        $this->Operators = array_values(
            array_unique( array_merge( $this->NoParamOperators, $this->ParamOperators ) )
        );

        // Build property aliases: e.g. 'addslashes' → $this->AddslashesName = 'addslashes'
        foreach ( $this->Operators as $operator )
        {
            $name    = $operator . 'Name';
            $name[0] = $name[0] & "\xdf";
            $this->$name = $operator;
        }
    }

    /*!
     Returns the list of operators this class provides.
    */
    function operatorList()
    {
        return $this->Operators;
    }

    /*!
     Returns template hints for all operators.  No named parameters are used;
     all arguments are positional.
    */
    function operatorTemplateHints()
    {
        $hints = array();

        foreach ( $this->NoParamOperators as $op )
        {
            $hints[$op] = array(
                'input'                       => true,
                'output'                      => true,
                'parameters'                  => false,
                'element-transformation'      => true,
                'transform-parameters'        => true,
                'input-as-parameter'          => 'always',
                'element-transformation-func' => 'noParamTransformation',
            );
        }

        foreach ( $this->ParamOperators as $op )
        {
            $hints[$op] = array(
                'input'      => true,
                'output'     => true,
                'parameters' => true,
            );
        }

        return $hints;
    }

    /*!
     \private
     Template compiler transformation for no-parameter operators.
     The operator name must equal the PHP function name.

     For constant inputs the result is inlined at compile time.
     For variable inputs the PHP call is emitted as generated code.
    */
    function noParamTransformation( $operatorName, $node, $tpl, $resourceData,
                                    $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        if ( count( $parameters ) !== 1 )
        {
            return false;
        }

        $phpFunc = $operatorName;
        $values  = array();

        if ( eZTemplateNodeTool::isConstantElement( $parameters[0] ) )
        {
            $result = $phpFunc( eZTemplateNodeTool::elementConstantValue( $parameters[0] ) );
            return array( eZTemplateNodeTool::createConstantElement( $result ) );
        }

        $values[] = $parameters[0];
        $code     = '%output% = ' . $phpFunc . '( %1% );' . "\n";
        return array( eZTemplateNodeTool::createCodePieceElement( $code, $values ) );
    }

    /*!
     Runtime evaluation for all operators.

     $operatorValue holds the piped input string.
     $operatorParameters holds the extra template arguments.
    */
    function modify( $tpl,
                     $operatorName,
                     $operatorParameters,
                     $rootNamespace,
                     $currentNamespace,
                     &$operatorValue,
                     $namedParameters,
                     $placement )
    {
        $paramCount = count( $operatorParameters );
        $params     = array();
        for ( $i = 0; $i < $paramCount; $i++ )
        {
            $params[$i] = $tpl->elementValue(
                $operatorParameters[$i],
                $rootNamespace,
                $currentNamespace,
                $placement
            );
        }

        // Convenience alias: most operators work on a string cast of the input.
        $str = (string) $operatorValue;

        switch ( $operatorName )
        {
            // ----------------------------------------------------------------
            // No-parameter operators (direct PHP wrappers, input only)
            // ----------------------------------------------------------------

            case 'addslashes':
                $operatorValue = addslashes( $str );
                break;

            case 'stripslashes':
                $operatorValue = stripslashes( $str );
                break;

            case 'stripcslashes':
                $operatorValue = stripcslashes( $str );
                break;

            case 'quotemeta':
                $operatorValue = quotemeta( $str );
                break;

            case 'lcfirst':
                $operatorValue = lcfirst( $str );
                break;

            case 'str_shuffle':
                $operatorValue = str_shuffle( $str );
                break;

            case 'bin2hex':
                $operatorValue = bin2hex( $str );
                break;

            case 'hex2bin':
                // Returns false on invalid input; suppress PHP's E_WARNING for
                // odd-length or non-hex strings so the template doesn't crash.
                $result = @hex2bin( $str );
                $operatorValue = ( $result === false ) ? false : $result;
                break;

            case 'convert_uuencode':
                $operatorValue = convert_uuencode( $str );
                break;

            case 'convert_uudecode':
                // Suppress E_WARNING for malformed uuencoded input.
                $result = @convert_uudecode( $str );
                $operatorValue = ( $result === false ) ? false : $result;
                break;

            case 'quoted_printable_encode':
                $operatorValue = quoted_printable_encode( $str );
                break;

            case 'quoted_printable_decode':
                $operatorValue = quoted_printable_decode( $str );
                break;

            case 'soundex':
                $operatorValue = soundex( $str );
                break;

            case 'strlen':
                // Returns byte length. For character length use count_chars (uses mb_strlen).
                $operatorValue = strlen( $str );
                break;

            case 'str_increment':
                if ( function_exists( 'str_increment' ) )
                {
                    $operatorValue = str_increment( $str );
                }
                break;

            case 'str_decrement':
                if ( function_exists( 'str_decrement' ) )
                {
                    $operatorValue = str_decrement( $str );
                }
                break;

            // ----------------------------------------------------------------
            // Multi-parameter operators
            // ----------------------------------------------------------------

            case 'addcslashes':
                // addcslashes( string, charlist ) — charlist required
                if ( $paramCount >= 1 )
                {
                    $operatorValue = addcslashes( $str, (string) $params[0] );
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: charlist', $placement );
                }
                break;

            case 'chunk_split':
                // chunk_split( string [, chunklen [, end]] )
                if ( $paramCount === 0 )
                {
                    $operatorValue = chunk_split( $str );
                }
                elseif ( $paramCount === 1 )
                {
                    $operatorValue = chunk_split( $str, (int) $params[0] );
                }
                else
                {
                    $operatorValue = chunk_split( $str, (int) $params[0], (string) $params[1] );
                }
                break;

            case 'hebrev':
                // hebrev( hebrew_text [, max_chars_per_line] )
                if ( $paramCount >= 1 )
                {
                    $operatorValue = hebrev( $str, (int) $params[0] );
                }
                else
                {
                    $operatorValue = hebrev( $str );
                }
                break;

            case 'html_entity_decode':
                // html_entity_decode( string [, flags [, encoding]] )
                // Defaults: ENT_QUOTES | ENT_HTML5, 'UTF-8'
                if ( $paramCount === 0 )
                {
                    $operatorValue = html_entity_decode( $str, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
                }
                elseif ( $paramCount === 1 )
                {
                    $operatorValue = html_entity_decode( $str, (int) $params[0], 'UTF-8' );
                }
                else
                {
                    $operatorValue = html_entity_decode( $str, (int) $params[0], (string) $params[1] );
                }
                break;

            case 'htmlentities':
                // htmlentities( string [, flags [, encoding [, double_encode]]] )
                // Defaults: ENT_QUOTES, 'UTF-8'
                if ( $paramCount === 0 )
                {
                    $operatorValue = htmlentities( $str, ENT_QUOTES, 'UTF-8' );
                }
                elseif ( $paramCount === 1 )
                {
                    $operatorValue = htmlentities( $str, (int) $params[0], 'UTF-8' );
                }
                elseif ( $paramCount === 2 )
                {
                    $operatorValue = htmlentities( $str, (int) $params[0], (string) $params[1] );
                }
                else
                {
                    $operatorValue = htmlentities( $str, (int) $params[0], (string) $params[1], (bool) $params[2] );
                }
                break;

            case 'htmlspecialchars_decode':
                // htmlspecialchars_decode( string [, flags] )
                // Default: ENT_QUOTES
                if ( $paramCount >= 1 )
                {
                    $operatorValue = htmlspecialchars_decode( $str, (int) $params[0] );
                }
                else
                {
                    $operatorValue = htmlspecialchars_decode( $str, ENT_QUOTES );
                }
                break;

            case 'levenshtein':
                // levenshtein( str1, str2 ) — str2 required
                if ( $paramCount >= 1 )
                {
                    $operatorValue = levenshtein( $str, (string) $params[0] );
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: string2', $placement );
                }
                break;

            case 'ltrim':
                // ltrim( string [, chars] )
                if ( $paramCount >= 1 )
                {
                    $operatorValue = ltrim( $str, (string) $params[0] );
                }
                else
                {
                    $operatorValue = ltrim( $str );
                }
                break;

            case 'metaphone':
                // metaphone( string [, max_phonemes] )
                if ( $paramCount >= 1 )
                {
                    $operatorValue = metaphone( $str, (int) $params[0] );
                }
                else
                {
                    $operatorValue = metaphone( $str );
                }
                break;

            case 'number_format':
                // number_format( number [, decimals [, dec_point, thousands_sep]] )
                // Note: PHP requires dec_point and thousands_sep to be given together.
                $num = (float) $operatorValue;
                if ( $paramCount === 0 )
                {
                    $operatorValue = number_format( $num );
                }
                elseif ( $paramCount === 1 )
                {
                    $operatorValue = number_format( $num, (int) $params[0] );
                }
                elseif ( $paramCount === 2 )
                {
                    // Provide a sensible default for thousands_sep when only dec_point given.
                    $operatorValue = number_format( $num, (int) $params[0], (string) $params[1], ',' );
                }
                else
                {
                    $operatorValue = number_format( $num, (int) $params[0], (string) $params[1], (string) $params[2] );
                }
                break;

            case 'rtrim':
                // rtrim( string [, chars] )
                if ( $paramCount >= 1 )
                {
                    $operatorValue = rtrim( $str, (string) $params[0] );
                }
                else
                {
                    $operatorValue = rtrim( $str );
                }
                break;

            case 'similar_text':
                // similar_text( str1, str2 ) — returns count of matching chars; str2 required
                if ( $paramCount >= 1 )
                {
                    $operatorValue = similar_text( $str, (string) $params[0] );
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: string2', $placement );
                }
                break;

            case 'sprintf':
                // sprintf( format, ...args ) — format is operatorValue, args are template params
                // Uses vsprintf internally to accept a flat list of positional parameters.
                $operatorValue = vsprintf( $str, $params );
                break;

            case 'str_contains':
                // str_contains( haystack, needle ) — PHP 8.0+; fallback for older PHP
                if ( $paramCount >= 1 )
                {
                    if ( function_exists( 'str_contains' ) )
                    {
                        $operatorValue = str_contains( $str, (string) $params[0] );
                    }
                    else
                    {
                        $needle        = (string) $params[0];
                        $operatorValue = ( $needle === '' ) ? true : ( strpos( $str, $needle ) !== false );
                    }
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: needle', $placement );
                }
                break;

            case 'str_ends_with':
                // str_ends_with( haystack, needle ) — PHP 8.0+; fallback for older PHP
                if ( $paramCount >= 1 )
                {
                    if ( function_exists( 'str_ends_with' ) )
                    {
                        $operatorValue = str_ends_with( $str, (string) $params[0] );
                    }
                    else
                    {
                        $needle        = (string) $params[0];
                        $needleLen     = strlen( $needle );
                        $operatorValue = ( $needleLen === 0 ) ? true : ( substr( $str, -$needleLen ) === $needle );
                    }
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: needle', $placement );
                }
                break;

            case 'str_getcsv':
                // str_getcsv( string [, separator [, enclosure [, escape]]] )
                // PHP 8.4+ deprecates non-empty $escape; pass '' to opt-in to the
                // RFC 4180-compliant behaviour and silence the deprecation notice.
                if ( $paramCount === 0 )
                {
                    $operatorValue = str_getcsv( $str, ',', '"', '' );
                }
                elseif ( $paramCount === 1 )
                {
                    $operatorValue = str_getcsv( $str, (string) $params[0], '"', '' );
                }
                elseif ( $paramCount === 2 )
                {
                    $operatorValue = str_getcsv( $str, (string) $params[0], (string) $params[1], '' );
                }
                else
                {
                    // Caller explicitly provides the escape param (may be '').
                    $operatorValue = str_getcsv( $str, (string) $params[0], (string) $params[1], (string) $params[2] );
                }
                break;

            case 'str_split':
                // str_split( string [, length] )
                if ( $paramCount >= 1 )
                {
                    $operatorValue = str_split( $str, (int) $params[0] );
                }
                else
                {
                    $operatorValue = str_split( $str );
                }
                break;

            case 'str_starts_with':
                // str_starts_with( haystack, needle ) — PHP 8.0+; fallback for older PHP
                if ( $paramCount >= 1 )
                {
                    if ( function_exists( 'str_starts_with' ) )
                    {
                        $operatorValue = str_starts_with( $str, (string) $params[0] );
                    }
                    else
                    {
                        $needle        = (string) $params[0];
                        $operatorValue = ( $needle === '' ) ? true : ( strpos( $str, $needle ) === 0 );
                    }
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: needle', $placement );
                }
                break;

            case 'str_word_count':
                // str_word_count( string [, format [, charlist]] )
                // format 0 (default) = integer count; 1 = array of words; 2 = assoc array pos=>word
                if ( $paramCount === 0 )
                {
                    $operatorValue = str_word_count( $str );
                }
                elseif ( $paramCount === 1 )
                {
                    $operatorValue = str_word_count( $str, (int) $params[0] );
                }
                else
                {
                    $operatorValue = str_word_count( $str, (int) $params[0], (string) $params[1] );
                }
                break;

            case 'strcasecmp':
                // strcasecmp( str1, str2 ) — case-insensitive comparison; str2 required
                if ( $paramCount >= 1 )
                {
                    $operatorValue = strcasecmp( $str, (string) $params[0] );
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: string2', $placement );
                }
                break;

            case 'strcmp':
                // strcmp( str1, str2 ) — binary-safe comparison; str2 required
                if ( $paramCount >= 1 )
                {
                    $operatorValue = strcmp( $str, (string) $params[0] );
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: string2', $placement );
                }
                break;

            case 'strcoll':
                // strcoll( str1, str2 ) — locale-based comparison; str2 required
                if ( $paramCount >= 1 )
                {
                    $operatorValue = strcoll( $str, (string) $params[0] );
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: string2', $placement );
                }
                break;

            case 'strcspn':
                // strcspn( str, mask [, offset [, length]] ) — mask required
                if ( $paramCount === 1 )
                {
                    $operatorValue = strcspn( $str, (string) $params[0] );
                }
                elseif ( $paramCount === 2 )
                {
                    $operatorValue = strcspn( $str, (string) $params[0], (int) $params[1] );
                }
                elseif ( $paramCount >= 3 )
                {
                    $operatorValue = strcspn( $str, (string) $params[0], (int) $params[1], (int) $params[2] );
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: mask', $placement );
                }
                break;

            case 'strip_tags':
                // strip_tags( string [, allowed_tags] )
                if ( $paramCount >= 1 )
                {
                    $operatorValue = strip_tags( $str, $params[0] );
                }
                else
                {
                    $operatorValue = strip_tags( $str );
                }
                break;

            case 'stripos':
                // stripos( haystack, needle [, offset] ) — case-insensitive strpos; needle required
                if ( $paramCount === 1 )
                {
                    $result        = stripos( $str, (string) $params[0] );
                    $operatorValue = ( $result === false ) ? false : $result;
                }
                elseif ( $paramCount >= 2 )
                {
                    $result        = stripos( $str, (string) $params[0], (int) $params[1] );
                    $operatorValue = ( $result === false ) ? false : $result;
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: needle', $placement );
                }
                break;

            case 'stristr':
                // stristr( haystack, needle [, before_needle] ) — case-insensitive strstr; needle required
                if ( $paramCount === 1 )
                {
                    $result        = stristr( $str, (string) $params[0] );
                    $operatorValue = ( $result === false ) ? false : $result;
                }
                elseif ( $paramCount >= 2 )
                {
                    $result        = stristr( $str, (string) $params[0], (bool) $params[1] );
                    $operatorValue = ( $result === false ) ? false : $result;
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: needle', $placement );
                }
                break;

            case 'strnatcasecmp':
                // strnatcasecmp( str1, str2 ) — case-insensitive natural-order comparison; str2 required
                if ( $paramCount >= 1 )
                {
                    $operatorValue = strnatcasecmp( $str, (string) $params[0] );
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: string2', $placement );
                }
                break;

            case 'strnatcmp':
                // strnatcmp( str1, str2 ) — natural-order comparison; str2 required
                if ( $paramCount >= 1 )
                {
                    $operatorValue = strnatcmp( $str, (string) $params[0] );
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: string2', $placement );
                }
                break;

            case 'strncasecmp':
                // strncasecmp( str1, str2, n ) — compare first n chars, case-insensitive; str2 and n required
                if ( $paramCount >= 2 )
                {
                    $operatorValue = strncasecmp( $str, (string) $params[0], (int) $params[1] );
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameters: string2 and n', $placement );
                }
                break;

            case 'strncmp':
                // strncmp( str1, str2, n ) — compare first n chars; str2 and n required
                if ( $paramCount >= 2 )
                {
                    $operatorValue = strncmp( $str, (string) $params[0], (int) $params[1] );
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameters: string2 and n', $placement );
                }
                break;

            case 'strpbrk':
                // strpbrk( haystack, char_list ) — search for any char in char_list; char_list required
                if ( $paramCount >= 1 )
                {
                    $result        = strpbrk( $str, (string) $params[0] );
                    $operatorValue = ( $result === false ) ? false : $result;
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: char_list', $placement );
                }
                break;

            case 'strpos':
                // strpos( haystack, needle [, offset] ) — needle required
                if ( $paramCount === 1 )
                {
                    $result        = strpos( $str, (string) $params[0] );
                    $operatorValue = ( $result === false ) ? false : $result;
                }
                elseif ( $paramCount >= 2 )
                {
                    $result        = strpos( $str, (string) $params[0], (int) $params[1] );
                    $operatorValue = ( $result === false ) ? false : $result;
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: needle', $placement );
                }
                break;

            case 'strrchr':
                // strrchr( haystack, needle ) — last occurrence; needle required
                if ( $paramCount >= 1 )
                {
                    $result        = strrchr( $str, (string) $params[0] );
                    $operatorValue = ( $result === false ) ? false : $result;
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: needle', $placement );
                }
                break;

            case 'strripos':
                // strripos( haystack, needle [, offset] ) — case-insensitive strrpos; needle required
                if ( $paramCount === 1 )
                {
                    $result        = strripos( $str, (string) $params[0] );
                    $operatorValue = ( $result === false ) ? false : $result;
                }
                elseif ( $paramCount >= 2 )
                {
                    $result        = strripos( $str, (string) $params[0], (int) $params[1] );
                    $operatorValue = ( $result === false ) ? false : $result;
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: needle', $placement );
                }
                break;

            case 'strrpos':
                // strrpos( haystack, needle [, offset] ) — needle required
                if ( $paramCount === 1 )
                {
                    $result        = strrpos( $str, (string) $params[0] );
                    $operatorValue = ( $result === false ) ? false : $result;
                }
                elseif ( $paramCount >= 2 )
                {
                    $result        = strrpos( $str, (string) $params[0], (int) $params[1] );
                    $operatorValue = ( $result === false ) ? false : $result;
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: needle', $placement );
                }
                break;

            case 'strspn':
                // strspn( str, mask [, offset [, length]] ) — mask required
                if ( $paramCount === 1 )
                {
                    $operatorValue = strspn( $str, (string) $params[0] );
                }
                elseif ( $paramCount === 2 )
                {
                    $operatorValue = strspn( $str, (string) $params[0], (int) $params[1] );
                }
                elseif ( $paramCount >= 3 )
                {
                    $operatorValue = strspn( $str, (string) $params[0], (int) $params[1], (int) $params[2] );
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: mask', $placement );
                }
                break;

            case 'strstr':
                // strstr( haystack, needle [, before_needle] ) — needle required
                if ( $paramCount === 1 )
                {
                    $result        = strstr( $str, (string) $params[0] );
                    $operatorValue = ( $result === false ) ? false : $result;
                }
                elseif ( $paramCount >= 2 )
                {
                    $result        = strstr( $str, (string) $params[0], (bool) $params[1] );
                    $operatorValue = ( $result === false ) ? false : $result;
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: needle', $placement );
                }
                break;

            case 'strtok':
                // strtok( string, token ) — token required
                // Note: PHP strtok is stateful. In a template this returns the first token only.
                // Subsequent {strtok(token)} calls without a string will continue tokenizing.
                if ( $paramCount >= 1 )
                {
                    $result        = strtok( $str, (string) $params[0] );
                    $operatorValue = ( $result === false ) ? false : $result;
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: token', $placement );
                }
                break;

            case 'strtr':
                // strtr( string, from, to ) or strtr( string, replace_pairs_array )
                if ( $paramCount >= 2 )
                {
                    $operatorValue = strtr( $str, (string) $params[0], (string) $params[1] );
                }
                elseif ( $paramCount === 1 && is_array( $params[0] ) )
                {
                    $operatorValue = strtr( $str, $params[0] );
                }
                else
                {
                    $tpl->warning( $operatorName,
                        'Requires (from_string, to_string) or a single associative array',
                        $placement );
                }
                break;

            case 'substr':
                // substr( string, start [, length] ) — start required
                if ( $paramCount === 1 )
                {
                    $operatorValue = substr( $str, (int) $params[0] );
                }
                elseif ( $paramCount >= 2 )
                {
                    $operatorValue = substr( $str, (int) $params[0], (int) $params[1] );
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: start', $placement );
                }
                break;

            case 'substr_compare':
                // substr_compare( main_str, str, offset [, length [, case_insensitive]] )
                // str and offset required
                if ( $paramCount === 2 )
                {
                    $operatorValue = substr_compare( $str, (string) $params[0], (int) $params[1] );
                }
                elseif ( $paramCount === 3 )
                {
                    $operatorValue = substr_compare( $str, (string) $params[0], (int) $params[1], (int) $params[2] );
                }
                elseif ( $paramCount >= 4 )
                {
                    $operatorValue = substr_compare( $str, (string) $params[0], (int) $params[1], (int) $params[2], (bool) $params[3] );
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameters: str and offset', $placement );
                }
                break;

            case 'substr_count':
                // substr_count( haystack, needle [, offset [, length]] ) — needle required
                if ( $paramCount === 1 )
                {
                    $operatorValue = substr_count( $str, (string) $params[0] );
                }
                elseif ( $paramCount === 2 )
                {
                    $operatorValue = substr_count( $str, (string) $params[0], (int) $params[1] );
                }
                elseif ( $paramCount >= 3 )
                {
                    $operatorValue = substr_count( $str, (string) $params[0], (int) $params[1], (int) $params[2] );
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: needle', $placement );
                }
                break;

            case 'substr_replace':
                // substr_replace( string, replace, start [, length] ) — replace and start required
                if ( $paramCount === 2 )
                {
                    $operatorValue = substr_replace( $str, (string) $params[0], (int) $params[1] );
                }
                elseif ( $paramCount >= 3 )
                {
                    $operatorValue = substr_replace( $str, (string) $params[0], (int) $params[1], (int) $params[2] );
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameters: replace and start', $placement );
                }
                break;

            case 'vsprintf':
                // vsprintf( format, args_array ) — format is operatorValue, args_array is params[0]
                if ( $paramCount >= 1 )
                {
                    $argsArray     = is_array( $params[0] ) ? $params[0] : (array) $params[0];
                    $operatorValue = vsprintf( $str, $argsArray );
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameter: args_array', $placement );
                }
                break;

            // ----------------------------------------------------------------
            // Creative additions
            // ----------------------------------------------------------------

            case 'ristring':
                // str_replace( search, replace, subject ) — case-sensitive in-string replace
                // Template: {$subject|ristring( "search", "replacement" )}
                // Supports scalar and array search/replace patterns.
                if ( $paramCount >= 2 )
                {
                    $operatorValue = str_replace( $params[0], $params[1], $operatorValue );
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameters: search and replace', $placement );
                }
                break;

            case 'rstring':
                // str_ireplace( search, replace, subject ) — case-insensitive in-string replace
                // Template: {$subject|rstring( "Search", "replacement" )}
                // Supports scalar and array search/replace patterns.
                if ( $paramCount >= 2 )
                {
                    $operatorValue = str_ireplace( $params[0], $params[1], $operatorValue );
                }
                else
                {
                    $tpl->warning( $operatorName, 'Missing required parameters: search and replace', $placement );
                }
                break;

            default:
            {
                $tpl->warning( $operatorName, "Unknown strings operator '{$operatorName}'", $placement );
            } break;
        }
    }
}

?>
