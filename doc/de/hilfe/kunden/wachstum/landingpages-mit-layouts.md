---
title: Landingpages mit Layouts
audience: customer, editor, businesses
category: customer-success
order: 4
summary: Die Sicht des Betreibers auf Exponential Layouts für Startseiten, Landingpages und Kampagnen - was die Redaktion ohne Entwickler ändern kann, wer für welches Layout zuständig ist, Kampagnenzeiträume per Regel, gemeinsamer Header und Footer, Entwürfe und die Regel „eine Person pro Layout“ - und wofür Sie weiterhin Ihren Partner brauchen.
keywords: Landingpages, Layouts, Exponential Layouts, Page Builder, Kampagne, Blöcke, Zonen, Regeln, gemeinsame Layouts, Startseite, Website-Betreiber, landing pages, layouts, page builder, campaign, blocks, zones, rules, shared layouts, front page, site owner
type: help
---
# Landingpages mit Layouts

Diese Seite ist für Betreiber und Marketingleitungen, die Startseiten und Kampagnenseiten ändern möchten, ohne auf einen
Entwickler zu warten. Exponential Layouts ist ein visueller Page Builder: Ein **Layout** ist eine gespeicherte Anordnung
von **Blöcken** in **Zonen**, und eine **Regel** entscheidet, welche Seiten es erhalten, sodass Redakteure Seiten neu
anordnen, ohne Templates anzufassen (`README.md`, „Exponential Layouts“). Die Klicks für Redakteure stehen unter
[Der Layouts-Editor](../../layout-editor.md).

## Inhalt

1. [Was die Redaktion selbst erledigen kann](#was-die-redaktion-selbst-erledigen-kann)
2. [Zuständigkeit](#zuständigkeit)
3. [Kampagnenzeiträume](#kampagnenzeiträume)
4. [Gemeinsame Teile: Header, Footer, Prefooter](#gemeinsame-teile-header-footer-prefooter)
5. [Entwürfe und Veröffentlichen](#entwürfe-und-veröffentlichen)
6. [Wann Sie Ihren Partner brauchen](#wann-sie-ihren-partner-brauchen)

## Was die Redaktion selbst erledigen kann

| Aufgabe | Wie | Quelle |
|---|---|---|
| Eine Seite neu anordnen | Blöcke in den Zonen ihres Layouts hinzufügen, verschieben und ändern | [Der Layouts-Editor](../../layout-editor.md) |
| Die neuesten Artikel automatisch anzeigen | ein Listen- oder Rasterblock mit einer dynamischen Sammlung (Kinder, Teilbaum, neueste ...) | `README.md`, „Exponential Layouts“ |
| Ein ausgewähltes Element unter dynamischen anheften | ein von Hand gewähltes Element in der Sammlung | ebenda |
| Text-, Bild-, Schaltflächen-, Karten-, Galerie-, Karussell-, Reiter-, Akkordeon-, Landkarten- oder Videoblöcke verwenden | die Standardblöcke von `explayouts_standard` | ebenda |
| Einem Teil der Website ein eigenes Layout geben | eine Regel nach Teilbaum, Klasse, Siteaccess oder Zeit | [Layout-Regeln](../../../../en/us/help/creators/website-creators/layout-rules.md) |
| Sehen, welches Layout eine Seite verwendet | der Reiter **Layouts** in der Ansicht eines Knotens | `README.md`, „Exponential Layouts“ |

Der Editor befindet sich unter `/explayouts_ui_api/app` in der Administration; der Zugriff erfordert die Richtlinien des
Moduls `explayouts`, mindestens `explayouts/read` ([Landingpages mit dem Layouts-Editor](../../../../en/us/help/creators/website-creators/landing-pages-with-layouts.md)).

## Zuständigkeit

Layouts sind gemeinsame Arbeit, geben Sie also jedem eine zuständige Person:

| Layout | Typischerweise zuständig |
|---|---|
| Startseite | Chefredaktion |
| Startseiten der Rubriken | die leitende Redakteurin oder der leitende Redakteur der Rubrik |
| Kampagnen-Layouts | die für die Kampagne verantwortliche Person, bis die Kampagne endet |
| Gemeinsamer Header, Footer, Prefooter | nur eine Person, oft der Betreiber oder die Web-Leitung |

Benennen Sie Blöcke klar und vermerken Sie im Namen eines Blocks, warum ein Element angeheftet ist; Ihre Nachfolger
werden es Ihnen danken ([Landingpages mit dem Layouts-Editor](../../../../en/us/help/creators/website-creators/landing-pages-with-layouts.md)).

## Kampagnenzeiträume

Eine Kampagnen-Landingpage ist ein Layout, das der Seite durch eine Regel mit einer Bedingung `time` (`from`, `to`)
zugeordnet wird, mit höherer Priorität als die normale Regel der Seite. Vor und nach dem Zeitraum wird das normale
Layout angezeigt ([Layout-Regeln](../../../../en/us/help/creators/website-creators/layout-rules.md)). Planen Sie sie im
[Redaktionskalender](../alltag/redaktionskalender.md) und prüfen Sie sie mit der
[Checkliste für Kampagnen-Landingpages](checkliste-kampagnen-landingpages.md).

## Gemeinsame Teile: Header, Footer, Prefooter

Ein gemeinsames Layout enthält den Header, den Footer oder den Prefooter genau einmal; die Zonen jedes Seitenlayouts
verweisen darauf. Ändern Sie es einmal, und jede Seite folgt (`README.md`, „Exponential Layouts“;
[Gemeinsame Layouts](../../../../en/us/help/creators/website-creators/shared-header-and-footer.md)). Das ist mächtig und daher riskant:
Ein Fehler dort steht auf jeder Seite. Überlassen Sie es einer einzigen zuständigen Person und veröffentlichen Sie
Änderungen daran zu einer ruhigen Zeit.

## Entwürfe und Veröffentlichen

> **Warnung**
> Der Editor arbeitet immer an einem Entwurf, und Besucher sehen nur veröffentlichte Layouts. Entwürfe gibt es **pro
> Layout, nicht pro Benutzer**: Zwei Personen, die dasselbe Layout bearbeiten, überschreiben sich gegenseitig
> ([Landingpages mit dem Layouts-Editor](../../../../en/us/help/creators/website-creators/landing-pages-with-layouts.md)). Vereinbaren
> Sie, dass jeweils nur eine Person ein Layout bearbeitet.

- **Publish** macht den Entwurf live; **Discard draft** verwirft ihn.
- Prüfen Sie die Seite nach dem Veröffentlichen auf der Website, abgemeldet.

## Wann Sie Ihren Partner brauchen

| Bedarf | Warum |
|---|---|
| Ein neuer Layout-Typ (eine neue Anordnung von Zonen) | Layout-Typen werden in Einstellungen definiert (`explayouts.ini`); der Typ eines Layouts lässt sich später nicht ändern |
| Eine neue Art von Block | ein Block-Typ ist eine Handler-Klasse und ein Template in einer Erweiterung |
| Ein Block, der ein Formular oder Shop-Schritte zeigt | Logik gehört in Templates ([Wann Layouts und wann Templates](../../../../en/us/help/creators/website-creators/landing-pages-with-layouts.md#when-to-use-layouts-and-when-templates)) |
| Layouts in eine andere Installation übertragen | Export und Import (`README.md`, „Exponential Layouts“) |

## Verwandte Seiten

- [Der Layouts-Editor](../../layout-editor.md)
- [Checkliste für Kampagnen-Landingpages](checkliste-kampagnen-landingpages.md)
- [Kampagnen und Landingpages für gemeinnützige Organisationen](../../../../en/us/help/businesses/nonprofits/campaigns-and-landing-pages.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
