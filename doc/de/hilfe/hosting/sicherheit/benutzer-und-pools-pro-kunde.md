---
title: Benutzer und Pools pro Kunde
audience: hosting
category: hosting
order: 120
summary: Die konkrete Einrichtung, die der Exponential-Website jedes Kunden einen eigenen Benutzer des Betriebssystems, einen eigenen PHP-FPM-Pool oder eine eigene Velocity-Instanz, eine eigene Crontab und einen eigenen Datenbankbenutzer gibt, mit Eigentum und Modi für Code, var/ und settings/, der Variante mit gemeinsamer Gruppe und den Prüfungen, die zeigen, dass sie hält.
keywords: Benutzer pro Kunde, Benutzer des Betriebssystems, PHP-FPM-Pool, Pool pro Website, Velocity-Instanz, User, Group, AllowRootWorkers, Crontab, Eigentum an Dateien, Berechtigungen, FileSettings, setgid, nur lesbarer Code, Datenbankbenutzer, Deploy-Benutzer, Trennung, per-customer user, operating-system user, pool per site, file ownership, permissions, read-only code, database user, deploy user, isolation
type: help
---
# Benutzer und Pools pro Kunde

Diese Seite ist für die Systemadministratoren eines Hosters, die die Einrichtung pro Kunde auf einer geteilten Maschine bauen. Sie macht
aus dem Prinzip der [Trennung von Mandanten](trennung-von-mandanten.md) Benutzer, Pools, Crontabs und Dateimodi. Das Verfahren dahinter
ist [Kapitel 8, Dateiberechtigungen und Eigentum](../../../../en/us/install/08-serving-the-site.md#87-file-permissions-and-ownership) und
[13.11](../../../../en/us/install/13-security-hardening.md#1311-file-permissions-and-ownership).

## Inhalt

1. [Das Ziel](#das-ziel)
2. [Der Benutzer](#der-benutzer)
3. [Eigentum und Modi](#eigentum-und-modi)
4. [Ein PHP-FPM-Pool pro Kunde](#ein-php-fpm-pool-pro-kunde)
5. [Eine Velocity-Instanz pro Kunde](#eine-velocity-instanz-pro-kunde)
6. [Cron und Konsole als Benutzer des Kunden](#cron-und-konsole-als-benutzer-des-kunden)
7. [Wenn zwei Benutzer schreiben müssen](#wenn-zwei-benutzer-schreiben-müssen)
8. [Datenbankbenutzer](#datenbankbenutzer)
9. [Prüfungen](#prüfungen)
10. [Checkliste](#checkliste)

## Das Ziel

```text
customer A                               customer B
  user  site-a, group site-a               user  site-b, group site-b
  PHP-FPM pool "site-a" (or Velocity)      PHP-FPM pool "site-b" (or Velocity)
  crontab of site-a                        crontab of site-b
  database "a", database user "a"          database "b", database user "b"
  /var/www/vhosts/<domain-a>/              /var/www/vhosts/<domain-b>/
```

Probleme mit Berechtigungen entstehen fast immer dadurch, dass **zwei Benutzer dieselben Dateien schreiben**
([8.7.2](../../../../en/us/install/08-serving-the-site.md#872-one-user-or-one-group)). Also schreibt ein Benutzer jede Website: Der Pool oder die
Velocity-Worker, die Cronjobs und die Konsolenskripte laufen alle als Benutzer dieses Kunden.

## Der Benutzer

Ein Benutzer und eine Gruppe des Betriebssystems pro Installation eines Kunden, angelegt von Ihrer Bereitstellung oder Ihrem Panel
(Panels legen meist einen pro Abonnement an; prüfen Sie die Dokumentation Ihres Panels). Shell-Zugang für den Kunden ist eine
Entscheidung des Tarifs ([Cron- und Shell-Zugang](../control-panels/cron-und-shell-zugang.md)).

## Eigentum und Modi

| Pfad | Eigentümer | Für den Benutzer der Website beschreibbar | Warum |
|---|---|---|---|
| `var/` und darunter | Benutzer der Website, Gruppe der Website | ja | Caches, Logs, Uploads, Autoloads |
| `settings/`, `settings/override`, `settings/siteaccess` | Benutzer der Website, Gruppe der Website | ja, solange Administratoren Einstellungen im Browser ändern | von der Administration geschriebene Einstellungen |
| `design/`, `extension/` | ein anderer Benutzer (Deploy-Benutzer) oder der Benutzer der Website während Paketinstallationen | nur, während Pakete aus der Administration installiert werden | Paketinstallation |
| `kernel/`, `lib/`, `vendor/`, `index.php` | ein anderer Benutzer | nein | ein kompromittierter PHP-Prozess kann keinen Code ändern |
| `settings/override/*.ini.append.php` | Benutzer der Website oder root, Gruppe der Website | `0640` oder strenger | Geheimnisse |

Setzen Sie die gehärteten Modi, die `site.ini` empfiehlt, statt der mitgelieferten `0777`/`0666`:

```ini
# settings/override/site.ini.append.php
[FileSettings]
StorageDirPermissions=0770
StorageFilePermissions=0660
TemporaryPermissions=0770
LogFilePermissions=0660
```

und korrigieren Sie vorhandene Dateien einmal, mit der Gruppe der Website auf allem
([13.11](../../../../en/us/install/13-security-hardening.md#1311-file-permissions-and-ownership)):

```bash
find var -type d -exec chmod 2770 {} +
find var -type f -exec chmod 0660 {} +
```

Geben Sie jeder Website ein eigenes `TemporaryDir` unter `var/` statt des geteilten `/tmp/`.

## Ein PHP-FPM-Pool pro Kunde

Der Pool führt PHP als Benutzer der Website aus ([8.5.2](../../../../en/us/install/08-serving-the-site.md#852-php-fpm)). Setzen Sie pro Pool
Benutzer und Gruppe und die PHP-Werte, die Exponential braucht (`memory_limit` mindestens 256M, `max_execution_time`,
`upload_max_filesize`, `post_max_size`, OPcache an). Wo Pools liegen und wie Sie den richtigen Master neu laden:
[PHP-Auswahl und FPM-Pools](../control-panels/php-auswahl-und-fpm-pools.md). Auf Maschinen mit mehreren PHP-Versionen findet
`exp:velocity deploy` den Master, der die Website ausliefert (`[DeploySettings] PhpFpmService=auto`).

Halten Sie Sitzungsdateien, `upload_tmp_dir` und jedes `auto_prepend_file` (zum Beispiel request-shield) pro Pool, damit nichts von einem
Kunden im Verzeichnis eines anderen landet.

## Eine Velocity-Instanz pro Kunde

Eine Velocity pro Website, jede als Benutzer des Kunden, an `127.0.0.1` gebunden, hinter einem Proxy, der nach Hostnamen routet
([Velocity neben Apache oder nginx](../server/velocity-neben-apache-oder-nginx.md)):

```bash
php bin/php/console exp:velocity config set ServerSettings User site-a --allow-root-user
php bin/php/console exp:velocity config set ServerSettings Group site-a --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user
```

- Wer die Worker werden: `[ServerSettings] User` und `Group`, sonst `VC_RUN_USER` und `VC_RUN_GROUP`, sonst der Eigentümer des Document
  Root. Ein Document Root, der root gehört, ohne Konfiguration lässt die Worker als root laufen; der Start meldet das
  ([8.3.5](../../../../en/us/install/08-serving-the-site.md#835-address-ports-user-and-group)).
- `AllowRootWorkers=disabled` (der Standard) verweigert root als Benutzer der Worker.
- Jede Instanz braucht eigene Ports; die Datei pro Website `/etc/vc/sites-available/<site>.conf` hat den Modus `0600`.
- Worker und Poolgrößen pro Tarif: [Velocity-Worker und Pools](../server/velocity-worker-und-pools.md).

## Cron und Konsole als Benutzer des Kunden

Cronjobs aus der Crontab dieses Benutzers oder mit `sudo -u`:

```bash
crontab -u site-a -e
# */5 * * * * cd /var/www/vhosts/<domain-a>/<installation> && php runcronjobs.php -q frequent
sudo -u site-a php bin/php/ezcache.php --clear-tag=ini
```

Die Kommandozeilenskripte von Exponential verweigern den Lauf als root, es sei denn, `--allow-root-user` wird angegeben; nutzen Sie es
nur für die dokumentierten Aufgaben als root, und prüfen Sie danach, dass die Dateien in `var/` noch dem Benutzer der Website gehören
([Sicherheitshärtung](../../../../en/us/help/security-hardening.md#cron-and-command-line-scripts-as-root)).

## Wenn zwei Benutzer schreiben müssen

Wenn die Benutzer verschieden sein müssen (zum Beispiel der PHP-Benutzer des Webservers und ein Deploy-Benutzer), geben Sie ihnen eine
gemeinsame Gruppe, machen Sie die beschreibbaren Verzeichnisse für die Gruppe beschreibbar und setgid, sodass neue Dateien die Gruppe
erben, und nutzen Sie die Modi oben, die die Gruppe erhalten ([8.7.2](../../../../en/us/install/08-serving-the-site.md#872-one-user-or-one-group)):

```bash
sudo chgrp -R site-a design extension settings var
sudo find var settings -type d -exec chmod 2775 {} +
```

Wenn Apache (PHP-FPM) und Velocity dieselbe Installation nebeneinander ausliefern, teilen sie `var/`, die Caches und die Sitzungen: Geben
Sie ihnen denselben Benutzer oder diese Einrichtung mit Gruppe, und sehen Sie sich nach einigen Anfragen den Eigentümer neuer Dateien in
`var/` an.

## Datenbankbenutzer

Eine Datenbank und ein Datenbankbenutzer pro Installation, mit Rechten nur auf diese Datenbank; die Verbindung wird nach
`settings/override/site.ini.append.php` geschrieben. Hinweise pro Engine, in der offiziellen Reihenfolge 1. SQLite 3 · 2. PostgreSQL ·
3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL: [Datenbanken und Benutzer](../control-panels/datenbanken-und-benutzer.md) und
[Datenbanken in der offiziellen Reihenfolge](../grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md). Mit SQLite 3 gibt es keinen
Datenbankbenutzer: Die Datei unter `var/storage/sqlite3/` ist durch die Dateiberechtigungen des Benutzers der Website geschützt.

## Prüfungen

```bash
# no world-writable settings files
find settings extension/*/settings -perm -o+w
# who owns new files after some traffic (expect the site's user and group)
find var -newer index.php -not -user site-a | head
# which user each PHP-FPM pool runs as
ps -eo user,cmd | grep 'php-fpm: pool' | grep -v grep
```

Die erste darf nichts ausgeben ([13.11](../../../../en/us/install/13-security-hardening.md#1311-file-permissions-and-ownership)).

## Checkliste

- [ ] Ein Benutzer und eine Gruppe des Betriebssystems pro Installation.
- [ ] Pool oder Velocity-Worker, Cron und Konsole laufen alle als dieser Benutzer.
- [ ] Code gehört einem anderen Benutzer und ist für den Benutzer der Website nur lesbar.
- [ ] Gehärtete `[FileSettings]`; keine für alle beschreibbaren Dateien; `settings/override` mit `0640` oder strenger.
- [ ] `TemporaryDir`, Sitzungsverzeichnis und Upload-Verzeichnis pro Website.
- [ ] Velocity: `User`, `Group` gesetzt; hinter dem Proxy an `127.0.0.1` gebunden; eigene Ports pro Instanz.
- [ ] Eine Datenbank und ein Datenbankbenutzer pro Installation.
- [ ] Die Prüfungen oben laufen nach der Bereitstellung und nach jedem Ausrollen.

## Verwandte Seiten

- [Sicherheit für Hoster: Überblick](README.md)
- [Trennung von Mandanten](trennung-von-mandanten.md)
- [Beschreibbare Verzeichnisse und Berechtigungen](../grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md)
- [PHP-Auswahl und FPM-Pools](../control-panels/php-auswahl-und-fpm-pools.md)
- [Kapitel 8: Dateiberechtigungen und Eigentum](../../../../en/us/install/08-serving-the-site.md#87-file-permissions-and-ownership)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
