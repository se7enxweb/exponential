---
title: Wie Updates funktionieren
audience: customer, businesses, admin
category: customer-success
order: 2
summary: Was ein Update einer Exponential-Website ist, welche Teile es betrifft (Code, Erweiterungen, Datenbank, Caches), wie Releases und Sicherheitskorrekturen Sie erreichen und was der Betreiber entscheidet.
keywords: Update, Upgrade, Release, Tag, Git, Composer, Datenbank-Update, Erweiterungen, Sicherheitskorrektur, Version, update, upgrade, release, tag, database update, extensions, security fix, version
type: help
---
# Wie Updates funktionieren

Diese Seite erklärt ohne Befehle, was geschieht, wenn Ihre Exponential-Website aktualisiert wird. Wenn Sie die Teile
kennen, können Sie besser planen, die richtigen Fragen stellen und einen Kostenvoranschlag beurteilen. Die Personen, die
die Arbeit erledigen, folgen [Upgrade](../../../anleitungen/aktualisieren.md) und [Mit Git aktualisieren](../../mit-git-aktualisieren.md).

## Warum Sie überhaupt aktualisieren

Das Projekt behebt Probleme, auch Sicherheitsprobleme, in der gepflegten Linie und veröffentlicht die Korrekturen im
nächsten Release. Eine veröffentlichte Version wird danach nie geändert: „Eine Korrektur für ein Problem in `v6.0.14`
oder einem früheren Tag erreicht Sie als nächstes Release, nicht als korrigierte Kopie des alten“ (`SECURITY.md`). Für
Sie bedeutet das: **Der einzige Weg, eine Korrektur zu erhalten, ist zu aktualisieren.** Aktuell zu bleiben ist der
Sicherheitsplan.

## Die vier Teile eines Updates

Ein Update einer Exponential-Website betrifft vier Dinge, in dieser Reihenfolge
([Upgrade, Teil C](../../../anleitungen/aktualisieren.md)):

| Teil | Was sich ändert | Wer es üblicherweise erledigt |
|---|---|---|
| Der Code des Kernels | Die Dateien von Exponential selbst, aus einem Release-Tag oder einem Branch | Ihr Entwickler, Partner oder Hoster |
| Die Erweiterungen | Zusätze wie der Newsletter, die zweistufige Anmeldung oder Layouts, mit Composer passend zum neuen Kernel aktualisiert | Dieselbe Person |
| Die Datenbank | Ein Release kann eine SQL-Datei mitbringen, die Tabellen und Spalten hinzufügt oder ändert; sie wird einmal angewendet | Dieselbe Person, nach einem Backup |
| Caches und der laufende Server | Caches werden geleert und der PHP-Server (oder Exponential Velocity) neu geladen, damit Besucher den neuen Code sehen | Dieselbe Person, zuletzt |

Bei einer Website aus Git-Klonen sind die ersten beiden Schritte „Git bringt den Kernel auf Stand“ und dann „Composer
bringt die Erweiterungen auf Stand“ ([Mit Git aktualisieren](../../mit-git-aktualisieren.md)). Beides lässt sich über die
Kommandozeile erledigen oder, wenn die Erweiterungen `git_manager` und `ezupdate` aktiv sind, aus der Administration.

## Was der Betreiber entscheidet

| Entscheidung | Warum sie Ihre ist |
|---|---|
| Wie eng Sie folgen | Das neueste Release-Tag oder ein Branch, den Ihr Anbieter pflegt; siehe [Mit Git aktualisieren](../../mit-git-aktualisieren.md) |
| Wann das Zeitfenster ist | Besucher sehen möglicherweise für kurze Zeit eine Wartungsseite |
| Wer danach was testet | Nur Ihr Team weiß, welche Seiten und Formulare am wichtigsten sind |
| Was geschieht, wenn es fehlschlägt | Zum Backup zurückkehren oder vorwärts korrigieren; entscheiden Sie vorher, nicht währenddessen |

## Woran Sie erkennen, dass ein Update funktioniert hat

Die Person, die das Update durchführt, hat Prüfungen mit einem klaren Ergebnis
([Upgrade, das Upgrade prüfen](../../../anleitungen/aktualisieren.md)):

- **Setup > Systeminformationen** nennt die neue Version.
- **Setup > Upgrade-Prüfung** vergleicht die Dateien mit dem Release und die Datenbank mit ihrem Schema; sie liest nur
  und ändert nichts ([Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)).
- Die Startseite lädt, die Administration öffnet sich, und ein Testelement lässt sich veröffentlichen.

Lassen Sie sich diese drei Ergebnisse nach jedem Update schriftlich geben.

## Wo Sie lesen, was sich geändert hat

Jedes Release hat ein Changelog mit einem Abschnitt „Upgrading“, und die Hinweise zu Verhaltensänderungen sagen, was
sich anders verhält ([Changelog 6.0.15](../../../../changelogs/6.0/6.0.15.md),
[Verhaltensänderungen vom Oktober 2026](../../../bc/6.0/verhaltensaenderungen-2026-10.md)). Bitten Sie Ihren Anbieter um
eine kurze Zusammenfassung in Ihren eigenen Worten: was sich für die Redaktion ändert, was sich für Besucher ändert und
alles, was Ihr Team tun muss.

> **Hinweis**
> Das Projekt veröffentlicht keinen Release-Kalender. Releases der Linie 6.0 kamen in Abständen von etwa einem bis fünf
> Monaten ([Release-Linie und Support-Richtlinie](../../unternehmen/zuverlaessigkeit/release-linie-und-support-richtlinie.md)).
> Planen Sie mit den Releases, die es gibt, nicht mit einem Datum.

## Automatische Updates

Eine Website aus Git-Klonen kann sich mit Cron oder einem systemd-Timer nach Plan selbst aktualisieren
([Mit Git aktualisieren](../../mit-git-aktualisieren.md)). Ob das zu Ihnen passt, ist eine Entscheidung, die Sie mit Ihrem
Anbieter treffen: Es spart Arbeit, aber jemand muss trotzdem das Ergebnis lesen und die Website prüfen. Der
Hosting-Leitfaden rät, den prüfenden Teil über viele Websites hinweg zu automatisieren, „ohne etwas unbeaufsichtigt zu
aktualisieren“ ([Automatische Updates mit ezupdate](../../hosting/betrieb/automatische-updates-mit-ezupdate.md)).

## Verwandte Seiten

- [Release-Linie und Support](release-linie-und-support.md)
- [Upgrades mit Ihrem Anbieter](upgrades-mit-ihrem-anbieter.md)
- [Checkliste für den Upgrade-Tag](checkliste-upgrade-tag.md)
- [Upgrades ohne Überraschungen](../../unternehmen/zuverlaessigkeit/aktualisierungen-ohne-ueberraschungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
