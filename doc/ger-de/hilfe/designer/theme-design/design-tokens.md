---
title: Design-Token
audience: designer
category: theme-design
order: 510
summary: Ein Design auf Token (CSS-Custom-Properties) für Farbe, Abstand, Größe, Radius und Schriften aufbauen: Benennung, ein Startsatz von Token mit hellen und dunklen Werten, Token in Regeln verwenden und die Token-Regeln von admin4 als Vorbild.
keywords: Design-Token, CSS-Custom-Properties, CSS-Variablen, Farbtoken, Abstandsskala, Schriftskala, Radius, Theme-Variablen, Benennung von Token, dunkle Werte, design tokens, css custom properties, css variables, color tokens, spacing scale, type scale, radius, theme variables, token naming, dark values
type: help
---
# Design-Token

Diese Seite ist für Designer, die ein Design möchten, das sich leicht auf eine neue Marke umstellen lässt, einen dunklen Modus hat und
einheitlich bleibt. Ein **Token** ist ein benannter Wert (eine CSS-Custom-Property), den jede Regel statt eines direkten Werts
verwendet.

## Inhalt

1. [Warum Token](#warum-token)
2. [Benennung](#benennung)
3. [Ein Startsatz](#ein-startsatz)
4. [Token verwenden](#token-verwenden)
5. [Regeln, die Token nützlich halten](#regeln-die-token-nützlich-halten)

## Warum Token

- **Eine Stelle zum Ändern**: Eine Markenfarbe ist eine Zeile, nicht fünfzig.
- **Dunkler Modus** ist ein zweiter Satz von Werten für dieselben Namen ([Dunkler Modus im Überblick](dunkelmodus-ueberblick.md)).
- **Marken einer Multisite** sind jeweils ein Dutzend Zeilen ([Designs in einer Multisite-Installation](designs-in-einer-multisite-installation.md)).
- **Einheitlichkeit**: Vier überall verwendete Abstandsstufen sehen besser aus als dreißig leicht verschiedene Innenabstände.

## Benennung

Benennen Sie Token nach ihrer **Rolle**, nicht nach ihrem Wert: `--site-ink` (Text), `--site-muted`, `--site-surface`, `--site-card`,
`--site-line`, `--site-accent`, `--site-focus`. Ein Token namens `--orange` ergibt keinen Sinn mehr, sobald die Marke blau wird oder
im dunklen Modus. Verwenden Sie ein Präfix für Ihr Design (admin4 verwendet `--a4-`).

## Ein Startsatz

```css
/* design/mytheme/stylesheets/tokens.css */
:root {
  /* color: roles */
  --site-ink: #1f2430;
  --site-muted: #5d6573;          /* 5.9:1 on --site-surface */
  --site-surface: #ffffff;
  --site-card: #f6f7f9;
  --site-line: #d5dae1;
  --site-accent: #0b5cad;          /* 6.4:1 on white, for links and buttons */
  --site-on-accent: #ffffff;
  --site-focus: #0b5cad;

  /* space: one scale */
  --site-space-1: 0.25rem;
  --site-space-2: 0.5rem;
  --site-space-3: 1rem;
  --site-space-4: 1.5rem;
  --site-space-5: 2.5rem;

  /* type */
  --site-font: system-ui, -apple-system, "Segoe UI", sans-serif;
  --site-text-s: 0.875rem;
  --site-text-m: 1rem;
  --site-text-l: 1.25rem;
  --site-text-xl: clamp(1.5rem, 1.2rem + 1.5vw, 2.25rem);

  /* shape */
  --site-radius: 0.5rem;
}

:root[data-theme="dark"] {
  color-scheme: dark;
  --site-ink: #e8ebf0;
  --site-muted: #a3abb9;
  --site-surface: #171c25;
  --site-card: #1b212c;
  --site-line: #2e3644;
  --site-accent: #7fb3f0;
  --site-on-accent: #0e1218;
  --site-focus: #7fb3f0;
}
```

Die Kontrastverhältnisse in den Kommentaren sind Beispiele dafür, was Sie notieren sollten; messen Sie Ihre eigenen Farben
([Farbkontrast](farbkontrast.md)).

## Token verwenden

```css
body   { color: var(--site-ink); background: var(--site-surface); font-family: var(--site-font); }
.card  { background: var(--site-card); border: 1px solid var(--site-line);
         border-radius: var(--site-radius); padding: var(--site-space-4); }
a      { color: var(--site-accent); }
```

Laden Sie `tokens.css` als erste Datei in der Liste von `ezcss_load`.

## Regeln, die Token nützlich halten

Das sind die Regeln der Administration (`doc/eng-us/bc/6.0/admin4-styling.md`), und sie funktionieren für jedes Design:

- Keine direkten Farben, Radien, Abstände oder Schriftwerte in Regeln: nur Token.
- Keine lokalen Aliasse (`--card-bg: var(--site-card)` in einer Komponente): Verwenden Sie das Token selbst.
- Ein Akzent und ein Komponentenradius; fügen Sie keinen zweiten „nur für diese Seite“ hinzu.
- Jedes Farbtoken hat einen dunklen Wert, hinzugefügt in derselben Änderung wie der helle.
- Abstands-, Größen- und Schrift-Token sind in beiden Themes gleich und werden einmal definiert.

## Verwandte Seiten

- [admin4-Token](admin4-tokens.md)
- [Dunkler Modus mit prefers-color-scheme](dunkelmodus-mit-prefers-color-scheme.md)
- [Tutorial: einen dunklen Modus hinzufügen](../../../../eng-us/help/designers/theme-design/tutorial-add-dark-mode.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
