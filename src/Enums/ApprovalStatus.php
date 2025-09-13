<?php

namespace Roberts\Support\Enums;

enum ApprovalStatus: string
{
    case PENDING = 'pending';
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::SUBMITTED => 'Submitted',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
        };
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isSubmitted(): bool
    {
        return $this === self::SUBMITTED;
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
}
