---
title: Von Exponential gesetzte Cookies
audience: government, admin, developer, project-manager
category: public-sector
order: 190
summary: Für öffentliche Stellen, die ihren Cookie-Hinweis schreiben - die Cookies, die der Kernel und die Administrationsdesigns von Exponential setzen (Sitzung, is_logged_in, Einrichtungsassistent, Erkennung mobiler Geräte, Zustand der Administrationsoberfläche), ihr Zweck und wann sie gesetzt werden, der Browserspeicher, den admin4 nutzt, und wie Sie prüfen, was Ihre eigene Website setzt.
keywords: Cookies, Sitzungscookie, eZSESSID, is_logged_in, exp_setup_wizard, eZMobileDeviceDetect, adminNavigationTab, localStorage, funktionale Cookies, unbedingt erforderlich, ePrivacy Artikel 5 Absatz 3, Cookie-Hinweis, öffentlicher Sektor, session cookie, functional cookies, strictly necessary, cookie notice
type: reference
---
# Von Exponential gesetzte Cookies

Diese Seite richtet sich an Datenschutzbeauftragte, Administratoren und Entwickler, die einen Cookie-Hinweis schreiben
oder eine Website anhand der ePrivacy-Regeln prüfen. Der Kernel setzt nur funktionale Cookies; keines davon dient dem
Tracking ([Datenschutzstandards: ePrivacy-Richtlinie](../../../../eng-us/standards/privacy.md#eprivacy-directive-cookies)). Was
Ihr Design und Ihre Erweiterungen hinzufügen, müssen Sie selbst auflisten.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Die Cookies des Kernels](#die-cookies-des-kernels)
2. [Browserspeicher](#browserspeicher)
3. [Was Ihre Website sonst noch setzen kann](#was-ihre-website-sonst-noch-setzen-kann)
4. [Ihre Website prüfen](#ihre-website-prüfen)
5. [Verwandte Seiten](#verwandte-seiten)

## Die Cookies des Kernels

| Cookie | Gesetzt von | Zweck | Wann |
|---|---|---|---|
| Sitzungscookie (`[Session] SessionNamePrefix=eZSESSID`, plus ein Suffix) | `lib/ezsession/classes/ezsession.php` | Anmeldung und Formularzustand; `HttpOnly`, `SameSite=Lax`, `Secure` über HTTPS | wenn eine Sitzung beginnt |
| `is_logged_in` | `kernel/private/classes/ezpkernelweb.php` | lässt HTTP-Caches angemeldete Benutzer überspringen | während der Anmeldung |
| `exp_setup_wizard` | `kernel/classes/expmaintenance.php` | der eigene Lauf des Einrichtungsassistenten | nur während der Installation |
| `eZMobileDeviceDetect` | `kernel/private/classes/ezpmobiledeviceregexpfilter.php` | merkt sich die Erkennung mobiler Geräte | nur mit `[SiteAccessSettings] DetectMobileDevice=enabled` (Voreinstellung `disabled`) |
| `adminNavigationTab` und andere Cookies für den Oberflächenzustand | Skripte von admin4 (`node_tabs.js`, `contentstructuremenu_dynamic.js`, `ezajaxsubitems_expdatatable.js`) | geöffneter Reiter, Zustand des Baums, Tabellenspalten | nur in der Administration |

Anonyme Besucher einer öffentlichen Seite erhalten in der Regel kein Sitzungscookie, bis etwas eine Sitzung braucht
(ein Formular, eine Anmeldung); `[Session] ForceStart=enabled` würde für jeden Besucher eine öffnen
([Sitzungen](../../../anleitungen/sitzungen.md)).

## Browserspeicher

admin4 speichert Anzeigeoptionen (Farbmodus, Seitenspalten, Menügruppen) für einen angemeldeten Benutzer als
Benutzereinstellungen und auf Seiten ohne angemeldeten Benutzer im `localStorage` des Browsers
(`design/admin4/javascript/a4_head.js`). Das betrifft die Administration, nicht die öffentliche Website.

## Was Ihre Website sonst noch setzen kann

- Die Skripte Ihres Designs (eine Sprachwahl, ein Umschalter für die Schriftgröße).
- Erweiterungen: Webanalyse (`bcwebsitestatistics`), CAPTCHAs (`recaptcha`, `hcaptcha`), Karten, eingebettete Videos,
  Social Login. Diese können Cookies von Drittanbietern setzen; siehe „Erweiterungen von Drittanbietern“.
- Ein Service Worker oder Cache in Ihrem Design.

## Ihre Website prüfen

1. Öffnen Sie ein neues privates Browserfenster und besuchen Sie einige öffentliche Seiten, ohne sich anzumelden.
2. Listen Sie in den Entwicklerwerkzeugen (Anwendung bzw. Speicher) Cookies und lokalen Speicher pro Host auf.
3. Senden Sie ein Formular ab, melden Sie sich an und wieder ab; listen Sie erneut auf.
4. Wiederholen Sie das auf jedem Siteaccess; halten Sie das Ergebnis mit Datum in Ihrer Cookie-Dokumentation fest.

## Verwandte Seiten

- [Datenschutzstandards](../../../../eng-us/standards/privacy.md)
- [Cookie-Banner und Einwilligung](cookie-banner-und-einwilligung.md)
- [Sitzungen und Cookies](../../../../eng-us/help/government/security/sessions-and-cookies.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
