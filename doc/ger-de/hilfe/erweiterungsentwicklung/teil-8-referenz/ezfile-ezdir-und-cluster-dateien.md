---
title: eZFile, eZDir und Cluster-Datei-Handler
audience: developer
category: extension-development
order: 150
summary: Dateien so lesen und schreiben, wie Exponential es erwartet - eZFile::create() und rename(), eZDir::mkdir() und path() sowie eZClusterFileHandler für alles unter var/, damit die Erweiterung auch auf geclusterten Installationen funktioniert.
keywords: Dateien, Verzeichnisse, Cluster, Cluster-Datei-Handler, Dateiberechtigungen, eZFile, create, rename, eZDir, mkdir, path, recursiveDelete, findSubitems, eZClusterFileHandler, instance, storeContents, fetchContents, exists, delete, processCache, cluster
type: help
---
# eZFile, eZDir und Cluster-Datei-Handler

Teil 8, Referenz 15. Eine Erweiterung schreibt Dateien: Caches, Exporte, erzeugte Bilder. Auf einem einzelnen Server
funktioniert jede Dateifunktion. In einem Cluster (mehrere Webserver, die sich den Speicher über eine Datenbank oder
ein gemeinsames Dateisystem teilen) müssen Dateien unter `var/` über den Cluster-Datei-Handler gehen, sonst schreibt
ein Server eine Datei, die die anderen nie sehen. Diese Seite zeigt die drei Hilfsklassen und wann Sie welche
verwenden.

## eZFile (lib/ezfile/classes/ezfile.php)

| Methode (statisch) | Verwendung |
|---|---|
| `create( $filename, $directory = false, $data = false, $atomic = false )` | eine Datei schreiben und dabei das Verzeichnis anlegen; `$atomic` schreibt in eine temporäre Datei und benennt sie um |
| `rename( $srcFile, $destFile, $mkdir = false, $flags = 0 )` | eine Datei verschieben |
| `suffix( $filename )` | die Dateiendung |
| `download( $file, $isAttachedDownload = true, ... )` | eine Datei an den Browser senden |

## eZDir (lib/ezfile/classes/ezdir.php)

| Methode (statisch) | Verwendung |
|---|---|
| `mkdir( $dir, $perm = false, $recursive = false )` | ein Verzeichnis mit den Berechtigungen der Installation anlegen |
| `path( $names, $includeEndSeparator = false, ... )` | Pfadteile zusammenfügen |
| `cleanPath( $path, ... )` | einen Pfad normalisieren |
| `findSubitems( $dir, $types = false, $fullPath = false, ... )` | ein Verzeichnis auflisten |
| `recursiveFind( $dir, $suffix )` | Dateien nach Endung finden |
| `recursiveDelete( $dir, $rootCheck = true )` | einen Verzeichnisbaum löschen |
| `isWriteable( $dirname )` | ob ein Verzeichnis beschreibbar ist |

`mkdir()` verwendet die Berechtigungen, für die die Installation konfiguriert ist, sodass Dateien für den Webserver
und den Kommandozeilenbenutzer gleichermaßen lesbar sind ([Dateimodi](../../../../eng-us/bc/6.0/file-modes.md)).

## eZClusterFileHandler (kernel/classes/ezclusterfilehandler.php)

Verwenden Sie ihn für jede Datei unter dem var-Verzeichnis der Website, die mehr als ein Request liest:

```php
<?php
$path = eZSys::cacheDirectory() . '/myext/summary-' . (int)$nodeID . '.html';
$file = eZClusterFileHandler::instance( $path );
if ( $file->exists() )
{
    $html = $file->fetchContents();
}
else
{
    $html = '...';  // build it
    $file->storeContents( $html, 'myext', 'text/html' );
}
```

| Methode | Verwendung |
|---|---|
| `eZClusterFileHandler::instance( $filename = false )` (statisch) | der Handler für eine Datei; die Klasse hängt von der Installation ab (`eZFSFileHandler` auf einem einzelnen Server) |
| `exists()` | ob die Datei existiert |
| `storeContents( $contents, $scope = false, $datatype = false, $storeLocally = false )` | sie schreiben; `$scope` nennt Ihre Erweiterung, `$datatype` den MIME-Typ |
| `fetchContents()` | sie lesen |
| `delete()` | sie entfernen |
| `processCache( $retrieveCallback, $generateCallback = null, $ttl = null, $expiry = null, $extraData = null )` | lesen oder erzeugen, mit Sperre, sodass nur ein Request eine abgelaufene Datei neu erzeugt |

Das obige Beispiel wurde lesend und schreibend in einem Arbeitsordner einer Testinstallation ausgeführt: `exists()`,
`fetchContents()` und `delete()` verhielten sich wie gezeigt, mit `eZFSFileHandler` als Handler.

## Was Sie verwenden

| Datei | Verwendung |
|---|---|
| unter `var/` (Caches, Speicher, erzeugte Dateien) | `eZClusterFileHandler` |
| eine temporäre Datei, die innerhalb eines Requests verwendet wird | `eZFile` / `tempnam()` im temporären Verzeichnis des Systems |
| Dateien der Erweiterung selbst | zur Laufzeit nie in sie schreiben |

Pfade kommen von `eZSys` (`cacheDirectory()`, `storageDirectory()`) und werden nie in den Code geschrieben
([eZSys und eZExtension](ezsys-und-ezextension.md)).

## Verwandte Seiten

- Zurück: [eZScript und eZCLI](ezscript-und-ezcli.md)
- Weiter: [Die eZDataType-API](ezdatatype-api.md)
- [Verzeichnisse unter var/](verzeichnisse-unter-var.md)
- [Leistung in Erweiterungen](../teil-3-erweiterungsentwickler/leistung-in-erweiterungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
