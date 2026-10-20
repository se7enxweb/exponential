---
title: Fetch-Funktionen von user
audience: developer, admin
category: examples
order: 10
summary: Die Fetch-Funktionen des Moduls user mit ihren Parametern.
keywords: Beispiele, Referenz, Fetch-Funktionen von user, examples, reference
type: generated
generated: exp:help:examples
---
# Fetch-Funktionen des Moduls user

Definiert in `kernel/user/function_definition.php`. Ein mit * markierter Parameter ist erforderlich.

| Funktion | Parameter |
|---|---|
| [account_status](account_status.md) | `user_id`* |
| [anonymous_count](anonymous_count.md) |  |
| [can_open](can_open.md) | `uri`* |
| [current_user](current_user.md) |  |
| [deactivation_offers](deactivation_offers.md) | `object_id`* |
| [has_access_to](has_access_to.md) | `module`*, `function`*, `user_id` |
| [is_logged_in](is_logged_in.md) | `user_id`* |
| [logged_in_count](logged_in_count.md) |  |
| [logged_in_list](logged_in_list.md) | `sort_by`, `offset`, `limit` |
| [logged_in_users](logged_in_users.md) | `sort_by`, `offset`, `limit` |
| [member_of](member_of.md) | `id`* |
| [user_role](user_role.md) | `user_id`* |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/user/function_definition.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
