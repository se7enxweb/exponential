---
title: Weiterleitungen nach einer Umstrukturierung
audience: government, admin, editor, project-manager
category: public-sector
order: 200
summary: Für öffentliche Stellen, die ihre Website neu ordnen (eine Zusammenlegung von Abteilungen, eine neue Navigation, ein Relaunch) - ein Weiterleitungsplan mit den Aliassen, globalen Aliassen und Platzhaltern von Exponential, Tests mit curl und die Beobachtung von 404.log danach, damit zitierte Adressen weiter funktionieren.
keywords: Weiterleitungen, Umstrukturierung, Relaunch, Zusammenlegung, Neuorganisation, Weiterleitungsplan, Platzhalter, URL-Aliasse, URL-Übersetzer, 301, 404.log, Tests, Linkverfall, öffentlicher Sektor, redirects, restructuring, relaunch, merger, reorganization, redirect plan, wildcards, URL aliases, URL translator, testing, link rot, public sector
type: help
---
# Weiterleitungen nach einer Umstrukturierung

Diese Seite richtet sich an Administratoren, Redaktionen und Projektleitungen. Öffentliche Stellen ordnen sich neu:
Abteilungen werden zusammengelegt, Leistungen ziehen um, Websites werden neu aufgesetzt. Adressen, die in Schreiben und
Gesetzen zitiert werden, müssen weiter irgendwohin führen. Verschiebungen und Umbenennungen innerhalb einer
Exponential-Website leiten von selbst weiter; Umstrukturierungen, die Seiten entfernen oder Websites wechseln, brauchen
einen Weiterleitungsplan
([Weiterleitungen und verschobene Inhalte: Eine Website neu aufsetzen](../../../../eng-us/help/creators/website-creators/redirects-and-moved-content.md#relaunching-a-site-a-redirect-plan)).

## Inhalt

1. [Der Plan](#der-plan)
2. [Das Werkzeug wählen](#das-werkzeug-wählen)
3. [Testen](#testen)
4. [Nach der Änderung](#nach-der-änderung)
5. [Verwandte Seiten](#verwandte-seiten)

## Der Plan

1. **Sammeln Sie die alten Adressen**: die Sitemap, die Liste der indexierten Seiten der Suchmaschinen, die Protokolle
   des Webservers und die in Ihren eigenen Dokumenten zitierten Adressen.
2. **Legen Sie für jede ein Ziel fest**: dieselbe Seite, eine bessere Seite oder eine Seite „nicht mehr vorhanden“, die
   erklärt, wohin die Leistung gewandert ist.
3. **Verschieben Sie lieber, statt neu anzulegen**: Ein verschobener Knoten behält seine ID, und seine alte Adresse
   leitet automatisch weiter.
4. **Schreiben Sie Muster als Platzhalter** und einzelne Seiten als Aliasse ihres Knotens.

| Alte Adresse | Neues Ziel | Art |
|---|---|---|
| `/department-a/*` | `/merged-department/{1}` | Platzhalter |
| `/forms/permit.html` | Knoten „Eine Genehmigung beantragen“ | Alias am Knoten |
| `/contact` | Ansicht eines Moduls oder Seite | globaler Alias |

## Das Werkzeug wählen

| Werkzeug | Wo | Hinweise |
|---|---|---|
| Alias am Knoten | der Reiter **URL-Aliase** des Knotens | für eine einzelne alte Adresse |
| Globaler Alias | **Setup > URL-Übersetzer** | „Alias should redirect“ (Alias soll weiterleiten) für eine 301 |
| Platzhalter | **Setup > URL Platzhalter** | wird nur herangezogen, wenn kein Alias passt, in der Reihenfolge der Anlage; **Eine Adresse ausprobieren** testet, ohne etwas zu ändern; „Umleitende URL“ für eine Weiterleitung |
| Webserver | der Server oder Velocity | Wechsel von Host und Schema (`http` zu `https`, alte Domain zu neuer) |

## Testen

```bash
while read old; do
  curl -s -o /dev/null -w "$old %{http_code} %{redirect_url}\n" "https://www.example.gov$old"
done < old-addresses.txt
```

Jede Zeile sollte `301` zeigen (oder `200`, wo die Adresse selbst noch existiert) und das vorgesehene Ziel.

## Nach der Änderung

- Beobachten Sie **Setup > Logdateien** > „Nicht gefundene Anfragen (404)“ auf Fehlgriffe; das Feld `reason=` zeigt,
  warum der Kernel nichts gefunden hat ([Anfrageprotokolle](../../../../eng-us/features/6.0/request-logs.md)).
- Behalten Sie die Weiterleitungen über Jahre: Zitierte Dokumente leben lange.
- Halten Sie den Plan zusammen mit der Änderung in Ihrem Schriftgut fest.

## Verwandte Seiten

- [URLs erhalten](urls-erhalten.md)
- [Weiterleitungen und verschobene Inhalte](../../../../eng-us/help/creators/website-creators/redirects-and-moved-content.md)
- [Links, URL-Aliasse, Platzhalter](../../../anleitungen/urls-und-aliase.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
