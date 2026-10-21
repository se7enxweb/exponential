---
title: site.ini [ProxySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ProxySettings] in site.ini: ProxyServer, User, Password"
keywords: Einstellung, site.ini, ProxySettings, INI, setting, ProxyServer, User, Password
type: generated
generated: exp:help:examples
---
# site.ini [ProxySettings]

Der Block `[ProxySettings]` von `settings/site.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ProxyServer

> Wenn ein Benutzer in linkcheck.php/rssimport.php oder beim Herunterladen von Seitenpaketen im „Setup-Assistenten“ einen Proxy verwenden will.  
> Wenn leer, wird kein Proxy verwendet.  
> Beispiel:  http://<host>:<port>  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ProxySettings]
ProxyServer=
*/ ?>
```

## User

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ProxySettings]
User=
*/ ?>
```

## Password

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ProxySettings]
Password=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
