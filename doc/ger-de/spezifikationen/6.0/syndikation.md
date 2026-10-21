# Spezifikation: die Extension syndication

Diese Seite ist die Referenz für die Extension `syndication` (1.3.3). Eine Site **exportiert** einen Teil ihres Inhaltsbaums
als Feed; eine andere Site **importiert** diesen Feed über SOAP und hält ihn per Cron auf dem gleichen Stand. Die Seite führt die
Modul-Views und Richtlinien, die SOAP-Funktionen, die Tabellen, die Cronjob-Teile, jede Einstellung und die Art auf, wie sich ein Import
authentifiziert. Lesen Sie sie, wenn Sie die Syndizierung installieren, zeitlich planen oder Fehler darin suchen. Die Einrichtung Schritt für Schritt steht auf der
[Funktionsseite](../../funktionen/6.0/erweiterungen/syndication.md).

## Kurz gesagt

- Einstiegspunkt in der Administration: `/syndication/menu` (der Reiter **Syndizierung** der Administration): ein Dashboard mit dem, was exportiert und was importiert wird, den letzten Läufen und den gefundenen Problemen.
- Zwei Cronjob-Teile erledigen die Arbeit: `export_feed` auf der exportierenden Site, `import_feed` auf der importierenden Site.
- Die importierende Site ruft den SOAP-Server der exportierenden Site auf; seit 1.2.0 kann sie dabei Login und Passwort senden.
- Elf Tabellen, angelegt aus `sql/mysql.sql` oder, auf jeder Datenbank, aus `share/db_schema.dba` (seit 1.3.1).

## Beispiel: einen Export und einen Import von Hand ausführen

Nachdem ein Feed und ein Import in `/syndication/menu` eingerichtet sind, führen Sie jeden Teil einmal im Wurzelverzeichnis der Installation aus und
lesen seine Ausgabe, bevor Sie ihn in Cron eintragen:

```bash
# on the exporting site
php runcronjobs.php export_feed

# on the importing site
php runcronjobs.php import_feed
```

Prüfen Sie danach die importierten Objekte unter **Syndizierung > Importe**. Führen Sie beide Teile alle paar Minuten per Cron aus; für den Export
werden 3 Minuten empfohlen.

## Modul `syndication`

Jede View verwendet den Navigationsteil `ezsyndicationnavigationpart` (seit 1.3.3; vorher `ezsyndicationpart`), daher
kommt das linke Menü in jedem Administrationsdesign aus `design/standard/templates/parts/syndication/menu.tpl`. Das alte
`parts/s/menu.tpl` bleibt bestehen und bindet es ein.

| View | Richtlinienfunktion | Parameter | Skript |
|---|---|---|---|
| `menu` | `menu` | `(job)` | `menu.php`: das Dashboard; legt die Tabellen an; startet „alle exportieren“ und „alle abrufen“ im Hintergrund |
| `list` | `view_export` | `(offset)`, `(q)`, `(sort)`, `(order)` | `list.php`: filtern, sortieren, blättern; Entfernen fragt zuerst nach einer Bestätigung |
| `import_list` | `view_export` | `(offset)`, `(q)`, `(sort)`, `(order)` | `import_list.php` |
| `import_edit` | `edit_import` | `ImportID`, `(step)` | `import_edit.php`: der Assistent mit fünf Schritten |
| `import_info` | `view_import` | `ImportID`, `(offset)`, `(job)` | `import_info.php`: Einstellungen, Zustände der Einträge, „jetzt abrufen“, „jetzt importieren“ |
| `pending_edit` | `import_object_status` | `ImportID`, `(offset)`, `(statusFilter)` | `pending_edit.php` |
| `edit` | `edit_export` | `FeedID` | `edit.php`: prüft den Namen, den Identifier (eindeutig) und die Zahlen |
| `feed_info` | `view_export_info` | `FeedID`, `(offset)`, `(job)` | `feed_info.php`: Einstellungen, Quellen, exportierte Einträge, „jetzt exportieren“ |
| `add_feed_source` | `edit_export` | `FeedID`, `Step`, `(source_type)` | `add_feed_source.php`; ohne Feed leitet sie Sie zur Liste der Feeds weiter |
| `list_source_filter` | `edit_export` | `SourceFeedID` | `list_source_filter.php` |
| `edit_source_filter` | `edit_export` | `SourceFilterID` | `edit_source_filter.php` |
| `edit_import_filter` | `edit_import` | `ImportFilterID` | `edit_import_filter.php` |
| `job` | `menu` | `JobID` | `job.php`: Zustand und Protokoll eines Hintergrundlaufs als JSON |

Richtlinienfunktionen: `import_object_status`, `view_export`, `edit_export`, `remove_feed`, `create_feed`, `menu`,
`view_import`, `edit_import`, `view_export_info`, `create_import` und `fetch_feed` mit der Einschränkung `Feed`.
Vor 1.3.3 nannte `import_info` als Skript `import_info` (ohne `.php`) und als Funktion `import_view`, die die Funktionsliste
nicht definierte, und `feed_info` deklarierte kein Skript; beide Views sind jetzt vollständig.

## Konsolenbefehle, Cronjob-Teile und Hintergrundläufe (seit 1.3.3)

Die Arbeit steckt in ausführbaren Klassen (dem Muster #207 des Kernels); die Dateien in `bin/php/` und `cronjobs/` bestehen jeweils aus einem Aufruf.
Ein Lauf setzt eine Sperre (jeweils ein Export und ein Import gleichzeitig, egal wer ihn startet), schreibt Zeitpunkt und Ergebnis für das
Dashboard und löst die Ereignisse für ausführbare Klassen des Kernels aus, sodass das Audit ihn wie jeden Cronjob des Kernels sieht.

| Befehl | Klasse | Optionen | Tut |
|---|---|---|---|
| `./console ext:syndication:export` (`@alias syn-export`) | `Exponential\Command\Extension\Syndication\Export` | `--dry-run`, `--feed=ID`, `--max-objects=N` | Schreibt den Export-Cache der aktiven Feeds |
| `./console ext:syndication:import` (`@alias syn-import`) | `...\Import` | `--dry-run`, `--import=ID`, `--fetch-only`, `--import-only`, `--limit=N` | Ruft die Eintragslisten ab und importiert die wartenden Einträge |
| `./console ext:syndication:install` | `...\Install` | `--dry-run` | Legt die Tabellen aus `share/db_schema.dba` an |
| `./console ext:syndication:status` | `...\Status` | | Gibt das Dashboard als Text aus |

| Cronjob-Teil | Klasse | Stub |
|---|---|---|
| `export_feed` | `Exponential\Cronjob\Extension\Syndication\ExportFeed` | `cronjobs/syndication_export.php` |
| `import_feed` | `Exponential\Cronjob\Extension\Syndication\ImportFeed` | `cronjobs/syndication_import.php` |

Der Cronjob und die Befehle überspringen Feeds und Importe, die nicht aktiv sind, und laufen als der Benutzer aus `[Syndication] CronUser`.
Die Schaltflächen „jetzt ausführen“ der Administration starten denselben Befehl mit `expProcessTools` (`setsid`, die
PHP-Kommandozeile) im Hintergrund und zeigen seinen Zustand und seine Ausgabe auf der Seite; die Zustandsdateien liegen in `var/<site>/syndication/jobs/`, die neuesten 20 werden aufbewahrt.

## SOAP-Funktionen

`soap/initialize.php` registriert diese Funktionen am SOAP-Server der exportierenden Site (`soap.ini`
`[ExtensionSettings] SOAPExtensions[]=syndication`):

| Funktion | Parameter | Verwendet für |
|---|---|---|
| `fetchSyndicationFeedList` | | Die Feeds, aus denen eine importierende Site wählen kann |
| `fetchSyndicationFeedItemList` | `feedID`, `modified` | Die seit einem Zeitpunkt geänderten Einträge eines Feeds |
| `fetchSyndicationFeedObjectList` | `feedID` | Die Objekte eines Feeds |
| `fetchSyndicationFeedContentObject` | `feedID`, `remoteID` | Ein Objekt, nach Remote-ID |
| `fetchSyndicationFeedRelatedContentObject` | `feedID`, `remoteID`, `relatedRemoteID` | Ein Objekt, das mit einem Objekt des Feeds in Relation steht |
| `hostID` | | Die Identität des exportierenden Hosts |

## Klassen

Alle in `classes/`: `eZSyndication`, `eZSyndicationFeed`, `eZSyndicationFeedCacheManager`, `eZSyndicationFeedItem`,
`eZSyndicationFeedItemExport`, `eZSyndicationFeedItemStatus`, `eZSyndicationFeedSource`,
`eZSyndicationFeedSourceFilter`, `eZSyndicationFilter`, `eZSyndicationImport`, `eZSyndicationImportFilter`. Die
Filter aus `[SyndicationFilters]` sind `eZFilterSection` und `eZFilterAttribute` in `classes/filter/`; beide erweitern `eZSyndicationFilter`.

## Tabellen

Elf Tabellen, definiert in `sql/mysql.sql` und, seit 1.3.1, im engine-neutralen `share/db_schema.dba`, sodass ein
Installer sie auf jeder Datenbank anlegen kann, die Exponential unterstützt. Das SOAP-Protokoll hat drei Indizes.
(`sql/mysql.sql` enthält außerdem zwei auskommentierte `ezsyndicate_export_event`-Tabellen; sie werden nicht angelegt.)

| Tabelle | Inhalt |
|---|---|
| `ezsyndication_feed` | Ein Feed (eine Exportdefinition) |
| `ezsyndication_feed_source` | Die Quellen eines Feeds im Inhaltsbaum |
| `ezsyndication_feed_source_filter` | Filter einer Feed-Quelle |
| `ezsyndication_filter` | Filterdefinitionen |
| `ezsyndication_feed_item` | Die Objekte eines Feeds |
| `ezsyndication_feed_item_export` | Exportzustand pro Eintrag |
| `ezsyndication_feed_item_status` | Status eines Eintrags |
| `ezsyndication_feed_cache` | Zwischengespeicherte Feed-Dateien |
| `ezsyndication_import` | Eine Importdefinition (Server, Login, Options-Array, Zielknoten) |
| `ezsyndication_import_filter` | Filter eines Imports |
| `ezx_ezpnet_soap_log` | Protokoll der SOAP-Aufrufe |

## Cronjob-Teile

| Block in `cronjob.ini` | Skript (`cronjobs/`) | Aufgabe |
|---|---|---|
| `[CronjobPart-export_feed]` | `syndication_export.php` | Schreibt die Export-Feeds |
| `[CronjobPart-import_feed]` | `syndication_import.php` | Ruft die entfernten Feeds ab und importiert sie |

Die Extension trägt sich selbst in `[CronjobSettings] ExtensionDirectories[]` ein, sodass `runcronjobs.php` beide Skripte findet. Beide sind Stubs, die die oben genannten ausführbaren Klassen aufrufen.

## Einstellungen

Alle Werte sind die eigenen Standardwerte der Extension (Geltungsbereich: Extension, `extension/syndication/settings/`). Überschreiben Sie sie in
`settings/override/` oder in einem Siteaccess.

| Datei | Block | Schlüssel | Standardwert | Bedeutung |
|---|---|---|---|---|
| `syndication.ini` | `SyndicationFilters` | `FilterArray[]` | `Section`, `Attribute` | Filter, die für eine Feed-Quelle oder einen Import angeboten werden |
| `syndication.ini` | `Syndication` | `CacheDir` | `syndication` | Cache-Ordner der Feed-Dateien |
| `syndication.ini` | `Syndication` | `CronUser` | `14` | Benutzer-ID, als die die Cronjob-Teile laufen |
| `soap.ini` | `GeneralSettings` | `EnableSOAP` | `true` | SOAP ausliefern (auf der exportierenden Site nötig) |
| `soap.ini` | `ExtensionSettings` | `SOAPExtensions[]` | `syndication` | Lädt `soap/initialize.php` |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories[]` | `syndication` | Wo die Cronjob-Skripte gefunden werden |
| `cronjob.ini` | `CronjobPart-export_feed` | `Scripts[]` | `syndication_export.php` | Der Export-Teil |
| `cronjob.ini` | `CronjobPart-import_feed` | `Scripts[]` | `syndication_import.php` | Der Import-Teil |
| `browse.ini` | `SyndicationFeedSourceBrowse` | `StartNode`, `TopLevelNodes[]` | `content`; `content`, `media` | Wo eine Feed-Quelle ausgewählt werden kann |
| `browse.ini` | `SyndicationSetImportPlacement` | `StartNode`, `TopLevelNodes[]` | `content`; `content`, `users`, `media` | Wo ein Import Inhalte platzieren kann |
| `content.ini` | `CustomTagSettings` | `AvailableCustomTags[]` | `syndication` | Das Custom Tag `syndication` |
| `menu.ini` | `TopAdminMenu` | `Tabs[]` | `syndication` | Der Reiter der Administration; das linke Menü verlinkt auf `menu`, `list`, `import_list`, `add_feed_source` (jeweils nur einem Benutzer angezeigt, den die Richtlinie zulässt) |

## Authentifizierung des Imports (seit 1.2.0)

`eZSyndicationImport` liest Login und Passwort für die exportierende Site aus der Serveradresse des Imports
(`https://<login>:<password>@<host>/`) oder aus den Schlüsseln `login` und `password` des Options-Arrays (den Feldern **Login** und
**Passwort** im ersten Schritt des Imports). Die Verbindung geht bei `https` an Port 443, sonst an Port 80.
Schützen Sie den SOAP-Endpunkt der exportierenden Site mit einer HTTP-Zugriffskontrolle und halten Sie diese Zugangsdaten aus der
Versionsverwaltung heraus.

## Siehe auch

- [Funktionsseite](../../funktionen/6.0/erweiterungen/syndication.md), [Verhaltensänderungen der Extensions](../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- [Cronjobs in der Konsole](../../funktionen/6.0/cronjobs-in-der-konsole.md), [Betrieb einer Site](../../anleitungen/betrieb-einer-website.md)
- [Chronik](../../geschichte/erweiterungen/syndication.md), [Release Notes](../../../changelogs/extensions/syndication.md)
- Monate: [2025-09](../../geschichte/erweiterungen/monate/2025-09.md), [2026-03](../../geschichte/erweiterungen/monate/2026-03.md), [2026-07](../../geschichte/erweiterungen/monate/2026-07.md), [2026-09](../../geschichte/erweiterungen/monate/2026-09.md), [2026-10](../../geschichte/erweiterungen/monate/2026-10.md)
