<?php
/**
 * @description Help maintained as content: create the help classes, sections, states, tree and roles (idempotent), check the topics against the style guide, seed the glossary and topics, report and uninstall (the changing actions are a dry run unless --apply)
 * @alias exp:help:content
 *
 * File containing the exp:help:content command (doc/eng-us/features/6.0/help-content.md, doc/eng-us/specifications/6.0/help-system.md).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Exphelpcontent extends \Exponential\Runnable\Command
{
    /** The actions that change the installation: a dry run unless --apply (check --fix and feedback --prune too) */
    const WRITING_ACTIONS = array( 'install', 'tables', 'classes', 'tree', 'roles', 'seed-glossary', 'seed-topics', 'build-manuals', 'ebooks', 'uninstall' );

    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "Help maintained as content (topics, manuals, glossary in the content tree)\n" .
                                 "\n" .
                                 "./console exp:help:content install -s <admin siteaccess> [--apply]   tables, classes, tree and roles (all four steps)\n" .
                                 "./console exp:help:content tables -s ...     the tables exphelp_help_id (released help_ids) and exphelp_feedback\n" .
                                 "                                              (\"Was this helpful?\"), when an older installation lacks them\n" .
                                 "./console exp:help:content classes -s ...    the eight help_* classes in the class group Help (create_classes.php)\n" .
                                 "./console exp:help:content tree -s ...       sections, the state group help_status, the folder tree (create_tree.php)\n" .
                                 "./console exp:help:content roles -s ...      the roles Help editor, Help release and the Anonymous policy (create_roles.php)\n" .
                                 "./console exp:help:content check -s ... [--fix --apply]   the validator (check_help.php); --fix sets the sections from the audience\n" .
                                 "./console exp:help:content seed-glossary -s ... [--file=] [--update]   the glossary (seed_glossary.php)\n" .
                                 "./console exp:help:content seed-topics -s ... [--file=] [--update] [--release]   topics and manuals (seed_topics.php)\n" .
                                 "./console exp:help:content build-manuals -s ... [--manual=<key>] [--language=ger-DE,...] [--release] [--queue] [--apply]\n" .
                                 "                                              the manuals of the Markdown help (exphelp.ini [ManualSettings]):\n" .
                                 "                                              made, updated and removed so they match the pages (help-gen-...)\n" .
                                 "./console exp:help:content report -s ...     what is installed, the roles' assignments, the content by state\n" .
                                 "./console exp:help:content export -s ... --manual=help-manual-editors --format=epub|pdf [--output=file] [--language=] [--check]\n" .
                                 "                                              an e-book of the Released topics of a manual (export_manual.php);\n" .
                                 "                                              --check runs epubcheck and Ace by DAISY where the site has them\n" .
                                 "./console exp:help:content dita -s ... [--manual=<remote_id>] [--output=dir] [--dry-run] [--commit]\n" .
                                 "                                              the DITA 1.3 export (bookmaps, DITAVAL, topics, glossary) into a\n" .
                                 "                                              directory, checked after writing (also: export --format=dita);\n" .
                                 "                                              --commit records it in git (the cronjob part exphelp does it regularly)\n" .
                                 "./console exp:help:content feedback -s ... [--prune --apply] [--json]   the answers to \"Was this helpful?\" per topic\n" .
                                 "./console exp:help:content ebooks -s ... [--manual=<remote_id>] [--language=ger-DE] [--apply]   build the e-books of the\n" .
                                 "                                              Released manuals and store them as file objects under E-books\n" .
                                 "./console exp:help:content review -s ... [--json]   the topics whose review is overdue\n" .
                                 "./console exp:help:content cache -s ... [--json]   fill the cache of the help rows and search words of the\n" .
                                 "                                              siteaccess (after a deploy or ezcache --clear-id=exphelpcontent)\n" .
                                 "./console exp:help:content package -s ... [--output=dir] [--version=6.0.15]   the eight classes as the content\n" .
                                 "                                              class package exp_help_classes (a directory and an .ezpkg)\n" .
                                 "./console exp:help:content verify-package -s ... --file=<.ezpkg or directory>   compare a package with the\n" .
                                 "                                              classes of this release (no database)\n" .
                                 "./console exp:help:content uninstall -s ... [--with-content] [--apply]   take the roles away (and the tree, classes,\n" .
                                 "                                              state group and sections with --with-content)\n" .
                                 "\n" .
                                 "-s (--siteaccess) is required: one tree may serve several installations with different databases. Every\n" .
                                 "step is idempotent: a second run prints \"No change.\". The actions that change the installation (install,\n" .
                                 "tables, classes, tree, roles, check --fix, seed-glossary, seed-topics, build-manuals, ebooks, feedback --prune,\n" .
                                 "uninstall) only show what would change unless --apply is given; --dry-run means the same as no --apply, both\n" .
                                 "together are a usage error (exit 2). export, dita, package and cache write their files as before (--dry-run\n" .
                                 "still shows the plan of export and dita); report, review and verify-package only read.\n" .
                                 "Exit code: 0 fine, 1 check found an ERROR (or a step could not be done), 2 error.",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[apply][dry-run][check][prune][commit][fix][file:][update][release][with-content][login:][json][manual:][format:][output:][version:][language:][queue]', '', array(
            'queue'        => 'build-manuals: queue the build as a background content job (helpmanuals) and return at once; nothing is queued while one runs',
            'apply'        => 'install, tables, classes, tree, roles, check --fix, seed-*, build-manuals, ebooks, feedback --prune, uninstall: make the changes (the default is a dry run)',
            'dry-run'      => 'Show what would change, change nothing (the default of the changing actions; export and dita: their plan)',
            'fix'          => 'check: set the section of each topic, figure, chapter and manual from the audience, and record the help_ids of Released topics',
            'file'         => 'seed-glossary, seed-topics: the data file (default share/help/seed-glossary.json, share/help/seed-topics.json)',
            'update'       => 'seed-*: also update existing objects that differ from the file',
            'release'      => 'seed-topics: set new topics, chapters and manuals to Released; build-manuals: set every generated one to Released',
            'language'     => 'build-manuals: the translations to write (default [ManualSettings] Languages[]; "none" for none); ebooks, export: the language of the edition (default: the default language)',
            'with-content' => 'uninstall: also the help tree (into the trash), the classes, the state group and the sections',
            'login'        => 'The user who creates the classes and objects (default admin)',
            'json'         => 'Print one JSON document instead of the text',
            'manual'       => 'export, dita, ebooks: the remote_id of the manual (help-manual-...); dita and ebooks without it: every manual; build-manuals: the key of one manual of [ManualSettings] Manuals[]',
            'format'       => 'export: epub, pdf or dita',
            'check'        => 'export: check the EPUB with epubcheck and Ace by DAISY ([ContentSettings] EpubCheckCommand, AceCommand); exit 1 when one fails',
            'prune'        => 'feedback: remove the answers older than [ContentSettings] FeedbackKeepDays',
            'commit'       => 'dita: commit the written files when the directory is a git repository (the history of delivery path 4)',
            'output'       => 'export: the file to write (default <cache>/exphelp/ebooks/<remote_id>.<format>); dita: the directory (default <cache>/exphelp/dita/all or /<remote_id>); package: the directory (default <cache>/exphelp/packages)',
            'version'      => 'package: the release, MAJOR.MINOR.PATCH (default: the version of this installation)',
        ) );

        $arguments = isset( $options['arguments'] ) ? array_values( $options['arguments'] ) : array();
        $action = $arguments ? strtolower( (string)array_shift( $arguments ) ) : '';
        $json = !empty( $options['json'] );
        $apply = self::applyRequested( $options );
        if ( $apply === null )
        {
            $cli->error( 'FAIL: --apply and --dry-run exclude each other; without --apply the script changes nothing' );
            $this->shutdown( 2 );
            return;
        }
        $dryRun = !$apply;
        // the actions that change the installation write only with --apply; export, dita, package and cache write the
        // files they are asked for as before (--dry-run still plans export and dita), report, review, verify-package read
        $writing = in_array( $action, self::WRITING_ACTIONS, true ) || ( $action === 'check' && !empty( $options['fix'] ) )
                   || ( $action === 'feedback' && !empty( $options['prune'] ) );
        if ( !$writing )
            $dryRun = !empty( $options['dry-run'] );

        if ( empty( $options['siteaccess'] ) )
        {
            $cli->error( 'FAIL: -s <siteaccess> is required (./console exp:help:content --help)' );
            $this->shutdown( 2 );
            return;
        }
        $known = array( 'install', 'tables', 'classes', 'tree', 'roles', 'check', 'seed-glossary', 'seed-topics', 'build-manuals', 'report', 'uninstall', 'export',
                        'dita', 'ebooks', 'review', 'feedback', 'package', 'verify-package', 'cache' );
        if ( !in_array( $action, $known, true ) )
        {
            $cli->error( "Unknown action \"$action\": " . implode( ', ', $known ) . ' (./console exp:help:content --help)' );
            $this->shutdown( 2 );
            return;
        }

        $login = !empty( $options['login'] ) ? (string)$options['login'] : 'admin';
        $user = \eZUser::fetchByName( $login );
        if ( !$user instanceof \eZUser )
        {
            $cli->error( 'FAIL: no user with the login given by --login' );
            $this->shutdown( 2 );
            return;
        }
        \eZUser::setCurrentlyLoggedInUser( $user, (int)$user->attribute( 'contentobject_id' ) );

        $db = \eZDB::instance();
        if ( !$json )
        {
            $cli->output( 'Siteaccess: ' . $options['siteaccess'] . '   database: ' . $db->databaseName() . ' ' . $db->DB .
                          ( $dryRun ? '   (dry run: nothing is changed)' : '' ) );
        }

        $code = 0;
        try
        {
            $common = array( 'dry_run' => $dryRun, 'user_id' => (int)$user->attribute( 'contentobject_id' ) );
            switch ( $action )
            {
                case 'install':
                case 'tables':
                case 'classes':
                case 'tree':
                case 'roles':
                    $installer = new \expHelpContentInstaller( $common );
                    $installer->install( $action === 'install' ? array( 'tables', 'classes', 'tree', 'roles' ) : array( $action ) );
                    $code = $this->printChanges( $installer->changes(), $json );
                    break;
                case 'seed-glossary':
                case 'seed-topics':
                    $code = $this->seed( $action, $options, $common, $json );
                    break;
                case 'build-manuals':
                    $code = !empty( $options['queue'] ) ? $this->queueManuals( $options, $user, $json, $dryRun )
                                                        : $this->buildManuals( $options, $common, $json );
                    break;
                case 'check':
                    $code = $this->check( !empty( $options['fix'] ), $common, $json );
                    break;
                case 'report':
                    $this->printReport( \expHelpContentInstaller::report()
                                        + array( 'manual_build' => \expContentJobHelpManuals::available() ? \expContentJobHelpManuals::status() : null ), $json );
                    break;
                case 'export':
                    $code = isset( $options['format'] ) && strtolower( (string)$options['format'] ) === 'dita'
                          ? $this->dita( $options, $dryRun, $json ) : $this->export( $options, $dryRun, $json );
                    break;
                case 'dita':
                    $code = $this->dita( $options, $dryRun, $json );
                    break;
                case 'ebooks':
                    if ( !\expHelpContentRepository::isInstalled() )
                    {
                        $cli->error( 'FAIL: help as content is not installed (./console exp:help:content install -s <siteaccess>)' );
                        $code = 2;
                        break;
                    }
                    if ( !empty( $options['language'] ) && !\expHelpContentEbooks::useLanguage( (string)$options['language'] ) )
                    {
                        $cli->error( 'FAIL: the installation has no content language ' . $options['language'] );
                        $code = 2;
                        break;
                    }
                    $books = new \expHelpContentEbooks( $common + ( !empty( $options['language'] ) ? array( 'language' => (string)$options['language'] ) : array() ) );
                    $books->build( !empty( $options['manual'] ) ? (string)$options['manual'] : null );
                    $code = $this->printChanges( array_values( array_filter( $books->changes(), function ( $c ) use ( $json ) {
                        // skipped manuals are reported, but are no change
                        return $json || $c['action'] !== 'skip';
                    } ) ), $json );
                    foreach ( $json ? array() : $books->changes() as $change )
                    {
                        if ( $change['action'] === 'skip' )
                            $cli->output( sprintf( '%-8s %-8s %s', $change['step'], $change['action'], $change['what'] ) );
                    }
                    break;
                case 'feedback':
                    $code = $this->feedback( $options, $dryRun, $json );
                    break;
                case 'review':
                    $code = $this->review( $json );
                    break;
                case 'cache':
                    $code = $this->fillCache( $json );
                    break;
                case 'package':
                case 'verify-package':
                    $code = $this->package( $action, $options, $dryRun, $json, $user );
                    break;
                case 'uninstall':
                    $installer = new \expHelpContentInstaller( $common );
                    $installer->uninstall( !empty( $options['with-content'] ) );
                    $code = $this->printChanges( $installer->changes(), $json );
                    break;
            }
            if ( !$dryRun && !in_array( $action, array( 'report', 'export', 'dita', 'ebooks', 'review', 'feedback', 'package', 'verify-package', 'cache' ), true ) )
                \expHelpContentProvider::clearCache();
            if ( $writing && $dryRun && $code !== 2 && !$json )
                $cli->output( "PASS: $action (dry run, nothing changed; --apply writes)" );
        }
        catch ( \Exception $e )
        {
            $cli->error( 'FAIL: ' . $e->getMessage() );
            $code = 2;
        }
        $this->shutdown( $code );
    }

    protected function seed( $action, array $options, array $common, $json )
    {
        $default = $action === 'seed-glossary' ? 'share/help/seed-glossary.json' : 'share/help/seed-topics.json';
        $file = !empty( $options['file'] ) ? (string)$options['file'] : $default;
        $seeder = new \expHelpContentSeeder( $common + array( 'update' => !empty( $options['update'] ), 'release' => !empty( $options['release'] ) ) );
        $data = \expHelpContentSeeder::readFile( $file );
        if ( $action === 'seed-glossary' )
            $seeder->seedGlossary( $data );
        else
            $seeder->seedTopics( $data );
        $changes = $seeder->changes();
        // new objects take the section of their parent: set it from the audience at once
        if ( $seeder->touched() && empty( $common['dry_run'] ) )
        {
            \expHelpContentRepository::resetInstance();
            $set = \expHelpContentRepository::instance()->load();
            $validator = new \expHelpContentValidator();
            $installer = new \expHelpContentInstaller( $common );
            $installer->fixSections( $validator->validate( $set['topics'], $set['figures'], $set['maps'], $set['glossary'] ) );
            $changes = array_merge( $changes, $installer->changes() );
        }
        return $this->printChanges( $changes, $json );
    }

    /**
     * The manuals of the Markdown help (doc/eng-us/features/6.0/help-content.md, "Manuals from the Markdown help"): the
     * classes get the audience options first (as "classes" does), then expHelpManualBuilder plans every manual of
     * [ManualSettings] Manuals[] and expHelpManualSync makes the database match it. Run on every release.
     */
    protected function buildManuals( array $options, array $common, $json )
    {
        $cli = $this->cli();
        $started = microtime( true );
        if ( !\expHelpContentRepository::isInstalled() )
        {
            $cli->error( 'FAIL: help as content is not installed (./console exp:help:content install -s <siteaccess>)' );
            return 2;
        }
        $changes = array();
        // the audience options the pages need ([ContentSettings] Audiences[]), appended as the classes step does
        $installer = new \expHelpContentInstaller( $common );
        $installer->install( array( 'classes' ) );
        $changes = array_merge( $changes, $installer->changes() );

        $settings = \expHelpManualBuilder::manualsFromSettings();
        foreach ( $settings['errors'] as $error )
            $changes[] = array( 'step' => 'manuals', 'action' => 'error', 'what' => $error );
        $manual = isset( $options['manual'] ) ? preg_replace( '/^' . preg_quote( \expHelpManualBuilder::MANUAL_PREFIX, '/' ) . '/', '', (string)$options['manual'] ) : '';

        $default = (string)\eZContentObject::defaultLanguage();
        $wanted = isset( $options['language'] ) && $options['language'] !== ''
                ? ( strtolower( (string)$options['language'] ) === 'none' ? array() : array_map( 'trim', explode( ',', (string)$options['language'] ) ) )
                : (array)\expHelpManualBuilder::setting( 'Languages', array() );
        $wanted = array_values( array_filter( $wanted, function ( $locale ) use ( $default ) { return $locale !== '' && $locale !== $default; } ) );
        $existing = \expHelpManualSync::existingLanguages( $wanted );
        $languages = array();
        $translations = array();
        foreach ( $existing['missing'] as $locale )
            $changes[] = array( 'step' => 'manuals', 'action' => 'skip', 'what' => "$locale: the installation has no such content language (not created; add it, then run again)" );
        foreach ( $existing['languages'] as $locale )
        {
            $source = \expHelpManualSource::forLocale( $locale );
            if ( $source === null )
            {
                $changes[] = array( 'step' => 'manuals', 'action' => 'skip', 'what' => "$locale: the help has no pages in that language ([DocLanguages])" );
                continue;
            }
            $languages[] = $locale;
            $translations[$locale] = $source;
        }

        $builder = new \expHelpManualBuilder( new \expHelpManualSource( \expHelpDocumentIndex::instance() ), $translations, array(
            'edition' => \expHelpContentPackage::release( '' )['version'],
            'license' => (string)\expHelpManualBuilder::setting( 'License', '' ),
            'translated_titles' => \expHelpManualBuilder::translatedTitlesFromSettings(),
        ) );
        $plan = $builder->plan( $settings['manuals'] );
        $planned = microtime( true );

        $sync = new \expHelpManualSync( $common + array( 'release' => !empty( $options['release'] ), 'manual' => $manual, 'language' => $default,
            // a long first build says how far it is; a run that changes nothing stays quiet ("No change.")
            'progress' => $json ? null : function ( $done, $total, $changed ) use ( $cli, $started ) {
                if ( $changed > 0 )
                    $cli->output( sprintf( '         %d of %d objects done (%.0fs)', $done, $total, microtime( true ) - $started ) );
            } ) );
        $sync->run( $plan, $languages );
        $changes = array_merge( $changes, $sync->changes() );

        // what the plan has, for the summary
        $counts = array( 'manual' => 0, 'chapter' => 0, 'topic' => 0 );
        $translated = array();
        foreach ( $plan['items'] as $item )
        {
            if ( $manual !== '' && $item['manual'] !== $manual )
                continue;
            $counts[$item['kind']]++;
            foreach ( array_keys( $item['translations'] ) as $locale )
                if ( in_array( $locale, $languages, true ) )
                    $translated[$locale][$item['kind']] = isset( $translated[$locale][$item['kind']] ) ? $translated[$locale][$item['kind']] + 1 : 1;
        }
        $notices = $builder->notices();
        if ( $json )
        {
            $errors = count( array_filter( $changes, function ( $c ) { return $c['action'] === 'error'; } ) );
            $this->printJson( array( 'manuals' => $counts['manual'], 'chapters' => $counts['chapter'], 'topics' => $counts['topic'],
                                     'translated' => $translated, 'languages' => $languages, 'changes' => $changes, 'counts' => $sync->counts(),
                                     'notices' => $notices, 'errors' => $errors, 'seconds' => round( microtime( true ) - $started, 1 ),
                                     'dry_run' => !empty( $common['dry_run'] ) ) );
            return $errors ? 1 : 0;
        }
        $code = $this->printChanges( $changes, false );
        foreach ( $notices as $notice )
            $cli->output( sprintf( '%-8s %-8s %s', 'manuals', 'notice', $notice ) );
        $parts = array();
        foreach ( $translated as $locale => $kinds )
            $parts[] = sprintf( '%s: %d manuals, %d chapters, %d topics', $locale, isset( $kinds['manual'] ) ? $kinds['manual'] : 0,
                                isset( $kinds['chapter'] ) ? $kinds['chapter'] : 0, isset( $kinds['topic'] ) ? $kinds['topic'] : 0 );
        $done = array();
        foreach ( $sync->counts() as $action => $count )
            $done[] = "$action $count";
        $cli->output( sprintf( '%s %d manuals, %d chapters, %d topics%s; %s; planned in %.1fs, %.1fs in all',
                               $code ? 'FAIL' : 'PASS', $counts['manual'], $counts['chapter'], $counts['topic'],
                               $parts ? ' (' . implode( '; ', $parts ) . ')' : '', $done ? implode( ', ', $done ) : 'no change',
                               $planned - $started, microtime( true ) - $started ) );
        return $code;
    }

    /**
     * build-manuals --queue: the build as the background content job helpmanuals (expContentJobHelpManuals), which
     * also installs the help as content first. Returns at once; while a build runs, nothing more is queued.
     */
    protected function queueManuals( array $options, \eZUser $user, $json, $dryRun )
    {
        $cli = $this->cli();
        if ( !\expContentJobHelpManuals::available() )
        {
            $cli->error( 'FAIL: the content job type helpmanuals is not registered (content.ini [ContentJobSettings] JobTypes[helpmanuals])' );
            return 2;
        }
        $params = array( 'release' => true );
        if ( !empty( $options['manual'] ) )
            $params['manual'] = preg_replace( '/^' . preg_quote( \expHelpManualBuilder::MANUAL_PREFIX, '/' ) . '/', '', (string)$options['manual'] );
        if ( isset( $options['language'] ) && $options['language'] !== '' )
            $params['languages'] = strtolower( (string)$options['language'] ) === 'none' ? array() : array_map( 'trim', explode( ',', (string)$options['language'] ) );
        if ( $dryRun )
        {
            $active = \expContentJobHelpManuals::activeJob();
            $cli->output( $active ? 'A build of the manuals runs already: content job ' . $active->id() : 'Would queue the content job helpmanuals' );
            return 0;
        }
        $result = \expContentJobHelpManuals::queue( $user, $params );
        $job = $result['job'];
        if ( $json )
        {
            $this->printJson( array( 'job' => $job->id(), 'queued' => $result['queued'], 'started' => $result['started'], 'state' => $job->state() ) );
            return 0;
        }
        $cli->output( ( $result['queued'] ? 'PASS queued' : 'PASS already queued:' ) . ' content job ' . $job->id() . ' (' . $job->description() . '), '
                      . ( $result['started'] ? 'its worker runs in the background' : 'the cronjob part contentjobs starts it' )
                      . '; progress: ./console exp:help:content report -s ' . $this->siteaccessName() );
        return 0;
    }

    protected function siteaccessName()
    {
        return isset( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : 'admin';
    }

    protected function export( array $options, $dryRun, $json )
    {
        $cli = $this->cli();
        $format = !empty( $options['format'] ) ? strtolower( (string)$options['format'] ) : 'epub';
        if ( !in_array( $format, array( 'epub', 'pdf' ), true ) )
        {
            $cli->error( 'FAIL: --format is epub or pdf' );
            return 2;
        }
        if ( !in_array( $format, \expHelpContentExport::formats(), true ) )
        {
            $cli->error( "FAIL: this installation cannot write $format (EPUB needs the zip extension, PDF the help's PDF writer)" );
            return 2;
        }
        if ( !empty( $options['language'] ) && !\expHelpContentEbooks::useLanguage( (string)$options['language'] ) )
        {
            $cli->error( 'FAIL: the installation has no content language ' . $options['language'] );
            return 2;
        }
        $provider = \expHelpContentProvider::instance();
        $manual = $provider->manual( isset( $options['manual'] ) ? (string)$options['manual'] : '' );
        if ( $manual === null )
        {
            $cli->error( 'FAIL: no manual with that remote_id (--manual=help-manual-...) the user may read' );
            return 2;
        }
        $export = new \expHelpContentExport( $provider, $manual );
        $file = !empty( $options['output'] ) ? (string)$options['output']
              : rtrim( \expHelp::cacheDirectory(), '/' ) . '/ebooks/' . $manual['remote_id']
                . ( !empty( $options['language'] ) ? '-' . strtolower( (string)$options['language'] ) : '' ) . '.' . $format;
        $topics = count( $export->topics() );
        if ( $dryRun )
        {
            $result = array( 'file' => $file, 'topics' => $topics, 'written' => false );
            if ( $json )
                $this->printJson( $result );
            else
                $cli->output( "Would write $file ($topics Released topics)" );
            return $topics ? 0 : 1;
        }
        $result = $format === 'epub' ? $export->writeEpub( $file ) : $export->writePdf( $file );
        $code = 0;
        if ( !empty( $options['check'] ) && $format === 'epub' )
        {
            // epubcheck and Ace by DAISY, as the site configured them ([ContentSettings] EpubCheckCommand, AceCommand)
            $checker = new \expHelpContentBookCheck();
            $result['checks'] = $checker->check( $result['file'] );
            foreach ( $result['checks'] as $check )
            {
                if ( in_array( $check['status'], array( \expHelpContentBookCheck::FAIL, \expHelpContentBookCheck::ERROR ), true ) )
                    $code = 1;
            }
        }
        if ( $json )
            $this->printJson( $result );
        else
        {
            $cli->output( sprintf( 'PASS wrote %s (%d bytes, %d topics)', $result['file'], $result['bytes'], $result['topics'] ) );
            foreach ( isset( $result['checks'] ) ? \expHelpContentBookCheck::lines( $result['checks'] ) : array() as $line )
                $cli->output( 'check    ' . $line );
        }
        return $code;
    }

    /**
     * The DITA 1.3 export into a directory (doc/eng-us/features/6.0/help-content.md, "DITA export"): changed files are
     * written, files of an earlier export that are gone are removed, then the directory is checked (well-formed, the
     * DITA 1.3 DOCTYPEs, every reference resolves). Exit 1 when the check finds a problem.
     */
    protected function dita( array $options, $dryRun, $json )
    {
        $cli = $this->cli();
        if ( !\expHelpContentRepository::isInstalled() )
        {
            $cli->error( 'FAIL: help as content is not installed (./console exp:help:content install -s <siteaccess>)' );
            return 2;
        }
        $manual = !empty( $options['manual'] ) ? (string)$options['manual'] : null;
        $dir = !empty( $options['output'] ) ? rtrim( (string)$options['output'], '/' )
             : rtrim( \expHelp::cacheDirectory(), '/' ) . '/dita/' . ( $manual === null ? 'all' : \expHelpContentDita::fileName( $manual ) );
        $export = ( new \expHelpContentDita( \expHelpContentProvider::instance(), $manual ) )->build();
        $problems = \expHelpContentDita::validate( $export );
        $result = \expHelpContentDita::write( $export, $dir, $dryRun || $problems );
        if ( !$dryRun && !$problems )
            $problems = \expHelpContentDita::validate( $dir );
        // the history in git (delivery path 4): a commit of what changed, the directory made a repository first
        $commit = null;
        if ( !empty( $options['commit'] ) && !$problems )
            $commit = ( new \expHelpContentDitaHistory( $dir ) )->commit( 'Help export of ' . gmdate( 'Y-m-d H:i' ) . ' UTC', true, $dryRun );
        if ( $commit !== null && $commit['status'] === 'error' )
            $problems[] = array( 'file' => $dir, 'message' => $commit['message'] );
        if ( $json )
        {
            $this->printJson( array( 'directory' => $dir, 'counts' => $export['counts'], 'written' => $result['written'], 'unchanged' => $result['unchanged'],
                                     'removed' => $result['removed'], 'dry_run' => $dryRun, 'problems' => $problems, 'commit' => $commit ) );
            return $problems ? 1 : 0;
        }
        if ( $commit !== null && $commit['status'] !== 'error' )
            $cli->output( sprintf( '%-8s %s%s', 'git', $commit['status'] . ': ' . $commit['message'], $commit['commit'] !== '' ? ' (' . $commit['commit'] . ')' : '' ) );
        foreach ( $result['written'] as $path )
            $cli->output( sprintf( '%-8s %s', $dryRun ? 'would' : 'write', $path ) );
        foreach ( $result['removed'] as $path )
            $cli->output( sprintf( '%-8s %s', $dryRun ? 'would rm' : 'remove', $path ) );
        if ( !$result['written'] && !$result['removed'] )
            $cli->output( 'No change.' );
        foreach ( $problems as $problem )
            $cli->output( sprintf( 'FAIL     %s: %s', $problem['file'], $problem['message'] ) );
        $counts = $export['counts'];
        $cli->output( sprintf( '%s %s: %d manuals, %d chapters, %d topics, %d figures, %d glossary entries; %d unchanged',
                               $problems ? 'FAIL' : 'PASS', $dir, $counts['manuals'], $counts['chapters'], $counts['topics'], $counts['figures'],
                               $counts['glossary'], $result['unchanged'] ) );
        return $problems ? 1 : 0;
    }

    /**
     * The answers to "Was this helpful?" (doc/eng-us/features/6.0/help-content.md, "Was this helpful?"): per topic yes,
     * no and the reasons, the least helpful first; --prune removes the answers older than FeedbackKeepDays first.
     */
    protected function feedback( array $options, $dryRun, $json )
    {
        $cli = $this->cli();
        if ( !\expHelpContentTables::available( \expHelpContentTables::FEEDBACK ) )
        {
            $cli->error( 'FAIL: the table exphelp_feedback is missing (./console exp:help:content tables -s <siteaccess>)' );
            return 2;
        }
        $pruned = !empty( $options['prune'] ) ? \expHelpContentFeedback::prune( null, $dryRun ) : 0;
        $summary = \expHelpContentFeedback::summary();
        uasort( $summary, function ( $a, $b ) {
            $shareA = $a['yes'] + $a['no'] ? $a['no'] / ( $a['yes'] + $a['no'] ) : 0;
            $shareB = $b['yes'] + $b['no'] ? $b['no'] / ( $b['yes'] + $b['no'] ) : 0;
            return array( $shareB, $b['no'], $a['help_id'] ) <=> array( $shareA, $a['no'], $b['help_id'] );
        } );
        if ( $json )
        {
            $this->printJson( array( 'keep_days' => \expHelpContentFeedback::keepDays(), 'pruned' => $pruned, 'dry_run' => $dryRun, 'topics' => $summary ) );
            return 0;
        }
        if ( !empty( $options['prune'] ) )
            $cli->output( sprintf( '%-8s %d answers older than %d days', $dryRun ? 'would rm' : 'removed', $pruned, \expHelpContentFeedback::keepDays() ) );
        foreach ( $summary as $objectId => $item )
        {
            $reasons = array();
            foreach ( $item['reasons'] as $reason => $count )
                $reasons[] = "$reason $count";
            $cli->output( sprintf( '%-32s %5d yes %5d no  %s', $item['help_id'] !== '' ? $item['help_id'] : '#' . $objectId, $item['yes'], $item['no'],
                                   implode( ', ', $reasons ) ) );
        }
        $cli->output( sprintf( '%d topics answered (answers are kept %d days)', count( $summary ), \expHelpContentFeedback::keepDays() ) );
        return 0;
    }

    /**
     * The overdue reviews (doc/eng-us/features/6.0/help-content.md, "Overdue reviews"): review date passed, or in Review
     * longer than [ContentSettings] ReviewMaxDays. Exit 0 also when some are overdue: it is a list, not a check
     * (check reports the past review dates as warnings).
     */
    protected function review( $json )
    {
        $cli = $this->cli();
        if ( !\expHelpContentRepository::isInstalled() )
        {
            $cli->error( 'FAIL: help as content is not installed (./console exp:help:content install -s <siteaccess>)' );
            return 2;
        }
        $items = \expHelpContentReview::all();
        if ( $json )
        {
            $this->printJson( array( 'max_days' => \expHelpContentReview::maxDays(), 'count' => count( $items ), 'topics' => $items ) );
            return 0;
        }
        foreach ( $items as $item )
            $cli->output( sprintf( '%-28s %-9s %4d days  %s', $item['help_id'], $item['state'], $item['days'],
                                   $item['reason'] === \expHelpContentReview::REASON_DATE
                                       ? 'review date ' . gmdate( 'Y-m-d', $item['since'] ) . ' has passed'
                                       : 'in Review, unchanged since ' . gmdate( 'Y-m-d', $item['since'] ) ) );
        $cli->output( sprintf( '%d topics overdue for review (in Review at most %d days)', count( $items ), \expHelpContentReview::maxDays() ) );
        return 0;
    }

    /**
     * cache: reads the help rows into the cache of this siteaccess (only what changed when the cache is there) and
     * the search words, so that the next help page does not (doc/eng-us/features/6.0/help-content.md, "How the help
     * stays fast with thousands of topics")
     */
    protected function fillCache( $json )
    {
        $cli = $this->cli();
        // nothing to fill: not a failure (exp:velocity deploy runs this for each siteaccess that shows the help)
        if ( !\expHelpContentProvider::isEnabled() || !\expHelpContentRepository::isInstalled() )
        {
            if ( $json )
                $this->printJson( array( 'skipped' => 'help as content is not installed or switched off' ) );
            else
                $cli->output( 'SKIP: help as content is not installed or switched off here (exphelp.ini [ContentSettings] Enabled)' );
            return 0;
        }
        \expHelpContentProvider::resetInstance();
        $repository = \expHelpContentRepository::instance();
        $started = microtime( true );
        $index = $repository->index();
        $rows = microtime( true ) - $started;
        $started = microtime( true );
        $words = $repository->searchWords();
        $search = microtime( true ) - $started;
        $root = \expHelpContentRepository::rootNode();
        // started by a web request that found the cache empty (expHelpContentRepository::startBackgroundBuild())
        if ( $root !== null && method_exists( '\expHelpContentRepository', 'finishBackgroundBuild' ) )
            \expHelpContentRepository::finishBackgroundBuild( \expHelpContentRepository::cacheFile( $root ) );
        $result = array( 'file' => $root !== null ? \expHelpContentRepository::cacheFile( $root ) : null, 'topics' => count( $index['topics'] ),
                         'maps' => count( $index['maps'] ), 'figures' => count( $index['figures'] ), 'glossary' => count( $index['glossary'] ),
                         'search_words' => count( $words ), 'rows_seconds' => round( $rows, 2 ), 'search_seconds' => round( $search, 2 ) );
        if ( $json )
            $this->printJson( $result );
        else
            $cli->output( sprintf( 'Help rows: %d topics, %d manuals and chapters, %d figures, %d glossary entries (%.2f s); search words of %d topics (%.2f s)',
                                   $result['topics'], $result['maps'], $result['figures'], $result['glossary'], $rows, $result['search_words'], $search ) );
        return 0;
    }

    /**
     * package: the help classes of this installation as the content class package exp_help_classes (directory and
     * .ezpkg); verify-package: compare a built package with the schema of this release, without the database.
     */
    protected function package( $action, array $options, $dryRun, $json, \eZUser $user )
    {
        $cli = $this->cli();
        if ( $action === 'verify-package' )
        {
            $file = isset( $options['file'] ) ? (string)$options['file'] : '';
            if ( $file === '' || !file_exists( $file ) )
            {
                $cli->error( 'FAIL: --file=<.ezpkg or package directory> is required' );
                return 2;
            }
            $problems = is_dir( $file ) ? \expHelpContentPackage::verifyDirectory( $file ) : \expHelpContentPackage::verifyArchive( $file );
            if ( $json )
                $this->printJson( array( 'file' => $file, 'problems' => $problems, 'ok' => !$problems ) );
            else
            {
                foreach ( $problems as $problem )
                    $cli->output( '  ' . $problem );
                $cli->output( $problems ? 'FAIL ' . count( $problems ) . " difference(s) in $file" : "PASS $file matches the help classes of this release" );
            }
            return $problems ? 1 : 0;
        }
        $dir = !empty( $options['output'] ) ? (string)$options['output'] : rtrim( \expHelp::cacheDirectory(), '/' ) . '/packages';
        $version = isset( $options['version'] ) ? (string)$options['version'] : '';
        $name = \expHelpContentPackage::fileName( $version );
        if ( $dryRun )
        {
            if ( $json )
                $this->printJson( array( 'directory' => $dir, 'archive' => $dir . '/' . $name, 'classes' => \expHelpContentPackage::provides(), 'written' => false ) );
            else
                $cli->output( "Would write $dir/" . \expHelpContentPackage::NAME . "/ and $dir/$name with the classes " . implode( ', ', \expHelpContentPackage::provides() ) );
            return 0;
        }
        $object = $user->attribute( 'contentobject' );
        $result = \expHelpContentPackage::build( $dir, $version, $object ? array( (string)$object->attribute( 'name' ), (string)$user->attribute( 'email' ) ) : array() );
        $problems = \expHelpContentPackage::verifyArchive( $result['archive'] );
        $result['problems'] = $problems;
        if ( $json )
            $this->printJson( $result );
        else
        {
            foreach ( $problems as $problem )
                $cli->output( '  ' . $problem );
            $cli->output( ( $problems ? 'FAIL' : 'PASS' ) . sprintf( ' wrote %s and %s (%d classes)', $result['directory'], $result['archive'], count( $result['classes'] ) ) );
        }
        return $problems ? 1 : 0;
    }

    protected function check( $fix, array $common, $json )
    {
        $cli = $this->cli();
        if ( !\expHelpContentRepository::isInstalled() )
        {
            $cli->error( 'FAIL: help as content is not installed (./console exp:help:content install -s <siteaccess>)' );
            return 2;
        }
        $set = \expHelpContentRepository::instance()->load();
        $fixed = 0;
        $changes = array();
        if ( $fix )
        {
            // the Released topics whose help_id is not recorded yet (Released before the table existed, or by a
            // seed): recorded first, so the rename and reuse checks see them
            foreach ( \expHelpContentHelpIds::recordReleased( $set['topics'], !empty( $common['dry_run'] ) ) as $helpId )
                $changes[] = array( 'step' => 'help_ids', 'action' => 'record', 'what' => "released help_id $helpId" );
        }
        $validator = new \expHelpContentValidator( array( 'released_ids' => \expHelpContentHelpIds::registry() ) );
        $findings = $validator->validate( $set['topics'], $set['figures'], $set['maps'], $set['glossary'] );
        if ( $fix )
        {
            $installer = new \expHelpContentInstaller( $common );
            $fixed = $installer->fixSections( $findings );
            $changes = array_merge( $changes, $installer->changes() );
            if ( $fixed > 0 && empty( $common['dry_run'] ) )
            {
                \expHelpContentRepository::resetInstance();
                $set = \expHelpContentRepository::instance()->load();
                $findings = $validator->validate( $set['topics'], $set['figures'], $set['maps'], $set['glossary'] );
            }
        }
        $counts = \expHelpContentValidator::counts( $findings );
        if ( $json )
        {
            $this->printJson( array( 'topics' => count( $set['topics'] ), 'figures' => count( $set['figures'] ), 'maps' => count( $set['maps'] ),
                                     'glossary' => count( $set['glossary'] ), 'counts' => $counts, 'fixed' => $fixed,
                                     'changes' => $changes, 'findings' => $findings ) );
        }
        else
        {
            foreach ( $changes as $change )
                $cli->output( sprintf( '%-8s %-8s %s', $change['step'], $change['action'], $change['what'] ) );
            foreach ( $findings as $finding )
                $cli->output( sprintf( '%-7s %-24s %-28s %-12s %s', $finding['level'], $finding['code'],
                                       $finding['help_id'] !== '' ? $finding['help_id'] : '#' . $finding['object_id'], $finding['field'], $finding['message'] ) );
            $cli->output( sprintf( '%d topics, %d figures, %d manuals and chapters, %d glossary entries: %d ERROR, %d WARNING, %d NOTICE%s',
                                   count( $set['topics'] ), count( $set['figures'] ), count( $set['maps'] ), count( $set['glossary'] ),
                                   $counts['ERROR'], $counts['WARNING'], $counts['NOTICE'],
                                   $fix ? ', ' . $fixed . ( empty( $common['dry_run'] ) ? ' sections set' : ' sections would be set' ) : '' ) );
        }
        return $counts['ERROR'] > 0 ? 1 : 0;
    }

    protected function printChanges( array $changes, $json )
    {
        $errors = 0;
        foreach ( $changes as $change )
        {
            if ( $change['action'] === 'error' )
                $errors++;
        }
        if ( $json )
        {
            $this->printJson( array( 'changes' => $changes, 'errors' => $errors ) );
            return $errors ? 1 : 0;
        }
        $cli = $this->cli();
        if ( !$changes )
            $cli->output( 'No change.' );
        foreach ( $changes as $change )
            $cli->output( sprintf( '%-8s %-8s %s', $change['step'], $change['action'], $change['what'] ) );
        return $errors ? 1 : 0;
    }

    protected function printReport( array $report, $json )
    {
        if ( $json )
        {
            $this->printJson( $report );
            return;
        }
        $cli = $this->cli();
        $cli->output( 'Classes:' );
        foreach ( $report['classes'] as $identifier => $row )
            $cli->output( sprintf( '  %-22s %s', $identifier, $row['exists']
                ? 'id ' . $row['id'] . ', ' . $row['objects'] . ' objects' . ( $row['missing_attributes'] ? ', missing: ' . implode( ', ', $row['missing_attributes'] ) : '' )
                : 'missing' ) );
        $cli->output( 'Sections:' );
        foreach ( $report['sections'] as $identifier => $id )
            $cli->output( sprintf( '  %-22s %s', $identifier, $id ? 'id ' . $id : 'missing' ) );
        $cli->output( 'States (' . \expHelpContentSchema::STATE_GROUP . '):' );
        foreach ( $report['states'] as $identifier => $id )
            $cli->output( sprintf( '  %-22s %s', $identifier, $id ? 'id ' . $id : 'missing' ) );
        $cli->output( 'Tree:' );
        foreach ( $report['tree'] as $remoteId => $nodeId )
            $cli->output( sprintf( '  %-26s %s', $remoteId, $nodeId ? 'node ' . $nodeId : 'missing' ) );
        $cli->output( 'Roles:' );
        foreach ( $report['roles'] as $name => $row )
            $cli->output( sprintf( '  %-22s %s', $name, $row['exists'] ? $row['policies'] . ' policies, assigned to ' . count( $row['assigned_to'] ) : 'missing' ) );
        if ( $report['content'] )
        {
            $states = array();
            foreach ( $report['content']['topics_by_state'] as $state => $count )
                $states[] = "$state $count";
            $cli->output( sprintf( 'Content: %d topics (%s), %d figures, %d manuals and chapters, %d glossary entries',
                                   $report['content']['topics'], implode( ', ', $states ), $report['content']['figures'],
                                   $report['content']['manuals_and_chapters'], $report['content']['glossary'] ) );
        }
        if ( !empty( $report['manual_build'] ) )
            $cli->output( $report['manual_build']['message'] . ' (content job ' . $report['manual_build']['job_id'] . ', ' . $report['manual_build']['state'] . ')' );
    }

    protected function printJson( $data )
    {
        $this->cli()->output( json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR ) );
    }
}

}
