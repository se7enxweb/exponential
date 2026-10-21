---
title: Aufbau einer Erweiterung
audience: app-creator, developer
category: app-creation
order: 70
summary: Jedes Verzeichnis und jede Datei, die eine Erweiterung haben kann - Module, Klassen, Autoloads, Design, Einstellungen, Datentypen, Ereignistypen, Cronjobs, bin, share, sql, Übersetzungen, doc - dazu extension.xml, ezinfo.php und composer.json und was jedes davon registriert.
keywords: Aufbau einer Erweiterung, Struktur einer Erweiterung, Verzeichnisse, Module, Klassen, Design, Einstellungen, Datentypen, Ereignistypen, Übersetzungen, Anwendungsentwickler, extension anatomy, extension structure, directories, extension.xml, ezinfo.php, composer.json, modules, classes, design, settings, datatypes, eventtypes, cronjobs, translations, app creator
type: help
---
# Aufbau einer Erweiterung

Diese Seite ist für Anwendungsentwickler, die wissen möchten, was in einer Erweiterung wohin gehört. Eine Erweiterung
ist ein Verzeichnis unter `extension/` (oder in einem zusätzlichen Erweiterungsverzeichnis); der Kernel findet ihre
Teile über Verzeichnisnamen und über die Einstellungen, die die Erweiterung mitbringt
([Erweiterungen](../../../anleitungen/extensions.md)).

![Die Seite "Über" der Administration, auf der Version und Lizenz einer Erweiterung erscheinen](../../../../eng-us/help/images/creators/about-extensions.webp)

## Inhalt

1. [Die Verzeichnisstruktur](#die-verzeichnisstruktur)
2. [Metadatendateien](#metadatendateien)
3. [Aktivierung](#aktivierung)
4. [Ladereihenfolge](#ladereihenfolge)
5. [Checkliste für eine neue Erweiterung](#checkliste-für-eine-neue-erweiterung)

## Die Verzeichnisstruktur

| Verzeichnis | Enthält | Registriert durch |
|---|---|---|
| `modules/<module>/` | `module.php`, Skripte der Ansichten, `function_definition.php` | `module.ini [ModuleSettings] ExtensionRepositories[]`, `ModuleList[]` |
| `classes/` | PHP-Klassen | `ezpgenerateautoloads.php -e` schreibt `autoload/ezp_extension.php` |
| `autoloads/` | Registrierung von Template-Operatoren (`eztemplateautoload.php`) | `site.ini [TemplateSettings] ExtensionAutoloadPath[]` |
| `design/<design>/` | Templates, Override-Templates, Stylesheets, Bilder, Skripte | `design.ini [ExtensionSettings] DesignExtensions[]` |
| `settings/` | `*.ini.append.php` für jeden Siteaccess; `siteaccess/<name>/` pro Siteaccess; `override/` | die Aktivierung |
| `datatypes/<name>/` | Datentyp-Klassen | `content.ini [DataTypeSettings] ExtensionDirectories[]`, `AvailableDataTypes[]` |
| `eventtypes/event/<name>/` | Workflow-Ereignistypen | `workflow.ini [EventSettings] ExtensionDirectories[]`, `AvailableEventTypes[]` |
| `cronjobs/` | Cronjob-Skripte | `cronjob.ini [CronjobSettings] ExtensionDirectories[]` und ein `[CronjobPart-<name>]` |
| `bin/php/` | Kommandozeilenskripte | von der Konsole als `ext:<ext>:<name>` gefunden |
| `share/`, `sql/<engine>/` | Datenbankschema und Daten | installiert durch Ihr Setup-Skript oder das Paket |
| `translations/<locale>/translation.ts` | Übersetzungen | `site.ini [RegionalSettings] TranslationExtensions[]` |
| `doc/` | die eigene Dokumentation der Erweiterung | die Hilfe kann sie lesen, wenn sie in `exphelp.ini` unter `ExtensionDocDirs[]` steht |
| `tests/` | PHPUnit-Tests | Ihre CI ([Anwendungen testen](anwendungen-testen.md)) |

Der tatsächliche Katalog jedes Erweiterungspunkts, mit der INI-Zeile, die ihn registriert, ist **Setup > RAD**
([Erweiterungspunkte](../../../bc/6.0/rad-erweiterungspunkte.md)).

## Metadatendateien

| Datei | Inhalt |
|---|---|
| `extension.xml` | `<metadata>` mit `name`, `version`, `copyright`, `license`, `info_url`; `<dependencies>` mit `<requires><extension name="..."/></requires>` |
| `ezinfo.php` | Klasse `<extension>Info` mit einer statischen Methode `info()`, die `Name`, `Version`, `Copyright`, `License`, `Info_url` zurückgibt |
| `composer.json` | das Composer-Paket, Typ `ezpublish-legacy-extension`, Lizenz, Anforderungen |

Die Seite "Über" (`/ezinfo/about`) liest zuerst `extension.xml`, daher müssen beide Dateien dieselbe Version angeben
([Seiten "Über" und Pakete](../../../funktionen/6.0/info-und-paketseiten.md),
[Versionen und Releases](versionen-und-releases.md)).

## Aktivierung

| Einstellung | Wirkung |
|---|---|
| `site.ini [ExtensionSettings] ActiveExtensions[]` | überall aktiv, geladen, bevor der Siteaccess bekannt ist |
| `ActiveAccessExtensions[]` (in einem Siteaccess) | nur in diesem Siteaccess aktiv |

Verwenden Sie `ActiveExtensions[]` für alles, was ein Kommandozeilenskript oder Cronjob sehen muss; eine Aktivierung pro
Siteaccess bedeutet, dass jedes Skript `-s <siteaccess>` braucht.

## Ladereihenfolge

Erweiterungen werden in der Reihenfolge ihrer Aktivierung gelesen (mit `ExtensionOrdering=enabled` werden die
Abhängigkeiten aus `extension.xml` berücksichtigt). Die Reihenfolge entscheidet, welches Template oder welche
Einstellung welcher Erweiterung gewinnt. Ändern Sie sie unter **Setup > Extensions**
([Ladereihenfolge der Erweiterungen](../../../funktionen/6.0/ladereihenfolge-der-extensions.md)).

## Checkliste für eine neue Erweiterung

- [ ] Name in Kleinbuchstaben, neue Namen beginnen mit `exp` (Klassen `exp<Name>...`).
- [ ] `extension.xml`, `ezinfo.php` und `composer.json` stimmen in Name, Version und Lizenz überein.
- [ ] Einstellungen in `settings/*.ini.append.php`, nie im `settings/` des Kernels.
- [ ] Autoloads nach jeder neuen Klasse neu erzeugt.
- [ ] Keine Geheimnisse im Repository; Schlüssel gehören nach `settings/override/` der Installation.

## Verwandte Seiten

- [Ihre erste Erweiterung](ihre-erste-erweiterung.md)
- [Ihre Anwendung als Erweiterung paketieren](als-erweiterung-paketieren.md)
- [Schichten und Benennung](../../../standards/architektur/schichten-und-benennung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
