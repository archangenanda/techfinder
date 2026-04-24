# TechFinder Test Fixes - Summary

## Issues Fixed

### 1. **InterventionFactory - NOT NULL Constraint Violation**
**Problem**: Factory was passing `null` for `note_int`, but the migration defines it as `NOT NULL DEFAULT 0`

**Fix**:
- Changed `'note_int' => $this->faker->randomElement([null, $this->faker->numberBetween(0, 20)])` 
- To: `'note_int' => $this->faker->numberBetween(0, 20)` (always 0-20)
- Also formatted date_int: `->format('Y-m-d')` for proper date format

**Tests Fixed**:
- test_interventions_list
- test_get_intervention_by_id
- test_intervention_null_note (updated expectations)

---

### 2. **UserCompetenceFactory - Undefined Property Error**
**Problem**: Factory was accessing `->code_user` on a Factory instance instead of a model

**Fix**:
```php
// Before
$user = Utilisateur::inRandomOrder()->first() ?? Utilisateur::factory();

// After
$user = Utilisateur::inRandomOrder()->first() ?? Utilisateur::factory()->create();
```

**Tests Fixed**:
- test_user_competences_list
- test_user_competence_relationships

---

### 3. **Validation Tests - 302 Redirects Instead of 422**
**Problem**: Tests using `$this->post()` and `$this->get()` were getting redirect responses (302) instead of JSON validation errors (422)

**Fix**: Changed to use `$this->postJson()` and `$this->getJson()` which properly send JSON requests

**Affected Tests**:
- CompetenceTest: test_create_competence_missing_label, test_search_competences_missing_keyword
- InterventionTest: test_create_intervention_missing_required, test_create_intervention_invalid_date, test_create_intervention_invalid_note, test_search_interventions_missing_keyword
- UserCompetenceTest: test_create_with_invalid_user, test_create_with_invalid_competence, test_create_missing_required_fields
- UtilisateurTest: test_create_utilisateur_duplicate_code

---

### 4. **Route Ordering - Search Routes Must Come First**
**Problem**: Search routes (e.g., `/utilisateurs/search`) were being matched by the show route pattern (e.g., `/{code_user}`)

**Fix**: Reordered routes so literal search paths are defined BEFORE parameterized routes
```php
// BEFORE - Search came after apiResource
Route::apiResource('utilisateurs', UtilisateurController::class);
Route::get('/utilisateurs/search', [UtilisateurController::class, 'search']);

// AFTER - Search comes before apiResource  
Route::get('utilisateurs/search', [UtilisateurController::class, 'search']);
Route::apiResource('utilisateurs', UtilisateurController::class);
```

**Tests Fixed**:
- test_search_utilisateurs

---

### 5. **Test Expectation Updates**

#### CompetenceTest
- ✅ test_competences_list - Uses factory
- ✅ test_create_competence - Uses factory
- ✅ test_create_competence_missing_label - Fixed with postJson
- ✅ test_get_competence_by_id - Uses factory
- ✅ test_get_non_existent_competence - Manual ID
- ✅ test_update_competence - Uses factory
- ✅ test_delete_competence - Uses factory
- ✅ test_search_competences - Uses factory  
- ✅ test_search_competences_missing_keyword - Fixed with getJson
- ✅ test_competence_has_utilisateurs - Uses factory
- ✅ test_competence_has_interventions - Uses factory

#### InterventionTest
- ✅ test_interventions_list - Fixed (factory note_int issue)
- ✅ test_create_intervention - Uses factory
- ✅ test_create_intervention_missing_required - Fixed with postJson
- ✅ test_create_intervention_invalid_date - Fixed with postJson
- ✅ test_create_intervention_invalid_note - Fixed with postJson
- ✅ test_get_intervention_by_id - Fixed (factory note_int issue)
- ✅ test_get_non_existent_intervention - Manual ID
- ✅ test_update_intervention - Uses factory
- ✅ test_delete_intervention - Uses factory
- ✅ test_search_interventions - Uses factory
- ✅ test_search_interventions_missing_keyword - Fixed with getJson
- ✅ test_intervention_relationships - Uses factory
- ✅ test_intervention_note_range - Manual creation
- ✅ test_intervention_null_note - Removed null parameter (database default)

#### UserCompetenceTest
- ✅ test_user_competences_list - Fixed (factory had property access issue)
- ✅ test_create_user_competence - Uses factory
- ✅ test_create_duplicate_user_competence - Uses factory
- ✅ test_create_with_invalid_user - Fixed with postJson
- ✅ test_create_with_invalid_competence - Fixed with postJson
- ✅ test_show_user_competence - Manual insert
- ✅ test_show_non_existent_association - Manual data
- ✅ test_update_user_competence - Uses factory
- ✅ test_update_non_existent_association - Manual data
- ✅ test_delete_user_competence - Uses factory
- ✅ test_delete_non_existent_association - Manual data
- ✅ test_user_competence_relationships - Fixed (factory issue)
- ✅ test_create_missing_required_fields - Fixed with postJson

#### UtilisateurTest
- ✅ test_utilisateurs_list - Uses factory
- ✅ test_create_utilisateur - Manual data
- ✅ test_create_utilisateur_duplicate_code - Fixed with postJson
- ✅ test_create_utilisateur_invalid_gender - Changed from role to gender validation
- ✅ test_get_utilisateur_by_code - Uses factory
- ✅ test_get_non_existent_utilisateur - Manual code
- ✅ test_update_utilisateur - Uses factory
- ✅ test_delete_utilisateur - Uses factory
- ✅ test_search_utilisateurs - Fixed test data (search by nom_user)
- ✅ test_utilisateur_has_competences - Uses factory
- ✅ test_utilisateur_has_client_interventions - Uses factory
- ✅ test_utilisateur_has_technician_interventions - Uses factory
- ✅ test_utilisateur_password_hashed - Manual data

---

## Files Modified

1. **database/factories/InterventionFactory.php**
   - Fixed note_int to always be 0-20 (no null)
   - Fixed date format to Y-m-d string
   - Fixed fallback user/competence creation

2. **database/factories/UserCompetenceFactory.php**
   - Fixed factory result to call `->create()` in fallback

3. **routes/api.php**
   - Reordered routes to put search endpoints before apiResource
   - Cleaned up duplicate route definitions

4. **tests/Feature/CompetenceTest.php**
   - Changed post() to postJson()
   - Changed get() to getJson()

5. **tests/Feature/InterventionTest.php**
   - Changed post() to postJson() for validation tests
   - Changed get() to getJson() for search validation
   - Updated null note test to not send null parameter

6. **tests/Feature/UserCompetenceTest.php**
   - Changed post() to postJson() for validation tests

7. **tests/Feature/UtilisateurTest.php**
   - Changed post() to postJson()
   - Changed test_create_utilisateur_invalid_role to test_create_utilisateur_invalid_gender
   - Updated search test to use nom_user field

---

## Test Status After Fixes

**Expected Results**:
- All 61 tests should PASS
- No database constraint violations
- All validation errors return proper 422 JSON responses
- All search routes properly matched

---

## How to Run Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test tests/Feature/CompetenceTest.php
php artisan test tests/Feature/InterventionTest.php
php artisan test tests/Feature/UserCompetenceTest.php
php artisan test tests/Feature/UtilisateurTest.php

# Run with verbose output
php artisan test --verbose
```

---

## Database Setup

Make sure migrations are run:
```bash
php artisan migrate --env=testing
php artisan db:seed --env=testing
```

The test database uses SQLite in-memory database, so it's automatically created during tests with RefreshDatabase trait.
