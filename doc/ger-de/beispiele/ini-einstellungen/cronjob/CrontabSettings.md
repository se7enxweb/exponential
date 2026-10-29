---
title: cronjob.ini [CrontabSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CrontabSettings] in cronjob.ini: Install, Parts, AdminParts, AdminSiteAccess, CrontabCommand"
keywords: Einstellung, cronjob.ini, CrontabSettings, INI, setting, Install, Parts, AdminParts, AdminSiteAccess, CrontabCommand
type: generated
generated: exp:help:examples
---
# cronjob.ini [CrontabSettings]

Der Block `[CrontabSettings]` von `settings/cronjob.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/cronjob.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/cronjob.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/cronjob.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/cronjob.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Install

> Der Cronjob der Installation: Der Setup-Assistent, der Kickstarter und exp:install schreiben die  
> Crontab-Zeilen, die die Teile unten ausführen, in die Crontab des Systembenutzers, dem das  
> Installationsverzeichnis gehört (nie in die Crontab von root), zwischen die Markierungszeilen  
> "# BEGIN Exponential cronjobs: <root>" und "# END Exponential cronjobs: <root>".  
> bin/php/installcrontab.php --show, --add, --remove tut dasselbe bei einer bestehenden  
> Installation (--add und --remove zeigen, was sich ändern würde; mit --apply schreiben sie). Ohne Crontab (ein Container, kein Befehl crontab, ein anderer Benutzer)  
> gibt der Installer die hinzuzufügenden Zeilen aus und macht weiter.  
> Anleitung: doc/eng-us/bc/6.0/installer-crontab.md  
>
> disabled: Die Installer schreiben nichts und geben die Zeilen stattdessen aus (für einen Lauf von exp:install:  
> --no-crontab oder die Umgebungsvariable EXP\_INSTALL\_CRONTAB=0).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CrontabSettings]
Install=enabled
*/ ?>
```

## Parts

> The parts the cron job runs, each only when the installation defines it with at least one  
> script (an extension's part when the extension is active). An extension can add its own in its  
> settings/cronjob.ini.append.php. The schedule is \[AdminSettings\] CrontabSchedule\_<part>.  

Standard: eine Liste mit 6: `global`, `frequent`, `infrequent`, `expqueue`, `sqliimport`, `expai`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CrontabSettings]
# ein leeres "Parts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Parts[]
Parts[]=global
Parts[]=frequent
Parts[]=infrequent
Parts[]=expqueue
Parts[]=sqliimport
Parts[]=expai
*/ ?>
```

## AdminParts

> The parts that run with the admin siteaccess (-s <admin>): their extensions are active there  
> only, or their jobs need the administration's settings. The others run with the default siteaccess.  

Standard: eine Liste mit 3: `expqueue`, `sqliimport`, `expai`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CrontabSettings]
# ein leeres "AdminParts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AdminParts[]
AdminParts[]=expqueue
AdminParts[]=sqliimport
AdminParts[]=expai
*/ ?>
```

## AdminSiteAccess

> The admin siteaccess of AdminParts\[\] (set it in settings/override). Empty: admin when it exists,  
> else the first siteaccess of AvailableSiteAccessList\[\] with "admin" in its name.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CrontabSettings]
AdminSiteAccess=
*/ ?>
```

## CrontabCommand

> The crontab command: a name on the PATH or a full path.  

Standard: `crontab`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CrontabSettings]
CrontabCommand=crontab
*/ ?>
```

## Siehe auch

- [Alle Blöcke von cronjob.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
