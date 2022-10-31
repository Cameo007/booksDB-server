# Lesedatenbank API Documentation

### API-Endpoint: *https://YOUR_HOST/api/lesedatenbank.php*

## Categories:
---
### addCategory (POST)
```
{
	"cmd": "addCategory",
	"username": "BENUTZERNAME",
	"password": "PASSWORT",
	"categoryName": "NAME_DER_KATEGORIE"
}
```
---
### deleteCategory (POST)
```
{
	"cmd": "deleteCategory",
	"username": "BENUTZERNAME",
	"password": "PASSWORT",
	"categoryName": "NAME_DER_KATEGORIE"
}
```
---
### renameCategory (POST)
```
{
	"cmd": "renameCategory",
	"username": "BENUTZERNAME",
	"password": "PASSWORT",
	"oldCategoryName": "ALTER_NAME_DER_KATEGORIE",
	"newCategoryName": "NEUER_NAME_DER_KATEGORIE"
}
```
---
### getCategories (GET)
```
{
	"cmd": "getCategories",
	"username": "BENUTZERNAME",
	"password": "PASSWORT"
}
```
---

## Bücher:
---
### addBook (POST)
```
{
	"cmd": "addBook",
	"username": "BENUTZERNAME",
	"password": "PASSWORT",
	"categoryName": "KATEGORIE",
	"bookName": "NAME_DES_BUCHS",
	"author": "AUTOR_DES_BUCHS",
	"read": GELESEN? (true/false)
}
```
---
### deleteBook (POST)
```
{
	"cmd": "deleteBook",
	"username": "BENUTZERNAME",
	"password": "PASSWORT",
	"categoryName": "KATEGORIE",
	"bookName": "NAME_DES_BUCHS"
}
```
---
### editBook (POST)
```
{
	"cmd": "editBook",
	"username": "BENUTZERNAME",
	"password": "PASSWORT",
	"categoryName": "KATEGORIE",
	"oldBookName": "ALTER_NAME_DES_BUCHES",
	"newBookName": "NEUER_NAME_DES_BUCHES",
	"author": "AUTOR_DES_BUCHS",
	"read": GELESEN? (true/false)
}
```
---
### getBooks (GET)
```
{
	"cmd": "getBooks",
	"username": "BENUTZERNAME",
	"password": "PASSWORT",
	"categoryName": "KATEGORIE"
}
```
---

## Sonstiges:
### register (POST)
```
{
	"cmd": "register",
	"username": "BENUTZERNAME",
	"password": "PASSWORT"
}
```
---
### changeUsername (POST)
```
{
	"cmd": "changeUsername",
	"username": "BENUTZERNAME",
	"password": "PASSWORT",
	"newUsername": "NEUER_BENUTZERNAME"
}
```
---
### changePassword (POST)
```
{
	"cmd": "changePassword",
	"username": "BENUTZERNAME",
	"password": "PASSWORT",
	"newPassword": "NEUES_Passwort"
}
```
---
### deleteAccount (POST)
```
{
	"cmd": "deleteAccount",
	"username": "BENUTZERNAME",
	"password": "PASSWORT"
}
```
---
### isUsernameAvailable (GET)
```
{
	"cmd": "isUsernameAvailable",
	"name": "BENUTZERNAME"
}
```
---
### authenticate (GET)
```
{
	"cmd": "authenticate",
	"username": "BENUTZERNAME",
	"password": "PASSWORT"
}
```
---

## Ergebnis:
zB.:
```
{
	"content": "Buch hinzugefügt."
}
```
