---
title: Fetch-Aliase
audience: developer, designer, app-creator
category: development
order: 600
summary: Was fetch_alias() tut, die Syntax der fetchalias.ini (Module, FunctionName, Parameter[], Constant[]), jeder auf alpha definierte Alias mit seinem Ziel und einem auf alpha gerenderten Beispiel, wie Parameter und Konstanten zusammenwirken und wie Sie aus einer Erweiterung heraus einen eigenen Alias anlegen oder einen vorhandenen ändern.
keywords: Fetch-Alias, Fetch-Aliase, Fetch-Funktion, Alias anlegen, Alias ändern, Konstante, fetch_alias, fetchalias.ini, fetch alias, Module, FunctionName, Parameter, Constant, children, children_count, subtree, object, by_identifier, news_list, comments, reviews, xrowextract, eZFunctionHandler::executeAlias
type: help
---
# Fetch-Aliase

`fetch_alias()` ruft eine Fetch-Funktion über einen Kurznamen auf, der in der `fetchalias.ini` definiert ist; einige ihrer
Parameter sind dabei fest vorgegeben, andere umbenannt. `{fetch_alias( 'children', hash( 'parent_node_id', 89 ) )}` entspricht
`{fetch( 'content', 'list', hash( 'parent_node_id', 89, ... ) )}` mit den Einstellungen des Alias. Der Operator gehört zu
`eZTemplateExecuteOperator` (`lib/eztemplate/classes/eztemplateexecuteoperator.php`, dieselbe Klasse wie `fetch`), und die Arbeit
erledigt `eZFunctionHandler::executeAlias()` (`lib/ezutils/classes/ezfunctionhandler.php`).

## Inhalt

1. [Syntax](#syntax)
2. [fetchalias.ini](#fetchaliasini)
3. [Wie Parameter und Konstanten zusammenwirken](#wie-parameter-und-konstanten-zusammenwirken)
4. [Die Aliase auf alpha](#die-aliase-auf-alpha)
5. [Einen eigenen Alias anlegen](#einen-eigenen-alias-anlegen)
6. [Einen vorhandenen Alias ändern](#einen-vorhandenen-alias-ändern)
7. [Wann sich ein Alias lohnt](#wann-sich-ein-alias-lohnt)

## Syntax

```tpl
{fetch_alias( alias_name, hash( key, value, ... ) )}
```

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `function_name` | Zeichenkette, der Abschnittsname in der `fetchalias.ini` | ja | |
| `function_parameters` | Hash | nein | `array()` |

Er nimmt keine Eingabe über die Pipe und gibt zurück, was die Fetch-Funktion zurückgibt (`null`, wenn der Alias nicht existiert).

## fetchalias.ini

Jeder Alias ist ein Abschnitt. Die `settings/fetchalias.ini` des Kernels dokumentiert die Schlüssel:

```ini
# [<parameter>]         # name of first parameter in fetch_alias
# Module=          # which module to use
# FunctionName=    # name of function defined in kernel/<module>/function_definition.php
# Parameter[key]=<value>  # key is the name of the parameter defined for the function in kernel/<module>/function_definition.php
#                         # value is the key of the hash entry in fetch_alias
# Constant[key]=<value>   # key is the name of the parameter defined for the function in kernel/<module>/function_definition.php
#                         # value is a constant defined in this ini file. use ; to separate elements if the value is supposed to be an array
```

- `Module` und `FunctionName` benennen die Fetch-Funktion, genau wie die ersten beiden Argumente von `fetch()`.
- `Parameter[<Funktionsparameter>]=<Alias-Schlüssel>` nimmt den Wert von `<Alias-Schlüssel>` aus dem übergebenen Hash und gibt ihn an
  `<Funktionsparameter>` weiter. Meist sind beide Namen gleich, ein Alias kann sie aber umbenennen: `[object]` bildet den
  Funktionsparameter `object_id` auf den Schlüssel `id` ab.
- `Constant[<Funktionsparameter>]=<Wert>` legt einen Wert fest. Ist der Funktionsparameter in der `function_definition.php` des
  Moduls vom Typ `array`, wird der Wert an `;` aufgeteilt (schreiben Sie `\;` für ein Semikolon innerhalb eines Elements; leere
  Elemente entfallen, `eZFunctionHandler::constantList()`): Aus `Constant[sort_by]=published;0` wird
  `array( 'published', '0' )`.

## Wie Parameter und Konstanten zusammenwirken

`eZFunctionHandler::executeAlias()` stellt die Parameter der Fetch-Funktion in dieser Reihenfolge zusammen:

1. jeder `Parameter[]`-Eintrag: der Wert seines Alias-Schlüssels aus Ihrem Hash oder `null`, wenn Sie ihn nicht übergeben haben
   (ein Parameter mit `null` erhält dann den Standardwert der Funktion);
2. jeder `Constant[]`-Eintrag, es sei denn, Ihr Hash enthält einen Schlüssel mit dem Namen des **Funktionsparameters**, der dann
   Vorrang vor der Konstanten hat;
3. jeder weitere Schlüssel Ihres Hashs, unverändert unter seinem eigenen Namen weitergereicht.

Sie können einem Alias also jeden Parameter der Fetch-Funktion übergeben, auch solche, die der Alias nicht nennt, und seine
Konstanten überschreiben. Der Alias `subtree` sortiert nach `priority;0`; ein `sort_by` im Hash ersetzt das:

```tpl
{* subtree has Constant[sort_by]=priority;0 - a sort_by given to the alias replaces it *}
{foreach fetch_alias( 'subtree', hash( 'parent_node_id', 144, 'limit', 3, 'sort_by', array( 'name', true() ) ) ) as $item}
- {$item.name|wash}

{/foreach}
```

Ausgabe auf alpha:

```text
- Almond French Toast
- Avocado Kale Pesto Grilled Cheese
- Gluten-Free Shrimp Pad Thai with Tofu
```

Kompilierte Templates nehmen für `fetch_alias`-Aufrufe, deren Aliasname eine Konstante ist, einen anderen Weg
(`eZTemplateExecuteOperator::fetchTransform()`): Der Aufruf wird beim Kompilieren anhand der Parameterliste der Funktion in PHP
umgewandelt. Alle Beispiele auf dieser Seite lieferten auf alpha interpretiert und kompiliert dasselbe Ergebnis. Halten Sie sich an
die dokumentierte Verwendung (Alias-Schlüssel aus `Parameter[]`, Namen der Funktionsparameter für alles andere), dann stimmen beide
Wege überein.

## Die Aliase auf alpha

Der Siteaccess `site` von alpha hat die acht Aliase aus der `settings/fetchalias.ini` des Kernels und zwei aus der Erweiterung
xrowextract (`extension/xrowextract/settings/fetchalias.ini.append.php`).

| Alias | Fetch-Funktion | Ihre Schlüssel | Fest vorgegeben |
|---|---|---|---|
| `object` | `content/object` | `id` (als `object_id`) | |
| `news_list` | `content/tree` | | `sort_by` published, absteigend; `class_id` 2; `parent_node_id` 2; `class_filter_type` include; `class_filter_array` 2; `limit` 10 |
| `by_identifier` | `content/object_by_attribute` | `attr_id` (als `identifier`) | |
| `comments` | `content/list` | `parent_node_id` | `sort_by` published, absteigend; nur Klasse `comment` |
| `children` | `content/list` | `parent_node_id`, `sort_by`, `offset`, `limit` | |
| `children_count` | `content/list_count` | `parent_node_id` | |
| `subtree` | `content/tree` | `parent_node_id`, `offset`, `limit` | `sort_by` priority, absteigend |
| `reviews` | `content/list` | `parent_node_id` | `sort_by` published, absteigend; nur Klasse `review` |
| `xrowextract_recent_content` | `content/tree` | `parent_node_id`, `limit` | `sort_by` modified, absteigend |
| `xrowextract_by_section` | `content/tree` | `parent_node_id`, `attribute_filter` | `sort_by` name, aufsteigend |

Die Parameter der Fetch-Funktionen sind unter [den Fetch-Funktionen von content](../../beispiele/fetch-funktionen/content/README.md)
dokumentiert.

### object

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )
     $object = fetch_alias( 'object', hash( 'id', $node.contentobject_id ) )}
{$object.id}: {$object.name|wash} ({$object.class_identifier})
```

Ausgabe auf alpha:

```text
87: Fit &amp; Healthy (ng_frontpage)
```

### children und children_count

```tpl
{def $children = fetch_alias( 'children', hash( 'parent_node_id', 89,
                                                 'sort_by', array( 'name', true() ),
                                                 'limit', 5 ) )}
{fetch_alias( 'children_count', hash( 'parent_node_id', 89 ) )} children, the first {$children|count} by name:
{foreach $children as $child}
- {$child.name|wash} ({$child.class_identifier})

{/foreach}
```

Ausgabe auf alpha:

```text
16 children, the first 5 by name:- Authors (ng_container)
- Contact (ng_contact_form)
- Cookie Policy (ng_article)
- Fit &amp; Healthy site info - [SITEINFO] (ng_site_info)
- Fitness (ng_landing_page)
```

### subtree

Knoten 144 ist die Kategorie Recipes; `subtree` geht beliebig tief und sortiert nach Priorität:

```tpl
{def $page = fetch_alias( 'subtree', hash( 'parent_node_id', 144, 'offset', 0, 'limit', 4 ) )}
{foreach $page as $item}
- {$item.name|wash} (depth {$item.depth})

{/foreach}
```

Ausgabe auf alpha:

```text
- Gluten-Free Shrimp Pad Thai with Tofu (depth 4)
- Vegan Granola with Almonds, Walnuts, Cranberries, and Goji Berries (depth 4)
- Zucchini Zoodles with Ground Turkey Tomato Sauce (depth 4)
- Kale Salad (depth 4)
```

### news_list

Bei `news_list` ist die ganze Abfrage fest vorgegeben: Artikel der Klasse 2 unterhalb von Knoten 2. Auf alpha existiert die
Klasse 2, die Artikel der Website sind aber von der Klasse `ng_article`, daher findet der Alias nichts. Er ist ein Beispiel aus den
ursprünglichen Kernel-Einstellungen, auf das Sie sich nicht verlassen sollten; definieren Sie stattdessen einen eigenen
([siehe unten](#einen-eigenen-alias-anlegen)).

```tpl
{def $class = fetch( 'content', 'class', hash( 'class_id', 2 ) )
     $news = fetch_alias( 'news_list' )}
Class 2 is "{$class.identifier}"; news_list returned {$news|count} item(s).
{foreach $news as $item}
- {$item.name|wash}

{/foreach}
```

Ausgabe auf alpha:

```text
Class 2 is "article"; news_list returned 0 item(s).
```

### by_identifier

`content/object_by_attribute` (`eZContentFunctionCollection::fetchObjectByAttribute()`) gibt über
`eZContentObjectAttribute::fetchByIdentifier()` das Objekt zurück, das ein Textzeilen-Attribut (`ezstring`) hat, dessen
Sortierschlüssel dem Wert entspricht. Der Sortierschlüssel einer Textzeile ist ihr Text in Kleinbuchstaben:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 90 ) )
     $found = fetch_alias( 'by_identifier', hash( 'attr_id', $node.name|downcase ) )}
Looked up "{$node.name|downcase|wash}": {if $found}object {$found.id}, {$found.name|wash}{else}nothing{/if}
```

Ausgabe auf alpha:

```text
Looked up "privacy policy": object 72, Privacy policy
```

Passen mehrere Objekte, gibt er eines davon zurück. Er durchsucht alle Textzeilen-Attribute aller Klassen; verwenden Sie ihn
daher nur mit Werten, von denen Sie wissen, dass sie eindeutig sind.

### comments und reviews

Beide listen die Unterelemente einer Klasse (`comment`, `review`), die neuesten zuerst. Auf alpha wird keine der beiden Klassen
unterhalb von Knoten 89 verwendet:

```tpl
{def $comments = fetch_alias( 'comments', hash( 'parent_node_id', 89 ) )
     $reviews = fetch_alias( 'reviews', hash( 'parent_node_id', 89 ) )}
comments: {$comments|count}, reviews: {$reviews|count}
```

Ausgabe auf alpha:

```text
comments: 0, reviews: 0
```

### xrowextract_recent_content und xrowextract_by_section

Von der Erweiterung xrowextract für ihre Exportansicht definiert und in jedem Template der Siteaccesses verwendbar, in denen die
Erweiterung aktiv ist:

```tpl
{def $recent = fetch_alias( 'xrowextract_recent_content', hash( 'parent_node_id', 144, 'limit', 3 ) )}
{foreach $recent as $item}
- {$item.name|wash}

{/foreach}
```

Ausgabe auf alpha:

```text
- Zucchini Zoodles with Ground Turkey Tomato Sauce
- Vibrant Guacamole
- Vegan Granola with Almonds, Walnuts, Cranberries, and Goji Berries
```

### Ein Alias, der nicht existiert

```tpl
[{fetch_alias( 'latest_news', hash( 'parent_node_id', 89 ) )}]
```

Ausgabe auf alpha:

```text
[]
```

Das Ergebnis ist leer. In einem interpretierten Template schreibt `eZFunctionHandler::executeAlias()` die Warnung
`Could not execute. Function latest_news not found.` in die Debug-Ausgabe; ein kompiliertes Template protokolliert beim Kompilieren
die Template-Warnung `Fetch alias 'latest_news' is not defined in fetchalias.ini`.

## Einen eigenen Alias anlegen

Legen Sie den Abschnitt in der `settings/fetchalias.ini.append.php` Ihrer Erweiterung (oder eines Siteaccess) an, niemals in der
`settings/fetchalias.ini` des Kernels:

```ini
<?php /* #?ini charset="utf-8"?

[c11_latest_articles]
Module=content
FunctionName=list
Parameter[parent_node_id]=parent
Parameter[limit]=limit
Constant[sort_by]=published;0
Constant[class_filter_type]=include
Constant[class_filter_array]=ng_article

*/ ?>
```

Die INI-Dateien aller aktiven Erweiterungen werden zusammengeführt, daher existiert der Alias in jedem Siteaccess, in dem die
Erweiterung aktiv ist. Leeren Sie danach den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`) und den
Template-Cache (kompilierte Templates enthalten die aufgelösten Parameter des Alias):

```tpl
{foreach fetch_alias( 'c11_latest_articles', hash( 'parent', 89, 'limit', 3 ) ) as $article}
- {$article.name|wash}, published {$article.object.published|l10n( 'shortdate' )}

{/foreach}
```

Ausgabe auf alpha:

```text
- Cookie Policy, published 01/25/2023
- Privacy Policy, published 01/25/2023
```

Auf alpha nicht mit einer Datei ausgeführt: Das Skript, das dieses Beispiel gerendert hat, fügte den obigen Abschnitt nur im
Speicher und nur für seinen eigenen Prozess zur `fetchalias.ini` hinzu.

Versehen Sie Aliasnamen mit dem Präfix Ihrer Erweiterung: Aliasnamen teilen sich über alle Erweiterungen hinweg einen
INI-Namensraum.

## Einen vorhandenen Alias ändern

Eine Append-Datei, die einen vorhandenen Abschnitt nennt, wird Schlüssel für Schlüssel mit ihm zusammengeführt; sie ersetzt den
Abschnitt nicht. Um die Sortierreihenfolge des Kernel-Alias `children` zu ändern, braucht eine Erweiterung nur:

```ini
[children]
Constant[sort_by]=name;1
```

`children` hat kein eigenes `Constant[sort_by]` (es nimmt `sort_by` aus Ihrem Hash), daher gilt die Konstante, wenn das Template
kein `sort_by` übergibt. Ohne die Änderung sortiert `content/list` ohne `sort_by` nach dem Pfad der Knoten (`path_string`
aufsteigend, der Standard von `eZContentObjectTreeNode::createSortingSQLStrings()`):

```tpl
{foreach fetch_alias( 'children', hash( 'parent_node_id', 89, 'limit', 4 ) ) as $child}
- {$child.name|wash}

{/foreach}
```

Ausgabe auf alpha:

```text
- Privacy Policy
- Cookie Policy
- Fitness
- Healthy eating
```

Mit der Änderung sortiert dasselbe Template nach Namen:

```tpl
{foreach fetch_alias( 'children', hash( 'parent_node_id', 89, 'limit', 4 ) ) as $child}
- {$child.name|wash}

{/foreach}
```

Ausgabe auf alpha:

```text
- Authors
- Contact
- Cookie Policy
- Fit &amp; Healthy site info - [SITEINFO]
```

(Gerendert mit der Änderung im Speicher, wie im vorigen Beispiel.) Das Risiko ist dasselbe wie bei jeder gemeinsam genutzten
Einstellung: Jedes Template des Siteaccess, das `children` verwendet, erhält nun das neue Verhalten, auch Templates anderer
Erweiterungen. Ein Array-Schlüssel (`Parameter[...]`, `Constant[...]`) lässt sich durch eine Append-Datei nicht entfernen, nur
ersetzen; eine Zeile `Constant[]` mit leeren Klammern würde die ganze Liste leeren. Legen Sie lieber einen neuen Alias mit eigenem
Namen an.

## Wann sich ein Alias lohnt

- Für eine Abfrage, die in vielen Templates einer Website verwendet wird: Ihre festen Teile lassen sich an einer Stelle ändern.
- Nicht, um teure Abfragen zu verbergen: Ein Alias führt bei jedem Aufruf genau den Fetch aus, den er nennt (setzen Sie den Aufruf
  in einen `cache-block`, wenn sein Ergebnis geteilt werden kann).
- Nicht für eine Abfrage, die nur einmal vorkommt: `fetch()` mit allen Parametern ist leichter zu lesen.

## Verwandte Seiten

- [Wie Fetch-Funktionen arbeiten](wie-fetch-funktionen-arbeiten.md)
- [Execute-Operatoren](execute-operatoren.md): `fetch` und `fetch_alias` als Operatoren
- [Grundlagen von fetch](../designer/vorlagen/fetch-grundlagen.md)
- [Die Referenz der Fetch-Funktionen](../../beispiele/fetch-funktionen/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
