# Velocity-Webserver

Diese Seite richtet sich an Administratoren, die Exponential schneller und mit weniger beweglichen Teilen ausliefern
möchten, und an alle, die entscheiden, ob sie Velocity einsetzen. Velocity ist ein in PHP geschriebener Webserver, der
Ihre Exponential-Installation (oder jede andere PHP-Anwendung) aus einem Pool langlebiger PHP-Prozesse betreibt. Er
beantwortet HTTP/1.1, HTTP/2 über TLS und WebSockets selbst, liefert statische Dateien aus, speichert Seiten in seinem
eigenen Prozess zwischen und hält die Anwendung zwischen den Anfragen geladen. Sie brauchen weder Apache, nginx noch
PHP-FPM davor, er funktioniert aber auch dahinter. Gilt für Exponential 6.0.x mit Exponential Velocity 0.0.4.x.

Die Engine stammt vom Qbix-Webserver und wird als Composer-Paket `se7enxweb/exponential-velocity` ausgeliefert (früher
`se7enxweb/qbix-webserver`). Exponential steuert sie mit `exp:velocity`; die eigenen Programme der Engine sind
`qbixserver`, `qbixctl` und `qbixconsole`.

## Teil von Exponential ab 6.0.15

Ab Exponential 6.0.15 ist der Velocity Internet / Web Application Server Teil von Exponential. Die Root-`composer.json`
verlangt `se7enxweb/exponential-velocity ~0.0.4.57` (vorher war es ein Vorschlag), sodass jede mit `composer install`
angelegte Installation den Server hat, unter PHP 8.0 und neuer, das mitgelieferte PHP 8.0 von Red Hat Enterprise
Linux 9 eingeschlossen. Er läuft neben Apache mit PHP-FPM, nginx und FrankenPHP: Sein eigener Server ist die empfohlene
Engine für jede Stufe, FrankenPHP die produktionsreife Alternative, und die herkömmlichen Server arbeiten weiter wie
bisher.

Das ist ein Wendepunkt für das Projekt, ein Grundstein und ein Schlussstein: eine neue Stufe der Integration des
Anwendungssystems, in der das Kernprodukt standardmäßig noch mehr Funktionen von Haus aus mitbringt. Mit dem Server im
Lieferumfang ist Exponential ein funktionierender Stack (Server, Plattform für schnelle Anwendungsentwicklung, CMS,
Digital Experience und E-Commerce) statt eines Frameworks, das man erst vervollständigen muss. Die ganze Geschichte, für
jede Leserin und jeden Leser:
[Velocity ist Teil von Exponential 6.0.15](../../hilfe/velocity-ist-teil-von-exponential-6-0-15.md). Alle anderen
Funktionsseiten: [Funktionen von Exponential 6.0](README.md).

Die Befehle der täglichen Arbeit sind `./console exp:velocity deploy`, `start`, `restart` und `cache clear`.

## In zwei Minuten starten

Verwenden Sie in einer Exponential-Installation den Konsolenbefehl (alle Verben und `velocity.ini` finden Sie unter
[Velocity-Engines](../../bc/6.0/velocity-engines.md)):

```bash
./bin/php/console exp:velocity status --allow-root-user
./bin/php/console exp:velocity start --engine=qbix --allow-root-user
```

Ohne Exponential, aus dem eigenen Verzeichnisbaum der Engine:

```bash
mkdir -p web && echo '<?php echo "Hello!";' > web/index.php
php sbin/qbixserver.php --root=web --port=8080
```

Öffnen Sie `http://localhost:8080/` (die Seite gibt `Hello!` aus) und `http://localhost:8080/Q/dashboard`. Beenden Sie
den Server mit `php sbin/qbixserver.php --stop` (nutzt die PID-Datei) oder `qbixctl stop`.

## Wozu Sie ihn einsetzen

- **Geschwindigkeit ohne Stack.** Ein einziger PHP-Prozessbaum beantwortet alles. Zwischengespeicherte öffentliche
  Seiten werden im Serverprozess beantwortet, bevor überhaupt ein Worker gewählt wird; ein Treffer kostet daher etwa
  0,2 bis 0,4 ms CPU-Zeit. Gemessen auf einem Host mit 12 Kernen mit einer Exponential-Startseite über TLS und gzip:
  etwa 2.950 zwischengespeicherte Seiten pro Sekunde von einem Server, etwa 12.000 von vier Servern auf einem Port
  ([Velocity-Engines](../../bc/6.0/velocity-engines.md)).
- **Persistente Worker.** Die Anwendung wird einmal in einem Elternprozess geladen. Die Worker werden davon geforkt,
  teilen sich seinen Speicher und werden zwischen den Anfragen in ihren geforkten Zustand zurückversetzt. Siehe
  [Worker-Pool](../../spezifikationen/6.0/velocity-worker-pool.md).
- **Alles an einem Ort.** HTTPS ([Zertifikate](velocity-https-zertifikate.md)), ein
  [Kontrollpanel und Dashboard](velocity-control-panel.md), eine [Shell](velocity-q-shell.md), ein
  [Antwort-Cache](velocity-antwort-cache.md), Logs und Metriken.
- **Geringer Ressourcenbedarf.** Ein Worker eines vollständigen CMS belegt etwa 10 MB privaten Speicher (gemessen);
  der dynamische Pool beendet untätige Worker.

## Die am häufigsten genutzten Optionen

| Option | Bedeutung |
|---|---|
| `--root=DIR` | Dokumentwurzel (Standard `./web`) |
| `--app=DIR` | Ein Verzeichnis einer Qbix-Anwendung |
| `--host=IP`, `--port=PORT`, `--https-port=PORT` | Wo der Server lauscht (Standard `0.0.0.0`, `80`, `443`) |
| `--socket=PATH`, `--socket-mode=MODE` | Auf einem Unix-Socket lauschen (Modus standardmäßig `0660`) |
| `--workers=N` | Poolgröße; Standard ist, was in den Arbeitsspeicher passt, höchstens 8 je Kern und 64, nie weniger als 4 |
| `--config=FILE`, `--conf-dir=DIR` | Eine JSON-Site-Datei; ein Konfigurationsverzeichnis, aufgebaut wie `/etc/apache2` |
| `--preset=NAME` | Framework-Voreinstellung: `laravel`, `symfony`, `wordpress`, `drupal`, `exponential` |
| `--user=NAME`, `--group=NAME` | Unter welchem Benutzer die Worker laufen, wenn der Server als root startet |
| `--hotreload` | Dateien überwachen und bei Änderungen neu starten |
| `-t` | Die Konfiguration prüfen und beenden |
| `--stop`, `--reload` | Geordnetes Beenden, Neustart per re-exec (über die PID-Datei) |
| `--version`, `--about`, `--copyright` | Release, Build, Lizenz |

Werte können als `--name=V`, `--name V`, `-name=V` oder `-name V` geschrieben werden; Schalter lassen sich mit
`--no-name` abschalten.

## Die Voreinstellung `exponential`

`--preset=exponential` betreibt eine Exponential-Installation mit einer einzigen Einstellung unter dem Server. Sie lässt
die Quelltexttransformation eingeschaltet (der Legacy-Kernel ruft `header()` und `setcookie()` auf eine Weise auf, die
von der SAPI abhängt) und erhält die Typ-Registries über Anfragen hinweg; ein modernes Framework braucht beides nicht.
Ohne konfigurierte Listen liefert die Voreinstellung außerdem die Listen der Skripte, Front-Controller und statischen
Pfade, die Exponential mitbringt, sodass nur Einstiegspunkte ausgeführt und nur aufgeführte Dateien unverändert
ausgeliefert werden. Siehe [Engine-Einstellungen](../../spezifikationen/6.0/velocity-engine-einstellungen.md).

## Was er ausliefert

| Anfrage | Was geschieht |
|---|---|
| Eine statische Datei | Wird direkt gesendet, mit `ETag`, `Last-Modified`, Bereichsanfragen, Beachtung von `Accept-Encoding` (einschließlich `q=0`), optionaler Vorkomprimierung und einem Dateicache im Arbeitsspeicher, der die am längsten nicht genutzte Datei verdrängt ([statische Dateien und Bilder](velocity-statische-dateien-und-bilder.md)) |
| Ein PHP-Skript | Läuft in einem Worker des Pools; ist `Q.webserver.scripts` gesetzt, werden nur die dort aufgeführten Skripte über ihren Namen ausgeführt, alles andere geht an den Front-Controller |
| Ein Verzeichnis | Verzeichnislisten und Bildergalerien; die Dokumentwurzel lässt sich wie jedes andere Verzeichnis auflisten |
| Eine WebSocket- oder socket.io-Verbindung | Wird in einem langlebigen Prozess offen gehalten (`/socket.io`, `/Q/socket.js`) |
| Ein Server-Sent-Events-Stream | Unterstützt (hinzugefügt im August 2026) |
| `/Q/dashboard`, `/Q/panel`, `/Q/health`, `/Q/metrics`, `/Q/phpinfo` | Die eigenen Ansichten des Servers; siehe [Kontrollpanel](velocity-control-panel.md) |

Der Server liefert niemals eine Datei außerhalb der Dokumentwurzel aus und führt auch keine aus, selbst nicht über einen
symbolischen Link ([HTTP/2 und Sicherheit](../../spezifikationen/6.0/velocity-http2-und-sicherheit.md)).

## Bestehende Anwendungen betreiben

Eine Kompatibilitätsschicht lässt Laravel, Symfony, WordPress, Drupal, Joomla und Exponential unverändert laufen. Sie
schreibt die wenigen PHP-Funktionen um, die in einem langlebigen CLI-Prozess nicht funktionieren (`header()`,
`setcookie()`, `exit`, `die`, `session_id()`; insgesamt werden 44 Funktionen ersetzt), kapselt die Dateischicht, sodass
Tausende `include`- und `stat`-Aufrufe je Anfrage günstig sind, und setzt zurück, was eine Anfrage hinterlässt.
Einzelheiten und Messungen: [Worker-Pool](../../spezifikationen/6.0/velocity-worker-pool.md).

Das Einzige, was Sie sich merken müssen: **Halten Sie keinen anfragebezogenen Zustand in eigenen statischen Variablen
oder Globals, ohne es dem Server mitzuteilen.** Globals, die erhalten bleiben müssen, werden in
`Q.webserver.keepGlobals` (oder `--keep-globals=A,B`) benannt.

## Umstieg von Apache, nginx oder Caddy

Die Engine bringt für jeden davon eine Migrationsseite mit. Die wichtigsten Schritte:

1. Ersetzen Sie die Rewrite-Regeln aus `.htaccess` durch `Q.webserver.frontControllers` (Pfadmuster zu Skript) und
   `Q.webserver.scripts` / `Q.web.static.paths` (was ausgeführt und was als Datei ausgeliefert werden darf).
2. Tragen Sie TLS in `Q.web.https` ein ([HTTPS und Zertifikate](velocity-https-zertifikate.md)).
3. Setzen Sie `--user` und `--group`, damit die Worker nicht als root laufen.
4. Behalten Sie Apache oder nginx nur davor, wenn Sie es brauchen. Hinter einem Load Balancer, der TLS terminiert,
   erhält der Seiten-Cache der Anwendung die Anfrage-Header und den Port des Listeners (Release 0.0.4.35), sodass er
   Seiten findet, die für den öffentlichen Hostnamen gespeichert wurden.

## Grenzen

- PHP 8.0 oder neuer (ab Release 0.0.4.54; bis dahin 8.1); die statischen Binärdateien und Images werden für 8.2, 8.3,
  8.4 und 8.5 gebaut. Unter PHP 8.0 stehen die AVIF-Bildausgabe (benötigt 8.1) und der Speicherhöchstwert je Anfrage im
  Dashboard (benötigt 8.2) nicht zur Verfügung, und `$_FILES` hat kein `full_path`; das mitgelieferte PHP 8.0 von
  RHEL 9 führt die Engine aus. Einzelheiten:
  [Pakete und Binärdateien](velocity-pakete-und-binaerdateien.md#php-80-und-red-hat-enterprise-linux) und die Seite
  [PHP 8.0 and Red Hat Enterprise Linux](https://github.com/se7enxweb/exponential-velocity/blob/main/docs/rhel.md) der
  Engine.
- Windows kennt kein `fork()`, daher gibt es dort keinen Pool geforkter Worker; verwenden Sie die Binär-Builds für
  Windows ([Pakete und Binärdateien](velocity-pakete-und-binaerdateien.md)).
- Die Standard-Ereignisschleife (`stream_select`) begrenzt einen Pool auf knapp unter 1.000 Worker. Setzen Sie lieber
  mehr Server auf einem Port ein (`Q.webserver.reusePort` oder `Instances` in `velocity.ini`) als mehr Worker.
- Ein POST wird nie wiederholt: Stirbt ein Worker mitten in der Anfrage, erhält der Besucher einen `502`.

## Verwandte Seiten

- [Velocity: Exponential in einem Webserver mit persistenten Workern betreiben](velocity-server-mit-dauerhaften-workern.md)
- Funktionen: [Scheduler](velocity-zeitplaner.md), [statische Dateien und Bilder](velocity-statische-dateien-und-bilder.md), [WebSockets, Events und Räume](velocity-websockets-und-ereignisse.md), [Discovery, Föderation und Deployment](velocity-erkennung-foederation-und-deployment.md), [Pakete und Binärdateien](velocity-pakete-und-binaerdateien.md), [uwebserver](velocity-uwebserver.md)
- Referenz: [Worker-Pool](../../spezifikationen/6.0/velocity-worker-pool.md), [HTTP/2 und Sicherheit](../../spezifikationen/6.0/velocity-http2-und-sicherheit.md), [Engine-Einstellungen](../../spezifikationen/6.0/velocity-engine-einstellungen.md) (Logging, Branding)
- Aktualisierung und Betrieb: [Velocity-Engines](../../bc/6.0/velocity-engines.md) (eine Engine wählen und betreiben, eine PHP-Änderung bereitstellen), [Velocity-Verzeichnisstruktur auf dem Datenträger](../../bc/6.0/velocity-verzeichnisstruktur.md) (der Baum `/etc/vc`), [Hinweise zur Aktualisierung der Engine](../../bc/6.0/velocity-engine-upgrade-hinweise.md)
- [Changelog: Engine Exponential Velocity](../../../changelogs/extensions/exponential-velocity.md)
- Historie: [Velocity-Chronik](../../geschichte/velocity/README.md), [Juli](../../geschichte/velocity/2026-07.md), [August](../../geschichte/velocity/2026-08.md), [September](../../geschichte/velocity/2026-09a.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
