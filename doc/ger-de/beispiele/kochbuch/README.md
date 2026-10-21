---
title: Kochbuch
audience: developer, admin
category: examples
order: 1
summary: Durchgearbeitete Beispiele für die häufigen Aufgaben beim Aufbauen auf Exponential, vom Erweiterungsgerüst über einen eigenen Datentyp und einen Cronjob bis zum Deployment mit Velocity, jeweils am Code dieser Version geprüft.
keywords: Kochbuch, Beispiele, Anleitung, Rezept, Erweiterung, Inhaltsklasse, Template-Override, Template-Operator, Datentyp, Modul, Ansicht, Workflow-Ereignis, Cronjob, REST, JSON, ezjscore, Siteaccess, Multisite, Übersetzungen, Caching, Velocity, Deployment, cookbook, examples, how to, recipe, extension, content class, template override, template operator, datatype, module, view, workflow event, cronjob, siteaccess, translations, caching, deploy
type: example
---
# Kochbuch

Jede Seite des Kochbuchs baut eine Sache von Anfang bis Ende: die Dateien, die Sie schreiben, die Einstellungen, die
sie aktivieren, den Befehl, der sie prüft, und das, was am häufigsten schiefgeht. Die Seiten bauen über eine
Beispielerweiterung, **expexample**, aufeinander auf, sodass der Code einer Seite auf der nächsten weiterverwendet
wird. Lesen Sie zuerst [Ein Erweiterungsgerüst](erweiterungsgeruest.md), wenn Sie noch nie eine Erweiterung erstellt
haben.

Jedes Beispiel wurde anhand des Codes dieser Version geschrieben und geprüft, wo das mit wenig Aufwand möglich ist:
PHP-Dateien mit `php -l`, Templates mit `bin/php/eztemplatecheck.php`, XML-Dateien mit `xmllint`. Jede Seite nennt,
was geprüft wurde, und führt die Quelldateien auf, aus denen sie abgeleitet ist. Nichts davon wurde gegen eine
Produktivdatenbank ausgeführt; probieren Sie jedes Beispiel zuerst an einer Kopie Ihrer Website aus.

## Die Rezepte

| Aufgabe | Seite | Was sie berührt |
|---|---|---|
| Eine eigene Erweiterung beginnen | [Ein Erweiterungsgerüst](erweiterungsgeruest.md) | `extension/<name>/`, `ezinfo.php`, `extension.xml`, `composer.json`, `ActiveExtensions[]`, das Autoload-Array |
| Eine Inhaltsklasse anlegen | [Eine Inhaltsklasse anlegen](inhaltsklasse.md) | Setup > Klassen, oder `eZContentClass` und `eZContentClassAttribute` in einem Skript |
| Ändern, wie eine Seite oder Klasse aussieht | [Ein Template-Override](template-override.md) | `override.ini.append.php`, `design/<design>/override/templates/`, der Override-Cache |
| Einen Template-Operator hinzufügen | [Ein eigener Template-Operator](eigener-template-operator.md) | `autoloads/eztemplateautoload.php`, `[TemplateSettings] ExtensionAutoloadPath[]` |
| Eine Seite mit eigener Adresse und Richtlinie hinzufügen | [Ein eigenes Modul mit Ansicht](eigenes-modul-mit-ansicht.md) | `modules/<module>/module.php`, `module.ini [ModuleSettings]`, `$FunctionList`, Rollen und Richtlinien |
| Eine neue Art von Feld speichern | [Ein eigener Datentyp](eigener-datentyp.md) | `datatypes/<type>/<type>type.php`, `content.ini [DataTypeSettings]`, die Templates des Datentyps |
| Code ausführen, wenn Inhalte veröffentlicht werden | [Ein Workflow-Ereignis](workflow-ereignis.md) | `eventtypes/event/<type>/`, `workflow.ini [EventSettings]`, Setup > Workflows und Auslöser |
| Code nach Zeitplan ausführen | [Ein Cronjob](cronjob.md) | `cronjobs/`, `cronjob.ini [CronjobSettings]` und `[CronjobPart-<part>]`, die Crontab |
| Inhalte als JSON aus einem Skript oder dem Browser lesen | [REST- und JSON-Aufrufe](rest-json-abruf.md) | ezjscore-Serverfunktionen, `ezjscore.ini [ezjscServer_<name>]`, die REST-API unterhalb von `/api/` |
| Mehrere Websites aus einer Installation bedienen | [Mehrere Websites: Siteaccesses](multisite-siteaccess.md) | `site.ini [SiteAccessSettings]`, `settings/siteaccess/<name>/`, `VarDir`, `CacheVarDir` |
| Die Texte einer Erweiterung übersetzen | [Übersetzungen](uebersetzungen.md) | `translations/<locale>/translation.ts`, `ezpI18n::tr()`, `\|i18n`, `TranslationExtensions[]` |
| Seiten schnell machen und aktuell halten | [Caching](caching.md) | der View-Cache, `cache-block`, `ezcache.php`, der Response-Cache von Velocity |
| Eine PHP-Änderung live schalten | [Deployment mit Velocity](velocity-deployment.md) | `exp:velocity deploy` und seine Schritte |

## Die Beispielerweiterung im Überblick

```text
extension/expexample/
├── ezinfo.php                         what the extension is (Setup > About, Setup > Extensions)
├── extension.xml                      the same, read first; the same version number
├── composer.json                      when it is installed with Composer
├── autoloads/                         template operators (custom-template-operator.md)
├── classes/                           classes found through the autoload array (rest-json-fetch.md)
├── cronjobs/                          cronjob scripts (cronjob.md)
├── datatypes/expcolor/                a datatype (custom-datatype.md)
├── eventtypes/event/explogpublish/    a workflow event (workflow-event.md)
├── modules/expexample/                a module with two views (custom-module-view.md)
├── design/standard/templates/         its templates; design/standard/javascript/ its scripts
├── translations/ger-DE/               its German texts (translations.md)
└── settings/                          *.ini.append.php: what switches each part on
```

Die Namen folgen der Konvention des Projekts für neuen Code: Die Erweiterung, ihr Modul und ihre Klassen beginnen mit
`exp` (`expexample`, `expExample...`), niemals mit `ez`.

## Referenzmaterial

Das Kochbuch zeigt, wie es geht; die Referenzbereiche listen auf, was es gibt, aus dem Code erzeugt:

- [Template-Operatoren](../template-operatoren/README.md) und [Fetch-Funktionen](../fetch-funktionen/README.md)
- [Modulansichten](../modulansichten/README.md) und [Richtlinienfunktionen](../richtlinien/README.md)
- [INI-Einstellungen](../ini-einstellungen/README.md), [Datentypen](../datentypen/README.md),
  [Workflow-Ereignisse](../workflow-ereignisse/README.md), [Cronjob-Teile](../cronjobs/README.md)
- [Befehle](../befehle/README.md) und [Erweiterungspunkte](../erweiterungspunkte/README.md)

Die Programmierregeln und der Musterkatalog (wie Code, Templates und Dokumentation in diesem Projekt geschrieben
werden) stehen in [den Standards](../../../eng-us/standards/README.md); das Kochbuch folgt ihnen und wiederholt sie
nicht.

## Verwandte Seiten

- [Beispiele](../README.md): alle Beispielbereiche, einschließlich der Webserver-Konfigurationen.
- [Erweiterungen: finden, installieren, konfigurieren, bauen und veröffentlichen](../../anleitungen/extensions.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [RAD-Werkzeuge](../../../eng-us/features/6.0/rad-tools.md): die Assistenten, die die meisten dieser Gerüste für Sie
  schreiben.
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf frei kopiert, übersetzt und verbessert
  werden.
