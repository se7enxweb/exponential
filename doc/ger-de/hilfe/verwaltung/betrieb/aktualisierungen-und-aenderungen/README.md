---
title: Aktualisierungen und Änderungen
audience: government, admin, project-manager
category: public-sector
order: 10
summary: Übersicht der Seiten zum Aktualisieren und Ändern einer Exponential-Website des öffentlichen Sektors unter Änderungsmanagement - der Weg der Aktualisierung, unveränderliche Releases, Änderungsbeiräte, Staging, durch die Bereitstellung verwaltete Konfiguration, Versionshinweise, Erweiterungen, Aktualisierungen von PHP und Datenbank, Rücknahme, Upgrade-Prüfungen, Wartungsfenster und Sicherheitsupdates.
keywords: Aktualisierungen, Änderungsmanagement, Änderungsbeirat, Staging, Versionshinweise, Rücknahme, Wartungsfenster, Sicherheitsupdates, ManagedByDeployment, Upgrade-Prüfung, Erweiterungen, PHP-Aktualisierung, Übersicht, upgrades, change management, change advisory board, release notes, rollback, maintenance window, security updates, upgrade check, extensions, PHP upgrade, index
type: help
---
# Aktualisierungen und Änderungen

Dieser Ordner richtet sich an Verantwortliche für Dienste, Change-Manager und Administratoren, die eine öffentliche
Website ohne Überraschungen aktuell halten. Er verbindet den Aktualisierungsablauf von Exponential
([Aktualisieren](../../../../anleitungen/aktualisieren.md), [Kapitel 11](../../../../installation/11-aktualisieren.md)) mit dem
Änderungsmanagement, das öffentliche Stellen betreiben: Freigaben, Staging, Nachweise, Rücknahme und Kommunikation.

## Inhalt
1. [Seiten](#seiten)
2. [Lesereihenfolge](#lesereihenfolge)
3. [Verwandte Seiten](#verwandte-seiten)

## Seiten

| Seite | Beantwortet |
|---|---|
| [Überblick über Aktualisierungen für den öffentlichen Sektor](aktualisierungen-im-oeffentlichen-sektor-im-ueberblick.md) | Welcher Aktualisierungsweg gilt und die Schritte von der Datensicherung bis zur Prüfung. |
| [Unveränderliche Releases und Versionen](unveraenderliche-releases-und-versionen.md) | Warum sich eine Version nach dem Release nie ändert und wie Sie festlegen und prüfen, was läuft. |
| [Änderungsmanagement und Änderungsbeiräte](aenderungsmanagement-und-aenderungsbeiraete.md) | Welche Änderungen an den Beirat gehen, welche Nachweise die Plattform liefert, eine Vorlage für den Änderungsdatensatz. |
| [Staging und Freigabe](staging-und-freigabe.md) | Entwicklung, Test und Produktion; Varianten mit EXP_ENV; was zu testen ist; Abnahme. |
| [Durch die Bereitstellung verwaltete Konfiguration](verwaltet-durch-bereitstellung.md) | Die Live-Konfiguration sperren, damit Änderungen über die Versionsverwaltung kommen. |
| [Versionshinweise lesen](versionshinweise-lesen.md) | Wo die Hinweise stehen und wie Sie daraus eine Risikobewertung machen. |
| [Aktualisierungen von Erweiterungen](aktualisierung-von-erweiterungen.md) | Veraltete Erweiterungen finden, mit einem Probelauf aktualisieren, Prüfung von Erweiterungen Dritter. |
| [Aktualisierungen von PHP und Datenbank](php-und-datenbank-aktualisierungen.md) | Unterstützte Versionen, die Reihenfolge der Aktualisierungen, das Log der veralteten Funktionen. |
| [Rücknahme planen](planung-der-ruecknahme.md) | Der Weg zurück für jede Art von Änderung; der Entscheidungspunkt. |
| [Upgrade-Prüfung vorher und nachher](upgrade-pruefung-vorher-und-nachher.md) | Datei- und Datenbankkonsistenz als Nachweis. |
| [Wartungsfenster](wartungsfenster.md) | Ausfallzeiten planen, ankündigen, durchführen und abschließen. |
| [Sicherheitsupdates und Offenlegung](sicherheitsaktualisierungen-und-offenlegung.md) | Unterstützte Versionen, wie Korrekturen ankommen, eine Schwachstelle melden. |

## Lesereihenfolge

1. [Überblick über Aktualisierungen](aktualisierungen-im-oeffentlichen-sektor-im-ueberblick.md) und [Unveränderliche Releases](unveraenderliche-releases-und-versionen.md).
2. [Änderungsmanagement und Änderungsbeiräte](aenderungsmanagement-und-aenderungsbeiraete.md), [Staging und Freigabe](staging-und-freigabe.md)
   und [Durch die Bereitstellung verwaltete Konfiguration](verwaltet-durch-bereitstellung.md): der Prozess.
3. Für jede Änderung: [Versionshinweise lesen](versionshinweise-lesen.md), [Upgrade-Prüfung vorher und nachher](upgrade-pruefung-vorher-und-nachher.md),
   [Rücknahme planen](planung-der-ruecknahme.md), [Wartungsfenster](wartungsfenster.md).
4. Plattform und Erweiterungen: [Aktualisierungen von Erweiterungen](aktualisierung-von-erweiterungen.md), [Aktualisierungen von PHP und Datenbank](php-und-datenbank-aktualisierungen.md).
5. [Sicherheitsupdates und Offenlegung](sicherheitsaktualisierungen-und-offenlegung.md): Nehmen Sie das in Ihren Sicherheitsplan auf.

Eine Ebene höher: [Exponential im öffentlichen Sektor betreiben](../README.md). Die Einstiegsseite des Bereichs ist
[Behörden: hier beginnen](../../verwaltungen-hier-beginnen.md).

## Verwandte Seiten
- [Betriebsaufgaben](../../aufgaben/betrieb/README.md)
- [Kunden-Websites aktualisieren (Hosting)](../../../hosting/betrieb/kunden-websites-aktualisieren.md)
- [Leitfaden zum Bereitstellen](../../../../anleitungen/bereitstellung.md)
- [Standard für Versionsverwaltung und Releases](../../../../standards/versionsverwaltung-und-releases.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
