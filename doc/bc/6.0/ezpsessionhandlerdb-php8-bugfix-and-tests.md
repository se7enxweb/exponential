# `ezpSessionHandlerDB` PHP 8 compatibility bugfixes and PHPUnit 13 test suite

**Affected class:** `lib/ezsession/classes/ezpsessionhandlerdb.php`  
**Fixed in:** Exponential 6.0.x HEAD (2026-04-08)  
**PHP version:** 8.0+  
**PHPUnit version:** 13.0.0  

---

## The short answer

Three bugs in `ezpSessionHandlerDB` caused silent session failures under PHP 8 and
prevented garbage collection from ever removing expired sessions. All three are now
fixed and covered by a PHPUnit 13 test suite that runs without a live database or eZ
kernel.

---

## Bug 1 — `read()` returned `false` instead of `''`

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

---

## Bug 2 — `gc()` `WHERE` clause used `$maxLifeTime` (duration) not `time()` (timestamp)

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

---

## Bug 3 — `gc()` timeout guard used `$maxLifeTime` as a start timestamp

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

---

## PHPUnit 13 test suite

A new test file covers all three bugs without requiring a live database or eZ kernel:

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

............                                    12 / 12 (100%)

OK (12 tests, 28 assertions)
```
