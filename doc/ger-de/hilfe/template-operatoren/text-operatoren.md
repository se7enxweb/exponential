---
title: Text-Operatoren
audience: developer, designer, app-creator
category: development
order: 200
summary: concat und indent aus eZTemplateTextOperator - Werte zu einer Zeichenkette verbinden und mehrzeiligen Text mit Leerzeichen, Tabulatoren oder einem eigenen Füllzeichen einrücken, die Objektfalle von concat in kompilierten Templates, mit Beispielen, die auf einer Entwicklungsinstallation gerendert wurden.
keywords: concat, indent, Zeichenketten verbinden, verketten, einrücken, string concatenation, join, eZTemplateTextOperator
type: help
---
# Text-Operatoren

Die Template-Sprache hat keinen Operator zum Verketten von Zeichenketten wie `.` oder `~`; diese Aufgabe übernimmt
`concat`. Er und `indent` werden von `eZTemplateTextOperator` (`lib/eztemplate/classes/eztemplatetextoperator.php`)
über `lib/eztemplate/classes/eztemplateautoload.php` registriert.

## Inhalt

1. [concat](#concat)
2. [indent](#indent)
3. [Verwandte Seiten](#verwandte-seiten)

## concat

### concat

Verbindet die gepipte Eingabe (sofern vorhanden) und jeden Parameter der Reihe nach zu einer Zeichenkette. Zahlen
werden in Text umgewandelt.

| Parameter | Typ | Erforderlich | Standardwert |
|---|---|---|---|
| Werte | Zeichenkette oder Zahl | nein | keiner |

Eingabe: optional, sie kommt zuerst. Gibt eine Zeichenkette zurück. `concat` maskiert nicht; wenden Sie daher `wash`
auf das Ergebnis oder auf jeden nicht vertrauenswürdigen Teil an, bevor Sie es in HTML ausgeben. Generierte Referenz:
[concat](../../beispiele/template-operatoren/concat.md).

Das Beispiel verwendet das Thema Running auf einer Entwicklungsinstallation (Knoten 163):

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 163 ) )}
{concat( 'Topic: ', $node.name, ' (node ', $node.node_id, ')' )|wash}
{'/'|concat( $node.url_alias )}
{concat( 'class ', 'ng-', $node.class_identifier )}
```

Ausgabe:

```text
Topic: Running (node 163)
/running
class ng-ng_topic
```

**Objekte.** Der Interpreter (`modify()`) überspringt stillschweigend einen Parameter, der ein Objekt ist:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 163 ) )}
[{concat( 'object skipped: ', $node )}]
```

Ausgabe:

```text
[object skipped: ]
```

Die kompilierte Form (`concatTransformation()`) verbindet die Werte mit dem PHP-Operator `.`, und PHP kann einen
Knoten nicht in eine Zeichenkette umwandeln. Auf einer Entwicklungsinstallation brach dasselbe Template kompiliert mit
`Error: Object of class eZContentObjectTreeNode could not be converted to string` ab und erzeugte überhaupt keine
Ausgabe, nicht einmal den Text davor. Übergeben Sie immer das gemeinte Attribut (`$node.name`), nie das Objekt.

## indent

### indent

Setzt ein Füllzeichen vor die erste Zeile und hinter jeden Zeilenumbruch der Eingabe.

| Parameter | Typ | Erforderlich | Standardwert |
|---|---|---|---|
| `indent_count` | Ganzzahl | ja | `false` |
| `indent_type` | Bezeichner: `space`, `tab` oder `custom` | nein | `space` |
| `indent_filler` | Zeichenkette (mit `custom`) | nein | `false` |

Jeder andere Typ ergibt Leerzeichen. Eine negative Anzahl schreibt den Fehler "The value of the "count" argument is
negative" in das Debug-Protokoll und lässt den Text unverändert. Gibt eine Zeichenkette zurück. Nützlich für
Klartextausgabe wie den Text von E-Mails; in HTML werden Leerräume zusammengefasst. Generierte Referenz:
[indent](../../beispiele/template-operatoren/indent.md).

```tpl
{def $text = "line one
line two"}
[{$text|indent( 4 )}]
[{$text|indent( 2, 'custom', '> ' )}]
[{$text|indent( 1, 'tab' )|bin2hex|extract_left( 4 )}] (09 = a tab)
```

Ausgabe:

```text
[    line one
    line two]
[> > line one
> > line two]
[096c] (09 = a tab)
```

## Verwandte Seiten

- [Zeichenketten-Operatoren](zeichenketten-operatoren.md): `wash`, `shorten`, `trim`
- [PHP-Zeichenkettenfunktionen: kodieren und formatieren](php-zeichenkettenfunktionen-kodieren-und-formatieren.md): `sprintf` für formatierten Text
- [Array-Operatoren](array-operatoren.md): `append` bei Zeichenketten und `implode`
- [Alle Template-Operatoren (generiert)](../../beispiele/template-operatoren/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
