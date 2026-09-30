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


if ( !class_exists( 'expRADHealth', false ) ) {
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
    public static function counts( ?array $findings = null )
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
    protected static function finding( $severity, $check, $what, $where, $means, $fix = array() )
    {
        // 'check' stays the English name: the key of a kind of finding is made
        // from it, and a link to one kind must not change with the language.
        // 'label' is what the page shows.
        return array( 'severity' => $severity, 'check' => $check,
                      'label' => self::checkLabel( $check ),
                      'what' => $what, 'where' => $where,
                      'means' => $means,
                      // What to actually do, as steps. Reporting a fault and
                      // leaving somebody to work out the remedy is half a job,
                      // and the half that is easy.
                      'fix' => (array) $fix,
                      'key' => self::keyOf( $check ) );
    }

    /**
     * A short word for a kind of finding, for linking straight to it.
     *
     * @param string $check
     * @return string
     */
    public static function keyOf( $check )
    {
        return strtolower( preg_replace( '/[^a-z0-9]+/i', '-', trim( (string) $check ) ) );
    }

    /**
     * The name of a kind of finding in the current language.
     *
     * Built on every call rather than kept in a static, so a process that
     * serves requests in several languages gives each its own.
     *
     * @param string $check the English name finding() is given
     * @return string
     */
    public static function checkLabel( $check )
    {
        $labels = array(
            'Setting names no class'                     => ezpI18n::tr( 'kernel/setup/rad', 'Setting names no class' ),
            'Extension switched on and not there'        => ezpI18n::tr( 'kernel/setup/rad', 'Extension switched on and not there' ),
            'Design extension with no design'            => ezpI18n::tr( 'kernel/setup/rad', 'Design extension with no design' ),
            'Translation extension with no translations' => ezpI18n::tr( 'kernel/setup/rad', 'Translation extension with no translations' ),
            'Module listed and not found'                => ezpI18n::tr( 'kernel/setup/rad', 'Module listed and not found' ),
            'View with no script'                        => ezpI18n::tr( 'kernel/setup/rad', 'View with no script' ),
            'Datatype offered and not found'             => ezpI18n::tr( 'kernel/setup/rad', 'Datatype offered and not found' ),
            'Directory searched and not there'           => ezpI18n::tr( 'kernel/setup/rad', 'Directory searched and not there' ),
            'Icon theme not found'                       => ezpI18n::tr( 'kernel/setup/rad', 'Icon theme not found' ),
            'Override with no template'                  => ezpI18n::tr( 'kernel/setup/rad', 'Override with no template' ),
            'Kernel override of nothing'                 => ezpI18n::tr( 'kernel/setup/rad', 'Kernel override of nothing' ),
            'Nothing implements it'                      => ezpI18n::tr( 'kernel/setup/rad', 'Nothing implements it' ),
            'Class php refuses to load'                  => ezpI18n::tr( 'kernel/setup/rad', 'Class php refuses to load' ) );

        return isset( $labels[$check] ) ? $labels[$check] : $check;
    }

    /**
     * The kind of a class loader fault in the current language.
     *
     * The class loader check is shared with the command line and says what
     * kind of fault it found in English; this is where the page translates it.
     *
     * @param string $kind as eZCheckClasses::kindOf gives it
     * @return string
     */
    public static function loaderKindLabel( $kind )
    {
        $labels = array(
            'Incompatible declaration: this class has to be changed.'
                => ezpI18n::tr( 'kernel/setup/rad', 'Incompatible declaration: this class has to be changed.' ),
            'A parent or interface is missing: usually an extension that needs another one.'
                => ezpI18n::tr( 'kernel/setup/rad', 'A parent or interface is missing: usually an extension that needs another one.' ),
            'Called statically at load time and not declared static: a php 8 incompatibility.'
                => ezpI18n::tr( 'kernel/setup/rad', 'Called statically at load time and not declared static: a php 8 incompatibility.' ),
            'Declared twice: two files claim the same name, or one is included by hand as well as autoloaded.'
                => ezpI18n::tr( 'kernel/setup/rad', 'Declared twice: two files claim the same name, or one is included by hand as well as autoloaded.' ),
            'php refused it and said nothing this script could read.'
                => ezpI18n::tr( 'kernel/setup/rad', 'php refused it and said nothing this script could read.' ),
            'php refused it.'
                => ezpI18n::tr( 'kernel/setup/rad', 'php refused it.' ) );

        return isset( $labels[$kind] ) ? $labels[$kind] : $kind;
    }

    /**
     * Every kind of finding there is, whether or not any were found.
     *
     * The page links to these by key, so it needs to know the kinds without
     * having to have found one.
     *
     * @return array
     */
    public static function kinds()
    {
        $kinds = array();

        foreach ( self::findings() as $finding )
            if ( !isset( $kinds[$finding['key']] ) )
                $kinds[$finding['key']] = array( 'key' => $finding['key'],
                                                 'check' => $finding['check'],
                                                 'label' => $finding['label'],
                                                 'severity' => $finding['severity'],
                                                 'count' => 0 );

        foreach ( self::findings() as $finding )
            $kinds[$finding['key']]['count']++;

        return $kinds;
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
                ezpI18n::tr( 'kernel/setup/rad', 'Nothing on this installation declares a class of that name, and the value has a capital in it so it is shaped like one rather than like an alias.' ),
                array( ezpI18n::tr( 'kernel/setup/rad', 'Check first whether the setting takes an alias. Several do, and an alias that happens to be capitalised is not a fault - eZECB is the alias for eZECBHandler and is perfectly correct.' ),
                       ezpI18n::tr( 'kernel/setup/rad', 'If it is meant to be a class, find out whether the extension that declares it is installed and in ActiveExtensions[].' ),
                       ezpI18n::tr( 'kernel/setup/rad', 'If it is installed, the autoload map may be stale: php bin/php/ezpgenerateautoloads.php' ),
                       ezpI18n::tr( 'kernel/setup/rad', 'If the class was renamed, change the setting to the new name. Nothing else will, and the handler is silently not running in the meantime.' ) ) );
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
                        ezpI18n::tr( 'kernel/setup/rad', 'Nothing in it can load, and the kernel reports it on every single request.' ),
                        array( ezpI18n::tr( 'kernel/setup/rad', 'If it should be there: composer require it, or put the directory in extension/ - and remember an extension can live in any root named by AdditionalExtensionDirectories[].' ),
                               ezpI18n::tr( 'kernel/setup/rad', 'Then regenerate the autoloads: php bin/php/ezpgenerateautoloads.php' ),
                               ezpI18n::tr( 'kernel/setup/rad', 'If it should not be there: take the line out of site.ini. A name left behind after an extension is removed costs a failed lookup on every request for ever.' ),
                               ezpI18n::tr( 'kernel/setup/rad', 'Clear the caches either way: php bin/php/ezcache.php --clear-all' ) ) );

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
                ? ezpI18n::tr( 'kernel/setup/rad', 'The extension is not on disk at all, so nothing it might have contained is in the design chain.' )
                : ezpI18n::tr( 'kernel/setup/rad', 'There is no %name/design directory, so listing it adds nothing to the design chain and a template somebody expects to be found is not.', null, array( '%name' => $name ) ),
                $path === false
                ? array( ezpI18n::tr( 'kernel/setup/rad', 'Install the extension, or take the line out of design.ini.' ) )
                : array( ezpI18n::tr( 'kernel/setup/rad', 'If the extension is supposed to carry templates, the directory has to be extension/%name/design/<designname>/templates/ - the design name in the middle is the part that is usually missed.', null, array( '%name' => $name ) ),
                         ezpI18n::tr( 'kernel/setup/rad', 'If it carries no templates, take the line out of design.ini: it costs a directory lookup per design resolution and buys nothing.' ),
                         ezpI18n::tr( 'kernel/setup/rad', 'Clear the template caches after either: php bin/php/ezcache.php --clear-tag=template' ) ) );
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
                ezpI18n::tr( 'kernel/setup/rad', 'There is no translations directory in it, so every string falls back to the source language and nothing says why.' ),
                array( ezpI18n::tr( 'kernel/setup/rad', 'The layout has to be extension/%name/translations/<locale>/translation.ts, with the locale in the shape eng-GB.', null, array( '%name' => $name ) ),
                       ezpI18n::tr( 'kernel/setup/rad', 'A locale nothing is set to is read by nobody: check site.ini [RegionalSettings] Locale on the siteaccess that should use it.' ),
                       ezpI18n::tr( 'kernel/setup/rad', 'Clear the caches: php bin/php/ezcache.php --clear-all' ) ) );
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
                    ezpI18n::tr( 'kernel/setup/rad', 'Every address beginning /%name/ answers with a module not found error.', null, array( '%name' => $name ) ),
                    array( ezpI18n::tr( 'kernel/setup/rad', 'Both lines are needed. ExtensionRepositories[] says which extension to look in and ModuleList[] says what to look for; with only the second the kernel looks in the kernel and reports it missing, which is this.' ),
                           ezpI18n::tr( 'kernel/setup/rad', 'Check the module directory really is extension/<name>/modules/%name/module.php - the modules/ in the middle is not optional.', null, array( '%name' => $name ) ),
                           ezpI18n::tr( 'kernel/setup/rad', 'If the module was removed, take it out of ModuleList[] as well.' ),
                           ezpI18n::tr( 'kernel/setup/rad', 'Clear the caches: php bin/php/ezcache.php --clear-all' ) ) );

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
                    ezpI18n::tr( 'kernel/setup/rad', 'The module declares the view and the file it names is not there, so the address exists and answers with a blank page rather than a not found.' ),
                    array( ezpI18n::tr( 'kernel/setup/rad', 'Either write the script at that exact path, or take the view out of $ViewList in %path/module.php.', null, array( '%path' => $module['path'] ) ),
                           ezpI18n::tr( 'kernel/setup/rad', 'A view left in the list after its script is gone is worse than one that was never declared: it is reachable, it is in the policy list a role can grant, and it does nothing.' ),
                           ezpI18n::tr( 'kernel/setup/rad', 'Clear the caches: php bin/php/ezcache.php --clear-all' ) ) );
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
                ezpI18n::tr( 'kernel/setup/rad', 'It is offered in the class editor and there is no %type/%typetype.php in any directory searched. An attribute of this type cannot be added, and any existing attribute of it holds a value nothing can read.', null, array( '%type' => $type ) ),
                array( ezpI18n::tr( 'kernel/setup/rad', 'Check the extension carrying it is installed and named in content.ini [DataTypeSettings] ExtensionDirectories[] - the datatype is looked for at <extension>/datatypes/%type/%typetype.php and nowhere else.', null, array( '%type' => $type ) ),
                       ezpI18n::tr( 'kernel/setup/rad', 'The three names have to agree exactly: the value here, the directory, and the file inside it.' ),
                       ezpI18n::tr( 'kernel/setup/rad', 'Before removing it from AvailableDataTypes[], check whether any content class still uses it. Content with an attribute of a datatype that is gone cannot be edited, and the values are unreadable rather than merely hidden.' ),
                       ezpI18n::tr( 'kernel/setup/rad', 'Clear the caches: php bin/php/ezcache.php --clear-all' ) ) );
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
                    ezpI18n::tr( 'kernel/setup/rad', 'Searching it costs a directory lookup and finds nothing. Usually left over from a version that had it.' ),
                    array( ezpI18n::tr( 'kernel/setup/rad', 'The path is read relative to the installation root, so it wants to be %path from %root - not from wherever the setting file is.', null, array( '%path' => $value, '%root' => getcwd() ) ),
                           ezpI18n::tr( 'kernel/setup/rad', 'If the directory should exist, create it, or correct the value of %variable in %ini [%section].', null, array( '%variable' => $entry['variable'], '%ini' => $entry['ini'], '%section' => $entry['section'] ) ),
                           ezpI18n::tr( 'kernel/setup/rad', 'If it should not, take that line out of %ini. This is the least urgent finding here: nothing is broken, there is simply a lookup on every resolution that can never succeed.', null, array( '%ini' => $entry['ini'] ) ),
                           ezpI18n::tr( 'kernel/setup/rad', 'Clear the caches after either: php bin/php/ezcache.php --clear-all' ) ) );
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
                ezpI18n::tr( 'kernel/setup/rad', 'Not in share/icons and not in any extension listed in IconExtensions[], so every icon asked of this theme falls through to the standard one or draws the default.' ),
                array( ezpI18n::tr( 'kernel/setup/rad', 'A theme in an extension needs both halves: the directory at extension/<name>/icons/%theme/, and the extension named in icon.ini [ExtensionSettings] IconExtensions[].', null, array( '%theme' => $theme ) ),
                       ezpI18n::tr( 'kernel/setup/rad', 'The directory is only a theme if it has an icon.ini of its own naming its sizes. Without that the sizes are never looked in.' ),
                       ezpI18n::tr( 'kernel/setup/rad', 'If the theme has gone, take it out of Theme, StandardTheme or AdditionalThemeList[] rather than leaving it to fall through.' ) ) );
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
                // A real arrow: in single quotes the escape was printed as
                // the eight characters \xe2\x86\x92.
                $override['name'] . " \xe2\x86\x92 " . $override['match'],
                $override['from'],
                ezpI18n::tr( 'kernel/setup/rad', 'The override matches and then finds nothing to draw with, so whatever it was meant to replace is drawn by the default instead and nothing reports it.' ),
                array( ezpI18n::tr( 'kernel/setup/rad', 'The template goes under the design, at <design>/override/templates/%match - the override/templates/ in the middle is what is usually missed.', null, array( '%match' => $override['match'] ) ),
                       ezpI18n::tr( 'kernel/setup/rad', 'The design carrying it has to be in the design chain: design.ini [ExtensionSettings] DesignExtensions[] for an extension, or SiteDesign for the siteaccess.' ),
                       ezpI18n::tr( 'kernel/setup/rad', 'If the override is no longer wanted, remove its whole block from %file rather than only the MatchFile line.', null, array( '%file' => $override['from'] ) ),
                       ezpI18n::tr( 'kernel/setup/rad', 'Clear the template caches: php bin/php/ezcache.php --clear-tag=template' ) ) );
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
                ezpI18n::tr( 'kernel/setup/rad', 'It is in the override autoload map and the kernel has no class of that name, so it overrides nothing and is simply an ordinary class loaded by an unusual route.' ),
                array( ezpI18n::tr( 'kernel/setup/rad', 'If the kernel class was renamed, this override is now doing nothing and whatever it was working around is back. Find the new name and decide whether the override is still needed.' ),
                       ezpI18n::tr( 'kernel/setup/rad', 'If it was never meant to override anything, move it to the extension\'s ordinary classes/ directory and regenerate: php bin/php/ezpgenerateautoloads.php' ),
                       ezpI18n::tr( 'kernel/setup/rad', 'Setup, RAD tools, Kernel override wizard writes these with a drift check that would have caught this at the upgrade that caused it.' ) ) );
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
                ezpI18n::tr( 'kernel/setup/rad', '%name (%count methods)', null, array( '%name' => $contract['name'], '%count' => $contract['methods'] ) ),
                $contract['source'],
                ezpI18n::tr( 'kernel/setup/rad', 'An extension point nobody here has taken up. Nothing is wrong with it; it is simply unused.' ),
                array( ezpI18n::tr( 'kernel/setup/rad', 'Nothing to do. It is listed so that the page is a complete picture rather than only a list of faults.' ),
                       ezpI18n::tr( 'kernel/setup/rad', 'If you are looking for somewhere to change behaviour, these are the places nobody has claimed yet.' ) ) );
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
                          'message' => ezpI18n::tr( 'kernel/setup/rad', 'The class loader check is not installed.' ) );

        require_once $library;

        if ( !is_file( eZCheckClasses::COMMAND ) )
            return array( 'ran' => false, 'checked' => 0, 'findings' => array(),
                          'message' => ezpI18n::tr( 'kernel/setup/rad', '%command is not there, and it is both the command and the child process.', null, array( '%command' => eZCheckClasses::COMMAND ) ) );

        $classes = eZCheckClasses::classNames( false, $withTests );
        $found   = eZCheckClasses::check( $classes );

        $findings = array();

        foreach ( $found['bad'] as $class )
        {
            $reason = eZCheckClasses::reason( $class );

            $findings[] = self::finding( self::BROKEN, 'Class php refuses to load',
                $class, self::fileOf( $class ),
                // php's own message stays as php wrote it.
                self::loaderKindLabel( $reason['kind'] ) . ' ' . $reason['message'],
                self::howToFix( $reason ) );
        }

        return array( 'ran' => true, 'checked' => $found['checked'],
                      'findings' => $findings, 'message' => '' );
    }

    /**
     * What to do about a class php refuses to load.
     *
     * Four kinds of fault, and they want four different things doing. Telling
     * somebody a class will not load and leaving it there is the half of the
     * job that is easy.
     *
     * @param array $reason as eZCheckClasses::reason gives it
     * @return array of string
     */
    public static function howToFix( array $reason )
    {
        $message = isset( $reason['message'] ) ? $reason['message'] : '';

        if ( strpos( $message, 'must be compatible with' ) !== false )
            return array(
                ezpI18n::tr( 'kernel/setup/rad', 'The class declares a method whose signature does not match the one it inherits, and php 8 refuses the whole class for it. This is a fault in the class, not in the configuration.' ),
                ezpI18n::tr( 'kernel/setup/rad', 'php names both signatures in the message above. Usually the difference is an argument that gained a default, a type, or an & - and the fix is to make the child match the parent exactly.' ),
                ezpI18n::tr( 'kernel/setup/rad', 'Check the parent is the one intended before changing signatures. A class extending the wrong base is the commoner cause, and matching signatures to a base it should not have had makes it harder to see.' ),
                ezpI18n::tr( 'kernel/setup/rad', 'The class is in an extension, so the fix belongs upstream: patch it there, release it, and move the constraint in composer.json rather than editing vendor code in place.' ) );

        if ( preg_match( '/Class ["\']?([^"\' ]+)["\']? not found/', $message, $found ) )
            return array(
                ezpI18n::tr( 'kernel/setup/rad', 'It extends or implements %class, and nothing on this installation declares that.', null, array( '%class' => $found[1] ) ),
                ezpI18n::tr( 'kernel/setup/rad', 'Usually an extension that needs another one. Find what provides %class and install it, or switch this extension off if it is not wanted.', null, array( '%class' => $found[1] ) ),
                ezpI18n::tr( 'kernel/setup/rad', 'If it is installed, the autoload map is stale: php bin/php/ezpgenerateautoloads.php' ),
                ezpI18n::tr( 'kernel/setup/rad', 'Until then every request that touches this class ends - not a wrong page, no page.' ) );

        if ( strpos( $message, 'cannot be called statically' ) !== false )
            return array(
                ezpI18n::tr( 'kernel/setup/rad', 'Something is called statically at load time and is not declared static. php 7 allowed this and php 8 does not.' ),
                ezpI18n::tr( 'kernel/setup/rad', 'Declare the method static where it is defined, if every caller is static - that is almost always the case for a method called from the foot of a file at include time.' ),
                ezpI18n::tr( 'kernel/setup/rad', 'This is the fault that stopped every payment gateway on this installation loading, and the fix was one keyword in the kernel.' ),
                ezpI18n::tr( 'kernel/setup/rad', 'If some callers use an instance, the method has to stay as it is and the static calls have to change instead.' ) );

        if ( strpos( $message, 'Cannot redeclare' ) !== false )
            return array(
                ezpI18n::tr( 'kernel/setup/rad', 'Two files declare the same name, and both are reached.' ),
                ezpI18n::tr( 'kernel/setup/rad', 'Usually a file that is include_once-d by hand as well as being in the autoload map. Take the manual include out and let the autoloader do it.' ),
                ezpI18n::tr( 'kernel/setup/rad', 'It can also be two extensions shipping the same class name, in which case one of them has to be renamed - there is no way for both to work.' ) );

        return array(
            ezpI18n::tr( 'kernel/setup/rad', 'php refused the class and the message above is what it said.' ),
            ezpI18n::tr( 'kernel/setup/rad', 'Run it on its own to see the whole thing: php bin/php/checkclasses.php' ),
            ezpI18n::tr( 'kernel/setup/rad', 'Whatever it is, the class ends any request that touches it, so it is worth chasing even if nothing appears to use it.' ) );
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

        return $path !== '' ? expRADSurvey::sourceLabel( $path ) : ezpI18n::tr( 'kernel/setup/rad', '(nothing declares it)' );
    }
}
}

require_once 'kernel/setup/expradsurvey.php';


?>
