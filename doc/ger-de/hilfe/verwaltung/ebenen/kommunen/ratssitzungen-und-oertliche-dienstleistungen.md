---
title: Ratssitzungen und örtliche Dienstleistungen
audience: government, project-manager, website-creator, admin
category: public-sector
order: 70
summary: Wie eine Kommune ihren Sitzungszyklus in Exponential veröffentlicht - Kalender, Tagesordnung und Vorlagen, die Sitzung selbst, Niederschriften nach der Genehmigung, Beschlüsse - und wie sie Dienstleistungsseiten mit Verantwortlichen, Überprüfungsdaten und einer monatlichen Prüfung richtig hält.
keywords: Ratssitzungen, Tagesordnung, Sitzungsvorlagen, Niederschriften, Beschlüsse, Ausschuss, Sitzungszyklus, Dienstleistungsseiten, Überprüfungsdatum, Verantwortung für Inhalte, örtliche Dienstleistungen, council meetings, agenda, meeting papers, minutes, decisions, committee, meeting cycle, service pages, review date, content ownership, local services
type: help
---
# Ratssitzungen und örtliche Dienstleistungen

Diese Seite richtet sich an Webverantwortliche, den Sitzungsdienst und Website-Ersteller einer Stadt, Gemeinde oder
eines Rats. Sie behandelt die zwei Dinge, die eine kommunale Website besonders machen: den Sitzungszyklus des Rats und
die Pflege vieler Dienstleistungsseiten durch ein kleines Team. Sie verwendet die Klassen Sitzung und Dienstleistung,
die in [Inhaltsmodell für Kommunen](inhaltsmodell.md) vorgeschlagen werden; jede genannte Produktfunktion ist auf der
daneben verlinkten Seite dokumentiert.

## Inhalt

1. [Der Sitzungszyklus](#der-sitzungszyklus)
2. [Jede Stufe veröffentlichen](#jede-stufe-veröffentlichen)
3. [Berichtigungen und die Aufzeichnung](#berichtigungen-und-die-aufzeichnung)
4. [Dienstleistungsseiten richtig halten](#dienstleistungsseiten-richtig-halten)
5. [Eine monatliche Routine](#eine-monatliche-routine)

## Der Sitzungszyklus

| Stufe | Übliche Zeitpunkte (durch Ihre örtlichen Regeln festgelegt) | Was veröffentlicht wird |
|---|---|---|
| Kalender | zu Beginn des Jahres | ein Objekt Sitzung je Sitzung, Status *geplant* |
| Tagesordnung und Vorlagen | eine Anzahl von Tagen vor der Sitzung | Text der Tagesordnung, Vorlagen als Objekte der Klasse File, mit der Sitzung verknüpft |
| Sitzung | am Tag selbst | ein Link auf eine Aufzeichnung oder einen Stream, falls vorhanden |
| Entwurf der Niederschrift | nach der Sitzung, wenn Ihre Regeln Entwürfe veröffentlichen | ein als Entwurf gekennzeichnetes Objekt der Klasse File |
| Genehmigte Niederschrift | nach der Genehmigung in der nächsten Sitzung | das Objekt der Klasse File mit der genehmigten Niederschrift |
| Beschlüsse | nach der Sitzung | Objekte für Beschlüsse oder eine Liste der Beschlüsse auf der Sitzungsseite |

Ordner: `Council and democracy > Meetings > <year>`, ein Objekt Sitzung je Sitzung, seine Vorlagen in einem Ordner
`Papers` darunter oder in der Medienbibliothek gespeichert und mit der Sitzung verknüpft.

## Jede Stufe veröffentlichen

1. **Kalender.** Legen Sie die Sitzungen des Jahres mit **Mehrere neu anlegen** (bis zu 50 auf einmal) unter dem
   Jahresordner an
   ([Inhaltsmodell und Bearbeitung, 6](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#6-viele-elemente-in-einem-formular-bearbeiten-mehrfachbearbeitung)).
2. **Tagesordnung.** Bearbeiten Sie die Sitzung: Füllen Sie die Tagesordnung aus, fügen Sie im Feld Vorlagen mit **Neu
   hochladen** Vorlagen hinzu (jeder Upload wird ein Objekt der Klasse File), setzen Sie den Status auf *Tagesordnung
   veröffentlicht* und drücken Sie **Zur Veröffentlichung schicken**.
3. **Sperrfristen für Vorlagen.** Dürfen Vorlagen erst zu einem festgelegten Zeitpunkt erscheinen, kann ein
   Workflow-Ereignis *Bis Datum warten* für die Klasse Sitzung die Version zurückhalten, bis ein Datumsattribut erreicht
   ist; der Workflow-Cronjob veröffentlicht sie dann ([Workflows](../../../../anleitungen/workflows.md)). Testen Sie das,
   bevor Sie sich darauf verlassen.
4. **Niederschrift.** Laden Sie die genehmigte Niederschrift als Objekt der Klasse File hoch, verknüpfen Sie sie im
   Feld Niederschrift der Sitzung, setzen Sie den Status auf *Niederschrift genehmigt* und veröffentlichen Sie.
5. **Benachrichtigen.** Einwohnerinnen und Einwohner, die dem Ordner Meetings folgen, erhalten eine
   Benachrichtigungs-E-Mail, wenn der Cronjob für Benachrichtigungen läuft
   ([Benachrichtigungen](../../../../anleitungen/benachrichtigungen-administrator.md)); ein RSS-Export des Ordners bedient
   Feed-Reader ([RSS-Feeds](../../../../anleitungen/rss-feeds.md)).

Vorlagen werden oft von einem Ratsinformationssystem erzeugt. Dessen PDFs hochzuladen ist in Ordnung; achten Sie darauf,
dass jede Vorlage auch einen aussagekräftigen Namen hat und dass der Text der Tagesordnung auf der Seite ohne Öffnen der
PDFs lesbar ist.

## Berichtigungen und die Aufzeichnung

| Situation | Tun |
|---|---|
| Ein Tippfehler auf der Seite der Tagesordnung | bearbeiten und veröffentlichen; die frühere Version bleibt im Versionsverlauf |
| Eine falsche Vorlage wurde veröffentlicht | die richtige Datei als neue Version desselben Objekts der Klasse File hochladen und der Tagesordnung eine Zeile „Berichtigt am <Datum>“ hinzufügen |
| Eine Vorlage muss zurückgezogen werden (zum Beispiel versehentlich veröffentlichte personenbezogene Daten) | das Objekt der Klasse File sofort verbergen oder entfernen; auch alte Versionen prüfen, weil eine archivierte Version die Datei weiterhin enthält, und diese Versionen unter **Versionen verwalten** entfernen ([Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md)) |
| Niederschrift mit Änderungen genehmigt | den genehmigten Text veröffentlichen; den Entwurf als archivierte Version oder als deutlich gekennzeichnete Datei aufbewahren, wie Ihre Regeln es verlangen |

Das Entfernen von Versionen ist endgültig. Stimmen Sie sich mit Ihrer Schriftgutverwaltung ab, bevor Sie etwas aus der
Aufzeichnung entfernen; eine versehentliche Veröffentlichung personenbezogener Daten braucht meist auch Ihre oder Ihren
Datenschutzbeauftragten.

## Dienstleistungsseiten richtig halten

| Maßnahme | Wie |
|---|---|
| Eine verantwortliche Person je Seite | die Sektion des Fachbereichs und das Feld Eigentümer des Objekts ([Eigentümer ändern](../../../../anleitungen/eigentuemer-aendern.md)) |
| Ein Überprüfungsdatum | das verpflichtende Attribut `review_date` der Klasse Dienstleistung |
| Eine Quelle für Kontaktdaten | Objekte der Klasse Anlaufstelle, mit den Dienstleistungsseiten verknüpft |
| Eine Quelle für Gebühren | jährlich wechselnde Gebühren an einer Stelle (eine Seite oder ein verknüpftes Objekt), nicht in viele Seiten kopiert |
| Rückmeldung | ein kurzes Formular „War diese Seite hilfreich?“ ([Formulare mit dem Informationssammler](../../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md)) |

## Eine monatliche Routine

- [ ] Dienstleistungen auflisten, deren Überprüfungsdatum überschritten ist; jeder verantwortlichen Person ihre Liste
  schicken.
- [ ] **Setup > Gesammelte Informationen** auf neue Rückmeldungen prüfen; danach handeln und dann entfernen, was nicht
  mehr gebraucht wird.
- [ ] Die Linkprüfung ausführen oder lesen (**Setup > URL-Verwaltung**, der Cronjob `linkcheck.php` im Teil
  infrequent).
- [ ] Unter **Suchstatistiken** nach Suchen ohne Ergebnis sehen und die fehlenden Wörter in die Seiten aufnehmen.
- [ ] Unter **Meine Warteliste** und **Setup > Workflow-Prozesse** nach Freigaben sehen, die zu lange warten.

## Verwandte Seiten

- [Kommunen](README.md)
- [Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md)
- [Link- und Inhaltsprüfungen](../../../../../eng-us/help/creators/website-creators/link-and-content-checks.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
