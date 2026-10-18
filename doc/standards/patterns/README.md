---
title: Design pattern catalogue
audience: developer
category: standards
order: 300
summary: Every abstraction and design pattern of the Exponential 6 stack, found in the source code: 5060 instances in 29 categories across kernel, lib and extensions, with file, class, description and extension point.
keywords: design patterns, pattern catalogue, abstractions, architecture, factory, singleton, registry, strategy, handler, template method, observer, adapter, facade, active record, eZPersistentObject, datatype, template operator, fetch function, module view, policy function, workflow event, extension point, plugin, INI
type: reference
---
# Design pattern catalogue

This catalogue is for developers who extend Exponential or change its kernel. It lists every abstraction the code uses, where it is, and how a new implementation is plugged in, so that new code follows the patterns that are already there instead of inventing new ones. It is generated from the source code, so it is complete and current for the code it was built from; the rules that recognize each pattern are heuristic and are explained below.

Runnable examples of most of these extension points: [Examples](../../examples/README.md) and the [cookbook](../../examples/cookbook/README.md).

The rules the code follows: [Standards](../README.md) and [Kernel architecture](../architecture/kernel-architecture.md).

## In numbers

| | |
|---|---|
| Instances (all categories) | 5060 |
| Classes, interfaces, traits and enums | 2877 (2804 classes, 72 interfaces, 1 trait) |
| ... with at least one pattern | 2297 |
| Module views | 491 |
| Policy functions | 232 |
| Fetch functions | 290 |
| Template operators | 236 |
| Cronjob parts | 36 |
| Event hooks | 97 |
| INI plugin points | 574 |
| Source files read | 3747 PHP, 509 INI |
| Areas | 142 |

## The patterns

| Pattern | Group | Instances | Recognized by |
|---|---|---|---|
| [Active Record (eZPersistentObject)](active-record.md) | Data access | 194 | Extends eZPersistentObject, directly or through a parent class. |
| [Datatype (Strategy for content fields)](datatype.md) | Plugins | 54 | Extends eZDataType, directly or through a parent class. |
| [Workflow event type (Command, plugin)](workflow-event.md) | Plugins | 12 | Extends eZWorkflowEventType. |
| [Template operator (Interpreter plugin)](template-operator.md) | Plugins | 314 | Registered in an eztemplateautoload.php ($eZTemplateOperatorArray[]), or has operatorList() and modify(). |
| [Template function (Interpreter plugin)](template-function.md) | Plugins | 45 | Registered in an eztemplateautoload.php ($eZTemplateFunctionArray[]), or has functionList() and process(). |
| [Fetch function (Query Object, Facade)](fetch-function.md) | Plugins | 319 | An entry of a module's function_definition.php, or a class named *FunctionCollection. |
| [Module view (Page Controller)](module-view.md) | Request handling | 921 | An entry of $ViewList in a module.php, or a class that extends ModuleView or Page (the runnable form of a view). |
| [Policy function (access control)](policy-function.md) | Request handling | 232 | An entry of $FunctionList in a module.php. |
| [Front Controller](front-controller.md) | Request handling | 10 | The kernel classes ezpKernel, ezpKernelWeb, ezpKernelTreeMenu, ezpKernelRest, eZModule and their kind. |
| [Command (console, scripts, cronjobs)](command.md) | Request handling | 221 | Extends Command, CronjobPart or Runnable (the runnable console commands and cronjob parts), or a cronjobs/*.php file. |
| [Singleton / instance()](singleton.md) | Creation | 69 | Has a static instance() or getInstance() method. |
| [Factory and static factory methods](factory.md) | Creation | 352 | Class name ends in Factory, or has static create*(), from*(), factory(), make*() methods, or creates new self/new static in a static method. |
| [Builder](builder.md) | Creation | 9 | Class name contains Builder or Generator. |
| [Registry](registry.md) | Structure | 30 | Class name contains Registry, or has static register*() methods. |
| [Strategy / handler](strategy-handler.md) | Behavior | 299 | Class name ends in Handler, Backend, Driver, Transport, Engine or Strategy, or implements an interface whose name does. |
| [Template Method / abstract base class](template-method.md) | Behavior | 53 | An abstract class with both abstract and concrete methods. |
| [Interface (contract)](interface.md) | Structure | 72 | Every interface. |
| [Observer / listener / event](observer.md) | Behavior | 8 | Class name contains Listener, Observer or Subscriber, or has attach()/notify()/addListener() methods; every ezpEvent notify()/filter() call (see the hooks page). |
| [Event hook (ezpEvent)](event-hook.md) | Behavior | 97 | A call of ezpEvent::getInstance()-›notify( 'EVENT' ) or -›filter( 'EVENT' ). |
| [Adapter / bridge / wrapper](adapter.md) | Structure | 4 | Class name contains Adapter, Bridge, Wrapper or Proxy. |
| [Facade / service](facade.md) | Structure | 37 | Class name ends in Facade, Service, Manager, API or Api. |
| [Decorator / filter](decorator.md) | Structure | 62 | Class name contains Decorator or Filter. |
| [Chain of Responsibility](chain.md) | Behavior | 7 | Class name contains LoginHandler, Chain, Pipeline, Middleware, Router or Matcher. |
| [Iterator](iterator.md) | Behavior | 5 | Implements Iterator, IteratorAggregate or ArrayAccess. |
| [Value object / struct](value-object.md) | Structure | 95 | Class name ends in Struct, Value, Result, Item, Info or Definition and the class has few methods, or extends ezcBaseStruct. |
| [Exception hierarchy](exception.md) | Structure | 51 | Extends Exception, an *Exception class or Error. |
| [Trait (mixin)](trait.md) | Structure | 1 | Every trait. |
| [Utility (static helper)](utility.md) | Structure | 540 | A concrete class whose methods are all static (at least two). |
| [INI plugin point (extension point)](ini-plugin-point.md) | Plugins | 947 | A setting of a settings/*.ini file (or an extension's) whose value is the name of a class of the catalogue. |

## The areas

One page per part of the code base: every class with its patterns, and the views, functions, operators and plugin points defined there.

| Area | Classes | Views | Fetch functions | Operators | Plugin points |
|---|---|---|---|---|---|
| [extension/AdminAid](area-extension-adminaid.md) | 1 | 6 | 0 | 0 | 18 |
| [extension/autorss](area-extension-autorss.md) | 2 | 0 | 0 | 0 | 0 |
| [extension/bccie](area-extension-bccie.md) | 46 | 5 | 0 | 0 | 42 |
| [extension/bcgooglesitemaps](area-extension-bcgooglesitemaps.md) | 4 | 1 | 0 | 0 | 0 |
| [extension/bcwebsitestatistics](area-extension-bcwebsitestatistics.md) | 4 | 0 | 0 | 6 | 0 |
| [extension/birthday](area-extension-birthday.md) | 4 | 0 | 0 | 0 | 1 |
| [extension/cjw_newsletter](area-extension-cjw-newsletter.md) | 197 | 57 | 16 | 13 | 20 |
| [extension/cjw_themes_jumper](area-extension-cjw-themes-jumper.md) | 6 | 2 | 0 | 0 | 2 |
| [extension/cjw_tmv](area-extension-cjw-tmv.md) | 7 | 0 | 4 | 0 | 1 |
| [extension/enhancedezbinaryfile](area-extension-enhancedezbinaryfile.md) | 3 | 0 | 0 | 1 | 0 |
| [extension/enhancedselection2](area-extension-enhancedselection2.md) | 6 | 0 | 0 | 0 | 1 |
| [extension/expchangeclass](area-extension-expchangeclass.md) | 7 | 3 | 1 | 0 | 3 |
| [extension/explayouts](area-extension-explayouts.md) | 146 | 9 | 8 | 0 | 85 |
| [extension/explayouts_api](area-extension-explayouts-api.md) | 10 | 0 | 0 | 0 | 0 |
| [extension/explayouts_content_browser](area-extension-explayouts-content-browser.md) | 4 | 0 | 0 | 0 | 0 |
| [extension/explayouts_content_browser_core](area-extension-explayouts-content-browser-core.md) | 2 | 0 | 0 | 0 | 0 |
| [extension/explayouts_content_browser_ui](area-extension-explayouts-content-browser-ui.md) | 2 | 1 | 0 | 0 | 0 |
| [extension/explayouts_core](area-extension-explayouts-core.md) | 13 | 0 | 0 | 0 | 1 |
| [extension/explayouts_relation_list_query](area-extension-explayouts-relation-list-query.md) | 4 | 0 | 0 | 0 | 0 |
| [extension/explayouts_site_api](area-extension-explayouts-site-api.md) | 6 | 0 | 0 | 0 | 0 |
| [extension/explayouts_standard](area-extension-explayouts-standard.md) | 23 | 0 | 0 | 0 | 0 |
| [extension/explayouts_tags_query](area-extension-explayouts-tags-query.md) | 2 | 0 | 0 | 0 | 0 |
| [extension/explayouts_ui](area-extension-explayouts-ui.md) | 20 | 14 | 0 | 1 | 0 |
| [extension/explayouts_ui_api](area-extension-explayouts-ui-api.md) | 6 | 4 | 0 | 0 | 0 |
| [extension/expquery_translator](area-extension-expquery-translator.md) | 53 | 0 | 0 | 0 | 0 |
| [extension/expservices](area-extension-expservices.md) | 78 | 0 | 0 | 0 | 69 |
| [extension/expsite_api](area-extension-expsite-api.md) | 7 | 0 | 0 | 0 | 0 |
| [extension/expsite_app](area-extension-expsite-app.md) | 2 | 0 | 0 | 0 | 0 |
| [extension/expsite_core](area-extension-expsite-core.md) | 4 | 0 | 0 | 0 | 0 |
| [extension/expsite_data_media](area-extension-expsite-data-media.md) | 2 | 0 | 0 | 0 | 0 |
| [extension/expsite_installer](area-extension-expsite-installer.md) | 2 | 0 | 0 | 0 | 0 |
| [extension/expui](area-extension-expui.md) | 4 | 0 | 0 | 1 | 1 |
| [extension/exp_adminui](area-extension-exp-adminui.md) | 1 | 0 | 0 | 0 | 0 |
| [extension/exp_enhanced_link](area-extension-exp-enhanced-link.md) | 3 | 0 | 0 | 0 | 0 |
| [extension/exp_oe_tiptap](area-extension-exp-oe-tiptap.md) | 14 | 1 | 0 | 0 | 3 |
| [extension/ezauthorize](area-extension-ezauthorize.md) | 5 | 2 | 0 | 0 | 0 |
| [extension/ezautosave](area-extension-ezautosave.md) | 1 | 0 | 0 | 0 | 1 |
| [extension/ezdemo](area-extension-ezdemo.md) | 5 | 0 | 0 | 6 | 0 |
| [extension/ezfind](area-extension-ezfind.md) | 51 | 3 | 7 | 2 | 8 |
| [extension/ezflow](area-extension-ezflow.md) | 28 | 8 | 6 | 5 | 6 |
| [extension/ezformtoken](area-extension-ezformtoken.md) | 1 | 0 | 0 | 0 | 0 |
| [extension/ezgmaplocation](area-extension-ezgmaplocation.md) | 3 | 0 | 0 | 0 | 1 |
| [extension/ezie](area-extension-ezie.md) | 32 | 13 | 0 | 0 | 0 |
| [extension/ezjscore](area-extension-ezjscore.md) | 21 | 3 | 0 | 12 | 11 |
| [extension/ezmbpaex](area-extension-ezmbpaex.md) | 4 | 2 | 0 | 0 | 0 |
| [extension/ezmultiupload](area-extension-ezmultiupload.md) | 8 | 2 | 3 | 0 | 0 |
| [extension/ezodf](area-extension-ezodf.md) | 8 | 2 | 0 | 0 | 1 |
| [extension/ezoe](area-extension-ezoe.md) | 26 | 8 | 0 | 1 | 7 |
| [extension/ezownerchange](area-extension-ezownerchange.md) | 5 | 1 | 0 | 0 | 1 |
| [extension/ezpaypal](area-extension-ezpaypal.md) | 4 | 1 | 0 | 0 | 0 |
| [extension/ezpm](area-extension-ezpm.md) | 5 | 12 | 4 | 0 | 0 |
| [extension/ezpostgresqlcluster](area-extension-ezpostgresqlcluster.md) | 2 | 0 | 0 | 0 | 0 |
| [extension/ezprestapi](area-extension-ezprestapi.md) | 9 | 0 | 0 | 0 | 2 |
| [extension/ezprestapiprovider](area-extension-ezprestapiprovider.md) | 2 | 0 | 0 | 0 | 2 |
| [extension/ezssp](area-extension-ezssp.md) | 2 | 0 | 0 | 0 | 0 |
| [extension/ezstarrating](area-extension-ezstarrating.md) | 7 | 0 | 0 | 3 | 2 |
| [extension/eztags](area-extension-eztags.md) | 32 | 17 | 9 | 5 | 7 |
| [extension/ezupdate](area-extension-ezupdate.md) | 17 | 0 | 0 | 0 | 0 |
| [extension/ezurlmultisite](area-extension-ezurlmultisite.md) | 8 | 0 | 0 | 13 | 4 |
| [extension/ezwebin](area-extension-ezwebin.md) | 1 | 0 | 0 | 6 | 0 |
| [extension/ezwt](area-extension-ezwt.md) | 4 | 1 | 0 | 1 | 1 |
| [extension/git_manager](area-extension-git-manager.md) | 23 | 0 | 0 | 0 | 1 |
| [extension/hcaptcha](area-extension-hcaptcha.md) | 5 | 0 | 0 | 0 | 0 |
| [extension/ngclasslist](area-extension-ngclasslist.md) | 2 | 0 | 0 | 0 | 0 |
| [extension/nxc_powercontent](area-extension-nxc-powercontent.md) | 6 | 40 | 56 | 0 | 0 |
| [extension/owsimpleoperator](area-extension-owsimpleoperator.md) | 5 | 0 | 0 | 0 | 0 |
| [extension/powercontent](area-extension-powercontent.md) | 3 | 1 | 0 | 0 | 0 |
| [extension/recaptcha](area-extension-recaptcha.md) | 4 | 0 | 0 | 0 | 0 |
| [extension/sevenx_authentication_2fa](area-extension-sevenx-authentication-2fa.md) | 19 | 4 | 0 | 0 | 0 |
| [extension/sevenx_dse](area-extension-sevenx-dse.md) | 53 | 0 | 0 | 0 | 0 |
| [extension/sevenx_themes_media](area-extension-sevenx-themes-media.md) | 7 | 2 | 0 | 0 | 3 |
| [extension/sevenx_themes_simple](area-extension-sevenx-themes-simple.md) | 1 | 0 | 0 | 0 | 16 |
| [extension/sevenx_themes_super](area-extension-sevenx-themes-super.md) | 1 | 0 | 0 | 0 | 0 |
| [extension/str_replace](area-extension-str-replace.md) | 2 | 0 | 0 | 1 | 0 |
| [extension/swark](area-extension-swark.md) | 43 | 0 | 0 | 0 | 0 |
| [extension/syndication](area-extension-syndication.md) | 34 | 13 | 0 | 0 | 0 |
| [extension/xrowextract](area-extension-xrowextract.md) | 91 | 9 | 2 | 0 | 33 |
| [extension/xrowmetadata](area-extension-xrowmetadata.md) | 38 | 2 | 0 | 7 | 1 |
| [kernel/classes](area-kernel-classes.md) | 202 | 0 | 0 | 0 | 0 |
| [kernel/classes/apikey](area-kernel-classes-apikey.md) | 3 | 0 | 0 | 0 | 0 |
| [kernel/classes/audit](area-kernel-classes-audit.md) | 54 | 0 | 0 | 0 | 0 |
| [kernel/classes/basketinfohandlers](area-kernel-classes-basketinfohandlers.md) | 1 | 0 | 0 | 0 | 0 |
| [kernel/classes/binaryhandlers](area-kernel-classes-binaryhandlers.md) | 1 | 0 | 0 | 0 | 0 |
| [kernel/classes/classicmenu](area-kernel-classes-classicmenu.md) | 2 | 0 | 0 | 0 | 0 |
| [kernel/classes/clusterfilehandlers](area-kernel-classes-clusterfilehandlers.md) | 2 | 0 | 0 | 0 | 0 |
| [kernel/classes/collaborationhandlers](area-kernel-classes-collaborationhandlers.md) | 1 | 0 | 0 | 0 | 0 |
| [kernel/classes/confirmorderhandlers](area-kernel-classes-confirmorderhandlers.md) | 1 | 0 | 0 | 0 | 0 |
| [kernel/classes/contentcheck](area-kernel-classes-contentcheck.md) | 13 | 0 | 0 | 0 | 0 |
| [kernel/classes/contentfinder](area-kernel-classes-contentfinder.md) | 3 | 0 | 0 | 0 | 0 |
| [kernel/classes/contentjob](area-kernel-classes-contentjob.md) | 18 | 0 | 0 | 0 | 0 |
| [kernel/classes/datatypes](area-kernel-classes-datatypes.md) | 81 | 0 | 0 | 0 | 0 |
| [kernel/classes/debugbar](area-kernel-classes-debugbar.md) | 8 | 0 | 0 | 0 | 0 |
| [kernel/classes/help](area-kernel-classes-help.md) | 31 | 0 | 0 | 0 | 0 |
| [kernel/classes/i18n](area-kernel-classes-i18n.md) | 1 | 0 | 0 | 0 | 0 |
| [kernel/classes/ini](area-kernel-classes-ini.md) | 32 | 0 | 0 | 0 | 0 |
| [kernel/classes/logviewer](area-kernel-classes-logviewer.md) | 19 | 0 | 0 | 0 | 0 |
| [kernel/classes/mail](area-kernel-classes-mail.md) | 1 | 0 | 0 | 0 | 0 |
| [kernel/classes/mailpreferences](area-kernel-classes-mailpreferences.md) | 18 | 0 | 0 | 0 | 0 |
| [kernel/classes/notification](area-kernel-classes-notification.md) | 19 | 0 | 0 | 0 | 0 |
| [kernel/classes/packagecreators](area-kernel-classes-packagecreators.md) | 4 | 0 | 0 | 0 | 0 |
| [kernel/classes/packagehandlers](area-kernel-classes-packagehandlers.md) | 7 | 0 | 0 | 0 | 0 |
| [kernel/classes/packageinstallers](area-kernel-classes-packageinstallers.md) | 2 | 0 | 0 | 0 | 0 |
| [kernel/classes/pdfexport](area-kernel-classes-pdfexport.md) | 4 | 0 | 0 | 0 | 0 |
| [kernel/classes/requestclassifier](area-kernel-classes-requestclassifier.md) | 12 | 0 | 0 | 0 | 0 |
| [kernel/classes/requestlog](area-kernel-classes-requestlog.md) | 1 | 0 | 0 | 0 | 0 |
| [kernel/classes/role](area-kernel-classes-role.md) | 3 | 0 | 0 | 0 | 0 |
| [kernel/classes/shopaccounthandlers](area-kernel-classes-shopaccounthandlers.md) | 4 | 0 | 0 | 0 | 0 |
| [kernel/classes/subitems](area-kernel-classes-subitems.md) | 24 | 0 | 0 | 0 | 0 |
| [kernel/classes/uiswitch](area-kernel-classes-uiswitch.md) | 6 | 0 | 0 | 0 | 0 |
| [kernel/classes/user](area-kernel-classes-user.md) | 7 | 0 | 0 | 0 | 0 |
| [kernel/classes/vathandlers](area-kernel-classes-vathandlers.md) | 1 | 0 | 0 | 0 | 0 |
| [kernel/classes/workflowtypes](area-kernel-classes-workflowtypes.md) | 9 | 0 | 0 | 0 | 0 |
| [kernel/common](area-kernel-common.md) | 23 | 0 | 0 | 42 | 0 |
| [kernel/modules](area-kernel-modules.md) | 76 | 234 | 173 | 0 | 0 |
| [kernel/private](area-kernel-private.md) | 177 | 8 | 1 | 1 | 0 |
| [kernel/private/classes/asynchronouspublisheroutput](area-kernel-private-classes-asynchronouspublisheroutput.md) | 2 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/clusterfilehandlers](area-kernel-private-classes-clusterfilehandlers.md) | 14 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/commands](area-kernel-private-classes-commands.md) | 97 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/cronjobs](area-kernel-private-classes-cronjobs.md) | 27 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/exceptions](area-kernel-private-classes-exceptions.md) | 14 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/httpcache](area-kernel-private-classes-httpcache.md) | 2 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/requestrules](area-kernel-private-classes-requestrules.md) | 44 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/runnable](area-kernel-private-classes-runnable.md) | 4 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/services](area-kernel-private-classes-services.md) | 8 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/urlaliasfilters](area-kernel-private-classes-urlaliasfilters.md) | 1 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/views](area-kernel-private-classes-views.md) | 255 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/webdav](area-kernel-private-classes-webdav.md) | 2 | 0 | 0 | 0 | 0 |
| [lib](area-lib.md) | 2 | 0 | 0 | 0 | 0 |
| [lib/ezdb](area-lib-ezdb.md) | 10 | 0 | 0 | 0 | 0 |
| [lib/ezdbschema](area-lib-ezdbschema.md) | 8 | 0 | 0 | 0 | 0 |
| [lib/ezdiff](area-lib-ezdiff.md) | 10 | 0 | 0 | 0 | 0 |
| [lib/ezfile](area-lib-ezfile.md) | 13 | 0 | 0 | 0 | 0 |
| [lib/ezi18n](area-lib-ezi18n.md) | 22 | 0 | 0 | 0 | 0 |
| [lib/ezimage](area-lib-ezimage.md) | 16 | 0 | 0 | 0 | 0 |
| [lib/ezlocale](area-lib-ezlocale.md) | 6 | 0 | 0 | 0 | 0 |
| [lib/ezmath](area-lib-ezmath.md) | 2 | 0 | 0 | 0 | 0 |
| [lib/ezpdf](area-lib-ezpdf.md) | 5 | 0 | 0 | 1 | 0 |
| [lib/ezsession](area-lib-ezsession.md) | 6 | 0 | 0 | 0 | 0 |
| [lib/ezsoap](area-lib-ezsoap.md) | 10 | 0 | 0 | 0 | 0 |
| [lib/eztemplate](area-lib-eztemplate.md) | 56 | 0 | 0 | 108 | 0 |
| [lib/ezutils](area-lib-ezutils.md) | 47 | 0 | 0 | 0 | 0 |
| [settings](area-settings.md) | 0 | 0 | 0 | 0 | 219 |

## What was read

The git-tracked PHP and INI files below `kernel/`, `lib/` and `extension/` of the Exponential repository (tests and `vendor/` directories left out), and these extensions:

| Extension | Source |
|---|---|
| `AdminAid` | its own repository |
| `autorss` | Composer package |
| `bccie` | Composer package |
| `bcgooglesitemaps` | Composer package |
| `bcwebsitestatistics` | Composer package |
| `birthday` | Composer package |
| `cjw_newsletter` | Composer package |
| `cjw_themes_jumper` | its own repository |
| `cjw_tmv` | its own repository |
| `enhancedezbinaryfile` | Composer package |
| `enhancedselection2` | Composer package |
| `exp_adminui` | its own repository |
| `exp_enhanced_link` | Composer package |
| `exp_oe_tiptap` | its own repository |
| `expchangeclass` | its own repository |
| `explayouts` | its own repository |
| `explayouts_api` | its own repository |
| `explayouts_content_browser` | its own repository |
| `explayouts_content_browser_core` | its own repository |
| `explayouts_content_browser_ui` | its own repository |
| `explayouts_core` | its own repository |
| `explayouts_relation_list_query` | its own repository |
| `explayouts_site_api` | its own repository |
| `explayouts_standard` | its own repository |
| `explayouts_tags_query` | its own repository |
| `explayouts_ui` | its own repository |
| `explayouts_ui_api` | its own repository |
| `expquery_translator` | its own repository |
| `expservices` | shipped with the kernel |
| `expsite_api` | its own repository |
| `expsite_app` | its own repository |
| `expsite_core` | its own repository |
| `expsite_data_media` | its own repository |
| `expsite_installer` | its own repository |
| `expui` | its own repository |
| `ezauthorize` | Composer package |
| `ezautosave` | Composer package |
| `ezdemo` | Composer package |
| `ezfind` | its own repository |
| `ezflow` | Composer package |
| `ezformtoken` | shipped with the kernel |
| `ezgmaplocation` | Composer package |
| `ezie` | Composer package |
| `ezjscore` | shipped with the kernel |
| `ezmbpaex` | Composer package |
| `ezmultiupload` | Composer package |
| `ezodf` | Composer package |
| `ezoe` | shipped with the kernel |
| `ezownerchange` | its own repository |
| `ezpaypal` | Composer package |
| `ezpm` | Composer package |
| `ezpostgresqlcluster` | Composer package |
| `ezprestapi` | Composer package |
| `ezprestapiprovider` | Composer package |
| `ezssp` | Composer package |
| `ezstarrating` | Composer package |
| `eztags` | Composer package |
| `ezupdate` | Composer package |
| `ezurlmultisite` | its own repository |
| `ezwebin` | Composer package |
| `ezwt` | Composer package |
| `git_manager` | Composer package |
| `hcaptcha` | Composer package |
| `ngclasslist` | Composer package |
| `nxc_powercontent` | Composer package |
| `owsimpleoperator` | Composer package |
| `powercontent` | its own repository |
| `recaptcha` | Composer package |
| `sevenx_authentication_2fa` | its own repository |
| `sevenx_dse` | its own repository |
| `sevenx_themes_media` | its own repository |
| `sevenx_themes_simple` | Composer package |
| `sevenx_themes_super` | Composer package |
| `str_replace` | Composer package |
| `swark` | Composer package |
| `syndication` | Composer package |
| `xrowextract` | Composer package |
| `xrowmetadata` | Composer package |

An extension is read when it is a git repository of its own below `extension/` whose origin is a se7enxweb repository, or a Composer package of se7enxweb installed there; private extensions and site settings are never read. A site that has other extensions installed gets a larger catalogue when it runs the generator.

## How the catalogue is built

Run in the installation root (no database, nothing is loaded or executed; PHP's tokenizer reads the files):

```bash
php bin/php/exppatterncatalogue.php            # report which pages would change
php bin/php/exppatterncatalogue.php --write    # write doc/standards/patterns/
php bin/php/exppatterncatalogue.php --check    # exit 1 when the pages are out of date
```

The output depends only on the source: no dates, no commit ids, everything sorted, so a second run on the same code writes the same bytes. Commit the pages with the code change that changes them.

Each category has a rule (the table above and each pattern's page). A class can match several categories. The rules use the class's name, its parent classes and interfaces (followed through the whole code base), its methods (static, abstract) and the registration files of the kernel: `module.php` (views and policy functions), `function_definition.php` (fetch functions), `eztemplateautoload.php` (template operators and functions), `cronjobs/` directories, `ezpEvent` calls and INI settings whose value is a class name. Names are a heuristic: a class called `...Manager` is listed as a facade or service even when it is something else, and a pattern implemented without the usual names is missed. Descriptions are the first sentence of each class's doc comment.

> **Note**
> The catalogue describes the code as it is, including inherited code. It is not a list of recommendations: for new code, follow [the layering and naming rules](../architecture/layering-and-naming.md).
