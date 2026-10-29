---
title: Container-Images
audience: bundler, hosting
category: bundling
order: 280
summary: Wie Sie Exponential als Docker- oder Podman-Image bündeln - das Projekt liefert kein Dockerfile, daher gibt diese Seite allgemeine Hinweise - die Wahl der Basis, was ins Image und was auf Volumes gehört, der dokumentierte Einstiegspunkt, der einmal installiert und Velocity am Laufen hält, Geheimnisse, die SQLite-Regel des einen Schreibers und eine Checkliste für das Image.
keywords: Container, Image, Volume, Einstiegspunkt, Geheimnisse, container, Docker, Podman, image, Dockerfile, volume, entry point, exp:install, Velocity, ghcr.io, SQLite, PostgreSQL, secrets, EXP_INSTALL_DB_PASSWORD, bundle
type: help
---
# Container-Images

Diese Seite richtet sich an Bündler, die Exponential als Container-Image veröffentlichen. Das Exponential-Repository
enthält **kein Dockerfile und keine Compose-Datei**; die Hinweise unten sind allgemein und beruhen auf dem, was die
Dokumentation sagt. Velocity, der Webserver, der Teil von Exponential ist, veröffentlicht durchaus Images, auf denen
Sie aufbauen können.

## Inhalt

1. [Was die Quellen sagen](#was-die-quellen-sagen)
2. [Eine Basis wählen](#eine-basis-wählen)
3. [Image oder Volume](#image-oder-volume)
4. [Ein Einstiegspunkt](#ein-einstiegspunkt)
5. [Geheimnisse und die Datenbank](#geheimnisse-und-die-datenbank)
6. [Checkliste für das Image](#checkliste-für-das-image)

## Was die Quellen sagen

- "Docker und Podman (Linux-Container) sind Stufe 2, nachweislich funktionsfähig"
  ([Betriebssystemunterstützung](../betriebssystemunterstuetzung.md#stufe-2-bekanntermaßen-lauffähig)).
- "Jedes Image mit PHP 8 und den oben genannten Erweiterungen führt Exponential aus ... Halten Sie `var/` (Speicher,
  Protokolle, die SQLite-Datenbank) und `settings/override` auf einem dauerhaften Volume. Betreiben Sie mit SQLite
  **einen** Container, der schreibt"
  ([Container, Cloud und Hosting-Panels](../betriebssystemunterstuetzung.md#container-cloud-und-hosting-panels)).
- `exp:install` ist die Methode für "CI-Pipeline, Container-Image, automatisierte Testumgebung"
  ([4.5](../../installation/04-installationsweg-waehlen.md#45-welche-methode-für-welche-situation)).

## Eine Basis wählen

| Weg | Hinweise |
|---|---|
| Ihr eigenes Image auf Basis von PHP 8.0+ | installieren Sie jede erforderliche PHP-Erweiterung: `dom`, `xsl`, `intl`, `mbstring`, `iconv`, `zlib`, eine Datenbankerweiterung, `gd` oder ImageMagick und für Velocity `sockets`, `pcntl`, `openssl` |
| Das Image von Velocity `ghcr.io/se7enxweb/exponential-velocity` | getaggt als `<release>-php<version>-<variant>`; "Legen Sie in Produktion einen festen Tag fest"; die Variante `standard` ist "das, wovon die Dokumentation ausgeht" |

Quelle: [Container: Docker und Podman](../hosting/modelle/container-docker-und-podman.md#images). Gleich welche
Basis, führen Sie in Ihrer Pipeline `php bin/php/console exp:velocity ext check` im gebauten Image aus.

Ein allgemeiner Build, in Worten statt als Dockerfile:

1. Beginnen Sie mit der festgelegten Basis.
2. Kopieren Sie `composer.json` und `composer.lock` Ihres Bundles; führen Sie `composer install --no-dev
   --no-interaction` als der Benutzer aus, unter dem die Website laufen wird.
3. Fügen Sie die Dateien Ihres Seitenpakets hinzu und importieren Sie sie, oder planen Sie den Import beim ersten
   Start ([Offline-Bundles](offline-bundles.md)).
4. Erzeugen Sie die Autoloads neu ([Caches und Autoloads nach dem Bauen](caches-und-autoloads-nach-dem-bauen.md)).
5. Setzen Sie die Besitzrechte der beschreibbaren Verzeichnisse ([Dateirechte](dateirechte-und-besitz-von-var.md)).
6. Fügen Sie den Einstiegspunkt unten hinzu; führen Sie den Installer nicht während des Builds aus.

## Image oder Volume

| Pfad | Wo |
|---|---|
| `kernel/`, `lib/`, `vendor/`, `extension/`, `design/` | im Image |
| `var/` | dauerhaftes Volume |
| `settings/override` | dauerhaftes Volume |
| `settings/siteaccess` | dauerhaftes Volume |

"Backen Sie `settings/override` nie in ein Image: Es enthält die Datenbank-Zugangsdaten"
([Container](../hosting/modelle/container-docker-und-podman.md#geheimnisse)). Legen Sie die Benutzer-ID für Image und
Volume einmal fest und behalten Sie sie bei.

## Ein Einstiegspunkt

Der dokumentierte minimale Einstiegspunkt "installiert einmal, startet dann den Server und bleibt im Vordergrund,
solange er läuft" ([4.6](../../installation/04-installationsweg-waehlen.md#46-unbeaufsichtigte-installationen-ci-und-container)):

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

- Übergeben Sie `EXP_INSTALL_DB_PASSWORD` in der Umgebung des Containers.
- "Wenn der Einstiegspunkt als `root` läuft, ergänzen Sie `--allow-root-user` bei den beiden `exp:velocity`-Befehlen."
- Ändern Sie `--db` auf die Datenbank Ihres Bundles (`sqlite` ist der Standard, `pgsql` der empfohlene Server).
- Ohne `--password` wird ein Passwort mit 24 Zeichen erzeugt und nach `var/log/initial-admin-password` geschrieben;
  sagen Sie dem Empfänger, wie er es einmal ausliest.

## Geheimnisse und die Datenbank

- **SQLite:** ein schreibender Container je Datenbankdatei; "das WAL von SQLite braucht gemeinsamen Speicher auf einem
  Host".
- **Mehrere Replikate:** ein Datenbankserver ("PostgreSQL zuerst"), gemeinsamer Speicher für `var/storage` und
  Sitzungen, die jeder Container lesen kann ([Kubernetes](../hosting/modelle/kubernetes.md#sitzungen)).
- **Cron** muss trotzdem laufen: ein Scheduler-Container, der die Volumes teilt, als derselbe Benutzer
  ([Container](../hosting/modelle/container-docker-und-podman.md#betrieb-in-containern)).

## Checkliste für das Image

- [ ] Basis-Image per Tag oder Digest festgelegt; Composer aus der Lock-Datei; `--no-dev`.
- [ ] `exp:velocity ext check` läuft im Image fehlerfrei durch.
- [ ] Keine `settings/override`, `settings/siteaccess`, `kickstart.ini`, Protokolle oder Caches aus dem Build im Image.
- [ ] Volumes dokumentiert: `var/`, `settings/override`, `settings/siteaccess`.
- [ ] Der Einstiegspunkt installiert einmal und hält den Hauptprozess am Leben.
- [ ] Der Quellcode jeder GPL-Komponente wird angeboten ([GPL-Pflichten](gpl-pflichten-beim-weitergeben.md)).
- [ ] Labels oder Hinweise nennen die Exponential-Version und die Version Ihres Bundles.

## Verwandte Seiten

- [Container: Docker und Podman](../hosting/modelle/container-docker-und-podman.md)
- [Erster Start und einmalige Installation](erster-start-und-einmalige-installation.md)
- [Velocity in einem Bundle](velocity-in-einem-bundle.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
