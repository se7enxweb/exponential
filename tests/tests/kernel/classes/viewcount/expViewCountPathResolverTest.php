<?php
/**
 * expViewCountPathResolver finds the node a path showed: "/" is the siteaccess's IndexPage (never the content root
 * reached through a PathPrefix), /content/view/<any view mode>/<node>, URL aliases with the path prefixes first and
 * then without, PathPrefixExclude[], URL encoded UTF-8, view parameters, SitePrefix as a whole element, the
 * languages of each siteaccess, and one lookup per path.
 *
 * No database: the alias lookup is a table.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

#[\PHPUnit\Framework\Attributes\Group('kernel')]
class expViewCountPathResolverTest extends PHPUnit\Framework\TestCase
{
    /** @var array path => node, as eZURLAliasML::fetchNodeIDByPath() would answer */
    private $aliases = array(
        '' => 2,                      // what gluing "/" to nothing found: the content root
        'fit-healthy' => 60,          // the PathPrefix of a siteaccess: its own top node
        'fit-healthy/recipes' => 61,
        'fit-healthy/über-uns' => 62,
        'media/images' => 43,
        'about' => 70,                // an alias outside the PathPrefix
        'home' => 80,
    );

    /** @var string[] */
    private $asked = array();

    /** @var array */
    private $languageCalls = array();

    private function resolver( array $viewModes = array() )
    {
        return new expViewCountPathResolver( function ( $path )
        {
            $this->asked[] = $path;
            $key = mb_strtolower( trim( $path, '/' ) );
            return isset( $this->aliases[$key] ) ? $this->aliases[$key] : false;
        }, $viewModes, function ( array $locales )
        {
            $this->languageCalls[] = $locales;
        } );
    }

    private static function site( array $properties = array() )
    {
        return new expViewCountSite( $properties + array( 'name' => 'korg', 'pathPrefixes' => array( 'Fit-Healthy' ),
                                                          'pathPrefixExclude' => array( 'media' ), 'indexPage' => '/content/view/full/60' ) );
    }

    public function testTheRootIsTheIndexPageNotTheContentRoot()
    {
        $resolver = $this->resolver();
        $this->assertSame( 60, $resolver->resolve( self::site(), expViewCountPathResolver::elements( '/' ) ) );
        $this->assertSame( 60, $resolver->resolve( self::site(), expViewCountPathResolver::elements( '/index.php' ) ) );
        $this->assertSame( 89, $resolver->resolve( self::site( array( 'indexPage' => 'content/view/full/89/' ) ), array() ) );
        $this->assertSame( array(), $this->asked, 'no alias lookup for "/"' );
    }

    public function testAnIndexPageThatIsAnAliasOrAModuleView()
    {
        $resolver = $this->resolver();
        $this->assertSame( 80, $resolver->resolve( self::site( array( 'indexPage' => '/Home', 'pathPrefixes' => array() ) ), array() ) );
        $this->assertFalse( $resolver->resolve( self::site( array( 'indexPage' => 'user/login', 'pathPrefixes' => array() ) ), array() ) );
        $this->assertFalse( $resolver->resolve( self::site( array( 'indexPage' => '' ) ), array() ) );
        $this->assertSame( 7, $resolver->resolve( self::site( array( 'indexNodeID' => 7 ) ), array() ) );
    }

    public function testContentViewOfEveryViewMode()
    {
        $resolver = $this->resolver();
        $this->assertSame( 42, $resolver->resolve( self::site(), expViewCountPathResolver::elements( '/content/view/full/42/(offset)/10' ) ) );
        $this->assertSame( 42, $resolver->resolve( self::site(), expViewCountPathResolver::elements( '/content/view/line/42' ) ) );
        $this->assertFalse( $resolver->resolve( self::site(), expViewCountPathResolver::elements( '/content/view/full/abc' ) ) );
        $limited = $this->resolver( array( 'full' ) );
        $this->assertFalse( $limited->resolve( self::site(), expViewCountPathResolver::elements( '/content/view/line/42' ) ) );
        $this->assertSame( array(), $this->asked );
    }

    public function testThePathPrefixFirstThenThePathAsItIs()
    {
        $resolver = $this->resolver();
        $this->assertSame( 61, $resolver->resolve( self::site(), array( 'Recipes' ) ) );
        // not below the prefix: the plain alias (the earlier cronjob lost these hits)
        $this->assertSame( 70, $resolver->resolve( self::site(), array( 'about' ) ) );
        $this->assertSame( array( 'Fit-Healthy/Recipes', 'Fit-Healthy/about', 'about' ), $this->asked );
    }

    public function testPathPrefixExcludeSkipsThePrefix()
    {
        $resolver = $this->resolver();
        $this->assertSame( 43, $resolver->resolve( self::site(), array( 'Media', 'Images' ) ) );
        $this->assertSame( array( 'Media/Images' ), $this->asked );
    }

    public function testUrlEncodedUtf8AndViewParameters()
    {
        $elements = expViewCountPathResolver::elements( '/%C3%BCber-uns/(language)/ger-DE/(offset)/2' );
        $this->assertSame( array( 'über-uns' ), $elements );
        $this->assertSame( 62, $this->resolver()->resolve( self::site(), $elements ) );
        $this->assertSame( array( 'a', 'b' ), expViewCountPathResolver::elements( '//a///b/' ) );
    }

    public function testTheSitePrefixIsAWholeElement()
    {
        $this->assertSame( array( 'news' ), expViewCountPathResolver::stripPrefix( array( 'mysite', 'news' ), array( 'mysite' ) ) );
        $this->assertSame( array( 'mysite2', 'news' ), expViewCountPathResolver::stripPrefix( array( 'mysite2', 'news' ), array( 'mysite' ) ) );
        $this->assertSame( array(), expViewCountPathResolver::stripPrefix( array( 'mysite' ), array( '/mysite/' ) ) );
        $this->assertSame( array( 'x' ), expViewCountPathResolver::stripPrefix( array( 'a', 'b', 'x' ), array( '', 'a/b' ) ) );
    }

    public function testEachPathIsLookedUpOncePerLanguageList()
    {
        $resolver = $this->resolver();
        $english = self::site( array( 'pathPrefixes' => array(), 'languages' => array( 'eng-GB' ) ) );
        $german = self::site( array( 'name' => 'ger', 'pathPrefixes' => array(), 'languages' => array( 'ger-DE', 'eng-GB' ) ) );
        $resolver->resolve( $english, array( 'about' ) );
        $resolver->resolve( $english, array( 'about' ) );
        $resolver->resolve( $german, array( 'about' ) );
        $resolver->resolve( $german, array( 'about' ) );
        $this->assertSame( 2, $resolver->lookups() );
        $this->assertSame( array( array( 'eng-GB' ), array(), array( 'ger-DE', 'eng-GB' ) ), $this->languageCalls );
        $resolver->restoreLanguages();
        $this->assertSame( array(), end( $this->languageCalls ) );
    }
}
