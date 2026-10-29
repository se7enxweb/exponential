---
title: cronjob.ini [CronjobPart-infrequent]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CronjobPart-infrequent] in cronjob.ini: Scripts"
keywords: Einstellung, cronjob.ini, CronjobPart-infrequent, INI, setting, Scripts
type: generated
generated: exp:help:examples
---
# cronjob.ini [CronjobPart-infrequent]

Der Block `[CronjobPart-infrequent]` von `settings/cronjob.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/cronjob.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/cronjob.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/cronjob.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/cronjob.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Scripts

> E-mail preferences retention (mailpreferences.ini \[ConsentSettings\], \[SuppressionSettings\])  
> Signed-in sessions: expired ones end, ended ones go after site.ini \[UserSessionSettings\] RetentionDays  
> User audit log: rows go after audit.ini \[UserAuditSettings\] RetentionDays (doc/eng-us/features/6.0/user-audit.md)  
> Help maintained as content: old answers to "Was this helpful?" go after exphelp.ini \[ContentSettings\] FeedbackKeepDays,  
> and with DitaHistoryDirectory set the DITA export of the help is recorded in git (doc/eng-us/features/6.0/help-content.md)  

Standard: eine Liste mit 6: `basket_cleanup.php`, `linkcheck.php`, `mailpreferences.php`, `usersessions.php`, `useraudit.php`, `exphelp.php`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-infrequent]
# ein leeres "Scripts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Scripts[]
Scripts[]=basket_cleanup.php
Scripts[]=linkcheck.php
Scripts[]=mailpreferences.php
Scripts[]=usersessions.php
Scripts[]=useraudit.php
Scripts[]=exphelp.php
*/ ?>
```

## Siehe auch

- [Alle Blöcke von cronjob.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
