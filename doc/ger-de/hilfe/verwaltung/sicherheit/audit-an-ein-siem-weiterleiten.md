---
title: Das Audit-Protokoll an ein SIEM weiterleiten
audience: government, admin, hosting
category: public-sector
order: 100
summary: Für öffentliche Stellen mit einer zentralen Sicherheitsbetriebsfunktion - wie Sie die Audit-Einträge von Exponential mit dem Syslog-Ziel (UDP, TCP, TLS) und dem signierten Webhook-Ziel an ein SIEM oder einen Log-Sammler senden, welche Kanäle Sie weiterleiten und wie Sie die Zustellung testen.
keywords: SIEM, Sicherheitsbetrieb, SOC, Protokollweiterleitung, Syslog, signierter Webhook, zentrale Protokollierung, Überwachung, security operations, log forwarding, syslog, TLS, webhook, signed webhook, X-Exponential-Signature, audit sinks, central logging, exp:audit sinks, NIS2, monitoring
type: help
---
# Das Audit-Protokoll an ein SIEM weiterleiten

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Hosting-Teams öffentlicher Stellen, die ein
Security Operations Center oder eine zentrale Protokollplattform haben. Werden Audit-Einträge vom Webserver
weitergeleitet, kann ein kompromittierter Rechner seine eigene Geschichte nicht unbemerkt umschreiben, und Ihre
Analystinnen und Analysten können die Website mit dem Rest Ihrer Infrastruktur in Beziehung setzen
([Sicherheitshärtung: Beobachten](../../sicherheitshaertung.md#beobachten-logs-der-request-klassifizierer-und-das-audit-protokoll)).
Die Konfiguration stammt aus [6.5 Weiterleitung an ein SIEM](../../../bc/6.0/audit.md#65-weiterleitung-an-ein-siem).

## Inhalt

1. [Die beiden Ziele](#die-beiden-ziele)
2. [Eine Konfiguration](#eine-konfiguration)
3. [Die Zustellung testen](#die-zustellung-testen)
4. [Im Betrieb](#im-betrieb)
5. [Verwandte Seiten](#verwandte-seiten)

## Die beiden Ziele

| Ziel | Transport | Geeignet für |
|---|---|---|
| `syslog` | lokales syslog/journald, UDP, TCP oder TLS zu einem Sammler | den vollständigen Strom, in jedes SIEM, das Syslog liest |
| `webhook` | HTTPS-POST, gebündelt, mit einem gemeinsamen Geheimnis signiert (`X-Exponential-Signature`) | ausgewählte Sicherheitsereignisse, in die HTTP-Annahme eines SIEM |

Ziele werden je Kanal zugeordnet (`[AuditChannel_<channel>] Sinks[]`); Zustellungen per Webhook und Netzwerk-Syslog
werden zwischengespeichert und vom Cronjob-Teil versandt.

## Eine Konfiguration

In `settings/override/audit.ini.append.php` (nie in der Versionsverwaltung):

```ini
[AuditSink_syslog]
Transport=tls
Host=siem.example.gov
Port=6514
Facility=local4
MinSeverity=info

[AuditSink_webhook]
URL=https://siem.example.gov/api/ingest/exponential
SigningSecret=<output of: openssl rand -hex 32>
BatchSize=200
MinSeverity=notice
Events[]
Events[]=access.*
Events[]=system.audit.*
Events[]=system.setting.*

[AuditChannel_content]
Sinks[]=syslog

[AuditChannel_access]
Sinks[]=webhook

[AuditChannel_system]
Sinks[]=webhook

[AuditChannel_commerce]
Sinks[]=syslog
```

`Sinks[]`-Zeilen werden an die mitgelieferten Listen angehängt, sodass `access` und `system` ihren mitgelieferten
Eintrag `syslog` behalten. Mit TLS prüft PHP das Zertifikat des Sammlers gegen den CA-Speicher des Systems.

## Die Zustellung testen

```bash
./console exp:audit sinks list --allow-root-user
./console exp:audit sinks test syslog --allow-root-user
./console exp:audit sinks test webhook --allow-root-user
```

Jeder Test sendet einen Eintrag; der Empfänger muss mit einem 2xx-Status antworten. Der Empfänger prüft die
Webhook-Signatur; in PHP lautet die Prüfung `expAuditWebhookSink::verify( $body, $headers, $secret )`.

## Im Betrieb

- Leiten Sie mindestens die täglich signierten Prüfpunkte (`system.audit.checkpoint`) weiter: Mit ihnen können Sie
  später beweisen, dass die lokale Kette nicht neu berechnet wurde.
- Überwachen Sie den Zwischenspeicher: `./console exp:audit sinks flush --allow-root-user` sendet, was wartet; die
  Betriebskarte des Dashboards zeigt die Ziele.
- Denken Sie daran, dass die Datenschutzeinstellungen vor der Weiterleitung greifen: Adressen gekürzt, Sitzungen gehasht.
  Stimmen Sie die Aufbewahrung im SIEM mit Ihrer oder Ihrem Datenschutzbeauftragten ab.
- In einem Cluster leitet jeder Server seine eigenen Einträge weiter ([6.3 Cluster](../../../bc/6.0/audit.md#63-cluster)).

## Verwandte Seiten

- [Audit-Leitfaden, 6.5 Weiterleitung an ein SIEM](../../../bc/6.0/audit.md#65-weiterleitung-an-ein-siem)
- [Integrität und Prüfung des Audit-Protokolls](audit-integritaet-und-pruefung.md)
- [Audit-Protokoll für Hoster](../../hosting/sicherheit/pruefprotokoll.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
