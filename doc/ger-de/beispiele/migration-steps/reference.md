---
title: reference (migration step)
audience: developer, admin
category: examples
summary: Sets references (${ref.<name>}) for the steps and migrations after it in the run.
keywords: Referenz, reference, migration, YAML, step, expMigrationStepReference
type: generated
generated: exp:help:examples
---
# reference

Sets references (${ref.<name>}) for the steps and migrations after it in the run.

A step of a YAML migration with `type: reference`. The class `expMigrationStepReference` (`kernel/classes/migration/steps/expmigrationstepreference.php`) is registered in `settings/migration.ini` `[StepSettings] StepTypeList[reference]`.

## Keys

| Key | Type | Default | What it is |
|---|---|---|---|
| `set` | `map` | erforderlich | name => value: the references to set |

Besides these, every step takes the keys of the engine: `type`, `mode`, `if`, `references`, `environments`, `engines` and `comment` ([the file format](../../spezifikationen/6.0/migrationsdateiformat.md#a-step)). A key of type `identifier` names a node, an object or another part of the installation by id, remote id, path or name ([identifiers](../../spezifikationen/6.0/migrationsdateiformat.md#identifiers-and-the-id-map)).

## Values for references

`references: { <name>: <value> }` keeps a value of the step's result as `${ref.<name>}` for the steps and migrations after it:

| Value | What it is |
|---|---|
| `*` | every name the step sets, with its value |

## Undoing

Every step of this type undoes itself when its migration is rolled back without `down:`.

It touches no cache.

## Beispiel

```yaml
- type: reference
  set:
    news_parent: ${idmap.node.news}
```

## Siehe auch

- [All step types](README.md)
- [YAML migration steps](../../../eng-us/guides/migrations-yaml-steps.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepreference.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
