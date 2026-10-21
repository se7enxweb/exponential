---
title: Bereitschaft für Aktualisierungen
audience: government, admin, hosting, project-manager
category: public-sector
order: 160
summary: Eine Checkliste für eine öffentliche Stelle vor, während und nach einer Aktualisierung ihrer Exponential-Website - Changelog, Upgrade-Prüfung, Staging, Datensicherung, Wartungsfenster, die sieben Update-Schritte, Prüfung und Kommunikation.
keywords: Aktualisierung, Upgrade, Update, Bereitschaft, Changelog, Upgrade-Prüfung, Staging, Datensicherung, Wartungsfenster, Git Manager, Updates and packages, Prüfung, öffentlicher Sektor, upgrade, update, readiness, upgrade check, backup, maintenance window, verification, public sector
type: help
---
# Bereitschaft für Aktualisierungen

Diese Checkliste richtet sich an die Website-Administration und die Projektleitung einer öffentlichen Stelle, die eine
Aktualisierung innerhalb der Versionslinie 6.0.x plant. Die Schritte stammen aus
[Mit Git aktualisieren](../../mit-git-aktualisieren.md), [Aktualisieren](../../../anleitungen/aktualisieren.md) und
[Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md).

## Inhalt

1. [Vorher](#vorher)
2. [Währenddessen](#währenddessen)
3. [Danach](#danach)
4. [Aktualisierungen von älteren Versionslinien](#aktualisierungen-von-älteren-versionslinien)

## Vorher

- [ ] Aktuelle Version aus **Setup > Systeminformationen** notiert ([Systeminformationen](../../../anleitungen/systeminformationen.md)).
- [ ] Das Changelog jeder Version, die Sie überspringen, gelesen, sowie die verlinkten Hinweise zur Aktualisierung ([Changelog 6.0.15](../../../../changelogs/6.0/6.0.15.md), [Aktualisieren, Teil C](../../../anleitungen/aktualisieren.md#teil-c-von-einer-beliebigen-60x-auf-den-aktuellen-stand)).
- [ ] **Setup > Upgrade-Prüfung** ausgeführt: direkt geänderte Dateien gefunden und in Erweiterungen oder Overrides verschoben ([Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)).
- [ ] Ihre eigenen Erweiterungen gegen die Änderungen geprüft ([Aktualisieren, C4](../../../anleitungen/aktualisieren.md#c4-ihre-eigenen-extensions-prüfen)).
- [ ] Die Aktualisierung auf einer Staging-Kopie mit einer schriftlichen Testliste geprobt.
- [ ] Ein Wartungsfenster vereinbart und angekündigt.
- [ ] Eine frische Datensicherung von Datenbank und Dateien ([Bereitschaft für Datensicherungen](bereitschaft-fuer-datensicherungen.md)).

## Währenddessen

- [ ] Wartungsmodus eingeschaltet, Administratoren zugelassen ([Wartungsmodus](../../../anleitungen/wartungsmodus.md)).
- [ ] Den sieben Schritten gefolgt, über die Kommandozeile oder die Administration ([Mit Git aktualisieren](../../mit-git-aktualisieren.md#die-sieben-schritte)):
  - [ ] den Kernel aktualisieren (Git oder **Git Manager**);
  - [ ] die Erweiterungen aktualisieren (Composer oder **Setup > Updates and packages**);
  - [ ] die Datenbank prüfen und aktualisieren;
  - [ ] die Caches leeren und den Code neu laden (zum Beispiel `php bin/php/ezcache.php --clear-all --allow-root-user`, dann die Engine neu laden) ([Caches](../../../anleitungen/caches.md));
- [ ] Als Administrator geprüft, bevor Besucher zurückkehren.
- [ ] Wartungsmodus ausgeschaltet.

## Danach

- [ ] **Setup > Systeminformationen** zeigt die neue Version und keine offenen Zustandsprüfungen.
- [ ] Wichtige Abläufe getestet: Startseite, Suche, ein Formular, Anmeldung, Veröffentlichen eines Artikels, ein Download.
- [ ] **Setup > Logdateien** auf neue Fehler geprüft ([Protokollanzeige](../../../../eng-us/features/6.0/log-viewer.md)).
- [ ] Stichprobe zur Barrierefreiheit an den Haupttemplates.
- [ ] Der Redaktion mitgeteilt, was sich in der Administration geändert hat; Schulungsmaterial aktualisiert.
- [ ] Die Aktualisierung festgehalten: von, auf, Datum, wer, Probleme.

## Aktualisierungen von älteren Versionslinien

Eine Website auf 5.x, 4.x oder den Symfony-Plattformen wird nicht unterstützt und braucht ein Migrationsprojekt, keine
routinemäßige Aktualisierung ([Unterstützte Versionen](../../../../eng-us/help/government/procurement/lifecycle/supported-versions.md)):

- [ ] Lesen Sie [14. Migration von der Versionslinie 4.x](../../../installation/14-migration-von-4x.md), [15. Migration vom Legacy-Stack 5.x](../../../installation/15-migration-von-5x-legacy.md) oder [16. Migration von eZ Platform und Ibexa](../../../installation/16-migration-von-ez-platform-und-ibexa.md).
- [ ] Verwenden Sie die [Checkliste zur Migration](../../../installation/migration/checkliste.md).
- [ ] Budgetieren Sie es als Projekt ([Aktualisierungen im Haushalt planen](../../../../eng-us/help/government/procurement/lifecycle/planning-upgrades-in-the-budget.md)).

## Verwandte Seiten

- [Checklisten für Verwaltungen](README.md)
- [Kundenwebsites aktualisieren (für Hoster)](../../hosting/betrieb/kunden-websites-aktualisieren.md)
- [Updates und Aktualisierungen (für Websiteersteller)](../../../../eng-us/help/creators/website-creators/updates-and-upgrades.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
