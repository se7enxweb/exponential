---
title: Das Audit-Protokoll an Syslog weiterleiten
audience: government, admin, hosting
category: public-sector
order: 220
summary: Schritt für Schritt - die Audit-Einträge von Exponential über TLS mit dem Syslog-Ziel an Ihren zentralen Log-Sammler oder Ihr SIEM senden, die Kanäle wählen, die Zustellung mit exp:audit sinks test testen und sie in Betrieb halten.
keywords: Syslog, TLS, SIEM, Weiterleitung von Logs, AuditSink_syslog, Transport, Host, Port, Facility, MinSeverity, Sinks, exp:audit sinks test, zentrale Protokollierung, öffentlicher Sektor, log forwarding, central logging, public sector
type: help
---
# Das Audit-Protokoll an Syslog weiterleiten

Für Administratoren und Hosting-Teams mit einem zentralen Log-Sammler. Einträge, die den Server sofort verlassen, kann
ein Angreifer, der den Server übernimmt, nicht unbemerkt umschreiben
([Das Audit-Protokoll an ein SIEM weiterleiten](../../../../../eng-us/help/government/security/forwarding-audit-to-a-siem.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Host, TLS-Port und Facility des Sammlers von Ihrem Team für Sicherheitsbetrieb; sein Zertifikat ist im
  CA-Speicher des Systems des Servers vertrauenswürdig.
- Die Firewall lässt den Webserver den Sammler erreichen.
- Vereinbaren Sie die Aufbewahrung der Einträge im Sammler mit Ihrem Datenschutzbeauftragten.

## Schritte

1. In `settings/override/audit.ini.append.php`:

   ```ini
   [AuditSink_syslog]
   Transport=tls
   Host=siem.example.gov
   Port=6514
   Facility=local4
   MinSeverity=info

   [AuditChannel_content]
   Sinks[]=syslog

   [AuditChannel_commerce]
   Sinks[]=syslog
   ```

   Die Kanäle `access` und `system` werden schon mit dem Ziel `syslog` ausgeliefert; Zeilen `Sinks[]` ergänzen die
   mitgelieferten Listen.
2. Leeren Sie den INI-Cache, laden Sie PHP-FPM neu, starten Sie Velocity neu.

## Prüfen, ob es funktioniert hat

```bash
./console exp:audit sinks list --allow-root-user
./console exp:audit sinks test syslog --allow-root-user
```

Der Test sendet einen Eintrag; bitten Sie das SOC zu bestätigen, dass er angekommen ist. Melden Sie sich dann in der
Administration an und prüfen Sie, dass auch der Eintrag `access.session.login` ankommt. Schlägt die Zustellung fehl,
werden Einträge zwischengespeichert und vom Teil der Cronjobs gesendet;
`./console exp:audit sinks flush syslog --allow-root-user` sendet, was wartet.

## Verwandte Seiten

- [Das Audit-Protokoll an ein SIEM weiterleiten](../../../../../eng-us/help/government/security/forwarding-audit-to-a-siem.md)
- [Anleitung zum Audit, 6.5 Weiterleitung an ein SIEM](../../../../bc/6.0/audit.md#65-weiterleitung-an-ein-siem)
- [Das Audit-Protokoll verifizieren](das-audit-protokoll-verifizieren.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
