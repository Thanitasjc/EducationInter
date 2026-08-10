<?php

namespace App\Enums;

enum LeadStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Consultation = 'consultation';
    case Interested = 'interested';
    case Document = 'document';
    case Application = 'application';
    case Submitted = 'submitted';
    case Offer = 'offer';
    case Visa = 'visa';
    case Success = 'success';
    case Lost = 'lost';
}
