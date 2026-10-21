---
title: Eingebaute Template-Funktionen
audience: developer, designer
category: extension-development
order: 280
summary: Die Template-Funktionen der Engine - Variablen, Bedingungen, Schleifen, Einbindungen, Blöcke, Caching, Debugging - und die View-Funktionen des Kernels wie node_view_gui, aus der laufenden Engine aufgelistet.
keywords: Template-Funktionen, Variablen, Bedingungen, Schleifen, Einbindungen, Blöcke, Caching, Debugging, template functions, def, undef, set, let, if, switch, foreach, for, while, do, include, set-block, append-block, run-once, cache-block, debug-timing-point, node_view_gui, attribute_view_gui, attribute_edit_gui, content_view_gui
type: help
---
# Eingebaute Template-Funktionen

Teil 8, Referenz 24. Template-Funktionen sind die Konstrukte `{name ...}...{/name}` der Template-Sprache. Wie die
Operatoren auf der vorigen Seite wurde diese Liste der Template-Engine einer laufenden Installation entnommen
(Oktober 2026). Die Verwendung mit Beispielen steht in der Hilfe für Designer:
[Funktionsreferenz](../../designer/vorlagen/funktionsreferenz.md).

## Variablen

| Funktion | Verwendung |
|---|---|
| `{def $name=value}`, `{undef $name}` | eine Variable für dieses Template definieren; am Ende wieder aufheben |
| `{set $name=value}` | eine definierte Variable ändern |
| `{let}`, `{default}` | ältere Formen, für alte Templates erhalten; verwenden Sie in neuen `def` |

## Bedingungen

| Funktion | Verwendung |
|---|---|
| `{if}`, `{elseif}`, `{else}`, `{/if}` | Bedingungen |
| `{switch match=...}`, `{case}`, `{/switch}` | einer von mehreren Werten |

## Schleifen

| Funktion | Verwendung |
|---|---|
| `{foreach $list as $key => $item}` | über ein Array laufen; Parameter `offset`, `max`, `reverse` und darin `{delimiter}`, `{skip}`, `{break}`, `{continue}` |
| `{for 1 to 10 as $i}` | zählen |
| `{while}`, `{do}` | Schleifen mit einer Bedingung |
| `{section}` | eine alte Schleifenform, für alte Templates erhalten |
| `{sequence}` | reihum durch Werte gehen (Zeilenklassen) |

Beachten Sie die Parametersyntax von `foreach`: `{foreach $items as $item offset 1 max 3}` (ohne `=`).

## Einbinden und Blöcke

| Funktion | Verwendung |
|---|---|
| `{include uri='design:parts/menu.tpl' name=value}` | ein anderes Template mit Variablen rendern |
| `{set-block variable=$name}...{/set-block}`, `{append-block}` | Ausgabe in einer Variablen auffangen |
| `{run-once}` | etwas einmal je Seite ausgeben (ein Script-Tag) |
| `{ldelim}`, `{rdelim}` | die Zeichen `{` und `}` selbst |

## Caching

`{cache-block keys=... expiry=... subtree_expiry=...}` cacht seine Ausgabe
([Cache-Tags, Cache-Blöcke und das Leeren von Caches](cache-tags-und-leeren.md)).

## Debugging

`{debug-timing-point id=...}`, `{debug-accumulator id=...}`, `{debug-log var=...}`, `{debug-trace}`: schreiben in die
Debug-Ausgabe.

## Views von Inhalten (Kernel)

| Funktion | Rendert |
|---|---|
| `{node_view_gui content_node=$node view='line'}` | einen Knoten in einem Ansichtsmodus, mit Override-Regeln |
| `{content_view_gui content_object=$object view='embed'}` | ein Objekt in einem Ansichtsmodus |
| `{attribute_view_gui attribute=$node.data_map.title}` | das View-Template eines Attributs (das seines Datentyps) |
| `{attribute_edit_gui attribute=$attribute}` | das Bearbeitungsformular eines Attributs |
| `{class_attribute_view_gui ...}`, `{class_attribute_edit_gui ...}` | Einstellungen von Klassenattributen |
| `{content_version_view_gui ...}`, `{attribute_diff_gui ...}`, `{attribute_result_gui ...}`, `{attribute_pdf_gui ...}`, `{content_pdf_gui ...}` | Versionen, Unterschiede, gesammelte Ergebnisse, PDF |
| `{event_view_gui ...}`, `{event_edit_gui ...}` | Views von Workflow-Ereignissen |
| `{related_view_gui ...}`, `{shop_account_view_gui ...}` | Beziehungen, Shop-Konten |
| `{collaboration_view_gui ...}` und die übrigen Collaboration-Funktionen | das Collaboration-System |
| `{tool_bar ...}`, `{menu ...}` | Werkzeugleisten und Menüs der Administration |

Die View-Funktionen sind der Grund, warum Templates Attributwerte nie von Hand ausgeben sollten: Sie wählen das
richtige Template für den Datentyp und den Ansichtsmodus, und Designs können es überschreiben.

## Funktionen mitgelieferter Erweiterungen

ezjscore fügt seine Template-Funktionen als Operatoren hinzu (`ezscript`, `ezcss` und die übrigen, vorige Seite);
Erweiterungen wie die Layouts-Suite fügen ihre eigenen Blockfunktionen hinzu, wenn sie aktiv sind.

## Verwandte Seiten

- Zurück: [Eingebaute Template-Operatoren](referenz-der-template-operatoren.md)
- Weiter: [Eingebaute Fetch-Funktionen nach Modul](referenz-der-fetch-funktionen.md)
- [Template-Funktionen](../teil-3-erweiterungsentwickler/template-funktionen.md)
- [Templates für Builder](../teil-2-builder/templates-fuer-builder.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
