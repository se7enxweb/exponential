---
title: Versionshinweise lesen
audience: government, admin, project-manager
category: public-sector
order: 70
summary: Wo die Versionshinweise, Changelogs und Seiten zu Verhaltensänderungen von Exponential stehen, wie Sie sie zwischen Ihrer Version und der Zielversion lesen und wie Sie daraus eine Risikobewertung und eine Testliste für einen Änderungsbeirat machen.
keywords: Versionshinweise, Changelog, Verhaltensänderungen, Hinweise zur Aktualisierung, Risikobewertung, was sich geändert hat, Added, Updated, Removed, Renamed, Changelog der Erweiterungen, Änderungsbeirat, release notes, changelog, behavior changes, upgrade notes, risk assessment, what changed, extensions changelog, change board
type: help
---
# Versionshinweise lesen

Diese Seite richtet sich an Administratoren und Projektleitungen, die eine Aktualisierung vor einem Änderungsbeirat
bewerten müssen. Sie zeigt, wo die Hinweise stehen und wie Sie sie effizient lesen.

## Inhalt
1. [Wo die Hinweise stehen](#wo-die-hinweise-stehen)
2. [Wie Sie sie lesen](#wie-sie-sie-lesen)
3. [Aus Hinweisen eine Bewertung machen](#aus-hinweisen-eine-bewertung-machen)
4. [Verwandte Seiten](#verwandte-seiten)

## Wo die Hinweise stehen

| Hinweise | Wo | Umfasst |
|---|---|---|
| Changelog pro Version | `doc/changelogs/6.0/` ([6.0.15](../../../../../changelogs/6.0/6.0.15.md), [6.0.14](../../../../../changelogs/6.0/6.0.14.md) ...) | jede Änderung der Version; 6.0.15 steht während der Entwicklung auf „Not released yet“ |
| Verhaltensänderungen nach Zeitraum | `doc/eng-us/bc/6.0/`, zum Beispiel [Juli und August 2026](../../../../bc/6.0/verhaltensaenderungen-2026-07-08.md), [16. bis 30. September 2026](../../../../bc/6.0/verhaltensaenderungen-2026-09b.md), [1. bis 2. Oktober 2026](../../../../bc/6.0/verhaltensaenderungen-2026-10.md) | was brechen kann und was zu tun ist, mit einer Checkliste |
| Die Tabelle im Leitfaden zum Aktualisieren | [Aktualisieren, C3](../../../../anleitungen/aktualisieren.md#c3-die-checkliste-jedes-durchlaufenen-zeitraums-lesen) | welche Hinweise Sie für Ihre Ausgangsversion lesen sollten |
| Erweiterungen | [Changelogs der Erweiterungen](../../../../../changelogs/extensions/README.md) | die Releases jeder Erweiterung |
| Sicherheit | [Sicherheitsstandard](../../../../../eng-us/standards/security.md), der Changelog | sicherheitsrelevante Änderungen und bekannte Lücken |

Commit-Nachrichten und Changelog-Einträge verwenden die Präfixe **Added**, **Updated**, **Removed** und **Renamed**
([Versionsverwaltung und Releases](../../../../../eng-us/standards/source-control-and-releases.md)), was das Überfliegen
erleichtert.

## Wie Sie sie lesen

1. Bestimmen Sie Ihre Version und die Zielversion.
2. Lesen Sie die Seite zu Verhaltensänderungen **jedes Zeitraums, den Sie durchlaufen**, beginnend mit ihrer kurzen
   Checkliste und der Spalte „was zu tun ist“; die Tabelle im Leitfaden zum Aktualisieren listet sie nach
   Ausgangsversion auf.
3. Durchsuchen Sie die Changelogs zuerst nach **Removed** und **Renamed**: Diese bringen Dinge zum Brechen. Danach
   **Updated** auf geänderte Standardwerte (Sicherheitsheader, Cookies, hochgeladene Dateien, Passwortprüfungen).
4. Lesen Sie für jede Erweiterung, die Sie betreiben, deren Changelog zwischen Ihrer und der neuen Version.
5. Notieren Sie jede Einstellung, die Sie laut den Hinweisen setzen oder prüfen sollen.

## Aus Hinweisen eine Bewertung machen

| Spalte | Beispiel |
|---|---|
| Änderung | „Konten ohne eine Zeile in `ezuser_setting` können sich nicht anmelden“ |
| Betrifft uns? | mit einer Abfrage auf dem Staging prüfen; ja/nein |
| Maßnahme | die Zeilen vor der Aktualisierung anlegen |
| Test | auf dem Staging mit einem migrierten Konto anmelden |
| Risiko bei Versäumnis | einige Beschäftigte können sich nicht anmelden |

Die Spalte Test wird zu Ihrer Testliste für das Staging ([Staging und Freigabe](staging-und-freigabe.md)); die Spalte
Risiko fließt in den Änderungsdatensatz ein ([Änderungsmanagement und Änderungsbeiräte](aenderungsmanagement-und-aenderungsbeiraete.md)).

## Verwandte Seiten
- [Überblick über Aktualisierungen für den öffentlichen Sektor](aktualisierungen-im-oeffentlichen-sektor-im-ueberblick.md)
- [Aktualisierungen von Erweiterungen](aktualisierung-von-erweiterungen.md)
- [Unveränderliche Releases und Versionen](unveraenderliche-releases-und-versionen.md)
- [Leitfaden zum Aktualisieren](../../../../anleitungen/aktualisieren.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
