<<<<<<< HEAD
# min_historia
My web site for ancestory media.
=======
# Passkeys (WebAuthn) Starter – PHP + MySQL (www.bolandgren.se)

Detta är en komplett startmall för att köra passkeys på **https://www.bolandgren.se** med **RP_ID = bolandgren.se**.
Använder **lbuchs/webauthn** (gratis, via Composer). Kräver **HTTPS**.

## Snabbstart
1) Kör:
```
composer require lbuchs/webauthn
```
2) Skapa MySQL-databas och kör `schema.sql`.
3) Uppdatera `lib/config.php` vid behov (DB-uppgifter).
4) Ladda upp allt till din server bakom **https://www.bolandgren.se** och öppna `public/index.html`.

## Tips
- Håll dig till **en** origin (www eller apex) för mindre förvirring.
- För produktion: lägg till felhantering, CORS/origin-kontroll, rate limiting, CSRF-skydd där det är relevant.
>>>>>>> de0ced2 (Första commit av projektet)
