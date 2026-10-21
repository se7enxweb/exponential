---
title: Operatoren registrieren
audience: developer, app-creator
category: development
order: 515
summary: Alle Wege, auf denen ein Template-Operator registriert wird - eztemplateautoload.php in AutoloadPathList und ExtensionAutoloadPath, die Schlüssel des Eintrags, template.ini PHPOperatorList, owsimpleoperator.ini -, wie die Reihenfolge der Erweiterungen entschieden wird (ActiveExtensions, ActiveAccessExtensions, ExtensionOrdering und die Abhängigkeiten in extension.xml) und was danach zu leeren ist.
keywords: Operator registrieren, Reihenfolge der Erweiterungen, Abhängigkeiten, Cache leeren, register operator, eztemplateautoload.php, eZTemplateOperatorArray, eZTemplateFunctionArray, AutoloadPathList, ExtensionAutoloadPath, PHPOperatorList, ActiveExtensions, ActiveAccessExtensions, ExtensionOrdering, extension.xml, requires, uses, extends, cache
type: help
---
# Operatoren registrieren

Eine Operatorklasse bewirkt nichts, solange die Template-Engine nicht weiß, auf welche Namen sie antwortet. Diese Seite
listet jede Stelle auf, die es ihr mitteilt, in `lib/eztemplate/classes/eztemplate.php` (`factory()`, `autoload()`), und
die Einstellungen, die sie liest.

## Inhalt

1. [eztemplateautoload.php](#eztemplateautoloadphp)
2. [Wo die Engine sucht: AutoloadPathList und ExtensionAutoloadPath](#wo-die-engine-sucht)
3. [Die Reihenfolge der Erweiterungen](#die-reihenfolge-der-erweiterungen)
4. [PHP-Funktionen aus template.ini](#php-funktionen-aus-templateini)
5. [OW Simple Operator](#ow-simple-operator)
6. [Template-Funktionen](#template-funktionen)
7. [Nach dem Registrieren](#nach-dem-registrieren)

## eztemplateautoload.php

Die Engine bindet aus jedem Ordner ihrer Liste eine Datei namens `eztemplateautoload.php` ein. Die Datei setzt zwei
Arrays, beide optional:

```php
<?php
$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array( 'script' => 'extension/myextension/autoloads/myextensionoperators.php',
                                    'class' => 'myExtensionOperators',
                                    'operator_names' => array( 'myext_initials', 'myext_reading_time' ) );

$eZTemplateFunctionArray = array();
$eZTemplateFunctionArray[] = array( 'script' => 'extension/myextension/autoloads/myextensionfunction.php',
                                    'class' => 'myExtensionFunction',
                                    'function_names' => array( 'myext_box' ) );
```

Die Schlüssel eines Operatoreintrags (`class`, `script`, `class_parameter`, `function`, `operator_names`,
`operator_names_function`) sind unter
[Die Schnittstelle der Operatorklasse](referenz-der-operatorklassen.md#der-registrierungseintrag) erklärt. Zwei Regeln:

- beginnen Sie jedes Array mit `= array();` (die Engine löscht beide Variablen vor dem Einbinden der Datei, aber eine
  Datei, die nur anhängt, ist fragil, wenn sie anderswo eingebunden wird);
- die Datei läuft bei jeder Anfrage, die die Template-Engine erzeugt: beschränken Sie sie auf Array-Definitionen, ohne
  Datenbank- oder Dateizugriff. (Die Datei von OW Simple Operator liest eine INI-Datei; diese ist gecacht.)

## Wo die Engine sucht

`eZTemplate::factory()` baut die Ordnerliste aus `site.ini` `[TemplateSettings]` auf:

```ini
[TemplateSettings]
AutoloadPathList[]
AutoloadPathList[]=lib/eztemplate/classes/
AutoloadPathList[]=kernel/common/
AutoloadPathList[]=lib/ezpdf/classes/
AutoloadPathList[]=kernel/private/eztemplate/
ExtensionAutoloadPath[]
```

- `AutoloadPathList[]` enthält Ordner (mit abschließendem Schrägstrich). Die vier des Kernels kommen aus
  `settings/site.ini`. Eine Erweiterung kann hier ihren Ordner hinzufügen (die Erweiterung bcwebsitestatistics tut das:
  `AutoloadPathList[]=extension/bcwebsitestatistics/autoloads/`), aber der übliche Weg ist die nächste Einstellung.
- `ExtensionAutoloadPath[]` enthält **Namen** von Erweiterungen; die Engine erweitert jeden zu `extension/<name>/autoloads/`
  (`eZExtension::expandedPathList()`). Jede Erweiterung mit Operatoren fügt in ihrer `settings/site.ini.append.php`
  eine eigene Zeile hinzu:

```ini
<?php /* #?ini charset="utf-8"?

[TemplateSettings]
ExtensionAutoloadPath[]=myextension

*/ ?>
```

Beginnen Sie diese Liste in einer Erweiterung nie mit einer leeren Zeile `ExtensionAutoloadPath[]`: In einem INI-Append
leert eine leere Zeile `[]` die bis dahin gesammelte Liste, und jede vor Ihrer Erweiterung zusammengeführte Erweiterung
verliert ihre Operatoren.

Ein Ordner auf einer der beiden Listen ohne `eztemplateautoload.php` erzeugt in der Debug-Ausgabe die Warnung
`Path '...' does not have the file 'eztemplateautoload.php' allthough it reported it had one.`.

## Die Reihenfolge der Erweiterungen

Die Engine liest zuerst `AutoloadPathList[]`, dann `ExtensionAutoloadPath[]`, jeweils in der Reihenfolge der Liste, und
die letzte Registrierung eines Namens gewinnt ([Registrierungsreihenfolge und Überschreibungen](registrierungsreihenfolge-und-ueberschreibungen.md)).
Die Reihenfolge von `ExtensionAutoloadPath[]` ist die Reihenfolge, in der die `site.ini.append.php`-Dateien der
Erweiterungen zusammengeführt werden:

1. `[ExtensionSettings] ActiveExtensions[]` (globale Erweiterungen), dann `ActiveAccessExtensions[]` des Siteaccess
   (Zugriffserweiterungen);
2. mit `[ExtensionSettings] ExtensionOrdering=enabled` (der Standard in `settings/site.ini`, und auf alpha) wird die
   Liste der aktiven Erweiterungen nach den Abhängigkeiten sortiert, die jede Erweiterung in ihrer `extension.xml`
   deklariert (`eZExtension::extensionOrdering()`, `ezpExtension::getLoadingOrder()`):
   `<requires>` und `<uses>` lassen Ihre Erweiterung **nach** der genannten laden, `<extends>` **vor** ihr.

```xml
<dependencies>
    <requires>
        <extension name="ezjscore" />
    </requires>
</dependencies>
```

Eine Erweiterung, die absichtlich den Operator einer anderen Erweiterung überschreibt, sollte daher `<requires>` (oder
`<uses>`) auf diese deklarieren; zum Überschreiben eines Operators des Kernels oder der Template-Bibliothek ist keine
Deklaration nötig, denn Erweiterungen werden immer nach `AutoloadPathList[]` gelesen.

## PHP-Funktionen aus template.ini

`template.ini` `[PHP] PHPOperatorList[]` ordnet Operatornamen PHP-Funktionen zu, die ein Argument annehmen:

```ini
[PHP]
PHPOperatorList[]
PHPOperatorList[urlencode]=urlencode
PHPOperatorList[urldecode]=urldecode
PHPOperatorList[rawurlencode]=rawurlencode
PHPOperatorList[rawurldecode]=rawurldecode
```

Das ist die `settings/template.ini` des Kernels. `kernel/common/eztemplateautoload.php` registriert diese Namen über die
Funktionen `eZPHPOperatorNameInit()` (die Namen) und `eZPHPOperatorInit()` (ein `eZTemplatePHPOperator`-Objekt für alle).
Erweiterungen fügen Einträge in ihrer `template.ini.append.php` hinzu; auf alpha ergänzt cjw_newsletter `nl2br`,
ezmultiupload `strip_tags`, swark `serialize` und `unserialize`, bcgooglesitemaps `getenv`. Verwenden Sie in einem
Append Zeilen der Form Schlüssel=Wert ohne führende leere Zeile `PHPOperatorList[]`, aus dem oben genannten Grund. Was
diese Operatoren tun und wo ihre Grenzen liegen: [PHP-Funktionsoperatoren](php-funktionsoperatoren.md).

## OW Simple Operator

Die Erweiterung owsimpleoperator registriert Operatoren aus `owsimpleoperator.ini` (`[PHPFunctions] PermittedFunctionList[]`
und `[ClassOperators] PermittedClassOperatorList[]`) über ihre eigene `eztemplateautoload.php`. Siehe
[OW Simple Operator](owsimpleoperator.md).

## Template-Funktionen

Template-Funktionen verwenden dieselben Dateien und dieselben Listen: Einträge in `$eZTemplateFunctionArray` mit
`function_names` (oder `function_names_function`) statt `operator_names`. Siehe
[Eigene Template-Funktionen](../template-sprache/eigene-template-funktionen.md).

## Nach dem Registrieren

- Prüfen Sie die Autoload-Datei und die Klassendatei mit `php -l`.
- Leeren Sie den INI-Cache (die Ordnerliste kommt aus den INI-Dateien) und die Template-Caches (kompilierte Templates
  wurden gegen die alte Liste kompiliert): `php bin/php/ezcache.php --clear-tag=ini,template --allow-root-user`.
- `php bin/php/ezpgenerateautoloads.php -e` nur dann, wenn Sie Klassen hinzugefügt haben, die über die Autoload-Arrays
  statt über `script` geladen werden.
- Auf alpha: `./console exp:velocity deploy --allow-root-user` erledigt all das und startet Velocity neu, dessen
  Worker die Registrierungen behalten, die sie beim Start vorgenommen haben.
- Prüfen Sie den Namen auf jedem Siteaccess, der ihn verwendet: `{'x'|myext_initials}` darf nicht
  `Operator 'myext_initials' is not registered` protokollieren, und das [Skript, das die Eigentümer auflistet](registrierungsreihenfolge-und-ueberschreibungen.md#die-auf-alpha-doppelt-registrierten-namen),
  darf Ihren Namen nicht neben einer anderen Klasse zeigen.

## Verwandte Seiten

- [Registrierungsreihenfolge und Überschreibungen](registrierungsreihenfolge-und-ueberschreibungen.md)
- [Die Schnittstelle der Operatorklasse](referenz-der-operatorklassen.md)
- [Einen Operator Schritt für Schritt anlegen](einen-operator-schritt-fuer-schritt-anlegen.md)
- [Einstellungen und INI-Dateien](../ersteller/anwendungsentwickler/einstellungen-und-ini-dateien.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
