---
title: Architektur für Anwendungsentwickler
audience: app-creator, developer
category: app-creation
order: 40
summary: Wie eine Anfrage durch Exponential läuft - Front-Controller, Siteaccess, Modul und Ansicht, Templates und Pagelayout - und wo Kernel, Erweiterungen, Einstellungen, Designs und var/ ihren Platz haben, damit Sie wissen, wohin Ihr Code gehört.
keywords: Architektur, Ablauf einer Anfrage, Front-Controller, Siteaccess, Modul, Ansicht, Template, Pagelayout, Erweiterung, Einstellungen, Anwendungsentwickler, architecture, request flow, index.php, ezpKernelWeb, siteaccess, module, view, eZModule, template, pagelayout, extension, settings, INI, var, autoload, app creator
type: help
---
# Architektur für Anwendungsentwickler

Diese Seite ist für Anwendungsentwickler, die die Landkarte kennen möchten, bevor sie Code schreiben: was bei einer
Anfrage passiert, welcher Teil der Installation was tut und wohin der Code einer Anwendung gehört. Der Bereich Standards
erklärt die Architektur des Kernels ausführlich ([Kernel-Architektur](../../../../eng-us/standards/architecture/kernel-architecture.md)).

## Inhalt

1. [Die Teile einer Installation](#die-teile-einer-installation)
2. [Eine Anfrage, Schritt für Schritt](#eine-anfrage-schritt-für-schritt)
3. [Wohin Ihr Code gehört](#wohin-ihr-code-gehört)
4. [Einstellungen und ihre Reihenfolge](#einstellungen-und-ihre-reihenfolge)
5. [Kommandozeile und Hintergrundarbeit](#kommandozeile-und-hintergrundarbeit)

## Die Teile einer Installation

Aus [1.2 Die Teile einer Installation](../../../installation/01-einfuehrung.md#12-die-bestandteile-einer-installation):

```text
kernel/, lib/      the kernel: modules, classes, the template engine, the database layer
autoload/          the kernel's class map (autoload/ezp_kernel.php)
extension/         extensions: your application lives here
design/            designs: templates, stylesheets, images
settings/          shipped settings; settings/siteaccess/ and settings/override/ hold yours
var/               runtime: caches, logs, storage, the SQLite database
bin/php/console    one entry point for every command
cronjobs/          the scripts runcronjobs.php runs
index.php          the front controller
```

## Eine Anfrage, Schritt für Schritt

1. **`index.php`** startet den Web-Kernel (`ezpKernelWeb`).
2. **Siteaccess**: Die Adresse wird mit `site.ini [SiteAccessSettings] MatchOrder` abgeglichen; die Einstellungen des
   Siteaccess werden geladen ([Siteaccesses](../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).
3. **URL-Übersetzung**: Ein URL-Alias wird in eine Moduladresse umgewandelt (`/About-us` zu `content/view/full/58`).
4. **Modul und Ansicht**: `eZModule` findet das Modul in `ModuleRepositories[]` und in den Modulverzeichnissen der
   Erweiterungen, prüft die Richtlinienfunktionen der Ansicht und führt das Skript der Ansicht aus, das ein `$Result`
   zurückgibt ([Eigene Module und Ansichten](eigene-module-und-ansichten.md)).
5. **Templates**: Die Ansicht rendert ein Template, das über die Design-Kette gefunden wird; das Ergebnis landet in
   `pagelayout.tpl` ([Ergebnisse von Ansichten, Templates und das Pagelayout](ergebnisse-von-ansichten-und-das-pagelayout.md)).
6. **Caches** kürzen das meiste davon bei wiederholten Anfragen ab: der View-Cache für Inhalte, Cache-Blöcke, der
   Antwort-Cache von Velocity.

## Wohin Ihr Code gehört

| Code | Ort |
|---|---|
| Adressen und Bildschirme | `extension/<ext>/modules/<module>/` |
| Geschäftslogik | PHP-Klassen in `extension/<ext>/classes/`, benannt `exp<Ext><Thing>` |
| Templates, Stylesheets, Skripte | `extension/<ext>/design/<design>/` |
| Einstellungen | `extension/<ext>/settings/*.ini.append.php` |
| Neue Feldtypen | `extension/<ext>/datatypes/` |
| Prozessschritte | `extension/<ext>/eventtypes/event/` |
| Geplante Arbeit | `extension/<ext>/cronjobs/` plus ein Teil in `cronjob.ini` |
| Befehle | `extension/<ext>/bin/php/` (Konsolenname `ext:<ext>:<name>`) |
| Tabellen | Schemadateien in `extension/<ext>/share/` oder `sql/` |

Bearbeiten Sie niemals `kernel/`, `lib/` oder eine andere Erweiterung direkt: Ein Update ersetzt sie. Nutzen Sie zuerst
die registrierten Erweiterungspunkte; ein Override einer Kernel-Klasse ist das letzte Mittel
([Erweiterungspunkte](../../../bc/6.0/rad-erweiterungspunkte.md)).

## Einstellungen und ihre Reihenfolge

Ein INI-Wert wird aus `settings/<file>.ini` gelesen, dann aus `settings/` der aktiven Erweiterungen, dann aus
`settings/siteaccess/<siteaccess>/` (und den Siteaccess-Verzeichnissen der Erweiterungen), dann aus `settings/override/`;
der letzte gewinnt. `./console exp:ini where <file>/<block>/<key> <siteaccess>` zeigt jede Datei, die einen Wert setzt
([Einstellungen und INI-Dateien](einstellungen-und-ini-dateien.md)).

## Kommandozeile und Hintergrundarbeit

Dieselben Klassen laufen auch außerhalb einer Web-Anfrage: Konsolenbefehle (`bin/php/console`), Cronjob-Teile
(`runcronjobs.php`) und Worker der Warteschlange (`expqueue`). Skripte, Teile und Ansichten des Kernels sind inzwischen
Klassen (`Exponential\Runnable\Command`, `CronjobPart`, `ModuleView`), und Ihre eigenen können es auch sein
([Befehle, Cronjob-Teile und Modulansichten als Klassen](../../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)).

## Verwandte Seiten

- [Exponential als Anwendungsplattform](exponential-als-anwendungsplattform.md)
- [Aufbau einer Erweiterung](aufbau-einer-erweiterung.md)
- [Schichten und Benennung](../../../../eng-us/standards/architecture/layering-and-naming.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
