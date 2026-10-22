---
title: Die REST-Inhalts-API für Apps
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 100
summary: Die Einstiegsseite der REST-API-Seiten für App-Entwickler - was die Routen von ezprestapi unter v1 und v2 einer App bieten, die Basis-URL, die Seiten in diesem Ordner und die Reihenfolge, in der Sie sie lesen.
keywords: REST, API, ezprestapi, v1, v2, Routen, JSON, Mobile App, Inhaltsknoten, Liste, Felder, anlegen, löschen, Fehler, routes, mobile app, content node, list, fields, create, delete, errors
type: help
---
# Die REST-Inhalts-API für Apps

Dieser Ordner richtet sich an App-Entwickler, die Inhalte über die REST-Inhalts-API von Exponential lesen oder
schreiben. Die API stammt aus der Erweiterung `ezprestapi`; ein Provider bedient die Präfixe `ezp` und `ezpl`,
Lesezugriffe antworten unter v1 und v2, Schreibzugriffe nur unter v2
([REST-API v1 und v2: eine Erweiterung](../../../anleitungen/rest-api-v1-und-v2.md#kurz-gesagt)).

## Inhalt

1. [Die Basis-URL](#die-basis-url)
2. [Was eine App damit tun kann](#was-eine-app-damit-tun-kann)
3. [Seiten in diesem Ordner](#seiten-in-diesem-ordner)
4. [Bevor Sie beginnen](#bevor-sie-beginnen)

## Die Basis-URL

```text
https://example.com/api/ezp/v2/<route>
```

`/api` ist `rest.ini [System] ApiPrefix`; `ezp` ist das Provider-Präfix (`ezpl` ist ein Alias mit denselben Routen);
`v1` oder `v2` ist die Version. Jedes Beispiel in den Leitfäden ruft die API wie oben direkt unterhalb des Hosts auf.

## Was eine App damit tun kann

| Aufgabe | Route | Versionen |
|---|---|---|
| Einen Knoten oder ein Objekt mit Metadaten, Platzierungen und Feldern lesen | `GET /content/node/<id>`, `GET /content/object/<id>` | v1, v2 |
| Die Unterknoten eines Knotens seitenweise und sortiert auflisten | `GET /content/node/<id>/list/...` | v1, v2 |
| Die Unterknoten zählen | `GET /content/node/<id>/childrenCount` | v1, v2 |
| Alle Felder oder ein Feld lesen | `GET .../fields`, `GET .../field/<identifier>` | v1, v2 |
| Anlegen und veröffentlichen | `POST /content/node/create` | v2 |
| Entfernen | `DELETE /content/node/delete/<id>` | v2 |

## Seiten in diesem Ordner

| Seite | Beantwortet |
|---|---|
| [Ihre erste Anfrage](ihre-erste-anfrage.md) | Wie Sie den ersten authentifizierten Aufruf ausführen und prüfen |
| [Routenreferenz](routenreferenz.md) | Jede Route mit Methode, Versionen und Controller-Aktion |
| [Einen Knoten lesen](einen-knoten-lesen.md) | Ein Knoten oder Objekt, seine Metadaten und Links |
| [Antwortgruppen](antwortgruppen.md) | Wie Sie Platzierungen und Felder anfordern |
| [Unterknoten auflisten und Paginierung](unterknoten-auflisten-und-paginierung.md) | Listen, Seiten, Sortierung und Anzahlen |
| [Felder und Feldwerte](felder-und-feldwerte.md) | Das Feldobjekt und wie Werte je Datentyp geschrieben werden |
| [Gerenderte XHTML-Ausgabe](gerenderte-xhtml-ausgabe.md) | `OutputFormat=xhtml` und die Templates, die es braucht |
| [Inhalte anlegen und entfernen](inhalte-anlegen-und-entfernen.md) | Die beiden Schreibzugriffe unter v2 und ihre Antworten |
| [Fehler und Statuscodes](fehler-und-statuscodes.md) | Jeder Status, den Ihr Client behandeln muss |
| [API-Versionen v1 und v2](api-versionen-v1-und-v2.md) | Welche Version Sie aufrufen und der veraltete Provider |

## Bevor Sie beginnen

- Sie brauchen Zugangsdaten: Die API verlangt standardmäßig eine Authentifizierung ([Authentifizierung](../anmeldung/README.md)).
- Sie brauchen eine Installation, in der `ezprestapi` aktiv ist: Prüfen Sie das zuerst
  ([Erst prüfen, dann bauen](../testen/gegen-eine-demo-testen.md#erst-prüfen-dann-bauen)).
- Die Routen sind für jede Client-Sprache gleich; die Beispiele verwenden `curl`, damit Sie sie in eine Shell
  einfügen können.

## Verwandte Seiten

- [REST-API v1 und v2 für Anwendungen](../../ersteller/anwendungsentwickler/rest-api.md)
- [REST-API v1 und v2: eine Erweiterung](../../../anleitungen/rest-api-v1-und-v2.md)
- [ezprestapi](../../../funktionen/6.0/erweiterungen/ezprestapi.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
