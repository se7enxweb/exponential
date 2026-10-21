---
title: FAQ zum Demo-Modus
audience: admin, hosting, developer, press, advocate, trainer, partner, sales-marketing
category: administration
order: 34
summary: Kurze Antworten zum Demo-Modus (EXP_DEMO_MODE=1) - wofür er da ist, was er sperrt und was nicht, wie Sie ihn ein- und ausschalten, warum er nicht aus einer Einstellung kommen kann, welche Passwörter die Installer akzeptieren, woran Sie erkennen, dass er eingeschaltet ist, und warum er nie eine echte Site erreichen darf.
keywords: Demo-Modus, FAQ, Fragen, öffentliche Demo, Administratorpasswort, Passwort zurücksetzen, Passwort vergessen, Installer, Hinweisbalken, Produktion, demo mode, questions, EXP_DEMO_MODE, public demo, administrator password, password reset, forgot password, publish, publishing, installer, banner, security.log, system.demo.mode, production, Velocity, LiteSpeed, PHP-FPM
type: help
---
# FAQ zum Demo-Modus

Kurze Antworten zum **Demo-Modus**, für alle, die mit einer öffentlichen Demo von Exponential arbeiten, sie vorführen,
verkaufen oder damit schulen. Die vollständige Referenz ist [Demo-Modus: der Upgrade-Hinweis](../../eng-us/bc/6.0/demo-mode.md);
die Sicht des Administrators beschreibt [Demo-Modus](demomodus.md).

## Inhalt

1. [Wofür ist der Demo-Modus da?](#wofür-ist-der-demo-modus-da)
2. [Was sperrt er?](#was-sperrt-er)
3. [Was sperrt er nicht?](#was-sperrt-er-nicht)
4. [Wie schalte ich ihn ein?](#wie-schalte-ich-ihn-ein)
5. [Warum gibt es keine Einstellung dafür?](#warum-gibt-es-keine-einstellung-dafür)
6. [Welche Passwörter akzeptieren die Installer im Demo-Modus?](#welche-passwörter-akzeptieren-die-installer-im-demo-modus)
7. [Woran erkenne ich, dass er eingeschaltet ist?](#woran-erkenne-ich-dass-er-eingeschaltet-ist)
8. [Wer gilt als Administrator?](#wer-gilt-als-administrator)
9. [Kann ich ihn auf einer echten Site verwenden?](#kann-ich-ihn-auf-einer-echten-site-verwenden)
10. [Wie schalte ich ihn aus?](#wie-schalte-ich-ihn-aus)
11. [Verwandte Seiten](#verwandte-seiten)

## Wofür ist der Demo-Modus da?

Für eine **öffentliche Demo**: eine Site, deren Anmeldedaten für den Administrator veröffentlicht sind, damit jeder
die Administration ausprobieren kann. Ohne Demo-Modus könnte der erste Besucher das Administratorpasswort ändern und
alle anderen aussperren. Mit ihm lassen sich Administratorpasswörter weder ändern noch zurücksetzen, so funktionieren
die veröffentlichten Anmeldedaten bis zum nächsten Neuaufbau.

## Was sperrt er?

Jeden Weg, ein neues Passwort für einen Administrator zu setzen: die Seite **Passwort ändern**, **Passwort
vergessen?** (es wird keine Mail zum Zurücksetzen verschickt; die Seite sagt jedem Besucher, dass Administratorpasswörter
nicht zurückgesetzt werden können), das Passwortfeld des Benutzerkontos im Bearbeitungsformular,
`exp:resetuserpassword` (auch mit `--allow-root-user`), die Webservices, die Operationen `user/password` und
`user/forgotpassword` sowie jeden Code, der einen Benutzer speichert: Der Kernel behält das gespeicherte Passwort
eines Administrators bei. Jede Ablehnung lautet „Diese Installation ist eine öffentliche Demo: Die Passwörter von
Administratoren können nicht geändert oder zurückgesetzt werden.“ und wird im Audit mit dem Grund `demo_mode`
festgehalten.

## Was sperrt er nicht?

Alles andere. Ein Administrator kann auf einer Demo weiterhin Inhalte bearbeiten, Konten anlegen, Rollen und
Einstellungen ändern, Pakete installieren und Dateien hochladen; deshalb wird eine Demo nach einem Zeitplan neu
aufgebaut. Andere Konten (Redakteure, Mitglieder) ändern ihre eigenen Passwörter und setzen sie wie gewohnt zurück,
mit allen üblichen Regeln. Die Anmeldung ist nie betroffen.

## Wie schalte ich ihn ein?

Setzen Sie die Umgebungsvariable `EXP_DEMO_MODE=1` für jeden PHP-Prozess der Demo: das PHP des Webservers
(`.htaccess` oder `SetEnv` des virtuellen Hosts, der PHP-FPM-Pool, `fastcgi_param` von nginx, die Umgebung von
Exponential Velocity), die Cronjobs und die Konsole. Fertige Dateien: [Beispiele zum Demo-Modus](../../eng-us/examples/demo-mode/README.md);
eine vollständige Einrichtung:
[Rezept: eine öffentliche Demo unter LiteSpeed und Plesk betreiben](../../eng-us/help/hosting/servers/run-a-public-demo-on-litespeed-and-plesk.md).

## Warum gibt es keine Einstellung dafür?

Damit er nicht versehentlich eingeschaltet werden kann. Eine INI-Einstellung würde mit einem kopierten
Einstellungsverzeichnis, einer wiederhergestellten Datenbank oder einem Paket mitwandern; einen Anfrageparameter könnte
jeder senden. Die Umgebung gehört zum Server einer einzelnen Site. Nur genau der Wert `1` zählt; `0`, `true`, `yes` und
`on` lassen ihn ausgeschaltet.

## Welche Passwörter akzeptieren die Installer im Demo-Modus?

Das Administratorpasswort einer neuen Installation darf allgemein bekannt und kürzer als `MinPasswordLength` sein, so
werden `publish` und `publishing` vom Einrichtungsassistenten, vom Kickstarter und von `exp:install` so installiert,
wie sie angegeben sind. Ein leeres Passwort wird weiterhin abgelehnt (der Kickstarter und `exp:install` erzeugen
eines). Jedes andere Konto und jedes Passwort, das nach der Installation gesetzt wird, unterliegt allen Regeln. Ohne
Demo-Modus ersetzen die Installer ein solches Passwort durch ein erzeugtes.

## Woran erkenne ich, dass er eingeschaltet ist?

- Die Administration zeigt auf jeder Seite einen gelben Hinweisbalken **Demo-Modus**.
- `var/log/security.log` enthält für jeden Prozess, der mit ihm gestartet ist, eine Zeile
  `demo/mode on pid=... sapi=...`.
- Das Audit enthält ein Ereignis `system.demo.mode` je Prozess.
- Konsolenbefehle geben `Demo mode is on (EXP_DEMO_MODE=1) ...` auf der Standardfehlerausgabe aus.

## Wer gilt als Administrator?

Ein Konto, dessen Rollen ihm uneingeschränkten Zugriff auf jedes Modul und jede Funktion geben: `admin`, alle in
**Administrator users** und jeder mit der Rolle **Administrator**. Ein Konto, dessen Rollen sich nicht lesen lassen,
gilt ebenfalls als Administrator.

## Kann ich ihn auf einer echten Site verwenden?

Nein. Demo-Modus bedeutet, dass das Administratorpasswort bekannt sein soll und nicht geändert werden kann. Setzen Sie
die Variable nie serverweit, in einem gemeinsamen Pool, einem Basis-Image oder einer Vorlage, aus der andere Sites
gebaut werden. Erscheint der Hinweisbalken oder eine Zeile `demo/mode` jemals auf einer echten Site, entfernen Sie die
Variable sofort: [Probleme mit dem Demo-Modus](../../eng-us/help/hosting/runbooks/demo-mode-problems.md).

## Wie schalte ich ihn aus?

Entfernen Sie die Variable überall, wo sie gesetzt war, und laden Sie den betreffenden Dienst neu oder starten Sie ihn
neu (PHP-FPM, den Webserver, Velocity) sowie die Crontab. In der Datenbank ist nichts rückgängig zu machen: Sobald die
Variable weg ist, lassen sich Passwörter wieder ändern, und der Hinweisbalken verschwindet mit der nächsten Anfrage.

## Verwandte Seiten

- [Demo-Modus](demomodus.md), [Demo-Modus: der Upgrade-Hinweis](../../eng-us/bc/6.0/demo-mode.md)
- [Demo-Modus für Hoster](../../eng-us/help/hosting/security/demo-mode-for-hosts.md),
  [Demo-Modus für Entwickler](../../eng-us/help/extension-development/part-8-reference/demo-mode-for-developers.md)
- [Probleme mit dem Demo-Modus](../../eng-us/help/hosting/runbooks/demo-mode-problems.md)
- [Umgebungsvariablen](../anleitungen/umgebungsvariablen.md)
- [Freie Dokumentation](freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
