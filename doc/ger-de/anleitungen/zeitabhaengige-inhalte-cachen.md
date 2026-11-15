# Zeitabhängige Inhalte cachen

Diese Anleitung zeigt, wie Sie Seiten cachen, deren Inhalt zu einem bestimmten Zeitpunkt nicht mehr stimmt, ohne dass
jemand etwas veröffentlicht: kommende Termine, der Beitrag des Tages, Öffnungszeiten, Countdowns, ein befristeter
Hinweis im Kopf der Seite, ein Live-Ticker. Das Werkzeug dafür ist der Template-Operator `cache_until` aus Exponential
6.0.15: Ein Template sagt, bis wann seine Ausgabe gilt, und jeder Cache der Seite hält sie höchstens bis dahin.

Sie richtet sich an Site-Gestalter und Entwickler, die Templates schreiben. Jedes Beispiel ist ein vollständiges
`.tpl`-Stück, das Sie anpassen können; die Klassen- und Attributnamen (`event`, `start`, `show_until` und so weiter)
sind Beispiele, setzen Sie die Ihrer Website ein. Die Fakten stammen aus dem Code (`kernel/classes/expcacheuntil.php`,
`kernel/common/expcacheuntiloperator.php`) und wurden am 15. November 2026 auf dem Demonstrationsserver geprüft.

[Anleitungen](README.md) · Referenz: [cache_until und seconds_until](../hilfe/designer/vorlagen/cache-until.md) ·
Funktion: [cache_until: Seiten, die pünktlich ablaufen](../funktionen/6.0/cache-until.md) · Aktualisierung:
[Hinweise zu cache_until](../bc/6.0/cache-until.md) · Verwandt: [Caches](caches.md),
[Cache-Block](../hilfe/designer/vorlagen/cache-block.md)

## Kurz gesagt

- `{cache_until( 'tomorrow' )}` heißt: Diese Seite gilt bis Mitternacht. `{cache_until( $zeitstempel )}` heißt: bis zu
  diesem Zeitpunkt. `{cache_until( 0 )}` heißt: in keinem Cache behalten.
- Der Aufruf wirkt **überall**: in der Inhaltsansicht, in Includes, im Pagelayout, in Menüs, in Fußzeilen, in Blöcken
  von Exponential Layouts und im Inhalt eines `{cache-block}`.
- **Der früheste Zeitpunkt gewinnt.** Rufen Sie `cache_until` ruhig mehrmals auf; jeder Aufruf kann den Ablauf nur
  vorziehen.
- Zeitangaben gelten in der **Zeitzone der Website** (`site.ini [TimeZoneSettings] TimeZone`). Richten Sie sie ein,
  bevor Sie sich auf `'tomorrow'` verlassen ([Abschnitt 13](#13-zeitzone-einrichten)).
- Eine **Zahl ist immer ein Zeitpunkt**, nie eine Dauer: Für eine Stunde schreiben Sie `'+1 hour'`, nicht `3600`.
- Ein **fehlender Wert wird ignoriert**: Prüfen Sie Variablen, die leer sein können, mit `{if}`.
- Prüfen Sie das Ergebnis am Header `X-Exp-Cache-Until` oder in der Debug-Leiste
  ([Abschnitt 12](#12-prüfen-mit-curl-und-der-debug-leiste)).

## Inhalt

1. [Wie es funktioniert](#1-wie-es-funktioniert)
2. [Kommende Termine](#2-kommende-termine)
3. [Beitrag des Tages](#3-beitrag-des-tages)
4. [Öffnungszeiten](#4-öffnungszeiten)
5. [Countdowns mit seconds_until](#5-countdowns-mit-seconds_until)
6. [Blöcke im Pagelayout: Hinweis im Kopf, Fußzeile, Menüs](#6-blöcke-im-pagelayout-hinweis-im-kopf-fußzeile-menüs)
7. [Blöcke von Exponential Layouts](#7-blöcke-von-exponential-layouts)
8. [Inhalte von Cache-Blöcken](#8-inhalte-von-cache-blöcken)
9. [Live-Ticker: gar nicht cachen](#9-live-ticker-gar-nicht-cachen)
10. [Veröffentlichungsfenster](#10-veröffentlichungsfenster)
11. [Umstieg von cache_ttl](#11-umstieg-von-cache_ttl)
12. [Prüfen mit curl und der Debug-Leiste](#12-prüfen-mit-curl-und-der-debug-leiste)
13. [Zeitzone einrichten](#13-zeitzone-einrichten)
14. [Häufige Fehler](#14-häufige-fehler)

## 1. Wie es funktioniert

Eine Seite durchläuft mehrere Caches: den View-Cache der Inhaltsansicht, den Template-Block-Cache, auf Wunsch den
rollenbewussten HTTP-Cache, den Antwort-Cache von Velocity, ein CDN, den Browser. Jeder davon hält eine Seite, bis sie
abläuft oder eine Veröffentlichung sie leert. Was eine Veröffentlichung nicht leert, ist die **Zeit**: Der Termin von
Freitag 19 Uhr steht nach Freitag 19 Uhr noch auf der Seite.

`cache_until` meldet einen Zeitpunkt an die Anfrage. Am Ende hat die Seite einen Ablauf, den frühesten aller Aufrufe,
und jede Schicht hält sich daran:

| Schicht | Was mit dem Ablauf geschieht |
|---|---|
| View-Cache | Der Eintrag läuft zum Zeitpunkt ab; wird er wieder ausgeliefert, meldet er die **verbleibende** Zeit weiter. |
| Template-Block-Cache | Der Block läuft zum Zeitpunkt ab und meldet ihn der Seite, auch wenn er aus dem Cache kommt. |
| Rollenbewusster HTTP-Cache | höchstens bis zum Zeitpunkt, ohne stale-while-revalidate danach |
| Response-Header | `max-age`, `s-maxage` und `Expires` werden auf die verbleibende Zeit gesenkt; dazu `X-Exp-Cache-Until` |
| Velocity, CDN, Browser | folgen den Headern |
| LiteSpeed | `X-LiteSpeed-Cache-Control: no-cache` bei Ablauf 0; eine Lebensdauer nur mit `site.ini [CacheUntilSettings] LiteSpeedMaxAge=enabled` |
| Statischer Cache | **nicht** berücksichtigt; nehmen Sie solche Seiten dort aus |

Eine Seite ohne `cache_until` ist unverändert. Mehr über jede Schicht: [die Funktionsseite](../funktionen/6.0/cache-until.md).

## 2. Kommende Termine

Ein Veranstaltungsordner listet die nächsten zehn Termine. Die Liste ändert sich, sobald der erste Termin beginnt: Dann
fällt er heraus. Das ist der Ablauf der Seite.

```tpl
{* design/<Ihr Design>/override/templates/full/event_folder.tpl *}
{def $now    = currentdate()
     $events = fetch( 'content', 'list', hash(
                   'parent_node_id',     $node.node_id,
                   'class_filter_type',  'include',
                   'class_filter_array', array( 'event' ),
                   'attribute_filter',   array( array( 'event/start', '>', $now ) ),
                   'sort_by',            array( 'attribute', true(), 'event/start' ),
                   'limit',              10 ) )}

<h1>{$node.name|wash}</h1>

{if $events}
    <ul class="events">
    {foreach $events as $event}
        <li>
            <time>{$event.data_map.start.content.timestamp|l10n( 'shortdatetime' )}</time>
            <a href={$event.url_alias|ezurl}>{$event.name|wash}</a>
        </li>
    {/foreach}
    </ul>
    {* der erste Termin beginnt: Dann ist die Liste falsch *}
    {cache_until( $events[0].data_map.start.content.timestamp )}
{else}
    <p>Zurzeit sind keine Termine geplant.</p>
    {* kein Termin, kein Ablauf: Ein neuer Termin wird veröffentlicht und leert den Cache ohnehin *}
{/if}
{undef $now $events}
```

Der Wächter `{if $events}` ist wichtig: Ohne Termine wäre `$events[0]` leer, und `cache_until` würde den Aufruf
ignorieren (mit einer Warnung in der Debug-Ausgabe). Das wäre hier sogar richtig, aber in anderen Templates führt ein
unbemerkt fehlender Wert dazu, dass die Seite bis zur nächsten Veröffentlichung im Cache bleibt.

**Termine, die bis zu ihrem Ende sichtbar bleiben.** Zeigt die Liste laufende Termine mit „läuft gerade“ an, ändert
sie sich zweimal je Termin: wenn er beginnt und wenn er endet. Rufen Sie `cache_until` für beide auf; der früheste
Zeitpunkt gewinnt:

```tpl
{def $now    = currentdate()
     $events = fetch( 'content', 'list', hash(
                   'parent_node_id',     $node.node_id,
                   'class_filter_type',  'include',
                   'class_filter_array', array( 'event' ),
                   'attribute_filter',   array( array( 'event/end', '>', $now ) ),
                   'sort_by',            array( 'attribute', true(), 'event/start' ),
                   'limit',              10 ) )}

<ul class="events">
{foreach $events as $event}
    {def $start = $event.data_map.start.content.timestamp
         $end   = $event.data_map.end.content.timestamp}
    <li>
        <a href={$event.url_alias|ezurl}>{$event.name|wash}</a>
        {if $start|gt( $now )}
            ab {$start|l10n( 'shortdatetime' )}
            {cache_until( $start )}    {* beginnt: "läuft gerade" erscheint *}
        {else}
            <strong>läuft gerade</strong>
        {/if}
        {cache_until( $end )}          {* endet: fällt aus der Liste *}
    </li>
    {undef $start $end}
{/foreach}
</ul>
{undef $now $events}
```

Rufen Sie `cache_until` nie mit dem Beginn eines Termins auf, der schon begonnen hat: Ein Zeitpunkt in der
Vergangenheit heißt „nicht behalten“, und die Seite würde bei jeder Anfrage neu gerendert. Deshalb steht der Aufruf
oben im Zweig `$start|gt( $now )`.

**Der nächste Termin in der Seitenleiste.** Derselbe Gedanke im Pagelayout, in einem Cache-Block, der für alle Seiten
gleich ist:

```tpl
{cache-block keys=array( 'next-event' )}
    {def $next = fetch( 'content', 'list', hash(
                     'parent_node_id',     60,    {* Knoten-ID Ihres Veranstaltungsordners *}
                     'class_filter_type',  'include',
                     'class_filter_array', array( 'event' ),
                     'attribute_filter',   array( array( 'event/start', '>', currentdate() ) ),
                     'sort_by',            array( 'attribute', true(), 'event/start' ),
                     'limit',              1 ) )}
    {if $next}
        <aside class="next-event">
            <h2>Nächster Termin</h2>
            <a href={$next[0].url_alias|ezurl}>{$next[0].name|wash}</a>,
            {$next[0].data_map.start.content.timestamp|l10n( 'shortdatetime' )}
        </aside>
        {cache_until( $next[0].data_map.start.content.timestamp )}
    {/if}
    {undef $next}
{/cache-block}
```

Der Block läuft ab, wenn der Termin beginnt, und solange er aus dem Cache kommt, meldet er diesen Zeitpunkt jeder
Seite, in der er steht. Ein `expiry=` brauchen Sie dafür nicht.

## 3. Beitrag des Tages

Ein „Tipp des Tages“ wählt jeden Tag einen anderen Artikel aus einem Ordner. Die Auswahl ändert sich um Mitternacht.

```tpl
{def $folder = 120    {* Knoten-ID des Ordners mit den Tipps *}
     $count  = fetch( 'content', 'list_count', hash(
                   'parent_node_id',     $folder,
                   'class_filter_type',  'include',
                   'class_filter_array', array( 'article' ) ) )}
{if $count}
    {def $day  = currentdate()|datetime( 'custom', '%z' )|int    {* Tag des Jahres, 0 bis 365 *}
         $tips = fetch( 'content', 'list', hash(
                     'parent_node_id',     $folder,
                     'class_filter_type',  'include',
                     'class_filter_array', array( 'article' ),
                     'sort_by',            array( 'published', true() ),
                     'offset',             mod( $day, $count ),
                     'limit',              1 ) )}
    <section class="tip-of-the-day">
        <h2>Tipp des Tages</h2>
        {node_view_gui content_node=$tips[0] view='line'}
    </section>
    {undef $day $tips}
{/if}
{cache_until( 'tomorrow' )}
{undef $folder $count}
```

`'tomorrow'` ist Mitternacht in der Zeitzone der Website. Steht der Tipp in einer Vollansicht, reicht dieser eine
Aufruf; steht er im Pagelayout, setzen Sie ihn in einen Cache-Block wie in [Abschnitt 8](#8-inhalte-von-cache-blöcken).

## 4. Öffnungszeiten

„Jetzt geöffnet“ wechselt zu „geschlossen“ und zurück, zu festen Uhrzeiten. Das Beispiel: Montag bis Freitag 9 bis 18
Uhr, Samstag 10 bis 14 Uhr, Sonntag geschlossen. Jeder Zweig nennt den Zeitpunkt, zu dem sich der Zustand als Nächstes
ändert.

```tpl
{def $now  = currentdate()
     $day  = $now|datetime( 'custom', '%w' )|int    {* 0 = Sonntag, 6 = Samstag *}
     $hour = $now|datetime( 'custom', '%G' )|int    {* 0 bis 23 *}
     $open = false()}

{if eq( $day, 0 )}                                  {* Sonntag *}
    {cache_until( 'monday 09:00' )}
{elseif eq( $day, 6 )}                              {* Samstag *}
    {if lt( $hour, 10 )}
        {cache_until( '10:00' )}
    {elseif lt( $hour, 14 )}
        {set $open = true()}
        {cache_until( '14:00' )}
    {else}
        {cache_until( 'monday 09:00' )}
    {/if}
{else}                                              {* Montag bis Freitag *}
    {if lt( $hour, 9 )}
        {cache_until( '09:00' )}
    {elseif lt( $hour, 18 )}
        {set $open = true()}
        {cache_until( '18:00' )}
    {elseif eq( $day, 5 )}
        {cache_until( 'saturday 10:00' )}
    {else}
        {cache_until( 'tomorrow 09:00' )}
    {/if}
{/if}

<p class="opening {if $open}is-open{else}is-closed{/if}">
    {if $open}Jetzt geöffnet{else}Zurzeit geschlossen{/if}
</p>
{undef $now $day $hour $open}
```

Eine Tageszeit, die heute schon vorbei ist, meint ihr nächstes Vorkommen. `'09:00'` um 7 Uhr ist heute 9 Uhr, um 19
Uhr wäre es morgen 9 Uhr; Sie müssen das nicht selbst ausrechnen. Die Zahlen aus `datetime` und die Zeichenketten von
`cache_until` gelten in derselben Zeitzone, wenn `[TimeZoneSettings] TimeZone` gesetzt ist
([Abschnitt 13](#13-zeitzone-einrichten)).

Für Feiertage und Ausnahmen legen Sie Inhaltsobjekte an (zum Beispiel „Geschlossen vom 24.12. bis 26.12.“ mit Beginn
und Ende) und behandeln Sie sie wie die Termine in [Abschnitt 2](#2-kommende-termine): Beginn und Ende als
`cache_until`, der früheste gewinnt.

## 5. Countdowns mit seconds_until

`seconds_until` rechnet die Sekunden bis zu einem Zeitpunkt aus und setzt selbst keinen Ablauf. Eine Zahl, die in eine
gecachte Seite geschrieben wird, veraltet mit ihr. Nennen Sie deshalb den Zeitpunkt, zu dem sich die **angezeigte**
Zahl ändert.

**Tage bis zu einem Datum.** Die Zahl der Tage ändert sich, wenn ein weiterer ganzer Tag vergangen ist:

```tpl
{def $secs = '2026-12-24 18:00'|seconds_until}
{if $secs|gt( 0 )}
    <p class="countdown">Noch {div( $secs, 86400 )|floor} Tage bis Heiligabend.</p>
    {cache_until( sum( currentdate(), mod( $secs, 86400 ), 1 ) )}
{else}
    <p class="countdown">Frohe Weihnachten!</p>
    {* ein festes Datum in der Vergangenheit hieße "nicht behalten": hier kein Aufruf *}
{/if}
{undef $secs}
```

**Stunden bis zum Ende einer Aktion.** Für eine gröbere Anzeige reicht der nächste Wechsel der Stundenzahl:

```tpl
{def $end  = $node.data_map.offer_end.content.timestamp
     $secs = $end|seconds_until}
{if $secs|gt( 0 )}
    <p>Nur noch {div( $secs, 3600 )|floor} Stunden.</p>
    {cache_until( sum( currentdate(), mod( $secs, 3600 ), 1 ) )}
    {cache_until( $end )}
{else}
    <p>Die Aktion ist beendet.</p>
{/if}
{undef $end $secs}
```

**Ein Countdown im Browser, der jede Sekunde zählt.** Geben Sie dem Skript den **festen Zielzeitpunkt**, nicht die
Sekunden: Der Zeitpunkt bleibt in einer gecachten Seite richtig, die Sekunden nicht. Die Seite selbst muss dann nur bis
zum Ziel gelten:

```tpl
{def $end = $node.data_map.offer_end.content.timestamp}
{if $end|gt( currentdate() )}
    <p class="countdown" data-countdown-until="{$end}">
        Endet am {$end|l10n( 'shortdatetime' )}
    </p>
    {cache_until( $end )}
{/if}
{undef $end}
```

Das Skript, das `data-countdown-until` liest, gehört in eine JavaScript-Datei Ihres Designs.

## 6. Blöcke im Pagelayout: Hinweis im Kopf, Fußzeile, Menüs

Das Pagelayout läuft bei jeder Anfrage, aber die ganze Seite liegt im HTTP-Cache, im Antwort-Cache von Velocity und im
CDN, und seine teuren Teile liegen in Cache-Blöcken. `cache_until` im Pagelayout begrenzt all das.

**Ein befristeter Hinweis im Kopf.** Redakteure legen ein Objekt „Hinweis“ (Klasse `site_notice`) mit einem Text und
einem Ende an (`show_until`, ein Datum mit Uhrzeit):

```tpl
{cache-block keys=array( 'site-notice' )}
    {def $notices = fetch( 'content', 'list', hash(
                        'parent_node_id',     2,
                        'depth',              2,
                        'class_filter_type',  'include',
                        'class_filter_array', array( 'site_notice' ),
                        'attribute_filter',   array( array( 'site_notice/show_until', '>', currentdate() ) ),
                        'sort_by',            array( 'attribute', true(), 'site_notice/show_until' ),
                        'limit',              1 ) )}
    {if $notices}
        <div class="site-notice" role="status">
            {attribute_view_gui attribute=$notices[0].data_map.text}
        </div>
        {cache_until( $notices[0].data_map.show_until.content.timestamp )}
    {/if}
    {undef $notices}
{/cache-block}
```

Ein neuer Hinweis erscheint, weil eine Veröffentlichung Cache-Blöcke ablaufen lässt; er verschwindet pünktlich, weil
der Block zum Ende abläuft.

**Die Fußzeile mit dem Jahr.** Ein Block, der nie von Inhalten abhängt, aber zum Jahreswechsel falsch wird:

```tpl
{cache-block ignore_content_expiry}
    {def $year = currentdate()|datetime( 'custom', '%Y' )|int}
    <footer>
        &copy; 2004&ndash;{$year} Musterverein e. V.
        {include uri='design:pagelayout/footer_links.tpl'}
    </footer>
    {cache_until( concat( $year|inc, '-01-01 00:00' ) )}
    {undef $year}
{/cache-block}
```

Früher hätte dieser Block `expiry=86400` gebraucht, damit die Jahreszahl höchstens einen Tag zu spät wechselt. Jetzt
liegt er bis Silvester um Mitternacht im Cache und wechselt genau dann.

**Ein Menüpunkt mit Saison.** Der Adventskalender erscheint vom 1. bis 24. Dezember im Hauptmenü:

```tpl
{cache-block keys=array( $module_result.uri, $current_user.role_id_list|implode( ',' ) ) subtree_expiry=2}
    {def $year  = currentdate()|datetime( 'custom', '%Y' )|int
         $month = currentdate()|datetime( 'custom', '%n' )|int
         $day   = currentdate()|datetime( 'custom', '%j' )|int}
    <ul class="main-menu">
        {include uri='design:pagelayout/menu_items.tpl'}
        {if and( eq( $month, 12 ), le( $day, 24 ) )}
            <li><a href={'/Adventskalender'|ezurl}>Adventskalender</a></li>
            {cache_until( concat( $year, '-12-25 00:00' ) )}
        {elseif lt( $month, 12 )}
            {cache_until( concat( $year, '-12-01 00:00' ) )}
        {else}
            {cache_until( concat( $year|inc, '-12-01 00:00' ) )}
        {/if}
    </ul>
    {undef $year $month $day}
{/cache-block}
```

Jede Kopie des Menüblocks (eine je Seite und Rollenkombination) läuft zum nächsten Wechsel ab. Feste Daten werden nicht
weitergerollt; deshalb rechnet das Template das Jahr selbst aus.

## 7. Blöcke von Exponential Layouts

Blöcke von Exponential Layouts werden im Pagelayout gerendert, über `fetch( 'explayouts', 'resolve_layout', hash() )`
und die Block-Templates Ihres Designs (`explayouts/block/*.tpl`). `cache_until` in einem Block-Template wirkt wie im
Pagelayout: Der Ablauf gilt für die ganze Seite.

Ein eigenes Block-Template „Nächste Termine“, das die Termine unter einem Ordner zeigt:

```tpl
{* design/<Ihr Design>/templates/explayouts/block/upcoming_events.tpl *}
{def $events = fetch( 'content', 'list', hash(
                   'parent_node_id',     60,    {* Knoten-ID Ihres Veranstaltungsordners *}
                   'class_filter_type',  'include',
                   'class_filter_array', array( 'event' ),
                   'attribute_filter',   array( array( 'event/start', '>', currentdate() ) ),
                   'sort_by',            array( 'attribute', true(), 'event/start' ),
                   'limit',              3 ) )}
<div class="ngl-block upcoming-events">
    {if $block.name}<h2>{$block.name|wash}</h2>{/if}
    {if $events}
        {foreach $events as $event}
            {node_view_gui content_node=$event view='line'}
        {/foreach}
        {cache_until( $events[0].data_map.start.content.timestamp )}
    {else}
        <p>Zurzeit sind keine Termine geplant.</p>
    {/if}
</div>
{undef $events}
```

Wie Sie ein Block-Template einer Blockdefinition zuordnen: [Exponential Layouts](../bc/6.0/layouts.md).

## 8. Inhalte von Cache-Blöcken

Ein Cache-Block, in dessen Inhalt `cache_until` aufgerufen wird, wird mit diesem Ablauf gespeichert. Er läuft dann
ab, zusätzlich zu seinem `expiry=` (Standard 7200 Sekunden) und den Veröffentlichungen, und wenn er aus dem Cache
gelesen wird, meldet er den Ablauf der Seite erneut, als wäre der Aufruf noch einmal gelaufen.

Für zeitabhängige Blöcke ist `expiry=` deshalb **nicht mehr nötig**:

```tpl
{* vorher: höchstens 15 Minuten zu spät, dafür alle 15 Minuten neu gerendert *}
{cache-block keys=array( 'specials' ) expiry=900}
    {include uri='design:parts/todays_specials.tpl'}
{/cache-block}

{* nachher: bis zum nächsten Wechsel im Cache, und dann sofort neu *}
{cache-block keys=array( 'specials' )}
    {include uri='design:parts/todays_specials.tpl'}
    {cache_until( 'tomorrow' )}
{/cache-block}
```

Der Aufruf darf auch in einem Template stehen, das der Block einbindet (hier `todays_specials.tpl`); er gehört zum
Block, solange er während dessen Erzeugung läuft.

Zu beachten:

- `{cache_until( 0 )}` im Inhalt eines Blocks: Der Block wird nicht gespeichert und bei jeder Anfrage erzeugt; auch
  die Seite wird nicht behalten.
- Das `expiry=` eines Blocks begrenzt die **Seite** nicht. Es sagt, wie fein der Block gecacht wird, nicht wann sein
  Inhalt falsch wird.
- Block-Dateien von vor 6.0.15 funktionieren weiter; sie haben keinen eigenen Ablauf.
- Dasselbe gilt für `{expcacheblock}` in Smarty- und `{% expcacheblock %}` in Twig-Templates.

## 9. Live-Ticker: gar nicht cachen

Eine Seite, die bei jedem Aufruf neu sein muss (ein Live-Ticker, ein Spielstand), sagt `cache_until( 0 )`:

```tpl
{* full/live_ticker.tpl *}
{cache_until( 0 )}
<h1>{$node.name|wash}</h1>
{def $entries = fetch( 'content', 'list', hash(
                    'parent_node_id', $node.node_id,
                    'sort_by',        array( 'published', false() ),
                    'limit',          20 ) )}
{foreach $entries as $entry}
    {node_view_gui content_node=$entry view='line'}
{/foreach}
{undef $entries}
```

Die Seite landet dann in keinem Cache: Der View-Cache speichert die Markierung `no_cache` (wie bei `cache_ttl` = 0),
der HTTP-Cache speichert nichts, `Cache-Control` bekommt `s-maxage=0`, LiteSpeed `X-LiteSpeed-Cache-Control: no-cache`,
und `X-Exp-Cache-Until` ist `0`. Die Markierung im View-Cache bleibt, bis der View-Cache des Knotens geleert wird, etwa
bei der nächsten Veröffentlichung.

Bei vielen Besuchern ist ein sehr kurzer Ablauf oft besser als gar keiner: `{cache_until( '+15 seconds' )}` lässt
Velocity und den HTTP-Cache eine Welle gleichzeitiger Anfragen aus einer einzigen gerenderten Seite beantworten, und
kein Besucher sieht einen Stand, der älter ist als 15 Sekunden.

## 10. Veröffentlichungsfenster

Ein Banner soll nur zwischen zwei Zeitpunkten erscheinen (`show_from`, `show_until`, beide optional). Das Template
zeigt es oder nicht und nennt den nächsten Wechsel:

```tpl
{def $banner = fetch( 'content', 'node', hash( 'node_id', 150 ) )    {* Knoten-ID des Banners *}
     $now    = currentdate()
     $from   = $banner.data_map.show_from.content.timestamp
     $until  = $banner.data_map.show_until.content.timestamp}

{if and( $from|gt( 0 ), $from|gt( $now ) )}
    {* noch nicht sichtbar: Die Seite wechselt, wenn das Fenster aufgeht *}
    {cache_until( $from )}
{elseif or( $until|eq( 0 ), $until|gt( $now ) )}
    <div class="banner">{attribute_view_gui attribute=$banner.data_map.image image_class='large'}</div>
    {if $until|gt( 0 )}
        {* sichtbar: Die Seite wechselt, wenn das Fenster zugeht *}
        {cache_until( $until )}
    {/if}
{/if}
{undef $banner $now $from $until}
```

Ein Datumsfeld ohne Wert hat den Zeitstempel 0; deshalb prüft das Template `gt( 0 )`, bevor es den Wert an
`cache_until` gibt. Ein Aufruf mit 0 hieße „nicht behalten“.

Inhalte, die zeitgesteuert **veröffentlicht** oder versteckt werden (über einen Workflow oder einen Cronjob), brauchen
das nicht: Die Veröffentlichung leert die Caches. `cache_until` ist für alles, was erst das Template aus der Zeit
ableitet.

## 11. Umstieg von cache_ttl

Bisher sagte eine Vollansicht mit `cache_ttl`, wie lange ihr View-Cache gilt. Das funktioniert weiter: Beim Erzeugen
der Ansicht wird `cache_ttl` = N zu `cache_until( time() + N )`, `cache_ttl` = 0 zu `cache_until( 0 )`. Sie müssen
nichts ändern. `cache_ttl` wirkt aber nur in der Inhaltsansicht, `cache_until` überall, und ein Zeitpunkt ist meist
genauer als eine Dauer.

| Vorher | Nachher |
|---|---|
| `{set-block scope=root variable=cache_ttl}{$sekunden_bis_mitternacht}{/set-block}` | `{cache_until( 'tomorrow' )}` |
| `{set-block scope=root variable=cache_ttl}3600{/set-block}` | `{cache_until( '+1 hour' )}`, besser der Zeitpunkt, zu dem sich die Seite wirklich ändert |
| `{set-block scope=root variable=cache_ttl}0{/set-block}` | `{cache_until( 0 )}` |
| `{cache-block expiry=900}` für einen zeitabhängigen Teil | `{cache-block}` mit `{cache_until( … )}` darin |
| ein kurzes `cache_ttl` in einer Ansicht, weil der Kopf des Pagelayouts zeitabhängig ist | `cache_until` im Pagelayout selbst |

`{set-block scope=root variable=cache_ttl}400{/set-block}` und `{cache_until( '+400 seconds' )}` sind gleichwertig.
Schreiben Sie nicht `{cache_until( 400 )}`: Das ist ein Zeitpunkt im Jahr 1970.

## 12. Prüfen mit curl und der Debug-Leiste

**Der Header.** Jede Seite mit einem Ablauf trägt `X-Exp-Cache-Until`:

```bash
curl -sI https://example.com/Veranstaltungen | grep -iE 'cache-control|expires|x-exp-cache'
```

Um 21:14 Uhr in Europe/Berlin, für eine Seite mit `{cache_until( 'tomorrow' )}`, sieht das so aus:

```
cache-control: no-cache, must-revalidate
expires: Mon, 26 Jul 1997 05:00:00 GMT
x-exp-cache-until: 2026-11-16T00:00:00+01:00
```

`Cache-Control` bleibt hier unverändert: Es nennt kein `max-age`, und die verbleibenden fast drei Stunden liegen über
`velocity.ini [CacheSettings] DefaultTtl` (30 Sekunden), sodass Velocity die Seite ohnehin kürzer hält. Fünfzehn
Sekunden vor Mitternacht dagegen:

```
cache-control: no-cache, must-revalidate, s-maxage=15
x-exp-cache-until: 2026-11-16T00:00:00+01:00
```

Ein vorhandenes `max-age` oder `s-maxage` (etwa aus `[HTTPHeaderSettings]`) wird auf die verbleibenden Sekunden
gesenkt, nie erhöht, und ein späteres `Expires` ebenso.

Mit eingeschalteter Debug-Ausgabe nennt der Header auch, was den Ablauf gesetzt hat:

```
x-exp-cache-until: 2026-11-16T00:00:00+01:00; source="design/mysite/override/templates/full/event_folder.tpl"
```

Mögliche Quellen: ein Template, `cache-block` (ein Block kam aus dem Cache), `view cache: …` (die Ansicht kam aus dem
View-Cache; dahinter steht, was ihren Ablauf gesetzt hatte) oder `cache_ttl=N`. Eine Seite, die nicht behalten wird, zeigt `x-exp-cache-until: 0`.

**Der HTTP-Cache.** Ist der rollenbewusste HTTP-Cache eingeschaltet, zeigt `X-Exp-Cache`, was er getan hat:

```bash
curl -sI https://example.com/Veranstaltungen | grep -iE 'x-exp-cache|cache-control'
curl -sI https://example.com/Veranstaltungen | grep -iE 'x-exp-cache|cache-control'
```

Die erste Anfrage zeigt `MISS`, die zweite `HIT` mit `cache-control: public, max-age=300`, kurz vor dem Ablauf mit den
verbleibenden Sekunden. Nach dem Ablauf ist es ein `MISS (expired (cache_until))`. Ist der Ablauf beim Rendern schon
erreicht, wird nichts gespeichert: `BYPASS (cache_until)`, oder `BYPASS (cache_ttl=0)`, wenn
`cache_until( 0 )` in der Inhaltsansicht selbst steht.

**Die Debug-Leiste.** Im Reiter **Rendering** zeigt ein Feld „Until 00:00“ (der Ablauf) oder „Not cached“ (die Seite
wird nicht behalten). Der Tooltip nennt den Zeitpunkt, die verbleibenden Sekunden, was ihn gesetzt hat und wie oft
`cache_until` aufgerufen wurde. Siehe [Die Exp-Debug-Leiste](../funktionen/6.0/exp-debug-leiste.md).

**Die Debug-Ausgabe.** Eine Zeile fasst zusammen:

```
cache_until: kept until 2026-11-16T00:00:00+01:00 (9960 s), set by design/mysite/override/templates/full/event_folder.tpl, 3 calls
```

Dazu kommen Warnungen für ignorierte Werte (`cache_until() ignored in …: no time given (NULL)`) und Hinweise für
Zeitpunkte in der Vergangenheit (`… the time 3600 has passed; for a lifetime in seconds write '+N seconds'; the page is
not kept`).

## 13. Zeitzone einrichten

`'tomorrow'`, `'18:00'` und `'monday 09:00'` werden in der Zeitzone der Website gelesen: `site.ini [TimeZoneSettings]
TimeZone`, und wenn sie leer ist, `date.timezone` aus der `php.ini`. Die ist auf vielen Servern UTC oder die Zone eines
Container-Images. Eine Website in Deutschland, deren Server in UTC läuft, liefe sonst um 1 Uhr nachts (im Sommer um 2
Uhr) ab statt um Mitternacht.

Neue Installationen schreiben die Zone selbst (aus `--timezone`, `kickstart.ini`, der Hauptsprache oder der
`php.ini`; siehe [die Funktionsseite](../funktionen/6.0/cache-until.md#die-zeitzone-bei-der-installation)).
Bestehende Websites werden nicht geändert. **Setup > Systeminformationen** zeigt einen Hinweis, wenn die Website keine
eigene Zeitzone hat ([Systeminformationen](systeminformationen.md)).

So setzen Sie sie, in `settings/override/site.ini.append.php`:

```ini
[TimeZoneSettings]
TimeZone=Europe/Berlin
```

Dann:

```bash
./console exp:velocity deploy --allow-root-user
```

oder, ohne Velocity, den INI-Cache leeren und den PHP-FPM-Dienst neu laden, der Ihre Website ausliefert (sein Name
hängt vom Server ab, unter Plesk zum Beispiel `plesk-php85-fpm`):

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
systemctl reload php-fpm
```

Prüfen Sie danach `X-Exp-Cache-Until` einer Seite mit `{cache_until( 'tomorrow' )}`: Der Versatz am Ende
(`+01:00` im Winter, `+02:00` im Sommer für Europe/Berlin) zeigt die Zone.

> **Achtung**
> Eine geänderte Zeitzone verschiebt jedes Datum, das die Website zeigt, und die Zeitpunkte der zeitgesteuerten
> Veröffentlichung. Prüfen Sie nach der Umstellung einige Seiten mit Datumsangaben.

## 14. Häufige Fehler

**Die Seite wird nie gecacht.** Die Debug-Leiste zeigt „Not cached“. Meist bekommt `cache_until` einen Zeitpunkt in
der Vergangenheit: eine Zahl als Dauer (`cache_until( 3600 )`), den Beginn eines Termins, der schon begonnen hat, ein
leeres Datumsfeld (Zeitstempel 0) oder ein festes Datum, das vorbei ist. Der Hinweis in der Debug-Ausgabe nennt den
Wert und das Template.

**Die Seite läuft nicht ab.** Kein `X-Exp-Cache-Until`-Header: Der Aufruf lief nicht, oder sein Wert wurde ignoriert
(eine fehlende Variable, ein Tippfehler im Attributnamen, ein Text, der keine Zeit ist); die Warnung in der
Debug-Ausgabe sagt es. Oder die Seite kommt aus dem **statischen Cache**, der keinen Ablauf kennt: Nehmen Sie sie dort
aus. Oder ein CDN mit einer festen Edge-TTL hält sie ([CDN](../hilfe/hosting/leistung/cdn.md)).

**Mitternacht ist eine Stunde daneben.** Die Website hat keine eigene Zeitzone ([Abschnitt 13](#13-zeitzone-einrichten)).

**Die Seite lief ab, zeigt aber noch das Alte.** Prüfen Sie zuerst, ob das Template selbst nach dem Ablauf etwas
anderes ausgibt: Die neue Ausgabe entsteht nur, wenn die Daten sich aus der Zeit ableiten, etwa über einen `fetch` mit
`currentdate()` im Filter. Prüfen Sie dann die Schichten vor Exponential: Läuft die Website unter LiteSpeed, beachtet dessen
Seiten-Cache das `Cache-Control` der Antwort oft nicht; schalten Sie `site.ini [CacheUntilSettings]
LiteSpeedMaxAge=enabled` ein, wenn die betroffenen Seiten zu denen gehören, die Ihr LiteSpeed-Block cacht
([Seiten-Caching mit LiteSpeed und OpenLiteSpeed](seiten-cache-mit-litespeed.md#wie-er-zu-den-caches-von-exponential-steht)).

**Ein später Aufruf verlängert nichts.** Das ist Absicht: Der früheste Zeitpunkt gewinnt. Ein Template kann eine Seite
nicht länger behalten lassen, als ein anderes erlaubt.

**`expiry=` am Cache-Block begrenzt die Seite nicht.** Auch das ist Absicht. Ein Menü-Block mit zwei Stunden lässt die
ganze Seite nicht nach zwei Stunden ablaufen. Wenn der Inhalt des Blocks zu einem Zeitpunkt falsch wird, sagen Sie es
mit `cache_until` im Block.

**`seconds_until` in der Seite veraltet.** Die Zahl wurde beim Rendern berechnet. Setzen Sie `cache_until` auf den
nächsten Wechsel der angezeigten Zahl, oder geben Sie einem Skript den festen Zielzeitpunkt
([Abschnitt 5](#5-countdowns-mit-seconds_until)).

**Ein Live-Ticker bleibt nach dem Entfernen von `cache_until( 0 )` ungecacht.** Die Markierung `no_cache` im View-Cache
bleibt, bis der View-Cache des Knotens geleert wird. Veröffentlichen Sie den Knoten erneut oder leeren Sie den
Inhalts-Cache.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
