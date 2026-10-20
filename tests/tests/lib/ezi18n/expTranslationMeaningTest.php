<?php
/**
 * File containing the expTranslationMeaningTest class
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

/**
 * Translations that were once wrong in meaning stay right. expTranslationQualityTest checks the form of every
 * translation (placeholders, tags, whitespace); it cannot see that a word was taken in the wrong sense. These were:
 *
 * - "Block" on the settings view (design/admin/settings) is an INI block, a noun. Brazilian Portuguese had the verb
 *   "Bloquear" (to block), Swedish the verb "Blockera", and Norwegian "Seksjon", the word its catalogue uses for a
 *   content section, a different thing in the same administration.
 * - "You are not allowed to access %1." said the opposite in Swedish: "Du har åtkomst till %1." (you have access).
 *
 * DB-less: the catalogues are read with DOMDocument.
 */
class expTranslationMeaningTest extends PHPUnit\Framework\TestCase
{
    private static function translation( $language, $context, $source )
    {
        $file = dirname( __DIR__, 4 ) . '/share/translations/' . $language . '/translation.ts';
        $doc = new DOMDocument();
        self::assertTrue( $doc->load( $file ), $file . ' loads' );
        $xpath = new DOMXPath( $doc );
        $found = array();
        foreach ( $xpath->query( '//context[name="' . $context . '"]/message' ) as $message )
        {
            $src = $xpath->query( 'source', $message )->item( 0 );
            $tr = $xpath->query( 'translation', $message )->item( 0 );
            if ( $src && $tr && $src->textContent === $source && $tr->getAttribute( 'type' ) !== 'obsolete' )
                $found[] = $tr->textContent;
        }
        return $found;
    }

    public static function cases()
    {
        return array(
            'por-BR settings Block' => array( 'por-BR', 'design/admin/settings', 'Block', 'Bloco', '/^Bloque/i' ),
            'swe-SE settings Block' => array( 'swe-SE', 'design/admin/settings', 'Block', 'Block', '/^Blocker/i' ),
            'nor-NO settings Block' => array( 'nor-NO', 'design/admin/settings', 'Block', 'Blokk', '/seksjon/i' ),
            'nor-NO settings Blocks' => array( 'nor-NO', 'design/admin/settings', 'Blocks', 'Blokker', '/seksjon/i' ),
            'swe-SE not allowed to access' => array( 'swe-SE', 'design/standard/user', 'You are not allowed to access %1.',
                                                     'Du har inte åtkomst till %1.', '/^Du har åtkomst/' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('cases')]
    public function testTranslationKeepsItsMeaning( $language, $context, $source, $expected, $wrong )
    {
        $found = self::translation( $language, $context, $source );
        $this->assertNotEmpty( $found, "$language has \"$source\" in $context" );
        foreach ( $found as $translation )
        {
            $this->assertDoesNotMatchRegularExpression( $wrong, $translation );
            $this->assertSame( $expected, $translation );
        }
    }
}
