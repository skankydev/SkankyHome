# Client HTTP sortant

Référence détaillée. Vue d'ensemble dans [CLAUDE.md](../CLAUDE.md).

`SkankyDev\Utilities\HttpClient` : petit wrapper cURL pour les requêtes **sortantes** (API externes, llama-server…). Sans état : chaque requête renvoie un `HttpResult` autoporteur.

```php
$client = new HttpClient();
$res = $client->timeout(60)->post($url, ['messages' => [...]]); // $data POSTé en JSON (Content-Type auto)

if ($res->ok()) {            // 2xx
    $data = $res->json();    // body décodé (ou ->body() pour le brut)
}
```

- Méthodes : `get($url, $query)` / `post($url, $data)` / `request($method, $url, $data)` → `HttpResult`.
- Setters fluent cumulables : `withHeader()` / `withHeaders()` / `timeout($s)` (utile pour l'inférence LLM, lente).
- `HttpResult` : `status()`, `ok()`, `failed()` (erreur transport **ou** ≥ 400), `body()`, `json()`, `header()` (insensible à la casse), `error()`.
- Échec réseau → `HttpResult` avec `status() === 0` et `error()` rempli (pas d'exception).
- ⚠️ Ne pas confondre `HttpResult` (réponse *reçue* d'un distant) avec `SkankyDev\Http\Response` (réponse *sortante* vers le navigateur).
