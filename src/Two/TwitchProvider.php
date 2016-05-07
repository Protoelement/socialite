<?php

namespace Laravel\Socialite\Two;


class TwitchProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = [
        'user_read',
        'user_subscriptions',
    ];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://api.twitch.tv/kraken/oauth2/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://api.twitch.tv/kraken/oauth2/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $userUrl = 'https://api.twitch.tv/kraken/user?oauth_token='.$token;

        $response = $this->getHttpClient()->get(
            $userUrl, $this->getRequestOptions()
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['_id'],
            'nickname' => $user['name'],
            'name' => $user['display_name'],
            'email' => $user['email'],
            'avatar' => $user['logo'],
        ]);
    }

    protected function getRequestOptions()
    {
        return [
            'headers' => [
                'Accept' => 'application/vnd.twitchtv.v3+json',
            ],
        ];
    }
}