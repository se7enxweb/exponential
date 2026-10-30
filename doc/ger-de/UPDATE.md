# Exponential 6.0 aktualisieren

Diese Seite aktualisiert eine laufende Installation von Exponential 6.0 auf ein neueres 6.0.x-Release oder auf den
aktuellen Stand der Linie 6.0: sichern, den neuen Code einspielen, die Datenbank aktualisieren, die Klassenzuordnung
und die Caches neu aufbauen, das Ergebnis prüfen und bei einem Fehler zurückrollen. Sie ist das kurze, praktische
Vorgehen für Entwickler und Administratoren. Eine Installation, die älter als 6.0 ist (4.x, 5.x, 5.90 oder
LoveStack), folgt [Upgrade](anleitungen/aktualisieren.md); dort gibt es für jede einen eigenen Teil, der mit denselben
Schritten endet wie diese Seite.

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](beispiele/webserver/nginx.md), [LiteSpeed](beispiele/webserver/litespeed.md), [Caddy](beispiele/webserver/caddy.md), [IIS](beispiele/webserver/iis.md),
> [Velocity](beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](hilfe/hosting/control-panels/plesk.md), [cPanel](hilfe/hosting/control-panels/cpanel.md), [DirectAdmin](hilfe/hosting/control-panels/directadmin.md), [ISPConfig](hilfe/hosting/control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](anleitungen/seiten-cache-mit-litespeed.md).

Jeder Befehl wird im Wurzelverzeichnis der Installation ausgeführt (dem Verzeichnis mit `index.php`, `autoload.php`,
`bin/` und `settings/`), als der Benutzer, unter dem die Website läuft. Wenn Sie ein Skript als `root` ausführen,
ergänzen Sie `--allow-root-user`. `./console` ist eine Verknüpfung auf `php bin/php/console` (siehe
[Exponential 6.0 installieren](INSTALL.md)).

## Inhalt

1. [Bevor Sie beginnen](#1-bevor-sie-beginnen)
2. [Sichern](#2-sichern)
3. [Die Installation mit der Upgrade-Prüfung prüfen](#3-die-installation-mit-der-upgrade-prüfung-prüfen)
4. [Den neuen Code einspielen](#4-den-neuen-code-einspielen)
5. [Die Datenbank aktualisieren](#5-die-datenbank-aktualisieren)
6. [Autoloads neu erzeugen und die Caches leeren](#6-autoloads-neu-erzeugen-und-die-caches-leeren)
7. [Die Aktualisierung prüfen](#7-die-aktualisierung-prüfen)
8. [Zurückrollen](#8-zurückrollen)
9. [Verwandte Seiten](#9-verwandte-seiten)

## 1. Bevor Sie beginnen

- **Ermitteln Sie Ihre Version.** **Setup** > **Systeminformationen** in der Administration nennt sie, ebenso
  `php bin/php/console --version` (zum Beispiel `console (Exponential) 6.0.15stable`). Bewahren Sie die vollständige
  Systemkarte für einen Vergleich danach auf:

  ```bash
  ./console exp:info --json --no-logo > before.json
  ```

- **Lesen Sie die Release-Notes jeder Version, die Sie überspringen.** Sie liegen in `doc/changelogs/6.0/` (eine Datei
  je Release, zum Beispiel [Changelog 6.0.15](../changelogs/6.0/6.0.15.md)). Jede nennt die Datenbankaktualisierung,
  die einmalig auszuführenden Skripte und die zu prüfenden Verhaltensänderungen. Die Checkliste je Zeitraum steht in
  [Upgrade, Teil C3](anleitungen/aktualisieren.md).
- **Probieren Sie es zuerst an einer Kopie.** Spielen Sie die Sicherung aus Schritt 2 auf einem Staging-Rechner ein,
  aktualisieren Sie dort und erst danach die Live-Website.
- **Planen Sie ein Wartungsfenster.** Der Wartungsmodus beantwortet jeden Besuch mit einer 503-Seite, während Sie
  arbeiten:

  ```bash
  php bin/php/maintenance.php on --message="Back soon" --until=30m --allow-root-user
  php bin/php/maintenance.php status --allow-root-user
  ```

  Siehe [Wartungsmodus](funktionen/6.0/wartungsmodus.md).

## 2. Sichern

Die Datenbank und die Dateien, zusammen genommen, mit der Website im Wartungsmodus. Setzen Sie Ihren eigenen
Benutzer, Ihre Datenbank und Ihre Dateinamen ein; `-p` lässt den Client nach dem Passwort fragen.

```bash
mysqldump --single-transaction -u USER -p DATABASE > backup-before-update.sql      # MySQL oder MariaDB
pg_dump -U USER DATABASE > backup-before-update.sql                               # PostgreSQL
sqlite3 var/storage/sqlite3/exponential.db ".backup 'backup-before-update.db'"     # SQLite: der Pfad Ihrer Datenbankdatei
tar czf backup-files-before-update.tar.gz var settings/override settings/siteaccess extension design composer.json composer.lock
```

Halten Sie fest, welchen Code Sie jetzt betreiben, damit er sich genau so wiederherstellen lässt:

```bash
git rev-parse HEAD > backup-code-revision.txt      # eine Git-Installation
cp composer.lock backup-composer.lock              # ein Composer-Projekt
```

Eine Sicherung, die Sie nie zurückgespielt haben, ist nur eine Hoffnung: spielen Sie sie einmal auf einem anderen
Rechner zurück. Mehr dazu: [Betrieb einer Website](anleitungen/betrieb-einer-website.md), Abschnitt „Sichern und
wiederherstellen“.

## 3. Die Installation mit der Upgrade-Prüfung prüfen

Finden Sie vor der Aktualisierung heraus, was vom Release abweicht, das Sie betreiben. So wissen Sie, was die
Aktualisierung berührt und welche lokalen Änderungen Sie mitnehmen müssen.

- In der Administration: **Setup** > **Upgrade-Prüfung** (`/setup/systemupgrade`). **Überprüfe Dateikonsistenz**
  vergleicht jede in `share/filelist.md5` aufgeführte Datei mit ihrer Prüfsumme; **Überprüfe Datenbankkonsistenz**
  vergleicht Tabellen, Felder und Indizes mit den Schemadateien. Beide Prüfungen lesen nur.
- Die Dateiprüfung aus der Shell, mit denselben Befunden:

  ```bash
  php bin/php/checkmanifest.php --all --extensions
  ```

Eine als **Geändert** gemeldete Datei, die Sie absichtlich geändert haben, gehört in eine Extension oder nach
`settings/override/`, nicht in den Kernel; verlegen Sie die Änderung vor der Aktualisierung dorthin, sonst wird sie
überschrieben. Jeder Befund ist in [Die Upgrade-Prüfung](anleitungen/upgrade-pruefung.md) erklärt.

## 4. Den neuen Code einspielen

Wählen Sie den Weg, auf dem Sie installiert haben. Lassen Sie nie ein Werkzeug `settings/override/`,
`settings/siteaccess/`, Ihre eigenen Extensions oder Ihre eigenen Designs überschreiben.

**Ein Composer-Projekt** (der Kernel ist das Paket `se7enxweb/exponential`): Setzen Sie bei Bedarf die
Versionsbedingung in der `composer.json` Ihres Projekts und aktualisieren Sie dann das Kernel-Paket:

```bash
composer update se7enxweb/exponential
```

Mit Composer installierte Extensions werden genauso aktualisiert, ein Paket nach dem anderen, nachdem Sie ihre
Release-Notes gelesen haben (`composer update se7enxweb/<package>`; siehe `doc/changelogs/extensions/`).

**Ein Git-Klon** (der Kernel ist `https://github.com/se7enxweb/exponential.git`): holen Sie den Stand, wechseln Sie
auf den Release-Tag oder führen Sie den Zweig in Ihren eigenen Zweig zusammen und installieren Sie dann die
Bibliotheken, die `composer.lock` nennt:

```bash
git fetch --tags
git tag -l 'v6.0.*' --sort=version:refname | tail -5   # die neuesten Releases; Tags nie nur nach Namen sortieren
git merge v6.0.14                                     # oder: git merge origin/main, für den aktuellen Stand der Linie
composer install
```

**Exponential Velocity** ist ein eigenes Composer-Paket (`se7enxweb/exponential-velocity`) und wird mit dem Rest
aktualisiert. Nachdem sich der Code geändert hat, müssen `composer.json` und `composer.lock` zusammenpassen: ein
`composer install`, das ohne Fehler endet, beweist es.

**Vergleichen Sie Ihre Dateien mit den neuen Vorlagen.** Eine Release kann `.htaccess_root`,
`.htaccess_root_static` und `config.php-RECOMMENDED` ändern; Ihre `.htaccess` und `config.php` bleiben, wie sie sind.
Prüfen Sie sie (ohne `--apply` wird nichts geschrieben):

```bash
./console exp:setup:status                     # ist .htaccess noch eine Kopie einer Vorlage?
./console exp:setup:htaccess                   # der Diff zwischen Ihrer .htaccess und der neuen Vorlage
./console exp:setup:htaccess --force --apply   # die neuen Regeln übernehmen (vorher Sicherung), wenn der Diff nichts Eigenes enthält
./console exp:config:list                      # Einstellungen, die neu in config.php-RECOMMENDED sind, erscheinen als (not set)
```

Setzen Sie die neuen Einstellungen, die Sie möchten, mit `./console exp:config:set <NAME> <value> --apply`; die
Einstellungen, die Sie hatten, behalten ihre Werte. Verwenden Sie `--static` mit `exp:setup:htaccess`, wenn die
Website den statischen Cache ausliefert. Siehe
[.htaccess und config.php von der Konsole aus einrichten](anleitungen/setup-dateien-von-der-konsole.md).

## 5. Die Datenbank aktualisieren

Die Dateien der Datenbankaktualisierung liegen in `update/database/<engine>/6.0/`:

| Datenbank | Datei für jede 6.0.x auf die aktuelle Linie |
|---|---|
| MySQL oder MariaDB | `update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql` |
| PostgreSQL | `update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql` |
| SQLite | `update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql` |

Spielen Sie die Datei einmal ein, vollständig und in ihrer Reihenfolge:

```bash
mysql -u USER -p DATABASE < update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql
psql -U USER DATABASE < update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql
sqlite3 var/storage/sqlite3/exponential.db < update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql
```

Hat Ihre Datenbank eine Spalte, die die Datei hinzufügt, schon, schlägt genau diese Anweisung fehl und nennt sie;
überspringen Sie nur diese Anweisung. Eine Datei, die mittendrin fehlschlägt, wird nie von Hand repariert: spielen Sie
die Sicherung zurück und die Datei noch einmal von vorn ein.

Führen Sie danach die einmaligen Skripte aus, die die Release-Notes nennen. Für die Linie 6.0.15 liegen sie in
`update/common/scripts/6.0/`:

```bash
php update/common/scripts/6.0/createaudittables.php
php update/common/scripts/6.0/movetrashrecords.php --remove-file
./console exp:user-login-check --allow-root-user                 # listet Anmeldenamen, die zwei Konten teilen
./console exp:user-login-check --fix-index --allow-root-user     # legt den eindeutigen Index an, sobald es keine mehr gibt
```

`./console exp:checkdbfiles --allow-root-user` prüft, ob alle Aktualisierungsdateien Ihrer Linie vorhanden sind. Die
Versionszeile danach (die Zeilennamen behalten absichtlich ihre historische Schreibweise):

```bash
mysql -u USER -p DATABASE -e "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
```

Die vollständige Beschreibung dieses Schritts, mit den Sonderfällen einer Website, die von 5.x kam, steht in
[Upgrade, Teil C2](anleitungen/aktualisieren.md).

## 6. Autoloads neu erzeugen und die Caches leeren

Bauen Sie die Klassenzuordnung neu auf und leeren Sie dann alle Caches, in dieser Reihenfolge:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
```

`-e` ordnet die Klassen der Extensions zu. Wenn sich der Kernel selbst geändert hat, ergänzen Sie `-k` und schließen
Sie jede zusätzliche Arbeitskopie des Codes im Wurzelverzeichnis (zum Beispiel einen Git-Worktree-Ordner) mit
`--exclude='<folder>'` aus, sonst werden Kernel-Klassen in diese Kopie abgebildet.

Sorgen Sie dann dafür, dass die laufenden PHP-Prozesse den neuen Code laden, und leeren Sie zuletzt die Caches vor PHP:

```bash
systemctl reload <your-php-fpm-service>                     # Apache oder nginx mit PHP-FPM
./console exp:velocity deploy --dry-run --allow-root-user    # Velocity: zeigt jeden Schritt
./console exp:velocity deploy --allow-root-user              # Velocity: Autoloads, Caches, Reload, Neustart, in Reihenfolge
./console exp:velocity cache clear --apply --allow-root-user         # der Antwort-Cache von Velocity
```

Leeren Sie den Content-View-Cache **nach** dem Reload (`php bin/php/ezcache.php --clear-tag=content --allow-root-user`):
eine Seite, die der alte Code zwischen dem Leeren und dem Reload erzeugt, würde sonst erneut zwischengespeichert. Bauen
Sie nach einer Aktualisierung auch die Handbücher der Hilfe neu:
`./console exp:help:content build-manuals --apply -s admin --release`.

## 7. Die Aktualisierung prüfen

```bash
./console exp:checkclasses --allow-root-user                 # jede Klasse lädt unter diesem PHP
./console exp:info --json --no-logo > after.json             # Vergleich mit before.json: jede geänderte Version
php bin/php/checkmanifest.php --all --extensions             # keine geänderte oder fehlende Datei, die Sie nicht selbst geändert haben
php bin/php/maintenance.php off --allow-root-user
```

Dann im Browser: die Startseite lädt, Sie können sich in der Administration anmelden, **Setup** >
**Systeminformationen** zeigt die neue Version, **Setup** > **Upgrade-Prüfung** besteht beide Prüfungen (Ihre eigenen,
absichtlichen Änderungen ausgenommen), und ein Testinhalt, den Sie veröffentlichen, erscheint auf der Website. Lesen Sie
`var/log/error.log` auf Neues durch.

## 8. Zurückrollen

Legen Sie vor dem Beginn fest, an welchem Punkt Sie zurückrollen: wenn Schritt 5 oder 7 fehlschlägt und die Ursache
innerhalb des Wartungsfensters nicht klar wird. Ein Zurückrollen stellt die drei Dinge wieder her, die die
Aktualisierung geändert hat, in dieser Reihenfolge:

1. **Der Code.** Git: `git checkout` der Revision aus `backup-code-revision.txt` (oder setzen Sie Ihren Zweig darauf
   zurück), dann `composer install`. Composer: legen Sie `backup-composer.lock` als `composer.lock` zurück, dann
   `composer install`.
2. **Die Datenbank.** Spielen Sie den Dump aus Schritt 2 in eine leere Datenbank ein (MySQL oder MariaDB:
   `mysql -u USER -p DATABASE < backup-before-update.sql`; PostgreSQL: `psql -U USER DATABASE < backup-before-update.sql`;
   SQLite: kopieren Sie `backup-before-update.db` mit der Website im Wartungsmodus zurück auf die Datenbankdatei).
   Lassen Sie den alten Code nie auf der aktualisierten Datenbank laufen: beide gehören zusammen.
3. **Die Dateien** in `var/` nur, wenn die Aktualisierungsskripte sie geändert haben (`movetrashrecords.php
   --remove-file` entfernt `trashed.json`): packen Sie sie aus `backup-files-before-update.tar.gz` aus.

Wiederholen Sie dann Schritt 6 (Autoloads, Caches, PHP-FPM-Reload oder `exp:velocity deploy`) und Schritt 7 mit der
alten Version. Schreiben Sie auf, was fehlgeschlagen ist: der nächste Versuch beginnt dort. Ein veröffentlichtes
Release wird nie geändert; eine Korrektur kommt als nächste Version, daher führt der Weg nach einem Zurückrollen über
das nächste Release, nicht über eine geflickte Kopie dieses Releases.

| Symptom | Was zu tun ist |
|---|---|
| Eine weiße Seite nach dem Codewechsel | `var/log/error.log`; Schritt 6 erneut ausführen; `vendor/` muss zu `composer.lock` passen (`composer install`) |
| `Class ... not found` | `php bin/php/ezpgenerateautoloads.php -e`, dann `php bin/php/ezcache.php --clear-all --allow-root-user` |
| Eine Änderung erscheint unter Velocity nicht | `./console exp:velocity restart --allow-root-user`, dann `./console exp:velocity cache clear --apply --allow-root-user` |
| Eine SQL-Datei ist mittendrin fehlgeschlagen | die Sicherung aus Schritt 2 zurückspielen und die Datei noch einmal von vorn einspielen |
| Bibliotheken fehlen nach einem fehlgeschlagenen Composer-Lauf | [Eine Installation reparieren](bc/6.0/reparatur.md) |

## 9. Verwandte Seiten

- [Exponential 6.0 installieren](INSTALL.md): eine saubere Installation derselben Version, für eine Staging-Kopie.
- [Upgrade](anleitungen/aktualisieren.md): von 4.x, 5.x, 5.90 oder LoveStack, und jede Checkliste je Zeitraum von 6.0.
- [Exponential 6.0 deinstallieren](UNINSTALL.md): der vollständige Datenexport, dann das Entfernen.
- [Die Upgrade-Prüfung](anleitungen/upgrade-pruefung.md) und [das Datei-Manifest](funktionen/6.0/dateikonsistenzpruefung.md).
- [.htaccess und config.php von der Konsole aus einrichten](anleitungen/setup-dateien-von-der-konsole.md): beide Dateien mit den neuen Vorlagen vergleichen.
- [Betrieb einer Website](anleitungen/betrieb-einer-website.md): Sicherungen, Caches, Cronjobs und Reparaturen.
- [Bereitstellung](anleitungen/bereitstellung.md) und [Velocity-Engines](bc/6.0/velocity-engines.md): das richtige PHP-FPM neu laden.
- Das Installationsbuch: [Aktualisieren](installation/11-aktualisieren.md).
