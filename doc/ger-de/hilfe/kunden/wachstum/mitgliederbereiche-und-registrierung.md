---
title: Mitgliederbereiche und Registrierung
audience: customer, businesses, admin
category: customer-success
order: 16
summary: Was ein Website-Betreiber entscheidet und betreibt, wenn Besucher Konten auf einer Exponential-Website erhalten - offene oder genehmigte Registrierung, E-Mail-Bestätigung, die Rolle Member, Bereiche nur für Mitglieder, nicht abgeschlossene Registrierungen, Aufräumen der Konten, E-Mails an Mitglieder und Supportfragen.
keywords: Mitglieder, Registrierung, Mitgliederbereich, nur für Mitglieder, Rolle Member, user/register, Aktivierung, nicht aktivierte Benutzer, Kontenpflege, zweistufige Anmeldung, Website-Betreiber, members, registration, member area, members-only, Member role, activation, unactivated users, account hygiene, two-step sign-in
type: help
---
# Mitgliederbereiche und Registrierung

Diese Seite ist für Betreiber, die Besucher Konten anlegen lassen: ein Mitgliederbereich, ein Kundenportal, eine
Community oder einfach Kommentare unter einem Namen. Der Website-Ersteller richtet die Seiten und Einstellungen ein
([Registrierung von Mitgliedern und Bereiche nur für Mitglieder](../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md));
die Planungssicht steht unter [Mitglieder-Websites](../../unternehmen/mitglieder/README.md). Diese Seite behandelt, was Sie
entscheiden und was Ihr Team jeden Monat tut.

## Inhalt

1. [Entscheidungen](#entscheidungen)
2. [Wie es funktioniert, in Kürze](#wie-es-funktioniert-in-kürze)
3. [Die monatliche Routine](#die-monatliche-routine)
4. [Fragen von Mitgliedern](#fragen-von-mitgliedern)
5. [Bevor Sie die Registrierung öffnen](#bevor-sie-die-registrierung-öffnen)

## Entscheidungen

| Frage | Optionen | Lesen Sie |
|---|---|---|
| Wer darf sich registrieren? | jeder, oder nur Personen, die Sie genehmigen | [Registrierung und Genehmigung von Konten](../../unternehmen/extranets/registrierung-und-kontofreigabe.md) |
| Wie wird ein Konto bestätigt? | ein Link in einer E-Mail (`VerifyUserType=email`) | [Registrierung von Mitgliedern und Bereiche nur für Mitglieder](../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md) |
| Was sehen Mitglieder, was Besucher nicht sehen? | eine Sektion nur für Mitglieder | [Inhalte nur für Mitglieder](../../unternehmen/mitglieder/inhalte-nur-fuer-mitglieder.md) |
| Welche E-Mails erhalten Mitglieder? | notwendige Konto-E-Mails immer; optionale E-Mails nur, wenn sie sie einschalten | [E-Mail-Einstellungen](../../../../eng-us/features/6.0/mail-preferences.md) |
| Zweistufige Anmeldung für Mitglieder? | optional für Mitglieder, verpflichtend für Mitarbeitende | [Konten, Passwörter und zweistufige Anmeldung](../alltag/konten-passwoerter-und-zweistufige-anmeldung.md) |
| Welche Daten fragen Sie ab? | so wenig wie nötig, auf dem Formular erklärt | [Personenbezogene Daten in Exponential](../../unternehmen/sicherheit/personenbezogene-daten-in-exponential.md) |

## Wie es funktioniert, in Kürze

Aus [Registrierung von Mitgliedern und Bereiche nur für Mitglieder](../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md):

- Registrierung, Aktivierung, vergessenes Passwort, Anmeldung und das Bearbeiten des eigenen Kontos sind Seiten der
  Website (`user/register`, `user/activate`, `user/forgotpassword`, `user/login`, `user/edit`), gestaltet wie die Website.
- Neue Konten werden in einer Benutzergruppe abgelegt (`DefaultUserPlacement`); das sollte eine Gruppe Members sein,
  nie eine Gruppe der Redaktion.
- Eine Rolle **Member** gibt dieser Gruppe das Lesen der Mitgliedersektion und die Anmeldung auf der öffentlichen
  Website, und sonst nichts.
- Der Mitgliederordner liegt in seiner eigenen Sektion, die anonyme Besucher nicht lesen dürfen; Besucher, die ihn
  öffnen, werden gebeten, sich anzumelden.
- Das Passwort wird nie per E-Mail verschickt (`PasswordInRegistrationEmail=disabled`); die Passwortregeln gelten auch
  für die Registrierung ([Ihr Passwort ändern](../../../../eng-us/features/6.0/modern-password-change.md)).

## Die monatliche Routine

| Aufgabe | Wo | Wer |
|---|---|---|
| Registrierungen, die niemand abgeschlossen hat | **Benutzer > Nicht aktivierte Benutzer**; eine Registrierung, die älter als 30 Tage ist, wird als **Alt** markiert; aktivieren oder entfernen ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)) | Kontenverwaltung |
| Nie genutzte, lange nicht gesehene, gesperrte Konten | **Benutzer > Benutzerkonten prüfen**, Filter **Nie angemeldet**, **Seit N Tagen nicht gesehen**, **Gesperrt** ([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)) | Kontenverwaltung |
| Spam-Registrierungen | dieselbe Seite, dann deaktivieren | Kontenverwaltung |
| Die Rolle Member gibt weiterhin nur, was Mitglieder brauchen | **Benutzer > Rollen und Richtlinien** | Administrator |
| Anfragen von Mitgliedern zu ihren Daten | Ihre Datenschutzroutine | Betreiber |

> **Hinweis**
> Wenn eine Registrierung von Hand aktiviert wird, erhält die Person die E-Mail „Registrierung genehmigt“, es sei denn,
> Ihr Administrator hat das abgeschaltet ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)). Das
> Entfernen eines Benutzers ist endgültig; das Deaktivieren lässt sich rückgängig machen
> ([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)).

## Fragen von Mitgliedern

| Frage | Antwort |
|---|---|
| „Ich habe mein Passwort vergessen“ | der Link „Passwort vergessen?“ auf der Anmeldeseite ([Ihr Konto und die Anmeldung](../../konten-und-anmeldung.md)) |
| „Die Aktivierungs-E-Mail ist nicht angekommen“ | den Spam-Ordner prüfen; die Kontenverwaltung kann das Konto unter Nicht aktivierte Benutzer aktivieren |
| „Ich habe mein Telefon verloren“ (zweistufige Anmeldung) | ein Administrator stellt das Konto auf E-Mail-Codes um oder schaltet die zweite Stufe ab ([Zwei-Faktor-Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)) |
| „Schicken Sie mir keine E-Mails mehr“ | der Abmeldelink mit einem Klick in jeder optionalen E-Mail, oder ihre Einstellungsseite ([E-Mail-Einstellungen](../../../../eng-us/features/6.0/mail-preferences.md)) |
| „Was wissen Sie über mich?“ | Mitglieder können ihre E-Mail-Einstellungen als JSON oder CSV herunterladen; für alles andere Ihre Datenschutzroutine |

## Bevor Sie die Registrierung öffnen

- [ ] Die Registrierung legt Benutzer nur in der Gruppe Members ab.
- [ ] E-Mail-Bestätigung an; ein CAPTCHA an der Benutzerklasse ([Spamschutz für Formulare](../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md)).
- [ ] Die Rolle Member gewährt nur, was Mitglieder brauchen.
- [ ] Das Registrierungsformular sagt, welche Daten gespeichert werden und warum.
- [ ] Das Aufräumen inaktiver und nicht bestätigter Konten ist Teil der obigen Routine.

Diese Liste folgt der Checkliste für Website-Ersteller
([Registrierung von Mitgliedern und Bereiche nur für Mitglieder](../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md)).

## Verwandte Seiten

- [Mitglieder-Websites](../../unternehmen/mitglieder/README.md)
- [Registrierung von Mitgliedern](../../unternehmen/mitglieder/registrierung-von-mitgliedern.md)
- [Kunden- und Partnerbereiche](../../unternehmen/extranets/kunden-und-partnerbereiche.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
