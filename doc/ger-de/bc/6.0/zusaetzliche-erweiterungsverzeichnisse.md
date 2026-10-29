# Zusätzliche Erweiterungsverzeichnisse

Lesen Sie diese Seite, wenn Sie Ihre eigenen Erweiterungen von denen Dritter getrennt halten möchten, zum Beispiel in
`extension_src/` neben `extension/`. Exponential 6.0 kann Erweiterungen aus mehr als einem Verzeichnis laden. Nichts
ändert sich, bis Sie es einschalten: Eine bestehende Installation verhält sich genau wie bisher.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neue Einstellung `site.ini [ExtensionSettings] AdditionalExtensionDirectories[]` (standardmäßig leer). Jeder Eintrag ist ein weiteres Wurzelverzeichnis für Erweiterungen. Einstellungen, Designs, Module, Übersetzungen und Autoloads werden in jedem Wurzelverzeichnis gefunden. |
| Wer betroffen ist | Niemand, solange die Einstellung nicht verwendet wird. Code, der Erweiterungspfade von Hand als `extension/<name>` baut, findet eine Erweiterung in einem anderen Wurzelverzeichnis nicht; verwenden Sie `eZExtension::extensionPath()`. |
| Wie Sie es prüfen | `grep -n "AdditionalExtensionDirectories" settings/site.ini` |
| Wie Sie es beheben | Nichts zu beheben. Um es zu nutzen, folgen Sie [Eine Erweiterung in ein zweites Wurzelverzeichnis verschieben](#eine-erweiterung-in-ein-zweites-wurzelverzeichnis-verschieben). |

Ein Paket in einem beliebigen Wurzelverzeichnis ist eine gewöhnliche Legacy-Erweiterung. Der Prüfstein des Entwurfs
ist, dass das hier ohne jede Änderung an einer Datei, einem Manifest oder `composer dump-autoload` funktioniert:

```bash
cp -r extension/acme_customer_extension extension_src/acme_customer_extension
```

## Eine Erweiterung in ein zweites Wurzelverzeichnis verschieben

Die empfohlene Struktur hält die beiden Wurzelverzeichnisse nebeneinander:

```
ezroot/
    extension/          # third-party and community extensions
    extension_src/      # project and customer extensions
```

1. Legen Sie das neue Wurzelverzeichnis an:

   ```bash
   mkdir extension_src
   ```

2. Fügen Sie es in `settings/override/site.ini.append.php` hinzu:

   ```ini
   [ExtensionSettings]
   AdditionalExtensionDirectories[]
   AdditionalExtensionDirectories[]=extension_src
   ```

3. Kopieren Sie die Erweiterung, die Sie ändern möchten:

   ```bash
   cp -r extension/acme_customer_extension extension_src/acme_customer_extension
   ```

4. Nehmen Sie Ihre Änderungen in `extension_src/` vor.
5. Erzeugen Sie die Legacy-Autoloads neu (oder führen Sie `composer run legacy-scripts` aus):

   ```bash
   php bin/php/ezpgenerateautoloads.php
   ```

   Um zu sehen, welche Wurzelverzeichnisse der Generator durchsucht, ohne etwas zu schreiben, verwenden Sie den
   Probelauf: `php bin/php/ezpgenerateautoloads.php -e -n`.

`composer dump-autoload` ist nicht nötig. Sie können `extension/` und `extension_src/` so lange nebeneinander
betreiben, wie Sie möchten.

Die Aktivierung ändert sich nicht: Führen Sie die Erweiterung wie bisher in `ActiveExtensions[]` oder
`ActiveAccessExtensions[]` auf. Der Kernel sucht den Namen in den konfigurierten Wurzelverzeichnissen.

```ini
[ExtensionSettings]
ActiveExtensions[]
ActiveExtensions[]=site_app
ActiveExtensions[]=acme_customer_extension
```

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `site.ini` | `[ExtensionSettings]` | `ExtensionDirectory` | `extension` (immer aktiv) | global |
| `site.ini` | `[ExtensionSettings]` | `AdditionalExtensionDirectories[]` | leere Liste; in der geschriebenen Reihenfolge durchsucht, ein späterer Eintrag hat höhere Priorität | global |

Die ausgelieferte `settings/site.ini` enthält die Einstellung mit auskommentiertem Beispiel:

```ini
[ExtensionSettings]
ExtensionDirectory=extension
AdditionalExtensionDirectories[]
#AdditionalExtensionDirectories[]=extension_src
```

Da es ein gewöhnliches INI-Array ist, können Sie so viele Wurzelverzeichnisse hinzufügen, wie Sie brauchen; andere
Erweiterungen und `settings/override/` können Wurzelverzeichnisse anhängen, ohne den Kernel anzufassen, und PHP-Code
kann die Liste über `eZExtension::filterExtensionRootDirectories()` ändern.

### Beispielstrukturen

```ini
# Sibling root (recommended)
[ExtensionSettings]
AdditionalExtensionDirectories[]=extension_src
```

```ini
# An "extensions/" directory, for projects that already use that name
[ExtensionSettings]
AdditionalExtensionDirectories[]=extensions
```

```ini
# A top-level src/ directory, as in the original proposal
[ExtensionSettings]
AdditionalExtensionDirectories[]=src
```

```ini
# One root per customer or project
[ExtensionSettings]
AdditionalExtensionDirectories[]=sites/customer_a
AdditionalExtensionDirectories[]=sites/customer_b
```

## Welche Kopie gewinnt

Gibt es denselben Erweiterungsnamen in zwei Wurzelverzeichnissen, ersetzt das spätere Wurzelverzeichnis das frühere
vollständig. Dateien werden nicht pro Unterverzeichnis zusammengeführt.

```ini
[ExtensionSettings]
ExtensionDirectory=extension
AdditionalExtensionDirectories[]
AdditionalExtensionDirectories[]=extension_src
```

Mit `extension/foobar/` und `extension_src/foobar/` verwendet der Kernel nur `extension_src/foobar/`. Der
Autoload-Generator wendet dieselbe Regel an und behält die spätere Kopie ohne Warnung; prüfen Sie daher selbst auf
doppelte Namen, wenn Sie eine Erweiterung kopieren. Das macht ein `composer update` einer Vendor-Erweiterung sicher,
während Ihre lokale Kopie in `extension_src/` aktiv ist.

## Aufbau einer Erweiterung

Ein Paket in einem beliebigen Wurzelverzeichnis hat denselben Aufbau wie eine klassische Erweiterung. Es gibt kein
neues Manifest, keine Pflicht zu PSR-4 und keine Änderung an der Benennung von Klassen.

```
extension_src/acme_customer_extension/
├── extension.xml
├── settings/
│   └── site.ini.append.php
├── classes/
├── modules/
├── design/
├── autoloads/
└── ...
```

### Eine ganze Website in einem Paket

Ein Paket kann eine vollständige Website enthalten: Design, gruppierte und ungruppierte Siteaccesses, Code und
Templates. Mehrere Websites können nebeneinander liegen und unabhängig voneinander aktiviert werden.

```
extension_src/site_app/
├── extension.xml
├── settings/
│   ├── override__app_user/
│   │   └── site.ini.append.php
│   ├── siteaccess/
│   │   ├── app_user__de/
│   │   ├── app_user__en/
│   │   └── app_admin/
│   └── siteaccess.ini.append.php
├── design/
│   ├── app_user/
│   └── app_admin/
├── classes/
├── modules/
└── autoloads/
```

## Einstellungen, Designs und Overrides

Die übliche Auflösung gilt für jedes Wurzelverzeichnis:

- `settings/*.ini.append.php` jedes aktiven Pakets wird geladen.
- `design/<siteaccess>/...` wird in jedem aktiven Paket gefunden.
- `settings/override/` innerhalb eines Pakets folgt den üblichen Regeln für Erweiterungen, Siteaccesses und
  Overrides, einschließlich der Gruppierung mit `__`.
- Das globale `settings/override/` bleibt über allem.

Ladereihenfolge, niedrigste Priorität zuerst:

```
base package settings/
extension/package settings/siteaccess/<sa>/
extension/package settings/override__<group>/
extension/package settings/override/
global settings/override/
```

Der einzige Unterschied ist, dass ein Paket in jedem konfigurierten Wurzelverzeichnis liegen kann.

## Autoloads

`bin/php/ezpgenerateautoloads.php` und `eZAutoloadGenerator` durchlaufen jedes konfigurierte Wurzelverzeichnis genauso
wie `extension/*`:

- `var/autoload/ezp_extension.php` enthält die Klassen aller Wurzelverzeichnisse, ohne die `tests/` eines Pakets und
  ohne die `tests/` und `Tests/` eines darin mitgelieferten Composer-Pakets (`vendor/<vendor>/<package>/tests`).
- `var/autoload/ezp_override.php` enthält die Kernel-Overrides aller Wurzelverzeichnisse, ebenfalls ohne diese
  Verzeichnisse.
- `var/autoload/ezp_tests.php` enthält die Testklassen aller Wurzelverzeichnisse. Sie wird nur dort geladen, wo Tests
  laufen: vom PHPUnit-Bootstrap des Kernels, von `ezexec.php` für ein Skript in einem Verzeichnis `tests/` und überall
  dort, wo `ezpAutoloader::enableTests()` aufgerufen wird (`EZP_AUTOLOAD_TESTS` in `config.php` lädt sie überall).
  `ezpAutoloader::enableTests( 'extension/foo/tests' )` liest außerdem sofort die Klassen dieses Verzeichnisses, sodass
  die eigene Suite einer Erweiterung oder ihre Skripttests ihre Testfälle, Fixtures und Mock-Server ohne jedes Array
  finden.
- Das Hinzufügen, Verschieben oder Entfernen eines Pakets braucht kein `composer dump-autoload`;
  `vendor/autoload.php` wird nicht angefasst.
- Klassenbasierte Overrides (`[ClassSettings]`, Workflow-Handler, Datentypen, Operatoren) funktionieren weiter, weil
  `autoloads/*.php` aus jedem Wurzelverzeichnis gesammelt wird.

Der Generator lädt `eZExtension` über `require_once 'autoload.php';`.

## PHP-API

Alle Methoden sind statische Methoden von `eZExtension` (`lib/ezutils/classes/ezextension.php`).

| Methode | Rückgabe |
|---|---|
| `extensionRootDirectories()` | Die Wurzelverzeichnisse in der Reihenfolge der Priorität, niedrig bis hoch: `ExtensionDirectory`, dann `AdditionalExtensionDirectories[]`, ohne leere Einträge und Duplikate, durch `filterExtensionRootDirectories()` geleitet. Pfade so, wie sie in der Einstellung stehen. |
| `extensionPath( $name )` | Der Pfad der Erweiterung im gewinnenden Wurzelverzeichnis oder `false`. Der Name wird ohne Beachtung der Groß- und Kleinschreibung verglichen; das Ergebnis behält die Schreibweise auf der Festplatte. |
| `expandedPathList( $extensions, $subdirectory = false )` | Die Pfade mehrerer Erweiterungen, jeweils mit angehängtem `$subdirectory`. Nicht gefundene Erweiterungen werden übersprungen. |
| `extensionName( $name )` | Der Verzeichnisname, wie er auf der Festplatte existiert (Schreibweise korrigiert). |
| `filterExtensionRootDirectories( $roots )` | Hook für Code, der `eZExtension` erweitert. Standardmäßig wird die Liste unverändert zurückgegeben. |

```php
echo eZExtension::extensionPath( 'ezfind' );
// extension/ezfind

echo eZExtension::extensionPath( 'acme_customer_extension' );
// extension_src/acme_customer_extension

$paths = eZExtension::expandedPathList(
    array( 'ezfind', 'acme_customer_extension' ),
    'design'
);
// array(
//     'extension/ezfind/design',
//     'extension_src/acme_customer_extension/design',
// )
```

Nachschlagen von Pfaden und Namen wird für die aktuelle Anfrage im Speicher gecacht.
`eZExtension::clearActiveExtensionsMemoryCache()` leert die Liste der aktiven Erweiterungen im Speicher.

## Kernel-Code, der die neuen Hilfsmethoden verwendet

Jede Aufrufstelle im Kernel, die früher `extension/` fest eingetragen oder einen Pfad aus
`eZExtension::baseDirectory()` gebaut hat, verwendet jetzt die obigen Hilfsmethoden.

| Bereich | Datei(en) | Mechanismus |
|------|---------|-----------|
| Metadaten von Erweiterungen | `lib/ezutils/classes/ezextension.php`, `kernel/private/classes/ezpextension.php` | `extensionRootDirectories()`, `extensionPath()` |
| Erzeugen der Autoloads | `kernel/private/classes/ezautoloadgenerator.php`, `bin/php/ezpgenerateautoloads.php` | `getExtensionRoots()`, `collectExtensionPackages()` |
| Design / Templates | `kernel/common/eztemplatedesignresource.php`, `kernel/visual/templatecreate.php`, `bin/php/eztc.php` | `extensionPath()`, `expandedPathList()` |
| Module | `lib/ezutils/classes/ezmodule.php` | `extensionPath()` |
| Einstellungen / Siteaccess | `kernel/settings/edit.php`, `kernel/classes/ezsiteaccess.php`, `runcronjobs.php`, `bin/php/ezwebincommon.php` | `extensionPath()` |
| Datentypen | `kernel/classes/ezdatatype.php`, `bin/php/ezimportdbafile.php` | `extensionPath()` |
| Inhalt / Upload / Baum / Bearbeiten | `kernel/classes/ezcontentupload.php`, `kernel/classes/ezcontentobjecttreenode.php`, `kernel/classes/ezcontentobjectedithandler.php` | `extensionPath()` |
| Workflow / Benachrichtigung | `kernel/classes/ezworkflowtype.php`, `kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentgatewaytype.php`, `kernel/classes/notification/eznotificationeventtype.php`, `kernel/classes/notification/eznotificationeventfilter.php` | `extensionPath()` |
| Shop-Handler | `kernel/classes/ezvatmanager.php`, `kernel/classes/ezshippingmanager.php`, `kernel/shop/classes/exchangeratehandlers/ezexchangeratesupdatehandler.php` | `extensionPath()` |
| i18n | `lib/ezi18n/classes/eztstranslator.php` | `expandedPathList()` |
| RSS | `kernel/classes/ezrssimport.php` | `extensionPath()` |
| Icons | `kernel/common/ezwordtoimageoperator.php` | `expandedPathList()` |
| SOAP | `index_soap.php` (`soap.php` leitet nur dorthin weiter) | `extensionPath()` |
| Paketverwaltung | `kernel/classes/packagehandlers/ezextension/ezextensionpackagehandler.php`, `kernel/classes/packagecreators/ezextension/ezextensionpackagecreator.php`, `kernel/classes/packagehandlers/ezinstallscript/ezinstallscriptpackagehandler.php`, `kernel/classes/packagehandlers/ezfile/ezfilepackagehandler.php` | `extensionRootDirectories()`, `extensionPath()`, `baseDirectory()` |
| Setup / Upgrade | `kernel/setup/extensions.php`, `kernel/setup/systemupgrade.php` | `extensionRootDirectories()`, `extensionPath()` |
| Test-Toolkit | `tests/toolkit/ezptestrunner.php`, `tests/toolkit/ezpextensionhelper.php` | `extensionRootDirectories()`, `extensionPath()` |

## Sicherheit

Der Kernel übernimmt die Wurzelverzeichnisse so, wie sie geschrieben sind; er prüft sie nicht. Behandeln Sie jedes
Wurzelverzeichnis genau wie `extension/`:

- Nur vertrauenswürdige Benutzer für das Bereitstellen dürfen in `settings/override/`, `config.php` und jede INI-Datei
  schreiben, die ein Wurzelverzeichnis festlegt.
- `autoloads/*.php` in jedem Wurzelverzeichnis wird beim Erzeugen der Autoloads eingebunden, daher braucht jedes
  Wurzelverzeichnis dieselben Regeln für Besitz und Integrität wie `extension/`.
- Bevorzugen Sie Wurzelverzeichnisse relativ zum Installationsverzeichnis und innerhalb davon.

## Abwärtskompatibilität

- `ExtensionDirectory=extension` bleibt der Standard.
- Mit leerem `AdditionalExtensionDirectories[]` verhält sich der Kernel genau wie bisher.
- Vorhandene Pakete in `extension/` werden nicht verschoben, umbenannt oder migriert.
- Composer-Skripte rufen weiterhin `bin/php/ezpgenerateautoloads.php` auf; es durchsucht einfach mehr
  Wurzelverzeichnisse, wenn welche gesetzt sind.
- Die Mechanismen für INI, Templates, Klassen-Overrides und Autoloads sind unverändert.

## Tests

`tests/tests/kernel/classes/eZExtensionAdditionalDirectoriesTest.php` prüft, dass:

- `extensionRootDirectories()` `ExtensionDirectory` und `AdditionalExtensionDirectories[]` zusammenführt;
- `extensionPath()` eine Erweiterung im Basis-Wurzelverzeichnis und in einem zusätzlichen Wurzelverzeichnis findet;
- ein späteres Wurzelverzeichnis gewinnt, wenn derselbe Name in mehreren existiert;
- ein unbekannter Name `false` zurückgibt;
- Namen ohne Beachtung der Groß- und Kleinschreibung verglichen werden und ihre Schreibweise auf der Festplatte
  behalten;
- aktive Erweiterungen aus zusätzlichen Wurzelverzeichnissen aufgelöst werden.

## Verwandte Seiten

- [Zusätzliche Erweiterungsverzeichnisse (Funktion)](../../funktionen/6.0/zusaetzliche-extension-verzeichnisse.md)
- [Spezifikation: Metadaten von Erweiterungen](../../spezifikationen/6.0/extension-metadaten.md)
- [Spezifikation: Verzeichnisse und Platzierungen von INI-Overrides](../../spezifikationen/6.0/ini-override-platzierungen.md)
- [Erweiterungen: Verhaltensänderungen](verhaltensaenderungen-von-erweiterungen.md)
- [Anleitung zu Erweiterungen](../../anleitungen/extensions.md)
