---
title: "Lernpfad: Designer"
audience: student
category: learning
order: 8
summary: Ein schrittweiser Lernpfad für Lernende in Design und Frontend, aufgebaut aus vorhandenen Hilfeseiten - was ein Design ist, die Design-Kaskade, das Pagelayout, Templates und Overrides, Stylesheets und Skripte, Barrierefreiheit und Schreibrichtung von rechts nach links, der Layouts-Editor und Themes als Erweiterungen -, mit einer Übungsaufgabe und einer Prüfung für jeden Schritt.
keywords: lernpfad, designer, frontend, design, theme, template, override, pagelayout, stylesheet, CSS, barrierefreiheit, rechts nach links, layouts, schwerpunkt, learning path, front-end, accessibility, right to left, track
type: help
---
# Lernpfad: Designer

Dieser Pfad ist für Lernende, die gestalten werden, wie mit Exponential gebaute Websites aussehen: Designer,
Frontend-Entwickler und Studierende des Webdesigns. Sie sollten HTML und CSS können. Schließen Sie zuerst den
[Redakteurpfad](lernpfad-redakteur.md) ab, damit Sie die Inhalte kennen, die Ihre Designs zeigen werden.

Der eigene Bereich der Hilfe für Designer ist groß; dieser Pfad wählt die Seiten aus, die Lernende zuerst brauchen.
Die vollständige Übersicht ist [Designer: hier beginnen](../designer/designer-hier-beginnen.md).

## Inhalt

1. [Die Schritte](#die-schritte)
2. [Regeln, die Sie früh lernen](#regeln-die-sie-früh-lernen)
3. [Weiterführend](#weiterführend)
4. [Wohin der Pfad als Nächstes führt](#wohin-der-pfad-als-nächstes-führt)

## Die Schritte

### Schritt 1: Was ein Design ist (1 Stunde)

- Lesen: [Designer: hier beginnen](../designer/designer-hier-beginnen.md#das-designsystem-in-fünf-sätzen),
  [Was ein Design ist](../designer/theme-design/was-ein-design-ist.md).
- Üben: Finden Sie das Design des öffentlichen Siteaccess Ihrer Lernwebsite mit
  `php bin/php/console exp:ini where site/DesignSettings/SiteDesign site`
  ([Erste Schritte](../../anleitungen/erste-schritte.md#7-erste-template-änderung)).
- Prüfen: Wo kann ein Design liegen? (In `design/` der Installation oder in `extension/<name>/design/<design>/`, wie
  [Templates und Design](../../anleitungen/templates-und-design.md#1-wie-ein-template-gefunden-wird) sagt.)

### Schritt 2: Die Design-Kaskade (1 Stunde)

- Lesen: [Die Design-Kaskade](../designer/theme-design/die-design-kaskade.md),
  [Templates und Design](../../anleitungen/templates-und-design.md), Abschnitt 1.
- Prüfen: Warum bearbeiten Sie niemals `design/standard/templates/`? (Es ist die Rückfallebene; Sie überschreiben es
  stattdessen.)

### Schritt 3: Sehen, welches Template was geschrieben hat (1 Stunde)

- Lesen: [Template-Pfadkommentare](../../funktionen/6.0/template-pfad-kommentare.md),
  [Templates und Design](../../anleitungen/templates-und-design.md#3-sehen-welches-template-welches-markup-geschrieben-hat).
- Üben: Schalten Sie die Pfadkommentare für Ihre Lernwebsite ein, finden Sie drei Templates im Quelltext der Seite und
  schalten Sie sie wieder aus.

### Schritt 4: Das Pagelayout (1 Stunde)

- Lesen: [Das Pagelayout](../designer/theme-design/das-pagelayout.md).
- Üben: Finden Sie mit den Pfadkommentaren das Pagelayout-Template Ihres Designs.

### Schritt 5: Templates und Overrides (3 Stunden)

- Lesen: [Überblick über Templates](../designer/vorlagen/templates-ueberblick.md),
  [Grundlagen der Overrides](../designer/vorlagen/override-grundlagen.md),
  [Die Template-Sprache lernen](die-template-sprache-lernen.md).
- Üben: [Projekt 3](projekt-03-ein-template-override.md).

### Schritt 6: Seiten mit Blöcken (2 Stunden)

- Lesen: [Der Layouts-Editor](../layout-editor.md).
- Üben: [Projekt 4](projekt-04-eine-seite-mit-layouts.md). Sehen Sie im Reiter **Design** des Blocks nach, welche
  Blockansichten Ihr Design anbietet.

### Schritt 7: Themes als Erweiterungen (2 Stunden)

- Lesen: [Themes sind Erweiterungen](../erweiterungsentwicklung/teil-6-theme-entwickler/themes-sind-erweiterungen.md),
  [Ein Theme von Hand](../erweiterungsentwicklung/teil-6-theme-entwickler/ein-theme-von-hand.md),
  [Ein Theme mit dem RAD-Assistenten](../erweiterungsentwicklung/teil-6-theme-entwickler/ein-theme-mit-dem-rad-assistenten.md).
- Üben: Legen Sie mit dem Assistenten unter `/setup/designextension` eine Design-Erweiterung an
  ([Erweiterungen](../../anleitungen/extensions.md#62-die-rad-werkzeuge-schreiben-lassen)), schalten Sie sie ein und überschreiben
  Sie darin ein Template.

### Schritt 8: Barrierefreiheit und Schreibrichtung von rechts nach links (2 Stunden)

- Lesen: den Kurs [Theme-Design](../trainer/theme-designer/README.md) der Trainer, Modul 5, und
  [Sprachen](../sprachen.md).
- Üben: Prüfen Sie eine Seite Ihres Designs nur mit der Tastatur und notieren Sie, was Sie nicht erreichen.

## Regeln, die Sie früh lernen

- **Kein Inline-CSS in Templates der Administrationsoberfläche.** Die Designs der Administrationsoberfläche halten
  alles CSS in Stylesheets, verwenden gemeinsame Komponenten und Tokens und geben jedem neuen Token einen Wert für den
  dunklen Modus; ein Test setzt das durch ([Die Administrationsoberfläche gestalten](../../bc/6.0/admin4-gestaltung.md)).
- **Maskieren Sie, was Benutzer eingeben.** `wash` auf jedem Wert aus Inhalten oder von Besuchern
  ([Templates und Design](../../anleitungen/templates-und-design.md#6-die-template-sprache-in-zehn-minuten)).
- **Überschreiben, nicht bearbeiten.** Änderungen gehören in Ihr eigenes Design oder Ihre eigene Erweiterung, damit
  Updates sie nicht überschreiben.

## Weiterführend

| Thema | Seiten |
|---|---|
| Jedes Thema für Designer | [Designer: hier beginnen](../designer/designer-hier-beginnen.md) |
| Ein Theme veröffentlichen | [Verbreitung: hier beginnen](../designer/veroeffentlichung/ein-theme-veroeffentlichen-hier-beginnen.md) |
| Ein Theme pflegen | [Lebenszyklus: hier beginnen](../designer/lebenszyklus/lebenszyklus-hier-beginnen.md) |
| Dunkler Modus und Tokens | [Dunkler Modus und Tokens in Themes](../erweiterungsentwicklung/teil-6-theme-entwickler/dunkler-modus-und-tokens-in-themes.md) |

## Wohin der Pfad als Nächstes führt

- Lernprojekte: [2](projekt-02-eine-kleine-website-struktur.md), [3](projekt-03-ein-template-override.md),
  [4](projekt-04-eine-seite-mit-layouts.md), [Abschlussprojekt](projekt-10-abschlussprojekt-eine-vollstaendige-kleine-website.md).
- Mit einem Trainer: [Theme-Design](../trainer/theme-designer/README.md), 8 Module, etwa 24 Stunden.
- Nächster Pfad: [Entwickler](lernpfad-entwickler.md), wenn Sie das PHP hinter einem Design schreiben möchten.

## Verwandte Seiten

- [Einen Lernpfad wählen](einen-lernpfad-waehlen.md)
- [Die Template-Sprache lernen](die-template-sprache-lernen.md)
- [Ein Portfolio aufbauen](ein-portfolio-aufbauen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
