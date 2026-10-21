---
title: Referenz von Inhaltsattribut und Version
audience: developer, designer, app-creator
category: development
order: 230
summary: Jedes Attribut, das ein Template von einem Inhaltsobjekt-Attribut (eZContentObjectAttribute, die Einträge von data_map) und von einer Version (eZContentObjectVersion) lesen kann, mit der PHP-Methode hinter jedem, Werten von alpha und dem, was .content für jeden Datentyp in den Inhalten von alpha zurückgibt.
keywords: eZContentObjectAttribute, eZContentObjectVersion, Eintrag von data_map, content, has_content, data_text, data_int, sort_key_string, view_template, Inhalt des Datentyps, Version, translation_list, Referenz, data_map item, datatype content, version, reference
type: help
---
# Referenz von Inhaltsattribut und Version

Die Werte von `data_map` sind Objekte `eZContentObjectAttribute` (`kernel/classes/ezcontentobjectattribute.php`): ein Feld einer Version in
einer Sprache. Eine Version ist ein `eZContentObjectVersion` (`kernel/classes/ezcontentobjectversion.php`), erreichbar über `$object.current`
oder `$object.versions`. Diese Seite führt auf, was Templates von beiden lesen können und was `content` für jeden Datentyp ist.

## Inhalt

1. [Inhaltsobjekt-Attribut: Datenbankfelder](#inhaltsobjekt-attribut-datenbankfelder)
2. [Inhaltsobjekt-Attribut: Funktionsattribute](#inhaltsobjekt-attribut-funktionsattribute)
3. [Was content zurückgibt, pro Datentyp](#was-content-zurückgibt-pro-datentyp)
4. [Version: Datenbankfelder](#version-datenbankfelder)
5. [Version: Funktionsattribute](#version-funktionsattribute)
6. [Hinweise](#hinweise)
7. [Verwandte Seiten](#verwandte-seiten)

Die Tabellen entstanden wie die Tabelle der [Knotenattribute](referenz-der-knotenattribute.md): `definition()` aufgeführt und jeder Name auf
alpha gelesen, hier am Attribut `title` und an der aktuellen Version von Knoten 96 (Objekt 94, ein `ng_article`).

## Inhaltsobjekt-Attribut: Datenbankfelder

| Attribut | Datenbankspalte / Mitglied | Datentyp | Wert auf alpha (Knoten 96) |
|---|---|---|---|
| `id` | `ID` | integer | integer 580 |
| `contentobject_id` | `ContentObjectID` | integer | integer 94 |
| `version` | `Version` | integer | integer 1 |
| `language_code` | `LanguageCode` | string | string "eng-US" |
| `language_id` | `LanguageID` | integer | integer 2 |
| `contentclassattribute_id` | `ContentClassAttributeID` | integer | integer 334 |
| `attribute_original_id` | `AttributeOriginalID` | integer | integer 0 |
| `sort_key_int` | `SortKeyInt` | integer | integer 0 |
| `sort_key_string` | `SortKeyString` | string | string "why should you choose strength training?" |
| `data_type_string` | `DataTypeString` | string | string "ezstring" |
| `data_text` | `DataText` | text | string "Why Should You Choose Strength Training?" |
| `data_int` | `DataInt` | integer | null |
| `data_float` | `DataFloat` | float | double 0.0 |

Die Felder `data_text`, `data_int` und `data_float` sind die rohe Speicherung. Welches ein Datentyp nutzt und wie, ist Sache des Datentyps:
`ezstring` behält seinen Text in `data_text` und lässt `data_int` leer (`null` oben); `eZContentObjectAttribute::attribute()` macht aus einem
`data_text` mit `null` den Wert `''`, sodass Templates dort nie null sehen.

## Inhaltsobjekt-Attribut: Funktionsattribute

| Funktionsattribut | PHP-Methode | Wert auf alpha (Knoten 96) |
|---|---|---|
| `contentclass_attribute` | `eZContentObjectAttribute::contentClassAttribute()` | Objekt eZContentClassAttribute |
| `contentclass_attribute_identifier` | `eZContentObjectAttribute::contentClassAttributeIdentifier()` | string "title" |
| `contentclass_attribute_name` | `eZContentObjectAttribute::contentClassAttributeName()` | string "Title" |
| `can_translate` | `eZContentObjectAttribute::contentClassAttributeCanTranslate()` | integer 1 |
| `is_information_collector` | `eZContentObjectAttribute::contentClassAttributeIsInformationCollector()` | integer 0 |
| `is_required` | `eZContentObjectAttribute::contentClassAttributeIsRequired()` | integer 1 |
| `content` | `eZContentObjectAttribute::content()` | string "Why Should You Choose Strength Training?" |
| `has_http_value` | `eZContentObjectAttribute::hasHTTPValue()` | boolean false |
| `value` | `eZContentObjectAttribute::value()` | string "Why Should You Choose Strength Training?" |
| `has_content` | `eZContentObjectAttribute::hasContent()` | boolean true |
| `class_content` | `eZContentObjectAttribute::classContent()` | string "" |
| `object` | `eZContentObjectAttribute::object()` | Objekt eZContentObject |
| `object_version` | `eZContentObjectAttribute::objectVersion()` | Objekt eZContentObjectVersion |
| `view_template` | `eZContentObjectAttribute::viewTemplateName()` | string "ezstring" |
| `edit_template` | `eZContentObjectAttribute::editTemplateName()` | string "ezstring" |
| `result_template` | `eZContentObjectAttribute::resultTemplate()` | string "ezstring" |
| `has_validation_error` | `eZContentObjectAttribute::hasValidationError()` | boolean false |
| `validation_error` | `eZContentObjectAttribute::validationError()` | null |
| `validation_log` | `eZContentObjectAttribute::validationLog()` | null |
| `language` | `eZContentObjectAttribute::language()` | Objekt eZContentObjectAttribute |
| `is_a` | `eZContentObjectAttribute::isA()` | string "ezstring" |
| `display_info` | `eZContentObjectAttribute::displayInfo()` | array(4) aus array |
| `class_display_info` | `eZContentObjectAttribute::classDisplayInfo()` | array(2) aus array |

Ein Beispiel:

```tpl
{def $attribute = fetch( 'content', 'node', hash( 'node_id', 96 ) ).data_map.title}
id / version / language: {$attribute.id} / {$attribute.version} / {$attribute.language_code}
class attribute:         {$attribute.contentclass_attribute_identifier} "{$attribute.contentclass_attribute_name|wash}" ({$attribute.contentclassattribute_id})
datatype:                {$attribute.data_type_string}, is_a: {$attribute.is_a}
required / translatable: {$attribute.is_required} / {$attribute.can_translate}
templates:               view {$attribute.view_template}, edit {$attribute.edit_template}
stored:                  data_text "{$attribute.data_text|wash}", sort_key_string "{$attribute.sort_key_string|wash}"
```

Ausgabe auf alpha:

```text
id / version / language: 580 / 1 / eng-US
class attribute:         title "Title" (334)
datatype:                ezstring, is_a: ezstring
required / translatable: 1 / 1
templates:               view ezstring, edit ezstring
stored:                  data_text "Why Should You Choose Strength Training?", sort_key_string "why should you choose strength training?"
```

## Was content zurückgibt, pro Datentyp

`content` ruft `eZContentObjectAttribute::content()` auf, das einmal `objectAttributeContent()` des Datentyps fragt und das Ergebnis am
Attribut behält. Dieses Template durchlief die ersten 150 Knoten unter der Startseite Fit & Healthy auf alpha und gab für jeden Datentyp beim
ersten Mal mit Inhalt aus, was `content` zurückgab:

```tpl
{def $seen = array()}
{foreach fetch( 'content', 'tree', hash( 'parent_node_id', 89, 'limit', 150 ) ) as $node}{foreach $node.data_map as $attribute}{if and( $attribute.has_content, $seen|contains( $attribute.data_type_string )|not )}{set $seen = $seen|append( $attribute.data_type_string )}{$attribute.data_type_string}: {if is_object( $attribute.content )}object {$attribute.content|get_class}{else}{$attribute.content|get_type|explode( '[' )|extract_left( 1 )|implode( '' )}{/if} ({$node.class_identifier}.{$attribute.contentclass_attribute_identifier})

{/if}{/foreach}{/foreach}
```

Ausgabe auf alpha:

```text
ezstring: string (ng_article.title)
ezxmltext: object ezxmltext (ng_article.teaser_intro)
xrowmetadata: object xrowmetadata (ng_article.metadata)
eztags: object eztags (ng_landing_page.tags)
ezimage: object ezimagealiashandler (ng_article.image)
ezobjectrelationlist: array (ng_article.authors)
ezdatetime: object ezdatetime (ng_blog_post.publish_date)
ezinteger: integer (ng_gallery.column_count)
ezboolean: integer (ng_category.show_children)
ezemail: string (ng_contact_form.recipient)
ngenhancedlink: object ngenhancedlinkvalue (ng_component_hero.link_1)
ezbinaryfile: object ezbinaryfile (ng_video.video_file)
ezmedia: object ezmedia (ng_audio.file)
eztext: string (ng_htmlbox.html_code)
ezurl: string (ng_site_info.facebook)
ezprice: object ezprice (product.price)
ezsrrating: object ezsrratingobject (product.rating)
ezmatrix: object ezmatrix (product.specifications)
```

| Datentyp | `content` auf alpha | Seite des Datentyps |
|---|---|---|
| `ezstring`, `eztext`, `ezemail`, `ezurl` | Zeichenkette (bei `ezurl` die URL) | [ezstring](../../beispiele/datentypen/ezstring.md), [eztext](../../beispiele/datentypen/eztext.md), [ezemail](../../beispiele/datentypen/ezemail.md), [ezurl](../../beispiele/datentypen/ezurl.md) |
| `ezinteger`, `ezboolean` | ganze Zahl | [ezinteger](../../beispiele/datentypen/ezinteger.md), [ezboolean](../../beispiele/datentypen/ezboolean.md) |
| `ezxmltext` | Objekt `eZXMLText`; `.output.output_text` ist das gerenderte HTML | [ezxmltext](../../beispiele/datentypen/ezxmltext.md) |
| `ezimage` | `eZImageAliasHandler`; `.original`, `[alias name]` | [ezimage](../../beispiele/datentypen/ezimage.md) |
| `ezobjectrelationlist` | Array mit dem Schlüssel `relation_list` | [ezobjectrelationlist](../../beispiele/datentypen/ezobjectrelationlist.md) |
| `ezdatetime` | Objekt `eZDateTime` | [ezdatetime](../../beispiele/datentypen/ezdatetime.md) |
| `ezbinaryfile` | Objekt `eZBinaryFile` | [ezbinaryfile](../../beispiele/datentypen/ezbinaryfile.md) |
| `ezmedia` | Objekt `eZMedia` | [ezmedia](../../beispiele/datentypen/ezmedia.md) |
| `ezprice` | Objekt `eZPrice` | [ezprice](../../beispiele/datentypen/ezprice.md) |
| `ezmatrix` | Objekt `eZMatrix` | [ezmatrix](../../beispiele/datentypen/ezmatrix.md) |
| `eztags` | Objekt `eZTags` (Erweiterung eztags) | |
| `xrowmetadata` | Objekt `xrowMetaData` (Erweiterung xrowmetadata) | |
| `ngenhancedlink` | Objekt `ngEnhancedLinkValue` (Erweiterung exp_enhanced_link) | |
| `ezsrrating` | `ezsrRatingObject` (Erweiterung ezstarrating) | |

`get_class` schreibt Klassennamen klein, das Beispiel zeigt also `ezimagealiashandler` für `eZImageAliasHandler`. Die Seite
[Data Map und Inhalt](data-map-und-inhalt.md) liest mehrere dieser Inhalte im Detail.

## Version: Datenbankfelder

| Attribut | Datenbankspalte / Mitglied | Datentyp | Wert auf alpha (Knoten 96) |
|---|---|---|---|
| `id` | `ID` | integer | integer 544 |
| `contentobject_id` | `ContentObjectID` | integer | integer 94 |
| `creator_id` | `CreatorID` | integer | integer 14 |
| `version` | `Version` | integer | integer 1 |
| `status` | `Status` | integer | integer 1 |
| `created` | `Created` | integer | integer 1600936225 |
| `modified` | `Modified` | integer | integer 1600936234 |
| `workflow_event_pos` | `WorkflowEventPos` | integer | integer 0 |
| `user_id` | `UserID` | integer | integer 0 |
| `language_mask` | `LanguageMask` | integer | integer 2 |
| `initial_language_id` | `InitialLanguageID` | integer | integer 2 |

`status` ist der Status der Version: `eZContentObjectVersion::STATUS_DRAFT` (0), `STATUS_PUBLISHED` (1), `STATUS_PENDING` (2),
`STATUS_ARCHIVED` (3), `STATUS_REJECTED` (4), `STATUS_INTERNAL_DRAFT` (5), `STATUS_REPEAT` (6), `STATUS_QUEUED` (7).

## Version: Funktionsattribute

| Funktionsattribut | PHP-Methode | Wert auf alpha (Knoten 96) |
|---|---|---|
| `creator` | `eZContentObjectVersion::creator()` | Objekt eZContentObject |
| `name` | `eZContentObjectVersion::name()` | string "Why Should You Choose Strength Training?" |
| `version_name` | `eZContentObjectVersion::versionName()` | string "Why Should You Choose Strength Training?" |
| `main_parent_node_id` | `eZContentObjectVersion::mainParentNodeID()` | integer 92 |
| `contentobject_attributes` | `eZContentObjectVersion::contentObjectAttributes()` | array(18) aus eZContentObjectAttribute |
| `related_contentobject_array` | `eZContentObjectVersion::relatedContentObjectArray()` | array(0) |
| `reverse_related_object_list` | `eZContentObjectVersion::reverseRelatedObjectList()` | array(0) |
| `parent_nodes` | `eZContentObjectVersion::parentNodes()` | array(1) aus eZNodeAssignment |
| `can_read` | `eZContentObjectVersion::canVersionRead()` | boolean false |
| `can_remove` | `eZContentObjectVersion::canVersionRemove()` | boolean false |
| `data_map` | `eZContentObjectVersion::dataMap()` | array(18) aus eZContentObjectAttribute |
| `node_assignments` | `eZContentObjectVersion::nodeAssignments()` | array(1) aus eZNodeAssignment |
| `contentobject` | `eZContentObjectVersion::contentObject()` | Objekt eZContentObject |
| `initial_language` | `eZContentObjectVersion::initialLanguage()` | Objekt eZContentLanguage |
| `language_list` | `eZContentObjectVersion::translations()` | array(1) aus eZContentObjectTranslation |
| `translation` | `eZContentObjectVersion::translation()` | boolean false |
| `translation_list` | `eZContentObjectVersion::defaultTranslationList()` | array(1) aus eZContentObjectTranslation |
| `complete_translation_list` | `eZContentObjectVersion::translationList()` | array(1) aus eZContentObjectTranslation |
| `temp_main_node` | `eZContentObjectVersion::tempMainNode()` | Objekt eZContentObjectTreeNode |

Ein Beispiel:

```tpl
{def $version = fetch( 'content', 'node', hash( 'node_id', 96 ) ).object.current}
version {$version.version}, status {$version.status}, created {$version.created|datetime( 'custom', '%Y-%m-%d' )}
name in this version: {$version.version_name|wash}
translations: {foreach $version.translation_list as $t}{$t.language_code}{delimiter}, {/delimiter}{/foreach}

creator object id: {$version.creator_id}
```

Ausgabe auf alpha:

```text
version 1, status 1, created 2020-09-24
name in this version: Why Should You Choose Strength Training?
translations: eng-US
creator object id: 14
```

## Hinweise

- `language` gibt dasselbe Attribut in einer anderen Sprache zurück: ohne Sprachcode in der Ausgangssprache des Objekts
  (`eZContentObjectAttribute::language()`).
- `view_template` und `edit_template` sind die Namen (ohne `.tpl`), die `attribute_view_gui` und `attribute_edit_gui` unter
  `content/datatype/view/` und `content/datatype/edit/` suchen; bei einem Attribut zur Informationssammlung gibt `view_template` stattdessen
  das Template des Sammlers zurück.
- `class_content` ist der Inhalt des Klassenattributs (die Einstellungen des Felds, etwa die Optionen einer Auswahl).
- `has_content` kommt von `hasObjectAttributeContent()` des Datentyps. Die meisten Datentypen geben einen booleschen Wert zurück; der Datentyp
  `ezimage` gab auf alpha bei einem Bildfeld ohne Bild eine leere Zeichenkette zurück (siehe [Der Template-Compiler](der-template-compiler.md) dazu,
  warum das bei `choose` zählt).
- `can_read` und `can_remove` einer Version prüfen die Richtlinien `content/versionread` und `content/versionremove` des aktuellen Benutzers;
  als anonymer Benutzer waren beide false.

## Verwandte Seiten

- [Das Objektmodell in Templates](das-objektmodell-in-templates.md)
- [Data Map und Inhalt](data-map-und-inhalt.md)
- [Referenz der Knotenattribute](referenz-der-knotenattribute.md)
- [Referenz der Attribute von Inhaltsobjekten](referenz-der-objektattribute.md)
- [Ansichts-Templates von Datentypen](../designer/vorlagen/datentyp-ansichtstemplates.md)
- [Datentypen mit Beispielen](../../beispiele/datentypen/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
