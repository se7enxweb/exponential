---
title: "Von Designtool-Variablen zu CSS Custom Properties"
audience: theme-designer
category: theme-design
order: 16
summary: Wie Sie die Variablen eines Designtools (Farben, Größen, Abstände, Modi) in das Token-Stylesheet eines Exponential-Themes übertragen - ein Namensschema, heller und dunkler Modus als zwei Wertesätze, eine Exporttabelle, die Datei tokens.css und wie beide Seiten im Gleichschritt bleiben.
keywords: Theme-Designer, Designtool, Variablen, Design-Tokens, CSS Custom Properties, Export, Modi, hell, dunkel, tokens.css, Benennung, Übergabe, theme designer, design tool, variables, design tokens, export, modes, light, dark, naming, handoff
type: help
---
# Von Designtool-Variablen zu CSS Custom Properties

Diese Seite ist für Theme-Designer, die Farben, Größen und Abstände als Variablen in einem Designtool führen und möchten,
dass sie unverändert im Theme ankommen. Die meisten aktuellen Designtools unterstützen Variablen mit Modi (zum Beispiel
einen hellen und einen dunklen Modus). Ein Exponential-Theme drückt dasselbe als CSS Custom Properties in einem
Token-Stylesheet aus, mit einem hellen und einem dunklen Wertesatz. Die Regeln für Tokens stehen in
[Design-Tokens](../designer/theme-design/design-tokens.md); diese Seite ist die Brücke.

## Inhalt

1. [Die Zuordnung](#die-zuordnung)
2. [Ein Namensschema auf beiden Seiten](#ein-namensschema-auf-beiden-seiten)
3. [Modi werden zu Selektoren](#modi-werden-zu-selektoren)
4. [Die Exporttabelle](#die-exporttabelle)
5. [tokens.css schreiben](#tokenscss-schreiben)
6. [Sie laden](#sie-laden)
7. [Beide Seiten im Gleichschritt halten](#beide-seiten-im-gleichschritt-halten)

## Die Zuordnung

| Designtool | Exponential-Theme |
|---|---|
| Sammlung primitiver Farben (Abstufungen) | nicht im CSS, oder ein kommentierter Referenzblock ([Farbpaletten und Farbabstufungen](farbpaletten-und-farbabstufungen.md)) |
| Semantische Farbvariable (Text, Fläche, Akzent) | Farb-Token `--site-*` |
| Heller Modus / dunkler Modus | Werte in `:root` / dunkle Werte ([Modi werden zu Selektoren](#modi-werden-zu-selektoren)) |
| Zahlenvariablen für Abstände | Tokens `--site-space-*` |
| Zahlenvariablen für Schriftgrößen | Tokens `--site-text-*` |
| Variable für den Eckenradius | der eine Token `--site-radius` |
| Textstile | Klassen im Komponenten-Stylesheet, aus Tokens gebaut |
| Markenmodi (mehrere Marken) | ein Marken-Stylesheet pro Marken-Design ([Für mehrere Marken im Multisite-Betrieb gestalten](fuer-mehrere-marken-in-multisite-gestalten.md)) |

## Ein Namensschema auf beiden Seiten

Benennen Sie die Variable im Designtool genau wie den Token, ohne das Präfix und die Bindestriche, die das Werkzeug
nicht mag:

| Designtool | CSS |
|---|---|
| `color/ink` | `--site-ink` |
| `color/accent` | `--site-accent` |
| `space/3` | `--site-space-3` |
| `text/l` | `--site-text-l` |

Verwenden Sie, wenn Sie möchten, das eigene Präfix Ihres Themes statt `site`, aber ein Präfix für das ganze Design (die
Administration verwendet `--a4-`). Benennen Sie eine semantische Variable nie nach ihrem Wert (`blue`), denn ihr dunkler
Wert wird nicht blau sein.

## Modi werden zu Selektoren

Ein Designtool schaltet einen Frame zwischen Modi um; CSS schaltet die Werte per Selektor um. Das Muster, das die Hilfe
für Designer für ein öffentliches Design verwendet, folgt der Systemeinstellung und beachtet außerdem eine ausdrückliche
Wahl, sobald es einen Umschalter gibt ([Dunkelmodus mit prefers-color-scheme](../designer/theme-design/dunkelmodus-mit-prefers-color-scheme.md)):

```css
:root { /* light values */ }

@media (prefers-color-scheme: dark) {
  :root:not([data-theme="light"]) { color-scheme: dark; /* dark values */ }
}
:root[data-theme="dark"] { color-scheme: dark; /* dark values */ }
```

Nur Farb-Tokens wechseln zwischen den Modi. Abstände, Größen, Radius und Schriften werden einmal in `:root` definiert.

## Die Exporttabelle

Die meisten Werkzeuge können Variablen als JSON exportieren oder auflisten. Machen Sie aus dem Export, in welchem
Format auch immer, eine Tabelle, die der Entwickler Zeile für Zeile prüfen kann, und bewahren Sie sie im Repository
des Themes auf:

| Token | Hell | Dunkel | Hinweise |
|---|---|---|---|
| `--site-ink` | `#1f2430` | `#e8ebf0` | Fließtext |
| `--site-surface` | `#ffffff` | `#171c25` | Seitenhintergrund |
| `--site-accent` | `#0b5cad` | `#7fb3f0` | Links, primäre Schaltflächen |
| `--site-space-3` | `1rem` | | in beiden gleich |
| `--site-radius` | `0.5rem` | | in beiden gleich |

Diese Beispielwerte sind der Startsatz aus [Design-Tokens](../designer/theme-design/design-tokens.md#ein-startsatz);
verwenden Sie Ihre eigenen. Rechnen Sie Pixelzahlen für Abstände und Schrift in `rem` um (durch 16 teilen).

## tokens.css schreiben

Die Token-Datei ist ein einfaches Stylesheet im Design: `design/mytheme/stylesheets/tokens.css`. Schreiben Sie sie
nach der Exporttabelle, in derselben Reihenfolge, mit den hellen Werten in `:root` und den dunklen Werten in den beiden
dunklen Selektoren. Erzeugt Ihr Team sie in einem Build-Schritt aus dem Export, committen Sie die erzeugte Datei, denn
der Packer liest Stylesheets zur Laufzeit aus dem Designordner; er hat keinen eigenen Build-Schritt.

Setzen Sie einen Token-Wert nie in ein Template, einen `<style>`-Block oder ein `style=""`-Attribut
([Kein Inline-CSS](../designer/theme-design/kein-inline-css.md)).

## Sie laden

Das Token-Stylesheet lädt zuerst, vor den Komponenten, über die Stylesheet-Liste des Designs und den Packer
([Stylesheets laden](../designer/theme-design/stylesheets-laden.md)). Eine Änderung erreicht Browser über die gepackte
Datei, deren Name die Änderungszeit trägt; hängen Sie nie eine `?v=`-Abfrage an den Link
([Keine Versionsabfragen an Asset-Links](../designer/theme-design/keine-versionsparameter-an-asset-links.md)).

## Beide Seiten im Gleichschritt halten

- Die Exporttabelle ist die maßgebliche Quelle; Designtool und `tokens.css` folgen ihr beide.
- Ein neuer Token erhält seinen dunklen Wert in derselben Änderung.
- Prüfen Sie einmal pro Release: Tokens im CSS, die kein Stylesheet verwendet, und Variablen im Designtool ohne Token
  sind beide abgedriftet. Die [Styleguide-Seite](eine-styleguide-seite-bauen.md) zeigt die programmierten Werte.

## Verwandte Seiten

- [Design-Tokens](../designer/theme-design/design-tokens.md)
- [Farbpaletten und Farbabstufungen](farbpaletten-und-farbabstufungen.md)
- [Dunkelmodus und Design-Tokens in Themes](../erweiterungsentwicklung/teil-6-theme-entwickler/dunkler-modus-und-tokens-in-themes.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
