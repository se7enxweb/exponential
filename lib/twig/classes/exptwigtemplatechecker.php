<?php
/**
 * exp:template:check for Twig templates (.twig, .html.twig): tokenized and parsed by Twig 3 with the bridge, its tags
 * and node visitors (syntax); compiled in memory and the compiled PHP linted (compile); at strict level the sandbox
 * policy applied as the site applies it, every exp_<name> that names no operator reported, and every design: include
 * resolved; Twig's deprecations while compiling reported with --deprecations.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigTemplateChecker implements expTemplateEngineChecker
{
    public function available()
    {
        return expTwigRenderer::loadLibrary();
    }

    public function check( $file, $text, $level, array $context )
    {
        $findings = array();
        $twig = expTwigRenderer::checkEnvironment();
        $name = $file === '-' ? 'stdin.twig' : $file;
        $twig->getLoader()->setTemplate( $name, $text );
        $deprecations = array();
        set_error_handler( function ( $no, $message ) use ( &$deprecations ) {
            $deprecations[] = $message;
            return true;
        }, E_USER_DEPRECATED | E_DEPRECATED );
        try
        {
            $module = $twig->parse( $twig->tokenize( new \Twig\Source( $text, $name ) ) );
            if ( $level !== 'syntax' )
            {
                $code = $twig->compile( $module );
                if ( ( $error = expTemplateChecker::lintPhp( $code ) ) !== null )
                    $findings[] = expTplTemplateChecker::finding( 'TWIG-COMPILE-001', 'ERROR', 0, 0, 'the compiled template is not valid PHP: ' . $error );
            }
        }
        catch ( \Twig\Error\SyntaxError $e )
        {
            $hint = null;
            if ( preg_match( '/Unknown "([^"]+)" (filter|function|test)/', $e->getRawMessage(), $m ) )
                $hint = self::suggestion( $m[1] );
            return array( expTplTemplateChecker::finding( 'TWIG-SYNTAX-001', 'ERROR', $e->getTemplateLine(), 0, $e->getRawMessage(), $hint ) );
        }
        catch ( \Twig\Error\Error $e )
        {
            return array( expTplTemplateChecker::finding( 'TWIG-COMPILE-001', 'ERROR', $e->getTemplateLine(), 0, $e->getRawMessage() ) );
        }
        finally
        {
            restore_error_handler();
        }
        if ( !empty( $context['deprecations'] ) )
        {
            foreach ( array_unique( $deprecations ) as $message )
                $findings[] = expTplTemplateChecker::finding( 'TWIG-DEPRECATION-001', 'WARN', 0, 0, $message );
        }

        $used = array( 'tag' => array(), 'filter' => array(), 'function' => array(), 'include' => array() );
        $this->collect( $module, $used );
        if ( $level === 'strict' || !empty( $context['security'] ) )
        {
            $policy = expTwigSecurity::fromSettings( eZINI::instance( 'twig.ini' ) );
            foreach ( array( 'tag', 'filter', 'function' ) as $kind )
            {
                foreach ( $used[$kind] as $usedName => $line )
                {
                    try
                    {
                        $policy->checkSecurity( $kind === 'tag' ? array( $usedName ) : array(), $kind === 'filter' ? array( $usedName ) : array(),
                                                $kind === 'function' ? array( $usedName ) : array() );
                    }
                    catch ( \Twig\Sandbox\SecurityError $e )
                    {
                        $findings[] = expTplTemplateChecker::finding( 'TWIG-SECURITY-001', 'ERROR', $line, 0, $e->getRawMessage() . ' (the sandbox would refuse the template)' );
                    }
                }
            }
        }
        if ( $level === 'strict' )
        {
            foreach ( array( 'filter', 'function' ) as $kind )
            {
                foreach ( $used[$kind] as $usedName => $line )
                {
                    if ( expTwigExtension::isUnknownOperatorName( $usedName ) && expTwigExtension::operatorFor( $usedName ) === null )
                        $findings[] = expTplTemplateChecker::finding( 'TWIG-STRICT-UNKNOWN-OPERATOR', 'WARN', $line, 0,
                                                                      "$usedName names no template operator: it passes its input through", self::suggestion( substr( $usedName, 4 ) ) );
                }
            }
        }
        if ( !empty( $context['includes'] ) && isset( $context['resolve'] ) )
        {
            foreach ( $used['include'] as $uri => $line )
            {
                if ( !call_user_func( $context['resolve'], $uri ) )
                    $findings[] = expTplTemplateChecker::finding( 'TWIG-STRICT-INCLUDE-MISSING', 'ERROR', $line, 0, "the included template $uri is not found in the design chain" );
            }
        }
        return $findings;
    }

    /**
     * The tags, filters, functions (name => first line) and the constant design: includes of a syntax tree.
     */
    private function collect( \Twig\Node\Node $node, array &$used )
    {
        if ( $node->getNodeTag() !== null && !isset( $used['tag'][$node->getNodeTag()] ) )
            $used['tag'][$node->getNodeTag()] = $node->getTemplateLine();
        if ( $node instanceof \Twig\Node\Expression\FilterExpression && !isset( $used['filter'][$node->getAttribute( 'name' )] ) )
            $used['filter'][$node->getAttribute( 'name' )] = $node->getTemplateLine();
        if ( $node instanceof \Twig\Node\Expression\FunctionExpression && !isset( $used['function'][$node->getAttribute( 'name' )] ) )
            $used['function'][$node->getAttribute( 'name' )] = $node->getTemplateLine();
        if ( $node instanceof expTwigTagNode && $node->getAttribute( 'kind' ) === 'include' && $node->getNode( 'params' )->hasNode( 'uri' ) )
        {
            $uri = $node->getNode( 'params' )->getNode( 'uri' );
            if ( $uri instanceof \Twig\Node\Expression\ConstantExpression && strncmp( (string)$uri->getAttribute( 'value' ), 'design:', 7 ) === 0 )
                $used['include'][(string)$uri->getAttribute( 'value' )] = $node->getTemplateLine();
        }
        foreach ( $node as $child )
            $this->collect( $child, $used );
    }

    /**
     * "did you mean ..." for an unknown name: the closest operator name.
     */
    private static function suggestion( $name )
    {
        return expTemplateChecker::operatorSuggestion( preg_replace( '/^exp_/', '', $name ) );
    }
}
