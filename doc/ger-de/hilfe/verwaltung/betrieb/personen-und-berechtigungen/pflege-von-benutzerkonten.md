---
title: Pflege der Benutzerkonten
audience: government, admin
category: public-sector
order: 120
summary: Konten von Beschäftigten und Besuchern mit Benutzer > Benutzerkonten prüfen in Ordnung halten - nie genutzte, seit Monaten nicht gesehene, deaktivierte, gesperrte, verwaiste Konten und Konten, die einen Anmeldenamen oder eine Adresse teilen - und die Sammelaktionen, Sperreinstellungen und die Prüfung der Anmeldenamen dahinter.
keywords: Kontenpflege, Benutzerkonten prüfen, ruhende Konten, inaktive Konten, gesperrte Konten, verwaiste Konten, gemeinsame E-Mail, doppelter Anmeldename, Kontosperre, MaxNumberOfFailedLogin, nicht aktivierte Benutzer, exp:user-login-check, user hygiene, Check user accounts, dormant accounts, inactive accounts, locked accounts, orphaned accounts, shared e-mail, duplicate login, account lockout, unactivated users
type: help
---
# Pflege der Benutzerkonten

Diese Seite richtet sich an Administratoren, die die Konten einer öffentlichen Website in Ordnung halten, meist im Rahmen einer
Sicherheitsrichtlinie oder einer Prüfungsfeststellung zu ruhenden Konten. Das wichtigste Werkzeug ist **Benutzer > Benutzerkonten prüfen**
(`/user/hygiene`), vollständig beschrieben unter [Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md).

## Inhalt
1. [Worauf Sie achten sollten](#worauf-sie-achten-sollten)
2. [Die Sammelaktionen](#die-sammelaktionen)
3. [Kontosperre](#kontosperre)
4. [Ein Konto je Anmeldename](#ein-konto-je-anmeldename)
5. [Nicht aktivierte Registrierungen](#nicht-aktivierte-registrierungen)
6. [Eine monatliche Routine](#eine-monatliche-routine)
7. [Verwandte Seiten](#verwandte-seiten)

## Worauf Sie achten sollten

Die Seite listet jedes Konto, oder die unterhalb einer Benutzergruppe, mit einem Filter:

| Filter | Findet | Typische Maßnahme |
|---|---|---|
| Nie angemeldet | angelegte, aber nie genutzte Konten | die Inhaberin oder den Inhaber fragen; deaktivieren |
| Seit N Tagen nicht gesehen | einmal angemeldet, seitdem untätig (standardmäßig 180 Tage) | deaktivieren; den Fachbereich fragen |
| Deaktiviert | Konten, die sich nicht anmelden können | prüfen, ob es Ausgetretene sind; stilllegen |
| Gesperrt | mehr fehlgeschlagene Anmeldungen als erlaubt | nach Rücksprache mit der Person entsperren |
| Verwaist | eine Kontozeile, deren Benutzerobjekt fehlt, nicht veröffentlicht ist oder keine Platzierung hat | vor allem anderen untersuchen |
| Doppelter Benutzername | Anmeldenamen, die mehr als ein Konto hat | bereinigen, dann den eindeutigen Index hinzufügen |
| Doppelte E-Mail-Adresse | eine Adresse an mehreren Konten (braucht `role/assign`) | auf gemeinsam genutzte Teamkonten prüfen |

Jede Zeile zeigt den letzten Besuch, die Zahl der Anmeldungen, Gruppen und Rollen, eigene und geerbte. Die Seite braucht `user/hygiene`;
vergeben Sie es nur an Kontenadministratoren, weil die Seite Name, Anmeldenamen, Zustand, Gruppen und Rollen jedes Kontos zeigt
([Wer sie nutzen darf](../../../../../eng-us/features/6.0/user-hygiene.md#who-may-use-it)). Sie braucht eine SQL-Datenbank; unter MongoDB listet sie nichts.

## Die Sammelaktionen

**Deaktivieren**, **Aktivieren** und **Entsperren** wirken auf markierte Konten, nach einer Bestätigung, die sagt, wie viele sich ändern werden
und warum andere ausgelassen werden. Der anonyme Benutzer, der Benutzer, der Konten anlegt, das Administratorkonto einer frischen Installation,
das Konto `admin` und Sie selbst werden nie geändert. Jede Änderung ist ein Audit-Ereignis (`access.user.disable`, `access.user.enable`,
`access.user.unlock`). Nichts hier entfernt ein Konto. Mit `DeactivateAndMove=enabled` legt **Deaktivieren und verschieben** Konten umkehrbar still.

**Liste als CSV exportieren** schreibt die gefilterte Liste (UTF-8 mit Byte-Order-Mark); der Export wird als `data.export.csv` mit der
Zeilenzahl erfasst, nie mit den Werten. Zellen, die wie Formeln aussehen, erhalten ein Präfix, damit eine Tabellenkalkulation sie als Text anzeigt.

## Kontosperre

Die Sperre ist standardmäßig **aus**: `settings/site.ini [UserSettings] MaxNumberOfFailedLogin=0`
([Sicherheitsstandard](../../../../../eng-us/standards/security.md)). Brute-Force-Versuche werden unabhängig davon von den Audit-Alarmen `brute_force`
und `brute_force_user` gemeldet. Um Konten nach wiederholten Fehlversuchen zu sperren, setzen Sie eine Zahl in
`settings/override/site.ini.append.php`; erwägen Sie `TrustedIPList[]` für Büronetze, wie der Standard anmerkt
([Die Kontosperre einschalten](../../../../../eng-us/help/government/tasks/people-and-permissions/switch-on-account-lockout.md)). Gesperrte Konten erscheinen dann unter dem
Filter **Gesperrt**.

## Ein Konto je Anmeldename

Zwei Konten mit demselben Anmeldenamen (oder mit Anmeldenamen, die sich nur in der Groß- und Kleinschreibung unterscheiden) können sich nicht
beide zuverlässig anmelden. Prüfen und beheben:

```bash
./console exp:user-login-check --allow-root-user
./console exp:user-login-check --fix-index --allow-root-user
```

Der erste Befehl listet Duplikate (Anmeldenamen gekürzt, außer mit `--show-logins`); der zweite fügt einen eindeutigen Index hinzu, sobald es
keine mehr gibt ([Sicherheit und Audit, Schritt 7](../../../../anleitungen/sicherheit-und-audit.md#7-ein-konto-pro-login-2-minuten)).

## Nicht aktivierte Registrierungen

Wenn sich Besucher registrieren können, listet **Benutzer > Nicht aktivierte Benutzer** nie aktivierte Registrierungen; eine Registrierung, die
älter als 30 Tage ist, wird als **Alt** markiert, und **Alle unaktivierten Benutzer entfernen** räumt sie auf, erfasst als `access.user.remove`
([Leitfaden zu Rollen, Abschnitt 7](../../../../anleitungen/rollen-und-richtlinien.md#7-nicht-aktivierte-benutzer-aufräumen)). Auf einer neuen Installation braucht
die offene Registrierung die Richtlinie `user/register`; eine aktualisierte Website hält sie offen, bis sie geschlossen wird
([Die offene Registrierung schließen](../../../../../eng-us/help/government/tasks/people-and-permissions/close-open-registration.md)).

## Eine monatliche Routine

1. **Benutzerkonten prüfen**, Filter **Seit 180 Tagen nicht gesehen** (oder der Zeitraum Ihrer Richtlinie); senden Sie die Liste an die
   Fachbereiche; deaktivieren Sie, was niemand beansprucht.
2. Filtern Sie nach **Gesperrt** und **Verwaist**; handeln Sie bei jedem Eintrag.
3. Filtern Sie nach **Doppelte E-Mail-Adresse**; ersetzen Sie gemeinsam genutzte Konten durch persönliche.
4. Führen Sie `./console exp:user-login-check --allow-root-user` aus.
5. Bewahren Sie den CSV-Export bei Ihrem Überprüfungsnachweis auf ([Überprüfung der Administrationszugänge](ueberpruefung-administrativer-zugaenge.md)).

## Verwandte Seiten
- [Konten finden, die seit Monaten nicht genutzt werden](../../../../../eng-us/help/government/tasks/people-and-permissions/find-accounts-not-used-for-months.md)
- [Ein gesperrtes Konto entsperren](../../../../../eng-us/help/government/tasks/people-and-permissions/unlock-a-locked-account.md)
- [Die Kontenliste für eine Überprüfung exportieren](../../../../../eng-us/help/government/tasks/people-and-permissions/export-the-account-list-for-a-review.md)
- [Eintritt, Wechsel und Austritt](eintritt-wechsel-austritt.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
