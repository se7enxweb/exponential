---
title: Den Alternativtext aller Bilder prüfen
audience: government, editor, admin
category: public-sector
order: 410
summary: Schritt für Schritt - Bilder ohne Alternativtext auf einer öffentlichen Site finden: mit dem optionalen Bericht „Missing alt text“ von Exponential Intelligence, wo diese Erweiterung aktiv ist, oder mit einem Prüfwerkzeug für Barrierefreiheit an Stichproben von Seiten, wo nicht, und sie dann in Entwürfen korrigieren.
keywords: Alternativtext, Alt-Text, Bilder, Missing alt text, expai/alttext, Exponential Intelligence, Prüfwerkzeug für Barrierefreiheit, WCAG 1.1.1, Entwürfe, Redakteure, Prüfung, öffentlicher Sektor, alternative text, alt text, images, accessibility checker, drafts, editors, review, public sector
type: help
---
# Den Alternativtext aller Bilder prüfen

Für Redakteure und Koordinatoren für Barrierefreiheit, als monatliche Prüfung oder vor der Veröffentlichung einer
Erklärung zur Barrierefreiheit. Der Kernel von Exponential hat keine Liste aller Bilder der Site ohne Alternativtext;
es gibt zwei Wege, sie zu finden ([Alternativtext für Bilder](../../../../../eng-us/help/government/accessibility/alternative-text-for-images.md)).

## Inhalt

1. [Weg 1: der Bericht Missing alt text](#weg-1-der-bericht-missing-alt-text)
2. [Weg 2: ein Prüfwerkzeug an Stichproben von Seiten](#weg-2-ein-prüfwerkzeug-an-stichproben-von-seiten)
3. [Korrigieren](#korrigieren)
4. [Verwandte Seiten](#verwandte-seiten)

## Weg 1: der Bericht Missing alt text

Nur wo die Erweiterung Exponential Intelligence (`expai`) aktiv ist
([Exponential Intelligence](../../../../../eng-us/features/6.0/extensions/expai.md)). Sie schickt Bilder nach dessen
Datenrichtlinie an einen KI-Anbieter; verwenden Sie sie nur, wenn Ihre Organisation das genehmigt hat.

1. Öffnen Sie **Missing alt text** (`/expai/alttext`, Richtlinie `expai/write`).
2. Geben Sie die **Node ID of the subtree** an, die geprüft werden soll (zum Beispiel Ihren Nachrichtenordner), und
   **Show**.
3. Die Liste hat eine Zeile je Objekt, Sprache und Bildfeld mit einem gespeicherten Bild und leerem Alternativtext,
   unter den Objekten, die Sie lesen dürfen.
4. Um Vorschläge zu erhalten, markieren Sie Zeilen und **Queue suggestions as drafts**: Jede wird zu einem Auftrag, der
   den Vorschlag in einen **neuen Entwurf** schreibt, der nie veröffentlicht wird. Ein optionales Budget begrenzt die
   Kosten der Auftragsgruppe.
5. Redakteure prüfen jeden Entwurf (korrigieren den Text oder kennzeichnen das Bild als dekorativ, wo Ihr Design das
   unterstützt) und veröffentlichen.

## Weg 2: ein Prüfwerkzeug an Stichproben von Seiten

1. Nehmen Sie eine Stichprobe von Seiten (eine je Klasse und Liste, dazu aktuelle Seiten).
2. Führen Sie ein Prüfwerkzeug auf Basis von axe aus; es meldet Bilder ohne `alt`.
3. Beachten Sie, dass das Bild-Template des Standarddesigns den Namen des Objekts als `alt` schreibt, wenn das Feld leer
   ist, sodass ein Prüfwerkzeug diese vielleicht nicht meldet: Achten Sie auch auf Alternativtexte, die nur der Name des
   Bildes sind.

## Korrigieren

- Bearbeiten Sie das Objekt, füllen Sie **Alternativer Bildtext** je Sprache aus, veröffentlichen Sie.
- Verhindern Sie neue Lücken: „Alternativtext an einem Bildfeld verlangen“.

## Verwandte Seiten

- [Alternativtext für Bilder](../../../../../eng-us/help/government/accessibility/alternative-text-for-images.md)
- [Alternativtext an einem Bildfeld verlangen](alternativtext-an-einem-bildfeld-verlangen.md)
- [Bilder, Medien und Uploads](../../../medien-und-uploads.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
