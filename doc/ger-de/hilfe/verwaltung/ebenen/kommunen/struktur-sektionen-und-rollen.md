---
title: Struktur, Sektionen und Rollen für Kommunen
audience: government, admin, website-creator, project-manager
category: public-sector
order: 40
summary: Ein Inhaltsbaum nach Anliegen für die Website einer Kommune, wenige Sektionen für Fachbereiche und den Rat und Rollen, bemessen für ein kleines Team mit gelegentlichen Redakteurinnen und Redakteuren in vielen Fachbereichen.
keywords: Inhaltsbaum, Kommune, Rat, Sektionen, Rollen, kleines Team, gelegentliche Redaktion, Editor-Siteaccess, Fachbereiche, Freigabe, Einschränkung nach Sektion, content tree, municipal, council, sections, roles, small team, occasional editors, editor siteaccess, departments, approval, section limitation
type: help
---
# Struktur, Sektionen und Rollen für Kommunen

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Website-Ersteller, die die Website einer
Stadt, Gemeinde oder eines Rats einrichten. Kleine Teams und viele gelegentliche Redakteurinnen und Redakteure prägen
die Entscheidungen. Die Mechanismen sind unter [Sektionen](../../../../anleitungen/sektionen.md),
[Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md) und [Workflows](../../../../anleitungen/workflows.md)
dokumentiert.

## Inhalt

1. [Ein Baum nach Anliegen](#ein-baum-nach-anliegen)
2. [Sektionen](#sektionen)
3. [Rollen für ein kleines Team](#rollen-für-ein-kleines-team)
4. [Gelegentliche Redakteurinnen und Redakteure](#gelegentliche-redakteurinnen-und-redakteure)
5. [Prüfungen](#prüfungen)

## Ein Baum nach Anliegen

```text
Home
├── Waste and recycling
├── Roads, parking and transport
├── Housing and planning
├── Taxes and payments
├── Births, marriages and deaths
├── Health and social care
├── Schools and childcare
├── Leisure, culture and libraries
├── Business and licenses
├── Council and democracy        members, meetings, decisions, elections
├── News and notices             news, public notices, alerts
├── Events                       an Event calendar
└── Contact us                   contact points
```

Jeder oberste Ordner ist ein Leistungsbereich, benannt so, wie die Einwohnerschaft spricht, nicht nach dem Fachbereich.
Dienstleistungen, die in zwei Bereiche gehören, erhalten einen zweiten Ort. Halten Sie die Tiefe, wo möglich, bei drei
Ebenen; das Menü zeigt den Baum ([Menüs und Navigation](../../../../../eng-us/help/creators/website-creators/menus-and-navigation.md)).

## Sektionen

Wenige Sektionen genügen:

| Sektion | Enthält | Anonym lesbar |
|---|---|---|
| Standard | die meisten Inhalte | ja |
| `council` | Council and democracy | ja (zu `content/read` von Anonymous hinzufügen) |
| Eine je Fachbereich, der seine eigenen Seiten bearbeitet | sein Leistungsbereich | ja |
| `staging` | vorbereitete Seiten | nein |

Legen Sie eine Sektion für einen Fachbereich nur an, wenn er eine eigene Redaktion hat. Andernfalls bearbeitet das
Webteam seine Seiten in Standard.

## Rollen für ein kleines Team

| Rolle | Richtlinien | Zugewiesen an |
|---|---|---|
| Webteam (Kopie von Editor, weiter gefasst) | `content/*` außer `cleantrash`; `section/assign`; `rss/edit`; `infocollector/read` | die ein oder zwei Webverantwortlichen |
| Redaktion des Fachbereichs | `content/read`; `content/create`, `content/edit`, `content/hide`, `content/versionread` eingeschränkt nach Sektion | die Gruppe jedes Fachbereichs, Sektion = seine Sektion |
| Ratsbüro | `content/create`, `content/edit` eingeschränkt nach Sektion `council`; `content/remove` für Entwürfe | die Gruppe des Sitzungsdienstes |
| Administration | `*/*` | die Systemadministration (oft ein Lieferant) |

Freigabe: Ein kleines Team gibt oft alles selbst frei. Ein nützlicher Mittelweg ist ein Ereignis *Freigeben* nur für
die Redaktion der Fachbereiche, mit dem Webteam als Freigebenden und dem Webteam und dem Ratsbüro unter **Gruppen
ausschließen** ([Workflows](../../../../anleitungen/workflows.md)). Denken Sie an den Workflow-Cronjob, sonst erscheinen
freigegebene Seiten nie.

## Gelegentliche Redakteurinnen und Redakteure

Viele Fachbereiche bearbeiten ein paar Mal im Monat. Der **Editor-Siteaccess** gibt ihnen eine einfachere Oberfläche zum
Bearbeiten von Inhalten ([Anmelden im Editor](../../../anmelden-im-editor.md),
[Einrichtungsassistent und Editor-Siteaccess](../../../../../eng-us/features/6.0/setup-wizard-and-editor-siteaccess.md)). Helfen
Sie ihnen mit:

- einer Anleitung auf einer Seite je Aufgabe ([Aufgaben zum Veröffentlichen](../../../../../eng-us/help/government/tasks/publishing/README.md));
- **Meine Entwürfe**, um unfertige Arbeit zu finden
  ([Entwürfe und ausstehende Objekte](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md));
- zweistufiger Anmeldung, weil das Passwort gelegentlicher Redakteurinnen und Redakteure am ehesten wiederverwendet ist
  ([Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md)).

## Prüfungen

- [ ] Die Redaktion eines Fachbereichs kann in ihrem Bereich anlegen und bearbeiten, nicht anderswo.
- [ ] Die Seite der Redaktion eines Fachbereichs wartet auf Freigabe; die des Webteams nicht.
- [ ] Abgemeldet öffnet sich jeder oberste Ordner.
- [ ] **Benutzer > Benutzerkonten prüfen**, Filter *Nie angemeldet*: keine verwaisten Redaktionskonten.

## Verwandte Seiten

- [Kommunen](README.md)
- [Schwerpunkte der Anforderungen für Kommunen](schwerpunkte-der-anforderungen.md)
- [Den Inhaltsbaum strukturieren](../../../../../eng-us/help/creators/website-creators/structuring-the-content-tree.md)
- [An die Redaktion übergeben](../../../../../eng-us/help/creators/website-creators/handing-over-to-editors.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
