---
title: Servicestellen in der Nähe finden
audience: government, website-creator, developer
category: public-sector
order: 50
summary: Wie ein Site-Ersteller mit dem ezgmlLocationFilter von ezgmaplocation die Servicestellen auflistet, die einem Ort am nächsten sind - der Fetch, sein Entfernungsparameter, die Genauigkeit (Begrenzungsrechteck gegenüber echter Entfernung) und wie man es Besuchern ohne Karte anbietet.
keywords: in der Nähe, Entfernung, nächstes Büro, nach Entfernung sortieren, Breitengrad, Längengrad, Servicestellensuche, nearby, distance, closest office, ezgmlLocationFilter, extended_attribute_filter, fetch content tree, sort by distance, latitude, longitude, service point finder
type: help
---
# Servicestellen in der Nähe finden

Diese Seite richtet sich an Site-Ersteller und Entwickler. Die Erweiterung `ezgmaplocation` enthält einen erweiterten
Attributfilter, `ezgmlLocationFilter`, der Inhalte nach Entfernung von einem Punkt abruft und nach Nähe sortiert
([ezgmaplocation](../../../../../eng-us/features/6.0/extensions/ezgmaplocation.md)). Er braucht weder eine Karte noch einen
Google-Dienst: Er ist eine Datenbankabfrage über gespeicherte Koordinaten.

## Inhalt

1. [Der Fetch](#der-fetch)
2. [Genauigkeit](#genauigkeit)
3. [Woher der Punkt des Besuchers kommt](#woher-der-punkt-des-besuchers-kommt)
4. [Das Ergebnis darstellen](#das-ergebnis-darstellen)

## Der Fetch

Das Beispiel aus dem README, angepasst an Servicestellen unter einem Ordner:

```text
{def $nearby = fetch( 'content', 'tree', hash(
        'parent_node_id', <service points folder node id>,
        'limit', 5,
        'sort_by', array( 'distance', true() ),
        'class_filter_type', 'include',
        'class_filter_array', array( 'service_point' ),
        'extended_attribute_filter', hash( 'id', 'ezgmlLocationFilter',
                                           'params', hash( 'latitude', <latitude>,
                                                           'longitude', <longitude>,
                                                           'distance', 0.5 ) ) ) )}
```

## Genauigkeit

Der Entfernungsfilter verwendet für die SQL-Geschwindigkeit ein „Begrenzungsrechteck“;
`classes/ezgmllocationfilter.php` dokumentiert Parameter für einen echten („arccosine“) oder annähernd echten
(„pythagorean“) kreisförmigen Filter. Die Sortierung ist genau. Um „2,5 km entfernt“ anzuzeigen, verwenden Sie den
Parameter `arccosine` mit `as_object`, `false()`, um einen Wert zu erhalten, aus dem sich die Entfernung berechnen lässt
(README).

## Woher der Punkt des Besuchers kommt

| Quelle | Hinweis |
|---|---|
| Eine Liste von Stadtteilen oder Orten mit ihren Koordinaten, als Inhalte gepflegt | keine personenbezogenen Daten; einfach |
| Eine Postleitzahl, die der Besucher eingibt, mit Ihrer eigenen Tabelle umgerechnet | braucht eine Postleitzahltabelle, die Sie pflegen |
| Die Standortbestimmung des Browsers | fragt den Besucher um Erlaubnis; personenbezogene Daten, nur im Browser verwendet, sofern Sie sie nicht senden |

Vermeiden Sie es, Positionen von Besuchern an Dienste von Drittanbietern zu senden; eine Liste von Stadtteilen genügt
oft.

## Das Ergebnis darstellen

Zeigen Sie eine Liste (Name, Adresse, Entfernung, Öffnungszeiten, Link), nicht nur eine Karte: Eine Liste funktioniert
mit Screenreadern, auf Telefonen und ohne Kartenschlüssel.

## Verwandte Seiten

- [Kartenstandorte mit ezgmaplocation](kartenstandorte-mit-ezgmaplocation.md)
- [Karten und Datenschutz](karten-und-datenschutz.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
