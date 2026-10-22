---
title: Spam stoppen
audience: local-business, businesses, customer
category: local-business
order: 52
summary: Spam aus den Kontakt- und Anfrageformularen eines lokalen Unternehmens mit Exponential heraushalten - Formular-Token, die standardmäßig aktiv sind, reCAPTCHA oder hCaptcha hinzufügen (beide brauchen Schlüssel vom Anbieter), welches Sie wählen, Datenschutzhinweise und was zu tun ist, wenn trotzdem Spam durchkommt.
keywords: Spam, CAPTCHA, reCAPTCHA, hCaptcha, ezformtoken, Formular-Token, API-Schlüssel, Bots, Spam im Kontaktformular, Datenschutz, lokales Unternehmen, spam, form token, API keys, contact form spam, local business
type: help
---
# Spam stoppen

Diese Seite ist für Inhaber eines lokalen Unternehmens, deren Kontaktformular mit Müll vollläuft oder die das
verhindern wollen. Exponential hat zwei Schutzebenen, die zusammenarbeiten
([Spamschutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md)).

## Inhalt

1. [Ebene 1: Formular-Token](#ebene-1-formular-token)
2. [Ebene 2: ein CAPTCHA](#ebene-2-ein-captcha)
3. [reCAPTCHA oder hCaptcha](#recaptcha-oder-hcaptcha)
4. [Schlüssel und wer sie verwahrt](#schlüssel-und-wer-sie-verwahrt)
5. [Wenn trotzdem Spam durchkommt](#wenn-trotzdem-spam-durchkommt)
6. [Checkliste](#checkliste)

## Ebene 1: Formular-Token

Die Erweiterung `ezformtoken` fügt jedem Formular ein geheimes Token hinzu und weist Einsendungen ohne dieses Token
ab. Sie stoppt gefälschte Einsendungen, die von anderen Websites geschickt werden. Sie wird mit dem Kernel
ausgeliefert und funktioniert, ohne dass Sie etwas tun müssen; Ihr Ersteller setzt das eigene Geheimnis der Website
(`site.ini [HTMLForms] Secret`). Sie stoppt keine Bots, die Ihre Seite laden und das Formular wie ein Mensch
ausfüllen.

## Ebene 2: ein CAPTCHA

Ein CAPTCHA fordert den Besucher auf zu beweisen, dass er ein Mensch ist, meist mit einem Kontrollkästchen oder einem
kleinen Rätsel. Zwei Erweiterungen in der Standard-`composer.json` fügen einer Inhaltsklasse ein CAPTCHA-Feld hinzu,
sodass es auf Ihrem Kontaktformular, Ihrem Registrierungsformular oder Ihrem Kommentarformular erscheint:

| | `recaptcha` | `hcaptcha` |
|---|---|---|
| Anbieter | Google reCAPTCHA | hCaptcha |
| Braucht | einen Website-Schlüssel und einen geheimen Schlüssel von Google | einen Website-Schlüssel und einen geheimen Schlüssel von hCaptcha |
| Einstellungen | `settings/override/recaptcha.ini.append.php` | `settings/override/hcaptcha.ini.append.php` |
| Lesen | [recaptcha](../../../funktionen/6.0/erweiterungen/recaptcha.md) | [hcaptcha](../../../funktionen/6.0/erweiterungen/hcaptcha.md) |

Die Beschreibung von `hcaptcha` in der `composer.json` nennt es eine Alternative „for users who can't or won't use the
Google reCAPTCHA solution“ (für Benutzer, die die Lösung von Google reCAPTCHA nicht nutzen können oder wollen).

## reCAPTCHA oder hCaptcha

| Wenn Sie | Wählen Sie |
|---|---|
| bereits ein Google-Konto für Karten oder Analyse haben und mit Google einverstanden sind | beides; reCAPTCHA hält es bei einem Anbieter |
| lieber keine Besucherdaten an Google senden | hCaptcha |
| unsicher sind | fragen Sie Ihren Ersteller, welches er getestet hat; beide funktionieren in Exponential gleich |

In beiden Fällen lädt das CAPTCHA ein Skript vom Anbieter. Nennen Sie den Anbieter in Ihrer Datenschutzerklärung.
Wenn die Website eine Content Security Policy sendet, erlaubt der Ersteller den Host des Anbieters.

## Schlüssel und wer sie verwahrt

Die Schlüssel stammen aus einem Konto beim Anbieter. Legen Sie es auf den Namen des Unternehmens an und geben Sie dem
Ersteller Zugang, oder lassen Sie es den Ersteller anlegen und an Sie übergeben ([Mit einem lokalen Partner oder Freiberufler arbeiten](../einstieg/mit-einem-lokalen-partner-oder-freiberufler-arbeiten.md#was-sie-selbst-behalten)).
Der geheime Schlüssel gehört nicht in die Versionsverwaltung und nicht in E-Mails.

## Wenn trotzdem Spam durchkommt

| Symptom | Versuchen Sie |
|---|---|
| Spam über das Kontaktformular | prüfen Sie, ob das CAPTCHA-Feld in der Klasse ist und im abgemeldeten Zustand erscheint |
| Spam-Registrierungen von Benutzern | CAPTCHA in der Benutzerklasse und E-Mail-Bestätigung (`site.ini [UserSettings] VerifyUserType=email`) ([Mitgliederregistrierung und Mitgliederbereiche](../../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md)) |
| Spam-Kommentare | CAPTCHA in der Kommentarklasse; schränken Sie ein, wer kommentieren darf ([Berechtigungen für Besucher](../../ersteller/website-ersteller/rechte-fuer-besucher.md)) |
| Ein Berg von Spam-Einsendungen | löschen Sie sie gesammelt unter **Setup > Gesammelte Informationen** |

Veröffentlichen Sie Ihre E-Mail-Adresse nicht als einfachen Textlink auf jeder Seite, wenn Spam per E-Mail ein Problem
ist; das Formular ist der bessere Weg.

## Checkliste

- [ ] Formular-Token aktiv mit dem eigenen Geheimnis der Website (fragen Sie den Ersteller einmal).
- [ ] CAPTCHA gewählt und auf jedem öffentlichen Formular.
- [ ] Schlüssel in einem Konto, über das das Unternehmen verfügt.
- [ ] Die Datenschutzerklärung nennt den CAPTCHA-Anbieter.
- [ ] Formular abgemeldet getestet, auf einem Smartphone.

## Verwandte Seiten

- [Ein Kontaktformular für Ihr Unternehmen](ein-kontaktformular-fuer-ihr-unternehmen.md)
- [Sicherheitsgrundlagen für Unternehmen](../../unternehmen/sicherheit/sicherheitsgrundlagen-fuer-unternehmen.md)
- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
