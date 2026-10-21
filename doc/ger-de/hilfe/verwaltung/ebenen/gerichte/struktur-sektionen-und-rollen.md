---
title: Struktur, Sektionen und Rollen für Gerichte
audience: government, admin, website-creator, project-manager
category: public-sector
order: 40
summary: Ein Inhaltsbaum für die Website eines Gerichts, eine eigene Sektion für Entscheidungen in Prüfung und Rollen für die Redaktion der Hinweise, das Team zur Veröffentlichung von Entscheidungen, die Prüfenden und eine diensthabende Person für dringende Hinweise.
keywords: Inhaltsbaum, Gericht, Sektionen, Rollen, Veröffentlichung von Entscheidungen, Prüfsektion, Freigabe, diensthabende Veröffentlichung, dringende Hinweise, Einschränkung nach Zustand, minimale Rechte, content tree, court, sections, roles, decision publishing, review section, approval, duty publisher, urgent notices, state limitation, least privilege
type: help
---
# Struktur, Sektionen und Rollen für Gerichte

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Website-Ersteller der Website eines Gerichts
oder Spruchkörpers. Entscheidungen veröffentlicht ein kleines geschultes Team mit Prüfung; alles andere die
Kommunikation. Die Mechanismen sind unter [Sektionen](../../../../anleitungen/sektionen.md),
[Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md), [Workflows](../../../../anleitungen/workflows.md) und
[Objektzustände](../../../../anleitungen/objektzustaende.md) beschrieben.

## Inhalt

1. [Der Inhaltsbaum](#der-inhaltsbaum)
2. [Sektionen](#sektionen)
3. [Rollen](#rollen)
4. [Prüfung und dringendes Veröffentlichen](#prüfung-und-dringendes-veröffentlichen)
5. [Prüfungen](#prüfungen)

## Der Inhaltsbaum

```text
Home
├── Going to court        guides by situation, help available, costs
├── Courts and locations  Court location objects
├── Hearings              hearing information where published, notices of changes and closures
├── Decisions             Decision objects by year or area of law
├── Rules and forms       rules, practice directions, forms, fees
├── News
└── About the court       jurisdiction, judges' public roles, annual report, complaints, contact
```

## Sektionen

| Sektion | Enthält | Anonym lesbar |
|---|---|---|
| Standard | Hinweise, Standorte, Nachrichten, über das Gericht | ja |
| `decisions` | veröffentlichte Entscheidungen | ja (zu `content/read` von Anonymous hinzufügen) |
| `decisions_review` | Entscheidungen in Vorbereitung | nein |
| `notices` | Änderungen und Schließungen von Verhandlungen | ja |

Eine Entscheidung wird in einem Ordner in `decisions_review` angelegt, dort geprüft und nach der Freigabe in den
öffentlichen Ordner Decisions verschoben; das Verschieben ändert ihre Sektion, wenn die Sektion des Zielordners vererbt
wird (prüfen Sie die Sektion nach dem Verschieben im Reiter **Details** oder weisen Sie sie mit **Inhalt zuweisen** zu)
([Sektionen, 8](../../../../anleitungen/sektionen.md#8-in-welcher-sektion-inhalte-liegen-anlegen-verschieben-und-zuweisen)). Eine
Alternative ist, einen Ordner zu behalten und die Veröffentlichung vom Ereignis *Freigeben* zurückhalten zu lassen; die
eigene Sektion fügt eine zweite Sicherung hinzu, denn selbst eine versehentliche Veröffentlichung ist für Besucher nicht
lesbar.

## Rollen

| Rolle | Richtlinien | Zugewiesen an |
|---|---|---|
| Redaktion Hinweise | `content/create`, `content/edit`, `content/hide`, `content/versionread` eingeschränkt nach Sektion Standard | Kommunikation |
| Vorbereitung Entscheidungen | `content/create`, `content/edit` eingeschränkt nach Sektion `decisions_review`, Klasse Entscheidung und File | das Team zur Veröffentlichung von Entscheidungen |
| Prüfung Entscheidungen | `content/read`, `content/versionread`, `content/diff` eingeschränkt nach Sektion `decisions_review` und `decisions`; `content/move` | Prüfende (Juristinnen und Juristen der Geschäftsstelle oder Mitarbeitende der Richterschaft, wie Ihre Vorschriften es vorsehen) |
| Leitung Veröffentlichung Entscheidungen | `state/assign` (NewState Veröffentlicht, Berichtigt, Zurückgezogen), `content/hide`, `content/remove`, `content/versionremove` eingeschränkt nach Sektion `decisions` | eine oder zwei namentlich benannte Personen |
| Diensthabende Veröffentlichung | `content/create`, `content/edit`, `content/hide` eingeschränkt nach Sektion `notices` | das diensthabende Team für Schließungen |
| Administration | `*/*` | Systemadministration |

Vergeben Sie `content/versionremove` sparsam: Es entfernt Versionen endgültig, was nötig ist, um personenbezogene Daten
aus alten Versionen zu entfernen, muss aber eine bewusste Handlung sein
([Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md)).

## Prüfung und dringendes Veröffentlichen

| Inhalt | Regel |
|---|---|
| Entscheidungen | Ereignis *Freigeben*, **Betroffene Sektionen** `decisions_review` und `decisions`, Freigebende sind die Prüfenden |
| Hinweise | Ereignis *Freigeben* oder keines, wie Ihre Vorschriften es verlangen |
| Hinweise auf Schließungen | keine Freigabe: die Gruppe des diensthabenden Teams unter **Gruppen ausschließen**; das Audit-Protokoll zeichnet jede Veröffentlichung auf |

Denken Sie daran, dass Freigaben vom Workflow-Cronjob fortgesetzt werden; prüfen Sie, dass **Setup > Workflow-Prozesse**
ihn als geplant zeigt.

## Prüfungen

- [ ] Eine Entscheidung in `decisions_review` lässt sich abgemeldet nicht lesen, selbst nachdem sie versehentlich
  veröffentlicht wurde.
- [ ] Die Vorbereitung kann den Zustand *Zurückgezogen* nicht setzen und keine Versionen entfernen.
- [ ] Ein Hinweis der diensthabenden Veröffentlichung auf eine Schließung erscheint sofort; eine Entscheidung der
  Vorbereitung wartet auf Prüfung.
- [ ] Jedes Mitglied des Teams für Entscheidungen hat die zweistufige Anmeldung eingerichtet
  ([Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md)).

## Verwandte Seiten

- [Gerichte](README.md)
- [Schwerpunkte der Anforderungen für Gerichte](schwerpunkte-der-anforderungen.md)
- [Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
