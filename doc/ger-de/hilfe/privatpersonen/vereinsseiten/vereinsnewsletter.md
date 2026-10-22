---
title: Vereinsnewsletter
audience: individual, website-creator, customer
category: personal-sites
order: 1713
summary: Wie eine ehrenamtliche Person mit der Erweiterung cjw_newsletter, die mit Exponential geliefert wird, einen Vereinsnewsletter verschickt - Listen, Abonnements mit Double-Opt-in, eine Ausgabe schreiben, Testversand, Versand per Cron, Bounces und Abmeldungen, was der Server bereitstellen muss und was die Erweiterung nicht tut.
keywords: Newsletter, Vereinsnewsletter, cjw_newsletter, Mailingliste, abonnieren, Double-Opt-in, abmelden, Ausgabe, Testversand, Cron, E-Mail-Zustellung, SMTP, Bounces, Sperrliste, Archiv, Einwilligung, club newsletter, double opt-in, unsubscribe, edition, mail delivery, consent
type: help
---
# Vereinsnewsletter

Diese Seite ist für Privatpersonen, die einen Newsletter für einen Verein oder eine Hobbygruppe verschicken.
`cjw_newsletter` wird mit Exponential geliefert (`se7enxweb/cjw_newsletter` in der `composer.json` im
Wurzelverzeichnis) und ist ein vollständiges Newslettersystem: Listen, Abonnements mit Double-Opt-in, als Inhalt
geschriebene Ausgaben, Versand per Cron, Bounces und ein Archiv
([cjw_newsletter: Newsletter in der Administration](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md)).

## Inhalt

1. [Bevor Sie beginnen: E-Mail und Cron](#bevor-sie-beginnen-e-mail-und-cron)
2. [Die Bestandteile](#die-bestandteile)
3. [Eine Liste einrichten](#eine-liste-einrichten)
4. [Abonnenten gewinnen](#abonnenten-gewinnen)
5. [Eine Ausgabe schreiben und versenden](#eine-ausgabe-schreiben-und-versenden)
6. [Nach dem Versand](#nach-dem-versand)
7. [Was sie nicht tut](#was-sie-nicht-tut)
8. [Checkliste](#checkliste)

## Bevor Sie beginnen: E-Mail und Cron

Zwei Dinge außerhalb der Erweiterung entscheiden, ob Ihr Newsletter ankommt:

| Bedarf | Warum | Fragen Sie |
|---|---|---|
| **Ein Cronjob** | Ausgaben werden von den Cronjobs `cjw_newsletter_mailqueue_create` und `cjw_newsletter_mailqueue_process` in eine Warteschlange gestellt und versendet | „Kann ich `runcronjobs.php` alle paar Minuten ausführen?" ([Cronjobs](../../../anleitungen/cronjobs.md)) |
| **E-Mail-Zustellung** | Die Erweiterung übergibt E-Mails an `sendmail` oder einen SMTP-Server; sie stellt nicht selbst zu | „Wie viele E-Mails darf ich pro Stunde versenden, und ist der Absender meiner Domain eingerichtet (SPF, DKIM)?" |

Siehe [E-Mail-Zustellung von Newslettern](../../unternehmen/newsletters/zustellung-der-newsletter-mails.md). Solange nicht
beides vorhanden ist, richten Sie noch nichts anderes ein.

## Die Bestandteile

| Teil | Was es ist |
|---|---|
| Newslettersystem | Der Container im Inhaltsbaum |
| Liste | Das, was abonniert wird („Vereinsnachrichten", „Spielergebnisse") |
| Ausgabe | Eine einzelne Ausgabe, geschrieben wie ein Artikel |
| Abonnements | Wer auf welcher Liste steht, mit einem Status (ausstehend, bestätigt, entfernt, unzustellbar, gesperrt) |
| Öffentliche Formulare | Abonnieren, bestätigen, ändern, abmelden |
| Archiv | Jede versendete Ausgabe |

Sie öffnen es über **Newsletter** im oberen Menü der Administration (`/newsletter/index`).

## Eine Liste einrichten

1. Legen Sie im Newsletterteil des Baums ein Newslettersystem und eine Liste („Vereinsnachrichten") an.
2. Setzen Sie Absendername und -adresse: die Adresse des Vereins auf der Domain des Vereins, keine persönliche
   Freemail-Adresse.
3. Wählen Sie das Ausgabeformat und das Design der E-Mail.
4. Verlinken Sie die Abonnementseite im Fußbereich der Website und auf der Seite „Mitglied werden".

Einzelheiten für Website-Ersteller: [Newsletter mit cjw_newsletter](../../ersteller/website-ersteller/newsletter-mit-cjw-newsletter.md),
[Newsletter-Listen und Abonnements](../../ersteller/website-ersteller/newsletter-listen-und-abonnements.md).

## Abonnenten gewinnen

- **Double-Opt-in**: Abonnenten bestätigen über einen Link in einer E-Mail. Behalten Sie das bei; es belegt die
  Einwilligung und hält Tippfehler fern.
- **Importieren Sie nie eine Liste mit Adressen** von Menschen, die nicht darum gebeten haben, auch nicht von
  Mitgliedern. Laden Sie sie stattdessen zum Abonnieren ein.
- Hat der Verein bereits eine Liste mit dokumentierter Einwilligung, kann die Erweiterung sie importieren; prüfen
  Sie, wofür die Einwilligung gilt ([Opt-in und Einwilligung](../../unternehmen/newsletters/opt-in-und-einwilligung.md)).

## Eine Ausgabe schreiben und versenden

1. Legen Sie unter der Liste eine Ausgabe an. Schreiben Sie sie wie eine Seite: ein klarer Betreff, kurze Meldungen,
   Links auf die Website für die langen Fassungen.
2. Sehen Sie sie in der **Vorschau** an.
3. **Schicken Sie einen Test** an sich selbst und eine weitere ehrenamtliche Person; öffnen Sie ihn auf einem
   Smartphone.
4. **Planen Sie den Versand.** Die Cronjobs stellen ihn in die Warteschlange und versenden im Hintergrund; große
   Listen brauchen eine Weile.
5. Beobachten Sie den Versandstatus in der Administration.

Schreibtipps: [Ausgaben schreiben und versenden](../../unternehmen/newsletters/ausgaben-schreiben-und-versenden.md),
[Newsletter versenden](../../ersteller/website-ersteller/newsletter-versenden.md).

## Nach dem Versand

- **Bounces**: Adressen, die wiederholt unzustellbar sind, erhalten keine E-Mails mehr (nach `BounceThresholdValue`
  Bounces, Standard 3), wenn ein Bounce-Postfach eingerichtet ist
  ([Bounces und Listenpflege](../../unternehmen/newsletters/bounces-und-listenpflege.md)).
- **Abmeldungen** werden über die öffentlichen Formulare abgewickelt; tragen Sie niemanden wieder ein, der gegangen ist.
- **Archiv**: Frühere Ausgaben bleiben im Archiv; verlinken Sie es auf Wunsch im Mitgliederbereich.

## Was sie nicht tut

Aus [cjw_newsletter für Unternehmen](../../unternehmen/newsletters/cjw-newsletter-fuer-unternehmen.md#was-sie-nicht-tut):

- Sie stellt E-Mails nicht selbst zu.
- Sie zählt Öffnungen und Klicks standardmäßig nicht.
- Sie entscheidet nicht, was für Ihre Liste rechtlich zulässig ist.

Sie verschickt auch neue Blogbeiträge nicht automatisch; jede Ausgabe wird von einem Menschen geschrieben und
versendet.

## Checkliste

- [ ] Cron läuft; E-Mails verlassen den Server und kommen an (an zwei verschiedene E-Mail-Anbieter getestet).
- [ ] Absenderadresse auf der Domain des Vereins.
- [ ] Eine Liste, Double-Opt-in, Abonnement-Link auf der Website.
- [ ] Testversand auf einem Smartphone geprüft.
- [ ] Bounce-Postfach eingerichtet, oder Bounces werden von Hand geprüft.
- [ ] Eine zweite ehrenamtliche Person weiß, wie man versendet.

## Verwandte Seiten

- [Vereinsseiten](README.md)
- [Newsletter (Unternehmen)](../../unternehmen/newsletters/README.md)
- [Newsletter-Checkliste](../../unternehmen/newsletters/checkliste-newsletter.md)
- [Newsletter-Routine (Kunden)](../../kunden/wachstum/newsletter-routine.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
