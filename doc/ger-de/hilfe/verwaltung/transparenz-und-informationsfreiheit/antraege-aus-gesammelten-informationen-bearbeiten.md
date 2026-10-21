---
title: Anträge aus gesammelten Informationen bearbeiten
audience: government, editor, admin
category: public-sector
order: 70
summary: Wie ein Team für Informationsfreiheit Antragseinsendungen unter Setup > Gesammelte Informationen liest, exportiert und entfernt, wie es sie in einen Vorgangsablauf überführt und wie lange es sie aufbewahrt.
keywords: Gesammelte Informationen, infocollector, Antragsbearbeitung, Anträge auf Informationszugang, CSV exportieren, Sammlungen entfernen, Aufbewahrung, Vorgangsverwaltung, infocollector/read, collected information, request handling, FOI requests, export CSV, remove collections, retention, case management
type: help
---
# Anträge aus gesammelten Informationen bearbeiten

Diese Seite richtet sich an die Menschen, die Anträge auf Informationszugang bearbeiten, die über ein Webformular
eingehen. In Exponential werden die Einsendungen unter **Setup > Gesammelte Informationen** (`/infocollector/overview`)
aufbewahrt, mit einem CSV-Export und einer Entfernung, die vorher nachfragt
([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)). Exponential hat keine Vorgangsverwaltung:
Fristen, Zuweisungen und Erinnerungen gehören in Ihren Vorgangsablauf.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Wer Anträge lesen darf](#wer-anträge-lesen-darf)
2. [Einen Antrag lesen](#einen-antrag-lesen)
3. [Ihn in den Vorgangsablauf überführen](#ihn-in-den-vorgangsablauf-überführen)
4. [Exportieren](#exportieren)
5. [Entfernen](#entfernen)
6. [Ein einfaches Register als Inhalt](#ein-einfaches-register-als-inhalt)

## Wer Anträge lesen darf

Alles auf der Seite erfordert die Richtlinie `infocollector/read`. Geben Sie sie nur dem Antragsteam, über eine Rolle,
die dessen Benutzergruppe zugewiesen ist ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)). Exporte
und Entfernungen werden im Audit-Trail als `data.export.csv` und `data.infocollection.remove` aufgezeichnet, mit
Anzahlen, aber nie mit Werten.

## Einen Antrag lesen

1. Öffnen Sie **Setup > Gesammelte Informationen**. Jede Karte ist ein Formularobjekt mit der Anzahl seiner Sammlungen.
2. Klicken Sie auf der Karte des Antragsformulars auf **Sammlungen**: eine Zeile pro Einsendung.
3. Öffnen Sie eine Zeile, um jeden Wert zu sehen, den die antragstellende Person gesendet hat.

## Ihn in den Vorgangsablauf überführen

Für jede neue Einsendung:

1. Legen Sie den Vorgang in Ihrem Vorgangssystem an, mit dem Datum der Einsendung (die Uhrzeit wird in UTC angezeigt).
2. Senden Sie die Eingangsbestätigung aus dem Teampostfach.
3. Notieren Sie das Aktenzeichen des Vorgangs neben der ID der Sammlung im Vorgang, nicht auf der Website.
4. Sobald der Vorgang angelegt ist, ist die Einsendung in Exponential nur noch eine Kopie; entfernen Sie sie nach Ihrer
   Aufbewahrungsregel.

## Exportieren

**CSV exportieren** auf der Karte lädt `<name>-collected.csv` herunter: ID der Sammlung, wann sie gesendet und
geändert wurde, die ID des Benutzers, der sie gesendet hat (bei Besuchern der anonyme Benutzer), dann eine Spalte pro
erfasstem Feld. UTF-8 mit Byte Order Mark; Werte, die eine Tabellenkalkulation als Formel lesen würde, beginnen mit
einem Apostroph; höchstens 50 000 Sammlungen, die neuesten zuerst. Die Datei enthält personenbezogene Daten: Speichern
Sie sie nur dort, wo Ihre Vorgangsakten liegen dürfen, und löschen Sie sie, wenn sie nicht mehr gebraucht wird.

## Entfernen

- Alle Einsendungen eines Formulars: Markieren Sie die Karte in der Übersicht, **Ausgewählte entfernen**, lesen Sie die
  Bestätigung, **Entfernen**.
- Einzelne Einsendungen: Markieren Sie sie in der Liste der Sammlungen des Formulars und klicken Sie auf **Ausgewählte
  entfernen**.

Das Formular selbst bleibt bestehen. Das Entfernen lässt sich nicht rückgängig machen; exportieren Sie vorher, wenn
die Daten noch gebraucht werden.

## Ein einfaches Register als Inhalt

Stellen ohne Vorgangssystem führen manchmal ein minimales internes Register als Inhalt: eine Klasse `request_case` mit
Aktenzeichen, Eingangsdatum, Fälligkeitsdatum, Status (Objektzustandsgruppe „Antragsstatus“: Eingegangen, In
Bearbeitung, Beantwortet, Abgeschlossen) und zuständigem Team, in einer Sektion, die nur das Team lesen darf. Das
bietet Versionen und einen Audit-Trail, aber keine Erinnerungen: Jemand muss die Fälligkeitsdaten prüfen. Exponential
berechnet keine gesetzlichen Fristen.

## Verwandte Seiten

- [Ein Antragsformular mit dem Informationssammler](ein-antragsformular-mit-dem-information-collector.md)
- [Objektzustände](../../../anleitungen/objektzustaende.md)
- [Datenschutzstandards](../../../../eng-us/standards/privacy.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
