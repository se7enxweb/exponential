# Kurdish, Kurmanji (kur-TR): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Northern Kurdish (Kurmanji) in the Latin (Hawar) alphabet, the standard written language of Kurdish Wikipedia, of
the free software translations into Kurmanji and of the major Kurdish news media. The tone is factual and short.
No other locale reads this catalogue: Sorani (`ckb-IQ`, Arabic script) is a different language and is not
served by it.

Where Kurmanji has no settled word, use the loanword the field already uses (cache, token, plugin, kernel) rather
than a new coinage; a long text may add the English term in parentheses at its first mention, a label never does.

## 2. Form of address

The user is addressed with the formal plural **hûn**, everywhere and without exception.

- **Buttons, menu items, links, tabs, column headings and titles** use the verbal noun (infinitive) or a noun,
  with no address: Tomarkirin (Save), Jêbirin (Delete), Rakirin (Remove), Sererastkirin (Edit), Betal (Cancel),
  Sepandin (Apply), Weşandin (Publish), Barkirin (Upload), Hemûyan hilbijartin (Select all), Reşnivîs avêtin
  (Discard draft), Li nîşankeran zêde kirin (Add to bookmarks).
- **Sentences, hints, questions and error messages** use the plural imperative in -in and the plural verb:
  - Hûn piştrast in ku hûn dixwazin van hêmanan rakin? (Are you sure you want to remove these items?)
  - Ji kerema xwe cihekî hilbijêrin (Please choose a location)
  - Destûra we tune ku hûn çînan rakin. (You do not have permission to remove classes.)
  - Pelê ku hûn dixwazin bar bikin hilbijêrin. (Select the file that you want to upload.)
- Possessives: "… we" or "… xwe" (şîfreya xwe binivîsin).
- "Please" is "Ji kerema xwe", followed by the plural imperative.
- **Not allowed**: the singular tu as the pronoun "you" and its forms (te, ya te, yê te, dixwazî, the singular
  imperative hilbijêre). The negative determiner tu ("no, any": tu çîn tune ne) is ordinary Kurmanji and is fine.

## 3. Script and typography

- Alphabet: a b c ç d e ê f g h i î j k l m n o p q r s ş t u û v w x y z. The long vowels ê, î, û are always
  written. **Never the Turkish letters ı, ğ, ö, ü.**
- Capitals: a sentence starts with a capital; labels capitalise only the first word, except names.
- The izafe is written in full: navê bikarhêner, mîhengên pergalê, koma bikarhêneran.
- A placeholder never takes a suffix (not `%namê`): build the sentence so it stands free ("heyber bi navê %name",
  "Hejmar: %count").
- Punctuation as in English; quotation marks "" or “ ” as in the source.
- Numbers: digits 0–9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Kurmanji | Note |
|---|---|---|
| content | naverok | |
| content object / object | heybera naverokê / heyber | |
| node | girêk | |
| location | cih | main location = cihê sereke |
| content class / class | çîna naverokê / çîn | class group = koma çînan |
| attribute | taybetmendî | |
| datatype | cureyê daneyê | |
| version | guherto | |
| draft | reşnivîs | My drafts = Reşnivîsên min |
| publish / published | weşandin / weşandî | |
| translation / language | werger / ziman | main language = zimanê sereke |
| siteaccess | gihîştina malperê | plural gihîştinên malperê |
| site | malper | |
| design | sêwiran | |
| section | beş | the Exponential section only |
| object state / state group | rewşa heyberê / koma rewşan | |
| role / policy | rol / polîtîka | |
| permission / access | destûr / gihîştin | Access denied = Gihîştin hate redkirin |
| limitation | sînorkirin | |
| user / user group | bikarhêner / koma bikarhêneran | anonymous user = bikarhênerê nenas |
| user account / My account | hesabê bikarhêner / Hesabê min | |
| log in / log out | têketin / derketin | sentence: têkevin / derkevin |
| password / username | şîfre / navê bikarhêner | |
| e-mail | e-name | |
| cache / clear cache | cache / paqijkirina cache | |
| template | şablon | |
| override | şûnguhertin | override template = şablona şûnguhertinê |
| fallback | yedek | fallback chain = zincîra yedek |
| workflow / workflow event | herikîna kar / bûyera herikîna kar | |
| trigger | tetik | |
| trash / empty trash | çopdank / valakirina çopdankê | |
| bookmark | nîşanker | |
| settings | mîheng | configuration = veavakirin |
| Setup (admin tab) | Sazkirin | |
| Dashboard | Panela kontrolê | |
| Content structure / Media library | Avahiya naverokê / Pirtûkxaneya medyayê | |
| layout / block / zone | plansazî / blok / herêm | Exponential Layouts stays as is |
| extension | pêvek | |
| plugin | plugin | never pêvek |
| package | pakêt | |
| module / view / function | modul / dîtin / fonksiyon | |
| search / search engine | lêgerîn / motora lêgerînê | |
| search index / index / reindex | îndeksa lêgerînê / îndeks / ji nû ve îndekskirin | |
| log / log file | tomar / pelê tomarê | |
| audit trail | şopa kontrolê | |
| cronjob | karê demsazkirî | |
| job / queue / worker | kar / rêz / xebatkar | |
| preview | pêşdîtin | |
| upload / download | barkirin / daxistin | |
| export / import | derxistin / anîn | |
| remove | rakirin | take out of a list or place |
| delete | jêbirin | destroy |
| edit / save / store | sererastkirin / tomarkirin / tomarkirin | "Store" is the save button; only the shop tab of the header is Firoşgeh |
| discard | avêtin | |
| apply / cancel / OK | sepandin / betal / temam | |
| back / next / previous | paşve / pêşve / yê berê | |
| yes / no | erê / na | |
| add / create / new | lêzêdekirin / afirandin / nû | |
| move / copy | livandin / kopîkirin | |
| close / refresh | girtin / ji nû ve barkirin | |
| update / reset / restore | nûkirin / ji nû ve danîn / vegerandin | software update = rojanekirin |
| select / select all | hilbijartin / hemûyan hilbijartin | |
| enable / disable | çalak kirin / neçalak kirin | enabled = çalak, disabled = neçalak |
| visible / hidden | xuya / veşartî | hide = veşartin, reveal = eşkerekirin |
| archive | arşîv | |
| order (shop) / order (sorting) | sipariş / rêz | |
| sort / ascending / descending | rêzkirin / ji kêm ber bi zêde / ji zêde ber bi kêm | |
| tag (keyword, eztags) | etîket | |
| tag (HTML, XML) / custom tag | tag / taga taybet | |
| keyword | peyva sereke | |
| owner / creator | xwedî / afirîner | |
| created / modified (column) | dîroka afirandinê / dîroka guhertinê | in a sentence: hate afirandin / hate guhertin |
| relation / related objects | têkilî / heyberên têkildar | reverse related = têkiliya berevajî |
| parent / sub-items | hêmana jorîn / binhêman | item = hêman |
| subtree | binedar | |
| ID / identifier / remote ID | ID / nasker / nasnameya dûr | node ID = nasnameya girêkê |
| URL alias / URL wildcard | nasnavê URL / jokerê URL | |
| priority / default / required | pêşîtî / standard / pêwîst | |
| status, state | rewş | |
| warning / error | hişyarî / çewtî | |
| notification | agahdarî | |
| collaboration | hevkarî | |
| information collection / collected information | berhevkirina agahiyan / agahiyên berhevkirî | |
| file / folder / image / media | pel / peldank / wêne / medya | |
| path / browse | rê / gerîn | |
| database | danegeh | |
| statistics / system information | statîstîk / agahiyên pergalê | |
| install / uninstall | sazkirin / rakirina sazkirinê | |
| shop / basket / customer / product | firoşgeh / selik / xerîdar / berhem | |
| VAT / discount / price / currency | BNZ / erzankirin / biha / dirav | VAT in full: baca nirxê zêdekirî |
| wish list | lîsteya daxwazan | |
| newsletter / subscription | nûçename / abonetî | |
| session | danişîn | |
| field | qad | |
| format | format | |
| feed | feed | |
| tab | tab | |
| theme | tema | |
| menu / toolbar | menû / darika amûran | |
| name / title / description / type / value / date | nav / sernav / danasîn / cure / nirx / dîrok | |
| AI / model / prompt / token | zekaya çêkirî / model / prompt / token | |
| embedding / chunk / vector | embedding / perçe / vektor | |
| provider | dabînker | |
| deploy, deployment | bicihkirin | |
| mapping | tayînkirin | |
| editor | edîtor | |
| interface language | zimanê navrûyê | |
| admin interface | panela rêveberiyê | |

## 5. Decisions after the translation

Terms settled when the translations were compared, mostly to keep two English words from sharing one Kurmanji word.

| English | Kurmanji | Note |
|---|---|---|
| Approve / Approval | pejirandin | approved = pejirandî, approver = pejirîner; only for the approval workflow |
| Accept | qebûlkirin | not pejirandin |
| Confirm | piştrastkirin | not pejirandin |
| Reject / Deny | redkirin | rejected = redkirî |
| Validate / valid | derbasdarî kontrol kirin / derbasdar | |
| Pending | li bendê | |
| Container (class property) | konteyner | |
| Flags (class properties) | nîşane | flag of a language, CLI flag = ala |
| Locale | koda herêmî (locale) | the INI key `Locale` stays |
| Charset | koma tîpan | |
| Wildcard / placeholder | joker / cihgir | |
| Redirect | beralîkirin | |
| License / permission | lîsans / destûr | never swapped |
| Embed / embedded | hundirxistin / hundirxistî | not bicihkirin, which is deploy; insert = têxistin |
| Link / linked | girêdan / girêdayî | node stays girêk |
| Thumbnail | wêneyê biçûk | plural wêneyên biçûk |
| Summary / teaser / digest | kurte / kurtenivîs / kurteya komkirî | |
| Preset | pêşmîheng | |
| Collection (one submission of an information collection) | berhevok | other collections = kom |
| Batch | deste | not kom |
| Overview | nêrîna giştî | |
| Rich text | nivîsa dewlemend | |
| Server / client | pêşkêşkar / klîent | client ≠ customer (xerîdar) |
| Release / upgrade | release / nûjenkirin | release is not weşan, which is publish |
| Kernel / core (Solr) | kernel / core | |
| Thread (process) / thread (conversation) | thread / gotûbêj | |
| Lock / locked | kilît / kilîtkirî | not girtî, which is closed |
| Label | nav | not etîket, which is tag |
| Storage / repository / registry | embar / depo / qeydgeh | |
| Entry, record | tomar | |
| Maintainer | parêzvan | |
| String / array | rêzenivîs / array | |
| Route / render / header | rêgeh / amadekirin / sernivîs | |
| Alert | alarm | not hişyarî, which is warning |
| Scope / assign | çarçove / veqetandin | |
| Revoke | vekişandin | not betal kirin, which is cancel |
| Checkout (shop) / receipt | dravdan / meqbûz | |
| Interface / process / transaction | navrû / pêvajo / danûstandin | |
| Online / offline / option / feature | serhêl / derhêl / vebijark / taybetî | feature ≠ attribute (taybetmendî) |
