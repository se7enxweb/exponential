# Spezifikation: die Extension xrowextract

Diese Seite ist die Referenz für die Extension `xrowextract` (Export und Import von Inhalten, Pakete, Zeitpläne und
Zustellung), Release-Linie 2.5 (2.5.0 bis 2.5.6, September bis Oktober 2026). Sie führt jede View, jede Richtlinie, jede Einstellung,
jedes Feld einer Vorlage, jede Tabelle, jede Klasse und jede Kommandozeilenoption auf. Lesen Sie sie, wenn Sie Exporte für eine Site einrichten,
Berechtigungen vergeben oder Exporte per Skript ausführen. Wie Sie die Extension benutzen, steht auf der
[Funktionsseite](../../../eng-us/features/6.0/extensions/xrowextract.md).

## Beispiel: Artikel über die Kommandozeile exportieren

Im Wurzelverzeichnis der Installation ausführen (für Ihre Inhalte nur lesend; geschrieben wird nur die Ausgabedatei):

```bash
php extension/xrowextract/bin/php/csv.php --list-classes --node=2                 # classes below node 2
php extension/xrowextract/bin/php/csv.php --list-columns --class=ng_article       # columns of one class
php extension/xrowextract/bin/php/csv.php --class=ng_article --node=2 --preview=10 # first 10 rows on screen
php extension/xrowextract/bin/php/csv.php --class=ng_article --node=2 --output=articles.csv
```

`./console ext:xrowextract:csv` führt denselben Befehl aus. Ersetzen Sie `ng_article` durch einen Klassen-Identifier Ihrer Site.
Der Exit-Code `0` bedeutet, dass die Datei geschrieben wurde.

## Modul, Views und Richtlinien

Modul `xrowextract` (`modules/xrowextract/module.php`), Navigationsteil
`ezextractnavigationpart`, Reiter im oberen Menü `xrowextract` (Name „Export“,
`menu.ini [Topmenu_xrowextract]`, URL `xrowextract/csv`).

| View | Richtlinienfunktion | Zweck |
|---|---|---|
| `csv` | `csv` | Exportseite für eine Klasse: Vorschau, Download, Manifest, im Hintergrund ausführen, als Paket exportieren |
| `archive` | `csv` | Site-Archiv (mehrere Klassen) |
| `jobs` | `jobs` | Seite der Jobs; löschen, abbrechen, erneut exportieren |
| `job_status` | `jobs` | Fortschritt eines Jobs als JSON (`JobID`) |
| `job_download` | `jobs` | Datei oder Protokoll eines Jobs herunterladen (`JobID`, `What`) |
| `import` | `import` | Inhaltsdatei importieren; Einstieg für den Upload in Teilstücken; fortsetzen |
| `upload_chunk` | `import` | Endpunkt des Uploads in Teilstücken |
| `package` | `import` | Inhaltspakete: hochladen, untersuchen, installieren, Vorlage erstellen (`PackageName`) |
| `browse`, `browse_file` | `import` | Browser für Paketinhalte (`PackageName`, `Offset`, `ViewIndex`) |
| `compare` | `import` | Ein Paket mit der Site oder einem anderen Paket vergleichen |
| `schedules` | `schedule` | Geplante Exporte und Importe |
| `destinations` | `destinations` | Zustellziele und Zugangsdaten |
| `history` | `history` | Exportverlauf |

Richtlinienfunktionen, die an keine einzelne View gebunden sind: `all_jobs` (die Jobs und geteilten Vorlagen aller
sehen und verwalten; ohne sie sieht ein Benutzer nur seine eigenen) und
`password_hash` (Hash und Hash-Typ von Benutzerkonten exportieren). Ein Link zum
Browser für Paketinhalte wird in die kernel-eigene View `package/view/full/<name>` eingefügt.

## Einstellungen

Alle Schlüssel werden mit `eZINI::instance( <file> )` gelesen. Sie gelten für die ganze Installation,
sofern nicht anders angegeben; überschreiben Sie sie in `settings/override/<file>.append.php` oder in einem
Siteaccess.

### csv.ini

| Block | Schlüssel | Standardwert | Bedeutung |
|---|---|---|---|
| General | `ExportableDatatypes[]` | 30 Datentypen (ezboolean, eztext, ezinteger, ezstring, eztime, ezurl, ezuser, ezxmltext, ezdate, ezdatetime, ezkeyword, eztags, ezobjectrelation, ezemail, ezfloat, ezidentifier, ezenhancedobjectrelation, ezenhancedselection, ezselection, ezenum, ezcountry, ezimage, ezmedia, ezbinaryfile, ezmatrix, ezobjectrelationlist, hmregexpline, ezprice, xrowmetadata) | Datentypen, die der Export anbietet |
| General | `StripURLText` | `true` | Von einem `ezurl`-Wert nur die URL exportieren |
| General | `NeutralizeFormulas` | `enabled` | Eine Zelle, die mit `=`, `+`, `-` oder `@` beginnt (und keine Zahl ist), erhält ein vorangestelltes `'` |
| General | `AllowPasswordHashExport` | `enabled` | `disabled` entfernt die Spalten mit dem Passwort-Hash für alle; andernfalls erhalten sie nur Inhaber von `xrowextract/password_hash` |
| Jobs | `RetentionDays` | `7` | Tage, die ein beendeter oder fehlgeschlagener Job aufbewahrt wird, bevor `ext:xrowextract:job --clean` ihn entfernt |
| Jobs | `PhpCli` | leer | PHP-Kommandozeilenprogramm für Hintergrund-Jobs. Leer: `PHP_BINARY`, wobei ein PHP-FPM- oder php-cgi-Programm auf das CLI-Programm daneben abgebildet wird, sonst `php` |
| Manifest | `EmbedInJSON` | `enabled` | JSON-Export als Umschlag `{"manifest","rows","summary"}`; `disabled` schreibt ein einfaches Array |
| Manifest | `EmbedInXML` | `enabled` | Ein `<manifest>`-Element nach `<columns>` |
| `<datatype>` | `HandlerFile`, `HandlerClass` | ein Block pro Datentyp | Handler dieses Datentyps; aus Ihrer Extension ersetzbar. `[ezenhancedobjectrelation] OutputRelatedObjectNames=true` schreibt Namen, `false` IDs |
| Uploads | `RetentionHours` | `24` | Stunden, die ein unvollständiger oder nie übernommener Upload in Teilstücken aufbewahrt wird |
| Uploads | `MinFreeMarginMB` | `256` | Freier Speicherplatz, der über die eingehende Datei hinaus übrig bleiben muss |
| Uploads | `QueueThresholdRows` | `2000` | Ab dieser Zeilenzahl laufen Probelauf und Anwendung als Hintergrund-Jobs |
| Uploads | `QueueThresholdMB` | `20` | Dasselbe, nach Dateigröße |
| Uploads | `JsonOneShotThresholdMB` | `20` | JSON oberhalb dieser Größe wird trotzdem in einem Zug gelesen (`json_decode()` kann nicht streamen); CSV und XML nie |

### export.ini

| Block | Schlüssel | Standardwert | Bedeutung |
|---|---|---|---|
| ExportSettings | `ExportClasses[]` | leer (alle Klassen) | Die Klassen einschränken, die die Seite anbietet |
| ExportSettings | `StartNodeID` | leer (Wurzelknoten des Standard-Siteaccess) | Knoten, mit dem die Seite beginnt |
| ExportSettings | `DefaultClassID` | leer (Klasse mit den meisten Objekten) | Klasse, mit der die Seite beginnt |
| ExportSettings | `PreselectAttributes` | `true` | Jedes Attribut ankreuzen |
| ExportSettings | `Limit`, `Offset` | `0`, `0` | Zeilenfenster (0 ist unbegrenzt) |
| SiteArchive | `SitesParentNodeID` | leer | Knoten, dessen Kinder die Menge „Sites“ bilden; leer verwendet die Inhaltsstruktur |
| SiteArchive | `DefaultSiteNodeIDs[]` | leer | Zu Beginn ausgewählte Sites; leer verwendet die Wurzel des Standard-Siteaccess |
| PackageTemplate | `ScratchNodeID` | leer (`content.ini [NodeSettings] MediaRootNode`) | Wo der Generator für Beispielinhalte seine Wegwerfobjekte anlegt. Nie ein Knoten, den die öffentliche Site darstellt |

### xrowextract.ini

| Block | Schlüssel | Standardwert | Bedeutung |
|---|---|---|---|
| Schedules | `CronjobEveryMinutes` | `5` | Intervall der Crontab-Zeile, die auf der Seite der Zeitpläne angezeigt wird |
| History | `RetentionDays` | `365` | Tage, die Verlaufszeilen aufbewahrt werden (ein Zeitplan kann einen eigenen Wert setzen) |
| History | `PageSize` | `25` | Zeilen pro Seite |
| Destinations | `LocalPathRoots[]` | leer | Die einzigen Ordner, in die ein lokales oder NAS-Ziel schreiben und aus denen ein geplanter Import lesen darf. Absolut oder relativ zum Wurzelverzeichnis der Installation |
| Destinations | `MaxAttempts` | `3` | Zustellversuche |
| Destinations | `BackoffSeconds` | `10` | Wartezeit zwischen den Versuchen, die sich jedes Mal verdoppelt |
| Destinations | `SshBinaryDir` | leer | Wo `sftp`, `ssh-keyscan` und `ssh-keygen` liegen, wenn nicht in `/usr/bin` oder `/usr/local/bin` |
| Secrets | `KeyFile` | `settings/override/xrowextract-secrets.key` | libsodium-secretbox-Schlüssel für die Zugangsdaten der Ziele, bei der ersten Verwendung mit Modus 0600 erzeugt |
| Notifications | `WebhookSecret` | leer | Wenn gesetzt, tragen Webhooks `X-Xrowextract-Signature` und `X-Xrowextract-Timestamp` |
| `Preset_<id>` | `Name`, `Description`, `Audience`, `View`, `Extends`, `Definition`, `Placeholders` | | Vorlagen der Site; siehe unten |

### Weitere Dateien

| Datei | Block | Zweck |
|---|---|---|
| `cronjob.ini` | `[CronjobPart-xrowextract] Scripts[]=xrowextract.php` | Cronjob-Teil, der fällige Zeitpläne startet und danach Job-Ordner und Verlaufszeilen entfernt, deren Aufbewahrungsfrist abgelaufen ist |
| `extendedattributefilter.ini` | `XrowExtractTranslation`, `XrowExtractHasChildren`, `XrowExtractRelation`, `XrowExtractUserStatus` | Registrierte erweiterte Attributfilter (`createSqlParts`). Der Übersetzungsfilter ist absichtlich nicht vom Benutzer wählbar: Er wird einmal pro exportierter Sprache angewendet |
| `fetchalias.ini` | `xrowextract_recent_content`, `xrowextract_by_section` | Beispiele für benannte Fetches, die der Export anwenden kann |
| `site.ini` | `[RegionalSettings] TranslationExtensions[]=xrowextract` | Übersetzungen: `eng-US`, `fre-FR`, `ger-DE`, `ita-IT`, `untranslated` |

## Definition einer Vorlage

`Definition` ist ein JSON-Objekt. Von `XrowExtractPreset::resolve()` gelesene Felder:
`scope`, `subtree`, `subtree_remote_id`, `class_id`, `class_identifier`,
`mainnodeonly`, `limit`, `offset`, `languages`, `attributes`, `filters` (ein
Werte-Array von `XrowExtractFilters`), `sort_field`, `sort_ascending`, `sort_field2`,
`sort_ascending2`, `output_format`, `separator`, `line_separator`, `escape`. Eine
Zeichenkette darf `{name}` enthalten; `Placeholders` ist `{"name": {"default": "..."}}`.
`Extends` ist `alias:<fetchalias name>[:<siteaccess>]` oder `user:<id>`. Die eigenen Vorlagen
einer Person werden als JSON in `ezsite_data` unter dem Namen
`xrowextract_preset_<20 hex>` gespeichert. Die `Audience`-Gruppen sind Site, Editors,
Developers, Partners, Users und Maintenance; eine Vorlage ohne Gruppe wird unter
Site eingeordnet.

## Tabellen

Bei der ersten Verwendung aus `share/db_schema.dba` angelegt (dem engine-neutralen Schema; `sql/mysql`,
`sql/oracle`, `sql/postgresql` und `sql/sqlite` enthalten dasselbe für eine manuelle Installation).

| Tabelle | Inhalt |
|---|---|
| `xrowextract_schedule` (+ Index `xrowextract_schedule_next`) | Gespeicherte Zeitpläne und ihr nächster Lauf |
| `xrowextract_destination` | Zustellziele; Zugangsdaten verschlüsselt |
| `xrowextract_history` (+ Indizes `_created`, `_job`, `_sched`) | Eine Zeile pro Lauf |

## Klassen

| Klasse | Aufgabe |
|---|---|
| `XrowExtractFilters`, `XrowExtractColumns`, `XrowExtractCatalogue`, `XrowExtractSchema` | Filter, Spalten, Spaltenkatalog und Klassenschema |
| `XrowExtractWriter`, `XrowExtractManifest` | Schreiben der Dateien (CSV, JSON, XML) und das typisierte Manifest |
| `XrowExtractArchive` | Site-Archiv; Formate zip, tar.gz, tar.bz2, tar.xz, 7z, rar |
| `XrowExtractImport`, `XrowExtractUpload` | Streamender Importer und Upload in Teilstücken (`ID_PATTERN` `^[0-9a-f]{32}$`; ein Ordner pro Upload, ein Teilstück wird nur an dem Offset angehängt, den der Server bereits hat) |
| `XrowExtractPackage` | Inhaltspakete (`.ezpkg`): untersuchen, vergleichen, installieren, Vorlage erstellen |
| `XrowExtractJob`, `XrowExtractCron`, `XrowExtractScheduler`, `XrowExtractSchedule` | Hintergrund-Jobs und Zeitpläne |
| `XrowExtractDestination`, `XrowExtractSecrets`, `XrowExtractNotifier`, `XrowExtractHistory` | Zustellung, verschlüsselte Zugangsdaten, Benachrichtigungen, Verlauf |
| `XrowExtractPreset`, `XrowExtractFetchAlias` | Vorlagen und Unterstützung für benannte Fetches |
| `XrowExtractRequirements` | Die Prüfung der 19 Voraussetzungen und 12 Funktionen |
| `classes/parsers/*` | Ein Handler pro Datentyp (`XroweZStringHandler`, ...) |
| `classes/transports/*` | Transporte `local`, `ftp`, `sftp`, `s3`, `webdav`, `http` |

Seit 2.5.5 sind die Kommandozeilenskripte, der Cronjob-Teil und die Modul-Views
Klassen, die von den Dateien aufgerufen werden (`Exponential\Command\Extension\Xrowextract\...`); siehe
[Abstraktionen für CLI, Cronjobs und Views](../../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md).
Das Verhalten hat sich nicht geändert.

## Kommandozeilenoptionen

`ext:xrowextract:csv` (`extension/xrowextract/bin/php/csv.php`) nimmt die Optionen class, node, scope (`node` oder `all`), depth und den Tiefen-Operator
(`eq`, `le`, `ge`), main-only, offset, limit, columns, add, sets
(`identity`, `urls`, `publishing`, `location`, `migration`), names, separator,
Zeilenenden (`win32`/`crlf`, `unix`/`lf`, `mac`/`cr`), unquoted, languages,
format (`csv`, `json`, `xml`, `ezpkg`), Datumsfilter (`date-field`, `since`,
`before`, `date`), section, state, visibility, name, where, sort, order, sort2,
order2, extended-filter, extended-params, fetch-alias, alias-param, preset, param,
list-presets, show-preset, output (`-` ist stdout), preview, list-classes,
list-columns, user, progress-file, keep, changed-since, lenient, no-manifest,
schedule und run-mode entgegen.

Exit-Codes: `0` Erfolg, `1` Fehler (ungültige Option, nicht auflösbare Klasse oder nicht auflösbarer Knoten),
`3` `--lenient` und es blieb nichts zu exportieren. Warnungen werden als
Zeilen `WARNING: ...` ausgegeben; mit `--output=-` gehen sie nach stderr, damit stdout die Datei bleibt.

Das Prüfskript `bin/check.sh` ist die Freigabeprüfung der Release (Lint, Übersetzungen, doppelte
Quellen, PHPStan Level 8, Unit-Tests, Voraussetzungen, CLI-, View- und POST-Tests);
`.github/workflows/check.yml` führt es auf PHP 8.1 bis 8.5 aus. Es braucht
`EXPONENTIAL_ROOT`, das auf ein Exponential-Wurzelverzeichnis zeigt.

## Siehe auch

- [Funktionsseite](../../../eng-us/features/6.0/extensions/xrowextract.md), [Verhaltensänderungen der Extensions](../../../eng-us/bc/6.0/extensions-behaviour-changes.md), [Abstraktionen für CLI, Cronjobs und Views](../../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md)
- [Chronik](../../../eng-us/history/extensions/xrowextract.md), [Release Notes](../../../changelogs/extensions/xrowextract.md)
- Monate: [2024-01](../../../eng-us/history/extensions/months/2024-01.md), [2024-02](../../../eng-us/history/extensions/months/2024-02.md), [2024-03](../../../eng-us/history/extensions/months/2024-03.md), [2024-04](../../../eng-us/history/extensions/months/2024-04.md), [2024-07](../../../eng-us/history/extensions/months/2024-07.md), [2024-08](../../../eng-us/history/extensions/months/2024-08.md), [2026-03](../../../eng-us/history/extensions/months/2026-03.md), [2026-09](../../../eng-us/history/extensions/months/2026-09.md), [2026-10](../../../eng-us/history/extensions/months/2026-10.md)
