---
title: Dunkler Modus im Überblick
audience: designer
category: theme-design
order: 500
summary: Wie der dunkle Modus in Exponential-Designs funktioniert: Farbtoken mit einem hellen und einem dunklen Wert, ein Theme-Attribut am html-Element, das vor dem ersten Zeichnen gesetzt wird, die Auswahl hell, dunkel und System und was admin4 als Referenzumsetzung tut.
keywords: dunkler Modus, Dark Mode, dunkles Theme, heller Modus, Farbschema, prefers-color-scheme, data-a4-theme, Token, Theme-Umschalter, System-Theme, color-scheme, dark mode, dark theme, light mode, color scheme, tokens, theme switch, system theme
featured: true
type: help
---
# Dunkler Modus im Überblick

Diese Seite ist für Designer, die einem Design einen dunklen Modus hinzufügen oder Seiten in der Administration gestalten, die einen
hat. Sie erklärt das Modell; die Seiten, auf die jeder Abschnitt verweist, zeigen den Code.

## Inhalt

1. [Das Modell in vier Teilen](#das-modell-in-vier-teilen)
2. [Hell, dunkel und System](#hell-dunkel-und-system)
3. [Wie admin4 es macht](#wie-admin4-es-macht)
4. [Zwei Wege für ein öffentliches Design](#zwei-wege-für-ein-öffentliches-design)
5. [Was ein dunkler Modus nicht ist](#was-ein-dunkler-modus-nicht-ist)

## Das Modell in vier Teilen

1. **Token.** Jede Farbe ist eine CSS-Custom-Property (`--site-ink`, `--site-surface`), nie ein direkter Wert in einer Regel
   ([Design-Token](design-tokens.md)).
2. **Zwei Sätze von Werten.** Die Token bekommen helle Werte auf `:root` und dunkle Werte unter einem Selektor oder einer Media Query.
3. **Ein Schalter am `<html>`.** Ein Attribut wie `data-theme="dark"` wählt den dunklen Satz, oder die Systemeinstellung tut es.
4. **Kein Aufblitzen.** Das Attribut wird gesetzt, bevor der Browser etwas zeichnet, von einer winzigen Skriptdatei im Kopf
   ([Der Theme-Umschalter und der Schutz vor dem ersten Zeichnen](theme-umschalter-und-pre-paint-guard.md)).

## Hell, dunkel und System

Ein guter Umschalter bietet drei Möglichkeiten: hell, dunkel und „System“ (der Einstellung des Betriebssystems folgen, auch wenn sie
sich ändert, während die Seite offen ist). Ohne jede Auswahl kann ein öffentliches Design dem System allein mit
`prefers-color-scheme` folgen ([Dunkler Modus mit prefers-color-scheme](dunkelmodus-mit-prefers-color-scheme.md)).

![Die Administration im hellen Modus: das Dashboard mit Kopfbereich, linker Spalte und Karten](../../../../eng-us/help/images/designers/dashboard-light.webp)

![Dasselbe Dashboard im dunklen Modus: Jede Karte, jeder Text und jedes Bedienelement übernimmt seinen dunklen Token-Wert](../../../../eng-us/help/images/designers/dashboard-dark.webp)

## Wie admin4 es macht

- **Token** `--a4-*` in `design/admin4/stylesheets/components.css`; jedes Farbtoken hat einen dunklen Wert unter
  `html[data-a4-theme="dark"]` (ein Test setzt die Regel durch: Ein Farbtoken ohne dunklen Wert lässt ihn fehlschlagen).
- **Die Auswahl** wird pro Benutzer als Präferenz `admin_color_mode` gespeichert (auf Seiten ohne angemeldeten Benutzer im
  `localStorage` des Browsers) und von `design/admin4/javascript/a4_head.js` angewendet, das als Datei im Kopf vor den Stylesheets
  geladen wird. Es setzt `data-a4-theme` (`light` oder `dark`, was die Stylesheets lesen) und `data-a4-theme-choice` (`light`,
  `dark` oder `system`, was der Umschalter zeigt).
- **Der Umschalter** im Kopfbereich (`#a4-theme-toggle`) wechselt zwischen hell, dunkel, System; ein Statusbereich kündigt jede
  Änderung für Screenreader an.
- **Drucken** verwendet die hellen Token: Das Skript setzt vor dem Drucken hell und stellt danach die Auswahl wieder her.
- `color-scheme: dark` im dunklen Block macht auch Formularelemente und Bildlaufleisten dunkel.
- Ein Rahmen, der kein dunkles Design hat, setzt `data-a4-modes="light"` am `<html>` und bleibt hell.

## Zwei Wege für ein öffentliches Design

| Ansatz | Aufwand | Ergebnis |
|---|---|---|
| Nur dem System folgen (`@media (prefers-color-scheme: dark)`) | ein Block dunkler Token-Werte | automatisch, kein Umschalter, kein Skript |
| Ein Umschalter mit hell, dunkel, System | Token, ein Attribut, ein kleines Skript im Kopf und ein Knopf | Besucher wählen; die Auswahl wird gemerkt |

Beginnen Sie mit dem ersten; fügen Sie den Umschalter hinzu, wenn Besucher danach fragen.
[Tutorial: einen dunklen Modus hinzufügen](tutorial-dunkelmodus-hinzufuegen.md) macht beides, Schritt für Schritt.

## Was ein dunkler Modus nicht ist

- Nicht `filter: invert()`: Bilder und Markenfarben kommen falsch heraus.
- Nicht nur ein schwarzer Hintergrund: Flächen brauchen mehrere Ebenen (Seite, Karte, erhöht), Rahmen brauchen ein eigenes Token, und
  jedes Paar muss seinen Kontrast halten ([Farbkontrast](farbkontrast.md)).
- Für neue Token in der Administration nicht optional: Jedes neue Farbtoken braucht seinen dunklen Wert in derselben Änderung.

## Verwandte Seiten

- [Design-Token](design-tokens.md)
- [admin4-Token](admin4-tokens.md)
- [Checkliste: dunkler Modus](checkliste-dunkelmodus.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
