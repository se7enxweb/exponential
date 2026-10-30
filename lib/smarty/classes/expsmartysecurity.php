<?php
/**
 * The Smarty security policy of .stpl templates, smarty.ini [SmartySettings] Security=enabled.
 *
 * Smarty's own tags and modifiers are checked against the Security*List settings. The tags and operators of the
 * template system (expSmartyExtension: {attribute_view_gui}, {expinclude}, {$text|wash}, fetch(...)) are what a
 * .tpl may use as well and stay allowed, unless a disabled list names them. Further: no $smarty special variable
 * of SecurityDisabledSpecialVariableList, no PHP constants (SecurityConstants), no static class but those of
 * SecurityStaticClassList, no stream but those of SecurityStreamList, templates only from the template directories
 * of the design bases and SecurityTrustedDirList, and with SecurityObjectAccess=disabled no -> in a tag
 * (objectAccessFilter()). Everything is checked when a template is compiled; a template the policy refuses does
 * not compile, and the renderer shows its failure.
 *
 * See doc/eng-us/guides/smarty-templates-security.md.
 *
 * @package lib
 */
class expSmartySecurity extends \Smarty\Security
{
    /** @var array name => bool: whether a Smarty extension other than expSmartyExtension has the tag */
    private $nativeTags = array();

    /** @var array name => bool: the same for a modifier */
    private $nativeModifiers = array();

    /** @var \Smarty\Smarty the instance the policy belongs to (Smarty's Security keeps it in an undeclared property) */
    private $owner;

    /**
     * @param \Smarty\Smarty $smarty
     */
    public function __construct( \Smarty\Smarty $smarty )
    {
        parent::__construct( $smarty );
        $this->owner = $smarty;
    }

    /**
     * The policy of smarty.ini [SmartySettings] for $smarty.
     *
     * @param \Smarty\Smarty $smarty
     * @param eZINI $ini smarty.ini
     * @return expSmartySecurity
     */
    public static function fromSettings( \Smarty\Smarty $smarty, eZINI $ini )
    {
        $policy = new self( $smarty );
        $policy->allowed_tags = self::lowerList( $ini, 'SecurityAllowedTagList' );
        $policy->disabled_tags = self::lowerList( $ini, 'SecurityDisabledTagList' );
        $policy->allowed_modifiers = self::lowerList( $ini, 'SecurityAllowedModifierList' );
        $policy->disabled_modifiers = self::lowerList( $ini, 'SecurityDisabledModifierList' );
        $policy->disabled_special_smarty_vars = self::lowerList( $ini, 'SecurityDisabledSpecialVariableList' );
        $policy->allow_constants = self::setting( $ini, 'SecurityConstants' ) === 'enabled';
        $policy->allow_super_globals = !in_array( 'get', $policy->disabled_special_smarty_vars, true ) &&
                                       !in_array( 'server', $policy->disabled_special_smarty_vars, true );
        // Smarty: an empty list allows every class and stream, null none
        $classes = self::values( $ini, 'SecurityStaticClassList' );
        $policy->static_classes = $classes ? $classes : null;
        $policy->trusted_static_methods = $classes ? array() : null;
        $policy->trusted_static_properties = $classes ? array() : null;
        $streams = self::lowerList( $ini, 'SecurityStreamList' );
        $policy->streams = $streams ? $streams : null;
        $dirs = array();
        foreach ( self::values( $ini, 'SecurityTrustedDirList' ) as $dir )
            $dirs[] = $dir[0] === '/' ? $dir : eZSys::rootDir() . '/' . $dir;
        $policy->secure_dir = $dirs;
        return $policy;
    }

    /**
     * Whether a template compiled under the policy may use -> (smarty.ini SecurityObjectAccess).
     *
     * @param eZINI $ini
     * @return bool
     */
    public static function allowsObjectAccess( eZINI $ini )
    {
        return self::setting( $ini, 'SecurityObjectAccess' ) === 'enabled';
    }

    private static function setting( eZINI $ini, $name )
    {
        return $ini->hasVariable( 'SmartySettings', $name ) ? trim( (string)$ini->variable( 'SmartySettings', $name ) ) : '';
    }

    private static function values( eZINI $ini, $name )
    {
        $list = $ini->hasVariable( 'SmartySettings', $name ) ? $ini->variable( 'SmartySettings', $name ) : array();
        if ( !is_array( $list ) )
            return array();
        return array_values( array_filter( array_map( 'trim', $list ), 'strlen' ) );
    }

    private static function lowerList( eZINI $ini, $name )
    {
        return array_map( 'strtolower', self::values( $ini, $name ) );
    }

    /**
     * A tag: Smarty's own ones by the allowed and disabled lists; the template system's ones and the functions a
     * template defines with {function} unless disabled. A closing tag ({/if}: ifclose) follows its opening tag.
     */
    public function isTrustedTag( $tag_name, $compiler )
    {
        $name = strtolower( (string)$tag_name );
        if ( in_array( $name, array( 'assign', 'call' ), true ) )
            return true;
        $base = strlen( $name ) > 5 && substr( $name, -5 ) === 'close' ? substr( $name, 0, -5 ) : $name;
        if ( in_array( $name, $this->disabled_tags, true ) || in_array( $base, $this->disabled_tags, true ) )
        {
            $compiler->trigger_template_error( "tag '{$name}' disabled by security setting", null, true );
            return false;
        }
        if ( !$this->isNativeTag( $name ) && !$this->isNativeTag( $base ) )
            return true;
        if ( empty( $this->allowed_tags ) || in_array( $name, $this->allowed_tags, true ) || in_array( $base, $this->allowed_tags, true ) )
            return true;
        $compiler->trigger_template_error( "tag '{$name}' not allowed by security setting", null, true );
        return false;
    }

    /**
     * A modifier: Smarty's own ones by the allowed and disabled lists, the operators of the template system unless
     * disabled.
     */
    public function isTrustedModifier( $modifier_name, $compiler )
    {
        $name = strtolower( (string)$modifier_name );
        if ( $name === 'default' )
            return true;
        if ( in_array( $name, $this->disabled_modifiers, true ) )
        {
            $compiler->trigger_template_error( "modifier '{$name}' disabled by security setting", null, true );
            return false;
        }
        if ( !$this->isNativeModifier( $name ) || empty( $this->allowed_modifiers ) || in_array( $name, $this->allowed_modifiers, true ) )
            return true;
        $compiler->trigger_template_error( "modifier '{$name}' not allowed by security setting", null, true );
        return false;
    }

    /** Whether one of Smarty's own extensions (not the template system's) compiles or handles the tag */
    private function isNativeTag( $name )
    {
        if ( !isset( $this->nativeTags[$name] ) )
        {
            $native = false;
            foreach ( $this->owner->getExtensions() as $extension )
            {
                if ( $extension instanceof expSmartyExtension )
                    continue;
                if ( $extension->getTagCompiler( $name ) || $extension->getFunctionHandler( $name ) || $extension->getBlockHandler( $name ) )
                {
                    $native = true;
                    break;
                }
            }
            $this->nativeTags[$name] = $native;
        }
        return $this->nativeTags[$name];
    }

    /** Whether one of Smarty's own extensions has the modifier */
    private function isNativeModifier( $name )
    {
        if ( !isset( $this->nativeModifiers[$name] ) )
        {
            $native = false;
            foreach ( $this->owner->getExtensions() as $extension )
            {
                if ( $extension instanceof expSmartyExtension )
                    continue;
                if ( $extension->getModifierCompiler( $name ) || $extension->getModifierCallback( $name ) )
                {
                    $native = true;
                    break;
                }
            }
            $this->nativeModifiers[$name] = $native;
        }
        return $this->nativeModifiers[$name];
    }

    /**
     * Smarty pre-filter of SecurityObjectAccess=disabled: refuses a template with -> in a tag ($x->method(),
     * $x->property, also in a double-quoted string, which Smarty evaluates). Comments ({* *}), {literal} blocks
     * and text outside the tags (a { followed by white space is text, as Smarty's auto_literal reads it) are
     * not looked at.
     *
     * @param string $source
     * @return string the source, unchanged
     * @throws \Smarty\CompilerException
     */
    public static function objectAccessFilter( $source )
    {
        $source = (string)$source;
        $length = strlen( $source );
        $offset = 0;
        while ( ( $open = strpos( $source, '{', $offset ) ) !== false )
        {
            $next = $open + 1 < $length ? $source[$open + 1] : '';
            if ( $next === '' || ctype_space( $next ) )
            {
                $offset = $open + 1;
                continue;
            }
            if ( $next === '*' )
            {
                $end = strpos( $source, '*}', $open + 2 );
                if ( $end === false )
                    break;
                $offset = $end + 2;
                continue;
            }
            if ( strncasecmp( substr( $source, $open, 9 ), '{literal}', 9 ) === 0 )
            {
                $end = stripos( $source, '{/literal}', $open + 9 );
                if ( $end === false )
                    break;
                $offset = $end + 10;
                continue;
            }
            $depth = 0;
            $quote = null;
            for ( $i = $open + 1; $i < $length; ++$i )
            {
                $c = $source[$i];
                if ( $quote === "'" )
                {
                    if ( $c === '\\' )
                        ++$i;
                    else if ( $c === "'" )
                        $quote = null;
                    continue;
                }
                if ( $c === '-' && $i + 1 < $length && $source[$i + 1] === '>' )
                {
                    $line = substr_count( $source, "\n", 0, $open ) + 1;
                    throw new \Smarty\CompilerException( "object access (->) not allowed by security setting, in the tag on line $line" );
                }
                if ( $quote === '"' )
                {
                    if ( $c === '\\' )
                        ++$i;
                    else if ( $c === '"' )
                        $quote = null;
                    continue;
                }
                if ( $c === '"' || $c === "'" )
                    $quote = $c;
                else if ( $c === '{' )
                    ++$depth;
                else if ( $c === '}' )
                {
                    if ( $depth === 0 )
                        break;
                    --$depth;
                }
            }
            $offset = $i + 1;
        }
        return $source;
    }
}
