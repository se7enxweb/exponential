---
title: Attribute und Punktnotation
audience: designer, developer
category: templates
order: 40
summary: Daten in Templates mit dem Punkt lesen - Attribute von Objekten ($node.name), Schlüssel von Hashes, Einträge von Arrays, verkettete Zugriffe ($node.data_map.image.content), dynamische Schlüssel und wie Sie mit attribute und dump sehen, welche Attribute ein Objekt hat.
keywords: Punktnotation, Attribute, data_map, Hash-Schlüssel, Array-Index, verketteter Zugriff, dynamischer Schlüssel, Operator attribute, dump, Attribute anzeigen, Variable debuggen, dot notation, attributes, hash keys, array index, chained lookup, dynamic key, show attributes, debug variable
type: help
---
# Attribute und Punktnotation

Diese Seite ist für Designer, die Werte aus Knoten, Objekten und Hashes lesen. Der Punkt ist der eine Weg der
Template-Sprache, in einen Wert hineinzusehen.

## Inhalt

1. [Objekte, Hashes und Arrays](#objekte-hashes-und-arrays)
2. [Ketten](#ketten)
3. [Dynamische Schlüssel](#dynamische-schlüssel)
4. [Was hat dieses Objekt?](#was-hat-dieses-objekt)
5. [Fehlende Attribute](#fehlende-attribute)

## Objekte, Hashes und Arrays

```tpl
{$node.name}                   {* attribute of an object *}
{$settings.color}              {* key of a hash *}
{$items.0.name}                {* first item of an array, then its attribute *}
```

Kernel-Objekte (Knoten, Objekte, Attribute, Benutzer) stellen eine feste Liste von Attributen bereit; ein Knoten hat zum
Beispiel `name`, `url_alias`, `data_map`, `children`, `parent`, `path`, `class_identifier`, `can_edit` und mehr
([Knoten, Objekte und Attribute](knoten-objekte-und-attribute.md)).

## Ketten

```tpl
{$node.data_map.image.content['large'].url}
{$node.object.owner.name}
{$node.parent.name}
```

Jeder Schritt ist ein Zugriff; manche (wie `children` oder `parent`) führen eine Datenbankabfrage aus. Speichern Sie einen
Wert, den Sie mehrmals verwenden, in einer Variablen.

## Dynamische Schlüssel

Steht der Schlüssel in einer Variablen, verwenden Sie eckige Klammern:

```tpl
{def $field = 'intro'}
{$node.data_map[$field].has_content}
{$image_content[$image_class].url}
```

## Was hat dieses Objekt?

Die Operatoren `attribute` und `dump` geben die Attribute eines Werts aus (`lib/eztemplate/classes/eztemplateattributeoperator.php`):

```tpl
{$node|attribute( show, 1 )}
{$node.data_map|dump( show, 2 )}
```

`show` gibt auch die Werte aus; die Zahl ist die Tiefe. Verwenden Sie sie nur auf einem Test-Siteaccess und entfernen Sie
sie vor dem Commit: Sie geben interne Daten aus.

## Fehlende Attribute

Ein nicht vorhandenes Attribut zu lesen, gibt nichts aus und schreibt eine Warnung in die Debug-Ausgabe. Prüfen Sie
optionale Werte zuerst:

```tpl
{if is_set( $node.data_map.subtitle )}...{/if}
{if $node.data_map.image.has_content}...{/if}
```

## Verwandte Seiten

- [TPL-Syntax: Grundlagen](tpl-syntax-grundlagen.md)
- [Knoten, Objekte und Attribute](knoten-objekte-und-attribute.md)
- [Template-Debugging](template-debugging.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
