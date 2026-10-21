---
title: Caches nach einer Änderung an Einstellungen leeren
audience: government, admin, hosting
category: public-sector
order: 70
summary: Schritt für Schritt - eine geänderte INI-Einstellung wirksam machen - den INI-Cache mit dem richtigen Tag leeren, PHP-FPM neu laden oder Velocity neu ausrollen, dann die Seiten-Caches leeren und den wirksamen Wert mit exp:ini prüfen.
keywords: Änderung an Einstellungen, INI-Cache, clear-tag=ini, exp:ini get, exp:ini where, PHP-FPM neu laden, Velocity deploy, Reihenfolge der Caches, Einstellungen nicht übernommen, settings change, INI cache, PHP-FPM reload, cache order, settings not applied
type: help
---
# Caches nach einer Änderung an Einstellungen leeren

Diese Aufgabe ist für Administratoren nach einer Änderung an einer Datei `.ini` oder `.ini.append.php`. Am Ende ist der
neue Wert überall wirksam. Einstellungen werden im Cache gehalten, und lang laufende PHP-Prozesse behalten, was sie
gelesen haben, also kommt es auf die Reihenfolge an
([Eine Site betreiben, Abschnitt 2](../../../../anleitungen/betrieb-einer-website.md#2-den-richtigen-cache-leeren)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Fallstricke](#fallstricke)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Änderung ist in der Versionsverwaltung und ausgerollt (oder, auf einer Site ohne ManagedByDeployment, in der
  Datei).
- Shell-Zugang.

## Schritte

1. Leeren Sie die INI-Caches (das Tag leert jeden INI-Cache, einschließlich des globalen):

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ```

2. Lassen Sie die Webprozesse sie neu lesen. Mit Velocity erledigt ein Befehl jeden Schritt in der richtigen
   Reihenfolge:

   ```bash
   ./console exp:velocity deploy --dry-run --allow-root-user
   ./console exp:velocity deploy --allow-root-user
   ```

   Ohne Velocity: Laden Sie den PHP-FPM-Dienst neu, der die Site ausliefert (`systemctl reload <service>`; auf Servern
   mit mehreren PHP-Versionen ist es nicht immer `php-fpm`).
3. Wenn die Einstellung ändert, was Seiten zeigen, leeren Sie den Inhalts-Cache und die vorgeschalteten Caches,
   **nach** dem Neuladen:

   ```bash
   php bin/php/ezcache.php --clear-tag=content --allow-root-user
   ./console exp:velocity cache clear --allow-root-user
   ```

## Prüfen, ob es funktioniert hat

```bash
./console exp:ini get 'site.ini/<Block>/<Setting>' --allow-root-user
./console exp:ini where site.ini/<Block>/<Setting> --allow-root-user
```

`where` zeigt, aus welcher Datei der Wert kommt; ein Wert aus `settings/override` gewinnt gegenüber einem Siteaccess.
Prüfen Sie dann das Verhalten im Browser.

## Fallstricke

- `--clear-id=ini` verfehlt den globalen INI-Cache; verwenden Sie `--clear-tag=ini`.
- Die Worker von Velocity behalten Klassen und Einstellungen von ihrem Start bis zu einem Neustart oder Deployment.
- Eine Seite, die zwischen dem Leeren des Caches und dem Neuladen dargestellt wurde, liegt mit der alten Einstellung im
  Cache: Leeren Sie die Seiten-Caches zuletzt.

Änderungsnachweis: Einstellung, alter und neuer Wert, Datum; das Audit-Protokoll zeichnet Einstellungen auf, die über
`exp:ini` und die Debug-Leiste geschrieben werden.

## Verwandte Seiten
- [Caches nach einer Änderung an Templates leeren](caches-nach-einer-aenderung-an-templates-leeren.md)
- [Eine PHP-Änderung ausrollen](eine-php-aenderung-ausrollen.md)
- [Der INI-Befehl](../../../../../eng-us/features/6.0/exp-ini-command.md)
- [Anleitung zu den Caches](../../../../anleitungen/caches.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
