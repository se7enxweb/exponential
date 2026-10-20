# 8. Die Site ausliefern: Velocity, Apache, nginx und FrankenPHP

Ein installiertes Exponential ist ein Verzeichnis voller PHP-Dateien; irgendetwas muss Verbindungen annehmen, HTTP und
HTTPS sprechen, statische Dateien ausliefern und jede andere Anfrage an einen Front-Controller weiterreichen. Dieses
Kapitel zeigt, wie das geht. Es beginnt mit **Exponential Velocity**, dem Anwendungsserver, der für Exponential
ausgeliefert wird und in jeder Phase von der Entwicklung bis zur Produktion der empfohlene Weg ist: Mit der Engine
`qbix` von Velocity **ist weder ein separater Webserver noch ein separater HTTPS-Server nötig**. Velocity lauscht selbst
auf den HTTP- und HTTPS-Ports, hält die Anwendung in persistenten Workern geladen, beantwortet wiederholte Seitenabrufe
aus seinem eigenen Antwort-Cache und verwaltet seine eigenen Zertifikate, einschließlich eines selbstsignierten
Ersatzzertifikats und Let's Encrypt. Anschließend behandelt das Kapitel die klassischen Setups (Apache mit PHP-FPM,
auch unter Plesk; nginx; FrankenPHP), die Dateiberechtigungen, die jedes Setup braucht, und den Betrieb hinter einem
Reverse Proxy.

[Inhalt](README.md) · Zurück: [7. Die Installation über die Konsole](07-installation-ueber-die-konsole.md) · Weiter: [9. Datenbanken](09-datenbanken.md)

---

## Inhalt dieses Kapitels

1. [Die Wahl in einer Tabelle](#81-die-wahl-in-einer-tabelle)
2. [Wie eine Anfrage beantwortet wird](#82-wie-eine-anfrage-beantwortet-wird)
3. [Exponential Velocity, der empfohlene Weg](#83-exponential-velocity-der-empfohlene-weg)
   1. [Das Engine-Paket installieren](#831-das-engine-paket-installieren)
   2. [Die Engine wählen](#832-die-engine-wählen)
   3. [Einstellungen: wo sie liegen und wie man sie ändert](#833-einstellungen-wo-sie-liegen-und-wie-man-sie-ändert)
   4. [Der erste Start](#834-der-erste-start)
   5. [Adresse, Ports, Benutzer und Gruppe](#835-adresse-ports-benutzer-und-gruppe)
   6. [Worker: Pool-Größe, der dynamische Pool und die Zygote](#836-worker-pool-größe-der-dynamische-pool-und-die-zygote)
   7. [Persistente Worker und KeepGlobals](#837-persistente-worker-und-keepglobals)
   8. [Der Antwort-Cache](#838-der-antwort-cache)
   9. [HTTPS direkt von Velocity](#839-https-direkt-von-velocity)
   10. [Der Konfigurationsbaum: /etc/vc im Debian-Stil](#8310-der-konfigurationsbaum-etcvc-im-debian-stil)
   11. [Velocity als Dienst betreiben](#8311-velocity-als-dienst-betreiben)
   12. [Logs](#8312-logs)
   13. [Das Dashboard und das Control Panel](#8313-das-dashboard-und-das-control-panel)
   14. [Eine Änderung bereitstellen: exp:velocity deploy](#8314-eine-änderung-bereitstellen-expvelocity-deploy)
   15. [Das Engine-Archiv: engine.phar](#8315-das-engine-archiv-enginephar)
   16. [Befehlsreferenz](#8316-befehlsreferenz)
4. [FrankenPHP](#84-frankenphp)
5. [Apache mit PHP-FPM](#85-apache-mit-php-fpm)
6. [nginx mit PHP-FPM](#86-nginx-mit-php-fpm)
7. [Dateiberechtigungen und Eigentümer](#87-dateiberechtigungen-und-eigentümer)
8. [Hinter einem Reverse Proxy](#88-hinter-einem-reverse-proxy)
9. [Checkliste](#89-checkliste)
10. [Referenzen](#810-referenzen)

Jeder Befehl in diesem Kapitel wird im Installationsverzeichnis ausgeführt, also in dem Verzeichnis, das `index.php`
enthält. Die Konsole ist `bin/php/console`; `./console` ist dasselbe Programm, sofern die Installation die übliche
Abkürzung hat. Wenn Sie einen Befehl als `root` ausführen, fügen Sie `--allow-root-user` hinzu; ein Skript, das dies
benötigt, weist darauf hin.

---

## 8.1 Die Wahl in einer Tabelle

| | Exponential Velocity (`qbix`) | FrankenPHP (über Velocity) | Apache + PHP-FPM | nginx + PHP-FPM |
|---|---|---|---|---|
| Rolle | **empfohlen** für jede Phase | produktionsreife Alternative | klassisch | klassisch |
| PHP-Version | 8.1 oder neuer (vom Engine-Paket vorausgesetzt) | das in die Binärdatei eingebaute PHP (8.5 in den Releases 1.12) | 8.0 oder neuer | 8.0 oder neuer |
| Separater Webserver nötig | **nein** | nein (Caddy ist in die Binärdatei eingebaut) | ja | ja |
| Separates HTTPS-/Zertifikatswerkzeug nötig | **nein**: eigene Zertifikate, selbstsignierter Ersatz, ACME | nein: Ihr Zertifikat oder ein selbstsigniertes | ja (`mod_ssl` plus certbot oder ein Hosting-Panel) | ja |
| Herkunft des Codes | Composer-Paket `se7enxweb/exponential-velocity` | Binärdatei, von `exp:velocity install` heruntergeladen und per SHA-256 geprüft | das Betriebssystem | das Betriebssystem |
| Anwendung bleibt zwischen Anfragen im Speicher | ja (persistente Worker) | nein (sauberer Zustand je Anfrage) | nein | nein |
| Antwort-Cache vor PHP | ja | noch nicht | nein (nur die eigenen Caches des Kernels) | nein |
| HTTP/2 | ja | ja | mit `mod_http2` | mit `http2` |
| Rewrite-Regeln | eingebaut, dieselbe Liste wie `.htaccess_root` | generierte Caddyfile, dieselbe Liste | `.htaccess_root` | von Ihnen geschrieben (Abschnitt 8.6) |
| Befehle | `exp:velocity start`, `stop`, `graceful`, `deploy`, ... | dieselben Befehle | `systemctl`, `apachectl` | `systemctl`, `nginx -t` |
| Geeignet für Shared Hosting | meist nicht (braucht einen dauerhaft laufenden Prozess) | meist nicht | ja | selten |

Der eingebaute Server von PHP (`php -S`) ist eine dritte Velocity-Engine. Er ist nur deshalb die ausgelieferte
Voreinstellung, weil er keine zusätzliche Installation braucht; er ist für die Entwicklung gedacht, hat kein TLS und
ist nicht für Besucher bestimmt.

Verwenden Sie einen klassischen Server, wenn Ihr Hosting keine dauerhaft laufenden Prozesse erlaubt, wenn Sie bereits
ein abgestimmtes Apache oder nginx betreiben, das Sie behalten möchten, wenn auf dem Rechner nur PHP 8.0 vorhanden ist
(das Standard-PHP von Red Hat Enterprise Linux 9 und seinen Nachbauten; Velocity braucht 8.1, siehe
[Kapitel 2](02-voraussetzungen.md)), oder wenn mehrere Kunden auf einem Rechner auf Betriebssystemebene voneinander
isoliert sein müssen (betreiben Sie dann ein Velocity je Website, jedes unter einem eigenen Benutzer, oder einen
klassischen Server mit einem PHP-FPM-Pool je Website).

Die Möglichkeiten schließen sich nicht gegenseitig aus. Eine verbreitete Form ist, dass Velocity die Website ausliefert
und Apache oder nginx während einer Migration auf denselben Dateien bestehen bleibt; Abschnitt 8.7.2 erklärt, was das
für die Eigentümer der Dateien bedeutet, und Abschnitt 8.3.14, wie ein einziges Deploy beide auf den neuesten Stand
bringt.

## 8.2 Wie eine Anfrage beantwortet wird

Was auch immer die Website ausliefert: Das Document Root ist das **Installationsverzeichnis**, und es gilt dasselbe
Routing. Es ist einmal in `.htaccess_root` niedergeschrieben; die Engines von Velocity und die generierte
FrankenPHP-Konfiguration setzen dieselbe Liste Regel für Regel um (`expVelocity::STATIC_PATHS`, `FRONT_CONTROLLERS`
und `ENTRY_SCRIPTS` in `kernel/classes/expvelocity.php`).

```
                     request path
                          |
          +---------------+-----------------------------------------------+
          |                                                               |
   a dot file or dot directory (.git, .env, .htaccess)  -----------------> 404 (qbix: 403)
   (.well-known stays reachable)                                          |
          |                                                               |
   an existing .php/.phtml/.phar below the root  ------------------------> 404
          |                                                               |
   /api/...  ------------------------------------------------------------> index_rest.php
   /content/treemenu..., /<siteaccess>/content/treemenu...  -------------> index_treemenu.php
          |                                                               |
   a listed asset path (design and extension stylesheets, images,        |
   scripts and fonts; var/*/storage/images; image originals;              |
   stored PDF exports; public caches; share/icons; package previews;      |
   favicon.ico, robots.txt, index.js, sw.js, w3c/p3p.xml)  --------------> sent as a file
          |                                                               |
   everything else (URL aliases, modules, /settings/site.ini, ...)  -----> index.php
```

Zwei Folgen sind für die Sicherheit wichtig und werden in [Kapitel 13](13-sicherheitshaertung.md) noch einmal behandelt:

- Keine PHP-Datei wird ausgeführt, nur weil ihr Pfad angefragt wurde, mit Ausnahme der Front-Controller im
  Wurzelverzeichnis.
- Eine Datei außerhalb der Liste wird nie ausgeliefert. `settings/site.ini`, die SQLite-Datenbank oder eine
  Kernel-Quelldatei, die per Pfad angefragt wird, landen bei `index.php` (das mit einer Seite oder einem 404 antwortet),
  nie die Datei selbst. Hochgeladene Originale, die keine Bilder sind, werden nur über `content/download` ausgeliefert,
  das die Berechtigungen prüft.
- Selbst innerhalb eines gelisteten Asset-Verzeichnisses wird ein Skript (`.php`, `.phtml`, `.phar`) oder ein
  Punkt-Pfad nie als Datei gesendet (`expVelocity::NEVER_STATIC`; die zweite Regel von `.htaccess_root`), sodass ein
  verirrtes Skript im Design-Verzeichnis einer Extension weder ausgeführt werden noch seinen Quelltext preisgeben kann.

**Prüfen Sie es auf jedem Server.** Sobald die Website antwortet, zeigen diese vier Anfragen, dass das Routing aktiv
ist. Ersetzen Sie die Adresse durch Ihre eigene:

```bash
curl -s -o /dev/null -w '%{http_code}\n' http://127.0.0.1:8088/.git/config            # 404 (403 from the qbix engine)
curl -s -o /dev/null -w '%{http_code}\n' http://127.0.0.1:8088/kernel/classes/ezcontentobject.php   # 404
curl -s -o /dev/null -w '%{http_code}\n' http://127.0.0.1:8088/robots.txt              # 200 when the file exists
curl -s http://127.0.0.1:8088/settings/site.ini | grep -c '^\[DatabaseSettings\]'      # 0: a page, not the file
```

Was schiefgehen kann: Ein `200` für die `.git`- oder die Kernel-Anfrage bedeutet, dass die Regeln nicht aktiv sind
(Apache ohne `.htaccess` oder `AllowOverride`, ein nginx-Block, der nicht der aus diesem Kapitel ist); halten Sie an
und beheben Sie das, bevor die Website von außen erreichbar ist. Ein `404` für jedes Asset unter `design/` bedeutet,
dass das Document Root nicht das Installationsverzeichnis ist.

---

## 8.3 Exponential Velocity, der empfohlene Weg

Velocity ist ein Anwendungsserver, der für Exponential geschrieben wurde. Ein Elternprozess lädt die Anwendung einmal
und forkt **Worker**, die eine Anfrage nach der anderen beantworten; zwischen den Anfragen wird jeder Worker in den
Zustand zurückversetzt, den er beim Forken hatte. Der Elternprozess nimmt jede Verbindung selbst an, beantwortet
statische Dateien, Treffer im Antwort-Cache und seine eigenen Seiten, ohne einen Worker einzubeziehen, und terminiert
TLS. Deshalb braucht eine Velocity-Website weder Apache noch nginx, PHP-FPM oder einen separaten TLS-Terminator.

```
                    Internet
                       |
         :80 / :443 (or 8088 / 8080 as shipped)
                       |
   +-------------------v--------------------------------------------+
   |  Velocity parent process (root when started as root)           |
   |   - binds the ports, reads the certificate, speaks TLS, HTTP/2  |
   |   - static files, response-cache hits, /Q/ pages               |
   |   - hands PHP requests to an idle worker                       |
   +--------+-------------------+-------------------+---------------+
            |                   |                   |
       worker (site user)  worker (site user)  ...  (forked by the zygote)
       Exponential kept loaded, reset after each request
```

Der Befehl `exp:velocity` (auch als `exp:vc` oder direkt als `php bin/php/velocity.php` erreichbar) steuert drei
Engines mit denselben Verben: `qbix` (der eigene Server von Velocity, Thema dieses Abschnitts), `frankenphp`
(Abschnitt 8.4) und `php` (der eingebaute Entwicklungsserver von PHP).

### 8.3.1 Das Engine-Paket installieren

Die Engine wird von der `composer.json` von Exponential vorgeschlagen, aber nicht standardmäßig installiert, weil sie
PHP 8.1 oder neuer braucht. Fügen Sie sie der Installation mit Composer hinzu:

```bash
composer require se7enxweb/exponential-velocity:~0.0.4.44
```

Sie landet in `vendor/se7enxweb/exponential-velocity` (der frühere Paketname `se7enxweb/qbix-webserver` wird weiterhin
gefunden). Die Tilde-Einschränkung folgt der letzten Versionsstelle der Engine, sodass `~0.0.4.44` spätere
`0.0.4.x`-Releases akzeptiert.

Die `composer.json` des Pakets setzt PHP 8.1 (`"php": ">=8.1"`) und die Extension `sockets` voraus (TCP-Optionen für
Client-Verbindungen und die Zygote aus Abschnitt 8.3.6), daher lehnt Composer das Paket unter PHP 8.0 oder ohne
`sockets` ab. Diese Ablehnung ist harmlos: Die Engine wird nur vorgeschlagen, und Exponential selbst läuft unter Apache
oder nginx weiter. Die Engine möchte außerdem `pcntl` und `posix`, um Worker zu forken und deren Benutzer zu wechseln,
sowie `openssl` für HTTPS. Beim Start verweigert sie den Betrieb ohne Prozessisolation (`pcntl` oder eine
php-cgi-Binärdatei) und, wenn HTTPS konfiguriert ist, ohne `openssl`, und gibt dabei den Installationsbefehl aus; für
den Rest ihres Standardumfangs warnt sie einmal und startet. Um zu sehen, was diesem PHP fehlt, zusammen mit dem
Befehl, der es auf diesem Betriebssystem installiert:

```bash
php bin/php/console exp:velocity ext check
php bin/php/console exp:velocity ext install-hint <extension>
```

Beides sind die engine-eigenen Befehle `qbixctl ext:check` und `ext:install-hint`, ausgeführt mit der Konfiguration
dieser Installation; `ext check` ohne Argumente ist die Standardaktion.

Das Engine-Paket gibt es auch als Betriebssystempaket (`exponential-velocity`, deb und rpm, mit einer systemd-Unit) und
als Docker-Image; siehe [Velocity-Pakete, Docker-Images und Binärdateien](../../en/us/features/6.0/velocity-packages-and-binaries.md).
Für eine Exponential-Installation ist das Composer-Paket dasjenige, das `exp:velocity` steuert.

### 8.3.2 Die Engine wählen

`settings/velocity.ini` wird mit `[ServerSettings] Engine=php` ausgeliefert, weil der eingebaute Server von PHP immer
funktioniert. Für jede echte Website setzen Sie `qbix`:

```bash
php bin/php/console exp:velocity config set ServerSettings Engine qbix
```

| Engine | Rolle | Ausgelieferte Ports (HTTP / HTTPS) | Hinweise |
|---|---|---|---|
| `qbix` | **empfohlen**: von der Entwicklung bis zur Produktion | 8088 / 8080 (`[ServerSettings] Port`, `HTTPSPort`) | persistente Worker, Antwort-Cache, TLS, HTTP/2, Baum `/etc/vc` |
| `frankenphp` | produktionsreife Alternative | 8089 / 8444 (`[FrankenPHPSettings]`) | eine heruntergeladene Binärdatei; sauberer Zustand je Anfrage; HTTPS standardmäßig an |
| `php` | Entwicklung (ausgelieferte Voreinstellung) | 8087 / keiner (`[PHPServerSettings]`) | `php -S` mit `bin/php/velocity-router.php`; kein TLS, kein Reload |

`Engine` ist nur die *Voreinstellung*: `--engine=<name>` spricht für einen Befehl eine andere Engine an,
`--engine=php,qbix` mehrere, `--all` jede Engine (für `start`, `stop`, `restart`, `graceful`, `kill` und `status`).
Jede Engine hat ihren eigenen Port, ihre eigene PID-Datei und ihre eigenen Logs, sodass sie für Tests nebeneinander
laufen können. Eine Website wird von einer einzigen ausgeliefert.

### 8.3.3 Einstellungen: wo sie liegen und wie man sie ändert

| Datei | Was sie ist |
|---|---|
| `settings/velocity.ini` | die ausgelieferten Voreinstellungen, jede Einstellung kommentiert; bearbeiten Sie sie nie |
| `settings/override/velocity.ini.append.php` | die Werte dieser Installation; geschrieben von `exp:velocity config set` |
| `/etc/vc/...` (oder `var/vc/qbix/etc/` ohne root) | der Konfigurationsbaum der Engine, bei jedem Start aus den beiden obigen Dateien erzeugt (Abschnitt 8.3.10) |

```bash
php bin/php/console exp:velocity config list                 # every setting; * marks the ones you set
php bin/php/console exp:velocity config list HTTPSSettings   # one block
php bin/php/console exp:velocity config get ServerSettings Workers
php bin/php/console exp:velocity config set ServerSettings Workers 16
php bin/php/console exp:velocity config unset ServerSettings Workers   # back to the shipped value
php bin/php/console exp:velocity config paths                # which file is which
```

Eine geänderte Einstellung erreicht den Server beim nächsten `restart` (oder `graceful`). Die Worker halten die
Anwendung im Speicher, daher gilt dasselbe für eine geänderte Klasse oder INI-Datei der Anwendung selbst
(Abschnitt 8.3.14).

### 8.3.4 Der erste Start

```bash
php bin/php/console exp:velocity start
php bin/php/console exp:velocity status
```

`start` schreibt die Konfiguration, startet den Server im Hintergrund (mit `setsid` abgekoppelt), wartet, bis der Port
antwortet, und gibt dann den Status aus. `status` listet jede Adresse, die sich öffnen lässt, als vollständige URL auf:
jede Website, die über einen Pfad erreicht wird, die Anmeldung zur Administration, Setup > Systeminformationen und
Setup > Caches, neben jeder einfachen Adresse die HTTPS-Adresse, wenn HTTPS aktiv ist, sowie das eigene Dashboard des
Servers. Darunter zeigt er den Prozess, die Version, die Konfiguration, den HTTPS-Zustand, die Log-Pfade und jede
Einstellung, auf die die laufende Engine nicht reagiert.

Mit den ausgelieferten Werten antwortet die Website unter `http://127.0.0.1:8088/` und ist nur vom Rechner selbst aus
(oder über einen SSH-Tunnel) erreichbar. Prüfen Sie es:

```bash
curl -sI http://127.0.0.1:8088/ | head -1
```

Erwarten Sie `HTTP/1.1 200 OK` oder eine Weiterleitung. `status --json` liefert dieselben Informationen für das
Monitoring.

Was beim ersten Start schiefgehen kann:

| Sie sehen | Warum | Was zu tun ist |
|---|---|---|
| `velocity: no server script at ...` | das Engine-Paket ist nicht installiert | Abschnitt 8.3.1, oder `--engine=php` für einen schnellen Blick |
| `velocity: port ... is already in use by another process` (die Engines `php` und `frankenphp`), oder ein `qbix`-Start, der nicht hochkommt, mit dem Bind-Fehler in `console.log` | eine andere Engine oder ein anderes Programm belegt den Port | `exp:velocity status --all`; die andere beenden oder `Port` ändern |
| der Start gelingt, aber `curl` von einem anderen Rechner bekommt keine Antwort | `Host` ist noch `127.0.0.1` | Abschnitt 8.3.5 |
| eine Warnung, dass die Worker als root laufen | nichts benennt einen Worker-Benutzer, und das Document Root gehört root | Abschnitt 8.3.5, dann 8.7 |
| eine Seite, die meldet, dass sich die Website in Wartung befindet | ein Installationslauf hat den Wartungsmodus eingeschaltet gelassen | `php bin/php/maintenance.php status`, dann `off` |

Die Startzeilen, einschließlich des Worker-Benutzers (`Workers as: ...`) und des TLS-Zustands, stehen in
`var/vc/qbix/run/console.log` (Abschnitt 8.3.12).

### 8.3.5 Adresse, Ports, Benutzer und Gruppe

Um Besucher direkt zu bedienen, binden Sie an alle Adressen auf den Standardports:

```bash
php bin/php/console exp:velocity config set ServerSettings Host 0.0.0.0 --allow-root-user
php bin/php/console exp:velocity config set ServerSettings Port 80 --allow-root-user
php bin/php/console exp:velocity config set ServerSettings HTTPSPort 443 --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user
```

| Einstellung (`[ServerSettings]`) | Ausgeliefert | Bedeutung |
|---|---|---|
| `Host` | `127.0.0.1` | Bind-Adresse. Die Voreinstellung hält den Server nur lokal, über einen Tunnel oder einen Reverse Proxy erreichbar |
| `Port` | `8088` | einfaches HTTP |
| `HTTPSPort` | `8080` | HTTPS, verwendet, wenn HTTPS aktiv ist (Abschnitt 8.3.9) |
| `DocumentRoot` | leer | leer bedeutet das Installationsverzeichnis, was richtig ist |
| `User`, `Group` | leer | unter wem die Worker laufen, wenn der Server als root gestartet wird |
| `AllowRootWorkers` | `disabled` | `root` als Worker-Benutzer wird abgelehnt, außer bei `enabled` |
| `FollowSymlinks` | `disabled` | eine Datei, die über einen symbolischen Link aus dem Document Root hinausführt, wird abgelehnt (403 bei `qbix`) |
| `HTTP2` | `enabled` | HTTP/2 für Clients, die es beim TLS-Handshake aushandeln |
| `StaticMaxAge` | `31536000` | Lebensdauer von Stylesheets, Skripten und Bildern im Browser-Cache, in Sekunden. Eine unter demselben Namen ersetzte Datei wird erst nach Ablauf erneut abgerufen; setzen Sie den Wert herab, während Sie an einem Stylesheet arbeiten |
| `ResponseHeaders[]` | `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy` | Header, die jeder Antwort hinzugefügt werden, die der Server selbst erzeugt (statische Dateien, skalierte Bilder, seine eigenen Fehlerseiten, Weiterleitungen); ein bereits vorhandener Header wird nie ersetzt. `Strict-Transport-Security` hat eine eigene Einstellung (Abschnitt 8.3.9) |
| `ResponseHeadersOnScripts` | `enabled` | dieselben Header auch dem hinzufügen, was `index.php`, `index_rest.php` und `index_treemenu.php` antworten, sofern das Skript diesen Header nicht selbst gesendet hat |

**Ports unter 1024 brauchen root.** Als root gestartet, behält der Elternprozess root (er bindet die Ports, liest das
Zertifikat und führt Reloads aus), und jeder Worker gibt root unmittelbar nach dem Forken ab, bevor irgendein
Anwendungscode läuft. Unter welchem Benutzer die Worker laufen, wird in dieser Reihenfolge entschieden, der erste
Treffer gewinnt:

1. `[ServerSettings] User` und `Group` in `velocity.ini` (übergeben als `--user` und `--group`);
2. `VC_RUN_USER` und `VC_RUN_GROUP` aus der Umgebung oder Zeilen `export VC_RUN_USER=...` in `/etc/vc/envvars`
   (die Engine liest auch `QBIX_RUN_USER` und `QBIX_RUN_GROUP`);
3. der Eigentümer und die Gruppe des Document Root.

Ein Benutzer oder eine Gruppe, die nicht existiert, bricht den Start ab. Ist nichts konfiguriert und gehört das
Document Root root, bleiben die Worker root, und der Start weist darauf hin: Geben Sie der Installation einen eigenen
Benutzer (Abschnitt 8.7) oder setzen Sie `User` und `Group`. Setzen Sie sie auf den Benutzer, dem die Dateien gehören:

```bash
php bin/php/console exp:velocity config set ServerSettings User example --allow-root-user
php bin/php/console exp:velocity config set ServerSettings Group example --allow-root-user
```

Als gewöhnlicher Benutzer gestartet, läuft der Server vollständig unter diesem Benutzer; verwenden Sie dann Ports über
1024 und stellen Sie einen Reverse Proxy oder eine Firewall-Weiterleitung davor (Abschnitt 8.8).

### 8.3.6 Worker: Pool-Größe, der dynamische Pool und die Zygote

| Einstellung (`[ServerSettings]`) | Ausgeliefert | Bedeutung |
|---|---|---|
| `Workers` | `4` | die Größe des Pools; ist `SpareWorkers` gesetzt, die Höchstzahl, auf die er wächst. Setzen Sie sie immer ausdrücklich |
| `SpareWorkers` | `0` | `0`: fester Pool. Über 0: ein **dynamischer Pool**, der so viele Worker untätig bereithält, weitere forkt, wenn alle beschäftigt sind (bis zu `Workers`), und überzählige wieder beendet |
| `IdleWorkerTimeout` | `60` | Sekunden, die ein zusätzlicher Worker untätig bleiben darf, bevor er beendet wird |
| `Instances` | `1` | Anzahl der Server, die sich mit `SO_REUSEPORT` dieselben Ports teilen (Linux und die BSDs); `Workers` und `SpareWorkers` gelten je Instanz |
| `Zygote` | leer (Voreinstellung der Engine: an) | neue Worker aus einem Prozess forken, der nie eine Client-Verbindung gehalten hat |
| `StatTtl` | `0` | Sekunden, die ein Worker Dateiinformationen (Existenz, mtime, Größe) über Anfragen hinweg behalten darf; höchstens 10 |

**Dimensionierung.** Ein Worker eines vollständigen Exponential kostet etwa 10 bis 11 MB eigenen Speicher (gemessen als
PSS auf 12 Kernen und PHP 8.5); der gemeinsam genutzte Teil, etwa 47 MB, fällt nur einmal an. Beurteilen Sie den
Speicher nach PSS oder privatem Speicher, nie nach RSS, das jede gemeinsam genutzte Seite einmal je Worker zählt und
einen Pool um ein Vielfaches zu groß ausweist. Sind alle Worker beschäftigt, wartet eine Anfrage im Elternprozess; sie
wird erst abgewiesen, wenn das Verbindungslimit (`maxConnections`, 1024) erreicht ist.

Ein guter Ausgangspunkt für eine Produktions-Website ist ein dynamischer Pool:

```ini
# settings/override/velocity.ini.append.php
[ServerSettings]
Workers=64
SpareWorkers=8
IdleWorkerTimeout=60
```

**Instanzen.** Ein Server beantwortet gecachte Seiten in seinem eigenen Prozess und ist auf einen Kern begrenzt
(gemessen: etwa 2.950 gecachte Seiten pro Sekunde über TLS; vier Instanzen, etwa 12.000). Gerenderte Seiten sind durch
die Worker begrenzt, nicht durch die Anzahl der Instanzen.

**Die Zygote.** `fork()` übergibt einem neuen Worker jeden Deskriptor, den der Server hält, einschließlich offener
Besucherverbindungen. Ein Worker, der unter Last vom Server geforkt wurde, hielt deshalb Verbindungen offen, bis er
beendet wurde. Die Zygote ist ein Prozess, der geforkt wird, bevor die erste Verbindung angenommen wird; spätere Worker
werden von ihr geforkt und erben keine Verbindungen. Sie braucht die Extension `sockets`; schlägt sie fehl, werden die
Worker wie bisher vom Server geforkt, und das Log meldet dies.

Worker werden außerdem von selbst ersetzt: nach `maxRequests` Anfragen (1000), wenn der Heap `workerMemoryCeiling`
überschreitet oder wenn eine Anfrage länger als `requestTimeout` läuft (30 Sekunden; der Client erhält 504). Dies sind
Engine-Einstellungen unter `Q.webserver`, beschrieben in der [Spezifikation des Worker-Pools](../../en/us/specifications/6.0/velocity-worker-pool.md).

### 8.3.7 Persistente Worker und KeepGlobals

`[ServerSettings] ForkPerRequest` entscheidet, wie lange ein Worker lebt:

| Wert | Verhalten |
|---|---|
| `disabled` (ausgeliefert) | **persistente Worker**: Jeder bedient viele Anfragen; der Zustand der Anfrage wird geleert, nachdem die Antwort hinausgegangen ist |
| `enabled` | eine Anfrage je Worker, dann ein frischer Fork: die Isolation von PHP-FPM, um den Preis, dass jede Anfrage kalt startet |

Persistente Worker behalten Datenbankverbindungen, Stat-Caches und alles, was der Kernel je Prozess vorhält. Gemessen
an einer Seite der Administration mit angemeldetem Benutzer: 256 bis 284 ms mit einem frischen Worker je Anfrage, 73
bis 80 ms mit persistenten Workern. Sie brauchen eine Engine, die `REQUEST_TIME_FLOAT` je Anfrage setzt und in diesem
Modus Header und Shutdown-Funktionen mitführt (Header und Shutdown-Funktionen ab qbix-webserver 0.0.4.25, das
`REQUEST_TIME_FLOAT` je Anfrage ab Exponential Velocity 0.0.4.42; Exponential schlägt 0.0.4.44 vor).

Zwischen den Anfragen setzt der Worker statische Klasseneigenschaften, Superglobals, eigene Globals, Ausgabepuffer,
Handler und Antwort-Header auf den Zustand zurück, mit dem er geforkt wurde. Einige Registries im Kernel werden mit
`include_once` gefüllt und könnten nach dem Leeren nie wieder gefüllt werden; sie müssen daher **behalten** werden: die
Registries für Datentypen, Workflow-Ereignisse, Benachrichtigungsereignisse und Payment-Gateways. Diese Namen sind
eingebaut (`expVelocity::DEFAULT_KEEP_GLOBALS`) und brauchen keine Einstellung.

```ini
[ApplicationSettings]
# Adds to the built-in list. Only for a global filled once by include_once.
KeepGlobals[]
KeepGlobals[]=MyExtensionRegistry
# disabled starts from an empty list (for a kernel without those registries)
KeepGlobalsDefaults=enabled
```

`--keep-global=Name[,Name]` fügt Namen für ein einzelnes `start`, `restart` oder `command` hinzu; `exp:velocity command`
gibt die vollständige Befehlszeile aus, `exp:velocity layout` die resultierende Liste. Behalten Sie nichts anderes:
Ein anfragebezogenes Global, das zwischen Anfragen behalten wird, lässt die Anfrage eines Besuchers in die nächste
durchsickern.

`[ServerSettings] PreloadWarmup` (eine Seite im Elternprozess vor dem Forken rendern) wird **deaktiviert** ausgeliefert
und sollte es bleiben; die Einstellungsdatei erklärt, welche Funktionen dadurch kaputtgingen.

### 8.3.8 Der Antwort-Cache

Der Antwort-Cache hält gerenderte anonyme Seiten vor und beantwortet wiederholte Anfragen im Serverprozess, bevor ein
Worker gewählt wird: kein Fork, kein PHP, keine Datenbank. Exponential konfiguriert ihn in `[CacheSettings]`:

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `Enabled` | `enabled` | der Cache ist an (er braucht qbix-webserver 0.0.4.26 oder neuer, das das Session-Cookie erkennt) |
| `DefaultTtl` | `30` | Sekunden, die eine anonyme Seite vorgehalten wird, wenn sie keine eigene Lebensdauer mitbringt |
| `Dir` | `var/cache/qbix-reverse` | Plattenebene für Einträge, die für den Shared Memory zu groß sind |
| `APCu`, `APCuMaxSize` | `enabled`, `0` | kleine Einträge in APCu, von den Workern gemeinsam genutzt (`apc.enable_cli` ist für den Serverprozess gesetzt) |
| `SkipCookies` | leer | Cookies, die „persönlich, nicht cachen“ bedeuten; leer leitet das Session-Cookie und `is_logged_in` ab |
| `StaleWhileRevalidate` | `60` | Sekunden, in denen eine abgelaufene Seite noch ausgeliefert wird, während eine Anfrage ihren Ersatz rendert |
| `NotFoundSeconds` | `0` | Sekunden, die ein 404 gemerkt wird; `0` schickt jedes 404 an PHP, sodass 404.log und security.log jedes einzelne sehen |
| `HonorRequestNoCache` | `enabled` | eine Anfrage mit `Cache-Control: no-cache` oder `Pragma: no-cache` erreicht PHP, und ihre Antwort wird neu gespeichert |
| `SweepEvery`, `SweepMaxAge` | `300`, `86400` | Entfernen abgelaufener Einträge von der Platte |
| `MinifyHtml` | `enabled` | gespeicherte Seiten ohne Template-Einrückung |

Angemeldete Besucher werden nie aus dem Cache bedient, und eine Seite mit einem Formular-Token wird als `private`
gesendet. Nach einer Änderung an einem Template oder Stylesheet:

```bash
php bin/php/console exp:velocity cache clear     # every cached page is rendered again on its next request
php bin/php/console exp:velocity cache stats     # where it is, how much it holds, when it was cleared
```

Prüfen Sie einen Treffer mit zwei Anfragen: Die zweite trägt `X-Cache: HIT` und einen `Age`-Header.

Die eigenen Caches des Kernels (View-Cache für Inhalte, Template-Blöcke, der rollenbewusste HTTP-Cache in
`settings/httpcache.ini`, der SQL-Abfrage-Cache in `settings/querycache.ini`) funktionieren unter jedem Server; sie
werden mit `bin/php/ezcache.php` oder `exp:cache` geleert. Siehe [die Seite zum Antwort-Cache](../../en/us/features/6.0/velocity-response-cache.md).

### 8.3.9 HTTPS direkt von Velocity

Velocity terminiert TLS in seinem eigenen Prozess, mit HTTP/2. Mehr ist nicht nötig: kein `mod_ssl`, kein stunnel,
kein Reverse Proxy und für ein öffentliches Zertifikat nicht einmal certbot.

Die Engine setzt `$_SERVER['HTTPS']` und `REQUEST_SCHEME` für die Anwendung. Eine Anfrage am TLS-Listener ist HTTPS,
gleich wer sie gesendet hat. Seit Velocity 0.0.4.44 ist auch eine Anfrage am einfachen Listener HTTPS, wenn sie von
einem vertrauenswürdigen Proxy kommt (`Q.webserver.proxy.trusted`, standardmäßig nur Loopback), der
`X-Forwarded-Proto: https`, `CloudFront-Forwarded-Proto: https` oder `CF-Visitor: {"scheme":"https"}` sendet; von jedem
anderen Client werden diese Header ignoriert. Siehe Abschnitt 8.8.

#### Mit einem vorhandenen Zertifikat

Jedes PEM-Zertifikat von einer CA oder einem Hosting-Panel (Endzertifikat mit Kette und dem privaten Schlüssel; eine
Datei darf beides enthalten):

```bash
php bin/php/console exp:velocity config set HTTPSSettings Certificate /etc/ssl/example.com/fullchain.pem --allow-root-user
php bin/php/console exp:velocity config set HTTPSSettings Key /etc/ssl/example.com/privkey.pem --allow-root-user
php bin/php/console exp:velocity config set HTTPSSettings Enabled true --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user
php bin/php/console exp:velocity ssl show --allow-root-user
curl -sI https://example.com/ | head -1      # expect HTTP/2 200 or a redirect
```

Was `velocity.ini` mit diesen Einstellungen macht (`expVelocity::httpsEnabled()` und `writeServerConfig()`):

- HTTPS wird nur eingeschaltet, wenn `Enabled` auf `true` (oder `enabled`) steht **und** beide Dateien existieren. Ein
  Pfad, der nicht existiert, lässt den Server bei einfachem HTTP, statt einen TLS-Port zu starten, der nicht
  funktionieren kann; `status` meldet, dass HTTPS aus ist und was es einschaltet. Relative Pfade werden vom
  Installationsverzeichnis aus gelesen.
- Die Site-Datei enthält dann `Q.web.https` mit `mode: manual` und den beiden Pfaden, und der Server wird mit
  `--https-port=<HTTPSPort>` gestartet.
- Vor der Verwendung wird das Paar geprüft: Das Zertifikat lässt sich parsen, ist nicht abgelaufen, und der Schlüssel
  gehört dazu. Ein Paar, das die Prüfung nicht besteht, wird nie einem Besucher gezeigt; ein **selbstsigniertes
  Zertifikat** springt ein, bis das echte wieder verwendbar ist (der `fallback` der Engine, Voreinstellung
  `self-signed`; `none` würde HTTPS stattdessen abschalten).
- Der Server liest eine private Kopie des Paars (`active-<port>-<fingerprint>-<pid>.pem`), sodass eine Datei, die
  anderswo neu geschrieben wird, keinen Handshake stören kann. Alle `watchInterval` Sekunden (60) werden die
  Quelldateien geprüft, und ein geändertes, gültiges Paar wird **im laufenden Betrieb eingewechselt**, für die nächste
  Verbindung, ohne Neustart und ohne abgebrochene Verbindung. Eine Erneuerung durch Ihr eigenes Werkzeug wird daher
  innerhalb einer Minute übernommen.
- HTTPS kommt vor einfachem HTTP hoch, sodass es nie einen Moment gibt, in dem der Server HTTP beantwortet, HTTPS aber
  nicht.

Der Serverprozess liest den Schlüssel als root, wenn er als root gestartet wurde, daher kann der Schlüssel bei
`0600 root` bleiben.

Auf den ausgelieferten Ports lautet die HTTPS-Adresse `https://<host>:8080/`, und einfaches HTTP bleibt auf 8088;
beide lauschen gleichzeitig, und `velocity.ini` hat keine Einstellung, die von einem auf das andere weiterleitet.
Stellen Sie sicher, dass die Adressen, die die Website erzeugt, die HTTPS-Adressen sind (`site.ini [SiteSettings]
SiteURL` und die URLs der Siteaccesses), bevor Sie `HSTSMaxAge` erhöhen.

#### Strict-Transport-Security

```ini
[HTTPSSettings]
HSTSMaxAge=300              # seconds; 0 sends no header
HSTSIncludeSubDomains=disabled
HSTSPreload=disabled
```

Der Header wird bei jeder HTTPS-Antwort gesendet (statische Dateien, die eigenen Seiten des Servers und die Antworten
der Skripte) und nie über einfaches HTTP. Ein Browser, der ihn gesehen hat, verweigert für diesen Host über den
gesamten Zeitraum einfaches HTTP, selbst nachdem Sie den Wert gesenkt haben. Erhöhen Sie ihn erst dann auf ein Jahr
(`31536000`), wenn jeder Name der Website dauerhaft über HTTPS ausgeliefert wird; Preload braucht
`HSTSIncludeSubDomains` und mindestens ein Jahr.

#### Let's Encrypt und andere ACME-CAs, Archive, PKCS#12

Das Zertifikats-Subsystem der Engine kann mehr, als `velocity.ini` zugänglich macht: Zertifikate von Let's Encrypt
oder jeder ACME-CA (ZeroSSL, Google) beziehen und erneuern, ein Zertifikat aus einem Archiv (`.zip`, `.tar.gz`, `.7z`
und andere) oder einem `.p12`/`.pfx`-Bündel lesen, übernehmen, was certbot bereits verwaltet, eines von einer URL
herunterladen oder ohne jede Datei ein selbstsigniertes Zertifikat erzeugen (die Modi `letsencrypt`, `archive`,
`pkcs12`, `certbot`, `remote` und `self-signed` neben `manual`). Diese Quellen werden unter `Q.web.https` in der
Konfiguration der Engine eingerichtet. Im Baum `/etc/vc` (Abschnitt 8.3.10) gehört alles in `conf-available/` dem
Administrator und wird vor der generierten Site-Datei zusammengeführt, daher ist ein Snippet dort der richtige Ort
dafür. Lassen Sie `[HTTPSSettings] Enabled` in diesem Fall auf `false`, damit die generierten Dateien `Q.web.https`
nicht selbst setzen.

Voraussetzungen für die HTTP-01-Challenge: Das DNS jedes Namens zeigt auf diesen Rechner, und **Port 80 dieses Servers
ist aus dem Internet erreichbar** (`[ServerSettings] Port 80`), weil die CA dort
`/.well-known/acme-challenge/...` abruft.

```bash
cat > /etc/vc/conf-available/https-acme.conf <<'EOF'
{ "Q": { "web": { "https": {
  "port": 443,
  "mode": "letsencrypt",
  "acme": {
    "email": "hostmaster@example.com",
    "domains": ["example.com", "www.example.com"],
    "directory": "letsencrypt-staging"
  }
} } } }
EOF
php bin/php/console exp:velocity conf enable https-acme --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user
php bin/php/console exp:velocity ssl show --allow-root-user
```

Anmerkungen:

- Die Dateien des Baums sind JSON-Objekte unter den Dateinamen von Apache. `/etc/vc` ist der Baum, wenn
  `exp:velocity` als root läuft; andernfalls nennt `exp:velocity layout` das verwendete Verzeichnis.
- Zuerst `directory: letsencrypt-staging`: Dessen Zertifikate werden von Browsern nicht als vertrauenswürdig
  anerkannt, aber seine Ratenlimits sind großzügig. Wenn `ssl show` ein Staging-Zertifikat aufführt, entfernen Sie diese
  Zeile (die Voreinstellung ist `letsencrypt`) und starten Sie neu.
- Bis das erste Zertifikat eintrifft, wird das selbstsignierte ausgeliefert; das CA-Zertifikat ersetzt es im laufenden
  Betrieb.
- Die Erneuerung läuft im Hintergrund, wenn noch ein Drittel der Laufzeit übrig ist (`renewAt` 0.33), mit wachsenden
  Pausen nach einem Fehlschlag (5 Minuten, verdoppelt bis zu einem Tag), sodass Ratenlimits nie aufgebraucht werden.
  `exp:velocity ssl renew [host...]` fordert sie sofort an.
- `keyType` (standardmäßig `ec256`), `challenge: dns-01` mit einem `dnsHook`-Programm (für Wildcards nötig), `webroot`
  (wenn ein anderer Server Port 80 belegt), `eab` (External Account Binding) und die übrigen Schlüssel sind in
  [Velocity: HTTPS und Zertifikate](../../en/us/features/6.0/velocity-https-certificates.md) und in der `docs/https.md` der
  Engine aufgeführt.
- `start` meldet, dass sich die Konfiguration wegen des aktivierten Snippets von `velocity.ini` unterscheidet; das ist
  zu erwarten.
- **Sichern Sie das Verzeichnis `ssl/`** des Konfigurationsverzeichnisses, mit dem der Server läuft (das
  `Q.webserver.confDir` der Engine, sofern `selfSigned.dir` kein anderes nennt). Alles darin lässt sich neu erzeugen,
  außer den ACME-Kontoschlüsseln (`acme/account-<ca>.pem`).

Für ein Zertifikat in einem Archiv oder Bündel verwenden Sie `"mode": "archive"` mit `"archive": "<file>"` oder
`"mode": "pkcs12"` mit `"bundle": "<file>"` und einem `password`, `passwordFile` oder `passwordEnv`. Für die
Entwicklung ganz ohne Zertifikat:
`{ "Q": { "web": { "https": { "port": 8443, "mode": "self-signed" } } } }`.

#### Zertifikate untersuchen

```bash
php bin/php/console exp:velocity ssl show --allow-root-user          # served certificate, every known one by expiry
php bin/php/console exp:velocity ssl show --json --allow-root-user   # for monitoring: watch daysLeft
php bin/php/console exp:velocity ssl renew example.com --allow-root-user
```

Ein CA-Zertifikat mit 90 Tagen Laufzeit wird erneuert, wenn noch etwa 30 Tage übrig sind; eines mit weniger als 14
verbleibenden Tagen bedeutet also, dass die Erneuerung seit mehr als zwei Wochen fehlschlägt; der letzte Fehler, den
`ssl show` zusammen mit dem ACME-Zustand ausgibt, sagt warum. Lassen Sie NTP laufen: Zertifikate werden gegen die
Systemuhr geprüft. Der Reiter **SSL** des Control Panels zeigt dasselbe und kann vom Browser aus erneuern und neu laden
(Abschnitt 8.3.13).

| Symptom | Ursache | Abhilfe |
|---|---|---|
| `status` meldet, HTTPS sei aus | `Enabled` ist nicht `true`, oder eine genannte Datei existiert nicht | die Pfade mit `ls -l` prüfen; `config get HTTPSSettings Certificate` |
| der Browser warnt vor dem Zertifikat | der selbstsignierte Ersatz wird ausgeliefert: das echte Paar ist abgelaufen, passt nicht zusammen oder ist noch nicht ausgestellt | `ssl show`; das Start-Log nennt den Grund |
| ACME `Invalid response from http://.../.well-known/acme-challenge/...` | Port 80 erreicht diesen Server nicht | Port 80 öffnen oder `acme.webroot` für den Server setzen, dem er gehört |
| `none of the certificates belongs to the private key` | Schlüssel und Zertifikat stammen aus verschiedenen Anträgen | den Schlüssel verwenden, für den das Zertifikat ausgestellt wurde |

### 8.3.10 Der Konfigurationsbaum: /etc/vc im Debian-Stil

Die Konfiguration von Velocity auf der Platte spiegelt Debians `/etc/apache2`, sodass ein Administrator, der Apache
kennt, alles an der erwarteten Stelle findet:

```
/etc/vc/                      Velocity's overlay on the engine's base tree /etc/qbix
├── vc.conf                   settings shared by every site        (apache2.conf)
├── ports.conf                listen ports                         (ports.conf)
├── envvars                   environment of the server process    (envvars)
├── conf-available/*.conf     shared snippets                      -> conf-enabled/  (symlinks)
├── mods-available/*.conf     engine modules: http2, cache, ...    -> mods-enabled/  (symlinks)
├── sites-available/*.conf    one file per installation            -> sites-enabled/ (symlinks)
├── designs/                  the server's own page designs
└── ssl/                      certificates (self-signed, imported, ACME), in the directory the server runs with

/var/lib/vc/sites/<site>.json  what is known about each installation
```

**Welches Verzeichnis.** `[LayoutSettings] ConfDir=auto` verwendet `/etc/vc`, wenn es existiert oder angelegt werden
kann (also wenn `exp:velocity` als root läuft), sonst ein vorhandenes `/etc/qbix`, sonst `var/vc/qbix/etc` innerhalb
der Installation. `disabled` verwendet nur eine einzige generierte Datei (`var/tmp/velocity-server.json`, die in jedem
Fall geschrieben wird, sodass auch eine ältere Engine daraus starten kann); ein Pfad benennt ein Verzeichnis.
`[LayoutSettings] StateDir=auto` legt die Metadaten der Website in `/var/lib/vc` ab, wenn die Konfiguration in
`/etc/vc` liegt, sonst in `var/vc/qbix/lib`. Der Name der Website in `sites-available` ist
`[LayoutSettings] SiteName`, standardmäßig der Host aus `site.ini [SiteSettings] SiteURL`.

**Das Overlay.** `/etc/qbix` ist der eigene Basisbaum der Engine (so wie ihn das Betriebssystempaket installiert);
`/etc/vc` wird von der `vc`-Distribution der Engine darübergelegt und nach ihm geladen, sodass es gewinnt.

**Ladereihenfolge**, das Spätere gewinnt (eine tiefe Zusammenführung, wie die Includes von Apache):

1. Voreinstellungen der Engine, dann die `config/server.json` der Anwendung
2. `vc.conf`
3. `ports.conf`
4. `mods-enabled/*` in Namensreihenfolge
5. `conf-enabled/*` in Namensreihenfolge
6. die Site-Datei, `sites-enabled/<site>.conf`
7. Kommandozeilen-Optionen (`--port`, `--workers`, ...)

**Wer was schreibt.** `velocity.ini` bleibt die Quelle. Bei jedem `start` und `restart` (und bei `layout migrate`)
schreibt `exp:velocity` die Datei `ports.conf`, die von ihm verwalteten Moduldateien und
`sites-available/<site>.conf` (Modus `0600`: Sie enthält Zertifikatspfade und das Dashboard-Token). Generierte Dateien
tragen `"_generated": "exp:velocity"`; entfernen Sie diese Markierung aus einer Datei, wird sie nie wieder
überschrieben, wie eine conffile von dpkg. `vc.conf`, `envvars` und alles andere, was Sie hinzufügen, werden einmal
angelegt und danach nur noch gelesen. Bevor der Baum verwendet wird, werden die generierten Teile zusammengeführt und
mit der einzelnen Konfiguration verglichen, die sie ersetzen; schlägt diese Prüfung fehl, wird der Server aus der
einzelnen Datei gestartet, und das Log nennt den Grund. Absichtlich vorgenommene Abweichungen (ein deaktiviertes Modul,
ein aktiviertes Snippet) werden berücksichtigt und in der Startmeldung genannt.

**Aktivieren und deaktivieren**, wie mit `a2ensite`, `a2enconf` und `a2enmod`:

```bash
php bin/php/console exp:velocity layout --allow-root-user                 # every path, what is enabled, the files in use
php bin/php/console exp:velocity layout migrate --allow-root-user         # write the tree now
php bin/php/console exp:velocity site enable <name> --allow-root-user     # sites-enabled/<name>.conf -> ../sites-available/
php bin/php/console exp:velocity site disable <name> --allow-root-user    # removes only the symlink
php bin/php/console exp:velocity conf enable|disable <name> --allow-root-user
php bin/php/console exp:velocity mod enable|disable <name> --allow-root-user
php bin/php/console exp:velocity ctl configtest --allow-root-user         # the engine's own check of the tree
```

Der Baum gehört zur Engine `qbix`. Bei `frankenphp` ist die Konfiguration eine generierte Caddyfile und bei `php` ein
Router-Skript; dort listet `layout` diese Dateien auf, und `site|conf|mod` verweigern den Dienst.

### 8.3.11 Velocity als Dienst betreiben

`exp:velocity` hat keinen Befehl, der einen Systemdienst für den Bootvorgang installiert. Es gibt zwei Wege:

**Die eigenen Units der Engine.** Das Engine-Paket liefert Beispiele mit:

- `vendor/se7enxweb/exponential-velocity/service/qbixserver.service` (systemd) und `com.qbix.server.plist` (launchd),
  mit einer `README.md`, für einen einfachen Qbix-Server, der mit `--root`, `--port` und `--pid` gestartet wird;
- `vendor/se7enxweb/exponential-velocity/packaging/systemd/exponential-velocity.service`, die Unit des
  Betriebssystempakets (`User=qbix`, `AmbientCapabilities=CAP_NET_BIND_SERVICE`,
  `EnvironmentFile=-/etc/default/exponential-velocity`, Härtung mit `NoNewPrivileges`, `ProtectSystem`,
  `ProtectHome`, `PrivateTmp`).

Beide starten die Engine direkt und umgehen damit, was `exp:velocity` hinzufügt: die generierte Site-Datei in
`/etc/vc`, `KeepGlobals`, `[PHPSettings] IniOptions[]` (OPcache, APCu), den Worker-Benutzer, das Engine-Archiv und die
Instanzen.

**Eine Unit, die exp:velocity ausführt (abgeleitet).** Die folgende Unit wird nicht mitgeliefert; sie ist aus dem
abgeleitet, was `expVelocity::start()` tut (es startet den Server abgekoppelt und kehrt zurück, sobald der Port
antwortet, wobei die PID in `var/vc/qbix/run/server.pid` hinterlegt wird), und aus den obigen Befehlen. Sie belässt
jede Einstellung in `velocity.ini`:

```ini
# /etc/systemd/system/exponential-velocity-example.service
[Unit]
Description=Exponential Velocity for example.com
After=network-online.target mysqld.service
Wants=network-online.target

[Service]
Type=forking
WorkingDirectory=/var/www/example.com
ExecStart=/usr/bin/php bin/php/console exp:velocity start --allow-root-user
ExecStop=/usr/bin/php bin/php/console exp:velocity stop --allow-root-user
ExecReload=/usr/bin/php bin/php/console exp:velocity graceful --allow-root-user
PIDFile=/var/www/example.com/var/vc/qbix/run/server.pid
TimeoutStartSec=90
Restart=on-failure
RestartSec=5

[Install]
WantedBy=multi-user.target
```

```bash
systemctl daemon-reload
systemctl enable --now exponential-velocity-example
systemctl status exponential-velocity-example
```

Hinweise: Sie läuft als root, damit der Server die Ports 80 und 443 binden und den Schlüssel lesen kann; die Worker
wechseln trotzdem zu `[ServerSettings] User` und `Group`. Passen Sie `After=` an Ihren Datenbankdienst an. Bei
`Instances` über 1 benennt `PIDFile` nur Instanz 0. Kombinieren Sie sie nicht mit der Unit des Betriebssystempakets für
dieselben Ports.

### 8.3.12 Logs

Alles, was Velocity schreibt, liegt unter `var/vc/`, ein Verzeichnis je Engine:

```
var/vc/
├── qbix/
│   ├── etc/   the configuration tree when /etc/vc is not used
│   ├── lib/   site metadata when /var/lib/vc is not used
│   ├── log/   <siteaccess>-access.log, <siteaccess>-error.log, velocity-layout.log   ([LogSettings] Dir)
│   └── run/   server.pid, console.log                                                 (PidFile, LogFile)
├── frankenphp/  bin/ caddy/ tls/ log/ run/
└── php/         log/server.log, run/server.pid
```

| Einstellung (`[LogSettings]`) | Ausgeliefert | Bedeutung |
|---|---|---|
| `Enabled` | `enabled` | ohne sie zeichnet der Server überhaupt nichts auf |
| `Dir` | `var/vc/qbix/log` | das Log-Verzeichnis der Engine `qbix` |
| `AccessName`, `ErrorName` | leer | leer benennt die Dateien nach dem Standard-Siteaccess (`site-access.log`) |
| `Format` | `combined` | das Combined-Format von Apache; `qbix` ergänzt die Antwortzeit; `common`; oder ein Format-String |
| `FileMode`, `DirMode` | `0640`, `0750` | ein Zugriffslog nennt Besucher; es ist nicht für jedes Konto lesbar gedacht |

Bei der Rotation wird das Datum vor die Dateiendung gesetzt (`site-access.2026-09-24.log`). `console.log` enthält, was
der Prozess ausgibt, einschließlich der Startzeilen (`Workers as: ...`, `tls: certificate ready ...`). Die eigenen Logs
des Kernels bleiben wie unter jedem Server in `var/<site>/log/`.

### 8.3.13 Das Dashboard und das Control Panel

Der Server beantwortet einige eigene Seiten unterhalb von `/Q/`, unabhängig von der Anwendung:

| Ansicht | Was | Wer sie öffnen darf (so wie `exp:velocity` es konfiguriert) |
|---|---|---|
| `/Q/dashboard` | laufende Anfragen, Worker, Speicher, Statuscodes | dieser Rechner; von anderswo mit `[DashboardSettings] Token` |
| `/Q/stats`, `/Q/metrics`, vollständiges `/Q/health` | Kennzahlen, Prometheus-Metriken, Statusbericht | dieser Rechner; von anderswo mit dem Token oder der Panel-Anmeldung |
| `/Q/phpinfo` | `phpinfo()` einschließlich der Prozessumgebung | dieser Rechner; von anderswo nur mit dem Token oder einer Panel-Sitzung, nie über `Remote=enabled` |
| `/Q/panel` | Control Panel: Domains, Zertifikate, Cache, Logs, Worker, Einstellungen | dieser Rechner, dann das Panel-Passwort |
| `/Q/health` (nur Status), `/Q/docs` | „ok“, Dokumentation | alle |

Ein Token ist eine beliebige lange Zufallszeichenkette, zum Beispiel von `openssl rand -hex 24`; setzen Sie es mit
`exp:velocity config set DashboardSettings Token <value>` und übergeben Sie es als `?token=...` oder als
`Authorization: Bearer ...`. `[DashboardSettings] Remote=enabled` gibt die Kennzahlen ohne Token aus der Ferne frei;
verwenden Sie das nur auf einem Port, den bereits eine Firewall einschränkt. Um das Panel eines entfernten Servers zu
erreichen, verwenden Sie einen SSH-Tunnel zum eigenen Port des Servers.

Der erste Besuch von `/Q/panel` legt das Panel-Passwort fest (mindestens 16 Zeichen nach den Stärkeregeln der Engine;
bcrypt; fehlgeschlagene Anmeldungen sperren die Adresse mit wachsenden Pausen). Auf einem Rechner, der aus dem
Internet erreichbar ist, legen Sie es sofort fest. Sein Speicher darf weder für die Gruppe noch für andere beschreibbar
sein, sonst sperrt sich das Panel selbst; `qbixctl panel:check` der Engine zeigt den Grund. Einzelheiten:
[Velocity-Control-Panel](../../en/us/features/6.0/velocity-control-panel.md).

### 8.3.14 Eine Änderung bereitstellen: exp:velocity deploy

Worker halten Klassen, INI-Werte und kompilierte Templates im Speicher, daher erreicht eine geänderte PHP-Klasse, ein
geänderter Operator oder eine geänderte INI-Datei sie erst nach einem Neustart, und Caches, die gerenderte Ausgaben
enthalten, müssen geleert werden, **nachdem** der neue Code läuft, sonst wird die alte Ausgabe erneut gecacht. Ein
einziger Befehl erledigt das in der richtigen Reihenfolge:

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user   # what it would do, nothing else
php bin/php/console exp:velocity deploy --allow-root-user             # the usual case
php bin/php/console exp:velocity deploy --kernel --allow-root-user    # a kernel class was added or renamed
```

| # | Schritt | Von Hand |
|---|---|---|
| 1 | Autoloads der Extensions | `php bin/php/ezpgenerateautoloads.php -e` |
| 2 | Autoloads des Kernels, nur mit `--kernel` (beschränkt auf `kernel/` und `lib/`) | `php bin/php/ezpgenerateautoloads.php -k` |
| 3 | INI-Caches | `php bin/php/ezcache.php --clear-tag=ini` |
| 4 | Caches für template, template-override, translation und design_base | `--clear-id=<id>`, eine je Aufruf |
| 5 | Engine-Archiv, wenn Velocity aus einem solchen läuft (nur neu gebaut, wenn sich darin etwas geändert hat) | `php bin/php/console exp:phar build` |
| 6 | PHP-FPM neu laden | `systemctl reload <[DeploySettings] PhpFpmService>` |
| 7 | Velocity neu starten, wenn es läuft | `exp:velocity restart` |
| 8 | Caches für content, exphttpcache, (`--packer`: ezjscore-packer,) template-block | `--clear-id=<id>` |
| 9 | der Antwort-Cache von Velocity | `exp:velocity cache clear` |

Jeder Schritt gibt `PASS`, `FAIL` oder `SKIP` mit seiner Dauer aus; der erste Fehlschlag beendet den Lauf
(Exit-Status 1), und die nicht ausgeführten Schritte werden als `NOT RUN` aufgeführt. `--dry-run` gibt jeden Schritt
mit dem aus, was er tun würde (`DRY`), und ändert nichts.

Warum diese Reihenfolge: Die Caches, die gerenderte Ausgaben enthalten (View-Cache für Inhalte, HTTP-Cache,
Template-Blöcke, der Antwort-Cache), werden erst geleert, nachdem PHP-FPM neu geladen und Velocity neu gestartet wurde.
Würden sie früher geleert, würde jede in der Zwischenzeit angefragte Seite vom alten Code gerendert und erneut gecacht,
und die Änderung sähe aus, als hätte sie nicht funktioniert.

Einige Schritte entscheiden selbst, dass sie nichts zu tun haben, und melden das:

- **Engine-Archiv** (Schritt 5) läuft nur, wenn Velocity aus einem Archiv läuft (`EnginePhar`, Abschnitt 8.3.15) und
  gestartet ist. Dann wird zuerst jede Datei geparst, die ins Archiv kommt, sodass eine Kernel-Datei, die sich nicht
  parsen lässt, das Deploy anhält, während PHP-FPM und Velocity noch den alten Code ausführen. Ohne Archiv gibt es
  diese Prüfung nicht: Führen Sie `php -l` für das aus, was Sie geändert haben.
- **Velocity neu starten** (Schritt 7) startet nur einen laufenden Server neu. Ein angehaltener bleibt angehalten: Ein
  Deploy bringt die laufenden Dienste auf den neuesten Stand, entscheidet aber nicht, welche laufen sollen.
- **PHP-FPM neu laden** (Schritt 6) wird mit einem Hinweis übersprungen, wenn kein Pool gefunden wurde, wenn er nicht
  als root ausgeführt wird oder wenn der Rechner kein `systemctl` hat.

Optionen: `--kernel`, `--no-autoload`, `--no-fpm`, `--no-velocity`, `--rebuild-phar`, `--packer` (die gepackten
Skripte und Styles zusammen mit den Template-Blöcken leeren; nur nötig, wenn sich eine Server-Funktion des Packers
oder deren Einstellungen geändert haben), `--engine=<name>`, `--dry-run`, `--json`.

`[DeploySettings] PhpFpmService` benennt das PHP-FPM, das neu geladen werden soll, sodass auch ein Apache oder nginx
neben Velocity den neuen Code ausführt: `auto` (die Voreinstellung) findet die Pool-Konfiguration, die nach der
Hosting-Domain der Installation benannt ist oder deren Verzeichnis nennt, in `/opt/plesk/php/*/etc/php-fpm.d`,
`/etc/php-fpm.d`, `/etc/php/*/fpm/pool.d` und `/etc/opt/remi/php*/php-fpm.d`; ein Unit-Name (oder mehrere, durch
Kommas getrennt) benennt es direkt; **`disabled`, wenn Velocity die Website allein ausliefert**. Der Schritt ist ein
sanftes `systemctl reload` und braucht root; unter jedem anderen Benutzer wird er mit einem Hinweis übersprungen.

Für eine Änderung an einem Template oder Stylesheet ist kein Neustart nötig: Leeren Sie wie gewohnt die
Template-Caches und führen Sie `exp:velocity cache clear` aus. `exp:velocity graceful` führt den Server neu aus und
behält dabei den lauschenden Socket, sodass ein Neustart keine Anfrage verliert.

### 8.3.15 Das Engine-Archiv: engine.phar

Der Kernel kann aus einem einzigen Archiv laufen, `dist/engine.phar`, statt aus den Dateien in `kernel/`, `lib/` und
`autoload/`. Das lohnt sich für die Verteilung und um zu wissen, dass die Engine genau das ausführt, was gebaut wurde;
gemessen ist es nicht schneller, sobald OPcache warm ist.

```bash
php bin/php/console exp:phar build                      # build (only when something changed; --force always)
php bin/php/console exp:phar check                      # is the archive current?
php bin/php/console exp:phar info
php bin/php/console exp:velocity config set ServerSettings EnginePhar enabled
php bin/php/console exp:velocity restart
```

Zum Schreiben eines Archivs muss `phar.readonly` von PHP ausgeschaltet sein. Die Konsole startet jeden Befehl in einem
frischen PHP, daher erreicht ein `php -d phar.readonly=0` vor `bin/php/console` den Befehl nie; `exp:phar build` prüft
deshalb zuerst, ob das Archiv aktuell ist, und startet sich, wenn es schreiben muss, selbst noch einmal mit
`-d phar.readonly=0`. Von Hand muss nichts gesetzt werden.

`[ServerSettings] EnginePhar` nimmt `disabled` (die Dateien auf der Platte, die Voreinstellung), `enabled`
(`dist/engine.phar`) oder einen Pfad. Ein Pfad, der nicht existiert, bricht den Start ab, statt stillschweigend
zurückzufallen. Ist ein Archiv in Gebrauch, bauen `start`, `restart` und `graceful` es zuerst neu, wenn eine Datei in
`kernel/`, `lib/` oder `autoload/` hinzugefügt, entfernt oder geändert wurde (andernfalls
`engine.phar is current, not rebuilt`); `--rebuild-phar` erzwingt es. Siehe
[Engine-Archiv (phar)](../../en/us/bc/6.0/phar.md).

### 8.3.16 Befehlsreferenz

| Befehl | Tut |
|---|---|
| `exp:velocity status` (Standardverb) | Übersicht über jede Engine, dann Einzelheiten; `--json`, `--all`, `--engine=` |
| `exp:velocity start` / `stop` / `restart` | wie benannt; `start` wartet, bis der Port antwortet |
| `exp:velocity graceful` | führt den Server neu aus und behält den lauschenden Socket; keine verlorenen Anfragen |
| `exp:velocity kill` | anhalten ohne Rückfrage, für einen festgefahrenen Worker |
| `exp:velocity deploy` | alles, was eine PHP-Änderung braucht, in der richtigen Reihenfolge (Abschnitt 8.3.14) |
| `exp:velocity command` | die Befehlszeile ausgeben, die ausgeführt würde, und beenden |
| `exp:velocity config list\|get\|set\|unset\|paths` | Werte von `velocity.ini` lesen und schreiben |
| `exp:velocity cache clear\|stats` | der Antwort-Cache |
| `exp:velocity layout [migrate]` | der Konfigurationsbaum und jede Datei, die der Server verwendet |
| `exp:velocity site\|conf\|mod enable\|disable <name>` | die a2ensite-Familie |
| `exp:velocity ssl show\|renew [host...]` | Zertifikate (nur `qbix`; die anderen Engines beziehen ihr Zertifikat aus `[HTTPSSettings]`). `show` ist die Standardaktion. Nicht in `--help` aufgeführt, aber akzeptiert |
| `exp:velocity ctl <args>` | `qbixctl` der Engine mit dem Baum, der Website und der PID-Datei dieser Installation (`ctl status`, `ctl configtest`, ...) |
| `exp:velocity ext check\|list\|plan\|install-hint\|build` | PHP-Extensions, die die Engine erwartet |
| `exp:velocity install` | FrankenPHP: die Binärdatei herunterladen und prüfen (`--force`, `--from=<file>`, `--check`, `--trust-github-digest`); `qbix`: nichts zu tun |

Optionen werden in GNU- und BSD-Schreibweise akzeptiert (`--keep-global V`, `-keep-global=V`, `-json`); alles nach
`--` wird unverändert an die Engine weitergereicht. `exp:velocity --help` gibt die vollständige Liste aus. Eine Option,
die zu einem anderen Verb gehört, wird mit einer Meldung abgelehnt, die die Verben nennt, zu denen sie gehört, zum
Beispiel `velocity: --dry-run goes with deploy` oder
`velocity: --rebuild-phar goes with start, restart, graceful and deploy`; ein unbekanntes Verb listet die gültigen auf.

---

## 8.4 FrankenPHP

FrankenPHP ist der Webserver Caddy mit eingebautem PHP, als eine einzige Binärdatei. Velocity lädt ihn herunter, prüft
ihn und steuert ihn mit denselben Befehlen. Er läuft im klassischen Modus: ein Pool von PHP-Threads, wobei jede Anfrage
wie unter PHP-FPM von einem sauberen Zustand aus startet, daher gelten `ForkPerRequest`, `KeepGlobals` und der
Antwort-Cache hier nicht.

```bash
php bin/php/console exp:velocity config set ServerSettings Engine frankenphp --allow-root-user
php bin/php/console exp:velocity install --allow-root-user    # optional: start installs it when missing
php bin/php/console exp:velocity start --allow-root-user
```

- **Die Binärdatei.** `install` holt das Release-Asset für diesen Rechner zu `[FrankenPHPSettings] Version`
  (ausgeliefert `1.12.7`) und weist es zurück, sofern seine SHA-256-Prüfsumme nicht mit der daneben festgelegten Zeile
  `Sha256[<asset>]` übereinstimmt. Es wird in `var/vc/frankenphp/bin/` abgelegt. `Variant` wählt den Linux-Build (`gnu`,
  die Voreinstellung, lädt `.so`-Extensions; `musl`; `mimalloc`). `BinaryPath` benennt einen eigenen Build.
  `install --from=<file>` installiert auf einem Rechner ohne Zugang zu GitHub eine kopierte Datei, die auf dieselbe Weise
  geprüft wird.
- **PHP-Einstellungen.** Die Binärdatei liest keine `php.ini` des Rechners, daher liefert
  `[FrankenPHPSettings] IniOptions[]` `display_errors=Off`, `log_errors=On`, `memory_limit=512M`, `opcache.enable=1`
  und die Upload-Limits mit; `PHPIniFile` benennt stattdessen eine eigene `php.ini`.
- **Threads.** `[FrankenPHPSettings] Workers` ist die Anzahl der PHP-Threads; leer bedeutet die Voreinstellung von
  FrankenPHP, die doppelte Anzahl der CPU-Kerne; mehr Threads als das erwiesen sich unter hoher Last als langsamer.
  `SpareWorkers` fügt unter Last Threads hinzu.
- **HTTPS.** Standardmäßig an (`HTTPS=enabled`) auf `HTTPSPort` (8444) neben HTTP auf `Port` (8089). Es verwendet
  `[HTTPSSettings] Certificate` und `Key`, wenn beide gesetzt sind; sind beide leer, erzeugt es ein selbstsigniertes
  Zertifikat für den Rechner in `var/vc/frankenphp/tls/` (ein Jahr gültig) und erneuert es einen Monat vor Ablauf. Eine
  öffentliche Website benennt ihr Zertifikat; führen Sie nach dem Ersetzen der Dateien `exp:velocity graceful` aus.
  `start --no-https` und `start --https` entscheiden für einen einzelnen Start (nur mit `start`, `restart` und
  `graceful`); `HTTPS=disabled` schaltet es ab. `exp:velocity ssl` gehört zur Engine `qbix` und verweigert hier den
  Dienst.
- **Komprimierung.** `[FrankenPHPSettings] Compression` (`zstd br gzip`) ist das, was Caddy den Clients anbietet;
  Caddy komprimiert nichts, solange es nicht dazu angewiesen wird. `disabled` schaltet es ab.
- **Konfiguration.** `var/vc/frankenphp/run/Caddyfile` wird bei jedem `start`, `graceful` und `restart` erzeugt und
  mit `frankenphp validate` geprüft, bevor sie die alte ersetzt. Ihr Routing ist das von `.htaccess_root`; sie verwendet
  nicht `php_server`, das jede vorhandene Datei ausliefern würde. Eigene Direktiven kommen in eine Datei, die
  `SiteInclude` benennt. `exp:velocity ctl caddyfile` gibt die Konfiguration aus.
- **Steuerung.** `stop` und `graceful` verwenden die Admin-API von Caddy über den nur für den Eigentümer zugänglichen
  Socket `var/vc/frankenphp/run/admin.sock`; `graceful` lädt die Konfiguration neu, ohne eine Verbindung abzubrechen.
- **Logs.** `var/vc/frankenphp/log/access.log` und `error.log`, im JSON-Format von Caddy.

Einzelheiten: [FrankenPHP](../../en/us/bc/6.0/frankenphp.md) und [Velocity-Engines](../../en/us/bc/6.0/velocity-engines.md).

---

## 8.5 Apache mit PHP-FPM

Apache mit `mod_rewrite`, `mod_proxy_fcgi` und einem PHP-FPM-Pool ist das klassische Setup und braucht keinen eigenen
dauerhaft laufenden Prozess.

### 8.5.1 Schritt für Schritt

1. **Die Rewrite-Regeln aktivieren.** Sie werden als `.htaccess_root` ausgeliefert; kopieren Sie die Datei, statt sie
   zu verschieben, damit ein Update eine neue Version mitbringen kann:

   ```bash
   cp .htaccess_root .htaccess
   ```

   Verwenden Sie stattdessen `.htaccess_root_static` nur, wenn Sie einen generierten statischen Cache ausliefern (es
   ist `.htaccess_root` mit zusätzlichen Regeln zum Ausliefern des statischen Caches).

2. **Die Module laden.** `mod_rewrite`, `mod_proxy` und `mod_proxy_fcgi` (Debian: `a2enmod rewrite proxy_fcgi`);
   für HTTPS `mod_ssl`, für HTTP/2 `mod_http2`.

3. **Den virtuellen Host anlegen.** Passen Sie die Pfade und den PHP-FPM-Socket an Ihr System an:

   ```apache
   <VirtualHost *:80>
       ServerName example.com
       ServerAlias www.example.com
       # The installation root, where index.php is
       DocumentRoot /var/www/example.com
       DirectoryIndex index.php

       <Directory /var/www/example.com>
           Options FollowSymLinks
           # Lets Apache read the .htaccess created in step 1
           AllowOverride All
           Require all granted
       </Directory>

       # Every PHP script goes to the PHP-FPM pool of this site
       <FilesMatch \.php$>
           SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost"
       </FilesMatch>
   </VirtualHost>
   ```

   Wenn Sie lieber auf `.htaccess` verzichten (`AllowOverride None` spart einen Dateizugriff je Verzeichnis), kopieren
   Sie die Regeln von `.htaccess_root` in den `<Directory>`-Block; innerhalb von `<Directory>` behalten sie ihre Form.
   Im Serverkontext (außerhalb von `<Directory>`) braucht jedes Muster einen führenden Schrägstrich, wie in
   `doc/en/us/examples/ezpublish.conf`, einem vollständigen virtuellen Host (HTTP-Weiterleitung, HTTPS, HTTP/2,
   PHP-FPM, Header) mit den Regeln der `.htaccess_root` dieser Release im Serverkontext
   ([Virtueller Host für Apache](../../en/us/examples/web-servers/apache-virtual-host.md)). Vergleichen Sie Ihre Kopie nach
   jedem Upgrade mit `.htaccess_root` und übernehmen Sie neue Regeln.

4. **HTTPS.** Fügen Sie einen virtuellen Host `*:443` mit demselben Inhalt hinzu, ergänzt um:

   ```apache
       SSLEngine on
       SSLCertificateFile    /etc/ssl/example.com/fullchain.pem
       SSLCertificateKeyFile /etc/ssl/example.com/privkey.pem
       Protocols h2 http/1.1
   ```

   und beziehen Sie das Zertifikat mit certbot oder Ihrem Hosting-Panel. Leiten Sie Port 80 im Host `*:80` auf HTTPS
   weiter (`Redirect permanent / https://example.com/`), sobald HTTPS funktioniert.

5. **Prüfen und neu laden:**

   ```bash
   apachectl configtest           # Syntax OK
   systemctl reload apache2       # httpd on Red Hat style systems
   curl -sI https://example.com/ | head -1
   ```

   Ein 404 für jede Seite bedeutet, dass `.htaccess` nicht gelesen wird (`AllowOverride`) oder `mod_rewrite` aus ist;
   ein 500 bedeutet, dass ein Blick in die Fehlerlogs von Apache und PHP-FPM fällig ist.

6. **Sicherheits-Header** sendet der Kernel auf seinen Seiten (`site.ini [HTTPHeaderSettings] SecurityHeaders[]`).
   Für statische Dateien fügen Sie dieselben mit `mod_headers` hinzu, wenn Sie sie auch dort haben möchten; siehe
   [Kapitel 13](13-sicherheitshaertung.md).

### 8.5.2 PHP-FPM

Der Pool führt PHP unter dem Benutzer der Website aus. Für Exponential relevante Einstellungen (in der Pool-Datei oder
in `php.ini`): `memory_limit` (mindestens 256M; mehr für große Importe), `max_execution_time`, `upload_max_filesize`
und `post_max_size` (Ihr größter Upload), OPcache eingeschaltet. Nach einer Änderung an einer PHP-Klasse, einem
Operator oder einer INI-Datei **laden Sie den FPM-Master neu, der die Website ausliefert**:

```bash
systemctl reload php-fpm                       # the distribution's single PHP
php bin/php/console exp:velocity deploy --no-velocity --allow-root-user   # finds the right one and clears caches in order
```

Auf Rechnern mit mehreren PHP-Versionen ist der Master, der die Website ausliefert, oft nicht derjenige namens
`php-fpm`. Finden Sie ihn über seine Pool-Datei: `[DeploySettings] PhpFpmService=auto` tut genau das
(`deploy --dry-run` zeigt, was es gefunden hat und warum).

### 8.5.3 Plesk

Plesk erzeugt den virtuellen Host selbst; bearbeiten Sie seine Dateien nicht. In Plesk:

- richten Sie das Document Root der Domain auf das Installationsverzeichnis (das Verzeichnis mit `index.php`);
- wählen Sie in den PHP-Einstellungen „FPM-Anwendung, bereitgestellt von Apache“ und die PHP-Version;
- behalten Sie `.htaccess` (kopiert aus `.htaccess_root`); das Apache von Plesk liest sie;
- installieren Sie das Zertifikat in Plesk (über seine Let's-Encrypt-Erweiterung oder ein eigenes) und schalten Sie
  die Weiterleitung auf HTTPS ein.

Plesk benennt jeden Pool nach der Domain in `/opt/plesk/php/<version>/etc/php-fpm.d/<domain>.conf`, ausgeliefert von
der systemd-Unit `plesk-php<XY>-fpm` (zum Beispiel `plesk-php85-fpm`). `systemctl restart php-fpm` startet einen
anderen Master neu, den keine Plesk-Website verwendet. Laden Sie den richtigen neu (`systemctl reload plesk-php85-fpm`)
oder lassen Sie `exp:velocity deploy` ihn finden: `auto` erkennt die Benennung von Plesk an der Hosting-Domain im Pfad
der Installation (`/var/www/vhosts/<domain>/...`).

---

## 8.6 nginx mit PHP-FPM

**Diese Konfiguration ist abgeleitet, nicht mitgeliefert.** Exponential liefert in seinem Repository keine
nginx-Konfiguration mit. Der folgende Server-Block ist eine Übertragung von `.htaccess_root` (der Version in dieser
Release) in nginx-Direktiven, Regel für Regel, nach der eigenen Dokumentation von nginx zu `location`, `try_files`,
`return` und `fastcgi_pass`. Testen Sie ihn auf Ihrer Website, bevor Sie sich darauf verlassen, und vergleichen Sie ihn
nach jedem Upgrade mit `.htaccess_root`.

```nginx
server {
    listen 80;
    server_name example.com www.example.com;

    # The installation root, where index.php is
    root /var/www/example.com;
    index index.php;

    # Uploads: nginx's default limit is 1 MB
    client_max_body_size 64m;

    # 1. No dot file or dot directory, anywhere; .well-known stays reachable
    location ~ (^|/)\.(?!well-known(/|$)) {
        return 404;
    }

    # 2. No script below the root runs because its path was asked for
    #    (.htaccess_root answers 404 only when the file exists; this answers 404 always)
    location ~ ^/.+/[^/]+\.(php[0-9]?|phtml|phar)$ {
        return 404;
    }

    # 3. Front controllers
    location ^~ /api/ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index_rest.php;
        fastcgi_param SCRIPT_NAME /index_rest.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }
    location = /index_rest.php {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index_rest.php;
        fastcgi_param SCRIPT_NAME /index_rest.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }
    location ~ ^/([^/]+/)?content/treemenu {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index_treemenu.php;
        fastcgi_param SCRIPT_NAME /index_treemenu.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }

    # 4. Files served as they are (the "- [L]" rules of .htaccess_root);
    #    a listed path that does not exist is a 404, as under Apache
    location ~ ^/var/([^/]+/)?storage/images(-versioned)?/                                    { try_files $uri =404; }
    location ~ ^/var/([^/]+/)?storage/original/image/.+\.(png|jpe?g|gif|webp|svg)$             { try_files $uri =404; }
    location ~ ^/var/([^/]+/)?storage/pdf/[A-Za-z0-9][A-Za-z0-9._-]*\.pdf$                      { try_files $uri =404; }
    location ~ ^/var/([^/]+/)?cache/(texttoimage|public)/                                       { try_files $uri =404; }
    # With site.ini [FileSettings] CacheVarDir=var_cache (multi-site hosting, see
    # doc/en/us/features/6.0/multi-site-log-and-cache-paths.md):
    #location ~ ^/var_cache/([^/]+/)?cache/(texttoimage|public)/                               { try_files $uri =404; }
    location ~ ^/design/[^/]+/(stylesheets|images|javascript|fonts)/                            { try_files $uri =404; }
    location ~ ^/share/icons/                                                                   { try_files $uri =404; }
    location ~ ^/extension/[^/]+/design/[^/]+/(stylesheets|flash|images|lib|javascripts?|fonts|vendor|media)/ { try_files $uri =404; }
    location ~ ^/packages/styles/.+/(stylesheets|images|javascript)/[^/]+/                      { try_files $uri =404; }
    location ~ ^/packages/styles/.+/thumbnail/                                                  { try_files $uri =404; }
    location ~ ^/var/storage/packages/.+\.(png|jpe?g|gif|webp)$                                 { try_files $uri =404; }
    location = /favicon.ico  { try_files $uri =404; }
    location = /robots.txt   { try_files $uri =404; }
    location = /index.js     { try_files $uri =404; }
    location = /sw.js        { try_files $uri =404; }
    location = /w3c/p3p.xml  { try_files $uri =404; }

    # 5. Everything else goes to index.php, with the original REQUEST_URI
    location / {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index.php;
        fastcgi_param SCRIPT_NAME /index.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }
}
```

Wie die Zuordnung funktioniert und warum die Reihenfolge so ist:

- nginx wählt eine Location nach seiner eigenen Rangfolge, nicht nach der Reihenfolge in der Datei: zuerst exakte
  Treffer (`=`), dann das längste `^~`-Präfix, dann **reguläre Ausdrücke in der Reihenfolge ihres Auftretens**, dann
  das längste einfache Präfix. Die Regeln für Punkt-Dateien und Skripte sind daher die ersten regulären Ausdrücke, so
  wie sie die ersten Regeln von `.htaccess_root` sind; `/` ist das einfache Präfix, das den Rest auffängt, wie die
  abschließende `RewriteRule .* index.php`.
- Die Regeln von `.htaccess_root` für `/api/` und das Baummenü schreiben auf einen Front-Controller um. Hier wird der
  Front-Controller in `SCRIPT_FILENAME` benannt, und die Anfrage wird nicht umgeschrieben, sodass `REQUEST_URI` (von
  `fastcgi_params` aus `$request_uri` gesetzt) weiterhin die Adresse enthält, die der Besucher angefragt hat, genau wie
  bei Apache.
- Eine `.php` im Wurzelverzeichnis, die kein Front-Controller ist (zum Beispiel `/config.php`), erreicht
  `location /` und wird von `index.php` beantwortet, nie ausgeführt: wie bei `.htaccess_root`.
- Die statischen Locations haben kein `fastcgi_pass`, daher läuft dort kein PHP; die Skript-Regel davor stellt sicher,
  dass eine `.php` unterhalb eines Asset-Verzeichnisses auch nicht als Text gesendet wird.
- `fastcgi_params` aus den nginx-Paketen übergibt `HTTPS`, wenn die Verbindung TLS ist, und genau das prüft
  Exponential zuerst.
- Nicht übertragen: die auskommentierten Favicon- und Debug-Zeilen von `.htaccess_root` sowie
  `/design/standard/images/favicon.ico`, das die Design-Regel bereits abdeckt.

Für HTTPS fügen Sie `listen 443 ssl;` (und `http2 on;` ab nginx 1.25.1), `ssl_certificate` und `ssl_certificate_key`
hinzu sowie einen separaten `server` auf Port 80, der `301 https://$host$request_uri` zurückgibt. Prüfen Sie mit
`nginx -t` und laden Sie mit `systemctl reload nginx` neu. Sicherheits-Header für statische Dateien:
`add_header ... always;` (Kapitel 13).

---

## 8.7 Dateiberechtigungen und Eigentümer

### 8.7.1 Was beschreibbar sein muss

Der Benutzer, der PHP für die Website ausführt (der Benutzer des PHP-FPM-Pools, der Worker-Benutzer von Velocity oder
der FrankenPHP-Prozess), muss in diese Verzeichnisse schreiben können; es ist die Liste, die der Setup-Assistent prüft
(`settings/setup.ini [directory_permissions]`):

| Verzeichnis | Warum |
|---|---|
| `var/` und darunter: `var/cache`, `var/log`, `var/storage`, `var/autoload`, `var/<site>/...` | Caches, kompilierte Templates, Logs, hochgeladene Dateien und Bildvarianten, Autoload-Arrays |
| `settings/`, `settings/override`, `settings/siteaccess`, `settings/siteaccess/<admin>` | der Setup-Assistent und die Administration schreiben Einstellungen |
| `design/`, `extension/` | der Setup-Assistent und die Paketinstallation schreiben dorthin |

Nach der Installation können Sie den Schreibzugriff auf `design/` und `extension/` wieder entziehen, wenn nichts Pakete
über die Administration installiert; `settings/` bleibt beschreibbar, wenn Administratoren Einstellungen im Browser
ändern (und die Prüfung `settings_permission` des Setups erwartet das).

Alles andere (kernel, lib, vendor, `index.php`) muss nur lesbar sein. Die Systemprüfung gibt für Ihren Rechner Befehle
in dieser Form aus:

```bash
sudo chown -R example:example design extension settings var
sudo chmod -R ug+rwx design extension settings var
```

### 8.7.2 Ein Benutzer oder eine Gruppe

Probleme mit Berechtigungen entstehen fast immer dadurch, dass **zwei Benutzer dieselben Dateien schreiben**: das PHP
des Webservers, die Velocity-Worker, Cronjobs und Kommandozeilen-Skripte. Eine Cache-Datei, die `root` auf der
Kommandozeile geschrieben hat, kann der Benutzer der Website danach nicht ersetzen, und die Website bricht an seltsamen
Stellen.

```
                 owner: example   group: example
   PHP-FPM pool  ----------------+
   Velocity workers ([ServerSettings] User/Group)
   cronjobs, console scripts ----+---- all write var/ as the same user
```

Empfohlen, in dieser Reihenfolge:

1. **Führen Sie alles unter dem Benutzer der Website aus.** PHP-FPM-Pool `user = example`; Velocity
   `[ServerSettings] User=example`, `Group=example` (oder lassen Sie beide leer und den Eigentümer des Document Root
   entscheiden); Cronjobs aus der Crontab dieses Benutzers (`crontab -u example -e`) oder mit
   `sudo -u example php runcronjobs.php`. Velocity als root zu starten ist in Ordnung: Nur sein Elternprozess bleibt
   root.
2. **Wenn die Benutzer verschieden sein müssen** (zum Beispiel `apache` von Apache und ein Deploy-Benutzer), geben Sie
   ihnen eine gemeinsame Gruppe, machen Sie die beschreibbaren Verzeichnisse für die Gruppe beschreibbar und setzen Sie
   setgid, damit neue Dateien die Gruppe erben, und verwenden Sie Berechtigungen, die den Schreibzugriff der Gruppe
   erhalten:

   ```bash
   sudo chgrp -R example design extension settings var
   sudo find var settings -type d -exec chmod 2775 {} +
   ```

   ```ini
   # settings/override/site.ini.append.php
   [FileSettings]
   StorageDirPermissions=0770
   StorageFilePermissions=0660
   TemporaryPermissions=0770
   LogFilePermissions=0660
   ```

   Die ausgelieferten Werte sind `0777` und `0666` (für alle beschreibbar); `site.ini` selbst empfiehlt `0770` und
   `0660` mit dem richtigen Benutzer und der richtigen Gruppe, was die gehärtete Wahl ist (Kapitel 13).

Wenn Apache (PHP-FPM) und Velocity dieselbe Installation nebeneinander ausliefern, teilen sie sich `var/`, die Caches
und die Sessions: Geben Sie ihnen denselben Benutzer oder das Setup aus Punkt 2. Testen Sie mit beiden im Betrieb und
sehen Sie sich nach einigen Anfragen den Eigentümer neuer Dateien in `var/` an.

### 8.7.3 Die eigenen Dateien von Velocity

| Pfad | Geschrieben von | Modus |
|---|---|---|
| `/etc/vc/sites-available/<site>.conf` | `exp:velocity` (root) | `0600` |
| `ssl/` des Konfigurationsverzeichnisses: private Schlüssel | dem Server | `0600`, atomar geschrieben, nie protokolliert |
| `var/vc/qbix/log/` | dem Server | `[LogSettings] FileMode` `0640`, `DirMode` `0750` |
| `var/cache/qbix-reverse/` | dem Server für die Worker | `[CacheSettings] FileMode` `0640`, `DirMode` `0750` |

Die Verzeichnisse für den Cache und die Vorkomprimierung werden beim Start an den Worker-Benutzer übergeben. Der
Worker-Benutzer muss den Quellbaum der Engine in `vendor/` lesen können.

---

## 8.8 Hinter einem Reverse Proxy

Velocity braucht keinen vorgeschalteten Proxy: Es liefert HTTPS selbst aus. Ein Proxy ist dennoch die richtige Form,
wenn ein Load Balancer oder CDN TLS bereits terminiert, wenn eine öffentliche Adresse mehrere Dienste trägt oder wenn
der Server als gewöhnlicher Benutzer auf einem hohen Port laufen muss. Das ausgelieferte `Host=127.0.0.1` von Velocity
ist genau dafür gemacht: Der Server ist nur vom Rechner selbst aus erreichbar, und nur über den Proxy.

**Was Exponential von einem Proxy liest** (`lib/ezutils/classes/ezsys.php`):

- **Welchen Proxys geglaubt wird.** Die unten genannten weitergeleiteten Header zählen nur, wenn die Gegenstelle der
  Verbindung (`REMOTE_ADDR`) in `site.ini [HTTPHeaderSettings] TrustedProxies[]` aufgeführt ist: Adressen und Bereiche,
  IPv4 und IPv6, standardmäßig `127.0.0.1` und `::1`, also ein Proxy auf demselben Rechner. Ein Proxy unter einer
  anderen Adresse muss hinzugefügt werden, zum Beispiel `TrustedProxies[]=192.0.2.10` oder
  `TrustedProxies[]=10.0.0.0/8`. Siehe
  [Weitergeleiteten Headern wird nur von konfigurierten Proxys vertraut](../../en/us/bc/6.0/trusted-proxies.md).
- **HTTPS.** `eZSys::isSSLNow()` prüft zuerst `$_SERVER['HTTPS']`, dann den Port gegen `site.ini [SiteSettings]
  SSLPort` (443), dann, von einem vertrauenswürdigen Proxy, `X-Forwarded-Proto: https`, dann `X-Forwarded-Port`, dann
  `X-Forwarded-Server` gegen `SSLProxyServerName`. Ein Proxy, der TLS terminiert, muss `X-Forwarded-Proto` setzen; dann
  stimmen die erzeugten URLs und das `Secure`-Flag des Session-Cookies (`[Session] CookieSecure=auto`).
- **Der Host.** `X-Forwarded-Host` von einem vertrauenswürdigen Proxy ersetzt `Host`; andernfalls lassen Sie den Proxy
  den ursprünglichen `Host` weitergeben (`proxy_set_header Host $host`, `ProxyPreserveHost On`).
- **Die Adresse des Besuchers.** `[HTTPHeaderSettings] ClientIpByCustomHTTPHeader=X-Forwarded-For` lässt
  `eZSys::clientIP()` diesen Header von einem vertrauenswürdigen Proxy lesen, und zwar von rechts: Vertrauenswürdige
  Proxys werden übersprungen, und die erste andere Adresse ist der Besucher. Einträge, die ein Client vorangestellt
  hat, werden ignoriert, daher darf ein Proxy anhängen (`$proxy_add_x_forwarded_for`) oder überschreiben.
- **Velocity** beantwortet diese Fragen, bevor der Kernel die Anfrage sieht; siehe „Velocity hinter einem Proxy“
  weiter unten.

**Velocity hinter einem Proxy.** Die Engine hat ihre eigene Liste vertrauenswürdiger Proxys,
`Q.webserver.proxy.trusted` in der Konfiguration der Engine, standardmäßig `127.0.0.1` und `::1`. Sie ist von
`TrustedProxies[]` getrennt, weil die Engine entscheidet, bevor irgendein PHP der Anwendung läuft:

- **Die Adresse des Besuchers.** Von einem vertrauenswürdigen Proxy übernimmt die Engine die Adresse des Besuchers aus
  `X-Forwarded-For` (oder dem in `Q.webserver.proxy.headers.ip` benannten Header), von rechts gelesen: Die erste
  Adresse, die kein vertrauenswürdiger Proxy ist, ist der Besucher. Diese Adresse übergibt sie dem Kernel als
  `REMOTE_ADDR`. Der Kernel sieht den Besucher dann als Gegenstelle: `ClientIpByCustomHTTPHeader` ist nicht nötig, und
  `TrustedProxies[]` beeinflusst die Adresse des Besuchers nicht.
- **HTTPS.** Die Engine setzt `$_SERVER['HTTPS']` und `REQUEST_SCHEME`, denen der Kernel ohne jede Proxy-Einstellung
  glaubt. Eine TLS-Verbindung zu Velocity selbst ist HTTPS, gleich wer sie gesendet hat. Eine einfache Verbindung gilt
  als HTTPS, wenn ein **vertrauenswürdiger** Proxy `X-Forwarded-Proto: https` (oder den in
  `Q.webserver.proxy.headers.proto` benannten Header), `CloudFront-Forwarded-Proto: https` oder das
  `CF-Visitor: {"scheme":"https"}` von Cloudflare sendet; bei einer Liste wie `https, http` zählt der erste Eintrag.
- **Engine-Versionen.** Das Lesen des weitergeleiteten Protokolls nur von vertrauenswürdigen Proxys kam mit
  Exponential Velocity **0.0.4.44** (Engine-Commit `380a64d`, 5. Oktober 2026), der Release, die die `composer.json`
  von Exponential vorschlägt. Bis einschließlich 0.0.4.43 übernahm ein Worker diese Header von jedem Client, sodass ein
  Besucher an einem einfachen Listener eine Anfrage für die Anwendung wie HTTPS aussehen lassen konnte. Prüfen Sie die
  installierte Version mit `composer show
  se7enxweb/exponential-velocity` und aktualisieren Sie mit
  `composer require se7enxweb/exponential-velocity:~0.0.4.44`. Halten Sie bei einer älteren Engine den einfachen Port
  von Velocity von außen unerreichbar (das ausgelieferte `Host=127.0.0.1` oder eine Firewall), wann immer ein Proxy
  davorsteht. Ab 0.0.4.44 muss ein Proxy, der TLS terminiert, in `Q.webserver.proxy.trusted` aufgeführt sein, sonst
  sieht die Website dahinter einfaches HTTP.
- **Der Host.** `X-Forwarded-Host` erreicht den Kernel von der Adresse des Besuchers aus und wird nicht verwendet.
  Lassen Sie den Proxy den ursprünglichen `Host`-Header weitergeben.

Um einem Proxy unter einer anderen Adresse zu vertrauen, legen Sie die Liste in einem Snippet des Konfigurationsbaums
ab (Abschnitt 8.3.10) und aktivieren es. Nennen Sie auch die Loopback-Adressen, damit ein lokaler Proxy weiter
funktioniert:

```bash
cat > /etc/vc/conf-available/reverse-proxy.conf <<'EOF'
{ "Q": { "webserver": { "proxy": { "trusted": ["127.0.0.1", "::1", "192.0.2.10", "10.0.0.0/8"] } } } }
EOF
php bin/php/console exp:velocity conf enable reverse-proxy --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user
```

`reverse-proxy` ist nur ein Name, den Sie wählen; jede Datei in `conf-available/` funktioniert auf dieselbe Weise.
Einträge sind Adressen oder CIDR-Bereiche.

**nginx vor Velocity (abgeleitetes Beispiel):**

```nginx
location / {
    proxy_pass http://127.0.0.1:8088;
    proxy_set_header Host              $host;
    proxy_set_header X-Forwarded-Proto $scheme;
    # appending is safe: Exponential reads the list from the right
    proxy_set_header X-Forwarded-For   $proxy_add_x_forwarded_for;
    proxy_http_version 1.1;
}
```

**Apache vor Velocity (abgeleitetes Beispiel):**

```apache
ProxyPreserveHost On
ProxyPass        / http://127.0.0.1:8088/
ProxyPassReverse / http://127.0.0.1:8088/
RequestHeader set X-Forwarded-Proto "https"
```

(`mod_proxy`, `mod_proxy_http` und `mod_headers`; `mod_proxy` von Apache fügt `X-Forwarded-For` selbst hinzu und hängt
es an einen vom Client gesendeten Wert an, was sicher ist, weil die Liste von rechts gelesen wird.)

Hinweise:

- **Let's Encrypt hinter einem Proxy:** Der Proxy belegt Port 80; lassen Sie also entweder ihn das Zertifikat beziehen,
  oder leiten Sie `/.well-known/acme-challenge/` an Velocity weiter, oder setzen Sie das `acme.webroot` der Engine auf
  ein Verzeichnis, das der Proxy ausliefert.
- **Cachende Proxys** dürfen keine Seiten angemeldeter Benutzer speichern. Exponential sendet `private` bei Seiten mit
  einem Formular-Token; ein Proxy, der nach Tags leert, liest den Header, der in
  `httpcache.ini [HttpCacheSettings] TagHeader` benannt ist (standardmäßig `disabled`).
- **Der Antwort-Cache von Velocity** funktioniert auch hinter einem Proxy; leeren Sie ihn wie gewohnt mit
  `exp:velocity cache clear`.
- **Apache oder nginx vor Apache mit PHP-FPM** (das nginx von Plesk vor Apache hat diese Form): Wenn der innere Server
  `REMOTE_ADDR` mit `mod_remoteip` oder dem `realip`-Modul von nginx auf den Besucher umschreibt und über HTTPS
  erreicht wird, sieht der Kernel den Besucher und `HTTPS=on` direkt und braucht weder Änderungen an
  `TrustedProxies[]` noch `ClientIpByCustomHTTPHeader`.

**Prüfen Sie es.** Ein von einem Besucher gesendeter weitergeleiteter Header darf nichts ändern. Direkt gegen die
Website (ohne Proxy):

```bash
curl -s -o /dev/null -w '%{redirect_url}\n' -H 'X-Forwarded-Proto: https' http://example.com/user/logout
# expected: http://example.com/   -- an https:// address here means the header was believed
```

Über den Proxy müssen die absoluten Links und Weiterleitungen einer Seite `https://` tragen, und „Ist meine Adresse
aufgeführt?“ (iptest) in der Debug-Leiste zeigt die Adresse, die der Kernel für den Besucher verwendet.

Was hinter einem Proxy schiefgehen kann:

| Symptom | Ursache | Abhilfe |
|---|---|---|
| Weiterleitungen und absolute Links führen zu `http://` | dem Proxy wird nicht vertraut (nicht Loopback, nicht in `TrustedProxies[]` bzw. für Velocity nicht in `Q.webserver.proxy.trusted`), oder er sendet kein `X-Forwarded-Proto` | ihn aufführen; den Header im Proxy setzen |
| jeder Besucher hat die Adresse des Proxys (Anmeldesperre, Logs) | dem Proxy wird nicht vertraut, oder `ClientIpByCustomHTTPHeader` ist unter Apache oder nginx mit PHP-FPM nicht gesetzt | den Proxy aufführen; `ClientIpByCustomHTTPHeader=X-Forwarded-For` setzen |
| eine Weiterleitungsschleife nach dem Umstellen der Website auf HTTPS | der Proxy spricht mit dem Besucher HTTPS, mit dem Server einfaches HTTP, und der Server weiß es nicht | wie in der ersten Zeile |
| Seiten angemeldeter Benutzer werden anderen Besuchern ausgeliefert | ein cachender Proxy speichert Seiten mit einem Session-Cookie | Antworten nicht cachen, die das Session-Cookie setzen oder davon abhängen |

---

## 8.9 Checkliste

- [ ] `Engine=qbix` gesetzt, `exp:velocity status` zeigt den laufenden Server, die Website antwortet.
- [ ] `Host`, `Port`, `HTTPSPort` wie beabsichtigt; Ports 80 und 443 in der Firewall offen, wenn Velocity direkt vor
      den Besuchern steht.
- [ ] Die Worker laufen unter dem Benutzer der Website (`Workers as: ...` in `var/vc/qbix/run/console.log`), nie als
      root.
- [ ] HTTPS: Ein CA-Zertifikat wird ausgeliefert (`exp:velocity ssl show`), nicht der selbstsignierte Ersatz;
      Erneuerung geplant.
- [ ] `HSTSMaxAge` erst erhöht, nachdem jeder Name dauerhaft auf HTTPS läuft.
- [ ] Ein Systemdienst startet Velocity nach einem Neustart des Rechners.
- [ ] `[DeploySettings] PhpFpmService=disabled`, wenn Velocity die Website allein ausliefert;
      `exp:velocity deploy --dry-run` zeigt die erwarteten Schritte.
- [ ] Apache oder nginx: `.htaccess` aus `.htaccess_root` kopiert (oder der abgeleitete nginx-Block), Front-Controller
      und Asset-Pfade getestet, eine `.php` unterhalb des Wurzelverzeichnisses antwortet mit 404.
- [ ] `var/` und `settings/` beschreibbar für den einen Benutzer (oder die eine Gruppe), der PHP ausführt; keine
      Dateien in `var/`, die root gehören.
- [ ] Hinter einem Proxy: Dem Proxy wird vertraut (`TrustedProxies[]` bzw. für Velocity `Q.webserver.proxy.trusted`),
      er setzt `X-Forwarded-Proto` und hängt an `X-Forwarded-For` an oder setzt es; `ClientIpByCustomHTTPHeader` nur
      unter Apache oder nginx mit PHP-FPM gesetzt; die curl-Prüfung aus Abschnitt 8.8 besteht.

---

**Eine öffentliche Demo.** Eine Demo, deren Administratoranmeldung veröffentlicht ist, braucht die Umgebungsvariable `EXP_DEMO_MODE=1` im PHP jedes Servers, der sie ausliefert: `SetEnv EXP_DEMO_MODE 1` (Apache, LiteSpeed), `env[EXP_DEMO_MODE] = 1` (PHP-FPM-Pool), `fastcgi_param EXP_DEMO_MODE 1` (nginx) oder die Umgebung der Unit, die Velocity startet. Fertige Dateien: [Beispiele für den Demo-Modus](../../en/us/examples/demo-mode/README.md); Hintergrund: [Demo-Modus](../../en/us/bc/6.0/demo-mode.md).

## 8.10 Referenzen

In diesem Repository:

- [Unterstützte Betriebssysteme](../../en/us/help/os-support.md): die Betriebssysteme, Webserver und PHP-SAPIs, in Stufen
  eingeteilt (hier getestet, bekanntermaßen funktionsfähig, voraussichtlich funktionsfähig, derzeit nicht möglich)
- [Velocity-Engines](../../en/us/bc/6.0/velocity-engines.md): Engines, `deploy`, `Instances`, FrankenPHP, Logs, die Dateien,
  die jede Engine ausliefert, die `/Q/`-Ansichten
- [Velocity: Exponential in einem Webserver mit persistenten Workern betreiben](../../en/us/features/6.0/velocity-persistent-worker-server.md)
- [Velocity-Verzeichnisstruktur auf der Platte (im Stil von Debians Apache)](../../en/us/bc/6.0/velocity-ondisk-layout.md)
- [Velocity: HTTPS und Zertifikate](../../en/us/features/6.0/velocity-https-certificates.md)
- [Velocity-Antwort-Cache](../../en/us/features/6.0/velocity-response-cache.md),
  [Control Panel](../../en/us/features/6.0/velocity-control-panel.md), [Webserver](../../en/us/features/6.0/velocity-web-server.md),
  [Pakete und Binärdateien](../../en/us/features/6.0/velocity-packages-and-binaries.md)
- Spezifikationen: [Worker-Pool](../../en/us/specifications/6.0/velocity-worker-pool.md),
  [Engine-Einstellungen](../../en/us/specifications/6.0/velocity-engine-settings.md),
  [HTTP/2 und Sicherheit](../../en/us/specifications/6.0/velocity-http2-and-security.md),
  [Sicherheitsvoreinstellungen](../../en/us/specifications/6.0/security-defaults-2026-09.md)
- [Upgrade-Hinweise zur Velocity-Engine](../../en/us/bc/6.0/velocity-engine-upgrade-notes.md), [FrankenPHP](../../en/us/bc/6.0/frankenphp.md),
  [Engine-Archiv (phar)](../../en/us/bc/6.0/phar.md)
- [Anleitung zur Bereitstellung](../../en/us/guides/deploying.md)
- [Weitergeleiteten Headern wird nur von konfigurierten Proxys vertraut](../../en/us/bc/6.0/trusted-proxies.md)
  (`TrustedProxies[]`, Beispiele für Apache, nginx, Velocity, Load Balancer und CDNs)
- [Voraussetzungen](02-voraussetzungen.md) (PHP-Versionen je Server) und [Sicherheitshärtung](13-sicherheitshaertung.md)
- Code: `kernel/classes/expvelocity.php`, `expvelocitydeploy.php`, `expvelocityconfiglayout.php`,
  `expvelocityfrankenphp.php`, `kernel/private/classes/commands/velocity.php`, `lib/ezutils/classes/ezsys.php`,
  `lib/ezutils/classes/eztrustedproxy.php`; im Engine-Paket `src/Q/WebServer/Proxy.php` (vertrauenswürdige Proxys,
  weitergeleitetes Protokoll) und `src/Q/WebServer/RunAs.php` (Worker-Benutzer)
- Die Einstellungen: [`settings/velocity.ini`](../../../settings/velocity.ini); die Rewrite-Regeln:
  [`.htaccess_root`](../../../.htaccess_root), [`.htaccess_root_static`](../../../.htaccess_root_static); der
  PHP-Router der Entwicklungs-Engine: [`bin/php/velocity-router.php`](../../../bin/php/velocity-router.php)
- Die frühere einseitige Anleitung: [doc/en/us/INSTALL.md, Abschnitt 8](../../en/us/INSTALL.md#8-serve-the-site-web-server-https-and-permissions)

Extern:

- Exponential-Velocity-Engine: <https://github.com/se7enxweb/exponential-velocity> (ihre `docs/https.md`,
  `docs/layout.md`, `docs/workers.md`, `docs/requirements.md`, `docs/dashboard.md`, `docs/panel.md`, `service/` und
  `packaging/systemd/`); die Änderung am weitergeleiteten Protokoll ist Commit
  [380a64d](https://github.com/se7enxweb/exponential-velocity/commit/380a64d6dc9a55c047b7d850556fcb17352a5abc),
  veröffentlicht in [v0.0.4.44](https://github.com/se7enxweb/exponential-velocity/blob/v0.0.4.44/CHANGELOG.md)
- Apache HTTP Server: [mod_rewrite](https://httpd.apache.org/docs/2.4/mod/mod_rewrite.html),
  [mod_proxy_fcgi](https://httpd.apache.org/docs/2.4/mod/mod_proxy_fcgi.html),
  [mod_ssl](https://httpd.apache.org/docs/2.4/mod/mod_ssl.html),
  [mod_proxy](https://httpd.apache.org/docs/2.4/mod/mod_proxy.html),
  [mod_headers](https://httpd.apache.org/docs/2.4/mod/mod_headers.html),
  [mod_http2](https://httpd.apache.org/docs/2.4/mod/mod_http2.html),
  [mod_remoteip](https://httpd.apache.org/docs/2.4/mod/mod_remoteip.html),
  [AllowOverride](https://httpd.apache.org/docs/2.4/mod/core.html#allowoverride)
- nginx: [location](https://nginx.org/en/docs/http/ngx_http_core_module.html#location),
  [try_files](https://nginx.org/en/docs/http/ngx_http_core_module.html#try_files),
  [fastcgi-Modul](https://nginx.org/en/docs/http/ngx_http_fastcgi_module.html),
  [proxy-Modul](https://nginx.org/en/docs/http/ngx_http_proxy_module.html),
  [ssl-Modul](https://nginx.org/en/docs/http/ngx_http_ssl_module.html),
  [realip-Modul](https://nginx.org/en/docs/http/ngx_http_realip_module.html),
  [client_max_body_size](https://nginx.org/en/docs/http/ngx_http_core_module.html#client_max_body_size)
- PHP: [FastCGI Process Manager (FPM)](https://www.php.net/manual/en/install.fpm.php),
  [FPM-Konfiguration](https://www.php.net/manual/en/install.fpm.configuration.php),
  [eingebauter Webserver](https://www.php.net/manual/en/features.commandline.webserver.php),
  [OPcache](https://www.php.net/manual/en/book.opcache.php)
- FrankenPHP: <https://frankenphp.dev/> und [seine Dokumentation](https://frankenphp.dev/docs/); Caddy:
  <https://caddyserver.com/docs/>
- Let's Encrypt: [Wie es funktioniert](https://letsencrypt.org/how-it-works/),
  [Challenge-Typen](https://letsencrypt.org/docs/challenge-types/),
  [Staging-Umgebung](https://letsencrypt.org/docs/staging-environment/),
  [Ratenlimits](https://letsencrypt.org/docs/rate-limits/); ACME: [RFC 8555](https://www.rfc-editor.org/rfc/rfc8555.html)
- systemd: [systemd.service](https://www.freedesktop.org/software/systemd/man/latest/systemd.service.html)
- Die Apache-Verzeichnisstruktur von Debian: [README.Debian von apache2](https://salsa.debian.org/apache-team/apache2/-/blob/master/debian/apache2.README.Debian)
- HSTS: [RFC 6797](https://www.rfc-editor.org/rfc/rfc6797.html), [hstspreload.org](https://hstspreload.org/)

[Inhalt](README.md) · Zurück: [7. Die Installation über die Konsole](07-installation-ueber-die-konsole.md) · Weiter: [9. Datenbanken](09-datenbanken.md)
