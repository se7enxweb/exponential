---
title: Fehler und Statuscodes
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 190
summary: Jeder HTTP-Status, mit dem die REST-API von Exponential antwortet, mit seinem JSON-Body und dem, was eine App tun sollte - 200, 201, 400, 401 mit und ohne Body, 403 access_denied und insufficient_scope, 404, 405 mit Allow, 429 mit Retry-After, 500 und 501 - dazu eine Richtlinie für Wiederholungen und wie sich die Fehler von expservices unterscheiden.
keywords: Fehler, Statuscodes, 400, 401, 403, 404, 405, 429, 500, 501, invalid_token, expired_token, access_denied, insufficient_scope, rate_limited, not_found, invalid_request, not_implemented, Retry-After, WWW-Authenticate, Wiederholung, REST, Mobile App, errors, status codes, retry, mobile app
type: help
---
# Fehler und Statuscodes

Diese Seite richtet sich an App-Entwickler, die die Fehlerbehandlung ihres API-Clients schreiben. Sie führt jeden
Status auf, mit dem die REST-API antwortet, entnommen dem Code des Controllers und dem Leitfaden
[Persönliche API-Schlüssel](../../../anleitungen/api-schluessel.md#3-einen-schlüssel-mit-curl-benutzen), jeweils mit dem, was die App tun
sollte.

## Inhalt

1. [Die Tabelle](#die-tabelle)
2. [Der Fehler-Body](#der-fehler-body)
3. [Was die App tun sollte](#was-die-app-tun-sollte)
4. [Eine Richtlinie für Wiederholungen](#eine-richtlinie-für-wiederholungen)
5. [Fehler der Remote-Dienste](#fehler-der-remote-dienste)

## Die Tabelle

| Status | Body | Bedeutung |
|---|---|---|
| `200` | der Inhalt | ein Lesezugriff oder ein Entfernen war erfolgreich |
| `201` | `{"message":"Created","objectId":...,"nodeId":...}` | ein angelegtes Element wurde veröffentlicht |
| `400` | `{"error":"invalid_request","error_message":...}` | ein Feld eines Schreibzugriffs fehlt oder nennt eine Klasse oder Sprache, die nicht existiert |
| `401` | leer, mit `WWW-Authenticate: OAuth realm='Exponential REST'` | beim Server kamen keine Zugangsdaten an |
| `401` | `{"error":"invalid_token"}` | ein unbekanntes, falsches oder widerrufenes Token oder ein solcher Schlüssel, ein deaktivierter Inhaber oder ein Schlüssel in der URL |
| `401` | `{"error":"expired_token"}` | ein persönlicher API-Schlüssel nach seinem Ablaufdatum |
| `403` | `{"error":"access_denied","error_message":...}` | der Benutzer darf diese Inhalte nicht lesen oder schreiben |
| `403` | `{"error":"insufficient_scope"}` | einem persönlichen API-Schlüssel fehlt der Scope der Route, oder sein Inhaber hat die zugrunde liegende Berechtigung verloren |
| `404` | `{"error":"not_found",...}` | kein solcher Knoten und kein solches Objekt |
| `405` | mit einem Header `Allow` | keine Route nimmt diese Methode für diesen Pfad an |
| `429` | `{"error":"rate_limited",...}` mit `Retry-After` | ein persönlicher API-Schlüssel hat in dieser Minute mehr Anfragen gesendet als `RateLimitPerMinute` (standardmäßig 120) |
| `500` | `{"error":"failed",...}` | ein Anlegen oder Entfernen ist auf dem Server fehlgeschlagen; außerdem jeder Aufruf von `listAtom` (ein bekanntes Problem) |
| `501` | `{"error":"not_implemented",...}` | die Route zum Aktualisieren, nach der Berechtigungsprüfung |

Das `401` ohne Body wurde auf dem Demonstrationsserver bei einer Anfrage ohne Header `Authorization` beobachtet. Die
Status mit `error` und `error_message` stammen aus `errorResult()` von `ezp7xRestContentController`.

## Der Fehler-Body

Inhaltsfehler haben diese Form:

```json
{"error": "access_denied", "error_message": "You may not create 'article' below node 60 in eng-US (content/create)."}
```

`error` ist ein stabiler, maschinenlesbarer Grund (`invalid_request`, `access_denied`, `not_found`, `failed`,
`not_implemented`); `error_message` ist englischer Text für Logs und erfahrene Benutzer. Verzweigen Sie nach `error`
und nach dem Status, nie nach dem Text.

## Was die App tun sollte

| Status | Verhalten der App |
|---|---|
| `401` (OAuth-Token) | einmal mit dem Refresh-Token erneuern; schlägt das fehl, den Benutzer erneut zur Anmeldung schicken |
| `401` (sonstige) | die Zugangsdaten als ungültig behandeln; nicht damit wiederholen |
| `403` | „nicht erlaubt“ anzeigen, nicht wiederholen; bei `insufficient_scope` während der Entwicklung die Scopes des Schlüssels prüfen |
| `404` | das Element wurde entfernt oder nicht mehr veröffentlicht: aus dem Cache der App löschen und zurückgehen |
| `400` | die Meldung anzeigen; sie nennt das Feld |
| `429` | `Retry-After` Sekunden warten |
| `500`, `502`, `503`, `504`, Zeitüberschreitungen | Lesezugriffe mit Backoff wiederholen; bei Schreibzugriffen zuerst prüfen, ob der Schreibzugriff stattgefunden hat |
| `501` | eine Funktionslücke: die Route verwenden, die auf [Was die APIs nicht tun](../was-die-apis-nicht-koennen.md) genannt ist |

## Eine Richtlinie für Wiederholungen

- Wiederholen Sie automatisch nur idempotente Lesezugriffe: zwei- oder dreimal, mit wachsenden Wartezeiten und etwas
  Zufall.
- Wiederholen Sie ein Anlegen nie blind: Es kann erfolgreich gewesen sein, bevor die Verbindung abbrach
  ([Inhalte anlegen und entfernen](inhalte-anlegen-und-entfernen.md#ratschläge-für-das-design-schreibender-apps)).
- Geben Sie bei `401` und `403` auf; durch Wiederholen ändern sie sich nicht.
- Protokollieren Sie Status, Route und Zeit; der Server führt in seinen Logs eine Request-ID (der Response-Header
  `X-Exp-Request-Id` war bei den ezjscore-Antworten des Demonstrationsservers vorhanden), die dem Administrator der
  Website hilft, die Anfrage zu finden ([Einen Fehler über seine Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md)).

## Fehler der Remote-Dienste

Die Remote-Dienste antworten anders: Ein Aufruf, der den Dienst erreicht, antwortet mit HTTP 200, und das Ergebnis
steht in `content.ok`, bei einem Fehlschlag mit `content.error.code` (400, 401, 403, 404, 409, 422, 500). Ein Status
ungleich 200 oder ein Body, der kein JSON ist, ist ein Transportfehler
([expservices aus einer App aufrufen](../json-endpunkte/expservices-aus-einer-app-aufrufen.md#der-umschlag)).

## Verwandte Seiten

- [Persönliche API-Schlüssel: Fehlerbehebung](../../../anleitungen/api-schluessel.md#12-fehlerbehebung)
- [Der Authorization-Header und Proxys](../anmeldung/der-authorization-header-und-proxys.md)
- [Routenreferenz](routenreferenz.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
