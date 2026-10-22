---
title: Einen Knoten lesen
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 130
summary: Ein Element für einen Detailbildschirm über die REST-API lesen - über die Knoten-ID oder die Objekt-ID, die Metadaten-Schlüssel, die Link-Tabelle, Platzierungen und Felder auf Anfrage, und wie Sie Links folgen, ohne URLs von Hand zu bauen.
keywords: Knoten lesen, Objekt lesen, Detailbildschirm, Metadaten, Links, Platzierungen, objectName, classIdentifier, datePublished, dateModified, nodeId, fullUrl, locations, isMain, REST, Mobile App, read node, read object, detail screen, metadata, mobile app
type: help
---
# Einen Knoten lesen

Diese Seite richtet sich an App-Entwickler, die einen Detailbildschirm bauen: einen Artikel, ein Produkt, eine
Veranstaltung. Sie zeigt die Leserouten für ein einzelnes Element, was die Antwort enthält und wie Sie mehr anfordern.

## Inhalt

1. [Über den Knoten oder über das Objekt](#über-den-knoten-oder-über-das-objekt)
2. [Die Metadaten](#die-metadaten)
3. [Links zu den Feldern](#links-zu-den-feldern)
4. [Platzierungen](#platzierungen)
5. [Ein Detailbildschirm mit einem Aufruf](#ein-detailbildschirm-mit-einem-aufruf)

## Über den Knoten oder über das Objekt

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/<node id>
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/object/<object id>
```

Verwenden Sie die Knotenroute, wenn Sie von einer Liste kommen (Listen liefern `nodeId`) oder von einem Link der
Website. Verwenden Sie die Objektroute, wenn Sie eine Objekt-ID gespeichert haben, zum Beispiel aus einer Relation.
Beide führen dieselbe Controller-Aktion `viewContent` aus, und beide prüfen zuerst `content/read` für den aktuellen
Benutzer: `403 access_denied`, wenn der Benutzer das Element nicht lesen darf, `404`, wenn es nicht existiert.

## Die Metadaten

Die Antwortgruppe `Metadata` ist immer enthalten. Die Schlüssel aus `ezpRestContentModel`:

| Schlüssel | Typ | Bedeutung |
|---|---|---|
| `objectName` | String | Der Name des Objekts in der gelesenen Sprache |
| `classIdentifier` | String | Seine Inhaltsklasse, zum Beispiel `article` |
| `datePublished` | Integer | Erste Veröffentlichung, Unix-Zeitstempel |
| `dateModified` | Integer | Letzte Änderung, Unix-Zeitstempel |
| `objectRemoteId` | String | Die Remote-ID des Objekts |
| `objectId` | Integer | Die Objekt-ID |
| `nodeId` | Integer | Nur beim Lesen von Knoten: die Knoten-ID |
| `nodeRemoteId` | String | Nur beim Lesen von Knoten: die Remote-ID des Knotens |
| `fullUrl` | String | Nur beim Lesen von Knoten: die vollständige Adresse der Seite des Knotens auf der Website |

Verwenden Sie `dateModified`, um zu entscheiden, ob eine zwischengespeicherte Kopie in der App noch aktuell ist
([Caching in der App](../offline-und-leistung/caching-in-der-app.md)), und `fullUrl` für eine Schaltfläche
„Teilen“ oder „Im Browser öffnen“.

## Links zu den Feldern

Jeder Lesezugriff enthält eine Tabelle `links`: einen Eintrag pro Feldbezeichner mit der URL dieses Feldes und `*` mit
der URL aller Felder. Die URLs wiederholen die Parameter `Translation` und `OutputFormat` der Anfrage. Folgen Sie
ihnen, statt Pfade in der App zusammenzubauen; so funktioniert die App weiter, falls sich ein Pfad einmal ändert.

## Platzierungen

Ein Objekt kann an mehreren Stellen des Baums liegen. Fragen Sie die Platzierungen mit `?ResponseGroups=Locations` ab:

```json
"locations": [
  {"nodeId": 61, "nodeRemoteId": "...", "fullUrl": "https://example.com/news/launch", "isMain": true},
  {"nodeId": 142, "nodeRemoteId": "...", "fullUrl": "https://example.com/products/launch", "isMain": false}
]
```

(Die IDs und Adressen sind für das Beispiel erfunden; die Schlüssel sind die von `getLocationsByContent()`.)
Verwenden Sie die Hauptplatzierung für geteilte Links, damit alle Benutzer dieselbe Adresse teilen.

## Ein Detailbildschirm mit einem Aufruf

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v2/content/node/<node id>?ResponseGroups=Metadata,Fields"
```

Das liefert die Metadaten und alle Felder in einer Antwort, was meist alles ist, was ein Detailbildschirm braucht.
Fügen Sie `Locations` nur hinzu, wenn der Bildschirm die Platzierungen verwendet. Die Feldwerte sind in
[Felder und Feldwerte](felder-und-feldwerte.md) beschrieben; Bilder und Rich Text erfordern besondere Sorgfalt
([Bilder in REST-Antworten](../medien-und-bilder/bilder-in-rest-antworten.md),
[Rich Text in Apps](../inhaltsmodellierung/rich-text-in-apps.md)).

## Verwandte Seiten

- [Antwortgruppen](antwortgruppen.md)
- [Unterknoten auflisten und Paginierung](unterknoten-auflisten-und-paginierung.md)
- [Wie Exponential-Inhalte in eine App gelangen](../wie-exponential-inhalte-in-eine-app-gelangen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
