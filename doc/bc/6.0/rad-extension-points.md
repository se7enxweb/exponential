# Extension points

## Added: every point this system can be extended at, listed in the RAD tools

eZ is extended in a handful of ways that repeat: a class in a directory the
kernel scans, a class named by an ini setting, a file in a place found by
convention, or a template in a design. Which of those applies, and which ini
line registers it, was previously discoverable only by reading the kernel - the
knowledge was spread across `kernel/`, `lib/` and a dozen settings files.

`kernel/setup/expradcatalogue.php` now holds it, and **Setup > RAD** is drawn
from it. A point with a tool and a point without are listed the same way, so
neither can be forgotten.

**48 extension points, 45 with a tool.**

Every entry was checked against this installation's own source; the *Kernel*
column names the file the mechanism actually lives in.

## How things are registered

- **directory** — A class in a directory the kernel scans. The directory is named by a RepositoryDirectories setting, and the class is found by its file name.
- **handler** — A class named by an ini setting and loaded through eZExtension::getHandlerClass. The setting gives an alias, the alias gives the class.
- **autoload** — A class reached through the extension autoload path, registered once in an ini and then available everywhere.
- **file** — A file in a place the kernel looks by name. Nothing registers it; being there is the registration.
- **design** — A template in a design, found through the design chain rather than by being named anywhere.
- **ini** — Settings only. Nothing is written but ini, and the behaviour changes.

## Content

What content is made of, and how it is edited and stored.

### Datatype  
*Tool:* `/setup/datatype`

A kind of value a content class attribute can hold, with its own editing, validation, storage and display.

| | |
|---|---|
| Code | `extension/<name>/datatypes/<datatype>/<datatype>type.php` |
| Registered by | content.ini [DataTypeSettings] ExtensionDirectories[] and AvailableDataTypes[], plus design.ini [ExtensionSettings] DesignExtensions[] or it draws nothing |
| Contract | `extends eZDataType` |
| Mechanism | directory |
| Kernel | `kernel/classes/ezdatatype.php` |

### Content class  
*Tool:* `/setup/contentextension`

A type of content: its attributes, their datatypes, and how an instance of it is named.

| | |
|---|---|
| Code | `extension/<name>/share/package/<name>/ezcontentclass/` |
| Registered by | Installed as a package, or created in the admin interface and exported. |
| Contract | `eZContentClass definition, as a class package` |
| Mechanism | file |
| Kernel | `kernel/classes/ezcontentclass.php` |

### XML custom tag  
*Tool:* `/setup/contentextension`

A tag authors can use in rich text, with its own attributes and its own template.

| | |
|---|---|
| Code | `extension/<name>/design/standard/templates/content/datatype/view/ezxmltags/<tag>.tpl` |
| Registered by | content.ini [CustomTagSettings] AvailableCustomTags[] and a [<tag>] section |
| Contract | `A template, plus ini describing the attributes` |
| Mechanism | design |
| Kernel | `settings/content.ini` |

### XML text input handler  
*Tool:* `/setup/handlerextension/xmlinput`

What turns what an author typed into the xml a rich text attribute stores.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>xmlinput.php` |
| Registered by | ezxml.ini [InputSettings] HandlerClass |
| Contract | `extends eZXMLInputHandler` |
| Mechanism | handler |
| Kernel | `kernel/classes/datatypes/ezxmltext/ezxmltext.php` |

### XML text output handler  
*Tool:* `/setup/handlerextension/xmloutput`

What turns stored rich text into what a visitor sees.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>xmloutput.php` |
| Registered by | ezxml.ini [OutputSettings] HandlerClass |
| Contract | `extends eZXMLOutputHandler` |
| Mechanism | handler |
| Kernel | `kernel/classes/datatypes/ezxmltext/ezxmltext.php` |

### Information collection behaviour  
*Tool:* `/setup/settingsextension`

What happens when a visitor fills in a form built out of content: what the submission is called, whether it is kept, whether it is emailed, and what the visitor is shown afterwards. Matched per content class, so a poll and a contact form built the same way behave differently.

| | |
|---|---|
| Code | `settings/override/collect.ini.append.php, and templates under content/collectedinfo/` |
| Registered by | collect.ini [InfoSettings] TypeList[<class>], [EmailSettings] SendEmailList[<class>], [CollectionSettings] CollectAnonymousDataList[<class>], [DisplaySettings] DisplayList[<class>] |
| Contract | `No class to write: a setting per content class, and a template per type` |
| Mechanism | ini |
| Kernel | `kernel/classes/ezinformationcollection.php` |

### View cache clearing rules  
*Tool:* `/setup/settingsextension`

Which other pages have to be rebuilt when one object is published. The default clears the object, its parents and what relates to it; a group named after a content class identifier says what else - a listing that has to change when a comment is posted, an object somewhere else entirely.

| | |
|---|---|
| Code | `settings/override/viewcache.ini.append.php` |
| Registered by | viewcache.ini [ViewCacheSettings] SmartCacheClear=enabled, then a [<class_identifier>] group with ClearCacheMethod[], DependentClassIdentifier[] and AdditionalObjectIDs[] |
| Contract | `No class to write: a group per content class identifier` |
| Mechanism | ini |
| Kernel | `kernel/classes/ezcontentcachemanager.php` |

## Templates and design

What a template can call, and what a design can replace.

### Template operator  
*Tool:* `/setup/templateoperator`

Something a template can pipe a value through: {$value|my_operator()}. One class may answer to many names, and what it promises the compiler decides whether it runs once at compile time or on every request for ever.

| | |
|---|---|
| Code | `extension/<name>/autoloads/<name>operators.php` |
| Registered by | site.ini [TemplateSettings] ExtensionAutoloadPath[], through $eZTemplateOperatorArray in autoloads/eztemplateautoload.php - not an ini naming the class |
| Contract | `operatorList(), namedParameterList(), operatorTemplateHints() and modify()` |
| Mechanism | autoload |
| Kernel | `lib/eztemplate/classes/eztemplate.php` |

### Template fetch function  
*Tool:* `/setup/templateoperator`

Something a template can ask a module for: fetch( 'module', 'thing', hash( ... ) ).

| | |
|---|---|
| Code | `extension/<name>/modules/<module>/function_definition.php` |
| Registered by | Being in the module directory is the registration. |
| Contract | `A $FunctionList naming a class and method per function` |
| Mechanism | file |
| Kernel | `lib/ezutils/classes/ezfunctionhandler.php` |

### Attribute operator  
*Tool:* `/setup/handlerextension/attributeoperator`

An operator that applies to a content attribute of a particular datatype.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>attributeoperator.php` |
| Registered by | template.ini [AttributeOperator] OutputFormatter[<format>] |
| Contract | `Implements ezpAttributeOperatorFormatterInterface` |
| Mechanism | handler |
| Kernel | `kernel/private/eztemplate/ezpattributeoperatorformatterinterface.php` |

### Template function  
*Tool:* `/setup/templateoperator`

Something a template calls rather than pipes through: {my_function arg=1}, optionally with a body it may draw none, one or many times. How {section} and {foreach} are built.

| | |
|---|---|
| Code | `extension/<name>/autoloads/<name>functions.php` |
| Registered by | site.ini [TemplateSettings] ExtensionAutoloadPath[], through $eZTemplateFunctionArray in autoloads/eztemplateautoload.php |
| Contract | `functionList(), attributeList(), hasChildren() and process()` |
| Mechanism | autoload |
| Kernel | `lib/eztemplate/classes/eztemplatesectionfunction.php` |

### Fetch alias  
*Tool:* `/setup/templateoperator`

A name for a fetch that is written out in full somewhere else, so templates can be short.

| | |
|---|---|
| Code | `extension/<name>/settings/fetchalias.ini.append.php` |
| Registered by | fetchalias.ini, one section per alias |
| Contract | `Settings only` |
| Mechanism | ini |
| Kernel | `settings/fetchalias.ini` |

### Design extension  
*Tool:* `/setup/designextension`

The templates, stylesheets and images a site is drawn with.

| | |
|---|---|
| Code | `extension/<name>/design/<name>/` |
| Registered by | design.ini [ExtensionSettings] DesignExtensions[] |
| Contract | `Templates, found through the design chain` |
| Mechanism | design |
| Kernel | `kernel/common/eztemplatedesignresource.php` |

### Template override set  
*Tool:* `/setup/designextension`

A template used in place of another, for the content it matches and nothing else.

| | |
|---|---|
| Code | `extension/<name>/design/<design>/override/templates/` |
| Registered by | override.ini, one section per override |
| Contract | `Source, MatchFile, Subdir and Match lines` |
| Mechanism | design |
| Kernel | `kernel/common/eztemplatedesignresource.php` |

## Modules and views

Addresses the site answers on, and who may reach them.

### Module over existing tables  
*Tool:* `/setup/moduleextension`

Administration and a template API for tables that already exist, here or on another database.

| | |
|---|---|
| Code | `extension/<name>/modules/<module>/ and classes/` |
| Registered by | module.ini [ModuleSettings] ExtensionRepositories[] and ModuleList[] |
| Contract | `eZPersistentObject, or a class with the same methods` |
| Mechanism | directory |
| Kernel | `kernel/classes/ezpersistentobject.php` |

### Module  
*No tool yet.*

A new address the site answers on, with its own views and its own policies.

| | |
|---|---|
| Code | `extension/<name>/modules/<module>/module.php` |
| Registered by | module.ini [ModuleSettings] ExtensionRepositories[] and ModuleList[] |
| Contract | `$Module, $ViewList and $FunctionList` |
| Mechanism | directory |
| Kernel | `lib/ezutils/classes/ezmodule.php` |

### View for an existing module  
*No tool yet.*

One more thing an existing module can be asked to do.

| | |
|---|---|
| Code | `extension/<name>/modules/<module>/<view>.php` |
| Registered by | The module's own module.php, extended by the extension |
| Contract | `A script setting $Result` |
| Mechanism | file |
| Kernel | `lib/ezutils/classes/ezmodule.php` |

### Policy function and limitation  
*No tool yet.*

A thing a role can be granted, and what it can be narrowed by.

| | |
|---|---|
| Code | `extension/<name>/modules/<module>/function_definition.php` |
| Registered by | The $FunctionList of the module |
| Contract | `Names, and a limitation description per function` |
| Mechanism | file |
| Kernel | `kernel/classes/ezrole.php` |

### REST provider  
*Tool:* `/setup/handlerextension/restprovider`

A set of addresses answering outside the template system, for something else to call.

| | |
|---|---|
| Code | `extension/<name>/classes/rest/<name>provider.php` |
| Registered by | rest.ini, through the rest provider registry |
| Contract | `Implements ezpRestProviderInterface` |
| Mechanism | handler |
| Kernel | `kernel/private/rest/classes/rest_provider.php` |

### REST route filter  
*Tool:* `/setup/handlerextension/restroutefilter`

Something that inspects or changes a REST request before it is routed.

| | |
|---|---|
| Code | `extension/<name>/classes/rest/<name>routefilter.php` |
| Registered by | rest.ini [RouteSettings] RouteSettingImpl |
| Contract | `Extends ezpRestRouteFilterInterface` |
| Mechanism | handler |
| Kernel | `kernel/private/rest/classes/interfaces/route_filter.php` |

### Server-side ajax function  
*Tool:* `/setup/handlerextension/ajaxfunction`

Something the browser can call and get json back from, without a page.

| | |
|---|---|
| Code | `extension/<name>/classes/ezjscserverfunctions<name>.php` |
| Registered by | ezjscore.ini [ezjscServer] FunctionList[] |
| Contract | `Static methods taking an argument list` |
| Mechanism | handler |
| Kernel | `extension/ezjscore` |

## Workflow, events and jobs

What happens when something is published, and what runs on its own.

### Workflow event type  
*Tool:* `/setup/workflowevent`

A step a workflow can take when something is published, moved or removed.

| | |
|---|---|
| Code | `extension/<name>/eventtypes/event/<event>/<event>type.php` |
| Registered by | workflow.ini [EventSettings] ExtensionDirectories[] and AvailableEventTypes[] |
| Contract | `extends eZWorkflowEventType` |
| Mechanism | directory |
| Kernel | `kernel/classes/ezworkfloweventtype.php` |

### Trigger  
*Tool:* `/setup/settingsextension`

The point in an operation where a workflow is given the chance to run.

| | |
|---|---|
| Code | `extension/<name>/settings/workflow.ini.append.php` |
| Registered by | workflow.ini, and the operation definition that declares the trigger |
| Contract | `Settings, and an operation body with a trigger in it` |
| Mechanism | ini |
| Kernel | `kernel/classes/eztrigger.php` |

### Notification event type  
*Tool:* `/setup/handlerextension/notificationtype`

A kind of thing people can be notified about.

| | |
|---|---|
| Code | `extension/<name>/notification/event/<type>/<type>type.php` |
| Registered by | notification.ini [NotificationEventTypeSettings] RepositoryDirectories[] and AvailableNotificationEventTypes[] |
| Contract | `extends eZNotificationEventType` |
| Mechanism | directory |
| Kernel | `kernel/classes/notification/eznotificationeventtype.php` |

### Notification handler  
*Tool:* `/setup/handlerextension/notificationhandler`

What decides who is told, and how they are told.

| | |
|---|---|
| Code | `extension/<name>/notification/handler/<handler>/<handler>handler.php` |
| Registered by | notification.ini [NotificationEventHandlerSettings] ExtensionDirectories[] and AvailableNotificationEventTypes[] (the kernel reads the Types variable in the Handler section too) |
| Contract | `extends eZNotificationEventHandler` |
| Mechanism | directory |
| Kernel | `kernel/classes/notification/eznotificationeventhandler.php` |

### Cronjob script and part  
*Tool:* `/setup/cronjobs`

Something that runs on its own, on a schedule, outside any request.

| | |
|---|---|
| Code | `extension/<name>/cronjobs/<script>.php` |
| Registered by | cronjob.ini [CronjobSettings] ExtensionDirectories[] and Scripts[], or a part of its own |
| Contract | `A script run by runcronjobs.php, with $cli and $sys available` |
| Mechanism | directory |
| Kernel | `runcronjobs.php` |

### Kernel event listener  
*Tool:* `/setup/settingsextension`

Something called when the kernel reaches a named point, such as a request arriving.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>listener.php` |
| Registered by | site.ini [Event] Listeners[]=<event>@<callback> |
| Contract | `A callable taking whatever the event passes` |
| Mechanism | ini |
| Kernel | `kernel/private/classes/ezpevent.php` |

## Storage and infrastructure

Where things are kept, and how they get there.

### Database handler  
*Tool:* `/setup/handlerextension/dbhandler`

A kind of database the whole system can run on.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>db.php` |
| Registered by | site.ini [DatabaseSettings] ImplementationAlias[<alias>] |
| Contract | `extends eZDBInterface` |
| Mechanism | handler |
| Kernel | `lib/ezdb/classes/ezdb.php` |

### Cluster file handler  
*Tool:* `/setup/handlerextension/cluster`

Where files live when more than one server serves the same site. Everything the kernel reads or writes under var/ goes through this, so it is the widest reaching of the storage points and the one most worth extending from eZFSFileHandler rather than from the bare interface.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>filehandler.php` |
| Registered by | file.ini [ClusteringSettings] FileHandler |
| Contract | `Implements eZClusterFileHandlerInterface, or extends eZFSFileHandler which already does` |
| Mechanism | handler |
| Kernel | `kernel/classes/ezclusterfilehandler.php` |

### DFS backend  
*Tool:* `/setup/handlerextension/dfsbackend`

Where the DFS cluster handler puts the bytes: a mounted share, an object store, anywhere reachable. The index of what exists stays in the database; this only moves file contents.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>dfsbackend.php` |
| Registered by | file.ini [eZDFSClusteringSettings] DFSBackend |
| Contract | `Implements eZDFSFileHandlerDFSBackendInterface` |
| Mechanism | handler |
| Kernel | `kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerdfsbackendinterface.php` |

### DFS database backend  
*Tool:* `/setup/handlerextension/dfsdbbackend`

The other half of DFS: the index of which files exist, how big they are and which are being generated. It is what stops two servers building the same cache entry at once, so it is the harder half to replace.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>dfsdbbackend.php` |
| Registered by | file.ini [eZDFSClusteringSettings] DBBackend |
| Contract | `Follows the shape of eZDFSFileHandlerMySQLiBackend` |
| Mechanism | handler |
| Kernel | `kernel/private/classes/clusterfilehandlers/dfsbackends/mysqli.php` |

### Binary file handler  
*Tool:* `/setup/handlerextension/binaryfile`

How an uploaded file is stored and handed back.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>binaryfilehandler.php` |
| Registered by | file.ini [BinaryFileSettings] Handler |
| Contract | `Implements the binary file handler interface` |
| Mechanism | handler |
| Kernel | `kernel/classes/ezbinaryfilehandler.php` |

### Search engine  
*Tool:* `/setup/handlerextension/search`

What indexes content as it is published, and what answers when somebody searches.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>searchengine.php` |
| Registered by | site.ini [SearchSettings] SearchEngine |
| Contract | `implements ezpSearchEngine` |
| Mechanism | handler |
| Kernel | `kernel/private/interfaces/ezpsearchengine.php` |

### Session handler  
*Tool:* `/setup/handlerextension/session`

Where sessions are kept and how they are cleaned up.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>sessionhandler.php` |
| Registered by | site.ini [Session] Handler |
| Contract | `extends ezpSessionHandler` |
| Mechanism | handler |
| Kernel | `lib/ezsession/classes/ezsession.php` |

### Mail transport  
*Tool:* `/setup/handlerextension/mail`

How mail leaves the system.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>transport.php` |
| Registered by | site.ini [MailSettings] TransportAlias[<alias>] |
| Contract | `extends eZMailTransport` |
| Mechanism | handler |
| Kernel | `lib/ezutils/classes/ezmailtransport.php` |

### Static cache handler  
*Tool:* `/setup/handlerextension/staticcache`

What writes pages to disk so the web server can serve them without php.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>staticcache.php` |
| Registered by | site.ini [ContentSettings] StaticCacheHandler |
| Contract | `Implements ezpStaticCache` |
| Mechanism | handler |
| Kernel | `kernel/setup/expstaticcacherunner.php` |

### Image handler and aliases  
*Tool:* `/setup/settingsextension`

How an image is scaled and what sizes exist.

| | |
|---|---|
| Code | `extension/<name>/settings/image.ini.append.php` |
| Registered by | image.ini [AliasSettings] AliasList[] and a section per alias |
| Contract | `Settings, and optionally an eZImageHandler class` |
| Mechanism | ini |
| Kernel | `lib/ezimage/classes/ezimagemanager.php` |

## Packaging and shop

Moving things between installations, and selling them.

### Package handler  
*Tool:* `/setup/handlerextension/packagehandler`

A kind of thing that can be put in a package and taken out again.

| | |
|---|---|
| Code | `extension/<name>/packagehandlers/<handler>/<handler>packagehandler.php` |
| Registered by | package.ini [PackageSettings] HandlerAlias[<alias>] |
| Contract | `extends eZPackageHandler` |
| Mechanism | handler |
| Kernel | `kernel/classes/ezpackagehandler.php` |

### Package creation handler  
*Tool:* `/setup/handlerextension/packagecreation`

The steps the admin interface walks through when a package is made.

| | |
|---|---|
| Code | `extension/<name>/packagehandlers/<handler>/<handler>creationhandler.php` |
| Registered by | package.ini [CreationSettings] HandlerAlias[<alias>] |
| Contract | `extends eZPackageCreationHandler` |
| Mechanism | handler |
| Kernel | `kernel/classes/ezpackagecreationhandler.php` |

### Package installation handler  
*Tool:* `/setup/handlerextension/packageinstall`

What happens when a package is installed or taken back out.

| | |
|---|---|
| Code | `extension/<name>/packagehandlers/<handler>/<handler>installhandler.php` |
| Registered by | package.ini [InstallerSettings] HandlerAlias[<alias>] |
| Contract | `extends eZPackageInstallationHandler` |
| Mechanism | handler |
| Kernel | `kernel/classes/ezpackageinstallationhandler.php` |

### VAT handler  
*Tool:* `/setup/handlerextension/vat`

What rate of tax applies to what, for whom.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>vathandler.php` |
| Registered by | shop.ini [VATSettings] Handler and RepositoryDirectories[] |
| Contract | `Implements the VAT handler interface` |
| Mechanism | directory |
| Kernel | `kernel/classes/ezvatmanager.php` |

### Shipping handler  
*Tool:* `/setup/handlerextension/shipping`

What delivery costs, and what the options are.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>shippinghandler.php` |
| Registered by | shop.ini [ShippingSettings] Handler and RepositoryDirectories[] |
| Contract | `Implements the shipping handler interface` |
| Mechanism | directory |
| Kernel | `kernel/classes/ezshippingmanager.php` |

### Basket info handler  
*Tool:* `/setup/handlerextension/basketinfo`

What a basket line says about itself: name, price, and what it is.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>basketinfohandler.php` |
| Registered by | shop.ini [BasketInfoSettings] Handler and RepositoryDirectories[] |
| Contract | `Implements the basket info handler interface` |
| Mechanism | directory |
| Kernel | `kernel/classes/basketinfohandlers/ezdefaultbasketinfohandler.php` |

### Exchange rate handler  
*Tool:* `/setup/handlerextension/exchangerate`

Where the rate between two currencies comes from.

| | |
|---|---|
| Code | `extension/<name>/classes/<name>exchangeratehandler.php` |
| Registered by | shop.ini [ExchangeRatesSettings] Handler and RepositoryDirectories[] |
| Contract | `Implements the exchange rate handler interface` |
| Mechanism | directory |
| Kernel | `kernel/shop/classes/exchangeratehandlers/ezexchangeratesupdatehandler.php` |

## Users, access and language

Who gets in, what they may do, and in what language.

### User login handler  
*Tool:* `/setup/handlerextension/login`

Where a user is checked against when they log in - a directory, another system, anything.

| | |
|---|---|
| Code | `extension/<name>/user/<handler>/<handler>user.php` |
| Registered by | site.ini [UserSettings] LoginHandler[] and ExtensionDirectory[] |
| Contract | `extends eZUser and implements loginUser()` |
| Mechanism | directory |
| Kernel | `kernel/classes/datatypes/ezuser` |

### Siteaccess settings extension  
*Tool:* `/setup/settingsextension`

Settings that apply to one siteaccess only, kept with the extension rather than in settings/.

| | |
|---|---|
| Code | `extension/<name>/settings/siteaccess/<siteaccess>/` |
| Registered by | site.ini [ExtensionSettings] ActiveAccessExtensions[] |
| Contract | `Settings only` |
| Mechanism | ini |
| Kernel | `lib/ezutils/classes/ezini.php` |

### Translation  
*Tool:* `/setup/contentextension`

The words the interface uses, in another language.

| | |
|---|---|
| Code | `extension/<name>/translations/<locale>/translation.ts` |
| Registered by | i18n.ini, and the locale being available |
| Contract | `A ts file of contexts and messages` |
| Mechanism | file |
| Kernel | `lib/ezi18n/classes/eztranslationcache.php` |

### RSS import handler  
*Tool:* `/rss/list`

What an imported feed item becomes once it has been fetched.

| | |
|---|---|
| Code | `extension/<name>/rss/ezrssimporthandler.php` |
| Registered by | site.ini [RSSSettings] ActiveExtensions[] |
| Contract | `Implements the RSS import handler interface` |
| Mechanism | directory |
| Kernel | `kernel/classes/ezrssimport.php` |

## Keeping it true

The catalogue is data, not prose: `expRADCatalogue::points()`. A point added
there appears on the RAD page and in this document without either being
edited. Regenerate this file with:

```sh
php ai/bin/one/write_rad_doc.php --allow-root-user
```
