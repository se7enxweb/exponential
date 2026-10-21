---
title: Zweistufige Anmeldung
audience: government, admin, website-creator
category: public-sector
order: 40
summary: Wie Sie die Erweiterung sevenx_authentication_2fa für öffentliche Kontoinhaber und Mitarbeiter auf einer Site für den öffentlichen Sektor einsetzen - Authenticator-App oder E-Mail-Codes, freiwillig oder verpflichtend, die Richtlinien, die Rollen brauchen, Wiederherstellung bei verlorenem Telefon und Überlegungen zum Social Login.
keywords: zweistufige Anmeldung, Zwei-Faktor, Authenticator-App, Einmalcode per E-Mail, Wiederherstellung, Social Login, two-step sign-in, two-factor, 2FA, TOTP, authenticator app, e-mail one-time code, Enforce2FA, DefaultMethod, user2fa/setup, user2fa/verify, recovery, social login, OAuth
type: help
---
# Zweistufige Anmeldung

Diese Seite richtet sich an Administratoren und Site-Ersteller, die entscheiden, wie Kontoinhaber nachweisen, wer sie
sind. Die zweistufige Anmeldung macht ein gestohlenes Passwort allein nutzlos. Sie wird von der Erweiterung
`sevenx_authentication_2fa` bereitgestellt, die der Installer im freiwilligen Modus aktiviert. Quellen:
[Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md) und
[sevenx_authentication_2fa](../../../../funktionen/6.0/erweiterungen/sevenx_authentication_2fa.md).

## Inhalt

1. [Die Verfahren](#die-verfahren)
2. [Mitarbeiter: verpflichtend machen](#mitarbeiter-verpflichtend-machen)
3. [Die Öffentlichkeit: anbieten](#die-öffentlichkeit-anbieten)
4. [Wiederherstellung](#wiederherstellung)
5. [Social Login](#social-login)
6. [Prüfungen](#prüfungen)

## Die Verfahren

| Verfahren | Wie es funktioniert | Passt für |
|---|---|---|
| Authenticator-App (TOTP, RFC 6238) | ein 6-stelliger Code aus einer App auf dem Telefon; der Schlüssel wird per QR-Code eingerichtet | Mitarbeiter; öffentliche Benutzer, die es möchten |
| Einmalcode per E-Mail | ein per E-Mail gesendeter Code, mit einem Link, der die Anmeldung im selben Browser abschließt | Benutzer ohne Smartphone; Wiederherstellung |

Codes funktionieren einmal; nach fünf falschen Codes beginnt die Anmeldung wieder mit dem Passwort; falsche Codes
zählen als fehlgeschlagene Anmeldungen. Jeder Schritt wird in `var/log/auth.log` protokolliert, nie ein Code oder ein
Geheimnis.

## Mitarbeiter: verpflichtend machen

Mitarbeiterkonten können die Site verändern; verlangen Sie für sie den zweiten Schritt.

1. Prüfen Sie die Einrichtung: die Erweiterung aktiv, in `LoginHandler[]` `sevenxUser2fa` vor `standard`, das Feld
   *Zweistufige Anmeldung* in der Benutzerklasse und `user2fa/setup` und `user2fa/verify` in jeder Rolle, die sich anmeldet.
   Der Installer erledigt das für Member, Editor und Partner
   ([Erweiterungen, die es aktiviert](../../../seitenpakete/sevenx-multisite/erweiterungen-die-sevenx-multisite-aktiviert.md)).
2. Setzen Sie einen `SecretKey` in `settings/override/sevenxauthentication2fa.ini.append.php`, damit die Geheimnisse
   der Authenticator-Apps verschlüsselt werden (der Installer erzeugt einen; halten Sie ihn aus der
   Versionsverwaltung heraus; geht er verloren, muss jeder die Einrichtung wiederholen).
3. Richten Sie zuerst die Administratoren ein.
4. Setzen Sie `[General] Enforce2FA=enabled` und `DefaultMethod=totp`; leeren Sie den INI-Cache
   (`php bin/php/ezcache.php --clear-tag=ini`) und rollen Sie die Änderung aus.

`Enforce2FA` ist eine Einstellung der Installation oder eines Siteaccess. Um sie für Mitarbeiter, aber nicht für die
Öffentlichkeit zu erzwingen, setzen Sie sie in den Einstellungen des Siteaccess der Administration und probieren Sie es
dort zuerst aus, wie es die Anleitung für das Testen auf einem Siteaccess beschreibt.

## Die Öffentlichkeit: anbieten

Für öffentliche Konten ist das Anbieten meist besser als das Erzwingen: Manche Menschen haben kein Smartphone, und ein
erzwungener zweiter Schritt kann Menschen von einer öffentlichen Leistung aussperren.

| Praxis | Warum |
|---|---|
| Authenticator-App und E-Mail-Codes anbieten | Inklusion |
| Es in einfacher Sprache unter **Mein Konto** erklären | [Ihr Konto und die Anmeldung](../../../konten-und-anmeldung.md#zweistufige-anmeldung) |
| Es nur verlangen, wo das Konto Zugang zu sensiblen Informationen gibt | verhältnismäßige Sicherheit |

## Wiederherstellung

| Situation | Was zu tun ist |
|---|---|
| Ein Mitarbeiter hat sein Telefon verloren | ein Administrator öffnet das Objekt des Benutzers, setzt *Zweistufige Anmeldung* auf *E-mail one-time code* oder *Deaktiviert*, veröffentlicht; der Benutzer richtet die App neu ein |
| Ein öffentlicher Benutzer hat sein Telefon verloren | dasselbe, nach einer Prüfung der Identität über einen Weg, dem Sie vertrauen (nicht über eine E-Mail von einer unbekannten Adresse) |
| Jemand möchte das Verfahren wechseln | er tut es selbst; Wechseln oder Ausschalten verlangt einen aktuellen Code |
| Der `SecretKey` ging verloren | jeder mit der App richtet sie neu ein |

Schreiben Sie die Identitätsprüfung für die Wiederherstellung in Ihr Support-Verfahren
([Benutzerdokumentation und Support](benutzerdokumentation-und-support.md)).

## Social Login

Die Erweiterung enthält Gerüste für Social-Login-Handler (Google, Facebook, Twitter/X, Instagram, Meta, ID.me), die für
aktivierte Anbieter Schaltflächen *Continue with ...* zeigen; ein so gefundener Benutzer durchläuft trotzdem seinen
eigenen zweiten Schritt. Für eine Behörde sendet Social Login Anmeldedaten an ein privates Unternehmen; viele Behörden
vermeiden es oder verwenden stattdessen einen staatlichen Identitätsdienst, was eine Entwicklungsaufgabe ist.
Entscheiden Sie mit Ihrem Datenschutzbeauftragten.

## Prüfungen

- [ ] Ein Testkonto für Mitarbeiter muss die App einrichten, bevor es hineinkommt (mit `Enforce2FA=enabled` in der
  Administration).
- [ ] Ein öffentliches Testkonto meldet sich ohne zweiten Schritt an und kann ihn unter **Mein Konto** einschalten.
- [ ] Ein falscher Code wird abgelehnt; derselbe Code zweimal wird abgelehnt.
- [ ] Die Wiederherstellung durch einen Administrator funktioniert und steht im Audit-Protokoll.

## Verwandte Seiten

- [Bürgerkonten](README.md)
- [Anmeldung und Sitzungen](anmeldung-und-sitzungen.md)
- [Sicherheitshärtung](../../../sicherheitshaertung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
