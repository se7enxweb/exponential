---
title: Tags und Kategorien
audience: individual, website-creator, editor
category: personal-sites
order: 1624
summary: Wie eine einzelne Person Blogbeiträge und Portfolioarbeiten in Exponential nach Thema ordnet - Ordner als Kategorien, das Schlüsselwortfeld (ezkeyword), das die mitgelieferten Klassen für Tags nutzen, die Erweiterung eztags für ein kontrolliertes Vokabular und die Gewohnheiten, die eine Tag-Liste über Jahre nützlich halten.
keywords: Tags, Kategorien, Schlüsselwörter, ezkeyword, eztags, Ordner, Themen, Taxonomie, Vokabular, Blog, Portfolio, Archiv, Navigation, categories, keywords, folders, taxonomy
type: help
---
# Tags und Kategorien

Diese Seite ist für Privatpersonen, deren Leser ältere Beiträge oder Arbeiten nach Thema finden sollen. Exponential
bietet Ihnen drei Werkzeuge; die meisten persönlichen Websites brauchen eines oder zwei davon.

## Inhalt

1. [Drei Werkzeuge](#drei-werkzeuge)
2. [Ordner als Kategorien](#ordner-als-kategorien)
3. [Das Schlüsselwortfeld der mitgelieferten Klassen](#das-schlüsselwortfeld-der-mitgelieferten-klassen)
4. [eztags für eine kontrollierte Liste](#eztags-für-eine-kontrollierte-liste)
5. [Was Sie verwenden sollten](#was-sie-verwenden-sollten)
6. [Gewohnheiten, die Tags nützlich halten](#gewohnheiten-die-tags-nützlich-halten)
7. [Checkliste](#checkliste)

## Drei Werkzeuge

| Werkzeug | Was es ist | Wo mitgeliefert |
|---|---|---|
| **Ordner** | Der Inhaltsbaum: Jedes Element liegt an einem Ort | Kernel |
| **Schlüsselwörter** (`ezkeyword`) | Freie Wörter, in ein Feld getippt, durch Kommas getrennt | das Feld `tags` von *Blog*, *Blog post*, *Image* und *Gallery* im Paket `sevenx_classes` |
| **eztags** | Ein verwalteter Tag-Baum mit Synonymen, Übersetzungen und Tag-Seiten | die Erweiterung `eztags`, in der `composer.json` im Wurzelverzeichnis |

Beachten Sie, dass das Feld „Tags" der mitgelieferten Blog- und Bildklassen ein **Schlüsselwortfeld** ist
(`datatype="ezkeyword"` in `class-blog_post.xml` und `class-image.xml`), kein `eztags`-Feld.

## Ordner als Kategorien

Ein Ordner gibt jedem Element genau ein Zuhause und wird Teil der Adresse und des Menüs. Gut für wenige breite,
dauerhafte Kategorien:

- ein Reiseblog mit einem Ordner pro Land;
- ein Portfolio mit einer Galerie pro Serie.

Für Themen, die sich überschneiden, taugen Ordner wenig. Ein Beitrag über „Kochen auf einem Segeltörn" gehört an zwei
Orte; dafür gibt es Tags. Einem Element lassen sich zusätzliche Orte im Baum geben, aber nutzen Sie das sparsam
([Den Inhaltsbaum strukturieren](../../ersteller/website-ersteller/den-inhaltsbaum-strukturieren.md)).

## Das Schlüsselwortfeld der mitgelieferten Klassen

- Tippen Sie Wörter durch Kommas getrennt ein: `sailing, cooking, Norway`.
- Nichts hindert Sie daran, `Sailing`, `sailing` und `sailling` zu tippen; für Einheitlichkeit sorgen Sie selbst.
- Schlüsselwörter werden mit dem Inhalt gespeichert, für die Suche indiziert, und Templates können verwandte Inhalte
  nach Schlüsselwort auflisten.

Das ist der schnellste Einstieg und reicht für ein Blog mit einigen hundert Beiträgen, wenn Sie eine schriftliche
Liste Ihrer Schlüsselwörter führen.

## eztags für eine kontrollierte Liste

`eztags` führt Tags als eigenen Baum (**Tags** in der Administration): Jedes Tag gibt es nur einmal, es kann
Synonyme und Übersetzungen haben und hat eine eigene Seite (`tags/view/...`), die alles auflistet, was damit getaggt
ist. Redakteure wählen mit Autovervollständigung aus der Liste, statt frei zu tippen. Um es zu nutzen, fügen Sie
Ihren Klassen ein `eztags`-Feld hinzu (oder ersetzen das Schlüsselwortfeld), was eine Klassenänderung ist
([Taggen mit eZ Tags](../../ersteller/website-ersteller/verschlagworten-mit-eztags.md),
[eztags](../../../funktionen/6.0/erweiterungen/eztags.md)).

## Was Sie verwenden sollten

| Ihre Website | Verwenden Sie |
|---|---|
| Ein kleines Blog, ein Autor | Schlüsselwörter, mit einer schriftlichen Liste |
| Ein Blog, das Sie viele Jahre führen möchten, oder zwei Autoren | `eztags` |
| Ein Portfolio mit Serien | Galerien als Ordner, dazu Schlüsselwörter oder `eztags` für Motive |
| Eine mehrsprachige Website | `eztags` (Tags lassen sich übersetzen) |
| Sie möchten eine Seite pro Thema ohne Template-Arbeit | `eztags` (Tag-Seiten werden mitgeliefert) |

## Gewohnheiten, die Tags nützlich halten

- **Zehn bis dreißig Tags** reichen für ein persönliches Blog völlig. Ein Tag, das nur einmal verwendet wird, ist
  Rauschen.
- **Legen Sie Schreibweise und Zahl** einmal fest: Singular oder Plural, Kleinschreibung.
- **Schreiben Sie die Liste auf** (oder lassen Sie `eztags` sie führen) und verwenden Sie wieder, statt neu zu
  erfinden.
- **Prüfen Sie zweimal im Jahr**: Führen Sie Beinahe-Duplikate zusammen, legen Sie Tags still, die niemand braucht.
- **Tags sind keine Schlüsselwörter für Suchmaschinen.** Sie sind für Ihre Leser da.

## Checkliste

- [ ] Breite Kategorien gegebenenfalls als Ordner festgelegt.
- [ ] Schlüsselwörter oder `eztags` gewählt.
- [ ] Eine schriftliche Tag-Liste mit Schreibweisen.
- [ ] Jeder neue Beitrag und jede neue Arbeit aus der Liste getaggt.
- [ ] Ein Prüftermin in Ihrem Kalender.

## Verwandte Seiten

- [Blogs](README.md)
- [Taggen mit eZ Tags](../../ersteller/website-ersteller/verschlagworten-mit-eztags.md)
- [Eine Bildergalerie aufbauen](../portfolios/eine-bildergalerie-aufbauen.md)
- [Inhalts-Audits](../../kunden/alltag/inhalts-audits.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
