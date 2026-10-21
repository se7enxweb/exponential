---
title: Website-Planung für kleine Gruppen
audience: businesses, project-manager, editor
category: business-solutions
order: 10
summary: Wie ein Verein, eine Gesellschaft oder eine ehrenamtlich geführte Gruppe eine Website mit Exponential plant, die wechselnde Ehrenamtliche übersteht - wofür die Website da ist, die wenigen Seiten, die sie braucht, die mitgelieferten Klassen, die sie abdecken, wer was tut, wo die Konten und Unterlagen der Gruppe aufbewahrt werden, und ein Planungsarbeitsblatt.
keywords: Website einer kleinen Gruppe, Vereinswebsite, Website eines Vereins, Gesellschaft, ehrenamtlich geführt, Planung, Website-Struktur, Rollen, Kontinuität, Arbeitsblatt, small group website, club website, association website, society, volunteer-run, planning, site structure, roles, continuity, worksheet
type: help
---
# Website-Planung für kleine Gruppen

Diese Seite ist für den Vorsitzenden, den Schriftführer oder den Ehrenamtlichen, der die Website einer kleinen Gruppe
einrichtet oder neu aufbaut. Kleine Gruppen scheitern selten an fehlenden Funktionen; sie scheitern, wenn die eine
Person geht, die die Website kannte. Diese Seite plant von Anfang an dafür und hält die Website so klein, dass
Ehrenamtliche sie betreiben können.

## Inhalt

1. [Wofür die Website da ist](#wofür-die-website-da-ist)
2. [Die wenigen Seiten, die eine Gruppe braucht](#die-wenigen-seiten-die-eine-gruppe-braucht)
3. [Klassen, die Sie nicht bauen müssen](#klassen-die-sie-nicht-bauen-müssen)
4. [Wer was tut](#wer-was-tut)
5. [Öffentlich, nur für Mitglieder und nur für den Vorstand](#öffentlich-nur-für-mitglieder-und-nur-für-den-vorstand)
6. [Für Kontinuität planen](#für-kontinuität-planen)
7. [Klein bleiben](#klein-bleiben)
8. [Ein Planungsarbeitsblatt](#ein-planungsarbeitsblatt)

## Wofür die Website da ist

Schreiben Sie in je einem Satz die zwei oder drei Dinge auf, die die Website leisten muss. Typisch für eine Gruppe:

- Neuen erklären, wer die Gruppe ist und wie man beitritt;
- Treffen und Veranstaltungen ankündigen;
- Neuigkeiten, Protokolle oder Rundbriefe für Mitglieder veröffentlichen;
- Kontaktanfragen, Anmeldungen oder Rückmeldungen sammeln.

Alles andere ist optional. Die allgemeine Methode steht in
[Ihre Website planen](../../ersteller/website-ersteller/ihre-website-planen.md).

## Die wenigen Seiten, die eine Gruppe braucht

- [ ] Startseite: wer Sie sind, die nächste Veranstaltung, wie man beitritt.
- [ ] Über uns: Zweck, der Vorstand nach Rollen, Satzung oder Regeln als Download.
- [ ] Neuigkeiten.
- [ ] Veranstaltungskalender ([Veranstaltungen und Neuigkeiten für Gruppen](veranstaltungen-und-neuigkeiten-fuer-gruppen.md)).
- [ ] Beitritts- oder Kontaktformular ([Formulare und Rückmeldungen von Mitgliedern](formulare-und-rueckmeldungen-von-mitgliedern.md)).
- [ ] Mitgliederbereich, falls nötig ([Mitglieder](../mitglieder/README.md)).
- [ ] Datenschutzerklärung und die rechtlichen Angaben, die Ihr Recht verlangt.

## Klassen, die Sie nicht bauen müssen

Die Website-Oberfläche `ezwebin` bringt fertige Klassenpakete mit (`extension/ezwebin/packages/`), darunter:

| Klassenpaket | Verwendung in einer Gruppe |
|---|---|
| `folder` | Bereiche der Website |
| `article`, `article_main_page`, `article_sub_page` | Neuigkeiten und längere Seiten |
| `event`, `event_calendar`, `multicalendar` | Treffen und Veranstaltungen |
| `feedback_form` | Kontakt und Rückmeldungen |
| `file` | Protokolle, Satzung, Berichte als Downloads |
| `blog`, `blog_post` | Ein Gruppenblog |
| `forums` | Diskussionsforen |
| `frontpage` | Die Startseite |
| `user_group` | Gruppen von Mitgliederkonten |

Wenn Sie diese statt eigener Klassen verwenden, gibt es bei jeder Übergabe weniger zu erklären. Wenn Ihre Website mit
einem Website-Paket installiert wurde, prüfen Sie, welche Klassen es schon hat, bevor Sie welche hinzufügen
([Website-Pakete und mitgelieferte Themes](../../ersteller/website-ersteller/seitenpakete-und-mitgelieferte-themes.md)).

## Wer was tut

Benennen Sie Rollen, nicht Personen, denn Personen wechseln:

| Rolle | Typischer Inhaber | Braucht |
|---|---|---|
| Web-Koordinator | Ein Vorstandsmitglied | Überblick, genehmigt Änderungen an der Struktur |
| Redakteure | Ehrenamtliche | Bearbeiten ihre eigene Sektion |
| Formularleser | Schriftführer | Einsendungen lesen (`infocollector/read`) |
| Administrator | Ein versierter Ehrenamtlicher oder ein Partner | Einstellungen, Updates, Backups, Konten |
| Stellvertretender Administrator | Eine zweite Person | Dasselbe, um jederzeit übernehmen zu können |

Einzelheiten in [Gemeinsam bearbeiten mit Ehrenamtlichen](gemeinsam-bearbeiten-mit-ehrenamtlichen.md).

## Öffentlich, nur für Mitglieder und nur für den Vorstand

Legen Sie fest, welche Inhalte öffentlich sind, welche für Mitglieder und welche nur für den Vorstand (Entwürfe von
Protokollen, Kontaktlisten). Sektionen und Rollen halten sie auseinander, und bei Downloads werden die Berechtigungen
geprüft ([Sektionen und Objektzustände](../../ersteller/website-ersteller/sektionen-und-objektzustaende.md),
[Dateien und Downloads](../../ersteller/website-ersteller/dateien-und-downloads.md#geschützte-downloads)). Halten Sie
personenbezogene Daten von Mitgliedern vollständig von öffentlichen Seiten fern.

## Für Kontinuität planen

Schreiben Sie Folgendes an einem Ort auf, über den der Vorstand verfügt, nicht im Postfach eines einzelnen
Ehrenamtlichen:

- [ ] Wer den Domainnamen hält, und die Zugangsdaten zum Registrar.
- [ ] Wer das Hosting-Konto hält, und die Zugangsdaten dazu.
- [ ] Wo Backups aufbewahrt werden und wie man sie wiederherstellt.
- [ ] Welche Mailadresse Formulare und Newsletter versendet.
- [ ] Der Administrator und sein Stellvertreter, mit ihren Rollen in der Website.

Speichern Sie Passwörter in einem Passwortmanager, über den der Vorstand verfügt, nie in den Inhalten der Website oder
in Einstellungsdateien, die in die Versionsverwaltung gehen. Die Übergabe selbst ist in
[Übergabe bei Wechsel der Ehrenamtlichen](uebergabe-bei-wechsel-der-ehrenamtlichen.md) beschrieben.

## Klein bleiben

- Verwenden Sie mitgelieferte Klassen und ein Design; vermeiden Sie eigenen Code, den ein künftiger Ehrenamtlicher
  nicht pflegen kann.
- Bevorzugen Sie gehostetes oder verwaltetes Hosting, wenn kein Ehrenamtlicher einen Server aktualisieren kann
  ([Betrieb mit kleinem Budget](../gemeinnuetzige/betrieb-mit-kleinem-budget.md#hosting-möglichkeiten)).
- SQLite, die Standarddatenbank, braucht keinen Datenbankserver, was zu einer Gruppe mit wenigen Redakteuren passt
  ([SQLite-Datenbankunterstützung](../../../funktionen/6.0/sqlite-datenbank.md)).
- Entfernen Sie bei jeder jährlichen Prüfung Funktionen, die niemand nutzt.

## Ein Planungsarbeitsblatt

```text
The site must:              1. ...  2. ...  3. ...
Pages:                      ...
Members area:               yes / no
Web coordinator (role):     ...
Administrator / deputy:     ... / ...
Form reader:                ...
Domain held by:             ...
Hosting held by:            ...
Backups kept at:            ...
Sender address for mail:    ...
Yearly review month:        ...
```

## Verwandte Seiten

- [Vereine und kleine Gruppen](README.md)
- [Checkliste für kleine Gruppen](checkliste-fuer-kleine-gruppen.md)
- [Website-Planung für gemeinnützige Organisationen](../gemeinnuetzige/website-planung-fuer-gemeinnuetzige.md)
- [Ihre Website planen](../../ersteller/website-ersteller/ihre-website-planen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
