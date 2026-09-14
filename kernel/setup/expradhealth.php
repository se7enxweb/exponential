<?php
/**
 * What is configured on this installation and cannot work.
 *
 * The survey beside this says what can be extended. This says what already has
 * been, wrongly. They are the same walk over the same files, asked a different
 * question: not "where could something go" but "what is here that points at
 * nothing".
 *
 * Every check answers something that is true or false about this installation
 * rather than about eZ in general, and every finding names the file and the
 * line somebody would have to open. A finding with nothing actionable in it is
 * worse than no finding, because it teaches whoever reads the page to stop
 * reading it.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

require_once 'kernel/setup/expradsurvey.php';

class expRADHealth
{
    /**
     * How much a finding matters.
     */
    const BROKEN = 'broken';
    const ODD    = 'odd';
    const NOTE   = 'note';

    /**
     * Everything the cheap checks found, worst first.
     *
     * Cheap meaning it reads files and settings and loads nothing, so it is
     * safe to run on every page view. The class loader check is not cheap and
     * is not here; it is asked for separately.
     *
     * @return array of array( severity, check, what, where, fix )
     */
    public static function findings()
    {
        $findings = array();

        foreach ( array( 'settingsNamingNothing', 'extensionsNotThere', 'designsWithoutDesigns',
                         'translationsWithoutTranslations', 'modulesNotThere', 'viewsWithoutScripts',
                         'datatypesNotThere', 'repositoriesNotThere', 'iconThemesNotThere',
                         'overridesWithoutTemplates', 'kernelOverridesOfNothing',
                         'contractsNobodyImplements' ) as $check )
            foreach ( self::$check() as $finding )
                $findings[] = $finding;

        usort( $findings, array( __CLASS__, 'compare' ) );

        return $findings;
    }

    /**
     * How many of each severity.
     *
     * @param array|null $findings
     * @return array
     */
    public static function counts( array $findings = null )
    {
        if ( $findings === null )
            $findings = self::findings();

        $counts = array( self::BROKEN => 0, self::ODD => 0, self::NOTE => 0, 'total' => count( $findings ) );

        foreach ( $findings as $finding )
            $counts[$finding['severity']]++;

        return $counts;
    }

    /**
     * Worst first, then by what the finding is about.
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    public static function compare( $a, $b )
    {
        $order = array( self::BROKEN => 0, self::ODD => 1, self::NOTE => 2 );

        if ( $order[$a['severity']] !== $order[$b['severity']] )
            return $order[$a['severity']] < $order[$b['severity']] ? -1 : 1;

        return strcmp( $a['check'] . $a['what'], $b['check'] . $b['what'] );
    }

    /**
     * One finding.
     *
     * @param string $severity
     * @param string $check
     * @param string $what
     * @param string $where
     * @param string $fix
     * @return array
     */
    protected static function finding( $severity, $check, $what, $where, $fix )
    {
        return array( 'severity' => $severity, 'check' => $check,
                      'what' => $what, 'where' => $where, 'fix' => $fix );
    }

    // ── The checks ───────────────────────────────────────────────────────────

    /**
     * A setting naming something class shaped that nothing declares.
     *
     * @return array
     */
    public static function settingsNamingNothing()
    {
        $findings = array();

        foreach ( expRADSurvey::survey()['settings'] as $setting )
        {
            if ( $setting['shape'] !== 'unknown' )
                continue;

            $findings[] = self::finding( self::ODD, 'Setting names no class',
                $setting['value'],
                $setting['ini'] . ' [' . $setting['section'] . '] ' . $setting['variable'],
                'Either it is an alias something else resolves - several of these settings take one - or the class was renamed and this was not. Nothing declares a class of that name.' );
        }

        return $findings;
    }

    /**
     * An extension switched on that is not on disk.
     *
     * @return array
     */
    public static function extensionsNotThere()
    {
        $findings = array();
        $ini      = eZINI::instance( 'site.ini' );

        foreach ( array( 'ActiveExtensions', 'ActiveAccessExtensions' ) as $variable )
            foreach ( self::listOf( $ini, 'ExtensionSettings', $variable ) as $name )
                if ( eZExtension::extensionPath( $name ) === false )
                    $findings[] = self::finding( self::BROKEN, 'Extension switched on and not there',
                        $name,
                        'site.ini [ExtensionSettings] ' . $variable . '[]',
                        'Nothing in it can load. Either install it, or take the line out - an extension that is listed and missing is reported on every request.' );

        return $findings;
    }

    /**
     * A design extension with no design directory.
     *
     * @return array
     */
    public static function designsWithoutDesigns()
    {
        $findings = array();

        foreach ( self::listOf( eZINI::instance( 'design.ini' ), 'ExtensionSettings', 'DesignExtensions' ) as $name )
        {
            $path = eZExtension::extensionPath( $name );

            if ( $path !== false && is_dir( $path . '/design' ) )
                continue;

            $findings[] = self::finding( self::ODD, 'Design extension with no design',
                $name,
                'design.ini [ExtensionSettings] DesignExtensions[]',
                $path === false
                ? 'The extension is not on disk at all.'
                : 'There is no ' . $name . '/design directory, so listing it adds nothing to the design chain. Harmless, and it means a template somebody expects to be found is not.' );
        }

        return $findings;
    }

    /**
     * A translation extension with no translations.
     *
     * @return array
     */
    public static function translationsWithoutTranslations()
    {
        $findings = array();

        foreach ( self::listOf( eZINI::instance( 'site.ini' ), 'RegionalSettings', 'TranslationExtensions' ) as $name )
        {
            $path = eZExtension::extensionPath( $name );

            if ( $path !== false && is_dir( $path . '/translations' ) )
                continue;

            $findings[] = self::finding( self::ODD, 'Translation extension with no translations',
                $name,
                'site.ini [RegionalSettings] TranslationExtensions[]',
                'There is no translations directory in it, so every string falls back to the source language and nothing says why.' );
        }

        return $findings;
    }

    /**
     * A module listed and not found.
     *
     * @return array
     */
    public static function modulesNotThere()
    {
        $known = array();

        foreach ( expRADSurvey::survey()['modules'] as $module )
            $known[$module['name']] = true;

        $findings = array();

        foreach ( self::listOf( eZINI::instance( 'module.ini' ), 'ModuleSettings', 'ModuleList' ) as $name )
            if ( !isset( $known[$name] ) )
                $findings[] = self::finding( self::BROKEN, 'Module listed and not found',
                    $name,
                    'module.ini [ModuleSettings] ModuleList[]',
                    'Every address beginning /' . $name . '/ answers with a module not found error. Usually the extension carrying it is not in ExtensionRepositories[], or is not installed.' );

        return $findings;
    }

    /**
     * A view whose script is not beside it.
     *
     * @return array
     */
    public static function viewsWithoutScripts()
    {
        $findings = array();

        foreach ( expRADSurvey::survey()['modules'] as $module )
            foreach ( $module['views'] as $view )
            {
                if ( $view['script'] === '' || is_file( $module['path'] . '/' . $view['script'] ) )
                    continue;

                $findings[] = self::finding( self::BROKEN, 'View with no script',
                    $module['name'] . '/' . $view['name'],
                    $module['path'] . '/' . $view['script'],
                    'The module declares the view and the file it names is not there, so the address exists and answers with nothing.' );
            }

        return $findings;
    }

    /**
     * A datatype offered in the class editor whose file is not there.
     *
     * @return array
     */
    public static function datatypesNotThere()
    {
        $ini         = eZINI::instance( 'content.ini' );
        $directories = self::listOf( $ini, 'DataTypeSettings', 'RepositoryDirectories' );

        foreach ( self::listOf( $ini, 'DataTypeSettings', 'ExtensionDirectories' ) as $name )
        {
            $path = eZExtension::extensionPath( $name );

            if ( $path !== false )
                $directories[] = $path . '/datatypes';
        }

        $findings = array();

        foreach ( self::listOf( $ini, 'DataTypeSettings', 'AvailableDataTypes' ) as $type )
        {
            foreach ( $directories as $directory )
                if ( is_file( rtrim( $directory, '/' ) . '/' . $type . '/' . $type . 'type.php' ) )
                    continue 2;

            $findings[] = self::finding( self::BROKEN, 'Datatype offered and not found',
                $type,
                'content.ini [DataTypeSettings] AvailableDataTypes[]',
                'It is offered in the class editor and there is no ' . $type . '/' . $type . 'type.php in any directory searched. An attribute of this type cannot be added, and an existing one holds a value nothing can read.' );
        }

        return $findings;
    }

    /**
     * A directory the kernel is told to search that is not there.
     *
     * @return array
     */
    public static function repositoriesNotThere()
    {
        $findings = array();

        foreach ( expRADSurvey::survey()['repositories'] as $entry )
        {
            // Only the ones that name a path. The rest name an extension, and
            // that is a different check.
            if ( !preg_match( '/(RepositoryDirectories|AutoloadPathList)/i', $entry['variable'] ) )
                continue;

            foreach ( $entry['values'] as $value )
            {
                if ( $value === '' || is_dir( rtrim( $value, '/' ) ) )
                    continue;

                $findings[] = self::finding( self::ODD, 'Directory searched and not there',
                    $value,
                    $entry['ini'] . ' [' . $entry['section'] . '] ' . $entry['variable'],
                    'Searching it costs nothing and finds nothing. Usually left over from a version that had it.' );
            }
        }

        return $findings;
    }

    /**
     * An icon theme named and not present anywhere it is looked for.
     *
     * @return array
     */
    public static function iconThemesNotThere()
    {
        $ini    = eZINI::instance( 'icon.ini' );
        $themes = array();

        foreach ( array( 'Theme', 'StandardTheme' ) as $variable )
            if ( $ini->hasVariable( 'IconSettings', $variable ) )
                $themes[] = (string) $ini->variable( 'IconSettings', $variable );

        foreach ( self::listOf( $ini, 'IconSettings', 'AdditionalThemeList' ) as $theme )
            $themes[] = $theme;

        $roots = array( rtrim( (string) $ini->variable( 'IconSettings', 'Repository' ), '/' ) );

        foreach ( self::listOf( $ini, 'ExtensionSettings', 'IconExtensions' ) as $name )
        {
            $path = eZExtension::extensionPath( $name );

            if ( $path !== false )
                $roots[] = $path . '/icons';
        }

        $findings = array();

        foreach ( array_unique( array_filter( $themes, 'strlen' ) ) as $theme )
        {
            foreach ( $roots as $root )
                if ( $root !== '' && is_dir( $root . '/' . $theme ) )
                    continue 2;

            $findings[] = self::finding( self::ODD, 'Icon theme not found',
                $theme,
                'icon.ini [IconSettings]',
                'Not in share/icons and not in any extension listed in IconExtensions[]. Icons fall through to the standard theme, or draw the default.' );
        }

        return $findings;
    }

    /**
     * An override naming a template that is not in any design.
     *
     * @return array
     */
    public static function overridesWithoutTemplates()
    {
        $findings = array();
        $bases    = self::designBases();

        foreach ( expRADSurvey::survey()['overrides'] as $override )
        {
            if ( $override['match'] === '' )
                continue;

            foreach ( $bases as $base )
                if ( is_file( $base . '/override/templates/' . $override['match'] )
                     || is_file( $base . '/templates/' . $override['match'] ) )
                    continue 2;

            $findings[] = self::finding( self::BROKEN, 'Override with no template',
                $override['name'] . ' \xe2\x86\x92 ' . $override['match'],
                $override['from'],
                'The override matches and then finds nothing to draw with. Whatever it was meant to replace is drawn by the default instead, silently.' );
        }

        return $findings;
    }

    /**
     * A kernel override replacing a class the kernel does not have.
     *
     * @return array
     */
    public static function kernelOverridesOfNothing()
    {
        $findings = array();

        foreach ( expRADSurvey::survey()['replaced'] as $entry )
        {
            if ( $entry['kernel'] !== '' )
                continue;

            $findings[] = self::finding( self::ODD, 'Kernel override of nothing',
                $entry['class'],
                $entry['path'],
                'It is in the override autoload map and the kernel has no class of that name, so it overrides nothing. Either the kernel class was renamed, or this belongs in the ordinary autoload path instead.' );
        }

        return $findings;
    }

    /**
     * A contract nothing implements.
     *
     * Not a fault. It is the list of extension points nobody here has taken up,
     * which is a different and quite useful thing to know.
     *
     * @return array
     */
    public static function contractsNobodyImplements()
    {
        $findings = array();

        foreach ( expRADSurvey::survey()['contracts'] as $contract )
        {
            if ( count( $contract['implementations'] ) )
                continue;

            $findings[] = self::finding( self::NOTE, 'Nothing implements it',
                $contract['name'] . ' (' . $contract['methods'] . ' methods)',
                $contract['source'],
                'An extension point nobody here has taken up. Nothing is wrong with it; it is simply unused.' );
        }

        return $findings;
    }

    // ── The class loader check, which is not cheap ───────────────────────────

    /**
     * Every class this installation declares that php refuses to load.
     *
     * Not run with the others: it loads several hundred classes in child
     * processes and takes seconds rather than milliseconds. It is also the most
     * valuable of them, because a class that cannot be loaded ends the request
     * that touches it, and stays invisible until something does.
     *
     * Done by the same script the command line uses, so the two cannot drift.
     *
     * @param bool $withTests
     * @return array with keys ran, checked and findings
     */
    public static function loaderFindings( $withTests = false )
    {
        // The library, not the command. Requiring the command would run it:
        // it builds an eZCLI and an eZScript of its own and starts them, which
        // is right in a terminal and wrong inside a request.
        $library = 'kernel/setup/expclassloadcheck.php';

        if ( !is_file( $library ) )
            return array( 'ran' => false, 'checked' => 0, 'findings' => array(),
                          'message' => 'The class loader check is not installed.' );

        require_once $library;

        if ( !is_file( eZCheckClasses::COMMAND ) )
            return array( 'ran' => false, 'checked' => 0, 'findings' => array(),
                          'message' => eZCheckClasses::COMMAND . ' is not there, and it is both the command and the child process.' );

        $classes = eZCheckClasses::classNames( false, $withTests );
        $found   = eZCheckClasses::check( $classes );

        $findings = array();

        foreach ( $found['bad'] as $class )
        {
            $reason = eZCheckClasses::reason( $class );

            $findings[] = self::finding( self::BROKEN, 'Class php refuses to load',
                $class, self::fileOf( $class ),
                $reason['kind'] . ' ' . $reason['message'] );
        }

        return array( 'ran' => true, 'checked' => $found['checked'],
                      'findings' => $findings, 'message' => '' );
    }

    // ── Odds and ends ────────────────────────────────────────────────────────

    /**
     * A setting as a list of non-empty strings.
     *
     * @param eZINI $ini
     * @param string $section
     * @param string $variable
     * @return array of string
     */
    protected static function listOf( $ini, $section, $variable )
    {
        if ( !is_object( $ini ) || !$ini->hasVariable( $section, $variable ) )
            return array();

        $values = $ini->variable( $section, $variable );

        if ( !is_array( $values ) )
            $values = array( $values );

        return array_values( array_filter( array_map( 'strval', $values ), 'strlen' ) );
    }

    /**
     * Every design directory a template could be found in.
     *
     * @return array of string
     */
    protected static function designBases()
    {
        $bases = array();

        foreach ( (array) glob( 'design/*' ) as $path )
            if ( is_dir( $path ) )
                $bases[] = $path;

        foreach ( (array) glob( 'extension/*/design/*' ) as $path )
            if ( is_dir( $path ) )
                $bases[] = $path;

        return $bases;
    }

    /**
     * Where a class is declared.
     *
     * @param string $class
     * @return string
     */
    protected static function fileOf( $class )
    {
        $path = expRADSurvey::fileOf( $class );

        return $path !== '' ? $path : '(nothing declares it)';
    }
}

?>
