<?php

namespace Roberts\Support\Enums;

enum ModeratorStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case FLAGGED = 'flagged';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::FLAGGED => 'Flagged',
        };
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isApproved(): bool
    {
        return $this === self::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this === self::REJECTED;
    }

    public function isFlagged(): bool
    {
        return $this === self::FLAGGED;
    }

    public function isDecided(): bool
    {
        return in_array($this, [self::APPROVED, self::REJECTED]);
    }

    public function needsAttention(): bool
    {
        return in_array($this, [self::PENDING, self::FLAGGED]);
    }
}
