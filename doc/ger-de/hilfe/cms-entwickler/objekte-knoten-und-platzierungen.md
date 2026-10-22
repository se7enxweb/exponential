---
title: Objekte, Knoten und Platzierungen
audience: cms-developer
category: cms-development
order: 9
summary: Für CMS-Entwickler der Unterschied zwischen einem Inhaltsobjekt und seinen Knoten (Platzierungen), der Hauptknoten, der Inhaltsbaum, die Sortierung, das Verbergen und das Entfernen. Die Seite behandelt auch, welche ID Sie in Templates, PHP und Importen verwenden.
keywords: Inhaltsobjekt, Knoten, Platzierung, Hauptknoten, Inhaltsbaum, übergeordneter Knoten, Pfad, Tiefe, Priorität, Sortierung, verborgen, unsichtbar, Papierkorb, mehrere Platzierungen, Knoten-ID, Objekt-ID, Remote-ID, content object, node, location, main node, path string, depth, priority, sort order, hidden, invisible, trash, node id, object id, remote id
type: help
---
# Objekte, Knoten und Platzierungen

Diese Seite richtet sich an Entwickler, die von Systemen kommen, in denen eine Seite ihr Platz im Menü *ist*. In
Exponential sind der Inhalt und sein Platz zwei verschiedene Dinge: Das **Objekt** enthält den Inhalt, seine **Knoten**
(Platzierungen) setzen ihn in den Baum. Wer weiß, was er in einem Template oder in PHP gerade in der Hand hat,
vermeidet die meisten Anfängerfehler.

## Inhalt

1. [Objekt und Knoten nebeneinander](#objekt-und-knoten-nebeneinander)
2. [Der Hauptknoten und weitere Platzierungen](#der-hauptknoten-und-weitere-platzierungen)
3. [Der Baum: Pfade, Tiefe und die festen Spitzen](#der-baum-pfade-tiefe-und-die-festen-spitzen)
4. [Sortierung der Unterelemente](#sortierung-der-unterelemente)
5. [Verborgen und unsichtbar](#verborgen-und-unsichtbar)
6. [Verschieben, Kopieren und Entfernen](#verschieben-kopieren-und-entfernen)
7. [Welche ID wo](#welche-id-wo)
8. [In Templates und in PHP](#in-templates-und-in-php)

## Objekt und Knoten nebeneinander

| | Inhaltsobjekt | Knoten (Platzierung) |
|---|---|---|
| Enthält | Versionen, Übersetzungen, Attributwerte, Eigentümer, Sektion, Zustände | einen Platz im Baum: übergeordneter Knoten, Pfad, Tiefe, Priorität, Sichtbarkeit |
| Tabelle | `ezcontentobject` (plus Versionen und Attribute) | `ezcontentobject_tree` |
| PHP-Klasse | `eZContentObject` | `eZContentObjectTreeNode` |
| Wie viele | eins | einer oder mehrere je veröffentlichtem Objekt |
| Hat eine URL | über seine Knoten | ja: jeder Knoten hat einen URL-Alias |

Eine Seitenansicht auf der Website ist immer die Ansicht eines **Knotens**: `/content/view/full/<node id>` oder der
URL-Alias des Knotens. Der Knoten liefert Ihnen sein Objekt (`$node.object`), und das Objekt liefert Ihnen seine Knoten
(`assigned_nodes`, `main_node`).

## Der Hauptknoten und weitere Platzierungen

Ein Objekt kann in mehreren Teilen des Baums platziert werden: ein Produkt unter zwei Kategorien, eine Person unter
der Teamseite und der Kontaktseite. Jeder Platz ist ein Knoten; einer davon ist der **Hauptknoten** (`main_node_id`).
Redakteure fügen Platzierungen in der Administration auf der Registerkarte **Orte** des Objekts hinzu.

- Alle Knoten zeigen dasselbe Objekt, eine Bearbeitung erscheint also überall zugleich.
- Der Hauptknoten liefert die kanonische Adresse. Verlinken Sie mit dem Hauptknoten und setzen Sie damit kanonische
  URLs: `$node.object.main_node.url_alias` in einem Template
  ([Knoten, Objekte und Attribute](../designer/vorlagen/knoten-objekte-und-attribute.md#mehrere-orte)).
- Fetches geben Knoten zurück; eine Liste, die zwei Platzierungen desselben Objekts umfasst, liefert es zweimal,
  wenn Sie nicht `main_node_only` übergeben ([Inhalte in Templates abrufen](inhalte-in-templates-abrufen.md)).

Verwenden Sie weitere Platzierungen dort, wo der Inhalt wirklich an zwei Stellen hingehört. Für Kästen mit
weiterführender Lektüre verwenden Sie stattdessen Objektrelationen ([Relationen und Einbettungen](relationen-und-einbettungen.md)).

## Der Baum: Pfade, Tiefe und die festen Spitzen

Jeder Knoten speichert seinen `path_string` (die Knoten-IDs von oben, zum Beispiel `/1/2/61/74/`) und seine `depth`.
Damit werden Fetches und Berechtigungen auf Teilbäumen schnell. Der mitgelieferte Baum hat feste Spitzen, festgelegt
in `settings/content.ini [NodeSettings]`:

| Einstellung | Knoten | Enthält |
|---|---|---|
| `RootNode` | 2 | die Inhalte der Website |
| `UserRootNode` | 5 | Benutzer und Benutzergruppen |
| `MediaRootNode` | 43 | die Medienbibliothek |
| `SetupRootNode` | 48 | Setup-Objekte |
| `DesignRootNode` | 58 | Design-Objekte |

Ein Siteaccess kann seine Website tiefer im Baum beginnen lassen; so teilen sich mehrere Websites einen Baum
([Siteaccesses und Multisite](siteaccesses-und-multisite.md)).

## Sortierung der Unterelemente

Jeder Knoten speichert, wie seine Unterelemente sortiert werden (`sort_field`, `sort_order`); neue Knoten übernehmen
den Standard ihrer Klasse. Die Sortierfelder sind Konstanten von `eZContentObjectTreeNode` (`SORT_FIELD_PATH`,
`PUBLISHED`, `MODIFIED`, `SECTION`, `DEPTH`, `CLASS_IDENTIFIER`, `CLASS_NAME`, `PRIORITY`, `NAME`,
`MODIFIED_SUBNODE`, `NODE_ID`, `CONTENTOBJECT_ID`, `VISIBILITY`). Bei Sortierung nach Priorität legen Redakteure die
Reihenfolge in der Liste der Unterelemente von Hand fest. In einem Template liefert `$node.sort_array` die eigene
Reihenfolge des Knotens, die Sie an einen Fetch übergeben, sodass Ihr Menü der Wahl der Redakteure folgt.

## Verborgen und unsichtbar

| Kennzeichen | Bedeutung |
|---|---|
| `is_hidden` | ein Redakteur hat diesen Knoten verborgen |
| `is_invisible` | dieser Knoten ist verborgen oder liegt unter einem verborgenen Knoten |

Besucher sehen unsichtbare Knoten nicht, solange `site.ini [SiteAccessSettings] ShowHiddenNodes=false` gilt, der
mitgelieferte Wert; der Administrations-Siteaccess zeigt sie meist an. Verbergen ist keine Berechtigung: Ein
verborgener Knoten bleibt für jeden lesbar, der `content/read` auf einem Siteaccess hat, der verborgene Knoten zeigt.
Für echte Zugriffskontrolle verwenden Sie Sektionen und Rollen
([Sektionen, Rollen und Richtlinien](sektionen-rollen-und-richtlinien.md)).

## Verschieben, Kopieren und Entfernen

- **Verschieben** ändert den übergeordneten Knoten eines Knotens; die URL-Aliase des Teilbaums folgen, und die alten
  Adressen leiten weiterhin weiter ([URL-Aliase und Routing](url-aliase-und-routing.md)).
- **Kopieren** erzeugt ein neues Objekt (mit neuer Objekt-ID) und einen Knoten dafür; ein Teilbaum lässt sich mit
  `bin/php/ezsubtreecopy.php` kopieren.
- **Entfernen** eines Knotens: Hat das Objekt weitere Platzierungen, verschwindet nur diese Platzierung. War es die
  letzte, kommt das Objekt standardmäßig in den **Papierkorb** und lässt sich von dort wiederherstellen
  ([Veröffentlichen, Versionen und der Papierkorb](../veroeffentlichen-und-versionen.md#entfernen-und-wiederherstellen)).

Verwenden Sie aus PHP die Kernel-Operationen, damit Caches, Suche und URL-Aliase folgen: siehe
[Inhalte aus PHP](inhalte-aus-php.md).

## Welche ID wo

| Sie brauchen | Verwenden Sie | Warum |
|---|---|---|
| Einen Link in einem Template | `$node.url_alias` (Hauptknoten für kanonische Links) | Adressen folgen Verschiebungen und Umbenennungen |
| Einen festen Startpunkt in Einstellungen | eine Knoten-ID in einer Installation oder eine eigene Einstellung, gelesen mit `ezini` | IDs unterscheiden sich zwischen Installationen |
| Einen Verweis aus Code oder einem Import | die **Remote-ID** des Objekts | stabil und gleich über Exporte und erneute Importe hinweg |
| Eine Relation zwischen Objekten | Objekt-IDs (der Datentyp speichert sie) | Relationen zeigen auf Objekte, nicht auf Plätze |

[Eine Knoten-ID finden](../zwei-wege-zum-bauen/knoten-id-finden.md) zeigt, wo Sie IDs in der Administration ablesen.

## In Templates und in PHP

In einem Template für eine Knotenansicht ist der aktuelle Knoten `$node`:

```smarty
<h1>{$node.name|wash()}</h1>
<p>Object {$node.contentobject_id}, node {$node.node_id}, depth {$node.depth}</p>
{if $node.object.assigned_nodes|count()|gt( 1 )}
    <link rel="canonical" href={$node.object.main_node.url_alias|ezurl( , 'full' )} />
{/if}
```

In PHP:

```php
<?php
$node = eZContentObjectTreeNode::fetch( 2 );        // a node by node ID
$object = $node->object();                          // its object
$main = $object->mainNode();                        // the main node
$all = $object->assignedNodes();                    // every location
$byRemote = eZContentObject::fetchByRemoteID( 'expsite-about-page' );
```

[eZContentObjectTreeNode](../erweiterungsentwicklung/teil-8-referenz/ezcontentobjecttreenode.md) und
[eZContentObject](../erweiterungsentwicklung/teil-8-referenz/ezcontentobject.md) im Buch listen die Methoden auf.

## Verwandte Seiten

- [Das Inhaltsmodell](das-inhaltsmodell.md)
- [Referenz der Knotenattribute](../template-sprache/referenz-der-knotenattribute.md)
- [Inhalte abbilden: Platzierungen](../../installation/migration/inhaltsmodell-zuordnen.md#4-orte-der-inhaltsbaum)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
