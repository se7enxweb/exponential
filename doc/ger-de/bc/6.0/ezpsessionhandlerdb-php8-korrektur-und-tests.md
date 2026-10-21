# `ezpSessionHandlerDB`: Korrekturen für die Kompatibilität mit PHP 8 und eine Testsuite für PHPUnit 13

Lesen Sie diese Seite, wenn Ihre Installation Sitzungen in der Datenbank speichert (`ezpSessionHandlerDB`) und unter
PHP 8 läuft, wenn Ihre Tabelle `ezsession` immer weiter wächst oder wenn Sie leere Seiten mit „Cannot call session
save handler in a recursive manner“ gesehen haben. Drei Fehler in `ezpSessionHandlerDB` verursachten unter PHP 8
stille Fehler bei Sitzungen und verhinderten, dass die Garbage Collection abgelaufene Sitzungen je entfernte. Alle drei
sind behoben (6.0.x, 2026-04-08; veröffentlicht in 6.0.13) und durch eine Testsuite für PHPUnit 13 abgedeckt, die ohne
laufende Datenbank und ohne den Kernel von Exponential läuft.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | `read()` gibt `''` statt `false` zurück; `gc()` vergleicht mit `time()`; der Timeout-Schutz von `gc()` misst die tatsächlich vergangene Zeit; `setSaveHandler()` ruft `session_module_name( 'user' )` nicht mehr auf (6.0.15). |
| Wer betroffen ist | Jede Installation mit Sitzungen in der Datenbank unter PHP 8.0 oder neuer. |
| Wie Sie es prüfen | Zählen Sie abgelaufene Zeilen: `SELECT COUNT(*) FROM ezsession WHERE expiration_time < UNIX_TIMESTAMP();` (MySQL). Vor der Korrektur wuchs diese Zahl nur. |
| Wie Sie es beheben | Aktualisieren. Von früher übrig gebliebene abgelaufene Zeilen entfernt der nächste Lauf der Garbage Collection. |

Betroffene Klasse: `lib/ezsession/classes/ezpsessionhandlerdb.php`. Erfordert PHP 8.0 oder neuer; die Tests verwenden
PHPUnit 13.0.0.

## Fehler 1: `read()` gab `false` statt `''` zurück

### Was kaputtging

`SessionHandlerInterface::read()` muss auf jedem Codepfad eine **Zeichenkette** zurückgeben — eine leere Zeichenkette
`''` bedeutet „keine Sitzungsdaten“, während `false` einen fatalen Fehler des Handlers bedeutet. Unter PHP 7 wurde die
Unterscheidung nicht durchgesetzt. PHP 8 ist darin streng: Die Rückgabe von `false` lässt PHP Folgendes ausgeben:

```
Cannot call session save handler in a recursive manner
```

und dann sofort versuchen, die Sitzung neu zu starten, was zu einer Endlosschleife oder einer leeren Seite führt.

### Betroffene Pfade

| Pfad | Alte Rückgabe | Korrigierte Rückgabe |
|------|-----------|--------------|
| Datenbank nicht verbunden | `return false` | `return ''` |
| Keine passende Sitzungszeile | `return false` | `return ''` |

### Was sich geändert hat

```php
// Before
if ( !$db->isConnected() )
    return false;

// After
if ( !$db->isConnected() )
    return '';          // PHP 8: '' = no data, false = fatal error
```


## Fehler 2: Die `WHERE`-Klausel von `gc()` verwendete `$maxLifeTime` (eine Dauer) statt `time()` (einen Zeitstempel)

### Was kaputtging

Die automatische Garbage Collection von PHP ruft `gc( $maxLifeTime )` auf, wobei `$maxLifeTime` der Wert von
`session.gc_maxlifetime` ist — eine **Dauer in Sekunden** (z. B. `1440`).

`ezsession.expiration_time` wird als **absoluter Unix-Zeitstempel** gespeichert (`time() + SessionTimeout`, 2026
typischerweise `~1744000000`). Die Abfrage:

```sql
DELETE FROM ezsession WHERE expiration_time < 1440
```

trifft nie auf eine Zeile, daher wurden **abgelaufene Sitzungen nie gelöscht**.

### Was sich geändert hat

Beide Codepfade, der iterierende und der nicht iterierende, verwenden jetzt `time()`:

```php
// Before
WHERE expiration_time < $maxLifeTime      -- always false: 1744xxxxxx < 1440

// After
WHERE expiration_time < ' . time() . '   -- correct: absolute timestamp comparison
```


## Fehler 3: Der Timeout-Schutz von `gc()` verwendete `$maxLifeTime` als Startzeitstempel

### Was kaputtging

Der iterierende Pfad prüft nach jedem Durchgang die verbleibende Ausführungszeit, um den Timeout des HTTP-Servers
nicht zu erreichen. Der Schutz lautete:

```php
$remaningTime = $maxExecutionTime - GC_TIMEOUT_MARGIN - ( $stopTime - $maxLifeTime );
```

`$maxLifeTime` (z. B. `1440`) wurde abgezogen, als wäre es ein Unix-Zeitstempel. Die tatsächlich vergangene Zeit ergab
daher ungefähr `time() − 1440 ≈ 1 744 000 000 Sekunden` — ein unsinniger Wert, um Größenordnungen größer als
`$maxExecutionTime`. Der Timeout-Schutz griff sofort nach dem **ersten** Durchgang, sodass die iterierende Garbage
Collection immer nach einem Durchgang anhielt, auch wenn reichlich Ausführungszeit übrig war.

### Was sich geändert hat

Eine Variable `$gcStartTime` wird einmal vor der Schleife erfasst, und der Schutz verwendet sie:

```php
// Before
$remaningTime = $maxExecutionTime - GC_TIMEOUT_MARGIN - ( $stopTime - $maxLifeTime );

// After
$gcStartTime = time();   // captured before the do-while loop
// ... inside loop ...
$remaningTime = $maxExecutionTime - GC_TIMEOUT_MARGIN - ( $stopTime - $gcStartTime );
```


## Fehler 4: `setSaveHandler()` rief `session_module_name( 'user' )` auf

Behoben am 5. Oktober 2026, in der Linie 6.0.15.

### Was kaputtging

`ezpSessionHandler::setSaveHandler()` (`lib/ezsession/classes/ezpsessionhandler.php`) wählte das Modul user aus, bevor
es die Callbacks registrierte:

```php
session_module_name( 'user' );
session_set_save_handler( ... );
```

Seit PHP 8.0 lehnt `session_module_name()` den Namen `user` ab, mit
`ValueError: session_module_name(): Argument #1 ($module) cannot be "user"`. Der Fehler beendete jede Anfrage einer
Installation mit `[Session] Handler=ezpSessionHandlerDB` und jedes Handlers einer Erweiterung, der `setSaveHandler()`
erbt. Die Standard-Handler `ezpSessionHandlerPHP` und `ezpSessionHandlerSymfony` haben ein eigenes `setSaveHandler()`
und waren nicht betroffen.

### Was sich geändert hat

Der Aufruf ist entfernt. `session_set_save_handler()` wählt das Modul user selbst aus, daher wird der Handler wie
bisher registriert.

PHP 8.4 meldete weiterhin eine Veraltungswarnung für `session_set_save_handler()` mit einzelnen Callbacks statt eines
Objekts, das `SessionHandlerInterface` implementiert; Fehler 5 unten beseitigt sie.

## Fehler 5: `setSaveHandler()` übergab PHP sechs einzelne Callbacks

### Was kaputtging

Seit PHP 8.4 protokollierte jede Anfrage einer Installation mit `[Session] Handler=ezpSessionHandlerDB` (oder mit
einem Handler einer Erweiterung, der `setSaveHandler()` erbt):

```
session_set_save_handler(): Providing individual callbacks instead of an object implementing SessionHandlerInterface is deprecated
```

### Was sich geändert hat

`setSaveHandler()` übergibt PHP jetzt ein Objekt: `ezpSessionHandlerAdapter`
(`lib/ezsession/classes/ezpsessionhandleradapter.php`) implementiert `SessionHandlerInterface` und reicht `open`,
`close`, `read`, `write`, `destroy` und `gc` unverändert an den Handler weiter. Daher gilt:

- **Handler bleiben, wie sie sind.** Ein Handler einer Erweiterung, der `ezpSessionHandler` erweitert, behält seine
  Methoden; darin muss sich nichts ändern. Der Adapter steht jedem Code zur Verfügung, der einen Handler selbst
  registriert: `session_set_save_handler( new ezpSessionHandlerAdapter( $handler ), false )`.
- **Es wird keine Shutdown-Funktion registriert** (`false`), genau wie zuvor mit den Callbacks: eZSession und die
  persistenten Worker von Exponential Velocity schreiben und schließen die Sitzung selbst.
- **PHP 8.0 bis 8.5:** Die Objektform funktioniert in jeder unterstützten Version. Die Methoden des Adapters tragen
  `#[\ReturnTypeWillChange]` statt Rückgabetypen (die Schnittstelle hat seit PHP 8.1 vorläufige Rückgabetypen, und der
  Codestil von Exponential kennt keine Rückgabetypen).

`ezpSessionHandlerPHP` und `ezpSessionHandlerSymfony` haben ein eigenes `setSaveHandler()` und sind unverändert.

Tests: `tests/tests/lib/ezsession/EzpSessionHandlerAdapterTest.php` (jeder Aufruf wird unverändert weitergereicht, der
Adapter ist ein `SessionHandlerInterface`, `setSaveHandler()` löst keine Veraltungswarnung aus und wählt das Modul user).

## Testsuite für PHPUnit 13

Eine neue Testdatei deckt alle vier Fehler ohne laufende Datenbank und ohne den Kernel von Exponential ab:

```
tests/tests/lib/ezsession/EzpSessionHandlerDBPhp8BugfixesTest.php
```

Von Hand geschriebene Stubs ersetzen `eZDB`, `eZINI`, `eZSession` und `ezpEvent`, sodass die Klasse in reinem
Unit-Test-Modus lädt. Die Suite verwendet durchgehend `createStub()` (die in PHPUnit 13 bevorzugte API — keine
Hinweise zu `getMockBuilder()`).

### Liste der Tests

| Test | Geprüfter Fehler |
|------|-------------|
| `testReadReturnsEmptyStringWhenDatabaseNotConnected` | Fehler 1 |
| `testReadReturnsEmptyStringWhenNoSessionRowFound` | Fehler 1 |
| `testReadReturnsSessionDataWhenRowFound` | Regressionsschutz zu Fehler 1 |
| `testReadReturnTypeIsAlwaysString` | Fehler 1 |
| `testGcIteratingPathUsesCurrentTimestampNotMaxLifetime` | Fehler 2 |
| `testGcNonIteratingPathUsesCurrentTimestampNotMaxLifetime` | Fehler 2 |
| `testGcDoesNotUseDurationLiteralInWhereClause` | Fehler 2 |
| `testGcDoesNotPrematurelyTimeOutWithReasonableMaxLifetime` | Fehler 3 |
| `testGcTimeoutGuardUsesElapsedTimeNotMaxLifetime` | Fehler 3 |
| `testGcNonIteratingPathReturnsTrue` | Regressionsschutz |
| `testGcIteratingPathReturnsTrueWhenNoExpiredSessions` | Regressionsschutz |
| `testHandlerImplementsRequiredMethods` | Schutz des API-Vertrags |
| `testSetSaveHandlerRegistersTheUserModule` | Fehler 4 |

### Ausführen

```bash
php vendor/phpunit/phpunit/phpunit \
    tests/tests/lib/ezsession/EzpSessionHandlerDBPhp8BugfixesTest.php \
    --no-coverage
```

Erwartete Ausgabe:

```
PHPUnit 13.0.0 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.5.5

.............                                   13 / 13 (100%)

OK (13 tests, 30 assertions)
```

## Verwandte Seiten

- [Unterstützung für PHP 8](php8.md)
- [Upgrade auf PHPUnit 10](phpunitv10.md), [Upgrade auf PHPUnit 13](phpunitv13.md)
  und [PHPUnit 13 unter PHP 8.4](phpunitv13-fuer-php841.md)
- [PHP 8.4 und 8.5 für den älteren Symfony-Stack: die Framework-Forks](../../funktionen/6.0/plattform-php85-framework-forks.md)
- Geschichte: [April 2025](../../geschichte/2025/2025-04.md), [September 2025](../../geschichte/2025/2025-09.md),
  [Dezember 2025](../../geschichte/2025/2025-12.md), [Februar 2026](../../geschichte/2026/2026-02.md),
  [März 2026](../../geschichte/2026/2026-03.md)
