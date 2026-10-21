---
title: Operatoren von eztags und ezstarrating
audience: developer, designer, app-creator
category: development
order: 420
summary: Die fünf Operatoren der Erweiterung eztags (eztagscloud, latest_tags, eztags_parent_string, tag_icon, user_limitations) und die drei von ezstarrating (fetch_starrating_stats, fetch_starrating_data, fetch_by_starrating), mit Parametern, der Form der Rückgabewerte, dem Solr-Weg der Tag-Wolke, einem zu vermeidenden Absturz und Beispielen, die auf alpha ausgegeben wurden.
keywords: eztags, Tags, Tag-Wolke, Schlagwörter, Bewertung, Sternebewertung, eztagscloud, latest_tags, eztags_parent_string, tag_icon, user_limitations, ezstarrating, fetch_starrating_stats, fetch_starrating_data, fetch_by_starrating, tag cloud, rating
type: help
---
# Operatoren von eztags und ezstarrating

Zwei Erweiterungen, jede mit ihrer eigenen `autoloads/eztemplateautoload.php`; die Operatoren gibt es nur, solange die
Erweiterung aktiv ist (auf alpha stehen beide in `ActiveExtensions[]`).

| Erweiterung | Klasse und Datei | Operatoren |
|---|---|---|
| eztags | `eZTagsCloud`, `extension/eztags/autoloads/eztagscloud.php` | `eztagscloud` |
| eztags | `eZTagsTemplateFunctions`, `extension/eztags/autoloads/eztagstemplatefunctions.php` | `latest_tags`, `eztags_parent_string`, `tag_icon`, `user_limitations` |
| ezstarrating | `ezsrTemplateOperators`, `extension/ezstarrating/autoloads/ezsrtemplateoperators.php` | `fetch_starrating_stats`, `fetch_starrating_data`, `fetch_by_starrating` |

## Inhalt

1. [eztags](#eztags): [eztagscloud](#eztagscloud), [latest_tags](#latest_tags),
   [eztags_parent_string](#eztags_parent_string), [tag_icon](#tag_icon), [user_limitations](#user_limitations)
2. [ezstarrating](#ezstarrating): [fetch_starrating_stats](#fetch_starrating_stats),
   [fetch_starrating_data](#fetch_starrating_data), [fetch_by_starrating](#fetch_by_starrating)

## eztags

### eztagscloud

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `params` | Array (Hash) | nein | leer |

- **Eingabe:** keine.
- **params:** `class_identifier`, `parent_node_id`, `limit`, `offset`, `sort_by` (`array( 'keyword' or 'count',
  ascending )` oder eine Liste davon; Standard: nach Schlagwort aufsteigend) und `post_sort_by` (`keyword`,
  `keyword_reverse`, `count`, `count_reverse`, angewendet nach dem Limit).
- **Rückgabe:** HTML: `design:tagcloud.tpl`, ausgegeben mit `$tag_cloud`, einer Liste aus `keyword`, `count`, `tag`
  (dem `eZTagsObject`) und `font_size` (100 bis 200 über die Spanne der Anzahlen).

Wenn die Suchmaschine `ezsolr` ist und `eztags.ini` `[GeneralSettings] TagCloudOverSolr=enabled` gilt, kommen die
Anzahlen aus einer Solr-Facette über `ezf_df_tag_ids`; sonst aus SQL über die veröffentlichten, sichtbaren Hauptknoten,
die der Benutzer lesen darf, wobei je Tag verschiedene Objekte gezählt werden. Hier lief der SQL-Weg: die fünf am
häufigsten verwendeten Tags unterhalb von Knoten 89, danach nach Schlagwort sortiert:

```tpl
{eztagscloud( hash( 'parent_node_id', 89, 'sort_by', array( 'count', false() ), 'limit', 5, 'post_sort_by', 'keyword' ) )}
```

Ausgabe auf alpha:

```text
    <a href="/tags/view/Topics/Eating" class="eztags-cloud-item eztags-cloud-size-10" data-font-size="118.75" title="27 objects tagged with 'Eating'">Eating</a>    <a href="/tags/view/Topics/HIIT" class="eztags-cloud-item eztags-cloud-size-10" data-font-size="100" title="21 objects tagged with 'HIIT'">HIIT</a>    <a href="/tags/view/Topics/Health" class="eztags-cloud-item eztags-cloud-size-10" data-font-size="200" title="53 objects tagged with 'Health'">Health</a>    <a href="/tags/view/Topics/Running" class="eztags-cloud-item eztags-cloud-size-10" data-font-size="109.375" title="24 objects tagged with 'Running'">Running</a>    <a href="/tags/view/Topics/Workout" class="eztags-cloud-item eztags-cloud-size-10" data-font-size="146.875" title="36 objects tagged with 'Workout'">Workout</a>
```

### latest_tags

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `limit` | Ganzzahl | nein | 10 |

- **Eingabe:** keine.
- **Rückgabe:** ein Hash `result` mit den zuletzt geänderten Haupt-Tags (`eZTagsFunctionCollection::fetchLatestTags()`)
  oder `result` `false`, wenn es keine gibt. Durchlaufen Sie `.result`, nicht den Rückgabewert.

### eztags_parent_string

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `tag_id` | Ganzzahl | ja | 0 |

- **Eingabe:** keine.
- **Rückgabe:** der Pfad der Schlagwörter des Tags, verbunden mit ` / `, das Tag selbst zuletzt, mit `(+n)` hinter
  einem Schlagwort, das Synonyme hat (`eZTagsObject::getParentString()`); für eine unbekannte ID `(no parent)`, übersetzt.

### tag_icon

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `first` | Zeichenkette | nein | leer |
| `second` | Zeichenkette | nein | leer |

- **Eingabe:** der Dateiname des Icons. **Rückgabe:** die Adresse von `tag_icons/<size>/<icon>` in den Bildern des
  Designs, über `eZURLOperator::eZImage()`. Mit der Pipe ist `first` die Größe (Standard `small`).
- Ohne Eingabe liest der Code `first` als Icon und `second` als Größe. Auf alpha endete die Funktionsform
  `tag_icon( 'people.png' )` mit einem PHP-Fehler („Call to a member function warning() on null“): Der Operator ruft
  `eZImage()` ohne Template-Objekt auf, und die Suche, die er dabei ausführte, schlug fehl. Verwenden Sie die Form mit
  der Pipe, hier im Siteaccess `admin` ausgegeben:

```tpl
1 {'people.png'|tag_icon}
2 {'places.png'|tag_icon( 'normal' )}
```

Ausgabe auf alpha:

```text
1 /extension/eztags/design/standard/images/tag_icons/small/people.png
2 /extension/eztags/design/standard/images/tag_icons/normal/places.png
```

### user_limitations

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `module` | Zeichenkette | ja | leer |
| `function` | Zeichenkette | ja | leer |

- **Eingabe:** keine.
- **Rückgabe:** das Ergebnis von `hasAccessTo()` für den aktuellen Benutzer (`accessWord`, `policies`, ...) und dazu
  `simplifiedLimitations`: bei eingeschränktem Zugriff jeder Name einer Einschränkung mit den zusammengeführten
  eindeutigen Werten aller Richtlinien.

```tpl
{def $latest = latest_tags( 3 )}
1 latest_tags: {foreach $latest.result as $t}{$t.keyword|wash} (id {$t.id}){delimiter}, {/delimiter}{/foreach}

2 eztags_parent_string: {eztags_parent_string( $latest.result[0].id )|wash}
3 eztags_parent_string of 0: {eztags_parent_string( 0 )|wash}

{def $access = user_limitations( 'tags', 'read' )}
4 user_limitations tags/read: accessWord={$access.accessWord} simplified={$access.simplifiedLimitations|count}
```

Ausgabe auf alpha:

```text
1 latest_tags: Starter (id 243), Angebote (id 244), Urlaub (id 245)
2 eztags_parent_string: Starter
3 eztags_parent_string of 0: (no parent)
4 user_limitations tags/read: accessWord=no simplified=0
```

## ezstarrating

### fetch_starrating_stats

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `object_id` | Ganzzahl | ja | 0 |

- **Eingabe:** keine.
- **Rückgabe:** die Summe von `rating_count` und der Durchschnitt von `rating_average` der Bewertungszeilen des
  Objekts (`ezsrRatingObject::stats()`), für den Rest der Anfrage aufbewahrt. Findet die Aggregation eine Zeile, der
  Hash selbst; sonst die rohe Ergebnisliste, wie unten beim Objekt ohne Bewertungen (eine Zeile mit leeren Werten).

### fetch_starrating_data

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `params` | Array (Hash) | ja | leer |

- **Eingabe:** keine.
- **Rückgabe:** die gespeicherten Stimmen (`ezsrRatingDataObject::fetchByConds()`), gefiltert nach `contentobject_id`
  (oder `object_id`), `contentobject_attribute_id`, `user_id`, `session_key`, mit `limit`, `offset` und `as_object`
  (Standard `true`).

### fetch_by_starrating

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `params` | Array (Hash) | nein | leer |

- **Eingabe:** keine.
- **Rückgabe:** Knoten, nach Bewertung geordnet (Standard `rating DESC, rating_count DESC`;
  `ezsrRatingObject::fetchNodeByRating()`).
- **params:** `parent_node_id` oder `parent_node_path`, `owner_id`, `owner_parent_node_id` oder
  `owner_parent_node_path`, `group_by_owner`, `class_filter_type` und `class_filter_array`, `include_not_rated`,
  `main_node_only`, `ignore_visibility`, `load_data_map`, `as_object`, `limit`, `offset`, `sort_by`.

Auf alpha wurde noch kein Inhalt bewertet, daher gibt nur `include_not_rated` Knoten zurück:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 90 ) )
     $stats = fetch_starrating_stats( $node.contentobject_id )}
1 stats for an object without ratings: {$stats|get_type}, first row: {foreach $stats[0] as $k => $v}{$k}=[{$v}] {/foreach}

2 votes stored: {fetch_starrating_data( hash( 'contentobject_id', $node.contentobject_id, 'as_object', false() ) )|count}
3 rated nodes below 89: {fetch_by_starrating( hash( 'parent_node_id', 89, 'limit', 3 ) )|count}
4 with include_not_rated: {foreach fetch_by_starrating( hash( 'parent_node_id', 89, 'limit', 3, 'include_not_rated', true() ) ) as $n}{$n.name|wash}{delimiter}, {/delimiter}{/foreach}
```

Ausgabe auf alpha:

```text
1 stats for an object without ratings: array[1], first row: rating_count=[] rating_average=[] 
2 votes stored: 0
3 rated nodes below 89: 0
4 with include_not_rated: Privacy Policy, Cookie Policy, Fitness
```

## Verwandte Seiten

- [ezwebin-Operatoren](ezwebin-operatoren.md) (`eztagcloud` für den älteren Schlagwort-Datentyp)
- [Fetch-Funktionen in den Beispielen](../../../eng-us/examples/fetch-functions/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
