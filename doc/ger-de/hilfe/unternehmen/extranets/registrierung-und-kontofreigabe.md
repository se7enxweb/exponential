---
title: Registrierung und Kontofreigabe
audience: businesses, admin, partner
category: business-solutions
order: 20
summary: Wie externe Benutzer in einem Exponential-Extranet Konten bekommen - offene Registrierung mit E-Mail-Bestätigung, die Mail „Neuer Benutzer registriert“ an den Administrator mit signierten Links zum Freigeben und Ablehnen, ein Freigabe-Workflow, der Konten zurückhält, bis jemand entscheidet, von Mitarbeitern angelegte Konten und Spamschutz.
keywords: Registrierung, Kontofreigabe, user/register, VerifyUserType, RegistrationFeedback, RegistrationEmail, signierte Links, freigeben, ablehnen, Nicht aktivierte Benutzer, user/activation, Freigabe-Ereignis, DefaultUserPlacement, CAPTCHA, Partnerkonten, Extranet, registration, account approval, signed links, approve, reject, unactivated users, Approve event, partner accounts, extranet
type: help
---
# Registrierung und Kontofreigabe

Diese Seite ist für Portalverantwortliche, die entscheiden, wie Partner und Kunden ein Konto bekommen. Exponential
unterstützt drei Wege: Externe Benutzer registrieren sich und bestätigen ihre E-Mail-Adresse, Ihre Mitarbeiter geben
jede Registrierung frei, bevor sie wirkt, oder Ihre Mitarbeiter legen die Konten selbst an. Die Seite erklärt jeden
Weg, was die freigebende Person erlebt und was den Ablauf schützt.

## Inhalt

1. [Drei Wege, Konten anzulegen](#drei-wege-konten-anzulegen)
2. [Wer sich überhaupt registrieren darf](#wer-sich-überhaupt-registrieren-darf)
3. [Wo neue Konten abgelegt werden](#wo-neue-konten-abgelegt-werden)
4. [E-Mail-Bestätigung](#e-mail-bestätigung)
5. [Die Mail an den Administrator mit signierten Links](#die-mail-an-den-administrator-mit-signierten-links)
6. [Konten bis zur Freigabe zurückhalten](#konten-bis-zur-freigabe-zurückhalten)
7. [Nicht aktivierte Benutzer](#nicht-aktivierte-benutzer)
8. [Von Ihren Mitarbeitern angelegte Konten](#von-ihren-mitarbeitern-angelegte-konten)
9. [Bots fernhalten](#bots-fernhalten)
10. [Checkliste](#checkliste)
11. [Verwandte Seiten](#verwandte-seiten)

## Drei Wege, Konten anzulegen

| Weg | Passt, wenn | Was ihn steuert |
|---|---|---|
| Registrierung mit E-Mail-Bestätigung, danach geprüft | viele Partner, Inhalte mit geringem Risiko in der ersten Gruppe | `VerifyUserType=email`, die Mail „Neuer Benutzer registriert“ |
| Registrierung, die zurückgehalten wird, bis eine freigebende Person entscheidet | jedes Konto geprüft werden muss, bevor es wirkt | ein Ereignis *Approve* im Veröffentlichungs-Workflow |
| Von Mitarbeitern angelegte Konten | wenige Partner, zuerst die Verträge | die Administration, keine öffentliche Registrierung |

## Wer sich überhaupt registrieren darf

In einer neuen Installation braucht die Registrierung die Richtlinie `user/register`. Für eine offene Registrierung im
Portal fügen Sie *User / Register* der Rolle Anonymous hinzu. Anmeldeseiten zeigen einen Link *Register* nur Besuchern,
die `user/register` öffnen dürfen. Eine aktualisierte Website lässt die Registrierung offen, bis
`PolicyOmitListExclude[]=user/register` gesetzt ist
([Rollen und Richtlinien, 8](../../../anleitungen/rollen-und-richtlinien.md#8-festlegen-wer-sich-registrieren-und-wer-knotendaten-lesen-darf)).

Die Funktion `user/register` hat keine Einschränkungen (`kernel/user/module.php`); die Richtlinie öffnet die
Registrierung also auf jedem Siteaccess der Installation, den die Rolle Anonymous erreicht. Wenn nur das Portal
Registrierungen annehmen soll, entscheiden Sie mit Ihrem Partner, wie: zum Beispiel eine eigene Installation, oder Sie
nehmen hin, dass `user/register` auf jedem Siteaccess antwortet, und legen jede Registrierung in eine Gruppe ohne
Rechte, bis jemand sie freigibt (siehe unten).

## Wo neue Konten abgelegt werden

`site.ini [UserSettings] DefaultUserPlacement` ist der Knoten der Benutzergruppe, in der neue Registrierungen landen
(ausgeliefert `12`). Setzen Sie ihn im Siteaccess des Portals auf eine eigene Gruppe, zum Beispiel *Partner-Bewerber*,
damit Registrierungen nie in einer Redakteursgruppe landen
([Mitgliederregistrierung, Registration settings](../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md#registration-settings)).

Ein nützliches Muster: Die Gruppe der Bewerber hat nur `user/login` für das Portal und Lesezugriff auf eine
Willkommensseite. Nach der Freigabe fügt ein Administrator das Konto der Gruppe seiner Partnerorganisation hinzu, die
die eigentlichen Rollen trägt ([Kunden- und Partnerbereiche](kunden-und-partnerbereiche.md)).

## E-Mail-Bestätigung

Mit `VerifyUserType=email` (der ausgelieferte Wert) wird das Konto über einen Link in einer Mail an den neuen Benutzer
aktiviert. Die Aktivierungsmail, die Mail „Aktivierung erneut gesendet“ und die Mail „Registrierung freigegeben“ sind
HTML-Mails mit einem Textteil und verwenden https-Links
([Signierte Links, The mails the new user gets](../../../../eng-us/features/6.0/registration-admin-links.md#the-mails-the-new-user-gets)).
`PasswordInRegistrationEmail=disabled` hält Passwörter aus Mails heraus.

## Die Mail an den Administrator mit signierten Links

Mit `RegistrationFeedback=email` schickt jede Registrierung dem Administrator (`RegistrationEmail`, sonst
`[MailSettings] AdminEmail`) die Mail „Neuer Benutzer registriert“. Seit 6.0.15 nennt sie das Konto und seinen Zustand
und enthält bis zu fünf Links: **View account**, **Activate account** oder **Approve**, **Reject and remove**,
**Open approval item** und **All unactivated users**
([Signierte Links in der Mail „New user registered“](../../../../eng-us/features/6.0/registration-admin-links.md)).

Was diese Links schützt ([Security](../../../../eng-us/features/6.0/registration-admin-links.md#security)):

- Jeder Link ist mit HMAC-SHA-256 unter einem aus dem Site-Geheimnis abgeleiteten Schlüssel signiert, nennt ein Konto
  und eine Aktion und läuft nach `RegistrationActionTTL` Sekunden ab (standardmäßig 7 Tage).
- Links zum Freigeben und Ablehnen funktionieren einmal.
- Das Öffnen eines Links ändert nichts. Die freigebende Person muss mit der Richtlinie `user/activation` angemeldet
  sein (oder Freigebende des wartenden Freigabeelements sein) und auf einer Bestätigungsseite eine Schaltfläche
  drücken. Mailscanner, die jeden Link öffnen, richten keinen Schaden an.
- Freigaben, Ablehnungen und jeder abgewiesene Link werden im Audit-Trail aufgezeichnet.

Wenn die `SiteURL` der Administration keine Adresse ist, die die freigebende Person aus der Mail erreichen kann, setzen
Sie `RegistrationAdminBaseURL`
([Rollen und Richtlinien, 7](../../../anleitungen/rollen-und-richtlinien.md#7-nicht-aktivierte-benutzer-aufräumen)).

## Konten bis zur Freigabe zurückhalten

Damit kein Konto funktioniert, bevor jemand es geprüft hat, kann die Veröffentlichung der Registrierung auf ein
Freigabeelement warten: ein Ereignis *Approve* in dem Workflow, der vor dem Veröffentlichen ausgelöst wird. Das Konto
ist dann im Zustand `workflow`, „Waiting for approval before it is published“; **Approve** in der Mail nimmt das
Freigabeelement an und veröffentlicht das Konto sofort, **Reject** lehnt es ab und entfernt das Konto
([What approve and reject do](../../../../eng-us/features/6.0/registration-admin-links.md#what-approve-and-reject-do)).

Das Ereignis Approve grenzt nach Sektion, Sprache, Version und Benutzergruppe ein, was es zurückhält
([Workflows, 3.2](../../../anleitungen/workflows.md#32-das-ereignis-freigeben-einrichten)). Bitten Sie Ihren Partner, es so
einzurichten, dass es für die Registrierungen des Portals gilt, und testen Sie vor dem Start eine Registrierung. Der
Cronjob des Workflows muss laufen
([Workflows, 6](../../../anleitungen/workflows.md#6-der-workflow-cronjob-wie-prozesse-fortgesetzt-werden)).

## Nicht aktivierte Benutzer

**Benutzer > Nicht aktivierte Benutzer** listet Personen auf, die sich registriert, aber nie auf ihren Aktivierungslink
geklickt haben. Freigebende können sie aktivieren, die Aktivierungsmail erneut senden oder sie entfernen;
Registrierungen, die älter als 30 Tage sind, sind mit **Old** gekennzeichnet
([Rollen und Richtlinien, 7](../../../anleitungen/rollen-und-richtlinien.md#7-nicht-aktivierte-benutzer-aufräumen)).

## Von Ihren Mitarbeitern angelegte Konten

Für eine kleine Zahl von Partnern legen Mitarbeiter die Konten in der Administration unter der Gruppe des Partners an.
Eine öffentliche Registrierung ist nicht nötig. Die Passwortregeln gelten für jedes Formular, das ein Passwort setzt
([Ihr Passwort ändern](../../../../eng-us/features/6.0/modern-password-change.md)), und Benutzer setzen vergessene Passwörter
selbst zurück ([Das Passwort eines Benutzers zurücksetzen](../../../../eng-us/features/6.0/reset-user-password.md)).

## Bots fernhalten

- Ein CAPTCHA-Feld (reCAPTCHA oder hCaptcha) in der Benutzerklasse schützt das Registrierungsformular
  ([Spamschutz für Formulare](../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md)).
- Formular-Tokens schützen jedes Formular gegen seitenübergreifende Anfragen.
- Nicht aktivierte Registrierungen werden regelmäßig bereinigt.

## Checkliste

- [ ] Weg der Kontoanlage gewählt.
- [ ] `user/register` nur dann an Anonymous vergeben, wenn die Registrierung offen ist.
- [ ] `DefaultUserPlacement` zeigt auf eine Bewerbergruppe mit minimalen Rechten.
- [ ] `RegistrationEmail` erreicht ein überwachtes Postfach.
- [ ] Links zum Freigeben und Ablehnen aus der Mail getestet.
- [ ] Wenn Konten zurückgehalten werden müssen: Ereignis Approve eingerichtet und in einem Test eine Registrierung
      freigegeben und eine abgelehnt.
- [ ] CAPTCHA im Registrierungsformular.
- [ ] Das Registrierungsformular sagt, welche Daten aufbewahrt werden und warum.

## Verwandte Seiten

- [Registrierung von Mitgliedern](../mitglieder/registrierung-von-mitgliedern.md)
- [Mitgliederregistrierung und Bereiche nur für Mitglieder](../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md)
- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../anleitungen/rollen-und-richtlinien.md)
- [Einwilligung und Formulare](../sicherheit/einwilligung-und-formulare.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
