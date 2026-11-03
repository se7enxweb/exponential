---
title: exp:expuserwhoami
audience: developer, admin
category: examples
summary: Als welches Konto die Konsole handelt (der Benutzer aus [UserSettings] UserCreatorID oder --as), mit seinen Rollen und ob es alle Rechte hat.
keywords: Konsole, Befehl, exp:expuserwhoami, expuserwhoami, console, command, CLI, expuserwhoami.php, exp:user:whoami
type: generated
generated: exp:help:examples
---
# exp:expuserwhoami

Als welches Konto die Konsole handelt (der Benutzer aus [UserSettings] UserCreatorID oder --as), mit seinen Rollen und ob es alle Rechte hat.

Skript `bin/php/expuserwhoami.php`, Code in `kernel/private/classes/commands/expuserwhoami.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuserwhoami --help
./console exp:user:whoami --help
php bin/php/expuserwhoami.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Which account the console acts as
The exp:user commands act as the user of [UserSettings] UserCreatorID, or as the user --as names;
the audit records that user as who did it. This shows the account, its roles and whether it has
every right (a role with a policy on every module). Run it before a script that changes accounts.
Changes nothing.

./console exp:user:whoami
./console exp:user:whoami --as=jane --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--as` | erforderlich | Als dieser Benutzer handeln (Anmeldename, E-Mail-Adresse oder ID; Vorgabe der Benutzer aus [UserSettings] UserCreatorID); das Audit hält ihn als Handelnden fest |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuserwhoami.php`, `kernel/private/classes/commands/expuserwhoami.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
