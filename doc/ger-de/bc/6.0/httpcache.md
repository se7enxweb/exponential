# Der rollenabhängige HTTP-Cache

Lesen Sie diese Seite, wenn Sie ganze Seiten nicht nur anonymen, sondern auch angemeldeten Besuchern aus einem Cache
ausliefern möchten, oder wenn Sie einen Header `X-Exp-Cache` sehen und wissen möchten, was er bedeutet. Der
rollenabhängige HTTP-Cache hält ganze gerenderte Seiten pro Berechtigungskontext und liefert sie aus, bevor der Kernel
startet. Er ist standardmäßig ausgeschaltet (`settings/httpcache.ini`, `Enabled=disabled`); nichts ändert sich, bis Sie
ihn einschalten.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neuer Cache `httpcache.ini`; früher Ausstieg `kernel/private/classes/httpcache/ezphttpcacheearlyexit.php`; Velocity liefert dieselben Einträge aus seinem Serverprozess aus. |
| Wer betroffen ist | Niemand, bis `Enabled=enabled` gilt. Dann: Websites mit einer anderen Sitzungsspeicherung als Dateien und Templates, die persönliche Daten ausgeben. |
| Wie Sie es prüfen | `curl -sI https://your-host/ \| grep -i x-exp-cache` zeigt `HIT`, `STALE`, `MISS (reason)` oder `BYPASS (reason)`, sobald er eingeschaltet ist. |
| Wie Sie ihn einschalten | Folgen Sie den drei Schritten unter „Einschalten“. |

## Was er tut

Eine Inhaltsansicht (`content/view`, jeder URL-Alias) wird nach dem Rendern gespeichert, geschlüsselt nach Schema,
Host, Siteaccess, Pfad und dem **Berechtigungskontext** des Besuchers:

- **anonym** — ein Kontext für alle, die nicht angemeldet sind;
- **Rollenkontext** — geteilt von allen Benutzern mit denselben Rollen und denselben Einschränkungen der
  Rollenzuweisung, sodass sich tausend Mitglieder eine Kopie jeder Seite teilen;
- **privater Kontext** — ein Benutzer allein, wenn eine Richtlinie `content/read` davon abhängt, wer er ist (`Owner`,
  `ParentOwner`, `Group`, `ParentGroup`, `User_Section`, `User_Subtree`).

Die nächste Anfrage nach derselben Seite im selben Kontext wird aus dem Speicher beantwortet:

- unter Apache oder php-fpm von `kernel/private/classes/httpcache/ezphttpcacheearlyexit.php`, eingebunden aus
  `config.php`, vor dem Autoloader, den INI-Dateien und der Datenbank;
- unter Exponential Velocity vom Serverprozess selbst (`Q.web.appCache`, von `exp:velocity` geschrieben), bevor ein
  Worker geweckt wird.

Beide verwenden dieselbe Klasse, `ezpHttpCacheContract`, daher berechnen sie dieselben Schlüssel und liefern dieselben
Bytes aus. Das Formular-Token des Besuchers wird aus der gespeicherten Seite entfernt und bei jedem Treffer das eigene
des Besuchers wieder eingesetzt.

## Gemessen auf einer Referenzinstallation (2026-09-26, eine Inhaltsseite, 8 gleichzeitig)

| | Treffer | Vollständiges Rendern |
|---|---|---|
| Apache, anonym | 508 Anfr./s, p50 14 ms | 28 Anfr./s, p50 220 ms |
| Apache, angemeldet | 484 Anfr./s, p50 15 ms | 4,3 Anfr./s, p50 1,86 s |
| Velocity, anonym | 1392 Anfr./s, p50 4 ms | — |
| Velocity, angemeldet | 1156 Anfr./s, p50 5 ms | 4,3 Anfr./s, p50 1,84 s |

## Einschalten

```ini
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
Enabled=enabled
```

Dann:

1. **Binden Sie den frühen Ausstieg aus `config.php` ein** (Apache, php-fpm, FrankenPHP):

   ```php
   if ( is_file( __DIR__ . '/kernel/private/classes/httpcache/ezphttpcacheearlyexit.php' ) )
       require __DIR__ . '/kernel/private/classes/httpcache/ezphttpcacheearlyexit.php';
   ```

2. **Halten Sie Sitzungen in Dateien, die jeder Server lesen kann.** Der frühe Ausstieg findet den Besucher über die
   Sitzungsdatei, die das Sitzungs-Cookie nennt; er kann nicht Redis oder eine Datenbank fragen. Jeder Server, der einen
   gecachten Siteaccess ausliefert, muss dasselbe Verzeichnis verwenden — der Pool von Apache und Velocity
   (`[PHPSettings] IniOptions[]=session.save_path=0;0660;<dir>` in `velocity.ini`). Ein Server, dessen
   Sitzungsspeicherung sich von der unterscheidet, die der Cache verzeichnet hat, speichert für angemeldete Besucher
   nichts und sagt das in `X-Exp-Cache`.

3. **Starten Sie Velocity neu** (`exp:velocity restart`), damit es die Kernel-Klassen lädt und den Cache fragt.

Die erste Anfrage auf einem gecachten Siteaccess schreibt die Konfiguration, die der frühe Ausstieg liest
(`var/<site>/cache/exphttpcache/contract.php`, mit einem erzeugten Schlüssel).

## Einstellungen

Datei `settings/httpcache.ini`, Block `[HttpCacheSettings]`, Geltungsbereich: Installation. Überschreiben in
`settings/override/httpcache.ini.append.php`.

| Einstellung | Standard | |
|---|---|---|
| `Enabled` | `disabled` | Solange ausgeschaltet, wird nichts gespeichert oder ausgeliefert. Das Ausschalten wirkt bei der nächsten Anfrage, die den Kernel erreicht. |
| `CachedSiteAccesses[]` | `site` | Siteaccesses, zugeordnet nach Host (map), nach URI (`element`, `map`) oder nach `host_uri`; siehe „Welcher Siteaccess“ unten. |
| `MaxAge` | `3600` | Längste Lebensdauer einer Seite, auch wenn nichts sie leert. Ein `cache_ttl` eines Templates verkürzt sie; `cache_ttl=0` hält die Seite heraus, wie beim View-Cache. |
| `StaleWhileRevalidate` | `60` | Eine abgelaufene (nicht geleerte) Seite wird als `STALE` ausgeliefert, während eine Anfrage sie neu rendert. |
| `ContentChangePurges` | `all` | `all`: Jede Inhaltsänderung leert jede Seite. `tags`: nur Seiten, die die geänderten Objekte, Knoten, Elternknoten zeigen, und Seiten mit Abfrageblöcken. |
| `QueryStringParameters[]` | keine | Seiten, die mit einem anderen Query-Parameter angefragt werden, werden nicht gecacht. |
| `TagHeader` | `disabled` | Die Leerungs-Tags jeder Seite in einem Header, für einen vorgeschalteten Proxy, der leert, oder beim Debuggen: `xkey` (xkey-vmod von Varnish), `Surrogate-Key` (Fastly), `Cache-Tag` (Cloudflare). Standardmäßig aus: Die Tags nennen interne IDs. `ProxyHeaders=enabled`, die Einstellung davor, bedeutet weiterhin `xkey`. |
| `APCu` | `enabled` | Häufig genutzte Einträge in APCu vor den Dateien. |
| `MaxBodySize` | 2 MB | Größere Seiten werden nicht gespeichert. |

## Welcher Siteaccess, welches Schema und welcher Host

Der frühe Ausstieg und Velocity antworten, bevor der Kernel läuft, daher ermitteln sie Siteaccess, Schema und Host
selbst, aus Werten, die der Kernel in `contract.php` schreibt:

- **Siteaccess** (`ezpHttpCacheContract::resolveSiteAccess()`): die Regeln von `eZSiteAccess::match()` auf den
  Einstellungen von `site.ini` ohne Overrides der Siteaccesses -- `StaticMatch`, dann `MatchOrder` mit `uri`
  (`URIMatchType` `element` oder `map`), `host` (`HostMatchType=map`) und `host_uri` (`HostUriMatchMapItems`, jede
  Methode der Host-Zuordnung), dann `DefaultAccess`. Eine Anfrage, die eine Regel erreicht, der er nicht folgen kann
  (`port`, `servervar`, `index`, die Typen `text` und `regexp`, ein Name, den der Kernel normalisieren würde), wird
  nicht früh ausgeliefert. `/bold_ger/kontakt` auf einer Website mit `MatchOrder=uri;host` ist `bold_ger`, wie beim
  Kernel.
- **Schema und Host** (`ezpHttpCacheContract::requestOrigin()`): wie `eZSys::hostname()` und `eZSys::isSSLNow()` --
  `X-Forwarded-Host`, dann `Host`; `HTTPS`, der Port gegenüber `SSLPort`, `X-Forwarded-Proto`, `X-Forwarded-Port`,
  `X-Forwarded-Server`. Hinter einem Load Balancer, der TLS beendet und an `exp:8080` weiterleitet, wird eine Seite für
  `https://www.example.org/...` gehalten, die Adresse, die der Besucher angefragt hat, und darunter wiedergefunden. Der
  frühe Ausstieg liest `$_SERVER`; der Serverprozess von Velocity braucht eine Engine, die die Anfrage-Header übergibt
  (Exponential Velocity v0.0.4.35 oder neuer), und verfehlt sonst hinter einem Load Balancer.
- **`RemoveSiteAccessIfDefaultAccess` und `PathPrefix`** brauchen nichts Eigenes. Eine Seite wird unter der
  vollständigen Anfrage-URI gehalten, wie der Besucher sie gesendet hat, daher sind `/kontakt` (der
  Standard-Siteaccess, dessen Name in Links weggelassen wird) und `/bold_ger/kontakt` zwei Einträge, jeder über die URI
  wiedergefunden, die ihn erzeugt hat; `PathPrefix` ändert, welchen Knoten eine URI zeigt, nicht unter welchem
  Siteaccess oder welcher URI die Seite gehalten wird.

Der Kernel speichert eine Seite nur, wenn diese den Siteaccess, das Schema und den Host ergeben, mit denen er die Seite
gerendert hat (sonst `X-Exp-Cache: BYPASS (siteaccess not known before the kernel)` oder
`BYPASS (scheme or host not known before the kernel)`). Eine Regel, der hier anders gefolgt wird, kostet ein Rendern,
nie eine Seite eines anderen Siteaccess, Schemas oder Hosts.

## Wann Seiten geleert werden

- **Ein Bearbeiten, Veröffentlichen, Verschieben, Verstecken oder Löschen** — das Leeren des View-Caches, das der
  Kernel bereits durchführt, löst `content/cache` aus; mit `ContentChangePurges=all` verschwindet jede Seite, mit `tags`
  die Seiten, die mit dem Geänderten getaggt sind: die Tags, die der HTTP-Cache von Ibexa leert, `l`, `pl` und `rl` jedes
  Knotens, `c` und `r` jedes Objekts. Die eigenen Seiten von Exponential tragen kein Tag `r` oder `rl`; diese beiden
  leeren Seiten, die eine Erweiterung auf die Ibexa-Art damit getaggt hat (`ezpHttpCacheListener::addTags()`). Die
  Leerung gehört diesem Cache allein: An einen vorgeschalteten Proxy wird nichts gesendet (`TagHeader` zeigt die Tags
  nur an). Mehr als 10000 innerhalb von `MaxAge` geleerte Tags leeren stattdessen jede Seite, damit der Zustand, den
  jeder Lookup liest, klein bleibt. Skripte und Cronjobs leeren ebenfalls (`eZScript` hängt die Listener an).
- **Layout-Editor** — jede erfolgreiche schreibende Anfrage an ein Modul `explayouts*` leert jede Seite. Layout-Blöcke
  taggen die Seite mit jedem Element, das sie zeigen, und bei Abfrageblöcken mit `dq`, das jede Inhaltsänderung leert.
- **Rollen, Richtlinien, Zuweisungen** — jeder Kontext ändert sich (eine neue Generation).
- **Die eigenen Daten eines Benutzers** — der Kontextdatensatz dieses Benutzers wird vergessen.
- **Setup > Caches, `bin/php/ezcache.php`** — „HTTP-Cache (rollenabhängige Seiten)“, `--clear-id=exphttpcache` und mit
  `--clear-tag=content` oder `template`.

Was eine Seite sonst zeigt, wird beim Rendern getaggt, das Pagelayout eingeschlossen: `fetch( 'content', 'list' )` und
`list_count` der Kinder eines Elternknotens taggen `pl<parent>` (jede Änderung an einem davon leert sie), tiefere
`tree`-Fetches taggen `dq`, und `fetch( 'content', 'node' )` taggt `l<node>`. Auflistungen innerhalb einer
Inhaltsansicht, die aus dem View-Cache kommt, werden nicht erneut geholt und folgen dem View-Cache: Die Seite wird
geleert, wenn dieser View-Cache geleert wird. Operatoren, die Inhalte auf andere Weise lesen, werden nicht getaggt,
deshalb bleibt `ContentChangePurges=all` der Standard; `tags` ist für Installationen, die ihre Templates kennen.

## Sehen, was er tut

- Jede Antwort sagt `X-Exp-Cache: HIT`, `STALE`, `MISS (reason)` oder `BYPASS (reason)`. Unter Velocity sagt eine
  Seite, die vom eigenen Antwort-Cache des Servers beantwortet wird (anonyme Besucher), außerdem `X-Cache: HIT`; ihr
  `X-Exp-Cache` ist das mit ihr gespeicherte, oft `MISS` vom Rendern, das beide Caches gefüllt hat.
- **Setup > Systeminformationen** hat einen Kasten „HTTP-Cache (rollenabhängig)“: Trefferquote über alle Server, warum
  Anfragen verfehlt haben, Einträge, Plattenbelegung, Benutzerkontexte, die letzte Leerung, die Einstellungen und
  Schaltflächen, um alles zu leeren, tote Einträge zu entfernen und die Zähler zurückzusetzen.
- Der Cronjob-Teil `cache_cleanup` entfernt abgelaufene, geleerte und verwaiste Dateien
  (`cronjobs/httpcache_cleanup.php`).

## Was nie gecacht wird

POST und andere Schreibvorgänge; Antworten außer 200; Antworten, die ein eigenes Cookie setzen; Seiten mit einem nicht
erlaubten Query-String; Seiten, deren Template `cache_ttl=0` setzt; Siteaccesses, die nicht in `CachedSiteAccesses`
stehen oder über eine Regel gefunden werden, die sich vor dem Kernel nicht ermitteln lässt; alles, was keine
Inhaltsansicht ist; eine Anfrage, die ein vorgeschaltetes [request-shield](https://github.com/cjw-network/request-shield)
als nicht für einen Cache markiert hat (`REQUEST_SHIELD=allow-uncached`: ein erfundener Pfad oder Parameter); eine Seite,
die den Debug-Bericht ihrer Anfrage trägt (die Leiste Exp Debug oder der klassische Bericht,
`X-Exp-Cache: BYPASS (debug report)`): Ihre Zeiten, Abfragen, Meldungen und die eigene Panelhöhe des Betrachters gehören
zu dieser Anfrage, und ein nach IP angezeigter Bericht würde sonst jeden erreichen, der den anonymen Kontext teilt.

## Mit vorgeschaltetem request-shield

[request-shield](https://github.com/cjw-network/request-shield) läuft vor `config.php` (`auto_prepend_file` oder die
erste Zeile von `index.php`). Sein HTTP-Cache bleibt unter Exponential 6 aus — dieser hier ist der Seiten-Cache —, und
beide arbeiten zusammen:

- **Nie behalten:** Eine Anfrage, die request-shield als `allow-uncached` markiert (ein Pfad oder Query-Parameter
  außerhalb seiner cachebaren Definition), wird beantwortet und nie gespeichert: Zufällige Adressen können den Cache
  nicht füllen (`X-Exp-Cache: BYPASS (request-shield: not for a cache)`).
- **Weniger Schlüssel:** Mit `cache-ignore utm_* gclid …` nimmt request-shield Tracking-Parameter aus `REQUEST_URI`,
  bevor der frühe Ausstieg läuft, sodass ein Kampagnenlink derselbe Eintrag wie die Seite ist.
- **Aus der Seite ohne erfundenen Parameter beantwortet:** Mit `set cache-unknown-query hit-only` nennt request-shield in
  `REQUEST_SHIELD_CACHE_LOOKUP` die Adresse, aus der eine Anfrage mit einem Parameter, den kein Schlüssel enthält,
  beantwortet werden darf. Der frühe Ausstieg übernimmt sie nur, wenn sie den eigenen Pfad der Anfrage nennt und genau
  die eigenen Parameter der Anfrage, die `QueryStringParameters[]` aufführt, in beliebiger Reihenfolge
  (`X-Exp-Cache: HIT (request-shield lookup)`); ein Shield, dessen `cache-query` einen davon auslässt, erhält einen
  Fehltreffer, nie eine andere Seite einer Liste. Für die eigene Adresse der Anfrage wird nichts gespeichert, die
  Antwort sagt `Cache-Control: private`, sodass auch ein vorgeschalteter Proxy keine behält, und eine abgelaufene Seite
  wird als `STALE` ausgeliefert, ohne dass der Lookup ihre Erneuerung übernimmt.
- **Seine Statistik** liest `X-Exp-Cache` (HIT, STALE, MISS, BYPASS, jeweils eventuell gefolgt von einem Grund in
  Klammern): die Antwortzeiten nach Treffer und Fehltreffer in seinem Dashboard.

**Unter Velocity** antwortet der Serverprozess aus diesem Cache vor einem Worker und damit vor request-shield, das im
Worker ab der ersten Zeile von `index.php` läuft: Es sieht die Adresse so, wie der Besucher sie gesendet hat, ohne
`cache-ignore` und ohne `REQUEST_SHIELD_CACHE_LOOKUP`, daher spart dort keines von beiden ein Rendern. Der frühe
Ausstieg läuft in einem Worker überhaupt nicht. Was gilt, ist **Nie behalten**: Der Listener, der die Seite speichert,
läuft im Worker nach request-shield, daher wird eine Seite mit `allow-uncached` auch unter Velocity nicht gespeichert.
Velocity behält in einem persistenten Worker Einträge von `$_SERVER`, deren Name mit `REQUEST_` beginnt, von einer
Anfrage zur nächsten; schalten Sie request-shield mit `set mode off` aus (es setzt `REQUEST_SHIELD` weiterhin bei jeder
Anfrage), nicht durch Entfernen des Includes, sonst behält ein Worker das `allow-uncached` der letzten Anfrage und
speichert nichts, bis er neu gestartet wird.

## Header, die eine Erweiterung einer Seite hinzufügt

Eine aus diesem Cache beantwortete Seite wird ohne den Kernel zusammengesetzt, aus den mit ihr gespeicherten Headern:
dem Inhaltstyp und den Sicherheits-Headern. Ein Header, den eine Erweiterung für eine Seite hinzufügt (zum Beispiel eine
Inhaltslizenz als `Link` und `X-Content-License`), würde bei jedem Treffer fehlen. Ab dem 2026-10-11 übergibt der
Listener die Header, die er gleich speichert, an das Filter-Ereignis `httpcache/store/headers`; ein Listener gibt sie
mit seinen eigenen ergänzt zurück:

```ini
# extension/myext/settings/site.ini.append.php
[Event]
Listeners[]=httpcache/store/headers@myExtHttpCacheHeaders::filter
```

```php
class myExtHttpCacheHeaders
{
    public static function filter( array $headers )
    {
        $headers['X-My-Header'] = 'value for this page';
        return $headers;
    }
}
```

Ein Listener, der etwas anderes als ein Array zurückgibt, wird ignoriert. Die Header werden mit dem Eintrag
gespeichert, daher trägt ein Treffer sie wie die gerenderte Seite; halten Sie sie frei von allem Persönlichen, denn
jeder Besucher desselben Berechtigungskontexts erhält dieselbe Kopie. Filter-Ereignisse sind unter
[RAD-Erweiterungspunkte](rad-erweiterungspunkte.md) aufgeführt.

## Sicherheit

- Schlüssel und Kontexte sind HMACs mit einem erzeugten Schlüssel (`secret`, 0600), nie gesendet.
- Ein Besucher mit einem Sitzungs-Cookie, das der Cache nicht auflösen kann, ist ein Fehltreffer, nie die anonyme
  Seite; ein unbekannter Name eines Sitzungs-Cookies ist ein Fehltreffer.
- Ein angemeldeter Besucher erhält nur Seiten seines eigenen Berechtigungskontexts; eine Änderung der Rollen verschiebt
  jeden Benutzer sofort in einen neuen Kontext.
- Formular-Tokens gelten pro Besucher und werden bei jedem Treffer wieder eingesetzt; andere persönliche Daten dürfen
  nicht in einer gecachten Seite stehen — Seiten, die den Namen des Besuchers zeigen, gehören in einen privaten
  Kontext oder aus dem Cache heraus (`cache_ttl=0`).
- Von root geschriebene Dateien (ein Leeren über die CLI) werden dem Besitzer des Cache-Verzeichnisses übergeben,
  sodass der Webserver sie weiterhin aktualisieren kann.

## Dateien

| | |
|---|---|
| `kernel/private/classes/httpcache/ezphttpcachecontract.php` | Schlüssel, Kontexte, Einträge, Zustand, Sitzungen, Platzhalter, Ausliefern; reines PHP |
| `kernel/private/classes/httpcache/ezphttpcacheearlyexit.php` | Der frühe Ausstieg, eingebunden aus `config.php` |
| `kernel/private/classes/httpcache/ezphttpcachelistener.php` | Speichern nach dem Rendern, Leerungen, Datensatz bei der Anmeldung |
| `settings/httpcache.ini` | Einstellungen |
| `cronjobs/httpcache_cleanup.php` | Entfernt tote Einträge |
| `tests/tests/kernel/classes/httpcache/ezpHttpCacheContractTest.php` | Unit-Tests (HC-01 … HC-16) |

## Verwandte Seiten

- [Verhaltensänderungen vom 16. bis 30. September 2026: HTTP-Cache](../../../eng-us/bc/6.0/behaviour-changes-2026-09b.md#http-cache-httpcacheini)
  (Kompression einmal, Header auf gecachten Seiten, Zuordnung der Siteaccesses)
- [Sicherheitsstandards](../../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md)
- [Velocity](../../../eng-us/features/6.0/velocity-persistent-worker-server.md)
- [HTTP-Caching für anonyme Besucher](http-caching.md) und [Cache-Steuerung über die Konsole](cache-konsole.md)
- [Chronik, 16. bis 30. September 2026](../../../eng-us/history/2026/2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Cache-Leerungen, die Verzeichnisse beiseiteschieben](../../../eng-us/features/6.0/cache-clear-rename-aside.md)
- [Antwort-Cache von Velocity](../../../eng-us/features/6.0/velocity-response-cache.md)
