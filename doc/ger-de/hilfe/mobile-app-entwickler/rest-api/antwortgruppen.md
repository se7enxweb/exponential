---
title: Antwortgruppen
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 140
summary: Mit dem Parameter ResponseGroups genau die Teile einer Antwort von der REST-API anfordern, die ein Bildschirm braucht - die Gruppen, die jede Route kennt (Metadata, Locations, Fields, FieldValues), die Voreinstellungen, die immer hinzugefügt werden, beide Schreibweisen des Parameters und die Rückmeldung in requestedResponseGroups.
keywords: ResponseGroups, Antwortgruppen, Metadata, Locations, Fields, FieldValues, requestedResponseGroups, Voreinstellung, Antwortgröße, REST, Mobile App, response groups, default, payload size, mobile app
type: help
---
# Antwortgruppen

Diese Seite richtet sich an App-Entwickler, die über eine langsame Mobilverbindung kleine Antworten und dort, wo ein
Bildschirm sie braucht, vollständige Antworten möchten. Antwortgruppen sind der Schalter der REST-API dafür.

## Inhalt

1. [Die Gruppen pro Route](#die-gruppen-pro-route)
2. [Den Parameter schreiben](#den-parameter-schreiben)
3. [Voreinstellungen werden immer hinzugefügt](#voreinstellungen-werden-immer-hinzugefügt)
4. [Gruppen für Bildschirme wählen](#gruppen-für-bildschirme-wählen)

## Die Gruppen pro Route

Aus den Konstanten von `ezp7xRestContentController`:

| Route | Gruppe | Fügt hinzu | Voreinstellung |
|---|---|---|---|
| `GET /content/node/<id>`, `GET /content/object/<id>` | `Metadata` | das Objekt `metadata` | ja |
| dieselbe | `Locations` | die Liste `locations` | nein |
| dieselbe | `Fields` | die Tabelle `fields` | nein |
| `GET .../fields`, `GET .../field/<identifier>` | `FieldValues` | die Tabelle `fields` (oder das eine Feld) | ja |
| dieselbe | `Metadata` | das Objekt `metadata` des Inhalts | nein |
| `GET /content/node/<id>/list...` | `Metadata` | die Listen-`metadata`: `childrenCount`, `parentNodeId` | ja |
| dieselbe | `Fields` | eine Tabelle `fields` in jedem Unterknoten | nein |
| `GET /content/node/<id>/childrenCount` | `Metadata` | `metadata` mit `childrenCount` | ja |

Bei den Namen kommt es auf Groß- und Kleinschreibung an: `Fields`, nicht `fields`.

## Den Parameter schreiben

Der Request-Parser akzeptiert beide Formen:

```text
?ResponseGroups=Metadata,Locations,Fields
?ResponseGroups[]=Metadata&ResponseGroups[]=Locations&ResponseGroups[]=Fields
```

Die zweite Form erzeugen manche HTTP-Bibliotheken aus einem Array. Vor 6.0.15 endete sie in einer Fehlerseite; der
Parser akzeptiert sie jetzt (Kommentar in `http_parser.php`).

## Voreinstellungen werden immer hinzugefügt

Die voreingestellten Gruppen einer Route sind enthalten, ob Sie sie nennen oder nicht: Wer beim Lesen eines Knotens
`?ResponseGroups=Fields` anfordert, erhält trotzdem `metadata`. Die Antwort führt die angewendeten Gruppen in
`requestedResponseGroups` auf, was bei der Fehlersuche nach einem fehlenden Schlüssel hilft.

## Gruppen für Bildschirme wählen

| Bildschirm | Aufruf | Warum |
|---|---|---|
| Eine Liste von Titeln | `.../list/offset/0/limit/20` | Die Metadaten enthalten `objectName`, `classIdentifier`, `nodeId`; keine Felder nötig |
| Eine Liste von Karten mit Anreißer und Bild | `.../list/offset/0/limit/20?ResponseGroups=Fields` | Jeder Unterknoten bringt seine Felder mit |
| Ein Detailbildschirm | `/content/node/<id>?ResponseGroups=Fields` | Metadaten und alle Felder in einem Aufruf |
| Ein Badge mit einer Anzahl | `/content/node/<id>/childrenCount` | Eine Zahl, keine Elemente |
| Einen einzelnen Wert aktualisieren | `/content/node/<id>/field/<identifier>` | Nur ein Feld |

`Fields` liefert bei einer Liste jedes Feld jedes Unterknotens. Messen Sie bei Klassen mit vielen oder großen Feldern
die Größe der Antwort über eine Mobilverbindung, und erwägen Sie, Details erst zu lesen, wenn der Benutzer ein Element
öffnet.

## Verwandte Seiten

- [Einen Knoten lesen](einen-knoten-lesen.md)
- [Unterknoten auflisten und Paginierung](unterknoten-auflisten-und-paginierung.md)
- [Felder und Feldwerte](felder-und-feldwerte.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
