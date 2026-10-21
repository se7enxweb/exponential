---
title: Aktualisierungen von Erweiterungen
audience: government, admin, developer
category: public-sector
order: 80
summary: Die Erweiterungen einer Exponential-Website des öffentlichen Sektors aktuell halten - herausfinden, was läuft und was veraltet ist, mit einem Probelauf über ezupdate oder Composer aktualisieren, Erweiterungen Dritter und die Prüfungen nach einer Aktualisierung.
keywords: Erweiterungen, Aktualisierungen von Erweiterungen, ezupdate, Updates und Pakete, composer outdated, Probelauf, Erweiterungen Dritter, Versionen von Erweiterungen, ezinfo.php, extension.xml, checkclasses, Lieferkette, extensions, extension updates, Updates and packages, dry run, third-party extensions, extension versions, supply chain
type: help
---
# Aktualisierungen von Erweiterungen

Diese Seite richtet sich an Administratoren und Entwicklerinnen und Entwickler, die die Erweiterungen einer
öffentlichen Website pflegen. Erweiterungen sind Composer-Pakete und Verzeichnisse unter `extension/`; jede trägt ihre
Version in `ezinfo.php` und `extension.xml`. Die Werkzeuge sind **Setup > Extensions**
([Die Seite Extensions](../../../../anleitungen/extensions-seite.md)) und **Setup > Updates und Pakete**, bereitgestellt von
der Erweiterung ezupdate ([ezupdate](../../../../../eng-us/features/6.0/extensions/ezupdate.md)).

## Inhalt
1. [Wissen, was Sie betreiben](#wissen-was-sie-betreiben)
2. [Herausfinden, was veraltet ist](#herausfinden-was-veraltet-ist)
3. [Unter Änderungskontrolle aktualisieren](#unter-änderungskontrolle-aktualisieren)
4. [Erweiterungen Dritter](#erweiterungen-dritter)
5. [Prüfungen nach einer Aktualisierung](#prüfungen-nach-einer-aktualisierung)
6. [Verwandte Seiten](#verwandte-seiten)

## Wissen, was Sie betreiben

- **Setup > Extensions** listet jede Erweiterung auf der Festplatte mit ihrer Ladereihenfolge, Version und Aktivierung auf.
- **Setup > Updates und Pakete**, **Installierte Pakete** (`/update/installed`) vergleicht `composer.json`,
  `composer.lock`, `vendor/composer/installed.json` und die Verzeichnisse der Erweiterungen und nennt, was nicht
  übereinstimmt; Downloads als JSON und CSV liefern Ihnen ein Softwareinventar.
- Halten Sie die Liste in Ihrer Konfigurationsdokumentation mit Versionen und dem Grund, warum jede Erweiterung aktiv ist.

## Herausfinden, was veraltet ist

**Nach Updates suchen** in der Übersicht von ezupdate führt `composer outdated` aus und ändert nichts. In der Shell:

```bash
php extension/ezupdate/bin/php/ezupdate.php --help
```

Lesen Sie den Changelog jeder Erweiterung zwischen Ihrer und der neuen Version
([Changelogs der Erweiterungen](../../../../../changelogs/extensions/README.md)).

## Unter Änderungskontrolle aktualisieren

1. Stellen Sie einen Änderungsantrag ([Änderungsmanagement und Änderungsbeiräte](aenderungsmanagement-und-aenderungsbeiraete.md));
   kleinere Aktualisierungen von Erweiterungen können in Ihrem Prozess eine Standardänderung sein, neue Erweiterungen
   eine normale Änderung.
2. Auf dem Staging: **Vorschau (Probelauf)** in ezupdate zeigt, was Composer tun würde; danach **Updates
   installieren**, was eine Bestätigung der Datensicherung verlangt und im Hintergrund läuft, mit der Ausgabe von
   Composer auf der Seite des Laufs.
3. Oder mit Composer in Ihrer Bereitstellungspipeline, wobei die neue `composer.lock` committet wird.
4. Stellen Sie dieselbe `composer.lock` in der Produktion bereit. Mit [ManagedByDeployment](verwaltet-durch-bereitstellung.md)
   in der Produktion sind Aktualisieren und Installieren über den Browser dort absichtlich gesperrt.

## Erweiterungen Dritter

Erweiterungen von außerhalb des Projekts (und solche, die externe Dienste aufrufen, etwa Captcha- oder Kartendienste)
brauchen dieselbe Prüfung wie jede Software, die Sie beschaffen: Lizenz, Betreuer, letztes Release, welche Daten Ihre
Website verlassen. Der Datenschutzstandard hält fest, dass Erweiterungen Dritter standardmäßig ausgeschaltet sind
([Datenschutzstandard](../../../../../eng-us/standards/privacy.md)). Führen Sie eine kurze Bewertung pro Erweiterung.

## Prüfungen nach einer Aktualisierung

```bash
php bin/php/ezpgenerateautoloads.php -e
./console exp:checkclasses --allow-root-user
php bin/php/ezcache.php --clear-all --allow-root-user
./console exp:velocity deploy --dry-run --allow-root-user
```

Danach **Setup > Upgrade-Prüfung**: Erweiterungen mit eigenem Manifest werden ebenfalls geprüft
([Upgrade-Prüfung, Manifeste von Erweiterungen](../../../../anleitungen/upgrade-pruefung.md#manifeste-von-extensions));
**Überprüfe Datenbankkonsistenz** meldet Tabellen, die eine aktualisierte Erweiterung erwartet. Sehen Sie sich nach
einem Tag **Setup > Logdateien**, **Fehlerübersicht** an.

## Verwandte Seiten
- [Versionshinweise lesen](versionshinweise-lesen.md)
- [Automatisierte Updates mit ezupdate (Hosting)](../../../hosting/betrieb/automatische-updates-mit-ezupdate.md)
- [Ladereihenfolge der Erweiterungen](../../../../../eng-us/features/6.0/extension-loading-order.md)
- [Leitfaden zu Erweiterungen](../../../../anleitungen/extensions.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
