---
title: E-Mail-Kategorien und Opt-in
audience: government, admin, project-manager, developer
category: public-sector
order: 120
summary: Für öffentliche Stellen, die optionale E-Mails versenden - die notwendigen und optionalen E-Mail-Kategorien von Exponential, warum optionale aus sind, bis eine Person sie einschaltet, Double-Opt-in für Newsletter und Angebote, eigene Kategorien hinzufügen und sicherstellen, dass jede E-Mail eine Kategorie trägt.
keywords: E-Mail-Kategorien, Opt-in, Double-Opt-in, notwendige E-Mail, optionale E-Mail, DefaultOn, AllowDefaultOn, Newsletter, Marketing, setCategory, E-Mail-Gate, Einwilligung, ePrivacy Artikel 13, öffentlicher Sektor, mail categories, double opt-in, essential mail, optional mail, consent
type: help
---
# E-Mail-Kategorien und Opt-in

Diese Seite richtet sich an Administratoren, Kommunikationsteams und Entwickler. Jede E-Mail, die eine
Exponential-Website versendet, durchläuft ein E-Mail-Gate, das ihre **Kategorie** kennt: Notwendige E-Mails gehen immer
hinaus, optionale nur an Personen, die sie eingeschaltet haben
([13.13 E-Mail: Einwilligung und Sperrung](../../../installation/13-sicherheitshaertung.md#1313-mail-einwilligung-und-sperrliste),
[E-Mail-Einstellungen](../../../funktionen/6.0/e-mail-einstellungen.md)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Notwendig und optional](#notwendig-und-optional)
2. [Aus, bis sie eingeschaltet werden](#aus-bis-sie-eingeschaltet-werden)
3. [Double-Opt-in](#double-opt-in)
4. [Eigene Kategorien](#eigene-kategorien)
5. [E-Mails ohne Kategorie](#e-mails-ohne-kategorie)
6. [Verwandte Seiten](#verwandte-seiten)

## Notwendig und optional

| Art | Kategorien (Standardinstallation) | Verhalten |
|---|---|---|
| Notwendig | `security`, `orders`, `legal`, `admin` | immer gesendet; keine Werbung |
| Optional | Inhaltsbenachrichtigungen, Zusammenarbeit und Freigaben, Newsletter, Angebote und Neuigkeiten, Anwendungs- und Systemhinweise | nur gesendet, wenn die Person sie eingeschaltet hat; Fußzeile mit dem Grund und Abmelden mit einem Klick |

## Aus, bis sie eingeschaltet werden

Optionale Kategorien sind standardmäßig aus (`[Category_*] DefaultOn=false`). `DefaultOn=true` wird abgelehnt, sofern
nicht `[CategorySettings] AllowDefaultOn=enabled` gesetzt ist; der Leitfaden für Administratoren warnt davor, es je für
eine optionale Kategorie zu setzen, weil es E-Mails an Menschen senden würde, die nicht darum gebeten haben
([Datenschutzstandards: DSGVO](../../../standards/datenschutz.md#dsgvo)).

## Double-Opt-in

**Newsletter** und **Angebote und Neuigkeiten** erfordern eine Bestätigung: Das Einschalten sendet einen Link, und es
wird nichts versendet, bis die Person bestätigt. Nach `[ConsentSettings] PendingDays` (7) verfällt die Anfrage. Das
Importieren einer anderswo erteilten Einwilligung
(`./console exp:mail:preferences set ... --source=import --wording="..."`) schaltet sie sofort ein und ist nur für
Einwilligungen gedacht, die Sie nachweisen können; schalten Sie auf einem System, das wirklich versendet, nie eine
Double-Opt-in-Kategorie ohne `--no-mail` von der Konsole aus ein.

## Eigene Kategorien

**Setup > E-Mail-Einstellungen > Kategorien > Neue Kategorie**: eine Kennung (nach dem Anlegen unveränderlich), ein Name
und eine Beschreibung, wie die Menschen sie sehen, die Häufigkeiten, die gewählt werden können, und ob eine Bestätigung
verlangt wird. Eine dort angelegte Kategorie ist immer optional und aus, bis eine Person sie einschaltet. Code versendet
darin mit `eZMail::setCategory( '<identifier>' )`. Kategorien aus den Einstellungen stehen in
`settings/mailpreferences.ini [CategorySettings] Categories[]`
([E-Mail-Einstellungen für Administratoren: Kategorien](../../../anleitungen/mail-einstellungen-administrator.md#4-kategorien)).

Benennen Sie Kategorien schlicht nach ihrem Zweck („Baustellenmeldungen für Ihren Bezirk“), damit die Einwilligung
bestimmt ist.

## E-Mails ohne Kategorie

E-Mails, die ohne `eZMail::setCategory()` versendet werden, gehen (aus Kompatibilitätsgründen) ohne Fußzeile und ohne
Abmeldelink hinaus und werden in `var/<vardir>/log/mailgate.jsonl` gezählt; die Statusseite nennt ihren Absender.
Bitten Sie die Entwickler, jeder optionalen E-Mail eine Kategorie zu geben. Prüfen Sie regelmäßig den Status unter
**Setup > E-Mail-Einstellungen**.

## Verwandte Seiten

- [E-Mail-Einstellungen](../../../funktionen/6.0/e-mail-einstellungen.md)
- [E-Mail-Einstellungen für Entwickler](../../../anleitungen/mail-einstellungen-entwickler.md)
- [Einwilligungsnachweise für E-Mail](einwilligungsnachweise-fuer-e-mail.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
