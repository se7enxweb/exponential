---
title: Struktur, Sektionen und Rollen für Behörden
audience: government, admin, website-creator, project-manager
category: public-sector
order: 40
summary: Ein Inhaltsbaum nach Zielgruppen für die Website einer Behörde, Sektionen, die Leitfäden, Durchsetzung und Inhalte über die Organisation trennen, und Rollen für die Fachverantwortlichen, die rechtliche Prüfung, die Kommunikation und das Webteam.
keywords: Inhaltsbaum, Behörde, Regulierungsbehörde, Sektionen, Rollen, rechtliche Prüfung, Freigabe-Workflow, Verantwortliche für Leitfäden, Durchsetzung, Zuweisung von Objektzuständen, content tree, agency, regulator, sections, roles, legal review, approval workflow, guidance owners, enforcement, object state assignment, NewState
type: help
---
# Struktur, Sektionen und Rollen für Behörden

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Website-Ersteller einer Regulierungsbehörde,
Aufsichtsbehörde oder nachgeordneten Behörde. Sie schlägt einen Baum, Sektionen und Rollen vor, in denen Leitfäden die
rechtliche Prüfung durchlaufen und ihr Status nur von denjenigen gesetzt wird, die das dürfen. Die Mechanismen sind
unter [Sektionen](../../../../anleitungen/sektionen.md), [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md),
[Workflows](../../../../anleitungen/workflows.md) und [Objektzustände](../../../../anleitungen/objektzustaende.md) beschrieben.

## Inhalt

1. [Der Inhaltsbaum](#der-inhaltsbaum)
2. [Sektionen](#sektionen)
3. [Rollen](#rollen)
4. [Prüfung vor dem Veröffentlichen](#prüfung-vor-dem-veröffentlichen)
5. [Prüfungen](#prüfungen)

## Der Inhaltsbaum

```text
Home
├── For the public            what we do for you, how to complain, warnings
├── For <regulated parties>   guidance, how to comply, forms, deadlines (one folder per audience if needed)
├── Guidance library          every Guidance object, by topic (the second location of guidance listed above)
├── Decisions and enforcement Enforcement notices by year
├── Programs                  Program objects
├── Publications and data     reports, statistics, data exports
├── Consultations
├── News and warnings
└── About us                  board, annual report, budget, jobs, contact
```

Ein Leitfaden-Objekt liegt einmal in der Leitfadenbibliothek (Guidance library) und erhält unter jedem Zielgruppenordner,
zu dem es gehört, einen zweiten Ort, sodass es eine kanonische Adresse hat und dort erscheint, wo Menschen suchen.

## Sektionen

| Sektion | Enthält | Warum getrennt |
|---|---|---|
| `guidance` | Leitfadenbibliothek und Zielgruppenordner | die rechtliche Prüfung gilt |
| `enforcement` | Entscheidungen und Durchsetzung | ein kleines, geschultes Team; Sorgfalt bei personenbezogenen Daten |
| `corporate` | Über uns, Publikationen | Kommunikationsteam |
| Standard | Nachrichten, der Rest | |
| `staging` | vorbereitete Veröffentlichungen von Leitfadenpaketen | unsichtbar bis zur Freigabe |

Fügen Sie die öffentlichen Sektionen der Richtlinie `content/read` der Rolle Anonymous hinzu.

## Rollen

| Rolle | Wichtige Richtlinien | Zugewiesen an |
|---|---|---|
| Autorin oder Autor von Leitfäden | `content/create`, `content/edit`, `content/versionread` eingeschränkt nach Sektion `guidance` | Fachreferate |
| Rechtliche Prüfung | `content/read`, `content/versionread`, `content/diff` eingeschränkt nach Sektion `guidance` und `enforcement` | das Rechtsteam (Freigebende) |
| Statusverantwortliche | `state/assign` mit einer Einschränkung `StateGroup_guidance_status` und `NewState` *Ersetzt*, *Zurückgezogen* | die Leitung der Verantwortlichen für Leitfäden |
| Veröffentlichung Durchsetzung | `content/create`, `content/edit`, `content/hide` eingeschränkt nach Sektion `enforcement` | das Durchsetzungsteam |
| Kommunikation | `content/*` eingeschränkt nach Sektion Standard und `corporate` | Kommunikation |
| Webteam | `content/*`, `section/assign`, `state/assign`, `rss/edit`, `infocollector/read` | das Webteam |

`content/diff` erlaubt der Prüfung, auf der Versionsseite **Vergleichen** zu verwenden
([Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md)). Die Einschränkung `NewState` von `state/assign`
entscheidet, welche Zustände ein Benutzer setzen darf
([Objektzustände, 8](../../../../anleitungen/objektzustaende.md#8-zustände-in-rollen-und-richtlinien)).

## Prüfung vor dem Veröffentlichen

Ein Workflow auf **Vor dem Veröffentlichen von Inhalten** mit einem Ereignis *Freigeben*:

| Feld | Wert |
|---|---|
| Betroffene Sektionen | `guidance`, `enforcement` |
| Betroffene Versionen | *Alle Versionen* |
| Gruppen, die Content freigeben | Rechtliche Prüfung |
| Gruppen ausschließen | Webteam (für dringende Korrekturen, mit geführtem Nachweis) |

Die Prüfenden öffnen die Anfrage unter **Mein Konto > Zusammenarbeit** (`/collaboration/view/summary`), lesen die
Vorschau und drücken **Freigeben** oder **Ablehnen** mit einem Kommentar. Der Workflow-Cronjob veröffentlicht
freigegebene Versionen; läuft er nicht, erscheint nichts ([Workflows](../../../../anleitungen/workflows.md)).

## Prüfungen

- [ ] Die Änderung einer Autorin oder eines Autors von Leitfäden wartet auf Freigabe; die rechtliche Prüfung sieht sie im
  Posteingang der Zusammenarbeit.
- [ ] Eine prüfende Person kann die ausstehende Version mit der veröffentlichten vergleichen.
- [ ] Nur die Statusverantwortlichen können *Zurückgezogen* setzen; andere sehen die Zustandsauswahl deaktiviert oder
  ohne diesen Zustand.
- [ ] Abgemeldet lässt sich eine vorbereitete Veröffentlichung in `staging` nicht lesen.

## Verwandte Seiten

- [Behörden](README.md)
- [Schwerpunkte der Anforderungen für Behörden](schwerpunkte-der-anforderungen.md)
- [Entwürfe und ausstehende Objekte](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
