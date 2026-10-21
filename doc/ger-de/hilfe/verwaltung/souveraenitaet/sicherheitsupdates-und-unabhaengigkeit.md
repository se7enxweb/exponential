---
title: Sicherheitsupdates und Unabhängigkeit
audience: government, admin, project-manager
category: public-sector
order: 220
summary: Wie eine öffentliche Stelle eine Exponential-Installation sicher hält, ohne von einem einzelnen Anbieter abzuhängen - woher Sicherheitskorrekturen kommen, wie Sie von Releases erfahren, wie Sie Updates mit der Upgrade-Prüfung und ezupdate einspielen und was Sie von Dienstleistern verlangen sollten.
keywords: Sicherheitsupdates, Patches, Releases, SECURITY.md, Upgrade-Prüfung, ezupdate, Composer, Offenlegung von Schwachstellen, Unabhängigkeit, Wartung, NIS2, security updates, upgrade check, vulnerability disclosure, independence, maintenance
type: help
---
# Sicherheitsupdates und Unabhängigkeit

Diese Seite richtet sich an Administratoren und Führungskräfte, die dafür verantwortlich sind, dass die Website auf dem
neuesten Patchstand bleibt. Sicherheitskorrekturen für Exponential sind öffentlich, wie der Code, sodass jede
kompetente Administration sie einspielen kann.

## Inhalt

1. [Woher die Korrekturen kommen](#woher-die-korrekturen-kommen)
2. [Von Releases erfahren](#von-releases-erfahren)
3. [Updates einspielen](#updates-einspielen)
4. [Härtung und Überwachung](#härtung-und-überwachung)
5. [Was Sie von Dienstleistern verlangen sollten](#was-sie-von-dienstleistern-verlangen-sollten)

## Woher die Korrekturen kommen

„Exponential 6.0 is the maintained line. Security fixes are made on the `main` branch ... and published with the next
6.0.x release“ (Exponential 6.0 ist die gepflegte Versionslinie; Sicherheitskorrekturen werden auf dem Zweig `main`
vorgenommen und mit dem nächsten Release 6.0.x veröffentlicht); die Versionen 5.x und älter werden nicht unterstützt;
Meldungen gehen vertraulich an die Adresse in der SECURITY.md, mit einer ersten Antwort, die innerhalb eines Werktags
zugesagt ist (SECURITY.md, zitiert in
[Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md#sicherheitskorrekturen)). Erweiterungen
werden in ihren eigenen Repositorys veröffentlicht.

## Von Releases erfahren

- Beobachten Sie die GitHub-Releases des Kernels und der Erweiterungen, die Sie nutzen.
- **Setup > Updates and packages** zeigt, für welche installierten Pakete es neuere Versionen gibt
  ([ezupdate](../../../../eng-us/features/6.0/extensions/ezupdate.md)).
- Lesen Sie die Changelogs vor dem Aktualisieren ([Changelog 6.0.15](../../../../changelogs/6.0/6.0.15.md) für die
  aktuelle Versionslinie).

## Updates einspielen

1. Aktualisieren Sie zuerst eine Testinstallation.
2. Führen Sie **Setup > Upgrade-Prüfung** aus, um Dateien und Datenbank mit dem zu vergleichen, was das Release
   erwartet ([Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)).
3. Erstellen Sie eine Datensicherung und versetzen Sie die Website in den Wartungsmodus, wenn das Release
   Datenbankänderungen verlangt ([Wartungsmodus](../../../anleitungen/wartungsmodus.md)).
4. Aktualisieren Sie mit Composer oder über **Setup > Updates and packages** (Prüfung, Probelauf, Bestätigung der
   Datensicherung).
5. Führen Sie die Update-Skripte aus, die das Release nennt, leeren Sie die Caches und stellen Sie, wenn Velocity die
   Website ausliefert, sie bereit (`./console exp:velocity deploy`).
6. Prüfen Sie die Website und die Protokolle.

Das vollständige Verfahren: [Aktualisieren](../../../anleitungen/aktualisieren.md); für Hoster:
[Automatische Updates mit ezupdate](../../hosting/betrieb/automatische-updates-mit-ezupdate.md).

## Härtung und Überwachung

- [Sicherheitshärtung](../../sicherheitshaertung.md) und [13. Sicherheitshärtung](../../../installation/13-sicherheitshaertung.md).
- Die Zuordnung zu OWASP und der Stand jeder Maßnahme: [Sicherheitsstandards](../../../../eng-us/standards/security.md).
- Die Alarmregeln des Audit-Trails für Brute-Force-Anmeldeversuche, Rollenvergaben und Massenlöschungen
  ([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)).

Rahmenwerke wie das [NIST Cybersecurity Framework 2.0](https://www.nist.gov/cyberframework) oder in der EU die
Pflichten, die sich aus der [NIS2-Richtlinie (EU) 2022/2555](https://eur-lex.europa.eu/eli/dir/2022/2555/oj) für
Einrichtungen in ihrem Anwendungsbereich ergeben, verlangen einen Patchprozess; diese Seite ist der Exponential-Teil
davon.

## Was Sie von Dienstleistern verlangen sollten

- Eine festgelegte Frist, innerhalb der Sicherheitsreleases nach ihrer Veröffentlichung eingespielt werden.
- Einen monatlichen Bericht über die installierten Versionen (die Info-Seite und `composer.lock` zeigen sie).
- Benachrichtigung bei Vorfällen und bei jeder nicht unterstützten Komponente.
- Zugang für Ihre eigenen Mitarbeitenden, um die installierten Versionen zu überprüfen.

## Verwandte Seiten

- [Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)
- [Updates und Aktualisierungen](../../../../eng-us/help/creators/website-creators/updates-and-upgrades.md)
- [Composer-Pakete und Quellcode auf GitHub](composer-pakete-und-quellcode-auf-github.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
