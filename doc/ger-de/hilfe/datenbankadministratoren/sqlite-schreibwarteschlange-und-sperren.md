---
title: "SQLite: die Schreibwarteschlange und Sperren"
audience: dba
category: database-administration
order: 9
summary: Wie der SQLite-Treiber von Exponential 6.0.15 gleichzeitige Schreiber mit einer Sperrdatei und BEGIN IMMEDIATE einreiht, wie lange eine Transaktion wartet (SQLiteTransactionWait) und wie sich das zu den Anfrage-Timeouts von Velocity und PHP-FPM verhält, wie ein Timeout aussieht und worauf Sie achten, wenn sich Timeouts wiederholen.
keywords: SQLite, Sperren, Schreibwarteschlange, writer-lock, BEGIN IMMEDIATE, database is locked, database is busy, SQLiteTransactionWait, busy_timeout, SQLITE_BUSY, requestTimeout, request_terminate_timeout, flock, Nebenläufigkeit, Veröffentlichen, locking, write queue, concurrency, publishing
type: help
---
# SQLite: die Schreibwarteschlange und Sperren

Diese Seite richtet sich an Datenbankadministratoren, die Exponential auf SQLite betreiben und verstehen müssen, was
passiert, wenn zwei Redakteure im selben Moment veröffentlichen. Sie beantwortet: warum einfache SQLite-Transaktionen mit
"database is locked" scheitern, was der Treiber seit 6.0.15 stattdessen tut, wie lange ein Schreibzugriff wartet, wie ein
Timeout aussieht und wie Sie seine Ursache finden. Quellen:
[Schreibzugriffe in der Warteschlange](../../installation/09-datenbanken.md#schreibvorgänge-in-der-warteschlange-begin-immediate-und-die-schreibsperre) und
[SQLite: Transaktionen reihen sich für die Schreibsperre ein](../../bc/6.0/sqlite-transaktionen.md).

## Inhalt

1. [Das Problem](#das-problem)
2. [Was der Treiber tut](#was-der-treiber-tut)
3. [Wie lange eine Transaktion wartet](#wie-lange-eine-transaktion-wartet)
4. [Wie ein Timeout aussieht](#wie-ein-timeout-aussieht)
5. [Wenn sich Timeouts wiederholen](#wenn-sich-timeouts-wiederholen)
6. [Geforkte Prozesse und andere Benutzer](#geforkte-prozesse-und-andere-benutzer)
7. [Gemessenes Verhalten](#gemessenes-verhalten)
8. [Checkliste](#checkliste)

## Das Problem

Ein einfaches `BEGIN` in SQLite ist verzögert: Die Transaktion liest zuerst und verlangt die Schreibsperre erst bei ihrem
ersten Schreibzugriff. Hat eine andere Verbindung dazwischen committet, ist der gelesene Schnappschuss veraltet, und SQLite
antwortet sofort mit `SQLITE_BUSY`, ohne zu warten, weil Warten den Schnappschuss nicht aktuell machen kann. Vor 6.0.15
konnte eine Veröffentlichung mit "database is locked" scheitern, egal wie lang das Busy-Timeout war.

## Was der Treiber tut

`eZSQLite3DB::beginQuery()` startet jede Transaktion so:

1. Es öffnet, einmal je Verbindung und erneut in jedem geforkten Prozess, eine Sperrdatei neben der Datenbank:
   `<database file>.writer-lock`, zum Beispiel `var/storage/sqlite3/exponential.db.writer-lock`.
2. Es nimmt ein exklusives `flock()` auf diese Datei und versucht es alle 1 bis 15 ms erneut; Schreiber, die schon länger
   als eine Sekunde warten, versuchen es öfter, was sie ungefähr in der Reihenfolge ihrer Ankunft bedient.
3. Mit gehaltener Sperre führt es `BEGIN IMMEDIATE` aus, das die Schreibsperre von SQLite am Anfang nimmt, wo das
   Busy-Timeout gilt. Auf Schreiber außerhalb der Warteschlange (eine einzelne Anweisung, ein anderes Programm) wartet der
   Busy-Handler von SQLite innerhalb der verbleibenden Zeit.
4. Bei `COMMIT` oder `ROLLBACK` gibt es die Sperrdatei frei. Stirbt der Prozess, gibt das Betriebssystem sie frei; ein
   Absturz blockiert die Warteschlange also nie.

Am Anfang zu warten ist sicher: Bevor `BEGIN IMMEDIATE` gelingt, hat die Transaktion nichts geschrieben, und danach kann
nichts in ihr ein Wettrennen um eine Sperre verlieren.

Jeder Prozess, der in die Datenbank schreibt, verwendet dasselbe `.writer-lock`: PHP-FPM-Worker, Velocity-Worker, Cronjobs
und Konsolenskripte reihen sich gegeneinander ein.

## Wie lange eine Transaktion wartet

Bis zu `[DatabaseSettings] SQLiteTransactionWait` Sekunden (Standard `25`; Werte unter 1 werden auf 1 angehoben). Halten
Sie den Wert unter dem Anfrage-Timeout des Webservers, damit eine wartende Anfrage mit einer klaren Meldung scheitert,
statt abgebrochen zu werden:

| Server | Anfrage-Timeout | Hinweis |
|---|---|---|
| Velocity | `requestTimeout` (`Q.webserver.requestTimeout`), 30 s, sofern nicht geändert | der Client erhält ein 504, wenn es überschritten wird |
| PHP-FPM | `request_terminate_timeout` des Pools (aus, sofern nicht gesetzt) | `max_execution_time` zählt Wartezeit unter Linux nicht |

Releases vor 6.0.15 lieferten `60` aus; setzt Ihr Override noch `60`, senken Sie den Wert auf `25`. Um länger zu warten,
erhöhen Sie das Anfrage-Timeout und `SQLiteTransactionWait` zusammen, die Wartezeit immer ein paar Sekunden unter dem
Timeout.

```bash
php bin/php/console exp:ini get site.ini/DatabaseSettings/SQLiteTransactionWait --allow-root-user
```

## Wie ein Timeout aussieht

In `var/log/error.log` und auf der Fehlerseite:

```text
database is busy: the transaction could not start within 25 s, another write held the lock all that time; nothing was written
```

Jeder andere Fehler beim Start lautet `the transaction could not start: <SQLite's message>; nothing was written`. In
beiden Fällen wurde nichts geschrieben; wiederholen Sie die Aktion.

## Wenn sich Timeouts wiederholen

Ein Schreibzugriff hat die Sperre während der ganzen Wartezeit gehalten. Sehen Sie in `var/log/error.log` und im
Cron-Log nach, was zu dieser Zeit lief:

- ein `VACUUM` (es hält die Schreibsperre während seines ganzen Laufs);
- ein großer Import oder ein Skript, das eine Transaktion geöffnet und dann auf etwas anderes gewartet hat;
- eine `sqlite3`-Shell, die innerhalb von `BEGIN` offen gelassen wurde.

Zählen Sie die Timeouts mit `grep -c "database is busy" var/log/error.log`. `SQLiteTransactionWait` zu erhöhen hilft nur,
wenn der Halter berechtigt lange lief. Ist die Warteschlange beim normalen Redigieren regelmäßig länger als die Wartezeit,
bedient ein Datenbankserver mit Sperren auf Zeilenebene die Redakteure besser ([Eine Engine wählen](eine-engine-waehlen.md)).

## Geforkte Prozesse und andere Benutzer

`flock()` gehört zu einer geöffneten Datei, ein geforkter Prozess darf also das Handle seines Elternprozesses nicht
wiederverwenden. Der Treiber merkt sich die Prozess-ID, die die Sperrdatei geöffnet hat, und öffnet in jedem Fork eine
neue; Velocity-Worker und die Kindprozesse des asynchronen Veröffentlichers reihen sich korrekt ein. Der Treiber öffnet
`.writer-lock` zum Schreiben und, falls das scheitert, nur lesend: `flock()` funktioniert auf einem nur lesenden Handle,
eine von einem anderen Benutzer angelegte Sperrdatei reiht also weiterhin korrekt ein.

## Gemessenes Verhalten

Aus den eigenen Tests der Änderung auf dem Entwicklungsserver: 192 Veröffentlichungen aus 64 gleichzeitigen Prozessen
wurden alle abgeschlossen, wo der frühere Start 6 davon verlor; 48 Transaktionen von je 300 ms, gemeinsam gestartet, waren
nach 14,7 Sekunden fertig, etwa die 14,4 Sekunden, die sie nacheinander brauchen. Die Warteschlange kostet über das Warten
hinaus fast nichts.

## Checkliste

- [ ] `SQLiteTransactionWait` liegt unter `requestTimeout` von Velocity und `request_terminate_timeout` von PHP-FPM.
- [ ] Kein Override setzt noch den alten Wert `60`.
- [ ] `VACUUM` und große Importe laufen im Wartungsmodus.
- [ ] `grep -c "database is busy" var/log/error.log` ist 0 oder erklärt.

## Verwandte Seiten

- [SQLite 3 für Datenbankadministratoren](sqlite-fuer-datenbankadministratoren.md)
- [SQLite-Wartung](sqlite-wartung.md)
- [Datenbankfehler beheben](datenbankfehler-beheben.md)
- [Velocity-Worker und Pools](../hosting/server/velocity-worker-und-pools.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
