# `ezpSessionHandlerDB` PHP 8 compatibility bugfixes and PHPUnit 13 test suite

Read this page if your installation stores sessions in the database (`ezpSessionHandlerDB`) and runs on
PHP 8, if your `ezsession` table keeps growing, or if you saw blank pages with "Cannot call session save handler in a
recursive manner". Three bugs in `ezpSessionHandlerDB` caused silent session failures under PHP 8 and stopped garbage
collection from ever removing expired sessions. All three are fixed (6.0.x, 2026-04-08; released in 6.0.13) and
covered by a PHPUnit 13 test suite that runs without a live database or the Exponential kernel.

## In short

| | |
|---|---|
| What changed | `read()` returns `''` instead of `false`; `gc()` compares with `time()`; the `gc()` timeout guard measures the real elapsed time; `setSaveHandler()` no longer calls `session_module_name( 'user' )` (6.0.15). |
| Who is affected | Every installation with database sessions on PHP 8.0 or newer. |
| How to check | Count expired rows: `SELECT COUNT(*) FROM ezsession WHERE expiration_time < UNIX_TIMESTAMP();` (MySQL). Before the fix this number only grew. |
| How to fix | Update. Expired rows left from before are removed by the next garbage collection run. |

Affected class: `lib/ezsession/classes/ezpsessionhandlerdb.php`. Requires PHP 8.0 or newer; tests use PHPUnit 13.0.0.

## Bug 1: `read()` returned `false` instead of `''`

### What broke

`SessionHandlerInterface::read()` must return a **string** on every code path — an empty
string `''` signals "no session data", while `false` signals a fatal handler error. In
PHP 7 the distinction was not enforced. PHP 8 made it strict: returning `false` causes
PHP to emit:

```
Cannot call session save handler in a recursive manner
```

and then immediately attempt to restart the session, resulting in an infinite loop or a
blank page.

### Affected paths

| Path | Old return | Fixed return |
|------|-----------|--------------|
| DB not connected | `return false` | `return ''` |
| No matching session row | `return false` | `return ''` |

### What changed

```php
// Before
if ( !$db->isConnected() )
    return false;

// After
if ( !$db->isConnected() )
    return '';          // PHP 8: '' = no data, false = fatal error
```


## Bug 2: `gc()` `WHERE` clause used `$maxLifeTime` (duration) not `time()` (timestamp)

### What broke

PHP's automatic garbage collector calls `gc( $maxLifeTime )` where `$maxLifeTime` is the
value of `session.gc_maxlifetime` — a **duration in seconds** (e.g. `1440`).

`ezsession.expiration_time` is stored as an **absolute Unix timestamp**
(`time() + SessionTimeout`, typically `~1744000000` in 2026). The query:

```sql
DELETE FROM ezsession WHERE expiration_time < 1440
```

never matches any row, so **expired sessions were never deleted**.

### What changed

Both the iterating and non-iterating code paths now use `time()`:

```php
// Before
WHERE expiration_time < $maxLifeTime      -- always false: 1744xxxxxx < 1440

// After
WHERE expiration_time < ' . time() . '   -- correct: absolute timestamp comparison
```


## Bug 3: `gc()` timeout guard used `$maxLifeTime` as a start timestamp

### What broke

The iterating path checks remaining execution time after each batch to avoid hitting
the HTTP server timeout. The guard was:

```php
$remaningTime = $maxExecutionTime - GC_TIMEOUT_MARGIN - ( $stopTime - $maxLifeTime );
```

`$maxLifeTime` (e.g. `1440`) was subtracted as if it were a Unix timestamp. The actual
elapsed time therefore evaluated to roughly `time() − 1440 ≈ 1 744 000 000 seconds` —
a nonsensical value orders of magnitude larger than `$maxExecutionTime`. The timeout
guard fired immediately after the **first** batch, so the iterating GC always stopped
after one batch even when ample execution time remained.

### What changed

A `$gcStartTime` variable is captured once before the loop, and the guard uses that:

```php
// Before
$remaningTime = $maxExecutionTime - GC_TIMEOUT_MARGIN - ( $stopTime - $maxLifeTime );

// After
$gcStartTime = time();   // captured before the do-while loop
// ... inside loop ...
$remaningTime = $maxExecutionTime - GC_TIMEOUT_MARGIN - ( $stopTime - $gcStartTime );
```


## Bug 4: `setSaveHandler()` called `session_module_name( 'user' )`

Fixed on 5 October 2026, in the 6.0.15 line.

### What broke

`ezpSessionHandler::setSaveHandler()` (`lib/ezsession/classes/ezpsessionhandler.php`) selected the user module
before it registered the callbacks:

```php
session_module_name( 'user' );
session_set_save_handler( ... );
```

Since PHP 8.0, `session_module_name()` refuses the name `user` with
`ValueError: session_module_name(): Argument #1 ($module) cannot be "user"`. The error stopped every request of an
installation with `[Session] Handler=ezpSessionHandlerDB`, and of every handler of an extension that inherits
`setSaveHandler()`. The default `ezpSessionHandlerPHP` and `ezpSessionHandlerSymfony` have their own
`setSaveHandler()` and were not affected.

### What changed

The call is gone. `session_set_save_handler()` selects the user module by itself, so the handler is registered as
before.

PHP 8.4 still reports a deprecation for `session_set_save_handler()` with single callbacks instead of an object that
implements `SessionHandlerInterface`. It is a notice, not an error, and is left for a change of its own.

## PHPUnit 13 test suite

A new test file covers all four bugs without a live database or the Exponential kernel:

```
tests/tests/lib/ezsession/EzpSessionHandlerDBPhp8BugfixesTest.php
```

Hand-rolled stubs replace `eZDB`, `eZINI`, `eZSession`, and `ezpEvent` so the class
loads in pure unit-test mode. The suite uses `createStub()` throughout (PHPUnit 13
preferred API — no `getMockBuilder()` notices).

### Test list

| Test | Bug verified |
|------|-------------|
| `testReadReturnsEmptyStringWhenDatabaseNotConnected` | Bug 1 |
| `testReadReturnsEmptyStringWhenNoSessionRowFound` | Bug 1 |
| `testReadReturnsSessionDataWhenRowFound` | Bug 1 regression guard |
| `testReadReturnTypeIsAlwaysString` | Bug 1 |
| `testGcIteratingPathUsesCurrentTimestampNotMaxLifetime` | Bug 2 |
| `testGcNonIteratingPathUsesCurrentTimestampNotMaxLifetime` | Bug 2 |
| `testGcDoesNotUseDurationLiteralInWhereClause` | Bug 2 |
| `testGcDoesNotPrematurelyTimeOutWithReasonableMaxLifetime` | Bug 3 |
| `testGcTimeoutGuardUsesElapsedTimeNotMaxLifetime` | Bug 3 |
| `testGcNonIteratingPathReturnsTrue` | Regression guard |
| `testGcIteratingPathReturnsTrueWhenNoExpiredSessions` | Regression guard |
| `testHandlerImplementsRequiredMethods` | API contract guard |
| `testSetSaveHandlerRegistersTheUserModule` | Bug 4 |

### Run

```bash
php vendor/phpunit/phpunit/phpunit \
    tests/tests/lib/ezsession/EzpSessionHandlerDBPhp8BugfixesTest.php \
    --no-coverage
```

Expected output:

```
PHPUnit 13.0.0 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.5.5

.............                                   13 / 13 (100%)

OK (13 tests, 30 assertions)
```

## Related pages

- [PHP 8 support](php8.md)
- [PHPUnit 10 upgrade](phpunitv10.md), [PHPUnit 13 upgrade](phpunitv13.md) and
  [PHPUnit 13 on PHP 8.4](phpunitv13forPHP841.md)
- [PHP 8.4 and 8.5 for the older Symfony stack: the framework forks](../../features/6.0/platform-php85-framework-forks.md)
- History: [April 2025](../../history/2025/2025-04.md), [September 2025](../../history/2025/2025-09.md),
  [December 2025](../../history/2025/2025-12.md), [February 2026](../../history/2026/2026-02.md),
  [March 2026](../../history/2026/2026-03.md)
