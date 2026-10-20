---
title: Ideen auf GitHub finden
audience: user, developer, designer, app-creator
category: extension-development
order: 90
summary: Auf GitHub nach Erweiterungen und funktionierendem Code suchen - die Organisation se7enxweb, Codesuche nach Kernel-Klassennamen, Historie und Issues, und ob ein Projekt lebt.
keywords: GitHub, Suche, Codesuche, se7enxweb, Repositories, Forks, archiviert, Issues, Pull Requests, Historie, Ideen, Beispiele, github, search, code search, repositories, forks, archived, issues, pull requests, history, ideas, examples, gh cli
type: help
---
# Ideen auf GitHub finden

Teil 1, Kapitel 9. Fast jede Erweiterung, die je für diese Codebasis geschrieben wurde und noch existiert, liegt auf
GitHub, und die Organisation se7enxweb bewahrt einen großen Teil davon auf, als Originale und als Forks. Dieses Kapitel
zeigt, wie Sie dort nach Ideen und funktionierendem Code suchen und wie Sie beurteilen, was Sie finden.

## Die Organisation se7enxweb

[github.com/se7enxweb](https://github.com/se7enxweb) enthält den Kernel, jede Erweiterung, die der Kernel voraussetzt,
die Dokumentations-Websites, die freien Bücher und eine große Zahl von Forks historischer Erweiterungen, die
aufbewahrt werden, damit sie nicht verschwinden. Als diese Seite geschrieben wurde (Oktober 2026), führte die
Organisation mehr als tausend Repositories; weniger als hundert davon waren eigene Repositories der Organisation, der
Rest Forks.

| Art | Woran Sie sie erkennen | Wofür Sie sie nutzen |
|---|---|---|
| Gepflegte Erweiterung | nicht archiviert, aktuelle Commits, ein Release-Tag, ein Packagist-Paket | installieren und darauf aufbauen |
| Aufbewahrter Fork | als Fork gekennzeichnet, oft archiviert | als Ideenquelle lesen; die Lizenz prüfen; vor der Verwendung auf den neuesten Stand bringen (Teil 4) |
| Dokumentation und Bücher | Namen wie `doc.exponential.earth`, `Book-...` | lernen |
| Distributionen und Grundgerüste | Namen wie `exponential-platform-...` | sehen, wie vollständige Installationen zusammengesetzt sind |

Auf einer Repository-Seite bedeutet **Archived** schreibgeschützt: Dort wird niemand Ihre Korrektur übernehmen. Ein Fork
zeigt „forked from“ und den ursprünglichen Eigentümer; das Original ist der Ort, an dem die Historie begann.

## Im Browser suchen

Die Suche von GitHub versteht Qualifikatoren. Einige nützliche Suchen (geben Sie sie in das Suchfeld auf github.com
ein):

| Sie suchen | Suche |
|---|---|
| Repositories der Organisation zum Thema Tags | `org:se7enxweb tags` |
| Für diese Codebasis geschriebene Datentypen | `"extends eZDataType" language:PHP` |
| Template-Operatoren | `"eZTemplateOperatorArray" language:PHP` |
| Workflow-Ereignistypen | `"extends eZWorkflowEventType" language:PHP` |
| Cronjob-Teile | `"[CronjobPart-" path:settings` |
| Ajax-Funktionen von ezjscore | `"extends ezjscServerFunctions"` |
| Verwendungen einer Kernel-Methode | `"eZContentFunctions::createAndPublishObject"` |

Für die Codesuche müssen Sie angemeldet sein. Eine Suche nach einem Kernel-Klassennamen ist der schnellste Weg zu
echtem Code, der sie verwendet, und der ist oft klarer als jede Beschreibung.

## Im Terminal suchen

Das Kommandozeilenwerkzeug `gh` von GitHub tut dasselbe in einem Terminal:

```bash
gh search repos --owner se7enxweb tags --limit 20
gh search code "extends eZDataType" --language php --limit 20
gh repo view se7enxweb/eztags
gh api repos/se7enxweb/eztags/releases --jq '.[0].tag_name'
```

`gh repo view` gibt die Beschreibung und die README aus; der letzte Befehl gibt das neueste Release-Tag aus.

## Lesen, bevor Sie etwas übernehmen

Wenn Sie etwas Vielversprechendes finden, lesen Sie Folgendes, bevor Sie auch nur eine Zeile kopieren:

1. **Die Lizenz.** Die Repository-Seite zeigt die erkannte Lizenz in ihrer Seitenleiste; maßgeblich ist die Datei
   `LICENSE`. Keine Lizenz bedeutet keine Erlaubnis zum Kopieren. Siehe
   [Lizenzen lesen, bevor Sie etwas wiederverwenden](lizenzen-vor-der-wiederverwendung-lesen.md).
2. **Die Historie.** `Commits` auf der Repository-Seite oder nach einem Klon:

   ```bash
   git clone https://github.com/se7enxweb/<repo>.git
   git -C <repo> log --format='%ad %s' --date=short | head -20
   ```

   Eine lange, stetige Historie mit klaren Nachrichten ist ein gutes Zeichen. Ein einziger Commit „initial import“
   bedeutet, dass die Historie verloren ging; der Code kann trotzdem in Ordnung sein, aber Sie wissen weniger über ihn.
3. **Die Issues und Pull Requests.** Offene Issues zeigen bekannte Probleme; übernommene Pull Requests zeigen, dass
   jemand Beiträge prüft.
4. **Für welche Version sie geschrieben wurde.** `require.php` in `composer.json`, die README, `extension.xml`. Code,
   der für PHP 5 geschrieben wurde, braucht die Änderungen aus [Teil 4](../../../../eng-us/help/extension-development/part-4-upgrading/README.md), bevor er unter
   Exponential 6 läuft.

## Lebt das Projekt?

| Zeichen | Lebt | Braucht einen neuen Maintainer |
|---|---|---|
| Letzter Commit | dieses oder letztes Jahr | vor vielen Jahren |
| Archiviert | nein | ja |
| Release-Tags | ja, nach einem Muster | keine |
| Packagist-Paket | ja, die Versionen passen zu den Tags | keines oder bei einer alten Version stehen geblieben |
| Issues | beantwortet | seit Jahren unbeantwortet |
| PHP-Anforderung in `composer.json` | 8.x | 5.x oder fehlt |

Eine Erweiterung, die „einen neuen Maintainer braucht“, ist nicht nutzlos; sie ist eine Gelegenheit.
[Eine verwaiste Erweiterung übernehmen](../teil-5-erweitern-forken-zurueckgeben/eine-verwaiste-erweiterung-uebernehmen.md) erklärt,
wie Sie eine respektvoll übernehmen.

## Forken oder nur lesen?

Für Ideen genügt das Lesen. Wenn Sie den Code ändern und die Änderung teilen möchten, forken Sie ihn auf GitHub und
arbeiten Sie auf einem Branch; [Auf GitHub forken, Schritt für Schritt](../teil-5-erweitern-forken-zurueckgeben/auf-github-forken.md)
zeigt, wie das geht, und
[Mit Pull Requests beitragen](../teil-5-erweitern-forken-zurueckgeben/mit-pull-requests-zurueckgeben.md), wie Sie die
Änderung ihrem Autor anbieten.

## Übung

1. Suchen Sie `org:se7enxweb datatype` und öffnen Sie drei Ergebnisse.
2. Notieren Sie für jedes: Lizenz, Datum des letzten Commits, archiviert oder nicht, Packagist-Paket oder nicht.
3. Wählen Sie dasjenige, das am lebendigsten wirkt, und lesen Sie seine Datentypklasse. Denselben Methoden begegnen Sie
   in [Datentypen](../teil-3-erweiterungsentwickler/datentypen.md).

## Verwandte Seiten

- Zurück: [Der Erweiterungskatalog von se7enxweb](der-se7enxweb-erweiterungskatalog.md)
- Weiter: [Ideen auf Packagist finden](ideen-auf-packagist-finden.md)
- [Mit Git entwickeln](../../mit-git-entwickeln.md)
- [GitHub Docs: Code durchsuchen](https://docs.github.com/en/search-github/searching-on-github/searching-code) (auf Englisch)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
