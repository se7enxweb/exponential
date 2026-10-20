---
title: "Designer: hier beginnen"
audience: designer
category: theme-design
order: 1
summary: Die Einstiegsseite für Designer - was das Designsystem von Exponential ist, ein Plan für die erste Woche, die sechs Bereiche der Designer-Hilfe (Theme-Design, Templates, Theme-Erweiterungen, Veröffentlichung, Verkauf, Wartung), die Tutorials und Checklisten und die Regeln, die jedes Design befolgt.
keywords: Designer, hier beginnen, Theme-Designer, Frontend, Webdesigner, Designsystem, Themes, Templates, Einarbeitung, erste Woche, Einstiegsseite, designers, start here, front-end, design system, onboarding
featured: true
type: help
---
# Designer: hier beginnen

Diese Seite ist für Designer und Frontend-Entwickler, die mit Exponential das Aussehen von Websites gestalten: neue
Themes, Änderungen an bestehenden, Landingpages, die Gestaltung der Administration und Themes, die Sie veröffentlichen
oder verkaufen. Sie beantwortet, was Sie zuerst wissen müssen, was Sie in Ihrer ersten Woche tun und wo jedes Thema in
dieser Hilfe steht.

> **Hinweis**
> Exponential ist das quelloffene Content-Management-System und Framework, auf dem diese Administration läuft. Es führt
> die Codebasis von eZ Publish fort, einem Produkt, das sein Hersteller nach fast 18 Jahren eingestellt hat, und wird
> von **7x** gemeinsam mit der **Exponential Foundation** geführt ([Wer ist 7x?](../wer-ist-7x.md), [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)).

## Inhalt

1. [Das Designsystem in fünf Sätzen](#das-designsystem-in-fünf-sätzen)
2. [Ihre erste Woche](#ihre-erste-woche)
3. [Die sechs Bereiche](#die-sechs-bereiche)
4. [Tutorials](#tutorials)
5. [Checklisten](#checklisten)
6. [Regeln, die jedes Design befolgt](#regeln-die-jedes-design-befolgt)
7. [Alle Seiten](#alle-seiten)

## Das Designsystem in fünf Sätzen

1. Ein **Design** ist ein benannter Ordner mit Templates, Stylesheets, Skripten und Bildern, meist innerhalb einer
   Erweiterung ([Was ein Design ist](../../../eng-us/help/designers/theme-design/what-a-design-is.md)).
2. Ein **Siteaccess** (ein Zugang zur Installation) nennt seine Designs, und der Kernel durchsucht sie der Reihe nach,
   die **Design-Kaskade**, und nimmt die erste Datei, die er findet ([Die Design-Kaskade](../../../eng-us/help/designers/theme-design/the-design-cascade.md)).
3. Das **Pagelayout** rahmt jede Seite um das Ergebnis des Moduls ein ([Das Pagelayout](../../../eng-us/help/designers/theme-design/the-pagelayout.md)).
4. **Templates** in der Sprache TPL stellen Inhalte dar, und **Overrides** wählen ein Template je Klasse, Knoten oder
   Sektion ([Templates im Überblick](../../../eng-us/help/designers/templates/templates-overview.md), [Override-Grundlagen](../../../eng-us/help/designers/templates/override-ini-basics.md)).
5. Stylesheets und Skripte erreichen den Browser von ezjscore **gepackt**, ohne Inline-Code
   ([Stylesheets laden](../../../eng-us/help/designers/theme-design/loading-stylesheets.md), [CSP-sicheres Design](../../../eng-us/help/designers/theme-design/csp-safe-design.md)).

## Ihre erste Woche

### Tag 1: sich umsehen

- [ ] Lesen Sie [Was ein Design ist](../../../eng-us/help/designers/theme-design/what-a-design-is.md) und [Designs und Siteaccesses](../../../eng-us/help/designers/theme-design/designs-and-siteaccesses.md).
- [ ] Sehen Sie sich [Die Themes, die mit Exponential kommen](../../../eng-us/help/designers/theme-design/themes-on-alpha.md) an; öffnen Sie die `pagelayout.tpl` eines Themes.

### Tag 2: die Template-Sprache

- [ ] [TPL-Syntax: Grundlagen](../../../eng-us/help/designers/templates/tpl-syntax-basics.md), [Variablen und def](../../../eng-us/help/designers/templates/variables-and-def.md),
      [Foreach-Schleifen](../../../eng-us/help/designers/templates/foreach-loops.md), [Wash und Maskierung](../../../eng-us/help/designers/templates/wash-and-escaping.md).

### Tag 3: Ihr eigenes Theme

- [ ] Folgen Sie auf einer Entwicklungsinstallation dem [Tutorial: eine Theme-Erweiterung anlegen](../../../eng-us/help/designers/theme-design/tutorial-create-a-theme-extension.md).

### Tag 4: Inhalte und Overrides

- [ ] Rufen Sie eine Liste ab ([Fetch-Grundlagen](../../../eng-us/help/designers/templates/fetch-basics.md)) und überschreiben Sie eine Vollansicht
      ([Tutorial: eine Vollansicht überschreiben](../../../eng-us/help/designers/theme-design/tutorial-override-a-full-view.md)).

### Tag 5: Qualität

- [ ] Fügen Sie den Dunkelmodus hinzu ([Tutorial: Dunkelmodus hinzufügen](../../../eng-us/help/designers/theme-design/tutorial-add-dark-mode.md)) und gehen
      Sie Ihre Seiten mit der [Checkliste Barrierefreiheit](../../../eng-us/help/designers/theme-design/checklist-accessibility.md) durch.

## Die sechs Bereiche

| Bereich | Was er abdeckt | Beginnen Sie mit |
|---|---|---|
| Theme-Design | Designs, Kaskade, Pagelayout, CSS und JS, Bilder und Schriften, Responsive Design, Barrierefreiheit, Dunkelmodus, rechts nach links, CSP, Leistung, Layout-Editor | [Die Design-Kaskade](../../../eng-us/help/designers/theme-design/the-design-cascade.md) |
| Templates | die Sprache TPL, Operatoren und Funktionen, Inhalte abrufen, Ansichtsmodi, Overrides, Debugging | [Templates im Überblick](../../../eng-us/help/designers/templates/templates-overview.md) |
| Theme-Erweiterungen | Metadaten, Einstellungen, Paketierung, die RAD-Assistenten für Designs und Pakete | [Theme-Erweiterungen: hier beginnen](erweiterungen/theme-erweiterungen-hier-beginnen.md) |
| Veröffentlichung | Repositories, Versionen, Releases, Packagist, Lizenzen, Veröffentlichen | [Ein Theme veröffentlichen: hier beginnen](veroeffentlichung/ein-theme-veroeffentlichen-hier-beginnen.md) |
| Verkauf | Themes und Dienstleistungen verkaufen | [Themes und Dienstleistungen verkaufen: hier beginnen](../../../eng-us/help/designers/sales/selling-start-here.md) |
| Lebenszyklus der Wartung | Kompatibilität, Upgrades, Sicherheit, Abkündigung, Lebensende, Forks | [Der Lebenszyklus der Wartung: hier beginnen](../../../eng-us/help/designers/lifecycle/lifecycle-start-here.md) |

## Tutorials

| Tutorial | Dauer |
|---|---|
| [Eine Theme-Erweiterung anlegen](../../../eng-us/help/designers/theme-design/tutorial-create-a-theme-extension.md) | etwa zwei Stunden |
| [Eine Vollansicht überschreiben](../../../eng-us/help/designers/theme-design/tutorial-override-a-full-view.md) | 30 Minuten |
| [Eine Landingpage mit Layouts bauen](../../../eng-us/help/designers/theme-design/tutorial-build-a-landing-page-with-layouts.md) | ein bis zwei Stunden |
| [Dunkelmodus hinzufügen](../../../eng-us/help/designers/theme-design/tutorial-add-dark-mode.md) | zwei bis vier Stunden |
| [Ein Theme internationalisieren](../../../eng-us/help/designers/theme-design/tutorial-internationalize-a-theme.md) | ein bis zwei Stunden |
| [Ein Rechts-nach-links-Theme](../../../eng-us/help/designers/theme-design/tutorial-right-to-left-theme.md) | einen halben Tag |
| [Ein statisches HTML-Design portieren](../../../eng-us/help/designers/theme-design/tutorial-port-a-static-html-design.md) | etwa einen Tag |
| [Zwei Websites, zwei Designs](../../../eng-us/help/designers/theme-design/tutorial-two-sites-two-designs.md) | zwei Stunden |
| [Ein Admin-Modul-Stylesheet hinzufügen](../../../eng-us/help/designers/theme-design/tutorial-add-an-admin-module-stylesheet.md) | eine Stunde |

## Checklisten

[Theme-Start](../../../eng-us/help/designers/theme-design/checklist-theme-launch.md) ·
[Barrierefreiheit](../../../eng-us/help/designers/theme-design/checklist-accessibility.md) ·
[Leistung](../../../eng-us/help/designers/theme-design/checklist-performance.md) ·
[CSP](../../../eng-us/help/designers/theme-design/checklist-csp.md) ·
[Dunkelmodus](../../../eng-us/help/designers/theme-design/checklist-dark-mode.md) ·
[Rechts nach links](../../../eng-us/help/designers/theme-design/checklist-right-to-left.md) ·
[Code-Review für Templates](../../../eng-us/help/designers/theme-design/checklist-code-review-for-templates.md) ·
[Die Theme-Erweiterung ist bereit](erweiterungen/checkliste-theme-erweiterung-bereit.md)

## Regeln, die jedes Design befolgt

- Ihr Design liegt in Ihrer eigenen Erweiterung; mitgelieferte Designs werden nie bearbeitet, nur überschrieben.
- Kein `<style>`, kein `style=""`, keine Inline-Skripte und keine `on*=`-Handler ([Kein Inline-CSS](../../../eng-us/help/designers/theme-design/no-inline-css.md),
  [Kein Inline-JavaScript](../../../eng-us/help/designers/theme-design/no-inline-javascript.md)).
- Farben, Abstände und Schriften kommen aus Tokens, mit einem dunklen Wert für jede Farbe ([Design-Tokens](../../../eng-us/help/designers/theme-design/design-tokens.md)).
- Jeder ausgegebene Wert wird mit wash behandelt, jeder Oberflächentext läuft durch `i18n`.
- Getestet im hellen und dunklen Modus, bei 1440 px, bei 960 px mit Skalierung 2 (200 % Zoom) und bei 375 px, mit der Tastatur.
- Keine `?v=`-Versionsparameter an Asset-Links ([Keine Versionsparameter an Asset-Links](../../../eng-us/help/designers/theme-design/no-version-queries-on-asset-links.md)).

## Alle Seiten

Der Index aller Designer-Seiten, Bereich für Bereich: [Designer-Hilfe, alle Seiten](../../../eng-us/help/designers/README.md). Die Begriffe erklärt:
[Design-Glossar](../../../eng-us/help/designers/theme-design/design-glossary.md).

## Verwandte Seiten

- [Erste Schritte](../erste-schritte.md)
- [Der Layout-Editor](../layout-editor.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
