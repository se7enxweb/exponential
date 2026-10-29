---
title: "Projektleiter: hier beginnen"
audience: project-manager
category: leadership
order: 10
summary: Die Einstiegsseite für Projektleiter - was die Führungsseiten dieser Hilfe bieten, ein Plan für Ihre erste Woche mit Exponential und Links zu jeder Führungsseite und zu den Hilfeseiten, die Sie am häufigsten brauchen.
keywords: Projektleiter, Projektmanagement, hier beginnen, Führung, Einarbeitung, erste Woche, Plan, Überblick, Rollen, Planung, Exponential, project manager, project management, start here, leadership, onboarding, first week, overview, roles, planning
featured: true
type: help
---
# Projektleiter: hier beginnen

Diese Seite richtet sich an Projektleiter, die ein Website- oder Anwendungsprojekt auf Basis von Exponential führen,
ob Sie nun neu im Produkt oder neu in der Rolle sind. Sie beantwortet drei Fragen: Was bietet Ihnen dieser Teil der
Hilfe, was tun Sie in Ihrer ersten Woche, und wo finden Sie später jede Antwort? Alles hier ist so geschrieben, dass
die Hilfe allein Ihre Informationsquelle sein kann: Jede Produktaussage auf diesen Seiten verweist auf die Datei, aus
der sie stammt.

> **Hinweis**
> Exponential ist das Open-Source-Content-Management-System, Framework und Shopsystem, das in dieser Administration
> läuft. Es führt die Codebasis von eZ Publish fort, einem Produkt, das sein Hersteller nach fast 18 Jahren eingestellt
> hat, und wird von dem Unternehmen **7x** gemeinsam mit der gemeinnützigen **Exponential Foundation** geführt
> ([Wer ist 7x?](../wer-ist-7x.md), [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)).

## Inhalt

1. [Was Ihnen dieser Bereich bietet](#was-ihnen-dieser-bereich-bietet)
2. [Ihre erste Woche](#ihre-erste-woche)
3. [Alle Führungsseiten](#alle-führungsseiten)
4. [Die vorhandenen Hilfeseiten, die Sie am häufigsten brauchen](#die-vorhandenen-hilfeseiten-die-sie-am-häufigsten-brauchen)
5. [Wie die Hilfe selbst funktioniert](#wie-die-hilfe-selbst-funktioniert)
6. [Grundregeln dieser Seiten](#grundregeln-dieser-seiten)
7. [Fragen, die vor dem Kick-off beantwortet sein sollten](#fragen-die-vor-dem-kick-off-beantwortet-sein-sollten)

## Was Ihnen dieser Bereich bietet

Die Führungsseiten übersetzen die technische Dokumentation von Exponential in das, was ein Projektleiter zum Planen,
Schätzen, Entscheiden, Koordinieren und Übergeben braucht. Sie ersetzen nicht die Leitfäden für Redakteure,
Administratoren und Entwickler; sie sagen Ihnen, welcher dieser Leitfäden welche Frage beantwortet und in welcher
Reihenfolge Sie sie lesen.

| Sie möchten | Lesen |
|---|---|
| Einem Auftraggeber oder einem neuen Teammitglied das Produkt und seine Familie erklären | [Was Exponential ist: die Produktfamilie](was-exponential-ist.md) |
| Die Phasen eines Projekts planen und Personen den Rollen in der Administration zuordnen | [Ein Exponential-Projekt planen](ein-exponential-projekt-planen.md) |
| Entscheiden, wo die Site läuft: Hosting, Datenbank, Betriebssystem, Webserver | [Hosting, Datenbank und Betriebssystem wählen](hosting-datenbank-und-betriebssystem-waehlen.md) |
| Den Arbeitsumfang abgrenzen und herausfinden, was es bereits als Erweiterung gibt | [Schätzen und Abgrenzen mit Erweiterungen](schaetzen-und-abgrenzen-mit-erweiterungen.md) |
| Die Inhaltsstruktur mit Redakteuren und Entwicklern entwerfen | [Workshops zur Inhaltsmodellierung durchführen](workshops-zur-inhaltsmodellierung.md) |
| Mehrere Sites oder mehrere Sprachen aus einer Installation planen | [Multisite- und mehrsprachige Projekte planen](multisite-und-mehrsprachigkeit-planen.md) |
| Prüfungen zu Barrierefreiheit, Sicherheit und Datenschutz vorbereiten | [Checklisten zu Barrierefreiheit, Sicherheit und Compliance](checklisten-barrierefreiheit-sicherheit-und-compliance.md) |
| Releases und Upgrades über die Lebensdauer der Site planen | [Release- und Upgrade-Planung](release-und-upgrade-planung.md) |
| Vereinbaren, was „fertig“ bedeutet und wie es geprüft wird | [Qualitätsschranken und Abnahme](qualitaetsschranken-und-abnahme.md) |
| Ein Risikoregister führen | [Risikomanagement für Exponential-Projekte](risikomanagement.md) |
| Einen Projektauftrag, eine RACI-Matrix oder eine Livegang-Checkliste übernehmen | [Projektvorlagen](projektvorlagen.md) |
| Den Fortschritt aus den eigenen Logs des Produkts berichten | [Ein Projekt messen: Kennzahlen aus Logs und Audit](kennzahlen-aus-logs-und-audit.md) |

## Ihre erste Woche

Der folgende Plan setzt voraus, dass Sie ein Konto in der Administration mit mindestens der Rolle Editor haben und eine
Demonstrations- oder Testinstallation, die Sie verändern dürfen. Wenn Sie noch keine Installation haben, bitten Sie
einen Administrator oder Entwickler darum: Eine vollständige Demonstrationssite wird mit einem einzigen Befehl
installiert, `./console exp:install --apply`, auf SQLite und ohne Datenbankserver
([Exponential mit einem Befehl installieren](../../funktionen/6.0/installation-mit-einem-befehl.md)).

### Tag 1: das Produkt und die Menschen dahinter kennenlernen

- [ ] Lesen Sie [Was Exponential ist: die Produktfamilie](was-exponential-ist.md) (30 Minuten).
- [ ] Überfliegen Sie [Wer ist 7x?](../wer-ist-7x.md) und [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md),
      zumindest die Abschnitte „Kurz gesagt“ und „Die Support-Zusage“.
- [ ] Schreiben Sie in jeweils einem Satz auf, welches Produkt der Familie Ihr Projekt verwendet und warum. Gelingt
      Ihnen das nicht, hilft die Tabelle „Welches Produkt passt zu Ihnen“ auf der Seite der Foundation.

### Tag 2: die Administration als Redakteur durchgehen

- [ ] Folgen Sie [Erste Schritte](../erste-schritte.md): anmelden, ein Objekt finden, eine erste Änderung vornehmen,
      abmelden.
- [ ] Folgen Sie [Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md), einschließlich „In Aktion: einen Ordner
      bearbeiten“ und „In Aktion: der Layout-Editor“.
- [ ] Lesen Sie [Veröffentlichen, Versionen und der Papierkorb](../veroeffentlichen-und-versionen.md): Nach Entwürfen,
      Versionen und dem Zurückkehren zu früheren Fassungen werden Redakteure Sie als Erstes fragen.
- [ ] Notieren Sie jede Stelle, an der Ihre Rolle eine Schaltfläche ausgegraut oder gar nicht zeigt. Das ist Ihre
      erste Lektion über Rollen und Richtlinien.

### Tag 3: Rollen, Berechtigungen und das Inhaltsmodell verstehen

- [ ] Lesen Sie die Abschnitte 1, 2 und 5 von [Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md).
- [ ] Lesen Sie Abschnitt 1 von [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md):
      Klassen, Attribute, Datentypen, Objekte und Knoten.
- [ ] Lesen Sie „Kurz gesagt“ in [Sektionen](../../anleitungen/sektionen.md) und in [Objektzustände](../../anleitungen/objektzustaende.md).
- [ ] Entwerfen Sie eine erste Liste der Personen in Ihrem Projekt und der Rolle, die jede von ihnen braucht
      ([Ein Exponential-Projekt planen](ein-exponential-projekt-planen.md) enthält eine Tabelle als Ausgangspunkt).

### Tag 4: verstehen, wo die Site läuft und wie sie sich ändert

- [ ] Lesen Sie [Hosting, Datenbank und Betriebssystem wählen](hosting-datenbank-und-betriebssystem-waehlen.md).
- [ ] Fragen Sie Ihren Administrator, welche Datenbank, welchen Webserver und welche Installationsmethode Ihr Projekt
      verwendet, und tragen Sie es in Ihren Projektauftrag ein ([Projektvorlagen](projektvorlagen.md)).
- [ ] Lesen Sie „Kurz gesagt“ in [Das Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md): Es ist Ihr Nachweis, wer
      was getan hat.
- [ ] Lesen Sie [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md): Es beschreibt das Verfahren, mit dem Ihr
      Team Fehler meldet.

### Tag 5: planen

- [ ] Füllen Sie das Arbeitsblatt zur Abgrenzung in [Schätzen und Abgrenzen mit Erweiterungen](schaetzen-und-abgrenzen-mit-erweiterungen.md) aus.
- [ ] Planen Sie den ersten Workshop zur Inhaltsmodellierung ([Workshops zur Inhaltsmodellierung durchführen](workshops-zur-inhaltsmodellierung.md)).
- [ ] Legen Sie ein Risikoregister nach [Risikomanagement für Exponential-Projekte](risikomanagement.md) an.
- [ ] Vereinbaren Sie die Qualitätsschranken mit den Entwicklern ([Qualitätsschranken und Abnahme](qualitaetsschranken-und-abnahme.md)).
- [ ] Lesen Sie [Zusammenarbeiten](zusammenarbeiten.md) und teilen Sie die Seite mit dem Team.

> **Tipp**
> Führen Sie ab Tag 1 ein Projekttagebuch: jede Entscheidung mit Datum, mit der Person, die sie getroffen hat, und
> mit der Hilfeseite oder Datei, auf der sie beruht. Die meisten Vorlagen unter [Projektvorlagen](projektvorlagen.md)
> setzen ein solches Tagebuch voraus.

## Alle Führungsseiten

| Reihenfolge | Seite | Für |
|---|---|---|
| 10 | Projektleiter: hier beginnen (diese Seite) | Projektleiter |
| 20 | [Was Exponential ist: die Produktfamilie](was-exponential-ist.md) | Projektleiter, Vertrieb und Marketing |
| 30 | [Ein Exponential-Projekt planen](ein-exponential-projekt-planen.md) | Projektleiter |
| 40 | [Hosting, Datenbank und Betriebssystem wählen](hosting-datenbank-und-betriebssystem-waehlen.md) | Projektleiter, Administratoren |
| 50 | [Schätzen und Abgrenzen mit Erweiterungen](schaetzen-und-abgrenzen-mit-erweiterungen.md) | Projektleiter, Entwickler |
| 60 | [Workshops zur Inhaltsmodellierung durchführen](workshops-zur-inhaltsmodellierung.md) | Projektleiter, Redakteure, Entwickler |
| 70 | [Multisite- und mehrsprachige Projekte planen](multisite-und-mehrsprachigkeit-planen.md) | Projektleiter, Administratoren |
| 80 | [Checklisten zu Barrierefreiheit, Sicherheit und Compliance](checklisten-barrierefreiheit-sicherheit-und-compliance.md) | Projektleiter, Administratoren |
| 90 | [Release- und Upgrade-Planung](release-und-upgrade-planung.md) | Projektleiter, Administratoren |
| 100 | [Qualitätsschranken und Abnahme](qualitaetsschranken-und-abnahme.md) | Projektleiter, Entwickler |
| 110 | [Risikomanagement für Exponential-Projekte](risikomanagement.md) | Projektleiter |
| 120 | [Mit der Community arbeiten und etwas zurückgeben](mit-der-community-arbeiten.md) | Projektleiter, Entwickler |
| 130 | [Ein Team mit Git führen](ein-team-mit-git-fuehren.md) | Projektleiter, Entwickler |
| 140 | [Projektvorlagen: Projektauftrag, RACI, Checklisten, Livegang und Übergabe](projektvorlagen.md) | Projektleiter |
| 150 | [Ein Projekt messen: Kennzahlen aus den eigenen Logs und dem Audit-Protokoll des Produkts](kennzahlen-aus-logs-und-audit.md) | Projektleiter, Administratoren |
| 200 | [Zusammenarbeiten](zusammenarbeiten.md) | alle |
| 210 | [Eine führende Rolle in der Exponential-Community übernehmen](community-leiter-werden.md) | alle |
| 220 | [Der Lesepfad für Führungskräfte](lesepfad-fuer-fuehrungskraefte.md) | Projektleiter, Vertrieb und Marketing |

Kollegen aus Vertrieb und Marketing haben einen eigenen Bereich, der mit
[Vertrieb und Marketing: hier beginnen](../geschaeft/vertrieb-und-marketing-hier-beginnen.md) beginnt. Zwei seiner Seiten
sind auch für Projektleiter nützlich: [Für wen Exponential gedacht ist](../geschaeft/fuer-wen-exponential-ist.md) und
[Lizenzierung für Käufer erklärt](../geschaeft/lizenzen-fuer-kaeufer.md).

## Die vorhandenen Hilfeseiten, die Sie am häufigsten brauchen

### Für die Arbeit mit Redakteuren

| Seite | Warum ein Projektleiter sie liest |
|---|---|
| [Erste Schritte](../erste-schritte.md) | Was jeder neue Redakteur in den ersten zehn Minuten tut; nutzen Sie sie für die Einarbeitung |
| [Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md) | Die Begriffe, die Ihre Redakteure verwenden: Kopfzeile, Tabs, Spalten, Dashboard |
| [Inhalte erstellen und bearbeiten](../inhalte-bearbeiten.md) | Was das Bearbeiten einer Seite umfasst, für die Schätzung der Inhaltserfassung |
| [Veröffentlichen, Versionen und der Papierkorb](../veroeffentlichen-und-versionen.md) | Entwürfe, Versionen, Verbergen und Wiederherstellen |
| [Sprachen](../sprachen.md) | Wie Übersetzung für Redakteure funktioniert |
| [Der Layout-Editor](../layout-editor.md) | Was Redakteure ohne Entwickler auf einer Seite anordnen können |
| [Bilder, Medien und Uploads](../medien-und-uploads.md) | Uploads, viele auf einmal, und die Medienbibliothek |

### Für die Arbeit mit Administratoren

| Seite | Warum ein Projektleiter sie liest |
|---|---|
| [Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md) | Wer was tun darf; wie man eine Rolle beantragt |
| [Datenbankunterstützung](../datenbankunterstuetzung.md) und [Betriebssystemunterstützung](../betriebssystemunterstuetzung.md) | Die Fakten hinter der Hosting-Entscheidung |
| [Sicherheitshärtung](../sicherheitshaertung.md) | Die Checkliste, die Administratoren vor dem Livegang abarbeiten |
| [Eine Site betreiben](../../anleitungen/betrieb-einer-website.md) | Caches, Cronjobs, Backups und die wöchentliche Checkliste |
| [Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md) | Wie ein Upgrade vorher und nachher geprüft wird |

### Für die Arbeit mit Entwicklern

| Seite | Warum ein Projektleiter sie liest |
|---|---|
| [Mit Git entwickeln](../mit-git-entwickeln.md) | Der empfohlene Weg, Websites mit Exponential zu bauen: Git-Klone mit vollständiger Historie |
| [Erweiterungen](../../anleitungen/extensions.md) | Wie Erweiterungen gefunden, installiert, gebaut und veröffentlicht werden |
| [Templates und Design](../../anleitungen/templates-und-design.md) | Wie das Erscheinungsbild einer Site entsteht |
| [REST API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md) | Was Integrationen nutzen können |
| [Das Glossar](../../glossar.md) | Alle Begriffe an einem Ort |

## Wie die Hilfe selbst funktioniert

Die Hilfe, die Sie gerade lesen, ist die in Git verwaltete Dokumentation des Repositorys, angezeigt in der
Administration ([Das Hilfesystem](../../funktionen/6.0/hilfesystem.md)):

- Das **Hilfe-Dashboard** (`/exphelp/dashboard`) enthält ein Suchfeld, die Seiten für jede Zielgruppe, die
  Kategorien, hervorgehobene Seiten und die neuesten Änderungen.
- Die Schaltfläche **Hilfe** in der Kopfzeile öffnet die Seite, die zum aktuellen Bildschirm gehört.
- Jede Seite lässt sich drucken und als PDF herunterladen (**Diese Seite drucken**, **Als PDF herunterladen**), was
  für Unterlagen des Lenkungsausschusses nützlich ist.
- **Fehler melden** (`/exphelp/report`) erklärt, welcher Tracker zu verwenden ist, und bereitet ein Issue mit den
  Diagnosedaten vor, niemals mit Passwörtern, Schlüsseln oder personenbezogenen Daten.
- Zum Lesen der Hilfe ist die Richtlinie `exphelp/read` nötig. Die Rolle Administrator hat sie, und eine neue
  Installation gibt sie der Rolle Editor ([Das Hilfesystem](../../funktionen/6.0/hilfesystem.md)). Kann ein Teammitglied
  die Hilfe nicht öffnen, ist das die Richtlinie, die es beantragen sollte.

## Grundregeln dieser Seiten

- **Jede Produktaussage hat eine Quelle.** Einen Link auf eine Seite in `doc/` oder einen Dateipfad. Steht auf einer
  Seite „die Quellen sagen dazu nichts“, betrachten Sie das als Frage an Ihr Team oder an 7x, nicht als Lücke, die
  mit Annahmen zu füllen ist.
- **Es werden keine Preise, Stunden oder Service-Level erfunden.** Wo eine Schätzung nötig ist, geben Ihnen die
  Seiten Fragen und Arbeitsblätter, keine Zahlen. Leistungen und Preise von 7x stehen auf der Website von 7x, nicht in
  dieser Hilfe ([Wer ist 7x?](../wer-ist-7x.md#dienstleistungen-und-hosting)).
- **Vorlagen sind Formulare zum Ausfüllen.** Platzhalter sehen so aus: `<project name>`.
- **Die Dokumentation ist frei.** Sie dürfen diese Seiten für Ihr Projekt unter der GNU Free Documentation License 1.3
  kopieren und anpassen ([Freie Dokumentation](../freie-dokumentation.md)).

## Fragen, die vor dem Kick-off beantwortet sein sollten

Übernehmen Sie diese Liste in Ihr Projekttagebuch und füllen Sie sie mit Ihrem Auftraggeber und Ihrer technischen
Leitung aus.

| Frage | Ihre Antwort | Wo die Hilfe die Möglichkeiten erklärt |
|---|---|---|
| Auf welchem Produkt der Familie bauen wir auf? | `<answer>` | [Was Exponential ist](was-exponential-ist.md) |
| Ist das eine neue Site oder ein Umzug von einer älteren Installation? | `<answer>` | [Upgrade](../../anleitungen/aktualisieren.md), [Ein Exponential-Projekt planen](ein-exponential-projekt-planen.md) |
| Wie viele Sites und wie viele Sprachen? | `<answer>` | [Multisite- und mehrsprachige Projekte planen](multisite-und-mehrsprachigkeit-planen.md) |
| Welche Datenbank, welcher Webserver, welches Betriebssystem? | `<answer>` | [Hosting, Datenbank und Betriebssystem wählen](hosting-datenbank-und-betriebssystem-waehlen.md) |
| Welche Erweiterungen brauchen wir, und welche entwickeln wir selbst? | `<answer>` | [Schätzen und Abgrenzen mit Erweiterungen](schaetzen-und-abgrenzen-mit-erweiterungen.md) |
| Wer sind die Redakteure, und wer gibt vor der Veröffentlichung frei? | `<answer>` | [Workflows](../../anleitungen/workflows.md), [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md) |
| Welche Regeln zu Barrierefreiheit, Sicherheit und Datenschutz gelten für uns? | `<answer>` | [Checklisten](checklisten-barrierefreiheit-sicherheit-und-compliance.md) |
| Wer betreut die Site nach dem Livegang, und wie werden Updates eingespielt? | `<answer>` | [Release- und Upgrade-Planung](release-und-upgrade-planung.md) |

## Verwandte Seiten

- [Der Lesepfad für Führungskräfte](lesepfad-fuer-fuehrungskraefte.md)
- [Zusammenarbeiten](zusammenarbeiten.md)
- [Wer ist 7x?](../wer-ist-7x.md)
- [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)
- [Das Hilfesystem](../../funktionen/6.0/hilfesystem.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-see-also-645 -->
**Siehe auch:** [Anwender und Umsteiger](../anwender-umsteiger/einfuehrung-hier-beginnen.md).

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
