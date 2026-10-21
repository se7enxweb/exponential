---
title: Kein Inline-JavaScript
audience: designer, developer
category: theme-design
order: 620
summary: Designs ohne Inline-Skriptcode schreiben: keine script-Blöcke mit Code, keine on*-Handler-Attribute, keine javascript:-Links; Event-Delegation mit data-Attributen, JSON-Konfigurationsblöcke, progressive Verbesserung und die Tests, die Templates prüfen.
keywords: Inline-JavaScript, Inline-Skript, onclick, Event-Handler-Attribut, javascript-URL, addEventListener, Event-Delegation, data-Attribute, JSON-Konfiguration, CSP script-src, unaufdringliches JavaScript, inline javascript, inline script, event handler attribute, javascript url, event delegation, data attributes, json config, unobtrusive javascript
type: help
---
# Kein Inline-JavaScript

Diese Seite ist für Designer und Frontend-Entwickler. Templates von Exponential-Designs tragen keinen Skriptcode: Jede Zeile JavaScript
steht in einer Datei unter `javascript/`, geladen über ezjscore. Die Seite zeigt die Muster, die Inline-Code ersetzen.

## Inhalt

1. [Was als inline gilt](#was-als-inline-gilt)
2. [Handler: data-Attribute und Delegation](#handler-data-attribute-und-delegation)
3. [Werte für Skripte: JSON-Blöcke](#werte-für-skripte-json-blöcke)
4. [Progressive Verbesserung](#progressive-verbesserung)
5. [Prüfen](#prüfen)

## Was als inline gilt

Dieselben drei Arten, die die Designtests zählen (`AdminContentSecurityPolicyTest`, `ExtensionInlineScriptsTest`):

- ein `<script>`-Element mit Code darin (ein `<script src="...">`, ein JSON-Datenblock und ein `<template>` sind in Ordnung);
- ein Event-Handler-Attribut: `onclick=""`, `onchange=""`, `onload=""` ...;
- eine `javascript:`-URL.

Alle drei werden von einer Richtlinie mit `script-src 'self'` blockiert.

## Handler: data-Attribute und Delegation

Das Markup sagt, *was*; die Skriptdatei sagt, *wie*:

```tpl
<button type="button" class="js-toggle" data-target="site-nav" aria-expanded="false" aria-controls="site-nav">
    {'Menu'|i18n( 'design/mytheme' )}
</button>
<nav id="site-nav" hidden>...</nav>
```

```js
/* design/mytheme/javascript/mytheme.js */
document.addEventListener('click', function (event) {
  var button = event.target.closest('.js-toggle');
  if (!button) return;
  var target = document.getElementById(button.dataset.target);
  var open = button.getAttribute('aria-expanded') === 'true';
  button.setAttribute('aria-expanded', String(!open));
  target.hidden = open;
});
```

Ein Listener am Dokument behandelt jeden Umschalter, auch solche, die später per AJAX hinzukommen. Die Administration verwendet dieselbe
Idee mit `data-a4-*`-Aktionen (die CSP-Seite führt sie als Ersatz für Handler auf).

## Werte für Skripte: JSON-Blöcke

```tpl
<script type="application/json" id="mytheme-map">{json_encode( hash(
    'lat', $node.data_map.location.content.latitude,
    'lng', $node.data_map.location.content.longitude,
    'label', $node.name ) )}</script>
```

```js
var data = JSON.parse(document.getElementById('mytheme-map').textContent);
```

`json_encode` (ezjscore) maskiert die Werte für JSON. Ein JSON-Block ist Daten, kein Code, und wird nicht blockiert.

## Progressive Verbesserung

Bauen Sie Seiten, die ohne das Skript funktionieren, und fügen Sie dann Verhalten hinzu:

- ein Menü, das eine einfache Liste von Links ist; das Skript macht daraus einen Umschalter;
- ein Formular, das normal absendet; das Skript fügt Live-Validierung oder AJAX hinzu;
- `<details>` für Aufklappbereiche, `<dialog>` für Dialoge.

Das Kopf-Skript von admin4 setzt die Klasse `a4-js` am `<html>`, wenn Skripte laufen, sodass Stylesheets den geskripteten Zustand erst dann
zeichnen. Machen Sie es in Ihrem Design genauso (setzen Sie eine Klasse aus Ihrer Kopf-Skriptdatei).

## Prüfen

```bash
grep -rn -E "on(click|change|load|submit|keyup|keydown|mouseover)=|javascript:|<script>" extension/mytheme/design --include=*.tpl
```

und sehen Sie sich die Konsole des Browsers auf jedem Seitentyp an, mit der Richtlinie der Website im Modus report-only.

## Verwandte Seiten

- [CSP-sicheres Design](csp-sicheres-design.md)
- [JavaScript laden](javascript-laden.md)
- [Kein Inline-CSS](kein-inline-css.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
