---
title: "Bündler: hier beginnen"
audience: bundler, partner, hosting
category: bundling
order: 1
summary: Die Einstiegsseite für Menschen und Firmen, die Exponential mit anderer Software bündeln und das Ergebnis ausliefern - Server-Images, Container, Panel-Installer, Distributionspakete, Starter-Kits für Agenturen, Distributionen, Seitenpakete, Erweiterungs-Bundles, Appliances und Demo-Bundles. Was ein Bundle ist, welche Regeln zählen, eine Übersicht aller Seiten für Bündler und Lesepfade nach Rolle.
keywords: Bündler, Bundle, bündeln, Distribution, Image, Container, Appliance, Ein-Klick-Installer, Starter-Kit, Seitenpaket, Erweiterungs-Bundle, Paketierung, bundler, bundling, one-click installer, starter kit, site package, extension bundle, packaging, GPL, kickstarter, exp:install, release checklist
featured: true
type: help
---
# Bündler: hier beginnen

Dieser Bereich richtet sich an **Bündler**: Menschen und Firmen, die Exponential nehmen, es mit anderen Dingen
kombinieren (einem Betriebssystem, einem Webserver, einer Datenbank, Erweiterungen, einem Theme, einem Seitenpaket,
eigenen Werkzeugen) und das Ergebnis an andere ausliefern. Ein vorinstalliertes Server-Image, ein Container, der
Ein-Klick-Installer eines Hosting-Panels, ein Paket einer Linux-Distribution, das Starter-Kit einer Agentur, eine
Legacy- oder Nexus-Distribution, ein Seitenpaket, ein Erweiterungs-Bundle, eine Appliance und ein Schulungs- oder
Demo-Bundle sind allesamt Bundles.

Er beantwortet vier Fragen: was Sie ausliefern dürfen und was Sie mitliefern müssen, auf welcher Basis Sie aufbauen,
wie Sie die Installation in Ihrem Bundle wiederholbar und sicher machen und wie Sie die Empfänger Ihres Bundles auf
dem aktuellen Stand halten.

## Inhalt

1. [Exponential bündeln in einer Minute](#exponential-bündeln-in-einer-minute)
2. [Die Grundregeln dieses Bereichs](#die-grundregeln-dieses-bereichs)
3. [Übersicht der Seiten für Bündler](#übersicht-der-seiten-für-bündler)
4. [Lesepfade](#lesepfade)
5. [Wen Sie fragen können](#wen-sie-fragen-können)

## Exponential bündeln in einer Minute

- **Sie dürfen es bündeln und ausliefern.** Der Code von Exponential ist freie Software unter der GNU General Public
  License, Version 2 oder jeder späteren Version (`composer.json`: `"license": "GPL-2.0-or-later"`). Sie dürfen für
  das Bundle, für Ihre Arbeit und für Dienstleistungen Geld verlangen
  ([GPL-Pflichten beim Weitergeben](gpl-pflichten-beim-weitergeben.md)).
- **Ausliefern bringt Pflichten mit sich.** Geben Sie die Lizenztexte und Hinweise weiter, bieten Sie den
  zugehörigen Quellcode an, fügen Sie keine Einschränkungen hinzu. Dies ist keine Rechtsberatung; maßgeblich sind die
  Lizenztexte.
- **Wählen Sie die Basis bewusst.** Exponential 6 für sich allein oder eine Platform-Distribution, die es enthält
  (Legacy, Nexus) ([Die Basis wählen](die-basis-waehlen.md)).
- **Legen Sie Versionen fest.** Ein Release-Tag ändert sich nie; bauen Sie aus Tags und behalten Sie Ihre Lock-Datei
  ([Versionen, Tags und Pinning](versionen-tags-und-pinning.md)).
- **Installieren Sie unbeaufsichtigt.** `php bin/php/console exp:install` oder der Kickstarter (`kickstart.ini`)
  erledigen die gesamte Installation ohne Browser ([Die drei Installer](die-drei-installer.md)).
- **Liefern Sie nie ein bekanntes Passwort aus.** Die Installer erzeugen ein zufälliges Administratorpasswort, wenn
  keines angegeben ist, und schreiben es nach `var/log/initial-admin-password`; öffentliche Demos sind die einzige
  Ausnahme, mit `EXP_DEMO_MODE=1`
  ([Standardzugangsdaten und erste Anmeldung](standardzugangsdaten-und-erste-anmeldung.md)).
- **Erst bauen, dann auffrischen.** Autoloads und Caches nach jedem Build, die richtigen Besitzrechte für `var/`,
  das Integritätsmanifest geprüft
  ([Caches und Autoloads nach dem Bauen](caches-und-autoloads-nach-dem-bauen.md),
  [Integritätsprüfungen](integritaetspruefungen.md)).
- **Planen Sie den Update-Weg vor dem ersten Release.** Legen Sie fest, wer was wie aktualisiert
  ([Updates und Upgrade-Pfade](updates-und-upgrade-pfade.md)).
- **Benennen Sie es ehrlich.** "Basierend auf Exponential", nie ein Name, der nahelegt, Sie wären das Projekt
  ([Ein Bundle benennen und gestalten](ein-bundle-benennen-und-gestalten.md)).

## Die Grundregeln dieses Bereichs

1. **Jede technische Aussage nennt ihre Quelle**: eine Datei, eine Einstellung, einen Befehl oder eine andere Seite
   der Dokumentation. Die Befehle und Optionen sind die, die der Code hat; keiner ist erfunden.
2. **Seiten zur Lizenzierung sind Erinnerungen, keine Rechtsberatung.** Lassen Sie Ihr eigenes Bundle juristisch
   prüfen.
3. **Fakten zum Partnerprogramm werden von den Partnerseiten zitiert.** Wo diese schweigen, sagen diese Seiten
   "nicht veröffentlicht; fragen Sie das Partnerteam".
4. **Beispiele verwenden Platzhalter**: `example.com`, "Ihre Firma", `<site user>`.

## Übersicht der Seiten für Bündler

| Bereich | Seiten |
|---|---|
| Bündeln verstehen | [Was ein Bundle ist](was-ein-bundle-ist.md) · [Arten von Bundles](arten-von-bundles.md) · [Lesepfad](lesepfad-fuer-buendler.md) · [Glossar](glossar-fuer-buendler.md) · [FAQ](faq-fuer-buendler.md) |
| Die Basis wählen | [Die Basis wählen](die-basis-waehlen.md) · [Exponential 6 als Basis](exponential-6-als-basis.md) · [Platform-Distributionen als Basis](platform-distributionen-als-basis.md) |
| Code beschaffen und festlegen | [Den Code beschaffen](den-code-fuer-ein-bundle-beschaffen.md) · [Versionen, Tags und Pinning](versionen-tags-und-pinning.md) · [Bundles auf Composer-Basis](bundles-auf-composer-basis.md) · [Lock-Dateien und reproduzierbare Builds](lock-dateien-und-reproduzierbare-builds.md) · [Erweiterungen hinzufügen und entfernen](erweiterungen-hinzufuegen-und-entfernen.md) |
| Lizenzen und Dokumentation | [GPL-Pflichten](gpl-pflichten-beim-weitergeben.md) · [Lizenztexte und Hinweise](mitzuliefernde-lizenztexte-und-hinweise.md) · [Komponenten Dritter](komponenten-dritter-und-ihre-lizenzen.md) · [Lizenzfelder in Ihren Paketen](lizenzfelder-in-ihren-eigenen-paketen.md) · [Mitzuliefernde Dokumentation](mitzuliefernde-dokumentation.md) |
| Seitenpakete | [Seitenpakete in einem Bundle](seitenpakete-in-einem-bundle.md) · [Ihr eigener Paketserver](ihr-eigener-paketserver.md) · [Offline-Bundles](offline-bundles.md) |
| Installation im Bundle | [Die drei Installer](die-drei-installer.md) · [Unbeaufsichtigte Installation mit exp:install](unbeaufsichtigte-installation-mit-exp-install.md) · [Der Kickstarter für Bundles](der-kickstarter-fuer-bundles.md) · [kickstart.ini-Vorlagen](kickstart-ini-vorlagen.md) · [Erster Start und einmalige Installation](erster-start-und-einmalige-installation.md) |
| Datenbank und Auslieferung | [Datenbankwahl](datenbankwahl-fuer-bundles.md) · [SQLite-Bundles](sqlite-bundles.md) · [Webserver- und Engine-Wahl](webserver-und-engine-wahl.md) · [Velocity in einem Bundle](velocity-in-einem-bundle.md) |
| Bundle-Arten | [Container-Images](container-images.md) · [Server- und VM-Images](server-und-vm-images.md) · [Installer für Hosting-Panels](installer-fuer-hosting-panels.md) · [Pakete für Linux-Distributionen](pakete-fuer-linux-distributionen.md) · [Starter-Kits für Agenturen](starter-kits-fuer-agenturen.md) · [Erweiterungs-Bundles](erweiterungs-bundles.md) · [Appliances](appliances.md) · [Bundles für Schulung und Demo](bundles-fuer-schulung-und-demo.md) |
| Sicher ab Werk | [Demo-Modus in Bundles](demomodus-in-bundles.md) · [Standardzugangsdaten und erste Anmeldung](standardzugangsdaten-und-erste-anmeldung.md) · [Sicherheitsvorgaben eines Bundles](sicherheitsvorgaben-eines-bundles.md) · [Dateirechte und Besitz von var/](dateirechte-und-besitz-von-var.md) |
| Bauen und veröffentlichen | [Caches und Autoloads nach dem Bauen](caches-und-autoloads-nach-dem-bauen.md) · [Integritätsprüfungen](integritaetspruefungen.md) · [Ein Bundle testen](ein-bundle-testen.md) · [Checkliste für die Veröffentlichung](checkliste-fuer-die-bundle-veroeffentlichung.md) · [Checklisten](checklisten-fuer-bundles.md) · [Arbeitsblatt für Bundles](arbeitsblatt-fuer-bundles.md) |
| Das Leben nach dem Release | [Updates und Upgrade-Pfade](updates-und-upgrade-pfade.md) · [Grenzen des Supports](grenzen-des-supports.md) · [Korrekturen zurückgeben](bundle-korrekturen-zurueckgeben.md) · [Erweiterungen veröffentlichen](erweiterungen-aus-einem-bundle-veroeffentlichen.md) |
| Marke und Partnerschaft | [Ein Bundle benennen und gestalten](ein-bundle-benennen-und-gestalten.md) · [OEM und das Partnerprogramm](oem-und-das-partnerprogramm-fuer-buendler.md) |

## Lesepfade

| Sie sind | Lesen Sie in dieser Reihenfolge |
|---|---|
| Sie bauen ein Container- oder Server-Image | [Was ein Bundle ist](was-ein-bundle-ist.md), [Container-Images](container-images.md) oder [Server- und VM-Images](server-und-vm-images.md), [Erster Start und einmalige Installation](erster-start-und-einmalige-installation.md), [Dateirechte](dateirechte-und-besitz-von-var.md), [Checkliste für die Veröffentlichung](checkliste-fuer-die-bundle-veroeffentlichung.md) |
| Sie schreiben einen Installer für ein Hosting-Panel | [Installer für Hosting-Panels](installer-fuer-hosting-panels.md), [Unbeaufsichtigte Installation mit exp:install](unbeaufsichtigte-installation-mit-exp-install.md), [Standardzugangsdaten](standardzugangsdaten-und-erste-anmeldung.md), dann die dort verlinkten Hosting-Seiten |
| Sie paketieren für eine Linux-Distribution | [Pakete für Linux-Distributionen](pakete-fuer-linux-distributionen.md), [GPL-Pflichten](gpl-pflichten-beim-weitergeben.md), [Komponenten Dritter](komponenten-dritter-und-ihre-lizenzen.md), [Updates und Upgrade-Pfade](updates-und-upgrade-pfade.md) |
| Eine Agentur, die ein Starter-Kit ausliefert | [Starter-Kits für Agenturen](starter-kits-fuer-agenturen.md), [Bundles auf Composer-Basis](bundles-auf-composer-basis.md), [Seitenpakete in einem Bundle](seitenpakete-in-einem-bundle.md), [kickstart.ini-Vorlagen](kickstart-ini-vorlagen.md) |
| Sie bauen eine Distribution | [Die Basis wählen](die-basis-waehlen.md), [Platform-Distributionen als Basis](platform-distributionen-als-basis.md), [Versionen, Tags und Pinning](versionen-tags-und-pinning.md), [Benennen und Gestalten](ein-bundle-benennen-und-gestalten.md) |
| Autor eines Seitenpakets oder Erweiterungs-Bundles | [Seitenpakete in einem Bundle](seitenpakete-in-einem-bundle.md), [Erweiterungs-Bundles](erweiterungs-bundles.md), [Lizenzfelder in Ihren Paketen](lizenzfelder-in-ihren-eigenen-paketen.md), [Erweiterungen veröffentlichen](erweiterungen-aus-einem-bundle-veroeffentlichen.md) |
| Anbieter einer Appliance | [Appliances](appliances.md), [Sicherheitsvorgaben](sicherheitsvorgaben-eines-bundles.md), [Grenzen des Supports](grenzen-des-supports.md), [OEM und das Partnerprogramm](oem-und-das-partnerprogramm-fuer-buendler.md) |
| Sie bereiten ein Schulungs- oder Demo-Bundle vor | [Bundles für Schulung und Demo](bundles-fuer-schulung-und-demo.md), [Demo-Modus in Bundles](demomodus-in-bundles.md), [SQLite-Bundles](sqlite-bundles.md) |

Die vollständige Reihenfolge, Seite für Seite, ist der [Lesepfad für Bündler](lesepfad-fuer-buendler.md).

## Wen Sie fragen können

| Frage | Wo | Quelle |
|---|---|---|
| Das Partnerprogramm, OEM-Weiterverkauf, Ihre Stufe | Das Partnerteam: <partners@exponential.earth> oder das Formular unter <https://exponential.earth/partner/contact-us> | [Partnerpläne und Preise](../partner/partnertarife-und-preise.md#partnerbedingungen) |
| Bezahlter Support und Dienstleistungen von 7x | <https://se7enx.com/contact> | [Partner: hier beginnen](../partner/partner-hier-beginnen.md#wen-sie-fragen) |
| Technische Fragen | Foren <https://share.exponential.earth/forums>, Telegram <https://t.me/exponentialcms> | [Partner: hier beginnen](../partner/partner-hier-beginnen.md#wen-sie-fragen) |
| Ein Sicherheitsproblem in Exponential | security@se7enx.com, vertraulich | `SECURITY.md` im Wurzelverzeichnis des Repositorys |
| Ein Fehler im Kernel | der Issue-Tracker von <https://github.com/se7enxweb/exponential> | [Korrekturen zurückgeben](bundle-korrekturen-zurueckgeben.md) |

## Verwandte Seiten

- [Partner: hier beginnen](../partner/partner-hier-beginnen.md)
- [Hosting-Firmen: hier beginnen](../hosting/hosting-anbieter-hier-beginnen.md)
- [Seitenpakete: hier beginnen](../seitenpakete/README.md)
- [Wiederverkäufer: hier beginnen](../wiederverkaeufer/wiederverkaeufer-hier-beginnen.md)
- [Integratoren: hier beginnen](../integratoren/integratoren-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
