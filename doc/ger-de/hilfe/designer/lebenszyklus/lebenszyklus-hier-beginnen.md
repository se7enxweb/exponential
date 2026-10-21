---
title: "Der Lebenszyklus der Wartung: hier beginnen"
audience: designer, developer
category: theme-lifecycle
order: 10
featured: true
summary: Die Übersicht, wie Sie ein Theme nach seinem ersten Release am Leben halten - Releases von Exponential verfolgen, Upgrade-Tests, Prüfungen der Barrierefreiheit, der CSP und der Leistung pro Release, Sicherheitskorrekturen, Abkündigung, Lebensende, Übergabe, Forken, Umbenennen und Zurückgeben.
keywords: Wartung, Lebenszyklus, Kompatibilität, Upgrade-Tests, Prüfungen, Sicherheit, Abkündigung, Lebensende, Übergabe, Fork, zurückgeben, maintenance, life cycle, compatibility, upgrade testing, audits, security, deprecation, end of life, handover, fork, contribute back
type: help
---
# Der Lebenszyklus der Wartung: hier beginnen

Dieser Bereich ist für Designer, die ein Theme veröffentlicht haben und es nun tragen. Ein Theme lebt, solange sich
Exponential, PHP und Browser weiterbewegen; jedes davon ändert jedes Jahr etwas. Die Wartung hält die Websites Ihrer
Nutzer am Laufen.

## Der Zyklus auf einen Blick

1. **Beobachten**, was sich ändert: Releases von Exponential, PHP-Versionen, Bibliotheken, Browser.
2. **Testen** Sie Ihr Theme gegen jede Änderung in einer Upgrade-Testumgebung.
3. **Prüfen** Sie das Release: Barrierefreiheit, Content Security Policy, Aussehen in hell und dunkel und mit Zoom,
   Leistung.
4. **Beheben und veröffentlichen** Sie eine neue Version, Sicherheitskorrekturen zuerst.
5. **Mitteilen**: Changelog, Release Notes, Abkündigungen und eines Tages das Lebensende oder eine Übergabe.

## Eine Lesereihenfolge

1. [Der Lebenszyklus eines Themes](der-lebenszyklus-eines-themes.md), [Eine Support-Richtlinie für Ihr Theme](support-richtlinie-fuer-ihr-theme.md).
2. Kompatibilität: [Releases von Exponential verfolgen](exponential-releases-verfolgen.md),
   [Ein Changelog als Designer lesen](ein-changelog-als-designer-lesen.md), [Eine Kompatibilitätsmatrix](kompatibilitaetsmatrix.md),
   [PHP-Versionskompatibilität](php-versionskompatibilitaet.md).
3. Testen: [Eine Upgrade-Testumgebung](upgrade-testumgebung.md), [Upgrade-Test Schritt für Schritt](upgrade-test-schritt-fuer-schritt.md).
4. Prüfungen: [Barrierefreiheit](barrierefreiheitspruefung-pro-release.md), [CSP](csp-pruefung-pro-release.md),
   [Visuelle Regression](visuelle-regressionspruefungen.md), [Leistung](leistungspruefung-pro-release.md).
5. Sicherheit: [Sicherheitskorrekturen in Themes](sicherheitskorrekturen-in-themes.md), [Sicherheitsmeldungen bearbeiten](sicherheitsmeldungen-bearbeiten.md),
   [Ein Sicherheitsrelease veröffentlichen](ein-sicherheitsrelease-veroeffentlichen.md).
6. Wandel und Ende: [Eine Abkündigungsrichtlinie](abkuendigungsrichtlinie.md), [Inkompatible Änderungen](inkompatible-aenderungen.md),
   [Lebensende](lebensende.md), [Ein Theme übergeben](uebergabe-an-einen-neuen-betreuer.md).
7. Forks und Community: [Ein Theme forken](ein-theme-forken.md), [Einen Fork fair umbenennen](einen-fork-umbenennen.md),
   [An Upstream zurückgeben](an-upstream-zurueckgeben.md).

## Alle Seiten dieses Bereichs

| Seite | Was sie beantwortet |
|---|---|
| [Der Lebenszyklus eines Themes](der-lebenszyklus-eines-themes.md) | Die Phasen vom ersten Release bis zum Archiv |
| [Eine Support-Richtlinie für Ihr Theme](support-richtlinie-fuer-ihr-theme.md) | Welche Versionen Sie pflegen |
| [Releases von Exponential verfolgen](exponential-releases-verfolgen.md) | Wo Änderungen angekündigt werden |
| [Ein Changelog als Designer lesen](ein-changelog-als-designer-lesen.md) | Was für ein Theme zählt |
| [Eine Kompatibilitätsmatrix](kompatibilitaetsmatrix.md) | Theme-Versionen gegen Exponential und PHP |
| [PHP-Versionskompatibilität](php-versionskompatibilitaet.md) | PHP 8.0 und später |
| [Kompatibilität angeben](kompatibilitaet-angeben.md) | Wo Nutzer sie lesen |
| [Eine Upgrade-Testumgebung](upgrade-testumgebung.md) | Ein sicherer Ort zum Testen |
| [Upgrade-Test Schritt für Schritt](upgrade-test-schritt-fuer-schritt.md) | Die Routine |
| [Ihre Overrides mit den Standard-Templates vergleichen](overrides-mit-dem-standard-vergleichen.md) | Abweichungen finden |
| [JavaScript-Bibliotheken aktualisieren](javascript-bibliotheken-aktualisieren.md) | jQuery 4, kein YUI |
| [Inline-Skripte und -Styles entfernen](inline-skripte-und-styles-entfernen.md) | Bereit für die CSP werden |
| [Eine Prüfung der Barrierefreiheit bei jedem Release](barrierefreiheitspruefung-pro-release.md) | Die Routine |
| [Eine CSP-Prüfung bei jedem Release](csp-pruefung-pro-release.md) | Die Routine |
| [Visuelle Regressionsprüfungen](visuelle-regressionspruefungen.md) | Hell, dunkel, Zoom |
| [Eine Leistungsprüfung bei jedem Release](leistungspruefung-pro-release.md) | Die Routine |
| [Sicherheitskorrekturen in Themes](sicherheitskorrekturen-in-themes.md) | Typische Probleme und Korrekturen |
| [Sicherheitsmeldungen bearbeiten](sicherheitsmeldungen-bearbeiten.md) | Von der Meldung zur Korrektur |
| [Ein Sicherheitsrelease veröffentlichen](ein-sicherheitsrelease-veroeffentlichen.md) | Beheben, veröffentlichen, beschreiben |
| [Abhängigkeiten aktuell halten](abhaengigkeiten-aktualisieren.md) | Bibliotheken und Erweiterungen |
| [Eine Abkündigungsrichtlinie](abkuendigungsrichtlinie.md) | Wie Sie Dinge in den Ruhestand schicken |
| [Eine Einstellung oder ein Template abkündigen](eine-einstellung-oder-ein-template-abkuendigen.md) | Schritt für Schritt |
| [Inkompatible Änderungen und die nächste Major-Version](inkompatible-aenderungen.md) | Wenn Nutzer handeln müssen |
| [Lebensende](lebensende.md) | Die Pflege gut beenden |
| [Das Lebensende ankündigen](das-lebensende-ankuendigen.md) | Was Sie sagen |
| [Ein Repository archivieren](ein-repository-archivieren.md) | Nur lesbar, weiter installierbar |
| [Ein Theme übergeben](uebergabe-an-einen-neuen-betreuer.md) | Ein neuer Betreuer |
| [Das Repository und das Packagist-Paket übertragen](repository-und-packagist-paket-uebertragen.md) | Eigentum übertragen |
| [Ein verwaistes Theme übernehmen](ein-verwaistes-theme-uebernehmen.md) | Übernehmen |
| [Ein Theme forken](ein-theme-forken.md) | Wann und wie |
| [Einen Fork fair umbenennen](einen-fork-umbenennen.md) | Namen, Hinweise, Nennung |
| [Danksagungen und Namensnennung](danksagungen-und-namensnennung.md) | Wen Sie nennen |
| [An Upstream zurückgeben](an-upstream-zurueckgeben.md) | Gemeinsames Wachstum |
| [Pull-Requests an fremde Themes](pull-requests-an-fremde-themes.md) | Gute Pull-Requests |
| [Einen Fork synchron halten](einen-fork-synchron-halten.md) | Änderungen im Original |
| [Ein Wartungskalender](wartungskalender.md) | Der Jahresrhythmus |
| [Checkliste: ein Wartungszyklus](checkliste-ein-wartungszyklus.md) | Die Routine als Liste |

## Verwandte Seiten

- [Release- und Upgrade-Planung](../../fuehrung/release-und-upgrade-planung.md)
- [Ein Theme veröffentlichen: hier beginnen](../veroeffentlichung/ein-theme-veroeffentlichen-hier-beginnen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
