---
title: Container: Docker und Podman
audience: hosting, admin
category: hosting
order: 60
summary: Exponential in Docker- oder Podman-Containern betreiben - was die Quellen sagen (Stufe 2, die veröffentlichten Images von Velocity), was auf ein persistentes Volume gehört, die Regel eines einzigen Schreibers für SQLite, ein Entry Point, der einmal installiert und Velocity im Vordergrund hält, und Geheimnisse.
keywords: Container, Docker, Podman, Image, Volume, persistentes Volume, Entry Point, exp:install, Velocity, ghcr.io, SQLite, WAL, ein Schreiber, PostgreSQL, EXP_INSTALL_DB_PASSWORD, Geheimnisse, containers, image, volume, persistent volume, entry point, one writer, secrets
type: help
---
# Container: Docker und Podman

Diese Seite ist für Hosting-Anbieter und Plattform-Teams, die Exponential-Websites in Linux-Containern mit Docker oder
Podman betreiben. Sie beantwortet, was die Quellen des Projekts zu Containern sagen, was auf einem persistenten Volume
liegen muss, wie viele Container in eine SQLite-Datenbank schreiben dürfen, wie ein Entry Point eine Website einmal
installiert und den Server laufen lässt und wie Sie Geheimnisse aus Images heraushalten.

## Inhalt

1. [Was die Quellen sagen](#was-die-quellen-sagen)
2. [Images](#images)
3. [Volumes: was erhalten bleiben muss](#volumes-was-erhalten-bleiben-muss)
4. [Die Datenbank und die Regel eines einzigen Schreibers](#die-datenbank-und-die-regel-eines-einzigen-schreibers)
5. [Ein Entry Point, der einmal installiert](#ein-entry-point-der-einmal-installiert)
6. [Geheimnisse](#geheimnisse)
7. [Betrieb in Containern](#betrieb-in-containern)
8. [Checkliste](#checkliste)

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

Zwei Wege:

| Weg | Was Sie bekommen | Worauf Sie achten |
|---|---|---|
| Ihr eigenes Image auf einer Basis mit PHP 8.0+ | die PHP-Erweiterungen, die Sie wählen, Composer, die mit `composer install` gebaute Installation | installieren Sie jede erforderliche Erweiterung: `dom`, `xsl`, `intl`, `mbstring`, `iconv`, `zlib`, eine Datenbankerweiterung, `gd` oder ImageMagick und für Velocity `sockets`, `pcntl`, `openssl` |
| Das veröffentlichte Image von Velocity als Basis | den Server und ein PHP mit einer festgelegten Erweiterungsausstattung | prüfen Sie die Variante gegen das, was Exponential braucht |

Die Images von Velocity sind nach Release, PHP-Version und Variante getaggt
([Velocity-Pakete, Docker-Images und Binärdateien](../../../../eng-us/features/6.0/velocity-packages-and-binaries.md#docker)):

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
Vordergrund bleibt, solange es läuft:

```bash
#!/usr/bin/env bash
set -euo pipefail
cd /srv/exponential
if ! grep -q '^\[DatabaseSettings\]' settings/override/site.ini.append.php 2>/dev/null; then
    php bin/php/console exp:install \
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

- [ ] Image mit PHP 8.0+ und jeder erforderlichen Erweiterung; `exp:velocity ext check` besteht in der Pipeline.
- [ ] Feste Image-Tags in der Produktion.
- [ ] `var/`, `settings/override`, `settings/siteaccess` auf persistenten Volumes, im Eigentum des PHP-Benutzers des Containers.
- [ ] Ein schreibender Container für SQLite, oder ein Datenbankserver für mehr.
- [ ] Entry Point mit dem Test „bereits installiert“; Geheimnisse aus der Umgebung, nicht aus dem Image.
- [ ] Cron, Logs und Backups festgelegt.

**Eine öffentliche Demo in einem Container.** Geben Sie dem Demo-Dienst `EXP_DEMO_MODE=1` (`-e`, `--env-file` oder `env_file:`; [`docker-demo.env`](../../../../eng-us/examples/demo-mode/docker-demo.env)), nie einem Basis-Image, das andere Websites nutzen. Mit PHP-FPM im Container behalten Sie `clear_env = no` oder setzen `env[EXP_DEMO_MODE] = 1` im Pool. [Demomodus für Hoster](../sicherheit/demomodus-fuer-hoster.md).

## Verwandte Seiten

- [Kubernetes](kubernetes.md), [Platform as a Service](platform-as-a-service.md)
- [exp:install](../bereitstellung/exp-install-fuer-hoster.md), [Bereitstellung automatisieren](../bereitstellung/bereitstellung-automatisieren.md)
- [Mit einem Befehl installieren](../../../../eng-us/features/6.0/install-in-one-command.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
