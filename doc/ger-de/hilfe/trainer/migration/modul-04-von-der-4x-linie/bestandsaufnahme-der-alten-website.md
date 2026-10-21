---
title: "Lektion 4.1: Bestandsaufnahme einer alten 4.x-Website"
audience: trainer, admin, developer
category: training
order: 41
summary: Die Teilnehmenden finden heraus, was eine alte 4.x-Website tatsächlich betreibt (Version, Datentypen, Passwort-Hash-Typen, Zeichensatz und Tabellentyp, Cluster-Modus, Suchmaschine, Größe), und bereiten sie vor, solange sie noch ihren alten Code ausführt - Konsistenzprüfungen, Verlassen des Datenbank-Clusters, Aufräumen, Sicherungen und eine Aufzeichnung dessen, was funktioniert. 45 Minuten.
keywords: migration zu exponential, legacy-kernel, bestandsaufnahme, version, datentypen, zeichensatz, cluster, upgrade-prüfung, sicherung, alte website vorbereiten, migrating to exponential, 4.x, legacy kernel, take stock, ezsite_data, datatypes, charset, innodb, eZDBFileHandler, upgrade check, backup, preparing the old site
type: help
---
# Lektion 4.1: Bestandsaufnahme einer alten 4.x-Website

Eine Website, die noch eine 4.x-Version des alten Legacy-Kernels unter PHP 5 betreibt, zieht in place zu
Exponential 6 um: dieselben Tabellen, dieselben Datentypen, dieselben Templates, auf den aktuellen Stand gebracht. Die
Arbeit liegt darin, genau zu wissen, wo die Website startet. Diese Lektion vermittelt die Bestandsaufnahme aus Kapitel
14.3 und die Vorbereitung aus Kapitel 14.4.

> **Hinweis**
> Exponential 6 ist die Fortführung des eZ Publish Legacy-Kernels, eines Produkts, das sein Hersteller nach fast 18
> Jahren eingestellt hat. Der alte Name wird in diesem Modul nur verwendet, um die Versionen zu bezeichnen, von denen
> die Teilnehmenden migrieren.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 4: Von der 4.x-Linie](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Experten |
| Zielgruppe | Administratoren und Entwickler, die den Datenbank- und den Code-Strang betreuen |
| Voraussetzungen | [Modul 1](../modul-01-eine-migration-planen/README.md); eine Schulungsinstallation mit Shell-Zugang und einem Administratorkonto |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Version des Codes und der Datenbank lesen und erklären, warum sie übereinstimmen müssen;
- die Fakten aufzählen, die Kapitel 14.3 verlangt, und die Abfrage oder Einstellung, die jeden davon liefert;
- die Vorbereitungsschritte nennen, die auf der alten Website erfolgen müssen, solange sie noch ihren alten Code
  ausführt;
- erklären, warum eine Website mit dem Datenbank-Cluster-Handler diesen vor der Migration verlassen muss.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Kapitel 14.1](../../../../installation/14-migration-von-4x.md#141-was-sich-ändert-und-was-nicht),
[14.3](../../../../installation/14-migration-von-4x.md#143-eine-bestandsaufnahme-der-alten-website-machen) und
[14.4](../../../../installation/14-migration-von-4x.md#144-die-alte-installation-vorbereiten).

1. **Was bleibt, was sich ändert** (5 Minuten). Zeigen Sie die erste Tabelle aus 14.1: Die Schemafamilie, die
   Datentypen, XmlText, ezflow-Seiten, Templates, die Erweiterungs-API und die `eZ`-Klassennamen bleiben. Was sich
   ändert: PHP 5 zu PHP 8 (das größte Arbeitspaket), Zeta Components, die entfernten Cluster-Handler, YUI entfällt,
   jQuery 4, der Online-Editor, Rewrite-Regeln und eine strengere Sicherheitsgrundlage.
2. **Welche Version** (3 Minuten). Die Zeilen `ezpublish-version` und `ezpublish-release` von `ezsite_data` sagen, wo
   die Kette beginnt ([Kapitel 11.2](../../../../installation/11-aktualisieren.md#112-welche-version-betreiben-sie)). Die
   Zeilennamen behalten absichtlich ihre historische Schreibweise.
3. **Die Fakten** (6 Minuten). Verwendete Datentypen, Passwort-Hash-Typen, Zeichensatz und Tabellentyp unter MySQL,
   Cluster-Modus (`[ClusteringSettings] FileHandler`), Suchmaschine, Größen der großen Tabellen und von `var/`.
4. **Die alte Website vorbereiten** (6 Minuten). Konsistenzprüfungen mit **Setup > Upgrade-Prüfung**; den
   Datenbank-Cluster verlassen (`eZDBFileHandler` und `eZFS2FileHandler` wurden entfernt, und Exponential hat keinen
   Handler, der `ezdbfile` lesen kann); optionales Aufräumen; eine Sicherung, die einmal eingespielt wurde; eine
   Aufzeichnung dessen, was funktioniert (URLs pro Siteaccess mit ihrem Status, Suchen mit Ergebnisanzahlen,
   Anmeldungen des letzten Monats).

### Häufige Fragen

**Kann eine 3.9-Website direkt migriert werden?** Nein. Die Update-Skripte von 3.0 bis 3.9 sind nicht im Repository;
bringen Sie die Website zuerst mit der alten Version 3.10 auf 3.10 ([14.5.1](../../../../installation/14-migration-von-4x.md#1451-welche-ausgangspunkte-funktionieren)).

**Lassen wir ein Installationsprogramm auf die alte Datenbank los?** Niemals. Der Setup-Assistent, der Kickstarter und
`exp:install` legen eine neue Datenbank an (Kapitel 14.2).

**Ist `5.4.0alpha1` in der Versionszeile ein Problem?** Nein. Die 5.x-Update-Dateien haben diese Werte geschrieben; so
wurden sie ausgeliefert.

### Stolperfallen

- **Kernel-Änderungen vergessen.** Dateien, die jemand im alten Kernel geändert hat, werden nicht übernommen; notieren
  Sie sie jetzt, damit sie zu Erweiterungen oder Overrides werden können.
- **Die Aufzeichnung dessen, was funktioniert, überspringen.** Ohne sie hat die Überprüfung aus Kapitel 14.12 nichts,
  womit sie vergleichen kann.
- **Auf einer Produktionskopie mit echten Benutzern unterrichten.** Verwenden Sie die Schulungsinstallation oder eine
  alte Demo-Datenbank ohne personenbezogene Daten.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation als Stellvertreter für „die alte Website“: Die Abfragen sind auf einer
Exponential-6-Datenbank dieselben. Die Standarddatenbank ist SQLite in `var/storage/sqlite3/exponential.db`.

1. Lesen Sie die Version des Codes:

   ```bash
   php bin/php/console --version
   ```

2. Lesen Sie die Version der Datenbank:

   ```bash
   sqlite3 -readonly var/storage/sqlite3/exponential.db "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
   ```

3. Führen Sie die Größenabfragen aus Kapitel 14.3 (Objekte, Attribute, Knoten, URL-Aliase, Bilddateien) auf dieselbe
   Weise mit `sqlite3 -readonly` aus und notieren Sie die Zahlen.
4. Lesen Sie den Cluster-Handler und die Suchmaschine, die Ihre Installation verwendet:

   ```bash
   php bin/php/console exp:ini get file.ini/ClusteringSettings/FileHandler
   php bin/php/console exp:ini get site.ini/SearchSettings/SearchEngine
   ```

5. Öffnen Sie in der Administration **Setup > Upgrade-Prüfung**. Lesen Sie die **Übersicht** (Version, Dateien im
   Manifest, Datenbank-Engine). Die Seite sagt, dass beide Prüfungen nur lesen; klicken Sie auf **Überprüfe
   Dateikonsistenz** und warten Sie auf das Ergebnis.
6. Schreiben Sie einen „Bestandsaufnahmebogen“ mit den Überschriften aus Kapitel 14.3: Version, Datentypen,
   Hash-Typen, Zeichensatz und Tabellentyp, Cluster-Modus, Suchmaschine, Größe. Tragen Sie ein, was Sie gefunden
   haben; schreiben Sie unter SQLite für Zeichensatz und Tabellentyp „nur MySQL“.
7. Fügen Sie darunter die Vorbereitungsliste aus Kapitel 14.4 als Checkliste an und markieren Sie, welche Punkte Sie
   vor dem Dump auf der **alten** Website erledigen würden.

## Erwartetes Ergebnis

- Schritt 1 gibt eine Zeile wie `console (Exponential) 6.0.15stable` aus; Schritt 2 die Zeilen `ezpublish-version`
  und `ezpublish-release`, und die beiden Versionen stimmen überein.
- Schritt 4 gibt auf einer Standardinstallation `eZFSFileHandler` und die konfigurierte Suchmaschine aus.
- Schritt 5 zeigt die Übersicht und das Ergebnis der Prüfung der Dateikonsistenz.
- Der Bogen hat jede Überschrift aus 14.3 ausgefüllt oder erklärt, und die Checkliste aus 14.4 markiert
  Konsistenzprüfungen, Verlassen des Datenbank-Clusters, Aufräumen, Sicherung und die Aufzeichnung dessen, was
  funktioniert, als „alte Website“.
- Der Trainer prüft einen Bogen und fragt, warum der Datenbank-Cluster auf der alten Website verlassen werden muss.

## Quiz

1. Welche Tabelle und welche Zeilen enthalten die Version der Datenbank?
2. Welchen Cluster-Handler kann Exponential 6 überhaupt nicht lesen?
   A. `eZFSFileHandler` B. `eZDFSFileHandler` C. `eZDBFileHandler` D. keinen davon
3. Nennen Sie drei Dinge, die laut Kapitel 14.1 gleich bleiben.
4. Warum dürfen Sie `exp:install` niemals auf die alte Datenbank loslassen?
5. Was zeichnen Sie auf der alten Website auf, damit Sie die Migration später überprüfen können?

<details>
<summary>Antworten</summary>

1. `ezsite_data`, Zeilen `ezpublish-version` und `ezpublish-release`
   ([Kapitel 11.2](../../../../installation/11-aktualisieren.md#112-welche-version-betreiben-sie)).
2. **C.** Er wurde entfernt; verschieben Sie die Dateien zuerst mit den Werkzeugen der alten Installation
   ([Kapitel 14.4](../../../../installation/14-migration-von-4x.md#144-die-alte-installation-vorbereiten)).
3. Drei beliebige aus: die Schemafamilie der Datenbank, die Datentypen, `ezxmltext`, der Seiten-Datentyp von ezflow,
   Templates, die Erweiterungs-API, `eZ`-Klassennamen ([Kapitel 14.1](../../../../installation/14-migration-von-4x.md#141-was-sich-ändert-und-was-nicht)).
4. Installationsprogramme legen eine neue Datenbank an ([Kapitel 14.2](../../../../installation/14-migration-von-4x.md#142-die-migration-auf-einer-staging-kopie-planen)).
5. URLs pro Siteaccess mit ihrem HTTP-Status, Suchen mit Ergebnisanzahlen, Anmeldungen des letzten Monats (Kapitel
   14.4).

</details>

## Weiterführende Informationen

- [Kapitel 14: Migration von der 4.x-Linie](../../../../installation/14-migration-von-4x.md): das vollständige Runbook.
- [Ältere Versionen dieses Kernels](../../../../installation/migration/aeltere-versionen.md): welches Kapitel zu welcher
  Version passt.
- [Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md): die beiden Konsistenzprüfungen in der Administration.

## Verwandte Seiten

- [Modul 4: Von der 4.x-Linie](README.md)
- [Migration zu Exponential: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
