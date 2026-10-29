---
title: OW Simple Operator - PHP-Funktionen als Template-Operatoren
audience: developer, designer, app-creator
category: development
order: 900
summary: Was die Erweiterung owsimpleoperator tut, wie ihre Operatoren aus owsimpleoperator.ini registriert werden, die fünf PHP-Funktionen, die sie in dieser Installation anbietet (time, mktime, getdate, str_replace, str_rot13), und wie Sie sie aufrufen, mit jedem Beispiel auf einer Entwicklungsinstallation ausgegeben.
keywords: owsimpleoperator, OW Simple Operator, OWSimpleOperator, PHPFunctionOperator, PermittedFunctionList, PHP-Funktion als Operator, Template-Operator, time, mktime, getdate, str_replace, str_rot13, php function operator
type: help
---
# OW Simple Operator: PHP-Funktionen als Template-Operatoren

OW Simple Operator (`extension/owsimpleoperator`, Version 1.2.7 laut `ezinfo.php`, GPL v3 oder später) ist eine kleine
Erweiterung, mit der Sie Template-Operatoren mit weniger Code schreiben. Sie wurde von Open Wide entwickelt; das
Copyright liegt bei Open Wide und Brookins Consulting (siehe `README.md` und `doc/COPYRIGHT.md` der Erweiterung). Sie
tut zwei Dinge:

1. Sie macht PHP-Funktionen, die in einer Einstellung aufgeführt sind, zu Template-Operatoren, und
2. sie stellt Ihnen eine Basisklasse `OWSimpleOperator` bereit, in der jede öffentliche Methode Ihrer Klasse zu einem
   Operator wird, ohne eigenes `operatorList()`, `namedParameterList()` oder `modify()`.

Diese Seite behandelt den ersten Teil und wie die Erweiterung eingebunden ist. Der zweite Teil steht unter
[Eigene Operatoren mit OW Simple Operator](eigene-operatoren-mit-owsimpleoperator.md); die Fallstricke beider unter
[OW Simple Operator: Grenzen und Risiken](grenzen-von-owsimpleoperator.md).

## Inhalt

1. [Wie sie eingebunden ist](#wie-sie-eingebunden-ist)
2. [Die Operatoren, die sie hier anbietet](#die-operatoren-die-sie-hier-anbietet)
3. [Einen PHP-Funktionsoperator aufrufen](#einen-php-funktionsoperator-aufrufen)
4. [Eine weitere PHP-Funktion erlauben](#eine-weitere-php-funktion-erlauben)
5. [Debug-Ausgabe](#debug-ausgabe)

## Wie sie eingebunden ist

Die Erweiterung registriert ihre Operatoren wie jede andere Erweiterung (siehe
[Operatoren registrieren](operatoren-registrieren.md)):

- `settings/site.ini.append.php` fügt `ExtensionAutoloadPath[]=owsimpleoperator` zu `[TemplateSettings]` hinzu, damit
  die Template-Engine `extension/owsimpleoperator/autoloads/eztemplateautoload.php` liest.
- Diese Datei enthält keine fest eingetragenen Operatornamen. Sie liest `owsimpleoperator.ini`:
  - `[PHPFunctions] PermittedFunctionList[]` wird zu den Operatornamen der Klasse `PHPFunctionOperator`
    (einer leeren Unterklasse von `OWSimpleOperator`);
  - jede Zeile von `[ClassOperators] PermittedClassOperatorList[]`, geschrieben als `file;Class;method`, fügt den
    Operator `method` hinzu, den `Class` aus `file` bedient.
- `OWSimpleOperator::modify()` ruft dann die gleichnamige Methode der Operatorklasse auf, wenn es eine gibt, und
  andernfalls die PHP-Funktion dieses Namens.

Die mitgelieferte `settings/owsimpleoperator.ini.append.php`:

```ini
[OWSimpleOperatorSettings]
DebugOutput=disabled

[PHPFunctions]
PermittedFunctionList[]
PermittedFunctionList[]=time
PermittedFunctionList[]=mktime
PermittedFunctionList[]=getdate
PermittedFunctionList[]=str_replace
PermittedFunctionList[]=str_rot13

[ClassOperators]
PermittedClassOperatorList[]
#PermittedClassOperatorList[]=extension/owsimpleoperator/autoloads/exampleoperator.php;ExampleOperator;example_sum
```

Auf einer Entwicklungsinstallation ist die Erweiterung in den Siteaccesses `site` und `admin` aktiv, und diese fünf Namen sind ihre Operatoren.

## Die Operatoren, die sie hier anbietet

### time, mktime, getdate, str_replace und str_rot13

| Operator | PHP-Funktion | Argumente, in PHP-Reihenfolge | Rückgabe |
|---|---|---|---|
| `time` | `time()` | keine | der aktuelle Unix-Zeitstempel (Ganzzahl) |
| `mktime` | `mktime()` | Stunde, Minute, Sekunde, Monat, Tag, Jahr (jeweils optional) | ein Unix-Zeitstempel in der Zeitzone des Servers |
| `getdate` | `getdate()` | Zeitstempel (optional, Standard jetzt) | ein Hash: `seconds`, `minutes`, `hours`, `mday`, `wday`, `mon`, `year`, `yday`, `weekday`, `month`, `0` |
| `str_replace` | `str_replace()` | search, replace, subject | subject, in dem jede Suchzeichenkette ersetzt ist |
| `str_rot13` | `str_rot13()` | Zeichenkette | die Zeichenkette mit angewendetem ROT13 |

Jeder nimmt bis zu zehn Argumente (`$max_operator_parameter = 10` in `OWSimpleOperator`); Argumente, die Sie nicht
angeben, werden beim PHP-Aufruf weggelassen, sodass die Standardwerte der PHP-Funktion selbst gelten.

`str_replace` hat denselben Namen wie ein Operator der Erweiterung swark. Die Registrierung von owsimpleoperator wird
später gelesen, daher gewinnt sie auf einer Entwicklungsinstallation (siehe
[Registrierungsreihenfolge und Überschreibungen](registrierungsreihenfolge-und-ueberschreibungen.md)). Die Datumsoperatoren des
Kernels selbst (`currentdate`, `maketime`, `makedate`, `datetime`, `l10n`) decken die meisten Bedürfnisse rund ums Datum
ohne diese Erweiterung ab: siehe [Locale-Operatoren](locale-operatoren.md).

## Einen PHP-Funktionsoperator aufrufen

Verwenden Sie die Aufrufform und geben Sie jedes Argument in der Reihenfolge der PHP-Funktion an:

```tpl
{* call form: every argument in the parentheses *}
{str_replace( 'great', 'very good', 'This is a great example!' )}
{str_rot13( 'Exponential' )}
{'Exponential'|str_rot13}
```

Ausgabe:

```text

This is a very good example!
Rkcbaragvny
Rkcbaragvny
```

`str_rot13` nimmt ein einziges Argument, daher funktioniert dafür auch die Form mit der Pipe (`{'Exponential'|str_rot13}`).

Die Datumsfunktionen:

```tpl
{def $now = time()
     $parts = getdate( mktime( 12, 30, 0, 7, 4, 2026 ) )}
{if $now|gt( 1700000000 )}time() returned a Unix timestamp: an integer{/if}

mktime( 0, 0, 0, 1, 1, 2026 ) = {mktime( 0, 0, 0, 1, 1, 2026 )}
{$parts.weekday} {$parts.mday} {$parts.month} {$parts.year}, {$parts.hours}:{$parts.minutes}
```

Ausgabe:

```text
time() returned a Unix timestamp: an integer
mktime( 0, 0, 0, 1, 1, 2026 ) = 1767254400
Saturday 4 July 2026, 12:30
```

`mktime()` verwendet die Zeitzone des PHP-Prozesses: Auf einer Entwicklungsinstallation ist das die pazifische Zeit, daher ist Mitternacht am
1. Januar 2026 `1767254400` und nicht der UTC-Wert. Formatieren Sie Zeitstempel für Menschen mit den Kernel-Operatoren
`l10n` oder `datetime`, die der Locale des Siteaccess folgen.

Bei mehr als einem Argument dürfen Sie das erste Argument nicht über die Pipe hineingeben.
`OWSimpleOperator::modify()` stellt den gepipten Wert vor die Argumentliste und entfernt das **letzte** Argument, das Sie
angegeben haben, sodass die PHP-Funktion ein Argument zu wenig erhält:

```tpl
{* pipe form: the piped value becomes the FIRST argument and the LAST given argument is dropped *}
{'This is a great example!'|str_replace( 'great', 'very good' )}
```

Ausgabe:

```text
(no output)
```

Von der Template-Engine protokolliert:

```text
EXCEPTION ArgumentCountError: str_replace() expects at least 3 arguments, 2 given
```

Die Template-Engine fängt PHPs `ArgumentCountError` nicht ab: Die Ausgabe bricht an diesem Tag ab. Einzelheiten unter
[OW Simple Operator: Grenzen und Risiken](grenzen-von-owsimpleoperator.md#die-pipe-form-verliert-ein-argument).

Eine PHP-Funktion, die nicht in `PermittedFunctionList[]` steht, ist kein Operator. Die Engine lässt das Tag leer und
protokolliert eine Warnung:

```tpl
{* a function that is not in PermittedFunctionList[] is no operator at all *}
{strrev( 'abc' )}
```

Ausgabe:

```text
(no output)
```

Von der Template-Engine protokolliert:

```text
WARNING : Operator 'strrev' is not registered
```

## Eine weitere PHP-Funktion erlauben

Fügen Sie sie zu `PermittedFunctionList[]` in einer `owsimpleoperator.ini.append.php` Ihrer eigenen Erweiterung oder
Ihres Siteaccess hinzu (nicht in der Datei der Erweiterung, die ein Update ersetzen würde), und leeren Sie danach die
INI- und Template-Caches:

```ini
[PHPFunctions]
PermittedFunctionList[]=ucwords
```

Der Kommentar in der mitgelieferten Einstellungsdatei nennt die Regel: Jedes Template kann eine aufgeführte Funktion
mit beliebigen Argumenten aufrufen, also führen Sie nur Funktionen ohne Nebenwirkungen auf, die nichts außerhalb ihrer
Argumente lesen. `file_get_contents` zum Beispiel würde jedes Template jede Datei lesen lassen, die der Webserver lesen
kann. Der Test der Erweiterung `tests/owSimpleOperatorDefaultSettingsTest.php` weist eine Liste solcher Funktionen
(Datei-, Prozess-, `eval`-artige und Konfigurationsfunktionen) in den mitgelieferten Einstellungen zurück und prüft,
dass jede aufgeführte Funktion im laufenden PHP existiert.

Bevor Sie eine Funktion erlauben, prüfen Sie, ob die Template-Bibliothek sie schon hat: Die Operatoren von
`eZTemplateStringsOperator` kapseln sechzig PHP-Zeichenkettenfunktionen mit geprüften Parametern
([PHP-Zeichenkettenfunktionen](php-zeichenkettenfunktionen-umwandeln.md)), und `settings/template.ini` `[PHP] PHPOperatorList[]`
ordnet PHP-Funktionen mit einem Argument Operatoren zu ([PHP-Funktionsoperatoren](php-funktionsoperatoren.md)).

## Debug-Ausgabe

`[OWSimpleOperatorSettings] DebugOutput=enabled` schreibt jeden Aufruf mit seinen Argumenten und seinem Ergebnis in die
Debug-Ausgabe (`eZDebug::writeDebug()`), zum Beispiel `PHP Function: str_rot13("Exponential") => "Rkcbaragvny"`. Lassen
Sie sie außerhalb der Entwicklung ausgeschaltet.

## Verwandte Seiten

- [Eigene Operatoren mit OW Simple Operator](eigene-operatoren-mit-owsimpleoperator.md)
- [OW Simple Operator: Grenzen und Risiken](grenzen-von-owsimpleoperator.md)
- [Einen Operator Schritt für Schritt anlegen](einen-operator-schritt-fuer-schritt-anlegen.md): die vollständige Operatorklasse ohne die Basisklasse
- [Registrierungsreihenfolge und Überschreibungen](registrierungsreihenfolge-und-ueberschreibungen.md)
- [Template-Operatoren](README.md): alle Operatorfamilien
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
