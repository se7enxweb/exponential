---
title: Benchmark einer gehosteten Website
audience: hosting, admin
category: hosting
order: 120
summary: Wie ein Hoster Exponential-Websites mit exp:benchmark misst - sichere Standardwerte, zwischengespeicherte gegenüber gerenderten Seiten, Server direkt nacheinander vergleichen, Prüfungen auf Verschlechterung nach dem Ausrollen, Rauschen auf geteilten Maschinen und wie aus Messungen Grenzen für Tarife werden, ohne Zahlen zu erfinden.
keywords: Benchmark, exp:benchmark, Leistungstest, Median, p90, p99, req/s, --cold, --compare, --baseline, --save, Verschlechterung, Rauschen, geteilter Server, ab, wrk, oha, Grenzen von Tarifen, Kapazität, benchmark, performance test, median, regression, noise, shared server, plan limits, capacity
type: help
---
# Benchmark einer gehosteten Website

Diese Seite ist für Systemadministratoren und Produktmanager bei Hosting-Anbietern, die sagen müssen, wie schnell eine
Kunden-Website ist, zwei Server-Einrichtungen vergleichen, beweisen, dass ein Ausrollen nichts langsamer gemacht hat, oder die Grenzen
eines Tarifs festlegen. Sie beantwortet: was `exp:benchmark` misst, wie Sie es sicher auf Produktionsmaschinen nutzen, wie Sie fair
vergleichen und wie Sie aus den Zahlen Entscheidungen machen. Die Quellen sind [Benchmark von Exponential](../../../anleitungen/benchmarking.md)
und [Benchmark: exp:benchmark](../../../funktionen/6.0/benchmark.md).

## Inhalt

1. [Was es misst und was nicht](#was-es-misst-und-was-nicht)
2. [Standardmäßig sicher](#standardmäßig-sicher)
3. [Der erste Lauf](#der-erste-lauf)
4. [Zwischengespeichert und gerendert](#zwischengespeichert-und-gerendert)
5. [Zwei Einrichtungen vergleichen](#zwei-einrichtungen-vergleichen)
6. [Ein Ausrollen prüfen](#ein-ausrollen-prüfen)
7. [Rauschen auf geteilten Maschinen](#rauschen-auf-geteilten-maschinen)
8. [Von Messungen zu Grenzen von Tarifen](#von-messungen-zu-grenzen-von-tarifen)
9. [Checkliste](#checkliste)

## Was es misst und was nicht

| Es misst | Es misst nicht |
|---|---|
| die Zeit bis zum letzten Byte jeder Antwort, pro Anfrage (HTTP-Modus) | was ein Browser danach tut (Bilder, Layout, Zeichnen) |
| den Antwort-Cache, den View-Cache und das Rendern, je nach warm oder `--cold` | das Netzwerk zwischen einem Besucher und dem Server, es sei denn, Sie führen es von anderswo aus |
| Anfragen pro Sekunde bei der gewählten Gleichzeitigkeit | das Maximum, das ein Server tragen kann: der Standard von 4 gleichzeitig ist höflich, kein Lasttest |
| die eigenen Kosten des Kernels im Prozess (Modus `kernel`), reine PHP-Pfade (Modus `micro`) | Seiten angemeldeter Benutzer, es sei denn, Sie übergeben ein Sitzungscookie |

Es beantwortet nicht, wie viele Besucher diese Maschine verkraftet; dafür braucht es einen Lastgenerator gegen eine Kopie der
Website. `--tool=ab|wrk|oha` übergibt die Anfragen an einen installierten Lastgenerator und ist der Anfang davon, nicht das Ende.

## Standardmäßig sicher

- Der HTTP-Modus sendet nur `GET`, schickt nie etwas ab, keine Cookies ohne `--cookie`.
- Gegen einen anderen Host als die Loopback-Adresse dieser Maschine brauchen mehr als 1000 Anfragen pro URL, mehr als 16 gleichzeitig,
  ein Vorwärmen über 100 oder insgesamt mehr als 20.000 Anfragen `--force`.
- Die Standardliste der Seiten wird nur an Hosts gesendet, die diese Installation ausliefert; ein anderer Host muss mit `--url` genannt
  werden.
- `--cold-clear` ist die eine Option, die etwas leert (den Content-View-Cache, einmal); sie wird für Server verweigert, die die
  Installation nicht ausliefert. Nutzen Sie sie nicht auf einem Server, auf dem andere arbeiten.
- Als root ausgeführt, gibt der Befehl eine Warnung aus und wartet zehn Sekunden; führen Sie ihn, wo Sie können, als Benutzer der
  Website aus.

## Der erste Lauf

Aus dem Wurzelverzeichnis der Installation:

```bash
./console exp:benchmark --allow-root-user
```

Er fragt die Startseite, die ersten drei Menüseiten, die Suchseite (wenn Besucher suchen dürfen) und die Anmeldeseite der
Administration an, jeweils 100-mal nach 5 Anfragen zum Vorwärmen, 4 gleichzeitig, und gibt pro Seite Median, p90, p99, Minimum,
Maximum und Standardabweichung aus, dazu die Umgebung (PHP, OPcache, Xdebug, CPUs, Last, Commit). Für eine Liste, die sich nie ändert,
nennen Sie die Seiten:

```bash
./console exp:benchmark --urls-file=benchmark-urls.txt --allow-root-user
```

| Zahl | Lesen Sie sie als |
|---|---|
| median | die typische Anfrage; die Zahl zum Vergleichen |
| p90, p99 | worauf die langsamsten 10 % und 1 % gewartet haben; bei 100 Läufen ist p99 fast das Maximum |
| sd | klein gegenüber dem Median bedeutet gleichmäßig; so groß wie der Median bedeutet zwei vermischte Arten von Antworten oder Hänger |
| req/s | durch die Gleichzeitigkeit begrenzt: 4 gleichzeitig mit je 10 ms können etwa 400 nicht überschreiten |
| cache | welcher Cache geantwortet hat (`HIT`, `BYPASS`, `-`); vergleichen Sie nur Zeilen mit derselben Antwort |

## Zwischengespeichert und gerendert

| Modus | Was antwortet | Option |
|---|---|---|
| warm (Standard) | was ein wiederkehrender Besucher bekommt: der Antwort-Cache von Velocity oder der HTTP-Cache, sonst der View-Cache | keine |
| kalt | die Anwendung, an jedem Antwort-Cache vorbei (ein eindeutiger Query-String pro Anfrage) | `--cold` |
| kalt, leerer View-Cache | die erste Anfrage jeder Seite rendert von Grund auf | `--cold-clear` |

Die meisten Besucher bekommen die warme Zahl; Redakteure, Suchen und Cache-Fehlschläge bekommen die kalte. Berichten Sie beide.

## Zwei Einrichtungen vergleichen

Betreiben Sie beide Server auf derselben Installation (zum Beispiel Velocity auf einem eigenen Port neben Apache) und messen Sie sie in
einem Lauf, direkt nacheinander:

```bash
./console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080 --allow-root-user
./console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080 --cold --runs=30 --allow-root-user
```

Das eigene Beispiel der Anleitung zeigt, warum beide Läufe zählen: Zwischengespeichert antwortete Velocity in 1,3 bis 2,6 ms gegenüber
8,7 bis 10,7 ms von Apache; gerendert war Apache in diesem Lauf schneller. Eine einzelne Zahl dazu, welcher Server schneller ist,
führt in die Irre ([Benchmark, Abschnitt 4.4](../../../anleitungen/benchmarking.md#44-zwei-server-unmittelbar-nacheinander-apache-und-velocity)).
Drei oder mehr Server lassen sich auf einmal vergleichen; jeder wird mit dem ersten verglichen.

## Ein Ausrollen prüfen

```bash
./console exp:benchmark --cold --runs=50 --save=var/benchmark/pre-deploy.json --allow-root-user
./console exp:velocity deploy --allow-root-user
php bin/php/warm.php --allow-root-user
./console exp:benchmark --cold --runs=50 --baseline=var/benchmark/pre-deploy.json --min-delta=5 --allow-root-user
echo $?        # 0 no regression, 1 a metric regressed, 2 usage error or refused
```

Eine Kennzahl gilt als verschlechtert, wenn sie um mehr als `--threshold` Prozent (15) und um mehr als `--min-delta` Millisekunden (1)
gewachsen ist. Ein Skript zum Ausrollen kann den Exit-Code nutzen; geben Sie ihm auf einer geteilten Maschine `--threshold=25` oder mehr
und ein `--min-delta`, sonst schlägt es falschen Alarm
([Benchmark, Abschnitt 11.5](../../../anleitungen/benchmarking.md#115-prüfen-dass-eine-bereitstellung-keine-regression-gebracht-hat)).

## Rauschen auf geteilten Maschinen

Die Anleitung wiederholte einen gespeicherten Lauf neun Minuten später ohne jede Änderung und bekam 13 „Verschlechterungen“: Die
Maschine war ausgelasteter. Cache-Treffer von 1 bis 3 ms verdoppelten sich auf einer geteilten Maschine von einer Minute zur nächsten.
Was zu tun ist ([Benchmark, Abschnitt 10](../../../anleitungen/benchmarking.md#10-rauschen-und-wie-man-es-verringert)):

1. Dieselbe Maschine, dasselbe PHP, dieselbe Einstellung für OPcache, JIT und Xdebug (die Umgebungszeile sagt, welche).
2. Derselbe Befehl: dieselben Seiten, Läufe, dasselbe Vorwärmen, dieselbe Gleichzeitigkeit, warm oder kalt.
3. Nichts anderes läuft, das Sie anhalten können; notieren Sie die Last in der Kopfzeile.
4. Vorher und nachher dicht beieinander; für zwei Server ein Lauf mit `--compare`, nie zwei getrennte.
5. Die Caches im selben Zustand: dieselbe Spalte `cache` in beiden Läufen.
6. Führen Sie es zweimal aus. Ein Unterschied, der sich nicht wiederholt, ist Rauschen.
7. Beurteilen Sie zuerst den Median, p90 und p99 als Zweites, das Maximum nie allein.

> **Tipp**
> Führen Sie nie einen Benchmark mit geladenem Xdebug aus (alles ist mehrfach langsamer); die Kopfzeile sagt `Xdebug ON`, wenn es
> geladen ist.

## Von Messungen zu Grenzen von Tarifen

Das Projekt veröffentlicht keine Kapazität pro Server, und diese Hilfe nennt keine. Was die Quellen geben, ist eine Methode:

| Entscheidung | Wie Sie sie messen |
|---|---|
| Anzahl der Velocity-Worker für einen Tarif | gerenderte Seiten bei steigender Gleichzeitigkeit; stimmen Sie bis zu dem Punkt ab, an dem p90 umkippt ([Velocity-Worker und Pools](../server/velocity-worker-und-pools.md#die-zahl-für-einen-tarif-finden)) |
| Engine für einen Tarif | `--compare` warm und `--cold`, dazu eine Seite angemeldeter Benutzer mit `--cookie`, wenn Redakteure zählen |
| Einstellungen für OPcache oder Worker | eine Einstellung pro Schritt, ein kalter Lauf davor und danach, nur behalten, wenn sich der Gewinn wiederholt ([Benchmark, Abschnitt 11.3](../../../anleitungen/benchmarking.md#113-opcache-und-velocity-optimieren)) |
| Kunden pro Maschine | messen Sie eine repräsentative Kunden-Website, dann eine Kopie davon unter einem Lastgenerator; lassen Sie Reserve für Cronjobs, Vorwärmen der Caches und Backups |

Zahlen, die in der Dokumentation zitiert werden (zum Beispiel 8.941 Anfragen pro Sekunde im README oder die Zahlen zu Instanzen auf
[Velocity-Worker und Pools](../server/velocity-worker-und-pools.md#instanzen-mehrere-server-auf-einem-port)), wurden auf bestimmten
Maschinen und Websites gemessen. Die Quellen sagen das: Sie beschreiben die Templates dieser Website auf dieser Maschine, sind keine
Eigenschaft des Servers und sollten nicht als solche zitiert werden. Nehmen Sie sie nicht in einen Tarif oder ein Angebot auf.

## Checkliste

- [ ] Benchmarks laufen als Benutzer der Website, gegen Hosts, die diese Installation ausliefert.
- [ ] Jeder Bericht nennt warme und kalte Zahlen und die Umgebungszeile.
- [ ] Vergleiche werden in einem Lauf mit `--compare` gemacht, direkt nacheinander.
- [ ] Prüfungen nach dem Ausrollen nutzen `--baseline` mit einem `--min-delta`, das zu einer geteilten Maschine passt.
- [ ] Grenzen von Tarifen stammen aus Ihren eigenen Messungen, nicht aus Zahlen der Dokumentation.

## Verwandte Seiten

- [Leistung](README.md)
- [Velocity-Worker und Pools](../server/velocity-worker-und-pools.md)
- [Hosting-Tarife gestalten](../geschaeft/hosting-tarife-gestalten.md)
- [Benchmark von Exponential](../../../anleitungen/benchmarking.md)
- [Benchmark: exp:benchmark](../../../funktionen/6.0/benchmark.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
