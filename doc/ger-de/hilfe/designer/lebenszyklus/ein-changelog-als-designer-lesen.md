---
title: "Ein Changelog als Designer lesen"
audience: designer
category: theme-lifecycle
order: 50
summary: Worauf Sie in einem Changelog von Exponential achten, wenn Sie ein Theme betreuen - Templates, die Sie überschreiben, Design-Einstellungen, Operatoren, JavaScript-Bibliotheken, Sicherheits-Header, PHP-Versionen, Barrierefreiheit - mit echten Beispielen aus dem Changelog von 6.0.15.
keywords: Changelog, lesen, Auswirkung, Templates, Operatoren, JavaScript, CSP, PHP-Version, Barrierefreiheit, 6.0.15, changelog, reading, impact, templates, operators, PHP version, accessibility
type: help
---
# Ein Changelog als Designer lesen

Diese Seite ist für Designer, die ein Changelog durchsehen. Die meisten Einträge betreffen ein Theme nicht; einige
betreffen es sehr.

## Worauf Sie achten

| Achten Sie auf | Warum | Beispiel |
|---|---|---|
| Templates, die Sie überschreiben | Ihrer Kopie fehlt die Korrektur oder das neue Markup | Eine Korrektur in einem Standard-Template, das Sie in Ihr Design kopiert haben |
| Template-Operatoren und -Funktionen | Neue, geänderte oder entfernte Operatoren | Ein Operator, den Ihre Templates aufrufen |
| Design-Einstellungen (`design.ini`, `site.ini [DesignSettings]`) | Das Laden von Stylesheets und Skripten | `design.ini` kennzeichnet `CSSFileList[]` als veraltet zugunsten von `FrontendCSSFileList[]` |
| JavaScript-Bibliotheken | Ihre Skripte können sich auf alte APIs verlassen | jQuery 4 und die Entfernung von YUI (1. und 2. Oktober 2026) |
| Sicherheits-Header und die Content Security Policy | Inline-Skripte können gemeldet oder blockiert werden | Die CSP der Administration, standardmäßig report-only |
| PHP-Versionen | Ihr PHP-Code muss darauf laufen | „PHP 8.0 is the oldest supported version“ (5. Oktober 2026), dann PHP 7.4 ohne Support nutzbar (29. Oktober 2026), dann PHP 7.4 ohne Support nutzbar (29. Oktober 2026) |
| Änderungen der Barrierefreiheit | Die Erwartungen des Produkts steigen | Änderungen an Kontrast oder Fokus in den gemeinsamen Komponenten |

## Schritt für Schritt

1. Öffnen Sie das Changelog des Releases ([Releases von Exponential verfolgen](exponential-releases-verfolgen.md)).
2. Durchsuchen Sie es nach den Wörtern der ersten Spalte und nach den Namen der Templates, die Sie überschreiben:

   ```bash
   grep -n -i -E "template|design|jquery|yui|csp|content security|php 8" doc/changelogs/6.0/6.0.15.md | head -50
   ```

3. Entscheiden Sie für jeden Treffer: keine Auswirkung, Test nötig, Änderung nötig.
4. Halten Sie die Entscheidungen in einem Issue fest; es wird später Teil Ihrer Release Notes.

## Verwandte Seiten

- [Ihre Overrides mit den Standard-Templates vergleichen](overrides-mit-dem-standard-vergleichen.md)
- [JavaScript-Bibliotheken aktualisieren](javascript-bibliotheken-aktualisieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
