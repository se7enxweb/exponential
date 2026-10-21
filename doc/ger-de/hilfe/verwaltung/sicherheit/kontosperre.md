---
title: Kontosperre
audience: government, admin
category: public-sector
order: 190
summary: Für Administratorinnen und Administratoren öffentlicher Websites - die Kontosperre von Exponential nach fehlgeschlagenen Anmeldungen ist standardmäßig aus; wie Sie sie einschalten, wie Sie Ihr eigenes Netzwerk davon ausnehmen, die Abwägung gegen absichtlich herbeigeführte Sperren und wie Sie Konten entsperren.
keywords: Kontosperre, fehlgeschlagene Anmeldungen, Brute-Force, Passwort erraten, entsperren, Benutzerkonten prüfen, zweistufige Anmeldung, öffentlicher Sektor, account lockout, failed logins, MaxNumberOfFailedLogin, TrustedIPList, ShowMessageIfExceeded, brute force, credential stuffing, unlock, reset failed sign-ins, Check user accounts, two-step sign-in, public sector
type: help
---
# Kontosperre

Diese Seite richtet sich an Administratorinnen und Administratoren. Exponential kann ein Konto nach einer bestimmten
Zahl fehlgeschlagener Anmeldungen sperren, aber **die Sperre ist standardmäßig aus**
(`settings/site.ini [UserSettings] MaxNumberOfFailedLogin=0`). Brute-Force-Versuche erkennen die Alarmregeln des Audits
in jedem Fall; der Kernel selbst bremst Anmeldungen nicht
([Sicherheitsstandards: bekannte Lücken](../../../standards/sicherheit.md#bekannte-lücken)).

## Inhalt

1. [Die Abwägung](#die-abwägung)
2. [Einschalten](#einschalten)
3. [Konten entsperren](#konten-entsperren)
4. [Alternativen und Ergänzungen](#alternativen-und-ergänzungen)
5. [Verwandte Seiten](#verwandte-seiten)

## Die Abwägung

| Mit Sperre | Ohne Sperre |
|---|---|
| das Erraten des Passworts eines Kontos endet nach N Versuchen | das Raten kann weitergehen, nur vom Webserver oder von request-shield gebremst |
| wer einen Anmeldenamen kennt, kann dieses Konto absichtlich sperren | keine absichtlich herbeigeführten Sperren |
| gesperrte Beschäftigte brauchen eine Administratorin oder einen Administrator | keine Supportanrufe |

Öffentliche Stellen veröffentlichen oft die Namen ihrer Beschäftigten, wodurch Anmeldenamen erratbar werden; wägen Sie
beide Seiten ab und halten Sie die Entscheidung schriftlich fest.

## Einschalten

```ini
# settings/override/site.ini.append.php
[UserSettings]
MaxNumberOfFailedLogin=10
TrustedIPList[]
TrustedIPList[]=203.0.113.0/24
ShowMessageIfExceeded=false
```

- `MaxNumberOfFailedLogin`: fehlgeschlagene Versuche, nach denen das Konto gesperrt bleibt, bis eine Administratorin
  oder ein Administrator es entsperrt.
- `TrustedIPList[]`: Adressen, die nie gesperrt werden, zum Beispiel Ihr Büronetz, damit ein Angriff von außen die
  Beschäftigten nicht aussperren kann, während sie im Büro sind.
- `ShowMessageIfExceeded`: ob dem Benutzer mitgeteilt wird, dass das Konto gesperrt ist.

Hinter einem Proxy konfigurieren Sie zuerst `TrustedProxies[]`, sonst hat jeder Besucher die Adresse des Proxys
([13.10](../../../installation/13-sicherheitshaertung.md#1310-hinter-einem-proxy-vertrauenswürdige-header)). Falsche Codes der
zweistufigen Anmeldung zählen ebenfalls als fehlgeschlagene Anmeldungen. Leeren Sie nach der Änderung den INI-Cache und
laden Sie PHP neu.

## Konten entsperren

- **Ein Konto**: Öffnen Sie seine Kontoseite (`/user/edit/<id>`); das Kennzeichen zeigt **Gesperrt**;
  **Kontoeinstellungen** setzt die fehlgeschlagenen Anmeldungen zurück
  ([Sicherheit und Audit, 6](../../../anleitungen/sicherheit-und-audit.md#6-ein-konto-auf-seiner-kontoseite-prüfen-2-minuten)).
- **Mehrere**: **Benutzer > Benutzerkonten prüfen** (`/user/hygiene`), Filter **Gesperrt**, ankreuzen, **Entsperren**,
  **Bestätigen**. Jede Entsperrung wird als `access.user.unlock` aufgezeichnet
  ([Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md)).
- Sehen Sie sich vor dem Entsperren die fehlgeschlagenen Anmeldungen in der Audit-Konsole an:
  `/audit/console/(name)/access.session.login.failed`. Viele Fehlschläge aus unbekannten Netzen deuten auf einen
  Angriff hin: Ändern Sie dann auch das Passwort.

## Alternativen und Ergänzungen

- **Zweistufige Anmeldung** macht ein erratenes Passwort allein wertlos (siehe „Zweistufige Anmeldung für Beschäftigte
  öffentlicher Stellen“).
- **request-shield** bremst Anmeldefluten und schaltet vor das Anmeldeformular eine Browserprüfung.
- **Alarmregeln** `brute_force` (20 Fehlschläge aus einem Netz in 5 Minuten) und `brute_force_user` (10 für ein Konto in
  15 Minuten) melden Angriffe an die Empfänger der Alarme.
- **Die Administration auf Ihr Netz zu beschränken** entzieht dem Raten aus dem Internet den größten Teil der
  Angriffsfläche.

## Verwandte Seiten

- [13.6 Passwörter und Anmeldung: fehlgeschlagene Anmeldungen](../../../installation/13-sicherheitshaertung.md#fehlgeschlagene-anmeldungen)
- [Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md)
- [Audit-Alarme und ihre Empfänger](audit-alarme-und-empfaenger.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
