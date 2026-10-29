---
title: "Thema: aktuelles PHP"
audience: press
category: press-media
order: 72
summary: Ein Themenbeitrag darüber, wie eine alte PHP-Codebasis auf aktuellem PHP gehalten wird - PHP 8.0 bis 8.5 unterstützt, PHP 7.4 ab 6.0.15 wieder unterstützt, die CI-Matrix, die Ziele über PHP 9 hinaus und die unterwegs gepatchten Forks von Bibliotheken - mit Daten, Quellen und den Aussagen, die Sie vermeiden sollten.
keywords: Thema, PHP, PHP 8, PHP 8.4, PHP 8.5, PHP 7.4, Kompatibilität, Modernisierung, CI, Zeta Components, Upgrades, technische Schulden, story, compatibility, modernization, upgrades, technical debt
type: help
---
# Thema: aktuelles PHP

Diese Seite ist für technische Journalisten. Eine Codebasis, die in der Ära von PHP 4 begann, auf PHP 8.5 zu halten, ist
eine Geschichte über beständige, sichtbare Pflege.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Was neu ist, und seit wann](#was-neu-ist-und-seit-wann)
3. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
4. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
5. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)

## Das Thema in einem Absatz

Exponential 6 unterstützt PHP 7.4 bis 8.5; PHP 8.0 wird beibehalten, damit das mitgelieferte PHP von Red Hat Enterprise
Linux 9 es ausführen kann, und PHP 7.4 wird ab 6.0.15 wieder für Websites unterstützt, die noch nicht umziehen können. „Current releases are primarily aimed at
easing the requirements to support current versions of the PHP language like PHP 8.2, 8.3, 8.4, 8.5, 8.6 and beyond
php9“ (README) – aktuelle Releases sollen vor allem die Unterstützung aktueller PHP-Versionen bis über PHP 9 hinaus
erleichtern. Die kontinuierliche Integration führt die Testsuite auf PHP 8.1 bis 8.5 aus und prüft bei jedem Push und
Pull Request, dass PHP 8.0 noch startet ([PHP-Versionen](../wer-ist-die-exponential-foundation.md#php-versionen)).

## Was neu ist, und seit wann

| Was | Wann | Quelle |
|---|---|---|
| PHP 8 im Setup-Assistenten | Dezember 2023 | [Dezember 2023](../../geschichte/2023/2023-12.md) |
| PHP 8.4 dazu, PHP 7 raus (6.0.7 das letzte für 7.4) | April 2025 | [April 2025](../../geschichte/2025/2025-04.md) |
| PHP 8.5 unterstützt | Dezember 2025 | [Dezember 2025](../../geschichte/2025/2025-12.md) |

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| PHP 8.0 bis 8.5 unterstützt, Velocity eingeschlossen (ab seinem Release 0.0.4.54) | [PHP-Versionen](../wer-ist-die-exponential-foundation.md#php-versionen) |
| `composer.json` erlaubt bis zu PHP 8.8 | ebenda |
| CI auf PHP 8.1 bis 8.5, Startprüfung mit PHP 8.0 | [CI](../../bc/6.0/ci.md) |
| 7x pflegt gepatchte Forks der Zeta Components und hat Sicherheits-Pull-Requests an das Ursprungsprojekt geschickt | [Der PHP-Stack, auf dem es steht](../wer-ist-die-exponential-foundation.md#der-php-stack-auf-dem-es-steht-und-dem-es-etwas-zurückgibt) |

## Was Sie nicht behaupten sollten

| Sagen Sie nicht | Sagen Sie stattdessen |
|---|---|
| „Unterstützt PHP 9“ | Das Projekt zielt auf künftige Versionen; dokumentiert ist die Unterstützung bis 8.5 |
| „Für PHP 8 neu geschrieben“ | Der bestehende Code wurde Release für Release aktualisiert |
| „Läuft auf PHP 7“ (aktuelle Releases) | Nur bis 6.0.7 |

## Blickwinkel und Ideen für Überschriften

- „Von Code aus der Ära von PHP 4 zu PHP 8.5.“
- „Alter Code, neues PHP: die Pflege, die niemand sieht.“

## Verwandte Seiten

- [Thema: eine lebendig gehaltene Codebasis](thema-eine-lebendig-gehaltene-codebasis.md)
- [Einwand: „PHP und moderne Technik“](../fuersprecher/einwand-php-und-moderne-technik.md)
- [Funktionsthemen: Übersicht](themenbeitraege-uebersicht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
