---
title: Antwort-Cache von Velocity
audience: hosting, admin
category: hosting
order: 50
summary: Der Antwort-Cache im Elternprozess von Velocity für gehostete Websites - was er speichert und nie speichert, die Einstellungen in velocity.ini und ihre Abwägungen, bedingte Anfragen und stale-while-revalidate, 404 und Logs, Leeren nach einem Ausrollen, Treffer prüfen und der Navigations-Cache im Browser.
keywords: Velocity, Antwort-Cache, CacheSettings, DefaultTtl, StaleWhileRevalidate, NotFoundSeconds, HonorRequestNoCache, MinifyHtml, SkipCookies, APCu, X-Cache, Age, ETag, 304, exp:velocity cache clear, Generationsmarkierung, Navigations-Cache, index.js, sw.js, response cache, generation marker, navigation cache
type: help
---
# Antwort-Cache von Velocity

Diese Seite ist für Systemadministratoren und Support-Mitarbeitende bei Hosting-Anbietern, deren Kunden-Websites auf Velocity laufen.
Sie beantwortet: was der Antwort-Cache speichert, welche Einstellungen entscheiden, wie frisch Seiten sind, wie Sie prüfen, dass er
funktioniert, wie Sie ihn leeren und was Sie einem Kunden sagen, der eine alte Seite sieht. Die Quellen sind
[Kapitel 8, Abschnitt 8.3.8](../../../installation/08-die-website-ausliefern.md#838-der-antwort-cache),
[Antwort-Cache von Velocity](../../../../eng-us/features/6.0/velocity-response-cache.md) und
[Der Antwort-Cache, und dem Netzwerk aus dem Weg gehen](../../../../eng-us/bc/6.0/response-cache-and-navigation.md).

## Inhalt

1. [Was er tut](#was-er-tut)
2. [Was er nie speichert](#was-er-nie-speichert)
3. [Die Einstellungen](#die-einstellungen)
4. [Frische gegen Geschwindigkeit](#frische-gegen-geschwindigkeit)
5. [Ihn prüfen](#ihn-prüfen)
6. [Ihn leeren](#ihn-leeren)
7. [Der Navigations-Cache im Browser](#der-navigations-cache-im-browser)
8. [Antworten für den Support](#antworten-für-den-support)
9. [Checkliste](#checkliste)

## Was er tut

Der Antwort-Cache hält gerenderte anonyme Seiten und beantwortet wiederholte Anfragen innerhalb des Serverprozesses, bevor ein Worker
gewählt wird: kein Fork, kein PHP, keine Datenbank. Gemessen auf einem Host mit 12 Kernen, eine zwischengespeicherte Startseite über TLS
mit gzip: etwa 2.950 Seiten pro Sekunde von einem Server, 11.985 von vier Servern auf einem Port
([Antwort-Cache von Velocity](../../../../eng-us/features/6.0/velocity-response-cache.md)). Er verhält sich wie ein kleiner Reverse Proxy:

- bedingte Anfragen werden mit `304` beantwortet, mit einem `ETag`, das aus der Seite abgeleitet ist, nicht aus der Uhr, sodass eine
  unveränderte Seite einen wiederkehrenden Browser 202 Byte Header kostet statt des ganzen Dokuments;
- stale-while-revalidate: Eine Anfrage rendert eine abgelaufene Seite, während die anderen die alte Kopie bekommen;
- `Date` bei jeder Antwort, `Age` und `X-Cache` bei zwischengespeicherten;
- kleine Einträge in APCu (von den Workern geteilt), größere auf der Festplatte in `var/cache/qbix-reverse`.

## Was er nie speichert

Aus [Kapitel 8](../../../installation/08-die-website-ausliefern.md#838-der-antwort-cache) und der Seite der Funktion:

- angemeldete Besucher werden nie daraus bedient; eine Seite mit einem Formular-Token wird `private` gesendet;
- eine Anfrage mit einem Cookie, das in `SkipCookies` genannt ist (leer bedeutet das Sitzungscookie und `is_logged_in`, das der Kernel
  genau für diesen Zweck für angemeldete Besucher setzt), oder mit einem Header `Authorization`;
- eine Antwort, die ein Cookie setzt oder `no-store` oder `private` sagt;
- alles außer `GET`, und die eigenen Pfade des Servers `/Q/` und `/.well-known/`;
- ein anderer Status als 200 (ein 404 nur, wenn `NotFoundSeconds` über 0 liegt).

> **Warnung**
> Der Antwort-Cache braucht qbix-webserver 0.0.4.26 oder später: Ältere Versionen erkannten das Sitzungscookie nur an seinem genauen
> Namen, erkannten das Sitzungscookie pro Siteaccess nie und lieferten die Seiten eines angemeldeten Besuchers an alle aus
> (`settings/velocity.ini`). Halten Sie die Engine aktuell ([Velocity für Hoster](../server/velocity-fuer-hoster.md)).

## Die Einstellungen

`settings/velocity.ini`, `[CacheSettings]` (überschrieben in `settings/override/velocity.ini.append.php`, dann neu starten):

| Einstellung | Mitgeliefert | Bedeutung |
|---|---|---|
| `Enabled` | `enabled` | der Cache ist an |
| `DefaultTtl` | `30` | Sekunden, die eine anonyme Seite behalten wird, wenn sie keine eigene Lebensdauer mitbringt |
| `Dir` | `var/cache/qbix-reverse` | Festplattenstufe für Einträge, die zu groß für den gemeinsamen Speicher sind |
| `APCu`, `APCuMaxSize` | `enabled`, `0` | kleine Einträge in APCu; `0` belässt die 65536 Byte des Servers |
| `SkipCookies` | leer | Cookies, die „persönlich, nicht zwischenspeichern“ bedeuten; eine Liste ersetzt den Standard |
| `StaleWhileRevalidate` | `60` | Sekunden, die eine abgelaufene Seite noch ausgeliefert wird, während eine Anfrage ihren Ersatz rendert |
| `NotFoundSeconds` | `0` | Sekunden, die ein 404 gemerkt wird; `0` schickt jedes 404 an PHP |
| `HonorRequestNoCache` | `enabled` | eine Anfrage mit `Cache-Control: no-cache` erreicht PHP, und ihre Antwort wird erneut gespeichert |
| `SweepEvery`, `SweepMaxAge` | `300`, `86400` | abgelaufene Einträge von der Festplatte räumen |
| `FileMode`, `DirMode` | `0640`, `0750` | nichts anderes auf der Maschine muss eine zwischengespeicherte Seite lesen |
| `MinifyHtml` | `enabled` | gespeicherte Seiten ohne Einrückung der Templates (27 % kleinere Dokumente) |
| `MiddleOutCompression` | `disabled` | gemessen und ausgeschaltet gelassen |

## Frische gegen Geschwindigkeit

| Einstellung | Erhöhen Sie sie, und | Senken Sie sie, und |
|---|---|---|
| `DefaultTtl` | mehr Seiten kommen aus dem Cache; ein Besucher sieht nach einer Bearbeitung vielleicht eine Seite, die bis zu so alt ist | frischere Seiten, mehr Renderings |
| `StaleWhileRevalidate` | ein Ablauf schickt nie alle Besucher gleichzeitig an PHP | eine abgelaufene, viel besuchte Seite kann eine Häufung von Renderings bringen |
| `NotFoundSeconds` | ein Crawler, der tote Links abläuft, rendert nicht jeden einzelnen | jedes 404 erreicht PHP, sodass `404.log` und der Request-Klassifizierer (`security.log`) jedes sehen |

Das mitgelieferte `NotFoundSeconds=0` ist eine bewusste Wahl: Ein aus dem Cache beantwortetes 404 tauchte in den Logs höchstens einmal
pro Minute auf. Für einen Hoster, dessen Sicherheitsüberwachung diese Logs liest, bleibt es bei 0.

Die Abwägung, die die Datei für `DefaultTtl` nennt: Eine Seite, die sich zwischen anonymen Besuchern unterscheidet, ohne dass ein Cookie
sie unterscheidet, würde zwischen ihnen geteilt. Ein Kunde, der anonyme Seiten durch etwas anderes als ein Cookie personalisiert, braucht
`DefaultTtl=0` (nur Seiten, die ihre eigene Lebensdauer mitbringen, werden zwischengespeichert).

## Ihn prüfen

Fragen Sie dieselbe anonyme Seite zweimal an; die zweite Antwort trägt `X-Cache: HIT` und einen Header `Age`:

```bash
curl -sI https://example.com/ | grep -iE 'x-cache|age'
curl -sI https://example.com/ | grep -iE 'x-cache|age'
php bin/php/console exp:velocity cache stats     # where it is, how much it holds, when it was cleared
```

`/Q/health` (volle Kennzahlen brauchen Zugang als Administrator) meldet `hits`, `misses`, `hitRate`, `stale` und woher die Treffer
kamen. Überwiegend `disk` bei eingeschaltetem APCu bedeutet, dass APCu seinen Teil nicht leistet: Prüfen Sie `apc.enable_cli`, das
Velocity für den Serverprozess setzt. Der Reiter **Cache** des Kontrollzentrums zeigt die aktuelle Trefferquote und kann nach URL oder
Muster leeren.

## Ihn leeren

```bash
php bin/php/console exp:velocity cache clear     # every cached page is rendered again on its next request
```

- Nach einer Änderung an Template oder Stylesheet: Leeren Sie die Template-Caches, dann `exp:velocity cache clear`; kein Neustart nötig.
- Nach einer Änderung an PHP: `exp:velocity deploy`, das den Antwort-Cache zuletzt leert, nachdem der neue Code läuft.
- Das Leeren funktioniert über eine Generationsmarkierung: Jeder Eintrag, der zu oder vor ihrem Zeitpunkt gespeichert wurde, gilt als
  Fehlschlag, und jeder Worker und jede Instanz, die das Verzeichnis teilt, sieht das innerhalb einer Sekunde.
- Setup > Caches hat eine Schaltfläche **Antwort-Cache von Velocity leeren** in der Gruppe „Seiten, Abfragen und der Server“.

## Der Navigations-Cache im Browser

Unter Velocity kann ein Service Worker (`/index.js`; `/sw.js` lädt ihn für ältere Registrierungen) Navigationen zu Seiten derselben
Herkunft aus dem eigenen Browser des Besuchers beantworten und sie im Hintergrund auffrischen. Er speichert nie eine Seite, die mit
einem Sitzungscookie abgerufen wurde, keine Antwort außer 200 und nicht `/admin`, `/user`, `/explayouts_ui` und `/api`. Die Folge für
den Support: Eine veröffentlichte Änderung erscheint beim **zweiten** Aufruf, nicht beim ersten. Der Notschalter ist, `index.js` als
`self.registration.unregister()` auszuliefern; jeder Browser verwirft ihn innerhalb von 24 Stunden
([Der Antwort-Cache, und dem Netzwerk aus dem Weg gehen](../../../../eng-us/bc/6.0/response-cache-and-navigation.md#added-a-navigation-cache-in-the-browser-indexjs)).

## Antworten für den Support

| Der Kunde sagt | Prüfen | Antwort |
|---|---|---|
| „Meine Bearbeitung erscheint für Besucher nicht“ | `X-Cache: HIT` und `Age` | sie erscheint innerhalb von `DefaultTtl` Sekunden oder nach `exp:velocity cache clear`; mit dem Navigations-Cache beim zweiten Aufruf |
| „Angemeldete Benutzer sehen die alte Seite“ | nicht der Antwort-Cache | angemeldete Besucher bekommen ihn nie; sehen Sie sich den View-Cache oder den HTTP-Cache an |
| „Eine 404-Seite erscheint nur manchmal in unseren Logs“ | `NotFoundSeconds` | über 0 erreichen zwischengespeicherte 404 PHP nicht |
| „Die Website ist nach einem Ausrollen langsam“ | der Cache wurde geleert | wärmen Sie ihn vor ([Caches leeren und vorwärmen](caches-leeren-und-vorwaermen.md)) |

## Checkliste

- [ ] Engine auf 0.0.4.26 oder später (in der Praxis die empfohlene 0.0.4.44 oder später).
- [ ] `DefaultTtl` mit dem Kunden vereinbart; `NotFoundSeconds` bleibt auf 0, wo Logs überwacht werden.
- [ ] Zwei Anfragen zeigen `X-Cache: HIT`; APCu für den Serverprozess eingeschaltet.
- [ ] Ausrollen leert den Antwort-Cache zuletzt (`exp:velocity deploy` tut das).
- [ ] Der Support kennt das Verhalten des Navigations-Caches beim „zweiten Aufruf“.

## Verwandte Seiten

- [Cache-Schichten](cache-schichten.md)
- [Caches leeren und vorwärmen](caches-leeren-und-vorwaermen.md)
- [Velocity für Hoster](../server/velocity-fuer-hoster.md)
- [Antwort-Cache von Velocity](../../../../eng-us/features/6.0/velocity-response-cache.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
