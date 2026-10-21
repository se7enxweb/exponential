---
title: admin4-Token
audience: designer, developer
category: theme-design
order: 540
summary: Die Design-Token --a4-* der Administration (Akzent, Neutraltöne, Zustände, Diagramme, Tiefe, Radius, Abstände, Bedienelemente, Layout, Schrift, Stapelebenen), wie sie sich im dunklen Modus ändern, und die Regeln für ihre Verwendung und ihr Hinzufügen.
keywords: admin4-Token, a4-Token, CSS-Variablen der Administration, --a4-accent, --a4-ink, --a4-card, --a4-space, --a4-radius-s, --a4-text, --a4-z, dunkle Token, Farben der Administration, admin4 tokens, a4 tokens, css variables admin, dark tokens, admin colors
type: help
---
# admin4-Token

Diese Seite ist für Designer, die eine Seite der Administration oder die Admin-Ansicht einer Erweiterung gestalten. Jede Farbe,
jede Größe und jeder Abstand in der Administration stammt aus einer CSS-Custom-Property `--a4-*`, definiert in
`design/admin4/stylesheets/components.css` (eine identische Kopie liegt in `design/admin`). Die vollständige Tabelle steht im
Gestaltungsleitfaden der Administration, [Gestaltung der Administration](../../../bc/6.0/admin4-gestaltung.md), Abschnitt „Tokens“;
diese Seite ist die Zusammenfassung für Designer.

## Inhalt

1. [Die Gruppen](#die-gruppen)
2. [Hell und dunkel](#hell-und-dunkel)
3. [Sie verwenden](#sie-verwenden)
4. [Eines hinzufügen](#eines-hinzufügen)

## Die Gruppen

| Gruppe | Beispiele | Im Dunkeln gleich? |
|---|---|---|
| Akzent | `--a4-accent` (`#f26a21`), `--a4-accent-strong` (gefüllte Bedienelemente mit weißem Text), `--a4-accent-text` (Akzent als Text), `--a4-accent-soft`, `--a4-ring` (Fokusring) | nein |
| Neutral | `--a4-ink`, `--a4-muted`, `--a4-line`, `--a4-soft`, `--a4-card`, `--a4-surface`, `--a4-zebra`, `--a4-field`, `--a4-field-line` | nein |
| Zustände | `--a4-ok`, `--a4-warn`, `--a4-bad`, `--a4-info`, jeweils mit `-bg` und `-line` | nein |
| Diagramme | `--a4-chart-1` bis `--a4-chart-8` | nein |
| Tiefe | `--a4-shadow-s`, `--a4-shadow-m`, `--a4-shadow-up` | nein |
| Radius | `--a4-radius-s` (9 px), der eine Radius der Komponenten; `--a4-radius` (18 px) nur für die Spalten des Rahmens | ja |
| Abstände | `--a4-space-1` bis `--a4-space-7` (4, 8, 12, 16, 24, 32, 48 px) | ja |
| Bedienelemente, Layout | `--a4-control-h` (32 px), `--a4-content-max` (1680 px), `--a4-gutter` | ja |
| Schrift | `--a4-font`, `--a4-mono`, `--a4-text-xs` bis `--a4-text-3xl`, `--a4-text-body`, `--a4-leading` | ja |
| Stapelung | `--a4-z-raised` ... `--a4-z-skip-link` | ja |

## Hell und dunkel

Die hellen Werte stehen auf `:root`. Die dunklen Werte stehen in einem einzigen Block:

![Die Karten des Dashboards im hellen Modus, gezeichnet mit den hellen Werten der admin4-Token](../../../../eng-us/help/images/designers/admin4-tokens-light.webp)

![Dieselben Karten im dunklen Modus, gezeichnet mit den dunklen Werten derselben Token](../../../../eng-us/help/images/designers/admin4-tokens-dark.webp)

```css
html[data-a4-theme="dark"]:not(:where([data-a4-modes="light"])) {
  color-scheme: dark;
  --a4-ink: #e8ebf0;
  --a4-card: #1b212c;
  --a4-accent-text: #ff9a5c;
  /* ... */
}
```

Ein Rahmen, der nur hell ist (das klassische Design), setzt `data-a4-modes="light"` und behält die hellen Werte.

## Sie verwenden

```css
.a4-mymodule-summary {
  padding: var(--a4-space-3) var(--a4-space-4);
  border: 1px solid var(--a4-line);
  border-radius: var(--a4-radius-s);
  background: var(--a4-card);
  color: var(--a4-ink);
  font-size: var(--a4-text-s);
}
```

Eine Länge zwischen zwei Abstandsstufen nimmt die nähere Stufe (der Leitfaden listet die Zuordnung). `z-index` ist immer ein
Token `--a4-z-*`; der Test schlägt bei jeder anderen Zahl fehl (ausgenommen 0, 1 und -1 innerhalb einer Komponente).

## Eines hinzufügen

Selten nötig: Versuchen Sie zuerst die vorhandenen Token. Ist ein neues Farbtoken nötig:

1. Fügen Sie es `:root` **und** dem dunklen Block hinzu, in derselben Änderung, mit einer Notiz zum Kontrast;
2. fügen Sie es beiden Kopien von `components.css` hinzu (`design/admin4` und `design/admin`, die identisch bleiben müssen);
3. führen Sie den [Test der Admin-Gestaltungsregeln](der-test-der-admin-gestaltungsregeln.md) aus.

Fügen Sie nie einen lokalen Alias (`--mymodule-bg: var(--a4-card)`) oder einen zweiten Akzent hinzu.

## Verwandte Seiten

- [admin4-Komponenten](admin4-komponenten.md)
- [Design-Token](design-tokens.md)
- [Farbkontrast](farbkontrast.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
