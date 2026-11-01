---
title: assert (migration step)
audience: developer, admin
category: examples
summary: Fails the migration when a condition is false.
keywords: assert, migration, YAML, step, expMigrationStepAssert
type: generated
generated: exp:help:examples
---
# assert

Fails the migration when a condition is false.

A step of a YAML migration with `type: assert`. The class `expMigrationStepAssert` (`kernel/classes/migration/steps/expmigrationstepassert.php`) is registered in `settings/migration.ini` `[StepSettings] StepTypeList[assert]`.

## Keys

| Key | Type | Default | What it is |
|---|---|---|---|
| `that` | `condition` | erforderlich | the condition that must be true (as an "if:") |
| `message` | `string` |  | the message of the failure (default: the condition) |

Besides these, every step takes the keys of the engine: `type`, `mode`, `if`, `references`, `environments`, `engines` and `comment` ([the file format](../../spezifikationen/6.0/migrationsdateiformat.md#a-step)). A key of type `identifier` names a node, an object or another part of the installation by id, remote id, path or name ([identifiers](../../spezifikationen/6.0/migrationsdateiformat.md#identifiers-and-the-id-map)).

## Undoing

Every step of this type undoes itself when its migration is rolled back without `down:`.

It touches no cache.

## Beispiel

```yaml
- type: assert
  that: { ref: news_parent, set: true }
  message: The news folder is missing
```

## Siehe auch

- [All step types](README.md)
- [YAML migration steps](../../../eng-us/guides/migrations-yaml-steps.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepassert.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
