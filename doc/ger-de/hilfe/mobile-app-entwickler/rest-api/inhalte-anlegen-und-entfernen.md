---
title: Inhalte anlegen und entfernen
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 180
summary: Inhalte aus einer App über die REST-API unter v2 schreiben - anlegen und veröffentlichen mit parentNodeID, classIdentifier, languageLocale und den Feldwerten als Formularfelder, die Antwort 201, Entfernen mit DELETE, die Berechtigungsprüfungen, Scopes für API-Schlüssel, die Überschreibung mit _method und was das Aktualisieren heute tut.
keywords: anlegen, veröffentlichen, POST, content/node/create, parentNodeID, classIdentifier, languageLocale, Formularfelder, 201, löschen, entfernen, DELETE, _method, aktualisieren, 501, nxc_powercontent, content/create, content/remove, REST, Mobile App, create, publish, form fields, delete, remove, update, mobile app
type: help
---
# Inhalte anlegen und entfernen

Diese Seite richtet sich an App-Entwickler, deren App Inhalte hinzufügt: einen Bericht aus dem Außeneinsatz, eine
Einsendung eines Benutzers, einen von einem Gerät importierten Eintrag. Sie erklärt die beiden Schreibrouten der
REST-API, die nur unter v2 antworten.

## Inhalt

1. [Anlegen und veröffentlichen](#anlegen-und-veröffentlichen)
2. [Wie Sie den Body senden](#wie-sie-den-body-senden)
3. [Entfernen](#entfernen)
4. [Aktualisieren](#aktualisieren)
5. [Berechtigungen und Scopes](#berechtigungen-und-scopes)
6. [Ratschläge für das Design schreibender Apps](#ratschläge-für-das-design-schreibender-apps)

## Anlegen und veröffentlichen

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/create \
     -d parentNodeID=<parent node id> -d classIdentifier=folder -d languageLocale=eng-US -d name='From a script'
```

```json
{"message":"Created","objectId":<object id>,"nodeId":<node id>}
```

| Feld | Pflicht | Bedeutung |
|---|---|---|
| `parentNodeID` | ja | der Knoten, unterhalb dessen das Element angelegt wird |
| `classIdentifier` | ja | die Inhaltsklasse, zum Beispiel `article` |
| `languageLocale` | ja | die Sprache, in der angelegt wird, zum Beispiel `eng-US`; ohne sie lautet die Antwort `400` |
| jeder Feldbezeichner der Klasse | soweit die Klasse es verlangt | der Wert dieses Feldes, zum Beispiel `name` bei einem Ordner oder `title` bei einem Artikel |

Die Antwort ist `201` mit `objectId` und `nodeId` (dem neuen Hauptknoten). Das Element wird sofort veröffentlicht:
Der Controller legt es über `nxc_powercontent` sichtbar an, und „ein Anlegen veröffentlicht, wenn `content/create`
es erlaubt, und der Veröffentlichungs-Workflow läuft wie beim Content-Modul“
([Berechtigungen der REST-Schnittstelle](../../../anleitungen/api-schluessel.md#32-berechtigungen-der-rest-schnittstelle)).

Feldwerte sind Zeichenketten. Ein Rich-Text-Feld wird vom Eingabeparser des Online-Editors verarbeitet und nimmt
daher einfaches HTML wie `<p>Text</p>` an. Senden Sie aus einer App keine Bild- oder Dateifelder: Keine
dokumentierte Route nimmt eine Datei von einem Gerät entgegen ([Dateien und Uploads](../medien-und-bilder/dateien-und-uploads.md)).

## Wie Sie den Body senden

Der Controller liest die Felder aus `$_POST` von PHP; senden Sie sie daher **formularkodiert**
(`application/x-www-form-urlencoded`, wie es `curl -d` tut, oder `multipart/form-data`). Einen JSON-Body liest diese
Route nicht. Im App-Code:

```kotlin
// Kotlin, OkHttp
val body = FormBody.Builder()
    .add("parentNodeID", parentId.toString())
    .add("classIdentifier", "article")
    .add("languageLocale", "eng-US")
    .add("title", title)
    .build()
```

```swift
// Swift: percent-encode each value before joining
request.httpMethod = "POST"
request.setValue("application/x-www-form-urlencoded", forHTTPHeaderField: "Content-Type")
request.httpBody = "parentNodeID=\(parentId)&classIdentifier=article&languageLocale=eng-US&title=\(encodedTitle)"
    .data(using: .utf8)
```

## Entfernen

```bash
curl -s -X DELETE -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/delete/<node id>
```

```json
{"message":"Removed","nodeId":<node id>,"objectId":<object id>}
```

Das entfernt das **Objekt** des Knotens mit allen seinen Platzierungen und allem, was darunter liegt. Ein `POST` an
denselben Pfad funktioniert ebenfalls, ebenso ein `POST` mit dem Formularfeld `_method=DELETE`, das der
Request-Parser für Clients, die kein `DELETE` senden können, in ein `DELETE` umwandelt. Lassen Sie den Benutzer in der
App bestätigen; es gibt keine Route zum Rückgängigmachen.

## Aktualisieren

`POST /api/ezp/v2/content/node/<id>` prüft `content/edit` für den Knoten in der gesendeten `languageLocale` und
antwortet dann mit `501` und `not_implemented`: „Updating content over REST is not implemented yet“ (das
Aktualisieren von Inhalten über REST ist noch nicht implementiert). Um ein bestehendes Element aus einer App zu
ändern, verwenden Sie `expnode::update` der Remote-Dienste
([Über expservices schreiben](../json-endpunkte/ueber-expservices-schreiben.md)).

## Berechtigungen und Scopes

| Aufruf | Für den aktuellen Benutzer geprüfte Richtlinie | Scope eines persönlichen API-Schlüssels |
|---|---|---|
| Anlegen | `content/create` der Klasse unterhalb des Elternknotens, in der Sprache (Einschränkungen Class, ParentClass, Section, Node, Subtree und Language) | `publish` |
| Entfernen | `content/remove` jeder Platzierung und von allem darunter | `remove` |
| Aktualisieren | `content/edit` des Knotens (und `user/selfedit` für den eigenen Benutzer) | `edit` |

Eine Ablehnung antwortet mit `403` und `access_denied` sowie einer Meldung wie „You may not create 'article' below
node 60 in eng-US (content/create).“ und ändert nichts ([Leitfaden zu API-Schlüsseln, 3.2](../../../anleitungen/api-schluessel.md#32-berechtigungen-der-rest-schnittstelle)).

## Ratschläge für das Design schreibender Apps

- **Legen Sie Inhalte unterhalb eines Ordners pro App an** und geben Sie den Benutzern der App `content/create` nur
  dort und nur für die Klassen, die sie brauchen ([Berechtigungen für App-Benutzer](../veroeffentlichungsablauf/berechtigungen-fuer-app-benutzer.md)).
- **Machen Sie das Anlegen in der App idempotent.** Ein Anlegen, das in eine Zeitüberschreitung lief, kann erfolgreich
  gewesen sein; prüfen Sie vor einem erneuten Versuch, ob das Element existiert, zum Beispiel über einen eindeutigen
  Titel oder indem Sie den Ordner nach `published` sortiert auflisten.
- **Stellen Sie offline vorgenommene Schreibzugriffe in eine Warteschlange** und senden Sie sie nacheinander, sobald
  das Gerät wieder online ist ([Synchronisieren und Offline-Lesen](../offline-und-leistung/synchronisation-und-offline-lesen.md)).
- **Zeigen Sie dem Benutzer die Meldung des Servers** bei einem `400` oder `403`; sie nennt, was fehlt oder abgelehnt
  wurde.

## Verwandte Seiten

- [Fehler und Statuscodes](fehler-und-statuscodes.md)
- [REST-API v1 und v2: Beispiele mit curl](../../../anleitungen/rest-api-v1-und-v2.md#4-beispiele-mit-curl)
- [Inhalte im Code anlegen](../../ersteller/anwendungsentwickler/inhalte-im-code-anlegen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
