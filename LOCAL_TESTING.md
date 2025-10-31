# Local Testing Guide

This guide explains how to test the Auth Fusion package locally before publishing to Packagist.

## Option 1: Using Path Repository (Recommended)

This method links the package directly from your local filesystem.

### Steps:

1. **Navigate to your Laravel project:**
   ```bash
   cd /path/to/your/laravel/project
   ```

2. **Add the path repository to your `composer.json`:**
   ```json
   {
       "repositories": [
           {
               "type": "path",
               "url": "../auth-fusion"
           }
       ]
   }
   ```
   
   Adjust the path relative to your Laravel project.

3. **Install the package:**
   ```bash
   composer require obrainwave/auth-fusion @dev
   ```
   
   The `@dev` constraint allows unstable versions.

4. **Symlink will be created:**
   ```bash
   vendor/obrainwave/auth-fusion -> ../../auth-fusion
   ```

## Option 2: Using composer link

This is useful for active development.

### Steps:

1. **In the auth-fusion directory:**
   ```bash
   cd auth-fusion
   composer link
   ```

2. **In your Laravel project:**
   ```bash
   cd /path/to/your/laravel/project
   composer link obrainwave/auth-fusion
   ```

## Option 3: Create a Git Tag (For Testing)

1. **Initialize git in auth-fusion:**
   ```bash
   cd auth-fusion
   git init
   git add .
   git commit -m "Initial commit"
   git tag v1.0.0
   ```

2. **Add as a git repository in composer.json:**
   ```json
   {
       "repositories": [
           {
               "type": "vcs",
               "url": "file:///C:/xampp/htdocs/auth-fusion"
           }
       ],
       "require": {
           "obrainwave/auth-fusion": "^1.0"
       }
   }
   ```

## Option 4: Use Orchestra Testbench

For package development, use the built-in tests:

```bash
cd auth-fusion
composer install
vendor/bin/phpunit
```

## Windows-Specific Notes

For Windows paths:
- Use forward slashes in composer.json: `C:/xampp/htdocs/auth-fusion`
- Or use relative paths: `../../auth-fusion`

## After Installation

1. **Publish config:**
   ```bash
   php artisan vendor:publish --tag=auth-fusion-config
   ```

2. **Test the package:**
   ```php
   use Obrainwave\AuthFusion\Facades\AuthFusion;
   
   // In your routes or controller
   Route::get('/test-auth-fusion', function() {
       return AuthFusion::getDefaultDriver();
   });
   ```

## Common Issues

### Issue: "Could not find a version matching minimum-stability"

**Solution:** Add `@dev` constraint:
```bash
composer require obrainwave/auth-fusion @dev
```

### Issue: Changes not reflecting

**Solution:** Run composer update:
```bash
composer update obrainwave/auth-fusion
```

### Issue: Autoload issues

**Solution:** Clear caches and regenerate:
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

## Testing Checklist

- [ ] Package installs successfully
- [ ] Config file publishes correctly
- [ ] Service provider loads
- [ ] Facade is accessible
- [ ] Can switch between drivers
- [ ] Each driver works correctly
- [ ] No autoload errors
- [ ] Tests pass

## Publishing to Packagist

Once testing is complete:

1. Create a GitHub repository
2. Push your code
3. Submit to Packagist
4. After approval, install normally:
   ```bash
   composer require obrainwave/auth-fusion
   ```

