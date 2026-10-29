---
title: exp:ezrequestrules
audience: developer, admin
category: examples
summary: Listet und prüft die Anfrageregeln eines Siteaccess und erklärt ihre Entscheidung für eine Adresse.
keywords: Konsole, Befehl, exp:ezrequestrules, ezrequestrules, console, command, CLI, ezrequestrules.php
type: generated
generated: exp:help:examples
---
# exp:ezrequestrules

Listet und prüft die Anfrageregeln eines Siteaccess und erklärt ihre Entscheidung für eine Adresse.

Skript `bin/php/ezrequestrules.php`, Code in `kernel/private/classes/commands/ezrequestrules.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezrequestrules --help
php bin/php/ezrequestrules.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Exponential request rules
Lists and checks the rules of a siteaccess and explains their decision for an address.

  php bin/php/ezrequestrules.php -s site --list
  php bin/php/ezrequestrules.php -s site --check
  php bin/php/ezrequestrules.php -s site --uri=content/view/full/2
  php bin/php/ezrequestrules.php -s site --uri=Fit-Healthy --user=admin
  php bin/php/ezrequestrules.php -s site --uri=content/view/full/2 --header=X-Requested-With:XMLHttpRequest --ip=10.1.2.3
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--list` |  | Die Regeln in der Reihenfolge auflisten, in der sie gefragt werden |
| `--check` |  | Probleme in den Einstellungen melden; Exit-Code 1, wenn es welche gibt |
| `--uri` | erforderlich | Die Entscheidung für diese Adresse erklären, so wie sie nach Host und Siteaccess eingegeben wird (z. B. content/view/full/2 oder Fit-Healthy) |
| `--user` | erforderlich | Das Login des Benutzers, für den es erklärt wird (Standard: der anonyme Benutzer) |
| `--method` | erforderlich | HTTP-Methode (Standard GET) |
| `--host` | erforderlich | Hostname, den der Besucher verwendet hat |
| `--ip` | erforderlich | Client-Adresse |
| `--scheme` | erforderlich | http oder https (Standard https) |
| `--header` | erforderlich | Ein Request-Header, Name:Wert; kann mehrmals angegeben werden |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezrequestrules.php`, `kernel/private/classes/commands/ezrequestrules.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
