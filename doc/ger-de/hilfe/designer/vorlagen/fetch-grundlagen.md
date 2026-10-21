---
title: Fetch-Grundlagen
audience: designer, developer
category: templates
order: 400
summary: Wie Templates Daten mit fetch( module, function, parameters ) lesen - die Module mit Fetch-Funktionen, die meistgenutzten Fetches für Inhalte und Benutzer, Parameter als Hash, was zurückkommt und wo die Funktionen jedes Moduls definiert sind.
keywords: fetch, Fetch-Funktion, fetch content node, fetch content list, fetch user current_user, function_definition, Template-Daten, Inhalte lesen, Fetch-Parameter, fetch function, template data, read content, fetch parameters
type: help
---
# Fetch-Grundlagen

Diese Seite ist für Designer, die Daten brauchen, die einem Template nicht übergeben wurden: die Kinder eines Ordners, die
neuesten Nachrichten, den aktuellen Benutzer. Templates lesen Daten nur über **Fetch-Funktionen**.

## Inhalt

1. [Der Aufruf](#der-aufruf)
2. [Die Fetches, die Designer am häufigsten verwenden](#die-fetches-die-designer-am-häufigsten-verwenden)
3. [Wo Fetch-Funktionen definiert sind](#wo-fetch-funktionen-definiert-sind)
4. [Was zurückkommt](#was-zurückkommt)
5. [Regeln](#regeln)

## Der Aufruf

```tpl
{def $news = fetch( 'content', 'list', hash( 'parent_node_id', 61,
                                             'sort_by', array( 'published', false() ),
                                             'limit', 5 ) )}
```

`fetch( <module>, <function>, <parameters as a hash> )`. Nicht angegebene Parameter nehmen ihre Standardwerte an;
Pflichtparameter müssen angegeben werden.

## Die Fetches, die Designer am häufigsten verwenden

| Aufruf | Gibt zurück |
|---|---|
| `fetch( 'content', 'node', hash( 'node_id', 2 ) )` | einen Knoten (auch über `node_path` oder `remote_id`) |
| `fetch( 'content', 'list', hash( 'parent_node_id', ..., ... ) )` | die Kinder eines Knotens |
| `fetch( 'content', 'list_count', hash( ... ) )` | wie viele Kinder (zum Blättern) |
| `fetch( 'content', 'tree', hash( ... ) )`, `tree_count` | alle Nachkommen, mit Tiefengrenzen |
| `fetch( 'content', 'object', hash( 'object_id', ... ) )` | ein Inhaltsobjekt |
| `fetch( 'content', 'related_objects', hash( ... ) )` | verwandte Objekte |
| `fetch( 'content', 'search', hash( 'text', ... ) )` | Suchergebnisse |
| `fetch( 'content', 'translation_list' )` | die Sprachen der Website |
| `fetch( 'user', 'current_user' )` | den angemeldeten Benutzer (im Pagelayout auch `$current_user`) |
| `fetch( 'user', 'has_access_to', hash( 'module', ..., 'function', ... ) )` | Berechtigungsprüfung |
| `fetch( 'explayouts', 'resolve_layout', hash() )` | das Layout der Seite ([Ein Layout in Templates abrufen](ein-layout-in-templates-abrufen.md)) |

## Wo Fetch-Funktionen definiert sind

Jedes Modul definiert seine Fetch-Funktionen in `function_definition.php`: `kernel/content/function_definition.php` (node,
list, tree, search und etwa fünfzig weitere), `kernel/user/function_definition.php` (current_user, has_access_to, can_open,
member_of ...) und Erweiterungen in `extension/<ext>/modules/<module>/function_definition.php` (zum Beispiel die der
Layouts in `extension/explayouts/modules/explayouts/function_definition.php`). Jeder Eintrag listet seine Parameter mit
Namen, Typen und ob sie Pflicht sind.

## Was zurückkommt

- `node` gibt ein Knotenobjekt zurück oder `false`, wenn er nicht existiert oder der Benutzer ihn nicht lesen darf.
- `list` und `tree` geben Arrays von Knoten zurück (oder einfache Arrays mit `'as_object', false()`).
- Zählfunktionen geben Zahlen zurück.

Prüfen Sie einzelne Ergebnisse immer vor der Verwendung: `{if $node}...{/if}`.

## Regeln

- Rufen Sie einmal ab und behalten Sie das Ergebnis in einer Variablen ([Fetch-Leistung](fetch-leistung.md)).
- Jeder Listen-Fetch hat ein `limit`.
- Inhalte, die der Benutzer nicht lesen darf, werden standardmäßig aus den Ergebnissen ausgelassen; umgehen Sie das nicht.
- Finden Sie besondere Knoten über die `remote_id`, nicht über eine fest eingetragene Knoten-ID, die sich zwischen
  Installationen unterscheidet.

## Verwandte Seiten

- [Knoten und Listen abrufen](knoten-und-listen-abrufen.md)
- [Fetch-Filter und Sortierung](fetch-filter-und-sortierung.md)
- [Knoten, Objekte und Attribute](knoten-objekte-und-attribute.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
