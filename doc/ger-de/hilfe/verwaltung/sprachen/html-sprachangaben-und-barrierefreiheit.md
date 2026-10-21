---
title: HTML-Sprachangaben und Barrierefreiheit
audience: government, website-creator, admin
category: public-sector
order: 270
summary: Wie die Sprache einer Seite und von Seitenteilen auf einer Exponential-Website Browser und Screenreader erreicht (BCP-47-Angaben aus den Locale-Dateien, das lang-Attribut im Pagelayout, lang und dir an Inhalten in einer anderen Sprache), die betroffenen WCAG-Kriterien und was geprüft ist und was nicht.
keywords: lang-Attribut, Sprache der Seite, Sprache von Teilen, BCP 47, ContentLanguage, WCAG 3.1.1, WCAG 3.1.2, Screenreader, Barrierefreiheit, hreflang, Pagelayout, lang attribute, language of page, language of parts, screen reader, accessibility, pagelayout
type: help
---
# HTML-Sprachangaben und Barrierefreiheit

Diese Seite richtet sich an Websiteersteller und Administratoren. Screenreader wählen ihre Aussprache nach dem `lang`
der Seite und ihrer Teile; Suchmaschinen und Browser verwenden es ebenfalls. [WCAG 2.2](https://www.w3.org/TR/WCAG22/)
verlangt die Angabe der Sprache der Seite (3.1.1) und von Passagen in einer anderen Sprache (3.1.2).

## Inhalt

1. [Woher die Angabe kommt](#woher-die-angabe-kommt)
2. [Die Sprache der Seite](#die-sprache-der-seite)
3. [Teile in einer anderen Sprache](#teile-in-einer-anderen-sprache)
4. [Was geprüft ist und was nicht](#was-geprüft-ist-und-was-nicht)
5. [Prüfungen](#prüfungen)

## Woher die Angabe kommt

Pagelayouts geben `eZLocale::httpLocaleCode()` aus, also `[HTTP] ContentLanguage` aus der Locale-Datei des Siteaccess.
Seit 6.0.15 nennt jede mitgelieferte Locale eine gültige BCP-47-Angabe (`fr-FR`, `nb-NO`, `he-IL`), die frühere
nicht standardkonforme Werte wie `no-bokmaal` ersetzt
([Übersetzungen und Sprachen, HTML-Sprachangaben](../../../../eng-us/features/6.0/translations-and-languages.md#html-language-tags)).
Interne Locale-Codes (`fre-FR`) sind kein BCP 47; nur die HTTP-Angabe ist es
([Standards zur Internationalisierung](../../../../eng-us/standards/internationalization.md#language-tags-bcp-47-and-iso-639)).

## Die Sprache der Seite

In Ihrem öffentlichen Pagelayout:

```text
<html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
```

Die Pagelayouts der Administration tun das bereits ([Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md)).

## Teile in einer anderen Sprache

- **Inhalte, die in einer anderen Sprache als die Seite angezeigt werden** (eine Ersatzübersetzung, ein Zitat):
  Umschließen Sie sie mit einem Element mit eigenem `lang`; für Inhalte von rechts nach links gibt
  `design/standard/templates/parts/exp_content_direction.tpl` ` lang="..." dir="rtl"` aus.
- **Der Sprachumschalter**: Jeder Link trägt das `lang` seiner Sprache (siehe „Der Sprachumschalter“).
- **Wendungen innerhalb von Rich Text**: Redakteure können die Sprache einer Passage nur kennzeichnen, wenn Ihre
  Rich-Text-Einrichtung dafür eine Möglichkeit bietet; prüfen Sie die Editor-Konfiguration Ihrer Website, bevor Sie es
  Redakteuren zusagen.

## Was geprüft ist und was nicht

Die [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md) nennen den Stand des Projekts: WCAG 2.2 Stufe
AA **Partly complies** (teilweise erfüllt) für die Administration admin4 und admin4l; Designs öffentlicher Websites und
der Online-Editor sind nicht geprüft; EN 301 549 und Section 508 sind **Unverified** (nicht überprüft); es gibt weder
ein VPAT noch einen Accessibility Conformance Report. Die Konformität Ihres öffentlichen Designs müssen Sie selbst
testen ([Barrierefreiheit testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md)).

## Prüfungen

- [ ] Jeder Sprach-Siteaccess gibt das richtige `lang` an `<html>` aus.
- [ ] Inhalte in einer Ersatzsprache tragen ihr eigenes `lang`.
- [ ] Links des Sprachumschalters tragen `lang`.
- [ ] Ein Screenreader-Test einer Seite pro Sprache.
- [ ] `hreflang`-Links verwenden, wenn Sie sie hinzufügen, dieselben Angaben.

## Verwandte Seiten

- [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md)
- [Barrierefreiheit für Websiteersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)
- [Rechts-nach-links-Sprachen auf öffentlichen Websites](rechts-nach-links-sprachen-auf-oeffentlichen-websites.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
