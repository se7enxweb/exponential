---
title: Eine Bildergalerie aufbauen
audience: individual, website-creator, editor
category: personal-sites
order: 1611
summary: Schritt für Schritt, wie eine Person mit den mitgelieferten Klassen Gallery und Image eine Portfolio-Galerie aufbaut - der Baum, das Titelbild, Bildunterschriften und Alternativtext, Sortierung, Tags und Bewertungen und was Sie prüfen sollten, bevor Sie sie jemandem zeigen.
keywords: Galerie, Bildergalerie, Portfolio, Klasse Gallery, Klasse Image, Bildunterschrift, Alternativtext, Titelbild, Sortierung, Tags, Bewertung, ezstarrating, Serie, Fotografie, gallery, image gallery, caption, alternative text, cover image, sort order, rating
type: help
---
# Eine Bildergalerie aufbauen

Diese Seite ist für Privatpersonen, die eine Galerie ihrer Arbeiten möchten. Sie verwendet die Klassen *Gallery*
und *Image* des Standard-Seitenpakets; die Vollansicht der Galerie listet die Bilder darunter auf (Templates
`full/gallery.tpl` im Design `ezwebin` und seinen Nachfolgern).

## Inhalt

1. [Die Klassen](#die-klassen)
2. [Schritt für Schritt](#schritt-für-schritt)
3. [Bildunterschriften und Alternativtext](#bildunterschriften-und-alternativtext)
4. [Reihenfolge](#reihenfolge)
5. [Tags und Bewertungen](#tags-und-bewertungen)
6. [Bevor Sie sie zeigen](#bevor-sie-sie-zeigen)

## Die Klassen

| Klasse | Felder (Bezeichner in der Klassendefinition) | Rolle |
|---|---|---|
| *Gallery* | `name`, `short_description`, `description`, `image`, `tags`, `rating`, `metadata` | Die Galerieseite; `image` ist ihr Titelbild |
| *Image* | `name`, `caption`, `image`, `tags`, `rating`, `metadata` | Eine einzelne Arbeit |

Quelle: `class-gallery.xml` und `class-image.xml` im Paket `sevenx_classes`. Wenn Ihre Arbeiten mehr Felder brauchen
(Jahr, Technik, Maße), fügen Sie sie *Image* hinzu oder legen Sie eine eigene Klasse an
([Ihr Inhaltsmodell entwerfen](../../ersteller/website-ersteller/ihr-inhaltsmodell-entwerfen.md)).

## Schritt für Schritt

1. **Schaffen Sie dem Portfolio ein Zuhause**: einen *Folder* namens „Arbeiten" oder „Portfolio" unter der Startseite.
2. **Legen Sie darunter eine *Gallery*** für die erste Serie an. Füllen Sie den Namen, eine einzeilige
   Kurzbeschreibung und eine längere Beschreibung der Serie aus.
3. **Wählen Sie ein Titelbild** für das Feld `image` der Galerie. Listen von Galerien zeigen es an.
4. **Veröffentlichen Sie die Galerie.** Sie ist vorerst leer.
5. **Fügen Sie darunter Bilder hinzu**, ein *Image* pro Arbeit: entweder einzeln (**Hier erstellen** > *Image*) oder
   viele auf einmal ([Viele Bilder hochladen](viele-bilder-hochladen.md)).
6. **Füllen Sie bei jedem Bild Namen und Bildunterschrift aus**; schneiden Sie bei Bedarf mit `ezie` zu oder drehen
   Sie das Bild ([Bilder mit ezie bearbeiten](bilder-mit-ezie-bearbeiten.md)).
7. **Veröffentlichen** Sie und sehen Sie sich die Galerie als Besucher an.
8. Wiederholen Sie das für die nächste Serie.

## Bildunterschriften und Alternativtext

Sie erfüllen unterschiedliche Aufgaben; füllen Sie beides aus.

| Feld | Für | Beispiel |
|---|---|---|
| Name | Der Titel der Arbeit | „Hafen im Morgengrauen" |
| Bildunterschrift | Kontext für alle Leser: Jahr, Ort, Technik | „Öl auf Leinwand, 2025, 60 x 80 cm" |
| Alternativtext (am Bildfeld) | Menschen, die das Bild nicht sehen können, und Suchmaschinen | „Ein kleiner Fischerhafen unter orangem Morgenhimmel, Boote als Silhouetten" |

Beschreiben Sie, was auf dem Bild zu sehen ist, nicht, dass es ein Bild ist. Siehe
[Barrierefreiheit für Website-Ersteller](../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md).

## Reihenfolge

Galerien listen ihre Bilder in der Sortierreihenfolge des Galerieknotens. Stellen Sie in der Administration die
Sortierung der Galerie ein (nach Name, nach Veröffentlichungsdatum oder nach Priorität) und geben Sie bei Priorität
jedem Bild eine Zahl, um es von Hand zu platzieren
([Den Inhaltsbaum strukturieren](../../ersteller/website-ersteller/den-inhaltsbaum-strukturieren.md)). Stellen Sie Ihre
stärksten Arbeiten an den Anfang; die meisten Besucher sehen drei bis fünf Bilder, bevor sie sich entscheiden.

## Tags und Bewertungen

- **Tags** (das Schlüsselwortfeld oder `eztags`, wenn Sie es hinzufügen) lassen Besucher jede Arbeit zu „Häfen"
  oder in „Aquarell" über alle Galerien hinweg sehen. Legen Sie vor dem Start mit sich selbst eine kurze Tag-Liste
  fest; zehn gute Tags schlagen hundert Einzelstücke ([Tags und Kategorien](../blogs/tags-und-kategorien.md)).
- **Bewertung** ist ein Feld für Sternebewertungen (`ezstarrating`): Besucher können einmal pro Sitzung zwischen 1
  und 5 Sterne vergeben (laut ihrer `README`). Lassen Sie es in Ihren Templates weg, wenn Sie keine öffentlichen
  Bewertungen möchten.

## Bevor Sie sie zeigen

- [ ] Jedes Bild hat einen Namen, eine Bildunterschrift und einen Alternativtext.
- [ ] Für jede Galerie ein Titelbild gewählt.
- [ ] Die Reihenfolge stellt das Beste an den Anfang.
- [ ] Standortdaten aus Originalen entfernt, die Sie nicht teilen wollten.
- [ ] Nur Arbeiten, die Sie veröffentlichen dürfen; Personen auf Fotos haben zugestimmt.
- [ ] Sieht auf einem Smartphone richtig aus und lädt schnell ([Bildvarianten für Portfolios](bildvarianten-fuer-portfolios.md)).

## Verwandte Seiten

- [Portfolios](README.md)
- [Viele Bilder hochladen](viele-bilder-hochladen.md)
- [Bilder und Bildvarianten](../../ersteller/website-ersteller/bilder-und-bildvarianten.md)
- [Medien und Uploads](../../medien-und-uploads.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
