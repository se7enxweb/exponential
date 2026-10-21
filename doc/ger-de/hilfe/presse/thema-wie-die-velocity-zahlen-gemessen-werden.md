---
title: "Thema: wie die Velocity-Zahlen gemessen werden"
audience: press
category: press-media
order: 57
summary: Die für Exponential Velocity veröffentlichten Geschwindigkeitszahlen (0,36 ms pro gecachter Seite, 8.941 Anfragen pro Sekunde) mit den Bedingungen, unter denen sie gemessen wurden, der Vorher-nachher-Tabelle, aus der sie stammen, wie Sie sie mit exp:benchmark nachmessen und wie Sie sie ehrlich drucken.
keywords: Thema, Velocity, Benchmark, exp:benchmark, Zahlen, 0,36 ms, 8941, Anfragen pro Sekunde, Messung, Methodik, Presse, Faktenprüfung, story, figures, 0.36 ms, requests per second, measurement, methodology, press, fact-checking
type: help
---
# Thema: wie die Velocity-Zahlen gemessen werden

Diese Seite ist für Journalisten, Rezensenten und Faktenprüfer, die eine Geschwindigkeitszahl für Exponential zitieren
möchten. Geschwindigkeitszahlen stimmen nur zusammen mit ihren Bedingungen. Diese Seite gibt die veröffentlichten
Zahlen, woher sie stammen, was sie bedeuten und wie ein Reporter selbst messen kann.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Die veröffentlichten Zahlen](#die-veröffentlichten-zahlen)
3. [Woher sie stammen](#woher-sie-stammen)
4. [Selbst messen](#selbst-messen)
5. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
6. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)
7. [Wen Sie fragen](#wen-sie-fragen)
8. [Verwandte Seiten](#verwandte-seiten)

## Das Thema in einem Absatz

Das README von Exponential veröffentlicht drei Zahlen für Velocity und erklärt, sie seien „reproduzierbar mit Skripten,
die mit dem Projekt ausgeliefert werden – es sind keine Marketingzahlen, und die Methodik ist daneben aufgeschrieben“
([README](https://github.com/se7enxweb/exponential/blob/main/README.md#how-exponential-is-served)). Derselbe Text fügt
den wichtigsten Vorbehalt selbst hinzu: „Der größte Teil der Geschwindigkeit kam aus der Korrektur von Exponential und
des Caches, nicht aus dem Prozessmodell des Servers.“ Ein fairer Beitrag berichtet beides: die Zahlen und dass die
größten Gewinne aus Cache-Korrekturen stammen, die auch unter Apache und Nginx wirken.

## Die veröffentlichten Zahlen

| Zahl, wie veröffentlicht | Was sie laut README bedeutet |
|---|---|
| „0.36 ms per cached page“ | „die eigene Arbeit des Servers, um eine gecachte Seite zu beantworten – gemessen als Median über Hunderte von Anfragen, nicht als bester Fall“ |
| „8,941 requests per second sustained“ | „gemessen auf einer Maschine mit 12 Kernen, die eine echte Seite ausliefert, ohne Fehler bei jeder getesteten Parallelität, bis zu 384 gleichzeitig offenen Verbindungen“ |
| „repeat visits never touch the network“ | „bezieht sich auf den optionalen Navigations-Cache (einen Service Worker)“ im Browser des Besuchers |

Quelle für alle drei: [README, How Exponential is served](https://github.com/se7enxweb/exponential/blob/main/README.md#how-exponential-is-served).

## Woher sie stammen

Die Vorher-nachher-Tabelle der Arbeit am Antwort-Cache nennt dieselben Zahlen neben ihren Ausgangswerten
([Der Antwort-Cache](../../../eng-us/bc/6.0/response-cache-and-navigation.md#what-this-adds-up-to)):

| Messgröße | Vorher | Nachher |
|---|---|---|
| Gecachte Seite, auf dem Server | 0,36 ms | 0,36 ms |
| CPU pro gecachter Anfrage | 3,80 ms | 0,54 ms |
| Spitzendurchsatz | 2.961 Anfr./s | 8.941 Anfr./s |
| p99 bei 128 parallelen Anfragen | 1.317 ms | 37,9 ms |
| Neuladen einer Seite, die der Browser vorhält | 9,7 KB | 202 Byte |
| Eine gecachte 404 | 758 ms | 27 ms |

Dieselbe Seite nennt die zwei Zahlen, zwischen denen „alles liegt“: ein Cache-Treffer von 0,36 ms und ein vollständiges
Rendern von 1.382 ms
([Die zwei Zahlen](../../../eng-us/bc/6.0/response-cache-and-navigation.md#the-two-numbers-everything-sits-between)). Das ist der
Zusammenhang, den eine Überschrift verliert: Eine gecachte Seite und eine gerenderte Seite sind sehr verschiedene Dinge.

## Selbst messen

Exponential liefert `exp:benchmark` mit, das „nichts braucht als PHP mit der curl-Erweiterung“
([Benchmark](../../../eng-us/features/6.0/benchmark.md)). Die Beispielausgabe der Anleitung entstand auf einem
Demonstrationsserver mit „12 CPUs, geteilt mit anderer Arbeit bei einer Last von 5 bis 11“
([Anleitung zum Benchmarking](../../anleitungen/benchmarking.md)).

```text
./console exp:benchmark --allow-root-user                       # front page, menu pages, admin login
./console exp:benchmark --compare=https://site,https://site:8080 --allow-root-user   # two servers back to back
./console exp:benchmark --cold --allow-root-user                # rendered pages instead of cache hits
```

Notieren Sie zu jedem Ergebnis die Maschine, die PHP-Version, die Engine, die Datenbank, die Caches, die Seiten und die
Zahl der Anfragen ([Gesprächspunkte: Geschwindigkeit](../fuersprecher/gespraechspunkte-geschwindigkeit-und-velocity.md#zahlen-messen-nicht-zitieren)).
Die Anleitung warnt: „Ein Durchlauf auf einer ausgelasteten Maschine beweist wenig“
([Benchmarking, Kurz gesagt](../../anleitungen/benchmarking.md#kurz-gesagt)).

## Was Sie nicht behaupten sollten

- „Exponential beantwortet jede Seite in 0,36 ms.“ Es ist die Arbeit des Servers für eine **gecachte** Seite.
- „Velocity ist N-mal schneller als X.“ Es gibt keinen veröffentlichten Vergleich mit einem anderen Produkt.
- „Unabhängig überprüft.“ Die Quellen beschreiben die eigenen Messungen und Skripte des Projekts.
- Eine Zahl ohne ihre Bedingungen (Maschine, gecacht oder gerendert, Parallelität).

## Blickwinkel und Ideen für Überschriften

```text
„0,36 Millisekunden - für eine gecachte Seite: die Geschwindigkeitsangaben von Exponential genau lesen“
„Die Geschwindigkeit kam aus dem Cache, nicht vom Server, sagt das eigene README von Exponential“
„Wir haben den Benchmark von Exponential selbst laufen lassen: Das ist unsere Methode“
```

## Wen Sie fragen

| Frage | Fragen Sie | Wo |
|---|---|---|
| Die Skripte und Bedingungen hinter einer Zahl | 7x | info@se7enx.com oder <https://exponential.earth/about/contact-us> |
| Ihre eigene Messung, öffentlich | die Community | <https://share.exponential.earth/forums> |

## Verwandte Seiten

- [Thema: Exponential Velocity](thema-exponential-velocity.md)
- [Benchmarking von Exponential](../../anleitungen/benchmarking.md)
- [Gesprächspunkte: Geschwindigkeit und Velocity](../fuersprecher/gespraechspunkte-geschwindigkeit-und-velocity.md)
- [Themenbeiträge: Übersicht](themenbeitraege-uebersicht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
