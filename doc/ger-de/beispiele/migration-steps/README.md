---
title: Migration step types
audience: developer, admin
category: examples
order: 0
summary: Every step type of YAML migrations with its modes, keys, reference values and an example.
keywords: Beispiele, Referenz, Migration step types, examples, reference
type: generated
generated: exp:help:examples
---
# Migration step types

Every step type of YAML migrations the kernel registers in `settings/migration.ini` `[StepSettings] StepTypeList`, one page each, made from the type's schema: its modes, keys, the values `references:` can take, whether it undoes itself, the caches it touches and an example. An extension registers its own types the same way ([how](../../../eng-us/guides/migrations-yaml-steps.md)).

| Step type | Modes | What it does |
|---|---|---|
| [reference](reference.md) |  | Sets references (${ref.<name>}) for the steps and migrations after it in the run |
| [assert](assert.md) |  | Fails the migration when a condition is false |

- [Alle Beispiele](../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
