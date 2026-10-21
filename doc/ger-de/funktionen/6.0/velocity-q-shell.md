# Velocity-Q-Shell

Diese Seite richtet sich an Administratoren, die einen Velocity-Server verwalten und lieber tippen als klicken. Die
Q-Shell ist eine ausklappbare Konsole in jeder Ansicht des Servers (`/Q/dashboard`, `/Q/panel`, Dokumentation, PHP
Info, Fehlerseiten). Ihre Befehle verwalten den Server: Status, Worker, Cache, Sites, Module, Zertifikate, Logs. Sie
spricht eine zsh-ähnliche Sprache mit einem echten Parser (nichts wird als String an eine Shell übergeben). Gilt für
Exponential Velocity 0.0.4.28 und neuer (Befehl `vc-qshell` ab 0.0.4.42).

## Öffnen und einen Befehl ausführen

1. Melden Sie sich am [Control Panel](velocity-control-panel.md) an. Die Shell benötigt eine angemeldete
   Panel-Session; sie ist standardmäßig eingeschaltet.
2. Drücken Sie die Backtick-Taste (oder `~`) oder klicken Sie in der Werkzeugleiste auf **Shell**.
3. Geben Sie einen Befehl ein:

   ```
   qsh> uptime
   qsh> workers -o pid,state,mem -H | sort -k3
   qsh> get Q.shell.timeout
   qsh> for n in 2 4 8; do workers resize $n -f; sleep 5; health; done
   qsh> man jobs
   ```

Wofür sie gut ist:

- Den Server aus dem Browser verwalten, ohne Anmeldung an der Maschine: `server status`, `cache clear`,
  `site enable`, `ssl`, `conf`, `mod`, `logs`, `workers`, `ext`.
- Skripten: Schleifen, Pipes, Bedingungen, Aliase, Skripte aus einem Verzeichnis mit `source` einlesen.
- In einer Exponential-Installation (ein Document Root, der ein Exponential-Verzeichnisbaum ist) fügt die Shell die
  eigenen Werkzeuge der Installation als `exp`-Befehle hinzu (Release 0.0.4.28).
- Sie ist von Grund auf sicher gebaut: Stufen, Bestätigung, erneute Passworteingabe für gefährliche Befehle, ein
  Audit-Log.

## Am Terminal verwenden

`php bin/qshell.php` startet dieselbe Shell ohne Server; `php bin/qshell.php -c 'health'` führt eine Zeile aus.

Die deb- und rpm-Pakete installieren sie als `/usr/bin/vc-qshell` (das Container-Image als
`/usr/local/bin/vc-qshell`), einen Link in den installierten Baum, sodass dies aus jedem Verzeichnis für jeden
Benutzer funktioniert:

```bash
vc-qshell -c 'health'
```

Der Name ist nicht `qshell`, weil `/usr/bin/qshell` zu einem anderen Programm gehört. Vor 0.0.4.42 installierten die
Pakete die Shell überhaupt nicht als Befehl.

Die Shell funktioniert aus dem Phar, den Paketen, dem Container-Image und den statischen Binärdateien (seit 0.0.4.28;
davor antwortete sie, ihr Runner fehle, als `429`). Ein fehlender Runner antwortet jetzt mit `503` und dem Grund.

## Das Terminal

- **Fenstersteuerung**: `+` startet oder zeigt die Shell; `-` blendet sie aus (Session und Jobs laufen weiter; `Esc`
  tut dasselbe); `m` maximiert; `x` schließt sie und beendet nach Rückfrage die Session und ihre Jobs.
- **Tabs und Teilungen**: `Ctrl-Shift-T` öffnet einen Tab, `Ctrl-Shift-D` teilt; jeder Bereich ist eine eigene
  Session mit eigenen Jobs.
- **Zeilenbearbeitung**: Emacs-Tasten (`Ctrl-A/E/K/U/W`, `Alt-B/F`), Pfeiltasten für den Verlauf, `Ctrl-R`
  Rückwärtssuche, `Tab` Vervollständigung, `Ctrl-C` Abbruch, `Ctrl-L` Leeren.
- **Themes**: `theme list`, `theme quake`; Themes sind JSON-Dateien im Verzeichnis `shell/` eines Designs.
- **Telefone**: Softkeys für Tab, Ctrl, Pfeiltasten und `|`.
- **Transport**: ein WebSocket unter `/Q/ws/shell`, mit HTTP-Polling als Rückfallebene.
- **Einstellungen**: `get` zeigt jede Einstellung mit ihrer Quelle; `set workers.count 8` gilt für den laufenden
  Server; `set -p workers.count 8` schreibt sie außerdem in die Konfigurationsdatei (eine `.bak` wird behalten). Die
  eigenen Sicherheitseinstellungen der Shell lassen sich nicht aus der Shell ändern.

## Die Sprache

| Merkmal | Syntax |
|---|---|
| Listen | `a; b`, `a && b`, `a \|\| b`, `a \| b`, `a &` |
| Quoting | `'literal'`, `"with $vars"`, `\x` |
| Expansion | `$x`, `${x:-default}`, `$(command)`, `$((1 + 2))`, `a{1,2,3}` |
| Steuerung | `if ... then ... elif ... else ... fi`, `for`, `while`, `until`, `[ ... ]` |
| Verlauf | `!!`, `!n`, `!-n`, `!prefix`, `^old^new`; `history`, `history N`, `history -a`, `history -c` |
| Skripte | `source name`, `exec name` aus dem Verzeichnis der Shell oder `Q.shell.scriptsDir` |

Schleifen stoppen nach 10.000 Durchläufen. Auflistungen folgen den Konventionen von zfs: `-o a,b,c` Spalten, `-H` ohne
Kopfzeile und tabulatorgetrennt, `-p` exakte Zahlen, `-j` JSON. Jeder Befehl beantwortet `--help`; `man <command>`,
`man <topic>` und `man -k <word>` decken den Rest ab.

## Jobs

`command &` läuft im Hintergrund; `jobs`, `fg %n`, `bg %n`, `kill %n`, `wait` verwalten sie. Ein Job läuft weiter,
während die Konsole ausgeblendet ist.

## Verlauf

Standardmäßig 100.000 Einträge in `<state dir>/shell/history` (erweitertes zsh-Format), unter einer Sperre angehängt,
sodass mehrere Sessions nie eine Zeile verlieren. Die Konsole lädt die neuesten 1.000; `Up` über den ältesten hinaus
holt die vorherigen 1.000; `Ctrl-R` durchsucht die geladenen Einträge, dann die ganze Datei auf dem Server. Gemessen
mit 150.000 Einträgen: neueste Seite etwa 1,5 ms, eine Suche zurück bis zum ältesten Eintrag etwa 30 ms.

## Stufen und Einstellungen

| Stufe | Darf ausführen |
|---|---|
| `basic` | nur lesend: Status, Health, Logs, `get` |
| `expanded` | zusätzlich Änderungen am laufenden Server: Cache, Worker, Sites, `set` |
| `advanced` | zusätzlich Serversteuerung und, falls erlaubt, Betriebssystembefehle |

| Konfigurationsschlüssel | Standard | Bedeutung |
|---|---|---|
| `Q.shell.enabled` | `true` | Die Konsole und die API |
| `Q.shell.tier` | `advanced` | Höchste Stufe, die eine Session verwenden darf |
| `Q.shell.allowSystem` | `false` | Rohe Betriebssystembefehle (`! command`, `sys command`) |
| `Q.shell.user` | Eigentümer des Document Root | Als wer Befehle laufen |
| `Q.shell.allowRoot` | `false` | `sudo <command>` als root |
| `Q.shell.elevateMinutes` | `5` | Wie lange eine Passwortprüfung gilt |
| `Q.shell.timeout` | `120` | Sekunden pro Befehl (TERM, dann KILL) |
| `Q.shell.maxOutput` | `8388608` | Bytes Ausgabe pro Befehl |
| `Q.shell.historySize` | `100000` | Behaltene Verlaufseinträge; `0` behält alle |
| `Q.shell.maxJobs` | `8` | Jobs pro Session |
| `Q.shell.scriptsDir` | keines | Verzeichnis mit Skripten, die die Shell ausführen kann |
| `Q.shell.toggleKey` | Backtick | Die Taste, die die Konsole öffnet |
| `Q.shell.allowedOrigins` | `[]` | Weitere Origins, die den WebSocket öffnen dürfen (die öffentliche URL eines Proxys) |

## Sicherheit

- **Bestätigung.** Befehle, die den laufenden Server ändern, fragen `proceed? [y/N]`; `-f` irgendwo in der Zeile
  beantwortet das mit Ja. In Skripten und über die API ist `-f` erforderlich.
- **Betriebssystembefehle sind ausgeschaltet**, sofern `Q.shell.allowSystem` nicht `true` ist, und nur in der Stufe
  advanced möglich.
- **Gefährliche Befehle fragen erneut nach dem Panel-Passwort**, wie es `sudo` tut: `rm`, `dd`, `mkfs`, `shred`,
  `shutdown`, `kill`, `chmod -R`, ein in eine Shell weitergeleiteter Download, Schreiben in `/etc` und weitere. Das
  Stoppen oder Neustarten des Servers und das Ändern des Panel-Passworts fragen ebenso nach. Falsche Antworten zählen
  für die Sperre des Panels.
- **Nie root für Ihren Code.** Jede Befehlszeile läuft in einem eigenen Prozess als `Q.shell.user`; sie verweigert
  die Ausführung, wenn dieser Benutzer fehlt oder root ist. Die eigenen Konsolenbefehle des Servers (`server`, `ssl`,
  `conf`, `site`, `mod`, `cache clear`, `panel`, `ext`, `logs tail`) führt der Server selbst aus, serverseitig erneut
  gegen Stufe und Passwortstatus geprüft.
- **Nichts vom Server.** Ein Befehl erhält keine der offenen Sockets oder Dateien des Servers und nur Pfad, Locale und
  die Variablen `QBIX_*`/`VC_*` (Release 0.0.4.28 behob ein Leck von Sockets und Umgebung).
- **Nur dieselbe Origin** für den WebSocket. Hinter einem Proxy tragen Sie die öffentliche Origin in
  `Q.shell.allowedOrigins` ein.
- **Audit.** Jeder Befehl (wer, von wo, Stufe, Exit-Status, Dauer) wird an `shell-audit.log` neben den Panel-Daten
  angehängt; das Dashboard hat eine Karte **Shell activity**.

## REST-API

Unter `/Q/api/shell/`, authentifiziert mit dem Session-Token des Panels in `Authorization: Bearer <token>` oder
`X-Panel-Token` (das Cookie allein wird abgelehnt):

| Methode und Pfad | Wirkung |
|---|---|
| `GET session` | wer, Stufe, Benutzer, ob Betriebssystembefehle und sudo erlaubt sind |
| `DELETE session?session=<name>` | die Session und ihre Jobs beenden |
| `POST exec` | `{"command": "...", "force": false, "timeout": 30}` liefert `202` mit einer Job-ID |
| `GET poll?since=<seq>` | Meldungen seit einer Sequenznummer |
| `POST input`, `POST signal` | eine Eingabe oder ein Signal an einen Job senden |
| `GET jobs`, `GET jobs/<id>`, `DELETE jobs/<id>` | auflisten, lesen, stoppen |
| `POST elevate` | die Passwortprüfung vor einem gefährlichen Befehl |
| `GET complete?line=...`, `GET history`, `GET history/search`, `GET audit` | Vervollständigung, Verlaufsseiten, Suche, Audit |

```bash
curl -s -H "Authorization: Bearer $TOKEN" -d '{"command":"uptime"}' https://host/Q/api/shell/exec
```

## Grenzen

- Benötigt eine angemeldete Panel-Session; abgemeldet ist der Eintrag in der Werkzeugleiste deaktiviert.
- Die Befehlsprüfung liest die Zeile als Text. Sie verhindert gewohnheitsmäßige Fehler; sie ist keine Sandbox. Lassen
  Sie `Q.shell.allowSystem` ausgeschaltet, sofern Sie es nicht brauchen.
- `server reload` und `server restart` nehmen die Session mit dem Server mit; die Shell verbindet sich neu.

## Verwandte Seiten

- [Control Panel und Dashboard](velocity-control-panel.md) (die Anmeldung, die die Shell braucht), [Pakete und Binärdateien](velocity-pakete-und-binaerdateien.md) (`vc-qshell`), [Velocity-Webserver](velocity-webserver.md)
- Spezifikationen: [Engine-Einstellungen](../../spezifikationen/6.0/velocity-engine-einstellungen.md) (Programme, `qbixconsole`), [HTTP/2 und Sicherheit](../../spezifikationen/6.0/velocity-http2-und-sicherheit.md) (die Administrationsfläche)
- Aktualisierung: [Hinweise zur Aktualisierung der Velocity-Engine](../../../eng-us/bc/6.0/velocity-engine-upgrade-notes.md)
- [Changelog: Exponential-Velocity-Engine](../../../changelogs/extensions/exponential-velocity.md)
- Chronik: [24. September](../../../eng-us/history/velocity/2026-09d.md), [25. bis 30. September](../../../eng-us/history/velocity/2026-09e.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
