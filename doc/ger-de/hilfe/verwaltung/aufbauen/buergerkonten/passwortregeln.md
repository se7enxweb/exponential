---
title: Passwortregeln
audience: government, admin
category: public-sector
order: 50
summary: Die Passwortregeln, die Exponential anwendet und anwenden kann - Mindestlänge, die Prüfung auf schwache Passwörter, Regeln für Zeichenklassen, kein Benutzername im Passwort, Ablauf von Passwörtern mit ezmbpaex, Links bei vergessenem Passwort - und wie Sie Regeln für öffentliche Konten und Mitarbeiterkonten wählen.
keywords: Passwortregeln, Passwortrichtlinie, schwache Passwörter, Ablauf von Passwörtern, Passwort vergessen, password rules, password policy, MinPasswordLength, WeakPasswordCheck, PasswordSettings, RequireDigit, MinCharacterClasses, ForbidLogin, ForbidCurrentPassword, ezmbpaex, password expiry, forgot password, ForgotPasswordKeyLifetime
type: help
---
# Passwortregeln

Diese Seite richtet sich an Administratoren, die die Passwortrichtlinie einer Site für den öffentlichen Sektor
festlegen. Sie listet jede Regel, die Exponential hat, wo sie gesetzt wird, und gibt einen Ausgangspunkt für
öffentliche Konten und Mitarbeiterkonten. Quellen:
[Das eigene Passwort ändern](../../../../funktionen/6.0/moderne-passwortaenderung.md),
[ezmbpaex](../../../../funktionen/6.0/erweiterungen/ezmbpaex.md) und
[Sicherheitshärtung](../../../sicherheitshaertung.md#brute-force-und-credential-stuffing-bei-der-anmeldung).

## Inhalt

1. [Die Regeln](#die-regeln)
2. [Ablauf von Passwörtern](#ablauf-von-passwörtern)
3. [Vergessene Passwörter](#vergessene-passwörter)
4. [Eine Richtlinie wählen](#eine-richtlinie-wählen)
5. [Wo die Regeln gelten](#wo-die-regeln-gelten)

## Die Regeln

| Einstellung | Voreinstellung | Regel |
|---|---|---|
| `site.ini [UserSettings] MinPasswordLength` | 10 bei einer neuen Installation (3, wenn sie fehlt) | Mindestlänge, in Bytes gezählt |
| `site.ini [UserSettings] WeakPasswordCheck` | eingeschaltet (seit 6.0.15) | bekannte und häufige Passwörter und einfache Abwandlungen ablehnen |
| `[PasswordSettings] RequireLowercase`, `RequireUppercase`, `RequireDigit`, `RequireSymbol` | aus | jede verlangt ein Zeichen dieser Art |
| `[PasswordSettings] MinCharacterClasses` | 0 (aus) | mindestens N der vier Arten mischen |
| `[PasswordSettings] ForbidLogin` | aus | darf den Benutzernamen nicht enthalten |
| `[PasswordSettings] ForbidCurrentPassword` | aus | muss sich vom aktuellen Passwort unterscheiden |
| `[PasswordSettings] EndOtherSessions` | eingeschaltet | andere Sitzungen werden nach einer Änderung abgemeldet |

Die Seite zum Ändern des Passworts listet die Anforderungen neben dem Feld, hakt sie beim Tippen ab (mit JavaScript)
und sendet eine Mail „Ihr Passwort wurde geändert“, die sich nicht abschalten lässt. Setzen Sie die Regeln in
`settings/override/site.ini.append.php` und leeren Sie danach den INI-Cache.

## Ablauf von Passwörtern

Die Erweiterung `ezmbpaex` fügt einen Ablauf und eine Regel mit regulärem Ausdruck hinzu:

| Einstellung (`mbpaex.ini [mbpaexSettings]`) | Voreinstellung | Bedeutung |
|---|---|---|
| `DefaultPasswordLifeTime` | 3 (Tage) | Tage, die ein Passwort gültig ist; `0` läuft nie ab |
| `ExpirationNotification` | 172800 (zwei Tage) | wann vor dem Ablauf die Warnmail gesendet wird |
| `PasswordValidationRegexp` | leer | ein Muster, dem neue Passwörter entsprechen müssen |
| `ForgotPasswordHashLifeTime` | 86400 | Sekunden, die ein Link der Erweiterung bei vergessenem Passwort funktioniert |

Sie braucht zwei Cronjob-Teile (`ezmbpaex_send_expiry_notifications`, `ezmbpaex_updatechildren`). Beachten Sie die
mitgelieferte Gültigkeit von drei Tagen: Setzen Sie Ihre eigene, bevor Sie die Erweiterung aktivieren. Aktuelle
Empfehlungen von Sicherheitsbehörden bevorzugen im Allgemeinen lange Passwörter, eine Prüfung gegen bekannte schwache
Passwörter und einen zweiten Schritt gegenüber erzwungenen regelmäßigen Wechseln; entscheiden Sie mit Ihrem
Sicherheitsbeauftragten.

## Vergessene Passwörter

| Eigenschaft | Exponential |
|---|---|
| Dieselbe Antwort für jede Adresse | ja: die Seite verrät nie, ob ein Konto existiert |
| Gültigkeit des Links | eine Verwendung, eine Stunde (`ForgotPasswordKeyLifetime`) |
| Stärke des Schlüssels | zufällige Schlüssel aus der sicheren Quelle des Systems |
| Ohne Zugang zum Postfach | ein Administrator setzt das Passwort zurück (im Konto des Benutzers, oder für Administratoren mit `bin/php/resetuserpassword.php`, [Ein Benutzerpasswort zurücksetzen](../../../../funktionen/6.0/benutzerpasswort-zuruecksetzen.md)), nachdem er die Identität geprüft hat |

## Eine Richtlinie wählen

Ein Ausgangspunkt zum Anpassen (Beispielannahme):

| Konten | Länge | Weitere Regeln | Zweiter Schritt |
|---|---|---|---|
| Öffentliche Konten | `MinPasswordLength=12` | `WeakPasswordCheck=enabled`, `ForbidLogin=enabled` | angeboten |
| Mitarbeiter | `MinPasswordLength=15` (der Vorschlag der Härtungsanleitung) | `WeakPasswordCheck`, `ForbidLogin`, `ForbidCurrentPassword` | verpflichtend |

Die Einstellungen gelten je Installation oder je Siteaccess; Registrierungen auf dem öffentlichen Siteaccess und in
der Administration bearbeitete Mitarbeiterkonten können daher unterschiedlichen Regeln folgen, wo Ihre Einrichtung sie
trennt.

## Wo die Regeln gelten

Jedes Formular, das ein Passwort setzt: Registrierung, die Seite zum Ändern des Passworts (`user/password`), das
Bearbeiten eines Benutzers in content/edit oder in der Administration und `bin/php/resetuserpassword.php`. Bestehende
Passwörter werden bei der Anmeldung nicht erneut geprüft; niemand wird durch eine neue Regel ausgesperrt.

## Verwandte Seiten

- [Bürgerkonten](README.md)
- [Zweistufige Anmeldung](zweistufige-anmeldung.md)
- [13.6 Passwörter und Anmeldung](../../../../installation/13-sicherheitshaertung.md#136-passwörter-und-anmeldung)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
