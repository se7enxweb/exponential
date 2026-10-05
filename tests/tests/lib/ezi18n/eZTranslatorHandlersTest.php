<?php
/**
 * The translator chain of lib/ezi18n, without translation files:
 *   - eZTranslatorManager: createKey/createMessage, the shared instance and resetGlobals(), registerHandler(), the
 *     handler order in translate/findMessage/findKey/keyTranslate (first non-null wins, key-based handlers only for
 *     keys), the "default" context for an empty one, dynamic translations on and off
 *   - eZTranslatorHandler: the base answers (no key, no message, translate() and keyTranslate() from them)
 *   - eZTranslatorGroup and eZRandomTranslator: handler count, picks within range, delegation to the picked handler
 *   - eZShuffleTranslator: the shuffled text keeps every character, short texts, findMessage() and translate()
 *
 * No kernel bootstrap, no database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezi18n
 */

/**
 * A handler answering from a fixed table of source => translation, optionally key based.
 */
class eZTranslatorHandlersTestTable extends eZTranslatorHandler
{
    public $Table;
    public $Calls = 0;

    public function __construct( $table, $keyBased = false )
    {
        parent::__construct( $keyBased );
        $this->Table = $table;
    }

    function findMessage( $context, $source, $comment = null )
    {
        ++$this->Calls;
        if ( !isset( $this->Table[$source] ) )
            return null;
        return eZTranslatorManager::createMessage( $context, $source, $comment, $this->Table[$source] );
    }

    function translate( $context, $source, $comment = null )
    {
        $msg = $this->findMessage( $context, $source, $comment );
        return $msg === null ? null : $msg['translation'];
    }

    function keyTranslate( $key )
    {
        $msg = $this->findKey( $key );
        return $msg === null ? null : $msg['translation'];
    }

    function findKey( $key )
    {
        ++$this->Calls;
        foreach ( $this->Table as $source => $translation )
        {
            if ( eZTranslatorManager::createKey( 'ctx', $source ) === $key )
                return eZTranslatorManager::createMessage( 'ctx', $source, null, $translation );
        }
        return null;
    }
}

/**
 * A group that always picks the handler number it was given.
 */
class eZTranslatorHandlersTestFixedGroup extends eZTranslatorGroup
{
    public $Pick;

    function keyPick( $key )
    {
        return $this->Pick;
    }

    function pick( $context, $source, $comment )
    {
        return $this->Pick;
    }
}

class eZTranslatorHandlersTest extends PHPUnit\Framework\TestCase
{
    private $savedManager;
    private $savedDynamic;

    protected function setUp(): void
    {
        $this->savedManager = $GLOBALS['eZTranslatorManagerInstance'] ?? null;
        $this->savedDynamic = array_key_exists( eZTranslatorManager::DYNAMIC_TRANSLATIONS_ENABLED, $GLOBALS );
        eZTranslatorManager::resetGlobals();
    }

    protected function tearDown(): void
    {
        eZTranslatorManager::resetGlobals();
        if ( $this->savedManager !== null )
            $GLOBALS['eZTranslatorManagerInstance'] = $this->savedManager;
        eZTranslatorManager::enableDynamicTranslations( $this->savedDynamic );
    }

    public function testCreateKeyIsTheMd5OfContextSourceAndComment()
    {
        $this->assertSame( md5( "c\ns\n" ), eZTranslatorManager::createKey( 'c', 's' ) );
        $this->assertSame( md5( "c\ns\nx" ), eZTranslatorManager::createKey( 'c', 's', 'x' ) );
        $this->assertSame( eZTranslatorManager::createKey( 'c', 's', null ), eZTranslatorManager::createKey( 'c', 's', '' ) );
        $this->assertNotSame( eZTranslatorManager::createKey( 'c', 's' ), eZTranslatorManager::createKey( 'd', 's' ) );
    }

    public function testCreateMessage()
    {
        $this->assertSame(
            array( 'context' => 'c', 'source' => 's', 'comment' => null, 'translation' => 't' ),
            eZTranslatorManager::createMessage( 'c', 's', null, 't' )
        );
    }

    public function testInstanceIsSharedUntilReset()
    {
        $a = eZTranslatorManager::instance();
        $this->assertSame( $a, eZTranslatorManager::instance() );
        eZTranslatorManager::resetGlobals();
        $this->assertNotSame( $a, eZTranslatorManager::instance() );
    }

    public function testFirstHandlerWithAnAnswerWins()
    {
        $first = new eZTranslatorHandlersTestTable( array( 'a' => 'A1' ) );
        $second = new eZTranslatorHandlersTestTable( array( 'a' => 'A2', 'b' => 'B2' ) );
        eZTranslatorManager::registerHandler( $first );
        eZTranslatorManager::registerHandler( $second );
        $man = eZTranslatorManager::instance();

        $this->assertSame( 'A1', $man->translate( 'ctx', 'a' ) );
        $this->assertSame( 'B2', $man->translate( 'ctx', 'b' ) );
        $this->assertNull( $man->translate( 'ctx', 'missing' ) );
        $msg = $man->findMessage( '', 'b' );
        $this->assertSame( 'default', $msg['context'], 'an empty context is the default context' );
        $this->assertSame( 'B2', $msg['translation'] );
        $this->assertNull( $man->findMessage( 'ctx', 'missing' ) );
    }

    public function testStopsAtTheFirstAnswer()
    {
        $first = new eZTranslatorHandlersTestTable( array( 'a' => 'A1' ) );
        $second = new eZTranslatorHandlersTestTable( array( 'a' => 'A2' ) );
        eZTranslatorManager::registerHandler( $first );
        eZTranslatorManager::registerHandler( $second );
        eZTranslatorManager::instance()->translate( 'ctx', 'a' );
        $this->assertSame( 1, $first->Calls );
        $this->assertSame( 0, $second->Calls );
    }

    public function testKeysOnlyAskKeyBasedHandlers()
    {
        $plain = new eZTranslatorHandlersTestTable( array( 'a' => 'plain' ), false );
        $keyed = new eZTranslatorHandlersTestTable( array( 'a' => 'keyed' ), true );
        eZTranslatorManager::registerHandler( $plain );
        eZTranslatorManager::registerHandler( $keyed );
        $man = eZTranslatorManager::instance();
        $key = eZTranslatorManager::createKey( 'ctx', 'a' );

        $this->assertSame( 'keyed', $man->keyTranslate( $key ) );
        $this->assertSame( 'keyed', $man->findKey( $key )['translation'] );
        $this->assertNull( $man->keyTranslate( md5( 'nothing' ) ) );
        $this->assertSame( 0, $plain->Calls );
    }

    public function testBaseHandlerKnowsNothing()
    {
        $h = new eZTranslatorHandler( true );
        $this->assertTrue( $h->isKeyBased() );
        $this->assertFalse( ( new eZTranslatorHandler( false ) )->isKeyBased() );
        $this->assertNull( $h->findKey( 'k' ) );
        $this->assertNull( $h->findMessage( 'c', 's' ) );
        $this->assertNull( $h->translate( 'c', 's' ) );
        $this->assertNull( $h->keyTranslate( 'k' ) );
    }

    public function testDynamicTranslationsSwitch()
    {
        eZTranslatorManager::enableDynamicTranslations( true );
        $this->assertTrue( eZTranslatorManager::dynamicTranslationsEnabled() );
        eZTranslatorManager::enableDynamicTranslations( false );
        $this->assertFalse( eZTranslatorManager::dynamicTranslationsEnabled() );
        // with dynamic translations off, setActiveTranslation() changes nothing and touches no settings
        eZTranslatorManager::setActiveTranslation( 'ger-DE' );
        $this->assertFalse( eZTranslatorManager::dynamicTranslationsEnabled() );
    }

    public function testGroupDelegatesToThePickedHandler()
    {
        $group = new eZTranslatorHandlersTestFixedGroup( true );
        $this->assertSame( 0, $group->handlerCount() );
        $this->assertTrue( $group->registerHandler( new eZTranslatorHandlersTestTable( array( 'a' => 'zero' ), true ) ) );
        $this->assertTrue( $group->registerHandler( new eZTranslatorHandlersTestTable( array( 'a' => 'one' ), false ) ) );
        $this->assertSame( 2, $group->handlerCount() );

        $group->Pick = 1;
        $this->assertSame( 'one', $group->translate( 'ctx', 'a' ) );
        $this->assertSame( 'one', $group->findMessage( 'ctx', 'a' )['translation'] );
        $group->Pick = 0;
        $this->assertSame( 'zero', $group->keyTranslate( eZTranslatorManager::createKey( 'ctx', 'a' ) ) );
        $this->assertSame( 'zero', $group->findKey( eZTranslatorManager::createKey( 'ctx', 'a' ) )['translation'] );
    }

    /**
     * A pick of -1 (no handler) or of a number past the handlers answers null. A pick equal to the handler count
     * was let through and read a handler that does not exist.
     */
    public function testGroupPickOutOfRangeAnswersNull()
    {
        $group = new eZTranslatorHandlersTestFixedGroup( false );
        $group->registerHandler( new eZTranslatorHandlersTestTable( array( 'a' => 'zero' ) ) );
        foreach ( array( -1, 1, 5 ) as $pick )
        {
            $group->Pick = $pick;
            $this->assertNull( $group->translate( 'ctx', 'a' ), "pick $pick" );
            $this->assertNull( $group->findMessage( 'ctx', 'a' ), "pick $pick" );
            $this->assertNull( $group->findKey( 'k' ), "pick $pick" );
            $this->assertNull( $group->keyTranslate( 'k' ), "pick $pick" );
        }
    }

    public function testRandomTranslatorPicksARegisteredHandler()
    {
        $random = new eZRandomTranslator( false );
        $this->assertSame( -1, $random->pick( 'c', 's', null ) );
        $this->assertSame( -1, $random->keyPick( 'k' ) );
        $this->assertNull( $random->translate( 'c', 'a' ) );

        $random->registerHandler( new eZTranslatorHandlersTestTable( array( 'a' => 'x' ) ) );
        $random->registerHandler( new eZTranslatorHandlersTestTable( array( 'a' => 'y' ) ) );
        mt_srand( 42 );
        $seen = array();
        for ( $i = 0; $i < 50; ++$i )
        {
            $pick = $random->pick( 'c', 'a', null );
            $this->assertContains( $pick, array( 0, 1 ) );
            $this->assertContains( $random->keyPick( 'k' ), array( 0, 1 ) );
            $seen[$random->translate( 'c', 'a' )] = true;
        }
        ksort( $seen );
        $this->assertSame( array( 'x', 'y' ), array_keys( $seen ) );
        mt_srand();
    }

    public static function shuffleProvider()
    {
        return array(
            'word'            => array( 'Translation' ),
            'two words'       => array( 'Hello world' ),
            'two characters'  => array( 'ab' ),
            'with spaces'     => array( 'a b c d e f' ),
        );
    }

    /**
     * The shuffled text has the same characters as the source, moved around. Moving the first character appended
     * it at the end instead of swapping it with the last, so the text grew a character each time.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('shuffleProvider')]
    public function testShuffleKeepsEveryCharacter( $text )
    {
        $shuffle = new eZShuffleTranslator( 5 );
        $sorted = str_split( $text );
        sort( $sorted );
        for ( $seed = 1; $seed <= 200; ++$seed )
        {
            mt_srand( $seed );
            $result = $shuffle->shuffleText( $text );
            $chars = str_split( $result );
            sort( $chars );
            $this->assertSame( $sorted, $chars, "seed $seed gave '$result'" );
        }
        mt_srand();
    }

    public function testShuffleOfShortTexts()
    {
        $shuffle = new eZShuffleTranslator();
        $this->assertSame( 3, $shuffle->MaxChars );
        $this->assertSame( '', $shuffle->shuffleText( '' ) );
        $this->assertSame( 'x', $shuffle->shuffleText( 'x' ) );
    }

    public function testShuffleWithoutMovesKeepsTheText()
    {
        $shuffle = new eZShuffleTranslator( 0 );
        $this->assertSame( 'unchanged', $shuffle->shuffleText( 'unchanged' ) );
        $this->assertSame( 'unchanged', $shuffle->translate( 'ctx', 'unchanged' ) );
        $msg = $shuffle->findMessage( 'ctx', 'unchanged', 'note' );
        $this->assertSame( array( 'context' => 'ctx', 'source' => 'unchanged', 'comment' => 'note', 'translation' => 'unchanged' ), $msg );
    }
}
