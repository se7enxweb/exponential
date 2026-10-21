---
title: Velocity-Antwort-Cache und HTTP-Cache
audience: government, hosting, admin
category: public-sector
order: 80
summary: Für IT-Teams, die eine Website des öffentlichen Sektors unter Last schnell und erreichbar halten müssen. Die zwei Caches, die antworten, bevor Exponential eine Seite rendert, was jeder speichert und nie speichert, wie aktuell die Seiten sind und wie Sie die Caches nach einer Änderung prüfen und leeren.
keywords: Antwort-Cache, Velocity-Cache, HTTP-Cache, rollenbewusster Cache, httpcache.ini, DefaultTtl, StaleWhileRevalidate, X-Cache, X-Exp-Cache, Cache leeren, Aktualität, Besucheransturm, Verfügbarkeit unter Last, response cache, Velocity cache, HTTP cache, role-aware cache, cache clear, freshness, traffic surge, availability under load
type: help
---
# Velocity-Antwort-Cache und HTTP-Cache

Diese Seite richtet sich an IT-Teams in öffentlichen Stellen, deren Websites Lastspitzen erleben: ein Wahlabend, eine
Unwetterwarnung, eine Antragsfrist. Sie beantwortet, welche Caches antworten, bevor Exponential eine Seite rendert,
was sie nie speichern, wie aktuell die ausgelieferten Seiten sind und wie Sie die Caches prüfen und leeren. Die
Referenzen sind [Velocity-Antwort-Cache](../../../../../eng-us/features/6.0/velocity-response-cache.md),
[Der rollenbewusste HTTP-Cache](../../../../bc/6.0/httpcache.md) und die Hosting-Seiten
[Velocity-Antwort-Cache](../../../hosting/leistung/antwort-cache-von-velocity.md) und
[Cache-Ebenen](../../../hosting/leistung/cache-schichten.md).

## Inhalt

1. [Zwei Caches vor der Anwendung](#zwei-caches-vor-der-anwendung)
2. [Was nie gecacht wird](#was-nie-gecacht-wird)
3. [Aktualität und dringende Korrekturen](#aktualität-und-dringende-korrekturen)
4. [Die Caches prüfen](#die-caches-prüfen)
5. [Die Caches in der richtigen Reihenfolge leeren](#die-caches-in-der-richtigen-reihenfolge-leeren)
6. [Checkliste](#checkliste)

## Zwei Caches vor der Anwendung

| | Velocity-Antwort-Cache | Rollenbewusster HTTP-Cache |
|---|---|---|
| Ausgeliefert | eingeschaltet unter Velocity (`velocity.ini [CacheSettings] Enabled=enabled`) | ausgeschaltet (`httpcache.ini Enabled=disabled`) |
| Bedient | anonyme Besucher | anonyme Besucher und angemeldete Benutzer, je Berechtigungskontext |
| Beantwortet durch | den Serverprozess von Velocity, vor einem Worker | einen frühen Ausstieg unter Apache oder PHP-FPM oder den Serverprozess von Velocity |
| Zu prüfender Header | `X-Cache`, `Age` | `X-Exp-Cache` |

Der Antwort-Cache „hält gerenderte anonyme Seiten vor und beantwortet wiederholte Anfragen im Serverprozess, bevor ein
Worker gewählt wird: kein Fork, kein PHP, keine Datenbank“
([Velocity-Antwort-Cache](../../../hosting/leistung/antwort-cache-von-velocity.md)). Der HTTP-Cache hält ganze Seiten
je Berechtigungskontext vor, sodass Benutzer mit denselben Rollen eine Kopie teilen
([Der rollenbewusste HTTP-Cache](../../../../bc/6.0/httpcache.md)).

Für die Verfügbarkeit ist das wichtig: Solange der Cache antwortet, kann eine Seite auch dann ausgeliefert werden, wenn
die PHP-Worker ausgelastet sind, und der Datenbank bleibt die Last eines Ansturms erspart. Er hält eine Website nicht
am Laufen, wenn die Datenbank oder der Server ausgefallen ist, für Seiten, die kürzlich niemand abgerufen hat.

## Was nie gecacht wird

Aus der Dokumentation des Antwort-Caches:

- angemeldete Besucher werden nie aus ihm bedient; eine Seite mit einem Formular-Token wird als `private` gesendet;
- eine Anfrage mit dem Sitzungscookie oder `is_logged_in` oder einem `Authorization`-Header;
- eine Antwort, die ein Cookie setzt oder `no-store` oder `private` angibt;
- alles außer `GET` und andere Statuscodes als 200 (ein 404 nur, wenn `NotFoundSeconds` über 0 liegt).

Der Antwort-Cache braucht die Engine 0.0.4.26 oder neuer: Ältere Versionen konnten die Seiten eines angemeldeten
Besuchers an alle ausliefern. Halten Sie die Engine aktuell.

## Aktualität und dringende Korrekturen

`DefaultTtl=30` Sekunden ist die Zeit, die eine anonyme Seite vorgehalten wird, wenn sie keine eigene Lebensdauer
mitbringt; `StaleWhileRevalidate=60` Sekunden ist die Zeit, die eine abgelaufene Seite noch ausgeliefert werden darf,
während eine Anfrage die neue rendert. Entscheiden Sie als öffentliche Stelle, ob diese Verzögerung für dringende
Korrekturen (eine falsche Telefonnummer, eine geänderte Frist) akzeptabel ist. Meist ist sie es; falls nicht, leeren
Sie den Cache direkt nach dem Veröffentlichen der Korrektur (siehe unten) oder senken Sie die Werte in
`settings/override/velocity.ini.append.php` und starten Velocity neu.

## Die Caches prüfen

```bash
./console exp:cache status --allow-root-user
php bin/php/cache.php velocity status --allow-root-user
php bin/php/cache.php httpcache status --allow-root-user
curl -sI https://www.example.gov/ | grep -i -E 'x-cache|x-exp-cache|age'
```

`exp:cache status` gibt je Ebene eine Zeile aus. Die Statusbefehle enden mit `PASS` und einer Zeile zu Einträgen und
Größe ([Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md)).

## Die Caches in der richtigen Reihenfolge leeren

Leeren Sie die Caches vor PHP **nachdem** PHP-FPM oder Velocity den neuen Code ausführt; sonst wird eine Seite, die
zwischendurch vom alten Code gerendert wurde, erneut gespeichert. Nach einer Code- oder Einstellungsänderung führt
`exp:velocity deploy` jeden Schritt in der richtigen Reihenfolge aus. Nach einer Template-Änderung oder einer
dringenden inhaltlichen Korrektur:

```bash
php bin/php/ezcache.php --clear-tag=content --allow-root-user
./console exp:velocity cache clear --allow-root-user
php bin/php/cache.php httpcache clear --allow-root-user
```

Bei mehreren Webservern leeren Sie den Antwort-Cache und den HTTP-Cache auf jedem Knoten; sie sind lokal
([Mehrere Webserver hinter einem Load Balancer](mehrere-webserver-hinter-einem-load-balancer.md)). Der Wartungsmodus
setzt die Antwort-Caches von Velocity aus, solange er eingeschaltet ist
([Wartungsmodus](../../../../../eng-us/features/6.0/maintenance-mode.md)).

## Checkliste

- [ ] Die Engine-Version unterstützt den Antwort-Cache sicher (0.0.4.26 oder neuer).
- [ ] Die Werte zur Aktualität sind von der Kommunikationsabteilung akzeptiert oder geändert.
- [ ] Die Entscheidung zum HTTP-Cache (ein oder aus) ist festgehalten.
- [ ] Runbooks leeren die Caches nach dem Neuladen, auf jedem Knoten.
- [ ] `exp:cache status` ist Teil der wöchentlichen Prüfungen.

## Verwandte Seiten

- [Verfügbarkeit](README.md): Übersicht dieses Ordners
- [Cache-Aufwärmen nach Änderungen](caches-nach-aenderungen-vorwaermen.md)
- [Statischer Cache](statischer-cache.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
