---
title: Content Security Policy für Ihren Code
audience: app-creator, developer
category: app-creation
order: 570
summary: Templates und Skripte schreiben, die unter der Content Security Policy von Exponential laufen - keine Inline-Skripte, -Handler oder -Styles; Seitenskripte als Dateien, data-*-Attribute und JSON-Datenblöcke; Quellen hinzufügen; das CSP-Berichtslog lesen.
keywords: Content Security Policy, CSP, Inline-Skript, Style-Attribut, Nonce, data-Attribute, JSON-Datenblock, Berichtsmodus, durchsetzen, Anwendungsentwickler, inline script, onclick, style attribute, nonce, data attributes, JSON data block, AdditionalSources, report-only, enforce, csp.log, app creator
type: help
---
# Content Security Policy für Ihren Code

Diese Seite ist für Anwendungsentwickler, deren Templates und Skripte unter einer Content Security Policy funktionieren
müssen. Die Administration läuft unter einer solchen Richtlinie und blockiert Inline-Code, sobald sie durchgesetzt wird;
öffentliche Websites können sie einschalten ([Content Security Policy](../../../funktionen/6.0/content-security-policy.md)).

## Inhalt

1. [Die mitgelieferte Richtlinie](#die-mitgelieferte-richtlinie)
2. [Was sie verbietet](#was-sie-verbietet)
3. [Wie Sie es stattdessen schreiben](#wie-sie-es-stattdessen-schreiben)
4. [Quellen von anderen Hosts](#quellen-von-anderen-hosts)
5. [Ihre Seiten prüfen](#ihre-seiten-prüfen)

## Die mitgelieferte Richtlinie

`site.ini [ContentSecurityPolicy]`: `Mode=auto` (nur Bericht in den Siteaccesses der Administration, sonst aus),
`Directives[]` mit `default-src 'self'`, `script-src 'self'`, `style-src 'self'`, `img-src 'self' data: blob:`,
`object-src 'none'`, `frame-ancestors 'self'` und weiteren. Mit `Nonce=enabled` trägt die Richtlinie der
Administration eine Nonce pro Anfrage.

## Was sie verbietet

| Verboten | Warum |
|---|---|
| `<script>` mit Code in Templates | `script-src 'self'` erlaubt nur Skriptdateien |
| Attribute `onclick=`, `onload=` ... | Inline-Handler |
| `javascript:`-Links | Inline-Code |
| `<style>`-Elemente, `style=""`-Attribute | `style-src 'self'` erlaubt nur Stylesheet-Dateien |
| `eval()`, `new Function()` | kein unsafe-eval |
| Skripte und Schriften von anderen Hosts | nicht in der Richtlinie, sofern nicht hinzugefügt |

## Wie Sie es stattdessen schreiben

| Statt | Schreiben Sie |
|---|---|
| Inline-Skript in der Seite | eine Datei `design/<design>/javascript/...`, ausgegeben mit `{ezscript( array( 'page/expdemo_list.js' ) )}` |
| Werte für ein Skript | `data-*`-Attribute, für HTML gewaschen, oder JSON: `data-conf="{json_encode( hash( ... ) )|wash}"` |
| ein Konfigurationsobjekt | `<script type="application/json" id="...">`, gelesen von einer Skriptdatei (Daten, kein Code) |
| `onclick` | eine Klasse oder ein `data-*`-Attribut und `addEventListener` in der Skriptdatei |
| `style="width: 40%"` | eine Klasse oder eine CSS-Custom-Property, die eine Stylesheet-Klasse setzt |
| Bestätigungs- und Umschaltknöpfe in der Administration | die gemeinsamen `data-a4-*`-Aktionen von `a4_actions.js` (`data-a4-confirm`, `data-a4-toggle-checkboxes` ...) |

In Admin-Designs folgen Styles außerdem den Admin-Regeln: gemeinsame Komponenten und Tokens, Modul-CSS in
`stylesheets/modules/` ([Styling von admin4](../../../bc/6.0/admin4-gestaltung.md)).

## Quellen von anderen Hosts

```ini
# extension/expdemo/settings/site.ini.append.php
[ContentSecurityPolicy]
AdditionalSources[]=script-src https://maps.example.org
AdditionalSources[]=connect-src https://api.example.org
```

`AdditionalSources[]` ist eine Liste, sodass Erweiterungen und Siteaccesses jeweils ihre Hosts hinzufügen können.

## Ihre Seiten prüfen

- Öffnen Sie jede Seite mit der Browserkonsole: Verstöße werden dort gemeldet.
- Berichte gehen an `csp/report` und werden nach `var/log/csp.log` geschrieben.
- Der Test `tests/tests/design/AdminContentSecurityPolicyTest.php` des Kernels prüft die Templates der Admin-Design-Ketten,
  einschließlich der Templates aktiver Erweiterungen; eine Erweiterung mit Inline-Code fällt dort auf.

## Verwandte Seiten

- [Stylesheets und Skripte für Ihre Website](../website-ersteller/stylesheets-und-skripte.md)
- [Sicherheit für Anwendungen](sicherheit-fuer-anwendungen.md)
- [Standard für die Webplattform](../../../standards/webplattform.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
