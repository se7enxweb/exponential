---
title: cache (Migrationsschritt)
audience: developer, admin
category: examples
summary: Leert Caches von ezcache nach ID oder Tag und die Ansichtscaches einzelner Objekte, sofort oder nach dem Lauf.
keywords: cache, migration, YAML, step, expMigrationStepCache
type: generated
generated: exp:help:examples
---
# cache

Leert Caches von ezcache nach ID oder Tag und die Ansichtscaches einzelner Objekte, sofort oder nach dem Lauf.

Ein Schritt einer YAML-Migration mit `type: cache`. Die Klasse `expMigrationStepCache` (`kernel/classes/migration/steps/expmigrationstepcache.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[cache]` registriert.

## Schlüssel

| Schlüssel | Typ | Standard | Was er ist |
|---|---|---|---|
| `caches` | `list` |  | IDs von ezcache: content, template-block, exphttpcache, template-override ... |
| `tags` | `list` |  | Tags von ezcache: ini, template, content, image ... (jeder Cache mit dem Tag) |
| `objects` | `list` |  | Objekte (ID, remote:, path:), deren Inhaltsansichtscaches geleert werden |
| `when` | `string`: `end`, `now` | `"end"` | end: einmal nach dem Lauf geleert, mit den Caches der anderen Schritte; now: sofort geleert |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `caches` | die IDs und Tags, die der Schritt leert (oder für das Ende vormerkt), durch Kommas getrennt |
| `objects` | wie viele Objekte ihre Ansichtscaches geleert bekamen |

## Zurücknehmen

Jeder Schritt dieses Typs nimmt sich selbst zurück, wenn seine Migration ohne `down:` zurückgenommen wird.

Er berührt keinen Cache.

## Beispiel

```yaml
- type: cache
  caches: [ template-block ]
  tags: [ ini ]
  objects: [ "remote:home" ]
  when: now
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepcache.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
