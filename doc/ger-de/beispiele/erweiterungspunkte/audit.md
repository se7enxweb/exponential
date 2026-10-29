---
title: Erweiterungspunkte in audit.ini
audience: developer, admin
category: examples
summary: Einstellungen von audit.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (25, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, audit.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in audit.ini

Einstellungen von `settings/audit.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/audit.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## AuditChannel_content Sinks

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/audit/AuditChannel_content.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditChannel_content]
Sinks[]=expExampleSink    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## AuditChannel_access Sinks

Ausgeliefert: eine Liste mit 2: `syslog`, `useraudit`. [Die Einstellung](../ini-einstellungen/audit/AuditChannel_access.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditChannel_access]
Sinks[]=expExampleSink    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## AuditChannel_system Sinks

Ausgeliefert: eine Liste mit 1: `syslog`. [Die Einstellung](../ini-einstellungen/audit/AuditChannel_system.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditChannel_system]
Sinks[]=expExampleSink    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## AuditChannel_commerce Sinks

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/audit/AuditChannel_commerce.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditChannel_commerce]
Sinks[]=expExampleSink    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## AuditChannel_read Sinks

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/audit/AuditChannel_read.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditChannel_read]
Sinks[]=expExampleSink    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## AuditReadSettings Classes

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/audit/AuditReadSettings.md#classes).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditReadSettings]
Classes[]=expExampleClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## AuditSinkSettings SinkClasses

Ausgeliefert: ein Hash mit 4: `syslog=expAuditSyslogSink`, `webhook=expAuditWebhookSink`, `mail=expAuditMailSink`, `useraudit=expAuditUserAuditSink`. [Die Einstellung](../ini-einstellungen/audit/AuditSinkSettings.md#sinkclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditSinkSettings]
SinkClasses[expexample]=expExampleSinkClasse    # Schlüssel und Wert wählen Sie
*/ ?>
```

## AuditSink_syslog Transport

Ausgeliefert: `local`. [Die Einstellung](../ini-einstellungen/audit/AuditSink_syslog.md#transport).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditSink_syslog]
Transport=local    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## AuditSink_mail Transport

Ausgeliefert: leer. [Die Einstellung](../ini-einstellungen/audit/AuditSink_mail.md#transport).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditSink_mail]
Transport=    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## AuditAlertSettings RuleClasses

Ausgeliefert: ein Hash mit 3: `threshold=expAuditThresholdRule`, `match=expAuditMatchRule`, `schedule=expAuditScheduleRule`. [Die Einstellung](../ini-einstellungen/audit/AuditAlertSettings.md#ruleclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditAlertSettings]
RuleClasses[expexample]=expExampleRuleClasse    # Schlüssel und Wert wählen Sie
*/ ?>
```

## AlertRule_brute_force Class

Ausgeliefert: `threshold`. [Die Einstellung](../ini-einstellungen/audit/AlertRule_brute_force.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_brute_force]
Class=threshold    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## AlertRule_brute_force Sinks

Ausgeliefert: eine Liste mit 2: `syslog`, `mail`. [Die Einstellung](../ini-einstellungen/audit/AlertRule_brute_force.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_brute_force]
Sinks[]=expExampleSink    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## AlertRule_brute_force_user Class

Ausgeliefert: `threshold`. [Die Einstellung](../ini-einstellungen/audit/AlertRule_brute_force_user.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_brute_force_user]
Class=threshold    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## AlertRule_brute_force_user Sinks

Ausgeliefert: eine Liste mit 2: `syslog`, `mail`. [Die Einstellung](../ini-einstellungen/audit/AlertRule_brute_force_user.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_brute_force_user]
Sinks[]=expExampleSink    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## AlertRule_admin_role_granted Class

Ausgeliefert: `match`. [Die Einstellung](../ini-einstellungen/audit/AlertRule_admin_role_granted.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_admin_role_granted]
Class=match    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## AlertRule_admin_role_granted Sinks

Ausgeliefert: eine Liste mit 2: `syslog`, `mail`. [Die Einstellung](../ini-einstellungen/audit/AlertRule_admin_role_granted.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_admin_role_granted]
Sinks[]=expExampleSink    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## AlertRule_settings_out_of_hours Class

Ausgeliefert: `schedule`. [Die Einstellung](../ini-einstellungen/audit/AlertRule_settings_out_of_hours.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_settings_out_of_hours]
Class=schedule    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## AlertRule_settings_out_of_hours Sinks

Ausgeliefert: eine Liste mit 1: `syslog`. [Die Einstellung](../ini-einstellungen/audit/AlertRule_settings_out_of_hours.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_settings_out_of_hours]
Sinks[]=expExampleSink    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## AlertRule_mass_delete Class

Ausgeliefert: `threshold`. [Die Einstellung](../ini-einstellungen/audit/AlertRule_mass_delete.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_mass_delete]
Class=threshold    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## AlertRule_mass_delete Sinks

Ausgeliefert: eine Liste mit 2: `syslog`, `mail`. [Die Einstellung](../ini-einstellungen/audit/AlertRule_mass_delete.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_mass_delete]
Sinks[]=expExampleSink    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## AlertRule_audit_disabled Class

Ausgeliefert: `match`. [Die Einstellung](../ini-einstellungen/audit/AlertRule_audit_disabled.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_audit_disabled]
Class=match    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## AlertRule_audit_disabled Sinks

Ausgeliefert: eine Liste mit 2: `syslog`, `mail`. [Die Einstellung](../ini-einstellungen/audit/AlertRule_audit_disabled.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_audit_disabled]
Sinks[]=expExampleSink    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## AlertRule_chain_broken Class

Ausgeliefert: `match`. [Die Einstellung](../ini-einstellungen/audit/AlertRule_chain_broken.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_chain_broken]
Class=match    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## AlertRule_chain_broken Sinks

Ausgeliefert: eine Liste mit 2: `syslog`, `mail`. [Die Einstellung](../ini-einstellungen/audit/AlertRule_chain_broken.md#sinks).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AlertRule_chain_broken]
Sinks[]=expExampleSink    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## AuditArchiveSettings FormatHandlers

Ausgeliefert: ein Hash mit 5: `gzip=expAuditGzipFormat`, `bzip2=expAuditBzip2Format`, `xz=expAuditXzFormat`, `zstd=expAuditZstdFormat`, `zip=expAuditZipFormat`. [Die Einstellung](../ini-einstellungen/audit/AuditArchiveSettings.md#formathandlers).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/audit.ini.append.php
[AuditArchiveSettings]
FormatHandlers[expexample]=expExampleFormatHandler    # Schlüssel und Wert wählen Sie
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von audit.ini](../ini-einstellungen/audit/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
