---
title: Offline und wiederholte Besuche
audience: businesses, admin, website-creator
category: business-solutions
order: 90
summary: Der optionale Navigationscache von Exponential im Browser (der Service Worker /index.js) für Unternehmen erklärt - schnellere wiederholte Seitenaufrufe, die strengen Regeln, was er nie speichert, der Effekt der „zweiten Ansicht“ nach dem Veröffentlichen, sein Ausschalter und was er nicht tut.
keywords: Service Worker, Navigationscache, index.js, sw.js, wiederholte Besuche, offline, stale-while-revalidate, Velocity, Antwortcache, zweite Ansicht, Notausschalter, Unternehmen, service worker, navigation cache, repeat visits, response cache, second view, kill switch, business
type: help
---
# Offline und wiederholte Besuche

Diese Seite ist für Inhaber, Supportmitarbeiter und Administratoren von Websites, auf die Besucher oft zurückkommen:
Nachrichten, Kataloge, Mitgliederwebsites, Startseiten von Shops. Exponential kann den eigenen Browser des Besuchers
einen wiederholten Seitenaufruf sofort beantworten und die Seite im Hintergrund aktualisieren lassen. Diese Seite
erklärt, was Ihnen das bringt, die Regeln, die es sicher halten, den einen Effekt, den Ihr Supportteam kennen muss, und
wie Sie es ausschalten. Es ist optional; nichts auf Ihrer Website hängt davon ab.

## Inhalt

1. [Was er tut](#was-er-tut)
2. [Warum er hilft](#warum-er-hilft)
3. [Was er nie speichert](#was-er-nie-speichert)
4. [Der Effekt der „zweiten Ansicht“](#der-effekt-der-zweiten-ansicht)
5. [Ihn ausschalten](#ihn-ausschalten)
6. [Was er nicht ist](#was-er-nicht-ist)
7. [Fragen an Ihr Team](#fragen-an-ihr-team)

## Was er tut

Der Service Worker der Website ist ein kleines Skript, `/index.js`, „the one service worker script of an Exponential
site“ (das eine Service-Worker-Skript einer Exponential-Website, Dateikopf von `index.js`). Seine erste und bisher
einzige Funktion ist der **Navigationscache**: „repeat page visits answered from the browser, refreshed in the
background“ (wiederholte Seitenaufrufe aus dem Browser beantwortet, im Hintergrund aktualisiert). `/sw.js` war sein
früherer Name und bleibt als einzeiliges Skript erhalten, das `/index.js` lädt; Browser, die unter dem alten Namen
registriert sind, führen also denselben Code aus ([Changelog 6.0.15](../../../../changelogs/6.0/6.0.15.md)).

Der Worker wird „by the site design after page load“ registriert (vom Design der Website nach dem Laden der Seite); der
Kopf von `index.js` nennt das Media-Theme (`extension/sevenx_themes_media`) als das Design, das das tut. Die
Hosting-Dokumentation beschreibt ihn als Teil der Einrichtung „under Velocity“
([Antwortcache von Velocity: der Navigationscache im Browser](../../hosting/leistung/antwort-cache-von-velocity.md#der-navigations-cache-im-browser)).
Ob Ihre Website ihn verwendet, hängt also von Ihrem Design und Ihrem Server ab: Fragen Sie Ihr Team.

## Warum er hilft

Die Quelle erklärt es mit zwei gemessenen Zahlen aus einer Installation
([Der Antwortcache, und dem Netzwerk aus dem Weg gehen](../../../../eng-us/bc/6.0/response-cache-and-navigation.md#added-a-navigation-cache-in-the-browser-indexjs)):

| Messung | Wert |
|---|---|
| Netzwerk-Roundtrip zu einer echten Browserverbindung | 12,47 ms |
| Die Antwort des Servers auf dieselbe Anfrage | 0,36 ms |

„A round trip cannot be shortened, only skipped“: Ein Roundtrip lässt sich nicht verkürzen, nur überspringen. Die Seite
wird also aus dem eigenen Speicher des Browsers beantwortet, und das Netzwerk wird danach gefragt. Diese Zahlen sind
Messungen der Quelle auf einem Server, kein Versprechen für Ihre Website; der Punkt für ein Unternehmen ist, dass ein
wiederholter Besuch nicht mehr auf das Netzwerk wartet.

## Was er nie speichert

Aus derselben Quelle die Regeln:

- nur GET-Navigationen auf demselben Ursprung (Assets cacht der Browser bereits);
- nie eine Seite, die mit einem Sitzungscookie abgerufen wurde; die Seite eines angemeldeten Besuchers wird also nie
  aufbewahrt;
- nie etwas anderes als eine erfolgreiche Seite (200);
- nie `/admin`, `/user`, `/explayouts_ui` oder `/api`.

„If a session cookie appears, the page-side script unregisters the worker and clears its cache, rather than leaving it
to serve one visitor another's page“: Erscheint ein Sitzungscookie, meldet das Skript auf der Seite den Worker ab und
leert seinen Cache, statt ihn einem Besucher die Seite eines anderen ausliefern zu lassen.

Für ein Unternehmen bedeutet das: Kundenkonten, Warenkörbe, Bestellablauf und die Administration werden nie aus diesem
Cache beantwortet.

## Der Effekt der „zweiten Ansicht“

Der Worker zeigt zuerst die gespeicherte Kopie und aktualisiert sie im Hintergrund, sodass „a published change appears
on the **second** view, not the first“ (eine veröffentlichte Änderung bei der **zweiten** Ansicht erscheint, nicht bei
der ersten). Sagen Sie Ihrem Supportteam und Ihren Redakteuren:

| Frage | Antwort |
|---|---|
| „Ich habe eine Korrektur veröffentlicht und sehe immer noch die alte Seite“ | Laden Sie noch einmal neu; die erste Ansicht hat die Kopie aktualisiert |
| „Ein Kunde sieht noch den alten Preis“ | Sein nächster Besuch zeigt den neuen; angemeldete Kunden sind nicht betroffen |
| „Es muss sich sofort für alle ändern“ | Fragen Sie den Administrator nach dem Antwortcache und, falls nötig, nach dem Ausschalter |

Die Checkliste für Hoster enthält: „Support knows the navigation cache's 'second view' behavior“ (der Support kennt das
Verhalten der „zweiten Ansicht“ des Navigationscaches).

> **Warnung**
> Planen Sie für zeitkritische Änderungen (ein zurückgerufenes Produkt, ein falscher Preis) mit Ihrem Administrator, wie
> der Antwortcache von Velocity geleert wird, und nehmen Sie hin, dass ein wiederkehrender Besucher die alte Seite einmal
> sehen kann ([Antwortcache von Velocity](../../hosting/leistung/antwort-cache-von-velocity.md)).

## Ihn ausschalten

Der dokumentierte **Notausschalter**: Liefern Sie `index.js` als Skript aus, das sich selbst abmeldet. „Every browser
drops it on its next update check, within 24 hours, because `index.js` is `no-cache`“ (jeder Browser verwirft ihn bei
seiner nächsten Prüfung auf Aktualisierungen innerhalb von 24 Stunden, weil `index.js` `no-cache` ist). Die Datei
`/sw.js` muss erhalten bleiben, bis kein Browser mehr die alte Registrierung halten kann; sie zu früh zu entfernen,
„would leave those browsers on their last copy of the old worker for good“ (würde diese Browser für immer auf ihrer
letzten Kopie des alten Workers lassen, Dateikopf von `sw.js`).

## Was er nicht ist

- **Kein Offline-Modus für die ganze Website.** Die Quellen beschreiben einen Cache von Seiten, die ein Browser bereits
  besucht hat, keine Website, die ohne Verbindung funktioniert. Nie besuchte Seiten sind nicht vorhanden.
- **Keine App.** Er braucht keine Installation durch den Besucher und bietet keine Benachrichtigungen.
- **Kein Ersatz für Server-Caches.** Der Antwortcache und das HTTP-Caching erledigen weiterhin die Hauptarbeit
  ([Caching für Verfügbarkeit](../zuverlaessigkeit/caching-fuer-verfuegbarkeit.md)).

## Fragen an Ihr Team

- [ ] Registriert unser Design den Service Worker, und in welchen Siteaccesses?
- [ ] Wird `/index.js` mit Revalidierung ausgeliefert (die Quelle führt es unter den Pfaden auf, die „always
      revalidated“, immer revalidiert, werden)?
- [ ] Kennt der Support die zweite Ansicht?
- [ ] Wer kann ihn ausschalten, und wie schnell?
- [ ] Ist `/sw.js` für ältere Registrierungen noch vorhanden?

## Verwandte Seiten

- [Zuverlässigkeit des App-Backends](zuverlaessigkeit-des-app-backends.md)
- [Responsive Websites](responsive-websites.md)
- [Statischer Cache und Antwortcache](../zuverlaessigkeit/statischer-cache-und-antwortcache.md)
- [Antwortcache von Velocity](../../hosting/leistung/antwort-cache-von-velocity.md)
- [Der Antwortcache, und dem Netzwerk aus dem Weg gehen](../../../../eng-us/bc/6.0/response-cache-and-navigation.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
