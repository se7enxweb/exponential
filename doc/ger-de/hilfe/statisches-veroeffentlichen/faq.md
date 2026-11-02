---
title: "FAQ zum statischen Veröffentlichen"
audience: admin, website-creator, editor, non-technical, non-profit, local-business, individual, student, developer, hosting
category: administration
order: 25
summary: Kurze Antworten auf die Fragen, die zum statischen Veröffentlichen gestellt werden - Kosten, Hosting, Redaktion, Kommentare, Zurückkehren, Exponential Layouts, Smarty- und Twig-Websites, Sprachen, Teile einer Website, private Repositorys und eigene Domains.
keywords: Statisches Veröffentlichen, FAQ, Fragen, kostenlos, kostenloses Hosting, Redaktion, Kommentare, Zurücknehmen, mehrsprachig, privates Repository, eigene Domain, static publishing, FAQ, is it free, free hosting, free website, GitHub Pages, github.io, hosting, editors, comments, rollback, Exponential Layouts, Smarty, Twig, multilingual, private repository, custom domain, CNAME, Jekyll alternative, Netlify, static site generator
featured: true
type: help
---
# FAQ zum statischen Veröffentlichen

## Was Sie am Ende haben

Eine Antwort auf Ihre Frage oder einen Link zu der Seite, die sie hat.

## Kosten und Hosting

**Ist es wirklich kostenlos?** GitHub Pages ist für öffentliche Repositorys im kostenlosen Plan von GitHub kostenlos.
Das statische Veröffentlichen gehört zu Exponential. Sie brauchen weiterhin einen Ort, an dem Exponential selbst läuft.

**Brauche ich noch Hosting?** Für Exponential ja: Dort arbeitet die Redaktion. Es kann aber klein sein und muss nicht
öffentlich sein: ein Server im Büro, ein Laptop, eine günstige virtuelle Maschine oder ein CI-Job. Nur die statische
Kopie ist öffentlich.

**Was kostet meine eigene Domain?** Nur die Domain selbst beim Registrar. GitHub liefert sie mit einem kostenlosen
HTTPS-Zertifikat aus ([Eigene Domain verwenden](eigene-domain-verwenden.md)).

**Gibt es Grenzen?** 1 GB je Website, 100 MB je Datei und etwa 100 GB Datenverkehr im Monat bei GitHub Pages
([Grenzen und Bedingungen von GitHub Pages](grenzen-und-bedingungen-von-github-pages.md)).

**Kann ich ein privates Repository nutzen?** GitHub liefert Pages aus privaten Repositorys nur in bezahlten Plänen
aus. Andere Hosts haben eigene Regeln ([Andere Git-Hosts](andere-git-hosts-gitlab-gitea-und-eigener-server.md)).

**Kann ich darüber verkaufen?** Nein: Die Bedingungen von GitHub schließen Shops aus, und ein Warenkorb braucht einen
Server. Lassen Sie einen Shop auf Ihrem eigenen Hosting.

## Redaktion und Inhalte

**Was ändert sich für meine Redaktion?** Nichts an der Arbeitsweise. Ihre Änderungen erreichen die öffentliche Website
mit der nächsten Veröffentlichung, von Hand, beim Veröffentlichen von Inhalten oder nach Zeitplan
([Automatisch veröffentlichen](automatisch-veroeffentlichen.md)).

**Wie schnell gehen Änderungen online?** Ein Build dauert Minuten; GitHub Pages braucht ein, zwei Minuten dazu.

**Kann ich nur eine Seite veröffentlichen?** Ja: Der Bereich **Statisches Veröffentlichen** der Website-Seitenleiste
veröffentlicht die Seite, auf der Sie sind ([Der Bereich in der Website-Seitenleiste](der-bereich-in-der-website-seitenleiste.md)),
und `exp:static:build --nodes=` baut ausgewählte Seiten.

**Kann ich zurück?** Ja, zu jeder früheren Veröffentlichung, in einem Schritt ([Verlauf und Rücknahme](verlauf-und-ruecknahme.md)).

**Kann ich nur einen Teil der Website veröffentlichen?** Ja: Seiten nach Pfad oder Sektion in Schritt 7 des Assistenten
auslassen.

## Was auf einer statischen Site funktioniert

**Suche?** Eine Suchseite, die im Browser läuft, wenn eingeschaltet.
**Formulare?** Sie können an Ihre Live-Website senden oder einen Hinweis mit Link zeigen.
**Kommentare und Anmeldung?** Nicht auf der Kopie; sie werden mit einem kurzen Hinweis ausgelassen.
Einzelheiten: [Suche, Formulare und Kommentare auf einer statischen Site](suche-formulare-und-kommentare-auf-einer-statischen-site.md).

**Funktioniert es mit Exponential Layouts?** Ja. Der Build speichert, was ein Besucher sieht, gleich was es darstellt.

**Mit Smarty- oder Twig-Templates?** Ja, aus demselben Grund.

**Mit einer mehrsprachigen Website?** Ja: ein Ziel je Sprach-Siteaccess, mit den `hreflang`-Links
([Sprachen auf einer statischen Site](sprachen-auf-einer-statischen-site.md)).

**Mit einer Installation mit mehreren Websites?** Ein Ziel je Website
([Statisches Veröffentlichen für Installationen mit mehreren Websites](../../anleitungen/statisches-veroeffentlichen-fuer-multisite-installationen.md)).

## Sicherheit

**Können private Inhalte durchsickern?** Builds laufen als nicht angemeldeter Besucher, und nicht saubere Seiten werden
abgelehnt. Lesen Sie [Private Inhalte privat halten](private-inhalte-privat-halten.md).

**Wo liegt mein GitHub-Token?** Unter Setup > Geheimnisse. Das Ziel enthält nur seinen Namen; das Token erscheint nie in
einem Log.

**Wer darf veröffentlichen?** Wer die Richtlinie `staticpublish/publish` hat, nach **Bestätigen Sie, dass Sie es sind**.

## Im Vergleich

**Warum nicht Jekyll oder Hugo?** Sie machen aus Dateien eine Website; Exponential bietet eine vollständige
Bearbeitungsoberfläche, Rollen, Versionen und Workflows und veröffentlicht das Ergebnis auf dieselbe Weise. Der
Vergleich für Web-Framework-Entwickler steht im Hilfezentrum.

**Kann ich zu Netlify oder Cloudflare Pages veröffentlichen?** Beide können aus einem Git-Repository ausliefern: zu
GitHub oder GitLab veröffentlichen und den Host mit diesem Repository verbinden.

## Nächster Schritt

[In 15 Minuten eine kostenlose Website](in-15-minuten-eine-kostenlose-website.md).
