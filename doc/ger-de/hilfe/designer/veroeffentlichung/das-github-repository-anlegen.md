---
title: "Das GitHub-Repository anlegen"
audience: designer
category: theme-distribution
order: 30
summary: Ein öffentliches GitHub-Repository für ein Theme mit der Kommandozeile gh oder im Browser anlegen, Ihr lokales Repository verbinden und Beschreibung, Website, Issues und Lizenz so setzen, dass GitHub sie anzeigt.
keywords: GitHub, gh repo create, Repository, Remote, origin, öffentliches Repository, Issues, Homepage, Lizenzerkennung, public repository, homepage, license detection
type: help
---
# Das GitHub-Repository anlegen

Diese Seite ist für Designer, die ein Theme auf ihrem Rechner unter Git haben und es auf GitHub bringen wollen.
Andere Git-Hosts funktionieren ganz ähnlich; die Befehle unten verwenden die GitHub-Kommandozeile `gh`.

## Bevor Sie beginnen

- Das Theme ist ein Git-Repository, dessen Wurzel der Erweiterungsordner ist ([Ein Theme unter Git stellen](../erweiterungen/ein-theme-unter-git-stellen.md)).
- `gh auth status` meldet, dass Sie bei github.com angemeldet sind.
- `LICENSE` und `README.md` sind committet.

## Schritt für Schritt mit gh

1. Legen Sie im Ordner des Themes das Repository an und pushen Sie in einem Zug:

   ```bash
   gh repo create yourvendor/mytheme --public --source . --remote origin --push \
       --description "A responsive theme for Exponential 6 sites"
   ```

   Erwartet: die URL des Repositorys, dann die Ausgabe des Pushs.
2. Setzen Sie Website und Topics (siehe [Repository-Name, Beschreibung und Topics](repository-name-beschreibung-und-topics.md)):

   ```bash
   gh repo edit yourvendor/mytheme --homepage https://packagist.org/packages/yourvendor/mytheme \
       --add-topic exponential --add-topic theme --add-topic responsive-design
   ```

3. Prüfen Sie, was GitHub erkannt hat:

   ```bash
   gh repo view yourvendor/mytheme --json licenseInfo,hasIssuesEnabled,homepageUrl
   ```

   Erwartet: Ihre Lizenz (für die GPL Version 2 meldet GitHub `GPL-2.0`), aktivierte Issues, die Homepage.

## Im Browser

1. Auf github.com **New repository**: Eigentümer, Name (der Name der Erweiterung), Beschreibung, **Public**. Lassen
   Sie GitHub kein README und keine Lizenz hinzufügen; Sie haben beides schon.
2. Kopieren Sie die URL des Repositorys und verbinden Sie Ihr lokales Repository:

   ```bash
   git remote add origin https://github.com/yourvendor/mytheme.git
   git push -u origin main
   ```

3. Prüfen Sie unter **Settings**, dass **Issues** eingeschaltet ist; fügen Sie über das Zahnrad in der Seitenleiste
   Website und Topics hinzu.

## Bewährte Praxis

- Verwenden Sie für ein Theme, das ein Team oder eine Firma betreut, ein Organisationskonto, damit es nicht von einer
  Person abhängt.
- Schalten Sie für jedes Konto mit Schreibzugriff die Zwei-Faktor-Authentifizierung ein.
- Fügen Sie vor dem ersten Release `SECURITY.md` hinzu ([Eine Sicherheitsrichtlinie für ein Theme](sicherheitsrichtlinie-fuer-ein-theme.md)).

## Verwandte Seiten

- [Der erste Push und der Standard-Branch](erster-push-und-standard-branch.md)
- [Mit Git entwickeln](../../mit-git-entwickeln.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
