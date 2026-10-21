---
title: Demo-Modus
audience: admin, hosting, developer
category: administration
order: 33
summary: Was sich auf einer Installation ändert, die als öffentliche Demo läuft (die Umgebungsvariable EXP_DEMO_MODE=1) - Administratorpasswörter lassen sich weder ändern noch zurücksetzen, die Installer akzeptieren das veröffentlichte Demo-Passwort, die Administration zeigt einen Hinweisbalken - und warum er auf einer echten Site nie eingeschaltet sein darf.
keywords: Demo-Modus, öffentliche Demo, Demo-Site, Administratorpasswort, Passwort zurücksetzen, Passwort vergessen, Hinweisbalken, Umgebungsvariable, bekanntes Passwort, demo mode, EXP_DEMO_MODE, public demo, demonstration site, administrator password, password reset, forgot password, banner, environment variable, well-known password, security.log, system.demo.mode
type: help
---
# Demo-Modus

Diese Seite ist für Administratoren einer Exponential-Site und für alle, die sich fragen, warum die Administration den
Hinweisbalken **Demo-Modus** zeigt. Eine öffentliche Demo-Site veröffentlicht die Anmeldedaten ihres Administrators,
damit jeder die Administration ausprobieren kann. Der **Demo-Modus** hält eine solche Demo für den nächsten Besucher
benutzbar: Niemand kann das Passwort eines Administrators ändern oder zurücksetzen. Eingeschaltet wird er vom Server,
mit der Umgebungsvariablen `EXP_DEMO_MODE=1`, und nie aus der Administration heraus.

> **Warnung:** Demo-Modus bedeutet, dass das Administratorpasswort allen bekannt ist. Wenn Sie den Hinweisbalken auf
> einer Site mit echten Inhalten, echten Benutzern oder echten Kunden sehen, informieren Sie sofort die Betreiber des
> Servers: Die Variable muss dort entfernt werden.

## Inhalt

1. [Woran Sie erkennen, dass der Demo-Modus eingeschaltet ist](#woran-sie-erkennen-dass-der-demo-modus-eingeschaltet-ist)
2. [Was Sie im Demo-Modus nicht tun können](#was-sie-im-demo-modus-nicht-tun-können)
3. [Was weiterhin funktioniert](#was-weiterhin-funktioniert)
4. [Wer als Administrator gilt](#wer-als-administrator-gilt)
5. [Ein- und ausschalten](#ein--und-ausschalten)
6. [Verwandte Seiten](#verwandte-seiten)

## Woran Sie erkennen, dass der Demo-Modus eingeschaltet ist

- Jede Seite der Administration (admin4 und die klassische Administration) zeigt oben einen gelben Hinweis:
  **Demo-Modus.** „Diese Installation ist eine öffentliche Demo: Jeder kann ihr Administratorpasswort kennen, und
  Administratorpasswörter können nicht geändert oder zurückgesetzt werden. Geben Sie hier nichts Privates ein.“
- Die Seite **Passwort ändern** eines Administrators sagt, dass das Passwort nicht geändert werden kann.
- **Setup > Logdateien** zeigt in `security.log` Zeilen mit `demo/mode`, und das Audit enthält Ereignisse
  `system.demo.mode`.

## Was Sie im Demo-Modus nicht tun können

Für ein Administratorkonto lehnt der Demo-Modus jeden Weg ab, ein neues Passwort zu setzen:

- **Passwort ändern** (im Benutzermenü): Das Formular zeigt „Diese Installation ist eine öffentliche Demo: Die
  Passwörter von Administratoren können nicht geändert oder zurückgesetzt werden.“, und es wird nichts geändert.
- **Passwort vergessen?**: Für einen Administrator wird keine Mail zum Zurücksetzen verschickt. Die Seite sagt jedem
  Besucher, dass Administratorpasswörter nicht zurückgesetzt werden können, und gibt jeder Adresse dieselbe Antwort, so
  verrät sie nicht, welche Adresse einem Administrator gehört.
- **Bearbeiten** des Benutzers (das Feld des Benutzerkontos): Ein neues Passwort für einen Administrator wird mit
  derselben Meldung abgelehnt; der Name, die E-Mail-Adresse und die anderen Felder lassen sich weiterhin ändern.
- Der Konsolenbefehl `exp:resetuserpassword`, die Webservices und Importe: für einen Administrator abgelehnt oder
  übergangen.

Jede Ablehnung wird im Audit (**Setup > Audit**) mit dem Grund `demo_mode` festgehalten.

## Was weiterhin funktioniert

- Die Anmeldung mit dem veröffentlichten Passwort.
- Alles andere, was ein Administrator tun darf. Der Demo-Modus macht die Site nicht schreibgeschützt: Ein Besucher
  kann Inhalte bearbeiten, Benutzer anlegen, Rollen und Einstellungen ändern. Deshalb wird eine Demo nach einem
  Zeitplan von Grund auf neu aufgebaut.
- Andere Konten: Ein Redakteur oder ein registriertes Mitglied ändert und setzt sein eigenes Passwort wie gewohnt
  zurück, mit allen üblichen Passwortregeln.

## Wer als Administrator gilt

Ein Konto, dessen Rollen ihm uneingeschränkten Zugriff auf alles geben: das Konto `admin`, alle in **Administrator
users** und jedes Konto, dem die Rolle **Administrator** direkt zugewiesen ist. Ein Konto mit eingeschränktem Zugriff
(ein Redakteur, ein Administrator einer Sektion) wird vom Demo-Modus nicht geschützt.

## Ein- und ausschalten

Der Demo-Modus ist keine Einstellung. Es gibt keinen Schalter in der Administration, in den Einstellungsdateien oder in
der Adresszeile, so kann niemand ihn versehentlich oder irrtümlich einschalten. Die Betreiber des Servers setzen oder
entfernen die Umgebungsvariable `EXP_DEMO_MODE=1` für die PHP-Prozesse der Demo; nur genau der Wert `1` schaltet ihn
ein. Wie das für jede Art von Server geht, steht unter
[Demo-Modus für Hoster](../../eng-us/help/hosting/security/demo-mode-for-hosts.md).

Während der Installation einer Demo erlaubt der Demo-Modus dem Installer außerdem, das veröffentlichte Demo-Passwort
zu akzeptieren, das er sonst als zu bekannt oder zu kurz ablehnen würde (`publish` oder `publishing`). Die Einzelheiten
stehen im Upgrade-Hinweis [Demo-Modus: EXP_DEMO_MODE=1](../../eng-us/bc/6.0/demo-mode.md).

## Verwandte Seiten

- [Demo-Modus für Hoster](../../eng-us/help/hosting/security/demo-mode-for-hosts.md), [FAQ zum Demo-Modus](demomodus-faq.md),
  [Probleme mit dem Demo-Modus](../../eng-us/help/hosting/runbooks/demo-mode-problems.md)
- [Demo-Modus für Entwickler](../../eng-us/help/extension-development/part-8-reference/demo-mode-for-developers.md)
- [Demo-Modus: der Upgrade-Hinweis](../../eng-us/bc/6.0/demo-mode.md)
- [Das Standard-Administratorpasswort](standard-administratorpasswort.md),
  [Das Administratorpasswort ändern](administratorpasswort-aendern.md)
- [Eine Demo-Installation einrichten](presse/eine-demo-installation-einrichten.md),
  [Sicherheit bei Live-Demos](../../eng-us/help/advocates/live-demo-safety.md)
- [Freie Dokumentation](freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.
