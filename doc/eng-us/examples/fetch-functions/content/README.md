---
title: Fetch functions of content
audience: developer, admin
category: examples
order: 10
summary: The fetch functions of the content module with their parameters.
keywords: Fetch functions of content, examples, reference
type: generated
generated: exp:help:examples
---
# Fetch functions of the content module

Defined in `kernel/content/function_definition.php`. A parameter marked * is required.

| Function | Parameters |
|---|---|
| [access](access.md) | `access`*, `contentobject`*, `contentclass_id`, `parent_contentclass_id`, `language` |
| [assigned_node_count](assigned_node_count.md) | `object_id`* |
| [assigned_nodes](assigned_nodes.md) | `object_id`*, `offset`, `limit`, `sort_field`, `sort_order` |
| [available_sort_fields](available_sort_fields.md) |  |
| [bookmark_folders](bookmark_folders.md) |  |
| [bookmark_rows](bookmark_rows.md) | `folder_id` |
| [bookmarks](bookmarks.md) | `offset`, `limit` |
| [calendar](calendar.md) | `parent_node_id`*, `offset`, `limit`, `depth`, `depth_operator`, `class_id`, `attribute_filter`, `extended_attribute_filter`, `class_filter_type`, `class_filter_array`, `group_by`, `main_node_only`, `ignore_visibility`, `limitation` |
| [can_instantiate_class_list](can_instantiate_class_list.md) | `group_id`, `parent_node`, `filter_type`, `fetch_id`, `as_object`, `group_by_class_group` |
| [can_instantiate_classes](can_instantiate_classes.md) | `parent_node` |
| [class](class.md) | `class_id`* |
| [class_attribute](class_attribute.md) | `attribute_id`*, `version_id` |
| [class_attribute_list](class_attribute_list.md) | `class_id`*, `version_id` |
| [class_list](class_list.md) | `as_object`, `group_list`, `sorts`, `limit` |
| [collected_info_collection](collected_info_collection.md) | `collection_id`, `contentobject_id` |
| [collected_info_count](collected_info_count.md) | `object_attribute_id`, `object_id`, `value`, `creator_id`, `user_identifier` |
| [collected_info_count_list](collected_info_count_list.md) | `object_attribute_id`* |
| [collected_info_list](collected_info_list.md) | `object_id`, `creator_id`, `user_identifier`, `limit`, `offset`, `sort_by` |
| [content_tree_menu_expiry](content_tree_menu_expiry.md) |  |
| [contentobject_attributes](contentobject_attributes.md) | `version`, `language_code` |
| [country_groups](country_groups.md) | `repeat` |
| [country_list](country_list.md) | `filter`, `value` |
| [draft_count](draft_count.md) |  |
| [draft_version_list](draft_version_list.md) | `offset`, `limit` |
| [keyword](keyword.md) | `alphabet`*, `classid`, `offset`, `limit`, `owner`, `sort_by`, `parent_node_id`, `include_duplicates`, `strict_matching`, `depth` |
| [keyword_count](keyword_count.md) | `alphabet`*, `classid`, `owner`, `parent_node_id`, `include_duplicates`, `strict_matching`, `depth` |
| [language_states](language_states.md) | `object`* |
| [list](list.md) | `parent_node_id`*, `sort_by`, `only_translated`, `language`, `offset`, `limit`, `depth`, `depth_operator`, `class_id`, `attribute_filter`, `extended_attribute_filter`, `class_filter_type`, `class_filter_array`, `group_by`, `main_node_only`, `ignore_visibility`, `limitation`, `as_object`, `objectname_filter`, `load_data_map` |
| [list_count](list_count.md) | `parent_node_id`*, `only_translated`, `language`, `class_filter_type`, `class_filter_array`, `attribute_filter`, `depth`, `depth_operator`, `ignore_visibility`, `limitation`, `main_node_only`, `extended_attribute_filter`, `objectname_filter` |
| [locale](locale.md) | `locale_code` |
| [locale_list](locale_list.md) | `with_variations` |
| [navigation_part](navigation_part.md) | `identifier`* |
| [navigation_parts](navigation_parts.md) |  |
| [node](node.md) | `node_id`, `node_path`, `language_code`, `remote_id` |
| [non_translation_list](non_translation_list.md) | `object_id`*, `version`* |
| [object](object.md) | `object_id`, `remote_id` |
| [object_by_attribute](object_by_attribute.md) | `identifier`* |
| [object_count_by_user_id](object_count_by_user_id.md) | `class_id`*, `user_id`*, `status` |
| [pending_count](pending_count.md) |  |
| [pending_list](pending_list.md) | `offset`, `limit` |
| [prioritized_language_codes](prioritized_language_codes.md) |  |
| [prioritized_languages](prioritized_languages.md) |  |
| [recent](recent.md) |  |
| [related_objects](related_objects.md) | `object_id`*, `attribute_identifier`, `all_relations`, `group_by_attribute`, `sort_by`, `limit`, `offset`, `as_object`, `load_data_map`, `ignore_visibility`, `related_class_identifiers` |
| [related_objects_count](related_objects_count.md) | `object_id`*, `attribute_identifier`, `all_relations` |
| [related_objects_ids](related_objects_ids.md) | `object_id`*, `attribute_identifier`, `all_relations` |
| [reverse_related_objects](reverse_related_objects.md) | `object_id`*, `attribute_identifier`, `all_relations`, `group_by_attribute`, `sort_by`, `ignore_visibility`, `limit`, `offset`, `as_object`, `load_data_map` |
| [reverse_related_objects_count](reverse_related_objects_count.md) | `object_id`*, `attribute_identifier`, `all_relations`, `ignore_visibility` |
| [reverse_related_objects_ids](reverse_related_objects_ids.md) | `object_id`*, `attribute_identifier`, `all_relations` |
| [same_classattribute_node](same_classattribute_node.md) | `classattribute_id`*, `value`*, `datatype`* |
| [search](search.md) | `text`*, `subtree_array`, `offset`, `limit`, `publish_timestamp`, `publish_date`, `section_id`, `class_id`, `class_attribute_id`, `ignore_visibility`, `limitation`, `sort_by` |
| [section_list](section_list.md) |  |
| [site_data](site_data.md) | `name`* |
| [tipafriend_top_list](tipafriend_top_list.md) | `offset`, `limit`, `start_time`, `end_time`, `duration`, `ascending`, `extended` |
| [translation_list](translation_list.md) |  |
| [trash_count](trash_count.md) | `objectname_filter`, `attribute_filter` |
| [trash_object_list](trash_object_list.md) | `offset`, `limit`, `objectname_filter`, `attribute_filter`, `sort_by`, `as_object` |
| [tree](tree.md) | `parent_node_id`*, `sort_by`, `only_translated`, `language`, `offset`, `limit`, `depth`, `depth_operator`, `class_id`, `attribute_filter`, `extended_attribute_filter`, `class_filter_type`, `class_filter_array`, `group_by`, `main_node_only`, `ignore_visibility`, `limitation`, `as_object`, `objectname_filter`, `load_data_map` |
| [tree_count](tree_count.md) | `parent_node_id`*, `only_translated`, `language`, `class_filter_type`, `class_filter_array`, `attribute_filter`, `depth`, `depth_operator`, `ignore_visibility`, `limitation`, `main_node_only`, `extended_attribute_filter`, `objectname_filter` |
| [version](version.md) | `object_id`*, `version_id`* |
| [version_count](version_count.md) | `contentobject`* |
| [version_list](version_list.md) | `contentobject`*, `offset`, `limit`, `sorts` |
| [view_bookmark](view_bookmark.md) | `uri`*, `path` |
| [view_top_list](view_top_list.md) | `class_id`, `section_id`, `offset`, `limit` |

- [All examples](../../README.md)

---

Generated by `./console exp:help:examples` from `kernel/content/function_definition.php`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
