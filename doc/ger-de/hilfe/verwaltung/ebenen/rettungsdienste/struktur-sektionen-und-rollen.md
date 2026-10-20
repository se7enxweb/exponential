---
title: Struktur, Sektionen und Rollen für Rettungsdienste
audience: government, admin, website-creator, project-manager
category: public-sector
order: 40
summary: Ein Inhaltsbaum für die Website eines Rettungsdienstes mit den Einsätzen ganz oben, Sektionen, die Einsatzinhalte von geprüften Inhalten trennen, und eine Bereitschaftsrolle für die Veröffentlichung, die nie auf eine Freigabe wartet.
keywords: Inhaltsbaum, Rettungsdienste, Sektionen, Rollen, Bereitschaft, Veröffentlichung von Einsätzen, Ausnahmen von der Freigabe, ausgeschlossene Gruppen, Editor-Siteaccess, zweistufige Anmeldung, content tree, emergency services, sections, roles, on-call, incident publishing, approval exceptions, excluded user groups, editor siteaccess, two-step sign-in
type: help
---
# Struktur, Sektionen und Rollen für Rettungsdienste

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Website-Ersteller der Website eines
Rettungsdienstes. Der Einsatzbereich gehört einem kleinen Bereitschaftsteam, das sofort veröffentlicht; alles andere
folgt der normalen Prüfung. Die Mechanismen sind unter [Sektionen](../../../../anleitungen/sektionen.md),
[Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md) und [Workflows](../../../../anleitungen/workflows.md)
beschrieben.

## Inhalt

1. [Der Inhaltsbaum](#der-inhaltsbaum)
2. [Sektionen](#sektionen)
3. [Rollen](#rollen)
4. [Die Freigabe und ihre Ausnahmen](#die-freigabe-und-ihre-ausnahmen)
5. [Prüfungen](#prüfungen)

## Der Inhaltsbaum

```text
Home                       current incidents at the top; the emergency number on every page
├── Incidents              Incident update objects (ongoing and ended)
├── Be prepared            Preparedness guides
├── Stay safe              prevention advice
├── Services               inspections, safety visits, permits
├── Join us                careers, volunteers
├── News
└── About us               stations, reports, plans, contact
```

## Sektionen

| Sektion | Enthält | Prüfung |
|---|---|---|
| `incidents` | Einsätze | keine; das Audit-Protokoll zeichnet jede Veröffentlichung auf |
| Standard | alles andere | Prüfung durch die Kommunikation, falls gewünscht |

Fügen Sie `incidents` der Richtlinie `content/read` der Rolle Anonymous hinzu.

## Rollen

| Rolle | Richtlinien | Zugewiesen an |
|---|---|---|
| Veröffentlichung Einsätze | `content/create`, `content/edit`, `content/hide`, `content/translate` eingeschränkt nach Sektion `incidents` und Klasse Einsatzmeldung | das Bereitschaftsteam (Einsatzleitende, Pressesprecherinnen und Pressesprecher) |
| Redaktion | Rolle Editor, Sektion Standard | Kommunikation |
| Webteam | `content/*`, `section/assign`, `rss/edit`, `setup/managecache` zum Leeren von Caches | das Webteam |
| Administration | `*/*` | Systemadministration |

Der **Editor-Siteaccess** bietet gelegentlich Veröffentlichenden eine einfachere Oberfläche
([Anmelden im Editor](../../../anmelden-im-editor.md)). Jede Person, die Einsätze veröffentlicht, braucht zu jeder
Stunde ein funktionierendes Konto: eine zweistufige Anmeldung mit einer Methode, die auch außerhalb des Büros
funktioniert ([Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md)), und einen Weg, sie nachts
zurückzusetzen (eine Administratorin oder ein Administrator in Bereitschaft kann das Feld *Zweistufige Anmeldung* des
Benutzers auf *E-mail one-time code* (Einmalcode per E-Mail) setzen).

## Die Freigabe und ihre Ausnahmen

| Inhalt | Regel |
|---|---|
| Einsatzmeldungen | außerhalb jedes Ereignisses *Freigeben* (Sektion `incidents` nicht aufgeführt); eine zweite Person liest die veröffentlichte Meldung innerhalb von Minuten, als Verfahren |
| Alles andere | ein Ereignis *Freigeben* für die Sektion Standard, wenn Ihre Organisation eine Prüfung möchte |

Ein Ereignis *Freigeben*, das versehentlich Einsätze umfasst, würde Meldungen zurückhalten, bis der Workflow-Cronjob
nach einer Freigabe läuft: Testen Sie mit dem Konto einer Person, die Einsätze veröffentlicht, dass eine Meldung sofort
erscheint.

## Prüfungen

- [ ] Eine Person, die Einsätze veröffentlicht und auf einem Telefon angemeldet ist, veröffentlicht eine Meldung; sie
  erscheint abgemeldet innerhalb der Zeit, die Sie festlegen.
- [ ] Wer Einsätze veröffentlicht, kann den Rest der Website nicht ändern.
- [ ] Mindestens zwei Personen je Schicht haben ein funktionierendes Konto.
- [ ] Das Webteam kann die Caches leeren, die unter
  [Warnungen, Verfügbarkeit, Wartungsmodus und statischer Cache](warnungen-verfuegbarkeit-wartung-und-statischer-cache.md)
  beschrieben sind.

## Verwandte Seiten

- [Rettungsdienste](README.md)
- [Schwerpunkte der Anforderungen für Rettungsdienste](schwerpunkte-der-anforderungen.md)
- [Caches: was jeder enthält und wie man ihn leert](../../../../anleitungen/caches.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
