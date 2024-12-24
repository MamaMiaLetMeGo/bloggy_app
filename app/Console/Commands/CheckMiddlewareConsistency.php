<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

class CheckMiddlewareConsistency extends Command
{
    protected $signature = 'check:middleware';
    protected $description = 'Check for middleware naming consistency across the application';

    private $middlewareMap = [];
    private $issues = [];

    public function handle()
    {
        $this->info('Checking middleware consistency...');

        // Step 1: Get middleware aliases from Kernel.php
        $this->getMiddlewareFromKernel();

        // Step 2: Check route files
        $this->info('Checking route files...');
        $this->checkRouteFiles();

        // Step 3: Check controllers
        $this->info('Checking controllers...');
        $this->checkControllers();

        // Step 4: Check middleware imports
        $this->info('Checking middleware imports...');
        $this->checkMiddlewareImports();

        // Report findings
        $this->reportFindings();

        return Command::SUCCESS;
    }

    private function getMiddlewareFromKernel()
    {
        $kernelPath = app_path('Http/Kernel.php');
        $content = File::get($kernelPath);

        // Get middleware aliases
        preg_match('/protected \$middlewareAliases = \[(.*?)\];/s', $content, $matches);
        if (isset($matches[1])) {
            preg_match_all("/'([^']+)' => \\\\App\\\\Http\\\\Middleware\\\\([^:,]+)/", $matches[1], $aliasMatches);
            foreach ($aliasMatches[1] as $index => $alias) {
                $this->middlewareMap[$alias] = $aliasMatches[2][$index];
            }
        }

        $this->info('Found ' . count($this->middlewareMap) . ' middleware aliases in Kernel.php');
    }

    private function checkRouteFiles()
    {
        $routeFiles = File::glob(base_path('routes/*.php'));
        foreach ($routeFiles as $file) {
            $content = File::get($file);
            $this->checkFileContent($content, $file, 'route');
        }
    }

    private function checkControllers()
    {
        $finder = new Finder();
        $finder->files()->in(app_path('Http/Controllers'))->name('*.php');

        foreach ($finder as $file) {
            $content = $file->getContents();
            $this->checkFileContent($content, $file->getRealPath(), 'controller');
        }
    }

    private function checkMiddlewareImports()
    {
        $finder = new Finder();
        $finder->files()->in(base_path())->name('*.php')->contains('use.*Middleware');

        foreach ($finder as $file) {
            $content = $file->getContents();
            
            // Skip namespace imports
            $lines = explode("\n", $content);
            $nonImportLines = array_filter($lines, function($line) {
                return !preg_match('/^use\s+.*\\\\[^;]+;$/', trim($line));
            });
            $content = implode("\n", $nonImportLines);

            // Check for middleware class usage
            foreach ($this->middlewareMap as $alias => $class) {
                if (strpos($content, $class) !== false && strpos($content, 'Kernel.php') === false) {
                    $this->issues[] = [
                        'type' => 'import',
                        'file' => $file->getRealPath(),
                        'message' => "Using middleware class '{$class}' directly instead of alias '{$alias}'"
                    ];
                }
            }
        }
    }

    private function checkFileContent($content, $file, $type)
    {
        // Skip namespace imports
        $lines = explode("\n", $content);
        $nonImportLines = array_filter($lines, function($line) {
            return !preg_match('/^use\s+.*\\\\[^;]+;$/', trim($line));
        });
        $content = implode("\n", $nonImportLines);

        // Check middleware usage in ->middleware() calls
        preg_match_all("/->middleware\(\['([^']+)'/", $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $middleware) {
                $middlewareNames = explode(',', $middleware);
                foreach ($middlewareNames as $name) {
                    $name = trim($name);
                    // Skip throttle:* middleware checks since they're dynamic
                    if (strpos($name, 'throttle:') === 0) {
                        continue;
                    }
                    if (!isset($this->middlewareMap[$name])) {
                        $this->issues[] = [
                            'type' => $type,
                            'file' => $file,
                            'message' => "Using unregistered middleware alias '{$name}'"
                        ];
                    }
                }
            }
        }

        // Check for direct middleware class usage
        foreach ($this->middlewareMap as $alias => $class) {
            if (strpos($content, $class) !== false && strpos($content, 'Kernel.php') === false) {
                $this->issues[] = [
                    'type' => $type,
                    'file' => $file,
                    'message' => "Using middleware class '{$class}' directly instead of alias '{$alias}'"
                ];
            }
        }
    }

    private function reportFindings()
    {
        if (empty($this->issues)) {
            $this->info('✅ No middleware consistency issues found!');
            return;
        }

        $this->error('Found ' . count($this->issues) . ' middleware consistency issues:');
        
        foreach ($this->issues as $issue) {
            $this->warn('');
            $this->warn('File: ' . str_replace(base_path(), '', $issue['file']));
            $this->warn('Type: ' . ucfirst($issue['type']));
            $this->warn('Issue: ' . $issue['message']);
        }

        $this->warn('');
        $this->info('Recommendations:');
        $this->info('1. Always use middleware aliases instead of class names in routes and controllers');
        $this->info('2. Register all middleware classes in Kernel.php before using them');
        $this->info('3. Keep middleware naming consistent (e.g., AdminMiddleware vs IsAdmin)');
    }
}
