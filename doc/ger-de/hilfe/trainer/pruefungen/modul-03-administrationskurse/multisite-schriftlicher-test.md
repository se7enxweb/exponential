---
title: "Lektion 3.5: Multisite, schriftlicher Test"
audience: trainer, admin, developer, hosting
category: training
order: 35
summary: Ein schriftlicher Test mit zwölf Fragen für das Ende des Kurses Multisite und Seitenpakete, zu Siteaccesses, Matching über URL, Host und Port, SiteURL, vertrauenswürdigen Proxys, Logs und Caches pro Website, Einstellungen pro Website in Erweiterungen, websiteübergreifenden Links und dazu, wann getrennte Installationen sinnvoll sind, mit einem Lösungsschlüssel, der die Dokumentation zitiert. 25 Minuten für die Teilnehmenden.
keywords: prüfungen und praktische übungen, multisite, schriftlicher test, siteaccess, MatchOrder, HostMatchMapItems, HostMatchMethod, SiteURL, TrustedProxies, VarDir, INICacheDir, ezurlmultisite, seitenpakete, assessments and practical exercises, written test, site packages
type: help
---
# Lektion 3.5: Multisite, schriftlicher Test

Dieser Test schließt den Kurs Multisite und Seitenpakete ab. Sein Kern ist der Siteaccess: was er ist, wie eine
Anfrage einen findet und was übereinstimmen muss (Einstellungen, Webserver, DNS, Zertifikat), damit eine zweite
Website funktioniert. Er fragt auch, wann eine zweite Installation die bessere Wahl ist.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 3: Die Administrationskurse prüfen](README.md) |
| Dauer | 40 Minuten (5 Einweisung, 25 Test, 10 Besprechung der Antworten) |
| Niveau | Experten |
| Zielgruppe | Teilnehmende am Ende des Kurses Multisite und Seitenpakete |
| Voraussetzungen | Der Kurs Multisite und Seitenpakete; [Lektion 1.2](../modul-01-pruefungen-gestalten/schriftliche-tests-schreiben.md) für den Trainer |

## Lernziele

Wer diesen Test besteht, hat gezeigt, dass er Folgendes kann:

- einen Siteaccess definieren und die drei üblichen Arten des Matchings nennen;
- Host-Matching konfigurieren und prüfen, auch hinter einem Proxy;
- Logs, Caches und INI-Caches pro Website getrennt halten;
- Einstellungen für alle Siteaccesses einer Website in einer Erweiterung ablegen;
- zwischen einer Installation mit mehreren Siteaccesses und mehreren Installationen wählen.

## Hinweise für Trainer

### So führen Sie ihn durch

1. Teilen Sie die Fragen ohne den Schlüssel aus; 25 Minuten.
2. Gehen Sie die Antworten mit [10.2](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)
   und [Multisite auf einer Installation](../../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md) auf dem
   Projektor durch.

### Häufige Fragen

**Unser Kurs hat websiteübergreifende Links nicht behandelt.** Streichen Sie Frage 10.

### Stolperfallen

- **Teilnehmende, die nur URL-Matching verwendet haben.** Die Fragen 3 bis 6 brauchen Host-Matching; stellen Sie
  sicher, dass der Kurs es gezeigt hat, zumindest auf dem Projektor.

## Der Test

1. Was ist ein Siteaccess, in einem Satz mit seinen drei Bestandteilen?
2. Wie lautet die mitgelieferte `MatchOrder`, und was bedeutet sie?
3. Nennen Sie einen Vorteil des Host-Matchings gegenüber dem URL-Matching für den Produktivbetrieb.
4. Sie haben Host-Matching eingerichtet, aber beide Hostnamen erreichen denselben Siteaccess. Was prüfen Sie in
   `HostMatchMapItems[]`?
5. Ein Testhost `www.example.com.test.local` soll den Siteaccess von `www.example.com` erreichen. Welche Einstellung
   und welcher Wert erlauben das, und was ist das Risiko?
6. Die Website steht hinter einem Load Balancer auf einem anderen Rechner und behandelt HTTPS-Anfragen wie reines
   HTTP. Welche Einstellung fehlt?
7. Wofür wird `[SiteSettings] SiteURL` verwendet?
8. Mehrere Websites teilen sich eine Installation. Welche Einstellung lässt jede Website ihre INI-Dateien in einem
   eigenen Verzeichnis cachen?
9. Eine Erweiterung bedient die Siteaccesses `example__eng` und `example__ger`. Wohin gehören Einstellungen, die für
   beide gelten?
10. Ein Link von Website A zu einem Artikel von Website B erhält den Pfad von Website A vorangestellt und führt ins
    Leere. Welche Erweiterung behebt das?
11. Zwei verschiedene Kunden möchten je eine Website. Eine Installation mit zwei Siteaccesses oder zwei
    Installationen? Warum?
12. Welchen Cache leeren Sie, nachdem Sie Siteaccess-Einstellungen geändert haben?

<details>
<summary>Antworten</summary>

1. Ein Zugang zur Installation: ein Name, ein Satz Einstellungen in `settings/siteaccess/<name>/` und eine Regel, auf
   welche Anfragen er antwortet
   ([10.2](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).
2. `uri;host;port`: die Arten, wie eine Anfrage zugeordnet wird, in dieser Reihenfolge versucht
   ([10.2](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).
3. Ein Hostname pro Siteaccess passt zum Produktivbetrieb und zu HTTPS-Zertifikaten pro Host
   ([Multisite auf einer Installation](../../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md#zuordnung-nach-url-host-oder-port)).
4. Der Hostname, den der Browser sendet, muss genau der aufgeführte sein, ohne Port und in Kleinbuchstaben
   ([10.2](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).
5. `HostMatchMethod=start` (oder die Methode `start` bei diesem einen Eintrag); `start` nimmt auch Hosts an, die Sie
   nicht gemeint haben, verwenden Sie es daher nur, wo der Webserver nur für Ihre eigenen Hosts antwortet
   ([Host- und Sprach-Matching für Siteaccesses](../../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md)).
6. Die Adresse des Load Balancers in `[HTTPHeaderSettings] TrustedProxies[]`
   ([10.2](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).
7. Die Adresse der Website, verwendet in E-Mails, Feeds, Sitemaps und von `exp:benchmark`
   ([10.2](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).
8. `[FileSettings] INICacheDir=site`
   ([Multisite auf einer Installation](../../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md#logs-und-caches-pro-website)).
9. In `settings/override__example/` der Erweiterung (für die Gruppe) oder in `settings/override/` (für jeden
   Siteaccess der Erweiterung)
   ([Einstellungen pro Website in Erweiterungen](../../../../../eng-us/features/6.0/multi-site-ini-overrides.md)).
10. `ezurlmultisite` ([Websiteübergreifende Links in Multisite](../../../../../eng-us/features/6.0/multisite-cross-site-links.md)).
11. Zwei Installationen: Verschiedene Kunden brauchen Isolation auf Betriebssystemebene, getrennte Last und getrennte
    Sicherungen
    ([Multisite auf einer Installation](../../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md#eine-installation-oder-mehrere)).
12. Den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`
    ([10.2](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).

</details>

## Bewertung

- Ein Punkt pro Frage; Frage 1 braucht alle drei Bestandteile, Frage 5 die Einstellung und das Risiko.
- Bestanden: 8 von 12 Punkten oder mehr, mit den Fragen 4 und 11 richtig.

## Weiterführende Literatur

- [10.2 Siteaccesses und Website-Adressen](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)
- [Multisite auf einer Installation](../../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md)
- [Einstellungen pro Website in Erweiterungen](../../../../../eng-us/features/6.0/multi-site-ini-overrides.md) und
  [Websiteübergreifende Links in Multisite](../../../../../eng-us/features/6.0/multisite-cross-site-links.md)

## Verwandte Seiten

- [Lektion 3.6: Multisite, praktische Aufgabe](multisite-praktische-aufgabe.md)
- [Modul 3: Die Administrationskurse prüfen](README.md)
- [Prüfungen und praktische Übungen: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
