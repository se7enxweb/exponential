Ereignissystem
==============

Geänderte Ereignisse
--------------------

#### 2017.12.2

In 2017.12.2 wurde `content/cache` so geändert, dass es zusätzlich die Liste der Objekt-IDs übergibt, damit die
Logik zum Leeren des Caches zwischen Legacy und Platform in mehr Anwendungsfällen zuverlässig arbeitet.

    content/cache ( array $NodeIdist, array $objectIdList = null )

Das neue zweite Argument ist optional, es wird jedoch dringend empfohlen, es zu befüllen, wenn eigener Code dieses
Ereignis auslöst. Wie bisher wird erwartet, dass Sie eine Liste von Knoten-IDs zurückgeben _(denselben Wert oder
ergänzt um Knoten-IDs nach eigener Logik)_.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
