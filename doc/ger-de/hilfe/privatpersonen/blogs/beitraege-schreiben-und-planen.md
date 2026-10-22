---
title: Beiträge schreiben und planen
audience: individual, editor, customer
category: personal-sites
order: 1622
summary: Wie eine einzelne Person in Exponential Blogbeiträge schreibt, veröffentlicht und zeitlich plant - die Felder eines Blog post, Entwürfe und Vorschau, das Feld für das Veröffentlichungsdatum, späteres Veröffentlichen mit dem Workflow „Bis Datum warten", Zurückziehen zu einem Datum und warum jede Zeitplanung von Cron abhängt.
keywords: Blogbeitrag, Schreiben, Veröffentlichen, Zeitplan, später veröffentlichen, Veröffentlichungsdatum, Datum für das Zurückziehen, Bis Datum warten, Workflow, Cron, Entwürfe, Vorschau, Versionen, Redaktionskalender, blog post, schedule, publish later, Wait until date, unpublish date
type: help
---
# Beiträge schreiben und planen

Diese Seite ist für Privatpersonen, die ihre Blogbeiträge selbst schreiben und möchten, dass manche davon erscheinen,
während sie beschäftigt oder unterwegs sind. Allgemeine Schreibtipps stehen unter
[Für die eigene Website schreiben](../persoenliche-seiten/fuer-die-eigene-website-schreiben.md).

## Inhalt

1. [Die Felder eines Beitrags](#die-felder-eines-beitrags)
2. [Einen Beitrag schreiben](#einen-beitrag-schreiben)
3. [Zeitplanung: was möglich ist](#zeitplanung-was-möglich-ist)
4. [Später veröffentlichen](#später-veröffentlichen)
5. [Einen Beitrag zu einem Datum zurückziehen](#einen-beitrag-zu-einem-datum-zurückziehen)
6. [Ein Tempo, das Sie halten können](#ein-tempo-das-sie-halten-können)
7. [Checkliste pro Beitrag](#checkliste-pro-beitrag)

## Die Felder eines Beitrags

| Feld | Bezeichner | Verwendung |
|---|---|---|
| Titel | `title` | Worum es im Beitrag geht; daraus wird die Adresse |
| Text | `body` | Der Text, mit Überschriften, Listen, Links und Bildern |
| Veröffentlichungsdatum | `publication_date` | Das Datum, das am Beitrag angezeigt wird |
| Datum für das Zurückziehen | `unpublish_date` | Optional: wann der Beitrag entfernt werden soll |
| Tags | `tags` | Themen ([Tags und Kategorien](tags-und-kategorien.md)) |
| Kommentare aktivieren | `enable_comments` | Kontrollkästchen ([Kommentare in Ihrem Blog](kommentare-in-ihrem-blog.md)) |
| Metadaten | `metadata` | Titel und Beschreibung für Suchmaschinen |

Quelle: `class-blog_post.xml` im Paket `sevenx_classes`.

## Einen Beitrag schreiben

1. Gehen Sie zu Ihrem *Blog* und wählen Sie **Hier erstellen** > *Blog post*.
2. Schreiben Sie Titel und Text. Nutzen Sie oft **Entwurf speichern**; ein Entwurf ist privat.
3. Fügen Sie Tags hinzu und entscheiden Sie über Kommentare.
4. Mit **Vorschau** sehen Sie ihn so, wie Leser ihn sehen werden.
5. **Veröffentlichen**. Er erscheint oben im Blog und im Feed.

Wenn Sie einen veröffentlichten Beitrag ändern, entsteht eine neue Version; die alte bleibt erhalten und kann
wiederhergestellt werden ([Veröffentlichen und Versionen](../../veroeffentlichen-und-versionen.md),
[Entwürfe, Versionen und Rückgängigmachen](../../kunden/alltag/entwuerfe-versionen-und-rueckgaengig.md)).

## Zeitplanung: was möglich ist

| Sie möchten | Was es erledigt | Braucht |
|---|---|---|
| Einen Beitrag, der an einem Datum erscheint | Das Workflow-Ereignis *Bis Datum warten* | Einmal vom Administrator eingerichtet; Cron |
| Einen Beitrag, der an einem Datum verschwindet | Der Cronjob `unpublish.php` mit dem Feld `unpublish_date` | Aktiviert in `settings/content.ini [UnpublishSettings]`; Cron |
| Einen Beitrag, der an einem Datum verborgen wird | Der Cronjob `hide.php` | `settings/content.ini [HideSettings]`; Cron |
| Ein am Beitrag angezeigtes Datum | Das Feld `publication_date` | Nichts; es wird nur angezeigt |

Quelle und Einzelheiten: [Zeitgesteuertes Veröffentlichen](../../kunden/alltag/zeitgesteuertes-veroeffentlichen.md).

> **Hinweis**
> Wenn Sie nur `publication_date` ausfüllen, wird ein Beitrag nicht zurückgehalten: Das Feld wird am Beitrag
> angezeigt, veröffentlicht wird aber trotzdem, sobald Sie auf **Veröffentlichen** klicken. Zum Zurückhalten
> braucht es den Workflow.

## Später veröffentlichen

Auf einer Website, die Sie selbst betreiben, sind Sie der Administrator:

1. Richten Sie einen Workflow mit dem Ereignis *Bis Datum warten* für die Klasse *Blog post* ein und verbinden Sie
   ihn mit dem Trigger „vor dem Veröffentlichen" ([Workflows](../../../anleitungen/workflows.md)).
2. Stellen Sie sicher, dass Cron den Teil `frequent` ausführt, der den Workflow-Cronjob enthält
   ([Cronjobs](../../../anleitungen/cronjobs.md)).
3. Schreiben Sie den Beitrag, setzen Sie das Datum und klicken Sie auf **Veröffentlichen**. Die Version wartet; sie
   wird beim ersten Cron-Lauf nach dem Datum veröffentlicht.

Testen Sie es mit einem Beitrag, der zehn Minuten in der Zukunft datiert ist, bevor Sie sich im Urlaub darauf
verlassen.

## Einen Beitrag zu einem Datum zurückziehen

Nützlich für einen Aufruf zur Einreichung, eine Veranstaltungsankündigung oder ein befristetes Angebot. Füllen Sie
`unpublish_date` aus; der Cronjob `unpublish.php` verschiebt den Beitrag nach dem Datum in den Papierkorb, aus dem
er wiederhergestellt werden kann. Er muss für die Klasse in `settings/content.ini [UnpublishSettings]` aktiviert sein.

## Ein Tempo, das Sie halten können

- Legen Sie einen Rhythmus fest (wöchentlich, alle zwei Wochen) und tragen Sie ihn in einen
  [Redaktionskalender](../../kunden/alltag/redaktionskalender.md) ein.
- Halten Sie zwei oder drei Beiträge als Entwürfe oder geplant vor, damit eine volle Woche den Rhythmus nicht bricht.
- Bündeln Sie die Arbeit: Schreiben Sie an einem ruhigen Abend zwei Beiträge und planen Sie einen davon ein.
- Ein später Beitrag ist besser als ein überhasteter; ein ehrliches „in zwei Wochen geht es weiter" ist besser als
  Schweigen.

## Checkliste pro Beitrag

- [ ] Ein Titel, der sagt, worum es im Beitrag geht.
- [ ] Ein erster Absatz, der für sich allein funktioniert (ihn zeigen Feeds und Suchergebnisse).
- [ ] Überschriften und kurze Absätze.
- [ ] Alternativtext für jedes Bild.
- [ ] Vorhandene Tags, keine neuen Schreibweisen alter Tags.
- [ ] Kommentare bewusst ein- oder ausgeschaltet.
- [ ] Vorschau geprüft; bei Planung stimmt das Datum und Cron läuft.

## Verwandte Seiten

- [Blogs](README.md)
- [Zeitgesteuertes Veröffentlichen](../../kunden/alltag/zeitgesteuertes-veroeffentlichen.md)
- [Für die eigene Website schreiben](../persoenliche-seiten/fuer-die-eigene-website-schreiben.md)
- [Der Schreibassistent](../im-betrieb/schreibassistent-expai.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
