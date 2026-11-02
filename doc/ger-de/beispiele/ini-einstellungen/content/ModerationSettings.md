---
title: content.ini [ModerationSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ModerationSettings] in content.ini: JobThreshold, SubmissionEvents"
keywords: Einstellung, content.ini, ModerationSettings, INI, setting, JobThreshold, SubmissionEvents
type: generated
generated: exp:help:examples
---
# content.ini [ModerationSettings]

Der Block `[ModerationSettings]` von `settings/content.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## JobThreshold

> Dashboard > Recent content (the module moderation, doc/eng-us/features/6.0/moderation.md): the most recently created  
> and modified content with its status, the review hold, who submitted it and from where, and actions on a selection.  
> The page sizes are admininterface.ini \[PaginationSettings\] ItemsPerPageList\_moderation\_recent\[\].  
>
> A selection of more than this many objects runs as a content job in the background, with its progress on  
> content/job/<id> and the result per object on moderation/report/<id>; up to it, the action runs in the request.  

Standard: `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ModerationSettings]
JobThreshold=100
*/ ?>
```

## SubmissionEvents

> The audit events the IP address and the browser of a submission are read from (the earliest of them per object,  
> from the audit index): the auto-hold always records one; content.object.publish and content.object.create are off  
> by default (audit.ini \[AuditEventSettings\]). The address and the browser are as the audit keeps them  
> (audit.ini \[AuditPrivacySettings\]: truncated by default) and are shown only with the policy moderation/personaldata.  

Standard: eine Liste mit 3: `content.object.hold.auto`, `content.object.publish`, `content.object.create`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ModerationSettings]
# ein leeres "SubmissionEvents[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SubmissionEvents[]
SubmissionEvents[]=content.object.hold.auto
SubmissionEvents[]=content.object.publish
SubmissionEvents[]=content.object.create
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
