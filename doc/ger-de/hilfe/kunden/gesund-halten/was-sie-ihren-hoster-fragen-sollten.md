---
title: Was Sie Ihren Hoster fragen sollten
audience: customer, hosting, businesses
category: customer-success
order: 15
summary: Die Fragen, die ein Website-Betreiber dem Unternehmen stellen sollte, das eine Exponential-Website hostet - Versionen, Updates, Backups, Wiederherstellungen, Überwachung, Sicherheit, Support und Ausstieg - mit dem, was eine gute Antwort enthält, für eine jährliche Überprüfung oder einen neuen Vertrag.
keywords: Hoster, Hosting, Fragen, Überprüfung, Backups, Updates, PHP, Überwachung, Support, Ausstieg, Nachweise, host, hosting, questions, review, backups, updates, monitoring, support, exit, evidence
type: help
---
# Was Sie Ihren Hoster fragen sollten

Ihr Hoster betreibt den Server, PHP, die Datenbank und oft auch die Updates. Diese Seite listet die zu stellenden Fragen
auf, mit dem, wie eine gute Antwort aussieht. Verwenden Sie sie, wenn Sie einen Hoster auswählen (siehe auch
[Einen Hoster auswählen](../partner-und-hoster/einen-hoster-auswaehlen.md)), und einmal im Jahr für den Hoster, den Sie haben
([Ihren Anbieter jedes Jahr überprüfen](../partner-und-hoster/anbieter-jedes-jahr-ueberpruefen.md)). Die Fragen an einen
neuen Hoster vor der Unterschrift stehen auch unter [Hosting auswählen](../../unternehmen/auswahl/hosting-auswaehlen.md).

## Die Plattform

| Fragen Sie | Eine gute Antwort |
|---|---|
| Welche PHP-Version betreibt unsere Website, und bis wann wird sie unterstützt? | Eine Version ab 8.0 (auch für Velocity), mit einem Datum; die README empfiehlt „die neueste Version von PHP, die auf Ihrem Betriebssystem verfügbar ist“ ([Release-Linie und Support-Richtlinie](../../unternehmen/zuverlaessigkeit/release-linie-und-support-richtlinie.md)) |
| Welche Datenbank-Engine und Version? | Eine benannte Engine; das Projekt listet sie in einer offiziellen Reihenfolge ([Datenbankwahl für Zuverlässigkeit](../../unternehmen/zuverlaessigkeit/datenbankwahl-fuer-zuverlaessigkeit.md)) |
| Welcher Webserver oder welche Engine liefert die Website aus? | Exponential Velocity, FrankenPHP, Apache oder nginx mit PHP-FPM, oder ein anderer benannter |
| Läuft Cron für unsere Website, wie oft? | Ja, mit benannten Cronjob-Teilen; ohne Cron schlägt Hintergrundarbeit unbemerkt fehl ([Cronjobs und Hintergrundaufträge](../../unternehmen/zuverlaessigkeit/cronjobs-und-hintergrundjobs.md)) |
| Funktioniert ausgehende E-Mail, mit welchem Absender? | Ja; Formulare, Belege, Passwort-Zurücksetzungen und Newsletter hängen davon ab |
| Läuft jeder Name über HTTPS, und wer erneuert die Zertifikate? | Ja; die Erneuerung wird überwacht ([Runbook für ablaufende Zertifikate](../../hosting/runbooks/ablauf-von-zertifikaten.md)) |

## Updates

- Wer spielt Updates von Exponential ein, und innerhalb welcher Zeit nach einem Release mit Sicherheitskorrekturen?
- Wer spielt Sicherheitsupdates für PHP und das Betriebssystem ein?
- Wird jedes Update zuerst auf einer Kopie ausprobiert? Wird direkt davor ein Backup erstellt?
- Was erhalten wir nach einem Update? (Siehe [Upgrades mit Ihrem Anbieter](upgrades-mit-ihrem-anbieter.md).)

## Backups und Wiederherstellungen

- Was wird gesichert (Datenbank und Dateien zusammen), wie oft, wie lange aufbewahrt, wo gespeichert?
- Sind die Kopien verschlüsselt und außerhalb des Servers?
- Wann war der letzte Wiederherstellungstest, wie lange hat er gedauert, und können wir das Protokoll sehen ([Wiederherstellungstest](wiederherstellungstest.md))?
- Wer darf eine Wiederherstellung anfordern, und wie schnell wird sie durchgeführt?

## Überwachung und Vorfälle

- Was wird von außen geprüft, und was auf dem Server ([Überwachung](ueberwachung.md))?
- Wer wird außerhalb der Bürozeiten alarmiert?
- Wie ist Ihre erste Reaktionszeit für „Die Website ist nicht erreichbar“ und für einen vermuteten Einbruch?
- Wie informieren Sie uns über einen Vorfall, und wie schnell?

## Sicherheit

- Ist die Administration eingeschränkt (eigener Hostname, Netzwerkregel oder Ähnliches)
  ([Die Administration einschränken](../../hosting/sicherheit/die-administration-einschraenken.md))?
- Ist die Website jedes Kunden von den anderen isoliert ([Mandantentrennung](../../hosting/sicherheit/trennung-von-mandanten.md))?
- Führen Sie vor dem Go-live und regelmäßig eine Sicherheitsüberprüfung durch ([Sicherheitsüberprüfung](../../hosting/checklisten/checkliste-fuer-die-sicherheitspruefung.md))?
- Welche Ihrer Mitarbeitenden haben Konten in unserer Administration?

## Support

- Welche Kanäle, welche Zeiten, welche Zeitzone?
- Wo verläuft die Grenze zwischen Hosting-Support und Anwendungs-Support? Der Support-Leitfaden für Hoster empfiehlt,
  diese Grenze in die Bedingungen zu schreiben ([Support-Stufen](../../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md)).
- Was brauchen Sie von uns in einer Meldung? (Ein guter Hoster fragt nach Seite, Uhrzeit und Fehlerreferenz.)

## Ausstieg

- In welchem Format und wie schnell erhalten wir einen vollständigen Export: Datenbank, Dateien, Einstellungen, Versionen
  ([Ausscheiden eines Kunden](../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md))?
- Sind Domain und DNS auf unseren Namen eingetragen?
- Was wird gelöscht, nachdem wir gegangen sind, und erhalten wir eine Bestätigung?

## Die Antworten aufbewahren

Bewahren Sie die Antworten schriftlich und datiert neben dem Vertrag auf. Ein guter Hoster beantwortet die meisten
davon innerhalb weniger Tage aus seinen eigenen Unterlagen; die [monatliche Checkliste für den Betrieb](../../hosting/checklisten/checkliste-fuer-den-monatlichen-betrieb.md)
im Bereich für Hosting zeigt, was ein solcher Hoster jeden Monat prüft.

## Verwandte Seiten

- [Was Sie Ihren Partner fragen sollten](was-sie-ihren-partner-fragen-sollten.md)
- [Wer was macht](../partner-und-hoster/wer-was-macht.md)
- [Verträge und SLAs](../partner-und-hoster/vertraege-und-slas.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
