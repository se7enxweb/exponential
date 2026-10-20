---
title: velocity.ini [HTTPSSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [HTTPSSettings] in velocity.ini: Enabled, Certificate, Key, HSTSMaxAge, HSTSIncludeSubDomains, HSTSPreload"
keywords: Einstellung, velocity.ini, HTTPSSettings, INI, setting, Enabled, Certificate, Key, HSTSMaxAge, HSTSIncludeSubDomains, HSTSPreload
type: generated
generated: exp:help:examples
---
# velocity.ini [HTTPSSettings]

Der Block `[HTTPSSettings]` von `settings/velocity.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/velocity.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/velocity.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/velocity.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/velocity.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> manual uses the certificate and key below. Leave Enabled=false to serve  
> plain HTTP only.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[HTTPSSettings]
Enabled=false
*/ ?>
```

## Certificate

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[HTTPSSettings]
Certificate=
*/ ?>
```

## Key

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[HTTPSSettings]
Key=
*/ ?>
```

## HSTSMaxAge

> Strict-Transport-Security on every answer sent over HTTPS: static files,  
> the server's own pages and the scripts' answers, unless an answer carries  
> one already. Never over plain HTTP. In seconds; 0 sends none.  
>
> 300 is what the Apache and nginx front ends of this installation send. A  
> browser that has seen it refuses plain HTTP to this host name, on every  
> port, for that long, and keeps doing so after the setting is lowered: raise  
> it (a year is 31536000) only once every name of the site is served over  
> HTTPS for good.  

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[HTTPSSettings]
HSTSMaxAge=300
*/ ?>
```

## HSTSIncludeSubDomains

> Also for every subdomain of the host (enabled or disabled).  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[HTTPSSettings]
HSTSIncludeSubDomains=disabled
*/ ?>
```

## HSTSPreload

> Ask to be put on the browsers' preload lists (enabled or disabled). Needs  
> HSTSIncludeSubDomains and a max-age of at least a year to be accepted.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[HTTPSSettings]
HSTSPreload=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von velocity.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/velocity.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
