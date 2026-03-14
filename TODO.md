# TODO: Fix Admin Geo Login

## ✅ Completed
- [x] 1. Create config/geo.php
- [x] 2. Create app/Http/Middleware/VerifyLocation.php  
- [x] 3. Create app/Http/Controllers/GeoController.php
- [x] 4. Edit routes/web.php (add POST /verify-location)
- [x] 5. Edit resources/views/auth/login.blade.php (add JS)
- [x] 6. Register middleware in bootstrap/app.php
- [x] 7. Update AuthenticatedSessionController.php

## ✅ Completed (4/7)\n- [x] 1. Increase geo radius to 5km\n- [x] 2. Fix login.blade.php JS\n- [x] 3. Fix AuthController session\n- [x] 4. Clear caches\n\n## ⏳ Test Steps\n- [ ] Login admin → GPS → dashboard\n- [ ] Check laravel.log\n- [ ] Update TODO complete\n
<parameter name="path">c:/laragon/www/louwescare_fix/config/geo.php
