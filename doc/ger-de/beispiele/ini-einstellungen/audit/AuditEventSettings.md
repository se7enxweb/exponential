---
title: audit.ini [AuditEventSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditEventSettings] in audit.ini: Enabled, Disabled, Branches, MinSeverity"
keywords: Einstellung, audit.ini, AuditEventSettings, INI, setting, Enabled, Disabled, Branches, MinSeverity
type: generated
generated: exp:help:examples
---
# audit.ini [AuditEventSettings]

Der Block `[AuditEventSettings]` von `settings/audit.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Which events are recorded, by taxonomy pattern: a whole domain (content.\*), a subject  
> (content.node.\*), an action with its details (content.node.remove.\*) or one name.  
> The most specific pattern wins; on a tie the later line wins. Events marked "always"  
> in the catalogue (system.audit.\*, access.view.sensitive) ignore Disabled\[\].  
> Migrations (migration.ini): a run, a migration applied (also a changed file accepted with  
> --force --reason), failed or rolled back, a caption taken, the migrations repository pushed,  
> and the requests, mails and console commands of their http, mail and command steps.  
> A site that switches system.\* off keeps them with these lines.  
> Static publishing: a publish (also a push tried again), a rollback, a deploy key made.  

Standard: eine Liste mit 45: `access.*`, `system.*`, `system.migration.run`, `system.migration.apply`, `system.migration.fail`, `system.migration.rollback`, `system.migration.caption`, `system.migration.push`, `system.migration.http`, `system.migration.mail`, `system.migration.command`, `system.staticpublish.publish` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditEventSettings]
# ein leeres "Enabled[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Enabled[]
Enabled[]=access.*
Enabled[]=system.*
Enabled[]=system.migration.run
Enabled[]=system.migration.apply
Enabled[]=system.migration.fail
Enabled[]=system.migration.rollback
Enabled[]=system.migration.caption
Enabled[]=system.migration.push
Enabled[]=system.migration.http
Enabled[]=system.migration.mail
Enabled[]=system.migration.command
Enabled[]=system.staticpublish.publish
Enabled[]=system.staticpublish.rollback
Enabled[]=system.staticpublish.credential
Enabled[]=content.node.move
Enabled[]=content.node.remove.*
Enabled[]=content.node.hide
Enabled[]=content.node.reveal
Enabled[]=content.node.swap
Enabled[]=content.node.section
# ... 25 weitere in der ausgelieferten Datei
*/ ?>
```

## Disabled

Standard: eine Liste mit 2: `access.session.regenerate`, `access.session.expire`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditEventSettings]
# ein leeres "Disabled[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Disabled[]
Disabled[]=access.session.regenerate
Disabled[]=access.session.expire
*/ ?>
```

## Branches

> Lesezugriffe werden in \[AuditReadSettings\] eingeschaltet, nicht hier.  
>
> Erweiterungen registrieren ihre Taxonomie-Zweige: Name => die Zweigklasse (implementiert  
> expAuditTaxonomyBranch), die die Namen, ihren Schweregrad, Kanal und den  
> Datenschutz ihrer eigenen Felder auflistet. Von der RAD-Survey gezählt (Registry "auditbranches").  
> Branches\[myext\]=myExtAuditBranch  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditEventSettings]
# ein leeres "Branches[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Branches[]
*/ ?>
```

## MinSeverity

> Der Schweregrad (Name nach RFC 5424), unter dem ein Ereignis gar nicht festgehalten wird.  
> debug | info | notice | warning | error | critical | alert | emergency  

Standard: `info`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditEventSettings]
MinSeverity=info
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
