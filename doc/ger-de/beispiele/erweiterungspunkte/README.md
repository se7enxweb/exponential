---
title: Erweiterungspunkte
audience: developer, admin
category: examples
order: 0
summary: Per INI gesteuerte Listen von Handlern, Klassen und Verzeichnissen, die eine Erweiterung ergänzen kann (am Namen erkannt, eine Heuristik).
keywords: Beispiele, Referenz, Erweiterungspunkte, examples, reference
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte

Wo eine Erweiterung sich über Einstellungen in den Kernel einklinkt: INI-Einstellungen, die Handler-Klassen, Filter, Provider, Listener, Repositorys oder Erweiterungsverzeichnisse nennen. Sie werden **am Namen der Einstellung erkannt (eine Heuristik)**, eine Seite pro INI-Datei, jede mit einem Override, das eine Erweiterung mitliefern kann. Andere Wege zu erweitern: [Template-Operatoren](../template-operatoren/README.md), [Module](../modulansichten/README.md), [Datentypen](../datentypen/README.md), [Workflow-Ereignisse](../workflow-ereignisse/README.md), [Cronjobs](../cronjobs/README.md) und [Design-Overrides](../kochbuch/template-override.md).

| INI-Datei | Aus | Einstellungen | Beispiele |
|---|---|---|---|
| [expservices.ini](expservices-expservices.md) | expservices | 7 | ForumClass, ForumContainerClass, TopicClass, ReplyClass, CommentClass, PollClass ... |
| [ezjscore.ini](ezjscore-ezjscore.md) | ezjscore | 7 | Class, Class, Class, Class, Class, Class ... |
| [ezoe.ini](ezoe-ezoe.md) | ezoe | 3 | UploadExtensionCheck, Plugins, ExternalPlugins |
| [audit.ini](audit.md) | Kernel | 25 | Sinks, Sinks, Sinks, Sinks, Sinks, Classes ... |
| [binaryfile.ini](binaryfile.md) | Kernel | 2 | Repositories, ExtensionRepositories |
| [browse.ini](browse.md) | Kernel | 3 | Class, Class, Class |
| [collaboration.ini](collaboration.md) | Kernel | 1 | Repositories |
| [content.ini](content.md) | Kernel | 33 | VersionHistoryClass, ExtensionDirectories, ExtensionDirectories, ExtensionDirectories, AvailableClasses, AvailableClasses ... |
| [contentcheck.ini](contentcheck.md) | Kernel | 3 | AssetClassList, StorageParentClassList, Handlers |
| [contentstructuremenu.ini](contentstructuremenu.md) | Kernel | 2 | ShowClasses, ChapterClasses |
| [cronjob.ini](cronjob.md) | Kernel | 1 | ExtensionDirectories |
| [datatable.ini](datatable.md) | Kernel | 1 | IgnoreClasses |
| [dbschema.ini](dbschema.md) | Kernel | 1 | SchemaHandlerClasses |
| [error.ini](error.md) | Kernel | 3 | DefaultErrorHandler, ErrorHandler, ErrorHandler |
| [exphelp.ini](exphelp.md) | Kernel | 2 | Providers, BookChecks |
| [ezxml.ini](ezxml.md) | Kernel | 4 | HandlerClass, AliasClasses, HandlerClass, AliasClasses |
| [file.ini](file.md) | Kernel | 5 | Handler, SystemUserCheck, Handlers, FileHandler, Listener |
| [image.ini](image.md) | Kernel | 15 | Filters, Filters, Filters, Filters, Filters, Filters ... |
| [ini.ini](ini.md) | Kernel | 1 | ScopeProviders |
| [kernelservices.ini](kernelservices.md) | Kernel | 25 | Class, Factory, Class, Factory, Class, Factory ... |
| [ldap.ini](ldap.md) | Kernel | 2 | LDAPSearchFilters, LDAPGroupClass |
| [mailpreferences.ini](mailpreferences.md) | Kernel | 11 | HandlerClass, HandlerClass, HandlerClass, HandlerClass, HandlerClass, HandlerClass ... |
| [membermanagement.ini](membermanagement.md) | Kernel | 1 | GuardedClasses |
| [module.ini](module.md) | Kernel | 2 | ModuleRepositories, ExtensionRepositories |
| [notification.ini](notification.md) | Kernel | 5 | IncludeClasses, ExtensionDirectories, ExtensionDirectories, ExtensionDirectories, DefaultTransport |
| [package.ini](package.md) | Kernel | 3 | ExtensionDirectories, HandlerList, HandlerList |
| [requestclassifier.ini](requestclassifier.md) | Kernel | 6 | Listeners, Class, Class, Class, Class, Class |
| [requestrules.ini](requestrules.md) | Kernel | 4 | RuleProviders, FactProviders, ConditionHandlers, ActionHandlers |
| [rest.ini](rest.md) | Kernel | 8 | PrefixFilterClass, ProviderClass, RendererClass, SkipFilter, Filters, Filters ... |
| [setup.ini](setup.md) | Kernel | 1 | TestClass |
| [shop.ini](shop.md) | Kernel | 7 | MathHandler, ExchangeRatesUpdateHandler, ExtensionDirectories, ExtensionDirectories, Handler, ExtensionDirectories ... |
| [shopaccount.ini](shopaccount.md) | Kernel | 4 | Repositories, ExtensionRepositories, Handler, Handler |
| [site.ini](site.md) | Kernel | 28 | purgeClass, purgeClass, DatabaseImplementation, Listeners, Implementation, Listeners ... |
| [subitemscolumns.ini](subitemscolumns.md) | Kernel | 112 | Class, Class, Class, Class, Class, Class ... |
| [syscard.ini](syscard.md) | Kernel | 1 | ProviderClasses |
| [template.ini](template.md) | Kernel | 2 | DefaultFormatter, OutputFormatter |
| [uiswitch.ini](uiswitch.md) | Kernel | 2 | Class, Class |
| [upload.ini](upload.md) | Kernel | 2 | ClassList, DefaultClass |
| [webdav.ini](webdav.md) | Kernel | 2 | FolderClasses, FolderClass |
| [workflow.ini](workflow.md) | Kernel | 1 | ExtensionDirectories |

- [Alle Beispiele](../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus den auf jeder Seite genannten Quellen. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
