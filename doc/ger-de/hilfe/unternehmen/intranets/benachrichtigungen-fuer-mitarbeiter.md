---
title: Benachrichtigungen für Mitarbeiter
audience: businesses, admin, editor
category: business-solutions
order: 90
summary: Wie Mitarbeiter in einem Exponential-Intranet von neuen und geänderten Inhalten erfahren - Einträgen folgen, Digests, Mails zur Zusammenarbeit für Freigebende, die Prüfung der E-Mail-Einstellungen, der Cronjob für Benachrichtigungen und die Statusseite und wie Sie vermeiden, Menschen zu überschwemmen.
keywords: Benachrichtigungen, benachrichtigen, Abonnements, Digest, täglicher Digest, Benachrichtigung zur Zusammenarbeit, Freigebende, E-Mail-Einstellungen, Mailkategorien, DefaultOn, Cronjob für Benachrichtigungen, frequent, Status der Benachrichtigungen, ohne Benachrichtigung veröffentlichen, interne Kommunikation, notifications, notify me, subscriptions, digest, daily digest, collaboration notification, approvers, e-mail preferences, mail categories, notification cronjob, notification status, publish without notification, staff communication
type: help
---
# Benachrichtigungen für Mitarbeiter

Diese Seite ist für Intranetverantwortliche und Teams der internen Kommunikation. Sie erklärt, wie Exponential
Mitarbeiter über neue Inhalte informiert, was jede Person wählen kann, was die Organisation am Laufen halten muss und
wie Sie auf der Seite von „nützlich“ statt „störend“ bleiben. Die Sicht der Benutzer steht unter
[Benachrichtigungen: die Anleitung für Benutzer](../../../../eng-us/features/6.0/notifications.md), die Sicht des Betriebs in
[der Anleitung für Administratoren](../../../anleitungen/benachrichtigungen-administrator.md).

## Inhalt

1. [Worüber Mitarbeiter informiert werden können](#worüber-mitarbeiter-informiert-werden-können)
2. [Einträgen folgen](#einträgen-folgen)
3. [Sofort oder als Digest](#sofort-oder-als-digest)
4. [Mails für Freigebende](#mails-für-freigebende)
5. [Die Prüfung der E-Mail-Einstellungen](#die-prüfung-der-e-mail-einstellungen)
6. [Was laufen muss](#was-laufen-muss)
7. [Störungen vermeiden](#störungen-vermeiden)
8. [Checkliste](#checkliste)
9. [Verwandte Seiten](#verwandte-seiten)

## Worüber Mitarbeiter informiert werden können

| Mail | Wann | Wer sie wählt |
|---|---|---|
| Benachrichtigung über Inhalte | unterhalb eines Eintrags, dem die Person folgt, wird etwas veröffentlicht, und die Person darf es lesen | die Person (folgen / nicht mehr folgen) |
| Digest | dieselben Nachrichten, gesammelt in einer Mail pro Tag, Woche oder Monat | die Person |
| Benachrichtigung zur Zusammenarbeit | ein Freigabeelement, an dem die Person beteiligt ist, als Autor oder Freigebende | die Person, pro Art von Element |
| Kontomails (Aktivierung, Passwort geändert) | immer | niemand; wesentliche Mails werden immer verschickt |

All das braucht ein Konto mit der Richtlinie `notification/use`
([Benachrichtigungen: die Anleitung für Benutzer](../../../../eng-us/features/6.0/notifications.md)). Geben Sie sie der Rolle
Mitarbeiter.

## Einträgen folgen

Eine Person folgt einem Eintrag mit **Benachrichtigen** in seinem Menü oder mit **Einträge hinzufügen** unter **Meine
Benachrichtigungen** (`/notification/settings`). Allein das Öffnen der Adresse von Benachrichtigen ändert nichts; erst
die Schaltfläche abonniert. Die Einstellungsseite listet alles auf, dem gefolgt wird, mit Filtern und Blättern, und
**Ausgewählte entfernen** beendet das Folgen ([Items I follow](../../../../eng-us/features/6.0/notifications.md#items-i-follow)).

Mails werden nur für Inhalte verschickt, die die Person lesen darf. Die Berechtigungen gelten auch in ihrem Postfach;
ein Mitglied des Vertriebs, das dem ganzen Intranet folgt, erfährt nichts von neuen Personalseiten
([Einschränkungen von Inhaltsrichtlinien](../../../../eng-us/features/6.0/content-limitation-handlers.md#what-you-can-do) nennt
Benachrichtigungen für Teilbäume unter den Stellen, an denen Berechtigungen geprüft werden).

Gute Einträge, die Sie Mitarbeitern empfehlen können: der Ordner mit Firmenneuigkeiten, der Ordner ihrer Abteilung, der
Ordner mit Richtlinien.

## Sofort oder als Digest

Normalerweise wird jede Änderung sofort gemailt. Ein Digest hält Nachrichten zurück und schickt eine Mail täglich zu
einer gewählten Stunde, wöchentlich an einem gewählten Tag oder monatlich an einem gewählten Tag des Monats; er wird nur
verschickt, wenn etwas geschehen ist ([E-mail digest](../../../../eng-us/features/6.0/notifications.md#e-mail-digest)). Für die
meisten Mitarbeiter ist ein täglicher Digest die freundliche Voreinstellung, die Sie empfehlen können.

## Mails für Freigebende

Wenn die Freigabe vor dem Veröffentlichen verwendet wird ([Freigabe-Workflows](freigabe-workflows.md)), erfahren
Freigebende über Benachrichtigungen zur Zusammenarbeit, dass etwas wartet: Sie markieren auf ihrer Einstellungsseite
unter **Benachrichtigung zur Zusammenarbeit** die Art **Freigabe**
([Collaboration notification](../../../../eng-us/features/6.0/notifications.md#collaboration-notification)). Machen Sie das zum
Teil der Einarbeitung von Freigebenden; eine freigebende Person, die die Mail nie bekommt, verzögert jeden Artikel.

## Die Prüfung der E-Mail-Einstellungen

Seit 6.0.15 durchläuft jede Mail eine Prüfung der Einstellungen des Empfängers
([E-Mail-Einstellungen: einrichten](../../../anleitungen/mail-einstellungen-administrator.md)). In
`settings/mailpreferences.ini` sind Benachrichtigungen über Inhalte (`[Category_content]`) und Mails zur Zusammenarbeit
(`[Category_collaboration]`) optionale Kategorien mit `DefaultOn=false`: Sie sind Opt-in. Wesentliche Mails wie zur
Kontosicherheit gehen immer.

Was das für ein Intranet bedeutet:

- Der Zustand dieser Kategorien wird aus den vorhandenen Abonnements der Person gelesen
  (`HandlerClass=expNotificationMailCategoryHandler`); wer vorher Einträgen gefolgt ist, sieht die Benachrichtigungen
  über Inhalte also als eingeschaltet. Eine Person kann die Kategorie unter **Meine E-Mail-Einstellungen** ausschalten,
  und jede optionale Mail enthält einen Link zum Abmelden mit einem Klick
  ([E-Mail-Einstellungen: die Anleitung für Benutzer](../../../../eng-us/features/6.0/mail-preferences.md)).
- `AllowDefaultOn=enabled` schaltet eine optionale Kategorie für alle ein, die nie gewählt haben. Der Kommentar in
  `mailpreferences.ini` beschränkt das auf Websites, deren Empfänger einem Recht unterliegen, das es für diese Mail
  erlaubt. Ob das für Ihre Mitarbeiter gilt, ist eine Frage für Ihre Rechtsberatung; diese Seite ist keine
  Rechtsberatung.

## Was laufen muss

Nichts wird verschickt, bevor ein **Lauf** der Benachrichtigungen stattfindet. Den Lauf startet die Cronjob-Gruppe
`frequent`, die Schaltfläche **Jetzt ausführen** auf der Statusseite oder `./console exp:notification:run`
([How it works in one minute](../../../anleitungen/benachrichtigungen-administrator.md#1-wie-es-funktioniert-in-einer-minute)).

**Setup > Benachrichtigungen** (`/notification/status`, Richtlinie `notification/administrate`) zeigt wartende
Ereignisse, für Digests aufbewahrte Nachrichten, Abonnements, den letzten Lauf und die letzten Läufe. Wenn der letzte
Lauf alt ist, läuft der Cronjob nicht, und niemand bekommt Mails
([Die Statusseite](../../../anleitungen/benachrichtigungen-administrator.md#3-die-statusseite)).

Die Mailzustellung selbst (Absenderadresse, SPF und DKIM bei Ihrem Mailanbieter) ist Aufgabe des Hosters
([Mailzustellung](../../hosting/betrieb/mailzustellung.md)).

## Störungen vermeiden

- Empfehlen Sie Digests statt sofortiger Mails.
- Lassen Sie Redakteure Korrekturen **Ohne Benachrichtigung veröffentlichen**
  ([Ohne Benachrichtigung veröffentlichen](../../../anleitungen/benachrichtigungen-administrator.md#8-veröffentlichen-ohne-benachrichtigung)).
- Halten Sie unternehmensweite Ankündigungen in einem Nachrichtenordner, damit das Folgen eine klare Entscheidung ist.
- Verwenden Sie für eine kuratierte wöchentliche Zusammenfassung einen Newsletter statt vieler einzelner Mails
  ([Newsletter](../newsletter/README.md)).
- Prüfen Sie einmal im Monat auf der Statusseite, ob es Abonnements für Inhalte gibt, die nicht mehr existieren.

## Checkliste

- [ ] Die Rolle Mitarbeiter hat `notification/use`.
- [ ] Die Cronjob-Gruppe `frequent` läuft; die Statusseite zeigt einen kürzlichen Lauf.
- [ ] Absender und Zustellung der Mails mit einer echten Mitarbeiteradresse getestet.
- [ ] Freigebende haben die Benachrichtigung zur Zusammenarbeit Freigabe markiert.
- [ ] Redakteure dürfen ohne Benachrichtigung veröffentlichen.
- [ ] Eine kurze Notiz an die Mitarbeiter, wie man Ordnern folgt und einen Digest wählt.

## Verwandte Seiten

- [Benachrichtigungen: die Anleitung für Benutzer](../../../../eng-us/features/6.0/notifications.md)
- [Benachrichtigungen: die Anleitung für Administratoren](../../../anleitungen/benachrichtigungen-administrator.md)
- [Neuigkeiten und Ankündigungen](neuigkeiten-und-ankuendigungen.md)
- [Kommunikation mit Mitgliedern](../mitglieder/kommunikation-mit-mitgliedern.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
