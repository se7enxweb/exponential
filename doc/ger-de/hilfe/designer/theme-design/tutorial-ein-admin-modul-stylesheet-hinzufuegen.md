---
title: "Tutorial: ein Admin-Modul-Stylesheet hinzufügen"
audience: designer, developer
category: theme-design
order: 850
summary: Schritt für Schritt die Administrationsseite einer Erweiterung nach Art von admin4 gestalten: zuerst Komponenten, dann ein Stylesheet im Admin-Design der Erweiterung, registriert in BackendCSSFileList, nur Token, bereit für dunklen Modus und rechts nach links, über den Packer deployt und bei zwei Breiten geprüft.
keywords: Tutorial, Admin-Stylesheet, Admin-Seite einer Erweiterung, BackendCSSFileList, admin4-Komponenten, Token, Admin-CSS, Modulseite, admin4-Design einer Erweiterung, Admin-Seite gestalten, tutorial, admin stylesheet, extension admin page, admin4 components, tokens, admin css, module page, extension design admin4, styling admin page
type: help
---
# Tutorial: ein Admin-Modul-Stylesheet hinzufügen

Dieses Tutorial ist für Designer und Entwickler, die eine Administrationsseite für eine Erweiterung (`myext`) bauen und etwas eigenes CSS
brauchen. Sie verwenden zuerst die gemeinsamen Komponenten und fügen dann ein kleines Stylesheet an der richtigen Stelle hinzu. Planen Sie
eine Stunde ein.

## Inhalt

1. [Schritt 1: mit Komponenten beginnen](#schritt-1-mit-komponenten-beginnen)
2. [Schritt 2: was übrig bleibt](#schritt-2-was-übrig-bleibt)
3. [Schritt 3: das Stylesheet](#schritt-3-das-stylesheet)
4. [Schritt 4: es registrieren](#schritt-4-es-registrieren)
5. [Schritt 5: deployen und prüfen](#schritt-5-deployen-und-prüfen)

## Schritt 1: mit Komponenten beginnen

Bauen Sie die Seite aus `.a4-*`-Klassen: `.a4-page`, `.a4-pagehead`, `.a4-card`, `.a4-table`, `.a4-btn`, `.a4-notice`, `.a4-empty`
([admin4-Komponenten](admin4-komponenten.md)). Sehen Sie sich die Seite hell und dunkel an: Die meisten Seiten brauchen kein eigenes CSS.

## Schritt 2: was übrig bleibt

Angenommen, die Seite zeigt eine Zeitleiste von Importläufen mit einem farbigen Balken pro Lauf, den keine Komponente zeichnet. Das ist der
Teil für Ihr Stylesheet. Schreiben Sie auf, was die Regeln tun müssen, bevor Sie sie schreiben.

## Schritt 3: das Stylesheet

`extension/myext/design/admin4/stylesheets/myext-admin.css`:

```css
/* myext admin pages. Tokens only (doc/eng-us/bc/6.0/admin4-styling.md); light and dark from the tokens. */
.myext-runs { display: grid; gap: var(--a4-space-2); }
.myext-run {
  display: grid;
  grid-template-columns: 10rem minmax(0, 1fr);
  gap: var(--a4-space-3);
  padding-block: var(--a4-space-2);
  border-block-end: 1px solid var(--a4-line);
}
.myext-run-bar {
  block-size: var(--a4-space-2);
  border-radius: var(--a4-radius-s);
  background: var(--a4-accent-soft);
}
.myext-run.is-bad .myext-run-bar { background: var(--a4-bad-bg); }
```

- Ihr eigenes Präfix (`myext-`), eine Komponentenklasse plus höchstens ein Zustand.
- Nur Token: kein `#fff`, kein `12px`; die Breite des Balkens kommt aus `.a4-pct-*`-Klassen daran, nicht aus `style=""`.
- Logische Eigenschaften (`border-block-end`, `padding-block`).

## Schritt 4: es registrieren

`extension/myext/settings/design.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?
[ExtensionSettings]
DesignExtensions[]=myext

[StylesheetSettings]
BackendCSSFileList[]=myext-admin.css
*/ ?>
```

Der Kopf von admin4 lädt `BackendCSSFileList` nach den Komponenten und Modul-Stylesheets, sodass Ihre Regeln sie verfeinern können.

## Schritt 5: deployen und prüfen

```bash
./console exp:velocity deploy --packer --allow-root-user
```

(auf Installationen ohne Velocity: Leeren Sie die INI-, Template-Block- und Content-Caches). Dann:

- [ ] hell und dunkel, bei 1440 px und bei 960 px mit Skalierung 2;
- [ ] kein waagrechter Überlauf, Kontrast mindestens 4,5:1;
- [ ] kein `<style>` und kein `style=""` in den Templates der Seite;
- [ ] eine Oberflächensprache von rechts nach links: Die Seite spiegelt sich.

## Verwandte Seiten

- [admin4-Modul-Stylesheets](admin4-modul-stylesheets.md)
- [admin4-Token](admin4-tokens.md)
- [Der Test der Admin-Gestaltungsregeln](der-test-der-admin-gestaltungsregeln.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
