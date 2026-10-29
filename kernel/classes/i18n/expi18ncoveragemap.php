<?php
/**
 * File containing the expI18nCoverageMap class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The language totals and the world map at the top of doc/eng-us/languages.md and doc/ger-de/sprachen.md, made from
 * the files a default installation ships (./console exp:i18n coverage):
 *
 * - the administration translations (share/translations/<code>/translation.ts) and how many are complete;
 * - the locales (share/locale/*.ini), their language codes, the countries and territories they name (the region of
 *   their [HTTP] ContentLanguage, ISO 3166-1 alpha-2; private-use codes and locales without a region left out) and
 *   how many are written right to left ([LanguageSettings] TextDirection=rtl);
 * - per country, whether it is covered: a shipped locale for it whose language the administration shows, through a
 *   translation of its own or the first translation of its fallback chain (settings/i18n.ini [TranslationSettings],
 *   as expI18nFallbackResolver and expUISwitchLanguageCatalogue count it: with FallbackMode=first only the first
 *   translation of the chain counts). English counts as covered, being the language of the source texts;
 * - the known languages (the language codes of those locales), the ones the administration shows translated in the
 *   same way and the ones it does not, in a section of their own (LANGUAGES_START to LANGUAGES_END).
 *
 * Only the shipped settings/i18n.ini is read (no override, no extension), so the result is that of a default
 * installation wherever it is made. The map is drawn on the public-domain outline in OUTLINE (Natural Earth): one SVG
 * per documentation language, with its title, description and legend, and a WebP copy of it for the help viewer,
 * which shows no SVG. The generated part of each page lies between its START and END markers; the date in the start
 * marker is kept while nothing else changes, so a check on another day still passes.
 *
 * @package kernel
 */
class expI18nCoverageMap
{
    /** The world outline, relative to the root */
    const OUTLINE = 'doc/eng-us/images/languages/world-outline.svg';

    /** Per documentation language: the page, the SVG, its WebP copy and the image as the page names it */
    const PAGES = array(
        'eng-us' => array( 'page' => 'doc/eng-us/languages.md',
                           'svg' => 'doc/eng-us/images/languages/coverage-map.svg',
                           'raster' => 'doc/eng-us/images/languages/coverage-map.webp',
                           'image' => 'images/languages/coverage-map.webp' ),
        'ger-de' => array( 'page' => 'doc/ger-de/sprachen.md',
                           'svg' => 'doc/ger-de/bilder/sprachen/abdeckungskarte.svg',
                           'raster' => 'doc/ger-de/bilder/sprachen/abdeckungskarte.webp',
                           'image' => 'bilder/sprachen/abdeckungskarte.webp' ),
    );

    /** The markers of the generated part of a page */
    const START_PATTERN = '/^<!-- generated: exp:i18n coverage (\d{4}-\d{2}-\d{2}) \(start\) -->$/m';

    /** The raster check (rasterDifference()): a pixel of a WebP copy differs from its map rendered again when one
     * of its channels does by more than this share of the range; green and grey land, and land and sea, differ by
     * far more */
    const RASTER_FUZZ = 0.1;
    /** ... and counts only in an area at least a disk of this radius (pixels) across: thinner differences are those
     * of the renderer (anti-aliasing, borders, strokes), not of the map; an island circle (radius 6) is wider */
    const RASTER_GRAIN = 3;
    const END = '<!-- generated: exp:i18n coverage (end) -->';

    /** The markers of the generated section of the languages not translated */
    const LANGUAGES_START = '<!-- generated: exp:i18n coverage languages (start) -->';
    const LANGUAGES_END = '<!-- generated: exp:i18n coverage languages (end) -->';

    /** The markers of the generated "In short" paragraph, with the list of the complete translations; a page without them keeps its own text */
    const TRANSLATIONS_START = '<!-- generated: exp:i18n coverage translations (start) -->';
    const TRANSLATIONS_END = '<!-- generated: exp:i18n coverage translations (end) -->';

    /** The colors: covered land, other land, borders; legible on a light and on a dark page */
    const COVERED = '#2e8b57';
    const OTHER = '#b8bec6';
    const STROKE = '#6f7781';
    /** The borders of covered land: a darker shade of COVERED, so that it reads green at every size */
    const COVERED_STROKE = '#24704a';
    const STROKE_WIDTH = 0.7;
    /** Shapes smaller than this (the larger side of their bounding box, map units) get the thinner border */
    const SMALL_SHAPE = 12;
    const SMALL_STROKE_WIDTH = 0.35;

    /** The height of the legend strip below the map, in map units */
    const LEGEND_HEIGHT = 170;

    /** Region codes that name no country: user-assigned ISO 3166-1 codes (QO, ZZ ...); XK, Kosovo, is the one
     * user-assigned code that CLDR, the European Union and the outline of the map use for a country */
    const NO_COUNTRY = '/^(AA|Q[M-Z]|X[A-JL-Z]|ZZ)$/';

    /**
     * The codes of settings/country.ini that have no locale by design: territories without a population, for which
     * CLDR defines no locale, and a code ISO 3166-1 withdrew, with the year and the codes that replaced it. Every
     * other code of settings/country.ini has a locale; one that has none is listed as "not yet" (see data()).
     */
    const NO_LOCALE_BY_DESIGN = array(
        'AN' => array( 'reason' => 'withdrawn', 'year' => 2010, 'successors' => array( 'BQ', 'CW', 'SX' ) ),
        'AQ' => array( 'reason' => 'uninhabited' ),
        'BV' => array( 'reason' => 'uninhabited' ),
        'HM' => array( 'reason' => 'uninhabited' ),
    );

    /** German names of withdrawn codes, which share/locale/ger-DE.ini (CLDR) does not name any more */
    const WITHDRAWN_NAMES_GERMAN = array( 'AN' => 'Niederländische Antillen' );

    /** @var string the installation root, without a trailing slash */
    protected $root;

    /** @var array|null see data() */
    protected $data;

    /** @var array|null the interface switch catalogue of the shipped files, see data() */
    protected $entries;

    /**
     * @param string $root the installation root
     */
    public function __construct( $root )
    {
        $this->root = rtrim( (string)$root, '/' );
    }

    /**
     * The settings of the shipped settings/i18n.ini [TranslationSettings]: configured, rules, options.
     *
     * @return array
     */
    public function settings()
    {
        $values = self::parseBlock( (string)@file_get_contents( $this->root . '/settings/i18n.ini' ), 'TranslationSettings' );
        $get = function ( $name, $default ) use ( $values )
        {
            return array_key_exists( $name, $values ) ? $values[$name] : $default;
        };
        return array(
            'configured' => (array)$get( 'FallbackLanguages', array() ),
            'rules' => array_values( (array)$get( 'FallbackRules', array() ) ),
            'options' => array(
                'automatic' => $get( 'AutomaticLanguageFallback', 'enabled' ) === 'enabled',
                'automatic_exclude' => (array)$get( 'AutomaticFallbackExclude', array( 'eng' ) ),
                'max_depth' => (int)$get( 'FallbackMaxDepth', 4 ),
                'mode' => (string)$get( 'FallbackMode', 'first' ),
                'repository' => $this->root . '/share/translations',
                'locale_directory' => $this->root . '/share/locale',
            ),
        );
    }

    /**
     * The settings of one block of an INI text: Name=value, Name[]=value (a list; "Name[]" alone empties it) and
     * Name[key]=value (a hash). Comments (#) and other blocks are skipped.
     *
     * @param string $text
     * @param string $block
     * @return array
     */
    public static function parseBlock( $text, $block )
    {
        $values = array();
        $current = '';
        foreach ( preg_split( '/\r\n|\r|\n/', (string)$text ) as $line )
        {
            $line = trim( $line );
            if ( $line === '' || $line[0] === '#' )
                continue;
            if ( preg_match( '/^\[([^\]]+)\]$/', $line, $m ) )
            {
                $current = $m[1];
                continue;
            }
            if ( $current !== $block )
                continue;
            if ( preg_match( '/^([A-Za-z0-9_-]+)\[\]$/', $line, $m ) )
                $values[$m[1]] = array();
            elseif ( preg_match( '/^([A-Za-z0-9_-]+)\[([^\]]*)\]=(.*)$/', $line, $m ) )
            {
                if ( !isset( $values[$m[1]] ) || !is_array( $values[$m[1]] ) )
                    $values[$m[1]] = array();
                if ( $m[2] === '' )
                    $values[$m[1]][] = trim( $m[3] );
                else
                    $values[$m[1]][$m[2]] = trim( $m[3] );
            }
            elseif ( preg_match( '/^([A-Za-z0-9_-]+)=(.*)$/', $line, $m ) )
                $values[$m[1]] = trim( $m[2] );
        }
        return $values;
    }

    /**
     * The fallback resolver of a default installation below the root.
     *
     * @return expI18nFallbackResolver
     */
    public function resolver()
    {
        $settings = $this->settings();
        return new expI18nFallbackResolver( $settings['configured'], $settings['rules'], $settings['options'] );
    }

    /**
     * Everything the page and the map show:
     *   translations: hash( total, complete, codes ),
     *   locales: hash( files, variants, test, base, languages (distinct language codes of the real locales), rtl ),
     *   countries: ISO code => hash( code, covered, locales => list of hash( locale, shows ) ) sorted by code, where
     *              shows is the translation the administration shows ('' for the source texts in English for a locale
     *              that has none, 'eng' for an English locale shown in the source texts),
     *   covered: the number of covered countries,
     *   languages: the known languages, those of the real locales (variants and test languages left out), by
     *              language code => hash( code, name (English, of its first locale), native, supported, locales ),
     *              supported when the administration shows one of its locales translated (or English),
     *   supported: the number of supported languages,
     *   without: the codes of settings/country.ini no locale names (user-assigned codes left out): design, ISO code
     *            => its NO_LOCALE_BY_DESIGN entry, and missing, the list of the others (none in a default installation).
     *
     * @return array
     */
    public function data()
    {
        if ( $this->data !== null )
            return $this->data;
        $resolver = $this->resolver();
        $catalogue = new expUISwitchLanguageCatalogue( $this->root, '', null, $resolver );
        $entries = $this->entries = $catalogue->build();

        $files = array();
        foreach ( (array)glob( $this->root . '/share/locale/*.ini' ) as $file )
            $files[basename( $file, '.ini' )] = $file;
        ksort( $files, SORT_STRING );

        $variants = 0;
        $test = 0;
        $rtl = 0;
        $languages = array();
        $countries = array();
        foreach ( $files as $code => $file )
        {
            $text = (string)file_get_contents( $file );
            if ( preg_match( '/^\s*TextDirection\s*=\s*rtl\s*$/mi', $text ) )
                $rtl++;
            if ( strpos( $code, '@' ) !== false )
            {
                $variants++;
                continue;
            }
            $entry = isset( $entries[$code] ) ? $entries[$code] : null;
            if ( $entry !== null && !empty( $entry['test'] ) )
            {
                $test++;
                continue;
            }
            $shows = '';
            if ( strpos( $code, 'eng-' ) === 0 )
            {
                // English: a translation of its own (eng-US) or of its chain, else the source texts
                $shows = 'eng';
                foreach ( array_merge( array( $code ), $resolver->chain( $code ) ) as $candidate )
                {
                    if ( $candidate === expI18nFallbackResolver::SOURCE )
                        break;
                    if ( $resolver->hasTranslation( $candidate ) )
                    {
                        $shows = $candidate;
                        break;
                    }
                }
            }
            elseif ( $entry !== null && $entry['coverage'] !== null && $entry['coverage'] > 0 )
                $shows = $entry['fallback'] !== '' ? (string)$entry['fallback'] : $code;

            // the language: its code (the three letters before the dash) and the name of its first locale
            $language = substr( $code, 0, 3 );
            if ( !isset( $languages[$language] ) )
            {
                $languages[$language] = array( 'code' => $language,
                                               'name' => self::baseName( $entry !== null ? (string)$entry['english'] : '', $code ),
                                               'native' => self::baseName( $entry !== null ? (string)$entry['native'] : '', '' ),
                                               'supported' => false, 'locales' => array() );
            }
            $languages[$language]['locales'][] = array( 'locale' => $code, 'shows' => $shows );
            if ( $shows !== '' )
                $languages[$language]['supported'] = true;

            $http = $entry !== null ? (string)$entry['http'] : '';
            if ( !preg_match( '/-([A-Z]{2})(?:-|$)/', $http, $m ) || preg_match( self::NO_COUNTRY, $m[1] ) )
                continue;
            $iso = $m[1];
            if ( !isset( $countries[$iso] ) )
                $countries[$iso] = array( 'code' => $iso, 'covered' => false, 'locales' => array() );
            $countries[$iso]['locales'][] = array( 'locale' => $code, 'shows' => $shows );
            if ( $shows !== '' )
                $countries[$iso]['covered'] = true;
        }
        ksort( $languages, SORT_STRING );
        ksort( $countries, SORT_STRING );

        $codes = $resolver->translations();
        $complete = 0;
        foreach ( $codes as $code )
        {
            if ( isset( $entries[$code] ) && $entries[$code]['coverage'] !== null && $entries[$code]['coverage'] >= 100 )
                $complete++;
        }
        $covered = count( array_filter( $countries, function ( $country ) { return $country['covered']; } ) );
        // the codes of settings/country.ini without a locale: by design (NO_LOCALE_BY_DESIGN) or not yet
        $without = array( 'design' => array(), 'missing' => array() );
        foreach ( array_keys( $this->countryNames( 'eng-us' ) ) as $iso )
        {
            if ( isset( $countries[$iso] ) || preg_match( self::NO_COUNTRY, $iso ) )
                continue;
            if ( isset( self::NO_LOCALE_BY_DESIGN[$iso] ) )
                $without['design'][$iso] = self::NO_LOCALE_BY_DESIGN[$iso];
            else
                $without['missing'][] = $iso;
        }
        ksort( $without['design'], SORT_STRING );
        sort( $without['missing'], SORT_STRING );
        return $this->data = array(
            'translations' => array( 'total' => count( $codes ), 'complete' => $complete, 'codes' => $codes ),
            'locales' => array( 'files' => count( $files ), 'variants' => $variants, 'test' => $test,
                                'base' => count( $files ) - $variants, 'languages' => count( $languages ), 'rtl' => $rtl ),
            'countries' => $countries,
            'covered' => $covered,
            'languages' => $languages,
            'supported' => count( array_filter( $languages, function ( $language ) { return $language['supported']; } ) ),
            'without' => $without,
        );
    }

    /**
     * A language name without what follows it in brackets: "German (Austria)" is German.
     *
     * @param string $name
     * @param string $default when the name is empty
     * @return string
     */
    public static function baseName( $name, $default )
    {
        $name = trim( (string)preg_replace( '/\s*\(.*$/s', '', (string)$name ) );
        return $name !== '' ? $name : (string)$default;
    }

    /**
     * The languages the administration does not show translated, sorted by name: list of the data() language.
     *
     * @return array
     */
    public function untranslatedLanguages()
    {
        $list = array_values( array_filter( $this->data()['languages'], function ( $language ) { return !$language['supported']; } ) );
        usort( $list, function ( $a, $b )
        {
            $order = strcmp( expUISwitchLanguageCatalogue::foldForSorting( $a['name'] ), expUISwitchLanguageCatalogue::foldForSorting( $b['name'] ) );
            return $order !== 0 ? $order : strcmp( $a['code'], $b['code'] );
        } );
        return $list;
    }

    /**
     * The country names of a documentation language, ISO code => name: English from settings/country.ini, German
     * from share/locale/ger-DE.ini [CountryNames]; a code without one keeps the English name or the code.
     *
     * @param string $language eng-us or ger-de
     * @return array
     */
    public function countryNames( $language )
    {
        $names = array();
        $current = '';
        foreach ( (array)@file( $this->root . '/settings/country.ini', FILE_IGNORE_NEW_LINES ) as $line )
        {
            $line = trim( $line );
            if ( preg_match( '/^\[([A-Z]{2})\]$/', $line, $m ) )
                $current = $m[1];
            elseif ( $current !== '' && preg_match( '/^Name=(.+)$/', $line, $m ) )
            {
                $names[$current] = trim( $m[1] );
                $current = '';
            }
        }
        if ( $language === 'ger-de' )
        {
            $german = self::parseBlock( (string)@file_get_contents( $this->root . '/share/locale/ger-DE.ini' ), 'CountryNames' );
            foreach ( isset( $german['Countries'] ) ? (array)$german['Countries'] : array() as $code => $name )
            {
                if ( preg_match( '/^[A-Z]{2}$/', (string)$code ) && $name !== '' )
                    $names[$code] = $name;
            }
            foreach ( self::WITHDRAWN_NAMES_GERMAN as $code => $name )
            {
                if ( isset( $names[$code] ) && ( !isset( $german['Countries'][$code] ) || $german['Countries'][$code] === '' ) )
                    $names[$code] = $name;
            }
        }
        return $names;
    }

    /**
     * The countries sorted by their name in the language: list of the data() country with 'name'.
     *
     * @param string $language
     * @return array
     */
    public function sortedCountries( $language )
    {
        $names = $this->countryNames( $language );
        $list = array();
        foreach ( $this->data()['countries'] as $iso => $country )
        {
            $country['name'] = isset( $names[$iso] ) ? $names[$iso] : $iso;
            $list[] = $country;
        }
        usort( $list, function ( $a, $b )
        {
            $order = strcmp( expUISwitchLanguageCatalogue::foldForSorting( $a['name'] ), expUISwitchLanguageCatalogue::foldForSorting( $b['name'] ) );
            return $order !== 0 ? $order : strcmp( $a['code'], $b['code'] );
        } );
        return $list;
    }

    /**
     * The codes of settings/country.ini without a locale, by design first, then those without one yet, each sorted
     * by code: list of hash( code, name, design (bool), why ), why in the documentation language.
     *
     * @param string $language eng-us or ger-de
     * @return array
     */
    public function withoutLocale( $language )
    {
        $german = $language === 'ger-de';
        $names = $this->countryNames( $language );
        $data = $this->data();
        $name = function ( $iso ) use ( $names ) { return isset( $names[$iso] ) ? $names[$iso] : $iso; };
        $list = array();
        foreach ( $data['without']['design'] as $iso => $entry )
        {
            if ( $entry['reason'] === 'withdrawn' )
            {
                $successors = array();
                $none = array();
                foreach ( isset( $entry['successors'] ) ? $entry['successors'] : array() as $successor )
                {
                    $successors[] = $name( $successor ) . ' (' . $successor . ')';
                    if ( !isset( $data['countries'][$successor] ) )
                        $none[] = $successor;
                }
                $joined = self::joinList( $successors, $german );
                if ( $german )
                    $why = 'Keine Locale, mit Absicht: Code ' . $entry['year'] . ' zurückgezogen'
                         . ( $successors ? '; ' . ( $none ? 'seine Nachfolger sind ' . $joined . ', ohne Locale noch ' . implode( ', ', $none )
                                                          : 'seine Nachfolger ' . $joined . ' haben je eine eigene Locale' ) : '' );
                else
                    $why = 'No locale by design: code withdrawn in ' . $entry['year']
                         . ( $successors ? '; ' . ( $none ? 'its successors are ' . $joined . ', still without a locale: ' . implode( ', ', $none )
                                                          : 'its successors ' . $joined . ' each have a locale of their own' ) : '' );
            }
            else
                $why = $german ? 'Keine Locale, mit Absicht: unbewohnt, CLDR definiert keine Locale dafür (grün dargestellt: Exponential bedient diese Gebiete in jeder Sprache; lokale Konventionen gibt es keine)'
                               : 'No locale by design: uninhabited, CLDR defines no locale for it (shown green: Exponential serves these territories in every language; no local conventions exist)';
            $list[] = array( 'code' => $iso, 'name' => $name( $iso ), 'design' => true, 'why' => $why );
        }
        foreach ( $data['without']['missing'] as $iso )
            $list[] = array( 'code' => $iso, 'name' => $name( $iso ), 'design' => false, 'why' => $german ? 'Noch keine Locale' : 'No locale yet' );
        return $list;
    }

    /**
     * The territories without a locale by design that the map shows green as well: those without a population
     * (Antarctica, Bouvet Island ...), which Exponential serves in every language, no local conventions existing
     * for them. A withdrawn code names no territory and is left out.
     *
     * @return string[] ISO codes, sorted
     */
    public function greenByDesign()
    {
        $codes = array();
        foreach ( $this->data()['without']['design'] as $iso => $entry )
        {
            if ( $entry['reason'] !== 'withdrawn' )
                $codes[] = $iso;
        }
        return $codes;
    }

    /**
     * The names of greenByDesign() in the documentation language, as a list: "A, B and C".
     *
     * @param string $language
     * @param bool $codes with the code in brackets after each name
     * @return string
     */
    protected function greenByDesignNames( $language, $codes = false )
    {
        $names = $this->countryNames( $language );
        $list = array();
        foreach ( $this->greenByDesign() as $iso )
            $list[] = ( isset( $names[$iso] ) ? $names[$iso] : $iso ) . ( $codes ? ' (`' . $iso . '`)' : '' );
        return self::joinList( $list, $language === 'ger-de' );
    }

    /**
     * The cell of the summary table that counts the codes without a locale.
     *
     * @param array $without the data() entry
     * @param bool $german
     * @return string
     */
    protected static function withoutCount( array $without, $german )
    {
        $design = count( $without['design'] );
        $missing = count( $without['missing'] );
        $text = '**' . ( $design + $missing ) . '**';
        $reasons = array();
        foreach ( $without['design'] as $entry )
            $reasons[$entry['reason']] = $german ? ( $entry['reason'] === 'withdrawn' ? 'zurückgezogen' : 'unbewohnt' ) : $entry['reason'];
        ksort( $reasons, SORT_STRING );
        $reasons = implode( $german ? ' oder ' : ' or ', $reasons );
        if ( $design && !$missing )
            $text .= $german ? ', alle mit Absicht: ' . $reasons . ' (Liste unter der Karte)'
                             : ', all of them by design: ' . $reasons . ' (list below the map)';
        elseif ( $missing )
            $text .= $german ? ': ' . $design . ' mit Absicht, ' . $missing . ' noch nicht (Liste unter der Karte)'
                             : ': ' . $design . ' by design, ' . $missing . ' not yet (list below the map)';
        return $text;
    }

    /**
     * "A, B and C" in the documentation language.
     *
     * @param string[] $items
     * @param bool $german
     * @return string
     */
    protected static function joinList( array $items, $german )
    {
        if ( count( $items ) < 2 )
            return implode( '', $items );
        $last = array_pop( $items );
        return implode( ', ', $items ) . ( $german ? ' und ' : ' and ' ) . $last;
    }

    /**
     * The texts of a documentation language.
     *
     * @param string $language
     * @return array
     */
    protected static function texts( $language )
    {
        if ( $language === 'ger-de' )
            return array(
                'title' => 'Exponential: die Länder und Gebiete mit der Administration in ihrer Sprache',
                'desc' => 'Weltkarte. Grün: %1$d Länder und Gebiete, für die eine mitgelieferte Locale existiert, deren Sprache die Administration einer Standardinstallation übersetzt zeigt (eigene Übersetzung oder Fallback). Grau: die übrigen. Die Liste steht unter der Karte in doc/ger-de/sprachen.md.',
                'legend_covered' => 'Administration in der Sprache einer mitgelieferten Locale (%1$d)',
                'legend_other' => 'Andere Länder und Gebiete',
                'design' => 'Ebenfalls grün, ohne Locale mit Absicht (unbewohnt): %1$s.',
                'design_why' => 'Exponential bedient diese Gebiete in jeder Sprache; lokale Konventionen gibt es keine.',
                'source' => 'Karte: Natural Earth, gemeinfrei',
            );
        return array(
            'title' => 'Exponential: the countries and territories with the administration in their language',
            'desc' => 'World map. Green: %1$d countries and territories with a shipped locale whose language the administration of a default installation shows translated (its own translation or a fallback). Grey: the others. The list is below the map in doc/eng-us/languages.md.',
            'legend_covered' => 'Administration in the language of a shipped locale (%1$d)',
            'legend_other' => 'Other countries and territories',
            'design' => 'Also green, without a locale by design (uninhabited): %1$s.',
            'design_why' => 'Exponential serves these territories in every language; no local conventions exist.',
            'source' => 'Map: Natural Earth, public domain',
        );
    }

    /**
     * The map of a documentation language: the outline with the covered countries in green, a title, a description
     * and a legend.
     *
     * @param string $language
     * @return string
     * @throws RuntimeException when the outline cannot be read
     */
    public function svg( $language )
    {
        $outline = @file_get_contents( $this->root . '/' . self::OUTLINE );
        if ( !is_string( $outline ) || !preg_match( '/viewBox="0 0 (\d+) (\d+)"/', $outline, $box ) )
            throw new RuntimeException( 'The world outline ' . self::OUTLINE . ' cannot be read' );
        $width = (int)$box[1];
        $mapHeight = (int)$box[2];
        $height = $mapHeight + self::LEGEND_HEIGHT;
        preg_match_all( '/<path data-iso="([A-Z]{0,2})" d="([^"]+)"\/>/', $outline, $paths, PREG_SET_ORDER );
        preg_match_all( '/<circle data-iso="([A-Z]{2})" cx="(\d+)" cy="(\d+)" r="(\d+)"\/>/', $outline, $circles, PREG_SET_ORDER );
        $data = $this->data();
        $covered = array();
        foreach ( $data['countries'] as $iso => $country )
        {
            if ( $country['covered'] )
                $covered[$iso] = true;
        }
        // the uninhabited territories without a locale by design are green too: served in every language
        $design = $this->greenByDesign();
        foreach ( $design as $iso )
            $covered[$iso] = true;
        $texts = self::texts( $language );
        $designText = $design ? sprintf( $texts['design'], $this->greenByDesignNames( $language ) ) : '';
        $esc = function ( $text ) { return htmlspecialchars( (string)$text, ENT_XML1 | ENT_QUOTES, 'UTF-8' ); };

        $groups = array( 'other' => array(), 'covered' => array() );
        foreach ( $paths as $path )
            $groups[isset( $covered[$path[1]] ) ? 'covered' : 'other'][] = '<path data-iso="' . $path[1] . '"'
                . ( self::pathSize( $path[2] ) < self::SMALL_SHAPE ? ' stroke-width="' . self::SMALL_STROKE_WIDTH . '"' : '' ) . ' d="' . $path[2] . '"/>';
        $dots = array( 'other' => array(), 'covered' => array() );
        foreach ( $circles as $circle )
            $dots[isset( $covered[$circle[1]] ) ? 'covered' : 'other'][] = '<circle data-iso="' . $circle[1] . '" cx="' . $circle[2] . '" cy="' . $circle[3] . '" r="' . $circle[4] . '"/>';

        $lines = array();
        $lines[] = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $width . ' ' . $height . '" width="' . $width . '" height="' . $height . '" role="img" aria-labelledby="map-title map-desc">';
        $lines[] = '<title id="map-title">' . $esc( $texts['title'] ) . '</title>';
        $lines[] = '<desc id="map-desc">' . $esc( sprintf( $texts['desc'], $data['covered'] ) . ( $design ? ' ' . $designText . ' ' . $texts['design_why'] : '' ) ) . '</desc>';
        $lines[] = '<!-- Generated by ./console exp:i18n coverage from the shipped locales, translations and settings/i18n.ini; do not edit.';
        $lines[] = '     World outline: Natural Earth 1:50m Admin 0 - Countries, Admin 0 - Map units and Admin 0 - Tiny countries';
        $lines[] = '     (https://www.naturalearthdata.com/), public domain; Equal Earth projection, Antarctica included,';
        $lines[] = '     simplified (' . self::OUTLINE . '). -->';
        // borders in a darker shade of each group's fill: covered land reads green at every size, also where many
        // small islands crowd together (the Arctic, Indonesia), which grey borders turned grey in a thumbnail
        $lines[] = '<g stroke-width="' . self::STROKE_WIDTH . '" stroke-linejoin="round">';
        $lines[] = '<g id="other" fill="' . self::OTHER . '" stroke="' . self::STROKE . '">';
        $lines = array_merge( $lines, $groups['other'], $dots['other'] );
        $lines[] = '</g>';
        $lines[] = '<g id="covered" fill="' . self::COVERED . '" stroke="' . self::COVERED_STROKE . '">';
        $lines = array_merge( $lines, $groups['covered'], $dots['covered'] );
        $lines[] = '</g>';
        $lines[] = '</g>';
        // the legend, in a strip below the map: a panel of its own, legible on a light and on a dark page
        $top = $mapHeight + 12;
        $lines[] = '<g id="legend" font-family="Helvetica, Arial, sans-serif" font-size="30" fill="#1f2328">';
        $lines[] = '<rect x="16" y="' . $top . '" width="' . ( $width - 32 ) . '" height="' . ( self::LEGEND_HEIGHT - 24 ) . '" rx="12" fill="#ffffff" stroke="' . self::STROKE . '" stroke-width="1.5"/>';
        $lines[] = '<rect x="40" y="' . ( $top + 22 ) . '" width="36" height="26" rx="4" fill="' . self::COVERED . '" stroke="' . self::COVERED_STROKE . '"/>';
        $lines[] = '<text x="90" y="' . ( $top + 45 ) . '">' . $esc( sprintf( $texts['legend_covered'], $data['covered'] ) ) . '</text>';
        $lines[] = '<rect x="1140" y="' . ( $top + 22 ) . '" width="36" height="26" rx="4" fill="' . self::OTHER . '" stroke="' . self::STROKE . '"/>';
        $lines[] = '<text x="1190" y="' . ( $top + 45 ) . '">' . $esc( $texts['legend_other'] ) . '</text>';
        if ( $design )
        {
            $lines[] = '<text x="40" y="' . ( $top + 90 ) . '" font-size="24">' . $esc( $designText ) . '</text>';
            $lines[] = '<text x="40" y="' . ( $top + 124 ) . '" font-size="24">' . $esc( $texts['design_why'] ) . '</text>';
        }
        $lines[] = '<text x="' . ( $design ? $width - 40 : 40 ) . '" y="' . ( $top + ( $design ? 124 : 88 ) ) . '" font-size="22" fill="#4b525a"'
                 . ( $design ? ' text-anchor="end"' : '' ) . '>' . $esc( $texts['source'] ) . '</text>';
        $lines[] = '</g>';
        $lines[] = '</svg>';
        return implode( "\n", $lines ) . "\n";
    }

    /**
     * The size of an outline path: the larger side of its bounding box, in map units. The outline writes each ring
     * as an absolute M followed by relative l steps.
     *
     * @param string $d
     * @return int
     */
    public static function pathSize( $d )
    {
        $size = 0;
        if ( !preg_match_all( '/M(-?\d+) (-?\d+)l([^Mz]*)/', $d, $rings, PREG_SET_ORDER ) )
            return $size;
        $minX = $minY = PHP_INT_MAX;
        $maxX = $maxY = PHP_INT_MIN;
        foreach ( $rings as $ring )
        {
            $x = (int)$ring[1];
            $y = (int)$ring[2];
            $minX = min( $minX, $x ); $maxX = max( $maxX, $x );
            $minY = min( $minY, $y ); $maxY = max( $maxY, $y );
            $steps = preg_split( '/\s+/', trim( $ring[3] ) );
            for ( $i = 0; $i + 1 < count( $steps ); $i += 2 )
            {
                $x += (int)$steps[$i];
                $y += (int)$steps[$i + 1];
                $minX = min( $minX, $x ); $maxX = max( $maxX, $x );
                $minY = min( $minY, $y ); $maxY = max( $maxY, $y );
            }
        }
        return max( $maxX - $minX, $maxY - $minY );
    }

    /**
     * A date as the page writes it.
     *
     * @param string $date YYYY-MM-DD
     * @param string $language
     * @return string
     */
    public static function formatDate( $date, $language )
    {
        list( $year, $month, $day ) = array_map( 'intval', explode( '-', $date ) );
        if ( $language === 'ger-de' )
        {
            $months = array( 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember' );
            return $day . '. ' . $months[$month - 1] . ' ' . $year;
        }
        $months = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
        return $day . ' ' . $months[$month - 1] . ' ' . $year;
    }

    /**
     * The generated part of a page, with its markers.
     *
     * @param string $language
     * @param string $date YYYY-MM-DD
     * @return string
     */
    public function block( $language, $date )
    {
        $data = $this->data();
        $t = $data['translations'];
        $l = $data['locales'];
        $countries = $this->sortedCountries( $language );
        $covered = array_values( array_filter( $countries, function ( $c ) { return $c['covered']; } ) );
        $others = array_values( array_filter( $countries, function ( $c ) { return !$c['covered']; } ) );
        $german = $language === 'ger-de';
        $image = self::PAGES[$language]['image'];
        $svgPath = self::PAGES[$language]['svg'];
        $when = self::formatDate( $date, $language );
        $design = $this->greenByDesignNames( $language, true );
        $designCount = count( $this->greenByDesign() );

        $out = array();
        $out[] = '<!-- generated: exp:i18n coverage ' . $date . ' (start) -->';
        if ( $german )
        {
            $out[] = '**Auf einen Blick, Stand ' . $when . '.** Eine Standardinstallation (die mitgelieferten `share/locale`,';
            $out[] = '`share/translations` und `settings/i18n.ini`, ohne Erweiterungen) hat:';
            $out[] = '';
            $out[] = '| Was | Anzahl |';
            $out[] = '|---|---:|';
            $out[] = '| Sprachen der Administration: Übersetzungen in `share/translations` | **' . $t['total'] . '**, davon ' . $t['complete'] . ' vollständig |';
            $out[] = '| Locales: Dateien `share/locale/*.ini` | **' . $l['files'] . '**: ' . ( $l['base'] - $l['test'] ) . ' Sprachen und Länder, ' . $l['test'] . ' Testsprache, ' . $l['variants'] . ' Varianten |';
            $out[] = '| Bekannte Sprachen: die Sprachcodes der Locales (ohne Varianten und Testsprache) | **' . $l['languages'] . '** |';
            $out[] = '| davon von der Administration übersetzt gezeigt (eigene Übersetzung, Fallback oder Englisch) | **' . $data['supported'] . '** |';
            $out[] = '| davon nicht übersetzt ([Liste](#die-noch-nicht-übersetzten-sprachen)) | **' . ( $l['languages'] - $data['supported'] ) . '** |';
            $out[] = '| Länder und Gebiete der Locales | **' . count( $countries ) . '** |';
            $out[] = '| davon mit der Administration in ihrer Sprache (grün auf der Karte) | **' . $data['covered'] . '** |';
            $out[] = '| Codes aus ISO 3166-1 (`settings/country.ini`) ohne Locale | ' . self::withoutCount( $data['without'], true ) . ' |';
            $out[] = '| Locales, die von rechts nach links geschrieben werden | **' . $l['rtl'] . '** |';
            $out[] = '';
            $out[] = '![Weltkarte: ' . $data['covered'] . ' Länder und Gebiete in Grün, für die eine mitgelieferte Locale die Administration in ihrer Sprache zeigt' . ( $designCount ? ', dazu ' . $designCount . ' unbewohnte Gebiete ohne Locale mit Absicht' : '' ) . '; die übrigen in Grau](' . $image . ')';
            $out[] = '';
            $out[] = 'Ein Land ist grün, wenn für es eine Locale mitgeliefert wird, deren Sprache die Administration übersetzt zeigt:';
            $out[] = 'mit einer eigenen Übersetzung oder der ersten Übersetzung ihrer Fallback-Kette (`i18n.ini [TranslationSettings]`,';
            $out[] = '`FallbackMode=first`, siehe [Abschnitt 13](#13-übersetzungs-fallbacks)). Englisch zählt als übersetzt: Es ist die';
            $out[] = 'Sprache der Quelltexte. Die Karte als Vektorgrafik: `' . $svgPath . '`; Umrisse: Natural Earth, gemeinfrei.';
            if ( $design )
            {
                $out[] = '';
                $out[] = 'Ebenfalls grün sind die unbewohnten Gebiete ohne Locale mit Absicht: ' . $design . '.';
                $out[] = 'Exponential bedient diese Gebiete in jeder Sprache; lokale Konventionen gibt es keine.';
            }
            $out[] = '';
            $out[] = '<details><summary>Die ' . count( $covered ) . ' Länder und Gebiete in Grün</summary>';
            $out[] = '';
            $out[] = '| Land oder Gebiet | Code | Locales und die Übersetzung, die sie zeigen |';
        }
        else
        {
            $out[] = '**At a glance, on ' . $when . '.** A default installation (the shipped `share/locale`, `share/translations`';
            $out[] = 'and `settings/i18n.ini`, no extension) has:';
            $out[] = '';
            $out[] = '| What | How many |';
            $out[] = '|---|---:|';
            $out[] = '| Languages of the administration: translations in `share/translations` | **' . $t['total'] . '**, ' . $t['complete'] . ' of them complete |';
            $out[] = '| Locales: `share/locale/*.ini` files | **' . $l['files'] . '**: ' . ( $l['base'] - $l['test'] ) . ' languages and countries, ' . $l['test'] . ' test language, ' . $l['variants'] . ' variants |';
            $out[] = '| Known languages: the language codes of the locales (variants and the test language left out) | **' . $l['languages'] . '** |';
            $out[] = '| of them shown translated by the administration (own translation, fallback or English) | **' . $data['supported'] . '** |';
            $out[] = '| of them not translated ([list](#the-languages-not-translated-yet)) | **' . ( $l['languages'] - $data['supported'] ) . '** |';
            $out[] = '| Countries and territories of the locales | **' . count( $countries ) . '** |';
            $out[] = '| of them with the administration in their language (green on the map) | **' . $data['covered'] . '** |';
            $out[] = '| Codes of ISO 3166-1 (`settings/country.ini`) without a locale | ' . self::withoutCount( $data['without'], false ) . ' |';
            $out[] = '| Locales written right to left | **' . $l['rtl'] . '** |';
            $out[] = '';
            $out[] = '![World map: ' . $data['covered'] . ' countries and territories in green, where a shipped locale shows the administration in its language' . ( $designCount ? ', and ' . $designCount . ' uninhabited territories without a locale by design' : '' ) . '; the others in grey](' . $image . ')';
            $out[] = '';
            $out[] = 'A country is green when a locale ships for it whose language the administration shows translated: with a';
            $out[] = 'translation of its own or the first translation of its fallback chain (`i18n.ini [TranslationSettings]`,';
            $out[] = '`FallbackMode=first`, see [section 13](#13-translation-fallbacks)). English counts as translated: it is the';
            $out[] = 'language of the source texts. The map as a vector image: `' . $svgPath . '`; outlines: Natural Earth, public domain.';
            if ( $design )
            {
                $out[] = '';
                $out[] = 'The uninhabited territories without a locale by design are green too: ' . $design . '.';
                $out[] = 'Exponential serves these territories in every language; no local conventions exist.';
            }
            $out[] = '';
            $out[] = '<details><summary>The ' . count( $covered ) . ' countries and territories in green</summary>';
            $out[] = '';
            $out[] = '| Country or territory | Code | Locales and the translation they show |';
        }
        $out[] = '|---|---|---|';
        foreach ( $covered as $country )
            $out[] = '| ' . self::cell( $country['name'] ) . ' | ' . $country['code'] . ' | ' . self::localeList( $country['locales'], $german, true ) . ' |';
        $out[] = '';
        $out[] = '</details>';
        $out[] = '';
        $out[] = $german ? '<details><summary>Die ' . count( $others ) . ' Länder und Gebiete mit einer Locale, aber der Administration auf Englisch</summary>'
                         : '<details><summary>The ' . count( $others ) . ' countries and territories with a locale but the administration in English</summary>';
        $out[] = '';
        $out[] = $german ? '| Land oder Gebiet | Code | Locales |' : '| Country or territory | Code | Locales |';
        $out[] = '|---|---|---|';
        foreach ( $others as $country )
            $out[] = '| ' . self::cell( $country['name'] ) . ' | ' . $country['code'] . ' | ' . self::localeList( $country['locales'], $german ) . ' |';
        $out[] = '';
        $out[] = '</details>';
        $out[] = '';
        $without = $this->withoutLocale( $language );
        if ( $without )
        {
            $out[] = $german ? '<details><summary>Die ' . count( $without ) . ' Codes aus ISO 3166-1 ohne Locale</summary>'
                             : '<details><summary>The ' . count( $without ) . ' codes of ISO 3166-1 without a locale</summary>';
            $out[] = '';
            $out[] = $german ? '| Land oder Gebiet | Code | Warum |' : '| Country or territory | Code | Why |';
            $out[] = '|---|---|---|';
            foreach ( $without as $row )
                $out[] = '| ' . self::cell( $row['name'] ) . ' | ' . $row['code'] . ' | ' . self::cell( $row['why'] ) . ' |';
            $out[] = '';
            $out[] = '</details>';
            $out[] = '';
        }
        $out[] = $german
            ? 'Erzeugt mit `./console exp:i18n coverage` aus den mitgelieferten Dateien; `./console exp:i18n coverage --check` und der Test `expI18nCoverageMapTest` schlagen fehl, sobald Zahlen oder Karte veraltet sind.'
            : 'Made by `./console exp:i18n coverage` from the shipped files; `./console exp:i18n coverage --check` and the test `expI18nCoverageMapTest` fail when the figures or the map are out of date.';
        $out[] = self::END;
        return implode( "\n", $out );
    }

    /**
     * The generated section of the languages the administration does not show translated, with its markers.
     *
     * @param string $language the documentation language
     * @return string
     */
    public function languagesBlock( $language )
    {
        $data = $this->data();
        $german = $language === 'ger-de';
        $missing = $this->untranslatedLanguages();
        $known = count( $data['languages'] );
        $out = array();
        $out[] = self::LANGUAGES_START;
        if ( $german )
        {
            $out[] = '## Die noch nicht übersetzten Sprachen';
            $out[] = '';
            $out[] = 'Exponential kennt **' . $known . ' Sprachen**: so viele verschiedene Sprachcodes (die drei Buchstaben vor dem';
            $out[] = 'Bindestrich) haben die mitgelieferten Locales `share/locale/*.ini`, ohne Varianten und Testsprache. **' . $data['supported'] . '** davon';
            $out[] = 'zeigt die Administration übersetzt: mindestens eine ihrer Locales hat eine eigene Übersetzung, fällt auf die erste';
            $out[] = 'Übersetzung ihrer Kette zurück (`FallbackMode=first`) oder ist Englisch, die Sprache der Quelltexte. Für die';
            $out[] = 'übrigen **' . count( $missing ) . '** zeigt sie die Quelltexte auf Englisch; jede ist ein Kandidat für eine neue Übersetzung';
            $out[] = '(siehe [Abschnitt 8](#8-eine-übersetzung-der-administrationsoberfläche-hinzufügen-oder-vervollständigen)). Die Namen sind die englischen der Locale-Dateien.';
            $out[] = '';
            $out[] = '<details><summary>Die ' . count( $missing ) . ' nicht übersetzten Sprachen</summary>';
            $out[] = '';
            $out[] = '| Sprache | Eigener Name | Code | Locales (Länder) |';
        }
        else
        {
            $out[] = '## The languages not translated yet';
            $out[] = '';
            $out[] = 'Exponential knows **' . $known . ' languages**: that many different language codes (the three letters before the';
            $out[] = 'dash) are found in the shipped locales `share/locale/*.ini`, variants and the test language left out. The';
            $out[] = 'administration shows **' . $data['supported'] . '** of them translated: at least one of their locales has a translation of its';
            $out[] = 'own, falls back to the first translation of its chain (`FallbackMode=first`) or is English, the language of the';
            $out[] = 'source texts. For the other **' . count( $missing ) . '** it shows the source texts in English; each is a candidate for a new';
            $out[] = 'translation (see [section 8](#8-adding-or-completing-an-administration-translation)).';
            $out[] = '';
            $out[] = '<details><summary>The ' . count( $missing ) . ' languages not translated</summary>';
            $out[] = '';
            $out[] = '| Language | Own name | Code | Locales (countries) |';
        }
        $out[] = '|---|---|---|---|';
        $names = $this->countryNames( $language );
        foreach ( $missing as $item )
        {
            $locales = array();
            foreach ( $item['locales'] as $locale )
            {
                $region = substr( $locale['locale'], 4, 2 );
                $locales[] = '`' . $locale['locale'] . '`' . ( isset( $names[$region] ) ? ' (' . self::cell( $names[$region] ) . ')' : '' );
            }
            $out[] = '| ' . self::cell( $item['name'] ) . ' | ' . self::cell( $item['native'] ) . ' | `' . $item['code'] . '` | ' . implode( ', ', $locales ) . ' |';
        }
        $out[] = '';
        $out[] = '</details>';
        $out[] = self::LANGUAGES_END;
        return implode( "\n", $out );
    }

    /**
     * The page with its generated languages section replaced, or put before the first numbered section ("## 1.")
     * when it has none yet, else at the end.
     *
     * @param string $page
     * @param string $block languagesBlock()
     * @return string
     */
    public static function replaceLanguagesBlock( $page, $block )
    {
        $start = strpos( $page, self::LANGUAGES_START );
        $end = strpos( $page, self::LANGUAGES_END );
        if ( $start !== false && $end !== false && $end > $start )
            return substr( $page, 0, $start ) . $block . substr( $page, $end + strlen( self::LANGUAGES_END ) );
        if ( preg_match( '/^## 1\. /m', $page, $h, PREG_OFFSET_CAPTURE ) )
            return substr( $page, 0, $h[0][1] ) . $block . "\n\n" . substr( $page, $h[0][1] );
        return rtrim( $page, "\n" ) . "\n\n" . $block . "\n";
    }

    /**
     * The locales of a country as a table cell: `fre-CA` → `fre-FR`, `ger-DE`, `eng-CA` (source texts) and, with
     * $marked, `kab-DZ` (untranslated).
     *
     * @param array $locales
     * @param bool $german
     * @param bool $marked mark the locales without a translation
     * @return string
     */
    protected static function localeList( array $locales, $german, $marked = false )
    {
        $parts = array();
        foreach ( $locales as $locale )
        {
            $text = '`' . $locale['locale'] . '`';
            if ( $locale['shows'] === 'eng' )
                $text .= $german ? ' (Quelltexte)' : ' (source texts)';
            elseif ( $locale['shows'] === '' && $marked )
                $text .= $german ? ' (nicht übersetzt)' : ' (untranslated)';
            elseif ( $locale['shows'] !== '' && $locale['shows'] !== $locale['locale'] )
                $text .= ' → `' . $locale['shows'] . '`';
            $parts[] = $text;
        }
        return implode( ', ', $parts );
    }

    /**
     * @param string $text
     * @return string text safe in a Markdown table cell
     */
    protected static function cell( $text )
    {
        return str_replace( array( '|', '<', '>' ), array( '\\|', '&lt;', '&gt;' ), (string)$text );
    }

    /**
     * The complete administration translations (the share/translations codes whose interface switch coverage is 100%)
     * with the English name of their locale, sorted by name: list of hash( code, name ).
     *
     * @return array
     */
    public function completeTranslations()
    {
        $data = $this->data();
        $list = array();
        foreach ( $data['translations']['codes'] as $code )
        {
            if ( isset( $this->entries[$code] ) && $this->entries[$code]['coverage'] !== null && $this->entries[$code]['coverage'] >= 100 )
                $list[] = array( 'code' => $code, 'name' => self::cell( (string)$this->entries[$code]['english'] !== '' ? $this->entries[$code]['english'] : $code ) );
        }
        usort( $list, function ( $a, $b )
        {
            $order = strcmp( expUISwitchLanguageCatalogue::foldForSorting( $a['name'] ), expUISwitchLanguageCatalogue::foldForSorting( $b['name'] ) );
            return $order !== 0 ? $order : strcmp( $a['code'], $b['code'] );
        } );
        return $list;
    }

    /**
     * The generated "In short" paragraph with the counts and the complete translations, with its markers.
     *
     * @param string $language the documentation language
     * @return string
     */
    public function translationsBlock( $language )
    {
        $data = $this->data();
        $german = $language === 'ger-de';
        $locales = $data['locales'];
        $names = array();
        foreach ( $this->completeTranslations() as $item )
            $names[] = $item['name'] . ' (`' . $item['code'] . '`)';
        $total = $data['translations']['total'];
        $complete = $data['translations']['complete'];
        $real = $locales['base'] - $locales['test'];
        if ( $german )
        {
            $test = $locales['test'] === 1 ? 'die Testsprache Lorem Ipsum' : $locales['test'] . ' Testsprachen';
            $text = 'Kurz gefasst: **' . $locales['files'] . ' Locale-Dateien** (' . $real . ' Sprachen und Länder, ' . $test . ' und ' . $locales['variants']
                . ( $locales['variants'] === 1 ? ' Variante, ' : ' Varianten, ' ) . $locales['rtl'] . ' davon von rechts nach links geschrieben) lassen sich als Inhaltssprachen und Siteaccess-Locales wählen; es gibt **'
                . $total . ' Übersetzungen der Administrationsoberfläche**, ' . ( $complete === $total ? 'alle' : $complete . ' davon' ) . ' vollständig: '
                . implode( ', ', $names ) . '. Die Namen sind die englischen der Locale-Dateien.';
        }
        else
        {
            $test = $locales['test'] === 1 ? 'the test language Lorem Ipsum' : $locales['test'] . ' test languages';
            $text = 'In short: **' . $locales['files'] . ' locale files** (' . $real . ' languages and countries, ' . $test . ' and ' . $locales['variants']
                . ( $locales['variants'] === 1 ? ' variation, ' : ' variations, ' ) . $locales['rtl'] . ' of them written right to left) can be chosen as content languages and siteaccess locales; **'
                . $total . ' administration translations** exist, ' . ( $complete === $total ? 'all of them' : $complete . ' of them' ) . ' complete: '
                . implode( ', ', $names ) . '. The names are the English ones of the locale files.';
        }
        return self::TRANSLATIONS_START . "\n" . wordwrap( $text, 118, "\n", false ) . "\n" . self::TRANSLATIONS_END;
    }

    /**
     * The page with its generated "In short" paragraph replaced; a page without the markers is returned as it is.
     *
     * @param string $page
     * @param string $block translationsBlock()
     * @return string
     */
    public static function replaceTranslationsBlock( $page, $block )
    {
        $start = strpos( $page, self::TRANSLATIONS_START );
        $end = strpos( $page, self::TRANSLATIONS_END );
        if ( $start !== false && $end !== false && $end > $start )
            return substr( $page, 0, $start ) . $block . substr( $page, $end + strlen( self::TRANSLATIONS_END ) );
        return $page;
    }

    /**
     * The "Translation coverage" cell of a locale in the locale table: `100%`, `100% (falls back to `ara-SA`)` or `—` for a
     * locale without a translation; the German page writes `100 %` and "fällt auf ... zurück". Null for a code without a locale file.
     *
     * @param string $code
     * @param bool $german
     * @return string|null
     */
    public function coverageCell( $code, $german )
    {
        $this->data();
        if ( !isset( $this->entries[$code] ) )
            return null;
        $entry = $this->entries[$code];
        if ( $entry['coverage'] === null )
            return '—';
        $fallback = (string)$entry['fallback'];
        if ( $fallback === '' && strpos( $code, 'eng-' ) === 0 )
        {
            // English is the source language (100%), but a translation of its chain (eng-NZ to eng-AU) is named
            $resolver = $this->resolver();
            foreach ( (array)$entry['chain'] as $candidate )
            {
                if ( $resolver->hasTranslation( $candidate ) )
                {
                    $fallback = $candidate;
                    break;
                }
            }
        }
        $cell = (int)$entry['coverage'] . ( $german ? ' %' : '%' );
        if ( $fallback !== '' )
            $cell .= $german ? ' (fällt auf `' . $fallback . '` zurück)' : ' (falls back to `' . $fallback . '`)';
        return $cell;
    }

    /**
     * The page with the last column of its locale table (the rows under "| Code |" in section 3) made from the
     * catalogue; a page without that table is returned as it is.
     *
     * @param string $page
     * @param bool $german
     * @return string
     */
    public function replaceLocaleCoverage( $page, $german )
    {
        $lines = explode( "\n", $page );
        $inTable = false;
        foreach ( $lines as $i => $line )
        {
            if ( strpos( $line, '| Code | ' ) === 0 )
                $inTable = true;
            elseif ( $inTable && strpos( $line, '|' ) !== 0 )
                $inTable = false;
            elseif ( $inTable && preg_match( '/^\| `([a-z]{3}-[A-Z]{2}(?:@\w+)?)` \|/', $line, $m ) )
            {
                $cell = $this->coverageCell( $m[1], $german );
                if ( $cell !== null )
                    $lines[$i] = preg_replace( '/\| [^|]* \|$/', '| ' . str_replace( '\\', '\\\\', $cell ) . ' |', $line, 1 );
            }
        }
        return implode( "\n", $lines );
    }

    /**
     * The page with its generated part replaced, or put right below the first heading when it has none yet.
     *
     * @param string $page the page's text
     * @param string $block block()
     * @return string
     */
    public static function replaceBlock( $page, $block )
    {
        $start = preg_match( self::START_PATTERN, $page, $m, PREG_OFFSET_CAPTURE ) ? $m[0][1] : false;
        $end = strpos( $page, self::END );
        if ( $start !== false && $end !== false && $end > $start )
            return substr( $page, 0, $start ) . $block . substr( $page, $end + strlen( self::END ) );
        // below the first heading, after any front matter
        if ( preg_match( '/^# .*\n\n/m', $page, $h, PREG_OFFSET_CAPTURE ) )
        {
            $at = $h[0][1] + strlen( $h[0][0] );
            return substr( $page, 0, $at ) . $block . "\n\n" . substr( $page, $at );
        }
        return $block . "\n\n" . $page;
    }

    /**
     * The date in a page's start marker, or null.
     *
     * @param string $page
     * @return string|null YYYY-MM-DD
     */
    public static function markerDate( $page )
    {
        return preg_match( self::START_PATTERN, (string)$page, $m ) ? $m[1] : null;
    }

    /**
     * What the files should hold: path => content, for each language's map and page. A page keeps the date of its
     * marker while its generated part is otherwise unchanged; else it gets $date.
     *
     * @param string $date YYYY-MM-DD
     * @return array
     */
    public function build( $date )
    {
        $files = array();
        foreach ( self::PAGES as $language => $paths )
        {
            $files[$paths['svg']] = $this->svg( $language );
            $page = (string)@file_get_contents( $this->root . '/' . $paths['page'] );
            $page = self::replaceLanguagesBlock( $page, $this->languagesBlock( $language ) );
            $page = self::replaceTranslationsBlock( $page, $this->translationsBlock( $language ) );
            $page = $this->replaceLocaleCoverage( $page, $language === 'ger-de' );
            $marked = self::markerDate( $page );
            $content = self::replaceBlock( $page, $this->block( $language, $date ) );
            if ( $marked !== null && self::replaceBlock( $page, $this->block( $language, $marked ) ) === $page )
                $content = $page;
            $files[$paths['page']] = $content;
        }
        return $files;
    }

    /**
     * The differences between the files and build(): a list of messages, empty when everything is current. A WebP
     * copy must exist and, when Imagick can render SVG, show no area in another colour than its map rendered again
     * (rasterDifference()).
     *
     * @param string $date
     * @param bool $raster false: only check that the WebP copies exist
     * @return string[]
     */
    public function check( $date, $raster = true )
    {
        $problems = array();
        foreach ( $this->build( $date ) as $path => $content )
        {
            $current = @file_get_contents( $this->root . '/' . $path );
            if ( $current === false )
                $problems[] = "$path is missing: run ./console exp:i18n coverage";
            elseif ( $current !== $content )
                $problems[] = "$path is out of date: run ./console exp:i18n coverage";
        }
        foreach ( self::PAGES as $language => $paths )
        {
            $file = $this->root . '/' . $paths['raster'];
            if ( !is_file( $file ) )
                $problems[] = $paths['raster'] . ' is missing: run ./console exp:i18n coverage';
            elseif ( $raster && self::canRasterize() )
            {
                $difference = self::rasterDifference( $this->svg( $language ), $file );
                if ( $difference === null )
                    $problems[] = $paths['raster'] . ' does not show the current map (another size, or unreadable): run ./console exp:i18n coverage --raster';
                elseif ( $difference > 0 )
                    $problems[] = $paths['raster'] . ' does not show the current map (' . $difference . ' pixels in another colour): run ./console exp:i18n coverage --raster';
            }
        }
        return $problems;
    }

    /**
     * Writes the files that changed, and the WebP copy of each map that changed, is missing or, with $raster, every
     * one.
     *
     * @param string $date
     * @param bool $raster true: write every WebP copy again
     * @return array hash( written => paths, errors => messages )
     */
    public function write( $date, $raster = false )
    {
        $written = array();
        $errors = array();
        foreach ( $this->build( $date ) as $path => $content )
        {
            $file = $this->root . '/' . $path;
            if ( @file_get_contents( $file ) === $content )
                continue;
            if ( !is_dir( dirname( $file ) ) )
                eZDir::mkdir( dirname( $file ), false, true );
            file_put_contents( $file, $content );
            $written[] = $path;
        }
        foreach ( self::PAGES as $paths )
        {
            $file = $this->root . '/' . $paths['raster'];
            if ( !$raster && is_file( $file ) && !in_array( $paths['svg'], $written, true ) )
                continue;
            $blob = self::rasterize( (string)file_get_contents( $this->root . '/' . $paths['svg'] ), $error );
            if ( $blob === null )
            {
                $errors[] = $paths['raster'] . ': ' . $error;
                continue;
            }
            if ( @file_get_contents( $file ) !== $blob )
            {
                file_put_contents( $file, $blob );
                $written[] = $paths['raster'];
            }
        }
        return array( 'written' => $written, 'errors' => $errors );
    }

    /**
     * Whether Imagick can read SVG and write WebP here.
     *
     * @return bool
     */
    public static function canRasterize()
    {
        return class_exists( 'Imagick' ) && Imagick::queryFormats( 'SVG' ) && Imagick::queryFormats( 'WEBP' );
    }

    /**
     * The WebP copy of an SVG at its own size, lossless, transparent where the map has no land.
     *
     * @param string $svg
     * @param string|null $error set to the reason when there is no copy
     * @return string|null
     */
    public static function rasterize( $svg, &$error = null )
    {
        $error = '';
        if ( !self::canRasterize() )
        {
            $error = 'Imagick with SVG and WebP support is needed for the WebP copy';
            return null;
        }
        try
        {
            $image = new Imagick();
            $image->setBackgroundColor( new ImagickPixel( 'transparent' ) );
            $image->readImageBlob( (string)$svg );
            $image->setImageFormat( 'webp' );
            $image->setOption( 'webp:lossless', 'true' );
            $image->setOption( 'webp:method', '6' );
            $image->stripImage();
            $blob = $image->getImageBlob();
            $image->clear();
            return $blob;
        }
        catch ( Exception $e )
        {
            $error = $e->getMessage();
            return null;
        }
    }

    /**
     * How far a WebP copy is from its map rendered again: the number of pixels of the map (the legend below it left
     * out) in areas of another colour; null when the sizes differ or a picture cannot be read.
     *
     * Only areas count that differ by more than RASTER_FUZZ and are at least a disk of radius RASTER_GRAIN across:
     * a territory, an island circle or a sea drawn in another colour. Renderers of SVG (librsvg in its versions,
     * ImageMagick's own) differ along every edge and border, by their anti-aliasing and stroke widths, and in the
     * text of the legend, whose fonts are those installed on the machine; none of that says whether the copy shows
     * the current map, and all of it is thinner than the grain. The legend is left out because its glyphs depend on
     * the fonts; its text is checked word for word in the SVG.
     *
     * @param string $svg
     * @param string $webpFile
     * @return int|null
     */
    public static function rasterDifference( $svg, $webpFile )
    {
        $blob = self::rasterize( $svg );
        if ( $blob === null )
            return null;
        try
        {
            $expected = new Imagick();
            $expected->readImageBlob( $blob );
            $actual = new Imagick( $webpFile );
            $width = $expected->getImageWidth();
            $height = $expected->getImageHeight() - self::LEGEND_HEIGHT;
            if ( $width !== $actual->getImageWidth() || $expected->getImageHeight() !== $actual->getImageHeight() || $height < 1 )
                return null;
            $range = (float)Imagick::getQuantumRange()['quantumRangeLong'];
            $pictures = array();
            foreach ( array( $expected, $actual ) as $image )
            {
                // the map on white, as a page shows it; transparent sea and white sea are the same picture
                $image->setImageBackgroundColor( new ImagickPixel( 'white' ) );
                $flat = $image->mergeImageLayers( Imagick::LAYERMETHOD_FLATTEN );
                $flat->cropImage( $width, $height, 0, 0 );
                $flat->setImagePage( 0, 0, 0, 0 );
                $flat->setImageColorspace( Imagick::COLORSPACE_SRGB );
                $pictures[] = $flat;
                $image->clear();
            }
            list( $mask, $other ) = $pictures;
            $mask->compositeImage( $other, Imagick::COMPOSITE_DIFFERENCE, 0, 0 );
            $other->clear();
            // a pixel differs when one of its channels does by more than the fuzz; then the thin differences go
            $mask->thresholdImage( self::RASTER_FUZZ * $range );
            $mask->transformImageColorspace( Imagick::COLORSPACE_GRAY );
            $mask->thresholdImage( 0.01 * $range );
            $mask->morphology( Imagick::MORPHOLOGY_OPEN, 1, ImagickKernel::fromBuiltIn( Imagick::KERNEL_DISK, (string)self::RASTER_GRAIN ) );
            $pixels = 0;
            foreach ( $mask->getImageHistogram() as $pixel )
            {
                if ( $pixel->getColorValue( Imagick::COLOR_RED ) > 0.5 )
                    $pixels += $pixel->getColorCount();
            }
            $mask->clear();
            return $pixels;
        }
        catch ( Exception $e )
        {
            return null;
        }
    }
}
