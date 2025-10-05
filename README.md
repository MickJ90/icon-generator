# 🧩 Laravel Icon Generator

**Laravel Icon Generator** converte automaticamente i tuoi file **SVG** in **componenti Blade** pronti all’uso,
normalizzando i colori (`fill` e `stroke`) e gestendo correttamente i casi complessi (es. `circle`, `g`, `fill="none"`).  
Perfettamente compatibile con **Laravel 8 → 12**.

---

## 🚀 Installazione

Puoi installare il pacchetto direttamente via **Composer**:

```bash
composer require mickj/laravel-icon-generator
```


⚙️ Utilizzo

1️⃣ Inserisci i tuoi file .svg nella cartella predefinita:

```bash
resources/icons/
```

2️⃣ Esegui il comando Artisan:

```bash
php artisan icons:generate
```
3️⃣ Troverai le icone convertite qui:

```bash
resources/views/components/icons/
```

Ogni file verrà generato in formato:

```bash
<svg {{ $attributes->merge(['class' => 'inline']) }} ... >
    ...
</svg>
```


🧠 Esempio

Se hai un file:

```bash
resources/icons/freccia.svg
```

verrà generato:

```bash
resources/views/components/icons/freccia.blade.php
```

E potrai usarlo direttamente in Blade:

```bash
<x.icons.freccia class="w-6 h-6 text-blue-500" />
```


🎨 Regole di conversione SVG

Tutti i fill diversi da none → diventano currentColor

Tutti i stroke diversi da none → diventano currentColor

I tag <circle> non vengono alterati

Il tag <svg> include automaticamente:

```bash
{{ $attributes->merge(['class' => 'inline']) }}
```
per integrarsi con Tailwind CSS


🧩 Opzioni del comando

Puoi specificare una cartella personalizzata:

```bash
php artisan icons:generate public/svg
```

In questo caso, le icone verranno prese da public/svg e salvate comunque in resources/views/components/icons.


💾 Output di esempio

```bash
✔ Icona [freccia] generata.
✔ Icona [profilo-utente] generata.
✔ Icona [stella-piena] generata.
Tutte le icone sono state generate in [resources/views/components/icons]
```


🧰 Compatibilità

```bash
| Laravel | Supporto |
| ------- | -------- |
| 8.x     | ✅        |
| 9.x     | ✅        |
| 10.x    | ✅        |
| 11.x    | ✅        |
| 12.x    | ✅        |
```

Richiede PHP ≥ 8.1


🧑‍💻 Autore

Michele Depalma
```bash
https://packagist.org/packages/mickj/laravel-icon-generator
```


📄 Licenza

Rilasciato sotto licenza MIT
.
© 2025 Michele Depalma
