---
title: "Registrierungen freigeben"
audience: admin, developer, hosting
category: administration
order: 5
summary: exp:user:pending listet die Registrierungen auf, die auf die E-Mail-Adresse, einen Administrator oder einen Freigabeeintrag warten; exp:user:approve gibt eine frei oder lehnt sie ab, und exp:user:resend-activation sendet die Aktivierungsmail mit einem neuen Schlüssel erneut.
keywords: wartende Registrierung, ausstehende Registrierung, nicht aktivierte Benutzer, Konto freigeben, Konto ablehnen, Aktivierungsmail, Aktivierung erneut senden, Freigabe der Registrierung, Freigabeeintrag, Workflow, Spam-Konten, exp:user:pending, exp:user:approve, exp:user:resend-activation, pending registration, approve account
type: help
---
# Registrierungen freigeben

Eine **wartende Registrierung** ist ein Konto, dessen Registrierung nicht abgeschlossen ist. Es kann sich erst
anmelden, wenn sie abgeschlossen ist.

## Was Sie am Ende haben

Jede wartende Registrierung freigegeben, abgelehnt oder erinnert.

## Sehen, was wartet

```bash
./console exp:user:pending
./console exp:user:pending --search=example.org --limit=100
```

Die Spalte State sagt, worauf jede Registrierung wartet:

| Zustand | Wartet auf |
|---|---|
| `email` | die Person, die ihre E-Mail-Adresse bestätigt (das Konto hat einen Aktivierungsschlüssel) |
| `administrator` | einen Administrator, der das Konto aktiviert |
| `workflow` | einen Freigebenden, der den Freigabeeintrag zur Veröffentlichung des Kontos annimmt |

Die Liste beginnt mit der neuesten. `exp:user:pending` ist dasselbe wie `exp:user:list --state=pending`.

## Eine freigeben oder ablehnen

```bash
./console exp:user:approve member1
./console exp:user:approve member1 --apply
./console exp:user:approve spam-account --reject --apply
```

Die Freigabe aktiviert das Konto wie **Benutzer > Nicht aktivierte Benutzer**: die Registrierung wird abgeschlossen,
das Konto veröffentlicht und die Willkommensmail gesendet. Bei `workflow` wird der Freigabeeintrag angenommen und
sein Workflow sofort fortgesetzt.

`--reject` verweigert den Freigabeeintrag und entfernt das Konto endgültig, wie es der Ablehnungslink der
Registrierungsmail tut. Ein Konto, das schon aktiv ist, entfernt `--reject` nie.

Das Audit hält `access.registration.approve` oder `access.registration.reject` fest.

## Die Aktivierungsmail erneut senden

```bash
./console exp:user:resend-activation member1
./console exp:user:resend-activation member1 --apply
```

Der Befehl sendet die Aktivierungsmail erneut, mit einem neuen Aktivierungsschlüssel, an ein Konto, das nie aktiviert
wurde. Der alte Link funktioniert nicht mehr. Nutzen Sie es, wenn die erste Mail verloren ging oder ihr Link
abgelaufen ist.

Um das Konto ohne die Person zu aktivieren, nehmen Sie stattdessen `exp:user:approve`.

## Verwandte Seiten

- [Einen Rückstau von Registrierungen freigeben](einen-rueckstau-von-registrierungen-freigeben.md): viele auf einmal.
- [Freigabe von Benutzerkonten](../../bc/6.0/freigabe-von-benutzerkonten.md): wie eine Installation neue Konten
  zurückhält.
- [Neue Redakteurskonten freigeben](../../anleitungen/neue-redakteurskonten-freigeben.md): dasselbe in der
  Administration.
