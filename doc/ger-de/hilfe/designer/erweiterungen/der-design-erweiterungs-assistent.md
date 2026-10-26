---
title: "Der Design-Erweiterungs-Assistent (Setup > RAD)"
audience: designer, developer
category: theme-extensions
order: 240
featured: true
summary: Mit dem Design-Erweiterungs-Assistenten unter /setup/designextension in wenigen Minuten eine vollständige Theme-Erweiterung erzeugen - jedes Feld, die Dateivorschau, In extension/ anlegen und Als ZIP herunterladen - Schritt für Schritt.
keywords: Design-Erweiterungs-Assistent, RAD, setup/designextension, Theme erzeugen, Gerüst, In extension/ anlegen, Als ZIP herunterladen, Vorschau aktualisieren, Basis-Design, design extension wizard, generate theme, skeleton, base design
type: help
---
# Der Design-Erweiterungs-Assistent (Setup > RAD)

Diese Seite ist für Designer, die ein funktionierendes Gerüst einer Theme-Erweiterung wollen, ohne es abzutippen. Der
Assistent schreibt den Ordner, das Design, die Einstellungen, die Metadaten, ein README, eine LICENSE und eine
`.gitignore` und zeigt jede Datei, bevor er etwas schreibt.

## Bevor Sie beginnen

- Ein Administratorkonto mit der Richtlinie `setup/setup` (die Funktion der View).
- Eine Entwicklungsinstallation, keine Live-Website: Der Assistent schreibt nach `extension/`.
- Ein Name für das Theme ([Ihre Theme-Erweiterung benennen](ihre-theme-erweiterung-benennen.md)).

## Schritt für Schritt

1. Öffnen Sie **Setup > RAD** (`/setup/rad`) und wählen Sie das Werkzeug für Design-Erweiterungen, oder gehen Sie direkt
   zu `/setup/designextension`.
2. Füllen Sie unter **Was es ist** aus:

![Der Design-Erweiterungs-Assistent: was das Theme ist, was hineinkommt, und die Vorschau jeder Datei, bevor sie geschrieben wird](../../bilder/designers/design-extension-wizard.webp)

   | Feld | Was Sie eingeben |
   |---|---|
   | **Name der Extension** | Kleinbuchstaben, Ziffern und Unterstriche, drei bis einundvierzig Zeichen, beginnend mit einem Buchstaben. Er „wird zu extension/<name> und design/<name>“. |
   | **Titel** | Wie die Administrationsoberfläche es nennt; leer gelassen, wird er aus dem Namen gebildet |
   | **Zusammenfassung** | Ein Satz für das README und `composer.json` |
   | **Autor**, **Composer-Vendor**, **Version** | Ihr Name oder Ihre Organisation, Ihr Packagist-Vendor, die erste Version |
   | **Lizenz** | Eine der in `package.ini [LicenseSettings]` eingerichteten Lizenzen |
   | **Greift zurück auf** | Das Basis-Design: `standard` oder ein auf der Installation vorhandenes Design (`admin3`, `admin2`, `admin`, `base`, `ezwebin`) |
   | **Siteaccess** | Nur verwendet, wenn der Teil für Siteaccess-Einstellungen angehakt ist |

3. Haken Sie unter **Was hineinkommt** die gewünschten Teile an ([Die Teile des Design-Assistenten](die-teile-des-design-assistenten.md)).
   **Alle anhaken** und **Keine anhaken** helfen dabei.
4. Drücken Sie **Vorschau aktualisieren**. **Jede Datei, bevor sie geschrieben wird** listet jeden Pfad mit seiner Größe;
   klicken Sie auf eine Überschrift, um die Datei zu lesen. **Alle öffnen** und **Alle schließen** klappen sie auf oder zu.
5. Wählen Sie:
   - **In extension/ anlegen** schreibt die Dateien nach `extension/<name>`. Der Assistent überschreibt nie eine
     vorhandene Erweiterung und schreibt nichts außerhalb von `extension/<name>`.
   - **Als ZIP herunterladen** gibt Ihnen dieselben Dateien als `<name>.zip`, ohne etwas zu schreiben
     ([Das Ergebnis des Assistenten als ZIP herunterladen](design-assistent-als-zip-herunterladen.md)).
6. Nach dem Anlegen meldet die Seite, dass nach extension/<name> geschrieben wurde, und fordert Sie auf, die Erweiterung
   mit den Zeilen darunter einzuschalten und dann die Caches zu leeren. Die Zeilen lauten:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=<name>
   ```

7. Aktivieren Sie das Theme ([Ein Theme aktivieren](eine-theme-erweiterung-aktivieren.md)), erzeugen Sie die Autoloads neu,
   leeren Sie die Caches und setzen Sie `SiteDesign=<name>` für einen Test-Siteaccess.

## Was Sie danach prüfen

- `php -l extension/<name>/ezinfo.php` und `xmllint --noout extension/<name>/extension.xml` laufen fehlerfrei durch.
- Ab Exponential 6.0.15 schreibt der Assistent `extra.ezpublish-legacy-extension-name` in `composer.json`, eine
  funktionierende `page_head.tpl` und `pagelayout.tpl`, die Frontend-Listen für Stylesheets und Skripte und, mit
  **Template-Operatoren**, `autoloads/eztemplateautoload.php`. Bei einem Theme, das ein älterer Assistent erzeugt hat:
  - `composer.json`: Fügen Sie unter `extra` `"ezpublish-legacy-extension-name": "<name>"` hinzu
    ([Der Composer-Installer und der Ordnername](composer-installer-und-erweiterungsname.md)).
  - Die erzeugten `page_head.tpl` und `pagelayout.tpl`: Korrigieren Sie den Titel, geben Sie die Stylesheets und Skripte
    aus und schreiben Sie einen einfachen Zeichensatz
    ([Drei Änderungen, die der erzeugte Kopf und das Layout brauchen](die-teile-des-design-assistenten.md#drei-änderungen-die-der-erzeugte-kopf-und-das-layout-brauchen)).
  - Wenn Sie **Template-Operatoren** angehakt haben, fügen Sie `autoloads/eztemplateautoload.php` hinzu
    ([Template-Operatoren in einem Theme](template-operatoren-in-einem-theme.md)).

## Verwandte Seiten

- [Vom Ergebnis des Assistenten zum ersten Commit](vom-assistenten-zum-ersten-commit.md)
- [RAD-Werkzeuge](../../../funktionen/6.0/rad-werkzeuge.md), [RAD-Sicherheit](../../../bc/6.0/rad-sicherheit.md)
- [Tutorial: eine Theme-Erweiterung anlegen](../theme-design/tutorial-eine-theme-erweiterung-anlegen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
