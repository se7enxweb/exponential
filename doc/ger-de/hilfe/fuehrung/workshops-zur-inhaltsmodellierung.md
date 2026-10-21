---
title: Workshops zur Inhaltsmodellierung durchführen
audience: project-manager, editor, developer
category: leadership
order: 60
summary: Wie Sie Workshops durchführen, die Ihre Inhalte in Inhaltsklassen, Attribute, einen Inhaltsbaum, Sektionen, Zustände, Sprachen und Rollen übersetzen; Tagesordnungen, Übungen und ein Klassenbogen zum Kopieren.
keywords: Inhaltsmodellierung, Inhaltsmodell, Workshop, Inhaltsklasse, Attribut, Datentyp, Inhaltsbaum, Knoten, Ort, Sektion, Objektzustand, Workflow, Rollen, Taxonomie, Layouts, Redakteur, Entwickler, Projektleiter, content modeling, content model, content class, attribute, datatype, content tree, node, location, section, object state, roles, taxonomy, eztags, layouts, editor, developer, project manager
type: help
---
# Workshops zur Inhaltsmodellierung durchführen

Diese Seite ist für Projektleiter, die die Inhaltsmodellierung moderieren, und für die Redakteure und Entwickler, die
daran teilnehmen. Sie beantwortet: wie Sie die Workshops vorbereiten und durchführen, welche Fragen Sie stellen, wie jede
Antwort einem Konzept von Exponential entspricht (Klasse, Attribut, Datentyp, Knoten, Sektion, Zustand, Sprache, Rolle,
Layout) und was die Workshops liefern müssen, damit Entwickler bauen können und Redakteure ihre Inhalte wiedererkennen.
Die Produktbegriffe stammen aus [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md).

## Inhalt

1. [Warum die Inhaltsmodellierung in Exponential wichtig ist](#warum-die-inhaltsmodellierung-in-exponential-wichtig-ist)
2. [Die Konzepte in einer Tabelle](#die-konzepte-in-einer-tabelle)
3. [Wer teilnimmt](#wer-teilnimmt)
4. [Vorbereitung](#vorbereitung)
5. [Workshop 1: Inhaltsinventur und Inhaltstypen](#workshop-1-inhaltsinventur-und-inhaltstypen)
6. [Workshop 2: Attribute und Datentypen](#workshop-2-attribute-und-datentypen)
7. [Workshop 3: der Baum, Beziehungen und Taxonomie](#workshop-3-der-baum-beziehungen-und-taxonomie)
8. [Workshop 4: Berechtigungen, Stufen und Sprachen](#workshop-4-berechtigungen-stufen-und-sprachen)
9. [Workshop 5: Seiten und Layouts](#workshop-5-seiten-und-layouts)
10. [Der Klassenbogen zum Kopieren](#der-klassenbogen-zum-kopieren)
11. [Nach den Workshops](#nach-den-workshops)
12. [Häufige Fallstricke](#häufige-fallstricke)

## Warum die Inhaltsmodellierung in Exponential wichtig ist

„Als CMS ist sein bemerkenswertestes Merkmal das vollständig anpassbare und erweiterbare Inhaltsmodell“ (README, zitiert
unter [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#exponential-6-der-kernel)). In
Exponential definieren Sie Ihre eigenen Inhaltstypen, statt Ihre Inhalte in feste Seiten und Beiträge zu pressen. Diese
Freiheit ist der Grund, sorgfältig zu modellieren: Jede Klasse, auf die Sie sich einigen, wird zu Formularen, die
Redakteure jeden Tag ausfüllen, zu Templates, die Entwickler schreiben, und zu Berechtigungen, die Administratoren
vergeben.

## Die Konzepte in einer Tabelle

| Konzept | Was es ist | Entschieden in | Lesen |
|---|---|---|---|
| **Inhaltsklasse** | Ein Inhaltstyp: ein Name, ein Bezeichner und eine Liste von Attributen (zum Beispiel Artikel: Titel, Einleitung, Haupttext, Bild) | Workshop 1 | [Inhaltsmodell, Abschnitt 1](../../anleitungen/inhaltsmodell-und-bearbeitung.md) |
| **Klassengruppe** | Ein Ordner von Klassen im Klasseneditor (Content, Users, Media, Setup) | Workshop 1 | [Klassengruppen](../../anleitungen/klassengruppen.md) |
| **Attribut und Datentyp** | Ein Feld einer Klasse und seine Art: eine Zeile, mehrere Zeilen, Rich Text, Bild, Beziehung ... | Workshop 2 | [Inhaltsmodell, Abschnitt 1](../../anleitungen/inhaltsmodell-und-bearbeitung.md) |
| **Inhaltsobjekt** | Ein einzelner Inhalt, eine Instanz einer Klasse, mit Versionen und Sprachen | | ebenda |
| **Knoten, Ort** | Ein Platz eines Objekts im Inhaltsbaum; ein Objekt kann mehrere haben | Workshop 3 | ebenda |
| **Sektion** | Eine Gruppe von Objekten für Berechtigungen, Navigation und Designs; kein Platz im Baum | Workshop 4 | [Sektionen](../../anleitungen/sektionen.md) |
| **Objektzustand** | Wie weit ein Inhalt fortgeschritten ist (Prüfung, rechtliche Freigabe, Sperrfrist, hervorgehoben) | Workshop 4 | [Objektzustände](../../anleitungen/objektzustaende.md) |
| **Workflow** | Schritte wie eine Freigabe, die vor oder nach einem Vorgang ablaufen | Workshop 4 | [Workflows](../../anleitungen/workflows.md) |
| **Inhaltssprache** | Eine Sprache, in der Inhalte verfasst sind; pro Siteaccess angezeigt | Workshop 4 | [Inhaltssprachen](../../anleitungen/inhaltssprachen.md) |
| **Rolle und Richtlinie** | Wer was tun darf, eingeschränkt nach Sektion, Klasse, Teilbaum, Sprache, Zustand | Workshop 4 | [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md) |
| **Layout, Zone, Block, Regel** | Wie eine Seite angeordnet ist und welche Seiten welches Layout bekommen | Workshop 5 | [Der Layout-Editor](../layout-editor.md) |

## Wer teilnimmt

| Person | Warum | Workshops |
|---|---|---|
| Projektleiter | Moderiert, führt das Entscheidungsprotokoll, wacht über den Umfang | alle |
| Leitender Redakteur | Kennt die Inhalte und die tägliche Arbeit | alle |
| Ein oder zwei Redakteure | Werden die Formulare ausfüllen; bemerken, was fehlt | 1, 2, 5 |
| Leitender Entwickler | Weiß, was jede Entscheidung in der Umsetzung kostet; ordnet Antworten Klassen zu | alle |
| Designer | Weiß, welche Felder das Design braucht | 2, 5 |
| Administrator | Rollen, Sektionen, Sprachen, Siteaccesses | 4 |
| Übersetzer oder Verantwortlicher für eine Sprache | Für mehrsprachige Sites | 4 |

Halten Sie die Gruppe so klein, dass sie entscheiden kann. Laden Sie die anderen stattdessen zu einer Durchsicht der
Ergebnisse ein.

## Vorbereitung

- [ ] Eine Demonstrationsinstallation, die jeder öffnen kann, mit einem Site-Paket mit Demo-Inhalten (`sevenx_multisite`
      ist die Voreinstellung von `exp:install`; [4.3 Site-Pakete](../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird)).
      Echte Klassen unter **Setup > Klassen** zu zeigen, macht jedes Konzept greifbar.
- [ ] Eine Inhaltsinventur: eine Liste der heute vorhandenen Inhalte (Seiten, Dokumente, Produkte, Personen,
      Veranstaltungen), mit groben Mengenangaben und einem Verantwortlichen pro Bereich.
- [ ] Zehn echte Beispiele für Inhalte unterschiedlicher Art, ausgedruckt oder auf dem Bildschirm.
- [ ] Die Anforderungsliste aus der Analysephase ([Ein Exponential-Projekt planen](ein-exponential-projekt-planen.md)).
- [ ] Kopien des [Klassenbogens](#der-klassenbogen-zum-kopieren).
- [ ] Ein Entscheidungsprotokoll ([Projektvorlagen](projektvorlagen.md)).

## Workshop 1: Inhaltsinventur und Inhaltstypen

**Ziel.** Eine Liste von Inhaltsklassen mit jeweils einem Satz zu ihrem Zweck.

Tagesordnung:

1. **Das Produkt zeigen** (15 Minuten). Der Entwickler öffnet eine Klasse unter **Setup > Klassen** und legt ein Objekt
   davon an, wie in [Inhaltsmodell, Abschnitte 2 und 3](../../anleitungen/inhaltsmodell-und-bearbeitung.md) beschrieben. Alle
   sehen: Eine Klasse ist ein Formular, und ein Objekt ist ein ausgefülltes Formular.
2. **Die Beispiele sortieren** (30 Minuten). Legen Sie die zehn Beispiele aus. Gruppieren Sie diejenigen, die dieselben
   Felder haben. Jede Gruppe ist eine Kandidatin für eine Klasse.
3. **Die Klassen benennen** (20 Minuten). Ein Name, den Menschen lesen, und ein Bezeichner, den der Code verwendet
   (`article`, `event`). Notieren Sie das Muster für den Objektnamen: welches Feld ein Objekt in Listen benennt (zum
   Beispiel `<title>`).
4. **Gegen die Inventur prüfen** (20 Minuten). Passt jeder Eintrag der Inventur in eine Klasse? Einträge, die in keine
   passen, sind eine neue Klasse oder ein Zeichen dafür, dass zwei Klassen eine sein sollten.
5. **Wiederverwenden** (15 Minuten). Welche Klassen gibt es im Site-Paket bereits? Eine mitgelieferte Klasse
   wiederzuverwenden spart Arbeit an Templates und Übersetzungen.

Ergebnis: die Klassenliste, jede Klasse mit Name, Bezeichner, Zweck, Verantwortlichem und geschätzter Anzahl von
Objekten.

> **Tipp**
> Fragen Sie „Wonach würde ein Redakteur suchen?“ statt „Wie sieht die Seite aus?“. Eine Klasse beschreibt Inhalte,
> nicht ein Seitenlayout; das Layout wird in Workshop 5 entschieden.

## Workshop 2: Attribute und Datentypen

**Ziel.** Für jede Klasse ihre Attribute mit Datentyp, ob sie Pflichtfelder sind und ob sie übersetzt werden.

Die für neue Attribute verfügbaren Datentypen sind die Liste `AvailableDataTypes[]` in `settings/content.ini`, 35
Einträge in der ausgelieferten Liste ([Inhaltsmodell, Abschnitt 1](../../anleitungen/inhaltsmodell-und-bearbeitung.md));
Erweiterungen fügen weitere hinzu. Ordnen Sie ihnen die Worte der Redakteure zu:

| Redakteure sagen | Bezeichner des Datentyps (`settings/content.ini`) |
|---|---|
| ein Titel, ein Name, eine Zeile Text | `ezstring` |
| mehrere Zeilen unformatierter Text | `eztext` |
| formatierter Text mit Überschriften, Links, Bildern | `ezxmltext` (Rich Text, im Online-Editor bearbeitet) |
| ein Datum; Datum und Uhrzeit; eine Uhrzeit | `ezdate`, `ezdatetime`, `eztime` |
| ja oder nein | `ezboolean` |
| eine Zahl | `ezinteger`, `ezfloat` |
| eine Auswahl aus einer Liste | `ezselection` |
| ein Verweis auf ein anderes Objekt; mehrere | `ezobjectrelation`, `ezobjectrelationlist` |
| ein Bild | `ezimage` |
| eine Datei zum Herunterladen; Audio oder Video | `ezbinaryfile`, `ezmedia` |
| eine Webadresse; eine E-Mail-Adresse | `ezurl`, `ezemail` |
| Schlagwörter | `ezkeyword` |
| eine Tabelle von Werten | `ezmatrix` |
| ein Preis | `ezprice`, `ezmultiprice` |
| ein Land | `ezcountry` |
| ein Benutzerkonto | `ezuser` |

Erweiterungen fügen weitere hinzu, zum Beispiel Tags (`eztags`), Kartenstandorte (`ezgmaplocation`), Sternebewertungen
(`ezstarrating`) und Spamschutz für Formulare (`recaptcha`, `hcaptcha`)
([Erweiterungen, Themes und Pakete](../../funktionen/6.0/erweiterungen/README.md)).

Entscheiden Sie für jedes Attribut:

- [ ] **Name und Bezeichner.**
- [ ] **Datentyp.**
- [ ] **Pflichtfeld?** Bildfelder können einen Alternativtext verlangen ([Inhaltsmodell, Abschnitt 9](../../anleitungen/inhaltsmodell-und-bearbeitung.md)); entscheiden Sie es hier, es ist eine Anforderung der Barrierefreiheit.
- [ ] **Übersetzt?** Für eine mehrsprachige Site: Ist der Wert in jeder Sprache gleich (ein Datum, ein Preis) oder wird er pro Sprache verfasst?
- [ ] **Durchsuchbar?** Werden Menschen danach suchen?
- [ ] **Wo angezeigt?** Vollansicht, Listenansicht, beides, nirgends (intern).
- [ ] **Beispielwert** aus den echten Beispielen.

Ergebnis: ein ausgefüllter Klassenbogen pro Klasse.

## Workshop 3: der Baum, Beziehungen und Taxonomie

**Ziel.** Wo die Inhalte liegen, wie Objekte aufeinander verweisen und wie Inhalte klassifiziert werden.

Fragen:

1. **Der Baum.** Zeichnen Sie die obersten zwei oder drei Ebenen des Inhaltsbaums. Welche Klasse darf unter welcher
   angelegt werden? Inhalte, die an mehrere Stellen gehören, bekommen mehrere Orte (ein Objekt, mehrere Knoten), keine
   Kopien.
2. **Beziehungen.** Welche Objekte verweisen auf welche (ein Artikel auf seinen Autor, eine Veranstaltung auf ihren
   Veranstaltungsort)? Jede davon ist ein Attribut für eine Objektbeziehung. Entscheiden Sie, was geschieht, wenn das
   Ziel entfernt wird; die Inhaltsprüfungen listen danach „defekte Beziehungen“ auf
   ([Inhaltsprüfungen](../../funktionen/6.0/inhaltspruefungen.md)).
3. **Taxonomie.** Ist die Klassifizierung frei (Schlagwörter) oder kontrolliert (Tags, die jemand verwaltet)?
   Kontrollierte Tags sind `eztags` ([eztags](../../funktionen/6.0/erweiterungen/eztags.md)); prüfen Sie zuerst die
   Datenbank, denn `eztags` ist unter MongoDB nicht nutzbar ([Grenzen von MongoDB](../datenbankunterstuetzung.md#grenzen-von-mongodb)).
4. **Medien.** Wo liegen Bilder und Dateien: in der Medienbibliothek, unter den Inhalten, die sie verwenden, oder an
   beiden Stellen?
5. **Adressen.** Welche URL soll ein Objekt haben? URL-Aliase folgen dem Baum; für Ausnahmen gibt es globale Aliase und
   Platzhalter ([URL-Aliase](../../anleitungen/urls-und-aliase.md)).

Ergebnis: ein Baumdiagramm (auf Papier genügt), eine Liste der Beziehungen, eine Entscheidung zur Taxonomie.

## Workshop 4: Berechtigungen, Stufen und Sprachen

**Ziel.** Sektionen, Zustände, Workflows, Sprachen und der Rollenplan.

| Frage | Antwort des Produkts | Lesen |
|---|---|---|
| Gibt es Gruppen von Inhalten, die nur bestimmte Personen lesen oder bearbeiten dürfen (Mitgliederbereich, Abteilung im Intranet)? | Eine Sektion pro Gruppe; Rollen nach Sektion eingeschränkt | [Sektionen](../../anleitungen/sektionen.md) |
| Eine neue Installation hat fünf Sektionen (Standard, Users, Media, Setup, Design); welche fügen wir hinzu? | Neue Sektionen; beachten Sie, dass die Rolle Anonymous ab Werk nur Standard liest | [Sektionen, Abschnitt 2](../../anleitungen/sektionen.md) |
| Durchlaufen Inhalte Stufen, mit unterschiedlichen Personen, die in jeder Stufe arbeiten dürfen? | Eine Zustandsgruppe mit Zuständen; Richtlinien nach Zustand eingeschränkt | [Objektzustände](../../anleitungen/objektzustaende.md) |
| Muss jemand vor dem Veröffentlichen freigeben? | Ein Workflow mit einem Freigabe-Ereignis (Approve) und einem Trigger; der Workflow-Cronjob muss laufen | [Workflows](../../anleitungen/workflows.md) |
| Welche Sprachen, und welche ist die Hauptsprache jedes Objekts? | Inhaltssprachen; `SiteLanguageList` pro Siteaccess; „immer verfügbare“ Objekte | [Inhaltssprachen](../../anleitungen/inhaltssprachen.md) |
| Wer darf was tun? | Rollen, die Benutzergruppen zugewiesen sind | [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md) |

> **Hinweis**
> Sektionen, Zustände und der Baum sind drei voneinander unabhängige Arten, Inhalte zu gruppieren. „Sektionen sagen,
> *wohin* ein Inhalt gehört ..., und der Inhaltsbaum sagt, *worunter er liegt*. Zustände sagen, *wie weit* ein Inhalt
> fortgeschritten ist“ ([Objektzustände](../../anleitungen/objektzustaende.md)). Verwenden Sie diejenige, die zur Frage passt,
> nicht alle drei für alles.

Ergebnis: Liste der Sektionen, Zustandsgruppen, Liste der Workflows, Liste der Sprachen, Rollenplan (Rolle, Gruppen,
Richtlinien, Einschränkungen).

## Workshop 5: Seiten und Layouts

**Ziel.** Welche Seiten Redakteure selbst anordnen und welche feste Templates sind.

Mit Exponential Layouts gilt: „Ein Layout ist eine gespeicherte Anordnung von Blöcken in Zonen, und eine Regel
entscheidet, welche Seiten es bekommen“ ([Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#exponential-layouts)).
Im Workshop:

1. Öffnen Sie den Tab **Layouts** der Demonstrationsinstallation und gehen Sie den Abschnitt „In action: the layouts
   editor“ des [Rundgangs durch die Administration](../rundgang-durch-die-administration.md) durch.
2. Listen Sie die Seitentypen auf (Startseite, Landingpages, Einstiegsseiten von Bereichen, Artikelseiten).
3. Für jeden: festes Template oder ein Layout, das Redakteure ändern? Welche Zonen? Welche Blöcke?
4. Welche Blöcke sind auf vielen Seiten gleich (Kopfbereich, Fußbereich, ein Bereich über dem Fuß)? Sie gehören in ein
   **gemeinsames Layout**, mit dem die anderen verknüpft werden ([Der Layout-Editor](../layout-editor.md)).
5. Wer darf Layouts ändern? Der Tab **Layouts** wird nur Rollen angezeigt, die mit Layouts arbeiten dürfen.

Ergebnis: Liste der Seitentypen mit der Layout-Entscheidung, Liste der Zonen und Blöcke, Liste der gemeinsamen Layouts.

## Der Klassenbogen zum Kopieren

```text
Class: <Name>                         identifier: <identifier>        group: <Content | Media | ...>
Purpose: <one sentence>
Owner (editor): <name>                Expected number of objects: <n>
Created below: <parent classes>       May contain: <child classes>
Object name pattern: <<attribute>>    Section: <section>               States: <state group or none>
Approval before publishing: <yes/no, by whom>

| # | Attribute name | Identifier | Datatype       | Required | Translated | Searchable | Shown in        | Example value |
|---|----------------|------------|----------------|----------|------------|------------|-----------------|---------------|
| 1 | Title          | title      | ezstring       | yes      | yes        | yes        | full, list      | <example>     |
| 2 | Intro          | intro      | ezxmltext      | no       | yes        | yes        | full, list      | <example>     |
| 3 | Image          | image      | ezimage        | no       | no         | no         | full            | <file>        |
| 4 | <name>         | <id>       | <datatype>     | <y/n>    | <y/n>      | <y/n>      | <where>         | <example>     |

Open questions: <list>
Decided on: <date>   by: <names>
```

## Nach den Workshops

1. **Schreiben Sie es auf**, in einem einzigen Dokument: Klassenbögen, Baum, Beziehungen, Taxonomie, Sektionen,
   Zustände, Workflows, Sprachen, Rollen, Seitentypen. Versionieren Sie es im Projekt-Repository neben dem Code.
2. **Lassen Sie es prüfen** von Redakteuren, die nicht im Raum waren. Bitten Sie jeden, fünf echte Objekte in das Modell
   einzuordnen.
3. **Bauen Sie einen Prototyp**: Ein Entwickler legt zwei oder drei Klassen auf der Testinstallation an (eine Klasse ist
   in Minuten angelegt, [Inhaltsmodell, Abschnitt 2](../../anleitungen/inhaltsmodell-und-bearbeitung.md)). Redakteure geben echte
   Inhalte darin ein.
4. **Passen Sie an** und frieren Sie das Modell für die Umsetzung ein. Spätere Änderungen laufen über den
   Änderungsprozess aus [Schätzen und Abgrenzen](schaetzen-und-abgrenzen-mit-erweiterungen.md#den-umfang-unter-kontrolle-halten).
5. **Planen Sie Migration und Eingabe** anhand der Klassenbögen ([Planung, Phase 4](ein-exponential-projekt-planen.md#phase-4-inhaltsmigration-und--erfassung)).

## Häufige Fallstricke

| Fallstrick | Warum er schadet | Abhilfe |
|---|---|---|
| Eine Klasse pro Seitendesign | Viele Klassen mit denselben Feldern; die Templates vervielfachen sich | Inhalte modellieren; Seiten mit Layouts oder View-Templates anordnen |
| Eine Klasse „allgemeine Seite“ für alles | Nichts lässt sich auflisten, filtern oder wiederverwenden | Danach aufteilen, wonach Redakteure suchen |
| Kopien statt Orte | Zwei Objekte entwickeln sich auseinander | Ein Objekt mit mehreren Orten |
| Sektionen als Ordner verwendet | Sektionen sind keine Plätze im Baum | Den Baum für die Struktur verwenden, Sektionen für Berechtigungen |
| Sprachen spät entschieden | Übersetzungskennzeichen, Siteaccesses und Rollen ändern sich | Sprachen in Workshop 4 entscheiden |
| Zu viele Sprachen „vorsichtshalber“ hinzugefügt | Jede Sprache ist ein Bit einer begrenzten Maske (höchstens 62), und eine Sprache lässt sich nur entfernen, wenn nichts sie verwendet | Eine Sprache dann hinzufügen, wenn sie gebraucht wird ([Inhaltssprachen](../../anleitungen/inhaltssprachen.md)) |
| Alternativtext optional gelassen | Lücken in der Barrierefreiheit werden erst in der Qualitätssicherung gefunden | In Workshop 2 zum Pflichtfeld machen |

## Verwandte Seiten

- [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md)
- [Sektionen](../../anleitungen/sektionen.md), [Objektzustände](../../anleitungen/objektzustaende.md), [Workflows](../../anleitungen/workflows.md)
- [Der Layout-Editor](../layout-editor.md)
- [Ein Exponential-Projekt planen](ein-exponential-projekt-planen.md)
- [Multisite- und mehrsprachige Projekte planen](multisite-und-mehrsprachigkeit-planen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
