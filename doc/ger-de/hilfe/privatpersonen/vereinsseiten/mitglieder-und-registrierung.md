---
title: Mitglieder und Registrierung
audience: individual, website-creator, customer
category: personal-sites
order: 1712
summary: Wie eine ehrenamtliche Person Vereinsmitglieder sich in Exponential registrieren und anmelden lässt - die Registrierungsseiten, Aktivierung per E-Mail, die Gruppe Members und die Rolle Member des Standardpakets, Freigabe neuer Mitglieder, ein Mitgliederbereich, was Mitglieder dürfen, Passwörter und zweistufige Anmeldung und eine aufgeräumte Mitgliederliste.
keywords: Mitglieder, Registrierung, registrieren, user/register, Aktivierung, VerifyUserType, DefaultUserPlacement, Gruppe Members, Rolle Member, Mitglieder freigeben, Mitgliederbereich, Passwort zurücksetzen, Zwei-Faktor, Mitgliederliste, Verein, members, registration, sign up, activation, approve members, members area, password reset
type: help
---
# Mitglieder und Registrierung

Diese Seite ist für Privatpersonen, die eine Vereinsseite betreiben und möchten, dass Mitglieder Konten haben. Die
Registrierung ist eingebaut; Sie entscheiden, wer Mitglied wird und was Mitglieder dürfen.

## Inhalt

1. [Die beteiligten Seiten](#die-beteiligten-seiten)
2. [Was das Standardpaket eingerichtet hat](#was-das-standardpaket-eingerichtet-hat)
3. [Offene oder freigegebene Mitgliedschaft](#offene-oder-freigegebene-mitgliedschaft)
4. [Die Einrichtung](#die-einrichtung)
5. [Was Mitglieder dürfen](#was-mitglieder-dürfen)
6. [Passwörter und zweistufige Anmeldung](#passwörter-und-zweistufige-anmeldung)
7. [Die Mitgliederliste aufgeräumt halten](#die-mitgliederliste-aufgeräumt-halten)
8. [Checkliste](#checkliste)

## Die beteiligten Seiten

| Seite | Adresse |
|---|---|
| Registrieren | `user/register` |
| Registrierung abgeschlossen | `user/success` |
| Konto aktivieren (Link in der E-Mail) | `user/activate` |
| Passwort vergessen | `user/forgotpassword` |
| Anmelden, abmelden | `user/login`, `user/logout` |
| Eigenes Konto bearbeiten | `user/edit` |

Quelle und Einstellungen: [Mitgliederregistrierung und Bereiche nur für Mitglieder](../../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md).

## Was das Standardpaket eingerichtet hat

Der Installer des Standard-Seitenpakets (`sevenx_multisite`) benennt die Benutzergruppe „Guest accounts" in
**Members** um und gibt ihr die Rolle **Member** (`sevenx-multi-site-installer.php`). Die Rolle Member darf in der
Sektion Standard Kommentare sowie Forenthemen und -antworten erstellen und diese bearbeiten, ihr eigenes Konto
bearbeiten (`user/selfedit`), Benachrichtigungen nutzen und ihr Passwort ändern. Eine private Sektion liest sie
**nicht** von selbst; das fügen Sie für Ihren Mitgliederbereich hinzu.

Neue Registrierungen landen in der Gruppe, die `site.ini [UserSettings] DefaultUserPlacement` festlegt.

## Offene oder freigegebene Mitgliedschaft

| Modell | Wie | Passt zu |
|---|---|---|
| **Offen** | Registrierungen landen direkt in Members | Vereinen, deren Mitgliederbereich nichts Sensibles enthält |
| **Freigegeben** | Registrierungen landen in einer Gruppe „Neue Registrierungen" ohne Zugriff; eine ehrenamtliche Person verschiebt echte Mitglieder nach Members | Den meisten Vereinen: Die Mitgliederliste stimmt mit der des Vereins überein |
| **Nur auf Einladung** | Registrierung geschlossen; der Administrator legt Konten an | Kleinen Vereinen, Vorständen |

## Die Einrichtung

1. Entscheiden Sie sich für eines der obigen Modelle.
2. Für **freigegeben**: Legen Sie eine Gruppe „Neue Registrierungen" ohne zusätzliche Rollen an und richten Sie
   `DefaultUserPlacement` in den Einstellungen des öffentlichen Siteaccess auf sie.
3. Stellen Sie sicher, dass `VerifyUserType=email` (Aktivierung über einen Link in einer E-Mail) und
   `RegistrationEmail` (wer über neue Registrierungen informiert wird) gesetzt sind
   ([Registrierungseinstellungen](../../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md#einstellungen-für-die-registrierung)).
4. Legen Sie den Mitgliederbereich an: einen Ordner in einer eigenen Sektion und eine Rolle mit `content/read` auf
   dieser Sektion, die Members zugewiesen wird
   ([Private Bereiche mit Rollen und Sektionen](../familienseiten/private-bereiche-mit-rollen-und-sektionen.md)).
5. Passen Sie bei Bedarf das Aussehen der Registrierungs- und Anmeldeseiten in Ihrem Design an.
6. Schützen Sie das Registrierungsformular vor Bots ([Grundlagen zu Spam und Sicherheit](../im-betrieb/grundlagen-zu-spam-und-sicherheit.md)).
7. Registrieren Sie selbst ein Testmitglied, aktivieren Sie es, melden Sie sich an und öffnen Sie den
   Mitgliederbereich.

Ausgehende E-Mail muss funktionieren: Ohne sie kann niemand ein Konto aktivieren oder ein Passwort zurücksetzen.

## Was Mitglieder dürfen

| Recht | Typische Einstellung |
|---|---|
| Den Mitgliederbereich lesen | Ja |
| Neuigkeiten kommentieren | Ja, oder nur im Mitgliederbereich |
| In Foren Themen beginnen und antworten | Ja, wenn Sie Foren nutzen |
| Anmeldeformulare ausfüllen | Ja |
| Neuigkeiten oder Veranstaltungen veröffentlichen | Nein; das ist Sache der Redakteure ([Die Arbeit mit Ehrenamtlichen teilen](die-arbeit-mit-ehrenamtlichen-teilen.md)) |

## Passwörter und zweistufige Anmeldung

- Passwortregeln und ablaufende Links zum Zurücksetzen sind eingebaut
  ([Moderne Passwortänderung](../../../funktionen/6.0/moderne-passwortaenderung.md),
  [Benutzerpasswort zurücksetzen](../../../funktionen/6.0/benutzerpasswort-zuruecksetzen.md)).
- Die zweistufige Anmeldung kann Mitgliedern angeboten und für Redakteure und Administratoren verpflichtend gemacht
  werden ([Zwei-Faktor-Anmeldung](../im-betrieb/zwei-faktor-anmeldung.md)).

## Die Mitgliederliste aufgeräumt halten

- Vergleichen Sie einmal im Jahr die Gruppe Members der Website mit der Mitgliederliste des Vereins.
- Verschieben Sie Ausgetretene aus Members (oder deaktivieren Sie ihre Konten); behalten Sie, was sie geschrieben
  haben.
- Entfernen Sie Registrierungen, die nie aktiviert wurden.
- Speichern Sie über Mitglieder nicht mehr, als der Verein braucht
  ([Personenbezogene Daten in Exponential](../../unternehmen/sicherheit/personenbezogene-daten-in-exponential.md)).

## Checkliste

- [ ] Mitgliedschaftsmodell mit dem Vorstand entschieden.
- [ ] `DefaultUserPlacement`, `VerifyUserType`, `RegistrationEmail` gesetzt.
- [ ] Mitgliederbereich nur für Members lesbar, als Fremder getestet.
- [ ] Registrierung gegen Bots geschützt.
- [ ] E-Mails zur Aktivierung und zum Zurücksetzen kommen an.
- [ ] Jährlicher Abgleich mit der Liste des Vereins im Kalender.

## Verwandte Seiten

- [Vereinsseiten](README.md)
- [Mitgliederregistrierung](../../unternehmen/mitglieder/registrierung-von-mitgliedern.md)
- [Inhalte nur für Mitglieder](../../unternehmen/mitglieder/inhalte-nur-fuer-mitglieder.md)
- [Mitgliederbereiche und Registrierung (Kunden)](../../kunden/wachstum/mitgliederbereiche-und-registrierung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
