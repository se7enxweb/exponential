---
title: Schwärzen ist Handarbeit
audience: government, editor, project-manager
category: public-sector
order: 80
summary: Exponential hat kein Werkzeug zum Schwärzen. Diese Seite erklärt, wo geschwärzt wird (in dem Programm, mit dem das Dokument erstellt wurde), wie Schwärzungen häufig scheitern, einen Prüfablauf vor dem Hochladen und wie die Versionen von Exponential eine ungeschwärzte Datei behalten, wenn Sie nicht aufpassen.
keywords: Schwärzung, schwärzen, PDF, versteckter Text, Textebene, Metadaten, Auskunft, Offenlegung, Ausnahmetatbestand, personenbezogene Daten, Prüfung, redaction, redact, blackout, hidden text, text layer, metadata, FOI response, disclosure, exemption, personal data, checking
type: help
---
# Schwärzen ist Handarbeit

Diese Seite richtet sich an Redaktionen und Zuständige, die Dokumente mit entfernten Teilen veröffentlichen: Namen
Dritter, sicherheitsrelevante Einzelheiten, ausgenommene Passagen. **Exponential hat kein Werkzeug zum Schwärzen.** Es
speichert und liefert die Datei, die Sie hochladen, genau so, wie sie ist. Geschwärzt wird vor dem Hochladen, in dem
Programm, mit dem das Dokument erstellt wurde, oder in einem eigenen Werkzeug zum Schwärzen, und eine Person muss es
prüfen.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Wie Schwärzungen scheitern](#wie-schwärzungen-scheitern)
2. [Ein Ablauf zum Schwärzen](#ein-ablauf-zum-schwärzen)
3. [Die endgültige Datei prüfen](#die-endgültige-datei-prüfen)
4. [Hochladen, ohne das Original zurückzulassen](#hochladen-ohne-das-original-zurückzulassen)
5. [Webseiten statt Dateien](#webseiten-statt-dateien)

## Wie Schwärzungen scheitern

| Fehler | Was passiert |
|---|---|
| Ein schwarzer Kasten über Text in einem PDF | Der Text darunter ist noch da; Kopieren und Einfügen bringt ihn zum Vorschein |
| Text in Weiß oder in der Farbe des Hintergrunds | Noch in der Datei und in Suchergebnissen |
| Ausgeblendete Zeilen, Spalten oder Tabellenblätter | Jeder kann sie wieder einblenden |
| Nachverfolgte Änderungen und Kommentare | Frühere Formulierungen und Namen der Prüfenden bleiben erhalten |
| Dokumenteigenschaften | Autor, Organisation, Dateipfade bleiben erhalten |
| Ein gescanntes Bild mit Textebene (OCR) | Das Bild ist abgedeckt, die Textebene nicht |
| Zuerst eine ungeschwärzte Kopie hochgeladen, dann ersetzt | Die erste Datei bleibt in der Versionsgeschichte |

## Ein Ablauf zum Schwärzen

1. Arbeiten Sie an einer Kopie; bewahren Sie das Original in der Vorgangsakte auf, nicht auf der Website.
2. Verwenden Sie eine Schwärzungsfunktion, die Inhalte **entfernt**, nicht eine, die sie abdeckt. Wenn Ihr Werkzeug das
   nicht kann, drucken Sie die geschwärzten Seiten aus und scannen Sie sie in eine reine Bilddatei, mit dem Nachteil,
   dass sie dann nicht maschinenlesbar ist.
3. Entfernen Sie Dokumenteigenschaften, Kommentare, nachverfolgte Änderungen und versteckte Inhalte.
4. Kennzeichnen Sie jede Schwärzung mit dem Grund (Ausnahmetatbestand), den Ihr Gesetz verlangt.
5. Speichern Sie als neue Datei mit einem neutralen Namen (keine Namen von Personen im Dateinamen: Er erscheint in der
   Download-Adresse).
6. Eine zweite Person prüft die Datei (siehe unten).

## Die endgültige Datei prüfen

- Markieren Sie den gesamten Text und fügen Sie ihn in einen reinen Texteditor ein: Suchen Sie nach den geschwärzten
  Namen und Begriffen.
- Durchsuchen Sie die Datei mit der Suche des Betrachters nach den geschwärzten Begriffen.
- Sehen Sie sich die Dokumenteigenschaften an.
- Bei Tabellen: Blenden Sie alles ein und prüfen Sie jedes Tabellenblatt.
- Halten Sie im Vorgang fest, wer wann welche Datei geprüft hat.

## Hochladen, ohne das Original zurückzulassen

Jedes Veröffentlichen eines Objekts ist eine Version, und ältere Versionen behalten ihre Dateien
([Inhaltsgeschichte](../../../anleitungen/inhaltsverlauf.md)). Wenn jemals eine ungeschwärzte Datei in ein veröffentlichtes
Objekt hochgeladen wurde, reicht das Ersetzen nicht:

1. Ersetzen Sie die Datei und veröffentlichen Sie.
2. Öffnen Sie die Seite **Versionen** des Objekts (`/content/history/<object ID>`), markieren Sie die Versionen, die die
   ungeschwärzte Datei enthalten, öffnen Sie **Ausgewählte Versionen entfernen** und klicken Sie auf **Endgültig
   entfernen** (Richtlinie `content/versionremove`). Die veröffentlichte Version wird dort nie entfernt; deshalb
   veröffentlichen Sie zuerst die korrigierte.
3. Leeren Sie die Caches vor der Website ([Caches](../../../anleitungen/caches.md)).
4. Melden Sie es Ihren Datenschutzbeauftragten.

Um das zu vermeiden, laden Sie nur geprüfte Dateien hoch und verwenden Sie die **Vorschau** oder einen Entwurf, den Sie
erst veröffentlichen, wenn die Prüfung abgeschlossen ist. Ein Entwurf ist nur für die Person sichtbar, die ihn angelegt
hat, und für Personen mit `content/versionread`.

## Webseiten statt Dateien

Wenn die herausgegebenen Informationen kurz sind, veröffentlichen Sie sie als Seitentext statt als Dokument: Was Sie
im Editor schreiben, ist alles, was es gibt, ohne versteckte Ebenen. Formatierter Text in Exponential behält keine
nachverfolgten Änderungen; frühere Formulierungen stehen in älteren Versionen, die nur Mitarbeitende sehen.

## Verwandte Seiten

- [Eine Auskunft nach Informationsfreiheitsrecht veröffentlichen](eine-auskunft-nach-informationsfreiheitsrecht-veroeffentlichen.md)
- [Datenschutzprüfung vor dem Veröffentlichen von Daten](../../../../eng-us/help/government/open-data/privacy-checks-before-publishing-data.md)
- [Inhaltsgeschichte](../../../anleitungen/inhaltsverlauf.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
