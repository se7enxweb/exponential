---
title: Autoloads und Klassennamen
audience: developer, app-creator
category: extension-development
order: 80
summary: Wie der Kernel Ihre Klassen findet - die Autoload-Arrays, ezpgenerateautoloads.php und was es durchsucht, Namen, die Kollisionen vermeiden, und was zu tun ist, wenn eine Klasse nicht gefunden wird.
keywords: Autoload, Klasse nicht gefunden, Klassennamen, Dateinamen, Präfix, doppelte Klasse, autoload, ezpgenerateautoloads, ezp_extension.php, class not found, class naming, file naming, prefix, exp prefix, autoloadignore, Composer autoload, PSR-4, duplicate class
type: help
---
# Autoloads und Klassennamen

Teil 3, Kapitel 8. Der Kernel verwendet für Klassen keine `include`-Zeilen. Er führt **Autoload-Arrays**, also
Zuordnungen von Klassennamen zu Dateien, und lädt eine Klasse, sobald Code sie zum ersten Mal verwendet. Die Klassen
Ihrer Erweiterung werden diesen Zuordnungen von einem Generator hinzugefügt, den Sie nach dem Hinzufügen oder
Umbenennen einer Klasse ausführen.

## Die Arrays

| Datei | Enthält | Erzeugt mit |
|---|---|---|
| `autoload/ezp_kernel.php` | die Klassen des Kernels | `-k` (nur für Maintainer des Kernels) |
| `var/autoload/ezp_extension.php` | die Klassen der Erweiterungen | `-e` |
| `var/autoload/ezp_override.php` | Kernel-Klassen, die von Erweiterungen ersetzt werden | `-o` |
| `var/autoload/ezp_tests.php` | Testklassen | `-s` |

Das Array der Erweiterungen liegt in `var/`, weil es die Erweiterungen dieser Installation beschreibt, nicht den
Kernel.

## Der Generator

```bash
php bin/php/ezpgenerateautoloads.php -e
```

Erwartet: `Found <n> classes, added <n> of them to the autoload array.`. Nützliche Optionen (aus `--help`):

| Option | Wirkung |
|---|---|
| `-e` | das Array der Erweiterungen |
| `-n` | Probelauf: zählen, nichts schreiben |
| `-v` | auflisten, was gefunden wird |
| `--exclude <dir>` | Ordner überspringen |
| Argument `<extension>` | nur eine Erweiterung |

## Was er durchsucht

Im Erweiterungsmodus durchläuft er den gesamten Ordner jeder Erweiterung auf der Festplatte (aktiv oder nicht, in jedem
Erweiterungsstamm) nach PHP-Dateien, die eine Klasse, ein Interface oder einen Trait deklarieren, und überspringt den
Ordner `tests/` jeder Erweiterung (Testklassen kommen in das Test-Array) sowie `tests/` von Composer-Paketen, die in der
Erweiterung mitgeliefert werden (`kernel/private/classes/ezautoloadgenerator.php`). Ordner, die in der Datei
`.autoloadignore` im Stammverzeichnis aufgeführt sind, werden nie durchlaufen.

Ihre Klassen können also in `classes/`, `classes/<subfolder>/`, `autoloads/`, `modules/` ... liegen, wo immer es
sinnvoll ist. `classes/` ist die Konvention.

## Benennung

Klassennamen sind global: Zwei Erweiterungen mit einer Klasse `Helper` machen sich gegenseitig kaputt. Die Konventionen
dieses Ökosystems:

| Art | Konvention | Beispiel |
|---|---|---|
| Ihre Klassen | ein Präfix Ihrer Erweiterung oder Organisation, Camel Case | `mysiteHelloGreeter`, `acmeShopExporter` |
| Neue Klassen dieses Projekts | das Präfix `exp` | `expInfo`, `expExtensionCatalogue` |
| Erweiterungen und Klassen von 7x | `sevenx` oder `exp`, je nach Bedarf von 7x | `sevenx_dse`, `explayouts` |
| Die historischen Klassen des Kernels | `eZ...`, `ezp...` | `eZINI`, `ezpExtension` (verwenden Sie das Präfix nie für neuen Code) |
| Dateiname | der Klassenname in Kleinbuchstaben | `classes/mysitehellogreeter.php` |
| Datentypen | `<datatype>type.php`, Klasse `<Name>Type` | `datatypes/mysiterating/mysiteratingtype.php` |
| Metadaten | `<extension>Info` in `ezinfo.php` | `mysite_helloInfo` |

Warum 7x zwei Präfixe hat: `sevenx` ist das offizielle öffentliche Präfix für PHP-Code von 7x, weil ein Klassenname
nicht mit einer Ziffer beginnen kann und "7x" deshalb keines sein kann; `exp` wird bewusst verwendet, um die Marke
Exponential aufzubauen und die Zuständigkeit dem Projekt Exponential und der Exponential Foundation zuzuordnen. Siehe
[Namenspräfixe: sevenx und exp](../../../standards/namenspraefixe-sevenx-und-exp.md).

Eine Klasse pro Datei. Deklarieren Sie in einer Klassendatei keine Funktionen oder Konstanten auf Dateiebene:
Autoloading funktioniert nur für Klassen.

## Composer-Bibliotheken in einer Erweiterung

Eine Erweiterung, die eine Bibliothek eines Drittanbieters braucht, kann sie in ihrer `composer.json` anfordern; der
Autoloader von Composer lädt sie dann neben den Arrays des Kernels. Eine kleine Bibliothek, die in der Erweiterung
mitgeliefert wird (`extension/<ext>/vendor/...`), findet der Generator ebenfalls; ziehen Sie, wo möglich, eine
Composer-Abhängigkeit vor.

## Wenn eine Klasse nicht gefunden wird

1. Führen Sie `php bin/php/ezpgenerateautoloads.php -e -v` aus und suchen Sie Ihre Klasse in der Ausgabe.
2. Liegt die Datei in einem Erweiterungsstamm (`extension/` oder ein Ordner aus `AdditionalExtensionDirectories[]`)?
   Jede Erweiterung auf der Festplatte wird durchsucht, aktiv oder nicht, aber nur die Einstellungen einer aktiven
   Erweiterung registrieren ihre Teile.
3. Enthält die Datei einen Syntaxfehler? Prüfen Sie sie mit `php -l`; eine Datei, die PHP nicht parsen kann, wird
   übersprungen.
4. Deklariert eine andere Erweiterung denselben Klassennamen? Der Generator warnt vor Duplikaten.
5. Leeren Sie die Caches und sorgen Sie dafür, dass das laufende PHP die neue Zuordnung sieht: Laden Sie PHP-FPM neu
   oder führen Sie `./console exp:velocity deploy` aus.

`php bin/php/checkclasses.php --quiet-ok` lädt anschließend jede Klasse und listet die auf, die PHP ablehnt.

## Verwandte Seiten

- Zurück: [Einstellungen in einer Erweiterung](einstellungen-in-einer-erweiterung.md)
- Weiter: [Module und Views](module-und-views.md)
- [Kernel-Klassen-Overrides: der letzte Ausweg](kernel-overrides-der-letzte-ausweg.md)
- [Erweiterungen debuggen](erweiterungen-debuggen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
