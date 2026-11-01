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
 * in the same recording leaves no step; anything followed by a removal is the removal). Writing the YAML waits for
 * the step types of work package 10 (content, location, class, role, user, section ...): until the installation has
 * every step type a recording needs, the generator reports which are missing and writes nothing.
 *
 * Personal data (open question 16, answer a): the account of a user is never in a recording; a generated user step
 * will name login, e-mail and password by secret references (expSecrets), and no step names who made a change.
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
            // moves, swaps, aliases
            $key = $subject !== '' && self::foldable( $type, $mode ) ? $subject : "\n" . count( $order );
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
     * The step types a plan needs that this installation does not have.
     *
     * @param array[] $plan plan()
     * @param expMigrationStepRegistry|null $registry
     * @return string[]
     */
    public static function missingStepTypes( array $plan, $registry = null )
    {
        $registry = $registry ?: expMigrationStepRegistry::fromSettings();
        $have = array_fill_keys( $registry->names(), true );
        $missing = array();
        foreach ( $plan as $entry )
            if ( !isset( $have[$entry['step']['type']] ) )
                $missing[$entry['step']['type']] = true;
        $missing = array_keys( $missing );
        sort( $missing );
        return $missing;
    }

    /**
     * The report of exp:migration:generate --from-recording: the plan, the step types missing, and (once every step
     * type exists) the migration. Until work package 10 has landed, the report says what is missing and the exit is 1.
     *
     * @param eZDBInterface $db
     * @param string $recordingId
     * @param array $options name (of the migration), source, registry (tests)
     * @return array mode, recording, plan (lines), steps, missing_step_types, errors, warnings, exit
     */
    public static function fromRecording( $db, $recordingId, array $options = array() )
    {
        $report = array( 'mode' => 'generate-from-recording', 'recording' => null, 'steps' => array(), 'plan' => array(),
                         'missing_step_types' => array(), 'omitted' => array(), 'errors' => array(), 'warnings' => array(),
                         'exit' => expUpgrade::EXIT_OK );
        $recording = expMigrationRecording::get( $db, $recordingId );
        if ( $recording === null )
        {
            $report['errors'][] = "no recording $recordingId (./console exp:migration:record:list lists them)";
            $report['exit'] = expUpgrade::EXIT_USAGE;
            return $report;
        }
        $report['recording'] = $recording;
        if ( $recording['state'] === expMigrationRecording::RECORDING )
            $report['warnings'][] = 'the recording is still running: the migration would hold what was recorded so far';
        $events = array();
        foreach ( expMigrationRecording::events( $db, $recordingId ) as $row )
            if ( $row['event'] !== null )
                $events[] = $row['event'];
        $plan = self::plan( $events );
        $report['steps'] = $plan;
        foreach ( $plan as $entry )
        {
            $report['plan'][] = expMigrationRecordingEvent::line( array( 'step' => $entry['step'], 'subject' => $entry['subject'] ) );
            $report['omitted'] = array_values( array_unique( array_merge( $report['omitted'], $entry['omitted'] ) ) );
        }
        $report['missing_step_types'] = self::missingStepTypes( $plan, isset( $options['registry'] ) ? $options['registry'] : null );
        if ( $report['missing_step_types'] )
        {
            $report['errors'][] = 'needs the YAML step types ' . implode( ', ', $report['missing_step_types'] )
                                . ' (the YAML steps v1 of work package 10): nothing is written until this installation has them';
            $report['exit'] = expUpgrade::EXIT_FAILED;
            return $report;
        }
        // every step type exists: the YAML writer of the steps arrives with them (work package 10)
        $report['errors'][] = 'needs the YAML step types of work package 10 to write the migration: the generator that writes it lands with them';
        $report['exit'] = expUpgrade::EXIT_FAILED;
        return $report;
    }
}
