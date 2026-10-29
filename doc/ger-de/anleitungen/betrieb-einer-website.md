# Betrieb einer Site: Caches, Cronjobs, Sicherungen und Reparaturen

Diese Anleitung richtet sich an die Person, die eine Exponential-Site Tag für Tag am Laufen hält. Sie führt Sie durch die Routinearbeiten, mit denen die Site gesund bleibt: die Caches ansehen und den richtigen leeren, Cronjobs ausführen, den statischen Cache einschalten, die Site vorwärmen, eine Sicherung anlegen, wissen, auf welcher Datenbank Sie arbeiten, die Logs lesen und eine Site reparieren, die nicht startet. Arbeiten Sie sie von oben nach unten durch. Jeder Teil dauert ein paar Minuten und endet mit etwas, das Sie prüfen können.

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../beispiele/webserver/nginx.md), [LiteSpeed](../beispiele/webserver/litespeed.md), [Caddy](../beispiele/webserver/caddy.md), [IIS](../beispiele/webserver/iis.md),
> [Velocity](../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../hilfe/hosting/control-panels/plesk.md), [cPanel](../hilfe/hosting/control-panels/cpanel.md), [DirectAdmin](../hilfe/hosting/control-panels/directadmin.md), [ISPConfig](../hilfe/hosting/control-panels/ispconfig.md).

Jeder Befehl wird im Wurzelverzeichnis der Installation ausgeführt, dem Verzeichnis, das `index.php` enthält. Wenn Sie als `root` angemeldet sind, ergänzen Sie bei den PHP-Skripten `--allow-root-user`; andernfalls verweigern die Skripte die Ausführung als root. In den Beispielen ist der Schalter ausgeschrieben, damit Sie sie unverändert kopieren können.

Wo ein Thema mehr als ein paar Minuten braucht, endet der Teil mit Links zu den Funktions-, Spezifikations- und Upgrade-Seiten.

## 1. Erst hinsehen, dann leeren: welche Caches es gibt

Exponential hat etwa siebenundzwanzig Caches: INI, Templates, Inhalte, Übersetzungen, Bild-Aliase, den HTTP-Cache, den SQL-Abfrage-Cache und, wenn Sie den Velocity-Server betreiben, dessen Antwort-Cache. Ein einziger Befehl listet sie auf und sagt, wie jeder geleert wird.

```bash
php bin/php/cache.php list --sizes --allow-root-user
```

Erwartete Ausgabe: ein Block je Cache, zum Beispiel

```
  content              on  content                    expiry timestamp content-view-cache set, directory moved aside
                          var/site/cache/content 3 files, 31.5 KB
  template             on  template                   directory removed
                          var/site/cache/template/compiled 227 files, 5.4 MB
```

Das Wort nach dem Namen des Caches ist sein **Tag**; ein Tag leert jeden Cache, der ihn trägt. Für eine Übersicht auf einer Bildschirmseite über die Caches, die vor PHP liegen (statisch, HTTP, Abfragen, Velocity, OPcache, APCu):

```bash
php bin/php/cache.php status --allow-root-user
```

Die erwartete Ausgabe endet mit `PASS  status read` und enthält je eine Zeile für `static`, `httpcache`, `querycache`, `velocity`, `precompress` und `php`.

In der Administrationsoberfläche finden Sie dieselben Caches unter **Setup > Cache**.

## 2. Den richtigen Cache leeren

Leeren Sie das Kleinste, das das Problem behebt. Jeder dieser Befehle zeigt zunächst mit `--dry-run`, was er tun würde, und ändert nichts:

```bash
php bin/php/cache.php clear --all --dry-run --allow-root-user
```

Leeren Sie dann tatsächlich, je nachdem, was Sie geändert haben:

| Sie haben geändert | Befehl |
|---|---|
| Eine Einstellung (`.ini`-Datei) | `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` |
| Ein Template (`.tpl`) | `php bin/php/ezcache.php --clear-tag=template --allow-root-user` |
| Inhalte wirken veraltet | `php bin/php/ezcache.php --clear-tag=content --allow-root-user` |
| Ein neues Template oder eine neue Extension wurde hinzugefügt | `php bin/php/ezcache.php --clear-id=template-override --allow-root-user` und `--clear-id=design_base` |
| Alles, wenn Sie unsicher sind | `php bin/php/ezcache.php --clear-all --allow-root-user` |

`ezcache.php --list-tags` und `--list-ids` geben die gültigen Namen aus. `--purge` entfernt Einträge physisch, statt sie nur als abgelaufen zu markieren, was Speicherplatz spart; `--expiry="-2 days"` zusammen mit `--purge` entfernt nur alte Einträge.

Dieselben Aktionen gibt es je Gruppe in `exp:cache`, das außerdem `PASS` oder `FAIL` meldet und eine Ausgabe mit `--json` für die Überwachung bietet:

```bash
php bin/php/cache.php clear --tag=ini --allow-root-user
php bin/php/cache.php imagealias clear --allow-root-user
php bin/php/cache.php opcache status --allow-root-user
```

Große Caches werden beiseitegeschoben und erst danach gelöscht, sodass ein Leeren keine Anfragen warten lässt; siehe [Cache-Leerungen, die Verzeichnisse beiseiteschieben](../funktionen/6.0/cache-leeren-durch-umbenennen.md).

### Die Caches vor PHP

Inhalte werden nach einem Leeren erneut zwischengespeichert, sobald die nächste Anfrage sie rendert. Drei Caches liegen vor dem Rendern der Seite und haben eigene Befehle:

```bash
php bin/php/cache.php httpcache status --allow-root-user     # the HTTP cache: entries, size, hits and misses
php bin/php/cache.php httpcache clear --allow-root-user      # every stored page becomes stale
php bin/php/cache.php velocity status --allow-root-user      # Velocity's response cache
php bin/php/cache.php velocity clear --allow-root-user
```

Die erwartete Ausgabe der Status-Befehle endet mit `PASS` und einer Zeile wie `the HTTP cache is on: 4651 entries, generation 22850`. Leeren Sie den HTTP-Cache und den Antwort-Cache **erst, nachdem** PHP-FPM oder Velocity den neuen Code ausführt, sonst wird eine Seite, die der alte Code in der Zwischenzeit gerendert hat, erneut gespeichert. Wenn Sie Velocity betreiben, erledigt ein einziger Befehl alle Schritte in der richtigen Reihenfolge:

```bash
./console exp:velocity deploy --dry-run --allow-root-user
```

Vertiefung: [Velocity-Antwort-Cache](../funktionen/6.0/velocity-antwort-cache.md), [HTTP-Cache](../bc/6.0/http-caching.md), [Antwort-Cache und Navigation](../bc/6.0/antwort-cache-und-navigation.md), [Cache-Konsole](../bc/6.0/cache-konsole.md), [Velocity-Engines: eine PHP-Änderung bereitstellen](../bc/6.0/velocity-engines.md#eine-php-änderung-bereitstellen-expvelocity-deploy).

## 3. Cronjobs ausführen

Cronjobs erledigen die Arbeit, auf die niemand wartet: geplante Inhalte veröffentlichen, Feeds importieren, Warenkörbe aufräumen, Newsletter versenden, Sitemaps erstellen.

Sehen Sie nach, was es gibt und welches Skript zu welchem Teil gehört:

```bash
php runcronjobs.php --list --allow-root-user
```

Erwartete Ausgabe: Blöcke mit den Namen `CronjobSettings:` und `CronjobPart-frequent:`, `CronjobPart-infrequent:` und so weiter, jeweils gefolgt von ihren Skriptdateien.

Führen Sie einen Teil oder ein einzelnes Skript eines Teils aus:

```bash
php runcronjobs.php --siteaccess=site frequent --allow-root-user
php runcronjobs.php --siteaccess=site --script=notification.php --allow-root-user
```

Verwenden Sie für `--siteaccess` den Namen Ihres eigenen öffentlichen Siteaccess. Dieselbe Liste finden Sie in der Administration unter **Setup > Cronjobs** (`/setup/cronjobs`); dort hat jeder Teil eine Karte mit seinem Zeitplan, seiner nächsten und letzten Ausführung, einer Schaltfläche *Teil starten* und seinem Shell-Befehl zum Kopieren. Die Ausgabe wird angezeigt, während der Job läuft, und die Seite schlägt die Crontab-Zeilen vor. Zeigen Sie an, was derzeit installiert ist:

```bash
./console crontab:list
```

Um den Zeitplan einzurichten, öffnen Sie die Crontab zum Bearbeiten (`./console crontab:edit`) und fügen je Teil eine Zeile hinzu. Ein üblicher Ausgangspunkt ist der Teil „frequent“ alle paar Minuten und der Teil „infrequent“ einmal pro Stunde oder pro Tag:

```
*/5 * * * * cd /path/to/installation && php runcronjobs.php --siteaccess=site frequent --allow-root-user >> var/log/cron-frequent.log 2>&1
17 * * * *  cd /path/to/installation && php runcronjobs.php --siteaccess=site infrequent --allow-root-user >> var/log/cron-infrequent.log 2>&1
```

Ersetzen Sie `/path/to/installation` durch das Wurzelverzeichnis. Jeder Teil hat außerdem einen Konsolen-Alias, zum Beispiel `./console cron:frequent`; `./console list cron` zeigt alle an. Wenn Sie Velocity betreiben, kann dessen eigener [Scheduler](../funktionen/6.0/velocity-zeitplaner.md) diese Aufgaben ohne den System-Cron starten.

Der Cronjob-Teil für Benachrichtigungen hat eine eigene Anleitung: [Benachrichtigungen: ausführen und Probleme beheben](benachrichtigungen-administrator.md).

Der Teil `mailpreferences` (ebenfalls in `infrequent`) hält das Protokoll der E-Mail-Einwilligungen und die Bestätigungen innerhalb ihrer Aufbewahrungsfrist: [E-Mail-Einstellungen: einrichten und ausführen](mail-einstellungen-administrator.md).

Vertiefung: [Cronjob-Konsole](../funktionen/6.0/cronjobs-in-der-konsole.md), [Befehle, Cronjob-Teile und Views als Klassen](../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md), [Inhaltsjobs](../funktionen/6.0/inhaltsauftraege.md) (große Lösch- und Kopiervorgänge laufen in Stapeln und werden von `cron:contentjobs` fortgesetzt).

## 4. Den statischen Cache einschalten

Der statische Cache speichert öffentliche Seiten als einfache Dateien, die der Webserver ausliefern kann, ohne Exponential zu starten. Sehen Sie sich zuerst an, was er tun würde:

```bash
php bin/php/cache.php static status --allow-root-user
```

Die erwartete Ausgabe listet die Sites auf, die erzeugt werden können, und endet mit `PASS` und einem Satz wie `the static cache is not enabled in site.ini [ContentSettings] StaticCache; 3 sites can be generated`.

Erzeugen Sie die Seiten einer Site, begrenzt auf 500 Seiten, damit Sie schnell sehen, dass es funktioniert:

```bash
php bin/php/makestaticcache.php --site=site --max-pages=500 --max-depth=6 --allow-root-user
```

Lassen Sie `--site` weg, um jeden öffentlichen Siteaccess zu erzeugen. Angeboten werden nur Siteaccesses, die keine Anmeldung erfordern, weil ihre Seiten für alle gleich sind. Dasselbe ist im Browser unter **Setup > Cache** im Abschnitt *Statischer Inhalts-Cache* möglich, mit einer Schaltfläche *Anhalten*. Prüfen Sie das Ergebnis:

```bash
php bin/php/cache.php static status --allow-root-user
```

Die Dateien werden je Site in `var/site/static` gezählt. Damit der Webserver aus ihnen antwortet, setzen Sie `StaticCache=enabled` in `settings/override/site.ini.append.php` im Block `[ContentSettings]`, leeren den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`) und prüfen, dass die Webserver-Regeln Ihrer Installation aktiv sind. Entfernen Sie die erzeugten Dateien mit `php bin/php/cache.php static clear --allow-root-user`.

Vertiefung: [Generator für den statischen Cache](../funktionen/6.0/generator-fuer-den-statischen-cache.md), [Standardwerte des statischen Caches](../bc/6.0/standardwerte-des-statischen-caches.md).

## 5. Die Site vorwärmen (Preload)

Nach einem Leeren oder einer Bereitstellung bezahlt der erste Besucher für das Rendern. Wärmen Sie die Seiten selbst vor:

```bash
php bin/php/preload.php --apply --siteaccess=site --allow-root-user
```

Das Skript fragt zuerst die Seiten der Sektionen ab und folgt dann den Links der Site, mit der curl-Erweiterung von PHP (kein `wget` nötig); es läuft immer nur ein Vorladen gleichzeitig, und jeder Lauf wird unter Setup > Vorladen aufgeführt. Um jede veröffentlichte Seite vorzuwärmen, was sich für einen Cron-Eintrag alle paar Minuten eignet:

```bash
php bin/php/warm.php --allow-root-user >> var/log/cache-warm.log 2>&1
```

Verwenden Sie im Browser die Ansicht *Seiten vorladen* der Administration: Wählen Sie die Site, eine Seitenbegrenzung (Standard 250) und eine Linktiefe (Standard 3) und verfolgen Sie, wie die Seiten abgerufen werden. Vertiefung: [Caches vorwärmen](caches-vorwaermen.md), [Ansicht „Seiten vorladen“](../funktionen/6.0/ansicht-websites-vorladen.md), [Preloader für den Site-Cache](../bc/6.0/preload.md), [HTTP/2 und Cache-Vorwärmung](../bc/6.0/http2-und-cache-vorwaermen.md).

## 6. Sichern und wiederherstellen

Exponential hält seinen Zustand an zwei Stellen: in der **Datenbank** und in den **Dateien** (`var/`, `settings/override/` und `settings/siteaccess/` sowie Ihre eigenen Extensions und Designs). Eine Sicherung umfasst beides, zum selben Zeitpunkt aufgenommen.

Der Wartungsmodus sorgt dafür, dass beide zusammenpassen. Er beantwortet jeden Besuch mit einer 503-Seite, während Sie arbeiten:

```bash
php bin/php/maintenance.php on --message="Back soon" --until=30m --allow-root-user
php bin/php/maintenance.php status --allow-root-user     # prints: Maintenance is on...
```

Sichern Sie die Dateien:

```bash
tar czf backup-files.tar.gz var/storage var/log settings/override settings/siteaccess
```

Sichern Sie die Datenbank mit dem Werkzeug Ihres Datenbanksystems. Ersetzen Sie `USER` und `DATABASE` durch Ihre Werte. `-p` lässt den Client nach dem Passwort fragen; schreiben Sie das Passwort selbst nie auf die Kommandozeile, wo es in der Shell-Historie landet.

| Datenbank | Sicherung | Wiederherstellung |
|---|---|---|
| MySQL oder MariaDB | `mysqldump --single-transaction -u USER -p DATABASE > backup.sql` | `mysql -u USER -p DATABASE < backup.sql` |
| PostgreSQL | `pg_dump -U USER DATABASE > backup.sql` | `psql -U USER DATABASE < backup.sql` |
| SQLite | `sqlite3 var/storage/sqlite3/exponential.db ".backup backup.db"` | `backup.db` zurückkopieren, während die Site im Wartungsmodus ist |

`exponential.db` ist der Dateiname, den `exp:install` standardmäßig verwendet; verwenden Sie den Namen Ihrer eigenen Datenbankdatei (`ls var/storage/sqlite3/`).
Kopieren Sie bei SQLite die Datei nie, während die Site in Betrieb ist; `.backup` ist sicher. Das Schema allein ist als portable Datei auch über `php bin/php/ezsqldumpschema.php --type=mysql --user=USER DATABASE schema.sql --allow-root-user` erhältlich.

Schalten Sie die Site anschließend wieder ein und leeren Sie die Caches:

```bash
php bin/php/maintenance.php off --allow-root-user
php bin/php/ezcache.php --clear-all --allow-root-user
```

Testen Sie eine Wiederherstellung mindestens einmal auf einem anderen Server; eine Sicherung, die Sie nie wiederhergestellt haben, ist nur eine Hoffnung. Vertiefung: [Wartungsmodus](../funktionen/6.0/wartungsmodus.md).

## 7. Den eigenen Datenbanktreiber kennen

Welche Datenbank die Site betreibt, ist eine einzige Einstellung. Lesen Sie sie aus:

```bash
grep -rn "DatabaseImplementation" settings/override settings/siteaccess/*/site.ini.append.php settings/site.ini
```

Erwartete Ausgabe: eine Zeile wie `DatabaseImplementation=ezmysqli`. Die Werte sind `ezmysqli` (MySQL, MariaDB), `pgsql` oder `ezpostgresql` (PostgreSQL), `sqlite3` (eine Datei unter `var/storage/sqlite3/`) und `mongodb`; Oracle bringt eine eigene Extension mit. Die Aliase stehen in `settings/site.ini` im Block `[DatabaseSettings]`, Schlüssel `ImplementationAlias[]`.

Was Sie sich merken sollten:

- **SQLite** ist der schnellste Weg zum Start, und seine Transaktionen stellen sich für die einzige Schreibsperre an (`SQLiteTransactionWait`, Standard 25 Sekunden, unter Velocitys Anfrage-Timeout von 30 Sekunden `Q.webserver.requestTimeout` gehalten, damit der Treiber „database is busy“ melden kann, bevor die Anfrage abgebrochen wird; erhöhen Sie beide gemeinsam). Wenn viele Redakteure gleichzeitig veröffentlichen, verwenden Sie MySQL oder PostgreSQL.
- **MySQL** und **PostgreSQL** sind die üblichen Wahlen für den Produktivbetrieb.
- **Oracle** und **MongoDB** werden unterstützt; lesen Sie die Seiten unten, bevor Sie sich dafür entscheiden.

Für Entwickler: Die Fehlernummer einer fehlgeschlagenen Anweisung ist die der Datenbank selbst, in `$db->errorNumber()` und,
mit `$db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS )`, als `getCode()` der geworfenen `eZDBException`, sodass Code
einen doppelten Schlüssel von einer fehlenden Tabelle unterscheiden kann, ohne die Meldung zu lesen:

| Treiber | Doppelter Schlüssel | Fehlende Tabelle |
|---|---|---|
| MySQL, MariaDB | `1062` | `1146` |
| PostgreSQL (der SQLSTATE, eine Zeichenkette) | `'23505'` | `'42P01'` |
| SQLite (der Ergebniscode) | `19` | `1` |
| Oracle (die ORA-Nummer) | `1` | `942` |
| MongoDB (nur `errorNumber()`; der Treiber antwortet `false`, er wirft nichts) | `11000` | - |

```php
$db = eZDB::instance();
$db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
try
{
    $db->query( "INSERT INTO my_table ( id ) VALUES ( 1 )" );
}
catch ( eZDBException $e )
{
    if ( $e->getCode() === 1062 || $e->getCode() === '23505' )   // MySQL, PostgreSQL
        { /* the row is there already */ }
}
$db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
```

Der SQLite-Treiber wirft innerhalb einer Transaktion; außerhalb einer Transaktion antwortet er `false` und setzt `errorNumber()`. Hinweis
zum Upgrade: `errorNumber()` von PostgreSQL war bei jedem Fehler `1` und ist jetzt der SQLSTATE; mit der Ausnahmebehandlung
wirft MySQL unter PHP 8.1 und neuer `eZDBException` statt PHPs `mysqli_sql_exception` (die Standardbehandlung
ist unverändert). Dank an Mugo Web, deren LoveStack-Commit 3d18f47551 die Fehlercodes von MySQL als Erste in die
Ausnahmen übernommen hat.

### Zahlen im eingebauten Suchindex

Die eingebaute Suchmaschine speichert jedes Wort eines Textes mit seinem Wert als Zahl, damit eine Suche nach
Zahlen filtern kann. Eine sehr große Zahl in einem Textfeld (eine 20-stellige Bestellnummer, `1e30`) bricht die Indizierung unter MySQL
oder PostgreSQL nicht mehr ab: Sie wird als größte Zahl gespeichert, die die Spalte aufnehmen kann, und das Objekt wird mit all seinen übrigen
Wörtern indiziert. Jeder Treiber kennt den Wertebereich seiner Ganzzahlspalten, `$db::MAX_INT` und `$db::MIN_INT` (vorzeichenbehaftet 32 Bit für
MySQL, PostgreSQL und Oracle, vorzeichenbehaftet 64 Bit für SQLite und MongoDB); `$db->clampInteger( $value )` liefert einen Wert
innerhalb dieses Bereichs, schneidet Nachkommastellen ab wie `(int)` und liefert 0 für alles, was keine Zahl ist. Für jede
Installation eingeschaltet; bei einem Upgrade ist nichts zu tun. Mit Dank an Mugo Web (LoveStack, Commit `3774a54ead`).

Verknüpfte Objekte werden mit dem Objekt indiziert, das auf sie verweist: Die durchsuchbaren Felder eines Objekts, das in einem
Objektbeziehungs- oder Beziehungslistenfeld ausgewählt ist, werden gefunden, wenn Sie nach dem Objekt suchen, das dieses Feld enthält. Die Beziehungsfelder
dieses verknüpften Objekts bleiben außen vor, sodass die Indizierung nie einer Kette von Beziehungen folgt. Exponential arbeitet
seit 2018 so (siehe [Indizierung von Beziehungen](../bc/5.90/indizierung-von-beziehungen.md)); LoveStacks Commit `5c089f6250`
macht dasselbe, sodass eine Site, die von LoveStack kommt, hier dieselben Suchergebnisse findet.

Vertiefung: [SQLite-Datenbankunterstützung](../funktionen/6.0/sqlite-datenbank.md), [SQLite-3-Treiber](../spezifikationen/6.0/sqlite3-datenbanktreiber.md), [Datenbanktreiber vom 16. bis 30. September 2026](../spezifikationen/6.0/datenbanktreiber-2026-09.md), [Verhalten der SQLite- und Oracle-Treiber](../spezifikationen/6.0/datenbanktreiber-sqlite-oracle.md), [MongoDB-Unterstützung](../funktionen/6.0/mongodb-datenbankunterstuetzung.md).

## 8. Die Logs lesen

Wo Sie zuerst nachsehen:

| Was | Wo | Wie |
|---|---|---|
| PHP- und Anwendungsfehler | `var/log/error.log`, `var/log/debug.log` | `tail -n 50 var/log/error.log` |
| PHP-Deprecations (Code, den eine spätere PHP-Version ablehnen wird) | `var/log/deprecated.log` | `sort var/log/deprecated.log \| uniq -c \| sort -rn \| head` |
| Ausgabe der Cronjobs | `var/log/` und, für im Browser gestartete Jobs, `cronjobs/output.log` und `cronjobs/error.log` im var-Verzeichnis des Siteaccess | **Setup > Cronjobs**, Abschnitt *Ausgabe* |
| Installationsprogramm | `var/log/kickstart.log` (Passwörter maskiert) | `tail -n 50 var/log/kickstart.log` |
| Wer hat was getan (Anmeldungen, Inhaltsänderungen, Änderungen an Berechtigungen) | das Audit-Protokoll | `php bin/php/audit.php status --allow-root-user` |

Das Audit-Protokoll ist ein signiertes, manipulationssicheres Log. Prüfen Sie, dass es unversehrt ist, und lesen Sie die neuesten Ereignisse:

```bash
php bin/php/audit.php status --allow-root-user
php bin/php/audit.php tail --channel=access --lines=20 --allow-root-user
php bin/php/audit.php verify --allow-root-user
```

Erwartete Ausgabe von `status`: `Audit: enabled` und eine Zeile je Kanal (`content`, `access`, `system`, ...), die auf `intact` endet. Um bei der Untersuchung mehr auf der Seite selbst zu sehen, setzen Sie `DebugOutput=enabled` in `settings/override/site.ini.append.php` im Block `[DebugSettings]`, laden die Seite neu und schalten es danach wieder aus; lassen Sie es im Produktivbetrieb nie eingeschaltet. Vertiefung: [Audit-Protokoll](../funktionen/6.0/pruefprotokoll.md), [Ereignismodell des Audit-Protokolls](../spezifikationen/6.0/audit-ereignismodell.md), [Verbesserungen der Debug-Ausgabe](../funktionen/6.0/verbesserte-debug-ausgabe.md).

### PHP-Deprecations und Größe der Logdateien

PHP 8 meldet eine Deprecation für jede Verwendung von etwas, das eine spätere PHP-Version entfernt, oft dieselbe viele Male
auf einer Seite. Sie haben ein eigenes Log, `var/log/deprecated.log`, in das jede unterschiedliche Meldung einmal pro
Anfrage geschrieben wird; sie werden nicht im Speicher gehalten und nicht in der Debug-Ausgabe aufgeführt, die sie nur zählt (der Wert `depr` der
[Debug-Leiste](../bc/6.0/debug-leiste.md#die-angeheftete-leiste) und die Anzahl der Warnungen). Für Entwickler ist die Datei die Liste dessen,
was vor dem nächsten PHP-Upgrade zu beheben ist; für Administratoren braucht eine Seite mit eingeschalteter Debug-Ausgabe weniger Speicher.

```ini
# settings/override/site.ini.append.php
[DebugSettings]
# enabled (default): deprecations go to deprecated.log; disabled: they are warnings, as before 6.0.15
DeprecatedLog=enabled
# Also write them while debug output is off (production): add the level to the list
AlwaysLog[]=deprecated
```

Entwickler schreiben eigene Meldungen mit `eZDebug::writeDeprecated( 'Use newThing() instead', __METHOD__ )`.

Jede Logdatei (error.log, warning.log, deprecated.log, storage.log, die Cronjob-Logs ...) wird rotiert, wenn sie
größer ist als `site.ini [FileSettings] LogFileMaxSize` (Standard 2097152 Byte, 2 MB), und zwar nach `.1`, `.2`, `.3`;
`LogFileRotateCount` (Standard 3) gibt an, wie viele aufbewahrt werden, 0 schaltet die Rotation aus. Mit den Standardwerten belegt ein Log
höchstens 8 MB auf der Festplatte. Die Konstanten `EZPUBLISH_LOG_MAX_FILE_SIZE`, `CUSTOM_LOG_MAX_FILE_SIZE` und ihre `*_ROTATE_FILES`
in `config.php` haben weiterhin Vorrang vor diesen Einstellungen; `./console exp:config:list --only-set` zeigt, ob
`config.php` sie setzt, und `./console exp:config:unset EZPUBLISH_LOG_MAX_FILE_SIZE --apply` gibt die Größe an
`site.ini` zurück (siehe [.htaccess und config.php von der Konsole aus einrichten](setup-dateien-von-der-konsole.md)).

Upgrade: Beides ist nach dem Upgrade eingeschaltet. Logdateien werden bei 2 MB statt bei 200 KB rotiert (setzen Sie
`LogFileMaxSize=204800` für die alte Größe), und Deprecations erscheinen nicht mehr in `warning.log` oder in der Debug-Liste
(setzen Sie `DeprecatedLog=disabled` für das alte Verhalten). Beides baut auf Arbeiten von Mugo Web in LoveStack auf (Commits
e39dde30ff, fbbd333a85, e20570a7ad, b7832d110e), mit Dank.

Entfernen Sie abgelaufene Sitzungen und alte Versionen regelmäßig, wenn Sie die passenden Cron-Teile nicht ausführen:

```bash
php bin/php/ezsessiongc.php --allow-root-user
php bin/php/cleanupversions.php --help --allow-root-user
```

## 9. Eine Site reparieren, die nicht startet

Die häufigste Ursache ist ein fehlendes Verzeichnis `vendor/`: Die Site zeigt dann eine Seite an, die das erklärt. Reparieren Sie sie ohne Shell-Sitzung oder mit einem einzigen Befehl:

```bash
sudo -u WEB_SERVER_USER php bin/php/exprepair.php --create-key
```

Der Schlüssel wird einmal ausgegeben und funktioniert einmal. Öffnen Sie eine beliebige Seite der Site, fügen Sie den Schlüssel in das Feld ein und drücken Sie die Eingabetaste; drei Schritte laufen ab (die Bibliotheken installieren, die Autoloads neu erzeugen, die Caches leeren), und die Seite öffnet sich erneut, wenn sie fertig sind. Prüfen Sie den Zustand jederzeit, und schalten Sie die Funktion ab, wenn Sie sie nicht möchten:

```bash
php bin/php/exprepair.php --status
php bin/php/exprepair.php --disable
```

Bei einer Site, die startet, sich aber falsch verhält, ist die übliche Reihenfolge: den INI- und den Template-Cache leeren (Teil 2), `var/log/error.log` prüfen (Teil 8), `php bin/php/checkclasses.php --allow-root-user` ausführen, um eine Klasse zu finden, die PHP nicht laden will, und nach dem Hinzufügen oder Umbenennen einer Klasse die Autoloads neu erzeugen:

```bash
php bin/php/ezpgenerateautoloads.php -e --allow-root-user
php bin/php/ezcache.php --clear-all --allow-root-user
```

Wenn jede Seite mit einem PHP-Parse-Fehler in `config.php` scheitert, startet die Konsole trotzdem und meldet ihn
(„Warning: config.php does not compile: ..."). Korrigieren Sie die genannte Zeile, kopieren Sie die letzte Sicherung
aus `var/backup/config/` oder `var/backup/setup/` zurück, oder beginnen Sie neu mit der Vorlage:

```bash
./console exp:setup:status                       # nennt den Syntaxfehler
./console exp:setup:config --force --apply       # ersetzt config.php durch config.php-RECOMMENDED, vorher Sicherung
```

Vertiefung: [Reparatur aus dem Browser](../funktionen/6.0/reparatur-im-browser.md), [Reparatur (Upgrade-Seite)](../bc/6.0/reparatur.md),
[.htaccess und config.php von der Konsole aus einrichten](setup-dateien-von-der-konsole.md).

## 10. Eine wöchentliche Checkliste

1. `php bin/php/cache.php status --allow-root-user` endet mit `PASS`.
2. `php bin/php/audit.php status --allow-root-user` zeigt jede Kette als `intact`.
3. `tail -n 50 var/log/error.log` zeigt nichts Neues, das Sie nicht verstehen.
4. `./console crontab:list` zeigt weiterhin Ihre Cron-Einträge.
5. Eine Sicherung aus den letzten sieben Tagen existiert und wurde mindestens einmal auf einem anderen Rechner wiederhergestellt.

## Verwandte Seiten

- Betrieb auf dem Velocity-Server: [Velocity-Server mit persistenten Workern](../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md) und [Velocity-Engines](../bc/6.0/velocity-engines.md).
- Eine neue Site installieren: [Installation mit einem Befehl](../funktionen/6.0/installation-mit-einem-befehl.md) und [Kickstarter](../funktionen/6.0/kickstarter-cli.md).
- `.htaccess` und `config.php`: [.htaccess und config.php von der Konsole aus einrichten](setup-dateien-von-der-konsole.md).
- Was sich geändert hat, Monat für Monat: die [Velocity-Chronik](../geschichte/velocity/README.md).
- Weitere Anleitungen: [Bereitstellung](bereitstellung.md) (Server, HTTPS, eine Änderung ausliefern), [Sicherheit und Audit](sicherheit-und-audit.md) (Härtung, Rollen, das Audit-Protokoll), [Upgrade](aktualisieren.md).
- Hier verwendete Begriffe: [Glossar](../glossar.md).

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
