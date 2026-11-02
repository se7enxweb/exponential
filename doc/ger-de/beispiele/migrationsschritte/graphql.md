---
title: graphql (Migrationsschritt)
audience: developer, admin
category: examples
summary: Führt eine Abfrage oder Mutation der GraphQL-API im Prozess als Benutzer des Laufs aus und behält ihre Antwort für Referenzen.
keywords: graphql, migration, YAML, step, expMigrationStepGraphql
type: generated
generated: exp:help:examples
---
# graphql

Führt eine Abfrage oder Mutation der GraphQL-API im Prozess als Benutzer des Laufs aus und behält ihre Antwort für Referenzen.

Ein Schritt einer YAML-Migration mit `type: graphql`. Die Klasse `expMigrationStepGraphql` (`kernel/classes/migration/steps/expmigrationstepgraphql.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[graphql]` registriert.

## Schlüssel

| Schlüssel | Typ | Standard | Was er ist |
|---|---|---|---|
| `persisted` | `string` |  | eine gespeicherte Abfrage: ihr Name oder ihr SHA-256 |
| `query` | `string` |  | der Text der Abfrage oder Mutation |
| `file` | `string` |  | eine .graphql-Datei neben der Migration |
| `variables` | `map` |  | die Variablen, Name => Wert (Referenzen erlaubt) |
| `operation` | `string` |  | die auszuführende Operation, wenn der Text mehrere hat |
| `allow_errors` | `bool` | `false` | true: Eine Antwort mit Fehlern gilt als bestanden, wenn sie Daten hat |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `data` | die Daten der Antwort (eine Zuordnung der Wurzelfelder) |
| `result` | der Wert des ersten Wurzelfelds der Antwort |
| `errors` | die Meldungen der Fehler (eine Liste, leer, wenn es keine gab) |

## Zurücknehmen

Ein Schritt dieses Typs nimmt sich nicht selbst zurück: Eine Migration mit ihm braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Er berührt keinen Cache.

## Beispiel

```yaml
- type: graphql
  query: |
    mutation ($parent: Int!) {
      createContent(parentId: $parent, classIdentifier: "folder", fields: [ { identifier: "name", value: "News" } ]) { contentId nodeId }
    }
  variables: { parent: 2 }
  references: { news: result }
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstepgraphql.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
