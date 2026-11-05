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

> Dashboard > Neue Inhalte (das Modul moderation, doc/eng-us/features/6.0/moderation.md): die zuletzt erstellten  
> und geänderten Inhalte mit ihrem Status, der Zurückhaltung zur Prüfung, wer sie eingereicht hat und von wo, und Aktionen auf einer Auswahl.  
> Die Seitengrößen sind admininterface.ini \[PaginationSettings\] ItemsPerPageList\_moderation\_recent\[\].  
>
> Eine Auswahl von mehr als so vielen Objekten läuft als Inhaltsjob im Hintergrund, mit ihrem Fortschritt auf  
> content/job/<id> und dem Ergebnis je Objekt auf moderation/report/<id>; bis dahin läuft die Aktion in der Anfrage.  

Standard: `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ModerationSettings]
JobThreshold=100
*/ ?>
```

## SubmissionEvents

> Die Audit-Ereignisse, aus denen die IP-Adresse und der Browser einer Einreichung gelesen werden (das früheste davon je Objekt,  
> aus dem Audit-Index): Die automatische Zurückhaltung zeichnet immer eines auf; content.object.publish und content.object.create sind  
> standardmäßig aus (audit.ini \[AuditEventSettings\]). Adresse und Browser sind so, wie das Audit sie aufbewahrt  
> (audit.ini \[AuditPrivacySettings\]: standardmäßig gekürzt), und werden nur mit der Richtlinie moderation/personaldata gezeigt.  

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
