---
title: velocity.ini [DashboardSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DashboardSettings] in velocity.ini: Token, Remote"
keywords: Einstellung, velocity.ini, DashboardSettings, INI, setting, Token, Remote
type: generated
generated: exp:help:examples
---
# velocity.ini [DashboardSettings]

Der Block `[DashboardSettings]` von `settings/velocity.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/velocity.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/velocity.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/velocity.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/velocity.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Token

> Who may open the server's own views: /Q/dashboard, /Q/stats, /Q/metrics,  
> the full /Q/health and /Q/phpinfo. By default only this machine.  
>
> Token      a secret; when set, it is required from anywhere (?token=...,  
>            "Authorization: Bearer ...") and opens them remotely. Generate  
>            one with: php -r 'echo bin2hex(random\_bytes(24));'  
> Remote     enabled lets them be opened remotely WITHOUT a token -- only  
>            for a port that a firewall already restricts to trusted  
>            addresses. /Q/phpinfo stays local-only without a token, as it  
>            shows the server process's environment.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[DashboardSettings]
Token=
*/ ?>
```

## Remote

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[DashboardSettings]
Remote=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von velocity.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/velocity.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
