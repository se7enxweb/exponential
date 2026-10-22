---
title: Fragen in der Community beantworten
audience: community, advocate, supporter, developer, admin
category: community
order: 41
summary: Wie Sie Fragen in den Exponential-Foren und -Chats beantworten - das Ziel verstehen, nach der Umgebung fragen, mit einer Quelle antworten, KI-generierte Antworten prüfen, die Privatsphäre schützen, Sicherheitsprobleme an den vertraulichen Kanal verweisen und den Faden abschließen.
keywords: antworten, Antwort, Foren, Telegram, Support, Hilfe, Quellen, Genauigkeit, KI-Antworten, Datenschutz, Sicherheit, auf die Hilfe verlinken, answering, answer, forums, support, sources, accuracy, AI answers, privacy, security
type: help
---
# Fragen in der Community beantworten

Eine gute Antwort löst das Problem, erklärt das Warum und verweist auf eine Quelle, zu der die Person zurückkehren
kann.

## Inhalt

1. [Bevor Sie antworten](#bevor-sie-antworten)
2. [Die Antwort](#die-antwort)
3. [Was Sie vermeiden sollten](#was-sie-vermeiden-sollten)
4. [Sonderfälle](#sonderfälle)
5. [Antwortmuster](#antwortmuster)

## Bevor Sie antworten

- **Verstehen Sie das Ziel**, nicht nur den Fehler. Fragen Sie „Was möchten Sie erreichen?“, wenn es unklar ist.
- **Fragen Sie nach der Umgebung**, wenn sie eine Rolle spielt: Die Ausgabe von `./console exp:info --plain --no-logo`
  oder von **Setup** > **Systeminformationen** > **Bericht als Text kopieren** kann gefahrlos geteilt werden
  ([Eine gute Frage stellen](../beitreten/eine-gute-frage-stellen.md)).
- **Prüfen Sie, ob Sie es wissen.** Wenn Sie raten, sagen Sie es.

## Die Antwort

1. Geben Sie zuerst die kurze Antwort.
2. Geben Sie die Schritte, nummeriert.
3. Verlinken Sie die Hilfeseite oder die Dokumentation, aus der die Antwort stammt
   ([Auf die richtige Hilfeseite verweisen](../../fuersprecher/auf-die-richtige-hilfeseite-verweisen.md)).
4. Nennen Sie die Version, mit der Sie es geprüft haben, wenn sich Versionen unterscheiden.
5. Bitten Sie um eine Bestätigung, ob es funktioniert hat.

## Was Sie vermeiden sollten

| Vermeiden | Warum |
|---|---|
| „Lesen Sie das Handbuch“ ohne Link | Es hilft niemandem |
| Nach Passwörtern oder Datenbank-Dumps fragen | Nie an einem öffentlichen Ort ([Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)) |
| Eine generierte Antwort als Tatsache posten | Der Verhaltenskodex: „Posten Sie generierte Antworten nicht als Tatsachen. Prüfen Sie sie zuerst an der Software und ihrer Dokumentation, oder sagen Sie, dass sie ungeprüft sind“ |
| Die eigenen bezahlten Dienstleistungen als Antwort anbieten | „Nutzen Sie die Räume der Community, um zu helfen, nicht um zu werben“ (`CODE_OF_CONDUCT.md`) |
| Eine Korrektur oder ein Release-Datum versprechen | Das können nur Maintainer, und die Quellen nennen keine Termine |

## Sonderfälle

| Die Frage ist | Tun Sie dies |
|---|---|
| Ein Sicherheitsproblem | Beenden Sie die öffentliche Diskussion und verweisen Sie auf security@se7enx.com (`SECURITY.md`); [Sicherheitsmeldungen bleiben vertraulich](../../fuersprecher/sicherheitsmeldungen-bleiben-vertraulich.md) |
| Ein Fehler | Helfen Sie, ihn zu melden ([Einen Fehler melden](../mitwirken/einen-fehler-melden.md)) |
| Zu einem bezahlten Produkt oder Vertrag | Verweisen Sie auf 7x oder den betreffenden Partner |
| Feindselig | Bleiben Sie ruhig; eskalieren Sie nicht ([Mit feindseligen Diskussionen umgehen](../../fuersprecher/feindselige-diskussionen.md)) |
| Enthält personenbezogene Daten | Bitten Sie darum, sie zu entfernen; informieren Sie einen Moderator |

## Antwortmuster

```text
Short answer: <one sentence>.
Steps:
1. ...
2. ...
Source: <link to the help page or documentation>.
Checked on: Exponential <version>, <setup>.
Did that solve it?
```

```text
I'm not sure, but this is where I would look: <page>. If it does not help, please post the output of
./console exp:info --plain --no-logo (it leaves out passwords and keys) so others can see your setup.
```

## Verwandte Seiten

- [Anderen in der Community helfen](README.md)
- [Fragen gut beantworten](../../fuersprecher/fragen-gut-beantworten.md)
- [Öffentlich antworten](../../fuersprecher/oeffentlich-antworten.md)
- [In den Foren arbeiten](../../fuersprecher/in-den-foren-arbeiten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
