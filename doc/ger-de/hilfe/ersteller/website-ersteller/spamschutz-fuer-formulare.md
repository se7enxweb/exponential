---
title: Spamschutz für Formulare
audience: website-creator, admin
category: website-creation
order: 470
summary: Kontaktformulare, Registrierung und Kommentare gegen Spam und gefälschte Einsendungen schützen - Formular-Tokens mit ezformtoken, Datentypen für reCAPTCHA oder hCaptcha, Schlüssel je Host und weitere Maßnahmen.
keywords: Spam, CAPTCHA, Formular-Token, Registrierungsspam, Kommentarspam, Bots, Website-Ersteller, recaptcha, hcaptcha, ezformtoken, form token, CSRF, registration spam, comment spam, website creator
type: help
---
# Spamschutz für Formulare

Diese Seite richtet sich an Website-Ersteller, die Spam und gefälschte Einsendungen aus Formularen heraushalten. Zwei
Schichten arbeiten zusammen: Ein **Formular-Token** beweist, dass eine Einsendung von Ihrer eigenen Seite kommt, und ein
**CAPTCHA** verlangt vom Besucher den Nachweis, dass er ein Mensch ist.

## Inhalt

1. [Formular-Tokens](#formular-tokens)
2. [CAPTCHA: reCAPTCHA oder hCaptcha](#captcha-recaptcha-oder-hcaptcha)
3. [Welche Formulare Sie schützen sollten](#welche-formulare-sie-schützen-sollten)
4. [Weitere Maßnahmen](#weitere-maßnahmen)

## Formular-Tokens

Die Erweiterung `ezformtoken` fügt jedem POST-Formular ein geheimes Token hinzu und lehnt Einsendungen ohne Token ab,
was gefälschte Cross-Site-Einsendungen (CSRF) verhindert. Eine abgelehnte Einsendung erhält eine 403 und eine Zeile in
`var/log/warning.log` (`site.ini [HTMLForms]`, `RefusalLogCollapseSeconds` fasst Wiederholungen zusammen). Setzen Sie
Ihr eigenes `[HTMLForms] Secret` in `settings/override/site.ini.append.php`; der mitgelieferte Wert fordert dazu auf, ihn
zu ändern. Wenn das Formular eines Besuchers abgelaufen ist, sieht er statt eines Fehlers eine freundliche Seite
([Seite für abgelaufene Formulare](../../../../eng-us/features/6.0/form-expired-page.md)).

## CAPTCHA: reCAPTCHA oder hCaptcha

| | `recaptcha` | `hcaptcha` |
|---|---|---|
| Anbieter | Google reCAPTCHA (v2; ein v3-Template für das Feedbackformular) | hCaptcha |
| Zusätzliches Composer-Paket | `google/recaptcha` | keines |
| Einstellungen | `settings/override/recaptcha.ini.append.php` | `settings/override/hcaptcha.ini.append.php` |
| Dokumentation | [recaptcha](../../../../eng-us/features/6.0/extensions/recaptcha.md) | [hcaptcha](../../../../eng-us/features/6.0/extensions/hcaptcha.md) |

Beide funktionieren auf dieselbe Weise:

1. Aktivieren Sie die Erweiterung, und holen Sie sich Schlüssel beim Anbieter.
2. Tragen Sie sie in die Override-Datei ein (für mehrere Websites als Arrays je Host):

   ```ini
   [Keys]
   PublicKey=<your site key>
   PrivateKey=<your secret key>
   [PublishSettings]
   NewObjectsOnly=false
   ```

   Halten Sie den geheimen Schlüssel aus der Versionsverwaltung heraus.
3. Fügen Sie der Klasse das CAPTCHA-Attribut hinzu: einer Klasse für Kontaktformulare oder der Benutzerklasse für die
   Registrierung.
4. Leeren Sie die Caches, und testen Sie das Formular abgemeldet.

`NewObjectsOnly=true` prüft nur neue Objekte (Registrierung ja, Bearbeiten eines Profils nein).

Das CAPTCHA-Widget lädt ein Skript vom Anbieter: Erlauben Sie dessen Host in der Content Security Policy der Website,
wenn Sie eine durchsetzen ([Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)), und nennen Sie
den Anbieter in Ihrem Datenschutzhinweis.

## Welche Formulare Sie schützen sollten

| Formular | Schützen mit |
|---|---|
| Kontakt- und Feedbackformulare | Token (automatisch) und CAPTCHA |
| Benutzerregistrierung (`user/register`) | Token und CAPTCHA an der Benutzerklasse |
| Kommentare | Token und CAPTCHA an der Kommentarklasse |
| Formulare nur für angemeldete Benutzer | Token reicht meist aus |

## Weitere Maßnahmen

- Verlangen Sie für Registrierungen eine Bestätigung per E-Mail (`site.ini [UserSettings] VerifyUserType=email`)
  ([Registrierung von Mitgliedern und Bereiche nur für Mitglieder](../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md)).
- Begrenzen Sie, was anonyme Besucher anlegen dürfen (`content/create` für Kommentare nur, wo gewünscht).
- Entfernen Sie Spam-Einsendungen gesammelt unter **Setup > Gesammelte Informationen**.

## Verwandte Seiten

- [Formulare mit dem Informationssammler](formulare-mit-dem-informationssammler.md)
- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Rechte für Besucher](../../../../eng-us/help/creators/website-creators/visitor-permissions.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
