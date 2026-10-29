<?php
/**
 * The Spanish administration translations address the reader with "tú" (pull request #282 made the last "usted"
 * messages use it): no finished translation of esl-ES or esl-MX, in the kernel or in the extensions shipped in the
 * kernel repository, uses "usted", "Ud." or a formal imperative at the start of a sentence ("Seleccione",
 * "Introduzca", "Haga clic" ...). A catalogue with the other convention is listed in CONVENTIONS as 'usted' and is then
 * checked the other way round. The forms are in FORMS: add one rather than weakening the test; a subjunctive inside
 * a sentence ("que se haga") is not matched, only a sentence's first word.
 *
 * No database. The messages are read with XMLReader, as expUISwitchLanguageCatalogue reads them.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group ezi18n
 */

class expSpanishTuFormTest extends PHPUnit\Framework\TestCase
{
    /** catalogue => the form of address it uses */
    const CONVENTIONS = array( 'esl-ES' => 'tu', 'esl-MX' => 'tu' );

    /** the translation roots checked, relative to the root */
    const ROOTS = array( 'share/translations', 'extension/ezoe/translations', 'extension/ezjscore/translations' );

    /** formal ("usted") forms: anywhere for the pronouns, at a sentence's start for the imperatives */
    const FORMS = array(
        'usted' => '/\b(usted|ustedes|Ud\.|Uds\.)(?![\p{L}])/iu',
        'imperative' => '/(^|[.!?:]\s+|["«(]\s*)(Seleccione|Introduzca|Pulse|Escriba|Elija|Haga clic|Asegúrese|Compruebe|Intente|Vuelva a|Espere|Indique|Utilice|Consulte|Inicie sesión|Cambie|Agregue|Añada|Revise)\b/u' );

    /** informal forms, for a catalogue whose convention is "usted" */
    const INFORMAL = array( 'tu' => '/(^|[.!?:]\s+)(Selecciona|Introduce|Pulsa|Escribe|Elige|Haz clic|Asegúrate)\b/u' );

    public static function catalogues()
    {
        $root = dirname( __DIR__, 4 );
        $cases = array();
        foreach ( self::CONVENTIONS as $code => $convention )
        {
            foreach ( self::ROOTS as $dir )
            {
                $file = $root . '/' . $dir . '/' . $code . '/translation.ts';
                if ( is_file( $file ) )
                    $cases[$dir . ' ' . $code] = array( $file, $convention );
            }
        }
        return $cases;
    }

    /**
     * @dataProvider catalogues
     */
    #[\PHPUnit\Framework\Attributes\DataProvider( 'catalogues' )]
    public function testTheCatalogueKeepsItsFormOfAddress( $file, $convention )
    {
        $found = array();
        foreach ( self::translations( $file ) as $source => $translation )
        {
            $patterns = $convention === 'tu' ? self::FORMS : self::INFORMAL;
            foreach ( $patterns as $kind => $pattern )
            {
                if ( preg_match( $pattern, $translation ) )
                    $found[] = $kind . ': ' . $translation;
            }
        }
        $this->assertSame( array(), array_slice( $found, 0, 20 ), count( $found ) . ' messages of ' . $file . ' do not use "' . $convention . '"' );
    }

    public function testThePatternsFindTheFormalForms()
    {
        $this->assertSame( 1, preg_match( self::FORMS['usted'], 'Si usted lo desea' ) );
        $this->assertSame( 1, preg_match( self::FORMS['usted'], 'Gracias a Ud. por todo' ) );
        $this->assertSame( 0, preg_match( self::FORMS['usted'], 'ustedad' ) );
        $this->assertSame( 1, preg_match( self::FORMS['imperative'], 'Seleccione la traducción.' ) );
        $this->assertSame( 1, preg_match( self::FORMS['imperative'], 'Listo. Haga clic aquí.' ) );
        $this->assertSame( 0, preg_match( self::FORMS['imperative'], 'Selecciona la traducción.' ) );
        $this->assertSame( 0, preg_match( self::FORMS['imperative'], 'Esto se haga clic' ), 'only at a sentence start' );
    }

    /**
     * The finished translations of a file, source => translation.
     *
     * @param string $file
     * @return array
     */
    protected static function translations( $file )
    {
        $result = array();
        $reader = new XMLReader();
        $reader->open( $file, null, LIBXML_NONET );
        while ( @$reader->read() )
        {
            if ( $reader->nodeType !== XMLReader::ELEMENT || $reader->name !== 'message' )
                continue;
            $node = $reader->expand();
            if ( !$node instanceof DOMElement )
                continue;
            $source = $translation = $type = '';
            foreach ( $node->childNodes as $child )
            {
                if ( $child instanceof DOMElement && $child->tagName === 'source' )
                    $source = $child->textContent;
                elseif ( $child instanceof DOMElement && $child->tagName === 'translation' )
                {
                    $translation = $child->textContent;
                    $type = $child->getAttribute( 'type' );
                }
            }
            if ( trim( $translation ) !== '' && $type !== 'unfinished' && $type !== 'obsolete' && $type !== 'vanished' )
                $result[$source . "\x1F" . count( $result )] = $translation;
            $reader->next();
        }
        $reader->close();
        return $result;
    }
}
