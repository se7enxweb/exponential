.. -*- coding: utf-8 -*-

=====================================
Anleitung zur Migration des Clusters
=====================================

:Datum: 2009/03/13
:Version: 1.0

Dieses Dokument beschreibt den Upgrade-Vorgang für Anwender, die ihren
Exponential-Cluster von Version 3.10 auf eine Version 4.* aktualisieren
möchten.

.. Note:: **Wenn Sie einen Exponential-Cluster älter als 3.10 aktualisieren
          wollen, lesen Sie zuerst diese Dokumentation** http://pubsvn.ez.no/nextgen/trunk/doc/features/3.10/cluster_enhancement.txt

.. Note:: **Diese Anleitung setzt voraus, dass der Code bereits auf Version 4.1
          aktualisiert ist. Aktualisieren Sie den Cluster nicht vor dem Code,
          das kann zu Datenverlust führen.**

.. sectnum::
.. contents:: Inhaltsverzeichnis

Migration eines Clusters von 3.10.*, 4.0.* auf 4.1
==================================================

Mit dem Datenbank-Backend
-------------------------

Von den Versionen 3.10.*, 4.0.1
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Die Konfigurationsdatei anpassen
++++++++++++++++++++++++++++++++

Die Konfigurationsdatei hat sich leicht geändert. Vor 4.1 sah
``settings/file.ini`` so aus:

::

    [ClusteringSettings]
    FileHandler=ezdb
    DBBackend=mysql
    [...]

Da Exponential 4.1 ein neues Handler-System hat, müssen Sie Ihre
Konfigurationsdatei ``file.ini.append.php`` anpassen.

Dazu genügt diese Konfiguration

Für MySQL:

::

    [ClusteringSettings]
    FileHandler=eZDBFileHandler
    DBBackend=eZDBFileHandlerMysqlBackend
    [...]

Die übrigen Konfigurationsanweisungen bleiben unverändert.

Alle Caches leeren
++++++++++++++++++

Für das Upgrade Ihres Exponential-Clusters auf 4.1 ist kein Auflösen des
Clusters nötig. Sie müssen nur zuerst alle Caches leeren.

::

    php ./bin/php/ezcache.php --clear-all --purge -s <yoursiteaccess>

Die Tabellen aktualisieren
++++++++++++++++++++++++++

Sind alle Caches geleert, aktualisieren Sie die Tabellen mit diesen
SQL-Abfragen.

Für MySQL:

::

    ALTER TABLE ezdbfile ADD name_trunk TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER name ;
    ALTER TABLE ezdbfile ADD INDEX ezdbfile_name_trunk ( name_trunk ( 250 ) )


Von Version 4.0.2
~~~~~~~~~~~~~~~~~

Die Konfigurationsdatei anpassen
++++++++++++++++++++++++++++++++

Die Konfigurationsdatei hat sich leicht geändert. Vor 4.1 sah
``settings/file.ini`` so aus:

::

    [ClusteringSettings]
    FileHandler=ezdb
    DBBackend=mysql
    [...]

Da Exponential 4.1 ein neues Handler-System hat, müssen Sie Ihre
Konfigurationsdatei ``file.ini.append.php`` anpassen.

Dazu genügt diese Konfiguration

Für MySQL:

::

    [ClusteringSettings]
    FileHandler=eZDBFileHandler
    DBBackend=eZDBFileHandlerMysqlBackend
    [...]

Die übrigen Konfigurationsanweisungen bleiben unverändert.


Mit dem neuen Dateisystem-Backend
---------------------------------

Seit Exponential 4.1 gibt es ein neues Dateisystem-Backend: eZFS2.

Dieses Backend arbeitet effizienter mit im Netzwerk freigegebenen Partitionen
(etwa NFS) und behandelt das Leeren und Aktualisieren von Inhalts-Caches und
Cache-Blöcken sauber.

Wenn Sie eine freigegebene Partition nutzen (oder nutzen wollen), wird eZFS2
dringend empfohlen.

.. Note:: Beachten Sie, dass eZFS2 Linux oder Windows mit PHP 5.3 oder neuer voraussetzt.

Die Konfigurationsdatei anpassen
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Für eZFS2 müssen Sie nur Ihre Datei ``file.ini.append.php`` anpassen. Das
geht sogar im laufenden Betrieb der Site.

Vor 4.1 lautete die Konfiguration:

::

    [ClusteringSettings]
    FileHandler=ezfs
    [...]

Ändern Sie sie so:

::

    [ClusteringSettings]
    FileHandler=eZFS2FileHandler
    [...]

Weitere Konfiguration für eZFS2 und eZDB
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

eZFS2FileHandler und eZDBFileHandlerMysqlBackend haben einige
Konfigurationsanweisungen. Die Standardwerte sind unbedenklich. Brauchen Sie
etwas Spezielleres für Ihr Projekt, passen Sie diese Anweisungen an:

file.ini: NonExistantStaleCacheHandling[]
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Legt fest, was geschieht, wenn eine angeforderte Cache-Datei bereits erzeugt
wird und keine abgelaufene Cache-Datei existiert (etwa bei neuem Inhalt).

Zwei mögliche Werte:

- wait: der Prozess wartet in einer Schleife eine begrenzte Zeit, bis die Datei fertig erzeugt ist. Das ist der Standardwert
- generate: der anfragende Prozess erzeugt seine eigenen Daten, ohne das Ergebnis zu speichern

Der Schlüssel des Arrays gibt die Art des betroffenen Caches an. Drei Arten
sind möglich:

- viewcache
- cacheblock
- misc (jeder Cache, der weder viewcache noch cacheblock ist).

Standardkonfiguration in ``settings/site.ini``, Abschnitt ``[ClusteringSettings]``:

::

    NonExistantStaleCacheHandling[]
    NonExistantStaleCacheHandling[viewcache]=wait
    NonExistantStaleCacheHandling[cacheblock]=wait
    NonExistantStaleCacheHandling[misc]=wait

site.ini: CacheGenerationTimeout
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Die längste Erzeugungszeit eines Caches. Bleibt eine Datei länger als diese
Anzahl Sekunden im Erzeugungsmodus, gilt sie als abgelaufen, und der
anfragende Prozess übernimmt die Erzeugung.

Standardkonfiguration in ``settings/site.ini``, Abschnitt ``[ContentSettings]``:

::

    CacheGenerationTimeout=60
