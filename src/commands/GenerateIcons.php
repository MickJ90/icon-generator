<?php

namespace MickJ\IconGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateIcons extends Command
{
    protected $signature = 'icons:generate {path? : Cartella dove si trovano gli SVG (default: resources/icons)}';
    protected $description = 'Converte SVG in componenti Blade in resources/views/components/icons, con fill/stroke uniformati ma circle esclusi.';

    public function handle()
    {
        $inputPath = $this->argument('path') ?? resource_path('icons');
        $outputPath = resource_path('views/components/icons');

        if (!File::exists($inputPath)) {
            $this->error("La cartella [$inputPath] non esiste.");
            return;
        }

        if (!File::exists($outputPath)) {
            File::makeDirectory($outputPath, 0755, true);
        }

        $files = File::files($inputPath);
        if (empty($files)) {
            $this->warn("Nessun file SVG trovato in [$inputPath].");
            return;
        }

        foreach ($files as $file) {
            if (strtolower($file->getExtension()) !== 'svg') {
                continue;
            }

            $svgContent = File::get($file->getPathname());

            $svgContent = preg_replace(
                '/<svg(.*?)>/i',
                '<svg {{ $attributes->merge([\'class\' => \'inline\']) }}$1>',
                $svgContent
            );

            $svgContent = preg_replace_callback(
                '/<(?!circle\b)(path|rect|ellipse|polygon|polyline|line)\b([^>]*)>/i',
                function ($matches) {
                    $tag = $matches[1];
                    $attrs = $matches[2];

                    if (preg_match('/fill="none"/i', $attrs)) {
                    } elseif (preg_match('/fill="/i', $attrs)) {
                        $attrs = preg_replace('/fill="(?!none)[^"]*"/i', 'fill="currentColor"', $attrs);
                    } else {
                        $attrs .= ' fill="currentColor"';
                    }

                    if (preg_match('/stroke="none"/i', $attrs)) {
                    } elseif (preg_match('/stroke="/i', $attrs)) {
                        $attrs = preg_replace('/stroke="(?!none)[^"]*"/i', 'stroke="currentColor"', $attrs);
                    }

                    return "<{$tag}{$attrs}>";
                },
                $svgContent
            );

            $svgContent = preg_replace_callback(
                '/<g\b([^>]*)>/i',
                function ($matches) {
                    $attrs = $matches[1];
                    if (preg_match('/stroke="(?!none)[^"]*"/i', $attrs)) {
                        $attrs = preg_replace('/stroke="(?!none)[^"]*"/i', 'stroke="currentColor"', $attrs);
                    }
                    if (preg_match('/fill="(?!none)[^"]*"/i', $attrs)) {
                        $attrs = preg_replace('/fill="(?!none)[^"]*"/i', 'fill="none"', $attrs);
                    }
                    return "<g{$attrs}>";
                },
                $svgContent
            );

            $rawName = $file->getFilenameWithoutExtension();

            $cleanName = preg_replace('/\bicona\b/i', '', $rawName);

            $cleanName = preg_replace('/[^A-Za-z0-9\s-]/', '', $cleanName);

            $cleanName = preg_replace('/[\s_-]+/', '-', trim($cleanName));

            $iconName = strtolower($cleanName);

            $bladePath = $outputPath . '/' . $iconName . '.blade.php';
            File::put($bladePath, $svgContent);

            $this->info("✔ Icona [$iconName] generata.");
        }

        $this->info("Tutte le icone sono state generate in [$outputPath]");

        $this->info("🧹 Rimozione automatica del pacchetto in corso...");

        $composer = trim(shell_exec('which composer')) ?: base_path('composer.phar');

        $process = proc_open(
            "$composer remove mickj/laravel-icon-generator --no-interaction --quiet",
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            base_path()
        );

        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            $error = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);

            if ($error) {
                $this->warn("Impossibile rimuovere automaticamente il pacchetto: $error");
            } else {
                $this->info("Pacchetto rimosso automaticamente.");
            }
        }
    }
}
