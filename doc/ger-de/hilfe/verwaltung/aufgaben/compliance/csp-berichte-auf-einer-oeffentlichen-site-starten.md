---
title: CSP-Berichte auf einer öffentlichen Site starten
audience: government, admin, developer
category: public-sector
order: 100
summary: Schritt für Schritt - öffentliche Siteaccesses erhalten standardmäßig keine Content Security Policy; einen davon mit dem eigenen Berichtssammler von Exponential auf report-only umstellen, damit Sie vor dem Durchsetzen erfahren, was Ihr Design, Ihre Inhalte und Einbettungen brauchen würden.
keywords: Content Security Policy, CSP, öffentliche Site, report-only, Mode auto, ReportEndpoint, csp.log, Einbettungen, Analyse, Stile im Rich Text, AdditionalSources, öffentlicher Sektor, public site, embeds, analytics, rich text styles, public sector
type: help
---
# CSP-Berichte auf einer öffentlichen Site starten

Für Administratoren und Entwickler. Mit `Mode=auto` senden öffentliche Siteaccesses **keine** Richtlinie; ihre Designs
und der Rich Text, den sie darstellen (Stile aus Inhalten, eingebettete Player), werden vom Test des Projekts nicht
geprüft ([Content Security Policy](../../../../../eng-us/features/6.0/content-security-policy.md#switching-to-enforce-step-by-step)).
report-only ist der sichere erste Schritt: Nichts wird blockiert.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Als Nächstes](#als-nächstes)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Kennen Sie Ihre öffentlichen Siteaccesses (einen je Sprache oder Site).
- Rechnen Sie anfangs mit vielen Berichten; der Sammler ist je Client und insgesamt begrenzt.

## Schritte

1. In `settings/siteaccess/<public siteaccess>/site.ini.append.php`:

   ```ini
   [ContentSecurityPolicy]
   Mode=report-only
   ReportEndpoint=enabled
   ```

2. Wenn Sie Ihre Drittanbieter-Hosts schon kennen (Analyse, Karten, Video), fügen Sie sie hinzu, damit sie die
   Berichte nicht fluten:

   ```ini
   AdditionalSources[]=script-src https://<analytics host>
   AdditionalSources[]=frame-src https://<video host>
   ```

3. `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`; laden Sie PHP-FPM neu oder rollen Sie auf Velocity aus.

## Prüfen, ob es funktioniert hat

```bash
curl -sI https://www.example.gov/ | grep -i 'content-security-policy-report-only'
```

Der Header ist vorhanden und nennt `report-uri`, das auf `csp/report` des Siteaccess zeigt. Innerhalb eines Tages zeigt
„Berichte der Content Security Policy“ unter **Setup > Logdateien** die ersten Zeilen.

## Als Nächstes

Arbeiten Sie die Berichte durch („CSP-Berichte prüfen“): Inline-Skripte in Ihrem Design, Stile in Inhalten,
Drittanbieter-Hosts. Setzen Sie durch, wenn sie ruhig sind, wie bei der Administration.

## Verwandte Seiten

- [Content Security Policy für öffentliche Sites](../../../../../eng-us/help/government/security/content-security-policy.md)
- [CSP-Berichte prüfen](csp-berichte-pruefen.md)
- [Besucherstatistiken und Analyse: unter einer CSP](../../../../../eng-us/help/creators/website-creators/statistics-and-analytics.md#under-a-content-security-policy)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
