---
title: "Sitzungen und Anmeldesicherheit"
audience: admin, developer, hosting
category: administration
order: 7
summary: exp:user:sessions listet und beendet die angemeldeten Sitzungen eines Kontos, exp:user:unlock setzt den Zähler fehlgeschlagener Anmeldungen eines gesperrten Kontos zurück, und exp:user:2fa zeigt, setzt zurück oder entfernt die zweistufige Anmeldung und Passkeys.
keywords: Sitzungen, Ihre Geräte, überall abmelden, Sitzung beenden, gesperrtes Konto, fehlgeschlagene Anmeldungen, entsperren, zweistufige Anmeldung, Authenticator-App, Telefon verloren, Passkeys entfernen, exp:user:sessions, exp:user:unlock, exp:user:2fa, sessions, unlock, two-step sign-in, 2FA, MaxNumberOfFailedLogin, sevenx_authentication_2fa
type: help
---
# Sitzungen und Anmeldesicherheit

Diese Befehle helfen, wenn ein Konto übernommen worden sein könnte, wenn jemand das Passwort zu oft falsch eingegeben
hat oder wenn jemand das Telefon mit der Authenticator-App verloren hat.

## Was Sie am Ende haben

Ein Konto, das überall abgemeldet, entsperrt oder dessen zweiter Schritt zurückgesetzt ist.

## Sitzungen

```bash
./console exp:user:sessions jane
./console exp:user:sessions jane --end --apply
./console exp:user:sessions jane --session=8f3a21 --apply
```

Ohne Optionen listet der Befehl die aktiven erfassten Sitzungen des Kontos auf, wie **Ihre Geräte** sie zeigt: die
ID, die Anmeldemethode, das Gerät, den Browser, die ungefähre Adresse und die letzte Aktivität.

- `--end` beendet alle.
- `--session=<id>` beendet eine.

Das Audit hält `access.session.revoke` fest. Sitzungen werden nur erfasst, wo **Ihre Geräte** eingeschaltet ist
([Ihre Geräte](../../funktionen/6.0/benutzersitzungen.md)).

**Hinweis:** Das Beenden von Sitzungen hindert die Person nicht daran, sich wieder anzumelden. Wenn ein Konto
übernommen worden sein könnte, setzen Sie auch das Passwort zurück oder deaktivieren Sie das Konto.

## Ein Konto entsperren

```bash
./console exp:user:unlock jane
./console exp:user:unlock jane --apply
```

Ein Konto wird nach mehr falschen Passwörtern als `[UserSettings] MaxNumberOfFailedLogin` gesperrt. Der Befehl setzt
den Zähler fehlgeschlagener Anmeldungen zurück, wie es **Fehlgeschlagene Anmeldungen zurücksetzen** in der
Administration tut. Das Passwort bleibt, wie es ist. Das Audit hält `access.user.unlock` fest.

`exp:user:list --state=locked` listet jedes gesperrte Konto auf.

## Zweistufige Anmeldung und Passkeys

```bash
./console exp:user:2fa jane
./console exp:user:2fa jane --reset --apply
./console exp:user:2fa jane --remove-passkeys --apply
```

Ohne Optionen zeigt der Befehl den Zustand der zweistufigen Anmeldung (die Methode, und ob sie aktiv ist) und wie
viele Passkeys das Konto hat.

- `--reset` entfernt den zweiten Schritt, für jemanden, der das Telefon verloren hat. Die fehlgeschlagenen
  Anmeldungen werden ebenfalls zurückgesetzt. Wo die zweistufige Anmeldung Pflicht ist, fragt die nächste Anmeldung
  nach einer neuen Einrichtung. Es braucht die Extension `sevenx_authentication_2fa`.
- `--remove-passkeys` entfernt alle Passkeys des Kontos.

Das Audit hält `access.user.two_factor.remove` und die Ereignisse `access.passkey.*` fest.

**Achtung:** Setzen Sie den zweiten Schritt nur für die Person zurück, der er gehört. Bestätigen Sie, wer fragt, auf
einem Weg, den Sie schon kennen, nicht auf dem Weg, auf dem die Anfrage kam.

## Verwandte Seiten

- [Die zweistufige Anmeldung nach einem verlorenen Telefon zurücksetzen](zweistufige-anmeldung-nach-verlorenem-telefon-zuruecksetzen.md)
- [Einen ausgesperrten Administrator wiederherstellen](einen-ausgesperrten-administrator-wiederherstellen.md)
- [Ihre Geräte und Sitzungen](../../anleitungen/ihre-geraete-und-sitzungen.md): was die Person sieht.
- [Ihr Konto und die Anmeldung](../konten-und-anmeldung.md#zweistufige-anmeldung): die zweistufige Anmeldung für die
  Person.
