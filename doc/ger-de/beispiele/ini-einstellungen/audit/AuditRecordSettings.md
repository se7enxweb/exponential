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

> Record before/after values (Q2).  enabled | keys (keys kept, values replaced by their  
> sha256) | disabled.  Secrets are never recorded either way.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRecordSettings]
BeforeAfter=enabled
*/ ?>
```

## MaxValueLength

> Longest value kept in before/after/object fields, in characters; longer ones are cut  
> and end in "…".  

Standard: `512`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRecordSettings]
MaxValueLength=512
*/ ?>
```

## ChildDepth

> Child events (F2): how many levels below a parent are written one by one, and how many  
> children per parent at most. Past either limit the parent counts the rest in  
> after.children\_omitted.  0 = no children at all.  

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

> Extra request fields.  enabled | disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRecordSettings]
RequestContext=enabled
*/ ?>
```

## RequestIdHeader

> Send the request id as a response header (F2); an empty value sends none. Named like  
> the other X-Exp-\* headers of Exponential (X-Exp-Cache).  

Standard: `X-Exp-Request-Id`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRecordSettings]
RequestIdHeader=X-Exp-Request-Id
*/ ?>
```

## TrustedRequestIdHeader

> Accept the request id from a trusted front proxy instead of making one; empty = never.  
> The header is only trusted from the addresses in TrustedProxies\[\] (site.ini's list  
> when empty).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRecordSettings]
TrustedRequestIdHeader=
*/ ?>
```

## MaxLineBytes

> Longest line the writer reads back to find the chain's head, in bytes.  

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
