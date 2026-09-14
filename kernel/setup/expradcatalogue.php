<?php
/**
 * File containing the expRADCatalogue class.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * Every point this system can be extended at, and what it takes to extend it.
 *
 * eZ is extended in a handful of ways that repeat: a class in a directory the
 * kernel scans, a class named by an ini setting, a file named by convention, or
 * a template in a design. Which of those applies, and which ini line registers
 * it, is the part nobody remembers - it is spread across the kernel, the lib and
 * a dozen settings files, and is discoverable only by reading the source.
 *
 * This is that knowledge, written down: for each point, what it is for, where
 * the code goes, what registers it, and which class or interface it has to
 * satisfy. Every entry was checked against this installation's own source
 * rather than remembered.
 *
 * The RAD tools page is drawn from it, so a tool that exists and a point that
 * has none are listed the same way and neither can be forgotten.
 */
class expRADCatalogue
{
    /**
     * The groups entries are listed under, in the order they are shown.
     *
     * @return array key => array( title, description )
     */
    public static function groups()
    {
        return array(
            'content' => array(
                'title' => 'Content',
                'description' => 'What content is made of, and how it is edited and stored.' ),
            'templates' => array(
                'title' => 'Templates and design',
                'description' => 'What a template can call, and what a design can replace.' ),
            'modules' => array(
                'title' => 'Modules and views',
                'description' => 'Addresses the site answers on, and who may reach them.' ),
            'workflow' => array(
                'title' => 'Workflow, events and jobs',
                'description' => 'What happens when something is published, and what runs on its own.' ),
            'storage' => array(
                'title' => 'Storage and infrastructure',
                'description' => 'Where things are kept, and how they get there.' ),
            'packaging' => array(
                'title' => 'Packaging and shop',
                'description' => 'Moving things between installations, and selling them.' ),
            'access' => array(
                'title' => 'Users, access and language',
                'description' => 'Who gets in, what they may do, and in what language.' ),
        );
    }

    /**
     * How a thing is registered, which is the part that is never obvious.
     *
     * @return array key => a sentence describing it.
     */
    public static function mechanisms()
    {
        return array(
            'directory' => 'A class in a directory the kernel scans. The directory is named by a RepositoryDirectories setting, and the class is found by its file name.',
            'handler'   => 'A class named by an ini setting and loaded through eZExtension::getHandlerClass. The setting gives an alias, the alias gives the class.',
            'autoload'  => 'A class reached through the extension autoload path, registered once in an ini and then available everywhere.',
            'file'      => 'A file in a place the kernel looks by name. Nothing registers it; being there is the registration.',
            'design'    => 'A template in a design, found through the design chain rather than by being named anywhere.',
            'ini'       => 'Settings only. Nothing is written but ini, and the behaviour changes.',
            'override'  => 'A kernel class replaced by one of your own through the override autoload path. The heaviest of these mechanisms and the last resort: nothing registers the replacement by name, so two extensions replacing the same class is a fight neither of them knows it is in.',
        );
    }

    /**
     * Every extension point, checked against this installation's source.
     *
     * @return array key => array with group, title, what, where, register,
     *         contract, mechanism, source and tool.
     */
    public static function points()
    {
        return array(

        // ── Content ──────────────────────────────────────────────────────────

        'datatype' => array(
            'group' => 'content',
            'title' => 'Datatype',
            'what'  => 'A kind of value a content class attribute can hold, with its own editing, validation, storage and display.',
            'where' => 'extension/<name>/datatypes/<datatype>/<datatype>type.php',
            'register' => 'content.ini [DataTypeSettings] ExtensionDirectories[] and AvailableDataTypes[], plus design.ini [ExtensionSettings] DesignExtensions[] or it draws nothing',
            'contract' => 'extends eZDataType',
            'mechanism' => 'directory',
            'source' => 'kernel/classes/ezdatatype.php',
            'tool'  => 'setup/datatype' ),

        'contentclass' => array(
            'group' => 'content',
            'title' => 'Content class',
            'what'  => 'A type of content: its attributes, their datatypes, and how an instance of it is named.',
            'where' => 'extension/<name>/share/package/<name>/ezcontentclass/',
            'register' => 'Installed as a package, or created in the admin interface and exported.',
            'contract' => 'eZContentClass definition, as a class package',
            'mechanism' => 'file',
            'source' => 'kernel/classes/ezcontentclass.php',
            'tool'  => 'setup/contentextension' ),

        'customtag' => array(
            'group' => 'content',
            'title' => 'XML custom tag',
            'what'  => 'A tag authors can use in rich text, with its own attributes and its own template.',
            'register' => 'content.ini [CustomTagSettings] AvailableCustomTags[] and a [<tag>] section',
            'where' => 'extension/<name>/design/standard/templates/content/datatype/view/ezxmltags/<tag>.tpl',
            'contract' => 'A template, plus ini describing the attributes',
            'mechanism' => 'design',
            'source' => 'settings/content.ini',
            'tool'  => 'setup/contentextension' ),

        'xmlinput' => array(
            'group' => 'content',
            'title' => 'XML text input handler',
            'what'  => 'What turns what an author typed into the xml a rich text attribute stores.',
            'where' => 'extension/<name>/classes/<name>xmlinput.php',
            'register' => 'ezxml.ini [InputSettings] HandlerClass',
            'contract' => 'extends eZXMLInputHandler',
            'mechanism' => 'handler',
            'source' => 'kernel/classes/datatypes/ezxmltext/ezxmltext.php',
            'tool'  => 'setup/handlerextension/xmlinput' ),

        'xmloutput' => array(
            'group' => 'content',
            'title' => 'XML text output handler',
            'what'  => 'What turns stored rich text into what a visitor sees.',
            'where' => 'extension/<name>/classes/<name>xmloutput.php',
            'register' => 'ezxml.ini [OutputSettings] HandlerClass',
            'contract' => 'extends eZXMLOutputHandler',
            'mechanism' => 'handler',
            'source' => 'kernel/classes/datatypes/ezxmltext/ezxmltext.php',
            'tool'  => 'setup/handlerextension/xmloutput' ),

        'infocollector' => array(
            'group' => 'content',
            'title' => 'Information collection behaviour',
            'what'  => 'What happens when a visitor fills in a form built out of content: what the submission is called, whether it is kept, whether it is emailed, and what the visitor is shown afterwards. Matched per content class, so a poll and a contact form built the same way behave differently.',
            'where' => 'settings/override/collect.ini.append.php, and templates under content/collectedinfo/',
            'register' => 'collect.ini [InfoSettings] TypeList[<class>], [EmailSettings] SendEmailList[<class>], [CollectionSettings] CollectAnonymousDataList[<class>], [DisplaySettings] DisplayList[<class>]',
            'contract' => 'No class to write: a setting per content class, and a template per type',
            'mechanism' => 'ini',
            'source' => 'kernel/classes/ezinformationcollection.php',
            'tool'  => 'setup/settingsextension' ),

        'viewcachecleanup' => array(
            'group' => 'content',
            'title' => 'View cache clearing rules',
            'what'  => 'Which other pages have to be rebuilt when one object is published. The default clears the object, its parents and what relates to it; a group named after a content class identifier says what else - a listing that has to change when a comment is posted, an object somewhere else entirely.',
            'where' => 'settings/override/viewcache.ini.append.php',
            'register' => 'viewcache.ini [ViewCacheSettings] SmartCacheClear=enabled, then a [<class_identifier>] group with ClearCacheMethod[], DependentClassIdentifier[] and AdditionalObjectIDs[]',
            'contract' => 'No class to write: a group per content class identifier',
            'mechanism' => 'ini',
            'source' => 'kernel/classes/ezcontentcachemanager.php',
            'tool'  => 'setup/settingsextension' ),

        // ── Templates and design ────────────────────────────────────────────

        'operator' => array(
            'group' => 'templates',
            'title' => 'Template operator',
            'what'  => 'Something a template can pipe a value through: {$value|my_operator()}. One class may answer to many names, and what it promises the compiler decides whether it runs once at compile time or on every request for ever.',
            'where' => 'extension/<name>/autoloads/<name>operators.php',
            'register' => 'site.ini [TemplateSettings] ExtensionAutoloadPath[], through $eZTemplateOperatorArray in autoloads/eztemplateautoload.php - not an ini naming the class',
            'contract' => 'operatorList(), namedParameterList(), operatorTemplateHints() and modify()',
            'mechanism' => 'autoload',
            'source' => 'lib/eztemplate/classes/eztemplate.php',
            'tool'  => 'setup/templateoperator' ),

        'fetchfunction' => array(
            'group' => 'templates',
            'title' => 'Template fetch function',
            'what'  => 'Something a template can ask a module for: fetch( \'module\', \'thing\', hash( ... ) ).',
            'where' => 'extension/<name>/modules/<module>/function_definition.php',
            'register' => 'Being in the module directory is the registration.',
            'contract' => 'A $FunctionList naming a class and method per function',
            'mechanism' => 'file',
            'source' => 'lib/ezutils/classes/ezfunctionhandler.php',
            'tool'  => 'setup/templateoperator' ),

        'attributeoperator' => array(
            'group' => 'templates',
            'title' => 'Attribute operator',
            'what'  => 'An operator that applies to a content attribute of a particular datatype.',
            'where' => 'extension/<name>/classes/<name>attributeoperator.php',
            'register' => 'template.ini [AttributeOperator] OutputFormatter[<format>]',
            'contract' => 'Implements ezpAttributeOperatorFormatterInterface',
            'mechanism' => 'handler',
            'source' => 'kernel/private/eztemplate/ezpattributeoperatorformatterinterface.php',
            'tool'  => 'setup/handlerextension/attributeoperator' ),

        'templatefunction' => array(
            'group' => 'templates',
            'title' => 'Template function',
            'what'  => 'Something a template calls rather than pipes through: {my_function arg=1}, optionally with a body it may draw none, one or many times. How {section} and {foreach} are built.',
            'where' => 'extension/<name>/autoloads/<name>functions.php',
            'register' => 'site.ini [TemplateSettings] ExtensionAutoloadPath[], through $eZTemplateFunctionArray in autoloads/eztemplateautoload.php',
            'contract' => 'functionList(), attributeList(), hasChildren() and process()',
            'mechanism' => 'autoload',
            'source' => 'lib/eztemplate/classes/eztemplatesectionfunction.php',
            'tool'  => 'setup/templateoperator' ),

        'fetchalias' => array(
            'group' => 'templates',
            'title' => 'Fetch alias',
            'what'  => 'A name for a fetch that is written out in full somewhere else, so templates can be short.',
            'where' => 'extension/<name>/settings/fetchalias.ini.append.php',
            'register' => 'fetchalias.ini, one section per alias',
            'contract' => 'Settings only',
            'mechanism' => 'ini',
            'source' => 'settings/fetchalias.ini',
            'tool'  => 'setup/templateoperator' ),

        'design' => array(
            'group' => 'templates',
            'title' => 'Design extension',
            'what'  => 'The templates, stylesheets and images a site is drawn with.',
            'where' => 'extension/<name>/design/<name>/',
            'register' => 'design.ini [ExtensionSettings] DesignExtensions[]',
            'contract' => 'Templates, found through the design chain',
            'mechanism' => 'design',
            'source' => 'kernel/common/eztemplatedesignresource.php',
            'tool'  => 'setup/designextension' ),

        'override' => array(
            'group' => 'templates',
            'title' => 'Template override set',
            'what'  => 'A template used in place of another, for the content it matches and nothing else.',
            'where' => 'extension/<name>/design/<design>/override/templates/',
            'register' => 'override.ini, one section per override',
            'contract' => 'Source, MatchFile, Subdir and Match lines',
            'mechanism' => 'design',
            'source' => 'kernel/common/eztemplatedesignresource.php',
            'tool'  => 'setup/designextension' ),

        // ── Modules and views ───────────────────────────────────────────────

        'moduletables' => array(
            'group' => 'modules',
            'title' => 'Module over existing tables',
            'what'  => 'Administration and a template API for tables that already exist, here or on another database.',
            'where' => 'extension/<name>/modules/<module>/ and classes/',
            'register' => 'module.ini [ModuleSettings] ExtensionRepositories[] and ModuleList[]',
            'contract' => 'eZPersistentObject, or a class with the same methods',
            'mechanism' => 'directory',
            'source' => 'kernel/classes/ezpersistentobject.php',
            'tool'  => 'setup/moduleextension' ),

        'module' => array(
            'group' => 'modules',
            'title' => 'Module',
            'what'  => 'A new address the site answers on, with its own views and its own policies.',
            'where' => 'extension/<name>/modules/<module>/module.php',
            'register' => 'module.ini [ModuleSettings] ExtensionRepositories[] and ModuleList[]',
            'contract' => '$Module, $ViewList and $FunctionList',
            'mechanism' => 'directory',
            'source' => 'lib/ezutils/classes/ezmodule.php',
            'tool'  => 'setup/modulewizard' ),

        'view' => array(
            'group' => 'modules',
            'title' => 'View for an existing module',
            'what'  => 'One more thing an existing module can be asked to do.',
            'where' => 'extension/<name>/modules/<module>/<view>.php',
            'register' => 'The module\'s own module.php, extended by the extension',
            'contract' => 'A script setting $Result',
            'mechanism' => 'file',
            'source' => 'lib/ezutils/classes/ezmodule.php',
            'tool'  => 'setup/modulewizard' ),

        'policy' => array(
            'group' => 'modules',
            'title' => 'Policy function and limitation',
            'what'  => 'A thing a role can be granted, and what it can be narrowed by.',
            'where' => 'extension/<name>/modules/<module>/function_definition.php',
            'register' => 'The $FunctionList of the module',
            'contract' => 'Names, and a limitation description per function',
            'mechanism' => 'file',
            'source' => 'kernel/classes/ezrole.php',
            'tool'  => 'setup/modulewizard' ),

        'restprovider' => array(
            'group' => 'modules',
            'title' => 'REST provider',
            'what'  => 'A set of addresses answering outside the template system, for something else to call.',
            'where' => 'extension/<name>/classes/rest/<name>provider.php',
            'register' => 'rest.ini, through the rest provider registry',
            'contract' => 'Implements ezpRestProviderInterface',
            'mechanism' => 'handler',
            'source' => 'kernel/private/rest/classes/rest_provider.php',
            'tool'  => 'setup/handlerextension/restprovider' ),

        'urlaliasfilter' => array(
            'group' => 'access',
            'title' => 'URL alias filter',
            'what'  => 'Runs over every url this system generates, before it is stored, and may rewrite it. The nearest thing here to an output filter over addresses: every alias, for every object, in every language, passes through it as it is made.',
            'where' => 'extension/<name>/classes/<name>urlfilter.php',
            'register' => 'site.ini [URLTranslator] FilterClasses[]',
            'contract' => 'extends eZURLAliasFilter, and implements process( $text, &$languageObject, &$caller )',
            'mechanism' => 'handler',
            'source' => 'kernel/classes/ezurlaliasfilter.php',
            'tool'  => 'setup/handlerextension/urlfilter' ),

        'publishfilter' => array(
            'group' => 'content',
            'title' => 'Asynchronous publishing filter',
            'what'  => 'Decides whether a version is published in the request or handed to the queue. Publishing a large object blocks whoever pressed the button; a queue that takes everything makes small edits feel broken. This is where that line is drawn.',
            'where' => 'extension/<name>/classes/<name>publishfilter.php',
            'register' => 'content.ini [PublishingSettings] AsynchronousPublishingFilters[]',
            'contract' => 'Implements ezpAsynchronousPublishingFilterInterface: one accept() method',
            'mechanism' => 'handler',
            'source' => 'kernel/private/interfaces/asynchronouspublishingfilter.php',
            'tool'  => 'setup/handlerextension/publishfilter' ),

        'mobilefilter' => array(
            'group' => 'access',
            'title' => 'Mobile device filter',
            'what'  => 'Decides whether a request came from a phone and what to do about it. The one that ships matches user agent strings against patterns, which ages badly; a filter of its own can use a header a proxy sets or a hint the browser gives.',
            'where' => 'extension/<name>/classes/<name>mobilefilter.php',
            'register' => 'site.ini [SiteAccessSettings] MobileDeviceFilterClass',
            'contract' => 'Implements ezpMobileDeviceDetectFilterInterface: process, isMobileDevice, getUserAgentAlias, redirect',
            'mechanism' => 'handler',
            'source' => 'kernel/private/classes/ezpmobiledevicedetectfilterinterface.php',
            'tool'  => 'setup/handlerextension/mobilefilter' ),

        'restprerouting' => array(
            'group' => 'access',
            'title' => 'REST pre routing filter',
            'what'  => 'The earliest place there is to see a REST request. Nothing has been matched and no controller chosen, so a request can be rewritten or turned away before anything has committed to answering it - and before authentication, so about a caller nobody has identified.',
            'where' => 'extension/<name>/classes/<name>preroutingfilter.php',
            'register' => 'rest.ini [PreRoutingFilters] Filters[]',
            'contract' => 'Implements ezpRestPreRoutingFilterInterface',
            'mechanism' => 'handler',
            'source' => 'kernel/private/rest/classes/interfaces/prerouting_filter.php',
            'tool'  => 'setup/handlerextension/restprerouting' ),

        'restrequestfilter' => array(
            'group' => 'access',
            'title' => 'REST request filter',
            'what'  => 'Runs once the request object is built and the route is known, so it knows which controller is about to answer. Where a header is read, a parameter normalised, or a request refused on grounds that depend on what it asked for.',
            'where' => 'extension/<name>/classes/<name>requestfilter.php',
            'register' => 'rest.ini [RequestFilters] Filters[]',
            'contract' => 'Implements ezpRestRequestFilterInterface',
            'mechanism' => 'handler',
            'source' => 'kernel/private/rest/classes/interfaces/request_filter.php',
            'tool'  => 'setup/handlerextension/restrequestfilter' ),

        'restresultfilter' => array(
            'group' => 'access',
            'title' => 'REST result filter',
            'what'  => 'Runs after the controller has worked out its answer and before it becomes json or xml. The result is still ordinary php here, so it can be added to or reshaped once for every format rather than in each renderer.',
            'where' => 'extension/<name>/classes/<name>resultfilter.php',
            'register' => 'rest.ini [ResultFilters] Filters[]',
            'contract' => 'Implements ezpRestResultFilterInterface',
            'mechanism' => 'handler',
            'source' => 'kernel/private/rest/classes/interfaces/result_filter.php',
            'tool'  => 'setup/handlerextension/restresultfilter' ),

        'restresponsefilter' => array(
            'group' => 'access',
            'title' => 'REST response filter',
            'what'  => 'The output filter of the REST layer: the last thing that happens before an answer leaves. A header on every response, a body wrapped, a content type changed, without touching a controller.',
            'where' => 'extension/<name>/classes/<name>responsefilter.php',
            'register' => 'rest.ini [ResponseFilters] Filters[]',
            'contract' => 'Implements ezpRestResponseFilterInterface',
            'mechanism' => 'handler',
            'source' => 'kernel/private/rest/classes/interfaces/response_filter.php',
            'tool'  => 'setup/handlerextension/restresponsefilter' ),

        'restprefixfilter' => array(
            'group' => 'access',
            'title' => 'REST prefix filter',
            'what'  => 'What decides where the api lives and which version of it a request asked for. The one that ships reads a regular expression; replacing it is how the api moves off /api/ or takes its version from somewhere other than the path.',
            'where' => 'extension/<name>/classes/<name>prefixfilter.php',
            'register' => 'rest.ini [System] PrefixFilterClass',
            'contract' => 'Extends ezpRestPrefixFilterInterface: parseVersionValue() and filter()',
            'mechanism' => 'handler',
            'source' => 'kernel/private/rest/classes/prefix_filter.php',
            'tool'  => false ),

        'restroutefilter' => array(
            'group' => 'modules',
            'title' => 'REST route filter',
            'what'  => 'Something that inspects or changes a REST request before it is routed.',
            'where' => 'extension/<name>/classes/rest/<name>routefilter.php',
            'register' => 'rest.ini [RouteSettings] RouteSettingImpl',
            'contract' => 'Extends ezpRestRouteFilterInterface',
            'mechanism' => 'handler',
            'source' => 'kernel/private/rest/classes/interfaces/route_filter.php',
            'tool'  => 'setup/handlerextension/restroutefilter' ),

        'ajaxfunction' => array(
            'group' => 'modules',
            'title' => 'Server-side ajax function',
            'what'  => 'Something the browser can call and get json back from, without a page.',
            'where' => 'extension/<name>/classes/ezjscserverfunctions<name>.php',
            'register' => 'ezjscore.ini [ezjscServer] FunctionList[]',
            'contract' => 'Static methods taking an argument list',
            'mechanism' => 'handler',
            'source' => 'extension/ezjscore',
            'tool'  => 'setup/handlerextension/ajaxfunction' ),

        // ── Workflow, events and jobs ───────────────────────────────────────

        'workflowevent' => array(
            'group' => 'workflow',
            'title' => 'Workflow event type',
            'what'  => 'A step a workflow can take when something is published, moved or removed.',
            'where' => 'extension/<name>/eventtypes/event/<event>/<event>type.php',
            'register' => 'workflow.ini [EventSettings] ExtensionDirectories[] and AvailableEventTypes[]',
            'contract' => 'extends eZWorkflowEventType',
            'mechanism' => 'directory',
            'source' => 'kernel/classes/ezworkfloweventtype.php',
            'tool'  => 'setup/workflowevent' ),

        'trigger' => array(
            'group' => 'workflow',
            'title' => 'Trigger',
            'what'  => 'The point in an operation where a workflow is given the chance to run.',
            'where' => 'extension/<name>/settings/workflow.ini.append.php',
            'register' => 'workflow.ini, and the operation definition that declares the trigger',
            'contract' => 'Settings, and an operation body with a trigger in it',
            'mechanism' => 'ini',
            'source' => 'kernel/classes/eztrigger.php',
            'tool'  => 'setup/settingsextension' ),

        'notificationtype' => array(
            'group' => 'workflow',
            'title' => 'Notification event type',
            'what'  => 'A kind of thing people can be notified about.',
            'where' => 'extension/<name>/notification/event/<type>/<type>type.php',
            'register' => 'notification.ini [NotificationEventTypeSettings] RepositoryDirectories[] and AvailableNotificationEventTypes[]',
            'contract' => 'extends eZNotificationEventType',
            'mechanism' => 'directory',
            'source' => 'kernel/classes/notification/eznotificationeventtype.php',
            'tool'  => 'setup/handlerextension/notificationtype' ),

        'notificationhandler' => array(
            'group' => 'workflow',
            'title' => 'Notification handler',
            'what'  => 'What decides who is told, and how they are told.',
            'where' => 'extension/<name>/notification/handler/<handler>/<handler>handler.php',
            'register' => 'notification.ini [NotificationEventHandlerSettings] ExtensionDirectories[] and AvailableNotificationEventTypes[] (the kernel reads the Types variable in the Handler section too)',
            'contract' => 'extends eZNotificationEventHandler',
            'mechanism' => 'directory',
            'source' => 'kernel/classes/notification/eznotificationeventhandler.php',
            'tool'  => 'setup/handlerextension/notificationhandler' ),

        'cronjob' => array(
            'group' => 'workflow',
            'title' => 'Cronjob script and part',
            'what'  => 'Something that runs on its own, on a schedule, outside any request.',
            'where' => 'extension/<name>/cronjobs/<script>.php',
            'register' => 'cronjob.ini [CronjobSettings] ExtensionDirectories[] and Scripts[], or a part of its own',
            'contract' => 'A script run by runcronjobs.php, with $cli and $sys available',
            'mechanism' => 'directory',
            'source' => 'runcronjobs.php',
            'tool'  => 'setup/cronjobs' ),

        'eventlistener' => array(
            'group' => 'workflow',
            'title' => 'Kernel event listener',
            'what'  => 'Something called when the kernel reaches a named point, such as a request arriving.',
            'where' => 'extension/<name>/classes/<name>listener.php',
            'register' => 'site.ini [Event] Listeners[]=<event>@<callback>',
            'contract' => 'A callable taking whatever the event passes',
            'mechanism' => 'ini',
            'source' => 'kernel/private/classes/ezpevent.php',
            'tool'  => 'setup/settingsextension' ),

        // ── Storage and infrastructure ──────────────────────────────────────

        'dbhandler' => array(
            'group' => 'storage',
            'title' => 'Database handler',
            'what'  => 'A kind of database the whole system can run on.',
            'where' => 'extension/<name>/classes/<name>db.php',
            'register' => 'site.ini [DatabaseSettings] ImplementationAlias[<alias>]',
            'contract' => 'extends eZDBInterface',
            'mechanism' => 'handler',
            'source' => 'lib/ezdb/classes/ezdb.php',
            'tool'  => 'setup/handlerextension/dbhandler' ),

        'clusterhandler' => array(
            'group' => 'storage',
            'title' => 'Cluster file handler',
            'what'  => 'Where files live when more than one server serves the same site. Everything the kernel reads or writes under var/ goes through this, so it is the widest reaching of the storage points and the one most worth extending from eZFSFileHandler rather than from the bare interface.',
            'where' => 'extension/<name>/classes/<name>filehandler.php',
            'register' => 'file.ini [ClusteringSettings] FileHandler',
            'contract' => 'Implements eZClusterFileHandlerInterface, or extends eZFSFileHandler which already does',
            'mechanism' => 'handler',
            'source' => 'kernel/classes/ezclusterfilehandler.php',
            'tool'  => 'setup/handlerextension/cluster' ),

        'dfsbackend' => array(
            'group' => 'storage',
            'title' => 'DFS backend',
            'what'  => 'Where the DFS cluster handler puts the bytes: a mounted share, an object store, anywhere reachable. The index of what exists stays in the database; this only moves file contents.',
            'where' => 'extension/<name>/classes/<name>dfsbackend.php',
            'register' => 'file.ini [eZDFSClusteringSettings] DFSBackend',
            'contract' => 'Implements eZDFSFileHandlerDFSBackendInterface',
            'mechanism' => 'handler',
            'source' => 'kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerdfsbackendinterface.php',
            'tool'  => 'setup/handlerextension/dfsbackend' ),

        'dfsdbbackend' => array(
            'group' => 'storage',
            'title' => 'DFS database backend',
            'what'  => 'The other half of DFS: the index of which files exist, how big they are and which are being generated. It is what stops two servers building the same cache entry at once, so it is the harder half to replace.',
            'where' => 'extension/<name>/classes/<name>dfsdbbackend.php',
            'register' => 'file.ini [eZDFSClusteringSettings] DBBackend',
            'contract' => 'Follows the shape of eZDFSFileHandlerMySQLiBackend',
            'mechanism' => 'handler',
            'source' => 'kernel/private/classes/clusterfilehandlers/dfsbackends/mysqli.php',
            'tool'  => 'setup/handlerextension/dfsdbbackend' ),

        'binaryhandler' => array(
            'group' => 'storage',
            'title' => 'Binary file handler',
            'what'  => 'How an uploaded file is stored and handed back.',
            'where' => 'extension/<name>/classes/<name>binaryfilehandler.php',
            'register' => 'file.ini [BinaryFileSettings] Handler',
            'contract' => 'Implements the binary file handler interface',
            'mechanism' => 'handler',
            'source' => 'kernel/classes/ezbinaryfilehandler.php',
            'tool'  => 'setup/handlerextension/binaryfile' ),

        'searchengine' => array(
            'group' => 'storage',
            'title' => 'Search engine',
            'what'  => 'What indexes content as it is published, and what answers when somebody searches.',
            'where' => 'extension/<name>/classes/<name>searchengine.php',
            'register' => 'site.ini [SearchSettings] SearchEngine',
            'contract' => 'implements ezpSearchEngine',
            'mechanism' => 'handler',
            'source' => 'kernel/private/interfaces/ezpsearchengine.php',
            'tool'  => 'setup/handlerextension/search' ),

        'sessionhandler' => array(
            'group' => 'storage',
            'title' => 'Session handler',
            'what'  => 'Where sessions are kept and how they are cleaned up.',
            'where' => 'extension/<name>/classes/<name>sessionhandler.php',
            'register' => 'site.ini [Session] Handler',
            'contract' => 'extends ezpSessionHandler',
            'mechanism' => 'handler',
            'source' => 'lib/ezsession/classes/ezsession.php',
            'tool'  => 'setup/handlerextension/session' ),

        'mailtransport' => array(
            'group' => 'storage',
            'title' => 'Mail transport',
            'what'  => 'How mail leaves the system.',
            'where' => 'extension/<name>/classes/<name>transport.php',
            'register' => 'site.ini [MailSettings] TransportAlias[<alias>]',
            'contract' => 'extends eZMailTransport',
            'mechanism' => 'handler',
            'source' => 'lib/ezutils/classes/ezmailtransport.php',
            'tool'  => 'setup/handlerextension/mail' ),

        'staticcache' => array(
            'group' => 'storage',
            'title' => 'Static cache handler',
            'what'  => 'What writes pages to disk so the web server can serve them without php.',
            'where' => 'extension/<name>/classes/<name>staticcache.php',
            'register' => 'site.ini [ContentSettings] StaticCacheHandler',
            'contract' => 'Implements ezpStaticCache',
            'mechanism' => 'handler',
            'source' => 'kernel/setup/expstaticcacherunner.php',
            'tool'  => 'setup/handlerextension/staticcache' ),

        'imagehandler' => array(
            'group' => 'storage',
            'title' => 'Image handler and aliases',
            'what'  => 'How an image is scaled and what sizes exist.',
            'where' => 'extension/<name>/settings/image.ini.append.php',
            'register' => 'image.ini [AliasSettings] AliasList[] and a section per alias',
            'contract' => 'Settings, and optionally an eZImageHandler class',
            'mechanism' => 'ini',
            'source' => 'lib/ezimage/classes/ezimagemanager.php',
            'tool'  => 'setup/settingsextension' ),

        // ── Packaging and shop ──────────────────────────────────────────────

        'packagehandler' => array(
            'group' => 'packaging',
            'title' => 'Package handler',
            'what'  => 'A kind of thing that can be put in a package and taken out again.',
            'where' => 'extension/<name>/packagehandlers/<handler>/<handler>packagehandler.php',
            'register' => 'package.ini [PackageSettings] HandlerAlias[<alias>]',
            'contract' => 'extends eZPackageHandler',
            'mechanism' => 'handler',
            'source' => 'kernel/classes/ezpackagehandler.php',
            'tool'  => 'setup/handlerextension/packagehandler' ),

        'packagecreation' => array(
            'group' => 'packaging',
            'title' => 'Package creation handler',
            'what'  => 'The steps the admin interface walks through when a package is made.',
            'where' => 'extension/<name>/packagehandlers/<handler>/<handler>creationhandler.php',
            'register' => 'package.ini [CreationSettings] HandlerAlias[<alias>]',
            'contract' => 'extends eZPackageCreationHandler',
            'mechanism' => 'handler',
            'source' => 'kernel/classes/ezpackagecreationhandler.php',
            'tool'  => 'setup/handlerextension/packagecreation' ),

        'packageinstall' => array(
            'group' => 'packaging',
            'title' => 'Package installation handler',
            'what'  => 'What happens when a package is installed or taken back out.',
            'where' => 'extension/<name>/packagehandlers/<handler>/<handler>installhandler.php',
            'register' => 'package.ini [InstallerSettings] HandlerAlias[<alias>]',
            'contract' => 'extends eZPackageInstallationHandler',
            'mechanism' => 'handler',
            'source' => 'kernel/classes/ezpackageinstallationhandler.php',
            'tool'  => 'setup/handlerextension/packageinstall' ),

        'vathandler' => array(
            'group' => 'packaging',
            'title' => 'VAT handler',
            'what'  => 'What rate of tax applies to what, for whom.',
            'where' => 'extension/<name>/classes/<name>vathandler.php',
            'register' => 'shop.ini [VATSettings] Handler and RepositoryDirectories[]',
            'contract' => 'Implements the VAT handler interface',
            'mechanism' => 'directory',
            'source' => 'kernel/classes/ezvatmanager.php',
            'tool'  => 'setup/handlerextension/vat' ),

        'shippinghandler' => array(
            'group' => 'packaging',
            'title' => 'Shipping handler',
            'what'  => 'What delivery costs, and what the options are.',
            'where' => 'extension/<name>/classes/<name>shippinghandler.php',
            'register' => 'shop.ini [ShippingSettings] Handler and RepositoryDirectories[]',
            'contract' => 'Implements the shipping handler interface',
            'mechanism' => 'directory',
            'source' => 'kernel/classes/ezshippingmanager.php',
            'tool'  => 'setup/handlerextension/shipping' ),

        'basketinfo' => array(
            'group' => 'packaging',
            'title' => 'Basket info handler',
            'what'  => 'What a basket line says about itself: name, price, and what it is.',
            'where' => 'extension/<name>/classes/<name>basketinfohandler.php',
            'register' => 'shop.ini [BasketInfoSettings] Handler and RepositoryDirectories[]',
            'contract' => 'Implements the basket info handler interface',
            'mechanism' => 'directory',
            'source' => 'kernel/classes/basketinfohandlers/ezdefaultbasketinfohandler.php',
            'tool'  => 'setup/handlerextension/basketinfo' ),

        'exchangerate' => array(
            'group' => 'packaging',
            'title' => 'Exchange rate handler',
            'what'  => 'Where the rate between two currencies comes from.',
            'where' => 'extension/<name>/classes/<name>exchangeratehandler.php',
            'register' => 'shop.ini [ExchangeRatesSettings] Handler and RepositoryDirectories[]',
            'contract' => 'Implements the exchange rate handler interface',
            'mechanism' => 'directory',
            'source' => 'kernel/shop/classes/exchangeratehandlers/ezexchangeratesupdatehandler.php',
            'tool'  => 'setup/handlerextension/exchangerate' ),

        // ── Users, access and language ──────────────────────────────────────

        'loginhandler' => array(
            'group' => 'access',
            'title' => 'User login handler',
            'what'  => 'Where a user is checked against when they log in - a directory, another system, anything.',
            'where' => 'extension/<name>/user/<handler>/<handler>user.php',
            'register' => 'site.ini [UserSettings] LoginHandler[] and ExtensionDirectory[]',
            'contract' => 'extends eZUser and implements loginUser()',
            'mechanism' => 'directory',
            'source' => 'kernel/classes/datatypes/ezuser',
            'tool'  => 'setup/handlerextension/login' ),

        'extensionroot' => array(
            'group' => 'access',
            'title' => 'Additional extension roots',
            'what'  => 'Where extensions may live, beyond extension/. A second root - extension_src/ is the usual name - separates what a project wrote from what it took from elsewhere, so ownership is readable off the directory layout rather than off a list somebody maintains. An extension of the same name in a later root shadows the one before it, which is how a vendor package is forked without being edited.',
            'where' => 'settings/override/site.ini.append.php, and the root directory itself',
            'register' => 'site.ini [ExtensionSettings] AdditionalExtensionDirectories[]',
            'contract' => 'Settings only. Every consumer goes through eZExtension::extensionPath() and expandedPathList(), so nothing needs changing to see a new root',
            'mechanism' => 'ini',
            'source' => 'lib/ezutils/classes/ezextension.php',
            'tool'  => 'setup/settingsextension' ),

        'extensionrootfilter' => array(
            'group' => 'access',
            'title' => 'Extension root filter',
            'what'  => 'The last word on which roots are searched, in code rather than in settings. Redefining it is how a root is worked out at runtime - from an environment variable, from which machine this is, from what a deployment put on disk - rather than written into an ini that has to differ per installation.',
            'where' => 'A kernel override of lib/ezutils/classes/ezextension.php',
            'register' => 'Nothing registers it: the kernel calls eZExtension::filterExtensionRootDirectories() and uses what comes back',
            'contract' => 'static filterExtensionRootDirectories( array $roots ) : array',
            'mechanism' => 'override',
            'source' => 'lib/ezutils/classes/ezextension.php',
            'tool'  => false ),

        'siteextension' => array(
            'group' => 'access',
            'title' => 'Site extension',
            'what'  => 'One extension carrying a whole site: its design, its siteaccesses, its grouped settings overrides, its modules and its code. What makes this possible rather than merely tidy is that an extension can now hold settings/siteaccess/<name>/ of its own, so the siteaccess travels with the thing that defines it instead of living in settings/ on one machine.',
            'where' => 'extension/<name>/{design,settings/siteaccess/<sa>,modules,classes,autoloads}/',
            'register' => 'site.ini [ExtensionSettings] ActiveExtensions[] and ActiveAccessExtensions[], plus AdditionalExtensionDirectories[] when it lives outside extension/',
            'contract' => 'No class: a directory laid out the way the kernel looks',
            'mechanism' => 'directory',
            'source' => 'doc/bc/6.0/AdditionalExtensionDirectories.md',
            'tool'  => 'setup/settingsextension' ),

        'iconextension' => array(
            'group' => 'templates',
            'title' => 'Icon theme in an extension',
            'what'  => 'A set of icons an extension brings with it, searched before the ones that ship. The whole chain falls back: the current theme, then any additional themes, then the standard one; and within each, extension directories before share/icons. A missing icon takes the theme default rather than drawing a broken image.',
            'where' => 'extension/<name>/icons/<theme>/',
            'register' => 'icon.ini [ExtensionSettings] IconExtensions[], and [IconSettings] Theme or AdditionalThemeList[]',
            'contract' => 'Image files named after what they illustrate. Served as static files, so no php runs per image',
            'mechanism' => 'ini',
            'source' => 'kernel/common/ezwordtoimageoperator.php',
            'tool'  => 'setup/settingsextension' ),

        'inicache' => array(
            'group' => 'storage',
            'title' => 'Compiled settings and view cache in Redis',
            'what'  => 'Two places the kernel will hand its caches to Redis or Valkey if something answers for them: the compiled ini cache, and the content view cache. Neither is an ini setting - the kernel asks whether a class exists and uses it if it does, so an installation without the extension behaves exactly as before.',
            'where' => 'A class named sevenxValkeyINICache or sevenxValkeyCacheBlock, anywhere the autoloader can see it',
            'register' => 'Nothing registers them: class_exists() decides',
            'contract' => 'instance(), then get() and put() for the block cache; instance() and the compiled file interface for the ini cache',
            'mechanism' => 'handler',
            'source' => 'lib/ezutils/classes/ezini.php',
            'tool'  => false ),

        'inisave' => array(
            'group' => 'access',
            'title' => 'Writing settings from code',
            'what'  => 'Changing an ini file from php rather than by hand: what the settings editor in the admin does, and what an installer or an upgrade script needs. Since 6.0 a direct access write keeps the comments and the ordering of the file it edits rather than rewriting it as bare key and value, so a generated change can be read afterwards by whoever has to maintain it.',
            'where' => 'Any script or module view',
            'register' => 'Nothing to register: eZINI with directAccess true, then setVariable() and save()',
            'contract' => 'setVariable( $section, $name, $value ) then save(); the file keeps its comments',
            'mechanism' => 'file',
            'source' => 'lib/ezutils/classes/ezini.php',
            'tool'  => 'setup/settingsextension' ),

        'accessextension' => array(
            'group' => 'access',
            'title' => 'Siteaccess settings extension',
            'what'  => 'Settings that apply to one siteaccess only, kept with the extension rather than in settings/.',
            'where' => 'extension/<name>/settings/siteaccess/<siteaccess>/',
            'register' => 'site.ini [ExtensionSettings] ActiveAccessExtensions[]',
            'contract' => 'Settings only',
            'mechanism' => 'ini',
            'source' => 'lib/ezutils/classes/ezini.php',
            'tool'  => 'setup/settingsextension' ),

        'translation' => array(
            'group' => 'access',
            'title' => 'Translation',
            'what'  => 'The words the interface uses, in another language.',
            'where' => 'extension/<name>/translations/<locale>/translation.ts',
            'register' => 'i18n.ini, and the locale being available',
            'contract' => 'A ts file of contexts and messages',
            'mechanism' => 'file',
            'source' => 'lib/ezi18n/classes/eztranslationcache.php',
            'tool'  => 'setup/contentextension' ),

        'rssimporthandler' => array(
            'group' => 'access',
            'title' => 'RSS import handler',
            'what'  => 'What an imported feed item becomes once it has been fetched.',
            'where' => 'extension/<name>/rss/ezrssimporthandler.php',
            'register' => 'site.ini [RSSSettings] ActiveExtensions[]',
            'contract' => 'Implements the RSS import handler interface',
            'mechanism' => 'directory',
            'source' => 'kernel/classes/ezrssimport.php',
            'tool'  => 'rss/list' ),

        );
    }

    /**
     * The points of one group, in the order they are declared.
     *
     * @param string $group
     * @return array
     */
    public static function pointsOf( $group )
    {
        $points = array();
        foreach ( self::points() as $key => $point )
            if ( $point['group'] === $group )
                $points[$key] = $point;

        return $points;
    }

    /**
     * How many points have a tool, and how many there are.
     *
     * @return array covered, total
     */
    public static function coverage()
    {
        $covered = 0;
        $points  = self::points();

        foreach ( $points as $point )
            if ( $point['tool'] !== false )
                $covered++;

        return array( 'covered' => $covered, 'total' => count( $points ) );
    }

    /**
     * The ways the list can be narrowed.
     *
     * @return array key => label
     */
    public static function filters()
    {
        return array( 'all'   => 'Everything',
                      'tools' => 'With a tool',
                      'docs'  => 'Documentation only' );
    }

    /**
     * The filter to apply, from what the address asked for.
     *
     * Anything that is not one of the three is everything, because a list that
     * silently shows nothing because of a typo is worse than one that ignores it.
     *
     * @param mixed $show
     * @return string
     */
    public static function filter( $show )
    {
        $filters = self::filters();

        return is_string( $show ) && isset( $filters[$show] ) ? $show : 'all';
    }

    /**
     * How many points each filter would show.
     *
     * @return array key => count
     */
    public static function filterCounts()
    {
        $coverage = self::coverage();

        return array( 'all'   => $coverage['total'],
                      'tools' => $coverage['covered'],
                      'docs'  => $coverage['total'] - $coverage['covered'] );
    }

    /**
     * Whether one point belongs in a filtered list.
     *
     * @param array $point
     * @param string $filter
     * @return bool
     */
    public static function matches( array $point, $filter )
    {
        switch ( $filter )
        {
            case 'tools': return $point['tool'] !== false;
            case 'docs':  return $point['tool'] === false;
        }

        return true;
    }
}
