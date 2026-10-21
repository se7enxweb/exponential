---
title: Ein Plan für einen Proof of Concept zur Bewertung von Exponential
audience: government, project-manager, admin
category: public-sector
order: 80
summary: Ein Plan für einen Proof of Concept, mit dem eine öffentliche Stelle Exponential in wenigen Tagen auf ihrem eigenen Rechner testet, beginnend mit einer SQLite-Installation mit einem Befehl - Umfang, Umgebung, Testfälle mit erwarteten Ergebnissen, Regeln für Daten und der Bericht, den das Gremium schreibt.
keywords: Proof of Concept, PoC, Pilot, Testinstallation, SQLite, Testplan, Testfälle, Bewertung, Abnahmekriterien, proof of concept, pilot, trial installation, exp:install, test plan, test cases, evaluation, acceptance criteria
type: help
---
# Ein Plan für einen Proof of Concept zur Bewertung von Exponential

Diese Seite richtet sich an die technische Leitung eines Bewertungsgremiums, die Exponential vor oder während einer
Ausschreibung in einer Umgebung testen möchte, die die öffentliche Stelle selbst kontrolliert. Weil die Software frei
ist und sich mit einem Befehl auf SQLite installieren lässt, braucht ein Proof of Concept keine Lizenz, keinen
Datenbankserver und keinen Vertrag. Die Schritte stammen aus
[Installation mit einem Befehl](../../../../funktionen/6.0/installation-mit-einem-befehl.md) und
[Erste Schritte](../../../../anleitungen/erste-schritte.md).

## Inhalt
1. [Umfang und Rollen](#umfang-und-rollen)
2. [Die Umgebung](#die-umgebung)
3. [Installation mit einem Befehl](#installation-mit-einem-befehl)
4. [Testfälle](#testfälle)
5. [Regeln für Daten im Proof of Concept](#regeln-für-daten-im-proof-of-concept)
6. [Der Bericht zum Proof of Concept](#der-bericht-zum-proof-of-concept)
7. [Aufräumen](#aufräumen)

## Umfang und Rollen

- **Ziel**: auf Ihrem eigenen Rechner bestätigen, dass die Anforderungen, auf die sich die Angebote stützen, wie
  dokumentiert funktionieren.
- **Nicht das Ziel**: die endgültige Website bauen oder das öffentliche Design des Bieters testen (das gehört zur
  Abnahme).
- **Rollen**: eine technische Leitung, die installiert und Befehle ausführt; eine Person, die die Redaktion testet; eine
  Person, die die Barrierefreiheit testet; eine Person, die die Sicherheit prüft. Jede führt die Testfälle ihres
  Bereichs aus.
- **Zeitrahmen**: Legen Sie ihn fest, bevor Sie beginnen, und hören Sie auf, wenn er endet.

## Die Umgebung

| Bedarf | Aus [Anforderungen](../../../../installation/02-voraussetzungen.md) |
|---|---|
| Ein Rechner oder eine virtuelle Maschine, die Sie löschen dürfen | AlmaLinux 9 und Ubuntu sind Stufe 1, „tested here“ ([Unterstützung von Betriebssystemen](../../../betriebssystemunterstuetzung.md#stufe-1-hier-getestet)) |
| PHP 8.0 oder neuer (der eingebaute Server Velocity eingeschlossen) | prüfen mit `php -v` |
| Composer 2 und Git | um den Code zu holen |
| Kein Datenbankserver | SQLite 3 ist die Standarddatenbank |

## Installation mit einem Befehl

```bash
git clone https://github.com/se7enxweb/exponential.git exponential
cd exponential
composer install
./console exp:install --dry-run          # checks configuration and packages, installs nothing
./console exp:install --url=http://localhost:8087
./console exp:velocity start --engine=php
```

Das Installationsprogramm gibt die Adressen der Website und der Administration aus, den Benutzernamen `admin` und ein
generiertes Passwort mit 24 Zeichen, das es auch nach `var/log/initial-admin-password` schreibt. Öffnen Sie die
Administration, melden Sie sich an, ändern Sie das Passwort mit **Passwort ändern** und löschen Sie dann diese Datei. Die
Engine `php` lauscht standardmäßig auf Port 8087
([Erste Schritte, Schritt 4](../../../../anleitungen/erste-schritte.md#4-die-site-ansehen)).

Nennt Ihr Hosting-Plan eine andere Datenbank, wiederholen Sie die Installation mit `--db=pgsql`, `--db=mysql` oder einer
anderen Engine aus der Tabelle in
[Installation mit einem Befehl](../../../../funktionen/6.0/installation-mit-einem-befehl.md#eine-datenbank-wählen) und übergeben das
Passwort über die Umgebungsvariable `EXP_INSTALL_DB_PASSWORD`.

## Testfälle

| # | Test | Erwartetes Ergebnis | Quelle |
|---|---|---|---|
| T1 | Eine Seite anlegen, in der Vorschau ansehen, veröffentlichen | veröffentlichte Version auf der Website sichtbar | [Inhaltsmodell und Bearbeitung](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md) |
| T2 | Erneut bearbeiten, Versionen vergleichen, wiederherstellen | ein neuer Entwurf aus der älteren Version, dann veröffentlicht | [Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md) |
| T3 | Eine auf eine Sektion eingeschränkte Rolle anlegen; als Testredaktion anmelden | die Redaktion sieht und bearbeitet nur diese Sektion | [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md) |
| T4 | Das Audit-Protokoll zeigt T1 bis T3 | Ereignisse in der Audit-Konsole aufgelistet | [Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md) |
| T5 | Die Audit-Kette prüfen: `./console exp:audit verify` | die Kette lässt sich verifizieren | [Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md) |
| T6 | Administration nur mit Tastatur | Sprunglink, sichtbarer Fokus, alle Aktionen erreichbar | [Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md) |
| T7 | Eine zweite Inhaltssprache hinzufügen und T1 übersetzen | beide Sprachen veröffentlicht | [Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md) |
| T8 | Wartungsmodus an und aus | Besucher sehen die Wartungsseite | [Wartungsmodus](../../../../anleitungen/wartungsmodus.md) |
| T9 | Sicherung und Wiederherstellung auf einem zweiten Rechner | die wiederhergestellte Website zeigt T1 | [Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md) |
| T10 | Ein Website-Archiv exportieren | ZIP mit `manifest.json` und je Klasse einer CSV-Datei | [Exponential verlassen](../../../../installation/migration/exponential-verlassen.md) |
| T11 | Die Upgrade-Prüfung ausführen | Bericht der Dateien, die vom Release abweichen | [Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md) |
| T12 | Die Hilfe in der Administration lesen | die Hilfe öffnet sich über die Schaltfläche „?“ | [Hilfesystem](../../../../funktionen/6.0/hilfesystem.md) |

Ergänzen Sie Testfälle für die Anforderungen Ihrer Ausschreibung und halten Sie für jeden fest: Datum, testende Person,
Ergebnis (bestanden, nicht bestanden, teilweise), Nachweis (Screenshot erfundener Daten, Protokollzeile, Dateiliste).

## Regeln für Daten im Proof of Concept

- Verwenden Sie nur erfundene Inhalte und erfundene Konten, mit Adressen unter `example.invalid` oder Ihrer eigenen
  Testdomain.
- Importieren Sie nie echte personenbezogene Daten von Bürgerinnen, Bürgern oder Beschäftigten in einen Proof of Concept.
- Stellen Sie die Testinstallation nicht ohne Härtung ins Internet; sie läuft mit einer Passwortrichtlinie für Demos und
  ohne TLS ([Sicherheitshärtung](../../../sicherheitshaertung.md)).

## Der Bericht zum Proof of Concept

```text
Proof of concept - <tender or project>         Version tested: Exponential <version / commit>
Environment: <OS, PHP version, database, engine>
Period: <dates>      Testers: <names and roles>
Results: T1 pass ... T12 pass/fail - with evidence references
Deviations from the documentation: <list, with links>
Open questions for bidders: <list>
Conclusion: <facts only; scores are given in the scoring sheet>
```

## Aufräumen

Halten Sie den Server mit `./console exp:velocity stop --engine=php` an und entfernen Sie dann den Testrechner oder das
Installationsverzeichnis, wie es Ihre IT-Regeln verlangen. Der Proof of Concept enthielt nur erfundene Daten, daher
brauchen keine personenbezogenen Daten eine besondere Behandlung.

## Verwandte Seiten
- [Bewertung: Übersicht](README.md)
- [Beschaffung: Übersicht](../README.md)
- [Installation mit einem Befehl](../../../../funktionen/6.0/installation-mit-einem-befehl.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
