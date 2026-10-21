---
title: Öffentliche Adressen nach einem Ausstieg funktionsfähig halten
audience: government, project-manager, admin, developer
category: public-sector
order: 90
summary: Warum die öffentlichen Adressen einer Verwaltungswebsite nach einem Systemwechsel weiter funktionieren müssen, wo Exponential jede Adresse speichert (Haupt-Aliase im Website-Archiv, weitere Aliase und Platzhalter in zwei Tabellen), wie Sie die Umleitungstabelle aufbauen und installieren und wie Sie sie vor und nach der Umstellung testen.
keywords: Umleitungen, URL-Umleitungen, 301, dauerhafte Umleitung, alte Adressen, tote Links, URL-Aliase, Platzhalter, ezurlalias_ml, ezurlwildcard, Migration, SEO, gedruckte Links, redirects, URL redirects, permanent redirect, old addresses, link rot, URL aliases, wildcards, migration, printed links
type: help
---
# Öffentliche Adressen nach einem Ausstieg funktionsfähig halten

Diese Seite richtet sich an Projektleitungen und Webteams, die eine öffentliche Website auf ein anderes System oder
eine andere Domain umziehen. Die Adressen einer Verwaltungswebsite stehen in gedruckten Briefen, Formularen, Gesetzen
und auf den Seiten anderer Organisationen; eine defekte Adresse schickt Bürgerinnen und Bürger auf eine Fehlerseite.
Eine Umleitungstabelle ordnet jeder alten Adresse die Adresse desselben Inhalts auf dem neuen System zu. Die Quellen
sind [Exponential verlassen, Abschnitt 4](../../../../installation/migration/exponential-verlassen.md#4-adressen-für-die-weiterleitungen)
und [Umleitungen und verschobene Inhalte](../../../../../eng-us/help/creators/website-creators/redirects-and-moved-content.md).

## Inhalt
1. [Wo Exponential Adressen speichert](#wo-exponential-adressen-speichert)
2. [Die Umleitungstabelle aufbauen](#die-umleitungstabelle-aufbauen)
3. [Sie auf dem neuen System installieren](#sie-auf-dem-neuen-system-installieren)
4. [Testen](#testen)
5. [Umleitungen auf Dauer erhalten](#umleitungen-auf-dauer-erhalten)
6. [Checkliste](#checkliste)

## Wo Exponential Adressen speichert

| Adresse | Wo |
|---|---|
| Haupt-URL-Alias jedes Knotens | eine Spalte des Website-Archivs, einer je Objekt |
| Eigene Aliase, Aliase in anderen Sprachen, historische Aliase nach Umbenennungen | Tabelle `ezurlalias_ml` |
| Platzhalter (Muster wie `old/*`) | Tabelle `ezurlwildcard` |
| Adressen von Dateien | Links über `content/download`; die Dateien liegen im Speicherverzeichnis |

Wie Aliase und Platzhalter auf der Website funktionieren, erklären [URL-Aliase und sprechende URLs](../../../../../eng-us/help/creators/website-creators/url-aliases-and-nice-urls.md)
und [URL-Platzhalter](../../../../../eng-us/help/creators/website-creators/url-wildcards.md). Administratoren sehen sie unter
**URL-Übersetzer** und **URL Platzhalter** ([URLs und Aliase](../../../../anleitungen/urls-und-aliase.md)).

## Die Umleitungstabelle aufbauen

1. Entnehmen Sie dem Website-Archiv den Haupt-Alias jedes Objekts und seine Objekt-ID oder Remote-ID.
2. Ergänzen Sie aus dem Datenbank-Dump die weiteren Aliase jedes Knotens aus `ezurlalias_ml` und die Platzhalter aus
   `ezurlwildcard`.
3. Halten Sie nach dem Import in das neue System die neue Adresse jedes Objekts fest, zugeordnet über Remote-ID oder
   Objekt-ID.
4. Erstellen Sie eine Tabelle: alte Adresse, neue Adresse, Status (dauerhafte Umleitung oder „gone“, wo Inhalte bewusst
   nicht migriert wurden).
5. Entscheiden Sie, was mit Adressen ohne Treffer geschieht: eine hilfreiche Seite „Seite verschoben“ mit Suche, kein
   nackter Fehler.

```text
old_path                                   new_url                                  object_remote_id   rule
/services/permits/building-permit          https://www.example.gov/permits/building <remote id>        301
/old-news/*                                https://www.example.gov/news             (wildcard)         301
/about/history-2009                        -                                        <remote id>        410 gone (archived)
```

## Sie auf dem neuen System installieren

- Installieren Sie die Umleitungen **bevor** das DNS umgestellt wird, wie es die Dokumentation sagt.
- Verwenden Sie die eigene Umleitungsfunktion des neuen Systems oder die Rewrite-Regeln des Webservers; bevorzugen Sie
  für verschobene Inhalte dauerhafte Umleitungen.
- Behalten Sie Umleitungsregeln auch auf der alten Domain, wenn sich die Domain ändert.

## Testen

- Vor der Umstellung: Rufen Sie eine Stichprobe alter Adressen (einschließlich der meistbesuchten, aus Ihrer Analyse
  oder den Logdateien des Webservers) über einen Eintrag in der Hosts-Datei gegen das neue System auf.
- Nach der Umstellung: Prüfen Sie die meistbesuchten alten Adressen und die in Drucksachen verwendeten Adressen.
- Beobachten Sie in den Wochen danach das 404-Protokoll des neuen Systems auf verpasste Adressen; ergänzen Sie sie in
  der Tabelle.
- Unter Exponential helfen das 404-Protokoll und der Log-Betrachter bei einem gewöhnlichen Relaunch auf dieselbe Weise
  ([Log-Betrachter](../../../../../eng-us/features/6.0/log-viewer.md)).

## Umleitungen auf Dauer erhalten

Alte Adressen kommen noch jahrelang aus Dokumenten und Lesezeichen. Behalten Sie die Umleitungstabelle so lange, wie es
Ihre Regeln für öffentliche Informationen erlauben, und nehmen Sie sie für den nächsten Wechsel in die Übergabe der
Dokumentation auf.

## Checkliste

- [ ] Haupt-Aliase aus dem Website-Archiv; weitere Aliase und Platzhalter aus der Datenbank.
- [ ] Jede alte Adresse zugeordnet oder mit Begründung als „gone“ markiert.
- [ ] Umleitungen vor der DNS-Umstellung installiert; mit einer Stichprobe getestet.
- [ ] Gedruckte und in Rechtstexten genannte Adressen einzeln getestet.
- [ ] 404-Protokoll nach der Umstellung beobachtet; Fehltreffer ergänzt.

## Verwandte Seiten
- [Ausstiegspläne: Übersicht](README.md)
- [Wechsel zu einem anderen System](wechsel-zu-einem-anderen-system.md)
- [Checkliste zur Migration, URLs und Umleitungen](../../../../installation/migration/checkliste.md#4-urls-und-weiterleitungen)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
