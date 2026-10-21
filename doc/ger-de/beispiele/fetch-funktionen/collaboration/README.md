---
title: Fetch-Funktionen von collaboration
audience: developer, admin
category: examples
order: 10
summary: Die Fetch-Funktionen des Moduls collaboration mit ihren Parametern.
keywords: Beispiele, Referenz, Fetch-Funktionen von collaboration, examples, reference
type: generated
generated: exp:help:examples
---
# Fetch-Funktionen des Moduls collaboration

Definiert in `kernel/collaboration/function_definition.php`. Ein mit * markierter Parameter ist erforderlich.

| Funktion | Parameter |
|---|---|
| [group_tree](group_tree.md) | `parent_group_id`*, `sort_by`, `offset`, `limit`, `depth` |
| [inbox](inbox.md) | `status`, `role`, `type`, `group_id`, `offset`, `limit` |
| [inbox_row](inbox_row.md) | `item_id`* |
| [item_count](item_count.md) | `is_read`, `is_active`, `parent_group_id`, `status` |
| [item_list](item_list.md) | `sort_by`, `offset`, `limit`, `status`, `is_read`, `is_active`, `parent_group_id` |
| [message_list](message_list.md) | `item_id`*, `sort_by`, `offset`, `limit` |
| [participant](participant.md) | `item_id`*, `participant_id` |
| [participant_list](participant_list.md) | `item_id`, `sort_by`, `offset`, `limit` |
| [participant_map](participant_map.md) | `item_id`, `sort_by`, `offset`, `limit`, `field` |
| [tree_count](tree_count.md) | `parent_node_id`*, `class_filter_type`, `class_filter_array`, `depth` |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/collaboration/function_definition.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
