---
title: "Das Design registrieren: DesignExtensions[]"
audience: designer, developer
category: theme-extensions
order: 120
summary: Die eine Einstellung, die das Template-System im Design-Ordner einer Theme-Erweiterung suchen lässt - design.ini [ExtensionSettings] DesignExtensions[] -, wie Sie sie schreiben, wie Sie prüfen, dass sie wirkt, und was ohne sie schiefgeht.
keywords: DesignExtensions, design.ini, ExtensionSettings, Design-Ordner, Design registrieren, Template-Suche, Design-Erweiterung, design folder, register design, template lookup, design extension
type: help
---
# Das Design registrieren: DesignExtensions[]

Diese Seite ist für Designer, deren Templates nicht gefunden werden, obwohl die Dateien vorhanden sind. Eine aktive
Erweiterung wird nicht automatisch nach Designs durchsucht: Sie muss sich **als Design-Erweiterung registrieren**.

## Die Einstellung

`settings/design.ini` erklärt es in seinem eigenen Kommentar: Fügen Sie den Namen der Erweiterung zu
`DesignExtensions[]` hinzu, um das Design automatisch aus der Erweiterung zu erhalten, sobald sie eingeschaltet ist. In
Ihrem Theme:

```php
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
DesignExtensions[]=mytheme

*/ ?>
```

gespeichert als `extension/mytheme/settings/design.ini.append.php`. Der Wert ist der **Name der Erweiterung (des
Ordners)**, nicht der Design-Name. Jeder Ordner `design/<name>/` unter der Erweiterung steht dann als Design zur
Verfügung.

`cjw_themes_jumper` tut genau das, mit einem Kommentar, der sein Design nennt:

```ini
[ExtensionSettings]
# The design "starter" (design/starter/) of this extension
DesignExtensions[]=cjw_themes_jumper
```

## Schritt für Schritt

1. Legen Sie die Datei oben in Ihrem Theme an.
2. Aktivieren Sie die Erweiterung ([Ein Theme aktivieren](eine-theme-erweiterung-aktivieren.md)).
3. Wählen Sie das Design für einen Siteaccess ([Die Design-Einstellungen, die ein Theme mitliefert](design-einstellungen-eines-themes.md)).
4. Leeren Sie den INI- und den Template-Cache:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   php bin/php/ezcache.php --clear-id=template,template-override --allow-root-user
   ```

5. Prüfen Sie, was gilt:

   ```bash
   ./console exp:ini where design.ini/ExtensionSettings/DesignExtensions --allow-root-user
   ```

   Erwartet: die `settings/design.ini.append.php` Ihrer Erweiterung unter den Dateien und Ihr Erweiterungsname im
   geltenden Wert.

6. Öffnen Sie eine Seite des Siteaccess. Bei eingeschaltetem Template-Debugging zeigen die Pfadkommentare der Templates
   Dateien aus `extension/mytheme/design/...` ([Ein Design debuggen](../theme-design/ein-design-debuggen.md)).

## Anzeichen, wenn sie fehlt

| Sie sehen | Warum |
|---|---|
| Die Website zeigt das Standard-Design, obwohl `SiteDesign=mytheme` gilt | Der Design-Ordner wird nicht durchsucht |
| Manche Templates des Themes funktionieren, andere nicht | Eine andere Erweiterung registriert ein Design desselben Namens |
| Eine neue Template-Datei wird ignoriert | Der Cache der Template-Overrides hat noch die alte Liste; leeren Sie `template-override` |

## Verwandte Seiten

- [Die Design-Kaskade](../theme-design/die-design-kaskade.md)
- [Design-Erweiterungen](../theme-design/design-erweiterungen.md)
- [Ladereihenfolge und welches Theme gewinnt](ladereihenfolge-von-themes.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
