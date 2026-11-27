---
title: Container: Docker und Podman
audience: hosting, admin
category: hosting
order: 60
summary: Exponential in Docker- oder Podman-Containern betreiben - die offiziellen Images (ghcr.io/se7enxweb/exponential, Apache mit PHP-FPM oder Velocity, amd64 und arm64), was die Quellen sagen (Stufe 2, die veröffentlichten Images von Velocity), was auf ein persistentes Volume gehört, die Regel eines einzigen Schreibers für SQLite, ein Entry Point, der einmal installiert und Velocity im Vordergrund hält, und Geheimnisse.
keywords: Container, Docker, Podman, offizielles Image, ghcr.io/se7enxweb/exponential, Compose, edge, Tags, Image, Volume, persistentes Volume, Entry Point, exp:install, Velocity, ghcr.io, SQLite, WAL, ein Schreiber, PostgreSQL, EXP_INSTALL_DB_PASSWORD, Geheimnisse, containers, image, volume, persistent volume, entry point, one writer, secrets
type: help
---
# Container: Docker und Podman

Diese Seite ist für Hosting-Anbieter und Plattform-Teams, die Exponential-Websites in Linux-Containern mit Docker oder
Podman betreiben. Sie beantwortet, was die Quellen des Projekts zu Containern sagen, was auf einem persistenten Volume
liegen muss, wie viele Container in eine SQLite-Datenbank schreiben dürfen, wie ein Entry Point eine Website einmal
installiert und den Server laufen lässt und wie Sie Geheimnisse aus Images heraushalten.

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../../beispiele/webserver/nginx.md), [LiteSpeed](../../../beispiele/webserver/litespeed.md), [Caddy](../../../beispiele/webserver/caddy.md), [IIS](../../../beispiele/webserver/iis.md),
> [Velocity](../../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../control-panels/plesk.md), [cPanel](../control-panels/cpanel.md), [DirectAdmin](../control-panels/directadmin.md), [ISPConfig](../control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../../anleitungen/seiten-cache-mit-litespeed.md).

## Inhalt

1. [Offizielle Images](#offizielle-images)
2. [Was die Quellen sagen](#was-die-quellen-sagen)
3. [Images](#images)
4. [Volumes: was erhalten bleiben muss](#volumes-was-erhalten-bleiben-muss)
5. [Die Datenbank und die Regel eines einzigen Schreibers](#die-datenbank-und-die-regel-eines-einzigen-schreibers)
6. [Ein Entry Point, der einmal installiert](#ein-entry-point-der-einmal-installiert)
7. [Geheimnisse](#geheimnisse)
8. [Betrieb in Containern](#betrieb-in-containern)
9. [Checkliste](#checkliste)

## Offizielle Images

Exponential veröffentlicht eigene Images in der GitHub Container Registry, `ghcr.io/se7enxweb/exponential`, für amd64
und arm64. Sie werden aus [`docker/`](../../../../../docker/Dockerfile) im Kernel-Repository auf den offiziellen
PHP-Images (Debian) gebaut, mit jeder PHP-Erweiterung, die Exponential und Velocity nutzen, und der mit
`composer install` erstellten Installation. Jedes Image:

- läuft als `www-data` (uid 33), nie als root; der Code gehört root und kann vom Server nicht geändert werden:
  `var/`, `settings/override`, `settings/siteaccess` und `autoload/` gehören `www-data`, und im Wurzelverzeichnis der
  Installation, in `design/`, `extension/` und `settings/` darf er eigene Dateien anlegen (Sticky-Bit), wie es der
  Installer braucht;
- lauscht auf Port **8080** (veröffentlichen Sie ihn nach Bedarf: `-p 80:8080`);
- installiert beim ersten Start mit `exp:install` eine Website aus der Umgebung und enthält keine Geheimnisse;
- beantwortet einen Health-Check (Apache: eine statische Datei und der PHP-FPM-Ping; Velocity: `/Q/health`).

### Tags

| Tag | Was es ist | Bewegt sich? |
|---|---|---|
| `<version>-php<php>-apache` | ein Release auf PHP `<php>`, ausgeliefert von Apache mit PHP-FPM | fest |
| `<version>-php<php>-velocity` | ein Release auf PHP `<php>`, ausgeliefert von Velocity (ab 6.0.15) | fest |
| `<version>`, `<version>-velocity` | ein Release auf dem Standard-PHP (8.5) | fest |
| `latest`, `latest-velocity` | das neueste Release auf dem Standard-PHP | mit jedem Release |
| `edge-php<php>-apache`, `edge-php<php>-velocity` | der Branch main, jede Nacht neu gebaut | immer |
| `edge`, `main`, `edge-velocity` | der Branch main auf dem Standard-PHP | immer |

`<version>` ist das Release ohne sein `v` (`6.0.14`). `<php>` ist 8.0 bis 8.5 ab 6.0.15 und 8.1 bis 8.5 für 6.0.14,
das PHP 8.1 braucht. PHP 8.5 ist der Standard, weil es das neueste ist; wählen Sie ein älteres nur, wenn eine Ihrer
Erweiterungen es braucht. Jeder Tag ist für mehrere Architekturen. Die Images mit PHP 8.0 werden auf Debian 11 gebaut,
der einzigen Basis des offiziellen PHP-8.0-Images; Debian 11 hat sein Lebensende erreicht, seine Pakete kommen von
archive.debian.org, daher erhalten weder PHP 8.0 noch das System Sicherheitsupdates. Nutzen Sie sie für
Kompatibilitätstests, nicht für eine öffentliche Website.

**Ein fester Tag ändert sich nie.** Die Images werden per Digest hochgeladen und erst getaggt, wenn jeder Build des
Laufs seine Tests bestanden hat; der Workflow hält an, bevor er einen vorhandenen festen Tag überschreiben würde. Ein
Image, das sich nach seinem Release als fehlerhaft erweist, wird nicht unter demselben Tag ersetzt: Der nächste Build
bringt die Korrektur, und seine Notizen sagen, was falsch war. Legen Sie in der Produktion einen festen Tag (oder
einen Digest) fest; nehmen Sie `edge`, um Kommendes auszuprobieren.

### Eine Website starten

Ein einzelner Container auf SQLite, ausgeliefert von Apache mit PHP-FPM:

```bash
docker run -d --name exponential -p 8080:8080 \
  -v exp-var:/var/www/exponential/var \
  -v exp-override:/var/www/exponential/settings/override \
  -v exp-siteaccess:/var/www/exponential/settings/siteaccess \
  -e EXP_SITE_URL=localhost:8080 -e EXP_ADMIN_EMAIL=you@example.com \
  ghcr.io/se7enxweb/exponential:edge
docker logs -f exponential                                  # die Installation, dann der Server
docker exec exponential cat var/log/initial-admin-password  # das Passwort des Administrators
```

Die Website antwortet unter `http://localhost:8080/`, die Administration unter `http://localhost:8080/admin`
(Benutzer `admin`). Podman nimmt denselben Befehl (`podman run ...`). Für Velocity nehmen Sie den `-velocity`-Tag
desselben Releases.

Mit einem Datenbankserver bringt das Kernel-Repository Compose-Dateien mit:
[`docker/compose.mariadb.yml`](../../../../../docker/compose.mariadb.yml) und
[`docker/compose.postgresql.yml`](../../../../../docker/compose.postgresql.yml) (die Website, ein Cron-Container und
die Datenbank, das Passwort als Secret-Datei) sowie [`docker/compose.sqlite.yml`](../../../../../docker/compose.sqlite.yml)
für den einzelnen Container:

```bash
printf '%s' "$(openssl rand -hex 24)" > docker/db_password.txt
docker compose -f docker/compose.postgresql.yml up -d
```

### Variablen

| Variable | Standard | Bedeutung |
|---|---|---|
| `EXP_SERVER` | `apache` (`velocity` in `-velocity`-Images) | der Server: Apache mit PHP-FPM oder Velocity |
| `EXP_INSTALL` | `auto` | `auto` installiert beim ersten Start, wenn `settings/override/site.ini.append.php` keinen Block `[DatabaseSettings]` hat; `never` nicht |
| `EXP_DB` | `sqlite` | `sqlite`, `mysql` (MariaDB und MySQL), `pgsql` und die anderen Werte von `exp:install --db` |
| `EXP_DB_HOST`, `EXP_DB_PORT`, `EXP_DB_NAME`, `EXP_DB_USER` | wie `exp:install` | der Datenbankserver |
| `EXP_DB_PASSWORD_FILE` | | eine Datei mit dem Datenbankpasswort (ein Docker- oder Podman-Secret); `EXP_DB_PASSWORD` geht auch, erscheint aber in `docker inspect` |
| `EXP_DB_ACTION` | `remove` für SQLite, `ignore` für einen Server | was `exp:install` mit einer vorhandenen Datenbank macht; eine Server-Datenbank wird nur geleert, wenn Sie `remove` setzen |
| `EXP_DB_WAIT` | `90` | Sekunden, in denen erneut versucht wird, solange der Datenbankserver startet |
| `EXP_SITE_URL`, `EXP_SITE_TITLE`, `EXP_LANGUAGE`, `EXP_TIMEZONE` | | Adresse, Titel, Hauptsprache und Zeitzone der Website |
| `EXP_ADMIN_EMAIL`, `EXP_ADMIN_PASSWORD_FILE` | | der Administrator; ohne Passwort wird eines erzeugt und in `var/log/initial-admin-password` geschrieben |
| `EXP_INSTALL_ARGS` | | weitere Optionen für `exp:install`, zum Beispiel `--package=... --languages=ger-DE` |
| `EXP_VELOCITY_WORKERS` | `4` | die Zahl der Worker von Velocity, beim ersten Start in `settings/override/velocity.ini.append.php` geschrieben |
| `EXP_CRON_INTERVAL` | `300` | Sekunden zwischen zwei Läufen von `runcronjobs.php` in einem Container mit dem Befehl `cron` |

Der Befehl bestimmt, was der Container tut: `serve` (der Standard), `install` (installieren und beenden), `cron` (die
Cronjobs nach Zeitplan ausführen, für einen zweiten Container auf denselben Volumes) oder ein beliebiger Befehl, zum
Beispiel `docker exec exponential bin/php/console exp:velocity ext check`.

Eigene PHP-Einstellungen binden Sie in `/usr/local/etc/php/conf.d/` ein (mit einem Namen nach `zz-exponential.ini`,
etwa `zzz-site.ini`). Der Code im Image ist nur lesbar; ein neues Image ist der Weg, ihn zu ändern.

### Releases vor 6.0.15

Die Images von 6.0.14 liefern das Release so aus, wie es veröffentlicht wurde: Es hat weder `exp:install` noch
Velocity, daher gibt es keinen `-velocity`-Tag, und ein neuer Container zeigt im Browser den Setup-Assistenten, der
in dieselben Volumes schreibt.

### Eigene Images bauen und testen

Aus einem Klon des Kernel-Repositorys:

```bash
docker build -f docker/Dockerfile -t exponential:local .                       # Apache mit PHP-FPM, PHP 8.5
docker build -f docker/Dockerfile --target velocity -t exponential:velocity .  # Velocity
docker build -f docker/Dockerfile --build-arg PHP_VERSION=8.3 -t exponential:php8.3 .
docker/test-image.sh exponential:local sqlite mysql pgsql                       # Installation, Startseite, Anmeldeseite
```

`PHP_VERSION` ist 8.0 bis 8.5 und `DEBIAN` das Basis-Release (`bookworm`; `bullseye` für 8.0). Der Build liest nie
`settings/override` oder `vendor/` aus Ihrem Arbeitsverzeichnis, daher lässt sich auch eine Arbeitskopie mit
Zugangsdaten gefahrlos bauen. Der Workflow `.github/workflows/docker.yml` baut genauso auf nativen amd64- und
arm64-Runnern, führt `docker/test-image.sh` aus und veröffentlicht.

## Was die Quellen sagen

- **Docker und Podman (Linux-Container) sind Stufe 2**, funktionieren bekanntermaßen: Velocity veröffentlicht Images
  unter `ghcr.io/se7enxweb/exponential-velocity` für amd64 und arm64, je eines pro PHP-Version und Variante
  ([Stufe 2](../../betriebssystemunterstuetzung.md#stufe-2-bekanntermaßen-lauffähig)).
- Jedes Image mit PHP 8 und den erforderlichen Erweiterungen kann Exponential ausführen. Halten Sie `var/` (Speicher,
  Logs, die SQLite-Datenbank) und `settings/override` auf einem persistenten Volume. Mit SQLite betreiben Sie **einen**
  Container, der schreibt ([Container, Cloud und Hosting-Panels](../../betriebssystemunterstuetzung.md#container-cloud-und-hosting-panels)).
- `exp:install` ist die Methode für CI-Pipelines, Container-Images und automatische Testumgebungen: Optionen oder
  Umgebungsvariablen, Exit-Status ([4.6 Unbeaufsichtigte Installationen](../../../installation/04-installationsweg-waehlen.md#46-unbeaufsichtigte-installationen-ci-und-container)).

## Images

Drei Wege:

| Weg | Was Sie bekommen | Worauf Sie achten |
|---|---|---|
| Das offizielle Image, `ghcr.io/se7enxweb/exponential` ([oben](#offizielle-images)) | eine getestete Installation, Apache mit PHP-FPM oder Velocity, beim ersten Start installiert | legen Sie einen festen Tag fest; eigene Erweiterungen kommen in ein Image, das darauf aufbaut (`FROM`) |
| Ihr eigenes Image auf einer Basis mit PHP 8.0+ | die PHP-Erweiterungen, die Sie wählen, Composer, die mit `composer install` gebaute Installation | installieren Sie jede erforderliche Erweiterung: `dom`, `xsl`, `intl`, `mbstring`, `iconv`, `zlib`, eine Datenbankerweiterung, `gd` oder ImageMagick und für Velocity `sockets`, `pcntl`, `openssl` |
| Das veröffentlichte Image von Velocity als Basis | den Server und ein PHP mit einer festgelegten Erweiterungsausstattung | prüfen Sie die Variante gegen das, was Exponential braucht |

Die Images von Velocity sind nach Release, PHP-Version und Variante getaggt
([Velocity-Pakete, Docker-Images und Binärdateien](../../../funktionen/6.0/velocity-pakete-und-binaerdateien.md#docker)):

```text
ghcr.io/se7enxweb/exponential-velocity:<release>-php<version>-<variant>   fixed
ghcr.io/se7enxweb/exponential-velocity:php<version>-<variant>             moves with each release
ghcr.io/se7enxweb/exponential-velocity:latest                             newest PHP, standard variant
```

`<version>` ist 8.2, 8.3, 8.4 oder 8.5; `<variant>` ist `mini`, `lite`, `standard` oder `full`. Schreiben Sie in der
Produktion einen festen Tag fest. Die Variante `standard` ist der Standard, von dem die Dokumentation ausgeht.

> **Tipp**
> Welche Basis auch immer: Führen Sie `php bin/php/console exp:velocity ext check` im gebauten Image als Teil Ihrer
> Image-Pipeline aus. Es listet jede Erweiterung auf, die die Installation will, markiert, was fehlt, und gibt den
> Installationsbefehl aus ([Herausfinden, was fehlt](../../../installation/02-voraussetzungen.md#herausfinden-was-fehlt)).

## Volumes: was erhalten bleiben muss

| Pfad | Enthält | Volume |
|---|---|---|
| `var/` | Uploads und Bildvarianten (`var/storage`), Caches, Logs, Autoload-Arrays, die SQLite-Datenbank (`var/storage/sqlite3/`) | persistent |
| `settings/override` | die Einstellungen der Installation, einschließlich der Datenbankverbindung | persistent |
| `settings/siteaccess` | die Einstellungen der Siteaccesses, die der Installer schreibt | persistent (die Backup-Liste nennt es) |
| `kernel/`, `lib/`, `vendor/`, `extension/`, `design/` | der Code | im Image |

Der Backup-Satz ist derselbe wie überall: die Datenbank und `var/storage`, `settings/override` und `settings/siteaccess`
([Backup und Wiederherstellung](../../datenbankunterstuetzung.md#backup-und-wiederherstellung)).

> **Warnung**
> Dateien auf dem Volume müssen dem Benutzer gehören, als der das PHP des Containers läuft. Dateien, die `root` anlegt,
> sind für den Webserver später vielleicht nicht beschreibbar ([4.6](../../../installation/04-installationsweg-waehlen.md#46-unbeaufsichtigte-installationen-ci-und-container)).
> Legen Sie die Benutzer-ID einmal für Image und Volume fest, und behalten Sie sie bei.

## Die Datenbank und die Regel eines einzigen Schreibers

In der offiziellen Reihenfolge: 1. SQLite 3 (der Standard) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle ·
6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlicher Arbeit möglich.

- **1. SQLite 3** funktioniert in einem Container, wenn **ein** Container in die Datenbank schreibt: Das WAL von SQLite
  braucht gemeinsamen Speicher auf einem Host, sodass mehrere Replikate keine Datenbankdatei über ein Netzwerk-Volume
  teilen können.
- **Mehrere Container, die eine Website ausliefern**, brauchen einen Datenbankserver (zuerst PostgreSQL) und gemeinsamen
  Speicher für `var/storage` ([Container, Cloud und Hosting-Panels](../../betriebssystemunterstuetzung.md#container-cloud-und-hosting-panels)).
  Sie brauchen außerdem Sitzungen, die jeder Container lesen kann; siehe [Kubernetes](kubernetes.md#sitzungen).
- Der Datenbankserver läuft meist in einem eigenen Container oder als verwalteter Dienst Ihrer Plattform, mit eigenem
  Volume und eigenen Backups ([Datenbank-Backups je Engine](../betrieb/datenbank-backups-je-engine.md)).

## Ein Entry Point, der einmal installiert

Kapitel 4 nennt einen minimalen Entry Point für Container, der einmal installiert, dann Velocity startet und im
Vordergrund bleibt, solange er läuft:

```bash
#!/usr/bin/env bash
set -euo pipefail
cd /srv/exponential
if ! grep -q '^\[DatabaseSettings\]' settings/override/site.ini.append.php 2>/dev/null; then
    php bin/php/console exp:install --apply \
        --db=mysql --db-host="${DB_HOST}" --db-name="${DB_NAME}" --db-user="${DB_USER}" \
        --url="${SITE_URL}" --email="${ADMIN_EMAIL}" --password="${ADMIN_PASSWORD}"
fi
php bin/php/console exp:velocity start
# exp:velocity start runs the server in the background and returns; status exits 0 while it runs
while php bin/php/console exp:velocity status >/dev/null 2>&1; do sleep 30; done
echo "Velocity stopped" >&2
exit 1
```

Was das Kapitel dazu erklärt:

- Das `grep` ist derselbe Test, mit dem `exp:install` selbst entscheidet, dass ein Verzeichnis bereits eine Installation
  enthält.
- `exp:velocity start` löst den Server ab und kehrt zurück, sobald er antwortet; die Schleife hält daher den Hauptprozess
  des Containers am Leben und beendet ihn, wenn der Server anhält, sodass die Container-Laufzeitumgebung ihn neu starten
  kann.
- Wenn der Entry Point als `root` läuft, fügen Sie den beiden Befehlen `exp:velocity` `--allow-root-user` hinzu.
- Das Beispiel nutzt `--db=mysql`; die anderen Werte sind `sqlite` (der Standard), `pgsql`, `mongodb`, `mariadb` und
  `oracle` ([Datenbankunterstützung, Auf einen Blick](../../datenbankunterstuetzung.md#auf-einen-blick)).

> **Warnung**
> `exp:install` nutzt standardmäßig die Datenbankaktion `remove`. Das ist, was ein frischer Container will, und genau das,
> was nie auf eine Datenbank zeigen darf, die Daten enthält, die Sie brauchen
> ([4.6](../../../installation/04-installationsweg-waehlen.md#46-unbeaufsichtigte-installationen-ci-und-container)). Lassen Sie den Test
> „bereits installiert“ davor stehen.

## Geheimnisse

- Übergeben Sie das Datenbankpasswort in `EXP_INSTALL_DB_PASSWORD`; `exp:install` liest es, wenn `--db-password` nicht
  angegeben ist, und hält es so aus der Prozessliste und der Shell-Historie heraus.
- Ohne `--password` erzeugt `exp:install` ein Administratorpasswort mit 24 Zeichen und schreibt es nach
  `var/log/initial-admin-password` (Modus `0600`); lesen Sie es dort und ändern Sie es, oder übergeben Sie eines aus Ihrem
  Geheimnisspeicher ([Erste Anmeldung und Standardpasswörter](../sicherheit/erste-anmeldung-und-standardpasswoerter.md)).
- Backen Sie `settings/override` nie in ein Image ein: Es enthält die Zugangsdaten zur Datenbank
  ([13.3 Geheimnisse in settings/override](../../../installation/13-sicherheitshaertung.md#133-geheimnisse-in-settingsoverride)).
- `config.php` kann im Image entstehen (`exp:setup:config --apply --allow-root-user --owner=www-data:www-data` im
  Build-Schritt), aber ein Passwort darin, etwa `CLUSTER_STORAGE_PASS`, wird beim Start aus der Umgebung gesetzt:
  `php bin/php/console exp:config:set CLUSTER_STORAGE_PASS --value="$CLUSTER_DB_PASS" --apply --allow-root-user`. Die
  Ausgabe zeigt es als `********`. In der CI zeigt `exp:config:list --only-set --format=json`, was ein Image oder ein
  laufender Container setzt ([.htaccess und config.php von der Konsole aus einrichten](../../../anleitungen/setup-dateien-von-der-konsole.md#11-in-skripten-containern-und-ci)).

## Betrieb in Containern

- **Cron.** `runcronjobs.php` muss weiterhin nach Zeitplan laufen: aus einem Planer-Container, der die Volumes und die
  Datenbank teilt, oder aus dem Planer Ihrer Plattform, als derselbe Benutzer wie der Webprozess
  ([Cronjobs](../betrieb/cronjobs.md)).
- **Logs.** Exponential schreibt seine Logs unter `var/log` (und pro Website unter `var/<site>/log`); halten Sie sie auf
  dem Volume oder sammeln Sie sie dort ein ([Anfrage-Logs](../betrieb/anfrage-logs.md)).
- **Codeänderungen** erreichen die Worker von Velocity erst nach einem Neustart; `exp:velocity deploy` führt die Schritte
  in Reihenfolge aus ([8.3.14](../../../installation/08-die-website-ausliefern.md#8314-eine-änderung-bereitstellen-expvelocity-deploy)). In
  Containern ist das übliche Gegenstück ein neues Image und ein neuer Container.

## Checkliste

- [ ] Das offizielle Image oder ein Image mit PHP 8.0+ und jeder erforderlichen Erweiterung; `exp:velocity ext check` besteht in der Pipeline.
- [ ] Feste Image-Tags in der Produktion.
- [ ] `var/`, `settings/override`, `settings/siteaccess` auf persistenten Volumes, im Eigentum des PHP-Benutzers des Containers.
- [ ] Ein schreibender Container für SQLite, oder ein Datenbankserver für mehr.
- [ ] Entry Point mit dem Test „bereits installiert“; Geheimnisse aus der Umgebung, nicht aus dem Image.
- [ ] Cron, Logs und Backups festgelegt.

**Eine öffentliche Demo in einem Container.** Geben Sie dem Demo-Dienst `EXP_DEMO_MODE=1` (`-e`, `--env-file` oder `env_file:`; [`docker-demo.env`](../../../../eng-us/examples/demo-mode/docker-demo.env)), nie einem Basis-Image, das andere Websites nutzen. Mit PHP-FPM im Container behalten Sie `clear_env = no` oder setzen `env[EXP_DEMO_MODE] = 1` im Pool. [Demomodus für Hoster](../sicherheit/demomodus-fuer-hoster.md).

## Verwandte Seiten

- [Kubernetes](kubernetes.md), [Platform as a Service](platform-as-a-service.md)
- [exp:install](../bereitstellung/exp-install-fuer-hoster.md), [Bereitstellung automatisieren](../bereitstellung/bereitstellung-automatisieren.md)
- [Mit einem Befehl installieren](../../../funktionen/6.0/installation-mit-einem-befehl.md)
- [.htaccess und config.php von der Konsole aus einrichten](../../../anleitungen/setup-dateien-von-der-konsole.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
