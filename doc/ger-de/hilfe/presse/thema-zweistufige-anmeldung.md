---
title: "Thema: zweistufige Anmeldung"
audience: press
category: press-media
order: 62
summary: Ein Themenbeitrag über die zweistufige Anmeldung in Exponential - Authenticator-Apps und Codes per E-Mail aus der Erweiterung sevenx_authentication_2fa, was sie schützt, was sie Benutzer an Zeit kostet - mit druckbaren Fakten, Quellen, einem Widerspruch, den Sie im Blick behalten sollten, und den Aussagen, die Sie vermeiden sollten.
keywords: Thema, zweistufige Anmeldung, Zwei-Faktor-Authentifizierung, 2FA, TOTP, Authenticator-App, Code per E-Mail, Social Login, sevenx_authentication_2fa, Presse, story, two-step sign-in, two-factor authentication, authenticator app, e-mail code, social login, press
type: help
---
# Thema: zweistufige Anmeldung

Diese Seite ist für Journalisten und Creator, die über die Kontensicherheit in Content-Management-Systemen schreiben.
Sie behandelt die zweistufige Anmeldung von Exponential: was ein Benutzer sieht, was sie schützt, seit wann es sie gibt
und einen Punkt, in dem sich zwei Quellen widersprechen.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Was neu ist, und seit wann](#was-neu-ist-und-seit-wann)
3. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
4. [Wo sich die Quellen unterscheiden](#wo-sich-die-quellen-unterscheiden)
5. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
6. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)
7. [Wen Sie fragen können](#wen-sie-fragen-können)
8. [Verwandte Seiten](#verwandte-seiten)

## Das Thema in einem Absatz

„After the password, a code from an authenticator app on the phone, or one sent by e-mail: the password alone is then
useless to anyone else“ ([Zweistufige Anmeldung](../../anleitungen/zwei-faktor-anmeldung.md)). Die Funktion stammt aus der
Erweiterung `sevenx_authentication_2fa`, die die `composer.json` des Kernels verlangt und die Installationsprogramme
aktivieren. Die Anleitung benennt den Aufwand ganz nüchtern: „15 minutes to turn it on, 2 minutes per user to set it
up.“ Für Benutzer ist sie freiwillig, sofern der Betreiber der Website sie nicht vorschreibt.

## Was neu ist, und seit wann

| Was | Wann | Quelle |
|---|---|---|
| Die Erweiterung übernommen (Version 1.0.0) | 21. Juli 2026 | [sevenx_authentication_2fa](../../../eng-us/features/6.0/extensions/sevenx_authentication_2fa.md) |
| Die Anleitung, Social-Login-Schaltflächen auf der Anmeldeseite von admin4, Codes, die nur einmal gelten und als fehlgeschlagene Anmeldungen gezählt werden, Geheimnisse, die im Ruhezustand verschlüsselt werden können | 6. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#6-october-2026-two-step-sign-in) |

Die Änderungen am Kernel sind Teil der Entwicklungslinie 6.0.15, die nicht veröffentlicht ist; die Erweiterung wird
eigenständig veröffentlicht.

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| Authenticator-Apps, die RFC 6238 folgen, funktionieren, zum Beispiel Google Authenticator, Authy und Microsoft Authenticator. | [sevenx_authentication_2fa, Was sie bietet](../../../eng-us/features/6.0/extensions/sevenx_authentication_2fa.md#what-it-offers) |
| Ein Einmalcode per E-Mail ist die Alternative, wenn keine App verwendet wird. | ebenda |
| „The password alone never signs in“: Der Benutzer wird erst nach dem richtigen Code angemeldet, mit einer neuen Sitzungs-ID. | [Zweistufige Anmeldung, Was sie schützt](../../anleitungen/zwei-faktor-anmeldung.md#was-sie-schützt) |
| Jeder Code gilt einmal; nach fünf falschen Codes beginnt die Anmeldung erneut mit dem Passwort. | [Zweistufige Anmeldung, Was ein Benutzer sieht](../../anleitungen/zwei-faktor-anmeldung.md#was-ein-benutzer-sieht) |
| Wer die Methode ändert oder abschaltet, muss einen aktuellen Code eingeben, „so someone at an unattended computer cannot weaken the account“. | ebenda |
| Der QR-Code wird auf dem Server erzeugt; der Schlüssel geht an keinen anderen Dienst. | [Zweistufige Anmeldung, Was sie schützt](../../anleitungen/zwei-faktor-anmeldung.md#was-sie-schützt) |
| Jeder Schritt wird in `var/log/auth.log` geschrieben, nie ein Code oder ein Geheimnis. | ebenda |
| Social-Login-Handler gibt es als Gerüst für Google, Facebook, Twitter/X, Instagram, Meta und ID.me. | [sevenx_authentication_2fa, Was sie bietet](../../../eng-us/features/6.0/extensions/sevenx_authentication_2fa.md#what-it-offers) |
| Das Velocity Control Panel hat eine eigene, optionale Zwei-Faktor-Authentifizierung. | [Velocity Control Panel](../../../eng-us/features/6.0/velocity-control-panel.md#two-factor-authentication-optional) |

## Wo sich die Quellen unterscheiden

Die Seite der Erweiterung sagt, der Link in der E-Mail „completes the login when clicked, even in another browser“
([sevenx_authentication_2fa](../../../eng-us/features/6.0/extensions/sevenx_authentication_2fa.md#what-it-offers)); die neuere
Anleitung sagt „the link in the mail only works in the browser where the password was typed“
([Zweistufige Anmeldung](../../anleitungen/zwei-faktor-anmeldung.md#was-sie-schützt)). Beschreiben Sie das Verhalten des
E-Mail-Links nicht, ohne 7x zu fragen, was aktuell gilt.

## Was Sie nicht behaupten sollten

- „Die zweistufige Anmeldung ist Pflicht.“ Sie ist freiwillig, sofern der Betreiber der Website sie nicht vorschreibt.
- „Social Login funktioniert mit jedem Anbieter sofort.“ Die Handler werden als Gerüste beschrieben, die angepasst
  werden müssen.
- „Konten können nicht übernommen werden.“ Sagen Sie, wovor der zweite Schritt schützt, und belassen Sie es dabei.

## Blickwinkel und Ideen für Überschriften

```text
"Two minutes per user: two-step sign-in comes to Exponential"
"The password alone is not enough: how Exponential's second step works"
"An open source CMS that keeps your authenticator key on its own server"
```

## Wen Sie fragen können

| Frage | Fragen Sie | Wo |
|---|---|---|
| Die Erweiterung, die Frage zum E-Mail-Link | 7x | info@se7enx.com oder <https://exponential.earth/about/contact-us> |
| Eine Schwachstelle bei der Anmeldung | vertraulich | security@se7enx.com ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)) |

## Verwandte Seiten

- [Thema: Sicherheit](thema-sicherheit.md)
- [Ihr Konto und die Anmeldung](../konten-und-anmeldung.md)
- [Gesprächspunkte: Sicherheit](../fuersprecher/gespraechspunkte-sicherheit.md)
- [Themenbeiträge: Übersicht](themenbeitraege-uebersicht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
