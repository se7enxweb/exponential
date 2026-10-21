---
title: Alternativtext an einem Bildfeld verlangen
audience: government, admin
category: public-sector
order: 420
summary: Schritt für Schritt - die Option des Klassenattributs „Alternativtext ist erforderlich“ (Exponential 6.0.15 und später) für die Bildfelder einschalten, die Redakteure verwenden, sodass ein Bild nicht ohne Alternativtext gespeichert werden kann, und es im Bearbeitungsformular prüfen.
keywords: Alternativtext, Pflichtfeld, Alternativtext ist erforderlich, ezimage, Klassenattribut, Inhaltsklasse, Setup Klassen, Bearbeitungsformular, WCAG 1.1.1, EN 301 549 11.8, Redakteure, öffentlicher Sektor, alternative text, required, Alternative text is required, class attribute, content class, edit form, editors, public sector
type: help
---
# Alternativtext an einem Bildfeld verlangen

Für Administratoren. Seit 6.0.15 kann ein Bildattribut einer Inhaltsklasse Alternativtext verlangen: Das
Bearbeitungsformular verweigert dann das Speichern eines Bildes ohne ihn
(`kernel/classes/datatypes/ezimage/ezimagetype.php`, Option standardmäßig ausgeschaltet).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Rechte, Klassen zu bearbeiten (Setup > Klassen).
- Entscheiden Sie, wie Redakteure mit dekorativen Bildern umgehen: Mit eingeschalteter Option ist ein leerer
  Alternativtext in diesem Feld nicht möglich, also brauchen dekorative Bilder einen anderen Weg (eine eigene Klasse
  oder eine designspezifische Regel).
- Informieren Sie die Redakteure, bevor Sie die Option einschalten.

## Schritte

1. **Setup > Klassen**, öffnen Sie die Klassengruppe und die Klasse (zum Beispiel Image, Article).
2. **Bearbeiten**.
3. Markieren Sie an jedem Bildattribut (Datentyp Bild) **Alternativtext ist erforderlich** (sein Tooltip: „Redakteure
   können in diesem Feld kein Bild ohne Alternativtext speichern.“).
4. **OK**, um die Klasse zu speichern.
5. Wiederholen Sie das für jede Klasse mit Bildfeldern, die Redakteure für informative Bilder verwenden.

## Prüfen, ob es funktioniert hat

1. Bearbeiten Sie ein Objekt der Klasse; die Beschriftung **Alternativer Bildtext** zeigt, dass das Feld Pflicht ist.
2. Laden Sie ein Bild ohne Alternativtext hoch und speichern Sie: Das Formular antwortet „Für dieses Bild ist ein
   Alternativtext erforderlich.“
3. Bestehende Objekte behalten ihre Bilder; die Regel gilt, wenn das nächste Mal ein Bild gespeichert wird.
4. ZIP-Uploads überspringen Bilder, wo die Site Alternativtext verlangt, weil eine ZIP-Datei keinen hat.

## Verwandte Seiten

- [Alternativtext für Bilder](../../barrierefreiheit/alternativtext-fuer-bilder.md)
- [Den Alternativtext aller Bilder prüfen](den-alternativtext-aller-bilder-pruefen.md)
- [Datentypen wählen](../../../ersteller/website-ersteller/datentypen-waehlen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
