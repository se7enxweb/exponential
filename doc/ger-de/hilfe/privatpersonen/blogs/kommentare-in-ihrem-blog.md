---
title: Kommentare in Ihrem Blog
audience: individual, website-creator, customer
category: personal-sites
order: 1623
summary: Wie Kommentare in einem Exponential-Blog funktionieren - die mitgelieferte Klasse Comment, das Kontrollkästchen zum Aktivieren von Kommentaren, wer standardmäßig kommentieren darf, wie Sie Kommentare auf angemeldete Leser beschränken, Kommentare zur Prüfung zurückhalten, Spam bekämpfen und was nicht verfügbar ist (kein ezcomments, keine Threads).
keywords: Kommentare, Klasse Comment, enable_comments, Blogkommentare, anonyme Kommentare, Moderation, Freigabe, Spam, CAPTCHA, hCaptcha, reCAPTCHA, Formular-Token, ezcomments, Berechtigungen, content/create, personenbezogene Daten, comments, moderation, approval, permissions, personal data
type: help
---
# Kommentare in Ihrem Blog

Diese Seite ist für Privatpersonen, die entscheiden, ob Leser ihr Blog kommentieren dürfen, und wie sie Kommentare
allein im Griff behalten. Kommentare machen aus einem Blog ein Gespräch; sie ziehen aber auch Spam an und manchmal
Unerfreuliches, mit dem Sie selbst umgehen müssen.

## Inhalt

1. [Wie Kommentare funktionieren](#wie-kommentare-funktionieren)
2. [Wer standardmäßig kommentieren darf](#wer-standardmäßig-kommentieren-darf)
3. [Drei Regeln zur Auswahl](#drei-regeln-zur-auswahl)
4. [Kommentare zur Prüfung zurückhalten](#kommentare-zur-prüfung-zurückhalten)
5. [Spam fernhalten](#spam-fernhalten)
6. [Einen Kommentar löschen](#einen-kommentar-löschen)
7. [Was es nicht gibt](#was-es-nicht-gibt)
8. [Checkliste](#checkliste)

## Wie Kommentare funktionieren

- Die Klasse *Blog post* hat ein Kontrollkästchen **Enable comments** (`enable_comments`).
- Ist es angekreuzt, listet die Vollansicht des Beitrags die unter dem Beitrag gespeicherten Kommentare und bietet
  Lesern, die einen anlegen dürfen, die Schaltfläche **Neuer Kommentar**; Leser ohne diese Berechtigung werden
  gebeten, sich anzumelden oder ein Konto anzulegen (Template `full/blog_post.tpl` in den Designs `ezwebin` und
  `sevenx_themes_simple`).
- Ein Kommentar ist ein Inhaltsobjekt der Klasse *Comment* mit drei Feldern: **subject** (Betreff), **author**
  (Autor) und **message** (Nachricht) (`class-comment.xml` im Paket `sevenx_classes`).

Weil ein Kommentar gewöhnlicher Inhalt ist, gilt alles, was Sie über Inhalte wissen: Er hat Versionen, kann
bearbeitet, in den Papierkorb verschoben, durchsucht und über Rollen gesteuert werden.

## Wer standardmäßig kommentieren darf

Der Installer des Standard-Seitenpakets (`sevenx_multisite`) gibt der Rolle **Anonymous** die Berechtigung, Objekte
der Klassen *Comment* (und *Review*) in der Sektion *Standard* zu erstellen (`sevenx-multi-site-installer.php`,
Richtlinien für die Rolle „Anonymous"). In der Praxis heißt das: In einer frischen Standardinstallation **kann jeder
Besucher einen Kommentar** zu einem Beitrag schreiben, bei dem Kommentare aktiviert sind. Auch die Rolle *Member* hat
Kommentarberechtigungen.

Prüfen Sie unter **Benutzer > Rollen** > *Anonymous*, was Ihre Website tatsächlich erlaubt, bevor Sie irgendwo
Kommentare aktivieren ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md),
[Besucherberechtigungen](../../ersteller/website-ersteller/rechte-fuer-besucher.md)).

## Drei Regeln zur Auswahl

| Regel | Wie | Passt zu |
|---|---|---|
| **Keine Kommentare** | **Enable comments** bei keinem Beitrag ankreuzen | Einem Blog, das Sie allein schreiben und nicht moderieren möchten |
| **Nur angemeldete Leser** | Die Richtlinie `content/create` für *Comment* bei *Anonymous* entfernen; bei *Member* behalten; Leser sich registrieren lassen | Den meisten persönlichen Blogs: weniger Bots, Leser haben ein Konto |
| **Alle** | Den Standard behalten; Spamschutz hinzufügen | Blogs, die schnelle Rückmeldungen möchten und täglich moderieren können |

Die Registrierung wird unter [Mitglieder und Registrierung](../vereinsseiten/mitglieder-und-registrierung.md) erklärt.

## Kommentare zur Prüfung zurückhalten

In den mitgelieferten Klassen hält nichts Kommentare von selbst zur Freigabe zurück. Es gibt zwei Wege:

1. **Ein Freigabe-Workflow** beim Veröffentlichen für die Klasse *Comment*: Neue Kommentare warten, bis Sie sie
   freigeben ([Workflows](../../../anleitungen/workflows.md), [Freigaben und Prüfung](../../kunden/alltag/freigabe-und-pruefung.md)).
   Das ist die echte Moderationswarteschlange; sie muss einmal eingerichtet werden.
2. **Tägliches Lesen**: Kommentare sind sofort online, und Sie entfernen, was nicht dorthin gehört. Einfach, aber
   schlechte Inhalte sind sichtbar, bis Sie nachsehen.

Wenn Sie „Alle" wählen, wählen Sie den Workflow.

## Spam fernhalten

| Ebene | Was sie tut | Quelle |
|---|---|---|
| Formular-Token | Stoppen gefälschte Einsendungen von anderen Websites | `ezformtoken`, in der Standardinstallation |
| CAPTCHA-Feld an der Klasse *Comment* | Fordert Besucher auf zu beweisen, dass sie Menschen sind | Datentyp `recaptcha` oder `hcaptcha`; Schlüssel vom Dienst |
| Nur angemeldete | Bots müssen sich zuerst registrieren | Rollen |
| Freigabe-Workflow | Nichts erscheint ohne Sie | Workflows |

Siehe [Spamschutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md) und
[Grundlagen zu Spam und Sicherheit](../im-betrieb/grundlagen-zu-spam-und-sicherheit.md).

## Einen Kommentar löschen

Öffnen Sie den Beitrag in der Administration, suchen Sie den Kommentar darunter und verschieben Sie ihn in den
Papierkorb (oder entfernen Sie ihn sofort, wenn er etwas enthält, das Sie nicht behalten dürfen). Wenn jemand Sie
bittet, einen Kommentar mit seinen personenbezogenen Daten zu entfernen, tun Sie es und leeren Sie ihn auch aus dem
Papierkorb ([Archivieren und der Papierkorb](../../kunden/alltag/archivieren-und-der-papierkorb.md),
[Aufbewahrung und Löschung von Daten](../../unternehmen/sicherheit/aufbewahrung-und-loeschung-von-daten.md)).

Schreiben Sie eine kurze Kommentarrichtlinie in das Blog: was Sie entfernen und warum. Das macht die Entscheidung
leichter, wenn es so weit ist.

## Was es nicht gibt

- **Keine Erweiterung `ezcomments`** in dieser Distribution; sie liegt weder in `extension/` noch in `composer.json`.
- **Keine verschachtelten Antworten, keine Likes und kein „Bei Antworten benachrichtigen"** für Kommentare.
- **Kein Import von Kommentaren** aus einer anderen Plattform, außer als Teil einer Migration, die Sie selbst
  skripten ([Umzug von einer anderen Blogplattform](umzug-von-einer-anderen-blogplattform.md)).

Wenn Sie mehr brauchen, ist ein externer Kommentardienst möglich, der im Template eingebunden wird; er schickt aber
die Daten Ihrer Leser an diesen Dienst. Wägen Sie das für ein persönliches Blog ab.

## Checkliste

- [ ] Regel gewählt: keine, nur angemeldete oder alle.
- [ ] Rolle *Anonymous* geprüft und passend zur Regel.
- [ ] Freigabe-Workflow eingerichtet, wenn alle kommentieren dürfen.
- [ ] Formular-Token aktiv; CAPTCHA an der Klasse *Comment*, wenn Sie anonyme Kommentare erlauben.
- [ ] Eine Kommentarrichtlinie veröffentlicht.
- [ ] Sie wissen, wie Sie einen Kommentar vollständig entfernen.

## Verwandte Seiten

- [Blogs](README.md)
- [Blogs: hier beginnen (Kunden)](../../kunden/typen/blogs-hier-beginnen.md)
- [Spamschutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md)
- [Besucherberechtigungen](../../ersteller/website-ersteller/rechte-fuer-besucher.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
