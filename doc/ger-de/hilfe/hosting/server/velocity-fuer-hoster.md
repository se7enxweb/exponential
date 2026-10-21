---
title: Velocity für Hoster
audience: hosting, admin
category: hosting
order: 80
summary: Wie ein Hosting-Unternehmen Exponential Velocity, die empfohlene Engine, installiert, konfiguriert, betreibt und unterstützt - Anforderungen, Installation, Ports und Worker-Benutzer, ein Dienst beim Systemstart, der Watchdog, Ausrollen, Logs, das Dashboard und das Kontrollzentrum, und eine Velocity pro Kunde.
keywords: Velocity, Exponential Velocity, qbix, exp:velocity, velocity.ini, Worker, persistente Worker, systemd, Watchdog, Ausrollen, graceful, status, Dashboard, Kontrollzentrum, /Q/health, /Q/metrics, Ports, Worker-Benutzer, Hosting, workers, persistent workers, watchdog, deploy, dashboard, control panel, ports, worker user, hosting
featured: true
type: help
---
# Velocity für Hoster

Diese Seite ist für Systemadministratoren und Support-Mitarbeitende bei Hosting-Unternehmen, die Exponential Velocity für Kunden-Websites
betreiben. Sie beantwortet: was Velocity von einem Server braucht, wie Sie es installieren und konfigurieren, wie es Neustarts und Abstürze
übersteht, wie eine Änderung es erreicht, wohin es protokolliert, wie Sie es beobachten und wie Sie jedem Kunden eine eigene Instanz
geben. Die Quellen sind [Kapitel 8](../../../installation/08-die-website-ausliefern.md#83-exponential-velocity-der-empfohlene-weg) und
[Velocity-Engines](../../../../eng-us/bc/6.0/velocity-engines.md); jeder Befehl unten wird aus dem Wurzelverzeichnis der Installation ausgeführt.

## Inhalt

1. [Was Velocity für einen Hoster ist](#was-velocity-für-einen-hoster-ist)
2. [Anforderungen](#anforderungen)
3. [Installation und erster Start](#installation-und-erster-start)
4. [Einstellungen und wie Sie sie ändern](#einstellungen-und-wie-sie-sie-ändern)
5. [Ports, Adresse und der Worker-Benutzer](#ports-adresse-und-der-worker-benutzer)
6. [Am Laufen halten: Dienst beim Systemstart und Watchdog](#am-laufen-halten-dienst-beim-systemstart-und-watchdog)
7. [Eine Änderung ausrollen](#eine-änderung-ausrollen)
8. [Logs, Status und Überwachung](#logs-status-und-überwachung)
9. [Das Dashboard und das Kontrollzentrum](#das-dashboard-und-das-kontrollzentrum)
10. [Eine Velocity pro Kunde](#eine-velocity-pro-kunde)
11. [Checkliste](#checkliste)

## Was Velocity für einen Hoster ist

Velocity ist ein für Exponential geschriebener Anwendungsserver. Ein Elternprozess lädt die Anwendung einmal und forkt **Worker**, die eine
Anfrage nach der anderen beantworten; zwischen den Anfragen wird jeder Worker auf den Zustand zurückgesetzt, den er beim Fork hatte. Der
Elternprozess nimmt jede Verbindung an, beantwortet statische Dateien, Treffer im Antwort-Cache und seine eigenen Seiten ohne Worker und
beendet TLS, sodass eine Velocity-Website kein Apache, nginx, PHP-FPM und keinen eigenen TLS-Terminator braucht
([Kapitel 8, Abschnitt 8.3](../../../installation/08-die-website-ausliefern.md#83-exponential-velocity-der-empfohlene-weg)).

Was das für Ihren Betrieb heißt:

| | Unter Velocity | Unter Apache/nginx mit PHP-FPM |
|---|---|---|
| Am Leben zu haltender Prozess | ein langlebiger Server pro Website | der Webserver und der FPM-Master |
| Eine geänderte Klasse oder INI-Datei | erreicht die Worker erst nach einem Neustart (`exp:velocity deploy`) | erreicht PHP, nachdem OPcache neu prüft, oder nach einem Neuladen von FPM |
| TLS | Velocity selbst, einschließlich ACME | `mod_ssl` oder nginx, plus certbot oder das Panel |
| Wiederholte anonyme Seiten | vom Elternprozess aus dem Antwort-Cache beantwortet | gerendert oder aus den Caches des Kernels ausgeliefert |
| Zu beurteilender Speicher | PSS von Elternprozess und Workern, nie RSS | die Prozesse des Pools |

Der Befehl ist `exp:velocity` (auch `exp:vc` oder `php bin/php/velocity.php`). Er steuert drei Engines mit denselben Verben: `qbix` (der
eigene Server von Velocity, Gegenstand dieser Seite), `frankenphp` ([FrankenPHP](frankenphp.md)) und `php` (der eingebaute
Entwicklungsserver von PHP).

## Anforderungen

| Bedarf | Detail |
|---|---|
| PHP | 8.0 oder neuer; das Engine-Paket verlangt es (`"php": ">=8.0"` ab dem Release 0.0.4.54, davor `>=8.1`); das Standard-PHP 8.0 von RHEL 9 führt es aus ([Kapitel 2](../../../installation/02-voraussetzungen.md#php-80-und-red-hat-enterprise-linux)) |
| PHP-Erweiterungen | `sockets` (vom Paket verlangt), `pcntl` und `posix` (Worker forken, ihren Benutzer wechseln), `openssl` (HTTPS) |
| Ein langlebiger Prozess | vom Tarif erlaubt; Shared Hosting erlaubt meist keinen |
| Root oder hohe Ports | Ports unter 1024 brauchen root; als root gestartet, behält nur der Elternprozess es |
| Betriebssystem | Linux und die BSDs für alles; unter Windows sind Worker Unterprozesse `php-cgi` ([Unterstützung von Betriebssystemen](../../betriebssystemunterstuetzung.md#windows)) |

Beim Start weigert sich die Engine, ohne Prozesstrennung (`pcntl` oder eine php-cgi-Binärdatei) und ohne `openssl` zu laufen, wenn HTTPS
konfiguriert ist; für den Rest warnt sie einmal. Fragen Sie die Maschine, was ihr fehlt:

```bash
php bin/php/console exp:velocity ext check
php bin/php/console exp:velocity ext install-hint <extension>    # the install command for this operating system
```

Auf Red Hat Enterprise Linux 9 und seinen Nachbauten ist das Standard-PHP 8.0: Schalten Sie zuerst einen neueren Modul-Stream ein
([Kapitel 2, Abschnitt 2.9](../../../installation/02-voraussetzungen.md#29-hinweise-zu-betriebssystemen)).

## Installation und erster Start

```bash
composer require se7enxweb/exponential-velocity:~0.0.4.44
php bin/php/console exp:velocity config set ServerSettings Engine qbix
php bin/php/console exp:velocity start
php bin/php/console exp:velocity status
curl -sI http://127.0.0.1:8088/ | head -1
```

- Das Paket landet in `vendor/se7enxweb/exponential-velocity`. Die Tilde-Bedingung akzeptiert spätere Releases `0.0.4.x`; 0.0.4.44 ist die
  Version, die die `composer.json` von Exponential vorschlägt, und die erste, die weitergeleitete Protokoll-Header nur von
  vertrauenswürdigen Proxys liest ([Kapitel 8, Abschnitt 8.8](../../../installation/08-die-website-ausliefern.md#88-hinter-einem-reverse-proxy)).
- `settings/velocity.ini` wird mit `Engine=php` ausgeliefert, weil der eingebaute Server von PHP immer funktioniert; jede echte Website
  setzt `qbix`.
- `start` schreibt die Konfiguration, startet den Server im Hintergrund, wartet, bis der Port antwortet, und gibt den Status aus.
- Die Engine gibt es auch als deb- und rpm-Pakete und Docker-Images
  ([Pakete und Binärdateien von Velocity](../../../../eng-us/features/6.0/velocity-packages-and-binaries.md)), aber für eine Installation von
  Exponential ist das Composer-Paket das, was `exp:velocity` steuert.

Was beim ersten Start schiefgehen kann (Kapitel 8): kein Serverskript (Paket nicht installiert), der Port ist schon belegt
(`exp:velocity status --all`), keine Antwort von einer anderen Maschine (`Host` ist noch `127.0.0.1`), eine Warnung, dass Worker als root
laufen (kein Worker-Benutzer und ein Document Root, der root gehört), oder eine Wartungsseite, die ein Installationslauf eingeschaltet
gelassen hat (`php bin/php/maintenance.php status`, dann `off`).

## Einstellungen und wie Sie sie ändern

| Datei | Was sie ist |
|---|---|
| `settings/velocity.ini` | die mitgelieferten Standards, jede Einstellung kommentiert; nie bearbeiten |
| `settings/override/velocity.ini.append.php` | die Werte dieser Installation, von `exp:velocity config set` geschrieben |
| `/etc/vc/...` (oder `var/vc/qbix/etc/` ohne root) | der Konfigurationsbaum der Engine, bei jedem Start aus den zwei Dateien oben erzeugt |

```bash
php bin/php/console exp:velocity config list                    # every setting; * marks the ones you set
php bin/php/console exp:velocity config get ServerSettings Workers
php bin/php/console exp:velocity config set ServerSettings Workers 16
php bin/php/console exp:velocity config unset ServerSettings Workers
php bin/php/console exp:velocity config paths
```

Eine geänderte Einstellung erreicht den Server beim nächsten `restart` oder `graceful`. Der Baum `/etc/vc` ist wie `/etc/apache2` von
Debian aufgebaut (`vc.conf`, `ports.conf`, `envvars`, `conf-`, `mods-` und `sites-available` mit Symlinks in `-enabled`), mit
`exp:velocity site|conf|mod enable|disable <name>` als Familie von `a2ensite`
([Aufbau von Velocity auf der Festplatte](../../../../eng-us/bc/6.0/velocity-ondisk-layout.md)). Erzeugte Dateien tragen
`"_generated": "exp:velocity"`; entfernen Sie diese Markierung aus einer Datei, und sie wird nie wieder überschrieben.

## Ports, Adresse und der Worker-Benutzer

| Einstellung (`[ServerSettings]`) | Ausgeliefert | Bedeutung |
|---|---|---|
| `Host` | `127.0.0.1` | Bindeadresse; nur lokal, über einen Tunnel oder einen Proxy erreichbar |
| `Port` | `8088` | einfaches HTTP |
| `HTTPSPort` | `8080` | HTTPS, wenn HTTPS an ist |
| `User`, `Group` | leer | als wer die Worker laufen, wenn der Server als root gestartet wird |
| `AllowRootWorkers` | `disabled` | `root` als Worker-Benutzer wird verweigert, außer bei `enabled` |
| `FollowSymlinks` | `disabled` | ein symbolischer Link aus dem Document Root hinaus wird verweigert |
| `Nice` | `5` | die Priorität des Servers und seiner Worker, egal mit welcher der Aufrufer läuft |

Um Besuchern direkt auf den Standardports gegenüberzustehen:

```bash
php bin/php/console exp:velocity config set ServerSettings Host 0.0.0.0 --allow-root-user
php bin/php/console exp:velocity config set ServerSettings Port 80 --allow-root-user
php bin/php/console exp:velocity config set ServerSettings HTTPSPort 443 --allow-root-user
php bin/php/console exp:velocity config set ServerSettings User <customer> --allow-root-user
php bin/php/console exp:velocity config set ServerSettings Group <customer> --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user
```

Als root gestartet, behält der Elternprozess root (Ports, Zertifikat, Neuladen), und jeder Worker gibt es direkt nach dem Fork auf, bevor
irgendein Code der Anwendung läuft. Der Worker-Benutzer wird in dieser Reihenfolge bestimmt: `[ServerSettings] User` und `Group`; dann
`VC_RUN_USER` und `VC_RUN_GROUP` aus der Umgebung oder `/etc/vc/envvars`; dann der Eigentümer des Document Root. Ein Benutzer, der nicht
existiert, stoppt den Start. Prüfen Sie die Startzeile `Workers as: ...` in `var/vc/qbix/run/console.log`. HTTPS steht auf
[HTTPS-Zertifikate in Velocity](https-zertifikate-in-velocity.md); die Poolgröße auf [Velocity-Worker und Pools](velocity-worker-und-pools.md).

## Am Laufen halten: Dienst beim Systemstart und Watchdog

`exp:velocity` hat keinen Befehl, der einen **Dienst beim Systemstart** installiert. Zwei Wege
([Kapitel 8, Abschnitt 8.3.11](../../../installation/08-die-website-ausliefern.md#8311-velocity-als-dienst-betreiben)):

- die eigenen Units der Engine (`vendor/se7enxweb/exponential-velocity/service/qbixserver.service` und `exponential-velocity.service` des
  Betriebssystempakets), die die Engine direkt starten und deshalb umgehen, was `exp:velocity` hinzufügt (die erzeugte Site-Datei,
  `KeepGlobals`, `[PHPSettings] IniOptions[]`, den Worker-Benutzer, Instanzen);
- eine eigene Unit, die `exp:velocity start`, `stop` und `graceful` ausführt (`Type=forking`, `PIDFile=` die Datei
  `var/vc/qbix/run/server.pid` der Installation). Kapitel 8 gibt ein abgeleitetes Beispiel; es wird nicht ausgeliefert.

Der **Watchdog** ist ein zweites Sicherheitsnetz für einen Absturz, einen Abbruch wegen Speichermangels oder ein Stoppen durch ein anderes
Werkzeug. Er ist aus, bis er installiert wird (root, systemd), und er startet die Engine nur, wenn kein Prozess von ihr läuft, niemand sie
absichtlich gestoppt hat, kein Neustart im Gange ist und er nicht gerade zurückweicht. Er stoppt oder startet nie einen laufenden Server
neu ([Velocity-Engines, Der Watchdog](../../../../eng-us/bc/6.0/velocity-engines.md#the-watchdog-expvelocity-watchdog)):

```bash
php bin/php/console exp:velocity watchdog install --allow-root-user   # vc-watchdog.service + .timer
php bin/php/console exp:velocity watchdog status --allow-root-user
php bin/php/console exp:velocity watchdog remove --allow-root-user
```

| Einstellung (`[WatchdogSettings]`) | Ausgeliefert | Bedeutung |
|---|---|---|
| `Interval` | `45` | Sekunden zwischen Prüfungen (15 bis 3600) |
| `RestartTimeout` | `300` | wie lange eine Neustart-Markierung oder eine gehaltene Sperre ihn zurückhalten darf |
| `Backoff` | `600` | die Wartezeit nach drei gescheiterten Starts in Folge |
| `LockFile` | leer | eine Sperre, die „Ausrollen läuft, warten“ bedeutet |
| `UnitName` | `vc-watchdog` | geben Sie jeder Installation auf einem Host eine eigene |

Jede Prüfung, die mehr tut, als den Server laufend vorzufinden, schreibt eine Zeile nach `var/log/velocity-watchdog.log`, und ein Start
oder ein Zurückweichen wird im Prüfprotokoll als `system.velocity.watchdog.*` festgehalten.

## Eine Änderung ausrollen

Worker halten Klassen, INI-Werte und kompilierte Templates im Speicher. Ein Befehl bringt eine Website in der Reihenfolge auf den neuesten
Stand, die funktioniert ([Kapitel 8, Abschnitt 8.3.14](../../../installation/08-die-website-ausliefern.md#8314-eine-änderung-bereitstellen-expvelocity-deploy)):

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user   # what it would do
php bin/php/console exp:velocity deploy --allow-root-user             # the usual case
php bin/php/console exp:velocity deploy --kernel --allow-root-user    # a kernel class was added or renamed
```

Er erzeugt die Autoloads neu, leert die INI- und Template-Caches, baut das Engine-Archiv neu, wenn eines genutzt wird, lädt PHP-FPM neu,
wenn nötig, startet Velocity neu, leert die Caches der gerenderten Ausgabe und den Antwort-Cache und fragt die Seiten aus
`[DeploySettings] WarmUpURL[]` an. Jeder Schritt gibt `PASS`, `FAIL` oder `SKIP` aus; der erste Fehlschlag stoppt den Lauf. Setzen Sie
`[DeploySettings] PhpFpmService=disabled`, wenn Velocity die Website allein ausliefert. Für eine Änderung an einem Template oder Stylesheet
ist kein Neustart nötig: Leeren Sie die Template-Caches und führen Sie `exp:velocity cache clear` aus. `exp:velocity graceful` führt den
Server neu aus und behält dabei den lauschenden Socket, sodass keine Anfrage verloren geht.

## Logs, Status und Überwachung

| Was | Wo |
|---|---|
| Zugriffs- und Fehlerlogs | `var/vc/qbix/log/<siteaccess>-access.log`, `-error.log` (`[LogSettings] Dir`), Format combined plus Antwortzeit |
| Prozessausgabe und Startzeilen | `var/vc/qbix/run/console.log` |
| PID-Datei | `var/vc/qbix/run/server.pid` (`server.1.pid` ... bei `Instances` über 1) |
| Die eigenen Logs des Kernels | `var/<site>/log/`, wie unter jedem Server |

`[LogSettings] FileMode` und `DirMode` werden als `0640` und `0750` ausgeliefert: Ein Zugriffslog nennt Besucher. `exp:velocity status`
meldet `running` nur für einen Server, der eine HTTP-Anfrage beantwortet, und endet sonst mit einem Wert ungleich null; `status --json` ist
für die Überwachung gedacht. `/Q/health` antwortet jedem mit „ok“; vollständige Zahlen, `/Q/stats` und `/Q/metrics` für Prometheus
brauchen diese Maschine, das Dashboard-Token oder eine Sitzung im Kontrollzentrum. Siehe [Überwachung: Überblick](../betrieb/ueberblick-ueber-die-ueberwachung.md).

## Das Dashboard und das Kontrollzentrum

| Ansicht | Wer sie öffnen darf |
|---|---|
| `/Q/dashboard` (laufende Anfragen, Worker, Speicher) | diese Maschine; anderswo mit `[DashboardSettings] Token` |
| `/Q/phpinfo` | diese Maschine oder mit dem Token oder einer Sitzung im Kontrollzentrum; nie über `Remote=enabled` |
| `/Q/panel` (Domains, Zertifikate, Cache, Logs, Worker, Einstellungen) | diese Maschine, dann das Passwort des Kontrollzentrums |

Der erste Besuch von `/Q/panel` setzt sein Passwort (mindestens 16 Zeichen). Auf einer Maschine, die aus dem Internet erreichbar ist,
setzen Sie es sofort, und erreichen Sie ein entferntes Kontrollzentrum über einen SSH-Tunnel zum eigenen Port des Servers. Ein Token ist
eine beliebige lange zufällige Zeichenkette, zum Beispiel aus `openssl rand -hex 24`. Lassen Sie `[DashboardSettings] Remote` auf
`disabled`, es sei denn, eine Firewall beschränkt den Port bereits
([Kontrollzentrum von Velocity](../../../../eng-us/features/6.0/velocity-control-panel.md)).

Der Reiter **Domains** des Kontrollzentrums kann eine Domain auf `suspended` (antwortet `503` mit `Retry-After`) oder `disabled`
(antwortet `404`) setzen; die eigenen Pfade `/Q/` und `/.well-known/` des Servers funktionieren weiter, sodass Erneuerungen von
Zertifikaten weiterlaufen.

## Eine Velocity pro Kunde

Ein einzelner Server, der mehrere Websites ausliefert, ist laut `README.md` eine Bequemlichkeit beim Routing, keine Sicherheitsgrenze. Für
mehrere Kunden auf einer Maschine betreiben Sie eine Installation und eine Velocity pro Kunde:

- jede als eigener Benutzer des Kunden (`[ServerSettings] User` und `Group`), nie als der eines anderen Kunden;
- jede auf eigenen Ports (`Port`, `HTTPSPort`), mit `Host=127.0.0.1` hinter einem Proxy oder auf einer eigenen Adresse;
- jede mit eigener Unit für den Systemstart und eigenem `UnitName` des Watchdogs;
- dem Proxy wird von Velocity vertraut ([Velocity neben Apache oder nginx](velocity-neben-apache-oder-nginx.md)).

Siehe [Trennung von Mandanten](../sicherheit/trennung-von-mandanten.md).

## Checkliste

Aus [Kapitel 8, Checkliste](../../../installation/08-die-website-ausliefern.md#89-checkliste), für jede Kunden-Website:

- [ ] `Engine=qbix` gesetzt, `exp:velocity status` zeigt den Server laufend, die Website antwortet.
- [ ] `Host`, `Port`, `HTTPSPort` wie beabsichtigt; Firewall offen, wo Velocity Besuchern gegenübersteht.
- [ ] Worker laufen als Benutzer der Website (`Workers as: ...` in `console.log`), nie als root.
- [ ] HTTPS: Ein CA-Zertifikat wird ausgeliefert, nicht der selbstsignierte Ersatz; Erneuerung geplant.
- [ ] Ein Dienst beim Systemstart startet Velocity nach einem Neustart; der Watchdog installiert, wenn Sie ihn wollen.
- [ ] `[DeploySettings] PhpFpmService=disabled`, wenn Velocity die Website allein ausliefert; `deploy --dry-run` zeigt die erwarteten
      Schritte.
- [ ] Das Passwort des Kontrollzentrums gesetzt, `Remote` ausgeschaltet gelassen, ein Dashboard-Token in Ihrem Geheimnisspeicher abgelegt.
- [ ] `var/` und `settings/` für den einen Benutzer beschreibbar, der PHP ausführt; keine Dateien in `var/`, die root gehören.

**Demomodus unter Velocity.** Worker erben die Umgebung des Servers: Setzen Sie `EXP_DEMO_MODE=1` in die Unit, die Velocity startet ([`velocity-demo-mode.conf`](../../../../eng-us/examples/demo-mode/velocity-demo-mode.conf)), und starten Sie über sie neu. `/etc/vc/envvars` wird nicht an die Worker exportiert, und `exp:velocity restart` oder `deploy` aus einer Shell ohne die Variable schaltet den Demomodus aus. [Demomodus für Hoster](../sicherheit/demomodus-fuer-hoster.md).

## Verwandte Seiten

- [Velocity-Worker und Pools](velocity-worker-und-pools.md)
- [HTTPS-Zertifikate in Velocity](https-zertifikate-in-velocity.md)
- [Antwort-Cache von Velocity](../leistung/antwort-cache-von-velocity.md)
- [Velocity startet nicht](../runbooks/velocity-startet-nicht.md)
- [Velocity: Exponential in einem Webserver mit persistenten Workern betreiben](../../../../eng-us/features/6.0/velocity-persistent-worker-server.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
