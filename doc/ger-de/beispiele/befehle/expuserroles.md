---
title: exp:expuserroles
audience: developer, admin
category: examples
summary: "Rollenzuweisungen: die eines Benutzers oder einer Gruppe auflisten, eine Rolle mit oder ohne Teilbaum- oder Bereichseinschränkung zuweisen, sie entziehen, wie es role/assign tut (Änderungen sind ohne --apply ein Probelauf)."
keywords: Konsole, Befehl, exp:expuserroles, expuserroles, console, command, CLI, expuserroles.php, exp:user:roles
type: generated
generated: exp:help:examples
---
# exp:expuserroles

Rollenzuweisungen: die eines Benutzers oder einer Gruppe auflisten, eine Rolle mit oder ohne Teilbaum- oder Bereichseinschränkung zuweisen, sie entziehen, wie es role/assign tut (Änderungen sind ohne --apply ein Probelauf).

Skript `bin/php/expuserroles.php`, Code in `kernel/private/classes/commands/expuserroles.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuserroles --help
./console exp:user:roles --help
php bin/php/expuserroles.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Role assignments of users and groups
list <target> shows the roles that reach a user or a group, with where each comes from (the
account, the group or a group above it) and its limitation. assign <role> <target> assigns a role,
limited to a subtree (--subtree=<node id>) or a section (--section=<id or identifier>) or without a
limitation; unassign <role> <target> removes the role's assignments from the target (with --subtree
or --section only that limited one). The role is named by id or name; the target is a user (login,
e-mail or id) or group:<node id or name>. The audit records access.role.assign and
access.role.unassign, and the role, policy and content caches are cleared as role/assign does.
Without --apply nothing is changed.

./console exp:user:roles list jane
./console exp:user:roles list group:Editors
./console exp:user:roles assign Editor group:Partners --subtree=2 --apply
./console exp:user:roles assign "Member management" jane --section=standard --apply
./console exp:user:roles unassign Editor jane --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--subtree` | erforderlich | roles assign und unassign: die Zuweisung beschränkt auf den Teilbaum dieses Knotens (Knoten-ID) |
| `--section` | erforderlich | roles assign und unassign: die Zuweisung beschränkt auf diesen Bereich (ID oder Bezeichner) |
| `--apply` |  | Die Änderung vornehmen; ohne die Option listet der Befehl nur auf, was er ändern würde (ein Probelauf) |
| `--dry-run` |  | Die Vorgabe, beibehalten für Skripte, die sie übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--as` | erforderlich | Als dieser Benutzer handeln (Anmeldename, E-Mail-Adresse oder ID; Vorgabe der Benutzer aus [UserSettings] UserCreatorID); das Audit hält ihn als Handelnden fest |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[action][role][target]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuserroles.php`, `kernel/private/classes/commands/expuserroles.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
