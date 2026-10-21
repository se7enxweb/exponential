---
title: Konten, Passwörter und zweistufige Anmeldung
audience: customer, admin, editor
category: customer-success
order: 8
summary: Die Regeln des Betreibers für die Konten des Teams auf einer Exponential-Website - persönliche Konten, die ausgelieferten Passwortregeln, zweistufige Anmeldung als Option oder Pflicht, verlorene Telefone und gesperrte Konten - und was Sie den Administrator einschalten lassen sollten.
keywords: Konten, Passwörter, zweistufige Anmeldung, Zwei-Faktor, 2FA, Authenticator, E-Mail-Code, Passwortregeln, gesperrtes Konto, geteilte Anmeldung, Website-Betreiber, accounts, passwords, two-step sign-in, two-factor, authenticator, e-mail code, password rules, locked account, shared login, site owner
type: help
---
# Konten, Passwörter und zweistufige Anmeldung

Diese Seite ist für den Betreiber oder die Leitung, die die Kontenregeln für das Team festlegt. Die Klicks für jede
Person stehen unter [Ihr Konto und die Anmeldung](../../konten-und-anmeldung.md); die Einrichtung für den
Administrator unter [Zwei-Faktor-Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md). Hier finden Sie, was zu entscheiden
und was zu prüfen ist.

## Inhalt

1. [Vier Regeln für das Team](#vier-regeln-für-das-team)
2. [Passwörter: was die Software bereits tut](#passwörter-was-die-software-bereits-tut)
3. [Zweistufige Anmeldung: optional oder verpflichtend](#zweistufige-anmeldung-optional-oder-verpflichtend)
4. [Wenn etwas schiefgeht](#wenn-etwas-schiefgeht)
5. [Was Sie jedes Quartal prüfen sollten](#was-sie-jedes-quartal-prüfen-sollten)

## Vier Regeln für das Team

1. **Ein persönliches Konto pro Person.** Keine geteilten Anmeldungen; die Seite zur Kontenüberprüfung findet sie mit
   dem Filter **Doppelter Benutzername** ([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)).
2. **Eine echte E-Mail-Adresse an jedem Konto.** Passwort-Zurücksetzung, die E-Mail „Ihr Passwort wurde geändert“ und
   E-Mail-Codes gehen dorthin.
3. **Zweistufige Anmeldung für alle, die veröffentlichen können**
   ([Die Website an die Redaktion übergeben](../../../../eng-us/help/creators/website-creators/handing-over-to-editors.md)), und immer
   für Administratoren.
4. **Konten enden am letzten Arbeitstag** ([Ausscheiden von Mitarbeitenden](ausscheiden-von-mitarbeitenden.md)).

## Passwörter: was die Software bereits tut

| Verhalten | Quelle |
|---|---|
| Ein neues Passwort muss mindestens 10 Zeichen haben (`[UserSettings] MinPasswordLength=10`), und eine Prüfung auf schwache Passwörter ist eingeschaltet (`WeakPasswordCheck=enabled`) | `settings/site.ini` |
| Die Änderungsseite listet die Anforderungen neben dem Feld auf, kann ein starkes Passwort erzeugen und schreibt ein eingegebenes Passwort nie in die Seite zurück | [Ihr Passwort ändern](../../../../eng-us/features/6.0/modern-password-change.md) |
| Nach einer Änderung werden die anderen Sitzungen der Person abgemeldet, und eine E-Mail „Ihr Passwort wurde geändert“ geht an die Adresse des Kontos; niemand kann diese E-Mail abschalten | ebenda |
| Dieselben Regeln gelten für die Registrierung, das Bearbeiten eines Kontos und das Zurücksetzen über die Konsole | ebenda |
| Jede Änderung und jede abgelehnte Änderung steht im Prüfprotokoll; das Passwort nie | ebenda |

`[UserSettings] MaxNumberOfFailedLogin` sperrt ein Konto nach so vielen fehlgeschlagenen Anmeldungen; der
ausgelieferte Wert ist `0`, was keine Sperre bedeutet (`settings/site.ini`). Fragen Sie Ihren Administrator, ob Ihre
Website eine Grenze setzt.

## Zweistufige Anmeldung: optional oder verpflichtend

Die zweistufige Anmeldung kommt mit der Erweiterung `sevenx_authentication_2fa`: Nach dem Passwort folgt ein
sechsstelliger Code aus einer Authenticator-App oder ein per E-Mail gesendeter Code ([Zwei-Faktor-Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).

| Einstellung (`[General] Enforce2FA`) | Was Ihr Team erlebt |
|---|---|
| `disabled` (Standard) | jede Person schaltet sie unter *Mein Konto* > *Zweistufige Anmeldung* selbst ein |
| `enabled` mit `DefaultMethod=totp` | eine Person ohne zweiten Schritt richtet die App direkt nach dem Passwort ein |
| `enabled` mit `DefaultMethod=email` | eine Person ohne zweiten Schritt bekommt sofort E-Mail-Codes |

Der Demonstrationsserver betreibt sie seit dem 6. Oktober 2026 als Opt-in (dieselbe Quelle). Wenn Sie sie
verpflichtend machen wollen, bitten Sie den Administrator, **zuerst die Administratoren einzurichten**, damit niemand
ohne Telefon in der Hand überrascht wird.

> **Hinweis**
> Eine später angelegte Rolle braucht die Richtlinien `user2fa/setup` und `user2fa/verify`, bevor ihre Benutzer die
> zweistufige Anmeldung einrichten können ([Zwei-Faktor-Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)). Sagen Sie
> Ihrem Administrator Bescheid, wenn Sie eine neue Rolle anfordern.

## Wenn etwas schiefgeht

| Situation | Was zu tun ist |
|---|---|
| Jemand hat sein Passwort vergessen | die Person nutzt „Passwort vergessen?“ auf der Anmeldeseite ([Ihr Konto und die Anmeldung](../../konten-und-anmeldung.md)) |
| Jemand hat sein Telefon verloren | ein Administrator setzt das Feld *Zweistufige Anmeldung* des Benutzers auf *E-Mail one-time code* oder *Deaktiviert* und veröffentlicht; die Person richtet die App neu ein ([Zwei-Faktor-Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)) |
| Ein Konto ist nach fehlgeschlagenen Anmeldungen gesperrt | wer die Konten verwaltet, nutzt **Entsperren** unter Benutzerkonten prüfen ([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)) |
| Niemand kann sich mehr als Administrator anmelden | Ihr Hoster oder Partner setzt das Passwort über die Kommandozeile zurück ([Ein Benutzerpasswort zurücksetzen](../../../../eng-us/features/6.0/reset-user-password.md)) |
| Sie vermuten, dass jemand anderes ein Konto benutzt hat | deaktivieren Sie es, und bitten Sie dann den Administrator, sich das Prüfprotokoll anzusehen ([Sicherheit und Prüfprotokoll](../../../anleitungen/sicherheit-und-audit.md)) |

## Was Sie jedes Quartal prüfen sollten

- [ ] Benutzerkonten prüfen, Filter **Doppelter Benutzername** und **Doppelte E-Mail-Adresse**: keine, oder jede erklärt.
- [ ] Filter **Gesperrt**: jede Sperre nachvollzogen.
- [ ] Jeder Administrator hat die zweistufige Anmeldung eingeschaltet.
- [ ] Das Standard-Administratorpasswort der Installation wurde längst geändert
      ([Das Admin-Passwort ändern](../../administratorpasswort-aendern.md)).

## Verwandte Seiten

- [Ihr Konto und die Anmeldung](../../konten-und-anmeldung.md)
- [Benutzerkonten und Passwörter](../../unternehmen/sicherheit/benutzerkonten-und-passwoerter.md)
- [Zwei-Faktor und Anmeldung für Intranets](../../unternehmen/intranets/zwei-faktor-und-anmeldung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
