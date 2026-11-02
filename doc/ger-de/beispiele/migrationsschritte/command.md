---
title: command (Migrationsschritt)
audience: developer, admin
category: examples
summary: Führt einen exp:*-Konsolenbefehl der Installation aus, den migration.ini erlaubt, ohne Shell.
keywords: Befehl, command, migration, YAML, step, expMigrationStepCommand
type: generated
generated: exp:help:examples
---
# command

Führt einen exp:*-Konsolenbefehl der Installation aus, den migration.ini erlaubt, ohne Shell.

Ein Schritt einer YAML-Migration mit `type: command`. Die Klasse `expMigrationStepCommand` (`kernel/classes/migration/steps/expmigrationstepcommand.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[command]` registriert.

## Schlüssel

| Schlüssel | Typ | Standard | Was er ist |
|---|---|---|---|
| `command` | `string` | erforderlich | ein exp:*-Befehl von ./console aus migration.ini [StepSettings] CommandAllowList[] |
| `arguments` | `list` |  | seine Argumente, jedes ein Argument |
| `timeout` | `int` | `600` | Sekunden |
| `expect_exit` | `int` | `0` | der Exit-Code, der als Erfolg gilt |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `exit` | der Exit-Code |
| `output` | was der Befehl ausgegeben hat (höchstens 64 KB) |

## Zurücknehmen

Ein Schritt dieses Typs nimmt sich nicht selbst zurück: Eine Migration mit ihm braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Er berührt keinen Cache.

## Beispiel

```yaml
- type: command
  command: exp:services:compile
  references: { compiled: exit }
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepcommand.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
