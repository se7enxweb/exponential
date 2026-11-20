# Ein DFS-Cluster mit einem Knoten in einem Befehl: exp:cluster:enable und exp:cluster:disable

Diese Seite ist für Administratoren, die die gespeicherten Dateien einer Installation in einem DFS-Cluster halten
wollen, damit später ein zweiter Webserver dazukommen kann, ohne dass die Website umzieht. Der DFS-Cluster-Dateihandler
des Kernels (`eZDFSFileHandler`) legt jede gespeicherte Datei und jede Cache-Datei in einem **Share**-Ordner ab und
führt ein Verzeichnis davon in zwei Tabellen (`ezdfsfile`, `ezdfsfile_cache`); jeder Webserver, der die Website
ausliefert, liest denselben Share und dieselben Tabellen. Ein **Ein-Knoten-Cluster** ist diese Einrichtung auf einem
Server.

| | |
|---|---|
| Befehle | `./console exp:cluster:enable`, `./console exp:cluster:disable` (`bin/php/expclusterenable.php`, `bin/php/expclusterdisable.php`) |
| Probelauf | beide zeigen, was sie tun würden, und schreiben ohne `--apply` nichts |
| Datenbanken | MySQL/MariaDB (`eZDFSFileHandlerMySQLiBackend`); PostgreSQL (`kernel/sql/postgresql/cluster_dfs_schema.sql`, `kernel/clustering/dfspostgresql.php`, `eZDFSFileHandlerPostgresqlBackend`); SQLite, nur ein Knoten (`kernel/sql/sqlite/cluster_dfs_schema.sql`, `kernel/clustering/dfssqlite.php`, `expDFSFileHandlerSQLiteBackend`, [Clustering mit SQLite](sqlite-dfs-cluster.md)); MongoDB, die Collections aus `kernel/sql/mongodb/cluster_dfs_schema.json` (`kernel/clustering/dfsmongodb.php`, `expDFSFileHandlerMongoDBBackend`, [Clustering mit MongoDB](mongodb-dfs-cluster.md)). `--backend` nennt eines (`mysqli`, `postgresql`, `sqlite`, `mongodb`); andere Installationen erfahren, warum nicht |
| Code | `expClusterSetup` (`kernel/classes/cluster/expclustersetup.php`) |

## exp:cluster:enable

```bash
./console exp:cluster:enable                                       # was getan würde
./console exp:cluster:enable --apply                               # Share: dfs neben der Installation
./console exp:cluster:enable --share=/mnt/e1/dfs/example.com --apply
```

1. Liest die Einstellungen jedes Siteaccess: Alle müssen eine Datenbank nutzen. Cluster-Einstellungen, die die
   Installation schon hat (ein eigenes `[ClusteringSettings]`, ein `config.cluster.php`, das der Befehl nicht
   geschrieben hat, die eines Hosting-Panels), bleiben unangetastet.
2. Legt den Share an (Standard: der Ordner `dfs` neben der Installation, `/var/www/vhosts/example.com/dfs` für
   `/var/www/vhosts/example.com/httpdocs`; mit `--share` ein anderer absoluter Pfad außerhalb davon) mit einer
   Markierungsdatei `.exp-cluster.json`.
3. Legt die Cluster-Tabellen in der eigenen Datenbank der Website aus `kernel/sql/<engine>/cluster_dfs_schema.sql` an.
   Tabellen mit Zeilen, die nicht von einem früheren Lauf des Befehls stammen, werden abgelehnt.
4. Schreibt einen markierten Block `# BEGIN Exponential cluster` ... `# END Exponential cluster` vor den schließenden
   Kommentar von `settings/override/file.ini.append.php` (`[ClusteringSettings] FileHandler=eZDFSFileHandler`,
   `[eZDFSClusteringSettings]` mit der eigenen Datenbankverbindung der Website) und `config.cluster.php` für das
   Cluster-Gateway `index_cluster.php`. Beide Dateien werden vorher nach `var/backup/cluster/<Zeit>/` gesichert; die
   INI-Caches werden geleert.
5. Verschiebt die gespeicherten Dateien mit `bin/php/clusterize.php` hinein, einmal pro var-Verzeichnis (`--no-move`
   verschiebt das auf später). Bricht das Verschieben ab, werden die Einstellungen zurückgestellt.
6. Gibt die Rewrite-Regel des Gateways für den Webserver aus; sie gehört vor die eigenen Regeln der Website:

```apache
# Apache, im virtuellen Host (Plesk: zusätzliche Direktiven); [END] verhindert, dass .htaccess sie wieder umschreibt
RewriteRule ^/var/([^/]+/)?storage/images(-versioned)?/.* /index_cluster.php [PT,END]
```

```apache
# .htaccess (die beiden auskommentierten Zeilen aus .htaccess_root, vor der Regel für index_image.php)
RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* index_cluster.php [L]
RewriteRule ^index_cluster\.php - [L]
```

```nginx
location ~ ^/var/([^/]+/)?storage/images(-versioned)?/ { rewrite ^ /index_cluster.php last; }
```

Der Befehl schreibt keine Webserver-Konfiguration: Die gehört dem, der den Webserver betreibt. Auf einem Knoten sind
die lokalen Kopien der Dateien noch da, die Website funktioniert also schon vor der Regel; ein zweiter Knoten braucht
sie.

## exp:cluster:disable

```bash
./console exp:cluster:disable            # was getan würde
./console exp:cluster:disable --apply
```

Nehmen Sie zuerst die Gateway-Regel aus dem Webserver. Der Befehl holt die Dateien mit `clusterize.php -u` zurück (einmal
pro var-Verzeichnis), nimmt seinen Block aus file.ini, entfernt sein `config.cluster.php` und leert die INI-Caches. Der
Share und die Tabellen bleiben; ein späteres `exp:cluster:enable` leert und füllt sie neu. Cluster-Einstellungen, die
er nicht geschrieben hat, bleiben unangetastet (Exit 2).

## Exit-Codes

| Code | |
|---|---|
| 0 | erledigt, gezeigt (Probelauf) oder schon an (aus) |
| 1 | ein Schritt ist fehlgeschlagen; was geschrieben wurde, wird zurückgestellt |
| 2 | falsche Optionen, oder die Installation kann nicht umgestellt werden (ihre Datenbank, eigene Cluster-Einstellungen) |

## Hosting-Panels

Katalyst von 7x erledigt dieselbe Umstellung für gehostete Websites auf dem Tab Einstellungen der Website, als
Systembenutzer der Website, und schreibt die Gateway-Regel über Plesk. Ein Cluster, den Katalyst angelegt hat, trägt
seine eigene Markierung; die Befehle lassen ihn unangetastet, und Katalyst lässt ihren unangetastet.
