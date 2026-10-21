---
title: site.ini [AdminEnvironment]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AdminEnvironment] in site.ini: Level, Name, ShortName, Release, ShowVersion, ShowInTitle, BadgeDisplay"
keywords: Einstellung, site.ini, AdminEnvironment, INI, setting, Level, Name, ShortName, Release, ShowVersion, ShowInTitle, BadgeDisplay
type: generated
generated: exp:help:examples
---
# site.ini [AdminEnvironment]

Der Block `[AdminEnvironment]` von `settings/site.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Level

> Die Umgebungsplakette der Administration (Designs admin4 und admin4l): der  
> Name der Umgebung dieser Installation im Kopf, auf der Anmeldeseite und  
> vor dem Seitentitel, damit eine Live-Website nicht für eine Testkopie gehalten wird.  
> Setzen Sie sie pro Umgebung, zum Beispiel in den Variantendateien von EXP\_ENV  
> (settings/override/site.ini.prod.append.php, siehe config.env.php.example).  
> Die Cache-Blöcke des Kopfes werden nach der Plakette unterschieden: Eine geänderte Einstellung erscheint bei der  
> nächsten Anfrage (leeren Sie den INI-Cache, wo EZP\_INI\_FILEMTIME\_CHECK false ist).  
>
> development, test, staging oder production: die Farbe und der Standardname.  
> auto übernimmt die Stufe aus EXP\_ENV: dev und development, test, stage und  
> staging, prod, production und live. Leer (der Standard): keine Plakette.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminEnvironment]
Level=
*/ ?>
```

## Name

> Der Text der Plakette; leer übernimmt den Namen der Stufe (Development, Test, Staging,  
> Production, übersetzt)  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminEnvironment]
Name=
*/ ?>
```

## ShortName

> Der Text, wo der Kopf schmal ist (bis 767px, auch Telefone), mit  
> BadgeDisplay=always oder auf Touchscreens; leer übernimmt den Code der Stufe (DEV, TEST, STAGE, PROD).  
> Der Name wird vorgelesen.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminEnvironment]
ShortName=
*/ ?>
```

## Release

> Freier Text nach dem Namen, zum Beispiel ein Release oder Build; nur angemeldeten  
> Benutzern angezeigt, nicht auf der Anmeldeseite  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminEnvironment]
Release=
*/ ?>
```

## ShowVersion

> enabled fügt nach dem Release die Version von Exponential hinzu (nur für angemeldete Benutzer)  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminEnvironment]
ShowVersion=disabled
*/ ?>
```

## ShowInTitle

> Der Name vor dem Seitentitel: "\[PRODUCTION\] Dashboard - Site"; disabled  
> lässt den Titel, wie er ist  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminEnvironment]
ShowInTitle=enabled
*/ ?>
```

## BadgeDisplay

> hover (der Standard): Die Plakette erscheint nur, solange ein Maus- oder Trackpad-Zeiger  
> auf dem Logo ruht oder das Logo den Tastaturfokus hat (Escape blendet sie aus); auf  
> Touchscreens, die kein Hover kennen, neben dem Logo wie bei always. Screenreader  
> lesen sie mit dem Logo-Link. always: neben dem Logo  
> jederzeit  

Standard: `hover`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminEnvironment]
BadgeDisplay=hover
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
