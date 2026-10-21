---
title: Katalog der Entwurfsmuster
audience: developer
category: standards
order: 300
summary: Jede Abstraktion und jedes Entwurfsmuster des Exponential-6-Stacks, im Quellcode gefunden: 5067 Vorkommen in 29 Kategorien über Kernel, lib und Extensions, mit Datei, Klasse, Beschreibung und Erweiterungspunkt.
keywords: Entwurfsmuster, Musterkatalog, Abstraktionen, Architektur, Fabrik, Erweiterungspunkt, Datentyp, Template-Operator, Fetch-Funktion, Modulansicht, Richtlinienfunktion, Workflow-Ereignis, design patterns, pattern catalogue, abstractions, architecture, factory, singleton, registry, strategy, handler, template method, observer, adapter, facade, active record, eZPersistentObject, datatype, template operator, fetch function, module view, policy function, workflow event, extension point, plugin, INI
type: reference
---
# Katalog der Entwurfsmuster

Dieser Katalog richtet sich an Entwickler, die Exponential erweitern oder seinen Kernel ändern. Er listet jede Abstraktion auf, die der Code verwendet, wo sie liegt und wie eine neue Implementierung eingehängt wird, damit neuer Code den bereits vorhandenen Mustern folgt, statt neue zu erfinden. Er wird aus dem Quellcode generiert und ist daher für den Code, aus dem er gebaut wurde, vollständig und aktuell; die Regeln, die jedes Muster erkennen, sind heuristisch und werden weiter unten erklärt.

Lauffähige Beispiele für die meisten dieser Erweiterungspunkte: [Beispiele](../../../eng-us/examples/README.md) und das [Kochbuch](../../../eng-us/examples/cookbook/README.md).

Die Regeln, denen der Code folgt: [Standards](../README.md) und [Architektur des Kernels](../architektur/kernel-architektur.md).

## In Zahlen

| | |
|---|---|
| Vorkommen (alle Kategorien) | 5067 |
| Klassen, Interfaces, Traits und Enums | 2877 (2804 Klassen, 72 Interfaces, 1 Trait) |
| ... mit mindestens einem Muster | 2297 |
| Modulansichten | 491 |
| Richtlinienfunktionen | 232 |
| Fetch-Funktionen | 290 |
| Template-Operatoren | 243 |
| Cronjob-Teile | 36 |
| Event-Hooks | 97 |
| INI-Plugin-Punkte | 574 |
| Gelesene Quelldateien | 3747 PHP, 509 INI |
| Bereiche | 142 |

## Die Muster

| Muster | Gruppe | Vorkommen | Erkannt an |
|---|---|---|---|
| [Active Record (eZPersistentObject)](active-record.md) | Datenzugriff | 194 | Erweitert eZPersistentObject, direkt oder über eine Elternklasse. |
| [Datentyp (Strategy für Inhaltsfelder)](datentyp.md) | Plugins | 54 | Erweitert eZDataType, direkt oder über eine Elternklasse. |
| [Workflow-Ereignistyp (Command, Plugin)](workflow-ereignis.md) | Plugins | 12 | Erweitert eZWorkflowEventType. |
| [Template-Operator (Interpreter-Plugin)](template-operator.md) | Plugins | 321 | Registriert in einer eztemplateautoload.php ($eZTemplateOperatorArray[]) oder hat operatorList() und modify(). |
| [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | Plugins | 45 | Registriert in einer eztemplateautoload.php ($eZTemplateFunctionArray[]) oder hat functionList() und process(). |
| [Fetch-Funktion (Query Object, Facade)](fetch-funktion.md) | Plugins | 319 | Ein Eintrag in der function_definition.php eines Moduls oder eine Klasse namens *FunctionCollection. |
| [Modulansicht (Page Controller)](modulansicht.md) | Anfrageverarbeitung | 921 | Ein Eintrag von $ViewList in einer module.php oder eine Klasse, die ModuleView oder Page erweitert (die ausführbare Form einer Ansicht). |
| [Richtlinienfunktion (Zugriffskontrolle)](richtlinienfunktion.md) | Anfrageverarbeitung | 232 | Ein Eintrag von $FunctionList in einer module.php. |
| [Front Controller](front-controller.md) | Anfrageverarbeitung | 10 | Die Kernel-Klassen ezpKernel, ezpKernelWeb, ezpKernelTreeMenu, ezpKernelRest, eZModule und verwandte. |
| [Command (Konsole, Skripte, Cronjobs)](befehl.md) | Anfrageverarbeitung | 221 | Erweitert Command, CronjobPart oder Runnable (die ausführbaren Konsolenbefehle und Cronjob-Teile) oder eine Datei cronjobs/*.php. |
| [Singleton / instance()](singleton.md) | Erzeugung | 69 | Hat eine statische Methode instance() oder getInstance(). |
| [Factory und statische Fabrikmethoden](fabrik.md) | Erzeugung | 352 | Klassenname endet auf Factory oder hat statische Methoden create*(), from*(), factory(), make*() oder erzeugt new self/new static in einer statischen Methode. |
| [Builder](builder.md) | Erzeugung | 9 | Klassenname enthält Builder oder Generator. |
| [Registry](registry.md) | Struktur | 30 | Klassenname enthält Registry oder hat statische Methoden register*(). |
| [Strategy / Handler](strategie-handler.md) | Verhalten | 299 | Klassenname endet auf Handler, Backend, Driver, Transport, Engine oder Strategy oder implementiert ein Interface, dessen Name so endet. |
| [Template Method / abstrakte Basisklasse](schablonenmethode.md) | Verhalten | 53 | Eine abstrakte Klasse mit abstrakten und konkreten Methoden. |
| [Interface (Vertrag)](schnittstelle.md) | Struktur | 72 | Jedes Interface. |
| [Observer / Listener / Ereignis](beobachter.md) | Verhalten | 8 | Klassenname enthält Listener, Observer oder Subscriber oder hat Methoden attach()/notify()/addListener(); jeder Aufruf von ezpEvent notify()/filter() (siehe die Seite zu den Hooks). |
| [Event-Hook (ezpEvent)](ereignis-hook.md) | Verhalten | 97 | Ein Aufruf von ezpEvent::getInstance()-›notify( 'EVENT' ) oder -›filter( 'EVENT' ). |
| [Adapter / Bridge / Wrapper](adapter.md) | Struktur | 4 | Klassenname enthält Adapter, Bridge, Wrapper oder Proxy. |
| [Facade / Service](fassade.md) | Struktur | 37 | Klassenname endet auf Facade, Service, Manager, API oder Api. |
| [Decorator / Filter](dekorierer.md) | Struktur | 62 | Klassenname enthält Decorator oder Filter. |
| [Chain of Responsibility](kette.md) | Verhalten | 7 | Klassenname enthält LoginHandler, Chain, Pipeline, Middleware, Router oder Matcher. |
| [Iterator](iterator.md) | Verhalten | 5 | Implementiert Iterator, IteratorAggregate oder ArrayAccess. |
| [Value Object / Struct](wertobjekt.md) | Struktur | 95 | Klassenname endet auf Struct, Value, Result, Item, Info oder Definition und die Klasse hat wenige Methoden, oder sie erweitert ezcBaseStruct. |
| [Exception-Hierarchie](ausnahme.md) | Struktur | 51 | Erweitert Exception, eine Klasse *Exception oder Error. |
| [Trait (Mixin)](trait.md) | Struktur | 1 | Jeder Trait. |
| [Utility (statischer Helfer)](hilfsklasse.md) | Struktur | 540 | Eine konkrete Klasse, deren Methoden alle statisch sind (mindestens zwei). |
| [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | Plugins | 947 | Eine Einstellung einer Datei settings/*.ini (oder der einer Extension), deren Wert der Name einer Klasse des Katalogs ist. |

## Die Bereiche

Eine Seite pro Teil der Codebasis: jede Klasse mit ihren Mustern und die dort definierten Ansichten, Funktionen, Operatoren und Plugin-Punkte.

| Bereich | Klassen | Ansichten | Fetch-Funktionen | Operatoren | Plugin-Punkte |
|---|---|---|---|---|---|
| [extension/AdminAid](bereich-extension-adminaid.md) | 1 | 6 | 0 | 0 | 18 |
| [extension/autorss](bereich-extension-autorss.md) | 2 | 0 | 0 | 0 | 0 |
| [extension/bccie](bereich-extension-bccie.md) | 46 | 5 | 0 | 0 | 42 |
| [extension/bcgooglesitemaps](bereich-extension-bcgooglesitemaps.md) | 4 | 1 | 0 | 0 | 0 |
| [extension/bcwebsitestatistics](bereich-extension-bcwebsitestatistics.md) | 4 | 0 | 0 | 6 | 0 |
| [extension/birthday](bereich-extension-birthday.md) | 4 | 0 | 0 | 0 | 1 |
| [extension/cjw_newsletter](bereich-extension-cjw-newsletter.md) | 197 | 57 | 16 | 13 | 20 |
| [extension/cjw_themes_jumper](bereich-extension-cjw-themes-jumper.md) | 6 | 2 | 0 | 0 | 2 |
| [extension/cjw_tmv](bereich-extension-cjw-tmv.md) | 7 | 0 | 4 | 0 | 1 |
| [extension/enhancedezbinaryfile](bereich-extension-enhancedezbinaryfile.md) | 3 | 0 | 0 | 1 | 0 |
| [extension/enhancedselection2](bereich-extension-enhancedselection2.md) | 6 | 0 | 0 | 0 | 1 |
| [extension/expchangeclass](bereich-extension-expchangeclass.md) | 7 | 3 | 1 | 0 | 3 |
| [extension/explayouts](bereich-extension-explayouts.md) | 146 | 9 | 8 | 0 | 85 |
| [extension/explayouts_api](bereich-extension-explayouts-api.md) | 10 | 0 | 0 | 0 | 0 |
| [extension/explayouts_content_browser](bereich-extension-explayouts-content-browser.md) | 4 | 0 | 0 | 0 | 0 |
| [extension/explayouts_content_browser_core](bereich-extension-explayouts-content-browser-core.md) | 2 | 0 | 0 | 0 | 0 |
| [extension/explayouts_content_browser_ui](bereich-extension-explayouts-content-browser-ui.md) | 2 | 1 | 0 | 0 | 0 |
| [extension/explayouts_core](bereich-extension-explayouts-core.md) | 13 | 0 | 0 | 0 | 1 |
| [extension/explayouts_relation_list_query](bereich-extension-explayouts-relation-list-query.md) | 4 | 0 | 0 | 0 | 0 |
| [extension/explayouts_site_api](bereich-extension-explayouts-site-api.md) | 6 | 0 | 0 | 0 | 0 |
| [extension/explayouts_standard](bereich-extension-explayouts-standard.md) | 23 | 0 | 0 | 0 | 0 |
| [extension/explayouts_tags_query](bereich-extension-explayouts-tags-query.md) | 2 | 0 | 0 | 0 | 0 |
| [extension/explayouts_ui](bereich-extension-explayouts-ui.md) | 20 | 14 | 0 | 1 | 0 |
| [extension/explayouts_ui_api](bereich-extension-explayouts-ui-api.md) | 6 | 4 | 0 | 0 | 0 |
| [extension/expquery_translator](bereich-extension-expquery-translator.md) | 53 | 0 | 0 | 0 | 0 |
| [extension/expservices](bereich-extension-expservices.md) | 78 | 0 | 0 | 0 | 69 |
| [extension/expsite_api](bereich-extension-expsite-api.md) | 7 | 0 | 0 | 0 | 0 |
| [extension/expsite_app](bereich-extension-expsite-app.md) | 2 | 0 | 0 | 0 | 0 |
| [extension/expsite_core](bereich-extension-expsite-core.md) | 4 | 0 | 0 | 0 | 0 |
| [extension/expsite_data_media](bereich-extension-expsite-data-media.md) | 2 | 0 | 0 | 0 | 0 |
| [extension/expsite_installer](bereich-extension-expsite-installer.md) | 2 | 0 | 0 | 0 | 0 |
| [extension/expui](bereich-extension-expui.md) | 4 | 0 | 0 | 1 | 1 |
| [extension/exp_adminui](bereich-extension-exp-adminui.md) | 1 | 0 | 0 | 0 | 0 |
| [extension/exp_enhanced_link](bereich-extension-exp-enhanced-link.md) | 3 | 0 | 0 | 0 | 0 |
| [extension/exp_oe_tiptap](bereich-extension-exp-oe-tiptap.md) | 14 | 1 | 0 | 0 | 3 |
| [extension/ezauthorize](bereich-extension-ezauthorize.md) | 5 | 2 | 0 | 0 | 0 |
| [extension/ezautosave](bereich-extension-ezautosave.md) | 1 | 0 | 0 | 0 | 1 |
| [extension/ezdemo](bereich-extension-ezdemo.md) | 5 | 0 | 0 | 6 | 0 |
| [extension/ezfind](bereich-extension-ezfind.md) | 51 | 3 | 7 | 2 | 8 |
| [extension/ezflow](bereich-extension-ezflow.md) | 28 | 8 | 6 | 5 | 6 |
| [extension/ezformtoken](bereich-extension-ezformtoken.md) | 1 | 0 | 0 | 0 | 0 |
| [extension/ezgmaplocation](bereich-extension-ezgmaplocation.md) | 3 | 0 | 0 | 0 | 1 |
| [extension/ezie](bereich-extension-ezie.md) | 32 | 13 | 0 | 0 | 0 |
| [extension/ezjscore](bereich-extension-ezjscore.md) | 21 | 3 | 0 | 12 | 11 |
| [extension/ezmbpaex](bereich-extension-ezmbpaex.md) | 4 | 2 | 0 | 0 | 0 |
| [extension/ezmultiupload](bereich-extension-ezmultiupload.md) | 8 | 2 | 3 | 0 | 0 |
| [extension/ezodf](bereich-extension-ezodf.md) | 8 | 2 | 0 | 0 | 1 |
| [extension/ezoe](bereich-extension-ezoe.md) | 26 | 8 | 0 | 1 | 7 |
| [extension/ezownerchange](bereich-extension-ezownerchange.md) | 5 | 1 | 0 | 0 | 1 |
| [extension/ezpaypal](bereich-extension-ezpaypal.md) | 4 | 1 | 0 | 0 | 0 |
| [extension/ezpm](bereich-extension-ezpm.md) | 5 | 12 | 4 | 0 | 0 |
| [extension/ezpostgresqlcluster](bereich-extension-ezpostgresqlcluster.md) | 2 | 0 | 0 | 0 | 0 |
| [extension/ezprestapi](bereich-extension-ezprestapi.md) | 9 | 0 | 0 | 0 | 2 |
| [extension/ezprestapiprovider](bereich-extension-ezprestapiprovider.md) | 2 | 0 | 0 | 0 | 2 |
| [extension/ezssp](bereich-extension-ezssp.md) | 2 | 0 | 0 | 0 | 0 |
| [extension/ezstarrating](bereich-extension-ezstarrating.md) | 7 | 0 | 0 | 3 | 2 |
| [extension/eztags](bereich-extension-eztags.md) | 32 | 17 | 9 | 5 | 7 |
| [extension/ezupdate](bereich-extension-ezupdate.md) | 17 | 0 | 0 | 0 | 0 |
| [extension/ezurlmultisite](bereich-extension-ezurlmultisite.md) | 8 | 0 | 0 | 13 | 4 |
| [extension/ezwebin](bereich-extension-ezwebin.md) | 1 | 0 | 0 | 6 | 0 |
| [extension/ezwt](bereich-extension-ezwt.md) | 4 | 1 | 0 | 1 | 1 |
| [extension/git_manager](bereich-extension-git-manager.md) | 23 | 0 | 0 | 0 | 1 |
| [extension/hcaptcha](bereich-extension-hcaptcha.md) | 5 | 0 | 0 | 0 | 0 |
| [extension/ngclasslist](bereich-extension-ngclasslist.md) | 2 | 0 | 0 | 0 | 0 |
| [extension/nxc_powercontent](bereich-extension-nxc-powercontent.md) | 6 | 40 | 56 | 0 | 0 |
| [extension/owsimpleoperator](bereich-extension-owsimpleoperator.md) | 5 | 0 | 0 | 0 | 0 |
| [extension/powercontent](bereich-extension-powercontent.md) | 3 | 1 | 0 | 0 | 0 |
| [extension/recaptcha](bereich-extension-recaptcha.md) | 4 | 0 | 0 | 0 | 0 |
| [extension/sevenx_authentication_2fa](bereich-extension-sevenx-authentication-2fa.md) | 19 | 4 | 0 | 0 | 0 |
| [extension/sevenx_dse](bereich-extension-sevenx-dse.md) | 53 | 0 | 0 | 0 | 0 |
| [extension/sevenx_themes_media](bereich-extension-sevenx-themes-media.md) | 7 | 2 | 0 | 0 | 3 |
| [extension/sevenx_themes_simple](bereich-extension-sevenx-themes-simple.md) | 1 | 0 | 0 | 0 | 16 |
| [extension/sevenx_themes_super](bereich-extension-sevenx-themes-super.md) | 1 | 0 | 0 | 0 | 0 |
| [extension/str_replace](bereich-extension-str-replace.md) | 2 | 0 | 0 | 1 | 0 |
| [extension/swark](bereich-extension-swark.md) | 43 | 0 | 0 | 0 | 0 |
| [extension/syndication](bereich-extension-syndication.md) | 34 | 13 | 0 | 0 | 0 |
| [extension/xrowextract](bereich-extension-xrowextract.md) | 91 | 9 | 2 | 0 | 33 |
| [extension/xrowmetadata](bereich-extension-xrowmetadata.md) | 38 | 2 | 0 | 7 | 1 |
| [kernel/classes](bereich-kernel-classes.md) | 202 | 0 | 0 | 0 | 0 |
| [kernel/classes/apikey](bereich-kernel-classes-apikey.md) | 3 | 0 | 0 | 0 | 0 |
| [kernel/classes/audit](bereich-kernel-classes-audit.md) | 54 | 0 | 0 | 0 | 0 |
| [kernel/classes/basketinfohandlers](bereich-kernel-classes-basketinfohandlers.md) | 1 | 0 | 0 | 0 | 0 |
| [kernel/classes/binaryhandlers](bereich-kernel-classes-binaryhandlers.md) | 1 | 0 | 0 | 0 | 0 |
| [kernel/classes/classicmenu](bereich-kernel-classes-classicmenu.md) | 2 | 0 | 0 | 0 | 0 |
| [kernel/classes/clusterfilehandlers](bereich-kernel-classes-clusterfilehandlers.md) | 2 | 0 | 0 | 0 | 0 |
| [kernel/classes/collaborationhandlers](bereich-kernel-classes-collaborationhandlers.md) | 1 | 0 | 0 | 0 | 0 |
| [kernel/classes/confirmorderhandlers](bereich-kernel-classes-confirmorderhandlers.md) | 1 | 0 | 0 | 0 | 0 |
| [kernel/classes/contentcheck](bereich-kernel-classes-contentcheck.md) | 13 | 0 | 0 | 0 | 0 |
| [kernel/classes/contentfinder](bereich-kernel-classes-contentfinder.md) | 3 | 0 | 0 | 0 | 0 |
| [kernel/classes/contentjob](bereich-kernel-classes-contentjob.md) | 18 | 0 | 0 | 0 | 0 |
| [kernel/classes/datatypes](bereich-kernel-classes-datatypes.md) | 81 | 0 | 0 | 0 | 0 |
| [kernel/classes/debugbar](bereich-kernel-classes-debugbar.md) | 8 | 0 | 0 | 0 | 0 |
| [kernel/classes/help](bereich-kernel-classes-help.md) | 31 | 0 | 0 | 0 | 0 |
| [kernel/classes/i18n](bereich-kernel-classes-i18n.md) | 1 | 0 | 0 | 0 | 0 |
| [kernel/classes/ini](bereich-kernel-classes-ini.md) | 32 | 0 | 0 | 0 | 0 |
| [kernel/classes/logviewer](bereich-kernel-classes-logviewer.md) | 19 | 0 | 0 | 0 | 0 |
| [kernel/classes/mail](bereich-kernel-classes-mail.md) | 1 | 0 | 0 | 0 | 0 |
| [kernel/classes/mailpreferences](bereich-kernel-classes-mailpreferences.md) | 18 | 0 | 0 | 0 | 0 |
| [kernel/classes/notification](bereich-kernel-classes-notification.md) | 19 | 0 | 0 | 0 | 0 |
| [kernel/classes/packagecreators](bereich-kernel-classes-packagecreators.md) | 4 | 0 | 0 | 0 | 0 |
| [kernel/classes/packagehandlers](bereich-kernel-classes-packagehandlers.md) | 7 | 0 | 0 | 0 | 0 |
| [kernel/classes/packageinstallers](bereich-kernel-classes-packageinstallers.md) | 2 | 0 | 0 | 0 | 0 |
| [kernel/classes/pdfexport](bereich-kernel-classes-pdfexport.md) | 4 | 0 | 0 | 0 | 0 |
| [kernel/classes/requestclassifier](bereich-kernel-classes-requestclassifier.md) | 12 | 0 | 0 | 0 | 0 |
| [kernel/classes/requestlog](bereich-kernel-classes-requestlog.md) | 1 | 0 | 0 | 0 | 0 |
| [kernel/classes/role](bereich-kernel-classes-role.md) | 3 | 0 | 0 | 0 | 0 |
| [kernel/classes/shopaccounthandlers](bereich-kernel-classes-shopaccounthandlers.md) | 4 | 0 | 0 | 0 | 0 |
| [kernel/classes/subitems](bereich-kernel-classes-subitems.md) | 24 | 0 | 0 | 0 | 0 |
| [kernel/classes/uiswitch](bereich-kernel-classes-uiswitch.md) | 6 | 0 | 0 | 0 | 0 |
| [kernel/classes/user](bereich-kernel-classes-user.md) | 7 | 0 | 0 | 0 | 0 |
| [kernel/classes/vathandlers](bereich-kernel-classes-vathandlers.md) | 1 | 0 | 0 | 0 | 0 |
| [kernel/classes/workflowtypes](bereich-kernel-classes-workflowtypes.md) | 9 | 0 | 0 | 0 | 0 |
| [kernel/common](bereich-kernel-common.md) | 23 | 0 | 0 | 42 | 0 |
| [kernel/modules](bereich-kernel-modules.md) | 76 | 234 | 173 | 0 | 0 |
| [kernel/private](bereich-kernel-private.md) | 177 | 8 | 1 | 1 | 0 |
| [kernel/private/classes/asynchronouspublisheroutput](bereich-kernel-private-classes-asynchronouspublisheroutput.md) | 2 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/clusterfilehandlers](bereich-kernel-private-classes-clusterfilehandlers.md) | 14 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/commands](bereich-kernel-private-classes-commands.md) | 97 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/cronjobs](bereich-kernel-private-classes-cronjobs.md) | 27 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/exceptions](bereich-kernel-private-classes-exceptions.md) | 14 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/httpcache](bereich-kernel-private-classes-httpcache.md) | 2 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/requestrules](bereich-kernel-private-classes-requestrules.md) | 44 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/runnable](bereich-kernel-private-classes-runnable.md) | 4 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/services](bereich-kernel-private-classes-services.md) | 8 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/urlaliasfilters](bereich-kernel-private-classes-urlaliasfilters.md) | 1 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/views](bereich-kernel-private-classes-views.md) | 255 | 0 | 0 | 0 | 0 |
| [kernel/private/classes/webdav](bereich-kernel-private-classes-webdav.md) | 2 | 0 | 0 | 0 | 0 |
| [lib](bereich-lib.md) | 2 | 0 | 0 | 0 | 0 |
| [lib/ezdb](bereich-lib-ezdb.md) | 10 | 0 | 0 | 0 | 0 |
| [lib/ezdbschema](bereich-lib-ezdbschema.md) | 8 | 0 | 0 | 0 | 0 |
| [lib/ezdiff](bereich-lib-ezdiff.md) | 10 | 0 | 0 | 0 | 0 |
| [lib/ezfile](bereich-lib-ezfile.md) | 13 | 0 | 0 | 0 | 0 |
| [lib/ezi18n](bereich-lib-ezi18n.md) | 22 | 0 | 0 | 0 | 0 |
| [lib/ezimage](bereich-lib-ezimage.md) | 16 | 0 | 0 | 0 | 0 |
| [lib/ezlocale](bereich-lib-ezlocale.md) | 6 | 0 | 0 | 0 | 0 |
| [lib/ezmath](bereich-lib-ezmath.md) | 2 | 0 | 0 | 0 | 0 |
| [lib/ezpdf](bereich-lib-ezpdf.md) | 5 | 0 | 0 | 1 | 0 |
| [lib/ezsession](bereich-lib-ezsession.md) | 6 | 0 | 0 | 0 | 0 |
| [lib/ezsoap](bereich-lib-ezsoap.md) | 10 | 0 | 0 | 0 | 0 |
| [lib/eztemplate](bereich-lib-eztemplate.md) | 56 | 0 | 0 | 115 | 0 |
| [lib/ezutils](bereich-lib-ezutils.md) | 47 | 0 | 0 | 0 | 0 |
| [settings](bereich-settings.md) | 0 | 0 | 0 | 0 | 219 |

## Was gelesen wurde

Die von Git verfolgten PHP- und INI-Dateien unterhalb von `kernel/`, `lib/` und `extension/` des Exponential-Repositorys (ohne Tests und `vendor/`-Verzeichnisse) sowie diese Extensions:

| Extension | Quelle |
|---|---|
| `AdminAid` | eigenes Repository |
| `autorss` | Composer-Paket |
| `bccie` | Composer-Paket |
| `bcgooglesitemaps` | Composer-Paket |
| `bcwebsitestatistics` | Composer-Paket |
| `birthday` | Composer-Paket |
| `cjw_newsletter` | Composer-Paket |
| `cjw_themes_jumper` | eigenes Repository |
| `cjw_tmv` | eigenes Repository |
| `enhancedezbinaryfile` | Composer-Paket |
| `enhancedselection2` | Composer-Paket |
| `exp_adminui` | eigenes Repository |
| `exp_enhanced_link` | Composer-Paket |
| `exp_oe_tiptap` | eigenes Repository |
| `expchangeclass` | eigenes Repository |
| `explayouts` | eigenes Repository |
| `explayouts_api` | eigenes Repository |
| `explayouts_content_browser` | eigenes Repository |
| `explayouts_content_browser_core` | eigenes Repository |
| `explayouts_content_browser_ui` | eigenes Repository |
| `explayouts_core` | eigenes Repository |
| `explayouts_relation_list_query` | eigenes Repository |
| `explayouts_site_api` | eigenes Repository |
| `explayouts_standard` | eigenes Repository |
| `explayouts_tags_query` | eigenes Repository |
| `explayouts_ui` | eigenes Repository |
| `explayouts_ui_api` | eigenes Repository |
| `expquery_translator` | eigenes Repository |
| `expservices` | mit dem Kernel ausgeliefert |
| `expsite_api` | eigenes Repository |
| `expsite_app` | eigenes Repository |
| `expsite_core` | eigenes Repository |
| `expsite_data_media` | eigenes Repository |
| `expsite_installer` | eigenes Repository |
| `expui` | eigenes Repository |
| `ezauthorize` | Composer-Paket |
| `ezautosave` | Composer-Paket |
| `ezdemo` | Composer-Paket |
| `ezfind` | eigenes Repository |
| `ezflow` | Composer-Paket |
| `ezformtoken` | mit dem Kernel ausgeliefert |
| `ezgmaplocation` | Composer-Paket |
| `ezie` | Composer-Paket |
| `ezjscore` | mit dem Kernel ausgeliefert |
| `ezmbpaex` | Composer-Paket |
| `ezmultiupload` | Composer-Paket |
| `ezodf` | Composer-Paket |
| `ezoe` | mit dem Kernel ausgeliefert |
| `ezownerchange` | eigenes Repository |
| `ezpaypal` | Composer-Paket |
| `ezpm` | Composer-Paket |
| `ezpostgresqlcluster` | Composer-Paket |
| `ezprestapi` | Composer-Paket |
| `ezprestapiprovider` | Composer-Paket |
| `ezssp` | Composer-Paket |
| `ezstarrating` | Composer-Paket |
| `eztags` | Composer-Paket |
| `ezupdate` | Composer-Paket |
| `ezurlmultisite` | eigenes Repository |
| `ezwebin` | Composer-Paket |
| `ezwt` | Composer-Paket |
| `git_manager` | Composer-Paket |
| `hcaptcha` | Composer-Paket |
| `ngclasslist` | Composer-Paket |
| `nxc_powercontent` | Composer-Paket |
| `owsimpleoperator` | Composer-Paket |
| `powercontent` | eigenes Repository |
| `recaptcha` | Composer-Paket |
| `sevenx_authentication_2fa` | eigenes Repository |
| `sevenx_dse` | eigenes Repository |
| `sevenx_themes_media` | eigenes Repository |
| `sevenx_themes_simple` | Composer-Paket |
| `sevenx_themes_super` | Composer-Paket |
| `str_replace` | Composer-Paket |
| `swark` | Composer-Paket |
| `syndication` | Composer-Paket |
| `xrowextract` | Composer-Paket |
| `xrowmetadata` | Composer-Paket |

Eine Extension wird gelesen, wenn sie ein eigenes Git-Repository unterhalb von `extension/` ist, dessen Origin ein Repository von se7enxweb ist, oder ein dort installiertes Composer-Paket von se7enxweb; private Extensions und Website-Einstellungen werden nie gelesen. Eine Website mit weiteren installierten Extensions erhält einen größeren Katalog, wenn sie den Generator ausführt.

## Wie der Katalog gebaut wird

Ausführen im Wurzelverzeichnis der Installation (keine Datenbank, nichts wird geladen oder ausgeführt; der Tokenizer von PHP liest die Dateien):

```bash
php bin/php/exppatterncatalogue.php            # report which pages would change
php bin/php/exppatterncatalogue.php --write    # write doc/eng-us/standards/patterns/
php bin/php/exppatterncatalogue.php --check    # exit 1 when the pages are out of date
```

Die Ausgabe hängt nur vom Quellcode ab: keine Datumsangaben, keine Commit-IDs, alles sortiert, sodass ein zweiter Lauf auf demselben Code dieselben Bytes schreibt. Committen Sie die Seiten mit der Code-Änderung, die sie verändert.

Jede Kategorie hat eine Regel (die Tabelle oben und die Seite jedes Musters). Eine Klasse kann zu mehreren Kategorien passen. Die Regeln verwenden den Namen der Klasse, ihre Elternklassen und Interfaces (durch die gesamte Codebasis verfolgt), ihre Methoden (statisch, abstrakt) und die Registrierungsdateien des Kernels: `module.php` (Ansichten und Richtlinienfunktionen), `function_definition.php` (Fetch-Funktionen), `eztemplateautoload.php` (Template-Operatoren und -Funktionen), Verzeichnisse `cronjobs/`, Aufrufe von `ezpEvent` und INI-Einstellungen, deren Wert ein Klassenname ist. Namen sind eine Heuristik: Eine Klasse namens `...Manager` wird als Facade oder Service geführt, auch wenn sie etwas anderes ist, und ein Muster, das ohne die üblichen Namen umgesetzt ist, wird übersehen. Die Beschreibungen sind der erste Satz des Doc-Kommentars jeder Klasse.

> **Hinweis**
> Der Katalog beschreibt den Code, wie er ist, einschließlich geerbten Codes. Er ist keine Liste von Empfehlungen: Folgen Sie für neuen Code [den Regeln zu Schichten und Benennung](../architektur/schichten-und-benennung.md).
