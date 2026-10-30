<?php

namespace App\Console\Commands;

use App\Models\Pengumuman;
use Illuminate\Console\Command;

class GenerateSitemapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate file public/sitemap.xml terbaru secara otomatis untuk SEO Google';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pembuatan sitemap.xml...');

        try {
            $pengumumans = Pengumuman::terbitPublik()->orderByDesc('updated_at')->get();
            $content = view('public.sitemap', compact('pengumumans'))->render();

            file_put_contents(public_path('sitemap.xml'), $content);

            $this->info('Berhasil! File public/sitemap.xml telah diperbarui dengan ' . $pengumumans->count() . ' artikel warta.');
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Gagal memperbarui sitemap: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
