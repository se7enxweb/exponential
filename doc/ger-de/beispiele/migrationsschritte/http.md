---
title: http (Migrationsschritt)
audience: developer, admin
category: examples
summary: Sendet eine HTTP-Anfrage an ein anderes System, mit Geheimnissen über ihren Namen und einer optionalen Signatur, und behält die Antwort für Referenzen.
keywords: http, migration, YAML, step, expMigrationStepHttp
type: generated
generated: exp:help:examples
---
# http

Sendet eine HTTP-Anfrage an ein anderes System, mit Geheimnissen über ihren Namen und einer optionalen Signatur, und behält die Antwort für Referenzen.

Ein Schritt einer YAML-Migration mit `type: http`. Die Klasse `expMigrationStepHttp` (`kernel/classes/migration/steps/expmigrationstephttp.php`) ist in `settings/migration.ini` `[StepSettings] StepTypeList[http]` registriert.

## Schlüssel

| Schlüssel | Typ | Standard | Was er ist |
|---|---|---|---|
| `method` | `string`: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `HEAD` | `"GET"` | die Methode |
| `url` | `string` | erforderlich | http:// oder https:// mit einem Host, ohne Benutzer oder Passwort |
| `headers` | `map` |  | Header => Wert (keine Geheimnisse hier: secret_headers:) |
| `secret_headers` | `map` |  | Header => der Name eines Geheimnisses (Setup > Geheimnisse, oder env:<VARIABLE>) |
| `bearer` | `string` |  | der Name eines Geheimnisses, das als Authorization: Bearer <Wert> gesendet wird |
| `body` | `string` |  | der Inhalt als Text |
| `json` | `any` |  | der Inhalt als JSON (Content-Type: application/json) |
| `sign` | `string` |  | der Name eines Geheimnisses, das die Anfrage signiert, wie Webhooks von Exponential signiert werden |
| `expect` | `list` |  | die Statuscodes, die als Erfolg gelten (Standard: jeder 2xx) |
| `timeout` | `int` | `30` | Sekunden |

Daneben nimmt jeder Schritt die Schlüssel der Engine: `type`, `mode`, `if`, `references`, `environments`, `engines` und `comment` ([das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md#ein-schritt)). Ein Schlüssel vom Typ `identifier` nennt einen Knoten, ein Objekt oder einen anderen Teil der Installation über ID, Remote-ID, Pfad oder Namen ([Bezeichner](../../spezifikationen/6.0/migrationsdateiformat.md#bezeichner-und-die-id-zuordnung)).

## Werte für Referenzen

`references: { <name>: <wert> }` behält einen Wert des Ergebnisses des Schritts als `${ref.<name>}` für die Schritte und Migrationen nach ihm:

| Wert | Was er ist |
|---|---|
| `status` | der Statuscode der Antwort |
| `body` | der Inhalt der Antwort (höchstens 64 KB) |
| `json` | der Inhalt als JSON gelesen (null, wenn er keines ist) |

## Zurücknehmen

Ein Schritt dieses Typs nimmt sich nicht selbst zurück: Eine Migration mit ihm braucht `down:` oder eine Rücknahme auf ihren Sicherungspunkt.

Er berührt keinen Cache.

## Beispiel

```yaml
- type: http
  method: POST
  url: https://cdn.example.com/purge
  bearer: cdn.token
  json: { paths: [ "/news/*" ] }
  expect: [ 200, 202 ]
  references: { purge_status: status }
```

## Siehe auch

- [Alle Schritttypen](README.md)
- [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`, `kernel/classes/migration/steps/expmigrationstephttp.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
