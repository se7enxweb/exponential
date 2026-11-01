---
title: reference (Migrationsschritt)
audience: developer, admin
category: examples
summary: Setzt Referenzen (${ref.<name>}) für die Schritte und Migrationen nach ihm im Lauf.
keywords: Referenz, reference, migration, YAML, step, expMigrationStepReference
type: generated
generated: exp:help:examples
---
# reference

Setzt Referenzen (${ref.<name>}) für die Schritte und Migrationen nach ihm im Lauf.

Ein Schritt einer YAML-Migration mit `type: reference`. Die Klasse `expMigrationStepReference` (`kernel/classes/migration/steps/expmigrationstepreference.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[reference]` registriert.

## Schlüssel

| Schlüssel | Typ | Standard | Was er ist |
|---|---|---|---|
| `set` | `map` | erforderlich | Name => Wert: die zu setzenden Referenzen |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `*` | jeder Name, den der Schritt setzt, mit seinem Wert |

## Zurücknehmen

Jeder Schritt dieses Typs nimmt sich selbst zurück, wenn seine Migration ohne `down:` zurückgenommen wird.

Er berührt keinen Cache.

## Beispiel

```yaml
- type: reference
  set:
    news_parent: ${idmap.node.news}
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepreference.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
