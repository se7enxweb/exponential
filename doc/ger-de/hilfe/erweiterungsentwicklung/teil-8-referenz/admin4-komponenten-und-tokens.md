---
title: admin4-Komponenten und -Tokens
audience: developer, designer
category: extension-development
order: 350
summary: Eine Referenzkarte der gemeinsamen Komponenten (.a4-*-Klassen) und Design-Tokens (--a4-*-Custom-Properties) der Administration, die die Administrationsseiten einer Erweiterung verwenden, mit den Regeln, die sie im hellen und im dunklen Modus funktionieren lassen.
keywords: admin4, Komponenten, Tokens, Dunkelmodus, dunkler Modus, Stylesheet, components, tokens, a4-card, a4-btn, a4-table, a4-notice, a4-badge, a4-toolbar, a4-field, --a4-ink, --a4-accent, --a4-space, dark mode, components.css
type: help
---
# admin4-Komponenten und -Tokens

Teil 8, Referenz 31. Die Administration gestaltet jede Seite mit einem Satz gemeinsamer Komponentenklassen (`.a4-*`)
und einem Satz Design-Tokens (`--a4-*`), die in der `components.css` der Administrationsdesigns definiert sind. Die
Administrationsseiten einer Erweiterung verwenden sie ebenfalls. Die vollständige Referenz mit jeder Klasse, jedem
Zustand und jedem Token ist [Die Administration gestalten](../../../../eng-us/bc/6.0/admin4-styling.md); wie Sie die Seiten
einer alten Erweiterung umstellen, beschreibt
[admin4-Styling für Erweiterungsseiten](../teil-4-aktualisieren/admin4-styling-fuer-erweiterungsseiten.md).

## Die meistverwendeten Komponenten

| Bedarf | Klassen |
|---|---|
| Seitenrahmen | `.a4-page` |
| Seitentitel mit Aktionen | `.a4-pagehead` > `.a4-pagehead-main` (`.a4-pagehead-title`, `.a4-pagehead-sub`) + `.a4-pagehead-actions` |
| Karte | `.a4-card` > `.a4-card-head`, `.a4-card-body`, `.a4-card-foot`; Zustände `.is-ok`, `.is-warn`, `.is-bad`, `.is-info`, `.is-flush` |
| Schaltflächen | `.a4-btn`, `.a4-btn-primary`, `.a4-btn-danger`, `.a4-btn-ghost`, `.a4-btn-small`; `.a4-actions` für eine Reihe |
| Werkzeugleiste | `.a4-toolbar` (`.is-bottom`, `.is-sticky`) |
| Tabelle | `.a4-table-wrap` > `table.a4-table` (`.is-compact`) |
| Formular | `.a4-field` > `.a4-label` + `.a4-input`; `.a4-field-error`; `.a4-check`, `.a4-radios` |
| Meldungen | `.a4-notice` mit `.is-ok`, `.is-warn`, `.is-bad`, `.is-info` |
| Status | `.a4-badge`, `.a4-tag` mit denselben Zuständen; `.a4-status` mit `.a4-dot` |
| Leere Liste | `.a4-empty` |
| Hilfsklassen | `.a4-sr-only`, `.a4-muted`, `.a4-meta`, `.a4-help`, `.a4-nowrap`, `.a4-code` |
| Symbole | `.a4-icon` mit `.a4-bi-<name>` |

Zustände sind `.is-*`-Klassen an der Komponente.

## Die meistverwendeten Tokens

| Gruppe | Tokens |
|---|---|
| Text | `--a4-ink`, `--a4-muted` |
| Flächen | `--a4-surface`, `--a4-card`, `--a4-soft` |
| Linien | `--a4-line`, `--a4-line-strong` |
| Akzent | `--a4-accent`, `--a4-accent-strong` (Füllungen mit weißem Text), `--a4-accent-text` (Text), `--a4-accent-soft` |
| Zustände | `--a4-ok`, `--a4-warn`, `--a4-bad`, `--a4-info`, jeweils mit `-bg` und `-line` |
| Abstände | `--a4-space-1` bis `--a4-space-7` (4, 8, 12, 16, 24, 32, 48 px) |
| Radius | `--a4-radius-s` |
| Schrift | `--a4-font`, `--a4-mono`, `--a4-text-xs` bis `--a4-text-3xl` |
| Bedienelemente | `--a4-control-h` |

Jedes Farb-Token hat einen dunklen Wert; der Modus ist `html[data-a4-theme="light"]` oder `"dark"`, und eine Regel,
die aus Tokens aufgebaut ist, braucht für den dunklen Modus nichts weiter.

## Regeln für Erweiterungsseiten

1. Kein `<style>` und kein `style=""` in Templates.
2. Zuerst die Komponenten; eigene Klassen nur für das, was diese nicht leisten, mit dem Präfix Ihrer Erweiterung.
3. Für Farben, Abstände, Radius und Schrift nur Tokens; keine lokalen Aliase, keine fest eingetragenen Werte.
4. Ihr Stylesheet kommt in `design.ini [StylesheetSettings] BackendCSSFileList[]`.
5. Prüfen Sie den hellen und den dunklen Modus, bei normaler Breite und bei 200 % Zoom.

## Verwandte Seiten

- Zurück: [Die JavaScript-API von expui](expui-javascript-api.md)
- Weiter: [Das Hilfesystem für Erweiterungen](hilfesystem-fuer-erweiterungen.md)
- [Administrationsseiten im admin4-Stil](../teil-3-erweiterungsentwickler/admin-seiten-im-admin4-stil.md)
- [admin4-Tokens](../../../../eng-us/help/designers/theme-design/admin4-tokens.md), [admin4-Komponenten](../../../../eng-us/help/designers/theme-design/admin4-components.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
