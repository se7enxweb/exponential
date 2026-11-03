---
title: "Passwörter und Anmeldeprüfungen"
audience: admin, developer, hosting
category: administration
order: 4
summary: exp:user:reset-password sendet den Link zum Zurücksetzen oder setzt ein erzeugtes oder eingeleitetes Passwort, exp:user:require-password-change lässt die nächste Anmeldung nach einem neuen Passwort fragen, und exp:user:check-login sagt einem Skript, ob ein Passwort ein Konto anmeldet - nie mit einem Passwort auf der Befehlszeile.
keywords: Passwort zurücksetzen, Passwort vergessen, Passwort erzeugen, Passwort von der Standardeingabe, Passwort bei nächster Anmeldung ändern, Zustandsprüfung, Zugangsdaten prüfen, exp:user:reset-password, exp:user:require-password-change, exp:user:check-login, reset password, --password-stdin, --send-link, --generate, --require-change, health check
type: help
---
# Passwörter und Anmeldeprüfungen

Kein Befehl nimmt ein Passwort als Argument. Ein Passwort kommt von der Standardeingabe, wird erzeugt und einmal
ausgegeben, oder die Person wählt es über eine Mail.

## Was Sie am Ende haben

Ein vergessenes Passwort ersetzt, ein neues Passwort bei der nächsten Anmeldung verlangt, oder ein Skript, das weiß,
ob das Passwort eines Dienstkontos noch funktioniert.

## Ein Passwort zurücksetzen

```bash
./console exp:user:reset-password jane --send-link --apply
./console exp:user:reset-password jane --generate --require-change --apply
./console exp:user:reset-password ci-bot --password-stdin --apply < /root/secrets/ci-bot-password
```

Wählen Sie genau eines davon:

| Option | Was geschieht |
|---|---|
| `--send-link` | die Mail von **Passwort vergessen** mit ihrem Einmal-Link; das Passwort bleibt, bis der Link benutzt wird, der für `[UserSettings] ForgotPasswordKeyLifetime` gilt |
| `--generate` | ein Passwort, das die Passwortregeln erfüllt, gesetzt und einmal ausgegeben |
| `--password-stdin` | das von der Standardeingabe gelesene Passwort; es muss die Passwortregeln erfüllen |

`--require-change` lässt die Person, zusammen mit `--generate` oder `--password-stdin`, bei der nächsten Anmeldung ein
neues Passwort wählen.

`--generate` und `--password-stdin` beenden die Sitzungen des Kontos, wie es eine Passwortänderung in der
Administration tut. Das Audit hält `access.user.password.change` fest, für den Link
`access.user.password.reset.request`. Das Passwort hält es nie fest. Im Demomodus (`EXP_DEMO_MODE=1`) wird das
Passwort eines Administrators nie geändert.

**Hinweis:** Der Link wird aus dem Siteaccess gebildet, in dem der Befehl läuft. Geben Sie
`--siteaccess=<der öffentliche Siteaccess>` an, wenn der Link auf die öffentliche Website zeigen soll.

## Bei der nächsten Anmeldung ein neues Passwort verlangen

```bash
./console exp:user:require-password-change jane
./console exp:user:require-password-change jane --apply
./console exp:user:require-password-change jane --clear --apply
```

Das Konto wird markiert. Bei der nächsten Anmeldung muss die Person ein neues Passwort wählen, bevor es weitergeht.
`--clear` entfernt die Markierung. Das aktuelle Passwort gilt bis dahin weiter. Das ist das Kästchen
**Passwort bei nächster Anmeldung ändern** der Administration
([Passwort bei nächster Anmeldung ändern](../../funktionen/6.0/passwort-bei-naechster-anmeldung-aendern.md)).

Nutzen Sie es, nachdem Sie ein vorläufiges Passwort übergeben haben, oder wenn jemand anderes ein Passwort gesehen
haben könnte.

## Prüfen, ob ein Passwort ein Konto anmeldet

```bash
printf '%s\n' "$CI_BOT_PASSWORD" | ./console exp:user:check-login ci-bot --password-stdin
```

`exp:user:check-login` liest das Passwort von der Standardeingabe und antwortet, ob es das Konto anmeldet: das
Passwort ist richtig, das Konto ist aktiviert und nicht gesperrt.

| Exit-Code | Bedeutung |
|---|---|
| 0 | ja, es meldet an |
| 1 | nein; die Ausgabe sagt, warum |

Die Prüfung zählt nie als fehlgeschlagene Anmeldung; ein falsches Passwort in einer Zustandsprüfung sperrt das Konto
also nicht. Das Passwort wird nie ausgegeben. Mit `--format=json` geben die Schlüssel `valid` (true oder false) und
`reason` einem Skript die Antwort.

Nutzen Sie es in einem Überwachungsjob oder nach einem Deployment, um früh zu erfahren, dass das Passwort eines
Dienstkontos geändert wurde.

## Verwandte Seiten

- [Einen ausgesperrten Administrator wiederherstellen](einen-ausgesperrten-administrator-wiederherstellen.md)
- [Sitzungen und Anmeldesicherheit](sitzungen-und-anmeldesicherheit.md): ein Konto entsperren, die zweistufige
  Anmeldung zurücksetzen.
- [Ihr Konto und die Anmeldung](../konten-und-anmeldung.md): was die Person sieht.
