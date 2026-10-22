---
title: Checklisten für Bundles
audience: bundler, hosting, partner
category: bundling
order: 510
summary: Die kurzen Checklisten der Seiten für Bündler an einem Ort - vor dem Bauen, der Build, das Artefakt, der erste Start, die installierte Website, Lizenzen, das Release, nach dem Release - jeder Punkt mit einem Link auf die Seite, die ihn erklärt.
keywords: Checkliste, Checklisten, Bundle, Build, Artefakt, erster Start, installierte Website, Lizenzen, Release, nach dem Release, Prüfung, checklist, checklists, bundle, build, artifact, first boot, installed site, licenses, release, after release, review
type: help
---
# Checklisten für Bundles

Diese Seite richtet sich an Bündler, die die Prüfungen ohne die Erklärungen möchten. Jeder Punkt verlinkt die Seite,
die ihn erklärt. Für ein vollständiges Release verwenden Sie die
[Checkliste für die Bundle-Veröffentlichung](checkliste-fuer-die-bundle-veroeffentlichung.md).

## Inhalt

1. [Vor dem Bauen](#vor-dem-bauen)
2. [Der Build](#der-build)
3. [Das Artefakt](#das-artefakt)
4. [Erster Start](#erster-start)
5. [Die installierte Website](#die-installierte-website)
6. [Lizenzen](#lizenzen)
7. [Nach dem Release](#nach-dem-release)

## Vor dem Bauen

- [ ] Basis gewählt und festgehalten ([Die Basis wählen](die-basis-waehlen.md)).
- [ ] Exponential-Version mit Versionssortierung aus den veröffentlichten Tags gewählt
      ([Versionen, Tags und Pinning](versionen-tags-und-pinning.md)).
- [ ] Datenbank und Server gewählt ([Datenbankwahl](datenbankwahl-fuer-bundles.md),
      [Webserver- und Engine-Wahl](webserver-und-engine-wahl.md)).
- [ ] Seitenpaket gewählt und seine Dateien gesichert ([Seitenpakete in einem Bundle](seitenpakete-in-einem-bundle.md)).
- [ ] Update-Weg und Supportbedingungen entworfen ([Updates und Upgrade-Pfade](updates-und-upgrade-pfade.md),
      [Grenzen des Supports](grenzen-des-supports.md)).

## Der Build

- [ ] `composer install --no-dev --no-interaction` aus der eingecheckten Lock-Datei
      ([Lock-Dateien](lock-dateien-und-reproduzierbare-builds.md)).
- [ ] Das Installer-Plugin in der `composer.json` erlaubt
      ([Bundles auf Composer-Basis](bundles-auf-composer-basis.md#das-installer-plugin)).
- [ ] `php bin/php/ezpgenerateautoloads.php -e` ausgeführt; `exp:checkclasses` ohne Befund
      ([Caches und Autoloads](caches-und-autoloads-nach-dem-bauen.md)).
- [ ] `exp:velocity ext check` läuft auf dem ausgelieferten PHP fehlerfrei durch
      ([Den Code beschaffen](den-code-fuer-ein-bundle-beschaffen.md#das-ergebnis-prüfen)).
- [ ] Ergebnis von `php bin/php/checkmanifest.php --all` festgehalten ([Integritätsprüfungen](integritaetspruefungen.md)).
- [ ] Build-Schritte laufen als Benutzer der Website
      ([Dateirechte](dateirechte-und-besitz-von-var.md#bauen-ohne-dateien-im-besitz-von-root)).

## Das Artefakt

- [ ] Kein `settings/override/` oder `settings/siteaccess/` aus dem Build
      ([Erster Start und einmalige Installation](erster-start-und-einmalige-installation.md#was-nicht-im-bundle-sein-darf)).
- [ ] Keine `kickstart.ini`, kein `var/log/`, `var/cache/`, `initial-admin-password`, `config.env.php`.
- [ ] Kein `EXP_DEMO_MODE`, außer es ist ein Demo-Bundle ([Demo-Modus in Bundles](demomodus-in-bundles.md)).
- [ ] Hinweise, Liste der Komponenten Dritter und Quellcode-Angebot enthalten
      ([Lizenztexte und Hinweise](mitzuliefernde-lizenztexte-und-hinweise.md)).
- [ ] `SHA256SUMS` der Artefakte.

## Erster Start

- [ ] Wartet auf die Datenbank; überspringt, wenn bereits installiert
      ([Erster Start und einmalige Installation](erster-start-und-einmalige-installation.md#ein-ablauf-für-den-ersten-start)).
- [ ] Geheimnisse von der Plattform, das Datenbankpasswort in `EXP_INSTALL_DB_PASSWORD`
      ([Unbeaufsichtigte Installation](unbeaufsichtigte-installation-mit-exp-install.md#geheimnisse)).
- [ ] Installiert als Benutzer der Website mit URL und E-Mail des Empfängers.
- [ ] Prüft den Exit-Status; bewahrt bei einem Fehler `var/log/setup.log` auf.
- [ ] Übergibt das Passwort einmal; löscht `var/log/initial-admin-password`
      ([Standardzugangsdaten](standardzugangsdaten-und-erste-anmeldung.md)).
- [ ] Startet den Server und Cron.

## Die installierte Website

- [ ] Website und Administration antworten unter den dokumentierten Adressen.
- [ ] `/.git/config`, `/settings/site.ini`, `/composer.json` werden nicht ausgeliefert
      ([Sicherheitsvorgaben](sicherheitsvorgaben-eines-bundles.md)).
- [ ] Gehärtete Dateimodi; nichts unter `var/` gehört root.
- [ ] Kein Hinweisbalken **Demo-Modus**.
- [ ] **Info > Über** und **Info > Copyright** korrekt.

## Lizenzen

- [ ] `LICENSE`, `LICENSE.md`, `COPYRIGHT.md` unverändert ([GPL-Pflichten](gpl-pflichten-beim-weitergeben.md)).
- [ ] `composer licenses --no-dev` geprüft ([Komponenten Dritter](komponenten-dritter-und-ihre-lizenzen.md)).
- [ ] Die Lizenzfelder der eigenen Teile stimmen überein ([Lizenzfelder](lizenzfelder-in-ihren-eigenen-paketen.md)).
- [ ] Die GFDL-Hinweise der Dokumentation beibehalten ([Mitzuliefernde Dokumentation](mitzuliefernde-dokumentation.md)).

## Nach dem Release

- [ ] Der Tag wird nie verschoben; Fehler behebt das nächste Release
      ([Versionen, Tags und Pinning](versionen-tags-und-pinning.md#ein-veröffentlichtes-release-ändert-sich-nie)).
- [ ] Exponential-Releases beobachtet; Sicherheitsreleases zügig gebündelt
      ([Updates und Upgrade-Pfade](updates-und-upgrade-pfade.md#sicherheitsreleases)).
- [ ] Fehler beim Projekt gemeldet, Korrekturen beigetragen ([Korrekturen zurückgeben](bundle-korrekturen-zurueckgeben.md)).

## Verwandte Seiten

- [Checkliste für die Bundle-Veröffentlichung](checkliste-fuer-die-bundle-veroeffentlichung.md)
- [Arbeitsblatt für Bundles](arbeitsblatt-fuer-bundles.md)
- [Checklisten für Hoster](../hosting/checklisten/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
