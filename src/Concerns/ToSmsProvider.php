<?php

namespace Octo\Concerns;

use Illuminate\Notifications\Messages\NexmoMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;

trait ToSmsProvider
{
    use HasSmsProviderConfig;

    private $smsContent;

    public function toSms($notifiable)
    {
        return null;
    }

    public function to($channels)
    {
        if (in_array('sms', $channels)) {
            return array_merge(array_diff($channels, ['sms']), [
                $this->resolveSmsProvider()
            ]);
        }

        return array_diff($channels, ['sms']);
    }

    private function resolveSmsProvider()
    {
        return $this->getSmsProvider(config('octo-options.sms.provider'), 'config-key')['channel'];
    }

    public function toNexmo($notifiable)
    {
        return (new NexmoMessage)
            ->content($this->toSms($notifiable));
    }

    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage())
            ->content($this->toSms($notifiable));
    }
}
