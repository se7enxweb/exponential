---
title: Symbole in einem Design
audience: designer
category: theme-design
order: 290
summary: Symbole für öffentliche Designs und Admin-Seiten: SVG-Dateien aus dem Design, der in share/icons mitgelieferte Satz Bootstrap Icons, die Maskenklassen .a4-bi-* und IconList von admin4, Symbol-Themes für Klassen und Dateitypen und barrierefreie Symbolknöpfe.
keywords: Symbole, Icons, SVG-Symbole, Bootstrap Icons, Symbolschrift, a4-bi, IconList, mask-image, Symbol-Theme, Klassensymbole, share/icons, barrierefreie Symbole, aria-label, Symbolknopf, icons, svg icons, icon font, icon theme, class icons, accessible icons, icon button
type: help
---
# Symbole in einem Design

Diese Seite ist für Designer, die Symbole hinzufügen. Sie beantwortet: welche Symbolsätze die Installation schon hat, wie admin4 seine
Symbole zeichnet, wie Sie eines hinzufügen und wie Sie Bedienelemente, die nur ein Symbol zeigen, barrierefrei machen.

## Inhalt

1. [Was schon da ist](#was-schon-da-ist)
2. [Symbole in einem öffentlichen Design](#symbole-in-einem-öffentlichen-design)
3. [Symbole in der Administration (admin4)](#symbole-in-der-administration-admin4)
4. [Symbole für Klassen und Dateitypen](#symbole-für-klassen-und-dateitypen)
5. [Barrierefreie Symbole](#barrierefreie-symbole)

## Was schon da ist

| Satz | Wo | Verwendet von |
|---|---|---|
| Bootstrap Icons 1.13.2 (SVG) | `share/icons/bootstrap-icons/svg/` mit seiner `LICENSE` | admin4 und admin4l sowie dem Symbol-Theme `bootstrap-icons` |
| Crystal | `share/icons/crystal`, `crystal-admin` | den klassischen Symbolen für Klassen und Dateitypen |
| Font Awesome (CSS und Schrift) | `extension/sevenx_themes_simple/design/simple/stylesheets/libs/fontawesome/`, das `fonts/` des Designs starter | den Designs simple und starter |

`share/icons/` wird von den mitgelieferten Rewrite-Regeln als statische Dateien ausgeliefert.

## Symbole in einem öffentlichen Design

Für eine Handvoll Symbole liefern Sie SVG-Dateien in `images/icons/` Ihres Designs aus und verweisen auf sie:

```tpl
<a class="social-link" href="{$profile_url|wash}">
    <img src={'icons/rss.svg'|ezimage} alt="" width="20" height="20">
    <span>{'News feed'|i18n( 'design/mytheme' )}</span>
</a>
```

Für Symbole, die der Textfarbe folgen, verwenden Sie sie als CSS-Masken (die Methode von admin4 unten) oder fügen das SVG-Markup in
ein kleines Template ein, das Sie einbinden. Laden Sie keine ganze Symbolschrift für ein paar Symbole.

## Symbole in der Administration (admin4)

admin4 zeichnet Bootstrap Icons als CSS-Masken, gefärbt über `currentColor`. `components.css` hat eine Regel pro Symbolname:

```css
.a4-bi-gear { -webkit-mask-image: url(../../../share/icons/bootstrap-icons/svg/gear.svg);
              mask-image: url(../../../share/icons/bootstrap-icons/svg/gear.svg); }
```

und das Markup verwendet die Klasse:

```html
<span class="a4-icon a4-bi-gear" aria-hidden="true"></span>
```

Auch Einstellungen nennen Symbole: Die Reitersymbole in `menu.ini` (`Icon=palette`) und die Kacheln in Setup (`LinkIcons[]`) nehmen
Namen, die in `design.ini [AdminDesignSettings] IconList[]` aufgeführt sind; ein nicht aufgeführter Name zeichnet das Steckersymbol
(Kommentar in `settings/design.ini`). Eine Erweiterung, die ein weiteres Bootstrap-Symbol braucht, fügt sein SVG, eine Regel
`.a4-bi-<name>` in einem Stylesheet aus `BackendCSSFileList[]` und `IconList[]=<name>` in ihrer `design.ini.append.php` hinzu.

## Symbole für Klassen und Dateitypen

Die kleinen Symbole für Inhaltsklassen und Dateitypen kommen aus **Symbol-Themes** (`settings/icon.ini`). admin4 und admin4l
verwenden das Theme `bootstrap-icons` über `[IconSettings] DesignTheme[]`. Eine Erweiterung kann ihr eigenes Symbol-Theme mitliefern;
siehe [Symbol-Themes in Erweiterungen](../../../../eng-us/features/6.0/icon-themes-in-extensions.md).

## Barrierefreie Symbole

- Ein Symbol neben sichtbarem Text ist Dekoration: `aria-hidden="true"` (oder `alt=""` an einem `<img>`).
- Ein Knopf, der nur ein Symbol zeigt, braucht einen Namen: Text, der für Screenreader sichtbar ist (`<span class="a4-sr-only">` in der
  Administration, anderswo Ihre eigene visuell versteckte Klasse) oder `aria-label`, und meist einen `title` für Mausbenutzer.
- Symbole, die entlang der Zeile zeigen (Pfeile, Chevrons), werden auf Seiten von rechts nach links gespiegelt; admin4 tut das mit
  `scale: -1 1` ([Designs für rechts nach links](rechts-nach-links-designs.md)).
- Maskierte Symbole folgen der Textfarbe und behalten daher im dunklen Modus ihren Kontrast.

## Verwandte Seiten

- [Bilder in einem Design](bilder-in-einem-design.md)
- [SVG-Bilder](svg-bilder.md)
- [admin4-Komponenten](admin4-komponenten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
