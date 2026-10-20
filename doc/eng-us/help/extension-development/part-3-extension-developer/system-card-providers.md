---
title: Facts, themes and logos for the system card
audience: developer, app-creator
category: extension-development
order: 215
summary: How an extension adds its own facts to ./console exp:expfetch, exp:info and exp:about - a provider class (expSysCardProvider, expSysCardBaseProvider), syscard.ini ProviderClasses[], a test with a fake context, a theme block and a logo directory.
keywords: system card, exp:expfetch, exp:info, exp:about, expSysCardProvider, expSysCardBaseProvider, expSysCardContext, expSysCardFact, syscard.ini, ProviderClasses, provider, theme, logo, extension
type: help
---
# Facts, themes and logos for the system card

This page is for extension developers. `./console exp:expfetch` (and `exp:info`, `exp:about`) draws a card of
facts from **providers**, one class per group of facts, listed in `syscard.ini`. Your extension can add a provider
for its own facts and hints, a color theme and a logo, with nothing but its own files. Reference:
[The system card](../../../features/6.0/system-card.md).

## 1. The provider class

Extend `expSysCardBaseProvider`. Read everything through the `$context` (INI values, files, environment, programs,
and `$context->get( $key, $closure )` for anything else): a test can then hand the provider made-up values.

```php
<?php
// extension/mybooking/classes/mybookingsyscardprovider.php

/**
 * The booking engine on the system card: pending bookings, and a hint when the calendar sync is old.
 */
class myBookingSysCardProvider extends expSysCardBaseProvider
{
    protected $id = 'mybooking';                                // its key in Enabled[], Priority[], Budget[]
    protected $section = 'bookings';                            // a section of its own
    protected $modes = array( expSysCardContext::MODE_CARD );   // MODE_INFO: only on exp:info
    protected $priority = 30;                                   // the higher, the earlier it runs
    protected $budget = 1.0;                                    // seconds; n/a when it takes longer

    public function collect( expSysCardContext $context )
    {
        $pending = $context->get( 'mybooking.pending', function () {
            $rows = eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS n FROM mybooking_booking WHERE state = 0' );
            return isset( $rows[0]['n'] ) ? (int)$rows[0]['n'] : null;
        } );
        $facts = array( $this->fact( 'pending', 'Pending', $pending === null ? 'n/a' : $pending . ' bookings' ) );

        $synced = (int)$context->get( 'mybooking.synced', function () {
            return (int)@filemtime( eZSys::cacheDirectory() . '/mybooking/last-sync' );
        }, 0 );
        $old = $synced === 0 || $context->now - $synced > 86400;
        $facts[] = $this->fact( 'sync', 'Calendar sync', $synced === 0 ? 'never' : self::ago( $synced, $context->now ),
                                $old ? expSysCardFact::WARN : expSysCardFact::OK,
                                $old ? 'The calendar has not synced for a day: check the mybooking cronjob part.' : '' );
        return $facts;
    }
}
```

`fact( $name, $label, $value, $severity, $hint )` makes a row; the severity is `OK`, `INFO`, `WARN` or `ERROR`, and a
hint adds a line to the card's Hints block. `text()` makes a paragraph. The helpers `size()`, `bytes()`,
`duration()` and `ago()` format values.

## 2. Register it

```ini
<?php /* #?ini charset="utf-8"?
# extension/mybooking/settings/syscard.ini.append.php
[Providers]
ProviderClasses[]=myBookingSysCardProvider

[Sections]
Titles[bookings]=Bookings
*/ ?>
```

Regenerate the autoloads (`php bin/php/ezpgenerateautoloads.php -e`) and check:

```bash
./console exp:expfetch --list-providers
./console exp:expfetch --section=bookings --plain
```

An administrator can switch it off with `Enabled[mybooking]=disabled` or change `Priority[mybooking]` and
`Budget[mybooking]`, without touching your code.

## 3. Test it without a database

```php
class myBookingSysCardProviderTest extends PHPUnit\Framework\TestCase
{
    public function testOldSyncIsAWarning()
    {
        $context = expSysCardContext::fake( array(
            'mybooking.pending' => 3,
            'mybooking.synced' => 1792483200 - 3 * 86400,
            'now' => 1792483200,
        ) );
        $facts = ( new myBookingSysCardProvider() )->collect( $context );
        $this->assertSame( '3 bookings', $facts[0]->value );
        $this->assertSame( expSysCardFact::WARN, $facts[1]->severity );
    }
}
```

A fake context answers only from its values: the closures never run, nothing on the machine is read.

## 4. Rules

- **No secrets.** Never return a password, key, token, user name or full address. The registry drops facts named like
  secrets and masks `user:password@` and `password=...` in values, but do not rely on it.
- **Stay fast.** Keep within the budget; walk directories and run programs through the context, which stops at the
  deadline. With isolation on, a provider that takes too long is killed and shows `n/a (timeout)`.
- **Read-only.** A provider never changes anything. If it caches, it writes only as the owner of the cache directory.
- **Naming.** Call the class `<prefix>SysCardProvider` or similar; do not use "fetch" in names (the word is taken by
  template fetch functions, module fetch definitions, function collections and persistent-object fetch methods).

## 5. A theme and a logo

```ini
# extension/mybooking/settings/syscard.ini.append.php
[Theme]
Themes[]=mybooking

[Theme-mybooking]
Logo=#0EA5E9
LogoShade=#0369A1
Logo16=bright-cyan
Accent=#0EA5E9
Label=#7DD3FC
Gradient[]
Gradient[]=#7DD3FC
Gradient[]=#0369A1

[Logo]
Directories[]=extension/mybooking/share/syscard/logos
```

Logo drawings are text files: `<name>-big.txt`, `<name>-small.txt`, `<name>-mini.txt` (Unicode) and the same with
`.ascii.txt`. `[Logo] Logo=<name>` picks them; a file named like the kernel's (`e-big.txt`) replaces that one.

## Related pages

- [The system card](../../../features/6.0/system-card.md)
- [CLI scripts and console commands](cli-scripts-and-console-commands.md)
- [Tests for extensions](tests-for-extensions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
