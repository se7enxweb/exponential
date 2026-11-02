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

> Welche Ereignisse aufgezeichnet werden, nach Taxonomie-Muster: eine ganze Domäne (content.\*), ein Gegenstand  
> (content.node.\*), eine Aktion mit ihren Details (content.node.remove.\*) oder ein einzelner Name.  
> Das genaueste Muster gewinnt; bei Gleichstand gewinnt die spätere Zeile. Ereignisse, die im Katalog als "always"  
> markiert sind (system.audit.\*, access.view.sensitive), ignorieren Disabled\[\].  
> Migrationen (migration.ini): ein Lauf, eine angewendete Migration (auch eine mit --force --reason angenommene  
> geänderte Datei), eine gescheiterte oder zurückgenommene, ein angelegter Sicherungspunkt, ein Push des  
> Migrations-Repositorys, und die Anfragen, Mails und Konsolenbefehle ihrer Schritte http, mail und command.  
> Eine Site, die system.\* abschaltet, behält sie mit diesen Zeilen.  
> Statisches Veröffentlichen: eine Veröffentlichung (auch ein erneut versuchter Push), ein Zurücknehmen, ein erzeugter  
> Deploy-Key, ein gespeichertes Ziel, ein Build, ein Post an den Formular-Endpunkt.  

Standard: eine Liste mit 52: `access.*`, `system.*`, `system.migration.run`, `system.migration.apply`, `system.migration.fail`, `system.migration.rollback`, `system.migration.caption`, `system.migration.push`, `system.migration.http`, `system.migration.mail`, `system.migration.command`, `system.migration.record` ....

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
Enabled[]=system.migration.record
Enabled[]=system.migration.deploy
Enabled[]=system.migration.deploy.rollback
Enabled[]=system.migration.remote
Enabled[]=system.staticpublish.publish
Enabled[]=system.staticpublish.rollback
Enabled[]=system.staticpublish.credential
Enabled[]=system.staticpublish.target
Enabled[]=system.staticpublish.build
# ... 32 weitere in der ausgelieferten Datei
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
