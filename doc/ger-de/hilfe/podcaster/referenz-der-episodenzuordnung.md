---
title: Referenz der Episodenzuordnung
audience: podcaster
category: podcasting-streaming
order: 19
summary: Wie jedes Element eines Podcast-Feed-Elements aus einer Episode entsteht - die Zuordnungen Titel, Beschreibung, Kategorie und Anhang einer Exportquelle, welche Datentypen jede akzeptiert, und die Elemente, die vom Objekt selbst stammen, etwa guid, pubDate, link, Autor und Dauer.
keywords: Zuordnung, Quelle, Titel, Beschreibung, Kategorie, Enclosure, Anhang, guid, Remote-ID, pubDate, link, itunes:author, itunes:duration, Datentypen, podcastText, podcastItemList, Referenz, mapping, source, title, description, category, enclosure, remote id, datatypes, reference
type: help
---
# Referenz der Episodenzuordnung

Diese Seite ist für Produzenten und Administratoren, die eine Episodenklasse in einem Podcast-Export zuordnen, und für
alle, die wissen möchten, woher ein Wert im Feed stammt. Der Code ist `eZRSSExport::podcastItemList()` und
`podcastText()` in `kernel/classes/ezrssexport.php`; die Spalten der Zuordnung liegen in `eZRSSExportItem`
(`kernel/classes/ezrssexportitem.php`).

## Inhalt

1. [Die vier Zuordnungen](#die-vier-zuordnungen)
2. [Datentypen und was der Feed erhält](#datentypen-und-was-der-feed-erhält)
3. [Elemente aus dem Objekt selbst](#elemente-aus-dem-objekt-selbst)
4. [Welche Zuordnung gilt](#welche-zuordnung-gilt)
5. [Häufige Fehler](#häufige-fehler)

## Die vier Zuordnungen

Jede Quelle eines Exports hat eine Klasse und vier Auswahlen für Attribute:

| Zuordnung | Element im Feed | Wenn leer oder nicht zugeordnet |
|---|---|---|
| **Titel** | `title` und `itunes:title` | der Name des Objekts |
| **Beschreibung** | `description` | das Element wird weggelassen |
| **Kategorie** | `category` | das Element wird weggelassen |
| **Anhang (Medien)** | `enclosure` mit `url`, `length` und `type` | **die Episode fehlt im Feed** |

## Datentypen und was der Feed erhält

Für Titel, Beschreibung und Kategorie (`podcastText()`):

| Datentyp des zugeordneten Felds | Was geschrieben wird |
|---|---|
| Formatierter Text (`ezxmltext`) | die gerenderte Ausgabe des Felds, als Text (das Markup in das XML maskiert) |
| Schlüsselwörter (`ezkeyword`) | die Schlüsselwörter als eine Zeichenkette |
| Textzeile, Textblock, Zahlen und andere Felder mit einem einfachen Wert | dieser Wert |
| Alles andere | nichts |

Für den Anhang:

| Datentyp | Ergebnis |
|---|---|
| Medien (`ezmedia`) | verwendet: Adresse, Größe und Typ der gespeicherten Datei |
| Datei (`ezbinaryfile`) | verwendet: dasselbe |
| Bild oder ein anderer | kein Enclosure: Die Episode fehlt in einem Podcast-Feed |

Die Hilfe des Formulars sagt, ein Enclosure könne ein Medien-, Bild- oder Dateiattribut sein. Der Podcast-Writer
akzeptiert nur Medien- und Dateifelder, und die Writer für RSS 1.0, RSS 2.0 und Atom verwenden überhaupt kein
Enclosure (`generateFeed()` in derselben Datei schreibt keines).

## Elemente aus dem Objekt selbst

| Element | Quelle |
|---|---|
| `link` | die Episodenseite: die sprechende URL bei eingeschalteter URL-Übersetzung, sonst `/content/view/full/<node id>`, hinter der Adresse der Website des Exports |
| `guid` (`isPermaLink="false"`) | die Remote-ID des Objekts, die Verschieben, Umbenennen und erneutes Veröffentlichen übersteht |
| `pubDate` | das Datum der Veröffentlichung des Objekts, als Datum nach RFC 2822 |
| `enclosure url` | `<site>/content/download/<object id>/<attribute id>/<file name>` |
| `enclosure length` | die Dateigröße in Byte, wie gespeichert |
| `enclosure type` | der gespeicherte MIME-Typ, bestimmt durch die Dateiendung über `settings/mime.ini` |
| `itunes:duration` | `HH:MM:SS`, berechnet aus dem ersten MPEG-1-Layer-III-Frame und der Dateigröße; fehlt bei anderen Dateien |
| `itunes:explicit` | **Enthält explizite Inhalte** des Kanals |
| `itunes:author` | der Name des Benutzers, der das Objekt angelegt hat (der Ersteller des Knotens) |

## Welche Zuordnung gilt

Ein Objekt wird einer Zuordnung über seine Klasse **und** darüber zugewiesen, dass der Ort der Quelle in seinem Pfad
liegt. Bei zwei Quellen für zwei Sendungen erhält jede Episode die Zuordnung der Sendung, in der sie liegt. Ein
Objekt, das zu keiner Zuordnung passt, wird übersprungen. Verborgene Objekte werden übersprungen, es sei denn, die
Website zeigt verborgene Objekte.

## Häufige Fehler

| Fehler | Wirkung | Lösung |
|---|---|---|
| Anhang nicht zugeordnet | der Feed hat einen Kanal und keine Elemente | das Medien- oder Dateifeld zuordnen |
| Anhang dem Bild zugeordnet | keine Elemente | das Audiofeld zuordnen |
| Falsche Klasse an der Quelle | keine Elemente | die Episodenklasse wählen und erneut **Setzen** drücken |
| Beschreibung einem sehr langen Feld zugeordnet | Apps schneiden den Text ab | ein kurzes Feld zuordnen; lange Notizen auf der Seite lassen |
| Eine M4A-Datei ohne Eintrag in `mime.ini` | `type="application/octet-stream"` | die Endung ergänzen ([Mit Ihrem Administrator arbeiten](mit-ihrem-administrator-arbeiten.md)) |
| Episoden von einem gemeinsamen technischen Benutzer angelegt | `itunes:author` zeigt den Namen dieses Benutzers | Episoden mit persönlichen Konten anlegen oder den Namen akzeptieren |

## Verwandte Seiten

- [Der Podcast-Feed erklärt](der-podcast-feed-erklaert.md)
- [Referenz der Kanalfelder](referenz-der-kanalfelder.md)
- [Fehlersuche bei Feeds und Medien](fehlersuche-bei-feeds-und-medien.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
