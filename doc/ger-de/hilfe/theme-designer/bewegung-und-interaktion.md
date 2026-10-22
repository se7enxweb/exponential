---
title: "Bewegung und Interaktion"
audience: theme-designer
category: theme-design
order: 30
summary: Wie Theme-Designer Bewegung und Interaktion für ein Exponential-Theme festlegen - wozu Bewegung dient, Dauern und Easing als Tokens, reduzierte Bewegung, Karussells und Slider, die Redakteure füllen, Aufklappbereiche und Dialoge und wie Interaktion als gepackte Skripte ohne Inline-Code ins Theme gelangt.
keywords: Theme-Designer, Bewegung, Animation, Übergänge, Easing, Dauer, reduzierte Bewegung, Karussell, Slider, Akkordeon, Dialog, Interaktionsdesign, Mikrointeraktionen, theme designer, motion, animation, transitions, duration, reduced motion, carousel, slider, accordion, dialog, interaction design, micro-interactions
type: help
---
# Bewegung und Interaktion

Diese Seite ist für Theme-Designer, die einem Theme Bewegung und Verhalten hinzufügen: Übergänge, Menüs, die sich
öffnen, Akkordeons, Slider. Bewegung hilft, wenn sie eine Veränderung erklärt, und schadet, wenn sie dekoriert: Sie
bremst Menschen, lenkt ab und kann manchen Besuchern übel werden lassen. Die Regeln zur Barrierefreiheit stehen in
[Reduzierte Bewegung](../designer/theme-design/reduzierte-bewegung.md) und [Tastatur und Fokus](../designer/theme-design/tastatur-und-fokus.md),
wie Skripte in ein Theme gelangen, in [JavaScript laden](../designer/theme-design/javascript-laden.md). Diese Seite
handelt davon, Bewegung und Interaktion gut festzulegen.

## Inhalt

1. [Wozu Bewegung dient](#wozu-bewegung-dient)
2. [Dauern und Easing als Tokens](#dauern-und-easing-als-tokens)
3. [Reduzierte Bewegung](#reduzierte-bewegung)
4. [Karussells und Slider](#karussells-und-slider)
5. [Aufklappbereiche, Menüs und Dialoge](#aufklappbereiche-menüs-und-dialoge)
6. [Wie Interaktion ins Theme gelangt](#wie-interaktion-ins-theme-gelangt)
7. [Bewegung bei der Übergabe festlegen](#bewegung-bei-der-übergabe-festlegen)

## Wozu Bewegung dient

Verwenden Sie Bewegung, um:

- zu zeigen, woher etwas kam oder wohin es ging (ein Menü, das aus seiner Schaltfläche gleitet);
- eine Aktion zu bestätigen (eine kurze Hervorhebung nach dem Speichern);
- zwei Zustände zu verbinden (ein Akkordeon, das sich öffnet).

Verwenden Sie sie nicht, um das Scrollen zu dekorieren, Inhalte beim Lesen ins Bild zu animieren oder eine Seite
"lebendig wirken" zu lassen. Inhaltswebsites werden gelesen; Bewegung, die mit dem Lesen konkurriert, kostet mehr, als
sie bringt.

## Dauern und Easing als Tokens

Halten Sie zwei oder drei Dauern und eine oder zwei Easing-Kurven bereit, und machen Sie sie wie die anderen Werte zu
Tokens ([Design-Tokens](../designer/theme-design/design-tokens.md)):

```css
:root {
  --site-duration-fast: 120ms;   /* hover, focus, small fades */
  --site-duration-base: 200ms;   /* menus, accordions */
  --site-ease: cubic-bezier(0.2, 0, 0, 1);
}
```

Kurz ist besser: Übergänge in der Oberfläche über etwa 300 ms wirken bei Wiederholung langsam. Bewegungs-Tokens sind
hell und dunkel gleich.

## Reduzierte Bewegung

Besucher können ihr System um weniger Bewegung bitten. Das Muster der Hilfe für Designer schreibt Bewegung nur innerhalb
von `@media (prefers-reduced-motion: no-preference)`, sie muss also ausdrücklich zugelassen sein
([Reduzierte Bewegung](../designer/theme-design/reduzierte-bewegung.md#das-muster)). Gestalten Sie für jede Animation auch
die reduzierte Fassung: meist eine sofortige Veränderung oder ein Überblenden statt einer Bewegung. Das Menü öffnet sich
trotzdem; es gleitet nur nicht.

## Karussells und Slider

Blocktypen wie Slider und Karussell gibt es im Layout-Editor, Redakteure werden sie also verwenden
([Blockdefinitionen](../designer/theme-design/blockdefinitionen.md#die-mitgelieferten-blöcke)). Wenn Ihr Theme sie
gestaltet:

- keine automatische Rotation, oder eine sichtbare Pause-Steuerung und ein Halt bei Hover und Fokus;
- Steuerungen für zurück und weiter, die echte Schaltflächen sind und mit der Tastatur erreichbar;
- eine Positionsangabe ("2 von 5");
- jede Folie ohne die Bewegung lesbar; die erste Folie trägt den wichtigsten Inhalt, denn viele Besucher sehen die
  anderen nie;
- ein Design für nur eine Folie, bei dem die Steuerungen verschwinden.

Oft dient ein Raster-Ansichtstyp desselben Blocks Besuchern besser; bieten Sie ihn neben dem Slider an
([Layoutblöcke entwerfen](layoutbloecke-gestalten.md)).

## Aufklappbereiche, Menüs und Dialoge

- **Aufklappbereiche** (Akkordeons, "mehr anzeigen"): Der Auslöser ist eine Schaltfläche, die sagt, ob sie geöffnet ist;
  der Inhalt öffnet sich darunter. Die nativen Elemente `details` und `summary` leisten das ohne Skript.
- **Menüs**: öffnen sich bei Klick oder Enter, schließen sich mit Escape und wenn der Fokus sie verlässt; Menüs nur für
  Hover versagen auf Touchscreens und bei Tastaturen
  ([Navigation und Menüs gestalten](navigation-und-menues-gestalten.md#kleine-bildschirme-und-tastaturen)).
- **Dialoge**: sparsam verwenden; der Fokus wandert in den Dialog und kehrt danach zum Auslöser zurück. Das native
  Element `dialog` übernimmt vieles davon.

Gestalten Sie für jedes den geöffneten und den geschlossenen Zustand und die Fokusposition nach dem Öffnen und
Schließen.

## Wie Interaktion ins Theme gelangt

Verhalten wird in JavaScript-Dateien des Designs geschrieben, die in den Skript-Einstellungen des Designs aufgeführt oder
aus einem Template angefordert und von ezjscore gepackt werden. Templates enthalten keine Inline-Skripte und keine
`on*=`-Attribute, sodass eine strenge Content Security Policy möglich ist
([Kein Inline-JavaScript](../designer/theme-design/kein-inline-javascript.md),
[CSP-sicheres Design](../designer/theme-design/csp-sicheres-design.md)). Wo jQuery verwendet wird, ist es die Version, die
ezjscore mitliefert ([jQuery in Designs](../designer/theme-design/jquery-in-designs.md)). Skripte schalten Klassen und
ARIA-Attribute um; das Stylesheet zeichnet daraus die Zustände
([Interaktive Zustände gestalten](interaktive-zustaende-gestalten.md#vom-zustand-zum-selektor)).

## Bewegung bei der Übergabe festlegen

Schreiben Sie für jedes animierte Element eine Zeile:

| Element | Auslöser | Eigenschaft | Dauer | Easing | Reduzierte Bewegung |
|---|---|---|---|---|---|
| Panel des Hauptmenüs | Menüschaltfläche | gleitet vom Anfangsrand herein | `--site-duration-base` | `--site-ease` | erscheint ohne Gleiten |
| Karte | Hover, focus-within | Schatten und 2 px Anheben | `--site-duration-fast` | `--site-ease` | nur Schatten |
| Akkordeon | Schaltfläche | Höhe und Überblenden | `--site-duration-base` | `--site-ease` | sofort |

Eine kurze Bildschirmaufnahme eines Prototyps hilft, aber nach der Tabelle baut der Entwickler.

## Verwandte Seiten

- [Reduzierte Bewegung](../designer/theme-design/reduzierte-bewegung.md)
- [JavaScript laden](../designer/theme-design/javascript-laden.md)
- [Für Performance-Budgets gestalten](fuer-performance-budgets-gestalten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
