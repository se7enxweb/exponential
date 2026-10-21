---
title: Shop-Leistung zu Spitzenzeiten
audience: businesses, hosting, project-manager
category: business-solutions
order: 200
summary: Einen Exponential-Shop während eines Ausverkaufs, einer Feiertagssaison oder eines Newsletter-Versands schnell halten - welche Seiten sich cachen lassen und welche nicht, die Stellschrauben nach ihrer Wirkung geordnet, Messen vor der Spitze und ein Plan für den Tag.
keywords: Leistung, Lastspitzen, Ausverkauf, Feiertagssaison, Caching, Velocity, Response-Cache, View-Cache, Benchmark, Last, langsame Website, Leistung der Kasse, performance, peak traffic, sale, holiday season, caching, response cache, view cache, benchmark, load, slow site, checkout performance
type: help
---
# Shop-Leistung zu Spitzenzeiten

Diese Seite richtet sich an Inhaber und Projektleiter, die arbeitsreiche Tage erwarten: einen Ausverkauf, eine
Feiertagssaison, einen Newsletter, der an alle Kunden gleichzeitig geht. Sie erklärt, warum sich Shop-Seiten unter
Last anders verhalten als andere Seiten, was Sie vorbereiten und was Sie tun, wenn die Website langsamer wird. Die
technischen Einzelheiten stehen unter [Leistung und Caching für Websites](../../../../eng-us/help/creators/website-creators/performance-and-caching.md).

## Inhalt

1. [Warum Shops anders sind](#warum-shops-anders-sind)
2. [Seiten, die sich cachen lassen](#seiten-die-sich-cachen-lassen)
3. [Seiten, die sich nicht cachen lassen](#seiten-die-sich-nicht-cachen-lassen)
4. [Die Stellschrauben nach ihrer Wirkung](#die-stellschrauben-nach-ihrer-wirkung)
5. [Vor der Spitze messen](#vor-der-spitze-messen)
6. [Die Woche davor](#die-woche-davor)
7. [Am Tag selbst](#am-tag-selbst)
8. [Nach der Spitze](#nach-der-spitze)

## Warum Shops anders sind

Die meisten Besucher einer Inhaltswebsite lesen Seiten, die für alle gleich sind, und das lässt sich gut cachen. Ein
Shop mischt zwei Arten von Seiten: Produkt- und Kategorieseiten, die für alle gleich sind, und Warenkorb-, Kassen- und
Kontoseiten, die persönlich sind. Spitzen treffen beide, und mit den persönlichen Seiten wird Geld verdient.

## Seiten, die sich cachen lassen

Produktseiten, Kategorieseiten, Landingpages und die Startseite können aus dem Cache ausgeliefert werden. Exponential
hat mehrere Cache-Schichten ([Leistung und Caching für Websites](../../../../eng-us/help/creators/website-creators/performance-and-caching.md#the-layers)):
den Velocity-Response-Cache für ganze öffentliche Seiten, den Content-View-Cache für den Hauptbereich jeder Seite, den
Template-Block-Cache für Menüs und Fußzeilen, kompilierte Templates und OPcache.

Der Velocity-Response-Cache beantwortet öffentliche Seiten, ohne PHP auszuführen, und überspringt Anfragen, die ein
Sitzungs-Cookie oder einen `Authorization`-Header tragen, oder Antworten, die ein Cookie setzen
([Velocity-Response-Cache](../../../../eng-us/features/6.0/velocity-response-cache.md)).

## Seiten, die sich nicht cachen lassen

| Seite | Warum |
|---|---|
| Warenkorb, Kasse, Bestätigung | Persönlich; der Besucher hat eine Sitzung |
| Kontoseiten, Wunschliste, Belege | Persönlich |
| Seiten mit Formularen, die ein Formular-Token tragen | Das Token gilt pro Sitzung |
| Zahlungsbenachrichtigungen des Anbieters | Müssen jedes Mal die Anwendung erreichen |

Die CDN-Seite nennt dasselbe: Seiten für angemeldete Benutzer, Warenkörbe, Kasse, Kontoseiten und Formulare mit einem
Formular-Token dürfen nicht von einem CDN gecacht werden ([HTTP-Caching, Proxys und CDNs](../../../../eng-us/help/creators/website-creators/http-caching-and-cdns.md)).

Die Kapazität, auf die es in der Spitze ankommt, lautet also: Wie viele persönliche Anfragen pro Sekunde kann der
Server bewältigen, nachdem ihm die gecachten Seiten abgenommen wurden?

## Die Stellschrauben nach ihrer Wirkung

Nach der Leistungsseite für Website-Ersteller, die dem Installationsbuch folgt:

1. **Mit Velocity ausliefern** und mit seinem Response-Cache; persistente Worker halten die Anwendung warm
   ([Velocity für Unternehmen](../../../../eng-us/help/businesses/reliability/velocity-for-businesses.md)).
2. **Content-View-Cache und Template-Block-Cache eingeschaltet lassen** (beide sind in den ausgelieferten
   Einstellungen eingeschaltet) und die Website nach einem vollständigen Leeren vorwärmen.
3. **OPcache** für kompiliertes PHP.
4. **APCu** für die Speicherstufe des Response-Caches und den gemeinsamen Abfrage-Cache.
5. **Der SQL-Abfrage-Cache**, auf Ihrer Website gemessen.
6. **Die Datenbank** selbst.

Lassen Sie den statischen Cache ausgeschaltet; der Response-Cache bietet den Besuchern denselben Vorteil, ohne das
Veröffentlichen zu verlangsamen (dieselbe Seite). Die Abstimmung auf Seiten des Hosters steht unter
[Cache-Schichten](../../hosting/leistung/cache-schichten.md) und
[Abstimmung von PHP-FPM](../../hosting/leistung/abstimmung-von-php-fpm.md).

## Vor der Spitze messen

Exponential liefert einen Benchmark-Befehl mit, der Median, p90 und p99 der Antwortzeiten pro Seite ausgibt und Läufe
mit einem Ausgangswert vergleicht ([Leistung und Caching für Websites](../../../../eng-us/help/creators/website-creators/performance-and-caching.md#measure)).
Bitten Sie Ihren Hoster oder Entwickler,

- [ ] ihn einige Wochen vor der Spitze auf dem Live-System auszuführen und das Ergebnis als Ausgangswert zu speichern;
- [ ] eine Produktseite, eine Kategorieseite und den Warenkorb einzubeziehen;
- [ ] ihn nach jeder Änderung zu wiederholen, auf derselben Maschine, direkt hintereinander;
- [ ] während eines Lasttests einige vollständige Bestellvorgänge im Testmodus des Zahlungsanbieters zu testen, wenn
  Ihr Hoster einen anbietet.

Die Kapazitätsplanung im Allgemeinen steht unter [Lastspitzen planen](../../../../eng-us/help/businesses/reliability/peak-traffic-planning.md).

## Die Woche davor

- [ ] Keine Code- oder Designänderungen in den letzten Tagen vor der Spitze (ein Änderungsstopp).
- [ ] Landingpages der Kampagne veröffentlicht, Caches vorgewärmt.
- [ ] Preise und Rabattregeln gesetzt und getestet; eine Testbestellung aufgegeben.
- [ ] Cronjobs laufen, sodass alte Warenkörbe bereinigt werden ([Cronjobs](../../../anleitungen/cronjobs.md)).
- [ ] Backups überprüft ([Backups, die sich wiederherstellen lassen](../zuverlaessigkeit/backups-die-sich-wiederherstellen-lassen.md)).
- [ ] Die Statusseite des Zahlungsanbieters als Lesezeichen gespeichert; sein Support-Kontakt zur Hand.
- [ ] Aufgeschrieben, wen man beim Hoster anruft und wer entscheidet, den Wartungsmodus einzuschalten.
- [ ] Newsletter-Versände verteilt, statt an alle Kunden in derselben Minute, wenn Ihr Newsletter-Werkzeug das erlaubt.

## Am Tag selbst

| Anzeichen | Erster Schritt |
|---|---|
| Seiten für alle langsam | Folgen Sie dem Runbook des Hosters für eine langsame Website ([Runbook: langsame Website](../../hosting/runbooks/langsame-website.md)) |
| Website nicht erreichbar | [Runbook: Website nicht erreichbar](../../hosting/runbooks/website-nicht-erreichbar.md) |
| Bestellungen brechen mit „payment failed“ ab | Prüfen Sie den Status des Anbieters; sehen Sie sich die Zahl der Bestellungen mit Payment failed im Dashboard an |
| Bezahlte Bestellungen nicht als bezahlt markiert | Benachrichtigungsadresse blockiert? ([Zahlungen mit PayPal](zahlungen-mit-paypal.md#wenn-etwas-schiefgeht)) |

Vermeiden Sie es, während der Spitze alle Caches zu leeren: Jede Seite muss dann im ungünstigsten Moment neu aufgebaut
werden. Leeren Sie nur, was eine Änderung braucht ([Leistung und Caching für Websites](../../../../eng-us/help/creators/website-creators/performance-and-caching.md#clearing-caches-in-the-right-order)).

## Nach der Spitze

- Vergleichen Sie den Umsatz pro Tag und die abgebrochenen Warenkörbe im Dashboard mit den Wochen davor.
- Sehen Sie sich die langsamsten Seiten in den Anfrage-Logs an ([Anfrage-Logs](../../hosting/betrieb/anfrage-logs.md)).
- Schreiben Sie auf, was schiefgegangen ist und was Sie beim nächsten Mal vorbereiten ([Störungsbehandlung für kleine Teams](../../../../eng-us/help/businesses/reliability/incident-response-for-small-teams.md)).

## Verwandte Seiten

- [Leistung und Caching für Websites](../../../../eng-us/help/creators/website-creators/performance-and-caching.md)
- [Statischer Cache und der Velocity-Response-Cache](../../../../eng-us/help/creators/website-creators/static-cache-and-velocity.md)
- [Caching für Verfügbarkeit](../zuverlaessigkeit/caching-fuer-verfuegbarkeit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
