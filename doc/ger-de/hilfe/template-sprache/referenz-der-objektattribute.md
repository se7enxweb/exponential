---
title: Referenz der Attribute von Inhaltsobjekten
audience: developer, designer, app-creator
category: development
order: 220
summary: Jedes Attribut, das ein Template von einem Inhaltsobjekt (eZContentObject) lesen kann - die 12 Datenbankfelder und die 65 Funktionsattribute seiner Definition, die PHP-Methode hinter jedem und der Wert, den jedes für einen echten Artikel auf alpha zurückgab.
keywords: Attribute von Inhaltsobjekten, eZContentObject, definition, function_attributes, data_map, current, versions, main_node, assigned_nodes, verwandte Objekte, Zustände, Sprachen, Referenz, content object attributes, related objects, states, languages, reference
type: help
---
# Referenz der Attribute von Inhaltsobjekten

`$node.object` und `$object` (in Templates von `content_view_gui` und in den Ergebnissen von `fetch( 'content', 'object' )`) sind Objekte
`eZContentObject` (`kernel/classes/ezcontentobject.php`). Die Attribute, die ein Template lesen kann, sind die `fields` (Spalten der Tabelle
`ezcontentobject`) und die `function_attributes` von `eZContentObject::definition()`.

## Inhalt

1. [Wie diese Tabelle entstand](#wie-diese-tabelle-entstand)
2. [Datenbankfelder](#datenbankfelder)
3. [Funktionsattribute](#funktionsattribute)
4. [Ein Beispiel auf alpha](#ein-beispiel-auf-alpha)
5. [Hinweise](#hinweise)
6. [Verwandte Seiten](#verwandte-seiten)

## Wie diese Tabelle entstand

Dasselbe nur lesende Skript wie für die [Knotenattribute](referenz-der-knotenattribute.md): `definition()` aufgeführt, jeder Name mit
`$object->attribute( $name )` am Objekt von Knoten 96 (Objekt 94, ein `ng_article`) gelesen, als anonymer Benutzer des Siteaccess `site`.

## Datenbankfelder

| Attribut | Datenbankspalte / Mitglied | Datentyp | Wert auf alpha (Knoten 96) |
|---|---|---|---|
| `id` | `ID` | integer | integer 94 |
| `section_id` | `SectionID` | integer | integer 1 |
| `owner_id` | `OwnerID` | integer | integer 14 |
| `contentclass_id` | `ClassID` | integer | integer 44 |
| `name` | `Name` | string | string "Why Should You Choose Strength Training?" |
| `published` | `Published` | integer | integer 1532345053 |
| `modified` | `Modified` | integer | integer 1600936234 |
| `current_version` | `CurrentVersion` | integer | integer 1 |
| `status` | `Status` | integer | integer 1 |
| `remote_id` | `RemoteID` | string | string "media-o-958" |
| `language_mask` | `LanguageMask` | integer | integer 2 |
| `initial_language_id` | `InitialLanguageID` | integer | integer 2 |

## Funktionsattribute

`name` und `remote_id` stehen in beiden Listen; ein Template liest die Funktionsattribute (`name()`, `remoteID()`). `name()` gibt den Namen in
der aktuellen Sprache des Objekts zurück, das Feld `Name` den am Objekt gespeicherten Namen.

| Funktionsattribut | PHP-Methode | Wert auf alpha (Knoten 96) |
|---|---|---|
| `current` | `eZContentObject::currentVersion()` | Objekt eZContentObjectVersion |
| `published_version` | `eZContentObject::publishedVersion()` | integer 1 |
| `versions` | `eZContentObject::versions()` | array(1) aus eZContentObjectVersion |
| `author_array` | `eZContentObject::authorArray()` | array(1) aus eZUser |
| `class_name` | `eZContentObject::className()` | string "NG Article" |
| `content_class` | `eZContentObject::contentClass()` | Objekt eZContentClass |
| `contentobject_attributes` | `eZContentObject::contentObjectAttributes()` | array(18) aus eZContentObjectAttribute |
| `owner` | `eZContentObject::owner()` | Objekt eZContentObject |
| `related_contentobject_array` | `eZContentObject::relatedContentObjectList()` | array(0) |
| `related_contentobject_count` | `eZContentObject::relatedContentObjectCount()` | integer 0 |
| `reverse_related_contentobject_array` | `eZContentObject::reverseRelatedObjectList()` | array(0) |
| `reverse_related_contentobject_count` | `eZContentObject::reverseRelatedObjectCount()` | integer 0 |
| `linked_contentobject_array` | `eZContentObject::linkedContentObjectList()` | array(0) |
| `linked_contentobject_count` | `eZContentObject::linkedContentObjectCount()` | integer 0 |
| `reverse_linked_contentobject_array` | `eZContentObject::reverseLinkedObjectList()` | array(0) |
| `reverse_linked_contentobject_count` | `eZContentObject::reverseLinkedObjectCount()` | integer 0 |
| `embedded_contentobject_array` | `eZContentObject::embeddedContentObjectList()` | array(0) |
| `embedded_contentobject_count` | `eZContentObject::embeddedContentObjectCount()` | integer 0 |
| `reverse_embedded_contentobject_array` | `eZContentObject::reverseEmbeddedObjectList()` | array(0) |
| `reverse_embedded_contentobject_count` | `eZContentObject::reverseEmbeddedObjectCount()` | integer 0 |
| `can_read` | `eZContentObject::canRead()` | boolean true |
| `can_pdf` | `eZContentObject::canPdf()` | boolean true |
| `can_diff` | `eZContentObject::canDiff()` | boolean false |
| `can_create` | `eZContentObject::canCreate()` | boolean true |
| `can_create_class_list` | `eZContentObject::canCreateClassList()` | array(2) aus array |
| `can_edit` | `eZContentObject::canEdit()` | boolean false |
| `can_translate` | `eZContentObject::canTranslate()` | boolean false |
| `can_remove` | `eZContentObject::canRemove()` | boolean false |
| `can_move` | `eZContentObject::canMoveFrom()` | boolean false |
| `can_move_from` | `eZContentObject::canMoveFrom()` | boolean false |
| `can_view_embed` | `eZContentObject::canViewEmbed()` | boolean false |
| `data_map` | `eZContentObject::dataMap()` | array(18) aus eZContentObjectAttribute |
| `grouped_data_map` | `eZContentObject::groupedDataMap()` | array(2) aus array |
| `main_parent_node_id` | `eZContentObject::mainParentNodeID()` | integer 92 |
| `assigned_nodes` | `eZContentObject::assignedNodes()` | array(1) aus eZContentObjectTreeNode |
| `visible_nodes` | `eZContentObject::visibleNodes()` | array(1) aus eZContentObjectTreeNode |
| `has_visible_node` | `eZContentObject::hasVisibleNode()` | boolean true |
| `parent_nodes` | `eZContentObject::parentNodeIDArray()` | array(1) aus integer |
| `main_node_id` | `eZContentObject::mainNodeID()` | integer 96 |
| `main_node` | `eZContentObject::mainNode()` | Objekt eZContentObjectTreeNode |
| `default_language` | `eZContentObject::defaultLanguage()` | string "eng-US" |
| `content_action_list` | `eZContentObject::contentActionList()` | array(0) |
| `class_identifier` | `eZContentObject::contentClassIdentifier()` | string "ng_article" |
| `class_group_id_list` | `eZContentObject::contentClassGroupIDList()` | array(1) aus integer |
| `name` | `eZContentObject::name()` | string "Why Should You Choose Strength Training?" |
| `match_ingroup_id_list` | `eZContentObject::matchIngroupIDList()` | boolean false |
| `remote_id` | `eZContentObject::remoteID()` | string "media-o-958" |
| `current_language` | `eZContentObject::currentLanguage()` | string "eng-US" |
| `current_language_object` | `eZContentObject::currentLanguageObject()` | Objekt eZContentLanguage |
| `initial_language` | `eZContentObject::initialLanguage()` | Objekt eZContentLanguage |
| `initial_language_code` | `eZContentObject::initialLanguageCode()` | string "eng-US" |
| `available_languages` | `eZContentObject::availableLanguages()` | array(1) aus string |
| `language_codes` | `eZContentObject::availableLanguages()` | array(1) aus string |
| `language_js_array` | `eZContentObject::availableLanguagesJsArray()` | string (49 Bytes) |
| `languages` | `eZContentObject::languages()` | array(1) aus eZContentLanguage |
| `all_languages` | `eZContentObject::allLanguages()` | array(1) aus eZContentLanguage |
| `can_edit_languages` | `eZContentObject::canEditLanguages()` | array(0) |
| `can_create_languages` | `eZContentObject::canCreateLanguages()` | array(0) |
| `always_available` | `eZContentObject::isAlwaysAvailable()` | integer 0 |
| `allowed_assign_section_list` | `eZContentObject::allowedAssignSectionList()` | array(1) aus array |
| `allowed_assign_state_id_list` | `eZContentObject::allowedAssignStateIDList()` | array(2) aus integer |
| `allowed_assign_state_list` | `eZContentObject::allowedAssignStateList()` | array(1) aus array |
| `state_id_array` | `eZContentObject::stateIDArray()` | array(2) aus integer |
| `state_identifier_array` | `eZContentObject::stateIdentifierArray()` | array(2) aus string |
| `section_identifier` | `eZContentObject::sectionIdentifier()` | string "standard" |

## Ein Beispiel auf alpha

```tpl
{def $object = fetch( 'content', 'node', hash( 'node_id', 96 ) ).object}
id / class:          {$object.id} / {$object.class_identifier} ({$object.contentclass_id})
section:             {$object.section_id} ({$object.section_identifier})
published:           {$object.published|datetime( 'custom', '%Y-%m-%d' )}
current version:     {$object.current_version}
language:            {$object.current_language}, available: {$object.available_languages|implode( ', ' )}
states:              {$object.state_identifier_array|implode( ', ' )}
main node:           {$object.main_node_id}, locations: {$object.assigned_nodes|count}
related objects:     {$object.related_contentobject_count}, reverse related: {$object.reverse_related_contentobject_count}
owner is a user:     {cond( $object.owner.class_identifier|eq( 'user' ), 'yes', 'no' )}
```

Ausgabe auf alpha:

```text
id / class:          94 / ng_article (44)
section:             1 (standard)
published:           2018-07-23
current version:     1
language:            eng-US, available: eng-US
states:              ez_lock/not_locked, help_status/draft
main node:           96, locations: 1
related objects:     0, reverse related: 0
owner is a user:     yes
```

## Hinweise

- **Beziehungen.** `related_contentobject_array` gibt die Objekte zurück, auf die dieses Objekt verweist (alle Beziehungsarten:
  `relatedContentObjectList()` ruft `relatedObjects()` ohne Typfilter auf); `linked_contentobject_array` nur die aus Rich Text verlinkten
  Objekte, `embedded_contentobject_array` nur die darin eingebetteten. Die Varianten `reverse_*` geben die Objekte zurück, die auf dieses
  zeigen. Jedes liest bei jedem Zugriff die Datenbank.
- **Versionen.** `current` ist das Objekt der veröffentlichten Version, `current_version` ihre Nummer, `versions` jede Version. Das Lesen von
  `versions` lädt sie alle; nutzen Sie es in der Administration, nicht auf einer Seite der Website.
- **Orte.** `main_node` und `main_node_id` geben den Hauptort, `assigned_nodes` jeden Ort, `visible_nodes` die sichtbaren, `parent_nodes` die
  Knoten-IDs der Elternknoten.
- **Gruppierte Data Map.** `grouped_data_map` gruppiert die Attribute nach der Kategorie ihres Klassenattributs (der „Kategorie“ eines
  Klassenattributs, etwa content und meta); der Code vermerkt, dass das Ergebnis nicht gecacht wird.
- **Zustände.** `state_id_array` ordnet jeder ID einer Zustandsgruppe die Zustands-ID des Objekts zu, `state_identifier_array` gibt
  Bezeichner `group/state` wie `ez_lock/not_locked`.
- **Berechtigungen.** `can_edit`, `can_remove`, `can_translate`, `can_create_class_list` und die anderen Attribute `can_*` prüfen die
  Richtlinien des aktuellen Benutzers, und `can_create_languages` / `can_edit_languages` die Sprachen, die der Benutzer nutzen darf.
- **`match_ingroup_id_list`** ist `false`, außer wenn `content.ini [ContentOverrideSettings] EnableClassGroupOverride=true` gesetzt ist; dann
  sind es die IDs der Klassengruppen, die die Override-Bedingung `class_group` nutzt.
- **`author_array`** gibt Objekte `eZUser` jedes Benutzers zurück, der eine Version des Objekts angelegt hat.

## Verwandte Seiten

- [Das Objektmodell in Templates](das-objektmodell-in-templates.md)
- [Referenz der Knotenattribute](referenz-der-knotenattribute.md)
- [Referenz von Inhaltsattribut und Version](referenz-von-inhaltsattribut-und-version.md)
- [Data Map und Inhalt](data-map-und-inhalt.md)
- [Content view gui](../designer/vorlagen/content-view-gui.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
