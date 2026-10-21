---
title: "Lektion 1.2: Mehrere Websites auf einer Installation planen"
audience: trainer, admin, developer
category: training
order: 12
summary: Die Teilnehmenden wählen zwischen den Formen einer Multisite (getrennte Inhalte, gemeinsame Inhalte, Sprachen, Website-Erweiterungen) und entscheiden, wann getrennte Installationen die bessere Antwort sind. 40 Minuten, überwiegend Diskussion und eine Planungsübung auf Papier.
keywords: multisite und seitenpakete, multisite, planung, eine installation oder mehrere, inhaltsbaum, wurzelknoten, website-erweiterung, abwägungen, isolation, multisite and site packages, planning, one installation or several, content tree, root node, site extension, trade-offs, sevenx_multisite
type: help
---
# Lektion 1.2: Mehrere Websites auf einer Installation planen

Bevor jemand ein Einstellungsverzeichnis kopiert, muss das Team entscheiden, was "mehrere Websites" für es bedeutet.
Eine Installation kann mehrere Marken mit getrennten Inhalten ausliefern, eine Marke mit mehreren Designs oder eine
Website in mehreren Sprachen; und manchmal lautet die ehrliche Antwort: mehrere Installationen. Diese Lektion gibt den
Teilnehmenden die Fragen und die Abwägungen, damit die folgenden technischen Lektionen einem Plan dienen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 1: Siteaccesses für mehrere Websites](README.md) |
| Dauer | 40 Minuten (15 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Administratoren, Entwickler und die Menschen, die mit ihnen Websites planen |
| Voraussetzungen | [Lektion 1.1](was-ein-siteaccess-ist.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- vier Formen einer Multisite beschreiben und für jede ein Beispiel nennen;
- erklären, was die Siteaccesses einer Installation immer teilen (Code, Erweiterungen, Datenbank, Benutzer);
- die Abwägungen zwischen einer Installation und einer Installation pro Website aufzählen;
- einen einseitigen Plan für eine Multisite schreiben: Websites, Siteaccesses, Wurzelordner, Zuordnung, Teams.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Mehrere Websites in einer Installation einrichten](../../../../../eng-us/help/creators/website-creators/multisite-setup.md) und
[Multisite auf einer Installation](../../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md).

1. **Die vier Formen** (6 Minuten). Zeigen Sie die Tabelle von der Seite für Website-Ersteller am Beamer:

   | Form | Wie |
   |---|---|
   | Getrennte Inhalte pro Website | ein Ordner pro Website unter der Wurzel; jeder Siteaccess verwendet seinen Ordner als Wurzel |
   | Gemeinsame Inhalte, verschiedene Designs | dieselbe Wurzel, ein anderes `SiteDesign` pro Siteaccess |
   | Eine Website in mehreren Sprachen | ein Siteaccess pro Sprache |
   | Jede Website in einer eigenen Erweiterung | Einstellungen, die nur gelten, während die Siteaccesses der Website laufen |

   Die Formen lassen sich kombinieren: Das Standard-Seitenpaket `sevenx_multisite` ist selbst eine
   Multisite-Installation mit mehr als einer öffentlichen Website in einem Inhaltsbaum.
2. **Was immer geteilt wird** (3 Minuten). Eine Codebasis, ein Satz Erweiterungen, eine Datenbank (für die
   Siteaccesses in `RelatedSiteAccessList[]`), ein Satz Benutzer und Rollen, ein Suchindex. Eine eigene Website
   bedeutet deshalb einen eigenen Ordner, einen eigenen Siteaccess und auf diesen Ordner beschränkte Rollen, nicht
   eine eigene Datenbank.
3. **Eine Installation oder mehrere** (6 Minuten). Gehen Sie die Abwägungen durch:

   | | Eine Installation, mehrere Siteaccesses | Eine Installation pro Website |
   |---|---|---|
   | Gemeinsame Inhalte und Benutzer | ja | nein |
   | Ein Update für alle Websites | ja | eines pro Website |
   | Isolation zwischen Websites auf Betriebssystemebene | nein | ja |
   | Die Last einer Website beeinflusst die anderen | ja | nein, mit getrennten Pools |
   | Sicherungen und Wiederherstellungen | gemeinsam | pro Website |

   Die Faustregel im Hosting-Leitfaden: mehrere Siteaccesses auf einer Installation für **eine Organisation** mit
   mehreren Websites; getrennte Installationen für **verschiedene Kunden**.

### Häufige Fragen

**Können zwei Websites unterschiedliche Erweiterungen haben?** Erweiterungen werden für die ganze Installation
installiert, aber eine Erweiterung kann nur für einige Siteaccesses aktiviert werden, und die Einstellungen einer
Website-Erweiterung können nur für ihre eigenen Siteaccesses gelten (Modul 3).

**Kann eine Website eigene Administratoren haben, die die anderen Websites nicht sehen?** Ja, über Rollen, die nach
Teilbaum oder Sektion eingeschränkt sind, oder über einen eigenen Administrations-Siteaccess. Modul 4 behandelt eine
Administration für mehrere Websites.

**Kostet eine zweite Website eine zweite Lizenz?** Nein. Exponential ist freie Software unter der GNU GPL; es gibt
keine Lizenz pro Website.

**Bewegt ein Upgrade alle Websites auf einmal?** Ja: Eine Codebasis bedeutet, dass jede Website mit den anderen
mitzieht. Planen Sie vor einem Upgrade Tests für jede Website.

### Stolperfallen

- **Von der Technik her planen.** Die Teilnehmenden springen sofort zu `HostMatchMapItems[]`. Bestehen Sie zuerst auf
  dem Plan: wer was bearbeitet, welche Adressen, welche Sprachen.
- **Kunden teilen sich eine Installation.** Gehören die Websites verschiedenen Kunden, lenken Sie die Diskussion auf
  getrennte Installationen; eine Wiederherstellung stellt jede Website wieder her.
- **Die Medienbibliothek vergessen.** Gemeinsame Bilder liegen in einer Medienbibliothek; entscheiden Sie, ob jede
  Website dort auch einen eigenen Ordner bekommt.

## Praktische Übung

Eine Planungsübung auf Papier oder in einem gemeinsamen Dokument; es wird keine Installation gebraucht. Arbeiten Sie
zu zweit.

Der Auftrag: *Ein Sportverein möchte eine Hauptwebsite, eine Website für seine Jugendabteilung mit einem anderen
Aussehen und die Hauptwebsite auf Englisch und Deutsch. Dieselbe Geschäftsstelle bearbeitet alles; die
Jugendtrainer dürfen nur die Jugendwebsite bearbeiten.*

1. Listen Sie die Websites auf, die die Besucher sehen werden.
2. Entscheiden Sie für jede die Form aus der obigen Tabelle (mehrere können sich kombinieren).
3. Nennen Sie die Siteaccesses, die Sie brauchen, einschließlich der Administration und des Editors. Verwenden Sie
   kleingeschriebene Namen mit Unterstrichen, wie es der Installer verlangt (zum Beispiel `club`, `club_ger`,
   `youth`).
4. Skizzieren Sie den Inhaltsbaum: welcher Ordner die Wurzel jeder Website ist und wo gemeinsame Inhalte (Logo,
   Medien, Benutzer) liegen.
5. Entscheiden Sie, wie jeder Siteaccess erreicht wird: über einen Pfad (`/youth`), einen Host (`youth.example.com`)
   oder einen Port. Notieren Sie es; Modul 2 vermittelt jede Methode.
6. Entscheiden Sie, wer was bearbeitet, und schreiben Sie pro Team einen Satz zu den Grenzen seiner Rolle.
7. Entscheiden Sie sich für eine Installation oder mehrere und schreiben Sie den Grund in einem Satz auf.
8. Stellen Sie den Plan einem anderen Paar in zwei Minuten vor und sammeln Sie eine Frage.

## Erwartetes Ergebnis

- Jedes Paar hat einen einseitigen Plan mit drei öffentlichen Siteaccesses (Hauptwebsite, ihr deutscher Siteaccess,
  Jugendwebsite), der Administration und dem Editor, einem Baum mit zwei Website-Wurzeln und einem gemeinsamen
  Medienordner, einer Adresswahl pro Siteaccess und einer Rolle für die Jugendtrainer, die auf den Teilbaum der Jugend
  beschränkt ist.
- Der Plan wählt eine Installation, weil eine Geschäftsstelle alles bearbeitet; ein Paar, das mehrere Installationen
  gewählt hat, kann das nur mit einem echten Isolationsbedarf begründen.
- Der Trainer prüft das, indem er zwei Pläne am Beamer vergleicht und jedes Paar fragt, woher der deutsche Siteaccess
  sein Design bekommt (dasselbe Design wie die Hauptwebsite; Lektion 1.4).

## Quiz

1. Nennen Sie die vier Formen einer Multisite.
2. Was teilen alle Siteaccesses einer Installation?
   A. Nur den Code B. Code, Erweiterungen, Datenbank, Benutzer C. Nur die Datenbank D. Nichts
3. Zwei Kunden möchten je eine Website auf Ihrem Server. Eine Installation oder zwei? Warum?
4. Richtig oder falsch: Jede öffentliche Website einer Multisite braucht eine eigene Datenbank.
5. Welches Seitenpaket installiert direkt eine Multisite?

<details>
<summary>Antworten</summary>

1. Getrennte Inhalte pro Website; gemeinsame Inhalte mit verschiedenen Designs; eine Website in mehreren Sprachen;
   jede Website in einer eigenen Erweiterung ([Mehrere Websites einrichten](../../../../../eng-us/help/creators/website-creators/multisite-setup.md#shapes-of-a-multisite)).
2. **B.** Eine Codebasis, die Erweiterungen, die Datenbank der verwandten Siteaccesses und die Benutzer.
3. Zwei Installationen: Verschiedene Kunden brauchen Isolation, und eine Wiederherstellung einer Installation stellt
   jede Website wieder her
   ([Multisite auf einer Installation](../../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md#eine-installation-oder-mehrere)).
4. **Falsch.** Die Siteaccesses in `RelatedSiteAccessList[]` teilen eine Datenbank; getrennte Inhalte sind getrennte
   Ordner.
5. `sevenx_multisite`, das Standard-Seitenpaket von `exp:install` und dem Kickstarter.

</details>

## Weiterführende Informationen

- [Mehrere Websites in einer Installation einrichten](../../../../../eng-us/help/creators/website-creators/multisite-setup.md): Formen
  und Abwägungen.
- [Multisite auf einer Installation](../../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md): die Fragen des
  Hosters.
- [Den Inhaltsbaum strukturieren](../../../../../eng-us/help/creators/website-creators/structuring-the-content-tree.md): Wurzelordner
  pro Website.
- [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md): ein Team auf seinen Teilbaum beschränken.

## Verwandte Seiten

- [Modul 1: Siteaccesses für mehrere Websites](README.md)
- [Multisite und Seitenpakete: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
