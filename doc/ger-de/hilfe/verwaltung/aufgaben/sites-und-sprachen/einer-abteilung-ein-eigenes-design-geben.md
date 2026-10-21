---
title: Einer Abteilung ein eigenes Design geben
audience: government, website-creator, designer
category: public-sector
order: 150
summary: Aufgabe für Site-Ersteller - eine dünne Designschicht für eine Abteilungs-Site anlegen, die das gemeinsame Basisdesign nur wo nötig überschreibt, sie im Siteaccess setzen und die Barrierefreiheit testen.
keywords: Abteilungsdesign, Design-Erweiterung, Fallback-Kette, Template-Override, Markenauftritt, department design, SiteDesign, AdditionalSiteDesignList, design extension, fallback chain, template override, branding
type: help
---
# Einer Abteilung ein eigenes Design geben

Diese Aufgabe richtet sich an Site-Ersteller. Hintergrund: [Designs je Site](../../aufbauen/multisite/designs-je-site.md)
und [Designs und die Fallback-Kette](../../../../../eng-us/help/creators/website-creators/designs-and-the-fallback-chain.md).

## Bevor Sie beginnen

Ein gemeinsames Basisdesign (zum Beispiel `gov_base`) existiert in einer eigenen Erweiterung.

## Schritte

1. Legen Sie eine Erweiterung `site_dept_example` mit `design/dept_example/` an und registrieren Sie sie als
   Design-Erweiterung.
2. Legen Sie nur hinein, was sich unterscheidet: das Logo, eine Akzentfarbe im Stylesheet, ein Template für die
   Fußzeile.
3. Im Siteaccess der Abteilung:

   ```ini
   [DesignSettings]
   SiteDesign=dept_example
   AdditionalSiteDesignList[]
   AdditionalSiteDesignList[]=gov_base
   AdditionalSiteDesignList[]=simple
   ```

4. Leeren Sie den INI-, den Template- und den Template-Override-Cache
   (`php bin/php/ezcache.php --clear-tag=ini,template`) und den Inhalts-Cache, wenn Seiten veraltet aussehen.

## Prüfen

- Die Abteilungs-Site zeigt ihr Logo und ihre Farbe; jeder andere Teil der Seite kommt aus dem Basisdesign.
- Der Kontrast der Akzentfarbe, die Fokus-Stile und die Tastaturnavigation bestehen weiterhin Ihre Prüfungen
  ([Barrierefreiheit testen](../../../../../eng-us/help/creators/website-creators/testing-accessibility.md)).

## Probleme

| Problem | Lösung |
|---|---|
| Ein neues Template wird nicht verwendet | leeren Sie den Template-Override-Cache; prüfen Sie, dass der Dateipfad zum Namen des Designs passt |
| Andere Sites haben sich ebenfalls geändert | die Änderung wurde im Basisdesign statt in der Abteilungsschicht gemacht |

## Verwandte Seiten

- [Template-Overrides](../../../../../eng-us/help/creators/website-creators/template-overrides.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
