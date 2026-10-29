---
title: exp:addoauthauthorizepolicy
audience: developer, admin
category: examples
summary: Gewährt oauth/authorize den Rollen, mit denen sich Benutzer anmelden (Upgrade auf 6.0.15; --dry-run listet sie zuerst auf).
keywords: Konsole, Befehl, exp:addoauthauthorizepolicy, addoauthauthorizepolicy, console, command, CLI, addoauthauthorizepolicy.php
type: generated
generated: exp:help:examples
---
# exp:addoauthauthorizepolicy

Gewährt oauth/authorize den Rollen, mit denen sich Benutzer anmelden (Upgrade auf 6.0.15; --dry-run listet sie zuerst auf).

Skript `bin/php/addoauthauthorizepolicy.php`, Code in `kernel/private/classes/commands/addoauthauthorizepolicy.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:addoauthauthorizepolicy --help
php bin/php/addoauthauthorizepolicy.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Grant the policy oauth/authorize to every role that lets users sign in (user/login),
with the same siteaccess limitation, so the users who could authorize OAuth clients before 6.0.15
still can. Roles that have the policy already are left alone; the script can run again.

Example: php bin/php/addoauthauthorizepolicy.php --dry-run
         php bin/php/addoauthauthorizepolicy.php --role=Editor --role=5
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--dry-run` |  | Auflisten, was gewährt würde, nichts ändern |
| `--role` | erforderlich | Nur diese Rolle (ID oder Name), kann wiederholt werden; eine genannte Rolle ohne user/login erhält eine unbeschränkte Richtlinie |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/addoauthauthorizepolicy.php`, `kernel/private/classes/commands/addoauthauthorizepolicy.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
