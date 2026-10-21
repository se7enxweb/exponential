---
title: "Lektion 6.1: Eine Installer-Engine, drei Oberflächen"
audience: trainer, admin, developer
category: training
order: 61
summary: Die Teilnehmenden erklären die Schrittklassen, die jede Installationsmethode ausführt, vergleichen den Einrichtungsassistenten, den Kickstarter und exp:install und wählen mit dem Entscheidungsleitfaden eine Methode für eine bestimmte Situation. 40 Minuten.
keywords: multisite und seitenpakete, installationsmethoden, einrichtungsassistent, kickstarter, schrittklassen, wartungsmodus, entscheidungsleitfaden, multisite and site packages, install methods, setup wizard, exp:install, step classes, eZStepData, CreateSites, CheckValidity, maintenance mode, setup.log, decision guide
type: help
---
# Lektion 6.1: Eine Installer-Engine, drei Oberflächen

Exponential lässt sich auf drei Wegen installieren, die alle zur gleichen Art von Installation führen: der
Einrichtungsassistent im Browser, der von `kickstart.ini` gesteuerte Kickstarter und das Ein-Befehl-`exp:install`.
Unter allen dreien läuft eine Engine: die Schrittklassen aus `kernel/setup/steps/` und ein Seitenpaket, dessen
Installationsskripte die neue Website formen. Diese Lektion gibt den Teilnehmenden dieses Bild und den
Entscheidungsleitfaden, bevor sie in den Lektionen 6.2 und 6.3 die Methoden für die Kommandozeile verwenden.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 6: Websites mit dem Kickstarter und exp:install installieren](README.md) |
| Dauer | 40 Minuten (20 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Administratoren, Entwickler und Hoster |
| Voraussetzungen | [Lektion 5.1](../modul-05-seitenpakete/was-ein-seitenpaket-ist.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die wichtigsten Installer-Schritte aufzählen und sagen, welcher das Seitenpaket installiert;
- die drei Methoden nach Einstiegspunkt, Quelle der Antworten, Probelauf und Schutz vor dem Überschreiben vergleichen;
- mit dem Entscheidungsleitfaden eine Methode für eine Situation empfehlen;
- die Logs nennen, die eine Installation schreibt, und die Wartungsseite, die sie anzeigt.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [4. Eine Installationsmethode wählen](../../../../installation/04-installationsweg-waehlen.md).

1. **Die Schritte** (6 Minuten). Jeder Schritt ist eine Klasse `eZStep<Name>`, in der Reihenfolge von
   `eZStepData::$StepTable`: Welcome, SystemCheck, SystemFinetune, EmailSettings, DatabaseChoice, DatabaseInit,
   LanguageOptions, SiteTypes, PackageLanguageOptions, SiteAccess, SiteDetails, SiteAdmin, Security, Registration,
   CreateSites, Final. `CreateSites` schreibt die Datenbank und die Einstellungen und installiert das Seitenpaket; er
   ruft die Installationsskripte des Pakets auf (Lektion 5.1).
2. **Die Oberflächen** (8 Minuten). Zeigen Sie den Vergleich am Beamer:

   | | Einrichtungsassistent | Kickstarter | Installation über die Konsole |
   |---|---|---|---|
   | Einstiegspunkt | jede Seite, solange `CheckValidity=true` | `php bin/php/console exp:kickstarter run --force` | `php bin/php/console exp:install` |
   | Antworten aus | den Formularen | `kickstart.ini` | Kommandozeilenoptionen mit Voreinstellungen |
   | Probelauf | nein | `run --dry-run` | `--dry-run` und `--print` |
   | Schutz | startet nur, solange `CheckValidity=true` | `--force` für `CreateSites` nötig | verweigert über einer Installation, sofern nicht `--force` |
   | Am besten für | eine erste Installation, Hosts ohne Shell | wiederholbare Installationen aus einer geprüften Datei | die schnellste Installation, Skripte, CI, Demos |

   `exp:install` ist eine Oberfläche für den Kickstarter: Es schreibt aus seinen Optionen eine temporäre
   `kickstart.ini` und führt dieselben Schritte aus.
3. **Während des Laufs** (3 Minuten). Jede Methode zeigt Besuchern während der Arbeit die Wartungsseite und schreibt
   `var/log/setup.log`; der Kickstarter außerdem `var/log/kickstart.log`, `exp:install` eine maskierte
   `var/log/exp-install-<date>.ini`.
4. **Der Entscheidungsleitfaden** (3 Minuten). Keine Shell: der Assistent. Eine Person muss jede Antwort vorher
   prüfen: der Kickstarter. Teil eines Skripts oder einer Pipeline: `exp:install`. Sonst ist `exp:install` der kürzeste
   Weg und der Assistent der am besten erklärte.

### Häufige Fragen

**Ergeben die drei Methoden unterschiedliche Websites?** Nein. Sie führen dieselben Schritte und dasselbe Seitenpaket
aus.

**Welche verwendet die Übungsumgebung?** Train the Trainer empfiehlt `exp:install` für eine Installation pro Person
und den Kickstarter, wenn dieselbe Übungsumgebung oft neu aufgebaut wird.

**Was ist mit dem Multi-Site-Installer des Seitenpakets?** Er ist der Teil des Seitenpakets bei jeder Installation,
keine vierte Methode.

**Kann der Assistent `kickstart.ini` lesen?** Ja; er füllt daraus Seiten vor und überspringt sie (Kapitel 5).

### Stolperfallen

- **`exp:install` für harmlos halten.** Sein voreingestelltes `--db-action` ist `remove`; mit `--force` über einer
  vorhandenen Installation leert es die Datenbank. Die Lektionen 6.2 und 6.4 zeigen die Schutzmechanismen.
- **`CheckValidity` verwechseln.** Wird es auf einer Live-Website wieder auf `true` gesetzt, landet jeder Besucher im
  Assistenten.
- **Die Logs vergessen.** Wenn etwas fehlschlägt, ist `var/log/setup.log` die erste Stelle zum Lesen.

## Praktische Übung

Eine Diskussions- und Rechercheübung zu zweit, mit geöffnetem Kapitel 4 des Installationsleitfadens, dazu zwei
lesende Befehle auf der Schulungsinstallation.

1. Geben Sie die Schritttabelle des Kickstarters aus, ohne zu installieren:
   `php bin/php/console exp:kickstarter run --list-steps`. Meldet er, dass `kickstart.ini` nicht gefunden wurde,
   notieren Sie die Meldung: Der Befehl braucht die Datei selbst dafür.
2. Geben Sie aus, was `exp:install` verwenden würde, ohne zu installieren: `php bin/php/console exp:install --print`.
3. Suchen Sie in der Ausgabe von Schritt 2 das Seitenpaket, den Datenbanktyp und den Editor-Siteaccess.
4. Wählen Sie für jede Situation eine Methode und nennen Sie den Grund:
   a. Ein Host ohne Shell-Zugang.
   b. Eine Übungsumgebung, die jede Woche auf dieselbe Weise neu aufgebaut wird.
   c. Ein Container-Image, das beim Start installiert.
   d. Eine erste Produktivinstallation, von Hand durchgeführt von einem Administrator, der jede Entscheidung erklärt
   haben möchte.
   e. Ein Server ohne Internetzugang, mit bereits importierten Paketen.
5. Vergleichen Sie mit einem anderen Paar, dann mit Abschnitt 4.5 des Leitfadens.
6. Nennen Sie den Schritt, der das Seitenpaket installiert, und den Fehlercode, der ein abgebrochenes Skript nach der
   Installation meldet.

## Erwartetes Ergebnis

- Schritt 1 gibt die sechzehn Schritte aus (Welcome bis Final, `CreateSites` als verborgen markiert) oder die Meldung
  "nicht gefunden" mit dem Befehl, der die Datei schreibt.
- Schritt 2 gibt die Konfiguration mit maskierten Passwörtern aus; das Paket ist `sevenx_multisite`, der Typ
  `sqlite3`.
- Schritt 4: a. der Assistent; b. der Kickstarter; c. `exp:install`; d. der Assistent oder `exp:install` mit
  ausdrücklichen Optionen; e. der Kickstarter oder der Assistent mit den lokalen Paketen.
- Schritt 6: `CreateSites`; `EZSW-080`.
- Der Trainer prüft Schritt 4 anhand von [4.5](../../../../installation/04-installationsweg-waehlen.md#45-welche-methode-für-welche-situation).

## Quiz

1. Was haben die drei Installationsmethoden gemeinsam?
2. Welche Methode braucht zuerst eine geschriebene Datei?
   A. Der Assistent B. Der Kickstarter C. `exp:install` D. Keine
3. Was schützt eine vorhandene Installation vor `exp:install`?
4. Welches Log schreiben alle drei Methoden?
5. Welche Methode für eine CI-Pipeline, und warum?

<details>
<summary>Antworten</summary>

1. Sie führen dieselben Schrittklassen aus und installieren ein Seitenpaket
   ([4.1](../../../../installation/04-installationsweg-waehlen.md#41-ein-installer-drei-frontends)).
2. **B.** `kickstart.ini`, die `exp:kickstarter ini` schreibt.
3. Es verweigert, wenn `settings/override/site.ini.append.php` einen Block `[DatabaseSettings]` enthält, sofern nicht
   `--force` angegeben ist.
4. `var/log/setup.log`.
5. `exp:install`: Optionen oder Umgebungsvariablen, ein Exit-Status und keine Datei, die als Vorlage dienen muss.

</details>

## Weiterführende Informationen

- [4. Eine Installationsmethode wählen](../../../../installation/04-installationsweg-waehlen.md).
- [5. Der Einrichtungsassistent](../../../../installation/05-setup-assistent.md).
- [Eine Installationsmethode wählen](../../../../../eng-us/help/creators/website-creators/choosing-an-install-method.md): die Sicht der
  Website-Ersteller.
- [Bereitstellung: Überblick](../../../hosting/bereitstellung/README.md): die Sicht des Hosters.

## Verwandte Seiten

- [Modul 6: Websites mit dem Kickstarter und exp:install installieren](README.md)
- [Multisite und Seitenpakete: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
