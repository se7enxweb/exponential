---
title: Wie richte ich einen Bereich nur für Mitglieder ein?
audience: admin
category: manual
order: 605
summary: Legen Sie die Seiten in eine eigene Sektion, die Besucher nicht lesen dürfen, und geben Sie der Benutzergruppe der Mitglieder eine Rolle mit content/read, beschränkt auf diese Sektion.
keywords: nur für Mitglieder, Mitgliederbereich, geschützter Bereich, private Seiten, Intranet, Sektion, Rolle, Benutzergruppe, Mitglieder, Anmeldung, Registrierung, content/read, members-only, protected area, private pages, intranet, section, role, user group, Members, login, registration
context: role/view
type: help
---
# Wie richte ich einen Bereich nur für Mitglieder ein?

Besucher, die nicht angemeldet sind, dürfen nur lesen, was die Rolle Anonymous erlaubt; auf einer Standard-Website ist
das die Sektion Standard. Seiten in einer anderen Sektion sind ihnen daher verschlossen. Mitglieder sehen die Seiten,
weil eine ihrer Rollen das Lesen dieser Sektion erlaubt.

## Wer das tun kann

Ein Administrator oder ein Benutzer, dessen Rollen `section/edit`, `section/assign` und `role/*` geben.

## Schritte

1. Legen Sie eine Sektion an, zum Beispiel „Mitgliederbereich“, und ordnen Sie ihr den Ordner mit den Seiten der
   Mitglieder zu, wie in
   [Wie lege ich eine Sektion an und ordne ihr Inhalte zu?](wie-lege-ich-eine-sektion-an-und-ordne-ihr-inhalte-zu.md) beschrieben.
2. Melden Sie sich ab oder öffnen Sie die Website in einem privaten Browserfenster, und öffnen Sie eine der Seiten.
   Die Seite wird verweigert und ist aus Menüs und Listen verschwunden.
3. Öffnen Sie in der Administration im oberen Menü **Benutzer** und wählen Sie **Rollen und Richtlinien**.
4. Klicken Sie auf **Neue Rolle** und benennen Sie sie, zum Beispiel „Leser Mitgliederbereich“.
5. Klicken Sie auf **Neue Richtlinie**, wählen Sie das Modul **content**, dann **Zugriff auf eine Funktion gewähren**,
   die Funktion **read** und **Eingeschränkten Zugriff gewähren**.
6. Wählen Sie unter **Sektion** „Mitgliederbereich“ und klicken Sie auf **OK**.
7. Klicken Sie auf **Speichern**.
8. Klicken Sie auf der Seite der Rolle auf **Zuweisen** und wählen Sie die Benutzergruppe Ihrer Mitglieder.
9. Melden Sie sich auf der Website als Test-Mitglied an und öffnen Sie die Seite erneut.
   Die Seite wird angezeigt.

## Was schiefgehen kann

- **Mitglieder können sich überhaupt nicht anmelden.** Die Anmeldung an einem Siteaccess erfordert in einer der Rollen
  des Benutzers die Richtlinie `user/login` für diesen Siteaccess. Auf einer Standard-Website hat die Rolle, mit der
  sich Besucher anmelden dürfen, sie bereits; einer Gruppe, die Sie selbst angelegt haben, kann sie fehlen. Prüfen Sie
  die Tabs **Rollen** und **Richtlinien** des Mitglieds
  ([Wie sehe ich, was ein Benutzer tun darf?](wie-sehe-ich-was-ein-benutzer-tun-darf.md)).
- **Bilder auf den Seiten der Mitglieder sind defekt.** Bilder und Dateien liegen meist in der Medienbibliothek, in der
  Sektion Media. Legen Sie sie ebenfalls in die Sektion der Mitglieder, sind sie auch für Besucher verschlossen, was
  oft gewollt ist.
- **Besucher sehen die Seiten immer noch.** Eine zwischengespeicherte Seite von vor der Änderung kann kurze Zeit
  ausgeliefert werden, und die Rolle Anonymous hat womöglich eine Richtlinie `content/read` ohne
  Sektionseinschränkung. Prüfen Sie zuerst die Rolle Anonymous und leeren Sie dann den Inhalts-Cache
  ([Welchen Cache leere ich, und wie?](welchen-cache-leere-ich.md)).
- **Ein Mitglied sieht die Seite erst nach erneuter Anmeldung.** Rechte werden pro Benutzer zwischengespeichert und bei
  der nächsten Anmeldung neu gelesen.
- **Neue Mitglieder landen in der falschen Gruppe.** Wer sich auf der Website registriert, wird in der Gruppe
  angelegt, die `site.ini [UserSettings] DefaultUserPlacement` nennt (eine Knoten-ID). Weisen Sie die Rolle dieser
  Gruppe zu, oder ändern Sie die Einstellung.

**Datenschutz:** Wenn Sie Seiten in einer Sektion verbergen, bleiben sie Besuchern und Suchmaschinen verborgen, aber
jedes Mitglied der Gruppe kann sie lesen und kopieren. Legen Sie dort keine Daten ab, die nur einige Mitglieder sehen
dürfen; geben Sie solchen Seiten eine eigene Sektion und eine eigene Rolle.

## Verwandte Seiten

- [Sektionen](../../anleitungen/sektionen.md): Abschnitt 13.1 beschreibt dieselbe Einrichtung mit den Sektionsseiten
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)
- [Wie lege ich einen Benutzer oder eine Benutzergruppe an?](wie-lege-ich-einen-benutzer-oder-eine-benutzergruppe-an.md)
- [Benutzer und Registrierung](../../../eng-us/help/cms-developers/users-and-registration.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
