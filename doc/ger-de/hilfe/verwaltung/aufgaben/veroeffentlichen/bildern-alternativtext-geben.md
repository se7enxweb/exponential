---
title: Bildern Alternativtext geben
audience: government, editor
category: public-sector
order: 100
summary: Für Redakteure - Alternativtext für Bilder in Exponential schreiben und eingeben, im Bildfeld, auf der Upload-Seite und in den Dialogen des Online-Editors, entscheiden, wann ein Bild keinen braucht, und Bilder finden, denen er noch fehlt.
keywords: Alternativtext, Alt-Text, Bilder, Barrierefreiheit, WCAG 1.1.1, Bildfeld, Upload, Online-Editor, dekorative Bilder, fehlender Alternativtext, Pflichtfeld, alternative text, alt text, images, accessibility, image field, upload, online editor, decorative images, missing alt text, required
type: help
---
# Bildern Alternativtext geben

Diese Seite ist für Redakteure, die Bilder veröffentlichen. Alternativtext ist das, was Screenreader anstelle des Bildes
vorlesen, und was erscheint, wenn ein Bild nicht lädt ([WCAG 2.2](https://www.w3.org/TR/WCAG22/), Kriterium 1.1.1).
Exponential kann ihn je Bildfeld zur Pflicht machen ([Inhaltsmodell und Bearbeitung, 9](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#9-pflicht-alternativtext-gekürzte-textzeilen-und-von-php-erzeugte-namen)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Ihn schreiben](#ihn-schreiben)
3. [Schritte](#schritte)
4. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Wissen Sie, warum das Bild auf der Seite ist: um zu informieren oder nur zur Dekoration.
- Wenn Ihre Site Alternativtext verlangt, ist das Feld als Pflichtfeld markiert (**Alternativer Bildtext**), und das
  Formular antwortet *Für dieses Bild ist ein Alternativtext erforderlich.*, wenn es leer ist.

## Ihn schreiben

| Bild | Alternativtext |
|---|---|
| Ein Foto, das informiert („der neue Eingang der Bibliothek“) | was der Leser daraus braucht: „Stufenloser Haupteingang der Bibliothek mit automatischen Türen“ |
| Ein Diagramm | die Hauptaussage: „Die Recyclingquote stieg zwischen 2020 und 2025 von 40 % auf 52 %“; die Daten in einer Tabelle oder im Text daneben |
| Ein Logo, das verlinkt | wohin es führt: „Startseite“ oder der Name der Organisation |
| Ein Bild mit Text darin | der Text |
| Rein dekorativ | keiner, und dann gehört es nicht in ein Bildfeld, das informieren soll |

Kurz ist meist besser; beginnen Sie nicht mit „Bild von“.

## Schritte

**In einem Bildfeld (Bild-Objekt oder ein Bild-Attribut):**

1. **Bearbeiten** Sie das Bild-Objekt (oder das Element mit dem Bildfeld).
2. Füllen Sie **Alternativer Bildtext** aus.
3. **Zur Veröffentlichung schicken**.

**Beim Hochladen vieler Bilder** ([Viele Dateien auf einmal hochladen](viele-dateien-auf-einmal-hochladen.md)): Füllen Sie in
der Warteschlange den **Alternativtext** für jedes Bild aus, wo die Site danach fragt. Bilder in einer ZIP-Datei werden
übersprungen, wo die Site Alternativtext verlangt, weil eine ZIP-Datei keinen Platz dafür hat.

**Aus einem Relationsfeld oder dem Online-Editor**: Die Upload-Dialoge fragen nach dem Alternativtext, wenn die Klasse
ihn verlangt; füllen Sie ihn dort aus.

## Das Ergebnis prüfen

- [ ] Öffnen Sie die Seite und untersuchen Sie das Bild (Entwicklerwerkzeuge des Browsers oder ein Screenreader): Der
  `alt`-Text ist Ihrer.
- [ ] Bilder, die über den allgemeinen Upload (`content/upload`, Mehrfach-Upload) oder durch Importe hochgeladen
  werden, werden nicht nach einem Text gefragt; prüfen Sie sie danach. Die Liste *Missing alt text* der Erweiterung
  expai zeigt Bilder ohne Alternativtext, wo expai installiert ist.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Der Dateiname als Alternativtext | schreiben Sie eine Beschreibung |
| Derselbe Text wie die Bildunterschrift daneben | Screenreader lesen beides vor; lassen Sie beide einander ergänzen |
| Alternativtext nur in einer Sprache | übersetzen Sie ihn mit der Übersetzung des Bildes |
| Informationen nur im Bild | schreiben Sie sie auch in den Text |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Barrierefreiheit für Site-Ersteller](../../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md)
- [Medien und Uploads](../../../medien-und-uploads.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
