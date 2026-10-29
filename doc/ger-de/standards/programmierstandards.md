---
title: Programmierstandards
audience: developer, admin
category: standards
order: 80
summary: Der Codestil von Exponential und wie er geprüft wird - das phpcs-Regelwerk, PHPCompatibility für PHP 8.0, PHPStan mit Baseline, PHP-8.0-Lint, Dateiköpfe, Benennung sowie die Regeln für Templates, CSS und JavaScript.
keywords: Programmierstandards, Codestil, Coding-Standards, Dateikopf, Urheberrecht, Namenskonventionen, Präfix, Templates, Stylesheets, coding standards, code style, phpcs, PHP_CodeSniffer, phpcs.xml.dist, PHPCompatibility, PHPStan, baseline, lint, php -l, lint80, docblock, file header, copyright, naming conventions, exp prefix, sevenx prefix, templates, INI, CSS, JavaScript, CSP, nonce, EditorConfig
type: reference
---
# Programmierstandards

Diese Seite richtet sich an Entwickler, die den Kernel, seine Templates oder seine Extensions ändern, und an Prüfer,
die solche Änderungen kontrollieren. Sie beantwortet: wie der Codestil aussieht, welche Werkzeuge ihn prüfen und wie
streng, wie Dateien eingeleitet und benannt werden und welche Regeln für Templates, Stylesheets und Skripte gelten. Die
tägliche Verwendung der Werkzeuge beschreibt [Qualitätsprüfungen](../funktionen/6.0/qualitaetspruefungen.md).

## Zusammenfassung

| Standard | Version | Status | Nachweis (kurz) |
|---|---|---|---|
| Codestil von Exponential (PHP_CodeSniffer) | `phpcs.xml.dist`, "Exponential 6.0.15 code style" | **Teilweise erfüllt** | nur neue Verstöße schlagen fehl; in CI nur eine Warnung |
| PHP-8.0-Kompatibilität (PHPCompatibility) | `testVersion` `8.0-` | **Erfüllt** | `phpcs.xml.dist`, CI-Job `php80` prüft jede Datei mit PHP 8.0 per Lint |
| Statische Analyse (PHPStan) | PHPStan 2.1, Level 1 | **Teilweise erfüllt** | `phpstan.neon.dist`, Baseline mit etwa 3.000 Befunden, blockierend in CI |
| PHP-Syntax (`php -l`) | PHP 8.3 und 8.0 | **Erfüllt** | `.githooks/pre-commit`, `quality.yml`, `make lint80-all` |
| Docblock als Dateikopf | Projektkopf | **Teilweise erfüllt** | 1.454 von 1.862 PHP-Dateien in `kernel/` und `lib/` tragen die aktuelle Copyright-Zeile |
| Benennung (Präfixe sevenx und exp, Datei = Klassenname in Kleinbuchstaben) | [schriftliche Regel](namenspraefixe-sevenx-und-exp.md) | **Erfüllt** für neuen Code | 375 Dateien `exp*.php` in `kernel/` und `lib/` |
| Kein Inline-CSS in Admin-Templates | Projektregel | **Teilweise erfüllt** | `tests/tests/design/AdminStylingRulesTest.php` mit einer schrumpfenden Allowlist |
| Kein Inline-Skript (Content Security Policy) | CSP Level 3 (W3C) | **Teilweise erfüllt** | `AdminContentSecurityPolicyTest.php`, `ExtensionInlineScriptsTest.php` |
| JavaScript-Syntax | ECMAScript, Node.js 22 `--check` | **Erfüllt** für die Syntax | `quality.yml`, Schritt "JavaScript syntax of changed files" |
| EditorConfig | - | **Nicht anwendbar** | keine `.editorconfig` im Repository |

## Der PHP-Codestil: `phpcs.xml.dist`

**Was es ist.** PHP_CodeSniffer (`squizlabs/php_codesniffer` `^3.13`, aus `composer.dev.json`) prüft Code gegen ein
Regelwerk. Das Regelwerk des Repositorys ist `phpcs.xml.dist`, benannt "Exponential" und beschrieben als "Exponential
6.0.15 code style". Es erweitert keinen ganzen Standard (weder PSR-12 noch PEAR), sondern wählt einzelne Sniffs aus.
Der Kommentar am Anfang besagt, dass jede Regel "vor ihrer Aufnahme am Code gemessen wurde".

**Was es prüft.**

| Regel | Sniff | Bedeutung |
|---|---|---|
| Lange Array-Syntax | `Generic.Arrays.DisallowShortArraySyntax` | `array( ... )`, nie `[ ... ]` |
| Leerzeichen in Kontrollstrukturen | `PSR2.ControlStructures.ControlStructureSpacing` (1 Leerzeichen nach `(` und vor `)`) | `if ( $a )`, `foreach ( $list as $item )` |
| Leerzeichen in Aufrufen | `PEAR.Functions.FunctionCallSignature` (1 Leerzeichen innen; ausgenommen sind Einrückung, Zeile der schließenden Klammer, Inhalt nach der öffnenden Klammer, öffnende Einrückung, mehrere Argumente pro Zeile und Leerzeilen) | `foo( $a, $b )`, kein Leerzeichen vor `(`; mehrzeiliges Layout ist frei |
| Leerzeichen in Deklarationen | `Squiz.Functions.FunctionDeclarationArgumentSpacing` (1 Leerzeichen innen, 1 um `=`) | `function foo( $a, $b = 1 )` |
| Geschweifte Klammern von Funktionen | `Generic.Functions.OpeningFunctionBraceBsdAllman` | die `{` einer Funktion auf einer eigenen Zeile |
| Not-Operator | `Generic.Formatting.SpaceAfterNot` (Abstand 0) | `!$a` |
| Kleinschreibung | `Generic.PHP.LowerCaseConstant`, `Generic.PHP.LowerCaseKeyword` | `true`, `false`, `null`, Schlüsselwörter |
| Dateien | `Generic.PHP.DisallowShortOpenTag`, `Generic.Files.LineEndings`, `Generic.WhiteSpace.DisallowTabIndent`, `Squiz.WhiteSpace.SuperfluousWhitespace.EndLine` | `<?php`, Unix-Zeilenenden, Leerzeichen statt Tabulatoren, keine Leerzeichen am Zeilenende |
| Verbotene Funktionen | `Generic.PHP.ForbiddenFunctions` | `each`, `create_function`, `split`, `ereg`, `eregi`, `ereg_replace`, `mysql_query`, `mysql_connect`, `sizeof`, `delete`, jeweils mit benanntem Ersatz (`foreach`, eine Closure, `preg_*`, `eZDB`, `count`, `unset`) |
| Referenzübergabe beim Aufruf | `Generic.Functions.CallTimePassByReference` | nicht erlaubt |
| Keine Typdeklarationen | `bin/phpcs/Exponential/Sniffs/TypeDeclarations/NoTypeDeclarationsSniff.php` | kein `declare( strict_types=1 )`, keine Skalar-, Union-, Rückgabe-, Eigenschafts- oder Konstantentypen; die PHP-5-Hinweise `array`, `callable`, `self` und Klassennamen (auch nullable) bleiben erlaubt; Tests dürfen `: void` deklarieren |
| PHP-8.0-Kompatibilität | `PHPCompatibility` mit `testVersion` `8.0-` | keine Syntax und keine Funktion, die neuer als PHP 8.0 ist, sofern nicht abgesichert, keine entfernten oder veralteten Funktionen |

Ausgeschlossene Pfade: `*/vendor/*`, `*/.devtools/*`, `*/var/*`, `*/node_modules/*`. Geprüft werden nur `.php`-Dateien.

**Wie es durchgesetzt wird.**

- `bin/php/checkcodestyle.php` führt phpcs nur für geänderte Dateien aus und vergleicht die Zahl der Verstöße jeder
  Datei mit ihrer vorherigen Version. **Nur eine Änderung, die Verstöße hinzufügt, schlägt fehl**, sodass das
  Bearbeiten einer alten Datei nicht verlangt, sie neu zu formatieren.
  - `--staged` im Pre-Commit-Hook (`.githooks/pre-commit`), `--base=<ref>` für `make phpcs` und CI.
  - Ohne die Entwicklungswerkzeuge (`make devtools`) gibt es einen Hinweis aus und besteht.
- In CI (`.github/workflows/quality.yml`, Schritt "Code style of changed files (warning only)") hat es
  `continue-on-error: true`: **Ein Verstoß ist eine Warnung, kein Fehlschlag.**
- Der eigene Sniff des Projekts hat einen Test: `tests/tests/bin/phpcs/NoTypeDeclarationsSniffTest.php`, ausgeführt im
  blockierenden CI-Job "Code style sniff tests" (`phpunit --testsuite quality`).

**Status: Teilweise erfüllt.** Neuer Code wird lokal am Stil gemessen; CI blockiert nicht darauf, und alter Code wird
nicht neu formatiert.

## Der überlieferte Stil des Kernels in Kürze

Der Stil stammt aus der von eZ Publish 4 übernommenen Codebasis und wird der Einheitlichkeit halber beibehalten:

```php
class expExampleThing
{
    /**
     * Returns the names of the items, sorted.
     *
     * @param array $items
     * @return array
     */
    public static function sortedNames( $items )
    {
        $names = array();
        foreach ( $items as $item )
        {
            if ( !$item->attribute( 'is_hidden' ) )
            {
                $names[] = $item->attribute( 'name' );
            }
        }
        sort( $names );
        return $names;
    }
}
```

- Geschweifte Klammern auf einer eigenen Zeile bei Klassen, Funktionen und Kontrollstrukturen (geprüft wird nur die
  Klammer der Funktion).
- Vier Leerzeichen Einrückung.
- Typen werden in Docblocks dokumentiert (`@param`, `@return`), nicht deklariert.

## Statische Analyse: PHPStan

**Was es ist.** PHPStan (`phpstan/phpstan` `^2.1`, aus `composer.dev.json`) findet Typ- und Logikfehler, ohne den Code
auszuführen.

**Wie es konfiguriert ist** (`phpstan.neon.dist`):

- `level: 1` (von 0 bis 10), `phpVersion: 80100`.
- `paths`: `kernel` und `lib`. Ausgeschlossen: `vendor/*`, `var/*`, `*/tests/*`, `kernel/private/classes/commands/*`
  (Skripte, die globale Hilfsfunktionen deklarieren) und
  `kernel/private/classes/clusterfilehandlers/dfsbackends/dfs_filter_iterator.php` (fehlende vorläufige Rückgabetypen).
- `bootstrapFiles`: `autoload.php`; `treatPhpDocTypesAsCertain: false`.
- `phpstan-baseline.neon` enthält die Befunde, die der Code bei Einführung der Prüfung hatte: 1.271 Einträge mit einer
  Gesamtzahl von 3.026 (für diese Seite summiert; [Qualitätsprüfungen](../funktionen/6.0/qualitaetspruefungen.md) nennt 3.033).
  **Nur Befunde, die nicht in der Baseline stehen, schlagen fehl.**

**Wie es durchgesetzt wird.** `make phpstan`; der `pre-push`-Hook (`.githooks/pre-push`), wenn die
Entwicklungswerkzeuge installiert sind; der blockierende CI-Job `phpstan` (PHP 8.3) in `quality.yml`.
`make phpstan-baseline` erzeugt die Baseline neu.

**Status: Teilweise erfüllt.** Level 1 ist eine niedrige Stufe, und die Baseline akzeptiert etwa 3.000 bestehende
Befunde.

## PHP-Syntax: `php -l` und PHP 8.0

- `.githooks/pre-commit` führt `php -l` für jede gestagte PHP-Datei in ihrem gestagten Zustand aus, und zusätzlich mit
  PHP 8.0, wenn eine PHP-8.0-Binärdatei gefunden wird (`EXP_PHP80`, `php8.0`, `php80` oder die PHP-8.0-Binärdatei
  eines Hosting-Panels).
- `make lint` und `make lint80` prüfen die seit `origin/main` geänderten Dateien; `make lint80-all` prüft jede
  PHP-Datei in Git mit PHP 8.0.
- CI: `quality.yml` prüft geänderte Dateien per Lint mit PHP 8.3 (Job `checks`) und jede Datei mit PHP 8.0
  (Job `php80`, `make lint80-all PHP80=php`), beide blockierend.

**Status: Erfüllt.** Siehe [Unterstützung für PHP 8.0](../bc/6.0/php-8.0-unterstuetzung.md) dazu, wie neuere PHP-Funktionen
abgesichert werden.

## Docblock als Dateikopf

Neue PHP-Dateien beginnen mit einem Docblock, der den Inhalt der Datei, das Copyright, die Lizenz und das Paket nennt:

```php
<?php
/**
 * File containing the expHelpAudience class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */
```

- Für diese Seite gezählt in `kernel/` und `lib/` (1.862 PHP-Dateien): 1.454 tragen die Zeile
  `Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.`, 1.818 tragen `@package`, 1.041 tragen daneben noch eine
  übernommene Copyright-Zeile von `eZ Systems AS`, und 1.058 tragen noch den Platzhalter `@version //autogentag//`.
- Die Zeile `@license` hat zwei verbreitete Formen: "GNU General Public License v2.0 (or any later version)"
  (684 Dateien) und die ältere "For full copyright and license information view LICENSE file distributed with this
  source code" (1.142 Dateien). Beide verweisen auf die Lizenz GPL-2.0-or-later aus `composer.json`.
- Übernommene Copyright-Zeilen bleiben erhalten: Sie halten fest, wer den ursprünglichen Code geschrieben hat.

### Die alte Kopfvorlage: header.txt

[header.txt](../../eng-us/standards/header.txt) ist der Dateikopf aus der Zeit von 4.x, zur Referenz aufbewahrt. Es ist ein Block aus
`//`-Kommentaren mit `SOFTWARE NAME`, `SOFTWARE RELEASE: 4.1.x`, zwei Zeilen `COPYRIGHT NOTICE` und dem vollständigen
GPL-v2.0-Hinweis. **Verwenden Sie ihn nicht für neue Dateien**; verwenden Sie den Docblock oben. Alte Dateien, die
noch diese Form haben, behalten sie.

**Status: Teilweise erfüllt.** Kein Werkzeug prüft den Dateikopf; die obigen Zahlen zeigen die Mischung.

## Namenskonventionen

Die Präfixregel ist vollständig in [Benennungspräfixe: sevenx und exp](namenspraefixe-sevenx-und-exp.md)
festgehalten: `sevenx` ist das offizielle öffentliche PHP-Code-Präfix von 7x (ein Bezeichner kann nicht mit einer
Ziffer beginnen, daher kann "7x" keines sein), Extensions und Klassen von 7x verwenden `sevenx` oder `exp`, wie 7x es
braucht, und `exp` wird bewusst eingesetzt, um die Marke Exponential aufzubauen und die Zugehörigkeit zum Projekt
Exponential und zur Exponential Foundation festzulegen.

- **Neuer Code verwendet das Präfix `exp`**: Klassen `expFoo` (`expHelpAudience`, `expContentSecurityPolicy`),
  Template-Operatoren (`kernel/common/expcontentsecuritypolicyoperator.php`), Templates
  (`design/standard/templates/parts/exp_content_direction.tpl`), Module und Einstellungen. 375 Dateien in `kernel/`
  und `lib/` heißen `exp*.php`.
- **Bestehende Bezeichner werden nie umbenannt**: `eZContentObject`, `eZINI`, `eZPersistentObject`, `ezpKernelWeb`
  und die Dateinamen `ez*` bleiben (667 Dateien `ez*.php` in `kernel/` und `lib/`). Eine Umbenennung würde jede
  Extension zerstören.
- **Dateiname = Klassenname in Kleinbuchstaben**: `expHelpAudience` liegt in `kernel/classes/help/exphelpaudience.php`,
  `eZMySQLCharset` in `lib/ezdb/classes/ezmysqlcharset.php`. Der Autoload-Generator findet Klassen durch Scannen,
  daher ist das eine Konvention, keine technische Voraussetzung.
- **Templates** sind kleingeschrieben mit Unterstrichen, `.tpl` (kein Template in `design/` hat einen Großbuchstaben
  im Pfad, für diese Seite geprüft); Überschreibungs-Templates liegen unter `override/templates/`.
- **INI-Dateien**: Die Standardwerte stehen in `settings/<name>.ini`; Überschreibungen sind `<name>.ini.append.php` in
  `settings/override/`, `settings/siteaccess/<siteaccess>/` oder im `settings/` einer Extension. Abschnitte sind
  `[CamelCase]`, Einstellungen `CamelCase`, Listen `Name[]`.

**Status: Erfüllt** für neuen Code. Die Regel ist in
[Benennungspräfixe: sevenx und exp](namenspraefixe-sevenx-und-exp.md) niedergeschrieben.

## Templates, Stylesheets und Skripte

### Kein Inline-CSS

Die Regeln stehen in [Gestaltung der Administrationsoberfläche](../bc/6.0/admin4-gestaltung.md) und in `CONTRIBUTING.md`:

- kein `<style>`-Block und kein Attribut `style=""` in einem Template eines Administrationsdesigns;
- zuerst die gemeinsamen Komponenten (`.a4-card`, `.a4-btn`, `.a4-table` ...) aus `stylesheets/components.css`;
- Farben, Radius, Abstände und Schriften nur aus den Tokens `--a4-*`, und jedes Farb-Token hat einen Wert für
  `html[data-a4-theme="dark"]`;
- Modul-CSS nur in `stylesheets/modules/<group>.css`.

`tests/tests/design/AdminStylingRulesTest.php` (Suite `design`, ohne Datenbank) setzt sie für admin4, admin4l, admin3
und admin durch. Dateien, die noch migriert werden, stehen auf einer **Allowlist, die nur schrumpft**: Eine
aufgeführte Datei, die sauber ist, schlägt fehl, bis sie von der Liste entfernt wird.
`tests/tests/design/StandardDatatypeEditNoStyleAttributeTest.php` deckt die Bearbeitungs-Templates der Datentypen im
Design standard ab, und die Suite `admin-extensions` deckt die Admin-Templates von `git_manager`, `ezupdate` und
`sevenx_dse` ab.

**Status: Teilweise erfüllt** (die Allowlist ist noch nicht leer).

### Kein Inline-Skript: die Content Security Policy

**Was es ist.** Content Security Policy Level 3 (W3C) erlaubt einer Site, Inline-Skripte und -Styles zu verbieten.

- `settings/site.ini [ContentSecurityPolicy]` konfiguriert sie (`Mode=auto`, `Nonce=enabled`,
  `NonceDirectives[]=script-src`); siehe [Content Security Policy](../funktionen/6.0/content-security-policy.md).
- `kernel/private/classes/expcontentsecuritypolicy.php` baut den Header; eine Nonce pro Anfrage steht Templates über den
  Operator `csp_nonce()` für ein Inline-Skript zur Verfügung, das sich nicht vermeiden lässt.
- `tests/tests/design/AdminContentSecurityPolicyTest.php` prüft, dass die von admin4 und admin4l gerenderten Templates
  kein Inline-Skript, keinen `on...=`-Handler, keine `javascript:`-URL, kein `style=""` und kein `<style>` enthalten.
- `tests/tests/design/ExtensionInlineScriptsTest.php` hält aufgeführte Extensions bei Skripten an dieselbe Regel, mit
  einer schrumpfenden Liste bekannter Befunde.

**Status: Teilweise erfüllt.** Einige ältere Designs bleiben im reinen Berichtsmodus (`ReportOnlyDesigns[]`), und
Site-Designs werden von den Tests nicht abgedeckt.

### JavaScript

- Syntax: `node --check` für geänderte `.js`-Dateien (nicht `*.min.js`) im Pre-Commit-Hook, wenn Node.js installiert
  ist, und in CI (Node.js 22).
- Designtests führen Skripte von admin4 in Node.js gegen `tests/tests/design/js/mini_dom.js` aus.
- Es gibt keine Konfiguration für ESLint, Prettier oder Stylelint im Repository.

**Status: Erfüllt** für die Syntax; über die Designtests hinaus gibt es kein Stil- oder Lint-Regelwerk für JavaScript
oder CSS.

### EditorConfig

Es gibt keine Datei `.editorconfig` im Repository. **Status: Nicht anwendbar.** Editoren müssen von Hand auf vier
Leerzeichen, Unix-Zeilenenden und UTF-8 eingestellt werden; phpcs prüft die ersten beiden für PHP.

## Bekannte Lücken

1. Der Codestil ist in CI eine Warnung, kein Fehlschlag.
2. PHPStan läuft auf Level 1 mit etwa 3.000 akzeptierten Befunden.
3. Die Dateiköpfe sind gemischt (alte Formulierung von `@license`, `@version //autogentag//`, der `//`-Kopf aus 4.x in
   alten Dateien).
4. `CONTRIBUTING.md` verlinkt ein `CODING_STYLE.md`, das nicht existiert.
5. Kein Linter für JavaScript oder CSS; keine `.editorconfig`.
6. Die Regeln zu Inline-CSS und Inline-Skripten haben noch Allowlists.

## Verwandte Seiten

- [Benennungspräfixe: sevenx und exp](namenspraefixe-sevenx-und-exp.md)
- [Qualitätsprüfungen](../funktionen/6.0/qualitaetspruefungen.md)
- [Gestaltung der Administrationsoberfläche](../bc/6.0/admin4-gestaltung.md)
- [Content Security Policy](../funktionen/6.0/content-security-policy.md)
- [Standards für PHP, PHP-FIG und Composer](php-und-composer.md)
- [Tests und Continuous Integration](tests-und-ci.md)

## Urheberrecht und Copyleft

Seit dem 29. Oktober 2026 halten die Exponential Foundation und 7x das Copyright an Exponential gemeinsam, die
Foundation zuerst genannt: „Exponential Copyright © 1998-2026 Exponential Foundation and 7x“, in Dateien
„Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.“ Wer es hält und warum, was die
GPL Ihnen erlaubt, welche Hinweise Dritter bleiben und wie der Hinweis in neue Dateien und Erweiterungen kommt:
[Urheberrecht und Copyleft](../hilfe/urheberrecht-und-copyleft.md).
