---
title: Muster in kernel/modules
audience: developer
category: standards
order: 526
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/modules mit den Entwurfsmustern.
keywords: kernel/modules, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/modules

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/modules` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (78)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expContentExtensionWizard` | class extends expExtensionWizard | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/setup/expcontentextensionwizard.php:27` | Der Assistent für Inhalts-Extensions: was Autoren, Redakteure und Übersetzer sehen. |
| `expCronjobRunner` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/setup/expcronjobrunner.php:38` | Listet die Cronjob-Teile auf, die diese Installation definiert, und startet einen davon aus der Administrationsoberfläche, sodass er nach dem Ende der Anfrage weiterläuft… |
| `expDatatypeWizard` | class extends expExtensionWizard | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/setup/expdatatypewizard.php:22` | Der Datentyp-Assistent: baut eine funktionierende Extension für einen Inhaltsdatentyp. |
| `expDesignExtensionWizard` | class extends expExtensionWizard | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/setup/expdesignextensionwizard.php:21` | Erstellt eine Design-Extension: die Verzeichnisse, die Einstellungen und die Templates, die ein funktionierendes Design braucht, bereit zum Einschalten. |
| `expExtensionWizard` | abstract class | [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/setup/expextensionwizard.php:20` | Was jeder Extension-Assistent braucht, gleich welche Art von Extension er erstellt. |
| `expHandlerWizard` | class extends expExtensionWizard | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/setup/exphandlerwizard.php:29` | Erstellt einen Handler: eine Klasse, die durch eine INI-Einstellung benannt wird und die der Kernel anstelle seiner eigenen lädt. |
| `expKernelOverrideWizard` | class extends expExtensionWizard | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/setup/expkerneloverridewizard.php:24` | Der Kernel-Überschreibungsassistent: eine Kernel-Klasse vollständig ersetzen. |
| `expModuleExtensionWizard` | class extends expExtensionWizard | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/setup/expmoduleextensionwizard.php:31` | Erstellt eine Modul-Extension über bereits vorhandene Tabellen. |
| `expModuleTableCode` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/setup/expmoduletablecode.php:22` | Die Dateien einer Modultabelle (expModuleTableWizard): die persistente Klasse, das Modul mit seinen Ansichten list, edit, remove und JSON und seinen Richtlinien, die… |
| `expModuleTableWizard` | class extends expExtensionWizard | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/setup/expmoduletablewizard.php:34` | Der Modultabellen-Assistent (Setup › RAD › Module table, setup/moduletable; doc/eng-us/features/6.0/data-tables.md, „The module table wizard“): eine NEUE Datentabelle… |
| `expModuleWizard` | class extends expExtensionWizard | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/setup/expmodulewizard.php:28` | Der Modul-Assistent: ein Modul, seine Ansichten und die Richtlinien, die sie erreichen. |
| `expPreloadRunner` | class | – | `kernel/setup/exppreloadrunner.php:28` | Der Preloader als Bibliothek: er wärmt die Seiten-Caches der Site auf und meldet über einen Callback, was er getan hat, damit derselbe Code ein Terminal, eine Admin… |
| `expRADCatalogue` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/setup/expradcatalogue.php:29` | Jeder Punkt, an dem dieses System erweitert werden kann, und was dazu nötig ist. |
| `expRADHealth` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/setup/expradhealth.php:23` | Was in dieser Installation konfiguriert ist und nicht funktionieren kann. |
| `expRADSurvey` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/setup/expradsurvey.php:26` | Eine Live-Erhebung, an welchen Stellen diese Installation erweitert werden kann. |
| `expSettingsExtensionWizard` | class extends expExtensionWizard | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/setup/expsettingsextensionwizard.php:26` | Der Einstellungs-Extension-Assistent: eine Extension, die überwiegend aus INI-Dateien besteht. |
| `expStaticCacheRunner` | class | – | `kernel/setup/expstaticcacherunner.php:33` | Erzeugt den statischen Cache für jeweils eine Website und meldet jede Seite, sobald sie geschrieben ist, sodass die Administrationsoberfläche einen Lauf statt eines Bl… |
| `expTemplateExtensionWizard` | class extends expExtensionWizard | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/setup/exptemplateextensionwizard.php:25` | Der Template-Extension-Assistent: alles, was man einem Template beibringen kann aufzurufen. |
| `expWorkflowEventWizard` | class extends expExtensionWizard | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/setup/expworkfloweventwizard.php:27` | Erstellt einen Workflow-Ereignistyp: einen Schritt, den ein Workflow ausführen kann, wenn etwas veröffentlicht, verschoben, entfernt, registriert oder gekauft wird. |
| `eZCheckClasses` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/setup/expclassloadcheck.php:26` | Die Teile der Prüfung, die es lohnt, an einer Stelle zu haben. |
| `eZClassFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md) | `kernel/class/ezclassfunctioncollection.php:20` | – |
| `eZClassFunctions` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/class/ezclassfunctions.php:14` | – |
| `eZCollaborationFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md) | `kernel/collaboration/ezcollaborationfunctioncollection.php:20` | – |
| `eZCurrencyConverter` | class | [Singleton / instance()](singleton.md), [Strategie / Handler](strategie-handler.md) | `kernel/shop/classes/ezcurrencyconverter.php:20` | – |
| `eZCurrencyData` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/shop/classes/ezcurrencydata.php:12` | – |
| `eZECBHandler` | class extends eZExchangeRatesUpdateHandler | [Strategie / Handler](strategie-handler.md) | `kernel/shop/classes/exchangeratehandlers/ezecb/ezecbhandler.php:12` | – |
| `eZExchangeRatesUpdateHandler` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Strategie / Handler](strategie-handler.md) | `kernel/shop/classes/exchangeratehandlers/ezexchangeratesupdatehandler.php:12` | – |
| `eZInfocollectorFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/infocollector/ezinfocollectorfunctioncollection.php:20` | – |
| `eZLayoutFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md) | `kernel/layout/ezlayoutfunctioncollection.php:20` | – |
| `eZMultiEdit` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/content/multiedit_functions.php:16` | Die Arbeitsteile von content/multiedit. |
| `eZMultiPriceData` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/shop/classes/ezmultipricedata.php:12` | – |
| `eZNotificationFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md) | `kernel/notification/eznotificationfunctioncollection.php:20` | – |
| `eZPackageFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md) | `kernel/package/ezpackagefunctioncollection.php:20` | – |
| `eZPaymentCallbackChecker` | class | – | `kernel/shop/classes/ezpaymentcallbackchecker.php:19` | – |
| `eZPaymentGateway` | class | – | `kernel/shop/classes/ezpaymentgateway.php:17` | – |
| `eZPaymentObject` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/shop/classes/ezpaymentobject.php:22` | – |
| `ezpClusterGateway` | abstract class | [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/clustering/gateway.php:15` | Basis für ein Cluster-Gateway. |
| `ezpDfsMySQLiClusterGateway` | class extends ezpClusterGateway | – | `kernel/clustering/dfsmysqli.php:13` | DFS/MySQLi-Cluster-Gateway |
| `eZRedirectGateway` | class extends eZPaymentGateway | – | `kernel/shop/classes/ezredirectgateway.php:21` | – |
| `eZRoleFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md) | `kernel/role/ezrolefunctioncollection.php:20` | – |
| `eZRSSEditFunction` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/rss/edit_functions.php:14` | – |
| `eZRSSFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/rss/ezrssfunctioncollection.php:14` | – |
| `eZRSSListPager` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/rss/ezrsslistpager.php:24` | Ermittelt eine Seite einer Liste und die Links, die die anderen Seiten erreichen. |
| `eZSearchEngine` | class implements ezpSearchEngine | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/search/plugins/ezsearchengine/ezsearchengine.php:18` | – |
| `eZSearchFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md) | `kernel/search/ezsearchfunctioncollection.php:20` | – |
| `eZSectionFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md) | `kernel/section/ezsectionfunctioncollection.php:20` | – |
| `eZSetupFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md) | `kernel/setup/ezsetupfunctioncollection.php:20` | – |
| `eZSetupSummary` | class | – | `kernel/setup/ezsetup_summary.php:20` | – |
| `eZShopFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md) | `kernel/shop/ezshopfunctioncollection.php:20` | – |
| `eZShopFunctions` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/shop/classes/ezshopfunctions.php:12` | – |
| `eZShopOperationCollection` | class | – | `kernel/shop/ezshopoperationcollection.php:19` | – |
| `eZShopReceipt` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/shop/classes/ezshopreceipt.php:35` | Eine dauerhafte, als Lesezeichen speicherbare Adresse für eine aufgegebene Bestellung: ihr Beleg. |
| `eZSimplePrice` | class | – | `kernel/shop/classes/ezsimpleprice.php:37` | – |
| `eZStepCreateSites` | class extends eZStepInstaller | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/setup/steps/ezstep_create_sites.php:52` | – |
| `eZStepData` | class | – | `kernel/setup/steps/ezstep_data.php:18` | – |
| `eZStepDatabaseChoice` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_database_choice.php:21` | – |
| `eZStepDatabaseInit` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_database_init.php:18` | – |
| `eZStepEmailSettings` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_email_settings.php:18` | – |
| `eZStepFinal` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_final.php:18` | – |
| `eZStepInstaller` | class | – | `kernel/setup/steps/ezstep_installer.php:17` | – |
| `eZStepLanguageOptions` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_language_options.php:18` | – |
| `eZStepPackageLanguageOptions` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_package_language_options.php:18` | – |
| `eZStepRegistration` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_registration.php:18` | – |
| `eZStepSecurity` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_security.php:18` | – |
| `eZStepSiteAccess` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_site_access.php:18` | – |
| `eZStepSiteAdmin` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_site_admin.php:18` | – |
| `eZStepSiteDetails` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_site_details.php:18` | – |
| `eZStepSitePackages` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_site_packages.php:18` | – |
| `eZStepSiteTemplates` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_site_templates.php:18` | – |
| `eZStepSiteTypes` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_site_types.php:18` | – |
| `eZStepSystemCheck` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_system_check.php:18` | – |
| `eZStepSystemFinetune` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_system_finetune.php:18` | – |
| `eZStepWelcome` | class extends eZStepInstaller | – | `kernel/setup/steps/ezstep_welcome.php:17` | – |
| `eZURLFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md) | `kernel/url/ezurlfunctioncollection.php:20` | – |
| `eZUserFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md) | `kernel/user/ezuserfunctioncollection.php:20` | – |
| `eZUserOperationCollection` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/user/ezuseroperationcollection.php:20` | – |
| `eZWorkflowFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md) | `kernel/workflow/ezworkflowfunctioncollection.php:20` | – |
| `eZWorkflowFunctions` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/workflow/ezworkflowfunctions.php:14` | – |

## Modulansichten (231)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/apikey/list` | `list.php` | – | `kernel/apikey/module.php:24` |
| `/audit/dashboard` | `dashboard.php` | read | `kernel/audit/module.php:27` |
| `/audit/console` | `console.php` | read | `kernel/audit/module.php:33` |
| `/audit/event` | `event.php` | read | `kernel/audit/module.php:40` |
| `/audit/charts` | `charts.php` | read | `kernel/audit/module.php:47` |
| `/audit/alerts` | `alerts.php` | read | `kernel/audit/module.php:54` |
| `/audit/export` | `export.php` | read | `kernel/audit/module.php:61` |
| `/audit/archives` | `archives.php` | manage | `kernel/audit/module.php:68` |
| `/audit/settings` | `settings.php` | manage | `kernel/audit/module.php:75` |
| `/audit/recent` | `recent.php` | read | `kernel/audit/module.php:81` |
| `/class/edit` | `edit.php` | – | `kernel/class/module.php:13` |
| `/class/view` | `view.php` | – | `kernel/class/module.php:20` |
| `/class/copy` | `copy.php` | – | `kernel/class/module.php:26` |
| `/class/down` | `edit.php` | – | `kernel/class/module.php:31` |
| `/class/up` | `edit.php` | – | `kernel/class/module.php:35` |
| `/class/removeclass` | `removeclass.php` | – | `kernel/class/module.php:39` |
| `/class/removegroup` | `removegroup.php` | – | `kernel/class/module.php:43` |
| `/class/classlist` | `classlist.php` | – | `kernel/class/module.php:47` |
| `/class/grouplist` | `grouplist.php` | – | `kernel/class/module.php:51` |
| `/class/groupedit` | `groupedit.php` | – | `kernel/class/module.php:55` |
| `/class/translation` | `translation.php` | – | `kernel/class/module.php:60` |
| `/collaboration/action` | `action.php` | – | `kernel/collaboration/module.php:13` |
| `/collaboration/view` | `view.php` | – | `kernel/collaboration/module.php:24` |
| `/collaboration/item` | `item.php` | – | `kernel/collaboration/module.php:34` |
| `/collaboration/group` | `group.php` | – | `kernel/collaboration/module.php:40` |
| `/content/multiedit` | `multiedit.php` | edit | `kernel/content/module.php:23` |
| `/content/edit` | `edit.php` | bearbeiten oder anlegen | `kernel/content/module.php:34` |
| `/content/removenode` | `removenode.php` | edit | `kernel/content/module.php:70` |
| `/content/removeassignment` | `removeassignment.php` | edit | `kernel/content/module.php:79` |
| `/content/pdf` | `pdf.php` | pdf | `kernel/content/module.php:89` |
| `/content/view` | `view.php` | read | `kernel/content/module.php:101` |
| `/content/copy` | `copy.php` | read | `kernel/content/module.php:113` |
| `/content/copysubtree` | `copysubtree.php` | create | `kernel/content/module.php:124` |
| `/content/versionview` | `versionview.php` | versionread | `kernel/content/module.php:138` |
| `/content/restore` | `restore.php` | restore | `kernel/content/module.php:158` |
| `/content/search` | `search.php` | read | `kernel/content/module.php:169` |
| `/content/urlalias` | `urlalias.php` | edit | `kernel/content/module.php:176` |
| `/content/urltranslator` | `urlalias_global.php` | urltranslator | `kernel/content/module.php:190` |
| `/content/urlwildcards` | `urlalias_wildcard.php` | urltranslator | `kernel/content/module.php:210` |
| `/content/advancedsearch` | `advancedsearch.php` | read | `kernel/content/module.php:227` |
| `/content/browse` | `browse.php` | read | `kernel/content/module.php:234` |
| `/content/upload` | `upload.php` | create | `kernel/content/module.php:242` |
| `/content/removeobject` | `removeobject.php` | read | `kernel/content/module.php:252` |
| `/content/removeuserobject` | `removeobject.php` | read | `kernel/content/module.php:258` |
| `/content/removemediaobject` | `removeobject.php` | read | `kernel/content/module.php:264` |
| `/content/removeeditversion` | `removeeditversion.php` | read | `kernel/content/module.php:270` |
| `/content/download` | `download.php` | read | `kernel/content/module.php:277` |
| `/content/action` | `action.php` | read | `kernel/content/module.php:284` |
| `/content/collectinformation` | `collectinformation.php` | read | `kernel/content/module.php:356` |
| `/content/draft` | `draft.php` | edit | `kernel/content/module.php:366` |
| `/content/history` | `history.php` | lesen oder bearbeiten | `kernel/content/module.php:373` |
| `/content/trash` | `trash.php` | restore | `kernel/content/module.php:389` |
| `/content/translations` | `translations.php` | translations | `kernel/content/module.php:396` |
| `/content/tipafriend` | `tipafriend.php` | tipafriend, read | `kernel/content/module.php:412` |
| `/content/keyword` | `keyword.php` | read | `kernel/content/module.php:418` |
| `/content/collectedinfo` | `collectedinfo.php` | read | `kernel/content/module.php:425` |
| `/content/bookmark` | `bookmark.php` | bookmark | `kernel/content/module.php:431` |
| `/content/pendinglist` | `pendinglist.php` | pendinglist | `kernel/content/module.php:442` |
| `/content/new` | `newcontent.php` | read | `kernel/content/module.php:449` |
| `/content/hide` | `hide.php` | hide | `kernel/content/module.php:455` |
| `/content/move` | `move.php` | edit | `kernel/content/module.php:463` |
| `/content/reverserelatedlist` | `reverserelatedlist.php` | reverserelatedlist | `kernel/content/module.php:469` |
| `/content/translation` | `translation.php` | read | `kernel/content/module.php:476` |
| `/content/treemenu` | `treemenu.php` | read | `kernel/content/module.php:505` |
| `/content/subitemsexport` | `subitemsexport.php` | read | `kernel/content/module.php:513` |
| `/content/dashboard` | `dashboard.php` | dashboard | `kernel/content/module.php:520` |
| `/content/queued` | `queued.php` | edit | `kernel/content/module.php:527` |
| `/content/job` | `job.php` | read | `kernel/content/module.php:536` |
| `/content/jobs` | `jobs.php` | read | `kernel/content/module.php:544` |
| `/contentcheck/unreferenced` | `unreferenced.php` | unreferenced | `kernel/contentcheck/module.php:28` |
| `/contentcheck/trash` | `trash.php` | unreferenced, trash | `kernel/contentcheck/module.php:37` |
| `/contentcheck/brokenrelations` | `brokenrelations.php` | brokenrelations | `kernel/contentcheck/module.php:46` |
| `/contentcheck/fixfiles` | `fixfiles.php` | brokenrelations, fixfiles | `kernel/contentcheck/module.php:56` |
| `/contentcheck/scope` | `scope.php` | – | `kernel/contentcheck/module.php:64` |
| `/contentcheck/storage` | `storage.php` | storage | `kernel/contentcheck/module.php:72` |
| `/contentcheck/storageaction` | `storageaction.php` | storage | `kernel/contentcheck/module.php:81` |
| `/contentfinder/search` | `search.php` | search | `kernel/contentfinder/module.php:25` |
| `/contentfinder/action` | `action.php` | search, delete | `kernel/contentfinder/module.php:33` |
| `/csp/report` | `report.php` | – | `kernel/csp/module.php:22` |
| `/datatable/dashboard` | `dashboard.php` | read | `kernel/datatable/module.php:21` |
| `/datatable/table` | `table.php` | read | `kernel/datatable/module.php:28` |
| `/datatable/rows` | `rows.php` | read | `kernel/datatable/module.php:34` |
| `/datatable/schema` | `schema.php` | manage | `kernel/datatable/module.php:41` |
| `/datatable/syncs` | `syncs.php` | sync | `kernel/datatable/module.php:48` |
| `/datatable/sync` | `sync.php` | sync | `kernel/datatable/module.php:54` |
| `/datatable/conflicts` | `conflicts.php` | sync | `kernel/datatable/module.php:61` |
| `/datatable/connections` | `connections.php` | connections | `kernel/datatable/module.php:68` |
| `/datatable/connection` | `connection.php` | connections | `kernel/datatable/module.php:74` |
| `/error/view` | `view.php` | – | `kernel/error/module.php:13` |
| `/exphelp/dashboard` | `dashboard.php` | read | `kernel/exphelp/module.php:41` |
| `/exphelp/audience` | `audience.php` | read | `kernel/exphelp/module.php:49` |
| `/exphelp/viewer` | `viewer.php` | read | `kernel/exphelp/module.php:59` |
| `/exphelp/contents` | `contents.php` | read | `kernel/exphelp/module.php:67` |
| `/exphelp/search` | `search.php` | read | `kernel/exphelp/module.php:77` |
| `/exphelp/extensions` | `extensions.php` | read | `kernel/exphelp/module.php:93` |
| `/exphelp/asset` | `asset.php` | read | `kernel/exphelp/module.php:100` |
| `/exphelp/image` | `image.php` | read | `kernel/exphelp/module.php:108` |
| `/exphelp/pdf` | `pdf.php` | read | `kernel/exphelp/module.php:116` |
| `/exphelp/report` | `report.php` | read | `kernel/exphelp/module.php:123` |
| `/exphelp/topic` | `topic.php` | read | `kernel/exphelp/module.php:132` |
| `/exphelp/state` | `state.php` | read | `kernel/exphelp/module.php:143` |
| `/exphelp/feedback` | `feedback.php` | – | `kernel/exphelp/module.php:151` |
| `/exphelp/manual` | `manual.php` | read | `kernel/exphelp/module.php:159` |
| `/exphelp/context` | `context.php` | context | `kernel/exphelp/module.php:168` |
| `/ezinfo/copyright` | `copyright.php` | read | `kernel/ezinfo/module.php:14` |
| `/ezinfo/about` | `about.php` | read | `kernel/ezinfo/module.php:21` |
| `/ezinfo/is_alive` | `isalive.php` | read | `kernel/ezinfo/module.php:27` |
| `/infocollector/overview` | `overview.php` | read | `kernel/infocollector/module.php:13` |
| `/infocollector/collectionlist` | `collectionlist.php` | read | `kernel/infocollector/module.php:23` |
| `/infocollector/view` | `view.php` | read | `kernel/infocollector/module.php:34` |
| `/infocollector/export` | `export.php` | read | `kernel/infocollector/module.php:43` |
| `/layout/set` | `set.php` | – | `kernel/layout/module.php:14` |
| `/mailpreferences/settings` | `settings.php` | – | `kernel/mailpreferences/module.php:22` |
| `/mailpreferences/manage` | `manage.php` | – | `kernel/mailpreferences/module.php:30` |
| `/mailpreferences/unsubscribe` | `unsubscribe.php` | – | `kernel/mailpreferences/module.php:37` |
| `/mailpreferences/confirm` | `confirm.php` | – | `kernel/mailpreferences/module.php:43` |
| `/mailpreferences/request` | `request.php` | – | `kernel/mailpreferences/module.php:49` |
| `/mailpreferences/export` | `export.php` | – | `kernel/mailpreferences/module.php:55` |
| `/mailpreferences/admin` | `admin.php` | administrate | `kernel/mailpreferences/module.php:62` |
| `/notification/settings` | `settings.php` | use | `kernel/notification/module.php:15` |
| `/notification/runfilter` | `runfilter.php` | administrate | `kernel/notification/module.php:23` |
| `/notification/status` | `status.php` | administrate | `kernel/notification/module.php:31` |
| `/notification/job` | `job.php` | administrate | `kernel/notification/module.php:39` |
| `/notification/addtonotification` | `addtonotification.php` | use | `kernel/notification/module.php:46` |
| `/package/list` | `list.php` | list | `kernel/package/module.php:13` |
| `/package/export` | `export.php` | export | `kernel/package/module.php:62` |
| `/package/view` | `view.php` | read | `kernel/package/module.php:71` |
| `/pdf/edit` | `edit.php` | edit | `kernel/pdf/module.php:13` |
| `/pdf/list` | `list.php` | edit | `kernel/pdf/module.php:48` |
| `/role/list` | `list.php` | – | `kernel/role/module.php:13` |
| `/role/edit` | `edit.php` | – | `kernel/role/module.php:19` |
| `/role/copy` | `copy.php` | – | `kernel/role/module.php:24` |
| `/role/policyedit` | `policyedit.php` | – | `kernel/role/module.php:29` |
| `/role/view` | `view.php` | – | `kernel/role/module.php:34` |
| `/role/assign` | `assign.php` | – | `kernel/role/module.php:39` |
| `/rss/list` | `list.php` | edit | `kernel/rss/module.php:13` |
| `/rss/edit_export` | `edit_export.php` | edit | `kernel/rss/module.php:28` |
| `/rss/edit_import` | `edit_import.php` | edit | `kernel/rss/module.php:41` |
| `/rss/feed` | `feed.php` | feed | `kernel/rss/module.php:55` |
| `/search/stats` | `stats.php` | – | `kernel/search/module.php:15` |
| `/secrets/list` | `list.php` | manage | `kernel/secrets/module.php:19` |
| `/section/list` | `list.php` | ansehen, bearbeiten oder zuweisen | `kernel/section/module.php:13` |
| `/section/view` | `view.php` | ansehen oder zuweisen | `kernel/section/module.php:20` |
| `/section/edit` | `edit.php` | edit | `kernel/section/module.php:28` |
| `/section/assign` | `assign.php` | assign | `kernel/section/module.php:35` |
| `/settings/view` | `view.php` | – | `kernel/settings/module.php:14` |
| `/settings/edit` | `edit.php` | – | `kernel/settings/module.php:21` |
| `/setup/init` | `ezsetup.php` | install | `kernel/setup/module.php:18` |
| `/setup/cache` | `cache.php` | managecache | `kernel/setup/module.php:25` |
| `/shop/add` | `add.php` | buy | `kernel/shop/module.php:14` |
| `/shop/orderview` | `orderview.php` | buy | `kernel/shop/module.php:20` |
| `/shop/orderreceipt` | `orderreceipt.php` | – | `kernel/shop/module.php:28` |
| `/shop/orderreciept` | `orderreciept.php` | – | `kernel/shop/module.php:36` |
| `/shop/updatebasket` | `updatebasket.php` | buy | `kernel/shop/module.php:42` |
| `/shop/basket` | `basket.php` | buy | `kernel/shop/module.php:48` |
| `/shop/register` | `register.php` | buy | `kernel/shop/module.php:70` |
| `/shop/userregister` | `userregister.php` | buy | `kernel/shop/module.php:80` |
| `/shop/wishlist` | `wishlist.php` | buy | `kernel/shop/module.php:90` |
| `/shop/dashboard` | `dashboard.php` | administrate | `kernel/shop/module.php:99` |
| `/shop/orderlist` | `orderlist.php` | administrate | `kernel/shop/module.php:105` |
| `/shop/archivelist` | `archivelist.php` | administrate | `kernel/shop/module.php:112` |
| `/shop/removeorder` | `removeorder.php` | administrate | `kernel/shop/module.php:119` |
| `/shop/archiveorder` | `archiveorder.php` | administrate | `kernel/shop/module.php:126` |
| `/shop/unarchiveorder` | `unarchiveorder.php` | administrate | `kernel/shop/module.php:133` |
| `/shop/customerlist` | `customerlist.php` | administrate | `kernel/shop/module.php:141` |
| `/shop/customerorderview` | `customerorderview.php` | administrate | `kernel/shop/module.php:148` |
| `/shop/statistics` | `orderstatistics.php` | administrate | `kernel/shop/module.php:154` |
| `/shop/confirmorder` | `confirmorder.php` | buy | `kernel/shop/module.php:160` |
| `/shop/checkout` | `checkout.php` | buy | `kernel/shop/module.php:166` |
| `/shop/vattype` | `vattype.php` | setup | `kernel/shop/module.php:172` |
| `/shop/vatrules` | `vatrules.php` | setup | `kernel/shop/module.php:185` |
| `/shop/editvatrule` | `editvatrule.php` | setup | `kernel/shop/module.php:191` |
| `/shop/productcategories` | `productcategories.php` | setup | `kernel/shop/module.php:209` |
| `/shop/discountgroup` | `discountgroup.php` | setup | `kernel/shop/module.php:222` |
| `/shop/discountgroupedit` | `discountgroupedit.php` | setup | `kernel/shop/module.php:228` |
| `/shop/discountruleedit` | `discountruleedit.php` | setup | `kernel/shop/module.php:235` |
| `/shop/discountgroupview` | `discountgroupmembershipview.php` | setup | `kernel/shop/module.php:243` |
| `/shop/status` | `status.php` | edit_status | `kernel/shop/module.php:250` |
| `/shop/setstatus` | `setstatus.php` | setstatus | `kernel/shop/module.php:256` |
| `/shop/currencylist` | `currencylist.php` | setup | `kernel/shop/module.php:262` |
| `/shop/editcurrency` | `editcurrency.php` | setup | `kernel/shop/module.php:279` |
| `/shop/preferredcurrency` | `preferredcurrency.php` | buy | `kernel/shop/module.php:292` |
| `/shop/productsoverview` | `productsoverview.php` | administrate | `kernel/shop/module.php:298` |
| `/shop/setpreferredcurrency` | `setpreferredcurrency.php` | buy | `kernel/shop/module.php:312` |
| `/shop/setusercountry` | `setusercountry.php` | buy | `kernel/shop/module.php:321` |
| `/state/assign` | `assign.php` | assign | `kernel/state/module.php:15` |
| `/state/groups` | `groups.php` | administrate | `kernel/state/module.php:26` |
| `/state/group` | `group.php` | administrate | `kernel/state/module.php:37` |
| `/state/group_edit` | `group_edit.php` | administrate | `kernel/state/module.php:50` |
| `/state/view` | `view.php` | administrate | `kernel/state/module.php:60` |
| `/state/edit` | `edit.php` | administrate | `kernel/state/module.php:68` |
| `/trigger/list` | `list.php` | – | `kernel/trigger/module.php:13` |
| `/uiswitch/set` | `set.php` | – | `kernel/uiswitch/module.php:24` |
| `/url/list` | `list.php` | – | `kernel/url/module.php:13` |
| `/url/view` | `view.php` | – | `kernel/url/module.php:22` |
| `/url/edit` | `edit.php` | – | `kernel/url/module.php:28` |
| `/user/logout` | `logout.php` | login | `kernel/user/module.php:14` |
| `/user/login` | `login.php` | login | `kernel/user/module.php:20` |
| `/user/setting` | `setting.php` | preferences | `kernel/user/module.php:34` |
| `/user/preferences` | `preferences.php` | login | `kernel/user/module.php:40` |
| `/user/megamenu` | `megamenu.php` | login | `kernel/user/module.php:52` |
| `/user/passkeys` | `passkeys.php` | login | `kernel/user/module.php:61` |
| `/user/password` | `password.php` | password | `kernel/user/module.php:67` |
| `/user/forgotpassword` | `forgotpassword.php` | password | `kernel/user/module.php:74` |
| `/user/edit` | `edit.php` | login | `kernel/user/module.php:85` |
| `/user/register` | `register.php` | register | `kernel/user/module.php:95` |
| `/user/activate` | `activate.php` | login | `kernel/user/module.php:105` |
| `/user/success` | `success.php` | register | `kernel/user/module.php:112` |
| `/user/unactivated` | `unactivated.php` | activation | `kernel/user/module.php:119` |
| `/user/hygiene` | `hygiene.php` | hygiene | `kernel/user/module.php:144` |
| `/user/deactivate` | `deactivate.php` | deactivate | `kernel/user/module.php:157` |
| `/user/registration` | `registration.php` | login | `kernel/user/module.php:170` |
| `/user/sessions` | `sessions.php` | login | `kernel/user/module.php:187` |
| `/visual/toolbarlist` | `toolbarlist.php` | – | `kernel/visual/module.php:15` |
| `/visual/toolbar` | `toolbar.php` | – | `kernel/visual/module.php:20` |
| `/visual/menuconfig` | `menuconfig.php` | – | `kernel/visual/module.php:33` |
| `/visual/templatelist` | `templatelist.php` | – | `kernel/visual/module.php:42` |
| `/visual/templateview` | `templateview.php` | – | `kernel/visual/module.php:48` |
| `/visual/templateedit` | `templateedit.php` | – | `kernel/visual/module.php:57` |
| `/visual/templatecreate` | `templatecreate.php` | – | `kernel/visual/module.php:66` |
| `/workflow/view` | `view.php` | – | `kernel/workflow/module.php:13` |
| `/workflow/edit` | `edit.php` | – | `kernel/workflow/module.php:17` |
| `/workflow/groupedit` | `groupedit.php` | – | `kernel/workflow/module.php:22` |
| `/workflow/down` | `edit.php` | – | `kernel/workflow/module.php:27` |
| `/workflow/up` | `edit.php` | – | `kernel/workflow/module.php:31` |
| `/workflow/workflowlist` | `workflowlist.php` | – | `kernel/workflow/module.php:35` |
| `/workflow/grouplist` | `grouplist.php` | – | `kernel/workflow/module.php:39` |
| `/workflow/process` | `process.php` | – | `kernel/workflow/module.php:43` |
| `/workflow/run` | `run.php` | – | `kernel/workflow/module.php:47` |
| `/workflow/event` | `event.php` | – | `kernel/workflow/module.php:51` |
| `/workflow/processlist` | `processlist.php` | – | `kernel/workflow/module.php:55` |

## Richtlinienfunktionen (94)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `apikey/create` | values | `kernel/apikey/module.php:47` |
| `audit/read` | – | `kernel/audit/module.php:90` |
| `audit/manage` | – | `kernel/audit/module.php:121` |
| `content/bookmark` | – | `kernel/content/module.php:664` |
| `content/move` | – | `kernel/content/module.php:666` |
| `content/read` | Class, Section, Owner, Group, Node, Subtree | `kernel/content/module.php:668` |
| `content/diff` | Class, Section, Owner, Node, Subtree | `kernel/content/module.php:675` |
| `content/view_embed` | Class, Section, Owner, Node, Subtree | `kernel/content/module.php:680` |
| `content/create` | Class, Section, ParentOwner, ParentGroup, ParentClass, ParentDepth, Subtree, Language | `kernel/content/module.php:685` |
| `content/edit` | Class, Section, Owner, Group, Node, Subtree, Language | `kernel/content/module.php:695` |
| `content/publish` | Class, Section, Owner, Group, Node, Subtree, Language | `kernel/content/module.php:704` |
| `content/manage_locations` | Class, Section, Owner, Subtree | `kernel/content/module.php:713` |
| `content/hide` | Class, Section, Owner, Group, Node, Subtree, Language | `kernel/content/module.php:719` |
| `content/reverserelatedlist` | – | `kernel/content/module.php:727` |
| `content/translate` | Class, Section, Owner, Node, Subtree, Language | `kernel/content/module.php:729` |
| `content/remove` | Class, Section, Owner, Node, Subtree | `kernel/content/module.php:735` |
| `content/versionread` | Class, Section, Owner, Status, Node, Subtree | `kernel/content/module.php:743` |
| `content/versionremove` | Class, Section, Owner, Status, Node, Subtree | `kernel/content/module.php:750` |
| `content/pdf` | Class, Section, Owner, Node, Subtree | `kernel/content/module.php:757` |
| `content/translations` | – | `kernel/content/module.php:763` |
| `content/urltranslator` | – | `kernel/content/module.php:764` |
| `content/pendinglist` | – | `kernel/content/module.php:765` |
| `content/restore` | – | `kernel/content/module.php:766` |
| `content/cleantrash` | – | `kernel/content/module.php:767` |
| `content/tipafriend` | – | `kernel/content/module.php:768` |
| `content/dashboard` | – | `kernel/content/module.php:769` |
| `content/view_system_url` | – | `kernel/content/module.php:773` |
| `content/jobs` | – | `kernel/content/module.php:775` |
| `content/publish_without_notification` | – | `kernel/content/module.php:779` |
| `contentcheck/unreferenced` | – | `kernel/contentcheck/module.php:89` |
| `contentcheck/trash` | – | `kernel/contentcheck/module.php:91` |
| `contentcheck/brokenrelations` | – | `kernel/contentcheck/module.php:93` |
| `contentcheck/fixfiles` | – | `kernel/contentcheck/module.php:96` |
| `contentcheck/storage` | – | `kernel/contentcheck/module.php:98` |
| `contentcheck/quarantine` | – | `kernel/contentcheck/module.php:100` |
| `contentcheck/permissions` | – | `kernel/contentcheck/module.php:102` |
| `contentfinder/search` | – | `kernel/contentfinder/module.php:41` |
| `contentfinder/delete` | – | `kernel/contentfinder/module.php:43` |
| `datatable/read` | – | `kernel/datatable/module.php:82` |
| `datatable/manage` | – | `kernel/datatable/module.php:84` |
| `datatable/connections` | – | `kernel/datatable/module.php:86` |
| `datatable/sync` | – | `kernel/datatable/module.php:88` |
| `exphelp/read` | – | `kernel/exphelp/module.php:176` |
| `exphelp/context` | – | `kernel/exphelp/module.php:178` |
| `exphelp/feedback` | – | `kernel/exphelp/module.php:180` |
| `ezinfo/read` | – | `kernel/ezinfo/module.php:33` |
| `infocollector/read` | – | `kernel/infocollector/module.php:50` |
| `mailpreferences/administrate` | – | `kernel/mailpreferences/module.php:72` |
| `mailpreferences/export` | – | `kernel/mailpreferences/module.php:73` |
| `notification/use` | – | `kernel/notification/module.php:53` |
| `notification/administrate` | – | `kernel/notification/module.php:54` |
| `package/read` | Typ | `kernel/package/module.php:179` |
| `package/list` | Typ | `kernel/package/module.php:180` |
| `package/create` | Type, CreatorType, Role | `kernel/package/module.php:181` |
| `package/edit` | Typ | `kernel/package/module.php:184` |
| `package/remove` | Typ | `kernel/package/module.php:185` |
| `package/install` | Typ | `kernel/package/module.php:186` |
| `package/import` | Typ | `kernel/package/module.php:187` |
| `package/export` | Typ | `kernel/package/module.php:188` |
| `package/export_extension` | – | `kernel/package/module.php:191` |
| `pdf/create` | – | `kernel/pdf/module.php:60` |
| `pdf/edit` | – | `kernel/pdf/module.php:61` |
| `rss/feed` | – | `kernel/rss/module.php:62` |
| `rss/edit` | – | `kernel/rss/module.php:63` |
| `secrets/manage` | – | `kernel/secrets/module.php:28` |
| `section/assign` | Class, Section, Owner, NewSection | `kernel/section/module.php:79` |
| `section/edit` | – | `kernel/section/module.php:83` |
| `section/view` | – | `kernel/section/module.php:84` |
| `setup/administrate` | – | `kernel/setup/module.php:485` |
| `setup/install` | – | `kernel/setup/module.php:486` |
| `setup/logs` | – | `kernel/setup/module.php:488` |
| `setup/managelogs` | – | `kernel/setup/module.php:489` |
| `setup/unmasklogs` | – | `kernel/setup/module.php:490` |
| `setup/managecache` | – | `kernel/setup/module.php:491` |
| `setup/managecronjobs` | – | `kernel/setup/module.php:492` |
| `setup/preload` | – | `kernel/setup/module.php:493` |
| `setup/setup` | – | `kernel/setup/module.php:494` |
| `setup/system_info` | – | `kernel/setup/module.php:495` |
| `shop/setup` | – | `kernel/shop/module.php:350` |
| `shop/administrate` | – | `kernel/shop/module.php:351` |
| `shop/buy` | – | `kernel/shop/module.php:352` |
| `shop/edit_status` | – | `kernel/shop/module.php:353` |
| `shop/setstatus` | FromStatus, ToStatus | `kernel/shop/module.php:354` |
| `state/administrate` | – | `kernel/state/module.php:132` |
| `state/assign` | Class, Section, Owner, Group, Node, Subtree | `kernel/state/module.php:134` |
| `user/login` | SiteAccess | `kernel/user/module.php:204` |
| `user/password` | – | `kernel/user/module.php:205` |
| `user/preferences` | – | `kernel/user/module.php:206` |
| `user/register` | – | `kernel/user/module.php:207` |
| `user/selfedit` | – | `kernel/user/module.php:208` |
| `user/activation` | – | `kernel/user/module.php:209` |
| `user/hygiene` | – | `kernel/user/module.php:210` |
| `user/deactivate` | – | `kernel/user/module.php:211` |
| `user/sessions` | – | `kernel/user/module.php:213` |

## Fetch-Funktionen (182)

| Fetch | Implementiert von | Definiert in |
| --- | --- | --- |
| `fetch( 'apikey', 'can_create' )` | `expApiKeyFunctionCollection::fetchCanCreate()` | `kernel/apikey/function_definition.php:17` |
| `fetch( 'apikey', 'counts' )` | `expApiKeyFunctionCollection::fetchCounts()` | `kernel/apikey/function_definition.php:22` |
| `fetch( 'audit', 'events' )` | `expAuditFunctionCollection::fetchEvents()` | `kernel/audit/function_definition.php:14` |
| `fetch( 'audit', 'count' )` | `expAuditFunctionCollection::fetchCount()` | `kernel/audit/function_definition.php:35` |
| `fetch( 'audit', 'event' )` | `expAuditFunctionCollection::fetchEvent()` | `kernel/audit/function_definition.php:54` |
| `fetch( 'audit', 'chain_status' )` | `expAuditFunctionCollection::fetchChainStatus()` | `kernel/audit/function_definition.php:59` |
| `fetch( 'audit', 'can_read' )` | `expAuditFunctionCollection::fetchCanRead()` | `kernel/audit/function_definition.php:66` |
| `fetch( 'audit', 'hidden_menu_links' )` | `expAuditFunctionCollection::fetchHiddenMenuLinks()` | `kernel/audit/function_definition.php:72` |
| `fetch( 'class', 'list' )` | `eZClassFunctionCollection::fetchClassList()` | `kernel/class/function_definition.php:11` |
| `fetch( 'class', 'list_by_groups' )` | `eZClassFunctionCollection::fetchClassListByGroups()` | `kernel/class/function_definition.php:25` |
| `fetch( 'class', 'latest_list' )` | `eZClassFunctionCollection::fetchLatestClassList()` | `kernel/class/function_definition.php:39` |
| `fetch( 'class', 'attribute_list' )` | `eZClassFunctionCollection::fetchClassAttributeList()` | `kernel/class/function_definition.php:52` |
| `fetch( 'class', 'override_template_list' )` | `eZClassFunctionCollection::fetchOverrideTemplateList()` | `kernel/class/function_definition.php:63` |
| `fetch( 'collaboration', 'participant' )` | `eZCollaborationFunctionCollection::fetchParticipant()` | `kernel/collaboration/function_definition.php:11` |
| `fetch( 'collaboration', 'participant_list' )` | `eZCollaborationFunctionCollection::fetchParticipantList()` | `kernel/collaboration/function_definition.php:22` |
| `fetch( 'collaboration', 'participant_map' )` | `eZCollaborationFunctionCollection::fetchParticipantMap()` | `kernel/collaboration/function_definition.php:39` |
| `fetch( 'collaboration', 'message_list' )` | `eZCollaborationFunctionCollection::fetchMessageList()` | `kernel/collaboration/function_definition.php:59` |
| `fetch( 'collaboration', 'item_list' )` | `eZCollaborationFunctionCollection::fetchItemList()` | `kernel/collaboration/function_definition.php:76` |
| `fetch( 'collaboration', 'inbox' )` | `eZCollaborationFunctionCollection::fetchInbox()` | `kernel/collaboration/function_definition.php:102` |
| `fetch( 'collaboration', 'inbox_row' )` | `eZCollaborationFunctionCollection::fetchInboxRow()` | `kernel/collaboration/function_definition.php:125` |
| `fetch( 'collaboration', 'item_count' )` | `eZCollaborationFunctionCollection::fetchItemCount()` | `kernel/collaboration/function_definition.php:132` |
| `fetch( 'collaboration', 'group_tree' )` | `eZCollaborationFunctionCollection::fetchGroupTree()` | `kernel/collaboration/function_definition.php:149` |
| `fetch( 'collaboration', 'tree_count' )` | `eZContentFunctionCollection::fetchObjectTreeCount()` | `kernel/collaboration/function_definition.php:169` |
| `fetch( 'content', 'object' )` | `eZContentFunctionCollection::fetchContentObject()` | `kernel/content/function_definition.php:12` |
| `fetch( 'content', 'version' )` | `eZContentFunctionCollection::fetchContentVersion()` | `kernel/content/function_definition.php:25` |
| `fetch( 'content', 'node' )` | `eZContentFunctionCollection::fetchContentNode()` | `kernel/content/function_definition.php:37` |
| `fetch( 'content', 'locale_list' )` | `eZContentFunctionCollection::fetchLocaleList()` | `kernel/content/function_definition.php:58` |
| `fetch( 'content', 'locale' )` | `eZContentFunctionCollection::fetchLocale()` | `kernel/content/function_definition.php:67` |
| `fetch( 'content', 'prioritized_languages' )` | `eZContentFunctionCollection::fetchPrioritizedLanguages()` | `kernel/content/function_definition.php:77` |
| `fetch( 'content', 'prioritized_language_codes' )` | `eZContentFunctionCollection::fetchPrioritizedLanguageCodes()` | `kernel/content/function_definition.php:83` |
| `fetch( 'content', 'translation_list' )` | `eZContentFunctionCollection::fetchTranslationList()` | `kernel/content/function_definition.php:89` |
| `fetch( 'content', 'language_states' )` | `expContentLanguageStates::fetchForObject()` | `kernel/content/function_definition.php:96` |
| `fetch( 'content', 'non_translation_list' )` | `eZContentFunctionCollection::fetchNonTranslationList()` | `kernel/content/function_definition.php:104` |
| `fetch( 'content', 'class' )` | `eZContentFunctionCollection::fetchClass()` | `kernel/content/function_definition.php:115` |
| `fetch( 'content', 'class_attribute_list' )` | `eZContentFunctionCollection::fetchClassAttributeList()` | `kernel/content/function_definition.php:123` |
| `fetch( 'content', 'class_attribute' )` | `eZContentFunctionCollection::fetchClassAttribute()` | `kernel/content/function_definition.php:135` |
| `fetch( 'content', 'calendar' )` | `eZContentFunctionCollection::calendar()` | `kernel/content/function_definition.php:147` |
| `fetch( 'content', 'list' )` | `eZContentFunctionCollection::fetchObjectTree()` | `kernel/content/function_definition.php:208` |
| `fetch( 'content', 'list_count' )` | `eZContentFunctionCollection::fetchObjectTreeCount()` | `kernel/content/function_definition.php:292` |
| `fetch( 'content', 'tree' )` | `eZContentFunctionCollection::fetchObjectTree()` | `kernel/content/function_definition.php:348` |
| `fetch( 'content', 'tree_count' )` | `eZContentFunctionCollection::fetchObjectTreeCount()` | `kernel/content/function_definition.php:434` |
| `fetch( 'content', 'search' )` | `eZContentFunctionCollection::fetchContentSearch()` | `kernel/content/function_definition.php:491` |
| `fetch( 'content', 'trash_count' )` | `eZContentFunctionCollection::fetchTrashObjectCount()` | `kernel/content/function_definition.php:544` |
| `fetch( 'content', 'trash_object_list' )` | `eZContentFunctionCollection::fetchTrashObjectList()` | `kernel/content/function_definition.php:558` |
| `fetch( 'content', 'draft_count' )` | `eZContentFunctionCollection::fetchDraftVersionCount()` | `kernel/content/function_definition.php:588` |
| `fetch( 'content', 'draft_version_list' )` | `eZContentFunctionCollection::fetchDraftVersionList()` | `kernel/content/function_definition.php:595` |
| `fetch( 'content', 'pending_count' )` | `eZContentFunctionCollection::fetchPendingCount()` | `kernel/content/function_definition.php:609` |
| `fetch( 'content', 'pending_list' )` | `eZContentFunctionCollection::fetchPendingList()` | `kernel/content/function_definition.php:616` |
| `fetch( 'content', 'version_count' )` | `eZContentFunctionCollection::fetchVersionCount()` | `kernel/content/function_definition.php:630` |
| `fetch( 'content', 'version_list' )` | `eZContentFunctionCollection::fetchVersionList()` | `kernel/content/function_definition.php:639` |
| `fetch( 'content', 'can_instantiate_class_list' )` | `eZContentFunctionCollection::canInstantiateClassList()` | `kernel/content/function_definition.php:662` |
| `fetch( 'content', 'class_list' )` | `eZContentFunctionCollection::fetchClassList()` | `kernel/content/function_definition.php:692` |
| `fetch( 'content', 'can_instantiate_classes' )` | `eZContentFunctionCollection::canInstantiateClasses()` | `kernel/content/function_definition.php:714` |
| `fetch( 'content', 'contentobject_attributes' )` | `eZContentFunctionCollection::contentobjectAttributes()` | `kernel/content/function_definition.php:723` |
| `fetch( 'content', 'bookmarks' )` | `eZContentFunctionCollection::fetchBookmarks()` | `kernel/content/function_definition.php:737` |
| `fetch( 'content', 'bookmark_rows' )` | `eZContentFunctionCollection::fetchBookmarkRows()` | `kernel/content/function_definition.php:751` |
| `fetch( 'content', 'view_bookmark' )` | `eZContentFunctionCollection::fetchViewBookmark()` | `kernel/content/function_definition.php:761` |
| `fetch( 'content', 'bookmark_folders' )` | `eZContentFunctionCollection::fetchBookmarkFolders()` | `kernel/content/function_definition.php:774` |
| `fetch( 'content', 'recent' )` | `eZContentFunctionCollection::fetchRecent()` | `kernel/content/function_definition.php:781` |
| `fetch( 'content', 'section_list' )` | `eZContentFunctionCollection::fetchSectionList()` | `kernel/content/function_definition.php:788` |
| `fetch( 'content', 'tipafriend_top_list' )` | `eZContentFunctionCollection::fetchTipafriendTopList()` | `kernel/content/function_definition.php:795` |
| `fetch( 'content', 'view_top_list' )` | `eZContentFunctionCollection::fetchMostViewedTopList()` | `kernel/content/function_definition.php:829` |
| `fetch( 'content', 'collected_info_count' )` | `eZContentFunctionCollection::fetchCollectedInfoCount()` | `kernel/content/function_definition.php:851` |
| `fetch( 'content', 'collected_info_count_list' )` | `eZContentFunctionCollection::fetchCollectedInfoCountList()` | `kernel/content/function_definition.php:877` |
| `fetch( 'content', 'collected_info_collection' )` | `eZContentFunctionCollection::fetchCollectedInfoCollection()` | `kernel/content/function_definition.php:888` |
| `fetch( 'content', 'collected_info_list' )` | `eZContentFunctionCollection::fetchCollectionsList()` | `kernel/content/function_definition.php:902` |
| `fetch( 'content', 'object_by_attribute' )` | `eZContentFunctionCollection::fetchObjectByAttribute()` | `kernel/content/function_definition.php:932` |
| `fetch( 'content', 'object_count_by_user_id' )` | `eZContentFunctionCollection::fetchObjectCountByUserID()` | `kernel/content/function_definition.php:942` |
| `fetch( 'content', 'same_classattribute_node' )` | `eZContentFunctionCollection::fetchSameClassAttributeNodeList()` | `kernel/content/function_definition.php:960` |
| `fetch( 'content', 'keyword' )` | `eZContentFunctionCollection::fetchKeyword()` | `kernel/content/function_definition.php:975` |
| `fetch( 'content', 'keyword_count' )` | `eZContentFunctionCollection::fetchKeywordCount()` | `kernel/content/function_definition.php:1021` |
| `fetch( 'content', 'assigned_nodes' )` | `eZContentFunctionCollection::fetchAssignedNodes()` | `kernel/content/function_definition.php:1061` |
| `fetch( 'content', 'assigned_node_count' )` | `eZContentFunctionCollection::fetchAssignedNodeCount()` | `kernel/content/function_definition.php:1086` |
| `fetch( 'content', 'access' )` | `eZContentFunctionCollection::checkAccess()` | `kernel/content/function_definition.php:1094` |
| `fetch( 'content', 'navigation_parts' )` | `eZContentFunctionCollection::fetchNavigationParts()` | `kernel/content/function_definition.php:1119` |
| `fetch( 'content', 'navigation_part' )` | `eZContentFunctionCollection::fetchNavigationPart()` | `kernel/content/function_definition.php:1127` |
| `fetch( 'content', 'related_objects' )` | `eZContentFunctionCollection::fetchRelatedObjects()` | `kernel/content/function_definition.php:1137` |
| `fetch( 'content', 'related_objects_count' )` | `eZContentFunctionCollection::fetchRelatedObjectsCount()` | `kernel/content/function_definition.php:1186` |
| `fetch( 'content', 'reverse_related_objects' )` | `eZContentFunctionCollection::fetchReverseRelatedObjects()` | `kernel/content/function_definition.php:1203` |
| `fetch( 'content', 'reverse_related_objects_count' )` | `eZContentFunctionCollection::fetchReverseRelatedObjectsCount()` | `kernel/content/function_definition.php:1247` |
| `fetch( 'content', 'available_sort_fields' )` | `eZContentFunctionCollection::fetchAvailableSortFieldList()` | `kernel/content/function_definition.php:1267` |
| `fetch( 'content', 'country_list' )` | `eZContentFunctionCollection::fetchCountryList()` | `kernel/content/function_definition.php:1274` |
| `fetch( 'content', 'country_groups' )` | `eZContentFunctionCollection::fetchCountryGroups()` | `kernel/content/function_definition.php:1288` |
| `fetch( 'content', 'related_objects_ids' )` | `eZContentFunctionCollection::fetchRelatedObjectsID()` | `kernel/content/function_definition.php:1298` |
| `fetch( 'content', 'reverse_related_objects_ids' )` | `eZContentFunctionCollection::fetchReverseRelatedObjectsID()` | `kernel/content/function_definition.php:1315` |
| `fetch( 'content', 'site_data' )` | `eZContentFunctionCollection::fetchSiteData()` | `kernel/content/function_definition.php:1334` |
| `fetch( 'content', 'content_tree_menu_expiry' )` | `eZContentFunctionCollection::fetchContentTreeMenuExpiry()` | `kernel/content/function_definition.php:1343` |
| `fetch( 'datatable', 'list' )` | `expDataTableFunctionCollection::fetchList()` | `kernel/datatable/function_definition.php:13` |
| `fetch( 'datatable', 'count' )` | `expDataTableFunctionCollection::fetchCount()` | `kernel/datatable/function_definition.php:23` |
| `fetch( 'datatable', 'row' )` | `expDataTableFunctionCollection::fetchRow()` | `kernel/datatable/function_definition.php:30` |
| `fetch( 'datatable', 'pointing_at' )` | `expDataTableFunctionCollection::fetchPointingAt()` | `kernel/datatable/function_definition.php:37` |
| `fetch( 'datatable', 'can' )` | `expDataTableFunctionCollection::fetchCan()` | `kernel/datatable/function_definition.php:44` |
| `fetch( 'exphelp', 'context' )` | `expHelpFunctionCollection::fetchContext()` | `kernel/exphelp/function_definition.php:16` |
| `fetch( 'exphelp', 'license' )` | `expHelpFunctionCollection::fetchLicense()` | `kernel/exphelp/function_definition.php:25` |
| `fetch( 'exphelp', 'context_menu' )` | `expHelpFunctionCollection::fetchContextMenu()` | `kernel/exphelp/function_definition.php:33` |
| `fetch( 'exphelp', 'site_topic' )` | `expHelpFunctionCollection::fetchSiteTopic()` | `kernel/exphelp/function_definition.php:45` |
| `fetch( 'exphelp', 'site_map' )` | `expHelpFunctionCollection::fetchSiteMap()` | `kernel/exphelp/function_definition.php:51` |
| `fetch( 'infocollector', 'collected_info_count' )` | `eZInfocollectorFunctionCollection::fetchCollectedInfoCount()` | `kernel/infocollector/function_definition.php:13` |
| `fetch( 'infocollector', 'collected_info_count_list' )` | `eZInfocollectorFunctionCollection::fetchCollectedInfoCountList()` | `kernel/infocollector/function_definition.php:39` |
| `fetch( 'infocollector', 'collected_info_collection' )` | `eZInfocollectorFunctionCollection::fetchCollectedInfoCollection()` | `kernel/infocollector/function_definition.php:50` |
| `fetch( 'infocollector', 'collected_info_list' )` | `eZInfocollectorFunctionCollection::fetchCollectionsList()` | `kernel/infocollector/function_definition.php:64` |
| `fetch( 'layout', 'sitedesign_list' )` | `eZLayoutFunctionCollection::fetchSitedesignList()` | `kernel/layout/function_definition.php:11` |
| `fetch( 'notification', 'handler_list' )` | `eZNotificationFunctionCollection::handlerList()` | `kernel/notification/function_definition.php:11` |
| `fetch( 'notification', 'digest_handlers' )` | `eZNotificationFunctionCollection::digestHandlerList()` | `kernel/notification/function_definition.php:18` |
| `fetch( 'notification', 'digest_items' )` | `eZNotificationFunctionCollection::digestItems()` | `kernel/notification/function_definition.php:31` |
| `fetch( 'notification', 'event_content' )` | `eZNotificationFunctionCollection::eventContent()` | `kernel/notification/function_definition.php:47` |
| `fetch( 'notification', 'subscribed_nodes' )` | `eZNotificationFunctionCollection::subscribedNodes()` | `kernel/notification/function_definition.php:56` |
| `fetch( 'notification', 'subscribed_nodes_count' )` | `eZNotificationFunctionCollection::subscribedNodesCount()` | `kernel/notification/function_definition.php:70` |
| `fetch( 'package', 'list' )` | `eZPackageFunctionCollection::fetchList()` | `kernel/package/function_definition.php:11` |
| `fetch( 'package', 'list_count' )` | `eZPackageFunctionCollection::fetchListCount()` | `kernel/package/function_definition.php:31` |
| `fetch( 'package', 'maintainer_role_list' )` | `eZPackageFunctionCollection::fetchMaintainerRoleList()` | `kernel/package/function_definition.php:43` |
| `fetch( 'package', 'can_create' )` | `eZPackageFunctionCollection::canCreate()` | `kernel/package/function_definition.php:55` |
| `fetch( 'package', 'can_edit' )` | `eZPackageFunctionCollection::canEdit()` | `kernel/package/function_definition.php:61` |
| `fetch( 'package', 'can_import' )` | `eZPackageFunctionCollection::canImport()` | `kernel/package/function_definition.php:67` |
| `fetch( 'package', 'can_install' )` | `eZPackageFunctionCollection::canInstall()` | `kernel/package/function_definition.php:73` |
| `fetch( 'package', 'can_export' )` | `eZPackageFunctionCollection::canExport()` | `kernel/package/function_definition.php:79` |
| `fetch( 'package', 'content_export_access' )` | `eZPackageFunctionCollection::contentExportAccess()` | `kernel/package/function_definition.php:87` |
| `fetch( 'package', 'can_read' )` | `eZPackageFunctionCollection::canRead()` | `kernel/package/function_definition.php:93` |
| `fetch( 'package', 'can_list' )` | `eZPackageFunctionCollection::canList()` | `kernel/package/function_definition.php:99` |
| `fetch( 'package', 'can_remove' )` | `eZPackageFunctionCollection::canRemove()` | `kernel/package/function_definition.php:105` |
| `fetch( 'package', 'item' )` | `eZPackageFunctionCollection::fetchPackage()` | `kernel/package/function_definition.php:112` |
| `fetch( 'package', 'dependent_list' )` | `eZPackageFunctionCollection::fetchDependentPackageList()` | `kernel/package/function_definition.php:124` |
| `fetch( 'package', 'repository_list' )` | `eZPackageFunctionCollection::fetchRepositoryList()` | `kernel/package/function_definition.php:139` |
| `fetch( 'role', 'role' )` | `eZRoleFunctionCollection::fetchRole()` | `kernel/role/function_definition.php:11` |
| `fetch( 'role', 'policies' )` | `eZRoleFunctionCollection::fetchRolePolicies()` | `kernel/role/function_definition.php:20` |
| `fetch( 'role', 'policy_count' )` | `eZRoleFunctionCollection::fetchRolePolicyCount()` | `kernel/role/function_definition.php:37` |
| `fetch( 'rss', 'has_export_by_node' )` | `eZRSSFunctionCollection::hasExportByNode()` | `kernel/rss/function_definition.php:14` |
| `fetch( 'rss', 'export_by_node' )` | `eZRSSFunctionCollection::exportByNode()` | `kernel/rss/function_definition.php:22` |
| `fetch( 'search', 'list_count' )` | `eZSearchFunctionCollection::fetchSearchListCount()` | `kernel/search/function_definition.php:11` |
| `fetch( 'search', 'list' )` | `eZSearchFunctionCollection::fetchSearchList()` | `kernel/search/function_definition.php:17` |
| `fetch( 'section', 'object' )` | `eZSectionFunctionCollection::fetchSectionObject()` | `kernel/section/function_definition.php:12` |
| `fetch( 'section', 'list' )` | `eZSectionFunctionCollection::fetchSectionList()` | `kernel/section/function_definition.php:25` |
| `fetch( 'section', 'object_list' )` | `eZSectionFunctionCollection::fetchObjectList()` | `kernel/section/function_definition.php:31` |
| `fetch( 'section', 'object_list_count' )` | `eZSectionFunctionCollection::fetchObjectListCount()` | `kernel/section/function_definition.php:55` |
| `fetch( 'section', 'roles' )` | `eZSectionFunctionCollection::fetchRoles()` | `kernel/section/function_definition.php:67` |
| `fetch( 'section', 'user_roles' )` | `eZSectionFunctionCollection::fetchUserRoles()` | `kernel/section/function_definition.php:75` |
| `fetch( 'setup', 'version' )` | `eZSetupFunctionCollection::fetchFullVersionString()` | `kernel/setup/function_definition.php:12` |
| `fetch( 'setup', 'alias' )` | `eZSetupFunctionCollection::fetchVersionAlias()` | `kernel/setup/function_definition.php:18` |
| `fetch( 'setup', 'major_version' )` | `eZSetupFunctionCollection::fetchMajorVersion()` | `kernel/setup/function_definition.php:24` |
| `fetch( 'setup', 'minor_version' )` | `eZSetupFunctionCollection::fetchMinorVersion()` | `kernel/setup/function_definition.php:30` |
| `fetch( 'setup', 'release' )` | `eZSetupFunctionCollection::fetchRelease()` | `kernel/setup/function_definition.php:36` |
| `fetch( 'setup', 'state' )` | `eZSetupFunctionCollection::fetchState()` | `kernel/setup/function_definition.php:42` |
| `fetch( 'setup', 'is_development' )` | `eZSetupFunctionCollection::fetchIsDevelopment()` | `kernel/setup/function_definition.php:48` |
| `fetch( 'setup', 'database_version' )` | `eZSetupFunctionCollection::fetchDatabaseVersion()` | `kernel/setup/function_definition.php:54` |
| `fetch( 'setup', 'database_release' )` | `eZSetupFunctionCollection::fetchDatabaseRelease()` | `kernel/setup/function_definition.php:63` |
| `fetch( 'setup', 'edition' )` | `eZSetupFunctionCollection::fetchEdition()` | `kernel/setup/function_definition.php:70` |
| `fetch( 'setup', 'extension_counts' )` | `eZSetupFunctionCollection::fetchExtensionCounts()` | `kernel/setup/function_definition.php:76` |
| `fetch( 'setup', 'cronjob_pauses' )` | `eZSetupFunctionCollection::fetchCronjobPauses()` | `kernel/setup/function_definition.php:86` |
| `fetch( 'setup', 'cronjob_status' )` | `eZSetupFunctionCollection::fetchCronjobStatus()` | `kernel/setup/function_definition.php:94` |
| `fetch( 'setup', 'log_files' )` | `eZSetupFunctionCollection::fetchLogFiles()` | `kernel/setup/function_definition.php:102` |
| `fetch( 'shop', 'basket' )` | `eZShopFunctionCollection::fetchBasket()` | `kernel/shop/function_definition.php:11` |
| `fetch( 'shop', 'best_sell_list' )` | `eZShopFunctionCollection::fetchBestSellList()` | `kernel/shop/function_definition.php:18` |
| `fetch( 'shop', 'related_purchase' )` | `eZShopFunctionCollection::fetchRelatedPurchaseList()` | `kernel/shop/function_definition.php:54` |
| `fetch( 'shop', 'wish_list' )` | `eZShopFunctionCollection::fetchWishList()` | `kernel/shop/function_definition.php:66` |
| `fetch( 'shop', 'wish_list_count' )` | `eZShopFunctionCollection::fetchWishListCount()` | `kernel/shop/function_definition.php:83` |
| `fetch( 'shop', 'current_wish_list' )` | `eZShopFunctionCollection::fetchCurrentWishList()` | `kernel/shop/function_definition.php:92` |
| `fetch( 'shop', 'order' )` | `eZShopFunctionCollection::fetchOrder()` | `kernel/shop/function_definition.php:99` |
| `fetch( 'shop', 'order_status_history_count' )` | `eZShopFunctionCollection::fetchOrderStatusHistoryCount()` | `kernel/shop/function_definition.php:107` |
| `fetch( 'shop', 'order_status_history' )` | `eZShopFunctionCollection::fetchOrderStatusHistory()` | `kernel/shop/function_definition.php:115` |
| `fetch( 'shop', 'currency_list' )` | `eZShopFunctionCollection::fetchCurrencyList()` | `kernel/shop/function_definition.php:123` |
| `fetch( 'shop', 'currency' )` | `eZShopFunctionCollection::fetchCurrency()` | `kernel/shop/function_definition.php:132` |
| `fetch( 'shop', 'preferred_currency_code' )` | `eZShopFunctionCollection::fetchPreferredCurrencyCode()` | `kernel/shop/function_definition.php:141` |
| `fetch( 'shop', 'user_country' )` | `eZShopFunctionCollection::fetchUserCountry()` | `kernel/shop/function_definition.php:148` |
| `fetch( 'shop', 'product_category_list' )` | `eZShopFunctionCollection::fetchProductCategoryList()` | `kernel/shop/function_definition.php:155` |
| `fetch( 'shop', 'product_category' )` | `eZShopFunctionCollection::fetchProductCategory()` | `kernel/shop/function_definition.php:163` |
| `fetch( 'uiswitch', 'toolbar' )` | `expUISwitch::fetchToolbar()` | `kernel/uiswitch/function_definition.php:13` |
| `fetch( 'url', 'list' )` | `eZURLFunctionCollection::fetchList()` | `kernel/url/function_definition.php:11` |
| `fetch( 'url', 'list_count' )` | `eZURLFunctionCollection::fetchListCount()` | `kernel/url/function_definition.php:28` |
| `fetch( 'user', 'current_user' )` | `eZUserFunctionCollection::fetchCurrentUser()` | `kernel/user/function_definition.php:11` |
| `fetch( 'user', 'is_logged_in' )` | `eZUserFunctionCollection::fetchIsLoggedIn()` | `kernel/user/function_definition.php:18` |
| `fetch( 'user', 'logged_in_count' )` | `eZUserFunctionCollection::fetchLoggedInCount()` | `kernel/user/function_definition.php:27` |
| `fetch( 'user', 'anonymous_count' )` | `eZUserFunctionCollection::fetchAnonymousCount()` | `kernel/user/function_definition.php:34` |
| `fetch( 'user', 'logged_in_list' )` | `eZUserFunctionCollection::fetchLoggedInList()` | `kernel/user/function_definition.php:41` |
| `fetch( 'user', 'logged_in_users' )` | `eZUserFunctionCollection::fetchLoggedInUsers()` | `kernel/user/function_definition.php:56` |
| `fetch( 'user', 'user_role' )` | `eZUserFunctionCollection::fetchUserRole()` | `kernel/user/function_definition.php:70` |
| `fetch( 'user', 'member_of' )` | `eZUserFunctionCollection::fetchMemberOf()` | `kernel/user/function_definition.php:80` |
| `fetch( 'user', 'has_access_to' )` | `eZUserFunctionCollection::hasAccessTo()` | `kernel/user/function_definition.php:89` |
| `fetch( 'user', 'can_open' )` | `eZUserFunctionCollection::canOpen()` | `kernel/user/function_definition.php:108` |
| `fetch( 'user', 'account_status' )` | `eZUserFunctionCollection::fetchAccountStatus()` | `kernel/user/function_definition.php:121` |
| `fetch( 'user', 'deactivation_offers' )` | `eZUserFunctionCollection::fetchDeactivationOffers()` | `kernel/user/function_definition.php:132` |
| `fetch( 'workflow', 'workflow_statuses' )` | `eZWorkflowFunctionCollection::fetchWorkflowStatuses()` | `kernel/workflow/function_definition.php:12` |
| `fetch( 'workflow', 'workflow_type_statuses' )` | `eZWorkflowFunctionCollection::fetchWorkflowTypeStatuses()` | `kernel/workflow/function_definition.php:19` |

## Ereignis-Hooks (8)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `content/cache/version` | notify | `kernel/content/attribute_edit.php:325` |
| `content/publish` | notify | `kernel/content/ezcontentoperationcollection.php:277` |
| `content/notification/create` | filter | `kernel/content/ezcontentoperationcollection.php:983` |
| `content/cache` | filter | `kernel/content/ezcontentoperationcollection.php:1481` |
| `content/remove` | notify | `kernel/content/ezcontentoperationcollection.php:1490` |
| `content/cache` | notify | `kernel/content/ezcontentoperationcollection.php:1604` |
| `content/state/assign` | notify | `kernel/content/ezcontentoperationcollection.php:2117` |
| `content/cache/version` | notify | `kernel/content/multiedit_functions.php:496` |
