---
title: "Komponenteninventar"
audience: theme-designer
category: theme-design
order: 11
summary: Wie Theme-Designer ein Inventar der Komponenten eines Exponential-Themes führen - was als Komponente zählt, eine Vorlage für jeden Eintrag mit Zuständen, Tokens und Template, wie das Inventar auf Templates und CSS abgebildet wird und wie es aktuell bleibt.
keywords: Theme-Designer, Komponenteninventar, Komponenten, Designsystem, Musterbibliothek, Zustände, Tokens, BEM, Klassennamen, Templates, include, theme designer, component inventory, components, design system, pattern library, states, class names
type: help
---
# Komponenteninventar

Diese Seite ist für Theme-Designer, die eine vereinbarte Liste aller wiederverwendbaren Teile ihres Themes möchten. Ein
Komponenteninventar ist eine Tabelle, kein Werkzeug: eine Zeile pro Komponente, mit ihren Zuständen, den Tokens, die sie
verwendet, dem Template, das sie zeichnet, und der CSS-Klasse, die sie gestaltet. Es ist der Vertrag zwischen Design und
Code, und Prüfende vergleichen dagegen. Die Administration hat ihre eigenen gemeinsamen Komponenten
([admin4-Komponenten](../designer/theme-design/admin4-komponenten.md)), die Sie wiederverwenden und nie kopieren; diese
Seite handelt von den Komponenten eines öffentlichen Themes.

## Inhalt

1. [Was als Komponente zählt](#was-als-komponente-zählt)
2. [Ein Eintrag](#ein-eintrag)
3. [Ein Inventar für den Anfang](#ein-inventar-für-den-anfang)
4. [Vom Inventar zum Code](#vom-inventar-zum-code)
5. [Benennung](#benennung)
6. [Aktuell halten](#aktuell-halten)

## Was als Komponente zählt

Etwas ist eine Komponente, wenn es in mehr als einem Template erscheint oder wahrscheinlich erscheinen wird. Eine Karte,
die in einer Ordnerliste, einer Liste verwandter Inhalte und einem Layoutblock erscheint, ist eine Komponente. Ein
Schlagzeilenstil, der nur in der Vollansicht des Artikels verwendet wird, ist keine; er gehört zu diesem Template.

## Ein Eintrag

| Feld | Beispiel |
|---|---|
| Name | Karte |
| Zweck | Ein Inhaltseintrag in einem Raster |
| Varianten | mit Bild, ohne Bild, hervorgehoben (breit) |
| Zustände | Standard, Hover, focus-within, besuchter Titel |
| Tokens | `--site-card`, `--site-line`, `--site-radius`, `--site-space-3`, `--site-space-4`, `--site-text-l` |
| Zuständig für das Markup | `design/mytheme/templates/parts/card.tpl` |
| CSS-Klasse | `.mytheme-card`, `.mytheme-card--featured` |
| Verwendet in | Vollansicht des Ordners, Liste verwandter Inhalte, Ansichtstyp `mytheme_cards` der Listenblöcke |
| Hinweise zur Barrierefreiheit | ein Link auf dem Titel, gedehnt; Überschriftenebene wird übergeben |
| Dunkelmodus | geprüft |
| Von rechts nach links | geprüft |

## Ein Inventar für den Anfang

Die meisten Inhaltswebsites brauchen etwa zwanzig Komponenten. Nehmen Sie diese Liste als Anfang und streichen Sie,
was die Website nicht verwendet:

| Gruppe | Komponenten |
|---|---|
| Aktionen | Schaltfläche (primär, sekundär, zurückhaltend), Textlink, Symbolschaltfläche |
| Inhalte | Karte, Zeileneintrag, Autorenzeile, Datum, Tag oder Abzeichen, Abbildung mit Bildunterschrift, Zitat, Tabelle |
| Navigation | Hauptmenü, Untermenü, Brotkrümelnavigation, Seitennavigation, Sprunglink, Sprachumschalter |
| Formulare | Textfeld, Textbereich, Auswahlliste, Kontrollkästchen, Optionsfeld, Feldfehler, Formularzusammenfassung |
| Rückmeldungen | Hinweis (Info, Erfolg, Warnung, Fehler), leerer Zustand |
| Rahmen | Kopfbereich, Fußbereich, Suchfeld |

Jede Komponente hat die interaktiven Zustände aus [Interaktive Zustände gestalten](interaktive-zustaende-gestalten.md).

## Vom Inventar zum Code

- **Markup.** Komponenten, die mehrere Templates verwenden, liegen in einem Template-Teil, der bei Bedarf eingebunden
  wird ([Include](../designer/vorlagen/include.md)): `{include uri='design:parts/card.tpl' node=$child heading_level=3}`.
  Eine Komponente, ein Template-Teil, damit sich das Markup an einer Stelle ändert.
- **Stile.** Komponenten kommen in ein Komponenten-Stylesheet des Designs, das in den Stylesheet-Einstellungen des
  Designs aufgeführt und gepackt wird ([Stylesheets laden](../designer/theme-design/stylesheets-laden.md)); nie ein
  `<style>`-Block oder ein `style=""`-Attribut ([Kein Inline-CSS](../designer/theme-design/kein-inline-css.md)).
- **Werte.** Komponenten verwenden nur Tokens ([Design-Tokens](../designer/theme-design/design-tokens.md)). Braucht eine
  Komponente einen Wert ohne Token, ist das ein Zeichen, einen Token samt dunklem Wert zu ergänzen, nicht den Wert
  hineinzuschreiben.
- **Varianten.** Eine Variante ist eine Modifikatorklasse, nie ein Inline-Stil oder ein Stylesheet pro Seite.

## Benennung

- Ein Präfix für das ganze Theme (`mytheme-`), damit Ihre Klassen nie mit denen des Kernels, einer Erweiterung oder
  eines anderen Themes kollidieren.
- Benennen Sie nach der Rolle: `mytheme-card`, `mytheme-alert--error`, nicht `mytheme-blue-box`.
- Derselbe Name im Inventar, im Designtool, im Template-Teil und in der CSS-Klasse. Nennt das Designtool es "Teaser" und
  das CSS "card", baut es jemand zweimal.

## Aktuell halten

- Das Inventar liegt im Repository des Themes neben dem Code, als Markdown-Tabelle oder als Seite in der Dokumentation
  des Themes ([Die README eines Themes schreiben](../designer/erweiterungen/readme-fuer-ein-theme.md)).
- Eine Änderung an einer Komponente ändert das Inventar im selben Commit.
- Prüfen Sie es einmal pro Release: Entfernen Sie Komponenten, die kein Template verwendet, und führen Sie fast gleiche
  zusammen.
- Eine [Styleguide-Seite](eine-styleguide-seite-bauen.md) auf einer Entwicklungswebsite zeigt jeden Eintrag live; so
  sehen Sie am einfachsten, was abgedriftet ist.

## Verwandte Seiten

- [Eine Styleguide-Seite bauen](eine-styleguide-seite-bauen.md)
- [Vom Mockup zum Theme](vom-mockup-zum-theme.md)
- [Styleguide für Templates](../designer/vorlagen/template-styleguide.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
