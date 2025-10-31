# Auth Fusion Commands

This document describes all available artisan commands in the Auth Fusion package.

## Install Driver Command

Install and configure authentication drivers with a single command.

### Usage

```bash
php artisan auth-fusion:install {driver} [options]
```

### Arguments

- `driver` - The driver to install (required)
  - `sanctum` - Install Laravel Sanctum driver
  - `jwt` - Install JWT driver (tymon/jwt-auth)
  - `passport` - Install Passport driver
  - `all` - Install all drivers

### Options

- `--model=User` - Specify the model to add traits to (default: User)
- `--force` - Force migrations to run even if tables already exist

### Examples

#### Basic Installation

```bash
# Install Sanctum driver
php artisan auth-fusion:install sanctum

# Install JWT driver
php artisan auth-fusion:install jwt

# Install Passport driver
php artisan auth-fusion:install passport
```

#### Advanced Usage

```bash
# Install Sanctum with custom model
php artisan auth-fusion:install sanctum --model=Admin

# Install all drivers at once
php artisan auth-fusion:install all

# Install JWT with custom model
php artisan auth-fusion:install jwt --model=Staff

# Force migrations to run (even if tables exist)
php artisan auth-fusion:install sanctum --force
```

### What the Command Does

The `auth-fusion:install` command performs the following actions:

#### For Sanctum:
1. Checks if Laravel Sanctum is already installed
2. Prompts to install via composer (requires manual execution)
3. Publishes Sanctum configuration files
4. Runs database migrations
5. Adds `HasApiTokens` trait to your User model
6. Updates `.env` file with `AUTH_FUSION_DRIVER=sanctum`

#### For JWT:
1. Checks if tymon/jwt-auth is already installed
2. Prompts to install via composer (requires manual execution)
3. Publishes JWT configuration files
4. Generates JWT secret key
5. Updates `.env` file with `AUTH_FUSION_DRIVER=jwt`

#### For Passport:
1. Checks if Laravel Passport is already installed
2. Prompts to install via composer (requires manual execution)
3. Runs database migrations
4. Creates OAuth clients
5. Adds `HasApiTokens` trait to your User model
6. Updates `.env` file with `AUTH_FUSION_DRIVER=passport`

#### For All:
Runs all three driver installations sequentially.

### Model Trait Addition

The command automatically adds the required trait to your model:

**Before:**
```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password',
    ];
}
```

**After (for Sanctum/Passport):**
```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'name', 'email', 'password',
    ];
}
```

### Notes

- The command will not modify your model if the trait already exists
- Composer commands must be run manually for security reasons
- All changes are logged to the console
- The command is idempotent (safe to run multiple times)

### Migration Handling

The command intelligently handles existing database tables:

#### Default Behavior (No --force)

If migration tables already exist, the command will:
- Show a warning that tables exist
- Skip the migration step
- Continue with other configuration steps
- Display a tip to use `--force` if you want to run migrations anyway

**Example:**
```bash
php artisan auth-fusion:install sanctum

⚠️  Migration tables already exist. Skipping migrations.
💡 Tip: Use --force flag to run migrations anyway.
✓ Model updated with HasApiTokens trait
✓ .env configured
```

#### Force Behavior (With --force)

If you need to run migrations despite existing tables:

```bash
php artisan auth-fusion:install sanctum --force
```

This will attempt to run migrations. Note: Most Laravel migrations will still fail if tables exist, but this gives you control over the behavior.

### Troubleshooting

#### Command not found

If the command doesn't appear:

1. Clear Laravel caches:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. Re-register the package:
   ```bash
   composer dump-autoload
   ```

#### Model not found

If your model path is different:

- Specify the exact model name: `--model=YourModelName`
- Models should be in `app/Models/` directory
- Supports both `User.php` and `AppUser.php` styles

#### Trait not added

If the trait wasn't added to your model:

1. Check the model exists at `app/Models/YourModel.php`
2. Ensure the class name matches the filename
3. Manually add the trait if needed

### Output Examples

#### Successful Installation

```
🚀 Installing sanctum driver for Auth Fusion...
📦 Installing Laravel Sanctum...
Sanctum is already installed!
Publishing Sanctum configuration...
✓ Configuration published
Running migrations...
✓ Migrations completed
✓ Added HasApiTokens trait to User
✓ Updated AUTH_FUSION_DRIVER in .env
✅ Sanctum driver installed successfully!

🎉 Installation Complete!
✓ Sanctum installed
✓ Migrations run
✓ Model updated with HasApiTokens trait
✓ .env configured

Next steps:
1. Run: composer require laravel/sanctum (if not already done)
2. Clear caches: php artisan config:clear
3. Start using: AuthFusion::driver()->login($credentials)
```

#### Already Installed

```
🚀 Installing sanctum driver for Auth Fusion...
📦 Installing Laravel Sanctum...
✓ User already uses HasApiTokens trait
✓ Sanctum driver installed successfully!
```

### Next Steps

After installation:

1. **Clear caches:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. **Start using the driver:**
   ```php
   use AuthFusion;
   
   $result = AuthFusion::driver()->login([
       'email' => 'user@example.com',
       'password' => 'password'
   ]);
   ```

3. **Test the installation:**
   ```bash
   php artisan tinker
   >>> AuthFusion::getDefaultDriver()
   ```

For more information, see the [Getting Started Guide](GETTING_STARTED.md) and [Usage Examples](USAGE_EXAMPLES.md).

