---
title: Schriften in einem Design
audience: designer
category: theme-design
order: 280
summary: Webfonts aus dem eigenen Design statt von einem fremden Host ausliefern: wohin Schriftdateien gehören, @font-face-Regeln, font-display, Unicode-Bereiche, System-Schriftstapel, Lizenzen und was der Webserver erlauben muss.
keywords: Schriften, Webfonts, selbst gehostete Schriften, font-face, woff2, font-display, Google Fonts, unicode-range, System-Schriftstapel, Schriftlizenz, Ordner fonts, Schrift vorladen, fonts, web fonts, self-hosted fonts, system font stack, font license, fonts folder, preload font
type: help
---
# Schriften in einem Design

Diese Seite ist für Designer, die die Schriften eines Designs auswählen und ausliefern. Sie beantwortet: wohin Schriftdateien gehören,
wie Sie sie deklarieren, warum Self-Hosting hier der Standard ist und welche Lizenzen es erlauben.

## Inhalt

1. [Standardmäßig selbst gehostet](#standardmäßig-selbst-gehostet)
2. [Wohin die Dateien gehören](#wohin-die-dateien-gehören)
3. [Die Schrift deklarieren](#die-schrift-deklarieren)
4. [Die Deklaration laden](#die-deklaration-laden)
5. [Systemschriften](#systemschriften)
6. [Lizenzen](#lizenzen)
7. [Prüfungen](#prüfungen)

## Standardmäßig selbst gehostet

Das Design media liefert seine Schrift Inter aus seinem eigenen Ordner `fonts/` aus. Der Kommentar in seiner
`pagelayout/head/style.tpl` nennt die Gründe: Ein fremder Schrift-Host kostet für sein Stylesheet eine DNS-Abfrage, eine
TCP-Verbindung und einen TLS-Handshake, dann dasselbe noch einmal für die Schriftdateien, bevor irgendein Text gezeichnet werden kann;
eine selbst gehostete Deklaration wird ohne zusätzliche Anfrage mit dem übrigen CSS gepackt. Self-Hosting bedeutet auch, dass keine
Besucherdaten an einen Schrift-Host gehen und eine Content Security Policy keinen zusätzlichen Ursprung für `font-src` braucht.

## Wohin die Dateien gehören

```
design/mytheme/fonts/source-sans-3-400.woff2
design/mytheme/fonts/source-sans-3-700.woff2
design/mytheme/stylesheets/fonts.css
```

Die mitgelieferten Rewrite-Regeln liefern `fonts/` eines Designs, im Kernel und in Erweiterungen, als statische Dateien aus
(`.htaccess_root`; das Apache-Beispiel `doc/eng-us/examples/ezpublish.conf` hat dieselbe Liste). WOFF2 genügt für jeden aktuellen
Browser.

## Die Schrift deklarieren

```css
/* design/mytheme/stylesheets/fonts.css */
@font-face {
  font-family: "Source Sans 3";
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url(../fonts/source-sans-3-400.woff2) format("woff2");
}
@font-face {
  font-family: "Source Sans 3";
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url(../fonts/source-sans-3-700.woff2) format("woff2");
}

:root { --site-font: "Source Sans 3", system-ui, -apple-system, "Segoe UI", sans-serif; }
body  { font-family: var(--site-font); }
```

- `font-display: swap` zeigt Text sofort in einer Ersatzschrift und tauscht, wenn die Schrift ankommt.
- Verwenden Sie ein relatives `url(../fonts/...)`: Der Packer schreibt es um, wenn er das Stylesheet verschiebt.
- Teilen Sie die Schrift für große Schriftsysteme (Kyrillisch, Griechisch, Vietnamesisch) nach `unicode-range` auf, wie es die
  `inter.css` des Designs media tut, sodass eine Seite nur die Bereiche lädt, die sie verwendet.

## Die Deklaration laden

Setzen Sie `fonts.css` an die **erste** Stelle Ihrer `ezcss_load`-Liste, damit die `@font-face`-Regeln existieren, bevor eine Regel die
Familie verwendet:

```tpl
{ezcss_load( array( 'fonts.css', 'mytheme.css' ) )}
```

## Systemschriften

Die schnellste Schrift ist eine, die der Besucher schon hat. Ein Systemstapel braucht keine Dateien:

```css
:root { --site-font: system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans", sans-serif; }
```

Das Design admin4 setzt seine Schriften als Token (`--a4-font`, `--a4-font-intl`, `--a4-font-cjk`, `--a4-mono` in `components.css`),
sodass Sprachen mit anderen Schriftsystemen einen passenden Stapel bekommen.

## Lizenzen

Liefern Sie nur Schriften aus, deren Lizenz das Einbetten im Web und die Weitergabe mit Ihrem Theme erlaubt (zum Beispiel Schriften
unter der SIL Open Font License). Legen Sie die Lizenzdatei neben die Schriften und nennen Sie Schrift und Lizenz im README Ihres
Themes. Unter einer Desktop-Lizenz gekaufte Schriften dürfen meist nicht ausgeliefert werden. Die Seiten zur Verteilung von Themes
behandeln Lizenzen von Assets Dritter ([Theme-Erweiterungen: hier beginnen](../erweiterungen/theme-erweiterungen-hier-beginnen.md)).

## Prüfungen

- [ ] Keine Anfrage an einen Schrift-Host im Netzwerkbereich des Browsers.
- [ ] Text sofort sichtbar, während die Schriften laden (`font-display: swap`).
- [ ] Der Ersatzstapel sieht ähnlich genug aus, dass der Tausch das Layout kaum verschiebt.
- [ ] Rechts nach links und andere Schriftsysteme der Sprachen Ihrer Website sind von der Schrift oder dem Ersatz abgedeckt.
- [ ] Lizenzdatei mit den Schriften ausgeliefert.

## Verwandte Seiten

- [Stylesheets laden](stylesheets-laden.md)
- [Performance von Themes](theme-leistung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
