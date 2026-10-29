---
title: ezwebin-Operatoren
audience: developer, designer, app-creator
category: development
order: 410
summary: Die sechs Operatoren der Erweiterung ezwebin - ezpagedata, ezpagedata_set, ezpagedata_append, ezarchive, ezkeywordlist und eztagcloud - mit ihren Parametern, was ezpagedata für ein Pagelayout berechnet, welche Datenbanksysteme ezarchive unterstützt, und Beispiele, auf einer Entwicklungsinstallation gerendert.
keywords: Seitendaten, Schlagwortwolke, Schlüsselwörter, Archiv, persistente Variable, ezwebin, ezpagedata, ezpagedata_set, ezpagedata_append, ezarchive, ezkeywordlist, eztagcloud, page data, persistent_variable, tag cloud, keywords, archive
type: help
---
# ezwebin-Operatoren

Die Erweiterung `ezwebin` (das Design der Website-Oberfläche) registriert sechs Operatoren in
`extension/ezwebin/autoloads/eztemplateautoload.php`, eine Klasse pro Datei in `extension/ezwebin/autoloads/`. Sie gibt es
nur, solange ezwebin eine aktive Erweiterung ist. `ezpagedata_set` und `ezpagedata_append` speichern ihre Werte über
ezjscore (`ezjscPackerTemplateFunctions::setPersistentArray()`), daher braucht ezwebin ezjscore.

| Operator | Klasse |
|---|---|
| `ezpagedata`, `ezpagedata_set`, `ezpagedata_append` | `eZPageData` (`ezpagedata.php`) |
| `ezarchive` | `eZArchive` (`ezarchive.php`) |
| `ezkeywordlist` | `eZKeywordList` (`ezkeywordlist.php`) |
| `eztagcloud` | `eZTagCloud` (`eztagcloud.php`) |

## Inhalt

1. [Die Operatoren](#die-operatoren): [ezpagedata_set](#ezpagedata_set), [ezpagedata_append](#ezpagedata_append),
   [ezpagedata](#ezpagedata), [ezarchive](#ezarchive), [ezkeywordlist](#ezkeywordlist), [eztagcloud](#eztagcloud)
2. [Hinweise zur Sicherheit](#hinweise-zur-sicherheit)

## Die Operatoren

### ezpagedata_set

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `key` | Zeichenkette | ja | `false` |
| `value` | beliebig | ja | `false` |

- **Eingabe:** keine. **Rückgabe:** nichts (keine Ausgabe).

Speichert `value` unter `key` in der persistenten Variablen und ersetzt, was dort stand. In einem View-Template ist das
`$persistent_variable`, im Pagelayout `$module_result.content_info.persistent_variable`. Da die persistente Variable mit
dem View-Cache gespeichert wird, erreicht ein in einem gecachten View-Template gesetzter Wert das Pagelayout auch auf
einer gecachten Seite.

### ezpagedata_append

Dieselben Parameter. Hängt `value` an die Liste unter `key` an (ein einzelner Wert wird zur Liste).

### ezpagedata

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `params` | Array (Hash) | nein | leer |

- **Eingabe:** keine.
- **Rückgabe:** ein Hash mit Seiteneinstellungen für das Pagelayout: `persistent_variable`, `main_node_id`, `show_path`,
  `website_toolbar`, `node_id`, `is_edit`, `page_root_depth`, `page_depth`, `root_node`, `canonical_url`,
  `canonical_language_url`, `template_look`, `class_identifier`, `top_menu`, `left_menu`, `current_menu`,
  `extra_menu`, `extra_menu_node_id`, `extra_menu_subitems`, `extra_menu_class_list`, `path_array`,
  `path_id_array`, `path_normalized` und `css_classes`.

Der Operator liest `$module_result` (path, content_info, node_id), `$ui_context`, `$uri_string` und `$current_user` aus dem
Template, `menu.ini` (`[SelectedMenu]`, `[MenuSettings] HideLeftMenuClasses[]` und `AlwaysAvailable`,
`[MenuContentSettings] ExtraIdentifierList[]` und `ExtraMenuSubitemsCheck`), `content.ini` `[NodeSettings] RootNode`
und `site.ini` `[SiteSettings] RootNodeDepth`. Die Werte aus `params` **und** aus der persistenten Variablen überschreiben
die berechneten (`left_menu`, `extra_menu`, `top_menu`, `show_path`, `website_toolbar`, `current_node_id` mit
`set_current_node_path`, `canonical_url`, `template_look`, `extra_menu_node_id`, `extra_menu_class_list`,
`extra_menu_subitems`). So blendet ein View-Template das linke Menü seiner Seite aus: `ezpagedata_set( 'left_menu',
false() )`. Wenn `template_look` nicht angegeben ist, holt der Operator das erste Objekt der Klasse `template_look`, was
eine Abfrage pro Aufruf bedeutet.

```tpl
{def $persistent_variable = hash()}{ezpagedata_set( 'left_menu', false() )}{ezpagedata_append( 'body_classes', 'recipes' )}{ezpagedata_append( 'body_classes', 'dark' )}
set: {$persistent_variable.left_menu|choose( 'false', 'true' )}, appended: {$persistent_variable.body_classes|implode( ' ' )}

{def $page = ezpagedata()}
keys: {foreach $page as $k => $v}{$k}{delimiter} {/delimiter}{/foreach}

left_menu={$page.left_menu|choose( 'false', 'true' )} css_classes={$page.css_classes} top_menu={$page.top_menu} root_node={$page.root_node}
```

Ausgabe:

```text

set: false, appended: recipes dark
keys: persistent_variable main_node_id show_path website_toolbar node_id is_edit page_root_depth page_depth root_node canonical_url canonical_language_url template_look class_identifier top_menu left_menu current_menu extra_menu extra_menu_node_id extra_menu_subitems extra_menu_class_list path_array path_id_array path_normalized css_classes
left_menu=false css_classes=nosidemenu noextrainfo top_menu=flat_top root_node=89
```

### ezarchive

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `class_identifier` | Zeichenkette | ja | leer |
| `parent_node_id` | Ganzzahl | ja | 0 |

- **Eingabe:** keine.
- **Rückgabe:** eine Liste von Zeilen mit `month`, `year` und `timestamp` (der Monatserste), die neuesten zuerst: die
  Monate, in denen sichtbare Unterknoten von `parent_node_id` dieser Klasse einen Wert im Attribut `publication_date`
  haben.

Der Attributbezeichner `publication_date` ist im SQL fest eingetragen. Der Operator hat SQL nur für MySQL, PostgreSQL und
Oracle; auf jedem anderen Datenbanksystem ist die Abfrage leer und das Ergebnis eine leere Liste. Die hier verwendete Entwicklungsinstallation läuft mit SQLite,
daher:

```tpl
database: {ezini( 'DatabaseSettings', 'DatabaseImplementation' )}

{def $months = ezarchive( 'blog_post', 2 )}
ezarchive rows: {$months|count}
```

Ausgabe:

```text
database: sqlite3
ezarchive rows: 0
```

### ezkeywordlist

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `class_identifier` | beliebig (Bezeichner oder Liste) | ja | leer |
| `parent_node_id` | Ganzzahl | ja | 0 |
| `depth` | Ganzzahl | nein | 0 (beliebige Tiefe) |

- **Eingabe:** keine.
- **Rückgabe:** eine Liste von Zeilen mit `keyword`, den unterschiedlichen Schlüsselwörtern der `ezkeyword`-Attribute
  unterhalb des Elternknotens (der Elternknoten selbst ausgenommen), sortiert und gefiltert nach Klasse (ein leerer
  Bezeichner bedeutet alle Klassen), nach Tiefe, wenn angegeben, nach der aktuellen Sprache, nach Sichtbarkeit und nach den
  Leseberechtigungen des Benutzers.

### eztagcloud

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `params` | Array (Hash) | nein | leer |

- **Eingabe:** keine.
- **params:** `class_identifier`, `parent_node_id`, `limit`, `offset`, `sort_by` (`array( 'keyword' oder 'count',
  aufsteigend )` oder eine Liste davon; Standard: keyword aufsteigend), `post_sort_by` (`keyword`, `keyword_reverse`,
  `count`, `count_reverse`: sortiert nach dem Limit).
- **Rückgabe:** HTML: das Template `design:tagcloud/tagcloud.tpl`, gerendert mit `$tag_cloud`, einer Liste aus `tag`,
  `count` und `font_size` (100 bis 200, über die Anzahlen verteilt).

Die hier verwendete Entwicklungsinstallation verschlagwortet ihre Inhalte mit eztags, nicht mit `ezkeyword`-Attributen, daher finden beide
Schlüsselwort-Operatoren hier nichts:

```tpl
{def $words = ezkeywordlist( '', 2 )}
ezkeywordlist: {$words|count} keywords, first: {foreach $words as $w max 5}{$w.keyword|wash}{delimiter}, {/delimiter}{/foreach}


eztagcloud:
{eztagcloud( hash( 'parent_node_id', 2, 'sort_by', array( 'count', false() ), 'limit', 4 ) )}
```

Ausgabe:

```text
ezkeywordlist: 0 keywords, first: 

eztagcloud:
```

Das Gegenstück für eztags ist `eztagscloud` unter [Operatoren von eztags und Sternbewertung](eztags-und-starrating-operatoren.md).

## Hinweise zur Sicherheit

- `ezarchive` setzt `class_identifier` ohne Maskierung in die MySQL- und PostgreSQL-Abfragen ein (nur die Oracle-Abfrage
  maskiert ihn). Übergeben Sie einen konstanten Bezeichner, nie Eingaben aus der Anfrage.
- `ezarchive` prüft keine Berechtigungen: Es kann Monate von Inhalten auflisten, die der Benutzer nicht lesen darf.
- `eztagcloud` setzt die Klassen-ID und die Pfadzeichenkette des Elternknotens in sein SQL ein; beide kommen aus der
  Datenbank, nicht vom Aufrufer.

## Verwandte Seiten

- [Die persistente Variable](../designer/vorlagen/die-persistent-variable.md)
- [ezjscore-Operatoren im Detail](ezjscore-operatoren.md)
- [Modulergebnis und Inhaltsinformationen](../designer/vorlagen/module-result-und-content-info.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
