---
title: logfile.ini [AccessLogFileSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AccessLogFileSettings] in logfile.ini: StorageDir, LogFileName, SitePrefix, PathPrefix, Host, Port, Hosts"
keywords: Einstellung, logfile.ini, AccessLogFileSettings, INI, setting, StorageDir, LogFileName, SitePrefix, PathPrefix, Host, Port, Hosts
type: generated
generated: exp:help:examples
---
# logfile.ini [AccessLogFileSettings]

Der Block `[AccessLogFileSettings]` von `settings/logfile.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/logfile.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/logfile.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/logfile.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/logfile.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StorageDir

> Das Zugriffsprotokoll eines Siteaccess. In settings/override setzen für ein Protokoll, das sich alle  
> Siteaccesses teilen, oder in settings/siteaccess/<siteaccess>/logfile.ini.append.php für das Protokoll  
> eines Siteaccess. Speicherort der Logdatei, zum Beispiel /var/log/httpd  
> Ohne Schrägstrich am Ende  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[AccessLogFileSettings]
StorageDir=
*/ ?>
```

## LogFileName

> Der Name der Logdatei, zum Beispiel mytest.log  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[AccessLogFileSettings]
LogFileName=
*/ ?>
```

## SitePrefix

> Bei der Erkennung der Website über die URL kann die Website etwa http://siteurl/mysite lauten, und  
> das Apache-Log beginnt dann zum Beispiel mit mysite/content/view/full/node\_id. Um  
> dieses Präfix im Apache-Log vor der Auswertung zu entfernen, tragen Sie mysite in  
> SitePrefix ein. Beispiel: SitePrefix\[\]=mysite  
> Entfernt wird nur ein ganzes erstes Pfadelement. Teilen sich mehrere gezählte Siteaccesses das  
> Protokoll, entfernt stattdessen die Siteaccess-Erkennung den Teil des Pfads, der zum Siteaccess gehört.  

Standard: eine Liste mit 1: ``.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[AccessLogFileSettings]
# ein leeres "SitePrefix[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SitePrefix[]
SitePrefix[]=
*/ ?>
```

## PathPrefix

> Wenn Siteaccesses die Einstellung PathPrefix verwenden, tragen Sie sie hier ein  
> Ein Pfad wird zuerst mit jedem Präfix davor gesucht, dann, wie er ist. Leer: der PathPrefix  
> aus site.ini \[SiteAccessSettings\] des Siteaccess.  

Standard: eine Liste mit 1: ``.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[AccessLogFileSettings]
# ein leeres "PathPrefix[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PathPrefix[]
PathPrefix[]=
*/ ?>
```

## Host

> Der Host, an den die Anfragen des Protokolls gingen, für die Siteaccess-Erkennung, wenn seine Zeilen  
> keinen nennen (die Formate common und combined nennen keinen, vhost\_combined schon). Beispiel: www.example.com  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[AccessLogFileSettings]
Host=
*/ ?>
```

## Port

> Der Port der Anfragen des Protokolls, für MatchOrder=port. Leer: 80.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[AccessLogFileSettings]
Port=
*/ ?>
```

## Hosts

> Nur Zeilen dieser Hosts werden für den Siteaccess gezählt (eine Zeile ohne Host nimmt  
> Host). Leer: jeder Host. Beispiel: Hosts\[\]=www.example.com  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[AccessLogFileSettings]
# ein leeres "Hosts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Hosts[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von logfile.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/logfile.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
