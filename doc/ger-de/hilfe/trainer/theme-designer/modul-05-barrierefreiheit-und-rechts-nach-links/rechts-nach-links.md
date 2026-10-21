---
title: "Lektion 5.4: Rechts-nach-links"
audience: trainer, designer, developer
category: training
order: 54
summary: Die Teilnehmenden geben die Schreibrichtung in ihrem Pagelayout aus, kennzeichnen Rechts-nach-links-Inhalte mit exp_content_direction.tpl, schreiben richtungssicheres CSS mit logischen Eigenschaften und Spiegelregeln und testen ihren Dashboard-Block in der Administration mit Hebräisch als Sprache der Oberfläche. 60 Minuten.
keywords: theme-design, rechts-nach-links, RTL, dir, text_direction, TextDirection, exp_content_direction.tpl, logische eigenschaften, inline-start, spiegelregel, where dir rtl, bdi, dir auto, hebräisch, arabisch, sprache der oberfläche, theme design, right to left, logical properties, mirror rule, Hebrew, Arabic, interface language
type: help
---
# Lektion 5.4: Rechts-nach-links

Hebräisch, Arabisch, Persisch, Urdu und weitere Sprachen werden von rechts nach links geschrieben. Ein Theme, das
`left` und `right` fest einträgt, funktioniert für sie nicht; ein Theme mit logischen Eigenschaften bedient beide
Richtungen mit einem einzigen Stylesheet. Die Administration von Exponential dreht sich bei einer Rechts-nach-links-
Sprache der Oberfläche vollständig um. In dieser Lektion lernen die Teilnehmenden, dasselbe in ihrem Theme zu tun und
es zu testen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 5: Barrierefreiheit und Rechts-nach-links](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Experten |
| Zielgruppe | Designer und Frontend-Entwickler |
| Voraussetzungen | [Lektion 5.2](ein-barrierefreies-website-design.md); [Lektion 4.4](../modul-04-die-administration-gestalten/modul-und-erweiterungs-stylesheets.md) für den Teil zur Administration |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, woher die Schreibrichtung kommt (aus der Locale-Datei) und wie ein Template sie liest;
- `dir="rtl"` auf der Seite und an Rechts-nach-links-Inhalten ausgeben;
- physische Eigenschaften durch logische ersetzen und eine Spiegelregel schreiben, wo es keine logische Eigenschaft
  gibt;
- einen eingetippten Namen mit `dir="auto"` oder `<bdi>` in seiner eigenen Richtung halten;
- eine Administrationsseite mit einer Rechts-nach-links-Sprache der Oberfläche testen und eine öffentliche Seite mit
  von Hand gesetztem `dir="rtl"`.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Rechts-nach-links-Sprachen in der Administration](../../../../../eng-us/features/6.0/right-to-left.md) und
[Rechts-nach-links-Websites](../../../../../eng-us/help/creators/website-creators/right-to-left-sites.md).

1. **Die Quelle** (4 Minuten). `share/locale/<locale>.ini`, `[LanguageSettings] TextDirection=rtl`; 28 Locale-Dateien
   tragen diese Einstellung. In Templates: `$site.text_direction` für die Oberflächen-Locale des Siteaccess und
   `$code|locale().is_right_to_left` für einen beliebigen Locale-Code.
2. **Die Seite** (3 Minuten). Die Pagelayouts der Administration geben Folgendes aus:

   ```
   <html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
   ```

   Eine Links-nach-rechts-Seite bekommt nichts Neues.
3. **Inhalte** (3 Minuten). Inhalte in einer Rechts-nach-links-Sprache werden auf jeder Seite mit
   `design:parts/exp_content_direction.tpl` gekennzeichnet; das Template gibt für eine Rechts-nach-links-Inhalts-Locale
   ` lang="..." dir="rtl"` aus und für eine Links-nach-rechts-Locale nichts; es erwartet `exp_locale_code`.
4. **CSS** (6 Minuten). Die vier Regeln der Administration: logische Eigenschaften für das Layout
   (`margin-inline-start`, `padding-inline-end`, `inset-inline-start`, `text-align: start`); eine Spiegelregel, wo eine
   logische Eigenschaft es nicht ausdrücken kann, geschrieben als `:where([dir="rtl"]) .x { ... }` direkt nach der
   Regel, die sie spiegelt; nur Icons spiegeln, die entlang der Zeile zeigen; nie eine Ausrichtung spiegeln, die eine
   Redakteurin oder ein Redakteur gewählt hat.
5. **Namen** (4 Minuten). Ein von Redakteuren eingetippter Name ist ein eigener Richtungsabschnitt: `dir="auto"` an
   einem Inline-Element, das nur den Namen enthält, `<bdi>` innerhalb eines Blocks oder um einen Namen in einem
   übersetzten Satz.

### Häufige Fragen

**Warum `:where()` in der Spiegelregel?** Es fügt keine Spezifität hinzu, sodass die Spiegelung bei Rechts-nach-links
genau dort gewinnt und verliert, wo auch die ursprüngliche Regel es tut.

**Brauche ich ein eigenes Stylesheet für Rechts-nach-links?** Nein. Mit logischen Eigenschaften bedient ein Stylesheet
beide Richtungen.

**Wie teste ich eine öffentliche Rechts-nach-links-Website, wenn ich keine habe?** Setzen Sie in den
Entwicklerwerkzeugen von Hand `dir="rtl"` am Element `<html>`; so wurde die Anmeldeseite der Administration geprüft.

### Stolperfallen

- **`float: left`.** Es gibt keinen logischen Wert für `float`, den alle hier verwendeten Regelsätze nutzen; verwenden
  Sie lieber Flex oder Grid, die der Richtung folgen.
- **Alles spiegeln.** Logos, Mediensteuerungen und Diagramme behalten ihre Richtung; nur Icons, die entlang der Zeile
  zeigen, drehen sich um.
- **Das gemeinsame Administratorkonto.** Eine Änderung der Sprache der Oberfläche ändert die Einstellung dieses
  Benutzers. Verwenden Sie Ihren eigenen Schulungsadministrator.

## Praktische Übung

Auf Ihrer Schulungsinstallation. `D` steht für `extension/training_theme/design/training_theme`.

1. Erweitern Sie in `D/templates/pagelayout.tpl` das Element `<html>`:

   ```
   <html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
   ```

2. Kennzeichnen Sie in `D/override/templates/full/article.tpl` den Artikel mit der Richtung seiner Sprache:

   ```
   <article class="training_theme-article"{include uri='design:parts/exp_content_direction.tpl' exp_locale_code=$node.object.current_language}>
   ```

3. Prüfen Sie beide Templates mit `eztemplatecheck.php`.
4. Durchsuchen Sie `D/stylesheets/` nach `left`, `right`, `float` und `text-align`. Schreiben Sie jede Stelle mit einer
   logischen Eigenschaft neu oder ergänzen Sie direkt danach eine Spiegelregel mit `:where([dir="rtl"])`, wo es keine
   gibt.
5. Ergänzen Sie in `training.css` ein Trennzeichen, das entlang der Zeile zeigt:

   ```css
   .training_theme-path a::after { content: " \203A"; }
   ```

   U+203A ist ein Zeichen, das Unicode als gespiegelt kennzeichnet: Browser drehen es in Rechts-nach-links-Text von
   selbst um. Ein mit CSS gezeichneter Pfeil (ein Hintergrundbild oder eine Form aus Rahmenlinien) dreht sich nicht; er
   bräuchte eine Spiegelregel wie `:where([dir="rtl"]) .training_theme-next::after { scale: -1 1; }`. Prüfen Sie den
   Unterschied in Schritt 7.
6. Führen Sie die Routine aus Lektion 3.3 aus und laden Sie eine Seite von `<site>` zwei Ebenen tiefer vollständig neu.
7. Ergänzen Sie in den Entwicklerwerkzeugen von Hand `dir="rtl"` am Element `<html>`. Prüfen Sie Kopfbereich, Menü,
   Brotkrumennavigation und Inhalt: Lesen sie sich von rechts? Gibt es eine horizontale Bildlaufleiste? Entfernen Sie
   das Attribut wieder.
8. Öffnen Sie in der Administration, angemeldet als Ihr eigener Schulungsadministrator, das Werkzeug **Oberfläche** in
   der rechten Spalte, wählen Sie `עברית — Hebrew (Israel)` unter **Sprache der Oberfläche** und klicken Sie auf
   **Anwenden**.
9. Prüfen Sie auf dem Dashboard Ihren Block `training_notes` aus Modul 4: Die Tabelle liest sich von rechts, die Kante
   der aktuellen Zeile ist rechts, die Namen behalten ihre eigene Reihenfolge. Prüfen Sie den hellen und den dunklen
   Modus.
10. Wählen Sie unter **Sprache der Oberfläche** wieder Englisch und klicken Sie auf **Anwenden**.

## Erwartetes Ergebnis

- Schritt 1: Eine Links-nach-rechts-Seite hat wie bisher kein Attribut `dir`.
- Schritt 4: Die Stylesheets des Themes enthalten keine physische Eigenschaft für links oder rechts ohne Spiegelung.
- Schritt 7: Mit von Hand gesetztem `dir="rtl"` spiegelt sich der Rahmen ohne horizontale Bildlaufleiste; das
  Trennzeichen aus Schritt 5 zeigt von selbst nach links, ohne eine eigene Regel der Teilnehmenden.
- Schritt 9: `<html dir="rtl" lang="he-IL">` im Quelltext; die Akzentkante des Blocks (`border-inline-start`) ist
  rechts, ohne jede Änderung an `training_admin.css`.
- Der Trainer prüft, dass am Ende alle Teilnehmenden wieder auf Englisch stehen.

## Quiz

1. Wo liest Exponential, ob eine Sprache von rechts nach links geschrieben wird?
2. Welches Template kennzeichnet Rechts-nach-links-Inhalte mit `lang` und `dir`, und welchen Parameter braucht es?
3. Was ersetzt `padding-right: 1rem` in einem richtungssicheren Stylesheet?
   A. `padding-end: 1rem` B. `padding-inline-end: 1rem` C. `padding-block-end: 1rem` D. `padding-right-rtl: 1rem`
4. Warum wird eine Spiegelregel mit `:where([dir="rtl"])` geschrieben?
5. Ein von Redakteuren eingetippter Name steht in einem übersetzten Satz. Wie wird er isoliert?

<details>
<summary>Antworten</summary>

1. In der Locale-Datei, `share/locale/<locale>.ini`, `[LanguageSettings] TextDirection=rtl`
   ([Rechts-nach-links-Sprachen](../../../../../eng-us/features/6.0/right-to-left.md#where-the-direction-comes-from)).
2. `design:parts/exp_content_direction.tpl`, mit `exp_locale_code`.
3. **B.**
4. `:where()` fügt keine Spezifität hinzu, sodass die Spiegelung genau dort gewinnt und verliert, wo auch die
   ursprüngliche Regel es tut.
5. Mit `<bdi>` um den Namen.

</details>

## Weiterführende Literatur

- [Rechts-nach-links-Sprachen in der Administration](../../../../../eng-us/features/6.0/right-to-left.md): jede Regel, Namen in
  Texten der Oberfläche und wie man testet.
- [Rechts-nach-links-Websites](../../../../../eng-us/help/creators/website-creators/right-to-left-sites.md): die Seite der öffentlichen
  Website.
- [Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md): Inhalte in mehreren Sprachen.
- [Sprachen und Rechts-nach-links in der Schulung](../../train-the-trainer/modul-05-barrierefreiheit-und-inklusion/sprachen-und-rechts-nach-links-im-kurs.md):
  mit Rechts-nach-links-Sprachen unterrichten.

## Verwandte Seiten

- [Modul 5: Barrierefreiheit und Rechts-nach-links](README.md)
- [Theme-Design: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
