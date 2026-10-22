---
title: Velocity und Leistung für Integrationen
audience: integrator, developer, hosting
category: integration
order: 370
summary: Was Velocity, der Webserver von Exponential mit dauerhaften Workern, für Integrationen bedeutet - beim Start geladene Klassen, statische Werte, die Anfragen überdauern, das Ausrollen einer geänderten Integration, welche API-Antworten der Antwort-Cache behalten darf, Ratenbegrenzungen je Server und wo die Last einer Integration laufen sollte.
keywords: Velocity, dauerhafter Worker, Leistung, Last, Neustart, Antwort-Cache, Ratenbegrenzung, Stapel, API-Durchsatz, persistent worker, qbix engine, FrankenPHP, PHP-FPM, statics, class loading, restart, exp:velocity deploy, response cache, X-Cache, Authorization, APCu rate limit, performance, load, batch, API throughput
type: help
---
# Velocity und Leistung für Integrationen

Diese Seite richtet sich an Integratoren, deren Code auf Websites läuft, die **Velocity** ausliefert, der Webserver
mit dauerhaften Workern, der Teil von Exponential 6.0.15 ist (`se7enxweb/exponential-velocity`, gestartet mit
`php bin/php/console exp:velocity`). Velocity startet den Kernel einmal und hält Worker am Leben, daher antwortet es
weit schneller als ein PHP-Prozess je Anfrage; es ändert aber auch einige Dinge, die eine Integration beachten muss
([Velocity: Exponential in einem Webserver mit dauerhaften Workern betreiben](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)).

## Inhalt

1. [Was sich für Integrationscode ändert](#was-sich-für-integrationscode-ändert)
2. [Eine geänderte Integration ausrollen](#eine-geänderte-integration-ausrollen)
3. [API-Antworten und der Antwort-Cache](#api-antworten-und-der-antwort-cache)
4. [Wo die Last laufen sollte](#wo-die-last-laufen-sollte)
5. [Messen](#messen)

## Was sich für Integrationscode ändert

| Thema | Mit PHP-FPM | Mit Velocity | Was zu tun ist |
|---|---|---|---|
| Klassen | Je Anfrage geladen | Beim Start des Servers geladen; die Worker erben sie | Velocity nach dem Hinzufügen oder Ändern von Klassen neu starten |
| Statische Eigenschaften | Bei jeder Anfrage leer | Können von einer Anfrage zur nächsten überdauern | Den Zustand der Anfrage zu Beginn jeder Anfrage zurücksetzen; nie den aktuellen Benutzer oder Zugangsdaten in einem statischen Wert halten |
| Listener und Registrierungen | Je Anfrage angehängt | Einmal aus den Einstellungen angehängt | Nie Listener in Code anhängen, der je Anfrage läuft: Sie häufen sich an |
| Einstellungen | Je Anfrage gelesen (mit dem INI-Cache) | Beim Start gelesen und zwischengespeichert | Nach Änderungen an Einstellungen den INI-Cache leeren (`--clear-tag=ini`) und neu starten |
| Lange Aufrufe | Belegen einen FPM-Kindprozess | Belegen einen Worker | Entfernte Aufrufe aus Anfragen heraushalten; auf Cronjobs verschieben |

Der Kernel selbst zeigt das Muster: Die REST-Schicht setzt den Zustand des API-Schlüssels zu Beginn jeder Anfrage
zurück (`expApiKeyRest::reset()`), weil sich ein dauerhafter Worker sonst an den Schlüssel der vorigen Anfrage erinnern
würde.

## Eine geänderte Integration ausrollen

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user   # what it would do
php bin/php/console exp:velocity deploy --allow-root-user
```

`exp:velocity deploy` erzeugt die Autoloads der Erweiterungen neu, leert den INI-Cache und die Template-Caches, lädt
PHP-FPM neu, startet Velocity neu und leert dann die Inhalts- und Antwort-Caches, in dieser Reihenfolge, und bricht
beim ersten Fehler ab ([Bereitstellung](../../anleitungen/bereitstellung.md#7-eine-php-änderung-mit-einem-befehl-ausliefern)). Ohne den
Befehl gehen Sie dieselben Schritte von Hand; Caches **vor** dem Neuladen zu leeren lässt vom alten Code gerenderte
Seiten erneut zwischenspeichern.

Eine neue Klasse, die eine Einstellung nennt (ein Login-Handler, ein Workflow-Ereignistyp, eine Suchmaschine), muss in
den Workern vorhanden sein, bevor die Einstellung aktiv wird: Rollen Sie zuerst den Code aus und starten Sie neu,
schalten Sie dann die Einstellung ein.

## API-Antworten und der Antwort-Cache

Der Antwort-Cache von Velocity ist **aus, bis er eingeschaltet wird**. Ist er an, behält er eine `GET`-Antwort nur,
wenn die Anfrage keinen Header `Authorization` und kein Sitzungs-Cookie trägt, die Antwort kein Cookie setzt und
`Cache-Control` weder `no-store` noch `private` sagt
([Antwort-Cache von Velocity](../../funktionen/6.0/velocity-antwort-cache.md)).

Für Integrationen bedeutet das:

- Anfragen mit einem API-Schlüssel, OAuth-Token oder `expservices`-Token (Header `Authorization`) werden nie aus dem
  Cache beantwortet.
- **Anonyme** `GET`-Aufrufe (ein öffentlicher RSS-Feed, ein anonymer `ezjscore`-Aufruf) können für die Lebensdauer der
  Seite aus dem Cache beantwortet werden; die Header `X-Cache: HIT` und `Age` sagen das. Senden Sie aus eigenen
  Endpunkten, die immer frisch sein müssen, `Cache-Control: no-store`.
- Leeren Sie nach einem Import den Antwort-Cache (`php bin/php/console exp:velocity cache clear`), wenn anonyme Clients
  die Änderung sofort sehen müssen ([Caching nach Importen](caching-nach-importen.md)).

## Wo die Last laufen sollte

- **Massenarbeit auf der Kommandozeile.** Importe, Exporte und Neuindizierung laufen als CLI-Skripte oder
  Cronjob-Teile, nicht als Tausende HTTP-Anfragen; sie nehmen den Besuchern keine Worker weg.
- **Ratenbegrenzungen je Server.** Die Ratenbegrenzung der API-Schlüssel (`RateLimitPerMinute`) wird in APCu je
  PHP-FPM-Pool oder je Velocity-Server gezählt; bei mehreren Servern ist die tatsächliche Grenze höher. Verlassen Sie
  sich nicht darauf als Kapazitätsplanung.
- **Den Stapel niedriger priorisieren.** Führen Sie große Importe auf einem geteilten Host mit niedrigerer Priorität
  aus (`nice -n 15 php ...`).
- **Außerhalb der Stoßzeiten.** Planen Sie vollständige Abgleiche in ruhigen Stunden ein; ein großer Import kann jeden
  View-Cache leeren ([Caching nach Importen](caching-nach-importen.md#was-das-veröffentlichen-selbst-leert)).

## Messen

- `php bin/php/console exp:velocity status` und das Velocity-Kontrollzentrum zeigen die Worker und ihre Last
  ([Velocity-Kontrollzentrum](../../funktionen/6.0/velocity-control-panel.md)).
- `php bin/php/benchmark.php --help` beschreibt den Benchmark-Befehl für den Durchsatz an Anfragen
  ([Benchmarking](../../anleitungen/benchmarking.md)).
- Messen Sie die Dauer Ihrer eigenen Läufe und protokollieren Sie sie in der Zusammenfassungszeile; ein Lauf, der jede
  Woche langsamer wird, ist das erste Zeichen einer wachsenden Tabelle oder eines fehlenden Index.

## Verwandte Seiten

- [Velocity: Exponential in einem Webserver mit dauerhaften Workern betreiben](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)
- [Mit Velocity bereitstellen, für App-Ersteller](../ersteller/anwendungsentwickler/mit-velocity-ausliefern.md)
- [Velocity ist Teil von Exponential 6.0.15](../velocity-ist-teil-von-exponential-6-0-15.md)
- [Caching nach Importen](caching-nach-importen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
