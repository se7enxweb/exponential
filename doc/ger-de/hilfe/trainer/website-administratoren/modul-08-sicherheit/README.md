---
title: "Modul 8: Sicherheit"
audience: trainer, admin
category: training
order: 80
summary: Das achte Modul von Website-Administration, mit der Sicht des Angreifers und der Checkliste zur Härtung, Passwörtern und Anmelderichtlinie, der zweistufigen Anmeldung, Sitzungen und Cookies sowie der Content Security Policy. Fünf Lektionen, 3 Stunden 20 Minuten.
keywords: website-administration, modul 8, sicherheit, härtung, passwörter, sperre, zweistufige anmeldung, 2FA, sitzungen, cookies, Content Security Policy, site administration, module 8, security, hardening, passwords, lockout, two-step sign-in, sessions
type: help
---
# Modul 8: Sicherheit

Exponential 6 ist gegen die meisten automatisierten Angriffe von Haus aus sicher; der Administrator sorgt dafür, dass
diese Voreinstellungen in Kraft bleiben, und schließt, was noch offen ist. Dieses Modul beginnt mit der Sicht des
Angreifers und einer Prüfung, die jeder Administrator ausführen kann, und arbeitet dann die Anmeldung
(Passwortrichtlinie, Sperre, zweistufige Anmeldung), Sitzungen und Cookies sowie die Content Security Policy durch,
die eingeschleuste Skripte stoppt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Dauer | 3 Stunden 20 Minuten (fünf Lektionen) |
| Lektionen | 5 |
| Niveau | Fortgeschrittene |
| Format | Praktisch; eine Schulungsinstallation mit Zugang zur Administration und zur Shell pro Person |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [8.1 Denken wie ein Angreifer](wie-ein-angreifer-denken.md) | 40 Minuten | Die Prüfung von außen ausführen, einen Sondierungsversuch in security.log finden, die Checkliste abhaken |
| [8.2 Passwörter und Anmelderichtlinie](passwoerter-und-anmelderichtlinie.md) | 40 Minuten | Eine Passwortrichtlinie und eine Sperre festlegen, ein Testkonto entsperren |
| [8.3 Zweistufige Anmeldung](zweistufige-anmeldung.md) | 45 Minuten | 2FA einschalten, einen Testbenutzer einrichten, sie zurücksetzen |
| [8.4 Sitzungen und Cookies](sitzungen-und-cookies.md) | 35 Minuten | Auf Sitzungen in der Datenbank umstellen, einen Browser abmelden, Cookie-Einstellungen wählen |
| [8.5 Content Security Policy](content-security-policy.md) | 40 Minuten | Den Header lesen, Berichte sammeln, die Richtlinie durchsetzen und zurücknehmen |

## Dieses Modul vorbereiten

- Eine Schulungsinstallation pro Person, über HTTP ausgeliefert (zum Beispiel mit `exp:velocity start`), mit Zugang
  zur Administration und zur Shell. Führen Sie die Prüfung von außen aus Lektion 8.1 nie gegen eine Website aus, die
  Sie nicht selbst betreiben.
- Ein Testbenutzer pro Person mit einer Adresse unter `example.invalid`; der E-Mail-Versand auf `file` gestellt.
- Für Lektion 8.3: Prüfen Sie auf Ihrer eigenen Installation, ob `sevenx_authentication_2fa` aktiv ist und die
  Benutzerklasse das Feld *7x 2FA Configuration* hat; bereiten Sie die Schritte 1 bis 4 vorab vor, wenn die Zeit knapp
  ist. Prüfen Sie, dass die Uhren der Schulungsrechner richtig gehen. Ein Telefon mit einer Authenticator-App hilft;
  E-Mail-Codes in Dateien funktionieren auch ohne.
- Für Lektion 8.5: ein Browser mit Entwicklerwerkzeugen.
- Vor dem Modul eine Kopie von `settings/override/site.ini.append.php` pro Person, damit sich jede Änderung rückgängig
  machen lässt.

## Zeitplan

| Zeit | Teil |
|---|---|
| 0:00 | Lektionen 8.1 und 8.2 (80 Minuten) |
| 1:20 | Pause (15 Minuten) |
| 1:35 | Lektion 8.3 (45 Minuten) |
| 2:20 | Pause (10 Minuten) |
| 2:30 | Lektionen 8.4 und 8.5 (75 Minuten) |
| 3:45 | Ende (3 Stunden 20 Minuten Unterricht plus Pausen) |

## Verwandte Seiten

- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
