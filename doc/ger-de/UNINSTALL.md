# Exponential 6.0 deinstallieren

Diese Seite entfernt eine Installation von Exponential 6.0 von einem Server und exportiert vorher alles, was die
Installation enthält: die Datenbank, die Inhalte in offenen Formaten, die gespeicherten Dateien und Bilder, die Pakete,
die Benutzer, die Einstellungen, die Übersetzungen und Ihren eigenen Code. Alles landet auf der Festplatte, in Formaten,
die andere Software liest, sodass ein Entwickler die Website anderswo neu aufbauen kann: auf einem anderen
Exponential, auf einem anderen System oder ganz ohne System.

Exponential hat keinerlei Herstellerbindung (Vendor-Lock-in). Keiner der folgenden Schritte braucht einen
Lizenzschlüssel, ein Herstellerkonto, ein proprietäres Werkzeug oder die Erlaubnis von irgendjemandem. Dieselben
Schritte sind der Migrationsweg: führen Sie die Abschnitte 2 und 3 aus, nehmen Sie den Export mit und hören Sie dort
auf, wenn der alte Server weiterläuft.

Jeder Befehl wird im Wurzelverzeichnis der Installation ausgeführt (dem Verzeichnis mit `index.php`, `autoload.php`,
`bin/` und `settings/`), als der Benutzer, unter dem die Website läuft. Wenn Sie ein Skript als `root` ausführen,
ergänzen Sie `--allow-root-user`. `./console` ist eine Verknüpfung auf `php bin/php/console` (siehe
[Exponential 6.0 installieren](INSTALL.md)).

## Inhalt

1. [Warum es keine Herstellerbindung gibt](#1-warum-es-keine-herstellerbindung-gibt)
2. [Bevor Sie beginnen](#2-bevor-sie-beginnen)
3. [Alle Daten exportieren](#3-alle-daten-exportieren)
4. [Anderswo neu aufbauen](#4-anderswo-neu-aufbauen)
5. [Die Dienste und die Cronjobs anhalten](#5-die-dienste-und-die-cronjobs-anhalten)
6. [Den virtuellen Host entfernen](#6-den-virtuellen-host-entfernen)
7. [Die Datenbank entfernen](#7-die-datenbank-entfernen)
8. [Die Dateien und den var-Speicher entfernen](#8-die-dateien-und-den-var-speicher-entfernen)
9. [Eine einzelne Extension entfernen](#9-eine-einzelne-extension-entfernen)
10. [Verwandte Seiten](#10-verwandte-seiten)

## 1. Warum es keine Herstellerbindung gibt

- **Freie Software.** Der Kernel steht unter der GNU General Public License, Version 2 oder jeder späteren Version
  (`"license": "GPL-2.0-or-later"` in `composer.json`; siehe [LICENSE](../../LICENSE.md)). Die Dokumentation steht unter
  der [GNU Free Documentation License 1.3](../eng-us/LICENSE-GFDL-1.3.txt). Sie haben den gesamten Quellcode, dürfen
  ihn überall betreiben und jede Zeile lesen, die Ihre Daten geschrieben hat.
- **Standard-SQL-Datenbanken.** Die Daten liegen in SQLite, PostgreSQL, MariaDB, MySQL oder Oracle (MongoDB wird
  ebenfalls unterstützt), in einfachen, dokumentierten Tabellen (`ezcontentobject`, `ezcontentobject_attribute`,
  `ezcontentobject_tree`, `ezuser` ...). Jedes Datenbankwerkzeug liest sie, und jede Datenbank hat ihr eigenes
  Dump-Werkzeug.
- **Offene Dateiformate.** Bilder und Dateien werden als gewöhnliche Dateien unter `var/` gespeichert, mit ihren
  ursprünglichen Namen und Typen. Einstellungen sind reiner INI-Text. Übersetzungen sind `.ts`-Dateien von Qt Linguist
  (XML). Formatierter Text wird als XML gespeichert. Pakete sind `.ezpkg`-Archive (tar mit XML oder JSON darin).
  Inhaltsexporte sind CSV, JSON oder XML.
- **Ihr Code bleibt Ihrer.** Extensions, Designs und Templates sind Dateien in Ihren eigenen Verzeichnissen, unter der
  Lizenz, die Sie ihnen geben.

## 2. Bevor Sie beginnen

1. **Entscheiden Sie, was Sie behalten.** Meist alles aus Abschnitt 3; mindestens den Datenbank-Dump, die
   gespeicherten Dateien und Ihre Einstellungen.
2. **Frieren Sie die Inhalte ein.** Der Wartungsmodus beantwortet Besuche mit einer 503-Seite, während Sie
   exportieren, damit sich zwischen den Teilen des Exports nichts ändert:

   ```bash
   php bin/php/maintenance.php on --message="This site is moving" --allow-root-user
   php bin/php/maintenance.php status --allow-root-user
   ```

   `--allow-admin` hält die Administration (`/admin`) erreichbar; siehe [Wartungsmodus](funktionen/6.0/wartungsmodus.md).
3. **Schaffen Sie Platz.** Der Export braucht etwa die Größe der Datenbank plus die Größe von `var/`. Schreiben Sie ihn
   in ein Verzeichnis außerhalb des Web-Roots, zum Beispiel `/srv/export/example.com`, unten `EXPORT` genannt:

   ```bash
   EXPORT=/srv/export/example.com
   mkdir -p "$EXPORT"
   ```

4. **Halten Sie die Eckdaten der Installation fest**, für wen auch immer sie neu aufbaut:

   ```bash
   ./console exp:info --json --no-logo > "$EXPORT/system-card.json"
   ./console exp:systeminfo --json > "$EXPORT/system-information.json"
   ```

## 3. Alle Daten exportieren

### 3.1 Die Datenbank

Die vollständige Datenbank, mit dem Werkzeug Ihres Datenbanksystems. Dieser Dump allein genügt, um die Website
wiederherzustellen.

| Datenbank | Export |
|---|---|
| MySQL oder MariaDB | `mysqldump --single-transaction --routines -u USER -p DATABASE > "$EXPORT/database.sql"` |
| PostgreSQL | `pg_dump -U USER DATABASE > "$EXPORT/database.sql"` |
| SQLite | `sqlite3 var/storage/sqlite3/exponential.db ".backup '$EXPORT/database.db'"` und `sqlite3 var/storage/sqlite3/exponential.db .dump > "$EXPORT/database.sql"` |

`exponential.db` ist der Standard-Dateiname von `exp:install`; verwenden Sie den Namen Ihrer eigenen Datei
(`ls var/storage/sqlite3/`).

Eine zweite, datenbankneutrale Kopie hilft, wenn das Ziel ein anderes Datenbanksystem ist. `ezsqldumpschema.php`
schreibt Schema und Daten als `.dba`-Dateien (ein PHP-Array aus Tabellen, Feldern, Indizes und Zeilen), die
`ezsqlinsertschema.php` in das SQL einer anderen Datenbank übersetzt (siehe [Abschnitt 4](#4-anderswo-neu-aufbauen)):

```bash
php bin/php/ezsqldumpschema.php --type=mysql --user=USER --output-array DATABASE "$EXPORT/db_schema.dba"
php bin/php/ezsqldumpschema.php --type=mysql --user=USER --output-array --output-types=data DATABASE "$EXPORT/db_data.dba"
```

`--type` ist `mysql` oder `postgresql` (ergänzen Sie bei Bedarf `--host`, `--port`, `--password`). Die Datendatei hält
beim Schreiben jede Zeile im Speicher: nutzen Sie sie für kleine und mittlere Websites, und den nativen Dump oben für
jede Größe.

### 3.2 Gespeicherte Dateien und Bilder

Jede hochgeladene Datei und jedes Bild ist eine Datei im var-Verzeichnis der Website (`site.ini [FileSettings]
VarDir`, zum Beispiel `var/site`): `storage/original/` enthält die hochgeladenen Dateien, `storage/images/` die
Bilder und ihre Varianten. Pakete liegen in `var/storage/packages/`, eine SQLite-Datenbank in `var/storage/sqlite3/`.

```bash
tar czf "$EXPORT/var-storage.tar.gz" --exclude='var/cache' --exclude='var/*/cache' --exclude='var/tmp' var
```

Bei einem Cluster, der seine Dateien in der Datenbank hält (`file.ini [ClusteringSettings]`), holen Sie sie zuerst
zurück auf die Festplatte; der Befehl verschiebt sie, danach kopieren Sie `var/` wie oben:

```bash
php bin/php/clusterize.php -u --allow-root-user
```

`./console exp:contentcheck --help` listet die Prüfungen, die Dateien finden, die die Datenbank nennt, die aber auf der
Festplatte fehlen. So wissen Sie vor dem Export, ob jede Datei da ist ([Fehlende Dateien](anleitungen/fehlende-dateien.md)).

### 3.3 Inhalte in offenen Formaten

Der Datenbank-Dump enthält jede Version jedes Objekts. Für andere Software exportieren Sie die Inhalte zusätzlich als
Dateien.

**CSV je Klasse, aus dem Kernel.** `ezcsvexport.php` schreibt den Teilbaum unter einem Knoten als eine CSV-Datei je
Inhaltsklasse:

```bash
php bin/php/ezcsvexport.php --storage-dir="$EXPORT/csv" 2 --allow-root-user     # Knoten 2: der Inhaltsbaum
php bin/php/ezcsvexport.php --storage-dir="$EXPORT/csv-media" 43 --allow-root-user  # Knoten 43: die Medienbibliothek
```

**Ein Archiv der ganzen Website in CSV, JSON oder XML, mit der Extension xrowextract** (in der Standard-`composer.json`;
der Reiter **Export** der Administration, wenn sie aktiv ist). Eine Datei je Klasse mit Objekt-ID, Remote-ID,
Hauptknoten, Elternknoten, URL-Alias, Daten und jedem Attribut, dazu `manifest.json`, jede Sprache als eigene Zeile:

```bash
php extension/xrowextract/bin/php/archive.php --list-sets --allow-root-user
php extension/xrowextract/bin/php/archive.php --set=everything --files=json --languages=all --format=tar.gz --output="$EXPORT" --allow-root-user
php extension/xrowextract/bin/php/archive.php --set=everything --files=xml --format=zip --output="$EXPORT" --allow-root-user
```

Die Menge `everything` ist der Inhaltsbaum, die Medienbibliothek und die Benutzer (die Knoten 2, 43 und 5 einer
Standardinstallation). `--plain-text` ergänzt den reinen Text jedes formatierten Textfelds; `--dry-run` zeigt, was
geschrieben würde. Jede Option: [xrowextract](funktionen/6.0/erweiterungen/xrowextract.md).

**JSON über HTTP, mit der REST-API** (Extension `ezprestapi`): jeder Knoten und seine Felder als JSON, für ein Skript
auf der Zielseite, das die Inhalte selbst abholt. Siehe [REST-API v1 und v2](anleitungen/rest-api-v1-und-v2.md).

### 3.4 Pakete (.ezpkg)

Ein Paket trägt Inhaltsklassen, Inhalte mit ihren Dateien und Templates in einem Archiv, das das Paketsystem jedes
Exponential importiert. Erstellen Sie eines aus dem ganzen Inhaltsbaum mit `ezpm.php`:

```bash
php ezpm.php create site-export 'Full content export' 1.0 install --allow-root-user
php ezpm.php add site-export ezcontentobject 1 --all-versions --allow-root-user
php ezpm.php export site-export -d "$EXPORT/packages" --format=json --allow-root-user
```

Knoten 1 ist die eigentliche Wurzel: alles darunter, mit den Klassen und den Template-Overrides
(`--exclude-templates` lässt sie weg). `--format=json` schreibt `package.json` und JSON-Dokumente mit Prüfsummen
([das JSON-Paketformat](bc/6.0/package-json-format.md)); ohne die Option wird das klassische XML-Format geschrieben.

Die Pakete, die schon auf der Website liegen (das Website-Paket, die Klassenpakete, Ihre eigenen), werden einzeln
exportiert:

```bash
php ezpm.php list --allow-root-user
php ezpm.php export <package> -d "$EXPORT/packages" --allow-root-user
```

Dasselbe in der Administration: **Setup** > **Pakete**, die Seite eines Pakets, Download; **Neues Paket erstellen**
erstellt Pakete mit Inhaltsobjekten und Inhaltsklassen mit einem Assistenten. Siehe [Pakete](anleitungen/pakete.md).

### 3.5 Benutzer

Benutzer sind Inhaltsobjekte der Benutzerklassen unter dem Knoten **Users** (Knoten 5 einer Standardinstallation),
daher enthalten die Exporte aus 3.3 und 3.4 sie, wenn sie Knoten 5 abdecken (die Menge `everything` tut das; mit
`ezcsvexport.php` führen Sie den Export auch für Knoten 5 aus). Der Datenbank-Dump enthält die Konten, die Rollen und
die Richtlinien (`ezuser`, `ezrole`, `ezpolicy`, `ezuser_role`).

Passwort-Hashes sind gewöhnliche Hashes von PHPs `password_hash()` (standardmäßig bcrypt), die jede PHP-Anwendung mit
`password_verify()` prüft, sodass Benutzer ihre Passwörter auf dem neuen System behalten können. xrowextract schreibt
sie nur mit `--password-hashes` und der Richtlinie `xrowextract/password_hash`; behandeln Sie eine solche Datei als
Geheimnis. Ohne sie setzen Benutzer auf dem neuen System ein neues Passwort.

### 3.6 Einstellungen

Einstellungen sind INI-Textdateien. Behalten Sie die, die Sie angelegt haben; die mitgelieferten kommen mit jeder neuen
Kopie des Codes:

```bash
tar czf "$EXPORT/settings.tar.gz" settings/override settings/siteaccess
```

`settings/override/` kann Passwörter und Schlüssel enthalten (Datenbank, Mail, API-Anbieter): bewahren Sie das Archiv
wie ein Geheimnis auf. Geänderte Einstellungen von Extensions liegen in denselben beiden Verzeichnissen. Die
Einstellungen, die jeder Siteaccess tatsächlich verwendet, zeigt auch **Setup** > **INI-Einstellungen** in der Administration
([Die Einstellungsseite](anleitungen/einstellungsseite.md)).

### 3.7 Übersetzungen

Die Übersetzungen der Oberfläche sind `.ts`-Dateien von Qt Linguist (XML), eine je Locale, in
`share/translations/<locale>/` und in `extension/<extension>/translations/<locale>/`. Behalten Sie die, die Sie angelegt
oder geändert haben:

```bash
tar czf "$EXPORT/translations.tar.gz" share/translations extension/*/translations
```

Die Übersetzungen der Inhalte sind Versionen der Objekte in jeder Sprache: sie stehen im Datenbank-Dump, im
xrowextract-Archiv (`--languages=all`, eine Zeile je Sprache) und in einem Paket (alle Sprachen, sofern `--language`
sie nicht einschränkt).

### 3.8 Ihr Code und Ihre Designs

Ihre eigenen Extensions, Designs und Templates, und die Beschreibung des Codes, den Sie betrieben haben:

```bash
tar czf "$EXPORT/code.tar.gz" extension design composer.json composer.lock
git log -1 --format='%H %d' > "$EXPORT/code-revision.txt"     # eine Git-Installation
```

### 3.9 Den Export prüfen

```bash
ls -la "$EXPORT"
(cd "$EXPORT" && find . -type f ! -name SHA256SUMS -exec sha256sum {} + > SHA256SUMS)
```

Spielen Sie den Datenbank-Dump auf einem anderen Rechner in eine leere Datenbank ein und öffnen Sie ein paar Seiten,
bevor Sie irgendetwas entfernen. Ein Export, den Sie nicht zurückgespielt haben, ist nur eine Hoffnung.

## 4. Anderswo neu aufbauen

- **Dieselbe Website auf einem anderen Server** (Exponential 6.0): holen Sie den Code
  ([Exponential 6.0 installieren](INSTALL.md), Abschnitt 3), spielen Sie den Datenbank-Dump ein, packen Sie
  `var-storage.tar.gz`, `settings.tar.gz` und `code.tar.gz` aus und führen Sie dann
  `php bin/php/ezpgenerateautoloads.php -e` und `php bin/php/ezcache.php --clear-all --allow-root-user` aus.
- **Ein anderes Datenbanksystem:** legen Sie auf der neuen Datenbank eine leere Datenbank an, fügen Sie die
  `.dba`-Dateien aus 3.1 ein (zuerst das Schema, dann die Daten) und richten Sie `site.ini [DatabaseSettings]` in
  `settings/override/` darauf aus:

  ```bash
  php bin/php/ezsqlinsertschema.php --type=postgresql --user=USER db_schema.dba NEWDATABASE
  php bin/php/ezsqlinsertschema.php --type=postgresql --user=USER --insert-types=data --schema-file=db_schema.dba db_data.dba NEWDATABASE
  ```

- **Inhalte in eine frische Installation:** importieren Sie das `.ezpkg` aus 3.4 (`php ezpm.php import <file>.ezpkg`
  oder **Setup** > **Pakete** > **Neues Paket importieren**), oder laden Sie die CSV-, JSON- oder XML-Dateien aus 3.3
  mit der Extension `sqliimport` (mehr als 60 Quellformate, in der Standard-`composer.json`) oder mit **Import content
  file** von xrowextract.
- **Ein anderes System:** die CSV-, JSON- und XML-Dateien, die `.ts`-Übersetzungen, die Bilddateien und der SQL-Dump
  sind mit jeder Sprache und jedem Importwerkzeug lesbar. Die Tabellenstruktur dokumentiert das Schema selbst
  (`share/db_schema.dba`) und der `.dba`-Export.

## 5. Die Dienste und die Cronjobs anhalten

1. **Die Cronjobs.** Entfernen Sie den Block, den der Installer geschrieben hat, und suchen Sie dann nach Zeilen, die
   Sie selbst ergänzt haben:

   ```bash
   php bin/php/installcrontab.php --remove --dry-run --allow-root-user
   php bin/php/installcrontab.php --remove --apply --allow-root-user
   crontab -l | grep -n "$(pwd)"
   ```

   Siehe [Der Cronjob der Installation](bc/6.0/crontab-bei-der-installation.md).
2. **Exponential Velocity**, wenn es die Website ausliefert (sein Scheduler hält mit an):

   ```bash
   ./console exp:velocity stop --allow-root-user
   ./console exp:velocity status --allow-root-user
   ```

   Wenn Sie einen Boot-Dienst aus der Beispiel-Unit eingerichtet haben, deaktivieren Sie ihn:
   `systemctl disable --now <your-unit>`.
3. **Hintergrund-Worker**, die Sie für die Job-Warteschlange oder andere Extensions gestartet haben (eine
   systemd-Unit oder ein Supervisor-Programm, das `runcronjobs.php` oder einen Konsolenbefehl ausführt): anhalten und
   deaktivieren.
4. **PHP-FPM**: entfernen Sie den Pool dieser Website, oder laden Sie den Dienst neu, nachdem Sie die Website in
   Abschnitt 6 entfernt haben.

## 6. Den virtuellen Host entfernen

- **Velocity:** deaktivieren Sie die Website und entfernen Sie danach ihre Datei aus `sites-available/`
  (`./console exp:velocity layout --allow-root-user` zeigt jede Datei, die es verwendet):

  ```bash
  ./console exp:velocity site disable --apply <name> --allow-root-user
  ```

- **Apache, nginx oder ein Hosting-Panel:** entfernen Sie den virtuellen Host oder den Server-Block der Website, ihren
  PHP-FPM-Pool und die nur für sie erstellten Zertifikate; prüfen Sie die Konfiguration (`apachectl configtest`,
  `nginx -t`) und laden Sie neu.
- **DNS:** richten Sie die Namen anderswohin oder entfernen Sie sie, sobald die neue Website antwortet.

## 7. Die Datenbank entfernen

Erst nachdem der Export anderswo zurückgespielt wurde (3.9). Entfernen Sie die Datenbank und den Datenbankbenutzer der
Website, und sonst nichts:

| Datenbank | Entfernen |
|---|---|
| MySQL oder MariaDB | `mysql -u root -p -e "DROP DATABASE DATABASE; DROP USER 'USER'@'localhost';"` |
| PostgreSQL | `dropdb -U postgres DATABASE` und `dropuser -U postgres USER` |
| SQLite | die Datenbankdatei unter `var/storage/sqlite3/` geht mit den Dateien in Abschnitt 8 |

Eine Suchmaschine mit eigenem Index (Solr für die Extension ezfind) wird mit ihrem Core oder ihrer Collection entfernt.

## 8. Die Dateien und den var-Speicher entfernen

Alles der Installation liegt unter ihrem Wurzelverzeichnis: der Code, `vendor/`, `extension/`, `settings/` und `var/`
(der Speicher, die Caches, die Logs und eine SQLite-Datenbank). Entfernen Sie zuerst den var-Speicher, wenn er auf einem
anderen Volume liegt (`VarDir` kann außerhalb des Wurzelverzeichnisses zeigen), dann das Wurzelverzeichnis selbst. Nichts
wird anderswo geschrieben, außer:

- der Konfiguration von Velocity in `/etc/vc/` (die Dateien dieser Website in `sites-available/`) und seinem Zustand
  in `/var/lib/vc/`, `/var/log/vc/` und `/var/cache/vc/`, wenn Velocity die Website ausgeliefert hat;
- dem virtuellen Host, dem PHP-FPM-Pool und den Cron-Zeilen aus den Abschnitten 5 und 6;
- Sicherungen und Exporten, die Sie selbst angelegt haben.

## 9. Eine einzelne Extension entfernen

Um eine Extension zu entfernen und die Installation zu behalten:

1. Exportieren Sie, was sie enthält (ihre Tabellen stehen im Datenbank-Dump; viele Extensions haben einen eigenen
   Export, zum Beispiel Pakete für Klassen oder die CSV-Exporte des Newsletters).
2. Entfernen Sie sie aus `ActiveExtensions[]` oder `ActiveAccessExtensions[]` in
   `settings/override/site.ini.append.php` und in den Siteaccesses.
3. Entfernen Sie ihre Tabellen, wenn sie einen Deinstallationsbefehl hat (zum Beispiel
   `./console exp:help:content uninstall --apply` für die als Inhalt gepflegte Hilfe); lesen Sie ihre eigene Dokumentation zu
   den Tabellen, die sie angelegt hat.
4. Entfernen Sie ihren Code: `composer remove se7enxweb/<package>`, wenn Composer sie installiert hat, oder ihr
   Verzeichnis unter `extension/`.
5. Bauen Sie die Klassenzuordnung neu auf und leeren Sie die Caches:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

   Mit Velocity erledigt `./console exp:velocity deploy --allow-root-user` beides und startet die Worker neu.

## 10. Verwandte Seiten

- [Exponential 6.0 installieren](INSTALL.md) und [Exponential 6.0 aktualisieren](UPDATE.md).
- [Betrieb einer Website](anleitungen/betrieb-einer-website.md): sichern und wiederherstellen.
- [Pakete](anleitungen/pakete.md) und [das JSON-Paketformat](bc/6.0/package-json-format.md).
- [xrowextract](funktionen/6.0/erweiterungen/xrowextract.md): Inhalte als Dateien exportieren, importieren und
  verschieben.
- [REST-API v1 und v2](anleitungen/rest-api-v1-und-v2.md).
- [Über die Lizenz dieser Dokumentation](README-LIZENZ.md).
