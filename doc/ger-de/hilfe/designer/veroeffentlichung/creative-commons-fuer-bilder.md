---
title: "Creative Commons für Bilder und Grafiken"
audience: designer
category: theme-distribution
order: 270
summary: Die Bilder, Illustrationen und Demofotos eines Themes lizenzieren - Creative-Commons-Lizenzen im Vergleich, welche zu einem GPL-Theme passen, die Fallstricke von NonCommercial und NoDerivatives und wie Sie festhalten, woher jedes Bild stammt. Keine Rechtsberatung.
keywords: Creative Commons, CC BY, CC BY-SA, CC0, NonCommercial, NoDerivatives, Bilder, Fotos, Illustrationen, Namensnennung, Bildnachweise, images, photos, illustrations, attribution, image credits
type: help
---
# Creative Commons für Bilder und Grafiken

Diese Seite ist für Designer, deren Theme Bilder mitliefert: einen Platzhalter für das Logo, selbst gezeichnete Icons,
Hintergrundtexturen, Demofotos. Bilder sind keine Software, und Creative-Commons-Lizenzen wurden für solche Werke
gemacht. Dies ist keine Rechtsberatung.

## Die Lizenzen im Vergleich

| Lizenz | Andere dürfen | Passt zu einem Theme, das jeder nutzen und ändern darf? |
|---|---|---|
| CC0 1.0 | Alles, ohne Bedingungen | Ja |
| CC BY 4.0 | Alles, mit Namensnennung | Ja |
| CC BY-SA 4.0 | Alles, mit Namensnennung, geänderte Fassungen unter derselben Lizenz | Ja |
| CC BY-ND 4.0 | Weitergeben, nicht ändern | Nur, wenn niemand das Bild ändern muss |
| CC BY-NC (und NC-SA, NC-ND) 4.0 | Nur nicht-kommerzielle Nutzung | Problematisch: Die Website eines Unternehmens, die das Theme nutzt, kann als kommerziell gelten |

All diese Lizenzen sind in `package.ini [LicenseSettings]` eingerichtet, sodass die Assistenten sie festhalten können.

## Regeln für ein Theme

- **Vermeiden Sie NonCommercial und NoDerivatives** für alles, was ein Website-Betreiber anzeigen oder anpassen wird;
  sie machen das Theme für viele Nutzer unbrauchbar.
- **Liefern Sie nur Bilder mit, die Sie weitergeben dürfen.** Ein Foto von einer Stock-Website, das für Ihre eigene
  Nutzung lizenziert ist, darf meist nicht innerhalb eines Themes weitergegeben werden.
- **Keine Personen ohne deren Einwilligung** auf Demofotos und kein Material von Kunden.

## Schritt für Schritt

1. Erstellen Sie eine Liste aller Bilder in `design/<name>/images/` und `share/`.
2. Schreiben Sie zu jedem Quelle, Urheber und Lizenz in `design/<name>/images/CREDITS.md` (oder in eine Datei für das
   ganze Theme).
3. Ersetzen Sie jedes Bild, dessen Lizenz unklar ist.
4. Nennen Sie die Bildlizenz im README: „Images in design/mytheme/images are CC BY 4.0, (C) <you>, except those listed
   in CREDITS.md.“

## Verwandte Seiten

- [Schriften, Icons und Bibliotheken Dritter](schriften-icons-und-bibliotheken-dritter.md)
- [Bilder und Schriften unter eigenen Lizenzen](../verkauf/bilder-und-schriften-unter-eigenen-lizenzen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
