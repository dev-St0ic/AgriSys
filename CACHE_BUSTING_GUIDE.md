# Cache Busting Implementation Guide

## What Was Done

I've implemented a **version-based cache busting system** to ensure users always get the latest CSS and JS files without manually clearing their browser cache.

## Changes Made

### 1. Added Asset Version Configuration

-   **File**: `config/app.php`
-   **Added**: `'asset_version' => env('ASSET_VERSION', '1.0.0')`
-   This reads the version from your `.env` file

### 2. Updated `.htaccess` Cache Headers

-   **File**: `public/.htaccess`
-   Files with `?v=` parameter are cached for 1 year (since version changes = new URL)
-   Files without version must revalidate (no cache)

### 3. Created Helper Function

-   **File**: `app/Helpers/AssetHelper.php`
-   **Function**: `versioned_asset()`
-   Makes it easy to add version to any asset

### 4. Registered Helper

-   **File**: `composer.json`
-   Added helper to autoload files

### 5. Updated Landing Page

-   **File**: `resources/views/landingPage/landing.blade.php`
-   Added `?v={{ config('app.asset_version') }}` to all CSS and JS files

## How to Use

### Method 1: Using the Config (Current Implementation)

```blade
<link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ config('app.asset_version') }}">
<script src="{{ asset('js/landing.js') }}?v={{ config('app.asset_version') }}"></script>
```

### Method 2: Using the Helper Function (Recommended)

After running `composer dump-autoload`, you can use:

```blade
<link rel="stylesheet" href="{{ versioned_asset('css/landing.css') }}">
<script src="{{ versioned_asset('js/landing.js') }}"></script>
```

## Deployment Workflow

### Every Time You Deploy Changes:

1. **Update the version in `.env` file**:

    ```env
    ASSET_VERSION=1.0.1
    ```

    Or use timestamp:

    ```env
    ASSET_VERSION=20260112
    ```

2. **Run composer dump-autoload** (if using helper function):

    ```bash
    composer dump-autoload
    ```

3. **Deploy your files**

4. **Users automatically get the new version** - no cache clearing needed!

## Automated Version with Git Commit Hash (Advanced)

If you want to automatically version based on your Git commit:

### Option A: In `.env` (Manual)

```env
ASSET_VERSION=abc123def
```

### Option B: Dynamic (More Advanced)

Edit `config/app.php`:

```php
'asset_version' => env('ASSET_VERSION', exec('git rev-parse --short HEAD') ?: time()),
```

This will use:

-   Git commit hash if available
-   Current timestamp as fallback

## Update All Views

You need to update **all your Blade templates** to use versioned assets. Here's what to look for:

### Find All Asset Calls

```bash
# Search for all asset() calls
grep -r "asset(" resources/views/
```

### Replace Pattern

**Old**:

```blade
{{ asset('css/style.css') }}
{{ asset('js/script.js') }}
```

**New**:

```blade
{{ asset('css/style.css') }}?v={{ config('app.asset_version') }}
{{ asset('js/script.js') }}?v={{ config('app.asset_version') }}
```

Or with helper:

```blade
{{ versioned_asset('css/style.css') }}
{{ versioned_asset('js/script.js') }}
```

## Testing

1. **Set initial version**:

    ```env
    ASSET_VERSION=1.0.0
    ```

2. **Load your site** - View source and check URLs:

    ```html
    <link href="http://yoursite.com/css/landing.css?v=1.0.0" />
    ```

3. **Change the version**:

    ```env
    ASSET_VERSION=1.0.1
    ```

4. **Refresh the page** - URLs should now show:

    ```html
    <link href="http://yoursite.com/css/landing.css?v=1.0.1" />
    ```

5. Browser will fetch the new file automatically!

## Additional Cache Control (Optional)

### For Production Server

Add to your `.env.production`:

```env
ASSET_VERSION=1.0.0
APP_ENV=production
```

### For Apache/Nginx

The `.htaccess` is already configured with proper headers:

-   Versioned files (with `?v=`): Cached for 1 year
-   Non-versioned files: Must revalidate
-   Images: Cached for 1 year

## Troubleshooting

### Issue: Users still see old files

**Solution**: Make sure you incremented the `ASSET_VERSION` in `.env`

### Issue: Helper function not found

**Solution**: Run `composer dump-autoload`

### Issue: Config not updating

**Solution**: Clear Laravel config cache:

```bash
php artisan config:clear
```

### Issue: Version not showing in URLs

**Solution**: Check that you're using either:

-   `?v={{ config('app.asset_version') }}`
-   OR `{{ versioned_asset('path') }}`

## Best Practices

1. ✅ **Increment version** with every deployment that changes CSS/JS
2. ✅ **Use semantic versioning**: 1.0.0 → 1.0.1 → 1.1.0
3. ✅ **Or use timestamps**: 20260112-1430
4. ✅ **Don't version images** (unless they change frequently)
5. ✅ **Keep old files** on server for at least 24 hours after deployment

## Quick Reference

| Action         | Command/Code                                                       |
| -------------- | ------------------------------------------------------------------ |
| Update version | Edit `.env`: `ASSET_VERSION=1.0.1`                                 |
| Use in Blade   | `{{ asset('css/style.css') }}?v={{ config('app.asset_version') }}` |
| Use helper     | `{{ versioned_asset('css/style.css') }}`                           |
| Reload helper  | `composer dump-autoload`                                           |
| Clear config   | `php artisan config:clear`                                         |

## What Happens Now

-   ✅ Every CSS/JS file has a version parameter
-   ✅ When you update `ASSET_VERSION`, browsers fetch new files
-   ✅ No more manual cache clearing needed
-   ✅ Users always see the latest version
-   ✅ Old cached files won't interfere

## Next Steps

1. Run `composer dump-autoload` to load the helper
2. Update remaining Blade templates (admin views, etc.)
3. Set your initial version in `.env`
4. Test by changing version and refreshing

---

**Need help?** The implementation is complete for your landing page. You can apply the same pattern to all other views!
