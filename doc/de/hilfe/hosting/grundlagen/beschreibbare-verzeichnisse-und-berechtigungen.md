---
title: Beschreibbare Verzeichnisse und Berechtigungen
audience: hosting, admin
category: hosting
order: 160
summary: Welche Verzeichnisse einer Exponential-Installation beschreibbar sein müssen und für wen, die Einrichtung mit einem Benutzer und mit einer Gruppe, die gehärteten Dateimodi, Einstellungsdateien mit Geheimnissen, die eigenen Dateien von Velocity und wie Sie die Eigentümer nach einem als root ausgeführten Befehl reparieren.
keywords: Berechtigungen, Dateiberechtigungen, Eigentum, beschreibbare Verzeichnisse, var, settings, design, extension, chmod, chown, setgid, umask, StorageDirPermissions, StorageFilePermissions, TemporaryPermissions, LogFilePermissions, PHP-FPM-Benutzer, Velocity User Group, AllowRootWorkers, root, Hosting, Mandanten, permissions, file permissions, ownership, writable directories, PHP-FPM user, hosting, multi-tenant
type: help
---
# Beschreibbare Verzeichnisse und Berechtigungen

Diese Seite ist für Systemadministratoren und Support-Mitarbeitende bei einem Hosting-Anbieter. Sie beantwortet: was eine
Exponential-Installation schreiben können muss, welchem Benutzer sie gehören sollte, wie Sie sie einrichten, wenn mehrere
Benutzer beteiligt sind, welche Dateimodi Sie statt der mitgelieferten, für alle beschreibbaren verwenden und wie Sie
eine Website reparieren, die ein root-Befehl beschädigt hat. Probleme mit Berechtigungen gehören bei jeder
PHP-Anwendung zu den häufigsten Support-Fällen; bei Exponential entstehen sie fast immer dadurch, dass zwei Benutzer
dieselben Dateien schreiben. Quellen:
[Kapitel 8.7](../../../../en/us/install/08-serving-the-site.md#87-file-permissions-and-ownership) und
[Kapitel 13.11](../../../../en/us/install/13-security-hardening.md#1311-file-permissions-and-ownership).

## Inhalt

1. [Was beschreibbar sein muss](#was-beschreibbar-sein-muss)
2. [Wer schreibt](#wer-schreibt)
3. [Einrichtung 1: ein Benutzer](#einrichtung-1-ein-benutzer)
4. [Einrichtung 2: eine Gruppe](#einrichtung-2-eine-gruppe)
5. [Gehärtete Dateimodi](#gehärtete-dateimodi)
6. [Einstellungsdateien mit Geheimnissen](#einstellungsdateien-mit-geheimnissen)
7. [Die eigenen Dateien von Velocity](#die-eigenen-dateien-von-velocity)
8. [Eine Website reparieren](#eine-website-reparieren)
9. [Checkliste](#checkliste)
10. [Verwandte Seiten](#verwandte-seiten)

## Was beschreibbar sein muss

Der kritische Test `directory_permissions` des Setup-Assistenten prüft die Liste in `settings/setup.ini`,
`[directory_permissions] CheckList`: `design`, `extension`, `settings`, `settings/override`, `settings/siteaccess`,
`settings/siteaccess/admin`, `var` und seine Unterverzeichnisse für Cache, Log, Speicher und Autoload.

| Verzeichnis | Warum | Muss beschreibbar bleiben |
|---|---|---|
| `var/` und alles darunter | Caches, kompilierte Templates, Logs, hochgeladene Dateien, Bildvarianten, Autoload-Arrays, die SQLite-Datenbank | immer |
| `settings/`, `settings/override`, `settings/siteaccess` | die Installer und die Administration schreiben Einstellungen | solange Administratoren Einstellungen im Browser ändern |
| `design/`, `extension/` | der Setup-Assistent und die Paketinstallation schreiben dorthin | nur solange Pakete aus der Administration installiert werden |

Alles andere (`kernel/`, `lib/`, `vendor/`, `index.php`) muss nur lesbar sein. Ein Code-Baum, den das PHP der Website
nicht ändern kann, ist ein starker Schutz: Eine Schwachstelle in einer Erweiterung kann dann keinen Code einschleusen
([Kapitel 13.11](../../../../en/us/install/13-security-hardening.md#1311-file-permissions-and-ownership)).

> **Tipp**
> Websites, die aus der Versionsverwaltung ausgerollt werden, können ihre Einstellungen mit
> `[ConfigurationSettings] ManagedByDeployment=enabled` sperren; dann schreibt die Administration auf dem Server keine
> Einstellungen und Templates mehr ([ManagedByDeployment](../../../../en/us/features/6.0/managed-by-deployment.md)). Änderungen an
> Einstellungen kommen dann nur noch über das Ausrollen, nie über den Browser.

## Wer schreibt

Vier Arten von Prozessen schreiben `var/` und müssen sich über das Eigentum einig sein:

- das PHP des Webservers (der Benutzer des PHP-FPM-Pools oder der Apache-Benutzer von mod_php);
- die Worker von Velocity, wenn Velocity die Website ausliefert;
- Cronjobs (`runcronjobs.php`);
- Konsolenbefehle und Composer, von einem Administrator ausgeführt.

Eine Cache-Datei, die `root` von der Kommandozeile geschrieben hat, kann der Benutzer der Website danach nicht ersetzen,
und die Website versagt an seltsamen Stellen.

## Einrichtung 1: ein Benutzer

Die empfohlene Einrichtung ([Kapitel 8.7.2](../../../../en/us/install/08-serving-the-site.md#872-one-user-or-one-group)):
**Führen Sie alles als Benutzer der Website aus.**

- PHP-FPM-Pool: `user = <customer>`.
- Velocity: `settings/velocity.ini [ServerSettings] User=<customer>`, `Group=<customer>`, oder lassen Sie sie leer und
  den Eigentümer des Document Root entscheiden. Velocity als root zu starten ist in Ordnung: Nur sein Elternprozess
  bleibt root, und root-Worker werden abgelehnt, sofern nicht `AllowRootWorkers=enabled` gesetzt ist (mitgeliefert
  `disabled`).
- Cron: die Crontab des Benutzers der Website (`crontab -u <customer> -e`) oder `sudo -u <customer> php runcronjobs.php`.
- Konsole und Composer: als Benutzer der Website (`sudo -u <customer> php bin/php/console ...`).

Für einen Hosting-Anbieter mit einem Systembenutzer pro Kunde ist das auch das natürliche Modell der Abschottung; siehe
[Benutzer und Pools pro Kunde](../sicherheit/benutzer-und-pools-pro-kunde.md).

## Einrichtung 2: eine Gruppe

Wenn die Benutzer verschieden sein müssen (zum Beispiel der Benutzer des Webservers und ein Benutzer für das Ausrollen),
geben Sie ihnen eine gemeinsame Gruppe und machen Sie die beschreibbaren Verzeichnisse für die Gruppe beschreibbar und
setgid, sodass neue Dateien die Gruppe erben:

```bash
sudo chgrp -R <customer> design extension settings var
sudo find var settings -type d -exec chmod 2775 {} +
```

und geben Sie jedem Prozess eine umask von `0002`. Das SQLite-Verzeichnis braucht dieselbe Behandlung
([SQLite für Hoster](sqlite-fuer-hoster.md#dateien-und-berechtigungen)).

Wenn Apache mit PHP-FPM und Velocity dieselbe Installation nebeneinander ausliefern, teilen sie sich `var/`, die Caches
und die Sitzungen: Geben Sie ihnen denselben Benutzer oder diese Einrichtung mit einer Gruppe.

## Gehärtete Dateimodi

Exponential legt Dateien mit den Modi aus `settings/site.ini`, `[FileSettings]` an. Die mitgelieferten Werte sind `0777`
und `0666`, für alle beschreibbar; `site.ini` selbst empfiehlt `0770` und `0660` mit dem richtigen Benutzer und der
richtigen Gruppe:

```ini
# settings/override/site.ini.append.php
[FileSettings]
StorageDirPermissions=0770
StorageFilePermissions=0660
TemporaryPermissions=0770
LogFilePermissions=0660
```

und korrigieren Sie vorhandene Dateien einmal, mit der Gruppe der Website auf allem:

```bash
find var -type d -exec chmod 2770 {} +
find var -type f -exec chmod 0660 {} +
```

`settings/image.ini [FileSettings]` hat eigene `DirPermissions` und `ImagePermissions` (mitgeliefert `0777` und `0666`)
für Bildverzeichnisse und -dateien; setzen Sie sie genauso. `config.php` kann jeden Modus mit `EZP_DIR_MODE_MAX` und
`EZP_FILE_MODE_MAX` begrenzen ([Dateimodi](../../../../en/us/bc/6.0/file-modes.md)).

> **Warnung**
> Auf einem Server mit mehr als einem Kunden erlauben für alle beschreibbare Dateien dem PHP eines Kunden, die Website
> eines anderen Kunden zu ändern. Setzen Sie die gehärteten Modi auf jeder Installation, die Sie hosten.

**`TemporaryDir`** (mitgeliefert `/tmp/`) wird mit jedem Benutzer der Maschine geteilt; erwägen Sie ein eigenes
Verzeichnis der Website, zum Beispiel unter `var/`.

## Einstellungsdateien mit Geheimnissen

`settings/override/` und `settings/siteaccess/` enthalten die Zugangsdaten zur Datenbank. Halten Sie sie auf `0640` oder
strenger. Seit 6.0.15 behält ein Speichern aus der Administration oder mit `exp:ini` Modus, Eigentümer und Gruppe der
Datei bei und legt eine neue Einstellungsdatei mit `0640` an; prüfen Sie einmal, dass nichts für alle beschreibbar
geblieben ist ([Dateiberechtigungen beim Speichern von INI-Dateien](../../../../en/us/bc/6.0/ini-save-file-permissions.md)):

```bash
find settings extension/*/settings -perm -o+w
```

Es darf nichts ausgeben. Stellen Sie sicher, dass der Webserver diese Dateien nie ausliefert
([Kapitel 13.2](../../../../en/us/install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out)).

## Die eigenen Dateien von Velocity

| Pfad | Geschrieben von | Modus |
|---|---|---|
| `/etc/vc/sites-available/<site>.conf` | `exp:velocity` (root) | `0600` |
| private Schlüssel im Verzeichnis `ssl/` der Konfiguration | dem Server | `0600` |
| `var/vc/qbix/log/` | dem Server | `[LogSettings] FileMode` `0640`, `DirMode` `0750` |
| `var/cache/qbix-reverse/` | dem Server, für die Worker | `[CacheSettings] FileMode` `0640`, `DirMode` `0750` |

Der Benutzer der Worker muss den Quellcode der Engine in `vendor/` lesen können
([Kapitel 8.7.3](../../../../en/us/install/08-serving-the-site.md#873-velocitys-own-files)).

## Eine Website reparieren

Nachdem versehentlich ein Befehl als root ausgeführt wurde, geben Sie den beschreibbaren Baum an den Benutzer der Website
zurück:

```bash
sudo chown -R <customer>:<customer> design extension settings var
sudo chmod -R ug+rwx design extension settings var
```

und leeren Sie dann die Caches als Benutzer der Website. Die Systemprüfung des Setup-Assistenten gibt die genauen Befehle
`chown` und `chmod` für die Maschine aus. Verwenden Sie `bin/modfix.sh` nicht: Es macht Verzeichnisse für alle
beschreibbar.

## Checkliste

- [ ] Ein Benutzer pro Website für PHP-FPM, Velocity-Worker, Cron und Konsole, oder eine Gruppe mit setgid-Verzeichnissen.
- [ ] `var/` und `settings/` für diesen Benutzer beschreibbar; `design/` und `extension/` nur, solange nötig.
- [ ] Code (`kernel/`, `lib/`, `vendor/`) für das PHP der Website nicht beschreibbar, wo der Tarif es erlaubt.
- [ ] Gehärtete Modi in `[FileSettings]` und `image.ini [FileSettings]`.
- [ ] Dateien in `settings/override` mit `0640`; `find settings -perm -o+w` gibt nichts aus.
- [ ] Velocity-Worker nie als root.

## Verwandte Seiten

- [Kapitel 8.7: Dateiberechtigungen und Eigentum](../../../../en/us/install/08-serving-the-site.md#87-file-permissions-and-ownership)
- [Kapitel 13.11](../../../../en/us/install/13-security-hardening.md#1311-file-permissions-and-ownership)
- [Trennung von Mandanten](../sicherheit/trennung-von-mandanten.md)
- [Aufbau des Speichers](../betrieb/aufbau-des-speichers.md)
- [Sicherheitshärtung](../../../../en/us/help/security-hardening.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
