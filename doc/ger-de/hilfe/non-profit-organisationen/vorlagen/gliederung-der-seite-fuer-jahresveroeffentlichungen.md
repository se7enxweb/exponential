---
title: Gliederung der Seite für Jahresveröffentlichungen
audience: non-profit, businesses, editor, website-creator
category: non-profits
order: 50
summary: Eine Gliederung zum Kopieren für die Seite, auf der eine Non-Profit-Organisation ihren Jahresbericht, ihre Jahresabschlüsse, Meldungen an Behörden und Dokumente zur Organisationsführung in Exponential veröffentlicht - die Struktur nach Jahren, was jeder Eintrag zeigt (Titel, Jahr, Format, Größe, eine kurze Zusammenfassung), Barrierefreiheit, Korrekturen und eine Checkliste für die jährliche Aktualisierung.
keywords: Jahresbericht, Jahresveröffentlichungen, Jahresabschluss, Abschlüsse, Meldungen, Dokumente zur Organisationsführung, Seitengliederung, Downloads, Dateiklasse, nach Jahr, PDF, Barrierefreiheit, Transparenz, annual review, annual publications, financial statements, accounts, filings, governance documents, page outline, downloads, file class, accessibility, transparency
type: help
---
# Gliederung der Seite für Jahresveröffentlichungen

Kopieren Sie diese Gliederung, wenn Sie die Seite bauen, auf der Unterstützer, Geldgeber und Aufsichtsbehörden Ihren
Jahresbericht, Ihre Jahresabschlüsse und Ihre Dokumente zur Organisationsführung finden. Wie Sie die Dateien selbst
veröffentlichen, erklärt [Jahresveröffentlichungen als Downloads](../transparenz/jahresveroeffentlichungen-als-downloads.md).

## Inhalt

1. [Die Struktur](#die-struktur)
2. [Die Einstiegsseite](#die-einstiegsseite)
3. [Ein Eintrag pro Dokument](#ein-eintrag-pro-dokument)
4. [Barrierefreiheit](#barrierefreiheit)
5. [Korrekturen](#korrekturen)
6. [Checkliste für die jährliche Aktualisierung](#checkliste-für-die-jährliche-aktualisierung)

## Die Struktur

Ein Ordner pro Jahr hält die Adressen stabil und das Archiv aufgeräumt:

```text
About us
  Accountability                  <- the landing page
    2026                          <- one folder per year
      Annual review 2026          (file)
      Financial statements 2026   (file)
      Statement of activities     (file or article)
    2025
      ...
    Governance                    <- documents that are not tied to a year
      Articles / bylaws           (file)
      Board members               (article)
      Board meetings              (folder, see Board and minutes pages)
      Policies                    (folder of files)
```

Ratschläge zum Inhaltsbaum: [Den Inhaltsbaum strukturieren](../../ersteller/website-ersteller/den-inhaltsbaum-strukturieren.md).
Seiten zum Vorstand: [Seiten für Vorstand und Protokolle](../transparenz/seiten-fuer-vorstand-und-protokolle.md).

## Die Einstiegsseite

| Teil | Inhalt |
|---|---|
| Einleitung | Zwei oder drei Sätze: wer Sie sind, was Sie hier veröffentlichen und warum |
| Neueste Veröffentlichungen | Der neueste Jahresbericht und die neuesten Jahresabschlüsse, ganz oben |
| Archiv | Frühere Jahre, die neuesten zuerst |
| Organisationsführung | Satzung, Vorstand, Richtlinien |
| Registrierung | Ihr offizieller Name und die Registrierungsangaben, die Ihr Recht verlangt |
| Kontakt | Wer Fragen zu diesen Dokumenten beantwortet |
| Maschinenlesbarer Feed | Ein RSS-Link für neue Veröffentlichungen, falls Sie einen anbieten ([Offene Daten, RSS und REST](../transparenz/offene-daten-rss-und-rest.md)) |

## Ein Eintrag pro Dokument

Jedes Dateiobjekt (Klasse `file`, Datentyp `ezbinaryfile`) wird über `content/download` heruntergeladen, das prüft, ob
der Besucher es lesen darf ([Dateien und Downloads](../../ersteller/website-ersteller/dateien-und-downloads.md)). Zeigen
Sie in der Liste für jedes Dokument:

| Feld | Beispiel (ersetzen) |
|---|---|
| Titel | Jahresbericht 2026 |
| Berichtszeitraum | Januar bis Dezember 2026 |
| Format und Größe | PDF, 2,4 MB |
| Zusammenfassung | Ein oder zwei Sätze zum Inhalt |
| Veröffentlichungsdatum | |
| Weitere Fassungen | HTML-Zusammenfassung, andere Sprachen |

Nennen Sie Besuchern Format und Größe, bevor sie klicken (dieselbe Quelle).

## Barrierefreiheit

- Ein barrierefreies PDF oder daneben eine HTML-Fassung der wichtigsten Inhalte
  ([Dokumente und PDFs](../../verwaltung/barrierefreiheit/dokumente-und-pdfs.md)).
- Tabellen in einer HTML-Zusammenfassung mit korrekten Kopfzeilen ([Tabellen in Inhalten](../../verwaltung/barrierefreiheit/tabellen-in-inhalten.md)).
- Linktexte, die das Dokument benennen, nicht „hier klicken“ ([Linktext](../../verwaltung/barrierefreiheit/linktext.md)).
- Exponential kann Inhalte unter **Setup > PDF-Export** auch in PDF-Dateien umwandeln
  ([PDF-Export](../../../funktionen/6.0/pdf-export.md)); prüfen Sie das Ergebnis wie jedes andere PDF auf Barrierefreiheit.

## Korrekturen

- Ersetzen Sie ein veröffentlichtes Finanzdokument nicht stillschweigend. Veröffentlichen Sie eine korrigierte Datei
  mit „korrigiert“ im Titel und einer kurzen Notiz, was sich wann geändert hat.
- Exponential bewahrt frühere Versionen jedes Objekts auf ([Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)),
  und das Audit-Protokoll hält fest, wer was geändert hat ([Versionen und das Audit-Protokoll](../transparenz/versionen-und-das-audit-protokoll.md)).
- Siehe [Veröffentlichte Informationen zurückziehen und korrigieren](../../verwaltung/transparenz-und-informationsfreiheit/veroeffentlichte-informationen-zuruecknehmen-und-berichtigen.md).

## Checkliste für die jährliche Aktualisierung

- [ ] Ordner für das neue Jahr angelegt.
- [ ] Jahresbericht und Jahresabschlüsse hochgeladen, mit Angabe von Format und Größe.
- [ ] Barrierefreies PDF oder HTML-Zusammenfassung geprüft.
- [ ] „Neueste Veröffentlichungen“ auf der Einstiegsseite aktualisiert.
- [ ] Dokumente zur Organisationsführung noch aktuell (Vorstandsliste, Satzung, Richtlinien).
- [ ] Die Spendenseite verlinkt auf den neuen Jahresbericht ([Die Spendenseite](../spenden/die-spendenseite.md)).
- [ ] Alte Links funktionieren weiterhin.

## Verwandte Seiten

- [Vorlagen und Arbeitsblätter](README.md)
- [Transparenz](../transparenz/README.md)
- [Proaktive Veröffentlichung](../../verwaltung/transparenz-und-informationsfreiheit/proaktive-veroeffentlichung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
