---
title: Ein Handbuch als PDF-Export veröffentlichen
audience: government, editor, admin
category: public-sector
order: 380
summary: Für Redakteure mit der Richtlinie pdf/edit - ein druckbares PDF eines Handbuchs anbieten, das als Webseiten gepflegt wird, mit Setup > PDF-Export - ein Baum-Export des Teilbaums des Handbuchs, einmal oder bei Bedarf erzeugt, ein Link vom Handbuch aus und Neuerzeugen nach Änderungen.
keywords: PDF-Export, Handbuch, Druckversion, Setup PDF-Export, Baum-Export, Einmal erzeugt, Bei Bedarf erzeugt, Neu erzeugen, öffentliche Adresse, Dokumentationsseite, PDF export, handbook, printable version, tree export, generated once, on the fly, Regenerate, public address, documentation page
type: help
---
# Ein Handbuch als PDF-Export veröffentlichen

Diese Seite ist für Redakteure, die ein Handbuch oder einen Leitfaden als Webseiten pflegen und davon ein druckbares
PDF anbieten möchten. **Setup > PDF-Export** erstellt ein PDF aus einem Quellknoten und, bei einem Baum-Export, aus den
Knoten darunter der Klassen, die Sie markieren ([PDF-Exporte](../../../../anleitungen/pdf-exporte.md),
[PDF-Export (Aufbau)](../../aufbauen/dokumente-und-publikationen/pdf-export.md)). Er braucht die Richtlinie `pdf/edit`,
die meist Administratoren erhalten.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Das Handbuch ist ein Teilbaum: eine oberste Seite (ein Ordner oder eine Dokumentationsseite) mit Kapiteln darunter.
- Entscheiden Sie: **Einmal erzeugt** (eine beständige Datei, öffentlich unter einer festen Adresse, von Ihnen nach
  Änderungen neu erzeugt) oder **Bei Bedarf erzeugt** (immer aktuell, nur für Benutzer mit `pdf/edit`). Für
  öffentliche Handbücher: einmal erzeugt.
- Fragen Sie das Web-Team, welche Schrift die Site für PDFs verwendet, wenn Ihr Handbuch Zeichen jenseits der
  westeuropäischen verwendet.

## Schritte

1. **Setup > PDF-Export**, legen Sie einen neuen Export an.
2. Wählen Sie die oberste Seite des Handbuchs als Quellknoten; machen Sie daraus einen Baum-Export; markieren Sie die
   Klassen der Kapitel (zum Beispiel Documentation page, Article).
3. Wählen Sie **Einmal erzeugt** und einen Dateinamen wie `handbook-<subject>.pdf`.
4. Speichern Sie. Die Karte zeigt **Datei bereit** und die **Öffentliche Adresse**.
5. **Herunterladen** und das PDF lesen.
6. Verlinken Sie es von der obersten Seite des Handbuchs aus: „Druckversion (PDF, <Größe>)“ mit der öffentlichen
   Adresse als Ziel.

Nachdem sich das Handbuch geändert hat: **Neu erzeugen** auf der Karte.

## Das Ergebnis prüfen

- [ ] Das PDF enthält jedes Kapitel in der richtigen Reihenfolge, mit lesbaren Zeichen.
- [ ] Abgemeldet lädt die öffentliche Adresse die Datei herunter.
- [ ] Der Link im Handbuch sagt, dass es die Druckversion ist, mit Format und Größe.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Das Neuerzeugen vergessen | das PDF zeigt den Inhalt, wie er war; erzeugen Sie es nach jeder Änderung neu oder nennen Sie das Datum im Link zum PDF |
| Ein eingeschränktes Handbuch „einmal erzeugt“ exportiert | die gespeicherte Datei ist öffentlich; verwenden Sie für eingeschränkte Inhalte „bei Bedarf erzeugt“ |
| Das PDF als die barrierefreie Fassung behandeln | es ist eine Druckkopie; die Webseiten sind die barrierefreie Fassung |
| Ein erratbarer Dateiname für interne Inhalte | gespeicherte PDFs sind öffentlich |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Barrierefreie Dokumente](../../aufbauen/dokumente-und-publikationen/barrierefreie-dokumente.md)
- [Die Funktion PDF-Export](../../../../../eng-us/features/6.0/pdf-export.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
