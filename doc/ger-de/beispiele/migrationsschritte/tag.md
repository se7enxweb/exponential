---
title: tag (Migrationsschritt)
audience: developer, admin
category: examples
summary: Legt einen Tag von eztags mit seinen Übersetzungen und Synonymen an, ändert, sichert, löscht, verschiebt oder führt ihn zusammen.
keywords: tag, migration, YAML, step, expMigrationStepTag
type: generated
generated: exp:help:examples
---
# tag

Legt einen Tag von eztags mit seinen Übersetzungen und Synonymen an, ändert, sichert, löscht, verschiebt oder führt ihn zusammen.

Ein Schritt einer YAML-Migration mit `type: tag`. Die Klasse `expMigrationStepTag` (`kernel/classes/migration/steps/expmigrationsteptag.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[tag]` registriert.

## Modi

`mode:` ist einer von `create`, `update`, `ensure`, `delete`, `move`, `merge`; jeder Schritt nennt einen.

## Schlüssel

| Schlüssel | Typ | Modi | Standard | Was er ist |
|---|---|---|---|---|
| `tag` | `identifier` (tag) | update, delete, move, merge |  | der Tag: seine ID, remote:<Remote-ID> oder path:<Schlagwortpfad> |
| `parent` | `scalar` | create, ensure, move |  | der übergeordnete Tag (ID, remote:, path:); keiner oder 0: ganz oben. move: der neue übergeordnete Tag |
| `remote_id` | `string` | create, update, ensure |  | die Remote-ID des Tags; ensure findet den Tag über sie (ohne sie: über keyword: unterhalb von parent:) |
| `language` | `string` | create, update, ensure |  | die Sprache von keyword: und synonyms: (Standard: die Standardsprache der Inhalte); die Hauptsprache eines neuen Tags |
| `keyword` | `string` | create, update, ensure |  | das Schlagwort in der Sprache von language: |
| `keywords` | `map` | create, update, ensure |  | Sprache => das Schlagwort in ihr: die Übersetzungen |
| `main_language` | `string` | update, ensure |  | die Sprache der Hauptübersetzung des Tags (er muss eine Übersetzung in ihr haben) |
| `always_available` | `bool` | create, update, ensure |  | ob der Tag in Sprachen gezeigt wird, in die er nicht übersetzt ist (ein neuer Tag: false) |
| `synonyms` | `list` | create, update, ensure |  | Schlagwörter, die Synonyme des Tags sind, in der Sprache von language:; die fehlenden werden ergänzt |
| `into` | `identifier` (tag) | merge |  | der Tag, der die Objekte, Kinder und Synonyme von tag: übernimmt |
| `missing` | `string`: `fail`, `skip` | alle | `"fail"` | was geschieht, wenn der Tag oder die Erweiterung eztags fehlt: fail (Standard) oder skip |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `tag_id` | die ID des Tags |
| `remote_id` | seine Remote-ID |
| `keyword` | sein Schlagwort in seiner Hauptsprache |
| `path` | sein Schlagwortpfad (Topics/Sport) |
| `created` | true, wenn der Schritt den Tag angelegt hat |
| `changed` | true, wenn der Schritt etwas geändert hat |

## Zurücknehmen

Manche Schritte dieses Typs nehmen sich selbst zurück, wenn ihre Migration ohne `down:` zurückgenommen wird (die Klasse sagt, welche); eine Migration mit einem anderen Schritt davon braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Caches, die er berührt (nach dem Lauf geleert): `content`.

## Beispiel

```yaml
- type: tag
  mode: ensure
  parent: "path:Topics"
  remote_id: topic-sport
  keyword: Sport
  keywords: { ger-DE: Sport }
  synonyms: [ Sports ]
  references: { sport: tag_id }
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationsteptag.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
