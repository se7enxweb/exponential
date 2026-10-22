---
title: "Inhalte in Templates abrufen"
audience: builder, designer, developer
category: books
order: 420
summary: Die Funktionen des Moduls content, mit denen Templates Daten holen (ein Knoten, ein Objekt, Listen, Bäume, Anzahlen, Versionen, der aktuelle Benutzer), als heutige fetch-Aufrufe geschrieben.
keywords: fetch, Modul content, Knoten, Objekt, Liste, Baum, Anzahl, Sortierung, aktueller Benutzer, Version, Buch Learning, content module, node, object, list, tree, list_count, tree_count, class_filter, sort_by, current_user, version, Learning book
type: help
---
# Inhalte in Templates abrufen

Ein Template kann die Module des Kernels mit `fetch( 'module', 'function', hash( ... ) )` nach Daten fragen. Das Ergebnis
ist ein Wert, den Sie einer Variablen zuweisen. Die Funktionen des Moduls `content` erledigen die meiste Arbeit.

## Ein Knoten oder ein Objekt

```
{def $node=fetch( 'content', 'node', hash( 'node_id', 2 ) )
     $object=fetch( 'content', 'object', hash( 'object_id', 4 ) )}
```

Dieselbe Funktion nimmt mit `fetch( 'content', 'node', hash( 'node_path', 'about-us' ) )` einen Knoten über seine
Adresse entgegen (siehe [fetch für Builder](../erweiterungsentwicklung/teil-2-builder/fetch-fuer-builder.md)).

## Listen und Bäume

`list` holt die Unterelemente eines Knotens; `tree` holt alles darunter:

```
{def $items=fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                            'class_filter_type', 'include',
                                            'class_filter_array', array( 'article' ),
                                            'sort_by', array( 'published', false() ),
                                            'limit', 10 ) )}
```

Die üblichen Parameter:

| Parameter | Bedeutung |
|---|---|
| `parent_node_id` | der Knoten, dessen Unterelemente (oder Baum) Sie wollen |
| `class_filter_type`, `class_filter_array` | die Klassen (nach Bezeichner) einschließen (`include`) oder ausschließen (`exclude`) |
| `sort_by` | ein Array: der Schlüssel (`published`, `name`, `priority`, `modified` oder ein Attribut) und aufsteigend als `true()` oder `false()` |
| `limit`, `offset` | wie viele, ab wo (zum Blättern) |
| `depth`, `main_node_only` | wie tief, und nur Hauptorte (bei `tree`) |
| `attribute_filter` | nach Attributwerten filtern |

Zählen Sie zuerst, wenn Sie die Gesamtzahl brauchen: `list_count` und `tree_count` nehmen dieselben Filterparameter und
liefern nur die Zahl (das Buch nennt das, die Objekte bestimmter Typen zu zählen). Die Kombination von `limit` mit der
Anzahl ergibt das Blättern.

## Weitere Funktionen, die das Buch nennt

- **Versionsinformationen**: `fetch( 'content', 'version', hash( 'object_id', ..., 'version_id', ... ) )` und die
  Liste der Versionen (`version_list`) für eine Änderungshistorie.
- **Der aktuelle Benutzer**: `fetch( 'user', 'current_user' )`; sein Inhaltsobjekt ist `$user.contentobject`.
- **Verknüpfte Objekte** (`related_objects`), **umgekehrt verknüpfte** (`reverse_related_objects`), über
  **Schlüsselwörter verwandte** Objekte.
- **Suche**: `fetch( 'content', 'search', hash( 'text', $q ) )`.
- **Papierkorb, Entwürfe, ausstehende Einträge, Lesezeichen, zuletzt bearbeitete Einträge, Sektionsliste** und vieles
  mehr.

Jede Funktion und ihre Parameter stehen in [Fetch-Funktionen: wie sie funktionieren](../template-operatoren/wie-fetch-funktionen-arbeiten.md)
und [Fetch-Aliase](../template-operatoren/fetch-aliase.md).

## Aufwand

Jeder fetch ist eine Datenbankabfrage. Rufen Sie nur ab, was Sie zeigen, setzen Sie `limit`, rufen Sie nie einen Baum ab,
nur um ihn zu zählen, und legen Sie das Ergebnis aufwendiger Abrufe in einen [Cache-Block](caching-in-templates.md).

Weiter: [Caching in Templates](caching-in-templates.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 3, „Displaying Content with eZ publish Templates“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Syntax, Operatoren, Einstellungen und Beispiele auf Exponential 6 aktualisiert (foreach, aktuelle Einstellungen, kein Inline-Stil und kein Inline-Skript).
