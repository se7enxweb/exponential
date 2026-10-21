---
title: Statischer Cache und Antwortcache
audience: businesses, admin, website-creator, hosting
category: business-solutions
order: 120
summary: Die beiden Caches, die Besucher bedienen, ohne die Anwendung auszuführen, der statische Cache und der Antwortcache von Velocity, was jeder ein Unternehmen kostet und ihm bringt, wann Sie welchen verwenden und wie Sie verhindern, dass sie veraltete oder persönliche Seiten ausliefern.
keywords: statischer Cache, Antwortcache, Antwortcache von Velocity, anonyme Besucher, Entlastung, static cache, response cache, Velocity response cache, makestaticcache, staticcache.ini, stale-while-revalidate, negative caching, X-Cache, Age, anonymous visitors, offload
type: help
---
# Statischer Cache und Antwortcache

Diese Seite richtet sich an Führungskräfte, die entscheiden, wie öffentliche Seiten unter Last schnell und verfügbar
bleiben, und an die Person im Betrieb, die die Caches einschaltet. Beide hier beschriebenen Caches beantworten einen
Besucher, ohne die Anwendung zu starten, und sind damit der stärkste Schutz gegen eine Lastspitze. Sie funktionieren
unterschiedlich und haben unterschiedliche Kosten. Das Gesamtbild finden Sie unter
[Caching für Verfügbarkeit](caching-fuer-verfuegbarkeit.md).

## Inhalt

1. [Zwei Caches im Vergleich](#zwei-caches-im-vergleich)
2. [Der Antwortcache von Velocity](#der-antwortcache-von-velocity)
3. [Der statische Cache](#der-statische-cache)
4. [Welcher für welches Unternehmen](#welcher-für-welches-unternehmen)
5. [Persönliche Seiten draußen halten](#persönliche-seiten-draußen-halten)
6. [Nach einer Änderung](#nach-einer-änderung)
7. [Checkliste](#checkliste)

## Zwei Caches im Vergleich

| | Antwortcache von Velocity | Statischer Cache |
|---|---|---|
| Wo er liegt | Im Velocity-Server | Einfache Dateien unter `var/<site>/static`, vom Webserver ausgeliefert |
| Braucht | Velocity, das die Website ausliefert | Webserver-Regeln, die aus den Dateien antworten |
| Zustand ab Werk | Eingeschaltet in `settings/velocity.ini` von Exponential (`[CacheSettings] Enabled=enabled`) | Ausgeschaltet (`[ContentSettings] StaticCache=disabled`) |
| Wer aus ihm bedient wird | Nur anonyme Besucher | Nur anonyme Besucher: kein Query-String, keine Sitzung |
| Wie Seiten hineinkommen | Die erste Anfrage nach einer Seite speichert sie | Ein Durchlauf über die Website, danach Aktualisierung beim Veröffentlichen |
| Wie lange eine Seite lebt | `[CacheSettings] DefaultTtl`, ab Werk 30 Sekunden, sofern die Seite nichts Eigenes angibt | Bis eine Veröffentlichung sie aktualisiert |
| Geleert durch | `exp:velocity cache clear` | `php bin/php/cache.php static clear` |

Quellen: `settings/velocity.ini`, [Cache-Schichten](../../hosting/leistung/cache-schichten.md#die-schichten-auf-einen-blick),
[Generator für den statischen Cache](../../../../eng-us/features/6.0/static-cache-generator.md),
[Betrieb einer Site, den statischen Cache einschalten](../../../anleitungen/betrieb-einer-website.md#4-den-statischen-cache-einschalten).

## Der Antwortcache von Velocity

Er „hält gerenderte öffentliche Seiten vor und beantwortet wiederholte Anfragen im Serverprozess, bevor ein Worker
ausgewählt wird, sodass ein Treffer überhaupt keinen Worker kostet“
([Antwortcache von Velocity](../../../../eng-us/features/6.0/velocity-response-cache.md)). Er verhält sich wie ein kleiner
Reverse Proxy. Die Funktionen, die die Verfügbarkeit schützen:

| Funktion | Was sie für Sie tut |
|---|---|
| Stale-while-revalidate | Eine abgelaufene Seite wird weiter ausgeliefert, während eine Anfrage eine neue Kopie rendert, sodass eine stark besuchte Seite nie alle Besucher gleichzeitig zu PHP schickt |
| Negatives Caching | Antworten „nicht gefunden“ können eine Weile behalten werden, sodass wiederholte Anfragen nach einer fehlenden Seite wenig kosten |
| Bedingte Anfragen (`304`) und `ETag` | Ein wiederkehrender Browser erhält eine kurze Antwort „nicht geändert“ statt der ganzen Seite |
| Neuladen umgeht ihn nicht | „Das eigene `Cache-Control: no-cache` eines Besuchers umgeht den Cache nicht, sodass das Drücken auf Neu laden den Server nicht jede Seite neu rendern lassen kann“ |
| Kennt den Wartungsmodus | Solange der Wartungsmodus eingeschaltet ist, antwortet der Cache nicht, sodass die Wartungsseite angezeigt wird ([Anleitung zum Wartungsmodus](../../../anleitungen/wartungsmodus.md#9-apache-velocity-und-die-caches)) |

Die Funktionsseite dokumentiert ihre eigene Messung: „Eine gecachte öffentliche Seite wird in etwa 0,2 ms CPU-Zeit
ausgeliefert (gemessen auf einem Host mit 12 Kernen: eine Startseite über TLS mit gzip, etwa 2.950 Seiten pro Sekunde
von einem Server, 11.985 von vier Servern an einem Port).“ Wie bei jedem Benchmark entscheiden Ihre Seiten und Ihre
Hardware über Ihr Ergebnis; siehe
[Velocity für Unternehmen](velocity-fuer-unternehmen.md#die-dokumentierten-zahlen-ehrlich-gelesen).

Der Kompromiss steht in den mitgelieferten Einstellungen: Mit `DefaultTtl=30` „kann ein Besucher eine Seite sehen, die
bis zu so alt ist“ (`settings/velocity.ini`, `[CacheSettings]`). Für die meisten öffentlichen Seiten ist eine halbe
Minute unsichtbar; für einen Lagerbestandszähler oder einen Live-Spielstand vielleicht nicht. Fragen Sie Ihren
Entwickler, welche Seiten nie gecacht werden dürfen und wie sie das mitteilen.

## Der statische Cache

„Der statische Cache speichert öffentliche Seiten als einfache Dateien, die der Webserver ausliefern kann, ohne
Exponential zu starten“ ([Betrieb einer Site](../../../anleitungen/betrieb-einer-website.md#4-den-statischen-cache-einschalten)). Er
passt zu Websites mit überwiegend öffentlichen, selten geänderten Seiten
([Generator für den statischen Cache](../../../../eng-us/features/6.0/static-cache-generator.md)).

So wird er eingeschaltet, in Grundzügen:

1. Erzeugen Sie die Seiten: **Setup > Cache > Statischer Inhalts-Cache** oder
   `php bin/php/makestaticcache.php --site=site --allow-root-user` (Standardwerte: bis zu 2.500 Seiten, Linktiefe 12).
2. Schalten Sie `StaticCache=enabled` in `[ContentSettings]` ein und leeren Sie den INI-Cache.
3. Lassen Sie den Webserver aus den Dateien antworten: Unter Apache mit `.htaccess` fügt die mitgelieferte
   `.htaccess_root_static` die Regeln hinzu.

Angeboten werden nur Siteaccesses, die keine Anmeldung brauchen, weil ihre Seiten für alle gleich sind; Formulare,
Suchen und angemeldete Besucher bekommen immer die Live-Seite.

> **Hinweis**
> Die Anleitung für Website-Ersteller empfiehlt, den statischen Cache ausgeschaltet zu lassen, solange Sie ihn nicht
> brauchen: Ist er eingeschaltet, erzeugt eine Veröffentlichung die betroffenen Seiten über HTTP innerhalb der Anfrage
> des Redakteurs neu, was das Veröffentlichen verlangsamt hat
> ([Statischer Cache und Antwortcache von Velocity](../../../../eng-us/help/creators/website-creators/static-cache-and-velocity.md#leave-the-static-cache-off)).
> Seit dem 1. Oktober 2026 ruft eine Veröffentlichung jede Seite nur einmal und mehrere gleichzeitig ab
> ([Generator für den statischen Cache](../../../../eng-us/features/6.0/static-cache-generator.md#refreshing-on-publish-once-each-in-parallel-2026-10-01)),
> doch die Kosten wachsen weiterhin mit der Zahl der Seiten, die eine Veröffentlichung betrifft. Messen Sie an einer
> Kopie Ihrer Website.

## Welcher für welches Unternehmen

| Ihre Situation | Empfehlung |
|---|---|
| Velocity liefert die Website aus | Verlassen Sie sich auf den Antwortcache; er ist in den mitgelieferten Einstellungen eingeschaltet |
| Shared Hosting mit Apache, überwiegend öffentliche Seiten, die sich selten ändern | Erwägen Sie den statischen Cache, nachdem Sie die Veröffentlichungszeiten gemessen haben |
| Viele Redakteure, die den ganzen Tag veröffentlichen | Vermeiden Sie den statischen Cache; lassen Sie den View-Cache und den Antwortcache arbeiten |
| Der größte Teil des Verkehrs ist angemeldet (Intranet, Mitgliederbereich, Kasse) | Keiner hilft viel; planen Sie die Kapazität für gerenderte Seiten ([Lastspitzen planen](lastspitzen-planen.md)) |
| Ein vorgeschaltetes CDN | Es kann ebenfalls öffentliche Seiten cachen; die Wartungsseite speichert es nicht, kann aber nach einer Änderung ältere Seiten halten ([Anleitung zum Wartungsmodus](../../../anleitungen/wartungsmodus.md#9-apache-velocity-und-die-caches)) |

## Persönliche Seiten draußen halten

Beide Caches sind nur für anonyme Seiten gedacht, und beide schützen persönliche Seiten von Grund auf:

- Der Antwortcache überspringt jede Anfrage mit einem Header `Authorization` oder einem Sitzungs-Cookie und jede
  Antwort, die ein Cookie setzt oder `private` oder `no-store` angibt
  ([Antwortcache von Velocity](../../../../eng-us/features/6.0/velocity-response-cache.md#what-is-kept)).
- Der statische Cache antwortet nur bei einem einfachen `GET` oder `HEAD` ohne Query-String und ohne Sitzung aus einer
  Datei.

Die Seite zum Antwortcache warnt: „Cachen Sie keine personalisierten Seiten.“ Baut Ihr Entwickler Seiten, die sich
zwischen anonymen Besuchern unterscheiden, ohne dass ein Cookie sie auseinanderhält (zum Beispiel nach Standort), muss er
sie entsprechend kennzeichnen.

## Nach einer Änderung

- Nach einer Änderung an Template, Stylesheet oder Einstellungen:
  `php bin/php/console exp:velocity cache clear --allow-root-user`. `exp:velocity deploy` leert ihn als letzten Schritt
  ([Statischer Cache und Antwortcache von Velocity](../../../../eng-us/help/creators/website-creators/static-cache-and-velocity.md#after-a-change)).
- Dateien des statischen Caches werden beim Veröffentlichen aktualisiert; alle entfernen Sie mit
  `php bin/php/cache.php static clear --allow-root-user`.
- Prüfen Sie, dass der Antwortcache funktioniert: Eine zweite anonyme Anfrage nach derselben Seite trägt einen Header
  `Age` und `X-Cache: HIT`.

## Checkliste

- [ ] Sie wissen, welchen der beiden Caches Ihre Website nutzt und warum.
- [ ] Die Lebensdauer im Antwortcache passt zu Ihren Inhalten (keine Seite wird älter angezeigt, als das Unternehmen
      akzeptiert).
- [ ] Seiten, die nie gecacht werden dürfen, sind von Ihrem Entwickler entsprechend gekennzeichnet.
- [ ] Ist der statische Cache eingeschaltet, wurden die Veröffentlichungszeiten gemessen und sind für die Redakteure
      akzeptabel.
- [ ] Caches werden nach Deployments in der richtigen Reihenfolge geleert
      ([Unterbrechungsfreie Deployments](unterbrechungsfreie-deployments.md)).

## Verwandte Seiten

- [Caching für Verfügbarkeit](caching-fuer-verfuegbarkeit.md)
- [Antwort-Cache von Velocity (Hosting)](../../hosting/leistung/antwort-cache-von-velocity.md)
- [Statischer Cache (Hosting)](../../hosting/leistung/statischer-cache.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
