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

> Welche Ereignisse festgehalten werden, nach Taxonomie-Muster: eine ganze Domäne (content.\*), ein Gegenstand  
> (content.node.\*), eine Aktion mit ihren Details (content.node.remove.\*) oder ein Name.  
> Das spezifischste Muster gewinnt; bei Gleichstand gewinnt die spätere Zeile. Im Katalog als "always" markierte Ereignisse  
> (system.audit.\*, access.view.sensitive) ignorieren Disabled\[\].  

Standard: eine Liste mit 33: `access.*`, `system.*`, `content.node.move`, `content.node.remove.*`, `content.node.hide`, `content.node.reveal`, `content.node.swap`, `content.node.section`, `content.object.remove`, `content.object.purge`, `content.object.restore`, `content.object.state` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditEventSettings]
# ein leeres "Enabled[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Enabled[]
Enabled[]=access.*
Enabled[]=system.*
Enabled[]=content.node.move
Enabled[]=content.node.remove.*
Enabled[]=content.node.hide
Enabled[]=content.node.reveal
Enabled[]=content.node.swap
Enabled[]=content.node.section
Enabled[]=content.object.remove
Enabled[]=content.object.purge
Enabled[]=content.object.restore
Enabled[]=content.object.state
Enabled[]=content.object.translation.remove
Enabled[]=content.version.remove
Enabled[]=content.trash.empty
Enabled[]=content.class.create
Enabled[]=content.class.change
Enabled[]=content.class.remove
Enabled[]=content.section.*
Enabled[]=content.state.*
# ... 13 weitere in der ausgelieferten Datei
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
