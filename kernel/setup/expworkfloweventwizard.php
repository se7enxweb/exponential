<?php
/**
 * File containing the expWorkflowEventWizard class.
 *
 * @copyright Copyright (C) Exponential Open Source Project. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

require_once 'kernel/setup/expextensionwizard.php';

/**
 * Builds a workflow event type: a step a workflow can take when something is
 * published, moved, removed, registered or bought.
 *
 * A workflow event is the hardest thing in this system to write from memory.
 * It has to declare which operations it may be attached to and at which point;
 * it stores its settings in nine numbered columns that mean nothing until it
 * says what they are for; it reads its own form with a naming scheme nothing
 * enforces; and it answers with one of thirteen statuses, several of which
 * change what the kernel does next rather than only what the workflow does.
 *
 * All four of those are choices, and all four are made here, in a form, from
 * what this installation actually offers - the triggers are read out of the
 * operation definitions rather than listed from memory.
 */
class expWorkflowEventWizard extends expExtensionWizard
{
    /**
     * What wrote the files, for the head of each one.
     *
     * @return string
     */
    public static function wizardName()
    {
        return 'workflow event wizard';
    }
    /** The columns eZWorkflowEvent gives an event to keep its settings in. */
    const MAX_ATTRIBUTES = 9;

    /**
     * What this wizard can put in.
     *
     * @return array
     */
    public static function parts()
    {
        return array(
            'eventtype' => array(
                'label' => 'The event type',
                'description' => 'The class itself: its triggers, its settings, its form handling and its execute().',
                'default' => true ),
            'edit_template' => array(
                'label' => 'Edit template',
                'description' => 'What an editor sees when the event is added to a workflow, with a field per setting.',
                'default' => true ),
            'view_template' => array(
                'label' => 'View template',
                'description' => 'What the workflow list shows about the event once it is configured.',
                'default' => true ),
            'workflow_ini' => array(
                'label' => 'Registration',
                'description' => 'workflow.ini, so the event appears in the list of ones that can be added.',
                'default' => true ),
            'design_ini' => array(
                'label' => 'Design registration',
                'description' => 'design.ini, so the two templates above are found through the design chain.',
                'default' => true ),
            'cronjob' => array(
                'label' => 'Cronjob note',
                'description' => 'A note in the readme about workflow_cron, which is what runs an event that deferred itself.',
                'default' => true ),
            'ezinfo' => array(
                'label' => 'ezinfo.php',
                'description' => 'What the admin interface reads to show the extension name, version and licence.',
                'default' => true ),
            'extension_xml' => array(
                'label' => 'extension.xml',
                'description' => 'The packaged description of the extension.',
                'default' => true ),
            'composer' => array(
                'label' => 'composer.json',
                'description' => 'So the extension can be required by name rather than copied in.',
                'default' => true ),
            'readme' => array(
                'label' => 'README.md',
                'description' => 'What the event does, when it runs, what it stores and what it answers.',
                'default' => true ),
            'gitignore' => array(
                'label' => '.gitignore',
                'description' => 'Keeps editor leftovers and build output out of the repository.',
                'default' => true ),
            'licence' => array(
                'label' => 'LICENSE',
                'description' => 'The licence text named below. On by default: an extension with no licence file says nothing about how it may be used.',
                'default' => true ),
        );
    }

    /**
     * Every operation a workflow can be attached to, and at which point.
     *
     * Read out of the operation definitions of this installation rather than
     * remembered: an extension that adds an operation with a trigger in it
     * appears here without this file being touched.
     *
     * @return array module => operation => array of 'before'|'after'
     */
    public static function triggers()
    {
        $triggers = array();

        foreach ( array( 'content', 'user', 'shop' ) as $moduleName )
        {
            $module = eZModule::exists( $moduleName );
            if ( !$module instanceof eZModule )
                continue;

            $definition = self::operationDefinition( $moduleName );
            if ( !is_array( $definition ) )
                continue;

            foreach ( $definition as $operationName => $operation )
            {
                if ( !is_array( $operation ) || !isset( $operation['body'] ) || !is_array( $operation['body'] ) )
                    continue;

                $points = array();
                foreach ( $operation['body'] as $step )
                {
                    if ( !is_array( $step ) || !isset( $step['type'] ) || $step['type'] !== 'trigger' )
                        continue;
                    if ( !isset( $step['name'] ) || !is_string( $step['name'] ) )
                        continue;

                    // pre_publish is the "before" connection, post_publish the
                    // "after" one; that is the whole of the naming rule.
                    if ( strpos( $step['name'], 'pre_' ) === 0 )
                        $points['before'] = 'before';
                    else if ( strpos( $step['name'], 'post_' ) === 0 )
                        $points['after'] = 'after';
                }

                if ( count( $points ) )
                    $triggers[$moduleName][$operationName] = array_values( $points );
            }
        }

        foreach ( $triggers as $moduleName => $operations )
        {
            ksort( $operations );
            $triggers[$moduleName] = $operations;
        }

        return $triggers;
    }

    /**
     * One module's operation definition, read from its file.
     *
     * eZModule does not hand the definition over, so the file is included in a
     * scope of its own. Nothing from a request reaches the path: the module
     * name is one of three this method is ever called with.
     *
     * @param string $moduleName
     * @return array|false
     */
    protected static function operationDefinition( $moduleName )
    {
        if ( !in_array( $moduleName, array( 'content', 'user', 'shop' ), true ) )
            return false;

        $file = self::installationRoot() . '/kernel/' . $moduleName . '/operation_definition.php';
        if ( !file_exists( $file ) )
            return false;

        $OperationList = array();
        include $file;

        return isset( $OperationList ) && is_array( $OperationList ) ? $OperationList : false;
    }

    /**
     * The statuses an event may answer with, and what each one makes happen.
     *
     * Several of these change what the kernel does next rather than only what
     * the workflow does, which is the part that is easy to get wrong.
     *
     * @return array constant => array( label, what, common )
     */
    public static function statuses()
    {
        return array(
            'STATUS_ACCEPTED' => array(
                'label' => 'Accepted',
                'what'  => 'The event is done and the workflow carries on to the next one. Every event needs a way to reach this.',
                'common' => true ),
            'STATUS_REJECTED' => array(
                'label' => 'Rejected',
                'what'  => 'The workflow stops here and the operation it was attached to does not happen. On a before trigger, that means the publish is refused.',
                'common' => true ),
            'STATUS_DEFERRED_TO_CRON' => array(
                'label' => 'Deferred to cron',
                'what'  => 'Nothing more happens now; the workflow waits for the workflow_cron cronjob to come back to it. Used when the answer is not available yet.',
                'common' => true ),
            'STATUS_DEFERRED_TO_CRON_REPEAT' => array(
                'label' => 'Deferred, and run again',
                'what'  => 'As above, but this event runs again rather than the one after it. Set an activation date with setActivationDate() or it will spin.',
                'common' => true ),
            'STATUS_FETCH_TEMPLATE' => array(
                'label' => 'Fetch a template',
                'what'  => 'The request stops and a template of the event\'s choosing is shown instead - a confirmation page, a form, a warning.',
                'common' => false ),
            'STATUS_FETCH_TEMPLATE_REPEAT' => array(
                'label' => 'Fetch a template, then run again',
                'what'  => 'As above, and this event runs again when the visitor comes back.',
                'common' => false ),
            'STATUS_REDIRECT' => array(
                'label' => 'Redirect',
                'what'  => 'The visitor is sent somewhere else. Used to hand off to a payment provider and come back.',
                'common' => false ),
            'STATUS_REDIRECT_REPEAT' => array(
                'label' => 'Redirect, then run again',
                'what'  => 'As above, and this event runs again on return - which is how a payment result is read.',
                'common' => false ),
            'STATUS_WORKFLOW_CANCELLED' => array(
                'label' => 'Cancelled',
                'what'  => 'The whole workflow is abandoned. Use when the thing it was about is gone - a deleted object, a missing order.',
                'common' => true ),
            'STATUS_WORKFLOW_DONE' => array(
                'label' => 'Done',
                'what'  => 'The workflow is finished here; events after this one do not run.',
                'common' => false ),
            'STATUS_WORKFLOW_RESET' => array(
                'label' => 'Reset',
                'what'  => 'The workflow starts again from its first event.',
                'common' => false ),
            'STATUS_RUN_SUB_EVENT' => array(
                'label' => 'Run a sub event',
                'what'  => 'Hands over to another workflow, the way the multiplexer event does.',
                'common' => false ),
        );
    }

    /**
     * The kinds of setting an event can keep, and which column each needs.
     *
     * @return array key => array( label, description, storage )
     */
    public static function attributeTypes()
    {
        return array(
            'integer' => array(
                'label' => 'Number',
                'description' => 'A whole number, kept in one of the four integer columns.',
                'storage' => 'int' ),
            'checkbox' => array(
                'label' => 'Yes or no',
                'description' => 'A tick box, kept as 0 or 1 in an integer column.',
                'storage' => 'int' ),
            'select' => array(
                'label' => 'One of a list',
                'description' => 'A drop-down of values you name below, kept as text.',
                'storage' => 'text' ),
            'text' => array(
                'label' => 'Text',
                'description' => 'A line of text, kept in one of the five text columns.',
                'storage' => 'text' ),
            'idlist' => array(
                'label' => 'List of ids',
                'description' => 'A list of numbers - class ids, section ids, user ids - kept as comma separated text.',
                'storage' => 'text' ),
            'classattribute' => array(
                'label' => 'Class attributes',
                'description' => 'Attributes picked from a content class, the way the wait-until-date event does. Uses the class list helpers on the base type.',
                'storage' => 'text' ),
        );
    }

    // ── What was asked for ───────────────────────────────────────────────────

    /**
     * Everything the wizard was asked for, with the gaps filled in.
     *
     * @param array $input
     * @return array
     */
    public static function settings( array $input )
    {
        $name = self::safeName( isset( $input['name'] ) ? $input['name'] : '' );

        $settings = array(
            'name'    => $name,
            'event'   => self::safeEventName( isset( $input['event'] ) ? $input['event'] : '' ),
            'label'   => self::text( isset( $input['label'] ) ? $input['label'] : '', 80 ),
            'title'   => self::text( isset( $input['title'] ) ? $input['title'] : '', 120 ),
            'summary' => self::text( isset( $input['summary'] ) ? $input['summary'] : '', 250 ),
            'author'  => self::text( isset( $input['author'] ) ? $input['author'] : '', 120 ),
            'vendor'  => self::safeName( isset( $input['vendor'] ) ? $input['vendor'] : '', true ),
            'version' => self::text( isset( $input['version'] ) ? $input['version'] : '', 20 ),
            'licence' => self::licence_id( isset( $input['licence'] ) ? $input['licence'] : '' ),
            'triggers'   => self::safeTriggers( isset( $input['triggers'] ) ? $input['triggers'] : array() ),
            'statuses'   => self::safeStatuses( isset( $input['statuses'] ) ? $input['statuses'] : null ),
            'attributes' => self::safeAttributes( isset( $input['attributes'] ) ? $input['attributes'] : array() ),
        );

        if ( $settings['event'] === '' && $settings['name'] !== '' )
            $settings['event'] = self::safeEventName( $settings['name'] );
        if ( $settings['label'] === '' && $settings['event'] !== '' )
            $settings['label'] = ucwords( str_replace( '_', ' ', $settings['event'] ) );
        if ( $settings['title'] === '' && $settings['name'] !== '' )
            $settings['title'] = ucwords( str_replace( '_', ' ', $settings['name'] ) );
        if ( $settings['version'] === '' )
            $settings['version'] = '1.0.0';
        if ( $settings['vendor'] === '' )
            $settings['vendor'] = 'exponential';
        if ( $settings['summary'] === '' && $settings['label'] !== '' )
            $settings['summary'] = 'The ' . $settings['label'] . ' workflow event.';

        $chosen = isset( $input['parts'] ) && is_array( $input['parts'] ) ? $input['parts'] : null;
        foreach ( self::parts() as $key => $part )
            $settings['parts'][$key] = $chosen === null ? $part['default'] : in_array( $key, $chosen, true );

        return $settings;
    }

    /**
     * An event name: lower case, and what the class name is built from.
     *
     * @param string $value
     * @return string
     */
    public static function safeEventName( $value )
    {
        if ( !is_string( $value ) )
            return '';

        $value = strtolower( trim( $value ) );
        $value = preg_replace( '/[^a-z0-9]+/', '', $value );

        return $value !== null && preg_match( '/^[a-z][a-z0-9]{2,40}$/', $value ) ? $value : '';
    }

    /**
     * The triggers that were ticked, keeping only ones this installation has.
     *
     * What comes back from the form is a list of strings; every one is matched
     * against the operations really found, so a trigger that was invented
     * cannot reach the generated class - where it would sit in setTriggerTypes
     * and never fire, which is worse than being refused.
     *
     * @param mixed $chosen list of "module/operation/point".
     * @return array module => operation => array of point
     */
    public static function safeTriggers( $chosen )
    {
        if ( !is_array( $chosen ) )
            return array();

        $available = self::triggers();
        $triggers  = array();

        foreach ( $chosen as $entry )
        {
            if ( !is_string( $entry ) )
                continue;

            $parts = explode( '/', $entry );
            if ( count( $parts ) !== 3 )
                continue;

            list( $module, $operation, $point ) = $parts;

            if ( !isset( $available[$module][$operation] ) )
                continue;
            if ( !in_array( $point, $available[$module][$operation], true ) )
                continue;
            if ( isset( $triggers[$module][$operation] ) && in_array( $point, $triggers[$module][$operation], true ) )
                continue;

            $triggers[$module][$operation][] = $point;
        }

        return $triggers;
    }

    /**
     * The statuses that were ticked, of the ones that exist.
     *
     * @param mixed $chosen
     * @return array of constant name
     */
    public static function safeStatuses( $chosen )
    {
        $available = self::statuses();

        // On a first visit nothing has been posted, so the usual four stand.
        if ( $chosen === null )
        {
            $common = array();
            foreach ( $available as $key => $status )
                if ( $status['common'] )
                    $common[] = $key;

            return $common;
        }

        if ( !is_array( $chosen ) )
            return array( 'STATUS_ACCEPTED' );

        $statuses = array();
        foreach ( $chosen as $key )
            if ( is_string( $key ) && isset( $available[$key] ) && !in_array( $key, $statuses, true ) )
                $statuses[] = $key;

        // An event with no way to say it is done would hang every workflow it
        // is put in, so that one is not optional.
        if ( !in_array( 'STATUS_ACCEPTED', $statuses, true ) )
            array_unshift( $statuses, 'STATUS_ACCEPTED' );

        return $statuses;
    }

    /**
     * The settings the event keeps, mapped onto the columns it has for them.
     *
     * eZWorkflowEvent gives an event four integer columns and five text ones.
     * Each setting is given the next free column of the kind its type needs,
     * and one that cannot be given a column is dropped rather than silently
     * sharing - two settings in one column is a bug that only shows up once
     * somebody uses both.
     *
     * @param mixed $attributes
     * @return array
     */
    public static function safeAttributes( $attributes )
    {
        if ( !is_array( $attributes ) )
            return array();

        $types = self::attributeTypes();
        $ints  = array( 'data_int1', 'data_int2', 'data_int3', 'data_int4' );
        $texts = array( 'data_text1', 'data_text2', 'data_text3', 'data_text4', 'data_text5' );

        $clean = array();
        $names = array();

        foreach ( $attributes as $attribute )
        {
            if ( !is_array( $attribute ) )
                continue;

            $key = self::safeEventName( isset( $attribute['name'] ) ? $attribute['name'] : '' );
            if ( $key === '' || isset( $names[$key] ) )
                continue;

            $type = isset( $attribute['type'] ) && isset( $types[$attribute['type']] )
                    ? $attribute['type'] : 'text';

            $column = $types[$type]['storage'] === 'int' ? array_shift( $ints ) : array_shift( $texts );
            if ( $column === null )
                continue;   // no column left of the kind this setting needs

            $choices = array();
            if ( $type === 'select' && isset( $attribute['choices'] ) && is_string( $attribute['choices'] ) )
            {
                foreach ( explode( ',', $attribute['choices'] ) as $choice )
                {
                    $choice = self::text( $choice, 60 );
                    if ( $choice !== '' && !in_array( $choice, $choices, true ) )
                        $choices[] = $choice;
                }
            }

            $names[$key] = true;
            $clean[] = array(
                'name'    => $key,
                'label'   => self::text( isset( $attribute['label'] ) ? $attribute['label'] : '', 80 ),
                'help'    => self::text( isset( $attribute['help'] ) ? $attribute['help'] : '', 200 ),
                'type'    => $type,
                'storage' => $types[$type]['storage'],
                'column'  => $column,
                'choices' => $choices );

            if ( count( $clean ) >= self::MAX_ATTRIBUTES )
                break;
        }

        foreach ( $clean as $index => $attribute )
            if ( $attribute['label'] === '' )
                $clean[$index]['label'] = ucwords( str_replace( '_', ' ', $attribute['name'] ) );

        return $clean;
    }

    /**
     * What is wrong with these settings.
     *
     * @param array $settings
     * @return array of string
     */
    public static function problems( array $settings )
    {
        $problems = array();

        if ( $settings['name'] === '' )
            $problems[] = 'The extension needs a name: lower case letters, digits and underscores, three to forty one characters, starting with a letter.';

        if ( $settings['name'] !== '' && is_dir( self::extensionPath( $settings['name'] ) ) )
            $problems[] = 'extension/' . $settings['name'] . ' already exists. Choose another name, or remove it first.';

        if ( $settings['event'] === '' )
            $problems[] = 'The event needs a name of its own: lower case letters and digits, three or more, starting with a letter. It becomes the class name and the template name.';

        if ( !count( $settings['triggers'] ) )
            $problems[] = 'Choose at least one trigger. An event that can be attached to nothing can never run.';

        foreach ( $settings['attributes'] as $attribute )
            if ( $attribute['type'] === 'select' && !count( $attribute['choices'] ) )
                $problems[] = 'The setting "' . $attribute['label'] . '" is a list, but no values were given for it.';

        return $problems;
    }

    /**
     * The class the generated event type is called.
     *
     * @param array $settings
     * @return string
     */
    public static function className( array $settings )
    {
        return $settings['event'] . 'Type';
    }

    /**
     * The triggers as a sentence, for the page and the readme.
     *
     * @param array $settings
     * @return array of string
     */
    public static function triggerSentences( array $settings )
    {
        $sentences = array();
        foreach ( $settings['triggers'] as $module => $operations )
            foreach ( $operations as $operation => $points )
                foreach ( $points as $point )
                    $sentences[] = $point . ' ' . $module . '/' . $operation;

        sort( $sentences );

        return $sentences;
    }

    // ── What it writes ───────────────────────────────────────────────────────

    /**
     * Every file the extension is made of.
     *
     * @param array $settings
     * @return array path => contents
     */
    public static function files( array $settings )
    {
        if ( $settings['name'] === '' || $settings['event'] === '' )
            return array();

        $parts = $settings['parts'];
        $event = $settings['event'];
        $files = array();

        if ( $parts['eventtype'] )
            $files['eventtypes/event/' . $event . '/' . $event . 'type.php'] = self::eventClass( $settings );

        if ( $parts['edit_template'] )
            $files['design/standard/templates/workflow/eventtype/edit/event_' . $event . '.tpl'] = self::editTemplate( $settings );

        if ( $parts['view_template'] )
            $files['design/standard/templates/workflow/eventtype/view/event_' . $event . '.tpl'] = self::viewTemplate( $settings );

        if ( $parts['workflow_ini'] )
            $files['settings/workflow.ini.append.php'] = self::workflowIni( $settings );

        if ( $parts['design_ini'] )
            $files['settings/design.ini.append.php'] = self::designIni( $settings );

        if ( $parts['ezinfo'] )
            $files['ezinfo.php'] = self::ezinfo( $settings );

        if ( $parts['extension_xml'] )
            $files['extension.xml'] = self::extensionXml( $settings );

        if ( $parts['composer'] )
            $files['composer.json'] = self::composerJson( $settings );

        if ( $parts['gitignore'] )
            $files['.gitignore'] = self::gitignore( $settings );

        if ( $parts['licence'] )
            $files['LICENSE'] = self::licence( $settings );

        ksort( $files );

        if ( $parts['readme'] )
        {
            $files['README.md'] = self::readme( $settings, array_keys( $files ) );
            ksort( $files );
        }

        return $files;
    }

    /**
     * The lines that switch the extension on.
     *
     * @param array $settings
     * @return string
     */
    public static function activation( array $settings )
    {
        return "[ExtensionSettings]\nActiveExtensions[]=" . $settings['name'];
    }

    /**
     * The event type itself.
     *
     * @param array $settings
     * @return string
     */
    protected static function eventClass( array $settings )
    {
        $class = self::className( $settings );
        $event = $settings['event'];

        $php  = "<?php\n/**\n * " . $class . " - the " . $settings['label'] . " workflow event.\n *\n";
        $php .= " * " . $settings['summary'] . "\n *\n";
        $php .= " * Runs: " . implode( ', ', self::triggerSentences( $settings ) ) . "\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $php .= "class " . $class . " extends eZWorkflowEventType\n{\n";
        $php .= "    const WORKFLOW_TYPE_STRING = '" . $event . "';\n\n";

        // The settings, as named constants rather than data_int1 scattered about.
        if ( count( $settings['attributes'] ) )
        {
            $php .= "    // Which column each setting is kept in. eZWorkflowEvent offers four\n";
            $php .= "    // integer columns and five text ones and gives them no meaning; these\n";
            $php .= "    // constants are that meaning, in one place.\n";
            foreach ( $settings['attributes'] as $attribute )
                $php .= "    const " . strtoupper( $attribute['name'] ) . " = '" . $attribute['column'] . "';\n";
            $php .= "\n";
        }

        $php .= "    public function __construct()\n    {\n";
        $php .= "        parent::__construct( self::WORKFLOW_TYPE_STRING,\n";
        $php .= "                             ezpI18n::tr( 'kernel/workflow/event', '" . self::phpString( $settings['label'] ) . "' ) );\n\n";
        $php .= "        // Where this event may be attached, and at which point. A workflow\n";
        $php .= "        // cannot be given it anywhere else.\n";
        $php .= "        \$this->setTriggerTypes( array(\n";

        $moduleLines = array();
        foreach ( $settings['triggers'] as $module => $operations )
        {
            $operationLines = array();
            foreach ( $operations as $operation => $points )
                $operationLines[] = "                '" . $operation . "' => array( '" . implode( "', '", $points ) . "' )";

            $moduleLines[] = "            '" . $module . "' => array(\n" . implode( ",\n", $operationLines ) . " )";
        }

        $php .= implode( ",\n", $moduleLines ) . " ) );\n    }\n\n";

        // Reading the form.
        $php .= "    /**\n     * Reads the event's own form when a workflow is saved.\n     *\n";
        $php .= "     * The field names are fixed by convention: WorkflowEvent_event_<type>_<field>_<id>.\n";
        $php .= "     * Nothing enforces it, so it is written out here rather than guessed at.\n     *\n";
        $php .= "     * @return bool true when everything was read.\n     */\n";
        $php .= "    function fetchHTTPInput( \$http, \$base, \$event )\n    {\n";

        if ( count( $settings['attributes'] ) )
        {
            $php .= "        \$prefix = 'WorkflowEvent_event_' . self::WORKFLOW_TYPE_STRING . '_';\n";
            $php .= "        \$suffix = '_' . \$event->attribute( 'id' );\n\n";

            foreach ( $settings['attributes'] as $attribute )
            {
                $field = "\$prefix . '" . $attribute['name'] . "' . \$suffix";
                $php .= "        // " . $attribute['label'] . "\n";

                if ( $attribute['type'] === 'checkbox' )
                {
                    $php .= "        // A tick box that is not ticked sends nothing, so its absence is\n";
                    $php .= "        // the answer rather than a missing value.\n";
                    $php .= "        \$event->setAttribute( self::" . strtoupper( $attribute['name'] ) . ",\n";
                    $php .= "                              \$http->hasPostVariable( " . $field . " ) ? 1 : 0 );\n\n";
                    continue;
                }

                $php .= "        if ( \$http->hasPostVariable( " . $field . " ) )\n        {\n";
                $php .= "            \$value = \$http->postVariable( " . $field . " );\n\n";

                switch ( $attribute['type'] )
                {
                    case 'integer':
                        $php .= "            \$event->setAttribute( self::" . strtoupper( $attribute['name'] ) . ",\n";
                        $php .= "                                  is_numeric( \$value ) ? (int) \$value : 0 );\n";
                        break;

                    case 'select':
                        $php .= "            // Only a value this event offers; a list in a form is a\n";
                        $php .= "            // suggestion, not a guarantee of what comes back.\n";
                        $php .= "            \$allowed = array( '" . implode( "', '", array_map( array( 'expWorkflowEventWizard', 'phpStringPublic' ), $attribute['choices'] ) ) . "' );\n";
                        $php .= "            if ( is_string( \$value ) && in_array( \$value, \$allowed, true ) )\n";
                        $php .= "                \$event->setAttribute( self::" . strtoupper( $attribute['name'] ) . ", \$value );\n";
                        break;

                    case 'idlist':
                        $php .= "            // A list of numbers and nothing else, however it arrived.\n";
                        $php .= "            \$ids = array();\n";
                        $php .= "            foreach ( is_array( \$value ) ? \$value : explode( ',', (string) \$value ) as \$id )\n";
                        $php .= "                if ( is_numeric( \$id ) )\n";
                        $php .= "                    \$ids[] = (int) \$id;\n\n";
                        $php .= "            \$event->setAttribute( self::" . strtoupper( $attribute['name'] ) . ", implode( ',', \$ids ) );\n";
                        break;

                    case 'classattribute':
                        $php .= "            // Class attribute ids, as the base type stores them.\n";
                        $php .= "            \$ids = array();\n";
                        $php .= "            foreach ( is_array( \$value ) ? \$value : array( \$value ) as \$id )\n";
                        $php .= "                if ( is_numeric( \$id ) )\n";
                        $php .= "                    \$ids[] = (int) \$id;\n\n";
                        $php .= "            \$event->setAttribute( self::" . strtoupper( $attribute['name'] ) . ", implode( ',', \$ids ) );\n";
                        break;

                    default:
                        $php .= "            \$event->setAttribute( self::" . strtoupper( $attribute['name'] ) . ",\n";
                        $php .= "                                  is_scalar( \$value ) ? (string) \$value : '' );\n";
                }

                $php .= "        }\n\n";
            }
        }
        else
        {
            $php .= "        // This event has no settings of its own.\n";
        }

        $php .= "        return true;\n    }\n\n";

        // execute()
        $php .= self::executeMethod( $settings );

        $php .= "}\n\n";
        $php .= "eZWorkflowEventType::registerEventType( " . $class . "::WORKFLOW_TYPE_STRING, '" . $class . "' );\n";

        return $php;
    }

    /**
     * Text on its way into single quotes in generated php, callable by name.
     *
     * @param string $value
     * @return string
     */
    public static function phpStringPublic( $value )
    {
        return str_replace( array( '\\', "'" ), array( '\\\\', "\\'" ), (string) $value );
    }

    /**
     * The execute() method, written around the statuses that were chosen.
     *
     * Each one gets a branch with a comment saying what returning it makes
     * happen, so the person filling this in does not have to go and find out.
     *
     * @param array $settings
     * @return string
     */
    protected static function executeMethod( array $settings )
    {
        $statuses = self::statuses();

        $php  = "    /**\n     * What the event does when the workflow reaches it.\n     *\n";
        $php .= "     * \$process carries the parameters of the operation that triggered the\n";
        $php .= "     * workflow - for content/publish those are object_id and version.\n";
        $php .= "     * \$event carries this event's own settings, read through the constants\n";
        $php .= "     * at the top of this class.\n     *\n";
        $php .= "     * @param eZWorkflowProcess \$process\n";
        $php .= "     * @param eZWorkflowEvent \$event\n";
        $php .= "     * @return int one of the statuses below.\n     */\n";
        $php .= "    function execute( \$process, \$event )\n    {\n";
        $php .= "        \$parameters = \$process->attribute( 'parameter_list' );\n\n";

        // Reading the settings back.
        if ( count( $settings['attributes'] ) )
        {
            $php .= "        // This event's own settings.\n";
            foreach ( $settings['attributes'] as $attribute )
            {
                $variable = '$' . $attribute['name'];
                $read = "\$event->attribute( self::" . strtoupper( $attribute['name'] ) . " )";

                switch ( $attribute['type'] )
                {
                    case 'integer':
                        $php .= "        " . $variable . " = (int) " . $read . ";\n";
                        break;
                    case 'checkbox':
                        $php .= "        " . $variable . " = (bool) " . $read . ";\n";
                        break;
                    case 'idlist':
                    case 'classattribute':
                        $php .= "        " . $variable . " = array_filter( explode( ',', (string) " . $read . " ), 'strlen' );\n";
                        break;
                    default:
                        $php .= "        " . $variable . " = (string) " . $read . ";\n";
                }
            }
            $php .= "\n";
        }

        // Content events almost always want the object; say so rather than
        // making the reader work it out.
        if ( isset( $settings['triggers']['content'] ) )
        {
            $php .= "        // Every content operation passes the object it is about. An object\n";
            $php .= "        // that has gone between the trigger and here is not an error worth\n";
            $php .= "        // stopping a workflow over - there is simply nothing left to act on.\n";
            $php .= "        if ( isset( \$parameters['object_id'] ) )\n        {\n";
            $php .= "            \$object = eZContentObject::fetch( \$parameters['object_id'] );\n";
            $php .= "            if ( !\$object instanceof eZContentObject )\n";
            $php .= "            {\n";
            $php .= "                eZDebug::writeNotice( 'Object ' . \$parameters['object_id'] . ' is gone; nothing to do.',\n";
            $php .= "                                      __METHOD__ );\n\n";
            $php .= "                return eZWorkflowType::" . ( in_array( 'STATUS_WORKFLOW_CANCELLED', $settings['statuses'], true )
                                                                  ? 'STATUS_WORKFLOW_CANCELLED' : 'STATUS_ACCEPTED' ) . ";\n";
            $php .= "            }\n        }\n\n";
        }

        $php .= "        // ─── What this event decides ─────────────────────────────────────\n";
        $php .= "        //\n";
        $php .= "        // Fill this in. Each status below says what returning it makes happen;\n";
        $php .= "        // the one that is returned now is the one that lets the workflow carry\n";
        $php .= "        // on, so an unfinished event is harmless rather than blocking.\n";
        $php .= "        //\n";

        foreach ( $settings['statuses'] as $status )
        {
            if ( !isset( $statuses[$status] ) )
                continue;

            $php .= "        // eZWorkflowType::" . $status . "\n";
            $php .= "        //     " . wordwrap( $statuses[$status]['what'], 68, "\n        //     " ) . "\n";

            // The ones that need something set before they are returned.
            switch ( $status )
            {
                case 'STATUS_DEFERRED_TO_CRON_REPEAT':
                    $php .= "        //     Set \$this->setActivationDate( \$timestamp ) first, or the\n";
                    $php .= "        //     cronjob will come back to it every time it runs.\n";
                    break;
                case 'STATUS_FETCH_TEMPLATE':
                case 'STATUS_FETCH_TEMPLATE_REPEAT':
                    $php .= "        //     Set \$this->setTemplateName( '<template>.tpl' ) first, and put\n";
                    $php .= "        //     that template in design/standard/templates/workflow/.\n";
                    break;
                case 'STATUS_REDIRECT':
                case 'STATUS_REDIRECT_REPEAT':
                    $php .= "        //     Set \$this->setRedirectUrl( \$url ) first.\n";
                    break;
                case 'STATUS_REJECTED':
                    $php .= "        //     Say why with \$this->setInformation( \$text ), or the editor is\n";
                    $php .= "        //     told only that it was refused.\n";
                    break;
            }

            $php .= "        //\n";
        }

        $php .= "\n        return eZWorkflowType::STATUS_ACCEPTED;\n    }\n";

        return $php;
    }

    /**
     * The form an editor fills in when the event is added to a workflow.
     *
     * @param array $settings
     * @return string
     */
    protected static function editTemplate( array $settings )
    {
        $event  = $settings['event'];
        $domain = 'design/standard/workflow/eventtype/edit';

        $tpl  = "{* " . $settings['label'] . " - what an editor fills in.\n\n";
        $tpl .= "   The field names are fixed by convention:\n";
        $tpl .= "   WorkflowEvent_event_" . $event . "_<field>_{\$event.id}\n";
        $tpl .= "   fetchHTTPInput() in the event type reads exactly these. *}\n\n";

        if ( !count( $settings['attributes'] ) )
        {
            $tpl .= "<div class=\"element\">\n";
            $tpl .= "<p>{'This event has no settings.'|i18n( '" . $domain . "' )}</p>\n";
            $tpl .= "</div>\n";

            return $tpl;
        }

        foreach ( $settings['attributes'] as $attribute )
        {
            $field = 'WorkflowEvent_event_' . $event . '_' . $attribute['name'] . '_{$event.id}';
            $value = '{$event.' . $attribute['column'] . '|wash}';

            $tpl .= "<div class=\"element\">\n";
            $tpl .= "    <label for=\"" . $attribute['name'] . "_{\$event.id}\">"
                  . "{'" . self::templateString( $attribute['label'] ) . "'|i18n( '" . $domain . "' )}</label>\n";
            $tpl .= "    <div class=\"labelbreak\"></div>\n";

            switch ( $attribute['type'] )
            {
                case 'checkbox':
                    $tpl .= "    <input type=\"checkbox\" id=\"" . $attribute['name'] . "_{\$event.id}\" name=\"" . $field . "\""
                          . "{if \$event." . $attribute['column'] . "} checked=\"checked\"{/if} />\n";
                    break;

                case 'select':
                    $tpl .= "    <select id=\"" . $attribute['name'] . "_{\$event.id}\" name=\"" . $field . "\">\n";
                    foreach ( $attribute['choices'] as $choice )
                    {
                        $safe = self::templateString( $choice );
                        $tpl .= "        <option value=\"" . $safe . "\""
                              . "{if eq( \$event." . $attribute['column'] . ", '" . $safe . "' )} selected=\"selected\"{/if}>"
                              . $safe . "</option>\n";
                    }
                    $tpl .= "    </select>\n";
                    break;

                case 'integer':
                    $tpl .= "    <input class=\"box\" type=\"text\" size=\"8\" id=\"" . $attribute['name'] . "_{\$event.id}\" "
                          . "name=\"" . $field . "\" value=\"" . $value . "\" />\n";
                    break;

                case 'classattribute':
                    $tpl .= "    {* The class and attribute pickers the base type provides. *}\n";
                    $tpl .= "    <select name=\"" . $field . "[]\" multiple=\"multiple\" size=\"5\">\n";
                    $tpl .= "    {foreach \$event.workflow_type.contentclassattribute_list as \$" . $event . "_attribute}\n";
                    $tpl .= "        <option value=\"{\$" . $event . "_attribute.id|wash}\">"
                          . "{\$" . $event . "_attribute.id|wash}-{\$" . $event . "_attribute.name|wash}</option>\n";
                    $tpl .= "    {/foreach}\n    </select>\n";
                    break;

                case 'idlist':
                    $tpl .= "    <input class=\"box\" type=\"text\" size=\"40\" id=\"" . $attribute['name'] . "_{\$event.id}\" "
                          . "name=\"" . $field . "\" value=\"" . $value . "\" />\n";
                    break;

                default:
                    $tpl .= "    <input class=\"box\" type=\"text\" size=\"40\" id=\"" . $attribute['name'] . "_{\$event.id}\" "
                          . "name=\"" . $field . "\" value=\"" . $value . "\" />\n";
            }

            if ( $attribute['help'] !== '' )
                $tpl .= "    <p class=\"small\">{'" . self::templateString( $attribute['help'] ) . "'|i18n( '" . $domain . "' )}</p>\n";

            $tpl .= "</div>\n\n";
        }

        return $tpl;
    }

    /**
     * What the workflow list shows about the event once it is set up.
     *
     * @param array $settings
     * @return string
     */
    protected static function viewTemplate( array $settings )
    {
        $domain = 'design/standard/workflow/eventtype/view';

        $tpl  = "{* " . $settings['label'] . " - what the workflow list shows. *}\n\n";
        $tpl .= "<div class=\"element\">\n";

        if ( !count( $settings['attributes'] ) )
        {
            $tpl .= "<p>{'" . self::templateString( $settings['label'] ) . "'|i18n( '" . $domain . "' )}</p>\n";
            $tpl .= "</div>\n";

            return $tpl;
        }

        $tpl .= "<table class=\"list\" cellspacing=\"0\">\n";
        foreach ( $settings['attributes'] as $attribute )
        {
            $tpl .= "<tr>\n    <th>{'" . self::templateString( $attribute['label'] ) . "'|i18n( '" . $domain . "' )}</th>\n";

            if ( $attribute['type'] === 'checkbox' )
            {
                $tpl .= "    <td>{if \$event." . $attribute['column'] . "}"
                      . "{'Yes'|i18n( '" . $domain . "' )}{else}{'No'|i18n( '" . $domain . "' )}{/if}</td>\n";
            }
            else
            {
                $tpl .= "    <td>{\$event." . $attribute['column'] . "|wash}</td>\n";
            }

            $tpl .= "</tr>\n";
        }
        $tpl .= "</table>\n</div>\n";

        return $tpl;
    }

    /**
     * Text on its way into a single-quoted template string.
     *
     * @param string $value
     * @return string
     */
    protected static function templateString( $value )
    {
        return str_replace( array( '\\', "'", '{', '}' ),
                            array( '\\\\', "\\'", '', '' ),
                            (string) $value );
    }

    /**
     * workflow.ini: where the event type lives and that it exists.
     *
     * @param array $settings
     * @return string
     */
    protected static function workflowIni( array $settings )
    {
        $ini  = self::iniHeader( $settings, 'Workflow event registration' );
        $ini .= "[EventSettings]\n";
        $ini .= "# Where to look for event types, and which one this extension adds. Both\n";
        $ini .= "# are needed: the directory alone does not make an event available.\n";
        $ini .= "ExtensionDirectories[]=" . $settings['name'] . "\n";
        $ini .= "AvailableEventTypes[]=event_" . $settings['event'] . "\n\n";
        $ini .= "*/ ?>\n";

        return $ini;
    }

    /**
     * design.ini: so the two templates are found.
     *
     * @param array $settings
     * @return string
     */
    protected static function designIni( array $settings )
    {
        $ini  = self::iniHeader( $settings, 'Design settings' );
        $ini .= "[ExtensionSettings]\n";
        $ini .= "# Puts this extension's templates into the design chain, which is how the\n";
        $ini .= "# event's edit and view templates are found.\n";
        $ini .= "DesignExtensions[]=" . $settings['name'] . "\n\n";
        $ini .= "*/ ?>\n";

        return $ini;
    }

    /**
     * What the extension is, how to switch it on, and what the event does.
     *
     * @param array $settings
     * @param array $paths
     * @return string
     */
    protected static function readme( array $settings, array $paths = array() )
    {
        $statuses = self::statuses();
        $class    = self::className( $settings );

        $readme  = "# " . $settings['title'] . "\n\n" . $settings['summary'] . "\n\n";
        $readme .= "A workflow event type for Exponential / eZ Publish legacy, built with the\n";
        $readme .= "workflow event wizard in Setup > RAD.\n\n";

        $readme .= "## When it runs\n\n";
        $readme .= "It can be attached to a workflow at these points, and nowhere else:\n\n";
        foreach ( self::triggerSentences( $settings ) as $sentence )
            $readme .= "- `" . $sentence . "`\n";
        $readme .= "\n";
        $readme .= "*before* runs while the operation is still deciding, so rejecting the event\n";
        $readme .= "stops the operation. *after* runs once it has happened, so rejecting it then\n";
        $readme .= "stops the rest of the workflow but not the thing itself.\n\n";

        if ( count( $settings['attributes'] ) )
        {
            $readme .= "## What an editor sets\n\n";
            $readme .= "| Setting | Kind | Kept in |\n|---|---|---|\n";
            foreach ( $settings['attributes'] as $attribute )
                $readme .= "| " . $attribute['label'] . " | " . $attribute['type'] . " | `" . $attribute['column'] . "` |\n";

            $readme .= "\nThose columns are `eZWorkflowEvent`'s own, and it gives them no meaning.\n";
            $readme .= "The constants at the top of `" . $class . "` are that meaning; read the\n";
            $readme .= "settings through them rather than by column name.\n\n";
        }

        $readme .= "## What it answers\n\n";
        foreach ( $settings['statuses'] as $status )
        {
            if ( !isset( $statuses[$status] ) )
                continue;
            $readme .= "**`" . $status . "`** — " . $statuses[$status]['what'] . "\n\n";
        }

        $readme .= "`execute()` returns `STATUS_ACCEPTED` as it stands, so the event is harmless\n";
        $readme .= "until it is filled in: a workflow carrying it will not block.\n\n";

        if ( $settings['parts']['cronjob'] )
        {
            $readme .= "## Deferring\n\n";
            $readme .= "An event that answers `STATUS_DEFERRED_TO_CRON` or\n";
            $readme .= "`STATUS_DEFERRED_TO_CRON_REPEAT` is not finished; it is waiting. What comes\n";
            $readme .= "back to it is the `workflow` cronjob part:\n\n";
            $readme .= "```sh\nphp runcronjobs.php -s <siteaccess> workflow\n```\n\n";
            $readme .= "Without that running on a schedule, a deferred workflow waits for ever.\n";
            $readme .= "Setup > Cronjobs shows whether it is scheduled.\n\n";
        }

        $readme .= "## Switching it on\n\n";
        $readme .= "Add it to the active extensions in `settings/override/site.ini.append.php`:\n\n";
        $readme .= "```ini\n" . self::activation( $settings ) . "\n```\n\n";
        $readme .= "Then clear the caches:\n\n```sh\nphp bin/php/ezcache.php --clear-all\n```\n\n";
        $readme .= "The event then appears in **Setup > Workflows** when an event is added to a\n";
        $readme .= "workflow, and the workflow is attached to an operation under **Triggers**.\n\n";

        $readme .= "## Licence\n\n" . self::licenceLine( $settings ) . "\n\n";

        if ( count( $paths ) )
        {
            $readme .= "## What is inside\n\n";
            foreach ( $paths as $path )
                $readme .= "- `" . $path . "`\n";
        }

        return $readme;
    }
}
