---
title: exp:userlogincheck
audience: developer, admin
category: examples
summary: "Unique logins: list the logins more than one user account has; --fix-index adds the unique index on ezuser.login once there are none."
keywords: Konsole, Befehl, exp:userlogincheck, userlogincheck, console, command, CLI, userlogincheck.php, exp:user-login-check
type: generated
generated: exp:help:examples
---
# exp:userlogincheck

Unique logins: list the logins more than one user account has; --fix-index adds the unique index on ezuser.login once there are none.

Skript `bin/php/userlogincheck.php`, Code in `kernel/private/classes/commands/userlogincheck.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:userlogincheck --help
./console exp:user-login-check --help
php bin/php/userlogincheck.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Unique logins
Lists the logins that more than one user account has: rows the unique index on ezuser.login
would refuse on this database engine, and logins that differ only in case, which signing in
cannot tell apart. Without --fix-index nothing is changed.

Logins are shown shortened (first letter and length); --show-logins shows them in full.

./console exp:user-login-check
./console exp:user-login-check --json
./console exp:user-login-check --fix-index

--fix-index adds the unique index only after a clean report and refuses otherwise.
Exit code: 0 clean (or index added), 1 duplicates found (or index refused), 2 error.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--fix-index` |  | Add the unique index on ezuser.login when no duplicates are found |
| `--json` |  | Ein JSON-Objekt statt des Textes |
| `--show-logins` |  | Show the logins in full instead of shortened |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/userlogincheck.php`, `kernel/private/classes/commands/userlogincheck.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
