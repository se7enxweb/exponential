---
title: Wie sehe ich die aktiven Erweiterungen und die Systeminformationen?
audience: admin
category: manual
order: 545
summary: Setup > Extensions listet jede Erweiterung, aktiv oder nicht, mit Version und Ladereihenfolge auf; Setup > Systeminformationen zeigt Zustandsprüfungen, PHP, die Datenbank und die aktiven Erweiterungen, mit einem Bericht zum Kopieren für den Support.
keywords: Erweiterungen, aktive Erweiterungen, Systeminformationen, Zustandsprüfungen, PHP-Version, Datenbank, Bericht für den Support, setup/extensions, setup/info, extensions, active extensions, system information, health checks, PHP version, database, support report
context: setup/info
type: help
---
# Wie sehe ich die aktiven Erweiterungen und die Systeminformationen?

Zwei Seiten beantworten die Frage „Was ist installiert und wie läuft es?“: **Extensions** und **Systeminformationen**.
Der Support fragt zuerst danach.

## Wer das tun kann

**Extensions** braucht `setup/setup`; das Aktivieren oder Deaktivieren braucht es ebenfalls. **Systeminformationen**
braucht `setup/system_info`.

## Schritte

1. Öffnen Sie **Setup** > **Extensions**.
   Die **Übersicht** zählt aktive und inaktive Erweiterungen; **Alle Extensions** listet jede mit ihrer Version, ihrer
   Lizenz und der Angabe, ob sie ein Git-Checkout ist.
2. Verwenden Sie **Extensions finden**, um nach Name, Beschreibung oder Lizenz zu suchen, oder zeigen Sie nur
   **Aktiv** oder **Inaktiv** an. Klicken Sie auf den Namen einer Erweiterung für ihre **Details**.
3. Öffnen Sie **Setup** > **Systeminformationen**.
   Zuerst kommen die **Zustandsprüfungen**, dann die **Übersicht** und Abschnitte für **Aktive Extensions**,
   **PHP-Erweiterungen und -Einstellungen**, **Siteaccesses**, **Datenbankverbindung** und mehr.
4. Für den Support öffnen Sie **Bericht für den Support** und klicken Sie auf **Bericht als Text kopieren** oder
   **Bericht herunterladen (.txt)**.

![Systeminformationen mit den Zustandsprüfungen, der Übersicht und den aufklappbaren Abschnitten](../../../eng-us/help/manual/images/system-information.webp)

## Was schiefgehen kann

- **Achtung: Das Deaktivieren einer Erweiterung kann die Website beschädigen.** Seiten und Inhalte, die sie brauchen,
  funktionieren nicht mehr. Ändern Sie Erweiterungen erst, nachdem Sie die Warnungen gelesen haben, und zuerst auf einer
  Testkopie.
- **Eine Zustandsprüfung ist rot.** Jede Prüfung sagt, was falsch ist und was zu tun ist; geben Sie das an die Person
  weiter, die den Server betreibt.
- **Der Bericht enthält Angaben zum Server.** Schicken Sie ihn nur an Ihren Support-Partner, nicht öffentlich.

## Verwandte Seiten

- [Die Seite Extensions](../../anleitungen/extensions-seite.md)
- [Systeminformationen](../../anleitungen/systeminformationen.md)
- [Setup > Extensions: Ladereihenfolge und sicheres Speichern](../../funktionen/6.0/ladereihenfolge-der-extensions.md)
- [Benutzerhandbuch](README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
