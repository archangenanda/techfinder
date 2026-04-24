# Guide d'Authentification - TechFinder API

## Overview

L'API TechFinder utilise **Laravel Sanctum** pour gérer l'authentification. Sanctum offre une solution légère pour l'authentification basée sur les tokens d'accès personnels, idéale pour les APIs mobiles et monolithiques.

---

## Configuration de Sanctum

### 1. Installation
Sanctum est déjà installé dans ce projet. Pour installer dans un nouveau projet :
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### 2. Modèle User
Le modèle `User` utilise le trait `HasApiTokens` de Sanctum :
```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
}
```

### 3. Configuration d'Authentification
Le guard `sanctum` est configuré dans `config/auth.php` :
```php
'guards' => [
    'sanctum' => [
        'driver' => 'sanctum',
        'provider' => 'users',
    ],
],
```

---

## API Endpoints

### 1. Registration (Inscription)
**Endpoint:** `POST /api/auth/register`

**Authentification:** Non requise

**Payload:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2026-04-20T12:00:00Z",
        "updated_at": "2026-04-20T12:00:00Z"
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer"
}
```

**Validation Errorsเนอ (422):**
- Email invalide
- Mot de passe trop court (< 8 caractères)
- Mots de passe ne correspondent pas
- Email déjà utilisé

---

### 2. Login (Connexion)
**Endpoint:** `POST /api/auth/login`

**Authentification:** Non requise

**Payload:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200):**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2026-04-20T12:00:00Z",
        "updated_at": "2026-04-20T12:00:00Z"
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer"
}
```

**Error Response (401):**
```json
{
    "message": "The provided credentials are incorrect."
}
```

---

### 3. Get Current User (Utilisateur Actualisé)
**Endpoint:** `GET /api/auth/me`

**Authentification:** Requise (Bearer Token)

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response (200):**
```json
{
    "message": "User authenticated",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2026-04-20T12:00:00Z",
        "updated_at": "2026-04-20T12:00:00Z"
    }
}
```

**Error Response (401):**
```json
{
    "message": "Unauthenticated."
}
```

---

### 4. Logout (Déconnexion)
**Endpoint:** `POST /api/auth/logout`

**Authentification:** Requise (Bearer Token)

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response (200):**
```json
{
    "message": "Logout successful"
}
```

**Error Response (401):**
```json
{
    "message": "Unauthenticated."
}
```

---

### 5. Refresh Token (Rafraîchir le Token)
**Endpoint:** `POST /api/auth/refresh`

**Authentification:** Requise (Bearer Token)

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response (200):**
```json
{
    "message": "Token refreshed successfully",
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer"
}
```

**Comportement:**
- L'ancien token est invalidé
- Un nouveau token est généré
- Peut être utilisé pour prolonger la session

---

## Utilisation des Tokens

### Format du Header d'Authentification
Tous les endpoints protégés nécessitent le header `Authorization` au format :
```
Authorization: Bearer {access_token}
```

L'`access_token` reçu pendant la registration ou le login doit être inclus dans chaque requête.

### Exemple avec cURL
```bash
# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'

# Get current user
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer {access_token}"

# Logout
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer {access_token}"
```

### Exemple avec JavaScript (Fetch API)
```javascript
// Login
const loginResponse = await fetch('http://localhost:8000/api/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        email: 'john@example.com',
        password: 'password123'
    })
});

const { access_token } = await loginResponse.json();

// Get current user
const meResponse = await fetch('http://localhost:8000/api/auth/me', {
    headers: { 'Authorization': `Bearer ${access_token}` }
});

const user = await meResponse.json();
```

### Exemple avec Axios
```javascript
// Login and set default header
const response = await axios.post('/api/auth/login', {
    email: 'john@example.com',
    password: 'password123'
});

const token = response.data.access_token;
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

// Now all requests include the token
const user = await axios.get('/api/auth/me');
```

---

## Sécurité

### Bonnes Pratiques

1. **Stockage du Token**
   - Stockez le token de manière sécurisée (ex: localStorage, sessionStorage, ou cookie HTTP-only)
   - **Ne le stockez pas en localStorage pour les applications sensibles** (risque XSS)
   - Utilisez `httpOnly` cookies pour une meilleure sécurité

2. **Expiration du Token**
   - Les tokens n'expirent pas par défaut dans Sanctum
   - Considérez l'implémentation d'une expiration si nécessaire
   - Utilisez le endpoint `/api/auth/refresh` pour générer un nouveau token

3. **Confirmation du Token**
   - Toujours inclure le header `Authorization` avec le format `Bearer {token}`
   - Le système rejette les requêtes sans authentification appropriée

4. **HTTPS**
   - Utilisez HTTPS en production
   - Les tokens en HTTP plaintext sont vulnérables

5. **Token Rotation**
   - Régulièrement appeler `/api/auth/refresh` pour obtenir un nouveau token
   - L'ancien token devient invalide après le rafraîchissement

---

## Tests

Les tests d'authentification sont localisés dans `tests/Feature/AuthTest.php` et couvrent :
- ✅ Inscription avec validation
- ✅ Connexion avec vérification des credentials
- ✅ Récupération de l'utilisateur authentifié
- ✅ Déconnexion
- ✅ Rafraîchissement du token
- ✅ Gestion des erreurs d'authentification

Pour exécuter les tests :
```bash
php artisan test tests/Feature/AuthTest.php
```

---

## Routes Protégées

Les routes de l'API qui nécessitent l'authentification utilisent le middleware `auth:sanctum` :

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
});
```

Pour protéger d'autres routes :
```php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('competences', CompetenceController::class, ['only' => ['store', 'update', 'destroy']]);
});
```

---

## Dépannage

### Token invalide ou expiré
```json
{
    "message": "Unauthenticated."
}
```

**Solution:** Regénérez le token en utilisant le endpoint `/api/auth/login` ou `/api/auth/refresh`

### Credentials incorrects
```json
{
    "message": "The provided credentials are incorrect."
}
```

**Solution:** Vérifiez l'email et le mot de passe

### Validation échouée
```json
{
    "message": "Validation failed",
    "errors": {
        "email": ["Email doit être valide"],
        "password": ["Le mot de passe doit contenir au moins 8 caractères"]
    }
}
```

**Solution:** Vérifiez les données envoyées selon les règles de validation

---

## Schéma de la Table `personal_access_tokens`

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | ID unique du token |
| tokenable_type | string | Type du modèle (App\Models\User) |
| tokenable_id | bigint | ID de l'utilisateur |
| name | string | Nom du token |
| token | string (unique) | Valeur du token hachée |
| abilities | text | Permissions du token |
| last_used_at | timestamp | Dernière utilisation |
| expires_at | timestamp | Date d'expiration |
| created_at | timestamp | Création |
| updated_at | timestamp | Modification |

---

## Ressources Supplémentaires

- [Documentation Laravel Sanctum](https://laravel.com/docs/11.x/sanctum)
- [Authentication Concepts](https://laravel.com/docs/11.x/authentication)
- [API Authentication](https://laravel.com/docs/11.x/authentication#api-authentication)
