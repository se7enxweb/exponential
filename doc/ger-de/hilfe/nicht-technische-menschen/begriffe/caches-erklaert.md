---
title: Caches erklärt
audience: non-technical, editor, customer
category: plain-language
order: 15
summary: Warum eine Website fertige Kopien ihrer Seiten bereithält, warum das nach dem Veröffentlichen manchmal eine alte Seite zeigt, was Sie gefahrlos im Browser dagegen tun können und wann Sie jemand anderen fragen - mit dem Vergleich einer Bäckerei.
keywords: Cache, alte Seite, veraltete Seite, Cache leeren, aktualisieren, neu laden, Browser-Cache, CDN, Geschwindigkeit, erklärt, cache, old page, stale page, clear cache, refresh, reload, browser cache, speed, explained
type: help
---
# Caches erklärt

## Inhalt

1. [Der Vergleich: eine Bäckerei](#der-vergleich-eine-bäckerei)
2. [Meistens tun Sie gar nichts](#meistens-tun-sie-gar-nichts)
3. [Wenn Sie eine alte Seite sehen](#wenn-sie-eine-alte-seite-sehen)
4. [Einen Cache selbst leeren](#einen-cache-selbst-leeren)
5. [Wann Sie jemand anderen fragen](#wann-sie-jemand-anderen-fragen)

## Der Vergleich: eine Bäckerei

Eine Bäckerei backt nicht jedes Brot erst, wenn ein Kunde hereinkommt: Sie backt am Morgen und hält das Brot im Regal
bereit, damit Kunden sofort bedient werden. Eine Website macht dasselbe mit Seiten. Eine Seite aufzubauen kostet Arbeit;
eine fertige Kopie bereitzuhalten (einen **Cache**) macht die Website schnell. Wenn sich das Rezept ändert, werden die
alten Brote aus dem Regal genommen und neue gebacken.

## Meistens tun Sie gar nichts

Exponential erneuert seine Caches, wenn Inhalte veröffentlicht werden; Leeren von Hand ist daher die Ausnahme
([Welchen Cache leere ich, und wie?](../../handbuch/welchen-cache-leere-ich.md)). Wenn Sie einen Artikel veröffentlichen,
werden die Seiten, die ihn zeigen, von selbst neu aufgebaut.

## Wenn Sie eine alte Seite sehen

Wenn Sie veröffentlicht haben und noch die alte Version sehen, gehen Sie diese Liste der Reihe nach durch:

1. **Wurde es wirklich veröffentlicht?** Sehen Sie unter **Meine Entwürfe** auf dem Dashboard nach. Steht Ihr Entwurf
   dort, öffnen Sie ihn und drücken Sie **Zur Veröffentlichung schicken**
   ([Entwürfe, Versionen und Veröffentlichen erklärt](entwuerfe-versionen-und-veroeffentlichen-erklaert.md)).
2. **Wartet es auf eine Freigabe?** Sehen Sie unter **Meine Warteliste** nach.
3. **Liegt es an Ihrem Browser?** Auch Ihr eigener Browser hält Kopien bereit. Laden Sie die Seite mit gedrückter
   Umschalttaste neu oder öffnen Sie sie in einem privaten Fenster.
4. **Ist es ein Cache der Website?** Warten Sie eine Minute und laden Sie neu. Ist sie immer noch alt, leeren Sie den
   Inhalts-Cache (unten) oder fragen Sie nach.

## Einen Cache selbst leeren

Nur wenn Ihre Rolle es erlaubt (die Berechtigung heißt `setup/managecache`):

1. Verwenden Sie in der rechten Werkzeugleiste der Administration **Cache leeren**: Wählen Sie in der Liste **Inhalt**
   und klicken Sie auf **Leeren**.
2. Laden Sie die Seite in einem privaten Fenster neu.

Leeren Sie den kleinsten Cache, der das Problem löst. **Den kompletten Cache leeren** funktioniert auch, aber die
Website ist danach eine Weile langsam, weil jede Seite beim ersten Besuch neu aufgebaut wird; vermeiden Sie es daher zu
Stoßzeiten ([Welchen Cache leere ich, und wie?](../../handbuch/welchen-cache-leere-ich.md#was-schiefgehen-kann)).

## Wann Sie jemand anderen fragen

| Situation | Wen |
|---|---|
| Die Seite ist nach dem Leeren des Inhalts-Caches immer noch alt | Ihren Hoster: Ein Cache außerhalb von Exponential (ein Proxy, ein CDN oder der Antwort-Cache des Webservers) hält sie vielleicht fest |
| Eine Änderung an Design oder Einstellungen erscheint nicht | Den Entwickler, der sie vorgenommen hat: Templates und Einstellungen haben eigene Caches, und Einstellungen brauchen vielleicht einen Neustart auf dem Server |
| Die ganze Website ist nach dem Leeren langsam | Warten Sie ein paar Minuten; bleibt sie langsam, sagen Sie es Ihrem Hoster ([Geschwindigkeit und Caching](../../kunden/gesund-halten/geschwindigkeit-und-caching.md)) |

## Verwandte Seiten

- [Begriffe erklärt](README.md)
- [Welchen Cache leere ich, und wie?](../../handbuch/welchen-cache-leere-ich.md)
- [Geschwindigkeit und Caching](../../kunden/gesund-halten/geschwindigkeit-und-caching.md)
- [Etwas sieht falsch aus](../wenn-es-hakt/etwas-sieht-falsch-aus.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
