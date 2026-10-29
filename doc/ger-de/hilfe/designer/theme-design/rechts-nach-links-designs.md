---
title: Designs für rechts nach links
audience: designer
category: theme-design
order: 580
summary: Ein Design für Arabisch, Hebräisch, Persisch und andere Sprachen von rechts nach links funktionsfähig machen: woher die Richtung kommt ($site.text_direction, die Locale-Dateien), dir am html-Element, logische CSS-Eigenschaften, Spiegelregeln, welche Symbole sich umdrehen, Richtung von Inhalten und Namen im Text.
keywords: rechts nach links, RTL, dir rtl, Arabisch, Hebräisch, Persisch, Urdu, Textrichtung, TextDirection, logische Eigenschaften, spiegeln, Bidi, bdi, dir auto, text_direction, right to left, rtl, text direction, logical properties, mirror
type: help
---
# Designs für rechts nach links

Diese Seite ist für Designer, die ein Design in Sprachen von rechts nach links funktionsfähig machen. Die Administration tut das bereits
(seit 6.0.15); ihre Regeln gelten für jedes Design. Die vollständige Beschreibung der Funktion ist
[Sprachen von rechts nach links in der Administration](../../../funktionen/6.0/rechts-nach-links.md).

## Inhalt

1. [Woher die Richtung kommt](#woher-die-richtung-kommt)
2. [Der Seitenrahmen](#der-seitenrahmen)
3. [Die vier CSS-Regeln](#die-vier-css-regeln)
4. [Inhalte in einer anderen Richtung](#inhalte-in-einer-anderen-richtung)
5. [Namen im Text](#namen-im-text)
6. [Testen](#testen)

## Woher die Richtung kommt

- `share/locale/<locale>.ini`, `[LanguageSettings] TextDirection=rtl` ist die einzige Quelle; 43 Locale-Dateien tragen sie.
- In Templates ist `$site.text_direction` `rtl` oder `ltr` für die aktuelle Oberflächensprache (vom Kernel für jede Seite gesetzt), und ein
  Locale-Objekt hat `text_direction` und `is_right_to_left`.

## Der Seitenrahmen

Geben Sie `dir="rtl"` nur für eine Sprache von rechts nach links aus, damit Seiten von links nach rechts Byte für Byte gleich bleiben:

```tpl
<html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
```

Jedes Pagelayout der Administration verwendet genau diese Zeile. `first_set()` hält die Seite auf einem Kernel ohne die Variable lauffähig.

## Die vier CSS-Regeln

1. **Logische Eigenschaften für das Layout**: `margin-inline-start`, `padding-inline-end`, `inset-inline-start`, `border-inline-start`,
   `text-align: start`, die logischen Eckradien. Von links nach rechts entsprechen sie den physischen; von rechts nach links tauschen sie
   von selbst ([Logische CSS-Eigenschaften](logische-css-eigenschaften.md)).
2. **Eine Spiegelregel, wo es keine logische Eigenschaft gibt** (Kurzschreibweisen mit vier Werten und verschiedenen Seiten, `float`,
   `background-position`, waagrechte Schatten), direkt nach der Regel, die sie spiegelt:

   ```css
   .x { margin: 0 4px 0 12px; }
   :where([dir="rtl"]) .x { margin: 0 12px 0 4px; }
   ```

   `:where()` fügt keine Spezifität hinzu, sodass die Spiegelung genau dort gewinnt und verliert, wo es das Original tut.
3. **Drehen Sie nur Symbole um, die entlang der Zeile zeigen**: Pfeile, Chevrons, Carets, Antworten, Abmelden. admin4 verwendet
   `scale: -1 1` an diesen `.a4-bi-*`-Symbolen. Ein Häkchen, eine Uhr oder ein Logo wird nie umgedreht.
4. **Spiegeln Sie keine Ausrichtung, die ein Redakteur gewählt hat**: Klassen wie `text-left`, `align-right` aus dem Rich Text behalten
   ihre Seite.

Skripte, die messen oder Dinge bewegen, verwenden ebenfalls die Richtung (`getComputedStyle(document.documentElement).direction` oder
logische Stileigenschaften wie `marginInlineStart`).

## Inhalte in einer anderen Richtung

Ein Text von rechts nach links kann auf einer Seite von links nach rechts erscheinen und umgekehrt. Das
`design/standard/templates/parts/exp_content_direction.tpl` des Kernels gibt für eine Inhaltssprache von rechts nach links
` lang="..." dir="rtl"` aus und sonst nichts; verwenden Sie es an dem Element, das den Inhalt enthält
([Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)).

## Namen im Text

Ein von einem Redakteur eingegebener Name, der in einen übersetzten Satz eingefügt wird, muss isoliert werden, damit seine Satzzeichen an
seinem eigenen Ende bleiben: `dir="auto"` an einem Inline-Element, das nur den Namen enthält, `<bdi>` um einen Namen in einem Satz oder
Block.

```tpl
{'Written by %name'|i18n( 'design/mytheme',, hash( '%name', concat( '<bdi>', $author|wash, '</bdi>' ) ) )}
```

## Testen

- Melden Sie sich mit einem Testkonto an und wählen Sie eine Oberflächensprache von rechts nach links (Hebräisch, Arabisch), oder fügen
  Sie für eine öffentliche Website einen Siteaccess von rechts nach links hinzu.
- Prüfen Sie den Rahmen (Spalten tauschen), Symbole (nur Richtungssymbole drehen sich um), Formulare, Tabellen, Blättern und Slider.
- Prüfen Sie erneut eine Seite von links nach rechts: Sie darf sich nicht verändert haben.
- Der Test `tests/tests/design/AdminRightToLeftTest.php` der Administration liest die Namen der logischen Eigenschaften; übernehmen Sie
  seine Idee für Ihr Design.

## Verwandte Seiten

- [Logische CSS-Eigenschaften](logische-css-eigenschaften.md)
- [Tutorial: ein Theme für rechts nach links](tutorial-ein-rechts-nach-links-theme.md)
- [Checkliste: rechts nach links](checkliste-rechts-nach-links.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
