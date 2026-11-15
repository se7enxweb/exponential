# Aktualisierung auf 6.0.15: cache_until

Exponential 6.0.15 bringt den Template-Operator `cache_until`: Ein Template sagt, bis wann seine Ausgabe gilt, und
jeder Cache der Seite hält sie höchstens bis dahin. Diese Seite sagt, was sich für eine bestehende Installation
ändert, die auf die Linie 6.0.15 wechselt, was Sie selbst tun sollten (vor allem: eine Zeitzone setzen) und welche
Einstellung neu ist.

Referenz der Funktion: [cache_until: Seiten, die pünktlich ablaufen](../../funktionen/6.0/cache-until.md) ·
Template-Referenz: [cache_until und seconds_until](../../hilfe/designer/vorlagen/cache-until.md) · Anleitung:
[Zeitabhängige Inhalte cachen](../../anleitungen/zeitabhaengige-inhalte-cachen.md)

## Kurz gesagt

- **Seiten ohne `cache_until` und ohne `cache_ttl` sind unverändert**, Byte für Byte, auch ihre Header.
- **`cache_ttl` funktioniert weiter** und wird beim Erzeugen der Ansicht zu `cache_until( time() + cache_ttl )`.
  Keine Template- und keine Einstellungsänderung ist nötig.
- **Behoben:** Der HTTP-Cache gab einer aus dem View-Cache ausgelieferten Seite ihr ursprüngliches `cache_ttl` als
  neue Lebensdauer; jetzt bekommt sie die verbleibende Zeit.
- **Kleine Verhaltensänderungen** für Seiten mit `cache_ttl` oder `cache_until`: kein stale-while-revalidate im
  HTTP-Cache nach dem Ablauf, `s-maxage` in `Cache-Control`, wenn die verbleibende Zeit unter Velocitys `DefaultTtl`
  liegt, auch `s-maxage=0` für `cache_ttl` = 0.
- **Setzen Sie eine Zeitzone** (`site.ini [TimeZoneSettings] TimeZone`), bevor Templates `'tomorrow'` verwenden.
  Bestehende Websites werden dabei nicht automatisch geändert.
- **Neue Einstellung** `site.ini [CacheUntilSettings] LiteSpeedMaxAge` (Standard `disabled`), nur für Websites hinter
  dem Seiten-Cache von LiteSpeed.
- **Neuer Response-Header** `X-Exp-Cache-Until` auf Seiten mit einem Ablauf.

## Inhalt

1. [Was sich ändert](#was-sich-ändert)
2. [Die Zeitzone bestehender Websites](#die-zeitzone-bestehender-websites)
3. [Die Einstellung LiteSpeedMaxAge](#die-einstellung-litespeedmaxage)
4. [Templates umstellen](#templates-umstellen)
5. [Was Sie nach der Aktualisierung prüfen](#was-sie-nach-der-aktualisierung-prüfen)

## Was sich ändert

| Bereich | Vor 6.0.15 | Ab 6.0.15 |
|---|---|---|
| View-Cache mit `cache_ttl` | Ablauf Dateizeit plus `cache_ttl` | Ablauf als absoluter Zeitpunkt im Eintrag (`cache_expires`); Einträge von vorher laufen weiter zur Dateizeit plus `cache_ttl` ab |
| Ansicht aus dem View-Cache, danach im HTTP-Cache | lebte dort noch einmal `cache_ttl` Sekunden | lebt dort nur noch die verbleibende Zeit (**Fehlerbehebung**) |
| HTTP-Cache, Seite mit Ablauf | stale-while-revalidate nach dem Ablauf | kein stale-while-revalidate nach dem Ablauf |
| HTTP-Cache, Antwort aus dem Cache (`HIT`) | `max-age=300` | `max-age` = der kleinere Wert aus 300 und der verbleibenden Zeit (anonyme Besucher) |
| `Cache-Control` einer Seite mit Ablauf | unverändert | `max-age`/`s-maxage` auf die verbleibende Zeit gesenkt; `s-maxage=<verbleibend>` ergänzt, wenn die verbleibende Zeit 0 ist oder unter `velocity.ini [CacheSettings] DefaultTtl` liegt |
| Seite mit `cache_ttl` = 0 unter Velocity | Velocity behielt sie `DefaultTtl` Sekunden | `s-maxage=0`: Velocity behält sie nicht |
| `Expires` einer Seite mit Ablauf | unverändert | gesenkt, wenn er später liegt (der Standard aus dem Jahr 1997 bleibt) |
| LiteSpeed, Seite mit Ablauf 0 | kein Header | `X-LiteSpeed-Cache-Control: no-cache` |
| Template-Block-Dateien | Text | unverändert für Blöcke ohne `cache_until`; Blöcke mit Aufruf beginnen mit einer internen Markierung. Alte Dateien werden weiter gelesen. |

Spätere Schichten überschreiben wie bisher: Eine Seite mit dem Debug-Bericht wird `private, no-store` gesendet, eine
Seite mit einem Formular-Token `private, no-cache`.

Der statische Cache ist nicht betroffen; er berücksichtigt den Ablauf nicht. Nehmen Sie Seiten, die `cache_until`
verwenden, dort aus (nicht in `staticcache.ini [CacheSettings] CachedURLArray[]`).

## Die Zeitzone bestehender Websites

`cache_until( 'tomorrow' )` meint Mitternacht in der Zeitzone der Website: `site.ini [TimeZoneSettings] TimeZone`,
und wenn sie leer ist, `date.timezone` aus der `php.ini`. Neue Installationen schreiben diese Einstellung jetzt selbst
(aus `exp:install --timezone=<Zone>`, `kickstart.ini [language_options] TimeZone=<Zone>`, sonst aus dem Land der
Hauptsprache, sonst aus der `php.ini`, sonst UTC).

Bestehende Websites werden **nicht** geändert, denn eine andere Zeitzone verschiebt jedes Datum, das die Website zeigt,
und die zeitgesteuerte Veröffentlichung. **Setup > Systeminformationen** zeigt einen Hinweis („The site has no time
zone of its own“), wenn `TimeZone` leer ist und PHP eine `date.timezone` hat, und wie bisher eine Warnung, wenn keines
von beiden gesetzt ist.

Setzen Sie die Zone ausdrücklich, in `settings/override/site.ini.append.php`:

```ini
[TimeZoneSettings]
TimeZone=Europe/Berlin
```

Dann:

```bash
./console exp:velocity deploy --allow-root-user
```

oder leeren Sie den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`) und laden Sie PHP-FPM neu.
Wenn die Website bisher mit der Zone von PHP lief und diese schon die gewünschte ist, ändert sich durch das
ausdrückliche Setzen nichts an den angezeigten Daten; es schützt nur vor einem späteren Wechsel des Servers.

## Die Einstellung LiteSpeedMaxAge

```ini
[CacheUntilSettings]
LiteSpeedMaxAge=disabled
```

Eine Seite, die nicht behalten werden darf (`cache_until( 0 )`), bekommt immer `X-LiteSpeed-Cache-Control: no-cache`;
das hält eine Seite nur aus dem Seiten-Cache von LiteSpeed heraus. Mit `enabled` bekommt die Seite eines anonymen
Besuchers mit einem Ablauf zusätzlich `X-LiteSpeed-Cache-Control: max-age=<verbleibende Sekunden>`, sodass LiteSpeed
sie nicht länger hält, auch wenn er das `Cache-Control` der Antwort ignoriert (die Voreinstellung vieler Hoster).
LiteSpeed liest diesen Header bei jeder Antwort, auch bei einer, die seine Rewrite-Regeln nicht cachen würden. Schalten
Sie ihn deshalb nur ein, wenn die Seiten mit `cache_until` zu denen gehören, die Ihr LiteSpeed-Block cacht. Siehe
[Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../anleitungen/seiten-cache-mit-litespeed.md#wie-er-zu-den-caches-von-exponential-steht).

## Templates umstellen

Nötig ist nichts. Empfohlen, weil `cache_until` überall wirkt und einen Zeitpunkt statt einer Dauer nennt:

| Vorher | Nachher |
|---|---|
| `{set-block scope=root variable=cache_ttl}{$sekunden_bis_mitternacht}{/set-block}` | `{cache_until( 'tomorrow' )}` |
| `{set-block scope=root variable=cache_ttl}N{/set-block}` | `{cache_until( '+N seconds' )}` oder der tatsächliche Zeitpunkt des Wechsels |
| `{set-block scope=root variable=cache_ttl}0{/set-block}` | `{cache_until( 0 )}` |
| `{cache-block expiry=…}` nur wegen eines zeitabhängigen Inhalts | `{cache_until( … )}` im Inhalt des Blocks, ohne `expiry=` |

Schreiben Sie nie eine Dauer als Zahl (`cache_until( 3600 )`): Eine Zahl ist ein Unix-Zeitstempel, 3600 also das Jahr
1970, und die Seite wird gar nicht behalten. Beispiele für jeden Fall:
[Zeitabhängige Inhalte cachen](../../anleitungen/zeitabhaengige-inhalte-cachen.md).

## Was Sie nach der Aktualisierung prüfen

- Eine Seite ohne `cache_until` und `cache_ttl`: dieselben Header wie vorher, kein `X-Exp-Cache-Until`.
- Eine Vollansicht mit `cache_ttl`: `X-Exp-Cache-Until` nennt den Zeitpunkt der Erzeugung plus `cache_ttl`; mit
  Debug-Ausgabe `source="cache_ttl=N"`, nach einem Treffer im View-Cache `source="view cache: cache_ttl=N"`.
- Mit eingeschaltetem HTTP-Cache: Eine solche Seite wird zum Ablauf ein `MISS (expired (cache_until))`, nicht erst nach
  `MaxAge`.
- Setup > Systeminformationen: kein Hinweis zur Zeitzone mehr, nachdem `TimeZone` gesetzt ist.
- `X-Exp-Cache-Until` einer Seite mit `{cache_until( 'tomorrow' )}` endet mit dem Versatz der gewünschten Zone.
