---
title: Die drei Installer aus Sicht des Bündlers
audience: bundler, hosting, developer
category: bundling
order: 190
summary: Der Einrichtungsassistent, der Kickstarter und exp:install führen dieselben Installationsschritte aus; der Unterschied liegt darin, woher die Antworten kommen. Welchen ein Bundle aufrufen sollte, was jeder schreibt, wie jeder vor dem Überschreiben einer Website schützt und wie jeder mit dem Administratorpasswort umgeht.
keywords: Installer, Einrichtungsassistent, Kickstarter, unbeaufsichtigt, Probelauf, Wartungsmodus, Exit-Status, installer, setup wizard, kickstarter, exp:install, install.php, kickstarter.php, CreateSites, unattended, bundle, dry run, force, maintenance mode, exit status
type: help
---
# Die drei Installer aus Sicht des Bündlers

Diese Seite richtet sich an Bündler, die entscheiden, wie ihr Bundle die Website installiert. Exponential hat einen
Installationsablauf mit drei Oberflächen. Die Referenz ist
[Kapitel 4: Eine Installationsmethode wählen](../../installation/04-installationsweg-waehlen.md).

## Inhalt

1. [Ein Ablauf, drei Oberflächen](#ein-ablauf-drei-oberflächen)
2. [Im Vergleich](#im-vergleich)
3. [Welchen Ihr Bundle aufruft](#welchen-ihr-bundle-aufruft)
4. [Was jeder Installer schreibt](#was-jeder-installer-schreibt)
5. [Wartungsmodus während der Installation](#wartungsmodus-während-der-installation)

## Ein Ablauf, drei Oberflächen

"Jede Installationsmethode führt dieselbe Folge von Schrittklassen in `kernel/setup/steps/` aus, von `Welcome` bis
`Final`; `CreateSites` ist der Schritt, der die Datenbank, die Pakete und die Einstellungen schreibt. Die Oberflächen
unterscheiden sich nur darin, woher die Antworten kommen"
([Ein-Klick-Installer](../hosting/bereitstellung/ein-klick-installer-fuer-exponential.md#ein-installer-drei-oberflächen)).

| Oberfläche | Gestartet durch | Antworten aus |
|---|---|---|
| Einrichtungsassistent | Aufruf der Website im Browser, solange `[SiteAccessSettings] CheckValidity=true` gilt | Browser-Formularen (vorbelegt durch `kickstart.ini`, falls vorhanden) |
| Kickstarter | `php bin/php/console exp:kickstarter run` oder `php bin/php/kickstarter.php run` | `kickstart.ini` im Wurzelverzeichnis der Installation |
| Konsoleninstallation | `php bin/php/console exp:install` oder `php bin/php/install.php` | Kommandozeilenoptionen, jede mit einem Standardwert |

## Im Vergleich

| | Einrichtungsassistent | Kickstarter | `exp:install` |
|---|---|---|---|
| Braucht während der Installation einen Webserver | ja | nein | nein |
| Probelauf | nein | `run --dry-run` | `--dry-run`, `--print` |
| Schutz vor dem Überschreiben | startet nur, solange `CheckValidity=true` gilt | `--force` erforderlich | verweigert eine bestehende Installation ohne `--force` |
| Protokoll | `var/log/setup.log` | `setup.log`, `kickstart.log` | `setup.log`, `var/log/exp-install-<date>.ini` |
| Administratorpasswort | im Formular eingegeben | aus der Datei; erzeugt, wenn leer oder allgemein bekannt | erzeugt, sofern kein zulässiges `--password` übergeben wird |
| Exit-Status für Skripte | entfällt | `0` oder `1` ([6.2.3](../../installation/06-kickstarter.md#623-exit-status)) | `0` oder `1` ([7.7](../../installation/07-installation-ueber-die-konsole.md#77-exit-status)) |

Quelle: [4.2](../../installation/04-installationsweg-waehlen.md#42-die-drei-methoden-im-überblick) und die verlinkten
Kapitel.

## Welchen Ihr Bundle aufruft

| Bundle | Oberfläche | Warum |
|---|---|---|
| Container-Image, CI, automatisierte Umgebungen | `exp:install` | "Er braucht keine Datei, prüft seine Optionen, bevor etwas geschrieben wird, und liefert bei jedem Fehler einen Exit-Status ungleich null" ([4.6](../../installation/04-installationsweg-waehlen.md#46-unbeaufsichtigte-installationen-ci-und-container)) |
| Panel-Installer | `exp:install` | die Optionen entsprechen den Formularfeldern des Panels |
| Starter-Kit einer Agentur, mehrere geprüfte Umgebungen | der Kickstarter | "die geprüfte `kickstart.ini` ist die Spezifikation der Installation" |
| Server-Image, dessen Kunde die Installation abschließt | der Einrichtungsassistent, vorbelegt durch `kickstart.ini` | der Kunde wählt die Angaben zur Website im Browser |
| Shared Hosting ohne Shell | der Einrichtungsassistent | "er braucht nur einen Browser und FTP-Zugang" ([4.5](../../installation/04-installationsweg-waehlen.md#45-welche-methode-für-welche-situation)) |

Einzelheiten: [Unbeaufsichtigte Installation mit exp:install](unbeaufsichtigte-installation-mit-exp-install.md),
[Der Kickstarter für Bundles](der-kickstarter-fuer-bundles.md).

## Was jeder Installer schreibt

Alle drei enden in `CreateSites`, "daher schreiben sie dieselben Dinge"
([4.4](../../installation/04-installationsweg-waehlen.md#44-was-eine-installation-schreibt)):

| Wo | Was |
|---|---|
| `settings/override/site.ini.append.php` | Siteaccess-Zuordnung, Liste der Websites, Mail-Einstellungen, `ActiveExtensions` aus dem Seitenpaket |
| `settings/siteaccess/site/`, `admin/`, `editor/` (und `adminui/`, wenn `exp_adminui` vorhanden ist) | Einstellungen je Siteaccess, **einschließlich des Datenbankpassworts** |
| die Datenbank | Schema, Basisdaten, Inhalte des Seitenpakets |
| `var/storage/packages/`, `var/storage/sqlite3/` | importierte Pakete, die SQLite-Datei |
| `var/log/setup.log`, `var/log/initial-admin-password` | das Protokoll des Laufs; ein erzeugtes Passwort (Modus `0600`) |

"Die **Verzeichnisnamen der Siteaccesses sind immer `site`, `admin` und `editor`**"; was Sie wählen, ist der
Zuordnungswert (URL-Pfad, Port oder Host). Die Dokumentation eines Bundles sollte sagen, welche Adressen es
konfiguriert hat.

> **Warnung**
> Weil die Installer Geheimnisse in `settings/override/` und `settings/siteaccess/` schreiben, darf ein Bundle diese
> Verzeichnisse nie befüllt von seiner Build-Maschine ausliefern. Installieren Sie beim ersten Start beim Empfänger
> ([Erster Start und einmalige Installation](erster-start-und-einmalige-installation.md)).

## Wartungsmodus während der Installation

"Jede Methode schaltet den **Wartungsmodus** ein, während sie arbeitet": `var/maintenance.json` existiert, und die
Front-Controller antworten mit HTTP 503. Nach einem fehlgeschlagenen Schritt bleibt er aktiv;
`php bin/php/maintenance.php off` beendet ihn. "Ein Probelauf schaltet ihn nicht ein"
([4.4](../../installation/04-installationsweg-waehlen.md#wartungsmodus-während-der-installation)). Die Zustandsprüfung eines
Bundles sollte 503 beim ersten Start als "wird installiert" werten, nicht als "defekt".

## Verwandte Seiten

- [Ein-Klick-Installer für Exponential](../hosting/bereitstellung/ein-klick-installer-fuer-exponential.md)
- [Kapitel 5: Der Einrichtungsassistent](../../installation/05-setup-assistent.md)
- [Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
