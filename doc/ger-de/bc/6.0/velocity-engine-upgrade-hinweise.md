# Hinweise zum Upgrade der Velocity-Engine (0.0.4.27 bis 0.0.4.42)

Lesen Sie diese Seite, bevor Sie die Webserver-Engine Velocity aktualisieren. Sie führt die Verhaltensänderungen auf,
die eine Installation betreffen können, in der Reihenfolge, in der Sie ihnen beim Upgrade von einem älteren Release
begegnen. Jeder Abschnitt sagt, wer betroffen ist, wie Sie es prüfen und wie Sie es beheben. Wie Sie eine Engine aus
Exponential heraus auswählen und betreiben, beschreibt [Velocity-Engines](velocity-engines.md); den Verzeichnisbaum
auf der Festplatte [Die Verzeichnisstruktur von Velocity](velocity-verzeichnisstruktur.md); das Engine-Archiv
[phar](phar.md); was Exponential selbst geändert hat,
[Verhaltensänderungen vom Oktober 2026](verhaltensaenderungen-2026-10.md). Die Release Notes stehen
im [Changelog](../../../changelogs/extensions/exponential-velocity.md), die Geschichte in der
[Velocity-Chronik](../../geschichte/velocity/README.md).

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Sicherheit des Panels (0.0.4.28), Zygote-Worker (0.0.4.29), Worker nicht mehr als root (0.0.4.34), Antwort-Cache aus, sofern nicht eingeschaltet (0.0.4.39), neue Programmpfade (0.0.4.41), `REQUEST_TIME` pro Anfrage (0.0.4.42) und kleinere Änderungen. |
| Wer betroffen ist | Alle, die Velocity betreiben; jeder Abschnitt sagt, wer genau. |
| Wie Sie es prüfen | `php sbin/qbixserver.php --version`, dann die Befehle unter „Nach dem Upgrade prüfen“. |
| Wie Sie es beheben | Lesen Sie jeden Abschnitt ab Ihrem Release aufwärts. „Wenn Sie das alte Verhalten brauchen“ führt die Schalter auf. |

## Vor dem Upgrade

1. **Ermitteln Sie Ihr Release.** `php sbin/qbixserver.php --version` (oder `php qbixserver.php --version` vor
   0.0.4.41) gibt das Release und den Build aus. Ein Checkout meldet das aus Git gelesene Release.
2. **Lesen Sie die Abschnitte unten ab Ihrem Release aufwärts.**
3. **Testen Sie die Konfiguration vor dem Neustart.** Führen Sie nach der Installation der neuen Engine
   `php sbin/qbixserver.php -t --config=<your site file>` aus; was geladen würde, listen Sie mit `--layout` auf.
4. **Starten Sie neu.** Ab 0.0.4.40 bringt `qbixctl restart` einen Server mit den Optionen zurück, mit denen er
   gestartet wurde; aus Exponential heraus verwenden Sie `./bin/php/console exp:velocity restart --allow-root-user`.
   Führen Sie dann `qbixctl panel:check` aus (das Panel kann gesperrt sein, siehe 0.0.4.28) und `qbixctl ext:check`.
5. **Überspringen Sie fehlende Versionen.** Ein Tag ist dauerhaft: Ist ein Release fehlerhaft, ist die Korrektur das
   nächste Release. Die Versionen `v0.0.4.21`, `v0.0.4.22` und `v0.0.4.26` haben keine Release-Seite; gehen Sie auf
   die nächste Version darüber.

## Auf 0.0.4.28

Betroffen: alle, die das Control Panel betreiben, das Zugriffsprotokoll lesen oder die Betriebssystempakete
verwenden.

- **Die Zugangsdaten und Sitzungen des Panels sind umgezogen und werden nur vertraut, wenn niemand anderes sie ändern kann.** Mit einem Konfigurationsbaum liegen sie in `<conf>/acl/panel.json` und `<state>/sessions/`; ohne einen in `local/` neben der Anwendung. Ein vorhandenes Passwort wird beim ersten Start übernommen (die alte Datei bleibt erhalten, umbenannt in `panel.json.migrated-<time>`, mit einer Kopie `panel.json.pre-migration-<time>`). Jedes Verzeichnis vom Speicherort bis hinauf zu `/` muss root oder dem Benutzer des Servers gehören und darf für Gruppe und andere nicht beschreibbar sein; sonst ist **das Panel gesperrt** (Anmeldung, Standardschlüssel und jede Sitzung werden abgelehnt). Prüfen: `qbixctl panel:check --root=...`. Übliche Lösung nach einem Upgrade (ein Anwendungsverzeichnis, das durch eine umask von `002` als `0775` hinterlassen wurde): `chmod g-w,o-w <dir>`, für das Verzeichnis, das die Ablehnung nennt. Oder setzen Sie `Q.panel.aclDir` und `Q.panel.sessionsDir` (oder starten Sie mit `--conf-dir`), um die Dateien an anderer Stelle abzulegen.
- **Das Panel startet mit einem Standardschlüssel, der bei der ersten Anmeldung geändert werden muss, und Passwörter werden nach Regeln geprüft und mit bcrypt gespeichert.** Ein vorhandenes Passwort funktioniert weiter. Setzen Sie auf jedem von außen erreichbaren Server sofort eines: `qbixctl panel:password --root=... --generate`. Siehe [Control Panel](../../funktionen/6.0/velocity-control-panel.md).
- **Das Standardformat des Zugriffsprotokolls ist `vhost`**: das Format `qbix` plus der Host in Anführungszeichen am Ende jeder Zeile. Ein Log-Leser, der am Zeilenende verankert ist (fail2ban, ein eigenes Format von GoAccess), muss angepasst werden, oder Sie behalten die alten Zeilen: `{"Q":{"webserver":{"log":{"format":"qbix"}}}}`.
- **Aufgeführte Skripte, Front-Controller und statische Pfade sind optional.** Nichts ändert sich, bis Sie `Q.webserver.scripts`, `Q.webserver.frontControllers` oder `Q.web.static.paths` setzen. Wenn Sie es tun, laufen nur aufgeführte Skripte über ihren Namen, und nur passende Dateien werden unverändert gesendet; der Antwort-Cache beginnt eine neue Generation. Verankern Sie die Muster der statischen Pfade und nennen Sie Skripte exakt.
- **Die Ereignisschleife `iopoll` ist optional.** `auto` wählt Revolt, wenn es installiert ist, sonst wie bisher `stream_select`.
- **Das Betriebssystempaket wurde in `exponential-velocity` umbenannt** (deb und rpm; Unit `exponential-velocity.service`; `/usr/share/exponential-velocity`, `/var/lib/exponential-velocity`, `/etc/default/exponential-velocity`). Die Installation über `qbix-webserver` aktualisiert an Ort und Stelle (Replaces/Obsoletes, Einstellungen und Zustand werden übernommen, der Dienst wird wieder aktiviert und neu gestartet, `/usr/share/qbix-webserver` bleibt als symbolischer Link). Wo apt den alten Dienst vor dem Entpacken entfernt hat, führen Sie `sudo systemctl enable --now exponential-velocity` aus. Das Docker-Image ist `ghcr.io/se7enxweb/exponential-velocity`; der alte Image-Name ist veraltet und erhält keine neuen Tags. Siehe [Pakete](../../funktionen/6.0/velocity-pakete-und-binaerdateien.md).
- **Der Standard-Branch ist `main`** (war `maintain`); das Composer-Paket ist `se7enxweb/exponential-velocity`.
- Ein vergessenes Passwort des Panels: `qbixctl panel:password --root=...`; ein gesperrtes Panel: siehe oben.

## Auf 0.0.4.29

- **Pool-Worker werden standardmäßig von der Zygote geforkt** (`Q.webserver.zygote`, aus Exponential `[ServerSettings] Zygote=enabled`). Setzen Sie es auf `false`, um wie bisher vom Server zu forken. Ohne `ext-sockets` fällt der Pool von selbst zurück. **Unter PHP 8.2 und 8.3 beantwortet das Release 0.0.4.29 selbst nur die erste Anfrage jedes Workers, der nach dem Start geforkt wurde:** Gehen Sie auf 0.0.4.30 oder neuer (oder setzen Sie `zygote` auf `false`).
- **Die Meldung einer nicht abgefangenen Ausnahme wird nicht mehr an den Client gesendet.** Ohne `Q.webserver.debug` ist die Antwort der eigene Exception-Handler der Anwendung (`set_exception_handler()`), sonst die gestaltete Seite `500`. Meldung, Klasse, Datei, Zeile und Trace gehen in das Fehlerprotokoll und nur mit `Q.webserver.debug` oder `--debug` in die Antwort. Alles, was die Meldung aus dem Body einer `500` gelesen hat, braucht eingeschaltetes Debug.
- **APCu wird nur verwendet, wenn es Einträge halten kann**: Unter der CLI bedeutet das `apc.enable_cli=1`. Ist es aus, meldet der Cache das beim Start, statt von der Festplatte zu laufen, während seine Einstellungen Arbeitsspeicher angaben.
- **Gecachte Seiten werden unter der Kodierung abgelegt, in der sie gespeichert sind**: Brotli- und gzip-Clients teilen sich einen Eintrag, daher wird die erste Anfrage für jede Seite nach dem Upgrade erneut gerendert.

## Auf 0.0.4.31

- Sitzungen werden dort gehalten, wo `session.save_path` es angibt, in der PHP-Form `N;MODE;/path`, und mit dessen Modus angelegt (bisher: das temporäre Verzeichnis, Modus 0644, wenn mit `-d` gesetzt). Wenn Sie sich auf Sitzungen in `/tmp` verlassen haben, setzen Sie `session.save_path` ausdrücklich.
- HTTP/2-Anfragen setzen jetzt `HTTPS` (die absoluten URLs einer Anwendung und ihre Prüfung auf sichere Anfragen waren bisher falsch; ein nach Schema geschlüsselter Seiten-Cache legte HTTP/2-Seiten unter `http://` ab: Leeren Sie einen solchen Cache einmal).

## Auf 0.0.4.33

- Eine PHP-Datei, die mit `file_get_contents()`, `md5_file()` oder `fopen()` gelesen wird, liefert die eigenen Bytes der Datei. Unter 0.0.4.32 und älter führte die Upgrade-Prüfung von Exponential (Setup, System-Upgrade, Dateikonsistenz) etwa 330 Dateien von Kernel und Bibliotheken als geändert auf, wenn Velocity sie auslieferte. Führen Sie die Prüfung nach dem Upgrade erneut aus ([Prüfung der Dateikonsistenz](../../funktionen/6.0/dateikonsistenzpruefung.md)).

## Auf 0.0.4.34

- **Worker laufen nicht mehr als root.** Als root gestartet, behält der Master root; Worker, die Zygote, Kindprozesse bei Fork-per-Request und geplante Aufgaben wechseln zu Benutzer und Gruppe des Document Root (oder `--user`, `--group`, `Q.webserver.user`/`group`, `VC_RUN_USER`/`VC_RUN_GROUP`). Die Cache-Verzeichnisse (`Q.web.cache.dir`, `Q.web.appCache.dir`, `Q.webserver.precompress.dir`, `Q.webserver.writable`) werden beim Start diesem Benutzer übergeben. Prüfen: die Startzeile `Workers as: user:group (...)`. Gehören Dateien unter Ihrer Website aus früheren Läufen root (Bildvarianten, Caches), ändern Sie einmal ihren Besitzer: `chown -R <user>:<group> <dir>`. Ein Benutzer oder eine Gruppe, die nicht existiert, verhindert den Start; root wird abgelehnt, außer mit `--allow-root-workers`.
- `Q.web.cache.pauseFile` pausiert beide Antwort-Caches, solange eine Datei existiert (Wartungsfenster).

## Auf 0.0.4.35

- Von selbst ändert sich nichts. Neu und optional: `Q.webserver.headers`, `headersOnScripts`, `hsts` ([Sicherheits-Header](../../spezifikationen/6.0/velocity-http2-und-sicherheit.md#header-auf-jeder-antwort)).
- Das Preset `exponential` bringt jetzt eigene Listen von Skripten, Front-Controllern und statischen Pfaden mit, wenn keine konfiguriert sind: Damit laufen nur Einstiegspunkte, und nur aufgeführte Dateien werden ausgeliefert. Engere Listen, die Sie setzen, werden nie erweitert; haben Sie bisher andere Dateien über das Preset ausgeliefert, führen Sie sie auf.
- `header('WWW-Authenticate: ...')` antwortet mit `401`, wie in PHP.

## Auf 0.0.4.38

- Anfragen über `post_max_size` erhalten über HTTP/1.1 und HTTP/2 ein sauberes `413` (HTTP/2 antwortete ab 8 MB mit `502`). Prüfen Sie, dass `post_max_size` und `upload_max_filesize` zu Ihrem größten Upload passen.
- `post_max_size = 0` bedeutet jetzt kein Limit, wie in PHP (bisher wurde damit jede Anfrage mit Body abgelehnt).

## Auf 0.0.4.39

- **Der Antwort-Cache ist aus, sofern keine Einstellung ihn einschaltet.** Bisher war der eingebaute Standard eingeschaltet, obwohl die Dokumentation aus angab. Eine Installation, die irgendwo `enabled: true` angegeben hat (ein Cache-Modul, die Site-Datei), cacht genau wie bisher. **Eine Installation, die sich auf den alten Standard verlassen hat, hört auf zu cachen:** Fügen Sie `{"Q":{"web":{"cache":{"enabled":true}}}}` zur Site-Datei hinzu. Ein Modul, das nur `dir` oder `defaultTtl` setzt, schaltet den Cache nicht mehr von selbst ein. Die Site-Datei hat Vorrang vor einem Modul; eine im Panel gespeicherte Einstellung hat Vorrang vor beiden. `qbixctl dismod cache` schaltet den Cache jetzt wirklich aus.
- Prüfen: `curl -sI https://your-host/ | grep -i x-cache` nach zwei Anfragen: Kein `X-Cache: HIT` bedeutet aus.

## Auf 0.0.4.40

- `qbixctl restart` startet mit den Optionen neu, mit denen der Server gestartet wurde (ein Datensatz `<pid file>.json` neben der PID-Datei). Ein von Hand ohne PID-Datei gestarteter Server wird anhand seines Eintrags in der Prozesstabelle neu gestartet.
- Das Standard-Serverprotokoll ist nicht mehr `/tmp/qbixserver.log`. Es geht nach `Q.webserver.log.dir`, sonst nach `var/log`, `files/log` oder `logs` des Document Root, sonst in das Log-Verzeichnis des Konfigurationsbaums (`/var/log/qbix`, `/var/log/vc` für Velocity). Ein von einer früheren Version hinterlassenes `/tmp/qbixserver.log` können Sie von Hand entfernen.
- Die Servertests verdecken die Standardeinstellung des Caches nicht mehr; für eine Installation ändert sich nichts.

## Auf 0.0.4.41

Es muss sich nichts ändern. Der Daemon und die Administrationsbefehle liegen in `sbin/`, die Shell in `bin/`; jeder frühere Pfad ist eine Weiterleitung, die die neue Datei im selben Prozess ausführt, mit derselben Kommandozeile, Prozess-ID und demselben Titel, sodass `ps`, `pkill -f 'qbixserver.php.*--port=N'` und die Überwachung weiter passen.

| | 0.0.4.40 und älter | 0.0.4.41 und neuer |
|---|---|---|
| Server | `qbixserver.php` | `sbin/qbixserver.php` (der alte Pfad leitet weiter) |
| Steuerung | `qbixctl.php` | `sbin/qbixctl.php` |
| Konsole | `qbixconsole.php` | `sbin/qbixconsole.php` |
| Shell | `qshell.php` | `bin/qshell.php` |
| Archiv | `bin/qbixserver.phar` | `sbin/qbixserver.phar` (`bin/qbixserver.phar` ist dieselbe Datei) |
| uwebserver | `bin/uwebserver` | `sbin/uwebserver` (C-Quellen in `native/uwebserver/`) |
| Pakete | Programme in `/usr/bin` | Programme in `/usr/sbin`, Links in `/usr/bin` |

- Eine Weiterleitung gibt nur dann einen Hinweis auf der Standardfehlerausgabe aus, wenn diese ein Terminal ist; `QBIX_MOVED_QUIET=1` unterdrückt ihn.
- `[ServerSettings] ScriptPath` von Exponential behält seinen ausgelieferten Wert (die Weiterleitung); `.../sbin/qbixserver.php` funktioniert ebenfalls. `exp:velocity` entscheidet danach, welche Dateien existieren ([Velocity-Engines](velocity-engines.md)).
- Ein Dienst, der vor dem Upgrade des Pakets gestartet wurde, wird vom Upgrade unter der neuen Unit neu gestartet.

## Auf 0.0.4.42

- Nichts in einer Website startet `uwebserver`; die Engine, die Pakete, das Image, die Release-Workflows und die Exponential-Installation führen ihn nie aus. Wenn **Sie** ihn ausführen:
  - Er lauscht standardmäßig auf `127.0.0.1:8000` (bisher auf allen Schnittstellen auf 8080); die alte Adresse behalten Sie mit `--listen='*:8080'`;
  - als root braucht er `--user` oder `--allow-root`;
  - eine unbekannte Option startet nichts und beendet mit 2; `--cert` ohne `--key` ist ein Fehler;
  - der Header `Server` ist `uwebserver`; die Startzeile geht auf die Standardfehlerausgabe.
- Die Pakete installieren die Shell als `/usr/bin/vc-qshell` (0.0.4.41 installierte sie nicht als Befehl).
- `REQUEST_TIME` und `REQUEST_TIME_FLOAT` gelten in persistenten Workern pro Anfrage. Eine Anwendung, die darauf gestützt cachte (Exponential tut das), liefert die Daten einer Anfrage nicht mehr an die nächste aus. Kernel-Caches pro Anfrage brauchen dieses Release.

## Templates der Installationsprogramme (HTML5)

Nicht Teil der Engine, aber im selben Zeitraum: Die Templates des Site-Pakets `ezwebin` (Repository `ezwebin-ezpackage`, Juli 2026) geben keine selbstschließenden XHTML-Schrägstriche und keine veralteten `type`-Attribute mehr aus (`<input ... />` wird zu `<input ...>`, `<script type="text/javascript">` wird zu `<script>`). Browser sind nicht betroffen. Prüfen Sie angepasste Websites auf Stylesheets, Skripte oder Tests, die das alte Markup exakt abgleichen. Das Installationsprogramm des Legacy-Kernels (`exponential-legacy-installer` 2.2.3) behebt Fehler von `composer update` mit Composer 2.10, wenn das Legacy-Wurzelverzeichnis `.` ist; aktualisieren Sie es, wenn der Kopierschritt fehlschlägt. Geschichte: [Installationsprogramme und Pakete](../../geschichte/velocity/installer-und-pakete.md).

## Wenn Sie das alte Verhalten brauchen

| Verhalten | So erhalten Sie es zurück |
|---|---|
| Worker forken vom Server | `Q.webserver.zygote` `false` |
| Alte Zeilen im Zugriffsprotokoll | `{"Q":{"webserver":{"log":{"format":"qbix"}}}}` |
| Symbolischen Links aus dem Document Root heraus folgen | `Q.webserver.followSymlinks` `true` (nicht empfohlen) |
| Cache eingeschaltet, ohne es anzugeben | Setzen Sie `enabled: true` ausdrücklich |
| Worker als root | `--allow-root-workers` (nicht empfohlen) |
| uwebserver auf allen Schnittstellen | `--listen='*:8080'` |

## Nach dem Upgrade prüfen

```bash
php sbin/qbixserver.php --version
php sbin/qbixserver.php -t --config=/etc/qbix/sites-enabled/default.conf
qbixctl status
qbixctl panel:check --root=/path/to/web
qbixctl ext:check
curl -s https://your-host/Q/health
```

## Verwandte Seiten

- [Velocity-Engines](velocity-engines.md) und [Die Verzeichnisstruktur von Velocity](velocity-verzeichnisstruktur.md)
- [Velocity: Exponential in einem Webserver mit persistenten Workern betreiben](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)
- [Einstellungen und Programme der Velocity-Engine](../../spezifikationen/6.0/velocity-engine-einstellungen.md)
- [Velocity-Chronik: September 2026, 25. bis 30. September](../../geschichte/velocity/2026-09e.md)
