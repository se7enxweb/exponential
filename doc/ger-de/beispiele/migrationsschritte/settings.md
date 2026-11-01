---
title: settings (Migrationsschritt)
audience: developer, admin
category: examples
summary: Ändert eine INI-Datei von settings/override oder settings/siteaccess/<name> über den INI-Editor, mit Sicherungskopie.
keywords: settings, migration, YAML, step, expMigrationStepSettings
type: generated
generated: exp:help:examples
---
# settings

Ändert eine INI-Datei von settings/override oder settings/siteaccess/<name> über den INI-Editor, mit Sicherungskopie.

Ein Schritt einer YAML-Migration mit `type: settings`. Die Klasse `expMigrationStepSettings` (`kernel/classes/migration/steps/expmigrationstepsettings.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[settings]` registriert.

## Schlüssel

| Schlüssel | Typ | Standard | Was er ist |
|---|---|---|---|
| `file` | `string` | erforderlich | die INI-Datei: site.ini, content.ini ... |
| `siteaccess` | `string` |  | der Siteaccess (settings/siteaccess/<name>); ohne: settings/override |
| `set` | `map` |  | Block => Variable => Wert (eine Liste: das Array; eine Map: Schlüssel eines Hashs) |
| `add` | `map` |  | Block => Variable => Werte, die dem Array hinzugefügt werden |
| `remove` | `map` |  | Block => Liste aus Var, Var[key] oder Var[]=value |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `file` | die geschriebene Datei, relativ zur Installation |
| `changed` | true, wenn sich die Datei geändert hat |

## Zurücknehmen

Ein Schritt dieses Typs nimmt sich nicht selbst zurück: Eine Migration mit ihm braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Caches, die er berührt (nach dem Lauf geleert): `tag:ini`.

## Beispiel

```yaml
- type: settings
  file: site.ini
  siteaccess: admin
  set:
    SiteSettings:
      DefaultPage: content/dashboard
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepsettings.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
