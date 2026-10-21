---
title: Eine Stichprobe zur Barrierefreiheit einer Seite durchführen
audience: government, editor, designer, project-manager
category: public-sector
order: 430
summary: Schritt für Schritt - eine 15-minütige Prüfung der Barrierefreiheit einer öffentlichen Seite (automatische Prüfung mit einem Browser-Werkzeug auf Basis von axe, Tastaturdurchgang, Zoom, ein kurzer Durchgang mit dem Screenreader, Inhaltsprüfung), und wie Befunde festgehalten und an die Verantwortlichen für Templates oder Inhalte weitergeleitet werden.
keywords: Stichprobe zur Barrierefreiheit, axe, Browser-Erweiterung, Tastaturtest, Zoom 200 %, Screenreader, Alternativtext, Überschriften, Linktext, Kontrast, WCAG 2.2, Befunde, öffentlicher Sektor, accessibility spot check, browser extension, keyboard test, screen reader, alt text, headings, link text, contrast, findings, public sector
type: help
---
# Eine Stichprobe zur Barrierefreiheit einer Seite durchführen

Für Redakteure, Designer und Koordinatoren für Barrierefreiheit: eine kurze Prüfung einer Seite, nachdem sie
veröffentlicht oder geändert wurde. Exponential liefert kein Prüfwerkzeug für die Barrierefreiheit öffentlicher Seiten
mit; die Prüfung nutzt verbreitete kostenlose Werkzeuge und folgt
[Die Barrierefreiheit Ihrer Site testen](../../../../../eng-us/help/creators/website-creators/testing-accessibility.md).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Befunde festhalten](#befunde-festhalten)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein Desktop-Browser mit installierter Erweiterung für Barrierefreiheit auf Basis von axe (oder dem von Ihrer
  Organisation zugelassenen Prüfwerkzeug).
- Der Screenreader Ihres Betriebssystems, wenn Sie ihn bedienen können.
- Die Adresse der Seite auf dem öffentlichen Siteaccess, abgemeldet (privates Fenster).

## Schritte

1. **Automatische Prüfung** (3 Minuten): Führen Sie das Prüfwerkzeug auf der Seite aus. Notieren Sie jeden Verstoß mit
   seiner Regel und seinem Element.
2. **Tastatur** (4 Minuten): Neu laden, Tab drücken: Der Sprunglink erscheint zuerst; mit Tab durch die Seite: Jeder
   Link und jedes Bedienelement ist in sinnvoller Reihenfolge erreichbar, und der Fokus ist immer sichtbar; Menüs öffnen
   und schließen sich mit Enter und Escape; nichts hält den Fokus fest (Karten, Video, Formulare).
3. **Zoom** (2 Minuten): 200 % Zoom und ein schmales Fenster: kein waagerechtes Scrollen, nichts abgeschnitten.
4. **Inhalt** (4 Minuten): Überschriften in der richtigen Reihenfolge (ein Werkzeug für die Gliederung der
   Überschriften oder die Überschriftenliste des Screenreaders); Bilder beschrieben (und Alternativtexte sind nicht nur
   Datei- oder Objektnamen); aussagekräftige Linktexte; Tabellen mit Kopfzellen; verlinkte Dokumente barrierefrei.
5. **Screenreader** (2 Minuten, optional): Seitentitel, Landmarks, die Hauptüberschrift, ein Bild, ein Formularfeld.

## Befunde festhalten

| Seite | Problem | WCAG-Kriterium | Schweregrad | Quelle | Verantwortlich |
|---|---|---|---|---|---|
| `<address>` | `<what happens>` | `<for example 1.1.1>` | `<blocker / major / minor>` | Template / Inhalt / Erweiterung | `<who>` |

Schicken Sie Probleme im Inhalt an den Redakteur der Seite; Probleme im Template an den Designer oder Lieferanten,
einmal für alle Seiten, die dieses Template verwenden.

## Verwandte Seiten

- [Testen mit axe und der Tastatur](../../../../../eng-us/help/government/accessibility/testing-with-axe-and-the-keyboard.md)
- [Die Barrierefreiheit Ihrer Site testen](../../../../../eng-us/help/creators/website-creators/testing-accessibility.md)
- [Barrierefreie Inhalte für Redakteure](../../../../../eng-us/help/government/accessibility/accessible-content-for-editors.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
