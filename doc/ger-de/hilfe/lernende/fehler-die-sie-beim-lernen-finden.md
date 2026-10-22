---
title: Fehler, die Sie beim Lernen finden
audience: student
category: learning
order: 41
summary: Wie Lernende einen Fehler von einem Missverständnis unterscheiden, den richtigen Tracker wählen (die Software, Velocity, eine Erweiterung oder den Issue-Tracker der Community), mit der Seite Fehler melden ein Issue schreiben, das andere nachvollziehen können, Sicherheitsprobleme vertraulich behandeln und das Issue weiterverfolgen.
keywords: fehler, bug, issue, issue-tracker, issue-tracker der community, exponential-community, Fehler melden, nachvollziehen, sicherheit, SECURITY.md, GitHub, lernende, issue tracker, community issue tracker, Report an error, reproduce, security, learner
type: help
---
# Fehler, die Sie beim Lernen finden

Beim Lernen werden Sie auf Dinge stoßen, die nicht funktionieren. Manche sind Ihre eigenen Fehler, manche die der
Software. Ein klares Issue zu einem echten Fehler hilft allen Benutzern nach Ihnen, und es ist ein Beitrag, den Sie
vorzeigen können. Diese Seite sagt Ihnen, wie es geht.

## Inhalt

1. [Fehler oder Missverständnis?](#fehler-oder-missverständnis)
2. [Den richtigen Tracker wählen](#den-richtigen-tracker-wählen)
3. [Ein Issue schreiben, das andere nachvollziehen können](#ein-issue-schreiben-das-andere-nachvollziehen-können)
4. [Sicherheitsprobleme bleiben vertraulich](#sicherheitsprobleme-bleiben-vertraulich)
5. [Nachdem Sie es gemeldet haben](#nachdem-sie-es-gemeldet-haben)

## Fehler oder Missverständnis?

Bevor Sie ein Issue anlegen, schließen Sie auf einer Lernwebsite die üblichen Ursachen aus:

| Prüfung | Seite |
|---|---|
| Ist es eine Seite zu fehlenden Rechten und kein Fehler? | [Logs und Fehler lesen](logs-und-fehler-lesen.md#ist-es-überhaupt-ein-fehler) |
| Liegt es an einem Cache? | [Caches verstehen](caches-verstehen.md#cache-problem-oder-echter-fehler) |
| Steht die Einstellung in der richtigen Datei und im richtigen Siteaccess? | [Siteaccesses und Einstellungen verstehen](siteaccesses-und-einstellungen-verstehen.md#häufige-fehler) |
| Tritt es auch bei einer frischen Installation auf? | [Die Lernwebsite zurücksetzen und neu aufbauen](die-lernwebsite-zuruecksetzen-und-neu-aufbauen.md) |
| Ist es bereits bekannt? | Durchsuchen Sie zuerst den Tracker |
| Ist es Absicht? | Manches Verhalten, das falsch aussieht, ist gewollt oder Auslegungssache, und andere Websites verlassen sich darauf; eine Änderung braucht eine klare Begründung und oft mehrere Runden der Begutachtung. Siehe [Negative Funktionen](../projekt/unterstuetzung-erhalten.md#negative-funktionen) |

Wenn es mit den dokumentierten Schritten auch bei einer frischen Installation auftritt, ist es wahrscheinlich ein
Fehler. Wenn eine Hilfeseite Ihnen etwas sagt, was die Software nicht tut, ist das ein Fehler der Dokumentation:
Melden Sie auch ihn, oder korrigieren Sie ihn ([Die Hilfe als Lernende verbessern](die-hilfe-als-lernende-verbessern.md)).

## Den richtigen Tracker wählen

Nach [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md#einen-fehler-in-exponential-melden) und
[Welcher Tracker für welchen Bericht](../fuersprecher/welcher-tracker-fuer-welche-meldung.md#die-entscheidungstabelle):

| Es geht um | Wohin |
|---|---|
| Den Kernel, die Administrationsoberfläche, eine mitgelieferte Erweiterung oder die Dokumentation | [Exponential-Issues](https://github.com/se7enxweb/exponential/issues) |
| Den Velocity-Server: Starten, Worker, sein Antwort-Cache, HTTPS | [Velocity-Issues](https://github.com/se7enxweb/exponential-velocity/issues) |
| Eine Erweiterung mit eigenem Repository | Die Issues dieses Repositorys |
| Das Projekt, die Website, das Ökosystem, alles, was kein Softwarefehler ist | Der [Issue-Tracker der Community](https://github.com/se7enxweb/exponential-community/issues) |
| Eine Frage der Art „Wie mache ich …?“ | Die Foren oder Telegram ([Um Hilfe bitten und Lerngruppen](um-hilfe-bitten-und-lerngruppen.md)) |
| Ein Sicherheitsproblem | **Vertraulich**, siehe unten |

Um ein Issue zu eröffnen, brauchen Sie ein kostenloses GitHub-Konto.

## Ein Issue schreiben, das andere nachvollziehen können

Die Seite **Fehler melden** der Hilfe (`exphelp/report` in der Administrationsoberfläche) wählt den Tracker, füllt
eine Zusammenfassung der Installation aus und öffnet ein neues Issue mit vorbereitetem Titel und Text
([Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md#einen-fehler-in-exponential-melden)). Auf der Kommandozeile liefert
`php bin/php/console exp:info --plain --no-logo` eine Zusammenfassung, die niemals ein Passwort, einen Schlüssel, ein
Token oder eine Datenbankadresse ausgibt.

Ein gutes Issue enthält ([Eine gute Fehlerbeschreibung](../wenn-etwas-schiefgeht.md#eine-gute-fehlerbeschreibung)):

```text
Title:     <short, names the problem: "Upload page: ZIP file with a folder fails with temp_dir">

What I did:
  1. <step>
  2. <step>
What I expected:  <...>
What happened:    <..., with the exact message>
Environment:      <Exponential version, PHP version, web server, database, administration design>
Fresh install?    <yes / no>
```

Screenshots helfen, wenn sie keine Passwörter und keine personenbezogenen Daten zeigen. Sagen Sie, dass Sie Lernende
sind, damit andere wissen, wie ausführlich sie antworten sollten.

## Sicherheitsprobleme bleiben vertraulich

Wenn Sie einen Weg finden, etwas zu sehen oder zu ändern, was Sie nicht dürften, ist das ein Sicherheitsproblem. Es
gehört niemals in ein öffentliches Issue, ein Forum oder einen Chat. Folgen Sie `SECURITY.md` im Wurzelverzeichnis des
Kernels ([Sicherheitsrichtlinie](../../../../SECURITY.md)); die Entscheidungstabelle der Fürsprecher nennt die Adresse
security@se7enx.com und die vertrauliche Meldung von Sicherheitslücken auf GitHub
([Welcher Tracker für welchen Bericht](../fuersprecher/welcher-tracker-fuer-welche-meldung.md#die-entscheidungstabelle)).

## Nachdem Sie es gemeldet haben

- Beobachten Sie das Issue und beantworten Sie Rückfragen zügig.
- Wenn jemand Sie bittet, eine Korrektur zu testen, testen Sie sie auf Ihrer Lernwebsite und sagen Sie, was Sie
  gesehen haben.
- Wenn Sie die Ursache selbst finden, dürfen Sie die Korrektur einsenden ([Ihr erster Beitrag, Schritt für Schritt](ihr-erster-beitrag-schritt-fuer-schritt.md)).
- Notieren Sie das Issue in Ihrem [Lerntagebuch](ein-lerntagebuch-fuehren.md) und, sobald es gelöst ist, in Ihrem
  Portfolio.

## Verwandte Seiten

- [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)
- [Testen und Fehler melden](../unterstuetzer/testen-und-fehler-melden.md)
- [Als Lernende beitragen](als-lernende-beitragen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
