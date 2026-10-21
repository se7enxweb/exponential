---
title: Kein Inline-CSS
audience: designer
category: theme-design
order: 610
summary: Warum Templates keine style-Blöcke und keine style-Attribute tragen, wohin das CSS stattdessen gehört, wie Sie mit Werten pro Element umgehen (Prozentwerte, Farben aus Inhalten, berechnete Breiten) und die eine dokumentierte Ausnahme in der Administration.
keywords: Inline-CSS, style-Attribut, style-Block, keine Inline-Stile, Werte pro Element, data-Attribute, Custom Properties, pct-Klassen, CSP style-src, nur Stylesheets, inline css, style attribute, style block, no inline styles, per element values, data attributes, custom properties, pct classes, csp style-src, stylesheet only
type: help
---
# Kein Inline-CSS

Diese Seite ist für Designer, die Templates schreiben. Die Regel des Projekts ist kurz: **kein `<style>`-Block und kein `style=""`-Attribut
in einem Template.** Alles CSS lebt im Ordner `stylesheets/` des Designs und wird über ezjscore geladen. Die Seite erklärt, warum, und wie
Sie mit den Fällen umgehen, die Inline-CSS zu brauchen scheinen.

## Inhalt

1. [Warum](#warum)
2. [Wohin das CSS gehört](#wohin-das-css-gehört)
3. [Werte pro Element](#werte-pro-element)
4. [Stile, die aus Inhalten kommen](#stile-die-aus-inhalten-kommen)
5. [Inline-CSS finden](#inline-css-finden)

## Warum

- Eine Content Security Policy mit `style-src 'self'` blockiert Inline-Stile ([CSP-sicheres Design](csp-sicheres-design.md)).
- Inline-CSS wird nicht gepackt oder zwischengespeichert und mit jeder Seite erneut gesendet.
- Kopien laufen auseinander: Die Administration hatte einmal 55 Templates mit eigenen `<style>`-Blöcken, vier verschiedene Akzentfarben
  und Dutzende weiße Karten, die den dunklen Modus ignorierten (die Geschichte steht in [Gestaltung der Administration](../../../../eng-us/bc/6.0/admin4-styling.md),
  „Why“).
- Inline-Stile schlagen Regeln aus Stylesheets, sodass ein Theme das Element nicht mehr neu gestalten kann.

## Wohin das CSS gehört

| CSS für | Gehört in |
|---|---|
| das ganze Design | Ihr Haupt-Stylesheet, geladen von `ezcss_load` im Pagelayout |
| eine Art von Seite | eine eigene Datei, die das View-Template mit `ezcss_require` anfordert |
| eine Modulseite der Administration | zuerst die gemeinsamen Komponenten, dann `stylesheets/modules/<group>.css` oder das Admin-Stylesheet der Erweiterung |

## Werte pro Element

Ein Wert, der wirklich zu einem Element gehört (ein Fortschritt in Prozent, die Höhe eines Balkens, eine von einem Redakteur gewählte Farbe):

- **Eine Klasse aus einem festen Satz.** admin4 hat `.a4-pct-0` bis `.a4-pct-100` in Schritten von 4 für Anzeigen und Balken:

  ```tpl
  <span class="a4-meter"><span class="a4-pct-{$percent|div( 4 )|round|mul( 4 )}"></span></span>
  ```

- **Ein `data-*`-Attribut**, das ein Skript in eine CSS-Custom-Property umwandelt:

  ```tpl
  <div class="chart-bar" data-value="{$value|wash}"></div>
  ```

  ```js
  document.querySelectorAll('.chart-bar').forEach(function (el) {
    el.style.setProperty('--value', el.dataset.value);   /* set from a script file: allowed by style-src 'self' */
  });
  ```

  ```css
  .chart-bar { inline-size: calc(var(--value) * 1%); }
  ```

  Eine Stileigenschaft aus einer Skriptdatei zu setzen, ist kein Inline-Stilattribut im Markup und wird von einer Richtlinie ohne
  `'unsafe-inline'` erlaubt.

## Stile, die aus Inhalten kommen

Rich Text kann von Redakteuren gewählte Ausrichtungen oder Klassen enthalten; die Datentyp-Templates machen daraus Klassen, nicht
`style=""`. Behalten Sie das in Overrides von `ezxmltags/*.tpl` bei. Beachten Sie, dass die CSP-Seite empfiehlt, Websites mit von Inhalten
erzeugtem `style=""` (eingebettete Player, alter Rich Text) mit `Mode=report-only` zu beginnen.

## Inline-CSS finden

```bash
grep -rn -E "<style|style=\"" extension/mytheme/design --include=*.tpl
```

Bei Admin-Designs schlägt `tests/tests/design/AdminStylingRulesTest.php` bei jedem Vorkommen fehl; die einzige Ausnahme ist
`design/admin/templates/pagelayout.tpl` für die Spaltenbreiten des Editors, bevor die Seite gezeichnet wird, im Test dokumentiert
([Der Test der Admin-Gestaltungsregeln](der-test-der-admin-gestaltungsregeln.md)).

## Verwandte Seiten

- [Kein Inline-JavaScript](kein-inline-javascript.md)
- [Stylesheets laden](stylesheets-laden.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
