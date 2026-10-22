---
title: Jahresveröffentlichungen als Downloads
audience: non-profit, editor, website-creator, government
category: non-profits
order: 20
summary: Den Jahresbericht, die jährlichen Meldungen, Jahresabschlüsse und Richtlinien einer Non-Profit-Organisation auf einer Exponential-Site als Datei-Downloads veröffentlichen - die Dateiklasse und content/download, ein Ordner pro Jahr, stabile Adressen, Angabe von Format und Größe, durchsuchbare PDFs, eine HTML-Zusammenfassung, zeitgesteuerte Veröffentlichung, Korrekturen ohne stillschweigendes Ersetzen und auffindbare ältere Jahrgänge.
keywords: Jahresbericht, Jahresveröffentlichungen, Meldungen, Gemeinnützigkeit, Abschlüsse, Dateiklasse, Downloads, PDF, Suche in Dateien, stabile URLs, Korrekturen, Archiv, annual review, annual publications, filings, 501(c)(3), Form 990, charity accounts, file class, ezbinaryfile, content/download, search inside files, stable URLs, corrections, archive
type: help
---
# Jahresveröffentlichungen als Downloads

Diese Seite richtet sich an Redakteure, die die jährlichen Dokumente veröffentlichen, und an Website-Ersteller, die
die Struktur einrichten. Sie behandelt den Jahresbericht, die Jahresabschlüsse, behördliche Meldungen (zum Beispiel die
jährliche Informationserklärung einer US-Non-Profit-Organisation oder die Jahresmeldung an eine Aufsichtsbehörde für
gemeinnützige Organisationen anderswo) und Richtlinien. Welche Dokumente Sie veröffentlichen müssen, ist eine
Rechtsfrage; fragen Sie Ihren Steuerberater.

## Inhalt

1. [Wie ein Download funktioniert](#wie-ein-download-funktioniert)
2. [Struktur und Adressen](#struktur-und-adressen)
3. [Ein Dokument Schritt für Schritt veröffentlichen](#ein-dokument-schritt-für-schritt-veröffentlichen)
4. [Dokumente auffindbar machen](#dokumente-auffindbar-machen)
5. [Zu einem Datum veröffentlichen](#zu-einem-datum-veröffentlichen)
6. [Korrekturen](#korrekturen)
7. [Ältere Jahrgänge](#ältere-jahrgänge)
8. [Checkliste](#checkliste)

## Wie ein Download funktioniert

- Ein Objekt der Klasse **Datei** (Klasse `file`) enthält das Dokument in seinem Attribut `file` (Datentyp
  `ezbinaryfile`). Hochgeladene Dateien, die keine Bilder sind, werden zu Dateiobjekten.
- Die Download-Adresse lautet `content/download/<object ID>/<attribute ID>`. Die View prüft, ob der Besucher das Objekt
  lesen darf, und sendet dann die Datei.
- Verlinken Sie Dateien nicht direkt aus dem Speicherverzeichnis: Das umgeht die Berechtigungsprüfung.

Quelle: [Dateien und Downloads](../../ersteller/website-ersteller/dateien-und-downloads.md#wie-ein-download-funktioniert).

## Struktur und Adressen

Verwenden Sie einen Ordner pro Jahr unterhalb eines Ordners „Rechenschaft“, wie in der
[Gliederung der Seite für Jahresveröffentlichungen](../vorlagen/gliederung-der-seite-fuer-jahresveroeffentlichungen.md). Die Adressen
lauten dann etwa `/About-us/Accountability/2026/Annual-review-2026` und bleiben stabil. Behalten Sie die Namen bei:
Umbenennen ändert Adressen, und verschobene Inhalte brauchen Weiterleitungen ([URLs, Weiterleitungen und verschobene Seiten](../../kunden/alltag/urls-weiterleitungen-und-verschobene-seiten.md),
[URLs erhalten](../../verwaltung/schriftgut-und-archivierung/urls-erhalten.md)).

## Ein Dokument Schritt für Schritt veröffentlichen

1. Erstellen Sie ein barrierefreies PDF ([Barrierefreie Kampagnen und Dokumente](../barrierefreiheit/barrierefreie-kampagnen-und-dokumente.md#dokumente-und-pdfs)).
2. Prüfen Sie es auf personenbezogene Daten, die nicht öffentlich sein dürfen (Unterschriften, Wohnadressen, Namen von
   Begünstigten). Das Schwärzen ist ein manueller Schritt vor dem Hochladen ([Schwärzen ist ein manueller Schritt](../../verwaltung/transparenz-und-informationsfreiheit/schwaerzen-ist-handarbeit.md)).
3. Legen Sie im Jahresordner ein Objekt der Klasse **Datei** an; benennen Sie es mit Titel und Jahr („Jahresbericht 2026“).
4. Laden Sie die Datei hoch; schreiben Sie eine Beschreibung von ein oder zwei Sätzen.
5. Veröffentlichen Sie es, oder senden Sie es zur Freigabe, wenn Ihr Workflow das verlangt.
6. Verlinken Sie es von der Einstiegsseite („Neueste Veröffentlichungen“) und von der Spendenseite ([Die Spendenseite](../spenden/die-spendenseite.md)).
7. Öffnen Sie den Download in abgemeldetem Zustand, um zu prüfen, ob er funktioniert.

## Dokumente auffindbar machen

- Zeigen Sie Format und Größe neben dem Link an (dieselbe Quelle wie oben).
- Fügen Sie eine HTML-Zusammenfassungsseite mit den wichtigsten Zahlen hinzu, die auf die Datei verlinkt.
- Inhalte von PDF- und Word-Dateien lassen sich für die Suche indizieren: `settings/binaryfile.ini` enthält Handler,
  die Text mit externen Programmen extrahieren, die Sie installieren, und mit Solr (`ezfind`) lassen sich
  Dateiinhalte ebenfalls indizieren ([Dateien und Downloads](../../ersteller/website-ersteller/dateien-und-downloads.md#in-dateien-suchen)).
- Verschlagworten Sie Dokumente nach Jahr und Art mit `eztags`, wenn Sie viele haben ([Verschlagworten mit eztags](../../ersteller/website-ersteller/verschlagworten-mit-eztags.md)).

## Zu einem Datum veröffentlichen

Muss der Jahresbericht an einem festen Datum erscheinen (einer Jahresversammlung), bereiten Sie ihn vorab vor und
lassen Sie die Site ihn veröffentlichen: Der Workflow „Bis Datum warten“ veröffentlicht zu einem Datum
([Zeitgesteuertes Veröffentlichen](../../kunden/alltag/zeitgesteuertes-veroeffentlichen.md),
[Zeitgesteuerte Veröffentlichung und Sperrfristen](../../verwaltung/transparenz-und-informationsfreiheit/zeitgesteuertes-veroeffentlichen-und-sperrfristen.md)).
Prüfen Sie danach, ob es geklappt hat; es hängt vom Cronjob ab.

## Korrekturen

- Ersetzen Sie ein Finanzdokument nicht stillschweigend. Laden Sie die korrigierte Datei als neue Version hoch und
  schreiben Sie „korrigiert am (Datum): (was sich geändert hat)“ in die Beschreibung, oder veröffentlichen Sie ein
  separates korrigiertes Dokument.
- Die frühere Version bleibt im Versionsverlauf, und das Audit-Protokoll hält fest, wer die Änderung veröffentlicht hat
  ([Versionen und das Audit-Protokoll](versionen-und-das-audit-protokoll.md)).
- Siehe [Veröffentlichte Informationen zurückziehen und korrigieren](../../verwaltung/transparenz-und-informationsfreiheit/veroeffentlichte-informationen-zuruecknehmen-und-berichtigen.md).

## Ältere Jahrgänge

- Lassen Sie frühere Jahre online, die neuesten zuerst; Unterstützer und Forschende vergleichen Jahrgänge.
- Verstecken oder verschieben Sie nur, was Sie nicht mehr veröffentlichen dürfen.
- Bevorzugen Sie für die Langzeitaufbewahrung Formate, die sich für die Archivierung eignen ([Dateiformate für die Archivierung](../../verwaltung/schriftgut-und-archivierung/dateiformate-fuer-die-langzeiterhaltung.md)).

## Checkliste

- [ ] Barrierefreies PDF, auf personenbezogene Daten geprüft.
- [ ] Dateiobjekt im richtigen Jahresordner, eindeutiger Name.
- [ ] Format und Größe angegeben; HTML-Zusammenfassung verlinkt.
- [ ] Download in abgemeldetem Zustand getestet.
- [ ] Einstiegsseite und Spendenseite aktualisiert.
- [ ] Korrekturen sichtbar, nie stillschweigend.

## Verwandte Seiten

- [Transparenz für Non-Profit-Organisationen](README.md)
- [Finanzdokumente](finanzdokumente.md)
- [PDF- und Dokumentformate für Offenlegungen](../../verwaltung/transparenz-und-informationsfreiheit/pdf-und-dokumentformate-fuer-offenlegungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
