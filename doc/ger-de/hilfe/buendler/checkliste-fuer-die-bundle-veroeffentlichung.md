---
title: Checkliste für die Bundle-Veröffentlichung
audience: bundler, partner
category: bundling
order: 440
summary: Die Checkliste, die Sie abarbeiten, bevor ein Bundle-Release hinausgeht - Versionen, gewählt nach dem bereits Veröffentlichten, festgelegte Teile, Lizenzen und Hinweise, Sicherheitsvorgaben, Tests, Integrität, Dokumentation, das Quellcode-Angebot, die Benennung und die Veröffentlichung eines Releases, das nie mehr geändert wird.
keywords: Checkliste, Veröffentlichung, Bundle-Release, Version, Tag, unveränderlich, Lock-Datei, Lizenzen, Hinweise, Quellcode-Angebot, Tests, Integrität, Dokumentation, Benennung, release checklist, bundle release, immutable, lock file, licenses, notices, source offer, tests, integrity, documentation, naming, publish
type: help
---
# Checkliste für die Bundle-Veröffentlichung

Diese Seite richtet sich an die Person, die bei einem Bundle auf "Veröffentlichen" drückt. Arbeiten Sie sie für jedes
Release ab; ein veröffentlichtes Release lässt sich nicht zurücknehmen, nur durch das nächste ablösen.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Inhalt des Bundles](#inhalt-des-bundles)
3. [Lizenzen und Hinweise](#lizenzen-und-hinweise)
4. [Sicherheit](#sicherheit)
5. [Tests](#tests)
6. [Dokumentation](#dokumentation)
7. [Veröffentlichen](#veröffentlichen)

## Bevor Sie beginnen

- [ ] Die nächste Versionsnummer ist nach dem gewählt, was **bereits veröffentlicht** ist (Ihre Tags und Releases,
      mit Versionssortierung aufgelistet: `git tag -l 'v*' --sort=version:refname`), nicht aus dem Gedächtnis.
- [ ] Die Exponential-Version ist aus den veröffentlichten Tags gewählt, ebenfalls mit Versionssortierung
      ([Versionen, Tags und Pinning](versionen-tags-und-pinning.md#das-neueste-release-richtig-finden)).
- [ ] Die Release-Hinweise jedes Exponential-Releases seit Ihrem letzten Bundle sind gelesen, einschließlich der
      Upgrade-Hinweise.

## Inhalt des Bundles

- [ ] Kernel auf einen Tag (oder einen festgehaltenen Commit) festgelegt; `composer.lock` eingecheckt; gebaut mit
      `composer install --no-dev`.
- [ ] Erweiterungen bewusst hinzugefügt oder entfernt und festgehalten
      ([Erweiterungen hinzufügen und entfernen](erweiterungen-hinzufuegen-und-entfernen.md)).
- [ ] Die `.ezpkg`-Dateien der Seitenpakete sind die getesteten, mit Prüfsummen
      ([Seitenpakete in einem Bundle](seitenpakete-in-einem-bundle.md)).
- [ ] Laufzeitumgebung festgelegt: Digest des Basis-Images, PHP-Version, Velocity-Version.
- [ ] Autoloads neu erzeugt; keine Build-Caches ausgeliefert ([Caches und Autoloads](caches-und-autoloads-nach-dem-bauen.md)).
- [ ] Ihre Version steht in dem Commit, den Sie taggen, in Ihren Dateien.

## Lizenzen und Hinweise

- [ ] `LICENSE`, `LICENSE.md`, `COPYRIGHT.md` aus der ausgelieferten Version enthalten
      ([Lizenztexte und Hinweise](mitzuliefernde-lizenztexte-und-hinweise.md)).
- [ ] Liste der Komponenten Dritter erzeugt und geprüft ([Komponenten Dritter](komponenten-dritter-und-ihre-lizenzen.md)).
- [ ] Ihre eigenen Teile haben übereinstimmende Lizenzfelder ([Lizenzfelder](lizenzfelder-in-ihren-eigenen-paketen.md)).
- [ ] Der zugehörige Quellcode ist veröffentlicht oder angeboten, und die Release-Hinweise sagen, wo
      ([GPL-Pflichten](gpl-pflichten-beim-weitergeben.md)).
- [ ] Die Dokumentation behält ihre GFDL-Hinweise ([Mitzuliefernde Dokumentation](mitzuliefernde-dokumentation.md)).

## Sicherheit

- [ ] Kein `settings/override/`, `settings/siteaccess/`, keine `kickstart.ini`, Protokolle oder
      `initial-admin-password` aus dem Build.
- [ ] Kein festes Administratorpasswort; Übergabe dokumentiert
      ([Standardzugangsdaten](standardzugangsdaten-und-erste-anmeldung.md)).
- [ ] Kein `EXP_DEMO_MODE` außerhalb von Demo-Bundles ([Demo-Modus in Bundles](demomodus-in-bundles.md)).
- [ ] Gehärtete Dateimodi und Besitzrechte ([Dateirechte](dateirechte-und-besitz-von-var.md)).
- [ ] Die Go-live-Punkte, die ein Bundle klären kann, sind geklärt ([Sicherheitsvorgaben](sicherheitsvorgaben-eines-bundles.md)).

## Tests

- [ ] Testmatrix ausgeführt und protokolliert ([Ein Bundle testen](ein-bundle-testen.md)).
- [ ] Upgrade vom vorigen Bundle-Release getestet.
- [ ] Ergebnis von `php bin/php/checkmanifest.php --all` festgehalten ([Integritätsprüfungen](integritaetspruefungen.md)).
- [ ] Arbeitsbaum sauber; das Artefakt entspricht den getaggten Quellen.

## Dokumentation

- [ ] Erste Schritte, Adressen, Passwortübergabe.
- [ ] Was enthalten ist, mit Versionen.
- [ ] Wie aktualisiert wird und welche Datenbankschritte nötig sind
      ([Updates und Upgrade-Pfade](updates-und-upgrade-pfade.md)).
- [ ] Grenzen des Supports ([Grenzen des Supports](grenzen-des-supports.md)).
- [ ] Änderungsprotokoll mit Added, Updated, Removed, Renamed.

## Veröffentlichen

- [ ] Name und Beschreibung folgen den Markenregeln ([Benennen und Gestalten](ein-bundle-benennen-und-gestalten.md)).
- [ ] Prüfsummen (`SHA256SUMS`) mit den Artefakten veröffentlicht.
- [ ] Der Tag wird **erst** gepusht, nachdem jede Prüfung oben bestanden ist. Verschieben, löschen oder erstellen Sie
      ihn danach nie neu; ein Fehler wird mit dem nächsten Release behoben, mit einem Hinweis, was falsch war.
- [ ] Das Release wird Ihren Empfängern angekündigt und, wenn Sie Partner sind, beim Partnerteam erfasst
      ([Ihre Partneraktivität erfassen](../partner/partneraktivitaet-erfassen.md)).

## Verwandte Seiten

- [Arbeitsblatt für Bundles](arbeitsblatt-fuer-bundles.md)
- [Ein Bundle testen](ein-bundle-testen.md)
- [Versionen, Tags und Pinning](versionen-tags-und-pinning.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
