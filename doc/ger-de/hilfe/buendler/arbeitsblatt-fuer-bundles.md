---
title: Arbeitsblatt für Bundles
audience: bundler, partner
category: bundling
order: 500
summary: Ein Arbeitsblatt, das Sie einmal je Bundle ausfüllen und bei jedem Release prüfen - Identität, Basis und Versionen, Teile, Lizenzen, Installation, Datenbank und Server, Sicherheit, Updates, Support und Partnerschaft - jeweils mit der Seite, die die Antwort erklärt.
keywords: Arbeitsblatt, Vorlage, Bundle-Plan, Entscheidungsprotokoll, Versionen, Lizenzen, Installer, Datenbank, Webserver, Sicherheit, Updates, Support, Checkliste, worksheet, template, bundle plan, decision record, versions, licenses, installer, database, web server, security, updates, support, checklist
type: help
---
# Arbeitsblatt für Bundles

Diese Seite richtet sich an Bündler, die jede Entscheidung über ein Bundle an einem Ort haben möchten. Kopieren Sie die
Blöcke in Ihr Repository (zum Beispiel nach `docs/bundle.md`), füllen Sie sie aus und prüfen Sie sie bei jedem Release
mit der [Checkliste für die Bundle-Veröffentlichung](checkliste-fuer-die-bundle-veroeffentlichung.md).

## Inhalt

1. [Identität](#identität)
2. [Basis und Teile](#basis-und-teile)
3. [Lizenzen](#lizenzen)
4. [Installation](#installation)
5. [Laufzeitumgebung](#laufzeitumgebung)
6. [Sicherheit](#sicherheit)
7. [Das Leben nach dem Release](#das-leben-nach-dem-release)

## Identität

```text
Bundle name:              <name>                     (Naming and branding a bundle)
Kind:                     container / image / panel / package / kit / site package / extensions / appliance / demo
Recipients:               <who>
Bundle version:           <version>
Maker and contact:        <your company>, <address>
Partner level / profile:  <level, profile, or none>
```

[Arten von Bundles](arten-von-bundles.md), [Ein Bundle benennen und gestalten](ein-bundle-benennen-und-gestalten.md).

## Basis und Teile

```text
Base:                     Exponential 6 / Legacy <line> / Nexus <line>
Exponential version:      <tag or commit>
Lock file checksum:       <sha256 of composer.lock>
Extensions added:         <list with versions>
Extensions removed:       <list with reasons>
Site package(s):          <name version checksum>
Package server:           public / mirror <URL> / shipped files
```

[Die Basis wählen](die-basis-waehlen.md), [Versionen, Tags und Pinning](versionen-tags-und-pinning.md),
[Seitenpakete in einem Bundle](seitenpakete-in-einem-bundle.md).

## Lizenzen

```text
Bundle license (own parts):  <SPDX>
Notice file:                 <path>
Third-party list:            <path>, generated with composer licenses on <date>
Source offer:                <URL or written offer>
Counsel reviewed:            <date>
```

[GPL-Pflichten](gpl-pflichten-beim-weitergeben.md), [Komponenten Dritter](komponenten-dritter-und-ihre-lizenzen.md).

## Installation

```text
Installer:                exp:install / kickstarter / setup wizard
Options or template:      <path>
Install at:               first boot / by the recipient
Already-installed test:   grep -q '^\[DatabaseSettings\]' settings/override/site.ini.append.php
Password hand-over:       <how>
Offline capable:          yes / no
```

[Die drei Installer](die-drei-installer.md), [Erster Start und einmalige Installation](erster-start-und-einmalige-installation.md).

## Laufzeitumgebung

```text
PHP:                      <versions>, extensions checked with exp:velocity ext check
Database:                 <engine, version>
Web server / engine:      Velocity qbix / FrankenPHP / Apache / nginx + PHP-FPM
Site user / group:        <user> / <group>
Cron:                     <how>
Backups:                  <what, where>
```

[Datenbankwahl](datenbankwahl-fuer-bundles.md), [Webserver- und Engine-Wahl](webserver-und-engine-wahl.md),
[Dateirechte](dateirechte-und-besitz-von-var.md).

## Sicherheit

```text
File modes:               0770 / 0660
HTTPS:                    <how>
Admin access:             <URL path / host / port>, restrictions
Demo mode:                off (or: demo bundle, set for <processes>)
Secrets shipped:          none (checked on <date>)
```

[Sicherheitsvorgaben eines Bundles](sicherheitsvorgaben-eines-bundles.md), [Demo-Modus in Bundles](demomodus-in-bundles.md).

## Das Leben nach dem Release

```text
Update route:             <image / package / Git and Composer / ezupdate>
Database updates by:      <who>
Security release time:    <promise>
Support statement:        <path>
End of support:           <date or policy>
Upstream contributions:   <issues and pull requests sent>
```

[Updates und Upgrade-Pfade](updates-und-upgrade-pfade.md), [Grenzen des Supports](grenzen-des-supports.md),
[Korrekturen zurückgeben](bundle-korrekturen-zurueckgeben.md).

Die Feldnamen in den Blöcken bleiben englisch, damit sie zu den Werkzeugen und Befehlen passen, die sie nennen; füllen
Sie die Werte in der Sprache Ihres Teams aus.

## Verwandte Seiten

- [Checkliste für die Bundle-Veröffentlichung](checkliste-fuer-die-bundle-veroeffentlichung.md)
- [Bündler: hier beginnen](buendler-hier-beginnen.md)
- [Checkliste für die Partnerbewerbung](../partner/checkliste-fuer-die-bewerbung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
