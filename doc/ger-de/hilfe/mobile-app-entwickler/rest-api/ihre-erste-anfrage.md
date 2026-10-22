---
title: Ihre erste Anfrage
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 110
summary: Den ersten authentifizierten Aufruf der REST-Inhalts-API von Exponential ausführen - prüfen, ob die API antwortet, Zugangsdaten senden, Knoten 2 unter v1 und v2 lesen und den Aufruf in Kotlin, Swift und JavaScript nachbauen.
keywords: erste Anfrage, curl, REST, v1, v2, Authorization, Bearer, API-Schlüssel, OAuth, 401, Knoten 2, Kotlin, Swift, JavaScript, fetch, Mobile App, Tutorial, Zugangsdaten, first request, API key, node 2, mobile app, tutorial
type: help
---
# Ihre erste Anfrage

Diese Seite richtet sich an App-Entwickler, die vor allem anderen einen funktionierenden Aufruf möchten. In etwa zehn
Minuten prüfen Sie, ob die REST-API antwortet, senden Zugangsdaten und lesen den Wurzelknoten, zuerst mit `curl`,
dann aus App-Code.

## Inhalt

1. [Schritt 1: Antwortet die API?](#schritt-1-antwortet-die-api)
2. [Schritt 2: Zugangsdaten für die Entwicklung besorgen](#schritt-2-zugangsdaten-für-die-entwicklung-besorgen)
3. [Schritt 3: Knoten 2 lesen](#schritt-3-knoten-2-lesen)
4. [Schritt 4: derselbe Aufruf aus App-Code](#schritt-4-derselbe-aufruf-aus-app-code)
5. [Wenn es nicht funktioniert](#wenn-es-nicht-funktioniert)

## Schritt 1: Antwortet die API?

```bash
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/api/ezp/v1/content/node/2
```

| Antwort | Bedeutung |
|---|---|
| `401` | Die API ist vorhanden und verlangt Zugangsdaten (die Voreinstellung). Machen Sie weiter. |
| `404` | `ezprestapi` ist auf dieser Installation nicht aktiv, oder der Pfad ist falsch. Fragen Sie den Administrator. |
| `200` | Die Authentifizierung ist dort ausgeschaltet; Aufrufe laufen als der Standardbenutzer. |

Die Antwort `401` hat keinen Inhalt und den Header `WWW-Authenticate: OAuth realm='Exponential REST'`.

## Schritt 2: Zugangsdaten für die Entwicklung besorgen

Öffnen Sie auf einer Testinstallation, auf der Sie ein Konto mit der Richtlinie `apikey/create` haben,
**Mein Konto > API-Zugang** (`/apikey/list`), erstellen Sie einen Schlüssel mit dem Scope `read` und kopieren Sie
ihn; er wird nur einmal angezeigt ([Persönliche API-Schlüssel für Werkzeuge](../anmeldung/persoenliche-api-schluessel-fuer-werkzeuge.md)).
Legen Sie ihn in eine Umgebungsvariable, nicht in eine Datei, die Sie committen:

```bash
export EXP_API_KEY='expk_...'
```

Ein persönlicher API-Schlüssel ist für die Entwicklung richtig. Eine veröffentlichte App verwendet für ihre Benutzer
OAuth2 ([Zugangsdaten auswählen](../anmeldung/zugangsdaten-auswaehlen.md)).

## Schritt 3: Knoten 2 lesen

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v1/content/node/2
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/2
```

Beide antworten mit `200` und demselben Inhalt: `metadata` (`objectName`, `classIdentifier`, `nodeId` 2 ...) und
`links` ([Einen Knoten lesen](einen-knoten-lesen.md)). Knoten 2 ist in einer Standardinstallation die Inhaltswurzel; auf
Ihrer Installation kann er anders heißen.

Senden Sie mit einem OAuth2-Access-Token statt eines Schlüssels `Authorization: OAuth <token>`; die REST-Schicht
akzeptiert für OAuth-Tokens auch `Bearer <token>`.

## Schritt 4: derselbe Aufruf aus App-Code

Die Anfrage ist schlichtes HTTPS mit einem Header. Drei Skizzen, die Sie an den HTTP-Client und die Fehlerbehandlung
Ihres Projekts anpassen:

```kotlin
// Kotlin, OkHttp
val request = Request.Builder()
    .url("https://example.com/api/ezp/v2/content/node/2")
    .header("Authorization", "Bearer $credential")
    .header("Accept", "application/json")
    .build()
client.newCall(request).execute().use { response ->
    if (response.code == 401) { /* sign in again */ }
    val json = JSONObject(response.body!!.string())
    val name = json.getJSONObject("metadata").getString("objectName")
}
```

```swift
// Swift, URLSession
var request = URLRequest(url: URL(string: "https://example.com/api/ezp/v2/content/node/2")!)
request.setValue("Bearer \(credential)", forHTTPHeaderField: "Authorization")
request.setValue("application/json", forHTTPHeaderField: "Accept")
let (data, response) = try await URLSession.shared.data(for: request)
guard (response as? HTTPURLResponse)?.statusCode == 200 else { /* handle the status */ return }
let json = try JSONSerialization.jsonObject(with: data) as? [String: Any]
```

```javascript
// JavaScript (React Native, a web app on the same origin)
const answer = await fetch('https://example.com/api/ezp/v2/content/node/2', {
  headers: { Authorization: 'Bearer ' + credential, Accept: 'application/json' },
});
if (answer.status !== 200) { /* handle the status */ }
const node = await answer.json();
console.log(node.metadata.objectName);
```

Diese Skizzen wurden für diese Seite nicht kompiliert; sie zeigen die Anfrage, mehr nicht. Schreiben Sie die
Zugangsdaten nie fest in den Code: [Geheimnisse auf Geräten speichern](../anmeldung/geheimnisse-auf-geraeten-speichern.md).

## Wenn es nicht funktioniert

| Symptom | Prüfen Sie |
|---|---|
| `401` mit einem gerade erstellten Schlüssel | Headername und Schema; einen Proxy, der `Authorization` verwirft ([Der Authorization-Header und Proxys](../anmeldung/der-authorization-header-und-proxys.md)) |
| `401` `{"error":"invalid_token"}` | Einen falschen, widerrufenen oder abgelaufenen Schlüssel, oder den Schlüssel in der URL statt im Header |
| `403` `insufficient_scope` | Dem Schlüssel fehlt der Scope `read` |
| `403` `access_denied` | Der Inhaber des Schlüssels darf diesen Knoten nicht lesen |
| `404` | `ezprestapi` nicht aktiv, oder den Knoten gibt es nicht |

Alle Statuscodes stehen in [Fehler und Statuscodes](fehler-und-statuscodes.md).

## Verwandte Seiten

- [Einen Knoten lesen](einen-knoten-lesen.md)
- [Unterknoten auflisten und Paginierung](unterknoten-auflisten-und-paginierung.md)
- [REST-API v1 und v2: Beispiele mit curl](../../../anleitungen/rest-api-v1-und-v2.md#4-beispiele-mit-curl)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
