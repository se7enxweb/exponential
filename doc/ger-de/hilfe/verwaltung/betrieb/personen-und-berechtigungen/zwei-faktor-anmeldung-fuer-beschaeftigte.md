---
title: Zweistufige Anmeldung für Beschäftigte
audience: government, admin, project-manager
category: public-sector
order: 130
summary: Wie eine öffentliche Stelle mit der Extension sevenx_authentication_2fa die zweistufige Anmeldung (Authenticator-App oder E-Mail-Code) für ihre Beschäftigten einschaltet, wie sie sie einführt, ohne jemanden auszusperren, und was zu tun ist, wenn jemand sein Telefon verliert.
keywords: Zwei-Faktor, 2FA, zweistufige Anmeldung, MFA, Mehr-Faktor-Authentifizierung, TOTP, Authenticator-App, E-Mail-Code, Enforce2FA, user2fa, verlorenes Telefon, Konten der Beschäftigten, Sicherheitsrichtlinie, two-factor, two-step sign-in, multi-factor authentication, authenticator app, e-mail code, lost phone, staff accounts, security policy
type: help
---
# Zweistufige Anmeldung für Beschäftigte

Diese Seite richtet sich an Administratoren und Sicherheitsbeauftragte, die für Konten der Beschäftigten mehr als ein Passwort verlangen müssen,
wie es viele Sicherheitsrichtlinien im öffentlichen Sektor tun. Die zweistufige Anmeldung wird von der Extension `sevenx_authentication_2fa`
bereitgestellt; der Leitfaden [Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md) enthält jede Einstellung. Diese Seite behandelt
die Einführung in einer Organisation.

## Inhalt
1. [Was die Beschäftigten erhalten](#was-die-beschäftigten-erhalten)
2. [Was vorhanden sein muss](#was-vorhanden-sein-muss)
3. [Die Einführung](#die-einführung)
4. [Verlorene Telefone und neue Geräte](#verlorene-telefone-und-neue-geräte)
5. [Wichtige Grenzen](#wichtige-grenzen)
6. [Verwandte Seiten](#verwandte-seiten)

## Was die Beschäftigten erhalten

Nach dem Passwort einen 6-stelligen Code aus einer Authenticator-App (jede App für zeitbasierte Einmalcodes) oder aus einer E-Mail. Jeder
Code funktioniert einmal; nach fünf falschen Codes beginnt die Anmeldung wieder mit dem Passwort. Benutzer richten sie unter
**Mein Konto > Zweistufige Anmeldung** (`/user2fa/setup`) ein. Das Ändern oder Ausschalten fragt nach einem aktuellen Code, sodass ein
unbeaufsichtigter Computer das Konto nicht schwächen kann ([Zweistufige Anmeldung, „Was ein Benutzer sieht“](../../../../anleitungen/zwei-faktor-anmeldung.md#was-ein-benutzer-sieht)).

## Was vorhanden sein muss

| Element | Wo | Hinweis |
|---|---|---|
| Die Extension aktiv | `[ExtensionSettings] ActiveExtensions[]` | `composer.json` verlangt sie, und die Installationsprogramme aktivieren sie |
| Der Login-Handler vor `standard` | `site.ini [UserSettings] LoginHandler[]` | `sevenxUser2fa`, dann `standard` |
| Ein Feld an der Benutzerklasse | Klassenattribut des Datentyps *7x 2FA Configuration* | ohne es können Benutzer keinen zweiten Schritt behalten |
| Richtlinien | `user2fa/setup` und `user2fa/verify` an jeder Rolle, die sich anmeldet | eine später angelegte Rolle braucht sie ebenfalls |
| Ein Schlüssel zur Verschlüsselung der Geheimnisse | `[TOTPSettings] SecretKey` in `settings/override/sevenxauthentication2fa.ini.append.php` | halten Sie ihn aus der Versionsverwaltung heraus und in Ihrer Datensicherung der Geheimnisse; geht er verloren, muss jeder die Einrichtung wiederholen |

Einstellungen und Richtlinien stehen im Abschnitt des Leitfadens [Einschalten](../../../../anleitungen/zwei-faktor-anmeldung.md#einschalten).

## Die Einführung

1. **Probieren Sie es auf einer Testkopie** mit Testkonten aus; registrieren Sie beim Ausprobieren nie ein echtes Administratorkonto.
2. **Zuerst freiwillig** (`[General] Enforce2FA=disabled`, der Standard): Bitten Sie Administratoren und das Web-Team, sich zu registrieren.
   Die Kontoseite jedes Benutzers zeigt einen Hinweis, wenn die zweistufige Anmeldung aus ist
   ([Sicherheit und Audit, Schritt 6](../../../../anleitungen/sicherheit-und-audit.md#6-ein-konto-auf-seiner-kontoseite-prüfen-2-minuten)).
3. **Informieren** Sie die Beschäftigten: welche App akzeptiert wird, was bei einem neuen Telefon zu tun ist, wen sie anrufen.
4. **Machen Sie sie zur Pflicht**: `Enforce2FA=enabled` mit `DefaultMethod=totp` (ein Authenticator direkt nach dem Passwort) oder
   `email` (Codes sofort per E-Mail; die E-Mail-Adresse des Kontos muss echt sein). Leeren Sie dann den INI-Cache
   (`php bin/php/ezcache.php --clear-tag=ini`) und stellen Sie unter Velocity bereit (`./console exp:velocity deploy`).
5. **Prüfen** Sie: Melden Sie sich als Testbenutzer an; ein falscher Code wird abgewiesen und erfasst; `var/log/auth.log` enthält jeden
   Schritt, nie einen Code.

Vorgehensweise: [Die zweistufige Anmeldung für Beschäftigte einschalten](../../../../../eng-us/help/government/tasks/people-and-permissions/turn-on-two-step-sign-in-for-staff.md).

## Verlorene Telefone und neue Geräte

Ein Administrator öffnet das Objekt des Benutzers, setzt das Feld *Zweistufige Anmeldung* auf *Einmalcode per E-Mail* oder *Deaktiviert* und
veröffentlicht; der Benutzer meldet sich an und richtet die App erneut ein
([Einem Benutzer helfen, der sein Telefon verloren hat](../../../../../eng-us/help/government/tasks/people-and-permissions/help-a-user-who-lost-their-phone.md)). Prüfen Sie
zuvor die Identität der Person über Ihren normalen Service-Desk-Prozess: Einen zweiten Faktor auf Anfrage eines nicht verifizierten Anrufers
zurückzusetzen, hebt ihn auf.

## Wichtige Grenzen

- **Die Pflicht gilt nicht je Rolle.** `Enforce2FA` ist ein einzelner Schalter in den Einstellungen der Extension, der für die Anmeldungen der
  Installation gelesen wird, in der er gesetzt ist; Exponential hat keine Einstellung „zweistufig nur für diese Rolle verlangen“.
  Organisationen, die sie nur für Beschäftigte wollen, lassen die Anmeldung der Beschäftigten oft über den Administrations- oder
  Redaktions-Siteaccess laufen und halten Besucherkonten davon fern.
- **E-Mail-Codes** sind nur so sicher wie das Postfach; bevorzugen Sie für Administratoren die Authenticator-App.
- **Falsche Codes zählen als fehlgeschlagene Anmeldungen** des Kontos, daher sperren wiederholt falsche Codes das Konto, wenn die Sperre
  eingeschaltet ist (`MaxNumberOfFailedLogin`) ([Was sie schützt](../../../../anleitungen/zwei-faktor-anmeldung.md#was-sie-schützt)).
- Schaltflächen für die Anmeldung über soziale Netzwerke verlangen, falls aktiviert, weiterhin den eigenen zweiten Schritt des Benutzers.

## Verwandte Seiten
- [Pflege der Benutzerkonten](pflege-von-benutzerkonten.md)
- [Grundsätze der geringsten Rechte](prinzip-der-geringsten-rechte.md)
- [Zwei-Faktor-Anmeldung (Hosting)](../../../hosting/sicherheit/zweistufige-anmeldung.md)
- [Extension für Zwei-Faktor- und Social-Login](../../../../../eng-us/features/6.0/extensions/sevenx_authentication_2fa.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
