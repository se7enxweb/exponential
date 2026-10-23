# Dutch (dut-NL): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Dutch as used in the Netherlands, spelled after the Woordenlijst (Groene Boekje). Flemish Dutch (`dut-BE`)
falls back to this catalogue, so avoid words that only one side uses. The tone is clear, modern and informal, as in
the Dutch versions of common operating systems and office software; avoid stiff words (wensen, indien, gelieve,
dient te): write willen, als, moet.

## 2. Form of address

- **Buttons, menu items, links and commands** use the infinitive, with the object first: "Opslaan", "Verwijderen",
  "Annuleren", "Bewerken", "Toevoegen", "Nieuw aanmaken", "Selectie verwijderen", "Wijzigingen toepassen",
  "Concept opslaan", "Blok bewerken".
- **Titles, column headings and tabs** are nouns: "Inhoudsstructuur", "Gebruikersaccounts", "Layouttype",
  "Notificatie-instellingen".
- **Sentences, hints, questions and error messages** address the user with the informal **je** ("jouw" only for
  emphasis); instructions use the imperative:
  - "Weet je zeker dat je deze klasse wilt verwijderen?"
  - "Kies een locatie" (Please choose a location)
  - "Je hebt geen rechten om klassen te verwijderen."
  - "Klik op de knop "Annuleren" en probeer alleen de locaties te verwijderen die je mag verwijderen."
  - "Je kunt bewerken"
- After inversion the verb loses its -t: "Wil je", "Heb je", "Kun je", "Weet je".
- A button named in a sentence is quoted the way the button reads: "Klik op de knop "Opslaan"".
- Not allowed: the formal u and uw, and the imperative on a button ("Sla op").

## 3. Script and typography

- Latin script, with the diaeresis where the spelling needs it (kopiëren, geüpload). Sentence case, not English
  title case: "Inhoudsstructuur", "Recente items".
- Quotation marks as in the source.
- Compounds are written as one word ("klassegroep", "zoekindex", "takenwachtrij"); after a code, an abbreviation or
  a name with a hyphen: "INI-bestand", "URL-alias", "node-ID", "object-ID", "REST-provider", "Exponential-project",
  "e-mailadres".
- The time unit "h" is written "uur", not "u".
- Counts: "%count objecten", or a label form: "Objecten: %count".

## 4. Glossary

| English | Dutch | Note |
|---|---|---|
| content | inhoud | |
| content object / object | inhoudsobject / object | pl. objecten |
| node | node | pl. nodes; main node: hoofdnode |
| location / main location | locatie / hoofdlocatie | |
| content class / class | inhoudsklasse / klasse | pl. klassen; class group: klassegroep |
| attribute | attribuut | pl. attributen |
| datatype | datatype | |
| version | versie | |
| draft | concept | pl. concepten; Store draft: Concept opslaan; Discard draft: Concept verwerpen |
| publish / published | publiceren / gepubliceerd | Send for publishing: Ter publicatie verzenden |
| siteaccess | siteaccess | pl. siteaccessen |
| site | site | |
| section | sectie | |
| object state / state | objectstatus / status | pl. statussen |
| role | rol | pl. rollen |
| policy | beleidsregel | |
| limitation | beperking | |
| permission / access denied | recht / toegang geweigerd | pl. rechten |
| user / user group | gebruiker / gebruikersgroep | |
| user accounts | gebruikersaccounts | |
| owner / creator | eigenaar / maker | |
| created / modified | aangemaakt / gewijzigd | column headings |
| cache | cache | Clear cache: Cache legen |
| template | template | pl. templates |
| override | override | override template: override-template |
| fallback | fallback | fallback chain: fallbackketen |
| design | design | |
| workflow | workflow | |
| trigger / event | trigger / gebeurtenis | |
| trash | prullenbak | |
| bookmarks | favorieten | |
| settings | instellingen | |
| Setup (tab) | Setup | |
| Dashboard | Dashboard | |
| Content structure | Inhoudsstructuur | |
| Media library | Mediabibliotheek | |
| layout | layout | layout type: layouttype |
| block / zone | blok / zone | |
| extension | extensie | |
| package | pakket | |
| module / view | module / weergave | the button "View": Bekijken |
| search | zoeken | search engine: zoekmachine |
| search index / reindex | zoekindex / herindexering | verb: opnieuw indexeren |
| log / log file | log / logbestand | |
| audit / audit trail | audit / audittrail | |
| job / queue | taak / wachtrij | job queue: takenwachtrij |
| worker | worker | |
| cronjob | cronjob | |
| preview | voorbeeld | |
| upload / download | uploaden / downloaden | |
| export / import | exporteren / importeren | |
| remove / delete | verwijderen | Remove selected: Selectie verwijderen |
| edit | bewerken | |
| save / store | opslaan | Store (shop tab): Winkel |
| discard | verwerpen | |
| apply | toepassen | Apply changes: Wijzigingen toepassen |
| cancel | annuleren | |
| OK / yes / no | OK / ja / nee | |
| back / next / previous | terug / volgende / vorige | |
| create / add | aanmaken / toevoegen | |
| select / browse | selecteren / bladeren | Select all: Alles selecteren; Deselect all: Alles deselecteren |
| move / copy | verplaatsen / kopiëren | |
| hide / reveal | verbergen / zichtbaar maken | hidden / visible: verborgen / zichtbaar |
| enable / disable | inschakelen / uitschakelen | enabled / disabled: ingeschakeld / uitgeschakeld |
| restore / assign | herstellen / toewijzen | |
| approve / reject / pending | goedkeuren / afwijzen / in afwachting | |
| archive | archief | |
| order (shop) | bestelling | order status: bestelstatus |
| order (sorting) | volgorde | |
| shop / basket / customer / product | winkel / winkelwagen / klant / product | |
| VAT / discount / price / currency | btw / korting / prijs / valuta | |
| wish list | verlanglijst | |
| newsletter / subscription | nieuwsbrief / abonnement | |
| tag / keywords | tag / trefwoorden | |
| URL alias / wildcard | URL-alias / wildcard | |
| translation / language | vertaling / taal | main language: hoofdtaal; interface language: interfacetaal |
| locale | locale | |
| relation / related objects | relatie / gerelateerde objecten | reverse related: omgekeerd gerelateerde objecten |
| subtree / parent / sub items | substructuur / bovenliggend item / subitems | children: onderliggende items |
| notification / collaboration | notificatie / samenwerking | |
| identifier / remote ID | identificatie / remote-ID | |
| login (field) / password | login / wachtwoord | username: gebruikersnaam |
| log in / log out | inloggen / uitloggen | |
| e-mail | e-mail | |
| file / folder / image | bestand / map / afbeelding | |
| item / items | item / items | |
| name / title / description | naam / titel / beschrijving | |
| type / status | type / status | |
| default / required | standaard / verplicht | |
| error / warning | fout / waarschuwing | |
| filter / details | filteren / details | |
| run / refresh / update / reset | uitvoeren / vernieuwen / bijwerken / terugzetten | |
| dry run | proefrun | |
| debug | debug | |
| editor (software) / editor (person) | editor / redacteur | template editor: template-editor |
| provider | provider | |
| prompt / model / token | prompt / model / token | |
| embedding | embedding | |
| thread / chain | thread / keten | |

## 5. Decisions after the translation

- **Store**: the shop tab of the administration header is "Winkel"; every other "Store" is the verb "Opslaan".
- **Remove and delete** are both "verwijderen"; where a text needs the difference, say what is removed from where
  ("uit de lijst verwijderen").
