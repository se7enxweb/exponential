---
title: Velocity für Unternehmen
audience: businesses, project-manager, hosting, admin
category: business-solutions
order: 90
summary: Was Exponential Velocity, der mit Exponential gelieferte Anwendungsserver, einem Unternehmen bringt, in einfachen Worten, die dokumentierten Zahlen mit ihrem Zusammenhang, was er von Ihrem Hosting braucht und wann ein klassischer Webserver weiterhin die richtige Wahl ist.
keywords: Velocity, Exponential Velocity, Anwendungsserver, dauerhafte Worker, Antwortcache, Leistung, sanfter Neustart, Watchdog, Anforderungen an das Hosting, application server, persistent workers, response cache, performance, graceful restart, watchdog, Qbix, FrankenPHP, hosting requirements
type: help
---
# Velocity für Unternehmen

Diese Seite richtet sich an Inhaber und Führungskräfte, die von ihrem Entwickler oder Hosting-Partner „Velocity“ hören
und wissen wollen, was das für das Unternehmen bedeutet. Sie erklärt, was Velocity ist, welche Zahlen das Projekt
dokumentiert und wie Sie sie lesen, was es für die Verfügbarkeit tut, was es von Ihrem Hosting braucht und wann ein
klassischer Webserver die bessere Antwort bleibt. Der technische Einstieg ist
[Velocity: Exponential in einem Webserver mit dauerhaften Workern betreiben](../../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md).

## Inhalt

1. [Was Velocity ist](#was-velocity-ist)
2. [Die dokumentierten Zahlen, ehrlich gelesen](#die-dokumentierten-zahlen-ehrlich-gelesen)
3. [Geschwindigkeit ohne Velocity](#geschwindigkeit-ohne-velocity)
4. [Was es für die Verfügbarkeit tut](#was-es-für-die-verfügbarkeit-tut)
5. [Was es von Ihrem Hosting braucht](#was-es-von-ihrem-hosting-braucht)
6. [Wann ein klassischer Server die richtige Antwort ist](#wann-ein-klassischer-server-die-richtige-antwort-ist)
7. [Fragen an Ihren Entwickler oder Hoster](#fragen-an-ihren-entwickler-oder-hoster)

## Was Velocity ist

Aus der [README, „How Exponential is served“](https://github.com/se7enxweb/exponential/blob/main/README.md#exponential-velocity--the-fast-option):
„Velocity ist ein in PHP geschriebener Webserver, der mit Exponential geliefert wird und die Anwendung zwischen den
Anfragen geladen hält.“ Er basiert auf dem Qbix-Server und wird zusammen mit Exponential entwickelt.

In einfachen Worten: Ein klassischer Aufbau startet die Anwendung für jede Seitenanfrage neu und wirft die Arbeit danach
weg. Velocity lädt die Anwendung einmal und hält Worker bereit, und er fügt einen Antwortcache hinzu, der eine
wiederholte Anfrage beantwortet, ohne die Anwendung überhaupt zu wecken (README, „Why it is faster“).

Velocity kann drei Engines betreiben, gewählt mit einer einzigen Einstellung
([README, „Engines“](https://github.com/se7enxweb/exponential/blob/main/README.md#engines-frankenphp-phps-built-in-server-or-qbix)):

| Engine | Rolle, wie die README sie beschreibt |
|---|---|
| Qbix | „Die empfohlene Engine für jede Phase, von der Entwicklung bis zur Produktion, und die schnellste“ |
| FrankenPHP | „Die produktionsreife Alternative“ |
| Eingebauter Server von PHP | „Für die Entwicklung, und der mitgelieferte Standard, weil er immer funktioniert“ |

## Die dokumentierten Zahlen, ehrlich gelesen

Die README sagt: „**0,36 ms pro gecachter Seite, 8.941 Anfragen pro Sekunde im Dauerbetrieb, und wiederholte Besuche
berühren nie das Netzwerk.**“ Danach erklärt sie jede Zahl, und die Erklärungen sind ebenso wichtig wie die Zahlen
([README, „What that measures as“](https://github.com/se7enxweb/exponential/blob/main/README.md#what-that-measures-as)):

| Zahl | Was sie laut README misst |
|---|---|
| 0,36 ms | Die eigene Arbeit des Servers, um eine **gecachte** Seite zu beantworten, als Median über Hunderte von Anfragen, kein Bestwert |
| 8.941 Anfragen pro Sekunde | Im Dauerbetrieb, gemessen auf einem Rechner mit 12 Kernen, der eine echte Seite ausliefert, ohne Fehler bei jeder getesteten Parallelität, bis zu 384 gleichzeitigen Verbindungen |
| Wiederholte Besuche berühren nie das Netzwerk | Bezieht sich auf den **optionalen** Navigations-Cache (einen Service Worker) im Browser des Besuchers |

Die README ergänzt, dass sich diese Zahlen mit Skripten nachvollziehen lassen, die mit dem Projekt geliefert werden,
und dass die Methodik aufgeschrieben ist. Was die Zahlen **nicht** sagen:

- Sie sind kein Versprechen für Ihren Server, Ihre Seiten oder Ihren Verkehr. Hardware, Seitengewicht, Datenbank und der
  Anteil Ihres Verkehrs, der sich cachen lässt, verändern das Ergebnis.
- Ein angemeldeter Kunde (ein Shop-Kunde mit Warenkorb, ein Mitglied, ein Redakteur) wird nie aus dem Antwortcache
  bedient ([settings/velocity.ini](../../../../../settings/velocity.ini), `[CacheSettings]`: „Angemeldete Besucher
  werden nie aus dem Cache bedient“). Ihre Seiten werden gerendert, allerdings von Workern, die die Anwendung geladen
  halten.

Um Ihre eigene Website zu messen, kann Ihr Betrieb `exp:benchmark` verwenden, das zwei Server direkt nacheinander
vergleicht ([Benchmarks](../../../anleitungen/benchmarking.md)).

## Geschwindigkeit ohne Velocity

Die README sagt ausdrücklich: „Der größte Teil der Geschwindigkeit kam aus Korrekturen an Exponential und am Cache,
nicht aus dem Prozessmodell des Servers“, und „Exponential 6.0.15+ ist auch unter Apache oder Nginx unter Last schnell“
([README, „The part that matters even if you never run Velocity“](https://github.com/se7enxweb/exponential/blob/main/README.md#the-part-that-matters-even-if-you-never-run-velocity)).
Zu diesen Korrekturen gehören: Das Neuladen eines wiederkehrenden Besuchers wird mit 202 Byte statt mit der ganzen Seite
beantwortet, und eine abgelaufene Seite wird von einer Anfrage neu aufgebaut, während alle anderen die vorhandene Kopie
erhalten. Siehe [Caching für Verfügbarkeit](caching-fuer-verfuegbarkeit.md).

Bei der Wahl von Velocity geht es also darum, die Obergrenze anzuheben, nicht darum, ob die Website überhaupt
nutzbar ist.

## Was es für die Verfügbarkeit tut

| Funktion | Was sie für das Unternehmen bedeutet | Quelle |
|---|---|---|
| Sanftes Neuladen | `graceful` „führt den Server neu aus, ohne den lauschenden Socket freizugeben, sodass ein Deployment keine Anfragen verliert“ | [README](https://github.com/se7enxweb/exponential/blob/main/README.md#using-it) |
| Status für das Monitoring | „`status --json` ist für das Monitoring da“; `status` meldet `running` nur, wenn der Server antwortet | [README](https://github.com/se7enxweb/exponential/blob/main/README.md#using-it), [Velocity-Engines](../../../bc/6.0/velocity-engines.md#stopp-neustart-und-status-der-qbix-engine) |
| Watchdog | Ein systemd-Timer startet den Server nach einem Absturz oder einem Abbruch wegen Speichermangels wieder | [Zustandsprüfungen und Verfügbarkeit](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md#velocity-zustandsendpunkt-und-watchdog) |
| Neustart, der von selbst abschließt | Ein Neustart, dessen Aufrufer zwischen Stopp und Start beendet wird, lässt den Server nicht mehr ausgefallen zurück | [Velocity-Engines](../../../bc/6.0/velocity-engines.md#ein-abgeschnittener-neustart-oder-deploy) |
| Deployment mit einem Befehl | `exp:velocity deploy` führt jeden Schritt zum Ausliefern einer PHP-Änderung in der richtigen Reihenfolge aus | [Unterbrechungsfreie Deployments](unterbrechungsfreie-deployments.md) |
| Eigenes HTTPS | Liefert HTTPS selbst aus und tauscht ein erneuertes Zertifikat ohne Neustart ein | [README](https://github.com/se7enxweb/exponential/blob/main/README.md#when-a-traditional-server-is-still-the-right-answer) |
| Control Panel und Dashboard | Ansichten des Servers, die weiter funktionieren, wenn die Anwendung defekt ist | [Velocity Control Panel](../../../funktionen/6.0/velocity-control-panel.md) |

## Was es von Ihrem Hosting braucht

- **PHP 8.0 oder neuer** (ab Velocity 0.0.4.54; davor 8.1)
  ([README, „What version of PHP is required“](https://github.com/se7enxweb/exponential/blob/main/README.md#what-version-of-php-is-required)).
- **Einen langlebigen Prozess.** „Shared Hosting erlaubt das meist nicht“
  ([README](https://github.com/se7enxweb/exponential/blob/main/README.md#when-a-traditional-server-is-still-the-right-answer)).
  Nötig ist ein VPS, ein dedizierter Server, ein Container oder ein Managed Hosting, das es unterstützt
  ([Ein Hosting-Modell wählen](../../hosting/modelle/ein-hosting-modell-waehlen.md)).
- **Einen Neustart nach Codeänderungen.** „Worker halten die Anwendung zwischen den Anfragen im Speicher. Starten Sie
  den Server nach der Änderung eines Templates, einer Einstellung oder einer Klasse neu — das Leeren von Caches allein
  verdrängt nicht, was ein Worker bereits hält“
  ([README, „One thing to know before you rely on it“](https://github.com/se7enxweb/exponential/blob/main/README.md#one-thing-to-know-before-you-rely-on-it)).
  Der Deploy-Befehl erledigt das für Sie.

## Wann ein klassischer Server die richtige Antwort ist

Aus der README ([When a traditional server is still the right answer](https://github.com/se7enxweb/exponential/blob/main/README.md#when-a-traditional-server-is-still-the-right-answer)):

- **Sie brauchen eine Trennung je Website auf Ebene des Betriebssystems.** „Ein einzelner Serverprozess, der mehrere
  Websites bedient, ist eine Bequemlichkeit beim Routing, keine Sicherheitsgrenze.“ Betreiben Sie eine Velocity-Instanz
  pro Website, jede als eigener Benutzer, oder einen klassischen Server.
- **Ihr Hosting lässt Sie keinen langlebigen Prozess betreiben.** Dort ist Apache oder LiteSpeed mit `.htaccess` die
  Antwort.
- **Sie haben bereits ein abgestimmtes Nginx oder Apache davor.** Behalten Sie es; Velocity läuft problemlos hinter
  einem Reverse Proxy.

Exponential „setzt keinen bestimmten“ Webserver voraus (README); Apache, Nginx, LiteSpeed, Caddy, IIS und andere
funktionieren alle. Die Wahl eines klassischen Servers ist kein Rückschritt bei der Zuverlässigkeit; sie ist eine andere
Abwägung.

## Fragen an Ihren Entwickler oder Hoster

- [ ] Welche Engine liefert unsere Website aus, und warum diese?
- [ ] Ist der Watchdog installiert, und wer liest sein Log?
- [ ] Nutzen Deployments `exp:velocity deploy` oder `graceful`, damit keine Besucher verloren gehen?
- [ ] Ist der Antwortcache eingeschaltet, und wie lange darf eine Seite gecacht werden (`[CacheSettings] DefaultTtl`,
      ab Werk 30 Sekunden)?
- [ ] Wenn sich mehrere Websites einen Server teilen, läuft jede als eigener Benutzer des Betriebssystems?
- [ ] Haben wir unsere eigenen Seiten vorher und nachher mit `exp:benchmark` gemessen?

## Verwandte Seiten

- [Unterbrechungsfreie Deployments](unterbrechungsfreie-deployments.md)
- [Statischer Cache und Antwortcache](statischer-cache-und-antwortcache.md)
- [Velocity für Hoster](../../hosting/server/velocity-fuer-hoster.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
