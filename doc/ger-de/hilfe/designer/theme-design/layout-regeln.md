---
title: Layout-Regeln
audience: designer
category: theme-design
order: 750
summary: Wie eine Seite ihr Layout bekommt: Regeln mit Zielen (Pfad, Knoten, Teilbaum), Bedingungen (Siteaccess, Inhaltstyp, Abfrageparameter, Zeit) und Priorität; der erste Treffer gewinnt; der Resolver-Cache; und das fetch, das zeigt, welche Regeln auf einen Knoten passen.
keywords: Layout-Regeln, Zuordnungen, Ziele, Bedingungen, Priorität, Teilbaum-Ziel, Bedingung content_type, Resolver, Resolver-Cache, rules_for_node, falsches Layout, DefaultLayout, layout rules, mappings, targets, conditions, priority, subtree target, resolver cache, wrong layout
type: help
---
# Layout-Regeln

Diese Seite ist für Designer, die sich fragen, warum eine Seite ein bestimmtes Layout zeigt. Regeln (im Editor Zuordnungen genannt)
entscheiden das. Vollständige Referenz: [Exponential Layouts](../../../bc/6.0/layouts.md), „Rules“.

## Inhalt

1. [Eine Regel](#eine-regel)
2. [Ziele](#ziele)
3. [Bedingungen](#bedingungen)
4. [Der erste Treffer gewinnt](#der-erste-treffer-gewinnt)
5. [Das Layout einer Seite debuggen](#das-layout-einer-seite-debuggen)

## Eine Regel

Eine Regel hat ein Layout, Ziele, Bedingungen, eine Priorität und einen Schalter zum Aktivieren. Der Resolver
(`expLayoutsResolver::resolve()`) geht die aktiven Regeln nach Priorität durch und gibt das erste veröffentlichte Layout zurück, dessen
Regel auf die Anfrage passt.

## Ziele

| Typ | Passt, wenn |
|---|---|
| `path_prefix` | der Anfragepfad mit dem Wert beginnt |
| `path` | der Pfad dem Wert entspricht |
| `path_regex` | der Pfad auf einen regulären Ausdruck passt |
| `node` | die Anfrage dieser Inhaltsknoten ist |
| `subtree` | der Knoten der Anfrage unter diesem Ort liegt |

`subtree` übersteht es, wenn ein Redakteur einen Ordner umbenennt; ein `path_prefix` nicht.

## Bedingungen

| Typ | Schränkt ein nach |
|---|---|
| `siteaccess` | dem Siteaccess, der die Seite ausliefert |
| `content_type` / `class` | der Inhaltsklasse des Knotens |
| `query_parameter`, `route_parameter` | einem Parameter in der URL |
| `time` | einem Datumsbereich (Kampagnen-Layouts, die sich selbst ein- und ausschalten) |

Ein unbekannter Bedingungstyp passt, statt fehlzuschlagen; ein Tippfehler erweitert eine Regel also, statt sie abzuschalten.

## Der erste Treffer gewinnt

Wenn ein Layout nicht erscheint, fängt meistens eine breitere Regel mit höherer Priorität die Seite zuerst ab. Sehen Sie sich vor allem
anderen die Prioritäten an.

## Das Layout einer Seite debuggen

- In einem Template: `fetch( 'explayouts', 'rules_for_node', hash( 'node_id', $node.node_id ) )` listet die Regeln, die auf einen Knoten
  passen, und `fetch( 'explayouts', 'resolve_layout_for_node', hash( 'node_id', ... ) )` gibt das Layout zurück, das er bekäme
  (`extension/explayouts/modules/explayouts/function_definition.php`).
- Der Resolver speichert sein Ergebnis pro Pfad unter `var/<site>/cache/explayouts/resolver/` zwischen, mit
  `explayouts.ini [ResolverSettings] CacheTTL` (3600 Sekunden). Veröffentlichen im Editor leert ihn; eine direkt in der Datenbank
  geänderte Regel nicht.
- `[ResolverSettings] DefaultLayout` nennt das Layout, das verwendet wird, wenn keine Regel passt, falls eines gesetzt ist.

## Verwandte Seiten

- [Layouts für Designer](layouts-fuer-designer.md)
- [Ein Layout in Templates holen](../vorlagen/ein-layout-in-templates-abrufen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
