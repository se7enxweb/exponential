<?php
/**
 * The translated messages of the GraphQL API (doc/eng-us/features/6.0/graphql.md, "Errors"): the catalogue an
 * Accept-Language header chooses, the parameters of a message, the library's limit messages in the kernel's own
 * words, and that every message the code sends has a translation in the German catalogue. No database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expGraphQLMessageTest extends PHPUnit\Framework\TestCase
{
    const HTTP_CODES = array( 'de-at' => 'ger-AT', 'de-de' => 'ger-DE', 'en-au' => 'eng-AU', 'en-us' => 'eng-US', 'es-es' => 'esl-ES',
                              'es-mx' => 'esl-MX', 'fr-fr' => 'fre-FR', 'pt-br' => 'por-BR' );

    protected function tearDown(): void
    {
        expGraphQLMessage::useLanguage( null );
    }

    public function testAcceptLanguageChoosesACatalogue()
    {
        $this->assertSame( 'ger-DE', expGraphQLMessage::localeFor( 'de-DE,de;q=0.9,en;q=0.8', self::HTTP_CODES ) );
        $this->assertSame( 'ger-DE', expGraphQLMessage::localeFor( 'de', self::HTTP_CODES ), 'de: de-DE before de-AT' );
        $this->assertSame( 'ger-AT', expGraphQLMessage::localeFor( 'de-AT', self::HTTP_CODES ) );
        $this->assertSame( 'esl-MX', expGraphQLMessage::localeFor( 'es-MX', self::HTTP_CODES ) );
        $this->assertSame( 'por-BR', expGraphQLMessage::localeFor( 'pt', self::HTTP_CODES ), 'the only catalogue of the language' );
        $this->assertSame( 'fre-FR', expGraphQLMessage::localeFor( 'xx-YY, fr;q=0.5, de;q=0.2', self::HTTP_CODES ), 'by q, the first one there is' );
        $this->assertSame( 'ger-DE', expGraphQLMessage::localeFor( 'fr;q=0.1, de;q=0.9', self::HTTP_CODES ) );
        $this->assertNull( expGraphQLMessage::localeFor( 'xx, *;q=0.1', self::HTTP_CODES ) );
        $this->assertNull( expGraphQLMessage::localeFor( 'fr;q=0', self::HTTP_CODES ), 'q=0: not wanted' );
        expGraphQLMessage::useLanguage( '' );
        $this->assertNull( expGraphQLMessage::locale(), 'no header: the language of the siteaccess' );
    }

    public function testAMessageCarriesItsParameters()
    {
        expGraphQLMessage::useLanguage( null );
        $this->assertSame( 'The query is longer than 5 bytes.', expGraphQLMessage::tr( 'The query is longer than %max bytes.', array( '%max' => 5 ) ) );
    }

    public function testTheLibrarysLimitMessagesAreTheKernelsOwn()
    {
        $this->assertSame( 'The query is 13 levels deep; at most 12 are allowed.',
                           expGraphQLServer::translatedMessage( expGraphQLError::DEPTH_LIMIT, 'Max query depth should be 12 but got 13.' ) );
        $this->assertSame( 'The query has a complexity of 20202; at most 1000 is allowed.',
                           expGraphQLServer::translatedMessage( expGraphQLError::COMPLEXITY_LIMIT, 'Max query complexity should be 1000 but got 20202.' ) );
        $this->assertSame( 'Cannot query field "x" on type "Content".',
                           expGraphQLServer::translatedMessage( expGraphQLError::VALIDATION, 'Cannot query field "x" on type "Content".' ), 'the specification\'s messages stay' );
    }
}
