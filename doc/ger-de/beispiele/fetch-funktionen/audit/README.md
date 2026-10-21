---
title: Fetch-Funktionen von audit
audience: developer, admin
category: examples
order: 10
summary: Die Fetch-Funktionen des Moduls audit mit ihren Parametern.
keywords: Beispiele, Referenz, Fetch-Funktionen von audit, examples, reference
type: generated
generated: exp:help:examples
---
# Fetch-Funktionen des Moduls audit

Definiert in `kernel/audit/function_definition.php`. Ein mit * markierter Parameter ist erforderlich.

| Funktion | Parameter |
|---|---|
| [can_read](can_read.md) | `channel` |
| [chain_status](chain_status.md) | `channel`, `verify` |
| [count](count.md) | `channel`, `name`, `user`, `login`, `object`, `target`, `result`, `severity`, `request`, `job`, `run`, `from`, `to`, `q` |
| [event](event.md) | `id`* |
| [events](events.md) | `channel`, `name`, `user`, `login`, `object`, `target`, `result`, `severity`, `request`, `job`, `run`, `from`, `to`, `q`, `offset`, `limit` |
| [hidden_menu_links](hidden_menu_links.md) | `section` |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/audit/function_definition.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
