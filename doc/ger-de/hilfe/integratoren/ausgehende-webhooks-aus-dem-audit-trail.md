---
title: Ausgehende Webhooks aus dem Audit-Trail
audience: integrator, developer, admin
category: integration
order: 190
summary: Die signierte Webhook-Senke des Audit-Trails als ausgehenden Ereignis-Feed nutzen - was sie sendet, wie Sie Inhaltsereignisse wie content.object.publish an sie leiten, die HMAC-Signatur und wie ein Empfänger sie prüft, Zustellung mit Spool und Wiederholungen, Tests und ihre Grenzen.
keywords: Webhook, Audit, Audit-Trail, expAuditWebhookSink, AuditSink_webhook, SigningSecret, X-Exponential-Signature, HMAC-SHA-256, content.object.publish, content.node.remove, Spool, Wiederholungen, Syslog-Senke, SIEM, Ereignis-Feed, webhook, audit trail, retries, syslog sink, event feed
type: help
---
# Ausgehende Webhooks aus dem Audit-Trail

Diese Seite ist für Integratoren, die wollen, dass Exponential **Ereignisse** ohne eigenen Code über HTTPS an ein
anderes System **pusht**: ein System für Security Information and Event Management (SIEM), eine Log-Plattform oder ein
Dienst, der von Veröffentlichungen und Löschungen erfahren muss. Der Audit-Trail zeichnet auf, was in der Installation
geschieht, und kann seine Datensätze neben seinen eigenen Dateien an **Senken** liefern: Syslog, E-Mail und einen
**signierten Webhook** (`kernel/classes/audit/sinks/expauditwebhooksink.php`). Das Audit selbst ist in
[Audit](../../bc/6.0/audit.md) beschrieben.

## Inhalt

1. [Was der Webhook sendet](#was-der-webhook-sendet)
2. [Sicherheitsereignisse an ein SIEM senden](#sicherheitsereignisse-an-ein-siem-senden)
3. [Inhaltsereignisse an ein anderes System senden](#inhaltsereignisse-an-ein-anderes-system-senden)
4. [Die Signatur auf der Empfängerseite prüfen](#die-signatur-auf-der-empfängerseite-prüfen)
5. [Zustellung, Wiederholungen und Tests](#zustellung-wiederholungen-und-tests)
6. [Grenzen](#grenzen)

## Was der Webhook sendet

Stapel von Audit-Datensätzen als JSON, per HTTP `POST`:

```text
{"v":1,"installation":"<installation id>","site":"<SiteName>","batch":"b-...","events":[<record>, ...]}
```

| Header | Wert |
|---|---|
| `X-Exponential-Timestamp` | Epochensekunden, als der Stapel signiert wurde |
| `X-Exponential-Batch` | die Stapel-ID, zur Duplikaterkennung |
| `X-Exponential-Signature` | `sha256=<hex HMAC-SHA-256 of timestamp + "." + body, keyed with SigningSecret>` |

Ein Datensatz nennt das Ereignis (`content.object.publish`, `access.session.login.failed` ...), den Handelnden, das
Objekt und das Ziel nach Typ, ID und identifizierenden Feldern, das Ergebnis und eine Anfrage-ID. Datensätze enthalten
nie Werte von Inhaltsattributen; der Empfänger liest die Inhalte, die er braucht, über die REST-API oder
`expservices`.

## Sicherheitsereignisse an ein SIEM senden

Die Kanäle `access` und `system` tragen Anmeldungen, Abweisungen, Rollenänderungen und Änderungen an Einstellungen. In
`settings/override/audit.ini.append.php`:

```ini
[AuditSink_webhook]
URL=https://siem.example.com/ingest/exponential
SigningSecret=<a long random value>
MinSeverity=notice

[AuditChannel_access]
Sinks[]
Sinks[]=syslog
Sinks[]=webhook

[AuditChannel_system]
Sinks[]
Sinks[]=syslog
Sinks[]=webhook
```

Die leere Zeile `Sinks[]` leert die mitgelieferte Liste vor der neuen; behalten Sie `syslog`, wenn Sie es auch wollen.
Das Geheimnis wird in der Administration maskiert; halten Sie es in `settings/override`, nie in Git.

## Inhaltsereignisse an ein anderes System senden

Inhaltsereignisse werden nach Ereignisname aufgezeichnet. `content.object.publish` und `content.object.create` sind
standardmäßig **aus**; Löschungen, Verschiebungen, Verstecken sowie Änderungen von Zustand und Sektion sind an. Um
Veröffentlichungen und Löschungen an einen Webhook zu liefern:

```ini
[AuditEventSettings]
Enabled[]=content.object.create
Enabled[]=content.object.publish

[AuditChannel_content]
Sinks[]
Sinks[]=webhook

[AuditSink_webhook]
URL=https://hub.example.com/hooks/exponential
SigningSecret=<a long random value>
Events[]
Events[]=content.object.publish
Events[]=content.node.remove.*
Events[]=content.object.remove
Events[]=content.node.move
# only the listed events: a record outside Events[] is still taken when it reaches MinSeverity
MinSeverity=emergency
```

Eine Senke nimmt einen Datensatz, der zu `Events[]` passt, unabhängig von seinem Schweregrad, und jeden anderen an sie
geleiteten Datensatz, wenn er `MinSeverity` erreicht; `emergency` beschränkt die Senke deshalb praktisch auf die
aufgeführten Ereignisse.

> **Hinweis**
> Das Audit ist als Aufzeichnung dessen gedacht, was geschehen ist, nicht als Nachrichtenbus: Datensätze laufen immer
> über den Spool (eine Anfrage wartet nie auf das Netzwerk), und der Audit-Cronjob-Teil stellt sie zu, sodass die
> Zustellung dem Cron-Zeitplan folgt, typischerweise alle paar Minuten. Die Form eines Datensatzes folgt dem
> Ereignismodell des Audits. Wenn das empfangende System eigene Nutzdaten, eine garantierte Reihenfolge pro Objekt
> oder Zustellung im Sekundenbruchteil braucht, verwenden Sie ein Workflow-Ereignis mit einer Outbox
> ([Workflows und Trigger](workflows-und-trigger-als-hooks.md)).

## Die Signatur auf der Empfängerseite prüfen

Weisen Sie eine Anfrage ab, wenn die Signatur nicht passt oder der Zeitstempel mehr als 300 Sekunden abweicht; erkennen
Sie Duplikate an der Stapel-ID und an der Ereignis-ID jedes Datensatzes. In Python:

```python
import hashlib, hmac, time

def verify(secret: bytes, timestamp: str, body: bytes, signature: str, tolerance=300) -> bool:
    if not timestamp.isdigit() or abs(time.time() - int(timestamp)) > tolerance:
        return False
    expected = 'sha256=' + hmac.new(secret, timestamp.encode() + b'.' + body, hashlib.sha256).hexdigest()
    return hmac.compare_digest(expected, signature)
```

Ein in PHP mit geladenem Kernel geschriebener Empfänger kann `expAuditWebhookSink::verify( $secret, $timestamp, $body,
$signature )` aufrufen, das `''` zurückgibt, wenn die Signatur gültig ist, sonst `bad_signature` oder
`stale_timestamp`.

## Zustellung, Wiederholungen und Tests

| Einstellung (`[AuditSink_webhook]`) | Standard | Bedeutung |
|---|---|---|
| `BatchSize` | `100` | Datensätze pro Anfrage |
| `BatchSeconds` | `10` | Sekunden, die ein Stapel warten darf |
| `Timeout` | `5` | Sekunden pro Anfrage |
| `Retries` | `5` | Versuche, bevor ein Stapel im Spool bleibt |
| `RetryBackoff` | `30` | Sekunden vor der ersten Wiederholung; jedes Mal verdoppelt |

Jeder Datensatz geht zuerst in den Spool (`[AuditSinkSettings] SpoolDir`, `log/audit/spool`); der Audit-Cronjob-Teil
(in `frequent` und als Teil `audit`) stellt ihn zu und wiederholt, was fehlgeschlagen ist. Eine Antwort `2xx` gilt als
Zustellung eines Stapels. Ein wiederholter Stapel behält seine Stapel-ID. Die Zustellung erfolgt mindestens einmal:
Der Empfänger muss Duplikate anhand der Ereignis-ID erkennen.

```bash
php bin/php/console exp:audit sinks list --allow-root-user          # ready? spooled? channels
php bin/php/console exp:audit sinks test webhook --allow-root-user  # one test record, exit 0 when delivered
php bin/php/console exp:audit sinks flush --apply --allow-root-user         # send what is spooled now
```

## Grenzen

- Nur der Audit-Trail sendet Webhooks; es gibt keinen allgemeinen ausgehenden Webhook pro Inhaltsklasse oder Feld.
- Es gibt keinen Endpunkt für eingehende Webhooks; ein Empfänger für Webhooks anderer Systeme ist ein Modul oder eine
  Dienstfunktion Ihrer Erweiterung.
- Ein Ereignis, das nicht aufgezeichnet wird (in `[AuditEventSettings]` abgeschaltet oder unter `MinSeverity` des
  Audits), wird nicht gesendet.

## Verwandte Seiten

- [Audit](../../bc/6.0/audit.md), Abschnitte „Sinks and alerts“ und „The developer API“
- [Audit-Trail für Hoster](../hosting/sicherheit/pruefprotokoll.md)
- [API- und Protokollstandards, Webhooks](../../standards/apis-und-protokolle.md#webhooks)
- [Integrationen überwachen](integrationen-ueberwachen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
