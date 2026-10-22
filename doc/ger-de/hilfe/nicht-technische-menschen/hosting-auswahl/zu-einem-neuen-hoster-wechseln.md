---
title: Zu einem neuen Hoster wechseln
audience: non-technical, customer
category: plain-language
order: 46
summary: Der Teil des Betreibers beim Umzug einer Exponential-Website zu einem neuen Hoster, als einfache Checkliste - vorher, am Tag selbst und danach - mit dem, was alter und neuer Hoster tun, wie Sie E-Mail und Domain sicher halten und wann Sie die alte Website loslassen.
keywords: Hoster wechseln, Umzug, Migration, neuer Hoster, Hosting wechseln, Export, DNS, E-Mail, Wartungsfenster, Checkliste, alter Hoster, nicht technisch, move host, migration, new host, change hosting, export, maintenance window, checklist, old host, non-technical
type: help
---
# Zu einem neuen Hoster wechseln

Eine Exponential-Website umzuziehen, ohne ihre Version zu ändern, ist für eine technische Person Routine
([Eine Website zwischen Hostern umziehen](../../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md)). Ihr Teil ist, ihn
zu organisieren, das Datum festzulegen und das Ergebnis zu prüfen. Diese Seite ist dieser Teil.

## Inhalt

1. [Wer macht was](#wer-macht-was)
2. [Vor dem Umzug](#vor-dem-umzug)
3. [Am Tag selbst](#am-tag-selbst)
4. [Nach dem Umzug](#nach-dem-umzug)
5. [Den alten Hoster loslassen](#den-alten-hoster-loslassen)

## Wer macht was

| Aufgabe | Wer |
|---|---|
| Den neuen Hoster und das Datum wählen | Sie |
| Datenbank, Dateien und Einstellungen exportieren | Alter Hoster |
| Den Server einrichten und importieren | Neuer Hoster (oder Ihr Entwickler) |
| Das DNS bei der Registrierungsstelle ändern | Wer die Zugangsdaten zur Registrierungsstelle hat |
| Die Website testen | Sie, mit dem neuen Hoster |
| Die alte Kopie löschen | Alter Hoster, auf Ihre Bitte |

## Vor dem Umzug

- [ ] Wählen Sie den neuen Hoster mit [Was Sie vor der Unterschrift fragen sollten](was-sie-vor-der-unterschrift-fragen-sollten.md).
- [ ] Vereinbaren Sie ein Datum außerhalb Ihrer geschäftigen Zeit; Besucher sehen vielleicht eine Weile eine
      Wartungsseite ([Wartungsmodus und geplante Arbeiten](../../kunden/gesund-halten/wartungsmodus-und-geplante-arbeiten.md)).
- [ ] Bitten Sie den alten Hoster um einen vollständigen Export (Datenbank, Dateien, Einstellungen, Versionen) und
      behalten Sie selbst eine Kopie ([Einen Hoster wählen](../../kunden/partner-und-hoster/einen-hoster-auswaehlen.md#der-umzug)).
- [ ] Entscheiden Sie, was mit der **E-Mail** geschieht: Zieht sie mit um, und wer richtet sie ein?
      ([Domainname und E-Mail](domainname-und-e-mail.md))
- [ ] Sagen Sie Ihren Redakteuren: Während des Umzugs wird nichts veröffentlicht.
- [ ] Schreiben Sie Ihre **Testliste**: die fünf wichtigsten Seiten und Formulare (Startseite, Kontaktformular, eine
      Nachricht, die Kasse des Shops, wenn Sie einen haben, die Anmeldung).

## Am Tag selbst

- [ ] Lassen Sie sich bestätigen, dass der alte Hoster den letzten Export nach dem Bearbeitungsstopp gemacht hat.
- [ ] Warten Sie auf das "fertig" des neuen Hosters.
- [ ] Lassen Sie das DNS ändern. Die Änderung kann zwischen Minuten und einem Tag brauchen, bis sie bei allen ankommt.
- [ ] Lassen Sie das Datenbankpasswort beim neuen Hoster ändern
      ([Abschied eines Kunden](../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)).

## Nach dem Umzug

- [ ] Gehen Sie Ihre Testliste beim neuen Hoster durch: Seiten, ein Formular, Anmeldung, Veröffentlichen eines
      Testobjekts, das HTTPS-Schloss.
- [ ] Schicken Sie sich eine Mail zum Zurücksetzen des Passworts, um die ausgehende E-Mail zu prüfen.
- [ ] Prüfen Sie **Setup > Systeminformationen** (oder fragen Sie nach) auf die Angaben des neuen Servers und dieselbe
      Exponential-Version.
- [ ] Lassen Sie sich bestätigen, dass beim neuen Hoster Sicherungen laufen.
- [ ] Aktualisieren Sie [Wen Sie anrufen](../wenn-es-hakt/wen-sie-anrufen.md) mit den Kontakten des neuen Hosters.

## Den alten Hoster loslassen

Behalten Sie die alte Website, bis die neue einige Tage gut gelaufen ist. Bitten Sie dann den alten Hoster, Ihre Daten
zu löschen und es schriftlich zu bestätigen. Kündigen Sie den alten Vertrag erst danach.

## Verwandte Seiten

- [Hosting-Auswahl](README.md)
- [Eine Website übernehmen](../../kunden/partner-und-hoster/eine-website-uebernehmen.md)
- [Checkliste zur Übergabe](../../kunden/partner-und-hoster/checkliste-uebergabe.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
