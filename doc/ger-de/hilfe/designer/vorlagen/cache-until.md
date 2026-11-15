---
title: cache_until und seconds_until
audience: designer, developer
category: templates
order: 135
summary: Die Operatoren cache_until und seconds_until (seit 6.0.15) - welche Werte sie annehmen, wie Zeitangaben in der Zeitzone der Website gelesen werden, warum der früheste Aufruf gewinnt, was cache_until( 0 ) bewirkt, welche Werte ignoriert werden, wo die Operatoren wirken und wie Sie das Ergebnis prüfen.
keywords: cache_until, seconds_until, Ablaufzeit, Ablauf einer Seite, zeitabhängige Inhalte, Mitternacht, tomorrow, cache_ttl, Zeitzone, TimeZone, X-Exp-Cache-Until, View-Cache, Cache-Block, HTTP-Cache, expiry, page expiry, time-dependent content, time zone
type: help
---
# cache_until und seconds_until

Diese Seite ist für Designer und Entwickler, deren Templates etwas zeigen, das zu einem bestimmten Zeitpunkt nicht
mehr stimmt: den nächsten Termin, den Beitrag des Tages, „heute geöffnet“, einen Countdown. Mit `cache_until` sagt ein
Template, **bis wann** seine Ausgabe gilt, und jeder Cache der Seite hält sie höchstens bis dahin. `seconds_until`
rechnet die Sekunden bis zu einem Zeitpunkt aus. Beide Operatoren gibt es seit Exponential 6.0.15. Implementierung:
`kernel/common/expcacheuntiloperator.php` und `kernel/classes/expcacheuntil.php`.

Die Anleitung mit vielen vollständigen Beispielen: [Zeitabhängige Inhalte cachen](../../../anleitungen/zeitabhaengige-inhalte-cachen.md).
Die Funktion im Überblick: [cache_until: Seiten, die pünktlich ablaufen](../../../funktionen/6.0/cache-until.md).

## Inhalt

1. [Syntax](#syntax)
2. [Welche Werte cache_until annimmt](#welche-werte-cache_until-annimmt)
3. [Zeitangaben und die Zeitzone](#zeitangaben-und-die-zeitzone)
4. [Der früheste Aufruf gewinnt](#der-früheste-aufruf-gewinnt)
5. [Wo die Operatoren wirken](#wo-die-operatoren-wirken)
6. [seconds_until](#seconds_until)
7. [Smarty- und Twig-Templates](#smarty--und-twig-templates)
8. [cache_ttl und cache_until](#cache_ttl-und-cache_until)
9. [Prüfen](#prüfen)
10. [Fehler](#fehler)

## Syntax

```tpl
{cache_until( 'tomorrow' )}                                 {* Mitternacht in der Zeitzone der Website *}
{cache_until( 'next hour' )}
{cache_until( '+15 minutes' )}
{cache_until( 'next monday' )}
{cache_until( '2026-12-24 18:00' )}
{cache_until( $event.data_map.start.content.timestamp )}    {* ein Unix-Zeitstempel *}
{cache_until( 0 )}                                          {* diese Seite in keinem Cache behalten *}
{$timestamp|cache_until}                                    {* dasselbe, als Pipe geschrieben *}

{def $secs = 'tomorrow'|seconds_until}                      {* ganze Sekunden bis dahin *}
{def $secs = seconds_until( 'tomorrow' )}
```

`cache_until` gibt nichts aus (der Operator liefert eine leere Zeichenkette). Sie können ihn also an jeder Stelle des
Templates aufrufen, auch mitten im Markup.

## Welche Werte cache_until annimmt

| Wert | Bedeutung |
|---|---|
| eine Ganzzahl, eine Zeichenkette aus Ziffern, ein `DateTime`-Objekt | ein Unix-Zeitstempel: die Seite gilt bis zu diesem Zeitpunkt |
| eine andere Zeichenkette | ein Datum oder eine relative Zeitangabe, gelesen wie von PHPs `strtotime()` bzw. `DateTime::modify()`: `'tomorrow'`, `'next hour'`, `'+15 minutes'`, `'next monday'`, `'2026-12-24 18:00'` |
| `0`, `false`, `'0'` | die Seite in **keinem** Cache behalten (dasselbe wie `cache_ttl` = 0) |
| ein Zeitpunkt in der Vergangenheit (Zeitstempel oder festes Datum) | wie `0`: die Seite wird nicht behalten; die Debug-Ausgabe enthält einen Hinweis (Notice) |
| `null` (eine fehlende Variable), `''`, `true`, ein Array, eine Zeichenkette, die keine Zeit ist | **ignoriert**: kein Effekt, eine Warnung in der Debug-Ausgabe |

> **Achtung**
> Eine Zahl ist **immer** ein Unix-Zeitstempel, nie eine Dauer. `{cache_until( 3600 )}` bedeutet eine Stunde nach dem
> 1. Januar 1970, liegt also in der Vergangenheit, und die Seite wird gar nicht behalten. Für eine Lebensdauer
> schreiben Sie `{cache_until( '+3600 seconds' )}`; der Hinweis in der Debug-Ausgabe sagt dasselbe.

Weil eine fehlende Variable ignoriert wird, prüfen Sie einen Wert, der fehlen kann, vorher:

```tpl
{if $next_event}
    {cache_until( $next_event.data_map.start.content.timestamp )}
{/if}
```

## Zeitangaben und die Zeitzone

Zeichenketten werden in der Zeitzone der Website gelesen: `site.ini [TimeZoneSettings] TimeZone`, und wenn sie leer
ist, die Standardzeitzone von PHP (`date.timezone`). `'tomorrow'` ist also Mitternacht in Berlin, wenn die Website
`TimeZone=Europe/Berlin` hat, und Mitternacht in der Zeitzone des Servers, wenn sie keine eigene hat. Wie Sie die
Zeitzone einrichten: [Zeitabhängige Inhalte cachen, Zeitzone einrichten](../../../anleitungen/zeitabhaengige-inhalte-cachen.md#13-zeitzone-einrichten).

Eine Tageszeit, die heute schon vorbei ist, bedeutet ihr **nächstes** Vorkommen:

| Aufruf | um | ergibt |
|---|---|---|
| `'midnight'`, `'today'` | jederzeit | die nächste Mitternacht |
| `'noon'` | 15:00 | morgen 12:00 |
| `'18:00'` | 19:30 | morgen 18:00 |
| `'18:00'` | 10:00 | heute 18:00 |
| `'monday'` | Montagabend | der nächste Montag |

Nicht weitergerollt werden Angaben, die rückwärts gehen (`'-5 minutes'`, `'yesterday'`, `'last friday'`,
`'2 hours ago'`), und feste Daten (`'2026-12-24 18:00'`): Liegen sie in der Vergangenheit, wird die Seite nicht
behalten.

An Tagen mit Zeitumstellung gelten die Regeln der Zeitzone: `'tomorrow'` ist die tatsächliche lokale Mitternacht,
auch wenn der Tag 23 oder 25 Stunden hat.

## Der früheste Aufruf gewinnt

Jeder Aufruf kann den Ablauf der Seite nur **vorziehen**, nie hinausschieben: Die Seite gilt bis zum frühesten
Zeitpunkt, den irgendein Template der Anfrage genannt hat.

```tpl
{cache_until( 'tomorrow' )}        {* bis Mitternacht ...                 *}
{cache_until( '+15 minutes' )}     {* ... nein, nur 15 Minuten: gilt      *}
{cache_until( 'next monday' )}     {* später als 15 Minuten: keine Wirkung *}
```

So kann jedes Template für sich sagen, was es weiß, ohne die anderen zu kennen: Die Vollansicht nennt den Beginn des
nächsten Termins, die Fußzeile das Jahresende, der Block im Kopf das Ende eines Hinweises.

## Wo die Operatoren wirken

`cache_until` meldet seinen Zeitpunkt an die **Anfrage**, nicht über eine Template-Variable. Er wirkt deshalb überall,
wo ein Template läuft:

- in der Inhaltsansicht (Vollansicht, Zeilenansicht und so weiter) und in jedem `{include}` darin;
- im Pagelayout, in Menüs, im Kopf und in der Fußzeile;
- in Blöcken von Exponential Layouts, die im Pagelayout gerendert werden;
- im Inhalt eines `{cache-block}`.

Beide Operatoren werden ausgewertet, wenn das Template **läuft**, auch in kompilierten Templates: Ein konstanter Wert
wie `'tomorrow'` wird nicht beim Kompilieren festgeschrieben.

Was mit dem Zeitpunkt geschieht, in Kurzform (Einzelheiten: [die Funktionsseite](../../../funktionen/6.0/cache-until.md)):

| Cache | Wirkung |
|---|---|
| View-Cache (Inhaltsansicht) | Der Eintrag läuft zum Zeitpunkt ab. Wird er später wieder ausgeliefert, meldet er seinen Zeitpunkt erneut, sodass die Caches danach nur die **verbleibende** Zeit bekommen. |
| Template-Block-Cache (`{cache-block}`) | Der Block läuft zum Zeitpunkt ab (zusätzlich zu `expiry=`) und meldet seinen Zeitpunkt der Seite erneut, wenn er aus dem Cache kommt. `cache_until( 0 )` im Block: Der Block wird nicht gespeichert. |
| Rollenbewusster HTTP-Cache | Der Eintrag lebt höchstens bis zum Zeitpunkt, ohne stale-while-revalidate danach. |
| Response-Header | `max-age` und `s-maxage` von `Cache-Control` werden auf die verbleibenden Sekunden gesenkt, `Expires` ebenso; dazu `X-Exp-Cache-Until`. |
| Antwort-Cache von Velocity, CDN, Browser, LiteSpeed | folgen den gesenkten Headern (LiteSpeed: siehe die Funktionsseite) |
| Statischer Cache | **nicht** berücksichtigt: Nehmen Sie solche Seiten aus dem statischen Cache aus. |

Eine Seite ohne Aufruf ist unverändert, Byte für Byte.

## seconds_until

`seconds_until` liest seinen Wert genau wie `cache_until` und gibt die ganzen Sekunden von jetzt bis zu diesem
Zeitpunkt zurück. Es setzt **keinen** Ablauf.

| Wert | Ergebnis |
|---|---|
| ein Zeitpunkt in der Zukunft | die Sekunden bis dahin |
| ein Zeitpunkt in der Vergangenheit, `0`, `false` | `0` |
| keine Zeit (`null`, `''`, Text ohne Zeit) | `0`, mit einer Warnung in der Debug-Ausgabe |

```tpl
{def $secs = '2026-12-24 18:00'|seconds_until}
{if $secs|gt( 0 )}
    <p>Noch {div( $secs, 86400 )|floor} Tage bis Heiligabend.</p>
    {* die Zahl der Tage ändert sich, wenn ein weiterer ganzer Tag vergangen ist *}
    {cache_until( sum( currentdate(), mod( $secs, 86400 ), 1 ) )}
{/if}
```

Eine Zahl aus `seconds_until`, die in eine gecachte Seite geschrieben wird, veraltet mit der Seite. Setzen Sie
deshalb immer auch `cache_until` auf den Zeitpunkt, zu dem sich die angezeigte Zahl ändert, oder geben Sie einem
Skript im Browser den festen Zielzeitpunkt statt der Sekunden.

## Smarty- und Twig-Templates

Beide Operatoren sind wie jeder Template-Operator für Smarty- (`.stpl`) und Twig-Templates überbrückt; sie benutzen
dieselbe Anfrage und wirken dort genauso. In einem `.stpl` als Funktion oder als Modifier:

```smarty
{cache_until('tomorrow')}
{$event_start|cache_until}
```

Wie Operatoren in diesen Templates aufgerufen werden:
[Smarty-Templates: Operatoren und Fetch-Funktionen](../../../anleitungen/smarty-templates-operatoren.md) und
[Twig-Templates: Operator-Referenz](../../../anleitungen/twig-templates-operator-referenz.md). Der Cache-Block von
Smarty (`{expcacheblock}`) und Twig (`{% expcacheblock %}`) nutzt denselben Template-Block-Cache und berücksichtigt den Zeitpunkt ebenso.

## cache_ttl und cache_until

Die alte Schreibweise funktioniert weiter:

```tpl
{set-block scope=root variable=cache_ttl}400{/set-block}
```

Beim Erzeugen der Ansicht wird daraus `cache_until( time() + 400 )`, und `cache_ttl` = 0 ist `cache_until( 0 )`. Es
ist keine Änderung an Einstellungen nötig. `cache_ttl` wirkt aber nur in der Inhaltsansicht, `cache_until` überall.
Stellen Sie deshalb um, zum Beispiel:

```tpl
{* vorher *}
{set-block scope=root variable=cache_ttl}{$sekunden_bis_mitternacht}{/set-block}

{* nachher *}
{cache_until( 'tomorrow' )}
```

Siehe auch [Set-block und append-block](set-block-und-append-block.md).

## Prüfen

- **Header**: Jede Seite mit einem Ablauf trägt `X-Exp-Cache-Until`, den Zeitpunkt nach ISO 8601 in der Zeitzone der
  Website (`2026-11-16T00:00:00+01:00`) oder `0`. Mit eingeschalteter Debug-Ausgabe folgt, was ihn gesetzt hat:
  `; source="design/standard/templates/…"`, `cache-block`, `view cache: …` (die Ansicht kam aus dem View-Cache; dahinter
  steht, was ihren Ablauf gesetzt hatte) oder `cache_ttl=400`.

  ```bash
  curl -sI https://example.com/veranstaltungen | grep -iE 'cache-control|expires|x-exp-cache'
  ```

- **Debug-Leiste**, Reiter **Rendering**: das Feld „Until HH:MM“ (der Ablaufzeitpunkt) oder „Not cached“ (die Seite
  wird nicht behalten). Der Tooltip nennt den Zeitpunkt, die verbleibenden Sekunden, was ihn gesetzt hat und die Zahl
  der Aufrufe ([Die Exp-Debug-Leiste](../../../funktionen/6.0/exp-debug-leiste.md)).
- **Debug-Ausgabe**: eine Notice `cache_until: kept until <Zeitpunkt> (<s> s), set by <Quelle>, N calls` oder
  `… the page is not kept`, Warnungen für ignorierte Werte, Hinweise für Zeitpunkte in der Vergangenheit.

## Fehler

- **Eine Dauer als Zahl**: `cache_until( 3600 )` ist das Jahr 1970; die Seite wird nie behalten. Schreiben Sie
  `'+3600 seconds'` oder `'+1 hour'`.
- **Eine fehlende Variable**: `cache_until( $next_event.data_map.start.content.timestamp )` ohne nächsten Termin wird
  ignoriert, die Seite bleibt bis zur nächsten Veröffentlichung im Cache. Prüfen Sie mit `{if}`.
- **Ein Termin, der schon begonnen hat**: Sein Beginn liegt in der Vergangenheit, also wird die Seite nicht behalten.
  Nennen Sie den Beginn des nächsten **künftigen** Termins (oder sein Ende, wenn die Seite dann wechselt).
- **Keine Zeitzone**: Ohne `[TimeZoneSettings] TimeZone` ist `'tomorrow'` Mitternacht in der Zeitzone von PHP, oft UTC.
- **Der statische Cache**: Er kennt keinen Ablauf; eine Seite mit `cache_until` bleibt dort bis zur nächsten
  Veröffentlichung.

## Verwandte Seiten

- [Zeitabhängige Inhalte cachen](../../../anleitungen/zeitabhaengige-inhalte-cachen.md)
- [cache_until: Seiten, die pünktlich ablaufen](../../../funktionen/6.0/cache-until.md)
- [Aktualisierung auf 6.0.15: cache_until](../../../bc/6.0/cache-until.md)
- [Cache-Block](cache-block.md)
- [Set-block und append-block](set-block-und-append-block.md)
- [Template-Kompilierung und Cache](template-kompilierung-und-cache.md)
- [Operatorreferenz](operatorreferenz.md)
- [Caching für Designer](../theme-design/caching-fuer-designer.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
