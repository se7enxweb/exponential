<?php
/**
 * The datatype wizard: builds a working content datatype extension.
 *
 * A datatype is the largest thing this system asks anybody to write. eZDataType
 * declares over ninety methods; a datatype that does its job well implements
 * perhaps twenty of them, and which twenty depends entirely on what it is for.
 * A date needs sorting and no files; an upload needs files and no sorting; a
 * poll answer needs information collection and neither.
 *
 * So this asks what the datatype has to do, and writes only the methods that
 * answer to that - each with a comment saying what the kernel calls it for,
 * what it is given, and what it has to give back.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */


if ( !class_exists( 'expDatatypeWizard', false ) ) {
class expDatatypeWizard extends expExtensionWizard
{
    /**
     * What wrote the files, for the head of each one.
     *
     * @return string
     */
    public static function wizardName()
    {
        return 'datatype wizard';
    }

    // ── What a datatype can store ────────────────────────────────────────────

    /**
     * The columns an attribute of this type has to keep its value in.
     *
     * ezcontentobject_attribute has these five and no others. A datatype that
     * needs more than they hold keeps a table of its own and puts a key here.
     *
     * @return array
     */
    public static function storageColumns()
    {
        return array(
            'data_text' => array(
                'label' => 'data_text',
                'sql'   => 'longtext',
                'what'  => 'Text of any length. What most datatypes end up using, including every one that keeps xml or serialised data.',
                'php'   => 'string' ),
            'data_int' => array(
                'label' => 'data_int',
                'sql'   => 'integer',
                'what'  => 'One whole number. Also how a boolean, a timestamp and a foreign key are kept.',
                'php'   => 'int' ),
            'data_float' => array(
                'label' => 'data_float',
                'sql'   => 'double',
                'what'  => 'One number with a fractional part. Not for money: two floats that look equal on screen need not be equal in a comparison.',
                'php'   => 'float' ),
            'sort_key_int' => array(
                'label' => 'sort_key_int',
                'sql'   => 'integer',
                'what'  => 'What the database sorts on when this attribute is a sort field. Written by sortKey(), never read by anything else.',
                'php'   => 'int' ),
            'sort_key_string' => array(
                'label' => 'sort_key_string',
                'sql'   => 'varchar(255)',
                'what'  => 'The same, for text. Cut to 255 characters by the database, so it is a sort key and not a copy of the value.',
                'php'   => 'string' ),
        );
    }

    /**
     * The columns the class attribute has for this datatype's own settings.
     *
     * ezcontentclass_attribute carries these. They are how a datatype says
     * "maximum length", "default value", "which folder to browse from".
     *
     * @return array
     */
    public static function settingColumns()
    {
        $columns = array();

        foreach ( array( 'data_int1', 'data_int2', 'data_int3', 'data_int4' ) as $name )
            $columns[$name] = array( 'label' => $name, 'kind' => 'int',
                                     'what' => 'A whole number setting.' );

        foreach ( array( 'data_float1', 'data_float2', 'data_float3', 'data_float4' ) as $name )
            $columns[$name] = array( 'label' => $name, 'kind' => 'float',
                                     'what' => 'A fractional number setting.' );

        foreach ( array( 'data_text1', 'data_text2', 'data_text3', 'data_text4', 'data_text5' ) as $name )
            $columns[$name] = array( 'label' => $name, 'kind' => 'text',
                                     'what' => 'A text setting.' );

        return $columns;
    }

    // ── What a datatype can do ───────────────────────────────────────────────

    /**
     * Every capability this wizard can write, and what each one costs.
     *
     * Each is a group of methods that only make sense together. Turning one on
     * writes all of them; leaving it off writes none, and the datatype simply
     * does not have that ability - which is the honest state for most of them
     * in most datatypes.
     *
     * @return array
     */
    public static function capabilities()
    {
        return array(

        'object_input' => array(
            'label'   => 'Editing',
            'default' => true,
            'required'=> true,
            'summary' => 'Read what an editor typed, check it, and store it.',
            'what'    => 'Without this the attribute can be added to a class and will never hold anything. Everything else here is optional; this is not.',
            'methods' => array(
                array( 'name' => 'validateObjectAttributeHTTPInput',
                       'signature' => 'validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )',
                       'returns' => 'eZInputValidator::STATE_ACCEPTED',
                       'what' => 'Checks what was submitted before anything is stored. Return STATE_INVALID and set a validation error on the attribute to send the editor back to the form; returning ACCEPTED without looking is how bad content gets in.' ),
                array( 'name' => 'fetchObjectAttributeHTTPInput',
                       'signature' => 'fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )',
                       'returns' => 'true',
                       'what' => 'Takes the submitted value off the request and puts it on the attribute. Runs after validation passed, so the value is already known to be sound.' ),
                array( 'name' => 'storeObjectAttribute',
                       'signature' => 'storeObjectAttribute( $contentObjectAttribute )',
                       'returns' => 'null',
                       'what' => 'The last chance to change what goes in the row, and the only place a datatype with a table of its own writes to it. The attribute row itself is stored by the kernel straight after.' ),
                array( 'name' => 'objectAttributeContent',
                       'signature' => 'objectAttributeContent( $contentObjectAttribute )',
                       'returns' => '$contentObjectAttribute->attribute( \'data_text\' )',
                       'what' => 'What a template gets when it asks for .content. Anything expensive here is paid for on every page that shows the attribute, so it is worth keeping cheap or keeping cached.' ),
                array( 'name' => 'hasObjectAttributeContent',
                       'signature' => 'hasObjectAttributeContent( $contentObjectAttribute )',
                       'returns' => 'true',
                       'what' => 'Whether there is anything in it. What "has_content" reads in a template, and what decides whether an empty attribute is drawn at all.' ) ) ),

        'class_settings' => array(
            'label'   => 'Class settings',
            'default' => true,
            'summary' => 'Settings an editor chooses once, when the attribute is added to a content class.',
            'what'    => 'A maximum length, a default value, which folder to browse from. Chosen in the class editor and read by every object of that class.',
            'methods' => array(
                array( 'name' => 'validateClassAttributeHTTPInput',
                       'signature' => 'validateClassAttributeHTTPInput( $http, $base, $classAttribute )',
                       'returns' => 'eZInputValidator::STATE_ACCEPTED',
                       'what' => 'Checks the settings before the class is stored. A class is edited rarely and read constantly, so a check here is cheap and a mistake here is expensive.' ),
                array( 'name' => 'fetchClassAttributeHTTPInput',
                       'signature' => 'fetchClassAttributeHTTPInput( $http, $base, $classAttribute )',
                       'returns' => 'true',
                       'what' => 'Takes the settings off the request and puts them on the class attribute.' ),
                array( 'name' => 'preStoreClassAttribute',
                       'signature' => 'preStoreClassAttribute( $classAttribute, $version )',
                       'returns' => 'null',
                       'what' => 'Runs before the class attribute row is written. Where a setting is worked out from other settings rather than typed.' ),
                array( 'name' => 'storeClassAttribute',
                       'signature' => 'storeClassAttribute( $classAttribute, $version )',
                       'returns' => 'null',
                       'what' => 'Runs after. Where a datatype keeping class settings in a table of its own writes them.' ),
                array( 'name' => 'initializeClassAttribute',
                       'signature' => 'initializeClassAttribute( $classAttribute )',
                       'returns' => 'null',
                       'what' => 'Sets the defaults the first time the attribute is added to a class, so the class editor opens with something sensible rather than with zeroes.' ),
                array( 'name' => 'classAttributeContent',
                       'signature' => 'classAttributeContent( $classAttribute )',
                       'returns' => 'null',
                       'what' => 'What a template gets from the class attribute. Used by an edit template that has to draw itself differently depending on a setting.' ) ) ),

        'default_value' => array(
            'label'   => 'Default value',
            'default' => true,
            'summary' => 'What a new attribute holds before anybody has typed anything.',
            'what'    => 'Runs when a new version of an object is made. Without it a new attribute starts empty, which is right for some datatypes and wrong for others.',
            'methods' => array(
                array( 'name' => 'initializeObjectAttribute',
                       'signature' => 'initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )',
                       'returns' => 'null',
                       'what' => 'Called when an attribute is first made, and again for each new version. $currentVersion is null the very first time; on a new version, copy from $originalContentObjectAttribute or the value is lost.' ) ) ),

        'title' => array(
            'label'   => 'Naming objects',
            'default' => true,
            'summary' => 'What an object is called when this attribute is its name.',
            'what'    => 'Content classes name their objects from a pattern of attributes. This is what this datatype contributes to that name.',
            'methods' => array(
                array( 'name' => 'title',
                       'signature' => 'title( $contentObjectAttribute, $name = null )',
                       'returns' => '(string) $contentObjectAttribute->attribute( \'data_text\' )',
                       'what' => 'A short line of plain text. It ends up in page titles, in listings, in the admin and in the url of every object named by it, so it must be text and not markup.' ) ) ),

        'search' => array(
            'label'   => 'Searchable',
            'default' => true,
            'summary' => 'Let what is in this attribute be found by a search.',
            'what'    => 'The search engine asks every attribute for something to index. An attribute that answers nothing is invisible to search, however visible it is on the page.',
            'methods' => array(
                array( 'name' => 'isIndexable',
                       'signature' => 'isIndexable()',
                       'returns' => 'true',
                       'what' => 'Whether this datatype is worth indexing at all. False for anything whose value means nothing as words - a colour, an id, a flag.' ),
                array( 'name' => 'metaData',
                       'signature' => 'metaData( $contentObjectAttribute )',
                       'returns' => '(string) $contentObjectAttribute->attribute( \'data_text\' )',
                       'what' => 'The words to index. Plain text, with markup taken out: what is indexed is what somebody would search for, not what is stored.' ) ) ),

        'sorting' => array(
            'label'   => 'Sortable',
            'default' => false,
            'summary' => 'Let a listing be sorted by this attribute.',
            'what'    => 'Sorting happens in the database, over one column, so the value has to be reduced to something a column can order. That reduction is what these two methods are.',
            'methods' => array(
                array( 'name' => 'sortKeyType',
                       'signature' => 'sortKeyType()',
                       'returns' => "'string'",
                       'what' => "Which column the key goes in: 'string' for sort_key_string, 'int' for sort_key_int. It cannot change once content exists without every row being rewritten." ),
                array( 'name' => 'sortKey',
                       'signature' => 'sortKey( $contentObjectAttribute )',
                       'returns' => '(string) $contentObjectAttribute->attribute( \'data_text\' )',
                       'what' => 'The value reduced to something sortable. A string key is cut to 255 characters by the column, so lead with what matters. Written on every store; read by the database, never by a template.' ) ) ),

        'string' => array(
            'label'   => 'Text in and out',
            'default' => true,
            'summary' => 'Turn the value into one line of text, and read it back.',
            'what'    => 'What the command line import and export use, what a package carries, and what a script setting content in bulk goes through. Cheap to write and the first thing missed.',
            'methods' => array(
                array( 'name' => 'toString',
                       'signature' => 'toString( $contentObjectAttribute )',
                       'returns' => '(string) $contentObjectAttribute->attribute( \'data_text\' )',
                       'what' => 'The whole value as one string. Anything fromString() cannot read back is a value that will not survive an export and import.' ),
                array( 'name' => 'fromString',
                       'signature' => 'fromString( $contentObjectAttribute, $string )',
                       'returns' => 'true',
                       'what' => 'The other direction. Has to cope with a string written by an older version of the datatype, or by a person.' ) ) ),

        'collect' => array(
            'label'   => 'Information collector',
            'default' => false,
            'summary' => 'Let a visitor fill this in on a published page, without editing the content.',
            'what'    => 'This is how a poll, a contact form or a booking works: the attribute is part of the content, but what a visitor types is stored against the content rather than in it.',
            'methods' => array(
                array( 'name' => 'isInformationCollector',
                       'signature' => 'isInformationCollector()',
                       'returns' => 'true',
                       'what' => 'Says this datatype can be one. The class editor then offers the checkbox that turns it on for a particular attribute.' ),
                array( 'name' => 'validateCollectionAttributeHTTPInput',
                       'signature' => 'validateCollectionAttributeHTTPInput( $http, $base, $contentObjectAttribute )',
                       'returns' => 'eZInputValidator::STATE_ACCEPTED',
                       'what' => 'Checks what a visitor - not an editor - submitted. This runs for anybody who can see the page, so it is the most exposed method a datatype has.' ),
                array( 'name' => 'fetchCollectionAttributeHTTPInput',
                       'signature' => 'fetchCollectionAttributeHTTPInput( $collection, $collectionAttribute, $http, $base, $contentObjectAttribute )',
                       'returns' => 'true',
                       'what' => 'Puts the submitted value on the collection attribute, which is a row of its own and not the content.' ) ) ),

        'file' => array(
            'label'   => 'Files',
            'default' => false,
            'summary' => 'Accept an uploaded file, and hand it back on download.',
            'what'    => 'Uploads arrive through three doors: a form, a path on disk, and a string. A datatype that takes files should answer all three, or it works in the editor and not from a script.',
            'methods' => array(
                array( 'name' => 'isHTTPFileInsertionSupported',
                       'signature' => 'isHTTPFileInsertionSupported()',
                       'returns' => 'true',
                       'what' => 'Says an uploaded file can become this attribute. What makes drag and drop upload offer this datatype at all.' ),
                array( 'name' => 'insertHTTPFile',
                       'signature' => 'insertHTTPFile( $object, $objectVersion, $objectLanguage, $objectAttribute, $httpFile, $mimeData, &$result )',
                       'returns' => 'true',
                       'what' => 'A file that arrived through a form. $mimeData says what it claims to be - which is what the browser said, so it is a hint and not a fact. Check the contents, not the name.' ),
                array( 'name' => 'isRegularFileInsertionSupported',
                       'signature' => 'isRegularFileInsertionSupported()',
                       'returns' => 'true',
                       'what' => 'Says a file already on disk can become this attribute. What an import script uses.' ),
                array( 'name' => 'insertRegularFile',
                       'signature' => 'insertRegularFile( $object, $objectVersion, $objectLanguage, $objectAttribute, $filePath, &$result )',
                       'returns' => 'true',
                       'what' => 'A file by path. The path comes from whoever called, so it is trusted exactly as far as they are.' ),
                array( 'name' => 'hasStoredFileInformation',
                       'signature' => 'hasStoredFileInformation( $object, $objectVersion, $objectLanguage, $objectAttribute )',
                       'returns' => 'true',
                       'what' => 'Whether there is a file to be had. Asked before every download.' ),
                array( 'name' => 'storedFileInformation',
                       'signature' => 'storedFileInformation( $object, $objectVersion, $objectLanguage, $objectAttribute )',
                       'returns' => 'false',
                       'what' => "Where the file is and what to call it, as array( 'filepath' => ..., 'filename' => ..., 'mime_type' => ..., 'filesize' => ... ). The kernel serves it from there." ) ) ),

        'simple_string' => array(
            'label'   => 'Simple string insertion',
            'default' => false,
            'summary' => 'Let a script set the value from one string, without a form.',
            'what'    => 'The cheapest way to make a datatype usable from the command line and from an import.',
            'methods' => array(
                array( 'name' => 'isSimpleStringInsertionSupported',
                       'signature' => 'isSimpleStringInsertionSupported()',
                       'returns' => 'true',
                       'what' => 'Says a plain string is enough to set this attribute.' ),
                array( 'name' => 'insertSimpleString',
                       'signature' => 'insertSimpleString( $object, $objectVersion, $objectLanguage, $objectAttribute, $string, &$result )',
                       'returns' => 'true',
                       'what' => 'Sets the value from that string. Usually the same work as fromString(), and worth calling it rather than repeating it.' ) ) ),

        'serialize' => array(
            'label'   => 'Packages',
            'default' => false,
            'summary' => 'Travel between installations inside a package.',
            'what'    => 'A package carries content classes and objects between installations. Without these, an attribute of this type arrives empty on the other side - and nothing reports it.',
            'methods' => array(
                array( 'name' => 'serializeContentClassAttribute',
                       'signature' => 'serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )',
                       'returns' => 'null',
                       'what' => 'Writes the class settings into the package xml, as children of $attributeParametersNode.' ),
                array( 'name' => 'unserializeContentClassAttribute',
                       'signature' => 'unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )',
                       'returns' => 'null',
                       'what' => 'Reads them back. Must survive xml written by an older version of this datatype.' ),
                array( 'name' => 'serializeContentObjectAttribute',
                       'signature' => 'serializeContentObjectAttribute( $package, $objectAttribute )',
                       'returns' => 'parent::serializeContentObjectAttribute( $package, $objectAttribute )',
                       'what' => 'Writes one attribute value into the package. A file goes in the package directory and only its name in the xml.' ),
                array( 'name' => 'unserializeContentObjectAttribute',
                       'signature' => 'unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )',
                       'returns' => 'null',
                       'what' => 'Reads one back, on the installation that is receiving it.' ) ) ),

        'diff' => array(
            'label'   => 'Version differences',
            'default' => false,
            'summary' => 'Show what changed between two versions of an attribute.',
            'what'    => 'What the version comparison screen draws. Without it the screen says the attribute changed and not how.',
            'methods' => array(
                array( 'name' => 'diff',
                       'signature' => 'diff( $old, $new, $options = false )',
                       'returns' => 'false',
                       'what' => 'A description of the difference between two attribute values, or false for no useful difference. Returning an eZDiffContentObject or a string are both read.' ) ) ),

        'custom_action' => array(
            'label'   => 'Buttons of its own',
            'default' => false,
            'summary' => 'Add a button inside the editing field that does something without leaving the form.',
            'what'    => 'Adding a row, browsing for an object, clearing a value: anything that changes the attribute while it is being edited and before it is stored.',
            'methods' => array(
                array( 'name' => 'customObjectAttributeHTTPAction',
                       'signature' => 'customObjectAttributeHTTPAction( $http, $action, $contentObjectAttribute, $parameters )',
                       'returns' => 'null',
                       'what' => 'One of this attribute\'s own buttons was pressed. $action says which. The version is not stored at this point, so whatever is changed here is changed on the draft.' ),
                array( 'name' => 'customClassAttributeHTTPAction',
                       'signature' => 'customClassAttributeHTTPAction( $http, $action, $classAttribute )',
                       'returns' => 'null',
                       'what' => 'The same, in the class editor rather than the object editor.' ) ) ),

        'publish' => array(
            'label'   => 'On publish',
            'default' => false,
            'summary' => 'Do something when the object is published, not when it is saved.',
            'what'    => 'A draft is saved many times and published once. Anything that should happen once - sending, indexing elsewhere, telling another system - belongs here and not in storeObjectAttribute().',
            'methods' => array(
                array( 'name' => 'onPublish',
                       'signature' => 'onPublish( $contentObjectAttribute, $contentObject, $publishedNodes )',
                       'returns' => 'true',
                       'what' => 'The object has been published and its nodes exist. Anything slow here is felt by whoever pressed publish, so queue it rather than do it.' ) ) ),

        'cleanup' => array(
            'label'   => 'Cleaning up',
            'default' => false,
            'summary' => 'Remove whatever the attribute left elsewhere when it goes.',
            'what'    => 'Only needed by a datatype that keeps something outside its own row: a table, a file, a row somewhere else. Without it, deleting content leaves it behind for ever.',
            'methods' => array(
                array( 'name' => 'deleteStoredObjectAttribute',
                       'signature' => 'deleteStoredObjectAttribute( $contentObjectAttribute, $version = null )',
                       'returns' => 'null',
                       'what' => 'One attribute is going. $version is null when the whole object is going and every version with it - which is the case where a shared file may finally be removed.' ),
                array( 'name' => 'deleteStoredClassAttribute',
                       'signature' => 'deleteStoredClassAttribute( $classAttribute, $version = null )',
                       'returns' => 'null',
                       'what' => 'The attribute is being taken off the class. Everything every object of that class stored for it is about to become unreachable.' ),
                array( 'name' => 'trashStoredObjectAttribute',
                       'signature' => 'trashStoredObjectAttribute( $contentObjectAttribute, $version = null )',
                       'returns' => 'null',
                       'what' => 'The object went to the trash rather than away. Nothing may be removed here: it can still come back.' ),
                array( 'name' => 'restoreTrashedObjectAttribute',
                       'signature' => 'restoreTrashedObjectAttribute( $contentObjectAttribute )',
                       'returns' => 'null',
                       'what' => 'And it came back.' ) ) ),

        'translatable' => array(
            'label'   => 'Translatable',
            'default' => true,
            'summary' => 'Hold a different value per language.',
            'what'    => 'Most datatypes should be. A value that must be the same in every language - a price, an id, a date - should not be.',
            'methods' => array(
                array( 'name' => 'isTranslatable',
                       'signature' => 'isTranslatable()',
                       'returns' => 'true',
                       'what' => 'Whether each language keeps its own value. Turning this off after content exists leaves the translations behind, unreachable.' ) ) ),

        'relation' => array(
            'label'   => 'Relates to other objects',
            'default' => false,
            'summary' => 'Say that this attribute points at other content.',
            'what'    => 'What makes reverse relations, cache clearing on the other object, and "what links here" work. A datatype that holds object ids and does not say so silently breaks all three.',
            'methods' => array(
                array( 'name' => 'isRelationType',
                       'signature' => 'isRelationType()',
                       'returns' => 'true',
                       'what' => 'Says the value is a reference to content.' ),
                array( 'name' => 'removeRelatedObjectItem',
                       'signature' => 'removeRelatedObjectItem( $contentObjectAttribute, $objectID )',
                       'returns' => 'null',
                       'what' => 'An object this attribute pointed at has gone. Take it out of the value, or the attribute keeps a reference to nothing.' ) ) ),

        'batch' => array(
            'label'   => 'Batch initialisation',
            'default' => false,
            'summary' => 'Fill in the new attribute on existing content in one statement.',
            'what'    => 'Adding an attribute to a class with a hundred thousand objects takes a row each. This does it in one, which is the difference between a class edit that finishes and one that times out.',
            'methods' => array(
                array( 'name' => 'supportsBatchInitializeObjectAttribute',
                       'signature' => 'supportsBatchInitializeObjectAttribute()',
                       'returns' => 'true',
                       'what' => 'Says the default value can be written without looking at each object.' ),
                array( 'name' => 'batchInitializeObjectAttributeData',
                       'signature' => 'batchInitializeObjectAttributeData( $classAttribute )',
                       'returns' => 'array()',
                       'what' => "The columns and values to write, as array( 'data_text' => \"''\" ). The values go straight into sql, so nothing that came from a request may appear here unescaped - and there is no attribute in scope to take it from, which is the point." ) ) ),
        );
    }

    // ── The templates a datatype needs ───────────────────────────────────────

    /**
     * Every template a datatype can have, and which capability calls for it.
     *
     * @return array
     */
    public static function templates()
    {
        return array(
            'templates/content/datatype/edit/%type%.tpl' => array(
                'needs' => 'object_input',
                'what'  => 'The editing field. What an editor types into.' ),
            'templates/content/datatype/view/%type%.tpl' => array(
                'needs' => false,
                'what'  => 'The attribute as a visitor sees it.' ),
            'templates/class/datatype/edit/%type%.tpl' => array(
                'needs' => 'class_settings',
                'what'  => 'The settings form in the class editor.' ),
            'templates/class/datatype/view/%type%.tpl' => array(
                'needs' => 'class_settings',
                'what'  => 'The settings as the class overview shows them.' ),
            'templates/content/datatype/collect/%type%.tpl' => array(
                'needs' => 'collect',
                'what'  => 'The field a visitor fills in on a published page.' ),
            'templates/content/datatype/result/%type%.tpl' => array(
                'needs' => 'collect',
                'what'  => 'What the visitor is shown after they have filled it in.' ),
            'templates/content/datatype/diff/%type%.tpl' => array(
                'needs' => 'diff',
                'what'  => 'The difference between two versions.' ),
        );
    }

    // ── What this wizard can put in ──────────────────────────────────────────

    /**
     * @return array
     */
    public static function parts()
    {
        return array(
            'datatype' => array(
                'label' => 'The datatype class',
                'description' => 'The class itself, with a method for every capability chosen below and a note on each saying what the kernel calls it for.',
                'default' => true ),
            'templates' => array(
                'label' => 'Templates',
                'description' => 'The editing field, the view, the class settings form and whatever else the chosen capabilities need - each one a working template rather than an empty file.',
                'default' => true ),
            'settings' => array(
                'label' => 'Registration',
                'description' => 'content.ini so the kernel finds the datatype, and design.ini so it finds the templates. Without the second the datatype loads and draws nothing.',
                'default' => true ),
            'examples' => array(
                'label' => 'API examples',
                'description' => 'Setting the value from a script, reading it in a template, adding the attribute to a class, and what each method is called for.',
                'default' => true ),
            'sql' => array(
                'label' => 'Notes on storage',
                'description' => 'Which column holds what, and what a table of its own would have to look like if the columns are not enough.',
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
                'description' => 'What it is, how to switch it on, and what each chosen capability means.',
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
            'type'     => self::safeType( isset( $input['type'] ) ? $input['type'] : '' ),
            'class'    => self::safeClass( isset( $input['class'] ) ? $input['class'] : '' ),
            'title'    => self::text( isset( $input['title'] ) ? $input['title'] : '', 120 ),
            'summary'  => self::text( isset( $input['summary'] ) ? $input['summary'] : '', 250 ),
            'author'   => self::text( isset( $input['author'] ) ? $input['author'] : '', 120 ),
            'vendor'   => self::safeName( isset( $input['vendor'] ) ? $input['vendor'] : '', true ),
            'version'  => self::text( isset( $input['version'] ) ? $input['version'] : '', 20 ),
            'licence'  => self::licence_id( isset( $input['licence'] ) ? $input['licence'] : '' ),
            'group'    => self::text( isset( $input['group'] ) ? $input['group'] : '', 60 ),
        );

        if ( $settings['type'] === '' && $settings['name'] !== '' )
            $settings['type'] = self::safeType( str_replace( '_', '', $settings['name'] ) );
        if ( $settings['class'] === '' && $settings['type'] !== '' )
            $settings['class'] = self::safeClass( $settings['type'] . 'Type' );
        if ( $settings['title'] === '' && $settings['name'] !== '' )
            $settings['title'] = ucwords( str_replace( '_', ' ', $settings['name'] ) );
        if ( $settings['version'] === '' )
            $settings['version'] = '1.0.0';
        if ( $settings['vendor'] === '' )
            $settings['vendor'] = 'exponential';
        if ( $settings['group'] === '' )
            $settings['group'] = 'Custom';
        if ( $settings['summary'] === '' )
            $settings['summary'] = 'A content datatype for Exponential.';

        // Which columns the value lives in. One at least, or the attribute has
        // nowhere to put anything.
        $wanted = isset( $input['storage'] ) && is_array( $input['storage'] ) ? $input['storage'] : null;
        foreach ( self::storageColumns() as $key => $column )
            $settings['storage'][$key] = $wanted === null
                                         ? ( $key === 'data_text' )
                                         : in_array( $key, $wanted, true );

        // Which abilities it has.
        $chosen = isset( $input['capabilities'] ) && is_array( $input['capabilities'] ) ? $input['capabilities'] : null;
        foreach ( self::capabilities() as $key => $capability )
            $settings['capabilities'][$key] = !empty( $capability['required'] )
                                              || ( $chosen === null ? $capability['default']
                                                                    : in_array( $key, $chosen, true ) );

        // Which class settings it keeps, and what each is for.
        $settings['settings_used'] = array();
        if ( isset( $input['class_setting_names'] ) && is_array( $input['class_setting_names'] ) )
        {
            $columns = self::settingColumns();
            foreach ( $input['class_setting_names'] as $column => $label )
            {
                if ( !isset( $columns[$column] ) || !is_scalar( $label ) )
                    continue;

                $label = self::safeIdentifier( $label );
                if ( $label === '' )
                    continue;

                $settings['settings_used'][$column] = $label;
            }
        }

        $chosenParts = isset( $input['parts'] ) && is_array( $input['parts'] ) ? $input['parts'] : null;
        foreach ( self::parts() as $key => $part )
            $settings['parts'][$key] = $chosenParts === null ? $part['default'] : in_array( $key, $chosenParts, true );

        return $settings;
    }

    /**
     * A datatype string: what goes in the database against every attribute of
     * this type, and in every ini that mentions it.
     *
     * Lower case, letters and digits. It can never be changed once content
     * exists, so it is worth being strict about now.
     *
     * @param string $value
     * @return string
     */
    public static function safeType( $value )
    {
        if ( !is_string( $value ) )
            return '';

        $value = strtolower( preg_replace( '/[^A-Za-z0-9]+/', '', $value ) );

        return $value !== null && preg_match( '/^[a-z][a-z0-9]{2,40}$/', $value ) ? $value : '';
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
     * A name for one class setting: it becomes part of a php constant and part
     * of a form field name, so it may be neither markup nor a surprise.
     *
     * @param string $value
     * @return string
     */
    public static function safeIdentifier( $value )
    {
        if ( !is_scalar( $value ) )
            return '';

        $value = strtolower( preg_replace( '/[^A-Za-z0-9_]+/', '_', (string) $value ) );
        $value = trim( (string) $value, '_' );

        return preg_match( '/^[a-z][a-z0-9_]{0,40}$/', $value ) ? $value : '';
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

        if ( $settings['type'] === '' )
            $problems[] = 'The datatype needs an identifier: lower case letters and digits, three to forty one characters, starting with a letter. This is what goes in the database against every attribute of this type and cannot be changed afterwards.';

        if ( $settings['class'] === '' )
            $problems[] = 'The class needs a name: letters and digits, starting with a letter.';

        if ( $settings['class'] !== '' && class_exists( $settings['class'] ) )
            $problems[] = 'A class called ' . $settings['class'] . ' already exists on this installation. Choose another name.';

        // A datatype string that is already taken would be shadowed by whichever
        // of the two the kernel happened to load first.
        if ( $settings['type'] !== '' && in_array( $settings['type'], self::existingTypes(), true ) )
            $problems[] = 'A datatype called ' . $settings['type'] . ' is already installed. Two datatypes with the same identifier cannot both work; choose another.';

        if ( !in_array( true, $settings['storage'], true ) )
            $problems[] = 'Choose at least one column for the value to live in, or the attribute has nowhere to store anything.';

        if ( $settings['capabilities']['sorting'] )
        {
            $needed = $settings['storage']['sort_key_string'] || $settings['storage']['sort_key_int'];
            if ( !$needed )
                $problems[] = 'Sorting needs sort_key_string or sort_key_int as well: the sort key is written to one of those columns and nowhere else.';
        }

        return $problems;
    }

    /**
     * The datatype identifiers this installation already knows.
     *
     * @return array of string
     */
    public static function existingTypes()
    {
        $types = array();

        foreach ( (array) eZINI::instance( 'content.ini' )->variable( 'DataTypeSettings', 'AvailableDataTypes' ) as $type )
            if ( is_string( $type ) )
                $types[] = $type;

        return $types;
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
        if ( $settings['name'] === '' || $settings['type'] === '' || $settings['class'] === '' )
            return array();

        $parts = $settings['parts'];
        $files = array();

        if ( $parts['datatype'] )
            $files['datatypes/' . $settings['type'] . '/' . $settings['type'] . 'type.php']
                = self::datatypeClass( $settings );

        if ( $parts['templates'] )
            foreach ( self::templates() as $path => $template )
            {
                if ( $template['needs'] !== false && !$settings['capabilities'][$template['needs']] )
                    continue;

                $files[str_replace( '%type%', $settings['type'], $path )]
                    = self::templateFor( $settings, $path );
            }

        if ( $parts['settings'] )
        {
            $files['settings/content.ini.append.php'] = self::contentIni( $settings );
            $files['settings/design.ini.append.php']  = self::designIni( $settings );
        }

        if ( $parts['examples'] )
            $files['doc/examples.php'] = self::examples( $settings );

        if ( $parts['sql'] )
            $files['doc/storage.md'] = self::storageNotes( $settings );

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
     * Every method this datatype will carry, in the order they will be written.
     *
     * @param array $settings
     * @return array
     */
    public static function chosenMethods( array $settings )
    {
        $methods = array();

        foreach ( self::capabilities() as $key => $capability )
        {
            if ( empty( $settings['capabilities'][$key] ) )
                continue;

            foreach ( $capability['methods'] as $method )
                $methods[] = array_merge( $method, array( 'capability' => $key,
                                                          'capability_label' => $capability['label'] ) );
        }

        return $methods;
    }

    /**
     * The datatype class itself.
     *
     * @param array $settings
     * @return string
     */
    protected static function datatypeClass( array $settings )
    {
        $class = $settings['class'];
        $type  = $settings['type'];

        $php  = "<?php\n/**\n * " . $class . " - the " . $settings['title'] . " datatype.\n *\n";
        $php .= " * " . wordwrap( $settings['summary'], 74, "\n * " ) . "\n *\n";
        $php .= " * Stored in " . implode( ', ', self::usedStorage( $settings ) ) . " of\n";
        $php .= " * ezcontentobject_attribute.\n *\n";
        $php .= " * Registered at the foot of this file, and named in content.ini. Every method\n";
        $php .= " * below is one the kernel calls; none of them does anything useful yet, and\n";
        $php .= " * each says what it is for and what it has to give back.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $php .= "class " . $class . " extends eZDataType\n{\n";

        $php .= "    /**\n";
        $php .= "     * What goes in the database against every attribute of this type, and in\n";
        $php .= "     * every ini that mentions it. It cannot be changed once content exists.\n";
        $php .= "     */\n";
        $php .= "    const DATA_TYPE_STRING = '" . self::phpString( $type ) . "';\n\n";

        // The class settings, as constants: the column, and the form field name
        // that goes with it. Two names that must agree, in one place.
        foreach ( $settings['settings_used'] as $column => $label )
        {
            $constant = strtoupper( $label );
            $php .= "    /** The class setting \"" . self::commentText( $label ) . "\", kept in " . $column . ". */\n";
            $php .= "    const " . $constant . "_FIELD = '" . self::phpString( $column ) . "';\n";
            $php .= "    const " . $constant . "_VARIABLE = '_" . self::phpString( $type . '_' . $label ) . "_';\n\n";
        }

        $php .= "    /**\n";
        $php .= "     * The kernel builds this with no arguments, so everything it needs to know\n";
        $php .= "     * about itself is said here.\n";
        $php .= "     */\n";
        $php .= "    public function __construct()\n    {\n";
        $php .= "        parent::__construct(\n";
        $php .= "            self::DATA_TYPE_STRING,\n";
        $php .= "            ezpI18n::tr( 'extension/" . self::phpString( $settings['name'] ) . "/datatypes', '"
              . self::phpString( $settings['title'] ) . "', 'Datatype name' ),\n";
        $php .= "            array( 'serialize_supported' => " . ( $settings['capabilities']['serialize'] ? 'true' : 'false' ) . " ) );\n";
        $php .= "    }\n";

        $lastCapability = null;
        foreach ( self::chosenMethods( $settings ) as $method )
        {
            if ( $method['capability'] !== $lastCapability )
            {
                $lastCapability = $method['capability'];
                $php .= "\n    // \xe2\x94\x80\xe2\x94\x80 " . $method['capability_label'] . " ";
                $php .= str_repeat( "\xe2\x94\x80", max( 4, 66 - strlen( $method['capability_label'] ) ) ) . "\n";
            }

            $php .= "\n    /**\n";
            $php .= "     * " . wordwrap( $method['what'], 70, "\n     * " ) . "\n";
            $php .= "     */\n";
            $php .= "    public function " . $method['signature'] . "\n    {\n";
            $php .= "        // Not written yet. Returning " . self::shortReturn( $method['returns'] ) . " is what the\n";
            $php .= "        // datatype that ships closest to this one does.\n";
            $php .= "        return " . $method['returns'] . ";\n";
            $php .= "    }\n";
        }

        $php .= "}\n\n";
        $php .= "// This is what makes the class a datatype. Without it the file loads and\n";
        $php .= "// nothing in the system knows the type exists.\n";
        $php .= "eZDataType::register( " . $class . "::DATA_TYPE_STRING, '" . self::phpString( $class ) . "' );\n";

        return $php;
    }

    /**
     * A return expression, short enough to sit inside a comment.
     *
     * @param string $returns
     * @return string
     */
    protected static function shortReturn( $returns )
    {
        $returns = trim( (string) $returns );

        return strlen( $returns ) > 34 ? 'what is below' : $returns;
    }

    /**
     * The storage columns this datatype was told to use.
     *
     * @param array $settings
     * @return array of string
     */
    public static function usedStorage( array $settings )
    {
        $used = array();

        foreach ( self::storageColumns() as $key => $column )
            if ( !empty( $settings['storage'][$key] ) )
                $used[] = $key;

        return $used ? $used : array( 'no column' );
    }

    // ── Templates ────────────────────────────────────────────────────────────

    /**
     * One template, written to do the obvious thing rather than left empty.
     *
     * @param array $settings
     * @param string $path the unsubstituted path, which says which one it is
     * @return string
     */
    protected static function templateFor( array $settings, $path )
    {
        $type  = $settings['type'];
        $base  = '$attribute_base' . '}_data_text_{$attribute.id';
        $notice = "{* " . self::commentText( $settings['title'] ) . " datatype.\n"
                . "   Written by the datatype wizard. This is a working template, not a\n"
                . "   placeholder: change it rather than start from nothing. *}\n";

        if ( strpos( $path, 'content/datatype/edit' ) !== false )
            return $notice
                 . "<div class=\"block\">\n"
                 . "<label>{\$attribute.contentclass_attribute_name|wash}:</label>\n"
                 . "<input class=\"box\"\n"
                 . "       type=\"text\"\n"
                 . "       size=\"40\"\n"
                 . "       name=\"{" . $base . "}\"\n"
                 . "       id=\"ezcoa-{\$attribute.contentclass_attribute_identifier}\"\n"
                 . "       value=\"{\$attribute.content|wash( xhtml )}\" />\n"
                 . "{* Any validation error set by validateObjectAttributeHTTPInput()\n"
                 . "   appears here, and nowhere else. *}\n"
                 . "{if \$attribute.has_validation_error}\n"
                 . "    <div class=\"validation-error\">{\$attribute.validation_error|wash}</div>\n"
                 . "{/if}\n"
                 . "{if \$attribute.contentclass_attribute.description}\n"
                 . "    <div class=\"attribute-description\">{\$attribute.contentclass_attribute.description|wash}</div>\n"
                 . "{/if}\n"
                 . "</div>\n";

        if ( strpos( $path, 'content/datatype/view' ) !== false )
            return $notice
                 . "{* Everything that came from an editor leaves here washed. This is the\n"
                 . "   template a visitor sees, so it is the last place that can go wrong. *}\n"
                 . "{if \$attribute.has_content}\n"
                 . "    <div class=\"attribute-" . $type . "\">{\$attribute.content|wash}</div>\n"
                 . "{/if}\n";

        if ( strpos( $path, 'class/datatype/edit' ) !== false )
        {
            $tpl = $notice . "<div class=\"class-attribute-settings\">\n";

            if ( count( $settings['settings_used'] ) === 0 )
                $tpl .= "{* No class settings were chosen, so there is nothing to ask for here. *}\n";

            foreach ( $settings['settings_used'] as $column => $label )
            {
                $field = '{$attribute_base}_' . $type . '_' . $label . '_{$class_attribute.id}';
                $tpl .= "<div class=\"block\">\n"
                      . "<label>" . ucwords( str_replace( '_', ' ', $label ) ) . ":</label>\n"
                      . "<input class=\"box\" type=\"text\" size=\"20\"\n"
                      . "       name=\"" . $field . "\"\n"
                      . "       value=\"{\$class_attribute." . $column . "|wash( xhtml )}\" />\n"
                      . "</div>\n";
            }

            return $tpl . "</div>\n";
        }

        if ( strpos( $path, 'class/datatype/view' ) !== false )
        {
            $tpl = $notice . "<div class=\"class-attribute-settings\">\n";

            foreach ( $settings['settings_used'] as $column => $label )
                $tpl .= "<p>" . ucwords( str_replace( '_', ' ', $label ) )
                      . ": {\$class_attribute." . $column . "|wash}</p>\n";

            if ( count( $settings['settings_used'] ) === 0 )
                $tpl .= "{* No class settings were chosen. *}\n";

            return $tpl . "</div>\n";
        }

        if ( strpos( $path, 'content/datatype/collect' ) !== false )
            return $notice
                 . "{* A visitor fills this in on a published page. Whatever it sends is\n"
                 . "   checked by validateCollectionAttributeHTTPInput() and nowhere else. *}\n"
                 . "<div class=\"block\">\n"
                 . "<label>{\$attribute.contentclass_attribute_name|wash}:</label>\n"
                 . "<input class=\"box\" type=\"text\" size=\"40\"\n"
                 . "       name=\"{" . $base . "}\"\n"
                 . "       value=\"\" />\n"
                 . "{if \$attribute.has_validation_error}\n"
                 . "    <div class=\"validation-error\">{\$attribute.validation_error|wash}</div>\n"
                 . "{/if}\n"
                 . "</div>\n";

        if ( strpos( $path, 'content/datatype/result' ) !== false )
            return $notice
                 . "{* What the visitor is shown once they have sent it. *}\n"
                 . "<p>{\$collection_attribute.contentobject_attribute.contentclass_attribute_name|wash}:\n"
                 . "   {\$collection_attribute.data_text|wash}</p>\n";

        if ( strpos( $path, 'content/datatype/diff' ) !== false )
            return $notice
                 . "{* Two versions side by side. \$attribute here is what diff() returned. *}\n"
                 . "<div class=\"attribute-diff\">{\$attribute|wash}</div>\n";

        return $notice;
    }

    // ── Registration ─────────────────────────────────────────────────────────

    /**
     * content.ini: where the kernel looks, and what it may offer.
     *
     * @param array $settings
     * @return string
     */
    protected static function contentIni( array $settings )
    {
        $ini  = self::iniHeader( $settings, 'Datatype registration' );
        $ini .= "[DataTypeSettings]\n";
        $ini .= "# The extension whose datatypes/ directory is searched. Without this line\n";
        $ini .= "# the class below is never looked for, however correctly it is named.\n";
        $ini .= "ExtensionDirectories[]=" . $settings['name'] . "\n\n";
        $ini .= "# What the class editor offers. The file is looked for at\n";
        $ini .= "# datatypes/" . $settings['type'] . "/" . $settings['type'] . "type.php inside this extension.\n";
        $ini .= "AvailableDataTypes[]=" . $settings['type'] . "\n";

        if ( $settings['group'] !== '' )
        {
            $ini .= "\n# Which group the class editor lists it under.\n";
            $ini .= "DataTypeGroup[" . $settings['type'] . "]=" . self::iniValue( $settings['group'], 60 ) . "\n";
        }

        $ini .= "\n*/ ?>\n";

        return $ini;
    }

    /**
     * design.ini: where the templates are.
     *
     * A datatype whose templates cannot be found loads, registers, appears in
     * the class editor, and draws an empty field. This is the line that is
     * forgotten, and this is what it does.
     *
     * @param array $settings
     * @return string
     */
    protected static function designIni( array $settings )
    {
        $ini  = self::iniHeader( $settings, 'Design settings' );
        $ini .= "[ExtensionSettings]\n";
        $ini .= "# Where the templates for this datatype are. Without this line the datatype\n";
        $ini .= "# works and draws nothing: the field is empty in the editor and the value\n";
        $ini .= "# never appears on a page.\n";
        $ini .= "DesignExtensions[]=" . $settings['name'] . "\n";
        $ini .= "\n*/ ?>\n";

        return $ini;
    }

    // ── Documentation ────────────────────────────────────────────────────────

    /**
     * Worked examples: how to set the value, read it, and add the attribute.
     *
     * @param array $settings
     * @return string
     */
    protected static function examples( array $settings )
    {
        $type  = $settings['type'];
        $class = $settings['class'];

        $php  = "<?php\n/**\n * " . $class . " - worked examples.\n *\n";
        $php .= " * Not part of the extension: a file to read, and to copy lines out of. It is\n";
        $php .= " * under doc/ rather than datatypes/ so nothing loads it by accident.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $php .= "// Nothing below runs on its own.\nreturn;\n\n";

        $php .= "// \xe2\x94\x80\xe2\x94\x80\xe2\x94\x80 Adding the attribute to a content class "
              . str_repeat( "\xe2\x94\x80", 24 ) . "\n//\n";
        $php .= "// Usually done in the admin. From a script it looks like this, and this is\n";
        $php .= "// also what a package does when it installs a class.\n\n";
        $php .= "\$class = eZContentClass::fetchByIdentifier( 'article' );\n\n";
        $php .= "\$attribute = eZContentClassAttribute::create(\n";
        $php .= "    \$class->attribute( 'id' ),\n";
        $php .= "    " . $class . "::DATA_TYPE_STRING,\n";
        $php .= "    array( 'identifier' => 'my_field',\n";
        $php .= "           'name'       => 'My field',\n";
        $php .= "           'version'    => eZContentClass::VERSION_STATUS_DEFINED ) );\n\n";
        $php .= "\$attribute->store();\n\n\n";

        $php .= "// \xe2\x94\x80\xe2\x94\x80\xe2\x94\x80 Reading the value " . str_repeat( "\xe2\x94\x80", 45 ) . "\n//\n";
        $php .= "// objectAttributeContent() decides what comes back here, so whatever it\n";
        $php .= "// returns is what every caller and every template will be handed.\n\n";
        $php .= "\$object    = eZContentObject::fetch( 42 );\n";
        $php .= "\$dataMap   = \$object->attribute( 'data_map' );\n";
        $php .= "\$attribute = \$dataMap['my_field'];\n\n";
        $php .= "var_dump( \$attribute->attribute( 'content' ) );\n";
        $php .= "var_dump( \$attribute->attribute( 'has_content' ) );\n\n\n";

        $php .= "// \xe2\x94\x80\xe2\x94\x80\xe2\x94\x80 In a template " . str_repeat( "\xe2\x94\x80", 49 ) . "\n//\n";
        $php .= "// {\$node.data_map.my_field.content}\n";
        $php .= "// {if \$node.data_map.my_field.has_content} ... {/if}\n";
        $php .= "//\n";
        $php .= "// The view template this extension ships is used automatically, so\n";
        $php .= "// {attribute_view_gui attribute=\$node.data_map.my_field} draws it.\n\n\n";

        if ( !empty( $settings['capabilities']['string'] ) )
        {
            $php .= "// \xe2\x94\x80\xe2\x94\x80\xe2\x94\x80 Setting it from a script " . str_repeat( "\xe2\x94\x80", 39 ) . "\n//\n";
            $php .= "// This is what fromString() is for, and why it is worth writing even when\n";
            $php .= "// nothing seems to need it yet.\n\n";
            $php .= "\$attribute->fromString( 'a new value' );\n";
            $php .= "\$attribute->store();\n\n";
            $php .= "// Or, for a whole object at once:\n";
            $php .= "\$object = eZContentFunctions::updateAndPublishObject( \$object, array(\n";
            $php .= "    'attributes' => array( 'my_field' => 'a new value' ) ) );\n\n\n";
        }

        if ( !empty( $settings['capabilities']['sorting'] ) )
        {
            $php .= "// \xe2\x94\x80\xe2\x94\x80\xe2\x94\x80 Sorting a listing by it " . str_repeat( "\xe2\x94\x80", 40 ) . "\n//\n";
            $php .= "// The sort happens over the sort key column, not over the value, so a\n";
            $php .= "// listing is only sorted as well as sortKey() reduced it.\n\n";
            $php .= "\$nodes = eZContentObjectTreeNode::subTreeByNodeID( array(\n";
            $php .= "    'SortBy' => array( array( 'attribute', true, \$classAttributeID ) ) ), 2 );\n\n\n";
        }

        if ( !empty( $settings['capabilities']['collect'] ) )
        {
            $php .= "// \xe2\x94\x80\xe2\x94\x80\xe2\x94\x80 Reading what visitors sent " . str_repeat( "\xe2\x94\x80", 36 ) . "\n//\n";
            $php .= "// Collected information is stored against the object, not in it, so it\n";
            $php .= "// survives the content being edited and is fetched separately.\n\n";
            $php .= "\$collections = eZInformationCollection::fetchCollectionsList( \$object->attribute( 'id' ) );\n\n";
            $php .= "foreach ( \$collections as \$collection )\n";
            $php .= "    foreach ( \$collection->attribute( 'attributes' ) as \$collected )\n";
            $php .= "        echo \$collected->attribute( 'data_text' ), PHP_EOL;\n\n\n";
        }

        $php .= "// \xe2\x94\x80\xe2\x94\x80\xe2\x94\x80 Every method written, and what calls it "
              . str_repeat( "\xe2\x94\x80", 24 ) . "\n//\n";
        foreach ( self::chosenMethods( $settings ) as $method )
        {
            $php .= "// " . $method['signature'] . "\n";
            $php .= "//     " . wordwrap( $method['what'], 68, "\n//     " ) . "\n//\n";
        }

        return $php;
    }

    /**
     * Which column holds what, and what to do when they are not enough.
     *
     * @param array $settings
     * @return string
     */
    protected static function storageNotes( array $settings )
    {
        $columns = self::storageColumns();

        $md  = "# Where " . $settings['title'] . " keeps its value\n\n";
        $md .= "An attribute of this type is one row in `ezcontentobject_attribute`. That row\n";
        $md .= "has five columns a datatype may use and no others.\n\n";
        $md .= "## Columns this datatype uses\n\n";
        $md .= "| Column | Type | What it holds |\n| --- | --- | --- |\n";

        foreach ( $columns as $key => $column )
            if ( !empty( $settings['storage'][$key] ) )
                $md .= "| `" . $key . "` | " . $column['sql'] . " | " . $column['what'] . " |\n";

        $unused = array();
        foreach ( $columns as $key => $column )
            if ( empty( $settings['storage'][$key] ) )
                $unused[] = '`' . $key . '`';

        if ( $unused )
            $md .= "\nLeft alone: " . implode( ', ', $unused ) . ".\n";

        if ( count( $settings['settings_used'] ) )
        {
            $md .= "\n## Class settings\n\n";
            $md .= "Chosen once per content class, in `ezcontentclass_attribute`.\n\n";
            $md .= "| Column | Setting |\n| --- | --- |\n";
            foreach ( $settings['settings_used'] as $column => $label )
                $md .= "| `" . $column . "` | " . $label . " |\n";
        }

        $md .= "\n## When five columns are not enough\n\n";
        $md .= "A datatype holding more than one value per attribute keeps a table of its own\n";
        $md .= "and puts the key to it in one of the columns above. The table is created by a\n";
        $md .= "schema file shipped with the extension:\n\n";
        $md .= "```\nextension/" . $settings['name'] . "/share/db_schema.dba\n```\n\n";
        $md .= "and the rows are written in `storeObjectAttribute()` and removed in\n";
        $md .= "`deleteStoredObjectAttribute()`. Both directions matter: without the second,\n";
        $md .= "deleting content leaves the rows behind for ever.\n\n";
        $md .= "Two things are easy to forget in such a table:\n\n";
        $md .= "- The attribute id **and** the version. Every version of an object has its own\n";
        $md .= "  attribute row, so a table keyed only on the attribute id loses history and\n";
        $md .= "  corrupts drafts.\n";
        $md .= "- The language. A translatable datatype has one row per language, and a table\n";
        $md .= "  that does not carry the language code cannot hold a translation.\n";

        return $md;
    }

    /**
     * What it is, how to switch it on, and what each capability means.
     *
     * @param array $settings
     * @param array $paths
     * @return string
     */
    protected static function readme( array $settings, array $paths = array() )
    {
        $readme  = "# " . $settings['title'] . "\n\n" . $settings['summary'] . "\n\n";
        $readme .= "A content datatype for Exponential / eZ Publish legacy, registered as\n";
        $readme .= "`" . $settings['type'] . "` and implemented by `" . $settings['class'] . "`.\n\n";

        $readme .= "## Switching it on\n\n";
        $readme .= "1. Put this directory in `extension/" . $settings['name'] . "`.\n";
        $readme .= "2. Add it to `settings/override/site.ini.append.php`:\n\n";
        $readme .= "```\n" . self::activation( $settings ) . "\n```\n\n";
        $readme .= "3. Clear the caches, and regenerate the autoloads:\n\n";
        $readme .= "```\nphp bin/php/ezpgenerateautoloads.php --extension=" . $settings['name'] . "\nphp bin/php/ezcache.php --clear-all\n```\n\n";
        $readme .= "4. The datatype then appears in the class editor";
        $readme .= $settings['group'] !== '' ? ", under " . $settings['group'] . ".\n\n" : ".\n\n";

        $readme .= "## What it can do\n\n";
        foreach ( self::capabilities() as $key => $capability )
        {
            $has = !empty( $settings['capabilities'][$key] );
            $readme .= "- " . ( $has ? "**" . $capability['label'] . "** — " . $capability['summary']
                                     : $capability['label'] . " — not implemented." ) . "\n";
        }

        $readme .= "\n## Where the value lives\n\n";
        foreach ( self::usedStorage( $settings ) as $column )
            $readme .= "- `" . $column . "`\n";
        $readme .= "\nSee `doc/storage.md`.\n";

        if ( count( $paths ) )
        {
            $readme .= "\n## What is in here\n\n```\n";
            foreach ( $paths as $path )
                $readme .= $path . "\n";
            $readme .= "```\n";
        }

        $readme .= "\n## Before it is used for real\n\n";
        $readme .= "Every method in the class returns what the kernel expects and does nothing\n";
        $readme .= "else. The datatype installs, appears, and holds nothing until they are\n";
        $readme .= "written. Start with the editing group; the rest can follow.\n";

        return $readme;
    }
}
}

require_once 'kernel/setup/expextensionwizard.php';


?>
