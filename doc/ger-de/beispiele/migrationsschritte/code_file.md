---
title: code_file (Migrationsschritt)
audience: developer, admin
category: examples
summary: Writes, replaces or removes one code or design file below the allowed folders, with a backup and checksums.
keywords: code_file, migration, YAML, step, expMigrationStepCodeFile
type: generated
generated: exp:help:examples
---
# code_file

Writes, replaces or removes one code or design file below the allowed folders, with a backup and checksums.

Ein Schritt einer YAML-Migration mit `type: code_file`. Die Klasse `expMigrationStepCodeFile` (`kernel/classes/migration/steps/expmigrationstepcodefile.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[code_file]` registriert.

## Modi

`mode:` ist einer von `create`, `update`, `ensure`, `delete`; ohne es `ensure`.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `path` | `string` | alle | erforderlich | the file, relative to the installation, below a folder of [FileStepSettings] AllowedRootList[] (design, extension) |
| `source` | `string` | create, update, ensure |  | the new content: a file below the folder of the migration file (files/page.tpl) |
| `content` | `string` | create, update, ensure |  | the new content itself (instead of source:) |
| `checksum` | `string` | create, update, ensure |  | the SHA-256 the new content must have (64 hex digits) |
| `expect` | `string` | alle |  | the SHA-256 the file must have before the step, or absent: it must not exist |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `path` | the file, relative to the installation |
| `changed` | true, wenn sich die Datei geändert hat |
| `before` | its SHA-256 before (empty: it did not exist) |
| `after` | its SHA-256 after (empty: removed) |

## Zurücknehmen

Jeder Schritt dieses Typs nimmt sich selbst zurück, wenn seine Migration ohne `down:` zurückgenommen wird.

Er berührt keinen Cache.

## Beispiel

```yaml
- type: code_file
  mode: ensure
  path: design/mysite/templates/page_footer.tpl
  source: files/page_footer.tpl
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepcodefile.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
