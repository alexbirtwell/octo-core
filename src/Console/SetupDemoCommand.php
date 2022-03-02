<?php

namespace Octo\Console;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Console\Command;
use Octo\Common\Models\Contact;

class SetupDemoCommand extends Command
{
    protected $signature = 'octo:demo';

    protected $description = 'Setup demo aplication';

    public const DEFAULT_SUPER_ADMIN_NAME = 'Octo Super Administrator';
    public const DEFAULT_SUPER_ADMIN_EMAIL = 'super-admin@octo.dev';
    public const DEFAULT_SUPER_ADMIN_PASSWORD = 'octoSuperAdmin';

    public const DEFAULT_USER_NAME = 'Octo User';
    public const DEFAULT_USER_EMAIL = 'user@octo.dev';
    public const DEFAULT_USER_PASSWORD = 'octoUser';

    public function handle()
    {
        $this->call('migrate:fresh', ['--force' => true, '--seed' => true]);
        $this->factoryData();
        $this->setUpAdminAccount();
        $this->setUpUserAccount();
    }

    private function setUpUserAccount()
    {
        $user = (new CreateNewUser())->create([
            'name' => self::DEFAULT_USER_NAME,
            'email' => self::DEFAULT_USER_EMAIL,
            'password' => self::DEFAULT_USER_PASSWORD,
            'password_confirmation' => self::DEFAULT_USER_PASSWORD,
            'terms' => true,
        ]);

        $user->markEmailAsVerified();

        $this->comment(sprintf('Log in user with email %s and password %s', self::DEFAULT_USER_EMAIL, self::DEFAULT_USER_PASSWORD));
    }

    private function setUpAdminAccount(): void
    {
        $user = (new CreateNewUser())->create([
            'name' => self::DEFAULT_SUPER_ADMIN_NAME,
            'email' => self::DEFAULT_SUPER_ADMIN_EMAIL,
            'calling_code' => '55',
            'phone' => '91 989242304',
            'password' => self::DEFAULT_SUPER_ADMIN_PASSWORD,
            'password_confirmation' => self::DEFAULT_SUPER_ADMIN_PASSWORD,
            'terms' => true,
        ]);

        $user->forceFill(['super_admin' => true])->save();

        $user->markEmailAsVerified();

        $this->comment(sprintf('Log in seper admin with email %s and password %s', self::DEFAULT_SUPER_ADMIN_EMAIL, self::DEFAULT_SUPER_ADMIN_PASSWORD));
    }

    public function factoryData()
    {
        $this->info('Seeding fake data in database');

        Contact::factory()->count(40)->create();
    }
}