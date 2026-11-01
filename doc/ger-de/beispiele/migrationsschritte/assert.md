---
title: assert (Migrationsschritt)
audience: developer, admin
category: examples
summary: Lässt die Migration scheitern, wenn eine Bedingung falsch ist.
keywords: assert, migration, YAML, step, expMigrationStepAssert
type: generated
generated: exp:help:examples
---
# assert

Lässt die Migration scheitern, wenn eine Bedingung falsch ist.

Ein Schritt einer YAML-Migration mit `type: assert`. Die Klasse `expMigrationStepAssert` (`kernel/classes/migration/steps/expmigrationstepassert.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[assert]` registriert.

## Schlüssel

| Schlüssel | Typ | Standard | Was er ist |
|---|---|---|---|
| `that` | `condition` | erforderlich | die Bedingung, die wahr sein muss (wie ein "if:") |
| `message` | `string` |  | die Meldung des Fehlschlags (Standard: die Bedingung) |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Zurücknehmen

Jeder Schritt dieses Typs nimmt sich selbst zurück, wenn seine Migration ohne `down:` zurückgenommen wird.

Er berührt keinen Cache.

## Beispiel

```yaml
- type: assert
  that: { ref: news_parent, set: true }
  message: The news folder is missing
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepassert.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
