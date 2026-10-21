---
title: Sprache der Seite und ihrer Teile
audience: government, developer, editor, admin
category: public-sector
order: 270
summary: Für öffentliche Stellen mit mehrsprachigen Websites - wie Exponential die Sprache von Administrations- und öffentlichen Seiten setzt, wie Übersetzungen und Sprach-Siteaccesses jeder Seite ihre eigene Sprache geben und wie die Redaktion Passagen in einer anderen Sprache kennzeichnet (WCAG 3.1.1 und 3.1.2).
keywords: Sprache, lang-Attribut, Sprache der Seite, Sprache von Teilen, Übersetzungen, Siteaccess, Locale, mehrsprachig, Stimme des Screenreaders, Inhaltssprachen, Minderheitensprachen, language, lang attribute, language of page, language of parts, translations, siteaccess, locale, multilingual, WCAG 3.1.1, 3.1.2, screen reader voice, content languages, minority languages
type: help
---
# Sprache der Seite und ihrer Teile

Diese Seite richtet sich an die Entwicklung, die das Pagelayout baut, an die Administration, die Sprachen einrichtet,
und an die Redaktion, die in mehr als einer Sprache schreibt. Screenreader wählen ihre Stimme und Aussprache anhand des
Attributs `lang`: Eine Seite in der falschen Sprache wird als Unsinn vorgelesen. WCAG 2.2 verlangt die Angabe der
Sprache der Seite (3.1.1, Stufe A) und von Passagen in einer anderen Sprache (3.1.2, Stufe AA)
(<https://www.w3.org/TR/WCAG22/>).

## Inhalt

1. [In der Administration](#in-der-administration)
2. [Auf der öffentlichen Website](#auf-der-öffentlichen-website)
3. [Passagen in einer anderen Sprache](#passagen-in-einer-anderen-sprache)
4. [Prüfen](#prüfen)
5. [Verwandte Seiten](#verwandte-seiten)

## In der Administration

Jedes Pagelayout von admin4 schreibt `<html lang="...">` aus dem Locale des Siteaccess
(`design/admin4/templates/pagelayout.tpl`; [Standards zur Barrierefreiheit](../../../standards/barrierefreiheit.md#wcag-22)).
Benutzer, die die Sprache der Oberfläche wechseln, erhalten das passende `lang` und, für Sprachen von rechts nach links,
`dir="rtl"`.

## Auf der öffentlichen Website

- Jede Sprache einer Website ist meist ein eigener Siteaccess mit eigenem Locale, und jedes Inhaltsobjekt hat eine
  Übersetzung je Sprache ([Inhaltssprachen](../../../anleitungen/inhaltssprachen.md),
  [Mehrsprachige Websites](../../ersteller/website-ersteller/mehrsprachige-websites.md)).
- Ihr Pagelayout schreibt `<html lang>` aus dem Siteaccess, zum Beispiel aus `$site.http_equiv.Content-language`
  ([Barrierefreiheit für Website-Ersteller](../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md)).
- Fehlt eine Übersetzung und zeigt die Website den Inhalt in einer anderen Sprache (ein Fallback, der durch die
  Sprachliste der Website konfiguriert ist), ist dieser Inhalt in einer anderen Sprache als die Seite: Kennzeichnen Sie
  ihn, oder zeigen Sie einen Hinweis und verlinken Sie auf die vorhandene Fassung.
- Sprachumschalter nennen jede Sprache in dieser Sprache („Deutsch“, „Français“) und tragen an jedem Eintrag `lang`
  ([Sprachumschalter und Sprach-URLs](../../ersteller/website-ersteller/sprachumschalter-und-sprach-urls.md)).

## Passagen in einer anderen Sprache

- Eine ganze Seite in einer anderen Sprache gehört in diese Übersetzung, nicht in die Standardsprache.
- Kurze Passagen (ein Zitat, der Titel eines ausländischen Gesetzes) brauchen `lang` am Element. Ob die Redaktion es
  setzen kann, hängt von der Editor-Engine und den Custom Tags ab, die Ihre Website anbietet; die Entwicklung kann in
  Templates, die Inhalte aus einer anderen Sprache zeigen, `parts/exp_content_direction.tpl` einbinden, das `lang` und,
  für Sprachen von rechts nach links, `dir` schreibt
  ([Sprachen von rechts nach links](../../../funktionen/6.0/rechts-nach-links.md#inhalte)).
- Eigennamen und Fachbegriffe müssen nicht gekennzeichnet werden.

## Prüfen

- Sehen Sie sich `<html lang>` auf einer Seite je Siteaccess an.
- Öffnen Sie mit einem Screenreader eine Seite in jeder Sprache und hören Sie zu: Die Stimme muss passen.
- Prüfen Sie Fallback-Inhalte und Sprachumschalter.

## Verwandte Seiten

- [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)
- [Sprachen](../../sprachen.md)
- [Barrierefreiheit von rechts nach links](barrierefreiheit-von-rechts-nach-links.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
