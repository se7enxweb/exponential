---
title: Den statischen Cache einschalten
audience: government, admin, hosting
category: public-sector
order: 290
summary: Schritt für Schritt - statische Kopien öffentlicher Seiten erzeugen, die der Webserver ohne Start von PHP sendet, das Ergebnis prüfen, StaticCache einschalten und den Cache aktuell halten, für Sites, die bei hoher öffentlicher Nachfrage schnell bleiben müssen.
keywords: statischer Cache, makestaticcache, StaticCache, cache.php static, öffentliche Seiten, Leistung, hohe Nachfrage, Webserver, var/site/static, static cache, public pages, performance, high demand, web server
type: help
---
# Den statischen Cache einschalten

Diese Aufgabe ist für Administratoren öffentlicher Sites, die plötzliche Nachfrage bewältigen müssen (eine
Ankündigung, ein Notfall). Der statische Cache speichert öffentliche Seiten als einfache Dateien, die der Webserver
sendet, ohne Exponential zu starten. In einer neuen Installation ist er ausgeschaltet
([Eine Site betreiben, Abschnitt 4](../../../../anleitungen/betrieb-einer-website.md#4-den-statischen-cache-einschalten)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Fallstricke](#fallstricke)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Nur Siteaccesses, die keine Anmeldung brauchen, werden angeboten, weil ihre Seiten für alle gleich sind.
- Die Regeln des Webservers Ihrer Installation, die die statischen Dateien ausliefern, sind aktiv (Apache mit der
  mitgelieferten `.htaccess`); unter Velocity ist der Antwort-Cache die übliche Alternative
  ([Antwort-Cache von Velocity und HTTP-Cache](../../../../../eng-us/help/government/operations/availability/velocity-response-cache-and-http-cache.md)).
- Eine genehmigte Änderung für eine Änderung an Einstellungen.

## Schritte

1. Sehen Sie nach, was es tun würde:

   ```bash
   php bin/php/cache.php static status --allow-root-user
   ```

2. Erzeugen Sie Seiten einer Site, zuerst begrenzt, damit Sie sehen, dass es funktioniert:

   ```bash
   php bin/php/makestaticcache.php --site=site --max-pages=500 --max-depth=6 --allow-root-user
   ```

   Lassen Sie `--site` für jeden öffentlichen Siteaccess weg. Im Browser: **Setup > Cache-Verwaltung**, Abschnitt
   *Statischer Inhalts-Cache*, mit einer Schaltfläche **Anhalten**.
3. Schalten Sie ihn in `settings/override/site.ini.append.php` ein:

   ```ini
   [ContentSettings]
   StaticCache=enabled
   ```

4. `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`, dann neu laden oder ausrollen.

## Prüfen, ob es funktioniert hat

- `php bin/php/cache.php static status --allow-root-user` zählt die Dateien je Site in `var/site/static`.
- Veröffentlichen Sie eine Änderung und prüfen Sie, ob die Seite sich aktualisiert (das Veröffentlichen leert ihre
  statischen Seiten).

## Fallstricke

- Seiten, die etwas Persönliches zeigen (den Namen eines angemeldeten Benutzers, ein Formular mit Token), dürfen nicht
  statisch sein.
- Entfernen Sie die Dateien mit `php bin/php/cache.php static clear --allow-root-user`, wenn Sie ihn wieder ausschalten.

## Verwandte Seiten
- [Statischer Cache](../../../../../eng-us/help/government/operations/availability/static-cache.md)
- [Generator für den statischen Cache](../../../../../eng-us/features/6.0/static-cache-generator.md)
- [Statischer Cache (Hosting)](../../../hosting/leistung/statischer-cache.md)
- [DDoS und Fluten von Sondierungen](../../../../../eng-us/help/government/operations/incidents/ddos-and-probe-floods.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
