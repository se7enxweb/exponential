---
title: Bilder in REST-Antworten
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 510
summary: Wie ein ezimage-Feld in einer REST-Antwort aussieht (der Pfad der Originaldatei, ohne Alternativtext), wie Sie daraus eine URL machen, warum das Original für ein Telefon die falsche Größe hat, wie Sie Alternativtext und Bildinformationen von expimage erhalten und wie sich Bildobjekte von Bildfeldern unterscheiden.
keywords: Bilder, ezimage, Original, Pfad, URL, Alternativtext, alt, expimage::info, expimage::alt, expimage::attributes, Bildobjekt, Bildfeld, Mobile App, images, original, path, alternative text, image object, image field, mobile app
type: help
---
# Bilder in REST-Antworten

Diese Seite richtet sich an App-Entwickler, die Bilder aus Exponential-Inhalten anzeigen. Sie erklärt genau, was die
REST-API für ein Bildfeld liefert und was Sie damit tun.

## Inhalt

1. [Der Wert eines Bildfelds](#der-wert-eines-bildfelds)
2. [Vom Pfad zur URL](#vom-pfad-zur-url)
3. [Warum Sie nicht das Original laden](#warum-sie-nicht-das-original-laden)
4. [Alternativtext und Bildinformationen](#alternativtext-und-bildinformationen)
5. [Bildobjekte und Bildfelder](#bildobjekte-und-bildfelder)

## Der Wert eines Bildfelds

Bei einem Feld des Datentyps `ezimage` ist der REST-`value` die Zeichenkettenform des Bilds (`eZImageType::toString()`
liefert `<url of the original>|<alternative text>`), die `ezpRestContentModel` beim `|` abschneidet. Die App erhält
also den **Pfad der Originalbilddatei**, so wie der Bilddatentyp ihn speichert, und **nicht** den Alternativtext. Ein
leeres Bildfeld liefert einen leeren Wert.

```json
"image": {"type": "ezimage", "identifier": "image", "value": "<path of the original file>", "id": 0, "classattribute_id": 0}
```

(Die Werte sind Platzhalter.) Weder Bildalias noch Breite oder Höhe stehen in der REST-Antwort.

## Vom Pfad zur URL

Der Pfad ist relativ zur Wurzel der Website. Verbinden Sie ihn mit der Basisadresse der Installation:

```text
https://example.com/ + <path>
```

Bilddateien sind Dateien auf dem Server; ob der Webserver sie ohne Zugangsdaten ausliefert, hängt von der Installation
und ihrer Hosting-Konfiguration ab. Prüfen Sie eine URL von einem abgemeldeten Client aus, bevor Sie das Laden der
Bilder in der App darauf aufbauen. Die `expimage`-Dienste antworten für jede Variante mit einer `absolute_url`, was den
Schritt des Verbindens erspart ([Bildaliase für Bildschirmgrößen](bildaliase-fuer-bildschirmgroessen.md)).

## Warum Sie nicht das Original laden

Das Original ist das, was der Redakteur hochgeladen hat: oft mehrere Megabyte groß und Tausende Pixel breit. Es in einer
Liste zu laden, kostet den Benutzer Datenvolumen und Speicher. Fordern Sie stattdessen eine benannte Größe (einen
Bildalias) an, die der Server bei Bedarf erzeugt und speichert
([Bildaliase für Bildschirmgrößen](bildaliase-fuer-bildschirmgroessen.md)). Verwenden Sie das Original höchstens für eine
Vollbild-Zoomansicht.

## Alternativtext und Bildinformationen

| Bedarf | Dienst | Argumente |
|---|---|---|
| Alternativtext für Screenreader | `expimage::alt` | Knoten-ID, Feldbezeichner |
| Dateiname, Größe, Abmessungen, MIME-Typ, Alternativtext des Originals | `expimage::info` | Knoten-ID, Feldbezeichner |
| Die Bildfelder eines Knotens und ob sie Inhalt haben | `expimage::attributes` | Knoten-ID |

Alle drei brauchen `content/read` auf dem Knoten. Geben Sie Bildern in der App immer eine Bedienungshilfen-Beschriftung;
Prüfer im App Store und Benutzer mit Screenreadern bemerken das
([Bereit für die App-Store-Prüfung](../checklisten/bereit-fuer-die-app-store-pruefung.md)).

## Bildobjekte und Bildfelder

Exponential hat Bild**felder** innerhalb anderer Klassen (das Bild eines Artikels) und Bild**objekte** (Elemente der
Klasse `image` in der Medienbibliothek, oft mit Artikeln verknüpft oder darin eingebettet). Ein Bildobjekt lesen Sie
wie jeden Knoten; sein Feld `image` enthält das Bild. Um Bildobjekte unterhalb eines Ordners mit jeweils einem Alias
aufzulisten, verwenden Sie `expimage::list::<parent node id>::<limit>::<offset>::<alias>`.

## Verwandte Seiten

- [Bildaliase für Bildschirmgrößen](bildaliase-fuer-bildschirmgroessen.md)
- [Felder und Feldwerte](../rest-api/felder-und-feldwerte.md)
- [Bilder und Bildvarianten](../../ersteller/website-ersteller/bilder-und-bildvarianten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
