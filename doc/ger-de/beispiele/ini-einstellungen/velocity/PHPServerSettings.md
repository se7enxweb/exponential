---
title: velocity.ini [PHPServerSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PHPServerSettings] in velocity.ini: Port, Host, Workers, DocumentRoot, PHPBinary, PidFile, LogFile, ExtraOptions"
keywords: Einstellung, velocity.ini, PHPServerSettings, INI, setting, Port, Host, Workers, DocumentRoot, PHPBinary, PidFile, LogFile, ExtraOptions
type: generated
generated: exp:help:examples
---
# velocity.ini [PHPServerSettings]

Der Block `[PHPServerSettings]` von `settings/velocity.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/velocity.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/velocity.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/velocity.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/velocity.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Port

> The php engine (ServerSettings Engine=php, the shipped default, or  
> --engine=php): the development engine, PHP's built-in web server,  
> \`php -S Host:Port -t DocumentRoot bin/php/velocity-router.php\`.  
>
> The PHP manual calls it a development server, and it is one: plain HTTP  
> only, no reload (graceful restarts it), files sent without a cache lifetime,  
> the request log on its console (LogFile below). Workers becomes  
> PHP\_CLI\_SERVER\_WORKERS, so that many requests are served at once (Linux  
> only; elsewhere one at a time). It runs with this PHP's php.ini and  
> extensions plus \[PHPSettings\] IniOptions\[\]; keep Host at 127.0.0.1.  
> This engine's own address. Empty = the \[ServerSettings\] value; set, so that  
> it can run beside the other engines. Workers becomes PHP\_CLI\_SERVER\_WORKERS.  

Standard: `8087`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[PHPServerSettings]
Port=8087
*/ ?>
```

## Host

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[PHPServerSettings]
Host=
*/ ?>
```

## Workers

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[PHPServerSettings]
Workers=
*/ ?>
```

## DocumentRoot

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[PHPServerSettings]
DocumentRoot=
*/ ?>
```

## PHPBinary

> Another PHP binary than the one running exp:velocity. Empty = the same.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[PHPServerSettings]
PHPBinary=
*/ ?>
```

## PidFile

> The built-in server prints each request and PHP's errors to its console;  
> this is where that goes -- its access and error log in one.  

Standard: `var/vc/php/run/server.pid`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[PHPServerSettings]
PidFile=var/vc/php/run/server.pid
*/ ?>
```

## LogFile

Standard: `var/vc/php/log/server.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[PHPServerSettings]
LogFile=var/vc/php/log/server.log
*/ ?>
```

## ExtraOptions

> Extra arguments for php, appended after the router, verbatim.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[PHPServerSettings]
# ein leeres "ExtraOptions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtraOptions[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von velocity.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/velocity.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
