# cache_until: Seiten, die pünktlich ablaufen

Lesen Sie diese Seite, wenn Sie wissen möchten, wie Exponential 6.0.15 eine Seite genau so lange cacht, wie ihr
Inhalt stimmt: Ein Template sagt mit `{cache_until( 'tomorrow' )}`, `{cache_until( $event_start )}` oder
`{cache_until( 0 )}`, bis wann seine Ausgabe gilt, und jeder Cache der Seite hält sie höchstens bis dahin, vom
View-Cache über den Template-Block-Cache und den rollenbewussten HTTP-Cache bis zu den Response-Headern für
Velocity, CDN, LiteSpeed und Browser. Die Seite beschreibt jede Schicht, die Entscheidungen dahinter, die Ausgabe
zur Fehlersuche und die Rückwärtskompatibilität.

Verwandt: [cache_until und seconds_until](../../hilfe/designer/vorlagen/cache-until.md) (Template-Referenz),
[Zeitabhängige Inhalte cachen](../../anleitungen/zeitabhaengige-inhalte-cachen.md) (Anleitung mit Beispielen),
[Aktualisierung auf 6.0.15: cache_until](../../bc/6.0/cache-until.md) (Hinweise zur Aktualisierung),
[Cache-Schichten](../../hilfe/hosting/leistung/cache-schichten.md) (alle Caches einer Website).

## Kurz gesagt

- **Ein Operator, jeder Cache.** `{cache_until( <Zeitpunkt> )}` setzt den Ablauf der Seite, die gerade gerendert
  wird. Der View-Cache, der Template-Block-Cache, der rollenbewusste HTTP-Cache, der Antwort-Cache von Velocity, ein
  CDN, LiteSpeed und der Browser halten die Seite nicht länger. Es ist keine Einstellung nötig.
- **Überall aufrufbar.** Der Operator meldet an die Anfrage, nicht über eine Template-Variable: in der
  Inhaltsansicht, in Includes, im Pagelayout, in Menüs und Fußzeilen, in Blöcken von Exponential Layouts und im
  Inhalt eines `{cache-block}`.
- **Der früheste Zeitpunkt gewinnt.** Jeder Aufruf kann den Ablauf nur vorziehen.
- **Zeitangaben in der Zeitzone der Website** (`site.ini [TimeZoneSettings] TimeZone`, sonst die von PHP). Eine
  Tageszeit, die heute vorbei ist, meint ihr nächstes Vorkommen: `'18:00'` um 19:30 ist morgen 18:00.
- **`cache_until( 0 )`** hält die Seite aus jedem Cache heraus, wie `cache_ttl` = 0.
- **`seconds_until`** rechnet die Sekunden bis zu einem Zeitpunkt aus, für Countdowns und eigene Rechnungen.
- **Eine Seite ohne Aufruf ist unverändert**, Byte für Byte. `cache_ttl` funktioniert weiter und wird intern zu
  `cache_until`.
- **Ein Fehler ist behoben:** Der HTTP-Cache gab einer aus dem View-Cache wieder ausgelieferten Seite ihr
  ursprüngliches `cache_ttl` als neue Lebensdauer. Jetzt bekommt jede Schicht nur noch die verbleibende Zeit.
- **Neue Installationen bekommen eine Zeitzone**, bestehende werden nicht verändert.
- Nicht berücksichtigt: der **statische Cache** (seine Dateien haben keine Lebensdauer).

## Inhalt

1. [Das Problem](#das-problem)
2. [Der Operator](#der-operator)
3. [Wie der Zeitpunkt gelesen wird](#wie-der-zeitpunkt-gelesen-wird)
4. [Der View-Cache](#der-view-cache)
5. [Der Template-Block-Cache](#der-template-block-cache)
6. [Der rollenbewusste HTTP-Cache](#der-rollenbewusste-http-cache)
7. [Die Response-Header](#die-response-header)
8. [Velocity, CDN, LiteSpeed und Browser](#velocity-cdn-litespeed-und-browser)
9. [Der statische Cache](#der-statische-cache)
10. [Die Ausgabe zur Fehlersuche](#die-ausgabe-zur-fehlersuche)
11. [Rückwärtskompatibilität](#rückwärtskompatibilität)
12. [Die Zeitzone bei der Installation](#die-zeitzone-bei-der-installation)
13. [Entscheidungen](#entscheidungen)
14. [Getestet](#getestet)

## Das Problem

Eine Seite mit „Nächster Termin: Freitag, 19 Uhr“ ist ab Freitag, 19 Uhr falsch, auch wenn niemand etwas
veröffentlicht hat. Bis 6.0.14 hatte ein Template dafür nur `{set-block scope=root variable=cache_ttl}N{/set-block}`:
Das wirkte nur in der Inhaltsansicht, nicht im Pagelayout, in einem Menü oder einem Cache-Block, nannte eine Dauer
statt eines Zeitpunkts, und die Caches nach dem View-Cache sahen nur diese Dauer. Eine Ansicht, die um 9 Uhr mit
`cache_ttl` = 54000 (bis Mitternacht) erzeugt und um 23 Uhr aus dem View-Cache ausgeliefert wurde, lebte im HTTP-Cache
noch bis etwa 14 Uhr am nächsten Tag.

## Der Operator

```tpl
{cache_until( 'tomorrow' )}                                 Mitternacht in der Zeitzone der Website
{cache_until( 'next hour' )}  {cache_until( '+15 minutes' )}  {cache_until( 'next monday' )}
{cache_until( '2026-12-24 18:00' )}
{cache_until( $event.data_map.start.content.timestamp )}    ein Unix-Zeitstempel
{cache_until( 0 )}                                          in keinem Cache behalten (auch false, '0')
{$timestamp|cache_until}                                    dasselbe als Pipe
{def $secs = 'tomorrow'|seconds_until}                      Sekunden bis dahin, ganzzahlig
```

`cache_until` gibt nichts aus. Beide Operatoren werden ausgewertet, wenn das Template läuft, auch in kompilierten
Templates; sie haben keine Template-Hinweise, ein konstanter Wert wird also nicht beim Kompilieren festgeschrieben.
In Smarty- und Twig-Templates sind sie wie jeder Operator überbrückt. Die vollständige Referenz:
[cache_until und seconds_until](../../hilfe/designer/vorlagen/cache-until.md).

Registriert in `kernel/common/eztemplateautoload.php`; die Klasse `expCacheUntilOperator`
(`kernel/common/expcacheuntiloperator.php`) gibt jeden Aufruf an `expCacheUntil::request()` weiter
(`kernel/classes/expcacheuntil.php`). Dort liegt der Ablauf der Anfrage; `expCacheUntil::limit()` senkt ihn auf
`min(bisher, neu)` und nie höher.

## Wie der Zeitpunkt gelesen wird

| Wert | Ergebnis |
|---|---|
| Ganzzahl, Ziffernfolge, `DateTime` | dieser Unix-Zeitstempel |
| andere Zeichenkette | wie `strtotime()` / `DateTime::modify()`, in der Zeitzone der Website |
| `0`, `false`, `'0'` | 0: die Seite nicht behalten |
| Zeitpunkt in der Vergangenheit | 0, mit einem Hinweis in der Debug-Ausgabe |
| `null`, `''`, `true`, Array, Text ohne Zeit | ignoriert, mit einer Warnung in der Debug-Ausgabe |

- Eine Tageszeit, die heute schon vorbei ist, meint ihr nächstes Vorkommen: `'midnight'` und `'today'` die nächste
  Mitternacht, `'noon'` um 15:00 morgen Mittag, `'18:00'` um 19:30 morgen 18:00, `'monday'` an einem Montagabend den
  nächsten Montag.
- Nicht weitergerollt werden Angaben, die rückwärts gehen (`'-5 minutes'`, `'yesterday'`, `'last …'`, `'… ago'`),
  und feste Daten.
- An Tagen mit Zeitumstellung gelten die Regeln der Zeitzone; `'tomorrow'` ist die tatsächliche lokale Mitternacht.
- Eine Zahl ist immer ein Zeitpunkt: `cache_until( 3600 )` ist 1970, die Seite wird nicht behalten. Für eine
  Lebensdauer: `'+3600 seconds'`.

## Der View-Cache

Beim Erzeugen einer Ansicht öffnet `eZNodeviewfunctions` einen Bereich (Scope). Der früheste `cache_until`-Aufruf
der Templates der Ansicht und ein `cache_ttl` (als `time() + cache_ttl`) ergeben den Ablauf der Ansicht, der
**absolut** im Cache-Eintrag gespeichert wird: `$Result['cache_expires']`, dazu `cache_expires_source` (was ihn
gesetzt hat). Der Eintrag läuft zu diesem Zeitpunkt ab.

Wird der Eintrag später wieder ausgeliefert, meldet er seinen Ablauf erneut an die Anfrage. Die Caches danach
bekommen deshalb die **verbleibende** Zeit, nicht die ursprüngliche Lebensdauer. Einträge, die vor 6.0.15
geschrieben wurden und nur `cache_ttl` kennen, laufen zur Dateizeit plus `cache_ttl` ab; auch sie melden die
verbleibende Zeit.

`cache_until( 0 )` in der Ansicht wirkt wie `cache_ttl` = 0: Die Ansicht speichert die Markierung `no_cache`, wie es
`cache_ttl` = 0 immer getan hat. Diese Markierung bleibt, bis der View-Cache des Knotens geleert wird, etwa bei der
nächsten Veröffentlichung; bis dahin wird die Seite bei jeder Anfrage gerendert. Das ist das bisherige Verhalten von
`cache_ttl` = 0.

## Der Template-Block-Cache

Ein `{cache-block}`, in dessen Inhalt `cache_until` aufgerufen wurde, wird mit seinem Ablauf gespeichert (eine interne
Markierung am Anfang der Datei; Blöcke ohne Aufruf werden genau wie bisher gespeichert). Der Block läuft dann zu
diesem Zeitpunkt ab, zusätzlich zu seinem `expiry=` (Standard 7200 Sekunden), und wenn er aus dem Cache gelesen wird,
meldet er seinen Ablauf der Seite erneut. Für zeitabhängige Blöcke ist `expiry=` also nicht mehr nötig.

- `cache_until( 0 )` im Inhalt eines Blocks: Der Block wird nicht gespeichert.
- Das `expiry=` eines Blocks begrenzt die **Seite nicht**: Es beschreibt, wie fein der Block gecacht wird, nicht wann
  sein Inhalt falsch wird (siehe [Entscheidungen](#entscheidungen)).
- Dasselbe gilt für `{expcacheblock}` in Smarty und `{% expcacheblock %}` in Twig, die denselben Block-Cache nutzen.
- Block-Dateien von vor 6.0.15 funktionieren weiter (keine Markierung, kein Ablauf).

Implementierung: `lib/eztemplate/classes/eztemplatecacheblock.php` (`cachedText()`, `storeData()`) und
`eztemplatecachefunction.php`.

## Der rollenbewusste HTTP-Cache

Der Cache aus `httpcache.ini` (Erweiterung `exphttpcache`, früher Ausstieg, und der App-Cache von Velocity, der
denselben Vertrag nutzt) berücksichtigt den Ablauf der Seite (`ezpHttpCacheListener`, `ezpHttpCacheContract`):

- Die Lebensdauer eines Eintrags ist `min(Ablauf - jetzt, MaxAge)`; der Eintrag behält den Ablauf (`expires`).
- Nach dem Ablauf gibt es kein stale-while-revalidate: Für solche Einträge ist es 0.
- Ist der Ablauf schon erreicht, wird nichts gespeichert: `X-Exp-Cache: BYPASS (cache_until)`. Kam der Ablauf aus
  der Inhaltsansicht selbst (`cache_until( 0 )` oder ein vergangener Zeitpunkt in einem View-Template), steht dort
  wie bisher `BYPASS (cache_ttl=0)`, weil die Ansicht dann als `cache_ttl` = 0 gespeichert ist.
- Eine Suche zum oder nach dem Ablauf ist ein Fehltreffer: `MISS (expired (cache_until))`.
- Antworten aus dem Cache (`HIT`) bekommen für anonyme Besucher `Cache-Control: public, max-age=` mit dem kleineren
  Wert aus 300 und den verbleibenden Sekunden statt fest 300; für angemeldete Besucher bleibt es
  `private, no-cache, must-revalidate`.
- Gespeicherte Einträge tragen `X-Exp-Cache-Until`.

**Behobener Fehler:** Bisher nahm `store()` das ursprüngliche `cache_ttl` der Ansicht als neue Lebensdauer. Eine
Seite, die um 9 Uhr mit `cache_ttl` = 54000 erzeugt (gültig bis Mitternacht) und um 23 Uhr aus dem View-Cache
ausgeliefert wurde, lebte im HTTP-Cache bis etwa 14 Uhr am nächsten Tag. Jetzt bekommt sie die verbleibende Stunde.

## Die Response-Header

Nach dem Pagelayout und vor der Ausgabe ruft `ezpKernelWeb` `expCacheUntil::sendResponseHeaders()` auf. Nur wenn ein
Ablauf gesetzt ist, ändert sich etwas:

| Header | Änderung |
|---|---|
| `Cache-Control` | Jedes `max-age` und `s-maxage` über den verbleibenden Sekunden wird auf sie gesenkt, nie erhöht. |
| `Cache-Control` ohne `max-age` und `s-maxage` | (der Standard des Kernels, `no-cache, must-revalidate`, und weder `private` noch `no-store`) `s-maxage=<verbleibend>` wird ergänzt, wenn die verbleibende Zeit 0 ist oder unter `velocity.ini [CacheSettings] DefaultTtl` liegt (Standard 30). So hält der Antwort-Cache von Velocity die Seite nicht länger als bis zum Ablauf. Sonst wird nichts ergänzt, denn das würde die Lebensdauer erhöhen. |
| `Expires` | gesenkt, wenn er einen späteren Zeitpunkt nennt (der Standard des Kernels aus dem Jahr 1997 bleibt) |
| `Pragma: no-cache` | entfernt, nur wenn das neue `Cache-Control` ein positives `max-age` und kein `no-cache` hat |
| `X-LiteSpeed-Cache-Control` | `no-cache`, wenn die verbleibende Zeit 0 ist (immer; das hält eine Seite nur heraus). Mit `site.ini [CacheUntilSettings] LiteSpeedMaxAge=enabled` (Standard `disabled`) bekommen Seiten anonymer Besucher `max-age=<verbleibend>`. |
| `X-Exp-Cache-Until` | der Ablauf nach ISO 8601 in der Zeitzone der Website, zum Beispiel `2026-11-16T00:00:00+01:00`, oder `0`; mit eingeschalteter Debug-Ausgabe zusätzlich `; source="<Template, cache-block, view cache: … oder cache_ttl=N>"` |

Spätere Schichten können überschreiben, wie bisher: Der Debug-Bericht sendet `private, no-store`, eine Seite mit einem
Formular-Token `private, no-cache`.

Die Ergänzung von `s-maxage` hängt von Velocity ab: Ist dessen Antwort-Cache nicht eingeschaltet
(`velocity.ini [CacheSettings] Enabled`), wird `s-maxage` nur für eine verbleibende Zeit von 0 ergänzt.

## Velocity, CDN, LiteSpeed und Browser

- **Antwort-Cache von Velocity.** Er liest `s-maxage`, sonst `max-age`, sonst `DefaultTtl`. Für gerenderte Seiten
  sorgen die gesenkten oder ergänzten Werte oben dafür, dass er die Seite nicht über den Ablauf hinaus hält; für
  Antworten aus dem HTTP-Cache das gesenkte `max-age` des Treffers (Velocity speichert die Antwort des App-Cache mit
  ihrem `Cache-Control`). Velocity selbst ist unverändert. Siehe
  [Der Antwort-Cache von Velocity](velocity-antwort-cache.md).
- **CDN, Cloudflare.** Ein CDN berücksichtigt das gesenkte `max-age` oder `s-maxage`, wenn seine Cache-Regel die
  Header des Ursprungs respektiert (bei Cloudflare: Edge TTL „use cache-control header if present“). Eine Edge-TTL,
  die den Ursprung übergeht, kann die Anwendung nicht begrenzen: Leeren Sie dort oder wählen Sie eine kürzere TTL.
  Siehe [CDN](../../hilfe/hosting/leistung/cdn.md).
- **LiteSpeed.** Eine Seite, die nicht behalten werden darf, bekommt immer `X-LiteSpeed-Cache-Control: no-cache`.
  Viele Hoster lassen LiteSpeed das `Cache-Control` der Antwort ignorieren; seinen eigenen Header liest er immer.
  `LiteSpeedMaxAge=enabled` schickt deshalb für anonyme Besucher `X-LiteSpeed-Cache-Control: max-age=<verbleibend>`.
  Es ist abschaltbar ausgeliefert, weil derselbe Header auch eine Seite zwischenspeicherbar machen kann, die die
  Rewrite-Regeln nicht cachen. Siehe
  [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../anleitungen/seiten-cache-mit-litespeed.md#wie-er-zu-den-caches-von-exponential-steht).
- **Browser.** Sie folgen demselben `Cache-Control` und `Expires`.

## Der statische Cache

Die Dateien des statischen Caches haben keine Lebensdauer; sie werden beim Veröffentlichen neu erzeugt. Der Ablauf
wird dort **nicht** berücksichtigt. Nehmen Sie Seiten mit `cache_until` aus dem statischen Cache aus
(nicht in `staticcache.ini [CacheSettings] CachedURLArray[]` und nicht in `AlwaysUpdateArray[]`), lassen Sie sie zum Ablaufzeitpunkt von einem Cronjob
neu erzeugen, oder nutzen Sie stattdessen den rollenbewussten HTTP-Cache. Siehe
[Statischer Cache](../../hilfe/hosting/leistung/statischer-cache.md).

## Die Ausgabe zur Fehlersuche

- **Debug-Leiste**, Reiter Rendering: das Feld „Until HH:MM“ (in Ordnung) oder „Not cached“ (Warnung). Der Tooltip
  nennt den Zeitpunkt nach ISO 8601, die verbleibenden Sekunden, was ihn gesetzt hat und die Zahl der Aufrufe.
- **Debug-Ausgabe**: `cache_until: kept until <ISO> (<s> s), set by <Quelle>, N calls` oder
  `… the page is not kept`; Warnungen für ignorierte Werte, Hinweise für Zeitpunkte in der Vergangenheit.
- **Header**: `X-Exp-Cache-Until` (oben) und vom HTTP-Cache `X-Exp-Cache` mit `BYPASS (cache_until)`, `MISS` oder
  `HIT`.

Auf dem Demonstrationsserver geprüft (15. November 2026): ein Cache-Block mit `{cache_until( '+90 seconds' )}`, in ein
Pagelayout eingebunden. Die erste Anfrage zeigte
`X-Exp-Cache-Until: …T11:00:05-08:00; source="design/standard/templates/cache_until_probe_block.tpl"`, die zweite (der
Block kam aus dem Cache) denselben Zeitpunkt mit `source="cache-block"`.

## Rückwärtskompatibilität

- `{set-block scope=root variable=cache_ttl}N{/set-block}` funktioniert weiter: Beim Erzeugen der Ansicht wird daraus
  `cache_until( time() + N )`; `cache_ttl` = 0 ist `cache_until( 0 )`. Keine Einstellung muss geändert werden.
- `cache_ttl` wirkt weiter nur in der Inhaltsansicht, `cache_until` überall; stellen Sie um, zum Beispiel
  `{set-block scope=root variable=cache_ttl}{$sekunden_bis_mitternacht}{/set-block}` zu `{cache_until( 'tomorrow' )}`.
- Template-Block-Dateien von vorher funktionieren weiter.
- Kleinere Unterschiede: Eine Seite mit `cache_ttl` > 0 im HTTP-Cache bekommt nach ihrem Ablauf kein
  stale-while-revalidate mehr, und `Cache-Control` bekommt `s-maxage`, wenn die verbleibende Zeit unter `DefaultTtl`
  von Velocity liegt. Auch Seiten mit `cache_ttl` = 0 tragen jetzt `s-maxage=0`, sodass Velocity sie nicht mehr
  `DefaultTtl` Sekunden behält.

Einzelheiten für bestehende Websites: [Aktualisierung auf 6.0.15: cache_until](../../bc/6.0/cache-until.md).

## Die Zeitzone bei der Installation

`'tomorrow'` braucht eine Zeitzone. Neue Installationen schreiben `site.ini [TimeZoneSettings] TimeZone` nach
`settings/override/site.ini.append.php`, gewählt in dieser Reihenfolge:

1. `exp:install --timezone=<Zone>` oder `kickstart.ini [language_options] TimeZone=<Zone>`. Eine unbekannte Zone
   beendet `exp:install` mit einem Fehler; der Kickstarter hält beim Sprachschritt an.
2. Sonst die Zone des Landes der Hauptsprache, wenn es eine Hauptzone hat: `ger-DE` Europe/Berlin, `eng-GB`
   Europe/London, `fre-FR` Europe/Paris, `nor-NO` Europe/Oslo, `ser-SR` Europe/Belgrade (Serbisch) und andere. Länder
   mit mehreren gewichtigen Zonen (USA, Kanada, Australien, Brasilien, Russland, Mexiko, Indonesien und andere) werden
   nie geraten.
3. Sonst `date.timezone` aus der `php.ini`.
4. Sonst UTC.

`exp:install` nennt die Zone und den Grund, in der Zeile des Probelaufs und in der Zusammenfassung
(`Time zone: Europe/Berlin (from ger-DE)`). Der Einrichtungsassistent im Browser nutzt die Schritte 2 bis 4 (kein
neues Formularfeld). Ein Seitenpaket kann danach weiterhin seinen eigenen Wert schreiben. Die Multisite-Installer
(Pakete `sevenx_multisite`, `cjw_multisite`) laufen durch denselben Schritt CreateSites und bekommen die Zone ebenso;
an den Paketen hat sich nichts geändert. Siehe
[Exponential mit einem einzigen Befehl installieren](installation-mit-einem-befehl.md) und
[Kickstarter](kickstarter-cli.md).

Bestehende Websites werden nicht geändert. **Setup > Systeminformationen** zeigt einen Hinweis („The site has no time
zone of its own“), wenn `TimeZone` leer ist und PHP eine `date.timezone` hat, und wie bisher eine Warnung, wenn
keines von beiden gesetzt ist.

## Entscheidungen

- **Der Ablauf eines Cache-Blocks (`expiry=`) begrenzt die Seite nicht.** Er sagt, wie fein ein Teil gecacht wird,
  nicht wann sein Inhalt falsch wird. Sonst liefe jede Seite mit einem Menü-Block nach zwei Stunden ab.
- **Kein stale-while-revalidate nach dem Ablauf.** Ein Zeitpunkt ist ein Zeitpunkt: Der Termin von gestern wird auch
  nicht für einige Sekunden noch gezeigt.
- **`max-age` und `s-maxage` werden nur gesenkt, nie erhöht**, und `s-maxage` wird nur ergänzt, wo ein geteilter
  Cache die Seite sonst länger hielte.
- **LiteSpeed bekommt eine Lebensdauer nur auf Wunsch** (`LiteSpeedMaxAge`), weil sie auch Seiten cachebar machen
  kann, die die Regeln nicht cachen.
- **Der statische Cache berücksichtigt den Ablauf nicht**; seine Dateien haben keine Lebensdauer.
- **Kein Upgrade-Schritt für die Zeitzone**: Eine geänderte Zeitzone verschiebt jedes Datum, das die Website zeigt.
  Das entscheidet der Betreiber.

## Getestet

`tests/tests/kernel/classes/expCacheUntilTest.php` (CU-01 bis CU-13) prüft die Zeitzone, das Weiterrollen von Tageszeiten,
den frühesten Aufruf, `0` und ignorierte Werte, `seconds_until`, `cache_ttl`, den behobenen Fehler mit der
verbleibenden Zeit, den HTTP-Cache, Block-Dateien und `cache_until` in einem Cache-Block (interpretiert und
kompiliert), die Response-Header, das Feld der Debug-Leiste und die Zeitzone des Installers;
`expSystemReportTest.php` (SR-14) den Hinweis zur Zeitzone in den Systeminformationen. Auf dem Demonstrationsserver wurde der Ablauf eines
Cache-Blocks im Pagelayout über zwei Anfragen geprüft (siehe oben). Der Demonstrationsserver läuft selbst mit leerem
`TimeZone` und der PHP-Zone America/Los_Angeles, daher der Versatz `-08:00` im Beispiel.
