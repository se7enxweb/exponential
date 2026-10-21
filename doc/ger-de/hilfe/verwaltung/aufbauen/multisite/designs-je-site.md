---
title: Designs je Site
audience: government, website-creator, designer
category: public-sector
order: 40
summary: Wie jede Abteilungs-Site einer Multisite-Installation von Exponential ein eigenes Erscheinungsbild erhält und dabei eine gemeinsame Basis teilt - SiteDesign und die Fallback-Kette der Designs, ein gemeinsames Basisdesign der Verwaltung, Overrides je Abteilung und einheitliche Barrierefreiheit.
keywords: Design, Fallback-Kette, Design-Erweiterung, Markenauftritt, Erscheinungsbild der Abteilung, Template-Override, gemeinsame Kopfzeile, Barrierefreiheit, design, SiteDesign, AdditionalSiteDesignList, fallback chain, design extension, branding, department branding, template override, shared header, accessibility
type: help
---
# Designs je Site

Diese Seite richtet sich an Site-Ersteller und Designer. Jeder Siteaccess nennt sein Design in
`[DesignSettings] SiteDesign`, mit `AdditionalSiteDesignList[]` als Fallbacks; Templates werden gefunden, indem diese
Kette durchlaufen wird
([Designs und die Fallback-Kette](../../../../../eng-us/help/creators/website-creators/designs-and-the-fallback-chain.md)).

## Inhalt

1. [Eine gemeinsame Basis, eine dünne Schicht je Abteilung](#eine-gemeinsame-basis-eine-dünne-schicht-je-abteilung)
2. [Einstellungen](#einstellungen)
3. [Was eine Abteilungsschicht meist ändert](#was-eine-abteilungsschicht-meist-ändert)
4. [Einheitlichkeit und Barrierefreiheit](#einheitlichkeit-und-barrierefreiheit)

## Eine gemeinsame Basis, eine dünne Schicht je Abteilung

Behörden haben oft ein Erscheinungsbild mit Varianten je Abteilung. Bauen Sie ein Basisdesign, das das Pagelayout, die
Komponenten und die Arbeit an der Barrierefreiheit enthält, und ein dünnes Design je Abteilung, das nur überschreibt,
was sich unterscheidet.

## Einstellungen

```ini
# settings/siteaccess/dept_example/site.ini.append.php
[DesignSettings]
SiteDesign=dept_example
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=gov_base
AdditionalSiteDesignList[]=simple
```

Halten Sie jedes Design in einer eigenen Erweiterung, damit es mit Git versioniert und ausgerollt wird
([Template-Overrides](../../../../../eng-us/help/creators/website-creators/template-overrides.md)).

## Was eine Abteilungsschicht meist ändert

- Das Logo und den Namen in der Kopfzeile (oft Inhalte: Eine gemeinsame Kopfzeile kann als Inhalt gepflegt werden,
  [Gemeinsame Kopf- und Fußzeile](../../../../../eng-us/help/creators/website-creators/shared-header-and-footer.md)).
- Eine Akzentfarbe, über die Tokens im Stylesheet des Designs.
- Die Kontakte in der Fußzeile.
- Templates für eine Klasse, die nur diese Abteilung verwendet.

## Einheitlichkeit und Barrierefreiheit

- Halten Sie Navigation, Suche und Sprachumschalter auf jeder Site an derselben Stelle.
- Die Arbeit an der Barrierefreiheit im Basisdesign gilt für jede Abteilung; Overrides dürfen sie nicht rückgängig
  machen (Kontrast von Akzentfarben, Fokus-Stile). Testen Sie jede Abteilungs-Site
  ([Barrierefreiheit testen](../../../../../eng-us/help/creators/website-creators/testing-accessibility.md)).
- Die mitgelieferten öffentlichen Designs sind nicht auf Barrierefreiheit geprüft
  ([Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md)).

## Verwandte Seiten

- [Ihre Site mit Ihrer Marke versehen](../../../../../eng-us/help/creators/website-creators/branding-your-site.md)
- [Ein Design oder Theme wählen](../../../../../eng-us/help/creators/website-creators/choosing-a-design-or-theme.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
