# Upgrade mit xne (Exponential Network)

Diese Anleitung erklärt **xne**, das Upgrade-Werkzeug von Exponential (Produktname Exponential Network): ein Befehl,
der eine Installation untersucht, ihr Upgrade plant, eine geprüfte Sicherung anlegt, Dateien, Bibliotheken und Datenbank
auf das neue Release bringt, das Ergebnis prüft und alles von selbst zurückrollt, wenn ein Schritt fehlschlägt. Sie
richtet sich an Administratoren, die Installationen aktualisieren, eine oder viele, unter Linux, macOS oder Windows.

xne ersetzt die Werkzeuge des Kernels nicht, es steuert sie. Die Schritte für Datenbank und Einstellungen sind weiterhin
[`exp:upgrade`](aktualisieren-mit-der-konsole.md) des Kernels (oder, bei Releases ohne diesen Befehl, die SQL-Dateien in
`update/database/<engine>/`), die Autoloads weiterhin `ezpgenerateautoloads.php`, die Caches weiterhin `ezcache.php`.
xne fügt alles darum herum hinzu: den Plan, die geprüfte Sicherung, die Dateiänderungen, die Gesundheitsprüfung und den
Weg zurück.

Die Angaben auf dieser Seite sind die von **xne 0.3.0.10**, erschienen am 26. November 2026, dem Tag, an dem xne
öffentlich wurde.

[Anleitungen](README.md) · Verwandt: [Upgrade](aktualisieren.md), [Upgrade mit der Konsole](aktualisieren-mit-der-konsole.md),
[11. Upgrade](../installation/11-aktualisieren.md), [Die Upgrade-Prüfung](upgrade-pruefung.md),
[Exponential unter Windows](../hilfe/exponential-unter-windows.md), [Eine Datenbank wählen](../hilfe/datenbank-waehlen.md)

## Kurz

```bash
xne detect   --root=/var/www/example.com     # was die Installation ist: Version, Art, PHP, Datenbank, Eigentümer
xne traps    --root=/var/www/example.com     # bekannte Upgrade-Fallen, jede mit ihrer Abhilfe
xne plan     --root=/var/www/example.com     # was sich ändern würde; nichts wird angefasst
xne upgrade  --root=/var/www/example.com --dry-run
xne upgrade  --root=/var/www/example.com     # geprüfte Sicherung, Upgrade, Gesundheitsprüfung, Rollback bei Fehlern
xne backups  --root=/var/www/example.com     # die Sicherungen, neueste zuerst
xne rollback --root=/var/www/example.com     # Dateien und Datenbank zurück auf die neueste Sicherung
```

Jeder Befehl antwortet mit `PASS`, `FAIL` oder `REFUSED` und einem festen Exit-Code (0 PASS, 1 FAIL, 2 Aufruffehler,
3 abgelehnt) und gibt mit `--json` ein JSON-Objekt aus.

## Inhalt

1. [Wann xne, wann exp:upgrade allein](#wann-xne-wann-expupgrade-allein)
2. [Voraussetzungen](#voraussetzungen)
3. [xne installieren](#xne-installieren)
4. [Ein erstes Upgrade, Schritt für Schritt](#ein-erstes-upgrade-schritt-für-schritt)
5. [Was bei einem Upgrade geschieht](#was-bei-einem-upgrade-geschieht)
6. [Kanäle und Ziele](#kanäle-und-ziele)
7. [Datenbanken](#datenbanken)
8. [Fallen](#fallen)
9. [Zurückrollen](#zurückrollen)
10. [Der Sicherungsordner](#der-sicherungsordner)
11. [Mehrere Installationen](#mehrere-installationen)
12. [Windows](#windows)
13. [Prüfungen und Benachrichtigungen](#prüfungen-und-benachrichtigungen)
14. [Befehle und Optionen](#befehle-und-optionen)
15. [Was xne nie tut](#was-xne-nie-tut)
16. [Grenzen](#grenzen)

## Wann xne, wann exp:upgrade allein

| Sie wollen | Verwenden Sie |
|---|---|
| Eine 6.0.x-Installation auf ein neueres Release bringen: Code, Bibliotheken, Datenbank und Einstellungen, mit einem Weg zurück | `xne upgrade` |
| Die Schritte für Datenbank und Einstellungen ausführen, nachdem Sie den neuen Code selbst bereitgestellt haben (eine Deployment-Pipeline, ein git pull) | `./console exp:upgrade` ([Anleitung](aktualisieren-mit-der-konsole.md)) |
| Sehen, was ein Upgrade täte, ohne etwas zu ändern | `xne plan`, oder `./console exp:upgrade:status` nur für die Datenbankschritte |
| Ein Upgrade rückgängig machen, Dateien und Datenbank | `xne rollback` |

xne ruft `exp:upgrade` selbst auf, wenn die Installation den Befehl hat, also kommen sich beide nie in die Quere:
`exp:upgrade` protokolliert, was es angewendet hat, und xne liest dieses Protokoll (`exp:upgrade:status`) in seiner
Gesundheitsprüfung.

## Voraussetzungen

- **PHP 7.4 bis 8.5** auf der Kommandozeile, mit `zlib` (Archive und Sicherungen) und `json`; `curl` oder `openssl` für
  Downloads über HTTPS.
- **Der PDO-Treiber der Datenbank der Installation** im PHP, das xne ausführt: `pdo_mysql`, `pdo_pgsql` oder
  `pdo_sqlite`; für MongoDB die Extension `mongodb`, für Oracle `oci8`. Fehlt er dem PHP im `PATH`, nennen Sie eines, das
  ihn hat, mit `--php=/pfad/zu/php` oder der Umgebungsvariable `XNE_PHP`.
- **git**, nur für Installationen, die ein git-Checkout sind (die git-Methode).
- **Schreibzugriff** auf die Installation, als ihr Eigentümer oder als root bzw. Administrator: xne führt die Werkzeuge
  der Installation dann als Eigentümer von `index.php` aus.

## xne installieren

xne ist ein Ordner mit PHP-Dateien unter GPL-2.0-or-later; nichts wird kompiliert. Releases tragen Tags `vX.Y.Z.N` auf
[github.com/se7enxweb/xne](https://github.com/se7enxweb/xne) und erscheinen als Composer-Paket `se7enxweb/xne`. Nehmen
Sie das neueste Tag, nach Version sortiert und nie nach Namen (`v0.3.0.10` kommt nach `v0.3.0.9`).

**Linux und BSD:**

```bash
sudo git clone https://github.com/se7enxweb/xne.git /opt/xne
cd /opt/xne && sudo git checkout "$(git tag -l 'v*' --sort=version:refname | tail -1)"
sudo ./install.sh            # zeigt, was es tun würde
sudo ./install.sh --apply    # verlinkt /usr/local/bin/xne, legt /etc/xne, /var/lib/xne (0700), /var/cache/xne an
xne version
```

`install.sh` entfernt nie etwas; führen Sie es nach jedem Update erneut aus (`git fetch --tags`, das neue Tag
auschecken, `sudo ./install.sh --apply`). Als root hält xne seinen Zustand in `/var/lib/xne` und seinen Release-Cache in
`/var/cache/xne`, als anderer Benutzer in `~/.xne/lib` und `~/.xne/cache`.

**macOS:** nach `~/xne` klonen, `~/xne/bin/xne` nach `/usr/local/bin` verlinken (oder `~/xne/bin` in den `PATH`
aufnehmen) und das PHP der Site mit `--php=` oder `XNE_PHP` nennen (Homebrews `php` oder das von MAMP). Zustand und
Cache: `~/.xne/`.

**Windows:** siehe [Windows](#windows) unten.

## Ein erstes Upgrade, Schritt für Schritt

Richten Sie `--root` auf die Installation: den Ordner mit `lib/version.php`, `kernel/` und `settings/` (oder starten
Sie xne darin). Die ersten vier Schritte ändern nichts.

**1. Was ist die Installation?**

```bash
xne detect --root=/var/www/example.com
```

Prüfen Sie die Version, die Art der Installation (`git`-Installationen werden vorgespult, `composer`- und reine
`files`-Installationen per Dateipatch aktualisiert), die Datenbankzeile (die Datenbank muss erreichbar sein) und die
gefundenen Werkzeuge des Kernels.

**2. Die Fallen.**

```bash
xne traps --root=/var/www/example.com
```

Eine Zeile `block` lehnt das Upgrade ab, bis sie behoben ist oder die verantwortliche Person diese Falle über ihre ID
annimmt (`--accept=<id>`). Eine Zeile `warn` verlangt eine Handlung nach dem Upgrade. Siehe [Fallen](#fallen).

**3. Der Plan.**

```bash
xne plan --root=/var/www/example.com                    # das neueste stabile Release derselben Hauptversion
xne plan --root=/var/www/example.com --to=v6.0.15       # ein genaues Release
```

Lesen Sie, was er aufführt:

- **files**: wie viele Dateien hinzukommen, aktualisiert und ausgemustert werden.
- **local edits**: auf diesem Server geänderte Dateien. Jede wird in den Ordner `kept/` der Sicherung kopiert und dann
  durch die Fassung des Releases ersetzt; planen Sie das Zusammenführen jeder Datei (eine Template-Änderung, ein Hotfix)
  vor oder direkt nach dem Upgrade.
- **protected**: Dateien des Releases, die xne nie schreibt: `settings/override`, `settings/siteaccess`, `var`,
  `config.php`, `.htaccess` und Ähnliche. Vergleichen Sie sie von Hand, wenn ein Release-Hinweis es verlangt.
- **Dateien von Composer**: in einer Installation, deren Bibliotheken Composer verwaltet
  (`vendor/composer/installed.json`), werden die Dateien in `vendor/`, im Installationspfad jedes Pakets und in den
  Extension-Ordnern, die `composer.lock` nennt, vom Dateipatch nie geschrieben oder ausgemustert.
- **composer.json**: folgt der Datei des Releases, zusammengeführt: die Anforderungen des Releases, dazu die eigenen
  Extension-Pakete der Site, die das Release nicht mehr verlangt, mit `>=` ihrer gesperrten Version; ein Release, das
  eine Extension fallen lässt, deinstalliert also nie eine, die die Site verwendet.
- **database**: `kernel` heißt, `exp:upgrade` des Kernels läuft (seine Stufen werden aufgeführt); `sql` nennt die
  Dateien aus `update/database/<engine>/`, die laufen, in ihrer Reihenfolge. Ändert sich `composer.json`, läuft zuerst
  die Stufe `libraries`.
- **PHP-Extensions**: seit 0.3.0.10 prüft der Plan die PHP-Extensions, die das Ziel-Release verlangt (`ext-*` seiner
  `composer.json`, die Plattform seiner `composer.lock` und jedes Paket, das die Lock-Datei installiert), gegen das PHP,
  mit dem seine Werkzeuge und Composer laufen, und blockiert mit der Falle `php-extension-missing`, bevor sich etwas
  ändert; er nennt jede Extension, wer sie verlangt, die geladene `php.ini` und die Zeile, die hinzuzufügen ist (zum
  Beispiel `extension=sockets`: der Hauptzweig von Exponential verlangt `ext-sockets`).
- **blocked**: alles hier lehnt das Upgrade ab.

**4. Der Probelauf.**

```bash
xne upgrade --root=/var/www/example.com --dry-run
```

**5. Das Upgrade.**

```bash
xne upgrade --root=/var/www/example.com
```

Es gibt jeden Schritt aus (`.. backup: archiving`, `.. files: writing`, `.. kernel: exp:upgrade`, ...) und endet mit
einem von:

- `PASS: upgraded 6.0.14 -> 6.0.15 (backup 20261124-031502-a1b2)`: fertig. Notieren Sie die Sicherungs-ID.
- `FAIL: <reason>; rolled back to backup <id>`: vom Upgrade ist nichts übrig, die Installation läuft wie zuvor.
  Beheben Sie die Ursache und planen Sie erneut.
- `REFUSED: ...`: eine Sicherheitsprüfung hat es gestoppt, bevor sich etwas geändert hat.

**6. Nach dem Upgrade.**

1. Öffnen Sie Site und Administration, melden Sie sich an, sehen Sie sich einige Seiten an.
2. Führen Sie die als beiseitegelegt aufgeführten lokalen Änderungen zusammen (Kopien in `<sicherung>/kept/`).
3. Handeln Sie bei jeder Falle `warn` (zum Beispiel einen View-Cache in Redis oder Valkey leeren).
4. Laden Sie die PHP-Prozesse neu, die noch die alten Klassen halten: PHP-FPM, LiteSpeed, oder Velocity mit
   `./console exp:velocity deploy` (bei einer Installation mit `finish=velocity` führt xne es selbst aus).
5. **Setup > Upgrade check** in der Administration sollte keine geänderte Kernel-Datei zeigen: die
   `share/filelist.md5` des Releases kam mit ihm.

**Besucher** werden während des Upgrades standardmäßig weiter bedient. Mit `maintenance=page` (eine Servereinstellung
oder `Maintenance=page` für eine Installation) bekommen sie die [Wartungsseite](../funktionen/6.0/wartungsmodus.md) des
Kernels (503 mit `Retry-After`), während Administratoren sich weiter anmelden können; das braucht
`bin/php/maintenance.php`, das Exponential ab 6.0.15 hat, in der Installation oder im Ziel.

## Was bei einem Upgrade geschieht

```
 detect -> plan -> lock -> backup (geprüft) -> Wartung an? (optional)
                                                       |
                         Dateien: Dateipatch | git fast-forward
                                                       |
            Kernel exp:upgrade: [libraries,] database, scripts   (sonst die SQL-Update-Dateien)
                                                       |
                         Abschluss: Autoloads + Cache leeren -> Gesundheitsprüfung
                                                       |
             ein Fehler -> automatisches Rollback: Dateien, Bibliotheken, Datenbank, Caches (Exit 1, rolled_back: true)
             PASS       -> Wartung aus -> Sicherung als angewendet markiert -> alte Sicherungen rotiert
```

- **Zwei Methoden, je Installation gewählt.** Ein geprüfter **Dateipatch** (der Unterschied zur `share/filelist.md5`
  des Releases, Datei für Datei geschrieben und gegen das Release geprüft) oder ein **git fast-forward** für
  git-Checkouts. Ein Checkout, der sich nicht vorspulen lässt, wird abgelehnt, nie erzwungen. `--method=filepatch` oder
  `--method=git` überschreibt die Wahl.
- **Die Stufe libraries des Kernels.** Meldet der Kernel seine Stufe `libraries` als ausstehend
  (`exp:upgrade:status --stages=libraries`), führt xne `libraries, database, scripts` in dieser Reihenfolge aus, mit
  `vendor/`, `composer.json`, `composer.lock` und den Extension-Ordnern von Composer in der geprüften Sicherung.
- **Die Gesundheitsprüfung** nach jedem Upgrade: Die geschriebenen Dateien gleichen dem Release, die
  `share/filelist.md5` des Releases passt zu den Dateien, die Installation startet auf der Kommandozeile,
  `exp:upgrade:status` zeigt nichts Fehlgeschlagenes oder Ausstehendes, und jede eingestellte `health_url` antwortet mit
  200 bis 399.
- **Ein Lauf je Installation.** Eine Sperre lehnt einen zweiten gleichzeitigen Lauf ab (Exit 3).

## Kanäle und Ziele

| Kanal | Ziel |
|---|---|
| `stable` (Standard) | das neueste stabile Release derselben Hauptversion |
| `security` | nur das neueste Release derselben x.y-Linie |
| `beta` | das neueste Release, Vorabversionen eingeschlossen |
| `main` | der Hauptzweig (nächtlich); der Plan warnt, und wie immer wird eine Sicherung angelegt |

`--to=v6.0.15` nennt stattdessen ein genaues Release. Releases kommen aus gereihten Quellen (GitLab, GitHub, Packagist
und eine lokale Quelle, in der Reihenfolge der Einstellung `sources=`) und werden einmal in den Release-Cache geladen.
`xne sources` zeigt die Reihenfolge und das Ziel jedes Kanals.

## Datenbanken

xne sichert, aktualisiert und rollt die Datenbank über die eigenen `[DatabaseSettings]` der Installation zurück; das
Passwort wird nie angezeigt. Jede Sicherung wird zurückgelesen und geprüft, bevor sich etwas ändert, und ein Rollback
ist exakt: Die Tests vergleichen einen Fingerabdruck jeder Tabelle oder Collection vor der Sicherung und nach der
Wiederherstellung.

| Engine | `DatabaseImplementation` | PHP-Extension für xne | Sicherung | Rollback |
|---|---|---|---|---|
| MariaDB / MySQL | `ezmysqli`, `ezmysql` | `pdo_mysql` | `db.sql.gz`, xnes gerahmter SQL-Dump mit Zeilenzahlen | jede Tabelle gelöscht und aus dem Dump neu angelegt |
| PostgreSQL | `ezpostgresql` | `pdo_pgsql` | `db.sql.gz`, DDL aus dem Katalog, Sequenzen an ihren Positionen | in einer Transaktion |
| SQLite | `sqlite3`, `ezsqlite3` | `pdo_sqlite` | `db.sqlite`, eine konsistente Kopie, auf Integrität geprüft | über die Backup-API von SQLite |
| MongoDB | `expmongodb` | `mongodb` | `db.mongo.jsonl.gz`, Indizes und Dokumente als kanonisches Extended JSON | Collections gelöscht, Indizes und Dokumente neu angelegt |
| Oracle | `ezoracle` | `oci8` | `db.oracle.jsonl.gz`, DDL aus `DBMS_METADATA`, Zeilen mit CLOB und BLOB vollständig | Tabellen geleert, neu angelegt, Zeilen zurückgebunden, dann Indizes und Trigger |

**Was erprobt ist.** MariaDB / MySQL, PostgreSQL und SQLite: Upgrades von 6.0.13 auf 6.0.14 und von 6.0.14 auf 6.0.15,
per Dateipatch und per git, mit exakten Rollbacks. MongoDB und Oracle: je ein vollständiges Upgrade einer
Kickstarter-Installation und ein exaktes `xne rollback`, je ein Beweislauf.

**Sites über ODBC** (`DatabaseImplementation=odbc`, 6.0.15): xne liest `[ODBCSettings] Dialect` und behandelt die
Installation als diese Engine. `postgresql` und `mysql` werden über den eigenen PDO-Treiber der Engine gegen denselben
Server gesichert und zurückgerollt; `sqlserver`, `db2` und `msaccess` über ODBC werden abgelehnt.

**Noch nicht:** Die in 6.0.15 hinzugekommenen Datenbanken mit eigenen Treibern (SQL Server, Firebird, DuckDB, Db2,
Informix, SAP HANA, libSQL, MonetDB, die Familien des MySQL- und PostgreSQL-Protokolls) haben noch keine geprüfte
Sicherung in xne, also lehnt xne ihr Upgrade ab; aktualisieren Sie sie mit `exp:upgrade` und einer eigenen Sicherung.
Eine Installation, deren Siteaccesse mehr als eine Datenbank verwenden, wird abgelehnt (`several-databases`). Siehe
[Eine Datenbank wählen](../hilfe/datenbank-waehlen.md) für die Upgrade-Spalte je Datenbank.

## Fallen

`xne traps` meldet die bekannten Fallen einer Installation als `info`, `warn` oder `block`, jede mit ihrer Abhilfe.
Darunter:

- eine veraltete Kopie von `lib/` oberhalb des Document Root;
- ein Composer-Autoload-Eintrag `files` außerhalb der Installation;
- Extension-Ordner, die sich Installationen teilen;
- PHP-Handler, die `.htaccess` auf LiteSpeed setzt;
- Überschreibungen von Kernel-Klassen, die ein neues Release zurücklassen könnte;
- eine unbekannte `DatabaseImplementation`;
- eine PHP-Extension, die das Ziel-Release verlangt und die dem PHP seiner Werkzeuge fehlt (`php-extension-missing`).

Eine Falle `block` wird nie umgangen, außer die verantwortliche Person nimmt diese Falle über ihre ID an:
`--accept=<id>`.

## Zurückrollen

Es gibt vier Wege zurück; alle verwenden die Sicherung, die xne vor dem Upgrade angelegt hat, und keiner löscht eine
Datei.

1. **Automatisch.** Jeder Fehler nach der Sicherung (Dateien, `exp:upgrade`, eine SQL-Datei, das Leeren der Caches, die
   Gesundheitsprüfung) rollt sofort zurück, und der Befehl endet mit Exit 1, `rolled_back: true` und dem Grund. Schlägt
   das Rollback selbst fehl (`rollback_error`, Zustand `rollback-failed`), ist die Sicherung noch da und geprüft:
   beheben Sie die Ursache (eine volle Platte, eine verschwundene Datenbank) und führen Sie `xne rollback <id>` aus.
2. **`xne rollback`.**

   ```bash
   xne backups  --root=...                      # die Sicherungen, neueste zuerst
   xne rollback --root=... --dry-run            # was die neueste Sicherung wiederherstellen würde
   xne rollback --root=... [BACKUP-ID]          # Dateien und Datenbank
   xne rollback --root=... [BACKUP-ID] --no-db  # nur Dateien
   ```

   Die Sicherung wird geprüft; die Dateien, die das Upgrade hinzugefügt hat, kommen nach `aside-rollback/`; die
   archivierten Dateien werden zurückgeschrieben, jede gegen ihre md5 geprüft; in `vendor/` und den Extension-Ordnern
   von Composer hinzugekommene Dateien werden beiseitegelegt; ein git-Checkout wird auf seinen Commit zurückgesetzt
   (`git reset --keep`, abgelehnt bei lokalen Änderungen); die Datenbank wird wiederhergestellt und ihre Zeilenzahlen
   verglichen; dann die Autoloads und die Caches.

   Ein Rollback der Datenbank bringt die Daten **vom Zeitpunkt der Sicherung** zurück: nach dem Upgrade angelegte
   Inhalte gehen verloren. Verwenden Sie `--no-db`, wenn der Datenbankschritt in Ordnung war und nur Dateien zurück
   müssen, und sagen Sie es dem Eigentümer der Site.
3. **Ein git-Checkout, über ein Tag:** `xne rollback --root=... --git-tag=v6.0.14` bringt den Code auf das Tag; die
   Datenbank bleibt unberührt.
4. **Von Hand**, nur wenn xne gar nicht laufen kann: `files.tar.gz` ist ein normales Archiv, das über das
   Wurzelverzeichnis entpackt wird; `db.sqlite` wird bei angehaltener Site über die Datenbankdatei kopiert;
   `db.sql.gz` ist SQL (vor jeder Anweisung ein Kommentar `-- xne-stmt <bytes>`), das nach dem Löschen der Tabellen an
   `mysql` oder `psql` geht; `backup.json` nennt die Dateien, die das Upgrade hinzugefügt hat. Dann die Caches leeren.

## Der Sicherungsordner

`<zustand>/installs/<installation>/backups/<id>/` (Modus 0700; `<zustand>` ist `/var/lib/xne` als root). Die neuesten
`backup_keep` Sicherungen (Standard 5) bleiben erhalten.

| Datei | Enthält |
|---|---|
| `.xne-backup` | die Markierung; die Rotation entfernt nur Ordner, die sie tragen |
| `backup.json` | Versionen, Ziel, Methode, git-Commit, die Dateiliste mit der md5 jeder Datei, die Metadaten der Datenbank (Tabellen, Zeilenzahlen, Prüfsumme), den Zustand und das Ergebnis eines Rollbacks |
| `files.tar.gz` | jede Datei, die das Upgrade ändert oder ausmustert, `settings/override` und `settings/siteaccess` vollständig, und `vendor/`, `composer.json`, `composer.lock` und die Extension-Ordner von Composer, wenn Composer läuft |
| `listing.json` | die Dateiliste dieser Ordner |
| `db.sql.gz`, `db.sqlite`, `db.mongo.jsonl.gz`, `db.oracle.jsonl.gz` | die Datenbank, je Engine |
| `kept/` | eine einfache Kopie jeder lokalen Änderung, die das Upgrade ersetzt hat |
| `aside/`, `aside-rollback/` | die Dateien, die das Upgrade ausgemustert hat; die Dateien, die ein Rollback zurückgenommen hat |

`xne verify-backup [id]` liest eine Sicherung zurück (die Prüfsumme des Archivs, die md5 jeder Datei, den Dump und
seine Zeilenzahlen); `xne backup` legt eine geprüfte Sicherung an, ohne zu aktualisieren.

## Mehrere Installationen

Planen Sie zuerst alle (`xne plan --json` für jede), dann aktualisieren Sie eine nach der anderen. Jede Installation hat
ihre eigene Sperre und ihre eigenen Sicherungen; Einstellungen je Installation (`Maintenance=page`, `finish=velocity`,
`health_url`) gehören in die Konfiguration von xne. Installationen, die sich Extension-Ordner teilen, werden als Falle
gemeldet.

## Windows

1. Installieren Sie PHP 7.4 oder neuer für die Kommandozeile, dasselbe PHP, das die Site verwendet (das von XAMPP
   `C:\xampp\php\php.exe`, das von WampServer oder ein Zip von windows.php.net), mit `extension=openssl`,
   `extension=curl` und dem `pdo_*` der Datenbank in seiner `php.ini`.
2. Holen Sie xne: `git clone https://github.com/se7enxweb/xne.git C:\xne`, oder entpacken Sie das Archiv des
   Repositorys dorthin.
3. Nehmen Sie `C:\xne\bin` in den `PATH` auf, oder setzen Sie `XNE_PHP` auf den vollen Pfad von `php.exe`:

   ```bat
   setx XNE_PHP "C:\xampp\php\php.exe"
   xne version
   xne detect --root="C:\xampp\htdocs\exponential"
   ```

In PowerShell laufen dieselben Befehle über `xne.ps1` (wenn Skripte gesperrt sind:
`powershell -ExecutionPolicy Bypass -File C:\xne\bin\xne.ps1 version`). Zustand und Cache: `%ProgramData%\xne\lib` und
`%ProgramData%\xne\cache`.

**Unter Windows erprobt:** Upgrades von 6.0.13 auf 6.0.14 und von 6.0.14 auf 6.0.15 laufen in der CI auf Windows Server
2022 und 2025, jedes mit exaktem Rollback, in der eigenen CI von xne und in der Windows-CI von Exponential
([Lauf 45867228580](https://github.com/se7enxweb/exponential/actions/runs/45867228580)). Die Windows-Starter sind
einfache Platzhalter; ein Installer (MSI), die Besonderheiten von IIS, XAMPP und WampServer und eine geplante Aufgabe
sind geplant. Siehe auch [Exponential unter Windows](../hilfe/exponential-unter-windows.md).

## Prüfungen und Benachrichtigungen

- `xne check` fragt `https://network.se7enx.com/api/v1`, was jeder Kanal anbietet und welche Hinweise die Installation
  betreffen (eine anonyme Prüfung sendet nur die Version), und führt die Benachrichtigungs-Hooks aus.
- **Benachrichtigungs-Hooks** (seit 0.3.0.8): `notify_problem_cmd`, `notify_ok_cmd` und `notify_event_cmd` sind
  Befehlszeilen mit Platzhaltern und Umgebungsvariablen `XNE_NOTIFY_*`, ohne Shell gestartet, sodass jedes Alarm-,
  Chat- oder Ticketwerkzeug ein Problem öffnen und schließen oder ein Ereignis empfangen kann.
- **Installationsschlüssel** (`xne key:set`, von der Standardeingabe gelesen, mit 0600 gespeichert; `xne key:status`)
  signieren den Bericht der Installation (`xne report`), auf die Stufe des Schlüssels gekürzt: nie Inhalte, Benutzer
  oder Werte von Einstellungen.
- Eine tägliche Prüfung liegt als systemd-Timer bei (`xne-check.timer`), standardmäßig nicht installiert.

Die öffentliche API: `GET /status`, `GET /releases`, `GET /advisories?version=6.0.14`, `POST /check` und das signierte
`POST /report` (HMAC-SHA256 mit einem Zeitstempel; eine Signatur, die mehr als 300 Sekunden abweicht, wird abgelehnt).

## Befehle und Optionen

Globale Optionen: `--root=DIR`, `--json`, `--dry-run`, `--help`, `--php=PFAD`, `--set key=value` (jeder Schlüssel aus
`xne.conf`).

| Befehl | Tut |
|---|---|
| `xne detect` | Version, Art der Installation (`git`, `composer`, `files`), PHP, Eigentümer, Datenbanken, Extensions, Siteaccesse, Kernel-Werkzeuge |
| `xne traps` | die bekannten Fallen, jede mit Stufe und Abhilfe |
| `xne plan [--channel=stable\|beta\|main\|security] [--to=REF] [--method=auto\|filepatch\|git]` | Ziel, Dateizahlen, lokale Änderungen, Datenbankschritte, Fallen, Warnungen, `blocked`; ändert nichts |
| `xne upgrade [Plan-Optionen] [--dry-run] [--accept=<id>]` | Plan, Sperre, geprüfte Sicherung, Dateien, Bibliotheken, Datenbank und Skripte, Abschluss, Gesundheitsprüfung; automatisches Rollback bei Fehlern |
| `xne rollback [BACKUP-ID] [--no-db] [--git-tag=TAG] [--dry-run]` | Dateien und Datenbank zurück auf eine Sicherung |
| `xne backup`, `xne backups`, `xne verify-backup [BACKUP-ID]` | eine geprüfte Sicherung jetzt; die Liste; eine Sicherung zurückgelesen |
| `xne sources [--refresh]` | die Release-Quellen in ihrer Rangfolge und das Ziel jedes Kanals |
| `xne config` | jede wirksame Einstellung und woher sie kommt (Geheimnisse nie angezeigt) |
| `xne db-fingerprint` | Zeilen und eine md5 je Tabelle, dazu ein Fingerabdruck über alle |
| `xne check [--no-notify]`, `xne report [--dry-run]` | Kanäle und Hinweise; der signierte Bericht |
| `xne key:set`, `xne key:status` | der Installationsschlüssel |
| `xne version`, `xne help [befehl]` | die Version; das Handbuch eines Befehls |

## Was xne nie tut

- Es ändert eine Installation nie ohne geprüfte Sicherung; schlägt die Sicherung fehl, ändert sich nichts.
- Es hinterlässt nie eine halb aktualisierte Installation: Jeder Fehler nach der Sicherung rollt Dateien und Datenbank
  zurück.
- Es löscht nie eine Datei: ausgemusterte Dateien und Dateien, die ein Rollback zurücknimmt, kommen in den
  Sicherungsordner.
- Es überschreibt nie stillschweigend eine lokale Änderung: Jede wird in der Sicherung aufbewahrt und aufgeführt.
- Es schreibt nie `settings/override`, `settings/siteaccess`, `var`, `config.php`, `.htaccess` und Ähnliche.
- Es läuft nie zweimal gleichzeitig auf einer Installation und erzwingt nie etwas in git.
- Es umgeht nie eine blockierende Falle, außer sie wird über ihre ID angenommen.
- Es gibt nie ein Datenbankpasswort, Token oder Schlüsselgeheimnis aus.
- Es erstellt oder veröffentlicht nie ein Release von Exponential; es installiert sie.

## Grenzen

- **Vorerst nur 6.x.** xne aktualisiert innerhalb von 6.x. Es erkennt 4.x- und 5.x-Installationen und reiht ihre
  SQL-Update-Dateien in einem Plan auf, aber diese Ketten sind noch nicht fertig und getestet: nur planen, und
  [11.4 Von 3.10 bis 5.3](../installation/11-aktualisieren.md#114-von-310-bis-53-die-alte-kette) und
  [11.5 Von 5.4 auf 6.0.0](../installation/11-aktualisieren.md#115-von-54-oder-590-auf-600) folgen.
- **Datenbanken:** siehe [Datenbanken](#datenbanken); die in 6.0.15 hinzugekommenen Treiber aktualisiert xne noch
  nicht.
- **Geplant, ohne Termin:** Setup > Network in der Administration und Befehle `exp:network:*` in einer Installation (die
  Extension `exp_network` im Repository von xne), eine Composer-Methode, die Ketten für 4.x und 5.x, nächtliche und
  Sicherheitskanäle mit automatischer Anwendung und der Windows-Installer.
