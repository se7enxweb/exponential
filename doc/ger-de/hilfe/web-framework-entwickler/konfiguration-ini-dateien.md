---
title: "Konfiguration: INI-Dateien statt YAML, PHP-Arrays oder .env"
audience: web-framework-developer, developer
category: web-frameworks
order: 170
summary: Wie Exponential konfiguriert wird, für Entwickler, die config/*.yaml, config/*.php, settings.py oder application.properties gewohnt sind – INI-Dateien in Ebenen (Standardwerte, Erweiterungen, Siteaccesses, Override), die Reihenfolge, in der sie gewinnen, Arrays, die sich aufsummieren, Werte mit eZINI lesen, mit exp:ini ändern, der INI-Cache und die Fehler, die Framework-Entwickler am häufigsten machen.
keywords: Konfiguration, configuration, Einstellungen, settings, INI, eZINI, ini.append.php, Override, override, Siteaccess, siteaccess, Erweiterungseinstellungen, extension settings, exp:ini, YAML, config, settings.py, application.properties, INI-Cache, INI cache, clear-tag, Web-Framework-Entwickler, web framework developer
type: help
---
# Konfiguration: INI-Dateien statt YAML, PHP-Arrays oder .env

Diese Seite richtet sich an Entwickler, die eine Anwendung in YAML, PHP-Arrays, `settings.py` oder
`application.properties` konfigurieren. Die gesamte Konfiguration von Exponential liegt in INI-Dateien, die über `eZINI`
(`lib/ezutils/classes/ezini.php`) gelesen werden. Die Standardwerte werden in `settings/<file>.ini` ausgeliefert; alles,
was Sie ändern, liegt in Ebenen darüber, sodass Updates Ihre Einstellungen nie überschreiben.

## Inhalt

1. [Dateien und Blöcke](#dateien-und-blöcke)
2. [Die Ebenen und ihre Reihenfolge](#die-ebenen-und-ihre-reihenfolge)
3. [Arrays](#arrays)
4. [Einstellungen lesen](#einstellungen-lesen)
5. [Einstellungen ändern](#einstellungen-ändern)
6. [Caches](#caches)
7. [Im Vergleich mit anderen Frameworks](#im-vergleich-mit-anderen-frameworks)

## Dateien und Blöcke

Jedes Thema hat eine Datei (`site.ini`, `content.ini`, `module.ini`, `design.ini`, `override.ini`, `rest.ini`,
`velocity.ini` ...), jede Datei hat Blöcke (`[DatabaseSettings]`), jeder Block hat Schlüssel. Dateien, die Sie in einer
Ebene hinzufügen, heißen `<file>.ini.append.php` und schließen ihren Inhalt in einen PHP-Kommentar ein, sodass der
Webserver sie nie ausliefert:

```ini
<?php /* #?ini charset="utf-8"?
[DebugSettings]
DebugOutput=enabled
*/ ?>
```

Ihre Erweiterung kann auch eine eigene Datei mitbringen, `extension/expdemo/settings/expdemo.ini`, in der jeder
Schlüssel dokumentiert ist ([Einstellungen und INI-Dateien in Ihrer Anwendung](../ersteller/anwendungsentwickler/einstellungen-und-ini-dateien.md)).

## Die Ebenen und ihre Reihenfolge

`eZINI` liest zuerst die Standarddatei, dann die Override-Verzeichnisse in dieser Reihenfolge, wobei eine spätere Datei
gewinnt ([Die INI-Einstellungskaskade](../../standards/architektur/kernel-architektur.md#die-kaskade-der-ini-einstellungen)):

| Reihenfolge | Geltungsbereich | Verzeichnisse |
|---|---|---|
| 0 | Standardwerte | `settings/<file>.ini` (bei einer eigenen Datei die `settings/<file>.ini` der Erweiterung) |
| 1 | Siteaccess der Erweiterung | `extension/<ext>/settings/siteaccess/<siteaccess>/` und verwandte |
| 2 | Siteaccess | `settings/siteaccess/<siteaccess>/` |
| 3 | Erweiterung | `extension/<ext>/settings/` jeder aktiven Erweiterung |
| 4 | Override | `settings/override/` der Installation |

Beachten Sie die Reihenfolge: Das globale `settings/` einer Erweiterung gewinnt gegen `settings/siteaccess/<siteaccess>/`,
und `settings/override/` gewinnt gegen alles. Ein Wert, der nur für einen Siteaccess gedacht ist, gehört deshalb in ein
Siteaccess-Verzeichnis einer Erweiterung oder in `settings/siteaccess/`, nicht in die globalen Einstellungen einer
Erweiterung. Die vollständige Referenz ist
[INI-Override-Verzeichnisse und Platzierungen](../../spezifikationen/6.0/ini-override-platzierungen.md). Ist `EXP_ENV`
gesetzt, kann jede Datei zusätzlich eine Umgebungsvariante haben, die direkt nach ihr gelesen wird
([Umgebungen und Geheimnisse](umgebungen-und-geheimnisse.md)).

Erweiterungen werden in `site.ini [ExtensionSettings] ActiveExtensions[]` (global) und `ActiveAccessExtensions[]`
(pro Siteaccess) eingeschaltet; diese beiden lassen sich nicht aus einer Erweiterung heraus setzen.

## Arrays

Schlüssel, die auf `[]` enden, sind Arrays und summieren sich über die Ebenen hinweg auf. Eine leere Zeile `Key[]` löscht,
was vorher kam, sodass eine Ebene eine Liste ersetzen statt erweitern kann:

```ini
[RequestRuleSettings]
RuleList[]
RuleList[]=system_url_full_view_to_alias
```

Assoziative Einträge werden als `Implementation[<class>]=<subclass>` geschrieben.

## Einstellungen lesen

```php
$ini = eZINI::instance( 'expdemo.ini' );
$limit = $ini->hasVariable( 'ListSettings', 'DefaultLimit' )
    ? (int)$ini->variable( 'ListSettings', 'DefaultLimit' )
    : 10;
$classes = $ini->variable( 'ListSettings', 'ClassList' );   // an array
```

In Templates: `ezini( 'ListSettings', 'DefaultLimit', 'expdemo.ini' )`. Werte sind Zeichenketten; wandeln Sie sie um
und geben Sie einen sicheren Standardwert an, wenn ein Schlüssel fehlen kann. Parsen Sie INI-Dateien nie selbst.

## Einstellungen ändern

```bash
./console exp:ini get expdemo.ini/ListSettings/DefaultLimit
./console exp:ini set expdemo.ini/ListSettings/DefaultLimit 20 global
./console exp:ini where expdemo.ini/ListSettings/DefaultLimit site
```

`exp:ini where` listet jede Datei auf, die für einen Siteaccess einen Wert setzt, und beantwortet so in Sekunden die
Frage, warum ein Wert nicht der ist, den Sie geschrieben haben
([Einstellungen über die Kommandozeile ändern](../../funktionen/6.0/exp-ini-befehl.md)). **Setup > Einstellungen** zeigt
Einstellungen im Browser an und bearbeitet sie.

## Caches

Einstellungen werden gecacht. Nach einer Änderung: `php bin/php/ezcache.php --clear-tag=ini` (nicht `--clear-id=ini`,
das den globalen INI-Cache verfehlt), und starten Sie die Velocity-Worker mit `./console exp:velocity deploy` neu, denn
die Worker behalten die Einstellungen, die sie geladen haben.

## Im Vergleich mit anderen Frameworks

| Framework | Konfiguration | Exponential |
|---|---|---|
| Symfony | `config/packages/*.yaml`, `when@prod`, Parameter | INI-Dateien; Umgebungsvarianten mit `EXP_ENV` |
| Laravel | `config/*.php`, die `env()` lesen | INI-Dateien; Geheimnisse in `settings/override/` |
| Django | `settings.py`, aufgeteilte Einstellungsmodule | die Override-Ebene und die Siteaccess-Ebenen |
| Rails | `config/environments/*.rb`, Credentials | Varianten über `EXP_ENV`; `settings/override/` |
| Spring | `application.yml` mit Profilen | Varianten pro Umgebung; Siteaccess-Ebenen pro Site |

Es gibt keine Ausdruckssprache, keine Service-Parameter und keinen typisierten Konfigurationsbaum: Ein Schlüssel ist
eine Zeichenkette oder ein Array von Zeichenketten.

## Verwandte Seiten

- [Einstellungen und INI-Dateien in Ihrer Anwendung](../ersteller/anwendungsentwickler/einstellungen-und-ini-dateien.md)
- [Console exp:ini](../../bc/6.0/console-exp-ini.md)
- [Seite Einstellungen](../../anleitungen/einstellungsseite.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
