<?php
namespace Octo\Marketing;

use Filament\PluginServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Octo\Marketing\Enums\CampaignContactStatus;
use Octo\Marketing\Models\Campaign;
use Octo\Marketing\Facades\Campaign as FacadesCampaign;
use Octo\Marketing\Facades\CampaignContact as FacadesCampaignContact;
use Octo\Marketing\Facades\Contact as FacadesContact;
use Octo\Marketing\Filament\Campaign\CampaignResource;
use Octo\Marketing\Filament\Contact\ContactResource;
use Octo\Marketing\Filament\Widgets\CampaingsChart;
use Octo\Marketing\Filament\Widgets\ContactsChart;
use Octo\Marketing\Filament\Widgets\Overview;
use Octo\Marketing\Models\CampaignContact;
use Octo\Marketing\Models\Contact;
use Spatie\LaravelPackageTools\Package;

class MarketingServiceProvider extends PluginServiceProvider
{
    protected array $resources = [
        ContactResource::class,
        CampaignResource::class,
    ];

    protected array $widgets = [
        Overview::class,
        ContactsChart::class,
        CampaingsChart::class,
    ];

    public function boot()
    {
        parent::boot();
    }

    public function register()
    {
        parent::register();

        Event::listen(NotificationSent::class, function (NotificationSent $event) {
            $campaign = $event?->notification?->campaign;
            $notifiable = $event?->notifiable;


            if ($campaign instanceof Campaign) {
                $campaign->contacts()->updateExistingPivot($notifiable->id, [
                    'notified_at' => now(),
                    'status' => CampaignContactStatus::NOTIFIED(),
                ]);

                try {
                    $subscription = $campaign->user->currentSubscription;

                    $subscription->recordFeatureUsage('metered.mails.units', 1, true, function ($feature, $valueOverQuota, $subscription) use (&$overQuota) {
                        $overQuota = $valueOverQuota;
                    });
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
        });

        Event::listen(JobProcessed::class, function (JobProcessed $event) {
            try {
                $notification = unserialize($event->job->payload()['data']['command'])->notification;

                $campaign = $notification?->campaign;

                if (!$campaign instanceof Campaign) {
                    return;
                }

                if (!$campaign->hasPendingContacts()) {
                    $campaign->finish();
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        });

        AliasLoader::getInstance()->alias('Marketing\Contact', FacadesContact::class);
        AliasLoader::getInstance()->alias('Marketing\Campaign', FacadesCampaign::class);
        AliasLoader::getInstance()->alias('Marketing\CampaignContact', FacadesCampaignContact::class);

        App::bind('Contact', function () {
            return app(Contact::class);
        });

        App::bind('Campaign', function () {
            return app(Campaign::class);
        });

        App::bind('CampaignContact', function () {
            return app(CampaignContact::class);
        });
    }

    public function configurePackage(Package $package): void
    {
        $package->name('octo.marketing');
    }
}
