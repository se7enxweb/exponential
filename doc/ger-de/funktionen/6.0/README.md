# Funktionen von Exponential 6.0

Diese Seite führt jede Funktionsseite der Linie Exponential 6.0 auf, damit Sie die gesuchte finden, ohne suchen zu
müssen. Jede Seite sagt, für wen sie gedacht ist, was die Funktion tut und wie Sie sie verwenden. Die älteren
Erweiterungen, Themes und Pakete haben ein eigenes Verzeichnis:
[Erweiterungen, Themes und Pakete](erweiterungen/README.md). Jede Änderung steht mit ihrem
Datum im [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md).

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../beispiele/webserver/nginx.md), [LiteSpeed](../../beispiele/webserver/litespeed.md), [Caddy](../../beispiele/webserver/caddy.md), [IIS](../../beispiele/webserver/iis.md),
> [Velocity](../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../../hilfe/hosting/control-panels/plesk.md), [cPanel](../../hilfe/hosting/control-panels/cpanel.md), [DirectAdmin](../../hilfe/hosting/control-panels/directadmin.md), [ISPConfig](../../hilfe/hosting/control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../anleitungen/seiten-cache-mit-litespeed.md).

Die Funktionsseiten selbst sind noch nicht alle ins Deutsche übersetzt; die Links führen bis dahin zu den englischen
Seiten.

## Hier beginnen: Velocity ist Teil von Exponential 6.0.15

Seit 6.0.15 ist der Velocity Internet / Web Application Server Teil von Exponential: Die `composer.json` im
Wurzelverzeichnis verlangt `se7enxweb/exponential-velocity ~0.0.4.57` (über `se7enxweb/exponential-php-floor`, auf PHP 8.0 und neuer), sodass jede Installation ihn hat, auf PHP 8.0
und neuer. Das markiert einen Wendepunkt, einen Grundstein und einen Schlussstein für das Projekt: eine neue Stufe der
Integration von Anwendung und System, mit noch mehr Funktionen von Haus aus und standardmäßig im Kernprodukt.
Exponential ist ein einziger funktionierender Stack (Server, Plattform für schnelle Anwendungsentwicklung, CMS,
Digital Experience und E-Commerce), nicht nur ein Framework. Lesen Sie zuerst
**[Velocity ist Teil von Exponential 6.0.15](../../hilfe/velocity-ist-teil-von-exponential-6-0-15.md)**.

Velocity ist die empfohlene Engine für jede Stufe, FrankenPHP ist die produktionsreife Alternative, und Apache oder
nginx mit PHP-FPM funktionieren weiter wie bisher.

## Exponential Velocity, der Internet- und Webanwendungsserver

- [Velocity-Webserver](velocity-webserver.md): der Überblick
- [Velocity: Exponential in einem Webserver mit persistenten Workern betreiben](velocity-server-mit-dauerhaften-workern.md)
- [Der Antwort-Cache von Velocity](velocity-antwort-cache.md)
- [HTTPS und Zertifikate in Velocity](velocity-https-zertifikate.md)
- [Statische Dateien und Bildverarbeitung in Velocity](velocity-statische-dateien-und-bilder.md)
- [WebSockets, Server-Sent Events und Räume in Velocity](velocity-websockets-und-ereignisse.md)
- [Bedienfeld und Dashboard von Velocity](velocity-control-panel.md)
- [Die Q-Shell von Velocity](velocity-q-shell.md)
- [Der Zeitplaner von Velocity](velocity-zeitplaner.md)
- [Erkennung, Föderation und Deployment in Velocity](velocity-erkennung-foederation-und-deployment.md)
- [Pakete, Docker-Images und Binärdateien von Velocity](velocity-pakete-und-binaerdateien.md)
- [Velocity und der Opcode-Cache: warum eine Seite kostet, was sie kostet, und wie Sie es messen](velocity-opcode-cache-und-profil.md)
- [uwebserver](velocity-uwebserver.md)
- [Befehle zur Serversteuerung: exp:webserver, exp:frankenphp und exp:solr](befehle-fuer-webserver-und-solr.md)

## Installieren, bereitstellen und betreiben

- [Exponential mit einem einzigen Befehl installieren](installation-mit-einem-befehl.md)
- [Kickstarter: eine ganze Site aus einer Datei installieren](kickstarter-cli.md)
- [Konsolenbefehle für .htaccess und config.php](befehle-fuer-htaccess-und-config-php.md)
- [Was eine neue Installation mitbringt: die Standardauswahl an Erweiterungen](standard-extension-auslieferung.md)
- [Erweiterungen einer neuen Installation (6.0.15)](erweiterungen-einer-neuen-installation.md): mitgeliefert, installiert, standardmäßig aktiviert, ihre Tabellen
- [Eine saubere Installation, die zu Shared Hosting passt und Exponential heißt](standardwerte-einer-sauberen-installation.md)
- [Das neue Aussehen des Einrichtungsassistenten und der Siteaccess für den Editor](einrichtungsassistent-und-editor-siteaccess.md)
- [Einstellungen pro Umgebung (EXP_ENV)](umgebungseinstellungen.md)
- [Vom Deployment verwaltete Konfiguration (ManagedByDeployment)](durch-deployment-verwaltet.md)
- [Installationsname: Staging nie wieder mit Produktion verwechseln](installationsname-in-seiten.md)
- [Einstellungen auf der Kommandozeile ändern: exp:ini](exp-ini-befehl.md)
- [Die Systemkarte: exp:expfetch, exp:info und exp:about](systemkarte.md)
- [Benchmark: `exp:benchmark`](benchmark.md)
- [Wartungsmodus](wartungsmodus.md)
- [Migrationen im Kernel](migrationen.md) (Rezepte, Zielgruppenseiten und FAQ: [Migrationen](../../hilfe/migrationen/README.md))
- [Eine defekte Installation im Browser reparieren](reparatur-im-browser.md)
- [Prüfung der Dateikonsistenz und die Dateiliste des Release](dateikonsistenzpruefung.md)
- [Qualitätsprüfungen: Git-Hooks, Dateimanifest, Codestil und PHPStan](qualitaetspruefungen.md)
- [Exponential Components: die Bibliotheken des Kernels, geforkt aus den Zeta Components](expcomponents.md):
  `se7enxweb/expcomponents-*` unter Apache 2.0, dieselben `ezc`-Klassen, die Paketzuordnung und die Geschichte
- [Cache leeren, indem Verzeichnisse beiseitegeschoben werden](cache-leeren-durch-umbenennen.md)
- [Inhalts- und INI-Cache in Redis oder Valkey halten](valkey-cache-hooks.md)
- [cache_until: Seiten, die pünktlich ablaufen](cache-until.md): ein Template sagt, bis wann seine Ausgabe gilt
  (`{cache_until( 'tomorrow' )}`), und View-Cache, Cache-Blöcke, HTTP-Cache, Velocity, CDN und Browser halten die Seite
  höchstens bis dahin; `seconds_until`, die Zeitzone neuer Installationen
- [Generator für den statischen Cache](generator-fuer-den-statischen-cache.md)
- [Statisches Veröffentlichen: eine statische Kopie einer Website für GitHub Pages und jeden statischen Host](statisches-veroeffentlichen.md)
- [Request Shield: eine Web Application Firewall vor jeder Anfrage](request-shield.md)
- [Auf Abruf erzeugte Bild-Aliase](bild-aliase-auf-abruf.md): beim Erzeugen einer Seite nur berechnet, erzeugt, wenn
  der Browser fragt; WebP/AVIF, Vorab-Erzeugung, Staging aus der Produktion, Setup > Bild-Aliase und `exp:image:alias`
- [Cronjob-Konsole](cronjobs-in-der-konsole.md), [Cronjob-Teile pausieren](cronjobs-pausieren.md),
  [Einen Cronjob-Teil auslassen](filter-fuer-cronjob-teile.md)
- [Rotation und Bereinigung der Logs](log-rotation.md), [Der Log-Viewer](log-betrachter.md),
  [Anfrage-Logs: 404.log und security.log](anfrageprotokolle.md)
- [Einstellungen pro Site in Erweiterungen (Hosting mehrerer Sites)](ini-ueberschreibungen-fuer-mehrere-websites.md)
- [Logs, INI-Cache und Ablaufzeitstempel pro Site (Hosting mehrerer Sites)](log-und-cache-pfade-fuer-mehrere-websites.md)

## Datenbanken

- [Eine Datenbank wählen](../../hilfe/datenbank-waehlen.md): jede Datenbank von 6.0.15 in einer Matrix und welche
  für einen Server, einen Cluster, eine verwaltete Cloud-Datenbank, eine Demo, Auswertungen und Importe, Windows passt
- [Unterstützung für die Datenbank SQLite](sqlite-datenbank.md)
- [MongoDB als Datenbank](mongodb-datenbankunterstuetzung.md)
- [PostgreSQL 18 und 19](postgresql-18-und-19.md): unterstützte Versionen und ihre Tests, Client-Versionen,
  Aktualisieren und ein Cluster auf 18 und 19
- [Microsoft-SQL-Server-Treiber](sqlserver-datenbanktreiber.md): `expSQLServerDB` für SQL Server 2017 bis 2025 und Azure
  SQL, PHP-Schichten, Einstellungen, der Installer und Tests
- [Datenbanken mit dem PostgreSQL-Protokoll](postgresql-wire-datenbanken.md): `expPostgreSQLWireDB` und seine Profile
  für CockroachDB, YugabyteDB, Spanner (PGAdapter), Neon, Supabase und Aurora; was je Datenbank geht und was nicht
- [ODBC-Treiber](odbc-datenbanktreiber.md): `expODBCDB` für jede Datenbank mit ODBC-Treiber, der SQL-Dialekt in den
  Einstellungen; was je Dialekt geht (SQL Server, PostgreSQL, MariaDB, SQLite) mit PDO_ODBC und der odbc-Erweiterung
- [Firebird-Treiber](firebird-datenbanktreiber.md): `expFirebirdDB` für Firebird 4 und 5 über pdo_firebird ab PHP
  8.4; eine ganze Site, wie das SQL des Kernels umgeschrieben wird, was geht und was nicht
- [MS-Access-Unterstützung](ms-access-unterstuetzung.md): `exp:access:import` (MDBTools unter Linux, der
  Access-ODBC-Treiber unter Windows) und der kleine Treiber `expMSAccessDB`; was Access kann und was nicht
- [Datenbanken, die das MySQL-Protokoll sprechen](mysql-wire-datenbanken.md): TiDB, Vitess und PlanetScale,
  SingleStore, MariaDB Xpand und Aurora MySQL auf dem MySQL-Treiber mit je einem Profil; was je Datenbank geht und was
  nicht
- [IBM-Db2-Treiber](db2-datenbanktreiber.md): `expDb2DB` für Db2 11.5 und 12.1 über pdo_ibm; eine ganze Site, wie das
  SQL des Kernels umgeschrieben wird, was geht und was nicht, Lizenzen
- [IBM-Informix-Treiber](informix-datenbanktreiber.md): `expInformixDB` für Informix 14.10 und 15 über pdo_ibm und den
  DRDA-Listener; den Server vorbereiten, was geht und was nicht
- [DuckDB-Treiber](duckdb-datenbanktreiber.md): `expDuckDBDB`, eine ganze Site in einer DuckDB-Datei über PHPs
  FFI-Erweiterung; es schreibt immer nur ein Prozess; was geht und was nicht
- [Apache-Cassandra- und ScyllaDB-Treiber](cassandra-scylladb-treiber.md): `expCassandraDB` und `expScyllaDB`,
  Datentabellen-Verbindungen und `exp:cassandra:import` über CQL; warum keine Site darauf läuft, mit Messungen
- [libSQL- und Turso-Treiber](libsql-datenbanktreiber.md): `expLibSQLDB` und `expTursoDB`, der SQLite-Treiber über die
  HTTP-API eines libSQL-Servers (sqld, Turso); eine ganze Site auf einem Server, den sich mehrere Webserver teilen; was
  geht und was nicht
- [Couchbase-Treiber](couchbase-treiber.md): `expCouchbaseDB`, das SQL des Kernels als SQL++ über die REST-API des
  Query-Dienstes; Datentabellen-Verbindungen und `exp:couchbase:import`; warum keine Site auf Couchbase läuft, gemessen
- [Apache-CouchDB-Treiber](couchdb-treiber.md): `expCouchDBDB`, Tabellen als CouchDB-Dokumente, SQL auf einer
  SQLite-Kopie im Speicher beantwortet; Datentabellen-Verbindungen und `exp:couchdb:import`; warum keine Site auf CouchDB
  läuft, gemessen
- [Spaltenorientierte Datenbanken: MonetDB und ClickHouse](spaltenorientierte-datenbanken.md): `expMonetDBDB`, eine
  ganze Site auf MonetDB über sein Protokoll in reinem PHP, immer nur ein Schreiber; `expClickHouseDB`, ClickHouse über
  HTTP für Datentabellen und Importe, und warum es keine Site halten kann; was geht und was nicht, gemessen
- [SAP-HANA-Treiber](hana-datenbanktreiber.md): `expHANADB` für SAP HANA 2.0 und HANA Cloud über SAPs HANA-ODBC-Treiber;
  wie das SQL des Kernels umgeschrieben wird, gebundene Literale, was geht und was nicht
- [SAP-ASE-Treiber](ase-datenbanktreiber.md): `expASEDB` für SAP Adaptive Server Enterprise (Sybase) 15.7 und 16.0 auf
  dem SQL-Server-Treiber, über FreeTDS oder ODBC; geschrieben und ohne Server getestet
- [Clustering in Exponential 6.0](clustering-ueberblick.md): Anwendungsserver, gemeinsame Dateien, Primärserver und
  Replikate, Caches, Leeren und Zustandsprüfungen an einer Stelle, mit allen Cluster-Seiten
- [Clustering mit PostgreSQL: das DFS-Backend im Kernel](postgresql-dfs-cluster.md)
- [Clustering mit SQLite: das Ein-Knoten-DFS-Backend im Kernel](sqlite-dfs-cluster.md)
- [Abfragen über ID-Mengen beliebiger Größe: expDBInList und InListChunkSize](in-listen-beliebiger-groesse.md)
- [Clustering mit MongoDB: das DFS-Backend im Kernel](mongodb-dfs-cluster.md)
- [Clustering mit Oracle: das DFS-Backend im Kernel](oracle-dfs-cluster.md)
- [Clustering mit SQL Server: das DFS-Backend im Kernel](sqlserver-dfs-cluster.md)
- [Lesereplikate: PostgreSQL, MySQL und MariaDB, Oracle](../../bc/6.0/postgresql-lesereplikat.md)
- [AdminNeo: die Datenbankverwaltung hinter dem Database Source Editor](adminneo-datenbankverwaltung.md)
- [Datentabellen: die Tabellen der Module als Teil des Systems](datentabellen.md): der Katalog der persistenten
  Klassen, benannte Datenbankverbindungen, Klassen auf ihrer eigenen Verbindung

## Schnelle Anwendungsentwicklung und Erweiterungen

- [RAD-Werkzeuge: Assistenten für Erweiterungen, Katalog, Bestandsaufnahme und Zustandsprüfung](rad-werkzeuge.md)
- [Erweiterungen in mehr als einem Ordner](zusaetzliche-extension-verzeichnisse.md)
- [Setup > Extensions: Ladereihenfolge und sicheres Speichern](ladereihenfolge-der-extensions.md)
- [Liste der Erweiterungen: jede Erweiterung sortieren, untersuchen und herunterladen](extension-liste-und-downloads.md)
- [Eine Erweiterung kann ein Modul des Kernels ersetzen](modulueberschreibung-durch-extensions.md)
- [Filter für Zugriff und View-Cache in Erweiterungen](zugriffs-und-cache-filter.md)
- [Einschränkungen von Inhaltsrichtlinien durch Erweiterungen](handler-fuer-inhaltseinschraenkungen.md)
- [Icon-Themes, die in Ihrer Erweiterung liegen](icon-themes-in-extensions.md)
- [Die Website-Seitenleiste (expwt) anstelle der Website-Toolbar (ezwt)](website-seitenleiste.md): der Wechsel mit
  `exp:wt:migrate-from-ezwt` und dem Upgrade-Schritt 6.0.15-expwt-enable
- [Pagelayout-Hooks: eine Stelle am Ende jeder Seite für Erweiterungen](seitenlayout-hooks.md)
- [Inhalte aus einfachen Werten: expContentWriter](inhalte-schreiben.md): Der Kernel legt Inhalte aus Textwerten in
  wenigen Zeilen an, ändert und entfernt sie, für Skripte, Importe, Migrationen, Integrationen und die REST API
- [Entfernte Dienste: die ganze Administration als HTTP-API (expservices)](entfernte-dienste-expservices.md)
- [Ein DFS-Cluster mit einem Knoten in einem Befehl](cluster-befehle.md): `exp:cluster:enable` und `exp:cluster:disable` legen
  Share, Cluster-Tabellen und Einstellungen an und verschieben die Dateien hinein (oder zurück); die Gateway-Regel wird für den Webserver ausgegeben
- [Die GraphQL-API: genau die Felder abfragen, die eine Seite braucht](graphql.md): `/api/graphql`, ein aus den
  Inhaltsklassen erzeugtes Schema, Richtlinien gelten immer, Setup > GraphQL mit einem Playground,
  `exp:graphql:status`, `exp:graphql:schema` und `exp:graphql:query`
- [Exponential Tron: die MCP-Server-Schicht](exponential-tron-mcp-server.md): KI-Agenten arbeiten als eigene Benutzer
  in einer Installation, mit ablaufenden Tokens, unter den Richtlinien der Installation, jeder Aufruf protokolliert;
  stdio und HTTPS unter `/tron/mcp`; Programme in Ihren eigenen Extensions (ein verlangtes Paket, nicht standardmäßig aktiv)
- [Anfrageregeln: entscheiden, was mit einer Anfrage geschieht, bevor ihr View läuft](anfrageregeln.md)
- [Prüfungen von Rollen und Richtlinien in Templates](template-operatoren-fuer-rollen-und-richtlinien.md)
- [String-Funktionen von PHP als Template-Operatoren](template-operatoren-fuer-zeichenketten.md)
- [Template-Editor: Overrides anlegen, ordnen und bearbeiten, ohne INI-Dateien anzufassen](template-editor-ueberschreibungen.md)
- [Design > Templates: Reihenfolge der Overrides per Drag and Drop](reihenfolge-der-template-ueberschreibungen.md)
- [Kommentare mit Template-Pfaden: sehen, welches Template welches Markup geschrieben hat](template-pfad-kommentare.md)
- [Debug-Ausgabe, die Sie auf einen Blick lesen und gestalten können](verbesserte-debug-ausgabe.md)
- [Die Exp-Debug-Leiste](exp-debug-leiste.md)

## Pakete

- [ezpm: der Paketmanager auf der Kommandozeile](ezpm-paketmanager-cli.md)
- [Der Paketassistent](paketassistent.md)
- [Ein Paketinstaller, der große Pakete übersteht](paketinstallation-in-teilschritten.md)
- [In ein Paket hineinsehen, es mit Ihrer Site vergleichen, einzelne Elemente importieren](pakete-vergleichen-und-importieren.md)
- [Paketlizenzen und Semantic Versioning](paketlizenzen-und-versionen.md)
- [Die Seiten „Über“ und „Copyright“](info-und-paketseiten.md)

## Content-Management und die Administration

- [Das Design admin4](admin4-design.md)
- [Das Design admin4l: die Admin-Seite, aus Layouts aufgebaut](admin4l.md)
- [Das responsive Admin-Design (admin3)](admin3-responsives-admin.md)
- [Breite und Schriftgröße der linken Seitenleiste](admin3-seitenleistenbreite-und-schriftgroesse.md)
- [Exponential Admin UI: das Design und der Siteaccess adminui](exp-adminui.md)
- [Das Design der Administration für Ihre Sitzung wählen](ui-umschalter-design.md)
- [Navigation in der Administration: der oberste Knoten, die Registerkarten und das Durchsuchen von Inhalten](oberster-knoten-des-inhaltsbaums-im-admin.md)
- [Admin-Links folgen den Berechtigungen](admin-links-folgen-den-berechtigungen.md)
- [Registerkarten der Administration ausblenden: HiddenTabs](ausgeblendete-admin-reiter.md)
- [Blättern, Sortieren und Seitengrößen in der Administrationsoberfläche](seitenweise-listen-im-admin.md)
- [Eigene Anzahl von Einträgen pro Seite](eigene-anzahl-eintraege-pro-seite.md)
- [Sites vorladen (Administrationsoberfläche)](ansicht-websites-vorladen.md)
- [Schaltflächen für die schnelle Platzierung](schaltflaechen-fuer-schnelle-platzierung.md)
- [Lesezeichenordner](lesezeichenordner.md)
- [Lesezeichen für Systemseiten](lesezeichen-fuer-systemseiten.md)
- [Der Content Finder](inhalts-finder.md)
- [Inhaltsaufträge: große Entfernen-, Kopier- und Verschiebevorgänge im Hintergrund](inhaltsauftraege.md)
- [Die Inhaltsprüfungen: nicht referenzierte Objekte, defekte Beziehungen und fehlende Dateien](inhaltspruefungen.md)
- [Inhaltsklassen: ein Attribut nach ganz oben oder ganz unten verschieben](klassenattribute-an-den-rand-verschieben.md)
- [Die Liste der Unterelemente: 129 Spalten, Voreinstellungen und CSV-Export](optionen-der-unterelementtabelle.md)
- [Ausgewählte Unterelemente kopieren](unterelemente-auswahl-kopieren.md)
- [Entfernen und Verbergen: die Prüfung der umgekehrten Beziehungen](pruefung-umgekehrter-beziehungen.md)
- [Der Papierkorb zeigt, wer ein Objekt gelöscht hat und wo es war](papierkorb-wer-und-wo.md)
- [Versionsvorschau in anderen Ansichtsmodi](ansichtsmodi-der-versionsvorschau.md)
- [Veröffentlichen ohne Benachrichtigung](veroeffentlichen-ohne-benachrichtigung.md)
- [Der Online-Editor auf TinyMCE 8 (optional, neben TinyMCE 3)](online-editor-tinymce8.md)
- [Rich Text: Zeichen oberhalb von U+FFFF und TeX-Quelltext in benutzerdefinierten Tags](rich-text-zusatzzeichen-und-tex-quelltext.md)
- [PDF-Export](pdf-export.md)
- [RSS: Feeds für Apple Podcasts, eine seitenweise Feedliste und sicherere Exporte](rss-podcast-und-feed-liste.md)
- [jQuery 4 in der Administration, und YUI ist fort](jquery4-und-entfernung-von-yui.md)
- [Das Hilfesystem: die Dokumentation in der Administration](hilfesystem.md)
- [Hilfe, als Inhalt gepflegt](hilfeinhalte.md)

## Sites, Sprachen und Digital Experience

- [Links zu Inhalten einer anderen Site einer Multisite-Installation](websiteuebergreifende-links-im-multisite.md)
- [Siteaccess nach Host-Präfix und die Weiterleitung zur Sprache](siteaccess-zuordnung-nach-host-und-sprache.md)
- [URL-Zuordnungen importieren und exportieren: alte Adressen nach einem Umzug erhalten, mit Rückgängig](url-zuordnungen-importieren-und-exportieren.md)
- [Übersetzungen und Sprachen](uebersetzungen-und-sprachen.md)
- [Sprachen mit Schreibrichtung von rechts nach links in der Administration](rechts-nach-links.md)
- [Sprache der Oberfläche und der UI-Umschalter](ui-umschalter.md)
- [Designs eines öffentlichen Siteaccess umschalten](front-design-umschalter.md)
- [Die Liste der Oberflächensprachen: jede Sprache, die meistgebrauchten zuerst](ui-umschalter-sprachen.md)
- [Die gruppierte Länderliste](laendergruppen.md)
- [Eine brauchbare robots.txt von Haus aus](robots-txt.md)
- [Das Produkt heißt Exponential](umbenennung-in-exponential.md)

## E-Commerce

- [Shop-Dashboard und Bestellstatus](shop-dashboard.md)
- [Dauerhafte Bestellbelege](bestellbelege.md)
- [Meine Bestellungen: die Bestellliste des Kunden](meine-bestellungen.md)
- [Die Bestellliste nach jeder Spalte sortieren](sortierung-der-bestellliste.md)
- [Aus welchem Siteaccess eine Bestellung kam](bestellungen-nach-siteaccess.md)

## Benutzer, Berechtigungen und Sicherheit

- [Das Prüfprotokoll: wer was getan hat, und der Nachweis, dass niemand den Eintrag geändert hat](pruefprotokoll.md)
- [Der Geheimnisspeicher: Passwörter und Schlüssel der Werkzeuge, verschlüsselt, nach Namen](geheimnisspeicher.md)
- [Content Security Policy](content-security-policy.md)
- [Sichere Weiterleitungen und „zurück, woher Sie gekommen sind“](sichere-weiterleitungen.md)
- [Korrekturen aus den Forks von Opencontent und Mugo Web: Grenzen, einzeilige Logs und sicherere Standards](korrekturen-aus-den-legacy-forks.md):
  `MaxPathDepth`, einzeilige `eZLog`-Einträge, REST-Listener, die Upload-Sperrliste, das `HardLimit` für Teilbäume
- [„Dieses Formular ist abgelaufen“: Ablehnungen durch das Formular-Token](seite-abgelaufenes-formular.md)
- [SOAP-Aufrufe über HTTPS: Zertifikate einer internen Zertifizierungsstelle](soap-client-tls.md)
- [Ihr Passwort ändern](moderne-passwortaenderung.md)
- [Passwort bei der nächsten Anmeldung ändern](passwort-bei-naechster-anmeldung-aendern.md)
- [Zweiter Schritt auf den anderen Anmeldewegen: REST, WebDAV, SOAP, expservices; App-Passwörter](zweiter-schritt-auf-anderen-anmeldewegen.md)
- [Das Passwort eines Benutzers auf der Kommandozeile zurücksetzen](benutzerpasswort-zuruecksetzen.md)
- [Benutzerverwaltung von der Konsole: die Befehle exp:user für Konten, Gruppen, Rollen, Sitzungen, Import und Export](benutzerverwaltung-von-der-konsole.md)
- [Benutzerkonten prüfen](benutzerpflege.md)
- [Benutzer-Auditprotokoll: wer was an einem Konto geändert hat, Anmeldungen und Ablehnungen](benutzer-auditprotokoll.md)
- [Ihre Geräte: angemeldete Sitzungen je Benutzer, endgültig abgemeldet, eine E-Mail über ein neues Gerät](benutzersitzungen.md)
- [Signierte Links in der E-Mail „Neuer Benutzer registriert“](admin-links-der-registrierung.md)
- [Administratoren geben neue Redakteurskonten frei](freigabe-von-redakteurskonten.md)
- [Rollen: IDs der Richtlinien, Sortierung und Schaltflächen für die Reihenfolge](reihenfolge-der-richtlinien-einer-rolle.md)
- [Die Benutzer und Gruppen einer Rolle, seitenweise](seitenweise-rollenzuweisungen.md)
- [Berechtigungsprüfungen für Benutzer mit vielen Rollenzuweisungen](berechtigungspruefung-bei-vielen-zuweisungen.md)
- [Schnellere Zugriffsarrays für Benutzer mit vielen Rollenzuweisungen](vorabladen-des-zugriffsarrays.md)
- [Benachrichtigungen: die Anleitung für Benutzer](benachrichtigungen.md)
- [E-Mail-Einstellungen: die Anleitung für Benutzer](e-mail-einstellungen.md)
- [Postfächer lesen: die gemeinsamen Regeln](postfaecher-lesen.md): automatische Antworten, Verzögerungsmeldungen,
  ein fehlschlagendes Konto oder eine Nachricht, Löschen nach dem Speichern, nachgebildete IMAP- und POP3-Server für Tests

## Die Linie Exponential Platform

- [Das Skelett von Exponential Platform DXP: create-project und eine dokumentierte Installation](plattform-dxp-skeleton.md)
- [Exponential Platform Nexus: eine fertige Site in Minuten](plattform-nexus-starter.md)
- [Die Administrationsoberfläche von Exponential Platform](plattform-admin-ui-fork.md)
- [Layouts auf der Plattform: der Fork von layouts-core](plattform-layouts-core-fork.md)
- [Site-Bundles, Metadaten und die Twig-Werkzeuge der Plattform](plattform-site-bundles.md)
- [SQLite für Exponential Platform: kein Datenbankserver nötig](plattform-sqlite-installation.md)
- [PHP 8.4 und 8.5 für den älteren Symfony-Stack: die Framework-Forks](plattform-php85-framework-forks.md)
- [Legacy Bridge: Exponential 6 innerhalb der Symfony-Plattform betreiben](legacy-bridge.md)

## Stand der Dokumentation für 6.0.15

Die Dokumentation von 6.0.15 wurde am 27. und 28. November 2026 gegen den Code im Hauptzweig geprüft: die Historie seit
dem Tag 6.0.14 (7.095 Commits), jeder Konsolenbefehl `exp:*` (146, jeder auf Englisch und Deutsch dokumentiert), jede
seit 6.0.14 in `settings/*.ini` hinzugekommene Einstellung (1.393 Schlüssel) und jeder Datenbanktreiber in
`lib/ezdb/classes/`. Was fehlte, veraltet oder dünn war, und was getan wurde:

| Bereich | War | Jetzt (Englisch und Deutsch) |
|---|---|---|
| Datenbanken: die Unterstützungsaussage und die Matrix | veraltet: SAP HANA und SAP ASE fehlten in der Matrix; die Treiber für SAP, Cassandra, ClickHouse und MonetDB hießen „in Entwicklung“, obwohl sie im Hauptzweig sind | [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md) und [Eine Datenbank wählen](../../hilfe/datenbank-waehlen.md) nennen jeden Treiber im Hauptzweig, mit den geplanten Datenbanken (noch ohne Code) statt des alten Absatzes |
| Datenbanken: je Treiber | vollständig: eine Funktionsseite je Treiber | unverändert; siehe [Datenbanken](#datenbanken) |
| Setup-Assistent: die Datenbankliste (Reihenfolge, die Beschriftung MariaDB / MySQL Improved, SAP HANA und MonetDB angeboten, SQL Server über ODBC) | veraltet | [Kapitel 5, Datenbanksystem auswählen](../../installation/05-setup-assistent.md#535-datenbanksystem-auswählen): jedes Angebot mit seiner Bedingung, die Reihenfolge, die Standardwerte für SAP HANA, MonetDB, libSQL und Turso |
| Setup-Assistent auf einer installierten Site (der Wächter) | vollständig | unverändert |
| Exponential Tron, die MCP-Server-Schicht | fehlte; neun Seiten sagten, Exponential liefere keinen MCP-Server | neu: [Exponential Tron: die MCP-Server-Schicht](exponential-tron-mcp-server.md); die Seiten zu Integration, KI-Agenten und Standards berichtigt |
| xne (Exponential Network), das Upgrade-Werkzeug | dünn: nur nebenbei genannt | neu: [Upgrade mit xne](../../anleitungen/aktualisieren-mit-xne.md); Kapitel 11 verweist darauf; die Upgrade-Beweise für MongoDB und Oracle berichtigt |
| Velocity: die Einstellungen, die eine Site von Apache übernehmen (`DropMaster`, `MimeTypes`, `DefaultCharset`, `StaticRoots[]`, Fehlerdokumente, der Host-Fallback) | fehlte | [Velocity-Engines: eine Site von Apache übernehmen](../../bc/6.0/velocity-engines.md#eine-site-von-apache-übernehmen-was-velocityini-hinzufügt) |
| HTTP-Cache im Cluster: `SharedStateCacheTime` | fehlte | [Der HTTP-Cache](../../bc/6.0/httpcache.md) |
| Konsolenbefehle (`exp:*`) | vollständig | unverändert |
| Velocity und `exp:velocity`, expqueue und Jobs, Layouts, Bild-Aliase, der Request Shield, das admin4-Design, Windows | vorhanden, mit Funktions-, Anleitungs- und Hilfeseiten in beiden Sprachen | in diesem Durchgang nicht Zeile für Zeile gegen die neuesten Commits gelesen |

**Noch offen, zum Einplanen:**

1. Ein Durchgang Zeile für Zeile durch die Seiten zu Layouts, expqueue und Inhaltsjobs, Bild-Aliasen, dem Request
   Shield, dem admin4-Design und Windows gegen die Commits vom 20. bis 27. November 2026.
2. Die Administration von xne in einer Installation (Setup > Network, `exp:network:*`), sobald sie kommt; heute ist sie
   nur geplant.
3. Das eigene Handbuch von Exponential Tron (`extension/tron/doc/`) gibt es nur auf Englisch; die Funktionsseite des
   Kernels gibt es in beiden Sprachen.
4. Die Datenbankseiten sagen, was xne noch nicht aktualisiert (die in 6.0.15 hinzugekommenen Treiber); sie ändern sich,
   wenn xne es tut.
