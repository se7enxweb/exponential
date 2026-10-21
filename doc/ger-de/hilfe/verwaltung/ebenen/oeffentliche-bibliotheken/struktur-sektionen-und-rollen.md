---
title: Struktur, Sektionen und Rollen für öffentliche Bibliotheken
audience: government, admin, website-creator, project-manager
category: public-sector
order: 40
summary: Ein Inhaltsbaum für eine Bibliothek oder einen Bibliotheksverbund, ein Teilbaum je Zweigstelle und Rollen, mit denen die Beschäftigten der Zweigstellen ihre eigenen Veranstaltungen und Hinweise veröffentlichen, während das Webteam den Rest einheitlich hält.
keywords: Inhaltsbaum, Bibliothek, Zweigstellen, Sektionen, Rollen, Beschäftigte der Zweigstellen, Redaktion Veranstaltungen, Einschränkung nach Teilbaum, Einschränkung nach Klasse, Veranstaltungskalender, content tree, library, branches, sections, roles, branch staff, events editor, subtree limitation, class limitation, event calendar
type: help
---
# Struktur, Sektionen und Rollen für öffentliche Bibliotheken

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Website-Ersteller einer Bibliothek oder eines
Bibliotheksverbunds. Viele Beschäftigte in vielen Zweigstellen veröffentlichen kleine Einträge; wenige Personen halten
die Website zusammen. Die Mechanismen sind unter [Sektionen](../../../../anleitungen/sektionen.md) und
[Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md) beschrieben.

## Inhalt

1. [Der Inhaltsbaum](#der-inhaltsbaum)
2. [Sektionen oder Teilbäume](#sektionen-oder-teilbäume)
3. [Rollen](#rollen)
4. [Prüfungen](#prüfungen)

## Der Inhaltsbaum

```text
Home
├── Find a book            a link to the catalog search, help with the catalog
├── Events                 Event calendar (all events), with views by branch via tags or relations
├── Branches               one Branch object per branch, with its notices below it
├── Services               joining, borrowing, printing, rooms, home delivery
├── Digital library        Digital resource objects
├── Reading                Recommendations
├── Learning               courses, help sessions
├── News
└── About us               governance, volunteering, jobs, contact
```

Ein zentraler Veranstaltungskalender (Event calendar) hält jede Veranstaltung in einer Liste, sodass Besucher alles auf
einer Seite sehen und nach Zweigstelle filtern. Ein Kalender je Zweigstelle ist die Alternative, wenn Zweigstellen
unabhängig arbeiten; dann liegt der Kalender jeder Zweigstelle unterhalb ihres Objekts Zweigstelle.

## Sektionen oder Teilbäume

| Wahl | Wann | Wie |
|---|---|---|
| Einschränkung nach Teilbaum | Beschäftigte der Zweigstelle bearbeiten die Seite ihrer Zweigstelle und deren Hinweise | die Rolle mit einer Einschränkung **Subtree** auf den Knoten der Zweigstelle zuweisen |
| Einschränkung nach Klasse | Beschäftigte dürfen Veranstaltungen anlegen, sonst nichts im Kalender | `content/create` mit Class = Event und ParentClass = Event calendar |
| Sektion | ein Teil der Website braucht andere Leseregeln (ein Bereich nur für Beschäftigte) | eine Sektion, die Anonymous nicht lesen darf |

Ein Bibliotheksverbund braucht selten eine Sektion je Zweigstelle: Alle Zweigstellen sind öffentlich, und
Einschränkungen nach Teilbaum halten die Beschäftigten bereits bei ihrem Teil.

## Rollen

| Rolle | Richtlinien | Zugewiesen an |
|---|---|---|
| Redaktion Veranstaltungen | `content/create` (Class Event, ParentClass Event calendar), `content/edit` und `content/remove` (Owner = self) | alle Beschäftigten, die Veranstaltungen durchführen |
| Redaktion Zweigstelle | `content/edit`, `content/create` (Klassen Article, File), `content/hide` | die Beschäftigten jeder Zweigstelle, Subtree = der Knoten der Zweigstelle |
| Redaktion Inhalte | Rolle Editor, Sektion Standard | das Web- oder Kommunikationsteam |
| Administration | `*/*` | die Systemadministration |

Die Einschränkung nach Eigentümer (*Self*) lässt Beschäftigte nur die Veranstaltungen bearbeiten und entfernen, die sie
angelegt haben; das Webteam kann alle bearbeiten. Einschränkungen von Richtlinien sind unter
[Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md) beschrieben. Beschäftigte der Zweigstellen arbeiten
oft an gemeinsam genutzten Computern: Zweistufige Anmeldung und Abmelden sind wichtig
([Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md)).

Eine Freigabe ist für Veranstaltungen meist nicht nötig; für Nachrichten und Dienstleistungsseiten lässt sie sich mit
einem Ereignis *Freigeben* ergänzen, das auf diese Sektionen eingeschränkt ist ([Workflows](../../../../anleitungen/workflows.md)).

## Prüfungen

- [ ] Die Redaktion Veranstaltungen kann eine Veranstaltung im Kalender anlegen und nirgendwo sonst.
- [ ] Die Redaktion Veranstaltungen kann die Veranstaltung einer anderen Person nicht bearbeiten.
- [ ] Die Redaktion einer Zweigstelle kann die Öffnungszeiten ihrer Zweigstelle aktualisieren, nicht die einer anderen.
- [ ] Abgemeldet listet der Kalender die neue Veranstaltung (nachdem das Veröffentlichen den Ansichts-Cache aufgefrischt
  hat).

## Verwandte Seiten

- [Öffentliche Bibliotheken](README.md)
- [Schwerpunkte der Anforderungen für öffentliche Bibliotheken](schwerpunkte-der-anforderungen.md)
- [Berechtigungen für Besucher](../../../../../eng-us/help/creators/website-creators/visitor-permissions.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
