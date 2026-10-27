---
title: Einstellungen und INI für CMS-Entwickler
audience: cms-developer
category: cms-development
order: 37
summary: Für CMS-Entwickler, wie Exponential mit INI-Dateien konfiguriert wird. Die Seite behandelt das Dateiformat, die Geltungsbereiche und die Reihenfolge, in der sie sich durchsetzen, wohin die Einstellungen eines Projekts gehören, das sichere Lesen und Ändern von Werten mit exp:ini, Einstellungen in der Administration, umgebungsabhängige Werte und die zu leerenden Caches.
keywords: Einstellungen, INI, INI-Dateien, append.php, settings/override, Siteaccess-Einstellungen, Erweiterungseinstellungen, Geltungsbereiche, Reihenfolge, exp:ini, get, where, set, eZINI, Operator ezini, Umgebungseinstellungen, Geheimnisse, INI-Cache, Konfiguration, settings, ini files, siteaccess settings, extension settings, scopes, order, environment settings, secrets, INI cache, configuration
type: help
---
# Einstellungen und INI für CMS-Entwickler

Diese Seite richtet sich an Entwickler, die Exponential-Projekte konfigurieren. Die gesamte Konfiguration besteht aus
INI-Dateien, die in Schichten gelesen werden, und zu wissen, welche Schicht sich durchsetzt, ist der größte Teil der
Arbeit. Die Regel für ein Projekt: Ändern Sie nie die mitgelieferten Dateien in `settings/`; legen Sie Ihre Werte in
Ihre Erweiterung und rechnerspezifische Werte in `settings/override/`.

## Inhalt

1. [Das Format](#das-format)
2. [Die Geltungsbereiche und wer gewinnt](#die-geltungsbereiche-und-wer-gewinnt)
3. [Wohin die Einstellungen eines Projekts gehören](#wohin-die-einstellungen-eines-projekts-gehören)
4. [exp:ini: sicher lesen und ändern](#expini-sicher-lesen-und-ändern)
5. [Einstellungen in der Administration](#einstellungen-in-der-administration)
6. [Werte, die sich je Rechner unterscheiden](#werte-die-sich-je-rechner-unterscheiden)
7. [Einstellungen in Templates und PHP lesen](#einstellungen-in-templates-und-php-lesen)
8. [Caches und Worker](#caches-und-worker)

## Das Format

```ini
<?php /* #?ini charset="utf-8"?

[DesignSettings]
SiteDesign=exp_mysite
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=standard

[RegionalSettings]
TranslationSA[eng]=English
*/ ?>
```

Override-Dateien enden auf `.ini.append.php` und schließen ihren Inhalt in einen PHP-Kommentar ein, sodass ein
Webserver, der die Datei ausliefert, nichts zeigt. `Name[]` ohne Wert leert ein Array, bevor die nächsten Zeilen etwas
hinzufügen; `Name[key]=value` setzt einen Hash-Eintrag
([Wie INI-Einstellungen funktionieren](../erweiterungsentwicklung/teil-2-builder/wie-ini-einstellungen-funktionieren.md)).

## Die Geltungsbereiche und wer gewinnt

Nach dem mitgelieferten Standard in `settings/` liest der Kernel diese Verzeichnisse, und eine spätere Datei gewinnt:

1. die Ordner je Siteaccess einer Erweiterung (`extension/<ext>/settings/siteaccess/<sa>/` und ihr `override/`),
2. `settings/siteaccess/<sa>/`,
3. `extension/<ext>/settings/` jeder aktiven Erweiterung (eine Erweiterung, die in `ActiveExtensions[]` früher steht,
   gewinnt),
4. `settings/override/`.

Beachten Sie, dass die globalen Einstellungen einer Erweiterung **nach** `settings/siteaccess/<sa>/` gelesen werden und
sich also gegen diese durchsetzen
([Geltungsbereiche der Einstellungen und ihre Reihenfolge](../erweiterungsentwicklung/teil-2-builder/einstellungsebenen-und-ihre-reihenfolge.md)).

## Wohin die Einstellungen eines Projekts gehören

| Einstellung | Legen Sie sie ab in |
|---|---|
| Ihr Design, die Templates der Klassen, Ansichtsmodi, Bildvarianten, eigene Tags | `extension/exp_mysite/settings/` |
| Alles zu einer öffentlichen Website oder Sprache | `extension/exp_mysite/settings/siteaccess/<sa>/` |
| Datenbank, E-Mail-Transport, Debugging, Hostnamen dieses Rechners | `settings/override/` (nie committet) |
| Siteaccess-Zuordnung (`[SiteAccessSettings] MatchOrder`, Host-Zuordnungen, `AvailableSiteAccessList[]`) | `settings/override/site.ini.append.php`: sie werden gelesen, bevor ein Siteaccess gewählt ist |

Wenn Sie die Website-Einstellungen in Ihrer Erweiterung halten, liegt die ganze Website in einem Repository, und ein
neuer Rechner braucht nur sein `settings/override/`
([Siteaccess-Einstellungen](../erweiterungsentwicklung/teil-2-builder/siteaccess-einstellungen.md),
[Erweiterungseinstellungen für Website-Bauer](../erweiterungsentwicklung/teil-2-builder/erweiterungseinstellungen-fuer-builder.md)).

## exp:ini: sicher lesen und ändern

```bash
./console exp:ini get site.ini/DesignSettings/SiteDesign -s <siteaccess>      # the value in effect
./console exp:ini where site.ini/DesignSettings/SiteDesign -s <siteaccess>    # every file that sets it, in load order
./console exp:ini list site.ini/DebugSettings                                  # a block
./console exp:ini set site.ini/DebugSettings/DebugOutput enabled global --dry-run --allow-root-user
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' exp_mysite global --allow-root-user
```

`where` beantwortet "Warum ist dieser Wert nicht der, den ich gesetzt habe?" mit einem Aufruf. Schreibvorgänge legen
eine Sicherung in `var/backup/ini/` ab, leeren den INI-Cache und nehmen `--dry-run` an. Geltungsbereiche: `global`,
`siteaccess:<sa>`, `extension:<ext>`, `extension:<ext>:siteaccess:<sa>`; das Schreiben der mitgelieferten Standards
wird verweigert ([Der Befehl exp:ini](../erweiterungsentwicklung/teil-2-builder/der-befehl-exp-ini.md),
[exp:ini in der Konsole](../../bc/6.0/console-exp-ini.md)).

## Einstellungen in der Administration

**Setup** > **INI-Einstellungen** (`settings/view`) zeigt die Einstellungen jedes Siteaccess mit der Datei, aus der jeder
Wert stammt, und kann für Administratoren Werte ändern ([Die Einstellungsseite](../../anleitungen/einstellungsseite.md),
[Einstellungen in der Administration](../erweiterungsentwicklung/teil-2-builder/einstellungen-in-der-administration.md)). Dort
vorgenommene Änderungen landen in Dateien; kopieren Sie die, die zum Projekt gehören, in Ihre Erweiterung.

## Werte, die sich je Rechner unterscheiden

Zugangsdaten zur Datenbank, E-Mail-Transporte und Hostnamen unterscheiden sich zwischen Entwicklung, Staging und Live.
Halten Sie sie im `settings/override/` jedes Rechners, das nicht committet und nicht zwischen Rechnern kopiert wird.
Einiges, das bekannt sein muss, bevor die Einstellungen gelesen werden, kommt aus Umgebungsvariablen und `config.php`
([Umgebungsvariablen](../../anleitungen/umgebungsvariablen.md),
[Umgebungseinstellungen](../../funktionen/6.0/umgebungseinstellungen.md)). Committen Sie nie Geheimnisse in ein Repository,
und kopieren Sie `settings/override/` nie von Live auf einen Entwicklungsrechner.

## Einstellungen in Templates und PHP lesen

```smarty
{ezini( 'SiteSettings', 'SiteName', 'site.ini' )|wash()}
```

```php
<?php
$ini = eZINI::instance( 'exp_mysite.ini' );
$limit = $ini->hasVariable( 'ListSettings', 'Limit' ) ? (int)$ini->variable( 'ListSettings', 'Limit' ) : 10;
```

Eine eigene Einstellungsdatei (`exp_mysite.ini`) liegt in `settings/` Ihrer Erweiterung und wird wie die des Kernels
gelesen ([Der Operator ezini](../designer/vorlagen/der-ezini-operator.md),
[eZINI](../erweiterungsentwicklung/teil-8-referenz/ezini.md),
[Einstellungen in einer Erweiterung](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/einstellungen-in-einer-erweiterung.md)).

## Caches und Worker

Einstellungen werden zwischengespeichert. Nach einer Änderung einer Datei von Hand:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

`--clear-tag=ini` deckt auch den globalen INI-Cache ab. Die Worker von Velocity halten Einstellungen im Speicher:
Starten Sie Velocity nach INI-Änderungen neu (`./console exp:velocity restart --allow-root-user`), und laden Sie das
PHP-FPM, das die Website bedient, bei Änderungen neu, die geladene Klassen betreffen
([Eine Website bereitstellen](eine-website-bereitstellen.md)).

## Verwandte Seiten

- [Siteaccesses und Multisite](siteaccesses-und-multisite.md)
- [Append-Dateien und Sicherheit](../erweiterungsentwicklung/teil-2-builder/append-php-dateien-und-sicherheit.md)
- [Modul 8: Einstellungen und Siteaccesses](../trainer/entwickler/modul-08-einstellungen-und-siteaccesses/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
