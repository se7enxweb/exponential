---
title: Cache-Aufwärmen nach Änderungen
audience: government, hosting, admin
category: public-sector
order: 90
summary: Für Betriebsteams in öffentlichen Stellen, die nach Bereitstellungen, Wiederherstellungen oder dringenden Korrekturen Caches leeren. Wie Sie die Website wieder aufwärmen, damit die ersten Besucher nicht für jedes Rendern bezahlen, mit preload.php, warm.php, der Ansicht „Seiten vorladen“ und den Aufwärm-URLs der Bereitstellung.
keywords: Cache-Aufwärmen, Vorladen, warm.php, preload.php, Seiten vorladen, WarmUpURL, nach der Bereitstellung, nach der Wiederherstellung, erster Besucher, kalter Cache, Lastspitze, Ankündigung, cache warming, preload, Preload Sites, after deploy, after restore, first visitor, cold cache, traffic peak, announcement
type: help
---
# Cache-Aufwärmen nach Änderungen

Diese Seite richtet sich an Betriebsteams in öffentlichen Stellen, die nach einer Bereitstellung, einer
Wiederherstellung oder einer dringenden Korrektur Caches leeren und nicht wollen, dass die ersten Besucher (oder ein
Ansturm nach einer Pressemitteilung) auf eine langsame Website treffen. Sie beantwortet, wie Sie die Caches mit den
Werkzeugen von Exponential wieder aufwärmen. Die Referenzen sind [Caches vorladen](../../../../anleitungen/caches-vorwaermen.md),
[Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md) und die Hosting-Seite
[Caches leeren und aufwärmen](../../../hosting/leistung/caches-leeren-und-vorwaermen.md).

![Vorladen: die Seiten einer Website abrufen, damit ihre Caches warm sind, bevor Besucher kommen](../../../../../eng-us/help/images/government/gov-preload.webp)

## Inhalt

1. [Warum aufwärmen](#warum-aufwärmen)
2. [Die Werkzeuge](#die-werkzeuge)
3. [Aufwärmen nach einer Bereitstellung](#aufwärmen-nach-einer-bereitstellung)
4. [Aufwärmen vor einer Ankündigung](#aufwärmen-vor-einer-ankündigung)
5. [Checkliste](#checkliste)

## Warum aufwärmen

„Nach einer Cache-Leerung oder einer Bereitstellung bezahlt der erste Besucher für das Rendern“
([Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md)). Gerenderte Seiten sind schnell, sobald sie im
Cache liegen; die erste Anfrage jeder Seite nach einer Leerung ist die langsame. Auf einer Website des öffentlichen
Sektors folgt auf eine Leerung oft eine Lastspitze: eine Bereitstellung am Abend, dann öffnen alle die Website am
Morgen; oder eine Korrektur, die direkt vor einer Pressemitteilung veröffentlicht wird.

## Die Werkzeuge

| Werkzeug | Was es tut |
|---|---|
| `php bin/php/preload.php --siteaccess=site --allow-root-user` | ruft zuerst die Bereichsseiten ab und folgt dann den Links der Website; es läuft jeweils nur ein Vorladevorgang, jeder Lauf wird unter **Setup > Seiten vorladen** aufgeführt |
| `php bin/php/warm.php --allow-root-user` | wärmt jede veröffentlichte Seite auf; eignet sich für einen Cronjob alle paar Minuten |
| **Setup > Seiten vorladen** | dasselbe im Browser: Wählen Sie die Website, eine Seitenobergrenze (Standard 250) und eine Linktiefe (Standard 3) |
| `[DeploySettings] WarmUpURL[]` | Seiten, die `exp:velocity deploy` als letzten Schritt im Hintergrund abruft |

Verwenden Sie für `--siteaccess` den Namen Ihres eigenen öffentlichen Siteaccess. Einzelheiten:
[Caches vorladen](../../../../anleitungen/caches-vorwaermen.md) und [Ansicht „Seiten vorladen“](../../../../../eng-us/features/6.0/preload-sites-view.md).

## Aufwärmen nach einer Bereitstellung

`exp:velocity deploy` leert die Caches mit fertigen Seiten zuletzt, nachdem der neue Code läuft, und ruft dann die
Aufwärm-URLs ab, damit der erste Besucher die Caches bereits aufgebaut vorfindet ([Bereitstellen](../../../../anleitungen/bereitstellung.md)).
Tragen Sie Ihre meistbesuchten Seiten in die Liste ein:

```bash
./console exp:velocity config get DeploySettings WarmUpURL --allow-root-user
```

Führen Sie nach einer Wiederherstellung oder wenn Sie alles von Hand geleert haben einen Vorladevorgang aus:

```bash
php bin/php/ezcache.php --clear-all --allow-root-user
php bin/php/preload.php --siteaccess=site --allow-root-user
```

Um jede veröffentlichte Seite warm zu halten, eine Cron-Zeile wie die in der Anleitung:

```bash
php bin/php/warm.php --allow-root-user >> var/log/cache-warm.log 2>&1
```

Bei mehreren Webservern wärmen Sie jeden Knoten auf; ihre Caches vor PHP sind lokal.

## Aufwärmen vor einer Ankündigung

Wenn die Kommunikationsabteilung eine Veröffentlichung plant, die viele Besucher auf wenige Seiten bringen wird:

1. Veröffentlichen Sie die Seiten früh genug, damit die Caches ablaufen und sich neu füllen, oder leeren Sie nur das,
   was sich geändert hat.
2. Rufen Sie die angekündigten Seiten und ihre übergeordneten Seiten einmal ab, zum Beispiel mit der Ansicht „Seiten
   vorladen“, begrenzt auf eine geringe Tiefe.
3. Prüfen Sie, dass die Seiten aus dem Cache ausgeliefert werden (`curl -sI` zeigt unter Velocity `X-Cache`).
4. Vermeiden Sie Bereitstellungen und vollständige Cache-Leerungen in den Stunden vor der Ankündigung; planen Sie
   dafür eine Änderungssperre ein.

## Checkliste

- [ ] `WarmUpURL[]` führt Ihre meistbesuchten Seiten auf.
- [ ] Runbooks für Wiederherstellungen und vollständige Leerungen enthalten einen Vorladevorgang.
- [ ] Jeder Knoten wird aufgewärmt, wenn es mehrere gibt.
- [ ] Ankündigungen werden mit dem Betrieb abgestimmt, ohne vollständige Leerung kurz davor.

## Verwandte Seiten

- [Verfügbarkeit](README.md): Übersicht dieses Ordners
- [Velocity-Antwort-Cache und HTTP-Cache](velocity-antwortcache-und-http-cache.md)
- [Kapazitätsplanung](kapazitaetsplanung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
