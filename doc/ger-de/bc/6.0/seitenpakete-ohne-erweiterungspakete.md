# Site-Pakete installieren keine Erweiterungspakete mehr

Seit 6.0.15 verlangt kein Site-Paket mehr die Erweiterungspakete `ezwt_extension`, `ezstarrating_extension` oder
`ezgmaplocation_extension`. Composer installiert ezstarrating und ezgmaplocation (`composer.json` im
Wurzelverzeichnis). Die Installer aktivieren sie wie bisher, und ihre Tabellen kommen aus der eigenen
`share/db_schema.dba` jeder Erweiterung. Die Website-Seitenleiste expwt tritt in der `extension_list` jedes Installers
an die Stelle von ezwt.

Das betrifft `sevenx_site`, `sevenx_site_clean`, `ezwebin_site`, `ezwebin_site_clean`, `ezflow_site`,
`ezflow_site_clean`, `ezdemo_site` und `ezdemo_site_clean` (die `<requires>` der `package.xml`) sowie die Installer
dieser Pakete und von `sevenx_multisite`, `sevenx_multisite_clean` und `cjw_multisite` (`extension_list` und die
Tabellen).

## Warum

Ein Erweiterungspaket trug eine Kopie der Dateien der Erweiterung, und die Installation des Site-Pakets schrieb diese
Kopie über die Kopie, die Composer installiert hatte. Seit 6.0.15 behält der Paket-Handler eine Kopie, die Composer
oder git verwaltet (siehe [Ein Site-Paket behält die neuere installierte
Erweiterung](paketinstallation-behaelt-neuere-erweiterungen.md)). Die Pakete kosteten danach nur noch einen Download,
und der Setup-Assistent blieb bei der Auswahl der Site stehen, wenn eines von ihnen fehlte.

## Was sich ändert

| Vorher | Seit 6.0.15 |
|---|---|
| `package.xml` verlangt `ezwt_extension`, `ezstarrating_extension`, `ezgmaplocation_extension` | kein solches `<require>` |
| der Installer aktiviert `ezwt` | der Installer aktiviert `expwt`, die Website-Seitenleiste |
| die Tabellen von ezstarrating und ezgmaplocation aus der Kopie des Pakets (`insertDBFile()`, `declaredExtensionSchemas()` nannte das Paket) | aus `extension/ezstarrating/share/db_schema.dba` und `extension/ezgmaplocation/share/db_schema.dba` (`expInstallDefaultExtensions::ensureExtensionTables()`) |
| fehlende Tabellen einer aktiven Erweiterung in einer bestehenden Installation: von Hand | der Upgrade-Schritt `6.0.15-active-extension-tables` |

Die drei Erweiterungspakete werden aus dem Ordner 6.0.15 des Paketservers genommen, sobald die neu gebauten
Site-Pakete veröffentlicht sind. Die Ordner früherer Versionen (6.0.0 bis 6.0.14) bleiben, wie sie veröffentlicht
wurden.

## Was Sie tun

- **Neue Installation**: nichts. Führen Sie wie immer `composer install` vor dem Setup-Assistenten oder `exp:install`
  aus.
- **Bestehende Installation**: führen Sie `./console exp:upgrade` aus (zuerst als Probelauf). Der Schritt
  `6.0.15-active-extension-tables` legt die Tabellen jeder aktiven Erweiterung mit einer `share/db_schema.dba` an, der
  sie fehlen. Wo die Tabellen da sind, legt er nichts an. ezwt zieht mit `./console exp:wt:migrate-from-ezwt --apply`
  zu expwt um (Schritt `6.0.15-expwt-enable`).
- **Ein eigenes Site-Paket**, das eines der drei Pakete verlangt: entfernen Sie das `<require>` und lassen Sie den
  Namen in der `extension_list`. Wenn der Installer die Tabellen aus dem Paket anlegte
  (`$package->path() . '/ezextension/...'`), rufen Sie stattdessen
  `expInstallDefaultExtensions::ensureExtensionTables( '<Erweiterung>', eZExtension::baseDirectory() )` auf.

## Verwandte Seiten

- [Erweiterungen einer neuen Installation](../../funktionen/6.0/erweiterungen-einer-neuen-installation.md), die Liste
  für 6.0.15
- [Die Website-Seitenleiste](../../funktionen/6.0/website-seitenleiste.md)
- [Aktualisieren mit der Konsole](../../anleitungen/aktualisieren-mit-der-konsole.md)
