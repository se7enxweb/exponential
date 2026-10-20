---
title: Bereitstellung automatisieren
audience: hosting
category: hosting
order: 70
summary: Eine Pipeline zur Bereitstellung neuer Exponential-Websites für Kunden, Schritt für Schritt - Systembenutzer und Abschottung, Datenbank, Code aus Git oder Composer, exp:install mit Geheimnissen aus der Umgebung, die Website ausliefern, Cron, Übergabe des Passworts, Backups und Überwachung, mit Prüfungen nach jedem Schritt und einem Weg, eine Website wieder zu entfernen.
keywords: Automatisierung, Bereitstellung, Pipeline, Skript, Systembenutzer, PHP-FPM-Pool, Velocity, Datenbank, Git, Composer, exp:install, EXP_INSTALL_DB_PASSWORD, Crontab, initial-admin-password, Backup, Überwachung, idempotent, Rückbau, automation, provisioning, pipeline, script, system user, database, backup, monitoring, deprovision
type: help
---
# Bereitstellung automatisieren

Diese Seite ist für Systemadministratoren und Entwickler, die das Anlegen von Exponential-Websites für Kunden automatisieren. Sie
beantwortet: welche Schritte eine Pipeline braucht, in welcher Reihenfolge, was jeder Schritt prüft und wie eine Website wieder entfernt
wird. Ihre eigenen Werkzeuge (Hooks des Panels, Konfigurationsmanagement, Shell-Skripte) erledigen die Arbeit; diese Seite gibt die
Seite von Exponential zu jedem Schritt.

## Inhalt

1. [Die Pipeline](#die-pipeline)
2. [Schritt für Schritt](#schritt-für-schritt)
3. [Sicher für eine Wiederholung machen](#sicher-für-eine-wiederholung-machen)
4. [Eine Website entfernen](#eine-website-entfernen)
5. [Ein Datensatz pro Website](#ein-datensatz-pro-website)

## Die Pipeline

| # | Schritt | Prüfungen |
|---|---|---|
| 1 | Systembenutzer und Verzeichnis | der Benutzer existiert, das Verzeichnis gehört ihm |
| 2 | PHP: FPM-Pool oder Velocity-Instanz | der Pool oder die Instanz läuft als dieser Benutzer |
| 3 | Datenbank | leere Datenbank, Benutzer mit Rechten zum Anlegen (nicht für SQLite 3) |
| 4 | Code | das getestete Release ist ausgecheckt, `vendor/` vollständig |
| 5 | Installation | `exp:install --dry-run`, dann `exp:install`; Exit-Status 0 |
| 6 | Ausliefern | die Website antwortet über HTTPS |
| 7 | Cron | die Crontab des Benutzers der Website führt `runcronjobs.php` aus |
| 8 | Übergabe | Passwort übergeben, `var/log/initial-admin-password` entfernt |
| 9 | Backups und Überwachung | die Website ist in beiden registriert |

## Schritt für Schritt

### 1. Systembenutzer und Verzeichnis

Ein Systembenutzer pro Website ([Benutzer und Pools pro Kunde](../sicherheit/benutzer-und-pools-pro-kunde.md)). Alles, was der
Installer und die Website schreiben, muss diesem Benutzer gehören: Führen Sie ihn als den Benutzer aus, als der der Webserver oder
Velocity läuft, oder korrigieren Sie danach das Eigentum
([4.6](../../../installation/04-installationsweg-waehlen.md#46-unbeaufsichtigte-installationen-ci-und-container)).

### 2. PHP

- PHP-FPM: ein Pool für die Website, der als ihr Benutzer läuft ([Abstimmung von PHP-FPM](../leistung/abstimmung-von-php-fpm.md)).
- Velocity: Worker laufen als `[ServerSettings] User` und `Group` der `velocity.ini` der Website; geben Sie jeder Website eigene Ports
  ([Velocity-Worker und Pools](../server/velocity-worker-und-pools.md)).

### 3. Datenbank

Für SQLite 3 nichts außer einem beschreibbaren `var/storage/sqlite3/`. Für eine Server-Engine legen Sie eine Datenbank und einen
Benutzer nur für diese Website an ([Datenbanken und Benutzer in Panels](../control-panels/datenbanken-und-benutzer.md)); verwenden Sie nie `root`
oder `postgres` erneut.

### 4. Code

Ein Checkout des Release-Tags, das Sie getestet haben, dann die Abhängigkeiten aus der Lock-Datei:

```bash
git clone --branch v6.0.<n> https://github.com/se7enxweb/exponential.git <installation>
cd <installation> && composer install --no-dev
```

Legen Sie ein Release für alle neuen Websites fest und verschieben Sie diese Festlegung erst nach einem Test
([Composer und Git auf Servern](../grundlagen/composer-und-git-auf-servern.md)).

### 5. Installation

Übergeben Sie das Datenbankpasswort über die Umgebung und testen Sie zuerst:

```bash
export EXP_INSTALL_DB_PASSWORD='<from your secret store>'
php bin/php/console exp:install --dry-run --db=pgsql --db-host=<db host> --db-name=<db> --db-user=<db user> \
  --url=https://www.example.com --email=<contact> --package=sevenx_multisite_clean
php bin/php/console exp:install --db=pgsql --db-host=<db host> --db-name=<db> --db-user=<db user> \
  --url=https://www.example.com --email=<contact> --package=sevenx_multisite_clean
```

Bei Exit-Status 1 bewahren Sie `var/log/setup.log` auf und halten die Pipeline an; die Website bleibt im Wartungsmodus
([exp:install für Hoster](exp-install-fuer-hoster.md#exit-status)).

### 6. Ausliefern

Konfigurieren Sie den Webserver für die Website, oder setzen Sie die Engine von Velocity und starten Sie sie
([Einen Webserver wählen](../server/einen-webserver-waehlen.md)). Prüfen Sie dann von außen:

```bash
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/admin/user/login
```

Beide sollten mit `200` antworten ([10.11](../../../installation/10-nach-der-installation.md#1011-überwachung-und-zustandsprüfungen)).

### 7. Cron

Richten Sie die Crontab aus [10.3](../../../installation/10-nach-der-installation.md#eine-crontab) in der Crontab des Benutzers der Website ein, mit
derselben PHP-Binärdatei, die das Web nutzt ([Cronjobs](../betrieb/cronjobs.md)).

### 8. Übergabe

Wenn das Passwort erzeugt wurde, lesen Sie es aus `var/log/initial-admin-password`, übergeben Sie es über einen sicheren Kanal und
löschen Sie die Datei. Sagen Sie dem Kunden, dass er es ändern und persönliche Konten anlegen soll
([Erste Anmeldung und Standardpasswörter](../sicherheit/erste-anmeldung-und-standardpasswoerter.md)).

### 9. Backups und Überwachung

Registrieren Sie die Datenbank und `var/storage`, `settings/override`, `settings/siteaccess` im Backup-System und die Adresse der
Website in der Überwachung ([Was gesichert werden muss](../betrieb/backups-was-gesichert-werden-muss.md),
[Zustandsprüfungen und Verfügbarkeit](../betrieb/zustandspruefungen-und-verfuegbarkeit.md)).

## Sicher für eine Wiederholung machen

- **Erkennen Sie eine bestehende Installation** so, wie `exp:install` es tut: `settings/override/site.ini.append.php` enthält einen
  Block `[DatabaseSettings]`. Überspringen Sie dann den Schritt der Installation; `exp:install` selbst verweigert ohne `--force`.
- **Übergeben Sie aus einer Pipeline nie `--force`**, es sei denn, der Lauf soll diese Website zerstören und neu aufbauen.
- **Halten Sie `--db-action=remove` von Datenbanken mit Daten fern.** Eine Pipeline sollte für eine neue Website immer eine neue
  Datenbank anlegen.
- **Halten Sie Geheimnisse aus Logs heraus.** Geben Sie die Umgebung nicht aus; die gespeicherte Konfiguration in `var/log/` maskiert
  Passwörter.

## Eine Website entfernen

In umgekehrter Reihenfolge: Wartungsmodus an, ein letztes Backup, Crontab entfernt, Webserver oder Velocity-Instanz entfernt,
Datenbank und Benutzer gelöscht, Dateien entfernt, Backups für die vereinbarte Aufbewahrung behalten. Die vollständige Liste ist die
[Checkliste für das Offboarding eines Kunden](../checklisten/checkliste-fuer-das-offboarding-eines-kunden.md).

## Ein Datensatz pro Website

Schreiben Sie, was die Pipeline getan hat, in einen Datensatz pro Website: Domain, Systembenutzer, PHP-Version, Engine und Ports,
Datenbank-Engine und Name, Release-Tag, Seitenpaket, Datum der Installation, Registrierung in Backup und Überwachung. Support-Mitarbeitende
brauchen ihn beim ersten Anruf.

## Verwandte Seiten

- [Ein-Klick-Installer](ein-klick-installer-fuer-exponential.md)
- [Container: Docker und Podman](../modelle/container-docker-und-podman.md)
- [Checkliste für einen neuen Server](../checklisten/checkliste-fuer-einen-neuen-server.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
