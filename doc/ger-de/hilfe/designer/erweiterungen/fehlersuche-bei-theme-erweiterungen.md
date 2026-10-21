---
title: "Fehlersuche bei Theme-Erweiterungen"
audience: designer, admin
category: theme-extensions
order: 460
summary: Anzeichen und Abhilfen bei Theme-Erweiterungen - das Design wird nicht verwendet, ein neues Template wird ignoriert, die Seite Über zeigt keine Version, Composer installiert in den falschen Ordner, ein Operator ist unbekannt, ein Export wird verweigert.
keywords: Fehlersuche, Design nicht verwendet, Template ignoriert, keine Version, falscher Ordner, unbekannter Operator, Export verweigert, Cache, Autoload, troubleshooting, design not used, template ignored, no version, wrong folder, unknown operator, export refused
type: help
---
# Fehlersuche bei Theme-Erweiterungen

Diese Seite ist für Designer und Administratoren, wenn sich ein Theme nicht wie erwartet verhält. Suchen Sie das
Anzeichen, lesen Sie die Ursache, wenden Sie die Abhilfe an. Für Probleme innerhalb von Templates siehe
[Ein Design debuggen](../theme-design/ein-design-debuggen.md) und [Häufige Template-Fehler](../vorlagen/haeufige-template-fehler.md).

| Sie sehen | Wahrscheinliche Ursache | Abhilfe |
|---|---|---|
| Die Website zeigt das Standard-Design | Die Erweiterung ist nicht aktiv oder registriert ihr Design nicht | [Ein Theme aktivieren](eine-theme-erweiterung-aktivieren.md), [Das Design registrieren](das-design-registrieren.md); den INI-Cache leeren |
| Die Administration verwendet plötzlich das Theme | Das Theme setzt `SiteDesign` global | `SiteDesign` in den Siteaccess der Website verschieben ([Die Design-Einstellungen, die ein Theme mitliefert](design-einstellungen-eines-themes.md)) |
| Eine neue `.tpl`-Datei wird ignoriert; die Seite ist leer | Der Cache der Template-Overrides hat noch die alte Liste | `php bin/php/ezcache.php --clear-id=template-override --allow-root-user` |
| Das Template eines anderen Themes gewinnt | Ladereihenfolge | [Ladereihenfolge und welches Theme gewinnt](ladereihenfolge-von-themes.md) |
| Die Seite Über zeigt einen Strich als Version | `ezinfo.php` oder `extension.xml` fehlt sie, oder `extension.xml` hat einen anderen Wert | [Alle Versionsfelder synchron halten](versionsfelder-synchron-halten.md) |
| Composer legt das Theme nach `extension/my-theme` | `extra.ezpublish-legacy-extension-name` fehlt | [Der Composer-Installer und der Ordnername](composer-installer-und-erweiterungsname.md) |
| „Unknown template operator“ für den Operator des Themes | `autoloads/eztemplateautoload.php` oder `ExtensionAutoloadPath[]` fehlt | [Template-Operatoren in einem Theme](template-operatoren-in-einem-theme.md) |
| Eine neue PHP-Klasse wird nicht gefunden | Autoload-Array nicht neu erzeugt oder PHP-Dienst nicht neu geladen | `php bin/php/ezpgenerateautoloads.php -e`, dann PHP-FPM oder Velocity neu laden |
| Texte des Themes bleiben englisch | `TranslationExtensions[]` fehlt, oder der Übersetzungs-Cache | [Übersetzungen in einem Theme](uebersetzungen-in-einem-theme.md) |
| Ein geändertes Stylesheet erreicht den Browser nicht | Das gepackte Stylesheet ist im Cache | Packer- und Template-Block-Cache leeren ([Packer-Cache und Deployment](../theme-design/packer-cache-und-deployment.md)); den Browser hart neu laden |
| Das Exportieren des Themes wird verweigert | Das Exportieren ist aus, oder die Geheimnisprüfung hat etwas gefunden | [Eine Erweiterung als Paket exportieren](eine-erweiterung-als-paket-exportieren.md), [Die Geheimnisprüfung](geheimnispruefung-vor-dem-paketieren.md) |
| Hochladen Ihres Pakets: „existiert bereits“ | Ein Paket dieses Namens liegt auf dieser Installation | Das alte dort entfernen oder Ihres umbenennen |

## Ein allgemeines Vorgehen

1. Leeren Sie die Caches: `php bin/php/ezcache.php --clear-all --allow-root-user`.
2. Erzeugen Sie die Autoloads neu: `php bin/php/ezpgenerateautoloads.php -e`.
3. Laden Sie den PHP-Dienst neu, der die Website ausliefert.
4. Schalten Sie das Template-Debugging für Ihre eigene IP-Adresse ein und lesen Sie die Pfadkommentare.
5. Prüfen Sie `var/log/error.log` und die Debug-Ausgabe der Website.

Bleibt das Problem und halten Sie es für einen Fehler in Exponential, melden Sie es
([Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)).

## Verwandte Seiten

- [Checkliste: Die Theme-Erweiterung ist bereit](checkliste-theme-erweiterung-bereit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
