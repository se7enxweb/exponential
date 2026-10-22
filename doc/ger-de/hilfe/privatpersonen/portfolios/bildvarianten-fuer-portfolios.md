---
title: Bildvarianten für Portfolios
audience: individual, website-creator
category: personal-sites
order: 1614
summary: Was Bildvarianten (Aliase) sind, welche in settings/image.ini mitgeliefert werden, wie ein Portfolio sie für Vorschaubilder, Galerieansichten und große Ansichten nutzt, wie Sie einen größeren Alias für Ihre Arbeiten hinzufügen, Qualität und Formate und was nach der Änderung eines Alias zu tun ist.
keywords: Bildvarianten, Bild-Alias, image.ini, reference, small, medium, large, original, Vorschaubilder, Qualität, WebP, responsive Bilder, Portfolio, ImageMagick, GD, Cache, image variations, image alias, thumbnails, quality, responsive images
type: help
---
# Bildvarianten für Portfolios

Diese Seite ist für Privatpersonen, deren Website überwiegend aus Bildern besteht und die sie scharf und schnell
geladen haben möchten. Sie laden eine Datei hoch; Exponential erzeugt für jede Stelle, an der das Bild erscheint,
kleinere Kopien, genannt **Bildvarianten** oder **Aliase**. Die Seite für Website-Ersteller mit allen Einzelheiten
ist [Bilder und Bildvarianten](../../ersteller/website-ersteller/bilder-und-bildvarianten.md).

## Inhalt

1. [Wie es funktioniert](#wie-es-funktioniert)
2. [Die mitgelieferten Aliase](#die-mitgelieferten-aliase)
3. [Was ein Portfolio braucht](#was-ein-portfolio-braucht)
4. [Einen größeren Alias hinzufügen](#einen-größeren-alias-hinzufügen)
5. [Qualität und Formate](#qualität-und-formate)
6. [Nach der Änderung eines Alias](#nach-der-änderung-eines-alias)
7. [Checkliste](#checkliste)

## Wie es funktioniert

- Sie laden das Original hoch. Es wird unverändert gespeichert (der Alias `original`).
- Wenn ein Template einen Alias anfordert, erzeugt die Website diese Kopie einmal aus dem Original und behält sie.
- Jede Seite zeigt die passende Kopie: ein Vorschaubild in einer Liste, eine mittlere Größe in einer Galerie, eine
  große auf der eigenen Seite des Bildes.

Sie verkleinern also nie für jede Stelle von Hand, und Besucher auf einem Smartphone laden für ein Vorschaubild kein
großes Original herunter.

## Die mitgelieferten Aliase

Aus `[AliasSettings] AliasList[]` in `settings/image.ini`:

| Alias | Größe | Typische Verwendung |
|---|---|---|
| `reference` | passt in 600 x 600; die Basis der anderen | Große Ansicht in älteren Designs |
| `small` | 100 x 100 | Vorschaubilder in Listen |
| `tiny` | 30 x 30 | Symbole |
| `medium` | 200 x 200 | Galerieraster |
| `large` | 300 x 300 | Bilder in Artikeln |
| `rss` | 88 x 31 | RSS-Feeds |
| `original` | die hochgeladene Datei | Downloads |

Bilder werden nur verkleinert, nie vergrößert. Themes fügen in ihrer `image.ini.append.php` eigene Aliase hinzu;
Ihre Website hat daher womöglich mehr. Die Seite für Website-Ersteller zeigt, wie Sie sie auflisten.

## Was ein Portfolio braucht

Die mitgelieferten Größen wurden für Textwebsites gewählt. Für Fotografie oder Kunst sind 600 Pixel auf einem
modernen Bildschirm klein. Fragen Sie sich:

| Frage | Wenn ja |
|---|---|
| Sehen Besucher einzelne Arbeiten im Vollbild an? | Einen größeren Alias für die Vollansicht des Bildes hinzufügen |
| Zeigen Sie ein Raster mit vielen Vorschaubildern? | Annähernd quadratische Vorschaubilder gleicher Größe wirken ruhiger |
| Sind Ihre Originale sehr groß? | In Ordnung; verlinken Sie das Original aber nicht, wenn es nicht kopiert werden soll |

## Einen größeren Alias hinzufügen

Aliase sind Einstellungen in einer Design-Erweiterung, nichts, was Sie in der Administration ändern. Ein einfaches
Beispiel in der `settings/image.ini.append.php` Ihrer eigenen Erweiterung:

```ini
[AliasSettings]
AliasList[]=portfolio_large

[portfolio_large]
Reference=
Filters[]
Filters[]=geometry/scaledownonly=1600;1600
```

Danach verwendet ein Template ihn, zum Beispiel
`{attribute_view_gui attribute=$node.data_map.image image_class='portfolio_large'}`. Die JPEG-Qualität beträgt
standardmäßig 75 (`[MIMETypeSettings] Quality[]`); Fotografen erhöhen sie manchmal. Templates zu ändern ist Arbeit
für Website-Ersteller; siehe
[Bilder und Bildvarianten, Eigene Aliase hinzufügen](../../ersteller/website-ersteller/bilder-und-bildvarianten.md#eigene-aliasse-hinzufügen)
und [Template-Overrides](../../ersteller/website-ersteller/template-overrides.md). Wenn Ihnen das zu weit geht, ist es
ein kleiner, klar umrissener Auftrag für einen Partner ([Wann Sie sich Hilfe holen sollten](../im-betrieb/wann-sie-sich-hilfe-holen-sollten.md)).

## Qualität und Formate

- Der Server wandelt zuerst mit GD um und mit ImageMagick bei Formaten, die GD nicht lesen kann
  ([Bildverarbeitung und Medien](../../hosting/grundlagen/bildverarbeitung-und-medien.md)).
- JPEG passt zu Fotografien, PNG zu Zeichnungen mit flächigen Farben und Transparenz.
- Ausgabeformate und Qualität sind Einstellungen; siehe
  [Bilder und Bildvarianten, Konverter, Formate und Qualität](../../ersteller/website-ersteller/bilder-und-bildvarianten.md#konverter-formate-und-qualität)
  und [SVG und Bildformate](../../ersteller/website-ersteller/svg-und-bildformate.md).
- Responsive Bilder (mehrere Größen für unterschiedliche Bildschirme) sind Sache der Templates
  ([Responsive Bilder](../../ersteller/website-ersteller/bilder-und-bildvarianten.md#responsive-bilder)).

## Nach der Änderung eines Alias

Bereits erzeugte Kopien werden nicht von selbst neu erzeugt. Nachdem Sie eine Alias-Definition geändert haben,
müssen die vorhandenen Varianten geleert werden, damit sie mit der neuen Regel neu entstehen, danach der Cache der
Inhaltsansichten (`php bin/php/cache.php imagealias clear --allow-root-user`; siehe
[Nach der Änderung eines Alias](../../ersteller/website-ersteller/bilder-und-bildvarianten.md#nach-der-änderung-eines-alias)).
Planen Sie das für einen ruhigen Moment: Die ersten Aufrufe nach dem Leeren sind langsamer, während die Kopien
erzeugt werden.

## Checkliste

- [ ] Sie wissen, welchen Alias jede Seite Ihres Portfolios verwendet.
- [ ] Einzelne Arbeiten werden groß genug für Ihre Art von Arbeit gezeigt.
- [ ] Vorschaubilder in einem Raster haben eine einheitliche Größe.
- [ ] Originale sind nur dort verlinkt, wo sie heruntergeladen werden sollen.
- [ ] Nach jeder Alias-Änderung wurden alte Varianten geleert.

## Verwandte Seiten

- [Portfolios](README.md)
- [Bilder und Bildvarianten](../../ersteller/website-ersteller/bilder-und-bildvarianten.md)
- [Performance und Caching](../../ersteller/website-ersteller/leistung-und-caching.md)
- [Richtlinien für Bilder und Dateien](../../kunden/alltag/richtlinien-fuer-bilder-und-dateien.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
