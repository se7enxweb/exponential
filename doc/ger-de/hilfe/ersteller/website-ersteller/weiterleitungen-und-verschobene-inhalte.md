---
title: Weiterleitungen und verschobene Inhalte
audience: website-creator, admin
category: website-creation
order: 430
summary: Alte Adressen funktionsfähig halten - automatische Weiterleitungen nach Umbenennen oder Verschieben, globale Aliasse und Platzhalter für alte Adressen, Server-Weiterleitungen für Hosts und HTTPS, sichere Weiterleitungen in Formularen und ein Weiterleitungsplan für einen Relaunch.
keywords: Weiterleitungen, 301, verschobene Inhalte, umbenannt, alte URLs, Verlaufs-Alias, Relaunch, Migration, Platzhalter, globaler Alias, sichere Weiterleitungen, Host-Weiterleitung, Website-Ersteller, redirects, moved content, renamed, old URLs, history alias, relaunch, migration, wildcard, global alias, safe redirects, host redirect, website creator
type: help
---
# Weiterleitungen und verschobene Inhalte

Diese Seite richtet sich an Website-Ersteller, die Inhalte verschieben, umbenennen oder neu starten und dafür sorgen
wollen, dass alte Links weiter funktionieren. Defekte Adressen kosten Besucher und Suchmaschinenplatzierungen;
Exponential erledigt die meisten Fälle von selbst.

## Inhalt

1. [Was automatisch geschieht](#was-automatisch-geschieht)
2. [Weiterleitungen, die Sie einrichten](#weiterleitungen-die-sie-einrichten)
3. [Relaunch einer Website: ein Weiterleitungsplan](#relaunch-einer-website-ein-weiterleitungsplan)
4. [Weiterleitungen im Webserver](#weiterleitungen-im-webserver)
5. [Sichere Weiterleitungen in der Anwendung](#sichere-weiterleitungen-in-der-anwendung)
6. [Checkliste](#checkliste)

## Was automatisch geschieht

| Ereignis | Ergebnis |
|---|---|
| Ein Knoten wird umbenannt | ein neuer Alias wird angelegt; der alte bleibt und leitet auf den neuen weiter |
| Ein Knoten wird verschoben | dasselbe: der alte Pfad leitet weiter |
| Eine Übersetzung wird umbenannt | dasselbe, je Sprache |
| Ein Knoten wird entfernt | die Adresse antwortet mit "nicht gefunden"; nichts leitet weiter |

Die Redaktion darf also frei umbenennen und verschieben. Beim Entfernen ist es anders: Wenn eine Seite wegfällt,
entscheiden Sie, wohin ihre Besucher stattdessen gehen sollen, und legen Sie dafür einen Alias oder einen Platzhalter an.

## Weiterleitungen, die Sie einrichten

| Bedarf | Werkzeug |
|---|---|
| Eine alte Adresse auf eine Seite | der Reiter **URL-Aliase** des Knotens: fügen Sie die alte Adresse als Alias des Knotens hinzu |
| Eine Adresse auf eine Modulansicht | **Setup > URL-Übersetzer**: ein globaler Alias, mit "Alias soll weiterleiten" angehakt (301) |
| Eine Gruppe alter Adressen | **Setup > URL-Platzhalter**: `news/*` auf `articles/{1}`, mit "URL umleiten" angehakt |
| Adressen einer anderen Website | ein Platzhalter auf ein `https://`-Ziel |

Platzhalter werden nur herangezogen, wenn kein Alias passt, und zwar in der Reihenfolge ihrer Anlage; **Eine Adresse
ausprobieren** zeigt, auf welchen Platzhalter eine Adresse passt, ohne etwas zu ändern
([URL-Platzhalter](../../../anleitungen/urls-und-aliase.md#4-url-platzhalter)).

## Relaunch einer Website: ein Weiterleitungsplan

1. Exportieren Sie die Adressen der alten Website (ihre Sitemap, die Liste der indexierten Seiten aus der Suchmaschine
   oder die Logs des Servers).
2. Entscheiden Sie für jede Adresse das neue Ziel: dieselbe Seite, eine bessere Seite oder keine (eine "entfernt"-Seite).
3. Legen Sie Muster als Platzhalter und einzelne Seiten als Aliasse ihres Knotens an.
4. Testen Sie vor dem Start jede alte Adresse mit `curl -sI <old address>` und prüfen Sie, ob ein `301` an die richtige
   Stelle zeigt.
5. Beobachten Sie nach dem Start das Fehlerlog und die Crawling-Fehler der Suchmaschinen auf Fehltreffer.

| Alte Adresse | Neues Ziel | Art |
|---|---|---|
| `/news/2019/*` | `/News/{1}` | Platzhalter |
| `/about.html` | Knoten "Über uns" | Alias am Knoten |
| `/shop/cart` | `shop/basket` | globaler Alias |

## Weiterleitungen im Webserver

Weiterleitungen für Host und Schema gehören in den Webserver oder in Velocity, bevor Exponential läuft: `http` auf
`https`, `example.com` auf `www.example.com`. Siehe [Livegang: Hosts und HTTPS](livegang-hosts-und-https.md) und
[Velocity-HTTPS-Zertifikate](../../../funktionen/6.0/velocity-https-zertifikate.md).

## Sichere Weiterleitungen in der Anwendung

Formulare und Schaltflächen, die Besucher "dorthin zurück, woher sie kamen" schicken, folgen nur Adressen der Website
selbst, sodass ein Angreifer sie nicht nutzen kann, um Besucher anderswohin zu schicken
([Sichere Weiterleitungen](../../../funktionen/6.0/sichere-weiterleitungen.md)). Verwenden Sie in Ihrem eigenen Code dieselben
Funktionen ([Formulare, CSRF und HTTP-Eingaben](../anwendungsentwickler/formulare-csrf-und-http-eingaben.md)).

## Checkliste

- [ ] Die Redaktion weiß, dass Umbenennen und Verschieben alte Adressen funktionsfähig halten, Entfernen aber nicht.
- [ ] Entfernte Seiten erhalten einen Alias oder Platzhalter auf einen Ersatz.
- [ ] Ein Relaunch hat eine getestete Weiterleitungstabelle.
- [ ] Weiterleitungen für Host und HTTPS liegen im Webserver.
- [ ] Weiterleitungen sind 301 (dauerhaft), sofern der Umzug nicht vorübergehend ist.

## Verwandte Seiten

- [URL-Aliasse und sprechende URLs](url-aliasse-und-sprechende-urls.md)
- [URL-Platzhalter](url-platzhalter.md)
- [Umzug von einer älteren Installation](../../../installation/14-migration-von-4x.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
