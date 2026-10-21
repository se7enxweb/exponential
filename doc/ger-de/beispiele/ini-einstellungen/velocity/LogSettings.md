---
title: velocity.ini [LogSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [LogSettings] in velocity.ini: Enabled, Dir, AccessName, ErrorName, Format, FileMode, DirMode"
keywords: Einstellung, velocity.ini, LogSettings, INI, setting, Enabled, Dir, AccessName, ErrorName, Format, FileMode, DirMode
type: generated
generated: exp:help:examples
---
# velocity.ini [LogSettings]

Der Block `[LogSettings]` von `settings/velocity.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/velocity.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/velocity.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/velocity.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/velocity.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Das eigene Zugriffs- und Fehlerlog des Servers, eine Zeile pro Anfrage und eine pro  
> Fehler. LogFile oben ist nur das, was der Prozess auf der Konsole ausgibt.  
>
> An, weil der Server ohne es gar nichts schreibt: kein Zugriffslog,  
> kein Fehlerlog, keine Aufzeichnung einer einzigen Anfrage.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LogSettings]
Enabled=enabled
*/ ?>
```

## Dir

> Das Log-Verzeichnis der Engine qbix; relative Pfade gelten ab dem  
> Wurzelverzeichnis der Installation. Alles, was Velocity für einen Server schreibt, wird  
> unter var/vc/<engine>/ zusammengehalten: log/ (das der Engine frankenphp ist  
> \[FrankenPHPSettings\] LogDir, das der Engine php \[PHPServerSettings\] LogFile),  
> run/ (PID-Datei, Konsolenausgabe, erzeugte Konfiguration, Admin-Socket) und  
> für qbix der Konfigurationsbaum etc/ und lib/, für frankenphp bin/, tls/ und  
> caddy/. Bis 2026-09 war dies var/log/qbix; eine Installation, die Dir setzt,  
> behält ihr eigenes.  

Standard: `var/vc/qbix/log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LogSettings]
Dir=var/vc/qbix/log
*/ ?>
```

## AccessName

> Die Dateinamen. Leer benennt sie nach dem Standard-Siteaccess  
> (\[SiteSettings\] DefaultAccess in site.ini): site-access.log und  
> site-error.log für einen Siteaccess namens "site", was mehrere  
> Installationen in einem Verzeichnis auseinanderhält. Die Rotation setzt das Datum vor  
> die Endung, aus site-access.log wird also site-access.2026-09-24.log.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LogSettings]
AccessName=
*/ ?>
```

## ErrorName

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LogSettings]
ErrorName=
*/ ?>
```

## Format

> Die Zugriffszeile.  
>
>   combined   was Apache und nginx schreiben, für Werkzeuge, die diese lesen. Hier der  
>              Standard.  
>   qbix       combined, plus die Zeit, die die Antwort brauchte. Der eigene  
>              Standard des Servers.  
>   common     combined ohne Referrer und User-Agent.  
>
> Alles andere wird als Format-Zeichenkette genommen, mit %{Host}i und Ähnlichem.  

Standard: `combined`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LogSettings]
Format=combined
*/ ?>
```

## FileMode

> Berechtigungen der Logdateien, oktal. Leer überlässt sie der Umask. Ein  
> Zugriffslog nennt die Menschen, die die Website besucht haben, es ist also nicht für jedes Konto  
> auf dem Rechner zum Lesen da.  

Standard: `0640`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LogSettings]
FileMode=0640
*/ ?>
```

## DirMode

> Berechtigungen des Log-Verzeichnisses, oktal, auch auf ein schon vorhandenes angewendet.  
> Leer überlässt es der Umask, was in der Praxis 0755 ist: Jeder  
> könnte die Dateien auflisten, und 0640 auf jeder verbirgt nur ihren Inhalt.  
> 0750 passt zu 0640. Für eine Gruppe, die die Logs teilt, 2770 mit einem FileMode  
> von 0660: Das setgid-Bit gibt die Gruppe an neue Dateien weiter, es gibt  
> der Gruppe keinen Schreibzugriff. chmod braucht den Besitz, ein Verzeichnis, das ein anderer Benutzer  
> angelegt hat, behält also seinen Modus, und der Server sagt das beim Start.  

Standard: `0750`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LogSettings]
DirMode=0750
*/ ?>
```

## Siehe auch

- [Alle Blöcke von velocity.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/velocity.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
