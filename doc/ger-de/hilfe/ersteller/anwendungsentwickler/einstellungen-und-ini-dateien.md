---
title: Einstellungen und INI-Dateien
audience: app-creator, developer
category: app-creation
order: 80
summary: Ihrer Anwendung Einstellungen auf die Art von Exponential geben - eine eigene INI-Datei mit Standardwerten, Overrides je Siteaccess und Installation, Lesen mit eZINI, Ändern mit exp:ini, Caches und Geheimnisse außerhalb von Git.
keywords: Einstellungen, INI, Override, Siteaccess, Standardwerte, Geheimnisse, Konfiguration, Anwendungsentwickler, settings, eZINI, ini.append.php, override, siteaccess, exp:ini, defaults, secrets, configuration, hasVariable, variable, app creator
type: help
---
# Einstellungen und INI-Dateien

Diese Seite richtet sich an Anwendungsentwickler, die ihre Anwendung konfigurierbar machen. Die Einstellungen von
Exponential sind INI-Dateien in Schichten; Ihre Erweiterung liefert ihre Standardwerte aus, und Installationen
überschreiben sie, ohne Ihren Code anzufassen.

## Inhalt

1. [Ihre eigene INI-Datei](#ihre-eigene-ini-datei)
2. [Die Schichten](#die-schichten)
3. [Einstellungen in PHP und Templates lesen](#einstellungen-in-php-und-templates-lesen)
4. [Einstellungen ändern](#einstellungen-ändern)
5. [Geheimnisse](#geheimnisse)
6. [Caches](#caches)

## Ihre eigene INI-Datei

Liefern Sie Standardwerte in `extension/expdemo/settings/expdemo.ini` aus (einer eigenen neuen Datei) und dokumentieren
Sie dort jeden Schlüssel:

```ini
#?ini charset="utf-8"?
# Settings of the expdemo extension.

[ListSettings]
# How many items the latest view shows when no limit is given
DefaultLimit=10
# Class identifiers listed
ClassList[]
ClassList[]=article
```

Änderungen an anderen INI-Dateien (`site.ini`, `module.ini`, `content.ini` ...) gehören in `*.ini.append.php`-Dateien
Ihrer Erweiterung, die den Inhalt in einen PHP-Kommentar einschließen, damit der Webserver sie nie anzeigt:

```ini
<?php /* #?ini charset="utf-8"?
[ModuleSettings]
ExtensionRepositories[]=expdemo
*/ ?>
```

## Die Schichten

Nach `settings/<file>.ini` (den Standardwerten des Kernels) liest `eZINI::overrideDirs()` die Override-Verzeichnisse
Geltungsbereich für Geltungsbereich: `sa-extension`, `siteaccess`, `extension`, `override`. Ein Wert wird in dieser
Reihenfolge gelesen; der letzte gewinnt:

1. `settings/<file>.ini`, die Standardwerte des Kernels
2. `sa-extension`: `settings/` der Erweiterungen, die der Siteaccess in `ActiveAccessExtensions[]` einschaltet
3. `siteaccess`: die Siteaccess-Verzeichnisse in Erweiterungen (`settings/siteaccess/<siteaccess>/`, dann
   `settings/override__<group>/` und `settings/override/`, nur gelesen für einen Siteaccess, für den die Erweiterung
   ein Verzeichnis `settings/siteaccess/<siteaccess>/` hat; [Einstellungen je Website](../../../funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md)),
   danach `settings/siteaccess/<siteaccess>/` der Installation
4. `extension`: `settings/` jeder Erweiterung in `ActiveExtensions[]`, also auch die `<file>.ini` und
   `<file>.ini.append.php`, die Ihre Erweiterung mitbringt
5. `override`: `settings/override/` der Installation

Ein Wert in `settings/` einer aktiven Erweiterung gewinnt also gegenüber demselben Wert in
`settings/siteaccess/<siteaccess>/`; nur `settings/override/` gewinnt gegenüber ihm, und das gilt für jeden Siteaccess.
Muss eine Website einen Schlüssel je Siteaccess setzen können, liefern Sie diesen Schlüssel nicht in der
`settings/`-Datei Ihrer Erweiterung aus; geben Sie seinen Standardwert im Code mit `hasVariable()` an (unten).
Innerhalb einer Schicht gewinnt eine Erweiterung, die früher in `ActiveExtensions[]` steht, gegenüber einer späteren.
`./console exp:ini where <file>/<block>/<key> <siteaccess>` zeigt die Dateien einer Einstellung in dieser Reihenfolge.

Arrays addieren sich über die Schichten; eine leere Zeile `Key[]` löscht, was vorher kam.

## Einstellungen in PHP und Templates lesen

```php
$ini = eZINI::instance( 'expdemo.ini' );
$limit = $ini->hasVariable( 'ListSettings', 'DefaultLimit' )
    ? (int)$ini->variable( 'ListSettings', 'DefaultLimit' )
    : 10;
$classes = $ini->variable( 'ListSettings', 'ClassList' );   // an array
```

In Templates: `ezini( 'ListSettings', 'DefaultLimit', 'expdemo.ini' )`. Geben Sie immer einen sicheren Standardwert an,
wenn ein Schlüssel fehlen kann.

## Einstellungen ändern

`exp:ini` liest und schreibt jeden Geltungsbereich Zeile für Zeile und behält Kommentare bei
([Einstellungen auf der Kommandozeile ändern](../../../funktionen/6.0/exp-ini-befehl.md)):

```bash
./console exp:ini get expdemo.ini/ListSettings/DefaultLimit
./console exp:ini set expdemo.ini/ListSettings/DefaultLimit 20 global
./console exp:ini where expdemo.ini/ListSettings/DefaultLimit site
```

**Setup > Einstellungen** zeigt und bearbeitet Einstellungen im Browser ([Die Seite Einstellungen](../../../anleitungen/einstellungsseite.md)).

## Geheimnisse

API-Schlüssel, Passwörter und Tokens gehören niemals in das Repository Ihrer Erweiterung. Lesen Sie sie aus einem
Schlüssel, den die Installation in `settings/override/<file>.ini.append.php` setzt (Modus 0640, nicht committet), und
brechen Sie mit einer klaren Meldung ab, wenn er leer ist. Umgebungsspezifische Werte:
[Umgebungseinstellungen und Geheimnisse](umgebungseinstellungen-und-geheimnisse.md).

## Caches

Einstellungen werden gecacht. Nach einer Änderung: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` (nicht
`--clear-id=ini`, das den globalen INI-Cache verfehlt), und starten Sie die Worker von Velocity neu
(`exp:velocity deploy`).

## Verwandte Seiten

- [Aufbau einer Erweiterung](aufbau-einer-erweiterung.md)
- [Konsole exp:ini](../../../bc/6.0/console-exp-ini.md)
- [INI-Dateien behalten ihre Kommentare](../../../bc/6.0/ezini-behaelt-kommentare.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
