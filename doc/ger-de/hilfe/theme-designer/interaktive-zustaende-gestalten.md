---
title: "Interaktive Zustände gestalten"
audience: theme-designer
category: theme-design
order: 17
summary: Die Zustände, die jedes interaktive Element eines Exponential-Themes braucht - Standard, Hover, Fokus, aktiv, besucht, aktuell, ausgewählt, deaktiviert, Fehler und Laden - was jeder zeigen muss, wie sie auf CSS-Selektoren und Tokens abgebildet werden und eine Zustandsmatrix zur Übergabe.
keywords: Theme-Designer, Zustände, Hover, Fokus, focus-visible, aktiv, besucht, aktuelle Seite, aria-current, deaktiviert, Fehlerzustand, Laden, Zustandsmatrix, theme designer, states, focus, active, visited, current page, disabled, error state, loading, state matrix
type: help
---
# Interaktive Zustände gestalten

Diese Seite ist für Theme-Designer, die Schaltflächen, Links, Formularfelder, Menüs und Karten zeichnen. Ein Mockup
zeigt jedes Element meist in einem Zustand; ein Browser zeigt es in vielen. Jeder Zustand, den Sie nicht gestalten,
wird zufällig gestaltet, meist schlecht und meist zuerst im Dunkelmodus. Diese Seite listet die Zustände auf, was jeder
zeigen muss und wie sie ins CSS gelangen.

## Inhalt

1. [Die Zustände](#die-zustände)
2. [Fokus kommt zuerst](#fokus-kommt-zuerst)
3. [Aktuell und ausgewählt](#aktuell-und-ausgewählt)
4. [Deaktiviert, Fehler und Laden](#deaktiviert-fehler-und-laden)
5. [Vom Zustand zum Selektor](#vom-zustand-zum-selektor)
6. [Die Zustandsmatrix](#die-zustandsmatrix)

## Die Zustände

| Zustand | Wann | Muss zeigen |
|---|---|---|
| Standard | in Ruhe | dass es interaktiv ist (Links von Text unterscheidbar, nicht nur durch Farbe) |
| Hover | Zeiger darüber | eine Veränderung, aber nichts, was zum Verständnis des Elements nötig ist |
| Fokus | mit der Tastatur erreicht | einen deutlich sichtbaren Ring, mindestens 3:1 gegenüber der Umgebung |
| Aktiv | wird gerade gedrückt | kurze Rückmeldung |
| Besucht | ein bereits verfolgter Link | optional; nützlich in langen Listen und Suchergebnissen |
| Aktuell | der Link zur Seite, auf der Sie sind | in Menüs und Brotkrümelnavigation markiert, auch für Screenreader |
| Ausgewählt / angehakt | eine gewählte Option oder ein Reiter | anders als Hover, nicht nur durch Farbe |
| Deaktiviert | gerade nicht verfügbar | erkennbar, aber nicht das Wichtigste auf dem Bildschirm |
| Fehler | ungültige Eingabe | Meldungstext und ein Symbol, nicht nur ein roter Rahmen |
| Laden | Warten auf ein Ergebnis | ein Hinweis, dass etwas geschieht, ohne Layoutverschiebung |

## Fokus kommt zuerst

Gestalten Sie den Fokuszustand vor dem Hover. Tastaturnutzer, Nutzer von Schaltersteuerungen und viele
Screenreader-Nutzer sind auf ihn angewiesen, und die Checklisten des Projekts verlangen ihn
([Tastatur und Fokus](../designer/theme-design/tastatur-und-fokus.md)):

- ein Fokusstil für das ganze Theme, aus einem Token `--site-focus`, mit einem dunklen Wert;
- eine Kontur mit Abstand, damit sie nicht mit dem eigenen Rahmen des Elements verschmilzt;
- sichtbar auf jeder Fläche, auf der das Element stehen kann, einschließlich der Akzentfarbe und Bildern;
- nie ohne Ersatz entfernt.

Hover und Fokus dürfen sich ähneln, aber Fokus darf nie schwächer sein als Hover.

## Aktuell und ausgewählt

Die aktuelle Seite im Hauptmenü und der letzte Eintrag der Brotkrümelnavigation brauchen eine sichtbare Markierung und
`aria-current="page"` im Markup. Verlangen Sie beides bei der Übergabe; das Template entscheidet, welcher Eintrag
aktuell ist ([Menüs und Navigation](../designer/vorlagen/menues-und-navigation.md#die-aktuelle-seite-markieren)).

Ausgewählte Reiter, angehakte Filter und die aktive Sprache in einem Sprachumschalter folgen derselben Regel: eine
Markierung, die nicht auf Farbe angewiesen ist, etwa eine Unterstreichung, ein Häkchen oder ein fetterer Schriftschnitt.

## Deaktiviert, Fehler und Laden

- **Deaktiviert**: Vermeiden Sie es, wo Sie können. Eine Schaltfläche, die deaktiviert ist, bis ein Formular gültig ist,
  lässt Menschen raten, was fehlt; eine aktive Schaltfläche, die die Fehler zeigt, ist klarer
  ([Formulare gestalten](formulare-gestalten.md)).
- **Fehler**: Zeichnen Sie die Meldung neben dem Feld und in einer Zusammenfassung oben im Formular, mit Text, Symbol und
  Farbe zusammen. Die Fehlerfarben kommen aus Status-Tokens ([Farbpaletten und Farbabstufungen](farbpaletten-und-farbabstufungen.md#statusfarben)).
- **Laden**: Reservieren Sie den Platz für das Ergebnis, damit nichts springt, wenn es eintrifft
  ([CLS und LCP messen](../designer/theme-design/cls-und-lcp-messen.md)). Beachten Sie reduzierte Bewegung bei
  Ladeanimationen ([Bewegung und Interaktion](bewegung-und-interaktion.md)).

## Vom Zustand zum Selektor

| Zustand | CSS |
|---|---|
| Hover | `:hover` (nur wo ein Zeiger schweben kann: `@media (hover: hover)`) |
| Fokus | `:focus-visible` für den Ring; `:focus-within` an einem Container wie einer Karte |
| Aktiv | `:active` |
| Besucht | `:visited` (nach den Regeln der Browser können sich nur Farben ändern) |
| Aktuell | `[aria-current="page"]` |
| Ausgewählt | `[aria-selected="true"]`, `:checked`, `[aria-pressed="true"]` |
| Deaktiviert | `:disabled`, `[aria-disabled="true"]` |
| Fehler | `[aria-invalid="true"]` am Feld |

Zustände über ARIA-Attribute zu gestalten hält den sichtbaren Zustand und den Zustand, den Hilfstechnologien ansagen,
im Gleichschritt: Das Template setzt das Attribut, und das Stylesheet folgt ihm. Alle Werte kommen aus Tokens; kein
Zustand wird mit einem Inline-Stil oder einem Skript gezeichnet, das Farben setzt.

## Die Zustandsmatrix

Übergeben Sie eine Matrix pro Komponente, hell und dunkel:

| Komponente | Standard | Hover | Fokus | Aktiv | Aktuell/ausgewählt | Deaktiviert | Fehler |
|---|---|---|---|---|---|---|---|
| Primäre Schaltfläche | gezeichnet | gezeichnet | gezeichnet | gezeichnet | entfällt | gezeichnet | entfällt |
| Textlink | gezeichnet | gezeichnet | gezeichnet | gezeichnet | gezeichnet (Menü) | entfällt | entfällt |
| Textfeld | gezeichnet | entfällt | gezeichnet | entfällt | entfällt | gezeichnet | gezeichnet |
| Karte | gezeichnet | gezeichnet | gezeichnet (focus-within) | entfällt | entfällt | entfällt | entfällt |

Fügen Sie die Matrix dem [Komponenteninventar](komponenteninventar.md) hinzu, und zeigen Sie die Zustände auf der
[Styleguide-Seite](eine-styleguide-seite-bauen.md), soweit sie sich statisch zeigen lassen.

## Verwandte Seiten

- [Tastatur und Fokus](../designer/theme-design/tastatur-und-fokus.md)
- [Farbkontrast](../designer/theme-design/farbkontrast.md#jeden-zustand-und-beide-themes-prüfen)
- [Barrierefreie Formulare](../designer/theme-design/barrierefreie-formulare.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
