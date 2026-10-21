---
title: Eine verwaiste Erweiterung übernehmen
audience: developer, app-creator
category: extension-development
order: 130
summary: Wie Sie erkennen, ob eine Erweiterung wirklich verwaist ist, wie Sie den Autor um Pflege oder eine Übergabe bitten und wie Sie sie fair übernehmen, wenn niemand antwortet - mit den Schritten und den Hinweisen, die erhalten bleiben müssen.
keywords: verwaiste Erweiterung, übernehmen, Maintainer, Übergabe, nicht gepflegt, Fork, Autor, Kontakt, Übertragung, Community, abandoned extension, adopt, hand-over, unmaintained, fork, author, transfer
type: help
---
# Eine verwaiste Erweiterung übernehmen

Teil 5, Kapitel 13. Viele nützliche Erweiterungen wurden vor Jahren geschrieben und werden nicht mehr gepflegt: Der
Autor hat sich anderem zugewandt, die Firma gibt es nicht mehr, das Repository wurde archiviert. Jemand muss sich um
sie kümmern, wenn sie weiter funktionieren sollen. Dieses Kapitel erklärt, wie Sie diese Person werden, ohne
jemandem auf die Füße zu treten.

## Ist sie wirklich verwaist?

Anzeichen, von denen keines für sich allein ein Beweis ist:

| Anzeichen | Wo Sie nachsehen |
|---|---|
| Seit Jahren keine Commits | die Commit-Historie des Repositorys |
| Issues und Pull Requests lange unbeantwortet | die Reiter „Issues“ und „Pull requests“ |
| Das Repository ist archiviert | ein Hinweisbanner auf GitHub („This repository has been archived by the owner“) |
| Die README sagt es | „no longer maintained“, „looking for a maintainer“ |
| Sie läuft auf keiner unterstützten PHP-Version | [Eine alte Erweiterung bewerten](../teil-1-vom-benutzer-zum-entwickler/eine-alte-erweiterung-bewerten.md) |

Eine Erweiterung, die einfach fertig ist (sie erledigt eine Sache, und das gut), hat vielleicht seit Jahren keine
Commits und wird trotzdem gepflegt. Fragen Sie, bevor Sie etwas annehmen.

## Schritt 1: fragen

Eröffnen Sie ein Issue im ursprünglichen Repository, und wenn der Autor einen Kontakt angibt, schreiben Sie auch dort:

```text
Title: Is this extension still maintained? Offer to help

Hello, we use this extension with Exponential 6 on PHP 8 and have fixes for PHP 8.0 to 8.5 and for the Content
Security Policy. We would like to send them as pull requests.

If you no longer have time for the extension, we would also be glad to maintain it: as a co-maintainer here, by
transferring the repository, or in a fork that names you as the original author. Whatever you prefer.

Thank you for writing it.
```

Bieten Sie die Möglichkeiten an und lassen Sie den Autor wählen. Viele Autoren sind erleichtert, gefragt zu werden.

## Schritt 2: warten

Lassen Sie sich Zeit: mindestens mehrere Wochen. Schreiben Sie einmal höflich nach. Suchen Sie nach anderen Wegen,
den Autor zu erreichen (eine Profilseite, eine Firmenwebsite), aber respektieren Sie es, wenn er nicht kontaktiert
werden möchte.

## Schritt 3: die möglichen Ergebnisse

| Ergebnis | Was Sie tun |
|---|---|
| Der Autor antwortet und möchte sie behalten | Ihre Pull Requests schicken; anbieten, die anderer zu prüfen |
| Der Autor nimmt Sie als Co-Maintainer auf | Sie pflegen sie im ursprünglichen Repository; nichts zieht um |
| Der Autor überträgt das Repository | GitHub leitet die alte Adresse um; Historie und Issues ziehen mit |
| Keine Antwort | ein Fork, fair gemacht (siehe unten) |
| Der Autor lehnt einen Fork ab | die Lizenz erlaubt ihn trotzdem, aber überlegen Sie es sich gut und reden Sie miteinander; vielleicht ist es ein Missverständnis |

## Schritt 4: ein fairer Fork, wenn niemand antwortet

- Forken Sie auf GitHub, damit die Historie und der Vermerk „forked from“ erhalten bleiben ([Auf GitHub forken, Schritt für Schritt](auf-github-forken.md)).
- Behalten Sie die Lizenz und jeden Urheberrechtshinweis; fügen Sie Ihren hinzu ([Namensnennung und Urheberrechtshinweise](namensnennung-und-urheberrechtshinweise.md)).
- Verwenden Sie einen eigenen Paketnamen. Schreiben Sie in die README: „This is a maintained fork of X by Y, which is
  no longer maintained (we asked in issue #N on date D). All credit for the original goes to Y.“
- Verlinken Sie das Issue, in dem Sie gefragt haben, damit jeder sehen kann, dass Sie es getan haben.
- Wenn der Autor zurückkommt, bieten Sie an, Ihre Arbeit in das Original zu übernehmen.

Mehrere Erweiterungen in der Organisation se7enxweb auf GitHub sind gepflegte Forks von Erweiterungen aus den
2000er- und 2010er-Jahren; ihre Repositorys zeigen „forked from“ unter dem Namen, und ihre READMEs und
`composer.json`-Dateien nennen die ursprünglichen Autoren. Sehen Sie sich einige davon an, bevor Sie Ihre eigene
schreiben.

## Schritt 5: sie pflegen

Übernehmen bedeutet die Arbeit aus [Der Weg zum Maintainer](der-weg-zum-maintainer.md): Issues beantworten, Pull
Requests prüfen, Releases veröffentlichen. Beginnen Sie damit, sie zu aktualisieren
([Teil 4](../teil-4-aktualisieren/README.md)) und eine Version zu
veröffentlichen, die auf aktuellem PHP läuft. Teilen Sie es dann den Leuten mit, die sie verwenden: auf
[share.exponential.earth](https://share.exponential.earth) und im Issue des ursprünglichen Repositorys.

## Verwandte Seiten

- Zurück: [Die Arbeit anderer prüfen](die-arbeit-anderer-pruefen.md)
- Weiter: [Der Weg zum Maintainer](der-weg-zum-maintainer.md)
- [Wann Sie forken sollten und wann nicht](wann-forken.md)
- [Transferring a repository](https://docs.github.com/en/repositories/creating-and-managing-repositories/transferring-a-repository) in der GitHub-Dokumentation (auf Englisch)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
