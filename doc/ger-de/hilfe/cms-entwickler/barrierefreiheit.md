---
title: Barrierefreiheit
audience: cms-developer
category: cms-development
order: 44
summary: Für CMS-Entwickler, wie Sie barrierefreie Websites mit Exponential bauen. Die Seite behandelt, wo das Produkt gegenüber WCAG 2.2 AA steht, was das Inhaltsmodell beiträgt (Alternativtext, Überschriften, strukturierter Rich Text), Templates und Designs, Formulare, Tests mit Tastatur, Zoom und Kontrast und was Redakteure wissen müssen.
keywords: Barrierefreiheit, a11y, WCAG 2.2, AA, WAI-ARIA, EN 301 549, Section 508, Alternativtext, Überschriften, Landmarks, Sprunglinks, Tastatur, Fokus, Kontrast, Zoom, Formulare, Beschriftungen, Screenreader, reduzierte Bewegung, Rechts-nach-links, Dunkelmodus, accessibility, alternative text, headings, landmarks, skip links, keyboard, focus, contrast, labels, screen reader, reduced motion, right-to-left, dark mode
type: help
---
# Barrierefreiheit

Diese Seite richtet sich an Entwickler, die dafür verantwortlich sind, dass eine Website für alle nutzbar ist,
einschließlich Menschen, die einen Screenreader, eine Tastatur, hohe Vergrößerung oder hohen Kontrast verwenden. Auf
einer Exponential-Website hängt die Barrierefreiheit vor allem von Ihrem Design und Ihren Templates ab und zum Teil vom
Inhaltsmodell, das gute Praxis für Redakteure zum Standard machen kann.

## Inhalt

1. [Wo das Produkt steht](#wo-das-produkt-steht)
2. [Was das Inhaltsmodell tun kann](#was-das-inhaltsmodell-tun-kann)
3. [Templates und Designs](#templates-und-designs)
4. [Formulare](#formulare)
5. [Testen](#testen)
6. [Redakteure](#redakteure)
7. [Checkliste](#checkliste)

## Wo das Produkt steht

Die eigene Einschätzung des Projekts steht in [Standards zur Barrierefreiheit](../../standards/barrierefreiheit.md):
Gegenüber WCAG 2.2 Stufe AA und WAI-ARIA 1.2 **erfüllt das Produkt die Anforderungen teilweise**: Die Administration
admin4 wurde nach AA-Kriterien gebaut und getestet, die klassische Administration teilweise korrigiert, und die
öffentlichen Designs und der Online Editor sind nicht geprüft. EN 301 549 und Section 508 sind nicht verifiziert. Für
eine öffentliche Website bedeutet das: **Ihr Design ist das, was ein Audit beurteilt**; planen Sie es in Ihren eigenen
Templates ein und testen Sie es selbst.

## Was das Inhaltsmodell tun kann

| Entscheidung | Wirkung |
|---|---|
| Bildattribute mit **Alternativtext ist erforderlich** | Redakteure können kein Bild ohne Text speichern ([Bilder und Bildvarianten](bilder-und-bildvarianten.md#alternativtext)) |
| Verknüpfte oder eingebettete Bildobjekte statt Bilder in vielen Feldern | ein Alternativtext, an einer Stelle aktuell gehalten |
| Rich Text statt vieler kleiner Felder | echte Überschriften, Listen und Tabellen statt optischer Nachahmungen ([Rich Text](rich-text.md)) |
| Ein kleiner Satz eigener Tags mit barrierefreien Templates | Infokästen, Zitate und Handlungsaufrufe überall mit korrektem Markup |
| Ein Attribut `title` für das `<h1>` der Seite | eine Überschrift je Seite aus den Inhalten |
| Eine Sprache je Übersetzung | das Pagelayout kann das Attribut `lang` der Seite aus der Inhaltssprache setzen ([Übersetzungen und Sprachen](uebersetzungen-und-sprachen.md)) |

## Templates und Designs

Die Hilfe für Designer behandelt jedes Thema mit Beispielen:

| Thema | Seite |
|---|---|
| Die Grundlagen für ein Theme | [Grundlagen der Barrierefreiheit für Themes](../designer/theme-design/barrierefreiheit-grundlagen-fuer-themes.md) |
| Landmarks und Überschriften | [Landmarks und Überschriften](../designer/theme-design/landmarks-und-ueberschriften.md) |
| Sprunglinks | [Sprunglinks](../designer/theme-design/sprunglinks.md) |
| Tastatur und Fokus | [Tastatur und Fokus](../designer/theme-design/tastatur-und-fokus.md) |
| Farbkontrast | [Farbkontrast](../designer/theme-design/farbkontrast.md) |
| Reduzierte Bewegung | [Reduzierte Bewegung](../designer/theme-design/reduzierte-bewegung.md) |
| Sprachen mit Schreibrichtung von rechts nach links | [Designs für Rechts-nach-links](../designer/theme-design/rechts-nach-links-designs.md) |

Regeln, die für jedes Exponential-Design gelten: CSS in Stylesheets mit Tokens und einem Wert für den Dunkelmodus je
Farb-Token, kein Inline-CSS, kein Inline-JavaScript ([Kein Inline-CSS](../designer/theme-design/kein-inline-css.md)).
Auch Blöcke von Layouts sind Templates: Prüfen Sie ihr Markup wie jedes andere
([Layout-Blöcke gestalten](../designer/theme-design/layout-bloecke-gestalten.md)).

## Formulare

Die mitgelieferten Collect- und Benutzer-Templates geben Eingabefelder ohne IDs aus; geben Sie Ihren Kopien IDs, damit
Beschriftungen funktionieren, zeigen Sie Prüfmeldungen neben dem Feld und als Text, und verwenden Sie `autocomplete` in
Anmelde- und Registrierungsformularen ([Barrierefreie Formulare](../designer/theme-design/barrierefreie-formulare.md),
[Templates für Benutzer und Anmeldung](../designer/vorlagen/benutzer-und-anmelde-templates.md),
[Formulare und Informationssammlung](formulare-und-informationssammlung.md)).

## Testen

- **Nur Tastatur**: Jeder Link, jedes Menü, jedes Formular und jeder Dialog ist erreichbar und bedienbar, der Fokus ist
  immer sichtbar.
- **Zoom**: Bei 200 % und bei geringer Breite überlappt nichts und nichts scrollt seitlich
  ([Testen bei Zoomstufen](../designer/theme-design/testen-bei-zoomstufen.md)).
- **Kontrast**: Text mindestens 4,5:1, im hellen und im dunklen Modus.
- **Ein Screenreader** auf den wichtigsten Templates: Startseite, eine Liste, eine Full View, ein Formular, Suche.
- **Automatische Prüfungen** helfen, fehlende Beschriftungen und Alternativtexte zu finden; sie ersetzen die
  manuellen Tests nicht.
- Ihre eigenen Prüfungen am Design: [Checkliste: Barrierefreiheit](../designer/theme-design/checkliste-barrierefreiheit.md).

## Redakteure

Barrierefreie Templates können trotzdem nicht barrierefreie Inhalte erhalten. Weisen Sie Redakteure in
Alternativtexte, aussagekräftige Linktexte, die Reihenfolge der Überschriften in Rich Text und Untertitel für Videos
ein. Der Kurs für Redakteure enthält das Material ([Editors 101](../trainer/redakteure-101/README.md)); Trainer, die
Barrierefreiheit unterrichten, finden es in
[Train the Trainer, Modul 5](../trainer/train-the-trainer/modul-05-barrierefreiheit-und-inklusion/README.md).

## Checkliste

- [ ] Bildattribute verlangen einen Alternativtext; dekorative Bilder haben im Template ein leeres `alt`.
- [ ] Ein `<h1>` je Seite, Überschriften in Reihenfolge, Landmarks vorhanden, ein Sprunglink zuerst.
- [ ] Jedes Formularfeld hat eine Beschriftung; Fehler werden als Text angekündigt.
- [ ] Tastatur, Zoom und Kontrast auf den wichtigsten Templates im hellen und dunklen Modus getestet.
- [ ] Das `lang` jeder Seite entspricht ihrer Inhaltssprache.
- [ ] Redakteure eingewiesen.

## Verwandte Seiten

- [Standards zur Barrierefreiheit](../../standards/barrierefreiheit.md)
- [Checkliste für den Projektstart](checkliste-fuer-den-projektstart.md)
- [Designer: hier beginnen](../designer/designer-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
