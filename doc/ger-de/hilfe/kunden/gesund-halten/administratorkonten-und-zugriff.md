---
title: Administratorkonten und Zugriff
audience: customer, admin, businesses
category: customer-success
order: 10
summary: Die Regeln des Betreibers für Administratorkonten auf einer Exponential-Website - ein Konto pro Person, wenige Administratoren, zweistufige Anmeldung, das gemeinsame Admin-Konto stilllegen, ein getesteter Weg zurück hinein und was zu tun ist, wenn sich Personal oder Anbieter ändern.
keywords: Administrator, Konten, Zugriff, Rollen, Richtlinien, zweistufige Anmeldung, Admin-Konto, Notfallzugang, Ausscheiden, Zugriff von Anbietern, administrator, accounts, access, roles, policies, two-step sign-in, admin account, break-glass, offboarding, provider access
type: help
---
# Administratorkonten und Zugriff

Ein Administrator kann alles auf der Website ändern. Diese Seite legt die Regeln dar, auf denen ein Betreiber für
diese Konten bestehen sollte, und was zu tun ist, wenn sich Personen oder Anbieter ändern. Rollen für Redakteure und
anderes Personal sind ein eigenes Thema: siehe [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md).

## Die Regeln

| Regel | Warum | Quelle |
|---|---|---|
| Ein Konto pro Person | Damit das Prüfprotokoll sagt, wer was getan hat | [Das Administratorpasswort ändern, Kontenpflege](../../administratorpasswort-aendern.md) |
| Wenige Administratoren | Die Vergabe der Rolle Administrator löst einen Alarm im Prüfprotokoll aus; jeder zusätzliche ist ein Risiko | [Sicherheit und Prüfprotokoll](../../../anleitungen/sicherheit-und-audit.md) |
| Die Rolle Administrator nie für einen Kollegen erweitern | stattdessen „eine neue Rolle anlegen“ | [Sicherheit und Prüfprotokoll](../../../anleitungen/sicherheit-und-audit.md) |
| Zweistufige Anmeldung für Administratoren | Das Passwort allein nützt dann niemand anderem etwas | [Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md) |
| `admin` als Anmeldung stilllegen | „sein Benutzername ist der erste, den ein Angreifer ausprobiert“ | [Das Administratorpasswort ändern](../../administratorpasswort-aendern.md) |
| Ein getesteter Weg zurück hinein | Damit ein verlorenes Passwort keinen Ausfall bedeutet | Dieselbe Seite, „Notfallverfahren“ |

## Das gemeinsame Admin-Konto stilllegen

Jede Installation hat ein Konto namens `admin`. Der Leitfaden zur Kontenpflege nennt zwei unterstützte Wege, seine
Nutzung zu beenden: es deaktivieren, nachdem die Einstellung `[UserSettings] UserCreatorID` auf ein anderes aktives
Administratorkonto zeigt, oder es mit einem langen Zufallspasswort behalten, das niemand aufbewahrt
([Das Administratorpasswort ändern](../../administratorpasswort-aendern.md)). Ihr Administrator oder Partner wählt den Weg;
Ihr Teil ist, darum zu bitten, dass es geschieht, und eine Bestätigung zu erhalten.

## Das Notfallverfahren

„Halten Sie ein Notfallverfahren bereit, schriftlich festgehalten und getestet“, und „bewahren Sie das Verfahren
(nicht ein Passwort) dort auf, wo die Personen in Bereitschaft es finden, und testen Sie es einmal im Jahr auf einer
Kopie der Website“ ([Das Administratorpasswort ändern](../../administratorpasswort-aendern.md)). Fragen Sie, wer es
durchführen kann (meist der Hoster, mit Shell-Zugriff auf den Server), und nehmen Sie diese Person in Ihre
[Eskalationskontakte](../partner-und-hoster/vorlage-eskalationskontakte.md) auf.

## Konten von Anbietern

Ihr Hoster und Ihr Partner haben möglicherweise Administratorkonten auf Ihrer Website. Vereinbaren Sie:

- [ ] Jede Person beim Anbieter hat ein namentliches Konto, kein gemeinsames.
- [ ] Ihre Konten verwenden die zweistufige Anmeldung.
- [ ] Sie erhalten die Liste ihrer Konten und werden informiert, wenn sie sich ändert.
- [ ] Wenn der Vertrag endet, werden ihre Konten deaktiviert; die Checkliste für das Ausscheiden von Kunden für Hoster
      sagt, „dem Kunden mitzuteilen, welche Konten die eigenen waren“ ([Ausscheiden eines Kunden](../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)).
- [ ] Hosting-Konten, die Domain und der Administratorzugang gehören Ihnen oder werden auf Anfrage übergeben
      ([Grundlagen zu Verträgen und SLAs](../../unternehmen/auswahl/grundlagen-zu-vertraegen-und-slas.md)).

## Wenn Personal kommt oder geht

| Ereignis | Tun Sie |
|---|---|
| Jemand kommt dazu | Ein persönliches Konto in der richtigen Gruppe, mit einer Rolle, die zur Aufgabe passt ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)) |
| Jemand wechselt die Aufgabe | Gruppe oder Rolle ändern; keine Rechte zusätzlich zu alten hinzufügen |
| Jemand geht | Das Konto am letzten Tag deaktivieren; offene Entwürfe prüfen |
| Ein Administrator geht | Zusätzlich prüfen, wer sonst die Rolle Administrator hat und ob das Notfallverfahren die Person nannte |

## Die vierteljährliche Überprüfung

**Benutzer > Benutzerkonten prüfen** listet Konten, die sich nie angemeldet haben, seit Monaten nicht gesehen wurden,
deaktiviert oder gesperrt sind oder sich einen Benutzernamen oder eine E-Mail-Adresse teilen
([Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md)). Bitten Sie Ihren Administrator, die Liste jedes
Quartal durchzugehen, zusammen mit den Zuweisungen der Rolle Administrator, und Ihnen die vorgenommenen Änderungen zu
schicken. Eine jährliche, gründlichere Fassung mit einem Protokoll zum Aufbewahren steht unter
[Zugriffsüberprüfungen](../../unternehmen/sicherheit/zugriffspruefungen.md).

> **Tipp**
> Um zu sehen, was sich bei Zugriffen geändert hat: Das Prüfprotokoll hält Rollenzuweisungen fest und schlägt bei einer
> neuen Zuweisung der Rolle Administrator Alarm ([Das Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md)).

## Verwandte Seiten

- [Sicherheitsgrundlagen](sicherheitsgrundlagen.md)
- [Benutzerkonten und Passwörter](../../unternehmen/sicherheit/benutzerkonten-und-passwoerter.md)
- [Konten und Anmeldung](../../konten-und-anmeldung.md)
- [Konten für Redakteure und Administratoren einrichten](../../konten-fuer-redakteure-und-administratoren-einrichten.md)
- [Das Benutzer-Auditprotokoll](../../das-benutzer-auditprotokoll.md)
- [Das Standard-Administratorpasswort](../../standard-administratorpasswort.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
