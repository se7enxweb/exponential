---
title: Die Caches nach einem Deployment vorwärmen
audience: government, admin, hosting
category: public-sector
order: 90
summary: Schritt für Schritt - nach einem Deployment oder dem Leeren der Caches die wichtigen Seiten selbst anfordern (preload.php, warm.php oder die Ansicht Seiten vorladen), damit die ersten Bürger nicht darauf warten, dass Seiten dargestellt werden.
keywords: Caches vorwärmen, vorladen, warm.php, preload.php, Seiten vorladen, nach dem Deployment, erster Besucher, Leistung, WarmUpURL, cache warming, preload, Preload Sites, after deploy, first visitor, performance
type: help
---
# Die Caches nach einem Deployment vorwärmen

Diese Aufgabe ist für Administratoren nach einem Deployment oder dem vollständigen Leeren der Caches. Am Ende sind die
wichtigsten Seiten dargestellt und im Cache, bevor Besucher danach fragen. Referenz:
[Caches vorwärmen](../../../../anleitungen/caches-vorwaermen.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Das Deployment ist abgeschlossen, einschließlich des Neuladens von PHP-FPM oder des Neustarts von Velocity; ein
  Vorwärmen davor legt alte Seiten in den Cache.
- Shell-Zugang oder `setup/preload` für die Ansicht **Seiten vorladen**.

## Schritte

Wählen Sie eine Möglichkeit:

1. **Zuerst die Seiten der Bereiche, dann die Links** (ein Lauf nach dem anderen; jeder Lauf wird unter
   **Setup > Seiten vorladen** gelistet):

   ```bash
   php bin/php/preload.php --siteaccess=site --allow-root-user
   ```

2. **Jede veröffentlichte Seite**, geeignet für einen Cron-Eintrag:

   ```bash
   php bin/php/warm.php --allow-root-user >> var/log/cache-warm.log 2>&1
   ```

3. **Im Browser**: **Setup > Seiten vorladen** (`/setup/preload`), wählen Sie die Site, eine Seitengrenze
   (Voreinstellung 250) und eine Linktiefe (Voreinstellung 3), und beobachten Sie, wie die Seiten abgerufen werden.

Mit Velocity fordert `exp:velocity deploy` in seinem letzten Schritt bereits die Adressen aus
`[DeploySettings] WarmUpURL[]` an; fügen Sie dort Ihre meistbesuchten Seiten hinzu.

## Prüfen, ob es funktioniert hat

```bash
curl -s -o /dev/null -w '%{time_total}s\n' https://www.example.gov/
```

Führen Sie es einige Male aus: Nach dem Vorwärmen ist die Zeit schon bei der ersten Anfrage die Zeit aus dem Cache.
Verwenden Sie für `--siteaccess` den Namen des öffentlichen Siteaccess Ihrer Site.

## Verwandte Seiten
- [Eine PHP-Änderung ausrollen](eine-php-aenderung-ausrollen.md)
- [Caches nach Änderungen vorwärmen](../../../../../eng-us/help/government/operations/availability/cache-warming-after-changes.md)
- [Die Ansicht Seiten vorladen](../../../../../eng-us/features/6.0/preload-sites-view.md)
- [Caches leeren und vorwärmen (Hosting)](../../../hosting/leistung/caches-leeren-und-vorwaermen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
