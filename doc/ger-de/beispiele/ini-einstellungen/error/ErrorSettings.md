---
title: error.ini [ErrorSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ErrorSettings] in error.ini: DefaultErrorHandler, DefaultRedirectURL, DefaultRerunURL, DefaultEmbedURL"
keywords: Einstellung, error.ini, ErrorSettings, INI, setting, DefaultErrorHandler, DefaultRedirectURL, DefaultRerunURL, DefaultEmbedURL
type: generated
generated: exp:help:examples
---
# error.ini [ErrorSettings]

Der Block `[ErrorSettings]` von `settings/error.ini`, mit 4 Einstellung(en).

> Globale Fehlereinstellungen, die für alle Fehlertypen gelten  
> Eine Gruppe für einen bestimmten Fehlertyp wird mit ErrorSettings, gefolgt  
> von einem - (Bindestrich) und dem Namen des Fehlertyps definiert, z. B. ErrorSettting-kernel  

Bearbeiten Sie `settings/error.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/error.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/error.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/error.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultErrorHandler

> Statische Seiten für Fehler außerhalb der Template-Engine (lib/ezutils/classes/ezexecution.php):  
> StaticErrorPage\[500\], StaticErrorPage\[503\], StaticErrorPage\[default\] und eine pro bekanntem Fehler-  
> fall, angezeigt statt der allgemeinen Seite, wenn die Installation den Fehler erklärt:  
>   StaticErrorPage\[dependencies\]  die Composer-Bibliotheken (vendor/) fehlen; Platzhalter  
>   {status} {title} {message} {steps} {repair} {reference} {home} {detail}. Nicht gesetzt: eine eingebaute Seite.  
> StaticErrorPage\[dependencies\]=design/standard/errors/dependencies.html  
> Welcher Handler verwendet wird, mögliche Handler sind:  
> displayerror - zeigt einen Text, der den Fehler erklärt  
> redirect     - leitet auf eine bestimmte URL weiter  
> rerun        - führt ein Modul/eine Ansicht auf der Seite erneut aus, angegeben durch eine URL  
> embed        - führt ein Modul/eine Ansicht aus und bettet die Ergebnisse auf  
>                derselben Seite ein, die displayerror verwendet  

Standard: `displayerror`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[ErrorSettings]
DefaultErrorHandler=displayerror
*/ ?>
```

## DefaultRedirectURL

Standard: `/content/view/full/2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[ErrorSettings]
DefaultRedirectURL=/content/view/full/2
*/ ?>
```

## DefaultRerunURL

Standard: `/content/view/full/2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[ErrorSettings]
DefaultRerunURL=/content/view/full/2
*/ ?>
```

## DefaultEmbedURL

Standard: `/user/login`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[ErrorSettings]
DefaultEmbedURL=/user/login
*/ ?>
```

## Siehe auch

- [Alle Blöcke von error.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/error.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
