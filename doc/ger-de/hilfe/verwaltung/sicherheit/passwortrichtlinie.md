---
title: Passwortrichtlinie
audience: government, admin, project-manager
category: public-sector
order: 180
summary: Für öffentliche Stellen, die eine Passwortrichtlinie festlegen müssen - die Passworteinstellungen von Exponential (Mindestlänge, Prüfung auf schwache Passwörter, Zusammensetzungsregeln, Gültigkeit des Links zum Zurücksetzen, Benachrichtigung bei Änderung), die mitgelieferten Standardwerte und eine längenbasierte Richtlinie im Einklang mit aktuellen NIST-Leitlinien und ASVS.
keywords: Passwortrichtlinie, Mindestlänge, schwache Passwörter, Zusammensetzungsregeln, Passwort zurücksetzen, Benachrichtigung, öffentlicher Sektor, password policy, MinPasswordLength, WeakPasswordCheck, composition rules, PasswordSettings, ForbidLogin, ForbidCurrentPassword, EndOtherSessions, ChangeNotificationMail, ForgotPasswordKeyLifetime, NIST SP 800-63B, ASVS, bcrypt, public sector
type: help
---
# Passwortrichtlinie

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Sicherheitsbeauftragte, die die
Passwortrichtlinie einer öffentlichen Stelle für die Konten ihrer Website schreiben oder umsetzen. Die Einstellungen
stehen in `settings/site.ini [UserSettings]` und `[PasswordSettings]`; jedes Formular, das ein Passwort setzt, wendet
sie über `expPasswordPolicy` an
([13.6 Passwörter und Anmeldung](../../../installation/13-sicherheitshaertung.md#136-passwörter-und-anmeldung)).

## Inhalt

1. [Die Einstellungen und ihre Standardwerte](#die-einstellungen-und-ihre-standardwerte)
2. [Eine empfohlene Richtlinie](#eine-empfohlene-richtlinie)
3. [Wenn Ihre Richtlinie Zusammensetzungsregeln vorschreibt](#wenn-ihre-richtlinie-zusammensetzungsregeln-vorschreibt)
4. [Was sich für die Benutzer ändert](#was-sich-für-die-benutzer-ändert)
5. [Verwandte Seiten](#verwandte-seiten)

## Die Einstellungen und ihre Standardwerte

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `[UserSettings] MinPasswordLength` | `10` | Mindestlänge, in **Bytes** |
| `GeneratePasswordLength` | `16` | Länge generierter Passwörter |
| `WeakPasswordCheck` | `enabled` | bekannte und häufige Passwörter abweisen |
| `WeakPasswordFile`, `WeakPasswordList[]` | leer | Ihre eigenen verbotenen Wörter |
| `ForgotPasswordKeyLifetime` | `3600` | Sekunden, die ein Link „Passwort vergessen?“ funktioniert |
| `HashType` | `php_default` | heute bcrypt; nie Klartext oder md5-Typen |
| `[PasswordSettings] RequireLowercase`, `RequireUppercase`, `RequireDigit`, `RequireSymbol` | `disabled` | Zusammensetzungsregeln |
| `MinCharacterClasses` | `0` | mindestens so viele der vier Zeichenarten |
| `ForbidLogin` | `disabled` | darf den Anmeldenamen nicht enthalten |
| `ForbidCurrentPassword` | `disabled` | muss sich vom aktuellen unterscheiden |
| `EndOtherSessions` | `enabled` | andere Sitzungen nach einer Änderung abmelden |
| `ChangeNotificationMail` | `enabled` | E-Mail „Ihr Passwort wurde geändert“ |
| `StrengthMeter`, `GenerateButton` | `enabled` | helfen den Benutzern, die Länge zu erreichen |

## Eine empfohlene Richtlinie

Die Installationsanleitung empfiehlt, im Einklang mit NIST SP 800-63B-4 (das mindestens 15 Zeichen verlangt, wo ein
Passwort der einzige Faktor ist, und von Zusammensetzungsregeln abrät):

```ini
# settings/override/site.ini.append.php
[UserSettings]
MinPasswordLength=15

[PasswordSettings]
ForbidLogin=enabled
ForbidCurrentPassword=enabled
```

Ergänzen Sie die eigenen verbotenen Wörter Ihrer Organisation (ihren Namen, den Namen der Website, die Stadt) mit
`WeakPasswordList[]` oder `WeakPasswordFile`. ASVS 4.0.3 Stufe 1 verlangt mindestens 12; 15 erfüllt beides. Kombinieren
Sie das für Beschäftigte mit der zweistufigen Anmeldung.

## Wenn Ihre Richtlinie Zusammensetzungsregeln vorschreibt

Manche Richtlinien von Organisationen schreiben sie noch vor. Exponential unterstützt sie:

```ini
[PasswordSettings]
MinCharacterClasses=3
```

oder die einzelnen `Require*`-Schalter. Die Installationsanleitung merkt an, dass solche Regeln eher vorhersehbare
Passwörter hervorbringen; halten Sie den Grund fest, wenn Sie sie verwenden.

## Was sich für die Benutzer ändert

- Das Anheben der Mindestlänge sperrt niemanden aus: Vorhandene Passwörter funktionieren weiter; die Regel gilt, wenn
  ein Passwort das nächste Mal gesetzt wird.
- `MinPasswordLength` zählt Bytes: In Schriften mit Mehrbyte-Zeichen erreichen weniger Zeichen die Grenze.
- Nach einer Änderung werden andere Sitzungen des Benutzers abgemeldet, und der Benutzer erhält eine
  Benachrichtigungs-E-Mail.
- Konten mit älteren Hash-Typen werden bei ihrer nächsten Anmeldung neu gehasht (`UpdateHash=true`).

## Verwandte Seiten

- [13.6 Passwörter und Anmeldung](../../../installation/13-sicherheitshaertung.md#136-passwörter-und-anmeldung)
- [Ihr Passwort ändern](../../../../eng-us/features/6.0/modern-password-change.md)
- [Das Administratorpasswort ändern](../../administratorpasswort-aendern.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
