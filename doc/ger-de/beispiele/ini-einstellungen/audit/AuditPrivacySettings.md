---
title: audit.ini [AuditPrivacySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditPrivacySettings] in audit.ini: Field, IPv4Prefix, IPv6Prefix, SecretPathViews, NeverRecord, PseudonymiseAfterDays"
keywords: Einstellung, audit.ini, AuditPrivacySettings, INI, setting, Field, IPv4Prefix, IPv6Prefix, SecretPathViews, NeverRecord, PseudonymiseAfterDays
type: generated
generated: exp:help:examples
---
# audit.ini [AuditPrivacySettings]

Der Block `[AuditPrivacySettings]` von `settings/audit.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Field

> Per field: full | truncate | hash | off (see "The record format" for what truncate  
> means per field). Q3: a safe default; the session can only be hashed or off.  
> E-mail addresses in access.user.\* events and attempted logins of unknown users.  

Standard: ein Hash mit 10: `actor.login=full`, `actor.ip=truncate`, `actor.ua=truncate`, `actor.session=hash`, `actor.cli.os_user=full`, `request.url=truncate`, `request.host=full`, `object.name=full`, `email=hash`, `attempted_login=hash`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditPrivacySettings]
# ein leeres "Field[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Field[]
Field[actor.login]=full
Field[actor.ip]=truncate
Field[actor.ua]=truncate
Field[actor.session]=hash
Field[actor.cli.os_user]=full
Field[request.url]=truncate
Field[request.host]=full
Field[object.name]=full
Field[email]=hash
Field[attempted_login]=hash
*/ ?>
```

## IPv4Prefix

> Prefix lengths for truncate of addresses: /24 for IPv4, /48 for IPv6 (Q3).  

Standard: `24`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditPrivacySettings]
IPv4Prefix=24
*/ ?>
```

## IPv6Prefix

Standard: `48`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditPrivacySettings]
IPv6Prefix=48
*/ ?>
```

## SecretPathViews

> Views whose path parameters are tokens: their parameters are never recorded.  

Standard: eine Liste mit 3: `user/activate`, `user/forgotpassword`, `userpaex/forgotpassword`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditPrivacySettings]
# ein leeres "SecretPathViews[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SecretPathViews[]
SecretPathViews[]=user/activate
SecretPathViews[]=user/forgotpassword
SecretPathViews[]=userpaex/forgotpassword
*/ ?>
```

## NeverRecord

> Attribute and parameter names never recorded, besides everything  
> expIniEditor::isSecret() recognises.  

Standard: eine Liste mit 6: `HashKey`, `Hash`, `Password`, `PasswordConfirm`, `password_hash`, `ezxform_token`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditPrivacySettings]
# ein leeres "NeverRecord[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
NeverRecord[]
NeverRecord[]=HashKey
NeverRecord[]=Hash
NeverRecord[]=Password
NeverRecord[]=PasswordConfirm
NeverRecord[]=password_hash
NeverRecord[]=ezxform_token
*/ ?>
```

## PseudonymiseAfterDays

> Days after which the index replaces personal fields by their hashed form (Q3, F6).  

Standard: `90`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditPrivacySettings]
PseudonymiseAfterDays=90
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
