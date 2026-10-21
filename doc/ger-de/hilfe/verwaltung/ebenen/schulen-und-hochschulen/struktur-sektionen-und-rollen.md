---
title: Struktur, Sektionen und Rollen für Schulen und Hochschulen
audience: government, admin, website-creator, project-manager
category: public-sector
order: 40
summary: Ein öffentlicher Baum nach Zielgruppe für die Website einer Bildungseinrichtung, eine Sektion je Fachbereich und für das Intranet und Rollen für die zentrale Kommunikation, die Redaktion der Fachbereiche sowie lesende Beschäftigte und Lernende.
keywords: Inhaltsbaum, Schule, Hochschule, Sektionen, Rollen, Fachbereiche, Sektion für das Intranet, Gruppe der Beschäftigten, Gruppe der Studierenden, Redaktion des Fachbereichs, Teilbaum, Einschränkung nach Sektion, content tree, school, university, sections, roles, departments, intranet section, staff group, student group, department editor, subtree, section limitation
type: help
---
# Struktur, Sektionen und Rollen für Schulen und Hochschulen

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Website-Ersteller einer Schule, Fachhochschule
oder Universität. Sie schlägt einen Baum, Sektionen und Rollen vor, in denen Fachbereiche ihre eigenen Inhalte bearbeiten
und Beschäftigte und Lernende ein Intranet sehen. Die Mechanismen sind unter [Sektionen](../../../../anleitungen/sektionen.md)
und [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md) beschrieben.

## Inhalt

1. [Der öffentliche Baum](#der-öffentliche-baum)
2. [Sektionen](#sektionen)
3. [Benutzergruppen](#benutzergruppen)
4. [Rollen](#rollen)
5. [Prüfungen](#prüfungen)

## Der öffentliche Baum

```text
Home
├── Study with us          admissions, programs, open days, fees
├── Departments            one Department object per department, its pages below
├── Research               (universities) groups, projects, publications
├── Life here              campus, support services, accommodation, clubs
├── News and events
├── About us               governance, policies, reports, jobs, contact
└── Intranet               restricted: staff and students (see below)
```

Zuerst die Zielgruppen (künftige Lernende, aktuelle Mitglieder, die Öffentlichkeit), dann die Fachbereiche. Besucher,
die die Struktur der Einrichtung nicht kennen, finden Studiengänge unter **Study with us**; ein Studiengang hat einen
zweiten Ort unter seinem Fachbereich.

## Sektionen

| Sektion | Enthält | Anonym lesbar |
|---|---|---|
| Standard | zentrale öffentliche Inhalte | ja |
| Eine je Fachbereich (`dept_<name>`) | die öffentlichen Seiten des Fachbereichs | ja |
| `intranet` | Informationen für Beschäftigte und Lernende | nein |
| `intranet_staff` | Informationen nur für Beschäftigte | nein |

Die Sektionen des Intranets stehen nicht in `content/read` der Rolle Anonymous, daher ergeben ihre Inhalte für Besucher
„Zugriff verweigert“ und werden aus den Menüs herausgelassen
([Sektionen, 13.1](../../../../anleitungen/sektionen.md#131-ein-mitgliederbereich)).

## Benutzergruppen

```text
Users
├── Staff
│   ├── <department> editors
│   └── ...
├── Students
├── Central web team
└── Members (public registrations, if any; never staff or students)
```

Konten für Beschäftigte und Lernende werden meist durch Import oder einen an das Verzeichnis der Einrichtung
angebundenen Login-Handler angelegt, was Entwicklungsarbeit ist; manuelle Konten sind nur für kleine Schulen
realistisch. Lernende gehen jedes Jahr: Planen Sie die Deaktivierung ein
([Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md), **Deaktivieren und verschieben**).

## Rollen

| Rolle | Richtlinien | Zugewiesen an, mit Einschränkung |
|---|---|---|
| Intranet lesen | `content/read` | Students und Staff, Sektion = `intranet`; Staff auch `intranet_staff` |
| Redaktion des Fachbereichs | `content/create`, `content/edit`, `content/hide`, `content/versionread`, `content/remove` | die Redaktion jedes Fachbereichs, Sektion = die Sektion des Fachbereichs (und seine Intranetseiten, Teilbaum) |
| Kommunikation | `content/*` eingeschränkt nach Sektion Standard | zentrale Kommunikation |
| Webteam | `content/*`, `section/assign`, `state/assign`, `rss/edit`, `infocollector/read` | das Webteam |
| Administration | `*/*` | Systemadministration |

Eine Freigabe wird oft nur für Seiten zur Zulassung und für Richtlinien verlangt: ein Ereignis *Freigeben*, dessen
**Betroffene Sektionen** auf diese eingeschränkt sind ([Workflows](../../../../anleitungen/workflows.md)).

## Prüfungen

- [ ] Abgemeldet: Seiten der Fachbereiche öffnen sich, Seiten des Intranets nicht, und das Intranet steht nicht im Menü.
- [ ] Angemeldet als Studierende oder Studierender: Seiten des Intranets öffnen sich, Seiten nur für Beschäftigte nicht.
- [ ] Die Redaktion eines Fachbereichs kann die Seiten eines anderen Fachbereichs nicht bearbeiten.
- [ ] **Setup > Sektionen**: Jede Sektion eines Fachbereichs zeigt die Rollenzuweisung ihrer Redaktion.

## Verwandte Seiten

- [Schulen und Hochschulen](README.md)
- [Intranets und Subsites von Fachbereichen](intranets-und-fachbereichs-subsites.md)
- [Berechtigungen für Besucher](../../../../../eng-us/help/creators/website-creators/visitor-permissions.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
