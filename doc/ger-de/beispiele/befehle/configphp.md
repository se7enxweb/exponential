---
title: exp:configphp
audience: developer, admin
category: examples
summary: "Listet, liest, setzt und entfernt die define()-Einstellungen der config.php, mit Typ und mit php -l geprüft, beschrieben durch config.php-RECOMMENDED (set und unset: Probelauf ohne --apply)."
keywords: Konsole, Befehl, exp:configphp, configphp, console, command, CLI, configphp.php, exp:config:list, exp:config:get, exp:config:set, exp:config:unset
type: generated
generated: exp:help:examples
---
# exp:configphp

Listet, liest, setzt und entfernt die define()-Einstellungen der config.php, mit Typ und mit php -l geprüft, beschrieben durch config.php-RECOMMENDED (set und unset: Probelauf ohne --apply).

Skript `bin/php/configphp.php`, Code in `kernel/private/classes/commands/configphp.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:configphp --help
./console exp:config:list --help
./console exp:config:get --help
./console exp:config:set --help
./console exp:config:unset --help
php bin/php/configphp.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
The define() settings of config.php

  ./console exp:config:list [--only-set] [--format=text|json]     every setting config.php-RECOMMENDED documents, with
                                                                 the value config.php has, the proposed one and the description
  ./console exp:config:get <NAME> [--format=text|json]           the value (exit 1 when it is not set)
  ./console exp:config:set <NAME> <value> [--type=<t>] [--apply] set it: in place, else where the documentation has it, else added
  ./console exp:config:unset <NAME> [--apply]                    comment it out again

config.php is read as PHP tokens, never run; only the value of the one define() changes, comments and
order stay. set and unset show the diff and write nothing without --apply (--dry-run is the default;
both together: exit 2). A write backs config.php up to var/backup/config/<time>/ (mode and owner
kept), checks the new file with php -l and renames it into place. A config.php that does not compile is
refused. Values of names with PASS, SECRET, KEY, TOKEN are shown as ******** unless --show-secrets.
Types (--type): bool, int, octal, float, string, null; without --type the current or the documented
value decides, true/false/null are always the PHP values. A value starting with "-": --value=<value>.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | set, unset: die config.php schreiben (ohne die Option wird nur der Diff gezeigt) |
| `--dry-run` |  | Der Standard, erhalten für Skripte, die die Option übergeben: den Diff zeigen, nichts ändern |
| `--type` | erforderlich | set: der Typ des Werts: bool, int, octal, float, string, null |
| `--value` | erforderlich | set: der Wert (statt des Arguments nach dem Namen), für einen Wert, der mit "-" beginnt |
| `--allow-unknown` |  | set: eine Einstellung hinzufügen, die config.php-RECOMMENDED nicht beschreibt |
| `--show-secrets` |  | Die Werte von Passwörtern, Geheimnissen, Schlüsseln und Tokens anzeigen |
| `--format` | erforderlich | list, get: text (Standard) oder json |
| `--only-set` |  | list: nur die Einstellungen, die config.php setzt |
| `--root` | erforderlich | Das Wurzelverzeichnis der Installation (Standard: die Installation, zu der der Befehl gehört) |
| `--file` | erforderlich | Die Konfigurationsdatei (Standard: <root>/config.php) |
| `--recommended` | erforderlich | Die beschriebenen Einstellungen (Standard: <root>/config.php-RECOMMENDED) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[verb][name][value]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/configphp.php`, `kernel/private/classes/commands/configphp.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
