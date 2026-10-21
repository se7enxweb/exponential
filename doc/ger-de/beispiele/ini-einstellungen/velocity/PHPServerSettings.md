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

> Die Engine php (ServerSettings Engine=php, der ausgelieferte Standard, oder  
> --engine=php): die Entwicklungs-Engine, der eingebaute Webserver von PHP,  
> \`php -S Host:Port -t DocumentRoot bin/php/velocity-router.php\`.  
>
> Das PHP-Handbuch nennt ihn einen Entwicklungsserver, und er ist einer: nur einfaches HTTP,  
> kein Neuladen (graceful startet ihn neu), Dateien ohne Cache-Lebensdauer gesendet,  
> das Anfrage-Log auf seiner Konsole (LogFile unten). Workers wird zu  
> PHP\_CLI\_SERVER\_WORKERS, sodass so viele Anfragen gleichzeitig bedient werden (nur  
> Linux; anderswo eine nach der anderen). Er läuft mit der php.ini und den  
> Erweiterungen dieses PHP plus \[PHPSettings\] IniOptions\[\]; lassen Sie Host auf 127.0.0.1.  
> Die eigene Adresse dieser Engine. Leer = der Wert aus \[ServerSettings\]; gesetzt, damit  
> sie neben den anderen Engines laufen kann. Workers wird zu PHP\_CLI\_SERVER\_WORKERS.  

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

> Eine andere PHP-Binärdatei als die, die exp:velocity ausführt. Leer = dieselbe.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[PHPServerSettings]
PHPBinary=
*/ ?>
```

## PidFile

> Der eingebaute Server gibt jede Anfrage und die Fehler von PHP auf seiner Konsole aus;  
> dorthin geht das, sein Zugriffs- und Fehlerlog in einem.  

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

> Zusätzliche Argumente für php, unverändert nach dem Router angehängt.  

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
