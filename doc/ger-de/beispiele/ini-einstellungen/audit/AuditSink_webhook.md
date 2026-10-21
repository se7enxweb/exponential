---
title: audit.ini [AuditSink_webhook]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditSink_webhook] in audit.ini: URL, SigningSecret, BatchSize, BatchSeconds, Timeout, Retries, RetryBackoff, Events ..."
keywords: Einstellung, audit.ini, AuditSink_webhook, INI, setting, URL, SigningSecret, BatchSize, BatchSeconds, Timeout, Retries, RetryBackoff, Events, MinSeverity
type: generated
generated: exp:help:examples
---
# audit.ini [AuditSink_webhook]

Der Block `[AuditSink_webhook]` von `settings/audit.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## URL

> Aus, bis eine URL gesetzt ist.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_webhook]
URL=
*/ ?>
```

## SigningSecret

> HMAC-SHA-256 des Inhalts mit diesem Geheimnis, gesendet als X-Exponential-Signature.  
> Setzen Sie es in settings/override (es wird als Geheimnis maskiert).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_webhook]
SigningSecret=
*/ ?>
```

## BatchSize

Standard: `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_webhook]
BatchSize=100
*/ ?>
```

## BatchSeconds

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_webhook]
BatchSeconds=10
*/ ?>
```

## Timeout

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_webhook]
Timeout=5
*/ ?>
```

## Retries

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_webhook]
Retries=5
*/ ?>
```

## RetryBackoff

> Sekunden bis zum ersten erneuten Versuch; jedes Mal verdoppelt.  

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_webhook]
RetryBackoff=30
*/ ?>
```

## Events

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_webhook]
# ein leeres "Events[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Events[]
*/ ?>
```

## MinSeverity

Standard: `notice`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_webhook]
MinSeverity=notice
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
