---
title: Portfolios
audience: individual, website-creator
category: personal-sites
order: 1610
summary: Der Bereich für Fotografen, Illustratoren, Designer und andere Kreative, die ihre Arbeiten mit Exponential zeigen - Galerien, viele Bilder auf einmal hochladen, Bilder im Browser mit ezie bearbeiten, Bildvarianten und die Grenzen, die Sie kennen sollten.
keywords: Portfolio, Galerie, Fotografie, Illustration, Kunstwerke, Bilder, Bildvarianten, ezie, Bildeditor, ezmultiupload, Mehrfach-Upload, Wasserzeichen, Bewertung, ezstarrating, Tags, Privatperson, gallery, photography, artwork, image variations, image editor, multi-upload, watermark, rating
type: help
---
# Portfolios

Dieser Bereich ist für Privatpersonen, die ihre Arbeiten zeigen: Fotografien, Illustrationen, Gemälde,
Designprojekte, Kunsthandwerk. Ein Portfolio besteht überwiegend aus Bildern, und Exponential behandelt jedes Bild
als Inhalt mit eigenem Titel, eigener Bildunterschrift und eigenen Tags, nicht als Datei in einem Ordner.

## Inhalt

1. [Was für ein Portfolio mitgeliefert wird](#was-für-ein-portfolio-mitgeliefert-wird)
2. [Die Seiten dieses Bereichs](#die-seiten-dieses-bereichs)
3. [Zwei Arten, ein Portfolio zu gliedern](#zwei-arten-ein-portfolio-zu-gliedern)
4. [Grenzen, die Sie kennen sollten](#grenzen-die-sie-kennen-sollten)

## Was für ein Portfolio mitgeliefert wird

| Bedarf | Was es trägt | Quelle |
|---|---|---|
| Eine Galerieseite | Die Klasse *Gallery* (Name, Kurzbeschreibung, Beschreibung, Bild, Tags, Bewertung) | Paket `sevenx_classes` |
| Eine einzelne Arbeit | Die Klasse *Image* (Name, Bildunterschrift, Bild, Tags, Bewertung) | ebenda |
| Viele Uploads auf einmal | Die Mehrfach-Upload-Seite; Ordner und Galerien bieten sie an | `ezmultiupload` |
| Zuschneiden, Drehen, Wasserzeichen im Browser | Der Bildeditor im Bearbeitungsformular | `ezie` |
| Vorschaubilder und Größen | Bildvarianten (Aliase) in `image.ini` | Kernel |
| Gruppierung nach Motiv | Das Schlüsselwortfeld `tags`; `eztags` für eine verwaltete Liste | `sevenx_classes`; `eztags` |
| Bewertungen durch Besucher | Feld für Sternebewertungen | `ezstarrating` |

## Die Seiten dieses Bereichs

| Seite | Lesen Sie sie, wenn |
|---|---|
| [Eine Bildergalerie aufbauen](eine-bildergalerie-aufbauen.md) | Sie die erste Galerie einrichten |
| [Viele Bilder hochladen](viele-bilder-hochladen.md) | Sie Dutzende oder Hunderte Dateien haben |
| [Bilder mit ezie bearbeiten](bilder-mit-ezie-bearbeiten.md) | Sie ohne ein anderes Programm zuschneiden, drehen oder ein Wasserzeichen setzen möchten |
| [Bildvarianten für Portfolios](bildvarianten-fuer-portfolios.md) | Ihnen wichtig ist, wie scharf und wie schnell Ihre Bilder sind |

## Zwei Arten, ein Portfolio zu gliedern

| Gliederung | Baum | Passt zu |
|---|---|---|
| **Nach Serien** | Portfolio (Ordner) > eine *Gallery* pro Serie > *Image*-Elemente | Fotografen, Illustratoren mit Serien oder Projekten |
| **Nach Projekten** | Portfolio (Ordner) > ein *Article* pro Projekt, darunter eine *Gallery* für die Bilder | Designern, die jedes Projekt im Text erklären |

Tags bieten einen zweiten Weg zum Stöbern: nach Motiv, Technik oder Jahr, über Serien hinweg
([Tags und Kategorien](../blogs/tags-und-kategorien.md)).

## Grenzen, die Sie kennen sollten

- **Die Originaldatei wird so gespeichert, wie Sie sie hochladen** (der Alias `original`). Entfernen Sie vor dem
  Hochladen Standortdaten und andere Metadaten, die Sie nicht teilen möchten, und laden Sie keine Dateien in
  Druckauflösung hoch, die nicht kopiert werden sollen.
- **Wasserzeichen** werden mit `ezie` pro Bild gesetzt, nicht automatisch bei jedem Upload.
- **Kein Druckshop und kein Lizenzshop** wird mitgeliefert. Der eingebaute Shop kann Artikel verkaufen, aber der
  Verkauf von Lizenzen oder Drucken braucht eine eigene Einrichtung ([Shop-Überblick](../../ersteller/website-ersteller/shop-ueberblick.md)).
- **Kein Schutz gegen Rechtsklick.** Alles, was ein Browser anzeigen kann, lässt sich speichern; veröffentlichen Sie
  Größen, die Sie zu teilen bereit sind.

## Verwandte Seiten

- [Privatpersonen: hier beginnen](../privatpersonen-hier-beginnen.md)
- [Bilder und Bildvarianten](../../ersteller/website-ersteller/bilder-und-bildvarianten.md)
- [Die Medienbibliothek organisieren](../../ersteller/website-ersteller/die-medienbibliothek-organisieren.md)
- [Medien und Uploads](../../medien-und-uploads.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
