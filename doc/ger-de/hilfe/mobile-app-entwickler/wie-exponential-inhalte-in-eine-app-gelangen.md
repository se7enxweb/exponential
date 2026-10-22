---
title: Wie Exponential-Inhalte in eine App gelangen
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 10
summary: Das Inhaltsmodell für App-Entwickler erklärt - Klassen, Objekte, Knoten, Felder, Versionen und Sprachen - und wie jedes davon im JSON der REST-API erscheint, mit der echten Form einer Knotenantwort und einer Listenantwort.
keywords: Inhaltsmodell, Klasse, Objekt, Knoten, Platzierung, Feld, Attribut, Version, Sprache, JSON, Metadaten, childrenNodes, fullUrl, Remote-ID, Mobile App, Erklärung, content model, class, object, node, location, field, attribute, version, language, metadata, remote id, mobile app
type: help
---
# Wie Exponential-Inhalte in eine App gelangen

Diese Seite richtet sich an App-Entwickler, die noch nie mit Exponential gearbeitet haben und ein Denkmodell brauchen,
bevor sie die erste Anfrage schreiben. Sie erklärt die wenigen Begriffe, die die APIs zeigen, und zeigt, wo jeder
davon im JSON der REST-API erscheint. Die Formen unten stammen aus der Inhaltsmodell-Klasse von `ezprestapi`
(`ezpRestContentModel`) und aus den Beispielen in [Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md#3-einen-schlüssel-mit-curl-benutzen).

## Inhalt

1. [Fünf Begriffe](#fünf-begriffe)
2. [Eine Knotenantwort](#eine-knotenantwort)
3. [Eine Listenantwort](#eine-listenantwort)
4. [Felder und ihre Werte](#felder-und-ihre-werte)
5. [Was entscheidet, was Sie sehen](#was-entscheidet-was-sie-sehen)

## Fünf Begriffe

| Begriff | Was es ist | Im REST-JSON |
|---|---|---|
| Inhaltsklasse | Der Typ eines Elements mit seinen Feldern: `folder`, `article`, `image` oder eine Klasse, die die Website definiert | `classIdentifier` |
| Inhaltsobjekt | Ein Element: ein Artikel, ein Bild | `objectId`, `objectRemoteId`, `objectName` |
| Knoten (Platzierung) | Wo ein Objekt im Inhaltsbaum liegt. Ein Objekt kann mehrere Knoten haben; einer davon ist sein Hauptknoten | `nodeId`, `nodeRemoteId`, `fullUrl`, die Liste `locations` |
| Feld (Attribut) | Ein Wert eines Objekts, mit einem Datentyp wie `ezstring`, `ezxmltext`, `ezimage` | die Map `fields`, nach Bezeichner geordnet |
| Version und Sprache | Jede Veröffentlichung erzeugt eine neue Version; jede Version kann mehrere Übersetzungen enthalten | gelesen wird nur die veröffentlichte Version; `Translation=<locale>` wählt eine Sprache |

Knoten 2 ist in einer Standardinstallation die Wurzel des Inhaltsbaums, deshalb lesen die Beispiele in der
Dokumentation `/content/node/2`. Ihre App beginnt meist bei einem Ordnerknoten, den Ihnen ein Redakteur nennt, nicht bei
der Wurzel. Wie Sie solche IDs über Installationen hinweg stabil halten, steht in
[Knoten-IDs, Remote-IDs und Deep Links](inhaltsmodellierung/knoten-ids-remote-ids-und-deep-links.md).

## Eine Knotenantwort

`GET /api/ezp/v2/content/node/2` mit gültigen Zugangsdaten antwortet (aus der Anleitung, gekürzt):

```json
{"metadata":{"objectName":"Websites","classIdentifier":"folder","datePublished":1033917596,
 "dateModified":1791063800,"objectId":1,"nodeId":2,"fullUrl":"https:\/\/example.com\/websites"},"links":{...}}
```

Die `metadata` eines gelesenen Knotens enthalten `objectName`, `classIdentifier`, `datePublished`, `dateModified`
(Unix-Zeitstempel), `objectRemoteId`, `objectId` und bei einem Knoten zusätzlich `nodeId`, `nodeRemoteId` und
`fullUrl` (die Adresse der Seite auf der Website). `links` ordnet jedem Feldbezeichner die URL genau dieses Felds zu,
dazu `*` für alle Felder. Mehr kommt nur hinzu, wenn Sie es mit Antwortgruppen anfordern
([Antwortgruppen](rest-api/antwortgruppen.md)).

## Eine Listenantwort

`GET /api/ezp/v1/content/node/<node id>/list/offset/0/limit/20` antwortet mit:

| Schlüssel | Enthält |
|---|---|
| `childrenNodes` | einen Eintrag je Unterknoten: die Objekt-Metadaten, `nodeId`, `nodeRemoteId`, `fullUrl` und `link`, die REST-Adresse des Unterknotens |
| `metadata` | `childrenCount` (alle Unterknoten, nicht nur die dieser Seite) und `parentNodeId` |
| `requestedResponseGroups` | die Antwortgruppen, die der Server angewendet hat |

Mit `?ResponseGroups=Fields` trägt jeder Unterknoten zusätzlich seine `fields`. Die Einzelheiten stehen in
[Unterknoten auflisten und Paginierung](rest-api/unterknoten-auflisten-und-paginierung.md).

## Felder und ihre Werte

Jedes Feld ist ein Objekt mit `type` (der Datentyp als Zeichenkette), `identifier`, `value`, `id` und
`classattribute_id`. Der `value` ist eine Darstellung als Zeichenkette, mit drei Sonderfällen im Code:

| Datentyp | `value` |
|---|---|
| `ezxmltext` (Rich Text) | der gerenderte Text, ohne alle HTML-Tags |
| `ezimage` | der Pfad der Originalbilddatei, ohne den Alternativtext |
| ein Datentyp in `site.ini [ContentSettings] DatatypeBlackListForExternal[]` (standardmäßig `ezuser`) | `null` |
| jeder andere Datentyp | was `toString()` des Datentyps liefert |

Lesen Sie [Felder und Feldwerte](rest-api/felder-und-feldwerte.md), bevor Sie Werte parsen, und
[Rich Text in Apps](inhaltsmodellierung/rich-text-in-apps.md), wenn Ihre App Artikel anzeigt.

## Was entscheidet, was Sie sehen

Jeder REST-Aufruf läuft als ein Benutzer: der Benutzer des OAuth-Tokens oder API-Schlüssels oder der Standardbenutzer,
wenn die Authentifizierung ausgeschaltet ist. Der Inhalts-Controller fragt bei jedem Lese- und Schreibzugriff die
Richtlinien dieses Benutzers ab, genauso wie das Modul content der Website (`content/read`, `content/create`,
`content/edit`, `content/remove`), über `expRestContentPermission` des Kernels
([REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md#kurz-gesagt)). Ein Unterknoten, den der Benutzer nicht lesen
darf, fehlt in der Liste; ein Knoten, den der Benutzer nicht lesen darf, antwortet mit `403`. Planen Sie die
Benutzerkonten der App mit diesem Wissen: [Berechtigungen für App-Benutzer](veroeffentlichungsablauf/berechtigungen-fuer-app-benutzer.md).

## Verwandte Seiten

- [Welche API für Ihre App](welche-api-fuer-ihre-app.md)
- [Inhaltsklassen für Apps](inhaltsmodellierung/inhaltsklassen-fuer-apps.md)
- [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md)
- [Headless-Exponential](../ersteller/anwendungsentwickler/headless-exponential.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
