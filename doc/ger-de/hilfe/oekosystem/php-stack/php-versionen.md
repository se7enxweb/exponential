---
title: PHP-Versionen im Ökosystem
audience: ecosystem, developer, admin, hosting
category: ecosystem
order: 31
summary: Welche PHP-Versionen das Ökosystem von Exponential unterstützt - PHP 8.0 bis 8.5 für Exponential 6 und Velocity, was die CI testet, PHP 7 nur in alten Releases, das Ziel 8.6 und darüber hinaus und die eigenen PHP-Anforderungen der Platform-Linie.
keywords: PHP-Versionen, PHP 8.0, PHP 8.5, PHP 8.6, PHP 7, RHEL 9, CI, Velocity, Platform-Linie, PHP-Anforderung in composer.json, PHP versions, platform line, composer.json php requirement
type: help
---
# PHP-Versionen im Ökosystem

## Inhalt

1. [Exponential 6](#exponential-6)
2. [Was die CI testet](#was-die-ci-testet)
3. [Die Platform-Linie](#die-platform-linie)
4. [Eine PHP-Version wählen](#eine-php-version-wählen)

## Exponential 6

| PHP | Exponential 6 | Quelle |
|---|---|---|
| 7.4 | Bis 6.0.7 und wieder ab 6.0.15 | `README.md` |
| 8.0 | Unterstützt, sodass das mitgelieferte PHP von Red Hat Enterprise Linux 9 es betreibt, ab 0.0.4.54 auch unter Velocity | `README.md`, [Unterstützung für PHP 8.0](../../../bc/6.0/php-8.0-unterstuetzung.md) |
| 8.1 bis 8.5 | Unterstützt, einschließlich Velocity | `README.md` |
| 8.6 und neuer | Von `composer.json` erlaubt (bis 8.8); das Projekt zielt auf "PHP 8.2, 8.3, 8.4, 8.5, 8.6 and beyond php9" | `composer.json`, `README.md` |

Quelltabelle: [PHP-Versionen](../../wer-ist-die-exponential-foundation.md#php-versionen).

## Was die CI testet

"The continuous integration runs the test suite on PHP 8.1 to 8.5 and checks that PHP 8.0 still boots, on every push
to `main` and every pull request", die CI lässt also die Testsuite auf PHP 8.1 bis 8.5 laufen und prüft bei jedem Push
auf `main` und jedem Pull-Request, ob PHP 8.0 noch startet ([PHP-Versionen](../../wer-ist-die-exponential-foundation.md#php-versionen);
[CI](../../../bc/6.0/ci.md)).

## Die Platform-Linie

Die Symfony-Distributionen haben eigene PHP-Bereiche, zum Beispiel Nexus "Symfony 7.4/5.4/3.4 PHP 8.0-8.5
Compatibility", die DXP "PHP 8.3 and later" und Platform Legacy 2.5 "on PHP 8.1 and 8.2"
([Die Produkte im Einzelnen](../../wer-ist-die-exponential-foundation.md#die-produkte-im-einzelnen),
[Das Ökosystem der Exponential-Plattform](../../../geschichte/oekosystem.md)). Prüfen Sie die `composer.json` der
Distribution.

## Eine PHP-Version wählen

"please use the latest version of PHP available on your OS", also die neueste PHP-Version, die Ihr Betriebssystem
bietet (`README.md`). 7x "stronly recommends you to upgrade and leverage the latest PHP 8.x security improvements",
empfiehlt also dringend, aufzurüsten und die neuesten Sicherheitsverbesserungen von PHP 8.x zu nutzen, statt bei PHP 7
zu bleiben (ebenda). Einzelheiten zum Hosting unter [PHP-Versionen und -Erweiterungen](../../hosting/grundlagen/php-versionen-und-erweiterungen.md)
und [PHP-Einstellungen für Exponential](../../hosting/grundlagen/php-einstellungen-fuer-exponential.md).

## Verwandte Seiten

- [Der PHP-Stack](README.md)
- [Anforderungen](../../../installation/02-voraussetzungen.md)
- [Kompatibilität und Abhängigkeiten](../pakete/kompatibilitaet-und-abhaengigkeiten.md)
- [Einwand: PHP und moderne Technik](../../fuersprecher/einwand-php-und-moderne-technik.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
