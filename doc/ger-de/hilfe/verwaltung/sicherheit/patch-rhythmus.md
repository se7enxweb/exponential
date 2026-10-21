---
title: Patch-Rhythmus
audience: government, admin, project-manager, hosting
category: public-sector
order: 270
summary: Für öffentliche Stellen, die ihre Systeme gepatcht halten müssen - wie Exponential Versionen veröffentlicht und unterstützt, was zu beobachten ist (Kernel, Erweiterungen, Composer-Abhängigkeiten, PHP, Datenbank, Engine), eine praktische Aktualisierungsroutine mit Testkopie, composer audit und Dateikonsistenzprüfung und die aufzubewahrenden Nachweise.
keywords: Patches, Updates, Aktualisierungen, Upgrades, Release-Rhythmus, unterstützte Versionen, Changelog, Testkopie, Dateikonsistenz, Schwachstellenmanagement, öffentlicher Sektor, patching, updates, upgrades, release cadence, supported versions, SECURITY.md, composer outdated, composer audit, Packagist, changelog, test copy, exp:velocity deploy, file consistency, vulnerability management, public sector
type: help
---
# Patch-Rhythmus

Diese Seite richtet sich an Administratorinnen und Administratoren, Hosting-Teams und Projektverantwortliche, die
dafür zuständig sind, eine öffentliche Website aktuell zu halten. Das Schwachstellenmanagement verlangt meist, dass
bekannte Schwachstellen innerhalb festgelegter Fristen gepatcht werden. Exponential und seine Erweiterungen sind
Composer-Pakete mit unveränderlichen Releases; die folgende Routine folgt
[13.15 Auf dem aktuellen Stand bleiben](../../../installation/13-sicherheitshaertung.md#1315-auf-dem-neuesten-stand-bleiben).

## Inhalt

1. [Was zu beobachten ist](#was-zu-beobachten-ist)
2. [Wie Exponential veröffentlicht](#wie-exponential-veröffentlicht)
3. [Eine Routine](#eine-routine)
4. [Aufzubewahrende Nachweise](#aufzubewahrende-nachweise)
5. [Verwandte Seiten](#verwandte-seiten)

## Was zu beobachten ist

| Komponente | Wie Sie sehen, was neu ist |
|---|---|
| Exponential und seine Erweiterungen | `composer outdated`; die Changelogs (`doc/changelogs/`); GitHub-Releases |
| Composer-Abhängigkeiten | `composer audit` listet bekannte Schwachstellen in den festgeschriebenen Abhängigkeiten |
| PHP, Datenbankserver, Betriebssystem | die Sicherheitsmeldungen Ihrer Distribution oder Ihres Herstellers |
| Die Engine von Velocity und FrankenPHP | Releases der Engine; Version und Prüfsummen von FrankenPHP sind in `velocity.ini [FrankenPHPSettings]` festgeschrieben |
| request-shield (falls verwendet) | seine Releases |

Die CI des Projekts führt keinen Schwachstellenscan der Abhängigkeiten aus
([Sicherheitsstandards: bekannte Lücken](../../../standards/sicherheit.md#bekannte-lücken)), daher ist `composer audit` Ihre
Aufgabe.

## Wie Exponential veröffentlicht

- 6.0 ist die gepflegte Linie; Korrekturen gehen nach `main` und erscheinen mit dem nächsten Release 6.0.x
  ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)).
- Release-Tags werden nie geändert; eine Korrektur ist immer eine neue Version.
- Jedes Release hat ein Changelog und, wo nötig, Hinweise zum Upgrade ([Aktualisieren](../../../anleitungen/aktualisieren.md)).

## Eine Routine

| Wann | Tun |
|---|---|
| wöchentlich | `composer outdated` und `composer audit` auf der Lock-Datei der Produktion; Sicherheitsmeldungen lesen |
| monatlich (oder sofort bei Sicherheitskorrekturen) | in einer Kopie aktualisieren, testen, dann die Produktion |
| nach jedem Update | Dateikonsistenzprüfung, Smoke-Test, Audit-Prüfung |

Die Schritte des Updates aus der Installationsanleitung:

1. In einer Kopie der Website: `composer update <package>`, die Datenbank-Updates ausführen, die das Release nennt, dann
   `./console exp:velocity deploy --allow-root-user` (oder Autoloads, Cache leeren und PHP-FPM von Hand neu laden).
2. Testen Sie die wichtigsten redaktionellen Aufgaben und die öffentlichen Seiten; führen Sie Ihre Stichproben zu
   Barrierefreiheit und Sicherheit durch.
3. Wiederholen Sie das auf der Produktion in einem Wartungsfenster ([Wartungsmodus](../../../anleitungen/wartungsmodus.md)).
4. **Setup > Upgrade-Prüfung**: Datei- und Datenbankkonsistenz
   ([Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)).
5. `./console exp:audit verify --allow-root-user`.

Halten Sie `composer.lock` in der Versionsverwaltung, damit die Produktion genau das installiert, was Sie getestet
haben.

## Aufzubewahrende Nachweise

- Datum, Versionen vorher und nachher, wer aktualisiert hat;
- Ausgabe von `composer audit` vorher und nachher;
- Ergebnis der Dateikonsistenzprüfung;
- bei Sicherheitskorrekturen: die Zeit vom Release bis zur Produktion, gemessen am Ziel Ihrer Richtlinie.

## Verwandte Seiten

- [13.15 Auf dem aktuellen Stand bleiben](../../../installation/13-sicherheitshaertung.md#1315-auf-dem-neuesten-stand-bleiben)
- [Aktualisieren](../../../anleitungen/aktualisieren.md)
- [Updates und Upgrades für Website-Ersteller](../../ersteller/website-ersteller/updates-und-aktualisierungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
