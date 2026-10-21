---
title: Geschwindigkeit und Caching
audience: customer, editor, businesses
category: customer-success
order: 14
summary: Was ein Betreiber über die Caches einer Exponential-Website wissen muss - warum eine Änderung manchmal nicht sofort erscheint, was zu melden ist, wenn die Website langsam ist, was standardmäßig eingeschaltet ist und was Sie Ihren Hoster vor einem geschäftigen Tag fragen sollten.
keywords: Geschwindigkeit, langsame Website, Cache, Caching, veraltete Seite, Response-Cache, Velocity, statischer Cache, Vorladen, Benchmark, speed, slow site, caching, stale page, response cache, static cache, preload, benchmark
type: help
---
# Geschwindigkeit und Caching

Caches machen die Website schnell und halten sie unter Last stabil. Sie erklären auch die beiden häufigsten Fragen von
Betreibern: „Warum ist die Website langsam?“ und „Warum erscheint meine Änderung nicht?“. Diese Seite beantwortet beide
aus Sicht des Betreibers. Die technischen Seiten sind [Caches](../../../anleitungen/caches.md) und
[Caching für Verfügbarkeit](../../unternehmen/zuverlaessigkeit/caching-fuer-verfuegbarkeit.md).

## Die Schichten in einer Tabelle

Aus [Caching für Verfügbarkeit](../../unternehmen/zuverlaessigkeit/caching-fuer-verfuegbarkeit.md):

| Schicht | In einfachen Worten | Ausgeliefert |
|---|---|---|
| Browser-Cache | Der Browser des Besuchers behält Bilder, Stylesheets und Skripte | Immer |
| Response-Cache von Velocity | Velocity beantwortet wiederholte Anfragen nach öffentlichen Seiten selbst | Ein, wenn Velocity die Website ausliefert |
| View-Cache der Inhalte | Der gerenderte Inhalt jeder Seite, pro Sprache und Rolle | Ein |
| Template-Block-Cache | Teile des Seitenlayouts, etwa Menüs | Ein |
| Statischer Cache | Öffentliche Seiten als einfache Dateien gespeichert | Aus |
| Rollenbewusster HTTP-Cache | Ganze Seiten pro Berechtigungskontext | Aus, bis er eingeschaltet wird |

Ein Content Delivery Network oder ein cachender Proxy vor der Website ist optional und nicht Teil von Exponential.

## „Meine Änderung erscheint nicht“

- **Warten Sie ein paar Sekunden und laden Sie neu.** Velocity beantwortet Seiten „für ein paar Sekunden aus seinem
  eigenen Response-Cache“ ([Caches](../../../anleitungen/caches.md)).
- **Prüfen Sie, dass die Version veröffentlicht wurde**, nicht als Entwurf liegen geblieben ist ([Veröffentlichen und Versionen](../../veroeffentlichen-und-versionen.md)).
- **Versuchen Sie ein privates Fenster**, das den Cache Ihres Browsers ignoriert.
- Erscheint sie dann immer noch nicht, nennen Sie Ihrem Administrator die Seitenadresse und die Uhrzeit, zu der Sie
  veröffentlicht haben. Caches zu leeren ist seine Aufgabe: „Leeren Sie das Kleinste, was das Problem behebt“, denn
  alles zu leeren „kostet die nächsten Besucher jeweils eine langsame Seite“ ([Caching für Verfügbarkeit](../../unternehmen/zuverlaessigkeit/caching-fuer-verfuegbarkeit.md)).

## „Die Website ist langsam“

Melden Sie es so, dass es sich messen lässt:

| Sagen Sie | Warum |
|---|---|
| Welche Seiten, oder alle | Eine langsame Seite ist ein anderes Problem als eine langsame Website |
| Seit wann | Eine Änderung um diese Zeit ist der erste Verdächtige |
| Angemeldet oder nicht | Seiten im angemeldeten Zustand werden anders gecacht |
| Von wo | Ihr Büronetz, ein Smartphone, ein anderes Land |
| Wie langsam, ungefähr | „Zehn Sekunden für die Startseite“ lässt sich später vergleichen |

Das Runbook für eine langsame Website beginnt mit „zuerst messen“ und trennt kalte von warmen Seiten, vor jeder
Änderung ([Langsame Website](../../hosting/runbooks/langsame-website.md)). Nach einem Leeren oder einem Deployment „bezahlt der
erste Besucher für das Rendern“; der Administrator kann die Seiten mit **Seiten vorladen** vorwärmen
([Caches vorladen](../../../anleitungen/caches-vorwaermen.md)).

## Vor einem geschäftigen Tag

Eine Aktion, eine Kampagne, ein Newsletter oder eine Erwähnung in der Presse kann die Zugriffe vervielfachen. Fragen Sie
Ihren Hoster, Wochen im Voraus ([Planung für Lastspitzen](../../unternehmen/zuverlaessigkeit/lastspitzen-planen.md)):

- [ ] Haben Sie unsere wichtigsten Seiten unter Last gemessen ([Benchmarking](../../../anleitungen/benchmarking.md))?
- [ ] Sind die Caches eingeschaltet, die eingeschaltet sein sollten? **Setup > Systeminformationen** warnt, wenn das
      View-Caching oder das Template-Caching ausgeschaltet ist ([Systeminformationen](../../../anleitungen/systeminformationen.md)).
- [ ] Werden Sie während der Spitze darauf verzichten, Caches zu leeren und Deployments durchzuführen?
- [ ] Wer hat an diesem Tag Bereitschaft?

## Worum Sie nicht bitten sollten

- „Alle Caches leeren“ als routinemäßige Lösung. Das macht die nächsten Seiten langsam und behebt selten das
  eigentliche Problem.
- Einen Cache abschalten, um Änderungen schneller zu sehen. Redakteure sehen sich ihre Arbeit stattdessen in der
  Vorschau an ([Vorschau vor dem Veröffentlichen](../../unternehmen/zuverlaessigkeit/vorschau-vor-dem-veroeffentlichen.md)).

## Verwandte Seiten

- [Statischer Cache und Response-Cache](../../../../eng-us/help/businesses/reliability/static-and-response-cache.md)
- [Velocity für Unternehmen](../../../../eng-us/help/businesses/reliability/velocity-for-businesses.md)
- [Caching-Schichten (für Hoster)](../../hosting/leistung/cache-schichten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
