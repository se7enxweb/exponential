---
title: exp:expmigrationdeploy
audience: developer, admin
category: examples
summary: "Überträgt die Migrationen dieser Installation auf eine oder viele Gegenstellen gleichzeitig und im Gleichstand: Preflight und Prüfung auf allen, dann alle umschalten oder alle zurücksetzen."
keywords: Konsole, Befehl, exp:expmigrationdeploy, expmigrationdeploy, console, command, CLI, expmigrationdeploy.php, exp:migration:deploy
type: generated
generated: exp:help:examples
---
# exp:expmigrationdeploy

Überträgt die Migrationen dieser Installation auf eine oder viele Gegenstellen gleichzeitig und im Gleichstand: Preflight und Prüfung auf allen, dann alle umschalten oder alle zurücksetzen.

Skript `bin/php/expmigrationdeploy.php`, Code in `kernel/private/classes/commands/expmigrationdeploy.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationdeploy --help
./console exp:migration:deploy --help
php bin/php/expmigrationdeploy.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Deploy migrations to remotes, all at once and in sync
Sends this installation's migrations (the site's and the active extensions') to the selected remotes of
migration.ini ([RemoteSettings] RemoteList[], one [Remote_<id>] each) and runs them there. A preflight
on every remote in parallel (reachable, run lock free, nothing failed, the same kernel version, no file
changed after it was applied) must pass before anything changes; the files go to every remote and are
verified there; then every remote applies with one shared run id. When one remote fails, every remote
that applied is taken back to its caption, so all of them stay in the same state. Without --apply it is
a dry run: the preflight and the plan, nothing is sent or changed.

./console exp:migration:deploy --remotes=stage,production
./console exp:migration:deploy --remotes=production --apply
./console exp:migration:deploy --remotes=all --transfer-only --apply
./console exp:migration:deploy --remotes=beta --only=20261102090000_add_news_class --apply --format=json

Staged: --staged puts the remotes in stages by their environment ([RemoteSettings] StageOrder[]); the
first stage is switched, then the deploy waits at a gate until --continue=<deploy id> switches the
next one. --rollback=<deploy id> takes back every remote that applied a deploy.

./console exp:migration:deploy --remotes=all --staged --apply
./console exp:migration:deploy --continue=20261102-120000-a1b2c3 --apply
./console exp:migration:deploy --rollback=20261102-120000-a1b2c3 --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--remotes` | erforderlich | Die Gegenstellen: IDs oder Gruppennamen aus [RemoteSettings] RemoteList[] in migration.ini, oder all (durch Kommas getrennt oder wiederholt) |
| `--apply` |  | Die Änderungen vornehmen (ohne die Option ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, angenommen für Skripte, die ihn übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--transfer-only` |  | Die Migrationsdateien senden und prüfen, aber nicht ausführen (mit --apply; der einzige Modus für eine FTP-Gegenstelle ohne Trigger) |
| `--only` | erforderlich | Nur diese Migrationen (Migrations-IDs, als Komma-Liste oder wiederholt) |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |
| `--parallel` | erforderlich | An wie vielen Gegenstellen gleichzeitig gearbeitet wird (Vorgabe [RemoteSettings] Parallel) |
| `--timeout` | erforderlich | Sekunden, die jede Gegenstelle für eine Phase brauchen darf (Vorgabe [RemoteSettings] Timeout) |
| `--retries` | erforderlich | Wie oft ein Verbindungsfehler erneut versucht wird (Vorgabe [RemoteSettings] Retries) |
| `--staged` |  | Stufe für Stufe ausliefern ([RemoteSettings] StageOrder[] nach Umgebung): die erste Stufe jetzt, vor jeder weiteren ein Tor (mit --apply) |
| `--continue` | erforderlich | Das Tor eines Deploys in Stufen freigeben und seine nächste Stufe ausliefern (die Deploy-ID; mit --apply) |
| `--rollback` | erforderlich | Jede Gegenstelle zurücknehmen, die diesen Deploy angewendet hat (die Deploy-ID; mit --apply) |
| `--login` | erforderlich | Als dieser Benutzer ausführen (Standard: [RunSettings] RunAsUser in migration.ini) |
| `--run-id` | erforderlich | Die Kennung des zu startenden Laufs (Standard eine neue: <Datum>-<Uhrzeit>-<6 Hex-Ziffern>); die Administration vergibt sie, bevor sie den Lauf startet |
| `--progress` | erforderlich | Den Fortschritt des Laufs an diese Datei anhängen, ein JSON-Objekt pro Zeile (was die Administration zeigt, während der Lauf vor sich geht) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationdeploy.php`, `kernel/private/classes/commands/expmigrationdeploy.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
