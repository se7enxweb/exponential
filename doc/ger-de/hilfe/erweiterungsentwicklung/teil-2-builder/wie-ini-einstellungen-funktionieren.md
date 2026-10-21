---
title: Wie INI-Einstellungen funktionieren
audience: developer, designer, app-creator
category: extension-development
order: 10
summary: Die INI-Dateien, die Exponential konfigurieren - Dateien, Blöcke, Variablen, Arrays und Hashes, Kommentare, die Form .ini.append.php, wie Werte aus vielen Dateien zusammengeführt werden und der INI-Cache, der nach einer Änderung geleert werden muss.
keywords: INI, Einstellungen, Block, Variable, Array, Hash, zurücksetzen, anhängen, Override, zusammenführen, Kommentar, Cache, clear-tag ini, eZINI, settings, reset, append, merge, comment
type: help
---
# Wie INI-Einstellungen funktionieren

Teil 2, Kapitel 1. Fast jedes Verhalten von Exponential wird durch eine Einstellung in einer INI-Datei bestimmt. Dieses
Kapitel erklärt das Format und die Regeln für das Zusammenführen genau, denn fast jedes Problem der Art „meine Änderung
wirkt nicht“ kommt von einer dieser Regeln.

## Dateien

Die Standardeinstellungen des Kernels liegen in `settings/*.ini`: `site.ini` (die wichtigste), `content.ini`,
`design.ini`, `image.ini`, `module.ini`, `override.ini`, `template.ini`, `workflow.ini`, `cronjob.ini` und Dutzende
mehr:

```bash
ls settings/*.ini | wc -l
ls settings/*.ini
```

Sie **bearbeiten diese Dateien nie**. Sie werden bei jedem Upgrade ersetzt. Stattdessen legen Sie Dateien mit demselben
Namen und der Endung `.ini.append.php` an einem Ort an, der nach ihnen gelesen wird (das nächste Kapitel nennt die
Orte). Jede solche Datei ergänzt oder ändert den Standard.

## Blöcke und Variablen

Eine Datei enthält **Blöcke** (Abschnitte) in eckigen Klammern und darin **Variablen**:

```ini
[SiteSettings]
SiteName=My site
DefaultPage=/content/view/full/2

[DebugSettings]
DebugOutput=disabled
```

Eine Einstellung wird durch Datei, Block und Variable benannt: „`site.ini`, `[SiteSettings]`, `SiteName`“, in diesem
Buch geschrieben als `site.ini [SiteSettings] SiteName` oder, für den Einstellungsbefehl, als
`site.ini/SiteSettings/SiteName`.

Schalter sind meist `enabled`/`disabled`, manchmal `true`/`false`. Verwenden Sie die Form, die die Standarddatei
verwendet; der Code vergleicht die genaue Zeichenkette.

## Arrays

Eine Variable mit `[]` ist ein Array. Jede Zeile fügt einen Wert hinzu:

```ini
[ExtensionSettings]
ActiveExtensions[]=ezjscore
ActiveExtensions[]=ezoe
```

Eine spätere Datei **ergänzt** ein Array. Um es zu **ersetzen**, beginnen Sie mit einer leeren `[]`-Zeile; sie leert,
was frühere Dateien gesetzt haben:

```ini
[RegionalSettings]
SiteLanguageList[]
SiteLanguageList[]=eng-US
SiteLanguageList[]=ger-DE
```

Eine vergessene Rücksetzzeile ist der häufigste Grund dafür, dass ein Array Werte enthält, die Sie nicht erwartet haben.

## Hashes

Eine Variable mit einem Schlüssel in Klammern ist ein Hash (ein assoziatives Array), zum Beispiel in `exphelp.ini`:

```ini
[AudienceSettings]
AudienceNames[]
AudienceNames[editor]=Editors
AudienceNames[admin]=Administrators
```

Eine spätere Datei kann einen Schlüssel ändern (`AudienceNames[editor]=Content editors`) oder mit der Rücksetzzeile den
ganzen Hash leeren.

## Kommentare

Eine Zeile, die mit `#` beginnt, ist ein Kommentar. Die Standarddateien des Kernels sind voll davon: Lesen Sie sie, sie
sind die beste Dokumentation jeder Einstellung.

```bash
grep -n -B5 "^ShowUsedTemplates=" settings/site.ini
```

## Die Form .ini.append.php

Override-Dateien sind PHP-Dateien, damit ein Webserver ihren Inhalt nie an einen Browser schickt, selbst wenn jemand sie
direkt anfordert. Der INI-Text steht in einem PHP-Kommentar:

```php
<?php /* #?ini charset="utf-8"?

[SiteSettings]
SiteName=My site

*/ ?>
```

Die erste Zeile öffnet einen Kommentar und gibt den Zeichensatz an; die letzte schließt ihn. Alles dazwischen ist
gewöhnliches INI. Eine Datei namens `site.ini.append` (ohne `.php`) wird noch gelesen, aber der Kernel kennzeichnet
diese Endung als veraltet (`lib/ezutils/classes/ezini.php`), und ein Webserver könnte sie als Text ausliefern:
Verwenden Sie immer `.ini.append.php`.

## Wie viele Dateien einen Wert ergeben

Für eine Anfrage liest der Kernel `settings/site.ini` und danach jede `site.ini.append.php` der aktiven Ebenen in einer
festen Reihenfolge. Jede spätere Datei:

- **ersetzt** eine einfache Variable,
- **ergänzt** ein Array (außer sie setzt es zurück),
- **ergänzt oder ersetzt** Schlüssel eines Hashes.

Die Reihenfolge ist das Thema des nächsten Kapitels. So sehen Sie sie für jede Einstellung:

```bash
./console exp:ini where site.ini/SiteSettings/SiteName
```

## Einstellungen aus PHP und Templates

Code liest Einstellungen mit `eZINI`:

```php
<?php
$ini = eZINI::instance( 'site.ini' );
$name = $ini->variable( 'SiteSettings', 'SiteName' );
if ( $ini->hasVariable( 'MySettings', 'Limit' ) )
{
    $limit = (int)$ini->variable( 'MySettings', 'Limit' );
}
```

Templates lesen sie mit dem Operator `ezini`:

```text
{ezini( 'SiteSettings', 'SiteName', 'site.ini' )|wash}
```

Beides wird ausführlich in [eZINI: Einstellungen lesen](../teil-8-referenz/ezini.md) behandelt.

## Der INI-Cache

Dutzende Dateien bei jeder Anfrage zu parsen wäre langsam, deshalb wird das zusammengeführte Ergebnis als PHP-Dateien
zwischengespeichert (standardmäßig unter `var/cache/ini/`). Nachdem Sie eine Einstellungsdatei von Hand geändert haben,
leeren Sie diesen Cache:

```bash
php bin/php/ezcache.php --clear-tag=ini
```

`./console exp:ini` und **Setup > Ini Einstellungen** leeren ihn nach einem Schreibvorgang für Sie. Verwenden Sie
`--clear-tag=ini`, nicht `--clear-id=ini`: Das Tag leert auch den globalen INI-Cache. Eine Installation, deren
`config.php` die Prüfung der Dateizeit für den INI-Cache abschaltet, sieht eine geänderte Datei **erst** nach diesem
Leeren.

> **Hinweis**
> Ein persistenter Anwendungsserver (Velocity) behält bei manchen Werten die Einstellungen, die seine Worker beim Start
> geladen haben. Scheint eine Einstellung dort ignoriert zu werden, leeren Sie den INI-Cache und starten Sie neu, wenn
> die Dokumentation der Einstellung das verlangt: `./console exp:velocity restart`.

## Checkliste, wenn eine Einstellung nicht wirkt

1. Stimmt der Dateiname (`site.ini.append.php`, nicht `site.append.ini.php`)?
2. Ist der Blockname genau wie in der Standarddatei geschrieben, einschließlich Groß- und Kleinschreibung?
3. Liegt die Datei an einem Ort, der für diesen Siteaccess gelesen wird (nächstes Kapitel)?
4. Überschreibt eine spätere Datei sie? `exp:ini where` zeigt die Kette.
5. Haben Sie den INI-Cache geleert?
6. Ist es ein Array, das eine Rücksetzzeile gebraucht hätte?

## Verwandte Seiten

- Zurück: [Teil 2: Der Builder: Einstellungen, Templates und Overrides](README.md)
- Weiter: [Einstellungsebenen und die Reihenfolge, in der sie gewinnen](einstellungsebenen-und-ihre-reihenfolge.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)
- [INI behält Kommentare](../../../bc/6.0/ezini-behaelt-kommentare.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
