---
title: Workflow triggers
audience: developer, admin
category: examples
order: 5
summary: "Every workflow trigger point: the content, shop and user operations and their before and after triggers."
keywords: trigger, workflow, operation, pre_publish, post_publish
type: generated
generated: exp:help:examples
---
# Workflow triggers

The points where a workflow can run: each operation of `kernel/content/operation_definition.php`, `kernel/shop/operation_definition.php`, `kernel/user/operation_definition.php` and its `pre_` (before) and `post_` (after) triggers. Connect a workflow at Setup > Triggers; the operation's parameters are what its events receive.

| Module | Operation | Trigger | When | Parameters |
|---|---|---|---|---|
| `content` | `addlocation` | `pre_addlocation` | before | node_id, object_id, select_node_id_array |
| `content` | `addlocation` | `post_addlocation` | after | node_id, object_id, select_node_id_array |
| `content` | `createnodefeed` | `pre_createnodefeed` | before | node_id |
| `content` | `createnodefeed` | `post_createnodefeed` | after | node_id |
| `content` | `delete` | `pre_delete` | before | node_id_list, move_to_trash |
| `content` | `delete` | `post_delete` | after | node_id_list, move_to_trash |
| `content` | `hide` | `pre_hide` | before | node_id |
| `content` | `hide` | `post_hide` | after | node_id |
| `content` | `move` | `pre_move` | before | node_id, object_id, new_parent_node_id |
| `content` | `move` | `post_move` | after | node_id, object_id, new_parent_node_id |
| `content` | `publish` | `pre_publish` | before | object_id, version, notify |
| `content` | `publish` | `post_publish` | after | object_id, version, notify |
| `content` | `read` | `pre_read` | before | node_id, user_id, language_code |
| `content` | `read` | `post_read` | after | node_id, user_id, language_code |
| `content` | `removelocation` | `pre_removelocation` | before | node_list |
| `content` | `removelocation` | `post_removelocation` | after | node_list |
| `content` | `removenodefeed` | `pre_removenodefeed` | before | node_id |
| `content` | `removenodefeed` | `post_removenodefeed` | after | node_id |
| `content` | `removetranslation` | `pre_removetranslation` | before | object_id, language_id_list, node_id |
| `content` | `removetranslation` | `post_removetranslation` | after | object_id, language_id_list, node_id |
| `content` | `sort` | `pre_sort` | before | node_id, sorting_field, sorting_order |
| `content` | `sort` | `post_sort` | after | node_id, sorting_field, sorting_order |
| `content` | `swap` | `pre_swap` | before | node_id, selected_node_id, node_id_list |
| `content` | `swap` | `post_swap` | after | node_id, selected_node_id, node_id_list |
| `content` | `translationavailable` | `pre_translationavailable` | before | object_id, status |
| `content` | `translationavailable` | `post_translationavailable` | after | object_id, status |
| `content` | `updatealwaysavailable` | `pre_updatealwaysavailable` | before | object_id, new_always_available, node_id |
| `content` | `updatealwaysavailable` | `post_updateAlwaysAvailable` | after | object_id, new_always_available, node_id |
| `content` | `updateinitiallanguage` | `pre_updateinitiallanguage` | before | object_id, new_initial_language_id, node_id |
| `content` | `updateinitiallanguage` | `post_updateinitiallanguage` | after | object_id, new_initial_language_id, node_id |
| `content` | `updatemainassignment` | `pre_updatemainassignment` | before | main_assignment_id, object_id, main_assignment_parent_id |
| `content` | `updatemainassignment` | `post_updatemainassignment` | after | main_assignment_id, object_id, main_assignment_parent_id |
| `content` | `updateobjectstate` | `pre_updateobjectstate` | before | object_id, state_id_list |
| `content` | `updateobjectstate` | `post_updateobjectstate` | after | object_id, state_id_list |
| `content` | `updatepriority` | `pre_updatepriority` | before | node_id, priority, priority_id |
| `content` | `updatepriority` | `post_updatepriority` | after | node_id, priority, priority_id |
| `content` | `updatesection` | `pre_updatesection` | before | node_id, selected_section_id |
| `content` | `updatesection` | `post_updatesection` | after | node_id, selected_section_id |
| `shop` | `addtobasket` | `pre_addtobasket` | before | object_id, option_list, quantity, basket_id |
| `shop` | `addtobasket` | `post_addtobasket` | after | object_id, option_list, quantity, basket_id |
| `shop` | `checkout` | `pre_checkout` | before | order_id |
| `shop` | `checkout` | `post_checkout` | after | order_id |
| `shop` | `confirmorder` | `pre_confirmorder` | before | order_id |
| `shop` | `updatebasket` | `pre_updatebasket` | before | item_count_list, item_id_list |
| `shop` | `updatebasket` | `post_updatebasket` | after | item_count_list, item_id_list |
| `user` | `activation` | `pre_activation` | before | user_id, user_hash, is_enabled |
| `user` | `activation` | `post_activation` | after | user_id, user_hash, is_enabled |
| `user` | `forgotpassword` | `pre_forgotpassword` | before | user_id, password_hash, time |
| `user` | `forgotpassword` | `post_forgotpassword` | after | user_id, password_hash, time |
| `user` | `password` | `pre_password` | before | user_id, new_password |
| `user` | `password` | `post_password` | after | user_id, new_password |
| `user` | `preferences` | `pre_preferences` | before | key, value |
| `user` | `preferences` | `post_preferences` | after | key, value |
| `user` | `register` | `pre_register` | before | user_id |
| `user` | `register` | `post_register` | after | user_id |
| `user` | `setsettings` | `pre_setsettings` | before | user_id, is_enabled, max_login |
| `user` | `setsettings` | `post_setsettings` | after | user_id, is_enabled, max_login |

## Related

- [Workflow event types](README.md)
- [Workflows](../../guides/workflows.md)

---

Generated by `./console exp:help:examples` from `kernel/content/operation_definition.php`, `kernel/shop/operation_definition.php`, `kernel/user/operation_definition.php`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
