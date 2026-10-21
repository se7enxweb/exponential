---
title: INI-Dateien, denen ein Erweiterungsautor begegnet
audience: developer
category: extension-development
order: 300
summary: Welche INI-Datei und welchen Abschnitt des Kernels eine Erweiterung für welchen Zweck ergänzt - site, module, content, workflow, cronjob, design, override, ezjscore, menu, admininterface, rad - und wie Append-Dateien funktionieren.
keywords: INI-Dateien, Einstellungen, Append-Dateien, Erweiterungseinstellungen, INI files, site.ini, module.ini, content.ini, workflow.ini, cronjob.ini, design.ini, override.ini, ezjscore.ini, menu.ini, admininterface.ini, rad.ini, i18n, append.php, extension settings
type: help
---
# INI-Dateien, denen ein Erweiterungsautor begegnet

Teil 8, Referenz 26. Die Einstellungen des Kernels liegen in `settings/*.ini` (etwa 70 Dateien). Eine Erweiterung
bearbeitet sie nie; sie fügt eigene Dateien `settings/<file>.ini.append.php` hinzu, die der Kernel für jede aktive
Erweiterung einmischt ([Einstellungen in einer Erweiterung](../teil-3-erweiterungsentwickler/einstellungen-in-einer-erweiterung.md)).
Diese Seite führt nach Zweck die Dateien und Abschnitte auf, die Erweiterungen am häufigsten ergänzen.

## Das Dateiformat der Einstellungen einer Erweiterung

```php
<?php /* #?ini charset="utf-8"?

[ModuleSettings]
ExtensionRepositories[]=myext

*/ ?>
```

Die Hülle aus einem PHP-Kommentar verhindert, dass die Datei als Text ausgeliefert wird. `Name[]=value` hängt an eine
Liste an; eine leere Zeile `Name[]` davor leert sie zuerst.

## Nach Zweck

| Zweck | Datei | Abschnitt und Einstellungen |
|---|---|---|
| Erweiterungen aktivieren | `site.ini` | `[ExtensionSettings] ActiveExtensions[]` (in `settings/override`), `ActiveAccessExtensions[]` (je Siteaccess) |
| Zusätzliche Erweiterungsverzeichnisse | `site.ini` | `[ExtensionSettings] AdditionalExtensionDirectories[]` |
| Ereignis-Listener und Filter | `site.ini` | `[Event] Listeners[]` |
| Template-Operatoren und -Funktionen | `site.ini` | `[TemplateSettings] ExtensionAutoloadPath[]` |
| Übersetzungen | `site.ini` | `[RegionalSettings] TranslationExtensions[]` |
| E-Mail | `site.ini` | `[MailSettings]` (die der Website; lesen Sie sie, setzen Sie sie nicht) |
| Module | `module.ini` | `[ModuleSettings] ExtensionRepositories[]`, `ModuleList[]` |
| Datentypen | `content.ini` | `[DataTypeSettings] ExtensionDirectories[]`, `AvailableDataTypes[]` |
| Benutzerdefinierte Tags | `content.ini` | `[CustomTagSettings] AvailableCustomTags[]` und ein Abschnitt je Tag |
| Workflow-Ereignistypen | `workflow.ini` | `[EventSettings] ExtensionDirectories[]`, `AvailableEventTypes[]` |
| Cronjob-Teile | `cronjob.ini` | `[CronjobSettings] ExtensionDirectories[]`, `[CronjobPart-<name>] Scripts[]` |
| Designs | `design.ini` | `[ExtensionSettings] DesignExtensions[]` |
| Stylesheets und Skripte | `design.ini` | `[StylesheetSettings] FrontendCSSFileList[]`, `BackendCSSFileList[]`; `[JavaScriptSettings] FrontendJavaScriptList[]`, `BackendJavaScriptList[]` |
| Template-Overrides | `override.ini` | ein Abschnitt je Regel (`Source`, `MatchFile`, `Subdir`, `Match[...]`) |
| Ajax-Serverfunktionen | `ezjscore.ini` | `[ezjscServer_<name>]` |
| Administrationsmenüs | `menu.ini` | `[Leftmenu_<name>] Links[]`, `LinkNames[]`, `PolicyList_<key>[]` |
| Kontextmenüs und Knoten-Reiter | `admininterface.ini` | `[AdditionalMenuSettings] ContextMenuTemplateArray[]` ...; `[WindowControlsSettings] AdditionalTabs[]` und `[AdditionalTab_<id>]` |
| Register des RAD-Katalogs | `rad.ini` | `[RegistrySettings]` und `[Registry_<name>]` |
| Fetch-Aliase | `fetchalias.ini` | ein Abschnitt je Alias |
| Hilfeseiten | `exphelp.ini` | der Dokumentationsordner der Erweiterung ([Das Hilfesystem für Erweiterungen](hilfesystem-fuer-erweiterungen.md)) |
| Ihre eigenen Einstellungen | `myext.ini` | in `settings/myext.ini`, Ihre eigenen Abschnitte |

## Weitere Dateien, die Sie lesen könnten

| Datei | Enthält |
|---|---|
| `image.ini` | Bildvarianten (Größen) und den Bild-Handler |
| `file.ini` | den Cluster-Datei-Handler |
| `viewcache.ini` | welche Views beim Veröffentlichen geleert werden |
| `i18n.ini` | Zeichensatz- und Übersetzungseinstellungen |
| `template.ini` | Einstellungen der Template-Engine (wash, auto-link) |
| `debug.ini` | Bedingungen für die Debug-Ausgabe |
| `rest.ini` | die Provider und Filter der REST-Schnittstelle |
| `velocity.ini` | den Anwendungsserver, wenn er verwendet wird |

## Regeln

- Ihre eigenen Einstellungen gehören in Ihre eigene Datei, nie in Abschnitte einer Kernel-Datei, die Sie nicht
  brauchen.
- Schlüssel und Abschnittsnamen unterscheiden Groß- und Kleinschreibung.
- Leeren Sie nach einer Änderung von Einstellungen den INI-Cache mit `--clear-tag=ini`.
- Liefern Sie in einer Erweiterung nie Dateien aus `settings/override` oder Passwörter aus.

## Verwandte Seiten

- Zurück: [Eingebaute Fetch-Funktionen nach Modul](referenz-der-fetch-funktionen.md)
- Weiter: [Konsolenbefehle für Entwickler](konsolenbefehle-fuer-entwickler.md)
- [eZINI: Einstellungen lesen](ezini.md)
- [Wie INI-Einstellungen funktionieren](../teil-2-builder/wie-ini-einstellungen-funktionieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
