---
title: Titel und Beschreibungen mit xrowmetadata
audience: local-business, businesses, customer, editor
category: local-business
order: 22
summary: Titel und Beschreibung schreiben, die für jede Seite der Website eines lokalen Unternehmens in den Suchergebnissen erscheinen - was xrowmetadata dem Bearbeitungsformular hinzufügt, was angezeigt wird, wenn die Felder leer sind, Schreibtipps mit Beispielen und das Vorschaubild für soziale Netzwerke.
keywords: xrowmetadata, Meta-Titel, Meta-Beschreibung, Suchergebnis-Snippet, Open Graph, Vorschaubild, Schlüsselwörter, Redakteur, lokales Unternehmen, meta title, meta description, search result snippet, sharing image, keywords, editor, local business
type: help
---
# Titel und Beschreibungen mit xrowmetadata

Diese Seite ist für Redakteure der Website eines lokalen Unternehmens, die ausfüllen, was Suchmaschinen für jede Seite
zeigen. Die Erweiterung `xrowmetadata` gehört zur Standarddistribution (`composer.json`, `se7enxweb/xrowmetadata`) und
fügt den Inhaltsklassen, die Ihr Ersteller auswählt, ein Feld für **Metadaten** hinzu
([xrowmetadata](../../../funktionen/6.0/erweiterungen/xrowmetadata.md)).

## Inhalt

1. [Was Sie im Bearbeitungsformular sehen](#was-sie-im-bearbeitungsformular-sehen)
2. [Was passiert, wenn die Felder leer sind](#was-passiert-wenn-die-felder-leer-sind)
3. [Einen guten Titel schreiben](#einen-guten-titel-schreiben)
4. [Eine gute Beschreibung schreiben](#eine-gute-beschreibung-schreiben)
5. [Das Vorschaubild zum Teilen](#das-vorschaubild-zum-teilen)
6. [Wenn xrowmetadata nicht eingeschaltet ist](#wenn-xrowmetadata-nicht-eingeschaltet-ist)
7. [Checkliste](#checkliste)

## Was Sie im Bearbeitungsformular sehen

Sobald Ihr Ersteller einer Klasse den Datentyp für Metadaten hinzugefügt hat (**Setup > Klassen**), hat jedes Objekt
dieser Klasse ein Metadatenfeld mit Titel, Schlüsselwörtern, Beschreibung und einem Open-Graph-Bild
([Seitenmetadaten und Teilen in sozialen Medien](../../ersteller/website-ersteller/metadaten-und-teilen-in-sozialen-medien.md#mit-xrowmetadata)).
Das Pagelayout schreibt sie dann in den Kopf der Seite.

## Was passiert, wenn die Felder leer sind

Das hängt vom Template ab, das Ihr Ersteller geschrieben hat. Das übliche Muster, beschrieben in
[Seitenmetadaten und Teilen in sozialen Medien](../../ersteller/website-ersteller/metadaten-und-teilen-in-sozialen-medien.md), greift
für den Titel auf den Namen der Website und für die Beschreibung auf den Einleitungstext der Seite zurück. Fragen Sie
Ihren Ersteller, was Ihre Website macht, und tragen Sie es hier ein:

| Feld leer | Unsere Website zeigt stattdessen |
|---|---|
| Titel | |
| Beschreibung | |
| Vorschaubild zum Teilen | |

## Einen guten Titel schreiben

- Stellen Sie die wichtigsten Wörter an den Anfang: die Leistung und den Ort.
- Hängen Sie den Namen des Unternehmens ans Ende.
- Halten Sie ihn kurz; Suchmaschinen schneiden lange Titel ab.

| Seite | Schwacher Titel | Besserer Titel |
|---|---|---|
| Startseite | Startseite | Fahrradreparatur in Flussdorf - Beispielräder |
| Leistung | Leistungen 3 | Reifenreparatur, während Sie warten - Beispielräder |
| Kontakt | Kontakt | Öffnungszeiten und Anfahrt - Beispielräder, Flussdorf |

(Unternehmensnamen und Orte sind hier erfundene Beispiele.)

## Eine gute Beschreibung schreiben

Die Beschreibung ist die eine oder andere Zeile unter dem Titel in den Suchergebnissen. Sie entscheidet kaum über die
Platzierung, aber darüber, ob Menschen klicken.

- Sagen Sie in ein oder zwei Sätzen, was die Seite bietet und warum man Sie wählen sollte.
- Nennen Sie eine konkrete Tatsache: „Samstags geöffnet“, „Reparatur am selben Tag“, „Kostenlose Parkplätze“.
- Wiederholen Sie nicht den Titel Wort für Wort.
- Schreiben Sie für jede Seite eine andere Beschreibung.

## Das Vorschaubild zum Teilen

Wenn jemand eine Seite in einem sozialen Netzwerk oder einer Chat-App teilt, zeigt das Netzwerk ein Bild. Mit
`xrowmetadata` können Sie pro Seite eines wählen, und Ihr Ersteller kann pro Klasse ein Standardbild festlegen.
Verwenden Sie ein klares Foto der Ladenfront, des Gerichts oder der Arbeit, kein Logo mit viel weißem Rand. Soziale
Netzwerke brauchen das Bild in einer für Vorschauen ausreichenden Größe, die Ihr Ersteller als Bildvariante festlegt
([Bilder und Bildvarianten](../../ersteller/website-ersteller/bilder-und-bildvarianten.md)).

## Wenn xrowmetadata nicht eingeschaltet ist

Ihr Ersteller kann stattdessen ein oder zwei eigene Felder hinzufügen, zum Beispiel ein Textfeld `meta_description`,
und den Einleitungstext verwenden, wenn es leer ist ([Mit eigenen Feldern](../../ersteller/website-ersteller/metadaten-und-teilen-in-sozialen-medien.md#mit-eigenen-feldern)).
Die Schreibtipps oben gelten genauso.

## Checkliste

- [ ] Die Klassen Ihrer Hauptseiten (Startseite, Ordner, Artikel, Produkte) haben das Metadatenfeld.
- [ ] Jede Hauptseite hat ihren eigenen Titel und ihre eigene Beschreibung.
- [ ] Titel nennen die Leistung und den Ort.
- [ ] Ein Standard-Vorschaubild ist festgelegt.
- [ ] Sie wissen, was die Website zeigt, wenn ein Feld leer ist.

## Verwandte Seiten

- [Grundlagen der lokalen Suchmaschinenoptimierung](grundlagen-der-lokalen-suchmaschinenoptimierung.md)
- [Sitemaps und robots.txt](sitemaps-und-robots-txt.md)
- [SEO-Prüfungen](../../kunden/alltag/seo-pruefungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
