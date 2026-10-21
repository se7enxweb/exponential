---
title: "Lektion 4.1: Entwickler, schriftlicher Test"
audience: trainer, developer
category: training
order: 41
summary: Ein schriftlicher Test mit vierzehn Fragen für das Ende des Kurses Entwickler, zu Erweiterungen und ihrer Ladereihenfolge, Autoloads, Modulen und Views, Zugriffskontrolle, Templates und Overrides, Git-Klonen mit Composer, der REST-API und dem Veröffentlichen einer Erweiterung, mit einem Lösungsschlüssel, der die Leitfäden zitiert. 30 Minuten für die Teilnehmenden.
keywords: prüfungen und praktische übungen, entwickler, schriftlicher test, erweiterung, ladereihenfolge, autoload, modul, view, zugriffskontrolle, template-sprache, override, release, assessments and practical exercises, developers, written test, extension, ActiveExtensions, module.php, FunctionList, override.ini, wash, Git, Composer, REST API, tag
type: help
---
# Lektion 4.1: Entwickler, schriftlicher Test

Dieser Test schließt den Kurs Entwickler ab. Er prüft das Wissen, das Entwickler brauchen, bevor sie Code für eine
Website schreiben: wie eine Erweiterung eingeschaltet und gefunden wird, wie eine Modul-View geschützt wird, wie ein
Template ausgewählt wird, wie man mit Git-Klonen arbeitet und wie ein Release entsteht, ohne die Installation anderer
zu beschädigen.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung. Die Befehle sind die der Leitfäden;
> prüfen Sie sie mit dem Release, das Sie unterrichten.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 4: Die technischen Kurse prüfen](README.md) |
| Dauer | 45 Minuten (5 Einweisung, 30 Test, 10 Besprechung der Antworten) |
| Niveau | Fortgeschrittene bis Experten |
| Zielgruppe | Teilnehmende am Ende des Kurses Entwickler |
| Voraussetzungen | Der Kurs Entwickler; [Lektion 1.2](../modul-01-pruefungen-gestalten/schriftliche-tests-schreiben.md) für den Trainer |

## Lernziele

Wer diesen Test besteht, hat gezeigt, dass er Folgendes kann:

- erklären, wie eine Erweiterung aktiviert, eingeordnet und mit Klassen, Einstellungen und Templates versehen wird;
- eine Modul-View mit einer Funktion und einer Richtlinie schützen;
- die Suchreihenfolge der Templates und eine Override-Regel erklären;
- erklären, warum Git-Klone die Entwicklung erleichtern und wie Composer so eingestellt wird, dass es sie bevorzugt;
- ein sicheres Release einer Erweiterung beschreiben.

## Hinweise für Trainer

### So führen Sie ihn durch

1. Teilen Sie die Fragen ohne den Schlüssel aus; 30 Minuten.
2. Gehen Sie die Antworten mit [Erweiterungen](../../../../anleitungen/extensions.md) und
   [Templates und Design](../../../../anleitungen/templates-und-design.md) auf dem Projektor durch.

### Häufige Fragen

**Die REST-Frage gehört bei uns nicht zum Stoff.** Lassen Sie Frage 12 weg und senken Sie die Bestehensgrenze um
einen Punkt.

### Stolperfallen

- **Teilnehmende, die aus anderen Frameworks antworten.** „Routen in YAML“ ist hier eine falsche Antwort; dieser
  Kernel verwendet `module.php`. Akzeptieren Sie nur, was die Leitfäden sagen.

## Der Test

1. Welche Einstellung schaltet eine Erweiterung für die ganze Installation ein, und welche für einen Siteaccess?
2. Zwei aktive Erweiterungen überschreiben dasselbe Template. Welche gewinnt?
3. Sie haben eine Klasse im Ordner `classes/` Ihrer Erweiterung hinzugefügt und erhalten „Class not found“. Welcher
   Befehl behebt das?
4. Welche zwei Dateien beschreiben eine Erweiterung für **Setup > Über** und **Setup > Extensions**, und was muss in
   beiden übereinstimmen?
5. Was sagt dem Kernel, dass er in Ihrer Erweiterung nach Modulen suchen soll?
6. Ihre neue View hat keinen Eintrag `'functions'`. Wer kann sie öffnen?
7. In welcher Reihenfolge sucht das Template-System nach `node/view/full.tpl`?
8. Was bedeuten in einer Override-Regel `Source`, `MatchFile` und `Match[...]`?
9. Welcher Befehl prüft die Syntax eines Templates, ohne einen Browser zu öffnen?
10. Warum sollte jeder Wert, den ein Benutzer eingegeben hat, mit `|wash` ausgegeben werden?
11. Nennen Sie zwei Dinge, die ein Git-Klon eines Pakets ermöglicht, ein Release-Archiv aber nicht.
12. Welche Version der REST-API beantwortet Schreibzugriffe (Anlegen, Ändern, Löschen)?
13. Wie wählen Sie die Nummer, bevor Sie ein Release Ihrer Erweiterung taggen?
14. Ein Tag, den Sie gepusht haben, zeigt auf den falschen Commit. Was tun Sie?

<details>
<summary>Antworten</summary>

1. `[ExtensionSettings] ActiveExtensions[]` in `site.ini` für die Installation; `ActiveAccessExtensions[]` für einen
   Siteaccess ([Erweiterungen](../../../../anleitungen/extensions.md#1-wie-extensions-funktionieren-in-einer-minute)).
2. Die früher geladene; mit `ExtensionOrdering=enabled` bestimmen zuerst die Abhängigkeiten in `extension.xml` die
   Reihenfolge ([Erweiterungen](../../../../anleitungen/extensions.md#1-wie-extensions-funktionieren-in-einer-minute)).
3. `php bin/php/ezpgenerateautoloads.php -e`, danach die Caches leeren und PHP-FPM oder Velocity neu laden
   ([Erweiterungen](../../../../anleitungen/extensions.md#8-wenn-es-nicht-funktioniert)).
4. `ezinfo.php` und `extension.xml`; die Version muss in beiden gleich sein
   ([Erweiterungen](../../../../anleitungen/extensions.md#61-die-kleinste-funktionierende-extension-von-hand)).
5. `ExtensionRepositories[]=<extension>` in der Datei `settings/module.ini.append.php` der Erweiterung
   ([Erweiterungen](../../../../anleitungen/extensions.md#61-die-kleinste-funktionierende-extension-von-hand)).
6. Jeder, der die Website erreicht: Eine View ohne Funktion hat keine Zugriffskontrolle
   ([Erweiterungen](../../../../anleitungen/extensions.md#61-die-kleinste-funktionierende-extension-von-hand)).
7. Passende Override-Templates, das Verzeichnis `templates/` des Website-Designs, die zusätzlichen Designs, dann
   `standard` ([Templates und Design](../../../../anleitungen/templates-und-design.md#1-wie-ein-template-gefunden-wird)).
8. `Source`: das ersetzte Template; `MatchFile`: Ihre Datei relativ zu `override/templates/`; `Match[...]`: die
   Bedingungen, die alle zutreffen müssen
   ([Templates und Design](../../../../anleitungen/templates-und-design.md#4-eine-änderung-mit-einem-override-vornehmen)).
9. `php bin/php/eztemplatecheck.php` mit dem Siteaccess und der Datei oder dem Verzeichnis
   ([Templates und Design](../../../../anleitungen/templates-und-design.md#5-die-syntax-prüfen-bevor-sie-in-den-browser-schauen)).
10. `wash` maskiert HTML, sodass eingegebener Text kein Markup und keine Skripte einschleusen kann
    ([Templates und Design](../../../../anleitungen/templates-und-design.md#6-die-template-sprache-in-zehn-minuten)).
11. Zwei davon: jede Änderung mit `git diff` sehen, mit `git log`/`git blame` herausfinden, warum eine Zeile da ist,
    eine Änderung, die etwas kaputt macht, mit `git bisect` finden, eine Korrektur vor dem Release nutzen, eigene
    Änderungen auf einem Branch halten, einen Pull Request schicken
    ([Mit Git entwickeln](../../../mit-git-entwickeln.md#warum-git-klone-und-keine-archive)).
12. Nur v2; Lesezugriffe beantworten v1 und v2
    ([REST-API v1 und v2](../../../../anleitungen/rest-api-v1-und-v2.md#kurz-gesagt)).
13. Anhand dessen, was bereits veröffentlicht ist: `git fetch --tags`, dann die Tags nach Version sortiert auflisten
    (`--sort=version:refname`), niemals nach Namen ([Erweiterungen](../../../../anleitungen/extensions.md#7-die-extension-veröffentlichen)).
14. Lassen Sie ihn stehen; veröffentlichen Sie die nächste Version mit der Korrektur und schreiben Sie in deren
    Hinweise, was falsch war. Verschieben oder löschen Sie niemals einen gepushten Tag
    ([Erweiterungen](../../../../anleitungen/extensions.md#7-die-extension-veröffentlichen)).

</details>

## Bewertung

- Ein Punkt pro Frage; die Fragen 1, 4 und 8 brauchen für den Punkt jeden Teil (sonst ein halber Punkt).
- Bestanden: 10 von 14 Punkten oder mehr, mit den Fragen 6 und 14 richtig: Die eine betrifft die Sicherheit, die
  andere lässt sich nicht rückgängig machen.

## Weiterführende Literatur

- [Erweiterungen](../../../../anleitungen/extensions.md): aktivieren, bauen, veröffentlichen.
- [Templates und Design](../../../../anleitungen/templates-und-design.md): Suchreihenfolge, Overrides, die Sprache.
- [Mit Git entwickeln](../../../mit-git-entwickeln.md) und [REST-API v1 und v2](../../../../anleitungen/rest-api-v1-und-v2.md).
- [Erweiterungsentwicklung](../../../erweiterungsentwicklung/README.md): das Buch hinter dem Kurs.

## Verwandte Seiten

- [Lektion 4.2: Entwickler, praktische Aufgabe](entwickler-praktische-aufgabe.md)
- [Modul 4: Die technischen Kurse prüfen](README.md)
- [Prüfungen und praktische Übungen: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
