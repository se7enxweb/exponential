---
title: "eZINI: Einstellungen lesen"
audience: developer
category: extension-development
order: 20
summary: Die eZINI-Methoden, die eine Erweiterung verwendet - instance(), variable(), hasVariable(), variableArray(), group(), hasSection() - mit ihren Signaturen, was sie zurückgeben, wenn eine Einstellung fehlt, und der Template-Operator ezini().
keywords: Einstellungen, INI-Dateien, Einstellungen lesen, Overrides, Template-Operator, eZINI, instance, variable, hasVariable, variableArray, variableMulti, group, hasSection, hasGroup, ezini operator, settings, ini
type: help
---
# eZINI: Einstellungen lesen

Teil 8, Referenz 2. `eZINI` (`lib/ezutils/classes/ezini.php`) liest INI-Dateien mit allen zusammengeführten
Overrides: die Vorgabe in `settings/`, die `settings/` der Erweiterungen, den Siteaccess, `settings/override`. Wie die
Dateien und Geltungsbereiche zusammenwirken, erklären [Wie INI-Einstellungen funktionieren](../teil-2-builder/wie-ini-einstellungen-funktionieren.md)
und [Einstellungen in einer Erweiterung](../teil-3-erweiterungsentwickler/einstellungen-in-einer-erweiterung.md).

## Eine Instanz holen

```php
static function instance( $fileName = 'site.ini', $rootDir = 'settings', $useTextCodec = null, $useCache = null,
                          $useLocalOverrides = null, $directAccess = false, $addArrayDefinition = false )
```

Verwenden Sie immer `eZINI::instance( 'myext.ini' )` nur mit dem Dateinamen. Die Instanz wird für den Request geteilt
und hat alle Overrides angewendet. Verwenden Sie nicht `new eZINI()`.

## Lesen

| Methode | Gibt zurück |
|---|---|
| `variable( $blockName, $varName )` | den Wert (eine Zeichenkette oder bei Einstellungen der Form `Name[]=` ein Array); `false` und einen Fehler in der Debug-Ausgabe, wenn die Gruppe oder die Einstellung fehlt |
| `hasVariable( $blockName, $varName )` | `true`, wenn die Einstellung existiert; verwenden Sie es bei optionalen Einstellungen vor `variable()` |
| `hasSection( $sectionName )`, `hasGroup( $blockName )` | `true`, wenn der Abschnitt existiert |
| `group( $blockName )` | alle Einstellungen eines Abschnitts als Array |
| `variableArray( $blockName, $varName )` | jeden Wert einer Array-Einstellung, an `;` in ein Array aufgeteilt |
| `variableMulti( $blockName, $varNames, $signatures = array() )` | mehrere Einstellungen eines Abschnitts auf einmal |
| `groups()` | jeden Abschnitt mit seinen Einstellungen |

`variable()` schreibt „Undefined variable: ... in group ...“ in das Fehlerprotokoll, wenn die Einstellung fehlt. Bei
einer optionalen Einstellung prüfen Sie zuerst:

```php
<?php
$ini = eZINI::instance( 'myext.ini' );
$limit = $ini->hasVariable( 'ListSettings', 'Limit' ) ? (int)$ini->variable( 'ListSettings', 'Limit' ) : 10;
$enabled = $ini->hasVariable( 'ListSettings', 'Enabled' ) && $ini->variable( 'ListSettings', 'Enabled' ) === 'enabled';
$types = $ini->hasVariable( 'ListSettings', 'Types' ) ? (array)$ini->variable( 'ListSettings', 'Types' ) : array();
```

Einstellungen sind Zeichenketten: Wandeln Sie Zahlen um, vergleichen Sie Schalter mit der Zeichenkette (`'enabled'`,
`'true'`) und wandeln Sie Arrays um.

## Statische Hilfsmethoden

| Methode | Verwendung |
|---|---|
| `eZINI::exists( $fileName = "site.ini", $rootDir = "settings" )` | ob die Datei in diesem Verzeichnis existiert |
| `eZINI::isLoaded( $fileName, ... )` | ob bereits eine Instanz erzeugt wurde |
| `eZINI::fetchFromFile( $fileName )` | eine einzelne Datei für sich lesen, ohne Overrides |
| `eZINI::getSiteAccessIni( $siteAccess, $iniFile )` | die Einstellungen, wie ein anderer Siteaccess sie sieht |

## Schreiben

Erweiterungen sollten zur Laufzeit keine Einstellungen schreiben. `setVariable()` ändert die Instanz im Speicher nur
für den aktuellen Request. Um gespeicherte Einstellungen zu ändern, verwenden Sie den Konsolenbefehl `exp:ini`
([Der Befehl exp:ini](../teil-2-builder/der-befehl-exp-ini.md)) oder die Einstellungsseiten der Administration; der
RAD-Katalog führt „Einstellungen aus Code schreiben“ für den seltenen Fall, der das braucht, als eigenen
Erweiterungspunkt.

## In Templates

```text
{ezini( 'ListSettings', 'Limit', 'myext.ini' )}
{if ezini_hasvariable( 'ListSettings', 'Limit', 'myext.ini' )} ... {/if}
```

`ezini()` verhält sich wie `variable()`; prüfen Sie optionale Einstellungen zuerst mit `ezini_hasvariable()`.

## Caches

Einstellungen werden gecacht. Leeren Sie nach der Änderung einer INI-Datei den INI-Cache:
`php bin/php/ezcache.php --clear-tag=ini`.

## Verwandte Seiten

- Zurück: [Erweiterungspunkte auf einen Blick](erweiterungspunkte-auf-einen-blick.md)
- Weiter: [eZDebug und Protokollierung](ezdebug-und-protokollierung.md)
- [Einstellungen in einer Erweiterung](../teil-3-erweiterungsentwickler/einstellungen-in-einer-erweiterung.md)
- [INI-Dateien, denen ein Erweiterungsautor begegnet](ini-dateien-fuer-erweiterungsautoren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
