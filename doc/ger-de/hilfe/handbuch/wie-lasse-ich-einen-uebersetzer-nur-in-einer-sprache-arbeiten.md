---
title: Wie lasse ich einen Übersetzer nur in einer Sprache arbeiten?
audience: admin
category: manual
order: 570
summary: Legen Sie eine Rolle mit content/read sowie content/edit und content/translate an, beide per Language auf die Sprache des Übersetzers eingeschränkt, und weisen Sie sie der Gruppe der Übersetzer zu; diese können dann diese Sprache übersetzen und bearbeiten, die anderen aber nicht ändern.
keywords: Übersetzer, Rolle, Richtlinie, Spracheinschränkung, eingeschränkten Zugriff gewähren, Übersetzungsrechte, content/translate, content/edit, content/create, translator, role, policy, language limitation, grant limited access, translation rights
type: help
---
# Wie lasse ich einen Übersetzer nur in einer Sprache arbeiten?

Die Inhaltsfunktionen `create`, `edit`, `translate` und `publish` lassen sich über **Sprache** einschränken. Ein
Übersetzer, dessen Bearbeitungs- und Übersetzungsrechte die Einschränkung *Deutsch* tragen, kann jedes Element auf
Deutsch öffnen, seinen deutschen Text anlegen oder ändern, und sonst nichts. Das Lesen wird nie nach Sprache
eingeschränkt.

## Wer das tun kann

Benutzer, deren Rolle `role/*` hat, meist nur Administratoren.

## Schritte

1. Öffnen Sie im oberen Menü **Benutzer** und wählen Sie **Rollen und Richtlinien**.
2. Klicken Sie auf **Neue Rolle** und benennen Sie sie, zum Beispiel „Übersetzer Deutsch“.
3. Klicken Sie auf **Neue Richtlinie**, wählen Sie das Modul **content**, die Funktion **read** und **Vollen Zugriff
   gewähren**.
4. Klicken Sie auf **Neue Richtlinie**, wählen Sie das Modul **content**, die Funktion **edit** und **Eingeschränkten
   Zugriff gewähren**. Wählen Sie unter **Sprache** die Sprache des Übersetzers. Fügen Sie auch Einschränkungen nach
   **Sektion** oder **Teilbaum** hinzu, wenn er nur in einem Teil arbeiten soll.
5. Wiederholen Sie Schritt 4 für die Funktion **translate** und für **publish**, wenn Ihre Website auch das
   Veröffentlichen einschränkt.
6. Klicken Sie auf **Speichern**.
7. Weisen Sie die Rolle mit **Zuweisen** der Benutzergruppe der Übersetzer zu.
8. Melden Sie sich als Test-Übersetzer an und prüfen Sie: Die Sprache lässt sich bearbeiten, die anderen nicht.

## Was schiefgehen kann

- **Der Übersetzer kann keine neue Übersetzung beginnen.** Die Sprache gehört noch nicht zu den Inhaltssprachen der
  Website. Fügen Sie sie zuerst hinzu: siehe [Wie füge ich eine Inhaltssprache hinzu?](wie-fuege-ich-eine-inhaltssprache-hinzu.md)
- **Der Übersetzer kann immer noch andere Sprachen ändern.** Eine andere seiner Rollen gewährt `content/edit` ohne die
  Einschränkung. Rechte summieren sich über alle Rollen des Benutzers und seiner Gruppen.
- **Der Übersetzer sieht nicht, was er übersetzen soll.** Ihm fehlt `content/read` in diesem Teil des Baums oder für
  die Sektion, in der er liegt.
- **Änderungen wirken nicht sofort.** Rechte werden pro Benutzer zwischengespeichert und gelten ab der nächsten
  Anmeldung.

## Verwandte Seiten

- [Rollen und Rechte für Übersetzer](../verwaltung/sprachen/rollen-und-rechte-fuer-uebersetzer.md)
- [Wie gebe ich einer Gruppe Bearbeitungsrechte nur für eine Sektion?](wie-gebe-ich-einer-gruppe-bearbeitungsrechte-fuer-eine-sektion.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
