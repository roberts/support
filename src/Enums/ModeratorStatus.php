<?php

namespace Roberts\Support\Enums;

enum ModeratorStatus: string
{
    case PENDING = 'pending';
    case FLAGGED = 'flagged';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::FLAGGED => 'Flagged',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
        };
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isFlagged(): bool
    {
        return $this === self::FLAGGED;
    }

    public function isApproved(): bool
    {
        return $this === self::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this === self::REJECTED;
    }

    public function isDecided(): bool
    {
        return in_array($this, [self::APPROVED, self::REJECTED]);
    }

    public function needsModeration(): bool
    {
        return in_array($this, [self::PENDING, self::FLAGGED]);
    }
}
