---
title: exp:ezconvertmysqltabletype
audience: developer, admin
category: examples
summary: Wandelt die Tabellen einer MySQL-Datenbank in einen anderen Tabellentyp um (--list, --newtype=TYPE).
keywords: Konsole, Befehl, exp:ezconvertmysqltabletype, ezconvertmysqltabletype, console, command, CLI, ezconvertmysqltabletype.php
type: generated
generated: exp:help:examples
---
# exp:ezconvertmysqltabletype

Wandelt die Tabellen einer MySQL-Datenbank in einen anderen Tabellentyp um (--list, --newtype=TYPE).

Skript `bin/php/ezconvertmysqltabletype.php`, Code in `kernel/private/classes/commands/ezconvertmysqltabletype.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezconvertmysqltabletype --help
php bin/php/ezconvertmysqltabletype.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--host` | erforderlich | Mit der Datenbank auf diesem Host verbinden |
| `--user` | erforderlich | Benutzer für die Anmeldung an der Datenbank |
| `--password` | erforderlich | Passwort für die Verbindung zur Datenbank |
| `--database` | erforderlich |  |
| `--list` |  | Die Tabellentypen auflisten |
| `--newtype` | erforderlich | Die Datenbank in den angegebenen Typ umwandeln. Der Typ ist myisam oder innodb Sorgen Sie vorher für eine SICHERUNG IHRER DATENBANK! |
| `--usecopy` |  | Zum Umwandeln wird die Originaltabelle umbenannt und die Daten in die neue Tabellenstruktur kopiert. Diese Methode ist viel langsamer und birgt ein höheres Risiko, die Daten in der Datenbank zu beschädigen. Sie kann aber den Absturz von MySQL bei der ALTER-Abfrage umgehen. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezconvertmysqltabletype.php`, `kernel/private/classes/commands/ezconvertmysqltabletype.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
