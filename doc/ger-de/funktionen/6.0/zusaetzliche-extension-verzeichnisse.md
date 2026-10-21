# Extensions in mehr als einem Ordner

Diese Seite ist für Entwickler und Administratoren, die ihre eigenen Projekt-Extensions von den Extensions trennen
möchten, die sie installieren. Bis 2026 musste jede Extension in `extension/` liegen. In diesem Ordner landen auch die
Extensions der Community, sodass Ihr Projektcode und der Code, den Sie nur verwenden, nebeneinander lagen und ein
sauberer Checkout einer Hersteller-Extension Ihre Änderungen überschreiben konnte. Seit dem 26. Juli 2026 (Commit
`17e7c11e71`) können Sie zusätzliche Extension-Ordner deklarieren. Eine typische Aufteilung:

```
extension/        extensions you install (community, vendor)
extension_src/    extensions you write for this project or this customer
```

Eine Extension zwischen den Ordnern zu verschieben ist eine einfache Kopie: kein Manifest, kein `composer dump-autoload`.

## Einrichten

1. Deklarieren Sie den zusätzlichen Ordner in `settings/override/site.ini.append.php`:

   ```ini
   [ExtensionSettings]
   AdditionalExtensionDirectories[]
   AdditionalExtensionDirectories[]=extension_src
   ActiveExtensions[]=acme_customer_extension
   ```

2. Legen Sie den Ordner an, verschieben oder kopieren Sie eine Extension hinein und erneuern Sie das Autoload-Array:

   ```bash
   mkdir extension_src
   cp -r extension/acme_customer_extension extension_src/acme_customer_extension
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

3. Öffnen Sie **Setup > Extensions**: Die Extension ist aufgeführt und aktiv, gleich, welche Wurzel sie enthält.

Extensions werden weiterhin mit `ActiveExtensions[]` und `ActiveAccessExtensions[]` eingeschaltet; der Kernel findet
jede in der Wurzel, die sie enthält.

| Datei | Block | Schlüssel | Standard | Geltungsbereich | Bedeutung |
|---|---|---|---|---|---|
| `settings/site.ini` | `ExtensionSettings` | `ExtensionDirectory` | `extension` | global | Die klassische Wurzel; immer aktiv |
| `settings/site.ini` | `ExtensionSettings` | `AdditionalExtensionDirectories[]` | leere Liste | global | Weitere Wurzeln, durchsucht in der geschriebenen Reihenfolge. Eine spätere Wurzel hat höhere Priorität. |

## Wenn zwei Wurzeln dieselbe Extension enthalten

Die später aufgeführte Wurzel gewinnt für diese Extension vollständig: Es gibt kein Zusammenführen Datei für Datei. Der
Autoload-Generator gibt für jede solche Kollision eine Warnung aus, sodass eine verdeckende Kopie nie eine Überraschung
ist. Damit lässt sich eine Hersteller-Extension in `extension/` sicher aktualisieren, während eine geänderte Kopie in
`extension_src/` aktiv bleibt.

## Namen und Groß-/Kleinschreibung

Der Name der Extension in `ActiveExtensions[]`, `module.ini [ModuleSettings] ExtensionRepositories[]` und
`design.ini [ExtensionSettings] DesignExtensions[]` darf sich in der Groß- und Kleinschreibung vom Ordner unterscheiden
(`adminaid` für `AdminAid`). `eZExtension::extensionName( $name )` gibt den echten Ordnernamen zurück, sodass die
Einstellung auf Dateisystemen funktioniert, die Groß- und Kleinschreibung unterscheiden (19. Juli, `c99f395fb5`). Auf
Systemen ohne diese Unterscheidung (macOS, Windows) wird ebenfalls die echte Schreibweise zurückgegeben, und Tests der
Schreibweise werden übersprungen (15. August, `c52c58c533`).

## Für Entwickler

Bauen Sie nie `'extension/' . $name`. Verwenden Sie:

| Aufruf | Gibt zurück |
|---|---|
| `eZExtension::extensionRootDirectories()` | Die zusammengeführte Liste der Wurzeln, niedrigste Priorität zuerst |
| `eZExtension::extensionPath( $name )` | Den Pfad einer Extension oder `false` |
| `eZExtension::expandedPathList( $names, $subdirectory )` | Pfade, zum Beispiel jedes Ordners `design` |
| `eZExtension::extensionName( $name )` | Den Ordnernamen in der echten Schreibweise |
| `eZExtension::filterExtensionRootDirectories( $roots )` | Den Hook, um Wurzeln zur Laufzeit zu berechnen |

Etwa 40 Dateien, die früher `extension/` fest eincodierten, verwenden jetzt diese Aufrufe: Suche nach Designs und
Templates, Module, Bearbeiten von Einstellungen, der Autoload-Generator, Datentypen, Workflow-Typen,
Benachrichtigungs-Handler, Shop-Manager, Übersetzungen und die Kommandozeilenwerkzeuge
(`bin/php/ezpgenerateautoloads.php`, `eztc.php`, `ezimportdbafile.php`, `ezwebincommon.php`, `runcronjobs.php`).

Die vollständige Referenz, mit der Ladereihenfolge der Einstellungen und dem Muster der Website-Extension, steht in
[Zusätzliche Extension-Verzeichnisse](../../bc/6.0/zusaetzliche-erweiterungsverzeichnisse.md).

## Grenzen

- Wurzeln sind relativ zum Installationsverzeichnis, sofern Sie keinen absoluten Pfad schreiben.
- Führen Sie `php bin/php/ezpgenerateautoloads.php -e` aus, nachdem Sie eine Extension zwischen Wurzeln verschoben
  haben.

## Verwandte Seiten

- [Zusätzliche Extension-Verzeichnisse (Referenz)](../../bc/6.0/zusaetzliche-erweiterungsverzeichnisse.md)
- [Extension-Liste und Downloads](extension-liste-und-downloads.md) (listet Extensions aus jeder Wurzel auf und lädt sie herunter), [Ladereihenfolge der Extensions](ladereihenfolge-der-extensions.md), [Modulüberschreibung durch Extensions](modulueberschreibung-durch-extensions.md)
- [Website-spezifische Einstellungen in Extensions](ini-ueberschreibungen-fuer-mehrere-websites.md), [Verzeichnisse und Platzierungen von INI-Überschreibungen](../../spezifikationen/6.0/ini-override-platzierungen.md)
- [Icon-Themes in Extensions](icon-themes-in-extensions.md), [Extension-Metadaten](../../spezifikationen/6.0/extension-metadaten.md)
- [Verhaltensänderungen der Legacy-Extensions (September und Oktober 2026)](../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chronik: [Juli 2026](../../geschichte/2026/2026-07.md), [August 2026](../../geschichte/2026/2026-08.md), [Januar 2026](../../geschichte/2026/2026-01.md), [Oktober 2024](../../geschichte/2024/2024-10.md), [September 2024](../../geschichte/2024/2024-09.md), [Januar 2024, zweite Hälfte](../../geschichte/2024/2024-01b.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
