---
title: Fetch-Funktionen von infocollector
audience: developer, admin
category: examples
order: 10
summary: Die Fetch-Funktionen des Moduls infocollector mit ihren Parametern.
keywords: Beispiele, Referenz, Fetch-Funktionen von infocollector, examples, reference
type: generated
generated: exp:help:examples
---
# Fetch-Funktionen des Moduls infocollector

Definiert in `kernel/infocollector/function_definition.php`. Ein mit * markierter Parameter ist erforderlich.

| Funktion | Parameter |
|---|---|
| [collected_info_collection](collected_info_collection.md) | `collection_id`, `contentobject_id` |
| [collected_info_count](collected_info_count.md) | `object_attribute_id`, `object_id`, `value`, `creator_id`, `user_identifier` |
| [collected_info_count_list](collected_info_count_list.md) | `object_attribute_id`* |
| [collected_info_list](collected_info_list.md) | `object_id`, `creator_id`, `user_identifier`, `limit`, `offset`, `sort_by` |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/infocollector/function_definition.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
