---
title: Autoloads und Klassen-Maps
audience: php-developer, developer
category: php-development
order: 160
summary: Wie Exponential PHP-Klassen findet - die generierten Klassen-Maps für den Kernel, die Erweiterungen, Kernel-Overrides und Tests, jede Option von bin/php/ezpgenerateautoloads.php, .autoloadignore, der Autoloader von Composer daneben und was zu tun ist, wenn eine Klasse nicht gefunden wird.
keywords: Autoload, Klassen-Map, Kernel-Override, Klasse nicht gefunden, autoload, class map, ezpgenerateautoloads.php, ezp_kernel.php, ezp_extension.php, ezp_override.php, ezp_tests.php, ezpAutoloader, EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE, .autoloadignore, kernel override, Composer autoload, class not found
type: help
---
# Autoloads und Klassen-Maps

Diese Seite richtet sich an PHP-Entwickler, die Klassen hinzufügen, umbenennen oder verschieben. Exponential verwendet
kein PSR-4. Es findet Klassen über Arrays, die jedem Klassennamen seine Datei zuordnen und durch Durchsuchen des
Quellcodes erzeugt werden. Eine Klasse, die in den Arrays fehlt, existiert für die Anwendung nicht, so korrekt ihre
Datei auch sein mag.

## Inhalt

1. [Die Klassen-Maps](#die-klassen-maps)
2. [Der Generator und seine Optionen](#der-generator-und-seine-optionen)
3. [Was der Generator durchsucht](#was-der-generator-durchsucht)
4. [Kernel-Overrides](#kernel-overrides)
5. [Der Autoloader von Composer](#der-autoloader-von-composer)
6. [Wann Sie neu generieren](#wann-sie-neu-generieren)
7. [Wenn eine Klasse nicht gefunden wird](#wenn-eine-klasse-nicht-gefunden-wird)

## Die Klassen-Maps

| Map | Umfasst | Generiert mit | Geladen |
|---|---|---|---|
| `autoload/ezp_kernel.php` | Kernel- und Bibliotheksklassen | `-k` (Kernel-Entwickler; committet) | immer |
| `var/autoload/ezp_extension.php` | Klassen der aktiven Erweiterungsverzeichnisse | `-e` | wenn sie existiert |
| `var/autoload/ezp_override.php` | durch Erweiterungen ersetzte Kernel-Klassen | `-o` | nur mit `EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE` |
| `var/autoload/ezp_tests.php` | Testklassen | `-s` | nur in Tests (`ezpAutoloader::enableTests()`) |

Der Autoloader in `autoload.php` führt sie zusammen; wenn eine Klasse in der Erweiterungs-Map und in der Kernel-Map
vorkommt, steht der Eintrag der Erweiterung bei der Zusammenführung zuerst. Die Laufzeit-Maps lassen jedes
`tests/`-Verzeichnis aus.

## Der Generator und seine Optionen

```bash
php bin/php/ezpgenerateautoloads.php -e          # extensions (the one you need most)
php bin/php/ezpgenerateautoloads.php -k          # kernel (kernel developers)
php bin/php/ezpgenerateautoloads.php -o          # kernel overrides
php bin/php/ezpgenerateautoloads.php -s          # tests
php bin/php/ezpgenerateautoloads.php -e -n -v    # dry run, verbose: what would change
php bin/php/console exp:ezpgenerateautoloads -e  # the same through the console
```

| Option | Bedeutung |
|---|---|
| `-e`, `--extension` | die Erweiterungs-Map |
| `-k`, `--kernel` | die Kernel-Map |
| `-o`, `--kernel-override` | die Map der Kernel-Overrides |
| `-s`, `--tests` | die Test-Map |
| `-n`, `--dry-run` | nichts schreiben |
| `-v`, `--verbose`, `-p`, `--progress` | mehr Ausgabe |
| `-t`, `--target` | ein anderes Verzeichnis für die generierte Datei |
| `--exclude` | Ordner, die bei der Suche ausgelassen werden |
| `<extension>` | für eine einzelne Erweiterung generieren |

`composer install` und `composer update` führen den Generator danach aus (`"legacy-scripts"` in `composer.json`).

## Was der Generator durchsucht

- `-e` durchläuft die Erweiterungsverzeichnisse (`extension/` und `[ExtensionSettings] AdditionalExtensionDirectories[]`)
  PHP-Datei für PHP-Datei und listet jede gefundene Klasse, jedes Interface und jeden Trait mit seinem Pfad auf.
- `.autoloadignore` im Wurzelverzeichnis nennt Verzeichnisse, die der Generator niemals durchlaufen darf. `vendor/` ist
  eines davon: Composer löst dessen Klassen auf, und als sie einmal in der Kernel-Map aufgeführt waren, kamen Hunderte
  von Klassen hinein, die niemand aktuell hielt.
- Ein doppelter Klassenname wird gemeldet; benennen Sie eine der beiden Klassen um
  ([Klassenbenennung und das Präfix exp](klassenbenennung-und-das-praefix-exp.md)).

## Kernel-Overrides

Eine Erweiterung kann eine Kernel-Klasse durch ihre eigene gleichnamige Kopie ersetzen, aufgeführt in
`var/autoload/ezp_override.php` (`-o`). Das ist nur aktiv, wenn `config.php` `EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE` als
`true` definiert (`config.php-RECOMMENDED` zeigt die Zeile, auskommentiert).

Das ist der schwerste Mechanismus und das letzte Mittel: Nichts registriert den Ersatz unter seinem Namen, zwei
Erweiterungen, die dieselbe Klasse ersetzen, kollidieren, ohne es zu wissen, und die Kopie muss jedem Kernel-Update von
Hand folgen. Bevorzugen Sie einen registrierten Erweiterungspunkt, einen durch eine Einstellung benannten Handler, einen
Event-Listener oder eine Unterklasse über `[RunnableSettings] Implementation[]`
([Kernel-Overrides, das letzte Mittel](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/kernel-overrides-der-letzte-ausweg.md),
[Erweiterungspunkte aus PHP](erweiterungspunkte-aus-php.md)).

## Der Autoloader von Composer

Bibliotheken in `vendor/` (Zeta Components, PHPUnit, `mongodb/mongodb` und der Rest) werden vom eigenen Autoloader von
Composer geladen, den `vendor/autoload.php` registriert. Eine Erweiterung, die eine Bibliothek braucht, gibt sie in ihrer
eigenen `composer.json` an; sie kopiert niemals `vendor/` in die Erweiterung
([Composer für PHP-Entwickler](composer-fuer-php-entwickler.md)).

## Wann Sie neu generieren

| Änderung | Ausführen |
|---|---|
| eine neue, umbenannte oder verschobene Klasse in einer Erweiterung | `-e` |
| eine Erweiterung aktiviert oder hinzugefügt | `-e` |
| eine neue Kernel-Klasse | `-k`, und committen Sie `autoload/ezp_kernel.php` |
| ein neuer Kernel-Override | `-o` |
| eine neue Testklasse | `-s` |
| unter Velocity | `exp:velocity deploy` (`--kernel` für eine Kernel-Klasse): generiert neu und startet dann die Worker neu, die die Map behalten, mit der sie gestartet sind |

## Wenn eine Klasse nicht gefunden wird

| Symptom | Ursache | Lösung |
|---|---|---|
| `Class "acmeReportService" not found` | Map nicht neu generiert | `ezpgenerateautoloads.php -e` |
| funktioniert auf der Kommandozeile, schlägt im Web fehl | die Worker des Webservers sind gestartet, bevor es die Klasse gab | PHP-FPM neu laden oder `exp:velocity deploy` |
| eine Einstellung nennt eine Klasse, und Seiten brechen | die Einstellung ging live, bevor die Klasse bereitgestellt war | zuerst den Code bereitstellen; `class_exists()`, wo eine fehlende Klasse keine Seite brechen darf |
| der Generator meldet ein Duplikat | zwei Dateien deklarieren die Klasse | eine umbenennen |
| eine Klasse in `tests/` wird zur Laufzeit nicht gefunden | Laufzeit-Maps lassen `tests/` aus | verschieben Sie sie aus `tests/` heraus |

## Verwandte Seiten

- [Autoloads und Klassenbenennung (Anwendungsentwickler)](../ersteller/anwendungsentwickler/autoloads-und-klassenbenennung.md)
- [Autoloads und Klassenbenennung (Erweiterungsentwickler)](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/autoloads-und-klassennamen.md)
- [Zusätzliche Erweiterungsverzeichnisse](../../funktionen/6.0/zusaetzliche-extension-verzeichnisse.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
