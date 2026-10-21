---
title: Verzeichnisse unter var/
audience: developer
category: extension-development
order: 320
summary: Was unter var/ liegt - Cache, Speicher und Log der Website, var/log, var/autoload -, was gelöscht werden darf, was gesichert werden muss und wo eine Erweiterung ihre eigenen Dateien ablegt.
keywords: var, var-Verzeichnis, Cache, Speicher, Log, Sicherung, Berechtigungen, Erweiterungsdateien, var directory, cache, storage, log, autoload, varDirectory, cacheDirectory, storageDirectory, backup, permissions, extension files
type: help
---
# Verzeichnisse unter var/

Teil 8, Referenz 28. Alles, was eine Installation zur Laufzeit schreibt, liegt unter `var/`. Wer weiß, was wo liegt,
weiß auch, was das Leeren eines Caches entfernt, was eine Sicherung enthalten muss und wohin Ihre Erweiterung
schreiben darf.

## Der Aufbau

| Pfad | Enthält | Löschen? | Sichern? |
|---|---|---|---|
| `var/<site>/` | das var-Verzeichnis der Website (`site.ini [FileSettings] VarDir`, standardmäßig `var`, meist auf `var/<site>` gesetzt); `eZSys::varDirectory()` | | |
| `var/<site>/cache/` | kompilierte Templates, View-Caches, Einstellungs-Caches, gepacktes CSS und JS, alles, was das Leeren eines Caches entfernt; `eZSys::cacheDirectory()` | ja, über die Cache-Befehle | nein |
| `var/<site>/storage/` | hochgeladene Dateien und Bilder von Inhalten; `eZSys::storageDirectory()` | nie | ja |
| `var/<site>/log/` | die Logdateien der Website | rotieren, nicht blind löschen | wie es die Richtlinien verlangen |
| `var/log/` | Logs, die geschrieben werden, bevor eine Website bekannt ist, und das Standardziel von `eZLog::write()` | rotieren | wie es die Richtlinien verlangen |
| `var/autoload/` | erzeugte Autoload-Arrays von Erweiterungen und Overrides (`ezp_extension.php`, `ezp_override.php`); das des Kernels ist `autoload/ezp_kernel.php` im Wurzelverzeichnis | wird vom Autoload-Befehl neu erzeugt | nein |

`<site>` ist ein Name, den die Installation wählt (oft `site`). Manche Installationen legen eigene Ordner an; die
Kernel-eigenen Erweiterungen können Caches und Auftragsdaten in Unterordnern des var-Verzeichnisses der Website
ablegen.

## Regeln für eine Erweiterung

- **Fragen Sie nach dem Pfad**: `eZSys::varDirectory()`, `eZSys::cacheDirectory()`, `eZSys::storageDirectory()`;
  schreiben Sie nie `var/site` in den Code ([eZSys und eZExtension](ezsys-und-ezextension.md)).
- **Caches in `cacheDirectory()`/myext/**: alles, was Sie neu aufbauen können. Das Leeren eines Caches kann es
  jederzeit löschen.
- **Daten in der Datenbank oder im Speicher**: alles, was Sie nicht neu aufbauen können. Bewahren Sie die einzige
  Kopie von Daten nie im Cache auf.
- **Über den Cluster-Datei-Handler** bei Dateien, die mehr als ein Request liest, damit die Erweiterung auch in
  Clustern funktioniert ([eZFile, eZDir und Cluster-Datei-Handler](ezfile-ezdir-und-cluster-dateien.md)).
- **Mit den Berechtigungen der Installation**: `eZDir::mkdir()` und `eZFile::create()` verwenden die konfigurierten
  Dateimodi, sodass Webserver und Kommandozeilenskripte jeweils lesen können, was der andere geschrieben hat
  ([Dateimodi](../../../bc/6.0/dateimodi.md)).
- **Nichts Geheimes in var/, wenn es über das Web lesbar ist.** Die Webserver-Konfiguration darf `var/` nicht
  ausliefern, außer dem Speicher und den gepackten öffentlichen Dateien; verlassen Sie sich nicht auf unauffällige
  Dateinamen.

## Von Hand leeren

Löschen Sie innerhalb von `var/` nie mit `rm`, um „die Caches zu leeren“: Cluster-Installationen führen je Datei einen
Datenbankeintrag, und der nächste Request findet womöglich einen Eintrag ohne Datei. Verwenden Sie
`php bin/php/ezcache.php` oder `./console exp:cache`
([Cache-Tags, Cache-Blöcke und das Leeren von Caches](cache-tags-und-leeren.md)).

## Verwandte Seiten

- Zurück: [Konsolenbefehle für Entwickler](konsolenbefehle-fuer-entwickler.md)
- Weiter: [Fehlercodes und Modulergebnisse](fehlercodes-und-modulergebnisse.md)
- [Debug-Ausgabe und Logs für Entwickler](../teil-1-vom-benutzer-zum-entwickler/debug-ausgabe-und-logs-fuer-entwickler.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
