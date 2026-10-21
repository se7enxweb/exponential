# Geteilte Tabellen des DFS-Clusters

Standardmäßig verwendet der MySQLi-DFS-Cluster getrennte Tabellen für Speicher- und Cache-Dateien.

Das macht Wartung und Deployment viel einfacher, weil sich die Cache-Tabelle mit TRUNCATE leeren lässt und damit
sofort alle Caches entfernt sind. Außerdem gibt es einen Leistungsvorteil, weil es auf jeder Tabelle weniger
Konkurrenz um Sperren gibt.

In einer Umgebung mit mehreren Websites können Sie außerdem für verschiedene Websites verschiedene Cache-Tabellen
verwenden, indem Sie für jede Website eine andere Cache-Tabelle festlegen.

## Einstellungen

Die Funktion ist standardmäßig eingeschaltet.

### Konstante CLUSTER_METADATA_TABLE_CACHE in config.[cluster.]php

Standard: "ezdfsfile_cache"

Legt den Namen der Tabelle fest, in der die Metadaten der Cache-Dateien gespeichert werden.
Setzen Sie sie auf eine vorhandene Tabelle, um diese Tabelle für den Cache zu verwenden.

### `eZDFSClusteringSettings.MetaDataTableNameCache` in file.ini

Standard: "ezdfsfile_cache"

Legt den Namen der Tabelle fest, in der die Metadaten der Cache-Dateien gespeichert werden.
Setzen Sie sie auf eine vorhandene Tabelle, um diese Tabelle für den Cache zu verwenden.

Hinweis: CLUSTER_METADATA_TABLE_CACHE ist der INI-Einstellung vorzuziehen, weil die Konstante sowohl die
FileHandler-API in den von index.php bearbeiteten Anfragen als auch den Cluster-Index betrifft, der Binärdateien
über HTTP ausliefert. Wir empfehlen, die Konstante und nicht die INI-Einstellung zu verwenden.

### Konstante CLUSTER_METADATA_CACHE_PATH in config.[cluster.]php

Standard: "/cache/"

Pfadbestandteil der Cache-Dateien, mit dem Cache-Dateien von Speicherdateien unterschieden werden.
Darf *nur geändert werden, wenn* Sie `FileSettings.StorageDir` in site.ini geändert haben.

### Konstante CLUSTER_METADATA_STORAGE_PATH in config.[cluster.]php

Standard: "/storage/"

Pfadbestandteil der Speicherdateien, mit dem Speicherdateien von Cache-Dateien unterschieden werden.
Darf *nur geändert werden, wenn* Sie `FileSettings.StorageDir` in site.ini geändert haben.

## Aktualisierung von Exponential < 5.2

Wenn Sie von einer früheren Version migrieren, wird empfohlen, die Cache-Einträge aus der Speichertabelle zu
entfernen.

Am sichersten gehen Sie dazu so vor:
- Daten aus dem Cluster zurückholen (`bin/php/clusterize.php -u`)
- TRUNCATE TABLE ezdfsfile ausführen
- Daten erneut in den Cluster übertragen (`bin/php/clusterize.php`)

## Abwärtskompatibilität

Diese Änderung bringt keinen ernsthaften Kompatibilitätsbruch mit sich. Wenn Sie die oben beschriebene Aktualisierung
nicht ausführen, ist die einzige Folge, dass die ungenutzten Cache-Einträge in ezdfsfile dort bleiben.

Sie können die Funktion auch ganz abschalten, indem Sie `CLUSTER_METADATA_TABLE_CACHE` in config.cluster.php auf
`ezdfsfile` setzen.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
