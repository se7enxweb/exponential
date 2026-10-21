---
title: "Checkliste: CSP"
audience: designer, developer
category: theme-design
order: 890
summary: Eine Checkliste zur Content Security Policy eines Designs: keine Inline-Skripte, Handler, javascript-Links oder Inline-Stile, Konfiguration als JSON-Daten, Code vor dem ersten Zeichnen in Dateien, Nonces nie in zwischengespeicherter Ausgabe, dokumentierte Fremdursprünge und eine Phase im Modus report-only vor dem Durchsetzen.
keywords: CSP-Checkliste, Checkliste Content Security Policy, Prüfung auf Inline-Skripte, Prüfung auf Inline-Stile, Nonce, report-only, enforce, csp.log, Sicherheitsprüfung eines Designs, csp checklist, content security policy checklist, inline script check, inline style check, design security review
type: help
---
# Checkliste: CSP

Diese Seite ist für Designer und Entwickler, die sicherstellen, dass ein Design unter einer durchgesetzten Content Security Policy
funktioniert. Hintergrund: [CSP-sicheres Design](csp-sicheres-design.md).

## Inhalt

1. [Templates](#templates)
2. [Skripte und Stile](#skripte-und-stile)
3. [Ursprünge](#ursprünge)
4. [Vor dem Durchsetzen](#vor-dem-durchsetzen)

## Templates

- [ ] Kein `<script>`-Element mit Code (ein Skript mit `src`, ein JSON-Datenblock und ein `<template>` sind in Ordnung).
- [ ] Keine Handler-Attribute `on*=`.
- [ ] Keine `javascript:`-URLs.
- [ ] Keine `<style>`-Blöcke und keine `style=""`-Attribute ([Kein Inline-CSS](kein-inline-css.md)).
- [ ] `csp_nonce()` nur, wo eine Datei wirklich unmöglich ist, und nie in einem Cache-Block oder einem im View-Cache
  gespeicherten Template.

```bash
grep -rn -E "<script>|on(click|change|load|submit|input|keyup|keydown|focus|blur|mouseover)=|javascript:|<style|style=\"" \
     extension/mytheme/design --include=*.tpl
```

## Skripte und Stile

- [ ] Jedes Skript ist eine Datei unter `javascript/`, geladen mit den Operatoren von ezjscore ([Kein Inline-JavaScript](kein-inline-javascript.md)).
- [ ] Werte für Skripte kommen aus `data-*`-Attributen oder Blöcken `<script type="application/json">`.
- [ ] Code, der vor dem ersten Zeichnen laufen muss, ist eine kleine Datei, die im Kopf geladen wird.
- [ ] Kein `eval`, kein `new Function` und keine Zeichenketten als Argumente für `setTimeout` in den Skripten des Designs.

## Ursprünge

- [ ] Jeder fremde Ursprung, den das Design braucht, steht im README des Themes, mit der Direktive, die er braucht.
- [ ] Schriften, Symbole und Bibliotheken liegen lokal, wenn es keinen Grund dagegen gibt.

## Vor dem Durchsetzen

- [ ] Der Siteaccess lief mit `[ContentSecurityPolicy] Mode=report-only`, während jeder Seitentyp und jeder Dialog benutzt wurde.
- [ ] Die Konsole des Browsers und `var/log/csp.log` (mit `ReportEndpoint=enabled`) zeigen keine Meldungen für das Design.
- [ ] Bei Admin-Designs und Erweiterungen: `AdminContentSecurityPolicyTest` und `ExtensionInlineScriptsTest` laufen durch.
- [ ] Dann `Mode=enforce` zuerst auf einem Siteaccess, INI-Cache geleert, PHP neu geladen.

## Verwandte Seiten

- [CSP-sicheres Design](csp-sicheres-design.md)
- [Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
