<?php

namespace Junges\Kafka\Console\Commands\KafkaConsumer;

use JetBrains\PhpStorm\Pure;
use Junges\Kafka\Config\Sasl;

class Options
{
    private ?array $topics = null;
    private ?string $consumer = null;
    private ?string $deserializer = null;
    private ?string $groupId = null;
    private ?int $commit = 1;
    private ?string $dlq = null;
    private int $maxMessages = -1;
    private ?string $securityProtocol = 'plaintext';
    private ?string $saslUsername;
    private ?string $saslPassword;
    private ?string $saslMechanisms;
    private array $config;
    private ?string $brokerConnection = null;

    #[Pure]
    public function __construct(array $options, array $config)
    {
        $options['topics'] = explode(",", $options['topics']);

        foreach ($options as $option => $value) {
            $this->{$option} = $value;
        }

        $this->config = $config;
        $this->saslPassword = $config['sasl']['password'];
        $this->saslUsername = $config['sasl']['username'];
        $this->saslMechanisms = $config['sasl']['mechanisms'];
    }

    public function getTopics(): array
    {
        return ! empty($this->topics) ? $this->topics : [];
    }

    public function getConsumer(): ?string
    {
        return $this->consumer;
    }

    public function getDeserializer(): ?string
    {
        return $this->deserializer;
    }

    public function getGroupId(): ?string
    {
        return strlen($this->groupId) > 1 ? $this->groupId : $this->config['groupId'];
    }

    public function getCommit(): ?string
    {
        return $this->commit;
    }

    public function getDlq(): ?string
    {
        return strlen($this->dlq) > 1 ? $this->dlq : null;
    }

    public function getMaxMessages(): int
    {
        return $this->maxMessages >= 1 ? $this->maxMessages : -1;
    }

    #[Pure]
    public function getSasl(): ?Sasl
    {
        if (is_null($this->saslMechanisms) || is_null($this->saslPassword) || is_null($this->saslUsername)) {
            return null;
        }

        return new Sasl(
            username: $this->saslUsername,
            password: $this->saslPassword,
            mechanisms: $this->saslMechanisms,
            securityProtocol: $this->securityProtocol
        );
    }

    public function getSecurityProtocol(): ?string
    {
        return $this->securityProtocol;
    }

    public function getBroker(): string
    {
        if ($this->brokerConnection && is_array($this->config['brokers'])) {
            return $this->config['brokers'][$this->brokerConnection];
        } elseif (is_array($this->config['brokers'])) {
            return array_values($this->config['brokers'])[0];
        }

        return $this->config['brokers'];
    }
}
