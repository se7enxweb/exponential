---
title: Unterknoten auflisten und Paginierung
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 150
summary: Die Elemente eines Ordners für einen Listenbildschirm über die REST-API auflisten - die Listenroute und ihre Standardwerte, seitenweises Laden mit offset und limit, endloses Scrollen, Sortieren nach Name, Datum oder Priorität, die Reihenfolge der Pfadsegmente, childrenCount in der Antwort und die Filterung nach Berechtigung.
keywords: Liste, Unterknoten, Ordner, Paginierung, seitenweise, offset, limit, sortieren, asc, desc, published, modified, priority, name, childrenCount, endloses Scrollen, REST, Mobile App, list, children, folder, pagination, paging, sort, infinite scroll, mobile app
type: help
---
# Unterknoten auflisten und Paginierung

Diese Seite richtet sich an App-Entwickler, die einen Listenbildschirm bauen: die Neuigkeiten eines Ordners, die
Produkte einer Kategorie. Sie erklärt die Route `list` der REST-API, wie Sie seitenweise durch sie blättern und wie
Sie sie sortieren.

## Inhalt

1. [Die Route](#die-route)
2. [Die Antwort](#die-antwort)
3. [Seitenweises Laden](#seitenweises-laden)
4. [Sortieren](#sortieren)
5. [Zählen](#zählen)
6. [Was die Liste auslässt](#was-die-liste-auslässt)

## Die Route

```text
GET /api/ezp/<version>/content/node/<id>/list(/offset/<n>)(/limit/<n>)(/sort/<key>(/asc|desc))
```

Jeder Teil in Klammern ist optional, **aber die Reihenfolge ist fest**: offset vor limit, limit vor sort. Die Route
ist ein regulärer Ausdruck (`ezpList` in `rest_provider.php`), und der Kommentar im Controller sagt es selbst: „you
can't provide sorting params before limit params“ (Sortierparameter können nicht vor Limitparametern stehen).
Standardwerte des Controllers: offset `0`, limit `10`, Sortierrichtung `asc` und kein Sortierschlüssel (die
Reihenfolge folgt dann der Voreinstellung der Inhaltsabfrage).

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v1/content/node/<node id>/list/offset/0/limit/20?ResponseGroups=Fields"
```

Genau dieses Muster ist der Aufruf einer bestehenden mobilen App, aufgezeichnet im Vergleich der Antworten vor und
nach 6.0.15 im Leitfaden ([Aktivierungskombinationen](../../../anleitungen/rest-api-v1-und-v2.md#7-aktivierungskombinationen-vorher-und-seitdem)).

## Die Antwort

```json
{
  "childrenNodes": [
    {"objectName": "...", "classIdentifier": "article", "datePublished": 0, "dateModified": 0,
     "objectRemoteId": "...", "objectId": 0, "nodeId": 0, "nodeRemoteId": "...", "fullUrl": "https://example.com/...",
     "link": "https://example.com/api/ezp/v1/content/node/<id>", "fields": {"...": "..."}},
    "..."
  ],
  "metadata": {"childrenCount": 0, "parentNodeId": "<id>"},
  "requestedResponseGroups": ["Fields", "Metadata"]
}
```

Die Werte sind Platzhalter; die Schlüssel sind die von `getChildrenList()` und `doList()`. `fields` erscheint nur mit
`ResponseGroups=Fields`. `link` ist die REST-Adresse des Unterknotens in derselben Version, mit den Parametern
`Translation` und `OutputFormat` der Anfrage.

## Seitenweises Laden

`metadata.childrenCount` ist die Anzahl aller Unterknoten, die die Abfrage findet, nicht die dieser Seite. Gehen Sie
in einer Schleife so vor:

```text
offset = 0, limit = 20
repeat:
    GET .../list/offset/{offset}/limit/{limit}
    show childrenNodes
    offset = offset + limit
until offset >= metadata.childrenCount
```

Für endloses Scrollen fordern Sie die nächste Seite an, wenn der Benutzer sich dem Ende der geladenen Elemente nähert,
und hören Sie auf, wenn `offset` `childrenCount` erreicht. Offsets verschieben sich, wenn Redakteure zwischen zwei
Anfragen Elemente veröffentlichen oder entfernen; entfernen Sie Duplikate in der App anhand von `nodeId`. Die
ausgelieferte Konfiguration setzt für diese Route kein Höchstlimit; halten Sie Seiten klein (20 bis 50), damit der
erste Bildschirm in Mobilfunknetzen schnell erscheint.

## Sortieren

```text
.../list/offset/0/limit/20/sort/published/desc
.../list/limit/50/sort/name
```

Der Sortierschlüssel wird dem Fetch des Inhaltsbaums als Wert von `SortBy` übergeben. Die Beispiele im Controller
verwenden `name` und `published`; die Sortiernamen für Knoten, die derselbe Fetch an anderer Stelle im Code verwendet
(die Liste von expservices in `expcontentbase.php`), sind `path`, `published`, `modified`, `section`, `depth`,
`class_identifier`, `class_name`, `priority`, `name`, `modified_subnode`, `node_id` und `contentobject_id`. Um der
Reihenfolge zu folgen, die Redakteure in der Administrationsoberfläche gewählt haben, sortieren Sie nach `priority`
oder fragen Sie einen Remote-Dienst nach den Sortiereinstellungen des Knotens (`expnode::sortInfo`).

## Zählen

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/<node id>/childrenCount
```

Antwortet mit `metadata` samt `childrenCount` und `parentNodeId`, für ein Badge oder um eine Liste vor dem Laden zu
dimensionieren.

## Was die Liste auslässt

- **Nur direkte Unterknoten** (Tiefe 1). Für einen ganzen Teilbaum verwenden Sie `expnode::subtree` der
  Remote-Dienste.
- **Kein Klassenfilter.** Filtern Sie in der App, oder verwenden Sie `expnode::children` mit einem Filter oder
  `expnode::byClass` ([expservices aus einer App aufrufen](../json-endpunkte/expservices-aus-einer-app-aufrufen.md)).
- **Unterknoten, die der Benutzer nicht lesen darf.** Eine Liste prüft `content/read` ihres Elternknotens, „die
  Unterknoten werden vom Fetch gefiltert“ ([Berechtigungen der REST-Schnittstelle](../../../anleitungen/api-schluessel.md#32-berechtigungen-der-rest-schnittstelle)).

## Verwandte Seiten

- [Antwortgruppen](antwortgruppen.md)
- [Caching in der App](../offline-und-leistung/caching-in-der-app.md)
- [REST-API v1 und v2: die Routen](../../../anleitungen/rest-api-v1-und-v2.md#2-die-routen)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
