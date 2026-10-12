<?php
/**
 * File containing the eZNamePatternResolver class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 95d86f5990), Copyright (C) Mugo Web, GPL v2
 */

/**
 * eZNamePatternResolver is a utility class for resolving object name and url alias patterns.
 * This code supports object name pattern groups.
 *
 * Syntax:
 * <code>
 * &lt;attribute_identifier&gt;
 * &lt;attribute_identifier&gt; &lt;2nd-identifier&gt;
 * User text &lt;attribute_identifier&gt;|(&lt;2nd-identifier&gt;&lt;3rd-identifier&gt;)
 * </code>
 *
 * Example:
 * <code>
 * &lt;nickname|(&lt;firstname&gt; &lt;lastname&gt;)&gt;
 * </code>
 *
 * Tokens are looked up from left to right. If a match is found for the
 * leftmost token, the 2nd token will not be used. Tokens are representations
 * of attributes. So a match means that that the current attribute has data.
 *
 * tokens are the class attribute identifiers which are used in the class
 * edit-interface.
 *
 * @package kernel
 * @version //autogentag//
 */
class eZNamePatternResolver
{
    const FIELD_NAME_MAX_SIZE = 255;

    /**
     * Holds token groups
     *
     * @var array
     */
    private $groupLookupTable;

    /**
     * Contains the original name pattern entered
     *
     * @var string
     */
    private $origNamePattern;

    /**
     * Holds the filtered name pattern where token groups are replaced with
     * meta strings
     *
     * @var string
     */
    private $namePattern;

    /**
     * The content object which holds the attributes used to resolve name pattern.
     *
     * @var eZContentObject
     */
    private $contentObject;

    /**
     * Version number of the content object to fetch attributes from.
     *
     * @var int
     */
    private $version;

    /**
     * Contains the language locale for which to fetch attributes.
     *
     * @var string
     */
    private $translation;

    /**
     * Holds data fetched from content object attributes
     *
     * @var array(string=>string)
     */
    private $attributeArray;


    /**
     * The string to use to signify group tokens.
     *
     * @var string
     */
    private $metaString = 'EZMETAGROUP_';

    /**
     * Constructs a object to resolve $namePattern. $contentVersion and
     * $contentTranslation specify which version and translation respectively
     * of the content object to use.
     *
     * @param string $namePattern
     * @param eZContentObject $contentObject
     * @param int|false $contentVersion
     * @param string|false $contentTranslation
     */
    public function __construct( $namePattern, eZContentObject $contentObject, $contentVersion = false, $contentTranslation = false )
    {
        $this->origNamePattern = $namePattern;
        $this->contentObject = $contentObject;
        $this->version = $contentVersion;
        $this->translation = $contentTranslation;

        $this->namePattern = $this->filterNamePattern( $namePattern );
    }

    /**
     * Return the real name for an object name pattern
     *
     * @param int $limit The limit on the string length, by defaul 0 aka none
     * @param string $sequence End sequence applied to string if limit has been reached
     * @return string
     */
    public function resolveNamePattern( $limit = 0, $sequence = '' )
    {
        // Fetch attributes for present identifiers
        $this->fetchContentAttributes();

        // Replace tokens with real values
        $objectName = $this->translatePattern();

        return self::limitName( $objectName, $limit, $sequence );
    }

    /**
     * $name shortened to $limit characters as the database counts them (0: FIELD_NAME_MAX_SIZE), ending in
     * $sequence when it was shortened.
     *
     * @param string $name
     * @param int|false $limit
     * @param string $sequence
     * @return string
     */
    public static function limitName( $name, $limit = 0, $sequence = '' )
    {
        $db = eZDB::instance();

        $limit = $limit ?: self::FIELD_NAME_MAX_SIZE;

        if ( $db->countStringSize( $name ) <= $limit )
        {
            return $name;
        }

        return preg_replace(
            "/[\pZ\pC]+$/u",
            '',
            $db->truncateString( $name, $limit, 'name', $sequence )
        ). $sequence;
    }

    /**
     * The name $namePattern gives $object: made by the resolver class the pattern names ("{ClassName}" at its
     * start, see customResolver()) or, without one, from the attribute tokens as before. Either way no longer
     * than $limit (see limitName()).
     *
     * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 95d86f5990), Copyright (C) Mugo Web, GPL v2
     *
     * @param string $namePattern
     * @param eZContentObject $object
     * @param int|false $version
     * @param string|false $translation
     * @param int $limit
     * @param string $sequence
     * @return string
     */
    public static function resolve( $namePattern, eZContentObject $object, $version = false, $translation = false, $limit = 0, $sequence = '' )
    {
        $resolver = self::customResolver( $namePattern );
        if ( $resolver !== null )
        {
            try
            {
                $name = $resolver->resolveNamePattern( $object, $version, $translation, $limit ?: false, $sequence );
                if ( is_scalar( $name ) )
                    return self::limitName( (string)$name, $limit, $sequence );
                eZDebug::writeError( 'The name pattern resolver ' . get_class( $resolver ) . ' returned no text; the pattern is resolved without it', __METHOD__ );
            }
            catch ( Throwable $e )
            {
                eZDebug::writeError( 'The name pattern resolver ' . get_class( $resolver ) . ' failed: ' . $e->getMessage() . '; the pattern is resolved without it', __METHOD__ );
            }
        }
        $nameResolver = new self( $namePattern, $object, $version, $translation );
        return $nameResolver->resolveNamePattern( $limit, $sequence );
    }

    /**
     * The resolver class $namePattern names, or null. A pattern names one when it starts with a PHP class name in
     * braces ("{myNameResolver}", optionally followed by more pattern text, which the class gets in its
     * constructor). The class is used only when it is listed in content.ini [NamePatternSettings]
     * ResolverClasses[], exists and implements expNamePatternResolverInterface; any other name is logged and
     * null returned. No class is loaded or made from a name that is not listed.
     *
     * @param string $namePattern
     * @return expNamePatternResolverInterface|null
     */
    public static function customResolver( $namePattern )
    {
        if ( !is_string( $namePattern ) || !preg_match( '/^\{([A-Za-z_][A-Za-z0-9_]*)\}/', $namePattern, $matches ) )
            return null;
        $className = $matches[1];

        $ini = eZINI::instance( 'content.ini' );
        $allowed = $ini->hasVariable( 'NamePatternSettings', 'ResolverClasses' ) ? $ini->variable( 'NamePatternSettings', 'ResolverClasses' ) : array();
        if ( !is_array( $allowed ) || !in_array( $className, $allowed, true ) )
        {
            eZDebug::writeWarning( "The name pattern names the resolver class '$className', which is not listed in content.ini [NamePatternSettings] ResolverClasses[]; it is not used", __METHOD__ );
            return null;
        }
        if ( !class_exists( $className ) )
        {
            eZDebug::writeError( "The name pattern resolver class '$className' does not exist; it is not used", __METHOD__ );
            return null;
        }
        $reflection = new ReflectionClass( $className );
        if ( !$reflection->implementsInterface( 'expNamePatternResolverInterface' ) || !$reflection->isInstantiable() )
        {
            eZDebug::writeError( "The name pattern resolver class '$className' does not implement expNamePatternResolverInterface; it is not used", __METHOD__ );
            return null;
        }
        return new $className( (string)substr( $namePattern, strlen( $matches[0] ) ) );
    }

    /**
     * Fetches the list of available class-identifiers in the token, and it
     * will only fetch the attributes which appear amongst the identifiers
     * found in tokens.
     *
     * @return void
     */
    private function fetchContentAttributes()
    {
        $returnAttributeArray = array();

        $identifierArray = $this->getIdentifiers( $this->origNamePattern );

        $attributes = $this->contentObject->fetchAttributesByIdentifier( $identifierArray, $this->version, array( $this->translation ) );

        if ( is_array( $attributes ) )
        {
            foreach ( $attributes as $attribute )
            {
                $identifier = $attribute->contentClassAttributeIdentifier();
                $returnAttributeArray[$identifier] = $attribute->title();
            }
        }
        else
        {
            $returnAttributeArray = array();
        }
        $this->attributeArray = $returnAttributeArray;
    }


    /**
     * Replaces tokens in the name pattern with their resolved values.
     *
     * @return string
     */
    private function translatePattern()
    {
        $tokenArray = $this->extractTokens( $this->namePattern );
        $objectName = $this->namePattern;

        foreach( $tokenArray as $token )
        {
            $string = $this->resolveToken( $token );
            $objectName = str_replace( $token, $string, $objectName );
        }

        return $objectName;
    }

    /**
     * Extract all tokens from $namePattern
     *
     * Example:
     * <code>
     * Text &lt;token&gt; more text ==&gt; &lt;token&gt;
     * </code>
     *
     * @param string $namePattern
     * @return array
     */
    private function extractTokens( $namePattern )
    {
        $foundTokens = preg_match_all( "|<([^>]+)>|U", $namePattern,
                                                       $tokenArray );

        if ( $foundTokens === false || empty( $tokenArray[0] ) )
            return [];

        return $tokenArray[0];
    }

    /**
     * Looks up the value $token should be replaced with and returns this as
     * a string. Meta strings denothing token groups are automatically
     * inferred.
     *
     * @param string $token
     * @return string
     */
    private function resolveToken( $token )
    {
        $replaceString = "";
        $tokenParts = $this->tokenParts( $token );

        foreach ( $tokenParts as $tokenPart )
        {
            if ( $this->isTokenGroup( $tokenPart ) )
            {
                $groupTokenArray = $this->extractTokens( $this->groupLookupTable[$tokenPart] );
                $replaceString = $this->groupLookupTable[$tokenPart];
                $groupHasValue = count( $groupTokenArray ) == 0;

                foreach ( $groupTokenArray as $groupToken )
                {
                    $value = $this->resolveToken( $groupToken );
                    if ( $value !== '' )
                        $groupHasValue = true;
                    $replaceString = str_replace( $groupToken, $value, $replaceString );
                }
                // We want to stop after the first matching token part / identifier is found
                // <id1|id2> if id1 has a value, id2 will not be used.
                // In this case id1 or id1 is a token group; a group none of whose tokens has a value
                // (only its separators would be left) does not count, so <(<first> <last>)|login> gives the login.
                if ( $groupHasValue )
                    break;
                $replaceString = '';
            }
            else
            {
                if ( isset( $this->attributeArray[$tokenPart] ) && $this->attributeArray[$tokenPart] !== '' && $this->attributeArray[$tokenPart] !== false )
                {
                    $replaceString = $this->attributeArray[$tokenPart];
                    // We want to stop after the first matching token part / identifier is found
                    // <id1|id2> if id1 has a value, id2 will not be used.
                    break;
                }
            }
        }
        return $replaceString;
    }


    /**
     * Checks whether $identifier is a placeholder for a token group.
     *
     * @param string $identifier
     * @return void
     */
    private function isTokenGroup( $identifier )
    {
        if ( strpos( $identifier, $this->metaString ) === false )
        {
            return false;
        }
        return true;
    }

    /**
     * Return the different constituents of $token in an array.
     * The normal case here is that the different identifiers within one token
     * will be tokenized and returned.
     *
     * Example:
     * <code>
     * "&lt;title|text&gt;" ==&gt; array( 'title', 'text' )
     * </code>
     *
     * @param string $token
     * @return array
     */
    private function tokenParts( $token )
    {
        $tokenParts = preg_split( '#\W#', $token, -1, PREG_SPLIT_NO_EMPTY );
        return $tokenParts;
    }

    /**
     * Builds a lookup / translation table for groups in the $namePattern.
     * The groups are referenced with a generated meta-token in the original
     * name pattern.
     *
     * Returns intermediate name pattern where groups are replaced with meta-
     * tokens.
     *
     * @param string $namePattern
     * @return string
     */
    private function filterNamePattern( $namePattern )
    {
        $retNamePattern = "";
        $foundGroups = preg_match_all( "/[<|\|](\(.+\))[\||>]/U", $namePattern, $groupArray );

        if ( $foundGroups )
        {
            $i = 0;
            // Each group is replaced in the pattern the previous groups were already replaced in: starting from the
            // original pattern every time kept only the last group's replacement
            $retNamePattern = $namePattern;
            foreach ( $groupArray[1] as $group )
            {
                // Create meta-token for group
                $metaToken = $this->metaString . $i;

                // Insert the group with its placeholder token
                $retNamePattern = str_replace( $group, $metaToken, $retNamePattern );

                // Remove the pattern "(" ")" from the tokens
                $group = str_replace( array( '(', ')' ), '', $group );

                $this->groupLookupTable[$metaToken] = $group;
                ++$i;
            }
            return $retNamePattern;
        }
        return $namePattern;

    }

    /**
     * Returns all identifiers from all tokens in the name pattern.
     *
     * @param string $patternString
     * @return array
     */
    private function getIdentifiers( $patternString )
    {
        // Everything between the outermost < and > at any depth: a group such as <(<first> <last>)|login> nests
        // tokens, and matching the shortest <...> stopped at the first inner > and never saw "login"
        $insideTokens = '';
        $depth = 0;
        $length = strlen( (string)$patternString );
        for ( $i = 0; $i < $length; $i++ )
        {
            $char = $patternString[$i];
            if ( $char === '<' )
            {
                $depth++;
                $insideTokens .= ' ';
            }
            else if ( $char === '>' )
            {
                $depth = max( 0, $depth - 1 );
                $insideTokens .= ' ';
            }
            else if ( $depth > 0 )
            {
                $insideTokens .= $char;
            }
        }

        $retArray = preg_split( '#\W#', $insideTokens, -1, PREG_SPLIT_NO_EMPTY );
        return is_array( $retArray ) ? array_values( array_unique( $retArray ) ) : array();
    }
}
?>
