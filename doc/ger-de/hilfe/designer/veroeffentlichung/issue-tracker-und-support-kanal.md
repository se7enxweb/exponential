---
title: "Issue-Tracker und Support-Kanal"
audience: designer
category: theme-distribution
order: 380
summary: Nutzern sagen, wo sie fragen und wo sie melden - GitHub-Issues für Fehler, ein Forum oder Chat für Fragen, eine vertrauliche Adresse für Sicherheitsprobleme - mit den support-Feldern in composer.json, Issue-Vorlagen und Gewohnheiten beim Antworten.
keywords: Issue-Tracker, GitHub-Issues, Support-Kanal, Forum, Composer support, Issue-Vorlagen, SUPPORT.md, Antwort, issue tracker, GitHub issues, support channel, issue templates, response
type: help
---
# Issue-Tracker und Support-Kanal

Diese Seite ist für Designer, die ein Theme veröffentlichen und möchten, dass Probleme an einem Ort gemeldet werden statt
per privater Mail.

## Ein Ort für jede Art von Nachricht

| Art | Wo | Beispiel |
|---|---|---|
| Fehler und Funktionswünsche | Die Issues des Repositorys | `https://github.com/se7enxweb/cjw_themes_jumper/issues` |
| Fragen und Hilfe | Ein Forum oder Chat | Das Forum **Extensions** oder **Setup & design** auf share.exponential.earth |
| Sicherheitsprobleme | Eine vertrauliche Adresse, nie ein öffentliches Issue | [Eine Sicherheitsrichtlinie für ein Theme](sicherheitsrichtlinie-fuer-ein-theme.md) |
| Bezahlter Support | Ihre eigene Kontaktseite | [Support-Angebote](../verkauf/support-angebote.md) |

Die Organisation `se7enxweb` führt in ihrem Repository `.github` eine `SUPPORT.md`, die kommerziellen Support vom
Community-Support trennt; sie ist ein nützliches Vorbild.

## Schritt für Schritt

1. Aktivieren Sie Issues für das Repository (`gh repo edit yourvendor/mytheme --enable-issues`).
2. Fügen Sie `composer.json` die support-Felder hinzu:

   ```json
   "support": {
       "issues": "https://github.com/yourvendor/mytheme/issues",
       "source": "https://github.com/yourvendor/mytheme"
   }
   ```

3. Fügen Sie Issue-Vorlagen hinzu (`.github/ISSUE_TEMPLATE/bug_report.md`), die nach der Version des Themes, der
   Version von Exponential, der PHP-Version, der Server-Engine, den Schritten und einem Screenshot fragen und daran
   erinnern, keine Passwörter oder personenbezogenen Daten zu posten.
4. Schreiben Sie im README einen kurzen Abschnitt „Support“ mit der Tabelle oben.
5. Entscheiden Sie, wie oft Sie nach neuen Issues sehen, und sagen Sie es ehrlich („Ich lese Issues wöchentlich“).

## Gewohnheiten, die helfen

- Versehen Sie Issues mit Labels (`bug`, `question`, `accessibility`, `security`).
- Schließen Sie ein Issue mit einem Link zum Release, das es behoben hat.
- Danken Sie den Meldenden; nennen Sie sie mit ihrer Einwilligung im Changelog.

## Verwandte Seiten

- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Sicherheitsmeldungen bearbeiten](../lebenszyklus/sicherheitsmeldungen-bearbeiten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
