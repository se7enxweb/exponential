# Clustering mit MongoDB: das DFS-Backend im Kernel

Diese Seite ist für Administratoren, die eine Website auf MongoDB betreiben und ihre Dateien in einem DFS-Cluster
halten wollen, auf einem Server oder auf mehreren, und für Entwickler, die die Cluster-Backends pflegen. Ab
Exponential 6.0.15 hält der DFS-Cluster des Kernels seine Datei-Metadaten auch in MongoDB, neben MySQL/MariaDB,
PostgreSQL und SQLite: Das Backend, seine Collections und sein Datei-Gateway gehören zum Kernel, ohne Erweiterung und
ohne PHP-Bibliothek, die zu installieren wäre.

## Inhalt

1. [Herkunft und Dank](#herkunft-und-dank)
2. [Was der Kernel mitbringt](#was-der-kernel-mitbringt)
3. [Wie es ohne Transaktionen sperrt](#wie-es-ohne-transaktionen-sperrt)
4. [Erprobte Beispielkonfiguration](#erprobte-beispielkonfiguration)
5. [Ein Ein-Knoten-Cluster mit exp:cluster:enable](#ein-ein-knoten-cluster-mit-expclusterenable)
6. [Mehrere Webserver: ein Replica Set](#mehrere-webserver-ein-replica-set)
7. [Tests](#tests)
8. [Grenzen](#grenzen)
9. [Verwandte Seiten](#verwandte-seiten)

## Herkunft und Dank

Das MongoDB-Backend folgt dem Aufbau des [PostgreSQL-Backends](postgresql-dfs-cluster.md) des Kernels, das eine
Überarbeitung und Integration von **ezpostgresqlcluster** von **eZ Systems AS** ist (GNU General Public License
Version 2, [github.com/ezsystems/ezpostgresqlcluster](https://github.com/ezsystems/ezpostgresqlcluster)) und des
Forks von 7x, [se7enxweb/ezpostgresqlcluster](https://github.com/se7enxweb/ezpostgresqlcluster). Der Entwurf, den es
beibehält (ein Datensatz je Cluster-Datei, die `.generating`-Datensätze, mit denen ein Prozess eine Cache-Datei erzeugt,
während die anderen warten, die Speicher- und die Cache-Collection), ist der Entwurf des Cluster-Codes von eZ Systems.
Die MongoDB-Klasse ist eine neue Umsetzung desselben Vertrags, Methode für Methode am SQLite- und am MySQLi-Backend
des Kernels geschrieben, unter derselben Lizenz (GNU General Public License Version 2 oder später).

## Was der Kernel mitbringt

| Datei | Was sie ist |
|---|---|
| `kernel/private/classes/clusterfilehandlers/dfsbackends/mongodb.php` | `expDFSFileHandlerMongoDBBackend`, das Metadaten-Backend |
| `kernel/sql/mongodb/cluster_dfs_schema.json` | die Collections `ezdfsfile` und `ezdfsfile_cache` und ihre Indizes |
| `kernel/clustering/dfsmongodb.php` | das Gateway, das `index_cluster.php` für `CLUSTER_STORAGE_BACKEND` `dfsmongodb` lädt |

Nötig ist MongoDB 5.0 oder neuer (das Backend markiert Dateien mit einer Update-Pipeline als gelöscht) mit der
PHP-Erweiterung `mongodb` (`pecl install mongodb`). Das Backend verwendet nur die Treiberklassen der Erweiterung
(`MongoDB\Driver\Manager`): Die Bibliothek `mongodb/mongodb`, die der Datenbanktreiber der Website verwendet, braucht
der Cluster nicht. PHP 7.4 ist nutzbar; PHP 8.0 bis 8.5 laufen sauber. Getestet mit MongoDB 7.0 und 8.0 und der
Erweiterung 2.3.

Jede Cluster-Datei ist ein Dokument. Seine `_id` ist `name_hash`, der md5-Wert des Pfads, wie auf jeder Datenbank, so
hat ein Pfad immer genau ein Dokument; `size` und `mtime` sind 64-Bit-Ganzzahlen (keine Grenze im Jahr 2038). Eine
gelöschte Datei behält ihr Dokument mit `mtime` = -ABS(`mtime`) und `expired` = 1, bis sie bereinigt wird, wie auf den
SQL-Datenbanken.

## Wie es ohne Transaktionen sperrt

Das Backend verwendet keine Transaktionen über mehrere Dokumente, deshalb läuft es auf einem einzelnen `mongod` ebenso
wie auf einem Replica Set. Jede Änderung einer Datei ist ein atomarer Schreibvorgang auf einem Dokument, und wo die
SQL-Backends eine Zeilensperre brauchen, nutzt es, was MongoDB für ein einzelnes Dokument zusichert:

- **Eine Erzeugung je Cache-Datei.** `_startCacheGeneration()` fügt das `.generating`-Dokument ein. Seine `_id` ist
  eindeutig, also gelingt das genau einem Prozess; die anderen erhalten den Fehler des doppelten Schlüssels (11000)
  und warten oder liefern die veraltete Datei aus, wie auf den anderen Datenbanken.
- **Übernahmen und Zeitprüfungen sind Vergleichen und Setzen.** Eine Erzeugung, deren Zeit abgelaufen ist, wird mit
  einem Update übernommen, das an die gelesene `mtime` gebunden ist; nur ein Prozess kann es gewinnen.
  `_checkCacheGenerationTimeout()` ist dasselbe Update.
- **Schreibvorgänge auf mehreren Dokumenten haben eine feste Reihenfolge**, sodass ein Prozess, der mittendrin
  abbricht, einen Zustand hinterlässt, mit dem der nächste weitermacht. Eine Datei wird auf dem DFS geschrieben, bevor
  ihr Dokument geschrieben wird. Ein Umbenennen kopiert die Datei, schreibt das Dokument des neuen Namens und entfernt
  erst dann das alte. Das Ende einer Erzeugung benennt die Datei um, setzt Größe und Zeit im Dokument der Datei mit
  einem Upsert und entfernt dann das `.generating`-Dokument. Ein Bereinigen entfernt die gefundenen Dokumente über ihre
  `_id`, dann ihre Dateien.
- **Fehler werden wiederholt, auf nichts wird ewig gewartet.** Ein Netzwerkfehler, ein Primary, der abgetreten ist,
  oder ein Schreibvorgang, den der Server als wiederholbar kennzeichnet, wird bis zu `DBExecuteRetries`-mal erneut
  versucht, mit einer kurzen Pause, die mit jedem Versuch wächst (höchstens 0,2 s). Die Suche nach einem Server wartet
  höchstens `DBLockTimeout` Sekunden (Standard 10).

Namenssuchen verwenden reguläre Ausdrücke statt `LIKE`. Sie unterscheiden Groß- und Kleinschreibung, wie MySQLs
`LIKE` auf der binären Spalte `name`, und ein Muster, das mit festem Text beginnt (`^var/site/cache/`), nutzt den
Namensindex. Das `%` eines LIKE-Musters wird zu `.*`, ein Unterstrich bleibt ein Zeichen (wie im MySQLi-Backend), und
jedes Zeichen regulärer Ausdrücke in einem Dateinamen (`.`, `*`, `?`, `[`, `(`, `+`, `$` ...) steht für sich selbst. Ein
Shell-Platzhalter mit Alternativen (`{a,b}.txt`) wird ein Ausdruck mit `(?:a|b)`.

## Erprobte Beispielkonfiguration

```ini
[ClusteringSettings]
FileHandler=eZDFSFileHandler

[eZDFSClusteringSettings]
MountPointPath=/var/www/vhosts/example.com/dfs
DBBackend=expDFSFileHandlerMongoDBBackend
DBHost=127.0.0.1
DBPort=27017
# die Datenbank; das Konto wird gegen sie angemeldet, wie es der MongoDB-Treiber der Website tut
DBName=example_cluster
DBUser=cluster
DBPassword=secret
# optional: Sekunden, die die Suche nach einem Server dauern darf, bevor der Vorgang fehlschlägt
DBLockTimeout=10
DBConnectRetries=3
DBExecuteRetries=20
```

`DBSocket` kann den Unix-Socket des Servers nennen (`/tmp/mongodb-27017.sock`), statt Host und Port. Das Passwort geht
als Option an den Treiber, nie in eine Verbindungszeichenkette, so kann kein Zeichen darin eine zerbrechen.

Die Collections und ihre Indizes werden einmal angelegt. `exp:cluster:enable` tut das (siehe unten); für eine eigene
Cluster-Datenbank tut es ein Aufruf aus einem Skript, das mit dem Kernel läuft (`php bin/php/ezexec.php <Skript>
--allow-root-user` oder ein Befehl): `expDFSFileHandlerMongoDBBackend::createSchema( $manager, 'example_cluster' )`,
wobei `$manager` ein `MongoDB\Driver\Manager` ist, verbunden mit einem Konto, das Collections anlegen darf. Er liest
`kernel/sql/mongodb/cluster_dfs_schema.json` und lässt vorhandene Collections und Indizes, wie sie sind.

```php
<?php
// config.cluster.php
define( 'CLUSTER_STORAGE_BACKEND', 'dfsmongodb' );
define( 'CLUSTER_STORAGE_HOST', '127.0.0.1' );
define( 'CLUSTER_STORAGE_PORT', 27017 );
define( 'CLUSTER_STORAGE_USER', 'cluster' );
define( 'CLUSTER_STORAGE_PASS', 'secret' );
define( 'CLUSTER_STORAGE_DB', 'example_cluster' );
define( 'CLUSTER_STORAGE_CHARSET', 'utf8' );
define( 'CLUSTER_MOUNT_POINT_PATH', '/var/www/vhosts/example.com/dfs' );
```

Die Datenbank der Website und die Cluster-Datenbank sind getrennte Entscheidungen: Eine Website auf MySQL kann ihre
Cluster-Metadaten in MongoDB halten, und eine Website auf MongoDB in ihrer eigenen Datenbank (die Collections
`ezdfsfile` und `ezdfsfile_cache` liegen dann neben denen der Website).

## Ein Ein-Knoten-Cluster mit exp:cluster:enable

Auf einer Website mit MongoDB als Datenbank (`DatabaseImplementation=mongodb`) wählt `./console exp:cluster:enable
--apply` das MongoDB-Backend von selbst (`--backend=mongodb` nennt es): Es legt die Freigabe an, erstellt die beiden
Collections und ihre Indizes in der Datenbank der Website, schreibt die Einstellungen und `config.cluster.php`
(`dfsmongodb`, Server, Konto und Datenbank der Website) und verschiebt die gespeicherten Dateien hinein. Ein Server
ohne Anmeldung (kein `User`) wird angenommen. `exp:cluster:disable` nimmt es zurück.
[Ein Ein-Knoten-DFS-Cluster mit einem Befehl](cluster-befehle.md) beschreibt beide Befehle.

## Mehrere Webserver: ein Replica Set

Für mehrere Webserver bekommt jeder Server dieselbe Freigabe (`MountPointPath`, ein NFS-Export) und dieselbe
Cluster-Datenbank. Ein MongoDB für den Betrieb ist dafür ein Replica Set; seine ganze Verbindungszeichenkette gehört in
`DBHost` (und für das Gateway in `CLUSTER_STORAGE_HOST`):

```ini
DBHost=mongodb://db1.example,db2.example,db3.example/?replicaSet=rs0&authSource=admin
DBName=example_cluster
DBUser=cluster
DBPassword=secret
```

Eine Zeichenkette, die mit `mongodb://` oder `mongodb+srv://` beginnt, wird so verwendet, wie sie ist; `DBPort` wird
dann nicht beachtet, und das Konto aus `DBUser`/`DBPassword` kommt als Treiberoption hinzu, wenn die Zeichenkette keins
enthält. Auf einem Replica Set wiederholt der Treiber außerdem selbst einen Schreibvorgang einmal, wenn der Primary
wechselt, und die eigenen Wiederholungen des Backends decken den Rest eines Failovers ab.

## Tests

| Test | Was er prüft | Braucht |
|---|---|---|
| `expDFSFileHandlerMongoDBBackendTest` | LIKE-Muster, Präfixe und Platzhalter als reguläre Ausdrücke (jedes Zeichen regulärer Ausdrücke in Namen steht für sich), die Verbindungszeichenkette und ihre Optionen, die wiederholten Fehler, Namensstämme, die Methodengleichheit mit dem MySQLi-Backend, kein SQL und keine Bibliothek im Code, die Schemadatei, das Gateway | nichts (die Wiederholungsprüfungen brauchen die Erweiterung) |
| `expDFSMongoDBBackendIntegrationTest` | die Paritätsprüfungen jedes Backends (`expDFSBackendParityChecks`), dazu die Indizes des Schemas (und dass zweimaliges Anlegen nichts ändert), 64-Bit-Zeiten und `_id` = `name_hash`, die Indexnutzung von Präfixsuchen, Groß- und Kleinschreibung in Namen, Zeichen regulärer Ausdrücke in Namen, das `.generating`-Dokument als Sperre, einen Server, der nicht antwortet, und das Gateway mit Metadaten und Bytebereichen | einen MongoDB-Server (`EXP_TEST_DFS_MONGODB_URI`), die Erweiterung und `pcntl`, `posix` für die Prüfungen mit mehreren Prozessen |

```bash
docker run -d --rm --name dfs-mongo -p 127.0.0.1:27711:27017 mongo:8.0
EXP_TEST_DFS_MONGODB_URI=mongodb://127.0.0.1:27711 php vendor/bin/phpunit \
  tests/tests/kernel/classes/clusterfilehandlers/dfsbackends/expDFSMongoDBBackendIntegrationTest.php
```

Der Integrationstest legt eine eigene Datenbank an (`dfsparity_<pid>_<zufall>`) und löscht sie nach dem letzten Test.
Die Paritätsprüfungen sind dieselben, die die anderen Backends bestehen, darunter **vier Prozesse, die im selben
Moment eine Cache-Datei erzeugen wollen** (einer erzeugt, drei warten, keiner scheitert). Die Prüfung einer Sperre, die
eine andere Verbindung hält, wird mit Absicht übersprungen: MongoDB hat außerhalb einer Transaktion keine Sperre, die
ein Client zwischen zwei Vorgängen hält, also gibt es nichts, worauf ein Speichern warten könnte.

Gemessen am 20. November 2026 auf alpha: der Integrationstest 30 von 31 (die Sperrprüfung mit Absicht übersprungen)
auf MongoDB 8.0 und auf MongoDB 7.0, der Unit-Test 10 von 10, alles auf PHP 8.5 mit der Erweiterung 2.3.3; und der
echte `eZDFSFileHandler` auf dem Backend, von Anfang bis Ende: eine 2-MB-Datei gespeichert, geholt, kopiert,
verschoben, gelöscht und bereinigt, ein View-Cache von `processCache()` einmal erzeugt und von der nächsten Anfrage
gelesen, über seinen Namensstamm abgelaufen, und der Collection-Schritt von `exp:cluster:enable` (angelegt, gezählt,
Dokumente anderer Herkunft abgelehnt, die eigenen geleert). Backend und Gateway bestehen `php -l` auf PHP 7.4 bis 8.5.

## Grenzen

- **Keine Transaktionen über mehrere Dokumente.** Ein Prozess, der mitten in einem Umbenennen oder am Ende einer
  Erzeugung abbricht, hinterlässt beide Namen oder ein `.generating`-Dokument; der nächste Prozess übernimmt die
  Erzeugung nach `CacheGenerationTimeout`, und `bin/php/dfscleanup.php` findet Dateien auf dem DFS ohne Dokument.
- **Keine Zeilensperre, auf die gewartet wird**, deshalb gilt die Prüfung der begrenzten Sperrwartezeit der
  Paritätsprüfungen nicht; ein Schreibvorgang, der wegen Netzwerk oder Failover fehlschlägt, wird stattdessen
  wiederholt.
- **Schreibvorgänge sind so dauerhaft wie das Write Concern.** Das Backend verwendet die Voreinstellung des Servers;
  auf einem Replica Set ist das ab MongoDB 5.0 `majority`. Für die Cluster-Datenbank nicht herabsetzen.
- **Noch kein `eZDFSFileHandlerTest` auf MongoDB**: Der Datenbank-Testlauf des Kernels richtet dafür MySQL- und
  PostgreSQL-Datenbanken ein; das MongoDB-Backend wird stattdessen über die Paritätsprüfungen und den Lauf von Anfang
  bis Ende oben getestet.

## Verwandte Seiten

- [Clustering mit PostgreSQL: das DFS-Backend im Kernel](postgresql-dfs-cluster.md)
- [Clustering mit SQLite: das Ein-Knoten-DFS-Backend im Kernel](sqlite-dfs-cluster.md)
- [Ein Ein-Knoten-DFS-Cluster mit einem Befehl](cluster-befehle.md)
- [MongoDB als Datenbank](mongodb-datenbankunterstuetzung.md)
- [Clustering: DFS und seine Metadaten-Datenbank](../../hilfe/datenbankadministratoren/clustering-dfs-und-die-datenbank.md)
- [Clustering und gemeinsamer Speicher](../../hilfe/hosting/betrieb/clustering-und-gemeinsamer-speicher.md)
- [Änderungsprotokoll 6.0.15](../../../changelogs/6.0/6.0.15.md)
