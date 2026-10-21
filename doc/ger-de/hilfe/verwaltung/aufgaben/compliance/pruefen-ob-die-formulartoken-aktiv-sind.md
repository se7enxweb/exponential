---
title: Prüfen, ob die Formulartoken aktiv sind
audience: government, admin
category: public-sector
order: 390
summary: Schritt für Schritt - bestätigen, dass die Erweiterung ezformtoken, die jedes Formular von Exponential gegen Cross-Site Request Forgery schützt, auf allen Siteaccesses aktiv ist, und was zu tun ist, wenn sie fehlt.
keywords: Formulartoken, CSRF, ezformtoken, ActiveExtensions, exp:ini where, Setup Extensions, Formular abgelaufen, öffentlicher Sektor, form tokens, form expired, public sector
type: help
---
# Prüfen, ob die Formulartoken aktiv sind

Für Administratoren, beim Go-live und nach Upgrades oder Änderungen an Einstellungen. Ohne `ezformtoken` könnte eine
andere Site den Browser eines angemeldeten Redakteurs dazu bringen, zu veröffentlichen, zu löschen oder Rollen zu
ändern ([CSRF und Formulartoken](../../../../../eng-us/help/government/security/csrf-and-form-tokens.md)).

## Inhalt

1. [Schritte](#schritte)
2. [Wenn sie fehlt](#wenn-sie-fehlt)
3. [Verwandte Seiten](#verwandte-seiten)

## Schritte

1. Welche Dateien die aktiven Erweiterungen setzen, und der wirksame Wert:

   ```bash
   php bin/php/console exp:ini where site.ini/ExtensionSettings/ActiveExtensions --allow-root-user
   ```

   `ezformtoken` muss in der Liste stehen.
2. Oder in der Administration: **Setup > Extensions** zeigt sie als aktiv.
3. Sehen Sie sich das HTML eines Formulars im Browser an (zum Beispiel die Anmeldeseite oder ein
   Bearbeitungsformular): Es enthält ein verstecktes Feld mit dem Formulartoken.
4. Prüfen Sie jeden Siteaccess, der eigene `ActiveExtensions[]` oder `ActiveAccessExtensions[]` setzt.

## Wenn sie fehlt

1. Fügen Sie sie in `settings/override/site.ini.append.php` hinzu:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=ezformtoken
   ```

2. Erzeugen Sie die Autoloads der Erweiterungen neu und leeren Sie die Caches: `php bin/php/ezpgenerateautoloads.php -e`
   und `php bin/php/ezcache.php --clear-all --allow-root-user`, dann laden Sie PHP neu (oder Deployment auf Velocity).
3. Finden Sie heraus, wie sie verloren ging (ein Override, das mit einer leeren Zeile `ActiveExtensions[]` beginnt,
   ersetzt die ganze Liste), und halten Sie den Vorfall fest.

## Verwandte Seiten

- [CSRF und Formulartoken](../../../../../eng-us/help/government/security/csrf-and-form-tokens.md)
- [13.8 Formulartoken](../../../../installation/13-sicherheitshaertung.md#138-formular-tokens-gegen-cross-site-request-forgery)
- [Seite für abgelaufene Formulare](../../../../../eng-us/features/6.0/form-expired-page.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
