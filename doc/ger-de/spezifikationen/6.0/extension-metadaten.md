# Spezifikation: Metadaten von Extensions (`ezinfo.php` und `extension.xml`)

Diese Seite ist die Referenz dafür, wie eine Extension dem System mitteilt, wer sie ist: ihren Namen, ihre Version, ihre
Lizenz und ihre Website, wie sie unter **Setup > Über** angezeigt und von den Upgrade-Prüfungen verwendet werden. Lesen Sie
sie, wenn Sie eine Extension schreiben oder veröffentlichen oder wenn eine Extension auf der Seite „Über“ eine falsche
Version oder keine Lizenz anzeigt.

Seit September 2026 bringt jede Extension, jedes Design und jedes Theme, das mit oder neben Exponential 6.0.15 ausgeliefert
wird, beide Dateien mit und hält sie mit seiner Release im Gleichschritt.

## Kurz gesagt

- Schreiben Sie sowohl `extension.xml` (mit einem Block `<metadata>`) als auch `ezinfo.php`. `extension.xml` wird zuerst gelesen.
- Die Version in beiden Dateien ist die Release-Version ohne `v` (Tag `v6.0.8` ergibt `6.0.8`).
- Erhöhen Sie beide in dem Commit, auf den der Release-Tag zeigt, und setzen Sie erst dann den Tag.
- Prüfen Sie mit `php -l ezinfo.php` und `xmllint --noout extension.xml`.

## Der Leser: `expInfo`

Die Klasse `expInfo` (`lib/ezutils/classes/expinfo.php`) ist die stabile, nur lesende API für die Metadaten von Extensions.
Sie liest beide Dateien und normalisiert sie. Ihre öffentlichen statischen Methoden:

| Methode | Rückgabe |
|---|---|
| `activeExtensions()` | Metadaten jeder aktiven Extension, mit dem Verzeichnisnamen als Schlüssel |
| `availableExtensions()` | Metadaten jeder Extension auf der Festplatte, aktiv oder nicht |
| `hasActiveExtension( $name )` | Ob die genannte Extension aktiv ist |
| `extensionInfo( $name, $activeOnly = false )` | Metadaten einer Extension oder `false`, wenn sie nicht gefunden wird (oder inaktiv ist, wenn `$activeOnly` true ist) |
| `activeNames()` | Die Verzeichnisnamen der aktiven Extensions |
| `kernelInfo( $section = false )` | Informationen über die Kernel-Installation |

Die normalisierten Felder sind `name`, `description`, `summary`, `version`, `copyright`, `author`, `license` und
`info_url`. Eine Zusammenfassung wird zur Beschreibung, wenn keine Beschreibung angegeben ist.

### Lesereihenfolge

| Reihenfolge | Quelle | Regel |
|---|---|---|
| 1 | `extension.xml` | Wird zuerst gelesen. Die Felder kommen aus `<metadata>`, wenn die Datei einen solchen Block hat, sonst aus der obersten Ebene der Datei. Nicht leere Werte haben Vorrang |
| 2 | `ezinfo.php` | Wird verwendet, wenn `extension.xml` fehlt oder nicht geparst werden kann. Klasse `<extensionname>Info` mit einer Methode `info()`, die ein Array zurückgibt; Schlüssel werden ohne Beachtung der Groß- und Kleinschreibung abgeglichen. Ein Schlüssel, den `extension.xml` bereits geliefert hat, wird nicht ersetzt |
| 3 | `composer.json` und der Git-Origin | Füllen, was keine der beiden Dateien geliefert hat (Version, Beschreibung, Lizenz, Website) |

Parse-Fehler von `extension.xml` werden gesammelt und dann gelöscht, sodass ein persistenter Worker keine Fehler über
mehrere Anfragen hinweg ansammelt.

## `ezinfo.php`

```php
<?php
class myextensionInfo
{
    static function info()
    {
        return array(
            'Name'      => "My Extension",
            'Version'   => "1.2.3",
            'Copyright' => "Copyright (C) 1998 - 2026 7x & Exponential Foundation",
            'License'   => "GNU General Public License v2.0 (or any later version)",
            'Info_url'  => "https://github.com/se7enxweb/myextension"
        );
    }
}
?>
```

Regeln, denen die Releases von Exponential folgen:

| Regel | Warum |
|---|---|
| Der Klassenname ist der Verzeichnisname plus `Info` | — |
| `Name` ist ein lesbarer Name, nicht der Verzeichnisname | `enhancedezbinaryfile`, `xrowmetadata` und `exp_enhanced_link` deklarierten ihren Verzeichnisnamen und zeigten ihn bis zur Release vom 30. September 2026 auf der Seite „Über“ an |
| Schlüssel sind Strings in Anführungszeichen | Ein Schlüssel ohne Anführungszeichen wie `Name` ist ein Konstantenzugriff; korrigiert in `explayouts_ui` und `explayouts_ui_api` |
| `info()` ist `static` | Die Methode wird statisch aufgerufen |
| `Version` hat kein Präfix `v` | Tag `v6.0.8` ergibt `'Version' => "6.0.8"` |

## `extension.xml`

```xml
<?xml version="1.0" encoding="utf-8" ?>
<software>
    <metadata>
        <name>Exponential Website Interface LS</name>
        <version>6.0.16</version>
        <copyright>Copyright (C) 7x. All rights reserved.</copyright>
        <license>GNU General Public License v2.0 (or any later version)</license>
        <info_url>https://github.com/se7enxweb/ezwebin</info_url>
    </metadata>
    <dependencies>
        <requires>
            <extension name="ezjscore" />
        </requires>
        <extends>
            <extension name="ezwt" />
        </extends>
    </dependencies>
</software>
```

- `<requires>` nennt Extensions, die aktiv sein müssen; `<extends>` nennt Extensions, auf denen diese aufbaut.
- Die flache Form `<extension requires="..."><name>..</name><summary>..</summary><version>..</version>...</extension>`
  (verwendet von `syndication`) wird genauso gelesen, aus der obersten Ebene.

## Release-Regel

1. Wählen Sie die Release-Version.
2. Schreiben Sie sie in **beide** Dateien, in dem Commit, auf den der Release-Tag zeigen wird. Setzen Sie niemals zuerst den
   Tag und erhöhen Sie die Version danach: Ein veröffentlichter Tag lässt sich nicht korrigieren.
3. Ziehen Sie die anderen Versionsfelder des Repositorys mit: `version` in `composer.json`, wo vorhanden, und den
   Versions-Header von `share/filelist.md5`, wo die Extension einen hat.
4. Setzen Sie `license` auf den vollständigen Text `GNU General Public License v2.0 (or any later version)` oder genau auf
   das, was die Lizenzdatei der Extension angibt, und `info_url` auf die Projektseite der Extension.

`//autogentag//` und `dev-master` sind keine Versionen.

## Dateimanifest (`share/filelist.md5`)

Eine Extension, die `share/filelist.md5` mitliefert (zum Beispiel `cjw_newsletter`), führt die Prüfsumme jeder ihrer
Dateien auf. **Setup > System-Upgrade > Dateikonsistenz Überprüfung** listet jede Zeile, die nicht mehr übereinstimmt, als
geänderte Datei auf; frischen Sie das Manifest daher in derselben Release auf wie jede Dateiänderung. Beispiele aus
`cjw_newsletter`:

| Release | Was geschah |
|---|---|
| 4.1.4 | Änderte 21 Dateien, ohne das Manifest aufzufrischen; die Prüfung listete alle auf |
| 4.1.5 | Korrigierte das Manifest |
| 4.1.14 | Im Manifest fehlten 33 Dateien unter `classes/runnable/` |
| 4.1.15 | Das Manifest führt alle 309 versionierten Dateien auf |

## Vor dem Veröffentlichen prüfen

Im Verzeichnis der Extension ausführen:

```bash
php -l ezinfo.php
xmllint --noout extension.xml
```

Erwartet: `No syntax errors detected in ezinfo.php` und keine Ausgabe von `xmllint`. Bestätigen Sie anschließend, dass
beide Dateien die neue Version zeigen.

## Verwandte Seiten

- [Verhaltensänderungen der Extensions: Metadaten, Übersetzungen und Integritätsmanifeste](../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md#10-metadaten-übersetzungen-und-integritätsmanifeste)
- [Extensions](../../funktionen/6.0/erweiterungen/README.md), [Standardmäßig ausgelieferte Extensions](../../funktionen/6.0/standard-extension-auslieferung.md), [Extension-Liste: sortieren, untersuchen und herunterladen](../../funktionen/6.0/extension-liste-und-downloads.md), [Setup > Extensions: Ladereihenfolge und sicheres Speichern](../../funktionen/6.0/ladereihenfolge-der-extensions.md), [Prüfung der Dateikonsistenz](../../funktionen/6.0/dateikonsistenzpruefung.md)
- Monatsseiten: [Januar 2024, erste Hälfte](../../geschichte/2024/2024-01a.md), [Januar 2024, zweite Hälfte](../../geschichte/2024/2024-01b.md), [Februar 2024](../../geschichte/2024/2024-02.md), [März 2024](../../geschichte/2024/2024-03.md), [Juni 2024](../../geschichte/2024/2024-06.md)
