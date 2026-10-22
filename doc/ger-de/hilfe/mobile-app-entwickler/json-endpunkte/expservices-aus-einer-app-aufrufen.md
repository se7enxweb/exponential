---
title: expservices aus einer App aufrufen
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 310
summary: Die Remote-Dienste von expservices aus App-Code aufrufen - die URL mit durch :: verbundenen Argumenten, Prozentkodierung, die Hülle von ezjscore und der Umschlag darin, Paginierung mit meta.has_more, Dienste aus dem Katalog erkunden, Fehlercodes innerhalb eines HTTP 200 und eine Client-Struktur zum Übernehmen.
keywords: expservices, ezjscore/call, Umschlag, ok, data, meta, error, has_more, total, Paginierung, MaxLimit, Katalog, Erkundung, expservices::catalog, Argumente, Prozentkodierung, Kotlin, Swift, Client, Mobile App, envelope, paging, catalog, discovery, arguments, percent encoding, client, mobile app
type: help
---
# expservices aus einer App aufrufen

Diese Seite richtet sich an App-Entwickler, die den Client für die Remote-Dienste schreiben. Sie fasst den Vertrag
zusammen, dem jeder Dienst folgt, aus dem
[Kapitel über Clients der expservices-Referenz](../../../bc/6.0/backend-dienste-ueber-ezjscore.md#clients), und zeigt, wie
er sich in die Netzwerkschicht einer App einfügt.

## Inhalt

1. [Die Anfrage](#die-anfrage)
2. [Der Umschlag](#der-umschlag)
3. [Paginierung](#paginierung)
4. [Dienste erkunden](#dienste-erkunden)
5. [Fehler](#fehler)
6. [Eine Client-Struktur](#eine-client-struktur)

## Die Anfrage

```text
GET  https://example.com/ezjscore/call/<service>[::<arg>...]?ContentType=json     (reads)
POST https://example.com/ezjscore/call/<service>?ContentType=json                  (writes, arguments as form fields)
Accept: application/json
Authorization: Bearer expt_...
X-Exp-Token: expt_...
```

- Die Argumente eines Lesezugriffs sind **positionsgebunden**, prozentkodiert und mit `::` verbunden, in der
  Reihenfolge der `args` des Diensts. `expnode::children` nimmt Knoten-ID, Sortierung, Reihenfolge, Limit, Offset (und
  einen optionalen JSON-Filter): `expnode::children::2::name::asc::5::0`.
- Ein Lesezugriff ohne Zugangsdaten läuft als anonymer Benutzer, was nur bei Diensten mit dem Zugriff `public`
  funktioniert.
- Schreibzugriffe beschreibt [Über expservices schreiben](ueber-expservices-schreiben.md).

## Der Umschlag

ezjscore verpackt jede Antwort:

```json
{"error_text": "", "content": {"ok": true, "data": {}, "meta": {}}}
```

- `error_text` ist nur gesetzt, wenn der Aufruf nicht weitergeleitet werden konnte (unbekannter Dienst, Erweiterung
  nicht aktiv). Dann ist `content` leer.
- `content` ist der Umschlag des Diensts: `{"ok":true,"data":...,"meta":{...}}` oder
  `{"ok":false,"error":{"code":403,"message":"..."}}`.

Die mitgelieferten Shell- und Python-Clients packen beide Schichten aus; Ihr Client sollte das auch tun und dem Rest der
App nur `data`, `meta` oder einen Fehler übergeben.

## Paginierung

Dienste, die auflisten, nehmen `limit` und `offset` als ihre letzten beiden URL-Argumente und antworten mit
`meta.total`, `meta.offset`, `meta.limit`, `meta.count` und `meta.has_more`. Wiederholen Sie, solange `has_more` wahr
ist. Standard- und Höchstlimit sind `expservices.ini [Paging] DefaultLimit` und `MaxLimit` (25 und 200).

```bash
curl -s -H "X-Exp-Token: $EXPSERVICES_TOKEN" \
     'https://example.com/ezjscore/call/expnode::children::<node id>::published::desc::20::0?ContentType=json'
```

## Dienste erkunden

`expservices::catalog` listet jeden Dienst mit Domäne, Methode, Zusammenfassung, `access` (`public`, `user` oder eine
Richtlinie `[module, function]`), `write`, `args` und `returns`; `expservices::service::<domain>::<method>` liefert einen
einzelnen Deskriptor. Laut Referenz lässt sich aus dem Katalog ein typisierter Client erzeugen. Ein praktischer Nutzen
in der App-Entwicklung: Rufen Sie beim Build oder in einem Test die Deskriptoren der wenigen Dienste ab, die die App
nutzt, und lassen Sie den Test fehlschlagen, wenn sich ihre Argumente ändern.

```bash
curl -s 'https://example.com/ezjscore/call/expservices::service::expimage::url?ContentType=json'
```

## Fehler

HTTP 200 bedeutet nicht Erfolg; sehen Sie sich `content.ok` an:

| `content.error.code` | Bedeutung | Verhalten der App |
|---|---|---|
| 400 | falsche oder fehlende Argumente | ein Fehler in der App: protokollieren |
| 401 | Anmeldung nötig, oder das Token ist ungültig geworden | Token löschen, erneut anmelden |
| 403 | keine Richtlinie, kein POST oder ein falsches Formular-Token | „nicht erlaubt“ anzeigen; nicht in einer Schleife wiederholen |
| 404 | kein solcher Dienst, Knoten, kein solches Objekt | das Element verwerfen |
| 409 | Konflikt | die Meldung anzeigen |
| 422 | die Daten sind ungültig | die Meldung anzeigen |
| 500 | der Dienst ist gescheitert | später erneut versuchen |

Ein HTTP-Status ungleich 200 oder ein Inhalt, der kein JSON ist, ist ein Transportfehler (Proxy, TLS, Server
ausgefallen): mit Backoff erneut versuchen.

## Eine Client-Struktur

```text
ExpServicesClient
  call(service, args[]) -> Result<data, meta>      GET, args percent-encoded, joined with ::
  post(service, fields{}) -> Result<data, meta>    POST form fields
  - adds Accept, Authorization: Bearer, X-Exp-Token
  - checks HTTP status and JSON (transport fault)
  - checks error_text (routing fault)
  - unwraps content.ok / content.error.code into the app's error type
```

Die Referenz enthält ausführlichere Beispiele für [Kotlin mit OkHttp](../../../bc/6.0/backend-dienste-ueber-ezjscore.md#kotlin--android-okhttp)
und [Swift mit URLSession](../../../bc/6.0/backend-dienste-ueber-ezjscore.md#swift--ios-urlsession); sie gibt an, dass diese
von Hand geprüft wurden, sich aber auf ihrer Build-Maschine nicht kompilieren ließen, also kompilieren Sie sie zuerst
in Ihrem Projekt.

## Verwandte Seiten

- [expservices-Tokens in Apps](../anmeldung/expservices-tokens-in-apps.md)
- [Remote-Dienste und Apps](../../../anleitungen/entfernte-dienste-und-apps.md)
- [Welche API für Ihre App](../welche-api-fuer-ihre-app.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
