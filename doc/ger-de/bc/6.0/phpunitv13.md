# Unterstützung für PHPUnit 13 und die Testsuite des String-Operators

Lesen Sie diese Seite, wenn Sie die Testsuite von Exponential ausführen oder eigene Tests pflegen, die das Test-Toolkit
von Exponential erweitern. Nach einem Upgrade auf PHPUnit 13 (`phpunit/phpunit 13.0.0`, PHP 8.4 und 8.5) brach
`php vendor/bin/phpunit` in einem Baum von Exponential 6.0.x, der noch die Konventionen von PHPUnit 10 bis 12
verwendete, mit einem fatalen Fehler ab und **führte überhaupt keine Tests aus**. Das wurde am 2026-03-31 behoben.
Dieselbe Änderung liefert `eZTemplateStringsOperator` aus, der jede String-Funktion von PHP zu einem Template-Operator
macht, mit einer eigenen Testsuite ([Abschnitt 6](#6-neue-funktion-eztemplatestringsoperator-und-seine-testsuite)).

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | PHPUnit 13 hat `TestCase::__construct()` final gemacht und verlangt `: void` bei `setUp()`, `tearDown()`, `setUpBeforeClass()` und `tearDownAfterClass()`. Das Toolkit (`tests/toolkit/ezpregressiontest.php`, `ezpdatabaseregressiontest.php`), `tests/bootstrap.php` und die Tests in lib wurden entsprechend geändert. |
| Wer betroffen ist | Ihre eigenen Tests, die `__construct()` überschreiben oder `setUp()`/`tearDown()` ohne `: void` deklarieren. Tests, die die alten Klassennamen `PHPUnit_Framework_*` verwenden, funktionieren über die Shims im Bootstrap weiter. |
| Wie Sie es prüfen | `php vendor/bin/phpunit --list-tests` muss jede Klasse ohne fatalen Fehler laden. |
| Wie Sie es beheben | Fügen Sie den vier Methoden `: void` hinzu; verschieben Sie Arbeit aus dem Konstruktor in `setUp(): void`. Ausgearbeitete Beispiele stehen unter [Korrektur 3](#4-korrektur-3--testdateien-in-lib--void-hinzufügen-und-konstruktoren-entfernen). |

Die beiden fatalen Fehler, die Sie vor der Korrektur sehen:

```
PHP Fatal error: Cannot override final method PHPUnit\Framework\TestCase::__construct()
  in tests/toolkit/ezpdatabaseregressiontest.php on line N
```

```
PHP Fatal error: Declaration of SomeTest::setUp() must be compatible with
PHPUnit\Framework\TestCase::setUp(): void
```

Beide treten beim Laden der Klasse auf, bevor irgendein Test läuft.

## Inhaltsverzeichnis

1. [Hintergrund — was PHPUnit 13 geändert hat](#1-hintergrund--was-phpunit-13-geändert-hat)
2. [Korrektur 1 — bootstrap.php: erweiterte Shim-Schicht](#2-korrektur-1--testsbootstrapphp-erweiterte-shim-schicht)
3. [Korrektur 2 — Toolkit: unzulässige Konstruktoren aus ezpregressiontest und ezpdatabaseregressiontest entfernen](#3-korrektur-2--toolkit-unzulässige-konstruktoren-aus-ezpregressiontest-und-ezpdatabaseregressiontest-entfernen)
4. [Korrektur 3 — Testdateien in lib: `: void` hinzufügen und Konstruktoren entfernen](#4-korrektur-3--testdateien-in-lib--void-hinzufügen-und-konstruktoren-entfernen)
5. [Befehle zur Überprüfung](#5-befehle-zur-überprüfung)
6. [Neue Funktion: eZTemplateStringsOperator und seine Testsuite](#6-neue-funktion-eztemplatestringsoperator-und-seine-testsuite)
7. [Bereits vorhandene Warnungen — Dateiname und Klassenname passen nicht](#7-bereits-vorhandene-warnungen--dateiname-und-klassenname-passen-nicht)
8. [In diesem Release geänderte Dateien](#8-in-diesem-release-geänderte-dateien)


<a name="background"></a>
## 1. Hintergrund — was PHPUnit 13 geändert hat

| Inkompatible Änderung | Eingeführt in PHPUnit-Version | Wirkung auf das Toolkit von Exponential |
|---|---|---|
| `TestCase::__construct()` wurde `final` | 10 (erzwungen in 13) | Jede Testklasse mit `public function __construct()` scheitert beim Laden fatal |
| `setUp()` verlangt den Rückgabetyp `: void` | 8+ (strikt in 13) | Jedes `public function setUp()` ohne `: void` verursacht eine fatale Unverträglichkeit der Deklaration |
| `tearDown()` verlangt den Rückgabetyp `: void` | 8+ (strikt in 13) | Wie oben |
| `setUpBeforeClass()` / `tearDownAfterClass()` verlangen `: void` | 8+ (strikt in 13) | Ebenso |
| `TestSuiteLoader` ist jetzt streng bei der Übereinstimmung von Dateiname ↔ Klassenname | 13 | Eine Klasse in CamelCase in einer kleingeschriebenen Datei erzeugt eine Warnung (die Tests laufen trotzdem) |
| Klasse `PHPUnit_Framework_Warning` entfernt | 10 | Im Bootstrap per Shim nachgebildet |
| `PHPUnit_Framework_ExpectationFailedException` entfernt | 7 | Im Bootstrap per Shim nachgebildet |


<a name="fix-bootstrap"></a>
## 2. Korrektur 1 — tests/bootstrap.php: erweiterte Shim-Schicht

Der Shim in `tests/bootstrap.php` wurde erstmals bei der Migration auf PHPUnit 10 eingeführt (siehe `phpunitv10.md`).
Für PHPUnit 13 brauchte er die folgenden Ergänzungen:

### 2a. Der Alias `PHPUnit_Framework_TestCase` — der Konstruktor ist jetzt final

PHPUnit 13 macht `TestCase::__construct()` bedingungslos `final`. Weil `PHPUnit_Framework_TestCase` ein einfaches
`class_alias` auf `PHPUnit\Framework\TestCase` ist, erbt **jeder Test, der `PHPUnit_Framework_TestCase` erweitert und
`__construct()` überschreibt**, ebenfalls die Final-Beschränkung — und scheitert fatal.

Der Alias selbst ist korrekt; das Problem liegt in den einzelnen Testdateien (siehe Abschnitt 4).

### 2b. Shim `PHPUnit_Framework_Warning`

Bei der Migration auf PHPUnit 10 hinzugefügt, aber diese Version stellt sicher, dass die Shim-Klasse `__construct()`
**nicht** überschreibt:

```php
if ( !class_exists( 'PHPUnit_Framework_Warning', false ) )
{
    class PHPUnit_Framework_Warning extends PHPUnit\Framework\TestCase
    {
        // Inherits final TestCase::__construct(string $name) — no override.
        // The warning message is passed as $name; surfaces as a test error
        // when run, which is the correct warning behaviour in PHPUnit 13.
    }
}
```

### 2c. Alias `PHPUnit_Framework_ExpectationFailedException`

```php
if ( !class_exists( 'PHPUnit_Framework_ExpectationFailedException', false ) )
{
    class_alias(
        \PHPUnit\Framework\ExpectationFailedException::class,
        'PHPUnit_Framework_ExpectationFailedException'
    );
}
```

### 2d. Shim für die statischen Konstanten von `PHPUnit_TextUI_TestRunner`

Alter Code des Toolkits verwendet die Konstanten `PHPUnit_TextUI_TestRunner::SUCCESS_EXIT`, `FAILURE_EXIT` und
`EXCEPTION_EXIT` sowie eine statische Methode `showError()`. Diese wurden per Shim nachgebildet:

```php
if ( !class_exists( 'PHPUnit_TextUI_TestRunner', false ) )
{
    class PHPUnit_TextUI_TestRunner
    {
        const SUCCESS_EXIT   = 0;
        const FAILURE_EXIT   = 1;
        const EXCEPTION_EXIT = 2;

        public static function showError( string $message ): void
        {
            fwrite( STDERR, $message . PHP_EOL );
        }
    }
}
```


<a name="fix-toolkit"></a>
## 3. Korrektur 2 — Toolkit: unzulässige Konstruktoren aus ezpregressiontest und ezpdatabaseregressiontest entfernen

Sowohl `tests/toolkit/ezpregressiontest.php` als auch `tests/toolkit/ezpdatabaseregressiontest.php` überschrieben
`__construct()` nur, um `$this->files` zu sortieren. PHPUnit 13 macht das fatal.

**Was der Konstruktor bisher tat:**

```php
// ezpregressiontest.php (before)
public function __construct( $name = NULL, array $data = array(), $dataName = '' )
{
    parent::__construct( $name, $data, $dataName );
    if ( self::SORT_MODE === 'mtime' )
        usort( $this->files, array( $this, 'sortTestsByMtime' ) );
    else
        usort( $this->files, array( $this, 'sortTestsByName' ) );
}
```

**Die Korrektur:** `__construct()` vollständig entfernen und die Sortierung in eine verzögerte Methode `getFiles()`
verschieben. Unterklassen, die `$this->files` bisher in ihren eigenen Konstruktoren vorbefüllt haben, überschreiben
jetzt stattdessen `getFiles()`.

```php
// ezpregressiontest.php (after)
final public function getFiles(): array
{
    if ( !empty( $this->files ) )
    {
        if ( self::SORT_MODE === 'mtime' )
            usort( $this->files, array( $this, 'sortTestsByMtime' ) );
        else
            usort( $this->files, array( $this, 'sortTestsByName' ) );
    }
    return $this->files;
}
```

Dieselbe Entfernung wurde in `ezpdatabaseregressiontest.php` genauso vorgenommen.

**Zusammenfassung des Diffs:**

```diff
# tests/toolkit/ezpregressiontest.php
-   public function __construct( $name = NULL, array $data = array(), $dataName = '' )
-   {
-       parent::__construct( $name, $data, $dataName );
-       if ( self::SORT_MODE === 'mtime' )
-           usort( $this->files, array( $this, 'sortTestsByMtime' ) );
-       else
-           usort( $this->files, array( $this, 'sortTestsByName' ) );
-   }
+   final public function getFiles(): array
+   {
+       if ( !empty( $this->files ) )
+       {
+           if ( self::SORT_MODE === 'mtime' )
+               usort( $this->files, array( $this, 'sortTestsByMtime' ) );
+           else
+               usort( $this->files, array( $this, 'sortTestsByName' ) );
+       }
+       return $this->files;
+   }
```


<a name="fix-lib-tests"></a>
## 4. Korrektur 3 — Testdateien in lib: `: void` hinzufügen und Konstruktoren entfernen

### 4a. Fehlende Rückgabetypen `: void` (38 Vorkommen in 22 Dateien)

PHPUnit 13 erzwingt die Verträglichkeit von Rückgabetypen streng. Jedes `setUp()` und `tearDown()`, das kein `: void`
deklarierte, verursachte einen fatalen Fehler beim Laden der Klasse.

**Korrigierte Dateien:**

| Datei | Korrigierte Methoden |
|---|---|
| `tests/tests/lib/ezdb/ezdbinterface_test.php` | `setUp` |
| `tests/tests/lib/ezdb/ezmysqldb_fk_test.php` | `setUp` |
| `tests/tests/lib/ezdb/ezpostgresqldb_test.php` | `setUp`, `tearDownAfterClass` |
| `tests/tests/lib/ezfile/ezdirinsideroot_test.php` | `setUp`, `tearDown` |
| `tests/tests/lib/ezfile/ezdiroutsideroot_test.php` | `setUp`, `tearDown` |
| `tests/tests/lib/ezfile/ezfiledownload_test.php` | `setUp` |
| `tests/tests/lib/ezfile/ezfilerename_test.php` | `setUp`, `tearDown` |
| `tests/tests/lib/ezimage/ezimagemanager_test.php` | `setUp`, `tearDown` |
| `tests/tests/lib/ezimage/ezimageshellhandler_test.php` | `setUp`, `tearDown` |
| `tests/tests/lib/ezlocale/ezlocale_regression.php` | `setUp`, `tearDown` |
| `tests/tests/lib/eztemplate/eztemplate_regression.php` | `setUp`, `tearDown` |
| `tests/tests/lib/eztemplate/eztemplateattributeoperator_test.php` | `setUp`, `tearDown` |
| `tests/tests/lib/eztemplate/eztemplatestringoperator_regression.php` | `setUp` |
| `tests/tests/lib/eztemplate/eztemplatetextoperator_regression.php` | `setUp`, `tearDown` |
| `tests/tests/lib/ezutils/ezdebug_regression.php` | `setUp`, `tearDown` |
| `tests/tests/lib/ezutils/ezmail_ezc_test.php` | `setUp`, `tearDown` |
| `tests/tests/lib/ezutils/ezmail_test.php` | `setUp`, `tearDown` |
| `tests/tests/lib/ezutils/ezoperationhandler_regression.php` | `setUp`, `tearDown` |
| `tests/tests/lib/ezutils/ezphpcreator_regression.php` | `setUp` |
| `tests/tests/lib/ezutils/ezsys_regressiontest.php` | `setUp`, `tearDown` |
| `tests/tests/lib/ezutils/ezuri_regression.php` | `setUp`, `tearDown` |
| `tests/tests/lib/ezutils/ezuri_test.php` | `setUp`, `tearDown` |

**Auf jede Datei angewendetes Muster:**

```diff
- public function setUp()
+ public function setUp(): void

- protected function setUp()
+ protected function setUp(): void

- public function tearDown()
+ public function tearDown(): void

- public static function tearDownAfterClass()
+ public static function tearDownAfterClass(): void
```

### 4b. Unzulässige Überschreibungen von `__construct()` in Testdateien von lib

Neun Testdateien in lib überschrieben `__construct()` nur, um `parent::__construct()` und `$this->setName(...)`
aufzurufen. In PHPUnit 13 ist `TestCase::__construct()` `final` — diese scheitern alle beim Laden fatal.

**Korrektur:** Entfernen Sie die Methode `__construct()` vollständig. Die Aufrufe von `setName()` dienten nur dazu,
Testsuiten in Berichten von PHPUnit 3.x zu beschriften; PHPUnit 13 leitet den Namen der Suite automatisch aus Klassen-
und Methodennamen ab.

Dateien mit entferntem Konstruktor:

- `tests/tests/lib/ezdb/ezpostgresqldb_test.php`
- `tests/tests/lib/ezfile/ezdirinsideroot_test.php`
- `tests/tests/lib/eztemplate/eztemplateattributeoperator_test.php`
- `tests/tests/lib/eztemplate/eztemplatetextoperator_regression.php`
- `tests/tests/lib/ezutils/ezini_test.php`
- `tests/tests/lib/ezutils/ezphpcreator_regression.php`
- `tests/tests/lib/ezutils/ezuri_test.php`

### 4c. `ezdiroutsideroot_test.php` — der Konstruktor initialisierte `$rootDir`

Der Konstruktor dieser Datei tat mehr als nur `setName()` — er setzte außerdem `$this->rootDir`. Die Korrektur war,
die Eigenschaft mit `null` zu initialisieren und sie verzögert in `setUp()` zuzuweisen:

```diff
- protected $rootDir;
-
- public function __construct()
- {
-     parent::__construct();
-     $this->rootDir = sys_get_temp_dir() . '/tests/';
-     $this->setName( "eZDirTestOutsideRoot" );
- }
-
  public function setUp(): void
  {
+     if ( $this->rootDir === null )
+         $this->rootDir = sys_get_temp_dir() . '/tests/';
      file_exists( $this->rootDir ) or ...
```

### 4d. `ezsys_regressiontest.php` — der Konstruktor befüllte `$this->files`

Der Konstruktor dieser Datei rief `readDirRecursively()` auf, um `$this->files` zu befüllen, bevor er
`parent::__construct()` aufrief. Da `ezpRegressionTest::getFiles()` jetzt verzögert arbeitet (siehe Abschnitt 3), ist
die richtige Korrektur, `getFiles()` in der Unterklasse zu überschreiben:

```php
public function getFiles(): array
{
    if ( empty( $this->files ) )
        $this->readDirRecursively( dirname( __FILE__ ) . '/server', $this->files, 'php' );
    return parent::getFiles();
}
```


<a name="verification"></a>
## 5. Befehle zur Überprüfung

Die vollständige Testsuite lib ausführen:

```bash
php vendor/bin/phpunit --configuration phpunit.xml --testsuite lib
```

Erwartete Ausgabe:

```
OK, but there were issues!
Tests: 92, Assertions: 337, PHPUnit Warnings: 17.
```

Die 17 Warnungen gab es bereits vorher (siehe Abschnitt 7). Alle 92 Tests bestehen.

Nur die neuen Tests des String-Operators ausführen:

```bash
php vendor/bin/phpunit tests/tests/lib/eztemplate/eZTemplateStringsOperatorTest.php
```

Erwartete Ausgabe:

```
OK (92 tests, 337 assertions)
```

Dreimal ausführen, um die Stabilität zu bestätigen:

```bash
for i in 1 2 3; do
    echo "=== Run $i ==="
    php vendor/bin/phpunit tests/tests/lib/eztemplate/eZTemplateStringsOperatorTest.php \
        2>&1 | tail -2
done
```


<a name="new-feature"></a>
## 6. Neue Funktion: eZTemplateStringsOperator und seine Testsuite

### 6a. Überblick

`lib/eztemplate/classes/eztemplatestringoperator.php` ist eine neue Klasse für eZ-Template-Operatoren, die jede unter
https://www.php.net/manual/en/ref.strings.php aufgeführte String-Funktion von PHP als vollwertigen
eZ-Template-Operator bereitstellt — mit zwei kreativen Ergänzungen.

Sie folgt genau demselben Architekturmuster wie das vorhandene `lib/eztemplate/classes/eztemplatearrayoperator.php`:

- Implementiert `eZTemplateOperatorInterface`
- Stellt `operatorList()`, `operatorTemplateHints()`, `modify()` und `namedParameterPerOperator()` /
  `namedParameterList()` bereit
- Jeder Operator bildet seinen Namen über einen sauberen `match`-Ausdruck mit voller Unterstützung benannter Parameter
  auf die entsprechende PHP-Funktion ab

### 6b. Implementierte Operatoren

Die folgenden Operatoren sind implementiert. Operatoren, die es bereits in Exponential 4.x gibt (siehe die Referenz
der Template-Operatoren von eZP4), sind bewusst ausgenommen, um Konflikte zu vermeiden.

| Template-Operator | PHP-Funktion | Hinweise |
|---|---|---|
| `chunk_split` | `chunk_split()` | Benannte Parameter: `chunklen`, `end` |
| `convert_uudecode` | `convert_uudecode()` | Unterdrückt die PHP-Warnung bei ungültiger Eingabe |
| `convert_uuencode` | `convert_uuencode()` | |
| `count_chars` | `count_chars()` | Benannter Parameter: `mode` (Standard 0) |
| `crc32` | `crc32()` | |
| `crypt` | `crypt()` | Benannter Parameter: `salt` |
| `hex2bin` | `hex2bin()` | Unterdrückt die PHP-Warnung bei ungültigem Hex |
| `levenshtein` | `levenshtein()` | Benannter Parameter: `str2` |
| `metaphone` | `metaphone()` | Benannter Parameter: `max_phonemes` |
| `money_format` | `money_format()` | In PHP 8 veraltet/entfernt; per Shim nachgebildet |
| `number_format` | `number_format()` | Benannte Parameter: `decimals`, `dec_point`, `thousands_sep` |
| `quoted_printable_decode` | `quoted_printable_decode()` | |
| `quoted_printable_encode` | `quoted_printable_encode()` | |
| `similar_text` | `similar_text()` | Benannter Parameter: `str2` |
| `soundex` | `soundex()` | |
| `sscanf` | `sscanf()` | Benannter Parameter: `format` |
| `str_contains` | `str_contains()` | Benannter Parameter: `needle` |
| `str_ends_with` | `str_ends_with()` | Benannter Parameter: `needle` |
| `str_getcsv` | `str_getcsv()` | Benannte Parameter: `separator`, `enclosure`; der Parameter escape fehlt (veraltet ab PHP 8.4) |
| `str_pad` | `str_pad()` | Benannte Parameter: `length`, `pad_string`, `pad_type` |
| `str_rot13` | `str_rot13()` | |
| `str_split` | `str_split()` | Benannter Parameter: `length` |
| `str_starts_with` | `str_starts_with()` | Benannter Parameter: `needle` |
| `str_word_count` | `str_word_count()` | Benannter Parameter: `format` |
| `strcasecmp` | `strcasecmp()` | Benannter Parameter: `str2` |
| `strchr` | `strchr()` | Benannter Parameter: `needle` |
| `strcmp` | `strcmp()` | Benannter Parameter: `str2` |
| `strcoll` | `strcoll()` | Benannter Parameter: `str2` |
| `strcspn` | `strcspn()` | Benannter Parameter: `characters` |
| `strip_tags` | `strip_tags()` | Benannter Parameter: `allowed_tags` |
| `stripcslashes` | `stripcslashes()` | Alias: `stripescapes` |
| `stripos` | `stripos()` | Benannte Parameter: `needle`, `offset` |
| `stristr` | `stristr()` | Benannter Parameter: `needle` |
| `strlen` | `strlen()` | |
| `strnatcasecmp` | `strnatcasecmp()` | Benannter Parameter: `str2` |
| `strnatcmp` | `strnatcmp()` | Benannter Parameter: `str2` |
| `strncasecmp` | `strncasecmp()` | Benannte Parameter: `str2`, `n` |
| `strncmp` | `strncmp()` | Benannte Parameter: `str2`, `n` |
| `strpbrk` | `strpbrk()` | Benannter Parameter: `characters` |
| `strpos` | `strpos()` | Benannte Parameter: `needle`, `offset` |
| `strrchr` | `strrchr()` | Benannter Parameter: `needle` |
| `strrev` | `strrev()` | |
| `strripos` | `strripos()` | Benannte Parameter: `needle`, `offset` |
| `strrpos` | `strrpos()` | Benannte Parameter: `needle`, `offset` |
| `strspn` | `strspn()` | Benannter Parameter: `characters` |
| `strstr` | `strstr()` | Benannter Parameter: `needle` |
| `strtok` | `strtok()` | Benannter Parameter: `token` |
| `substr_compare` | `substr_compare()` | Benannte Parameter: `str2`, `offset`, `length`, `case_insensitive` |
| `substr_count` | `substr_count()` | Benannter Parameter: `needle` |
| `substr_replace` | `substr_replace()` | Benannte Parameter: `replace`, `offset`, `length` |
| `wordwrap` | `wordwrap()` | Benannte Parameter: `width`, `break`, `cut_long_words` |
| `strtr` | `strtr()` | Benannter Parameter: `replace` — Übersetzung auf Zeichenebene |
| **`ristring`** | `str_replace()` | **Kreative Ergänzung** — rekursives Ersetzen in Zeichenketten mit Beachtung der Groß- und Kleinschreibung |
| **`rstring`** | `str_ireplace()` | **Kreative Ergänzung** — rekursives Ersetzen in Zeichenketten ohne Beachtung der Groß- und Kleinschreibung |

**Ausgenommen (bereits in den Kernoperatoren von eZP4):**

`addslashes`, `stripslashes`, `htmlspecialchars`, `htmlspecialchars_decode`,
`htmlentities`, `html_entity_decode`, `nl2br`, `number_format` (Kern),
`sprintf`, `printf`, `sscanf` (teilweise), `ltrim`, `rtrim`, `trim`,
`strtolower`, `strtoupper`, `ucfirst`, `ucwords`, `str_replace` (als `replace`),
`str_repeat`, `substr`, `md5`, `sha1`, `base64_encode`, `base64_decode`,
`urlencode`, `urldecode`, `rawurlencode`, `rawurldecode`, `chunk_split` (als
`wrap`), `wordwrap` (als `wrap`), `implode`, `explode`, `str_split`,
`preg_match`, `preg_replace`.

> Diese stehen bereits über `eZTemplateStringOperator` (den eingebauten von eZP4), `eZTemplateArrayOperator` und
> `eZTemplateTextOperator` zur Verfügung. Sie erneut hinzuzufügen würde `eZTemplate::registerOperator()` eine stille
> Warnung über das Überschreiben ausgeben lassen und könnte Fehler verdecken.

### 6c. Kreative Ergänzungen: `ristring` und `rstring`

#### `ristring` — str_replace mit Beachtung der Groß- und Kleinschreibung

```
{$haystack|ristring( 'old', 'new' )}
{$haystack|ristring( $search_array, $replace_array )}
```

Bildet direkt auf PHP `str_replace( $search, $replace, $input )` ab. Benannte Parameter: `search` (Pflicht),
`replace` (Pflicht). Gibt die Ergebniszeichenkette zurück oder die Zahl der Ersetzungen, wenn der benannte Parameter
`count` angegeben wird.

#### `rstring` — str_ireplace ohne Beachtung der Groß- und Kleinschreibung

```
{$haystack|rstring( 'OLD', 'new' )}
{$haystack|rstring( $search_array, $replace_array )}
```

Bildet direkt auf PHP `str_ireplace( $search, $replace, $input )` ab. Dieselbe Parametersignatur wie `ristring`. Ohne
Beachtung der Groß- und Kleinschreibung: `'OLD'`, `'old'` und `'Old'` passen alle.

**Begründung des Namens:** `r` = replace (ersetzen), `i` = insensitive (Groß- und Kleinschreibung), `string` =
Zeichenkettenoperation. Kurz und passend zur Namenskonvention der Operatoren in dieser Klasse.

### 6d. Anwendungsbeispiele in eZ-Templates

```
{* Case-sensitive replace *}
{"Hello World"|ristring( 'World', 'eZ' )}
{* → "Hello eZ" *}

{* Case-insensitive replace *}
{"Hello World"|rstring( 'WORLD', 'eZ' )}
{* → "Hello eZ" *}

{* String utilities *}
{"hello"|strlen}
{* → 5 *}

{"hello world"|str_word_count}
{* → 2 *}

{"aabbcc"|str_split( 2 )}
{* → ['aa','bb','cc'] *}

{1234.5678|number_format( 2, '.', ',' )}
{* → "1,234.57" *}
```

### 6e. Den Operator registrieren

Fügen Sie ihn zu `autoload/ezp_kernel.php` Ihrer Website hinzu (oder zum Autoload-Override Ihrer Erweiterung):

```php
$operatorArray = [
    // ... existing operators ...
    'eZTemplateStringsOperator' => 'lib/eztemplate/classes/eztemplatestringoperator.php',
];
```

Oder laden Sie ihn von Hand in Ihrem Bootstrap:

```php
require_once 'lib/eztemplate/classes/eztemplatestringoperator.php';
$tpl = eZTemplate::instance();
$tpl->registerOperator( new eZTemplateStringsOperator() );
```


<a name="warnings"></a>
## 7. Bereits vorhandene Warnungen — Dateiname und Klassenname passen nicht

Nach allen Korrekturen für PHPUnit 13 gibt `--testsuite lib` weiterhin 17 Warnungen dieser Form aus:

```
Class ezdbinterface_test cannot be found in .../ezdbinterface_test.php
```

**Ursache:** Der `TestSuiteLoader` von PHPUnit 13 erwartet, dass der Dateiname exakt zum Klassennamen passt (mit
Beachtung der Groß- und Kleinschreibung). Die alte eZ-Konvention waren durchgehend kleingeschriebene Dateinamen
(`ezdbinterface_test.php`) mit Klassen in CamelCase (`eZDBInterfaceTest`). PHPUnit 13 warnt, wenn es den Dateinamen
über seinen Suchalgorithmus nicht der Klasse zuordnen kann.

**Auswirkung:** Keine — alle 92 Tests laufen und bestehen. Die Tests werden über den Mechanismus `suite.php` korrekt
geladen. Die Warnung ist nur ein Hinweis.

**Künftige Lösung:** Benennen Sie die betroffenen Dateien passend zu ihren Klassennamen um (zum Beispiel
`eZDBInterfaceTest.php`). Das ist eine eigene Migration und beeinflusst die Korrektheit der Tests nicht.

**Dateien, die Warnungen erzeugen** (schon vorher vorhanden, nicht durch dieses Release eingeführt):

`ezdbinterface_test.php`, `ezmysqldb_fk_test.php`, `ezpostgresqldb_test.php`,
`ezdirinsideroot_test.php`, `ezdiroutsideroot_test.php`, `ezfiledownload_test.php`,
`ezfilerename_test.php`, `ezimagemanager_test.php`, `ezimageshellhandler_test.php`,
`ezsoapclient_test.php`, `eztemplateattributeoperator_test.php`,
`ezhttptool_test.php`, `ezini_test.php`, `ezmail_ezc_test.php`, `ezmail_test.php`,
`ezsys_test.php`, `ezuri_test.php`


<a name="files-changed"></a>
## 8. In diesem Release geänderte Dateien

### Neue Dateien

| Datei | Beschreibung |
|---|---|
| `lib/eztemplate/classes/eztemplatestringoperator.php` | Neu: mehr als 60 String-Funktionen von PHP als eZ-Template-Operatoren |
| `tests/tests/lib/eztemplate/eZTemplateStringsOperatorTest.php` | Neu: Testsuite für PHPUnit 13 (92 Tests, 337 Assertions) |
| `doc/eng-us/bc/6.0/phpunitv13.md` | Dieses Dokument (im englischen Original) |

### Geänderte Dateien — Toolkit

| Datei | Änderung |
|---|---|
| `tests/bootstrap.php` | Shims für `PHPUnit_Framework_Warning`, `PHPUnit_Framework_ExpectationFailedException`, `PHPUnit_TextUI_TestRunner` hinzugefügt; Hinweise zu PHPUnit 13 dokumentiert |
| `tests/toolkit/ezpregressiontest.php` | `__construct()` entfernt, verzögertes `getFiles()` hinzugefügt |
| `tests/toolkit/ezpdatabaseregressiontest.php` | `__construct()` entfernt, verzögertes `getFiles()` hinzugefügt |

### Geänderte Dateien — Tests in lib (`: void` bei setUp/tearDown und Entfernen von Konstruktoren)

`tests/tests/lib/ezdb/ezdbinterface_test.php`,
`tests/tests/lib/ezdb/ezmysqldb_fk_test.php`,
`tests/tests/lib/ezdb/ezpostgresqldb_test.php`,
`tests/tests/lib/ezfile/ezdirinsideroot_test.php`,
`tests/tests/lib/ezfile/ezdiroutsideroot_test.php`,
`tests/tests/lib/ezfile/ezfiledownload_test.php`,
`tests/tests/lib/ezfile/ezfilerename_test.php`,
`tests/tests/lib/ezimage/ezimagemanager_test.php`,
`tests/tests/lib/ezimage/ezimageshellhandler_test.php`,
`tests/tests/lib/ezlocale/ezlocale_regression.php`,
`tests/tests/lib/eztemplate/eztemplate_regression.php`,
`tests/tests/lib/eztemplate/eztemplateattributeoperator_test.php`,
`tests/tests/lib/eztemplate/eztemplatestringoperator_regression.php`,
`tests/tests/lib/eztemplate/eztemplatetextoperator_regression.php`,
`tests/tests/lib/ezutils/ezdebug_regression.php`,
`tests/tests/lib/ezutils/ezmail_ezc_test.php`,
`tests/tests/lib/ezutils/ezmail_test.php`,
`tests/tests/lib/ezutils/ezoperationhandler_regression.php`,
`tests/tests/lib/ezutils/ezphpcreator_regression.php`,
`tests/tests/lib/ezutils/ezsys_regressiontest.php`,
`tests/tests/lib/ezutils/ezuri_regression.php`,
`tests/tests/lib/ezutils/ezuri_test.php`



## Verwandte Seiten

- [Unterstützung für PHP 8](php8.md)
- [Bereinigung der Testsuite für PHPUnit 13 / PHP 8.4.23](phpunitv13-fuer-php841.md)
- [`ezpSessionHandlerDB`: Korrekturen für PHP 8 und die Testsuite für PHPUnit 13](ezpsessionhandlerdb-php8-korrektur-und-tests.md)
- [PHP 8.4 und 8.5 für den älteren Symfony-Stack: die Framework-Forks](../../../eng-us/features/6.0/platform-php85-framework-forks.md)
- [April 2025](../../../eng-us/history/2025/2025-04.md)
- [September 2025](../../../eng-us/history/2025/2025-09.md)
- [Dezember 2025](../../../eng-us/history/2025/2025-12.md)
- [Februar 2026](../../../eng-us/history/2026/2026-02.md)
- [März 2026](../../../eng-us/history/2026/2026-03.md)
- [Migration auf PHPUnit 10](phpunitv10.md)
