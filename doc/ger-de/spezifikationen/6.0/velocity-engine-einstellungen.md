# Spezifikation: Einstellungen und Programme der Velocity-Engine

Diese Seite ist die Referenz für die eigenen Einstellungen der Velocity-Engine (JSON unter dem Schlüssel `Q`), für die Stellen, aus denen sie gelesen
werden, die Programme, die die Engine mitbringt, die Protokollierung, das Branding, die Event-Loop, die Wiederaufnahme von TLS-Sessions und den Vorwärm-Cache
beim Start. Lesen Sie sie, wenn Sie Velocity außerhalb der `settings/velocity.ini` von Exponential konfigurieren, seine Protokolle lesen
oder den Standardwert eines Engine-Schlüssels brauchen. Sie gilt für Exponential Velocity 0.0.4.42.

Aus Exponential heraus wird die Engine normalerweise über `settings/velocity.ini` konfiguriert; siehe
[Velocity-Engines](../../../eng-us/bc/6.0/velocity-engines.md). Die Einstellungen größerer Teilsysteme haben eigene Seiten:
[Antwort-Cache](../../../eng-us/features/6.0/velocity-response-cache.md), [HTTPS](../../../eng-us/features/6.0/velocity-https-certificates.md),
[Control Panel](../../../eng-us/features/6.0/velocity-control-panel.md), [Q-Shell](../../../eng-us/features/6.0/velocity-q-shell.md),
[Worker-Pool](velocity-worker-pool.md), [Header und Grenzen](velocity-http2-und-sicherheit.md).

In den Tabellen unten ist ein Schlüssel ohne das Präfix `Q.` relativ zu `Q` (`webserver.keepAlive.max` ist
`Q.webserver.keepAlive.max`).

## Woher die Einstellungen kommen

Einstellungen sind JSON unter dem Schlüssel `Q`. Sie werden in dieser Reihenfolge gelesen, und eine spätere Quelle hat Vorrang: die eingebauten Standardwerte der Engine; der Konfigurationsbaum (`qbix.conf`, `ports.conf`, `conf-enabled/`, `mods-enabled/`, `sites-enabled/`, in `/etc/qbix`, mit darübergelegten Overlays wie `/etc/vc`); die mit `--config` angegebene Datei; und der Speicher des Control Panels (nur für die Einstellungen, die das Panel anbietet).

So geben Sie aus, was geladen würde, ohne den Server zu starten:

```bash
php sbin/qbixserver.php --root=web --layout
php sbin/qbixserver.php --root=web -t --config=/etc/qbix/sites-enabled/default.conf
```

| Option | Bedeutung |
|---|---|
| `--conf-dir=DIR` | Ein Konfigurationsverzeichnis, aufgebaut wie `/etc/apache2` (`auto` ist `/etc/qbix` mit Overlays) |
| `--config=FILE` | Eine JSON-Site-Datei, meist `DIR/sites-enabled/SITE.conf` |
| `--distribution=NAME` | Die Ergänzungen von `Q_WebServer_Distribution_NAME` laden (Standard: `QBIX_DISTRIBUTION`, dann die Datei `DISTRIBUTION`). Die Distribution von Velocity ist `vc`, die `/etc/vc` über `/etc/qbix` legt |
| `--layout` | Die Konfigurationsdateien ausgeben, die geladen würden, und beenden |

Der Baum: `qbix.conf` (Basis, wie `apache2.conf`), `ports.conf` (Ports, auf denen gelauscht wird), `envvars` (Umgebung des Serverprozesses, einschließlich `QBIX_RUN_USER` und `QBIX_RUN_GROUP`), `conf-available/` und `conf-enabled/`, `mods-available/` und `mods-enabled/`, `sites-available/` und `sites-enabled/` (aktiviert per symbolischem Link), `designs/`, `ssl/`, `acl/`. Der Zustand liegt in `/var/lib/qbix` (`/var/lib/vc`), die Protokolle in `/var/log/qbix` (`/var/log/vc`). Einzelheiten und die Migration einer bestehenden Installation: [Velocity-Verzeichnisstruktur auf der Festplatte](../../../eng-us/bc/6.0/velocity-ondisk-layout.md).

## Programme

| Programm | Was |
|---|---|
| `sbin/qbixserver.php` (Phar: `sbin/qbixserver.phar`) | Der Server |
| `sbin/qbixctl.php` | `start`, `stop`, `restart`, `graceful`, `status`, `-t`, `-S`, `ensite`, `dissite`, `enconf`, `disconf`, `enmod`, `dismod`, `panel:password`, `panel:check`, `panel:2fa`, `ext:*`, `ssl:*` |
| `sbin/qbixconsole.php` | Jeder Konsolenbefehl; `qbixconsole list`, `qbixconsole help <command>` |
| `bin/qshell.php` (`vc-qshell`, wenn aus einem Paket installiert) | Die [Q-Shell](../../../eng-us/features/6.0/velocity-q-shell.md) in einem Terminal |
| `sbin/uwebserver` | Der kleine [Server für statische Dateien](../../../eng-us/features/6.0/velocity-uwebserver.md) in C |

Seit 0.0.4.41 liegen der Daemon und seine Administrationsbefehle in `sbin/` und die Benutzerbefehle in `bin/`. Jeder frühere Pfad (`qbixserver.php`, `qbixctl.php`, `qbixconsole.php`, `qshell.php` oben im Baum, `bin/qbixserver.phar`, `bin/uwebserver`) ist eine Weiterleitung, die die neue Datei im selben Prozess ausführt (gleiche Kommandozeile, Prozess-ID und Prozesstitel, Standardströme und Exit-Status). Eine Weiterleitung gibt nur dann einen Hinweis auf der Standardfehlerausgabe aus, wenn diese ein Terminal ist; `QBIX_MOVED_QUIET=1` unterdrückt ihn. Das `bin` von Composer listet die vier PHP-Programme in `sbin/` und `bin/` auf.

Alle Programme akzeptieren `--name=value`, `--name value`, `-name=value`, `-name value`, gebündelte Ein-Buchstaben-Flags und `--no-flag` sowie `--version`, `-version`, `-v`, `-V`, `--about`, `--copyright` (Release, Build, von wo es läuft, PHP, System und Erweiterungen, Lizenz).

### `qbixctl restart` und Protokolle

`qbixctl restart` startet mit den Optionen neu, mit denen der Server gestartet wurde: Ein Server, der mit einer PID-Datei gestartet wurde, schreibt daneben, wie er gestartet wurde (`<pid file>.json`: die Kommandozeile, die `-d`-Einstellungen des Interpreters, das Startverzeichnis, die Variablen `QBIX_*`, `*_CONF_DIR`, `*_STATE_DIR`, `*_DISTRIBUTION`, `*_RUN_USER` und `*_RUN_GROUP` sowie die Ausgabedatei). Optionen, die `restart` übergeben werden, ersetzen die aufgezeichneten (`qbixctl restart --workers=16`); alles nach `--` wird angehängt. Ohne `--log` geht die Ausgabe des Servers nach `Q.webserver.log.dir`, sonst nach `var/log`, `files/log` oder `logs` des Dokumentenstammverzeichnisses oder des Verzeichnisses darüber, sonst in das Protokollverzeichnis des Konfigurationsbaums (`/var/log/qbix`, `/var/log/vc` für Velocity oder `QBIX_LOG_DIR`), sonst nach `var/log` neben dem Dokumentenstammverzeichnis; nie in das temporäre Verzeichnis. `qbixctl status`, `stop` und `graceful` finden einen laufenden Server auch ohne seine PID-Datei.

## Allgemeine Servereinstellungen (`Q.webserver` und `Q`)

| Schlüssel | Standard | Was er bewirkt |
|---|---|---|
| `webserver.keepAlive.max` | `100` | Höchstzahl an Anfragen pro Keep-alive-Verbindung; die letzte und jede `5xx` werden mit `Connection: close` beantwortet |
| `webserver.keepAlive.timeout` | `15` | Sekunden, bevor eine untätige Verbindung geschlossen wird |
| `webserver.maxConnections` | `1024` | Höchstzahl gleichzeitiger Verbindungen; darüber hinaus `503` |
| `webserver.fileCache.maxSize` | `64MB` | Speicher für zwischengespeicherte Dateiantworten; ist er voll, macht die am längsten nicht verwendete Datei Platz |
| `webserver.fileCache.maxFile` | `1MB` | Größte Datei, die im Speicher gehalten wird |
| `webserver.fileCache.checkInterval` | `1` | Sekunden zwischen Prüfungen auf Dateiänderungen |
| `webserver.rateLimit.enabled`, `.requests`, `.window` | `false`, `100`, `60` | Ratenbegrenzung pro IP |
| `webserver.requestTimeout` | `30` | Sekunden, die eine Anfrage laufen darf, bevor `504` folgt; `0` keine Grenze |
| `webserver.workerMemoryCeiling` | `256` MB oder 3/4 von `memory_limit` | Heap-Größe, ab der ein Worker ersetzt wird |
| `webserver.warmup` | keiner | Skript, das einmal im Elternprozess nach der Transformation und vor dem Forken läuft |
| `webserver.eventLoop` | `auto` | `auto`, `iopoll`, `revolt`, `select`; `QBIX_EVENT_LOOP` hat Vorrang |
| `webserver.reusePort` | `false` | `SO_REUSEPORT`, damit sich mehrere Server einen Port teilen |
| `webserver.debug` | `false` | Meldung, Klasse, Datei, Zeile und Stacktrace eines nicht abgefangenen Fehlers in der Antwort zeigen; ist es aus, läuft der Exception-Handler der Anwendung, sonst die gestaltete `500`-Seite |
| `webserver.followSymlinks` | `false` | Links folgen, die das Dokumentenstammverzeichnis verlassen |
| `webserver.hosts.<host>.root` | das Standard-Stammverzeichnis | Dokumentenstammverzeichnis pro Host (ausgewählt über `Host`) |
| `webserver.hosts.<host>.log` | keiner | `true` für `<host>-access.log` und `<host>-error.log` im Protokollverzeichnis des Servers, oder ein Objekt, das `dir`, `accessName`, `errorName`, `fileMode`, `dirMode` überschreibt |
| `webserver.headers`, `headersOnScripts`, `hsts` | siehe [Sicherheit](velocity-http2-und-sicherheit.md) | Header in jeder Antwort |
| `webserver.scripts`, `frontControllers`; `web.static.paths` | alle | Welche Skripte über ihren Namen laufen, welche Datei der Front-Controller ist, welche Dateien unverändert gesendet werden (Opt-in-Listen) |
| `webserver.fallback` | keiner | Auffangregel: `"index.html"`, `{"handler":"app/notfound"}` oder `{"file":"404.html"}` |
| `webserver.hotReload` | `false` | `classes/`, `handlers/`, `config/` überwachen; bei Änderungen neu starten (`--hotreload`) |
| `webserver.cgi.patterns`, `webserver.cgi.binary` | `[]`, automatisch | Skripte, die `php-cgi` brauchen, und dessen Pfad |
| `webserver.user`, `group`, `allowRootWorkers` | siehe [Worker-Pool](velocity-worker-pool.md) | Als wer die Worker laufen |
| `webserver.brand`, `brandUrl`, `maintainer`, `maintainerUrl` | `Qbix Server`, leer, leer, leer | Der Produktname und optionale Links, die in den eigenen Ansichten des Servers gezeigt werden; eine URL, die nicht http oder https ist, wird verworfen |
| `dashboard` | aktiviert | `false` deaktiviert `/Q/dashboard`, `/Q/health`, `/Q/ws` |
| `dashboard.token` | keiner | `?token=VALUE` verlangen |
| `dashboard.hidePanelRequests` | `false` | Anfragen an `/Q/` aus dem Dashboard herauslassen |
| `autoload.psr-4`, `autoload.psr-0` | `{}` | Namespace-Zuordnungen, zum Beispiel `{"App\\": "src/"}` |
| `socket.io`, `socket.js` | `/socket.io`, `/Q/socket.js` | Socket.IO-Endpunkt und der Pfad des minimalen WebSocket-Clients; `false` deaktiviert |
| `app` | leer | App-Name, der den Namen der Handler-Funktionen vorangestellt wird |
| `compat.statTtl` | `0` | Siehe [Worker-Pool](velocity-worker-pool.md) |
| `log.*` | ohne ihn kein Protokoll | Das Zugriffs- und das Fehlerprotokoll, siehe „Protokollierung“ unten. Die Protokolle werden nur geschrieben, wenn `Q.webserver.log` vorhanden ist |

### Protokollierung

Einstellungen unter `Q.webserver.log` (in der Engine gegen `Q_WebServer_Log` geprüft; **es wird nichts protokolliert, bis der Block existiert**, selbst ein leer wirkender wie `{"dir": "logs"}`). Aus Exponential heraus schreibt `[LogSettings]` in `settings/velocity.ini` diesen Block für Sie (dessen Standard für `Format` ist `combined`, nicht `vhost` wie bei der Engine).

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `dir` | `<app>/logs`, sonst `logs` | Verzeichnis der Protokolldateien |
| `access`, `error` | `true`, `true` | Mit `false` eines der beiden Protokolle ausschalten |
| `accessName`, `errorName` | `access.log`, `error.log` | Dateinamen: ein bloßer Name ohne Verzeichnisanteil (alles andere fällt auf den Standard zurück, sodass ein Name nicht außerhalb von `dir` schreiben kann) |
| `format` | `vhost` | `vhost`, `qbix`, `combined`, `common` oder eine Formatzeichenkette mit Apache-Token (`%h %t %r %>s %b %D %T %{ms}T %{Name}i` und andere) |
| `bufferSize` | `65536` | Bytes, die vor einem Schreibvorgang gepuffert werden; `0` schreibt jede Zeile |
| `flushInterval` | `1` | Sekunden zwischen zeitgesteuerten Leerungen des Puffers |
| `maxSize` | `52428800` | Größe, ab der eine Datei rotiert wird |
| `archiveAfterDays` | `2` | Ältere Protokolle werden zu `.gz` komprimiert |
| `deleteAfterDays` | `30` | Ältere Protokolle werden gelöscht |
| `fileMode`, `dirMode` | `0666` abzüglich umask, `0755` | Berechtigungen; `0640` und `0750` verhindern, dass andere Konten lesen, wer die Site besucht hat |

Die Rotation erfolgt täglich und fügt das Datum vor `.log` ein (`site.log` wird zu `site.2000-10-10.log`). Ein virtueller Host protokolliert in eigene Dateien, indem er seinem Eintrag `webserver.hosts.<host>` ein `log` hinzufügt (`true` oder ein Objekt, das `dir`, `accessName`, `errorName`, `fileMode`, `dirMode` überschreibt); eine Anfrage an einen Host ohne diesen Eintrag geht in das Protokoll des Servers. Benannte Formate: `common` ist `%h %l %u %t "%r" %>s %b`; `combined` ergänzt Referrer und User-Agent; `qbix` ergänzt die Zeit in Millisekunden; `vhost` ergänzt den Hostnamen in Anführungszeichen.

### Das Zugriffsprotokoll

Seit 0.0.4.28 ist das Standardformat `vhost`: die ältere `qbix`-Zeile plus der Hostname in Anführungszeichen am Ende. Ein Protokollleser, der sich am Zeilenende verankert (fail2ban, ein benutzerdefiniertes Format von GoAccess), muss angepasst werden, oder Sie setzen `{"Q":{"webserver":{"log":{"format":"qbix"}}}}`, um die alten Zeilen beizubehalten. Antworten aus dem Pool werden protokolliert, wenn sie hinausgehen, nicht wenn sie zugeteilt werden (0.0.3.1), und die Zeile behauptet für andere Protokolle nicht mehr HTTP/1.1. Die Dateien jedes Hosts werden unter denselben Bedingungen rotiert, archiviert und bereinigt wie die des Servers selbst; der Protokollbetrachter des Dashboards nimmt `?host=` entgegen.

### Presets

`--preset=NAME` mit `laravel`, `symfony`, `wordpress`, `drupal`, `exponential`. Ein Preset trägt einen `_webserver`-Block, der unter `Q.webserver` eingemischt wird (so erreicht das Preset `keepGlobals`). Das Preset `exponential` lässt die Quelltexttransformation eingeschaltet, erhält die Typregister über Anfragen hinweg und bringt die eigenen Listen von Exponential für Skripte, Front-Controller und statische Pfade mit, wenn die Konfiguration keine nennt; konfigurierte Listen werden nie erweitert.

### Umgebung

| Variable | Bedeutung |
|---|---|
| `QBIX_RUN_USER`, `QBIX_RUN_GROUP`, `QBIX_RUN_ALLOW_ROOT` (unter Velocity zuerst `VC_RUN_*`) | Als wer die Worker laufen |
| `QBIX_DISTRIBUTION` | Zu ladende Distribution |
| `QBIX_CONF_DIR`, `QBIX_STATE_DIR`, `QBIX_LOG_DIR` | Verzeichnisse |
| `QBIX_EVENT_LOOP` | Backend der Event-Loop |
| `QBIX_SHIP_VERSION` | Beim Bauen des Phar: die Version, die das Archiv zeigt (auf die Release setzen, die erstellt wird) |
| `QBIX_MOVED_QUIET` | Den Hinweis der Weiterleitung unterdrücken |

## Marke und die eigenen Seiten des Servers

Die Namen und Zeichen, die in den eigenen Ansichten des Servers (`/Q/dashboard`, `/Q/panel`, Fehlerseiten, Verzeichnislisten) gezeigt werden, stammen aus `Q.webserver` (in der Engine gegen `Q_WebServer_Brand` geprüft):

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `brand` | `Qbix Server` | Produktname; wird in Kopfzeilen, Titeln und im Kopf des Control Panels gezeigt |
| `brandUrl`, `maintainer`, `maintainerUrl` | leer | Links für die Marke und eine Nennung des Maintainers in der Fußzeile (nur `http`- und `https`-URLs bleiben erhalten) |
| `brandDescription` | eingebaut | Text für Linkvorschauen |
| `brandIcon` | keiner | Eine `.svg`-, `.png`-, `.jpg`- oder `.ico`-Datei, die als Symbol der Seiten verwendet wird |
| `brandMark` | erster Buchstabe der Marke | Der Buchstabe, der auf das generierte Symbol gezeichnet wird |
| `brandColor`, `brandAccent`, `brandBackground` | `#7c8aff`, `#22d3ee`, `#0f1117` | Farben des generierten Symbols |
| `brandFont` | Suche in gängigen Systemschriften | Schriftdatei für das generierte Rastersymbol |

Mit der Standardmarke und ohne weitere Einstellungen wird das eigene Logo des Servers verwendet. Die Seiten tragen außerdem Symbole, ein Web-App-Manifest und Linkvorschauen (24. September 2026); jede Markup-Datei ist ein Design auf der Festplatte ([Designs](../../../eng-us/features/6.0/velocity-control-panel.md#restyle-the-servers-pages-designs)).

## Event-Loop und TLS

`webserver.eventLoop` wählt die Schleife, die auf Sockets wartet: `auto` nimmt Revolt, wenn `revolt/event-loop` installiert ist, sonst `stream_select`; `iopoll` (das native `Io\Poll` von PHP) muss ausdrücklich gewählt werden, weil es noch nicht gegen die echte Erweiterung gelaufen ist; `QBIX_EVENT_LOOP` hat Vorrang vor der Einstellung. Revolt wird nur mit der Erweiterung `ev`, `event` oder `uv` schneller und war in Messungen bis 2.000 Verbindungen nicht schneller als `select`; die Obergrenze unterhalb von etwa 1.000 Workern ist die von `stream_select` ([Worker-Pool](velocity-worker-pool.md)).

Eine TLS-Session kann wiederaufgenommen werden (22. September 2026): Der HTTPS-Listener hält einen Kontext mit dem Session-Cache und dem Ticket-Schlüssel, und jede angenommene Verbindung erbt ihn, sodass ein wiederkehrender Besucher und jede neue Verbindung innerhalb eines Besuchs den vollständigen Handshake überspringen. Prüfen Sie das an einem HTTPS-Listener von Velocity (nicht an einem vorgeschalteten Server wie Apache) mit `openssl s_client -connect host:443 -reconnect < /dev/null | grep -c Reused`; mehr als 0 bedeutet, dass Sessions wiederaufgenommen werden.

## Vorwärm-Cache beim Start

Beim Start durchläuft die Kompatibilitätsschicht das Dokumentenstammverzeichnis und zerlegt jede PHP-Datei in Token, damit die Worker die Ergebnisse erben. Seit dem 23. September 2026 wird das Ergebnis zwischen den Starts aufbewahrt (ein Exponential-Baum mit 5.000 Dateien: von 4,7 s auf 0,6 s). Ein Eintrag wird nur wiederverwendet, wenn seine Größe und seine Änderungszeit noch übereinstimmen; die Datei wird verworfen, wenn sich der Transformer, die PHP-Version oder das Stammverzeichnis unterscheidet. Die Startzeile meldet, wie viele Einträge wiederverwendet wurden: Wurde bei einem zweiten Start keiner wiederverwendet, konnte der Cache nicht geschrieben werden.

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `Q.webserver.compat.persistPrewarm` | `true` | Das Ergebnis zwischen den Starts aufbewahren |
| `Q.webserver.compat.dir` | `qbixserver-compat` im temporären Verzeichnis des Systems | Wo es aufbewahrt wird (angelegt mit `0700`, die Datei mit `0600`: Sie enthält transformierte Kopien Ihres Quelltexts) |
| `Q.webserver.compat.dirMode` | `0700` | Verzeichnismodus |
| `Q.compat.skipSourceCodeTransform` | `false` | Die Quelltexttransformation ausschalten (siehe den [Worker-Pool](velocity-worker-pool.md), „Die Kompatibilitätsschicht“) |

## Bindung und Dateien

Ohne `--user` und eine Konfiguration behält ein als root gestarteter Server root-Worker nur, wenn das Dokumentenstammverzeichnis root gehört (die Startmeldung sagt das). `--socket=PATH` lauscht auf einem Unix-Socket (Modus `--socket-mode`, Standard `0660`). Protokoll- und Cache-Dateien pro Host und des Servers verwenden `fileMode` und `dirMode` (Standards: Dateien `0666` abzüglich der umask, Verzeichnisse `0755`); bei Sites, die jeweils unter einem eigenen Benutzer laufen, sollte der Host, der die Datei schreibt, ihr Eigentümer sein.

## Scheduler

Der Server führt Handler nach einem Zeitplan aus, wie cron, gesteuert über `Q.scheduler` (eingeführt am 21. Juli 2026): Jede Aufgabe nennt einen Handler, der mit `Q::event()` in einem geforkten Kindprozess aufgerufen wird, mit `every` Sekunden oder mit `times` (`HH:MM`), eingeschränkt durch `weekdays` und `monthdays`. Es ist kein Weg, ein PHP-Skript für die Kommandozeile auszuführen. Geplante Aufgaben geben root-Rechte ab, wie es die Worker tun. Felder, Beispiele und Grenzen: [Velocity-Scheduler](../../../eng-us/features/6.0/velocity-scheduler.md); die eigene `docs/configuration.md` der Engine, Abschnitt „Scheduler“.

## Siehe auch

- Funktionen: [Velocity-Webserver](../../../eng-us/features/6.0/velocity-web-server.md), [Server mit persistenten Workern](../../../eng-us/features/6.0/velocity-persistent-worker-server.md), [Scheduler](../../../eng-us/features/6.0/velocity-scheduler.md), [Statische Dateien und Bilder](../../../eng-us/features/6.0/velocity-static-files-and-images.md), [WebSockets und Ereignisse](../../../eng-us/features/6.0/velocity-websockets-and-events.md), [Discovery, Föderation und Bereitstellung](../../../eng-us/features/6.0/velocity-discovery-federation-and-deploy.md), [Pakete und Binärdateien](../../../eng-us/features/6.0/velocity-packages-and-binaries.md)
- Spezifikationen: [Worker-Pool](velocity-worker-pool.md), [HTTP/2 und Sicherheit](velocity-http2-und-sicherheit.md)
- Upgrade-Hinweise: [Upgrade-Hinweise zur Velocity-Engine](../../../eng-us/bc/6.0/velocity-engine-upgrade-notes.md), [Velocity-Engines](../../../eng-us/bc/6.0/velocity-engines.md), [Velocity-Verzeichnisstruktur auf der Festplatte](../../../eng-us/bc/6.0/velocity-ondisk-layout.md)
- Geschichte: [Velocity-Chronik](../../../eng-us/history/velocity/README.md), [Velocity-Changelog](../../../changelogs/extensions/exponential-velocity.md)
