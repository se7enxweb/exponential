---
title: Dunkler Modus mit prefers-color-scheme
audience: designer
category: theme-design
order: 520
summary: Der einfachste dunkle Modus für ein öffentliches Design: dunkle Token-Werte in einer Media Query prefers-color-scheme, kombiniert mit einer ausdrücklichen Wahl von hell oder dunkel, wenn ein Umschalter hinzukommt, dazu Bilder, color-scheme und Testen.
keywords: prefers-color-scheme, dunkler Modus, Dark Mode, Media Query, System-Theme, color-scheme, automatischer dunkler Modus, picture source media, dunkle Bilder, dunklen Modus emulieren, dark mode, media query, system theme, automatic dark mode, dark images, emulate dark mode
type: help
---
# Dunkler Modus mit prefers-color-scheme

Diese Seite ist für Designer, die einen dunklen Modus möchten, der der Systemeinstellung des Besuchers folgt, ohne Skript. Sie zeigt
auch, wie Sie ihn später mit einem Umschalter kombinieren.

## Inhalt

1. [Die Systemeinstellung allein](#die-systemeinstellung-allein)
2. [Später eine ausdrückliche Wahl hinzufügen](#später-eine-ausdrückliche-wahl-hinzufügen)
3. [Bilder und Logos](#bilder-und-logos)
4. [Testen](#testen)

## Die Systemeinstellung allein

Mit Token auf `:root` (siehe [Design-Token](design-tokens.md)) ergibt ein einziger Block einen vollständigen dunklen Modus:

```css
@media (prefers-color-scheme: dark) {
  :root {
    color-scheme: dark;
    --site-ink: #e8ebf0;
    --site-surface: #171c25;
    --site-card: #1b212c;
    --site-line: #2e3644;
    --site-accent: #7fb3f0;
  }
}
```

`color-scheme: dark` weist den Browser an, auch Formularelemente, Bildlaufleisten und die Standardfläche dunkel zu zeichnen.

## Später eine ausdrückliche Wahl hinzufügen

Wenn ein Umschalter `data-theme` am `<html>` setzt, muss der Systemblock einer ausdrücklichen Wahl weichen. Schränken Sie ihn so ein,
dass er nur gilt, wenn der Besucher nichts oder „System“ gewählt hat, und fügen Sie einen Block für eine ausdrückliche dunkle Wahl
hinzu:

```css
@media (prefers-color-scheme: dark) {
  :root:not([data-theme="light"]) { color-scheme: dark; --site-ink: #e8ebf0; --site-surface: #171c25; /* ... */ }
}
:root[data-theme="dark"]          { color-scheme: dark; --site-ink: #e8ebf0; --site-surface: #171c25; /* ... */ }
```

Die dunklen Werte erscheinen zweimal; halten Sie sie an einer Stelle, indem Sie die Datei mit einem Präprozessor bauen, oder nehmen
Sie die Doppelung hin und halten Sie beide Blöcke nebeneinander. admin4 geht den anderen Weg: Sein Skript im Kopf löst „System“
selbst auf, und die Stylesheets lesen nur `html[data-a4-theme="dark"]`, sodass jeder Wert einmal geschrieben wird.

## Bilder und Logos

- Ein Logo für jedes Schema:

  ```tpl
  <picture>
      <source srcset={'logo-dark.svg'|ezimage( 'no' )} media="(prefers-color-scheme: dark)">
      <img src={'logo.svg'|ezimage} alt="{$site.title|wash}" width="160" height="40">
  </picture>
  ```

  (`<picture>` folgt nur dem System; mit einem Umschalter tauschen Sie das Logo stattdessen per CSS über `[data-theme]`.)
- Fotos bleiben, wie sie sind; ein leichtes Abdunkeln (`filter: brightness(0.9)`) im dunklen Modus ist optional.
- Symbole, die mit `currentColor` oder CSS-Masken gezeichnet sind, folgen automatisch der Textfarbe.

## Testen

- Die Entwicklerwerkzeuge des Browsers können `prefers-color-scheme: dark` emulieren (Rendering-Einstellungen).
- Prüfen Sie jeden Seitentyp in beiden Schemata, bei 1440 px und bei 960 px mit Skalierung 2.
- Suchen Sie nach weißen Kästen auf der dunklen Seite: irgendwo ein direktes `#fff` statt eines Tokens.

## Verwandte Seiten

- [Dunkler Modus im Überblick](dunkelmodus-ueberblick.md)
- [Der Theme-Umschalter und der Schutz vor dem ersten Zeichnen](theme-umschalter-und-pre-paint-guard.md)
- [Checkliste: dunkler Modus](checkliste-dunkelmodus.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
