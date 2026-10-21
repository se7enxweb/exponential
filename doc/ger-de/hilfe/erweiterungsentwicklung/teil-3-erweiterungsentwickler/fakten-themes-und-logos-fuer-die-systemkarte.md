---
title: Fakten, Themes und Logos für die Systemkarte
audience: developer, app-creator
category: extension-development
order: 215
summary: Wie eine Erweiterung eigene Fakten zu ./console exp:expfetch, exp:info und exp:about hinzufügt - eine Provider-Klasse (expSysCardProvider, expSysCardBaseProvider), syscard.ini ProviderClasses[], ein Test mit einem gefälschten Kontext, ein Theme-Block und ein Logo-Verzeichnis.
keywords: Systemkarte, Provider, Fakten, Hinweise, Theme, Logo, Erweiterung, Test, system card, exp:expfetch, exp:info, exp:about, expSysCardProvider, expSysCardBaseProvider, expSysCardContext, expSysCardFact, syscard.ini, ProviderClasses, provider, theme, logo, extension
type: help
---
# Fakten, Themes und Logos für die Systemkarte

Diese Seite ist für Erweiterungsentwickler. `./console exp:expfetch` (sowie `exp:info` und `exp:about`) zeichnet eine
Karte mit Fakten, die von **Providern** stammen, einer Klasse pro Gruppe von Fakten, die in `syscard.ini` aufgeführt
sind. Ihre Erweiterung kann einen Provider für ihre eigenen Fakten und Hinweise, ein Farbschema und ein Logo
hinzufügen, und zwar nur mit ihren eigenen Dateien. Referenz:
[Die Systemkarte](../../../../eng-us/features/6.0/system-card.md).

## 1. Die Provider-Klasse

Erweitern Sie `expSysCardBaseProvider`. Lesen Sie alles über den `$context` (INI-Werte, Dateien, Umgebung, Programme,
und `$context->get( $key, $closure )` für alles andere): Ein Test kann dem Provider dann erfundene Werte übergeben.

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

`fact( $name, $label, $value, $severity, $hint )` erzeugt eine Zeile; der Schweregrad ist `OK`, `INFO`, `WARN` oder
`ERROR`, und ein Hinweis fügt dem Hinweisblock der Karte eine Zeile hinzu. `text()` erzeugt einen Absatz. Die
Hilfsfunktionen `size()`, `bytes()`, `duration()` und `ago()` formatieren Werte.

## 2. Den Provider registrieren

```ini
<?php /* #?ini charset="utf-8"?
# extension/mybooking/settings/syscard.ini.append.php
[Providers]
ProviderClasses[]=myBookingSysCardProvider

[Sections]
Titles[bookings]=Bookings
*/ ?>
```

Erzeugen Sie die Autoloads neu (`php bin/php/ezpgenerateautoloads.php -e`) und prüfen Sie:

```bash
./console exp:expfetch --list-providers
./console exp:expfetch --section=bookings --plain
```

Ein Administrator kann ihn mit `Enabled[mybooking]=disabled` ausschalten oder `Priority[mybooking]` und
`Budget[mybooking]` ändern, ohne Ihren Code anzufassen.

## 3. Ohne Datenbank testen

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

Ein gefälschter Kontext antwortet nur aus seinen Werten: Die Closures laufen nie, auf dem Rechner wird nichts gelesen.

## 4. Regeln

- **Keine Geheimnisse.** Geben Sie nie ein Passwort, einen Schlüssel, ein Token, einen Benutzernamen oder eine
  vollständige Adresse zurück. Die Registry verwirft Fakten, deren Namen nach Geheimnissen aussehen, und maskiert
  `user:password@` und `password=...` in Werten, aber verlassen Sie sich nicht darauf.
- **Bleiben Sie schnell.** Halten Sie das Zeitbudget ein; durchlaufen Sie Verzeichnisse und starten Sie Programme über
  den Kontext, der bei Ablauf der Frist abbricht. Mit eingeschalteter Isolation wird ein Provider, der zu lange
  braucht, beendet und zeigt `n/a (timeout)`.
- **Nur lesend.** Ein Provider ändert nie etwas. Wenn er cacht, schreibt er nur als Eigentümer des Cache-Verzeichnisses.
- **Benennung.** Nennen Sie die Klasse `<prefix>SysCardProvider` oder ähnlich; verwenden Sie in Namen nicht das Wort
  „fetch“ (es ist bereits belegt durch Template-Fetch-Funktionen, Fetch-Definitionen von Modulen, Funktionssammlungen
  und die Fetch-Methoden persistenter Objekte).

## 5. Ein Theme und ein Logo

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

Logo-Zeichnungen sind Textdateien: `<name>-big.txt`, `<name>-small.txt`, `<name>-mini.txt` (Unicode) und dieselben mit
`.ascii.txt`. `[Logo] Logo=<name>` wählt sie aus; eine Datei, die wie eine des Kernels heißt (`e-big.txt`), ersetzt
diese.

## Verwandte Seiten

- [Die Systemkarte](../../../../eng-us/features/6.0/system-card.md)
- [CLI-Skripte und Konsolenbefehle](cli-skripte-und-console-befehle.md)
- [Tests für Erweiterungen](tests-fuer-erweiterungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
