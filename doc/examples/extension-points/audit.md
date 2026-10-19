---
title: Extension points in audit.ini
audience: developer, admin
category: examples
summary: Settings of audit.ini that list handlers, classes or directories an extension can add to (25, found by name).
keywords: audit.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Extension points in audit.ini

Settings of `settings/audit.ini` whose name says they hold handlers, classes, filters, providers or directories that an extension can add to or replace. **Found by the setting's name: a heuristic.** Read the setting's comment (linked) before relying on it.

An extension adds to a list in its own `settings/audit.ini.append.php` (active extensions' settings are read after the kernel's and before `settings/override`):

## AuditChannel_content Sinks

Shipped: an empty list. [The setting](../ini-settings/audit/AuditChannel_content.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditChannel_content]
Sinks[]=expExampleSink    # your class or value, added to the list
*/ ?>
```

## AuditChannel_access Sinks

Shipped: a list of 1: `syslog`. [The setting](../ini-settings/audit/AuditChannel_access.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditChannel_access]
Sinks[]=expExampleSink    # your class or value, added to the list
*/ ?>
```

## AuditChannel_system Sinks

Shipped: a list of 1: `syslog`. [The setting](../ini-settings/audit/AuditChannel_system.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditChannel_system]
Sinks[]=expExampleSink    # your class or value, added to the list
*/ ?>
```

## AuditChannel_commerce Sinks

Shipped: an empty list. [The setting](../ini-settings/audit/AuditChannel_commerce.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditChannel_commerce]
Sinks[]=expExampleSink    # your class or value, added to the list
*/ ?>
```

## AuditChannel_read Sinks

Shipped: an empty list. [The setting](../ini-settings/audit/AuditChannel_read.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditChannel_read]
Sinks[]=expExampleSink    # your class or value, added to the list
*/ ?>
```

## AuditReadSettings Classes

Shipped: an empty list. [The setting](../ini-settings/audit/AuditReadSettings.md#classes).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditReadSettings]
Classes[]=expExampleClasse    # your class or value, added to the list
*/ ?>
```

## AuditSinkSettings SinkClasses

Shipped: a hash of 3: `syslog=expAuditSyslogSink`, `webhook=expAuditWebhookSink`, `mail=expAuditMailSink`. [The setting](../ini-settings/audit/AuditSinkSettings.md#sinkclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditSinkSettings]
SinkClasses[expexample]=expExampleSinkClasse    # key and value are yours
*/ ?>
```

## AuditSink_syslog Transport

Shipped: `local`. [The setting](../ini-settings/audit/AuditSink_syslog.md#transport).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditSink_syslog]
Transport=local    # replace with your class or value
*/ ?>
```

## AuditSink_mail Transport

Shipped: empty. [The setting](../ini-settings/audit/AuditSink_mail.md#transport).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditSink_mail]
Transport=    # replace with your class or value
*/ ?>
```

## AuditAlertSettings RuleClasses

Shipped: a hash of 3: `threshold=expAuditThresholdRule`, `match=expAuditMatchRule`, `schedule=expAuditScheduleRule`. [The setting](../ini-settings/audit/AuditAlertSettings.md#ruleclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditAlertSettings]
RuleClasses[expexample]=expExampleRuleClasse    # key and value are yours
*/ ?>
```

## AlertRule_brute_force Class

Shipped: `threshold`. [The setting](../ini-settings/audit/AlertRule_brute_force.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_brute_force]
Class=threshold    # replace with your class or value
*/ ?>
```

## AlertRule_brute_force Sinks

Shipped: a list of 2: `syslog`, `mail`. [The setting](../ini-settings/audit/AlertRule_brute_force.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_brute_force]
Sinks[]=expExampleSink    # your class or value, added to the list
*/ ?>
```

## AlertRule_brute_force_user Class

Shipped: `threshold`. [The setting](../ini-settings/audit/AlertRule_brute_force_user.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_brute_force_user]
Class=threshold    # replace with your class or value
*/ ?>
```

## AlertRule_brute_force_user Sinks

Shipped: a list of 2: `syslog`, `mail`. [The setting](../ini-settings/audit/AlertRule_brute_force_user.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_brute_force_user]
Sinks[]=expExampleSink    # your class or value, added to the list
*/ ?>
```

## AlertRule_admin_role_granted Class

Shipped: `match`. [The setting](../ini-settings/audit/AlertRule_admin_role_granted.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_admin_role_granted]
Class=match    # replace with your class or value
*/ ?>
```

## AlertRule_admin_role_granted Sinks

Shipped: a list of 2: `syslog`, `mail`. [The setting](../ini-settings/audit/AlertRule_admin_role_granted.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_admin_role_granted]
Sinks[]=expExampleSink    # your class or value, added to the list
*/ ?>
```

## AlertRule_settings_out_of_hours Class

Shipped: `schedule`. [The setting](../ini-settings/audit/AlertRule_settings_out_of_hours.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_settings_out_of_hours]
Class=schedule    # replace with your class or value
*/ ?>
```

## AlertRule_settings_out_of_hours Sinks

Shipped: a list of 1: `syslog`. [The setting](../ini-settings/audit/AlertRule_settings_out_of_hours.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_settings_out_of_hours]
Sinks[]=expExampleSink    # your class or value, added to the list
*/ ?>
```

## AlertRule_mass_delete Class

Shipped: `threshold`. [The setting](../ini-settings/audit/AlertRule_mass_delete.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_mass_delete]
Class=threshold    # replace with your class or value
*/ ?>
```

## AlertRule_mass_delete Sinks

Shipped: a list of 2: `syslog`, `mail`. [The setting](../ini-settings/audit/AlertRule_mass_delete.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_mass_delete]
Sinks[]=expExampleSink    # your class or value, added to the list
*/ ?>
```

## AlertRule_audit_disabled Class

Shipped: `match`. [The setting](../ini-settings/audit/AlertRule_audit_disabled.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_audit_disabled]
Class=match    # replace with your class or value
*/ ?>
```

## AlertRule_audit_disabled Sinks

Shipped: a list of 2: `syslog`, `mail`. [The setting](../ini-settings/audit/AlertRule_audit_disabled.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_audit_disabled]
Sinks[]=expExampleSink    # your class or value, added to the list
*/ ?>
```

## AlertRule_chain_broken Class

Shipped: `match`. [The setting](../ini-settings/audit/AlertRule_chain_broken.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_chain_broken]
Class=match    # replace with your class or value
*/ ?>
```

## AlertRule_chain_broken Sinks

Shipped: a list of 2: `syslog`, `mail`. [The setting](../ini-settings/audit/AlertRule_chain_broken.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_chain_broken]
Sinks[]=expExampleSink    # your class or value, added to the list
*/ ?>
```

## AuditArchiveSettings FormatHandlers

Shipped: a hash of 5: `gzip=expAuditGzipFormat`, `bzip2=expAuditBzip2Format`, `xz=expAuditXzFormat`, `zstd=expAuditZstdFormat`, `zip=expAuditZipFormat`. [The setting](../ini-settings/audit/AuditArchiveSettings.md#formathandlers).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditArchiveSettings]
FormatHandlers[expexample]=expExampleFormatHandler    # key and value are yours
*/ ?>
```

## Related

- [All extension points](README.md)
- [An extension skeleton](../cookbook/extension-skeleton.md)
- [The blocks of audit.ini](../ini-settings/audit/README.md)

---

Generated by `./console exp:help:examples` from `settings/audit.ini`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
