---
title: exp:expusergroups
audience: developer, admin
category: examples
summary: "Benutzergruppen: den Gruppenbaum mit seinen Mitgliedern auflisten, eine Gruppe anlegen, umbenennen und verschieben, eine leere Gruppe in den Papierkorb legen (Änderungen sind ohne --apply ein Probelauf)."
keywords: Konsole, Befehl, exp:expusergroups, expusergroups, console, command, CLI, expusergroups.php, exp:user:groups
type: generated
generated: exp:help:examples
---
# exp:expusergroups

Benutzergruppen: den Gruppenbaum mit seinen Mitgliedern auflisten, eine Gruppe anlegen, umbenennen und verschieben, eine leere Gruppe in den Papierkorb legen (Änderungen sind ohne --apply ein Probelauf).

Skript `bin/php/expusergroups.php`, Code in `kernel/private/classes/commands/expusergroups.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expusergroups --help
./console exp:user:groups --help
php bin/php/expusergroups.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
User groups
list (the default) shows the user group tree below a group, or below the users root, with each
group's node id, its direct members and its subgroups. create makes a group (--name, below
--parent), rename gives it a new name (a new version, published with its workflows), move puts it
below another group, and delete moves an empty group to the trash: a group that still holds
accounts or groups is refused, because removing a group never removes accounts. Groups are named
by node id or name. Without --apply nothing is changed.

./console exp:user:groups
./console exp:user:groups list Editors
./console exp:user:groups create --name=Partners --parent=Members --apply
./console exp:user:groups rename Partners --name="Partner agencies" --apply
./console exp:user:groups move 245 --to=Editors --apply
./console exp:user:groups delete 245 --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--parent` | erforderlich | groups create: die Gruppe, unter die die neue Gruppe kommt (Knoten-ID oder Name; Vorgabe die Wurzel der Benutzer, content.ini [NodeSettings] UserRootNode) |
| `--name` | erforderlich | groups create und rename: der Name der Gruppe |
| `--to` | erforderlich | groups move: die Gruppe, unter die sie verschoben wird (Knoten-ID oder Name); audit: nur Ereignisse an oder vor diesem Tag (JJJJ-MM-TT) |
| `--apply` |  | Die Änderung vornehmen; ohne die Option listet der Befehl nur auf, was er ändern würde (ein Probelauf) |
| `--dry-run` |  | Die Vorgabe, beibehalten für Skripte, die sie übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--as` | erforderlich | Als dieser Benutzer handeln (Anmeldename, E-Mail-Adresse oder ID; Vorgabe der Benutzer aus [UserSettings] UserCreatorID); das Audit hält ihn als Handelnden fest |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[action][group]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expusergroups.php`, `kernel/private/classes/commands/expusergroups.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
