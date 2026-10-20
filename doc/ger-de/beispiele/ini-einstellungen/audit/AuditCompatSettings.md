---
title: audit.ini [AuditCompatSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditCompatSettings] in audit.ini: Map, UnmappedAsLegacy, LegacyFiles"
keywords: Einstellung, audit.ini, AuditCompatSettings, INI, setting, Map, UnmappedAsLegacy, LegacyFiles
type: generated
generated: exp:help:examples
---
# audit.ini [AuditCompatSettings]

Der Block `[AuditCompatSettings]` von `settings/audit.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Map

> eZAudit::writeAudit( <old name> ) wird unter dem neuen Namen festgehalten (Z3).  
> Änderungen der Platzierung (die Namen, die LoveStack schreibt): eine hinzugefügte, eine entfernte, eine neue Hauptplatzierung.  
> Teile aus LoveStack übernommen (mugoweb/ezpublish-legacy, Commit 9c931192e0), Copyright (C) Mugo Web, GPL v2  

Standard: ein Hash mit 18: `user-login=access.session.login`, `user-failed-login=access.session.login.failed`, `content-delete=content.node.remove.trash`, `content-move=content.node.move`, `content-hide=content.node.hide`, `role-change=access.role.change`, `role-assign=access.role.assign`, `section-assign=content.node.section`, `state-assign=content.object.state`, `order-delete=commerce.order.delete`, `location-assign=content.node.add`, `location-remove=content.node.remove` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditCompatSettings]
# ein leeres "Map[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Map[]
Map[user-login]=access.session.login
Map[user-failed-login]=access.session.login.failed
Map[content-delete]=content.node.remove.trash
Map[content-move]=content.node.move
Map[content-hide]=content.node.hide
Map[role-change]=access.role.change
Map[role-assign]=access.role.assign
Map[section-assign]=content.node.section
Map[state-assign]=content.object.state
Map[order-delete]=commerce.order.delete
Map[location-assign]=content.node.add
Map[location-remove]=content.node.remove
Map[main-node-update]=content.node.main
Map[user-password-change]=access.user.password.change
Map[user-password-change-self]=access.user.password.change
Map[user-password-change-self-fail]=access.user.password.change.failed
Map[user-forgotpassword]=access.user.password.reset
Map[user-forgotpassword-fail]=access.user.password.reset.failed
*/ ?>
```

## UnmappedAsLegacy

> Alte Namen ohne Eintrag in Map\[\]: festgehalten als system.legacy.<name>.  enabled | disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditCompatSettings]
UnmappedAsLegacy=enabled
*/ ?>
```

## LegacyFiles

> Auch die alten Textdateien schreiben (altes Format, außerhalb der Kette).  enabled | disabled  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditCompatSettings]
LegacyFiles=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
