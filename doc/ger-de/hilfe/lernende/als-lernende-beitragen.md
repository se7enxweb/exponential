---
title: Als Lernende beitragen
audience: student
category: learning
order: 39
summary: Warum und wie Lernende zu Exponential beitragen - die Arten von Beiträgen, die zu jedem Lernpfad passen, was Sie brauchen, die Regeln aus CONTRIBUTING.md und dem Verhaltenskodex, wie die Begutachtung abläuft, die CLA für Kernel-Code und wie Beiträge Teil Ihres Portfolios werden.
keywords: beitragen, beitrag, lernende, open source, pull request, dokumentation, übersetzung, fehlerbericht, code, begutachtung, review, CONTRIBUTING.md, verhaltenskodex, CLA, portfolio, contribute, contribution, student, documentation, translation, bug report, code of conduct
type: help
---
# Als Lernende beitragen

Exponential ist freie Software, die offen entwickelt wird, und Beiträge von Lernenden sind willkommen. Ein Beitrag
lehrt Sie mehr als eine Übung, weil echte Menschen ihn begutachten und echte Benutzer davon profitieren. „Echte
Beiträge sind das beste Portfolio, das Studierende haben können: Ein gemergter Pull Request ist ein öffentlicher
Nachweis von Können“ ([Partner für Studierende und Bildung](../partner/partner-fuer-studierende-und-bildung.md#ideen-für-kurse-und-projekte)).

## Inhalt

1. [Arten von Beiträgen](#arten-von-beiträgen)
2. [Was Sie brauchen](#was-sie-brauchen)
3. [Die Regeln](#die-regeln)
4. [Wie die Begutachtung abläuft](#wie-die-begutachtung-abläuft)
5. [Urheberrecht und Lizenzen](#urheberrecht-und-lizenzen)
6. [Wo Sie anfangen](#wo-sie-anfangen)

## Arten von Beiträgen

| Beitrag | Braucht | Passt zum Pfad | Seite |
|---|---|---|---|
| Einen Fehler gut melden | Ein GitHub-Konto, eine Lernwebsite | Alle | [Fehler, die Sie beim Lernen finden](fehler-die-sie-beim-lernen-finden.md) |
| Einen Tippfehler oder einen veralteten Schritt in der Hilfe korrigieren | Ein GitHub-Konto | Alle | [Die Hilfe als Lernende verbessern](die-hilfe-als-lernende-verbessern.md) |
| Texte der Oberfläche übersetzen | Eine Sprache, die Sie gut schreiben | Redakteur, alle | [Als Lernende übersetzen](als-lernende-uebersetzen.md) |
| Eine Frage in der Community beantworten | Was Sie gelernt haben | Alle | [Um Hilfe bitten und Lerngruppen](um-hilfe-bitten-und-lerngruppen.md) |
| Einen Test schreiben, der einen Fehler nachstellt | PHP, Git | Entwickler | [Code beitragen](../unterstuetzer/code-beitragen.md) |
| Einen kleinen Fehler beheben | PHP, Git | Entwickler | [Ihr erster Beitrag, Schritt für Schritt](ihr-erster-beitrag-schritt-fuer-schritt.md) |
| Eine eigene Erweiterung oder ein eigenes Theme veröffentlichen | Ihr eigenes Projekt | Entwickler, Designer | [Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md) |
| Ein Site-Paket beitragen | Eine Website, die es wert ist, geteilt zu werden | Administrator, Designer | [Ihr Site-Paket beitragen](../seitenpakete/ihr-seitenpaket-beitragen.md) |

Der Bereich für Unterstützer nennt weitere Wege zu helfen ([Unterstützer: hier beginnen](../unterstuetzer/unterstuetzer-hier-beginnen.md)).

## Was Sie brauchen

- Ein kostenloses **GitHub-Konto**. Sie brauchen es, um ein Issue oder einen Pull Request zu eröffnen
  ([Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md#einen-fehler-in-exponential-melden)).
- Für Code: Git und eine Lerninstallation aus Git-Klonen
  ([Aus Git-Klonen zum Lernen installieren](aus-git-klonen-zum-lernen-installieren.md)).
- Zeit, um auf Anmerkungen aus der Begutachtung zu antworten.

## Die Regeln

Aus `CONTRIBUTING.md` im Wurzelverzeichnis des Kernels ([Richtlinien für Mitwirkende](../../../../CONTRIBUTING.md)):

- Lesen Sie zuerst den [Verhaltenskodex](../../../../CODE_OF_CONDUCT.md); von allen Mitwirkenden wird erwartet, dass
  sie ihn einhalten.
- Arbeiten Sie immer auf einem neuen Branch; halten Sie jeden Pull Request fokussiert, eine Änderung pro Pull Request.
- Commit-Betreffzeilen beginnen mit dem, was die Änderung tut: `Added: `, `Updated: `, `Fixed: `, `Removed: ` oder
  `Renamed: `.
- Folgen Sie den [Coding-Standards](../../standards/programmierstandards.md) und dem Stil der Dateien rund um Ihre
  Änderung.
- Halten Sie das Dateimanifest `share/filelist.md5` aktuell (`make manifest-fix`); `make hooks` installiert einen
  Pre-Commit-Hook, der es, die PHP-Syntax und den Codestil prüft.
- Fügen Sie Tests hinzu, wo Sie können, und aktualisieren Sie bei Bedarf die Dokumentation.

## Wie die Begutachtung abläuft

`CONTRIBUTING.md` warnt: „Stellen Sie sich auf viele Runden der Begutachtung ein. 7x führt deutlich mehr
Begutachtungsrunden als üblich durch, bevor es einen Commit oder Pull Request annimmt, manchmal Hunderte von
Überarbeitungen … das ist kein Urteil über Sie.“

- Beantworten Sie jede Anmerkung im Pull Request, mit einer Änderung oder einer Begründung.
- Wenn ein Gutachter sagt, das Verhalten, das Sie geändert haben, sei eine
  [negative Funktion](../projekt/unterstuetzung-erhalten.md#negative-funktionen), also gewollt oder Auslegungssache, und andere
  Websites verlassen sich darauf, dann erklären Sie Ihren Anwendungsfall und bieten Sie die Änderung als Option statt
  als neue Voreinstellung an.
- Fassen Sie Ihren Pull Request mit Squash zusammen und rebasen Sie ihn auf `main`, solange er offen ist, wie 7x es
  verlangt; einen Force-Push machen Sie nur auf den Branch Ihres eigenen Pull Requests
  ([Mit Git entwickeln](../mit-git-entwickeln.md#eine-änderung-per-pull-request-beitragen)).
- Jede Begutachtungsrunde ist eine kostenlose Lektion von erfahrenen Entwicklern. Notieren Sie, was Sie gelernt haben,
  in Ihrem [Lerntagebuch](ein-lerntagebuch-fuehren.md).

## Urheberrecht und Lizenzen

- Codebeiträge stehen unter der GNU GPL Version 2 oder später, wie der Rest von Exponential.
- Dokumentation ist freie Dokumentation unter der GNU FDL 1.3 oder später ([Freie Dokumentation](../freie-dokumentation.md)).
- Ein Codebeitrag zum Kernel wird erst gemergt, wenn sein Autor das Contributor Licensing Agreement unterzeichnet hat,
  mit dem der Autor das Urheberrecht an 7x überträgt. Lesen Sie [Die CLA für Lernende](die-cla-fuer-lernende.md), bevor
  Sie Kernel-Code einsenden. Ihre eigenen Erweiterungen sind davon nicht betroffen.

## Wo Sie anfangen

1. Wählen Sie die kleinste echte Aufgabe: einen Tippfehler, den Sie gefunden haben, einen Schritt, der bei Ihnen nicht
   funktioniert hat, eine Zeichenkette in Ihrer Sprache.
2. Folgen Sie [Ihr erster Beitrag, Schritt für Schritt](ihr-erster-beitrag-schritt-fuer-schritt.md).
3. Fügen Sie den Link Ihrem Portfolio hinzu, sobald er gemergt ist ([Ein Portfolio aufbauen](ein-portfolio-aufbauen.md)).

Der Leitfaden der Fürsprecher [Einen ersten Beitrag begleiten](../fuersprecher/einen-ersten-beitrag-begleiten.md) ist für
Menschen geschrieben, die Ihnen helfen; wenn Sie ihn lesen, sehen Sie, wie eine gute erste Aufgabe aussieht.

## Verwandte Seiten

- [Ihr erster Beitrag, Schritt für Schritt](ihr-erster-beitrag-schritt-fuer-schritt.md)
- [Die CLA für Lernende](die-cla-fuer-lernende.md)
- [Zum Projekt beitragen](../projekt/zum-projekt-beitragen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
