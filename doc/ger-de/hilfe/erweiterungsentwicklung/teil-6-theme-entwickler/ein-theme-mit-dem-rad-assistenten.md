---
title: Ein Theme mit dem RAD-Assistenten
audience: designer, developer
category: extension-development
order: 30
summary: Beginnen Sie ein Theme mit dem Assistenten Design-Extension unter Setup > RAD und machen Sie aus seinem Gerüst ein echtes Theme - Siteaccess, Frontend-Listen, Basis-Design, Seitenteile, Ansichts-Templates - mit drei Korrekturen.
keywords: RAD, Assistent, Design-Extension, Theme, Gerüst, Pagelayout, Siteaccess, Basis-Design, design extension wizard, theme, /setup/designextension, skeleton, pagelayout, siteaccess, FrontendCSSFileList, base design
type: help
---
# Ein Theme mit dem RAD-Assistenten

Teil 6, Kapitel 3. Am schnellsten beginnen Sie ein Theme mit dem Assistenten **Design-Extension** in der Administration
(**Setup > RAD**, Adresse `/setup/designextension`). Er schreibt in einer Minute ein vollständiges, funktionierendes
Gerüst. Dieses Kapitel nimmt es als Ausgangspunkt und zeigt dann, wie aus dem Gerüst ein Theme wird. Den Assistenten
selbst, Feld für Feld, beschreibt das Tutorial [Mit RAD: eine Design-Erweiterung](../teil-3-erweiterungsentwickler/tutorials/rad-design-erweiterung.md);
den Ablauf, den alle Assistenten teilen, finden Sie in [Der Rundgang durch die RAD-Werkzeuge](../teil-3-erweiterungsentwickler/tutorials/rundgang-durch-die-rad-werkzeuge.md).

## 1. Den Assistenten ausfüllen

| Feld | Für ein Theme |
|---|---|
| Name der Extension | der Maschinenname des Themes, in Kleinbuchstaben: `mytheme` |
| Titel | der lesbare Name: `My theme` |
| Base design | das Design, auf das Ihr Theme für jedes Template zurückfällt, das es nicht hat: meist `base` oder `standard` |
| Was hineinkommt | lassen Sie die Vorgaben (Pagelayout, Seitenteile, Stylesheets, JavaScript, Bilder, Template-Overrides, ezinfo.php); kreuzen Sie **Siteaccess-Einstellungen** an und geben Sie Ihren öffentlichen Siteaccess ein |
| Lizenz | die Lizenz, unter der Sie veröffentlichen werden; GPL-2.0-or-later entspricht dem Kernel |

Sehen Sie sich die Vorschau jeder Datei an und klicken Sie dann auf **In extension/ anlegen** (oder **Als ZIP
herunterladen**, wenn der Webserver dort nicht schreiben darf).

## 2. Drei Korrekturen

Die erzeugten Dateien funktionieren, aber drei Dinge sollten Sie sofort ändern. Alle drei wurden beim Schreiben dieses
Buchs gefunden, indem eine Design-Erweiterung erzeugt und jede Datei gelesen wurde:

1. **Der Siteaccess muss das Design nennen.** Die erzeugte README sagt, das Design werde ohne weitere Einstellungen
   gefunden. Registriert ist es zwar (`DesignExtensions[]`), aber ein Siteaccess verwendet es nur, wenn seine
   `site.ini [DesignSettings]` es nennt. Wenn Sie **Siteaccess-Einstellungen** ankreuzen, wird diese Datei für Sie
   geschrieben; sonst setzen Sie es selbst ([Themes und Siteaccesses](themes-und-siteaccesses.md)).
2. **Verwenden Sie die Frontend-Listen.** Die erzeugte `design.ini.append.php` registriert die Dateien in
   `CSSFileList[]` und `JavaScriptList[]`, die der Kernel als veraltet kennzeichnet. Ändern Sie sie in
   `FrontendCSSFileList[]` und `FrontendJavaScriptList[]`; das erzeugte Pagelayout lädt beide.
3. **Aktivieren Sie die Erweiterung.** Der Assistent nennt Ihnen die Zeile; solange Sie sie nicht hinzufügen, passiert
   nichts.

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mytheme global
php bin/php/ezcache.php --clear-all
```

## 3. Vom Gerüst zum Theme

Das Gerüst hat ein Pagelayout, einen Kopfbereich, eine Fußzeile, ein Stylesheet, ein Skript und einen Override (Artikel
in der Vollansicht). Ein Theme braucht mehr. Arbeiten Sie in dieser Reihenfolge und prüfen Sie die Website nach jedem
Schritt:

| Schritt | Dateien | Hilfe |
|---|---|---|
| Der Rahmen | `templates/pagelayout.tpl`, `page_head.tpl`, `page_header.tpl`, `page_footer.tpl` | [Templates für Builder](../teil-2-builder/templates-fuer-builder.md) |
| Navigation | ein Menü-Template in `templates/parts/`, aus dem Kopfbereich eingebunden | [Inhalte in Templates abrufen](../teil-2-builder/fetch-fuer-builder.md) |
| Vollansichten Ihrer Klassen | `override/templates/full/<class>.tpl` plus je eine Regel | [Template-Overrides](../teil-2-builder/grundlagen-der-template-overrides.md) |
| Listen | `override/templates/line/<class>.tpl` | [View-Modi für Builder](../teil-2-builder/view-modi-fuer-builder.md) |
| Eingebettete Objekte | `override/templates/embed/<class>.tpl` | dasselbe |
| Formulare und Benutzerseiten | Kopien der `user/`- und `content/`-Templates des Kernels, die Sie ändern möchten | [Die Design-Kette](../teil-2-builder/die-design-kette.md) |
| Styles | `stylesheets/site.css`, in mehrere Dateien aufgeteilt, wenn es wächst | [Theme-Assets, der Packer und CSP](theme-assets-der-packer-und-csp.md) |

Kopieren Sie ein Kernel-Template nur dann in Ihr Design, wenn Sie es ändern. Jedes kopierte Template müssen Sie nach
jedem Upgrade mit dem des Kernels vergleichen.

## 4. Unterwegs committen

Stellen Sie das Theme gleich nachdem der Assistent es geschrieben hat unter Git, vor Ihrer ersten Änderung. Dann ist
jeder Schritt oben ein Commit, und Sie können jederzeit sehen, was der Assistent geschrieben und was Sie geändert haben.

```bash
cd extension/mytheme
git init
git add -A
git commit -m "Added: The theme skeleton written by the design extension wizard"
```

## Was schiefgehen kann

| Symptom | Ursache | Lösung |
|---|---|---|
| Die Website sieht unverändert aus | der Siteaccess nennt das Design nicht, oder die Erweiterung ist nicht aktiv | Kapitel [Themes und Siteaccesses](themes-und-siteaccesses.md) |
| Ein neues Template wird nicht verwendet | der Template-Override-Cache | `php bin/php/ezcache.php --clear-id=template,template-override` |
| Das Stylesheet fehlt | es steht in einer veralteten Liste, die das Pagelayout nicht liest, oder der Packer-Cache ist alt | prüfen Sie die Namen der Listen; leeren Sie die Caches |
| Die Seite ist unter dem Kopfbereich leer | ein Fehler in einem Template | schalten Sie Debug-Ausgabe und Template-Debugging ein ([Templates debuggen](../teil-2-builder/templates-debuggen.md)) |

## Verwandte Seiten

- Zurück: [Der Aufbau einer Design-Erweiterung](aufbau-einer-design-erweiterung.md)
- Weiter: [Ein Theme von Hand](ein-theme-von-hand.md)
- [Mit RAD: eine Design-Erweiterung](../teil-3-erweiterungsentwickler/tutorials/rad-design-erweiterung.md)
- [RAD-Werkzeuge](../../../../eng-us/features/6.0/rad-tools.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
