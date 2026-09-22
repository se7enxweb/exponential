<?php
/**
 * The content extension wizard: what authors, editors and translators see.
 *
 * Three things that belong together because they are all about the content
 * itself rather than about the machinery under it:
 *
 *   a content class   the shape of a kind of content, as a script rather than
 *                     as an afternoon of clicking
 *   a custom tag      a tag authors can use in rich text, with its own
 *                     attributes and its own template
 *   a translation     the strings of this installation in another language
 *
 * The content class is the interesting one. A class is normally built in the
 * admin, which means it exists on the machine it was built on and nowhere else,
 * and the only record of how it was made is whatever somebody wrote down. This
 * writes it as a script: reviewable, re-runnable, and the same on every
 * installation it is run on.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */


if ( !class_exists( 'expContentExtensionWizard', false ) ) {
class expContentExtensionWizard extends expExtensionWizard
{
    /**
     * What wrote the files, for the head of each one.
     *
     * @return string
     */
    public static function wizardName()
    {
        return 'content extension wizard';
    }

    // ── What it can write ────────────────────────────────────────────────────

    /**
     * @return array
     */
    public static function topics()
    {
        return array(

        'class' => array(
            'label'   => 'Content class',
            'default' => true,
            'summary' => 'The shape of a kind of content, written as a script.',
            'what'    => 'A class built in the admin exists on the machine it was built on and nowhere else, and the only record of how it was made is whatever somebody wrote down. As a script it is reviewable, re-runnable, and the same everywhere it is run.',
            'note'    => 'The script refuses to run twice. A class identifier cannot be changed once content exists, and neither can a datatype, so getting those right first is worth more than getting them quickly.' ),

        'customtag' => array(
            'label'   => 'XML custom tag',
            'default' => false,
            'summary' => 'A tag authors can use in rich text.',
            'what'    => 'Rich text allows a <custom> tag with a name on it. Each name is a tag of its own with its own attributes and its own template, which is how a warning box, a pull quote or an embedded thing gets into content without a datatype or a module.',
            'note'    => 'Inline tags sit inside a paragraph and block tags replace one. Getting that wrong produces markup that validates and lays out wrongly, which is harder to spot than markup that does not validate at all.' ),

        'translation' => array(
            'label'   => 'Translation',
            'default' => false,
            'summary' => 'The strings of this installation in another language.',
            'what'    => 'Every string in a template and in the kernel is wrapped in a translation call with a context and a source. A translation file answers those, and one that answers none of them is still worth shipping: it is the list of what there is to translate.',
            'note'    => 'Translations are matched on the exact source string. Change the English in a template and every translation of that line stops being found, silently, and the English comes back.' ),
        );
    }

    /**
     * The datatypes a generated content class can use, with what each stores.
     *
     * Read off this installation rather than written down, so a datatype added
     * by another extension - or by the datatype wizard next door - is offered
     * here as soon as it exists.
     *
     * @return array
     */
    public static function datatypes()
    {
        $known = array();

        foreach ( (array) eZINI::instance( 'content.ini' )->variable( 'DataTypeSettings', 'AvailableDataTypes' ) as $type )
        {
            if ( !is_string( $type ) || $type === '' )
                continue;

            $known[$type] = array( 'type' => $type, 'name' => $type );
        }

        // The names as an editor sees them, where the datatype is loaded. Not
        // loading one that is not: a datatype is loaded by including a file,
        // and a survey of them must not be able to fail because one of them
        // has a fault in it.
        foreach ( eZDataType::registeredDataTypes() as $type => $object )
            if ( isset( $known[$type] ) && is_object( $object ) )
                $known[$type]['name'] = (string) $object->attribute( 'name' );

        ksort( $known );

        return $known;
    }

    /**
     * Where a content class can be put, as class groups.
     *
     * @return array
     */
    public static function groups()
    {
        $groups = array();

        foreach ( eZContentClassGroup::fetchList() as $group )
            if ( is_object( $group ) )
                $groups[(int) $group->attribute( 'id' )] = (string) $group->attribute( 'name' );

        return $groups;
    }

    // ── What this wizard can put in ──────────────────────────────────────────

    /**
     * @return array
     */
    public static function parts()
    {
        $parts = array();

        foreach ( self::topics() as $key => $topic )
            $parts[$key] = array( 'label'       => $topic['label'],
                                  'description' => $topic['summary'],
                                  'default'     => $topic['default'] );

        return array_merge( $parts, array(
            'settings' => array(
                'label' => 'Registration',
                'description' => 'content.ini for the custom tags, site.ini for the translations, and design.ini so the templates are found. Without the last one a custom tag is allowed and draws nothing.',
                'default' => true ),
            'readme' => array(
                'label' => 'README.md',
                'description' => 'What is in it, how to run the class script, and what to do before it is run on a site with content on it.',
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
            'name'        => self::safeName( isset( $input['name'] ) ? $input['name'] : '' ),
            'title'       => self::text( isset( $input['title'] ) ? $input['title'] : '', 120 ),
            'summary'     => self::text( isset( $input['summary'] ) ? $input['summary'] : '', 250 ),
            'author'      => self::text( isset( $input['author'] ) ? $input['author'] : '', 120 ),
            'vendor'      => self::safeName( isset( $input['vendor'] ) ? $input['vendor'] : '', true ),
            'version'     => self::text( isset( $input['version'] ) ? $input['version'] : '', 20 ),
            'licence'     => self::licence_id( isset( $input['licence'] ) ? $input['licence'] : '' ),
            'class'       => self::safeIdentifier( isset( $input['class'] ) ? $input['class'] : '' ),
            'class_name'  => self::text( isset( $input['class_name'] ) ? $input['class_name'] : '', 120 ),
            'class_group' => isset( $input['class_group'] ) ? (int) $input['class_group'] : 1,
            'pattern'     => self::text( isset( $input['pattern'] ) ? $input['pattern'] : '', 120 ),
            'locale'      => self::safeLocale( isset( $input['locale'] ) ? $input['locale'] : '' ),
        );

        $settings['attributes'] = self::attributeList( isset( $input['attributes'] ) ? $input['attributes'] : '' );
        $settings['tags']       = self::tagList( isset( $input['tags'] ) ? $input['tags'] : '' );
        $settings['strings']    = self::stringList( isset( $input['strings'] ) ? $input['strings'] : '' );

        if ( $settings['title'] === '' && $settings['name'] !== '' )
            $settings['title'] = ucwords( str_replace( '_', ' ', $settings['name'] ) );
        if ( $settings['class_name'] === '' && $settings['class'] !== '' )
            $settings['class_name'] = ucwords( str_replace( '_', ' ', $settings['class'] ) );
        if ( $settings['version'] === '' )
            $settings['version'] = '1.0.0';
        if ( $settings['vendor'] === '' )
            $settings['vendor'] = 'exponential';
        if ( $settings['summary'] === '' )
            $settings['summary'] = 'Content for Exponential.';

        // The name pattern defaults to the first attribute, which is what the
        // admin does and is right far more often than it is wrong.
        if ( $settings['pattern'] === '' && count( $settings['attributes'] ) )
            $settings['pattern'] = '<' . $settings['attributes'][0]['identifier'] . '>';

        $chosen = isset( $input['parts'] ) && is_array( $input['parts'] ) ? $input['parts'] : null;
        foreach ( self::parts() as $key => $part )
            $settings['parts'][$key] = $chosen === null ? $part['default'] : in_array( $key, $chosen, true );

        return $settings;
    }

    /**
     * The attributes, one per line: identifier, datatype, name, then flags.
     *
     * @param mixed $value
     * @return array
     */
    public static function attributeList( $value )
    {
        if ( !is_scalar( $value ) )
            return array();

        $datatypes  = self::datatypes();
        $attributes = array();
        $seen       = array();

        foreach ( preg_split( '/[\r\n]+/', (string) $value ) as $line )
        {
            $line = trim( $line );
            if ( $line === '' )
                continue;

            $bits       = array_map( 'trim', explode( ',', $line ) );
            $identifier = self::safeIdentifier( isset( $bits[0] ) ? $bits[0] : '' );

            if ( $identifier === '' || isset( $seen[$identifier] ) )
                continue;

            $seen[$identifier] = true;

            $type = isset( $bits[1] ) ? self::safeType( $bits[1] ) : '';
            if ( $type === '' )
                $type = 'ezstring';

            $name = isset( $bits[2] ) && $bits[2] !== ''
                    ? self::text( $bits[2], 120 )
                    : ucwords( str_replace( '_', ' ', $identifier ) );

            $flags = array();
            for ( $at = 3; $at < count( $bits ); $at++ )
                $flags[] = strtolower( $bits[$at] );

            $attributes[] = array(
                'identifier' => $identifier,
                'type'       => $type,
                'known'      => isset( $datatypes[$type] ),
                'name'       => $name,
                'required'   => in_array( 'required', $flags, true ),
                'searchable' => !in_array( 'nosearch', $flags, true ),
                'collector'  => in_array( 'collect', $flags, true ),
                'translatable' => !in_array( 'notranslate', $flags, true ) );

            if ( count( $attributes ) >= 50 )
                break;
        }

        return $attributes;
    }

    /**
     * The custom tags, one per line: a name, then its attributes.
     *
     * @param mixed $value
     * @return array
     */
    public static function tagList( $value )
    {
        if ( !is_scalar( $value ) )
            return array();

        $tags = array();

        foreach ( preg_split( '/[\r\n]+/', (string) $value ) as $line )
        {
            $line = trim( $line );
            if ( $line === '' )
                continue;

            $colon = strpos( $line, ':' );
            $name  = self::safeIdentifier( $colon === false ? $line : substr( $line, 0, $colon ) );

            if ( $name === '' )
                continue;

            $attributes = array();
            $inline     = false;

            if ( $colon !== false )
                foreach ( explode( ',', substr( $line, $colon + 1 ) ) as $word )
                {
                    $word = self::safeIdentifier( $word );

                    if ( $word === '' )
                        continue;

                    if ( $word === 'inline' )
                        $inline = true;
                    else
                        $attributes[] = $word;
                }

            $tags[] = array( 'name'       => $name,
                             'inline'     => $inline,
                             'attributes' => array_values( array_unique( $attributes ) ) );

            if ( count( $tags ) >= 30 )
                break;
        }

        return $tags;
    }

    /**
     * The strings to translate, one per line: a context, then the source.
     *
     * @param mixed $value
     * @return array
     */
    public static function stringList( $value )
    {
        if ( !is_scalar( $value ) )
            return array();

        $strings = array();

        foreach ( preg_split( '/[\r\n]+/', (string) $value ) as $line )
        {
            $line = trim( $line );
            if ( $line === '' )
                continue;

            $bar = strpos( $line, '|' );

            if ( $bar === false )
                continue;

            $context = self::text( substr( $line, 0, $bar ), 120 );
            $source  = self::text( substr( $line, $bar + 1 ), 400 );

            if ( trim( $context ) === '' || trim( $source ) === '' )
                continue;

            $strings[] = array( 'context' => trim( $context ), 'source' => trim( $source ) );

            if ( count( $strings ) >= 200 )
                break;
        }

        return $strings;
    }

    /**
     * A lower case identifier: a class identifier, an attribute, a tag.
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
     * A datatype string: lower case letters and digits.
     *
     * @param mixed $value
     * @return string
     */
    public static function safeType( $value )
    {
        if ( !is_scalar( $value ) )
            return '';

        $value = strtolower( preg_replace( '/[^A-Za-z0-9]+/', '', (string) $value ) );

        return preg_match( '/^[a-z][a-z0-9]{1,40}$/', $value ) ? $value : '';
    }

    /**
     * A locale, in the shape eZ uses: three letters, a dash, two capitals.
     *
     * @param mixed $value
     * @return string
     */
    public static function safeLocale( $value )
    {
        if ( !is_scalar( $value ) )
            return '';

        $value = preg_replace( '/[^A-Za-z-]+/', '', (string) $value );

        if ( !preg_match( '/^([A-Za-z]{3})-([A-Za-z]{2})$/', (string) $value, $found ) )
            return '';

        return strtolower( $found[1] ) . '-' . strtoupper( $found[2] );
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
            $problems[] = 'Choose at least one thing for this extension to carry, or there is nothing to write.';

        if ( $settings['parts']['class'] )
        {
            if ( $settings['class'] === '' )
                $problems[] = 'The content class needs an identifier: lower case letters, digits and underscores, starting with a letter. It cannot be changed once content exists.';

            if ( count( $settings['attributes'] ) === 0 )
                $problems[] = 'A content class with no attributes holds nothing. Name at least one.';

            // fetchByIdentifier() answers null rather than false when there is
            // no such class, so anything comparing against false finds every
            // identifier taken.
            if ( $settings['class'] !== '' && is_object( eZContentClass::fetchByIdentifier( $settings['class'] ) ) )
                $problems[] = 'A content class called ' . $settings['class'] . ' already exists on this installation. The script this writes refuses to run against it rather than change it; choose another identifier, or expect to run this somewhere else.';

            foreach ( $settings['attributes'] as $attribute )
                if ( !$attribute['known'] )
                    $problems[] = 'There is no datatype called ' . $attribute['type'] . ' on this installation, so the attribute ' . $attribute['identifier'] . ' cannot be made. It may exist where the script is run; if it does not, the script stops there.';
        }

        if ( $settings['parts']['customtag'] )
        {
            if ( count( $settings['tags'] ) === 0 )
                $problems[] = 'Custom tags were chosen and none were named.';

            foreach ( self::takenTags( $settings ) as $tag )
                $problems[] = 'A custom tag called ' . $tag . ' already exists on this installation. Naming it again redefines its attributes rather than adding a tag; choose another name.';
        }

        if ( $settings['parts']['translation'] )
        {
            if ( $settings['locale'] === '' )
                $problems[] = 'A translation needs a locale, in the shape eng-GB: three letters, a dash, two letters.';

            if ( count( $settings['strings'] ) === 0 )
                $problems[] = 'A translation was chosen and no strings were given. One per line, as: context|the English text';
        }

        return $problems;
    }

    /**
     * Which of the chosen tag names this installation already has.
     *
     * @param array $settings
     * @return array of string
     */
    public static function takenTags( array $settings )
    {
        $existing = (array) eZINI::instance( 'content.ini' )->variable( 'CustomTagSettings', 'AvailableCustomTags' );
        $taken    = array();

        foreach ( $settings['tags'] as $tag )
            if ( in_array( $tag['name'], $existing, true ) )
                $taken[] = $tag['name'];

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
            'class'       => $settings['class'] !== '' && count( $settings['attributes'] ),
            'customtag'   => count( $settings['tags'] ),
            'translation' => $settings['locale'] !== '' && count( $settings['strings'] ) );

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

        if ( in_array( 'class', $topics, true ) )
            $files['bin/install_' . $settings['class'] . '.php'] = self::classScript( $settings );

        if ( in_array( 'customtag', $topics, true ) )
            foreach ( $settings['tags'] as $tag )
                $files['design/' . $settings['name'] . '/templates/content/datatype/view/ezxmltags/'
                       . $tag['name'] . '.tpl'] = self::tagTemplate( $settings, $tag );

        if ( in_array( 'translation', $topics, true ) )
            $files['translations/' . $settings['locale'] . '/translation.ts'] = self::translation( $settings );

        if ( $parts['settings'] )
        {
            if ( in_array( 'customtag', $topics, true ) )
            {
                $files['settings/content.ini.append.php'] = self::contentIni( $settings );
                $files['settings/design.ini.append.php']  = self::designIni( $settings );
            }

            if ( in_array( 'translation', $topics, true ) )
                $files['settings/site.ini.append.php'] = self::siteIni( $settings );
        }

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

    // ── The content class, as a script ───────────────────────────────────────

    /**
     * A script that makes the content class.
     *
     * @param array $settings
     * @return string
     */
    protected static function classScript( array $settings )
    {
        $php  = "<?php\n/**\n * Creates the " . self::commentText( $settings['class_name'] ) . " content class.\n *\n";
        $php .= " * Run once, from the installation root:\n *\n";
        $php .= " *     php extension/" . self::commentText( $settings['name'] ) . "/bin/install_"
              . self::commentText( $settings['class'] ) . ".php -s <siteaccess>\n *\n";
        $php .= " * It refuses to run a second time rather than change a class that exists.\n";
        $php .= " * Changing a class that content is already using is a different job, and one\n";
        $php .= " * nobody should do by running a script they have not read.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";

        $php .= "require 'autoload.php';\n\n";
        $php .= "\$script = eZScript::instance( array( 'description' => 'Creates the "
              . self::phpString( $settings['class_name'] ) . " content class.',\n";
        $php .= "                                    'use-session'    => false,\n";
        $php .= "                                    'use-modules'    => true,\n";
        $php .= "                                    'use-extensions' => true ) );\n";
        $php .= "\$script->startup();\n";
        $php .= "\$script->initialize();\n\n";
        $php .= "\$cli = eZCLI::instance();\n\n";

        $php .= "const CLASS_IDENTIFIER = '" . self::phpString( $settings['class'] ) . "';\n\n";

        $php .= "// Nothing is changed if it is already here. A class identifier cannot be\n";
        $php .= "// changed once content exists, so a script that quietly altered one would be\n";
        $php .= "// the most expensive kind of convenience.\n";
        $php .= "// fetchByIdentifier() answers null rather than false when there is no such\n";
        $php .= "// class, so is_object() and not a comparison against false.\n";
        $php .= "if ( is_object( eZContentClass::fetchByIdentifier( CLASS_IDENTIFIER ) ) )\n";
        $php .= "{\n";
        $php .= "    \$cli->error( 'A content class called ' . CLASS_IDENTIFIER . ' already exists. Nothing done.' );\n";
        $php .= "    \$script->shutdown( 1 );\n";
        $php .= "}\n\n";

        $php .= "\$db = eZDB::instance();\n";
        $php .= "\$db->begin();\n\n";

        $php .= "// Built as a temporary class and promoted at the end, which is what the\n";
        $php .= "// class editor does. It matters: storeDefined() recreates the class group\n";
        $php .= "// links from the version before it, so a group added to a class that is\n";
        $php .= "// already defined is wiped by the very call meant to finish it.\n";
        $php .= "\$class = eZContentClass::create( eZUser::currentUserID(), array(\n";
        $php .= "    'version'             => eZContentClass::VERSION_STATUS_TEMPORARY,\n";
        $php .= "    'identifier'          => CLASS_IDENTIFIER,\n";
        $php .= "    'name'                => '" . self::phpString( $settings['class_name'] ) . "',\n";
        $php .= "    'contentobject_name'  => '" . self::phpString( $settings['pattern'] ) . "',\n";
        $php .= "    'is_container'        => 0,\n";
        $php .= "    'always_available'    => 0 ) );\n\n";
        $php .= "\$class->store();\n\n";

        $php .= "// The name pattern decides what every object of this class is called, and\n";
        $php .= "// that name ends up in the url. Changing it later renames nothing that\n";
        $php .= "// already exists.\n\n";

        $php .= "\$attributes = array(\n";
        foreach ( $settings['attributes'] as $position => $attribute )
        {
            $php .= "    array( 'identifier'    => '" . self::phpString( $attribute['identifier'] ) . "',\n";
            $php .= "           'datatype'      => '" . self::phpString( $attribute['type'] ) . "',\n";
            $php .= "           'name'          => '" . self::phpString( $attribute['name'] ) . "',\n";
            $php .= "           'is_required'   => " . ( $attribute['required'] ? '1' : '0' ) . ",\n";
            $php .= "           'is_searchable' => " . ( $attribute['searchable'] ? '1' : '0' ) . ",\n";
            $php .= "           'is_information_collector' => " . ( $attribute['collector'] ? '1' : '0' ) . ",\n";
            $php .= "           'can_translate' => " . ( $attribute['translatable'] ? '1' : '0' ) . ",\n";
            $php .= "           'placement'     => " . ( $position + 1 ) . " ),\n";
        }
        $php .= ");\n\n";

        $php .= "foreach ( \$attributes as \$definition )\n";
        $php .= "{\n";
        $php .= "    // A datatype that is not installed here cannot be made into an attribute.\n";
        $php .= "    // Stopping is the only safe answer: carrying on would leave a class that\n";
        $php .= "    // looks finished and is missing a field.\n";
        $php .= "    if ( !eZDataType::create( \$definition['datatype'] ) )\n";
        $php .= "    {\n";
        $php .= "        \$db->rollback();\n";
        $php .= "        \$cli->error( 'No datatype called ' . \$definition['datatype'] . ' on this installation. Nothing done.' );\n";
        $php .= "        \$script->shutdown( 1 );\n";
        $php .= "    }\n\n";
        $php .= "    \$attribute = eZContentClassAttribute::create(\n";
        $php .= "        \$class->attribute( 'id' ), \$definition['datatype'], \$definition );\n\n";
        $php .= "    \$attribute->setAttribute( 'version', eZContentClass::VERSION_STATUS_TEMPORARY );\n\n";
        $php .= "    // The datatype gets to set its own defaults before the row is written.\n";
        $php .= "    \$dataType = \$attribute->dataType();\n";
        $php .= "    if ( \$dataType )\n";
        $php .= "        \$dataType->initializeClassAttribute( \$attribute );\n\n";
        $php .= "    \$attribute->store();\n";
        $php .= "}\n\n";

        $php .= "// Without a group the class exists and appears nowhere in the admin. Added\n";
        $php .= "// while the class is still temporary, so that storeDefined() below carries\n";
        $php .= "// the link forward rather than removing it.\n";
        $php .= "\$group = eZContentClassGroup::fetch( " . (int) $settings['class_group'] . " );\n";
        $php .= "if ( !is_object( \$group ) )\n";
        $php .= "{\n";
        $php .= "    \$db->rollback();\n";
        $php .= "    \$cli->error( 'No class group " . (int) $settings['class_group'] . " on this installation. Nothing done.' );\n";
        $php .= "    \$script->shutdown( 1 );\n";
        $php .= "}\n\n";
        $php .= "\$group->appendClass( \$class );\n\n";

        $php .= "// Promotes the class, its attributes and its group links from temporary to\n";
        $php .= "// defined, all together. Until this runs the class is invisible.\n";
        $php .= "\$class->storeDefined( \$class->fetchAttributes( false, true, eZContentClass::VERSION_STATUS_TEMPORARY ) );\n\n";
        $php .= "\$db->commit();\n\n";
        $php .= "// The class list is cached by identifier, so nothing sees the new class\n";
        $php .= "// until that is cleared.\n";
        $php .= "eZContentCacheManager::clearAllContentCache();\n\n";

        $php .= "\$cli->output( 'Created ' . CLASS_IDENTIFIER . ' with ' . count( \$attributes ) . ' attributes.' );\n";
        $php .= "\$cli->output( 'Clear the class cache before using it:' );\n";
        $php .= "\$cli->output( '    php bin/php/ezcache.php --clear-id=class' );\n\n";
        $php .= "\$script->shutdown( 0 );\n";

        return $php;
    }

    // ── Custom tags ──────────────────────────────────────────────────────────

    /**
     * The template one custom tag is drawn with.
     *
     * @param array $settings
     * @param array $tag
     * @return string
     */
    protected static function tagTemplate( array $settings, array $tag )
    {
        $element = $tag['inline'] ? 'span' : 'div';

        $tpl  = "{* The " . self::commentText( $tag['name'] ) . " custom tag.\n\n";
        $tpl .= "   Drawn wherever an author writes <custom name=\"" . self::commentText( $tag['name'] ) . "\">\n";
        $tpl .= "   in a rich text field. \$content is what they wrote inside it and has\n";
        $tpl .= "   already been rendered, so it must not be washed again - but anything\n";
        $tpl .= "   taken out of \$classification or \$custom_parameters must be. *}\n\n";

        if ( count( $tag['attributes'] ) )
        {
            $tpl .= "{* The attributes this tag declares. Each is whatever the author typed,\n";
            $tpl .= "   which is why every one of them is washed on the way out. *}\n";
            foreach ( $tag['attributes'] as $attribute )
                $tpl .= "{def \$" . $attribute . "=cond( is_set( \$custom_parameters."
                      . $attribute . " ), \$custom_parameters." . $attribute . ", '' )}\n";
            $tpl .= "\n";
        }

        $tpl .= "<" . $element . " class=\"custom-tag " . $tag['name'] . "\">\n";

        foreach ( $tag['attributes'] as $attribute )
        {
            $tpl .= "{if ne( \$" . $attribute . ", '' )}";
            $tpl .= "<" . ( $tag['inline'] ? 'span' : 'div' ) . " class=\"" . $tag['name'] . "-"
                  . $attribute . "\">{\$" . $attribute . "|wash}</"
                  . ( $tag['inline'] ? 'span' : 'div' ) . ">{/if}\n";
        }

        $tpl .= "{\$content}\n";
        $tpl .= "</" . $element . ">\n";

        if ( count( $tag['attributes'] ) )
        {
            $tpl .= "\n";
            foreach ( $tag['attributes'] as $attribute )
                $tpl .= "{undef \$" . $attribute . "}\n";
        }

        return $tpl;
    }

    /**
     * content.ini: the tags, and what each may carry.
     *
     * @param array $settings
     * @return string
     */
    protected static function contentIni( array $settings )
    {
        $ini  = self::iniHeader( $settings, 'Custom tags' );
        $ini .= "[CustomTagSettings]\n";
        $ini .= "# Adding to the list rather than replacing it. A bare AvailableCustomTags[]\n";
        $ini .= "# above these lines would remove every tag the system already has, and every\n";
        $ini .= "# piece of content using one would stop rendering it.\n";

        foreach ( $settings['tags'] as $tag )
            $ini .= "AvailableCustomTags[]=" . $tag['name'] . "\n";

        $inline = array();
        foreach ( $settings['tags'] as $tag )
            if ( $tag['inline'] )
                $inline[] = $tag['name'];

        if ( count( $inline ) )
        {
            $ini .= "\n# Inline tags sit inside a paragraph. Block tags replace one, and a block\n";
            $ini .= "# tag used inline produces markup that lays out wrongly while validating.\n";
            foreach ( $inline as $name )
                $ini .= "IsInline[" . $name . "]=true\n";
        }

        foreach ( $settings['tags'] as $tag )
        {
            $ini .= "\n[" . $tag['name'] . "]\n";

            if ( count( $tag['attributes'] ) === 0 )
            {
                $ini .= "# This tag carries nothing but what the author writes inside it.\n";
                $ini .= "CustomAttributes[]\n";
                continue;
            }

            $ini .= "# What an author may set on the tag. Anything not listed here cannot be\n";
            $ini .= "# set at all, which is the only validation a custom tag has.\n";
            $ini .= "CustomAttributes[]\n";

            foreach ( $tag['attributes'] as $attribute )
                $ini .= "CustomAttributes[]=" . $attribute . "\n";
        }

        $ini .= "\n*/ ?>\n";

        return $ini;
    }

    /**
     * design.ini: where the tag templates are.
     *
     * @param array $settings
     * @return string
     */
    protected static function designIni( array $settings )
    {
        $ini  = self::iniHeader( $settings, 'Design settings' );
        $ini .= "[ExtensionSettings]\n";
        $ini .= "# Where the templates for these tags are. Without this line the tags are\n";
        $ini .= "# allowed in content and draw nothing at all.\n";
        $ini .= "DesignExtensions[]=" . $settings['name'] . "\n";
        $ini .= "\n*/ ?>\n";

        return $ini;
    }

    // ── Translation ──────────────────────────────────────────────────────────

    /**
     * The translation file, with every string waiting to be answered.
     *
     * @param array $settings
     * @return string
     */
    protected static function translation( array $settings )
    {
        $byContext = array();
        foreach ( $settings['strings'] as $string )
            $byContext[$string['context']][] = $string['source'];

        $ts  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $ts .= "<!DOCTYPE TS>\n";
        $ts .= "<!--\n";
        $ts .= "    " . self::xml( $settings['title'] ) . " - " . self::xml( $settings['locale'] ) . "\n\n";
        $ts .= "    Every message below is unanswered: the translation is the same as the\n";
        $ts .= "    source, so this file changes nothing until it is filled in. That is the\n";
        $ts .= "    useful state to ship it in - it is the list of what there is to do.\n\n";
        $ts .= "    Matching is on the exact source string. Change the English in a template\n";
        $ts .= "    and the line below stops being found, silently, and the English comes\n";
        $ts .= "    back on the page.\n";
        $ts .= "-->\n";
        $ts .= "<TS version=\"2.0\">\n";

        foreach ( $byContext as $context => $sources )
        {
            $ts .= "<context>\n";
            $ts .= "    <name>" . self::xml( $context ) . "</name>\n";

            foreach ( $sources as $source )
            {
                $ts .= "    <message>\n";
                $ts .= "        <source>" . self::xml( $source ) . "</source>\n";
                $ts .= "        <translation type=\"unfinished\">" . self::xml( $source ) . "</translation>\n";
                $ts .= "    </message>\n";
            }

            $ts .= "</context>\n";
        }

        $ts .= "</TS>\n";

        return $ts;
    }

    /**
     * site.ini: where the translation is.
     *
     * @param array $settings
     * @return string
     */
    protected static function siteIni( array $settings )
    {
        $ini  = self::iniHeader( $settings, 'Translation' );
        $ini .= "[RegionalSettings]\n";
        $ini .= "# The extension whose translations/ directory is read. Without this line the\n";
        $ini .= "# file is never looked at, however correctly the locale directory is named.\n";
        $ini .= "TranslationExtensions[]=" . $settings['name'] . "\n\n";
        $ini .= "# And the locale has to be one the siteaccess is actually using. A\n";
        $ini .= "# translation for a locale nothing is set to is read by nobody.\n";
        $ini .= "# Locale=" . $settings['locale'] . "\n";
        $ini .= "\n*/ ?>\n";

        return $ini;
    }

    // ── Documentation ────────────────────────────────────────────────────────

    /**
     * What is in it, and what to do before running the class script.
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

        $readme .= "## Switching it on\n\n";
        $readme .= "1. Put this directory in `extension/" . $settings['name'] . "`.\n";
        $readme .= "2. Add it to `settings/override/site.ini.append.php`:\n\n";
        $readme .= "```\n" . self::activation( $settings ) . "\n```\n\n";
        $readme .= "3. Clear the caches:\n\n```\nphp bin/php/ezcache.php --clear-all\n```\n\n";

        foreach ( $chosen as $key )
        {
            $readme .= "## " . $topics[$key]['label'] . "\n\n";
            $readme .= $topics[$key]['what'] . "\n\n";
            $readme .= "*" . $topics[$key]['note'] . "*\n\n";

            switch ( $key )
            {
                case 'class':
                    $readme .= "Run it once, from the installation root:\n\n";
                    $readme .= "```\nphp extension/" . $settings['name'] . "/bin/install_"
                             . $settings['class'] . ".php -s <siteaccess>\n```\n\n";
                    $readme .= "| Attribute | Datatype | Required | Searchable |\n| --- | --- | --- | --- |\n";
                    foreach ( $settings['attributes'] as $attribute )
                        $readme .= "| `" . $attribute['identifier'] . "` | `" . $attribute['type'] . "` | "
                                 . ( $attribute['required'] ? 'yes' : 'no' ) . " | "
                                 . ( $attribute['searchable'] ? 'yes' : 'no' ) . " |\n";

                    $readme .= "\nNamed by `" . $settings['pattern'] . "`.\n\n";
                    $readme .= "The script refuses to run against a class that already exists. Changing a\n";
                    $readme .= "class that content is using is a different job: attributes can be added, but\n";
                    $readme .= "removing one throws away every value in it, and changing a datatype throws\n";
                    $readme .= "away the lot. Neither belongs in a script somebody runs without reading.\n\n";
                    break;

                case 'customtag':
                    $readme .= "| Tag | Inline | Attributes |\n| --- | --- | --- |\n";
                    foreach ( $settings['tags'] as $tag )
                        $readme .= "| `" . $tag['name'] . "` | " . ( $tag['inline'] ? 'yes' : 'no' ) . " | "
                                 . ( $tag['attributes'] ? '`' . implode( '`, `', $tag['attributes'] ) . '`' : 'none' ) . " |\n";

                    $readme .= "\nAn author writes:\n\n```\n<custom name=\"" . $settings['tags'][0]['name'] . "\"";
                    foreach ( $settings['tags'][0]['attributes'] as $attribute )
                        $readme .= " custom:" . $attribute . "=\"...\"";
                    $readme .= ">text</custom>\n```\n\n";
                    break;

                case 'translation':
                    $readme .= "`translations/" . $settings['locale'] . "/translation.ts`, with "
                             . count( $settings['strings'] ) . " messages waiting to be answered.\n\n";
                    $readme .= "Every one is marked unfinished and translated as itself, so the file changes\n";
                    $readme .= "nothing until it is filled in.\n\n";
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
}

require_once 'kernel/setup/expextensionwizard.php';


?>
