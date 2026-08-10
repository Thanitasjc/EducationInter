<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case Draft = 'draft';
    case Consultation = 'consultation';
    case DocumentRequired = 'document_required';
    case ReadyToApply = 'ready_to_apply';
    case Submitted = 'submitted';
    case ConditionalOffer = 'conditional_offer';
    case UnconditionalOffer = 'unconditional_offer';
    case Visa = 'visa';
    case Completed = 'completed';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
}
