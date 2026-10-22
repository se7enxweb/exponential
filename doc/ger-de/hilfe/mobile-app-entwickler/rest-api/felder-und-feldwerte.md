---
title: Felder und Feldwerte
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 160
summary: Die Felder von Exponential-Inhalten in einer App lesen und auswerten - die Feldrouten, die fünf Schlüssel eines Feldobjekts, wie der Wert bei Rich Text, Bildern, gesperrten Datentypen und allen anderen Datentypen geschrieben wird und wie Sie Felder sicher auf das Modell Ihrer App abbilden.
keywords: Felder, Feldwerte, Attribute, Datentyp, ezstring, ezxmltext, ezimage, ezuser, toString, DatatypeBlackListForExternal, FieldValues, Bezeichner, Parsen, Modellabbildung, REST, Mobile App, fields, field values, attributes, datatype, identifier, parsing, model mapping, mobile app
type: help
---
# Felder und Feldwerte

Diese Seite richtet sich an App-Entwickler, die REST-Antworten in das eigene Datenmodell der App überführen. Sie
erklärt die Feldrouten, die Form eines Feldes und vor allem, wie jeder Wert geschrieben wird, damit die App ihn
korrekt auswertet.

## Inhalt

1. [Die Feldrouten](#die-feldrouten)
2. [Das Feldobjekt](#das-feldobjekt)
3. [Wie Werte geschrieben werden](#wie-werte-geschrieben-werden)
4. [Felder auf Ihr Modell abbilden](#felder-auf-ihr-modell-abbilden)
5. [Wenn ein Feld nicht genügt](#wenn-ein-feld-nicht-genügt)

## Die Feldrouten

```bash
# every field of a node's object
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/<node id>/fields
# one field
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/object/<object id>/field/title
```

Dieselben Felder erhalten Sie mit `?ResponseGroups=Fields` beim Lesen eines Knotens, eines Objekts oder einer Liste
([Antwortgruppen](antwortgruppen.md)). Die Feldrouten verwenden standardmäßig die Gruppe `FieldValues` und fügen
`metadata` nur mit `?ResponseGroups=Metadata` hinzu.

## Das Feldobjekt

Jedes Feld hat unter seinem Bezeichner in der Map `fields` fünf Schlüssel (`ezpRestContentModel::attributeOutputData()`):

| Schlüssel | Bedeutung |
|---|---|
| `type` | die Zeichenkette des Datentyps, zum Beispiel `ezstring`, `ezxmltext`, `ezimage` |
| `identifier` | der Feldbezeichner der Klasse, zum Beispiel `title` |
| `value` | der Wert, siehe unten |
| `id` | die numerische ID dieses Feldes dieses Objekts |
| `classattribute_id` | die numerische ID der Felddefinition in der Klasse |

## Wie Werte geschrieben werden

| Datentyp | `value` |
|---|---|
| `ezxmltext` (Rich Text) | das gerenderte HTML des Feldes ohne alle Tags: reiner Text, ohne Absätze, Links oder eingebettete Bilder |
| `ezimage` | der Pfad der Originalbilddatei, wie der Bild-Datentyp sie speichert (der Teil vor `\|` seiner Zeichenkettenform); der Alternativtext wird abgeschnitten |
| jeder Datentyp in `site.ini [ContentSettings] DatatypeBlackListForExternal[]` | `null`; die ausgelieferte Liste enthält `ezuser`, sodass Benutzerkonten REST nie verlassen |
| jeder andere Datentyp | die Darstellung von `toString()` des Datentyps |

Die Form von `toString()` ist das eigene einfache Exportformat des Datentyps. Sie unterscheidet sich je Datentyp
(Zahlen, Daten, Wahrheitswerte, Relationslisten, Auswahlen); testen Sie daher jeden Datentyp, den Ihre Klassen
verwenden, an einer echten Antwort, bevor Sie den Parser schreiben, und behandeln Sie jeden Wert als Zeichenkette,
die Sie umwandeln. Erraten Sie Formate nicht aus dem Feldnamen.

Zwei Folgen für Apps:

- Rich Text braucht eine andere Quelle, wenn die App Formatierungen anzeigt: [Rich Text in Apps](../inhaltsmodellierung/rich-text-in-apps.md).
- Bilder brauchen eine Alias-URL für Bildschirmgrößen: [Bilder in REST-Antworten](../medien-und-bilder/bilder-in-rest-antworten.md).

## Felder auf Ihr Modell abbilden

- **Schlüssel nach `identifier`, Prüfung nach `type`.** Eine Klasse kann sich ändern: Der Administrator der Redaktion
  kann ein Feld hinzufügen oder einen Datentyp ändern. Ignorieren Sie unbekannte Bezeichner und reagieren Sie
  nachsichtig, wenn `type` nicht dem entspricht, was die App erwartet.
- **Behandeln Sie `null` und leere Zeichenketten als „kein Wert“.** Optionale Felder kommen oft leer.
- **Bewahren Sie `classIdentifier` beim Element auf.** Es sagt der App, welchen Bildschirm oder welche Zelle sie
  verwenden soll ([Inhaltsklassen für Apps](../inhaltsmodellierung/inhaltsklassen-fuer-apps.md)).
- **Verlassen Sie sich nicht auf `id`.** Die `id` eines Feldes gehört zu einem einzigen Objekt; als Schlüssel über
  Elemente hinweg ist sie nicht nützlich. Verwenden Sie `classattribute_id` oder `identifier` für die Felddefinition.

## Wenn ein Feld nicht genügt

| Bedarf | Quelle |
|---|---|
| Ein Feld in einer anderen Sprache | `?Translation=<locale>` ([Mehrsprachige Inhalte](../inhaltsmodellierung/mehrsprachige-inhalte.md)) |
| Attributwerte, wie die Remote-Dienste sie exportieren | `expnode::dataMap::<node id>` oder `expnode::attribute::<node id>::<identifier>` ([expservices aus einer App aufrufen](../json-endpunkte/expservices-aus-einer-app-aufrufen.md)) |
| Ein berechneter oder zusammengesetzter Wert | eine für die App geschriebene Serverfunktion ([ezjscore-Serverfunktionen für Ihre App](../json-endpunkte/ezjscore-serverfunktionen-fuer-ihre-app.md)) |

## Verwandte Seiten

- [Einen Knoten lesen](einen-knoten-lesen.md)
- [Datentypen auswählen](../../ersteller/website-ersteller/datentypen-waehlen.md)
- [Wie Exponential-Inhalte in eine App gelangen](../wie-exponential-inhalte-in-eine-app-gelangen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
