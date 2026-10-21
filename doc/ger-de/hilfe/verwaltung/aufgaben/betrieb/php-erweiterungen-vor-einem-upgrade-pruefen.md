---
title: PHP-Erweiterungen vor einem Upgrade prüfen
audience: government, admin, hosting
category: public-sector
order: 350
summary: Schritt für Schritt - vor einem Upgrade von PHP oder Exponential mit exp:velocity ext check auflisten, welche PHP-Erweiterungen die Installation braucht und welche auf der Zielmaschine fehlen, mit den Systeminformationen vergleichen und das Log der Deprecations leeren.
keywords: PHP-Erweiterungen, exp:velocity ext check, PHP-Upgrade, fehlende Erweiterungen, fileinfo, intl, opcache, Systeminformationen, deprecated.log, Vorbereitung, PHP extensions, PHP upgrade, missing extensions, system information, preparation
type: help
---
# PHP-Erweiterungen vor einem Upgrade prüfen

Diese Aufgabe ist für Administratoren, die ein PHP-Upgrade, einen Umzug auf einen neuen Server oder ein Upgrade von
Exponential vorbereiten. Am Ende wissen Sie, welche PHP-Erweiterungen der Zielmaschine fehlen, und haben den Befehl, um
sie zu installieren ([Bereitstellung, Abschnitt 2](../../../../anleitungen/bereitstellung.md#2-prüfen-sie-die-maschine-bevor-sie-beginnen)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Shell-Zugang zur Zielmaschine (zuerst Staging).

## Schritte

1. PHP-Version und Erweiterungen:

   ```bash
   php -v | head -1
   ./console exp:velocity ext check --allow-root-user
   ```

   Der zweite Befehl listet jede PHP-Erweiterung, die die Installation braucht, markiert, was fehlt, und gibt den
   Installationsbefehl für das System aus (zum Beispiel `sudo dnf install -y php-<name>`). Fehlende optionale
   Erweiterungen halten die Site nicht an.
2. Das PHP des Webservers kann sich von dem der Kommandozeile unterscheiden: Öffnen Sie **Setup > Systeminformationen**
   über den Webserver; die Zustandsprüfungen *PHP extensions* (Fehler, wenn eine erforderliche fehlt) und
   *Recommended extensions* (curl, fileinfo, intl, xsl, OPcache, APCu, gd oder imagick) decken es ab.
3. Code, den ein neueres PHP ablehnen wird:

   ```bash
   sort var/log/deprecated.log | uniq -c | sort -rn | head
   ```

   Geben Sie die Liste an Ihre Entwickler; Ihre eigenen Erweiterungen sind die übliche Quelle.
4. Installieren Sie, was fehlt, und starten Sie dann PHP-FPM oder Velocity neu, damit die Erweiterungen geladen werden.

## Prüfen, ob es funktioniert hat

- `./console exp:velocity ext check --allow-root-user` zeigt für die Funktionen, die Sie verwenden, nichts Fehlendes.
- **Setup > Systeminformationen** hat keine roten PHP-Prüfungen.
- `php -m | grep fileinfo` (fileinfo ist für die Prüfungen beim Upload wichtig).

## Verwandte Seiten
- [Upgrades von PHP und Datenbank](../../../../../eng-us/help/government/operations/upgrades-and-change/php-and-database-upgrades.md)
- [PHP-Versionen und Erweiterungen (Hosting)](../../../hosting/grundlagen/php-versionen-und-erweiterungen.md)
- [PHP-Upgrade (Runbook für Hoster)](../../../hosting/runbooks/php-upgrade.md)
- [Anforderungen](../../../../installation/02-voraussetzungen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
