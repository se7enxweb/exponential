---
title: Aufbewahrung gesammelter Informationen
audience: government, admin, project-manager, editor
category: public-sector
order: 80
summary: Wie Sie Formulareinsendungen in Exponential nur so lange wie nötig aufbewahren - wer sie lesen darf, Export als CSV mit dem Audit-Eintrag, Sammlungen je Formular oder je Einsendung entfernen, die Kopien, die anderswo bleiben, und ein Aufbewahrungsplan, den Sie von Hand ausführen.
keywords: Aufbewahrung, gesammelte Informationen, Formulareinsendungen, CSV exportieren, Sammlungen entfernen, DSGVO, Speicherbegrenzung, Plan, retention, collected information, form submissions, infocollector/read, export CSV, remove collections, audit data.export.csv, data.infocollection.remove, GDPR, storage limitation, schedule
type: help
---
# Aufbewahrung gesammelter Informationen

Diese Seite richtet sich an Administratoren, Projektleiter und Redakteure, die mit dem umgehen, was Formulare sammeln.
Einsendungen enthalten oft Namen, Adressen und Nachrichten, von denen Menschen erwarten, dass sie für einen Zweck
verwendet und dann gelöscht werden. Exponential speichert sie, bis jemand sie entfernt: Es gibt **kein automatisches
Löschen** gesammelter Informationen. Diese Seite zeigt, wie Sie sie lesen, exportieren und entfernen und wie Sie einen
Aufbewahrungsplan ausführen. Quelle: [Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Wer Einsendungen lesen darf](#wer-einsendungen-lesen-darf)
2. [Exportieren](#exportieren)
3. [Entfernen](#entfernen)
4. [Kopien, die anderswo bleiben](#kopien-die-anderswo-bleiben)
5. [Ein Aufbewahrungsplan](#ein-aufbewahrungsplan)

## Wer Einsendungen lesen darf

Alles unter **Setup > Gesammelte Informationen** (`/infocollector/overview`) braucht die Richtlinie
`infocollector/read`. Geben Sie sie nur den Menschen, die die Einsendungen bearbeiten
([Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)). Die Richtlinie hat keine Einschränkungen: Wer sie
hat, liest die Einsendungen jedes Formulars. Dürfen Teams die Einsendungen der anderen nicht sehen, senden Sie jedes
Formular per Mail an sein eigenes Team und behalten Sie `infocollector/read` bei den wenigen Menschen, die die
Aufbewahrung verwalten.

## Exportieren

**CSV exportieren** auf der Karte eines Formulars lädt `<name>-collected.csv` herunter: UTF-8, Sammlungs-ID, Sende- und
Änderungszeit in UTC, die ID des sendenden Benutzers, dann eine Spalte je sammelndem Attribut; höchstens 50 000
Sammlungen, die neuesten zuerst. Werte, die eine Tabellenkalkulation als Formel lesen würde, beginnen mit einem
Apostroph. Jeder Export wird im Audit als `data.export.csv` mit der Zahl der Zeilen festgehalten, nie mit den Werten.

Der Export enthält, was Menschen eingegeben haben. Speichern Sie ihn dort, wo personenbezogene Daten hingehören, und
löschen Sie ihn, wenn Sie fertig sind.

## Entfernen

| Entfernen | Wie |
|---|---|
| Alle Einsendungen einiger Formulare | markieren Sie in der Übersicht die Formulare, **Ausgewählte entfernen**, prüfen Sie die Bestätigung (sie sagt, wie viele Sammlungen entfernt werden und dass die Formulare bleiben), **Entfernen** |
| Einige Einsendungen eines Formulars | öffnen Sie die **Sammlungen** des Formulars, markieren Sie sie, **Ausgewählte entfernen** |

Entfernungen werden im Audit als `data.infocollection.remove` festgehalten. Das Entfernen ist endgültig; exportieren Sie
zuerst, wenn die Daten noch gebraucht werden. Die Übersicht zählt auch Sammlungen von Formularen, die entfernt wurden
oder im Papierkorb liegen (eine rote Zahl); stellen Sie das Formular aus dem Papierkorb wieder her, um sie zu sehen und
zu entfernen.

## Kopien, die anderswo bleiben

| Kopie | Tun Sie |
|---|---|
| An Empfänger gesendete Mails | löschen Sie sie nach demselben Plan ([Mail an Empfänger](mail-an-empfaenger.md)) |
| CSV-Exporte auf Computern von Mitarbeitern | löschen Sie sie |
| Hochgeladene Dateien des Datentyps Enhanced File | die Erweiterung behält höchstens `MaxFiles` Dateien; prüfen Sie den Speicherpfad ([Dateianhänge in Formularen](dateianhaenge-in-formularen.md)) |
| Sicherungen | entfernte Daten bleiben in Sicherungen, bis diese ablaufen |
| Das Audit-Protokoll | hält die Aktionen fest (Export, Entfernen), nicht die Werte; es wird nach einer Frist pseudonymisiert ([Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)) |

## Ein Aufbewahrungsplan

| Formular | Typische Frist (Beispielannahme, setzen Sie Ihre eigene) | Wer entfernt |
|---|---|---|
| Kontaktformular | bis zur Antwort, plus eine kurze Frist | das antwortende Team |
| Meldung eines Problems | bis zur Behebung | das zuständige Fachteam |
| Anmeldung zu einer Veranstaltung | bis die Veranstaltung vorbei ist | der Veranstalter |
| Antworten in Beteiligungsverfahren | wie es die Schriftgutregeln des Beteiligungsverfahrens vorsehen; der zusammenfassende Bericht kann länger aufbewahrt werden | das Fachreferat |
| Rückmeldungen zu Seiten | bis sie gelesen sind | das Webteam |

Führen Sie ihn monatlich aus:

- [ ] Öffnen Sie **Setup > Gesammelte Informationen**, sortieren Sie nach **erster Sammlung** (die ältesten zuerst).
- [ ] Für jedes Formular nach Ablauf seiner Frist: bei Bedarf exportieren, dann die alten Sammlungen entfernen.
- [ ] Löschen Sie die zugehörigen Mails und Exporte.
- [ ] Vermerken Sie das Datum in Ihrem Schriftgutprotokoll.

Nach der [DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj) in der EU ist die Speicherbegrenzung einer der
Grundsätze; die Datenschutzfunktionen von Exponential sind in
[Standards zum Datenschutz](../../../../../eng-us/standards/privacy.md) zusammengefasst.

## Verwandte Seiten

- [Formulare aufbauen](README.md)
- [Formulareinsendungen lesen und exportieren](../../../../../eng-us/help/creators/website-creators/form-submissions.md)
- [Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
