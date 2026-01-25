# TODO LIST

- [ ] Sistemare l'utilizzo di Response in PostController

## Middleware
- [ ] AuthMiddleware
- [ ] CsrfMiddleware
- [ ] ValidationMiddleware



## PostController

### POST /api/posts/create

Crea un nuovo post.

#### Headers
```http
Content-Type: application/json
```

#### Body
```json
{
  "title": "Titolo del post",
  "content": "Contenuto del post",
  "community_id": 1
}
```

---

### POST /api/posts/search

Ricerca e filtra i post.

#### Headers
```http
Content-Type: application/json
```

#### Parametri
- `order` (obbligatorio): `"asc"` o `"desc"`
- `community_id` (opzionale): ID della community
- `tail` (opzionale): ID dell’ultimo post visualizzato
- `categories` (opzionale): array di categorie
- `tags` (opzionale): array di tag (richiede `community_id`)
- `search` (opzionale): termine di ricerca nel titolo o contenuto
- `author_id` (opzionale): ID dell’autore

#### Response
```json
{
  "ok": true,
  "error": null,
  "posts": [
    {
      "id": 14,
      "title": "Titolo del post",
      "content": "Contenuto del post",
      "attachments": [],
      "author_id": "mario",
      "community_id": 4,
      "created_at": "1767475873",
      "tags": ["tag1", "tag2"],
      "category": "news",
      "like_count": 10,
      "dislike_count": 3,
      "liked_by_user": true,
      "comment_count": 5
    }, ...
  ]
}
```

---

### GET /api/posts/{id}/comments

Recupera i commenti di un post specifico.

#### Response
```json
{
  "ok": true,
  "error": null,
  "comments": [
    {
      "id": 1,
      "post_id": 14,
      "author_id": "luigi",
      "content": "Contenuto del commento",
      "created_at": "1767475900"
    }, ...
  ]
}
```

---

### POST /api/posts/{id}/comments

Aggiunge un commento a un post specifico.

#### Headers
```http
Content-Type: application/json
```

#### Parametri 
- `content` (obbligatorio): Contenuto del commento
- `reply_to` (opzionale): ID del commento a cui rispondere

#### Response
```json
{
    "ok": true,
    "error": null
}
```

---

### DELETE /api/posts/{id}

Elimina un post specifico.

#### Response
```json
{
    "ok": true,
    "error": null
}
```

---

### POST /api/posts/{id}/like

Mette like/dislike o rimuove da un post specifico.

#### Headers
```http
Content-Type: application/json
```

#### Parametri
- `action` (obbligatorio): `"like"`, `"dislike"` o `"remove"`

---

### DELETE /api/posts/{id}/comments/{comment\_id}

Elimina un commento specifico da un post. Ricordare di verificare se l’utente è l’autore del commento.



## CommunityController
Gestione delle community.

### GET /communities
Mostra la lista delle community.

### GET /communities/{id}
Pagina dettagliata di una community.



## UserController
Gestione degli utenti.

### GET /users/{id}
Mostra il profilo di un utente.

Il file è da finire ma è un buon punto di partenza.

