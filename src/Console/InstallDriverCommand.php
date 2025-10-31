<?php

namespace Obrainwave\AuthFusion\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallDriverCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth-fusion:install 
                            {driver : The driver to install (sanctum, jwt, passport, all)}
                            {--model=User : The model to add traits to (default: User)}
                            {--force : Force the operation to run when migrations already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install and configure authentication drivers for Auth Fusion';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $driver = $this->argument('driver');
        $model = $this->option('model');

        $this->info("🚀 Installing {$driver} driver for Auth Fusion...");

        switch ($driver) {
            case 'sanctum':
                return $this->installSanctum($model);
            case 'jwt':
                return $this->installJWT();
            case 'passport':
                return $this->installPassport($model);
            case 'all':
                return $this->installAll($model);
            default:
                $this->error("Invalid driver: {$driver}. Available: sanctum, jwt, passport, all");
                return 1;
        }
    }

    /**
     * Install Sanctum driver.
     *
     * @param string $modelName
     * @return int
     */
    protected function installSanctum(string $modelName): int
    {
        $this->info('📦 Installing Laravel Sanctum...');

        // Check if already installed
        $isInstalled = class_exists(\Laravel\Sanctum\HasApiTokens::class);
        
        if ($isInstalled) {
            $this->warn('Sanctum is already installed!');
        } else {
            $this->executeComposerInstall('laravel/sanctum', '*');
            // Re-check after installation
            $isInstalled = class_exists(\Laravel\Sanctum\HasApiTokens::class);
        }

        // Run configuration if installed
        if ($isInstalled) {
            // Publish Sanctum service provider
            $this->info('Publishing Sanctum configuration...');
            $this->call('vendor:publish', ['--provider' => 'Laravel\Sanctum\SanctumServiceProvider']);

            // Run migrations
            $this->info('Running migrations...');
            $this->runMigrations();

            // Update model
            $this->updateModelWithTrait($modelName, 'Laravel\Sanctum\HasApiTokens', 'HasApiTokens');
            
            // Set default driver
            $this->updateEnvFile('AUTH_FUSION_DRIVER', 'sanctum');
        }

        $this->displayNextSteps('sanctum');

        return 0;
    }

    /**
     * Install JWT driver.
     *
     * @return int
     */
    protected function installJWT(): int
    {
        $this->info('📦 Installing tymon/jwt-auth...');

        // Check if already installed
        $isInstalled = class_exists(\Tymon\JWTAuth\JWT::class);
        
        if ($isInstalled) {
            $this->warn('JWT is already installed!');
        } else {
            $this->executeComposerInstall('tymon/jwt-auth', '*');
            // Re-check after installation
            $isInstalled = class_exists(\Tymon\JWTAuth\JWT::class);
        }

        // Run configuration if installed
        if ($isInstalled) {
            // Publish JWT config
            $this->info('Publishing JWT configuration...');
            $this->call('vendor:publish', ['--provider' => 'Tymon\JWTAuth\Providers\LaravelServiceProvider']);

            // Generate JWT secret
            $this->info('Generating JWT secret...');
            try {
                $this->call('jwt:secret', ['--force' => true]);
                $this->info('✓ JWT secret generated successfully');
            } catch (\Exception $e) {
                $this->error('Could not generate JWT secret: ' . $e->getMessage());
                $this->warn('Please run manually: php artisan jwt:secret');
            }
            
            // Add JWTSubject trait and interface to User model
            $this->info('Updating User model with JWT support...');
            $this->updateModelWithJWT('User');
            
            // Set default driver
            $this->updateEnvFile('AUTH_FUSION_DRIVER', 'jwt');
        }

        $this->displayNextSteps('jwt');

        return 0;
    }

    /**
     * Install Passport driver.
     *
     * @param string $modelName
     * @return int
     */
    protected function installPassport(string $modelName): int
    {
        $this->info('📦 Installing Laravel Passport...');

        // Check if already installed
        $isInstalled = class_exists(\Laravel\Passport\Client::class);
        
        if ($isInstalled) {
            $this->warn('Passport is already installed!');
        } else {
            $this->executeComposerInstall('laravel/passport', '*');
            // Re-check after installation
            $isInstalled = class_exists(\Laravel\Passport\Client::class);
        }

        // Run configuration if installed
        if ($isInstalled) {
            // Run migrations
            $this->info('Running migrations...');
            $this->runMigrations();

            // Install Passport clients
            $this->info('Installing Passport clients...');
            $this->call('passport:install');

            // Update model
            $this->updateModelWithTrait($modelName, 'Laravel\Passport\HasApiTokens', 'HasApiTokens');
            
            // Set default driver
            $this->updateEnvFile('AUTH_FUSION_DRIVER', 'passport');
        }

        $this->displayNextSteps('passport');

        return 0;
    }

    /**
     * Install all drivers.
     *
     * @param string $modelName
     * @return int
     */
    protected function installAll(string $modelName): int
    {
        $this->info('📦 Installing all drivers...');
        
        $this->installSanctum($modelName);
        $this->newLine(2);
        $this->installJWT();
        $this->newLine(2);
        $this->installPassport($modelName);

        $this->info('✅ All drivers installed successfully!');
        $this->newLine();
        $this->displayNextSteps('all');

        return 0;
    }

    /**
     * Update model with trait.
     *
     * @param string $modelName
     * @param string $traitNamespace
     * @param string $traitName
     * @return void
     */
    protected function updateModelWithTrait(string $modelName, string $traitNamespace, string $traitName): void
    {
        $modelPath = app_path("Models/{$modelName}.php");

        if (!File::exists($modelPath)) {
            $this->warn("Model {$modelName}.php not found. Please add 'use {$traitName};' manually.");
            return;
        }

        $content = File::get($modelPath);

        // Check if trait already exists in class body
        if (preg_match("/^\s*use\s+{$traitName}\s*;/m", $content)) {
            $this->info("✓ {$modelName} already uses {$traitName} trait");
            return;
        }

        // Add use statement at the top if not exists
        if (!preg_match("/^use\s+{$traitNamespace}\s*;/m", $content)) {
            // Find the namespace section and add use statement
            $content = preg_replace(
                "/(namespace\s+[^;]+;)/",
                "$1\n\nuse {$traitNamespace};",
                $content,
                1
            );
        }

        // Add trait in class body - place after opening brace
        $content = preg_replace(
            "/(class\s+{$modelName}[^{]*\{\s*)/",
            "$1\n    use {$traitName};\n",
            $content,
            1
        );

        File::put($modelPath, $content);
        $this->info("✓ Added {$traitName} trait to {$modelName}");
    }

    /**
     * Update model with JWT support (trait and interface).
     *
     * @param string $modelName
     * @return void
     */
    protected function updateModelWithJWT(string $modelName): void
    {
        $modelPath = app_path("Models/{$modelName}.php");

        if (!File::exists($modelPath)) {
            $this->warn("Model {$modelName}.php not found. Please add JWT support manually.");
            return;
        }

        $content = File::get($modelPath);

        // Check if already has JWT support
        if (preg_match("/JWTSubject/", $content)) {
            $this->info("✓ {$modelName} already has JWT support");
            return;
        }

        // Escape model name for use in regex
        $modelNameEscaped = preg_quote($modelName, '/');

        // Add use statements if not exists
        if (!preg_match("/use\s+Tymon\\\\JWTAuth\\\\Contracts\\\\JWTSubject;/", $content)) {
            $content = preg_replace(
                "/(namespace\s+[^;]+;)/",
                "$1\n\nuse Tymon\\JWTAuth\\Contracts\\JWTSubject;",
                $content,
                1
            );
        }

        // Implement JWTSubject interface
        if (preg_match("/class\s+{$modelNameEscaped}\s+extends/", $content)) {
            // Add interface implementation - match until { or whitespace/newline
            $content = preg_replace(
                "/(class\s+{$modelNameEscaped}\s+extends[^{]*?)(\s*[{])/",
                "$1 implements JWTSubject$2",
                $content,
                1
            );
        }

        // Add JWT methods if not exists
        if (!preg_match("/public\s+function\s+getJWTIdentifier/", $content)) {
            // Find the last closing brace and add methods before it
            $methodsToAdd = "\n    /**\n     * Get the identifier that will be stored in the subject claim of the JWT.\n     *\n     * @return mixed\n     */\n    public function getJWTIdentifier()\n    {\n        return \$this->getKey();\n    }\n\n    /**\n     * Return a key value array, containing any custom claims to be added to the JWT.\n     *\n     * @return array\n     */\n    public function getJWTCustomClaims()\n    {\n        return [];\n    }";
            
            // Match the last closing brace of the class
            $content = preg_replace(
                "/(\n)(})\s*$/",
                "$1{$methodsToAdd}$1$2",
                $content
            );
        }

        File::put($modelPath, $content);
        $this->info("✓ Added JWT support to {$modelName}");
    }

    /**
     * Update .env file with key-value.
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    protected function updateEnvFile(string $key, string $value): void
    {
        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            $this->warn('.env file not found. Please add manually: ' . $key . '=' . $value);
            return;
        }

        $content = File::get($envPath);

        // Check if already exists
        if (preg_match("/^{$key}=/m", $content)) {
            // Update existing value
            $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
            $this->info("✓ Updated {$key} in .env");
        } else {
            // Add new entry
            $content .= "\n{$key}={$value}\n";
            $this->info("✓ Added {$key} to .env");
        }

        File::put($envPath, $content);
    }

    /**
     * Run migrations safely, handling existing tables.
     *
     * @return void
     */
    protected function runMigrations(): void
    {
        try {
            $this->call('migrate');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            
            // Check for table already exists error (various formats)
            if (str_contains($errorMessage, 'already exists') || 
                str_contains($errorMessage, '42S01') ||
                (str_contains($errorMessage, 'Table') && str_contains($errorMessage, 'already exists'))) {
                
                $this->warn('⚠️  Migration tables already exist. Skipping migrations.');
                
                if (!$this->option('force')) {
                    $this->line('');
                    $this->info('💡 Tip: Use --force flag to run migrations anyway.');
                    $this->line('');
                    return;
                }
                
                // If force is enabled, try to run with force flag
                $this->warn('Running with --force flag...');
                try {
                    $this->call('migrate', ['--force' => true]);
                } catch (\Exception $e2) {
                    $this->error('❌ Migration failed: ' . $e2->getMessage());
                }
            } else {
                throw $e;
            }
        }
    }

    /**
     * Execute composer install with proper version constraints.
     *
     * @param string $package
     * @param string $constraint
     * @return void
     */
    protected function executeComposerInstall(string $package, string $constraint): void
    {
        $this->info("Installing package: {$package}:{$constraint}");
        $this->newLine();
        
        $command = ['composer', 'require', "{$package}:{$constraint}", '--no-interaction'];
        $process = new \Symfony\Component\Process\Process($command);
        $process->setTimeout(600); // 10 minutes timeout
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            $this->error("❌ Package installation failed!");
            $this->error($process->getErrorOutput());
            throw new \RuntimeException("Failed to install package: {$package}");
        }
        
        $this->newLine();
        $this->info("✅ Package installed successfully!");
    }

    /**
     * Execute a shell command.
     *
     * @param array $command
     * @param string $displayCommand
     * @return void
     */
    protected function executeCommand(array $command, string $displayCommand): void
    {
        $this->info("Running: {$displayCommand}");
        $this->newLine();
        
        $process = new \Symfony\Component\Process\Process($command);
        $process->setTimeout(600); // 10 minutes timeout
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            $this->error("❌ Command failed!");
            $this->error($process->getErrorOutput());
            throw new \RuntimeException("Failed to execute command: {$displayCommand}");
        }
        
        $this->newLine();
    }

    /**
     * Display next steps after installation.
     *
     * @param string $driver
     * @return void
     */
    protected function displayNextSteps(string $driver): void
    {
        $this->newLine();
        $this->info('🎉 Installation Complete!');
        $this->newLine();
        
        $steps = match($driver) {
            'sanctum' => [
                '✅ Sanctum driver installed successfully!',
                '',
                'Next steps:',
                '1. Clear caches: php artisan config:clear',
                '2. Start using: AuthFusion::driver()->login($credentials)'
            ],
            'jwt' => [
                '✅ JWT driver installed successfully!',
                '',
                'Next steps:',
                '1. Clear caches: php artisan config:clear',
                '2. Start using: AuthFusion::driver()->login($credentials)'
            ],
            'passport' => [
                '✅ Passport driver installed successfully!',
                '',
                'Next steps:',
                '1. Clear caches: php artisan config:clear',
                '2. Start using: AuthFusion::driver()->login($credentials)'
            ],
            'all' => [
                '✅ All drivers installed successfully!',
                '',
                'Next steps:',
                '1. Clear caches: php artisan config:clear',
                '2. Configure AUTH_FUSION_DRIVER in .env',
                '3. Start using: AuthFusion::driver()->login($credentials)'
            ],
            default => []
        };

        foreach ($steps as $step) {
            $this->line($step);
        }
    }
}

