---
title: "Anmelde- und Benutzerseiten gestalten"
audience: theme-designer
category: theme-design
order: 23
summary: Wie Theme-Designer die Kontoseiten einer Exponential-Website gestalten - Anmelden, Registrieren, vergessene und geänderte Passwörter, Profil, die zugehörigen E-Mails, die Seiten der Zwei-Faktor-Authentifizierung, wo diese Erweiterung installiert ist, und den angemeldeten Zustand im Rahmen.
keywords: Theme-Designer, Anmeldung, anmelden, registrieren, Passwort vergessen, Profil, Benutzerseiten, Konto, Zwei-Faktor, 2FA, Passwortmanager, Benutzer-Templates, theme designer, login, sign in, register, forgot password, profile, user pages, account, two-factor, password manager, user templates
type: help
---
# Anmelde- und Benutzerseiten gestalten

Diese Seite ist für Theme-Designer, deren Website Besuchern das Anmelden erlaubt: Mitgliedern, Kunden, Mitwirkenden
oder nur den Redakteuren auf dem öffentlichen Siteaccess. Die Kontoseiten werden von den Templates des
**Benutzermoduls** innerhalb Ihres Rahmens gezeichnet, und es sind Seiten, auf denen Menschen am wenigsten Geduld haben.
Die Templates und die Feldnamen, die bleiben müssen, sind in
[Benutzer- und Anmelde-Templates](../designer/vorlagen/benutzer-und-anmelde-templates.md) aufgeführt.

## Inhalt

1. [Die Seiten](#die-seiten)
2. [Anmelden](#anmelden)
3. [Registrieren](#registrieren)
4. [Vergessene und geänderte Passwörter](#vergessene-und-geänderte-passwörter)
5. [Profil und Einstellungen](#profil-und-einstellungen)
6. [Seiten der Zwei-Faktor-Authentifizierung](#seiten-der-zwei-faktor-authentifizierung)
7. [Die E-Mails](#die-e-mails)
8. [Der angemeldete Rahmen](#der-angemeldete-rahmen)
9. [Prüfungen](#prüfungen)

## Die Seiten

| Seite | Template im Kernel-Design | Hinweis zum Design |
|---|---|---|
| Anmelden | `user/login.tpl` | die meistgenutzte; auch in die Seite für verweigerten Zugriff eingebettet |
| Registrieren | `user/register.tpl`, `user/success.tpl` | nur wenn Besucher sich registrieren dürfen |
| Aktivieren | `user/activate.tpl` | nach der Aktivierungs-E-Mail |
| Passwort vergessen | `user/forgotpassword.tpl` | darf nicht verraten, ob zu einer Adresse ein Konto existiert |
| Passwort ändern | `user/password.tpl` | |
| Profil bearbeiten, Einstellungen | `user/edit.tpl`, `user/setting.tpl` | die Felder kommen aus der Benutzerklasse |

Kopieren Sie nur die Templates in Ihr Design, die Sie neu gestalten ([Grundlagen der Overrides](../designer/vorlagen/override-grundlagen.md)
erklärt Templates und Overrides).

## Anmelden

- Eine kurze Seite: Überschrift, zwei Felder, "angemeldet bleiben", wenn die Website es anbietet, eine Schaltfläche und
  Links zu "Passwort vergessen?" und "Registrieren".
- Sichtbare Beschriftungen und die richtigen Autocomplete-Hinweise, damit Passwortmanager das Formular ausfüllen
  ([Benutzer- und Anmelde-Templates](../designer/vorlagen/benutzer-und-anmelde-templates.md#barrierefrei-und-freundlich-für-passwortmanager)).
- Eine Fehlermeldung für ein falsches Passwort, die nicht sagt, welche der beiden Angaben falsch war.
- Dasselbe Formular erscheint mit den mitgelieferten Fehlereinstellungen auch in der Seite für verweigerten Zugriff für
  anonyme Besucher ([Leere Zustände und Fehlerzustände gestalten](leere-zustaende-und-fehlerzustaende-gestalten.md#zugriff-verweigert)).
  Gestalten Sie es so, dass es an beiden Stellen funktioniert: als eigene Seite und als Teil einer anderen Seite.

## Registrieren

Das Registrierungsformular zeichnet die Felder der **Benutzerklasse** der Installation über die
Bearbeitungs-Templates der Datentypen, seine Felder können sich also ändern, wenn sich die Klasse ändert. Gestalten Sie
die Feldtypen, nicht ein festes Formular ([Formulare gestalten](formulare-gestalten.md)). Gestalten Sie die Ergebnisseiten:
"Prüfen Sie Ihre E-Mails, um das Konto zu aktivieren" und "Ihr Konto ist aktiv".

## Vergessene und geänderte Passwörter

- Die Seite "Passwort vergessen" zeigt immer dieselbe Bestätigung, ob die Adresse existiert oder nicht. Behalten Sie den
  Wortlaut des Kernels; er wurde absichtlich so geschrieben.
- Die Seite zum Ändern des Passworts fragt das neue Passwort zweimal ab; zeigen Sie die Passwortregeln, bevor der
  Besucher tippt, nicht erst nach einem Fehler.

## Profil und Einstellungen

Profilseiten zeigen und bearbeiten die Felder des Benutzerobjekts. Halten Sie sie schlicht und nah am Formulardesign der
übrigen Website. Können Besucher ein Bild hochladen, ist es ein Bildfeld: Geben Sie ihm einen Bild-Alias und ein Design
ohne Bild.

## Seiten der Zwei-Faktor-Authentifizierung

Wo die Erweiterung sevenx_authentication_2fa installiert ist, kann die Anmeldung einen zweiten Schritt brauchen. Die
Erweiterung liefert in ihrem Standarddesign Templates in einem Ordner `user2fa/` (`verify.tpl` zum Eingeben eines Codes,
`setup.tpl` zum Einrichten, `callback.tpl`), und das Mediendesign der Demonstrationsinstallation hat eigene Versionen.
Gestalten Sie sie wie die Anmeldeseite: ein Feld für den Code, eine klare Erklärung und ein Ausweg ("andere Methode
verwenden", "abmelden"). Fragen Sie den Entwickler, welche Schritte Ihre Website verwendet, bevor Sie sie gestalten.

## Die E-Mails

E-Mails zu Registrierung, Aktivierung und vergessenem Passwort sind ebenfalls Templates (`registrationinfo.tpl`,
`forgotpasswordmail.tpl`, `password_changed_mail.tpl`). Es sind kurze Nachrichten in einfachem Text; setzen Sie den Link
in eine eigene Zeile und sagen Sie, wer die E-Mail gesendet hat und warum. Test-E-Mails gehen auf einer
Entwicklungsinstallation an Adressen unter `example.invalid`, nie an echte Menschen.

## Der angemeldete Rahmen

Nach der Anmeldung zeigt der Rahmen den Namen des Besuchers und Links zu seinem Profil und zum Abmelden
([Kopf- und Fußbereiche gestalten](kopf-und-fussbereiche-gestalten.md#angemeldet-oder-nicht)). Die Administration hat eine
eigene Anmeldeseite, gestaltet vom Administrationsdesign; ein öffentliches Theme gestaltet sie nicht um
([Überblick über die Admin-Designs](../designer/theme-design/admin-designs-ueberblick.md)).

## Prüfungen

- [ ] Anmeldung als eigene Seite und innerhalb der Seite für verweigerten Zugriff.
- [ ] Meldungen für falsches Passwort, gesperrtes oder inaktives Konto gezeichnet.
- [ ] Der Passwortmanager füllt das Formular aus (in zwei Browsern getestet).
- [ ] Registrierungsformular mit den echten Feldern der Benutzerklasse.
- [ ] Die E-Mails lesen sich als einfacher Text gut.
- [ ] Hell und dunkel, drei Breiten, nur mit der Tastatur.

## Verwandte Seiten

- [Benutzer- und Anmelde-Templates](../designer/vorlagen/benutzer-und-anmelde-templates.md)
- [Barrierefreie Formulare](../designer/theme-design/barrierefreie-formulare.md)
- [Konten und Anmeldung](../konten-und-anmeldung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
