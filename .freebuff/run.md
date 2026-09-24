# Run doc — Daily Report (Laravel)

## Reproduce artifacts (fresh checkout)

1. Copy `.env` from the main checkout (never commit it; adapt `APP_URL`/ports if needed). Keep `database/database.sqlite` too if the app is configured for SQLite.
2. `composer install`
3. `npm install && npm run build` (Vite assets are served from `public/build/`).

## Run the server (Windows)

Start detached with PowerShell (stdout and stderr must go to different files):

```powershell
powershell -NoProfile -Command "(Start-Process -FilePath 'C:\Users\ASUS\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe' -ArgumentList 'artisan','serve','--host=127.0.0.1','--port=8000' -WorkingDirectory 'C:\Users\ASUS\Documents\antigravity\daily-report' -RedirectStandardOutput '<log>' -RedirectStandardError '<log>.err' -WindowStyle Hidden -PassThru).Id"
```

- Default port **8000**; if taken, use another free port and register that URL.
- Verify: `curl http://127.0.0.1:8000/` returns 200, then `register_preview` with the URL and printed pid.
- Public form: `/` — Admin/HRD login: `/admin/login`.
