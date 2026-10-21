---
title: "Mit RAD: eine Erweiterung nur mit Einstellungen"
audience: developer, app-creator, designer
category: extension-development
order: 70
summary: Der Assistent für Einstellungs-Extensions schreibt Einstellungen und Event-Listener - seine Themen, die standardmäßig sichere Listener-Klasse und als Alternative das Verschieben vorhandener Einstellungen mit exp:ini move-all.
keywords: RAD, Assistent für Einstellungs-Extensions, Einstellungen, Event-Listener, Bild-Aliase, View-Cache, Informationssammlung, Trigger, Siteaccess-Einstellungen, Icon-Thema, settings extension wizard, setup/settingsextension, settings extension, event listeners, image aliases, viewcache, collect, triggers, siteaccess settings, extension roots, icon theme, exp:ini move-all
type: help
---
# Mit RAD: eine Erweiterung nur mit Einstellungen

Tutorial 7. Der **Assistent für Einstellungs-Extensions** (`/setup/settingsextension`) schreibt Erweiterungen, die
überwiegend aus Einstellungen bestehen: Bild-Aliase, Event-Listener, Regeln für den View-Cache, Informationssammlung,
Trigger-Operationen, Siteaccess-Einstellungen, zusätzliche Wurzeln für Erweiterungen, Icon-Themen und mehr. Die Ausgabe
unten wurde vom Generator des Assistenten (`expSettingsExtensionWizard::files()`) mit zwei ausgewählten Event-Listenern
erzeugt; jede PHP-Datei hat `php -l` bestanden.

## Die Themen

Jedes Thema schreibt eine Einstellungsdatei (der Assistent nennt sie):

| Thema | Schreibt nach | Gedacht für |
|---|---|---|
| Bild-Aliase | `image.ini` | „Eine benannte Größe, mit den Filtern, die sie erzeugen.“ |
| Event-Listener | `site.ini` | „Ein Callback auf etwas, das der Kernel ankündigt.“ |
| Regeln zum Leeren des View-Caches | `viewcache.ini` | „Was sonst noch neu erzeugt werden muss, wenn ein Objekt veröffentlicht wird.“ |
| Informationssammlung | `collect.ini` | „Was passiert, wenn ein Besucher ein aus Inhalten gebautes Formular ausfüllt.“ |
| Trigger-Operationen | `workflow.ini` | „An welche Vorgänge ein Workflow gebunden werden darf.“ |
| Siteaccess-Einstellungen | `site.ini` | „Einstellungen, die nur für einen Siteaccess gelten, bei der Extension aufbewahrt.“ |
| Zusätzliche Extension-Wurzeln | `site.ini` | „Ein anderer Ort als extension/, an dem Extensions liegen können.“ |
| Icon-Thema | `icon.ini` | „Ein Satz Icons, den eine Extension mitbringt.“ |

Dazu kommen die Listener-Klasse, API-Beispiele, README, Metadaten, die Composer-Datei, `.gitignore` und die Lizenz.

## Event-Listener

Mit den Events `content/state/assign` und `response/output` und der Klasse `mysiteSettingsListeners` schrieb der
Assistent:

```text
.gitignore
LICENSE
README.md
classes/mysitesettingslisteners.php
composer.json
doc/examples.php
extension.xml
ezinfo.php
settings/site.ini.append.php
```

```ini
[Event]
Listeners[]=content/state/assign@mysiteSettingsListeners::contentStateAssign
Listeners[]=response/output@mysiteSettingsListeners::responseOutput
```

Die Listener-Klasse weiß, welcher Art jedes Event ist, und ist sicher, bis Sie sie ausschreiben:

```php
/**
 * response/output
 *
 * The whole page, about to be sent. Whatever is returned is what the
 * browser gets.
 *
 * A filter event. Whatever this returns is what the rest of the
 * system uses, so returning nothing destroys the value. The first
 * argument is the value; anything after it is context.
 */
public static function responseOutput( $value = null )
{
    // Not written yet. Handing the value back unchanged leaves the
    // system working exactly as it did before this listener existed.
    return $value;
}
```

Der Benachrichtigungs-Listener (`contentStateAssign()`) gibt nichts zurück, mit dem Hinweis „What this returns is
ignored, and nothing waits for it either - anything slow here is felt by whoever caused it“ (der Rückgabewert wird
ignoriert, und es wartet auch nichts darauf; alles Langsame hier spürt der, der es ausgelöst hat). Füllen Sie sie aus wie
in [Events, Listener und Filter](../ereignisse-und-filter.md).
Beachten Sie, dass `content/state/assign` mit `( $objectID, $selectedStateIDList )` benachrichtigt wird; geben Sie Ihrer
Methode beide Parameter.

## Stattdessen vorhandene Einstellungen verschieben

Wenn die Einstellungen bereits in `settings/siteaccess/<sa>/` oder `settings/override/` existieren, baut der
Einstellungsbefehl die Erweiterung daraus und behält dabei jeden wirksamen Wert bei:

```bash
./console exp:ini move-all siteaccess:mysite extension:mysite_settings:siteaccess:mysite --create-extension --dry-run
```

Siehe [Einstellungen, die in einer Erweiterung liegen](../../teil-2-builder/erweiterungseinstellungen-fuer-builder.md).

## Einschalten

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_settings global
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=ini
```

## Verwandte Seiten

- Zurück: [Mit RAD: eine Design-Erweiterung](rad-design-erweiterung.md)
- Weiter: [Mit RAD: Handler und Kernel-Overrides](rad-handler-und-kernel-overrides.md)
- [Einstellungen in einer Erweiterung](../einstellungen-in-einer-erweiterung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
