---
title: CSP-Berichte prüfen
audience: government, admin, developer
category: public-sector
order: 80
summary: Schritt für Schritt - Berichte über Verstöße gegen die Content Security Policy mit dem eigenen Sammler csp/report von Exponential sammeln, sie unter Setup > Logdateien lesen, echte Probleme von Rauschen unterscheiden und je Quelle entscheiden, was zu beheben oder zu erlauben ist.
keywords: CSP-Berichte, csp.log, csp/report, ReportEndpoint, ReportURI, Verstöße, Log-Viewer, blockiertes Inline-Skript, report-sample, Erweiterungen, Browser-Erweiterungen, Sichtung, öffentlicher Sektor, CSP reports, violations, log viewer, blocked inline script, extensions, browser extensions, triage, public sector
type: help
---
# CSP-Berichte prüfen

Für Administratoren und Entwickler, die das Durchsetzen einer Content Security Policy vorbereiten, und für die
wöchentliche Prüfung der Logs nach dem Durchsetzen. Der Kernel sammelt die Berichte selbst: `csp/report` schreibt je
Verstoß eine Zeile in `var/log/csp.log`
([Content Security Policy: Berichte](../../../../../eng-us/features/6.0/content-security-policy.md#reports)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Die Zeilen lesen](#die-zeilen-lesen)
4. [Entscheiden](#entscheiden)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Richtlinie des Siteaccess verweist auf den Sammler: `[ContentSecurityPolicy] ReportEndpoint=enabled` (oder
  `ReportURI=/csp/report`) in `site.ini.append.php` dieses Siteaccess; INI-Cache geleert, PHP neu geladen.
- Rechte, Logs zu lesen (`setup/logs`).

## Schritte

1. Verwenden Sie den Siteaccess einige Tage so, wie Redakteure es tun, einschließlich Dialogen und Kontextmenüs.
2. Öffnen Sie **Setup > Logdateien** > „Berichte der Content Security Policy“ (Log-Schlüssel `csp`).
3. Gruppieren Sie nach Direktive und blockierter Quelle (der Viewer gruppiert `csp.log` nach Direktive und blockiertem
   Ursprung).
4. Finden Sie für jede Gruppe die Seite und das Template oder die Erweiterung, die sie verursacht.
5. Halten Sie jede Entscheidung in einer kurzen Tabelle fest: Quelle, Entscheidung, Verantwortliche, Datum.

## Die Zeilen lesen

- Die Direktive (`script-src`, `style-src`, `img-src` ...) und was blockiert wurde (`inline` oder ein Ursprung wie
  `https://maps.example.org`).
- Mit `'report-sample'` in `script-src` oder `style-src` werden die ersten 40 Zeichen eines blockierten Inline-Skripts
  oder -Stils als `sample="..."` mitgeliefert, was beim Finden des Templates hilft.
- Fluten werden begrenzt; eine Flut ist eine Zeile `abuse/csp-report-flood` je Minute in `security.log`.

## Entscheiden

| Quelle | Übliche Entscheidung |
|---|---|
| ein Template Ihres Designs oder Ihrer Erweiterung mit Inline-Code | beheben: Skript in eine Datei, Werte in `data-*` |
| ein Drittanbieterdienst, den Sie auf diesem Siteaccess nutzen | `AdditionalSources[]=<directive> <host>` nur für diesen Siteaccess |
| eine Browser-Erweiterung eines Benutzers (unbekannte Ursprünge, `chrome-extension:`) | ignorieren |
| ein Drittanbieterdienst, von dem Sie nichts wussten | herausfinden, wer ihn hinzugefügt hat; entfernen oder entscheiden |

## Verwandte Seiten

- [Content Security Policy für öffentliche Sites](../../../../../eng-us/help/government/security/content-security-policy.md)
- [Die CSP der Administration auf Durchsetzen umstellen](die-csp-der-administration-auf-durchsetzen-umstellen.md)
- [Routine zur Prüfung der Logs](../../../../../eng-us/help/government/security/log-review-routine.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
