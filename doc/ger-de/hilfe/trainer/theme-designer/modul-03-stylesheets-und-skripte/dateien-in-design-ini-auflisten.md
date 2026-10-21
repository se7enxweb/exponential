---
title: "Lektion 3.1: Stylesheets und Skripte in design.ini aufführen"
audience: trainer, designer, developer
category: training
order: 31
summary: Die Teilnehmenden fügen ihrem Theme über design.ini (FrontendCSSFileList, FrontendJavaScriptList) ein Stylesheet und ein Skript hinzu, laden sie mit ezcss_load und ezscript_load und fordern mit ezcss_require eine Datei aus einem einzelnen Template an. 45 Minuten.
keywords: theme-design, stylesheet, JavaScript, skript, design.ini, CSSFileList, FrontendCSSFileList, BackendCSSFileList, JavaScriptList, FrontendJavaScriptList, ezcss_load, ezscript_load, ezcss_require, ezscript_require, ezjsc::jquery, theme design
type: help
---
# Lektion 3.1: Stylesheets und Skripte in design.ini aufführen

In Exponential werden Stylesheets und Skripte nicht von Hand im Pagelayout verlinkt. Sie werden in Listen in
`design.ini` benannt, und das Pagelayout bittet den ezjscore-Packer, die Listen zu laden. Ein Theme, das sich daran
hält, lässt sich von anderen Erweiterungen ergänzen, in wenige Dateien packen und unter einer Content Security Policy
betreiben. Diese Lektion richtet die Listen von `training_theme` ein.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 3: Stylesheets und Skripte](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Designer und Frontend-Entwickler |
| Voraussetzungen | [Modul 2](../modul-02-templates-overrides-und-das-pagelayout/README.md): `training_theme` mit einem Sprunglink und einem Breadcrumb |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Stylesheet- und Skriptlisten von `design.ini` nennen und sagen, welche Designs welche Liste lesen;
- den Listen ihres Themes eine Datei hinzufügen und sie in den Ordner `stylesheets/` oder `javascript/` des Designs
  legen;
- die Listen in einem Pagelayout mit `ezcss_load` und `ezscript_load` laden;
- mit `ezcss_require` oder `ezscript_require` eine Datei aus einem einzelnen Template anfordern.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Stylesheets und Skripte für Ihre Website](../../../../../eng-us/help/creators/website-creators/stylesheets-and-scripts.md).

1. **Die Listen** (5 Minuten). `settings/design.ini`:

   | Einstellung | Enthält |
   |---|---|
   | `[StylesheetSettings] FrontendCSSFileList[]` | Stylesheets für öffentliche Designs |
   | `[StylesheetSettings] BackendCSSFileList[]` | Stylesheets für Administrationsdesigns (Erweiterungen fügen ihre hier hinzu) |
   | `[StylesheetSettings] CSSFileList[]` | die ältere Liste für beide; in `design.ini` zugunsten der beiden oberen als veraltet markiert |
   | `[JavaScriptSettings] FrontendJavaScriptList[]`, `BackendJavaScriptList[]`, `JavaScriptList[]` | dasselbe für Skripte |

   Ein Dateiname wird in `stylesheets/` oder `javascript/` entlang der Design-Kette gesucht. `ezjsc::jquery` ist das
   eigene jQuery von ezjscore; ein Theme liefert keine Kopie mit.
2. **Sie laden** (4 Minuten). Die `page_head.tpl` und das `pagelayout.tpl` des Assistenten tun das bereits:

   ```
   {ezcss_load( array( ezini( 'StylesheetSettings', 'CSSFileList', 'design.ini' ),
                       ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' ) ) )}
   ...
   {ezscript_load( array( 'ezjsc::jquery',
                          ezini( 'JavaScriptSettings', 'JavaScriptList', 'design.ini' ),
                          ezini( 'JavaScriptSettings', 'FrontendJavaScriptList', 'design.ini' ) ) )}
   ```

   Skripte werden am Ende des Body geladen, Stylesheets im Head.
3. **Eine Datei für ein Template** (4 Minuten). Ein View-Template, das eine Datei nur auf manchen Seiten braucht, ruft
   `{ezcss_require( 'article.css' )}` auf. Die View läuft vor dem Pagelayout, also nimmt das `ezcss_load` des
   Pagelayouts die Datei dann mit auf. Die vier Funktionen sind in
   `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php` definiert.
4. **Warum kein `<link>` von Hand** (2 Minuten). Ein von Hand geschriebener Link umgeht den Packer (eine Anfrage pro
   Datei, kein Änderungszeitpunkt im Namen) und lässt sich über Einstellungen weder ergänzen noch entfernen.

### Häufige Fragen

**Sollte ein Theme noch `CSSFileList[]` verwenden?** Der Assistent schreibt es, und es funktioniert; neue Themes und
überarbeitete Heads verwenden `FrontendCSSFileList[]` und `FrontendJavaScriptList[]`. In dieser Lektion ergänzen die
Teilnehmenden die Frontend-Listen.

**In welcher Reihenfolge werden die Dateien geladen?** In der Reihenfolge der Listen, wie das Pagelayout sie übergibt,
und innerhalb einer Liste in der Reihenfolge, in der die Einstellungsdateien Einträge hinzufügen (Erweiterungen in
ihrer Ladereihenfolge).

**Kann eine andere Erweiterung den Seiten meines Themes eine Datei hinzufügen?** Ja, indem sie in ihrer eigenen
`design.ini` dieselbe Liste ergänzt. Das ist ein Grund, die Listen zu verwenden.

### Stolperfallen

- **Die Datei im falschen Ordner.** `FrontendCSSFileList[]=training.css` sucht in `stylesheets/`, nicht im
  Stammordner des Designs. Eine fehlende Datei hinterlässt eine Warnung in der Debug-Ausgabe und wird übersprungen.
- **Den INI-Cache vergessen.** Ein neuer Listeneintrag verlangt `--clear-tag=ini`; eine neue Datei zusätzlich die
  Caches des Packers und der Templates (Lektion 3.3).
- **Ein Pfad mit `stylesheets/` in der Liste.** Schreiben Sie den Namen relativ zu `stylesheets/`: `training.css` oder
  `parts/menu.css` für einen Unterordner.

## Praktische Übung

Auf Ihrer Schulungsinstallation. `D` steht für `extension/training_theme/design/training_theme`.

1. Öffnen Sie `extension/training_theme/settings/design.ini.append.php` und lesen Sie die Blöcke `[StylesheetSettings]`
   und `[JavaScriptSettings]`, die der Assistent geschrieben hat.
2. Fügen Sie in `[StylesheetSettings]` hinzu:

   ```ini
   FrontendCSSFileList[]=training.css
   ```

3. Legen Sie `D/stylesheets/training.css` mit einem Stil für den Sprunglink und den Breadcrumb aus Lektion 2.2 an:

   ```css
   .skip-link { position: absolute; inset-inline-start: -999em; }
   .skip-link:focus { position: static; }
   .training_theme-path { margin-block: 1rem; font-size: 0.9rem; }
   ```

4. Legen Sie `D/stylesheets/article.css` mit einer sichtbaren Regel an, zum Beispiel
   `.training_theme-article h1 { font-size: 2.5rem; }`.
5. Fügen Sie in `D/override/templates/full/article.tpl` als erste Zeile ein: `{ezcss_require( 'article.css' )}`.
6. Leeren Sie die Caches: `php bin/php/ezcache.php --clear-tag=ini`, `php bin/php/ezcache.php --clear-id=ezjscore-packer`,
   `php bin/php/ezcache.php --clear-tag=template` und `php bin/php/ezcache.php --clear-tag=content`.
7. Öffnen Sie eine Ordnerseite von `<site>`. Suchen Sie im Seitenquelltext den Stylesheet-`<link>` und öffnen Sie die
   Datei, die er nennt. Durchsuchen Sie sie nach `skip-link`.
8. Öffnen Sie eine Artikelseite und öffnen Sie deren Stylesheet-Datei auf dieselbe Weise. Durchsuchen Sie sie nach
   `training_theme-article`.
9. Drücken Sie auf einer Seite einmal die Tabulatortaste: Der Sprunglink wird sichtbar.

## Erwartetes Ergebnis

- Schritt 7: Die Seite verlinkt ein gepacktes Stylesheet, dessen Inhalt die Regeln von `training.css` enthält.
- Schritt 8: Auf einer Artikelseite enthält das gepackte Stylesheet auch die Regel von `article.css`; auf einer
  Ordnerseite nicht.
- Schritt 9: Der Sprunglink ist verborgen, bis er den Fokus erhält.
- Der Trainer prüft die `design.ini.append.php` einer Person und die beiden gepackten Dateien am Projektor.

## Quiz

1. Zu welcher Liste fügt ein öffentliches Theme sein Stylesheet hinzu?
   A. `BackendCSSFileList[]` B. `FrontendCSSFileList[]` C. `AdminComponentCSSFileList[]` D. `CustomHosts[]`
2. In welchem Ordner des Designs sucht `FrontendJavaScriptList[]=menu.js` nach der Datei?
3. Warum erreicht `ezcss_require( 'article.css' )` in einem View-Template das `ezcss_load` des Pagelayouts?
4. Warum sollte ein Theme keine eigene Kopie von jQuery mitliefern?
5. Nennen Sie zwei Gründe gegen ein von Hand geschriebenes `<link rel="stylesheet">` im Pagelayout.

<details>
<summary>Antworten</summary>

1. **B.** Die Backend-Liste ist für Administrationsdesigns
   ([Stylesheets und Skripte für Ihre Website](../../../../../eng-us/help/creators/website-creators/stylesheets-and-scripts.md)).
2. In `javascript/` der Designs entlang der Kette.
3. Das View-Template läuft vor dem Pagelayout, und seine angeforderten Dateien werden gesammelt; `ezcss_load` nimmt
   sie dann mit auf.
4. ezjscore liefert eines mit (`ezjsc::jquery`), sodass alle Erweiterungen dieselbe Version verwenden.
5. Zwei beliebige: Er wird nicht gepackt (eine Anfrage pro Datei), er trägt keinen Änderungszeitpunkt im Namen, andere
   Erweiterungen und Einstellungen können ihn nicht ändern, und er folgt nicht der Design-Kette.

</details>

## Weiterführende Literatur

- [Stylesheets und Skripte für Ihre Website](../../../../../eng-us/help/creators/website-creators/stylesheets-and-scripts.md): die
  Listen, der Packer und die Checkliste.
- [jQuery 4 und die Entfernung von YUI](../../../../../eng-us/features/6.0/jquery4-and-yui-removal.md): welches jQuery ezjscore
  bereitstellt.
- [Die Design-Einstellungen, die ein Theme mitliefert](../../../../../eng-us/help/designers/extensions/design-settings-a-theme-ships.md):
  welche Listen ins Theme gehören.

## Verwandte Seiten

- [Modul 3: Stylesheets und Skripte](README.md)
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
