---
title: Die Operatoren attribute und dump
audience: developer, designer, app-creator
category: development
order: 220
summary: attribute und dump aus eZTemplateAttributeOperator - auflisten, was ein Objekt, ein Hash oder ein Array Templates anbietet, mit oder ohne Werte, bis zu einer Tiefe, als HTML-Tabelle oder als Text, und warum ihre Ausgabe nie eine öffentliche Seite erreichen darf; Beispiele, die auf einer Entwicklungsinstallation gerendert wurden.
keywords: attribute, dump, Fehlersuche, untersuchen, Objektattribute, Attribute auflisten, debug, inspect, object attributes, data_map, ezpAttributeOperatorManager, AttributeOperator, DefaultFormatter, eZTemplateAttributeOperator
type: help
---
# Die Operatoren attribute und dump

Wenn Sie nicht wissen, was eine Variable enthält, listen `attribute` und `dump` es auf. Beide werden von
`eZTemplateAttributeOperator` (`lib/eztemplate/classes/eztemplateattributeoperator.php`) über
`lib/eztemplate/classes/eztemplateautoload.php` registriert. Sie durchlaufen Arrays und jedes Objekt, das die Methoden
`attributes()` und `attribute()` hat (Knoten, Inhaltsobjekte, Attribute, Benutzer und die anderen persistenten
Objekte), eine Ebene pro Schritt, und geben eine Zeile pro Schlüssel aus.

Die Ausgabe erzeugt ein Formatierer, der in `template.ini` gewählt wird:

```ini
[AttributeOperator]
DefaultFormatter=html
OutputFormatter[html]=ezpAttributeOperatorHTMLFormatter
OutputFormatter[text]=ezpAttributeOperatorTextFormatter
```

`ezpAttributeOperatorManager::getOutputFormatter()` (`kernel/private/eztemplate/ezpattributeoperatormanager.php`)
fällt bei einem nicht registrierten Namen auf `DefaultFormatter` zurück. Eine Erweiterung kann eine Formatiererklasse
hinzufügen, die `ezpAttributeOperatorFormatterInterface` implementiert, und sie unter `OutputFormatter[]` eintragen.

## Inhalt

1. [attribute](#attribute)
2. [dump](#dump)
3. [Sicherheit](#sicherheit)
4. [Verwandte Seiten](#verwandte-seiten)

## attribute

### attribute

Listet die Schlüssel des gepipten Arrays oder Objekts auf, standardmäßig **ohne** Werte und zwei Ebenen tief.

| Parameter | Typ | Erforderlich | Standardwert |
|---|---|---|---|
| `show_values` | Zeichenkette: `show`, um Werte einzuschließen | nein | `''` (nur Namen und Typen) |
| `max_val` | Zahl: Anzahl der Ebenen, in die abgestiegen wird | nein | `2` |
| `format` | `html` oder `text` | nein | `[AttributeOperator] DefaultFormatter` |

Eingabe: der zu untersuchende Wert, über die Pipe. Gibt die formatierte Auflistung zurück; ein Skalar oder ein leeres
Array wird so zurückgegeben, wie der Formatierer es exportiert. Generierte Referenz:
[attribute](../../beispiele/template-operatoren/attribute.md).

Die Felder der Startseite von Fit & Healthy (Knoten 89), eine Ebene, als Text:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )}
{$node.data_map|attribute( 'show', 1, 'text' )}
```

Ausgabe:

```text
title (object[eZContentObjectAttribute]=Object)
metadata (object[eZContentObjectAttribute]=Object)
teaser_title (object[eZContentObjectAttribute]=Object)
teaser_intro (object[eZContentObjectAttribute]=Object)
full_intro (object[eZContentObjectAttribute]=Object)
image (object[eZContentObjectAttribute]=Object)
tags (object[eZContentObjectAttribute]=Object)
```

Ohne `show` nur Namen und Typen:

```tpl
{def $hash = hash( 'title', 'Fit & Healthy', 'tags', array( 'fit', 'healthy' ) )}
{$hash|attribute( '', 2, 'text' )}
```

Ausgabe:

```text
title (string)
tags (array)
    0 (string)
    1 (string)
```

## dump

### dump

Derselbe Durchlauf mit anderen Standardwerten: Werte werden **angezeigt**, eine Ebene tief.

| Parameter | Typ | Erforderlich | Standardwert |
|---|---|---|---|
| `show_values` | Zeichenkette: `show`, um Werte einzuschließen | nein | `'show'` |
| `max_val` | Zahl: Anzahl der Ebenen, in die abgestiegen wird | nein | `1` |
| `format` | `html` oder `text` | nein | `[AttributeOperator] DefaultFormatter` |

Generierte Referenz: [dump](../../beispiele/template-operatoren/dump.md).

```tpl
{def $hash = hash( 'title', 'Fit & Healthy', 'tags', array( 'fit', 'healthy' ) )}
{$hash|dump( 'show', 2, 'text' )}
```

Ausgabe:

```text
title (string='Fit & Healthy')
tags (array=Array(2))
    0 (string='fit')
    1 (string='healthy')
```

Mit dem Standardformatierer `html` ist das Ergebnis eine Tabelle:

```tpl
{hash( 'node_id', 89, 'name', 'Fit & Healthy' )|dump}
```

Ausgabe:

```text
<table><tr><th align="left">Attribute</th>
<th align="left">Type</th>
<th align="left">Value</th>
</tr>
<tr><td>node_id</td>
<td>integer</td>
<td>89</td>
</tr>
<tr><td>name</td>
<td>string</td>
<td>'Fit & Healthy'</td>
</tr>
</table>
```

Ein Skalar kommt unverändert zurück:

```tpl
[{'plain text'|dump( 'show', 1, 'text' )}] [{42|attribute( 'show', 1, 'text' )}]
```

Ausgabe:

```text
['plain text'] [42]
```

## Sicherheit

- Der HTML-Formatierer maskiert Werte nicht: `'Fit & Healthy'` kam oben mit einem nackten `&` heraus. Ein Wert, der
  Markup oder Skript enthält, wird so ausgegeben, wie er ist. Verwenden Sie diese Operatoren auf einer
  Entwicklungsinstallation oder in einem Block, den nur Administratoren sehen, und entfernen Sie sie, bevor ein Template
  live geht.
- Eine tiefe Ebene bei einem Knoten läuft in sein Objekt, seine Versionen, seine Eltern und deren Attribute: Jede Ebene
  vervielfacht die Zahl der Datenbankabfragen. Beginnen Sie mit `max_val` 1.
- Manche Attribute werden erst beim Lesen berechnet. Sie mit Werten aufzulisten führt diesen Code aus, sodass ein `dump`
  langsam sein kann, auch wenn die Seite klein ist.

Die Beispiele wurden auf einer Entwicklungsinstallation vom Interpreter und noch einmal mit dem Template-Compiler gerendert; beide lieferten
dieselbe Ausgabe.

## Verwandte Seiten

- [Fehlersuche in Templates](../designer/vorlagen/template-debugging.md)
- [Knoten, Objekte und Attribute](../designer/vorlagen/knoten-objekte-und-attribute.md)
- [Typ-Operatoren](typ-operatoren.md): `get_type` und `get_class` für einen Wert
- [Alle Template-Operatoren (generiert)](../../beispiele/template-operatoren/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
