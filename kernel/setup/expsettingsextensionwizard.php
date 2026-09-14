<?php
/**
 * The settings extension wizard: an extension that is mostly ini files.
 *
 * A good deal of what this system can be told to do differently is told in
 * settings rather than in code. None of it is hard; all of it is in a shape
 * nobody remembers, spread over half a dozen files, with a rule about where the
 * file has to live for anything to read it at all.
 *
 * So this writes those files. Six things it knows about:
 *
 *   image aliases       a size and a set of filters, available to every template
 *   event listeners     a callback on a kernel event, plus the class behind it
 *   view cache rules    what else has to be rebuilt when one object is published
 *   collected info      what happens when a visitor fills in a form
 *   trigger operations  which operations a workflow may be bound to
 *   siteaccess settings settings that apply to one siteaccess only
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

require_once 'kernel/setup/expextensionwizard.php';

class expSettingsExtensionWizard extends expExtensionWizard
{
    /**
     * What wrote the files, for the head of each one.
     *
     * @return string
     */
    public static function wizardName()
    {
        return 'settings extension wizard';
    }

    // ── What it can write ────────────────────────────────────────────────────

    /**
     * The six, each its own ini file and its own set of questions.
     *
     * @return array
     */
    public static function topics()
    {
        return array(

        'image' => array(
            'label'   => 'Image aliases',
            'ini'     => 'image.ini',
            'default' => true,
            'summary' => 'A named size, with the filters that produce it.',
            'what'    => 'Every image attribute is stored once and served in as many sizes as there are aliases. A template asks for one by name; the file is made the first time it is asked for and kept. Adding an alias costs nothing until something asks for it, and removing one that content still asks for leaves broken images.',
            'note'    => 'Aliases are generated on demand, so a new one appears on old content as soon as a template asks. Changing the filters on an existing alias does not: the old files have to be cleared with bin/php/ezimagealias.php or they are served as they were.' ),

        'event' => array(
            'label'   => 'Event listeners',
            'ini'     => 'site.ini',
            'default' => false,
            'summary' => 'A callback on something the kernel announces.',
            'what'    => 'The kernel announces a few dozen things as they happen - a cache being cleared, a request arriving, a response about to be sent - and anything listening is called. It is the lightest way to add behaviour: no module, no handler, no class to replace, just a static method that runs when something happens.',
            'note'    => 'Two kinds of event. A notify event tells you something happened and ignores what you return. A filter event hands you a value and uses what you give back, so a filter listener that forgets to return the value destroys it.' ),

        'viewcache' => array(
            'label'   => 'View cache clearing rules',
            'ini'     => 'viewcache.ini',
            'default' => false,
            'summary' => 'What else has to be rebuilt when one object is published.',
            'what'    => 'Publishing clears the cache for the object, its parents and what relates to it. Anything else showing that content - a listing, a count, a menu somewhere else entirely - keeps showing what it showed before. These rules are how the system is told about those.',
            'note'    => 'SmartCacheClear has to be enabled for any of this to be read, and the group has to be named after the class identifier exactly. A rule under a name no class has is not an error; it simply never runs.' ),

        'collect' => array(
            'label'   => 'Information collection',
            'ini'     => 'collect.ini',
            'default' => false,
            'summary' => 'What happens when a visitor fills in a form built out of content.',
            'what'    => 'A poll, a contact form and a booking are all the same thing: a content class with information collector attributes. What separates them is here - what the submission is called, whether it is kept, whether it is emailed, and what the visitor is shown afterwards. All of it matched per content class.',
            'note'    => 'Collected information is stored against the object rather than in it, so it survives the content being edited and is not carried by a package. Whoever owns the site owns that data.' ),

        'trigger' => array(
            'label'   => 'Trigger operations',
            'ini'     => 'workflow.ini',
            'default' => false,
            'summary' => 'Which operations a workflow may be bound to.',
            'what'    => 'A workflow runs at a trigger, and a trigger is an operation plus a moment - before or after. Only the operations listed here can be bound to in the admin; the rest are invisible, however much code is behind them.',
            'note'    => 'Listing an operation makes it bindable. It does not bind anything: that is done in the admin, under Setup, and is a row in the database rather than a setting.' ),

        'siteaccess' => array(
            'label'   => 'Siteaccess settings',
            'ini'     => 'site.ini',
            'default' => false,
            'summary' => 'Settings that apply to one siteaccess only, kept with the extension.',
            'what'    => 'Settings in settings/siteaccess/ belong to the installation and are awkward to deploy with an extension. The same settings under an extension travel with it, and can be switched on and off with it.',
            'note'    => 'These are only read when the extension is listed in ActiveAccessExtensions[] as well as ActiveExtensions[]. Being active is not enough, and this is the line that is forgotten.' ),
        );
    }

    /**
     * The image filters that ship, with what each one takes.
     *
     * Every one of these is supported by both converters. There are more that
     * are not, and an alias using one of those works until the day somebody
     * installs the site without ImageMagick.
     *
     * @return array
     */
    public static function filters()
    {
        return array(
            'geometry/scale'                => array( 'takes' => 'width;height', 'what' => 'Fits the image inside a box, keeping its shape. Enlarges a small image to fill it.' ),
            'geometry/scaledownonly'        => array( 'takes' => 'width;height', 'what' => 'The same, but never enlarges. What almost every alias wants: a thumbnail of a small image should stay small rather than go soft.' ),
            'geometry/scaleexact'           => array( 'takes' => 'width;height', 'what' => 'Forces exactly this size, changing the shape of the image to do it.' ),
            'geometry/scalewidth'           => array( 'takes' => 'width',        'what' => 'Sets the width and lets the height follow.' ),
            'geometry/scalewidthdownonly'   => array( 'takes' => 'width',        'what' => 'The same, but never enlarges.' ),
            'geometry/scaleheight'          => array( 'takes' => 'height',       'what' => 'Sets the height and lets the width follow.' ),
            'geometry/scaleheightdownonly'  => array( 'takes' => 'height',       'what' => 'The same, but never enlarges.' ),
            'geometry/scalepercent'         => array( 'takes' => 'x;y',          'what' => 'Scales by a percentage rather than to a size.' ),
            'geometry/crop'                 => array( 'takes' => 'x;y;w;h',      'what' => 'Cuts a rectangle out. Combined with a scale this is how a fixed size thumbnail is made without distortion.' ),
            'colorspace/gray'               => array( 'takes' => '',             'what' => 'Takes the colour out.' ),
            'colorspace/transparent'        => array( 'takes' => '',             'what' => 'Keeps transparency through the conversion. Without it a transparent png can come out with a black background.' ),
            'filter/swirl'                  => array( 'takes' => 'degrees',      'what' => 'Twists the image. Of no use to anybody, and in every example ever written.' ),
            'border/color'                  => array( 'takes' => 'colour',       'what' => 'A border in a named or hex colour.' ),
            'flatten'                       => array( 'takes' => '',             'what' => 'Flattens layers into one. Needed for some source formats before anything else will work.' ),
        );
    }

    /**
     * The events the kernel announces, and which kind each is.
     *
     * A notify event ignores what a listener returns. A filter event uses it -
     * so a filter listener that forgets to return the value destroys it, which
     * is the one mistake worth guarding against here.
     *
     * @return array
     */
    public static function events()
    {
        return array(
            'request/preinput'   => array( 'kind' => 'filter', 'what' => 'The request has arrived and nothing has looked at it yet.' ),
            'request/input'      => array( 'kind' => 'filter', 'what' => 'The request, after the kernel has read it.' ),
            'response/preoutput' => array( 'kind' => 'filter', 'what' => 'The page has been built and is about to be wrapped. The last place to change what a template produced.' ),
            'response/output'    => array( 'kind' => 'filter', 'what' => 'The whole page, about to be sent. Whatever is returned is what the browser gets.' ),
            'content/view'       => array( 'kind' => 'filter', 'what' => 'A node is about to be viewed; the node id is passed and the one returned is used. How a request for one node is answered with another.' ),
            'content/cache'      => array( 'kind' => 'notify', 'what' => 'View caches are being cleared for a list of nodes.' ),
            'content/cache/all'  => array( 'kind' => 'notify', 'what' => 'Every view cache is being cleared.' ),
            'content/cache/version' => array( 'kind' => 'notify', 'what' => 'One version of one object had its cache cleared.' ),
            'content/download'   => array( 'kind' => 'notify', 'what' => 'A file attribute is being served. Where a download count belongs.' ),
            'content/class/cache' => array( 'kind' => 'notify', 'what' => 'A content class changed and its cache is going.' ),
            'content/section/cache' => array( 'kind' => 'notify', 'what' => 'A section changed.' ),
            'content/state/assign' => array( 'kind' => 'notify', 'what' => 'An object state was assigned to an object.' ),
            'content/translations/cache' => array( 'kind' => 'notify', 'what' => 'The list of languages changed.' ),
            'image/alias'        => array( 'kind' => 'notify', 'what' => 'An image alias was generated. Where a copy to somewhere else belongs.' ),
            'image/purgeAliases' => array( 'kind' => 'notify', 'what' => 'Generated image files are being removed for good.' ),
            'image/removeAliases' => array( 'kind' => 'notify', 'what' => 'Generated image files are being removed.' ),
            'image/trashAliases' => array( 'kind' => 'notify', 'what' => 'An object with images went to the trash, so its aliases went with it.' ),
            'session/regenerate' => array( 'kind' => 'notify', 'what' => 'A session was given a new id, which happens on login.' ),
            'session/destroy'    => array( 'kind' => 'notify', 'what' => 'A session was forgotten, which happens on logout.' ),
            'session/cleanup'    => array( 'kind' => 'notify', 'what' => 'Every session was forgotten.' ),
            'session/gc'         => array( 'kind' => 'notify', 'what' => 'Old sessions were collected.' ),
            'user/cache/all'     => array( 'kind' => 'notify', 'what' => 'Every user cache is going, which happens when roles change.' ),
        );
    }

    /**
     * The ways a view cache rule can reach other content.
     *
     * @return array
     */
    public static function clearMethods()
    {
        return array(
            'object'    => 'The object itself, and nothing else.',
            'parent'    => 'Its parents, up to MaxParents.',
            'relating'  => 'Everything that relates to it. The expensive one, and usually the one that was wanted.',
            'keyword'   => 'Everything sharing a keyword with it.',
            'siblings'  => 'Everything beside it under the same parent. What a listing of a folder needs.',
            'children'  => 'Everything under it.',
            'all'       => 'The whole view cache. Correct, ruinous, and occasionally the only thing that works.',
            'none'      => 'Nothing at all. For content that is never shown anywhere but on its own page.',
        );
    }

    /**
     * What a collected information form can be told to do.
     *
     * @return array
     */
    public static function collectTypes()
    {
        return array(
            'form'     => 'A form. Kept, and the visitor is shown a thank you.',
            'poll'     => 'A poll. Kept, and the visitor is shown the result.',
            'feedback' => 'Feedback. Emailed, and not necessarily kept.',
        );
    }

    // ── What this wizard can put in ──────────────────────────────────────────

    /**
     * @return array
     */
    public static function parts()
    {
        $parts = array();

        foreach ( self::topics() as $key => $topic )
            $parts[$key] = array(
                'label'       => $topic['label'],
                'description' => $topic['summary'] . ' Written to ' . $topic['ini'] . '.',
                'default'     => $topic['default'] );

        return array_merge( $parts, array(
            'listener' => array(
                'label' => 'Listener class',
                'description' => 'The class the event listeners point at, with a method per event and a note saying whether what it returns is used or ignored. Written only when events are chosen.',
                'default' => true ),
            'readme' => array(
                'label' => 'README.md',
                'description' => 'What each setting does, why it is where it is, and what has to be cleared before it takes effect.',
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
            'gitignore' => array(
                'label' => '.gitignore',
                'description' => 'Keeps editor leftovers and build output out of the repository.',
                'default' => true ),
            'licence' => array(
                'label' => 'LICENSE',
                'description' => 'The licence text named below. On by default: an extension with no licence file says nothing about how it may be used.',
                'default' => true ),
        ) );
    }

    // ── Settings ─────────────────────────────────────────────────────────────

    /**
     * Everything the wizard was asked for.
     *
     * @param array $input
     * @return array
     */
    public static function settings( array $input )
    {
        $settings = array(
            'name'     => self::safeName( isset( $input['name'] ) ? $input['name'] : '' ),
            'class'    => self::safeClass( isset( $input['class'] ) ? $input['class'] : '' ),
            'title'    => self::text( isset( $input['title'] ) ? $input['title'] : '', 120 ),
            'summary'  => self::text( isset( $input['summary'] ) ? $input['summary'] : '', 250 ),
            'author'   => self::text( isset( $input['author'] ) ? $input['author'] : '', 120 ),
            'vendor'   => self::safeName( isset( $input['vendor'] ) ? $input['vendor'] : '', true ),
            'version'  => self::text( isset( $input['version'] ) ? $input['version'] : '', 20 ),
            'licence'  => self::licence_id( isset( $input['licence'] ) ? $input['licence'] : '' ),
            'siteaccess' => self::safeIdentifier( isset( $input['siteaccess'] ) ? $input['siteaccess'] : '' ),
        );

        $settings['aliases']   = self::aliasList( isset( $input['aliases'] ) ? $input['aliases'] : '' );
        $settings['events']    = self::chosenEvents( isset( $input['events'] ) ? $input['events'] : null );
        $settings['rules']     = self::ruleList( isset( $input['rules'] ) ? $input['rules'] : '' );
        $settings['forms']     = self::formList( isset( $input['forms'] ) ? $input['forms'] : '' );
        $settings['operations']= self::operationList( isset( $input['operations'] ) ? $input['operations'] : '' );
        $settings['overrides'] = self::overrideList( isset( $input['overrides'] ) ? $input['overrides'] : '' );

        if ( $settings['class'] === '' && $settings['name'] !== '' )
            $settings['class'] = self::safeClass( str_replace( '_', '', $settings['name'] ) . 'Listener' );
        if ( $settings['title'] === '' && $settings['name'] !== '' )
            $settings['title'] = ucwords( str_replace( '_', ' ', $settings['name'] ) );
        if ( $settings['version'] === '' )
            $settings['version'] = '1.0.0';
        if ( $settings['vendor'] === '' )
            $settings['vendor'] = 'exponential';
        if ( $settings['summary'] === '' )
            $settings['summary'] = 'Settings for Exponential.';

        $chosen = isset( $input['parts'] ) && is_array( $input['parts'] ) ? $input['parts'] : null;
        foreach ( self::parts() as $key => $part )
            $settings['parts'][$key] = $chosen === null ? $part['default'] : in_array( $key, $chosen, true );

        return $settings;
    }

    /**
     * The image aliases, one per line: a name, then the filters.
     *
     * @param mixed $value
     * @return array
     */
    public static function aliasList( $value )
    {
        if ( !is_scalar( $value ) )
            return array();

        $known   = self::filters();
        $aliases = array();

        foreach ( preg_split( '/[\r\n]+/', (string) $value ) as $line )
        {
            $line = trim( $line );
            if ( $line === '' )
                continue;

            $colon = strpos( $line, ':' );
            $name  = self::safeIdentifier( $colon === false ? $line : substr( $line, 0, $colon ) );

            if ( $name === '' )
                continue;

            $filters = array();
            if ( $colon !== false )
                foreach ( explode( ',', substr( $line, $colon + 1 ) ) as $filter )
                {
                    $filter = trim( $filter );
                    if ( $filter === '' )
                        continue;

                    // A filter is a name, then optionally = and its arguments.
                    // Nothing else may be in it: whatever is written here ends
                    // up on a command line when ImageMagick is the converter.
                    if ( !preg_match( '#^([a-z]+(?:/[a-z]+)?)(?:=([0-9;.,%-]*))?$#i', $filter, $found ) )
                        continue;

                    $filters[] = array( 'name'  => $found[1],
                                        'args'  => isset( $found[2] ) ? $found[2] : '',
                                        'known' => isset( $known[$found[1]] ) );
                }

            $aliases[] = array( 'name' => $name, 'filters' => $filters );

            if ( count( $aliases ) >= 30 )
                break;
        }

        return $aliases;
    }

    /**
     * Which events were chosen, keeping only ones the kernel announces.
     *
     * @param mixed $value
     * @return array of string
     */
    public static function chosenEvents( $value )
    {
        if ( !is_array( $value ) )
            return array();

        $known  = self::events();
        $chosen = array();

        foreach ( $value as $event )
            if ( is_string( $event ) && isset( $known[$event] ) && !in_array( $event, $chosen, true ) )
                $chosen[] = $event;

        return $chosen;
    }

    /**
     * The view cache rules, one per line: a class identifier, then the methods.
     *
     * @param mixed $value
     * @return array
     */
    public static function ruleList( $value )
    {
        if ( !is_scalar( $value ) )
            return array();

        $methods = self::clearMethods();
        $rules   = array();

        foreach ( preg_split( '/[\r\n]+/', (string) $value ) as $line )
        {
            $line = trim( $line );
            if ( $line === '' )
                continue;

            $colon = strpos( $line, ':' );
            $class = self::safeIdentifier( $colon === false ? $line : substr( $line, 0, $colon ) );

            if ( $class === '' )
                continue;

            $chosen = array();
            $depends = array();

            if ( $colon !== false )
                foreach ( explode( ',', substr( $line, $colon + 1 ) ) as $word )
                {
                    $word = self::safeIdentifier( $word );

                    if ( $word === '' )
                        continue;

                    if ( isset( $methods[$word] ) )
                        $chosen[] = $word;
                    else
                        $depends[] = $word;
                }

            $rules[] = array( 'class'   => $class,
                              'methods' => array_values( array_unique( $chosen ) ),
                              'depends' => array_values( array_unique( $depends ) ) );

            if ( count( $rules ) >= 30 )
                break;
        }

        return $rules;
    }

    /**
     * The collected information forms, one per line: a class, then a type.
     *
     * @param mixed $value
     * @return array
     */
    public static function formList( $value )
    {
        if ( !is_scalar( $value ) )
            return array();

        $types = self::collectTypes();
        $forms = array();

        foreach ( preg_split( '/[\r\n]+/', (string) $value ) as $line )
        {
            $line = trim( $line );
            if ( $line === '' )
                continue;

            $bits  = preg_split( '/[\s,:]+/', $line );
            $class = self::safeIdentifier( isset( $bits[0] ) ? $bits[0] : '' );

            if ( $class === '' )
                continue;

            $type = isset( $bits[1] ) && isset( $types[strtolower( $bits[1] )] ) ? strtolower( $bits[1] ) : 'form';
            $mail = !isset( $bits[2] ) || !in_array( strtolower( $bits[2] ), array( 'nomail', 'no', 'off' ), true );

            $forms[] = array( 'class' => $class, 'type' => $type, 'mail' => $mail );

            if ( count( $forms ) >= 30 )
                break;
        }

        return $forms;
    }

    /**
     * The trigger operations to make bindable.
     *
     * @param mixed $value
     * @return array of string
     */
    public static function operationList( $value )
    {
        if ( is_array( $value ) )
            $value = implode( ',', array_filter( $value, 'is_scalar' ) );

        if ( !is_scalar( $value ) )
            return array();

        $operations = array();

        foreach ( preg_split( '/[\s,;]+/', (string) $value ) as $operation )
        {
            $operation = self::safeIdentifier( $operation );

            if ( $operation !== '' && !in_array( $operation, $operations, true ) )
                $operations[] = $operation;

            if ( count( $operations ) >= 40 )
                break;
        }

        return $operations;
    }

    /**
     * The siteaccess settings, one per line: file, section, setting, value.
     *
     * @param mixed $value
     * @return array
     */
    public static function overrideList( $value )
    {
        if ( !is_scalar( $value ) )
            return array();

        $overrides = array();

        foreach ( preg_split( '/[\r\n]+/', (string) $value ) as $line )
        {
            $line = trim( $line );
            if ( $line === '' )
                continue;

            // site.ini [SiteSettings] DefaultPage=content/view/full/2
            if ( !preg_match( '/^([a-z0-9_]+\.ini)\s*\[([A-Za-z0-9_-]+)\]\s*([A-Za-z][A-Za-z0-9_]*(?:\[[^\]]*\])?)\s*=\s*(.*)$/i',
                              $line, $found ) )
                continue;

            $overrides[] = array( 'ini'      => strtolower( $found[1] ),
                                  'section'  => $found[2],
                                  'variable' => $found[3],
                                  'value'    => self::iniValue( $found[4], 400 ) );

            if ( count( $overrides ) >= 60 )
                break;
        }

        return $overrides;
    }

    /**
     * A class name: letters and digits, starting with a letter.
     *
     * @param string $value
     * @return string
     */
    public static function safeClass( $value )
    {
        if ( !is_string( $value ) )
            return '';

        $value = preg_replace( '/[^A-Za-z0-9_]+/', '', $value );

        return $value !== null && preg_match( '/^[A-Za-z][A-Za-z0-9_]{2,60}$/', $value ) ? $value : '';
    }

    /**
     * A lower case identifier: a class identifier, an alias, a siteaccess.
     *
     * @param mixed $value
     * @return string
     */
    public static function safeIdentifier( $value )
    {
        if ( !is_scalar( $value ) )
            return '';

        $value = strtolower( preg_replace( '/[^A-Za-z0-9_]+/', '_', (string) $value ) );
        $value = trim( (string) $value, '_' );

        return preg_match( '/^[a-z][a-z0-9_]{0,60}$/', $value ) ? $value : '';
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

        if ( count( self::chosenTopics( $settings ) ) === 0 )
            $problems[] = 'Choose at least one thing for this extension to say, or there is nothing to write.';

        if ( $settings['parts']['image'] && count( $settings['aliases'] ) === 0 )
            $problems[] = 'Image aliases were chosen and none were named.';

        if ( $settings['parts']['event'] )
        {
            if ( count( $settings['events'] ) === 0 )
                $problems[] = 'Event listeners were chosen and no events were picked.';

            if ( $settings['class'] === '' )
                $problems[] = 'The listener class needs a name: letters and digits, starting with a letter.';

            if ( $settings['class'] !== '' && class_exists( $settings['class'] ) )
                $problems[] = 'A class called ' . $settings['class'] . ' already exists on this installation. Choose another name.';
        }

        if ( $settings['parts']['viewcache'] && count( $settings['rules'] ) === 0 )
            $problems[] = 'View cache rules were chosen and none were written.';

        if ( $settings['parts']['collect'] && count( $settings['forms'] ) === 0 )
            $problems[] = 'Information collection was chosen and no content class was named.';

        if ( $settings['parts']['trigger'] && count( $settings['operations'] ) === 0 )
            $problems[] = 'Trigger operations were chosen and none were named.';

        if ( $settings['parts']['siteaccess'] )
        {
            if ( $settings['siteaccess'] === '' )
                $problems[] = 'Siteaccess settings were chosen and no siteaccess was named.';

            if ( count( $settings['overrides'] ) === 0 )
                $problems[] = 'Siteaccess settings were chosen and none were written. One per line, as: site.ini [SiteSettings] DefaultPage=content/view/full/2';
        }

        // An alias that already exists would be redefined rather than added,
        // and every image on the site would quietly change size.
        foreach ( self::takenAliases( $settings ) as $alias )
            $problems[] = 'An image alias called ' . $alias . ' already exists on this installation. Writing it again redefines it, and every image served through it changes. Choose another name, or say so deliberately by removing this check.';

        return $problems;
    }

    /**
     * Which of the chosen alias names this installation already has.
     *
     * @param array $settings
     * @return array of string
     */
    public static function takenAliases( array $settings )
    {
        if ( !$settings['parts']['image'] || count( $settings['aliases'] ) === 0 )
            return array();

        $existing = (array) eZINI::instance( 'image.ini' )->variable( 'AliasSettings', 'AliasList' );
        $taken    = array();

        foreach ( $settings['aliases'] as $alias )
            if ( in_array( $alias['name'], $existing, true ) )
                $taken[] = $alias['name'];

        return $taken;
    }

    /**
     * The topics that were chosen and have something to say.
     *
     * @param array $settings
     * @return array of string
     */
    public static function chosenTopics( array $settings )
    {
        $has = array(
            'image'      => count( $settings['aliases'] ),
            'event'      => count( $settings['events'] ),
            'viewcache'  => count( $settings['rules'] ),
            'collect'    => count( $settings['forms'] ),
            'trigger'    => count( $settings['operations'] ),
            'siteaccess' => count( $settings['overrides'] ) && $settings['siteaccess'] !== '' );

        $chosen = array();
        foreach ( self::topics() as $key => $topic )
            if ( !empty( $settings['parts'][$key] ) && !empty( $has[$key] ) )
                $chosen[] = $key;

        return $chosen;
    }

    // ── What it writes ───────────────────────────────────────────────────────

    /**
     * Every file the extension is made of.
     *
     * @param array $settings
     * @return array
     */
    public static function files( array $settings )
    {
        if ( $settings['name'] === '' )
            return array();

        $topics = self::chosenTopics( $settings );

        if ( count( $topics ) === 0 )
            return array();

        $parts = $settings['parts'];
        $files = array();

        if ( in_array( 'image', $topics, true ) )
            $files['settings/image.ini.append.php'] = self::imageIni( $settings );

        if ( in_array( 'event', $topics, true ) )
        {
            $files['settings/site.ini.append.php'] = self::eventIni( $settings );

            if ( $parts['listener'] )
                $files['classes/' . strtolower( $settings['class'] ) . '.php'] = self::listenerClass( $settings );
        }

        if ( in_array( 'viewcache', $topics, true ) )
            $files['settings/viewcache.ini.append.php'] = self::viewcacheIni( $settings );

        if ( in_array( 'collect', $topics, true ) )
            $files['settings/collect.ini.append.php'] = self::collectIni( $settings );

        if ( in_array( 'trigger', $topics, true ) )
            $files['settings/workflow.ini.append.php'] = self::workflowIni( $settings );

        if ( in_array( 'siteaccess', $topics, true ) )
            foreach ( self::siteaccessFiles( $settings ) as $path => $contents )
                $files[$path] = $contents;

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
        $lines = "[ExtensionSettings]\nActiveExtensions[]=" . $settings['name'];

        if ( in_array( 'siteaccess', self::chosenTopics( $settings ), true ) )
        {
            $lines .= "\n\n# Siteaccess settings need this as well. Being active is not enough,\n";
            $lines .= "# and this is the line that is forgotten.\n";
            $lines .= "ActiveAccessExtensions[]=" . $settings['name'];
        }

        return $lines;
    }

    // ── The ini files ────────────────────────────────────────────────────────

    /**
     * image.ini: the aliases, and the list that makes them reachable.
     *
     * @param array $settings
     * @return string
     */
    protected static function imageIni( array $settings )
    {
        $known = self::filters();

        $ini  = self::iniHeader( $settings, 'Image aliases' );
        $ini .= "[AliasSettings]\n";
        $ini .= "# Adding to the list rather than replacing it. A bare AliasList[] above one\n";
        $ini .= "# of these lines would throw away every alias the system already has, and\n";
        $ini .= "# every template asking for one would get nothing.\n";

        foreach ( $settings['aliases'] as $alias )
            $ini .= "AliasList[]=" . $alias['name'] . "\n";

        foreach ( $settings['aliases'] as $alias )
        {
            $ini .= "\n[" . $alias['name'] . "]\n";
            $ini .= "# Built from the reference image rather than from the original, which is\n";
            $ini .= "# smaller and already right way up.\n";
            $ini .= "Reference=reference\n";

            if ( count( $alias['filters'] ) === 0 )
            {
                $ini .= "\n# No filters, so this alias is the reference image under another name.\n";
                $ini .= "# Add them as, for example:\n";
                $ini .= "# Filters[]=geometry/scaledownonly=200;200\n";
                continue;
            }

            $ini .= "\n# Cleared first, so this list is exactly what runs rather than what runs\n";
            $ini .= "# after whatever anything else said.\n";
            $ini .= "Filters[]\n";

            foreach ( $alias['filters'] as $filter )
            {
                if ( !$filter['known'] )
                    $ini .= "# Not one of the filters that ships. It may be provided by an image\n"
                          . "# converter of your own; if it is not, this alias produces nothing.\n";
                else if ( $known[$filter['name']]['takes'] !== '' && $filter['args'] === '' )
                    $ini .= "# This filter takes " . $known[$filter['name']]['takes'] . " and none were given.\n";

                $ini .= "Filters[]=" . $filter['name'] . ( $filter['args'] !== '' ? '=' . $filter['args'] : '' ) . "\n";
            }
        }

        $ini .= "\n# Aliases are made the first time something asks for one, so a new alias\n";
        $ini .= "# appears on old content by itself. Changing the filters on an existing\n";
        $ini .= "# alias does not: the files already made are served as they are until\n";
        $ini .= "#\n";
        $ini .= "#     php bin/php/ezimagealias.php --remove-aliases\n";
        $ini .= "\n*/ ?>\n";

        return $ini;
    }

    /**
     * site.ini: the listeners.
     *
     * @param array $settings
     * @return string
     */
    protected static function eventIni( array $settings )
    {
        $events = self::events();

        $ini  = self::iniHeader( $settings, 'Event listeners' );
        $ini .= "[Event]\n";
        $ini .= "# Each line is an event, then @, then what to call. Adding to the list\n";
        $ini .= "# rather than replacing it: a bare Listeners[] here would silence every\n";
        $ini .= "# listener anything else registered.\n";

        foreach ( $settings['events'] as $event )
        {
            $method = self::methodFor( $event );

            $ini .= "\n# " . wordwrap( $events[$event]['what'], 72, "\n# " ) . "\n";
            $ini .= "# A " . $events[$event]['kind'] . " event: what the listener returns is "
                  . ( $events[$event]['kind'] === 'filter' ? 'used' : 'ignored' ) . ".\n";
            $ini .= "Listeners[]=" . $event . "@" . $settings['class'] . "::" . $method . "\n";
        }

        $ini .= "\n*/ ?>\n";

        return $ini;
    }

    /**
     * A method name for an event, which has slashes in it and cannot be one.
     *
     * @param string $event
     * @return string
     */
    public static function methodFor( $event )
    {
        $parts = explode( '/', (string) $event );
        $name  = array_shift( $parts );

        foreach ( $parts as $part )
            $name .= ucfirst( $part );

        return preg_replace( '/[^A-Za-z0-9_]/', '', $name );
    }

    /**
     * The class the listeners point at.
     *
     * @param array $settings
     * @return string
     */
    protected static function listenerClass( array $settings )
    {
        $events = self::events();
        $class  = $settings['class'];

        $php  = "<?php\n/**\n * " . $class . " - what runs when the kernel announces something.\n *\n";
        $php .= " * Registered in site.ini [Event] Listeners[], one line per method below.\n";
        $php .= " * Nothing constructs this class: the methods are called statically, by name,\n";
        $php .= " * so renaming one means changing the ini too.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $php .= "class " . $class . "\n{\n";

        $methods = array();
        foreach ( $settings['events'] as $event )
        {
            $method = self::methodFor( $event );
            $filter = $events[$event]['kind'] === 'filter';

            $body  = "    /**\n";
            $body .= "     * " . $event . "\n";
            $body .= "     *\n";
            $body .= "     * " . wordwrap( $events[$event]['what'], 70, "\n     * " ) . "\n";
            $body .= "     *\n";

            if ( $filter )
            {
                $body .= "     * A filter event. Whatever this returns is what the rest of the\n";
                $body .= "     * system uses, so returning nothing destroys the value. The first\n";
                $body .= "     * argument is the value; anything after it is context.\n";
            }
            else
            {
                $body .= "     * A notify event. What this returns is ignored, and nothing waits\n";
                $body .= "     * for it either - anything slow here is felt by whoever caused it.\n";
            }

            $body .= "     */\n";
            $body .= "    public static function " . $method . "( \$value = null )\n    {\n";

            if ( $filter )
            {
                $body .= "        // Not written yet. Handing the value back unchanged leaves the\n";
                $body .= "        // system working exactly as it did before this listener existed.\n";
                $body .= "        return \$value;\n";
            }
            else
            {
                $body .= "        // Not written yet. Doing nothing leaves the system working\n";
                $body .= "        // exactly as it did before this listener existed.\n";
            }

            $body .= "    }";
            $methods[] = $body;
        }

        $php .= implode( "\n\n", $methods ) . "\n}\n";

        return $php;
    }

    /**
     * viewcache.ini: what else has to go when one object is published.
     *
     * @param array $settings
     * @return string
     */
    protected static function viewcacheIni( array $settings )
    {
        $ini  = self::iniHeader( $settings, 'View cache clearing rules' );
        $ini .= "[ViewCacheSettings]\n";
        $ini .= "# Nothing below is read unless this is on. It is off by default, so an\n";
        $ini .= "# extension carrying rules has to turn it on as well as write them.\n";
        $ini .= "SmartCacheClear=enabled\n";

        foreach ( $settings['rules'] as $rule )
        {
            $ini .= "\n# What has to be rebuilt when a " . self::iniValue( $rule['class'], 60 ) . " is published.\n";
            $ini .= "# The group is named after the class identifier exactly. A group under a\n";
            $ini .= "# name no class has is not an error; it simply never runs.\n";
            $ini .= "[" . $rule['class'] . "]\n";

            if ( count( $rule['methods'] ) )
            {
                $ini .= "ClearCacheMethod[]\n";
                foreach ( $rule['methods'] as $method )
                    $ini .= "ClearCacheMethod[]=" . $method . "\n";
            }
            else
            {
                $ini .= "# No method was chosen, so the default applies: the object, its parents\n";
                $ini .= "# and what relates to it.\n";
                $ini .= "# ClearCacheMethod[]=relating\n";
            }

            foreach ( $rule['depends'] as $depends )
                $ini .= "DependentClassIdentifier[]=" . $depends . "\n";

            if ( count( $rule['depends'] ) )
            {
                $ini .= "# Each of those is matched against the parents of the object being\n";
                $ini .= "# published, so this reaches a listing above it rather than beside it.\n";
            }

            $ini .= "MaxParents=0\n";
            $ini .= "# 0 means go all the way up. A number here stops the walk, which matters\n";
            $ini .= "# on a deep tree where publishing one page would otherwise clear a\n";
            $ini .= "# hundred caches.\n";
        }

        $ini .= "\n*/ ?>\n";

        return $ini;
    }

    /**
     * collect.ini: what happens when a visitor fills a form in.
     *
     * @param array $settings
     * @return string
     */
    protected static function collectIni( array $settings )
    {
        $ini  = self::iniHeader( $settings, 'Information collection' );
        $ini .= "# Everything here is matched on the content class identifier, so the same\n";
        $ini .= "# attributes behave differently depending on which class they are in.\n\n";

        $ini .= "[InfoSettings]\n";
        foreach ( $settings['forms'] as $form )
            $ini .= "TypeList[" . $form['class'] . "]=" . $form['type'] . "\n";

        $ini .= "\n[EmailSettings]\n";
        $ini .= "# Whether a submission is emailed as well as kept. The address comes from\n";
        $ini .= "# the attribute marked as the receiver, or from site.ini AdminEmail.\n";
        foreach ( $settings['forms'] as $form )
            $ini .= "SendEmailList[" . $form['class'] . "]=" . ( $form['mail'] ? 'enabled' : 'disabled' ) . "\n";

        $ini .= "\n[CollectionSettings]\n";
        $ini .= "# Whether somebody who is not logged in may submit at all. Off means a\n";
        $ini .= "# visitor is asked to log in first, which is right for some forms and the\n";
        $ini .= "# end of the others.\n";
        foreach ( $settings['forms'] as $form )
            $ini .= "CollectAnonymousDataList[" . $form['class'] . "]=enabled\n";

        $ini .= "\n# Whether one person may submit more than once. unique means one\n";
        $ini .= "# submission each, which is what a poll wants and what a contact form\n";
        $ini .= "# does not.\n";
        foreach ( $settings['forms'] as $form )
            $ini .= "CollectionUserDataList[" . $form['class'] . "]="
                  . ( $form['type'] === 'poll' ? 'unique' : 'multiple' ) . "\n";

        $ini .= "\n[DisplaySettings]\n";
        $ini .= "# Which template draws what the visitor sees afterwards. Looked for as\n";
        $ini .= "# templates/content/collectedinfo/<name>.tpl in the design.\n";
        foreach ( $settings['forms'] as $form )
            $ini .= "DisplayList[" . $form['class'] . "]="
                  . ( $form['type'] === 'poll' ? 'result' : 'thanks' ) . "\n";

        $ini .= "\n# What a visitor submits is stored against the object rather than in it, so\n";
        $ini .= "# it survives the content being edited and is not carried by a package.\n";
        $ini .= "# Whoever owns the site owns that data, and has to have decided what to do\n";
        $ini .= "# with it before the form is published rather than after.\n";
        $ini .= "\n*/ ?>\n";

        return $ini;
    }

    /**
     * workflow.ini: which operations a workflow may be bound to.
     *
     * @param array $settings
     * @return string
     */
    protected static function workflowIni( array $settings )
    {
        $ini  = self::iniHeader( $settings, 'Trigger operations' );
        $ini .= "[OperationSettings]\n";
        $ini .= "# Only operations listed here can be bound to in the admin. The rest are\n";
        $ini .= "# invisible there, however much code is behind them.\n";
        $ini .= "#\n";
        $ini .= "# Adding to the list rather than replacing it: a bare AvailableOperationList[]\n";
        $ini .= "# would unbind content_publish, and every workflow bound to it would stop.\n";

        foreach ( $settings['operations'] as $operation )
            $ini .= "AvailableOperationList[]=" . $operation . "\n";

        $ini .= "\n# Listing an operation makes it bindable and binds nothing. The binding is\n";
        $ini .= "# done in the admin, under Setup, and is a row in the database rather than\n";
        $ini .= "# a setting - so it does not travel with this extension.\n";
        $ini .= "\n*/ ?>\n";

        return $ini;
    }

    /**
     * The settings that apply to one siteaccess only.
     *
     * @param array $settings
     * @return array path to contents
     */
    protected static function siteaccessFiles( array $settings )
    {
        $byFile = array();

        foreach ( $settings['overrides'] as $override )
            $byFile[$override['ini']][$override['section']][] = $override;

        $files = array();

        foreach ( $byFile as $ini => $sections )
        {
            $contents = self::iniHeader( $settings, 'Settings for the ' . $settings['siteaccess'] . ' siteaccess' );
            $contents .= "# Read only when this extension is in ActiveAccessExtensions[] as well as\n";
            $contents .= "# ActiveExtensions[], and only for the " . self::iniValue( $settings['siteaccess'], 60 ) . " siteaccess.\n";

            foreach ( $sections as $section => $lines )
            {
                $contents .= "\n[" . $section . "]\n";

                foreach ( $lines as $line )
                    $contents .= $line['variable'] . "=" . $line['value'] . "\n";
            }

            $contents .= "\n*/ ?>\n";

            $files['settings/siteaccess/' . $settings['siteaccess'] . '/' . $ini . '.append.php'] = $contents;
        }

        return $files;
    }

    // ── Documentation ────────────────────────────────────────────────────────

    /**
     * What each setting does and what has to be cleared before it takes effect.
     *
     * @param array $settings
     * @param array $paths
     * @return string
     */
    protected static function readme( array $settings, array $paths = array() )
    {
        $topics = self::topics();
        $chosen = self::chosenTopics( $settings );

        $readme  = "# " . $settings['title'] . "\n\n" . $settings['summary'] . "\n\n";
        $readme .= "An extension that is mostly settings. Nothing here is hard; all of it is in a\n";
        $readme .= "shape nobody remembers.\n\n";

        $readme .= "## Switching it on\n\n";
        $readme .= "1. Put this directory in `extension/" . $settings['name'] . "`.\n";
        $readme .= "2. Add it to `settings/override/site.ini.append.php`:\n\n";
        $readme .= "```\n" . self::activation( $settings ) . "\n```\n\n";
        $readme .= "3. Clear the caches:\n\n```\nphp bin/php/ezcache.php --clear-all\n```\n\n";

        if ( in_array( 'event', $chosen, true ) )
            $readme .= "4. Regenerate the autoloads, or the listener class is never found:\n\n"
                     . "```\nphp bin/php/ezpgenerateautoloads.php --extension=" . $settings['name'] . "\n```\n\n";

        $readme .= "## What it says\n\n";

        foreach ( $chosen as $key )
        {
            $readme .= "### " . $topics[$key]['label'] . "\n\n";
            $readme .= $topics[$key]['what'] . "\n\n";
            $readme .= "*" . $topics[$key]['note'] . "*\n\n";

            switch ( $key )
            {
                case 'image':
                    $readme .= "| Alias | Filters |\n| --- | --- |\n";
                    foreach ( $settings['aliases'] as $alias )
                    {
                        $filters = array();
                        foreach ( $alias['filters'] as $filter )
                            $filters[] = '`' . $filter['name'] . ( $filter['args'] !== '' ? '=' . $filter['args'] : '' ) . '`';

                        $readme .= "| `" . $alias['name'] . "` | " . ( $filters ? implode( ', ', $filters ) : 'none' ) . " |\n";
                    }
                    $readme .= "\nIn a template:\n\n```\n{\$node.data_map.image.content["
                             . $settings['aliases'][0]['name'] . "].full_path|ezroot}\n```\n\n";
                    break;

                case 'event':
                    $events = self::events();
                    $readme .= "| Event | Kind | Method |\n| --- | --- | --- |\n";
                    foreach ( $settings['events'] as $event )
                        $readme .= "| `" . $event . "` | " . $events[$event]['kind'] . " | `"
                                 . $settings['class'] . "::" . self::methodFor( $event ) . "` |\n";
                    $readme .= "\n";
                    break;

                case 'viewcache':
                    $readme .= "| Content class | Also clears |\n| --- | --- |\n";
                    foreach ( $settings['rules'] as $rule )
                        $readme .= "| `" . $rule['class'] . "` | "
                                 . ( $rule['methods'] ? '`' . implode( '`, `', $rule['methods'] ) . '`' : 'the default' )
                                 . ( $rule['depends'] ? ', through `' . implode( '`, `', $rule['depends'] ) . '`' : '' )
                                 . " |\n";
                    $readme .= "\n";
                    break;

                case 'collect':
                    $readme .= "| Content class | Type | Emailed |\n| --- | --- | --- |\n";
                    foreach ( $settings['forms'] as $form )
                        $readme .= "| `" . $form['class'] . "` | " . $form['type'] . " | "
                                 . ( $form['mail'] ? 'yes' : 'no' ) . " |\n";
                    $readme .= "\n";
                    break;

                case 'trigger':
                    $readme .= "Bindable: `" . implode( '`, `', $settings['operations'] ) . "`\n\n";
                    break;

                case 'siteaccess':
                    $readme .= "For the `" . $settings['siteaccess'] . "` siteaccess:\n\n";
                    $readme .= "| File | Section | Setting |\n| --- | --- | --- |\n";
                    foreach ( $settings['overrides'] as $override )
                        $readme .= "| `" . $override['ini'] . "` | `" . $override['section'] . "` | `"
                                 . $override['variable'] . "=" . $override['value'] . "` |\n";
                    $readme .= "\n";
                    break;
            }
        }

        if ( count( $paths ) )
        {
            $readme .= "## What is in here\n\n```\n";
            foreach ( $paths as $path )
                $readme .= $path . "\n";
            $readme .= "```\n";
        }

        return $readme;
    }
}

?>
