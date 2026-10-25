---
title: Workflow-Auslöser
audience: developer, admin
category: examples
order: 5
summary: "Jeder Auslösepunkt für Workflows: die Operationen für Inhalte, Shop und Benutzer und ihre Auslöser davor und danach."
keywords: Auslöser, trigger, workflow, operation, pre_publish, post_publish
type: generated
generated: exp:help:examples
---
# Workflow-Auslöser

Die Stellen, an denen ein Workflow laufen kann: jede Operation von `kernel/content/operation_definition.php`, `kernel/shop/operation_definition.php`, `kernel/user/operation_definition.php` und ihre Auslöser `pre_` (davor) und `post_` (danach). Verbinden Sie einen Workflow unter Setup > Auslöser; die Parameter der Operation sind das, was seine Ereignisse erhalten.

| Modul | Operation | Auslöser | Wann | Parameter |
|---|---|---|---|---|
| `content` | `addlocation` | `pre_addlocation` | davor | node_id, object_id, select_node_id_array |
| `content` | `addlocation` | `post_addlocation` | danach | node_id, object_id, select_node_id_array |
| `content` | `createnodefeed` | `pre_createnodefeed` | davor | node_id |
| `content` | `createnodefeed` | `post_createnodefeed` | danach | node_id |
| `content` | `delete` | `pre_delete` | davor | node_id_list, move_to_trash |
| `content` | `delete` | `post_delete` | danach | node_id_list, move_to_trash |
| `content` | `hide` | `pre_hide` | davor | node_id |
| `content` | `hide` | `post_hide` | danach | node_id |
| `content` | `move` | `pre_move` | davor | node_id, object_id, new_parent_node_id |
| `content` | `move` | `post_move` | danach | node_id, object_id, new_parent_node_id |
| `content` | `publish` | `pre_publish` | davor | object_id, version, notify |
| `content` | `publish` | `post_publish` | danach | object_id, version, notify |
| `content` | `read` | `pre_read` | davor | node_id, user_id, language_code |
| `content` | `read` | `post_read` | danach | node_id, user_id, language_code |
| `content` | `removelocation` | `pre_removelocation` | davor | node_list |
| `content` | `removelocation` | `post_removelocation` | danach | node_list |
| `content` | `removenodefeed` | `pre_removenodefeed` | davor | node_id |
| `content` | `removenodefeed` | `post_removenodefeed` | danach | node_id |
| `content` | `removetranslation` | `pre_removetranslation` | davor | object_id, language_id_list, node_id |
| `content` | `removetranslation` | `post_removetranslation` | danach | object_id, language_id_list, node_id |
| `content` | `sort` | `pre_sort` | davor | node_id, sorting_field, sorting_order |
| `content` | `sort` | `post_sort` | danach | node_id, sorting_field, sorting_order |
| `content` | `swap` | `pre_swap` | davor | node_id, selected_node_id, node_id_list |
| `content` | `swap` | `post_swap` | danach | node_id, selected_node_id, node_id_list |
| `content` | `translationavailable` | `pre_translationavailable` | davor | object_id, status |
| `content` | `translationavailable` | `post_translationavailable` | danach | object_id, status |
| `content` | `updatealwaysavailable` | `pre_updatealwaysavailable` | davor | object_id, new_always_available, node_id |
| `content` | `updatealwaysavailable` | `post_updateAlwaysAvailable` | danach | object_id, new_always_available, node_id |
| `content` | `updateinitiallanguage` | `pre_updateinitiallanguage` | davor | object_id, new_initial_language_id, node_id |
| `content` | `updateinitiallanguage` | `post_updateinitiallanguage` | danach | object_id, new_initial_language_id, node_id |
| `content` | `updatemainassignment` | `pre_updatemainassignment` | davor | main_assignment_id, object_id, main_assignment_parent_id |
| `content` | `updatemainassignment` | `post_updatemainassignment` | danach | main_assignment_id, object_id, main_assignment_parent_id |
| `content` | `updateobjectstate` | `pre_updateobjectstate` | davor | object_id, state_id_list |
| `content` | `updateobjectstate` | `post_updateobjectstate` | danach | object_id, state_id_list |
| `content` | `updatepriority` | `pre_updatepriority` | davor | node_id, priority, priority_id |
| `content` | `updatepriority` | `post_updatepriority` | danach | node_id, priority, priority_id |
| `content` | `updatesection` | `pre_updatesection` | davor | node_id, selected_section_id, update_search_indexes, scope, old_section_id, check_access |
| `content` | `updatesection` | `post_updatesection` | danach | node_id, selected_section_id, update_search_indexes, scope, old_section_id, check_access |
| `shop` | `addtobasket` | `pre_addtobasket` | davor | object_id, option_list, quantity, basket_id |
| `shop` | `addtobasket` | `post_addtobasket` | danach | object_id, option_list, quantity, basket_id |
| `shop` | `checkout` | `pre_checkout` | davor | order_id |
| `shop` | `checkout` | `post_checkout` | danach | order_id |
| `shop` | `confirmorder` | `pre_confirmorder` | davor | order_id |
| `shop` | `updatebasket` | `pre_updatebasket` | davor | item_count_list, item_id_list |
| `shop` | `updatebasket` | `post_updatebasket` | danach | item_count_list, item_id_list |
| `user` | `activation` | `pre_activation` | davor | user_id, user_hash, is_enabled |
| `user` | `activation` | `post_activation` | danach | user_id, user_hash, is_enabled |
| `user` | `forgotpassword` | `pre_forgotpassword` | davor | user_id, password_hash, time |
| `user` | `forgotpassword` | `post_forgotpassword` | danach | user_id, password_hash, time |
| `user` | `password` | `pre_password` | davor | user_id, new_password |
| `user` | `password` | `post_password` | danach | user_id, new_password |
| `user` | `preferences` | `pre_preferences` | davor | key, value |
| `user` | `preferences` | `post_preferences` | danach | key, value |
| `user` | `register` | `pre_register` | davor | user_id |
| `user` | `register` | `post_register` | danach | user_id |
| `user` | `setsettings` | `pre_setsettings` | davor | user_id, is_enabled, max_login |
| `user` | `setsettings` | `post_setsettings` | danach | user_id, is_enabled, max_login |

## Siehe auch

- [Workflow-Ereignistypen](README.md)
- [Workflows](../../anleitungen/workflows.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/operation_definition.php`, `kernel/shop/operation_definition.php`, `kernel/user/operation_definition.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
