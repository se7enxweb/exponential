---
title: Wie nehme ich eine Seite in die Sitemap auf oder aus ihr heraus?
audience: editor, admin
category: manual
order: 385
summary: Veröffentlichte Seiten, die Besucher lesen dürfen, kommen beim nächsten Sitemap-Cronjob in die XML-Sitemap; entfernen Sie in den Metadaten der Seite das Häkchen bei Use in sitemap, um eine auszulassen, oder verbergen Sie sie; Administratoren legen die Klassen und Teilbäume fest.
keywords: Sitemap, XML-Sitemap, bcgooglesitemaps, in Sitemap verwenden, Priorität, Änderungshäufigkeit, Suchmaschinen, robots.txt, sitemap, XML sitemap, use in sitemap, priority, change frequency, search engines
type: help
---
# Wie nehme ich eine Seite in die Sitemap auf oder aus ihr heraus?

Die XML-Sitemap teilt Suchmaschinen mit, welche Seiten es gibt. Sie wird von einem Cronjob geschrieben, aus den
veröffentlichten Seiten, die Besucher lesen dürfen.

## Wer das tun kann

Redakteure, die die Seite bearbeiten dürfen, entscheiden für eine einzelne Seite. Administratoren entscheiden, welche
Klassen und Teile des Baums aufgenommen werden (die Einstellungen von bcgooglesitemaps und xrowmetadata).

## Schritte

1. **Um eine Seite aufzunehmen:** Veröffentlichen Sie sie in einem Teil des Baums, den Besucher lesen dürfen. Sie steht
   nach dem nächsten Sitemap-Cronjob in der Sitemap, meist über Nacht.
2. **Um eine Seite auszulassen:** Bearbeiten Sie sie, öffnen Sie das Metadatenfeld und entfernen Sie das Häkchen bei
   **Use in sitemap**. Klicken Sie auf **Zur Veröffentlichung schicken**.
3. **Um eine Seite zu gewichten:** Setzen Sie im selben Feld **Priority** und **Change frequence**.
4. **Um einen ganzen Teil des Baums oder eine Klasse auszulassen:** Bitten Sie einen Administrator, sie in den
   Sitemap-Einstellungen auszuschließen.

## Was schiefgehen kann

- **Eine neue Seite steht nicht in der Sitemap.** Der Cronjob ist noch nicht gelaufen, oder die Klasse ist
  ausgeschlossen.
- **Eine ausgelassene Seite erscheint trotzdem in Suchergebnissen.** Die Sitemap lädt Suchmaschinen nur ein. Um eine
  Seite aus den Ergebnissen herauszuhalten, verbergen Sie sie oder fragen Sie einen Administrator nach `noindex`.

## Verwandte Seiten

- [Wie setze ich SEO-Titel und -Beschreibung einer Seite?](wie-setze-ich-seo-titel-und-beschreibung.md)
- [bcgooglesitemaps](../../../eng-us/features/6.0/extensions/bcgooglesitemaps.md)
- [Sitemaps](../../../eng-us/help/creators/website-creators/sitemaps.md)
- [robots.txt](../../../eng-us/help/creators/website-creators/robots-txt.md)
- [Benutzerhandbuch](README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
