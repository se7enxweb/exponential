---
title: audit.ini [UserAuditSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [UserAuditSettings] in audit.ini: UserAudit, Events, RecordIP, RetentionDays, MaxValueLength"
keywords: Einstellung, audit.ini, UserAuditSettings, INI, setting, UserAudit, Events, RecordIP, RetentionDays, MaxValueLength
type: generated
generated: exp:help:examples
---
# audit.ini [UserAuditSettings]

Der Block `[UserAuditSettings]` von `settings/audit.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## UserAudit

> Das Benutzer-Audit-Log (doc/eng-us/features/6.0/user-audit.md): eine Zeile pro Sicherheitsereignis zu einem  
> Benutzerkonto oder einer Benutzergruppe in der Tabelle expuser\_audit, eine Kopie der Einträge des Access-Kanals,  
> geschrieben von der Senke useraudit (\[AuditChannel\_access\] Sinks\[\]). Lesbar unter Benutzer > Benutzer-Audit-Log  
> (user/audit, Richtlinie user/audit), gefiltert und als CSV exportierbar.  enabled | disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[UserAuditSettings]
UserAudit=enabled
*/ ?>
```

## Events

> Welche Ereignisse: Muster wie in \[AuditEventSettings\]; nur Einträge, die ein Benutzerkonto nennen (das  
> geänderte Konto, angemeldet oder abgewiesen, der Benutzer, dem eine Rolle zugewiesen wurde) oder eine Benutzergruppe, werden behalten.  
> Ein Ereignis, das das Audit nicht aufzeichnet (\[AuditEventSettings\]), erreicht auch die Tabelle nie.  

Standard: eine Liste mit 7: `access.user.*`, `access.role.assign`, `access.role.unassign`, `access.session.*`, `access.passkey.*`, `access.registration.*`, `access.permission.refused`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[UserAuditSettings]
# ein leeres "Events[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Events[]
Events[]=access.user.*
Events[]=access.role.assign
Events[]=access.role.unassign
Events[]=access.session.*
Events[]=access.passkey.*
Events[]=access.registration.*
Events[]=access.permission.refused
*/ ?>
```

## RecordIP

> Die Adresse der Anfrage, so wie \[AuditPrivacySettings\] Field\[actor.ip\] sie belässt (standardmäßig  
> auf /24 oder /48 gekürzt); disabled: wird nie in der Tabelle gespeichert.  enabled | disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[UserAuditSettings]
RecordIP=enabled
*/ ?>
```

## RetentionDays

> Ältere Zeilen entfernt der Cronjob-Teil useraudit (cronjobs/useraudit.php, in der  
> Gruppe infrequent). 0 behält alle.  

Standard: `730`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[UserAuditSettings]
RetentionDays=730
*/ ?>
```

## MaxValueLength

> Der längste gespeicherte Wert vorher oder nachher, in Bytes JSON; ein längerer wird abgeschnitten.  

Standard: `2000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[UserAuditSettings]
MaxValueLength=2000
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
