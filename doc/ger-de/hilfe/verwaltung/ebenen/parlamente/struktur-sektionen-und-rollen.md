---
title: Struktur, Sektionen und Rollen für Parlamente
audience: government, admin, website-creator, project-manager
category: public-sector
order: 40
summary: Ein Inhaltsbaum für die Website eines Parlaments, gegliedert nach Geschäften und Mitgliedern, Sektionen für die amtliche Aufzeichnung und für die Kommunikation und Rollen für die Parlamentsverwaltung, die Ausschussbüros, die Kommunikation und das Webteam.
keywords: Inhaltsbaum, Parlament, Sektionen, Rollen, amtliche Aufzeichnung, Parlamentsverwaltung, Ausschussbüros, Kommunikation, Seiten der Mitglieder, Prüfung, Berichtigung, content tree, parliament, sections, roles, official record, clerks, committee staff, communications, member pages, review, correction
type: help
---
# Struktur, Sektionen und Rollen für Parlamente

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Website-Ersteller der Website eines
Parlaments oder einer gesetzgebenden Versammlung. Die amtliche Aufzeichnung (Tagesordnungen, Gesetzentwürfe,
Abstimmungen, Protokolle) veröffentlichen die Büros der Parlamentsverwaltung; alles andere veröffentlicht die
Kommunikation. Die Mechanismen sind unter [Sektionen](../../../../anleitungen/sektionen.md),
[Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md) und [Workflows](../../../../anleitungen/workflows.md)
beschrieben.

## Inhalt

1. [Der Inhaltsbaum](#der-inhaltsbaum)
2. [Sektionen](#sektionen)
3. [Rollen](#rollen)
4. [Die amtliche Aufzeichnung und Berichtigungen](#die-amtliche-aufzeichnung-und-berichtigungen)
5. [Prüfungen](#prüfungen)

## Der Inhaltsbaum

```text
Home
├── Members              current members; former members (with end dates)
├── Business             sittings by year and month, agendas, transcripts
│   ├── Bills            Bill objects by session
│   └── Votes            Vote records by year
├── Committees           one folder per committee: members, inquiries, reports
├── Petitions and questions
├── Visit and learn      visiting, education, how parliament works
├── News
└── About                administration, budget, jobs, contact
```

## Sektionen

| Sektion | Enthält | Wer bearbeitet |
|---|---|---|
| `record` | Business, Bills, Votes | die Büros der Parlamentsverwaltung |
| `committees` | Committees | die Ausschussbüros |
| `members` | Members | der Dienst für die Mitglieder |
| Standard | Besuch und politische Bildung, Nachrichten, über uns | Kommunikation |

Alle vier sind öffentlich: Fügen Sie sie der Richtlinie `content/read` der Rolle Anonymous hinzu.

## Rollen

| Rolle | Richtlinien | Zugewiesen an |
|---|---|---|
| Parlamentsverwaltung | `content/create`, `content/edit`, `content/versionread` eingeschränkt nach Sektion `record`; Klassen Sitzung, Gesetzentwurf, Abstimmungsergebnis, File | die Büros der Parlamentsverwaltung |
| Ausschussbüro | `content/create`, `content/edit`, `content/hide` eingeschränkt nach Sektion `committees` | die Ausschusssekretariate |
| Dienst für die Mitglieder | `content/create`, `content/edit` eingeschränkt nach Sektion `members` | das Büro des Dienstes für die Mitglieder |
| Kommunikation | `content/*` eingeschränkt nach Sektion Standard | Kommunikation |
| Berichtigungen der Aufzeichnung | `content/versionremove`, `content/remove` eingeschränkt nach Sektion `record` | eine oder zwei leitende Personen der Parlamentsverwaltung |
| Webteam | `content/*`, `section/assign`, `rss/edit` | das Webteam |

Die Mitglieder bearbeiten die Website des Parlaments meist nicht. Dürfen Mitglieder Teile ihrer eigenen Seite
aktualisieren, geben Sie ihnen `content/edit` mit einer Einschränkung nach Eigentümer nur auf ihr eigenes Objekt, und
halten Sie die Felder, die amtliche Rollen zeigen, an einer Stelle, die sie nicht bearbeiten können.

## Die amtliche Aufzeichnung und Berichtigungen

| Regel | Wie |
|---|---|
| Einträge der Aufzeichnung werden von der Parlamentsverwaltung ohne Verzögerung durch eine Freigabe veröffentlicht | kein Ereignis *Freigeben* für die Sektion `record`, oder die Gruppe der Parlamentsverwaltung unter **Gruppen ausschließen** |
| Berichtigungen hinterlassen eine Spur | eine neue Version mit einem Berichtigungshinweis; die frühere Version bleibt im Versionsverlauf |
| Entfernen von Versionen nur aus rechtlichen Gründen (zum Beispiel rechtswidrig veröffentlichte personenbezogene Daten) | die Rolle Berichtigungen der Aufzeichnung, verwendet nach einem schriftlichen Verfahren |
| Jede Handlung zuordenbar | das Audit-Protokoll, standardmäßig an ([Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md)) |

Inhalte der Kommunikation können ein Ereignis *Freigeben* durchlaufen, das auf die Sektion Standard eingeschränkt ist.

## Prüfungen

- [ ] Die Parlamentsverwaltung kann ein Abstimmungsergebnis sofort veröffentlichen; die Redaktion der Kommunikation kann
  es nicht bearbeiten.
- [ ] Ausschussbüros können nur die Seiten ihres Ausschusses bearbeiten.
- [ ] Die Seite eines ehemaligen Mitglieds öffnet sich weiterhin und listet die mit ihm verknüpften Abstimmungen auf.
- [ ] **Setup > Audit** ordnet jeden Eintrag der Aufzeichnung der Person der Parlamentsverwaltung zu, die ihn
  veröffentlicht hat.

## Verwandte Seiten

- [Parlamente](README.md)
- [Schwerpunkte der Anforderungen für Parlamente](schwerpunkte-der-anforderungen.md)
- [Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
