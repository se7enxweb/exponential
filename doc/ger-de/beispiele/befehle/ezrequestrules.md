---
title: exp:ezrequestrules
audience: developer, admin
category: examples
summary: List and check the request rules of a siteaccess and explain their decision for an address.
keywords: Konsole, Befehl, exp:ezrequestrules, ezrequestrules, console, command, CLI, ezrequestrules.php
type: generated
generated: exp:help:examples
---
# exp:ezrequestrules

List and check the request rules of a siteaccess and explain their decision for an address.

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
| `--list` |  | List the rules in the order they are asked |
| `--check` |  | Report problems in the settings; exits 1 when there are any |
| `--uri` | erforderlich | Explain the decision for this address, as typed after the host and siteaccess (e.g. content/view/full/2 or Fit-Healthy) |
| `--user` | erforderlich | The login of the user to explain it for (default: the anonymous user) |
| `--method` | erforderlich | HTTP method (default GET) |
| `--host` | erforderlich | Host name the visitor used |
| `--ip` | erforderlich | Client address |
| `--scheme` | erforderlich | http or https (default https) |
| `--header:*` |  |  |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezrequestrules.php`, `kernel/private/classes/commands/ezrequestrules.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
