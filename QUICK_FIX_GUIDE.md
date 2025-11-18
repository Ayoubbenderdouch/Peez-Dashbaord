# 🔧 QUICK FIX GUIDE - Post Implementation

## Run These Commands After Implementation:

```bash
# 1. Run the new migration
php artisan migrate

# 2. Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 3. Run tests to verify everything works
php artisan test

# 4. (Optional) Assign vendors to existing shops
php artisan tinker
# Then run:
# $vendor = \App\Models\User::where('is_vendor', true)->first();
# \App\Models\Shop::whereNull('vendor_id')->update(['vendor_id' => $vendor->id]);
# exit

# 5. Start the server
php artisan serve
```

## Test the New Features:

1. **Login**: http://localhost:8000/admin
   - Email: admin@peez.dz
   - Password: password

2. **Dashboard**: Look for Quick Actions widget at top

3. **Test Shop Validation**:
   - Go to Shops → Create
   - Try to create a shop with existing neighborhood+category combo
   - Should see validation error

4. **Test Subscription Actions**:
   - Go to Subscriptions
   - Find an active subscription
   - Click "Extend" → Choose months → Submit
   - Click "Cancel" → Confirm

5. **Test Notification Page**:
   - Visit: http://localhost:8000/admin/send-notification
   - Select segment, template, fill message
   - Click "Send Notification"

## Troubleshooting:

### Issue: "Column 'vendor_id' not found"
**Fix:**
```bash
php artisan migrate:fresh --seed
```
⚠️ **Warning**: This will delete all data!

### Issue: "SendNotification page not found"
**Fix:**
```bash
php artisan filament:upgrade
php artisan config:clear
```

### Issue: "Quick Actions widget not showing"
**Fix:**
The widget should auto-discover. If not, check `AdminPanelProvider.php` has:
```php
->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
```

### Issue: Arabic text not showing correctly
**Fix:**
Ensure your browser supports Arabic fonts. The panel should auto-detect RTL direction.

### Issue: Subscription actions not visible
**Fix:**
Make sure you're viewing an **active** subscription. Cancelled/expired subscriptions hide the actions.

## Environment Variables:

Add to your `.env` file (optional):

```env
# Localization
APP_LOCALE=ar
APP_FALLBACK_LOCALE=en

# FCM (for real push notifications)
FCM_SERVER_KEY=your_firebase_server_key_here
```

## Done! 🎉

Your PEEZ Dashboard is now fully upgraded with all critical features!
