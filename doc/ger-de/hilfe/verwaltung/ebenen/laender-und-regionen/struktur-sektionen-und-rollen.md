---
title: Struktur, Sektionen und Rollen für Länder und Regionen
audience: government, admin, website-creator, project-manager
category: public-sector
order: 40
summary: Ein Inhaltsbaum nach Thema und Ort für die Website eines Landes oder einer Region, Sektionen für Fachbereiche, das Sekretariat der Volksvertretung und eingeschränktes kommunales Material sowie Rollen für die Redaktion der Fachbereiche, das Sekretariat und kommunale Lesende.
keywords: Inhaltsbaum, Sektionen, Rollen, Region, Land, Fachbereiche, Sekretariat der Volksvertretung, eingeschränkter Bereich, kommunale Benutzer, Einschränkung nach Sektion, Einschränkung nach Teilbaum, content tree, sections, roles, region, state, departments, assembly secretariat, restricted area, municipal users, section limitation, subtree limitation
type: help
---
# Struktur, Sektionen und Rollen für Länder und Regionen

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Website-Ersteller, die Baum, Sektionen und
Rollen der Website eines Landes oder einer Region einrichten. Die Mechanismen sind die aus
[Sektionen](../../../../anleitungen/sektionen.md) und [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md).

## Inhalt

1. [Der Inhaltsbaum](#der-inhaltsbaum)
2. [Sektionen](#sektionen)
3. [Rollen und Gruppen](#rollen-und-gruppen)
4. [Das Sekretariat der Volksvertretung](#das-sekretariat-der-volksvertretung)
5. [Prüfungen](#prüfungen)

## Der Inhaltsbaum

```text
Home
├── Living and working      services by theme (transport, environment, health ...)
├── Business and funding    funding programs, permits, procurement
├── Places                  areas, offices, sites (folders tagged with the area)
├── The assembly            members, committees, meetings, decisions
├── Plans and strategies    Plan objects
├── News                    news, road works and disruptions
├── For municipalities      guidance and toolkits (public or restricted)
└── About us                organization, budget, jobs, contact
```

Themen, nicht Fachbereiche, stehen oben: Besucher wissen nicht, welcher Fachbereich Buszuschüsse verwaltet. Fachbereiche
verantworten Inhalte über Sektionen. Baustellen und Störungen liegen in einem eigenen Ordner, damit sie einen eigenen
RSS-Feed und eine eigene Ablaufregel haben können.

## Sektionen

| Sektion | Enthält | Anonym lesbar |
|---|---|---|
| Standard | alles Öffentliche | ja (mitgeliefert) |
| Eine je Fachbereich, zum Beispiel `dept_mobility` | die Seiten des Fachbereichs | ja, zu `content/read` von Anonymous hinzufügen |
| `assembly` | Sitzungen, Tagesordnungen, Niederschriften, Beschlüsse | ja |
| `municipal_restricted` | Material nur für kommunale Beschäftigte | nein |
| `staging` | für einen Start vorbereitete Seiten | nein |

Eine Sektion, die Anonymous nicht lesen darf, lässt den Inhalt für Besucher „Zugriff verweigert“ ergeben und aus den
Menüs verschwinden. Prüfen Sie nach dem Anlegen des Baums die Sektion jedes obersten Ordners in seinem Reiter
**Details**.

## Rollen und Gruppen

| Rolle | Wichtige Richtlinien | Zugewiesen an, mit Einschränkung |
|---|---|---|
| Redaktion des Fachbereichs (Kopie von Editor) | `content/read`, `content/create`, `content/edit`, `content/hide`, `content/versionread` | die Gruppe jedes Fachbereichs, Einschränkung Sektion = dieser Fachbereich |
| Redaktion Störungen | `content/create`, `content/edit`, `content/remove` | das Verkehrsteam, Einschränkung Teilbaum = der Ordner der Störungen |
| Sekretariat | `content/create`, `content/edit` für die Klassen Beschluss, File, Event, Folder | das Sekretariat der Volksvertretung, Sektion = `assembly` |
| Kommunale Lesende | `content/read` | die Gruppe Kommunale Benutzer, Sektion = `municipal_restricted` |
| Webteam | `content/*`, `section/assign`, `state/assign`, `rss/edit` | das Webteam |

Eine Einschränkung nach Teilbaum folgt dem Baum; eine Einschränkung nach Sektion folgt der Sektionszuordnung. Verwenden
Sie Teilbaum für ein kleines Team mit einem festen Ordner, Sektion für einen Fachbereich, dessen Seiten über mehrere
Themen verteilt sind.

Konten kommunaler Beschäftigter legen Ihre Administratorinnen und Administratoren an (oder eine Registrierung in eine
Gruppe, die Sie freigeben), und sie werden in die Gruppe Kommunale Benutzer gesetzt. Lassen Sie öffentliche
Registrierungen nie Benutzer dorthin setzen; Registrierungen gehen nach `[UserSettings] DefaultUserPlacement`
([Registrierung von Mitgliedern und Mitgliederbereiche](../../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md)).

## Das Sekretariat der Volksvertretung

Das Sekretariat veröffentlicht die amtliche Aufzeichnung. Zwei Regeln schützen sie:

- **Keine Verzögerung durch Freigabe für förmliche Dokumente.** Schließen Sie die Gruppe des Sekretariats im Ereignis
  *Freigeben* aus (**Gruppen ausschließen**), weil die Dokumente bereits von der Volksvertretung beschlossen wurden.
- **Keine stillschweigenden Änderungen.** Berichtigungen veröffentlichter Beschlüsse erfolgen als neue Version mit einem
  Hinweis in der Zusammenfassung; die frühere Version bleibt im Versionsverlauf
  ([Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md)). Erwägen Sie eine Gruppe von Objektzuständen
  „Status der Aufzeichnung“ (aktuell, berichtigt, aufgehoben), die auf der Seite angezeigt wird
  ([Objektzustände](../../../../anleitungen/objektzustaende.md)).

## Prüfungen

- [ ] Die Redaktion eines Fachbereichs kann die Seite eines anderen Fachbereichs nicht bearbeiten.
- [ ] Abgemeldet ergibt eine Seite in `municipal_restricted` „Zugriff verweigert“; angemeldet als kommunaler Benutzer
  öffnet sie sich.
- [ ] Das Sekretariat veröffentlicht einen Beschluss, ohne auf eine Freigabe zu warten; die Seite einer Redaktion eines
  Fachbereichs wartet.
- [ ] Jede Sektion hat einen Bezeichner (**Setup > Sektionen** kennzeichnet Sektionen ohne einen als
  prüfungsbedürftig).

## Verwandte Seiten

- [Länder und Regionen](README.md)
- [Schwerpunkte der Anforderungen für Länder und Regionen](schwerpunkte-der-anforderungen.md)
- [Berechtigungen für Besucher](../../../../../eng-us/help/creators/website-creators/visitor-permissions.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
