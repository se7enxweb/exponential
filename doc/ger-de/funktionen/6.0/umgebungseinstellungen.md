# Einstellungen pro Umgebung (EXP_ENV)

Diese Seite ist für Administratoren und Entwickler, die dieselbe Installation auf mehreren Rechnern betreiben: einem
Entwicklerrechner, einer Test- oder Staging-Kopie und der Live-Website. Seit Oktober 2026 benennt eine Konstante,
`EXP_ENV`, die Umgebung eines Rechners, und jede Einstellungsdatei kann daneben eine Variante für diese Umgebung haben.
Die Einstellungen, die sich pro Rechner unterscheiden, müssen nach jeder Kopie oder jedem Deployment nicht mehr von Hand
bearbeitet werden.

## Einrichten

1. Kopieren Sie `config.env.php.example` im Wurzelverzeichnis der Installation nach `config.env.php` und setzen Sie den
   Namen:

   ```php
   <?php
   define( 'EXP_ENV', 'test' );
   ```

   `config.env.php` wird vor `config.php` gelesen, von den Web-Einstiegspunkten und von den Kommandozeilenskripten,
   und `.gitignore` hält sie aus der Versionsverwaltung heraus.

2. Legen Sie die Einstellungen der Umgebung in eine Variantendatei. Der Name der Umgebung steht direkt nach `.ini`:

   | Standarddatei | Variante für `test` |
   |---|---|
   | `settings/site.ini` | `settings/site.ini.test` |
   | `settings/override/site.ini.append.php` | `settings/override/site.ini.test.append.php` |
   | `settings/siteaccess/admin/site.ini.append.php` | `settings/siteaccess/admin/site.ini.test.append.php` |
   | `extension/site_example/settings/site.ini.append.php` | `extension/site_example/settings/site.ini.test.append.php` |

   ```ini
   # settings/override/site.ini.test.append.php
   <?php /*
   [MailSettings]
   Transport=file

   [DebugSettings]
   DebugOutput=enabled
   */ ?>
   ```

3. Leeren Sie den INI-Cache. Jede Umgebung hat ihre eigenen Cache-Dateien, sodass ein späterer Wechsel von `EXP_ENV`
   kein Leeren des Caches braucht.

## Wie die Varianten gelesen werden

- Eine Variante wird **direkt nach ihrer Standarddatei** gelesen, in jeder Schicht: den Grundeinstellungen, Extensions,
  Extension-Siteaccesses, Siteaccesses und `settings/override`. Sie überschreibt nur die Einstellungen ihrer eigenen
  Schicht, sodass eine Siteaccess-Einstellung weiterhin über eine Variante der Grundeinstellungen gewinnt und
  `settings/override` weiterhin über alles.
- Eine Variante wird auch gelesen, wenn ihre Standarddatei nicht existiert.
- Ohne `EXP_ENV` liest eZINI genau die Dateien, die es vorher las, und die Namen der Cache-Dateien bleiben gleich.
- Ein Name muss aus Kleinbuchstaben, Ziffern, `-` oder `_` bestehen, mit einem Buchstaben beginnen und darf höchstens
  32 Zeichen haben. Ein ungültiger Name wird als keine Umgebung behandelt, und eine Zeile im PHP-Fehlerprotokoll sagt
  es.

## Was der Name außer den Einstellungen ändert

| `EXP_ENV` | Wirkung |
|---|---|
| `dev`, `test` | `index.php` zeigt PHP-Fehler im Browser (`display_errors` ein) |
| `prod`, jeder andere Name, keiner | `display_errors` bleibt, wie die php.ini es setzt |

Templates lesen den Namen mit `{exp_environment()}`, ohne Umgebung eine leere Zeichenkette:

```
{if eq( exp_environment(), 'test' )}<div class="test-site-banner">Test site</div>{/if}
```

Die Administrationsdesigns admin4 und admin4l können die Umgebung als Plakette im Kopfbereich, auf der Anmeldeseite und
vor dem Seitentitel zeigen: `site.ini [AdminEnvironment] Level=auto` nimmt die Stufe aus diesem Namen, oder Sie setzen
die Stufe in der Variantendatei der Umgebung. Siehe [Das Design admin4](admin4-design.md#einstellungen).

## PHP

`eZINI::environment()` gibt den Namen oder `false` zurück, `eZINI::environmentName( $value )` prüft einen Namen,
`eZINI::environmentFilePath( $filePath, $environment )` gibt den Variantenpfad einer Einstellungsdatei zurück, und
`eZINI::resetEnvironment()` lässt den nächsten Aufruf `EXP_ENV` neu lesen (für Tests). Der Operator ist
`expEnvironmentOperator` (`kernel/common/expenvironmentoperator.php`).

## Tests

`tests/tests/lib/ezutils/eZINIEnvironmentTest.php` (Suite lib): die Reihenfolge der Dateien pro Schicht, eine Variante
ohne ihre Standarddatei, unveränderte Dateien und Cache-Dateinamen ohne Umgebung, eine Cache-Datei pro Umgebung,
Prüfungen von Namen und Variantenpfade.

## Der Demo-Modus ist keine Umgebungseinstellung

Der Name der Umgebung (`EXP_ENV`, eine Konstante in `config.env.php`) wählt Einstellungsdateien aus. Der Demo-Modus ist etwas anderes: die Umgebungsvariable `EXP_DEMO_MODE=1` der Serverprozesse, ohne Konstante und ohne INI-Einstellung, sodass er nie mit einer Kopie von `config.php` oder `settings/` mitreist. [Demo-Modus](../../../eng-us/bc/6.0/demo-mode.md), [Umgebungsvariablen](../../anleitungen/umgebungsvariablen.md).

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
