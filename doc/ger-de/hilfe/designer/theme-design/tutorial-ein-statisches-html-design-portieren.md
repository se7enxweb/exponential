---
title: "Tutorial: ein statisches HTML-Design portieren"
audience: designer
category: theme-design
order: 830
summary: Ein statisches HTML/CSS-Mockup oder die Templates einer Agentur in ein Exponential-Design umwandeln: die Seite in Pagelayout und Teile aufteilen, Assets in die Design-Ordner verschieben, feste Texte und Links durch Template-Code ersetzen, Seitentypen auf View-Templates abbilden und unterwegs Inline-Code entfernen.
keywords: Tutorial, HTML portieren, statisches HTML, Mockup zum Theme, HTML-Template umwandeln, Agentur-Design, HTML zu tpl, Design-Portierung, Pagelayout aus HTML, Seitentypen, tutorial, port html, static html, mockup to theme, convert html template, agency design, html to tpl, design port, pagelayout from html, page types
type: help
---
# Tutorial: ein statisches HTML-Design portieren

Dieses Tutorial ist für Designer, die statische HTML-Seiten bekommen haben (von einer Agentur, aus einem Styleguide oder einem gekauften
Template) und sie in ein funktionierendes Exponential-Design umwandeln müssen. Planen Sie für eine typische Website mit fünf Seitentypen einen
Tag ein.

## Inhalt

1. [Schritt 1: Bestandsaufnahme](#schritt-1-bestandsaufnahme)
2. [Schritt 2: Assets ins Design](#schritt-2-assets-ins-design)
3. [Schritt 3: Der Rahmen wird zum Pagelayout](#schritt-3-der-rahmen-wird-zum-pagelayout)
4. [Schritt 4: Seitentypen werden zu View-Templates](#schritt-4-seitentypen-werden-zu-view-templates)
5. [Schritt 5: Fester Inhalt wird zu Template-Code](#schritt-5-fester-inhalt-wird-zu-template-code)
6. [Schritt 6: unterwegs aufräumen](#schritt-6-unterwegs-aufräumen)

## Schritt 1: Bestandsaufnahme

Listen Sie die Seitentypen des Mockups auf und ordnen Sie jeden Exponential zu:

| Seite des Mockups | Wird zu |
|---|---|
| Rahmen (Kopfbereich, Menü, Fußbereich) | `pagelayout.tpl` und Teile |
| Startseite | Vollansicht der Klasse der Startseite oder ein Layout ([Layouts für Designer](layouts-fuer-designer.md)) |
| Artikel | Override der Vollansicht für die Klasse `article` |
| Liste von Artikeln | Vollansicht von `folder` mit einer line-Ansicht pro Eintrag |
| Kontaktformular | Vollansicht der Klasse des Feedbackformulars |
| Suchergebnisse | `content/search.tpl` |

## Schritt 2: Assets ins Design

```
mockup/css/*.css     ->  design/mytheme/stylesheets/
mockup/js/*.js       ->  design/mytheme/javascript/
mockup/img/*         ->  design/mytheme/images/
mockup/fonts/*       ->  design/mytheme/fonts/
```

Korrigieren Sie relative `url()`-Pfade im CSS (`../images/...`, `../fonts/...`). Laden Sie das CSS mit einem `ezcss_load` und die Skripte mit
`ezscript_load` ([Stylesheets laden](stylesheets-laden.md)). Ersetzen Sie einen CDN-Link zu einer Bibliothek durch eine lokale Kopie
([Externe Ressourcen und CDNs](externe-ressourcen-und-cdns.md)).

## Schritt 3: Der Rahmen wird zum Pagelayout

Nehmen Sie den gemeinsamen Rahmen des Mockups; ersetzen Sie den Inhaltsbereich durch `{$module_result.content}`; teilen Sie Kopfbereich,
Fußbereich und Menü in Teile `pagelayout/*.tpl` auf ([Das Pagelayout](das-pagelayout.md)). Ersetzen Sie:

| Mockup | Template |
|---|---|
| `<html lang="en">` | `<html lang="{$site.http_equiv.Content-language\|wash}">` |
| `<title>Site</title>` | einen Titel aus dem Pfad der Seite und `$site.title` |
| `href="index.html"` | `href={'/'\|ezurl}` |
| `src="img/logo.svg"` | `src={'logo.svg'\|ezimage}` |
| ein festes Menü | ein fetch der Kinder der Wurzel ([Menüs und Navigation](../vorlagen/menues-und-navigation.md)) |

## Schritt 4: Seitentypen werden zu View-Templates

Für jeden Inhalts-Seitentyp ein Override mit eigenem Template ([Tutorial: eine Vollansicht überschreiben](tutorial-eine-vollansicht-ueberschreiben.md)).
Fügen Sie das Markup des Mockups ein und ersetzen Sie die Platzhaltertexte durch Felder:

```tpl
<h1>{$node.name|wash}</h1>
<div class="lead">{attribute_view_gui attribute=$node.data_map.intro}</div>
```

## Schritt 5: Fester Inhalt wird zu Template-Code

- Lorem ipsum wird zu Feldern des Inhalts.
- Wiederholte Elemente (Karten, Teaser) werden zu einem `{foreach}` über ein fetch und eine line-Ansicht.
- Texte der Oberfläche („Read more“, „Search“) laufen über `i18n`.
- Daten über `l10n`.

## Schritt 6: unterwegs aufräumen

Statische Templates tragen oft Gewohnheiten, die in Exponential nicht überleben:

- Inline-`<style>`, `style=""`, `onclick=""` und Inline-Skripte: Verschieben Sie sie in Dateien ([CSP-sicheres Design](csp-sicheres-design.md));
- fehlende `alt`, übersprungene Überschriften, ein `div` als Knopf: Beheben Sie sie jetzt
  ([Barrierefreiheit: Grundlagen für Themes](barrierefreiheit-grundlagen-fuer-themes.md));
- feste Farben: Machen Sie daraus Token, was Ihnen später den dunklen Modus gibt ([Design-Token](design-tokens.md));
- `left`/`right` in CSS: logische Eigenschaften, für Sprachen von rechts nach links.

## Verwandte Seiten

- [Tutorial: eine Theme-Erweiterung anlegen](tutorial-eine-theme-erweiterung-anlegen.md)
- [Ein Ausgangs-Theme wählen](ein-ausgangs-theme-waehlen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
