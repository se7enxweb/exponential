---
title: Content object attributes reference
audience: developer, designer, app-creator
category: development
order: 220
summary: Every attribute a template can read from a content object (eZContentObject) - the 12 database fields and the 65 function attributes of its definition, the PHP method behind each, and the value each returned for a real article on alpha.
keywords: content object attributes, eZContentObject, definition, function_attributes, data_map, current, versions, main_node, assigned_nodes, related objects, states, languages, reference
type: help
---
# Content object attributes reference

`$node.object` and `$object` (in `content_view_gui` templates and in the results of `fetch( 'content', 'object' )`)
are `eZContentObject` objects (`kernel/classes/ezcontentobject.php`). The attributes a template can read are the
`fields` (columns of the `ezcontentobject` table) and the `function_attributes` of `eZContentObject::definition()`.

## Contents

1. [How this table was made](#how-this-table-was-made)
2. [Database fields](#database-fields)
3. [Function attributes](#function-attributes)
4. [A sample on alpha](#a-sample-on-alpha)
5. [Notes](#notes)
6. [Related pages](#related-pages)

## How this table was made

The same read-only script as for the [node attributes](node-attributes-reference.md): `definition()` listed, every
name read with `$object->attribute( $name )` on the object of node 96 (object 94, an `ng_article`), as the anonymous
user of the `site` siteaccess.

## Database fields

| Attribute | Database column / member | Datatype | Value on alpha (node 96) |
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

## Function attributes

`name` and `remote_id` are in both lists; the function attributes (`name()`, `remoteID()`) are the ones a template
reads. `name()` returns the name in the object's current language, the field `Name` the name stored on the object.

| Function attribute | PHP method | Value on alpha (node 96) |
|---|---|---|
| `current` | `eZContentObject::currentVersion()` | object eZContentObjectVersion |
| `published_version` | `eZContentObject::publishedVersion()` | integer 1 |
| `versions` | `eZContentObject::versions()` | array(1) of eZContentObjectVersion |
| `author_array` | `eZContentObject::authorArray()` | array(1) of eZUser |
| `class_name` | `eZContentObject::className()` | string "NG Article" |
| `content_class` | `eZContentObject::contentClass()` | object eZContentClass |
| `contentobject_attributes` | `eZContentObject::contentObjectAttributes()` | array(18) of eZContentObjectAttribute |
| `owner` | `eZContentObject::owner()` | object eZContentObject |
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
| `can_create_class_list` | `eZContentObject::canCreateClassList()` | array(2) of array |
| `can_edit` | `eZContentObject::canEdit()` | boolean false |
| `can_translate` | `eZContentObject::canTranslate()` | boolean false |
| `can_remove` | `eZContentObject::canRemove()` | boolean false |
| `can_move` | `eZContentObject::canMoveFrom()` | boolean false |
| `can_move_from` | `eZContentObject::canMoveFrom()` | boolean false |
| `can_view_embed` | `eZContentObject::canViewEmbed()` | boolean false |
| `data_map` | `eZContentObject::dataMap()` | array(18) of eZContentObjectAttribute |
| `grouped_data_map` | `eZContentObject::groupedDataMap()` | array(2) of array |
| `main_parent_node_id` | `eZContentObject::mainParentNodeID()` | integer 92 |
| `assigned_nodes` | `eZContentObject::assignedNodes()` | array(1) of eZContentObjectTreeNode |
| `visible_nodes` | `eZContentObject::visibleNodes()` | array(1) of eZContentObjectTreeNode |
| `has_visible_node` | `eZContentObject::hasVisibleNode()` | boolean true |
| `parent_nodes` | `eZContentObject::parentNodeIDArray()` | array(1) of integer |
| `main_node_id` | `eZContentObject::mainNodeID()` | integer 96 |
| `main_node` | `eZContentObject::mainNode()` | object eZContentObjectTreeNode |
| `default_language` | `eZContentObject::defaultLanguage()` | string "eng-US" |
| `content_action_list` | `eZContentObject::contentActionList()` | array(0) |
| `class_identifier` | `eZContentObject::contentClassIdentifier()` | string "ng_article" |
| `class_group_id_list` | `eZContentObject::contentClassGroupIDList()` | array(1) of integer |
| `name` | `eZContentObject::name()` | string "Why Should You Choose Strength Training?" |
| `match_ingroup_id_list` | `eZContentObject::matchIngroupIDList()` | boolean false |
| `remote_id` | `eZContentObject::remoteID()` | string "media-o-958" |
| `current_language` | `eZContentObject::currentLanguage()` | string "eng-US" |
| `current_language_object` | `eZContentObject::currentLanguageObject()` | object eZContentLanguage |
| `initial_language` | `eZContentObject::initialLanguage()` | object eZContentLanguage |
| `initial_language_code` | `eZContentObject::initialLanguageCode()` | string "eng-US" |
| `available_languages` | `eZContentObject::availableLanguages()` | array(1) of string |
| `language_codes` | `eZContentObject::availableLanguages()` | array(1) of string |
| `language_js_array` | `eZContentObject::availableLanguagesJsArray()` | string (49 bytes) |
| `languages` | `eZContentObject::languages()` | array(1) of eZContentLanguage |
| `all_languages` | `eZContentObject::allLanguages()` | array(1) of eZContentLanguage |
| `can_edit_languages` | `eZContentObject::canEditLanguages()` | array(0) |
| `can_create_languages` | `eZContentObject::canCreateLanguages()` | array(0) |
| `always_available` | `eZContentObject::isAlwaysAvailable()` | integer 0 |
| `allowed_assign_section_list` | `eZContentObject::allowedAssignSectionList()` | array(1) of array |
| `allowed_assign_state_id_list` | `eZContentObject::allowedAssignStateIDList()` | array(2) of integer |
| `allowed_assign_state_list` | `eZContentObject::allowedAssignStateList()` | array(1) of array |
| `state_id_array` | `eZContentObject::stateIDArray()` | array(2) of integer |
| `state_identifier_array` | `eZContentObject::stateIdentifierArray()` | array(2) of string |
| `section_identifier` | `eZContentObject::sectionIdentifier()` | string "standard" |

## A sample on alpha

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

Output on alpha:

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

## Notes

- **Relations.** `related_contentobject_array` returns the objects this object relates to (all relation types:
  `relatedContentObjectList()` calls `relatedObjects()` without a type filter); `linked_contentobject_array` only the
  objects linked from rich text, `embedded_contentobject_array` only the ones embedded in it. The `reverse_*` variants
  return the objects that point at this one. Each reads the database when it is read.
- **Versions.** `current` is the published version object, `current_version` its number, `versions` every version.
  Reading `versions` loads them all; use it in the admin, not on a site page.
- **Locations.** `main_node` and `main_node_id` give the main location, `assigned_nodes` every location,
  `visible_nodes` the visible ones, `parent_nodes` the node IDs of the parents.
- **Grouped data map.** `grouped_data_map` groups the attributes by their class attribute category (the "Category"
  of a class attribute, such as content and meta); the code notes that the result is not cached.
- **States.** `state_id_array` maps each state group ID to the object's state ID, `state_identifier_array` gives
  `group/state` identifiers such as `ez_lock/not_locked`.
- **Permissions.** `can_edit`, `can_remove`, `can_translate`, `can_create_class_list` and the other `can_*`
  attributes check the current user's policies, and `can_create_languages` / `can_edit_languages` the languages the
  user may use.
- **`match_ingroup_id_list`** is `false` unless `content.ini [ContentOverrideSettings] EnableClassGroupOverride=true`;
  then it is the class group IDs, which the `class_group` override condition uses.
- **`author_array`** returns `eZUser` objects of every user who created a version of the object.

## Related pages

- [The object model in templates](object-model.md)
- [Node attributes reference](node-attributes-reference.md)
- [Content attribute and version reference](content-attribute-reference.md)
- [Data map and content](data-map-and-content.md)
- [Content view gui](../designers/templates/content-view-gui.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
