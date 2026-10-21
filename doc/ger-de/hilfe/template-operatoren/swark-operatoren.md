---
title: Die swark-Operatoren
audience: developer, designer, app-creator
category: development
order: 430
summary: Wie die Erweiterung swark ihre 37 Template-Operatoren mit je einer Klasse pro Operator registriert, die Basisklasse SwarkOperator mit Parameterzeichenketten der Form name=default und kompilierten execute()-Aufrufen, welche ihrer Namen andere Erweiterungen oder der Kernel auf alpha übernehmen, und ein belegter Fall, in dem der Besitzer eines Operators mitten in einer Anfrage wechselt.
keywords: swark, SwarkOperator, SwarkOperators, Namenskonflikt, Operatorkonflikt, Registrierungsreihenfolge, operator collision, strpos, substr, ltrim, rtrim, str_replace, json_encode, registration order, ExtensionAutoloadPath
type: help
---
# Die swark-Operatoren

Die Erweiterung `swark` (`extension/swark/`, GPL 2.0, 2008 für das Produkt geschrieben, von dem dieser Kernel abstammt)
fügt kleine Hilfsoperatoren hinzu, eine Klasse pro Operator in `extension/swark/autoloads/classes/`. Diese Seite
erklärt, wie sie aufgebaut und registriert werden; die beiden folgenden Seiten dokumentieren jeden Operator:

- [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md)
- [Swark-Operatoren: Anfrage und System](swark-operatoren-anfrage-und-system.md)

## Inhalt

1. [Registrierung](#registrierung)
2. [Die Basisklasse](#die-basisklasse)
3. [Namen, die auf alpha kollidieren](#namen-die-auf-alpha-kollidieren)
4. [Der Besitzer kann während einer Anfrage wechseln](#der-besitzer-kann-während-einer-anfrage-wechseln)

## Registrierung

`extension/swark/settings/site.ini.append.php` fügt `swark` zu `[TemplateSettings] ExtensionAutoloadPath[]` hinzu.
Die Autoload-Datei `extension/swark/autoloads/eztemplateautoload.php` enthält eine Zeile:

```php
$eZTemplateOperatorArray = SwarkOperators::operators();
```

`SwarkOperators::operators()` (`extension/swark/autoloads/SwarkOperators.php`) ordnet 37 Operatornamen Klassen zu und
gibt pro Name eine Definition zurück (`'class' => ..., 'operator_names' => array( <name> )`). Ihre
`template.ini.append.php` führt außerdem `serialize` und `unserialize` in `[PHP] PHPOperatorList[]` auf, was die
`template.ini` des Kernels bereits tut.

## Die Basisklasse

Jeder Operator erweitert `SwarkOperator` (`autoloads/classes/SwarkOperator.php`). Ihr Konstruktor nimmt den
Operatornamen und Parameterzeichenketten entgegen: `'name'` ist ein erforderlicher Parameter, `'name=value'` ein
optionaler, dessen Standardwert die **Zeichenkette** nach `=` ist (`not_found_key=-1` hat also die Zeichenkette `-1`
als Standardwert). Alle Parameter haben den Typ `mixed`. `modify()` ruft die statische Methode
`execute( $operatorValue, $namedParameters )` der Klasse auf und verwendet deren Rückgabewert.

`operatorTemplateHints()` kennzeichnet jeden swark-Operator für die Elementtransformation: Der Compiler schreibt einen
direkten Aufruf von `<Class>::execute()` in das kompilierte Template und füllt fehlende Parameter mit ihren
Standardwerten. Nur `variable_names` schaltet das ab (seine `transformation()` gibt `false` zurück), weil er das
Template-Objekt braucht.

## Namen, die auf alpha kollidieren

Sieben swark-Namen werden auch anderswo registriert. Auf alpha steht `swark` in `ExtensionAutoloadPath[]` vor
`owsimpleoperator` und `ezjscore`, und eine spätere Definition eines Namens ersetzt eine frühere:

| Name | Verhalten in swark | Zuletzt registriert von | Ergebnis auf alpha |
|---|---|---|---|
| `json_encode` | `$value|json_encode` oder `json_encode( $value )` | ezjscore (`json_encode( $hash )`) | der Operator von ezjscore |
| `str_replace` | `$text|str_replace( search, replace )` | owsimpleoperator (PHP `str_replace`) | der Operator von owsimpleoperator |
| `strpos`, `strrpos` | gibt -1 zurück, wenn nichts gefunden wird | Kernel `eZTemplateStringsOperator` (gibt `false` zurück) | hängt von der ersten Verwendung ab, siehe unten |
| `substr` | `length` 0 bedeutet "bis zum Ende" | Kernel `eZTemplateStringsOperator` | hängt von der ersten Verwendung ab |
| `ltrim`, `rtrim` | optionale `charlist` | Kernel `eZTemplateStringsOperator` | hängt von der ersten Verwendung ab |

Die swark-Klassen für diese Namen sind zusammen mit den anderen dokumentiert; auf alpha kann sich ein Template nicht
darauf verlassen, sie zu erreichen.

## Der Besitzer kann während einer Anfrage wechseln

Der Kernel-Operator `eZTemplateStringsOperator` wird aus `lib/eztemplate/classes/eztemplateautoload.php` vor den
Erweiterungen registriert, sodass die swark-Definitionen von `strpos`, `strrpos`, `substr`, `ltrim` und `rtrim` ihn in
der Operatortabelle ersetzen. Sobald aber ein beliebiger Operator der Kernel-Klasse zum ersten Mal läuft, legt die
Engine dieses Objekt an und registriert es für **jeden** Namen in seiner `operatorList()`
(`eZTemplate::registerOperatorsInternal()`), womit sie diese fünf Namen für den Rest der Anfrage zurückholt. Das
Ergebnis von `strpos` hängt daher davon ab, ob ein Operator wie `strlen` früher in der Anfrage lief, in irgendeinem
Template:

```tpl
1 strpos before:  [{'abc'|strpos( 'z' )}] {'abc'|strpos( 'z' )|get_type}
2 strlen:         {'abc'|strlen}
3 strpos after:   [{'abc'|strpos( 'z' )}] {'abc'|strpos( 'z' )|get_type}
```

Ausgabe auf alpha:

```text
1 strpos before:  [-1] integer
2 strlen:         3
3 strpos after:   [] boolean[false]
```

Das kompilierte Template liefert dasselbe Ergebnis. Verlassen Sie sich auf keine der beiden Varianten dieser Namen:
Prüfen Sie mit `|gt( -1 )` und `|ne( false() )`, oder verwenden Sie `contains`/`begins_with` der Template-Bibliothek.
Die allgemeinen Regeln stehen unter [Registrierungsreihenfolge und Überschreibungen](registrierungsreihenfolge-und-ueberschreibungen.md).

## Verwandte Seiten

- [Registrierungsreihenfolge und Überschreibungen](registrierungsreihenfolge-und-ueberschreibungen.md)
- [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md)
- [Swark-Operatoren: Anfrage und System](swark-operatoren-anfrage-und-system.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
