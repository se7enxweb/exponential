<?php
/**
 * expViewParameters: the options of an administration view as view parameters, /(name)/value, never GET query
 * parameters, and the one reversible encoding of their values (encode(), decode(): "~2F" for "/" ...). DB-less.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expViewParametersTest extends PHPUnit\Framework\TestCase
{
    public function testGetReadsTheDeclaredVariableThenTheUserParameter()
    {
        $params = array( 'Query' => 'editor', 'UserParameters' => array( 'q' => 'other', 'kind' => 'redirect' ) );
        $this->assertSame( 'editor', expViewParameters::get( $params, 'q', 'Query' ) );
        $this->assertSame( 'redirect', expViewParameters::get( $params, 'kind' ) );
        $this->assertSame( 'other', expViewParameters::get( array( 'Query' => false, 'UserParameters' => array( 'q' => 'other' ) ), 'q', 'Query' ) );
        $this->assertFalse( expViewParameters::get( array(), 'q', 'Query' ) );
        $this->assertFalse( expViewParameters::get( array( 'UserParameters' => array( 'q' => array( 'x' ) ) ), 'q' ) );
        $this->assertFalse( expViewParameters::raw( array(), 'q', 'Query' ) );
    }

    public function testGetDecodesRawDoesNot()
    {
        $params = array( 'Query' => 'a~2Fb', 'UserParameters' => array( 'filter' => 'x~28y~29' ) );
        $this->assertSame( 'a/b', expViewParameters::get( $params, 'q', 'Query' ) );
        $this->assertSame( 'a~2Fb', expViewParameters::raw( $params, 'q', 'Query' ) );
        $this->assertSame( 'x(y)', expViewParameters::get( $params, 'filter' ) );
        $this->assertSame( 'x(y)', expViewParameters::text( $params, 'filter' ) );
    }

    public function testGetNeverReadsTheQueryString()
    {
        $saved = $_GET;
        $_GET = array( 'q' => 'from-get' );
        try
        {
            $this->assertFalse( expViewParameters::get( array(), 'q', 'Query' ) );
            $this->assertSame( '', expViewParameters::text( array(), 'q', 'Query' ) );
        }
        finally
        {
            $_GET = $saved;
        }
    }

    public function testCleanAndSuffix()
    {
        $this->assertSame( 'a/b (c)', expViewParameters::clean( " a/b  (c)\n " ), 'slashes and brackets stay: encode() carries them' );
        $this->assertSame( 200, mb_strlen( expViewParameters::clean( str_repeat( 'x', 300 ) ) ) );
        $this->assertSame( '', expViewParameters::clean( array( 'x' ) ) );
        $this->assertSame( '/(q)/a~2Fb/(order)/1/(all)/1',
                           expViewParameters::suffix( array( 'q' => 'a/b', 'sort' => '', 'user' => false, 'order' => 1, 'all' => true, 'x' => null ) ) );
        $this->assertSame( '/(q)/%3Cx%3E~2B', expViewParameters::suffix( array( 'q' => '<x>+' ) ), 'a + is not turned into a space by the second decoding' );
        $this->assertStringNotContainsString( '?', expViewParameters::suffix( array( 'q' => 'a?b=c' ) ) );
        $this->assertSame( '/(q)/example.com~2Fa', expViewParameters::suffix( array( 'q' => 'example.com/a' ) ), 'a URL keeps its slash' );
    }

    public function testEncodeEscapesWhatTheAddressCannotCarry()
    {
        $this->assertSame( 'editor', expViewParameters::encode( 'editor' ) );
        $this->assertSame( 'error,warning:x@y=1!*', expViewParameters::encode( 'error,warning:x@y=1!*' ), 'readable characters stay' );
        $this->assertSame( 'a_b.c-d', expViewParameters::encode( 'a_b.c-d' ) );
        $this->assertSame( 'a%20b', expViewParameters::encode( 'a b' ) );
        $this->assertSame( '~2Fadmin~2Fcontent', expViewParameters::encode( '/admin/content' ) );
        $this->assertSame( '~28x~29', expViewParameters::encode( '(x)' ) );
        $this->assertSame( '~7E', expViewParameters::encode( '~' ) );
        $this->assertSame( '100~25', expViewParameters::encode( '100%' ) );
        $this->assertSame( 'a~2Bb', expViewParameters::encode( 'a+b' ) );
        $this->assertSame( '~3F%26=~25~23~5C', expViewParameters::encode( '?&=%#\\' ) );
        $this->assertSame( 'a~09b', expViewParameters::encode( "a\tb" ) );
        $this->assertSame( 'M%C3%BCller', expViewParameters::encode( 'Müller' ) );
        $this->assertSame( '~2E', expViewParameters::encode( '.' ), 'a dot segment would be resolved by the browser' );
        $this->assertSame( '~2E~2E', expViewParameters::encode( '..' ) );
        $this->assertSame( '...', expViewParameters::encode( '...' ) );
        $this->assertSame( '42', expViewParameters::encode( 42 ) );
        $this->assertSame( '', expViewParameters::encode( array( 'x' ) ) );
        $this->assertSame( 'a~2Fb ~28c~29', expViewParameters::escape( 'a/b (c)' ), 'escape() leaves the percent-encoding to encode()' );
        foreach ( array( 'a/b (c)~', 'https://example.com/a?b=c#d', 'Müller 日本語', '100% + 50%', '..', "\x00\x7F", "'\"<>" ) as $value )
            $this->assertMatchesRegularExpression( '/^[A-Za-z0-9_.,:@=!*~%\-]*$/', expViewParameters::encode( $value ), 'only characters a path segment carries as they are' );
    }

    public function testDecodeIsTheInverseAndReadsTheFormOfBefore()
    {
        $this->assertSame( 'a/b', expViewParameters::decode( 'a~2Fb' ) );
        $this->assertSame( '/', expViewParameters::decode( '~2f' ), 'lower case hex digits are read too' );
        $this->assertSame( 'a~zz', expViewParameters::decode( 'a~zz' ), 'a ~ without hex digits stays' );
        $this->assertSame( 'a~', expViewParameters::decode( 'a~' ) );
        $this->assertSame( 'foo bar', expViewParameters::decode( 'foo~20bar' ), 'the log viewer and templatelist links of before (every character ~XX)' );
        $this->assertSame( 'Müller', expViewParameters::decode( 'M~C3~BCller' ) );
        $this->assertSame( '', expViewParameters::decode( array() ) );
    }

    /**
     * Every value comes back unchanged through the address: encode(), the kernel's decoding (twice as shipped, once
     * with site.ini [URLTranslator] DecodeOnce), get() of the view parameter.
     */
    public function testRoundTripThroughTheKernel()
    {
        $values = array( 'example.com/a', '/var/site/storage/images/a/b (1).jpg', 'https://www.example.com/News?x=1&y=2#top',
                         '(q)', 'a)(b', '~', '~2F', '~7E~', '~zz', 'with space', '  two  spaces  ', 'a+b', '100%', '%2F', '%252F',
                         'Müller', "\xC3\xA4rger/%2F", '日本語 テスト', "emoji \xF0\x9F\x98\x80", '.', '..', '...', './..',
                         'back\\slash', "tab\there", "line\nbreak", 'quote " and \' too', '<b>&amp;</b>', 'a,b:c@d=e!f*g',
                         'x;y$z', "\x00", '' );
        foreach ( $values as $value )
        {
            $encoded = expViewParameters::encode( $value );
            $this->assertStringNotContainsString( '/', $encoded, $value );
            $this->assertStringNotContainsString( '%2F', strtoupper( $encoded ), "Apache refuses an encoded slash: $value" );
            $once = urldecode( $encoded );
            $this->assertSame( $once, urldecode( $once ), "the second decoding changes nothing: $value" );
            $this->assertSame( expViewParameters::escape( $value ), $once, $value );
            $this->assertStringNotContainsString( '/', $once, $value );
            $this->assertSame( $value, expViewParameters::decode( $once ), $value );
            $params = array( 'Query' => false, 'UserParameters' => array( 'q' => $once ) );
            $this->assertSame( $value, expViewParameters::get( $params, 'q', 'Query' ), $value );
            $this->assertSame( $once, expViewParameters::raw( $params, 'q', 'Query' ), $value );
        }
        $this->assertSame( 'example.com/a', expViewParameters::text( array( 'UserParameters' => array( 'q' => 'example.com~2Fa' ) ), 'q' ) );
    }

    /** The kernel splits the address it decoded: no encoded value starts a view parameter or adds a segment */
    public function testTheKernelSplitsTheAddressAsWritten()
    {
        $suffix = expViewParameters::suffix( array( 'q' => 'a/(b)/c', 'scope' => 'all' ) );
        $path = urldecode( urldecode( 'settings/view/admin/site.ini' . $suffix ) );
        $this->assertSame( array( 'settings', 'view', 'admin', 'site.ini', '(q)', 'a~2F~28b~29~2Fc', '(scope)', 'all' ), explode( '/', $path ) );
    }

    public function testTheTemplateOperatorWritesTheSameEncoding()
    {
        $operator = new expViewParameterOperator();
        $this->assertSame( array( 'view_parameter' ), $operator->operatorList() );
        $this->assertSame( array(), $operator->namedParameterList() );
        $tpl = null;
        foreach ( array( 'a/b (c)', 'Müller', 42, '' ) as $value )
        {
            $out = $value;
            $operator->modify( $tpl, 'view_parameter', array(), '', '', $out, array(), false );
            $this->assertSame( expViewParameters::encode( $value ), $out );
        }
        $out = array( 'x' );
        $operator->modify( $tpl, 'view_parameter', array(), '', '', $out, array(), false );
        $this->assertSame( '', $out );
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/kernel/common/eztemplateautoload.php' );
        $this->assertMatchesRegularExpression( "/'class' => 'expViewParameterOperator',\\s*'operator_names' => array\\( 'view_parameter' \\)/", $source );
    }

    public function testRedirectsOnlyAPostOfTheForm()
    {
        $post = array( 'REQUEST_METHOD' => 'POST' );
        $get = array( 'REQUEST_METHOD' => 'GET' );
        $this->assertTrue( expViewParameters::redirectsPost( array( 'q' => '' ), array( 'q' ), $post ), 'an empty field counts' );
        $this->assertFalse( expViewParameters::redirectsPost( array( 'Other' => '1' ), array( 'q' ), $post ) );
        $this->assertFalse( expViewParameters::redirectsPost( array( 'q' => 'x' ), array( 'q' ), $get ), 'a GET is never redirected' );
        $this->assertSame( 'x', expViewParameters::posted( array( 'q' => 'x' ), 'q' ) );
        $this->assertSame( '', expViewParameters::posted( array( 'q' => array( 'x' ) ), 'q' ) );
    }
}
