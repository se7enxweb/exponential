---
title: Ein Design pro Marke
audience: businesses, sales-marketing, website-creator
category: business-solutions
order: 40
summary: Wie jede Marke in einer Exponential-Installation ihr eigenes Erscheinungsbild behält - ein Design pro Siteaccess, die Design-Fallback-Kette, ein mitgeliefertes Theme sicher anpassen, Namen, Logos und Meta-Tags pro Marke und was Sie budgetieren sollten.
keywords: Design, Theme, Marke, Design pro Marke, Fallback-Kette, Logo, Favicon, Unternehmen, design, theme, brand, per-brand design, SiteDesign, AdditionalSiteDesignList, fallback chain, logo, favicon, SiteName, sevenx_themes_simple, business
type: help
---
# Ein Design pro Marke

Diese Seite richtet sich an Marken- und Marketingverantwortliche sowie an die Führungskräfte, die die Designarbeit
budgetieren, wenn mehrere Marken in einer Exponential-Installation laufen. Sie erklärt, wie jede Marke ihr eigenes
Erscheinungsbild bekommt, während die Marken den darunterliegenden Code teilen, welche Teile eine Marke ohne Entwickler
ändern kann und wie Sie Designs über Jahre von Updates wartbar halten.

## Inhalt

1. [Wie eine Marke ihr eigenes Erscheinungsbild bekommt](#wie-eine-marke-ihr-eigenes-erscheinungsbild-bekommt)
2. [Die Fallback-Kette in geschäftlichen Begriffen](#die-fallback-kette-in-geschäftlichen-begriffen)
3. [Drei Wege zum Design einer Marke](#drei-wege-zum-design-einer-marke)
4. [Was Redakteure ohne Entwickler ändern können](#was-redakteure-ohne-entwickler-ändern-können)
5. [Designs sicher durch Updates bringen](#designs-sicher-durch-updates-bringen)
6. [Checkliste für das Markendesign](#checkliste-für-das-markendesign)

## Wie eine Marke ihr eigenes Erscheinungsbild bekommt

Jeder Siteaccess nennt sein Design in `site.ini [DesignSettings]`: ein `SiteDesign`, das zuerst durchsucht wird, dann
die Designs in `AdditionalSiteDesignList[]`, dann das Design `standard`
([Ein Design oder Theme wählen](../../../../eng-us/help/creators/website-creators/choosing-a-design-or-theme.md#pointing-a-siteaccess-at-a-design)).
Zwei Marken sind daher einfach zwei Siteaccesses mit unterschiedlichen Designnamen. Sie können dieselben Inhalte völlig
unterschiedlich darstellen oder jeweils ihren eigenen Ordner zeigen.

## Die Fallback-Kette in geschäftlichen Begriffen

Für jedes Template, das eine Seite braucht, verwendet Exponential den ersten Treffer in einer festen Reihenfolge:
Override-Templates für die Seite, das eigene Design der Marke, jedes zusätzliche Design und schließlich das
Standard-Design
([Designs, Siteaccesses und die Design-Fallback-Kette](../../../../eng-us/help/creators/website-creators/designs-and-the-fallback-chain.md#the-chain)).

Was das für Ihr Budget bedeutet:

| Situation | Wirkung |
|---|---|
| Marke B unterscheidet sich von Marke A nur in Farben, Logo und einigen Templates | Das Design von Marke B enthält nur diese Dateien; alles andere fällt auf das Design von Marke A oder das gemeinsame Theme zurück |
| Jede Marke ist optisch eigenständig | Jede Marke hat ihr eigenes Pagelayout und eigene Templates; gemeinsame Teile können trotzdem in einem gemeinsamen Design liegen, das dahinter aufgeführt ist |
| Eine neue Marke ist eine Variante einer bestehenden | Beginnen Sie mit dem bestehenden Design als Fallback und überschreiben Sie nur, was sich unterscheidet |

Kleinere Designs pro Marke bedeuten weniger Code, der bei jedem Update getestet werden muss.

## Drei Wege zum Design einer Marke

Aus [Ein Design oder Theme wählen](../../../../eng-us/help/creators/website-creators/choosing-a-design-or-theme.md#three-ways):

| Weg | Aufwand | Upgrades | Wählen Sie ihn, wenn |
|---|---|---|---|
| Ein mitgeliefertes Theme unverändert nutzen | am geringsten: nur Einstellungen | Sie erhalten die Updates des Themes mit Composer | Aussehen und Klassen des Themes passen |
| Ein Theme in Ihrer eigenen Erweiterung anpassen | mittel: Overrides der Templates, die Sie ändern | das Theme wird darunter aktualisiert; Ihre Overrides bleiben | Sie die Struktur des Themes mit Ihrer Marke wollen |
| Ein eigenes Design bauen | am größten: jedes Template, das Sie brauchen | nur Ihr eigener Code | das Erscheinungsbild speziell ist oder Sie auf dem Layout-Editor aufbauen |

Das öffentliche Standard-Design, `sevenx_themes_simple`, wird als „das leichte, responsive öffentliche Design der
Standardinstallation“ beschrieben ([sevenx_themes_simple](../../../../eng-us/features/6.0/extensions/sevenx_themes_simple.md)).
Dieselbe Seite führt die anderen mitgelieferten Themes auf.

> **Tipp**
> Wenn sich Marken vor allem im Seitenaufbau unterscheiden, sehen Sie sich Exponential Layouts an: Redakteure setzen
> Seiten aus Blöcken zusammen, und gemeinsame Layouts tragen gemeinsame Teile wie die Fußzeile
> ([Landingpages mit dem Layout-Editor](../../../../eng-us/help/creators/website-creators/landing-pages-with-layouts.md)).

## Was Redakteure ohne Entwickler ändern können

In den auf ezwebin basierenden Designs, einschließlich des mitgelieferten Simple-Themes:

- **Das Logo** stammt aus einem Seitendesign-Objekt; Redakteure ändern es, indem sie dieses Objekt in der
  Administration bearbeiten, einschließlich seines Alternativtexts
  ([Branding: Website-Name, Logo, Favicon und Meta-Tags](../../../../eng-us/help/creators/website-creators/branding-your-site.md#the-logo-and-the-page-design-object)).
- **Der Hinweis „Powered by Exponential“** in der Fußzeile wird über ein Attribut desselben Objekts ausgeblendet, ohne
  Änderung am Template.

Was pro Siteaccess einer Marke einen Entwickler oder Administrator braucht:

- `SiteName`, `SiteURL` und `SiteDescription` in `site.ini [SiteSettings]`, die für Titel, Betreffzeilen von Mails,
  Feeds, Sitemaps und das Meta-Tag „description“ verwendet werden.
- Favicons und das Web-Manifest, die im Verzeichnis `images/favicon/` des Designs liegen.
- Stylesheets, Seiten-Templates und das Pagelayout.

## Designs sicher durch Updates bringen

- **Bearbeiten Sie ein mit Composer installiertes Theme nie direkt**: Es wird bei jedem Update ersetzt. Legen Sie
  Änderungen in Ihre eigene Design-Erweiterung, die vor dem Theme aufgeführt ist
  ([Ein Theme sicher anpassen](../../../../eng-us/help/creators/website-creators/choosing-a-design-or-theme.md#adapting-a-theme-the-safe-way)).
- **Halten Sie das Design jeder Marke in der Versionsverwaltung**, in einer eigenen Erweiterung. Eine Marke, die in
  ihrer eigenen Erweiterung lebt, kann auch ihre eigenen Einstellungen mitbringen
  ([Einstellungen pro Website innerhalb von Erweiterungen](../../../../eng-us/features/6.0/multi-site-ini-overrides.md)).
- **Prüfen Sie Designs auf Rechts-nach-links-Schrift und Barrierefreiheit**, wenn eine Marke solche Märkte bedient
  ([Websites mit Rechts-nach-links-Schrift](../../../../eng-us/help/creators/website-creators/right-to-left-sites.md),
  [Barrierefreiheit für Website-Ersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)).
- **Testen Sie nach einem Update jede Marke**, nicht nur die Hauptmarke. Eine Installation bedeutet ein Update für
  alle Marken.

## Checkliste für das Markendesign

- [ ] Der Siteaccess jeder Marke nennt sein Design, mit einem gemeinsamen Fallback, wo sich Marken überschneiden.
- [ ] Website-Name, Adresse und Beschreibung sind pro Marke gesetzt.
- [ ] Logo, Favicon und Bild für das Teilen in sozialen Netzwerken gibt es pro Marke.
- [ ] Keine Theme-Datei wurde direkt bearbeitet; Änderungen liegen in Ihrer eigenen Design-Erweiterung.
- [ ] Das Design jeder Marke liegt in der Versionsverwaltung und hat einen namentlich benannten Verantwortlichen.
- [ ] Jede Marke wurde auf einem Telefon und einem Desktop geprüft ([Responsive Websites](../mobil-und-headless/responsive-websites.md)).
- [ ] Jede Marke besteht Ihre Prüfung der Barrierefreiheit.

## Verwandte Seiten

- [Inhalte zwischen Marken teilen](inhalte-zwischen-marken-teilen.md)
- [Designs, Siteaccesses und die Design-Fallback-Kette](../../../../eng-us/help/creators/website-creators/designs-and-the-fallback-chain.md)
- [Ein Design oder Theme wählen](../../../../eng-us/help/creators/website-creators/choosing-a-design-or-theme.md)
- [Branding: Website-Name, Logo, Favicon und Meta-Tags](../../../../eng-us/help/creators/website-creators/branding-your-site.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
