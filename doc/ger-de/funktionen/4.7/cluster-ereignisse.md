# Cluster-Ereignisse #

Cluster-Ereignisse sind eine neue Funktion, mit der sich mit dem Cluster-System von Exponential interagieren lässt.

Je nach der Aktion, die der Cluster von Exponential ausführt, wird über die Klasse `ezpEvent` ein Ereignis ausgelöst.
Damit kann ein eigenes Listener-Objekt zum Beispiel Metadaten in Cache-Engines speichern (etwa **APC** oder **Memcached**)
und so Datenbankabfragen einsparen (und Leistung und Skalierbarkeit erhöhen).
Diese Ereignisse würden es später auch erlauben, Mediendateien direkt an ein CDN zu übertragen (Akamai, Amazon S3 ...).



## Voraussetzungen ##

Derzeit stehen Cluster-Ereignisse nur in einer Umgebung mit **DFS-Cluster** und **MySQLi-Backend** zur Verfügung.


## Listener für Cluster-Ereignisse ##

Um richtig auf Cluster-Ereignisse zu reagieren, muss ein eigener Ereignis-Listener als PHP-Klasse entwickelt werden.
Diese Klasse muss die Schnittstelle `eZClusterEventListener` implementieren (weitere Informationen in den Kommentaren
der Schnittstelle).

Damit es dann funktioniert, müssen die Cluster-Ereignisse aktiviert und der Listener in
`settings/override/file.ini.append.php` deklariert werden:

```ini
[ClusterEventsSettings]
ClusterEvents=enabled
Listener=MyClusterEventListener
```

### Cluster-Gateway für index_cluster.php ###
Seit 4.7 ist der Cluster-Index (der die Binärdateien ausliefert) überarbeitet (siehe `doc/eng-us/features/4.7/cluster_index.txt`).
Je nach Cluster-Backend wird eine Gateway-Klasse verwendet (`ezpDfsMySQLiClusterGateway` im Fall von DFS MySQLi).

Aus Leistungsgründen werden Cluster-Ereignisse nicht aus den Cluster-Gateways ausgelöst, daher muss der Listener direkt
benachrichtigt werden. Bewährt hat sich, das Gateway zu erweitern und mindestens die Methoden `connect()` und
`fetchFileMetadata()` zu überschreiben, um Ihren Listener zu verwenden:

```php
  <?php
  require_once 'kernel/clustering/dfsmysqli.php';

  class MyClusterGatewayMySQLi extends ezpDfsMySQLiClusterGateway
  {
      private $eventListener;

      public function connect()
      {
          // Inject eZClusterEventLoggerPhp to allow logging through error_log() function instead of eZDebug.
          $this->eventListener = new MyClusterEventListener( new eZClusterEventLoggerPhp );
          $this->eventListener->initialize();
          parent::connect();
      }

      public function fetchFileMetadata( $filepath )
      {
          $cachedMetadata = $this->eventListener->loadMetadata( $filepath );

          if ( $cachedMetadata !== false )
              return $cachedMetadata;

          $clusterMetadata = parent::fetchFileMetadata( $filepath );

          if ( $clusterMetadata !== false )
              $this->eventListener->storeMetadata( $clusterMetadata );

          return $clusterMetadata;
      }

  }
```

Dann sollte die folgende Konfiguration in `config.cluster.php` ergänzt werden

```php
  <?php
  // Adapt settings to your needs
  define( 'CLUSTER_STORAGE_BACKEND', 'dfsmysqli'  );
  define( 'CLUSTER_STORAGE_HOST', 'localhost' );
  define( 'CLUSTER_STORAGE_PORT', 3306 );
  define( 'CLUSTER_STORAGE_USER', 'root' );
  define( 'CLUSTER_STORAGE_PASS', 'root' );
  define( 'CLUSTER_STORAGE_DB', 'cluster' );
  define( 'CLUSTER_STORAGE_CHARSET', 'utf8' );
  define( 'CLUSTER_MOUNT_POINT_PATH', '/mnt/ezdfs' );

  require_once 'extension/myclustereventsextension/classes/MyClusterGatewayMySQLi.php';
  require_once 'kernel/clustering/gateway.php';
  ezpClusterGateway::setGatewayClass( 'MyClusterGatewayMySQLi' );
```

Wenn Sie Konfiguration brauchen, versuchen Sie NICHT, eZINI zu laden, denn es hat viele Abhängigkeiten, die sich nicht
dafür eignen, in `index_cluster.php` geladen zu werden.
Verwenden Sie stattdessen Konstanten oder Konfigurationsobjekte, die Sie Ihrem eigenen Gateway übergeben.


## Unterstützte Ereignisse ##

- `cluster/storeMetadata` - **Benachrichtigung**.
  Das Metadaten-Array wird als Argument übergeben.

- `cluster/loadMetadata` - **Filter**.
  Der Dateipfad, dessen Metadaten gebraucht werden, wird als Argument übergeben.
  Muss das Metadaten-Array oder false zurückgeben.

- `cluster/fileExists` - **Filter**.
  Der Dateipfad wird als Argument übergeben.
  Muss ein Array (numerische Indizes) mit Name (erster Index) und mtime (zweiter Index) zurückgeben, wenn die Datei existiert, sonst false.

- `cluster/deleteFile` - **Benachrichtigung**.
  Der Dateipfad wird als Argument übergeben.

- `cluster/deleteByLike` - **Benachrichtigung**.
  Der Like-Parameter wird als Argument übergeben.

- `cluster/deleteByDirList` - **Benachrichtigung**.
  Übergeben werden: *dirList* (Array), *commonPath* und *commonSuffix*

- `cluster/deleteByNametrunk` - **Benachrichtigung**.
  Nametrunk wird als Argument übergeben.


## Bekannte Einschränkungen ##

Das Cluster-System wird in Exponential *sehr früh* im Lebenszyklus der Anfrage aufgebaut und erlaubt daher keine
Einstellungen in Extensions oder Siteaccesses.

Wer also eine Extension für Cluster-Ereignisse entwickeln will, **MUSS die Konfigurationsdatei(en) in `settings/override/` ablegen**.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
