<?php

namespace Roberts\Support\Enums;

enum ApplicationStatus: string
{
    case PENDING = 'pending';
    case STARTED = 'started';
    case VERIFIED = 'verified';
    case APPLIED = 'applied';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::STARTED => 'Started',
            self::VERIFIED => 'Verified',
            self::APPLIED => 'Applied',
            self::ACCEPTED => 'Accepted',
            self::REJECTED => 'Rejected',
        };
    }

    public function isStarted(): bool
    {
        return $this === self::STARTED;
    }

    public function isVerified(): bool
    {
        return $this === self::VERIFIED;
    }

    public function isApplied(): bool
    {
        return $this === self::APPLIED;
    }

    public function isAccepted(): bool
    {
        return $this === self::ACCEPTED;
    }

    public function isRejected(): bool
    {
        return $this === self::REJECTED;
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isInProgress(): bool
    {
        return in_array($this, [self::STARTED, self::VERIFIED, self::APPLIED]);
    }

    public function isDecided(): bool
    {
        return in_array($this, [self::ACCEPTED, self::REJECTED]);
    }
}
