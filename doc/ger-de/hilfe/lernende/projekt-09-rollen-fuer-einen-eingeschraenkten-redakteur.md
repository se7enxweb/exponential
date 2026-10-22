---
title: "Projekt 9: Rollen für einen eingeschränkten Redakteur"
audience: student
category: learning
order: 33
summary: Lernprojekt 9 - eine Benutzergruppe und einen Testredakteur anlegen, eine Rolle bauen, deren Richtlinien der Gruppe nur das Bearbeiten des Vereinsteilbaums erlauben, sie mit einer Einschränkung zuweisen, sich als Redakteur anmelden, um nachzuweisen, was erlaubt und was verweigert ist, die Rolle als Sätze lesen und einen Zugriffsplan mit Nachweisen abgeben, bewertet mit einem Bewertungsraster.
keywords: lernprojekt, rollen, richtlinien, einschränkungen, benutzergruppe, zuweisen, teilbaum, sektion, redakteur, zugriffskontrolle, minimale rechte, content/edit, content/create, lernende, study project, roles, policies, limitations, user group, assign, subtree, section, editor, access control, least privilege, learner
type: help
---
# Projekt 9: Rollen für einen eingeschränkten Redakteur

Echte Websites geben niemals allen Redakteuren das Administratorkonto. In diesem Projekt geben Sie einer Gruppe von
Vereinsredakteuren das Recht, an ihrem eigenen Teil der Website zu arbeiten, und sonst nichts, und Sie weisen es
nach. Die Schritte folgen [Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md).

## Inhalt

1. [Auf einen Blick](#auf-einen-blick)
2. [Ziel](#ziel)
3. [Bevor Sie beginnen](#bevor-sie-beginnen)
4. [Aufgaben](#aufgaben)
5. [Abgabe](#abgabe)
6. [Bewertungsraster](#bewertungsraster)
7. [Zusatzziele](#zusatzziele)

## Auf einen Blick

| | |
|---|---|
| Stufe | Fortgeschritten |
| Zeit | Etwa 2 Stunden |
| Pfade | Administrator |
| Voraussetzungen | [Projekt 2](projekt-02-eine-kleine-website-struktur.md); zwei Browser oder ein privates Fenster, um zweimal angemeldet zu sein |

## Ziel

Eine Benutzergruppe „Vereinsredaktion“ mit einem Testbenutzer, eine Rolle, die der Gruppe erlaubt, Inhalte nur
innerhalb des Teilbaums „Studierendenverein“ anzulegen und zu bearbeiten, eine Zuweisung und ein Nachweis aus Sicht
des Testbenutzers, was funktioniert und was verweigert wird.

## Bevor Sie beginnen

Lesen Sie [Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md), die Abschnitte
„Kurz gesagt“ und 1 bis 5. Wissen Sie:

- Eine **Rolle** ist eine Gruppe von **Richtlinien**; sie bewirkt nichts, bis sie Benutzern oder Gruppen
  **zugewiesen** wird.
- Ein Benutzer hat jede Richtlinie jeder Rolle, die ihm oder einer seiner Gruppen zugewiesen ist.
- Jede Änderung im Rolleneditor geschieht in einem Entwurf; nichts ändert sich, bis Sie **Speichern** drücken.
- Weisen Sie Rollen bevorzugt Gruppen zu.

## Aufgaben

1. **Schreiben Sie zuerst einen Zugriffsplan**: was Vereinsredakteure dürfen (sich an der Administrationsoberfläche
   anmelden, Inhalte lesen, im Vereinsteilbaum anlegen und bearbeiten, Bilder dafür hochladen) und was nicht (Setup,
   Benutzer, andere Teile des Baums).
2. **Legen Sie die Gruppe und den Benutzer an.** Legen Sie unter **Benutzer** eine Gruppe „Vereinsredaktion“ an und
   darin einen Benutzer mit einer Adresse unter `example.invalid` und einem Passwort mit mindestens 10 Zeichen.
3. **Lesen Sie vorhandene Rollen.** Öffnen Sie **Benutzer > Rollen und Richtlinien**. Sehen Sie sich **Editor** (falls
   Ihr Site-Paket sie hat) und **Anonymous** an; lesen Sie ihre Richtlinien als Sätze
   ([Abschnitt 2](../../anleitungen/rollen-und-richtlinien.md#2-eine-rolle-lesen)).
4. **Bauen Sie die Rolle.** **Neue Rolle**, Name „Vereinsredaktion“. Fügen Sie mit dem Assistenten Richtlinien hinzu
   (Modul, Funktion, Einschränkungen), zum Beispiel `content/read`, `content/create` und `content/edit` mit einer
   Einschränkung **Teilbaum**, und die Richtlinie, mit der sich der Benutzer am Siteaccess der
   Administrationsoberfläche anmelden darf. Drücken Sie **Speichern**
   ([Abschnitt 3](../../anleitungen/rollen-und-richtlinien.md#3-eine-rolle-mit-eingeschränkten-richtlinien-anlegen)).
   Vermeiden Sie jede Richtlinie über alle Module, die vollen Zugriff gibt, und jede Richtlinie über das Modul role,
   mit der sich Benutzer selbst jeden Zugriff geben können ([Kurz gesagt](../../anleitungen/rollen-und-richtlinien.md#kurz-gesagt)).
5. **Weisen Sie sie zu**, der Gruppe, oder mit **Mit Einschränkungen zuordnen** dem Teilbaum
   ([Abschnitt 5](../../anleitungen/rollen-und-richtlinien.md#5-die-rolle-zuweisen)).
6. **Testen Sie als Redakteur** in einem privaten Fenster: Melden Sie sich an, legen Sie im Vereinsordner einen Artikel
   an, bearbeiten Sie einen anderen und versuchen Sie dann, außerhalb des Teilbaums etwas anzulegen und **Setup** zu
   öffnen. Notieren Sie jedes Ergebnis und jede Meldung.
7. **Korrigieren und testen Sie erneut**, bis Plan und Ergebnisse übereinstimmen. Jede Korrektur ist eine Änderung an
   der Rolle; geben Sie dem Testbenutzer niemals die Rolle Administrator.
8. **Lesen Sie die Seite der Rolle** mit ihren Zahlen und ihren Richtlinien als Sätze und vergleichen Sie sie mit
   Ihrem Plan.

## Abgabe

- Der Zugriffsplan und die endgültige Rolle, wie die Seite der Rolle sie zeigt (Richtlinien als Sätze).
- Screenshots aus Sicht des Redakteurs: eine erfolgreiche Bearbeitung innerhalb des Teilbaums und eine verweigerte
  Aktion außerhalb davon.
- Ein kurzer Bericht: welche Richtlinie Sie zuletzt hinzugefügt haben und warum, und was „minimale Rechte“ für diese
  Gruppe bedeutet.

## Bewertungsraster

Bewerten Sie die vier gemeinsamen Kriterien aus [Die eigene Arbeit bewerten](die-eigene-arbeit-bewerten.md) und diese
Prüfungen (8):

1. Der Plan wurde vor der Rolle geschrieben.
2. Die Rolle ist einer Gruppe zugewiesen, nicht nur dem einzelnen Benutzer.
3. Keine Richtlinie über alle Module und keine über das Modul role.
4. Anlegen und Bearbeiten sind auf den Vereinsteilbaum beschränkt.
5. Der Testbenutzer kann innerhalb des Teilbaums arbeiten, gezeigt mit einem Screenshot.
6. Der Testbenutzer wird außerhalb davon abgewiesen, gezeigt mit der Meldung.
7. Die Adresse des Testbenutzers liegt unter `example.invalid`.
8. Der Bericht erklärt den Unterschied zwischen einer Rolle und ihrer Zuweisung.

## Zusatzziele

- Schränken Sie die Rolle zusätzlich nach **Klasse** ein (nur Artikel und Bild) und testen Sie erneut.
- Fügen Sie einen Übersetzer hinzu, der nur eine Sprache bearbeiten darf ([Inhaltssprachen, Abschnitt 7](../../anleitungen/inhaltssprachen.md#7-inhalte-im-editor-übersetzen)).
- Geben Sie den Redakteuren den Siteaccess `editor` statt `admin`: Er ist die Administrationsoberfläche nur für
  Inhalte, ohne Setup, Design und Entwicklerwerkzeuge ([Exponential 6.0 installieren](../../INSTALL.md#1-was-sie-erhalten-und-der-schnellstart)).
- Schalten Sie für den Testbenutzer die Anmeldung in zwei Schritten ein ([Wie schalte ich die Anmeldung in zwei Schritten ein?](../handbuch/wie-schalte-ich-die-zweistufige-anmeldung-ein.md)).

## Verwandte Seiten

- [Lernprojekte: Überblick](lernprojekte-ueberblick.md)
- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md)
- [Eine Lernwebsite mit einer Klasse teilen](eine-lernwebsite-mit-einer-klasse-teilen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
