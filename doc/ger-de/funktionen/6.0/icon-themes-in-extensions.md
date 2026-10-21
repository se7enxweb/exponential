# Icon-Themes, die in Ihrer Extension liegen

Diese Seite ist für Entwickler und Designer, die eigene Klassen-Icons oder Dateityp-Icons in der Administrationsoberfläche
möchten. Exponential nimmt diese kleinen Icons aus einem *Icon-Theme*, einem Ordner mit Bildern. Vor Juli 2026 war der
einzige Ort für ein Theme `share/icons/`, ein eigenes Icon bedeutete also, Dateien in den Kern zu kopieren oder eine
Kernel-Klasse zu überschreiben. Seit dem 20. Juli 2026 (Commit `3a40db1988`) kann eine Extension ihr eigenes Icon-Theme
mitbringen: Die Funktionen der früheren Extensions `bciconextensions` und `bciconextensions_share_icons` sind Teil des
Kernels.

**Aktualisieren Sie?** Wenn Sie diese beiden Extensions verwendet haben, entfernen Sie sie aus `ActiveExtensions[]`.
Weder eine Überschreibung einer Kernel-Klasse noch eine Einrichtung von `EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE` sind noch
nötig.

## Ein Theme in eine Extension legen

1. Legen Sie die Ordner an. Ein Ordner pro Größe, darin pro Gruppe:

   ```
   extension/mycompany_icons/
     icons/
       crystal-admin/              the theme name
         32x32/apps/job.png
         16x16_indexed/apps/job.png
         icon.ini                  sizes and the class to icon map of this theme
     settings/icon.ini.append.php
   ```

2. Schreiben Sie die eigene `icons/crystal-admin/icon.ini` des Themes:

   ```ini
   #?ini charset="utf-8"?
   [IconSettings]
   Sizes[]
   Sizes[normal]=32x32
   Sizes[small]=16x16_indexed

   [ClassIcons]
   Default=mimetypes/empty.png
   ClassMap[job]=apps/job.png
   ```

3. Nennen Sie die Extension und das Theme in der `settings/icon.ini.append.php` der Extension:

   ```ini
   <?php /* #?ini charset="utf-8"?

   [ExtensionSettings]
   IconExtensions[]=mycompany_icons

   [IconSettings]
   Theme=crystal-admin
   StandardTheme=crystal
   AdditionalThemeList[]=crystal-admin

   */ ?>
   ```

4. Aktivieren Sie die Extension (`ActiveExtensions[]=mycompany_icons` in `settings/override/site.ini.append.php`),
   dann:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

5. Öffnen Sie in der Administrationsoberfläche einen Ordner mit Elementen vom Typ `job`: Die Liste zeigt Ihr
   `apps/job.png`.

Die in `IconExtensions[]` aufgeführten Extensions werden vor `share/icons/` durchsucht, in der aufgeführten
Reihenfolge.

## Wie ein Icon gefunden wird

1. Das aktuelle Theme (`Theme`), in den Extension-Ordnern, dann in `Repository` (Standard `share/icons`).
2. Jedes Theme in `AdditionalThemeList[]`, in derselben Reihenfolge der Orte.
3. `StandardTheme`.
4. Das Icon `Default` des Themes oder der Überschreibung, sodass eine fehlende Datei nie ein kaputtes Bild-Tag erzeugt.

Icons in Extensions sind einfache statische Dateien: Der Webserver liefert sie ohne zusätzliche PHP-Arbeit pro Bild aus.

Das Theme `crystal-admin` bringt jetzt ein Icon für den Bezeichner der Inhaltsklasse `job` mit.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich | Bedeutung |
|---|---|---|---|---|---|
| `settings/icon.ini` | `IconSettings` | `Repository` | `share/icons` | Installation | Standard-Icon-Repository, relativ zum Document Root |
| `settings/icon.ini` | `IconSettings` | `Theme` | `crystal` | Installation | Zuerst versuchtes Theme |
| `settings/icon.ini` | `IconSettings` | `StandardTheme` | `crystal` | Installation | Letztes Rückfall-Theme |
| `settings/icon.ini` | `IconSettings` | `AdditionalThemeList[]` | leer | Installation | Themes, die vor `StandardTheme` durchsucht werden |
| `settings/icon.ini` | `ExtensionSettings` | `IconExtensions[]` | leer | Installation | Extensions, deren Ordner `icons/` durchsucht wird; die Reihenfolge zählt |

Die vollständige Anleitung mit der Klasse und den Hinweisen zur Aktualisierung ist [Icon-Unterstützung](../../bc/6.0/symbole.md).

## Verwandte Seiten

- [Icon-Unterstützung (Anleitung)](../../bc/6.0/symbole.md)
- [Zusätzliche Extension-Verzeichnisse](zusaetzliche-extension-verzeichnisse.md), [das Design admin4](admin4-design.md)
- [Chronik: Juli 2026](../../geschichte/2026/2026-07.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
