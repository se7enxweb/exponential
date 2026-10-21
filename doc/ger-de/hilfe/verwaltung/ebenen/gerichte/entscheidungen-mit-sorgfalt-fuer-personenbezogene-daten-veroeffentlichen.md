---
title: Entscheidungen mit Sorgfalt für personenbezogene Daten veröffentlichen
audience: government, project-manager, admin, editor
category: public-sector
order: 70
summary: Ein sorgfältiger Veröffentlichungsablauf für Gerichtsentscheidungen in Exponential - vor dem Hochladen anonymisieren, in einer geschlossenen Sektion prüfen, veröffentlichen, berichtigen, ohne personenbezogene Daten in alten Versionen zu lassen, schnell zurückziehen und wissen, welche Kopien in Caches, Suchindizes, dem Papierkorb und Sicherungen bleiben.
keywords: Gerichtsentscheidungen, Urteile, Anonymisierung, Pseudonymisierung, personenbezogene Daten, Prüfung, zurückziehen, Berichtigung, Versionen, Entfernen von Versionen, Papierkorb, Suchindex, Cache, Sicherungen, court decisions, judgments, anonymization, pseudonymization, personal data, review, withdraw, correction, versions, version removal, trash, search index, cache, backups
type: help
---
# Entscheidungen mit Sorgfalt für personenbezogene Daten veröffentlichen

Diese Seite richtet sich an die Personen, die Entscheidungen von Gerichten oder Spruchkörpern veröffentlichen, und an
die Administration, die sie unterstützt. Sie beschreibt einen Ablauf aus Funktionen von Exponential (Sektionen, das
Workflow-Ereignis *Freigeben*, Objektzustände, Versionen, Verbergen und der Papierkorb) und sagt deutlich, was das
System nicht tut. Lesen Sie sie zusammen mit der Veröffentlichungsrichtlinie des Gerichts.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Der Ablauf auf einen Blick](#der-ablauf-auf-einen-blick)
2. [Vor dem Hochladen: anonymisieren](#vor-dem-hochladen-anonymisieren)
3. [Vorbereiten und prüfen](#vorbereiten-und-prüfen)
4. [Veröffentlichen](#veröffentlichen)
5. [Eine veröffentlichte Entscheidung berichtigen](#eine-veröffentlichte-entscheidung-berichtigen)
6. [Eine Entscheidung zurückziehen](#eine-entscheidung-zurückziehen)
7. [Wo Kopien bleiben](#wo-kopien-bleiben)

## Der Ablauf auf einen Blick

| Schritt | Wer | Wo |
|---|---|---|
| Das Dokument anonymisieren | Team für Entscheidungen | außerhalb von Exponential |
| Das Objekt Entscheidung anlegen, die Datei hochladen | Vorbereitung | der Prüfordner, Sektion `decisions_review` |
| Text, Titel und Zusammenfassung prüfen | Prüfung | **Mein Konto > Zusammenarbeit**, die Vorschau |
| Veröffentlichen und in den öffentlichen Ordner verschieben | Veröffentlichung | Sektion `decisions` |
| Berichtigen oder zurückziehen | Leitung der Veröffentlichung | das Objekt, seine Versionen |

Rollen und Sektionen werden wie unter [Struktur, Sektionen und Rollen für Gerichte](struktur-sektionen-und-rollen.md)
eingerichtet.

## Vor dem Hochladen: anonymisieren

Exponential findet oder entfernt keine personenbezogenen Daten in Dokumenten. Anonymisieren (oder pseudonymisieren, wie
Ihre Vorschriften es verlangen) Sie, bevor die Datei das System erreicht:

- Namen von Parteien, Zeugen und Dritten, wie die Vorschriften es vorgeben;
- Anschriften, Geburtsdaten, Kennnummern, Kontonummern;
- indirekte Identifikatoren: Orte, Arbeitgeber, seltene Umstände;
- Metadaten des Dokuments: die Felder Autor und Titel in den Dateieigenschaften, nachverfolgte Änderungen und Kommentare;
- den Dateinamen: `decision-<case number>.pdf`, nie der Name einer Partei.

Tun Sie dasselbe für das, was Sie in das Objekt eingeben: Titel, Zusammenfassung und Tags sind so öffentlich wie das
Dokument.

## Vorbereiten und prüfen

1. Im Prüfordner **Neu erstellen** > **Entscheidung**. Füllen Sie Aktenzeichen, Kammer, Datum, Rechtsgebiet und
   Zusammenfassung aus und laden Sie die anonymisierte Datei in das Feld **Text der Entscheidung** hoch.
2. **Zur Veröffentlichung schicken**. Das Ereignis *Freigeben* hält die Version zurück; sie ist nun ausstehend
   ([Entwürfe und ausstehende Objekte](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)).
3. Die prüfende Person öffnet das Element unter **Mein Konto > Zusammenarbeit**, öffnet die Vorschau und die Datei,
   prüft jedes Feld gegen die Richtlinie und drückt **Freigeben** oder **Ablehnen** mit einem Kommentar
   ([Workflows](../../../../anleitungen/workflows.md)).
4. Nach dem Lauf des Workflow-Cronjobs wird die Version veröffentlicht, weiterhin in der geschlossenen Sektion.

## Veröffentlichen

1. Die veröffentlichende Person verschiebt das Objekt in den öffentlichen Ordner Decisions (**Verschieben**); prüfen Sie
   im Reiter **Details**, dass seine Sektion nun `decisions` ist
   ([Sektionen, 8.2](../../../../anleitungen/sektionen.md#82-verschieben-und-orte)).
2. Setzen Sie im Reiter **Details**, **Zustand des Objekts**, den Zustand *Veröffentlicht* und drücken Sie
   **Zustände einstellen**.
3. Öffnen Sie die Entscheidung abgemeldet und prüfen Sie die Seite und den Download.

## Eine veröffentlichte Entscheidung berichtigen

| Berichtigung | Schritte |
|---|---|
| Ein Tippfehler in der Zusammenfassung | **Bearbeiten**, korrigieren, „Berichtigt am <Datum>“ zur Zusammenfassung hinzufügen, veröffentlichen; den Zustand *Berichtigt* setzen |
| Personenbezogene Daten im Dokument gefunden | die Datei sofort durch eine berichtigte Datei ersetzen (**Bearbeiten**, hochladen, veröffentlichen); dann die früheren Versionen entfernen, die die alte Datei enthalten |

Die früheren Versionen entfernen: Öffnen Sie **Versionen verwalten**, filtern Sie nach dem Status *Archiviert*, kreuzen
Sie die Versionen an, die die alte Datei oder den alten Text enthalten, **Ausgewählte Versionen entfernen**, prüfen Sie
die Liste, **Endgültig entfernen**. Die veröffentlichte Version lässt sich auf diesem Weg nie entfernen, deshalb
veröffentlichen Sie zuerst die berichtigte Version ([Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md)).
Das Entfernen erfordert `content/versionremove`.

## Eine Entscheidung zurückziehen

| Bedarf | Handlung | Wirkung |
|---|---|---|
| Jetzt nicht mehr anzeigen, für die Akten behalten | **Verstecken / Zeigen** im Menü des Elements (oder **Verstecken** an seinem Ort) | Besucher sehen sie nicht mehr; das Objekt und die Versionen bleiben |
| Vollständig von der Website nehmen | **Entfernen** (verschiebt in den Papierkorb) | Besucher können sie nicht erreichen; sie lässt sich aus dem **Papierkorb** wiederherstellen |
| Löschen | aus dem Papierkorb entfernen (**Papierkorb leeren** oder die Bereinigung des Papierkorbs) | endgültig; die Dateien gehen mit |

Setzen Sie vor dem Verbergen den Zustand *Zurückgezogen*, wenn Sie das Objekt behalten, damit die Aufzeichnung sagt,
warum. Jeder Schritt wird im Audit-Protokoll aufgezeichnet ([Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)).

## Wo Kopien bleiben

| Ort | Was zu tun ist |
|---|---|
| Archivierte Versionen | entfernen Sie sie wie oben beschrieben |
| Der Papierkorb | ein Objekt im Papierkorb enthält weiterhin seine Dateien; leeren Sie ihn, wenn die Entscheidung gelöscht werden muss |
| Suchindex | Verbergen und Entfernen aktualisieren den Index; mit eZ Find prüfen Sie, dass die Entscheidung aus der Suche verschwunden ist |
| Caches | das Veröffentlichen leert den Ansichts-Cache des Objekts; wenn Sie den statischen Cache, den Antwort-Cache von Velocity (`./console exp:velocity cache clear`) oder ein CDN verwenden, leeren Sie auch diese ([Caches](../../../../anleitungen/caches.md), [Antwort-Cache von Velocity](../../../../../eng-us/features/6.0/velocity-response-cache.md)) |
| Sicherungen | die Entscheidung bleibt in Sicherungen, bis diese ablaufen; Ihre Aufbewahrungsregeln entscheiden |
| Kopien anderswo | Suchmaschinen, Archive und Personen, die sie heruntergeladen haben; verlangen Sie die Entfernung, wo das Gesetz es vorsieht |

Vereinbaren Sie im Voraus, wer die oder den Datenschutzbeauftragten informiert, wenn versehentlich personenbezogene
Daten veröffentlicht wurden.

## Verwandte Seiten

- [Gerichte](README.md)
- [Schwerpunkte der Anforderungen für Gerichte](schwerpunkte-der-anforderungen.md)
- [Veröffentlichen, Versionen und der Papierkorb](../../../veroeffentlichen-und-versionen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
