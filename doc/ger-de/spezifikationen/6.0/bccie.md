# Spezifikation: die Extension bccie

Diese Seite ist die Referenz für die Extension `bccie` (1.1.12), „BC CIE“, in der Administration als **CIE** angezeigt. Sie exportiert die
**gesammelten Informationen** von Inhaltsobjekten (die Antworten, die über ein Kontaktformular, eine Umfrage oder eine Bewerbung gesendet wurden) in
CSV- oder SYLK-Dateien (Excel): aus der Administration, im Hintergrund, über die Konsole und per Cron. Die Seite führt die Modul-Views
und Richtlinien, die Exportoptionen und den Inhalt der Dateien, die Konsolenbefehle, die Cronjob-Teile und jede
Einstellung auf. Die kurze Anleitung für Site-Betreiber ist die [Funktionsseite](../../../en/us/features/6.0/extensions/bccie.md).

## Kurz gesagt

- Einstiegspunkt in der Administration: `/bccie/overview` (der Reiter **CIE**): ein Dashboard mit den Formularen, die Informationen gesammelt haben, den letzten Exporten, dem geplanten Export und den gefundenen Problemen sowie eine Liste der Formulare mit Filter, Sortierung und Blättern.
- Ein Formular wird unter `/bccie/export/<object id>` exportiert: Felder, Zeitraum, Typ (CSV oder SYLK), Trennzeichen und Zeichensatz. Ein kleiner Export wird geschrieben, während Sie warten; ein großer läuft im Hintergrund und wird zum Herunterladen angeboten.
- Dieselbe Arbeit steht als Konsolenbefehle (`ext:bccie:export`, `status`, `purge`) und als zwei Cronjob-Teile (`exportcsv`, `exportsylk`) zur Verfügung. Alle verwenden die Runner-Klasse `bccieRunner`.
- Jede Textzelle wird für eine Tabellenkalkulation unschädlich gemacht: Eine Zelle, die mit `=`, `+`, `-` oder `@` (oder einem Tabulator) beginnt, erhält ein vorangestelltes einfaches Anführungszeichen.
- Keine Tabellen: Die Extension liest `ezinfocollection` und `ezinfocollection_attribute`. Ihr eigener Zustand (die letzten Exporte) besteht aus zwei Zeilen in `ezsite_data`; Hintergrundläufe legen Dateien in `var/<site>/bccie/jobs/` ab.

## Beispiel: ein Formular über die Konsole exportieren und die Datei ansehen

```bash
./console ext:bccie:status                                   # which forms have data
./console ext:bccie:export --object=123 --dry-run            # how many collections would be written
./console ext:bccie:export --object=123 --charset=utf8bom --from=2026-10-01 --output=var/export
```

Öffnen Sie die Datei in einer Tabellenkalkulation: Die erste Zeile ist die Kopfzeile mit den Feldnamen, danach folgt eine Zeile pro Sammlung, und eine
Zelle, die mit `=` begann, zeigt ein vorangestelltes Anführungszeichen statt eines Ergebnisses.

## Modul `bccie`

Jede View verwendet den Navigationsteil `ezbccienavigationpart`, daher kommt das linke Menü in jedem Administrationsdesign aus
`design/standard/templates/parts/bccie/menu.tpl` (die Links sind `[Leftmenu_bccie]` in `menu.ini`).

| View | Richtlinienfunktion | Parameter | Skript |
|---|---|---|---|
| `overview` | `read` | `(offset)`, `(q)`, `(sort)`, `(order)` | `overview.php`: das Dashboard und die Liste der Formulare; das Entfernen ausgewählter Formulare fragt zuerst nach einer Bestätigung |
| `export` | `read` | `ObjectID`, `(job)` | `export.php`: das Formular mit den Optionen, der Download, „Im Hintergrund ausführen“, das Entfernen aller gesammelten Informationen |
| `doexport` | `read` | `ObjectID` | `doexport.php`: die Adresse, an die das Formular früherer Versionen sendet; sie tut dasselbe wie `export` |
| `job` | `read` | `JobID` | `job.php`: Zustand und Protokoll eines Hintergrund-Exports als JSON (HTTP 404 für einen unbekannten Job) |
| `download` | `read` | `JobID` | `download.php`: die Datei eines beendeten Hintergrund-Exports |

Richtlinienfunktionen: `read` (jede View) und, seit 1.1.12, `remove` (Entfernen gesammelter Informationen, aus der Liste und von
der Exportseite; die Rolle **Admin** hat sie). Die View-Skripte sind schlanke Aufrufe ausführbarer Klassen in
`classes/runnable/views/bccie/`; die Arbeit steckt in `bccieExportPage`, `bccieRunner` und `bccieExportUtils`.

## Exportoptionen

| Option | Werte | Hinweise |
|---|---|---|
| Felder | die ID der Sammlung (`contentobjectid`), IDs von Klassenattributen, die Informationen sammeln, `-1` (eine leere Spalte), `-2` (ignoriert) | Eine Kopfzelle und eine Datenzelle pro Spalte, in der angegebenen Reihenfolge. Das alte Formular sendet sie als `field_0`, `field_1`, ...; das neue Formular als `include_id` und `columns[]`. |
| Zeitraum | `start_date`, `end_date` als `YYYY-MM-DD` (oder die alten `start_day`, `start_month`, `start_year` ...) | Eine Sammlung zählt für den Tag, an dem sie angelegt wurde. Ein ungültiges Datum oder ein Beginn nach dem Ende wird mit einer Meldung abgelehnt. |
| Typ | `csv`, `sylk` | Jeder andere Wert wird abgelehnt. |
| Trennzeichen | `;` `,` `:` `\|` `#` | Nur CSV. Die Konsole akzeptiert die Namen `semicolon`, `comma`, `colon`, `pipe`, `hash`. |
| Zeichensatz | die Schlüssel von `ExportOutputFormatHandlers`: `utf8`, `utf8bom`, `utf16le`, `cp1252` | Standardwert `ExportOutputFormatHandlerDefault`. `cp1252` schreibt ein Zeichen, das in der Codepage fehlt, als `?`. |
| Erstellungs- und Änderungsdatum | ein oder aus | Je eine zusätzliche Spalte, ISO 8601. |

### Was die Dateien enthalten

- **CSV**: jede Zelle in doppelten Anführungszeichen, ein doppeltes Anführungszeichen innerhalb einer Zelle verdoppelt, Zellen durch das Trennzeichen getrennt, kein Trennzeichen nach der letzten Zelle, eine Zeile pro Sammlung, ein Zeilenumbruch innerhalb einer Zelle als Leerzeichen geschrieben. Zellen werden von Leerraum am Rand befreit.
- **SYLK**: die Formatdatensätze, eine fette Kopfzeile mit den Feldnamen, dann eine Zeile pro Sammlung; die ID der Sammlung ist eine Zahl, alle anderen Zellen sind Text (`"` verdoppelt, `;` verdoppelt).
- **Text** ist UTF-8 und bleibt unverändert; erst der Zeichensatz-Handler konvertiert die fertige Datei.
- **Formeln**: Eine Textzelle, die mit `=`, `+`, `-` oder `@`, einem Tabulator oder einem Wagenrücklauf beginnt, erhält ein vorangestelltes einfaches Anführungszeichen (`=1+1` wird zu `'=1+1`). Eine reine Zahl, auch mit Vorzeichen (`+4912345`, `-5`), bleibt unverändert, weil eine Tabellenkalkulation sie als Zahl liest, nicht als Formel.
- **Dateiname**: `<ExportFileName><object name>-on-<date>.csv` (`.slk`), wobei der Name auf Buchstaben, Ziffern, Punkt, Bindestrich und Unterstrich reduziert wird.
- **Ein Datentyp mit Handler** (siehe `export.ini`) wird so geschrieben, wie der Handler es vorgibt (der Text der Option, der Name des verknüpften Objekts, der Name des Landes); **jeder andere Datentyp** schreibt seinen gespeicherten Text.

### Direkte Exporte und Hintergrund-Exporte

Ein Download wird geschrieben, während die Seite wartet, bis zu `DirectExportLimit` Sammlungen (2000). Darüber, oder wenn „Im Hintergrund
ausführen“ gedrückt wird, startet `bccieJob` `extension/bccie/bin/php/export.php --job=<id>` mit `expProcessTools` (`setsid`, die
PHP-Kommandozeile). Die Seite fragt `bccie/job/<id>` regelmäßig nach Zustand, Fortschritt (`done` von `total`) und Protokoll ab und zeigt eine
Schaltfläche **Herunterladen**, wenn der Zustand `finished` ist. Die neuesten 20 Jobs und ihre Dateien werden aufbewahrt. Ein Job, der zehn Minuten lang
kein Lebenszeichen zeigt, wird als fehlgeschlagen gemeldet.

## Konsolenbefehle

| Befehl | Klasse | Optionen | Tut |
|---|---|---|---|
| `./console ext:bccie:export` (`@alias cie-export`) | `Exponential\Command\Extension\Bccie\Export` | `--object=ID`, `--format`, `--separator`, `--charset`, `--fields=a,b,c`, `--from`, `--to`, `--days=N`, `--output=DIR`, `--creation-date`, `--modification-date`, `--remove-exported`, `--dry-run`, `--cron`, `--job=ID` | Schreibt die gesammelten Informationen eines Formulars in eine Datei oder führt mit `--cron` den geplanten Export aus den Einstellungen aus |
| `./console ext:bccie:status` (`@alias cie-status`) | `...\Status` | | Gibt das Dashboard als Text aus |
| `./console ext:bccie:purge` (`@alias cie-purge`) | `...\Purge` | `--object=ID`, `--before=YYYY-MM-DD`, `--dry-run`, `--yes` | Entfernt gesammelte Informationen; ohne `--yes` wird nichts entfernt |

Jeder Befehl zeigt seine Optionen mit `--help`. Es läuft jeweils nur ein Export gleichzeitig (eine Sperre im Cache-Verzeichnis, egal wer ihn startet).
Ein Lauf hält für das Dashboard seinen Zeitpunkt und das Geschriebene fest und löst die Ereignisse für ausführbare Klassen des Kernels aus, sodass das Audit ihn wie jeden
Befehl des Kernels sieht; ein Entfernen wird außerdem als `bccie-purge` in das Audit geschrieben.

## Cronjob-Teile

| Block in `cronjob.ini` | Skript (`cronjobs/`) | Klasse | Aufgabe |
|---|---|---|---|
| `[CronjobPart-exportcsv]` | `exportcsv.php` | `Exponential\Cronjob\Extension\Bccie\Exportcsv` | Eine CSV-Datei pro Objekt aus `Collection[]` in `Directory` |
| `[CronjobPart-exportsylk]` | `exportsylk.php` | `Exponential\Cronjob\Extension\Bccie\Exportsylk` | Dasselbe als SYLK-Dateien |

Beide Dateien sind Stubs, die die Klassen aufrufen; `ext:bccie:export --cron --format=sylk` führt denselben Code von Hand aus. Die Dateien heißen
`<object>_export_<date>_<time>.csv` oder, mit `ExportLimitedRange`, `<object>_<from>_to_<to>.csv`. Jedes Feld des Formulars
wird geschrieben, außer den Klassenattributen aus `ExcludeAttributeID`, mit der ID der Sammlung zuerst. Mit `RemoveExported=enabled`
werden die Sammlungen, die eine Datei enthält, entfernt, nachdem die Datei geschrieben wurde; konnte die Datei nicht geschrieben werden, wird nichts entfernt.

## Einstellungen

Geltungsbereich: Extension (`extension/bccie/settings/`). Überschreiben Sie sie in `settings/override/` oder in einem Siteaccess.

| Datei | Block | Schlüssel | Standardwert | Bedeutung |
|---|---|---|---|---|
| `cie.ini` | `CieSettings` | `Directory` | `var/export` | Wohin die geplanten Exporte und `ext:bccie:export` schreiben |
| `cie.ini` | `CieSettings` | `Collection[]` | leer | Objekt-IDs, die der geplante Export abdeckt |
| `cie.ini` | `CieSettings` | `ExcludeAttributeID[]` | leer | IDs von Klassenattributen, die im geplanten Export ausgelassen werden |
| `cie.ini` | `CieSettings` | `ExportLimitedRange`, `DateRangeToExport` | `disabled`, `7` | Nur die letzten N Tage im geplanten Export |
| `cie.ini` | `CieSettings` | `RemoveExported` | `disabled` | Entfernen, was ein geplanter Export geschrieben hat |
| `cie.ini` | `CieSettings` | `CsvSeparator`, `SylkSeparator` | `;` | Trennzeichen des geplanten Exports |
| `cie.ini` | `CieSettings` | `CronUser` | `14` | Benutzer-ID, als die Cronjob-Teile und Befehle laufen (seit 1.1.12) |
| `cie.ini` | `CieSettings` | `DirectExportLimit` | `2000` | Anzahl der Sammlungen, die ein Export aus der Administration höchstens haben darf, um geschrieben zu werden, während Sie warten (seit 1.1.12) |
| `cie.ini` | `CieSettings` | `ExportExecutionTimeLimit` | `180` | Sekunden, die ein Download dauern darf |
| `cie.ini` | `CieSettings` | `ExportZeroToEmptyString` | `disabled` | 0 als leere Zelle schreiben |
| `cie.ini` | `CieSettings` | `ExportUsingDaysCalcualation` | `disabled` | Zeitraum nach Tagen (Schlüssel so geschrieben, wie er ausgeliefert wird) |
| `cie.ini` | `CieSettings` | `ExportFileName`, `ExportFileNameDateFormat` | `bccie_cie_export-`, `Y-m-d_H-i-s` | Präfix und Datum des Dateinamens |
| `cie.ini` | `CieSettings` | `ExportOutputFormatHandlers[]`, `ExportOutputFormatHandlerDefault` | vier Handler, `utf8` | Die Zeichensätze und der Standardwert |
| `export.ini` | `General` | `ExportableDatatypes[]` und ein Block pro Datentyp | siehe Datei | Die Handler-Klasse eines Datentyps |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories[]` | `bccie` | Wo die Cronjob-Skripte gefunden werden |
| `menu.ini` | `TopAdminMenu`, `Leftmenu_bccie` | `Tabs[]`, `Links[]` | `bccie_overview` | Der Reiter und das linke Menü |

## Siehe auch

- [Funktionsseite](../../../en/us/features/6.0/extensions/bccie.md), [Verhaltensänderungen der Extensions](../../../en/us/bc/6.0/extensions-behaviour-changes.md)
- [Abstraktionen für CLI, Cronjobs und Views](../../../en/us/bc/6.0/cli_cronjob_view_abstractions.md), [Cronjobs in der Konsole](../../../en/us/features/6.0/cronjobs-console.md)
- [Chronik](../../../en/us/history/extensions/bccie.md) und [Release Notes](../../../changelogs/extensions/bccie.md)
