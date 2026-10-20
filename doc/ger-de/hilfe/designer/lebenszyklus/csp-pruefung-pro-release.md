---
title: "Eine CSP-Prüfung bei jedem Release"
audience: designer, developer
category: theme-lifecycle
order: 150
summary: Vor jedem Release prüfen, dass das Theme unter einer strikten Content Security Policy noch funktioniert - Modus report-only auf einem Test-Siteaccess, die Browserkonsole, eine Suche nach Inline-Code und externe Ressourcen.
keywords: CSP-Prüfung, Content Security Policy, report-only, unsafe-inline, Konsole, Inline-Skripte, externe Ressourcen, Release-Routine, CSP audit, console, inline scripts, external resources, release routine
type: help
---
# Eine CSP-Prüfung bei jedem Release

Diese Seite ist für Designer, die ein Theme betreuen, das unter einer Content Security Policy läuft oder laufen soll. Ist
ein Theme einmal frei von Inline-Code ([Inline-Skripte und -Styles entfernen](inline-skripte-und-styles-entfernen.md)),
muss jedes Release das so halten.

## Die Routine

1. **Richtlinie an, nur melden**, für den Test-Siteaccess (`site.ini [ContentSecurityPolicy] Mode=report-only` in den
   Einstellungen dieses Siteaccess; siehe [Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)).
2. **Gehen Sie die Seitenliste durch**, mit geöffneter Browserkonsole. Erwartet: keine CSP-Meldungen. Verwenden Sie
   `ReportURI`, um Meldungen aus einer längeren Sitzung zu sammeln.
3. **Durchsuchen Sie den Diff** des Releases nach Inline-Code:

   ```bash
   git diff "$(git describe --tags --abbrev=0)"..HEAD -- design | grep -n -E "^\+.*(<script[^>]*>[^<]|on[a-z]+=|javascript:|style=\")"
   ```

   Erwartet: keine Ausgabe.
4. **Externe Ressourcen**: Alles, was nicht von der Website selbst ausgeliefert wird (Schriften, Skripte, Bilder), braucht
   einen Eintrag in der Richtlinie und einen Grund; ziehen Sie Selbsthosting vor ([Externe Ressourcen und CDNs](../../../../eng-us/help/designers/theme-design/external-resources-and-cdns.md)).
5. **Interaktive Teile** (Menüs, Galerien, Formulare, Karten) funktionieren, wenn die Richtlinie auf dem Test-Siteaccess
   durchgesetzt wird.

## Festhalten

Schreiben Sie das Ergebnis in die Release Notes („Läuft unter einer Content Security Policy ohne 'unsafe-inline'“), nur
wenn es stimmt.

## Verwandte Seiten

- [Checkliste: CSP](../../../../eng-us/help/designers/theme-design/checklist-csp.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
