---
title: Muster in kernel/common
audience: developer
category: standards
order: 525
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/common mit den Entwurfsmustern.
keywords: kernel/common, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/common

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/common` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (26)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expContentSecurityPolicyOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/expcontentsecuritypolicyoperator.php:21` | Der Template-Operator csp_nonce(): die Nonce der Content Security Policy dieser Anfrage, für ein Inline-Skript, das ein Template nicht in eine Datei verschieben kann… |
| `expDemoModeOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/expdemomodeoperator.php:21` | Der Template-Operator exp_demo_mode(): true, solange der Demo-Modus aktiv ist (die Umgebungsvariable EXP_DEMO_MODE=1, expDemoMode). |
| `expEnvironmentOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/expenvironmentoperator.php:29` | – |
| `expKernelTransferOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/expkerneltransferoperator.php:29` | Die Template-Operatoren der Transfer-Registry (expKernelTransferRegistry; doc/eng-us/features/6.0/transfer-registry.md). |
| `expViewParameterOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/expviewparameteroperator.php:21` | Der Template-Operator view_parameter: ein Wert als Wert eines View-Parameters, /(name)/, so geschrieben, wie expViewParameters::encode() ihn schreibt („~2F“ für… |
| `eZAlphabetOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/ezalphabetoperator.php:19` | – |
| `eZAutoLinkOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/ezautolinkoperator.php:12` | – |
| `eZContentStructureTreeOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/ezcontentstructuretreeoperator.php:17` | – |
| `eZDateOperatorCollection` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/ezdateoperatorcollection.php:12` | – |
| `eZi18nOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/ezi18noperator.php:18` | – |
| `eZKernelOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/ezkerneloperator.php:17` | – |
| `eZModuleOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/ezmoduleoperator.php:19` | – |
| `eZModuleParamsOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/ezmoduleparamsoperator.php:32` | – |
| `eZObjectForwarder` | class | [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | `kernel/common/ezobjectforwarder.php:18` | – |
| `eZPackageOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/ezpackageoperator.php:18` | – |
| `ezpI18n` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/common/ezpi18n.php:12` | – |
| `ezpSiteAccessMenuOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/ezpsiteaccessmenuoperator.php:24` | Template-Operatoren des Site-Menüs der Administrationsoberfläche (ezpSiteAccessMenu): |
| `ezpSiteAccessURLOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/ezpsiteaccessurloperator.php:22` | Template-Operator siteaccess_url: die Adresse der Startseite eines Siteaccess (ezpSiteAccessURL::root()), mit Schema, Host, Port und Siteaccess-Pf… |
| `eZSimpleTagsOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/ezsimpletagsoperator.php:12` | – |
| `eZTemplateDesignResource` | class extends eZTemplateFileResource | [Singleton / instance()](singleton.md) | `kernel/common/eztemplatedesignresource.php:18` | – |
| `eZTemplatesStatisticsReporter` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/common/eztemplatesstatisticsreporter.php:19` | – |
| `eZTOCOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/eztocoperator.php:18` | – |
| `eZTopMenuOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/eztopmenuoperator.php:19` | – |
| `eZTreeMenuOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/eztreemenuoperator.php:12` | – |
| `eZURLOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/ezurloperator.php:18` | – |
| `eZWordToImageOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `kernel/common/ezwordtoimageoperator.php:17` | – |

## Template-Operatoren (24)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| ezurl, ezroot, ezdesign, ezimage, exturl, ezsys, ezhttp, ezhttp_hasvariable, ezini, ezini_hasvariable | `eZURLOperator` | `kernel/common/eztemplateautoload.php:14` |
| i18n, x18n, d18n | `eZi18nOperator` | `kernel/common/eztemplateautoload.php:18` |
| alphabet | `eZAlphabetOperator` | `kernel/common/eztemplateautoload.php:21` |
| month_overview | `eZDateOperatorCollection` | `kernel/common/eztemplateautoload.php:24` |
| autolink | `eZAutoLinkOperator` | `kernel/common/eztemplateautoload.php:27` |
| simpletags | `eZSimpleTagsOperator` | `kernel/common/eztemplateautoload.php:30` |
| treemenu | `eZTreeMenuOperator` | `kernel/common/eztemplateautoload.php:33` |
| content_structure_tree | `eZContentStructureTreeOperator` | `kernel/common/eztemplateautoload.php:36` |
| wordtoimage, mimetype_icon, class_icon, classgroup_icon, action_icon, icon, flag_icon, icon_info | `eZWordToImageOperator` | `kernel/common/eztemplateautoload.php:39` |
| ezpreference | `eZKernelOperator` | `kernel/common/eztemplateautoload.php:44` |
| siteaccess_url | `ezpSiteAccessURLOperator` | `kernel/common/eztemplateautoload.php:47` |
| siteaccess_menu, siteaccess_menu_key | `ezpSiteAccessMenuOperator` | `kernel/common/eztemplateautoload.php:50` |
| module_params | `eZModuleParamsOperator` | `kernel/common/eztemplateautoload.php:57` |
| topmenu | `eZTopMenuOperator` | `kernel/common/eztemplateautoload.php:60` |
| ezpackage | `eZPackageOperator` | `kernel/common/eztemplateautoload.php:63` |
| eztoc | `eZTOCOperator` | `kernel/common/eztemplateautoload.php:66` |
| ezmodule | `eZModuleOperator` | `kernel/common/eztemplateautoload.php:69` |
| installation_name, is_production_system | `ExpInstallationOperator` | `kernel/common/eztemplateautoload.php:72` |
| audit_label | `expAuditTemplateOperator` | `kernel/common/eztemplateautoload.php:75` |
| exp_environment, exp_environment_badge | `expEnvironmentOperator` | `kernel/common/eztemplateautoload.php:78` |
| exp_demo_mode | `expDemoModeOperator` | `kernel/common/eztemplateautoload.php:81` |
| csp_nonce | `expContentSecurityPolicyOperator` | `kernel/common/eztemplateautoload.php:84` |
| view_parameter | `expViewParameterOperator` | `kernel/common/eztemplateautoload.php:87` |
| transfer_tools, transfer_actions, transfer_related, transfer_tool | `expKernelTransferOperator` | `kernel/common/eztemplateautoload.php:90` |
