<?php
/**
 * File containing the expMigrationRecordingGenerator class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * exp:migration:generate --from-recording=<id>: a recording of changes (expMigrationRecording) as a YAML migration.
 *
 * The plan (plan()) is ready: the recorded events folded into one step per thing changed, in the order the things
 * were first changed (a create followed by updates is one create with the last values; a thing created and removed
 * in the same recording leaves no step; anything followed by a removal is the removal). expMigrationRecordingSteps
 * turns the plan into the steps of the step types (content, location, content_class, role, user, section ...),
 * expMigrationYamlWriter writes them, and the migration is checked as exp:migration:check checks a file before it is
 * written. Until the installation has every step type the steps need, the generator reports which are missing and
 * writes nothing.
 *
 * exp:migration:generate --from-content / --from-class / --from-role / --from-section / --from-settings
 * (fromExisting()) make the same events from what is there now: each thing as if it had been created, read through
 * the same resolver, so a generated migration of existing things follows the same rules (stable identifiers, no
 * personal data, mode ensure where the step type has it, so that it can run on an installation that has them).
 *
 * Personal data (open question 16, answer a): the account of a user is never in a recording; a generated user step
 * names login, e-mail and password by secrets (secrets: of the user step), and no step names who made a change.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationRecordingGenerator
{
    /**
     * The steps of a recording: its events folded per subject. Pure.
     *
     * @param array[] $events normalised events in the order they were recorded
     * @return array[] each: subject, step (type, mode, keys), audit (the audit names folded into it), omitted, notes
     */
    public static function plan( array $events )
    {
        $steps = array();
        $order = array();
        foreach ( $events as $event )
        {
            if ( !is_array( $event ) || !isset( $event['step']['type'], $event['step']['mode'] ) )
                continue;
            $type = (string)$event['step']['type'];
            $mode = (string)$event['step']['mode'];
            $subject = isset( $event['subject'] ) ? (string)$event['subject'] : '';
            // the key events fold under; events that are not about one thing's state are kept one by one: assignments,
            // moves, swaps, aliases. A user is an object too: the events of its account (user remote:...) and of its
            // object (object remote:...) fold together.
            $key = $subject !== '' && self::foldable( $type, $mode ) ? preg_replace( '/^user remote:/', 'object remote:', $subject ) : "\n" . count( $order );
            $omitted = isset( $event['omitted'] ) ? (array)$event['omitted'] : array();
            $notes = isset( $event['notes'] ) ? (array)$event['notes'] : array();
            if ( !isset( $steps[$key] ) )
            {
                $steps[$key] = array( 'subject' => $subject, 'step' => $event['step'], 'audit' => array( $event['audit'] ),
                                      'omitted' => $omitted, 'notes' => $notes );
                $order[] = $key;
                continue;
            }
            $current =& $steps[$key];
            $current['audit'][] = $event['audit'];
            $current['omitted'] = array_values( array_unique( array_merge( $current['omitted'], $omitted ) ) );
            $current['notes'] = array_values( array_unique( array_merge( $current['notes'], $notes ) ) );
            $was = $current['step'] === null ? '' : (string)$current['step']['mode'];
            if ( $mode === 'delete' )
            {
                if ( $was === 'create' )
                    $current['step'] = null;
                else
                    $current['step'] = $event['step'];
            }
            else if ( $current['step'] === null )
                $current['step'] = $event['step'];
            else if ( $was === 'create' && $mode !== 'create' )
            {
                // the creation with the last values
                $keys = $event['step'];
                unset( $keys['match'], $keys['mode'] );
                $current['step'] = $keys + $current['step'];
                $current['step']['mode'] = 'create';
            }
            else
                $current['step'] = $event['step'];
            unset( $current );
        }
        $out = array();
        foreach ( $order as $key )
            if ( $steps[$key]['step'] !== null )
                $out[] = $steps[$key];
        return $out;
    }

    /**
     * Whether the events of a step type and mode describe the whole state of a thing (the last one wins). Pure.
     *
     * @param string $type
     * @param string $mode
     * @return bool
     */
    public static function foldable( $type, $mode )
    {
        if ( in_array( $mode, array( 'create', 'update', 'ensure', 'delete', 'set', 'remove' ), true ) )
            return !in_array( $type, array( 'url_alias' ), true );
        return false;
    }


    /**
     * The step types the migration of a plan needs that this installation does not have.
     *
     * @param array[] $plan plan()
     * @param expMigrationStepRegistry|null $registry
     * @return string[]
     */
    public static function missingStepTypes( array $plan, $registry = null )
    {
        $steps = array();
        foreach ( $plan as $entry )
        {
            $type = isset( $entry['step']['type'] ) ? (string)$entry['step']['type'] : '';
            $mode = isset( $entry['step']['mode'] ) ? (string)$entry['step']['mode'] : '';
            foreach ( expMigrationRecordingSteps::typesOf( $type, $mode ) as $target )
                $steps[] = array( 'type' => $target );
        }
        return self::missingIn( $steps, $registry );
    }

    /**
     * The step types of a list of migration steps that this installation does not have.
     *
     * @param array[] $steps
     * @param expMigrationStepRegistry|null $registry
     * @return string[] sorted
     */
    public static function missingIn( array $steps, $registry = null )
    {
        $registry = $registry ?: expMigrationStepRegistry::fromSettings();
        $have = array_fill_keys( $registry->names(), true );
        $missing = array();
        foreach ( $steps as $step )
            if ( isset( $step['type'] ) && !isset( $have[$step['type']] ) )
                $missing[$step['type']] = true;
        $missing = array_keys( $missing );
        sort( $missing );
        return $missing;
    }

    /**
     * The report of exp:migration:generate --from-recording: the plan, and the migration of it (written with apply).
     *
     * @param eZDBInterface $db
     * @param string $recordingId
     * @param array $options name (of the migration), source, description, author, apply (write the file), registry,
     *                       time, root, directory (as expMigrationGenerator::skeleton())
     * @return array mode, recording, plan (lines), steps, manual, missing_step_types, omitted, generated, errors,
     *               warnings, exit
     */
    public static function fromRecording( $db, $recordingId, array $options = array() )
    {
        $report = self::emptyReport( 'generate-from-recording' );
        $recording = expMigrationRecording::get( $db, $recordingId );
        if ( $recording === null )
        {
            $report['errors'][] = "no recording $recordingId (./console exp:migration:record:list lists them)";
            $report['exit'] = expUpgrade::EXIT_USAGE;
            return $report;
        }
        $report['recording'] = $recording;
        if ( $recording['state'] === expMigrationRecording::RECORDING )
            $report['warnings'][] = 'the recording is still running: the migration holds what was recorded so far';
        $events = array();
        foreach ( expMigrationRecording::events( $db, $recordingId ) as $row )
            if ( $row['event'] !== null )
                $events[] = $row['event'];
        if ( !isset( $options['name'] ) || trim( (string)$options['name'] ) === '' )
            $options['name'] = $recording['label'] !== null && trim( (string)$recording['label'] ) !== '' ? (string)$recording['label'] : 'recording';
        if ( !isset( $options['description'] ) || trim( (string)$options['description'] ) === '' )
            $options['description'] = 'Recording ' . $recordingId . ( $recording['label'] !== null && trim( (string)$recording['label'] ) !== '' ? ': ' . $recording['label'] : '' );
        $options['comments'] = array( 'Generated from the recording ' . $recordingId . ' (' . count( $events ) . ' events).' );
        return self::migration( $events, $options, $report );
    }

    /**
     * The report of exp:migration:generate --from-content, --from-class, --from-role, --from-section and
     * --from-settings: what is there now, as a migration that makes it so (mode ensure where the step type has it).
     *
     * @param array $what content (node identifiers), depth (levels below each node, default 0), limit (objects, default
     *                    500), classes (identifiers), roles (names), sections (identifiers), settings (list of
     *                    array( file, siteaccess ))
     * @param array $options as fromRecording(), and source (expMigrationRecordingSource, for tests), keepUserAttributes
     * @return array as fromRecording()
     */
    public static function fromExisting( array $what, array $options = array() )
    {
        $report = self::emptyReport( 'generate-from-existing' );
        $source = isset( $options['recordingSource'] ) ? $options['recordingSource'] : new expMigrationRecordingResolver();
        $keep = isset( $options['keepUserAttributes'] ) ? (array)$options['keepUserAttributes'] : expMigrationRecorder::settings()['keepUserAttributes'];
        $normalise = array( 'keepUserAttributes' => $keep );
        $events = array();
        $add = function ( $name, array $data, $label ) use ( &$events, &$report, $source, $normalise )
        {
            $event = expMigrationRecordingEvent::normalise( $name, $data, $source, $normalise );
            if ( $event === null || isset( $event['missing'] ) )
                $report['errors'][] = "$label: not found";
            else
                $events[] = $event;
        };
        foreach ( isset( $what['classes'] ) ? (array)$what['classes'] : array() as $identifier )
            $add( 'content.class.create', array( 'object' => array( 'identifier' => (string)$identifier ) ), "the class $identifier" );
        foreach ( isset( $what['sections'] ) ? (array)$what['sections'] : array() as $identifier )
        {
            $section = eZSection::fetchByIdentifier( (string)$identifier, false );
            if ( !is_array( $section ) )
                $report['errors'][] = "the section $identifier: not found";
            else
                $add( 'content.section.change', array( 'object' => array( 'id' => (int)$section['id'], 'identifier' => (string)$identifier ) ), "the section $identifier" );
        }
        foreach ( isset( $what['roles'] ) ? (array)$what['roles'] : array() as $name )
            $add( 'access.role.create', array( 'object' => array( 'name' => (string)$name ) ), "the role $name" );
        $limit = isset( $what['limit'] ) ? max( 1, (int)$what['limit'] ) : 500;
        $seen = array();
        foreach ( isset( $what['content'] ) ? (array)$what['content'] : array() as $identifier )
        {
            $nodeId = expMigrationIdentifier::find( 'node', (string)$identifier );
            if ( $nodeId === null )
            {
                $report['errors'][] = "the node $identifier: not found";
                continue;
            }
            foreach ( self::subtreeObjects( (int)$nodeId, isset( $what['depth'] ) ? max( 0, (int)$what['depth'] ) : 0 ) as $objectId )
            {
                if ( isset( $seen[$objectId] ) )
                    continue;
                if ( count( $seen ) >= $limit )
                {
                    $report['warnings'][] = "only the first $limit objects (--max-objects)";
                    break 2;
                }
                $seen[$objectId] = true;
                $add( 'content.object.create', array( 'object' => array( 'id' => $objectId ) ), "the object $objectId" );
            }
        }
        foreach ( isset( $what['settings'] ) ? (array)$what['settings'] : array() as $entry )
            foreach ( self::settingEvents( (string)$entry['file'], isset( $entry['siteaccess'] ) ? (string)$entry['siteaccess'] : '', $report ) as $data )
                $add( 'system.setting.write', $data, 'the setting ' . $data['object']['variable'] );
        if ( $report['errors'] )
        {
            $report['exit'] = expUpgrade::EXIT_USAGE;
            return $report;
        }
        if ( !$events )
        {
            $report['errors'][] = 'nothing to generate: name what with --from-content, --from-class, --from-role, --from-section or --from-settings';
            $report['exit'] = expUpgrade::EXIT_USAGE;
            return $report;
        }
        $options['ensure'] = true;
        $options['comments'] = array( 'Generated from what this installation has (' . count( $events ) . ' things).' );
        if ( !isset( $options['name'] ) || trim( (string)$options['name'] ) === '' )
            $options['name'] = 'generated';
        return self::migration( $events, $options, $report );
    }

    /**
     * The migration of normalised events: folded (plan()), turned into steps (expMigrationRecordingSteps), checked
     * against the step types (expMigrationYaml::validate()), written as YAML (expMigrationYamlWriter), and with apply
     * written to the migrations directory (never over a file).
     *
     * @param array[] $events
     * @param array $options name, source, description, author, apply, registry, time, root, directory, ensure (modes
     *                       ensure instead of create), comments (lines for the header), manual (lines to be done by
     *                       hand that the caller found, listed before those of the steps)
     * @param array $report emptyReport()
     * @return array the report
     */
    public static function migration( array $events, array $options, array $report )
    {
        $plan = self::plan( $events );
        $report['steps'] = $plan;
        foreach ( $plan as $entry )
        {
            $report['plan'][] = expMigrationRecordingEvent::line( array( 'step' => $entry['step'], 'subject' => $entry['subject'] ) );
            $report['omitted'] = array_values( array_unique( array_merge( $report['omitted'], (array)$entry['omitted'] ) ) );
        }
        $made = expMigrationRecordingSteps::fromPlan( $plan, array( 'ensure' => !empty( $options['ensure'] ) ) );
        $made['manual'] = array_values( array_unique( array_merge( isset( $options['manual'] ) ? (array)$options['manual'] : array(), $made['manual'] ) ) );
        $report['manual'] = $made['manual'];
        $registry = isset( $options['registry'] ) ? $options['registry'] : expMigrationStepRegistry::fromSettings();
        $report['missing_step_types'] = self::missingIn( $made['steps'], $registry );
        if ( $report['missing_step_types'] )
        {
            $report['errors'][] = 'needs the YAML step types ' . implode( ', ', $report['missing_step_types'] )
                                . ' (an extension that is not active?): nothing is written until this installation has them';
            $report['exit'] = expUpgrade::EXIT_FAILED;
            return $report;
        }
        if ( !$made['steps'] )
        {
            $report['errors'][] = 'no step to write: ' . ( $made['manual'] ? 'every change has to be made by hand' : 'nothing was changed' );
            $report['exit'] = expUpgrade::EXIT_FAILED;
            return $report;
        }
        $document = array( 'steps' => $made['steps'] );
        $warnings = array();
        foreach ( expMigrationYaml::validate( $document + array( 'down' => null, 'transactional' => true ), $registry, $warnings ) as $problem )
            $report['errors'][] = 'the generated migration does not pass the check: ' . $problem;
        foreach ( $warnings as $warning )
            $report['warnings'][] = $warning;

        $skeleton = expMigrationGenerator::skeleton( isset( $options['name'] ) ? (string)$options['name'] : 'generated', array( 'type' => 'yml' ) + array_intersect_key(
            $options, array_flip( array( 'source', 'description', 'author', 'time', 'root', 'directory', 'ini' ) ) ) );
        $report['errors'] = array_merge( $report['errors'], $skeleton['errors'] );
        if ( $skeleton['id'] !== '' )
        {
            $comments = array( 'description: ' . ( isset( $options['description'] ) && trim( (string)$options['description'] ) !== ''
                                                   ? trim( preg_replace( '/\s+/', ' ', (string)$options['description'] ) )
                                                   : ucfirst( str_replace( '_', ' ', expMigrationGenerator::slug( $options['name'] ) ) ) ) );
            if ( isset( $options['author'] ) && trim( (string)$options['author'] ) !== '' )
                $comments[] = 'author: ' . trim( preg_replace( '/\s+/', ' ', (string)$options['author'] ) );
            $comments[] = '';
            foreach ( isset( $options['comments'] ) ? (array)$options['comments'] : array() as $line )
                $comments[] = $line;
            $comments[] = 'Check it before the run: ./console exp:migration:check and ./console exp:migration:dry-run';
            if ( $made['manual'] )
            {
                $comments[] = '';
                $comments[] = 'Not generated, to be done by hand:';
                foreach ( $made['manual'] as $line )
                    $comments[] = '- ' . $line;
            }
            $skeleton['content'] = expMigrationYamlWriter::document( $document, $comments );
            $skeleton['ok'] = !$report['errors'];
            unset( $skeleton['errors'] );
            $report['generated'] = $skeleton;
        }
        if ( $report['errors'] )
        {
            $report['exit'] = expUpgrade::EXIT_FAILED;
            return $report;
        }
        if ( !empty( $options['apply'] ) )
        {
            $errors = expMigrationGenerator::write( $report['generated'] + array( 'errors' => array() ) );
            if ( $errors )
            {
                $report['errors'] = $errors;
                $report['exit'] = expUpgrade::EXIT_FAILED;
            }
            else
                $report['written'] = true;
        }
        return $report;
    }

    /**
     * The ids of the objects of a node and of the nodes below it up to a depth, parents first.
     *
     * @param int $nodeId
     * @param int $depth 0: the node alone
     * @return int[]
     */
    public static function subtreeObjects( $nodeId, $depth )
    {
        $node = eZContentObjectTreeNode::fetch( (int)$nodeId );
        if ( !$node instanceof eZContentObjectTreeNode )
            return array();
        $ids = array( (int)$node->attribute( 'contentobject_id' ) );
        if ( $depth > 0 )
        {
            $children = eZContentObjectTreeNode::subTreeByNodeID( array( 'Depth' => $depth, 'DepthOperator' => 'le', 'Limitation' => array(),
                                                                         'SortBy' => array( array( 'depth', true ), array( 'path', true ) ),
                                                                         'AsObject' => true, 'IgnoreVisibility' => true ), (int)$nodeId );
            foreach ( is_array( $children ) ? $children : array() as $child )
                $ids[] = (int)$child->attribute( 'contentobject_id' );
        }
        return array_values( array_unique( $ids ) );
    }

    /**
     * The settings of one file of settings/override or of a siteaccess, as the data of system.setting.write events.
     *
     * @param string $file site.ini ...
     * @param string $siteaccess empty: settings/override
     * @param array $report errors are added to it
     * @return array[]
     */
    public static function settingEvents( $file, $siteaccess, array &$report )
    {
        $file = preg_replace( '/\.append(\.php)?$/', '', basename( (string)$file ) );
        if ( !preg_match( '/^[A-Za-z0-9_.-]+\.ini$/', $file ) || ( $siteaccess !== '' && !preg_match( '/^[A-Za-z0-9_-]+$/', $siteaccess ) ) )
        {
            $report['errors'][] = "the settings file $file" . ( $siteaccess !== '' ? " of $siteaccess" : '' ) . ': a name like site.ini, and a siteaccess name';
            return array();
        }
        $dir = $siteaccess !== '' ? "settings/siteaccess/$siteaccess" : 'settings/override';
        $path = null;
        foreach ( array( "$file.append.php", "$file.append" ) as $name )
            if ( is_file( "$dir/$name" ) )
                $path = "$dir/$name";
        if ( $path === null )
        {
            $report['errors'][] = "the settings file $dir/$file.append.php: not found";
            return array();
        }
        $ini = eZINI::fetchFromFile( $path );
        $out = array();
        foreach ( (array)$ini->groups() as $block => $variables )
            foreach ( (array)$variables as $variable => $value )
                $out[] = array( 'object' => array( 'file' => $file, 'block' => (string)$block, 'variable' => (string)$variable, 'path' => $path,
                                                   'scope' => $siteaccess !== '' ? "siteaccess:$siteaccess" : 'override' ),
                                'after' => array( 'set' => true, 'value' => $value ) );
        return $out;
    }

    /**
     * @param string $mode
     * @return array
     */
    protected static function emptyReport( $mode )
    {
        return array( 'mode' => $mode, 'recording' => null, 'steps' => array(), 'plan' => array(), 'manual' => array(),
                      'missing_step_types' => array(), 'omitted' => array(), 'generated' => null, 'written' => false,
                      'errors' => array(), 'warnings' => array(), 'exit' => expUpgrade::EXIT_OK );
    }
}
