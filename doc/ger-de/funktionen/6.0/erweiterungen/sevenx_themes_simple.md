# sevenx_themes_simple: das Simple-Theme

Diese Seite ist für Website-Entwickler, die das öffentliche Standard-Design von Exponential verwenden oder anpassen.
`sevenx_themes_simple` ist das leichte, responsive Design, das mit der Standardinstallation ausgeliefert wird. Es hat:

- einen Kopfbereich mit Logo, einem mehrstufigen Dropdown-Menü und einem Warenkorb-Symbol sowie eine Fußzeile;
- Templates für Artikel, Blog, Galerie, Produkt und Warenkorb;
- einen Satz Favicons und das Stylesheet `main.css` mit seinem responsiven Begleiter `main.res.css`.

Es baut auf den Klassen von [ezwebin](ezwebin.md) und der Werkzeugleiste von [ezwt](ezwt.md) auf und installiert die auf
ezwebin beruhenden Pakete, die die Standardinstallation braucht. Das Design heißt `simple`; ein Logo und Banner
`Exponential` ersetzen das frühere Produktlogo. Es wurde zuerst am 25. Januar 2024 importiert (1.0.0) und erreichte am
2. Oktober 2026 1.0.21.

## Verwenden

1. Aktivieren Sie es als Design-Extension: `design.ini [ExtensionSettings] DesignExtensions[]=sevenx_themes_simple`.
2. Die Extension trägt Siteaccess-Standards (`ezjscore.ini` und `override.ini`) für die Siteaccesses `site` und
   `sevenx_site_user` (den für `site` seit 1.0.5), was die Standardinstallation erwartet.
3. Um ein Template zu ändern, überschreiben Sie es in Ihrer eigenen Design-Extension, die in `ActiveAccessExtensions` vor
   dieser steht. Das Theme wird von Composer verwaltet: Bearbeiten Sie seine Dateien nicht an Ort und Stelle.

Prüfen, welches Release Sie haben:

```bash
grep Version extension/sevenx_themes_simple/ezinfo.php
```

oder öffnen Sie die Info-Seite (`/ezinfo/about`).

### Den Hinweis „Powered by Exponential“ ausblenden

`page_footer.tpl` gibt den Hinweis aus, sofern nicht das Attribut **Hide powered by** (`hide_powered_by`) des
Seitendesign-Objekts (die Klasse `pagedesign`, die Designs auf Grundlage von ezwebin verwenden) gesetzt ist. Bearbeiten
Sie dieses Objekt in der Administrationsoberfläche; es ist keine Änderung am Template nötig (seit 1.0.4).

## Einstellungen

Stylesheets und Skripte kommen aus der `design.ini` der Extension:

| Datei | Block | Schlüssel | Wert |
|---|---|---|---|
| `design.ini` | `StylesheetSettings` | `CSSFileList[]` | `websitetoolbar.css`, `libs/fontawesome/css/all.min.css`, `magnific-popup.css`, `main.css`, `main.res.css` |
| `design.ini` | `JavaScriptSettings` | `JavaScriptList[]` | `ezjsc::jquery`, `jquery.magnific-popup.js`, `main.js` |

## Wo was liegt

| Pfad in der Extension | Was er enthält |
|---|---|
| `settings/design.ini.append.php` | Die Registrierung der Design-Extension und die Listen der Stylesheets und Skripte oben (die alten Farbdateien von ezwebin sind als Kommentare vorhanden) |
| `settings/siteaccess/site/` und `settings/siteaccess/sevenx_site_user/` | `ezjscore.ini` und `override.ini.append.php` für diese beiden Siteaccesses |
| `design/simple/templates/` | `pagelayout.tpl`, `page_header.tpl`, `page_footer.tpl`, `page_head*.tpl`, `link.tpl` und die Ordner `content`, `error`, `ezinfo`, `menu`, `node`, `parts`, `shop`, `user` |
| `design/simple/stylesheets`, `javascript`, `images` | `main.css`, `main.res.css`, `main.js`, das Logo, das Banner und der Satz Favicons |
| `packages/` | Die Klassenpakete `.ezpkg` (article, blog, event, forums, frontpage, ...), die die Standardinstallation importiert |
| `translations/ger-DE/translation.ts` | Die deutschen Texte der eigenen Texte des Themes |
| `extension.xml`, `ezinfo.php` | Version (1.0.21 zum Zeitpunkt des Schreibens) und Lizenz, gleich gehalten; die Anforderungen sind `ezjscore` und `expui` |

## Was sich in der Zeit von Exponential 6 geändert hat

| Release | Änderung |
|---|---|
| 1.0.0 bis 1.0.2 | Erster Import, Favicons passend zum Standardlogo, Logo im Seitenkopf, Stile der Website-Werkzeugleiste, Template-Überschreibungen für den Standard-Siteaccess, responsive Fotogalerien, Warenkorb-Schaltfläche im Kopfbereich, Billboard-Banner; das Extension-Verzeichnis aus Kompatibilitätsgründen umbenannt |
| 1.0.3, 1.0.4 | Produktname und Logo in Templates, `composer.json` und der Projekt-URL durch Exponential ersetzt; `page_footer.tpl` so geändert, dass sich der Hinweis „Powered by Exponential“ leichter ausblenden lässt |
| 1.0.5, 1.0.6 | Auf der Extension beruhende Standardeinstellungen für den Siteaccess `site` (passend zu dem, was die Standardinstallation von `sevenx_site_user` erwartet); Copyright-Jahr |
| 1.0.7 bis 1.0.10 | Die schwebende Website-Werkzeugleiste verdeckt die Menüs der Website nicht mehr und schiebt den Inhalt nicht mehr um mehrere rem nach unten; Ausrichtung des Warenkorbs und Überlauf eingebetteter Bilder; Innenabstand der Hauptabschnitte |
| 1.0.11 | **Mehrstufiges Dropdown-Menü**: Eine dritte Ebene öffnet sich als Ausklappmenü unter der zweiten Ebene; der aktive Pfad (obere, zweite und dritte Ebene) wird orange hervorgehoben, und die genaue aktuelle Seite ist fett auf hell-orangefarbenem Hintergrund; „aktiv“ und „offen“ sind getrennt, sodass eine tiefe Seite das Menü nicht automatisch aufklappt; ein nach rechts zeigender Umschalter kennzeichnet übergeordnete Einträge; Bild-Templates verwenden standardmäßig das hochgeladene Original und begrenzen die Bildbreite |
| 1.0.12 | Bereinigung des HTML5-Markups: XHTML-Schrägstriche, doppelte Meta-Tags, bedingte Breite und Höhe des Logos und veraltete Attribute entfernt |
| 1.0.14 | Position des Warenkorb-Symbols im Kopfmenü auf kleinen Bildschirmen |
| 1.0.15 | **Fehlerseiten zeigen ihren eigenen Fehler im Titel** ([Einzelheiten](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md#3-fehlerseiten-zeigen-ihren-eigenen-fehler-im-seitentitel)) |
| 1.0.16 | Der Hintergrund der Fußzeile ist ein relativer Pfad, der sich auflöst, wo immer das Extension-Verzeichnis ausgeliefert wird |
| 1.0.17, 1.0.18 | Jeder sichtbare Text ist ein Übersetzungstext mit Deutsch: Produktbeschriftungen, die Fehlerseite, der Warenkorb, die Fußzeile („Powered by“ ist ein Satz mit dem Link als Platzhalter), die Tags von Blogbeiträgen sowie die Zusammenfassung und die Summe der Bestellbestätigung |
| 1.0.19 | Magnific Popup funktioniert unter jQuery 4 (`Array.isArray`, `typeof`, `.on('click')`, `.trigger('focus')`); kein mitgeliefertes Template lädt es heute |
| 1.0.20 | **YUI entfernt**: Die `ezjscore.ini`-Dateien der Siteaccesses tragen keine Bibliothekspfade, CDN-URLs oder Loader-Optionen von YUI mehr, die Stylesheets gestalten den YUI-Kalender nicht mehr (Datumsfelder verwenden den von Exponential UI), und die Ansicht der Sternebewertung lädt nur das jQuery-Skript. Eine neue `extension.xml` wird mitgeliefert |
| 1.0.21 | Die Info-Seite nennt zuerst „1998 - 2026 7x & Exponential Foundation“ |

## Verwandte Seiten

- [ezwebin](ezwebin.md), [ezwt](ezwt.md), [ezstarrating](ezstarrating.md)
- [Entfernung von YUI](../../../bc/6.0/yui-entfernung.md)
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../geschichte/erweiterungen/sevenx_themes_simple.md) und [Versionshinweise](../../../../changelogs/extensions/sevenx_themes_simple.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/sevenx_themes_simple.md)
- Monate: [2025-01](../../../geschichte/erweiterungen/monate/2025-01.md), [2025-08](../../../geschichte/erweiterungen/monate/2025-08.md), [2026-04](../../../geschichte/erweiterungen/monate/2026-04.md), [2026-07](../../../geschichte/erweiterungen/monate/2026-07.md), [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md), [2026-10](../../../geschichte/erweiterungen/monate/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
