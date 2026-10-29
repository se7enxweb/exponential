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

> Der Cronjob der Installation: der Setup-Assistent, der Kickstarter und exp:install schreiben die  
> Crontab-Zeilen, die die folgenden Teile ausführen, in die Crontab des Systembenutzers, dem das  
> Installationsverzeichnis gehört (nie in die Crontab von root), zwischen die Markierungszeilen  
> "# BEGIN Exponential cronjobs: <root>" und "# END Exponential cronjobs: <root>".  
> bin/php/installcrontab.php --show, --add, --remove (mit --dry-run) tut dasselbe in einer  
> bestehenden Installation. Ohne Crontab (ein Container, kein crontab-Befehl, ein anderer Benutzer)  
> gibt der Installer die hinzuzufügenden Zeilen aus und macht weiter.  
> Anleitung: doc/eng-us/bc/6.0/installer-crontab.md  
>
> disabled: die Installer schreiben nichts und geben stattdessen die Zeilen aus (für einen exp:install-Lauf:  
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

> Die Teile, die der Cronjob ausführt, jeweils nur, wenn die Installation ihn mit mindestens einem  
> Skript definiert (der Teil einer Erweiterung, wenn die Erweiterung aktiv ist). Eine Erweiterung kann in ihrer  
> settings/cronjob.ini.append.php eigene hinzufügen. Der Zeitplan ist \[AdminSettings\] CrontabSchedule\_<part>.  

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

> Die Teile, die mit dem Admin-Siteaccess laufen (-s <admin>): ihre Erweiterungen sind nur dort aktiv,  
> oder ihre Jobs brauchen die Einstellungen der Administration. Die anderen laufen mit dem Standard-Siteaccess.  

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

> Der Admin-Siteaccess von AdminParts\[\] (in settings/override setzen). Leer: admin, wenn er existiert,  
> sonst der erste Siteaccess aus AvailableSiteAccessList\[\] mit "admin" im Namen.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CrontabSettings]
AdminSiteAccess=
*/ ?>
```

## CrontabCommand

> Der Crontab-Befehl: ein Name im PATH oder ein vollständiger Pfad.  

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
