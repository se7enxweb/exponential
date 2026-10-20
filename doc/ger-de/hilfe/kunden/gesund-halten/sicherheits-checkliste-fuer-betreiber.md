---
title: Sicherheits-Checkliste für Betreiber
audience: customer, businesses, admin
category: customer-success
order: 9
summary: Eine vierteljährliche Sicherheits-Checkliste für den Betreiber einer Exponential-Website, formuliert als Fragen, die zu stellen sind, und Nachweise, die zu erhalten sind, mit der Seite hinter jedem Punkt.
keywords: Sicherheits-Checkliste, vierteljährliche Überprüfung, Betreiber, Nachweise, Konten, zweistufige Anmeldung, Updates, Prüfprotokoll, Backups, Header, security checklist, quarterly review, owner, evidence, accounts, two-step sign-in, updates, audit trail, backups, headers
type: help
---
# Sicherheits-Checkliste für Betreiber

Diese Checkliste macht aus den technischen Checklisten der [Sicherheitshärtung](../../sicherheitshaertung.md) und der
[Go-live-Checkliste](../../../installation/13-sicherheitshaertung.md) des Installationshandbuchs Fragen, die ein Betreiber
stellen kann, und Nachweise, die ein Betreiber erhalten kann. Gehen Sie sie einmal im Quartal durch, und nach jedem
Wechsel des Anbieters oder des Personals mit Administratorrechten.

## Wie Sie sie verwenden

Schicken Sie die Fragen an die verantwortliche Person (Ihren Hoster, Partner oder Administrator). Bitten Sie um eine
kurze Antwort und, wo „Nachweis“ steht, um einen Screenshot oder die Ausgabe der Prüfung. Bewahren Sie die Antworten
mit dem Datum auf; sie zeigen einem Prüfer, einem Versicherer oder einem Kunden, dass für Sicherheit gesorgt wird.

## Versionen

- [ ] Welche Version von Exponential läuft, und ist es die neueste 6.0.x? Nachweis: **Setup > Systeminformationen**
      ([Release-Linie und Support](release-linie-und-support.md)).
- [ ] Wurden die Sicherheitskorrekturen des neuesten Releases eingespielt, und wann?
- [ ] Erhält die laufende PHP-Version noch Sicherheitsunterstützung? **Setup > Systeminformationen** warnt, wenn
      nicht ([Systeminformationen](../../../anleitungen/systeminformationen.md)).
- [ ] Werden alle Erweiterungen gepflegt? Nachweis: **Setup > Extensions** mit Versionen.

## Konten

- [ ] Hat jede Person ihr eigenes Konto? Ist das gemeinsame Konto `admin` als Anmeldung stillgelegt
      ([Das Administratorpasswort ändern, Kontenpflege](../../administratorpasswort-aendern.md))?
- [ ] Ist die zweistufige Anmeldung für Administratoren verpflichtend ([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md))?
- [ ] Wer hat die Rolle Administrator? Ist die Liste so kurz wie möglich?
- [ ] Wurden die Konten von Personen geschlossen, die gegangen sind? Nachweis: **Benutzer > Benutzerkonten prüfen**
      ([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)).
- [ ] Gibt es einen schriftlichen, getesteten Weg zurück hinein, wenn sich kein Administrator anmelden kann (ein Notfallverfahren)?

## Einstellungen und ausgelieferte Dateien

- [ ] Erscheinen die fünf Sicherheits-Header auf jeder Seite? Nachweis: die `curl`-Prüfung aus
      [Sicherheit und Prüfprotokoll](../../../anleitungen/sicherheit-und-audit.md).
- [ ] Antworten private Dateien (`.env`, `.git/config`, PHP-Dateien von Erweiterungen) von außen mit 404? Dieselbe Seite.
- [ ] Ist die Debug-Ausgabe für alle ausgeschaltet, außer höchstens für eine Adresse mit Ablaufdatum?
- [ ] Läuft die ganze Website über HTTPS?
- [ ] Ist die Administration eingeschränkt, zum Beispiel auf einem eigenen Hostnamen oder hinter einer Netzwerkregel
      ([Die Administration einschränken](../../hosting/sicherheit/die-administration-einschraenken.md))?

## Beobachten

- [ ] Ist das Prüfprotokoll unversehrt? Nachweis: die Ausgabe von `exp:audit status` oder `audit.php verify`
      ([Das Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md)).
- [ ] Wer erhält die Alarm-E-Mails des Prüfprotokolls (Brute-Force-Anmeldungen, eine vergebene Rolle Administrator, ein
      abgeschaltetes Prüfprotokoll)? Ist das eine Person, die sie liest?
- [ ] Werden die Logs mindestens wöchentlich gelesen ([Logs für Betreiber](logs-fuer-betreiber.md))?

## Backups und Wiederherstellung

- [ ] Liegen Backups außerhalb des Servers, vertraulich und in mehreren Generationen ([Backups](backups.md))?
- [ ] Wann war der letzte Wiederherstellungstest, und was war sein Ergebnis ([Wiederherstellungstest](wiederherstellungstest.md))?

## Menschen und Abläufe

- [ ] Weiß das Team, dass ein Sicherheitsproblem vertraulich an security@se7enx.com gemeldet wird, nicht öffentlich
      ([Vertrauliche Sicherheitsmeldungen](vertrauliche-sicherheitsmeldungen.md))?
- [ ] Gibt es beim Hoster einen benannten Kontakt für einen vermuteten Einbruch, der auch außerhalb der Bürozeiten
      erreichbar ist ([Vorlage für Eskalationskontakte](../partner-und-hoster/vorlage-eskalationskontakte.md))?
- [ ] Ist vereinbart, wer die Sicherheitsmitteilungen des Projekts erhält und wer sie umsetzt?

## Ein Protokoll zum Aufbewahren

```text
Security review    date ____    done by ____
Version ____  newest 6.0.x ____  PHP ____ (supported until ____)
Administrators: ____ accounts, two-step sign-in: yes / no
Headers and private files checked: yes / no      Debug output off: yes / no
Audit trail intact: yes / no      Alert recipients: ____
Last restore test: ____           Result: ____
Open actions: ____ (who, by when)
```

## Verwandte Seiten

- [Sicherheitsgrundlagen](sicherheitsgrundlagen.md)
- [Sicherheitsüberprüfung (für Hoster)](../../hosting/checklisten/checkliste-fuer-die-sicherheitspruefung.md)
- [Zugriffsüberprüfungen](../../../../eng-us/help/businesses/security/access-reviews.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
