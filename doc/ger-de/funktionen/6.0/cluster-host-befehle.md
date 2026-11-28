# Befehle auf anderen Hosts und im ganzen Cluster: exp:host:run und exp:cluster:*

Diese Seite ist für Administratoren einer Installation, die auf mehr als einem Server läuft: zwei Webserver hinter
einem Load Balancer, ein Webserver und ein Velocity-Knoten oder ein Primär- und ein Bereitschaftsserver. Caches leeren
oder eine Änderung ausrollen hieß bisher, sich auf jedem Server anzumelden und dieselben Befehle von Hand auszuführen,
als der richtige Benutzer, mit dem richtigen PHP-Binary, in der richtigen Reihenfolge. Dabei gingen Schritte verloren:
die INI-Caches auf einem Knoten, die Valkey-Schlüssel, das Neuladen von PHP-FPM, der Neustart von Velocity. Diese
Befehle erledigen das von einem Server aus für jeden Host, mit PASS oder FAIL je Host.

| | |
|---|---|
| Befehle | `./console exp:host:run`, `exp:cluster:run`, `exp:cluster:cache`, `exp:cluster:deploy`, `exp:cluster:status`, `exp:cluster:hosts` (`bin/php/exphostrun.php`, `bin/php/expclusterrun.php`, `bin/php/expclustercache.php`, `bin/php/expclusterdeploy.php`, `bin/php/expclusterstatus.php`, `bin/php/expclusterhosts.php`) |
| Einstellungen | `settings/cluster.ini`: `[ClusterSettings]` und ein Block `[Host_<name>]` je Host, in `settings/override/cluster.ini.append.php` |
| Transport | das `ssh` des Systems mit `BatchMode=yes`, `-n -T`; `Ssh=local` läuft auf dieser Maschine mit `sh -c`. Kein Passwort und kein Schlüssel steht in den Einstellungen |
| Ausgabe | PASS oder FAIL je Host, Exit-Code 1, wenn ein Host fehlschlug, `--json` für Skripte, `--dry-run` überall |
| Protokoll | eine Zeile je Host in `var/log/cluster.log` und das Audit-Ereignis `system.cluster.run` ([Prüfprotokoll](pruefprotokoll.md)) |
| Code | `expClusterHosts`, `expClusterRemote`, `expClusterOperations` (`kernel/classes/cluster/`), die Befehle in `kernel/private/classes/commands/` |

Diese Befehle sind unabhängig vom DFS-Cluster-Dateihandler ([exp:cluster:enable](cluster-befehle.md)): Sie
funktionieren für jede Installation, die auf mehreren Hosts läuft, ob ihre Dateien in einem DFS-Share, auf NFS oder
auf der eigenen Platte jedes Servers liegen.

## Die Einstellungen

Kopieren Sie einen Block `[Host_<name>]` für jeden Host und führen Sie die Namen in `HostList[]` auf, in der
Reihenfolge, in der die Befehle sie besuchen. Der Server, auf dem Sie die Befehle ausführen, kann selbst einer der
Hosts sein, mit `Ssh=local`.

```ini
# settings/override/cluster.ini.append.php
[ClusterSettings]
HostList[]
HostList[]=cluster
HostList[]=web
DefaultPhp=/opt/plesk/php/8.5/bin/php
Mode=sequential

[Host_cluster]
Ssh=local
Root=/var/www/vhosts/example.com/doc/example.com
User=example
Engine=velocity

[Host_web]
Ssh=root@web.example.com
Root=/var/www/vhosts/example.com/doc/example.com
User=example
Engine=fpm
```

| Einstellung | Bedeutung |
|---|---|
| `[ClusterSettings] HostList[]` | die Hosts; ein Name besteht aus Buchstaben, Ziffern, Punkt, Bindestrich und Unterstrich |
| `DefaultPhp` | das PHP-Binary eines Hosts, dessen Block keines nennt |
| `ConnectTimeout` | Sekunden, die ssh auf die Antwort eines Hosts wartet (Standard 20) |
| `Timeout` | Sekunden, die ein Host für alles haben darf; ein Host, der länger braucht, wird gestoppt und gilt als fehlgeschlagen (Standard 1800) |
| `Mode` | `sequential` (Standard) oder `parallel`; `--parallel` und `--sequential` haben Vorrang |
| `AllowRootUser` | hängt `--allow-root-user` an jeden Konsolenbefehl, für Hosts mit root als Anmeldung (Standard enabled) |
| `SshOptions[]` | weitere `ssh -o`-Optionen für jeden Host, z. B. `StrictHostKeyChecking=accept-new` |
| `LogFile` | das Protokoll in `var/log` (Standard `cluster.log`) |
| `[Host_<name>] Ssh` | ein Alias aus `~/.ssh/config`, ein Hostname oder `user@host`; `local` läuft hier ohne ssh |
| `Port` | der ssh-Port; leer: der Standard von ssh oder der des Alias |
| `Root` | das Wurzelverzeichnis der Installation auf diesem Host (Pflicht) |
| `Php` | das PHP-Binary auf diesem Host; leer: `DefaultPhp` |
| `User` | als dieser Benutzer ausführen (`sudo -n -u <user> -H`); nehmen Sie den Benutzer der Website, damit keine Caches als root geschrieben werden |
| `DeployUser` | der Benutzer, als der `exp:cluster:deploy` läuft; leer: die ssh-Anmeldung, weil ein Deploy PHP-FPM neu lädt und Velocity neu startet |
| `Engine` | `fpm` oder `velocity`: wie der Host die Website ausliefert (siehe unten) |
| `DeployOptions[]` | Optionen, die `exp:cluster:deploy` für diesen Host an `exp:velocity deploy` anhängt |

Die Schlüssel kommen aus dem ssh-Agent oder der `~/.ssh/config` des Benutzers, der den Befehl ausführt. Ein Host, der
nach einem Passwort fragen würde, oder ein `sudo`, das eines verlangen würde, schlägt fehl, statt zu warten. Prüfen Sie
zuerst die Einstellungen und die Anmeldungen:

```bash
./console exp:cluster:hosts            # die Hosts und was an ihren Einstellungen falsch ist
./console exp:cluster:hosts --check    # meldet sich zusätzlich auf jedem an, prüft bin/php/console und das PHP-Binary
```

## exp:host:run und exp:cluster:run

```bash
./console exp:host:run web -- exp:cache status
./console exp:host:run web --shell -- git log -1 --oneline
./console exp:cluster:run -- exp:maintenance status
./console exp:cluster:run --hosts=web,cluster --parallel -- exp:ezcache --clear-tag=ini
./console exp:cluster:run --dry-run -- exp:velocity deploy
```

Die Wörter nach `--` sind ein Konsolenbefehl: Jeder Host führt `bin/php/console <Wörter>` in seinem `Root` aus, mit
seinem PHP-Binary, als sein `User` (sonst als die ssh-Anmeldung). `--shell` führt die Wörter stattdessen als
Befehlszeile für `sh` aus. `--as` wählt den Benutzer: `user` (Standard), `deploy` (`DeployUser`) oder `login`. Nichts
ist interaktiv.

## exp:cluster:cache clear

```bash
./console exp:cluster:cache clear                         # jeder Cache auf jedem Host
./console exp:cluster:cache clear --tag=template,content  # nur diese
./console exp:cluster:cache clear --id=template-block
./console exp:cluster:cache clear --full --parallel       # mit Neuladen von PHP-FPM und Neustart von Velocity
```

Auf jedem Host, in dieser Reihenfolge:

1. `exp:ezcache --clear-all` (oder `--clear-tag` / `--clear-id` mit `--tag` / `--id`; `--purge` entfernt die Dateien
   wirklich). Dabei laufen auch die Cache-Clear-Listener, die die Caches leeren, die Erweiterungen anderswo halten,
   etwa die Valkey-Schlüssel für INI und Ansichten, und der Template-Block-Cache.
2. Auf einem Host mit `Engine=velocity` der Antwort-Cache von Velocity (`exp:velocity cache clear --apply`).

`--full` führt stattdessen `exp:velocity deploy --all-caches --no-autoload` aus, das zusätzlich PHP-FPM neu lädt und
Velocity neu startet, bevor die Caches der gerenderten Ausgabe geleert werden, damit keine vom alten Code erzeugte
Seite erneut in den Cache gelangt.

## exp:cluster:deploy

```bash
./console exp:cluster:deploy                                # exp:velocity deploy auf jedem Host
./console exp:cluster:deploy --kernel --packer
./console exp:cluster:deploy --hosts=web --remote-dry-run   # jeder Host zeigt seine Schritte
```

Jeder Host führt `exp:velocity deploy` mit den hier angegebenen Optionen und seinen eigenen `DeployOptions[]` aus:
Autoloads, INI- und Template-Caches, PHP-FPM, Velocity und die Caches der gerenderten Ausgabe, in dieser Reihenfolge,
mit Abbruch beim ersten Fehler. Ein Host mit `Engine=fpm` bekommt `--no-velocity`. Der Code muss schon auf den Hosts
liegen (git pull, rsync); dieser Befehl kopiert keine Dateien. `--dry-run` zeigt den ssh-Befehl für jeden Host;
`--remote-dry-run` fragt jeden Host nach seinem Deploy-Plan.

## exp:cluster:status

```bash
./console exp:cluster:status
./console exp:cluster:status --parallel --json
```

Für jeden Host: die Version, die PHP-Version, der git-Stand mit Branch und Commit-Datum, ob der Wartungsmodus an ist,
und auf einem Velocity-Host, ob Velocity antwortet. Der Befehl liest nur und warnt, wenn die Hosts nicht auf demselben
git-Stand sind.

## Optionen aller Befehle

| Option | Bedeutung |
|---|---|
| `--hosts=a,b` / `--nodes=a,b` | nur diese Hosts aus `HostList[]` |
| `--all` | jeder Host (Standard) |
| `--parallel` / `--sequential` | alle Hosts gleichzeitig oder einer nach dem anderen |
| `--timeout=<s>` | Sekunden, die ein Host haben darf |
| `--dry-run` | zeigt den Befehl, den jeder Host ausführen würde, führt nichts aus, protokolliert nichts |
| `--json` | ein JSON-Objekt mit Ergebnis, Ausgabe und Dauer je Host |

Exit-Code: 0 jeder Host bestand; 1 ein Host schlug fehl (die anderen liefen trotzdem); 2 falsche Optionen oder
Einstellungen.

## Getestet

`tests/tests/kernel/classes/cluster/expClusterHostCommandsTest.php`: 20 Tests ohne Datenbank, mit einem simulierten
ssh-Transport für die Befehlszeilen, das Quoting, die Benutzer, Zeitlimits, parallele Läufe und den JSON-Bericht,
und echten lokalen `sh`-Läufen. Live auf einer Installation mit zwei Knoten (ein Velocity-Knoten mit `Ssh=local`, ein
FPM-Knoten über ssh, der Benutzer der Website): `exp:cluster:hosts --check`, `exp:cluster:status`,
`exp:cluster:deploy --remote-dry-run --parallel` und `exp:cluster:cache clear --id=template-block` bestanden auf
beiden Hosts.

## Siehe auch

- [Clustering in Exponential 6.0](clustering-ueberblick.md)
- [Ein DFS-Cluster mit einem Knoten in einem Befehl](cluster-befehle.md)
- [Alle Konsolenbefehle](../../beispiele/befehle/README.md), [cluster.ini](../../beispiele/ini-einstellungen/cluster/README.md)
