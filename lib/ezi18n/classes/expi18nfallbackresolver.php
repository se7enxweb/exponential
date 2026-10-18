<?php
/**
 * File containing the expI18nFallbackResolver class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 * @subpackage i18n
 */

/**
 * Which translations a locale falls back to, in order: the one source of truth for the translator (eZTSTranslator),
 * the interface language choice (expUISwitchLanguageCatalogue), exp:i18n and Setup > Interface languages.
 *
 * Each step of the chain is decided by the first rule that answers (expI18nFallbackRuleInterface):
 *  1. i18n.ini [TranslationSettings] FallbackLanguages[] (expI18nFallbackConfiguredRule): what is configured wins;
 *  2. the classes of FallbackRules[], in their order (extensions add theirs there);
 *  3. with AutomaticLanguageFallback=enabled, the most complete translation of the same language
 *     (expI18nFallbackSameLanguageRule).
 * The resolver then asks again for the answer, so FallbackLanguages[esl-AR]=esl-MX and FallbackLanguages[esl-MX]=esl-ES
 * make the chain esl-AR -> esl-MX -> esl-ES. It ends at a locale no rule answers for, at the source texts
 * ('untranslated', also written eng-GB), before a locale already in it (a cycle) or after FallbackMaxDepth steps.
 *
 * The translator tries the chain for each translation root on its own (the kernel's share/translations, then each
 * extension's): esl-MX has a kernel translation of its own and takes esl-ES only in the extensions without esl-MX.
 * FallbackMode=first (the default) loads the first file of the chain a root has; merge loads all of them, the most
 * general first, so the messages a translation lacks are taken from the next one in the chain.
 *
 * @package lib
 * @subpackage i18n
 */
class expI18nFallbackResolver
{
    /** the source texts: the pseudo-translation 'untranslated', which ends every chain */
    const SOURCE = 'untranslated';

    /** a locale code, or a variant of one */
    const LOCALE_PATTERN = '/^[a-z]{3}-[A-Z]{2}(@[a-z0-9]{1,16})?$/';

    /** the rule names of the two built-in rules */
    const RULE_CONFIGURED = 'configured';
    const RULE_AUTOMATIC = 'automatic';

    /** the highest FallbackMaxDepth taken */
    const MAX_DEPTH_LIMIT = 16;

    /** @var array signature => resolver, for the life of the process */
    private static $instances = array();

    /** @var array locale => translation, as FallbackLanguages[] */
    protected $configured;

    /** @var array name => expI18nFallbackRuleInterface, in the order they are asked */
    protected $rules = array();

    /** @var array problems found while building the rules (FallbackRules[] that are no rule) */
    protected $ruleProblems = array();

    /** @var array */
    protected $options;

    /** @var array locale => step|false */
    protected $steps = array();

    /** @var array locale => explain() */
    protected $explained = array();

    /** @var array|null codes with a translation in the repository */
    protected $translations;

    /**
     * @param array $configured locale => translation, as FallbackLanguages[]
     * @param array $rules the extra rules, asked after the configured ones: objects implementing
     *        expI18nFallbackRuleInterface, or class names of such (a name that is not one is a problem, see problems())
     * @param array $options repository (the kernel's translations, default share/translations), locale_directory
     *        (share/locale), automatic (bool, default true), automatic_exclude (languages or locales, default eng),
     *        max_depth (default 4), mode (first or merge, default first)
     */
    public function __construct( array $configured = array(), array $rules = array(), array $options = array() )
    {
        $this->configured = array();
        foreach ( $configured as $locale => $target )
        {
            if ( is_string( $locale ) && is_scalar( $target ) )
                $this->configured[$locale] = trim( (string)$target );
        }
        $this->options = array_merge( array( 'repository' => 'share/translations', 'locale_directory' => 'share/locale',
                                             'automatic' => true, 'automatic_exclude' => array( 'eng' ),
                                             'max_depth' => 4, 'mode' => 'first' ), $options );
        $this->options['max_depth'] = max( 1, min( self::MAX_DEPTH_LIMIT, (int)$this->options['max_depth'] ) );
        $this->options['mode'] = $this->options['mode'] === 'merge' ? 'merge' : 'first';
        $this->options['automatic_exclude'] = array_values( array_filter( array_map( 'trim', (array)$this->options['automatic_exclude'] ), 'strlen' ) );

        $this->rules[self::RULE_CONFIGURED] = new expI18nFallbackConfiguredRule();
        foreach ( $rules as $rule )
        {
            if ( is_string( $rule ) )
            {
                $class = trim( $rule );
                if ( $class === '' )
                    continue;
                if ( !class_exists( $class ) || !is_subclass_of( $class, 'expI18nFallbackRuleInterface' ) )
                {
                    $this->ruleProblems[] = self::problem( 'error', '', 'invalid-rule', $class );
                    continue;
                }
                $rule = new $class();
            }
            if ( !$rule instanceof expI18nFallbackRuleInterface )
            {
                $this->ruleProblems[] = self::problem( 'error', '', 'invalid-rule', is_object( $rule ) ? get_class( $rule ) : gettype( $rule ) );
                continue;
            }
            $name = get_class( $rule );
            if ( !isset( $this->rules[$name] ) && $name !== 'expI18nFallbackConfiguredRule' && $name !== 'expI18nFallbackSameLanguageRule' )
                $this->rules[$name] = $rule;
        }
        if ( $this->options['automatic'] )
            $this->rules[self::RULE_AUTOMATIC] = new expI18nFallbackSameLanguageRule();
    }

    /**
     * The resolver of the installation's settings (i18n.ini [TranslationSettings]), shared in the process while the
     * settings stay the same.
     *
     * @return expI18nFallbackResolver
     */
    public static function instance()
    {
        $settings = self::settings();
        $key = md5( serialize( $settings ) );
        if ( !isset( self::$instances[$key] ) )
        {
            if ( count( self::$instances ) > 8 )
                self::$instances = array();
            self::$instances[$key] = new self( $settings['configured'], $settings['rules'], $settings['options'] );
        }
        return self::$instances[$key];
    }

    /**
     * Forgets the shared resolvers (tests, and a translation added while a persistent worker runs).
     */
    public static function resetInstances()
    {
        self::$instances = array();
    }

    /**
     * The settings of i18n.ini [TranslationSettings] the resolver is built from: configured, rules, options.
     *
     * @return array
     */
    public static function settings()
    {
        $settings = array( 'configured' => array(), 'rules' => array(), 'options' => array() );
        if ( !class_exists( 'eZINI' ) )
            return $settings;
        $ini = eZINI::instance( 'i18n.ini' );
        $value = function ( $name, $default ) use ( $ini )
        {
            return $ini->hasVariable( 'TranslationSettings', $name ) ? $ini->variable( 'TranslationSettings', $name ) : $default;
        };
        $settings['configured'] = (array)$value( 'FallbackLanguages', array() );
        $settings['rules'] = array_values( (array)$value( 'FallbackRules', array() ) );
        $settings['options'] = array(
            'automatic' => $value( 'AutomaticLanguageFallback', 'enabled' ) === 'enabled',
            'automatic_exclude' => (array)$value( 'AutomaticFallbackExclude', array( 'eng' ) ),
            'max_depth' => (int)$value( 'FallbackMaxDepth', 4 ),
            'mode' => (string)$value( 'FallbackMode', 'first' ),
        );
        $site = eZINI::instance();
        if ( $site->hasVariable( 'RegionalSettings', 'TranslationRepository' ) )
            $settings['options']['repository'] = rtrim( (string)$site->variable( 'RegionalSettings', 'TranslationRepository' ), '/' );
        return $settings;
    }

    /**
     * What the resolver's answers depend on, besides which translations exist: for the keys of caches.
     *
     * @return string
     */
    public function signature()
    {
        $configured = $this->configured;
        ksort( $configured, SORT_STRING );
        return md5( json_encode( array( $configured, array_keys( $this->rules ), $this->options ) ) );
    }

    /**
     * FallbackLanguages[] as given, locale => translation.
     *
     * @return array
     */
    public function configured()
    {
        return $this->configured;
    }

    /**
     * An option of the constructor.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function option( $name, $default = null )
    {
        return array_key_exists( $name, $this->options ) ? $this->options[$name] : $default;
    }

    /**
     * first or merge (FallbackMode).
     *
     * @return string
     */
    public function mode()
    {
        return $this->options['mode'];
    }

    /**
     * The names of the rules in the order they are asked: configured, the classes of FallbackRules[], automatic.
     *
     * @return string[]
     */
    public function ruleNames()
    {
        return array_keys( $this->rules );
    }

    /**
     * The kernel translation file of a locale (it need not exist).
     *
     * @param string $locale
     * @return string
     */
    public function translationFile( $locale )
    {
        return rtrim( (string)$this->options['repository'], '/' ) . '/' . $locale . '/translation.ts';
    }

    /**
     * Whether the kernel's translation repository has a translation of the locale.
     *
     * @param string $locale
     * @return bool
     */
    public function hasTranslation( $locale )
    {
        return in_array( (string)$locale, $this->translations(), true );
    }

    /**
     * The codes with a translation in the kernel's translation repository ('untranslated' left out), sorted.
     *
     * @return string[]
     */
    public function translations()
    {
        if ( $this->translations === null )
        {
            $this->translations = array();
            foreach ( (array)glob( $this->translationFile( '*' ) ) as $file )
            {
                $code = basename( dirname( $file ) );
                if ( preg_match( self::LOCALE_PATTERN, $code ) )
                    $this->translations[] = $code;
            }
            sort( $this->translations, SORT_STRING );
        }
        return $this->translations;
    }

    /**
     * Whether the installation has a locale file (share/locale/<code>.ini) for the code.
     *
     * @param string $locale
     * @return bool
     */
    public function localeExists( $locale )
    {
        return preg_match( self::LOCALE_PATTERN, (string)$locale )
               && is_file( rtrim( (string)$this->options['locale_directory'], '/' ) . '/' . $locale . '.ini' );
    }

    /**
     * One step: the translation the first rule that answers sends $locale to, and that rule's name; null when no
     * rule answers.
     *
     * @param string $locale
     * @return array|null hash( target, rule )
     */
    public function step( $locale )
    {
        $locale = (string)$locale;
        if ( array_key_exists( $locale, $this->steps ) )
            return $this->steps[$locale] === false ? null : $this->steps[$locale];
        $this->steps[$locale] = false;
        if ( $locale === self::SOURCE )
            return null;
        foreach ( $this->rules as $name => $rule )
        {
            try
            {
                $target = $rule->fallbackFor( $locale, $this );
            }
            catch ( Throwable $e )
            {
                // a broken rule must not stop the translation: it has no say
                if ( class_exists( 'eZDebug' ) )
                    eZDebug::writeWarning( "Fallback rule $name failed for $locale: " . $e->getMessage(), __METHOD__ );
                continue;
            }
            if ( !is_string( $target ) || trim( $target ) === '' )
                continue;
            $target = trim( $target );
            if ( $target === 'eng-GB' )
                $target = self::SOURCE;
            if ( $target === $locale )
                continue;
            return $this->steps[$locale] = array( 'target' => $target, 'rule' => $name );
        }
        return null;
    }

    /**
     * The translations $locale falls back to, in order, without $locale itself; may end with 'untranslated'.
     *
     * @param string $locale
     * @return string[]
     */
    public function chain( $locale )
    {
        $explained = $this->explain( $locale );
        $chain = array();
        foreach ( $explained['steps'] as $step )
            $chain[] = $step['target'];
        return $chain;
    }

    /**
     * The chain of $locale with the rule of each step, and why it ended.
     *
     * @param string $locale
     * @return array hash( locale, steps: list of hash( target, rule ), problem: '' or cycle or depth, problem_target )
     */
    public function explain( $locale )
    {
        $locale = (string)$locale;
        if ( isset( $this->explained[$locale] ) )
            return $this->explained[$locale];
        $result = array( 'locale' => $locale, 'steps' => array(), 'problem' => '', 'problem_target' => '' );
        $seen = array( $locale => true );
        $current = $locale;
        while ( ( $step = $this->step( $current ) ) !== null )
        {
            if ( isset( $seen[$step['target']] ) )
            {
                $result['problem'] = 'cycle';
                $result['problem_target'] = $step['target'];
                break;
            }
            if ( count( $result['steps'] ) >= $this->options['max_depth'] )
            {
                $result['problem'] = 'depth';
                $result['problem_target'] = $step['target'];
                break;
            }
            $result['steps'][] = $step;
            if ( $step['target'] === self::SOURCE || !preg_match( self::LOCALE_PATTERN, $step['target'] ) )
                break;
            $seen[$step['target']] = true;
            $current = $step['target'];
        }
        return $this->explained[$locale] = $result;
    }

    /**
     * The problems of the fallbacks of these locales (default: every configured locale), and of FallbackRules[]:
     * list of hash( level, locale, code, detail ), level error (cycle, depth, invalid-target, missing-target,
     * invalid-rule) or warning (unknown-locale: a configured locale without a locale file; no-translation: a target
     * with neither a translation nor a fallback of its own, so the step gives the source texts).
     *
     * @param string[]|null $locales
     * @return array
     */
    public function problems( ?array $locales = null )
    {
        $problems = $this->ruleProblems;
        if ( $locales === null )
            $locales = array_keys( $this->configured );
        sort( $locales, SORT_STRING );
        foreach ( $locales as $locale )
        {
            if ( isset( $this->configured[$locale] ) && !$this->localeExists( $locale ) )
                $problems[] = self::problem( 'warning', $locale, 'unknown-locale', $locale );
            $explained = $this->explain( $locale );
            if ( $explained['problem'] !== '' )
                $problems[] = self::problem( 'error', $locale, $explained['problem'], $explained['problem_target'] );
            foreach ( $explained['steps'] as $step )
            {
                $target = $step['target'];
                if ( $target === self::SOURCE )
                    continue;
                if ( !preg_match( self::LOCALE_PATTERN, $target ) )
                    $problems[] = self::problem( 'error', $locale, 'invalid-target', $target );
                elseif ( !$this->localeExists( $target ) && !$this->hasTranslation( $target ) )
                    $problems[] = self::problem( 'error', $locale, 'missing-target', $target );
            }
            $last = end( $explained['steps'] );
            if ( $last && $explained['problem'] === '' && $last['target'] !== self::SOURCE && preg_match( self::LOCALE_PATTERN, $last['target'] )
                 && $this->localeExists( $last['target'] ) && !$this->hasTranslation( $last['target'] )
                 && !$this->anyTranslation( $explained['steps'] ) )
                $problems[] = self::problem( 'warning', $locale, 'no-translation', $last['target'] );
        }
        return $problems;
    }

    /**
     * Whether a step of a chain is a kernel translation.
     *
     * @param array $steps
     * @return bool
     */
    protected function anyTranslation( array $steps )
    {
        foreach ( $steps as $step )
        {
            if ( $this->hasTranslation( $step['target'] ) )
                return true;
        }
        return false;
    }

    protected static function problem( $level, $locale, $code, $detail )
    {
        return array( 'level' => $level, 'locale' => (string)$locale, 'code' => $code, 'detail' => (string)$detail );
    }
}
