---
title: Einstellungen mit eZINI lesen
audience: php-developer, developer
category: php-development
order: 170
summary: Die eZINI-API für PHP-Entwickler - instance(), variable(), hasVariable(), variableArray(), group() und hasGroup(), die Reihenfolge der Einstellungsschichten, Umgebungsvarianten, Arrays und leere Zurücksetzungen, Standardwerte für fehlende Schlüssel, Einstellungen aus Code schreiben, Caches und Einstellungen unter persistenten Workern.
keywords: Einstellungen, Einstellungsschichten, Umgebungsvariante, INI-Cache, Siteaccess, eZINI, instance, variable, hasVariable, variableArray, group, hasGroup, setVariable, settings layers, siteaccess, override, EXP_ENV, environment variant, ini cache, clear-tag=ini, exp:ini, append.php
type: help
---
# Einstellungen mit eZINI lesen

Diese Seite richtet sich an PHP-Entwickler, die in ihrem Code Konfiguration lesen. Jede Einstellung in Exponential ist
ein INI-Wert, gelesen über `eZINI` (`lib/ezutils/classes/ezini.php`), das die Standardwerte des Kernels, die
Erweiterungen, den Siteaccess und die Überschreibungen der Installation zu einer Sicht zusammenführt. Die auf
Erweiterungen ausgerichtete Referenz ist [eZINI](../erweiterungsentwicklung/teil-8-referenz/ezini.md); diese Seite
konzentriert sich auf die korrekte Verwendung in PHP-Code.

## Inhalt

1. [Die Aufrufe](#die-aufrufe)
2. [Die Schichten](#die-schichten)
3. [Umgebungsvarianten](#umgebungsvarianten)
4. [Arrays](#arrays)
5. [Standardwerte für fehlende Schlüssel](#standardwerte-für-fehlende-schlüssel)
6. [Ihre eigene Einstellungsdatei](#ihre-eigene-einstellungsdatei)
7. [Einstellungen schreiben](#einstellungen-schreiben)
8. [Caches und persistente Worker](#caches-und-persistente-worker)

## Die Aufrufe

```php
$ini = eZINI::instance();                          // site.ini
$ini = eZINI::instance( 'content.ini' );           // another file
$value  = $ini->variable( 'DebugSettings', 'DebugOutput' );      // a string
$has    = $ini->hasVariable( 'DebugSettings', 'DebugOutput' );   // bool
$list   = $ini->variable( 'ExtensionSettings', 'ActiveExtensions' ); // an array for Key[] settings
$parts  = $ini->variableArray( 'SomeBlock', 'SemicolonList' );   // a value split on ';'
$block  = $ini->group( 'DatabaseSettings' );                     // the whole block as an array
$exists = $ini->hasGroup( 'AcmeReportSettings' );
```

`eZINI::instance()` gibt ein gemeinsam genutztes Objekt je Datei und Verzeichnis zurück, sodass ein erneuter Aufruf
günstig ist.

## Die Schichten

Ein Wert wird in dieser Reihenfolge gelesen; die letzte Schicht, die ihn setzt, gewinnt:

1. `settings/<file>.ini`, die Standardwerte des Kernels (oder die eigene `settings/<file>.ini` Ihrer Erweiterung für eine Datei von Ihnen)
2. `settings/<file>.ini.append.php` der aktiven Erweiterungen, in der Reihenfolge der Erweiterungen
3. `settings/siteaccess/<siteaccess>/` und `settings/siteaccess/<siteaccess>/` der Erweiterungen
4. `settings/override/` der Erweiterungen
5. `settings/override/<file>.ini.append.php` der Installation

Die Reihenfolge der Erweiterungen spielt eine Rolle, wenn zwei Erweiterungen denselben Schlüssel setzen
([Ladereihenfolge der Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ladereihenfolge-der-erweiterungen.md),
[Geltungsbereiche von Einstellungen und ihre Reihenfolge](../erweiterungsentwicklung/teil-2-builder/einstellungsebenen-und-ihre-reihenfolge.md)).

## Umgebungsvarianten

Wenn `config.env.php` `EXP_ENV` definiert (zum Beispiel `dev`, `test`, `prod`), liest `eZINI` zusätzlich eine Variante
jeder Datei mit dem Umgebungsnamen nach `.ini`, nach der Standarddatei derselben Schicht:

```text
settings/override/site.ini.append.php
settings/override/site.ini.test.append.php     read after it when EXP_ENV is 'test'
```

Die Variante überschreibt nur ihre eigene Schicht, und jede Umgebung hat ihren eigenen INI-Cache.
`eZINI::environment()` gibt den Namen zurück; Templates lesen ihn mit `{exp_environment()}`
([Umgebungseinstellungen](../../funktionen/6.0/umgebungseinstellungen.md)).

## Arrays

```ini
[AcmeReportSettings]
ClassList[]
ClassList[]=article
ClassList[]=event
```

- Zeilen `Key[]=value` addieren sich über die Schichten hinweg.
- Eine leere Zeile `Key[]` löscht alles, was davor gesetzt wurde, auch in früheren Schichten. Liefern Sie sie als erste
  Zeile einer Liste in Ihren Standardwerten aus, damit eine Überschreibung die Liste sauber ersetzen kann.
- `Key[name]=value` ergibt ein assoziatives Array.

## Standardwerte für fehlende Schlüssel

`variable()` auf einen fehlenden Schlüssel schreibt einen Fehler („Undefined variable“ oder „Undefined group“) in die
Debug-Ausgabe und das Fehler-Log und gibt `false` zurück. Code, der optionale oder neue Schlüssel liest, muss prüfen:

```php
$ini = eZINI::instance( 'acmereport.ini' );
$limit = $ini->hasVariable( 'ListSettings', 'DefaultLimit' )
    ? max( 1, min( 100, (int)$ini->variable( 'ListSettings', 'DefaultLimit' ) ) )
    : 10;
```

Casten und begrenzen Sie Werte aus Einstellungen immer, wie jede Eingabe: Einstellungen werden von Menschen geschrieben.

## Ihre eigene Einstellungsdatei

Liefern Sie `extension/acmereport/settings/acmereport.ini` mit jedem Schlüssel und einem Kommentar zu jedem aus.
Änderungen an den Dateien des Kernels gehören in `*.ini.append.php`, eingeschlossen in einen PHP-Kommentar, damit der
Webserver sie niemals anzeigt:

```ini
<?php /* #?ini charset="utf-8"?
[ModuleSettings]
ExtensionRepositories[]=acmereport
*/ ?>
```

## Einstellungen schreiben

`$ini->setVariable( $block, $name, $value )` ändert den Wert im Speicher für diese Anfrage; `save()` schreibt eine Datei.
Um Einstellungen in einer Datei zu ändern, verwenden Sie die Engine `exp:ini`, die Zeile für Zeile bearbeitet und
Kommentare erhält ([Einstellungen auf der Kommandozeile ändern](../../funktionen/6.0/exp-ini-befehl.md), [exp:ini](../../bc/6.0/console-exp-ini.md)):

```bash
php bin/php/console exp:ini get acmereport.ini/ListSettings/DefaultLimit
php bin/php/console exp:ini set acmereport.ini/ListSettings/DefaultLimit 20 global
```

Schreiben Sie niemals Geheimnisse in eine Datei, die Ihre Erweiterung ausliefert; lesen Sie sie aus `settings/override/`
der Installation.

## Caches und persistente Worker

- Einstellungen werden in den INI-Cache kompiliert. Nach einer Änderung: `php bin/php/ezcache.php --clear-tag=ini`.
  `--clear-id=ini` erfasst den globalen INI-Cache nicht.
- Unter Velocity liest ein Worker die Einstellungen einmal und behält sie; `exp:velocity deploy` leert den INI-Cache und
  startet die Worker in der richtigen Reihenfolge neu ([Persistente Velocity-Worker für PHP-Entwickler](persistente-velocity-worker-fuer-php-entwickler.md)).
- Halten Sie einen `eZINI`-Wert nicht anfrageübergreifend in einer eigenen statischen Eigenschaft; lesen Sie ihn dort aus
  `eZINI::instance()`, wo Sie ihn brauchen ([Fallen durch statischen Zustand](fallen-durch-statischen-zustand.md)).

## Verwandte Seiten

- [Einstellungen und INI-Dateien in Ihrer Anwendung](../ersteller/anwendungsentwickler/einstellungen-und-ini-dateien.md)
- [INI-Dateien für Erweiterungsautoren](../erweiterungsentwicklung/teil-8-referenz/ini-dateien-fuer-erweiterungsautoren.md)
- [eZINI erhält Kommentare](../../bc/6.0/ezini-behaelt-kommentare.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
