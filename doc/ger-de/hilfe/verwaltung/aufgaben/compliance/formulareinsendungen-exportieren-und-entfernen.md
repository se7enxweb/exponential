---
title: Formulareinsendungen exportieren und entfernen
audience: government, admin, editor
category: public-sector
order: 310
summary: Schritt für Schritt - die regelmäßige Aufbewahrungsroutine für Einsendungen über Webformulare in Exponential: das Aufzubewahrende als CSV in Ihr Fall- oder Schriftgutsystem exportieren, dann die Sammlungen unter Setup > Gesammelte Informationen entfernen und die Exportdateien löschen.
keywords: Formulareinsendungen, Informationssammlung, Gesammelte Informationen, CSV-Export, Sammlungen entfernen, Aufbewahrung, Routine, infocollector/read, data.export.csv, data.infocollection.remove, öffentlicher Sektor, form submissions, information collection, collected information, CSV export, remove collections, retention, routine, public sector
type: help
---
# Formulareinsendungen exportieren und entfernen

Für Administratoren und Verantwortliche für Formulare. Nichts entfernt Formulareinsendungen automatisch; eine monatliche
oder vierteljährliche Routine hält sie innerhalb Ihrer Aufbewahrungsregeln
([Formulare zur Informationssammlung und Datenschutz](../../datenschutz/formulare-des-information-collectors.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Richtlinie `infocollector/read` (nötig für jede Seite unter Setup > Gesammelte Informationen).
- Die Aufbewahrungsregel je Formular und wohin aufzubewahrende Einsendungen gehen (Fallsystem, Schriftgutsystem).

## Schritte

1. Öffnen Sie **Setup > Gesammelte Informationen** (`/infocollector/overview`). Sortieren Sie nach der letzten Sammlung
   oder nach der Zahl der Sammlungen.
2. Für jedes Formular, dessen Regel „exportieren“ sagt:
   **CSV exportieren** (`infocollector/export/<object id>`, Datei `<name>-collected.csv`, UTF-8 mit Byte Order Mark,
   die neuesten zuerst, höchstens 50 000). Importieren Sie sie in das Fall- oder Schriftgutsystem.
3. Entfernen Sie, was fällig ist:
   - ganze Formulare: Markieren Sie sie in der Übersicht, **Ausgewählte entfernen**, prüfen Sie die Bestätigung (sie
     sagt, wie viele Sammlungen wegfallen und dass die Formulare bleiben), **Entfernen**;
   - nur einige Sammlungen: Öffnen Sie die **Sammlungen** des Formulars, markieren Sie sie, **Ausgewählte entfernen**.
4. Löschen Sie die CSV-Dateien von Ihrem Computer und von jedem gemeinsamen Laufwerk.
5. Halten Sie den Durchlauf fest: Datum, Formulare, Zahlen.

## Prüfen, ob es funktioniert hat

- Die Zahlen der Übersicht zeigen die neuen Stände.
- Das Audit zeigt `data.export.csv` für jeden Export (Zeilen gezählt, niemals Werte) und `data.infocollection.remove`
  für jedes Entfernen: `/audit/console/(name)/data.*`.

## Verwandte Seiten

- [Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md)
- [Formulareinsendungen als Schriftgut](../../schriftgut-und-archivierung/gesammelte-informationen-als-schriftgut.md)
- [Formulareinsendungen für Website-Ersteller](../../../../../eng-us/help/creators/website-creators/form-submissions.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
