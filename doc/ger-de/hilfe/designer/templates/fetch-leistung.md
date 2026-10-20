---
title: Fetch-Leistung
audience: designer, developer
category: templates
order: 430
summary: Templates schnell halten, wenn sie Inhalte lesen - einmal abrufen, immer begrenzen, im Fetch filtern, Fetches in Schleifen und children bei großen Ordnern vermeiden, content_info bevorzugen, Zählungen zum Blättern verwenden, teure Teile cachen und mit der Debug-Ausgabe messen.
keywords: Fetch-Leistung, langsame Templates, Datenbankabfragen, Fetch in Schleife, limit, children, data_map, load_data_map, cache-block, Debug-Ausgabe, SQL-Zahl, N+1, fetch performance, slow templates, database queries, fetch in loop, debug output, sql count, n plus one
type: help
---
# Fetch-Leistung

Diese Seite ist für Designer, deren Seiten langsam geworden sind. In Templates kommen fast alle Kosten von Fetches und den
Attributen, die unbemerkt abrufen (`children`, `parent`, `data_map`). Die Regeln unten halten die Zahl der
Datenbankabfragen klein.

## Inhalt

1. [Die Regeln](#die-regeln)
2. [Attribute, die abrufen](#attribute-die-abrufen)
3. [Die Schleifenfalle](#die-schleifenfalle)
4. [Messen](#messen)

## Die Regeln

1. **Einmal abrufen.** Speichern Sie das Ergebnis in einer Variablen; übergeben Sie es Teilen als Parameter.
2. **Immer ein `limit`** bei `list` und `tree`; blättern Sie mit `offset` und einem `list_count`.
3. **Im Fetch filtern** (`class_filter_array`, `attribute_filter`), nicht mit `{if}` in der Schleife.
4. **Bevorzugen Sie, was Sie schon haben.** Im Pagelayout enthält `$module_result.content_info` Klasse, Knoten-ID, Tiefe
   und URL-Alias der Seite ohne Fetch.
5. **Cachen Sie, was für viele Besucher gleich ist**: Menüs und Fußzeilen in Cache-Blöcken ([Cache-Block](cache-block.md)).
6. **Halten Sie Vollansichten cachebar**, damit der View-Cache sie ausliefert ([Caching für Designer](../../../../eng-us/help/designers/theme-design/caching-for-designers.md)).

## Attribute, die abrufen

| Attribut | Kosten |
|---|---|
| `$node.children` | alle Kinder, kein Limit, kein Filter |
| `$node.children_count` | eine Zählabfrage |
| `$node.parent`, `$node.path` | ein oder mehrere Knoten-Fetches |
| `$node.data_map` | die Felder des Objekts (einmal pro Objekt geladen) |
| `$node.object.owner`, `$node.creator` | ein Objekt-Fetch |

`$node.children` bei einem Ordner mit Tausenden Einträgen ist die häufigste Ursache einer langsamen Seite: Verwenden Sie
stattdessen einen Fetch mit Limit.

## Die Schleifenfalle

```tpl
{* slow: one fetch per child *}
{foreach $children as $child}
    {def $sub = fetch( 'content', 'list', hash( 'parent_node_id', $child.node_id ) )}
    ...
{/foreach}
```

Zehn Kinder bedeuten elf Abfragen, hundert bedeuten hundertundeine. Rufen Sie stattdessen die Nachkommen einmal mit `tree`
und `depth` 2 ab und gruppieren Sie sie im Template, oder zeigen Sie die Unterebene nur auf der eigenen Seite des Kindes.

## Messen

Schalten Sie auf einem Test-Siteaccess die Debug-Ausgabe für Ihre Adresse ein (`[DebugSettings] DebugOutput=enabled`,
`DebugByIP=enabled`, `DebugIPList[]`): Sie zeigt die Zeit der Seite und die Datenbankabfragen. Vergleichen Sie vor und nach
einer Änderung, mit geleerten Caches und noch einmal mit warmen.

## Verwandte Seiten

- [Knoten und Listen abrufen](knoten-und-listen-abrufen.md)
- [Theme-Leistung](../../../../eng-us/help/designers/theme-design/theme-performance.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
