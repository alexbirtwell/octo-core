<?php

namespace Octo\Console;

use Illuminate\Console\Command;
use Octo\Concerns\HasSmsProviderConfig;
use Octo\Concerns\InteractWithComposer;

class InstallSmsDriverCommand extends Command
{
    use InteractWithComposer;
    use HasSmsProviderConfig;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'octo:sms-install {provider}
                           {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the octo sms feature';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("\nOcto SMS Installer");
        $this->info("--------------------\n");

        $this->requireComposerPackages($this->getSmsProvider($this->argument('provider'))['sdk']);
    }
}
