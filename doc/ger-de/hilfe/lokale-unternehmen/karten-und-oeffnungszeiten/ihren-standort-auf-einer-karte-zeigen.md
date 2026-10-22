---
title: Ihren Standort auf einer Karte zeigen
audience: local-business, businesses, customer, website-creator
category: local-business
order: 31
summary: Zwei Wege, den Standort eines lokalen Unternehmens auf einer Exponential-Website zu zeigen - der Datentyp ezgmaplocation (ein an Inhalten gespeicherter Punkt, Google Maps mit API-Schlüssel) und der Karten-Block des Layout-Editors (Google mit Schlüssel, OpenStreetMap ohne) - mit Einrichtungsschritten, was einen API-Schlüssel braucht, Datenschutz und einer Checkliste.
keywords: Karte, Standort, ezgmaplocation, Google Maps, API-Schlüssel, OpenStreetMap, Karten-Block, Layout-Editor, Breitengrad, Längengrad, Anfahrt, Datenschutz, Content Security Policy, map, location, API key, map block, layouts editor, latitude, longitude, directions, privacy
type: help
---
# Ihren Standort auf einer Karte zeigen

Diese Seite ist für Inhaber eines lokalen Unternehmens und die Person, die die Website baut. Exponential hat zwei Wege,
eine Karte auf eine Seite zu bringen. Beide zeigen einen Punkt; keiner ist ein Routenplaner. Für die Anfahrt öffnen
Besucher die Adresse in ihrer eigenen Karten-App.

## Inhalt

1. [Zwei Wege im Vergleich](#zwei-wege-im-vergleich)
2. [Der Datentyp ezgmaplocation](#der-datentyp-ezgmaplocation)
3. [Der Karten-Block des Layout-Editors](#der-karten-block-des-layout-editors)
4. [API-Schlüssel und Datenschutz](#api-schlüssel-und-datenschutz)
5. [Ein Anfahrtslink ganz ohne Schlüssel](#ein-anfahrtslink-ganz-ohne-schlüssel)
6. [Checkliste](#checkliste)

## Zwei Wege im Vergleich

| | Datentyp `ezgmaplocation` | Karten-Block (Layout-Editor) |
|---|---|---|
| Was es ist | ein Feld einer Inhaltsklasse, das Breitengrad, Längengrad und die Adresse speichert | ein Block, der auf einer Layoutseite platziert wird |
| Redakteure setzen den Punkt, indem sie | eine Adresse eintippen und **Find address** drücken oder die Markierung ziehen | Breitengrad, Längengrad und eine Zoomstufe eintippen oder die Adresse einer eingebetteten Karte einfügen |
| Kartenanbieter | Google Maps | Google Maps Embed mit Schlüssel, sonst OpenStreetMap |
| Ohne API-Schlüssel | Koordinaten angezeigt, keine Karte | OpenStreetMap-Karte |
| Außerdem nützlich für | Objekte in der Nähe eines Punktes finden (`ezgmlLocationFilter`) | Landingpages und die Startseite |
| Quelle | `extension/ezgmaplocation/`, [ezgmaplocation](../../../funktionen/6.0/erweiterungen/ezgmaplocation.md) | `extension/explayouts/classes/explayoutsmapblockhandler.php`, `explayouts.ini [BlockDefinition_map]` |

Beide Erweiterungen stehen in der Standard-`composer.json` (`se7enxweb/ezgmaplocation`, `se7enxweb/explayouts`).

## Der Datentyp ezgmaplocation

Einrichtung, laut `extension/ezgmaplocation/README` und [ezgmaplocation](../../../funktionen/6.0/erweiterungen/ezgmaplocation.md):

1. Besorgen Sie einen API-Schlüssel für die Google Maps JavaScript API (ein Konto bei Google; außerhalb von
   Exponential).
2. Tragen Sie ihn in `ezgmaplocation.ini` ein, Block `[GMapSettings]`, `ApiKey=<key>`, in `settings/override/` oder pro
   Siteaccess.
3. Aktivieren Sie die Erweiterung, erzeugen Sie die Autoloads neu und legen Sie die Tabelle `ezgmaplocation` an (die
   SQL-Dateien für jede Datenbank, einschließlich SQLite, liegen der Erweiterung bei).
4. Fügen Sie der Klasse, die Ihre Adresse enthält, ein Attribut **Google Maps Location** hinzu, zum Beispiel dem
   Kontaktobjekt.

Bearbeiten, für den Inhaber:

1. Öffnen Sie das Kontaktobjekt zum Bearbeiten.
2. Tippen Sie die Adresse unter der Karte ein, und drücken Sie **Find address** (Adresse suchen): Die Karte zentriert
   sich, und Breiten- und Längengrad werden ausgefüllt.
3. Ziehen Sie die Markierung bei Bedarf genau auf den Eingang. **Restore** (Wiederherstellen) holt den gespeicherten
   Punkt zurück.
4. Veröffentlichen Sie.

Ist der Schlüssel leer, laden Bearbeitungsformular und Seite kein Google-Skript und zeigen die Koordinaten ohne Karte
(`ezgmaplocation.ini.append.php`, Kommentar zu `ApiKey`).

## Der Karten-Block des Layout-Editors

Fügen Sie auf einer mit dem Layout-Editor gebauten Seite ([Layout-Editor](../../layout-editor.md)) einen Block
**Map** hinzu, und füllen Sie aus:

| Parameter | Bedeutung |
|---|---|
| Latitude, Longitude | Breiten- und Längengrad des Punktes (kopieren Sie sie aus einer beliebigen Karten-App) |
| Zoom (0-20) | wie viel Fläche gezeigt wird |
| Map type | Kartentyp: Roadmap (Straßenkarte) oder Satellite (Satellit), bei Google auch Terrain und Hybrid |
| Show marker | eine Nadel auf dem Punkt |
| Map embed URL | der ältere Weg: die Adresse einer einzubettenden Karte, verwendet, wenn kein Punkt angegeben ist |
| Width, Height | Breite und Höhe |

Der Handler baut die Kartenadresse: Mit `explayouts.ini [MapSettings] GoogleMapsApiKey` verwendet er die Maps Embed API
von Google; ohne Schlüssel verwendet er eine eingebettete OpenStreetMap-Karte. Die Karte **lädt erst nach einem Klick**
(das Block-Template verwendet den gemeinsamen Einbettungsteil mit einer Schaltfläche „Karte laden“), sodass Besucher
erst dann Kontakt mit dem Kartenanbieter haben, wenn sie es wählen.

## API-Schlüssel und Datenschutz

| Element | Braucht einen Schlüssel? | Wer ihn verwahrt |
|---|---|---|
| Karte von `ezgmaplocation` in Bearbeitungsformular und Seite | ja, Google Maps JavaScript API | das Unternehmen, eingetragen vom Ersteller |
| Karten-Block, Google | ja, `GoogleMapsApiKey` | das Unternehmen |
| Karten-Block, OpenStreetMap | nein | - |

Gut zu wissen:

- Ein Google-Schlüssel gehört zu einem Konto bei Google, das oberhalb eines kostenlosen Kontingents abrechnen kann.
  Lesen Sie die aktuellen Bedingungen von Google; diese Seite nennt keine Preise.
- Beschränken Sie den Schlüssel in der Konsole des Anbieters auf die Domain Ihrer Website.
- Eine Karte lädt Inhalte eines anderen Unternehmens. Nennen Sie es in Ihrer Datenschutzerklärung. Das Laden per Klick
  im Karten-Block hilft dabei.
- Wenn Ihre Website eine Content Security Policy sendet, muss der Ersteller die Hosts des Kartenanbieters erlauben
  ([Content Security Policy](../../../funktionen/6.0/content-security-policy.md)).

## Ein Anfahrtslink ganz ohne Schlüssel

Die einfachste und datensparsamste Möglichkeit: unter der Adresse ein einfacher Link, der die Adresse in der
Karten-App des Besuchers öffnet. Ein Redakteur kann ihn im Rich Text hinzufügen. Kein Schlüssel, kein Skript,
funktioniert auf jedem Smartphone. Viele lokale Unternehmen nutzen das zusammen mit einer eingebetteten Karte oder
stattdessen.

## Checkliste

- [ ] Die Adresse steht als Text auf der Seite.
- [ ] Kartenweg gewählt: Datentyp, Block, einfacher Link oder eine Kombination.
- [ ] Wenn Google verwendet wird: Schlüssel im Konto des Unternehmens angelegt, auf die Domain beschränkt, vom
      Ersteller eingetragen.
- [ ] Die Markierung sitzt auf dem Eingang, nicht in der Mitte des Häuserblocks.
- [ ] Die Datenschutzerklärung nennt den Kartenanbieter.
- [ ] Auf einem Smartphone getestet.

## Verwandte Seiten

- [Mehrere Standorte](mehrere-standorte.md)
- [Öffnungszeiten ohne besonderen Datentyp](oeffnungszeiten-ohne-besonderen-datentyp.md)
- [Landingpages mit Layouts](../../ersteller/website-ersteller/landingpages-mit-dem-layouts-editor.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
