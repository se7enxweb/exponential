---
title: Bewerbungen von Ehrenamtlichen bearbeiten
audience: non-profit, editor, businesses, admin
category: non-profits
order: 40
summary: Die Routine der Koordinatoren für Ehrenamtliche mit den Gesammelten Informationen von Exponential - wer Bewerbungen lesen darf (infocollector/read), die Übersicht mit ihren Zahlen, eine Bewerbung lesen, antworten, sicher nach CSV exportieren, Bewerbungen nach Plan entfernen und was das Audit-Protokoll aufzeichnet - mit einer wöchentlichen Routine und einem Aufbewahrungsplan.
keywords: Bewerbungen von Ehrenamtlichen, Gesammelte Informationen, infocollector/read, CSV exportieren, Ausgewählte entfernen, Aufbewahrung, Audit-Protokoll, Koordinator, wöchentliche Routine, personenbezogene Daten, Antwort, volunteer applications, collected information, Export CSV, remove selected, retention, audit trail, coordinator, weekly routine, personal data, reply
type: help
---
# Bewerbungen von Ehrenamtlichen bearbeiten

Diese Seite richtet sich an die Koordinatoren für Ehrenamtliche. Bewerbungen kommen per E-Mail an und werden unter
**Setup > Gesammelte Informationen** gespeichert. Beides enthält personenbezogene Daten, deshalb geht es in der
folgenden Routine ebenso ums Löschen wie ums Lesen. Die Referenz ist
[Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md).

## Inhalt

1. [Wer Bewerbungen lesen darf](#wer-bewerbungen-lesen-darf)
2. [Die Übersicht](#die-übersicht)
3. [Lesen und antworten](#lesen-und-antworten)
4. [Sicher exportieren](#sicher-exportieren)
5. [Bewerbungen entfernen](#bewerbungen-entfernen)
6. [Was das Audit-Protokoll aufzeichnet](#was-das-audit-protokoll-aufzeichnet)
7. [Die wöchentliche Routine](#die-wöchentliche-routine)
8. [Ein Aufbewahrungsplan](#ein-aufbewahrungsplan)

## Wer Bewerbungen lesen darf

Alles unter **Gesammelte Informationen** erfordert die Richtlinie `infocollector/read`. „Geben Sie sie den Personen,
die die Formulare bearbeiten, nicht jedem Redakteur“ ([Formulareinsendungen lesen und exportieren](../../ersteller/website-ersteller/formulareinsendungen.md)).
Tragen Sie die Inhaber im [Arbeitsblatt: Rollen und Berechtigungen](../vorlagen/arbeitsblatt-rollen-und-berechtigungen.md)
ein und geben Sie ihnen die Zwei-Faktor-Anmeldung ([Zwei-Faktor-Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).

## Die Übersicht

**Setup > Gesammelte Informationen** zeigt Zahlen (Sammlungen insgesamt, in den letzten 30 und 7 Tagen, die neueste)
und eine Karte pro Formular mit **Sammlungen**, **CSV exportieren** und **Objekt**. Die Zahlen der letzten 30 Tage
zeigen schnell, ob die Gewinnung von Ehrenamtlichen funktioniert.

## Lesen und antworten

1. Öffnen Sie **Sammlungen** auf der Karte des Ehrenamtsformulars: eine Zeile pro Bewerbung.
2. Öffnen Sie eine Zeile, um alle Werte zu sehen.
3. Antworten Sie aus dem gemeinsamen Postfach, das die Formular-E-Mail erhalten hat, damit der Verlauf der Antworten
   bei der Organisation bleibt.
4. Halten Sie Ihre Entscheidung dort fest, wo Ihre Organisation die Unterlagen über Ehrenamtliche führt (nicht auf der
   Website).

> **Tipp**
> Beantworten Sie jede Bewerbung, auch mit einem kurzen „im Moment nicht“. Die Dankesseite der Website hat eine
> Antwort versprochen; halten Sie das Versprechen in der dort genannten Zeit.

## Sicher exportieren

**CSV exportieren** lädt die Bewerbungen eines Formulars herunter: UTF-8 mit Byte Order Mark, eine Spalte pro Feld,
höchstens 50 000 Zeilen; Werte, die eine Tabellenkalkulation als Formel lesen würde, beginnen mit einem Apostroph.
Jeder Export wird im Audit-Protokoll mit der Anzahl der Zeilen verzeichnet, nie mit den Werten
([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md#2-sammlungen-lesen-und-exportieren)).

- Exportieren Sie nur, wenn Sie es brauchen (zum Beispiel, um einen Einführungstag zu planen).
- Speichern Sie die Datei dort, wo personenbezogene Daten hingehören, nicht auf einem privaten Laptop oder einem
  gemeinsamen Laufwerk, das alle öffnen können.
- Löschen Sie die Datei, wenn die Aufgabe erledigt ist.

## Bewerbungen entfernen

Markieren Sie Bewerbungen in der Liste des Formulars und wählen Sie **Ausgewählte entfernen**; die Bestätigung nennt,
wie viele entfernt werden und dass das Formular bestehen bleibt. Entfernungen werden im Audit-Protokoll als
`data.infocollection.remove` verzeichnet (gleiche Quelle). Exportieren Sie vorher, wenn Sie noch etwas brauchen.

## Was das Audit-Protokoll aufzeichnet

| Aktion | Verzeichnet als | Werte verzeichnet? |
|---|---|---|
| CSV-Export | `data.export.csv` mit der Zeilenzahl | Nein |
| Entfernen | `data.infocollection.remove` | Nein |

So können Sie nachweisen, dass Bewerbungen nach Ihrem Plan exportiert und gelöscht wurden, ohne dass das
Audit-Protokoll selbst die personenbezogenen Daten enthält ([Das Audit-Protokoll](../../../funktionen/6.0/pruefprotokoll.md)).

## Die wöchentliche Routine

- [ ] Die Übersicht öffnen; die Zahl der letzten 7 Tage prüfen.
- [ ] Neue Bewerbungen lesen und beantworten.
- [ ] Spam gesammelt entfernen.
- [ ] Alle CSV-Exporte dieser Woche löschen.
- [ ] Bewerbungen entfernen, die älter als Ihre Aufbewahrungsfrist sind.

## Ein Aufbewahrungsplan

Schreiben Sie ihn auf und stellen Sie ihn neben das Formular („Wir bewahren Bewerbungen ... Monate auf“):

| Art | Aufbewahren für (Ihre Entscheidung) | Danach |
|---|---|---|
| Nicht weiterverfolgte Bewerbungen | | Unter Gesammelte Informationen entfernen |
| Bewerbungen angenommener Ehrenamtlicher | Bis die Angaben in Ihren Unterlagen über Ehrenamtliche stehen | Entfernen |
| Spam | Gar nicht | Wöchentlich entfernen |
| CSV-Exporte | Bis die Aufgabe erledigt ist | Die Datei löschen |

Auch Sicherungen enthalten alte Bewerbungen; sie verfallen mit Ihrer Rotation der Sicherungen. Siehe
[Aufbewahrung und Löschung von Daten](../../unternehmen/sicherheit/aufbewahrung-und-loeschung-von-daten.md).

> **Hinweis**
> Grundlagen, keine Rechtsberatung.

## Verwandte Seiten

- [Ehrenamtliche](README.md)
- [Datenschutz für Spender und Ehrenamtliche](../betrieb/datenschutz-fuer-spender-und-ehrenamtliche.md)
- [Formulare zum Sammeln von Informationen (Datenschutz)](../../verwaltung/datenschutz/formulare-des-information-collectors.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
