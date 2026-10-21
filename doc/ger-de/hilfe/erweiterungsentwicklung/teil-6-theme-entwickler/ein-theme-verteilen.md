---
title: Ein Theme verteilen
audience: designer, developer, app-creator
category: extension-development
order: 80
summary: Ein Theme veröffentlichen, das andere installieren können - Lizenzen des Codes, der Schriften, Symbole und Bilder, die es enthält, Metadaten, Composer, Pakete für Inhaltsklassen, Screenshots und eine README - mit einer Checkliste vor dem ersten Release.
keywords: verteilen, Theme, veröffentlichen, Lizenz, Schriften, Symbole, Bilder, Nachweise, Composer, Packagist, Pakete, README, Screenshot, distribute, publish, license, fonts, icons, images, credits, packagist, packages, ezpkg, screenshot
type: help
---
# Ein Theme verteilen

Teil 6, Kapitel 8. Ein Theme ist die sichtbarste Erweiterung überhaupt und die am leichtesten wiederverwendbare. Bevor
Sie eines veröffentlichen, müssen Sie sicher sein, dass Sie alles darin weitergeben dürfen: Code ist nur ein Teil eines
Themes. Dieses Kapitel behandelt, was beim Veröffentlichen eines Themes anders ist; das allgemeine Vorgehen für Releases
beschreibt [Teil 7](../teil-7-veroeffentlichen/README.md).

## Alles in einem Theme hat eine Lizenz

| Teil | Typische Lizenz | Was sie verlangt |
|---|---|---|
| Templates, CSS, JavaScript, die Sie geschrieben haben | Ihre Wahl; GPL-2.0-or-later entspricht dem Kernel | die Lizenzdatei |
| Kernel-Templates, die Sie kopiert und geändert haben | die Lizenz des Kernels, GPL 2 oder später | beibehalten; Ihre Kopie ist GPL |
| Eine CSS- oder JS-Bibliothek (eine Symbolschrift, eine Lightbox) | oft MIT | ihre Lizenzdatei daneben |
| Schriften | oft die SIL Open Font License | ihre Lizenzdatei; die Schrift nicht allein verkaufen |
| Symbole | MIT, CC BY oder die eigene Lizenz des Satzes | ein Nachweis, wo CC BY ihn verlangt |
| Fotos und Illustrationen | alles Mögliche, oft nicht frei | prüfen Sie jedes einzelne; ersetzen Sie alle, die Sie nicht weitergeben dürfen |
| Logos | meist überhaupt nicht lizenziert | liefern Sie einen neutralen Platzhalter aus, nie das echte Logo eines Unternehmens |

Bei den letzten beiden Zeilen gehen Themes schief. Für eine Website gekaufte Demofotos dürfen womöglich nicht
weitergegeben werden. Ein Theme, das mit dem Logo eines Kunden ausgeliefert wird, gibt dieses Logo jedem. Ersetzen Sie
sie durch freie Bilder (mit ihrem Nachweis) oder einfache Platzhalter.

Beachten Sie, dass ein aus dem Kernel kopiertes Template GPL ist, auch wenn der Rest Ihres Themes unter einer anderen
Lizenz steht; die einfachste Wahl für ein Theme ist deshalb GPL-2.0-or-later für alles.

## Nachweise im Theme

- Eine Datei `LICENSE` für das Theme.
- Die Lizenzdatei jeder Bibliothek, jeder Schrift und jedes Symbolsatzes daneben (sevenx_themes_simple hält sein CSS von
  Dritten unter `stylesheets/libs/`).
- Ein Eintrag `Includes` in `ezinfo.php` für jede mitgelieferte Bibliothek oder Schrift, damit die Infoseite sie
  aufführt ([Namensnennung und Copyright-Hinweise](../teil-5-erweitern-forken-zurueckgeben/namensnennung-und-urheberrechtshinweise.md)).
- Ein Abschnitt „Credits“ in der README, der Bilder und ihre Urheber nennt, wo deren Lizenz das verlangt.

## Inhaltsklassen: Pakete

Die Templates eines Themes erwarten Inhaltsklassen (`article`, `blog_post`, `event`). Eine Website, die das Theme
installiert, hat sie womöglich nicht. Liefern Sie sie als Pakete aus: Exportieren Sie jede Klasse als `.ezpkg`-Datei in
den Ordner `packages/` des Themes, wie es sevenx_themes_simple tut, und beschreiben Sie in der README, wie man sie
importiert ([Pakete und der Paketersteller](../teil-7-veroeffentlichen/pakete-ezpkg.md)). Liefern Sie nie einen
Datenbank-Dump aus.

## Einstellungen, die die Website vornehmen muss

Die Website muss Ihr Design immer noch auswählen ([Themes und Siteaccesses](themes-und-siteaccesses.md)). Schreiben Sie
die genauen Zeilen in die README. Liefern Sie `settings/siteaccess/<name>/` im Theme mit, nennen Sie die Namen der
Siteaccesses, die es erwartet.

## composer.json

Ein Theme wird wie jede Erweiterung installiert ([composer.json modernisieren](../teil-4-aktualisieren/composer-json-modernisieren.md)):

```json
{
    "name": "your-account/mytheme",
    "description": "My theme: a clean, responsive theme for Exponential 6 sites, with dark mode.",
    "type": "ezpublish-legacy-extension",
    "license": "GPL-2.0-or-later",
    "keywords": ["exponential", "theme", "design"],
    "require": {
        "php": "^8.0",
        "se7enxweb/ezpublish-legacy-installer": "*"
    },
    "extra": {
        "ezpublish-legacy-extension-name": "mytheme"
    }
}
```

## Die README eines Themes

Die README eines Themes sollte das Theme zeigen. Nehmen Sie auf:

1. einen Screenshot der Startseite und einer Inhaltsseite, im hellen und im dunklen Modus;
2. was es braucht (Inhaltsklassen, andere Erweiterungen wie ezjscore);
3. die Installation: Composer, Aktivierung, die Siteaccess-Einstellungen, der Import der Pakete;
4. was sich über Einstellungen anpassen lässt und wie man darauf eine Variante baut
   ([Themes und Siteaccesses](themes-und-siteaccesses.md), „Ein Theme auf einem Theme“);
5. Nachweise und Lizenzen.

## Vor dem ersten Release

- [ ] Jedes Bild, jede Schrift, jedes Symbol und jede Bibliothek ist für die Weitergabe lizenziert, mit ihrer Lizenzdatei.
- [ ] Kein echtes Firmenlogo, keine Kundeninhalte, keine Fotos echter Personen ohne deren Erlaubnis.
- [ ] Keine Knoten-IDs oder Hostnamen Ihrer eigenen Website in Templates oder Einstellungen.
- [ ] Keine Sicherungsdateien des Editors und keine ungenutzten minimierten Kopien.
- [ ] Funktioniert im hellen und im dunklen Modus und in Smartphone-Breite.
- [ ] Funktioniert unter einer Content Security Policy mit `'self'` ([Theme-Assets, der Packer und CSP](theme-assets-der-packer-und-csp.md)).
- [ ] `ezinfo.php`, `extension.xml` und `composer.json` enthalten Version und Lizenz.
- [ ] Die README hat Screenshots und Installationsschritte.

Veröffentlichen Sie es dann so, wie [Teil 7](../teil-7-veroeffentlichen/README.md) es beschreibt, und sagen Sie den Leuten dort
Bescheid, wo sie nach Themes suchen ([Die Community durch Code wachsen lassen](../teil-5-erweitern-forken-zurueckgeben/die-community-durch-code-wachsen-lassen.md)).

## Verwandte Seiten

- Zurück: [Dunkler Modus und Design-Tokens in Themes](dunkler-modus-und-tokens-in-themes.md)
- Weiter: [Wo die Hilfe für Designer weitergeht](wo-die-hilfe-fuer-designer-weitergeht.md)
- [Lizenzen und wie sie zusammenpassen](../teil-5-erweitern-forken-zurueckgeben/lizenzen-und-kompatibilitaet.md)
- [Pakete](../../../anleitungen/pakete.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
