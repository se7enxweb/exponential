---
title: audit.ini [AuditRecordSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditRecordSettings] in audit.ini: BeforeAfter, MaxValueLength, ChildDepth, MaxChildren, RequestContext, RequestIdHeader, TrustedRequestIdHeader, MaxLineBytes"
keywords: Einstellung, audit.ini, AuditRecordSettings, INI, setting, BeforeAfter, MaxValueLength, ChildDepth, MaxChildren, RequestContext, RequestIdHeader, TrustedRequestIdHeader, MaxLineBytes
type: generated
generated: exp:help:examples
---
# audit.ini [AuditRecordSettings]

Der Block `[AuditRecordSettings]` von `settings/audit.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## BeforeAfter

> Werte vorher/nachher festhalten (Q2).  enabled | keys (Schlüssel behalten, Werte durch ihren  
> sha256 ersetzt) | disabled.  Geheimnisse werden in keinem Fall festgehalten.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRecordSettings]
BeforeAfter=enabled
*/ ?>
```

## MaxValueLength

> Längster Wert, der in den Feldern before/after/object erhalten bleibt, in Zeichen; längere werden abgeschnitten  
> und enden auf "…".  

Standard: `512`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRecordSettings]
MaxValueLength=512
*/ ?>
```

## ChildDepth

> Untergeordnete Ereignisse (F2): wie viele Ebenen unter einem übergeordneten einzeln geschrieben werden und wie viele  
> untergeordnete pro übergeordnetem höchstens. Über eine der Grenzen hinaus zählt das übergeordnete den Rest in  
> after.children\_omitted.  0 = gar keine untergeordneten.  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRecordSettings]
ChildDepth=3
*/ ?>
```

## MaxChildren

Standard: `10000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRecordSettings]
MaxChildren=10000
*/ ?>
```

## RequestContext

> Zusätzliche Felder der Anfrage.  enabled | disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRecordSettings]
RequestContext=enabled
*/ ?>
```

## RequestIdHeader

> Die Anfrage-ID als Antwort-Header senden (F2); ein leerer Wert sendet keinen. Benannt wie  
> die anderen X-Exp-\*-Header von Exponential (X-Exp-Cache).  

Standard: `X-Exp-Request-Id`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRecordSettings]
RequestIdHeader=X-Exp-Request-Id
*/ ?>
```

## TrustedRequestIdHeader

> Die Anfrage-ID von einem vertrauenswürdigen vorgelagerten Proxy übernehmen, statt eine zu erzeugen; leer = nie.  
> Dem Header wird nur von den Adressen in TrustedProxies\[\] vertraut (bei leerem Wert die Liste  
> von site.ini).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRecordSettings]
TrustedRequestIdHeader=
*/ ?>
```

## MaxLineBytes

> Längste Zeile, die der Schreiber zurückliest, um den Kopf der Kette zu finden, in Bytes.  

Standard: `262144`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRecordSettings]
MaxLineBytes=262144
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
