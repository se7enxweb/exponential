---
title: Wie lege ich einen Benutzer oder eine Benutzergruppe an?
audience: admin
category: manual
order: 610
summary: Öffnen Sie im Tab Benutzer die Gruppe, zu der das Konto gehört, klicken Sie auf Neu erstellen und wählen Sie Benutzer (oder Benutzergruppe), füllen Sie den Namen und die Felder des Benutzerkontos aus und veröffentlichen Sie; das neue Konto erhält die Rollen seiner Gruppe.
keywords: Benutzer anlegen, neuer Benutzer, Konto anlegen, Benutzergruppe, Tab Benutzer, Benutzerkonto, Benutzername, Passwort, E-Mail, Neu erstellen, Rollen einer Gruppe, add user, new user, create account, user group, Users tab, user account, username, password, email, Create new
context: content/view
type: help
---
# Wie lege ich einen Benutzer oder eine Benutzergruppe an?

Benutzer und Benutzergruppen sind Inhaltsobjekte im Bereich **Benutzer** des Inhaltsbaums. Ein Benutzer erhält seine
Rechte über die Rollen der Gruppen, in denen er liegt; der Ort, an dem Sie das Konto anlegen, entscheidet also darüber,
was die Person tun darf:

- **Editors** für Personen, die Inhalte schreiben und veröffentlichen (die Rolle Editor und ihre Begleiter,
  [Was die Redakteursrolle erlaubt](../was-die-redakteursrolle-erlaubt.md));
- **Administrator users** nur für die Systemadministratoren, die Entwickler und die Eigentümer der Website;
- **Members** für die Mitglieder der öffentlichen Website, die von Redakteuren verwaltet werden
  ([Mitglieder verwalten](../mitglieder-verwalten.md)).

Den ganzen Weg vom Konto eines neuen Redakteurs bis zu seiner ersten Anmeldung, mit einem Bildschirmfoto zu jedem
Schritt, zeigt [Konten für Redakteure und Administratoren einrichten](../konten-fuer-redakteure-und-administratoren-einrichten.md).

## Wer das tun kann

Benutzer, die im Benutzerbaum Objekte der Klassen **Benutzer** und **Benutzergruppe** anlegen (`content/create` mit
diesen Klassen, meist auf die Sektion Users beschränkt) und den Baum lesen dürfen. Administratoren dürfen das in jeder
Gruppe. Redakteure dürfen es in Members (Rolle *Member management*) und können in Editors das Konto eines neuen
Redakteurs beantragen, das ein Administrator freigibt (Rolle *Editor account requests*).

## Schritte

1. Klicken Sie im oberen Menü auf **Benutzer**.
   Der Benutzerbaum öffnet sich mit seinen Gruppen.
2. Öffnen Sie die Gruppe, zu der die Person gehört, zum Beispiel „Redakteure“.
3. Klicken Sie unter **Unterelemente** auf **Neu erstellen** und wählen Sie **Benutzer**.
   Um stattdessen eine Gruppe anzulegen, wählen Sie **Benutzergruppe**, füllen Sie ihren Namen aus und veröffentlichen
   Sie; öffnen Sie sie dann und fahren Sie hier fort.
4. Füllen Sie die Namensfelder der Person aus.
5. Füllen Sie beim Benutzerkonto **Benutzername**, **E-Mail** und, falls das Formular danach fragt,
   **E-Mail-Adresse bestätigen** aus.
6. Füllen Sie **Passwort** und **Passwort bestätigen** aus.
   Das Passwort muss die Passwortregeln der Website erfüllen, zum Beispiel eine Mindestlänge. Legen Sie das Konto für
   jemand anderen an, geben Sie ihm ein vorläufiges Passwort und lassen Sie **Passwort bei der nächsten Anmeldung
   ändern** angekreuzt: Die Person wählt dann bei der ersten Anmeldung ihr eigenes.
7. Klicken Sie auf **Zur Veröffentlichung schicken**.
   **Bestätigen Sie, dass Sie es sind** fragt nach Ihrem eigenen Passwort, bevor ein Konto angelegt wird; geben Sie es
   ein und klicken Sie auf **Bestätigen**. Das Konto wird angelegt und in der Gruppe aufgeführt.
8. Öffnen Sie das neue Konto und sehen Sie sich seinen Tab **Rollen** an, um zu prüfen, was es von seiner Gruppe
   erhalten hat.
9. Melden Sie sich ab und einmal als neuer Benutzer an, um Benutzername und vorläufiges Passwort zu prüfen; geben Sie
   der Person dann den Benutzernamen und auf einem zweiten Weg das vorläufige Passwort.

## Was schiefgehen kann

- **„Der Benutzername existiert bereits. Bitte wählen Sie einen anderen.“ oder „Ein Benutzer mit dieser E-Mail ist bereits vorhanden.“**
  Jeder Benutzername existiert nur einmal. Mit
  `site.ini [UserSettings] RequireUniqueEmail=true`, der Voreinstellung, darf auch jede E-Mail-Adresse nur zu einem
  Konto gehören.
- **Der Benutzername enthält einen Tippfehler.** Nach dem Anlegen des Kontos lässt er sich im Formular nicht mehr
  ändern. Entfernen Sie das Konto und legen Sie es neu an, oder fragen Sie einen Entwickler.
- **Sie haben das Passwort leer gelassen.** Die Website erzeugt dann ein zufälliges Passwort, das niemandem mitgeteilt
  wird. Die Person setzt ihr eigenes über den Link für vergessene Passwörter auf der Anmeldeseite.
- **Der neue Benutzer kann nichts tun.** Die Gruppe hat keine Rolle, oder die Rolle hat keine Richtlinie für den Ort,
  an dem die Person arbeitet. Weisen Sie der Gruppe eine Rolle zu
  ([Wie gebe ich einer Gruppe Bearbeitungsrechte nur für eine Sektion?](wie-gebe-ich-einer-gruppe-bearbeitungsrechte-fuer-eine-sektion.md)).
- **Benutzer oder Benutzergruppe fehlt in der Liste Neu erstellen.** Ihre Rolle erlaubt es nicht, diese Klassen hier
  anzulegen, oder die Klassen heißen auf Ihrer Website anders.

**Datenschutz:** Sie speichern personenbezogene Daten (Name, E-Mail-Adresse). Erfassen Sie nur, was die Person für
ihre Arbeit auf der Website braucht, und entfernen oder deaktivieren Sie das Konto, wenn sie geht
([Wie deaktiviere oder entsperre ich ein Benutzerkonto?](wie-deaktiviere-oder-entsperre-ich-ein-benutzerkonto.md)).

## Verwandte Seiten

- [Konten für Redakteure und Administratoren einrichten](../konten-fuer-redakteure-und-administratoren-einrichten.md)
- [Mitglieder verwalten](../mitglieder-verwalten.md)
- [Wie verschiebe ich einen Benutzer in eine andere Gruppe?](wie-verschiebe-ich-einen-benutzer-in-eine-andere-gruppe.md)
- [Wie sehe ich, was ein Benutzer tun darf?](wie-sehe-ich-was-ein-benutzer-tun-darf.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)
- [Das Administratorpasswort ändern](../administratorpasswort-aendern.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
