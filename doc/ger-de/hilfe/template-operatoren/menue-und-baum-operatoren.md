---
title: Menü- und Baum-Operatoren
audience: developer, designer, app-creator
category: development
order: 330
summary: Die Kernel-Operatoren, die Navigation aufbauen - treemenu (ein Menü, das sich entlang des aktuellen Pfads aufklappt), content_structure_tree (ein verschachtelter Baum von Knoten) und topmenu (die Reiter der Administration aus menu.ini) - mit jedem Parameter, der Form ihrer Rückgabe, ihren Kosten und auf einer Entwicklungsinstallation ausgegebenen Beispielen.
keywords: Menü, Navigation, Baum, Inhaltsstruktur, Reiter, Administration, treemenu, content_structure_tree, topmenu, menu, navigation, tree, menu.ini, TopAdminMenu, eZTreeMenuOperator, eZContentStructureTreeOperator, eZTopMenuOperator
type: help
---
# Menü- und Baum-Operatoren

Drei Kernel-Klassen, registriert in `kernel/common/eztemplateautoload.php`, bauen Navigationsdaten auf. Sie geben
Arrays zurück; das Template macht daraus Markup.

| Operator | Klasse und Datei |
|---|---|
| `treemenu` | `eZTreeMenuOperator`, `kernel/common/eztreemenuoperator.php` |
| `content_structure_tree` | `eZContentStructureTreeOperator`, `kernel/common/ezcontentstructuretreeoperator.php` |
| `topmenu` | `eZTopMenuOperator`, `kernel/common/eztopmenuoperator.php` |

## Inhalt

1. [Die Operatoren](#die-operatoren): [treemenu](#treemenu), [content_structure_tree](#content_structure_tree), [topmenu](#topmenu)
2. [Kosten](#kosten)

## Die Operatoren

### treemenu

Erzeugte Seite: [treemenu](../../beispiele/template-operatoren/treemenu.md).

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `path` | Array | ja | |
| `node_id` | int | nein | `false` (veraltet: ein Wert schreibt nur den Hinweis "Deprecated parameter") |
| `class_filter` | Array | nein | `false` (alle Klassen); ein leeres Array bedeutet die Klassen-ID 1 |
| `depth_skip` | int | nein | `false` (0) |
| `max_level` | int | nein | `false`, wird zu 2 |
| `is_selected_method` | Zeichenkette | nein | `tree` (jeder Knoten auf dem Pfad kann markiert werden); `node` markiert nur den letzten Knoten des Pfads |
| `indentation_level` | int | nein | 15 |
| `language` | Zeichenkette oder Array | nein | `false` |
| `load_data_map` | boolescher Wert | nein | `null`: die Data-Maps laden, wenn eine Ebene höchstens 15 Kinder hat |

- **Eingabe:** keine (rufen Sie ihn wie eine Funktion auf).
- **Pfad:** eine Liste von Hashes mit `url` (`/content/view/full/<node id>`) und `node_id`, die Form von
  `$module_result.path` auf Inhaltsseiten. Die Knoten-ID wird aus dem fünften Teil von `url` gelesen; hat der letzte
  Eintrag keine `url`, aber eine `node_id`, wird die URL daraus gebildet.
- **Gibt zurück:** eine flache Liste von Einträgen. Jeder hat `id`, `level`, `class_name`, `is_main_node`,
  `has_children`, `indent` (`(level - 1) * indentation_level`), `url_alias` (mit führendem `/`), `url`, `text` (den
  Namen), `is_selected` und `node` (das Knotenobjekt). Die Kinder jedes Pfadknotens werden direkt nach ihm eingefügt,
  die Liste steht also bereits in Menüreihenfolge.

Ab `path[depth_skip]` holt der Operator für bis zu `max_level` Ebenen die Kinder jedes Pfadknotens (so sortiert, wie
dieser Knoten seine Kinder sortiert, nach Klasse gefiltert). Ist der Pfad kürzer als eine Ebene, listet er die Kinder
von Knoten 2 auf.

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 144 ) )}
{def $path = $node.path|extract_left( 0 )}
{foreach $node.path as $p}{set $path = $path|append( hash( 'url', concat( '/content/view/full/', $p.node_id ), 'node_id', $p.node_id ) )}{/foreach}
{set $path = $path|append( hash( 'url', false(), 'node_id', 144 ) )}
{foreach treemenu( $path, 144, array( 'ng_frontpage', 'ng_category', 'ng_landing_page' ), 1, 3 ) as $item}
{$item.level} {$item.indent} {$item.id} {$item.text}{if $item.is_selected} (selected){/if} {$item.url_alias}

{/foreach}
path: {foreach $path as $p}{$p.url} {/foreach}
node_id is {$node.node_id|get_type}, the id taken from a URL is {'/content/view/full/144'|explode( '/' )|extract( 4, 1 )|implode( '' )|get_type}
```

Ausgabe:

```text
0 -15 92 Fitness /fitness
0 -15 130 Healthy eating /healthy-eating
0 -15 144 Recipes /recipes
0 -15 164 Video /video
0 -15 192 Showcase /showcase
0 -15 26210 Tour /exponential-live
path: /content/view/full/2 /content/view/full/89  node_id is integer, the id taken from a URL is string[3]
```

Kein Eintrag wurde als ausgewählt markiert. `is_selected` wird gesetzt, wenn die aus der URL des **nächsten**
Pfadeintrags gelesene Knoten-ID identisch (`===`) mit der `node_id` des Kindes ist. Die aus einer URL gelesene ID ist
eine Zeichenkette, das Attribut `node_id` dagegen eine Ganzzahl, wie die letzte Zeile zeigt, daher trifft der strenge
Vergleich hier nicht zu. Markieren Sie den aktuellen Eintrag stattdessen im Template, indem Sie `$item.id` mit der
aktuellen Knoten-ID vergleichen.

### content_structure_tree

Erzeugte Seite: [content_structure_tree](../../beispiele/template-operatoren/content_structure_tree.md).

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `root_node_id` | int | ja | 0 |
| `class_filter` | Array von Klassenbezeichnern | nein | `false` |
| `max_depth` | int | nein | 0 (keine Grenze) |
| `max_nodes` | int | nein | 0 (keine Grenze) |
| `sort_by` | Array von `'<field>/ascending'` oder `'<field>/descending'` | nein | die Sortierung des jeweiligen Elternknotens |
| `fetch_hidden` | bool | nein | `false` |
| `unfold_node_id` | int | nein | 0 (alles aufklappen) |

- **Eingabe:** keine.
- **Gibt zurück:** `false`, wenn die Wurzel nicht gelesen werden kann, verborgen ist (ohne `fetch_hidden`) oder ihre
  Klasse nicht in `class_filter` steht; sonst einen verschachtelten Hash: `parent_node` (mit `node`: `node_id`,
  `path_identification_string`, `children_count`, `sort_array`, `path_string`, `depth`, `is_hidden`, `is_invisible`;
  `classes_js_array`; und `object`: `id`, `name`, `class_identifier`, `class_name`, `published`, `is_container`,
  `language_js_array`) und `children`, eine Liste mit derselben Struktur.

Der Klassenfilter gilt auch für die Wurzel: Lassen Sie die Klasse der Wurzel weg, erhalten Sie `false`. `max_depth`
zählt die Wurzel mit, `2` bedeutet also die Wurzel und ihre Kinder. `unfold_node_id` klappt unterhalb der ersten Ebene
nur dieses Kind auf. Die Kinder werden mit SQL gelesen, das die Leseeinschränkungen des aktuellen Benutzers anwendet,
nur veröffentlichte und sichtbare Knoten.

```tpl
{def $tree = content_structure_tree( 89, array( 'ng_frontpage', 'ng_category', 'ng_landing_page', 'ng_topic' ), 2, 8, array( 'name/ascending' ) )}
root: {$tree.parent_node.node.node_id} {$tree.parent_node.object.name} ({$tree.parent_node.object.class_identifier}), {$tree.parent_node.node.children_count} children

{foreach $tree.children as $child}
- {$child.parent_node.node.node_id} {$child.parent_node.object.name} [{$child.parent_node.object.class_name}] depth {$child.parent_node.node.depth}

{/foreach}
```

Ausgabe:

```text
root: 89 Fit & Healthy (ng_frontpage), 16 children
- 92 Fitness [Landing page] depth 3
- 191 HIIT [Topic] depth 3
- 190 Health [Topic] depth 3
- 130 Healthy eating [Landing page] depth 3
- 144 Recipes [Category] depth 3
- 163 Running [Topic] depth 3
- 192 Showcase [Landing page] depth 3
```

`max_nodes` 8 beendete die Liste nach der Wurzel und sieben Kindern, obwohl die Wurzel 16 hat.

### topmenu

Erzeugte Seite: [topmenu](../../beispiele/template-operatoren/topmenu.md).

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `context` | Zeichenkette | ja | `content` |
| `filter_on_access` | bool | nein | `false` |

- **Eingabe:** keine.
- **Gibt zurück:** die Reiter der Administration als Liste von Hashes mit `access`, `url`, `enabled`, `name`,
  `tooltip`, `icon`, `navigationpart_identifier` und `position` (`first`, `middle`, `last`). Eine leere Liste (und
  einen Fehler im Protokoll), wenn `menu.ini` kein `[TopAdminMenu] Tabs[]` hat.

Für jede ID in `[TopAdminMenu] Tabs[]` (ohne `HiddenTabs[]` und ohne `SetupTabs[]`, wenn der Benutzer `setup/menu`
öffnen darf) liest er die Gruppe `[Topmenu_<id>]`: `Shown[<context>]=false` lässt den Reiter weg, `URL[<context>]`
oder `URL[default]` liefert die Adresse, `Enabled[...]` den Aktivierungszustand, `Name` und `Tooltip` werden im
`TranslationContext` übersetzt (Standard `design/admin/pagelayout`), `Icon` und `NavigationPartIdentifier` werden
weitergegeben. Einträge in `PolicyList[]` sind entweder `module/function` (mit `hasAccessTo()` geprüft) oder eine
Knoten-ID (der Knoten muss lesbar sein); ist `[MenuAccessSettings] CheckViewAccess` nicht abgeschaltet, muss auch die
Ansicht hinter der URL für den Benutzer zugänglich sein. Mit `filter_on_access` werden die Reiter ohne Zugriff
entfernt.

Mit dem Siteaccess `admin` ausgegeben. Die Kommandozeile läuft als anonymer Benutzer, daher hat jeder Reiter
`access=no`, und die gefilterte Liste ist leer:

```tpl
{foreach topmenu( 'content' ) as $tab}
{$tab.position}: {$tab.name} -> {$tab.url} enabled={$tab.enabled|choose( 'no', 'yes' )} access={$tab.access|choose( 'no', 'yes' )} part={$tab.navigationpart_identifier}

{/foreach}
shown to this user: {topmenu( 'content', true() )|count}
```

Ausgabe:

```text
first: Dashboard -> content/dashboard enabled=yes access=no part=ezmynavigationpart
middle: Content structure -> content/view/full/2 enabled=yes access=no part=ezcontentnavigationpart
middle: Media -> content/view/full/43 enabled=yes access=no part=ezmedianavigationpart
middle: Users -> content/view/full/5 enabled=yes access=no part=ezusernavigationpart
middle: Store -> shop/dashboard enabled=yes access=no part=ezshopnavigationpart
middle: Layouts -> explayouts_ui/dashboard enabled=yes access=no part=ezexplayoutsuinavigationpart
middle: Setup -> setup/menu enabled=yes access=no part=ezsetupnavigationpart
middle: Tags -> tags/dashboard enabled=yes access=no part=eztagsnavigationpart
middle: Design -> content/view/full/58 enabled=yes access=no part=ezvisualnavigationpart
middle: Audit -> audit/dashboard enabled=yes access=no part=expauditnavigationpart
middle: Git -> git_manager/dashboard enabled=yes access=no part=gitmanagernavigationpart
middle: Export -> xrowextract/csv enabled=yes access=no part=ezextractnavigationpart
middle: CIE -> bccie/overview enabled=yes access=no part=ezbccienavigationpart
middle: Newsletter -> newsletter/index enabled=yes access=no part=eznewsletternavigationpart
last: Syndication -> syndication/menu enabled=yes access=no part=ezsyndicationnavigationpart
shown to this user: 0
```

## Kosten

- `treemenu` führt pro Pfadebene einen Abruf der Kinder aus und lädt standardmäßig die Data-Maps kleiner Ebenen.
  Fassen Sie Menüs in einen `cache-block` mit dem Pfad als Schlüssel.
- `content_structure_tree` mit `max_depth` und `max_nodes` 0 durchläuft den gesamten Teilbaum unter der Wurzel, mit
  mindestens einer Abfrage pro Knoten. Geben Sie auf einer öffentlichen Seite immer beide Grenzen an.
- `topmenu` prüft bei jedem Aufruf die Richtlinien jedes Reiters.

## Verwandte Seiten

- [Menüs und Navigation](../designer/vorlagen/menues-und-navigation.md)
- [Modulergebnis und Inhaltsinformationen](../designer/vorlagen/module-result-und-content-info.md)
- [Cache-Blöcke](../designer/vorlagen/cache-block.md)
- [Alle Template-Operatoren (erzeugt)](../../beispiele/template-operatoren/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
