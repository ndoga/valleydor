# Valley d'Or Padel

Nuovo sito di [valleydor.it](https://www.valleydor.it/), il centro padel di La Ciaccia – Valledoria (SS):
due campi panoramici a 100 metri dal mare.

## Contenuto

| Cartella | Cosa contiene |
| --- | --- |
| `valleydor-padel/` | **Tema WordPress** da installare sul sito |
| `index.html`, `css/`, `js/`, `img/` | Anteprima statica senza WordPress (pubblicata su GitHub Pages): solo grafica, senza area riservata |

## Anteprima per il proprietario

**https://ndoga.github.io/valleydor/demo.html** – pagina da mandare al proprietario, con due link:

- il sito (anteprima statica su GitHub Pages);
- l'area riservata di prova su [WordPress Playground](https://playground.wordpress.net/), che gira nel
  browser: installa il tema dal ramo `main` di questo repository, crea l'utente `gestore` (password
  `demo`, esiste solo nella demo), ricrea la configurazione del sito di prova (prezzi, extra, corsi,
  galleria, sponsor, tornei, testi della Clinic) e si apre sul pannello.
  Le modifiche restano nel browser di chi prova e spariscono chiudendo la pagina.

La ricetta è `demo/blueprint.json`, generata da `demo/genera-blueprint.py` a partire da
`demo/prepara-demo.php`: per cambiarla modificare il PHP e rigenerare. Ogni push su `main` aggiorna
anche la demo, perché Playground scarica il tema dal repository a ogni apertura.

Per portare nella demo la configurazione attuale di un WordPress con il tema:

```bash
php demo/esporta-dal-sito.php C:/percorso/del/wordpress
python demo/genera-blueprint.py
```

Il primo comando scrive `demo/dati.json` e copia in `demo/media/` le foto caricate a mano; la demo le
scarica da GitHub Pages all'avvio.

## Installare il tema

1. Creare lo zip della cartella `valleydor-padel` (la cartella stessa deve stare dentro lo zip).
2. WordPress › **Aspetto › Temi › Aggiungi nuovo › Carica tema**, scegliere lo zip e attivarlo.
   In alternativa copiare la cartella via FTP in `wp-content/themes/`.

La home usa `front-page.php`, qualunque sia l'impostazione di *Impostazioni › Lettura*.
Pagine interne, articoli e 404 usano `index.php`.

## Area riservata per il titolare

L'accesso è quello di WordPress (`/wp-login.php`, link **Area riservata** in fondo al sito), con la grafica
del sito. Per il titolare conviene creare un utente con il ruolo **Gestore del centro**
(*Utenti › Aggiungi nuovo*): dopo l'accesso arriva direttamente al pannello e vede solo
Valley d'Or, Tornei, Articoli, Media, Aspetto e Profilo. Non può installare plugin, gestire utenti,
cambiare tema o impostazioni.

### Pannello "Valley d'Or" (bacheca)

Una sola pagina, salvataggio con un pulsante.

**Tariffe del campo**, **Extra** e **Corsi** sono tabelle: ogni voce si aggiunge, si toglie e si
riordina trascinandola, e ha tre comandi:

- **Visibile**: spento, la voce resta salvata ma non compare sul sito
- **In offerta a**: se compilato, sul sito il prezzo normale compare barrato con il badge "Offerta"
- **In evidenza**: bordo e badge con un'etichetta a scelta (es. "Più scelto", "Novità")

Dettagli per tipo:

- **Tariffe**: nome, prezzo del campo, giocatori (il prezzo a persona è calcolato da solo),
  cosa include (una riga per voce)
- **Extra**: nome e prezzo
- **Corsi**: nome, descrizione e un elenco di prezzi (per chi, prezzo, testo dopo il prezzo come
  "/ ora", offerta). Senza prezzi sul sito compare "chiedi info". Il primo corso in evidenza si
  apre da solo.
- **Orari** di apertura
- **Galleria** e **Sponsor**: foto scelte dalla libreria media, si riordinano trascinandole.
  In home compaiono 9 foto, le altre con "Vedi tutte le foto". Finché non si sceglie nulla
  il sito mostra le foto predefinite del tema.

### Tornei (menu Tornei)

Ogni torneo ha titolo, descrizione, locandina, data, orario, categoria, quota e link di iscrizione
(se manca, il pulsante "Iscriviti" apre WhatsApp con un messaggio pronto). I tornei futuri compaiono
da soli in home (i 3 più vicini); l'elenco completo è su `/tornei/`.

### Personalizza

Da **Aspetto › Personalizza › Valley d'Or** le cose che cambiano di rado: contatti, link Playtomic e
social, coordinate della mappa, shortcode di un modulo contatti (es. Contact Form 7), nome in grande,
testi e foto dell'apertura, foto dell'istruttore. Il logo si cambia da *Identità del sito*.

## Struttura del tema

| File | Ruolo |
| --- | --- |
| `style.css` | Intestazione del tema e tutta la grafica |
| `functions.php` | Caricamento di stili e script, valori predefiniti, impostazioni del Personalizza, dati strutturati |
| `inc/listini.php` | Tariffe, extra e corsi: valori iniziali, prezzi, offerte, pulizia dei dati |
| `inc/pannello.php` | Pannello "Valley d'Or" nella bacheca (prezzi, orari, corsi, galleria, sponsor) |
| `inc/tornei.php` | Tipo di contenuto Torneo, dettagli, colonne nella bacheca, prossimi tornei |
| `inc/accesso.php` | Pagina di accesso, ruolo "Gestore del centro", bacheca semplificata |
| `header.php`, `footer.php` | Menu, piè di pagina con area riservata, pulsante fisso "Prenota" su mobile, lightbox |
| `front-page.php` | Home a pagina unica |
| `index.php` | Pagine interne, articoli, tornei, archivi, 404 |
| `assets/js/main.js` | Animazioni, menu mobile, contatori, galleria |
| `assets/js/pannello.js`, `assets/css/pannello.css` | Scelta e ordinamento delle foto nel pannello |
| `assets/css/login.css` | Grafica della pagina di accesso |

I valori del pannello e del Personalizza sono salvati come `theme_mod` (tariffe, extra e corsi come
elenchi: `tariffe`, `extra`, `corsi`); i dettagli dei tornei come
meta del post (`_vd_data`, `_vd_orario`, `_vd_categoria`, `_vd_quota`, `_vd_iscrizioni`).

Requisiti: WordPress 6.0+, PHP 7.4+. Nessun plugin necessario.
