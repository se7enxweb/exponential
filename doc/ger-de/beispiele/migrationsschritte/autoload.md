---
title: autoload (Migrationsschritt)
audience: developer, admin
category: examples
summary: Erzeugt das Autoload-Array der Erweiterungen oder der Kernel-Klassen, die Erweiterungen ersetzen, neu.
keywords: autoload, migration, YAML, step, expMigrationStepAutoload
type: generated
generated: exp:help:examples
---
# autoload

Erzeugt das Autoload-Array der Erweiterungen oder der Kernel-Klassen, die Erweiterungen ersetzen, neu.

Ein Schritt einer YAML-Migration mit `type: autoload`. Die Klasse `expMigrationStepAutoload` (`kernel/classes/migration/steps/expmigrationstepautoload.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[autoload]` registriert.

## Schlüssel

| Schlüssel | Typ | Standard | Was er ist |
|---|---|---|---|
| `scope` | `string`: `extension`, `kernel-override` | `"extension"` | extension: die Klassen der Erweiterungen; kernel-override: die Kernel-Klassen, die Erweiterungen ersetzen |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `file` | das geschriebene Autoload-Array, unterhalb des Wurzelverzeichnisses |

## Zurücknehmen

Jeder Schritt dieses Typs nimmt sich selbst zurück, wenn seine Migration ohne `down:` zurückgenommen wird.

Er berührt keinen Cache.

## Beispiel

```yaml
- type: autoload
  scope: extension
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepautoload.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
