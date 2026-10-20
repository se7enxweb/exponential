---
title: "Teil 5: Erweitern, fair forken und zurückgeben"
audience: developer, designer, app-creator
category: extension-development
order: 50
summary: Teil 5 des Buches - fair auf den Erweiterungen anderer aufbauen - erweitern ohne zu forken, forken, wenn es sein muss, Namensnennung und Lizenzen richtig halten und die eigene Arbeit mit Pull Requests, Issues und Reviews zurückgeben.
keywords: Buch, Teil 5, Fork, forken, erweitern, beitragen, zurückgeben, Pull Request, Issue, Lizenz, Namensnennung, Urheberrecht, Rebranding, Maintainer, Community, Ethik, book, part 5, fork, extend, contribute, pull request, license, attribution, credit, rebranding, maintainer, community, ethics
type: help
---
# Teil 5: Erweitern, fair forken und zurückgeben

Dies ist der fünfte Teil von [Erweiterungsentwicklung: das Buch](../README.md).
Kaum jemand schreibt eine Erweiterung allein und aus dem Nichts. Sie bauen auf dem Kernel auf, auf den Erweiterungen
anderer und auf Bibliotheken. In diesem Teil geht es darum, das gut zu machen: technisch (wie Sie den Code eines
anderen ändern, ohne dessen Updates zu verlieren) und fair (wie Sie die Namensnennung erhalten, die Lizenz achten und
Ihre Verbesserungen zurückgeben).

## Die Kapitel

| Gruppe | Kapitel |
|---|---|
| Den Code eines anderen ändern | [Erweitern, ohne zu forken](erweitern-ohne-zu-forken.md), [Wann Sie forken sollten und wann nicht](wann-forken.md), [Auf GitHub forken, Schritt für Schritt](auf-github-forken.md), [Einen Fork mit seiner Quelle synchron halten](einen-fork-synchron-halten.md) |
| Fairness | [Ethisches Rebranding](ethisches-rebranding.md), [Lizenzen und wie sie sich kombinieren lassen](lizenzen-und-kompatibilitaet.md), [Namensnennung und Urheberrechtshinweise](namensnennung-und-urheberrechtshinweise.md) |
| Zurückgeben | [Mit Pull Requests zurückgeben](mit-pull-requests-zurueckgeben.md), [Pull-Request-Etikette](pull-request-etikette.md), [Gute Issues schreiben](gute-issues-schreiben.md), [Commit-Nachrichten, aus denen das Projekt ein Changelog bauen kann](commit-nachrichten.md), [Die Arbeit anderer prüfen](die-arbeit-anderer-pruefen.md) |
| Verantwortung übernehmen | [Eine verwaiste Erweiterung übernehmen](eine-verwaiste-erweiterung-uebernehmen.md), [Der Weg zum Maintainer](der-weg-zum-maintainer.md), [Die Community durch Code wachsen lassen](die-community-durch-code-wachsen-lassen.md) |
| Fallstudien | [Ein Paket aus einem anderen Projekt mit Namensnennung portieren](fallstudie-aus-einem-anderen-projekt-portiert.md), [Ein Pull Request an einen Upstream-Autor](fallstudie-ein-pull-request-an-einen-upstream-autor.md) |

## Drei Grundsätze

1. **Ändern Sie so wenig wie möglich am Code anderer.** Der Kernel und die meisten Erweiterungen bieten Wege, das
   Verhalten aus Ihrer eigenen Erweiterung heraus zu ändern. Eine Änderung, die Sie am Code eines anderen nicht
   vornehmen, müssen Sie auch nie weitertragen.
2. **Nehmen Sie nie Anerkennung in Anspruch, die Sie sich nicht verdient haben.** Behalten Sie jede Copyright-Zeile
   und jeden Autorennamen. Fügen Sie Ihren daneben hinzu.
3. **Geben Sie zurück, was anderen hilft.** Eine Korrektur, die Sie zurückschicken, müssen Sie nicht mehr allein
   pflegen, und die nächste Person muss den Fehler nicht noch einmal finden.

**Übertragung des Urheberrechts (CLA).** 7x und die Exponential Foundation übernehmen einen Code-Beitrag zum
Kern-Kernel (`se7enxweb/exponential`), der in beliebiger Form eingereicht wird (Pull Request, Patch, Issue oder E-Mail),
erst, nachdem Sie das Contributor Licensing Agreement unterzeichnet haben. **Mit der Unterzeichnung und der Einreichung
übertragen Sie das Urheberrecht an Ihrem Beitrag auf 7x, das damit sein Inhaber wird, und geben die Kontrolle darüber
auf:** 7x darf ihn auf jede Weise verwenden, ändern, neu lizenzieren und verbreiten, auch unter anderen Lizenzen und in
kommerziellen Produkten, ohne Sie zu fragen oder zu bezahlen, und Sie können ihn weder zurückziehen noch Bedingungen
daran knüpfen. Den Kernel aus Ihrer eigenen Erweiterung heraus zu erweitern, eine Erweiterung zu forken oder Ihre eigene
Arbeit zu veröffentlichen, ist davon nicht berührt. Siehe
[Copyright assignment (CLA)](../../../../../CONTRIBUTING.md#copyright-assignment-cla) (auf Englisch).

## Keine Rechtsberatung

Dieser Teil erklärt Lizenzen so, wie Entwickler sie im Alltag verstehen müssen. Er ist keine Rechtsberatung. Wenn es
um Geld, einen Kundenvertrag oder einen Streit geht, fragen Sie jemanden mit entsprechender Qualifikation.

## Verwandte Seiten

- Zurück: [Fallstudie: kleine PHP-8-Korrekturen in vielen Erweiterungen](../../../../eng-us/help/extension-development/part-4-upgrading/case-study-small-fixes-across-many-extensions.md)
- Weiter: [Erweitern, ohne zu forken](erweitern-ohne-zu-forken.md)
- [Erweiterungen zurückgeben](../../erweiterungen-zurueckgeben.md)
- [Lizenzen lesen, bevor Sie etwas wiederverwenden](../teil-1-vom-benutzer-zum-entwickler/lizenzen-vor-der-wiederverwendung-lesen.md)
- [Erweiterungsentwicklung: das Buch](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
