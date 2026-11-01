# Valley d'Or Padel

Nuovo sito di [valleydor.it](https://www.valleydor.it/), il centro padel di La Ciaccia – Valledoria (SS):
due campi panoramici a 100 metri dal mare.

## Contenuto

| Cartella | Cosa contiene |
| --- | --- |
| `valleydor-padel/` | **Tema WordPress** da installare sul sito |
| `index.html`, `css/`, `js/`, `img/` | Anteprima statica (senza WordPress) usata per definire la grafica |

## Installare il tema

1. Creare lo zip della cartella `valleydor-padel` (la cartella stessa deve stare dentro lo zip).
2. WordPress › **Aspetto › Temi › Aggiungi nuovo › Carica tema**, scegliere lo zip e attivarlo.
   In alternativa copiare la cartella via FTP in `wp-content/themes/`.

La home usa `front-page.php`, qualunque sia l'impostazione di *Impostazioni › Lettura*.
Pagine interne, articoli e 404 usano `index.php`.

## Modificare i contenuti

Da **Aspetto › Personalizza › Valley d'Or**, senza toccare il codice:

- **Contatti e prenotazioni**: telefono, WhatsApp, email, indirizzo, orari, coordinate della mappa,
  link Playtomic, Facebook, Instagram, shortcode di un modulo contatti (es. Contact Form 7)
- **Apertura della pagina**: nome in grande, testi, foto di sfondo
- **Prezzi**: campo 1 ora e 1 ora e 30 (il prezzo a persona è calcolato da solo), extra
- **Corsi**: foto dell'istruttore, prezzo della scuola junior
- **Galleria** (9 foto) e **Sponsor** (12 loghi): una foto rimossa viene saltata

Il logo si cambia da *Identità del sito*.

## Struttura del tema

| File | Ruolo |
| --- | --- |
| `style.css` | Intestazione del tema e tutta la grafica |
| `functions.php` | Caricamento di stili e script, valori predefiniti, impostazioni del Personalizza, dati strutturati |
| `header.php`, `footer.php` | Menu, piè di pagina, pulsante fisso "Prenota" su mobile, lightbox |
| `front-page.php` | Home a pagina unica |
| `index.php` | Pagine interne, articoli, archivi, 404 |
| `assets/js/main.js` | Animazioni, menu mobile, contatori, galleria |

Requisiti: WordPress 6.0+, PHP 7.4+. Nessun plugin necessario.
