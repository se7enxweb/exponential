---
title: CSP-Berichte
audience: government, admin, developer
category: public-sector
order: 60
summary: Berichte über Verstöße gegen die Content Security Policy in csp.log sammeln, sie im Log-Viewer und in der Fehlerübersicht lesen und mit ihrer Hilfe einen Siteaccess des öffentlichen Sektors vom reinen Berichtsmodus zu einer durchgesetzten Richtlinie bringen, ohne Seiten zu beschädigen.
keywords: Content Security Policy, CSP, csp.log, nur berichten, durchsetzen, ReportEndpoint, Verstoßberichte, XSS, Cross-Site-Scripting, Browsersicherheit, Überwachung, report-only, enforce, violation reports, cross-site scripting, browser security, monitoring
type: help
---
# CSP-Berichte

Diese Seite richtet sich an die Administration und die Entwicklung, die den Schutz einer Content Security Policy
([CSP Level 3](https://www.w3.org/TR/CSP3/)) auf einer öffentlichen Website nutzen möchten und vor der Durchsetzung
Belege brauchen. Exponential sammelt die Berichte der Browser selbst. Referenz:
[Content Security Policy](../../../../funktionen/6.0/content-security-policy.md); Sicht des Hostings:
[Content Security Policy (Hosting)](../../../hosting/sicherheit/content-security-policy.md).

## Inhalt
1. [Was ausgeliefert wird](#was-ausgeliefert-wird)
2. [Berichte einschalten](#berichte-einschalten)
3. [Die Berichte lesen](#die-berichte-lesen)
4. [Von Berichten zur Durchsetzung](#von-berichten-zur-durchsetzung)
5. [Verwandte Seiten](#verwandte-seiten)

## Was ausgeliefert wird

`site.ini [ContentSecurityPolicy] Mode=auto` (Standard) sendet die Richtlinie auf Siteaccesses der Administration
(Designs, die mit `admin` beginnen) als **report-only** und auf öffentlichen Websites **keine Richtlinie**. Die
ausgelieferten Direktiven erlauben nur die eigenen Dateien der Website (`script-src 'self'`, `style-src 'self'`,
`object-src 'none'`, `frame-ancestors 'self'` ...), mit einer Nonce je Anfrage. Im Auslieferungszustand wird also für
niemanden etwas blockiert. Der Sicherheitsstandard zählt die fehlende CSP öffentlicher Websites zu seinen bekannten
Lücken ([Sicherheitsstandard](../../../../standards/sicherheit.md)).

## Berichte einschalten

Je Siteaccess:

```ini
# settings/siteaccess/<siteaccess>/site.ini.append.php
[ContentSecurityPolicy]
ReportEndpoint=enabled
```

Leeren Sie den INI-Cache. Die Ansicht `csp/report` nimmt die Berichte der Browser entgegen und schreibt je Verstoß eine
Zeile in `var/log/csp.log`. Sie ist für jeden Besucher offen (Browser senden Berichte ohne Anmeldung), antwortet ohne
Inhalt, wird nie gecacht und ist je Client (`ReportsPerMinute=60`) und insgesamt (`ReportsPerMinuteTotal=600`) in der
Rate begrenzt; eine Flut wird zu einer Zeile `abuse/csp-report-flood` pro Minute in `security.log`.

## Die Berichte lesen

- **Setup > Logdateien**, Log **Berichte der Content Security Policy** (Schlüssel `csp`).
- Die **Fehlerübersicht** gruppiert `csp.log` nach Direktive und blockierter Quelle: eine Gruppe je Sache, die kaputt
  gehen würde.
- Adressen werden wie in den Anfrage-Logs maskiert. Rotation: 5 Archive, komprimiert, 10 MB, bereinigt nach 30 Tagen.

Typische Quellen: ein Inline-Skript in einem Template oder einer Erweiterung, ein externes Analyse- oder Kartenskript,
eine ältere Editor-Komponente. Jede wird entweder im Template behoben oder bewusst über die zusätzlichen Quellen der
Richtlinie erlaubt.

## Von Berichten zur Durchsetzung

1. Betreiben Sie den Siteaccess eine Zeit lang im Modus report-only, die alle Seitentypen und redaktionellen Aufgaben
   abdeckt.
2. Arbeiten Sie die Gruppen der Fehlerübersicht mit der Entwicklung ab, bis nur noch akzeptierte Quellen übrig sind.
3. Schalten Sie den Siteaccess auf dem Staging-System auf `Mode=enforce`, testen Sie, dann über Ihren Änderungsprozess
   auf der Produktion ([Änderungsmanagement und Änderungsbeiräte](../aktualisierungen-und-aenderungen/aenderungsmanagement-und-aenderungsbeiraete.md)).
4. Behalten Sie `ReportEndpoint=enabled` bei: Nach der Durchsetzung zeigen die Berichte, was blockiert wurde.
5. Die Rücknahme ist eine Einstellungsänderung zurück auf `report-only`.

Die Schritt-für-Schritt-Anleitung steht im Abschnitt „Switching to enforce, step by step“ der Funktionsseite.

## Verwandte Seiten
- [Anfrage-Logs: 404 und Sicherheit](anfrageprotokolle-404-und-sicherheit.md)
- [Fehlerübersicht und Zustand der Logs](fehleruebersicht-und-zustand-der-logs.md)
- [Sicherheitsstandard](../../../../standards/sicherheit.md)
- [CSP-Checkliste für Theme-Designer](../../../designer/theme-design/checkliste-csp.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
