---
title: Glossar für Bündler
audience: bundler, partner, hosting
category: bundling
order: 530
summary: Die Begriffe der Seiten für Bündler an einem Ort - Bundle, Basis, Distribution, Release-Tag, Lock-Datei, Seitenpaket, Paketserver, Installer, Kickstarter, exp:install, CreateSites, Demo-Modus, Manifest, Velocity, Engine, OEM und mehr - jeweils mit der Seite, die ihn erklärt.
keywords: Glossar, Begriffe, Bundle, Distribution, Tag, Lock-Datei, Seitenpaket, Paketserver, Demo-Modus, glossary, terms, lock file, site package, ezpkg, package server, kickstarter, exp:install, CreateSites, demo mode, manifest, Velocity, OEM, GPL, GFDL
type: help
---
# Glossar für Bündler

Diese Seite richtet sich an alle, die beim Lesen der Seiten für Bündler auf ein unbekanntes Wort stoßen. Die Begriffe
stehen in alphabetischer Reihenfolge.

## Inhalt

1. [A bis F](#a-bis-f)
2. [G bis O](#g-bis-o)
3. [P bis Z](#p-bis-z)

## A bis F

| Begriff | Bedeutung | Seite |
|---|---|---|
| Appliance | Hardware oder eine versiegelte virtuelle Maschine mit Exponential darin | [Appliances](appliances.md) |
| Autoload-Arrays | erzeugte Klassenzuordnungen in `autoload/` und `var/autoload/` | [Caches und Autoloads](caches-und-autoloads-nach-dem-bauen.md) |
| Basis | worauf ein Bundle aufbaut: Exponential 6, Legacy oder Nexus | [Die Basis wählen](die-basis-waehlen.md) |
| Bundle | Exponential, kombiniert mit anderen Dingen und an jemanden außerhalb Ihrer Organisation übergeben | [Was ein Bundle ist](was-ein-bundle-ist.md) |
| CreateSites | der Installationsschritt, der die Datenbank, die Pakete und die Einstellungen schreibt | [Die drei Installer](die-drei-installer.md) |
| DatabaseAction | `remove`, `ignore` oder `skip`: was der Installer mit einer bestehenden Datenbank macht | [Der Kickstarter für Bundles](der-kickstarter-fuer-bundles.md#databaseaction) |
| Demo-Modus | `EXP_DEMO_MODE=1`: Administratorpasswörter lassen sich nicht ändern; nur für öffentliche Demos | [Demo-Modus in Bundles](demomodus-in-bundles.md) |
| Distribution (Produkt) | Exponential Platform Legacy oder Nexus, die Exponential 6 enthalten | [Platform-Distributionen](platform-distributionen-als-basis.md) |
| `.ezpkg` | eine Paketdatei: ein gzip-komprimiertes tar-Archiv mit `package.xml` | [Seitenpakete in einem Bundle](seitenpakete-in-einem-bundle.md) |
| `exp:install` | der Installer mit einem Befehl (`bin/php/install.php`) | [Unbeaufsichtigte Installation](unbeaufsichtigte-installation-mit-exp-install.md) |
| FrankenPHP | eine Engine von Velocity: Caddy mit eingebautem PHP | [Webserver- und Engine-Wahl](webserver-und-engine-wahl.md) |

## G bis O

| Begriff | Bedeutung | Seite |
|---|---|---|
| GFDL | GNU Free Documentation License 1.3, die Lizenz der Dokumentation | [Mitzuliefernde Dokumentation](mitzuliefernde-dokumentation.md) |
| GPL | GNU General Public License, Version 2 oder später, die Lizenz des Codes | [GPL-Pflichten](gpl-pflichten-beim-weitergeben.md) |
| `initial-admin-password` | `var/log/initial-admin-password`, das erzeugte Passwort, Modus `0600` | [Standardzugangsdaten](standardzugangsdaten-und-erste-anmeldung.md) |
| Kickstarter | der Installer, der `kickstart.ini` liest (`exp:kickstarter`, `bin/php/kickstarter.php`) | [Der Kickstarter für Bundles](der-kickstarter-fuer-bundles.md) |
| Lock-Datei | `composer.lock`: die genauen Versionen eines Builds; Ihre, nicht die des Projekts | [Lock-Dateien](lock-dateien-und-reproduzierbare-builds.md) |
| Manifest | `share/filelist.md5`, Prüfsummen aller ausgelieferten Dateien | [Integritätsprüfungen](integritaetspruefungen.md) |
| OEM | Weiterverkauf eigener Produkte auf Basis von Exponential, ein Partnervorteil | [OEM und das Partnerprogramm](oem-und-das-partnerprogramm-fuer-buendler.md) |

## P bis Z

| Begriff | Bedeutung | Seite |
|---|---|---|
| Paketserver | eine statische HTTPS-Website mit `index.xml` und `.ezpkg`-Dateien je Release | [Ihr eigener Paketserver](ihr-eigener-paketserver.md) |
| Quellcode-Angebot | wie Empfänger den zugehörigen Quellcode der GPL-Teile erhalten | [GPL-Pflichten](gpl-pflichten-beim-weitergeben.md) |
| Release-Tag | ein dauerhafter Git-Tag wie `v6.0.14`; wird nie verschoben | [Versionen, Tags und Pinning](versionen-tags-und-pinning.md) |
| Seitenpaket | ein Paket vom Typ `site`, das die neue Website aufbaut | [Seitenpakete in einem Bundle](seitenpakete-in-einem-bundle.md) |
| Siteaccess | ein konfigurierter Zugang zur Website; Verzeichnisse `site`, `admin`, `editor` | [Die drei Installer](die-drei-installer.md#was-jeder-installer-schreibt) |
| Starter-Kit | das Repository einer Agentur, aus dem neue Projekte starten | [Starter-Kits für Agenturen](starter-kits-fuer-agenturen.md) |
| Test auf bestehende Installation | `grep -q '^\[DatabaseSettings\]' settings/override/site.ini.append.php`, derselbe Test, den `exp:install` verwendet | [Erster Start und einmalige Installation](erster-start-und-einmalige-installation.md) |
| Tilde-Bedingung | `~6.0.8`: die letzte Stelle ist frei | [Versionen, Tags und Pinning](versionen-tags-und-pinning.md#tilde-bedingungen) |
| Velocity | der Web- und Anwendungsserver von Exponential; Engine `qbix` empfohlen | [Velocity in einem Bundle](velocity-in-einem-bundle.md) |
| Wartungsmodus | `var/maintenance.json`; die Website antwortet mit 503, solange eine Installation oder ein Update läuft | [Die drei Installer](die-drei-installer.md#wartungsmodus-während-der-installation) |
| Weitergabe (rechtlich) | die Übergabe von Kopien an andere, mit der die Bedingungen der GPL greifen | [GPL-Pflichten](gpl-pflichten-beim-weitergeben.md) |

Weitere Begriffe: [Glossar für Partner](../partner/glossar-fuer-partner.md), [Glossar für Hoster](../hosting/glossar-fuer-hoster.md),
[Glossar der Seitenpakete](../seitenpakete/glossar-der-seitenpakete.md).

## Verwandte Seiten

- [FAQ für Bündler](faq-fuer-buendler.md)
- [Lesepfad für Bündler](lesepfad-fuer-buendler.md)
- [Bündler: hier beginnen](buendler-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
