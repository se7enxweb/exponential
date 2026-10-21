---
title: "Inline-Skripte und -Styles entfernen"
audience: designer, developer
category: theme-lifecycle
order: 130
summary: Ein gepflegtes Theme in Richtung einer strikten Content Security Policy bewegen - Inline-Skriptblöcke, Event-Handler-Attribute, javascript:-Links und style-Attribute finden und in Dateien und Datenattribute verschieben - so, wie es die veröffentlichten Themes von 7x im Oktober 2026 getan haben.
keywords: Inline-Skripte, Inline-Styles, CSP, unsafe-inline, onclick, javascript:-Links, Datenattribute, style-Attribut, Migration, inline scripts, inline styles, javascript: links, data attributes, style attribute, migration
type: help
---
# Inline-Skripte und -Styles entfernen

Diese Seite ist für Designer, die ein Theme betreuen, das geschrieben wurde, bevor Content Security Policies üblich waren.
Wie CSP-sichere Templates gehen, steht in [CSP-sicheres Design](../theme-design/csp-sicheres-design.md),
[Kein Inline-JavaScript](../theme-design/kein-inline-javascript.md) und [Kein Inline-CSS](../theme-design/kein-inline-css.md);
diese Seite ist die Migrationsroutine.

## Warum jetzt

Exponential sendet eine Content Security Policy aus `site.ini [ContentSecurityPolicy]`: standardmäßig report-only in der
Administration und aus auf Websites, mit `script-src 'self'` und `style-src 'self'`
([Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)). Ein Website-Betreiber, der sie für seine
Website einschaltet, braucht ein Theme ohne Inline-Code.

Die Themes von 7x haben diese Migration in ihren Releases vom 17. Oktober 2026 vollzogen: `cjw_themes_jumper` v0.1.6
(„the starter design without inline scripts“), `sevenx_themes_simple` v1.0.24 und `sevenx_themes_media` v1.5.70.

## Schritt für Schritt

1. **Finden** Sie Inline-Code:

   ```bash
   grep -rn -E "<script[^>]*>[^<]|on(click|change|load|submit)=|javascript:|style=\"" extension/mytheme/design
   ```

2. **Verschieben Sie Skripte in Dateien** in `design/mytheme/javascript/` und laden Sie sie über die Skriptliste des
   Designs.
3. **Ersetzen Sie Event-Attribute und `javascript:`-Links** durch `href="#"` oder Schaltflächen plus `data-*`-Attribute,
   die eine Skriptdatei liest, wie es `cjw_themes_jumper` 0.1.6 mit `data-cjw-bubble` getan hat.
4. **Übergeben Sie Werte** aus Templates in `data-*`-Attributen, statt sie in Skriptblöcke zu schreiben.
5. **Ersetzen Sie `style=""`** durch Klassen im Stylesheet; Werte pro Element gehören in `data-*`-Attribute oder in
   Custom Properties, die von einer Klasse gesetzt werden.
6. **Testen** Sie mit der Richtlinie im Modus report-only auf einem Test-Siteaccess (`Mode=report-only`) und lesen Sie die
   Browserkonsole: keine Meldungen.

## Verwandte Seiten

- [Eine CSP-Prüfung bei jedem Release](csp-pruefung-pro-release.md)
- [Checkliste: CSP](../theme-design/checkliste-csp.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
