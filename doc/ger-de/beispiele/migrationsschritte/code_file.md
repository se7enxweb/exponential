---
title: code_file (Migrationsschritt)
audience: developer, admin
category: examples
summary: Schreibt, ersetzt oder entfernt eine Code- oder Designdatei unterhalb der erlaubten Ordner, mit Sicherung und Prüfsummen.
keywords: code_file, migration, YAML, step, expMigrationStepCodeFile
type: generated
generated: exp:help:examples
---
# code_file

Schreibt, ersetzt oder entfernt eine Code- oder Designdatei unterhalb der erlaubten Ordner, mit Sicherung und Prüfsummen.

Ein Schritt einer YAML-Migration mit `type: code_file`. Die Klasse `expMigrationStepCodeFile` (`kernel/classes/migration/steps/expmigrationstepcodefile.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[code_file]` registriert.

## Modi

`mode:` ist einer von `create`, `update`, `ensure`, `delete`; ohne es `ensure`.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `path` | `string` | alle | erforderlich | die Datei, relativ zur Installation, unterhalb eines Ordners aus [FileStepSettings] AllowedRootList[] (design, extension) |
| `source` | `string` | create, update, ensure |  | der neue Inhalt: eine Datei unterhalb des Ordners der Migrationsdatei (files/page.tpl) |
| `content` | `string` | create, update, ensure |  | der neue Inhalt selbst (statt source:) |
| `checksum` | `string` | create, update, ensure |  | der SHA-256, den der neue Inhalt haben muss (64 Hexziffern) |
| `expect` | `string` | alle |  | der SHA-256, den die Datei vor dem Schritt haben muss, oder fehlend: Sie darf nicht existieren |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `path` | die Datei, relativ zur Installation |
| `changed` | true, wenn sich die Datei geändert hat |
| `before` | ihr SHA-256 vorher (leer: Sie existierte nicht) |
| `after` | ihr SHA-256 danach (leer: entfernt) |

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
