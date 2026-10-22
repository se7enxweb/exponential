---
title: Ihr erster Beitrag
audience: community, supporter, developer, editor
category: community
order: 22
summary: Drei kleine erste Beiträge zu Exponential, Schritt für Schritt - eine über „Auf GitHub bearbeiten“ korrigierte Hilfeseite, ein gut gemachter Fehlerbericht über die Seite „Fehler melden“ und ein erster Pull Request mit der richtigen Commit-Nachricht und Manifestzeile.
keywords: erster Beitrag, Auf GitHub bearbeiten, Pull Request, Fehlerbericht, Fehler melden, Commit-Nachricht, Manifest, filelist.md5, Fork, Neuling, first contribution, Edit on GitHub, bug report, Report an error, commit message, manifest, newcomer
type: help
---
# Ihr erster Beitrag

Wählen Sie einen der drei. Jeder dauert beim ersten Mal weniger als eine Stunde.

## Inhalt

1. [Möglichkeit A: eine Hilfeseite korrigieren](#möglichkeit-a-eine-hilfeseite-korrigieren)
2. [Möglichkeit B: einen Fehler melden](#möglichkeit-b-einen-fehler-melden)
3. [Möglichkeit C: Ihr erster Pull Request mit Code](#möglichkeit-c-ihr-erster-pull-request-mit-code)
4. [Nach Ihrem ersten Beitrag](#nach-ihrem-ersten-beitrag)

## Möglichkeit A: eine Hilfeseite korrigieren

1. Legen Sie ein kostenloses GitHub-Konto an, falls Sie keines haben.
2. Folgen Sie auf der Hilfeseite mit dem Fehler dem Link **Auf GitHub bearbeiten**.
3. GitHub bietet an, das Repository zu forken; nehmen Sie an. Korrigieren Sie den Text im Browser.
4. Schreiben Sie eine kurze Commit-Nachricht, die mit dem beginnt, was die Änderung tut, zum Beispiel
   `Updated: The help page on searching names the search filters correctly` (die Präfixe sind `Added:`,
   `Updated:`, `Fixed:`, `Removed:`, `Renamed:`; `CONTRIBUTING.md`, „Making Changes“).
5. Schlagen Sie die Änderung als Pull Request vor und beschreiben Sie in einem Satz, was falsch war.

Das Repository führt ein Manifest aller Dateien, `share/filelist.md5`, und die CI prüft es. Schlägt die Prüfung bei
Ihrem Pull Request fehl, „zeigt“ ihre Zusammenfassung „die zu ändernden Zeilen“ (`CONTRIBUTING.md`, „Submitting
Changes“); eine prüfende Person kann helfen.

## Möglichkeit B: einen Fehler melden

1. Öffnen Sie **Fehler melden** in der Hilfe Ihrer Administration (`exphelp/report`). Die Seite „füllt eine
   Zusammenfassung der Installation für Sie aus und öffnet ein neues Issue mit vorbereitetem Titel und Text“
   ([Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)).
2. Durchsuchen Sie zuerst den Tracker; ist der Fehler bekannt, ergänzen Sie das vorhandene Issue um das, was Sie
   wissen.
3. Schreiben Sie die Schritte auf, was Sie erwartet haben und was geschehen ist. Entfernen Sie alles Private.
4. Beobachten Sie das Issue und beantworten Sie Fragen der Maintainer.

Manches Verhalten, das wie ein Fehler aussieht, ist gewollt oder auslegungsfähig, und andere Websites hängen davon ab.
Wenn ein Maintainer es eine [negative Funktion](../../projekt/unterstuetzung-erhalten.md#negative-funktionen) nennt, ist die
Diskussion nicht beendet: Erklären Sie Ihren Anwendungsfall und rechnen Sie damit, dass eine Änderung als Option und
erst nach sorgfältigem Review kommt.

[Einen Fehler melden](einen-fehler-melden.md) enthält die Einzelheiten.

## Möglichkeit C: Ihr erster Pull Request mit Code

1. Forken Sie [se7enxweb/exponential](https://github.com/se7enxweb/exponential) und klonen Sie Ihren Fork.
2. Führen Sie `composer install` aus und richten Sie eine Testinstallation ein ([Mit Git entwickeln](../../mit-git-entwickeln.md)).
3. Führen Sie einmal `make hooks` aus: Die Hooks prüfen die Commit-Nachricht, die PHP-Syntax, den Code-Stil und das
   Manifest (`CONTRIBUTING.md`, „Submitting Changes“).
4. Legen Sie einen Branch an, zum Beispiel `git checkout -b fix/typo-in-setup-message`.
5. Nehmen Sie eine kleine Änderung vor, testen Sie sie und committen Sie mit einer Nachricht mit Präfix.
6. Aktualisieren Sie das Manifest mit `make manifest-fix`, wenn Ihre Änderung eine Datei hinzufügt, ändert oder
   entfernt.
7. Pushen Sie in Ihren Fork und eröffnen Sie einen Pull Request. Kreuzen Sie das CLA-Kästchen der Vorlage an, sobald
   Ihr unterschriebenes CLA vorliegt ([Das CLA erklärt](das-cla-erklaert.md)).

Gute erste Änderungen: ein falscher Meldungstext, ein fehlender Übersetzungstext, ein defekter Link in einem Template,
ein Test für einen Fehler, der bereits behoben ist.

## Nach Ihrem ersten Beitrag

- Rechnen Sie mit Fragen und mehreren Review-Runden; das ist normal ([Reviews geben und erhalten](reviews-geben-und-erhalten.md)).
- Notieren Sie, was Sie getan haben ([Ihre Beiträge festhalten](../../fuersprecher/ihre-beitraege-festhalten.md)).
- Leisten Sie einen zweiten Beitrag im selben Bereich; durch Wiederholung werden Menschen zu vertrauenswürdigen
  Mitwirkenden ([Vom Nutzer zum Mitwirkenden](../lernpfade/vom-nutzer-zum-mitwirkenden.md)).

## Verwandte Seiten

- [Als Community-Mitglied mitwirken](README.md)
- [Einen ersten Beitrag betreuen](../../fuersprecher/einen-ersten-beitrag-begleiten.md)
- [Mit Git entwickeln](../../mit-git-entwickeln.md)
- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
