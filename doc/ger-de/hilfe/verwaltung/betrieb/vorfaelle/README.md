---
title: Vorfälle
audience: government, admin, hosting, project-manager
category: public-sector
order: 10
summary: Übersicht der Runbooks für Vorfälle auf Exponential-Websites des öffentlichen Sektors - der gemeinsame Rahmen, Website nicht erreichbar, Datenbank nicht erreichbar, Verunstaltung, kompromittierte Konten, Meldung von Datenschutzverletzungen, Anfragefluten, fehlerhafte Bereitstellungen, massenhafte Inhaltsfehler, veraltete Caches, E-Mail, Festplatte und Zertifikate sowie die Nachbetrachtung.
keywords: Vorfälle, Runbooks, Ausfall, Website nicht erreichbar, Datenbank, Verunstaltung, Kompromittierung, Datenschutzverletzung, DDoS, Rücknahme, Inhaltsfehler, Cache, E-Mail, Festplatte voll, Zertifikat, Nachbetrachtung, Übersicht, incidents, runbooks, outage, site down, defacement, data breach, rollback, disk full, certificate, post-incident review
type: help
---
# Vorfälle

Dieser Ordner enthält Runbooks für die schlechten Tage einer öffentlichen Website. Jedes beginnt mit dem, was zu prüfen
ist, verwendet Befehle, die es in der Installation gibt, und ergänzt, was eine öffentliche Stelle über die technische
Behebung hinaus braucht: Rollen, Beweise, Kommunikation mit der Öffentlichkeit und eine Dokumentation. Die Runbooks für
Hoster ([Hosting-Runbooks](../../../hosting/runbooks/README.md)) gehen auf der Serverseite tiefer und sind dort
verlinkt, wo es hilft.

## Inhalt
1. [Seiten](#seiten)
2. [So verwenden Sie sie](#so-verwenden-sie-sie)
3. [Verwandte Seiten](#verwandte-seiten)

## Seiten

| Seite | Verwenden Sie sie, wenn |
|---|---|
| [Grundlagen der Vorfallsbehandlung](grundlagen-der-vorfallsbehandlung.md) | bei jedem Vorfall: Rollen, Schweregrad, die ersten fünfzehn Minuten, Beweise, Kommunikation |
| [Website nicht erreichbar](website-nicht-erreichbar.md) | die Website keine Seiten ausliefert |
| [Datenbank nicht erreichbar](datenbank-nicht-erreichbar.md) | Besucher „We'll be right back“ sehen |
| [Verunstaltung der Website](verunstaltung-der-website.md) | die Website Inhalte zeigt, die niemand freigegeben hat |
| [Verdacht auf ein kompromittiertes Konto](verdacht-auf-kompromittiertes-konto.md) | ein Konto möglicherweise von jemand anderem benutzt wird |
| [Meldung von Datenschutzverletzungen](meldung-von-datenschutzverletzungen.md) | möglicherweise personenbezogene Daten betroffen sind |
| [DDoS und Fluten von Sondierungsanfragen](ddos-und-flut-von-sondierungsanfragen.md) | eine Flut von Anfragen die Website verlangsamt oder stoppt |
| [Fehlerhafte Bereitstellung zurücknehmen](fehlerhafte-bereitstellung-zuruecknehmen.md) | Fehler direkt nach einer Bereitstellung oder Aktualisierung begannen |
| [Massenhafter Inhaltsfehler](massenhafter-inhaltsfehler.md) | viele Seiten auf einmal fehlerhaft wurden |
| [Veralteter Cache](veralteter-cache.md) | alte Inhalte oder alter Code weiterhin angezeigt werden |
| [Probleme mit E-Mails](probleme-mit-e-mails.md) | E-Mails nicht ankommen oder eine Testkopie an echte Personen ging |
| [Festplatte voll](festplatte-voll.md) | auf dem Server kein Platz mehr frei ist |
| [Ablauf von Zertifikaten](ablauf-von-zertifikaten.md) | Browser vor dem Zertifikat warnen |
| [Nachbetrachtung eines Vorfalls](nachbetrachtung-eines-vorfalls.md) | nach dem Vorfall: Zeitablauf, Ursachen, Maßnahmen |

## So verwenden Sie sie

1. Lesen Sie die [Grundlagen der Vorfallsbehandlung](grundlagen-der-vorfallsbehandlung.md) einmal, bevor Sie sie brauchen, und
   vereinbaren Sie die Rollen und die Schweregradskala mit Ihrem Team.
2. Öffnen Sie während eines Vorfalls das passende Runbook und arbeiten Sie es von oben nach unten ab.
3. Machen Sie Notizen mit Uhrzeiten; den Großteil des Rests ergänzt der Audit-Trail.
4. Schließen Sie mit einer [Nachbetrachtung des Vorfalls](nachbetrachtung-eines-vorfalls.md) ab.

Übung: Spielen Sie jedes Quartal ein Runbook auf einer Testkopie durch (nehmen Sie die Website zum Beispiel in den
Wartungsmodus und wieder heraus, oder stellen Sie eine Datensicherung wieder her), damit die Befehle vertraut sind,
bevor es darauf ankommt.

Nach oben: [Exponential im öffentlichen Sektor betreiben](../README.md). Die Einstiegsseite des Bereichs ist
[Verwaltungen: hier beginnen](../../verwaltungen-hier-beginnen.md).

## Verwandte Seiten
- [Überwachung](../ueberwachung/README.md)
- [Datensicherung und Wiederherstellung](../datensicherung-und-wiederherstellung/README.md)
- [Wenn etwas schiefgeht (Hilfe)](../../../wenn-etwas-schiefgeht.md)
- [Sicherheitshärtung](../../../sicherheitshaertung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
