---
title: Vereine und kleine Gruppen
audience: businesses, editor, project-manager
category: business-solutions
order: 1
summary: Die Einstiegsseite des Bereichs Vereine - was Clubs, Gesellschaften, Anwendergruppen, Elternvereine und andere ehrenamtlich geführte Gruppen mit Exponential betreiben können, die Seiten des Bereichs und die Reihenfolge, in der Sie sie lesen.
keywords: Verein, Club, Gesellschaft, kleine Gruppe, ehrenamtlich geführt, Anwendergruppe, Gemeinschaftsgruppe, Veranstaltungen, Neuigkeiten, ehrenamtliche Redakteure, Übergabe, association, club, society, small group, volunteer-run, user group, community group, events, news, volunteer editors, handover
type: help
---
# Vereine und kleine Gruppen

Dieser Bereich ist für Clubs, Gesellschaften, Anwendergruppen, Eltern- und Nachbarschaftsvereine und jede Gruppe, die
überwiegend von Ehrenamtlichen getragen wird. Solche Gruppen teilen drei Probleme: Die Redakteure wechseln alle ein bis
zwei Jahre, niemand wird für die Pflege der Website bezahlt, und trotzdem muss die Website die Neuigkeiten,
Veranstaltungen und Formulare der Gruppe zuverlässig tragen. Die Seiten erklären, wie Sie Exponential so einrichten,
dass ein wechselndes Team von Ehrenamtlichen es betreiben kann, und wie Sie es sauber übergeben.

## Inhalt

1. [Was eine kleine Gruppe typischerweise braucht](#was-eine-kleine-gruppe-typischerweise-braucht)
2. [Die Seiten dieses Bereichs](#die-seiten-dieses-bereichs)
3. [Empfohlene Lesereihenfolge](#empfohlene-lesereihenfolge)
4. [Benachbarte Bereiche](#benachbarte-bereiche)

## Was eine kleine Gruppe typischerweise braucht

| Bedarf | Was es in Exponential gibt | Seite |
|---|---|---|
| Mehrere Ehrenamtliche bearbeiten sicher | Persönliche Konten, auf einen Teilbaum oder eine Sektion beschränkte Rollen, Versionen jeder Änderung | [Gemeinsam bearbeiten mit Ehrenamtlichen](gemeinsam-bearbeiten-mit-ehrenamtlichen.md) |
| Neuigkeiten und ein Veranstaltungskalender | Die `ezwebin`-Klassen `article`, `event`, `event_calendar`, `blog` und Feeds | [Veranstaltungen und Neuigkeiten für Gruppen](veranstaltungen-und-neuigkeiten-fuer-gruppen.md) |
| Kontakt, Rückmeldungen, Umfragen, Anmeldungen | Formulare des Informationssammlers mit den Typen form, feedback und poll | [Formulare und Rückmeldungen von Mitgliedern](formulare-und-rueckmeldungen-von-mitgliedern.md) |
| Übernahme, wenn Ehrenamtliche gehen | Kontoprüfungen, Eigentümerwechsel, Zurücksetzen des Passworts, Audit-Trail | [Übergabe bei Wechsel der Ehrenamtlichen](uebergabe-bei-wechsel-der-ehrenamtlichen.md) |
| Geringe Betriebskosten | Keine Lizenzgebühr; SQLite als Standarddatenbank | [Betrieb mit kleinem Budget](../gemeinnuetzige/betrieb-mit-kleinem-budget.md) |

Die Website-Oberfläche `ezwebin`, die diese Klassen mitbringt (`extension/ezwebin/packages/`), und die Erweiterung
`ezownerchange`, die bei der Übergabe verwendet wird, stehen beide im `require`-Block der `composer.json`.

> **Hinweis**
> Größere Mitgliederorganisationen mit Registrierung, Bereichen nur für Mitglieder und Kommunikation mit Mitgliedern
> sollten auch den Bereich [Mitglieder](../mitglieder/README.md) lesen. Wohltätige Organisationen und Stiftungen finden
> Spenden und Kampagnen unter [Gemeinnützige Organisationen](../gemeinnuetzige/README.md).

## Die Seiten dieses Bereichs

| Seite | Welche Frage sie beantwortet |
|---|---|
| [Website-Planung für kleine Gruppen](website-planung-fuer-kleine-gruppen.md) | Wofür die Website da ist, die wenigen Seiten, die eine Gruppe braucht, wer was tut, und ein Planungsarbeitsblatt |
| [Gemeinsam bearbeiten mit Ehrenamtlichen](gemeinsam-bearbeiten-mit-ehrenamtlichen.md) | Konten, einfache Rollen, Freigabe, Versionen, Hausregeln |
| [Veranstaltungen und Neuigkeiten für Gruppen](veranstaltungen-und-neuigkeiten-fuer-gruppen.md) | Neuigkeiten, ein Veranstaltungskalender, Feeds, Benachrichtigungen und ein Newsletter |
| [Formulare und Rückmeldungen von Mitgliedern](formulare-und-rueckmeldungen-von-mitgliedern.md) | Kontakt- und Rückmeldeformulare, Umfragen, Anmeldungen, Bewertungen und wer sie liest |
| [Übergabe bei Wechsel der Ehrenamtlichen](uebergabe-bei-wechsel-der-ehrenamtlichen.md) | Eine saubere Übergabe von Konten, Eigentum an Inhalten, Wissen und Zugängen |
| [Checkliste für kleine Gruppen](checkliste-fuer-kleine-gruppen.md) | Eine Checkliste von der Planung bis zur jährlichen Übergabe |

## Empfohlene Lesereihenfolge

1. [Website-Planung für kleine Gruppen](website-planung-fuer-kleine-gruppen.md)
2. [Gemeinsam bearbeiten mit Ehrenamtlichen](gemeinsam-bearbeiten-mit-ehrenamtlichen.md)
3. [Veranstaltungen und Neuigkeiten für Gruppen](veranstaltungen-und-neuigkeiten-fuer-gruppen.md)
4. [Formulare und Rückmeldungen von Mitgliedern](formulare-und-rueckmeldungen-von-mitgliedern.md)
5. [Übergabe bei Wechsel der Ehrenamtlichen](uebergabe-bei-wechsel-der-ehrenamtlichen.md): Lesen Sie sie, bevor Sie sie brauchen.
6. [Checkliste für kleine Gruppen](checkliste-fuer-kleine-gruppen.md)

## Benachbarte Bereiche

- [Gemeinnützige Organisationen](../gemeinnuetzige/README.md): Spenden, Anmeldung von Ehrenamtlichen, Kampagnen,
  Barrierefreiheit, kleine Budgets.
- [Mitglieder](../mitglieder/README.md): Registrierung von Mitgliedern und Inhalte nur für Mitglieder.
- [Newsletter](../newsletter/README.md): Mails an Mitglieder und Unterstützer.
- [Bibliotheken und Kataloge](../bibliotheken/README.md): für Gruppen, die eine Sammlung pflegen.
- [Sicherheit und Datenschutz](../sicherheit/README.md): Konten, Zugriffsprüfungen, DSGVO-Grundlagen.
- Öffentliche Einrichtungen haben einen eigenen Bereich: [Behörden: hier beginnen](../../../../eng-us/help/government/government-start-here.md).

## Verwandte Seiten

- [Unternehmen: hier beginnen](../unternehmen-hier-beginnen.md)
- [Welcher Bereich passt zu Ihrer Organisation?](../welcher-bereich-zu-ihrer-organisation-passt.md)
- [Die Website an Redakteure übergeben](../../../../eng-us/help/creators/website-creators/handing-over-to-editors.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
