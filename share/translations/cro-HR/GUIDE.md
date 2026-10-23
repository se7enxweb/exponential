# Croatian (cro-HR): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Croatian as written in Croatia, in its ijekavian form (vrijeme, mjesto, uvjet, primijeniti, pretpregled).
No other locale falls back to this catalogue; Serbian has catalogues of its own, and Serbian or Bosnian forms do not
belong here:

| Use | Not |
|---|---|
| objekt, datoteka, izbornik, popis | objekat, fajl, meni, lista |
| spremiti, stvoriti, uvjet, pogreška | sačuvati, kreirati, uslov, greška |
| postavke, povijest, usluga, tjedan | podešavanja, istorija, servis, nedelja |
| je li / jeste li | da li |

The tone is factual and short, as in the Croatian versions of common operating systems and office software.

## 2. Form of address

- **Buttons, menu items, links and tabs** use the imperative singular: "Spremi", "Odustani", "Ukloni", "Izbriši",
  "Uredi", "Objavi", "Primijeni", "Prenesi", "Preuzmi", "Pretraži", "Odaberi sve", "Natrag". Never the infinitive
  ("Spremiti").
- **Titles, column headings and tabs that name a thing** are nouns: "Struktura sadržaja", "Korisnički računi",
  "Vrsta rasporeda", "Postavke obavijesti".
- **Sentences, hints, questions and error messages** address the user with the formal plural **vi**:
  - "Jeste li sigurni da želite ukloniti ovu klasu?"
  - "Odaberite lokaciju" (Please choose a location)
  - "Nemate dopuštenje za uklanjanje klasa."
  - "Kliknite ikonu za prikaz kontekstnog izbornika."
  - "Postavke povijesti verzija možete promijeniti u datoteci content.ini ..."
- The possessive is "vaš, vaša, vaše", written in lower case inside a sentence.
- Status messages are impersonal or passive: "Objavljeno.", "Nije pronađeno nijedno pokretanje cron-zadataka".
- Not allowed: the familiar form in a sentence (ti, tvoj, možeš, jesi li). "Odaberi" is a button; inside a sentence
  it is "Odaberite".

## 3. Script and typography

- Latin script with č, ć, đ, š, ž. Sentence case: only the first word and proper names are capitalised
  ("Korisnički računi", not "Korisnički Računi").
- Quotation marks in running text: „…“. Where the source has straight quotes around a button name or a value,
  they may stay; inside HTML attributes and JavaScript they always stay straight.
- A placeholder is never declined. Put the count in a label form: "Broj objekata označenih oznakom '%keyword': %count",
  "Broj radnih procesa: %count", or after a noun: "objekt %name".
- Compounds with a code take a hyphen or stand apart as usual in Croatian: "cron-zadatak", "web-mjesto",
  "PDF stranice".

## 4. Glossary

| English | Croatian | Note |
|---|---|---|
| content | sadržaj | |
| content object / object | objekt sadržaja / objekt | never "objekat" |
| node | čvor | main node: glavni čvor |
| location / main location | lokacija / glavna lokacija | |
| content class / class | klasa sadržaja / klasa | class group: grupa klasa |
| attribute | atribut | |
| datatype | vrsta podataka | |
| version | verzija | |
| draft | skica | Store draft: Spremi skicu; Discard draft: Odbaci skicu |
| publish / published | objavi / objavljeno | Send for publishing: Pošalji na objavljivanje |
| siteaccess | pristup web-mjestu | pl. pristupi web-mjestu |
| site | web-mjesto | sitemap: karta web-mjesta |
| section | odjeljak | |
| object state / state | stanje objekta / stanje | |
| role | uloga | |
| policy | pravilo | pl. pravila |
| limitation | ograničenje | |
| permission | dopuštenje | Permission denied: Dopuštenje je odbijeno |
| access denied | pristup odbijen | |
| user / user group | korisnik / korisnička grupa | |
| user accounts | korisnički računi | |
| owner / creator | vlasnik / autor | |
| created / modified | stvoreno / izmijenjeno | column headings |
| cache | predmemorija | Clear cache: Očisti predmemoriju |
| template | predložak | pl. predlošci |
| override | nadjačavanje | override template: predložak za nadjačavanje |
| fallback (language) | zamjenski jezik | fallback chain: lanac zamjenskih jezika |
| design | dizajn | |
| workflow | tijek rada | pl. tijekovi rada |
| trigger / event | okidač / događaj | |
| trash | smeće | |
| bookmarks | oznake | same word as tag; the context tells them apart |
| settings | postavke | |
| Setup (tab) | Postavljanje | |
| Dashboard | Nadzorna ploča | |
| Content structure | Struktura sadržaja | |
| Media library | Medijateka | |
| layout | raspored | layout type: vrsta rasporeda; the name Exponential Layouts stays |
| block / zone | blok / zona | block type: vrsta bloka |
| extension | proširenje | |
| package | paket | |
| module / view | modul / prikaz | |
| search | pretraživanje | button: Pretraži; search engine: tražilica |
| search index / reindex | indeks pretraživanja / ponovno indeksiranje | |
| log / log file | zapisnik / datoteka zapisnika | |
| audit / audit trail | revizija / revizijski trag | |
| job / queue | posao / red čekanja | job queue: red čekanja poslova |
| worker | radni proces | |
| cronjob | cron-zadatak | |
| preview | pretpregled | |
| upload / download | prenesi / preuzmi | noun: prijenos / preuzimanje |
| export / import | izvezi / uvezi | noun: izvoz / uvoz |
| remove | ukloni | Remove selected: Ukloni odabrano |
| delete | izbriši | |
| edit | uredi | |
| save / store | spremi | Store (shop tab): Trgovina |
| discard | odbaci | |
| apply | primijeni | Apply changes: Primijeni promjene |
| cancel | odustani | |
| OK / yes / no | U redu / Da / Ne | |
| back | natrag | |
| next / previous | sljedeća / prethodna | pagination; a wizard step button is "Dalje" |
| create / add | stvori / dodaj | Create new: Stvori novo |
| select / browse | odaberi / pregledaj | Select all: Odaberi sve; Deselect all: Poništi odabir |
| move / copy | premjesti / kopiraj | |
| hide / reveal | sakrij / otkrij | hidden / visible: skriveno / vidljivo |
| enable / disable | omogući / onemogući | enabled / disabled: omogućeno / onemogućeno |
| restore / assign | vrati / dodijeli | |
| approve / reject / pending | odobri / odbij / na čekanju | |
| archive | arhiva | |
| order (shop) | narudžba | pl. narudžbe; order status: status narudžbe |
| order (sorting) | redoslijed | |
| shop / basket / customer / product | trgovina / košarica / kupac / proizvod | |
| VAT / discount / price / currency | PDV / popust / cijena / valuta | |
| wish list | popis želja | |
| newsletter / subscription | bilten / pretplata | |
| tag / keywords | oznaka / ključne riječi | an HTML/XML tag is "oznaka" too |
| URL alias / wildcard | zamjenski URL / zamjenski znak | URL wildcard: URL sa zamjenskim znakom |
| translation / language | prijevod / jezik | main language: glavni jezik; interface language: jezik sučelja |
| locale | lokalizacija | |
| relation / related objects | veza / povezani objekti | reverse related: obrnuto povezani objekti |
| subtree / parent / sub items | podstablo / nadređeni / podstavke | |
| notification / collaboration | obavijest / suradnja | |
| identifier / remote ID | identifikator / udaljeni ID | |
| login (field) / password | korisničko ime / lozinka | |
| log in / log out | prijavi se / odjavi se | |
| e-mail | e-pošta | |
| file / folder / image | datoteka / mapa / slika | |
| item / items | stavka / stavke | |
| name / title / description | naziv / naslov / opis | a person's name: ime |
| type / status | vrsta / status | |
| default / required | zadano / obavezno | |
| error / warning | pogreška / upozorenje | |
| filter / details | filtar / pojedinosti | |
| run / refresh / update / reset | pokreni / osvježi / ažuriraj / poništi | |
| dry run | probno pokretanje | |
| debug | otklanjanje pogrešaka | |
| online editor / template editor | mrežni uređivač / uređivač predložaka | an editing person: urednik |
| provider | davatelj | |
| prompt / model / token | upit / model / token | |
| embedding | ugrađivanje | |
| thread / chain | nit / lanac | |

## 5. Decisions after the translation

- **Store**: the shop tab of the administration header is "Trgovina"; every other "Store" is the verb "Spremi".
- **Next**: "Sljedeća" in page navigation, "Dalje" for the button that moves on to the next step.
