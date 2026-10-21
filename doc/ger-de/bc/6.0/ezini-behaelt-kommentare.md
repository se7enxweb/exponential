# eZINI behält beim Speichern Kommentare bei

Lesen Sie diese Seite, wenn Sie INI-Dateien von Hand bearbeiten und sie außerdem von der Administration oder einem
Skript ändern lassen (Aktivieren einer Erweiterung, Einstellungen von Werkzeugleiste oder Menü, `exp:ini`). Bisher
schrieb jedes Speichern über `eZINI` die ganze Datei neu und verwarf Ihre Kommentare und Ihr Layout. Jetzt ändert ein
Speichern nur die Zeilen, die es ändern muss, und behält alles andere bei. Die API hat sich nicht geändert, und es gibt
nichts zu migrieren.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Speichervorgänge mit direktem Zugriff in `eZINI` passen die Datei an Ort und Stelle an (Round Trip), statt sie neu zu schreiben. Das alte vollständige Neuschreiben bleibt als Rückfallweg. |
| Wer betroffen ist | Alle, deren Override-Dateien (zum Beispiel `settings/override/site.ini.append.php`) Kommentare enthalten. Nur eine positive Änderung. |
| Wie Sie es prüfen | Fügen Sie einem Override einen Kommentar hinzu, aktivieren Sie eine Erweiterung unter Setup > Extensions und sehen Sie sich die Datei an: Der Kommentar ist noch da. |
| Wie Sie es beheben | Nichts zu beheben. Keine Migration von Einstellungen oder Schema. |

## Was sich geändert hat

Bisher verlor `eZINI` beim Speichern Kommentare und Formatierung, weil:

- der Parser Kommentarzeilen (`# ...`) ignorierte;
- der Parser angehängte Kommentare am Zeilenende (`## ...`) entfernte;
- der Schreiber die Datei immer aus `BlockValues` serialisierte.

In echten Abläufen der Administration, etwa beim Aktivieren einer Erweiterung, verlor
`settings/override/site.ini.append.php` einen großen Teil ihres Layouts und ihrer Kommentare.

Jetzt speichert `lib/ezutils/classes/ezini.php` per Round Trip:

- Es behält beim Parsen die ursprünglichen Zeilen der Datei und die Zeilennummern von Abschnitten und Einstellungen;
- beim Speichern passt es nur die Schlüssel und Abschnitte an, die sich geändert haben;
- unberührte Zeilen, Kommentare und Abstände bleiben, wie sie waren.

## Wann der Round Trip verwendet wird

Alle drei Bedingungen müssen gelten:

1. Der Modus mit direktem Zugriff wird verwendet;
2. die Quellzeilen der Zieldatei lassen sich finden;
3. das Anpassen ist für den aktuellen Vorgang sicher.

Dann gilt für ein Speichern:

- vorhandene Kommentare und Leerzeilen bleiben erhalten;
- geänderte Werte werden an Ort und Stelle aktualisiert;
- neue Schlüssel werden im richtigen Abschnitt angehängt;
- gelöschte Schlüssel und Abschnitte werden bei einem vollständigen Speichern (`$onlyModified = false`) entfernt.

Ist eine Bedingung nicht erfüllt, schreibt der alte Serialisierer die Datei wie bisher.

Eine Zeile `[Include <pattern>]` (6.0.15, siehe
[die Anleitung zur Einstellungsseite](../../../eng-us/guides/settings-page.md#splitting-a-large-settings-file-with-include-))
bleibt beim Round Trip so erhalten, wie sie geschrieben ist: Sie ist kein Abschnitt, sie beendet den Abschnitt davor,
und ein vollständiges Speichern entfernt sie nie. Eine Instanz mit direktem Zugriff liest die eingebundenen Dateien
nicht, daher werden deren Einstellungen nie in die gespeicherte Datei kopiert.

## Im echten Einsatz gefundene Korrekturen

Zwei weitere Fehler wurden nach Tests mit echten Dateien behoben:

1. **Aus dem INI-Cache geladene Instanzen.** Kamen die Werte aus dem INI-Cache, konnte der beim Parsen erstellte
   Schnappschuss fehlen. Ein Speichern lädt die Quelle jetzt zuerst von der Festplatte.
2. **Abschnitte und das Ersetzen von Zeilen.** Abschnitte, die nur Kommentare oder gar nichts enthalten, werden beim
   Parsen verfolgt und beibehalten. Das Ersetzen einer Variablen entfernt genau ihre indizierten Zeilen, nicht eine
   Folge benachbarter Zeilen, sodass unbeteiligte Zeilen nicht mehr verloren gehen.

## Kompatibilität

- Geltungsbereich: Speichervorgänge mit direktem Zugriff.
- API: unverändert; Konstruktor, Signaturen und Aufrufer bleiben gleich.
- Ausgabe: dieselben Werte; Kommentare und Layout bleiben jetzt erhalten.

## So prüfen Sie es

1. Fügen Sie auf einer Staging-Kopie eine Kommentarzeile in `settings/override/site.ini.append.php` ein.
2. Aktivieren oder deaktivieren Sie eine Erweiterung unter Setup > Extensions (oder ändern Sie einen Wert mit
   `exp:ini`).
3. Öffnen Sie die Datei erneut. Ihr Kommentar ist noch da, und nur der geänderte Wert hat sich bewegt.

Prüfen Sie die INI-Abläufe der Administration, die Sie verwenden, auf dieselbe Weise (Erweiterungen, Einstellungen von
Werkzeugleiste und Menü).

## Tests

Die Tests stehen in `tests/tests/lib/ezutils/eZINITest.php`. `tests/tests/lib/ezutils/ezini_test.php` ist ein
Kompatibilitätsalias (`ezini_test extends eZINITest`) für Runner, die nach diesem Dateinamen suchen.

Regressionstests für den Round Trip:

1. `testSavePreservesCommentsInDirectAccessMode`
2. `testSaveAppendsNewSettingWithoutDroppingComments`
3. `testSaveRemovesDeletedSettingAndKeepsSectionComments`
4. `testSavePreservesCommentsWhenLoadedFromCache`
5. `testSaveRetainsRealSiteIniStructureWhenUpdatingExtensions`
6. `testSaveRetainsCurrentSiteIniAppendOutsideExtensionSettings`

Sie räumen ihre temporären Fixture-Dateien auf.

So führen Sie sie aus:

```bash
php -l lib/ezutils/classes/ezini.php
./vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/tests/lib/ezutils/ezini_test.php
```

Bei der Änderung festgehaltene Ergebnisse:

| Lauf | Ergebnis |
|---|---|
| `php -l` auf beiden Dateien | keine Syntaxfehler |
| `--filter 'testSave(RetainsCurrentSiteIniAppendOutsideExtensionSettings\|RetainsRealSiteIniStructureWhenUpdatingExtensions\|PreservesCommentsWhenLoadedFromCache)$'` | OK (3 Tests, 17 Assertions) |
| `--filter 'testSave(PreservesCommentsInDirectAccessMode\|AppendsNewSettingWithoutDroppingComments\|RemovesDeletedSettingAndKeepsSectionComments\|PreservesCommentsWhenLoadedFromCache\|RetainsRealSiteIniStructureWhenUpdatingExtensions)$'` | OK (5 Tests, 29 Assertions) |
| die ganze Datei | OK (11 Tests, 50 Assertions) |

## Einführung

1. Stellen Sie den Code bereit.
2. Führen Sie die ganze Testdatei von eZINI aus.
3. Prüfen Sie auf Staging die Schreibabläufe der Administration für INI-Dateien mit Kommentaren (Erweiterungen,
   Einstellungen von Werkzeugleiste und Menü).

## Verwandte Seiten

- [Einstellungen von der Kommandozeile aus ändern: exp:ini](../../../eng-us/features/6.0/exp-ini-command.md)
- [Einstellungen von der Kommandozeile aus ändern: `exp:ini`](console-exp-ini.md)
- [Juni 2026, zweite Hälfte (16. bis 30. Juni)](../../../eng-us/history/2026/2026-06b.md)
